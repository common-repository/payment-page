PaymentPage.Library.Conditionaldisplay = {

  Init : function( targetElement ) {
    targetElement.off( "change.payment_page_conditional_display" ).on( "change.payment_page_conditional_display", function() {
      PaymentPage.Library.Conditionaldisplay._processSingleElement( jQuery( this ) );
    }).each( function() {
      PaymentPage.Library.Conditionaldisplay._processSingleElement( jQuery( this ) );
    });
  },

  refresh : function() {
    jQuery( '[data-payment-page-library="conditionaldisplay"]' ).each( function() {
      PaymentPage.Library.Conditionaldisplay._processSingleElement( jQuery( this ) );
    });
  },

  _processSingleElement : function( target ) {
    let configuration = payment_page_parse_args( target.attr( 'data-payment-page-library-args' ), {
        constrain_to_parent_target : "",
        ignore_hidden_parent : 0,
        hide_input_name  : 0,
        special_empty_for_false : 0
      } ),
      conditional_display_value = false;

    if( target.is( ":hidden" ) && !configuration.ignore_hidden_parent ) {
      conditional_display_value = false;
    } else if( target.is( ":checkbox" ) ) {
      let _form_target = target.parents( 'form:first,[data-payment-page-component="data-schema-management"]:first' );

      if( _form_target.length !== 0 ) {
        let _form_hidden_target = _form_target.find( '[name=' + "'" + target.attr( 'name' ) + "'" + '][type="hidden"]' );

        if( _form_hidden_target.length > 0 ) {
          conditional_display_value = target.is( ":checked" ) ? target.val() : _form_hidden_target.val();
        } else {
          conditional_display_value = target.is( ":checked" );
        }
      } else {
        conditional_display_value = target.is( ":checked" );
      }
    } else {
      conditional_display_value = target.val();
    }

    jQuery.each( configuration.target.split( " " ), function( key, current_display_rule ) {
      if( current_display_rule.indexOf( "::" ) !== -1 ) {
        let current_display_rule_token_1 = current_display_rule.substr( 0, current_display_rule.indexOf( "::" ) ),
          current_display_rule_token_2 = current_display_rule.replace( current_display_rule_token_1 + '::', '' );

        if( current_display_rule_token_1 === conditional_display_value || conditional_display_value === true ) {
          PaymentPage.Library.Conditionaldisplay.__show( jQuery( current_display_rule_token_2 ), configuration );
        } else {
          PaymentPage.Library.Conditionaldisplay.__hide( jQuery( current_display_rule_token_2 ), configuration );
        }
      } else {
        let _target = ( configuration.constrain_to_parent_target !== '' ? target.parents( configuration.constrain_to_parent_target ).find( '[' + current_display_rule + ']' ) : jQuery( '[' + current_display_rule + ']' ) );

        _target.each( function() {
          let hide = true;

          if(conditional_display_value !== false) {
            let _check = ( typeof jQuery(this).attr( current_display_rule ) !== "undefined" ? jQuery(this).attr( current_display_rule ).split( " " ) : [] );

            if(configuration.special_empty_for_false) {
              conditional_display_value = ( conditional_display_value === '' ? '0' : '1' );
            }

            if( _check.length !== 0 ) {
              if( conditional_display_value instanceof Array ) {
                hide = payment_page_array_intersect( conditional_display_value, _check ).length === 0;
              } else {
                hide = !payment_page_in_array( conditional_display_value, _check );
              }
            }
          }

          if( hide ) {
            PaymentPage.Library.Conditionaldisplay.__hide( jQuery(this), configuration );

            if( typeof jQuery( this ).attr( 'data-payment-page-conditionaldisplay-hidden-callback' ) !== "undefined" )
              jQuery( jQuery( this ).attr( 'data-payment-page-conditionaldisplay-hidden-callback' ) ).trigger( "click" );

            if( jQuery(this).is( 'option' ) && conditional_display_value !== false ) {
              let parent = jQuery(this).parent();

              if( parent.is( "select" ) && ( parent.val() === null || parent.val() === jQuery(this).val() ) ) {
                parent.find( " > option" ).each( function() {
                  if( typeof jQuery(this).attr( 'selected' ) !== 'undefined' )
                    jQuery(this).removeAttr( 'selected' );

                  if( jQuery.inArray( conditional_display_value, ( typeof jQuery(this).attr( current_display_rule ) !== "undefined" ? jQuery(this).attr( current_display_rule ).split( " " ) : [] ) ) !== -1 ) {
                    parent.val( jQuery(this).val() );
                    jQuery(this).attr( 'selected', 'selected' );

                    return false;
                  }
                });
              }
            }
          } else {
            PaymentPage.Library.Conditionaldisplay.__show( jQuery(this), configuration );

            if( typeof jQuery( this ).attr( 'data-payment-page-conditionaldisplay-visible-callback' ) !== "undefined" )
              jQuery( jQuery( this ).attr( 'data-payment-page-conditionaldisplay-visible-callback' ) ).trigger( "click" );
          }

          jQuery(this).find( '[data-payment-page-library="conditionaldisplay"]').trigger( "change" );

          if( jQuery(this).is( ':input' ) )
            jQuery(this).trigger( "change" );
          else if( jQuery(this).is( "option" ) )
            jQuery(this).parent().trigger( "change" );
        });
      }
    });
  },

  __hide( target, configuration ) {
    target.hide();

    if( configuration.hide_input_name ) {
      target.find('[name]').each( function() {
        jQuery(this).attr( 'data-payment-page-hidden-name', jQuery(this).attr( 'name' ) );
        jQuery(this).removeAttr( 'name' );
      });
    }
  },

  __show( target, configuration ) {
    target.show();

    if( configuration.hide_input_name ) {
      target.find('[data-payment-page-hidden-name]').each( function() {
        jQuery(this).attr( 'name', jQuery(this).attr( 'data-payment-page-hidden-name' ) );
        jQuery(this).removeAttr( 'data-payment-page-hidden-name' );
      });
    }
  }

}