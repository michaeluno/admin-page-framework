<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2018, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to validate user form inputs.
 * 
 * @package     AdminPageFramework/Factory/AdminPage/Model
 * @since       3.6.3
 * @internal
 */
class AdminPageFramework_Model__FormSubmission__Validator extends AdminPageFramework_Model__FormSubmission__Validator_Base {
    
    public $oFactory;
    public $aInputs     = array();
    public $aRawInputs  = array();
    public $aOptions    = array();
    
    /**
     * Sets up properties and hooks.
     */
    public function __construct( $oFactory ) {
        
        $this->oFactory = $oFactory;
        
        add_filter(
            "validation_pre_" . $this->oFactory->oProp->sClassName,
            array( $this, '_replyToValiateUserFormInputs' ),
            10,
            4
        );
        
    }
        /**
         * Validates the submitted user inputs.
         * 
         * @since       2.0.0
         * @since       3.3.0       Changed the name from _doValidationCall(). The input array is passed by reference and returns the status array.
         * @since       3.6.3       Moved from `AdminPageFramework_Validation`. Changed the name from `_validateSubmittedData()`. Deprecated the 4th status parameter.
         * @param       array       $aInputs     The submitted form user input data merged with the default option values. The variable contents will be validated and merged with the original saved options.
         * @param       array       $aRawInputs  The submitted form user input data as a row array.
         * @param       array       $aOptions    The stored options (input) data.
         * @return      array       Returns the filtered validated inputs to be saved in the options table.
         * @internal
         * @callback        filter      validation_pre_{class name}
         */ 
        public function _replyToValiateUserFormInputs( $aInputs, $aRawInputs, $aOptions, $oFactory ) {
            
            // No need to retrieve the default tab slug here because it is an embedded value that is already set in the previous page load. 
            $_sTabSlug          = $this->getElement( $_POST, 'tab_slug', '' );   
            $_sPageSlug         = $this->getElement( $_POST, 'page_slug', '' );
            
            $_aSubmits          = $this->getElementAsArray( $_POST, '__submit', array() );
            $_sPressedInputName = $this->_getPressedSubmitButtonData( $_aSubmits, 'name' );
            $_sSubmitSectionID  = $this->_getPressedSubmitButtonData( $_aSubmits, 'section_id' );
            
            // Submit Information - [3.5.0+] this will be passed to validation callback methods.
            $_aSubmitsInformation        = array(
                'page_slug'     => $_sPageSlug,
                'tab_slug'      => $_sTabSlug,
                'input_id'      => $this->_getPressedSubmitButtonData( $_aSubmits, 'input_id' ), 
                'section_id'    => $_sSubmitSectionID,
                'field_id'      => $this->_getPressedSubmitButtonData( $_aSubmits, 'field_id' ),
                'input_name'    => $_sPressedInputName, // 3.6.3+ note that this value may not be accurate for fields with nested input tags.
            );
            
            // Hooks - before and after validation
            $_aClassNames = array(
                // before validation
                'AdminPageFramework_Model__FormSubmission__Validator__Link',
                'AdminPageFramework_Model__FormSubmission__Validator__Redirect',
                
                // after validation               
                'AdminPageFramework_Model__FormSubmission__Validator__Import',
                'AdminPageFramework_Model__FormSubmission__Validator__Export',
                'AdminPageFramework_Model__FormSubmission__Validator__Reset',
                'AdminPageFramework_Model__FormSubmission__Validator__ResetConfirm', // 3.7.6+ Moved to after validation from before validation
                'AdminPageFramework_Model__FormSubmission__Validator__ContactForm',  // 3.7.6+ Moved to after validation from before validation
                'AdminPageFramework_Model__FormSubmission__Validator__ContactFormConfirm',
                
            );
            foreach( $_aClassNames as $_sClassName ) {
                new $_sClassName( $this->oFactory );
            }
            
            // Validate
            try {
                
                $this->addAndDoActions(
                    $this->oFactory,
                    'try_validation_before_' . $this->oFactory->oProp->sClassName,
                    $aInputs,
                    $aRawInputs,
                    $_aSubmits,
                    $_aSubmitsInformation,
                    $this->oFactory
                );                       

                $_oFormSubmissionFilter = new AdminPageFramework_Model__FormSubmission__Validator__Filter(
                    $this->oFactory,
                    $aInputs, 
                    $aRawInputs, 
                    $aOptions, 
                    $_aSubmitsInformation   // 3.5.0+
                );
                $aInputs = $_oFormSubmissionFilter->get();

                $this->addAndDoActions(
                    $this->oFactory,
                    'try_validation_after_' . $this->oFactory->oProp->sClassName,
                    $aInputs,
                    $aRawInputs,
                    $_aSubmits,
                    $_aSubmitsInformation,
                    $this->oFactory
                );                
        
            } catch ( Exception $_oException ) {
                
                // Assuming the message serves as the property name to return.
                $_sPropertyName = $_oException->getMessage();
                if ( isset( $_oException->$_sPropertyName ) ) {
                    $this->_setSettingNoticeAfterValidation( empty( $_oException->{$_sPropertyName} ) );
                    return $_oException->{$_sPropertyName};
                }
                
                // If not set, return an empty array.
                return array();

            }           

            // Admin Notice & Return     
            $this->_setSettingNoticeAfterValidation( empty( $aInputs ) );  
            return $aInputs;
            
        }   

}
