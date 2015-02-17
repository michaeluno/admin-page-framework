<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
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
     * Registers form fields and sections.
     * 
     * @since       3.5.0
     * @internal
     */
    public function _replyToRegisterFormElements( $oScreen ) {
    
        $this->_loadFieldTypeDefinitions();
        
        // Format the fields array.
        $this->oForm->format();
        $this->oForm->applyConditions();
                
        // @todo    Examine whether applyFiltersToFields() should be performed here or not.
        // @todo    Examine whether setDynamicElements() should be performed here or not.
        $this->_registerFields( $this->oForm->aConditionedFields );  
        
    }    
 
        /**
         * Sets the options array.
         * 
         * @since       3.5.0
         * @internal
         */
        protected function _setOptionArray( $iUserID ) {

            if ( ! $iUserID ) {
                return;
            }
                        
            // Parse the registered fields
            $_aOptions = array();
            foreach( $this->oForm->aConditionedFields as $_sSectionID => $_aFields ) {
                
                if ( '_default' == $_sSectionID  ) {
                    foreach( $_aFields as $_aField ) {
                        $_aOptions[ $_aField['field_id'] ] = get_user_meta( $iUserID, $_aField['field_id'], true );
                    }
                }
                $_aOptions[ $_sSectionID ] = get_user_meta( $iUserID, $_sSectionID, true );
                
            }
            
            // Apply the filter to let third party scripts to set own options array.
            $_aOptions = $this->oUtil->addAndApplyFilter( // Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
                $this, // the caller object
                'options_' . $this->oProp->sClassName, // options_{instantiated class name}
                $_aOptions
            );

            $_aLastInput    = isset( $_GET['field_errors'] ) && $_GET['field_errors'] 
                ? $this->oProp->aLastInput 
                : array();
            $_aOptions      = $_aLastInput + $this->oUtil->getAsArray( $_aOptions );
            
            $this->oProp->aOptions = $_aOptions;

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
        $_aInput        = $this->oForm->getUserSubmitDataFromPOST( $this->oForm->aConditionedFields, $this->oForm->aConditionedSections );
        $_aInputRaw     = $_aInput; // store one for the last input array.

        // Prepare the saved data. For a new post, the id is set to 0.
        $_aSavedMeta    = $iUserID 
            ? $this->_getSavedMetaArray( $iUserID, array_keys( $_aInput ) )
            : array();
        
        // Apply filters to the array of the submitted values.
        $_aInput = $this->oUtil->addAndApplyFilters( 
            $this, 
            "validation_{$this->oProp->sClassName}",
            call_user_func_array( 
                array( $this, 'validate' ), // triggers __call()
                array( $_aInput, $_aSavedMeta, $this )
            ), // 3.5.3+
            $_aSavedMeta, 
            $this 
        ); 
 
        // If there are validation errors. Change the post status to 'pending'.
        if ( $this->hasFieldError() ) {
            $this->_setLastInput( $_aInputRaw );
        }
                            
        $this->oForm->updateMetaDataByType( 
            $iUserID,  // object id
            $_aInput,   // user submit form data
            $this->oForm->dropRepeatableElements( $_aSavedMeta ), // Drop repeatable section elements from the saved meta array.
            $this->oForm->sFieldsType   // fields type
        );            
        
    }
    
        /**
         * Retrieves the saved meta values from the given meta keys.
         * 
         * @since       3.5.0
         * @return      array       The saved meta data.
         * @internal
         */
        private function _getSavedMetaArray( $iUserID, array $aKeys ) {
                        
            $_aSavedMeta = array();
            foreach ( $aKeys as $_sKey ) {
                $_aSavedMeta[ $_sKey ] = get_post_meta( $iUserID, $_sKey, true );
            }
            return $_aSavedMeta;
            
        }    
           
}