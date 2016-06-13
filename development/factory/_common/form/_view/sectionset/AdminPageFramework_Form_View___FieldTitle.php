<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render a field title
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form/View/Field
 * @extends     AdminPageFramework_Form_Utility
 * @since       3.8.0 
 * @internal
 */
class AdminPageFramework_Form_View___FieldTitle extends AdminPageFramework_Form_Utility {

    public $aFieldset = array();

    /**
     * Stores additional class selectors 
     */
    public $aClassSelectors = array();
    
    /**
     * Sets up properties.
     * @since       3.8.0
     */
    public function __construct( /* array $aFieldset, $aClassSelectors */ ) {

        $_aParameters = func_get_args() + array( 
            $this->aFieldset, 
            $this->aClassSelectors, 
        );
        $this->aFieldset                = $_aParameters[ 0 ];
        $this->aClassSelectors          = $_aParameters[ 1 ];
        
    }

    /**
     * Returns a field title.
     * 
     * @since       3.8.0
     * @return      string      The output of a field title.
     */
    public function get() {
        
        $aField = $this->aFieldset;
        
        if ( ! $aField[ 'show_title_column' ] ) {
            return '';
        }                
        if ( ! $aField[ 'title' ] ) {
            return '';
        }        
        
        $_oInputTagIDGenerator = new AdminPageFramework_Form_View___Generate_FieldInputID( 
            $aField,
            0   // the first item
        );
        
        $_aLabelAttributes = array(
            'class' => $this->getClassAttribute( 'admin-page-framework-field-title', $this->aClassSelectors ),
            'for'   => $_oInputTagIDGenerator->get(),
        );
        
        return "<label " . $this->getAttributes( $_aLabelAttributes ) . "'>"
                . "<a id='{$aField[ 'field_id' ]}'></a>"  // to allow the browser to link to the element.
                . "<span title='" 
                        . esc_attr( 
                            strip_tags( 
                                is_array( $aField[ 'description' ] )
                                    ? implode( '&#10;', $aField[ 'description' ] )
                                    : $aField[ 'description' ] 
                            )
                        ) 
                    . "'>"
                        . $aField[ 'title' ]
                        . $this->_getTitleColon( $aField )
                        . $this->_getToolTip( $aField[ 'tip' ], $aField[ 'field_id' ] )
                . "</span>"
            . "</label>";        
   
    }    

        /**
         * @return      string
         * @since       3.7.0
         */
        private function _getToolTip( $asTip, $sElementID ) {
            $_oToolTip           = new AdminPageFramework_Form_View___ToolTip(
                $asTip,
                $sElementID
            );            
            return $_oToolTip->get();
        }
        
        /**
         * @since       3.7.0
         * @return      string
         */
        private function _getTitleColon( $aField ) {
            
            if ( ! isset( $aField[ 'title' ] ) || '' === $aField[ 'title' ] ) {
                return '';
            }                    
            if ( 
                in_array( 
                    $aField[ '_structure_type' ], 
                    array( 'widget', 'post_meta_box', 'page_meta_box' ) 
                ) 
            ){
                return "<span class='title-colon'>:</span>" ;
            }                                                 
            
        }
    
}
