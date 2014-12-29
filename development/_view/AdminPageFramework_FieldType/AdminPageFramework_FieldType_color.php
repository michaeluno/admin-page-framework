<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the color field type.
 * 
 * @package         AdminPageFramework
 * @subpackage      FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 * @internal
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
            'size' => 10,
            'maxlength' => 400,
            'value' => 'transparent',
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
.admin-page-framework-field-color .admin-page-framework-field .admin-page-framework-input-label-container {
    vertical-align: top; 
}
.admin-page-framework-field-color .admin-page-framework-repeatable-field-buttons {
    margin-top: 0;
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
registerAPFColorPickerField = function( osTragetInput ) {
    
    var osTargetInput   = typeof osTragetInput === 'string' ? '#' + osTragetInput : osTragetInput;
    var sInputID        = typeof osTragetInput === 'string' ? osTragetInput : osTragetInput.attr( 'id' );
    
    'use strict';
    /* This if statement checks if the color picker element exists within jQuery UI
     If it does exist, then we initialize the WordPress color picker on our text input field */
    if( 'object' === typeof jQuery.wp && 'function' === typeof jQuery.wp.wpColorPicker ){
        var aColorPickerOptions = {
            defaultColor: false, // you can declare a default color here, or in the data-default-color attribute on the input     
            change: function(event, ui){}, // a callback to fire whenever the color changes to a valid color. reference : http://automattic.github.io/Iris/     
            clear: function() {}, // a callback to fire when the input is emptied or an invalid color
            hide: true, // hide the color picker controls on load
            palettes: true // show a group of common colors beneath the square or, supply an array of colors to customize further
        };     
        jQuery( osTargetInput ).wpColorPicker( aColorPickerOptions );
    }
    else {
        /* We use farbtastic if the WordPress color picker widget doesn't exist */
        jQuery( '#color_' + sInputID ).farbtastic( osTargetInput );                   
    }
}

/* The below function will be triggered when a new repeatable field is added. Since the APF repeater script does not
    renew the color piker element (while it does on the input tag value), the renewal task must be dealt here separately. */
jQuery( document ).ready( function(){
    
    jQuery().registerAPFCallback( {     
        added_repeatable_field: function( node, sFieldType, sFieldTagID, sCallType ) {

            /* If it is not the color field type, do nothing. */
            if ( jQuery.inArray( sFieldType, $_aJSArray ) <= -1 ) { 
                return; 
            }
            
            /* If the input tag is not found, do nothing  */
            var nodeNewColorInput = node.find( 'input.input_color' );
            if ( nodeNewColorInput.length <= 0 ) { return; }
            
            var nodeIris = node.find( '.wp-picker-container' ).first();
            // WP 3.5+
            if ( nodeIris.length > 0 ) { 
                // unbind the existing color picker script in case there is.
                var nodeNewColorInput = nodeNewColorInput.clone(); 
            }
            var sInputID = nodeNewColorInput.attr( 'id' );

            /* Reset the value of the color picker */
            var sInputValue = nodeNewColorInput.val() ? nodeNewColorInput.val() : 'transparent'; // For WP 3.4.x or below
            var sInputStyle = sInputValue != 'transparent' && nodeNewColorInput.attr( 'style' ) ? nodeNewColorInput.attr( 'style' ) : '';
            nodeNewColorInput.val( sInputValue ); // set the default value    
            nodeNewColorInput.attr( 'style', sInputStyle ); // remove the background color set to the input field ( for WP 3.4.x or below )  

            /* Replace the old color picker elements with the new one */
            // WP 3.5+
            if ( nodeIris.length > 0 ) { 
                jQuery( nodeIris ).replaceWith( nodeNewColorInput );
            } 
            // WP 3.4.x -     
            else { 
                node.find( '.colorpicker' ).replaceWith( '<div class=\"colorpicker\" id=\"color_' + sInputID + '\"></div>' );    
            }

            /* Bind the color picker script */     
            registerAPFColorPickerField( nodeNewColorInput );     
            
        }
    });
});
JAVASCRIPTS;

    }    
    
    /**
     * Returns the output of the color field.
     * 
     * @since       2.1.5
     * @since       3.0.0     Removed unnecessary parameters.
     * @since       3.3.1     Changed from `_replyToGetField()`.
     */
    protected function getField( $aField ) {

        $aField['attributes'] = array(
            'color' => $aField['value'],    
            'type' => 'text', // it must be text
            'class' => trim( 'input_color ' . $aField['attributes']['class'] ),
        ) + $aField['attributes'];

        return 
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']
                    . ( $aField['label'] && ! $aField['repeatable']
                        ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . $aField['label'] . "</span>"
                        : "" 
                    )
                    . "<input " . $this->generateAttributes( $aField['attributes'] ) . " />" // this method is defined in the base class
                    . $aField['after_input']
                    . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                . "</label>"
                . "<div class='colorpicker' id='color_{$aField['input_id']}'></div>" // this div element with this class selector becomes a farbtastic color picker. ( below 3.4.x ) // rel='{$aField['input_id']}'
                . $this->_getColorPickerEnablerScript( "{$aField['input_id']}" )     
            . "</div>"
            . $aField['after_label'];
        
    }
        /**
         * A helper function for the above getColorField() method to add a script to enable the color picker.
         */
        private function _getColorPickerEnablerScript( $sInputID ) {
            $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function(){
    registerAPFColorPickerField( '{$sInputID}' );
});            
JAVASCRIPTS;
            return
                "<script type='text/javascript' class='color-picker-enabler-script'>"
                    . $_sScript
                . "</script>";
        }    
    
}