<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides hared methods for form validation.
 * 
 * @package     AdminPageFramework/Factory/AdminPage/Model
 * @extends     AdminPageFramework_WPUtility
 * @since       3.6.3
 * @since       3.8.17      Made it extend `AdminPageFramework_Form_Utility` from `AdminPageFramework_FrameworkUtility`.
 * @internal
 * @extends     AdminPageFramework_Form_Utility
 */
abstract class AdminPageFramework_Model__FormSubmission_Base extends AdminPageFramework_Form_Utility {

    /**
     * Retrieves the target key's value associated with the given data to a custom submit button.
     * 
     * This method checks if the associated submit button is pressed with the input fields.
     * 
     * @since       2.0.0
     * @since       3.6.3       Moved from `AdminPageFramework_Validation`.
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
    protected function _getPressedSubmitButtonData( array $aPostElements, $sTargetKey='field_id' ) {    

        foreach( $aPostElements as $_sInputID => $_aSubElements ) {
            
            // The 'name' key must be set.
            if ( ! isset( $_aSubElements[ 'name' ] ) ) {
                continue;
            }
            $_aNameKeys = explode( '|', $_aSubElements[ 'name' ] ); 
            
            // If the element is not found, skip.
            if ( null === $this->getElement( $_POST, $_aNameKeys, null ) ) {
                continue;
            }
            
            // Return the associated value.
            return $this->getElement(
                $_aSubElements,
                $sTargetKey,
                null
            );
            
        }
        return null; // not found
        
    }
    
    /**
     * Sets a setting notice after form validation.
     * 
     * @since       3.5.3
     * @since       3.6.3       Moved from `AdminPageFramework_Form_Model_Validation_Option`.
     * @internal
     * @return      void
     * @remark      Accessed from some of the parent/child classes.
     */
    protected function _setSettingNoticeAfterValidation( $bIsInputEmtpy ) {
     
        if ( $this->oFactory->hasSettingNotice() ) {     
            return;
        }
        $this->oFactory->setSettingNotice(  
            $this->getAOrB( 
                $bIsInputEmtpy, 
                $this->oFactory->oMsg->get( 'option_cleared' ), 
                $this->oFactory->oMsg->get( 'option_updated' ) 
            ),
            'updated',
            $this->oFactory->oProp->sOptionKey, // the id
            false // do not override
        );
     
    }                 
        
}
