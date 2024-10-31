<?php

namespace PaymentPage\ThirdPartyIntegration\Elementor\Widgets\PaymentFormSections;

use Elementor\Controls_Manager;

class SubscriptionSelector extends Skeleton {

	public function is_enabled() :bool {
		return true;
	}

	public function attach_controls() {
		$this->elementorWidgetInstance->start_controls_section(
			'subscription_selector_section',
			array(
				'label' => 'Subscription Filter',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

    $this->elementorWidgetInstance->add_control(
      'subscription_selector',
      array(
        'label'     => __( 'Subscription Filter', 'payment-page' ),
        'type'      => Controls_Manager::SWITCHER,
        'label_on'  => __( 'On', 'payment-page' ),
        'label_off' => __( 'Off', 'payment-page' ),
        'default'   => 'yes',
      )
    );

		$this->elementorWidgetInstance->end_controls_section();
	}
}
