<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to handle a contact form.
 *
 * @package     AdminPageFramework/Factory/AdminPage/Model
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
        $this->___sendEmail(
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
        private function ___sendEmail( $aInputs, $sPressedInputNameFlat, $sSubmitSectionID ) {

            $_sTransientKey = 'apf_em_' . md5( $sPressedInputNameFlat . get_current_user_id() );
            $_aEmailOptions = $this->getTransient( $_sTransientKey );
            $this->deleteTransient( $_sTransientKey );

            $_aEmailOptions = $this->getAsArray( $_aEmailOptions ) + array(
                'nonce'         => '',
                'to'            => '',
                'subject'       => '',
                'message'       => '',
                'headers'       => '',
                'attachments'   => '',
                'is_html'       => false,
                'from'          => '',
                'name'          => '',
            );

            if ( false === wp_verify_nonce( $_aEmailOptions[ 'nonce' ], 'apf_email_nonce_' . md5( ( string ) site_url() ) ) ) {
                $this->oFactory->setSettingNotice(
                    $this->oFactory->oMsg->get( 'nonce_verification_failed' ),
                    'error'
                );
                return;
            }

            $_oEmail = new AdminPageFramework_FormEmail(
                $_aEmailOptions,
                $aInputs,
                $sSubmitSectionID
            );
            $_bSent = $_oEmail->send();
            $this->oFactory->setSettingNotice(
                $this->oFactory->oMsg->get(
                    $this->getAOrB(
                        $_bSent,
                        'email_sent',
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
