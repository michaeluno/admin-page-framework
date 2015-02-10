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
 * @internal
 */
abstract class AdminPageFramework_TaxonomyField_Model extends AdminPageFramework_TaxonomyField_Router {
   
    /**
     * Modifies the columns of the taxonomy term listing table in the edit-tags.php page.
     * 
     * @internal
     * @since       3.0.0
     * @since       3.5.0       Moved from `AdminPageFramework_TaxonomyField`.
     */
    public function _replyToManageColumns( $aColumns ) {

        /* By default something like this is passed.
            Array (
                [cb] => <input type="checkbox" />
                [name] => Name
                [description] => Description
                [slug] => Slug
                [posts] => Admin Page Framework
            ) 
         */
        if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] ) {
            $aColumns = $this->oUtil->addAndApplyFilter( $this, "columns_{$_GET['taxonomy']}", $aColumns );
        }
        $aColumns = $this->oUtil->addAndApplyFilter( $this, "columns_{$this->oProp->sClassName}", $aColumns );    
        return $aColumns;
        
    }
    
    /**
     * Sets the taxonomy term listing table column elements.
     * 
     * @internal
     * @since       3.0.0
     * @since       3.5.0       Moved from `AdminPageFramework_TaxonomyField`.
     */
    public function _replyToSetSortableColumns( $aSortableColumns ) {

        if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] ) {
            $aSortableColumns = $this->oUtil->addAndApplyFilter( $this, "sortable_columns_{$_GET['taxonomy']}", $aSortableColumns );
        }
        $aSortableColumns = $this->oUtil->addAndApplyFilter( $this, "sortable_columns_{$this->oProp->sClassName}", $aSortableColumns );
        return $aSortableColumns;
        
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
     * #internal
     * @todo        Add the `options_{instantiated class name}` filter.
     */
    protected function _setOptionArray( $iTermID=null, $sOptionKey ) {
                
        $aOptions               = get_option( $sOptionKey, array() );
        $this->oProp->aOptions  = isset( $iTermID, $aOptions[ $iTermID ] ) ? $aOptions[ $iTermID ] : array();

    }    
    
    /**
     * Validates the given option array.
     * 
     * @since       3.0.0
     * @since       3.5.0       Moved from `AdminPageFramework_TaxonomyField`.
     * @internal
     */
    public function _replyToValidateOptions( $iTermID ) {
        
        if ( ! isset( $_POST[ $this->oProp->sClassHash ] ) ) { return; }
        if ( ! wp_verify_nonce( $_POST[ $this->oProp->sClassHash ], $this->oProp->sClassHash ) ) { return; }
        
        $aTaxonomyFieldOptions  = get_option( $this->oProp->sOptionKey, array() );
        $aOldOptions            = isset( $aTaxonomyFieldOptions[ $iTermID ] ) ? $aTaxonomyFieldOptions[ $iTermID ] : array();
        $aSubmittedOptions      = array();
        foreach( $this->oForm->aFields as $_sSectionID => $_aFields ) {
            foreach( $_aFields as $_sFieldID => $_aField ) {
                if ( isset( $_POST[ $_sFieldID ] ) ) {
                    $aSubmittedOptions[ $_sFieldID ] = $_POST[ $_sFieldID ];
                }
            }
        }
            
        /* Apply validation filters to the submitted option array. */
        // @todo call the validate() method.
        $aSubmittedOptions                  = $this->oUtil->addAndApplyFilters( $this, 'validation_' . $this->oProp->sClassName, $aSubmittedOptions, $aOldOptions, $this );        
        $aTaxonomyFieldOptions[ $iTermID ]  = $this->oUtil->uniteArrays( $aSubmittedOptions, $aOldOptions );
        update_option( $this->oProp->sOptionKey, $aTaxonomyFieldOptions );
        
    }           
    
}