<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 */

/**
 * The model class of the user factory class.
 *
 * @abstract
 * @since           3.5.0
 * @package         AdminPageFramework
 * @subpackage      UserMeta
 * @internal
 */
abstract class AdminPageFramework_UserMeta_Model extends AdminPageFramework_UserMeta_Router {
      
    /**
     * A validation callback method.
     * 
     * The user may just override this method instead of defining a `validation_{...}` callback method.
     * 
     * @since       3.4.1
     * @since       3.5.3       Moved from `AdminPageFramework_Factory_Model`.
     * @remark      Do not even declare this method to avoid PHP strict standard warnings.
     */
    // public function validate( $aInput, $aOldInput, $oFactory ) {
        // return $aInput;
    // }   

    /**
     * Called when the form object tries to set the form data from the database.
     * 
     * @callback    form        `saved_data`    
     * @remark      The `oOptions` property will be automatically set with the overload method.
     * @return      array       The saved form data.
     * @since       3.7.0
     */
    public function _replyToGetSavedFormData() {
                
        $_iUserID = isset( $GLOBALS[ 'profileuser' ]->ID )
            ? $GLOBALS[ 'profileuser' ]->ID
            : 0;

        $_oMetaData = new AdminPageFramework_UserMeta_Model___UserMeta(
            $_iUserID,
            $this->oForm->aFieldsets
        );
        $this->oProp->aOptions = $_oMetaData->get();
        
        // The parent method will handle applying filters with the set property object.
        return parent::_replyToGetSavedFormData();
    
    }

    /**
     * Saves the custom user profile field values.
     * 
     * @since       3.5.0
     * @internal
     */
    public function _replyToSaveFieldValues( $iUserID ) {

        if ( ! current_user_can( 'edit_user', $iUserID ) ) {
            return;
        }

        // Extract the fields data from $_POST
        // Retrieve the submitted data. 
        $_aInputs       = $this->oForm->getSubmittedData(
            $_POST,     // subject data to be parsed
            true,       // extract data with the fieldset structure
            false       // strip slashes
        );
        $_aInputsRaw    = $_aInputs; // store one for the last input array.

        // Prepare the saved data. For a new post, the id is set to 0.
        $_aSavedMeta   = $this->oUtil->getSavedUserMetaArray( $iUserID, array_keys( $_aInputs ) );
        
        // Apply filters to the array of the submitted values.
        $_aInputs = $this->oUtil->addAndApplyFilters(
            $this,
            "validation_{$this->oProp->sClassName}",
            call_user_func_array(
                array( $this, 'validate' ), // triggers __call()
                array( $_aInputs, $_aSavedMeta, $this )
            ), // 3.5.3+
            $_aSavedMeta,
            $this
        );
 
        // If there are validation errors. Change the post status to 'pending'.
        if ( $this->hasFieldError() ) {
            $this->setLastInputs( $_aInputsRaw );
        }
                            
        $this->oForm->updateMetaDataByType(
            $iUserID,  // object id
            $_aInputs,  // user submit form data
            $this->oForm->dropRepeatableElements( $_aSavedMeta ), // Drop repeatable section elements from the saved meta array.
            $this->oForm->sStructureType   // fields type
        );
        
    }
     
}
