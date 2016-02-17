<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides JavaScript scripts for creating switchable form section tabs.
 * 
 * @since       3.0.0     
 * @since       3.3.0       Extends `AdminPageFramework_Form_View___Script_Base`.
 * @since       3.7.0      Renamed from `AdminPageFramework_Form_View___Script_Tab`.
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 */
class AdminPageFramework_Form_View___Script_SectionTab extends AdminPageFramework_Form_View___Script_Base {
    
    /**
     * Returns an inline JavaScript script.
     * 
     * @since       3.2.0
     * @since       3.3.0       Changed the name from `getjQueryPlugin()`.
     * @param       $oMsg       object      The message object.
     * @return      string      The inline JavaScript script.
     */
    static public function getScript( /* $oMsg */ ) {
        
        // Uncomment these lines when parameters need to be accessed.
        // $_aParams   = func_get_args() + array( null );
        // $_oMsg      = $_aParams[ 0 ];            

        return <<<JAVASCRIPTS
( function( $ ) {
        
    $.fn.createTabs = function( asOptions ) {
        
        var _bIsRefresh = ( typeof asOptions === 'string' && asOptions === 'refresh' );
        if ( typeof asOptions === 'object' ) {
            var aOptions = $.extend( 
                {}, 
                asOptions 
            );
        }
             
        var _sURLHash = 'undefined' !== typeof window.location.hash
            ? window.location.hash
            : '';

        this.children( 'ul' ).each( function () {
                 
            // First, check if the url has a hash that exists in this tab group. 
            // Consider the possibility that multiple tab groups are in one page.
            var _bSetActive = false;
            $( this ).children( 'li' ).each( function( i ) {     
                var sTabContentID = $( this ).children( 'a' ).attr( 'href' );
                if ( '' !== _sURLHash && sTabContentID === _sURLHash ) {
                    _bSetActive = true;
                    return false;
                }
            });
            
            // Second iteration
            $( this ).children( 'li' ).each( function( i ) {     
                
                var sTabContentID = $( this ).children( 'a' ).attr( 'href' );

                // If the url hash is set, compare the content id with it. If it matches, activate it.
                if ( '' !== _sURLHash && sTabContentID === _sURLHash ) {
                    $( this ).addClass( 'active' );
                }
                
                if ( ! _bIsRefresh && ! _bSetActive ) {
                    $( this ).addClass( 'active' );
                    _bSetActive = true;
                }
                
                if ( $( this ).hasClass( 'active' ) ) {
                    $( sTabContentID ).show();
                } else {                           
                    $( sTabContentID ).css( 'display', 'none' );
                }
                
                $( this ).addClass( 'nav-tab' );
                $( this ).children( 'a' ).addClass( 'anchor' );
                
                $( this ).unbind( 'click' ); // for refreshing 
                $( this ).click( function( e ){
                         
                    e.preventDefault(); // Prevents jumping to the anchor which moves the scroll bar.
                    
                    // Remove the active tab and set the clicked tab to be active.
                    $( this ).siblings( 'li.active' ).removeClass( 'active' );
                    $( this ).addClass( 'active' );
                    
                    // Find the element id and select the content element with it.
                    var sTabContentID = $( this ).find( 'a' ).attr( 'href' );
                    var _oActiveContent = $( this ).parent().parent().find( sTabContentID ).css( 'display', 'block' ); 
                    _oActiveContent.siblings( ':not( ul )' ).css( 'display', 'none' );
                    
                });                        

            });

        });      
                                
    };

    
}( jQuery ));
JAVASCRIPTS;
        
    }

    /**
     * Indicates whether the tab enabler script is loaded or not.
     */
    static private $_bLoadedTabEnablerScript = false;
    
    /**
     * Returns the JavaScript script that enables form section tabs.
     * 
     * @since       3.0.0
     * @since       3.6.0       Moved from `AdminPageFramework_FormPart_Table_Base`.
     * @return      string
     */
    static public function getEnabler() {
        
        if ( self::$_bLoadedTabEnablerScript ) {
            return '';
        }
        self::$_bLoadedTabEnablerScript = true;
        
        new self;
        
        $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function() {
// the parent element of the ul tag; The ul element holds li tags of titles.
jQuery( '.admin-page-framework-section-tabs-contents' ).createTabs(); 
});            
JAVASCRIPTS;

        return "<script type='text/javascript' class='admin-page-framework-section-tabs-script'>"
            . '/* <![CDATA[ */'
            . $_sScript
            . '/* ]]> */'
        . "</script>";
        
    }
    
}
