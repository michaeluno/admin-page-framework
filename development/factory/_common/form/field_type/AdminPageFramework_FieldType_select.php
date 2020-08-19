<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * A select field lets the user select drop-down list items.
 *
 * This class defines the `select` field type.
 *
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *     <li>**is_multiple** - (optional, boolean) if this is set to true, the `multiple` attribute will be inserted into the field input tag, which enables the multiple selections for the user.</li>
 * </ul>
 *
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * <h2>Example</h2>
 *
 * <code>
 *  array(
 *      'field_id'      => 'select',
 *      'title'         => __( 'Dropdown List', 'admin-page-framework-loader' ),
 *      'type'          => 'select',
 *      'help'          => __( 'This is the <em>select</em> field type.', 'admin-page-framework-loader' ),
 *      'default'       => 2,
 *      'label'         => array(
 *          0 => __( 'Red', 'admin-page-framework-loader' ),
 *          1 => __( 'Blue', 'admin-page-framework-loader' ),
 *          2 => __( 'Yellow', 'admin-page-framework-loader' ),
 *          3 => __( 'Orange', 'admin-page-framework-loader' ),
 *      ),
 *  )
 * </code>
 * <h3>Multiple Selection</h3>
 * <code>
 *  array(
 *      'field_id'      => 'select_multiple_options',
 *      'title'         => __( 'Multiple', 'admin-page-framework-loader' ),
 *      'help'          => __( 'This is the <em>select</em> field type with multiple elements.', 'admin-page-framework' ),
 *      'type'          => 'select',
 *      'is_multiple'   => true,
 *      'default'       => array( 3, 4 ), // note that PHP array indices are zero-base, meaning the index count starts from 0 (not 1). 3 here means the fourth item of the array. array( 3, 4 ) will select the fourth and fifth elements.
 *      'label'         => array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'November', 'October', 'December' ),
 *      'attributes'    =>  array(
 *          'select'    =>  array(
 *              'size'  => 10,
 *          ),
 *      ),
 *  )
 * </code>
 * <h3>Grouping</h3>
 * <code>
 *  array(
 *      'field_id'      => 'select_multiple_groups',
 *      'title'         => __( 'Grouping', 'admin-page-framework-loader' ),
 *      'type'          => 'select',
 *      'default'       => 'b',
 *      'label'         => array(
 *          'alphabets' => array(
 *              'a' => 'a',
 *              'b' => 'b',
 *              'c' => 'c',
 *          ),
 *          'numbers' => array(
 *              0 => '0',
 *              1 => '1',
 *              2 => '2',
 *          ),
 *      ),
 *      'attributes'    => array(
 *          'select' => array(
 *              'style' => "width: 200px;",
 *          ),
 *          'option' => array(
 *              1 => array(
 *                  'disabled' => 'disabled',
 *                  'style' => 'background-color: #ECECEC; color: #888;',
 *              ),
 *          ),
 *          'optgroup' => array(
 *              'style' => 'background-color: #DDD',
 *          )
 *      ),
 *  )
 * </code>
 *
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/select.png
 * @package         AdminPageFramework/Common/Form/FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 * @extends         AdminPageFramework_FieldType
 */
class AdminPageFramework_FieldType_select extends AdminPageFramework_FieldType {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'select', );

    /**
     * Defines the default key-values of this field type.
     */
    protected $aDefaultKeys = array(
        'label'             => array(),
        'is_multiple'       => false,
        'attributes'        => array(
            'select'    => array(
                'size'          => 1,
                'autofocusNew'  => null,
                'multiple'      => null,    // set 'multiple' for multiple selections. If 'is_multiple' is set, it takes the precedence.
                'required'      => null,
            ),
            'optgroup'  => array(),
            'option'    => array(),
        ),
    );


    /**
     * Returns the field type specific CSS rules.
     *
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     * @return      string
     */
    protected function getStyles() {
        return <<<CSSRULES
/* Select Field Type */
.admin-page-framework-field-select .admin-page-framework-input-label-container {
    vertical-align: top; 
}
.admin-page-framework-field-select .admin-page-framework-input-label-container {
    padding-right: 1em;
}
CSSRULES;
    }


    /**
     * Returns the output of the field type.
     *
     * @since       2.1.5
     * @since       3.0.0       Removed unnecessary parameters.
     * @since       3.3.1       Changed from `_replyToGetField()`.
     * @return      string
     */
    protected function getField( $aField ) {

        $_oSelectInput = new AdminPageFramework_Input_select( $aField[ 'attributes' ] );
        if ( $aField[ 'is_multiple' ]  ) {
            $_oSelectInput->setAttribute( array( 'select', 'multiple' ), 'multiple' );
        }
        return
            $aField[ 'before_label' ]
            . "<div " . $this->getLabelContainerAttributes( $aField, 'admin-page-framework-input-label-container admin-page-framework-select-label' ) . ">"
                . "<label for='{$aField[ 'input_id' ]}'>"
                    . $aField[ 'before_input' ]
                    . $_oSelectInput->get( $aField[ 'label' ] )
                    . $aField[ 'after_input' ]
                    . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                . "</label>"
            . "</div>"
            . $aField[ 'after_label' ];

    }

}
