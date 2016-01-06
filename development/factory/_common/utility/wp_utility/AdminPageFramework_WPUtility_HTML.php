<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
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
     * Generates HTML attributes to be inserted into an HTML tag by escaping the attribute values.
     * 
     * For example, 
     * <code>
     *      array( 'id' => 'my_id', 'name' => 'my_name', 'class' => 'my_class' ) 
     * </code>
     * will become
     * <code>
     *      id='my_id' name='my_name' class='my_class'
     * </code>
     * @remark      The single quotes will be used.
     * @remark      For an element with an empty string, only the attribute name will be placed. To prevent the attribute name gets inserted, set `null` to it.
     * @return      string      the generated attributes string output.
     * @since       3.6.0
     */
    static public function getAttributes( array $aAttributes ) {
     
        $_sQuoteCharactor   = "'";
        $_aOutput           = array();
        foreach( $aAttributes as $_sAttribute => $_mProperty ) {
            if ( is_scalar( $_mProperty ) ) {
                $_aOutput[] = "{$_sAttribute}={$_sQuoteCharactor}" . esc_attr( $_mProperty ) . "{$_sQuoteCharactor}";
            }            
        }     
        return implode( ' ', $_aOutput );
     
    }    
        /**
         * Enhances the parent method getAttributes() by escaping the attribute values.
         * @since       3.0.0
         * @deprecated  3.6.0       Use the `getAttributes()` method instead.
         */
        static public function generateAttributes( array $aAttributes ) {
            return self::getAttributes( $aAttributes );                
        }    
    /**
     * Generates a string of data attributes from the given associative array.
     * 
     * @since       3.0.0
     * @return      string
     */
    static public function getDataAttributes( array $aArray ) {
        return self::getAttributes( self::getDataAttributeArray( $aArray ) );
    }    
        /**
         * Generates a string of data attributes from the given associative array.
         * 
         * @since       3.0.0
         * @return      string
         * @deprecated  3.6.0       Use `getDataAttributes()` instead.
         */
        static public function generateDataAttributes( array $aArray ) {
            return self::getDataAttributes( $aArray );
        }

    /**
     * Generates an HTML tag.
     * @since       3.6.0
     * @return      string
     */
    static public function getHTMLTag( $sTagName, array $aAttributes, $sValue=null ) {
        $_sTag = tag_escape( $sTagName );
        return null === $sValue
            ? "<" . $_sTag . " " . self::getAttributes( $aAttributes ) . " />"
            : "<" . $_sTag . " " . self::getAttributes( $aAttributes ) . ">"
                    . $sValue
                . "</{$_sTag}>";        
    }    
        /**
         * Generates an HTML tag.
         * @since       3.5.3
         * @return      string
         * @deprecated  3.6.0       Use `getHTMLTag()` instead.
         */
        static public function generateHTMLTag( $sTagName, array $aAttributes, $sValue=null ) {
            return self::getHTMLTag( $sTagName, $aAttributes, $sValue );
        }
    
}