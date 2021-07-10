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

if ( ! class_exists( 'Select2CustomFieldType' ) ) :
/**
 * A filed of the `select2` field type lets the user select items from a predefined list by typing the item name and the items possibly be fetched with AJAX.
 *
 * This class defines the `select2` field type.
 *
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *     <li>**is_multiple** - (optional, boolean) if this is set to true, the `multiple` attribute will be inserted into the field input tag, which enables the multiple selections for the user.</li>
 *     <li>**options** - (optional, array) The options argument passed to the `select2` method. For detaks, see https://select2.github.io/options.html
 *     </li>
 *     <li>**callback** - (optional, array)
 *          <ul>
 *              <li>**search** - (optional, callable) Set a callback function that is triggered in the background when the user type something in the select input field expecting a list of suggested items will be displayed.
 *  - `$aQueries` - (array) an array holding the following arguments.
 *     - `q` - (string) the queried characters.
 *     - `page` - (string) the pagination number. When the result has too many items, it can be paginated.
 *     - `field_id` - (string) the field ID that calls the query.
 *     - `section_id` - (string) the section ID that calls the query.
 *  - `$aFieldset` - (array) the field definition array.
 *
 *  The callback method is expected to return an array with the following structure:
 * <code>
 *  array(
 *      'results' => array(
 *          array(
 *              'id'    => 224, //the value saved in the database.
 *              'text'  => 'The title of this item.'   //The text displayed in the drop-down list.
 *          ),
 *          array(
 *              'id'    => 567,
 *              'text'  => 'The title of this item.'
 *          ),
 *          ... continues ...
 *      ),
 *      'pagination' => array(  // can be omitted
 *          'more'  => true,    // (boolean) or false - whether the next paginated item exists or not.
 *      )
 *  )
 * </code>
 *              </li>
 *              <li>**new_tag** - (optional, callable) Set a callback function that is called when the user creates a new tag.
  *  - `$aQueries` - (array) an array holding the following arguments.
 *     - `tag` - (string) the tag name.
 *     - `field_id` - (string) the field ID that calls the query.
 *     - `section_id` - (string) the section ID that calls the query.
 *  - `$aFieldset` - (array) the field definition array.
 *  If this callback is set, the `options` -> `tags` argument will be automatically enabled.
 *
 *  The callback method is expected to return an array with the following structure:
 * <code>
 *  array(
 *      'id'    => 78,              // the value which will be stored in the database.
 *      'text'  => 'Tag Name',   // the tag name gets displayed in the field.
 *      'note'  => 'A console message.' // (optional) A note displayed in the browser console.
 *      'error' => 'An error massage.',  // (optional) if this is set, an error message will be displayed.
 *  )
 * </code>
 *              </li>
 *          </ul>
 *     </li>
 * </ul>
 *
 * @since       3.8.7
 * @version     0.0.4
 * @supports    IE8 or above. (uses JSON object)
 * @requires    Admin Page Framework 3.8.20
 */
class Select2CustomFieldType extends AdminPageFramework_FieldType_select {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'select2', );

    /**
     * Defines the default key-values of this field type settings.
     *
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(

        'label'             => array(),
        'is_multiple'       => false,
        'attributes'        => array(
            'select'    => array(
                'size'          => 1,
                'autofocusNew'  => null,
                'multiple'      => null,    // set 'multiple' for multiple selections. If 'is_multiple' is set, it takes the precedence.
                'required'      => null,
            ),
            'optgroup'  => array(),
            'option'    => array(),
        ),

        /**
         * @see     https://select2.github.io/options.html
         */
        'options'   => array(
            'width' => 'auto',
            // 'maximum-selection-length' => 2,
        ),

        // If a callback is set, the select list will be generated dynamically with Ajax.
        // The callback function must return an array of select list.
        'callback'  => array(
            'search' => null,
            'new_tag' => null,
        ),

    );

    protected function construct() {}

    /**
     * Loads the field type necessary components.
     */
    public function setUp() {}


    /**
     * Returns an array holding the urls of enqueuing scripts.
     * @return      array
     */
    protected function getEnqueuingScripts() {
        $_bDebugMode = $this->isDebugMode();
        return array(
            array(
                'handle_id'     => 'select2',
                'src'           => $_bDebugMode
                    ? dirname( __FILE__ ) . '/select2/js/select2.full.js'
                    : dirname( __FILE__ ) . '/select2/js/select2.full.min.js',
                'in_footer'     => true,
                'dependencies'  => array( 'jquery' ),
            ),
            array(
                'handle_id'     => 'AdminPageFrameworkSelect2FieldType',
                'src'           => dirname( __FILE__ ) . '/js/loader.js',
                'in_footer'     => true,
                'dependencies'  => array( 'jquery', 'select2' ),
                'translation'   => array(
                    'debugMode' => $_bDebugMode,
                    'ajaxURL'   => admin_url( 'admin-ajax.php' ),
                    'nonce'     => wp_create_nonce( get_class( $this ) ),
                ),
            ),
        );
    }

    /**
     * @return      array
     */
    protected function getEnqueuingStyles() {
        return array(
            $this->isDebugMode()
                ? dirname( __FILE__ ) . '/select2/css/select2.css'
                : dirname( __FILE__ ) . '/select2/css/select2.min.css',
            dirname( __FILE__ ) . '/css/style.css',
        );
    }

    /**
     * Returns the field type specific JavaScript script.
     */
    protected function getScripts() {
        // $_sAjaxURL = admin_url( 'admin-ajax.php' );
        // $_aJSArray = json_encode( $this->aFieldTypeSlugs );
        return "";
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

        $_sInputForEncodedValue = '';
        if ( is_callable( $this->getElement( $aField, array( 'callback', 'search' ) ) ) ) {
            $_sInputForEncodedValue = $this->_getChildInputByKey( 'encoded', $aField );
            $aField[ 'attributes' ] = $this->_getAttributesUpdatedForAJAX( $aField );
        }

        $_aOptions = $this->_getSelect2OptionsFormatted( $aField[ 'options' ], $aField );

        $aField[ 'attributes' ][ 'select' ] = array(
            'data-type'       => 'select2',
            'data-field_id'   => $aField[ 'field_id' ],   // checked in the background with the `doOnFieldRegistration()` method using AJAX.
            'data-section_id' => $aField[ 'section_id' ], // checked in the background with the `doOnFieldRegistration()` method using AJAX.
        )   + $this->getDataAttributeArray( $_aOptions )
            + $this->getElementAsArray( $aField, array( 'attributes', 'select', ) );

        return parent::getField( $aField ) // the select field
            . $_sInputForEncodedValue;     // a nested input that stores an encoded selection value.

    }

        /**
         * @return      string
         */
        private function _getChildInputByKey( $sKey, $aField ) {

            $_aAttributes = array(
                'name'              => $aField[ 'attributes' ][ 'name' ] . '[' . $sKey . ']',
                'id'                => $aField[ 'attributes' ][ 'id' ] . '_' . $sKey,
                'data-' . $sKey     => true,
                'type'              => 'hidden',
                'value'             => ( string ) $this->getElement( $aField, array( 'value', $sKey ), '' ),
                'style'             => 'width: 100%',   // for debugging
            );
            return "<input " . $this->getAttributes( $_aAttributes ) . " />";

        }

        /**
         * For AJAX enabled fields, the stored field data structure becomes different.
         *
         * Nested elements of `encoded` and `value` will be added. The selection IDs will be stored in the `value`.
         * The `encoded` element will store the text and id of the user's selection.
         *
         * @return      array
         */
        private function _getAttributesUpdatedForAJAX( $aField ) {

            $_aAttributes = $aField[ 'attributes' ];
            $_aAttributes[ 'name' ] = $_aAttributes[ 'name' ] . "[value]";
            $_aAttributes[ 'id' ]   = $_aAttributes[ 'id' ] . "_value";
            return $_aAttributes;

        }


        /**
         * @return          array
         */
        private function _getSelect2OptionsFormatted( $aOptions, $aField ) {

            // Format camel-cased key names.
            foreach( $aOptions as $_sKey => $_mValue ) {

                if( ! preg_match( '/([a-zA-Z])(?=[A-Z])/', $_sKey ) ) {
                    continue;
                }

                $_sDashed = $this->_getCamelCaseToDashed( $_sKey );
                $aOptions[ $_sDashed ] = $_mValue;
                unset( $aOptions[ $_sKey ] );

            }

            $aOptions[ 'search_callback' ]    = is_callable(
                $this->getElement( $aField, array( 'callback', 'search' ) )
            );
            $_bNewTagCallbackCallable = is_callable(
                $this->getElement( $aField, array( 'callback', 'new_tag' ) )
            );
            $aOptions[ 'new_tag_callback' ] = $_bNewTagCallbackCallable;
            if ( $_bNewTagCallbackCallable ) {
                $aOptions[ 'tags' ] = true;
            }
            return $aOptions;

        }
            /**
             * @return      string
             */
            private function _getCamelCaseToDashed( $sString ) {
                return strtolower( preg_replace( '/([a-zA-Z])(?=[A-Z])/', '$1-', $sString ) );
            }


    /**
     * Calls back the callback function if it is set.
     *
     * Called when the field type is registered.
     */
    protected function doOnFieldRegistration( $aFieldset ) {

        $_aQueries = $_REQUEST;
        if ( ! $this->___shouldProceedToAjaxRequest( $_aQueries, $aFieldset ) ) {
            return;
        }
        unset( $_aQueries[ 'doing_select2_ajax' ] );

        $_asCallable = $this->___getAjaxCallback( $_aQueries, $aFieldset );
        if ( ! is_callable( $_asCallable ) ) {
            return;
        }

        // Will exit in the function.
        wp_send_json(
            call_user_func_array(
                $_asCallable,   // callable
                array(
                    $_aQueries, // param 1
                    $aFieldset  // param 2
                )
            )
        );

    }
        /**
         * @return      boolean|callable        False when a callback is not found. Otherwise, the found callable.
         */
        private function ___getAjaxCallback( $aRequest, $aFieldset ) {

            if ( isset( $aRequest[ 'q' ] ) ) {
                return $this->getElement( $aFieldset, array( 'callback', 'search' ), false );
            }
            if ( isset( $aRequest[ 'tag' ] ) ) {
                return $this->getElement( $aFieldset, array( 'callback', 'new_tag' ), false );
            }
            return false;

        }

        /**
         * @return      boolean
         */
        private function ___shouldProceedToAjaxRequest( $aRequest, $aFieldset ) {

            if (
                ! isset(
                    $aRequest[ 'doing_select2_ajax' ],
                    $aRequest[ 'field_id' ],
                    $aRequest[ 'section_id' ]
                )
            ) {
                return false;
            }

            if ( ! wp_verify_nonce( $aRequest[ 'doing_select2_ajax' ], get_class( $this ) ) ) {
                return false;
            }

            if ( $aFieldset[ 'field_id' ] !== $aRequest[ 'field_id' ] ) {
                return false;
            }
            // @deprecated 0.0.4 Bug fix - this is for nested repeated fields to process properly
            // in meta boxes, if the section id is '_default', fieldset may not have that key
//            if ( $aFieldset[ 'section_id' ] !== $aRequest[ 'section_id' ] ) {
//                return false;
//            }

            return true;

        }

}
endif;
