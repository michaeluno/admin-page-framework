<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Deals with displaying outputs of forms.
 *
 * @abstract
 * @since           3.3.1
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Form_View extends AdminPageFramework_Form_Model {
    
    
    /**
     * Returns the output of the filtered section description.
     * 
     * @remark      An alternative to `_renderSectionDescription()`.
     * @since       3.0.0
     * @since       3.3.1       Moved from `AdminPageFramework_Setting_Base`.
     * @internal
     */
    public function _replyToGetSectionHeaderOutput( $sSectionDescription, $aSection ) {

        return $this->oUtil->addAndApplyFilters(
            $this,
            array( 'section_head_' . $this->oProp->sClassName . '_' . $aSection['section_id'] ), // section_{instantiated class name}_{section id}
            $sSectionDescription
        );     
        
    }
        
    /**
     * Returns the output of the given field.
     * 
     * @since       3.0.0
     * @since       3.3.1       Moved from `AdminPageFramework_Setting_Base`.
     * @internal
     */  
    public function _replyToGetFieldOutput( $aField ) {

        $_sCurrentPageSlug  = isset( $_GET['page'] ) ? $_GET['page'] : null;    
        $_sSectionID        = isset( $aField['section_id'] ) ? $aField['section_id'] : '_default';
        $_sFieldID          = $aField['field_id'];
        
        // If the specified field does not exist, do nothing.
        if ( $aField['page_slug'] != $_sCurrentPageSlug ) { return ''; }

        // Retrieve the field error array.
        $this->aFieldErrors = isset( $this->aFieldErrors ) ? $this->aFieldErrors : $this->_getFieldErrors( $_sCurrentPageSlug ); 

        // Render the form field.         
        $sFieldType = isset( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] ) && is_callable( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] )
            ? $aField['type']
            : 'default'; // the predefined reserved field type is applied if the parsing field type is not defined(not found).

        $_aTemp     = $this->getSavedOptions();    // assigning a variable for the strict standard
        $_oField    = new AdminPageFramework_FormField( 
            $aField, 
            $_aTemp,    // passed by reference. @todo: check if it is necessary to pass it as a reference.
            $this->aFieldErrors, 
            $this->oProp->aFieldTypeDefinitions, 
            $this->oMsg 
        );
        $_sFieldOutput = $_oField->_getFieldOutput(); // field output
        unset( $_oField ); // release the object for PHP 5.2.x or below.

        return $this->oUtil->addAndApplyFilters(
            $this,
            array( 
                isset( $aField['section_id'] ) && $aField['section_id'] != '_default' 
                    ? 'field_' . $this->oProp->sClassName . '_' . $aField['section_id'] . '_' . $_sFieldID
                    : 'field_' . $this->oProp->sClassName . '_' . $_sFieldID,
            ),
            $_sFieldOutput,
            $aField // the field array
        );     
        
    }   
}