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
class APF_Demo_AdvancedUsage_Complex_Collapsible {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_advanced_usage';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'complex';

    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'collapsible';

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
                'title'         => __( 'More Complex Form', 'admin-page-framework-loader' ),
                'collapsible'       => array(
                    'toggle_all_button' => array( 'top-left', 'bottom-left' ),
                    'container'         => 'section',
                    'is_collapsed'      => false,
                ),
                'repeatable'        => true, // this makes the section repeatable
                'sortable'          => true,
            )
        );

        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID
            array(
                'field_id'         => 'name',
                'type'             => 'section_title',
                'title' => __( 'Name', 'admin-page-framework-loader' ),
                'attributes'       => array(
                    'placeholder'   => __( 'Enter a name', 'admin-page-framework-loader' ),
                ),
            ),
            array(
                'field_id'         => 'status',
                'type'             => 'radio',
                'title'            => __( 'Status', 'admin-page-framework-loader' ),
                'placement'        => 'section_title',
                'label'            => array(
                    1   => __( 'On', 'admin-page-framework-loader' ),
                    0   => __( 'Off', 'admin-page-framework-loader' ),
                ),
                'label_min_width'  => '40px',
                'default' => 1,
            ),
            array(
                'field_id'         => 'text',
                'type'             => 'textarea',
                'title'            => __( 'Content', 'admin-page-framework-loader' ),
                'rich'             => true,
                'repeatable'       => true,
                'sortable'         => true,
            ),
            array(
                'field_id'         => 'field_title',
                'content'          => array(
                    array(
                        'field_id'  => 'field_title_checkbox',
                        'type'      => 'checkbox',
                        'placement' => 'field_title',
                        'label'     => '<strong>' . __( 'Field Title Fields', 'admin-page-framework-loader' ) . '</strong>',
                    ),
                    array(
                        'field_id'      => 'field_title_textarea',
                        'type'          => 'textarea',
                        'before_input'  => __( 'Memo', 'admin-page-framework-loader' ),
                        'placement'     => 'field_title',
                        'attributes'    => array(
                            'cols' => '',
                        ),
                    ),
                    array(
                        'field_id'   => 'text',
                        'type'       => 'text',
                        'title'      => __( 'Normal Nested Field', 'admin-page-framework-loader' ),
                        'repeatable' => true,
                        'sortable'   => true,
                    ),
                ),
            ),
            array()
        );

    }

}
