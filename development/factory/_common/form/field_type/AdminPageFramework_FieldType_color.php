<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * A text field with a color picker.
 * 
 * This class defines the color field type.
 * 
 * <h2>Field Definition Arguments</h2>
 * 
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 * 
 * <h2>Example</h2>
 * <code>
 *  array( 
 *      'field_id'      => 'color_picker_field',
 *      'title'         => __( 'Color Picker', 'admin-page-framework-loader' ),
 *      'type'          => 'color',
 *  ),
 * </code>
 * 
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/color.png
 * @package         AdminPageFramework
 * @subpackage      Common/Form/FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 */
class AdminPageFramework_FieldType_color extends AdminPageFramework_FieldType {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'color' );
    
    /**
     * Defines the default key-values of this field type. 
     */
    protected $aDefaultKeys = array(
        'attributes' => array(
            'size'      => 10,
            'maxlength' => 400,
            'value'     => 'transparent',
        ),    
    );

    /**
     * Loads the field type necessary components.
     * 
     * Loads necessary files of the color field type.
     * @since       2.0.0
     * @since       2.1.5       Moved from AdminPageFramework_MetaBox. Changed the name from enqueueColorFieldScript().
     * @since       3.3.1       Changed from `_replyToFieldLoader()`.
     * @see         http://www.sitepoint.com/upgrading-to-the-new-wordpress-color-picker/
     * @internal
     */ 
    protected function setUp() {
        
        // If the WordPress version is greater than or equal to 3.5, then load the new WordPress color picker.
        if ( version_compare( $GLOBALS['wp_version'], '3.5', '>=' ) ) {
            //Both the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
        }
        // If the WordPress version is less than 3.5 load the older farbtasic color picker.
        else {
            //As with wp-color-picker the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
            wp_enqueue_style( 'farbtastic' );
            wp_enqueue_script( 'farbtastic' );
        }    
        
    }    

    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     * @internal
     */ 
    protected function getStyles() {
        return <<<CSSRULES
/* Color Picker */
.repeatable .colorpicker {
    display: inline;
}
.admin-page-framework-field-color .wp-picker-container {
    vertical-align: middle;
}
.admin-page-framework-field-color .ui-widget-content {
    border: none;
    background: none;
    color: transparent;
}
.admin-page-framework-field-color .ui-slider-vertical {
    width: inherit;
    height: auto;
    margin-top: -11px;
}
.admin-page-framework-field-color .admin-page-framework-repeatable-field-buttons {
    margin-top: 0;
}
.admin-page-framework-field-color .wp-color-result {
    /* Overriding the default css rule, margin: 0 6px 6px 0px; to vertically align middle in the sortable box */
    margin: 3px;
}

CSSRULES;

    }    
    
    /**
     * Returns the color picker JavaScript script loaded in the head tag of the created admin pages.
     * 
     * @since       2.0.0
     * @since       2.1.3       Changed to define a global function literal that registers the given input field as a color picker.
     * @since       2.1.5       Changed the name from `getColorPickerScript()`.
     * @since       3.3.1       Changed the name from `_replyToGetScripts()`.
     * @internal
     * @return      string      The image selector script.
     * @see         https://github.com/Automattic/Iris
     */ 
    protected function getScripts() {
        $_aJSArray      = json_encode( $this->aFieldTypeSlugs );
        $_sDoubleQuote  = '\"';
        return <<<JAVASCRIPTS
registerAdminPageFrameworkColorPickerField = function( osTragetInput, aOptions ) {
    
    var osTargetInput   = 'string' === typeof osTragetInput 
        ? '#' + osTragetInput 
        : osTragetInput;
    var sInputID        = 'string' === typeof osTragetInput 
        ? osTragetInput 
        : osTragetInput.attr( 'id' );

    // Only for the iris color picker.
    var _aDefaults = {
        defaultColor: false, // you can declare a default color here, or in the data-default-color attribute on the input     
        change: function( event, ui ){
            jQuery( this ).trigger( 
                'admin_page_framework_field_type_color_changed',
                [ jQuery( this ), sInputID ]
            ); 
        }, // a callback to fire whenever the color changes to a valid color. reference : http://automattic.github.io/Iris/     
        clear: function( event, ui ) {
            jQuery( this ).trigger( 
                'admin_page_framework_field_type_color_cleared', 
                [ jQuery( this ), sInputID ]
            );            
        }, // a callback to fire when the input is emptied or an invalid color
        hide: true, // hide the color picker controls on load
        palettes: true // show a group of common colors beneath the square or, supply an array of colors to customize further                
    };
    var _aColorPickerOptions = jQuery.extend( {}, _aDefaults, aOptions );
        
    'use strict';
    /* This if-statement checks if the color picker element exists within jQuery UI
     If it does exist, then we initialize the WordPress color picker on our text input field */
    if( 'object' === typeof jQuery.wp && 'function' === typeof jQuery.wp.wpColorPicker ){
        jQuery( osTargetInput ).wpColorPicker( _aColorPickerOptions );
    }
    else {
        /* We use farbtastic if the WordPress color picker widget doesn't exist */
        jQuery( '#color_' + sInputID ).farbtastic( osTargetInput );
    }
}

/* The below function will be triggered when a new repeatable field is added. Since the APF repeater script does not
    renew the color piker element (while it does on the input tag value), the renewal task must be dealt here separately. */
jQuery( document ).ready( function(){
        
    jQuery().registerAdminPageFrameworkCallbacks( {     
        added_repeatable_field: function( oClonedField, sFieldType, sFieldTagID, sCallType ) {
                        
            oClonedField.find( 'input.input_color' ).each( function( iIterationIndex ) {
                
                var _oNewColorInput = jQuery( this );
                var _oIris          = _oNewColorInput.closest( '.wp-picker-container' );
                // WP 3.5+
                if ( _oIris.length > 0 ) { 
                    // unbind the existing color picker script in case there is.
                    var _oNewColorInput = _oNewColorInput.clone(); 
                }                    
                var _sInputID       = _oNewColorInput.attr( 'id' );
                
                // Reset the value of the color picker.
                var _sInputValue    = _oNewColorInput.val() 
                    ? _oNewColorInput.val() 
                    : _oNewColorInput.attr( 'data-default' );
                var _sInputStyle = _sInputValue !== 'transparent' && _oNewColorInput.attr( 'style' )
                    ? _oNewColorInput.attr( 'style' ) 
                    : '';
                _oNewColorInput.val( _sInputValue ); // set the default value    
                _oNewColorInput.attr( 'style', _sInputStyle ); // remove the background color set to the input field ( for WP 3.4.x or below )  

                // Replace the old color picker elements with the new one.
                // WP 3.5+
                if ( _oIris.length > 0 ) { 
                    jQuery( _oIris ).replaceWith( _oNewColorInput );
                } 
                // WP 3.4.x -     
                else { 
                    oClonedField.find( '.colorpicker' )
                        .replaceWith( '<div class=\"colorpicker\" id=\"color_' + _sInputID + '\"></div>' );
                }

                // Bind the color picker event.
                registerAdminPageFrameworkColorPickerField( _oNewColorInput );                
            
            } );

        }
    },
    {$_aJSArray}
    );
});
JAVASCRIPTS;

    }    
    
    /**
     * Returns the output of the color field.
     * 
     * @since       2.1.5
     * @since       3.0.0     Removed unnecessary parameters.
     * @since       3.3.1     Changed from `_replyToGetField()`.
     * @internal
     */
    protected function getField( $aField ) {

        // If the value is not set, apply the default value, 'transparent'.
        $aField['value'] = is_null( $aField['value'] ) 
            ? 'transparent' 
            : $aField['value'];    
            
        $aField[ 'attributes' ] = $this->_getInputAttributes( $aField );
        
        return 
            $aField[ 'before_label' ]
            . "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField[ 'input_id' ]}'>"
                    . $aField[ 'before_input' ]
                    . ( $aField[ 'label' ] && ! $aField[ 'repeatable' ]
                        ? "<span " . $this->getLabelContainerAttributes( $aField, 'admin-page-framework-input-label-string' ) . ">" 
                                . $aField['label'] 
                            . "</span>"
                        : "" 
                    )
                    . "<input " . $this->getAttributes( $aField[ 'attributes' ] ) . " />" 
                    . $aField[ 'after_input' ]
                    . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                . "</label>"
                . "<div class='colorpicker' id='color_{$aField[ 'input_id' ]}'></div>" // this div element with this class selector becomes a farbtastic color picker. ( below 3.4.x ) // rel='{$aField['input_id']}'
                . $this->_getColorPickerEnablerScript( "{$aField[ 'input_id' ]}" )
            . "</div>"
            . $aField['after_label'];
        
    }
        /**
         * 
         * @return      array
         * @since       3.5.10
         * @internal
         */
        private function _getInputAttributes( array $aField ) {
                               
            return array(
                'color'        => $aField['value'],    
                'value'        => $aField['value'],
                'data-default' => isset( $aField[ 'default' ] )
                    ? $aField[ 'default' ]
                    : 'transparent', // used by the repeatable script
                'type'         => 'text', // it must be text
                'class'        => trim( 'input_color ' . $aField['attributes']['class'] ),
            ) + $aField[ 'attributes' ];
            
        }    
        /**
         * A helper function for the above getColorField() method to add a script to enable the color picker.
         * @return      string
         * @internal
         */
        private function _getColorPickerEnablerScript( $sInputID ) {
            $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function(){
    registerAdminPageFrameworkColorPickerField( '{$sInputID}' );
});            
JAVASCRIPTS;
            return
                "<script type='text/javascript' class='color-picker-enabler-script'>"
                    . '/* <![CDATA[ */'
                    . $_sScript
                    . '/* ]]> */'
                . "</script>";
        }    
    
}
