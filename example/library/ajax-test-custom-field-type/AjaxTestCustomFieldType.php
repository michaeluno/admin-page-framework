<?php
/**
 * Admin Page Framework
 *
 * Facilitates WordPress plugin and theme development.
 *
 * @author      Michael Uno <michael@michaeluno.jp>
 * @copyright   2013-2019 (c) Michael Uno
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

    /**
     * Defines the default key-values of this field type settings.
     *
     * @remark\ $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    // protected $aDefaultKeys = array();

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
        switch ( $_POST[ 'ajax_test_field_value' ] ) {
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
     * Loads the field type necessary components.
     */
    public function setUp() {}

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
     * @return      array
     */
    protected function getEnqueuingStyles() {
        return array();
    }

    /**
     * Returns the field type specific JavaScript script.
     */
    protected function getScripts() {

        $_aJSArray = json_encode( $this->aFieldTypeSlugs );
        return
"jQuery( document ).ready( function(){
    

});";
    }

    /**
     * Returns the field type specific CSS rules.
     */
    protected function getStyles() {
        return "";
    }

    /**
     * Returns the output of the field type.
     *
     * @return      string
     */
    public function getField( $aField ) {

        return parent::getField( $aField );

    }



    /**
     * Callks back the callback function if it is set.
     *
     * Called when the field type is registered.
     */
    protected function doOnFieldRegistration( $aFieldset ) {

        $_aQueries = $_REQUEST;
        if ( ! $this->_shouldProceedToAjaxRequest( $_aQueries, $aFieldset ) ) {
            return;
        }
        unset( $_aQueries[ '_doing_apf_ajax_text' ] );
        $this->replyToHandleAjaxRequest();

    }

        /**
         * @return      boolean
         */
        private function _shouldProceedToAjaxRequest( $aRequest, $aFieldset ) {

            return isset( $aRequest[ '_doing_apf_ajax_text' ] );

            if (
                ! isset(
                    $aRequest[ '_doing_apf_ajax_text' ],
                    $aRequest[ 'field_id' ],
                    $aRequest[ 'section_id' ]
                )
            ) {
                return false;
            }
            if ( $aFieldset[ 'field_id' ] !== $aRequest[ 'field_id' ] ) {
                return false;
            }
            if ( $aFieldset[ 'section_id' ] !== $aRequest[ 'section_id' ] ) {
                return false;
            }

            return true;

        }

}
