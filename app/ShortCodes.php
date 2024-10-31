<?php

namespace PaymentPage;

use PaymentPage\PaymentForm as PP_PaymentForm;

class ShortCodes {

	/**
	 * Register the shortcode
	 */
	public static function register() {
		add_shortcode( 'payment-page-success-details', '\PaymentPage\ShortCodes::success_details' );
    add_shortcode( 'payment-page-payment-form', '\PaymentPage\ShortCodes::payment_form' );
	}

	/**
	 * Render the content of the shortcode
	 */
	public static function success_details(): string {
		return Template::get_template( 'shortcode-payment-success.php', [
			'title'          => ( isset( $_REQUEST['title'] ) ? urldecode( $_REQUEST['title'] ) : '' ),
			'message'        => ( isset( $_REQUEST['message'] ) ? urldecode( $_REQUEST['message'] ) : '' ),
			'item'           => ( isset( $_REQUEST['item'] ) ? urldecode( $_REQUEST['item'] ) : '' ),
			'purchased_from' => ( isset( $_REQUEST['purchased_from'] ) ? urldecode( $_REQUEST['purchased_from'] ) : '' ),
			'payment_date'   => ( isset( $_REQUEST['payment_date'] ) ? urldecode( $_REQUEST['payment_date'] ) : '' ),
			'currency'       => ( isset( $_REQUEST['currency'] ) ? urldecode( $_REQUEST['currency'] ) : '' ),
			'payment_amount' => ( isset( $_REQUEST['amount'] ) ? urldecode( $_REQUEST['amount'] ) : '' ),
			'customer_name'  => ( isset( $_REQUEST['customer_name'] ) ? urldecode( $_REQUEST['customer_name'] ) : '' ),
			'customer_email' => ( isset( $_REQUEST['email'] ) ? urldecode( $_REQUEST['email'] ) : '' ),
		] );
	}

  public static function payment_form( $args, $content) {
    if(empty($args['id'])) {
      return '';
    }

    $post_id = intval($args['id']);

    if(get_post_type($post_id) !== PAYMENT_PAGE_POST_TYPE_PAYMENT_FORM) {
      return '';
    }

    $response = '';

    if(!current_user_can(PAYMENT_PAGE_ADMIN_CAP)) {
      if(get_post_status($post_id) === 'published') {
        return '';
      }

      $response .= '<p data-payment-page-notification="warning">' . __( "This form is currently in draft mode, and cannot be viewed by non-admin users" ) . '</p>';
    }

    payment_page_register_universal_interface();

    $response .= PP_PaymentForm::instance()->get_from_payment_form_id( $post_id );

    return $response;
  }

}