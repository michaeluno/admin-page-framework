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
     * Stores the ID selector names for screen icons. `generic` is not available in WordPress v3.4.x.
     * 
     * @since       2.0.0
     * @since       3.3.1       Moved from `AdminPageFramework_Page`.
     * @var         array
     * @static
     * @access      protected
     * @internal
     */     
    static protected $_aScreenIconIDs = array(
        'edit', 'post', 'index', 'media', 'upload', 'link-manager', 'link', 'link-category', 
        'edit-pages', 'page', 'edit-comments', 'themes', 'plugins', 'users', 'profile', 
        'user-edit', 'tools', 'admin', 'options-general', 'ms-admin', 'generic',
    );    
    
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
        
}