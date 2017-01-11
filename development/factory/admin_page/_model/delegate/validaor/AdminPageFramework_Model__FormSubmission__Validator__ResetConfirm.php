<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to confirm resetting options.
 * 
 * @package     AdminPageFramework
 * @subpackage  Factory/AdminPage/Model
 * @since       3.6.3
 * @internal
 */
class AdminPageFramework_Model__FormSubmission__Validator__ResetConfirm extends AdminPageFramework_Model__FormSubmission__Validator__Reset {
        
    public $sActionHookPrefix = 'try_validation_after_'; // 3.7.6 Changed it from `try_validation_before_`
    public $iHookPriority = 40; // 3.7.6 Changed it from 20. Made it load later than the reset routine.
    public $iCallbackParameters = 5;

    /**
     * Resets the entire / part of the stored options.
     * 
     * @since       3.5.3
     * @since       3.6.3       Moved from `AdminPageFramework_Validation`. Changed the name from `_confirmReset()`.
     * @return      void
     * @internal
     * @callback    action      try_validation_before_{class name}
     */
    public function _replyToCallback( $aInputs, $aRawInputs, array $aSubmits, $aSubmitInformation, $oFactory ) {
                
        // if the 'reset' key in the field definition array is set, this value will be set.
        if ( ! $this->_shouldProceed( $oFactory, $aSubmits ) ) {
            return;
        }
        
        add_filter(
            "options_update_status_{$this->oFactory->oProp->sClassName}", 
            array( $this, '_replyToSetStatus' )
        );
                    
        // Go to the catch clause.
        $_oException = new Exception( 'aReturn' );  // the property name to return from the catch clasue.
        $_oException->aReturn = $this->_confirmSubmitButtonAction( 
            $this->getElement( $aSubmitInformation, 'input_name' ),
            $this->getElement( $aSubmitInformation, 'section_id' ),
            'reset' 
        );
        throw $_oException;        
        
    }      
        /**
         * @since       3.7.6
         * @return      boolean
         */
        protected function _shouldProceed( $oFactory, $aSubmits ) {
            
            if ( $oFactory->hasFieldError() ) {
                return false;
            }
            
            return ( bool ) $this->_getPressedSubmitButtonData( 
                $aSubmits, 
                'is_reset' 
            );  
            
        }
        
        /**
         * @return      array
         * @since       3.6.3
         * @callback    filter      options_update_status_{class name}
         */
        public function _replyToSetStatus( $aStatus ) {
            return array( 
                'confirmation' => 'reset'
            ) + $aStatus;
        }  
   
}
