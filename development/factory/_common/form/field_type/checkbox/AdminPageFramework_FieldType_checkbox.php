<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * A check box that lets the user enable/disable an option item.
 *
 * This class defines the checkbox field type.
 *
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *     <li>**select_all_button**    - [3.3.0+] (optional, boolean|array) pass `true` to enable the `Select All` button. To set a custom label, set the text such as `__( 'Check All', 'test-domain' )`. Default: `true`.</li>
 *     <li>**select_none_button**   - [3.3.0+] (optional, boolean|array) pass `true` to enable the `Select None` button. To set a custom label, set the text such as `__( 'Check All', 'test-domain' )`. Default: `true`.</li>
 *     <li>**save_unchecked**       - [3.8.8+] (optional, boolean) Whether to store the values of unchecked items. Default: `true`.</li>
 * </ul>
 *
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * <h2>Example</h2>
 * <code>
 *  array(
 *      'field_id'      => 'checkbox',
 *      'title'         => __( 'Checkbox', 'admin-page-framework-loader' ),
 *      'type'          => 'checkbox',
 *      'label'         => __( 'This is a check box.', 'admin-page-framework-loader' )
 *          . ' ' . __( 'A string can be passed to the label argument for a single item.', 'admin-page-framework-loader' ),
 *      'default'   => false,
 *  )
 * </code>
 * <code>
 *  array(
 *      'field_id'      => 'checkbox_multiple_items',
 *      'title'         => __( 'Multiple', 'admin-page-framework-loader' ),
 *      'type'          => 'checkbox',
 *      'label'         => array(
 *          'moon'  => __( 'Moon', 'admin-page-framework-loader' ),
 *          'earth' => __( 'Earth', 'admin-page-framework-loader' ) . ' (' . __( 'this option is disabled.', 'admin-page-framework-loader' ) . ')',
 *          'sun'   => __( 'Sun', 'admin-page-framework-loader' ),
 *          'mars'  => __( 'Mars', 'admin-page-framework-loader' ),
 *      ),
 *      'default'       => array(
 *          'moon'  => true,
 *          'earth' => false,
 *          'sun'   => true,
 *          'mars'  => false,
 *      ),
 *      'attributes'    => array(
 *          'earth' => array(
 *              'disabled' => 'disabled',
 *          ),
 *      ),
 *      'after_label'   => '<br />',
 *  )
 * </code>
 *
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/checkbox.png
 * @package         AdminPageFramework/Common/Form/FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 */
class AdminPageFramework_FieldType_checkbox extends AdminPageFramework_FieldType {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'checkbox' );

    /**
     * Defines the default key-values of this field type.
     */
    protected $aDefaultKeys = array(
        'select_all_button'     => false,        // 3.3.0+   to change the label, set the label here
        'select_none_button'    => false,        // 3.3.0+   to change the label, set the label here
        'save_unchecked'        => true,        // (optional, boolean) 3.8.8+   Whether to store the values of unchecked items.
    );

    /**
     * @return array
     * @since  3.9.0
     */
    protected function getEnqueuingScripts() {
        return array(
            array(
                'handle_id'         => 'admin-page-framework-field-type-checkbox',
                'src'               => dirname( __FILE__ ) . '/js/checkbox.bundle.js',
                'in_footer'         => true,
                'dependencies'      => array( 'jquery', 'admin-page-framework-script-form-main' ),
                'translation_var'   => 'AdminPageFrameworkFieldTypeCheckbox',
                'translation'       => array(
                    'fieldTypeSlugs' => $this->aFieldTypeSlugs,
                    'selectors'      => array(
                        'selectAll'  => $this->___getSelectButtonClassSelectors( $this->aFieldTypeSlugs, 'select_all_button' ),
                        'selectNone' => $this->___getSelectButtonClassSelectors( $this->aFieldTypeSlugs, 'select_none_button' ),
                    ),
                    'messages'       => array(),
                ),
            ),
        );
    }
        /**
         * @since    3.5.12
         * @return   string
         * @internal
         */
        private function ___getSelectButtonClassSelectors( array $aFieldTypeSlugs, $sDataAttribute='select_all_button' ) {
            $_aClassSelectors = array();
            foreach ( $aFieldTypeSlugs as $_sSlug ) {
                if ( ! is_scalar( $_sSlug ) ) {
                    continue;
                }
                $_aClassSelectors[] = '.admin-page-framework-checkbox-container-' . $_sSlug . "[data-{$sDataAttribute}]";
            }
            return implode( ',', $_aClassSelectors );
        }

    /**
     * The class selector to indicate that the input tag is a admin page framework checkbox.
     *
     * This selector is used for the repeatable and sortable field scripts.
     * @since   3.1.7
     * @internal
     * @var     string
     */
    protected $_sCheckboxClassSelector = 'apf_checkbox';

    /**
     * Returns the output of the field type.
     *
     * @since       2.1.5
     * @since       3.0.0     Removed unnecessary parameters.
     * @since       3.3.0     Changed from `_replyToGetField()`.
     * @internal
     */
    protected function getField( $aField ) {

        $_aOutput       = array();
        $_bIsMultiple   = is_array( $aField[ 'label' ] );
        foreach( $this->getAsArray( $aField[ 'label' ], true ) as $_sKey => $_sLabel ) {
            $_aOutput[] = $this->_getEachCheckboxOutput(
                $aField,
                $_bIsMultiple
                    ? $_sKey
                    : '',
                $_sLabel
            );
        }
        return "<div " . $this->getAttributes( $this->_getCheckboxContainerAttributes( $aField ) ) . ">"
                . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                . implode( PHP_EOL, $_aOutput )
            . "</div>";

    }
        /**
         * Returns the checkbox container element attributes array.
         * @internal
         * @access      protected   The taxonomy field type class accesses this method.
         * @since       3.5.3
         * @return      array       The generated attributes array.
         */
        protected function _getCheckboxContainerAttributes( array $aField ) {
            return array(
                'class'                     => 'admin-page-framework-checkbox-container-' . $aField[ 'type' ],
                'data-select_all_button'    => $aField[ 'select_all_button' ]
                    ? ( ! is_string( $aField[ 'select_all_button' ] ) ? $this->oMsg->get( 'select_all' ) : $aField[ 'select_all_button' ] )
                    : null,
                'data-select_none_button'   => $aField[ 'select_none_button' ]
                    ? ( ! is_string( $aField[ 'select_none_button' ] ) ? $this->oMsg->get( 'select_none' ) : $aField[ 'select_none_button' ] )
                    : null,
            );
        }

        /**
         * Returns the output of an individual checkbox by the given key.
         *
         * @since       3.5.3
         * @return      string      The generated checkbox output.
         * @internal
         */
        private function _getEachCheckboxOutput( array $aField, $sKey, $sLabel ) {

            $_aInputAttributes = array(
                'data-key'  => $sKey,   // 3.8.8+ For the `post_type_taxonomy` field type.
            ) + $aField[ 'attributes' ];
            $_oCheckbox = new AdminPageFramework_Input_checkbox(
                $_aInputAttributes,
                array(
                    'save_unchecked'    => $this->getElement( $aField, 'save_unchecked' ),
                )
            );
            $_oCheckbox->setAttributesByKey( $sKey );
            $_oCheckbox->addClass( $this->_sCheckboxClassSelector );
            return $this->getElementByLabel( $aField[ 'before_label' ], $sKey, $aField[ 'label' ] )
                . "<div " . $this->getLabelContainerAttributes( $aField, 'admin-page-framework-input-label-container admin-page-framework-checkbox-label' ) . ">"
                    . "<label " . $this->getAttributes(
                        array(
                            'for'   => $_oCheckbox->getAttribute( 'id' ),
                            'class' => $_oCheckbox->getAttribute( 'disabled' )
                                ? 'disabled'
                                : null,
                        )
                    )
                    . ">"
                        . $this->getElementByLabel( $aField[ 'before_input' ], $sKey, $aField[ 'label' ] )
                        . $_oCheckbox->get( $sLabel )
                        . $this->getElementByLabel( $aField[ 'after_input' ], $sKey, $aField[ 'label' ] )
                    . "</label>"
                . "</div>"
                . $this->getElementByLabel( $aField[ 'after_label' ], $sKey, $aField[ 'label' ] )
                ;

        }

}