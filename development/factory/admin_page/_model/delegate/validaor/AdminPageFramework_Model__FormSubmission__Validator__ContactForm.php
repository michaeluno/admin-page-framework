<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to handle a contact form.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.3
 * @internal
 */
class AdminPageFramework_Model__FormSubmission__Validator__ContactForm extends AdminPageFramework_Model__FormSubmission__Validator_Base {
    
    public $sActionHookPrefix = 'try_validation_after_';    // 3.7.6 Changed it from `try_validation_before_`
    public $iHookPriority = 10;
    public $iCallbackParameters = 5;
 
    /**
     * Sends a user set contact form as an email.
     * 
     * @remark      This should be done before the redirect because the user may set a redirect and email. In that case, send the email first and redirect to the set page.
     * @internal    
     * @since       3.5.3
     * @since       3.6.3       Moved from `AdminPageFramework_Validation`. Changed the name from `_doContactForm()`.
     * @return      void
     * @callback    action      try_validation_before_{class name}
     */
    public function _replyToCallback( $aInputs, $aRawInputs, array $aSubmits, $aSubmitInformation, $oFactory ) {
                
        // Check whether sending an email has been confirmed by the user or not.
        if ( ! $this->_shouldProceed( $oFactory, $aSubmits ) ) {
            return;
        }
        
        // At this point, the user has confirmed to send an email of a contact form.
        $this->_sendEmailInBackground( 
            $aInputs, 
            $this->getElement( $aSubmitInformation, 'input_name' ), 
            $this->getElement( $aSubmitInformation, 'section_id' )
        );
        $this->oFactory->oProp->_bDisableSavingOptions = true;
        $this->deleteTransient( 'apf_tfd' . md5( 'temporary_form_data_' . $this->oFactory->oProp->sClassName . get_current_user_id() ) );
        
        // Schedule to remove the confirmation url query key.
        add_action( "setting_update_url_{$this->oFactory->oProp->sClassName}", array( $this, '_replyToRemoveConfirmationQueryKey' ) );
        
        // Go to the catch clause.
        $_oException = new Exception( 'aReturn' );  // the property name to return from the catch clause.
        $_oException->aReturn = $aInputs;
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
                'confirmed_sending_email' 
            );            
            
        }
         
        /**
         * Sends an email set via the form.
         * 
         * The email contents should be set with the form fields. 
         * 
         * @since       3.3.0
         * @since       3.6.3       Moved from `AdminPageFramework_Validation`.
         * @remark      At the moment, it is not possible to tell whether it is sent or not 
         * because it is performed in the background. 
         * @todo        Maybe handle this with Ajax at later some point.
         */
        private function _sendEmailInBackground( $aInputs, $sPressedInputNameFlat, $sSubmitSectionID ) {
            
            $_sTranskentKey = 'apf_em_' . md5( $sPressedInputNameFlat . get_current_user_id() );
            $_aEmailOptions = $this->getTransient( $_sTranskentKey );
            $this->deleteTransient( $_sTranskentKey );

            $_aEmailOptions = $this->getAsArray( $_aEmailOptions ) + array(
                'to'            => '',
                'subject'       => '',
                'message'       => '',
                'headers'       => '',
                'attachments'   => '',
                'is_html'       => false,
                'from'          => '',
                'name'          => '',
            );

            $_sTransientKey  = 'apf_emd_' . md5( $sPressedInputNameFlat . get_current_user_id() );
            $_aFormEmailData = array(
                'email_options' => $_aEmailOptions,
                'input'         => $aInputs,
                'section_id'    => $sSubmitSectionID,
            );
            $_bIsSet = $this->setTransient( $_sTransientKey,  $_aFormEmailData, 100 );
            
            // Send the email in the background.
            wp_remote_get( 
                add_query_arg( 
                    array( 
                        'apf_action' => 'email',
                        'transient'  => $_sTransientKey,
                    ), 
                    admin_url( $GLOBALS[ 'pagenow' ] ) 
                ),
                array( 
                    'timeout'     => 0.01, 
                    'sslverify'   => false, 
                ) 
            );  
            
            // @remark      Not possible to tell whether it is sent or not at the moment because it is performed in the background.
            $_bSent      = $_bIsSet;    
            $this->oFactory->setSettingNotice( 
                $this->oFactory->oMsg->get( 
                    $this->getAOrB( 
                        $_bSent, 
                        'email_scheduled', 
                        'email_could_not_send' 
                    ) 
                ),
                $this->getAOrB( $_bSent, 'updated', 'error' )
            );
        
        }        
        /**
         * Removes the 'confirmation' key in the query url.
         * 
         * @since       3.4.5
         * @since       3.6.3       Moved from `AdminPageFramework_Validation`.
         * @callback    filter      setting_update_url_{class name}
         * @return      string
         */
        public function _replyToRemoveConfirmationQueryKey( $sSettingUpdateURL ) {
            return remove_query_arg( array( 'confirmation', ), $sSettingUpdateURL );
        }      
        
}
