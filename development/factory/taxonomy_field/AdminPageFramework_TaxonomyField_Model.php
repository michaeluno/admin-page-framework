<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * The model class of the factory class.
 *
 * @abstract
 * @since           3.5.0
 * @package         AdminPageFramework
 * @subpackage      TaxonomyField
 */
abstract class AdminPageFramework_TaxonomyField_Model extends AdminPageFramework_TaxonomyField_Router {

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
     * Modifies the columns of the taxonomy term listing table in the edit-tags.php/term.php page.
     * 
     * @internal
     * @since       3.0.0
     * @since       3.5.0       Moved from `AdminPageFramework_TaxonomyField`.
     * @param       array       $aColumns
     * By default something like this is passed.
     * <code>
     * Array (
     *      [cb]            => <input type="checkbox" />
     *      [name]          => Name
     *      [description]   => Description
     *      [slug]          => Slug
     *      [posts]         => Admin Page Framework
     * ) 
     * </code>
     * @callback    filter      manage_edit-{taxonomy slug}_columns
     * @return      string
     */
    public function _replyToManageColumns( $aColumns ) {
        return $this->_getFilteredColumnsByFilterPrefix( 
            $this->oUtil->getAsArray( $aColumns ), 
            'columns_', 
            isset( $_GET['taxonomy'] )  // in ajax, $_GET is not even set.
                ? $_GET['taxonomy']
                : ''
        );        
    }
    /**
     * Sets the taxonomy term listing table column elements.
     * 
     * @internal
     * @since       3.0.0
     * @since       3.5.0       Moved from `AdminPageFramework_TaxonomyField`.
     * @callback    filter      manage_edit-{taxonomy slug}_sortable_columns
     * @return      string
     */
    public function _replyToSetSortableColumns( $aSortableColumns ) {
        return $this->_getFilteredColumnsByFilterPrefix( 
            $this->oUtil->getAsArray( $aSortableColumns ), 
            'sortable_columns_', 
            isset( $_GET['taxonomy'] )  // in ajax, $_GET is not even set.
                ? $_GET['taxonomy']
                : ''
        );
    }   
        /**
         * Filters columns array by the given filter prefix.
         * @since       3.5.3
         * @return      array
         */
        private function _getFilteredColumnsByFilterPrefix( array $aColumns, $sFilterPrefix, $sTaxonomy ) {
            
            if ( $sTaxonomy ) {
                $aColumns = $this->oUtil->addAndApplyFilter(
                    $this, 
                    "{$sFilterPrefix}{$_GET['taxonomy']}",
                    $aColumns
                );
            }
            return $this->oUtil->addAndApplyFilter( 
                $this, 
                "{$sFilterPrefix}{$this->oProp->sClassName}",
                $aColumns
            );
        
        }
   
    /**
     * Called when the form object tries to set the form data from the database.
     * 
     * @callback    form        `saved_data`    
     * @remark      The `oOptions` property will be automatically set with the overload method.
     * @remark      Do not call the parant method as it triggers the `option_{...}` filter hook.
     * This class will set the data right before rendering the form fields as there is no way to find the term id.
     * @return      array       The saved form data.
     * @since       3.7.0
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
     * @since       3.0.0       The scope is changed to protected as the taxonomy field class redefines it.
     * @since       3.5.0       Moved from `AdminPageFramework_TaxonomyField`.
     * @since       3.7.0      No longer sets the value to `$this-oProp->aOptions` but to the form peoperty.
     * @internal
     */
    protected function _setOptionArray( $iTermID=null, $sOptionKey ) {
        $this->oForm->aSavedData = $this->_getSavedFormData( 
            $iTermID, 
            $sOptionKey
        );
    } 
        /**
         * @remark      The returned values are portion of the entire data set to the options table row
         * as the row stores all the form data associated with the taxonomy slug. And each element with the key of term id holds 
         * each form data of the term.
         * @return      array
         */
        private function _getSavedFormData( $iTermID, $sOptionKey ) {
            
            return $this->oUtil->addAndApplyFilter(
                $this, // the caller factory object
                'options_' . $this->oProp->sClassName,
                $this->_getSavedTermFormData( $iTermID, $sOptionKey )
                // @todo maybe pass the term id because the user will not know whihch form data is
            );
            
        }
        /**
         * @return      array
         */
        private function _getSavedTermFormData( $iTermID, $sOptionKey ) {
            $_aSavedTaxonomyFormData = $this->_getSavedTaxonomyFormData( $sOptionKey );
            return $this->oUtil->getElementAsArray(
                $_aSavedTaxonomyFormData,
                $iTermID
            );
        }
        /**
         * @return      array
         */
        private function _getSavedTaxonomyFormData( $sOptionKey ) {
            return get_option( $sOptionKey, array() );
        }
    
    /**
     * Validates the given option array.
     * 
     * @since       3.0.0
     * @since       3.5.0       Moved from `AdminPageFramework_TaxonomyField`.
     * @callback    action      created_{taxonomy slug}
     * @callback    action      edited_{taxonomy slug}
     * @internal
     */
    public function _replyToValidateOptions( $iTermID ) {

        if ( ! $this->_shouldProceedValidation() ) {
            return;
        }              

        $_aTaxonomyFormData     = $this->_getSavedTaxonomyFormData( $this->oProp->sOptionKey );
        $_aSavedFormData        = $this->_getSavedTermFormData( $iTermID, $this->oProp->sOptionKey );
        $_aSubmittedFormData    = $this->oForm->getSubmittedData( $_POST ); 
        $_aSubmittedFormData    = $this->oUtil->addAndApplyFilters( 
            $this, 
            'validation_' . $this->oProp->sClassName, 
            call_user_func_array( 
                array( $this, 'validate' ), // triggers __call()
                array( $_aSubmittedFormData, $_aSavedFormData, $this )
            ), // 3.5.10+
            $_aSavedFormData, 
            $this 
        );
        
        // @todo Examine whether it is appropriate to merge recursivly 
        // as some fields will have a problem such as select with multiple options.
        $_aTaxonomyFormData[ $iTermID ]  = $this->oUtil->uniteArrays( 
            $_aSubmittedFormData, 
            $_aSavedFormData 
        );
                 
        update_option( 
            $this->oProp->sOptionKey, 
            $_aTaxonomyFormData 
        );
        
    }        
        /**
         * Verifies the form submit.
         * 
         * @since       3.3.3
         * @since       3.7.0      Renamed from `_verifyFormSubmit()`.
         * @internal
         * @return      boolean     True if it is verified; otherwise, false.
         */        
        private function _shouldProceedValidation() {

            if ( ! isset( $_POST[ $this->oProp->sClassHash ] ) ) { 
            
                return false;
            }
            if ( ! wp_verify_nonce( $_POST[ $this->oProp->sClassHash ], $this->oProp->sClassHash ) ) {
                return false;
            }        
            return true;
            
        }    
    
}
