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
 * Adds a tab of the set page to the loader plugin.
 *
 * @since       3.5.0
 */
class AdminPageFrameworkLoader_AdminPage_Help_Example extends AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase {

    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {

        $_aItems     = $this->getContentsByHeader( $this->getReadmeContents(), 3 );
        $_iLastIndex = count( $_aItems ) - 1;
        foreach( $_aItems as $_iIndex => $_aContent ) {

            $_oParser   = new AdminPageFramework_WPReadmeParser;
            $_oParser->setText( $_aContent[ 1 ] );
            $_sContent  = $_oParser->get();
            $oAdminPage->addSettingSections(
                $this->sPageSlug, // the target page slug
                array(
                    'section_id'        => 'examples_' . $_iIndex,
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
                AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/text/examples.txt',
                '',
                array( 'Examples' )
            );
        }

}
