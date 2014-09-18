<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Script_Widget' ) ) :
/**
 * Provides JavaScript scripts to handle widget events.
 * 
 * @since       3.2.0
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 */
class AdminPageFramework_Script_Widget {

    static public function getjQueryPlugin() {
        
        /**
         * Triggers the 'admin_page_framework_saved_widget' event when a widget is dropped.
         * 
         * @since   3.2.0
         */     
        return "(function ( $ ) {
            
            $( document ).ready( function() {
                 
                $( document ).ajaxComplete( function( event, XMLHttpRequest, ajaxOptions ) {
       
                    // determine which ajax request is this (we're after \"save-widget\")
                    var request = {}, pairs = ajaxOptions.data.split('&'), i, split, oWidget;
                    for( i in pairs ) {
                        split = pairs[i].split( '=' );
                        request[decodeURIComponent( split[0] )] = decodeURIComponent( split[1] );
                    }
                    // only proceed if this was a widget-save request
                    if( request.action && ( 'save-widget' === request.action ) ) {
                        
                        // locate the widget block
                        oWidget = $( 'input.widget-id[value=\"' + request['widget-id'] + '\"]' ).parents( '.widget' );

                        // trigger manual save, if this was the save request 
                        // and if we didn't get the form html response (the wp bug)
                        if( ! XMLHttpRequest.responseText )  {
                            
                            wpWidgets.save( oWidget, 0, 1, 0);
                            return;
                            
                        } 
    
                        // Check if it is the framework widget.
                        if ( $( oWidget ).find( '.admin-page-framework-sectionset' ).length <= 0 ) { 
                            return; 
                        } 
                        
                        // we got an response, this could be either our request above,
                        // or a correct widget-save call, so fire an event on which we can hook our js                        
                        $( document ).trigger( 'admin_page_framework_saved_widget', oWidget );
                
                    }
                });           
           
            }); 

        }( jQuery ));";     
        
    }

}
endif;