<?php
/**
 * Admin Page Framework Loader
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a tab of the set page to the loader plugin.
 * 
 * @since       3.5.0    
 * @since       3.5.3       Extends `AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase`.
 * @extends     AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase
 */
class AdminPageFrameworkLoader_AdminPage_Help_Tip extends AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase {
   
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
            
        $_aItems     = $this->getContentsByHeader( $this->getReadmeContents(), 4 );
        $_iLastIndex = count( $_aItems ) - 1;
        foreach( $_aItems as $_iIndex => $_aContent ) {

            $_oParser   = new AdminPageFramework_WPReadmeParser( $_aContent[ 1 ] );
            $_sContent  = $_oParser->get();
            $oAdminPage->addSettingSections(
                $this->sPageSlug, // the target page slug  
                array(
                    'section_id'        => 'tips_' . $_iIndex,
                    'title'             => $_aContent[ 0 ],
                    'collapsible'       => array(
                        'toggle_all_button' => $_iLastIndex === $_iIndex
                            ? array( 'bottom-right' )
                            : ( 0 === $_iIndex
                                ? array( 'top-right' )
                                : false
                            ),
                    ),
                    'content'           => $_sContent,
                            
                )
            );
            
        }

    }
        /**
         * @return      string
         */
        private function getReadMeContents()  {
            return $this->_getReadmeContents(
                AdminPageFrameworkLoader_Registry::$sDirPath . '/readme.txt', // source path
                '', // TOC title
                array( 'Other Notes' )  // sections
            );

        }
    
}
