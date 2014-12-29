<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to output form elements.
 * 
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.3.1
 * @internal
 */
abstract class AdminPageFramework_FormOutput extends AdminPageFramework_WPUtility {
    
    /**
     * Generates attributes of the field container tag.
     * 
     * @since       3.0.0
     * @since       3.3.1       Changed the name from `_getAttributes()`. Added the <var>$sContext</var> parameter. Moved from `AdminPageFramework_FormTable_Base`.
     * @internal
     */
    protected function _getFieldContainerAttributes( $aField, $aAttributes=array(), $sContext='fieldrow' ) {

        // [3.3.1+] Changed the custom attributes to take its precedence.
        $_aAttributes = $this->uniteArrays( 
            isset( $aField['attributes'][ $sContext ] ) && is_array( $aField['attributes'][ $sContext ] )
                ? $aField['attributes'][ $sContext ] 
                : array(),
            $aAttributes
        );
        
        $_aAttributes['class']   = $this->generateClassAttribute( 
            isset( $_aAttributes['class'] ) 
                ? $_aAttributes['class'] 
                : array(), 
            isset( $aField['class'][ $sContext ] )
                ? $aField['class'][ $sContext ]
                : array()
        );  
        
        // Set the visibility CSS property for the outermost container element.
        if ( 'fieldrow' === $sContext && $aField['hidden'] ) { 
            $_aAttributes['style'] = $this->generateStyleAttribute( 
                isset( $_aAttributes['style'] )
                    ? $_aAttributes['style']
                    : array(),
                'display:none' 
            );
        }
            
        return $this->generateAttributes( $_aAttributes );
        
    }
    
    
}