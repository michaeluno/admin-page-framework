<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Handles displaying field outputs.
 *
 * @abstract
 * @since           3.5.0
 * @package         AdminPageFramework
 * @subpackage      TaxonomyField
 * @internal
 */
abstract class AdminPageFramework_TaxonomyField_View extends AdminPageFramework_TaxonomyField_Model {
    
    /**
     * Generates a name attribute value for a form input element.
     * @internal    
     * @since       3.5.7
     * @remark      The taxonomy form fields do not have sections.
     * @return      string      the input name attribute
     */    
    public function _replyToGetInputNameAttribute( /* $sNameAttribute, $aField, $sKey */ ) {
        
        $_aParams   = func_get_args() + array( null, null, null );
        $_aField    = $_aParams[ 1 ];
        $_sKey      = ( string ) $_aParams[ 2 ]; // a 0 value may have been interpreted as false.
        $_sKey      = $this->oUtil->getAOrB(
            '0' !== $_sKey && empty( $_sKey ),
            '',
            "[{$_sKey}]"
        );        
        return $_aField['field_id'] . $_sKey; 
        
    }
    /**
     * Generates a flat input name whose dimensional element keys are delimited by the pipe (|) character.
     * @internal    
     * @since       3.5.7
     * @return      string      the flat input name attribute
     */    
    public function _replyToGetFlatInputName( /* $sFlatNameAttribute, $aField, $sKey, $sSectionIndex */ ) {
        $_aParams   = func_get_args() + array( null, null, null );
        $_aField    = $_aParams[ 1 ];
        $_sKey      = ( string ) $_aParams[ 2 ];
        $_sKey      = $this->oUtil->getAOrB(
            '0' !== $_sKey && empty( $_sKey ),
            '',
            "|{$_sKey}"
        );
        return "{$_aField['field_id']}{$_sKey}";
    }

   /**
     * Adds input fields
     * 
     * @internal
     * @since       3.0.0
     * @since       3.5.0       Moved from `AdminPageFramework_TaxonomyField`. Renamed from '_replyToAddFieldsWOTableRows'.
     */    
    public function _replyToPrintFieldsWOTableRows( $oTerm ) {
        echo $this->_getFieldsOutput( 
            isset( $oTerm->term_id ) 
                ? $oTerm->term_id 
                : null, 
            false 
        );
    }

    /**
     * Adds input fields with table rows.
     * 
     * @remark      Used for the Edit Category(taxonomy) page.
     * @internal
     * @since       3.0.0
     * @since       3.5.0       Moved from `AdminPageFramework_TaxonomyField`. Renamed from '_replyToAddFieldsWithTableRows'.
     */
    public function _replyToPrintFieldsWithTableRows( $oTerm ) {
        echo $this->_getFieldsOutput( 
            isset( $oTerm->term_id )
                ? $oTerm->term_id 
                : null, 
            true 
        );
    }    
        /**
         * Retrieves the fields output.
         * 
         * @since       3.0.0
         * @internal
         */
        private function _getFieldsOutput( $iTermID, $bRenderTableRow ) {
        
            $_aOutput = array();
            
            /* Set nonce. */
            $_aOutput[] = wp_nonce_field( $this->oProp->sClassHash, $this->oProp->sClassHash, true, false );
            
            /* Set the option property array */
            // @todo Move _setOptionArray() to _replyToRegisterFormElements().
            $this->_setOptionArray( $iTermID, $this->oProp->sOptionKey );
            
            /* Format the fields arrays - taxonomy fields do not support sections */
            $this->oForm->format();
            
            /* Get the field outputs */
            $_oFieldsTable = new AdminPageFramework_FormTable( $this->oProp->aFieldTypeDefinitions, $this->_getFieldErrors(), $this->oMsg );
            $_aOutput[] = $bRenderTableRow 
                ? $_oFieldsTable->getFieldsetRows( $this->oForm->aFields['_default'], array( $this, '_replyToGetFieldOutput' ) )
                : $_oFieldsTable->getFieldsets( $this->oForm->aFields['_default'], array( $this, '_replyToGetFieldOutput' ) );
                    
            /* Filter the output */
            // @todo call the content() method.
            $_sOutput = $this->oUtil->addAndApplyFilters( $this, 'content_' . $this->oProp->sClassName, implode( PHP_EOL, $_aOutput ) );
            
            /* Do action */
            $this->oUtil->addAndDoActions( $this, 'do_' . $this->oProp->sClassName, $this );
                
            return $_sOutput;
        
        }    

    
    /**
     * Displayes column cell output.
     * 
     * @internal
     * @since       3.0.0
     * @sine        3.5.0       Moved from `AdminPageFramework_TaxonomyField`. Changed the name from '_replyToSetColumnCell'.
     * @todo        Format the local variable names.
     */
    public function _replyToPrintColumnCell( $vValue, $sColumnSlug, $sTermID ) {
        
        $_sCellHTML = '';
        if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] ) {
            $_sCellHTML = $this->oUtil->addAndApplyFilter( $this, "cell_{$_GET['taxonomy']}", $vValue, $sColumnSlug, $sTermID );
        }
        $_sCellHTML = $this->oUtil->addAndApplyFilter( $this, "cell_{$this->oProp->sClassName}", $_sCellHTML, $sColumnSlug, $sTermID );
        $_sCellHTML = $this->oUtil->addAndApplyFilter( $this, "cell_{$this->oProp->sClassName}_{$sColumnSlug}", $_sCellHTML, $sTermID ); // 3.0.2+
        echo $_sCellHTML;
                
    }
        
}