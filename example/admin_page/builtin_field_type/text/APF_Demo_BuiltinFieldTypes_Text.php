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

/**
 * Adds a tab in a page.
 *
 * @package     AdminPageFramework/Example
 */
class APF_Demo_BuiltinFieldTypes_Text {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'textfields';


    /**
     * Adds in-page tabs and sets up hooks.
     */
    public function __construct( $oFactory ) {

        /*
         * ( optional ) Add in-page tabs - In Admin Page Framework, there are two kinds of tabs: page-heading tabs and in-page tabs.
         * Page-heading tabs show the titles of sub-page items which belong to the set root page.
         * In-page tabs show tabs that you define to be embedded within an individual page.
         */
        $oFactory->addInPageTabs(
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Text', 'admin-page-framework-loader' ),
                'order'         => 1, // ( optional ) - if this is not set, an index will be assigned automatically in the added order
            )
        );

        add_action(
            'load_' . $this->sPageSlug . '_' . $this->sTabSlug,
            array( $this, 'replyToLoadTab' )
        );

        add_filter(
            'footer_left_' . $this->sPageSlug . '_' . $this->sTabSlug,
            array( $this, 'replyToModifyLeftFooterText' )
        );

        add_filter(
            'footer_right_' . $this->sPageSlug . '_' . $this->sTabSlug,
            array( $this, 'replyToModifyRightFooterText' )
        );

    }

        /**
         * Adds form sections.
         *
         * Triggered when the tab is loaded.
         *
         * @callback        action      load_{page slug}_{tab slug}
         */
        public function replyToLoadTab( $oFactory ) {

            // Form sections
            $_aClasses = array(
                'APF_Demo_BuiltinFieldTypes_Text_Text',
                'APF_Demo_BuiltinFieldTypes_Text_TextArea',
            );
            foreach ( $_aClasses as $_sClassName ) {
                if ( ! class_exists( $_sClassName ) ) {
                    continue;
                }
                new $_sClassName( $oFactory );
            }

        }

    /**
     * Modifies the left footer text.
     *
     * @callback    filter      footer_left_{page slug}_{tab slug}
     * @return      string
     */
    public function replyToModifyLeftFooterText( $sHTML ) {
        return "<span>" . sprintf(
                    __( 'Inserted with the <code>%1$s</code> filter.', 'admin-page-framework-loader' ),
                    'footer_left_{page slug}_{tab slug}'
                )
            . "</span><br />"
            . $sHTML;
    }
    /**
     * Modifies the right footer text.
     *
     * @callback    filter      footer_right_{page slug}_{tab slug}
     * @return      string
     */
    public function replyToModifyRightFooterText( $sHTML ) {
        return "<span>" . sprintf(
                    __( 'Inserted with the <code>%1$s</code> filter.', 'admin-page-framework-loader' ),
                    'footer_right_{page slug}_{tab slug}'
                )
            . "</span><br />"
            . $sHTML;
    }

}
