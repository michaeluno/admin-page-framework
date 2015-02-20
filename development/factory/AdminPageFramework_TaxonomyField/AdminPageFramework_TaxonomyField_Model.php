<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
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
     */
    public function validate( $aInput, $aOldInput, $oFactory ) {
        return $aInput;
    }      
   
    /**
     * Modifies the columns of the taxonomy term listing table in the edit-tags.php page.
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
     * Registers form fields and sections.
     * 
     * @since       3.0.0
     * @since       3.5.0       Moved from `AdminPageFramework_TaxonomyField`.
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
     * Sets the <var>$aOptions</var> property array in the property object. 
     * 
     * This array will be referred later in the `getFieldOutput()` method.
     * 
     * @since       unknown
     * @since       3.0.0     the scope is changed to protected as the taxonomy field class redefines it.
     * @since       3.5.0       Moved from `AdminPageFramework_TaxonomyField`.
     * @internal
     * @todo        Add the `options_{instantiated class name}` filter.
     */
    protected function _setOptionArray( $iTermID=null, $sOptionKey ) {
                
        $aOptions               = get_option( $sOptionKey, array() );
        $this->oProp->aOptions  = isset( $iTermID, $aOptions[ $iTermID ] )
            ? $aOptions[ $iTermID ] 
            : array();

    }    
    
    /**
     * Validates the given option array.
     * 
     * @since       3.0.0
     * @since       3.5.0       Moved from `AdminPageFramework_TaxonomyField`.
     * @internal
     */
    public function _replyToValidateOptions( $iTermID ) {
                
        if ( ! $this->_verifyFormSubmit() ) {
            return;
        }              
        
        $aTaxonomyFieldOptions  = get_option( $this->oProp->sOptionKey, array() );
        $_aOldOptions           = $this->oUtil->getElementAsArray(
            $aTaxonomyFieldOptions,
            $iTermID,
            array()
        );
        $_aSubmittedOptions     = array();
        foreach( $this->oForm->aFields as $_sSectionID => $_aFields ) {
            foreach( $_aFields as $_sFieldID => $_aField ) {
                if ( isset( $_POST[ $_sFieldID ] ) ) {
                    $_aSubmittedOptions[ $_sFieldID ] = $_POST[ $_sFieldID ];
                }
            }
        }
            
        /* Apply validation filters to the submitted option array. */
        // @todo call the validate() method.
        $_aSubmittedOptions = $this->oUtil->addAndApplyFilters( 
            $this, 
            'validation_' . $this->oProp->sClassName, 
            $_aSubmittedOptions, 
            $_aOldOptions, 
            $this 
        );
        $aTaxonomyFieldOptions[ $iTermID ]  = $this->oUtil->uniteArrays( $_aSubmittedOptions, $_aOldOptions );
        update_option( $this->oProp->sOptionKey, $aTaxonomyFieldOptions );
        
    }        
        /**
         * Verifies the form submit.
         * 
         * @since       3.3.3
         * @internal
         * @return      boolean     True if it is verified; otherwise, false.
         */        
        private function _verifyFormSubmit() {

            if ( ! isset( $_POST[ $this->oProp->sClassHash ] ) ) { 
                return false;
            }
            if ( ! wp_verify_nonce( $_POST[ $this->oProp->sClassHash ], $this->oProp->sClassHash ) ) {
                return false;
            }        
            return true;
            
        }    
    
}