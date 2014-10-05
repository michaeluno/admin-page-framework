<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_WPUtility_HTML' ) ) :
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
     *      id="my_id" name="my_name" class="my_class"
     * 
     * @since 3.0.0
     */
    static public function generateAttributes( array $aAttributes ) {
        
        foreach( $aAttributes as $sAttribute => &$asProperty ) {
            if ( is_array( $asProperty ) || is_object( $asProperty ) ) {
                unset( $aAttributes[ $sAttribute ] );
            }
            if ( is_string( $asProperty ) ) {
                $asProperty = esc_attr( $asProperty );  
            }
        }     
        return parent::generateAttributes( $aAttributes );
        
    }    
    
    /**
     * Generates a string of data attributes from the given associative array.
     * @since 3.0.0
     */
    static public function generateDataAttributes( array $aArray ) {
        
        $_aNewArray = array();
        foreach( $aArray as $sKey => $v ) {
            $_aNewArray[ "data-{$sKey}" ] = $v;
        }
        return self::generateAttributes( $_aNewArray );
        
    }
    
}
endif;