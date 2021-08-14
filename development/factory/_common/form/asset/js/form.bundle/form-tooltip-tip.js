/**
 * Displays tips for fields in a tooltip.
 */
(function($){

  // Initialize
  $( document ).ready( function() {
    initialize( $( 'a.admin-page-framework-form-tooltip' ) );
  } );

  function initialize( target ) {

    var _this = $( target )

    // Disable the CSS default tooltip
    $( _this ).removeClass( 'no-js' );

    var _pointerTooltip = $( _this );
    _pointerTooltip.on( 'click', function() {
      return false; // disable click
    });
    _pointerTooltip.on( 'mouseover touchend', function( event ) {

      var _body      = $( 'body' );
      var _width     = $( this ).data( 'width' ) || 340;
      var _content   = $( this ).find( '.admin-page-framework-form-tooltip-content' ).clone();
      // Add it to the bottom of body to calculate width. The initial position will make the window expand and wider than the initial window width and a horizontal scrollbar appears
      // This added element should be removed when the tooltip is closed.
      _body.append( _content.css( 'display', 'block' ) );

      var _offscreen = $( this ).offset().left + $( this ).width() + _width > _body.offset().left + _body.width();

      // Open the tooltip
      $( this ).pointerTooltip({
        pointerClass: 'admin-page-framework-form-tooltip-balloon',
        pointerWidth: _width,
        content: function() {
          return _content.html();
        },
        position: {
          edge: _offscreen ? 'top' : 'left',
          align: _offscreen ? 'center' : 'left',
          within: _offscreen ? _body : $( this ).closest( '.admin-page-framework-section' ),
          collision: 'fit',
        },
        buttons: function() {},
        close: function() {},
      }).pointerTooltip( 'open' );

      // Handle toolitip closing
      var _self    = this;
      /// For non-mobile devices
      $( this ).add( '.admin-page-framework-form-tooltip-balloon' ).on( 'mouseleave', function( event ){
        var _selfMouseLeave = this;
        // Set a timeout for the tooltip to close, allowing us to clear this trigger if the mouse comes back over
        var _timeoutId = setTimeout(function(){
          $( _self ).pointerTooltip( 'close' );
          _content.remove();
          $( _self ).off( 'mouseleave' );
          $( _selfMouseLeave ).off( 'mouseleave' );
          $( _self ).off( 'mouseenter' );
          $( _selfMouseLeave ).off( 'mouseenter' );
        }, 650 );
        $( _self ).data( 'timeoutId', _timeoutId );

      } );
      $( this ).add( '.admin-page-framework-form-tooltip-balloon' ).on( 'mouseenter', function(){
        clearTimeout( $( _self ).data('timeoutId' ) );
      });
      /// For mobile devices
      setTimeout( function() {
        _body.on( 'touchstart', closeTooltipMobile );
        function closeTooltipMobile( event ) {
          $( 'body' ).off( 'touchstart', closeTooltipMobile );
          $( _self ).pointerTooltip( 'close' );
        }
      }, 200 );


    } );


  }

}(jQuery));