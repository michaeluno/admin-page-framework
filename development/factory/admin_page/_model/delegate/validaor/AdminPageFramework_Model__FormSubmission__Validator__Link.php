<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to go to a specified page with a form submit button.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.3
 * @internal
 */
class AdminPageFramework_Model__FormSubmission__Validator__Link extends AdminPageFramework_Model__FormSubmission__Validator_Base {
        
    public $sActionHookPrefix = 'try_validation_before_';
    public $iHookPriority = 30;
    public $iCallbackParameters = 5;

    /**
     * Go to a specified page.
     * 
     * If the associated submit button for the link is pressed, it will be redirected.
     * 
     * @remark      Exits the script in the method if the link is set.
     * @since       3.5.3
     * @since       3.6.3       Moved from `AdminPageFramework_Validation`. Changed the name from `_goToLink()`.
     * @return      void
     * @internal
     * @callback    action      try_validation_before_{class name}
     */
    public function _replyToCallback( $aInputs, $aRawInputs, array $aSubmits, $aSubmitInformation, $oFactory ) {
        $_sLinkURL = $this->_getPressedSubmitButtonData( $aSubmits, 'href' );
        if ( ! $_sLinkURL ) {
            return;
        }
        $this->goToURL( $_sLinkURL );
    }
   
}
