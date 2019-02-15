<?php
/**
 * Admin Page Framework - Loader
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Creates an in-page tab that demonstrates the usage of 'image', 'media', 'file' field types.
 *
 * @package     AdminPageFramework/Example
 */
class APF_Demo_BuiltinFieldTypes_File {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'files';

    /**
     * Sets up hooks.
     */
    public function __construct( $oFactory ) {

        // Tab
        $oFactory->addInPageTabs(
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'  => $this->sTabSlug,
                'title'     => __( 'Files', 'admin-page-framework-loader' ),
            )
        );

        add_action(
            'load_' . $this->sPageSlug . '_' . $this->sTabSlug,
            array( $this, 'replyToLoadTab' )
        );

    }

    /**
     * Triggered when the tab is loaded.
     *
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oFactory ) {

        $_aClasses = array(
            'APF_Demo_BuiltinFieldTypes_File_Image',
            'APF_Demo_BuiltinFieldTypes_File_Media',
            'APF_Demo_BuiltinFieldTypes_File_Upload',
        );
        foreach ( $_aClasses as $_sClassName ) {
            if ( ! class_exists( $_sClassName ) ) {
                continue;
            }
            new $_sClassName( $oFactory );
        }

    }

}
