<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods that generates field id.
 *
 * @package     AdminPageFramework/Common/Form/View/Generator
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Form_View___Generate_FieldName extends AdminPageFramework_Form_View___Generate_Field_Base {

    /**
     *
     * @return      string       The generated string value.
     */
    public function get() {
        $_sResult = $this->_getFiltered( $this->_getFieldName() );
        return $_sResult;
    }

    public function getModel()     {
        return $this->get() . '[' . $this->sIndexMark . ']';
    }

        /**
         * @return      string
         */
        protected function _getFieldName() {

            $_aFieldPath   = $this->aArguments[ '_field_path_array' ];
            if ( ! $this->_isSectionSet() ) {
                return $this->_getInputNameConstructed( $_aFieldPath );
            }

            $_aSectionPath = $this->aArguments[ '_section_path_array' ];
            if ( $this->_isSectionSet() && isset( $this->aArguments[ '_section_index' ] ) ) {
                $_aSectionPath[] = $this->aArguments[ '_section_index' ];
            }
            $_sFieldName = $this->_getInputNameConstructed(
                array_merge( $_aSectionPath, $_aFieldPath )
            );
            return $_sFieldName;

        }

}
