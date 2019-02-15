<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to format and generate <em>fieldset</em> container HTML attributes.
 *
 * @package     AdminPageFramework/Common/Form/View/Attribute
 * @since       3.6.0
 * @extends     AdminPageFramework_Form_View___Attribute_FieldContainer_Base
 * @internal
 */
class AdminPageFramework_Form_View___Attribute_Fieldset extends AdminPageFramework_Form_View___Attribute_FieldContainer_Base {

    /**
     * Indicates the context of the attribute.
     *
     * e.g. fieldset, fieldrow etc.
     *
     * @since       3.6.0
     */
    public $sContext    = 'fieldset';

    /**
     * Returns an attribute array.
     * @return      array
     */
    protected function _getAttributes() {
        return array(
            'id'            => $this->sContext . '-' . $this->aArguments[ 'tag_id' ],
            'class'         => implode(
                ' ',
                array(
                    'admin-page-framework-' . $this->sContext,
                    $this->_getSelectorForChildFieldset()
                )
            ),
            'data-field_id' => $this->aArguments[ 'tag_id' ], // <-- not sure what this was for...
            'style'         => $this->_getInlineCSS(),
        );
    }

        /**
         * Returns inline CSS rules for the section-set element.
         * @since       3.8.10
         */
        private function _getInlineCSS() {
            return ( 1 <= $this->aArguments[ '_nested_depth' ] ) && $this->aArguments[ 'hidden' ]
                ? 'display:none'
                : null;
        }

        /**
         * Returns a class selector for the HTML class attribute for nested field-sets.
         *
         * @return      string
         * @since       3.8.0
         */
        private function _getSelectorForChildFieldset() {

            if ( $this->aArguments[ '_nested_depth' ] == 0 ) {
                return '';
            }
            if ( $this->aArguments[ '_nested_depth' ] == 1 ) {
                return 'child-fieldset nested-depth-' . $this->aArguments[ '_nested_depth' ];
            }
            return 'child-fieldset multiple-nesting nested-depth-' . $this->aArguments[ '_nested_depth' ];

        }

}
