<?php
/**
 * Admin Page Framework Loader
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a tab of the set page to the loader plugin.
 *
 * @since 3.9.0
 */
class APF_Demo_CustomFieldType_Path2 {

    public $oFactory;

    public $sClassName;

    public $sPageSlug;

    public $sTabSlug = 'path2';

    public $sSectionID;

    public function __construct( $oFactory, $sPageSlug ) {

        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug;
        $this->sSectionID   = $this->sTabSlug;

        $this->oFactory->addInPageTabs(
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => 'Path2',
            )
        );

        // Register the field type.
        new Path2CustomFieldType( $this->sClassName );

        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );

    }

    /**
     * Triggered when the tab starts loading.
     *
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oAdminPage ) {

        add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );

         // Section
        $oAdminPage->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'File Path Selector', 'admin-page-framework-loader' ),
                'description'   => array(
                    __( 'This field type lets the user select a file path.', 'admin-page-framework-loader' )
                    . ' '
                    . __( 'The relative path to the value of <code>$_SERVER[ "DOCUMENT_ROOT" ]</code> (the document root set by the web server) will be set.', 'admin-page-framework-loader' ),
                ),
            )
        );

        // Fields
        $oAdminPage->addSettingFields(
            $this->sSectionID,
            array(
                'field_id'      => 'path2_field',
                'type'          => 'path2',
                'title'         => 'Path2',
                'options'       => array(
                    'fileExtensions' => 'php',
                    'root'           => ABSPATH,
                ),
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'path2',   
    'options'       => array(
        'fileExtensions' => 'php',
        'root'           => ABSPATH,
    ),
)
EOD
                        )
                        . "</pre>"
                ),
            ),
            array(
                'field_id'      => 'path2_field_repeatable_sortable',
                'type'          => 'path2',
                'title'         => __( 'Repeatable & Sortable', 'admin-page-framework-loader' ),
                'repeatable'    => true,
                'sortable'      => true,
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'path2',
    'repeatable'    => true,
    'sortable'      => true,
)
EOD
                        )
                        . "</pre>"
                ),
            )
        );

    }



    public function replyToDoTab() {
        submit_button();
    }

}
