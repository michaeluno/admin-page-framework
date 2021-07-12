<?php
/**
 * Admin Page Framework Loader
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a tab of the test page to the loader plugin.
 *
 * @since       3.8.31
 */
class APF_Demo_Test_Resources {

    public $oFactory;

    public $sClassName;

    public $sPageSlug;

    public $sTabSlug = 'resource';

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
                'title'         => 'Resources',
                'style'         => dirname( __FILE__ ) . '/asset/css/test-style.css',
                'script'        => dirname( __FILE__ ) . '/asset/js/test-js.js',
            )
        );

        // load + page slug + tab slug
         add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );
         add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );

        $this->oFactory->enqueueScript( dirname( __FILE__ ) . '/asset/js/test-js2.js' );
        $this->oFactory->enqueueStyle( dirname( __FILE__ ) . '/asset/css/test-style2.css' );

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
        ?>
        <div >
            <p class="test-style1">This text should be red.</p>
            <p class="test-style2">This text should be blue.</p>
            <p class="test-script1">This text should be changed.</p>
            <p class="test-script2">This text should be changed.</p>
        </div>
        <?php
    }

}