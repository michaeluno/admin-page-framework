<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to generate tool tip outputs.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER
 * @internal
 */
class AdminPageFramework_Form_View___ToolTip extends AdminPageFramework_Form_View___Section_Base {            
  
    public $aArguments      = array(
        'attributes'    => array(), // attributes
        'icon'          => null,  // the icon output
        'title'         => null,  
        'content'       => null,
    );
    
    public $sTitleElementID;
    
    /**
     * Sets up properties.
     * @since       3.6.0
     * @since       DEVVER      Changed the parameter structure.
     */
    public function __construct( /* $aArguments, $sTitleElementID */ ) {

        $_aParameters = func_get_args() + array( 
            $this->aArguments,                 
            $this->sTitleElementID,
        );

        if ( is_string( $_aParameters[ 0 ] ) ) {
            $this->aArguments[ 'content' ] = $_aParameters[ 0 ];
        } else {
            $this->aArguments = $this->getAsArray( $_aParameters[ 0 ] ) + $this->aArguments;
        }
        $this->sTitleElementID = $_aParameters[ 1 ] + $this->sTitleElementID;
    }

    /**
     * Returns HTML formatted description blocks by the given description definition.
     * 
     * @return      string      The output.
     */
    public function get() {
        if ( ! $this->aArguments[ 'content' ] ) {
            return '';
        }
        return ''
            . "<a href='#{$this->sTitleElementID}' class='admin-page-framework-form-tooltip'>"
            . $this->_getTipLinkIcon()
            . "<span class='admin-page-framework-form-tooltip-content'>"
                . $this->_getTipTitle()
                . $this->_getDescriptions()
            . "</a>"
            
            ;
    }
        /**
         * @since       DEVVER
         * @return      string
         */    
        private function _getTipLinkIcon() {
            
            if ( isset( $this->aArguments[ 'icon' ] ) ) {
                return $this->aArguments[ 'icon' ];
            }
            
            if ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) {
                return "<span class='dashicons dashicons-editor-help'></span>";
            } 
            
            return '[ ? ]';
            
        }
        /**
         * @since       DEVVER
         * @return      string
         */
        private function _getTipTitle() {
            if ( isset( $this->aArguments[ 'title' ] ) ) {
                return "<span class='admin-page-framework-form-tool-tip-title'>"
                    . $this->aArguments[ 'title' ]
                    . "</span>";
            }
            return '';
        }
        /**
         * @since       DEVVER
         * @return      string
         */
        private function _getDescriptions() {         

            if ( isset( $this->aArguments[ 'content' ] ) ) {
                return  "<span class='admin-page-framework-form-tool-tip-description'>"
                    
                    . $this->aArguments[ 'content' ]
                    . "</span>"
                    ;
            }
            return '';
        }

}