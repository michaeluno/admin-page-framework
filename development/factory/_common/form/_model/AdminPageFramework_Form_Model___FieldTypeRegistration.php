<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to register field types.
 *
 * @package     AdminPageFramework/Common/Form/Model
 * @since       3.7.0
 * @extends     AdminPageFramework_FrameworkUtility
 * @internal
 */
class AdminPageFramework_Form_Model___FieldTypeRegistration extends AdminPageFramework_FrameworkUtility {

    /**
     * Initializes the field type.
     * @since       3.7.0
     */
    public function __construct( array $aFieldTypeDefinition, $sStructureType ) {

        $this->_initialize(
            $aFieldTypeDefinition,
            $sStructureType
        );

    }

        /**
         * Runs the initializer the given field type.
         *
         * @since       3.5.3
         * @since       3.7.0  Moved from `AdminPageFramework_FieldTypeRegistration`. Changed it not static. Chaned the name from `_initializeFieldType()`.
         * @return      void
         */
        private function _initialize( $aFieldTypeDefinition, $sStructureType ) {

            if ( is_callable( $aFieldTypeDefinition[ 'hfFieldSetTypeSetter' ] ) ) {
                call_user_func_array(
                    $aFieldTypeDefinition[ 'hfFieldSetTypeSetter' ],
                    array( $sStructureType )
               );
            }

            if ( is_callable( $aFieldTypeDefinition[ 'hfFieldLoader' ] ) ) {
                call_user_func_array(
                    $aFieldTypeDefinition[ 'hfFieldLoader' ],
                    array()
                );
            }

        }

}
