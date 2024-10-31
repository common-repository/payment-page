<div data-payment-page-admin-section="header">
  <div data-payment-page-admin-section="logo-container">
    <img src="<?php echo esc_url( plugin_dir_url( PAYMENT_PAGE_BASE_FILE_PATH ) . 'interface/app/component/admin-dashboard/img/logo.png' ); ?>"/>
  </div>

  <div data-payment-page-admin-section="navigation">
    <div data-payment-page-admin-section="menu_container">
      <a href="<?php echo admin_url( 'edit.php?post_type=' . PAYMENT_PAGE_POST_TYPE_PAYMENT_FORM ); ?>"
         data-payment-page-interaction-state="active"><?php echo __('Payment Forms', 'payment-page'); ?></a>

      <a href="<?php echo admin_url( 'admin.php?page=payment-page' ); ?>#payment-gateways"
         data-payment-page-admin-trigger="hashtag_navigation"
         data-payment-page-interaction-state="inactive"
      ><?php echo __('Payment Gateways', 'payment-page'); ?></a>
    </div>
  </div>
</div>