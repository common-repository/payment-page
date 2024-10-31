<?php

namespace PaymentPage\PostTypes\Form;

use WP_Query;

class FieldMap {

  protected static $_fields = array();

  public static function get_field_groups() {
    if(!empty(self::$_fields))
      return self::$_fields;

    self::$_fields = array();

    if( FieldMap\PaymentFormDisplay::is_enabled() )
      self::$_fields = array_merge( self::$_fields, FieldMap\PaymentFormDisplay::get_field_groups() );

    if( FieldMap\GlobalOptions::is_enabled() )
      self::$_fields = array_merge( self::$_fields, FieldMap\GlobalOptions::get_field_groups() );

    if( FieldMap\PricingPlans::is_enabled() )
      self::$_fields = array_merge( self::$_fields, FieldMap\PricingPlans::get_field_groups() );

    if( FieldMap\PaymentGateways::is_enabled() )
      self::$_fields = array_merge( self::$_fields, FieldMap\PaymentGateways::get_field_groups() );

    if( FieldMap\Form::is_enabled() )
      self::$_fields = array_merge( self::$_fields, FieldMap\Form::get_field_groups() );

    if( FieldMap\CurrencySelector::is_enabled() )
      self::$_fields = array_merge( self::$_fields, FieldMap\CurrencySelector::get_field_groups() );

    if( FieldMap\SubscriptionSelector::is_enabled() )
      self::$_fields = array_merge( self::$_fields, FieldMap\SubscriptionSelector::get_field_groups() );

    if( FieldMap\SubmitButtonControl::is_enabled() )
      self::$_fields = array_merge( self::$_fields, FieldMap\SubmitButtonControl::get_field_groups() );

    if( FieldMap\ActionsForm::is_enabled() )
      self::$_fields = array_merge( self::$_fields, FieldMap\ActionsForm::get_field_groups() );

    if( FieldMap\Upgrade::is_enabled() )
      self::$_fields = array_merge( self::$_fields, FieldMap\Upgrade::get_field_groups() );

    self::$_fields = apply_filters( "payment_page_administration_form_field_map", self::$_fields );

    return self::$_fields;
  }

  public static function get_field_groups_with_values($form_id) {
    $settings = get_post_meta( $form_id, 'payment_page_settings', true );
    $field_groups = self::get_field_groups();

    foreach($field_groups as $field_group_key => $field_group) {
      foreach($field_group['fields'] as $field_key => $field) {
        if(!isset($field['name']))
          continue;

        if(!isset($settings[ $field['name'] ]))
          continue;

        $field_groups[$field_group_key]['fields'][$field_key]['value'] = $settings[ $field['name'] ];
      }
    }

    return $field_groups;
  }

  public static function get_form_settings_mapped_as_key_value( $form_id ) {
    $field_groups = self::get_field_groups_with_values( $form_id );
    $response = [];

    foreach($field_groups as $field_group) {
      foreach($field_group['fields'] as $field) {
        if(!isset($field['name']))
          continue;

        $value = ( $field['value'] ?? null );

        if($value === null && isset($field['default'])) {
          $value = $field['default'];
        }

        $response[ $field['name'] ] = $value;
      }
    }

    return $response;
  }

}