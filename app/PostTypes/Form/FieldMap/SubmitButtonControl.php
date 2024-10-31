<?php

namespace PaymentPage\PostTypes\Form\FieldMap;

class SubmitButtonControl extends Skeleton {

  public static function get_field_groups() {
    $response = array();

    $response[] = self::_get_field_group_content();
    $response[] = self::_get_field_group_style();

    return $response;
  }

  private static function _get_field_group_content() {
    return array(
      'label'   => __( 'Submit Button', 'payment-page' ),
      'group'   => 'payment_form_submit_button',
      'section' => 'content',
      'fields'  => array(
        array(
          'label'       => null,
          'name'        => 'submit_trigger_label',
          'type'        => 'message_builder',
          'pieces'      => array(
            array(
              'label'  => __( 'Price', 'payment-page' ),
              'name'   => 'totalPrice',
              'type'   => 'true_false'
            ),
            array(
              'label'  => __( 'Frequency', 'payment-page' ),
              'name'   => 'frequency',
              'type'   => 'true_false'
            ),
            array(
              'label'  => __( 'Currency', 'payment-page' ),
              'name'   => 'currency',
              'type'   => 'true_false'
            ),
            array(
              'label'  => __( 'Enter Custom Text 1', 'payment-page' ),
              'name'   => 'customText1',
              'type'   => 'text'
            ),
            array(
              'label'  => __( 'Enter Custom Text 2', 'payment-page' ),
              'name'   => 'customText2',
              'type'   => 'text'
            ),
            array(
              'label'  => __( 'Enter Custom Text 2', 'payment-page' ),
              'name'   => 'customText3',
              'type'   => 'text'
            )
          ),
          'default' => array(
            'token_order' => array('customText1', 'totalPrice', 'currency'),
            'token_map'   => array(
              'customText1' => __( "Pay", 'payment-page' ),
              'totalPrice'  => 1,
              'currency'    => 1
            )
          )
        )
      )
    );
  }

  private static function _get_field_group_style() {
    $fields = [];

    $fields[] = array(
      'label'       => __( 'Submit Button', 'payment-page' ),
      'type'        => 'header'
    );

    $fields[] = array(
      'label'       => __( 'Background', 'payment-page' ),
      'name'        => 'submit_button_background_color',
      'type'        => 'css_style_background_color',
      'default'     => '#470fc6'
    );

    $fields = array_merge(
      $fields,
      payment_page_administration_payment_form_field_group_fields_typography(
        'submit_button_text_color',
        array(
          'color'          => '#fff',
          'font_family'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY,
          'font_size'      => array(
            'unit' => 'px',
            'size' => 12,
          ),
          'font_weight'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_WEIGHT,
          'font_transform' => 'uppercase',
        )
      )
    );

    $fields[] = array(
      'label'       => __( 'Padding', 'payment-page' ),
      'name'        => 'submit_button_padding',
      'type'        => 'css_style_unit_dimensions',
      'size_units'  => [ 'px', '%', 'em' ],
      'default'     => [
        'unit'   => 'px',
        'size_1' => 12,
        'size_2' => 12,
        'size_3' => 12,
        'size_4' => 12,
      ]
    );

    $fields[] = array(
      'label'       => __( 'Spacing Top', 'payment-page' ),
      'name'        => 'submit_button_margin_top',
      'type'        => 'css_style_unit',
      'size_units'  => array( 'px', '%', 'em' ),
      'default'     => array(
        'unit' => 'px',
        'size' => 25,
      )
    );

    // @todo payment_page_elementor_builder_attach_popover_box_shadow( $this->elementorWidgetInstance, submit, 'button' );

    $fields[] = array(
      'label'       => __( 'Border', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields = array_merge(
      $fields,
      payment_page_administration_payment_form_field_group_fields_border(
        'submit_button',
        array(
          'border_color'  => 'transparent',
          'border_radius' => array(
            'unit' => 'px',
            'size' => 4,
          ),
          'border_size'   => array(
            'unit' => 'px',
            'size' => 1,
          ),
        )
      )
    );

    return array(
      'label'   => __( 'Submit Button', 'payment-page' ),
      'group'   => 'payment_form_submit_button_styles',
      'section' => 'style',
      'fields'  => $fields
    );
  }

}
