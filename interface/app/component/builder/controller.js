/**
 * This is an advanced form builder, which allows you to create mega administration panels.
 */
PaymentPage.Component[ 'builder' ] = {

  container     : {},
  configuration : {
    sections        : [],
    field_groups    : [],
    field_namespace : 'payment_page_form',
    live_preview_endpoint : ''
  },

  headerContainer : {},
  sectionsWrapper : {},
  previewContainer : {},
  previewFormDataString : '',
  previewXHR : false,

  Init : function( container ) {
    this.container = container;

    let objectInstance = this;

    payment_page_component_configuration_parse( this, function() {
      payment_page_registerjQueryObjectSerialize();

      objectInstance.loadTemplate();
    } );
  },

  loadTemplate : function() {
    let objectInstance = this,
        content = '',
        active_section = this.configuration.sections[ 0 ].alias;

    content += '<div data-payment-page-component-builder-section="settings">';
    content += '<div data-payment-page-component-builder-section="header">';
    jQuery.each(this.configuration.sections, function( index, section ) {
      content += '<div data-payment-page-component-builder-trigger="show_section_' + section.alias + '"' +
                     ' data-payment-page-interaction-state="' + ( index === 0 ? 'active' : 'inactive' ) + '">';
      content +=    '<span>' + section.title + '</span>';
      content += '</div>';
    });
    content += '</div>';

    content += '<div data-payment-page-component-builder-section="sections_wrapper">';

    jQuery.each(this.configuration.field_groups, function( index, field_group ) {
      let component_args = {
        field_namespace : objectInstance.configuration.field_namespace,
        fields          : field_group.fields,
        layout          : "default"
      };

      content += '<div data-payment-page-component-builder-section="section_' + field_group.section + '"' +
                     ' data-payment-page-interaction-state="closed"' +
                     ' data-payment-page-display-state="' + ( active_section === field_group.section ? 'visible' : 'hidden' ) + '"';

      if( typeof field_group.attributes !== 'undefined' ) {
        jQuery.each( field_group.attributes, function( _k, _v ) {
          content += ' ' + _k + '="' + _.escape(_v) + '"';
        });
      }

      content += '>';
      content +=    '<p>';
      content +=        PaymentPage.settings.svg_icon_map[ 'angle_down' ];
      content +=        '<span>' + field_group.label + '</span>';
      content +=    '</p>';
      content +=    '<div data-payment-page-component="form" data-payment-page-component-args="' + _.escape( JSON.stringify( component_args ) )  + '"></div>';
      content += '</div>';
    });

    content += '</div>';
    content += '</div>';
    content += '<div data-payment-page-component-builder-section="preview">' + payment_page_element_loader() + '</div>';

    this.container.html( content );

    PaymentPage.Init( this.container );

    this.headerContainer = this.container.find( ' > [data-payment-page-component-builder-section="settings"] > [data-payment-page-component-builder-section="header"]' );
    this.sectionsWrapper = this.container.find( ' > [data-payment-page-component-builder-section="settings"] > [data-payment-page-component-builder-section="sections_wrapper"]' );
    this.previewContainer = this.container.find( ' > [data-payment-page-component-builder-section="preview"]' );

    this._bindEvents();
  },

  _bindEvents : function() {
    let objectInstance = this;

    this.headerContainer.find( ' > [data-payment-page-component-builder-trigger]' ).off( 'click' ).on( 'click', function(event) {
      event.preventDefault();
      event.stopImmediatePropagation();

      if( typeof jQuery(this).attr( 'data-payment-page-interaction-state' ) !== 'undefined' ) {
        if( jQuery(this).attr( 'data-payment-page-interaction-state' ) === 'active' ) {
          return;
        }
      }

      objectInstance.headerContainer.find( ' > [data-payment-page-component-builder-trigger]' )
                                    .not( jQuery(this) )
                                    .attr( 'data-payment-page-interaction-state', 'inactive' );
      jQuery(this).attr( 'data-payment-page-interaction-state', 'active');

      let _displaySection = jQuery(this).attr( 'data-payment-page-component-builder-trigger' ).replace( 'show_section_', '' );

      let sections = objectInstance.sectionsWrapper.find( ' > [data-payment-page-component-builder-section]' ),
          displaySections = objectInstance.sectionsWrapper.find( ' > [data-payment-page-component-builder-section="section_' + _displaySection + '"]' );

      sections.attr( 'data-payment-page-interaction-state', 'closed' );
      payment_page_set_display_state_hidden( sections.not( displaySections ) );
      payment_page_set_display_state_visible( displaySections );
    });

    this.sectionsWrapper.find( ' > [data-payment-page-component-builder-section] > p' ).off( "click" ).on( "click", function(event) {
      event.preventDefault();
      event.stopImmediatePropagation();

      let parent = jQuery(this).parent();

      if( parent.attr( 'data-payment-page-interaction-state' ) === 'closed' ) {
        parent.attr( 'data-payment-page-interaction-state', 'open' );

        objectInstance.sectionsWrapper.find( ' > [data-payment-page-component-builder-section][data-payment-page-display-state="visible"]' )
                                      .not( parent ).attr( 'data-payment-page-interaction-state', 'closed' );
      } else {
        parent.attr( 'data-payment-page-interaction-state', 'closed' );
      }
    });

    this.sectionsWrapper.find( ' > [data-payment-page-component-builder-section][data-payment-page-display-state="visible"]:first' ).find( ' > p' ).trigger( "click" );

    this.livePreview();

    setTimeout( function() {
      objectInstance.sectionsWrapper.on( "change", ':input[name]', function() {
        objectInstance.refreshLivePreview();
      });
    }, 2000 );
  },

  livePreview : function( settings ) {
    if( typeof settings !== 'object' )
      settings = {};

    let objectInstance = this;

    if( objectInstance.previewContainer.find( ' > .payment-page-application-loader-wrapper' ).length === 0 )
      objectInstance.previewContainer.prepend( payment_page_element_loader( 'mini' ) );

    if( this.previewXHR !== false && typeof this.previewXHR.abort === 'function' )
      this.previewXHR.abort();

    this.previewXHR = PaymentPage.API.post( this.configuration.live_preview_endpoint, settings, function( response ) {
      if( typeof response !== 'undefined' )
        PaymentPage.setHTML( objectInstance.previewContainer, response );

      objectInstance.previewXHR = false;
    } );
  },

  refreshLivePreview : function() {
    let objectInstance = this,
        _formData = this.container.parents("form:first").paymentPageSerializeObject();

    jQuery.each( _formData, function( _k, _v ) {
      if( !_k.startsWith( objectInstance.configuration.field_namespace ) )
        delete _formData[ _k ];
    });

    if( this.previewFormDataString === JSON.stringify( _formData ) )
      return;

    this.previewFormDataString = JSON.stringify( _formData );

    this.livePreview( _formData );
  }

};