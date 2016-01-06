<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to validate user form inputs.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.3
 * @internal
 */
class AdminPageFramework_Model__FormSubmission__Validator_Base extends AdminPageFramework_Model__FormSubmission_Base {
    
    public $oFactory;
    public $sHookType = 'action'; // or 'filter'
    public $sActionHookPrefix = 'try_validation_before_';
    public $iHookPriority = 10;
    public $iCallbackParameters = 5;
    public $sCallbackName = '_replyToCallback';
    
    /**
     * Sets up properties and hooks.
     */
    public function __construct( $oFactory ) {
        
        $this->oFactory = $oFactory;
        
        $_sFunctionName = 'action' === $this->sHookType
            ? 'add_action'
            : 'add_filter';
            
        $_sFunctionName(
            $this->sActionHookPrefix . $this->oFactory->oProp->sClassName,
            array( $this, $this->sCallbackName ),
            $this->iHookPriority,
            $this->iCallbackParameters
        );
        
    }
    
    // public function _replyToCallback() {}
    
    // Shared methods.
    
    /**
     * Confirms the given submit button action and sets a confirmation message as a field error message and admin notice.
     * 
     * @since   2.1.2
     * @since   3.3.0       Changed the name from _askResetOptions(). Deprecated the page slug parameter. Added the $sType parameter.
     * @since   3.6.3       Moved from `AdminPageFramework_Validation`.
     * @return  array       The intact stored options.
     */
    protected function _confirmSubmitButtonAction( $sPressedInputName, $sSectionID, $sType='reset' ) {
        
        switch( $sType ) {
            default:
            case 'reset':
                $_sFieldErrorMessage = $this->oFactory->oMsg->get( 'reset_options' );
                $_sTransientKey      =  'apf_rc_' . md5( $sPressedInputName . get_current_user_id() );
                break;
            case 'email':
                $_sFieldErrorMessage = $this->oFactory->oMsg->get( 'send_email' );
                $_sTransientKey      =  'apf_ec_' . md5( $sPressedInputName . get_current_user_id() );
                break;                
        }
        
        // Retrieve the pressed button's associated submit field ID.
        $_aNameKeys = explode( '|', $sPressedInputName ) + array( '', '', '' );
        $_sFieldID  = $this->getAOrB(
            $sSectionID,
            $_aNameKeys[ 2 ], // OptionKey|section_id|field_id
            $_aNameKeys[ 1 ]  // OptionKey|field_id
        );
        
        // Set up the field error array to show a confirmation message just above the field besides the admin notice at the top of the page.
        $_aErrors = array();
        if ( $sSectionID && $_sFieldID ) {
            $_aErrors[ $sSectionID ][ $_sFieldID ] = $_sFieldErrorMessage;
        } else if ( $_sFieldID ) {
            $_aErrors[ $_sFieldID ] = $_sFieldErrorMessage;
        }
        $this->oFactory->setFieldErrors( $_aErrors );
            
        // Set a flag that the confirmation is displayed
        $this->setTransient( $_sTransientKey, $sPressedInputName, 60*2 );
        
        // Set the admin notice
        $this->oFactory->setSettingNotice( $this->oFactory->oMsg->get( 'confirm_perform_task' ), 'error confirmation' );
        
        // Their returned options will be saved so returned the saved options not to change anything.
        return $this->oFactory->oProp->aOptions;
        
    }      
    
}