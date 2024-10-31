PaymentPage.Library.Wpmedia = {

  /**
   * @param target
   * @param args
   * @constructor
   */
  Init : function( target, args ) {
    let objectInstance = this;

    if ( typeof wp.media !== "undefined" ) {
      if( typeof args === 'undefined' ) {
        args = {};

        if( typeof target.attr( 'data-payment-page-library-args' ) !== 'undefined' ) {
          try {
            args = JSON.parse( target.attr( 'data-payment-page-library-args' ) );
          } catch (e) {
            args = {};
          }
        }
      }

      this._init( target, args );
    } else {
      console.log( "... this is rather funny to load, wp media is not present." );
    }
  },

  _init : function( target, args ) {
    if( typeof target.attr( "data-payment-page-library-loaded" ) !== 'undefined' )
      return;

    args = payment_page_parse_args( args, {
      allow_delete : 1,
    } );

    target.attr( "data-payment-page-library-loaded", 0 );

    let _input = target.find( '> input' ),
        src = _input.val();

    if( payment_page_is_number( src )
        && typeof _input.attr( 'data-attachment-src' ) !== 'undefined'
        && _input.attr( 'data-attachment-src' ) !== '' )
      src = _input.attr( 'data-attachment-src' );

    if( payment_page_is_number( src ) && parseInt( src ) !== 0 ) {
      target.append( payment_page_element_loader( 'mini' ) );

      PaymentPage.API.fetch( 'wp/v2/media/' + src, {}, function( response ) {
        target.find( '.payment-page-application-loader-wrapper' ).remove();
        PaymentPage.Library.Wpmedia._initDataReady( target, args, ( typeof response.source_url !== 'undefined' ? response.source_url : '' ) );
      });
    } else {
      this._initDataReady( target, args, src );
    }
  },

  _initDataReady : function( target, args, src ) {
    let upload_type = args.type,
        html = '';

    html += '<img data-payment-page-library-wpmedia-trigger="' + upload_type + '" src="' + src + '"/>';
    html += '<div data-payment-page-component-form-section="actions">';

    html += '<span data-payment-page-button="primary" data-payment-page-button-size="tiny" data-payment-page-library-wpmedia-trigger="set_' + upload_type + '">' +
              'Set' +
            '</span>' +
            '<span data-payment-page-button="gray" data-payment-page-button-size="tiny" data-payment-page-library-wpmedia-trigger="edit_' + upload_type + '">' +
              'Edit' +
            '</span>';


    if( src.length > 0 && '' + src !== '0' ) {
      target.attr( "data-payment-page-interaction-state", 'uploaded' );
    }

    if( args.allow_delete )
      html += '<span data-payment-page-button="danger-plain" data-payment-page-button-size="tiny" data-payment-page-library-wpmedia-trigger="remove_' + upload_type + '">' +
                'Delete' +
              '</span>';

    html += '</div>';

    target.append( html );

    target.find(
      '[data-payment-page-library-wpmedia-trigger="set_' + upload_type + '"], ' +
      '[data-payment-page-library-wpmedia-trigger="edit_' + upload_type + '"], ' +
      '[data-payment-page-library-wpmedia-trigger="' + upload_type + '"]'
    ).off( "click" ).on( "click", function() {
      let attachmentMediaInstance = wp.media({
        title: "Upload File",
        multiple: false,
      }).open().on("select", function (e) {
        let uploaded_file = attachmentMediaInstance.state().get("selection").first(),
            data = uploaded_file.toJSON();

        target.find( ":input" ).each( function() {
          jQuery(this).attr( "value", data.id ).val(data.id).trigger( "change" );
        });
        target.find( "img" ).attr("src", data.url);
        target.attr( "data-payment-page-interaction-state", 'uploaded' );
      });
    });

    target.find( ' > [data-payment-page-component-form-section="actions"] > [data-payment-page-library-wpmedia-trigger="remove_' + upload_type + '"]' ).off( "click" ).on( "click", function() {
      if( args.is_multiple ) {
        target.remove();
        return;
      }

      target.find( "img" ).attr( "src", "" );
      target.find( "input" ).val( "" ).trigger( "change" );
      target.attr( "data-payment-page-interaction-state", 'pending' );
    });

    target.attr( "data-payment-page-library-loaded", 1 );
  }

};