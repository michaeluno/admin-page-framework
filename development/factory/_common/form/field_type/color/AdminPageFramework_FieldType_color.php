<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * A text field with a color picker.
 *
 * This class defines the color field type.
 *
 * <h2>Field Definition Arguments</h2>
 *
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * <h2>Example</h2>
 * <code>
 *  array(
 *      'field_id'      => 'color_picker_field',
 *      'title'         => __( 'Color Picker', 'admin-page-framework-loader' ),
 *      'type'          => 'color',
 *  ),
 * </code>
 *
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/color.png
 * @package         AdminPageFramework/Common/Form/FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 */
class AdminPageFramework_FieldType_color extends AdminPageFramework_FieldType {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'color' );

    /**
     * Defines the default key-values of this field type.
     */
    protected $aDefaultKeys = array(
        'attributes' => array(
            'size'      => 10,
            'maxlength' => 400,
            'value'     => 'transparent',
        ),
    );

    /**
     * Loads the field type necessary components.
     *
     * Loads necessary files of the color field type.
     * @since       2.0.0
     * @since       2.1.5       Moved from AdminPageFramework_MetaBox. Changed the name from enqueueColorFieldScript().
     * @since       3.3.1       Changed from `_replyToFieldLoader()`.
     * @see         http://www.sitepoint.com/upgrading-to-the-new-wordpress-color-picker/
     * @internal
     */
    protected function setUp() {

        // If the WordPress version is greater than or equal to 3.5, then load the new WordPress color picker.
        if ( version_compare( $GLOBALS[ 'wp_version' ], '3.5', '>=' ) ) {
            $this->___enqueueWPColorPicker();
        }
        // If the WordPress version is less than 3.5 load the older farbtasic color picker.
        else {
            //As with wp-color-picker the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
            wp_enqueue_style( 'farbtastic' );
            wp_enqueue_script( 'farbtastic' );
        }

    }
        /**
         * @since       3.8.11
         */
        private function ___enqueueWPColorPicker() {

            // Both the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );

            // For front-end
            // @see     http://wordpress.stackexchange.com/a/82722
            if ( ! is_admin() ) {

                // wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script(
                    'iris',
                    admin_url( 'js/iris.min.js' ),
                    array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
                    false,
                    1
                );
                wp_enqueue_script(
                    'wp-color-picker',
                    admin_url( 'js/color-picker.min.js' ),
                    array( 'iris' ),
                    false,
                    1
                );
                wp_localize_script(
                    'wp-color-picker',
                    'wpColorPickerL10n',
                    array(
                        'clear'         => __( 'Clear' ),
                        'defaultString' => __( 'Default' ),
                        'pick'          => __( 'Select Color' ),
                        'current'       => __( 'Current Color' ),
                    )
                );

            }

        }

    /**
     * @return array
     * @since  3.9.0
     */
    protected function getEnqueuingScripts() {
        return array(
            array(
                'handle_id'     => 'admin-page-framework-field-type-color',
                'src'           => dirname( __FILE__ ) . '/js/color.bundle.js',
                'in_footer'         => true,
                'dependencies'      => array( 'jquery', 'admin-page-framework-script-form-main' ),
                'translation_var'   => 'AdminPageFrameworkColorFieldType',
                'translation'       => array(
                    'fieldTypeSlugs' => $this->aFieldTypeSlugs,
                ),
            ),
        );
    }

    /**
     * Returns the field type specific CSS rules.
     *
     * @since  2.1.5
     * @since  3.3.1 Changed from `_replyToGetStyles()`.
     * @since  3.9.0 Moved to an external file.
     * @return string
     */
    protected function getStyles() {
        return '';
    }

    /**
     * Returns the color picker JavaScript script loaded in the head tag of the created admin pages.
     *
     * @since  2.0.0
     * @since  2.1.3  Changed to define a global function literal that registers the given input field as a color picker.
     * @since  2.1.5  Changed the name from `getColorPickerScript()`.
     * @since  3.3.1  Changed the name from `_replyToGetScripts()`.
     * @since  3.9.0  Moved to an external file.
     * @return string The image selector script.
     * @see    https://github.com/Automattic/Iris
     */
    protected function getScripts() {
        return '';
    }

    /**
     * Returns the output of the color field.
     *
     * @since 2.1.5
     * @since 3.0.0 Removed unnecessary parameters.
     * @since 3.3.1 Changed from `_replyToGetField()`.
     */
    protected function getField( $aField ) {

        // If the value is not set, apply the default value, 'transparent'.
        $aField[ 'value' ]      = is_null( $aField[ 'value' ] ) ? 'transparent' : $aField[ 'value' ];
        $aField[ 'attributes' ] = $this->___getInputAttributes( $aField );
        return $aField[ 'before_label' ]
            . "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField[ 'input_id' ]}'>"
                    . $aField[ 'before_input' ]
                    . ( $aField[ 'label' ] && ! $aField[ 'repeatable' ]
                        ? "<span " . $this->getLabelContainerAttributes( $aField, 'admin-page-framework-input-label-string' ) . ">"
                                . $aField['label']
                            . "</span>"
                        : ""
                    )
                    . "<input " . $this->getAttributes( $aField[ 'attributes' ] ) . " />"
                    . $aField[ 'after_input' ]
                    . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                . "</label>"
                // this div element with this class selector becomes a farbtastic color picker. ( below 3.4.x ) // rel='{$aField['input_id']}'
                . "<div class='colorpicker admin-page-framework-field-color-picker' id='color_" . esc_attr( $aField[ 'input_id' ] ) . "' data-input_id='" . esc_attr( $aField[ 'input_id' ] ) . "'></div>"
            . "</div>"
            . $aField['after_label'];

    }
        /**
         *
         * @return array
         * @since  3.5.10
         */
        private function ___getInputAttributes( array $aField ) {

            return array(
                'color'        => $aField['value'],
                'value'        => $aField['value'],
                'data-default' => isset( $aField[ 'default' ] )
                    ? $aField[ 'default' ]
                    : 'transparent', // used by the repeatable script
                'type'         => 'text', // it must be text
                'class'        => trim( 'input_color ' . $aField['attributes']['class'] ),
            ) + $aField[ 'attributes' ];

        }

}