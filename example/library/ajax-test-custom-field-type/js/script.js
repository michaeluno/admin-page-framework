/*jslint browser: true, plusplus: true */
(function ($, window, document) {
    'use strict';
    // execute when the DOM is ready
    $(document).ready(function () {
        // js 'change' event triggered on the wporg_field form field
        $('*[data-type=ajax_test] select').on('change', function () {

            // jQuery post method, a shorthand for $.ajax with POST
            $.post(
                ajax_field_test.admin_ajax_url,                        // or ajaxurl
                {
                    action: 'apf_ajax_test_field_type',               // POST data, action
                    _doing_apf_ajax_text: true,
                    ajax_test_field_value: $( this ).val() // POST data, wporg_field_value
                },
                function (data) {
                    if ( 0 === data ) {
                        alert( 'No response' );
                    } else {
                        alert( data.value );
                    }
                },
                'json'
            );
        });
    });
}(jQuery, window, document));