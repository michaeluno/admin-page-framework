<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides shared methods for the form classes.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.7.0
 * @extends     AdminPageFramework_FrameworkUtility
 */
abstract class AdminPageFramework_Form_Utility extends AdminPageFramework_FrameworkUtility {
    
    /**
     * @since       3.7.0
     * @return      array
     */
    static public function getElementPathAsArray( $asPath ) {
        if ( is_array( $asPath ) ) {
            return;
        }
        return explode( '|', $asPath );
    }
    
    /**
     * @since       3.7.0
     * @return      string      The section path. e.g. my_section|nested_section
     */
    static public function getFormElementPath( $asID ) {
        return implode( 
            '|', 
            self::getAsArray( $asID ) 
        );        
    }
  
    /**
     * Sanitizes a given section or field id.
     * @return      array|string
     * @since       3.7.0
     */
    static public function getIDSanitized( $asID ) {
        return is_scalar( $asID )
            ? self::sanitizeSlug( $asID )
            : self::getAsArray( $asID );
            
    }
    
}