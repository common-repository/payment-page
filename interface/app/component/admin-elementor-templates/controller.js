PaymentPage.Component[ 'admin-elementor-templates' ] = {

	container     : {},
	configuration : {},
	data : {},

	Init : function( container ) {
		this.container = container;

		let objectInstance = this;

		payment_page_component_configuration_parse( this, function() {
      objectInstance._loadData();
    } );
	},

	_loadData : function() {
		let objectInstance = this;

		PaymentPage.setLoadingContent( this.container );

		PaymentPage.API.fetch(
			'payment-page/v1/administration/dashboard',
			false,
			function( response ) {
				if ( typeof response !== 'object' ) {
					PaymentPage.setFailedAssetFetchContent( objectInstance.container );
					return;
				}

				objectInstance.data = response;

				objectInstance._loadTemplate();
			}
		);
	},
  
	_loadTemplate : function() {
		let objectInstance = this;

		PaymentPage.Template.load(
			this.container,
			'admin-elementor-templates',
			'template/default.html',
			this.data,
			function() {
				objectInstance._bindEvents();
			}
		);
	},

	_bindEvents : function() {
		let objectInstance = this;

		this.container.find( '[data-payment-page-component-admin-elementor-templates-trigger="install_plugin_elementor"]' ).on(
			"click",
			function() {
				if ( typeof jQuery( this ).attr( "disabled" ) !== 'undefined' ) {
					return;
				}

				if ( jQuery( this ).find( '.payment-page-application-loader-wrapper' ).length > 0 ) {
					return;
				}

				PaymentPage.setLoadingContent( jQuery( this ), '', 'mini' );

				objectInstance.container.find( '[data-payment-page-component-admin-elementor-templates-trigger="install_plugin_elementor"]' ).not( jQuery( this ) ).attr( "disabled", "disabled" );

				let parentContainer = jQuery( this ).parents( '[data-payment-page-component-admin-elementor-templates-section="template_information"]:first' );

				parentContainer.find( '[data-payment-page-notification]' ).remove();

				PaymentPage.API.post(
					'payment-page/v1/plugin/install',
					{ 'identifier' : 'elementor' },
					function( response ) {
						if ( typeof response.message !== 'undefined' ) {
							parentContainer.append( '<div data-payment-page-notification="danger">' + response.message + '</div>' );

							return;
						}

						PaymentPage.API.post(
							'payment-page/v1/plugin/activate',
							{ 'identifier' : 'elementor' },
							function( response ) {
								objectInstance._loadData();
							}
						);
					}
				);
			}
		);

		this.container.find( '[data-payment-page-component-admin-elementor-templates-trigger="activate_plugin_elementor"]' ).on(
			"click",
			function() {
				if ( typeof jQuery( this ).attr( "disabled" ) !== 'undefined' ) {
					return;
				}

				if ( jQuery( this ).find( '.payment-page-application-loader-wrapper' ).length > 0 ) {
					return;
				}

				let parentContainer = jQuery( this ).parents( '[data-payment-page-component-admin-elementor-templates-section="template_information"]:first' );

				parentContainer.find( '[data-payment-page-notification]' ).remove();

				PaymentPage.setLoadingContent( jQuery( this ), '', 'mini' );

				objectInstance.container.find( '[data-payment-page-component-admin-elementor-templates-trigger="activate_plugin_elementor"]' ).not( jQuery( this ) ).attr( "disabled", "disabled" );

				PaymentPage.API.post(
					'payment-page/v1/plugin/activate',
					{ 'identifier' : 'elementor' },
					function( response ) {
						if ( typeof response.message !== 'undefined' ) {
							parentContainer.append( '<div data-payment-page-notification="danger">' + response.message + '</div>' );

							return;
						}

						objectInstance._loadData();
					}
				);
			}
		);

		this.container.find( '[data-payment-page-component-admin-elementor-templates-trigger^="select_template_"]' ).on(
			"click",
			function() {
				if ( typeof jQuery( this ).attr( "disabled" ) !== 'undefined' ) {
					return;
				}

				if ( jQuery( this ).find( '.payment-page-application-loader-wrapper' ).length > 0 ) {
					return;
				}

				let parentContainer = jQuery( this ).parents( '[data-payment-page-component-admin-elementor-templates-section="template_information"]:first' );

				parentContainer.find( '[data-payment-page-notification]' ).remove();

				PaymentPage.setLoadingContent( jQuery( this ), '', 'mini' );

				objectInstance.container.find( '[data-payment-page-component-admin-elementor-templates-trigger^="select_template_"]' ).not( jQuery( this ) ).attr( "disabled", "disabled" );

				PaymentPage.API.post(
					'payment-page/v1/administration/import-template',
					{
						'id' : parseInt( jQuery( this ).attr( 'data-payment-page-component-admin-elementor-templates-trigger' ).replace( "select_template_", "" ) )
					},
					function( response ) {
						if ( typeof response.message !== 'undefined' ) {
							parentContainer.append( '<div data-payment-page-notification="' + ( typeof response.code !== 'undefined' ? 'danger' : 'success' ) + '">' + response.message + '</div>' );
						}

						objectInstance.container.find( '[data-payment-page-component-admin-elementor-templates-trigger^="select_template_"]' ).removeAttr( "disabled" ).html( objectInstance.data.lang.template_select );
					}
				);
			}
		);
	}

};
