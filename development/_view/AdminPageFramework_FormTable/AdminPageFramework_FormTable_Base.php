<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FormTable_Base' ) ) :
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
         * @since   3.3.4
         */
        static private $_bLoadedCollapsibleSectionsEnablerScript = false;
        
        /**
         * Returns the enabler script of collapsible sections.
         * @since   3.3.4
         */
        protected function _getCollapsibleSectionsEnablerScript() {
            
            if ( self::$_bLoadedCollapsibleSectionsEnablerScript ) {
                return;
            }
            self::$_bLoadedCollapsibleSectionsEnablerScript = true;
            // new AdminPageFramework_Script_CollapsibleSection( $this->oMsg );   
            
            $_sLabelToggleAll           = $this->oMsg->get( 'toggle_all' );
            $_sLabelToggleAllSections   = $this->oMsg->get( 'toggle_all_collapsible_sections' );
            $_sDashIconSort             = version_compare( $GLOBALS['wp_version'], '3.8', '<' ) 
                ? '' 
                : 'dashicons dashicons-sort';
            $_sToggleAllButton          = "<div class='admin-page-framework-collapsible-sections-toggle-all-button-container'>"
                    . "<span class='admin-page-framework-collapsible-sections-toggle-all-button button " . $_sDashIconSort. "' title='" . esc_attr( $_sLabelToggleAllSections ) . "'>"
                    . ( $_sDashIconSort ? '' : $_sLabelToggleAll )  // text
                    . "</span>"
                . "</div>";
            $_sToggleAllButtonHTML  = '"' . $_sToggleAllButton . '"';                
            wp_enqueue_script( 'juery' );
            wp_enqueue_script( 'juery-ui-accordion' );
            $_sScript       = <<<JAVASCRIPTS
jQuery( document ).ready( function() {
    
    jQuery( '.admin-page-framework-collapsible-sections-title[data-is_collapsed=\"0\"]' )
        .next( '.admin-page-framework-collapsible-sections' )
        .slideDown( 'fast' );
    jQuery( '.admin-page-framework-collapsible-sections-title' ).click( function( event, sContext ){

        // Expand or collapse this panel
        var _oThis = jQuery( this );
        var _oTargetSections = jQuery( this ).next( '.admin-page-framework-collapsible-sections' );
        
        _oThis.removeClass( 'collapsed' );
        _oTargetSections.slideToggle( 'fast', function(){
            if ( _oTargetSections.is( ':visible' ) ) {
                _oThis.removeClass( 'collapsed' );
            } else {
                _oThis.addClass( 'collapsed' );
            }            
        } );
        
        // If it is triggred from the toglle all button, do not continue.
        if ( 'by_toggle_all_button' === sContext ) {
            return;
        }
        
        // If collapse_others_on_expand argument is true, collapse others 
        if ( _oThis.data( 'collapse_others_on_expand' ) ) {
            jQuery( '.admin-page-framework-collapsible-sections' ).not( _oTargetSections ).slideUp( 'fast', function() {
                jQuery( this ).prev( '.admin-page-framework-collapsible-sections-title' ).addClass( 'collapsed' );
            });
        }

    }); 
    
    // Insert the toggle all button.
    jQuery( '.admin-page-framework-collapsible-sections-title[data-show_toggle_all_button!=\"0\"]' ).each( function(){
        
        // var _oButton = jQuery( '<div class=\"admin-page-framework-collapsible-sections-toggle-all-button-container\"><span class=\"admin-page-framework-collapsible-sections-toggle-all-button button dashicons dashicons-sort\"></span></div>' );
        var _oButton = jQuery( $_sToggleAllButtonHTML );
        jQuery( this ).before( _oButton );
        var _sLeftOrRight = 0 === jQuery( this ).data( 'show_toggle_all_button' ) || 'left' !== jQuery( this ).data( 'show_toggle_all_button' )
            ? 'right'
            : 'left';
        _oButton.find( '.admin-page-framework-collapsible-sections-toggle-all-button' ).css( 'float', _sLeftOrRight );
    
        // Expand or collapse this panel
        _oButton.click( function(){
            var _oButton = jQuery( this ).find( '.admin-page-framework-collapsible-sections-toggle-all-button' );
            _oButton.toggleClass( 'flipped' );
            if ( _oButton.hasClass( 'flipped' ) && _oButton.hasClass( 'dashicons' ) ) {
                _oButton.css( 'transform', 'rotateY( 180deg )' );
            } else {
                _oButton.css( 'transform', '' );
            }
            jQuery( '.admin-page-framework-collapsible-sections-title' ).each( function() {
                jQuery( this ).trigger( 'click', [ 'by_toggle_all_button' ] );   
            } );
        } );
    } );      

});               
JAVASCRIPTS;
            return "<script type='text/javascript' class='admin-page-framework-section-collapsible-script'>" . $_sScript . "</script>";
            
        }
        
        /**
         * Returns the enabler script of repeatable sections.
         * @since   3.0.0
         */
        protected function _getRepeatableSectionsEnablerScript( $sContainerTagID, $iSectionCount, $aSettings ) {
            
            new AdminPageFramework_Script_RepeatableSection( $this->oMsg );
            
            if ( empty( $aSettings ) ) { return ''; }
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
    jQuery( '#{$sContainerTagID} .admin-page-framework-section-caption' ).show().prepend( $_sButtonsHTML );
    // Update the fields     
    jQuery( '#{$sContainerTagID}' ).updateAPFRepeatableSections( $_aJSArray ); 
});            
JAVASCRIPTS;
            return "<script type='text/javascript' class='admin-page-framework-seciton-repeatable-script'>" . $_sScript . "</script>";
                
        }
 
}
endif;