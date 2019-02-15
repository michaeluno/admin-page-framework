<?php
/**
 * Admin Page Framework Loader
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds the Contact page to the loader plugin.
 *
 * @since       3.5.0
 * @since       3.5.3       Extends `AdminPageFrameworkLoader_AdminPage_Page_Base`.
 * @extends     AdminPageFrameworkLoader_AdminPage_Page_Base
 */
class AdminPageFrameworkLoader_AdminPage_Help extends AdminPageFrameworkLoader_AdminPage_Page_Base {

    /**
     * Gets triggered when the page loads.
     *
     * @callback        action      load_{page slug}
     */
    public function replyToLoadPage( $oFactory ) {

        // Tabs
        new AdminPageFrameworkLoader_AdminPage_Help_Information(
            $oFactory,    // factory object
            $this->sPageSlug,   // page slug
            array(  // tab definition
                'tab_slug'  => 'information',
                'title'     => __( 'Support', 'admin-page-framework-loader' ),
            )
        );
        new AdminPageFrameworkLoader_AdminPage_Help_Guide(
            $oFactory,
            $this->sPageSlug,
            array(
                'tab_slug'  => 'guide',
                'title'         => __( 'Getting Started', 'admin-page-framework-loader' ),
                'url'           => add_query_arg(
                    array(
                        'page'  => AdminPageFrameworkLoader_Registry::$aAdminPages['about'],
                        // 'tab'   => 'guide',
                    ),
                    admin_url( 'index.php' )   // Dashboard
                ) . '#section-getting_started__'
            )
        );
        new AdminPageFrameworkLoader_AdminPage_Help_FAQ(
            $oFactory,
            $this->sPageSlug,
            array(
                'tab_slug'  => 'faq',
                'title'     => __( 'FAQ', 'admin-page-framework-loader' ),
            )
        );
        new AdminPageFrameworkLoader_AdminPage_Help_Tip(
            $oFactory,
            $this->sPageSlug,
            array(
                'tab_slug'  => 'tips',
                'title'     => __( 'Tips', 'admin-page-framework-loader' ),
            )
        );
        new AdminPageFrameworkLoader_AdminPage_Help_Example(
            $oFactory,
            $this->sPageSlug,
            array(
                'tab_slug'  => 'examples',
                'title'     => __( 'Examples', 'admin-page-framework-loader' ),
            )
        );
        new AdminPageFrameworkLoader_AdminPage_Help_Report(
            $oFactory,
            $this->sPageSlug,
            array(
                'tab_slug'  => 'report',
                'title'     => __( 'Report', 'admin-page-framework-loader' ),
            )
        );
        new AdminPageFrameworkLoader_AdminPage_Help_About(
            $oFactory,
            $this->sPageSlug,
            array(
                'tab_slug'  => 'about',
                'title'     => __( 'About', 'admin-page-framework-loader' ),
                'url'       => add_query_arg(
                    array(
                        'page' => AdminPageFrameworkLoader_Registry::$aAdminPages['about']
                    ),
                    admin_url( 'index.php' )   // Dashboard
                )
            )
        );
        new AdminPageFrameworkLoader_AdminPage_Help_Debug(
            $oFactory,
            $this->sPageSlug,
            array(
                'tab_slug'  => 'debug',
                'title'     => __( 'Debug', 'admin-page-framework-loader' ),
                'if'        => defined( 'WP_DEBUG' ) && WP_DEBUG,
            )
        );

        // Page meta boxes
        new AdminPageFrameworkLoader_AdminPageMetaBox_ExternalLinks(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Resources', 'admin-page-framework-loader' ), // title
            array( // page slugs
                AdminPageFrameworkLoader_Registry::$aAdminPages[ 'help' ],
            ),
            'side',                                       // context
            'default'                                     // priority
        );

        $oFactory->enqueueStyle(
            AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/help.css',
            $this->sPageSlug
        );

    }

}
