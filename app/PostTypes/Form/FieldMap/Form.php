<?php

namespace PaymentPage\PostTypes\Form\FieldMap;

use PaymentPage\PaymentGateway as PaymentGateway;

class Form extends Skeleton {

	public static function get_field_groups() {
    $response = array();

    $response[] = self::_get_field_group_content();
    $response[] = self::_get_field_group_style();

    return $response;
	}

  private static function _get_field_group_content() {
    $payment_page_form_fields_description = '';
    $can_add = 0;

    $payment_page_form_fields_description .=
      '<p>' .
      __( 'Custom fields are stored at your payment gateway in the relevant transaction details. E.g. - You might want to collect an invoice ID when a customer is entering a unique Custom Amount for an invoice.', 'payment-page' ) .
      '</p>';

    if ( PaymentGateway::get_integration_from_settings( 'paypal' )->is_configured() ) {
      $payment_page_form_fields_description .= '<p>' . __( 'PayPal does not support storing Custom Fields.', 'payment-page' ) . '</p>';
    }

    return array(
      'label'   => __( 'Form Fields', 'payment-page' ),
      'group'   => 'section_form_fields',
      'section' => 'content',
      'fields'  => array(
        array(
          'label'       => __( 'Section Title', 'payment-page' ),
          'name'        => 'form_data_section_label',
          'type'        => 'text',
          'default'     => __( 'PAYMENT METHOD', 'payment-page' ),
          'placeholder' => __( 'Type your section title', 'payment-page' ),
        ),
        array(
          'label'       => null,
          'name'        => 'form_fields_map',
          'type'        => 'form_fields_map',
          'can_add'     => $can_add,
          'layout'      => 'basic',
          'description' => $payment_page_form_fields_description,
          'default'     => payment_page_form_field_map_core_fields()
        )
      )
    );
  }

  private static function _get_field_group_style() {
    $fields = [];

    $fields[] = array(
      'label'       => __( 'Section Title', 'payment-page' ),
      'type'        => 'header'
    );

    $fields = array_merge(
      $fields,
      payment_page_administration_payment_form_field_group_fields_typography(
        'form_section_title_style',
        array(
          'color'          => '#2676f1',
          'font_family'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY,
          'font_size'      => array(
            'unit' => 'px',
            'size' => 12,
          ),
          'font_weight'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_WEIGHT,
          'font_transform' => 'none',
        )
      )
    );

    //// Field Labels (Inactive)
    $fields[] = array(
      'label'       => __( 'Field Labels (Inactive)', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields = array_merge(
      $fields,
      payment_page_administration_payment_form_field_group_fields_typography(
        'form_field_label',
        array(
          'color'          => '#8676AA',
          'font_family'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY,
          'font_size'      => array(
            'unit' => 'px',
            'size' => 15,
          ),
          'font_weight'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_WEIGHT,
          'font_transform' => 'none',
        )
      )
    );

    //// Field Labels (Active)
    $fields[] = array(
      'label'       => __( 'Field Labels (Active)', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields = array_merge(
      $fields,
      payment_page_administration_payment_form_field_group_fields_typography(
        'form_field_label_active',
        array(
          'color'          => '#8676AA',
          'font_family'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY,
          'font_size'      => array(
            'unit' => 'px',
            'size' => 15,
          ),
          'font_weight'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_WEIGHT,
          'font_transform' => 'none',
        )
      )
    );

    //// Input Text
    $fields[] = array(
      'label'       => __( 'Input Text', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields = array_merge(
      $fields,
      payment_page_administration_payment_form_field_group_fields_typography(
        'form_field_input',
        array(
          'color'          => '#8676AA',
          'font_family'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY,
          'font_size'      => array(
            'unit' => 'px',
            'size' => 12,
          ),
          'font_weight'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_WEIGHT,
          'font_transform' => 'none',
        )
      )
    );

    //// Placeholder Text
    $fields[] = array(
      'label'       => __( 'Placeholder Text', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields = array_merge(
      $fields,
      payment_page_administration_payment_form_field_group_fields_typography(
        'form_field_input_placeholder',
        array(
          'color'          => '#32325d',
          'font_family'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY,
          'font_size'      => array(
            'unit' => 'px',
            'size' => 13,
          ),
          'font_weight'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_WEIGHT,
          'font_transform' => 'none',
        )
      )
    );

    //// Border
    $fields[] = array(
      'label'       => __( 'Border', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields = array_merge(
      $fields,
      payment_page_administration_payment_form_field_group_fields_border( 'form_fields', array(
        'border_color'  => '#CEC3E6',
        'border_radius' => array(
          'unit' => 'px',
          'size' => 3,
        ),
        'border_size'   => array(
          'unit' => 'px',
          'size' => 1,
        ),
      ), array( 'border_size' => 'single' ) )
    );

    //// Background
    $fields[] = array(
      'label'       => __( 'Background', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields[] = array(
      'label'       => __( 'Background', 'payment-page' ),
      'name'        => 'form_fields_background_color',
      'type'        => 'css_style_background_color',
      'default'     => '#ffffff'
    );

    return array(
      'label'   => __( 'Form', 'payment-page' ),
      'group'   => 'section_form_style',
      'section' => 'style',
      'fields'  => $fields
    );
  }

}
