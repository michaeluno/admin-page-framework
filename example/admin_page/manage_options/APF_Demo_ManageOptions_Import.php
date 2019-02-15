<?php
/**
 * Admin Page Framework Loader
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a tab of the set page to the loader plugin.
 *
 * @since       3.5.0
 */
class APF_Demo_ManageOptions_Import {

    private $_oFactory;
    private $_sClassName;
    private $_sPageSlug;

    private $_sTabSlug   = 'import';
    private $_sSectionID = 'import';

    /**
     * Sets uo properties, hooks, and in-page tabs.
     */
    public function __construct( $oFactory, $sPageSlug ) {

        $this->_oFactory     = $oFactory;
        $this->_sClassName   = $oFactory->oProp->sClassName;
        $this->_sPageSlug    = $sPageSlug;

        $this->_oFactory->addInPageTabs(
            $this->_sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->_sTabSlug,
                'title'         => __( 'Import', 'admin-page-framework-loader' ),
            )
        );

        // load + page slug + tab slug
        add_action( 'load_' . $this->_sPageSlug . '_' . $this->_sTabSlug, array( $this, 'replyToLoadTab' ) );

    }

    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oFactory ) {

        add_action( 'do_' . $this->_sPageSlug . '_' . $this->_sTabSlug, array( $this, 'replyToDoTab' ) );

        $oFactory->addSettingSections(
            $this->_sPageSlug,
            array(
                'section_id'    => $this->_sSectionID,
                'tab_slug'      => $this->_sTabSlug,
                'title'         => __( 'Import Data', 'admin-page-framework-loader' ),
            )
        );
        $oFactory->addSettingFields(
            $this->_sSectionID,
            array(
                'field_id'      => 'import_format_type',
                'title'         => __( 'Import Format Type', 'admin-page-framework-loader' ),
                'type'          => 'radio',
                'description'   => __( 'The text format type will not set the option values properly. However, you can see that the text contents are directly saved in the database.', 'admin-page-framework-loader' ),
                'label'         => array(
                    'json'  => __( 'JSON', 'admin-page-framework-loader' ),
                    'array' => __( 'Serialized Array', 'admin-page-framework-loader' ),
                    'text'  => __( 'Text', 'admin-page-framework-loader' ),
                ),
                'default'       => 'json',
            ),
            array( // Single Import Button
                'field_id'      => 'import_single',
                'title'         => __( 'Single Import Field', 'admin-page-framework-loader' ),
                'type'          => 'import',
                'description'   => __( 'Upload the saved option data.', 'admin-page-framework-loader' ),
                'label'         => __( 'Import Options', 'admin-page-framework-loader' ),
            )
        );

        // import_format_{page slug}_{tab slug}
        add_filter( "import_format_{$this->_sPageSlug}_{$this->_sTabSlug}", array( $this, 'replyToModifyFormat' ), 10, 2 );

        // import_{instantiated class name}_{import section id}_{import field id}
        add_filter( "import_{$this->_oFactory->oProp->sClassName}_{$this->_sSectionID}_import_single", array( $this, 'replyToModifyImportData' ), 10, 6 );

    }

    public function replyToDoTab() {}

    /**
     *
     * @remark      import_format_{page slug}_{tab slug}
     */
    public function replyToModifyFormat( $sFormatType, $sFieldID ) {

        return isset( $_POST[ $this->_oFactory->oProp->sOptionKey ][ $this->_sSectionID ]['import_format_type'] )
            ? $_POST[ $this->_oFactory->oProp->sOptionKey ][ $this->_sSectionID ]['import_format_type']
            : $sFormatType;

    }
    /**
     *
     * @remark      import_{instantiated class name}_{import section id}_{import field id}
     */
    public function replyToModifyImportData( $vData, $aOldOptions, $sFieldID, $sInputID, $sImportFormat, $sOptionKey ) {

        if ( 'text' === $sImportFormat ) {
            $this->_oFactory->setSettingNotice(
                __( 'The text import type is not supported.', 'admin-page-framework-loader' )
            );
            return $aOldOptions;
        }

        $this->_oFactory->setSettingNotice(
            __( 'Importing options were validated.', 'admin-page-framework-loader' ),
            'updated'
        );
        return $vData;

    }

}
