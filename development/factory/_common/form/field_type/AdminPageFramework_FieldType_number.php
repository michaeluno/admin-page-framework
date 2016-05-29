<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * A number field that lets the user set numbers.
 * 
 * This class defines the number and range field type.
 * 
 * <h2>Field Definition Arguments</h2>
 * 
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 * 
 * <h2>Example</h2>
 * <code>
 *  array( 
 *      'field_id'          => 'number',
 *      'title'             => __( 'Number', 'admin-page-framework-loader' ),
 *      'type'              => 'number',
 *  ),    
 * </code>
 * 
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/number.png
 * @package         AdminPageFramework
 * @subpackage      Common/Form/FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 */
class AdminPageFramework_FieldType_number extends AdminPageFramework_FieldType_text {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'number', 'range' );

    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes' => array(
            'size'          => 30,
            'maxlength'     => 400,
            'class'         => null,    
            'min'           => null,
            'max'           => null,
            'step'          => null,
            'readonly'      => null,
            'required'      => null,
            'placeholder'   => null,
            'list'          => null,
            'autofocus'     => null,
            'autocomplete'  => null,
        ),
    );

    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     * @internal
     * @return      string
     */ 
    protected function getStyles() {
        return "";     
    }
    
}
