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
abstract class AdminPageFrameworkLoader_AdminPage_Tab_Base extends AdminPageFrameworkLoader_AdminPage_RootBase {

    /**
     * Stores the caller object.
     */
    public $oFactory;

    /**
     * Stores the associated page slug.
     */
    public $sPageSlug;

    /**
     * Stores the associated tab slug.
     */
    public $sTabSlug;

    /**
     * Stores callback method names.
     */
    protected $aMethods = array(
        'replyToLoadTab',
        'replyToDoTab',
        'replyToDoAfterTab',
        'validate',
    );

    /**
     * Sets up hooks and properties.
     */
    public function __construct( $oFactory, $sPageSlug, array $aTabDefinition ) {

        $this->oFactory     = $oFactory;
        $this->sPageSlug    = $sPageSlug;
        $this->sTabSlug     = isset( $aTabDefinition[ 'tab_slug' ] )
            ? $aTabDefinition[ 'tab_slug' ]
            : '';

        if ( ! $this->sTabSlug ) {
            return;
        }

        $this->_addTab( $this->sPageSlug, $aTabDefinition );
        $this->construct( $oFactory );

    }

    private function _addTab( $sPageSlug, $aTabDefinition ) {

        $this->oFactory->addInPageTabs(
            $sPageSlug,
            $aTabDefinition + array(
                'tab_slug'          => null,
                'title'             => null,
                'parent_tab_slug'   => null,
                'show_in_page_tab'  => null,
            )
        );

        if ( $aTabDefinition[ 'tab_slug' ] ) {
            add_action(
                "load_{$sPageSlug}_{$aTabDefinition['tab_slug']}",
                array( $this, 'replyToLoadTab' )
            );
            add_action(
                "do_{$this->sPageSlug}_{$this->sTabSlug}",
                array( $this, 'replyToDoTab' )
            );
            add_action(
                "do_after_{$this->sPageSlug}_{$this->sTabSlug}",
                array( $this, 'replyToDoAfterTab' )
            );
            add_filter(
                "validation_{$this->sPageSlug}_{$this->sTabSlug}",
                array( $this, 'validate' ),
                10,
                4
            );
        }

    }

    /**
     * Called when the in-page tab loads.
     *
     * @remark      This method should be overridden in each extended class.
     */
    // public function replyToLoadTab( $oFactory ) {}
    // public function replyToDoTab( $oFactory ) {}
    // public function replyToDoAfterTab( $oFactory ) {}


    public function validate( $aInput, $aOldInput, $oFactory, $aSubmitInfo ) {
        return $aInput;
    }

}
