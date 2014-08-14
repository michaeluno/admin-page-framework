<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Script_Tab' ) ) :
/**
 * Provides JavaScript scripts for creating switchable tabs.
 * 
 * @since 3.0.0     
 * @package AdminPageFramework
 * @subpackage JavaScript
 * @internal
 */
class AdminPageFramework_Script_Tab {

    static public function getjQueryPlugin() {
        
        return "( function( $ ) {
            
            $.fn.createTabs = function( asOptions ) {
                
                bIsRefresh = ( typeof asOptions === 'string' && asOptions === 'refresh' );
                if ( typeof asOptions === 'object' )
                    var aOptions = $.extend( {
                    }, asOptions );
                
                this.children( 'ul' ).each( function () {
                
                    $( this ).children( 'li' ).each( function( i ) {     
                        
                        var sTabContentID = $( this ).children( 'a' ).attr( 'href' );
                        if ( ! bIsRefresh && i == 0 ) 
                            $( this ).addClass( 'active' );
                        
                        if ( $( this ).hasClass( 'active' ) ) 
                            $( sTabContentID ).show();
                        else
                            $( sTabContentID ).css( 'display', 'none' );
                        
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
                            oActiveContent = $( this ).parent().parent().find( sTabContentID ).css( 'display', 'block' ); 
                            oActiveContent.siblings( ':not( ul )' ).css( 'display', 'none' );
                            
                        });
                    });
                });
                                
            };
        }( jQuery ));";
        
    }

}
endif;