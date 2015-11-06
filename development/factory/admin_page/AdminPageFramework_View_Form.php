<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Deals with displaying outputs of forms.
 *
 * @abstract
 * @since           3.3.1
 * @since           3.6.3       Changed the name from `AdminPageFramework_View_Form`.
 * @extends         AdminPageFramework_Model_Form
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Form_View extends AdminPageFramework_Model_Form {
    
    /**
     * Modifies the section name attribute value.
     * 
     * @since       3.6.0
     * @return      string
     */
    public function _replyToGetSectionName( /* $sAttribute, $aSectionset */ ) {

        $_aParams            = func_get_args() + array( null, null,  );
        $sNameAttribute      = $_aParams[ 0 ];
        $aSectionset         = $_aParams[ 1 ];        
        
        $_aDimensionalKeys   = array( $this->oProp->sOptionKey );   
        $_aDimensionalKeys[] = '[' . $aSectionset[ 'section_id' ] . ']';
        if ( isset( $aSectionset[ '_index' ] ) ) {
            $_aDimensionalKeys[] = '[' . $aSectionset[ '_index' ] . ']';
        }
        
        return implode( '', $_aDimensionalKeys );
        
    }
    
    /**
     * @since       3.6.0
     * @return      string
     */
    public function _replyToGetFieldNameAttribute( /* $sAttribute, $aFieldset */ ) {
        
        $_aParams           = func_get_args() + array( null, null,  );
        $sNameAttribute     = $_aParams[ 0 ];
        $aFieldset          = $_aParams[ 1 ];        
        
        $_aDimensionalKeys  = array( $aFieldset[ 'option_key' ] );
        if ( $this->isSectionSet( $aFieldset ) ) {
            $_aDimensionalKeys[] = '[' . $aFieldset[ 'section_id' ] . ']';
        }
        if ( isset( $aFieldset[ 'section_id' ], $aFieldset[ '_section_index' ] ) ) {
            $_aDimensionalKeys[] = '[' . $aFieldset[ '_section_index' ] . ']';
        }
        $_aDimensionalKeys[] = '[' . $aFieldset[ 'field_id' ] . ']';
        return implode( '', $_aDimensionalKeys );
        
    }
    
    /**
     * @return      string
     * @since       3.6.0
     */
    public function _replyToGetFlatFieldName( /* $sAttribute, $aFieldset */ ) {

        $_aParams           = func_get_args() + array( null, null,  );
        $sNameAttribute     = $_aParams[ 0 ];
        $aFieldset          = $_aParams[ 1 ];        
        
        $_aDimensionalKeys  = array( $aFieldset[ 'option_key' ] );
        if ( $this->isSectionSet( $aFieldset ) ) {
            $_aDimensionalKeys[] = $aFieldset[ 'section_id' ];
        }
        if ( isset( $aFieldset[ 'section_id' ], $aFieldset[ '_section_index' ] ) ) {
            $_aDimensionalKeys[] = $aFieldset[ '_section_index' ];
        }
        $_aDimensionalKeys[] = $aFieldset[ 'field_id' ];
        return implode( '|', $_aDimensionalKeys );        
        
    }
    
    /**
     * Generates a name attribute value for a form input element.
     * @internal    
     * @since       3.5.7
     * @return      string      the input name attribute
     */    
    public function _replyToGetInputNameAttribute( /* $sNameAttribute, $aField, $sKey */ ) {
        
        $_aParams       = func_get_args() + array( null, null, null );
        $sNameAttribute = $_aParams[ 0 ];
        $aField         = $_aParams[ 1 ];
        $sKey           = ( string ) $_aParams[ 2 ];
        $sKey           = $this->oUtil->getAOrB(
            '0' !== $sKey && empty( $sKey ),
            '',
            "[{$sKey}]"
        );   
        
        return $this->_replyToGetFieldNameAttribute( '', $aField ) . $sKey;
        
    }
    /**
     * Generates a flat input name whose dimensional element keys are delimited by the pipe (|) character.
     * @internal    
     * @since       3.5.7
     * @since       3.5.7.1     Fixed a bug that the tailing key element was not delimited properly.
     * @return      string      the flat input name attribute
     */    
    public function _replyToGetFlatInputName( /* $sFlatNameAttribute, $aField, $sKey, */ ) {
        $_aParams           = func_get_args() + array( null, null, null );
        $sFlatNameAttribute = $_aParams[ 0 ];
        $aField             = $_aParams[ 1 ];
        $_sKey              = ( string ) $_aParams[ 2 ];
        $_sKey              = $this->oUtil->getAOrB(
            '0' !== $_sKey && empty( $_sKey ),
            '',
            "|" . $_sKey
        );        
        
        return $this->_replyToGetFlatFieldName( '', $aField ) . $_sKey;

    }
    
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

        $_sCurrentPageSlug  = $this->oProp->getCurrentPageSlug();
        $_sSectionID        = $this->oUtil->getElement( $aField, 'section_id', '_default' );
        $_sFieldID          = $aField['field_id'];
        
        // If the specified field does not exist, do nothing.
        if ( $aField['page_slug'] != $_sCurrentPageSlug ) { 
            return ''; 
        }

        // Retrieve the field error array.
        $this->aFieldErrors = isset( $this->aFieldErrors ) 
            ? $this->aFieldErrors 
            : $this->_getFieldErrors( $_sCurrentPageSlug );

        // Render the form field.         
        $sFieldType = isset( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] ) && is_callable( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] )
            ? $aField['type']
            : 'default'; // the predefined reserved field type is applied if the parsing field type is not defined(not found).

        $_aTemp     = $this->getSavedOptions();    // assigning a variable for the strict standard
        $_oFieldset = new AdminPageFramework_FormFieldset( 
            $aField, 
            $_aTemp,    // passed by reference. @todo: examine why it needs to be passed by reference.
            $this->aFieldErrors, 
            $this->oProp->aFieldTypeDefinitions, 
            $this->oMsg,
            $this->oProp->aFieldCallbacks // field output element callables.
        );
        $_sFieldOutput = $_oFieldset->get(); // field output
        unset( $_oFieldset ); // release the object for PHP 5.2.x or below.

        return $this->oUtil->addAndApplyFilters(
            $this,
            array( 
                isset( $aField['section_id'] ) && '_default' !== $aField['section_id']
                    ? 'field_' . $this->oProp->sClassName . '_' . $aField['section_id'] . '_' . $_sFieldID
                    : 'field_' . $this->oProp->sClassName . '_' . $_sFieldID,
            ),
            $_sFieldOutput,
            $aField // the field array
        );     

    }   

}