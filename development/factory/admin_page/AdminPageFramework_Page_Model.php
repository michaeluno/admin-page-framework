<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to manipulate menu items.
 *
 * @abstract
 * @since           3.3.1
 * @extends         AdminPageFramework_Form_Controller
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Page_Model extends AdminPageFramework_Form_Controller {
       
    /**
     * Finalizes the in-page tab property array.
     * 
     * This finalizes the added in-page tabs and sets the default in-page tab for each page.
     * Also this sorts the in-page tab property array.
     * This must be done before registering settings sections because the default tab needs to be determined in the process.
     * 
     * @since       2.0.0
     * @since       3.3.0       Changed the name from `_replyToFinalizeInPageTabs()` and been no longer a callback.
     * @since       3.3.1       Moved from `AdminPageFramework_Page`.
     * @return      void
     * @internal
     */         
    protected function _finalizeInPageTabs() {

        if ( ! $this->oProp->isPageAdded() ) { 
            return; 
        }

        foreach( $this->oProp->aPages as $_sPageSlug => $_aPage ) {
            
            if ( ! isset( $this->oProp->aInPageTabs[ $_sPageSlug ] ) ) { 
                continue; 
            }
            
            // Format
            $_oFormatter = new AdminPageFramework_Format_InPageTabs(
                $this->oProp->aInPageTabs[ $_sPageSlug ], // subject array
                $_sPageSlug,
                $this   // the factory class
            );
            $this->oProp->aInPageTabs[ $_sPageSlug ] = $_oFormatter->get();
         
            // Set the default tab for the page.
            $this->oProp->aDefaultInPageTabs[ $_sPageSlug ] = $this->_getDefaultInPageTab( 
                $_sPageSlug,
                $this->oProp->aInPageTabs[ $_sPageSlug ]
            );
         
        }

    }     
        /**
         * Returns the default in-page tab slug of the given page slug.
         * 
         * @internal
         * @remark      The first found item is the default one.
         * @return      string
         * @since       3.6.0
         */
        private function _getDefaultInPageTab( $sPageSlug, $aInPageTabs ) {
            foreach( $aInPageTabs as $_aInPageTab ) {                 
                if ( ! isset( $_aInPageTab[ 'tab_slug' ] ) ) { 
                    continue; 
                }                
                // Regardless of whether it's a hidden tab, it is stored as the default in-page tab.
                return $_aInPageTab[ 'tab_slug' ];
            }
        }    

        /**
         * An alias of _finalizeInPageTabs().
         * @deprecated  3.3.0
         * @since       3.3.1       Moved from `AdminPageFramework_Page`.
         */
        public function _replyToFinalizeInPageTabs() { $this->_finalizeInPageTabs(); }
    
    /**
     * @remark      Accessed from the form definition class to determine the section and field capability.
     * @return      string      The capability value.
     * @since       3.6.0
     * @internal
     */
    public function _getPageCapability( $sPageSlug ) {
        return $this->oUtil->getElement(
            $this->oProp->aPages,
            array( $sPageSlug, 'capability' )
        );
    }
    /**
     * 
     * @remark      Accessed from the form definition class to determine the section and field capability.
     * @return      string      The capability value.
     * @since       3.6.0
     * @internal
     */
    public function _getInPageTabCapability( $sTabSlug, $sPageSlug ) {
        return $this->oUtil->getElement(
            $this->oProp->aInPageTabs,
            array( $sPageSlug, $sTabSlug, 'capability' )
        );            
    }        
        
}