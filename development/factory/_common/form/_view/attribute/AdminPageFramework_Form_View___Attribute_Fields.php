<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
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
class AdminPageFramework_Form_View___Attribute_Fields extends AdminPageFramework_Form_View___Attribute_FieldContainer_Base {

    /**
     * Indicates the context of the attribute.
     * 
     * e.g. fieldrow, fieldset, fields etc.
     * 
     * @since       3.6.0
     */
    public $sContext     = 'fields'; 

    public $iFieldsCount = 0;
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $aArguments, $aAttributes, $iFieldsCount */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aArguments, 
            $this->aAttributes,
            $this->iFieldsCount,
        );
        $this->aArguments    = $_aParameters[ 0 ];        
        $this->aAttributes  = $_aParameters[ 1 ];
        $this->iFieldsCount = $_aParameters[ 2 ];
        
    }
    
    /**
     * Returns an attribute array.
     * @since       3.6.0
     * @return      array
     */
    protected function _getAttributes() {
        return array(
            'id'            => $this->sContext . '-' . $this->aArguments[ 'tag_id' ],
            'class'         => 'admin-page-framework-' . $this->sContext
                . $this->getAOrB( $this->aArguments[ 'repeatable' ], ' repeatable dynamic-fields', '' ) // 3.6.0+ Added the 'dynamic-fields' class selector.
                . $this->getAOrB( $this->aArguments[ 'sortable' ], ' sortable dynamic-fields', '' ),

            // referred by the sortable field JavaScript script.
            'data-type'     => $this->aArguments[ 'type' ], 

            // 3.6.0+ Stores the total number of dynamic fields, used to generate the input id and name of repeated fields which contain an incremented index number.
            'data-largest_index'            => max(     
                ( int ) $this->iFieldsCount - 1,  // zero-base index
                0 
            ), // convert negative numbers to zero.
            
            // 3.6.0+ Stores the field name model
            'data-field_name_model'         => $this->aArguments[ '_field_name_model' ],
            'data-field_name_flat'          => $this->aArguments[ '_field_name_flat' ],
            'data-field_name_flat_model'    => $this->aArguments[ '_field_name_flat_model' ],
            'data-field_tag_id_model'       => $this->aArguments[ '_tag_id_model' ],
            
            // 3.6.0+ Referred by repeatable scripts.
            'data-field_address'            => $this->aArguments[ '_field_address' ],
            'data-field_address_model'      => $this->aArguments[ '_field_address_model' ],
            
        );                    
    }
           
}