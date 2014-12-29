<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
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
     * Represents the array structure of an in-page tab array.
     * 
     * @since       2.0.0
     * @since       3.3.1       Moved from `AdminPageFramework_Page`.
     * @var         array
     * @static
     * @access      private
     * @internal
     */     
    static protected $_aStructure_InPageTabElements = array(
        'page_slug'         => null,
        'tab_slug'          => null,
        'title'             => null,
        'order'             => null,
        'show_in_page_tab'  => true,
        'parent_tab_slug'   => null, // this needs to be set if the above show_in_page_tab is false so that the framework can mark the parent tab to be active when the hidden page is accessed.
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

        if ( ! $this->oProp->isPageAdded() ) { return; }

        foreach( $this->oProp->aPages as $sPageSlug => $aPage ) {
            
            if ( ! isset( $this->oProp->aInPageTabs[ $sPageSlug ] ) ) { continue; }
            
            // Apply filters to let modify the in-page tab array.
            $this->oProp->aInPageTabs[ $sPageSlug ] = $this->oUtil->addAndApplyFilter( // Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
                $this,
                "tabs_{$this->oProp->sClassName}_{$sPageSlug}",
                $this->oProp->aInPageTabs[ $sPageSlug ]     
            );    
            // Added in-page arrays may be missing necessary keys so merge them with the default array structure.
            foreach( $this->oProp->aInPageTabs[ $sPageSlug ] as &$aInPageTab ) {
                $aInPageTab = $aInPageTab + self::$_aStructure_InPageTabElements;
                $aInPageTab['order'] = is_null( $aInPageTab['order'] ) ? 10 : $aInPageTab['order'];
            }
                        
            // Sort the in-page tab array.
            uasort( $this->oProp->aInPageTabs[ $sPageSlug ], array( $this, '_sortByOrder' ) );
            
            // Set the default tab for the page.
            // Read the value as reference; otherwise, a strange bug occurs. It may be due to the variable name, $aInPageTab, is also used as reference in the above foreach.
            foreach( $this->oProp->aInPageTabs[ $sPageSlug ] as $sTabSlug => &$aInPageTab ) {     
            
                if ( ! isset( $aInPageTab['tab_slug'] ) ) { continue; }
                
                // Regardless of whether it's a hidden tab, it is stored as the default in-page tab.
                $this->oProp->aDefaultInPageTabs[ $sPageSlug ] = $aInPageTab['tab_slug'];
                    
                break; // The first iteration item is the default one.
            }
        }

    }     
        /**
         * An alias of _finalizeInPageTabs().
         * @deprecated  3.3.0
         * @since       3.3.1       Moved from `AdminPageFramework_Page`.
         */
        public function _replyToFinalizeInPageTabs() { $this->_finalizeInPageTabs(); }
        
}