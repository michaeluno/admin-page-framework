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
 * Adds a tab in a page.
 *
 * @package     AdminPageFramework/Example
 */
class APF_Demo_AdvancedUsage_Section {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_advanced_usage';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'sections';

    /**
     * Sets up hooks.
     */
    public function __construct( $oFactory ) {

        // Tab
        $oFactory->addInPageTabs(
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'  => $this->sTabSlug,
                'title'     => __( 'Sections', 'admin-page-framework-loader' ),
            )
        );

        add_action(
            'load_' . $this->sPageSlug . '_' . $this->sTabSlug,
            array( $this, 'replyToLoadTab' )
        );

    }

    /**
     * Adds form sections.
     *
     * Triggered when the tab is loaded.
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oFactory ) {

        $_aClasses = array(
            'APF_Demo_AdvancedUsage_Section_SectionTitleField',
            'APF_Demo_AdvancedUsage_Section_CustomOutput',
            'APF_Demo_AdvancedUsage_Section_Repeatable',
            'APF_Demo_AdvancedUsage_Section_Tabbed_A',
            'APF_Demo_AdvancedUsage_Section_Tabbed_B',
            'APF_Demo_AdvancedUsage_Section_Tabbed_C',
            'APF_Demo_AdvancedUsage_Section_Repeatable_Tabbed',
            'APF_Demo_AdvancedUsage_Section_Collapsible_A',
            'APF_Demo_AdvancedUsage_Section_Collapsible_B',
            'APF_Demo_AdvancedUsage_Section_Collapsible_C',
            'APF_Demo_AdvancedUsage_Section_Collapsible_D',
            'APF_Demo_AdvancedUsage_Section_CollapsibleType_Button',
            'APF_Demo_AdvancedUsage_Section_Collapsible_Repeatable',
        );
        foreach ( $_aClasses as $_sClassName ) {
            if ( ! class_exists( $_sClassName ) ) {
                continue;
            }
            new $_sClassName( $oFactory );
        }

    }

}
