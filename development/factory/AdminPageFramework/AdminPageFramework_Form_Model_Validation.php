<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Deals with validating submitted options.
 * 
 * @abstract
 * @since           3.0.0
 * @since           3.3.1       Changed the name from `AdminPageFramework_Setting_Validation`.
 * @extends         AdminPageFramework_Form_Model_Validation_Opiton
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Form_Model_Validation extends AdminPageFramework_Form_Model_Validation_Opiton {     
       
    /**
     * Handles the form submitted data.
     * 
     * If the form is submitted, it calls the validation callback method and reloads the page.
     * 
     * @since       3.1.0
     * @since       3.1.5       Moved from AdminPageFramework_Setting_Form.
     * @remark      This method is triggered after form elements are registered when the page is abut to be loaded with the `load_after_{instantiated class name}` hook.
     * @remark      The $_POST array will look like the below.
     *  <code>array(
     *      [option_page]       => APF_Demo
     *      [action]            => update
     *      [_wpnonce]          => d3f9bd2fbc
     *      [_wp_http_referer]  => /wp39x/wp-admin/edit.php?post_type=apf_posts&page=apf_builtin_field_types&tab=textfields
     *      [APF_Demo]          => Array (
     *          [text_fields] => Array( ...)
     *      )
     *      [page_slug]         => apf_builtin_field_types
     *      [tab_slug]          => textfields
     *      [_is_admin_page_framework] => ...
     *  )</code>
     *        
     */
    protected function _handleSubmittedData() {

        // 1. Verify the form submission. 
        if ( ! $this->_verifyFormSubmit() ) {
            return;
        }

        // 2. Apply user validation callbacks to the submitted data.
        // If only page-meta-boxes are used, it's possible that the option key element does not exist.
        
        // 2-1. Prepare the saved options 
        $_aDefaultOptions   = $this->oProp->getDefaultOptions( $this->oForm->aFields );
        $_aOptions          = $this->oUtil->addAndApplyFilter( 
            $this, 
            "validation_saved_options_{$this->oProp->sClassName}", 
            $this->oUtil->uniteArrays( $this->oProp->aOptions, $_aDefaultOptions ), 
            $this
        );
        
        // 2-2. Prepare the user submit input data. Copy one for parsing as $aInput will be merged with the default options.
        $_aInput     = $this->oUtil->getElementAsArray( $_POST, $this->oProp->sOptionKey, array() );
        $_aInput     = stripslashes_deep( $_aInput );  
        $_aInputRaw  = $_aInput; // for parsing
        
        // Merge the submitted input data with the default options. Now $_aInput is modified.       
        $_sTabSlug   = $this->oUtil->getElement( $_POST, 'tab_slug', '' );
        $_sPageSlug  = $this->oUtil->getElement( $_POST, 'page_slug', '' );
        $_aInput     = $this->oUtil->uniteArrays( 
            $_aInput, 
            $this->oUtil->castArrayContents( 
                $_aInput, 
                // do not include the default values of the submitted page's elements as they merge recursively
                $this->_removePageElements( $_aDefaultOptions, $_sPageSlug, $_sTabSlug )
            ) 
        );                

        // 3. Execute the submit_{...} actions.
        $_aSubmit           = $this->oUtil->getElementAsArray( $_POST, '__submit', array() );
        $_sSubmitSectionID  = $this->_getPressedSubmitButtonData( $_aSubmit, 'section_id' );
        $_sPressedFieldID   = $this->_getPressedSubmitButtonData( $_aSubmit, 'field_id' );
        $_sPressedInputID   = $this->_getPressedSubmitButtonData( $_aSubmit, 'input_id' );        
        $this->_doActions_submit( 
            $_aInput, 
            $_aOptions, 
            $_sPageSlug, 
            $_sTabSlug, 
            $_sSubmitSectionID, 
            $_sPressedFieldID, 
            $_sPressedInputID 
        );
        
        // 4. Validate the data.
        $_aStatus   = array( 'settings-updated' => true );        
        $_aInput    = $this->_validateSubmittedData( 
            $_aInput,       // submitted user input
            $_aInputRaw,    // without default values being merged.
            $_aOptions,     // stored options data 
            $_aStatus       // passed by referenc - gets updated in the method.
        ); 
        
        // 5. Save the data.
        $_bUpdated = false;
        if ( ! $this->oProp->_bDisableSavingOptions ) {  
            $_bUpdated = $this->oProp->updateOption( $_aInput );
        }

        // 6. Trigger the submit_after_{...} action hooks. [3.3.1+]
        $this->_doActions_submit_after( 
            $_aInput, 
            $_aOptions, 
            $_sPageSlug, 
            $_sTabSlug, 
            $_sSubmitSectionID, 
            $_sPressedFieldID,
            $_bUpdated
        );
       
        // 7. Reload the page with the update notice.
        exit( wp_redirect( $this->_getSettingUpdateURL( $_aStatus, $_sPageSlug, $_sTabSlug ) ) );
        
    }
        /**
         * Do the 'submit_...' actions.
         * @internal
         * @return      void
         * @since       3.5.3
         */
        private function _doActions_submit( $_aInput, $_aOptions, $_sPageSlug, $_sTabSlug, $_sSubmitSectionID, $_sPressedFieldID, $_sPressedInputID ) {
         
            // Warnings for deprecated hooks.
            if ( has_action( "submit_{$this->oProp->sClassName}_{$_sPressedInputID}" ) ) {
                trigger_error( 
                    'Admin Page Framework: ' . ' : ' 
                        . sprintf( 
                            __( 'The hook <code>%1$s</code>is deprecated. Use <code>%2$s</code> instead.', $this->oProp->sTextDomain ), 
                            "submit_{instantiated class name}_{pressed input id}", 
                            "submit_{instantiated class name}_{pressed field id}"
                        ), 
                    E_USER_WARNING 
                );
            }
            $this->oUtil->addAndDoActions(
                $this,
                array( 
                    // @todo deprecate the hook with the input ID
                    "submit_{$this->oProp->sClassName}_{$_sPressedInputID}",  // will be deprecated in near future release
                    $_sSubmitSectionID 
                        ? "submit_{$this->oProp->sClassName}_{$_sSubmitSectionID}_{$_sPressedFieldID}" 
                        : "submit_{$this->oProp->sClassName}_{$_sPressedFieldID}",
                    $_sSubmitSectionID 
                        ? "submit_{$this->oProp->sClassName}_{$_sSubmitSectionID}" 
                        : null, // if null given, the method will ignore it
                    isset( $_POST['tab_slug'] ) 
                        ? "submit_{$this->oProp->sClassName}_{$_sPageSlug}_{$_sTabSlug}"
                        : null, // if null given, the method will ignore it
                    "submit_{$this->oProp->sClassName}_{$_sPageSlug}",
                    "submit_{$this->oProp->sClassName}",
                ),
                // 3.3.1+ Added parameters to be passed
                $_aInput,
                $_aOptions,
                $this
            );     
            
        }
        /**
         * Do the 'submit_after_...' actions.
         * @internal
         * @return      void
         * @since       3.5.3
         */
        private function _doActions_submit_after( $_aInput, $_aOptions, $_sPageSlug, $_sTabSlug, $_sSubmitSectionID, $_sPressedFieldID, $_bUpdated ) {
            
            $this->oUtil->addAndDoActions(
                $this,
                array( 
                    $this->oUtil->getAOrB(
                        $_sSubmitSectionID,                        
                        "submit_after_{$this->oProp->sClassName}_{$_sSubmitSectionID}_{$_sPressedFieldID}",
                        "submit_after_{$this->oProp->sClassName}_{$_sPressedFieldID}"
                    ),
                    $this->oUtil->getAOrB(
                        $_sSubmitSectionID,
                        "submit_after_{$this->oProp->sClassName}_{$_sSubmitSectionID}",
                        null
                    ),
                    $this->oUtil->getAOrB(
                        isset( $_POST['tab_slug'] ),
                        "submit_after_{$this->oProp->sClassName}_{$_sPageSlug}_{$_sTabSlug}",
                        null
                    ),
                    "submit_after_{$this->oProp->sClassName}_{$_sPageSlug}",
                    "submit_after_{$this->oProp->sClassName}",
                ),
                // 3.3.1+ Added parameters to be passed
                $_bUpdated 
                    ? $_aInput 
                    : array(),
                $_aOptions,
                $this
            );                     
            
        }
        /**
         * Returns the url to reload.
         * 
         * Sanitizes the $_GET query key-values.
         * 
         * @since       3.4.1
         */
        private function _getSettingUpdateURL( array $aStatus, $sPageSlug, $sTabSlug ) {
            
            // Apply filters. This allows page-meta-box classes to insert the 'field_errors' key when they have validation errors.
            $aStatus = $this->oUtil->addAndApplyFilters(    // 3.4.1+
                $this, 
                array( 
                    "options_update_status_{$sPageSlug}_{$sTabSlug}",
                    "options_update_status_{$sPageSlug}", 
                    "options_update_status_{$this->oProp->sClassName}", 
                ), 
                $aStatus
            ); 
            
            // Drop the 'field_errors' key.
            $_aRemoveQueries = array();
            if ( ! isset( $aStatus[ 'field_errors' ] ) || ! $aStatus[ 'field_errors' ] ) {
                unset( $aStatus[ 'field_errors' ] );
                $_aRemoveQueries[] = 'field_errors';
            }        
         
            return $this->oUtil->addAndApplyFilters(    // 3.4.4+
                $this, 
                array( 
                    "setting_update_url_{$this->oProp->sClassName}", 
                ), 
                $this->oUtil->getQueryURL( $aStatus, $_aRemoveQueries, $_SERVER['REQUEST_URI'] )
            ); 
         
        }
        /**
         * Verifies the form submit.
         * 
         * @since       3.3.1
         * @internal
         * @return      boolean     True if it is verified; otherwise, false.
         */
        private function _verifyFormSubmit() {
            
            if ( 
                ! isset( 
                    // The framework specific keys
                    $_POST['_is_admin_page_framework'], // holds the form nonce
                    $_POST['page_slug'], 
                    $_POST['tab_slug'], 
                    $_POST['_wp_http_referer']
                ) 
            ) {     
                return false;
            }
            $_sRequestURI   = remove_query_arg( array( 'settings-updated', 'confirmation', 'field_errors' ), wp_unslash( $_SERVER['REQUEST_URI'] ) );
            $_sReffererURI  = remove_query_arg( array( 'settings-updated', 'confirmation', 'field_errors' ), $_POST['_wp_http_referer'] );
            if ( $_sRequestURI != $_sReffererURI ) { // see the function definition of wp_referer_field() in functions.php.
                return false;
            }
            
            $_sNonceTransientKey = 'form_' . md5( $this->oProp->sClassName . get_current_user_id() );
            if ( $_POST['_is_admin_page_framework'] !== $this->oUtil->getTransient( $_sNonceTransientKey ) ) {
                $this->setAdminNotice( $this->oMsg->get( 'nonce_verification_failed' ) );
                return false;
            }
            // Do not delete the nonce transient to let it vanish by itself. This allows the user to open multiple pages/tabs in their browser and save forms by switching pages/tabs.
            // $this->oUtil->deleteTransient( $_sNonceTransientKey );            
            
            return true;
            
        }
       
    /**
     * Validates the submitted user input.
     * 
     * @since       2.0.0
     * @since       3.3.0       Changed the name from _doValidationCall(). The input array is passed by reference and returns the status array.
     * @access      protected
     * @param       array       $aInput     The submitted form user input data merged with the default option values. The variable contents will be validated and merged with the original saved options.
     * @param       array       $aInputRaw  The submitted form user input data as a row array.
     * @param       array       $aOptions   The stored options (input) data.
     * @param       array       &$aStatus   A status array that will be inserted in the url $_GET query array in next page load, passed by reference.
     * @return      array       Returns the filtered validated input which will be saved in the options table.
     * @internal
     */ 
    protected function _validateSubmittedData( $aInput, $aInputRaw, $aOptions, &$aStatus ) {
        
        $_sTabSlug          = $this->oUtil->getElement( $_POST, 'tab_slug', '' );   // No need to retrieve the default tab slug here because it's an embedded value that is already set in the previous page. 
        $_sPageSlug         = $this->oUtil->getElement( $_POST, 'page_slug', '' );
        $_aSubmit           = $this->oUtil->getElementAsArray( $_POST, '__submit', array() );
        $_sPressedInputName = $this->_getPressedSubmitButtonData( $_aSubmit, 'name' );
        $_sSubmitSectionID  = $this->_getPressedSubmitButtonData( $_aSubmit, 'section_id' );
        
        // Submit Information - [3.5.0+] this will be passed to validation callback methods.
        $_aSubmitInformation        = array(
            'page_slug'     => $_sPageSlug,
            'tab_slug'      => $_sTabSlug,
            // 'input_name'    => $_sPressedInputName,  // removed as the format of it may change later at some point.
            'input_id'      => $this->_getPressedSubmitButtonData( $_aSubmit, 'input_id' ), 
            'section_id'    => $_sSubmitSectionID,
            'field_id'      => $this->_getPressedSubmitButtonData( $_aSubmit, 'field_id' ),
        );
        
        try {
            
            // Contact form
            $this->_doContactForm( 
                $aInputRaw,     // @todo Consider passing $aInput rather than $aInputRaw.
                $_aSubmit,
                $_sPressedInputName, 
                $_sSubmitSectionID
            );
            
            // Reset
            $this->_confirmReset(
                $aStatus,   // by reference, will be updated in the method
                $_aSubmit, 
                $_sPressedInputName, 
                $_sSubmitSectionID
            );
            
            // Link button (href) - will exit the script in the method if the link is set.
            $this->_goToLink( $_aSubmit );
                    
            // Redirect button (redirect_url)
            $this->_setRedirect( 
                $aStatus,   // by reference
                $_aSubmit,
                $_sPageSlug
            );
            
            // Form Validation
            $aInput           = $this->_getFilteredOptions( 
                $aInput, 
                $aInputRaw, 
                $aOptions, 
                $_aSubmitInformation,   // 3.5.0+
                $aStatus // 3.5.3+
            );
                           
            // Import - moved to after the validation callbacks (3.4.6+)
            $this->_doImportOptions(
                $_sPageSlug, 
                $_sTabSlug
            );
                     
            // Export - moved to after the validation callbacks (3.4.6+)
            $this->_doExportOptions(
                $_sPageSlug, 
                $_sTabSlug
            );
            
            // Reset - if the key to reset is not specified, it does nothing.
            $this->_doResetOptions(
                $_aSubmit,
                $aInput
            );

            // Email confirmation
            $this->_confirmContactForm( 
                $aStatus, 
                $_aSubmit, 
                $aInput, 
                $_sPressedInputName, 
                $_sSubmitSectionID 
            );
    
        } catch ( Exception $_oException ) {
            
            // Assuming the message serves as the property name to return.
            $_sPropertyName = $_oException->getMessage();
            if ( isset( $_oException->$_sPropertyName ) ) {
                return $_oException->{$_sPropertyName};
            }
            
            // If not set, return an empty array.
            return array();

        }           

        // Admin Notice & Return
        $this->_setSettingNoticeAfterValidation( empty( $aInput ) );
        return $aInput;
        
    }    
        /**
         * Sends a user set contact form as an email.
         * 
         * @remark      This should be done before the redirect because the user may set a redirect and email. In that case, send the email first and redirect to the set page.
         * @internal    
         * @since       3.5.3
         * @return      void
         */
        private function _doContactForm( $aInputRaw, array $_aSubmit, $_sPressedInputName, $_sSubmitSectionID ) {

            // Check whether sending an email has been confirmed by the user or not.
            $_bConfirmedToSendEmail     = ( bool ) $this->_getPressedSubmitButtonData( 
                $_aSubmit, 
                'confirmed_sending_email' 
            );
            if ( ! $_bConfirmedToSendEmail ) {
                return;
            }
            
            // At this point, the user has confirmed to send an email of a contact form.
            $this->_sendEmailInBackground( 
                $aInputRaw, 
                $_sPressedInputName, 
                $_sSubmitSectionID 
            );
            $this->oProp->_bDisableSavingOptions = true;
            $this->oUtil->deleteTransient( 'apf_tfd' . md5( 'temporary_form_data_' . $this->oProp->sClassName . get_current_user_id() ) );
            
            // Schedule to remove the confirmation url query key.
            add_action( "setting_update_url_{$this->oProp->sClassName}", array( $this, '_replyToRemoveConfirmationQueryKey' ) );
            
            // Go to the catch clause.
            $_oException = new Exception( 'aReturn' );  // the property name to return from the catch clasue.
            $_oException->aReturn = $aInputRaw;
            throw $_oException;
        
        }
        /**
         * Resets the entire / part of the stored options.
         * 
         * @since       3.53.3
         * @return      void
         * @internal
         */
        private function _confirmReset( array &$aStatus, array $_aSubmit, $_sPressedInputName, $_sSubmitSectionID ) {
           
            // if the 'reset' key in the field definition array is set, this value will be set.
            $_bIsReset = ( bool ) $this->_getPressedSubmitButtonData( 
                $_aSubmit, 
                'is_reset' 
            );  
            if ( ! $_bIsReset ) {
                return;
            }        
            $aStatus = $aStatus + array( 'confirmation' => 'reset' );
            
            // Go to the catch clause.
            $_oException = new Exception( 'aReturn' );  // the property name to return from the catch clasue.
            $_oException->aReturn = $this->_confirmSubmitButtonAction( 
                $_sPressedInputName, 
                $_sSubmitSectionID, 
                'reset' 
            );
            throw $_oException;        
            
        }        
        /**
         * If the associated submit button for the link is pressed, it will be redirected.
         * 
         * @since       3.5.3
         * @intrnal
         * @return      void
         */
        private function _goToLink( array $_aSubmit ) {
            $_sLinkURL = $this->_getPressedSubmitButtonData( $_aSubmit, 'href' );
            if ( ! $_sLinkURL ) {
                return;
            }
            exit( wp_redirect( $_sLinkURL ) ); 
        }
        /**
         * Sets a redirect url in a transient and confirmation message.
         * 
         * @since       3.5.3
         * @internal
         * @return      void
         */
        private function _setRedirect( array &$aStatus, $_aSubmit, $_sPageSlug ) {
            
            $_sRedirectURL = $this->_getPressedSubmitButtonData( 
                $_aSubmit, 
                'redirect_url' 
            );
            if ( ! $_sRedirectURL ) {
                return;
            }
            
            $aStatus = $aStatus + array( 'confirmation' => 'redirect' );
            $this->_setRedirectTransients( $_sRedirectURL, $_sPageSlug );
            
        }  
        /**
         * Handles importing options.
         * 
         * @internal
         * @since       3.5.3
         * @return      void
         */
        private function _doImportOptions( $_sPageSlug, $_sTabSlug ) {
            
            if ( $this->hasFieldError() ) {
                return;
            }
            if (
                ! isset( 
                    $_POST['__import']['submit'], 
                    $_FILES['__import'] 
                )
            ) {
                return;
            }
            // Import data are set.
            $_oException = new Exception( 'aReturn' );
            $_oException->aReturn = $this->_importOptions( 
                $this->oProp->aOptions, 
                $_sPageSlug, 
                $_sTabSlug 
            );
            throw $_oException;
            
        }    
        /**
         * Handles exporting options.
         * @sinec       3.5.3
         * @internal
         * @return      void
         */
        private function _doExportOptions( $_sPageSlug, $_sTabSlug ) {
            if ( $this->hasFieldError() ) {
                return;
            }
            if ( ! isset( $_POST['__export']['submit'] ) ) {
                return;
            }
            exit( 
                $this->_exportOptions( 
                    $this->oProp->aOptions, 
                    $_sPageSlug, 
                    $_sTabSlug
                ) 
            ); 
        }       
        /**
         * Handles resetting options.
         * 
         * @since       3.5.3
         * @return      void
         * @internal
         */
        private function _doResetOptions( array $_aSubmit, array $aInput ) {
            
            // this will be set if the user confirms the reset action.
            $_sKeyToReset = $this->_getPressedSubmitButtonData( 
                $_aSubmit, 
                'reset_key' 
            );
            $_sKeyToReset = trim( $_sKeyToReset );
            if ( ! $_sKeyToReset ) {
                return;
            }            
            $_oException = new Exception( 'aReturn' );
            $_oException->aReturn = $this->_resetOptions( 
                $_sKeyToReset, 
                $aInput 
            );
            throw $_oException;               
            
        }                
            /**
             * Performs resetting options.
             * 
             * @since       2.1.2
             * @remark      `$aInput` has only the page elements that called the validation callback. 
             * In other words, it does not hold other pages' option keys.
             * @return      array       The modified input array.
             */
            private function _resetOptions( $sKeyToReset, array $aInput ) {

                // As of 3.1.0, an empty value is accepted for the option key.
                if ( ! $this->oProp->sOptionKey ) {
                    return array();
                }
                
                // The key to delete is not specified.
                if ( in_array( $sKeyToReset, array( '1', ), true ) ) {
                    delete_option( $this->oProp->sOptionKey );
                    return array();
                }
                
                // The key to reset is specified.
                $_aDimensionalKeys = explode( '|', $sKeyToReset );
                $this->oUtil->unsetDimensionalArrayElement( $this->oProp->aOptions, $_aDimensionalKeys );
                $this->oUtil->unsetDimensionalArrayElement( $aInput, $_aDimensionalKeys );
              
                update_option( $this->oProp->sOptionKey, $this->oProp->aOptions );
                $this->setSettingNotice( $this->oMsg->get( 'specified_option_been_deleted' ) );
            
                return $aInput; // the returned array will be saved with the Settings API.
             
            }
            
        /**
         * Confirms contact form submittion.
         * @internal
         * @since       3.5.3
         * @return      void
         */
        private function _confirmContactForm( array &$aStatus, array $_aSubmit, array $aInput, $_sPressedInputName, $_sSubmitSectionID ) {
                        
            if ( $this->hasFieldError() ) {
                return;
            }
            $_bConfirmingToSendEmail    = ( bool ) $this->_getPressedSubmitButtonData( 
                $_aSubmit, 
                'confirming_sending_email' 
            );
            if ( ! $_bConfirmingToSendEmail ) {
                return;
            }
            
            $this->_setLastInput( $aInput );
            $this->oProp->_bDisableSavingOptions = true;
            $aStatus    = $aStatus + array( 'confirmation' => 'email' );
            
            // Go to the catch clause.
            $_oException = new Exception( 'aReturn' );  // the property name to return from the catch clasue.
            $_oException->aReturn = $this->_confirmSubmitButtonAction( 
                $_sPressedInputName, 
                $_sSubmitSectionID, 
                'email' 
            );
            throw $_oException;
            
        }                
                
        /**
         * Removes the 'confirmation' key in the query url.
         * 
         * @since   3.4.5
         */
        public function _replyToRemoveConfirmationQueryKey( $sSettingUpdateURL ) {
            return remove_query_arg( array( 'confirmation', ), $sSettingUpdateURL );
        }
    
        /**
         * Sends an email set via the form.
         * 
         * The email contents should be set with the form fields. 
         * 
         * @since       3.3.0
         * @remark      At the moment, it is not possible to tell whether it is sent or not 
         * because it is performed in the background.
         */
        private function _sendEmailInBackground( $aInput, $sPressedInputNameFlat, $sSubmitSectionID ) {
            
            $_sTranskentKey = 'apf_em_' . md5( $sPressedInputNameFlat . get_current_user_id() );
            $_aEmailOptions = $this->oUtil->getTransient( $_sTranskentKey );
            $this->oUtil->deleteTransient( $_sTranskentKey );

            $_aEmailOptions = $this->oUtil->getAsArray( $_aEmailOptions ) + array(
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
                'input'         => $aInput,
                'section_id'    => $sSubmitSectionID,
            );
            $_bIsSet = $this->oUtil->setTransient( $_sTransientKey,  $_aFormEmailData, 100 );
            
            // Send the email in the background.
            wp_remote_get( 
                add_query_arg( 
                    array( 
                        'apf_action' => 'email',
                        'transient'  => $_sTransientKey,
                    ), 
                    admin_url( $GLOBALS['pagenow'] ) 
                ),
                array( 
                    'timeout'     => 0.01, 
                    'sslverify'   => false, 
                ) 
            );  
            
            // @remark      Not possible to tell whether it is sent or not at the moment because it is performed in the background.
            $_bSent      = $_bIsSet;    
            $this->setSettingNotice( 
                $this->oMsg->get( $this->oUtil->getAOrB( $_bSent, 'email_scheduled', 'email_could_not_send' ) ),
                $this->oUtil->getAOrB( $_bSent, 'updated', 'error' )
            );
        
        }   
            
        /**
         * Confirms the given submit button action and sets a confirmation message as a field error message and admin notice.
         * 
         * @since   2.1.2
         * @since   3.3.0       Changed the name from _askResetOptions(). Deprecated the page slug parameter. Added the $sType parameter.
         * @return  array       The intact stored options.
         */
        private function _confirmSubmitButtonAction( $sPressedInputName, $sSectionID, $sType='reset' ) {
            
            switch( $sType ) {
                default:
                case 'reset':
                    $_sFieldErrorMessage = $this->oMsg->get( 'reset_options' );
                    $_sTransientKey      =  'apf_rc_' . md5( $sPressedInputName . get_current_user_id() );
                    break;
                case 'email':
                    $_sFieldErrorMessage = $this->oMsg->get( 'send_email' );
                    $_sTransientKey      =  'apf_ec_' . md5( $sPressedInputName . get_current_user_id() );
                    break;                
            }
            
            // Retrieve the pressed button's associated submit field ID.
            $_aNameKeys = explode( '|', $sPressedInputName );    
            $_sFieldID  = $this->oUtil->getAOrB(
                $sSectionID,
                $_aNameKeys[ 2 ], // OptionKey|section_id|field_id
                $_aNameKeys[ 1 ]  // OptionKey|field_id
            );
            
            // Set up the field error array to show a confirmation message just above the field besides the admin notice at the top of the page.
            $_aErrors = array();
            if ( $sSectionID ) {
                $_aErrors[ $sSectionID ][ $_sFieldID ] = $_sFieldErrorMessage;
            } else {
                $_aErrors[ $_sFieldID ] = $_sFieldErrorMessage;
            }
            $this->setFieldErrors( $_aErrors );
                
            // Set a flag that the confirmation is displayed
            $this->oUtil->setTransient( $_sTransientKey, $sPressedInputName, 60*2 );
            
            // Set the admin notice
            $this->setSettingNotice( $this->oMsg->get( 'confirm_perform_task' ), 'error confirmation' );
            
            // Their returned options will be saved so returned the saved options not to change anything.
            return $this->oProp->aOptions;
            
        }
             
        /**
         * Sets the given URL's transient.
         */
        private function _setRedirectTransients( $sURL, $sPageSlug ) {
            if ( empty( $sURL ) ) { return; }
            $_sTransient = 'apf_rurl' . md5( trim( "redirect_{$this->oProp->sClassName}_{$sPageSlug}" ) );
            return $this->oUtil->setTransient( $_sTransient, $sURL , 60*2 );
        }
        
        /**
         * Retrieves the target key's value associated with the given data to a custom submit button.
         * 
         * This method checks if the associated submit button is pressed with the input fields.
         * 
         * @since       2.0.0
         * @remark      The structure of the `$aPostElements` array looks like this:
         * <code>[submit_buttons_submit_button_field_0] => Array
         *      (
         *          [input_id] => submit_buttons_submit_button_field_0
         *          [field_id] => submit_button_field
         *          [name] => APF_Demo|submit_buttons|submit_button_field
         *          [section_id] => submit_buttons
         *      )
         *
         *  [submit_buttons_submit_button_link_0] => Array
         *      (
         *          [input_id] => submit_buttons_submit_button_link_0
         *          [field_id] => submit_button_link
         *          [name] => APF_Demo|submit_buttons|submit_button_link|0
         *          [section_id] => submit_buttons
         *      )
         * </code>
         * The keys are the input id.
         * @return      null|string     Returns `null` if no value is found and the associated link url if found. 
         * Otherwise, the found value.
         */ 
        private function _getPressedSubmitButtonData( array $aPostElements, $sTargetKey='field_id' ) {    

            foreach( $aPostElements as $_sInputID => $_aSubElements ) {
                
                // The 'name' key must be set.
                if ( ! isset( $_aSubElements[ 'name' ] ) ) {
                    continue;
                }
                $_aNameKeys = explode( '|', $_aSubElements[ 'name' ] ); 
                
                // If the element is not found, skip.
                if ( null === $this->oUtil->getElement( $_POST, $_aNameKeys, null ) ) {
                    continue;
                }
                
                // Return the associated value.
                return $this->oUtil->getElement(
                    $_aSubElements,
                    $sTargetKey,
                    null
                );
                
            }
            return null; // not found
            
        }
                
        /**
         * Removes option array elements that belong to the given page/tab by their slug.
         * 
         * This is used when merging options and avoiding merging options that have an array structure as the framework uses the recursive merge
         * and if an option is not a string but an array, the default array of such a structure will merge with the user input of the corresponding structure. 
         * This problem will occur with the select field type with multiple attribute enabled. 
         * 
         * @since       3.0.0
         */
        private function _removePageElements( $aOptions, $sPageSlug, $sTabSlug ) {
            
            if ( ! $sPageSlug && ! $sTabSlug ) { return $aOptions; }
            
            // If the tab is given
            if ( $sTabSlug && $sPageSlug ) {
                return $this->oForm->getOtherTabOptions( $aOptions, $sPageSlug, $sTabSlug );
            }
            
            // If only the page is given 
            return $this->oForm->getOtherPageOptions( $aOptions, $sPageSlug );
            
        }
            
    /**
     * Sets a setting notice after form validation.
     * 
     * @since       3.5.3
     * @internal
     * @return      void
     * @remark      Accessed from one of the parent classes.
     */
    protected function _setSettingNoticeAfterValidation( $bIsInputEmtpy ) {
     
        if ( $this->hasSettingNotice() ) {     
            return;
        }
        $this->setSettingNotice(  
            $this->oUtil->getAOrB( $bIsInputEmtpy, $this->oMsg->get( 'option_cleared' ), $this->oMsg->get( 'option_updated' ) ),
            $this->oUtil->getAOrB( $bIsInputEmtpy, 'error', 'updated' ),
            $this->oProp->sOptionKey, // the id
            false // do not override
        );
     
    }            
}