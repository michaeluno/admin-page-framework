<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format field container HTML attributes.
 * 
 * @package     AdminPageFramework
 * @subpackage  Attribute
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Form_View___Attribute_Field extends AdminPageFramework_Form_View___Attribute_FieldContainer_Base {

    /**
     * Indicates the context of the attribute.
     * 
     * e.g. fieldrow, fieldset, fields etc.
     * 
     * @since       3.6.0
     */
    public $sContext    = 'field';

    /**
     * Returns the field container attribute array.
     * 
     * @remark      Formatting each sub-field should be performed prior to callign this method.
     * @param       array       $aField     The (sub-)field definition array. This should have been formatted already.
     * @return      array       The generated field container attribute array.
     * @internal   
     * @since       3.5.3
     * @since       3.6.0       Moved from `AdminPageFramework_Form_View___Fieldset`.
     * @return      array
     */
    protected function _getAttributes() {

        return array(
            'id'            => $this->aArguments[ '_field_container_id' ],
            'data-type'     => $this->aArguments[ 'type' ],   // referred by the repeatable field JavaScript script.
            // @deprecated 3.6.0 
            // 'data-id_model' => $this->aArguments[ '_fields_container_id_model' ], // 3.3.1+
            'class'         => "admin-page-framework-field admin-page-framework-field-" . $this->aArguments[ 'type' ]
                . $this->getAOrB(
                    $this->aArguments[ 'attributes' ][ 'disabled' ],
                    ' disabled',
                    ''
                )
                . $this->getAOrB(
                    $this->aArguments[ '_is_sub_field' ],
                    ' admin-page-framework-subfield',
                    ''
                ),
        );
        
    }
           
}
