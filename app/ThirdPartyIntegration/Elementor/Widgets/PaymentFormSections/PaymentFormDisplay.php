<?php

namespace PaymentPage\ThirdPartyIntegration\Elementor\Widgets\PaymentFormSections;

class PaymentFormDisplay extends Skeleton {

	public function attach_controls() {
		$this->elementorWidgetInstance->start_controls_section(
			'section_display',
			array(
				'label' => __( 'Payment Form Display', 'payment-page' ),
			)
		);

		$this->elementorWidgetInstance->add_control(
			'FREE_VERSION_MODE',
			array(
				'type'    => \Elementor\Controls_Manager::HIDDEN,
				'default' => payment_page_fs()->is_free_plan(),
			)
		);

		$this->elementorWidgetInstance->add_control(
			'display_form',
			array(
				'label'   => __( 'Select One:', 'payment-page' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'standard',
				'options' => array(
					'standard' => __( 'Inline/embed payment form', 'payment-page' ),
					'popup'    => __( 'Payment buttons (popup form)', 'payment-page' ),
				),
			)
		);

		$this->elementorWidgetInstance->end_controls_section();
	}
}
