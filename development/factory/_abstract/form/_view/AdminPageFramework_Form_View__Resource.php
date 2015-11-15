<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to build forms.
 * 
 * This is a delegation class of `AdminPageFramework_Form_View`.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER
 */
class AdminPageFramework_Form_View__Resource extends AdminPageFramework_WPUtility {
    
    /**
     * Stores the form object to let this class access the resource array.
     */
    public $oForm;
    
    /**
     * Sets up hooks.
     * @since       DEVVER
     */
    public function __construct( $oForm ) {

        $this->oForm = $oForm;
    
        // If it is loaded in the background, no need to load scripts and styles.
        if ( $this->isDoingAjax() ) {
            return;
        }    
    
        $this->_setHooks();
  
    }
        /**
         * @since       DEVVER
         */
        private function _setHooks() {
            
            if ( is_admin() ) {
                $this->_setAdminHooks();
                return;
            }
            
            // Hook the admin header to insert custom admin stylesheets and scripts.
            add_action( 'wp_enqueue_scripts', array( $this, '_replyToEnqueueScripts' ) );
            add_action( 'wp_enqueue_scripts', array( $this, '_replyToEnqueueStyles' ) );
            
            /// A low priority is required to let dependencies loaded fast especially in customizer.php.
            add_action( did_action( 'wp_print_styles' ) ? 'wp_print_footer_scripts' : 'wp_print_styles', array( $this, '_replyToAddStyle' ), 999 );
            add_action( did_action( 'wp_print_scripts' ) ? 'wp_print_footer_scripts' : 'wp_print_scripts', array( $this, '_replyToAddScript' ), 999 );     
        
            // Take care of items that could not be added in the head tag.
            
            /// For admin pages other than wp-admin/customizer.php 
            add_action( 'wp_footer', array( $this, '_replyToEnqueueScripts' ) ); 
            add_action( 'wp_footer', array( $this, '_replyToEnqueueStyles' ) );        
            
            /// For all admin pages.
            add_action( 'wp_print_footer_scripts', array( $this, '_replyToAddStyle' ), 999 );
            add_action( 'wp_print_footer_scripts', array( $this, '_replyToAddScript' ), 999 );

            // To add the custom attributes to the enqueued style and script tags.
            // @deprecated      DEVVER      Deprecated since modifying inline attributes sometimes does not take effect
            // add_filter( 'script_loader_src', array( $this, '_replyToSetupArgumentCallback' ), 1, 2 );
            // add_filter( 'style_loader_src', array( $this, '_replyToSetupArgumentCallback' ), 1, 2 );
            
        }
            private function _setAdminHooks() {
                
                // Hook the admin header to insert custom admin stylesheets and scripts.
                add_action( 'admin_enqueue_scripts', array( $this, '_replyToEnqueueScripts' ) );
                add_action( 'admin_enqueue_scripts', array( $this, '_replyToEnqueueStyles' ) );
        
                add_action( did_action( 'admin_print_styles' ) ? 'admin_print_footer_scripts' : 'admin_print_styles', array( $this, '_replyToAddStyle' ), 999 );
                add_action( did_action( 'admin_print_scripts' ) ? 'admin_print_footer_scripts' : 'admin_print_scripts', array( $this, '_replyToAddScript' ), 999 );                         
                    
                // Take care of items that could not be added in the head tag.                
                /// For wp-admin/customizer.php 
                add_action( 'customize_controls_print_footer_scripts', array( $this, '_replyToEnqueueScripts' ) );
                add_action( 'customize_controls_print_footer_scripts', array( $this, '_replyToEnqueueStyles' ) );

                /// For admin pages other than wp-admin/customizer.php 
                add_action( 'admin_footer', array( $this, '_replyToEnqueueScripts' ) ); 
                add_action( 'admin_footer', array( $this, '_replyToEnqueueStyles' ) );        
                
                /// For all admin pages.
                add_action( 'admin_print_footer_scripts', array( $this, '_replyToAddStyle' ), 999 );
                add_action( 'admin_print_footer_scripts', array( $this, '_replyToAddScript' ), 999 );  
                    
            }

    /**
     * Enqueues page script resources.
     * 
     * @since       DEVVER
     */
    public function _replyToEnqueueScripts() {
        if ( ! $this->oForm->isInThePage() ) {
            return;
        }
        foreach( $this->oForm->getResources( 'src_scripts' ) as $_asEnqueue ) {
            $this->_enqueueScript( $_asEnqueue );
        }       
    }
        /**
         * Stores flags of enqueued items.
         * @since       DEVVER
         */
        static private $_aEnqueued = array();    
        /**
         * @return      void
         * @since       DEVVER
         */
        private function _enqueueScript( $asEnqueue ) {
                
            $_aEnqueueItem = $this->_getFormattedEnqueueScript( $asEnqueue );
            
            // Do not load the same items multiple times.
            if ( isset( self::$_aEnqueued[ $_aEnqueueItem[ 'src' ] ] ) ) {
                return;
            }
            self::$_aEnqueued[ $_aEnqueueItem[ 'src' ] ] = $_aEnqueueItem;
            
            wp_enqueue_script( 
                $_aEnqueueItem[ 'handle_id' ], 
                $_aEnqueueItem[ 'src' ], 
                $_aEnqueueItem[ 'dependencies' ], 
                $_aEnqueueItem[ 'version' ], 
                did_action( 'admin_body_class' ) 
                    ? true 
                    : $_aEnqueueItem[ 'in_footer' ]
            );
            if ( $_aEnqueueItem[ 'translation' ] ) {
                wp_localize_script( 
                    $_aEnqueueItem[ 'handle_id' ], 
                    $_aEnqueueItem[ 'handle_id' ], 
                    $_aEnqueueItem[ 'translation' ] 
                );
            }                
            
        }            
            /**
             * @return      array
             * @since       DEVVER
             */
            private function _getFormattedEnqueueScript( $asEnqueue ) {
                static $_iCallCount = 1;
                $_aEnqueueItem = $this->getAsArray( $asEnqueue ) + array(
                    'handle_id'     => 'script_' . $this->oForm->aArguments[ 'caller_id' ] . '_' . $_iCallCount,
                    'src'           => null,
                    'dependencies'  => null,
                    'version'       => null,
                    'in_footer'     => false,
                    'translation'   => null,
                );
                if ( is_string( $asEnqueue ) ) {
                    $_aEnqueueItem[ 'src' ] = $asEnqueue;
                }                 
                $_aEnqueueItem[ 'src' ] = $this->getResolvedSRC( $_aEnqueueItem[ 'src' ] );
                $_iCallCount++;
                return $_aEnqueueItem;
            }    

    
    /**
     * Enqueues page stylesheet resources.
     * 
     * @since       DEVVER
     */    
    public function _replyToEnqueueStyles() {

        if ( ! $this->oForm->isInThePage() ) {
            return;
        }
        foreach( $this->oForm->getResources( 'src_styles' ) as $_asEnqueueItem ) {
            $this->_enqueueStyle( $_asEnqueueItem );
        }           
    
    }
        private function _enqueueStyle( $asEnqueue ) {
            $_aEnqueueItem = $this->_getFormattedEnqueueStyle( $asEnqueue );
            wp_enqueue_style( 
                $_aEnqueueItem[ 'handle_id' ],
                $_aEnqueueItem[ 'src' ], 
                $_aEnqueueItem[ 'dependencies' ], 
                $_aEnqueueItem[ 'version' ], 
                $_aEnqueueItem[ 'media' ]
            );            
        }
            /**
             * @return      array
             */
            private function _getFormattedEnqueueStyle( $asEnqueue ) {
                static $_iCallCount = 1;
                $_aEnqueueItem = $this->getAsArray( $asEnqueue ) + array(
                    'handle_id'     => 'style_' . $this->oForm->aArguments[ 'caller_id' ] . '_' . $_iCallCount,
                    'src'           => null,
                    'dependencies'  => null,
                    'version'       => null,
                    'media'         => null,
                );
                if ( is_string( $asEnqueue ) ) {
                    $_aEnqueueItem[ 'src' ] = $asEnqueue;
                }                 
                $_aEnqueueItem[ 'src' ] = $this->getResolvedSRC( $_aEnqueueItem[ 'src' ] );
                $_iCallCount++;
                return $_aEnqueueItem;
            }            
    
    /**
     * Enqueues inline styles.
     * 
     * @since       DEVVER
     */    
    public function _replyToAddStyle() {
        
        if ( ! $this->oForm->isInThePage() ) {
            return;
        }   
        $_sCSSRules = $this->_getFormattedInlineStyles( 
            $this->oForm->getResources( 'inline_styles' )
        );
        
        $_sID = $this->sanitizeSlug( strtolower( $this->oForm->aArguments[ 'caller_id' ] ) );
        if ( $_sCSSRules ) {            
            echo "<style type='text/css' id='inline-style-{$_sID}' class='admin-page-framework-form-style'>"
                    . $_sCSSRules
                . "</style>";
        }        
        $_sIECSSRules = $this->_getFormattedInlineStyles( 
            $this->oForm->getResources( 'inline_styles_ie' )
        );        
        if ( $_sIECSSRules ) {
            echo "<!--[if IE]><style type='text/css' id='inline-style-ie-{$_sID}' class='admin-page-framework-form-ie-style'>"
                    . $_sIECSSRules
                . "</style><![endif]-->";
        } 
        
        // Empty the values as this method can be called multiple times, in the head tag and the footer.
        $this->oForm->setResources( 'inline_styles', array() );
        $this->oForm->setResources( 'inline_styles_ie', array() );
        
    }
        /**
         * @since       DEVVER
         * @string
         */
        private function _getFormattedInlineStyles( array $aInlineStyles ) {
            $_sCSSRules = implode( PHP_EOL, array_unique( $aInlineStyles ) );
            return $this->isDebugMode()
                ? $_sCSSRules
                : $this->minifyCSS( $_sCSSRules );
        }
    
    /**
     * Enqueues page inline scripts.
     * 
     * @since       DEVVER
     */    
    public function _replyToAddScript() {
        
        if ( ! $this->oForm->isInThePage() ) {
            return;
        }        
        
        $_sScript = implode( PHP_EOL, array_unique( $this->oForm->getResources( 'inline_scripts' ) ) );
        if ( $_sScript ) {
            $_sID = $this->sanitizeSlug( strtolower( $this->oForm->aArguments[ 'caller_id' ] ) );
            echo "<script type='text/javascript' id='inline-script-{$_sID}' class='admin-page-framework-form-script'>" 
                    . $_sScript
                . "</script>"; 
        }        
        $this->oForm->setResources( 'inline_scripts', array() );
        
    }
   
}