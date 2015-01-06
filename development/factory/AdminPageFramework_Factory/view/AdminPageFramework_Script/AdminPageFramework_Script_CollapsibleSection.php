<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides JavaScript utility scripts.
 * 
 * @since       3.4.0
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 */
class AdminPageFramework_Script_CollapsibleSection extends AdminPageFramework_Script_Base {

    /**
     * The user constructor.
     * 
     * @since       3.4.0
     */
    public function construct() {

        wp_enqueue_script( 'juery' );
        wp_enqueue_script( 'juery-ui-accordion' );    
        
    }

    /**
     * Returns the script.
     * 
     * @since       3.4.0
     */
    static public function getScript() {
        
        $_aParams           = func_get_args() + array( null );
        $_oMsg              = $_aParams[ 0 ];        

        $_sLabelToggleAll           = $_oMsg->get( 'toggle_all' );
        $_sLabelToggleAllSections   = $_oMsg->get( 'toggle_all_collapsible_sections' );
        $_sDashIconSort             = version_compare( $GLOBALS['wp_version'], '3.8', '<' ) 
            ? '' 
            : 'dashicons dashicons-sort';
        $_sToggleAllButton          = "<div class='admin-page-framework-collapsible-toggle-all-button-container'>"
                . "<span class='admin-page-framework-collapsible-toggle-all-button button " . $_sDashIconSort. "' title='" . esc_attr( $_sLabelToggleAllSections ) . "'>"
                . ( $_sDashIconSort ? '' : $_sLabelToggleAll )  // text
                . "</span>"
            . "</div>";
        $_sToggleAllButtonHTML  = '"' . $_sToggleAllButton . '"';                

        
        return <<<JAVASCRIPTS
( function( $ ) {

    jQuery( document ).ready( function() {
        
        // Expand collapsible sections that are set not to collapse by default 
        jQuery( '.admin-page-framework-collapsible-sections-title[data-is_collapsed=\"0\"]' )
            .next( '.admin-page-framework-collapsible-sections-content' )
            .slideDown( 'fast' );
        jQuery( '.admin-page-framework-collapsible-section-title[data-is_collapsed=\"0\"]' )
            .closest( '.admin-page-framework-section-table' )
            .find( 'tbody' )
            .slideDown( 'fast' );
        // Hide collapsible sections of 'section' containers as they are somehow do not get collapsed by default.
        jQuery( '.admin-page-framework-collapsible-section-title[data-is_collapsed=\"1\"]' )
            .closest( '.admin-page-framework-section-table' )
            .find( 'tbody' )
            .hide();
        
        // Bind the click event
        jQuery( '.admin-page-framework-collapsible-sections-title, .admin-page-framework-collapsible-section-title' ).enableAPFCollapsibleButton();
        
        // Insert the toggle all button.
        jQuery( '.admin-page-framework-collapsible-title[data-toggle_all_button!=\"0\"]' ).each( function(){
            
            var _oThis        = jQuery( this ); // to access from inside the below each() method.
            var _bForSections = jQuery( this ).hasClass( 'admin-page-framework-collapsible-sections-title' );   // or for the 'section' container.
            var _isPositions  = jQuery( this ).data( 'toggle_all_button' );
            var _isPositions  = 1 === _isPositions
                ? 'top-right'   // default
                : _isPositions;
            var _aPositions   = 'string' === typeof _isPositions
                ? _isPositions.split( ',' )
                : [ 'top-right' ];

            jQuery.each( _aPositions, function( iIndex, _sPosition ) {
         
                var _oButton = jQuery( $_sToggleAllButtonHTML );
                var _sLeftOrRight = -1 !== jQuery.inArray( _sPosition, [ 'top-right', 'bottom-right', '0' ] )   // if found
                    ? 'right'   // default
                    : 'left';            
                _oButton.find( '.admin-page-framework-collapsible-toggle-all-button' ).css( 'float', _sLeftOrRight );

                var _sTopOrBottom = -1 !== jQuery.inArray( _sPosition, [ 'top-right', 'top-left', '0' ] )   // if found
                    ? 'before'   // default
                    : 'after';            
                
                // Insert the button - there are two versions: for the sections container or the section container.
                if ( _bForSections ) {
                    var _oTargetElement = 'before' === _sTopOrBottom
                        ? _oThis
                        : _oThis.next( '.admin-page-framework-collapsible-content' );
                        _oTargetElement[ _sTopOrBottom ]( _oButton );
                } else {    // for 'section' containers
                    _oThis.closest( '.admin-page-framework-section' )[ _sTopOrBottom ]( _oButton );
                }                
                
                // Expand or collapse this panel
                _oButton.click( function(){
                   
                    _oButton.toggleClass( 'flipped' );
                    if ( _oButton.hasClass( 'flipped' ) && _oButton.hasClass( 'dashicons' ) ) {
                        _oButton.css( 'transform', 'rotateY( 180deg )' );
                    } else {
                        _oButton.css( 'transform', '' );
                    }
                    if ( _bForSections ) {
                        _oButton.parent().parent().children().children( '* > .admin-page-framework-collapsible-title' ).each( function() {
                            jQuery( this ).trigger( 'click', [ 'by_toggle_all_button' ] );
                        } );
                    } else {
                        _oButton.closest( '.admin-page-framework-sections' ).children( '.admin-page-framework-section' ).children( '.admin-page-framework-section-table' ).children( 'caption' ).children( '.admin-page-framework-collapsible-title' ).each( function() {
                            jQuery( this ).trigger( 'click', [ 'by_toggle_all_button' ] );
                        } );
                    }
                } );                
                             
            });                  
           
        } );      
                
    });              


    /**
     * Adds a repeatable section.
     */
    $.fn.enableAPFCollapsibleButton = function() {
        
        jQuery( this ).click( function( event, sContext ){

            // Expand or collapse this panel
            var _oThis = jQuery( this );
            var _sContainerType = jQuery( this ).hasClass( 'admin-page-framework-collapsible-sections-title' )
                ? 'sections'
                : 'section';
            var _oTargetContent = 'sections' === _sContainerType
                ? jQuery( this ).next( '.admin-page-framework-collapsible-content' ).first()
                : jQuery( this ).parent().siblings( 'tbody' );
            var _sAction = _oTargetContent.is( ':visible' ) ? 'collapse' : 'expand';

            _oThis.removeClass( 'collapsed' );
            _oTargetContent.slideToggle( 'fast', function(){
                
                // For Google Chrome, table-caption will animate smoothly for the 'section' containers (not 'sections' container). For FireFox, 'block' is required. For IE both works.
                var _bIsChrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
                if ( 'expand' === _sAction && 'section' === _sContainerType && ! _bIsChrome ) {
                    _oTargetContent.css( 'display', 'block' );
                }
                
                // Update the class selector.
                if ( _oTargetContent.is( ':visible' ) ) {
                    _oThis.removeClass( 'collapsed' );
                } else {
                    _oThis.addClass( 'collapsed' );
                }            

            } );
            
            // If it is triggered from the toggle all button, do not continue.
            if ( 'by_toggle_all_button' === sContext ) {
                return;
            }

            // If collapse_others_on_expand argument is true, collapse others 
            if ( 'expand' === _sAction && _oThis.data( 'collapse_others_on_expand' ) ) {
                _oThis.parent().parent().children().children( '* > .admin-page-framework-collapsible-content' ).not( _oTargetContent ).slideUp( 'fast', function() {
                    jQuery( this ).prev( '.admin-page-framework-collapsible-title' ).addClass( 'collapsed' );
                });
            }

        });         
        
        
    }
}( jQuery ));
JAVASCRIPTS;
    }
}