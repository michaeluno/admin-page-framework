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
 * Adds a tab of the set page to the loader plugin.
 * 
 * @since       3.5.0    
 */
class AdminPageFrameworkLoader_AdminPage_Help_FAQ {

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
                'title'         => __( "FAQ", 'admin-page-framework-loader' ),   // '
            )
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );
  
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );
        
        $_aFAQs = array();
        foreach( $this->getContentsByHeader( $this->getFAQContents(), 4 ) as $_aContent ) {
            $_aFAQs = array_merge( $_aFAQs, $this->getContentsByHeader( $_aContent[ 1 ], 5 ) );
        }        
        $_iLastIndex = count( $_aFAQs ) - 1;
        foreach( $_aFAQs as $_iIndex => $_aContent ) {

            $_oParser   = new AdminPageFramework_WPReadmeParser;
            $_oParser->setText( $_aContent[ 1 ] );
            $_sContent  = $_oParser->get();
            $oAdminPage->addSettingSections(    
                $this->sPageSlug, // the target page slug  
                array(
                    'section_id'        => 'faq_items_' . $_iIndex,
                    'tab_slug'          => $this->sTabSlug,                
                    'title'             => $_aContent[ 0 ],
                    'collapsible'       => array(
                        'toggle_all_button' => $_iLastIndex === $_iIndex 
                            ? array( 'bottom-right' )
                            : ( 0 === $_iIndex
                                ? array( 'top-right' )
                                : false
                            ),
                        // 'container'         => 'section',
                    )
                )
            );
            $oAdminPage->addSettingFields(    
                'faq_items_' . $_iIndex , // the target section ID     
                array(
                    'field_id'      => 'faq',
                    'type'          => 'faq',
                    'show_title_column'  => false,
                    'before_field'   => $_sContent,
                )
            );                 
            
        }        

    }
    
        private function getFAQContents()  {
            
            $_aReplacements   = array(
                '%PLUGIN_DIR_URL%'  => AdminPageFrameworkLoader_Registry::getPluginURL(),
                '%WP_ADMIN_URL%'    => admin_url(),
            );
            $_oWPReadmeParser = new AdminPageFramework_WPReadmeParser( 
                AdminPageFrameworkLoader_Registry::$sDirPath . '/readme.txt',
                $_aReplacements
            );    
            return $_oWPReadmeParser->getRawSection( 'Frequently asked questions' );                        
            
        }
        /**
         * Returns HTML contents divided by heading.
         * 
         * For example,
         * <h3>First Heading</h3>
         * Some text.
         * <h3>Second Heading</h3>
         * Another text.
         * 
         * Will be
         * array(  
         *  array( 'First Heading' => 'Some text', ),
         *  array( 'Second Heading' => 'Another text', ),
         * )
         */
        private function getContentsByHeader( $sContents, $iHeaderNumber=2 ) {
        
            $_aContents = array();
            $_aSplitContents = preg_split( 
                // '/^[\s]*==[\s]*(.+?)[\s]*==/m', 
                '/(<h[' . $iHeaderNumber . ']*[^>]*>.*?<\/h[' . $iHeaderNumber . ']>)/i',
                $sContents,
                -1, 
                PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY 
            );                   

            foreach( $_aSplitContents as $_iIndex => $_sSplitContent ) {
                if ( ! preg_match( '/<h[' . $iHeaderNumber . ']*[^>]*>(.*?)<\/h[' . $iHeaderNumber . ']>/i', $_sSplitContent , $_aMatches ) ) {
                    continue;
                }
            
                if ( ! isset( $_aMatches[ 1 ] ) ) {
                    continue;
                }
                if ( isset( $_aSplitContents[ $_iIndex + 1 ] ) )  {
                    $_aContents[] = array( 
                        $_aMatches[ 1 ],
                        $_aSplitContents[ $_iIndex + 1 ]
                    );
                }
            }
       
            return $_aContents;
            
        }
        
    
    public function replyToDoTab() {
    }
    
}
