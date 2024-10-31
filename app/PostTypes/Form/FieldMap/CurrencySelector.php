<?php

namespace PaymentPage\PostTypes\Form\FieldMap;

class CurrencySelector extends Skeleton {

	private static array $_defaultBorderStyle = array(
		'border_color'  => '#CEC3E6',
		'border_radius' => array(
			'unit'   => 'px',
      'size_1' => 0,
      'size_2' => 0,
      'size_3' => 0,
      'size_4' => 0,
		),
		'border_size'   => array(
			'unit'   => 'px',
			'size_1' => 0,
      'size_2' => 0,
      'size_3' => 0,
      'size_4' => 0,
		),
	);

	public static function get_field_groups() {
    $response = array();

    $fields_currency_options = array();

    $fields_currency_options[] = array(
      'label'       => __( 'Currency Filter', 'payment-page' ),
      'name'        => 'currency_selector',
      'type'        => 'toggle',
      'default'     => 'no',
      'toggle_label_on'  => __( 'On', 'payment-page' ),
      'toggle_label_off' => __( 'Off', 'payment-page' ),
      'toggle_value_on'  => 'yes',
      'toggle_value_off' => 'no'
    );

    $fields_currency_options[] = array(
      'label'       => __( 'Currency Symbol', 'payment-page' ),
      'name'        => 'currency_symbol',
      'type'        => 'toggle',
      'default'     => 'no',
      'toggle_label_on'  => __( 'Symbol', 'payment-page' ),
      'toggle_label_off' => __( 'Text', 'payment-page' ),
      'toggle_value_on'  => 'yes',
      'toggle_value_off' => 'no'
    );

    $response[] = array(
      'label'   => __( 'Currency Options', 'payment-page' ),
      'group'   => 'section_currency_options',
      'section' => 'content',
      'fields'  => $fields_currency_options
    );

    $filter_switcher_fields = [];
    $filter_dropdown_fields = [];

    $filter_switcher_fields = array_merge(
      $filter_switcher_fields,
      payment_page_administration_payment_form_field_group_fields_typography(
        'switcher_text',
        array(
          'font_family'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY,
          'font_size'      => array(
            'unit' => 'px',
            'size' => 12,
          ),
          'font_weight'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_WEIGHT,
          'font_transform' => 'uppercase',
        ),
        array( 'font_family', 'font_size', 'font_weight', 'font_transform' )
      )
    );

    $filter_switcher_fields[] = array(
      'label'       => __( 'Button Background Color (Active)', 'payment-page' ),
      'name'        => 'switcher_button_active_background_color',
      'type'        => 'css_style_background_color',
      'default'     => '#470fc6'
    );

    $filter_switcher_fields[] = array(
      'label'       => __( 'Button Background Color (Inactive)', 'payment-page' ),
      'name'        => 'switcher_button_background_color',
      'type'        => 'css_style_background_color',
      'default'     => 'transparent'
    );

    $filter_switcher_fields[] = array(
      'label'       => __( 'Active Text color', 'payment-page' ),
      'name'        => 'switcher_text_active_color',
      'type'        => 'color',
      'default'     => '#ffffff'
    );

    $filter_switcher_fields[] = array(
      'label'       => __( 'Inactive Text color', 'payment-page' ),
      'name'        => 'switcher_text_inactive_color',
      'type'        => 'color',
      'default'     => '#470fc6'
    );

    $filter_switcher_fields[] = array(
      'label'       => __( 'Padding', 'payment-page' ),
      'name'        => 'switcher_button_padding',
      'type'        => 'css_style_unit_dimensions',
      'size_units' => [ 'px', '%', 'em' ]
    );

    $filter_switcher_fields[] = array(
      'label'       => __( 'Selector Border', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $filter_switcher_fields = array_merge(
      $filter_switcher_fields,
      payment_page_administration_payment_form_field_group_fields_border( 'switcher_button_selector', self::$_defaultBorderStyle )
    );

    $filter_switcher_fields[] = array(
      'label'       => __( 'Padding', 'payment-page' ),
      'name'        => 'switcher_button_selector_padding',
      'type'        => 'css_style_unit_dimensions',
      'size_units' => [ 'px', '%', 'em' ]
    );

    $filter_switcher_fields[] = array(
      'label'       => __( 'Active border', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $filter_switcher_fields = array_merge(
      $filter_switcher_fields,
      payment_page_administration_payment_form_field_group_fields_border( 'switcher_button_active', self::$_defaultBorderStyle )
    );

    $filter_switcher_fields[] = array(
      'label'       => __( 'Inactive border', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $filter_switcher_fields = array_merge(
      $filter_switcher_fields,
      payment_page_administration_payment_form_field_group_fields_border( 'switcher_button', self::$_defaultBorderStyle )
    );

    /// Start $filter_dropdown_fields

    $filter_dropdown_fields[] = array(
      'label'       => __( 'Background color', 'payment-page' ),
      'name'        => 'switcher_select_background_color',
      'type'        => 'css_style_background_color',
      'default'     => '#dedef6'
    );

    $filter_dropdown_fields[] = array(
      'label'       => __( 'Arrow Color', 'payment-page' ),
      'name'        => 'switcher_select_arrow_color',
      'type'        => 'color',
      'default'     => '#5e3da8'
    );

    $filter_dropdown_fields = array_merge(
      $filter_dropdown_fields,
      payment_page_administration_payment_form_field_group_fields_typography(
        'switcher_select',
        array(
          'color'          => '#5e3da8',
          'font_family'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_FAMILY,
          'font_size'      => array(
            'unit' => 'px',
            'size' => 16,
          ),
          'font_weight'    => PAYMENT_PAGE_STYLE_DEFAULT_FONT_WEIGHT,
          'font_transform' => 'none',
        )
      )
    );

    $filter_dropdown_fields[] = array(
      'label'       => __( 'Border', 'payment-page' ),
      'type'        => 'header',
      'separator'   => 'before'
    );

    $filter_dropdown_fields = array_merge(
      $filter_dropdown_fields,
      payment_page_administration_payment_form_field_group_fields_border( 'switcher_select', array(
        'border_color'  => '#CEC3E6',
        'border_radius' => array(
          'unit' => 'px',
          'size' => 5,
        ),
        'border_size'   => array(
          'unit' => 'px',
          'size' => 1,
        ),
      ) )
    );

    $response[] = array(
      'label'   => __( 'Filter - Switcher', 'payment-page' ),
      'group'   => 'section_filter_switcher_style',
      'section' => 'style',
      'fields'  => $filter_switcher_fields
    );
    $response[] = array(
      'label'   => __( 'Filter - Dropdown', 'payment-page' ),
      'group'   => 'section_filter_dropdown_style',
      'section' => 'style',
      'fields'  => $filter_dropdown_fields
    );

    return $response;
	}
}
