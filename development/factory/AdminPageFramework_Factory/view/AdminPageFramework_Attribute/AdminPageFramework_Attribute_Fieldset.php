<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format fieldset container HTML attributes.
 * 
 * @package     AdminPageFramework
 * @subpackage  Attribute
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Attribute_Fieldset extends AdminPageFramework_Attribute_FieldContainer_Base {

    /**
     * Indicates the context of the attribute.
     * 
     * e.g. fieldset, fieldrow etc.
     * 
     * @since       3.6.0
     */
    public $sContext    = 'fieldset';
  

    /**
     * @return      array
     */
    protected function _getAttributes() {
        return array(
            'id'            => $this->sContext . '-' . $this->aFieldset['tag_id'],
            'class'         => 'admin-page-framework-' . $this->sContext,
            'data-field_id' => $this->aFieldset[ 'tag_id' ], // <-- not sure what this was for...
        );                    
    }
           
}