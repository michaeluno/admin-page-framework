<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * A text field that lets the user set short text values.
 *
 * This class defines the `text` field type. Also the field types of 'password', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'url', and 'week' are defined.
 *
 * <h3>Example</h3>
 * <code>
 *  array(
 *      'field_id'          => 'text',
 *      'title'             => __( 'Text', 'admin-page-framework-loader' ),
 *      'type'              => 'text',
 *      'default'           => 123456,
 *      'attributes'        => array(
 *          'size' => 40,
 *          'placeholder' => __( 'Type something here.', 'admin-page-framework-loader' ),
 *      ),
 *  )
 * </code>
 *
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/text_field_type.png
 * @package         AdminPageFramework/Common/Form/FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 */
class AdminPageFramework_FieldType_text extends AdminPageFramework_FieldType {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'text', 'password', 'date', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'url', 'week', );

    /**
     * Defines the default key-values of this field type.
     *
     * @remark `$_aDefaultKeys` holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
    );


    /**
     * Returns the field type specific CSS output inside the `<style></style>` tags.
     *
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     */
    protected function getStyles() {
        return <<<CSSRULES
/* Text Field Type */
.admin-page-framework-field.admin-page-framework-field-text > .admin-page-framework-input-label-container {
    /* vertical-align: top; @depracated 3.7.1 */
    vertical-align: middle; 
}

.admin-page-framework-field.admin-page-framework-field-text > .admin-page-framework-input-label-container.admin-page-framework-field-text-multiple-labels {
    /* When the browser screen width gets narrow, avoid the inputs getting placed next each other. */
    display: block;
}
CSSRULES;

    }

    /**
     * Returns the output of the text input field.
     *
     * @since       2.1.5
     * @since       3.0.0       Removed unnecessary parameters.
     * @since       3.3.1       Changed from `_replyToGetField()`.
     * @internal
     */
    protected function getField( $aField ) {

        $_aOutput = array();
        foreach( ( array ) $aField[ 'label' ] as $_sKey => $_sLabel ) {
            $_aOutput[] = $this->_getFieldOutputByLabel(
                $_sKey,
                $_sLabel,
                $aField
            );
        }

        // the repeatable field buttons will be replaced with this element.
        $_aOutput[] = "<div class='repeatable-field-buttons'></div>";
        return implode( '', $_aOutput );

    }

        /**
         * @internal
         * @since       3.5.8
         * @return      string
         */
        private function _getFieldOutputByLabel( $sKey, $sLabel, $aField ) {

            $_bIsArray          = is_array( $aField[ 'label' ] );
            $_sClassSelector    = $_bIsArray
                ? 'admin-page-framework-field-text-multiple-labels'
                : '';
            $_sLabel            = $this->getElementByLabel( $aField[ 'label' ], $sKey, $aField[ 'label' ] );
            $aField[ 'value' ]  = $this->getElementByLabel( $aField[ 'value' ], $sKey, $aField[ 'label' ] );
            $_aInputAttributes  = $_bIsArray
                ? array(
                        'name'  => $aField[ 'attributes' ][ 'name' ] . "[{$sKey}]",
                        'id'    => $aField[ 'attributes' ][ 'id' ] . "_{$sKey}",
                        'value' => $aField[ 'value' ],
                    )
                    + $this->getAsArray(
                        $this->getElementByLabel( $aField[ 'attributes' ], $sKey, $aField[ 'label' ] )
                    )    // 3.8.6+ Allows the user to set individual attributes by label.
                    + $aField[ 'attributes' ]
                : $aField[ 'attributes' ];
            $_aOutput           = array(
                $this->getElementByLabel( $aField[ 'before_label' ], $sKey, $aField[ 'label' ] ),
                "<div class='admin-page-framework-input-label-container {$_sClassSelector}'>",
                    "<label for='" . $_aInputAttributes[ 'id' ] . "'>",
                        $this->getElementByLabel( $aField[ 'before_input' ], $sKey, $aField[ 'label' ] ),
                        $_sLabel
                            ? "<span " . $this->getLabelContainerAttributes( $aField, 'admin-page-framework-input-label-string' ) . ">"
                                    . $_sLabel
                                . "</span>"
                            : '',
                        "<input " . $this->getAttributes( $_aInputAttributes ) . " />",
                        $this->getElementByLabel( $aField[ 'after_input' ], $sKey, $aField[ 'label' ] ),
                    "</label>",
                "</div>",
                $this->getElementByLabel( $aField[ 'after_label' ], $sKey, $aField[ 'label' ] ),
            );
            return implode( '', $_aOutput );

        }

}
