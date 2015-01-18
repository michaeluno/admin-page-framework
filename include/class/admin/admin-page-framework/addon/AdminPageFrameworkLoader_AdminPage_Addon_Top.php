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
 */
class AdminPageFrameworkLoader_AdminPage_Addon_Top {
    
    /**
     * Stores the RSS url to fetch an addon list.
     * @since       3.5.0
     */
    private $sRSSURL = '';  // e.g. 'http://feeds.feedburner.com/MiunosoftFetchTweetsExtension';

    public function __construct( $oFactory, $sPageSlug, $sTabSlug ) {
    
        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug; 
        $this->sTabSlug     = $sTabSlug;
        $this->sSectionID   = $this->sTabSlug;
        
        $this->_addTab();
    
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
        
    public function replyToDoTab() {
        
        echo $this->_getAddOnList();
        
    }
    
        private $_aColumnOption = array (
            'sClassAttr'         => 'apfl_columns',
            'sClassAttrGroup'    => 'apfl_columns_box',
            'sClassAttrRow'      => 'apfl_columns_row',
            'sClassAttrCol'      => 'apfl_columns_col',
            'sClassAttrFirstCol' => 'apfl_columns_first_col',
        );    
        private $_aColumnInfoDefault = array (    // this will be modified as the items get rendered
            'bRowTagClosed'    => false,
            'iCurrRowPos'      => 0,
            'iCurrColPos'      => 0,
        );        
    
        private function _getAddOnList() {

            $_oFeedList = new AdminPageFrameworkLoader_FeedLister;
            $_aFeedItems = $_oFeedList->get( $this->sRSSURL );
            if ( empty( $_aFeedItems ) ) {
                echo "<p>" . __( 'No add-on could be found.', 'admin-page-framework-loader' ) . "</p>";
                return;
            }
            
            $_aOutput   = array();
            $_iMaxCols  = 3;
            $this->_aColumnInfo = $this->_aColumnInfoDefault;
            foreach( $_aFeedItems as $_sTitle => $_aItem ) {
                
                if ( ! isset( $_aItem['title'] ) ) { continue; }
                
                // Increment the position
                $this->_aColumnInfo['iCurrColPos']++;
                
                // Enclose the item buffer into the item container
                $strItem = '<div class="' . $this->_aColumnOption['sClassAttrCol'] 
                    . ' apfl_col_element_of_' . $_iMaxCols . ' '
                    . ' apfl_extension '
                    . ( ( $this->_aColumnInfo['iCurrColPos'] == 1 ) ?  $this->_aColumnOption['sClassAttrFirstCol']  : '' )
                    . '"'
                    . '>' 
                        . '<div class="apfl_addon_item">' 
                            . "<h4 class='apfl_feed_item_title'>{$_aItem['title']}</h4>"
                            . $_aItem['description'] 
                            . "<div class='get-now apfl_feed_item_link_button'>"
                                . "<a href='{$_aItem['strLink']}' target='_blank' rel='nofollow'>" 
                                    . "<input class='button button-secondary' type='submit' value='" . __( 'Get it Now', 'admin-page-framework-loader' ) . "' />"
                                . "</a>"
                            . "</div>"
                        . '</div>'
                    . '</div>';    
                    
                // If it's the first item in the row, add the class attribute. 
                // Be aware that at this point, the tag will be unclosed. Therefore, it must be closed somewhere. 
                if ( $this->_aColumnInfo['iCurrColPos'] == 1 ) 
                    $strItem = '<div class="' . $this->_aColumnOption['sClassAttrRow']  . '">' . $strItem;
            
                // If the current column position reached the set max column, increment the current position of row
                if ( $this->_aColumnInfo['iCurrColPos'] % $_iMaxCols == 0 ) {
                    $this->_aColumnInfo['iCurrRowPos']++;        // increment the row number
                    $this->_aColumnInfo['iCurrColPos'] = 0;        // reset the current column position
                    $strItem .= '</div>';  // close the section(row) div tag
                    $this->_aColumnInfo['bRowTagClosed'] = true;
                }        
                
                $_aOutput[] = $strItem;
            
            }
            
            // if the section(row) tag is not closed, close it
            if ( ! $this->_aColumnInfo['bRowTagClosed'] ) $_aOutput[] .= '</div>';    
            $this->_aColumnInfo['bRowTagClosed'] = true;
            
            // enclose the output in the group tag
            return '<div class="apfl_addon_list_container">' 
                    . '<div class="' . $this->_aColumnOption['sClassAttr'] . ' ' . $this->_aColumnOption['sClassAttrGroup'] . '">'
                        . implode( '', $_aOutput )
                    . '</div>'
                . '</div>';
                        
        }
    
}
