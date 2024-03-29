<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to render forms.
 *
 * @package     AdminPageFramework/Common/Form/View/Field
 * @since       3.7.0
 * @extends     AdminPageFramework_FrameworkUtility
 * @internal
 */
class AdminPageFramework_Form_View___FieldsetRows extends AdminPageFramework_FrameworkUtility {

    public $aFieldsetsPerSection    = array();
    public $iSectionIndex           = null;
    public $aSavedData              = array();
    public $aFieldErrors            = array();
    public $aFieldTypeDefinitions   = array();
    public $aCallbacks              = array();
    public $oMsg;

    /**
     * Sets up properties.
     * @since       3.7.0
     */
    public function __construct( /* $aFieldsetsPerSection, $iSectionIndex, $aSavedData, $aFieldErrors, $aCallbacks=array(), $oMsg */ ) {

        $_aParameters = func_get_args() + array(
            $this->aFieldsetsPerSection,
            $this->iSectionIndex,
            $this->aSavedData,
            $this->aFieldErrors,
            $this->aFieldTypeDefinitions,
            $this->aCallbacks,
            $this->oMsg,
        );
        $this->aFieldsetsPerSection  = $_aParameters[ 0 ];
        $this->iSectionIndex         = $_aParameters[ 1 ];
        $this->aSavedData            = $_aParameters[ 2 ];
        $this->aFieldErrors          = $_aParameters[ 3 ];
        $this->aFieldTypeDefinitions = $_aParameters[ 4 ];
        $this->aCallbacks            = $_aParameters[ 5 ] + $this->aCallbacks;
        $this->oMsg                  = $_aParameters[ 6 ];

    }

    /**
     * Returns the output of table rows of fieldsets.
     *
     * The each row has an enclosing `<td>` tag.
     *
     * @return      string
     * @since       3.7.0
     */
    public function get( $bTableRow=true ) {

        $_sMethodName = $this->getAOrB(
            $bTableRow,
            '_getFieldsetRow',
            '_getFieldset'
        );

        $_sOutput = '';
        foreach( $this->aFieldsetsPerSection as $_aFieldset ) {

            $_oFieldsetOutputFormatter = new AdminPageFramework_Form_Model___Format_FieldsetOutput(
                $_aFieldset,
                $this->iSectionIndex,
                $this->aFieldTypeDefinitions
            );

            $_aFieldset = $_oFieldsetOutputFormatter->get();
            if ( ! $this->callBack( $this->aCallbacks[ 'is_fieldset_visible' ], array( true, $_aFieldset ) ) ) {
                continue;
            }

            $_sOutput .= call_user_func_array( array( $this, $_sMethodName ), array( $_aFieldset ) );

        }
        return $_sOutput;

    }
        /**
         * Returns a fieldset output enclosed in a `tr` and `td` tag.
         * @return      string
         * @since       3.7.0
         */
        private function _getFieldsetRow( $aFieldset ) {

            $_oFieldsetRow = new AdminPageFramework_Form_View___FieldsetTableRow(
                $aFieldset, // a field set definition array
                $this->aSavedData,
                $this->aFieldErrors,
                $this->aFieldTypeDefinitions,
                $this->aCallbacks,
                $this->oMsg
            );
            return $_oFieldsetRow->get();

        }

        /**
         * Returns a fieldset output enclosed in a `div` tag.
         * @return      string
         */
        private function _getFieldset( $aFieldset ) {

            $_oFieldsetRow = new AdminPageFramework_Form_View___FieldsetRow(
                $aFieldset, // a field set definition array
                $this->aSavedData,
                $this->aFieldErrors,
                $this->aFieldTypeDefinitions,
                $this->aCallbacks,
                $this->oMsg
            );
            return $_oFieldsetRow->get();

        }

}
