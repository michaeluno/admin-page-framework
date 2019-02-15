<?php
/**
 * Admin Page Framework - Loader
 *
 * Loads Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 */

/**
 * Demonstrates the usage of Admin Page Framework.
 */
class APF_BasicUsage extends AdminPageFramework {

    /**
     * Sets up pages.
     */
    public function setUp() {

        $this->setRootMenuPage(
            "<span id='apf-demo-2-menu-label'>"
                . __( 'Basic Usage', 'admin-page-framework-loader' )
            . "</span>"
            ,
            version_compare( $GLOBALS[ 'wp_version' ], '3.8', '>=' )
                ? 'dashicons-format-audio'
                : null // dash-icons are supported since WordPress v3.8
        );

        $this->addSubMenuItems(
            array(
                'title'         => __( 'First Page', 'admin-page-framework-loader' ),
                'page_slug'     => 'apf_first_page',
            ),
            array(
                'title'         => __( 'Second Page', 'admin-page-framework-loader' ),
                'page_slug'     => 'apf_second_page',
                'style'         => AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/code.css',
            ),
            array(
                'title'         => __( 'Disabled', 'admin-page-framework-loader' ),
                'page_slug'     => 'apf_disabled',
                'disabled'      => true,
                'attributes'    => array(
                    'title'     => __( 'When the disabled argument is true, this tab will be disabled and has no link.', 'admin-page-framework-loader' ),
                ),
                'show_in_menu'  => false,
            )
        );

        // Disable the plugin action link.
        $this->setPluginSettingsLinkLabel( '' );

    }

    /**
     * Called when one of the added pages starts loading.
     *
     * Alternatively use `load_{instantiated class name}` hook.
     * @return      void
     */
    public function load() {

        // Disable the page heading tabs by passing false.
        $this->setPageHeadingTabsVisibility( true );

    }

    /**
     * Do page specific settings.
     *
     * @callback        action      load_{page slug}
     */
    public function load_apf_first_page() {

        $this->addSettingSections(
            array(
                'section_id'    => 'basic_usage',
                'title'         => __( 'Setting Form', 'admin-page-framework-loader' )
            )
        );

        $this->addSettingFields(
            'basic_usage',  // target section ID
            array(
                'field_id'  => 'text',
                'type'      => 'text',
                'title'     => __( 'Text', 'admin-page-framework-loader' ),
                'default'   => 'xyz',
            ),
            array(
                'field_id'  => '__submit',
                'type'      => 'submit',
                'save'      => false
            )
        );

    }

    /**
     * Do render the page contents.
     *
     * @callback        action      do_ + {page slug}
     */
    public function do_apf_first_page() {
        ?>
            <h3><?php _e( 'do_ + {...} Action Hooks', 'admin-page-framework-loader' ); ?></h3>
            <p><?php _e( 'Hi there! This text message is inserted by the <code>do_{page slug}</code> action hook and the callback method.', 'admin-page-framework-loader' ); ?></p>
        <?php

    }

    /**
     * Filter the page contents.
     *
     * @callback        filter      content_ + {page slug}
     */
    public function content_apf_second_page( $sContent ) {
        return $sContent
            . "<h3>" . __( 'content_ + {...} Filter Hooks', 'admin-page-framework-loader' ) . "</h3>"
            . "<p>"
                . __( 'This message is inserted with the <code>content_{page slug}</code> filter.', 'admin-page-framework-loader' )
            . "</p>"
            . "<h3>" . __( 'Saved Options', 'admin-page-framework-loader' ) . "</h3>"
            . $this->oDebug->get( $this->getValue() );
    }

}

new APF_BasicUsage(
    null,                           // the option key - when null is passed the class name in this case 'APF_BasicUsage' will be used
    AdminPageFrameworkLoader_Registry::$sFilePath, // the caller script path.
    'manage_options',               // the default capability
    'admin-page-framework-loader'   // the text domain
);
