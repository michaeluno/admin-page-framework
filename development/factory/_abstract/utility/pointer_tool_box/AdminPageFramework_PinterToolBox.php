<?php
/**
 * Displays notification in the administration area.
 *    
 * @package      Admin Page Framework
 * @copyright    Copyright (c) 2015, <Michael Uno>
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 */

/**
 * Displays pointer tool boxes in the admin area.
 * 
 * Usage:
 * 
 * `
 * new AdminPageFramework_PointerToolBox(
 *     'post',  // screen id or page slug
 *     'xyz140', // unique id for the pointer tool box
 *     array(  // pointer data
 *         'target'    => '#change-permalinks',
 *         'options'   => array(
 *             'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
 *                 __( 'Title' ,'plugindomain'), 
 *                 __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.','plugindomain')
 *             ),
 *             'position'  => array( 'edge' => 'top', 'align' => 'middle' )
 *         )
 *     )
 * );  
 *  
 * `
 * 
 * @since       DEVVER
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @extends     AdminPageFramework_WPUtility
 */
class AdminPageFramework_PointerToolBox extends AdminPageFramework_WPUtility {
    
    static private $_bResourceLoaded = false;
        
    public $sPointerID;
    
    public $aPointerData;
    
    /**
     * User set screen IDs. Accepts APF page slugs.
     */
    public $aScreenIDs = array();
    
    
    /**
     * Sets up hooks and properties.
     * 
     * @since       DEVVER
     * @see         https://codex.wordpress.org/Plugin_API/Admin_Screen_Reference
     * @param       array|strin     $asScreenIDs        Screen IDs or page slug.
     * @param       string          $sPointerID         A unique pointer ID.
     * @Param       array           $aPointerData       The pointer data.
     */
    public function __construct( $asScreenIDs, $sPointerID, array $aPointerData ) {

        // Bail if the WordPress version is less than 3.3,
        if ( version_compare( $GLOBALS[ 'wp_version' ], '3.3', '<' ) ) {        
            return false;
        }       
    
        // Prints out the script in the background.
        if ( 'admin-page-framework-pointer-tool-box' === $this->getElement( $_GET, 'script' ) ) {
            exit( $this->_renderScript() );
        }
        
        // Store the registration data to the property.
        $this->aScreenIDs    = $this->getAsArray( $asScreenIDs );
        $this->sPointerID    = $sPointerID;
        $this->aPointerData  = $aPointerData;

        $this->_setHooks( $this->aScreenIDs );
        
        if ( ! $this->_shouldProceed() ) {
            return;
        }
   
        add_action(
            'admin_enqueue_scripts', 
            array( $this, '_replyToLoadPointer' ),
            1000
        );
        
    }   
        /**
         * 
         */
        private function _setHooks( $aScreenIDs ) {
            foreach( $aScreenIDs as $_sScreenID ) {            
                if ( ! $_sScreenID ) {
                    continue;
                }
                add_filter( 
                    get_class( $this ) . '-' . $_sScreenID, 
                    array( $this, '_replyToSetPointer' )
                );
                                
            }       
        }    
        /**
         * @return      boolean
         */
        private function _shouldProceed() {
            if ( self::$_bResourceLoaded ) {
                return false;
            }            
            self::$_bResourceLoaded = true;
            return true;
        }
    
        /**
         * @callback    filter      {class name}-{screen id}
         * @return      array
         */
        public function _replyToSetPointer( $aPointers ) {
            return array(
                $this->sPointerID   => $this->aPointerData
            ) + $aPointers;
        }    
    
    /**
     * @callback        action      admin_enqueue_scripts
     */
    public function _replyToLoadPointer( /* $hook_suffix */ ) {
    
        $_aPointers = $this->_getPointers();
             
        if ( empty( $_aPointers ) || ! is_array( $_aPointers ) ) {
            return;
        }
        
        $this->_loadScripts( 
            $this->_getValidPointers( $_aPointers )
        );
        
    }
        /**
         * Get pointers for this screen
         * @return      array
         */
        private function _getPointers() {
            
            $_oScreen   = get_current_screen();
            $_sScreenID = $_oScreen->id;    
            if ( in_array( $_sScreenID, $this->aScreenIDs ) ) {
                return apply_filters( get_class( $this ) . '-' . $_sScreenID, array() );
            } 
            
            if ( isset( $_GET[ 'page' ] ) ) {
                return apply_filters( get_class( $this ) . '-' . $_GET[ 'page' ], array() );
            }
            return array();
            
        }
    
        /**
         * @return      array
         * @since       DEVVER
         */
        private function _getValidPointers( $_aPointers ) {
        
            // Get dismissed pointers
            $_aDismissed      = explode( 
                ',', 
                ( string ) get_user_meta( 
                    get_current_user_id(), 
                    'dismissed_wp_pointers', 
                    true 
               )
            );
            $_aValidPointers = array(
                'pointers'  => array(),
            );
         
            // Check pointers and remove dismissed ones.
            foreach ( $_aPointers as $_iPointerID => $_aPointer ) {
                
                $_aPointer = $_aPointer + array(
                    'target'        => null,
                    'options'       => null,
                    'pointer_id'    => null,
                );
                
                // Sanity check
                if ( $this->_shouldSkip( $_iPointerID, $_aDismissed, $_aPointer ) ) {
                    continue;
                }

                $_aPointer[ 'pointer_id' ] = $_iPointerID;
         
                // Add the pointer to $_aValidPointers array
                $_aValidPointers[ 'pointers' ][] =  $_aPointer;
                
            }            
            return $_aValidPointers;
            
        }        
            /**
             * @return      boolean
             * @since       DEVVER
             */
            private function _shouldSkip( $_iPointerID, $_aDismissed, $_aPointer ) {
                
                if ( in_array( $_iPointerID, $_aDismissed ) ) {
                    return true;
                }               
                if ( empty( $_aPointer ) ) {
                    return true;
                }
                if ( empty( $_iPointerID ) ) {
                    return true;
                }
                if ( empty( $_aPointer[ 'target' ] ) ) {
                    return true;
                }
                if ( empty( $_aPointer[ 'options' ] ) ) {
                    return true;
                }
                return false;
                
            }   
            
        private function _loadScripts( $_aValidPointers ) {
               
            // No valid pointers? Stop here.
            if ( empty( $_aValidPointers ) ) {
                return;
            }           
            
            wp_enqueue_script( 'jquery' );         
         
            // Add pointers style to queue.
            wp_enqueue_style( 'wp-pointer' );
         
            // Add pointers script to queue. Add custom script.
            wp_enqueue_script( 
                'admin-page-framework-pointer',     // handle id
                add_query_arg( 
                    array( 
                        'script' => 'admin-page-framework-pointer-tool-box'
                    ), 
                    admin_url()
                ),
                array( 'wp-pointer' ) 
            );
         
            // Add pointer options to script.
            wp_localize_script( 
                'admin-page-framework-pointer',     // handle id
                'AdminPageFrameworkPointerToolBoxes',    // data name
                $_aValidPointers 
            );
            
        }    

    /**
     * Renders the script.
     */
    public function _renderScript() {
        echo $this->_getScript();
    }
        /**
         * Returns an inline JavaScript script.
         * 
         * @since       DEVVER
         * @return      string     
         */
        public function _getScript() {
            
            /**
             * Checks checkboxes in siblings.
             */
            return <<<JAVASCRIPTS
( function( $ ) {
jQuery( document ).ready( function( $ ) {
    
    $.each( AdminPageFrameworkPointerToolBoxes.pointers, function( iIndex, _aPointer ) {
        
        var _aOptions = $.extend( _aPointer.options, {
            close: function() {
                $.post( ajaxurl, {
                    pointer: _aPointer.pointer_id,
                    action: 'dismiss-wp-pointer'
                });
            }
        });
 
        $( _aPointer.target ).pointer( _aOptions ).pointer( 'open' );

    });
});
}( jQuery ));
JAVASCRIPTS;
        
        } 
    
}