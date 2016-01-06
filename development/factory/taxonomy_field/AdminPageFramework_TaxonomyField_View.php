<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
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
     * The content filter method,
     * 
     * The user may just override this method instead of defining a `content_{...}` callback method.
     * 
     * @since       33.7.0
     * @remark      Declare this method in each factory class as the form of parameters varies and if parameters are different, it triggers PHP strict standard warnings.
     * @param       string      $sContent       The filtering content string.
     */
    public function content( $sContent ) {
        return $sContent;
    }           
    
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
     * @callback    action      {taxonomy slug}_add_form_fields
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
         * @return      string
         */
        private function _getFieldsOutput( $iTermID, $bRenderTableRow ) {
        
            $_aOutput = array();
            
            // Set nonce.           
            $_aOutput[] = wp_nonce_field( 
                $this->oProp->sClassHash, 
                $this->oProp->sClassHash, 
                true, 
                false 
            );
            
            // Set the form data
            $this->_setOptionArray( $iTermID, $this->oProp->sOptionKey );
            
            // Get the field outputs
            $_aOutput[] = $this->oForm->get();
            
            // Filter the output
            $_sOutput = $this->oUtil->addAndApplyFilters( 
                $this, 
                'content_' . $this->oProp->sClassName, 
                $this->content( implode( PHP_EOL, $_aOutput ) )
            );

            // Do action 
            $this->oUtil->addAndDoActions( $this, 'do_' . $this->oProp->sClassName, $this );

            return $_sOutput;
                       
        }    

    
    /**
     * Displayes column cell output.
     * 
     * @internal
     * @since       3.0.0
     * @sine        3.5.0       Moved from `AdminPageFramework_TaxonomyField`. Changed the name from '_replyToSetColumnCell'.
     * @callback    filter      manage_{taxonomy slug}_custom_column
     * @return      string
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