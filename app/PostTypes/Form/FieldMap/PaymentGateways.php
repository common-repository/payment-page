<?php

namespace PaymentPage\PostTypes\Form\FieldMap;

use PaymentPage\PaymentGateway as PP_PaymentGateway;
use PaymentPage\Settings;

class PaymentGateways extends Skeleton {

  public static function get_field_groups() {
    $response = array();

    $response[] = self::_get_field_group_content();
    $response[] = self::_get_field_group_style();

    return $response;
  }

  private static function _get_field_group_content() {
    $dashboard = PP_PaymentGateway::get_administration_dashboard();

    $fields = [];

    $fields[] = array(
      'label'       => __( 'Section Title', 'payment-page' ),
      'name'        => 'payment_method_title',
      'type'        => 'text',
      'layout'      => 'inline',
      'default'     => __( 'Payment Method', 'payment-page' ),
      'placeholder' => __( 'Type your section title', 'payment-page' ),
    );

    foreach ( $dashboard as $current_item ) {
      $fields[] = array(
        'label'       => $current_item['name'],
        'type'        => 'header',
        'separator'   => 'before'
      );

      if ( ! $current_item['mode_live_configured'] && ! $current_item['mode_test_configured'] ) {
        $fields[] = array(
          'label'       => null,
          'type'        => 'raw_html',
          'html'        => '<a style="color:#F44336;" 
                               target="_blank"
                               href="' . esc_url( admin_url( 'admin.php?page=' . PAYMENT_PAGE_MENU_SLUG ) ) . '#payment-gateways">' .
                              sprintf( __( 'Connect %s >', 'payment-page' ), $current_item['name'] ) .
                           '</a>'
        );

        continue;
      }

      foreach ( $current_item['payment_methods'] as $payment_method ) {
        $field_name = 'payment_method_' . $current_item['alias'] . '_' . $payment_method['alias'];

        if ( isset( $payment_method['is_available'] ) && ! $payment_method['is_available'] ) {
          $fields[] = array(
            'label'       => '<span style="opacity:0.6;">' . $payment_method['name'] . '</span>' .
                             '<a style="font-style: normal;font-size: 11px;cursor:pointer !important;margin:0 0 0 5px;" target="_blank" href="' . payment_page_fs()->get_upgrade_url() . '">Upgrade ></a>',
            'name'        => $field_name . '_disabled',
            'type'        => 'toggle',
            'default'     => 'no',
            'disabled'    => 'disabled',
            'toggle_label_on'  => __( 'On', 'payment-page' ),
            'toggle_label_off' => __( 'Off', 'payment-page' ),
            'toggle_value_on'  => 'yes',
            'toggle_value_off' => 'no'
          );
          continue;
        }

        if ( ! in_array( $payment_method['alias'], Settings::instance()->get( $current_item['alias'] . '_payment_methods' ) ) ) {
          $fields[] = array(
            'label'       => '<span style="opacity:0.6;">' . $payment_method['name'] . '</span>' .
                             '<a style="font-style: normal;font-size: 11px;cursor:pointer !important;margin:0 0 0 5px;" target="_blank" href="' . admin_url( PAYMENT_PAGE_DEFAULT_URL_PATH ) . '#payment-gateways">Enable ></a>',
            'name'        => $field_name . '_disabled',
            'type'        => 'toggle',
            'default'     => 'no',
            'disabled'    => 'disabled',
            'toggle_label_on'  => __( 'On', 'payment-page' ),
            'toggle_label_off' => __( 'Off', 'payment-page' ),
            'toggle_value_on'  => 'yes',
            'toggle_value_off' => 'no'
          );

          continue;
        }

        $fields[] = array(
          'label'       => $payment_method['name'],
          'name'        => $field_name,
          'type'        => 'toggle',
          'default'     => 'yes',
          'toggle_label_on'  => __( 'On', 'payment-page' ),
          'toggle_label_off' => __( 'Off', 'payment-page' ),
          'toggle_value_on'  => 'yes',
          'toggle_value_off' => 'no'
        );
      }
    }

    return array(
      'label'   => __( 'Payment Methods', 'payment-page' ),
      'group'   => 'section_payment_gateways',
      'section' => 'content',
      'fields'  => $fields
    );
  }

  private static function _get_field_group_style() {
    $fields = [];

    $fields[] = array(
      'label'       => null,
      'type'        => 'raw_html',
      'html'        => '<p>' . __( 'Payment Methods are displayed as Tabs (on the embedded payment form) or Buttons (to open the popup form). Their styling options are exactly the same, so you can easily decide which approach you want to take on this payment form.', 'payment-page' ) . '</p>',
    );

    $fields[] = array(
      'label'       => __( 'Section Title', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields = array_merge(
      $fields,
      payment_page_administration_payment_form_field_group_fields_typography(
        'payment_method_section_title_style',
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
      'label'   => __( 'Per Row', 'payment-page' ),
      'name'    => 'payment_method_items_per_row',
      'type'    => 'select',
      'default' => '3',
      'options' => array(
        '1' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,
      ),
    );

    $fields[] = array(
      'label'       => __( 'Vertical Spacing', 'payment-page' ),
      'name'        => 'payment_method_items_spacing',
      'type'        => 'css_style_unit',
      'size_units'  => array( 'px', '%', 'em' ),
      'default'     => array(
        'unit' => 'px',
        'size' => 10,
      )
    );

    $fields[] = array(
      'label'       => __( 'Item Height', 'payment-page' ),
      'name'        => 'payment_method_item_image_height',
      'type'        => 'css_style_unit',
      'size_units'  => array( 'px', '%', 'em' ),
      'default'     => array(
        'unit' => 'px',
        'size' => 40,
      )
    );

    $fields[] = array(
      'label'      => __( 'Padding (Inactive)', 'payment-page' ),
      'name'       => 'payment_method_item_inactive_padding',
      'type'       => 'css_style_unit_dimensions',
      'size_units' => [ 'px', '%', 'em' ],
      'default'    =>  array(
        'unit'   => 'px',
        'size_1' => 5,
        'size_2' => 5,
        'size_3' => 5,
        'size_4' => 5,
      )
    );

    $fields[] = array(
      'label'      => __( 'Padding (Active)', 'payment-page' ),
      'name'       => 'payment_method_item_active_padding',
      'type'       => 'css_style_unit_dimensions',
      'size_units' => [ 'px', '%', 'em' ],
      'default'    =>  array(
        'unit'   => 'px',
        'size_1' => 5,
        'size_2' => 5,
        'size_3' => 5,
        'size_4' => 5,
      )
    );

    $fields[] = array(
      'label'       => __( 'Border (Inactive)', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields = array_merge(
      $fields,
      payment_page_administration_payment_form_field_group_fields_border(
        'payment_method_item_inactive',
        array(
          'border_color'  => 'transparent',
          'border_radius' => array(
            'unit' => 'px',
            'size' => 0,
          ),
          'border_size'   => array(
            'unit'    => 'px',
            'size_1'  => 0,
            'size_2'  => 0,
            'size_3'  => 2,
            'size_4'  => 0,
          ),
        )
      )
    );

    $fields[] = array(
      'label'       => __( 'Border (Active)', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $fields = array_merge(
      $fields,
      payment_page_administration_payment_form_field_group_fields_border(
        'payment_method_item_active',
        array(
          'border_color'  => '#2676f1',
          'border_radius' => array(
            'unit' => 'px',
            'size' => 0,
          ),
          'border_size'   => array(
            'unit'    => 'px',
            'size_1'  => 0,
            'size_2'  => 0,
            'size_3'  => 2,
            'size_4'  => 0,
          ),
        )
      )
    );

    return array(
      'label'   => __( 'Payment Method Tabs/Buttons', 'payment-page' ),
      'group'   => 'section_payment_gateways',
      'section' => 'style',
      'fields'  => $fields
    );
  }
}
