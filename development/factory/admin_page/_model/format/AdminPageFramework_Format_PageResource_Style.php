<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to format page resource definition array for styles.
 *
 * @package     AdminPageFramework/Factory/AdminPage/Format
 * @since       3.6.3
 * @internal
 */
class AdminPageFramework_Format_PageResource_Style extends AdminPageFramework_Format_Base {

    /**
     * Represents the structure of the subject array.
     *
     * @since       3.6.3
     * @static
     */
    static public $aStructure = array(
        'src'           => null,    // (required, string) the source url or path
        'handle_id'     => null,    // (optional, string) The handle ID of the stylesheet
        'dependencies'  => null,    // (optional, array) The dependency array.
        'version'       => null,    // (optional, string) The stylesheet version number.
        'media'         => null,    // (optional, string) the description of the field which is inserted into the after the input field tag.
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

            if ( is_array( $asSubject ) ) {
                return $asSubject + self::$aStructure;
            }

            $_aSubject = array();
            if ( is_string( $asSubject ) ) {
                $_aSubject[ 'src' ] = $asSubject;
            }
            return $_aSubject + self::$aStructure;
        }

}
