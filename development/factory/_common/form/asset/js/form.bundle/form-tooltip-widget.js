/**
 * Extends the core wp-pointer tooltip jQuery widget to add additional features.
 */
(function($){

	var zindex = 9999;

  // Extend wp.pointer jQuery widget
  $.widget( 'admin-page-framework.pointerTooltip', $.wp.pointer, {
   /**
    * Overrides the reposition() method.
    * The show() method is replaced with fadeIn().
    */
    reposition: function() {
      var position;

      if ( this.options.disabled ) {
        return;
      }

      position = this._processPosition( this.options.position );

      // Reposition pointer.
      this.pointer.css({
        top: 0,
        left: 0,
        zIndex: zindex++ // Increment the z-index so that it shows above other opened pointers.
      });
      this.pointer.fadeIn( this.options.fadeIn );
          var _optionsPosition = $.extend(
            {
              of: this.element,
              collision: 'fit none'
            },
            position
          );
      this.pointer.position( _optionsPosition ); // The object comes before this.options.position so the user can override position.of.
      this.repoint();
    },
    _create: function() {
      this._super();
      if ( this.options.pointerHeight ) {
        this.pointer
          .css({
            height: this.options.pointerHeight+'px'
          });
      }
    },
    get: function() {
      return this.pointer;
    }

  } );

}(jQuery));