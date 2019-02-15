<?php
/**
 * Admin Page Framework Loader
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds the Tool page to the loader plugin.
 *
 * @since       3.5.0
 * @since       3.5.3       Extends `AdminPageFrameworkLoader_AdminPage_Page_Base`.
 * @extends     AdminPageFrameworkLoader_AdminPage_Page_Base
 */
class AdminPageFrameworkLoader_AdminPage_Tool extends AdminPageFrameworkLoader_AdminPage_Page_Base {

    public function replyToLoadPage( $oFactory ) {

        // Tabs
        new AdminPageFrameworkLoader_AdminPage_Tool_Generator(
            $this->oFactory,
            $this->sPageSlug,
            array(
                'tab_slug'  => 'generator',
                'title'     => __( 'Generator', 'admin-page-framework-loader' ),
            )
        );

    }

}
