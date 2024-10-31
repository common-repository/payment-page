<?php

function payment_page_administration_payment_form_field_group_fields_typography( $field_prefix, $default_typography = array(), $fields = null ) {
  if( $fields === null )
    $fields = array( 'color', 'font_family', 'font_size', 'font_weight', 'font_transform' );

  $response = array();

  if(in_array('color', $fields))
    $response[] = array(
      'label'       => __( 'font-color', 'payment-page' ),
      'name'        => $field_prefix . '_color',
      'type'        => 'color',
      'default'     => ( $default_typography['color'] ?? '#333333' )
    );

  if(in_array('font_family', $fields))
    $response[] = array(
      'label'       => __( 'Font Family', 'payment-page' ),
      'name'        => $field_prefix . '_font_family',
      'type'        => 'select',
      'options'     => payment_page_font_list_assoc(),
      'default'     => ( $default_typography['font_family'] ?? PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY )
    );

  if(in_array('font_size', $fields))
    $response[] = array(
      'label'       => __( 'Size', 'payment-page' ),
      'name'        => $field_prefix . '_font_size',
      'type'        => 'css_style_unit',
      'size_units' => [ 'px', '%', 'em' ],
      'range'      => [ 'px' => [ 'min' => 1, 'max' => 200 ] ],
      'default'     => ( $default_typography['font_size'] ?? [ 'unit' => 'px', 'size' => 15 ] )
    );

  if(in_array('font_weight', $fields))
    $response[] = array(
      'label'       => __( 'Font Weight', 'payment-page' ),
      'name'        => $field_prefix . '_font_weight',
      'type'        => 'select',
      'options'     => [
        ''        => __( "Default", 'payment-page' ),
        'normal'  => __( "Normal", 'payment-page' ),
        'bold'    => __( "Bold", 'payment-page' ),
        100       => 100,
        200       => 200,
        300       => 300,
        400       => 400,
        500       => 500,
        600       => 600,
        700       => 700,
        800       => 800,
        900       => 900
      ],
      'default'     => ( $defaults['font_weight'] ?? PAYMENT_PAGE_STYLE_DEFAULT_FONT_WEIGHT )
    );

  if(in_array('font_transform', $fields))
    $response[] = array(
      'label'       => __( 'Font Transform', 'payment-page' ),
      'name'        => $field_prefix . '_font_transform',
      'type'        => 'select',
      'options'     => [
        ''           => __('Default', 'payment-page'),
        'uppercase'  => __('Uppercase', 'payment-page'),
        'lowercase'  => _x('Lowercase', 'Typography Control', 'payment-page'),
        'capitalize' => _x('Capitalize', 'Typography Control', 'payment-page'),
        'none'       => _x('Normal', 'Typography Control', 'payment-page')
      ],
      'default'     => ( $defaults['font_transform'] ?? 'none' )
    );

  return $response;
}

function payment_page_administration_payment_form_field_group_fields_border( $field_prefix, $defaults = array(), $preferences = array() ) {
  $response = array();

  $response[] = array(
    'label'       => __( 'Color', 'payment-page' ),
    'name'        => $field_prefix . '_border_color',
    'type'        => 'css_style_border_color',
    'default'     => ( $defaults['border_color'] ?? '#CEC3E6' )
  );

  if(!isset($preferences['border_size']) || $preferences['border_size'] !== 'single') {
    $response[] = array(
      'label'       => __( 'Width', 'payment-page' ),
      'name'        => $field_prefix . '_border_size',
      'type'        => 'css_style_unit_dimensions',
      'size_units' => [ 'px', '%', 'em' ],
      'default'     => ( $defaults['border_size'] ?? [ 'unit' => 'px', 'size' => 1 ] )
    );
  } else {
    $response[] = array(
      'label'       => __( 'Width', 'payment-page' ),
      'name'        => $field_prefix . '_border_size',
      'type'        => 'css_style_unit',
      'size_units' => [ 'px', '%', 'em' ],
      'default'     => ( $defaults['border_size'] ?? [ 'unit' => 'px', 'size' => 1 ] )
    );
  }

  $response[] = array(
    'label'       => __( 'Border Radius', 'payment-page' ),
    'name'        => $field_prefix . '_border_radius',
    'type'        => 'css_style_unit_dimensions',
    'size_units' => [ 'px', '%', 'em' ],
    'default'     => ( $defaults['border_size'] ?? [ 'unit' => 'px', 'size' => 0 ] )
  );

  return $response;
}

function payment_page_administration_payment_form_field_currencies() :array {
  $response = [];
  $currencies = payment_page_currencies();

  foreach( $currencies as $currency_code ) {
    $response[ $currency_code ] = strtoupper($currency_code);
  }

  return $response;
}

function payment_page_administration_payment_form_field_payment_frequencies() :array {
  $response = array();

  $response[ 'one-time' ] = __( 'One-time', 'payment-page' );
  $response[ 'd_1' ] = __( 'Daily', 'payment-page' );
  $response[ 'w_1' ] = __( 'Weekly', 'payment-page' );
  $response[ 'm_1' ] = __( 'Monthly', 'payment-page' );
  $response[ 'm_3' ] = __( 'Quarterly', 'payment-page' );
  $response[ 'm_6' ] = __( 'Every 6 months', 'payment-page' );
  $response[ 'y_1' ] = __( 'Annually', 'payment-page' );
  $response[ 'y_3' ] = __( 'Every 3 years', 'payment-page' );

  return $response;
}

function payment_page_administration_process_request_payment_page_settings( $settings ) {
  if(isset($settings['form_fields_map'])) {
    if(is_string($settings['form_fields_map'])) {
      $settings['form_fields_map'] = stripslashes( $settings['form_fields_map'] );

      $settings['form_fields_map'] = json_decode( $settings['form_fields_map'], true );
    }
  }

  if(isset($settings['pricing_selector_label'])) {
    if(isset($settings['pricing_selector_label']['token_order'])) {
      if(is_string($settings['pricing_selector_label']['token_order'])) {
        $settings['pricing_selector_label']['token_order'] = explode( ",", $settings['pricing_selector_label']['token_order'] );
      }
    }
  }

  if(isset($settings['submit_trigger_label'])) {
    if(isset($settings['submit_trigger_label']['token_order'])) {
      if(is_string($settings['submit_trigger_label']['token_order'])) {
        $settings['submit_trigger_label']['token_order'] = explode( ",", $settings['submit_trigger_label']['token_order'] );
      }
    }
  }

  // @todo Maybe implement sanitization, and everything required around this; at the given moment, only administrators would be able to do harm, if it's intended.
  foreach($settings as $k => $v) {
    if(is_string($v)) {
      $settings[$k] = stripslashes( $v );

      // This would break certain fields, including defaults
      // $settings[$k] = sanitize_text_field( $v );
      // Also, if done ,needs to be avoided on numeric.
    }
  }

  return $settings;
}