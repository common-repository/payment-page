<?php

namespace PaymentPage\PaymentGateway;

use PaymentPage\API\PaymentPage as API_PaymentPage;
use PaymentPage\Settings as API_Settings;

class PayPal extends Skeleton {

	public static function setup_start_connection( $options ) :array {
		$settings_prefix = 'paypal_' . ( intval( $options['is_live'] ) ? 'live' : 'test' );

		$description = '<p>' . sprintf(
			__( 'Create a %1$s app, which can be done in %2$s in the PayPal account area. Then, enter the credentials below.', 'payment-page' ),
			( intval( $options['is_live'] ) ? 'Live' : 'Sandbox' ),
			'<a href="https://developer.paypal.com/developer/applications" target="_blank">' . __( 'My Apps & Credentials', 'payment-page' ) . '</a>'
		) .
			  '</p>';

		$description .= '<p>' . sprintf(
			__( 'To configure PayPal, please read our %s.', 'payment-page' ),
			'<a href="https://docs.payment.page/payment-gateways/paypal/paypal-setup" target="_blank">' . __( 'Documentation', 'payment-page' ) . '</a>'
		) . '</p>';

		return array(
			'status'      => 'ok',
			'type'        => 'settings',
			'description' => $description,
			'fields'      => array(
				'email_address' => array(
					'label'       => __( 'Email Address', 'payment-page' ),
					'name'        => 'email_address',
					'type'        => 'text',
					'value'       => API_Settings::instance()->get( $settings_prefix . '_email_address' ),
					'order'       => 1,
					'is_required' => 1,
				),
				'client_id'     => array(
					'label'       => __( 'Client ID', 'payment-page' ),
					'name'        => 'client_id',
					'type'        => 'text',
					'value'       => API_Settings::instance()->get( $settings_prefix . '_client_id' ),
					'order'       => 2,
					'is_required' => 1,
				),
				'secret'        => array(
					'label'       => __( 'Secret', 'payment-page' ),
					'name'        => 'secret',
					'type'        => 'text',
					'value'       => API_Settings::instance()->get( $settings_prefix . '_secret' ),
					'order'       => 3,
					'is_required' => 1,
				),
			),
			'operations'  => array(
				'save' => array(
					'label' => __( 'Save Settings', 'payment-page' ),
					'type'  => 'save',
					'order' => 1,
				),
			),
		);
	}

	public static function save_master_credentials_response( $credentials ) :bool {
		if ( ! is_array( $credentials ) ) {
			return false;
		}

		if ( ! isset( $credentials['email_address'] )
		|| ! isset( $credentials['client_id'] )
		|| ! isset( $credentials['secret'] ) ) {
			return false;
		}

		if ( empty( $credentials['email_address'] )
		|| empty( $credentials['client_id'] )
		|| empty( $credentials['secret'] ) ) {
			return false;
		}

		$settings = array(
			'paypal_' . ( intval( $credentials['is_live'] ) ? 'live' : 'test' ) . '_email_address'  => $credentials['email_address'],
			'paypal_' . ( intval( $credentials['is_live'] ) ? 'live' : 'test' ) . '_client_id'      => $credentials['client_id'],
			'paypal_' . ( intval( $credentials['is_live'] ) ? 'live' : 'test' ) . '_secret'         => $credentials['secret'],
		);

		if ( isset( $credentials['is_live'] ) ) {
			$settings['paypal_is_live'] = intval( $credentials['is_live'] );
		}

		API_Settings::instance()->update( $settings );

		return true;
	}

	protected $_email_address;
	protected $_client_id;
	protected $_secret;
	protected $_is_live = false;

	public function get_client_id() {
		return $this->_client_id;
	}

	public function get_secret() {
		return $this->_secret;
	}

	public function is_live() {
		return $this->_is_live;
	}

	/**
	 * @return $this
	 */
	public function attach_settings_credentials( $is_live = null ) : PayPal {
		if ( $is_live === null ) {
			$is_live = intval( API_Settings::instance()->get( 'paypal_is_live' ) );
		}

		if ( $is_live ) {
			$this->_email_address = API_Settings::instance()->get( 'paypal_live_email_address' );
			$this->_client_id     = API_Settings::instance()->get( 'paypal_live_client_id' );
			$this->_secret        = API_Settings::instance()->get( 'paypal_live_secret' );
			$this->_is_live       = true;
		} else {
			$this->_email_address = API_Settings::instance()->get( 'paypal_test_email_address' );
			$this->_client_id     = API_Settings::instance()->get( 'paypal_test_client_id' );
			$this->_secret        = API_Settings::instance()->get( 'paypal_test_secret' );
			$this->_is_live       = false;
		}

		return $this;
	}

	public function is_configured() :bool {
		return $this->get_client_id() !== '';
	}

	public function delete_settings_credentials( $is_live = true ) {
		if ( $is_live ) {
			API_Settings::instance()->update(
				array(
					'paypal_live_email_address' => '',
					'paypal_live_client_id'     => '',
					'paypal_live_secret'        => '',
				)
			);
		} else {
			API_Settings::instance()->update(
				array(
					'paypal_test_email_address' => '',
					'paypal_test_client_id'     => '',
					'paypal_test_secret'        => '',
				)
			);
		}
	}

	public function attach_credentials( $credentials ) {
		$this->_email_address = $credentials['email_address'];
		$this->_client_id     = $credentials['client_id'];
		$this->_secret        = $credentials['secret'];
		$this->_is_live       = $credentials['is_live'];
	}

	public function get_name() :string {
		return __( 'PayPal', 'payment-page' );
	}

	public function get_logo_url() :string {
		return plugins_url( 'interface/img/payment-gateway/logo-paypal.png', PAYMENT_PAGE_BASE_FILE_PATH );
	}

	public function get_description() :string {
		$response = __( 'PayPal is one of the most global-reaching payment gateways to accept one-time payments, with multiple payment methods and currencies.', 'payment-page' );

		return $response;
	}

	public function get_account_name() :string {
		return $this->_email_address;
	}

	public function get_payment_methods_administration() :array {
		$response = array(
			array(
				'name'         => __( 'Standard Checkout', 'payment-page' ),
				'alias'        => 'standard_checkout',
				'is_available' => 1,
				'description'  => '<p>' .
									  '<span>' . __( 'Standard Checkout', 'payment-page' ) . '</span>' .
									  '<img alt="paypal" src="' . plugins_url( 'interface/img/payment-gateway/payment-method-paypal.svg', PAYMENT_PAGE_BASE_FILE_PATH ) . '"/>' .
								   '</p>' .
								   '<p>' . __( 'Set up standard payments on your checkout page so your buyers can pay with PayPal, debit and credit cards. ', 'payment-page' ) . '</p>',
			),
		);

		return apply_filters( 'payment_page_paypal_payment_methods_administration', $response );
	}

	public function get_payment_methods_frontend( $active_payment_methods ) :array {
		$response = array();

		if ( in_array( 'standard_checkout', $active_payment_methods ) ) {
			$response[] = array(
				'id'                    => 'standard_checkout',
				'name'                  => __( 'Standard PayPal Checkout', 'payment-page' ),
				'payment_method'        => 'standard_checkout',
				'has_recurring_support' => 0,
				'image'                 => plugins_url( 'interface/img/payment-gateway/payment-method-paypal.svg', PAYMENT_PAGE_BASE_FILE_PATH ),
			);
		}

		return apply_filters( 'payment_page_paypal_payment_methods_frontend', $response, $active_payment_methods );
	}

	public function get_webhook_settings_administration() :array {
		$live_fields_description = sprintf(
			__( 'Create an Endpoint in the %1$s, to send the event: %2$s', 'payment-page' ),
			'<a href="https://developer.paypal.com/developer/applications" target="_blank">' . __( 'PayPal My Apps & Credentials', 'payment-page' ) . '</a>',
			'<strong>Payment capture completed</strong>' .
			'<p>' . sprintf(
				__( 'Our %s covers how to configure Webhooks properly.', 'payment-page' ),
				'<a href="https://docs.payment.page/payment-gateways/paypal/paypal-webhook-configuration" target="_blank">' . __( 'Documentation', 'payment-page' ) . '</a>'
			) . '</p>'
		);

		return array(
			'title'                   => __( 'Webhook Settings (Recommended)', 'payment-page' ),
			'title_popup'             => __( 'Webhook Settings', 'payment-page' ),
			'test_configured'         => intval( payment_page_setting_get( 'paypal_test_webhook_id' ) !== '' ),
			'test_available'          => ( payment_page_setting_get( 'paypal_test_email_address' ) !== '' ? 1 : 0 ),
			'test_fields_description' => '<p>' . sprintf(
				__( 'Create an Endpoint in the %1$s, to send the event: %2$s', 'payment-page' ),
				'<a href="https://developer.paypal.com/developer/applications" target="_blank">' . __( 'PayPal My Apps & Credentials', 'payment-page' ) . '</a>',
				'<strong>Payment capture completed</strong>'
			) . '</p>' .
			'<p>' . sprintf(
				__( 'Our %s covers how to configure Webhooks properly.', 'payment-page' ),
				'<a href="https://docs.payment.page/payment-gateways/paypal/paypal-webhook-configuration" target="_blank">' . __( 'Documentation', 'payment-page' ) . '</a>'
			) . '</p>',
			'test_fields'             => array(
				'paypal_test_webhook_url' => array(
					'label' => __( 'Webhook URL', 'payment-page' ),
					'type'  => 'textarea_disabled',
					'name'  => 'stripe_test_webhook_url',
					'order' => 1,
					'value' => rest_url() . PAYMENT_PAGE_REST_API_PREFIX . '/v1/webhook/paypal-callback/test',
				),
				'paypal_test_webhook_id'  => array(
					'label' => __( 'Webhook ID', 'payment-page' ),
					'type'  => 'text',
					'name'  => 'paypal_test_webhook_id',
					'order' => 2,
					'value' => payment_page_setting_get( 'paypal_test_webhook_id' ),
				),
			),
			'live_configured'         => intval( payment_page_setting_get( 'paypal_live_webhook_id' ) !== '' ),
			'live_available'          => ( payment_page_setting_get( 'paypal_live_email_address' ) !== '' ? 1 : 0 ),
			'live_fields_description' => $live_fields_description,
			'live_fields'             => array(
				'paypal_live_webhook_url' => array(
					'label' => __( 'Webhook URL', 'payment-page' ),
					'type'  => 'textarea_disabled',
					'name'  => 'paypal_live_webhook_url',
					'order' => 1,
					'value' => rest_url() . PAYMENT_PAGE_REST_API_PREFIX . '/v1/webhook/paypal-callback/live',
				),
				'paypal_live_webhook_id'  => array(
					'label' => __( 'Webhook ID', 'payment-page' ),
					'type'  => 'text',
					'name'  => 'paypal_live_webhook_id',
					'order' => 2,
					'value' => payment_page_setting_get( 'paypal_live_webhook_id' ),
				),
			),
		);
	}

}
