<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to reset options.
 * 
 * If the key to reset is not specified, it does nothing.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.3
 * @internal
 */
class AdminPageFramework_Model__FormSubmission__Validator__Reset extends AdminPageFramework_Model__FormSubmission__Validator_Base {
    
    public $sActionHookPrefix = 'try_validation_after_';
    public $iHookPriority = 30;
    public $iCallbackParameters = 5;

    /**
     * Handles resetting options.
     * 
     * @since       3.5.3
     * @since       3.5.9       Added the third parameter.
     * @since       3.6.3       Moved from `AdminPageFramework_Validation`. Changed the name from `_doResetOptions()`.
     * @return      void
     * @internal
     * @callback    action      try_validation_after_{class name}
     */
    public function _replyToCallback( $aInputs, $aRawInputs, array $aSubmits, $aSubmitInformation, $oFactory ) {
            
        if ( ! $this->_shouldProceed( $oFactory, $aSubmits ) ) {
            return;
        }       
            
        // this will be set if the user confirms the reset action.
        $_sKeyToReset = $this->_getPressedSubmitButtonData( 
            $aSubmits, 
            'reset_key' 
        );
        $_sKeyToReset = trim( $_sKeyToReset );
        if ( ! $_sKeyToReset ) {
            return;
        }            
        $_oException = new Exception( 'aReturn' );
        $_oException->aReturn = $this->_resetOptions(
            $_sKeyToReset, 
            $aInputs,
            $aSubmitInformation
        );
        throw $_oException;               
        
    }        
        /**
         * @since       3.7.6
         * @return      boolean
         */
        protected function _shouldProceed( $oFactory, $aSubmits ) {
            return ! $oFactory->hasFieldError();            
        }
        
        /**
         * Performs resetting options.
         * 
         * @since       2.1.2
         * @since       3.6.3       Moved from `AdminPageFramework_Validation`.
         * @remark      `$aInputs` has only the page elements that called the validation callback. 
         * In other words, it does not hold other pages' option keys.
         * @return      array       The modified input array.
         */
        private function _resetOptions( $sKeyToReset, array $aInputs, array $aSubmitInformation ) {

            // 3.5.9+     
            $this->_doResetActions( $sKeyToReset, $aInputs, $aSubmitInformation );
            
            // As of 3.1.0, an empty value is accepted for the option key.
            if ( ! $this->oFactory->oProp->sOptionKey ) {
                return array();
            }
            
            // The key to delete is not specified, 1 is sent from the form input.
            if ( in_array( $sKeyToReset, array( '1', ), true ) ) {
                delete_option( $this->oFactory->oProp->sOptionKey );
                return array();
            }
            
            // The key to reset is specified.
            $_aDimensionalKeys = explode( '|', $sKeyToReset );
            $this->unsetDimensionalArrayElement( $this->oFactory->oProp->aOptions, $_aDimensionalKeys );
            $this->unsetDimensionalArrayElement( $aInputs, $_aDimensionalKeys );
          
            update_option( $this->oFactory->oProp->sOptionKey, $this->oFactory->oProp->aOptions );
            $this->oFactory->setSettingNotice( $this->oFactory->oMsg->get( 'specified_option_been_deleted' ) );
        
            // the returned array will be saved.
            return $aInputs; 
         
        }
            /**
             * Triggers reset actions.
             * @since       3.5.9
             * @since       3.6.3       Moved from `AdminPageFramework_Validation`.
             * @internal
             */
            private function _doResetActions( $sKeyToReset, $aInputs, $aSubmitInformation ) {
                
                // '1' is reserved by the framework for resetting all options.
                $sKeyToReset = '1' === $sKeyToReset
                    ? ''
                    : $sKeyToReset;
                    
                $_sPageSlug  = $aSubmitInformation[ 'page_slug' ];
                $_sTabSlug   = $aSubmitInformation[ 'tab_slug' ];
                $_sFieldID   = $aSubmitInformation[ 'field_id' ];
                $_sSectionID = $aSubmitInformation[ 'section_id' ];
                $this->addAndDoActions(
                    $this->oFactory,
                    array( 
                        $_sSectionID 
                            ? "reset_{$this->oFactory->oProp->sClassName}_{$_sSectionID}_{$_sFieldID}" 
                            : "reset_{$this->oFactory->oProp->sClassName}_{$_sFieldID}",
                        $_sSectionID 
                            ? "reset_{$this->oFactory->oProp->sClassName}_{$_sSectionID}" 
                            : null, // if null given, the method will ignore it
                        $_sTabSlug
                            ? "reset_{$this->oFactory->oProp->sClassName}_{$_sPageSlug}_{$_sTabSlug}"
                            : null, // if null given, the method will ignore it
                        "reset_{$this->oFactory->oProp->sClassName}_{$_sPageSlug}",
                        "reset_{$this->oFactory->oProp->sClassName}",
                    ),
                    $sKeyToReset,
                    $aInputs,
                    $this->oFactory,
                    $aSubmitInformation
                );                      
                
            }        

}