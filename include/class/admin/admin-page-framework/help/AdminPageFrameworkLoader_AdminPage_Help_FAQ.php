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
class AdminPageFrameworkLoader_AdminPage_Help_FAQ extends AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase {

    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {

        $_aSections  = $this->getContentsByHeader( $this->getFAQContents(), 4 );
        foreach( $_aSections as $_iIndex => $_aContent ) {

            $_sTitle   = $_aContent[ 0 ];
            $_sContent = $this->_getFAQSubSections( $_aContent[ 1 ] );
            if ( in_array( $_sTitle, array( 'Tutorials' ) ) ) {
                continue;
            }
            $oAdminPage->addSettingSections(
                $this->sPageSlug, // the target page slug
                array(
                    'section_id'        => 'faq_sections_' . $_iIndex,
                    'tab_slug'          => $this->sTabSlug,
                    'section_tab_slug'  => 'apf_faq',
                    'title'             => $_sTitle,
                    'content'           => $_sContent,
                )
            );
        }

    }
        /**
         * @return      array|string        If sections exits, an array holding sections. If no, a string content of the item.
         */
        private function _getFAQSubSections( $asItems ) {

            if ( empty( $asItems ) ) {
                return array();
            }
            $aItems = $this->getContentsByHeader( $asItems, 5 );

            $_aNestedSections = array();
            $_iLastIndex = count( $aItems ) - 1;
            foreach( $aItems as $_iIndex => $_aContent ) {

                $_oParser   = new AdminPageFramework_WPReadmeParser( $_aContent[ 1 ] );

                // If no sections, return the contents of the first item.
                if ( ! $_aContent[ 0 ] ) {
                    return $_oParser->get();
                }

                $_aNestedSections[] = array(
                    'section_id'        => 'faq_item_' . $_iIndex,
                    'title'             => $_aContent[ 0 ],
                    'collapsible'       => array(
                        'toggle_all_button' => $_iLastIndex === $_iIndex
                            ? array( 'bottom-right' )
                            : ( 0 === $_iIndex
                                ? array( 'top-right' )
                                : false
                            ),
                    ),
                    'content'           => $_oParser->get(),
                );
            }
            return $_aNestedSections;

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

}
