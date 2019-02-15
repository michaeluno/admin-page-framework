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
 * @since       3.8.4
 */
class APF_Demo_CustomFieldType_Path {

    public $oFactory;

    public $sClassName;

    public $sPageSlug;

    public $sTabSlug = 'path';

    public function __construct( $oFactory, $sPageSlug ) {

        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug;
        $this->sSectionID   = $this->sTabSlug;

        $this->oFactory->addInPageTabs(
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Path', 'admin-page-framework-loader' ),
            )
        );

        // Register the field type.
        new PathCustomFieldType( $this->sClassName );

        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );

    }

    /**
     * Triggered when the tab starts loading.
     *
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oAdminPage ) {

        // $this->registerFieldTypes( $this->sClassName );

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
                'field_id'      => 'path_field',
                'type'          => 'path',
                'title'         => __( 'Path', 'admin-page-framework-loader' ),
                // @see For the list of arguments, refer to https://github.com/jqueryfiletree/jqueryfiletree#configuring-the-file-tree
                'options'       => array(
                    'root'  => ABSPATH,
                    'fileExtensions'    => 'php,txt',
                ),
                'description'    => array(
                    __( 'With the <code>fileExtensions</code> option, listed file types can be specified.', 'admin-page-framework-loader' ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'path',
    'options'       => array(
        'root'              => ABSPATH,
        'fileExtensions'    => 'php,txt',
    ),     
)
EOD
                        )
                        . "</pre>"
                ),
            ),
            array(
                'field_id'      => 'path_field_repeatable_sortable',
                'type'          => 'path',
                'title'         => __( 'Repeatable & Sortable', 'admin-page-framework-loader' ),
                'repeatable'    => true,
                'sortable'      => true,
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'path',
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

        /**
         * Registers the field types.
         */
        private function registerFieldTypes( $sClassName ) {
            new PathCustomFieldType( $sClassName );
        }


    public function replyToDoTab() {
        submit_button();
    }

}
