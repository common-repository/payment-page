<?php

namespace PaymentPage\ThirdPartyIntegration\Elementor\Widgets\PaymentFormSections;

use Elementor\Controls_Manager;

class SubmitButtonControl extends Skeleton {

	public $control_alias = 'submit';

	public function attach_controls() {
		$this->elementorWidgetInstance->start_controls_section(
			'section_pricing_plans',
			array(
				'label' => __( 'Submit Button', 'payment-page' ),
			)
		);
		$this->elementorWidgetInstance->add_control(
			'sorted_text_button',
			array(
				'label'       => __( 'Drag to reorder fields:', 'payment-page' ),
				'type'        => 'fieldssorted',
				'placeholder' => __( 'Type your title here', 'payment-page' ),
				'label_block' => true,
			)
		);
		$this->elementorWidgetInstance->end_controls_section();

		$this->elementorWidgetInstance->start_controls_section(
			'section_' . $this->control_alias . '_style',
			array(
				'label' => __( 'Submit Button', 'payment-page' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		payment_page_elementor_builder_attach_color_control( $this->elementorWidgetInstance, $this->control_alias, 'button_text', null, '#fff' );
		payment_page_elementor_builder_attach_background_control( $this->elementorWidgetInstance, $this->control_alias, 'button', null, '#470fc6' );
		payment_page_elementor_builder_attach_popover_typography(
			$this->elementorWidgetInstance,
			$this->control_alias,
			'button_text',
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
		payment_page_elementor_builder_attach_padding_control(
			$this->elementorWidgetInstance,
			$this->control_alias,
			'button',
			null,
			array(
				'unit' => 'px',
				'size' => 12,
			)
		);

		$this->elementorWidgetInstance->add_control(
			$this->control_alias . '_button_margin_top',
			array(
				'label'      => __( 'Spacing Top', 'payment-page' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 25,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
			)
		);

		payment_page_elementor_builder_attach_popover_box_shadow( $this->elementorWidgetInstance, $this->control_alias, 'button' );

		$this->elementorWidgetInstance->add_control(
			'section_submit_button_styles_heading',
			array(
				'label'     => __( 'Border', 'payment-page' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		payment_page_elementor_builder_attach_border_control(
			$this->elementorWidgetInstance,
			$this->control_alias,
			'button',
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
		);

		$this->elementorWidgetInstance->end_controls_section();
	}

}
