<?php

namespace PaymentPage\PostTypes\Form\FieldMap;

use PaymentPage\ThirdPartyIntegration\Freemius as TPI_Freemius;

class Upgrade extends Skeleton {

	public static function is_enabled() :bool {
		return ! TPI_Freemius::has_personal_plan() && ! TPI_Freemius::has_pro_plan() && ! TPI_Freemius::has_agency_plan();
	}

  public static function get_field_groups() {
    $text  = '<p style="margin:0;font-size:18px;">' . __( 'Upgrade to PRO to remove the 2% transaction fee.', 'payment-page' ) . '</p>';
    $text .= '<p style="margin: 10px 0 0 0;"><a href="' . admin_url( 'admin.php?page=payment-page-pricing' ) . '" data-payment-page-button="primary" target="_blank">' . __( 'Upgrade Now', 'payment-page' ) . '</a></p>';

    return array(
      array(
        'label'   => __( 'Upgrade Options', 'payment-page' ),
        'group'   => 'pp_upgrade_section',
        'section' => 'content',
        'fields'  => array(
          array(
            'label'       => null,
            'type'        => 'raw_html',
            'html'        => '<p>' . $text . '</p>'
          )
        )
      )
    );
  }

}
