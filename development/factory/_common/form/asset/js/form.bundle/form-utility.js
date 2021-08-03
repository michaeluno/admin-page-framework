( function( $ ) {

    /**
     * Checks whether an element is off screen or not.
     *
     * ```
     * // returns all elements that are offscreen
     * $(':offscreen');
     *
     * // boolean returned if element is offscreen
     * $('div').is(':offscreen');
     * ```
     *
     * @see https://stackoverflow.com/a/8897628
     * @param el
     * @returns {boolean}
     */
    $.expr.filters.offscreen = function(el) {
      var rect = el.getBoundingClientRect();
      return (
               (rect.x + rect.width) < 0 
                 || (rect.y + rect.height) < 0
                 || (rect.x > window.innerWidth || rect.y > window.innerHeight)
             );
    };    
    
    $.fn.reverse = [].reverse;

    $.fn.formatPrintText = function() {
        var aArgs = arguments;
        return aArgs[ 0 ].replace( /{(\d+)}/g, function( match, number ) {
            return typeof aArgs[ parseInt( number ) + 1 ] != 'undefined'
                ? aArgs[ parseInt( number ) + 1 ]
                : match;
        });
    };

    /**
     * Compare two software version numbers (e.g. 1.7.1)
     * Returns:
     *
     *  0 if they're identical
     *  negative if v1 < v2
     *  positive if v1 > v2
     *  Nan if they in the wrong format
     *
     *  E.g.:
     *
     *  assert(version_number_compare("1.7.1", "1.6.10") > 0);
     *  assert(version_number_compare("1.7.1", "1.7.10") < 0);
     *
     *  "Unit tests": http://jsfiddle.net/ripper234/Xv9WL/28/
     *
     *  Taken from http://stackoverflow.com/a/6832721/11236
     *  @since 3.9.0
     *  @see   https://stackoverflow.com/a/6832721
     */
    $.fn.compareVersionNumbers = function( v1, v2 ){
        var v1parts = v1.split('.');
        var v2parts = v2.split('.');

        // First, validate both numbers are true version numbers
        function validateParts(parts) {
            for (var i = 0; i < parts.length; ++i) {
                if (!isPositiveInteger(parts[i])) {
                    return false;
                }
            }
            return true;
        }
        if (!validateParts(v1parts) || !validateParts(v2parts)) {
            return NaN;
        }

        for (var i = 0; i < v1parts.length; ++i) {
            if (v2parts.length === i) {
                return 1;
            }

            if (v1parts[i] === v2parts[i]) {
                continue;
            }
            if (v1parts[i] > v2parts[i]) {
                return 1;
            }
            return -1;
        }

        if (v1parts.length !== v2parts.length) {
            return -1;
        }

        return 0;

        function isPositiveInteger(x) {
            // http://stackoverflow.com/a/1019526/11236
            return /^\d+$/.test(x);
        }
    }

}( jQuery ));