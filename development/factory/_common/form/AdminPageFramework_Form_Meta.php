<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to build forms for the user meta and post meta structure type.
 *
 * This is used by taxonomy meta, user meta, and post meta form classes.
 *
 * @package     AdminPageFramework/Common/Form
 * @since       3.7.0
 * @extends     AdminPageFramework_Form
 * @internal
 */
class AdminPageFramework_Form_Meta extends AdminPageFramework_Form {

    /**
     * Saves the meta data with the given object ID with the given type with the user submit data.
     *
     * Used by the post meta box and user meta classes.
     *
     * @since       3.5.3
     * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Meta`.
     * @param       integer     $iObjectID      The ID that is associated with the meta data such as user ID and post ID.
     * @param       array       $aInput         The user submit form input data.
     * @param       array       $aSavedMeta     The stored form data (old input data).
     * @param       string      $sStructureType    The type of object. Currently 'post_meta_box' or 'user_meta' is accepted.
     * @return      void
     */
    public function updateMetaDataByType( $iObjectID, array $aInput, array $aSavedMeta, $sStructureType='post_meta_box' ) {

        if ( ! $iObjectID ) {
            return;
        }

        $_aFunctionNameMapByFieldsType = array(
            'post_meta_box'     => 'update_post_meta',
            'user_meta'         => 'update_user_meta',
            'term_meta'         => 'update_term_meta',
        );
        if ( ! in_array( $sStructureType, array_keys( $_aFunctionNameMapByFieldsType ) ) ) {
            return;
        }
        $_sFunctionName = $this->getElement( $_aFunctionNameMapByFieldsType, $sStructureType );

        // 3.6.0+ Unset field elements that the 'save' argument is false.
        $aInput = $this->getInputsUnset( $aInput, $this->sStructureType );

        // Loop through sections/fields and save the data.
        foreach ( $aInput as $_sSectionOrFieldID => $_vValue ) {
            $this->_updateMetaDatumByFunctionName(
                $iObjectID,
                $_vValue,
                $aSavedMeta,
                $_sSectionOrFieldID,
                $_sFunctionName
            );
        }

    }

        /**
         * Saves an individual meta datum with the given section or field ID with the given function name.
         *
         * @internal
         * @since       3.5.3
         * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Meta`.
         * @return      void
         */
        private function _updateMetaDatumByFunctionName( $iObjectID, $_vValue, array $aSavedMeta, $_sSectionOrFieldID, $_sFunctionName ) {

            if ( is_null( $_vValue ) ) {
                return;
            }

            $_vSavedValue = $this->getElement(
                $aSavedMeta, // subject
                $_sSectionOrFieldID,    // dimensional keys
                null   // default value
            );

            // PHP can compare even array contents with the == operator. See http://www.php.net/manual/en/language.operators.array.php
            // if the input value and the saved meta value are the same, no need to update it.
            if ( $_vValue == $_vSavedValue ) {
                return;
            }

            // Currently either 'update_post_meta' or 'update_user_meta'
            $_sFunctionName( $iObjectID, $_sSectionOrFieldID, $_vValue );

        }

}
