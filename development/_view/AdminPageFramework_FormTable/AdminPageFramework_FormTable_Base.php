<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * The base class of the form table class that provides methods to render setting sections and fields.
 * 
 * This base class mainly deals with setting properties in the constructor and internal methods. 
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.0.0
 * @internal
 */
class AdminPageFramework_FormTable_Base extends AdminPageFramework_FormOutput {
    
    /**
     * Sets up properties and hooks.
     * 
     * @since 3.0.0
     * @since 3.0.4 The $aFieldErrors parameter was added.
     */
    public function __construct( $aFieldTypeDefinitions, array $aFieldErrors, $oMsg=null ) {
        
        $this->aFieldTypeDefinitions    = $aFieldTypeDefinitions; // used to merge the field definition array with the default field type definition. 
        $this->aFieldErrors             = $aFieldErrors;
        $this->oMsg                     = $oMsg ? $oMsg: AdminPageFramework_Message::getInstance();
        
        $this->_loadScripts();
        
    }
        
        /**
         * Indicates whether the tab JavaScript plugin is loaded or not.
         */
        static private $_bIsLoadedTabPlugin;
        
        /**
         * Inserts necessary JavaScript scripts for fields.
         * 
         * @since       3.0.0
         * @internal
         */ 
        private function _loadScripts() {
            
            if ( self::$_bIsLoadedTabPlugin ) { return; }
            self::$_bIsLoadedTabPlugin = true;
            new AdminPageFramework_Script_Tab;
            
        }
       
    /**
     * Returns the section title output.
     * 
     * @since       3.0.0
     * @since       3.4.0   Moved from `AdminPageFramework_FormTable`.
     */
    protected function _getSectionTitle( $sTitle, $sTag, $aFields, $hfFieldCallback ) {
        
        $_aSectionTitleField = $this->_getSectionTitleField( $aFields );
        return $_aSectionTitleField
            ? call_user_func_array( $hfFieldCallback, array( $_aSectionTitleField ) )
            : "<{$sTag}>" . $sTitle . "</{$sTag}>";
        
    }    
        /**
         * Returns the first found `section_title` field.
         * 
         * @since       3.0.0
         * @since       3.4.0       Moved from `AdminPageFramework_FormTable`.
         */
        private function _getSectionTitleField( array $aFields ) {   
            foreach( $aFields as $_aField ) {
                if ( 'section_title' === $_aField['type'] ) {
                    return $_aField; // will return the first found one.
                }
            }
        }
    
    /**
     * Returns the collapsible argument array from the given sections definition array.
     * 
     * @since   3.4.0
     */
    protected function _getCollapsibleArgument( array $aSections=array(), $iSectionIndex=0 ) {
        
        // Only the first found item is needed
        foreach( $aSections as $_aSection ) {
            if ( ! isset( $_aSection['collapsible'] ) ) { 
                continue; 
            }
            if ( empty( $_aSection['collapsible'] ) ) {
                return array();
            }
            
            $_aSection['collapsible']['toggle_all_button'] = $this->_sanitizeToggleAllButtonArgument( $_aSection['collapsible']['toggle_all_button'], $_aSection );

            return $_aSection['collapsible'];
        }
        return array();
        
    }       
        /**
         * Sanitizes the toggle all button argument.
         * @since       3.4.0
         * @param       string      $sToggleAll         Comma delimited button positions.
         * @param       array       $aSection           The section definition array.
         */
        private function _sanitizeToggleAllButtonArgument( $sToggleAll, array $aSection ) {
            
            if ( ! $aSection['repeatable'] ) {            
                return $sToggleAll;
            }
            
            // If the both first index and last index is true, it means there is only one section. Treat it as a single non-repeatable section.
            if ( $aSection['_is_first_index'] && $aSection['_is_last_index'] ) {
                return $sToggleAll;
            }
            
            // Disable the toggle all button for middle sub-sections in repeatable sections.
            if ( ! $aSection['_is_first_index'] && ! $aSection['_is_last_index'] ) {
                return 0;
            }            
            
            $_aToggleAll = true === $sToggleAll || 1 ===  $sToggleAll 
                ? array( 'top-right', 'bottom-right' )
                : explode( ',', $sToggleAll );
            
            if ( $aSection['_is_first_index'] ) {                
                $_aToggleAll = $this->dropElementByValue( $_aToggleAll, array( 1, true, 0, false, 'bottom-right', 'bottom-left' ) );
            }
            if ( $aSection['_is_last_index'] ) {
                $_aToggleAll = $this->dropElementByValue( $_aToggleAll, array( 1, true, 0, false, 'top-right', 'top-left' ) );                    
            } 
            $_aToggleAll = empty( $_aToggleAll ) ? array( 0 ) : $_aToggleAll;
            return implode( ',', $_aToggleAll );
            
        }
    /**
     * Returns the output of a title block of the given collapsible section.
     * 
     * @since       3.4.0
     * @param       array|boolean   $aCollapsible       The collapsible argument.
     * @param       string          $sContainer          The position context. Accepts either 'sections' or 'section'. If the set position in the argument array does not match this value, the method will return an empty string.
     */
    protected function _getCollapsibleSectionTitleBlock( array $aCollapsible, $sContainer='sections', array $aFields=array(), $hfFieldCallback=null ) {

        if ( empty( $aCollapsible ) ) { return ''; }
        if ( $sContainer !== $aCollapsible['container'] ) { return ''; }
        
        return $this->_getCollapsibleSectionsEnablerScript()
            . "<div " . $this->generateAttributes(
                array(
                    'class' => $this->generateClassAttribute( 
                        'admin-page-framework-section-title',
                        'accordion-section-title',
                        'admin-page-framework-collapsible-title',
                        'sections' === $aCollapsible['container']
                            ? 'admin-page-framework-collapsible-sections-title'
                            : 'admin-page-framework-collapsible-section-title',
                        $aCollapsible['is_collapsed'] ? 'collapsed' : ''
                    ),
                ) 
                + $this->getDataAttributeArray( $aCollapsible )
            ) . ">"  
                    . $this->_getSectionTitle( $aCollapsible['title'], 'h3', $aFields, $hfFieldCallback )
                . "</div>";
        
    }    
    
       
    /**
     * Indicates whether the tab enabler script is loaded or not.
     */
    static private $_bLoadedTabEnablerScript = false;
    
    /**
     * Returns the JavaScript script that enables section tabs.
     * 
     * @since 3.0.0
     */
    protected function _getSectionTabsEnablerScript() {
        
        if ( self::$_bLoadedTabEnablerScript ) { return ''; }
        self::$_bLoadedTabEnablerScript = true;
        $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function() {
// the parent element of the ul tag; The ul element holds li tags of titles.
jQuery( '.admin-page-framework-section-tabs-contents' ).createTabs(); 
});            
JAVASCRIPTS;
        return "<script type='text/javascript' class='admin-page-framework-section-tabs-script'>"
            . $_sScript
        . "</script>";
        
    } 
 
    /**
     * Indicates whether the collapsible script is loaded or not.
     * 
     * @since   3.4.0
     */
    static private $_bLoadedCollapsibleSectionsEnablerScript = false;
    
    /**
     * Returns the enabler script of collapsible sections.
     * @since   3.4.0
     */
    protected function _getCollapsibleSectionsEnablerScript() {
        
        if ( self::$_bLoadedCollapsibleSectionsEnablerScript ) {
            return;
        }
        self::$_bLoadedCollapsibleSectionsEnablerScript = true;
        new AdminPageFramework_Script_CollapsibleSection( $this->oMsg );     
   
    }
        
    /**
     * Stores the set container IDs to prevent multiple calls.
     * 
     * Collapsible and tabbed sections can call this method multiple times with the same container ID.
     * 
     * @since       3.4.0
     */
    static private $_aSetContainerIDsForRepeatableSections = array();
    /**
     * Returns the enabler script of repeatable sections.
     * @since       3.0.0
     * @since       3.4.0       Moved from `AdminPageFramework_FormTable`.
     */
    protected function _getRepeatableSectionsEnablerScript( $sContainerTagID, $iSectionCount, $aSettings ) {
        
        if ( empty( $aSettings ) ) { return ''; }
        if ( in_array( $sContainerTagID, self::$_aSetContainerIDsForRepeatableSections ) ) { return ''; }
        self::$_aSetContainerIDsForRepeatableSections[ $sContainerTagID ] = $sContainerTagID;
        
        new AdminPageFramework_Script_RepeatableSection( $this->oMsg );
        $aSettings              = $this->getAsArray( $aSettings ) + array( 'min' => 0, 'max' => 0 ); 
        $_sAdd                  = $this->oMsg->get( 'add_section' );
        $_sRemove               = $this->oMsg->get( 'remove_section' );
        $_sVisibility           = $iSectionCount <= 1 ? " style='display:none;'" : "";
        $_sSettingsAttributes   = $this->generateDataAttributes( $aSettings );
        $_sButtons              = 
            "<div class='admin-page-framework-repeatable-section-buttons' {$_sSettingsAttributes} >"
                . "<a class='repeatable-section-remove button-secondary repeatable-section-button button button-large' href='#' title='{$_sRemove}' {$_sVisibility} data-id='{$sContainerTagID}'>-</a>"
                . "<a class='repeatable-section-add button-secondary repeatable-section-button button button-large' href='#' title='{$_sAdd}' data-id='{$sContainerTagID}'>+</a>"
            . "</div>";
        $_sButtonsHTML  = '"' . $_sButtons . '"';
        $_aJSArray      = json_encode( $aSettings );
        $_sScript       = <<<JAVASCRIPTS
jQuery( document ).ready( function() {
    // Adds the buttons
    jQuery( '#{$sContainerTagID} .admin-page-framework-section-caption' ).each( function(){
        
        jQuery( this ).show();
        
        var _oButtons = jQuery( $_sButtonsHTML );
        if ( jQuery( this ).children( '.admin-page-framework-collapsible-section-title' ).children( 'fieldset' ).length > 0 ) {
            _oButtons.addClass( 'section_title_field_sibling' );
        }
        var _oCollapsibleSectionTitle = jQuery( this ).find( '.admin-page-framework-collapsible-section-title' );
        if ( _oCollapsibleSectionTitle.length ) {
            _oButtons.find( '.repeatable-section-button' ).removeClass( 'button-large' );
            _oCollapsibleSectionTitle.prepend( _oButtons );
        } else {
            jQuery( this ).prepend( _oButtons );
        }
        
    } );
    // Update the fields     
    jQuery( '#{$sContainerTagID}' ).updateAPFRepeatableSections( $_aJSArray ); 
});            
JAVASCRIPTS;
        return "<script type='text/javascript' class='admin-page-framework-seciton-repeatable-script'>" . $_sScript . "</script>";
            
    }
 
}