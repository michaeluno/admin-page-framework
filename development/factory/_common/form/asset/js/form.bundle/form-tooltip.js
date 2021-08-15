/**
 * Displays tips for fields in a tooltip.
 *
 * It provides the $[ 'admin-page-framework-tooltip' ]( options ) jQuery plugin method.
 * The name uses hyphens for the user's text domain to be replaced. So the compiled framework script will be entirely ported with without the keyword of `admin-page-framework`.
 *
 * To use the method, have a text message enclosed in an element with a class `admin-page-framework-form-tooltip-content`.
 * ```
 *    <span class="my-tooltip dashicons dashicons-editor-help">
 *      <span class="admin-page-framework-form-tooltip-content">
 *        Some text
 *      </span>
 *    </span>
 * ```
 * Then, call it like
 * ```
 * $( '.my-tooltip' )[ 'admin-page-framework-form-tooltip' ]();
 * ```
 *
 * When the framework file is compiled, replace the keyword `admin-page-framework` with your text domain.
 *
 */
(function($){

  // Initialize
  $( document ).ready( function() {
    $( 'a.admin-page-framework-form-tooltip' )[ 'admin-page-framework-form-tooltip' ]();
  } );

  $.fn[ 'admin-page-framework-form-tooltip' ] = function( options ) {
    initialize( this, options )
  };

  function initialize( target, options ) {

    var _this = $( target );
    options = 'undefined' === typeof options ? {} : options;

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

      var _offscreen  = $( this ).offset().left + $( this ).width() + _width > _body.offset().left + _body.width();
      // Open the tooltip
      $( this ).pointerTooltip( $.extend( true, {}, {
        pointerClass: 'admin-page-framework-form-tooltip-balloon' + ( _offscreen ? ' offscreen' : '' ),
        pointerWidth: _width,
        content: function() {
          return _content.html();
        },
        position: {
          edge: _offscreen ? 'top' : 'left',
          align: _offscreen ? 'center' : 'left',
          within: _offscreen ? _body : $( this ).closest( '.admin-page-framework-field, .admin-page-framework-fieldrow, .admin-page-framework-section' ),
          collision: 'fit',
        },
        buttons: function() {},
        close: function() {},
      }, options ) )
        .pointerTooltip( 'open' );

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