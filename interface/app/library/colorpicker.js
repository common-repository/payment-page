PaymentPage.Library.Colorpicker = {

  instances : {},

  /**
   * @param target
   * @constructor
   */
  Init : function( targetElement ) {
    let objectInstance = this;

    if ( typeof window.Pickr !== 'undefined' ) {
      this._init( targetElement );
    } else {
      PaymentPage.LoadAssets( PaymentPage.settings.libraries.pickr, function() {
        objectInstance._init( targetElement );
      });
    }
  },

  _init : function( target ) {
    target.each(function() {
      if( typeof jQuery(this).attr( "data-payment-page-library-loaded" ) !== 'undefined' )
        return;

      let _currentTarget = jQuery(this);

      if( typeof _currentTarget.attr( 'id' ) === 'undefined' )
        _currentTarget.attr( 'id', 'pp-cp-' + payment_page_get_random_string() );

      _currentTarget.before( '<div data-payment-page-scope="replace-by-colorpicker"></div>' );

      let pickr = window.Pickr.create({
        el : _currentTarget.parent().find('[data-payment-page-scope="replace-by-colorpicker"]:first')[0],
        theme: 'nano',
        default: _currentTarget.val(),
        inline: false,
        position: "right-end",
        comparison: false,
        autoReposition: true,
        components: {
          preview: true,
          opacity: true,
          hue: true,

          interaction: {
            hex: true,
            rgba: true,
            hsva: true
          }
        }
      });

      pickr.on("change", function(e) {
        let _selectedRepresentation = pickr.getColorRepresentation(),
            value = '';

        if(typeof _selectedRepresentation === "undefined" || _selectedRepresentation === 'HEXA') {
          value = e.toHEXA().toString();
        } else if(_selectedRepresentation === 'RGBA') {
          value = e.toRGBA().toString(3);
        } else if(_selectedRepresentation === 'HSVA') {
          value = e.toHSVA().toString(3);
        }

        _currentTarget.attr("data-color-picker-value", value).val(value).trigger("change");
      });

      _currentTarget.on("change", function(e) {
        if(typeof jQuery(this).attr("data-color-picker-value") !== 'undefined') {
          if(jQuery(this).attr("data-color-picker-value") === jQuery(this).val()) {
            return;
          }
        }

        pickr.setColor(jQuery(this).val());
      });

      PaymentPage.Library.Colorpicker[_currentTarget.attr("id")] = pickr;

      target.attr( "data-payment-page-library-loaded", "colorpicker" );
    })
  },

  Destroy : function( targetElement ) {
    if( typeof targetElement.attr( "id" ) === 'undefined' )
      return;

    if( typeof this.instances[ targetElement.attr( "id" ) ] !== 'undefined' ) {
      PaymentPage.Library.Colorpicker[targetElement.attr("id")].destroy();
      delete this.instances[ targetElement.attr( "id" ) ];
    }
  }

};