<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to register an admin sidebar menu item.
 *
 * @since           3.7.4
 * @extends         AdminPageFramework_Controller_Page
 * @package         AdminPageFramework/Factory/AdminPage/Model
 * @internal
 */
class AdminPageFramework_Model_Menu__RegisterMenu extends AdminPageFramework_FrameworkUtility {

    /**
     * Stores a factory object.
     */
    public $oFactory;

    /**
     * Sets up hooks
     *
     * @internal
     */
    public function __construct( $oFactory, $sActionHook='admin_menu' ) {

        $this->oFactory = $oFactory;
        add_action(
            $sActionHook,
            array( $this, '_replyToRegisterRootMenu' ),
            98      // this is below the value set in the `AdminPageFramework_Property_page_meta_box` class.
        );
        add_action(
            $sActionHook,
            array( $this, '_replyToRegisterSubMenuItems' ),
            99
        );
        add_action(
            $sActionHook,
            array( $this, '_replyToRemoveRootMenuPage' ),
            100
        );

        add_action(
            $sActionHook,
            array( $this, 'sortAdminSubMenu' ), // defined in the framework utility class.
            9999
        );

        // Stores sub-menu items to sort.
        $GLOBALS[ '_apf_sub_menus_to_sort' ] = isset( $GLOBALS[ '_apf_sub_menus_to_sort' ] )
            ? $GLOBALS[ '_apf_sub_menus_to_sort' ]
            : array();

    }


    /**
     * Builds the sidebar menu of the added pages.
     *
     * @since       2.0.0
     * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
     * @since       3.7.4       Moved from `AdminPageFramework_Model_Menu`. Changed the name from `_replyToBuildMenu()`.
     * @since       3.8.3       Renamed from `_replyToRegisterMenu`.
     * @callback    action      admin_menu
     * @internal
     * @uses        remove_submenu_page
     */
    public function _replyToRegisterRootMenu() {

        // If the root menu label is not set but the slug is set,
        if ( ! $this->oFactory->oProp->aRootMenu[ 'fCreateRoot' ] ) {
            return;
        }
        $this->_registerRootMenuPage();

    }
        /**
         * Registers the root menu page.
         *
         * @since       2.0.0
         * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
         * @internal
         * @uses        add_menu_page
         */
        private function _registerRootMenuPage() {

            if ( $this->oFactory->oProp->bIsAdminAjax ) {
                $this->oFactory->oProp->aRootMenu[ '_page_hook' ] = $this->oFactory->oProp->sClassName;
                return;
            }

            $this->oFactory->oProp->aRootMenu[ '_page_hook' ] = add_menu_page(
                $this->oFactory->oProp->sClassName,                 // Page title - will be invisible anyway
                $this->oFactory->oProp->aRootMenu[ 'sTitle' ],      // Menu title - should be the root page title.
                $this->oFactory->oProp->sCapability,                // Capability - access right
                $this->oFactory->oProp->aRootMenu[ 'sPageSlug' ],   // Menu ID
                '',                                       // Callback function for the page content output - the root page will be removed so no need to register a function.
                $this->oFactory->oProp->aRootMenu[ 'sIcon16x16' ],  // icon path
                $this->getElement(
                    $this->oFactory->oProp->aRootMenu,
                    'iPosition',
                    null
                )  // menu position
            );

        }

    /**
     * Registers sub-menu items.
     *
     * @since       3.8.3
     * @callback    action      admin_menu
     * @internal
     * @uses        remove_submenu_page
     */
    public function _replyToRegisterSubMenuItems() {

        // Let external scripts add sub-menu pages.
        $_aPages = $this->addAndApplyFilter(
            $this->oFactory,    // caller object
            "pages_{$this->oFactory->oProp->sClassName}",   // filter
            $this->oFactory->oProp->aPages  // arguments
        );

        // Set the default page, the first element.
        $this->oFactory->oProp->sDefaultPageSlug = $this->_getDefaultPageSlug( $_aPages );

        // Format the `$aPages` property and register the pages.
        $_iParsedIndex    = 0;
        $_aFormattedPages = array();
        foreach( $_aPages as $_aSubMenuItem ) {

            // needs to be sanitized because there are hook filters applied to this array.
            $_oFormatter = new AdminPageFramework_Format_SubMenuItem(
                $_aSubMenuItem,
                $this->oFactory,
                ++$_iParsedIndex
            );
            $_aSubMenuItem = $_oFormatter->get();

            // store the page hook; this is same as the value stored in the global $page_hook or $hook_suffix variable.
            $_aSubMenuItem[ '_page_hook' ] = $this->_registerSubMenuItem( $_aSubMenuItem );
            $_sKey = isset( $_aSubMenuItem[ 'href' ] )
                ? $_aSubMenuItem[ 'href' ]
                : $_aSubMenuItem[ 'page_slug' ];
            $_aFormattedPages[ $_sKey ] = $_aSubMenuItem;

        }
        $this->oFactory->oProp->aPages = $_aFormattedPages;

    }

        /**
         * Sets the default page.
         *
         * The first item of the added pages.
         *
         * @internal
         * @since       3.6.0
         * @since       3.8.3       Changed it to receive a pages definition array and return the found page slug.
         * @since       3.8.3       Renamed from `_setDefaultPage`.
         * @return      string
         */
        private function _getDefaultPageSlug( array $aPages ) {

            foreach( $aPages as $_aPage ) {
                if ( ! isset( $_aPage[ 'page_slug' ] ) ) {
                    continue;
                }
                return $_aPage[ 'page_slug' ];
            }
            return '';

        }

        /**
         * Registers the sub-menu item.
         *
         * @since       2.0.0
         * @since       3.0.0       Changed the name from `registerSubMenuPage()`.
         * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
         * @remark      The sub menu page slug should be unique because `add_submenu_page()` can add one callback per page slug.
         * @remark      Assumes the argument array is already formatted.
         * @internal
         * @return      string      The page hook if the page is added.
         */
        private function _registerSubMenuItem( array $aArgs ) {

            if ( ! current_user_can( $aArgs[ 'capability' ] ) ) {
                return '';
            }

            $_sRootPageSlug = $this->oFactory->oProp->aRootMenu[ 'sPageSlug' ];
            $_sRootMenuSlug = $this->_getRootMenuSlug( $_sRootPageSlug );

            // It is possible that the page_slug key is not set if the user uses a method like setPageHeadingTabsVisibility() prior to addSubMenuItam().
            if ( 'page' === $aArgs[ 'type' ] ) {
                return $this->_addPageSubmenuItem(
                    $_sRootPageSlug,
                    $_sRootMenuSlug,
                    $aArgs[ 'page_slug' ],
                    $this->getElement( $aArgs, 'page_title', $aArgs[ 'title' ] ),
                    $this->getElement( $aArgs, 'menu_title', $aArgs[ 'title' ] ),
                    $aArgs[ 'capability' ],
                    $aArgs[ 'show_in_menu' ],
                    $aArgs[ 'order' ]
                );
            }
            if ( 'link' === $aArgs[ 'type' ] ) {
                return $this->_addLinkSubmenuItem(
                    $_sRootMenuSlug,
                    $aArgs[ 'title' ],
                    $aArgs[ 'capability' ],
                    $aArgs[ 'href' ],
                    $aArgs[ 'show_in_menu' ],
                    $aArgs[ 'order' ]
                );
            }

            return '';

        }
            /**
             * @remark      To be compatible with `add_submenu_page()`
             * @since       3.7.10
             * @return      string
             * @uses        plugin_basename
             */
            private function _getRootMenuSlug( $sRootPageSlug ) {

                if ( isset( self::$_aRootMenuSlugCache[ $sRootPageSlug ] ) ) {
                    return self::$_aRootMenuSlugCache[ $sRootPageSlug ];
                }
                self::$_aRootMenuSlugCache[ $sRootPageSlug ] = plugin_basename( $sRootPageSlug );
                return self::$_aRootMenuSlugCache[ $sRootPageSlug ];

            }
                /**
                 * @since       3.7.10
                 */
                static private $_aRootMenuSlugCache = array();

            /**
             * Adds a page sub-menu item.
             *
             * @since       3.3.0
             * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
             * @since       3.7.4       Added the `$nOrder` parameter.
             * @return      string      The page hook of the added page.
             */
            private function _addPageSubmenuItem( $sRootPageSlug, $sMenuSlug, $sPageSlug, $sPageTitle, $sMenuTitle, $sCapability, $bShowInMenu, $nOrder ) {

                if ( ! $sPageSlug ) {
                    return '';
                }
                $_sPageHook = $this->___addSubMenuPage(
                    $sRootPageSlug,         // the root (parent) page slug
                    $sPageTitle,            // page title
                    $sMenuTitle,            // menu title
                    $sCapability,           // capability
                    $sPageSlug              // menu slug
                );

//                $_sPageHook = add_submenu_page(
//                    $sRootPageSlug,         // the root (parent) page slug
//                    $sPageTitle,            // page title
//                    $sMenuTitle,            // menu title
//                    $sCapability,           // capability
//                    $sPageSlug,             // menu slug
//                    array( $this->oFactory, '_replyToRenderPage' )  // callback 3.7.10+
//                );
                $this->_setPageHooks( $_sPageHook, $sPageSlug );

                // Now we are going to remove the added sub-menu from the WordPress global variable.
                $_nSubMenuPageIndex = $this->_getSubMenuPageIndex(
                    $sMenuSlug,
                    $sMenuTitle,
                    $sPageTitle,
                    $sPageSlug
                );
                if ( null === $_nSubMenuPageIndex ) {
                    return $_sPageHook;
                }

                $_aRemovedMenuItem = $this->_removePageSubmenuItem( $_nSubMenuPageIndex, $sMenuSlug, $sPageSlug, $sMenuTitle );

                // If the visibility option is `false`, remove the one just added from the sub-menu global array
                if ( ! $bShowInMenu && ! $this->_isCurrentPage( $sPageSlug ) ) {
                    return $_sPageHook;
                }

                // Set the order index in the element of the `submenu` global array.
                $this->_setSubMenuPageByIndex(
                    $nOrder,                // user-set order
                    $_aRemovedMenuItem,     // will be reassign with a new index
                    $sMenuSlug
                );

                // Update the property for sorting.
                $GLOBALS[ '_apf_sub_menus_to_sort' ][ $sMenuSlug ] = $sMenuSlug;
                return $_sPageHook;

            }

                /**
                 * @param       string $sRootPageSlug
                 * @param       string $sPageTitle
                 * @param       string $sMenuTitle
                 * @param       string $sCapability
                 * @param       string $sPageSlug
                 * @since       3.8.14
                 * @return      false|string
                 * @uses        add_submenu_page
                 */
                private function ___addSubMenuPage(
                    $sRootPageSlug,         // the root (parent) page slug
                    $sPageTitle,            // page title
                    $sMenuTitle,            // menu title
                    $sCapability,           // capability
                    $sPageSlug              // menu slug
                ) {
                    if ( $this->oFactory->oProp->bIsAdminAjax ) {
                        return $sPageSlug;
                    }
                    return add_submenu_page(
                        $sRootPageSlug,         // the root (parent) page slug
                        $sPageTitle,            // page title
                        $sMenuTitle,            // menu title
                        $sCapability,           // capability
                        $sPageSlug,             // menu slug
                        array( $this->oFactory, '_replyToRenderPage' )  // callback 3.7.10+
                    );
                }
                /**
                 * Checks whether the given page slug is of the currently loading page.
                 *
                 * Used to decide whether the menu item of the current hidden page should be shown.
                 * Currently, the menu will be displayed even it is a hidden menu page item if it is the currently loading page.
                 * @return      boolean
                 * @since       3.7.4
                 */
                private function _isCurrentPage( $sPageSlug ) {
                    return $sPageSlug === $this->getElement( $this->oFactory->oProp->aQuery, 'page' );
                }

                /**
                 * Sets up hooks for the page.
                 * @since       3.7.4
                 */
                private function _setPageHooks( $sPageHook, $sPageSlug ) {

                    // Ensure only it is added one time per page slug.
                    if ( isset( $this->oFactory->oProp->aPageHooks[ $sPageHook ] ) ) {
                        return;
                    }

                    /**
                     * Give a lower priority as the page meta box class also hooks the current_screen to register form elements.
                     * When the validation callback is triggered, their form registration should be done already. So this hook should be loaded later than them.
                     * @since       3.4.1
                     */
                    add_action(
                        'current_screen',
                        array( $this->oFactory, "load_pre_" . $sPageSlug ),
                        20
                    );
                    /**
                     * For Ajax calls.
                     * @since       3.8.14
                     * @since       3.8.22  Change the action to `pseudo_current_screen_{$sPageSlug}` from `pseudo_current_screen` to avoid overload in `admin-ajax.php`.
                     */
                    add_action( "pseudo_current_screen_{$sPageSlug}", array( $this, '_replyToLoadPageForAjax' ), 20 ); // 3.8.22

                    /**
                     * It is possible that an in-page tab is added during the above hooks and the current page is the default tab without the tab GET query key in the url.
                     * Set a low priority because the user may add in-page tabs in their callback method of this action hook.
                     * @since       3.6.3
                     */
                    add_action( "load_" . $sPageSlug, array( $this->oFactory, '_replyToFinalizeInPageTabs' ), 9999 );

                    // 3.6.3+
                    add_action( "load_after_" . $sPageSlug, array( $this->oFactory, '_replyToEnqueuePageAssets' ) );
                    add_action( "load_after_" . $sPageSlug, array( $this->oFactory, '_replyToEnablePageMetaBoxes' ) );  // 3.7.10+

                    $this->oFactory->oProp->aPageHooks[ $sPageSlug ] = $this->getAOrB(
                        is_network_admin(),
                        $sPageHook . '-network',
                        $sPageHook
                    );

                }


                /**
                 * @since       3.7.4
                 * @return      void
                 * @param       numeric     $$nOrder            A user set order (menu position, index).
                 * @param       array       $aSubMenuItem       The sub menu item array set in the global `$submenu` array.
                 */
                private function _setSubMenuPageByIndex( $nOrder, $aSubMenuItem, $sMenuSlug ) {

                    $_nNewIndex = $this->getUnusedNumericIndex(
                        $this->getElementAsArray( $GLOBALS, array( 'submenu', $sMenuSlug ) ), // subject array to parser
                        $nOrder,    // a desired menu position
                        5           // offset
                    );

                    $GLOBALS[ 'submenu' ][ $sMenuSlug ][ $_nNewIndex ] = $aSubMenuItem;

                }

                /**
                 * Finds the sub-menu page index of the given menu slug by menu and page title.
                 * @return      numeric|null
                 */
                private function _getSubMenuPageIndex( $sMenuSlug, $sMenuTitle, $sPageTitle, $sPageSlug ) {

                    foreach( $this->getElementAsArray( $GLOBALS, array( 'submenu', $sMenuSlug ) ) as $_iIndex => $_aSubMenu ) {

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
                        return $_iIndex;

                    }
                    return null;

                }

                /**
                 * Removes a page sub-menu item.
                 *
                 * @since       3.3.0
                 * @since       3.1.1       Moved from `AdminPageFramework_Menu`. Chagned the return type.
                 * @return      array       removed menu item.
                 */
                private function _removePageSubmenuItem( $nSubMenuPageIndex, $sMenuSlug, $sPageSlug, $sMenuTitle ){

                    $_aRemovedMenuItem = $this->_removePageSubMenuItemByIndex(
                        $nSubMenuPageIndex,
                        $sMenuSlug,
                        $sPageSlug
                    );

                    // The page title in the browser window title bar will miss the page title as this is left as it is.
                    $this->oFactory->oProp->aHiddenPages[ $sPageSlug ] = $sMenuTitle;
                    // @deprecated 3.7.6 - the below function caused the page title to get doubled in the `<title>` tag.
                    /* add_filter(
                        'admin_title',
                        array( $this, '_replyToFixPageTitleForHiddenPages' ),
                        10,
                        2
                    ); */

                    return $_aRemovedMenuItem;

                }

                    /**
                     * A callback function for the admin_title filter to fix the page title for hidden pages.
                     *
                     * @since       2.1.4
                     * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
                     * @internal
                     * @callback    filter      admin_title
                     * return       string
                     * @deprecated  3.7.6       Not sure what this was for. This caused the page title in the `<title>` tag to be doubled.
                     */
                    /* public function _replyToFixPageTitleForHiddenPages( $sAdminTitle, $sPageTitle ) {

                        if ( isset( $_GET[ 'page' ], $this->oFactory->oProp->aHiddenPages[ $_GET[ 'page' ] ] ) ) { // sanitization unnecessary
                            return $this->oFactory->oProp->aHiddenPages[ $this->oFactory->oUtil->getHTTPQueryGET( 'page', '' ) ] . $sAdminTitle;
                        }
                        return $sAdminTitle;
                    }   */

                    /**
                     * Remove the specified item from the menu.
                     *
                     * If the current page is being accessed, do not remove it from the menu.
                     *
                     * @since       3.5.3
                     * @since       3.7.4       Changed the parameter structure. Changed the return value.
                     * @return      array       the removed item.
                     * @internal
                     */
                    private function _removePageSubMenuItemByIndex( $_iIndex, $sMenuSlug, $sPageSlug ) {

                        // Extract the contents.
                        $_aSubMenuItem = $this->getElementAsArray(
                            $GLOBALS,
                            array( 'submenu', $sMenuSlug, $_iIndex )
                        );

                        unset( $GLOBALS[ 'submenu' ][ $sMenuSlug ][ $_iIndex ] );
                        return $_aSubMenuItem;

                    }
            /**
             * Adds a link sub-menu item.
             *
             * @since       3.3.0
             * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
             * @since       3.5.3       Added the `$bShowInMenu` parameter.
             * @since       3.7.4       Added the `$nOrder` parameter.
             * @return      void
             */
            private function _addLinkSubmenuItem( $sMenuSlug, $sTitle, $sCapability, $sHref, $bShowInMenu, $nOrder ) {

                if ( ! $bShowInMenu ) {
                    return;
                }

                $_aSubMenuItems = $this->getElementAsArray(
                    $GLOBALS,
                    array( 'submenu', $sMenuSlug )
                );

                $_nIndex = $this->getUnusedNumericIndex(
                    $_aSubMenuItems,
                    $nOrder,
                    5   // offset
                );
                $_aSubMenuItems[ $_nIndex ] = array(
                    $sTitle,        // 0
                    $sCapability,   // 1
                    $sHref,         // 2
                );
                $GLOBALS[ 'submenu' ][ $sMenuSlug ] = $_aSubMenuItems;

                // Update the property for sorting.
                $GLOBALS[ '_apf_sub_menus_to_sort' ][ $sMenuSlug ] = $sMenuSlug;

            }

    /**
     * For loading admin-ajax.php, allow only items that add a page that matches currently loading page.
     * @since   3.8.22
     * @internal
     * @callback    action  pseudo_current_screen_{$sPageSlug}
     */
    public function _replyToLoadPageForAjax() {
        $_sCurrentPageSlug = $this->oFactory->oProp->getCurrentPageSlugIfAdded();
        if ( ! $_sCurrentPageSlug ) {
            return;
        }
        $_sCurrentTabSlug  = $this->oFactory->oProp->getCurrentInPageTabSlugIfAdded( $_sCurrentPageSlug );
        if (
            ! empty( $this->oFactory->oProp->aInPageTabs )      // it means at least one in-page tab is added
            && ! $_sCurrentTabSlug // the current tab could not be retrieved
        ) {
            return;
        }
        call_user_func_array( array( $this->oFactory, "load_pre_" . $_sCurrentPageSlug ), array() );
    }

    /**
     * Removes the root menu page which gets automatically added by the system.
     *
     * @since       3.8.3
     * @callback    action      admin_menu
     * @internal
     */
    public function _replyToRemoveRootMenuPage() {

        // After adding the sub menus, if the root menu is created, remove the page that is automatically created when registering the root menu.
        if ( ! $this->oFactory->oProp->aRootMenu[ 'fCreateRoot' ] ) {
            return;
        }
        if ( $this->oFactory->oProp->bIsAdminAjax ) {
            return;
        }

        /**
         * Namespace slugs have backslashes but the registered slugs backslashes are all converted to forward-slashes.
         * @since       3.5.16
         */
        $_sMenuSlug =             str_replace(
            '\\',
            '/',
            $this->oFactory->oProp->aRootMenu[ 'sPageSlug' ]
        );

        remove_submenu_page(
            $_sMenuSlug, // parent menu slug
            $_sMenuSlug // sub-menu slug
        );

    }

}
