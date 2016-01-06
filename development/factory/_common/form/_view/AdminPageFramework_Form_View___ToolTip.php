<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to generate tool tip outputs.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.7.0
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
     * @since       3.7.0      Changed the parameter structure.
     */
    public function __construct( /* $aArguments, $sTitleElementID */ ) {

        $_aParameters = func_get_args() + array( 
            $this->aArguments,                 
            $this->sTitleElementID,
        );

        if ( $this->_isContent( $_aParameters[ 0 ] ) ) {
            $this->aArguments[ 'content' ] = $_aParameters[ 0 ];
        } else {
            $this->aArguments = $this->getAsArray( $_aParameters[ 0 ] ) + $this->aArguments;
        }
        $this->sTitleElementID = $_aParameters[ 1 ];
    }
        /**
         * @return      boolean
         * @sine        3.7.0
         */
        private function _isContent( $asContent ) {
            
            if ( is_string( $asContent ) ) {
                return true;
            }
            if ( is_array( $asContent ) && ! $this->isAssociative( $asContent ) ) {
                return true;
            }
            return false;
            
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
        $_sHref = esc_attr( "#{$this->sTitleElementID}" );
        return ''
            . "<a href='{$_sHref}' class='admin-page-framework-form-tooltip'>"
            . $this->_getTipLinkIcon()
            . "<span class='admin-page-framework-form-tooltip-content'>"
                . $this->_getTipTitle()
                . $this->_getDescriptions()
            . "</a>"
            
            ;
    }
        /**
         * @since       3.7.0
         * @return      string
         */    
        private function _getTipLinkIcon() {
            
            if ( isset( $this->aArguments[ 'icon' ] ) ) {
                return $this->aArguments[ 'icon' ];
            }
            
            if ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) {
                return "<span class='dashicons dashicons-editor-help'></span>";
            } 
            
            return '[?]';
            
        }
        /**
         * @since       3.7.0
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
         * @since       3.7.0
         * @return      string
         */
        private function _getDescriptions() {         

            if ( isset( $this->aArguments[ 'content' ] ) ) {
                return  "<span class='admin-page-framework-form-tool-tip-description'>"
                        . implode( 
                            "</span><span class='admin-page-framework-form-tool-tip-description'>", 
                            $this->getAsArray( $this->aArguments[ 'content' ] )
                        )
                    . "</span>"
                    ;
            }
            return '';
        }

}