<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Defines the `section_title` field type.
 *
 * When a field is defined with this field type, the section title will be replaced with this field. This is used for repeatable tabbed sections.
 *
 * <h2>Field Definition Arguments</h2>
 *
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * <h2>Example</h2>
 * Set a field definition normally. Only the first item with the type `section_title` can be displayed.
 * <code>
 * $this->addSettingFields(
 *     'my_section', // the target section ID
 *     array(
 *         'field_id'      => 'section_title_field',
 *         'type'          => 'section_title',
 *         'label'         => '<h3>'
 *                 . __( 'Section Name', 'admin-page-framework-loader' )
 *             . '</h3>',
 *         'attributes'    => array(
 *             'size' => 30,
 *         ),
 *     )
 * );
 * </code>
 *
 * @image       http://admin-page-framework.michaeluno.jp/image/common/form/field_type/section_title.png
 * @package     AdminPageFramework/Common/Form/FieldType
 * @since       3.0.0
 * @since       3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 * @since       3.5.3       Changed to extend `AdminPageFramework_FieldType_text` from `AdminPageFramework_FieldType`.
 * @extends     AdminPageFramework_FieldType_text
 */
class AdminPageFramework_FieldType_section_title extends AdminPageFramework_FieldType_text {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'section_title', );

    /**
     * Defines the default key-values of this field type.
     *
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'label_min_width'   => 30,
        'attributes'        => array(
            'size'      => 20,
            'maxlength' => 100,
        ),
    );

    /**
     * Returns the output of the text input field.
     *
     * @since       2.1.5
     * @since       3.0.0     Removed unnecessary parameters.
     * @since       3.3.1     Changed from `_replyToGetField()`.
     * @internal
     * @return      string
     */
    protected function getField( $aField ) {
        $aField[ 'attributes' ] = array( 'type' => 'text' ) + $aField[ 'attributes' ];
        return parent::getField( $aField );
    }

}