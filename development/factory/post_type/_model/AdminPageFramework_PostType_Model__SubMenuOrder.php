<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to modify sub-menu order.
 * 
 * @since           3.7.4
 * @package         AdminPageFramework
 * @subpackage      PostType
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
        
        add_action( 'admin_menu', array( $this, '_replyToSetSubMenuOrder' ), 1 );
        
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
        if ( ! $this->isPostTypeAdminUIVisible( $this->oFactory->oProp->aPostTypeArgs ) ) {
            return;
        }
        
        $_sSubMenuSlug  = 'edit.php?post_type=' . $this->oFactory->oProp->sPostType;
        
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

                $_aLinkSlugs = array(
                    'edit.php?post_type=' . $this->oFactory->oProp->sPostType     => $_nSubMenuOrderManage,
                    'post-new.php?post_type=' . $this->oFactory->oProp->sPostType => $_nSubMenuOrderAddNew,
                );
                
                // If the user does not set a custom value, unset it
                if ( 5 == $_nSubMenuOrderManage ) {
                    unset( $_aLinkSlugs[ 'edit.php?post_type=' . $this->oFactory->oProp->sPostType ] );
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
            
            foreach( ( array ) $GLOBALS[ 'submenu' ][ $sSubMenuSlug ] as $_nIndex => $_aSubMenuItem ) {
                
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
                $_nNewIndex = $this->getUnusedNumericIndex( $GLOBALS[ 'submenu' ][ $sSubMenuSlug ], $nOrder );
                $GLOBALS[ 'submenu' ][ $sSubMenuSlug ][ $_nNewIndex ] = $aSubMenuItem;

                return true;
            }                
   
}