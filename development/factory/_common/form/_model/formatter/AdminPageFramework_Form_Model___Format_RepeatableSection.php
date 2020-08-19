<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to format repeatable section arguments.
 *
 * @package     AdminPageFramework/Common/Form/Model/Format
 * @since       3.8.13
 * @extends     AdminPageFramework_FrameworkUtility
 * @internal
 */
class AdminPageFramework_Form_Model___Format_RepeatableSection extends AdminPageFramework_FrameworkUtility {

    /**
     * Represents the structure of the form section definition array.
     *
     * @since       3.8.13
     * @var         array       Represents the array structure of form section definition.
     * @static
     */
    static protected $_aStructure = array(
        'min'      => 0,
        'max'      => 0,
        'disabled' => false,
    );

    /**
     * @var     array
     * @since   3.8.13
     */
    static protected $_aStructure_Disabled = array(
        'message'    => 'The ability of repeating sections is disabled.',   // will be reassigned
        'caption'    => 'Warning',  // will be reassigned
        'box_width'  => 300,
        'box_height' => 72,
    );

    /**
     * Stores the section definition.
     */
    protected $_aArguments = array();

    protected $_oMsg;

    /**
     * Sets up properties.
     */
    public function __construct( /* array $asArguments, $oMsg */ ) {

        $_aParameters       = func_get_args() + array(
            $this->_aArguments,
            null
        );
        $this->_aArguments  = $this->getAsArray( $_aParameters[ 0 ] );
        $this->_oMsg        = $_aParameters[ 1 ]
            ? $_aParameters[ 1 ]
            : AdminPageFramework_Message::getInstance();

    }

    /**
     * Returns an formatted definition array.
     *
     * @return      array       The formatted definition array.
     */
    public function get() {

        $_aArguments = $this->_aArguments + self::$_aStructure;
        unset( $_aArguments[ 0 ] );   // remove the 0 index element converted from `'repeatable   => 'true',`.
        if ( ! empty( $_aArguments[ 'disabled' ] ) ) {
            $_aArguments[ 'disabled' ] = $_aArguments[ 'disabled' ] + array(
                'message'    => $this->_getDefaultMessage(),
                'caption'    => $this->_oMsg->get( 'warning_caption' ),
            ) + self::$_aStructure_Disabled;
        }
        return $_aArguments;

    }

    /**
     * @since   3.8.13
     * @return  sttring
     */
    protected function _getDefaultMessage() {
        return $this->_oMsg->get( 'repeatable_section_is_disabled' );
    }

}
