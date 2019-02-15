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
class APF_Demo_AdvancedUsage_Argument_DebugInfo {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_advanced_usage';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'argument';

    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'debug_info';

    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {

        // Section
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_id'        => $this->sSectionID,
                'title'             => __( 'Unsaving Items', 'admin-page-framework-loader' ),
                'show_debug_info'   => false,
                'description'       => array(
                    __( 'With the <code>show_debug_info</code> argument, you can control whether a tool-tip showing section/field arguments or not.', 'admin-page-framework-loader' ),
                    __( 'Notice that this section does not have the tool-tip.', 'admin-page-framework-loader' ),
                )
            )
        );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID
            array(
                'field_id'          => 'show_debug_info',
                'title'             => __( 'Show Debug Info', 'admin-page-framework-loader' ),
                'content'           => '',
                'description'       => __( 'Notice that this field has the the tool-tip showing the field arguments.', 'admin-page-framework-loader' ),
                'show_debug_info'   => true,
            ),
            array(
                'field_id'          => 'do_not_show_debug_info',
                'title'             => __( 'No Debug Info', 'admin-page-framework-loader' ),
                'content'           => '',
                'show_debug_info'   => false,
                'description'       => __( 'Notice that this field doe not have the the tool-tip showing the field arguments.', 'admin-page-framework-loader' ),
            )
        );

    }

}
