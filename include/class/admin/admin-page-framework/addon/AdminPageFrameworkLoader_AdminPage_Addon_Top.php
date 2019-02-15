<?php
/**
 * Admin Page Framework Loader
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds the Addon page to the demo plugin.
 *
 * @since       3.5.0       Moved from the demo.
 * @filter      apply       admin_page_framework_loader_filter_admin_add_ons        Receives an array holding add-on information to list.
 */
class AdminPageFrameworkLoader_AdminPage_Addon_Top extends AdminPageFrameworkLoader_AdminPage_Tab_Base {

    /**
     * Stores the RSS url to fetch an add-on list.
     * @since       3.5.0
     */
    private $sRSSURL = 'http://feeds.feedburner.com/MiunosoftTagsAdd-ons';


    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {

        // Enable this to renew caches of the feed.
        // add_filter( 'wp_feed_cache_transient_lifetime', '__return_zero' );

        // Styles
        $this->oFactory->enqueueStyle( AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/column.css' );
        $this->oFactory->enqueueStyle( AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/feed-list.css' );

        // $this->oFactory->setPageTitleVisibility( false );
        $this->oFactory->setInPageTabsVisibility( false );

    }

    /**
     * Called when the tab is being rendered.
     */
    public function replyToDoTab() {

        $_oFeedList  = new AdminPageFrameworkLoader_FeedList( $this->sRSSURL );
        $_aFeedItems = apply_filters( AdminPageFrameworkLoader_Registry::HOOK_SLUG . '_filter_admin_add_ons', $this->_getDemo() + $_oFeedList->get() );
        if ( empty( $_aFeedItems ) ) {
            echo "<p>" . __( 'No add-on could be found.', 'admin-page-framework-loader' ) . "</p>";
            return;
        }

        echo $this->_getList( $_aFeedItems );

    }

        /**
         * Generates an output of a list of boxes from the given array.
         *
         * @since       3.5.0
         * @since       3.5.1       Removed the part that fetches a RSS feed.
         * @return      string
         */
        private function _getList( array $aFeedItems ) {

            $_aOutput       = array();
            $_aColumnInfo   = array (    // this will be modified as the items get rendered
                'bRowTagOpened'      => false,
                'bRowTagClosed'      => false,
                'iCurrRowPos'        => 0,
                'iCurrColPos'        => 0,
            );
            $_aColumnOption = array (
                'iMaxCols'           => 3,
                'sClassAttr'         => 'apfl_columns',
                'sClassAttrGroup'    => 'apfl_columns_box',
                'sClassAttrRow'      => 'apfl_columns_row',
                'sClassAttrCol'      => 'apfl_columns_col',
                'sClassAttrFirstCol' => 'apfl_columns_first_col',
            );

            $_sSiteURLWOQuery   = preg_replace( '/\?.*/', '', get_bloginfo( 'url' ) );
            foreach( $aFeedItems as $_aItem ) {
                $_aOutput[] = $this->_getFeedListItem(
                    ( array ) $_aItem,
                    $_aColumnInfo,
                    $_aColumnOption,
                    $_sSiteURLWOQuery
                );
            }

            // If the section (row) tag is not closed, close it.
            if ( $_aColumnInfo[ 'bRowTagOpened' ] && ! $_aColumnInfo[ 'bRowTagClosed' ] ) {
                $_aOutput[] = '</div>';
            }
            $_aColumnInfo[ 'bRowTagClosed' ] = true;

            // Enclose the output in the group tag
            return '<div class="apfl_addon_list_container">'
                    . '<div class="' . $_aColumnOption[ 'sClassAttr' ] . ' ' . $_aColumnOption[ 'sClassAttrGroup' ] . '">'
                        . implode( '', $_aOutput )
                    . '</div>'
                . '</div>';

        }
            /**
             * Returns an HTML output from the given feed item array.
             * @return      string
             */
            private function _getFeedListItem( array $aItem, array &$aColumnInfo, array $aColumnOption, $sSiteURLWOQuery='' ) {

                // Initial checks
                if ( ! isset( $aItem[ 'title' ] ) ) {
                    return '';
                }

                // Format
                $aItem = $aItem + array(
                    'label'         => __( 'Get it Now', 'admin-page-framework-loader' ),
                    'content'       => null,
                    'description'   => null,
                    'title'         => null,
                    'date'          => null,
                    'author'        => null,
                    'link'          => null,
                );

                // Increment the position
                $aColumnInfo[ 'iCurrColPos' ]++;

                // Making the target '_blank' causes the Feedburner redirect to fail so set no target.
                // $_sLinkURLWOQuery   = preg_replace( '/\?.*/', '', $aItem['link'] );
                // $_sTarget           = false === strpos( $_sLinkURLWOQuery , $sSiteURLWOQuery )
                    // ? '_blank'
                    // : '';
                $_sTarget = '';

                // Enclose the item buffer into the item container
                $_sItem = '<div class="' . $aColumnOption['sClassAttrCol']
                    . ' apfl_col_element_of_' . $aColumnOption['iMaxCols'] . ' '
                    . ' apfl_extension '
                    . ( ( 1 == $aColumnInfo['iCurrColPos'] ) ?  $aColumnOption['sClassAttrFirstCol']  : '' )
                    . '"'
                    . '>'
                        . '<div class="apfl_addon_item">'
                            . "<h4 class='apfl_feed_item_title'>{$aItem['title']}</h4>"
                            . "<div class='apfl_feed_item_description'>"
                                . $aItem['description']
                            . "</div>"
                            . "<div class='get-now apfl_feed_item_link_button'>"
                                . "<a href='" . esc_url( $aItem[ 'link' ] ) . "' target='{$_sTarget}' rel='nofollow' class='button button-secondary'>"
                                    . $aItem['label']
                                . "</a>"
                            . "</div>"
                        . '</div>'
                    . '</div>';

                // If it's the first item in the row, add the class attribute.
                // Be aware that at this point, the tag will be unclosed. Therefore, it must be closed later at some point.
                if ( 1 == $aColumnInfo['iCurrColPos'] ) {
                    $aColumnInfo['bRowTagOpened'] = true;
                    $_sItem = '<div class="' . $aColumnOption['sClassAttrRow']  . '">'
                        . $_sItem;
                }

                // If the current column position reached the set max column, increment the current position of row
                if ( 0 === ( $aColumnInfo['iCurrColPos'] % $aColumnOption['iMaxCols'] ) ) {
                    $aColumnInfo['iCurrRowPos']++;          // increment the row number
                    $aColumnInfo['iCurrColPos'] = 0;        // reset the current column position
                    $_sItem .= '</div>';  // close the section(row) div tag
                    $aColumnInfo['bRowTagClosed'] = true;
                }
                return $_sItem;

            }

        /**
         *
         * @return      array       The demo content.
         */
        private function _getDemo() {

            $_oOption = AdminPageFrameworkLoader_Option::getInstance( AdminPageFrameworkLoader_Registry::$aOptionKeys['main'] );
            $_bEnabled = $_oOption->get( 'enable_demo' );

            $_sTitle = __( 'Demo', 'admin=page-framework-loader' );
            return array(
                $_sTitle => array(
                    'title'         => $_sTitle,
                    'description'   => '<div style="text-align: center;" class="aligncenter"><img class="aligncenter" src="' . AdminPageFrameworkLoader_Registry::getPluginURL( '/asset/image/icon-128x128.png' ) . '" alt="' . esc_attr( $_sTitle ) . '"/></div>'
                        . '<p>'
                            . __( 'Showcases the features of Admin Page Framework.', 'admin-page-framework-loader' )
                        . '</p>',
                    'link'          => add_query_arg(
                        array(
                            'enable_apfl_demo_pages' => $_bEnabled
                                ? 0
                                : 1,
                        ) + $_GET,
                        admin_url( $GLOBALS['pagenow'] )
                    ),
                    'label'         => $_bEnabled
                        ? "<span id='button-deactivate-demo' class='deactivate'>"
                                . __( 'Deactivate', 'admin-page-framework-loader' )
                            . "</span>"
                        : "<span id='button-activate-demo' class='activate'>"
                                . __( 'Activate', 'admin-page-framework-loader' )
                            . "</span>"
                )
            );

        }

}
