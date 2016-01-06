<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format fieldrow container HTML attributes.
 * 
 * @package     AdminPageFramework
 * @subpackage  Attribute
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Form_View___Attribute_Fieldrow extends AdminPageFramework_Form_View___Attribute_FieldContainer_Base {

    /**
     * Indicates the context of the attribute.
     * 
     * e.g. fieldset, fieldrow etc.
     * 
     * @since       3.6.0
     */
    public $sContext    = 'fieldrow';

    /**
     * @return      array       The formatted attributes array.
     */
    protected function _getFormattedAttributes() {
        
        $_aAttributes = parent::_getFormattedAttributes();

        // Set the visibility CSS property for the outermost container element.
        if ( $this->aArguments[ 'hidden' ] ) { 
            $_aAttributes[ 'style' ] = $this->getStyleAttribute( 
                $this->getElement( $_aAttributes, 'style', array() ),
                'display:none' 
            );
        }        
        
        return $_aAttributes;
        
    }    
  
}