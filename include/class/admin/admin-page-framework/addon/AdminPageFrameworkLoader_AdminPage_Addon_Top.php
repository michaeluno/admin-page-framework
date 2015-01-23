<?php
/**
 * Admin Page Framework Loader
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the Contact page to the demo plugin.
 * 
 * @since       3.5.0       Moved from the demo.
 * @filter      apply       admin_page_framework_loader_filter_admin_add_ons        Receives an array holding add-on information to list.
 */
class AdminPageFrameworkLoader_AdminPage_Addon_Top {
    
    /**
     * Stores the RSS url to fetch an add-on list.
     * @since       3.5.0
     */
    private $sRSSURL = 'http://feeds.feedburner.com/MiunosoftTagsAdd-ons';  
    
    /**
     * Set up properties.
     */
    public function __construct( $oFactory, $sPageSlug, $sTabSlug ) {
    
        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug; 
        $this->sTabSlug     = $sTabSlug;
        $this->sSectionID   = $this->sTabSlug;
        
        $this->_addTab();
        
        // Enable this to renew caches of the feed.
        // add_filter( 'wp_feed_cache_transient_lifetime', '__return_zero' );
        
    }
    
    private function _addTab() {
        
        $this->oFactory->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Add Ons', 'admin-page-framework-loader' ),
            )
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );
    
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        add_action( "do_{$this->sPageSlug}_{$this->sTabSlug}", array( $this, 'replyToDoTab' ) );        
        
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
        $_aFeedItems = apply_filters( AdminPageFrameworkLoader_Registry::HookSlug . '_filter_admin_add_ons', $this->_getDemo() + $_oFeedList->get() );
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
         */
        private function _getList( array $aFeedItem ) {

            // Local variables
            $_aOutput       = array();
            $_iMaxCols      = 3;
            $_aColumnInfo   = array (    // this will be modified as the items get rendered
                'bRowTagOpened'    => false,
                'bRowTagClosed'    => false,
                'iCurrRowPos'      => 0,
                'iCurrColPos'      => 0,
            );            
            $_aColumnOption = array (
                'sClassAttr'         => 'apfl_columns',
                'sClassAttrGroup'    => 'apfl_columns_box',
                'sClassAttrRow'      => 'apfl_columns_row',
                'sClassAttrCol'      => 'apfl_columns_col',
                'sClassAttrFirstCol' => 'apfl_columns_first_col',
            );                
                
            $_sSiteURL          = get_bloginfo( 'url' );
            $_sSiteURLWOQuery   = preg_replace( '/\?.*/', '', $_sSiteURL );
            
            foreach( $aFeedItem as $_sTitle => $_aItem ) {
                
                if ( ! is_array( $_aItem ) ) {
                    continue;
                }
                if ( ! isset( $_aItem['title'] ) ) { 
                    continue; 
                }

                // Increment the position
                $_aColumnInfo['iCurrColPos']++;
                
                $_aItem = $_aItem + array(
                    'label'         => __( 'Get it Now', 'admin-page-framework-loader' ),
                    'content'       => null,
                    'description'   => null,
                    'title'         => null,
                    'date'          => null,
                    'author'        => null,
                    'link'          => null,
                );

                $_sLinkURLWOQuery   = preg_replace( '/\?.*/', '', $_aItem['link'] );
                $_sTarget           = false === strpos( $_sLinkURLWOQuery , $_sSiteURLWOQuery )
                    ? '_blank'
                    : '';
                
                // Enclose the item buffer into the item container
                $_sItem = '<div class="' . $_aColumnOption['sClassAttrCol'] 
                    . ' apfl_col_element_of_' . $_iMaxCols . ' '
                    . ' apfl_extension '
                    . ( ( 1 == $_aColumnInfo['iCurrColPos'] ) ?  $_aColumnOption['sClassAttrFirstCol']  : '' )
                    . '"'
                    . '>' 
                        . '<div class="apfl_addon_item">' 
                            . "<h4 class='apfl_feed_item_title'>{$_aItem['title']}</h4>"
                            . "<div class='apfl_feed_item_description'>"
                                . $_aItem['description'] 
                            . "</div>"
                            . "<div class='get-now apfl_feed_item_link_button'>"
                                . "<a href='{$_aItem['link']}' target='{$_sTarget}' rel='nofollow' class='button button-secondary'>" 
                                    . $_aItem['label']
                                . "</a>"
                            . "</div>"
                        . '</div>'
                    . '</div>';    
                    
                // If it's the first item in the row, add the class attribute. 
                // Be aware that at this point, the tag will be unclosed. Therefore, it must be closed later at some point. 
                if ( 1 == $_aColumnInfo['iCurrColPos'] ) {
                    $_aColumnInfo['bRowTagOpened'] = true;
                    $_sItem = '<div class="' . $_aColumnOption['sClassAttrRow']  . '">' 
                        . $_sItem;
                }
            
                // If the current column position reached the set max column, increment the current position of row
                if ( 0 === ( $_aColumnInfo['iCurrColPos'] % $_iMaxCols ) ) {
                    $_aColumnInfo['iCurrRowPos']++;          // increment the row number
                    $_aColumnInfo['iCurrColPos'] = 0;        // reset the current column position
                    $_sItem .= '</div>';  // close the section(row) div tag
                    $_aColumnInfo['bRowTagClosed'] = true;
                }        
                
                $_aOutput[] = $_sItem;
            
            }
            
            // if the section(row) tag is not closed, close it
            if ( $_aColumnInfo['bRowTagOpened'] && ! $_aColumnInfo['bRowTagClosed'] ) { 
                $_aOutput[] .= '</div>';    
            }
            $_aColumnInfo['bRowTagClosed'] = true;
            
            // enclose the output in the group tag
            return '<div class="apfl_addon_list_container">' 
                    . '<div class="' . $_aColumnOption['sClassAttr'] . ' ' . $_aColumnOption['sClassAttrGroup'] . '">'
                        . implode( '', $_aOutput )
                    . '</div>'
                . '</div>';
                        
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
                        ? __( 'Deactivate', 'admin-page-framework-loader' )
                        : __( 'Activate', 'admin-page-framework-loader' ),
                )
            );
                        
        }
    
}
