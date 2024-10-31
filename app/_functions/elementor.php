<?php

use Elementor\Controls_Manager;
use Elementor\Core\Schemes;

function payment_page_elementor_control_pricing_frequencies() :array {
  return [
    [
      'value' => 'one-time',
      'label' => 'One-time'
    ],
    [
      'value' => 'd_1',
      'label' => 'Daily'
    ],
    [
      'value' => 'w_1',
      'label' => 'Weekly'
    ],
    [
      'value' => 'm_1',
      'label' => 'Monthly'
    ],
    [
      'value' => 'm_3',
      'label' => 'Quarterly'
    ],
    [
      'value' => 'm_6',
      'label' => 'Every 6 months'
    ],
    [
      'value' => 'y_1',
      'label' => 'Annually'
    ],
    [
      'value' => 'y_3',
      'label' => 'Every 3 years'
    ]
  ];
}

/**
 * This is the US List of Stripe supported currencies, which was previously returned through API call.
 * @deprecated Please use payment_page_currencies instead, this will be removed at one point.
 * @return string[]
 */
function payment_page_elementor_control_pricing_currencies() :array {
  return payment_page_currencies();
}

function payment_page_elementor_control_pricing_default_payment_values() :array {
  $response = [
    [
      "price"     => 99,
      "currency"  => "usd",
      "frequency" => [
        'value' => 'one-time',
        'label' => 'One-time'
      ]
    ],
    [
      "price"     => 99,
      "currency"  => "usd",
      "frequency" => [
        'value' => 'm_1',
        'label' => 'Monthly'
      ]
    ],
    [
      "price"     => 89,
      "currency"  => "eur",
      "frequency" => [
        'value' => 'm_1',
        'label' => 'Monthly'
      ]
    ],
    [
      "price"     => 999,
      "currency"  => "usd",
      "frequency" => [
        'value' => 'y_1',
        'label' => 'Annually'
      ]
    ],
    [
      "price"     => 899,
      "currency"  => "eur",
      "frequency" => [
        'value' => 'y_1',
        'label' => 'Annually'
      ]
    ]
  ];

  return $response;
}

function payment_page_elementor_builder_font_weight_assoc() :array {
  return [
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
  ];
}

function payment_page_elementor_builder_text_transform_assoc() :array {
  return [
    ''           => __('Default', 'elementor'),
    'uppercase'  => __('Uppercase', 'elementor'),
    'lowercase'  => _x('Lowercase', 'Typography Control', 'elementor'),
    'capitalize' => _x('Capitalize', 'Typography Control', 'elementor'),
    'none'       => _x('Normal', 'Typography Control', 'elementor')
  ];
}

if( !function_exists( 'payment_page_elementor_builder_attach_heading_control' ) ) {

  function payment_page_elementor_builder_attach_heading_control( $elementor, $control_name, $field_name, $field_label=null ) {
    $elementor->add_control( $control_name . '_' . $field_name . '_styles_heading', [
      'label'     => __( $field_label ? $field_label : 'Field labels', 'payment-page'),
      'type'      => Controls_Manager::HEADING,
      'separator' => 'before'
    ]);
  }

}

if( !function_exists( 'payment_page_elementor_builder_attach_border_control' ) ) {

  /**
   * @param $elementor
   * @param $control_name
   * @param $field_name
   * @param null $defaults
   */
  function payment_page_elementor_builder_attach_border_control( $elementor, $control_name, $field_name, $defaults=null, $extended_border = false ) {
    $elementor->add_control($control_name.'_'.$field_name.'_border_color', [
      'label'   => __('Color', 'payment-page'),
      'type'    => Controls_Manager::COLOR,
      'default' => $defaults['border_color'] ?? '#CEC3E6',
      'scheme'  => [
        'type'  => Schemes\Color::get_type(),
        'value' => Schemes\Color::COLOR_1
      ]
    ]);

    if( $extended_border ) {
      $default_border_radius = $defaults['border_size'] ?? [ 'unit' => 'px', 'size' => 1 ];

      $elementor->add_control( $control_name.'_'.$field_name.'_border_size', [
        'label'      => esc_html__( 'Width', 'elementor' ),
        'type'       => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'default'    => [
          'unit'     => $default_border_radius[ 'unit' ],
          'top'      => ( $default_border_radius[ 'size_top' ] ?? $default_border_radius[ 'size' ] ),
          'right'    => ( $default_border_radius[ 'size_right' ] ?? $default_border_radius[ 'size' ] ),
          'bottom'   => ( $default_border_radius[ 'size_bottom' ] ?? $default_border_radius[ 'size' ] ),
          'left'     => ( $default_border_radius[ 'size_left' ] ?? $default_border_radius[ 'size' ] ),
          'isLinked' => ( ( $default_border_radius[ 'size_top' ] ?? $default_border_radius[ 'size' ] ) === ( $default_border_radius[ 'size_right' ] ?? $default_border_radius[ 'size' ] ) )
        ],
      ]);
    } else {
      $elementor->add_control( $control_name.'_'.$field_name.'_border_size', [
        'label'      => __('Width', 'payment-page'),
        'type'       => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em' ],
        'default'    => $defaults['border_size'] ?? [ 'unit' => 'px', 'size' => 1 ],
        'range'      => [ 'px' => [ 'min' => 0, 'max' => 20 ] ],
        'responsive' => true
      ]);
    }

    $default_border_radius = $defaults['border_radius'] ?? [ 'unit' => 'px', 'size' => 3 ];

    $elementor->add_control( $control_name.'_'.$field_name.'_border_radius', [
      'label'      => esc_html__( 'Border Radius', 'elementor' ),
      'type'       => Controls_Manager::DIMENSIONS,
      'size_units' => [ 'px', '%' ],
      'default'    => [
        'unit'     => $default_border_radius[ 'unit' ],
        'top'      => ( $default_border_radius[ 'size_top' ] ?? $default_border_radius[ 'size' ] ),
        'right'    => ( $default_border_radius[ 'size_right' ] ?? $default_border_radius[ 'size' ] ),
        'bottom'   => ( $default_border_radius[ 'size_bottom' ] ?? $default_border_radius[ 'size' ] ),
        'left'     => ( $default_border_radius[ 'size_left' ] ?? $default_border_radius[ 'size' ] ),
        'isLinked' => ( ( $default_border_radius[ 'size_top' ] ?? $default_border_radius[ 'size' ] ) === ( $default_border_radius[ 'size_right' ] ?? $default_border_radius[ 'size' ] ) )
      ],
    ]);
  }
}


if( !function_exists( 'payment_page_elementor_builder_attach_background_control' ) ) {

  function payment_page_elementor_builder_attach_background_control( $elementor, $control_name, $field_name, $field_label=null, $defaults=null ) {
    $elementor->add_control($control_name. '_'.$field_name.'_background_color', [
      'label'   => empty( $field_label ) ? __( 'Background', 'payment-page' ) : $field_label,
      'type'    => Controls_Manager::COLOR,
      'default' => $defaults ? $defaults : "#ffffff",
      'scheme'  => [
        'type' => Schemes\Color::get_type(),
        'value' => Schemes\Color::COLOR_1
      ]
    ]);
  }

}

if( !function_exists( 'payment_page_elementor_builder_attach_arrow_control' ) ) {

  function payment_page_elementor_builder_attach_arrow_control( $elementor, $control_name, $field_name, $field_label=null, $defaults=null ) {
    $elementor->add_control($control_name. '_'.$field_name.'_color', [
      'label'   => empty( $field_label ) ? __( 'Background', 'payment-page' ) : $field_label,
      'type'    => Controls_Manager::COLOR,
      'default' => $defaults ? $defaults : "#ffffff",
      'scheme'  => [
        'type' => Schemes\Color::get_type(),
        'value' => Schemes\Color::COLOR_1
      ]
    ]);
  }

}

if( !function_exists( 'payment_page_elementor_builder_attach_switch_control' ) ) {

  function payment_page_elementor_builder_attach_switch_control( $elementor, $control_name, $field_name, $defaults=null ) {
    $elementor->add_control( $control_name.'_'.$field_name.'_display', [
      'label'     => __('On/Off', 'payment-page'),
      'type'      => \Elementor\Controls_Manager::SWITCHER,
      'label_on'  => __('On', 'payment-page'),
      'label_off' => __('Off', 'payment-page'),
      'default'   =>  $defaults ? $defaults : 'yes'
    ]);
  }

}

if( !function_exists( '_payment_page_elementor_builder_attach_spacing_control' ) ) {

  function _payment_page_elementor_builder_attach_spacing_control( $elementor, $control_name, $field_name, $field_label=null, $default=null, $option_key = 'spacing' ) {
    $default_padding = $default ?? [ 'unit' => 'px', 'size' => 0 ];

    $elementor->add_control( $control_name.'_'.$field_name . '_' . $option_key, [
      'label'      => ( empty( $field_label ) ? __( 'Spacing', 'payment-page' ) : $field_label ),
      'type'       => Controls_Manager::DIMENSIONS,
      'size_units' => [ 'px', 'em', '%' ],
      'default'    => [
        'unit'     => $default_padding[ 'unit' ],
        'top'      => ( $default_padding[ 'size_top' ] ?? $default_padding[ 'size' ] ),
        'right'    => ( $default_padding[ 'size_right' ] ?? $default_padding[ 'size' ] ),
        'bottom'   => ( $default_padding[ 'size_bottom' ] ?? $default_padding[ 'size' ] ),
        'left'     => ( $default_padding[ 'size_left' ] ?? $default_padding[ 'size' ] ),
        'isLinked' => ( ( $default_padding[ 'size_top' ] ?? $default_padding[ 'size' ] ) === ( $default_padding[ 'size_right' ] ?? $default_padding[ 'size' ] ) )
      ],
    ]);
  }

}

if( !function_exists( 'payment_page_elementor_builder_attach_padding_control' ) ) {

  function payment_page_elementor_builder_attach_padding_control( $elementor, $control_name, $field_name, $field_label=null, $default=null ) {
    _payment_page_elementor_builder_attach_spacing_control( $elementor, $control_name, $field_name, ( empty( $field_label ) ? __( 'Padding', 'payment-page' ) : $field_label ), $default, 'padding' );
  }

}

if( !function_exists( 'payment_page_elementor_builder_attach_margin_control' ) ) {

  function payment_page_elementor_builder_attach_margin_control( $elementor, $control_name, $field_name, $field_label=null, $default=null ) {
    _payment_page_elementor_builder_attach_spacing_control( $elementor, $control_name, $field_name, ( empty( $field_label ) ? __( 'Margin', 'payment-page' ) : $field_label ), $default, 'margin' );
  }

}

if( !function_exists( 'payment_page_elementor_builder_attach_color_control' ) ) {

  function payment_page_elementor_builder_attach_color_control($elementor, $control_name, $field_name, $field_label=null, $defaults=null){
    return $elementor->add_control($control_name.'_'.$field_name.'_color', [
      'label'   => ( empty( $field_label ) ? __( 'Text Color', 'elementor' ) : $field_label ),
      'type'    => \Elementor\Controls_Manager::COLOR,
      'default' =>  empty( $defaults ) ? '#8676AA' : $defaults,
      'scheme'  => [
        'type'  => \Elementor\Core\Schemes\Color::get_type(),
        'value' => \Elementor\Core\Schemes\Color::COLOR_1
      ]
    ]);
  }

}

if( !function_exists( 'payment_page_elementor_builder_attach_popover_typography' ) ) {

  function payment_page_elementor_builder_attach_popover_typography( $elementor, $control_name, $field_name, $defaults = null ) {

    $elementor->add_control( $control_name . '_' . $field_name . '_typography', [
      'label'        => __('Typography', 'payment-page'),
      'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
      'label_off'    => __('Default', 'payment-page'),
      'label_on'     => __('Custom', 'payment-page'),
      'return_value' => 'yes'
    ]);
    $elementor->start_popover();
    $elementor->add_control($control_name.'_'.$field_name.'_font_family', [
      'label'   => __('Font Family', 'payment-page'),
      'type'    => \Elementor\Controls_Manager::FONT,
      'default' => $defaults['font_family'] ?? PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY
    ]);
    $elementor->add_control( $control_name.'_'.$field_name.'_font_size', [
      'label'      => __('Size', 'payment-page'),
      'type'       => \Elementor\Controls_Manager::SLIDER,
      'size_units' => [ 'px', '%', 'em' ],
      'default'    => $defaults['font_size'] ?? [ 'unit' => 'px', 'size' => 15 ],
      'range'      => [ 'px' => [ 'min' => 1, 'max' => 200 ] ],
      'responsive' => true
    ]);

    $elementor->add_control($control_name.'_'.$field_name.'_font_weight', [
      'label'   => _x('Weight', 'Typography Control', 'elementor'),
      'type'    => \Elementor\Controls_Manager::SELECT,
      'default' => ( $defaults['font_weight'] ?? PAYMENT_PAGE_STYLE_DEFAULT_FONT_WEIGHT ),
      'options' => payment_page_elementor_builder_font_weight_assoc()
    ]);

    $elementor->add_control( $control_name.'_'.$field_name.'_font_transform', [
      'label'   => _x('Transform', 'Typography Control', 'elementor'),
      'type'    => \Elementor\Controls_Manager::SELECT,
      'default' => ( $defaults['font_transform'] ?? 'none'),
      'options' => payment_page_elementor_builder_text_transform_assoc()
    ]);

    $elementor->end_popover();
    return $elementor;
  }

}

if( !function_exists( 'payment_page_elementor_builder_attach_popover_box_shadow' ) ) {

  function payment_page_elementor_builder_attach_popover_box_shadow( $elementor, $control_name, $field_name,$field_label=null ) {
    $elementor->add_control($control_name.'_'.$field_name.'_box_shadow', [
      'label'        => __('Box Shadow', 'payment-page'),
      'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
      'label_off'    => __('Default', 'payment-page'),
      'label_on'     => __('Custom', 'payment-page'),
      'return_value' => 'yes'
    ]);
    $elementor->start_popover();
    $elementor->add_control($control_name.'_'.$field_name.'_box_shadow_color', [
      'label'   => __( $field_label ? $field_label : 'Color', 'elementor'),
      'type'    => \Elementor\Controls_Manager::COLOR,
      'default' => 'transparent',
      'scheme' => [
        'type'  => \Elementor\Core\Schemes\Color::get_type(),
        'value' => \Elementor\Core\Schemes\Color::COLOR_1
      ]
    ]);

    foreach( ['horizontal', 'vertical', 'blur', 'spread'] as $property )
      $elementor->add_control( $control_name.'_'.$field_name.'_box_shadow_'.$property, [
        'label'      => __( ucwords($property), 'payment-page'),
        'type'       => Controls_Manager::SLIDER,
        'size_units' => [ 'px', '%', 'em' ],
        'default'    => ['unit' => 'px', 'size' => 0 ],
        'range'      => [ 'px' => [ 'min' => -100, 'max' => 100 ] ],
        'responsive' => true
      ]);

    $elementor->add_control($control_name.'_'.$field_name.'_box_shadow_position', [
      'label'   => __('Position', 'elementor-pro'),
      'type'    => Controls_Manager::SELECT,
      'options' => [
        'inset'   => 'Inset',
        'outline' => 'Outline',
      ],
      'render_type' => 'none',
      'label_block' => true,
    ]);

    $elementor->end_popover();

    return $elementor;
  }

}