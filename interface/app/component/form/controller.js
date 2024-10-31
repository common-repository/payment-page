PaymentPage.Component[ 'form' ] = {

  kit_injections : [ "data-payment-page-button", "data-payment-page-notification" ],

  container     : {},
  configuration : {
    layout            : "default",
    fields            : [],
    field_namespace   : 'payment_page_form',
    field_nonce       : '',
    field_nonce_value : ''
  },

  Init : function( container ) {
    this.container = container;

    let objectInstance = this;

    payment_page_component_configuration_parse( this, function() {
      PaymentPage.Template.preload(
      'form',
        'template/field.html',
      function() {
        objectInstance.container.attr( "data-payment-page-layout", objectInstance.configuration.layout );

        if (jQuery.fn.select2) {
          objectInstance.loadTemplate();
        } else {
          PaymentPage.LoadAssets( PaymentPage.settings.libraries.select2, function() {
            objectInstance.loadTemplate();
          });
        }
      });
    } );
  },

  loadTemplate : function() {
    let objectInstance = this;

    PaymentPage.Template.load( this.container, 'form', 'template/default.html', this.configuration, function() {
      objectInstance._bindEvents();
    } );
  },

  _bindEvents : function() {
    let objectInstance = this;

    this.container.find( 'select[multiple]').select2( {} );

    this.container.find( '[data-payment-page-component-form-trigger="remove"]' ).off( "click" ).on( "click", function( event ) {
      jQuery(this).parents( '[data-payment-page-component-form-section="repeater_form_item"]:first' ).slideUp( "slow", function() {
        jQuery(this).remove();
      });
    });

    this.container.find( '[data-payment-page-component-form-section="repeater_form_item"] > [data-payment-page-component-form-section="repeater_form_item_header"] > h4' )
                  .off( "click" ).on( "click", function( event ) {
      let itemContainer = jQuery(this).parents( '[data-payment-page-component-form-section="repeater_form_item"]:first' );

      if( typeof itemContainer.attr( 'data-payment-page-interaction-state' ) !== 'undefined'
          && itemContainer.attr( 'data-payment-page-interaction-state' ) === 'none' ) {
        itemContainer.attr( 'data-payment-page-interaction-state', 'collapsed' );
      } else {
        itemContainer.attr( 'data-payment-page-interaction-state', 'none' );
      }

      PaymentPage.Library.Conditionaldisplay.refresh();
    });

    this.container.find( '[data-payment-page-component-form-trigger="add_repeater"]' ).off( "click" ).on( "click", function( event ) {
      event.preventDefault();

      let uniqid = payment_page_get_random_string(),
          wrapper = jQuery(this).parents('[data-payment-page-component-form-section="repeater_form_wrapper"]:first' ),
          repeaterFormItemContainer = wrapper.find( '[data-payment-page-component-form-section="repeater_form_item_container"]:first' ),
          field_namespace = wrapper.attr( 'data-repeater-field-input-name' ) + '[' + uniqid + ']',
          layout = wrapper.attr( 'data-payment-page-layout' ),
          html = '';

      let field_name = wrapper.attr( 'data-repeater-field-name' );
      let fields = [];

      if(wrapper.parents('[data-payment-page-component-form-section="repeater_form_wrapper"]:first').length === 0) {
        fields = objectInstance._repeaterFieldFields( field_name, objectInstance.configuration.fields );
      } else {
        let _nested_parents = [];

        wrapper.parents('[data-payment-page-component-form-section="repeater_form_wrapper"]').each( function() {
          _nested_parents.push( jQuery(this).attr('data-repeater-field-name') );
        });

        _nested_parents = _nested_parents.reverse();

        let _fields_target = objectInstance.configuration.fields;

        jQuery.each( _nested_parents, function( _i, nested_parent ) {
          _fields_target = objectInstance._repeaterFieldFields( nested_parent, _fields_target );
        });

        fields = objectInstance._repeaterFieldFields( field_name, _fields_target );
      }

      if( layout === 'basic' ) {
        html += '<div data-payment-page-component-form-section="repeater_form_item" data-field-namespace="' + field_namespace + '">';
        html +=   '<div data-payment-page-component-form-section="repeater_form_item_content">';
        html +=     PaymentPage.Template.get( 'form', 'template/default', { fields : fields, field_namespace : field_namespace } );
        html +=   '</div>';
        html +=   '<div data-payment-page-component-form-section="repeater_form_item_footer">';
        html +=     '<button type="button" data-payment-page-button="danger" data-payment-page-component-form-trigger="remove">' + PaymentPage.lang.delete + '</button>';
        html +=   '</div>';
        html += '</div>';
      } else {
        let _has_admin_label = false;

        html += '<div data-payment-page-component-form-section="repeater_form_item" data-field-namespace="' + field_namespace + '">';
        html +=   '<div data-payment-page-component-form-section="repeater_form_item_header">';
        html +=     '<h4>';
        html +=       '<span data-payment-page-component-form-trigger="order">' + PaymentPage.settings.svg_icon_map.element_order + '</span>';
        jQuery(fields).each( function( _key, parsed_field_information ) {
          if( typeof parsed_field_information.admin_label !== 'undefined' && parsed_field_information.admin_label ) {
            html += '<strong data-label-key="{{ parsed_field_information.name }}">' + ( typeof parsed_field_information.default !== 'undefined' ? parsed_field_information.default : '' ) + '</strong>'
            _has_admin_label = true;
          }
        });
        if( !_has_admin_label ) {
          html +=       '<span>Item #' + ( repeaterFormItemContainer.find( '[data-payment-page-component-form-section="repeater_form_item"]' ).length + 1 ) + '</span>';
        }

        html +=         '<i>' + PaymentPage.settings.svg_icon_map.angle_down + '</i>';
        html +=     '</h4>';
        html +=     '<button type="button" data-payment-page-button="danger" data-payment-page-component-form-trigger="remove">' + PaymentPage.lang.delete + '</button>';
        html +=   '</div>';
        html +=   '<div data-payment-page-component-form-section="repeater_form_item_content">';
        html +=     PaymentPage.Template.get( 'form', 'template/default', { fields : fields, field_namespace : field_namespace } );
        html +=   '</div>';
        html += '</div>';
      }

      repeaterFormItemContainer.append( html );

      PaymentPage.Init( repeaterFormItemContainer.find( '[data-field-namespace="' + field_namespace + '"]' ) );

      objectInstance._bindEvents();

      PaymentPage.Library.Conditionaldisplay.refresh();
    });

    this.container.find( ':input[data-payment-page-is-admin-label]' )
                  .off("keyup.pp_repeater_admin_label change.pp_repeater_admin_label")
                  .on("keyup.pp_repeater_admin_label change.pp_repeater_admin_label", function() {
      let _container = jQuery(this).parents( '[data-payment-page-component-form-section="repeater_form_item"]:first' )
                                   .find( '[data-payment-page-component-form-section="repeater_form_item_header"]:first' );

      let _name = jQuery(this).attr( 'name' ).slice( jQuery(this).attr( 'name' ).lastIndexOf( '][' ) + 2 );
      _name = _name.replace( ']', '' );

      _container.find( '[data-label-key="' + _name + '"]:first' ).text( jQuery(this).val() );
    })

    this.container.find( '[data-payment-page-component-form-section="field_container_message_builder"]' ).not( '.loaded-message-builder').each( function() {
      jQuery(this).addClass( 'loaded-message-builder' );

      let fieldListContainer = jQuery(this).find( '> [data-payment-page-scope="field_list"]' ),
          fieldOrderContainer = jQuery(this).find( '> [data-payment-page-scope="field_order"]' ),
          textareaObject = jQuery(this).find( '> textarea' ),
          _currentFieldOrder = textareaObject.val().split(','),
          updateOrder = function() {
            let _newOrder = [];

            fieldOrderContainer.find( '> li' ).each( function() {
              _newOrder.push( jQuery(this).attr( 'data-field-key' ) );
            });

            textareaObject.val( _newOrder.join(',') );
          };

      fieldListContainer.find(':input').off( "change keyup" ).on( "change keyup", function() {
        let _isAvailable = jQuery(this).attr( "type" ) === 'checkbox' ? jQuery(this).is( ":checked" ) : jQuery(this).val() !== '',
            _field_key = jQuery(this).parents('[data-payment-page-field-key]:first').attr('data-payment-page-field-key');

        if( _isAvailable ) {
          if( fieldOrderContainer.find('[data-field-key="' + _field_key + '"]').length === 0 ) {
            fieldOrderContainer.append(
              '<li data-field-key="' + _field_key + '">' +
              fieldListContainer.find(':input[name$="[' + _field_key + ']"]').parents('[data-payment-page-scope]:first').find( ' > label' ).text() +
              '</li>'
            );

            updateOrder();
          }
        } else {
          if( fieldOrderContainer.find('[data-field-key="' + _field_key + '"]').length !== 0 ) {
            fieldOrderContainer.find('[data-field-key="' + _field_key + '"]').remove();

            updateOrder();
          }
        }
      });

      jQuery.each(_currentFieldOrder, function(_index, _currentFieldKey) {
        if( _currentFieldKey !== '' ) {
          fieldOrderContainer.append(
            '<li data-field-key="' + _currentFieldKey + '">' +
              fieldListContainer.find(':input[name$="[' + _currentFieldKey + ']"]').parents('[data-payment-page-scope]:first').find( ' > label' ).text() +
            '</li>'
          );
        }
      });

      PaymentPage.Library.Sortable.Init( fieldOrderContainer, {
        onUpdate : updateOrder
        // handle : '[data-payment-page-component-form-trigger="order"]'
      } );
    });

    objectInstance.container.find( '[data-payment-page-component-form-section="field_wrapper_repeater"] [data-payment-page-component-form-section="repeater_form_item_container"]' ).each( function() {
      if(jQuery(this).find('[data-payment-page-component-form-trigger="order"]').length === 0)
        return;

      PaymentPage.Library.Sortable.Init( jQuery(this), {
        handle : '[data-payment-page-component-form-trigger="order"]'
      } );
    });

    this.container.find('[data-payment-page-component-form-section="fields_map_form_wrapper"]').each(function() {
      if(typeof jQuery(this).attr('data-payment-page-interaction-state') !== 'undefined'
          && jQuery(this).attr('data-payment-page-interaction-state') === 'loaded') {
        return;
      }

      let wrapperContainer = jQuery(this),
          textareaObject = wrapperContainer.find('> textarea'),
          fieldListContainer = wrapperContainer.find('> [data-payment-page-component-form-section="fields_map_form_container"]'),
          fieldAddTrigger = wrapperContainer.find('[data-payment-page-component-form-trigger="fields_map_form_add"]');

      let fields = Object.values( JSON.parse( textareaObject.val() ) ),
          ordered_fields = [];

      jQuery.each( fields, function( k, v ) {
        if( typeof v.order !== 'undefined' )
          v.order = parseInt( v.order );
        else
          v.order = 0;

        ordered_fields.push( v );
      });

      ordered_fields = _.sortBy( ordered_fields, 'order' );

      jQuery.each( ordered_fields, function( _k, field_data ) {
        objectInstance._fieldsMapAttachField( field_data, fieldListContainer, textareaObject );
      });

      PaymentPage.Library.Sortable.Init( fieldListContainer, {
        handle : '[data-payment-page-component-form-trigger="fields_map_form_item_order"]',
        onUpdate : function() {
          fieldListContainer.find( '> [data-payment-page-component-form-section="field_container"]' ).each( function() {
            jQuery(this).find( '[data-setting="order"]' ).val( jQuery(this).index() );
          });

          objectInstance._fieldsMapSyncToTextarea(fieldListContainer, textareaObject);
        }
      } );

      fieldAddTrigger.off( "click" ).on( "click", function( event ) {
        event.preventDefault();

        objectInstance._fieldsMapAttachField( {
          name : "custom_field_" + Date.now(),
          type : "custom",
          size : 6,
          size_mobile : 0,
          order : fieldListContainer.find( '> [data-payment-page-component-form-section="field_container"]' ).length
        }, fieldListContainer, textareaObject );

        objectInstance._fieldsMapSyncToTextarea(fieldListContainer, textareaObject);
      });

      wrapperContainer.attr('data-payment-page-interaction-state', 'loaded');
    });

  },

  _fieldsMapAttachField : function( row_data, fieldListContainer, textareaObject ) {
    let objectInstance = this,
        html = '';

    html += '<div data-payment-page-component-form-section="field_container">';
    html +=   '<div data-payment-page-component-form-section="field_container_draggable_container">';
    html +=     '<span data-payment-page-component-form-trigger="fields_map_form_item_order">';
    html +=       PaymentPage.settings.svg_icon_map['element_order'];
    html +=     '</span>';
    html +=   '</div>';
    html +=   '<div data-payment-page-component-form-section="field_container_information_container">';
    html +=     '<input data-setting="key" type="hidden" value="' + ( typeof row_data.name !== 'undefined' ? row_data.name : '' ) + '">';
    html +=     '<input data-setting="type" type="hidden" value="' + ( typeof row_data.type !== 'undefined' ? row_data.type : '' ) + '">';
    html +=     '<input data-setting="order" type="hidden" value="' + ( typeof row_data.order !== 'undefined' ? row_data.order : '0' ) + '">';

    html +=     '<div data-setting-container="label">';
    html +=       '<label>';
    html +=         '<span>Label</span>';
    html +=         '<input data-setting="label" class="field" type="text" value="' + ( typeof row_data.label !== 'undefined' ? row_data.label : '' ) + '">';
    html +=       '</label>';
    html +=     '</div>';

    if( jQuery.inArray( row_data.type, [ 'payment_method_card', 'payment_method_iban'] ) === -1 ) {
      html +=     '<div data-setting-container="placeholder">';
      html +=       '<label>';
      html +=         '<span>Placeholder</span>';
      html +=         '<input data-setting="placeholder" class="field" type="text" value="' + ( typeof row_data.placeholder !== 'undefined' ? row_data.placeholder : '' ) + '">';
      html +=       '</label>';
      html +=     '</div>';
    }

    if( row_data.type === "custom" ) {
      html +=     '<div data-setting-container="is_required">';
      html +=       '<label>';
      html +=         '<span>Is Required</span>';
      html +=         '<input data-setting="is_required" class="field" type="checkbox" value="1" ' + ( typeof row_data.is_required !== 'undefined' && parseInt( row_data.is_required ) ? 'checked="checked"' : '' ) + '>';
      html +=       '</label>';
      html +=     '</div>';
    }

    if( row_data.name === "card_zip_code" ) {
      html +=     '<div data-setting-container="is_hidden">';
      html +=       '<label>';
      html +=         '<span>Hide on checkout</span>';
      html +=         '<input data-setting="is_hidden" class="field" type="checkbox" value="1" ' + ( typeof row_data.is_hidden !== 'undefined' && parseInt( row_data.is_hidden ) ? 'checked="checked"' : '' ) + '>';
      html +=       '</label>';
      html +=     '</div>';
    }

    html +=     '<div data-setting-container="size">';
    html +=       '<label>';
    html +=         '<span>Size</span>';
    html +=         '<select data-setting="size">';

    jQuery.each( [ 1, 2, 3, 4, 5, 6 ], function( _k, _v ) {
      html +=      '<option value="' + _v + '" ' + ( typeof row_data.size !== 'undefined' && parseInt( row_data.size ) === _v ? 'selected="selected"' : '' ) + '>' + ( _v + '/6' ) + '</option>';
    });

    html +=         '</select>';
    html +=       '</label>';
    html +=     '</div>';

    html +=     '<div data-setting-container="size_mobile">';
    html +=       '<label>';
    html +=         '<span>Size Mobile</span>';
    html +=         '<select data-setting="size_mobile">';

    jQuery.each( [ 0, 1, 2, 3, 4, 5, 6 ], function( _k, _v ) {
      html +=      '<option value="' + _v + '" ' + ( typeof row_data.size_mobile !== 'undefined' && parseInt( row_data.size_mobile ) === _v ? 'selected="selected"' : '' ) + '>';
      html +=         ( _v === 0 ? 'Inherit' : ( _v + '/6' ) );
      html +=      '</option>';
    });

    html +=         '</select>';
    html +=       '</label>';
    html +=     '</div>';

    if( row_data.type === "custom" ) {
      html +=     '<p><span class="remove-field" data-payment-page-button="danger">Remove</span></p>';
    }

    html +=   '</div>';
    html += '</div>';

    fieldListContainer.append( html );

    let fieldRowContainer = fieldListContainer.find( '> [data-payment-page-component-form-section="field_container"]:last' );

    fieldRowContainer.find( '[data-setting]' ).on( "keyup change", function( event ) {
      objectInstance._fieldsMapSyncToTextarea(fieldListContainer, textareaObject);
    });

    fieldRowContainer.find( '.remove-field' ).off( "click" ).on( "click", function() {
      let _fieldRow = jQuery(this).parents( '[data-payment-page-component-form-section="field_container"]:first' );

      _fieldRow.remove();

      objectInstance._fieldsMapSyncToTextarea(fieldListContainer, textareaObject);
    });
  },

  _fieldsMapSyncToTextarea : function(fieldListContainer, textareaObject) {
    let value = {};

    fieldListContainer.find( '> [data-payment-page-component-form-section="field_container"]' ).each( function() {
      let key = jQuery(this).find( '[data-setting="key"]' ).val(),
          type = jQuery(this).find( '[data-setting="type"]' ).val(),
          order = jQuery(this).find( '[data-setting="order"]' ).val(),
          label = jQuery(this).find( '[data-setting="label"]' ).val(),
          size = jQuery(this).find( '[data-setting="size"]' ).val(),
          size_mobile = jQuery(this).find( '[data-setting="size_mobile"]' ).val(),
          isHiddenInput = jQuery(this).find( '[data-setting="is_hidden"]' ),
          isRequiredInput = jQuery(this).find( '[data-setting="is_required"]' ),
          placeholderInput = jQuery(this).find( '[data-setting="placeholder"]' );

      value[ key ] = {
        label       : label,
        name        : key,
        placeholder : ( placeholderInput.length > 0 ? placeholderInput.val() : '' ),
        is_required : ( isRequiredInput.length > 0 ? ( isRequiredInput.is( ':checked' ) ? 1 : 0 ) : 1 ),
        type        : type,
        size        : size,
        size_mobile : size_mobile,
        order       : order
      };

      if( isHiddenInput.length > 0 )
        value[ key ][ 'is_hidden' ] = ( isHiddenInput.is( ':checked' ) ? 1 : 0 );

    });

    textareaObject.val( JSON.stringify(value) ).trigger( "change" );
  },

  _repeaterFieldFields( name, field_list ) {
    let response = [];

    jQuery.each( field_list, function( index, field ) {
      if( typeof field.type === 'undefined' )
        return;

      if( field.type !== 'repeater' )
        return;

      if( field.name !== name )
        return;

      if( typeof field.fields === 'undefined' )
        return;

      response = field.fields;
    });

    return response;
  }

};