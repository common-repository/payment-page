PaymentPage.Library.Sortable = {

  /**
   * @param target
   * @param args
   * @constructor
   */
  Init : function( target, args ) {
    let objectInstance = this;

    if ( typeof window.Sortable !== "undefined" ) {
      this._init( target, args );
    } else {
      PaymentPage.LoadAssets( PaymentPage.settings.libraries.sortable, function() {
        objectInstance._init( target, args );
      });
    }
  },

  _init : function( target, args ) {
    let sortable = new window.Sortable( jQuery( target )[ 0 ], args );
  }

};