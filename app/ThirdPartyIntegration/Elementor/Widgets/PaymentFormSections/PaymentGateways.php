<?php

namespace PaymentPage\ThirdPartyIntegration\Elementor\Widgets\PaymentFormSections;

use PaymentPage\PaymentGateway as PP_PaymentGateway;
use \Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use PaymentPage\Settings;

class PaymentGateways extends Skeleton {

	public $control_alias = 'payment_method';

	public function attach_controls() {
		$dashboard = PP_PaymentGateway::get_administration_dashboard();

		$this->elementorWidgetInstance->start_controls_section(
			'section_payment_gateways',
			array(
				'label' => __( 'Payment Methods', 'payment-page' ),
			)
		);

		$this->elementorWidgetInstance->add_control(
			'payment_method_title',
			array(
				'label'       => __( 'Section Title', 'payment-page' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Payment Method', 'payment-page' ),
				'placeholder' => __( 'Type your section title', 'payment-page' ),
			)
		);

		foreach ( $dashboard as $current_item ) {
			$this->elementorWidgetInstance->add_control(
				'section_' . $this->control_alias . '_' . $current_item['alias'],
				array(
					'label'     => $current_item['name'],
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			if ( ! $current_item['mode_live_configured'] && ! $current_item['mode_test_configured'] ) {
				$this->elementorWidgetInstance->add_control(
					'section_payment_gateway_connection_' . $current_item['alias'],
					array(
						'type' => \Elementor\Controls_Manager::RAW_HTML,
						'raw'  => '<a style="color:#F44336;" 
                            target="_blank"
                            href="' . esc_url( admin_url( 'admin.php?page=' . PAYMENT_PAGE_MENU_SLUG ) ) . '#payment-gateways">' .
								sprintf( __( 'Connect %s >', 'payment-page' ), $current_item['name'] ) .
							  '</a>',
					)
				);

				  continue;
			}

			foreach ( $current_item['payment_methods'] as $payment_method ) {
				if ( isset( $payment_method['is_available'] ) && ! $payment_method['is_available'] ) {
					$this->elementorWidgetInstance->add_control(
						$this->control_alias . '_' . $current_item['alias'] . '_' . $payment_method['alias'] . '_disabled',
						array(
							'label'     => '<span style="opacity:0.6;">' . $payment_method['name'] . '</span>' .
											 '<a style="font-style: normal;font-size: 11px;color: #fff;cursor:pointer !important;margin:0 0 0 5px;" target="_blank" href="' . payment_page_fs()->get_upgrade_url() . '">Upgrade ></a>',
							'type'      => 'switcher_disabled',
							'label_on'  => __( 'On', 'payment-page' ),
							'label_off' => __( 'Off', 'payment-page' ),
							'default'   => 'no',
						)
					);
					continue;
				}

				if ( ! in_array( $payment_method['alias'], Settings::instance()->get( $current_item['alias'] . '_payment_methods' ) ) ) {
					$this->elementorWidgetInstance->add_control(
						$this->control_alias . '_' . $current_item['alias'] . '_' . $payment_method['alias'] . '_disabled',
						array(
							'label'     => '<span style="opacity:0.6;">' . $payment_method['name'] . '</span>' .
								  '<a style="font-style: normal;font-size: 11px;color: #fff;cursor:pointer !important;margin:0 0 0 5px;" target="_blank" href="' . admin_url( PAYMENT_PAGE_DEFAULT_URL_PATH ) . '">Enable ></a>',
							'type'      => 'switcher_disabled',
							'label_on'  => __( 'On', 'payment-page' ),
							'label_off' => __( 'Off', 'payment-page' ),
							'default'   => 'no',
						)
					);
					continue;
				}

				$this->elementorWidgetInstance->add_control(
					$this->control_alias . '_' . $current_item['alias'] . '_' . $payment_method['alias'],
					array(
						'label'     => $payment_method['name'],
						'type'      => Controls_Manager::SWITCHER,
						'label_on'  => __( 'On', 'payment-page' ),
						'label_off' => __( 'Off', 'payment-page' ),
						'default'   => 'yes',
					)
				);
			}
		}

		$this->elementorWidgetInstance->end_controls_section();

		$this->elementorWidgetInstance->start_controls_section(
			$this->control_alias . '_section_title_style',
			array(
				'label' => __( 'Payment Method Tabs/Buttons', 'payment-page' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->elementorWidgetInstance->add_control(
			'section_payment_gateways_description',
			array(
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw'  => '<p style="color:#fff;">' . __( 'Payment Methods are displayed as Tabs (on the embedded payment form) or Buttons (to open the popup form). Their styling options are exactly the same, so you can easily decide which approach you want to take on this payment form.', 'payment-page' ) . '</p>',
			)
		);

		$field_name_title = 'section_title';

		$this->elementorWidgetInstance->add_control(
			$this->control_alias . '_' . $field_name_title . '_style_title',
			array(
				'label'     => __( 'Section Title', 'payment-page' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->elementorWidgetInstance->add_control(
			$this->control_alias . '_' . $field_name_title . '_style_color',
			array(
				'label'   => __( 'Title Color', 'payment-page' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#2676f1',
				'scheme'  => array(
					'type'  => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				),
			)
		);
		$this->elementorWidgetInstance->add_control(
			$this->control_alias . '_' . $field_name_title . '_style_typography',
			array(
				'label'        => __( 'Typography', 'payment-page' ),
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'Default', 'payment-page' ),
				'label_on'     => __( 'Custom', 'payment-page' ),
				'return_value' => 'yes',
			)
		);
		$this->elementorWidgetInstance->start_popover();
		$this->elementorWidgetInstance->add_control(
			$this->control_alias . '_' . $field_name_title . '_style_font_family',
			array(
				'label'   => __( 'Font Family', 'payment-page' ),
				'type'    => \Elementor\Controls_Manager::FONT,
				'default' => PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY,
			)
		);

		$this->elementorWidgetInstance->add_control(
			$this->control_alias . '_' . $field_name_title . '_style_font_size',
			array(
				'label'      => __( 'Size', 'payment-page' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 200,
					),
				),
				'responsive' => true,
			)
		);

		$this->elementorWidgetInstance->add_control(
			$this->control_alias . '_' . $field_name_title . '_style_font_weight',
			array(
				'label'   => _x( 'Weight', 'Typography Control', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => payment_page_elementor_builder_font_weight_assoc(),
			)
		);

		$this->elementorWidgetInstance->add_control(
			$this->control_alias . '_' . $field_name_title . '_style_font_transform',
			array(
				'label'   => _x( 'Transform', 'Typography Control', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => payment_page_elementor_builder_text_transform_assoc(),
			)
		);
		$this->elementorWidgetInstance->end_popover();

		$this->elementorWidgetInstance->add_control(
			$this->control_alias . '_items_per_row',
			array(
				'label'   => __( 'Per Row', 'payment-page' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 3,
				'options' => array(
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
				),
			)
		);

		$this->elementorWidgetInstance->add_control(
			$this->control_alias . '_items_spacing',
			array(
				'label'      => __( 'Item Spacing', 'payment-page' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 200,
					),
				),
			)
		);

		$this->elementorWidgetInstance->add_control(
			$this->control_alias . '_item_image_height',
			array(
				'label'      => __( 'Image Height', 'payment-page' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 40,
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 200,
					),
				),
			)
		);

		payment_page_elementor_builder_attach_padding_control(
			$this->elementorWidgetInstance,
			$this->control_alias,
			'item_inactive',
			__( 'Padding (Inactive)', 'payment-page' ),
			array(
				'unit' => 'px',
				'size' => 5,
			)
		);

		payment_page_elementor_builder_attach_padding_control(
			$this->elementorWidgetInstance,
			$this->control_alias,
			'item_active',
			__( 'Padding (Active)', 'payment-page' ),
			array(
				'unit' => 'px',
				'size' => 5,
			)
		);

		$this->elementorWidgetInstance->add_control(
			$this->control_alias . '_section_title_border_inactive',
			array(
				'label'     => __( 'Border (Inactive)', 'payment-page' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		payment_page_elementor_builder_attach_border_control(
			$this->elementorWidgetInstance,
			$this->control_alias,
			'item_inactive',
			array(
				'border_color'  => 'transparent',
				'border_radius' => array(
					'unit' => 'px',
					'size' => 0,
				),
				'border_size'   => array(
					'unit'        => 'px',
					'size_top'    => 0,
					'size_right'  => 0,
					'size_bottom' => 2,
					'size_left'   => 0,
				),
			),
			true
		);

		$this->elementorWidgetInstance->add_control(
			$this->control_alias . '_section_title_border_active',
			array(
				'label'     => __( 'Border (Active)', 'payment-page' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		payment_page_elementor_builder_attach_border_control(
			$this->elementorWidgetInstance,
			$this->control_alias,
			'item_active',
			array(
				'border_color'  => '#2676f1',
				'border_radius' => array(
					'unit' => 'px',
					'size' => 0,
				),
				'border_size'   => array(
					'unit'        => 'px',
					'size_top'    => 0,
					'size_right'  => 0,
					'size_bottom' => 2,
					'size_left'   => 0,
				),
			),
			true
		);

		$this->elementorWidgetInstance->end_controls_section();
	}
}
