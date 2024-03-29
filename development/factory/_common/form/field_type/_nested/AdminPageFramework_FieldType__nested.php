<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Defines the `_nested` field type.
 *
 *
 * @package         AdminPageFramework/Common/Form/FieldType
 * @since           3.8.0
 * @internal
 */
class AdminPageFramework_FieldType__nested extends AdminPageFramework_FieldType {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( '_nested' );

    /**
     * Defines the default key-values of this field type.
     *
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
    );

    /**
     * Returns the output of the field.
     *
     * @since       3.8.0
     * @return      string
     */
    protected function getField( $aField ) {

        $_oCallerForm   = $aField[ '_caller_object' ];

        $_aInlineMixedOutput = array();
        foreach( $this->getAsArray( $aField[ 'content' ] ) as $_aChildFieldset ) {

            if ( is_scalar( $_aChildFieldset ) ) {
                continue;
            }

            if ( ! $this->isNormalPlacement( $_aChildFieldset ) ) {
                continue;
            }

            // Now re-format it so that the field path will be re-generated with the sub-field index.
            $_aChildFieldset = $this->getFieldsetReformattedBySubFieldIndex(
                $_aChildFieldset,
                ( integer ) $aField[ '_index' ],
                $aField[ '_is_multiple_fields' ],
                $aField
            );

            // Generate the output.
            $_oFieldset = new AdminPageFramework_Form_View___Fieldset(
                $_aChildFieldset,
                $_oCallerForm->aSavedData,
                $_oCallerForm->getFieldErrors(),    // @todo Generate field errors. $this->aErrors,
                $_oCallerForm->aFieldTypeDefinitions,
                $_oCallerForm->oMsg,
                $_oCallerForm->aCallbacks // field output element callables.
            );
            $_aInlineMixedOutput[] = $_oFieldset->get(); // field output


        }
        return implode( '', $_aInlineMixedOutput );

    }

}