<?php

function _payment_page_form_setting_size_to_css( $size, $valid_after = ''  ) {
  if( isset( $size[ 'top' ] ) && isset( $size[ 'right' ] ) && isset( $size[ 'bottom' ] ) && isset( $size[ 'left' ] ) )
    return $size[ 'top' ] . $size[ 'unit' ] . ' ' .
      $size[ 'right' ] . $size[ 'unit' ] . ' ' .
      $size[ 'bottom' ] . $size[ 'unit' ] . ' ' .
      $size[ 'left' ] . $size[ 'unit' ];

  if( isset( $size[ 'size_1' ] ) && isset( $size[ 'size_2' ] ) && isset( $size[ 'size_3' ] ) && isset( $size[ 'size_4' ] ) )
    return $size[ 'size_1' ] . $size[ 'unit' ] . ' ' .
            $size[ 'size_2' ] . $size[ 'unit' ] . ' ' .
            $size[ 'size_3' ] . $size[ 'unit' ] . ' ' .
            $size[ 'size_4' ] . $size[ 'unit' ];

  if( empty( $size[ 'size' ] ) )
    return 0;

  return $size[ 'size' ] . $size[ 'unit' ] . $valid_after;
}

function payment_page_form_setting_to_css_variable_border( $size, $color ) {
  return _payment_page_form_setting_size_to_css( $size, ' solid ' . $color );
}

function payment_page_form_setting_to_css_variable_border_radius( $size ) {
  return _payment_page_form_setting_size_to_css( $size );
}

function payment_page_form_setting_to_css_variable_box_shadow( $horizontal, $vertical, $blur, $spread, $color, $position ) {
  return _payment_page_form_setting_size_to_css( $horizontal ) .
    ' ' . _payment_page_form_setting_size_to_css( $vertical ) .
    ' ' . _payment_page_form_setting_size_to_css( $blur ) .
    ' ' . _payment_page_form_setting_size_to_css( $spread ) .
    ' ' . $color  .
    ' ' . $position;
}

function payment_page_form_setting_to_css_variable_font_size( $size ) {
  return _payment_page_form_setting_size_to_css( $size );
}

function payment_page_form_setting_to_css_variable_padding( $size ) {
  return _payment_page_form_setting_size_to_css( $size );
}

function payment_page_form_setting_to_css_variable_margin( $size ) {
  return _payment_page_form_setting_size_to_css( $size );
}

function payment_page_form_setting_to_css_variable_color( $settings, $index ) {
  if( isset( $settings[ '__globals__' ] ) && isset( $settings[ '__globals__' ][ $index ] ) ) {
    if( strpos( $settings[ '__globals__' ][ $index ],'?id=' ) !== false ) {
      $identifier = substr( $settings[ '__globals__' ][ $index ], strpos( $settings[ '__globals__' ][ $index ],'?id=' ) + 4 );

      return 'var( --e-global-color-' . $identifier . ' )';
    }
  }

  if( isset( $settings[ $index ] ) )
    return $settings[ $index ];

  return 'inherit';
}