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
    function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {
                        
        parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain );
        
        if ( $this->oProp->bIsAdminAjax ) {
            return;
        }     
        add_action( 'admin_head', array( $this, '_replyToEnableMetaBox' ) ); // since the screen object needs to be established, some hooks are too early like admin_init or admin_menu.
        add_filter( 'screen_layout_columns', array( $this, '_replyToSetNumberOfScreenLayoutColumns'), 10, 2 ); // sets the column layout option for meta boxes.
                    
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
     * @since 3.0.0
     * @internal
     */
    protected function _getNumberOfColumns() {
    
        $_sCurrentScreenID = $this->oUtil->getCurrentScreenID();
        if ( isset( $GLOBALS['wp_meta_boxes'][ $_sCurrentScreenID ][ 'side' ] ) && count( $GLOBALS['wp_meta_boxes'][ $_sCurrentScreenID ][ 'side' ] ) > 0 )
            return 2;
        return 1;

        // the below does not seem to work
        return 1 == get_current_screen()->get_columns() 
            ? '1' 
            : '2';     
        
    }
    
    /**
     * Sets the number of screen layout columns.
     * @since 3.0.0
     */
    public function _replyToSetNumberOfScreenLayoutColumns( $aColumns, $sScreenID ) { //for WordPress 2.8 we have to tell, that we support 2 columns !
        
        if ( ! isset( $GLOBALS['page_hook'] ) ) { return; }
        if ( ! $this->_isMetaBoxAdded() ) { return; }
        if ( ! $this->oProp->isPageAdded() ) { return; }
        
        $_sCurrentScreenID = $this->oUtil->getCurrentScreenID();
        
        add_filter( 'get_user_option_' . 'screen_layout_' . $_sCurrentScreenID, array( $this, '_replyToReturnDefaultNumberOfScreenColumns' ), 10, 3 ); // this will give the screen object the default value
        
        if ( $sScreenID == $_sCurrentScreenID ) {
            $aColumns[ $_sCurrentScreenID ] = 2;
        }
    
        return $aColumns;
        
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
                : ( isset( $_GET['page'] )
                    ? $_GET['page']
                    : ''
                );
            if ( ! $sPageSlug ) {     
                return false;
            }
            
            foreach( $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] as $sClassName => $oMetaBox ) {
                if ( $this->_isPageOfMetaBox( $sPageSlug, $oMetaBox ) ) return true;     
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
            $aTabs = $oMetaBox->oProp->aPageSlugs[ $sPageSlug ];
    
            $sCurrentTabSlug = isset( $_GET['tab'] ) 
                ? $_GET['tab']
                : ( isset( $_GET['page'] ) 
                    ? $this->oProp->getDefaultInPageTab( $_GET['page'] )
                    : ''
                );
            if ( $sCurrentTabSlug && in_array( $sCurrentTabSlug, $aTabs ) ) return true;
                        
            return false;
        }
        
    /**
     * Returns the default number of screen columns
     * @since       3.0.0
     */
    public function _replyToReturnDefaultNumberOfScreenColumns( $vStoredData, $sOptionKey, $oUser ) {

        $_sCurrentScreenID = $this->oUtil->getCurrentScreenID();
        if ( $sOptionKey != 'screen_layout_' . $_sCurrentScreenID ) return $vStoredData; // if the option key is different, do nothing.
    
        return ( $vStoredData )
            ? $vStoredData
            : $this->_getNumberOfColumns(); // the default value;
        
    }    
                    
    /**
     * Enables meta boxes for the currently loading page 
     * 
     * @remark      In order to enable the Screen Option tab, this must be called at earlier point of the page load. The admin_head hooks seems to be sufficient.
     * @since       3.0.0
     * @internal
     */
    public function _replyToEnableMetaBox() {
        
        if ( ! $this->oProp->isPageAdded() ) {     
            return;
        }
        if ( ! $this->_isMetaBoxAdded() ) {
            return;
        }
        
        $_sCurrentScreenID =  $this->oUtil->getCurrentScreenID();

        /* Trigger the add_meta_boxes hooks to allow meta boxes to be added */
        do_action( "add_meta_boxes_{$_sCurrentScreenID}", null );
        do_action( 'add_meta_boxes', $_sCurrentScreenID, null );
        wp_enqueue_script( 'postbox' );
        
        // the network admin adds '-network' in the screen ID and the hooks with that id won't be triggered so use the 'page_hook' global variable.
        if ( isset( $GLOBALS['page_hook'] ) ) {
            add_action( "admin_footer-{$GLOBALS['page_hook']}", array( $this, '_replyToAddMetaboxScript' ) );    
        }

    }
        /**
         * Adds meta box script.
         * @remark      This method may be called multiple times if the main class is instantiated multiple times. But it is only enough to perform once.
         * @since       3.0.0
         * @internal
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