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
 * Adds the 'Minifier' tab to the 'Tools' page of the loader plugin.
 *
 * @since       3.4.6
 * @since       3.5.0       Moved from the demo.
 * @since       3.5.3       Extends `AdminPageFrameworkLoader_AdminPage_Tab_Base`.
 * @extends     AdminPageFrameworkLoader_AdminPage_Tab_Base
 * @deprecated  3.5.4       As the component generator was introduced.
 */
class AdminPageFrameworkLoader_AdminPage_Tool_Minifier extends AdminPageFrameworkLoader_AdminPage_Tab_Base {

    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {

        new AdminPageFrameworkLoader_AdminPage_Tool_Minifier_Minifier(
            $oAdminPage,
            $this->sPageSlug,
            array(
                'section_id'    => $this->sTabSlug, // using the tab slug as the section id as only one section is in this tab.
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Download Minified Version', 'admin-page-framework-loader' ),
                'description'   => __( 'When you click the Download link below, the minified version of the framework will be generated.', 'admin-page-framework-loader' ),
            )
        );

    }

}
