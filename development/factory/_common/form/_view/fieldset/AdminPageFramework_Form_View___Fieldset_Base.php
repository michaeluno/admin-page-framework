<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2018, Michael Uno; Licensed MIT
 * 
 */

/**
 * The base class of the form input field class that provides methods for rendering form input fields.
 * 
 * This class mainly handles JavaScript scripts and the constructor setting class properties.
 * 
 * @since       3.0.0       Separated the methods that defines field types to different classes.
 * @since       3.3.1       Extends `AdminPageFramework_FormOutput`.
 * @since       3.6.0       Extends `AdminPageFramework_WPUtility`.
 * @extends     AdminPageFramework_FrameworkUtility
 * @package     AdminPageFramework/Common/Form/View/Field
 * @internal
 */
abstract class AdminPageFramework_Form_View___Fieldset_Base extends AdminPageFramework_Form_Utility {

    /**
     * Stores the field definition array.
     */
    public $aFieldset = array();
    
    /**
     * Stores field type definitions.
     */
    public $aFieldTypeDefinitions = array();
        
    /**
     * Stores stored options in the database.
     */
    public $aOptions = array();

    /**
     * Stores field error messages.
     */
    public $aErrors = array();
    
    /**
     * Stores the message object
     */
    public $oMsg;
    
    /**
     * Stores callables.
     */
    public $aCallbacks = array();
            
    /**
     * Sets up properties and load necessary scripts.
     * 
     * @remark The third parameter should not be by reference as an expression will be passed.
     * 
     * @internal
     * @since       3.0.0
     * @since       3.2.0       Added the $aCallbacks parameter.
     * @since       3.4.1       Removed the reference (&) of the second parameter to let a function call being passed.
     * @param       array       $aFieldset              An array storing the field definition array.
     * @param       array       $aOptions               An array storing the stored data in the database.
     * @param       array       $aErrors                An array storing the field errors.
     * @param       array       $aFieldTypeDefinitions  An array storing registered field type definitions.
     * @param       object      $oMsg                   An object storing the system messages.
     * @param       array       $aCallbacks             An array storing the form-field specific callbacks.     
     */
    public function __construct( $aFieldset, $aOptions, $aErrors, &$aFieldTypeDefinitions, &$oMsg, array $aCallbacks=array() ) {

        // Set up the properties that will be accessed later in the methods.
        $this->aFieldset                = $this->_getFormatted( $aFieldset, $aFieldTypeDefinitions );
        $this->aFieldTypeDefinitions    = $aFieldTypeDefinitions;
        $this->aOptions                 = $aOptions;
        $this->aErrors                  = $this->getAsArray( $aErrors );
        $this->oMsg                     = $oMsg;
        $this->aCallbacks               = $aCallbacks + array(
            'hfID'              => null,    // the input id attribute
            'hfTagID'           => null,    // the fieldset/field row container id attribute
            'hfName'            => null,    // the input name attribute
            'hfNameFlat'        => null,    // the flat input name attribute                
            'hfInputName'       => null,
            'hfInputNameFlat'   => null,
            'hfClass'           => null,    // the class attribute
        );        
        
        // 2. Load necessary JavaScript scripts.
        $this->_loadScripts( $this->aFieldset[ '_structure_type' ] );

    }    
        /**
         * @return      3.6.3
         * @return      array       The formatted fieldset definition array
         */
        private function _getFormatted( $aFieldset, $aFieldTypeDefinitions ) {
            return $this->uniteArrays( 
                $aFieldset, 
                $this->_getFieldTypeDefaultArguments( 
                    $aFieldset[ 'type' ], 
                    $aFieldTypeDefinitions 
                ) + AdminPageFramework_Form_Model___Format_Fieldset::$aStructure
            );
        }    
        
        /**
         * 
         * @since       3.6.0
         * @return      array
         */
        private function _getFieldTypeDefaultArguments( $sFieldType, $aFieldTypeDefinitions ) {
                
            // Extract the field type arguments from the field type definitions array.
            $_aFieldTypeDefinition = $this->getElement(
                $aFieldTypeDefinitions,
                $sFieldType,
                $aFieldTypeDefinitions[ 'default' ]
            );
            
            $_aDefaultKeys = $this->getAsArray( $_aFieldTypeDefinition[ 'aDefaultKeys' ] );
            
            /* 
             * Attributes - the 'attributes' element is dealt separately as it contains some overlapping elements with the regular elements such as 'value'.
             * There are required keys in the attributes array: 'fieldrow', 'fieldset', 'fields', and 'field'; these should not be removed here.
             */
            $_aDefaultKeys[ 'attributes' ] = array(    
                'fieldrow'  => $_aDefaultKeys[ 'attributes' ][ 'fieldrow' ],
                'fieldset'  => $_aDefaultKeys[ 'attributes' ][ 'fieldset' ],
                'fields'    => $_aDefaultKeys[ 'attributes' ][ 'fields' ],
                'field'     => $_aDefaultKeys[ 'attributes' ][ 'field' ],
            );                
            
            return $_aDefaultKeys;
            
        }    
    
        /**
         * Flags whether scripts are loaded or not.
         * 
         * @since   3.2.0
         */
        static private $_bIsLoadedJSScripts          = false;
        static private $_bIsLoadedJSScripts_Widget   = false;
        
        /**
         * Inserts necessary JavaScript scripts for fields.
         * 
         * @since   3.0.0
         * @since   3.2.0   Added the `$sFieldsType` parameter.
         * @internal
         */
        private function _loadScripts( $sStructureType='' ) {

            if ( 'widget' === $sStructureType && ! self::$_bIsLoadedJSScripts_Widget ) {
                new AdminPageFramework_Form_View___Script_Widget;
                self::$_bIsLoadedJSScripts_Widget = true;
            }
            
            if ( self::$_bIsLoadedJSScripts ) { 
                return; 
            }
            self::$_bIsLoadedJSScripts = true;
            
            new AdminPageFramework_Form_View___Script_Utility;
            new AdminPageFramework_Form_View___Script_OptionStorage;
            new AdminPageFramework_Form_View___Script_AttributeUpdator;
            new AdminPageFramework_Form_View___Script_RepeatableField( $this->oMsg );
            new AdminPageFramework_Form_View___Script_SortableField;
                        
        }
    
    /**
     * Returns the repeatable fields script.
     * 
     * @since 2.1.3
     */
    protected function _getRepeaterFieldEnablerScript( $sFieldsContainerID, $iFieldCount, $aSettings ) {
        
        $_sSmallButtons         = '"' . $this->___getRepeatableButtonHTML( $sFieldsContainerID, ( array ) $aSettings, $iFieldCount, true ) . '"';
        $_sNestedFieldsButtons  = '"' . $this->___getRepeatableButtonHTML( $sFieldsContainerID, ( array ) $aSettings, $iFieldCount, false ) . '"';
        $_aJSArray              = json_encode( $aSettings );
        $_sScript               = <<<JAVASCRIPTS
jQuery( document ).ready( function() {
    var _oButtonPlaceHolders = jQuery( '#{$sFieldsContainerID} > .admin-page-framework-field.without-child-fields .repeatable-field-buttons' );
    /* If the button place-holder is set in the field type definition, replace it with the created output */
    if ( _oButtonPlaceHolders.length > 0 ) {
        _oButtonPlaceHolders.replaceWith( $_sSmallButtons );
    } 
    /* Otherwise, insert the button element at the beginning of the field tag */
    else { 
        /**
         * Adds the buttons
         * Check whether the button container already exists for WordPress 3.5.1 or below.
         * @todo 3.8.0 Examine the below conditional line whether the behavior does not break for nested fields.
         */
        if ( ! jQuery( '#{$sFieldsContainerID} .admin-page-framework-repeatable-field-buttons' ).length ) { 
            jQuery( '#{$sFieldsContainerID} > .admin-page-framework-field.without-nested-fields' ).prepend( $_sSmallButtons );
        }
        /**
         * Support for nested fields.
         * For nested fields, add the buttons to the fields tag.
         */
        jQuery( '#{$sFieldsContainerID} > .admin-page-framework-field.with-nested-fields' ).prepend( $_sNestedFieldsButtons );
        
        /**
         * Support for inline mixed fields.
         */
        // jQuery( '#{$sFieldsContainerID} > .admin-page-framework-field.with-mixed-fields' ).prepend( $_sNestedFieldsButtons );
        
    }     
    jQuery( '#{$sFieldsContainerID}' ).updateAdminPageFrameworkRepeatableFields( $_aJSArray ); // Update the fields     
});
JAVASCRIPTS;
        return "<script type='text/javascript'>" 
                . '/* <![CDATA[ */'
                . $_sScript 
                . '/* ]]> */'
            . "</script>";
        
    }
        /**
         * Creates an HTML button output for repeatable field buttons.
         * @since       3.8.0
         * @return      string
         */
        private function ___getRepeatableButtonHTML( $sFieldsContainerID, array $aArguments, $iFieldCount, $bSmall=true ) {

            // @todo Move this formatting routine to the field-set formatter class.
            $_oFormatter             = new AdminPageFramework_Form_Model___Format_RepeatableField( $aArguments, $this->oMsg );
            $_aArguments             = $_oFormatter->get();
            $_sSmallButtonSelector   = $bSmall ? ' button-small' : '';
            return "<div " . $this->___getContainerAttributes( $_aArguments ) . " >"
                    . "<a " . $this->___getRemvoeButtonAttribtes( $sFieldsContainerID, $_sSmallButtonSelector, $iFieldCount ) . ">-</a>"
                    . "<a " . $this->___getAddButtonAttribtes( $_aArguments, $sFieldsContainerID, $_sSmallButtonSelector ) . ">+</a>"
                . "</div>"
                . $this->getModalForDisabledRepeatableElement(
                    'repeatable_field_disabled_' . $sFieldsContainerID,
                    $_aArguments[ 'disabled' ]
                );

        }

            /**
             * @since       3.8.13
             * @param       array       $aArguments
             * @return      string
             */
            private function ___getAddButtonAttribtes( $aArguments, $sFieldsContainerID, $sSmallButtonSelector ) {
                $_sPlusButtonAttributes = array(
                    'class'     => 'repeatable-field-add-button button-secondary repeatable-field-button button'
                        . $sSmallButtonSelector,
                    'title'     => $this->oMsg->get( 'add' ),
                    'data-id'   => $sFieldsContainerID,
                    'href'      => empty( $aArguments[ 'disabled' ] )
                        ? null
                        : '#TB_inline?width=' . $aArguments[ 'disabled' ][ 'box_width' ]
                          . '&height=' . $aArguments[ 'disabled' ][ 'box_height' ]
                          . '&inlineId=' . 'repeatable_field_disabled_' . $sFieldsContainerID,
                );
                return $this->getAttributes( $_sPlusButtonAttributes );
            }
            /**
             * @since       3.8.13
             * @param       array       $aArguments
             * @return      string
             */
            private function ___getRemvoeButtonAttribtes( $sFieldsContainerID, $sSmallButtonSelector, $iFieldCount ) {
                $_aMinusButtonAttributes = array(
                    'class'     => 'repeatable-field-remove-button button-secondary repeatable-field-button button'
                         . $sSmallButtonSelector,
                    'title'     => $this->oMsg->get( 'remove' ),
                    'style'     => $iFieldCount <= 1
                        ? 'visibility: hidden'
                        : null,
                    'data-id'   => $sFieldsContainerID,
                );
                return $this->getAttributes( $_aMinusButtonAttributes );
            }
            /**
             * @since       3.8.13
             * @param       $aArguments
             * @return      string
             */
            private function ___getContainerAttributes( $aArguments ) {
                $_aContainerAttributes   = array(
                    'class' => $this->getClassAttribute(
                        'admin-page-framework-repeatable-field-buttons',
                        ! empty( $aArguments[ 'disabled' ] )
                            ? 'disabled'
                            : ''
                    ),
                );
                unset(
                    $aArguments[ 'disabled' ][ 'message' ] // this element can contain HTML tags.
                );
                if ( empty( $aArguments[ 'disabled' ] ) ) {
                    // if it is an empty array, it must be removed as the data attributes will be checked in the JavaScript script.
                    unset( $aArguments[ 'disabled' ] );
                }
                return $this->getAttributes( $_aContainerAttributes )
                    . ' ' . $this->getDataAttributes( $aArguments );
            }

    /**
     * Returns the sortable fields script.
     * 
     * @since 3.0.0
     */    
    protected function _getSortableFieldEnablerScript( $sFieldsContainerID ) {        
    
        $_sScript = <<<JAVASCRIPTS
    jQuery( document ).ready( function() {
        jQuery( this ).enableAdminPageFrameworkSortableFields( '$sFieldsContainerID' );
    });
JAVASCRIPTS;
        return "<script type='text/javascript' class='admin-page-framework-sortable-field-enabler-script'>"
                . '/* <![CDATA[ */'
                . $_sScript
                . '/* ]]> */'
            . "</script>";
            
    }
        
}
