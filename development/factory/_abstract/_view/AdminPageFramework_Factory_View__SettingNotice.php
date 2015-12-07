<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to display setting notices.
 * 
 * @since       3.7.0
 * @package     AdminPageFramework
 * @subpackage  Factory
 * @internal
 * @extends     AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_Factory_View__SettingNotice extends AdminPageFramework_FrameworkUtility {
    
    public $oFactory;
    
    /**
     * Sets up hooks and properties.
     * 
     * @since       3.7.0
     */
    public function __construct( $oFactory ) {
        
        $this->oFactory = $oFactory;
        
        if ( is_network_admin() ) {
            add_action( 'network_admin_notices', array( $this, '_replyToPrintSettingNotice' ) );
        } else {
            add_action( 'admin_notices', array( $this, '_replyToPrintSettingNotice' ) );
        }          
        
    }
    
    /**
     * Displays stored setting notification messages.
     * 
     * @since       3.0.4
     * @since       3.7.0      Moved from `AdminPageFramework_Factory_View`.
     * @internal
     * @callback    action      network_admin_notices
     * @callback    action      admin_notice
     */
    public function _replyToPrintSettingNotice() {
            
        if ( ! $this->_shouldProceed() ) {
            return;
        }
        $this->oFactory->oForm->printSubmitNotices();
        
    }
        
        /**
         * Determines whether to proceed.
         * @sine        3.7.0
         * @return      boolean
         */
        private function _shouldProceed() {
            
            if ( ! $this->oFactory->_isInThePage() ) { 
                return false; 
            }
                
            // Ensure this method is called only once per a page load.
            if ( self::$_bSettingNoticeLoaded ) { 
                return false;
            }
            self::$_bSettingNoticeLoaded = true;
            return true;            
            
        }    
            /**
             * Stores a flag value indicating whether the setting notice method is called or not.
             * 
             * @since       3.1.3
             * @since       3.7.0      Moved from `AdminPageFramework_Factory_View`.
             * @internal
             */
            static private $_bSettingNoticeLoaded = false;   
  
}