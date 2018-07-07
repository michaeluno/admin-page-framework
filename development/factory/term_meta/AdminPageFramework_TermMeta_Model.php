<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2018, Michael Uno; Licensed MIT
 * 
 */

/**
 * The model class of the factory class.
 *
 * @abstract
 * @since           3.8.0
 * @package         AdminPageFramework/Factory/TermMeta
 * @internal
 */
abstract class AdminPageFramework_TermMeta_Model extends AdminPageFramework_TermMeta_Router {

    /**
     * A validation callback method.
     * 
     * The user may just override this method instead of defining a `validation_{...}` callback method.
     * 
     * @since       3.8.0
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
     * @remark      Do not call the parant method as it triggers the `option_{...}` filter hook.
     * This class will set the data right before rendering the form fields as there is no way to find the term id.
     * @return      array       The saved form data.
     * @since       3.8.0
     * @internal
     */
    public function _replyToGetSavedFormData() {
        
        return array();    
    }
       
    /**
     * Sets the <var>$aOptions</var> property array in the property object. 
     * 
     * This array will be referred later in the `getFieldOutput()` method.
     * 
     * @since       unknown
     * @since       3.8.0
     * @internal
     */
    protected function _setOptionArray( $iTermID=null, $_deprecated=null ) {
        $this->oForm->aSavedData = $this->oUtil->addAndApplyFilter(
            $this, // the caller factory object
            'options_' . $this->oProp->sClassName,
            $this->_getSavedTermMetas( $iTermID, $this->oForm->aFieldsets )
            // @todo maybe pass the term id because the user will not know whihch form data is
        );        
    } 
        /**
         * Retrieves the term metas with the field-set keys.
         * @since       3.8.0
         * @return      array
         * @internal
         */
        private function _getSavedTermMetas( $iTermID, array $aFieldsets ) {

            $_oMetaData = new AdminPageFramework_TermMeta_Model___TermMeta(
                $iTermID,
                $this->oForm->aFieldsets
            );        
            return $_oMetaData->get();           
            
        }
        
    
    /**
     * Validates the given option array.
     * 
     * @since       3.8.0
     * @callback    action      created_{taxonomy slug}
     * @callback    action      edited_{taxonomy slug}
     * @internal
     */
    public function _replyToValidateOptions( $iTermID ) {

        if ( ! $this->_shouldProceedValidation() ) {
            return;
        }              

        $_aSavedFormData        = $this->_getSavedTermMetas( $iTermID, $this->oForm->aFieldsets );
        $_aSubmittedFormData    = $this->oForm->getSubmittedData( $_POST ); 
        $_aSubmittedFormData    = $this->oUtil->addAndApplyFilters( 
            $this, 
            'validation_' . $this->oProp->sClassName, 
            call_user_func_array(       // 1st param
                array( $this, 'validate' ), // triggers __call()
                array( $_aSubmittedFormData, $_aSavedFormData, $this )
            ), // 3.5.10+
            $_aSavedFormData,   // 2nd param
            $this   // 3rd param
        );
        
        // @todo Update term metas
        $this->oForm->updateMetaDataByType( 
            $iTermID,  // object id
            $_aSubmittedFormData,  // user submit form data
            $this->oForm->dropRepeatableElements( $_aSavedFormData ), // Drop repeatable section elements from the saved meta array.
            $this->oForm->sStructureType   // fields type
        );             
        
    }        
     
    
}
