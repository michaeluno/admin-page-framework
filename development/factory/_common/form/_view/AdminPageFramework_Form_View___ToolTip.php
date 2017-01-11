<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to generate tool tip outputs.
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form/View
 * @since       3.7.0
 * @extends     AdminPageFramework_Form_View___Section_Base
 * @internal
 */
class AdminPageFramework_Form_View___ToolTip extends AdminPageFramework_Form_View___Section_Base {            
  
    /**
     * Stores the default argument values.
     */
    public $aArguments      = array(
        'attributes'    => array(
            'container'   => array(),   
            'title'       => array(),
            'content'     => array(),
            'description' => array(),
            'icon'        => array(),
        ), 
        'icon'          => null,    // the icon output to override
        'dash-icon'     => 'dashicons-editor-help',   // the dash-icon class selector for the icon
        'icon_alt_text' => '[?]',   // [3.8.5+] alternative text when icon is not available. For WP v3.7.x or below.
        'title'         => null,  
        'content'       => null,    // will be assigned in the constructor
    );
    
    public $sTitleElementID;
    
    /**
     * Sets up properties.
     * @since       3.6.0
     * @since       3.7.0           Changed the parameter structure.
     * @param       array|string    $asArguments        The content output or the tooltip argument array.
     * @param       string          $sTitleElementID    Not used at the moment.
     */
    public function __construct( /* $aArguments, $sTitleElementID */ ) {

        $_aParameters = func_get_args() + array( 
            $this->aArguments,                 
            $this->sTitleElementID,
        );

        $this->aArguments = $this->_getArgumentsFormatted( $_aParameters[ 0 ], $this->aArguments );
        
        // @remark      Not used at the moment. May be deprecated.
        $this->sTitleElementID = $_aParameters[ 1 ];
        
    }
        /**
         * Formats the argumnent array.
         * @return      array
         */
        private function _getArgumentsFormatted( $asArguments, $aDefaults ) {
            
            $_aArguments = array();
            
            // If simply the content value is passed, set it to the `content` element.
            if ( $this->_isContent( $asArguments ) ) {
                $_aArguments[ 'content' ] = $asArguments;
                return $_aArguments + $aDefaults;
            } 
            
            // Othersize, an argument array is passed.
            $_aArguments                 = $this->getAsArray( $asArguments );
            $_aArguments[ 'attributes' ] = $this->uniteArrays( 
                $this->getElementAsArray( $_aArguments, 'attributes' ),
                $aDefaults[ 'attributes' ]
            );
            return $_aArguments + $aDefaults;
            
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
        return "<a " . $this->_getElementAttributes( 'container', 'admin-page-framework-form-tooltip' ) . ">"
                . $this->_getTipLinkIcon()
                . "<span " . $this->_getElementAttributes( 'content', 'admin-page-framework-form-tooltip-content' ) . ">"
                    . $this->_getTipTitle()
                    . $this->_getDescriptions()
                . "</span>"
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
            if ( version_compare( $GLOBALS[ 'wp_version' ], '3.8', '>=' ) ) {
                return "<span " . $this->_getElementAttributes( 
                        'icon', 
                        array(
                            'dashicons', 
                            $this->aArguments[ 'dash-icon' ]
                        )
                    )                    
                    . "></span>";
            } 
            return $this->aArguments[ 'icon_alt_text' ];
            
        }
        /**
         * @since       3.7.0
         * @return      string
         */
        private function _getTipTitle() {
            if ( isset( $this->aArguments[ 'title' ] ) ) {
                return "<span " . $this->_getElementAttributes( 'title', 'admin-page-framework-form-tooltip-title' ) . ">"
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
                return "<span " . $this->_getElementAttributes( 'description', 'admin-page-framework-form-tooltip-description' ) . ">"
                        . implode( 
                            "</span><span " . $this->_getElementAttributes( 'description', 'admin-page-framework-form-tooltip-description' ) . ">", 
                            $this->getAsArray( $this->aArguments[ 'content' ] )
                        )
                    . "</span>"
                    ;
            }
            return '';
        }

    /**
     * Generates HTML attributes of the specified element.
     * @return      string
     * @since       3.8.5
     */
    private function _getElementAttributes( $sElementKey, $asClassSelectors ) {
            
        $_aContainerAttributes = $this->getElementAsArray(
            $this->aArguments,
            array( 'attributes', $sElementKey )
        ) + array( 'class' => '' ) ;
        $_aContainerAttributes[ 'class' ] = $this->getClassAttribute( 
            $_aContainerAttributes[ 'class' ], 
            $asClassSelectors 
        );
        return $this->getAttributes( $_aContainerAttributes );
            
    }        

}
