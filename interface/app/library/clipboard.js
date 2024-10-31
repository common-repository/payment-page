PaymentPage.Library.Clipboard = {

  Init : function( targetElement ) {
    if( typeof window.ClipboardJS === "undefined" ) {
      PaymentPage.LoadAssets( PaymentPage.settings.libraries.clipboard, function() {
        PaymentPage.Library.Clipboard._init( targetElement );
      });
    } else {
      PaymentPage.Library.Clipboard._init( targetElement );
    }
  },

  _init : function( targetElement ) {
    let configuration = payment_page_parse_args( targetElement.attr( 'data-payment-page-library-args' ), {
      trigger    : null,
      target     : null
    } ),
      id_base = 'payment-page-clipboard-' + payment_page_get_random_string(),
      id_copy = id_base + '-copy',
      clipboardJSInstance = null;

    if( configuration.target === null && configuration.trigger === null ) {
      if( targetElement.find( '[type="text"][readonly="readonly"]').length > 0 )
        return;

      let text = targetElement.text();
      let html = '<input type="text" value="' + _.escape(text) + '" readonly="readonly"/>' +
                        '<span id="' + id_copy + '" data-payment-page-button="primary" data-clipboard-text="' + _.escape(text) + '">' +
                            PaymentPage.settings.svg_icon_map[ 'copy' ] +
                        '</span>';

      targetElement.html( html );

      clipboardJSInstance = new ClipboardJS( '#' + id_copy, {
        text : function() {
          return text;
        }
      } );
    } else {
      targetElement.find( configuration.trigger ).attr( "id", id_copy );

      clipboardJSInstance = new ClipboardJS( '#' + id_copy, {
        text : function() {
          return targetElement.find( configuration.target ).val();
        }
      } );
    }

    clipboardJSInstance.on('success', function(e) {
      let _trigger = jQuery(e.trigger);

      if( _trigger.find( '> span' ).length === 0 ) {
        _trigger.find( '> svg, > strong' ).fadeOut( "fast" );
        _trigger.append( '<span>' + PaymentPage.settings.svg_icon_map[ 'circle_checkmark' ] + '</span>' );

        setTimeout( function() {
          _trigger.find( '> span' ).fadeOut( "fast", function() {
            jQuery(this).remove();
          } );
          _trigger.find( '> svg, > strong' ).fadeIn( "slow" );
        }, 2000 );
      }
    });
  }

};