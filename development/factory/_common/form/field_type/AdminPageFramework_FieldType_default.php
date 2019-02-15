<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Defines the default field type.
 *
 * When no field type slug is found with the field type slug that the user set, this field type will be applied.
 *
 * @package     AdminPageFramework/Common/Form/FieldType
 * @since       2.1.5
 * @internal
 */
class AdminPageFramework_FieldType_default extends AdminPageFramework_FieldType {

    /**
     * Defines the default key-values of this field type.
     *
     * @access      public      This must be public as accessed from outside.
     * @remark      <var>$_aDefaultKeys</var> holds shared default key-values defined in the base class.
     */
    public $aDefaultKeys = array();

    /**
     * Returns the output of the field type.
     *
     * This one is triggered when the called field type is unknown. This does not insert the input tag but just renders the value stored in the $vValue variable.
     *
     * @since       2.1.5
     * @since       3.0.0       Removed unnecessary elements as well as parameters.
     */
    public function _replyToGetField( $aField ) {
        return
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']
                    . ( $aField['label'] && ! $aField['repeatable']
                        ? "<span " . $this->getLabelContainerAttributes( $aField, 'admin-page-framework-input-label-string' ) . ">"
                                . $aField[ 'label' ]
                            . "</span>"
                        : ""
                    )
                    . $aField['value']
                    . $aField['after_input']
                . "</label>"
            . "</div>"
            . $aField['after_label']
        ;
    }

}
