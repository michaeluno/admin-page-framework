<?php
/**
 * Admin Page Framework Loader
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 */

/**
 * Provides an abstract base for adding pages.
 *
 * @since       3.5.3
 */
abstract class AdminPageFrameworkLoader_AdminPage_Page_Base extends AdminPageFrameworkLoader_AdminPage_RootBase {

    /**
     * Stores the factory object.
     */
    public $oFactory;

    /**
     * Stores the associated page slug with the adding section.
     */
    public $sPageSlug;

    /**
     * Sets up hooks and properties.
     */
    public function __construct( $oFactory, array $aPageArguments ) {

        $this->oFactory     = $oFactory;
        $this->sPageSlug    = $aPageArguments['page_slug'];
        $this->_addPage( $aPageArguments );
        $this->construct( $oFactory );

    }

    private function _addPage( array $aPageArguments ) {

        $this->oFactory->addSubMenuItems(
            $aPageArguments
            + array(
                'page_slug'     => null,
                'title'         => null,
                'screen_icon'   => null,
            )
        );
        add_action( "load_{$this->sPageSlug}", array( $this, 'replyToLoadResources' ) );
        add_action( "load_{$this->sPageSlug}", array( $this, 'replyToLoadPage' ) );
        add_action( "do_{$this->sPageSlug}", array( $this, 'replyToDoPage' ) );
        add_action( "do_after_{$this->sPageSlug}", array( $this, 'replyToDoAfterPage' ) );
        add_filter( "validation_{$this->sPageSlug}", array( $this, 'validate' ), 10, 4 );

    }

    /**
     * @callback        action      load_{page slug}
     */
    public function replyToLoadResources( $oFactory ) {

        $_sCSSPath = AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/' . $this->sPageSlug . '.css';
        if ( ! file_exists( $_sCSSPath ) ) {
            return;
        }
        $this->oFactory->enqueueStyle(
            $_sCSSPath,
            $this->sPageSlug
        );

    }

    /**
     * Called when the page loads.
     *
     * @remark      This method should be overridden in each extended class.
     */
    // public function replyToLoadPage( $oFactory ) {}
    // public function replyToDoPage( $oFactory ) {}
    // public function replyToDoAfterPage( $oFactory ) {}
    // public function validate( $aInput, $aOldInput, $oFactory, $aSubmitInfo ){}

}
