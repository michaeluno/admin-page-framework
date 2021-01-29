<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to build forms of the `term_meta` structure type.
 *
 * The suffix represents the structure type of the form.
 *
 * @package     AdminPageFramework/Factory/TermMeta/Form
 * @since       3.8.0
 * @extends     AdminPageFramework_Form_taxonomy_field
 * @internal
 */
class AdminPageFramework_Form_term_meta extends AdminPageFramework_Form_Meta {

    public $sStructureType = 'term_meta';

    /**
     * Does set-ups.
     * @since       3.8.0
     * @return      void
     */
    public function construct() {
        $this->_addDefaultResources();
    }
        /**
         * @return      void
         * @since       3.8.0
         */
        private function _addDefaultResources() {
            $_oCSS = new AdminPageFramework_Form_View___CSS_term_meta;
            $this->addResource( 'internal_styles', $_oCSS->get() );
        }

    /**
     * Rerieves the form fields output.
     *
     * @return      string
     */
    public function get( /* $bEditTerm */ ) {

        $_aArguments = func_get_args() + array( false );
        $bEditTerm   = $_aArguments[ 0 ];

        $this->sCapability = $this->callback(
            $this->aCallbacks[ 'capability' ],
            '' // default value
        );

        if ( ! $this->canUserView( $this->sCapability ) ) {
            return '';
        }

        // Format and update sectionset and fieldset definitions.
        $this->_formatElementDefinitions( $this->aSavedData );

        // Load scripts for forms.
        new AdminPageFramework_Form_View___Script_Form;

        $_oFormTables = new AdminPageFramework_Form_View___Sectionsets(
            // Arguments which determine the object behaviour
            array(
                'capability'                => $this->sCapability,
            ) + $this->aArguments,
            // Form structure definitions
            array(
                'field_type_definitions'    => $this->aFieldTypeDefinitions,
                'sectionsets'               => $this->aSectionsets,
                'fieldsets'                 => $this->aFieldsets,
            ),
            $this->aSavedData,
            $this->getFieldErrors(),
            $this->aCallbacks,
            $this->oMsg
        );

        $_sAddNewTerm     = $bEditTerm ? '' : ' add-new-term';
        $_sClassSelectors = 'admin-page-framework-form-table-term_meta' . $_sAddNewTerm;
        return '<tr class="admin-page-framework-form-table-outer-row-term_meta">'
            . '<td colspan=2>'
                . '<table class="' . $_sClassSelectors . '">'
                    . '<tbody>'
                        . '<tr>'
                            . '<td>'
                                . $_oFormTables->get()
                            . '</td>'
                        . '</tr>'
                    . '</tbody>'
                . '</table>'
            . '</td>'
        . '</tr>';


    }

}
