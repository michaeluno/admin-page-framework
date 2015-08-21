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

    public $iFieldsCount = 0;
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $aFieldset, $aAttributes, $iFieldsCount */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aFieldset, 
            $this->aAttributes,
            $this->iFieldsCount,
        );
        $this->aFieldset    = $_aParameters[ 0 ];        
        $this->aAttributes  = $_aParameters[ 1 ];
        $this->iFieldsCount = $_aParameters[ 2 ];
        
    }
    
    /**
     * @since       3.6.0
     * @return      array
     */
    protected function _getAttributes() {
        return array(
            'id'            => $this->sContext . '-' . $this->aFieldset[ 'tag_id' ],
            'class'         => 'admin-page-framework-' . $this->sContext
                . $this->getAOrB( $this->aFieldset[ 'repeatable' ], ' repeatable dynamic-fields', '' ) // 3.6.0+ Added the 'dynamic-fields' class selector.
                . $this->getAOrB( $this->aFieldset[ 'sortable' ], ' sortable dynamic-fields', '' ),

            // referred by the sortable field JavaScript script.
            'data-type'     => $this->aFieldset[ 'type' ], 

            // 3.6.0+ Stores the total number of dynamic fields, used to generate the input id and name of repeated fields which contain an incremented index number.
            'data-field_count'    => $this->iFieldsCount,
            
            // 3.6.0+ Stores the field name model
            'data-field_name_model' => $this->aFieldset[ '_field_name_model' ],
            'data-field_name_flat'  => $this->aFieldset[ '_field_name_flat' ],
            'data-tag_id_model'     => $this->aFieldset[ '_tag_id_model' ],
        );                    
    }
           
}