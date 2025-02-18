<?php

namespace PaymentPage\ThirdPartyIntegration\Elementor\Widgets\PaymentFormSections;

use Elementor\Controls_Manager;
use PaymentPage\ThirdPartyIntegration\Freemius as FS_Integration;

class ActionsForm extends Skeleton {

	private $_defaultFontFamily = array(
		'font_family'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY,
		'font_size'      => array(
			'unit' => 'px',
			'size' => 16,
		),
		'font_weight'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_WEIGHT,
		'font_transform' => 'none',
	);

	public function attach_controls() {
		$this->elementorWidgetInstance->start_controls_section(
			'section_integration',
			array(
				'label' => __( 'Actions After Submit', 'elementor-pro' ),
			)
		);

		$this->elementorWidgetInstance->add_control(
			'submit_actions',
			array(
				'label'       => __( 'Add Action', 'elementor-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => payment_page_form_submit_actions_assoc(),
				'render_type' => 'none',
				'label_block' => true,
				'default'     => array( 'email' ),
				'description' => __( 'Add actions that will be performed after a visitor submits the form (e.g. send an email notification). Choosing an action will add its setting below.', 'elementor-pro' ),
			)
		);

		$this->elementorWidgetInstance->end_controls_section();

		$this->_styles_settings();
		$this->_register_email_action();
		$this->_register_redirect_action();
		$this->_register_dynamic_message_action();

    $this->elementorWidgetInstance->start_controls_section(
      'section_http_request',
      array(
        'label'     => __( 'HTTP Request', 'payment-page' ),
        'tab'       => Controls_Manager::TAB_CONTENT,
        'condition' => array(
          'submit_actions' => 'http_request',
        ),
      )
    );

    $this->elementorWidgetInstance->add_control(
      'http_request_url',
      array(
        'label'       => __( 'HTTP Request URL', 'payment-page' ),
        'type'        => \Elementor\Controls_Manager::URL,
        'placeholder' => __( 'Type your http request URL here', 'payment-page' ),
        'description' => __( 'Send a HTTP Request on successful payments to the following URL', 'payment-page' ),
      )
    );

    $this->elementorWidgetInstance->end_controls_section();
	}

	private function _styles_settings() {
		$this->elementorWidgetInstance->start_controls_section(
			'section_dynamic_message_style',
			array(
				'label' => __( 'Dynamic Message', 'payment-page' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->elementorWidgetInstance->add_control(
			'section_dynamic_message_style_success_title',
			array(
				'label'     => __( 'Payment Success Title', 'payment-page' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		payment_page_elementor_builder_attach_color_control( $this->elementorWidgetInstance, '', 'payment_success_title', 'Font-color', '#333333' );
		payment_page_elementor_builder_attach_popover_typography( $this->elementorWidgetInstance, '', 'payment_success_title', $this->_defaultFontFamily );

		$this->elementorWidgetInstance->add_control(
			'section_dynamic_message_style_success_message',
			array(
				'label'     => __( 'Payment Success Message', 'payment-page' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		payment_page_elementor_builder_attach_color_control( $this->elementorWidgetInstance, '', 'payment_success_content', 'Font-color', '#333333' );
		payment_page_elementor_builder_attach_popover_typography( $this->elementorWidgetInstance, '', 'payment_success_content', $this->_defaultFontFamily );

		$this->elementorWidgetInstance->add_control(
			'section_dynamic_message_style_success_details_label',
			array(
				'label'     => __( 'Payment Success Detail Label', 'payment-page' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		payment_page_elementor_builder_attach_color_control( $this->elementorWidgetInstance, '', 'payment_success_details_label', 'Font-color', '#333333' );
		payment_page_elementor_builder_attach_popover_typography( $this->elementorWidgetInstance, '', 'payment_success_details_label', $this->_defaultFontFamily );

		$this->elementorWidgetInstance->add_control(
			'section_dynamic_message_style_success_details',
			array(
				'label'     => __( 'Payment Success Detail Value', 'payment-page' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		payment_page_elementor_builder_attach_color_control( $this->elementorWidgetInstance, '', 'payment_success_details', 'Font-color', '#333333' );
		payment_page_elementor_builder_attach_popover_typography( $this->elementorWidgetInstance, '', 'payment_success_details', $this->_defaultFontFamily );

		// Section failure
		$this->elementorWidgetInstance->add_control(
			'section_dynamic_message_style_failure_message',
			array(
				'label'     => __( 'Payment Failure Message', 'payment-page' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		payment_page_elementor_builder_attach_color_control( $this->elementorWidgetInstance, '', 'dynamic_message_error_text_color', 'Font-color', '#333333' );
		payment_page_elementor_builder_attach_popover_typography( $this->elementorWidgetInstance, '', 'dynamic_message_failure_label', $this->_defaultFontFamily );

		$this->elementorWidgetInstance->end_controls_section();
	}

	private function _register_email_action() {
		$this->elementorWidgetInstance->start_controls_section(
			'section_email',
			array(
				'label'     => 'Email',
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'submit_actions' => 'email',
				),
			)
		);

		$this->elementorWidgetInstance->add_control(
			'email_to',
			array(
				'label'       => __( 'To', 'payment-page' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => get_option( 'admin_email' ),
				'placeholder' => get_option( 'admin_email' ),
				'label_block' => true,
				'title'       => __( 'Separate emails with commas', 'payment-page' ),
				'render_type' => 'none',
			)
		);

		$default_message_admin = sprintf( __( 'New Payment from "%s"', 'payment-page' ), get_option( 'blogname' ) );
		$default_message_payer = sprintf( __( 'Payment Details from "%s"', 'payment-page' ), get_option( 'blogname' ) );

		$this->elementorWidgetInstance->add_control(
			'email_subject_admin',
			array(
				'label'       => __( 'Subject Admin', 'payment-page' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $default_message_admin,
				'placeholder' => $default_message_admin,
				'label_block' => true,
				'render_type' => 'none',
			)
		);

		$this->elementorWidgetInstance->add_control(
			'email_subject_payer',
			array(
				'label'       => __( 'Subject Payer', 'payment-page' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $default_message_payer,
				'placeholder' => $default_message_payer,
				'label_block' => true,
				'render_type' => 'none',
			)
		);

		$this->elementorWidgetInstance->add_control(
			'email_from',
			array(
				'label'       => __( 'From email', 'payment-page' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'render_type' => 'none',
			)
		);

		$this->elementorWidgetInstance->add_control(
			'email_from_name',
			array(
				'label'       => __( 'From name', 'payment-page' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'render_type' => 'none',
			)
		);

		$this->elementorWidgetInstance->end_controls_section();
	}

	private function _register_redirect_action() {
		$this->elementorWidgetInstance->start_controls_section(
			'section_redirect_to',
			array(
				'label'     => __( 'Redirect', 'payment-page' ),
				'condition' => array(
					'submit_actions' => 'redirect_to',
				),
			)
		);

		$this->elementorWidgetInstance->add_control(
			'redirect_to_url',
			array(
				'label'       => __( 'Redirect to URL after Sucessful Payment', 'payment-page' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'Type your redirect URL here', 'payment-page' ),
			)
		);

		$this->elementorWidgetInstance->end_controls_section();
	}

	private function _register_dynamic_message_action() {
		$this->elementorWidgetInstance->start_controls_section(
			'dynamic_message',
			array(
				'label'     => 'Dynamic Message',
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'submit_actions' => 'dynamic_message',
				),
			)
		);

		$this->elementorWidgetInstance->add_control(
			'success_message',
			array(
				'label'       => __( 'Payment Success Message:', 'elementor-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'render_type' => 'none',
			)
		);

		$this->elementorWidgetInstance->add_control(
			'payment_details_title',
			array(
				'label'       => __( 'Payment Details Title:', 'elementor-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'render_type' => 'none',
			)
		);

		$this->elementorWidgetInstance->add_control(
			'payment_details',
			array(
				'label'     => __( 'Show Payment Details?', 'elementor-pro' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'label_on'  => __( 'On', 'payment-page' ),
				'label_off' => __( 'Off', 'payment-page' ),
				'default'   => 'yes',
			)
		);

		$this->elementorWidgetInstance->add_control(
			'failure_message',
			array(
				'label'       => __( 'Payment Failure Message:', 'elementor-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'render_type' => 'none',
			)
		);

		$this->elementorWidgetInstance->end_controls_section();
	}
}
