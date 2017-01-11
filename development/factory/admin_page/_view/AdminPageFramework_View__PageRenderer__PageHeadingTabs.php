<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
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
class AdminPageFramework_View__PageRenderer__PageHeadingTabs extends AdminPageFramework_FrameworkUtility {
        
    public $oFactory;
    public $sPageSlug;
    public $sTag = 'h2';

    /**
     * Sets up properties.
     * @since       3.6.3
     */
    public function __construct( $oFactory, $sPageSlug ) {
       
        $this->oFactory         = $oFactory;
        $this->sPageSlug        = $sPageSlug;
        $this->sTag             = $oFactory->oProp->sPageHeadingTabTag
            ? $oFactory->oProp->sPageHeadingTabTag
            : 'h2';
        
    }   
    
    /**
     * Returns the output.
     * @since       3.6.3
     */
    public function get() {
        
        $_aPage = $this->oFactory->oProp->aPages[ $this->sPageSlug ];
        
        // If the page title is disabled, return an empty string.
        if ( ! $_aPage[ 'show_page_title' ] ) { 
            return ""; 
        }

        return $this->_getOutput( $_aPage, $this->sTag );
        
    }
    
        /**
         * Retrieves the output of page heading tab navigation bar as HTML.
         * 
         * @since       2.0.0
         * @since       3.3.1       Moved from `AdminPageFramework_Page`.
         * @since       3.5.3       Deprecated the `$aOutput` parameter.
         * @since       3.6.3       Moved from `AdminPageFramework_Page_View`. Changed the name from `_getPageHeadingTabs()`.
         * @return      string      the output of page heading tabs.
         */
        private function _getOutput( $aPage, $sTag ) {
   
            $sTag  = $this->_getPageHeadingTabTag( $sTag, $aPage );
            
            // If the page heading tab visibility is disabled, or only one page is registered, return the title.
            if ( ! $aPage[ 'show_page_heading_tabs' ] || count( $this->oFactory->oProp->aPages ) == 1 ) {
                return "<{$sTag}>" 
                        . $aPage[ 'title' ]  
                    . "</{$sTag}>";     
            }

            return $this->_getPageHeadingtabNavigationBar( 
                $this->oFactory->oProp->aPages, 
                $sTag,
                $aPage[ 'page_slug' ] 
            );        
            
        }
            /**
             * Returns the HTML page heading tab tag.
             * @since       3.5.3
             * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
             * @internal
             * @return      string      the HTML page heading tab tag.
             */
            private function _getPageHeadingTabTag( $sTag, array $aPage ) {
                return tag_escape( 
                    $aPage[ 'page_heading_tab_tag' ]
                        ? $aPage[ 'page_heading_tab_tag' ]
                        : $sTag
                );
            }            
            /**
             * Returns the HTML page heading tab navigation bar output.
             * 
             * @internal
             * @since       3.5.3
             * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
             * @return      string      the HTML page heading tab navigation bar output.
             */
            private function _getPageHeadingtabNavigationBar( array $aPages, $sTag, $sCurrentPageSlug ) {
                
                $_oTabBar = new AdminPageFramework_TabNavigationBar(
                    $aPages,     // tab items
                    $sCurrentPageSlug, // active tab slug
                    $sTag,       // container tag
                    array(       // container attributes
                        // 'class' => '...',
                    ),
                    array(       // callbacks
                        'format'    => array( $this, '_replyToFormatNavigationTabItem_PageHeadingTab' ),
                    )
                );            
                $_sTabBar = $_oTabBar->get();
                return $_sTabBar
                    ? "<div class='admin-page-framework-page-heading-tab'>"
                            . $_sTabBar
                        . "</div>"
                    : '';                
            }
                /**
                 * Formats navigation tab array of page-heading tabs.
                 * 
                 * @callback        function        AdminPageFramework_TabNavigationBar::_getFormattedTab
                 * @return          array
                 * @since           3.5.10
                 * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
                 */            
                public function _replyToFormatNavigationTabItem_PageHeadingTab( $aSubPage, $aStructure, $aPages, $aArguments=array() ) {                    
                    switch( $aSubPage['type'] ) {
                        case 'link':
                            return $this->_getFormattedPageHeadingtabNavigationBarLinkItem( $aSubPage, $aStructure );
                        default:
                            return $this->_getFormattedPageHeadingtabNavigationBarPageItem( $aSubPage, $aStructure );
                    }                    
                    return $aSubPage + $aStructure;
                }
                    /**
                     * Returns the HTML output of a navigation bar item of a sub-page.
                     * @since       3.5.3
                     * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
                     * @internal
                     * @return      string      the HTML output of a navigation bar item of a sub-page.
                     */
                    private function _getFormattedPageHeadingtabNavigationBarPageItem( array $aSubPage, $aStructure ) {
                        
                        if ( ! isset( $aSubPage[ 'page_slug' ] ) ) {
                            return array();
                        }
                        if ( ! $aSubPage[ 'show_page_heading_tab' ] ) {
                            return array();
                        }
                        return array(
                            'slug'  => $aSubPage[ 'page_slug' ],
                            'title' => $aSubPage[ 'title' ],
                            'href'  => esc_url( 
                                $this->getQueryAdminURL( 
                                    array( 
                                        'page'  => $aSubPage[ 'page_slug' ], 
                                        'tab'   => false, 
                                    ), 
                                    $this->oFactory->oProp->aDisallowedQueryKeys 
                                ) 
                            ),
                        ) 
                        + $aSubPage
                        + array( 'class' => null )
                        + $aStructure;
                        
                    }
                    /**
                     * Returns a formatted tab array for a navigation bar item of a link for page heading tabs.
                     * @since       3.5.10
                     * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
                     * @since       
                     * @internal
                     * @return      array      the HTML output of a navigation bar item of a link.
                     */                    
                    private function _getFormattedPageHeadingtabNavigationBarLinkItem( array $aSubPage, $aStructure ) {
                        
                        if ( ! isset( $aSubPage[ 'href' ] ) ) {
                            return array();
                        }
                        if ( ! $aSubPage[ 'show_page_heading_tab' ] ) {
                            return array();
                        }                        
                        $aSubPage = array(
                            'slug'  => $aSubPage[ 'href' ],
                            'title' => $aSubPage[ 'title' ],
                            'href'  => esc_url( $aSubPage[ 'href' ] ),
                        ) 
                            + $aSubPage
                            + array( 'class' => null )
                            + $aStructure;
                            
                        $aSubPage[ 'class' ] = trim( $aSubPage[ 'class' ] . ' link' );
                        return $aSubPage;
                    }                      
                
}
