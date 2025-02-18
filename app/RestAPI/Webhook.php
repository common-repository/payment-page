<?php

namespace PaymentPage\RestAPI;

use WP_REST_Server;
use WP_REST_Request;
use PaymentPage\PaymentGateway as PP_PaymentGateway;
use PaymentPage\ThirdPartyIntegration\Freemius as PP_Freemius;
use PaymentPage\Model\Log as PP_Model_Log;
use PaymentPage\Model\Payments as PP_Model_Payments;

class Webhook {

	public static function register_routes() {
		register_rest_route(
			PAYMENT_PAGE_REST_API_PREFIX . '/v1',
			'/webhook/stripe-callback/(?P<mode>[\w-]+)',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => '\PaymentPage\RestAPI\Webhook::stripe_callback',
				'permission_callback' => function() {
					return true;
				},
			)
		);

		register_rest_route(
			PAYMENT_PAGE_REST_API_PREFIX . '/v1',
			'/webhook/paypal-callback/(?P<mode>[\w-]+)',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => '\PaymentPage\RestAPI\Webhook::paypal_callback',
				'permission_callback' => function() {
					return true;
				},
			)
		);
	}

	public static function stripe_callback( WP_REST_Request $request ) {
		ini_set( 'display_errors', 1 );
		ini_set( 'display_startup_errors', 1 );
		error_reporting( E_ALL );

		if ( ! isset( $_SERVER['HTTP_STRIPE_SIGNATURE'] ) ) {
			http_response_code( 400 );

			return rest_ensure_response(
				array(
					'message' => sprintf( __( '%s not found', 'payment-page' ), 'HTTP_STRIPE_SIGNATURE' ),
				)
			);
		}

		$mode = $request->get_param( 'mode' );

		if ( ! class_exists( '\Stripe\Stripe' ) ) {
			require_once PAYMENT_PAGE_BASE_PATH . '/lib/stripe/init.php';
		}

		$payload = @file_get_contents( 'php://input' );

		$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

		try {
			$event = \Stripe\Webhook::constructEvent(
				$payload,
				$sig_header,
				payment_page_setting_get( 'stripe_' . $mode . '_webhook_secret' )
			);
		} catch ( \UnexpectedValueException $e ) {
			// Invalid payload
			http_response_code( 400 );

			return rest_ensure_response(
				array(
					'message' => __( 'Invalid Payload', 'payment-page' ),
				)
			);
		}

		if ( ! isset( $event->data->object->metadata->payment_page_payment_id ) ) {
			http_response_code( 200 );

			return rest_ensure_response(
				array(
					'message' => sprintf( __( 'Event not tracked, %s metadata not attached', 'payment-page' ), 'payment_page_payment_id' ),
				)
			);
		}

		if ( ! isset( $event->data->object->metadata->domain_name ) || $event->data->object->metadata->domain_name !== payment_page_domain_name() ) {
			http_response_code( 200 );

			return rest_ensure_response(
				array(
					'message' => sprintf( __( 'Event not tracked, %s metadata does not match', 'payment-page' ), 'domain_name' ),
				)
			);
		}

		if ( ! in_array( $event->type, array( 'payment_intent.succeeded', 'setup_intent.succeeded' ) ) ) {
			http_response_code( 200 );

			return rest_ensure_response(
				array(
					'message' => __( 'Event not tracked', 'payment-page' ),
				)
			);
		}

		$modelPayment = PP_Model_Payments::findOrFail( array( 'id' => intval( $event->data->object->metadata->payment_page_payment_id ) ) );

		$modelPayment->amount_received = intval( $event->data->object->amount_received );
		$modelPayment->is_paid         = 1;
		$modelPayment->save();

		self::_onPaymentSuccess( $modelPayment, $event );

		http_response_code( 201 );

		return rest_ensure_response(
			array(
				'message' => __( 'Handled Callback', 'payment-page' ),
			)
		);
	}

	public static function paypal_callback( WP_REST_Request $request ) {
		ini_set( 'display_errors', 1 );
		ini_set( 'display_startup_errors', 1 );
		error_reporting( E_ALL );
		date_default_timezone_set( @date_default_timezone_get() );

		// I hate paypal.
		require_once PAYMENT_PAGE_BASE_PATH . '/lib/paypal-sdk/autoload.php';
		require_once PAYMENT_PAGE_BASE_PATH . '/lib/paypal-http/autoload.php';
		require_once PAYMENT_PAGE_BASE_PATH . '/lib/paypal-checkout-sdk/autoload.php';

		$requestBody = file_get_contents( 'php://input' );

		if ( empty( $requestBody ) ) {
			exit;
		}

		$headers = getallheaders();
		$headers = array_change_key_case( $headers, CASE_UPPER );
		if (
		( ! array_key_exists( 'PAYPAL-AUTH-ALGO', $headers ) ) ||
		( ! array_key_exists( 'PAYPAL-TRANSMISSION-ID', $headers ) ) ||
		( ! array_key_exists( 'PAYPAL-CERT-URL', $headers ) ) ||
		( ! array_key_exists( 'PAYPAL-TRANSMISSION-SIG', $headers ) ) ||
		( ! array_key_exists( 'PAYPAL-TRANSMISSION-TIME', $headers ) )
		) {
			exit;
		}

		$webhookID = payment_page_setting_get( 'paypal_' . $request->get_param( 'mode' ) . '_webhook_id' );

		$signatureVerification = new \PayPal\Api\VerifyWebhookSignature();
		$signatureVerification->setAuthAlgo( $headers['PAYPAL-AUTH-ALGO'] );
		$signatureVerification->setTransmissionId( $headers['PAYPAL-TRANSMISSION-ID'] );
		$signatureVerification->setCertUrl( $headers['PAYPAL-CERT-URL'] );
		$signatureVerification->setWebhookId( $webhookID );
		$signatureVerification->setTransmissionSig( $headers['PAYPAL-TRANSMISSION-SIG'] );
		$signatureVerification->setTransmissionTime( $headers['PAYPAL-TRANSMISSION-TIME'] );

		$signatureVerification->setRequestBody( $requestBody );

		try {
			$apiContext = new \PayPal\Rest\ApiContext(
				new \PayPal\Auth\OAuthTokenCredential(
					payment_page_setting_get( 'paypal_' . $request->get_param( 'mode' ) . '_client_id' ),
					payment_page_setting_get( 'paypal_' . $request->get_param( 'mode' ) . '_secret' )
				)
			);

			$apiContext->setConfig(
				array(
					'mode'           => ( $request->get_param( 'mode' ) === 'test' ? 'sandbox' : 'live' ),
					'log.LogEnabled' => false,
					'cache.enabled'  => false,
				)
			);

			$output = $signatureVerification->post( $apiContext );

		} catch ( \Exception $ex ) {
			exit;
		}

		$sigVerificationResult = $output->getVerificationStatus();

		if ( $sigVerificationResult == 'SUCCESS' ) {
			$requestBodyDecode = json_decode( $requestBody, ARRAY_A );

			$order_id       = $requestBodyDecode['resource']['supplementary_data']['related_ids']['order_id'];
			$transaction_id = $requestBodyDecode['resource']['id'];

			if ( $request->get_param( 'mode' ) === 'test' ) {
				$environment = new \PayPalCheckoutSdk\Core\SandboxEnvironment(
					payment_page_setting_get( 'paypal_' . $request->get_param( 'mode' ) . '_client_id' ),
					payment_page_setting_get( 'paypal_' . $request->get_param( 'mode' ) . '_secret' )
				);
			} else {
				$environment = new \PayPalCheckoutSdk\Core\ProductionEnvironment(
					payment_page_setting_get( 'paypal_' . $request->get_param( 'mode' ) . '_client_id' ),
					payment_page_setting_get( 'paypal_' . $request->get_param( 'mode' ) . '_secret' )
				);
			}

			$client = new \PayPalCheckoutSdk\Core\PayPalHttpClient( $environment );

			try {
				$order_information = $client->execute( new \PayPalCheckoutSdk\Orders\OrdersGetRequest( $order_id ) );

				$description = $order_information->result->purchase_units[0]->description;
				$description = explode( ':', $description );

				if ( strpos( $description[0], payment_page_domain_name() ) === false ) {
					exit;
				}

				$modelPayment = PP_Model_Payments::findOrFail( array( 'id' => intval( $description[1] ) ) );

				$modelPayment->amount_received = payment_page_format_price_as_non_decimal_int(
					$order_information->result->purchase_units[0]->amount->value,
					$modelPayment->currency
				);

				$modelPayment->is_paid       = 1;
				$modelPayment->paypal_txn_id = $transaction_id;
				$modelPayment->save();

				self::_onPaymentSuccess( $modelPayment, $order_information );

				/**
				$capture_id = $order_information->result->purchase_units[0]->payments->captures[0]->id;
				$capture_information = $client->execute( new \PayPalCheckoutSdk\Payments\CapturesGetRequest( $capture_id ) );
				*/
			} catch ( \Exception $e ) {

			}
		}
	}

  public static function _onPaymentSuccess( PP_Model_Payments $modelPayment, $gateway_args ) {
    $request_data = array(
      'gateway'         => $modelPayment->payment_gateway,
      'method'          => $modelPayment->payment_method,
      'mode'            => $modelPayment->is_live ? 'live' : 'test',
      'name'            => $modelPayment->first_name . ' ' . $modelPayment->last_name,
      'email'           => $modelPayment->email_address,
      'amount'          => $modelPayment->amount,
      'amount_received' => $modelPayment->amount_received,
      'currency'        => $modelPayment->currency,
    );

    $request_data += _payment_page_payment_identification_fields( $modelPayment->post_id, $modelPayment->id );

    foreach ( $modelPayment->metadata_json as $k => $v ) {
      $request_data[ $k ] = $v;
    }

    if ( $modelPayment->payment_gateway === 'stripe' ) {
      if ( $gateway_args->type === 'payment_intent.succeeded' ) {
        do_action( 'payment_page_payment_received', $request_data, array( 'stripe' => $gateway_args->data ) );
      }

      if ( $gateway_args->type === 'setup_intent.succeeded' ) {
        do_action( 'payment_page_subscription_created', $request_data, array( 'stripe' => $gateway_args->data ) );
      }
    } else {
      do_action( 'payment_page_payment_received', $request_data, array( 'paypal' => $gateway_args->result ) );
    }

    $page_settings = get_post_meta( $modelPayment->post_id, '_elementor_data', true );

    if ( !empty( $page_settings ) ) {
      $page_settings = json_decode( $page_settings, true );

      if ( !empty( $page_settings ) ) {
        $paymentPageElements = self::_extractPaymentPageElements( $page_settings );

        foreach ( $paymentPageElements as $paymentPageElement ) {
          self::_onPaymentSuccessWithPageSettings( $modelPayment, $gateway_args, $paymentPageElement, $request_data );
        }
      }
    }

    $page_settings = get_post_meta( $modelPayment->post_id, 'payment_page_settings', true );

    if(!empty($page_settings)) {
      self::_onPaymentSuccessWithPageSettings( $modelPayment, $gateway_args, $page_settings, $request_data );
    }

  }



	/**
	 * Not Private, can be used to debug if wanted.
	 *
	 * @param PP_Model_Payments $modelPayment
	 * @param $gateway_args
	 * @return void
	 * @throws \Exception
	 */
  private static function _onPaymentSuccessWithPageSettings( PP_Model_Payments $modelPayment, $gateway_args, $page_settings, $request_data ) {
    if ( ! isset( $page_settings['submit_actions'] ) || empty( $page_settings['submit_actions'] ) ) {
      return;
    }

    if ( in_array( 'http_request', $page_settings['submit_actions'] ) && isset( $page_settings['http_request_url'] ) && ! empty( $page_settings['http_request_url'] ) ) {
      $url = $page_settings['http_request_url'];

      if( is_array( $url ) && isset( $url['url'] ) )
        $url = $url['url'];

      if(!empty($url)) {
        $log = new PP_Model_Log(
          array(
            'post_id'   => $modelPayment->post_id,
            'namespace' => 'form',
            'action'    => 'http_request',
            'content'   => array(
              'request_response' => null,
              'request_data'     => $request_data,
            ),
          )
        );

        $log->save();

        $log->content['request_response'] = wp_remote_post(
          $url,
          array(
            'body' => $request_data,
          )
        );

        $log->save();
      }
    }

    if ( in_array( 'email', $page_settings['submit_actions'] )
        && isset( $page_settings['email_to'] )
        && isset( $page_settings['email_subject_payer'] )
        && isset( $page_settings['email_subject_admin'] ) ) {

      $headers = array( 'Content-Type: text/html; charset=UTF-8' );

      if ( isset( $page_settings['email_from'] ) && ! empty( $page_settings['email_from'] ) && isset( $page_settings['email_from_name'] ) && ! empty( $page_settings['email_from_name'] ) ) {
        $from_header  = 'From: ' . $page_settings['email_from_name'];
        $from_header .= ' <' . $page_settings['email_from'] . '>';

        $headers[] = $from_header;
      }

      $content  = '';
      $content .= '<ul style="list-style-type:none;padding:0;margin:0;">';

      foreach ( $request_data as $k => $v ) {
        if ( $k === 'gateway' ) {
          $value = ucfirst( $v );
        } elseif ( $k === 'currency' ) {
          $value = strtoupper( $v );
        } elseif ( $k === 'method' ) {
          if ( $v === 'ccard' ) {
            $value = __( 'Credit Card', 'payment-page' );
          } else {
            $value = ucwords( $v );
          }
        } elseif ( is_array( $v ) ) {
          $value = json_encode( $v, JSON_PRETTY_PRINT );
        } elseif ( is_numeric( $v ) ) {
          if ( stripos( 'amount', $k ) !== false ) {
            $value = payment_page_format_decimal_int_price_as_readable( $v, $request_data['currency'] );
          } else {
            $value = $v;
          }
        } elseif ( stripos( $k, 'domain' ) === false && stripos( $k, 'url' ) === false ) {
          $value = ucfirst( $v );
        } else {
          $value = $v;
        }

        $content .= '<li><strong>' . payment_page_alias_to_label( $k ) . '</strong>: ' . $value . '</li>';
      }

      $content .= '</ul>';

      foreach ( explode( ',', $page_settings['email_to'] ) as $email ) {
        wp_mail(
          $email,
          $page_settings['email_subject_admin'],
          $content,
          $headers
        );
      }

      wp_mail(
        $modelPayment->email_address,
        $page_settings['email_subject_payer'],
        $content,
        $headers
      );
    }
	}

	private static function _extractPaymentPageElements( $page_settings ) {
		$response = array();

		foreach ( $page_settings as $page_setting ) {
			if ( isset( $page_setting['elements'] ) && ! empty( $page_setting['elements'] ) ) {
				$response += self::_extractPaymentPageElements( $page_setting['elements'] );

				continue;
			}

			if ( isset( $page_setting['widgetType'] ) && $page_setting['widgetType'] === 'payment-form' ) {
				$response[ $page_setting['id'] ] = $page_setting['settings'];
			}
		}

		return $response;
	}

}
