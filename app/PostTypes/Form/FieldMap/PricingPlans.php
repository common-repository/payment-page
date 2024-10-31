<?php

namespace PaymentPage\PostTypes\Form\FieldMap;

class PricingPlans extends Skeleton {

  public static function get_field_groups() {
    $response = array();

    $response[] = self::_get_field_group_content();
    $response[] = self::_get_field_group_style();

    return $response;
  }

  private static function _get_field_group_content() {
    $pricing_plan_fields = [];

    $pricing_plan_fields[] = [
      'label'       => __( 'Price', 'payment-page' ),
      'name'        => 'price',
      'type'        => 'number',
      'default'     => '',
      'description' => __( "If left empty, the customer will be able to enter any amount.", "payment-page" ),
      'attributes'  => [
        'data-payment-page-library'       => 'conditionaldisplay',
        'data-payment-page-library-args'  => [
          'target'                     => 'data-payment-page-has-price',
          'constrain_to_parent_target' => '[data-payment-page-component-form-section="repeater_form_item"]:first',
          'ignore_hidden_parent'       => 1,
          'special_empty_for_false'    => 1
        ]
      ],
    ];

    $subscription_frequencies = payment_page_administration_payment_form_field_payment_frequencies();
    unset( $subscription_frequencies['one-time'] );

    $pricing_plan_fields[] = [
      'label'       => __( 'Different first payment amount.', 'payment-page' ),
      'name'        => 'has_setup_price',
      'type'        => 'true_false',
      'default'     => 0,
      'attributes'  => [
        'data-payment-page-library'       => 'conditionaldisplay',
        'data-payment-page-library-args'  => [
          'target'                     => 'data-payment-page-has-setup-price',
          'constrain_to_parent_target' => '[data-payment-page-component-form-section="repeater_form_item"]:first',
          'ignore_hidden_parent'       => 1
        ]
      ],
      'row_attributes' => [
        'data-payment-page-has-price' => '1',
        'data-payment-page-payment-frequency' => implode( " ", array_keys($subscription_frequencies) )
      ]
    ];

    $pricing_plan_fields[] = [
      'label'       => __( 'First Payment Amount', 'payment-page' ),
      'name'        => 'setup_price',
      'type'        => 'number',
      'default'     => 0,
      'row_attributes' => [
        'data-payment-page-has-setup-price' => '1'
      ],
      'description' => '<p data-payment-page-payment-frequency="one-time">' . __( "This option will not be used for one-time payments", 'payment-page' ) . '</p>'
    ];

    $pricing_plan_fields[] = [
      'label'       => __( 'Currency', 'payment-page' ),
      'name'        => 'currency',
      'type'        => 'select',
      'default'     => '',
      'options'     => payment_page_administration_payment_form_field_currencies()
    ];

    $pricing_plan_fields[] = array(
      'label'     => __( 'Subscription frequency:', 'payment-page' ),
      'name'      => 'frequency',
      'type'      => 'select',
      'options'   => payment_page_administration_payment_form_field_payment_frequencies(),
      'attributes'  => [
        'data-payment-page-library'       => 'conditionaldisplay',
        'data-payment-page-library-args'  => [
          'target'                     => 'data-payment-page-payment-frequency',
          'constrain_to_parent_target' => '[data-payment-page-component-form-section="repeater_form_item"]:first',
          'ignore_hidden_parent'       => 1
        ]
      ],
    );

    $fields = array();

    $fields[] = array(
      'label'       => __( 'Section Title', 'payment-page' ),
      'name'        => 'pricing_selector_section_label',
      'type'        => 'text',
      'default'     => __( 'CHOOSE YOUR PLAN', 'payment-page' ),
      'placeholder' => __( 'Type your section title', 'payment-page' ),
    );

    $fields[] = array(
      'label'       =>__( 'Plans List', 'payment-page' ),
      'name'        => 'plans',
      'type'        => 'repeater',
      'enhance'     => [ 'duplicate' ],
      'default'     => array(
        array(
          'plan_title' => __( 'Your first plan', 'payment-page' ),
          'prices'     => [
            [
              'price'    => 9.99,
              'currency' => 'USD'
            ]
          ]
        ),
        array(
          'plan_title' => __( 'Your second plan', 'payment-page' ),
          'prices'     => [
            [
              'price'    => 99.99,
              'currency' => 'USD'
            ]
          ]
        ),
      ),
      'fields'      => array(
        array(
          'label'       => __( 'Title', 'payment-page' ),
          'name'        => 'plan_title',
          'type'        => 'text',
          'default'     => __( 'Plan Title', 'payment-page' ),
          'placeholder' => __( 'Plan Title', 'payment-page' ),
          'admin_label' => '1',
        ),
        array(
          'name'        => 'prices',
          'type'        => 'repeater',
          'layout'      => 'basic',
          'enhance'     => [ 'unique_combinations' ],
          'message_unique_combinations' => __( "Duplicate Pricing Configuration.", 'payment-page' ),
          'fields'      => $pricing_plan_fields,
          'label_add'   => __( "Add Price Option", 'payment-page' )
        )
      )
    );

    $message_builder_pieces = array(
        array(
          'label'  => __( 'Plan name', 'payment-page' ),
          'name'   => 'select_field_plan_name',
          'type'   => 'true_false'
        ),
        array(
          'label'  => __( 'Price', 'payment-page' ),
          'name'   => 'select_field_plan_price',
          'type'   => 'true_false'
        ),
        array(
          'label'  => __( 'Currency', 'payment-page' ),
          'name'   => 'select_field_plan_price_currency',
          'type'   => 'true_false'
        )
    );
    $message_builder_pieces_default = array(
      'token_order' => array('select_field_plan_name', 'select_field_separator_text', 'select_field_plan_price', 'select_field_plan_price_currency'),
      'token_map'   => array(
        'select_field_plan_name' => 1,
        'select_field_plan_price' => 1,
        'select_field_plan_price_currency'   => 1,
        'select_field_separator_text' => '-'
      )
    );

    $message_builder_pieces[] = array(
      'label'  => __( 'Frequency', 'payment-page' ),
      'name'   => 'select_field_frequency',
      'type'   => 'true_false'
    );

    $message_builder_pieces[] = array(
      'label'  => __( 'First Payment Amount', 'payment-page' ),
      'name'   => 'select_field_setup_price',
      'type'   => 'true_false'
    );

    $message_builder_pieces_default['token_map']['select_field_frequency'] = 1;
    $message_builder_pieces_default['token_order'][] = 'select_field_frequency';


    $message_builder_pieces[] = array(
      'label'  => __( 'Custom Text', 'payment-page' ),
      'name'   => 'select_field_custom_text',
      'type'   => 'text'
    );

    $message_builder_pieces[] = array(
      'label'  => __( 'Custom Text 2', 'payment-page' ),
      'name'   => 'select_field_separator_text',
      'type'   => 'text'
    );

    $fields[] = array(
      'label'       => __( 'Select Fields To Display:', 'payment-page' ),
      'name'        => 'pricing_selector_label',
      'type'        => 'message_builder',
      'pieces'      => $message_builder_pieces,
      'default'     => $message_builder_pieces_default
    );

    $fields[] = array(
      'label'       => __( 'Custom Amount Title', 'payment-page' ),
      'name'        => 'custom_pricing_input_section_label',
      'type'        => 'text',
      'default'     => __( 'ENTER YOUR AMOUNT', 'payment-page' ),
      'placeholder' => __( 'Type your custom amount title', 'payment-page' ),
    );

    return array(
      'label'   => __( 'Pricing Plans', 'payment-page' ),
      'group'   => 'plans',
      'section' => 'content',
      'fields'  => $fields
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
        'pricing_plan_select_title',
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

    // Start custom amount section title

    $fields[] = array(
      'label'       => __( 'Custom Amount Title', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields = array_merge(
      $fields,
      payment_page_administration_payment_form_field_group_fields_typography(
        'pricing_plan_custom_amount',
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

    $fields[] = array(
      'label'       => __( 'Border', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields = array_merge(
      $fields,
      payment_page_administration_payment_form_field_group_fields_border(
        'pricing_plan_select',
        array(
          'border_color'  => '#CEC3E6',
          'border_radius' => array(
            'unit'   => 'px',
            'size_1' => 5,
            'size_2' => 5,
            'size_3' => 5,
            'size_4' => 5,
          ),
          'border_size'   => array(
            'unit'   => 'px',
            'size_1' => 5,
            'size_2' => 5,
            'size_3' => 5,
            'size_4' => 5,
          ),
        )
      )
    );

    $fields[] = array(
      'label'       => __( 'Pricing Plans Dropdown', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields[] = array(
      'label'       => __( 'Background', 'payment-page' ),
      'name'        => 'pricing_plan_select_background_color',
      'type'        => 'css_style_background_color',
      'default'     => '#dedef6'
    );

    $fields[] = array(
      'label'       => __( 'Arrow Color', 'payment-page' ),
      'name'        => 'pricing_plan_select_arrow_color',
      'type'        => 'color',
      'default'     => '#5e3da8'
    );

    $fields = array_merge(
      $fields,
      payment_page_administration_payment_form_field_group_fields_typography(
        'pricing_plan_select_text',
        array(
          'color'          => '#5e3da8',
          'font_family'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY,
          'font_size'      => array(
            'unit' => 'px',
            'size' => 16,
          ),
          'font_weight'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_WEIGHT,
          'font_transform' => 'none',
        )
      )
    );

    $fields[] = array(
      'label'       => __( 'Pricing Plans Dropdown List', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields[] = array(
      'label'       => __( 'Background Color (Inactive)', 'payment-page' ),
      'name'        => 'pricing_plan_select_option_background_color',
      'type'        => 'css_style_background_color',
      'default'     => '#FFFFFF'
    );

    $fields[] = array(
      'label'       => __( 'Background Color (Active)', 'payment-page' ),
      'name'        => 'pricing_plan_select_option_active_background_color',
      'type'        => 'css_style_background_color',
      'default'     => '#0000FF'
    );

    return array(
      'label'   => __( 'Pricing Plans', 'payment-page' ),
      'group'   => 'pricing_plan_styles',
      'section' => 'style',
      'fields'  => $fields
    );
	}

}
