<?php
/**
 * Admin Page Framework Loader
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a tab of the test page to the loader plugin.
 *
 * @since       3.8.23
 */
class APF_Demo_Test_Transients {

    public $oFactory;

    public $sClassName;

    public $sPageSlug;

    public $sTabSlug = 'transient';

    public $sSectionID;

    public function __construct( $oFactory, $sPageSlug ) {

        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug;
        $this->sSectionID   = $this->sTabSlug;

        $this->oFactory->addInPageTabs(
            $this->sPageSlug,
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => 'Transients',
            )
        );

        // load + page slug + tab slug
         add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );
         add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );

    }

    /**
     * Triggered when the tab starts loading.
     *
     * @callback        add_action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oAdminPage ) {}

    /**
     * @param AdminPageFramework $oAdminPage
     */
    public function replyToDoTab( $oAdminPage ) {

        $oAdminPage->oUtil->setTransient( 'apfl_test_transient', 'foo bar', 5 );
        echo "<h5>getTransient() - should yield foo bar</h5>";
        var_dump( $oAdminPage->oUtil->getTransient( 'apfl_test_transient' ) );

        echo "<h5>getTransient() - should be empty</h5>";
        $oAdminPage->oUtil->deleteTransient( 'apfl_test_transient' );
        var_dump( $oAdminPage->oUtil->getTransient( 'apfl_test_transient' ) );

        echo "<h5>getTransientWithoutCache() - should yield 'TESTING'</h5>";
        $oAdminPage->oUtil->setTransient( 'apfl_test_transient2', 'TESTING', 5 );
        var_dump( $oAdminPage->oUtil->getTransientWithoutCache( 'apfl_test_transient2' ) );

        echo "<h5>cleanTransients() - should be null</h5>";
        $oAdminPage->oUtil->cleanTransients( 'apfl_test' );
        var_dump( $oAdminPage->oUtil->getTransientWithoutCache( 'apfl_test_transient2' ) );

    }

}