<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to display setting notices.
 * 
 * @since       3.7.0
 * @package     AdminPageFramework/Common/Factory
 * @internal
 * @extends     AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_Factory_View__SettingNotice extends AdminPageFramework_FrameworkUtility {
    
    public $oFactory;
    
    /**
     * Sets up hooks and properties.
     * 
     * @since       3.7.0
     * @since       3.7.9       Added the second parameter to accept an action hook name.
     */
        
    public function __construct( $oFactory, $sActionHookName='admin_notices' ) {
        
        $this->oFactory = $oFactory;
           
        add_action( $sActionHookName, array( $this, '_replyToPrintSettingNotice' ) );
        
    }
    
    /**
     * Displays stored setting notification messages.
     * 
     * @since       3.0.4
     * @since       3.7.0       Moved from `AdminPageFramework_Factory_View`.
     * @internal
     * @callback    action      network_admin_notices
     * @callback    action      admin_notices
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
            if ( $this->hasBeenCalled( __METHOD__ ) ) {
                return false;
            }
            
            // Some factory classes including the page meta box factory can leave the form object uninstantiated.
            return isset( $this->oFactory->oForm );
            
        }    
  
}
