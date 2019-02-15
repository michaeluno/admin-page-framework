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

if ( ! class_exists( 'ToggleCustomFieldType' ) ) :
/**
 * A field type that lets the user toggle a switch.
 *
 * @since       3.8.5
 * @version     0.0.3b
 * @requires    Admin Page Framework 3.8.8 or above.
 */
class ToggleCustomFieldType extends AdminPageFramework_FieldType_checkbox {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'toggle', );

    /**
     * Defines the default key-values of this field type settings.
     *
     * @remark\ $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes'    =>  array(
            'input'         => array(),
            'remove_button' => array(),
            'select_button' => array(),
        ),
        'label'         => '',  // dummy value as the JS script will hide it.
        /**
         * @see     https://github.com/simontabor/jquery-toggles
         */
        'options'   => array(
            'drag'      =>  true,   // allow dragging the toggle between positions
            'click'     =>  true,   // allow clicking on the toggle
            'text'      => array(
                'on'    => 'ON',    // text for the ON position
                'off'   => 'OFF',   // and off
             ),
            'animate'   => 250,     // animation time (ms)
            'easing'    =>'swing',  // animation transition easing function. Can be `leaner`.
            'width'     => 50,      // width used if not set in css
            'height'    => 20,      // height if not set in css
            'type'      => 'compact',    // if this is set to 'select' then the select style toggle will be used

            // Unused:
            // 'on'        => true,    // is the toggle ON on init - will be set with the field value
            // 'checkbox'  => null,    // the checkbox jQuery element object to toggle (to use in forms), or the selector string of the element e.g. `#the-element`.
            // 'clicker'   => null,    // the jQuery element object that can be clicked on to toggle and removes binding from the toggle itself (use nesting). Or the selector string of the element.

        ),

        'theme'           => 'modern',    // either `soft`, `light`, `dark`, `iphone`, or `modern` is allowed.
        'save_unchecked'  => true,      // 0.0.3+
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
        return array(
            array(
                'src'           => $this->isDebugMode()
                    ? dirname( __FILE__ ) . '/toggles/js/toggles.js'
                    : dirname( __FILE__ ) . '/toggles/js/toggles.min.js',
                'in_footer' => true,
                'dependencies'  => array( 'jquery' )
            ),
        );
    }

    /**
     * @return      array
     */
    protected function getEnqueuingStyles() {
        return array(
            dirname( __FILE__ ) . '/toggles/css/toggles-full.css',
        );
    }

    /**
     * Returns the field type specific JavaScript script.
     */
    protected function getScripts() {

        $_aJSArray            = json_encode( $this->aFieldTypeSlugs );
        return "jQuery( document ).ready( function(){

            /**
             * An enabler function
             * 
             * @since 3.8.5
             */
            var _initalizeToggles = function( oNode ) {
                
                var _iTargetInputDataID = jQuery( oNode ).attr( 'data-checkbox-id' );
                var _iTargetInputID = jQuery( oNode ).attr( 'data-checkbox-id' );
                var _aOptions = {};                
                var _aOptions       = jQuery.extend(
                    {}, 
                    {}, // default
                    _aOptions,  // user input
                    {
                        checkbox: jQuery( 'input[type=checkbox][data-id=' + _iTargetInputDataID + ']' ),
                        text: {
                            on : jQuery( oNode ).attr( 'data-toggle-text-on' ),
                            off: jQuery( oNode ).attr( 'data-toggle-text-off' ),
                        }
                    }   // overriding values
                );
                
                jQuery( oNode ).toggles( _aOptions );
                
            }
            
            // Initialize toggle elements.
            jQuery( '.switch_toggle_buttons' ).each( function () {
                
                // Hide the actual check-box.
                jQuery( this ).closest( '.admin-page-framework-field' )
                    .children( '.admin-page-framework-checkbox-container-toggle' )
                    .hide();
                    
                _initalizeToggles( this );
            });
            
            jQuery().registerAdminPageFrameworkCallbacks( {
                /**
                 * Called when a field of this field type gets repeated.
                 */
                repeated_field: function( oCloned, aModel ) {
                                
                    // Increment element IDs.
                   oCloned.find( '.switch_toggle_buttons' ).incrementAttributes(
                        [ 'data-checkbox-id' ], // attribute name
                        aModel[ 'incremented_from' ], // increment from
                        aModel[ 'id' ] // digit model
                    ); 
                        
                    oCloned.find( '.switch_toggle_buttons' ).each( function () {
                        _initalizeToggles( this );
                    });                    
                    
                }, 
            },
            [ 'toggle' ]    // subject field type slugs
            );

        });";
    }

    /**
     * Returns the field type specific CSS rules.
     */
    protected function getStyles() {

        return
".toggle-button-container {
    display: inline-block;
    padding: 5px 8px 0 0;
}
";
    }

    /**
     * Returns the output of the field type.
     */
    public function getField( $aField ) {

        // Fix some checkbox specific field arguments.
        $aField[ 'label' ]              = '';
        $aField[ 'select_all_button' ]  = false;
        $aField[ 'select_none_button' ] = false;

        $_sOutput = parent::getField( $aField );
        $_sOutput  = str_replace(
            "<div class='repeatable-field-buttons'></div>", // search
            '', // replace
            $_sOutput // subject
        );
        return $_sOutput
            . "<div class='toggle-button-container'>" . $this->_getToggleElement( $aField ) . "</div>"
            . "<div class='repeatable-field-buttons'></div>";

    }

        /**
         * Returns the HTML output of the toggle element.
         * @since       3.8.5
         * @return      string
         */
        private function _getToggleElement( $aField ) {

            $_aAttributes = $this->getDataAttributeArray(
                $this->_getTogglesOptionsFormatted( $aField[ 'options' ], $aField )
            );
            $_sDisabled = isset( $aField[ 'attributes' ][ 'disabled' ] )
                ? 'disabled'
                : '';
            $_aAttributes = array(
                'class'     => $_sDisabled . ' '
                    . 'switch_toggle_buttons '
                    . 'toggle-' . $aField[ 'theme' ],
                ) + $_aAttributes;
            return '<div ' . $this->getAttributes( $_aAttributes ) . ' >'
                . '</div>';

        }
            /**
             *
             * These can be set with the data attributes.
             * on, drag, click, width, height, animate, easing, type, checkbox
             * @return      array
             */
            private function _getTogglesOptionsFormatted( $aOptions, $aField ) {

                // Set the input value.
                $aOptions[ 'on' ] = $aField[ 'value' ];

                // These have to be dealt differently.
                $aOptions[ 'text-on' ] = $aOptions[ 'text' ][ 'on' ];
                $aOptions[ 'text-off' ] = $aOptions[ 'text' ][ 'off' ];

                // Prepend `toggle-` to each key.
                $_aOptions = array();
                foreach( $aOptions as $_sKey => $_mValue ) {

                    // Convert boolean values to a string value.
                    if ( is_bool( $_mValue ) ) {
                        $_mValue = $_mValue ? 'true' : 'false';
                    }
                    $_sKey = 'toggle-' . $_sKey;
                    $_aOptions[ $_sKey ] = $_mValue;

                }

                /**
                 * Embed an input id. Note that checkbox inputs expect `label` arguments to be set and they are parsed as an array.
                 * There fore the `checkbox` field type appends `_` to the actual input id which can be confusing and this behavour may be changed in the future.
                 * So use a different way to retrieve the subject input id and therefore, embed the input id here.
                 */
                $_aOptions[ 'checkbox-id' ] = $aField[ 'input_id' ];

                return $_aOptions;

            }

}
endif;
