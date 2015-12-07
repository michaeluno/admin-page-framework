<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Handles form redirects.
 *
 * @since           3.6.3
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 * @extends         AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_Model__FormRedirectHandler extends AdminPageFramework_FrameworkUtility {
    
    /**
     * Stores the factory object.
     */
    public $oFactory;

    /**
     * Sets up properties and hooks.
     * @since       3.6.3
     */
    public function __construct( $oFactory ) {
       
        $this->oFactory         = $oFactory;        
        
        // wp_mail() will be loaded by the time 'after_setup_theme' is loaded.
        // @deprecated      3.7.0
        // add_action( 
            // "load_after_{$this->oFactory->oProp->sClassName}", 
            // array( $this, '_replyToCheckRedirects' ), 
            // 22  // lower priority - this must be called after form validation is done. 20: field registration, 21: validation handling 22: handle redirects
        // ); 
        $this->_replyToCheckRedirects();
        
    }   
    
    /**
     * Check if a redirect transient is set and if so it redirects to the set page.
     * 
     * @since       3.0.0
     * @since       3.3.1       Moved from `AdminPageFramework_Setting_Base`.
     * @since       3.6.3       Moved from `AdminPageFramework_Form_Model`.
     * @internal
     * @callback    action      load_after_{class name}
     */
    public function _replyToCheckRedirects() {
  
        if ( ! $this->_shouldProceed() ) {
            return;
        }

        // The redirect transient key.
        $_sTransient = 'apf_rurl' . md5( 
            trim( "redirect_{$this->oFactory->oProp->sClassName}_{$_GET['page']}" )
        );
        
        // Check the settings error transient.
        $_aError = $this->oFactory->getFieldErrors();
        if ( ! empty( $_aError ) ) {
            $this->deleteTransient( $_sTransient ); // we don't need it any more.
            return;
        }
        
        // Okay, it seems the submitted data have been updated successfully.
        $_sURL = $this->getTransient( $_sTransient );
        if ( false === $_sURL ) {
            return;
        }
        
        // The redirect URL seems to be set.
        $this->deleteTransient( $_sTransient ); // we don't need it any more.
                    
        // Go to the page.
        $this->goToURL( $_sURL );
        
    }    
    
        /**
         * @since       3.6.3
         * @internal
         * @return      boolean     
         */
        private function _shouldProceed() {

            // Check if it's one of the plugin's added page. If not, do nothing.
            if ( ! $this->oFactory->_isInThePage() ) {
                return false;
            }
            
            // If the settings have not updated the options, do nothing.
            // if ( ! ( isset( $_GET[ 'settings-updated' ] ) && ! empty( $_GET[ 'settings-updated' ] ) ) ) {
                // return false;
            // }            
            $_bsSettingsUpdatedFlag = $this->getElement(
                $_GET,
                'settings-updated',
                false
            );            
            if ( ! $_bsSettingsUpdatedFlag ) {
                return false;
            }
            
            // [3.3.0+] If the confirmation key does not hold the 'redirect' string value, do not process.
            $_sConfirmationType = $this->getElement(
                $_GET,
                'confirmation',
                ''
            );
            return 'redirect' === $_sConfirmationType;
    
        }    
    
}