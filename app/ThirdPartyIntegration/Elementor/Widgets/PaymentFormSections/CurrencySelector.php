<?php

namespace PaymentPage\ThirdPartyIntegration\Elementor\Widgets\PaymentFormSections;

use Elementor\Controls_Manager;

class CurrencySelector extends Skeleton {

	public $control_alias = 'switcher';

	private $_defaultBorderStyle = array(
		'border_color'  => '#CEC3E6',
		'border_radius' => array(
			'unit' => 'px',
			'size' => 0,
		),
		'border_size'   => array(
			'unit' => 'px',
			'size' => 0,
		),
	);

	public function attach_controls() {
		$this->elementorWidgetInstance->start_controls_section(
			'currency_selector_section',
			array(
				'label' => 'Currency Options',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

    $this->elementorWidgetInstance->add_control(
      'currency_selector',
      array(
        'label'     => __( 'Currency Filter', 'payment-page' ),
        'type'      => \Elementor\Controls_Manager::SWITCHER,
        'label_on'  => __( 'On', 'payment-page' ),
        'label_off' => __( 'Off', 'payment-page' ),
        'default'   => 'yes',
      )
    );

		$this->elementorWidgetInstance->add_control(
			'currency_symbol',
			array(
				'label'     => __( 'Currency Symbol', 'payment-page' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'label_on'  => __( 'Symbol', 'payment-page' ),
				'label_off' => __( 'Text', 'payment-page' ),
				'default'   => 'no',
			)
		);

		$this->elementorWidgetInstance->end_controls_section();

    $this->elementorWidgetInstance->start_controls_section(
      'section_' . $this->control_alias . '_style',
      array(
        'label' => __( 'Filter - Switcher', 'payment-page' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      )
    );

    payment_page_elementor_builder_attach_popover_typography(
      $this->elementorWidgetInstance,
      $this->control_alias,
      'text',
      array(
        'font_family'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY,
        'font_size'      => array(
          'unit' => 'px',
          'size' => 12,
        ),
        'font_weight'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_WEIGHT,
        'font_transform' => 'uppercase',
      )
    );
    payment_page_elementor_builder_attach_background_control( $this->elementorWidgetInstance, $this->control_alias, 'button_active', 'Button Background Color (Active)', '#470fc6' );
    payment_page_elementor_builder_attach_background_control( $this->elementorWidgetInstance, $this->control_alias, 'button', 'Button Background Color (Inactive)', 'transparent' );
    payment_page_elementor_builder_attach_background_control( $this->elementorWidgetInstance, $this->control_alias, 'text_active', 'Active Text color', 'white' );
    payment_page_elementor_builder_attach_background_control( $this->elementorWidgetInstance, $this->control_alias, 'text_inactive', 'Inactive Text color', '#470fc6' );
    payment_page_elementor_builder_attach_padding_control( $this->elementorWidgetInstance, $this->control_alias, 'button' );

    payment_page_elementor_builder_attach_heading_control( $this->elementorWidgetInstance, $this->control_alias, 'button_selector', 'Selector Border' );
    payment_page_elementor_builder_attach_border_control( $this->elementorWidgetInstance, $this->control_alias, 'button_selector', $this->_defaultBorderStyle );
    payment_page_elementor_builder_attach_padding_control( $this->elementorWidgetInstance, $this->control_alias, 'button_selector' );

    payment_page_elementor_builder_attach_heading_control( $this->elementorWidgetInstance, $this->control_alias, 'button_active', 'Active border' );
    payment_page_elementor_builder_attach_border_control( $this->elementorWidgetInstance, $this->control_alias, 'button_active', $this->_defaultBorderStyle );

    payment_page_elementor_builder_attach_heading_control( $this->elementorWidgetInstance, $this->control_alias, 'button', 'Inactive Border' );
    payment_page_elementor_builder_attach_border_control( $this->elementorWidgetInstance, $this->control_alias, 'button', $this->_defaultBorderStyle );

    $this->elementorWidgetInstance->end_controls_section();

    $this->elementorWidgetInstance->start_controls_section(
      'section_' . $this->control_alias . '_select_style',
      array(
        'label' => __( 'Filter - Dropdown', 'payment-page' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      )
    );

    payment_page_elementor_builder_attach_background_control( $this->elementorWidgetInstance, $this->control_alias, 'select', null, '#dedef6' );
    payment_page_elementor_builder_attach_arrow_control( $this->elementorWidgetInstance, $this->control_alias, 'select_arrow', __( 'Arrow Color', 'payment-page' ), '#5e3da8' );
    payment_page_elementor_builder_attach_color_control( $this->elementorWidgetInstance, $this->control_alias, 'select', null, '#5e3da8' );
    payment_page_elementor_builder_attach_popover_typography(
      $this->elementorWidgetInstance,
      $this->control_alias,
      'select',
      array(
        'font_family'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY,
        'font_size'      => array(
          'unit' => 'px',
          'size' => 16,
        ),
        'font_weight'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_WEIGHT,
        'font_transform' => 'none',
      )
    );

    $this->elementorWidgetInstance->add_control(
      'section_' . $this->control_alias . '_select_style_border',
      array(
        'label'     => __( 'Border', 'payment-page' ),
        'type'      => Controls_Manager::HEADING,
        'separator' => 'before',
      )
    );
    payment_page_elementor_builder_attach_border_control(
      $this->elementorWidgetInstance,
      $this->control_alias,
      'select',
      array(
        'border_color'  => '#CEC3E6',
        'border_radius' => array(
          'unit' => 'px',
          'size' => 5,
        ),
        'border_size'   => array(
          'unit' => 'px',
          'size' => 1,
        ),
      )
    );

    $this->elementorWidgetInstance->end_controls_section();
	}
}
