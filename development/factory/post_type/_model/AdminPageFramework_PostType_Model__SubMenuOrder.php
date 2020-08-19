<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to modify sub-menu order.
 *
 * @since           3.7.4
 * @package         AdminPageFramework/Factory/PostType
 * @internal
 */
class AdminPageFramework_PostType_Model__SubMenuOrder extends AdminPageFramework_FrameworkUtility {

    /**
     * Stores a post type factory object.
     */
    public $oFactory;

    /**
     * Sets up hooks and properties.
     *
     * @internal
     */
    public function __construct( $oFactory ) {

        $this->oFactory = $oFactory;

        if ( ! $oFactory->oProp->bIsAdmin ) {
            return;
        }

        add_action(
            'admin_menu',
            array( $this, '_replyToSetSubMenuOrder' ),
            /**
             * Set a low priority to let WordPress insert sub-menu items of post types with the `show_in_menu` argument.
             * Also Admin Page Framework admin pages will add sub-menu page items before this.
             */
            200
        );

        add_action(
            'admin_menu',
            array( $this, 'sortAdminSubMenu' ),  // defined in the framework utility class.
            9999
        );


    }

    /**
     * @since       3.7.4
     * @callback    action      admin_menu
     */
    public function _replyToSetSubMenuOrder() {

        // Check the post type `show_ui` and other related UI arguments.
        $_bsShowInMeenu = $this->getShowInMenuPostTypeArgument( $this->oFactory->oProp->aPostTypeArgs );
        if ( ! $_bsShowInMeenu ) {
            return;
        }

        // If the user sets a menu slug to the 'show_in_menu' argument, use that.
        // It is used to set a custom post type sub-menu belong to another menu.
        $_sSubMenuSlug  = is_string( $_bsShowInMeenu )
            ? $_bsShowInMeenu
            : 'edit.php?post_type=' . $this->oFactory->oProp->sPostType;

        // Set the index to the framework specific global array for sorting.
        $this->_setSubMenuSlugForSorting( $_sSubMenuSlug );

        // Set the index to the `submenu` global array.
        $this->_setSubMenuItemIndex( $_sSubMenuSlug );

    }

        /**
         * @since       3.7.4
         */
        private function _setSubMenuSlugForSorting( $sSubMenuSlug ) {

            $GLOBALS[ '_apf_sub_menus_to_sort' ] = isset( $GLOBALS[ '_apf_sub_menus_to_sort' ] )
                ? $GLOBALS[ '_apf_sub_menus_to_sort' ]
                : array();
            $GLOBALS[ '_apf_sub_menus_to_sort' ][ $sSubMenuSlug ] = $sSubMenuSlug;

        }

        /**
         * @since       3.7.4
         */
        private function _setSubMenuItemIndex( $sSubMenuSlug ) {

            // Only if custom values are set, set them.
            $this->_setSubMenuIndexByLinksSlugs(
                $sSubMenuSlug,
                $this->_getPostTypeMenuLinkSlugs()
                + $this->oFactory->oProp->aTaxonomySubMenuOrder
            );

        }

            /**
             * @since       3.7.4
             * @return      array
             */
            private function _getPostTypeMenuLinkSlugs() {

                $_nSubMenuOrderManage = $this->getElement(
                    $this->oFactory->oProp->aPostTypeArgs,
                    'submenu_order_manage',
                    5 // default
                );

                $_bShowAddNew = $this->getElement(
                    $this->oFactory->oProp->aPostTypeArgs, // subject array
                    'show_submenu_add_new', // dimensional keys
                    true // default
                );
                $_nSubMenuOrderAddNew = $this->getElement(
                    $this->oFactory->oProp->aPostTypeArgs,
                    'submenu_order_addnew',
                    10  // default
                );

                // @remark      This is the partial link url set in the third element ( index of 2 ) in the third dimension of submenu global array element.
                // This is not the submenu slug.
                $_sLinkSlugManage = 'edit.php?post_type=' . $this->oFactory->oProp->sPostType;

                $_aLinkSlugs = array(
                    $_sLinkSlugManage => $_nSubMenuOrderManage,
                    'post-new.php?post_type=' . $this->oFactory->oProp->sPostType => $_nSubMenuOrderAddNew,
                );

                // If the user does not set a custom value, unset it
                if ( 5 == $_nSubMenuOrderManage ) {
                    unset( $_aLinkSlugs[ $_sLinkSlugManage ] );
                }

                // If the user does not want to show the Add New sub menu, no need to change the order.
                if ( ! $_bShowAddNew || 10 == $_nSubMenuOrderAddNew ) {
                    unset( $_aLinkSlugs[ 'post-new.php?post_type=' . $this->oFactory->oProp->sPostType ] );
                }

                return $_aLinkSlugs;

            }

        /**
         * @return      void
         * @since       3.7.4
         * @param       string      $sSubMenuSlug       The first dimensional key of the `$submenu` global array.
         * @param       array       $aLinkSlugs         An array holding key-value pairs of an order and a link slug.
         * `
         * array(
         *      10 => 'edit.php?post_type=my_post_type_slug'
         * )
         * `
         */
        private function _setSubMenuIndexByLinksSlugs( $sSubMenuSlug, array $aLinkSlugs ) {

            foreach( $this->getElementAsArray( $GLOBALS, array( 'submenu', $sSubMenuSlug ) ) as $_nIndex => $_aSubMenuItem ) {

                foreach( $aLinkSlugs as $_sLinkSlug => $_nOrder ) {

                    $_bIsSet = $this->_setSubMenuIndexByLinksSlug( $sSubMenuSlug, $_nIndex, $_aSubMenuItem, $_sLinkSlug, $_nOrder );

                    // If set, no longer needed to parse.
                    if ( $_bIsSet ) {
                        unset( $aLinkSlugs[ $_sLinkSlug ] );
                    }

                }

            }

        }
            /**
             * @since       3.7.4
             * @return      boolean     true if set; otherwise. false.
             * @param       string      $sSubMenuSlug       The slug of the sub-menu to be modified. It is the array key of the first dimension of the `submenu` global array.
             * @param       numeric     $nIndex             The system-set index number to the sub-menu array.
             * @param       array       $aSubMenuItem       An array holding the sub-menu item.
             * @param       string      $sLinkSlug          The link slug (partial url) of the target.
             * @pram        numeric     $nOrder             The position of the sub-menu to change.
             */
            private function _setSubMenuIndexByLinksSlug( $sSubMenuSlug, $nIndex, $aSubMenuItem, $sLinkSlug, $nOrder ) {

                // The third item is the link slug.
                if ( ! isset( $aSubMenuItem[ 2 ] ) ) {
                    return false;
                }
                if ( $aSubMenuItem[ 2 ] !== $sLinkSlug ) {
                    return false;
                }

                // Remove the existent sub-menu item of the index.
                unset( $GLOBALS[ 'submenu' ][ $sSubMenuSlug ][ $nIndex ] );

                // Get a new index and assign it.
                $_nNewIndex = $this->getUnusedNumericIndex(
                    $this->getElementAsArray( $GLOBALS, array( 'submenu', $sSubMenuSlug ) ),
                    $nOrder
                );
                $GLOBALS[ 'submenu' ][ $sSubMenuSlug ][ $_nNewIndex ] = $aSubMenuItem;

                return true;

            }

}
