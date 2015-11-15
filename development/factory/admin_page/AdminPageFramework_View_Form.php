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
abstract class AdminPageFramework_View_Form extends AdminPageFramework_Model_Form {
    
    /**
     * Modifies the section name attribute value.
     * 
     * @since       3.6.0
     * @return      string
     */
    public function _replyToGetSectionName( /* $sAttribute, $aSectionset */ ) {

        $_aParams            = func_get_args() + array( null, null, );
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
            
}