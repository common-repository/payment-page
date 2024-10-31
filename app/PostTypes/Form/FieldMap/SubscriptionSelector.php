<?php

namespace PaymentPage\PostTypes\Form\FieldMap;

class SubscriptionSelector extends Skeleton {

  public static function get_field_groups() {
    $fields = array();

    $fields[] = array(
      'label'       => __( 'Subscription Filter', 'payment-page' ),
      'name'        => 'subscription_selector',
      'type'        => 'toggle',
      'default'     => 'no',
      'toggle_label_on'  => __( 'On', 'payment-page' ),
      'toggle_label_off' => __( 'Off', 'payment-page' ),
      'toggle_value_on'  => 'yes',
      'toggle_value_off' => 'no'
    );

    return array(
      array(
        'label'   => __( 'Subscription Filter', 'payment-page' ),
        'group'   => 'subscription_selector_section',
        'section' => 'content',
        'fields'  => $fields
      )
    );
  }
}
