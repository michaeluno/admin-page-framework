<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to handle redirects set with the submit button with the redirect_url argument.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.3
 * @internal
 */
class AdminPageFramework_Model__FormSubmission__Validator__Redirect extends AdminPageFramework_Model__FormSubmission__Validator_Base {
    
    public $sActionHookPrefix   = 'try_validation_before_';
    public $iHookPriority       = 40;
    public $iCallbackParameters = 5;
        
    /**
     * Sets a redirect url in a transient and confirmation message.
     * 
     * @since       3.5.3
     * @since       3.6.3       Moved from `AdminPageFramework_Validation`. Changed the name from `_setRedirect()`.
     * @internal
     * @return      void
     */
    public function _replyToCallback( $aInputs, $aRawInputs, array $aSubmits, $aSubmitInformation, $oFactory ) {
        
        $_sRedirectURL = $this->_getPressedSubmitButtonData(
            $aSubmits,
            'redirect_url'
        );
        if ( ! $_sRedirectURL ) {
            return;
        }
        
        add_filter(
            "options_update_status_{$this->oFactory->oProp->sClassName}",
            array( $this, '_replyToSetStatus' )
        );
        
        $this->_setRedirectTransients(
            $_sRedirectURL,
            $this->getElement( $aSubmitInformation, 'page_slug' )
        );
        
    }
    
        /**
         * @return      array
         * @since       3.6.3
         * @callback    filter      options_update_status_{class name}
         */
        public function _replyToSetStatus( $aStatus ) {
            return array(
                'confirmation' => 'redirect',
            ) + $aStatus;
        }
        
        /**
         * Sets the given URL's transient.
         * @since       unknown
         * @since       3.6.3       Moved from `AdminPageFramework_Validation`.
         */
        private function _setRedirectTransients( $sURL, $sPageSlug ) {
            if ( empty( $sURL ) ) {
                return;
            }
            $_sTransient = 'apf_rurl' . md5( trim( "redirect_{$this->oFactory->oProp->sClassName}_{$sPageSlug}" ) );

            return $this->setTransient( $_sTransient, $sURL , 60*2 );
        }
 
}
