<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * The base class of the form input field class that provides methods for rendering form input fields.
 * 
 * This class mainly handles JavaScript scripts and the constructor setting class properties.
 * 
 * @since       3.0.0       Separated the methods that defines field types to different classes.
 * @since       3.3.1       Extends `AdminPageFramework_FormOutput`.
 * @extends     AdminPageFramework_FormOutput
 * @package     AdminPageFramework
 * @subpackage  Form
 * @internal
 */
class AdminPageFramework_FormField_Base extends AdminPageFramework_FormOutput {
            
    /**
     * Sets up properties and load necessary scripts.
     * 
     * @remark The third parameter should not be by reference as an expression will be passed.
     * 
     * @internal
     * @since       3.0.0
     * @since       3.2.0       Added the $aCallbacks parameter.
     * @since       3.4.1       Removed the reference (&) of the second parameter to let a function call being passed.
     * @param       array       $aField                 An array storing the field definition array.
     * @param       array       $aOptions               An array storing the stored data in the database.
     * @param       array       $aErrors                An array storing the field errors.
     * @param       array       $aFieldTypeDefinitions  An array storing registered field type definitions.
     * @param       object      $oMsg                   An object storing the system messages.
     * @param       array       $aCallbacks             An array storing the form-field specific callbacks.     
     */
    public function __construct( &$aField, $aOptions, $aErrors, &$aFieldTypeDefinitions, &$oMsg, array $aCallbacks=array() ) {

        /* 1. Set up the properties that will be accessed later in the methods. */
        $aFieldTypeDefinition = isset( $aFieldTypeDefinitions[ $aField['type'] ] ) 
            ? $aFieldTypeDefinitions[ $aField['type'] ] 
            : $aFieldTypeDefinitions['default'];
        
        /* 
         * 1-1. Set up the 'attributes' array - the 'attributes' element is dealt separately as it contains some overlapping elements with the regular elements such as 'value'.
         * There are required keys in the attributes array: 'fieldrow', 'fieldset', 'fields', and 'field'; these should not be removed here.
         */
        $aFieldTypeDefinition['aDefaultKeys']['attributes'] = array(    
            'fieldrow'  => $aFieldTypeDefinition['aDefaultKeys']['attributes']['fieldrow'],
            'fieldset'  => $aFieldTypeDefinition['aDefaultKeys']['attributes']['fieldset'],
            'fields'    => $aFieldTypeDefinition['aDefaultKeys']['attributes']['fields'],
            'field'     => $aFieldTypeDefinition['aDefaultKeys']['attributes']['field'],
        );    
        $this->aField = $this->uniteArrays( $aField, $aFieldTypeDefinition['aDefaultKeys'] );
        
        /* 1-2. Set the other properties */
        $this->aFieldTypeDefinitions    = $aFieldTypeDefinitions;
        $this->aOptions                 = $aOptions;
        $this->aErrors                  = $aErrors ? $aErrors : array();
        $this->oMsg                     = $oMsg;
        $this->aCallbacks               = $aCallbacks + array(
            'hfID'          => null,    // the input id attribute
            'hfTagID'       => null,    // the fieldset/field row container id attribute
            'hfName'        => null,    // the input name attribute
            'hfNameFlat'    => null,    // the flat input name attribute                
            'hfClass'       => null,    // the class attribute
        );        
        
        /* 2. Load necessary JavaScript scripts */
        $this->_loadScripts( $this->aField['_fields_type'] );
        
    }    
        /**
         * Flags whether scripts are loaded or not.
         * 
         * @since   3.2.0
         */
        static private $_bIsLoadedSScripts          = false;
        static private $_bIsLoadedSScripts_Widget   = false;
        
        /**
         * Inserts necessary JavaScript scripts for fields.
         * 
         * @since   3.0.0
         * @since   3.2.0   Added the $sFieldsType parameter.
         * @internal
         */
        private function _loadScripts( $sFieldsType='' ) {

            if ( 'widget' === $sFieldsType && ! self::$_bIsLoadedSScripts_Widget ) {
                new AdminPageFramework_Script_Widget;
                self::$_bIsLoadedSScripts_Widget = true;
            }
            
            if ( self::$_bIsLoadedSScripts ) { return; }
            self::$_bIsLoadedSScripts = true;
            
            new AdminPageFramework_Script_Utility;
            new AdminPageFramework_Script_OptionStorage;
            new AdminPageFramework_Script_AttributeUpdator;
            new AdminPageFramework_Script_RepeatableField( $this->oMsg );
            new AdminPageFramework_Script_Sortable;
            new AdminPageFramework_Script_RegisterCallback;
                        
        }
    
    /**
     * Returns the repeatable fields script.
     * 
     * @since 2.1.3
     */
    protected function _getRepeaterFieldEnablerScript( $sFieldsContainerID, $iFieldCount, $aSettings ) {

        $_sAdd                  = $this->oMsg->get( 'add' );
        $_sRemove               = $this->oMsg->get( 'remove' );
        $_sVisibility           = $iFieldCount <= 1 ? " style='visibility: hidden;'" : "";
        $_sSettingsAttributes   = $this->generateDataAttributes( ( array ) $aSettings );
        $_bDashiconSupported    = false;     // version_compare( $GLOBALS['wp_version'], '3.8', '>=' );
        $_sDashiconPlus         = $_bDashiconSupported ? 'dashicons dashicons-plus' : '';
        $_sDashiconMinus        = $_bDashiconSupported ? 'dashicons dashicons-minus' : '';
        $_sButtons              = 
            "<div class='admin-page-framework-repeatable-field-buttons' {$_sSettingsAttributes} >"
                . "<a class='repeatable-field-remove button-secondary repeatable-field-button button button-small {$_sDashiconMinus}' href='#' title='{$_sRemove}' {$_sVisibility} data-id='{$sFieldsContainerID}'>"
                  . ( $_bDashiconSupported ? '' : '-' )
                . "</a>"
                . "<a class='repeatable-field-add button-secondary repeatable-field-button button button-small {$_sDashiconPlus}' href='#' title='{$_sAdd}' data-id='{$sFieldsContainerID}'>" 
                    . ( $_bDashiconSupported ? '' : '+' )
                . "</a>"                
            . "</div>";
        $_aJSArray              = json_encode( $aSettings );
        $_sButtonsHTML          = '"' . $_sButtons . '"';
        $_sScript               = <<<JAVASCRIPTS
jQuery( document ).ready( function() {
    var _nodePositionIndicators = jQuery( '#{$sFieldsContainerID} .admin-page-framework-field .repeatable-field-buttons' );
    /* If the position of inserting the buttons is specified in the field type definition, replace the pointer element with the created output */
    if ( _nodePositionIndicators.length > 0 ) {
        _nodePositionIndicators.replaceWith( $_sButtonsHTML );
    } else { 
    /* Otherwise, insert the button element at the beginning of the field tag */
        // check the button container already exists for WordPress 3.5.1 or below
        if ( ! jQuery( '#{$sFieldsContainerID} .admin-page-framework-repeatable-field-buttons' ).length ) { 
            // Adds the buttons
            jQuery( '#{$sFieldsContainerID} .admin-page-framework-field' ).prepend( $_sButtonsHTML ); 
        }
    }     
    jQuery( '#{$sFieldsContainerID}' ).updateAPFRepeatableFields( $_aJSArray ); // Update the fields     
});
JAVASCRIPTS;
        return "<script type='text/javascript'>" . $_sScript . "</script>";
        
    }
    
    /**
     * Returns the sortable fields script.
     * 
     * @since 3.0.0
     */    
    protected function _getSortableFieldEnablerScript( $sFieldsContainerID ) {        
    
        $_sScript = <<<JAVASCRIPTS
    jQuery( document ).ready( function() {
        jQuery( this ).enableAPFSortable( '$sFieldsContainerID' );
    });
JAVASCRIPTS;
        return "<script type='text/javascript' class='admin-page-framework-sortable-field-enabler-script'>"
                . $_sScript
            . "</script>";
            
    }
        
}