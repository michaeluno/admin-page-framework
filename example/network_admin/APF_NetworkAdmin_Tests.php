<?php
/**
 * Admin Page Framework - Demo
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed GPLv2
 *
 */

class APF_NetworkAdmin_Tests extends AdminPageFramework_NetworkAdmin {

    protected $sPageSlug = 'apf_tests';

    public function _isInstantiatable() {

        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return false;
        }
        return parent::_isInstantiatable();

    }

    /**
     * Sets up pages.
     */
    public function setUp() { // this method automatically gets triggered with the wp_loaded hook.

        /* ( optional ) this can be set via the constructor. For available values, see https://codex.wordpress.org/Roles_and_Capabilities */
        $this->setCapability( 'read' );

        /* ( required ) Set the root page */
        $this->setRootMenuPageBySlug( 'APF_NetworkAdmin' );

        /* ( required ) Add sub-menu items (pages or links) */
        $this->addSubMenuItems(
            array(
                'title'         => 'Tests',
                'page_slug'     => $this->sPageSlug,
                'order'         => 99999,
            )
        );

    }

    /**
     * The pre-defined callback method that is triggered when the page loads.
     *
     * @callback        action      load_{page slug}
     */
    public function load_apf_tests( $oAdminPage ) {

        new APF_Demo_Test_Transients( $this, $this->sPageSlug );

    }

}