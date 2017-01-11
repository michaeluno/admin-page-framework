<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * A field with invisible input values.
 * 
 * This defines the hidden field type.
 * 
 * <h2>Field Definition Arguments</h2>
 * 
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 * 
 * <h2>Example</h2>
 * <code>
 *  array( 
 *      'field_id'      => 'hidden_single',
 *      'title'         => __( 'Hidden Field', 'admin-page-framework-loader' ),
 *      'type'          => 'hidden',
 *      'default'       => __( 'Test value', 'admin-page-framework-loader' ),
 *      'label'         => __( 'Test label', 'admin-page-framework-loader' ),
 *  ),
 * </code>
 * 
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/hidden.png
 * @package         AdminPageFramework
 * @subpackage      Common/Form/FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 */
class AdminPageFramework_FieldType_hidden extends AdminPageFramework_FieldType {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'hidden' );
    
    /**
     * Defines the default key-values of this field type. 
     */
    protected $aDefaultKeys = array();
        
    /**
     * Returns the output of the field type.
     * 
     * @remark      The user needs to assign the value to either the default key or the vValue key in order to set the hidden field. 
     * If it's not set ( null value ), the below `foreach()` will not iterate an element so no input field will be embedded.
     * @since       2.0.0
     * @since       2.1.5       Moved from the AdminPageFramework_FormField class. The name was changed from getHiddenField().
     * @since       3.0.0       Removed unnecessary elements including the parameters.
     * @since       3.3.1       Changed from `_replyToGetField`.
     * @internal
     */
    protected function getField( $aField ) {

        return 
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField[ 'input_id' ]}'>"
                    . $aField[ 'before_input' ]
                    . ( $aField[ 'label' ]
                        ? "<span " . $this->getLabelContainerAttributes( $aField, 'admin-page-framework-input-label-string' ) . ">" 
                            . $aField[ 'label' ]
                          . "</span>"
                        : "" 
                    )
                    . "<input " . $this->getAttributes( $aField[ 'attributes' ] ) . " />" 
                    . $aField['after_input']
                . "</label>"
            . "</div>"
            . $aField['after_label'];
        
    }
    
}
