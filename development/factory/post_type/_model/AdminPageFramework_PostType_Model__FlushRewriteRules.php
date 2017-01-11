<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to modify sub-menu order.
 * 
 * @since           3.7.6
 * @package         AdminPageFramework/Factory/PostType
 * @internal
 */
class AdminPageFramework_PostType_Model__FlushRewriteRules extends AdminPageFramework_FrameworkUtility {
    
    /**
     * Stores a post type factory object.
     */
    public $oFactory;

    /**
     * Sets up hooks and properties.
     * 
     * @internal
     */
    public function __construct( $oFactory ) {
                
        if ( ! $this->_shouldProceed( $oFactory ) ) {
            return;
        }        
        
        $this->oFactory = $oFactory;
        
        register_activation_hook( 
            $this->oFactory->oProp->sCallerPath, 
            array( $this, '_replyToSetUpPostType' ) 
        );            
                
        add_action( 
            'registered_post_type', 
            array( $this, '_replyToScheduleToFlushRewriteRules' ), 
            10, 
            2 
        );
                
    }    
        /**
         * @return      boolean
         * @since       3.7.6
         */
        private function _shouldProceed( $oFactory ) {

            if ( ! $oFactory->oProp->bIsAdmin ) {
                return false;
            }        
            if ( ! $oFactory->oProp->sCallerPath ) {
                return false;
            }
            return 'plugin' === $oFactory->oProp->sScriptType;
  
        }     
    
    /**
     * Triggers the `setUp()` method so that the post type gets registered on the WordPress site.
     * 
     * Called when the plugin gets activated.
     * 
     * @callback    action      activate_{plugin base name}     
     * @since       3.7.6
     */
    public function _replyToSetUpPostType() {            
        do_action( "set_up_{$this->oFactory->oProp->sClassName}", $this );
    }        
    
    /**
     * @since       3.7.6
     * @callback    action      registered_post_type
     */
    public function _replyToScheduleToFlushRewriteRules( $sPostType, $aArguments ) {
        
        if ( $this->oFactory->oProp->sPostType !== $sPostType ) {
            return;
        }
                
        // If the execution flow in the plugin activation hook, schedule flushing rewrite rules.
        if ( did_action( 'activate_' . plugin_basename( $this->oFactory->oProp->sCallerPath ) ) ) {
            add_action( 'shutdown', array( $this, '_replyToFlushRewriteRules' ) );
        }

    }
    
       /**
         * Resets the rewrite rules for custom post types.
         * 
         * This must be done after the post type is registered. So the shutdown action hook is used.
         * 
         * @callback    action      shutdown
         * @since       3.7.6
         */
        public function _replyToFlushRewriteRules() {
            
            // Ensure to run this only once per page load and site-wide (among many post types created by the framework)
            if ( $this->hasBeenCalled( 'flush_rewrite_rules' ) ) {
                return;
            }
            $this->flushRewriteRules();
            
        }    
       
}
