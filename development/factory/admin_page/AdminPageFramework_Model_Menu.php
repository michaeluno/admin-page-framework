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
 * @since           3.6.3           Changed the name from `AdminPageFramework_Menu_Model`.
 * @extends         AdminPageFramework_Controller_Page
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Model_Menu extends AdminPageFramework_Controller_Page {
    
    /**
     * Registers necessary callbacks and sets up properties.
     * 
     * @internal
     */
    public function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {
        
        parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain );
        
        if ( $this->oProp->bIsAdminAjax ) {
            return;
        }
        
        add_action( 'admin_menu', array( $this, '_replyToBuildMenu' ), 98 );     
        
    }     
    
    /**
     * A look-up array for the built-in root menu slugs.
     * 
     * @since       2.0.0
     * @since       3.1.0       Changed it non-static.
     * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
     * @var         array       Holds the built-in root menu slugs.
     * @internal
     */ 
    protected $_aBuiltInRootMenuSlugs = array(
        // All keys must be lower case to support case insensitive look-ups.
        'dashboard'     => 'index.php',
        'posts'         => 'edit.php',
        'media'         => 'upload.php',
        'links'         => 'link-manager.php',
        'pages'         => 'edit.php?post_type=page',
        'comments'      => 'edit-comments.php',
        'appearance'    => 'themes.php',
        'plugins'       => 'plugins.php',
        'users'         => 'users.php',
        'tools'         => 'tools.php',
        'settings'      => 'options-general.php',
        'network admin' => "network_admin_menu",
    );     
    
    /**
     * Builds the sidebar menu of the added pages.
     * 
     * @since       2.0.0
     * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
     * @callback    action      admin_menu
     * @internal
     */
    public function _replyToBuildMenu() {

        // If the root menu label is not set but the slug is set, 
        if ( $this->oProp->aRootMenu[ 'fCreateRoot' ] ) {
            $this->_registerRootMenuPage();
        }
        
        // Apply filters to let other scripts add sub menu pages.
        // Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
        $this->oProp->aPages = $this->oUtil->addAndApplyFilter( 
            $this,
            "pages_{$this->oProp->sClassName}", 
            $this->oProp->aPages
        );
        
        // Sort the page array.
        uasort( $this->oProp->aPages, array( $this->oUtil, 'sortArrayByKey' ) ); 
        
        // Set the default page, the first element.
        $this->_setDefaultPage();
    
        // Register them.
        foreach ( $this->oProp->aPages as &$aSubMenuItem ) {
            
            // needs to be sanitized because there are hook filters applied to this array.
            $_oFormatter = new AdminPageFramework_Format_SubMenuItem( 
                $aSubMenuItem, 
                $this 
            );
            $aSubMenuItem = $_oFormatter->get();
            
            // store the page hook; this is same as the value stored in the global $page_hook or $hook_suffix variable. 
            $aSubMenuItem[ '_page_hook' ] = $this->_registerSubMenuItem( $aSubMenuItem ); 
            
        }

        // After adding the sub menus, if the root menu is created, remove the page that is automatically created when registering the root menu.
        if ( $this->oProp->aRootMenu[ 'fCreateRoot' ] ) {
            remove_submenu_page( 
                $this->oProp->aRootMenu[ 'sPageSlug' ], 
                $this->oProp->aRootMenu[ 'sPageSlug' ] 
            );
        }

        $this->oProp->_bBuiltMenu = true;
        
    }    
        /**
         * Sets the default page.
         * @internal   
         * @since       3.6.0
         */
        private function _setDefaultPage() {

            foreach ( $this->oProp->aPages as $_aPage ) {
                
                if ( ! isset( $_aPage[ 'page_slug' ] ) ) { 
                    continue; 
                }
                $this->oProp->sDefaultPageSlug = $_aPage[ 'page_slug' ];
                return;
                
            }
        
        }        
        /**
         * Registers the root menu page.
         * 
         * @since       2.0.0
         * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
         * @internal
         */ 
        private function _registerRootMenuPage() {
            $this->oProp->aRootMenu[ '_page_hook' ] = add_menu_page(  
                $this->oProp->sClassName,                 // Page title - will be invisible anyway
                $this->oProp->aRootMenu[ 'sTitle' ],      // Menu title - should be the root page title.
                $this->oProp->sCapability,                // Capability - access right
                $this->oProp->aRootMenu[ 'sPageSlug' ],   // Menu ID 
                '',                                       // Page content displaying function - the root page will be removed so no need to register a function.
                $this->oProp->aRootMenu[ 'sIcon16x16' ],  // icon path
                $this->oUtil->getElement( 
                    $this->oProp->aRootMenu, 
                    'iPosition', 
                    null 
                )  // menu position
            );
        }
               
        /**
         * Registers the sub-menu item.
         * 
         * @since       2.0.0
         * @since       3.0.0       Changed the name from `registerSubMenuPage()`.
         * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
         * @remark      Used in the `buildMenu()` method. 
         * @remark      Within the `admin_menu` hook callback process.
         * @remark      The sub menu page slug should be unique because `add_submenu_page()` can add one callback per page slug.
         * @remark      Assumes the argument array is aready formatted.
         * @internal
         * @return      string      The page hook if the page is added.
         */ 
        private function _registerSubMenuItem( array $aArgs ) {

            if ( ! current_user_can( $aArgs['capability'] ) ) {
                return '';
            }
                 
            $_sRootPageSlug = $this->oProp->aRootMenu['sPageSlug'];
            $_sMenuSlug     = plugin_basename( $_sRootPageSlug ); // to be compatible with add_submenu_page()
            
            switch( $aArgs['type'] ) {
                case 'page':
                    // it's possible that the page_slug key is not set if the user uses a method like setPageHeadingTabsVisibility() prior to addSubMenuItam().
                    return $this->_addPageSubmenuItem(
                        $_sRootPageSlug,
                        $_sMenuSlug,
                        $aArgs['page_slug'],
                        $this->oUtil->getElement( $aArgs, 'page_title', $aArgs['title'] ),
                        $this->oUtil->getElement( $aArgs, 'menu_title', $aArgs['title'] ),
                        $aArgs['capability'],
                        $aArgs['show_in_menu']
                    );
                case 'link':
                    return $this->_addLinkSubmenuItem( 
                        $_sMenuSlug, 
                        $aArgs['title'], 
                        $aArgs['capability'],
                        $aArgs['href'],
                        $aArgs['show_in_menu']
                    );
                default:
                    return '';
            }
      
        }     
            /**
             * Adds a page sub-menu item.
             * 
             * @since       3.3.0
             * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
             * @return      string      The page hook of the added page.
             */
            private function _addPageSubmenuItem( $sRootPageSlug, $sMenuSlug, $sPageSlug, $sPageTitle, $sMenuTitle, $sCapability, $bShowInMenu ) {
                
                if ( ! $sPageSlug ) {
                    return '';
                }
                $_sPageHook = add_submenu_page( 
                    $sRootPageSlug,         // the root(parent) page slug
                    $sPageTitle,            // page title
                    $sMenuTitle,            // menu title
                    $sCapability,           // capability
                    $sPageSlug,             // menu slug
                    // In admin.php ( line 149 of WordPress v3.6.1 ), do_action($page_hook) ( where $page_hook is $_sPageHook )
                    // will be executed and it triggers the __call() magic method with the method name of "md5 class hash + _page_ + this page slug".
                    array( $this, $this->oProp->sClassHash . '_page_' . $sPageSlug )
                );     
                
                // Ensure only it is added one time per page slug.
                if ( ! isset( $this->oProp->aPageHooks[ $_sPageHook ] ) ) {
                    // 3.4.1+ Give a lower priority as the page meta box class also hooks the current_screen to register form elements.
                    // When the validation callback is triggered, their form registration should be done already. So this hook should be loaded later than them.
                    add_action( 'current_screen' , array( $this, "load_pre_" . $sPageSlug ), 20 );
                    
                    // 3.6.3+
                    // It is possible that an in-page tab is added during the above hooks and the current page is the default tab without the tab GET query key in the url. 
                    // Set a low priority because the user may add in-page tabs in their callback method of this action hook.
                    add_action( "load_" . $sPageSlug, array( $this, '_replyToFinalizeInPageTabs' ), 9999 );
                    
                    // 3.6.3+
                    add_action( "load_after_" . $sPageSlug, array( $this, '_replyToEnqueuePageAssets' ) );
                    
                }
                $this->oProp->aPageHooks[ $sPageSlug ] = $this->oUtil->getAOrB(
                    is_network_admin(),
                    $_sPageHook . '-network',
                    $_sPageHook
                );
                
                // If the visibility option is false, remove the one just added from the sub-menu array
                if ( ! $bShowInMenu ) {
                    $this->_removePageSubmenuItem( $sMenuSlug, $sMenuTitle, $sPageTitle, $sPageSlug );
                }                
                return $_sPageHook;
            
            }
                /**
                 * Removes a page sub-menu item.
                 * 
                 * @since       3.3.0
                 * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
                 * @return      void
                 */
                private function _removePageSubmenuItem( $sMenuSlug, $sMenuTitle, $sPageTitle, $sPageSlug ){
                    
                    foreach( ( array ) $GLOBALS['submenu'][ $sMenuSlug ] as $_iIndex => $_aSubMenu ) {
                      
                        if ( ! isset( $_aSubMenu[ 3 ] ) ) { 
                            continue; 
                        }
                                               
                        // the array structure is defined in plugin.php - $submenu[$parent_slug][] = array ( $menu_title, $capability, $menu_slug, $page_title ) 
                        $_aA = array(
                            $_aSubMenu[ 0 ],
                            $_aSubMenu[ 3 ],
                            $_aSubMenu[ 2 ],
                        );
                        $_aB = array(
                            $sMenuTitle,
                            $sPageTitle,
                            $sPageSlug,
                        );
                        if ( $_aA !== $_aB ) { 
                            continue;
                        }

                        $this->_removePageSubMenuItemByIndex( 
                            $sPageSlug, 
                            $sMenuSlug, 
                            $_iIndex 
                        );

                        // The page title in the browser window title bar will miss the page title as this is left as it is.
                        $this->oProp->aHiddenPages[ $sPageSlug ] = $sMenuTitle;
                        add_filter( 
                            'admin_title', 
                            array( $this, '_replyToFixPageTitleForHiddenPages' ), 
                            10, 
                            2 
                        );
                        break;                                                                                           
                        
                    }                    
                    
                }
                    /**
                     * Remove the specified item from the menu. 
                     * 
                     * If the current page is being accessed, do not remove it from the menu.
                     * 
                     * @since       3.5.3
                     * @return      void
                     * @internal
                     */
                    private function _removePageSubMenuItemByIndex( $sPageSlug, $sMenuSlug, $_iIndex ) {
                        
                        // If it is in the network admin area, do not remove the menu; otherwise, it gets not accessible. 
                        if ( is_network_admin() ) {
                            unset( $GLOBALS['submenu'][ $sMenuSlug ][ $_iIndex ] );
                            return;
                        } 
                        
                        if ( 
                            ! isset( $_GET['page'] ) 
                            || isset( $_GET['page'] ) && $sPageSlug != $_GET['page'] 
                        ) {
                            unset( $GLOBALS['submenu'][ $sMenuSlug ][ $_iIndex ] );
                        }                        
                        
                    }
            /**
             * Adds a link sub-menu item.
             * 
             * @since       3.3.0
             * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
             * @since       3.5.3       Added the `$bShowInMenu` parameter.
             * @return      void
             */
            private function _addLinkSubmenuItem( $sMenuSlug, $sTitle, $sCapability, $sHref, $bShowInMenu ) {
                if ( ! $bShowInMenu ) {
                    return;
                }
                if ( ! isset( $GLOBALS['submenu'][ $sMenuSlug ] ) ) {
                    $GLOBALS['submenu'][ $sMenuSlug ] = array();
                }
                $GLOBALS['submenu'][ $sMenuSlug ][] = array ( 
                    $sTitle, 
                    $sCapability, 
                    $sHref,
                );   
            }
        
        /**
         * A callback function for the admin_title filter to fix the page title for hidden pages.
         * 
         * @since       2.1.4
         * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
         * @internal
         */
        public function _replyToFixPageTitleForHiddenPages( $sAdminTitle, $sPageTitle ) {
            if ( isset( $_GET['page'], $this->oProp->aHiddenPages[ $_GET['page'] ] ) ) {
                return $this->oProp->aHiddenPages[ $_GET['page'] ] . $sAdminTitle;
            }    
            return $sAdminTitle;     
        }  
}