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
class APF_Demo_AdvancedUsage_Section_Tabbed_C {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_advanced_usage';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'sections';

    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'tabbed_sections_c';

    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {

        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'        => $this->sSectionID,
                'tab_slug'          => $this->sTabSlug,
                'section_tab_slug'  => 'tabbed_sections',
                'title'             => __( 'Custom Content', 'admin-page-framework-loader' ),
                'description'       => __( 'This is the third item of the tabbed section.', 'admin-page-framework-loader' ),
                'content'           => "<p>"
                        . __( 'This custom output is inserted with the <code>content</code> argument.', 'admin-page-framework-loader' )
                    . "</p>",
            )
        );

    }

}
