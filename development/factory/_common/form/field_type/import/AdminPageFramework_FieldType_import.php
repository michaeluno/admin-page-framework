<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Defines the import field type.
 *
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 *  <ul>
 *      <li>**option_key** - (optional, string) the option table key to save the importing data.</li>
 *      <li>**format** - (optional, string) the import format. json, or array is supported. Default: array</li>
 *      <li>**is_merge** - (optional, boolean) [2.0.5+] determines whether the imported data should be merged with the existing options.</li>
 *  </ul>
 *
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * <h2>Example</h2>
 * <code>
 *  array(
 *      'field_id'      => 'import_single',
 *      'title'         => __( 'Import Field', 'admin-page-framework-loader' ),
 *      'type'          => 'import',
 *      'label'         => __( 'Import Options', 'admin-page-framework-loader' ),
 *  )
 * </code>
 *
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/import.png
 * @package         AdminPageFramework/Common/Form/FieldType
 * @since           2.1.5
 */
class AdminPageFramework_FieldType_import extends AdminPageFramework_FieldType_submit {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'import', );

    /**
     * Defines the default key-values of this field type.
     *
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'option_key'        => null,
        'format'            => 'json',
        'is_merge'          => false,
        'attributes'        => array(
            'class'     => 'button button-primary',
            'file'      => array(
                'accept'    => 'audio/*|video/*|image/*|MIME_type',
                'class'     => 'import',
                'type'      => 'file',
            ),
            'submit'    => array(
                'class' => 'import button button-primary',
                'type'  => 'submit',
            ),
        ),
    );

    /**
     * @return array
     * @since  3.9.0
     */
    protected function getEnqueuingScripts() {
        return array(
            array(
                'handle_id'     => 'admin-page-framework-field-type-import',
                'src'           => dirname( __FILE__ ) . '/js/import.bundle.js',
                'in_footer'         => true,
                'dependencies'      => array( 'jquery', 'admin-page-framework-script-form-main' ),
                'translation_var'   => 'AdminPageFrameworkImportFieldType',
                'translation'       => array(
                    'fieldTypeSlugs'    => $this->aFieldTypeSlugs,
                    'label'             => array(
                        'noFile'    => $this->oMsg->get( 'import_no_file' ),
                    ),
                ),
            ),
        );
    }

    /**
     * Returns the output of the field type.
     *
     * @since       2.1.5       Moved from the AdminPageFramework_FormField class. The name was changed from getHiddenField().
     * @since       3.3.1       Changed from `_replyToGetField()`.
     * @internal
     * @param       array
     * @return      string
     */
    protected function getField( $aField ) {
        $aField[ 'attributes'][ 'name' ]  = "__import[submit][{$aField[ 'input_id' ]}]";
        $aField[ 'label' ]                = $aField[ 'label' ]
            ? $aField[ 'label' ]
            : $this->oMsg->get( 'import' );
        return parent::getField( $aField );
    }

    /**
     * Returns extra output for the field.
     *
     * This is for the import field type that extends this class. The import field type cannot place the file input tag inside the label tag that causes a problem in FireFox.
     *
     * @since       3.0.0
     * @internal
     * @param       array   $aField
     * @return      string
     */
    protected function _getExtraFieldsBeforeLabel( &$aField ) {
        return "<label>"
                . "<input " . $this->getAttributes(
                    array(
                        'id' => "{$aField[ 'input_id' ]}_file",
                        'type' => 'file',
                        'name' => "__import[{$aField[ 'input_id' ]}]",
                    ) + $aField[ 'attributes' ][ 'file' ]
                ) . " />"
            . "</label>";
    }

    /**
     * Returns the output of hidden fields for this field type that enables custom submit buttons.
     * @since       3.0.0
     * @internal
     * @param       array   $aField
     * @return      string
     */
    protected function _getExtraInputFields( &$aField ) {

        $aHiddenAttributes = array( 'type' => 'hidden', );
        return
            "<input " . $this->getAttributes(
                array(
                    'name' => "__import[{$aField['input_id']}][input_id]",
                    'value' => $aField['input_id'],
                ) + $aHiddenAttributes
            ) . "/>"
            . "<input " . $this->getAttributes(
                array(
                    'name' => "__import[{$aField['input_id']}][field_id]",
                    'value' => $aField['field_id'],
                ) + $aHiddenAttributes
            ) . "/>"
            . "<input " . $this->getAttributes(
                array(
                    'name' => "__import[{$aField['input_id']}][section_id]",
                    'value' => isset( $aField['section_id'] ) && $aField['section_id'] != '_default' ? $aField['section_id'] : '',
                ) + $aHiddenAttributes
            ) . "/>"
            . "<input " . $this->getAttributes(
                array(
                    'name' => "__import[{$aField['input_id']}][is_merge]",
                    'value' => $aField['is_merge'],
                ) + $aHiddenAttributes
            ) . "/>"
            . "<input " . $this->getAttributes(
                array(
                    'name' => "__import[{$aField['input_id']}][option_key]",
                    'value' => $aField['option_key'],
                ) + $aHiddenAttributes
            ) . "/>"
            . "<input " . $this->getAttributes(
                array(
                    'name' => "__import[{$aField['input_id']}][format]",
                    'value' => $aField['format'],
                ) + $aHiddenAttributes
            ) . "/>"
            ;
    }

}