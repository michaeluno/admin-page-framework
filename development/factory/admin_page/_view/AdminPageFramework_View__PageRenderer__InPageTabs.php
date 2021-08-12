<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Enqueues page resources set with the `style` and `script` arguments.
 *
 * @abstract
 * @since           3.6.3
 * @package         AdminPageFramework/Factory/AdminPage/View
 * @internal
 * @extends         AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_View__PageRenderer__InPageTabs extends AdminPageFramework_FrameworkUtility {

    public $oFactory;
    public $sPageSlug;
    public $sTag = 'h3';

    /**
     * Sets up properties.
     * @since       3.6.3
     */
    public function __construct( $oFactory, $sPageSlug ) {

        $this->oFactory         = $oFactory;
        $this->sPageSlug        = $sPageSlug;
        $this->sTag             = $oFactory->oProp->sInPageTabTag
            ? $oFactory->oProp->sInPageTabTag
            : 'h3';

    }

    /**
     * Returns the output.
     * @since       3.6.3
     */
    public function get() {

        // If in-page tabs are not set, return an empty string.
        $_aInPageTabs = $this->getElement(
            $this->oFactory->oProp->aInPageTabs,
            $this->sPageSlug,
            array()
        );
        if ( empty( $_aInPageTabs ) ) {
            return '';
        }

        return $this->_getOutput( $_aInPageTabs, $this->sPageSlug, $this->sTag );

    }

        /**
         * Retrieves the output of in-page tab navigation bar as HTML.
         *
         * @since       2.0.0
         * @since       3.3.1        Moved from `AdminPageFramework_Page`.
         * @since       3.5.0        Deprecated the third $aOutput parameter.
         * @since       3.6.3        Moved from `AdminPageFramework_Page_View`. Changed the name from `_getInPageTabs()`.
         * @return      string       The output of in-page tabs.
         * @internal
         */
        private function _getOutput( $aInPageTabs, $sCurrentPageSlug, $sTag ) {

            $_aPage             = $this->oFactory->oProp->aPages[ $sCurrentPageSlug ];
            $_sCurrentTabSlug   = $this->_getCurrentTabSlug( $sCurrentPageSlug );
            $_sTag              = $this->_getInPageTabTag( $sTag, $_aPage );

            // If the in-page tabs' visibility is set to false, returns the title.
            if ( ! $_aPage[ 'show_in_page_tabs' ] ) {
                return $this->getElement( $aInPageTabs, array( $_sCurrentTabSlug, 'title' ) )
                    ? "<{$_sTag} class='admin-page-framework-in-page-tab-title'>"
                            . $aInPageTabs[ $_sCurrentTabSlug ][ 'title' ]
                        . "</{$_sTag}>"
                    : "";
            }

            return $this->_getInPageTabNavigationBar(
                $aInPageTabs,
                $_sCurrentTabSlug,
                $sCurrentPageSlug,
                $_sTag
            );

        }
            /**
             * Generates in-page tab navigation bar HTML output.
             *
             * @since       3.5.3
             * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
             * @internal
             * @return      string      the in-page tab navigation bar output.
             */
            private function _getInPageTabNavigationBar( array $aTabs, $sActiveTab, $sCurrentPageSlug, $sTag ) {

                $_oTabBar = new AdminPageFramework_TabNavigationBar(
                    $aTabs,      // tabs
                    $sActiveTab, // active tab slug
                    $sTag,       // container tag
                    array(       // container attributes
                        'class' => 'in-page-tab',
                    ),
                    array(       // callbacks
                        'format'    => array( $this, '_replyToFormatNavigationTabItem_InPageTab' ),

                        // Custom arguments to pass to the callback functions
                        'arguments' => array(
                            'page_slug'         => $sCurrentPageSlug,
                        ),
                    )
                );
                $_sTabBar = $_oTabBar->get();
                return $_sTabBar
                    ? "<div class='admin-page-framework-in-page-tab'>"
                            . $_sTabBar
                        . "</div>"
                    : '';

            }
                /**
                 * Formats navigation tab definition array of in-page tabs.
                 * @callback        function        AdminPageFramework_TabNavigationBar::_getFormattedTab
                 * @return          array
                 * @since           3.5.10
                 * @since           3.6.3       Moved from `AdminPageFramework_Page_View`.
                 */
                public function _replyToFormatNavigationTabItem_InPageTab( array $aTab, array $aStructure, array $aTabs, array $aArguments=array() ) {
                    $_oFormatter = new AdminPageFramework_Format_NavigationTab_InPageTab(
                        $aTab,
                        $aStructure,
                        $aTabs,
                        $aArguments,
                        $this->oFactory
                    );
                    return $_oFormatter->get();
                }

            /**
             * Returns the in-page tab tag.
             *
             * @since       3.5.3
             * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
             * @return      string      the in-page tab tag.
             * @internal
             */
            private function _getInPageTabTag( $sTag, array $aPage ) {
                return tag_escape(
                    $aPage[ 'in_page_tab_tag' ]
                        ? $aPage[ 'in_page_tab_tag' ]
                        : $sTag
                );
            }
            /**
             * Determines the currently loading in-page tab slug.
             * @since       3.5.3
             * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
             * @return      string      the currently loading in-page tab slug.
             * @internal
             */
            private function _getCurrentTabSlug( $sCurrentPageSlug ) {
                return $this->_getParentTabSlug(
                    $sCurrentPageSlug,
                    $this->getHTTPQueryGET( 'tab', $this->oFactory->oProp->getDefaultInPageTab( $sCurrentPageSlug ) )
                );
            }
                /**
                 * Retrieves the parent tab slug from the given tab slug.
                 *
                 * @since       2.0.0
                 * @since       2.1.2       If the parent slug has the show_in_page_tab to be true, it returns an empty string.
                 * @since       3.3.1       Moved from `AdminPageFramework_Page`.
                 * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
                 * @return      string      the parent tab slug.
                 * @internal
                 */
                private function _getParentTabSlug( $sPageSlug, $sTabSlug ) {

                    $_sParentTabSlug = $this->getElement(
                        $this->oFactory->oProp->aInPageTabs,
                        array( $sPageSlug, $sTabSlug, 'parent_tab_slug' ),
                        $sTabSlug
                    );
                    return isset( $this->oFactory->oProp->aInPageTabs[ $sPageSlug ][ $_sParentTabSlug ][ 'show_in_page_tab' ] )
                            && $this->oFactory->oProp->aInPageTabs[ $sPageSlug ][ $_sParentTabSlug ][ 'show_in_page_tab' ]
                        ? $_sParentTabSlug
                        : $sTabSlug;

                }

}
