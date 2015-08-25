<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
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
 * @since       3.6.0       Changed the name from `AdminPageFramework_FormTable_Base`.
 * @internal
 */
class AdminPageFramework_FormPart_Table_Base extends AdminPageFramework_WPUtility {
    
    /**
     * Sets up properties and hooks.
     * 
     * @since 3.0.0
     * @since 3.0.4     The `$aFieldErrors` parameter was added.
     */
    public function __construct( $aFieldTypeDefinitions, array $aFieldErrors, $oMsg=null ) {
        
        $this->aFieldTypeDefinitions    = $aFieldTypeDefinitions; // used to merge the field definition array with the default field type definition. 
        $this->aFieldErrors             = $aFieldErrors;
        $this->oMsg                     = $oMsg 
            ? $oMsg
            : AdminPageFramework_Message::getInstance();
        
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
            
            if ( self::$_bIsLoadedTabPlugin ) { 
                return; 
            }
            self::$_bIsLoadedTabPlugin = true;
            new AdminPageFramework_Script_Tab;
            
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
     * @since       3.4.0       Moved from `AdminPageFramework_FormPart_Table`.
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
                . "<a class='repeatable-section-remove-button button-secondary repeatable-section-button button button-large' href='#' title='{$_sRemove}' {$_sVisibility} data-id='{$sContainerTagID}'>-</a>"
                . "<a class='repeatable-section-add-button button-secondary repeatable-section-button button button-large' href='#' title='{$_sAdd}' data-id='{$sContainerTagID}'>+</a>"
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
    jQuery( '#{$sContainerTagID}' ).updateAdminPageFrameworkRepeatableSections( $_aJSArray ); 
});            
JAVASCRIPTS;
        return "<script type='text/javascript' class='admin-page-framework-seciton-repeatable-script'>" . $_sScript . "</script>";
            
    }

}