<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods regarding admin pages which use WordPress functions and classes.
 *
 * @since       2.0.0
 * @extends     AdminPageFramework_WPUtility_URL
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal
 */
class AdminPageFramework_WPUtility_HTML extends AdminPageFramework_WPUtility_URL {
    
    /**
     * Enhances the parent method generateAttributes() by escaping the attribute values.
     * 
     * For example, 
     * <code>
     *      array( 'id' => 'my_id', 'name' => 'my_name', 'class' => 'my_class' ) 
     * </code>
     * will become
     * <code>
     *      id='my_id' name='my_name' class='my_class'
     * </code>
     * @since       3.0.0
     * @remark      The single quotes will be used.
     * @remark      For an element with an empty string, only the attribute name will be placed. To prevent the attribute name gets inserted, set `null` to it.
     * @return      string      the generated attributes string output.
     */
    static public function generateAttributes( array $aAttributes ) {
        
        $_sQuoteCharactor   = "'";
        $_aOutput           = array();
        foreach( $aAttributes as $_sAttribute => $_vProperty ) {
            
            if ( in_array( gettype( $_vProperty ), array( 'array', 'object', 'NULL' ) ) ) {
                continue;
            }                
                
            $_aOutput[] = "{$_sAttribute}={$_sQuoteCharactor}"
                    . esc_attr( $_vProperty )
                . "{$_sQuoteCharactor}";
            
        }     
        return implode( ' ', $_aOutput );
                
    }    
    
    /**
     * Generates a string of data attributes from the given associative array.
     * 
     * @since       3.0.0
     * @return      string
     */
    static public function generateDataAttributes( array $aArray ) {
        return self::generateAttributes( self::getDataAttributeArray( $aArray ) );
    }

    /**
     * Generates an HTML tag.
     * @since       3.5.3
     * @return      string
     */
    static public function generateHTMLTag( $sTagName, array $aAttributes, $sValue=null ) {
        $_sTag = tag_escape( $sTagName );
        return null === $sValue
            ? "<" . $_sTag . " " . self::generateAttributes( $aAttributes ) . " />"
            : "<" . $_sTag . " " . self::generateAttributes( $aAttributes ) . ">"
                    . $sValue
                . "</{$_sTag}>";
    }
    
}