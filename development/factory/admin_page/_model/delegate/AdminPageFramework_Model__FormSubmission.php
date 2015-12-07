<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Handles form submission.
 *
 * @since           3.6.3
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
class AdminPageFramework_Model__FormSubmission extends AdminPageFramework_Model__FormSubmission_Base {
        
    /**
     * Stores the factory object.
     * @since       3.6.3
     */
    public $oFactory;

    /**
     * Sets up hooks and properties.
     * @since       3.6.3
     */
    public function __construct( $oFactory, $aSavedData, $aArguments, $aSectionsets, $aFieldsets ) {
       
        $this->oFactory         = $oFactory;        
                
        // add_action
        // @deprecated      3.7.0
        // add_action( 
            // "load_after_{$this->oFactory->oProp->sClassName}", 
            // array( $this, '_replyToProcessFormData' ), 
            // 21  // lower priority - this must be called after form validation is done. 20: field registration, 21: validation handling 22: handle redirects
        // );                 
        $this->_handleFormData();
        
        new AdminPageFramework_Model__FormRedirectHandler( $oFactory );
                        
    }   
    
    /**
     * Handles the form submitted data.
     * 
     * If the form is submitted, it calls the validation callback method and reloads the page.
     * 
     * @since       3.1.0
     * @since       3.1.5       Moved from `AdminPageFramework_Setting_Form`.
     * @since       3.6.3       Moved from `AdminPageFramework_Validation`. Changed the name from `_handleSubmittedData()`.
     * @since       3.7.0      Changed the name from `_replyToProcessFormData()`.
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
     * @callback    action      load_after_{class name}
     */    
    public function _handleFormData() {
        
        if ( ! $this->_shouldProceed() ) {
            return;
        }

        $_sTabSlug   = $this->getElement( $_POST, 'tab_slug', '' );
        $_sPageSlug  = $this->getElement( $_POST, 'page_slug', '' );        
        
        // Apply user validation callbacks to the submitted data.
        // If only page-meta-boxes are used, it's possible that the option key element does not exist.
        
        // Prepare the saved options 
        $_aDefaultOptions   = $this->oFactory->oForm->getDefaultFormValues();
        $_aOptions          = $this->addAndApplyFilter( 
            $this->oFactory, 
            "validation_saved_options_{$this->oFactory->oProp->sClassName}", 
            // @todo    Examine whether recursive merging here is appropriate here or not 
            // for cases of a select field with the multiple options and repeatable fields with user-set default values.
            $this->uniteArrays( 
                $this->oFactory->oProp->aOptions, 
                $_aDefaultOptions 
            ), 
            $this->oFactory
        );
        
        // Prepare the user submit input data. Copy one for parsing as $aInput will be merged with the default options.
        // Merge the submitted input data with the default options. Now $_aInputs is modified.
        $_aRawInputs  = $this->_getUserInputsFromPOST();
        $_aInputs     = $this->uniteArrays( 
            $_aRawInputs, 
            $this->castArrayContents( 
                $_aRawInputs, 
                // do not include the default values of the submitted page's elements as they merge recursively
                $this->_removePageElements( $_aDefaultOptions, $_sPageSlug, $_sTabSlug )
            ) 
        );                

        // Execute the submit_{...} actions.
        $_aSubmits          = $this->getElementAsArray( $_POST, '__submit', array() );
        $_sSubmitSectionID  = $this->_getPressedSubmitButtonData( $_aSubmits, 'section_id' );
        $_sPressedFieldID   = $this->_getPressedSubmitButtonData( $_aSubmits, 'field_id' );
        $_sPressedInputID   = $this->_getPressedSubmitButtonData( $_aSubmits, 'input_id' );        
        $this->_doActions_submit( 
            $_aInputs, 
            $_aOptions, 
            $_sPageSlug, 
            $_sTabSlug, 
            $_sSubmitSectionID, 
            $_sPressedFieldID, 
            $_sPressedInputID 
        );
        
        // Validate the data.
        new AdminPageFramework_Model__FormSubmission__Validator( $this->oFactory );
        
        // [3.6.3+] Apply filters. All the sub-routines of handling form submit data use this filter hook.
        $_aInputs    = $this->addAndApplyFilters(    
            $this->oFactory, 
            "validation_pre_{$this->oFactory->oProp->sClassName}", 
            $_aInputs,
            $_aRawInputs,
            $_aOptions,
            $this->oFactory
        ); 
        
        // Save the data.
        $_bUpdated = false;
        if ( ! $this->oFactory->oProp->_bDisableSavingOptions ) {  
            $_bUpdated = $this->oFactory->oProp->updateOption( $_aInputs );
        }

        // Trigger the submit_after_{...} action hooks. [3.3.1+]
        $this->_doActions_submit_after( 
            $_aInputs, 
            $_aOptions, 
            $_sPageSlug, 
            $_sTabSlug, 
            $_sSubmitSectionID, 
            $_sPressedFieldID,
            $_bUpdated
        );
       
        // Reload the page with the update notice.
        $this->goToLocalURL( 
            $this->_getSettingUpdateURL( 
                // status array - this will be updated with filters.
                array(  
                    'settings-updated' => true 
                ), 
                $_sPageSlug, 
                $_sTabSlug 
            )            
        );        
        
    }
        /**
         * Checks if the form validation can be processed.
         * 
         * @since       3.3.1
         * @since       3.6.3       Moved from `AdminPageFramework_Model_Validation`. Changed the name from `_validateFormSubmit()`.
         * @internal
         * @return      boolean     True if it is verified; otherwise, false.
         */
        private function _shouldProceed() {
            
            if ( 
                ! isset( 
                    $_POST[ 'admin_page_framework_start' ], // indicates the framework form is started
                    $_POST[ '_wp_http_referer' ]
                ) 
            ) {     
                return false;
            }
            
            // Referrer
            $_sRequestURI   = remove_query_arg( array( 'settings-updated', 'confirmation', 'field_errors' ), wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) );
            $_sReffererURI  = remove_query_arg( array( 'settings-updated', 'confirmation', 'field_errors' ), $_POST[ '_wp_http_referer' ] );
            if ( $_sRequestURI != $_sReffererURI ) { // see the function definition of wp_referer_field() in functions.php.
                return false;
            }
            
            // Check if all the form fields are sent. 
            if (
                ! isset(
                    // these keys are supposed to be embedded at the end of the form.
                    // if the server truncates the form input values for `max_input_vars`, these will be lost in PHP 5.3.9 or above.
                    $_POST[ '_is_admin_page_framework' ], // holds the form nonce
                    $_POST[ 'page_slug' ], 
                    $_POST[ 'tab_slug' ]
                )
            ) {
                $this->oFactory->setAdminNotice( 
                    sprintf( 
                        $this->oFactory->oMsg->get( 'check_max_input_vars' ),
                        function_exists( 'ini_get' ) 
                            ? ini_get( 'max_input_vars' )
                            : 'unknown',
                        count( $_POST, COUNT_RECURSIVE )
                    )                    
                );
                return false;
            }
                        
            $_sNonceTransientKey = 'form_' . md5( $this->oFactory->oProp->sClassName . get_current_user_id() );
            if ( $_POST[ '_is_admin_page_framework' ] !== $this->getTransient( $_sNonceTransientKey ) ) {
                $this->oFactory->setAdminNotice( $this->oFactory->oMsg->get( 'nonce_verification_failed' ) );
                return false;
            }
            // Do not delete the nonce transient to let it vanish by itself. This allows the user to open multiple pages/tabs in their browser and save forms by switching pages/tabs.
            // $this->deleteTransient( $_sNonceTransientKey );            
            
            return true;
            
        }        
        
        /**
         * @since       3.6.3
         * @return      array
         */
        private function _getUserInputsFromPOST() {

            $_aInputs     = $this->getElementAsArray( 
                $_POST, 
                $this->oFactory->oProp->sOptionKey, 
                array() 
            );
            return $this->oFactory->oForm->getSubmittedData(
                $_aInputs,
                false   // do not extract from form fieldsets structure
            );
            // $_aInputs     = stripslashes_deep( $_aInputs );  
            // return $this->oFactory->oForm->getSortedInputs( $_aInputs ); // 3.6.0+
        
        }        
    
        /**
         * Do the 'submit_...' actions.
         * 
         * @internal
         * @return      void
         * @since       3.5.3
         * @since       3.6.3       Moved from `AdminPageFramework_Validation`.
         */
        private function _doActions_submit( $_aInputs, $_aOptions, $_sPageSlug, $_sTabSlug, $_sSubmitSectionID, $_sPressedFieldID, $_sPressedInputID ) {
         
            // Warnings for deprecated hooks.
            if ( has_action( "submit_{$this->oFactory->oProp->sClassName}_{$_sPressedInputID}" ) ) {
                trigger_error( 
                    'Admin Page Framework: ' . ' : ' 
                        . sprintf( 
                            __( 'The hook <code>%1$s</code>is deprecated. Use <code>%2$s</code> instead.', $this->oFactory->oProp->sTextDomain ), 
                            "submit_{instantiated class name}_{pressed input id}", 
                            "submit_{instantiated class name}_{pressed field id}"
                        ), 
                    E_USER_WARNING 
                );
            }
            $this->addAndDoActions(
                $this->oFactory,
                array( 
                    // @todo deprecate the hook with the input ID
                    "submit_{$this->oFactory->oProp->sClassName}_{$_sPressedInputID}",  // will be deprecated in near future release
                    $_sSubmitSectionID 
                        ? "submit_{$this->oFactory->oProp->sClassName}_{$_sSubmitSectionID}_{$_sPressedFieldID}" 
                        : "submit_{$this->oFactory->oProp->sClassName}_{$_sPressedFieldID}",
                    $_sSubmitSectionID 
                        ? "submit_{$this->oFactory->oProp->sClassName}_{$_sSubmitSectionID}" 
                        : null, // if null given, the method will ignore it
                    isset( $_POST[ 'tab_slug' ] ) 
                        ? "submit_{$this->oFactory->oProp->sClassName}_{$_sPageSlug}_{$_sTabSlug}"
                        : null, // if null given, the method will ignore it
                    "submit_{$this->oFactory->oProp->sClassName}_{$_sPageSlug}",
                    "submit_{$this->oFactory->oProp->sClassName}",
                ),
                // 3.3.1+ Added parameters to be passed
                $_aInputs,
                $_aOptions,
                $this->oFactory
            );     
            
        }
        /**
         * Do the 'submit_after_...' actions.
         * 
         * @internal
         * @return      void
         * @since       3.5.3
         * @since       3.6.3       Moved from `AdminPageFramework_Validation`.
         */
        private function _doActions_submit_after( $_aInputs, $_aOptions, $_sPageSlug, $_sTabSlug, $_sSubmitSectionID, $_sPressedFieldID, $_bUpdated ) {
            
            $this->addAndDoActions(
                $this->oFactory,
                array( 
                    $this->getAOrB(
                        $_sSubmitSectionID,                        
                        "submit_after_{$this->oFactory->oProp->sClassName}_{$_sSubmitSectionID}_{$_sPressedFieldID}",
                        "submit_after_{$this->oFactory->oProp->sClassName}_{$_sPressedFieldID}"
                    ),
                    $this->getAOrB(
                        $_sSubmitSectionID,
                        "submit_after_{$this->oFactory->oProp->sClassName}_{$_sSubmitSectionID}",
                        null
                    ),
                    $this->getAOrB(
                        isset( $_POST[ 'tab_slug' ] ),
                        "submit_after_{$this->oFactory->oProp->sClassName}_{$_sPageSlug}_{$_sTabSlug}",
                        null
                    ),
                    "submit_after_{$this->oFactory->oProp->sClassName}_{$_sPageSlug}",
                    "submit_after_{$this->oFactory->oProp->sClassName}",
                ),
                // 3.3.1+ Added parameters to be passed
                $_bUpdated 
                    ? $_aInputs 
                    : array(),
                $_aOptions,
                $this->oFactory
            );                     
            
        }
        /**
         * Returns the url to reload.
         * 
         * Sanitizes the $_GET query key-values.
         * 
         * @since       3.4.1
         * @since       3.6.3       Moved from `AdminPageFramework_Validation`.
         */
        private function _getSettingUpdateURL( array $aStatus, $sPageSlug, $sTabSlug ) {
            
            // Apply filters. This allows page-meta-box classes to insert the 'field_errors' key when they have validation errors.
            $aStatus = $this->addAndApplyFilters(    // 3.4.1+
                $this->oFactory, 
                array( 
                    "options_update_status_{$sPageSlug}_{$sTabSlug}",
                    "options_update_status_{$sPageSlug}", 
                    "options_update_status_{$this->oFactory->oProp->sClassName}", 
                ), 
                $aStatus
            ); 
            
            // Drop the 'field_errors' key.
            $_aRemoveQueries = array();
            if ( ! isset( $aStatus[ 'field_errors' ] ) || ! $aStatus[ 'field_errors' ] ) {
                unset( $aStatus[ 'field_errors' ] );
                $_aRemoveQueries[] = 'field_errors';
            }        
         
            return $this->addAndApplyFilters(    // 3.4.4+
                $this->oFactory, 
                array( 
                    "setting_update_url_{$this->oFactory->oProp->sClassName}", 
                ), 
                $this->getQueryURL( $aStatus, $_aRemoveQueries, $_SERVER[ 'REQUEST_URI' ] )
            ); 
         
        }

        /**
         * Removes option array elements that belong to the given page/tab by their slug.
         * 
         * This is used when merging options and avoiding merging options that have an array structure as the framework performs recursive merges
         * and if an option is not a string but an array, the default array of such a structure will merge with the user input of the corresponding structure. 
         * This problem will occur with the select field type with multiple attribute enabled. 
         * 
         * @since       3.0.0
         * @since       3.6.3       Moved from `AdminPageFramework_Form_Model_Validation`.
         */
        private function _removePageElements( $aOptions, $sPageSlug, $sTabSlug ) {
            
            if ( ! $sPageSlug && ! $sTabSlug ) {
                return $aOptions;
            }
            
            // If the tab is given
            if ( $sTabSlug && $sPageSlug ) {
                return $this->oFactory->oForm->getOtherTabOptions( $aOptions, $sPageSlug, $sTabSlug );
            }
            
            // If only the page is given 
            return $this->oFactory->oForm->getOtherPageOptions( $aOptions, $sPageSlug );
            
        }        
        
}