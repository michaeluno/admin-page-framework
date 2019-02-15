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
 * Adds the 'Generator' tab to the 'Tools' page of the loader plugin.
 *
 * @since       3.5.4
 * @extends     AdminPageFrameworkLoader_AdminPage_Tab_Base
 */
class AdminPageFrameworkLoader_AdminPage_Tool_Generator extends AdminPageFrameworkLoader_AdminPage_Tab_Base {

    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {

        new  AdminPageFrameworkLoader_AdminPage_Tool_Generator_Generator(
            $oAdminPage,
            $this->sPageSlug,
            array(
                'section_id'    => $this->sTabSlug,       // avoid hyphen(dash), dots, and white spaces
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Download Framework', 'admin-page-framework-loader' ),
                'description'   => array(
                    __( 'Generate your own version of the framework to avoid library conflicts.', 'admin-page-framework-loader' ),
                ),
            )
        );

    }

}
