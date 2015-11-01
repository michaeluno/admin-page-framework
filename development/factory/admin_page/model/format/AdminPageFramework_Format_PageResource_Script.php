<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format page resource definition array for scripts.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.3
 * @internal
 */
class AdminPageFramework_Format_PageResource_Script extends AdminPageFramework_Format_Base {
    
    /**
     * Represents the structure of the subject array.
     * 
     * @since       3.6.3
     * @static
     */     
    static public $aStructure = array(    
        'src'           => null,    // (required, string) the source url or path        
        'handle_id'     => null,
        'dependencies'  => array(),
        'version'       => false,       // although the type should be string, the wp_enqueue_...() functions want false as the default value.
        'translation'   => array(),     // only for scripts
        'in_footer'     => false,       // only for scripts
        // 'attributes'    => null,    // (optional, array) [3.3.0+] attributes array. `array( 'data-id' => '...' )`
    );        
    
    public $asSubject = '';
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $asSubject */ ) {
        $_aParameters = func_get_args() + array( 
            $this->asSubject, 
        );
        $this->asSubject             = $_aParameters[ 0 ];
    }
    
    /**
     * Returns an formatted definition array.
     * 
     * @since       3.6.3
     * @return      array       The formatted definition array.
     */
    public function get() {
        return $this->_getFormatted( $this->asSubject );
    }
        /**
         * @since       3.6.3
         * @return      array
         */
        private function _getFormatted( $asSubject ) {
            $_aSubject = array();
            if ( is_string( $asSubject ) ) {
                $_aSubject[ 'src' ] = $asSubject;
            }
            return $_aSubject + self::$aStructure;
        }
           
}