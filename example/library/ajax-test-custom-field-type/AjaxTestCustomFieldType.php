<?php
/**
 * Admin Page Framework
 *
 * Facilitates WordPress plugin and theme development.
 *
 * @author      Michael Uno <michael@michaeluno.jp>
 * @copyright   2013-2021 (c) Michael Uno
 * @license     MIT <http://opensource.org/licenses/MIT>
 * @package     AdminPageFramework
 */


/**
 * A filed type that test Ajax calls.
 *
 * @since       3.8.14
 * @version     0.0.1
 */
class AjaxTestCustomFieldType extends AdminPageFramework_FieldType_select {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'ajax_test', );

    protected function construct() {
        // wp_ajax_{action name}
        // This is a dummy callback. Adding a dummy callback because WordPress does not proceed in admin-ajax.php
        // and the `admin_init` action is not triggered if no `wp_ajax_{...}` action is registered.
        add_action( 'wp_ajax_apf_ajax_test_field_type' , '__return_empty_string' );

    }
    public function replyToHandleAjaxRequest() {

        $_aResponse = array();
        if ( ! isset( $_POST[ 'ajax_test_field_value' ] ) ) {
            wp_send_json( $_aResponse );
        }
        switch ( sanitize_text_field( $_POST[ 'ajax_test_field_value' ] ) ) {
            case 'a':
                $_aResponse = array( 'value' => 'Apple' );
                break;
            case 'b':
                $_aResponse = array( 'value' => 'Banana' );
                break;
            default:
                $_aResponse = array( 'value' => 'Cherry' );
                break;
        }

        // ajax handlers must die
        wp_send_json( $_aResponse );

    }

    /**
     * Returns an array holding the urls of enqueuing scripts.
     * @return      array
     */
    protected function getEnqueuingScripts() {
        return array(
            array(
                'handle_id'     => 'ajax_field_test',
                'src'           => dirname( __FILE__ ) . '/js/script.js',
                'in_footer'     => true,
                'dependencies'  => array( 'jquery' ),
                'translation'   => array(
                    'admin_ajax_url' => admin_url( 'admin-ajax.php' ),
                ),
            ),
        );
    }

    /**
     * Calls back the callback function if it is set.
     *
     * Called when the field type is registered.
     * @param array $aFieldset
     */
    protected function doOnFieldRegistration( $aFieldset ) {

        if ( isset( $_REQUEST[ '_doing_apf_ajax_text' ] ) ) {
            $this->replyToHandleAjaxRequest();
        }

    }

}