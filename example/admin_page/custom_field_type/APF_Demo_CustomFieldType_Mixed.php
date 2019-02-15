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
 * @since       3.8.7
 */
class APF_Demo_CustomFieldType_Mixed {

    public $oFactory;

    public $sClassName;

    public $sPageSlug;

    public $sTabSlug = 'custom_mixed';

    public function __construct( $oFactory, $sPageSlug ) {

        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug;
        $this->sSectionID   = $this->sTabSlug;

        $this->oFactory->addInPageTabs(
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Mixed', 'admin-page-framework-loader' ),
            )
        );

        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );

    }

    /**
     * Triggered when the tab starts loading.
     *
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oAdminPage ) {

        $this->registerFieldTypes( $this->sClassName );

        add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );

         // Section
        $oAdminPage->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Various Field Types', 'admin-page-framework-loader' ),
                'repeatable'    => true,
                'sortable'      => true,
                'collapsible'   => array(
                    'toggle_all_button' => array( 'top-left', 'bottom-left' ),
                    'container'         => 'section',
                ),
                'description'   => array(
                    __( 'This section shows various custom field types.', 'admin-page-framework-loader' )
                ),
            )
        );

        // Fields
        $oAdminPage->addSettingFields(
            $this->sSectionID,
            array(
                'field_id'      => 'toggle_status',
                'type'          => 'radio',
                'placement'     => 'section_title',
                'label' => array(
                    '1' => 'On',
                    '0' => 'Off',
                ),
                'label_min_width' => 0,
            ),
            array(
                'field_id'      => 'toggle_status',
                'type'          => 'toggle',
                'placement'     => 'section_title',
                'default'       => true,
                'options'       => array(
                    'width'  => 100,
                    'height' => 20,
                ),
            ),
            array(
                'field_id'      => 'path_field_repeatable_sortable',
                'type'          => 'path',
                'title'         => __( 'Path', 'admin-page-framework-loader' ),
                'repeatable'    => true,
                'sortable'      => true,
            ),
            array(
                'field_id'      => 'no_ui_slider_repeatable_sortable',
                'type'          => 'no_ui_slider',
                'title'         => __( 'Slider', 'admin-page-framework-loader' ),
                'repeatable'    => true,
                'sortable'      => true,
                'options'       => array(
                    'start'     => array( 10, ),
                    'connect'   => array( true, false ),
                ),
            ),
            array(
                'field_id'        => 'ajax_repeatable_sortable',
                'type'            => 'select2',
                'title'           => __( 'Select2', 'admin-page-framework-loader' ),
                'repeatable'      => true,
                'sortable'        => true,
                'options'         => array(
                    'width'      => '100%',
                ),
                'callback'        => array(
                    'search'    => 'APF_Demo_CustomFieldType_Select2::getPosts',
                ),
            ),
            array(
                'field_id'      => 'select_post_taxonomy',
                'title'         => __( 'Post Types and Taxonomies', 'admin-page-framework-loader' ),
                'repeatable'    => true,
                'sortable'      => true,
                'content'       => array(
                    array(
                        'type'      => 'select',
                        'field_id'  => 'select',
                        'label'     => array(
                            'include'       => __( 'Include', 'admin-page-framework-loader' ),
                            'ixclude'       => __( 'Exclude', 'admin-page-framework-loader' ),
                        ),
                    ),
                    array(
                        'field_id'      => 'post_type_taxonomy',
                        'type'          => 'post_type_taxonomy',
                    ),
                ),
            ),
            array()
        );

    }

        /**
         * Registers the field types.
         */
        private function registerFieldTypes( $sClassName ) {

        }


    public function replyToDoTab() {
        submit_button();
    }

}
