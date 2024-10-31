jQuery(document).ready(function() {
  let topLevelPaymentPage = jQuery("#toplevel_page_payment-page");

  if(topLevelPaymentPage.hasClass('wp-menu-open')) {
    let submenuItems = topLevelPaymentPage.find('> ul > li'),
        displayedMenuItem = submenuItems.find( 'a[href^="edit.php?post_type=pp_payment_form"]').parents('li:first');

    submenuItems.not(displayedMenuItem).removeClass( 'current' ).find('a.current').removeClass('current');

    displayedMenuItem.addClass('current').find('a').addClass('current');
  }

});