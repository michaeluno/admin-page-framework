<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods regarding admin pages which use WordPress functions and classes.
 *
 * @since 2.0.0
 * @extends AdminPageFramework_Utility
 * @package AdminPageFramework
 * @subpackage Utility
 * @internal
 */
class AdminPageFramework_WPUtility_HTML extends AdminPageFramework_WPUtility_URL {
    
    /**
     * Enhances the parent method generateAttributes() by escaping the attribute values.
     * 
     * For example, 
     * 
     *      array( 'id' => 'my_id', 'name' => 'my_name', 'class' => 'my_class' ) 
     * 
     * will become
     * 
     *      id='my_id' name='my_name' class='my_class'
     * 
     * @since   3.0.0
     * @remark  The single quotes will be used.
     * @remark  For an element with an empty string, only the attribute name will be placed. To prevent the attribute name gets inserted, set null to it.
     */
    static public function generateAttributes( array $aAttributes ) {  
        
        // Sanitize the attribute array.
        foreach( $aAttributes as $_sAttribute => &$_vProperty ) {
            if ( is_array( $_vProperty ) || is_object( $_vProperty ) ) {
                unset( $aAttributes[ $_sAttribute ] );
            }
            if ( is_null( $_vProperty ) ) {
                unset( $aAttributes[ $_sAttribute ] );
            }
            if ( is_string( $_vProperty ) ) {
                $_vProperty = esc_attr( $_vProperty );  
            }
        }     
        
        // Generate the attributes string.
        return parent::generateAttributes( $aAttributes );
        
    }    
    
    /**
     * Generates a string of data attributes from the given associative array.
     * 
     * @since 3.0.0
     */
    static public function generateDataAttributes( array $aArray ) {
        
        return self::generateAttributes( self::getDataAttributeArray( $aArray ) );
        
    }

}