<?php
/**
 * Admin Page Framework - Demo
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

class APF_Demo_PageMetaBox {

    private $_sClassName = 'APF_Demo';

    /**
     * Stores the page slug.
     */
    private $_sPageSlug = 'apf_page_meta_boxes';

    public function __construct() {

        add_action(
            'set_up_' . $this->_sClassName,
            array( $this, 'replyToSetUp' )
        );

    }

    /**
     * Sets up pages.
     *
     * @callback        set_up_{instantiated class name}
     */
    public function replyToSetUp( $oFactory ) {

        // Add sub-menu items (pages or links)
        $oFactory->addSubMenuItems(
            array(
                'title'         => __( 'Page Meta Boxes', 'admin-page-framework-loader' ),
                'page_slug'     => $this->_sPageSlug,
                'order'         => 35,
            )
        );

        add_action(
            'load_' . $this->_sPageSlug,
            array( $this, 'replyToLoadPage' )
        );



    }

    /**
     * Called when the page starts loading.
     *
     * @callback        action      load_{page slug}
     * @return          void
     */
    public function replyToLoadPage( $oFactory ) {

        // Set up the page settings
        $oFactory->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $oFactory->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs

        // Enqueue styles
        $oFactory->enqueueStyle( AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/code.css' );

        // Tabs
        new APF_Demo_PageMetaBox__FirstTab( $oFactory, $this->_sPageSlug );
        new APF_Demo_PageMetaBox__SecondTab( $oFactory, $this->_sPageSlug );

        // Page meta boxes

        new APF_Demo_PageMetaBox__Normal(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Normal', 'admin-page-framework-loader' ), // title
            array(
                $this->_sPageSlug => array(
                    'first',
                ),
            ),
            'normal',                                       // context
            'default'                                       // priority
        );

        new APF_Demo_PageMetaBox__Advanced(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Advanced', 'admin-page-framework-loader' ), // title
            array(
                $this->_sPageSlug => array(
                    'first',
                ),
            ),
            'advanced',                                     // context
            'default'                                       // priority
        );

        new APF_Demo_PageMetaBox__Nested(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Nested Sections', 'admin-page-framework-loader' ), // title
            array(
                $this->_sPageSlug => array(
                    'second',
                ),
            ),
            'normal',                                     // context
            'default'                                       // priority
        );

        new APF_Demo_PageMetaBox__Side(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Side', 'admin-page-framework-loader' ), // title
            array(
                $this->_sPageSlug => array(
                    'first',
                    'second',
                ),
            ),
            'side',                                         // context
            'default'                                       // priority
        );

        new APF_Demo_PageMetaBox__WithFormSection(
            null,
            __( 'With a Form Section', 'admin-page-framework-loader' ), // title
            array(
                $this->_sPageSlug => array(
                    'first',
                    'second',
                ),
            ),
            'side',
            'low'
        );

        new APF_Demo_PageMetaBox__NoField(
            null,
            __( 'Information Box', 'admin-page-framework-loader' ), // title
            array(
                $this->_sPageSlug => array(
                    'first',
                    'second',
                ),
            ),
            'side',
            'low'
        );

        // Pointer Tool Tips
        new AdminPageFramework_PointerToolTip(
            array(
                $this->_sPageSlug,  // page slugs
            ),
            'apf_demo_page_meta_boxes', // unique id for the pointer tool box
            array(        // pointer data
                'target'    => '#apf_metabox_for_pages_normal',
                'options'   => array(
                    'content' => sprintf( '<h3> %1$s </h3> <p> %2$s </p>',
                        __( 'Page Meta Boxes' ,'admin-page-framework-loader' ),
                        __( 'Demonstrates the use of meta boxes for admin pages.','admin-page-framework-loader')
                        . ' ' . __( 'Usually meta boxes are displayed in post editing pages but with Admin Page Framework, you can display them in generic admin pages you create with the framework.','admin-page-framework-loader')
                    ),
                    'position'  => array( 'edge' => 'top', 'align' => 'middle' )
                )
            )
        );

    }

}
