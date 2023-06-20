<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to render a field title
 *
 * @package     AdminPageFramework/Common/Form/View/Field
 * @extends     AdminPageFramework_Form_Utility
 * @since       3.8.0
 * @internal
 */
class AdminPageFramework_Form_View___FieldTitle extends AdminPageFramework_Form_Utility {

    public $aFieldset = array();

    /**
     * Stores additional class selectors
     */
    public $aClassSelectors = array();

    public $aSavedData = array();

    public $aFieldErrors = array();
    public $aFieldTypeDefinitions = array();

    public $aCallbacks = array();
    public $oMsg = array();

    /**
     * Sets up properties.
     * @since       3.8.0
     */
    public function __construct( /* array $aFieldset, $aClassSelectors, $aSavedData, $aFieldErrors, $aFieldTypeDefinitions, $aCallbacks, $oMsg */ ) {

        $_aParameters = func_get_args() + array(
            $this->aFieldset,
            $this->aClassSelectors,
            $this->aSavedData,
            $this->aFieldErrors,
            $this->aFieldTypeDefinitions,
            $this->aCallbacks,
            $this->oMsg
        );
        $this->aFieldset                = $_aParameters[ 0 ];
        $this->aClassSelectors          = $_aParameters[ 1 ];
        $this->aSavedData               = $_aParameters[ 2 ];
        $this->aFieldErrors             = $_aParameters[ 3 ];
        $this->aFieldTypeDefinitions    = $_aParameters[ 4 ];
        $this->aCallbacks               = $_aParameters[ 5 ];
        $this->oMsg                     = $_aParameters[ 6 ];

    }

    /**
     * Returns a field title.
     *
     * @since       3.8.0
     * @return      string      The output of a field title.
     */
    public function get() {

        $_sOutput = '';

        $aField = $this->aFieldset;

        if ( ! $aField[ 'show_title_column' ] ) {
            return '';
        }

        $_oInputTagIDGenerator = new AdminPageFramework_Form_View___Generate_FieldInputID(
            $aField,
            0   // the first item
        );

        $_aLabelAttributes = array(
            'class' => $this->getClassAttribute( 'admin-page-framework-field-title', $this->aClassSelectors ),
            'for'   => $_oInputTagIDGenerator->get(),
        );
        $_sOutput .= $aField[ 'title' ]
            ? "<label " . $this->getAttributes( $_aLabelAttributes ) . "'>"
                    . "<a id='{$aField[ 'field_id' ]}'></a>"  // to allow the browser to link to the element.
                    . "<span title='"
                            . esc_attr(
                                strip_tags(
                                    is_array( $aField[ 'description' ] )
                                        ? implode( '&#10;', $aField[ 'description' ] )
                                        : ( is_string( $aField[ 'description' ] ) ? $aField[ 'description' ] : '' )
                                )
                            )
                        . "'>"
                            . $aField[ 'title' ]
                            . $this->_getTitleColon( $aField )
                    . "</span>"
                    . $this->___getToolTip( $aField[ 'tip' ], $aField[ 'field_id' ] )
                . "</label>"
            : '';

        $_sOutput .= $this->_getFieldOutputsInFieldTitleAreaFromNestedFields( $aField );
        return $_sOutput;
    }

        /**
         * Generates the field outputs from the nested fields with the `placement` argument of the value of `field_title`.
         * @return      string
         * @since       3.8.0
         * @internal
         */
        private function _getFieldOutputsInFieldTitleAreaFromNestedFields( $aField ) {

            if ( ! $this->hasNestedFields( $aField ) ) {
                return '';
            }

            $_sOutput = '';
            foreach( $aField[ 'content' ] as $_aNestedField ) {

                if ( 'field_title' !== $_aNestedField[ 'placement' ] ) {
                    continue;
                }

                $_oFieldset = new AdminPageFramework_Form_View___Fieldset(
                    $_aNestedField,
                    $this->aSavedData,    // passed by reference. @todo: examine why it needs to be passed by reference.
                    $this->aFieldErrors,
                    $this->aFieldTypeDefinitions,
                    $this->oMsg,
                    $this->aCallbacks // field output element callables.
                );
                $_sOutput   .= $_oFieldset->get(); // field output

            }
            return $_sOutput;

        }

        /**
         * @return      string
         * @since       3.7.0
         */
        private function ___getToolTip( $asTip, $sElementID ) {
            $_oToolTip           = new AdminPageFramework_Form_View___ToolTip(
                $asTip,
                $sElementID
            );
            return $_oToolTip->get();
        }

        /**
         * @since       3.7.0
         * @return      string
         */
        private function _getTitleColon( $aField ) {

            if ( ! isset( $aField[ 'title' ] ) || '' === $aField[ 'title' ] ) {
                return '';
            }
            if (
                in_array(
                    $aField[ '_structure_type' ],
                    array( 'widget', 'post_meta_box', 'page_meta_box' )
                )
            ){
                return "<span class='title-colon'>:</span>" ;
            }

        }

}
