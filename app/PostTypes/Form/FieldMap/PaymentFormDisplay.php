<?php

namespace PaymentPage\PostTypes\Form\FieldMap;

class PaymentFormDisplay extends Skeleton {

  public static function get_field_groups() {
    return array(
      array(
        'label'   => __( 'Payment Form Display', 'payment-page' ),
        'group'   => 'payment_form_display',
        'section' => 'content',
        'fields'  => array(
          array(
            'label'       => __( 'Select One:', 'payment-page' ),
            'name'        => 'display_form',
            'type'        => 'select',
            'default' => 'standard',
            'options' => array(
              'standard' => __( 'Inline/embed payment form', 'payment-page' ),
              'popup'    => __( 'Payment buttons (popup form)', 'payment-page' ),
            ),
          )
        )
      )
    );
	}

}
