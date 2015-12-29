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
 * new AdminPageFramework_PointerToolTip(
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
 * @since       3.7.0
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @extends     AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_PointerToolTip extends AdminPageFramework_FrameworkUtility {
    
    static private $_bResourceLoaded = false;
    
    /**
     * Stores pointer data.
     */
    static private $aPointers = array();
    
    /**
     * Stores the pointer tool box id for the class instance.
     */
    public $sPointerID;
    
    /**
     * Stores the pointer tool box defintion for the class instance.
     */
    public $aPointerData;
    
    /**
     * User set screen IDs. Accepts APF page slugs.
     */
    public $aScreenIDs = array();
    
    
    /**
     * Sets up hooks and properties.
     * 
     * @since       3.7.0
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
    
        // Store the registration data to the property.
        $this->aScreenIDs    = $this->getAsArray( $asScreenIDs );
        $this->sPointerID    = $sPointerID;
        $this->aPointerData  = $aPointerData;

        $this->_setHooks( $this->aScreenIDs );        
        
    }   
        /**
         * Sets up hooks.
         * @since       3.7.0
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
            
            if ( ! $this->_hasBeenCalled() ) {
                return;
            } 
            
            // Checks the screen id and page slug and add items if they match the current screen
            add_action(
                'admin_enqueue_scripts', 
                array( $this, '_replyToLoadPointers' ),
                1000
            );            
                        
        }    
            /**
             * @return      boolean
             */
            private function _hasBeenCalled() {
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
     * Checks the screen id and page slug and add items if they match the current screen
     * 
     * @callback    action      admin_enqueue_scripts
     * @since       3.7.0
     * @return      void
     */
    public function _replyToLoadPointers( /* $hook_suffix */ ) {
    
        $_aPointers = $this->_getValidPointers( $this->_getPointers() );
             
        if ( empty( $_aPointers ) || ! is_array( $_aPointers ) ) {
            return;
        }
        
        $this->_enqueueScripts(); 
        
        self::$aPointers = $_aPointers + self::$aPointers;
        
    }
        /**
         * Get pointers for this screen.
         * @since       3.7.0
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
         * @since       3.7.0
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
            $_aValidPointers = array();
         
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

                $_aPointer[ 'target' ]     = $this->getAsArray( $_aPointer[ 'target' ] );
                $_aPointer[ 'pointer_id' ] = $_iPointerID;
                
                // Add the pointer to $_aValidPointers array
                $_aValidPointers[] =  $_aPointer;
                
            }            
            return $_aValidPointers;
            
        }        
            
            /**
             * @return      boolean
             * @since       3.7.0
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
        
        /**
         * Enqueues scripts.
         */
        private function _enqueueScripts() {
               
            wp_enqueue_script( 'jquery' );         
         
            // Add pointers style to queue.
            wp_enqueue_script( 'wp-pointer' );
            wp_enqueue_style( 'wp-pointer' );

            // Embeds the inline script
            add_action( 
                'admin_print_footer_scripts',
                array( $this, '_replyToInsertInlineScript' )
            );            
            
        }    
    
    /**
     * @since       3.7.0
     * @callback    action      admin_print_footer_scripts
     */
    public function _replyToInsertInlineScript() {
   
        echo "<script type='text/javascript' class='admin-page-framework-pointer-tool-tip'>"
            . '/* <![CDATA[ */'
            . $this->_getInlineScript( self::$aPointers )
            . '/* ]]> */'
        . "</script>";
        
    }
        /**
         * Returns an inline JavaScript script.
         * 
         * @since       3.7.0
         * @return      string     
         */
        public function _getInlineScript( $aPointers=array() ) {

            $_aJSArray      = json_encode( $aPointers );

            /**
             * Checks checkboxes in siblings.
             */
            return <<<JAVASCRIPTS
( function( jQuery ) {
jQuery( document ).ready( function( jQuery ) {
    jQuery.each( $_aJSArray, function( iIndex, _aPointer ) {
        var _aOptions = jQuery.extend( _aPointer.options, {
            close: function() {
                jQuery.post( ajaxurl, {
                    pointer: _aPointer.pointer_id,
                    action: 'dismiss-wp-pointer'
                });
            }
        });
        jQuery.each( _aPointer.target, function( iIndex, _sTarget ) {
            var _oTarget = jQuery( _sTarget );
            if ( _oTarget.length <= 0 ) {
                return true;    // skip
            }
            var _oResult = jQuery( _sTarget ).pointer( _aOptions ).pointer( 'open' );
            if ( _oResult.length > 0 ) {
                return false;   // escape to ensure no same item gets displayed in one screen
            }
        });
    });
});
}( jQuery ));
JAVASCRIPTS;
        
        }         
    
}