<?php

namespace PaymentPage\PostTypes\Form\FieldMap;

use PaymentPage\ThirdPartyIntegration\Freemius as FS_Integration;

class ActionsForm extends Skeleton {

	private static array $_defaultTypography = array(
    'color'          => '#333333',
		'font_family'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY,
		'font_size'      => array(
			'unit' => 'px',
			'size' => 16,
		),
		'font_weight'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_WEIGHT,
		'font_transform' => 'none',
	);

	public static function get_field_groups() {
    $response = array();

    $response[] = array(
      'label'   => __( 'Actions After Submit', 'payment-page' ),
      'group'   => 'section_integration',
      'section' => 'content',
      'fields'  => array(
        array(
          'label'       => __( 'Add Action', 'payment-page' ),
          'name'        => 'submit_actions',
          'type'        => 'multiple_select',
          'options'     => payment_page_form_submit_actions_assoc(),
          'default'     => array( 'email' ),
          'description' => __( 'Add actions that will be performed after a visitor submits the form (e.g. send an email notification). Choosing an action will add its setting below.', 'payment-page' ),
          'attributes'  => [
            'data-payment-page-library'       => 'conditionaldisplay',
            'data-payment-page-library-args'  => [ 'target' => 'data-payment-page-actions-after-submit-conditional', 'ignore_hidden_parent' => 1 ]
          ],
        )
      )
    );

    $response[] = self::_styles_settings();
    $response[] = self::_register_email_action();
    $response[] = self::_register_redirect_action();
    $response[] = self::_register_dynamic_message_action();
    $response[] = array(
      'label'   => __( 'HTTP Request', 'payment-page' ),
      'group'   => 'section_http_request',
      'section' => 'content',
      'fields'  => array(
        array(
          'label'       => __( 'HTTP Request URL', 'payment-page' ),
          'name'        => 'http_request_url',
          'type'        => 'link',
          'placeholder' => __( 'Type your http request URL here', 'payment-page' ),
          'description' => __( 'Send a HTTP Request on successful payments to the following URL', 'payment-page' ),
        )
      ),
      'attributes' => [
        'data-payment-page-actions-after-submit-conditional' => 'http_request'
      ]
    );

    return $response;
	}

	private static function _styles_settings() :array {
    $fields_dynamic_message = array();

    $fields_dynamic_message[] = array(
      'label'       => __( 'Payment Success Title', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'none'
    );

    $fields_dynamic_message = array_merge(
      $fields_dynamic_message,
      payment_page_administration_payment_form_field_group_fields_typography( '_payment_success_title', self::$_defaultTypography )
    );

    $fields_dynamic_message[] = array(
      'label'       => __( 'Payment Success Message', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields_dynamic_message = array_merge(
      $fields_dynamic_message,
      payment_page_administration_payment_form_field_group_fields_typography( '_payment_success_content', self::$_defaultTypography )
    );

    $fields_dynamic_message[] = array(
      'label'       => __( 'Payment Success Detail Label', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields_dynamic_message = array_merge(
      $fields_dynamic_message,
      payment_page_administration_payment_form_field_group_fields_typography( '_payment_success_details_label', self::$_defaultTypography )
    );

    $fields_dynamic_message[] = array(
      'label'       => __( 'Payment Success Detail Value', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields_dynamic_message = array_merge(
      $fields_dynamic_message,
      payment_page_administration_payment_form_field_group_fields_typography( '_payment_success_details', self::$_defaultTypography )
    );

    $fields_dynamic_message[] = array(
      'label'       => __( 'Payment Failure Message', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields_dynamic_message = array_merge(
      $fields_dynamic_message,
      payment_page_administration_payment_form_field_group_fields_typography( '_dynamic_message_failure', self::$_defaultTypography )
    );


    return array(
      'label'   => __( 'Dynamic Message', 'payment-page' ),
      'group'   => 'section_dynamic_message_style',
      'section' => 'style',
      'fields'  => $fields_dynamic_message,
      'attributes' => [
        'data-payment-page-actions-after-submit-conditional' => 'dynamic_message'
      ]
    );
	}

	private static function _register_email_action() :array {
    $default_message_admin = sprintf( __( 'New Payment from "%s"', 'payment-page' ), get_option( 'blogname' ) );
    $default_message_payer = sprintf( __( 'Payment Details from "%s"', 'payment-page' ), get_option( 'blogname' ) );

    return array(
      'label'   => __( 'Email', 'payment-page' ),
      'group'   => 'section_email',
      'section' => 'content',
      'fields'  => [
        array(
          'label'       => __( 'To', 'payment-page' ),
          'name'        => 'email_to',
          'type'        => 'text',
          'default'     => get_option( 'admin_email' ),
          'placeholder' => get_option( 'admin_email' ),
          'description' => __( 'Separate emails with commas', 'payment-page' ),
        ),
        array(
          'label'       => __( 'Subject Admin', 'payment-page' ),
          'name'        => 'email_subject_admin',
          'type'        => 'text',
          'default'     => $default_message_admin,
          'placeholder' => $default_message_admin,
        ),
        array(
          'label'       => __( 'Subject Payer', 'payment-page' ),
          'name'        => 'email_subject_payer',
          'type'        => 'text',
          'default'     => $default_message_payer,
          'placeholder' => $default_message_payer,
        ),
        array(
          'label'       => __( 'From email', 'payment-page' ),
          'name'        => 'email_to',
          'type'        => 'text',
          'layout'      => 'inline',
          'default'     => '',
          'placeholder' => ''
        ),
        array(
          'label'       => __( 'From name', 'payment-page' ),
          'name'        => 'email_from_name',
          'type'        => 'text',
          'layout'      => 'inline',
          'default'     => '',
          'placeholder' => ''
        ),
      ],
      'attributes' => [
        'data-payment-page-actions-after-submit-conditional' => 'email'
      ]
    );
	}

	private static function _register_redirect_action() :array {
    return array(
      'label'   => __( 'Redirect', 'payment-page' ),
      'group'   => 'section_redirect_to',
      'section' => 'content',
      'fields'  => [
        array(
          'label'       => __( 'Redirect to URL after Successful Payment', 'payment-page' ),
          'name'        => 'redirect_to_url',
          'type'        => 'url',
          'default'     => '',
          'placeholder' => __( 'Type your redirect URL here', 'payment-page' )
        )
      ],
      'attributes' => [
        'data-payment-page-actions-after-submit-conditional' => 'redirect_to'
      ]
    );
	}

	private static function _register_dynamic_message_action() :array {
    return array(
      'label'   => __( 'Dynamic Message', 'payment-page' ),
      'group'   => 'section_dynamic_message',
      'section' => 'content',
      'fields'  => [
        array(
          'label'       => __( 'Payment Success Message:', 'payment-page' ),
          'name'        => 'success_message',
          'type'        => 'textarea',
          'default'     => ''
        ),
        array(
          'label'       => __( 'Payment Details Title:', 'payment-page' ),
          'name'        => 'payment_details_title',
          'type'        => 'textarea',
          'default'     => ''
        ),
        array(
          'label'       => __( 'Payment Details Title:', 'payment-page' ),
          'name'        => 'payment_details',
          'type'        => 'toggle',
          'default'     => 'yes',
          'toggle_label_on'  => __( 'On', 'payment-page' ),
          'toggle_label_off' => __( 'Off', 'payment-page' ),
          'toggle_value_on'  => 'yes',
          'toggle_value_off' => 'no'
        ),
        array(
          'label'       => __( 'Payment Failure Message:', 'payment-page' ),
          'name'        => 'failure_message',
          'type'        => 'text',
          'default'     => ''
        )
      ],
      'attributes' => [
        'data-payment-page-actions-after-submit-conditional' => 'dynamic_message'
      ]
    );
	}
}
