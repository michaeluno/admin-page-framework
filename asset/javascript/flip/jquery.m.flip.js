/*!
 * M Flip - jQuery Plugin
 * version 0.1 (17 Feb 2015)
 * requires jQuery v1.6 or later
 *
 * Examples at http://codepen.io/unavezfui/pen/Byryep
 *
 * Copyright 2015 Manu Morante - www.manumorante.com
 *
 */
(function($){

  var isTouch   = document.createTouch !== undefined,
    evt_hover = (isTouch)? 'touchstart' : 'mouseover',
    evt_out   = (isTouch)? 'touchend'   : 'mouseout';


  $.fn.extend({
    mflip: function(){
      return this.each(function(){

        var $f = $(this),
          $c,
          rotation = $f.data('rotation');

        $f.html('<div class="m-flip__content">'+ $f.html() +'</div>');
        $c = $('.m-flip__content', $f);

        // Event: Rollover / Touchstart
        $f.bind(evt_hover, function(){

          if( isNaN(rotation) ){
            $c.addClass('active');

          } else {
            $c.css({
              '-webkit-transform': 'rotateY('+ rotation +'deg)',
              '-moz-transform': 'rotateY('+ rotation +'deg)',
              'transform': 'rotateY('+ rotation +'deg)'
            });
          }

          // Event: Rollout / Touchend
        }).bind(evt_out, function(){

          if( isNaN(rotation) ){
            $c.removeClass('active');

          }else{
            $c.css({
              '-webkit-transform': 'rotateY(0deg)',
              '-moz-transform': 'rotateY(0deg)',
              'transform': 'rotateY(0deg)'
            });
          }

        });
      });
    }
  });

})(jQuery);
