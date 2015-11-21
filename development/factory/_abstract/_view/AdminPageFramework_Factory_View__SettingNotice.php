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
 * @since       DEVVER
 * @package     AdminPageFramework
 * @subpackage  Factory
 * @internal
 */
class AdminPageFramework_Factory_View__SettingNotice extends AdminPageFramework_WPUtility {
    
    public $oFactory;
    
    /**
     * Sets up hooks and properties.
     * 
     * @since       DEVVER
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
     * @since       DEVVER      Moved from `AdminPageFramework_Factory_View`.
     * @internal
     * @callback    action      network_admin_notices
     * @callback    action      admin_notice
     */
    public function _replyToPrintSettingNotice() {
            
        if ( ! $this->_shouldProceed() ) {
            return;
        }
            
        // This will load scripts for the fade-in effect.
        new AdminPageFramework_AdminNotice( '' );
        
        $_iUserID  = get_current_user_id();
        $_aNotices = $this->getTransient( "apf_notices_{$_iUserID}" );
        if ( false === $_aNotices ) { 
            return; 
        }
        $this->deleteTransient( "apf_notices_{$_iUserID}" );
    
        // By setting false to the 'settings-notice' key, it's possible to disable the notifications set with the framework.
        if ( isset( $_GET['settings-notice'] ) && ! $_GET['settings-notice'] ) { 
            return; 
        }
        
        $this->_printSettingNotices( $_aNotices );
        
    }
        
        /**
         * Determines whether to proceed.
         * @sine        DEVVER
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
             * @since       DEVVER      Moved from `AdminPageFramework_Factory_View`.
             * @internal
             */
            static private $_bSettingNoticeLoaded = false;   
        
        /**
         * Displays settings notices.
         * @since       3.5.3
         * @since       DEVVER      Moved from `AdminPageFramework_Factory_View`.
         * @internal
         * @return      void
         */
        private function _printSettingNotices( $aNotices ) {
            
            $_aPeventDuplicates = array();
            foreach ( array_filter( ( array ) $aNotices, 'is_array' ) as $_aNotice ) {
                
                $_sNotificationKey = md5( serialize( $_aNotice ) );
                if ( isset( $_aPeventDuplicates[ $_sNotificationKey ] ) ) {
                    continue;
                }
                $_aPeventDuplicates[ $_sNotificationKey ] = true;
                
                new AdminPageFramework_AdminNotice(
                    $this->getElement( $_aNotice, 'sMessage' ),
                    $this->getElement( $_aNotice, 'aAttributes' )
                );              
              
            }            
            
        }
  
}