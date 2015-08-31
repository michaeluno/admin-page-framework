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
 * @extends         AdminPageFramework_Menu_Model
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Menu_Model extends AdminPageFramework_Page_Controller {
    
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
     * Represents the structure of the sub-menu link array.
     * 
     * @since       2.0.0
     * @since       2.1.4       Changed to be static since it is used from multiple classes.
     * @since       3.0.0       Moved from the link class.
     * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
     * @remark      The scope is public because this is accessed from an extended class.
     * @internal
     */ 
    protected static $_aStructure_SubMenuLinkForUser = array(     
        'type'                  => 'link',    
        'title'                 => null, // required
        'href'                  => null, // required
        'capability'            => null, // optional
        'order'                 => null, // optional
        'show_page_heading_tab' => true,
        'show_in_menu'          => true,
    );
        
    /**
     * Represents the structure of sub-menu page array.
     * 
     * @since       2.0.0
     * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
     * @remark      Not for the user.
     * @var         array Holds array structure of sub-menu page.
     * @static
     * @internal
     */ 
    protected static $_aStructure_SubMenuPageForUser = array(
        'type'                      => 'page', // this is used to compare with the link type.
        'title'                     => null, 
        'page_title'                => null,    // (optional) 3.3.0+ When the page title is different from the above 'title' argument, set this.
        'menu_title'                => null,    // (optional) 3.3.0+ When the menu title is different from the above 'title' argument, set this.
        'page_slug'                 => null, 
        'screen_icon'               => null, // this will become either href_icon_32x32 or screen_icon_id
        'capability'                => null, 
        'order'                     => null,
        'show_page_heading_tab'     => true, // if this is false, the page title won't be displayed in the page heading tab.
        'show_in_menu'              => true, // if this is false, the menu label will not be displayed in the sidebar menu.     
        'href_icon_32x32'           => null,
        'screen_icon_id'            => null,
        // 'show_menu' => null, <-- not sure what this was for.
        'show_page_title'           => null,
        'show_page_heading_tabs'    => null,
        'show_in_page_tabs'         => null,
        'in_page_tab_tag'           => null,
        'page_heading_tab_tag'      => null,
        'disabled'                  => null, // 3.5.10+ (boolean) If false, in the page heading navigation tab, the link will be disabled.
        'attributes'                => null, // 3.5.10+ (array) Applied to navigation tab element.
    );    
    
    /**
     * Builds the sidebar menu of the added pages.
     * 
     * @since       2.0.0
     * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
     * @internal
     */
    public function _replyToBuildMenu() {

        // If the root menu label is not set but the slug is set, 
        if ( $this->oProp->aRootMenu['fCreateRoot'] ) {
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
        uasort( $this->oProp->aPages, array( $this, '_sortByOrder' ) ); 
        
        // Set the default page, the first element.
        foreach ( $this->oProp->aPages as $aPage ) {
            
            if ( ! isset( $aPage['page_slug'] ) ) { 
                continue; 
            }
            $this->oProp->sDefaultPageSlug = $aPage['page_slug'];
            break;
            
        }
        
        // Register them.
        foreach ( $this->oProp->aPages as &$aSubMenuItem ) {
            
            // needs to be sanitized because there are hook filters applied to this array.
            $aSubMenuItem               = $this->_formatSubMenuItemArray( $aSubMenuItem ); 
            
            // store the page hook; this is same as the value stored in the global $page_hook or $hook_suffix variable. 
            $aSubMenuItem['_page_hook'] = $this->_registerSubMenuItem( $aSubMenuItem ); 
            
        }

        // After adding the sub menus, if the root menu is created, remove the page that is automatically created when registering the root menu.
        if ( $this->oProp->aRootMenu['fCreateRoot'] ) {
            remove_submenu_page( $this->oProp->aRootMenu['sPageSlug'], $this->oProp->aRootMenu['sPageSlug'] );
        }

        $this->oProp->_bBuiltMenu = true;
        
    }    
        
        /**
         * Registers the root menu page.
         * 
         * @since       2.0.0
         * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
         * @internal
         */ 
        private function _registerRootMenuPage() {
            $this->oProp->aRootMenu['_page_hook'] = add_menu_page(  
                $this->oProp->sClassName,               // Page title - will be invisible anyway
                $this->oProp->aRootMenu['sTitle'],      // Menu title - should be the root page title.
                $this->oProp->sCapability,              // Capability - access right
                $this->oProp->aRootMenu['sPageSlug'],   // Menu ID 
                '',                                     // Page content displaying function - the root page will be removed so no need to register a function.
                $this->oProp->aRootMenu['sIcon16x16'],  // icon path
                $this->oUtil->getElement( $this->oProp->aRootMenu, 'iPosition', null )  // menu position
            );
        }
        
        /**
         * Formats the sub-menu item arrays.
         * 
         * @since       3.0.0
         * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
         * @internal
         */
        private function _formatSubMenuItemArray( $aSubMenuItem ) {
            
            $aSubMenuItem = $this->oUtil->getAsArray( $aSubMenuItem );
            
            if ( isset( $aSubMenuItem['page_slug'] ) ) {
                return $this->_formatSubMenuPageArray( $aSubMenuItem );
            }
                
            if ( isset( $aSubMenuItem['href'] ) ) {
                return $this->_formatSubmenuLinkArray( $aSubMenuItem ); 
            }
                
            return array();
            
        }
        
    /**
     * Formats the given sub-menu link array.
     * 
     * @since       3.0.0
     * @since       3.3.1       Changed the scope to `protected` from `private` as the method is called from a different class.
     * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
     * @internal
     * @remark      Assumes the passed sub-menu link argument array is formatted to have required keys.
     */
    protected function _formatSubmenuLinkArray( array $aSubMenuLink ) {
        
        // If the set URL is not valid, return.
        if ( ! filter_var( $aSubMenuLink['href'], FILTER_VALIDATE_URL ) ) { 
            return array(); 
        }
        
        return array(  
                'capability'    => $this->oUtil->getElement( $aSubMenuLink, 'capability', $this->oProp->sCapability ),
                'order'         => isset( $aSubMenuLink['order'] ) && is_numeric( $aSubMenuLink['order'] )
                    ? $aSubMenuLink['order'] 
                    : count( $this->oProp->aPages ) + 10,
            )
            + $aSubMenuLink 
            + self::$_aStructure_SubMenuLinkForUser
            ;     
        
    }
        
    /**
     * Formats the given sub-menu page array.
     * @since       3.0.0
     * @since       3.3.1       Changed the scope to `protected` from `private` as the method is called from a different class.
     * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
     * @internal
     */
    protected function _formatSubMenuPageArray( array $aSubMenuPage ) {
        
        $aSubMenuPage = $aSubMenuPage 
            + array(
                'show_page_title'           => $this->oProp->bShowPageTitle, // boolean
                'show_page_heading_tabs'    => $this->oProp->bShowPageHeadingTabs, // boolean
                'show_in_page_tabs'         => $this->oProp->bShowInPageTabs, // boolean
                'in_page_tab_tag'           => $this->oProp->sInPageTabTag, // string
                'page_heading_tab_tag'      => $this->oProp->sPageHeadingTabTag, // string
            )       
            + self::$_aStructure_SubMenuPageForUser;

        $aSubMenuPage['screen_icon_id'] = trim( $aSubMenuPage['screen_icon_id'] );
        return array( 
                'href_icon_32x32'   => $this->oUtil->getResolvedSRC( $aSubMenuPage['screen_icon'], true ),
                'screen_icon_id'    => $this->oUtil->getAOrB(
                    in_array( $aSubMenuPage['screen_icon'], self::$_aScreenIconIDs ),
                    $aSubMenuPage['screen_icon'],
                    'generic'   // $_aScreenIconIDs is defined in the page class.
                ), 
                'capability'        => $this->oUtil->getElement( $aSubMenuPage, 'capability', $this->oProp->sCapability ),
                'order'             => $this->oUtil->getAOrB(
                    is_numeric( $aSubMenuPage['order'] ),
                    $aSubMenuPage['order'],
                    count( $this->oProp->aPages ) + 10
                ),
            )
            + $aSubMenuPage;
        
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
                }
                $this->oProp->aPageHooks[ $sPageSlug ] = $this->oUtil->getAOrB(
                    is_network_admin(),
                    $_sPageHook . '-network',
                    $_sPageHook
                );
                if ( $bShowInMenu ) {
                    return $_sPageHook;
                }
                
                // If the visibility option is false, remove the one just added from the sub-menu array
                $this->_removePageSubmenuItem( $sMenuSlug, $sMenuTitle, $sPageTitle, $sPageSlug );
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