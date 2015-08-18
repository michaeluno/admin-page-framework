<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format fields container HTML attributes.
 * 
 * @package     AdminPageFramework
 * @subpackage  Attribute
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Attribute_Fields extends AdminPageFramework_Attribute_FieldContainer_Base {

    /**
     * Indicates the context of the attribute.
     * 
     * e.g. fieldrow, fieldset, fields etc.
     * 
     * @since       3.6.0
     */
    public $sContext    = 'fields'; 

    /**
     * @since       3.6.0
     * @return      array
     */
    protected function _getAttributes() {
        return array(
            'id'            => $this->sContext . '-' . $this->aFieldset[ 'tag_id' ],
            'class'         => 'admin-page-framework-' . $this->sContext
                . $this->getAOrB( $this->aFieldset[ 'repeatable' ], ' repeatable', '' )
                . $this->getAOrB( $this->aFieldset[ 'sortable' ], ' sortable', '' ),
            'data-type'     => $this->aFieldset[ 'type' ], // referred by the sortable field JavaScript script.
        );                    
    }
           
}