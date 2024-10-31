<?php

namespace PaymentPage\PostTypes\Form\FieldMap;

class GlobalOptions extends Skeleton {

  public static function get_field_groups() {
    return array(
      array(
        'label'   => __( 'Container Settings', 'payment-page' ),
        'group'   => 'section_container_settings_style',
        'section' => 'style',
        'fields'  => array(
          array(
            'label'       => __( 'Max Width', 'payment-page' ),
            'name'        => 'container_max_width',
            'type'        => 'css_style_unit',
            'size_units' => [ 'px', '%', 'em' ],
            'default'    => [ 'unit' => 'px', 'size' => 600 ]
          ),
          array(
            'label'       => __( 'Spacing Top', 'payment-page' ),
            'name'        => 'container_spacing_top',
            'type'        => 'css_style_unit',
            'size_units' => [ 'px', '%', 'em' ],
            'default'    => [ 'unit' => 'px', 'size' => 20 ]
          ),
          array(
            'label'       => __( 'Spacing Bottom', 'payment-page' ),
            'name'        => 'container_spacing_bottom',
            'type'        => 'css_style_unit',
            'size_units' => [ 'px', '%', 'em' ],
            'default'    => [ 'unit' => 'px', 'size' => 20 ]
          )
        )
      ),
      array(
        'label'   => __( 'Global Options', 'payment-page' ),
        'group'   => 'section_global_options_style',
        'section' => 'style',
        'fields'  => array(
          array(
            'label'       => __( 'Section Titles', 'payment-page' ),
            'type'        => 'header'
          ),
          array(
            'label'       => __( 'Spacing', 'payment-page' ),
            'name'        => 'global_options_title_margin',
            'type'        => 'css_style_unit_dimensions',
            'size_units'  => [ 'px', '%', 'em' ],
            'default'     => array(
              'unit'    => 'px',
              'size_1'  => 10,
              'size_2'  => 0,
              'size_3'  => 10,
              'size_4'  => 0,
            )
          )
        )
      )
    );
  }

}
