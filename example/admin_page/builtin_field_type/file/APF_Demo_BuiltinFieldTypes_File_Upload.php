<?php
/**
 * Admin Page Framework - Demo
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a section in a tab.
 *
 * @package     AdminPageFramework/Example
 */
class APF_Demo_BuiltinFieldTypes_File_Upload {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'files';

    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'file_uploads';

    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {

        // Section
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'File Uploads', 'admin-page-framework-loader' ),
                'tip'           => __( 'The <code>file</code> field type lets your users uploade and submit their files.', 'admin-page-framework-loader' ),
                'description'   => __( 'These are upload fields. Check the <code>$_FILES</code> variable in the validation callback method that indicates the temporary location of the uploaded files.', 'admin-page-framework-loader' ),
            )
        );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID,
            array(
                'field_id'              => 'file_single',
                'title'                 => __( 'File', 'admin-page-framework-loader' ),
                'type'                  => 'file',
                'label'                 => __( 'Select the file', 'admin-page-framework-loader' ) . ": ",
                'description'           => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'  => 'file',
    'label' => 'Select the file',
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'              => 'file_multiple',
                'title'                 => __( 'Multiple', 'admin-page-framework-loader' ),
                'type'                  => 'file',
                'label'                 => __( 'First', 'admin-page-framework-loader' ),
                'delimiter'             => '<br />',
                array(
                    'label' => __( 'Second', 'admin-page-framework-loader' ),
                ),
                array(
                    'label' => __( 'Third', 'admin-page-framework-loader' ),
                ),
                'description'           => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'                  => 'file',
    'label'                 => __( 'First', 'admin-page-framework-loader' ),
    'delimiter'             => '<br />',
    array(
        'label' => __( 'Second', 'admin-page-framework-loader' ),
    ),
    array(
        'label' => __( 'Third', 'admin-page-framework-loader' ),
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'              => 'file_repeatable',
                'title'                 => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'                  => 'file',
                'repeatable'            => true,
'description'           => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'                  => 'file',
    'repeatable'            => true,
)
EOD
                        )
                        . "</pre>",
                ),
            )
        );

        add_filter(
            'validation_' . $oFactory->oProp->sClassName . '_' . $this->sSectionID,
            array( $this, 'validateSectionForm' ),
            10,
            4
        );

    }


    /**
     * Validates the items in the 'files' tab of the 'apf_bultin_field_types' page.
     *
     * @callback        filter      validation_{class name}_{section id}
     */
    public function validateSectionForm( $aInput, $aOldInput, $oFactory, $aSubmitInfo ) {

        // Display the uploaded file information.
        $_aFileErrors   = array();
        $_aFileErrors[] = $_FILES[ $oFactory->oProp->sOptionKey ][ 'error' ][ $this->sSectionID ][ 'file_single' ];
        $_aFileErrors[] = $_FILES[ $oFactory->oProp->sOptionKey ][ 'error' ][ $this->sSectionID ][ 'file_multiple' ][0];
        $_aFileErrors[] = $_FILES[ $oFactory->oProp->sOptionKey ][ 'error' ][ $this->sSectionID ][ 'file_multiple' ][1];
        $_aFileErrors[] = $_FILES[ $oFactory->oProp->sOptionKey ][ 'error' ][ $this->sSectionID ][ 'file_multiple' ][2];

        $_aFiles = $oFactory->oUtil->getElementAsArray(
            $_FILES,
            array( $oFactory->oProp->sOptionKey, 'error', $this->sSectionID, 'file_repeatable' )
        );
        foreach( $_aFiles as $_aFile ) {
            $_aFileErrors[] = $_aFile;
        }

        if ( in_array( 0, $_aFileErrors ) ) {
            $oFactory->setSettingNotice(
                __( '<h3>File(s) Uploaded</h3>', 'admin-page-framework-loader' ) . $oFactory->oDebug->getArray( $_FILES ),
                'updated'
            );
        }

        return $aInput;

    }

}
