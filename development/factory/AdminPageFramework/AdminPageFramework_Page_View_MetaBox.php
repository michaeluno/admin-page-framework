<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to insert meta boxes in pages added by the framework.
 *
 * @abstract
 * @since           3.0.0
 * @since           3.3.1       Changed the name from `AdminPageFramework_Page_MetaBox`.
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Page_View_MetaBox extends AdminPageFramework_Page_Model {
    
    /**
     * Sets up hooks and properties.
     */
    public function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {
                        
        parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain );
        
        if ( $this->oProp->bIsAdminAjax ) {
            return;
        }     
        
        // Since the screen object needs to be established, some hooks are too early like admin_init or admin_menu.
        add_action( 
            'admin_head', 
            array( $this, '_replyToEnableMetaBox' )
        ); 
                    
    }
        
        
    /**
     * Renders the registered meta boxes.
     * 
     * @remark      Called in the _renderPage() method.
     * @remark      If no meta box is registered, nothing will be printed.
     * @param       string $sContext `side`, `normal`, or `advanced`.
     * @since 3.0.0
     * @internal
     */
    protected function _printMetaBox( $sContext, $iContainerID ) {

        $_sCurrentScreenID =  $this->oUtil->getCurrentScreenID();

        /* If nothing is registered do not render even the container */
        if ( ! isset( $GLOBALS['wp_meta_boxes'][ $_sCurrentScreenID ][ $sContext ] ) ) {
            return;
        }
        if ( count( $GLOBALS['wp_meta_boxes'][ $_sCurrentScreenID ][ $sContext ] ) <= 0 ) {
            return;
        }
        
        echo "<div id='postbox-container-{$iContainerID}' class='postbox-container'>";
            do_meta_boxes( '', $sContext, null ); 
        echo "</div>";

    }
    
    /**
     * Returns the number of columns in the page.
     * 
     * @since           3.0.0
     * @internal
     */
    protected function _getNumberOfColumns() {
        $_iColumns = get_current_screen()->get_columns();
        return $_iColumns
            ? $_iColumns
            : 1;    // default - this is because generic pages do not have meta boxes.
    } 
    
        /**
         * Checks if there are meta boxes added to the given slug of the page.
         * @internal
         * @since       3.0.0 
         * @return      boolean
         */
        private function _isMetaBoxAdded( $sPageSlug='' ) {
            
            if ( ! isset( $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] ) || ! is_array( $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] ) ) {
                return false;
            }
            $sPageSlug = $sPageSlug 
                ? $sPageSlug 
                : $this->oUtil-> getElement( $_GET, 'page', '' );
                
            if ( ! $sPageSlug ) {     
                return false;
            }
            
            foreach( $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] as $sClassName => $oMetaBox ) {
                if ( $this->_isPageOfMetaBox( $sPageSlug, $oMetaBox ) ) { 
                    return true; 
                }
            }

            return false;
            
        }
        /**
         * Checks if the given page slug belongs to the meta box of the given meta box object.
         * @internal
         * @remark      Checks the tab array in the aPageSlugs array and if the loading page tab does not match the tab elements, it yields false.
         * @since       3.0.0
         */
        private function _isPageOfMetaBox( $sPageSlug, $oMetaBox ) {
            
            if ( in_array( $sPageSlug , $oMetaBox->oProp->aPageSlugs ) ) {
                return true; // for numeric keys with a string value.
            }
            if ( ! array_key_exists( $sPageSlug , $oMetaBox->oProp->aPageSlugs ) ) {
                return false; // for keys of page slugs, the key does not exist, it means not added.
            }
            
            /* So the page slug key and its tab array is set. This means the user want to specify the meta box visibility per a tab basis. */     
            $_aTabs             = $oMetaBox->oProp->aPageSlugs[ $sPageSlug ];
            $_sCurrentTabSlug   = $this->oProp->getCurrentTabSlug();
            return ( $_sCurrentTabSlug && in_array( $_sCurrentTabSlug, $_aTabs ) );
                
        }
        
                    
    /**
     * Enables meta boxes for the currently loading page 
     * 
     * @remark      In order to enable the Screen Option tab, this must be called at earlier point of the page load. The admin_head hooks seems to be sufficient.
     * @since       3.0.0
     * @internal
     * @callback    action      admin_head
     */
    public function _replyToEnableMetaBox() {
        
        if ( ! $this->oProp->isPageAdded() ) {     
            return;
        }
        if ( ! $this->_isMetaBoxAdded() ) {
            return;
        }
        
        $_sCurrentScreenID =  $this->oUtil->getCurrentScreenID();

        // Trigger the add_meta_boxes hooks to allow meta boxes to be added.
        do_action( "add_meta_boxes_{$_sCurrentScreenID}", null );
        do_action( 'add_meta_boxes', $_sCurrentScreenID, null );
        
        wp_enqueue_script( 'postbox' );
        add_screen_option(
            'layout_columns', 
            array(
                'max'       => 2, 
                'default'   => 2,
            )
        );
        
        // Used to save screen options.
        wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
        wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
                
        // the network admin adds '-network' in the screen ID and the hooks with that id won't be triggered so use the 'page_hook' global variable.
        if ( isset( $GLOBALS[ 'page_hook' ] ) ) {
            add_action( "admin_footer-{$GLOBALS['page_hook']}", array( $this, '_replyToAddMetaboxScript' ) );    
        }

    }
        /**
         * Adds meta box script.
         * @remark      This method may be called multiple times if the main class is instantiated multiple times. But it is only enough to perform once.
         * @since       3.0.0
         * @internal
         * @callback    action      admin_footer-{$GLOBALS[ 'page_hook' ]}
         */
        public function _replyToAddMetaboxScript() {

            if ( isset( $GLOBALS['aAdminPageFramework']['bAddedMetaBoxScript'] ) && $GLOBALS['aAdminPageFramework']['bAddedMetaBoxScript'] ) {
                return;
            }
            $GLOBALS['aAdminPageFramework']['bAddedMetaBoxScript'] = true;

            $_sScript = <<<JAVASCRIPTS
jQuery( document).ready( function(){ 
    postboxes.add_postbox_toggles( pagenow ); 
});
JAVASCRIPTS;

            echo '<script class="admin-page-framework-insert-metabox-script">'
                    . $_sScript
                . '</script>';

        }
    
}