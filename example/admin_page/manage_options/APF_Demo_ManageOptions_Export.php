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
class APF_Demo_ManageOptions_Export {

    private $_oFactory;
    private $_sClassName;
    private $_sPageSlug;

    private $_sTabSlug   = 'export';
    private $_sSectionID = 'export';

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
                'title'         => __( 'Export', 'admin-page-framework-loader' ),
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
                'title'         => __( 'Export Data', 'admin-page-frameowork-demo' ),
                'description'   => __( 'After exporting the options, change and save new options and then import the file to see if the options get restored.', 'admin-page-framework-loader' ),
            )
        );

       $oFactory->addSettingFields(
            $this->_sSectionID,  // target section id
            array(
                'field_id'      => 'export_format_type',
                'title'         => __( 'Export Format Type', 'admin-page-framework-loader' ),
                'type'          => 'radio',
                'description'   => __( 'Choose the file format. Array means the PHP serialized array.', 'admin-page-framework-loader' ),
                'label'         => array(
                    'json'  => __( 'JSON', 'admin-page-framework-loader' ),
                    'array' => __( 'Serialized Array', 'admin-page-framework-loader' ),
                    'text'  => __( 'Text', 'admin-page-framework-loader' ),
                ),
                'default'       => 'json',
            ),
            array( // Single Export Button
                'field_id'      => 'export_single',
                'type'          => 'export',
                'description'   => __( 'Download the saved option data.', 'admin-page-framework-loader' ),
            ),
            array( // Multiple Export Buttons
                'field_id'      => 'export_multiple',
                'title'         => __( 'Multiple Export Buttons', 'admin-page-framework-loader' ),
                'type'          => 'export',
                'label'         => __( 'Plain Text', 'admin-page-framework-loader' ),
                'file_name'     => 'plain_text.txt',
                'format'        => 'text',
                'attributes'    => array(
                    'field' => array(
                        'style' => 'display: inline; clear: none;',
                    ),
                ),
                array(
                    'label'     => __( 'JSON', 'admin-page-framework-loader' ),
                    'file_name' => 'json.json',
                    'format'    => 'json',
                ),
                array(
                    'label'     => __( 'Serialized Array', 'admin-page-framework-loader' ),
                    'file_name' => 'serialized_array.txt',
                    'format'    => 'array',
                ),
                'description' => __( 'To set a file name, use the <code>file_name</code> argument in the field definition array.', 'admin-page-framework-loader' )
                    . ' ' . __( 'To set the data format, use the <code>format</code> argument in the field definition array.', 'admin-page-framework-loader' ),
            ),
            array( // Custom Data to Export
                'field_id'      => 'export_custom_data',
                'title'         => __( 'Custom Exporting Data', 'admin-page-framework-loader' ),
                'type'          => 'export',
                'data'          => __( 'Hello World! This is custom export data.', 'admin-page-framework-loader' ),
                'file_name'     => 'hello_world.txt',
                'label'         => __( 'Export Custom Data', 'admin-page-framework-loader' ),
                'description'   => __( 'It is possible to set custom data to be downloaded. For that, use the <code>data</code> argument in the field definition array.', 'admin-page-framework-loader' ),
            )
        );

        // export_name_{instantiated class name}_{export section id}_{export field id}
        add_filter( "export_name_{$this->_oFactory->oProp->sClassName}_{$this->_sSectionID}_export_single", array( $this, 'replyToModifyFileName' ), 10, 5 );

        // export_format_{instantiated class name}_{export section id}_{export field id}
        add_filter( "export_format_{$this->_oFactory->oProp->sClassName}_{$this->_sSectionID}_export_single", array( $this, 'replyToModifyFileType' ), 10, 2 );

    }

    public function replyToDoTab() {


    }

    /**
     *
     * @remark      export_name_{instantiated class name}_{export section id}_{export field id}
     */
    public function replyToModifyFileName( $sFileName, $sFieldID, $sInputID, $vData, $oFactory ) {

        // Change the exporting file name based on the selected format type in the other field.
        $sSelectedFormatType = isset( $_POST[ $this->_oFactory->oProp->sOptionKey ][ $this->_sSectionID ]['export_format_type'] )
            ? $_POST[ $this->_oFactory->oProp->sOptionKey ][ $this->_sSectionID ]['export_format_type']
            : null;
        $aFileNameParts = pathinfo( $sFileName );
        $sFileNameWOExt = $aFileNameParts['filename'];
        switch( $sSelectedFormatType ) {
            default:
            case 'json':
                $sReturnName = $sFileNameWOExt . '.json';
                break;
            case 'text':
            case 'array':
                $sReturnName = $sFileNameWOExt . '.txt';
                break;
        }
        return $sReturnName;

    }

    /**
     *
     *
     * @remark      export_format_{instantiated class name}_{export section id}_{export field id}
     */
    public function replyToModifyFileType( $sFormatType, $sFieldID ) {

        // Set the internal formatting type based on the selected format type in the other field.
        return isset( $_POST[ $this->_oFactory->oProp->sOptionKey ][ $this->_sSectionID ]['export_format_type'] )
            ? $_POST[ $this->_oFactory->oProp->sOptionKey ][ $this->_sSectionID ]['export_format_type']
            : $sFormatType;

    }

}
