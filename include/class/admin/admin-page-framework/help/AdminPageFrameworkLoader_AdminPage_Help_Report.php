<?php
/**
 * Admin Page Framework - Loader
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 */

/**
 * Adds the Contact page to the demo plugin.
 *
 * @since       3.4.2
 * @since       3.5.0       Moved from the demo example.
 */
class AdminPageFrameworkLoader_AdminPage_Help_Report extends AdminPageFrameworkLoader_AdminPage_Tab_Base {

    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {

        new AdminPageFrameworkLoader_AdminPage_Help_Report_Report(
            $oAdminPage,
            $this->sPageSlug,
            array(
                'section_id'    => $this->sTabSlug, // using the tab slug as only one section is added to this tab.
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Report Issues', 'admin-page-framework-loader' ),
                'description'   => __( 'If you find a bug, you can report it from here.', 'admin-page-framework-loader' ),
            )
        );

    }
}
