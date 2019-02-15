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
 * @since       3.8.8
 */
class APF_Demo_CustomFieldType_PostTypeTaxonomy {

    public $oFactory;

    public $sClassName;

    public $sPageSlug;

    public $sTabSlug = 'post_type_taxonomy';

    public function __construct( $oFactory, $sPageSlug ) {

        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug;
        $this->sSectionID   = $this->sTabSlug;

        $this->oFactory->addInPageTabs(
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Taxonomy', 'admin-page-framework-loader' ),
            )
        );

        // Register the field type.
        new PostTypeTaxonomyCustomFieldType( $this->sClassName );

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
                'title'         => __( 'Post Type Taxonomy Terms', 'admin-page-framework-loader' ),
                'description'   => array(
                    __( 'When the user checks on a post type, associated taxonomy term check-boxes will appear.', 'admin-page-framework-loader' )
                ),
            )
        );

        // Fields
        $oAdminPage->addSettingFields(
            $this->sSectionID,
            array(
                'field_id'      => 'default',
                'type'          => 'post_type_taxonomy',
                'title'         => __( 'Default', 'admin-page-framework-loader' ),
                'description'    => array(
                    sprintf( __( 'For the argument of <code>query</code>, see <a href="%1$s" target="_blank">here</a>.', 'admin-page-framework-loader' ),
                        'http://codex.wordpress.org/Function_Reference/get_post_types#Parameters'
                    ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'post_type_taxonomy',  
)
EOD
                        )
                        . "</pre>"
                ),
            ),
            array(
                'field_id'      => 'repeatable_and_sortable',
                'type'          => 'post_type_taxonomy',
                'title'         => __( 'Repeatable and Sortable', 'admin-page-framework-loader' ),
                'repeatable'    => true,
                'sortable'      => true,
                'description'    => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'post_type_taxonomy',
    'repeatable'    => true,
    'sortable'      => true,    
)
EOD
                        )
                        . "</pre>"
                ),
            ),
            array()
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
