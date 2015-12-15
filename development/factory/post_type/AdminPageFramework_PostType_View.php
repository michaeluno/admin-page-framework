<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods of views for the post type factory class.
 * 
 * Those methods are internal and deal with printing outputs.
 * 
 * @abstract
 * @since       3.0.4
 * @package     AdminPageFramework
 * @subpackage  PostType
 */
abstract class AdminPageFramework_PostType_View extends AdminPageFramework_PostType_Model {    

    /**
     * Sets up hooks.
     * 
     * @internal    
     * @remark      Make sure to call the parent construct first as the factory router need to set up sub-class objects.
     */
    function __construct( $oProp ) {
        
        parent::__construct( $oProp );
                        
        if ( $this->_isInThePage() ) {     
    
            // Table filters
            add_action( 'restrict_manage_posts', array( $this, '_replyToAddAuthorTableFilter' ) );
            add_action( 'restrict_manage_posts', array( $this, '_replyToAddTaxonomyTableFilter' ) );
            add_filter( 'parse_query', array( $this, '_replyToGetTableFilterQueryForTaxonomies' ) );
            
            // Add an warning icon to the tag unit type's action link.
            add_filter( 
                'post_row_actions',
                array( $this, '_replyToModifyActionLinks' ), 
                10, 
                2 
            );            
            
            // Style
            add_action( 'admin_head', array( $this, '_replyToPrintStyle' ) );

            // 3.5.10+ Menu 
            add_action( 'admin_menu', array( $this, '_replyToRemoveAddNewSidebarMenu' ) );
            
        }     
        
        // Front-end
        add_action( 'the_content', array( $this, '_replyToFilterPostTypeContent' ) );
        
    }

        /**
         * Removes the sidebar item of "Add New" if the 'show_menu_add_new' argument is set.
         * 
         * @internal
         * @since       3.5.10
         * @return      void
         * @callback    action      admin_menu
         */
        public function _replyToRemoveAddNewSidebarMenu() {
            
            if ( 
                $this->oUtil->getElement(
                    $this->oProp->aPostTypeArgs, // subject array
                    'show_submenu_add_new', // dimensional keys
                    true // default
                )
            ) {
                return;
            }
            
            // Remove the Add New menu
            $_bsShowInMenu = $this->oUtil->getShowInMenuPostTypeArgument( $this->oProp->aPostTypeArgs );
            $this->_removeAddNewSidebarSubMenu(
                is_string( $_bsShowInMenu )
                    ? $_bsShowInMenu
                    : 'edit.php?post_type=' . $this->oProp->sPostType,
                $this->oProp->sPostType
            );
            
        }    
  
            /**
             * Removes the sidebar menu item of "Add New .." of the post type.
             * 
             * @internal
             * @since       3.5.10
             * @return      void
             */
            private function _removeAddNewSidebarSubMenu( $sMenuKey, $sPostTypeSlug ) {

                // Remove the default post type menu item.
                if ( ! isset( $GLOBALS['submenu'][ $sMenuKey ] ) ) {
                    // logged-in users of an insufficient access level don't have the menu to be registered.
                    return; 
                } 
                
                foreach ( $GLOBALS['submenu'][ $sMenuKey ] as $_iIndex => $_aSubMenu ) {
                                
                    if ( ! isset( $_aSubMenu[ 2 ] ) ) { 
                        continue; 
                    }
                    
                    // Remove the default Add New entry.
                    if ( 'post-new.php?post_type=' . $sPostTypeSlug === $_aSubMenu[ 2 ] ) {
                        unset( $GLOBALS['submenu'][ $sMenuKey ][ $_iIndex ] );
                        break;
                    }
                    
                }
                
            }
        
    /**
     * Modifies the action links for the post listing table.
     * @callback    filter      post_row_actions
     * @since       3.7.3
     */ 
    public function _replyToModifyActionLinks( $aActionLinks, $oPost )  {
        
        if ( $oPost->post_type !== $this->oProp->sPostType ){
            return $aActionLinks;
        }        

        return $this->oUtil->addAndApplyFilters(
            $this, 
            "action_links_{$this->oProp->sPostType}", 
            $aActionLinks,
            $oPost
        );    
        
    }
    
    /**
     * Adds a drop-down list to filter posts by author, placed above the post type listing table.
     * 
     * @internal
     * @uses        wp_dropdown_users
     * @callback    filter      restrict_manage_posts
     */ 
    public function _replyToAddAuthorTableFilter() {
        
        if ( ! $this->oProp->bEnableAuthorTableFileter ) { 
            return; 
        }
        
        if ( 
            ! ( isset( $_GET[ 'post_type' ] ) && post_type_exists( $_GET[ 'post_type' ] ) 
            && in_array( strtolower( $_GET[ 'post_type' ] ), array( $this->oProp->sPostType ) ) ) 
        ) {
            return;
        }
        
        wp_dropdown_users( 
            array(
                'show_option_all'   => $this->oMsg->get( 'show_all_authors' ),
                'show_option_none'  => false,
                'name'              => 'author',
                'selected'          => ! empty( $_GET[ 'author' ] ) 
                    ? $_GET[ 'author' ] 
                    : 0,
                'include_selected'  => false,
            )
        );
            
    }
    
    /**
     * Adds drop-down lists to filter posts by added taxonomies, placed above the post type listing table.
     * 
     * @internal
     */ 
    public function _replyToAddTaxonomyTableFilter() {
        
        if ( $GLOBALS[ 'typenow' ] != $this->oProp->sPostType ) { 
            return; 
        }
        
        // If there is no post added to the post type, do nothing.
        $_oPostCount = wp_count_posts( $this->oProp->sPostType );
        if ( 0 == $_oPostCount->publish + $_oPostCount->future + $_oPostCount->draft + $_oPostCount->pending + $_oPostCount->private + $_oPostCount->trash ) {
            return;
        }
        
        foreach ( get_object_taxonomies( $GLOBALS[ 'typenow' ] ) as $_sTaxonomySulg ) {
            
            if ( ! in_array( $_sTaxonomySulg, $this->oProp->aTaxonomyTableFilters ) ) {
                continue;
            }
            
            $_oTaxonomy = get_taxonomy( $_sTaxonomySulg );
 
            // If there is no added term, skip.
            if ( 0 == wp_count_terms( $_oTaxonomy->name ) ) {
                continue;             
            }

            // Echo the drop down list based on the passed array argument.
            wp_dropdown_categories( 
                array(
                    'show_option_all'   => $this->oMsg->get( 'show_all' ) . ' ' . $_oTaxonomy->label,
                    'taxonomy'          => $_sTaxonomySulg,
                    'name'              => $_oTaxonomy->name,
                    'orderby'           => 'name',
                    'selected'          => intval( isset( $_GET[ $_sTaxonomySulg ] ) ),
                    'hierarchical'      => $_oTaxonomy->hierarchical,
                    'show_count'        => true,
                    'hide_empty'        => false,
                    'hide_if_empty'     => false,
                    'echo'              => true, // print the output
                ) 
            );
            
        }
    }
    /**
     * Returns a query object based on the taxonomies belongs to the post type.
     * 
     * @internal
     */
    public function _replyToGetTableFilterQueryForTaxonomies( $oQuery=null ) {
        
        if ( 'edit.php' != $this->oProp->sPageNow ) { 
            return $oQuery; 
        }
        
        if ( ! isset( $GLOBALS[ 'typenow' ] ) ) { 
            return $oQuery; 
        }

        foreach ( get_object_taxonomies( $GLOBALS[ 'typenow' ] ) as $sTaxonomySlug ) {
            
            if ( ! in_array( $sTaxonomySlug, $this->oProp->aTaxonomyTableFilters ) ) { 
                continue; 
            }
            
            $sVar = &$oQuery->query_vars[ $sTaxonomySlug ];
            if ( ! isset( $sVar ) ) { 
                continue; 
            }
            
            $oTerm = get_term_by( 'id', $sVar, $sTaxonomySlug );
            if ( is_object( $oTerm ) ) {
                $sVar = $oTerm->slug;
            }

        }
        
        return $oQuery;
        
    }
    
    
    /**
     * Prints the script.
     * @internal
     * @return      void
     */
    public function _replyToPrintStyle() {
        
        if ( $this->oUtil->getCurrentPostType() !== $this->oProp->sPostType ) {
            return;
        }

        // If the screen icon url is specified
        if ( isset( $this->oProp->aPostTypeArgs[ 'screen_icon' ] ) && $this->oProp->aPostTypeArgs[ 'screen_icon' ] ) {
            $this->oProp->sStyle .= $this->_getStylesForPostTypeScreenIcon( $this->oProp->aPostTypeArgs[ 'screen_icon' ] );
        }
            
        $this->oProp->sStyle = $this->oUtil->addAndApplyFilters( $this, "style_{$this->oProp->sClassName}", $this->oProp->sStyle );
        
        // Print out the filtered styles.
        if ( ! empty( $this->oProp->sStyle ) ) {
            echo "<style type='text/css' id='admin-page-framework-style-post-type'>" 
                    . $this->oProp->sStyle
                . "</style>";     
        }
        
    }
        /**
         * Sets the given screen icon to the post type screen icon.
         * 
         * @since       2.1.3
         * @since       2.1.6     The $sSRC parameter can accept file path.
         * @internal
         */
        private function _getStylesForPostTypeScreenIcon( $sSRC ) {
            
            $sNone = 'none';
            $sSRC  = $this->oUtil->getResolvedSRC( $sSRC );
            return <<<CSSRULES
#post-body-content {
    margin-bottom: 10px;
}
#edit-slug-box {
    display: {$sNone};
}
#icon-edit.icon32.icon32-posts-{$this->oProp->sPostType} {
    background: url('{$sSRC}') no-repeat;
    background-size: 32px 32px;
}     
CSSRULES;
            
        }    
    
    /**
     * Filters the post type post content.
     * 
     * This method is called in the same timing of the content_{instantiated class name.}. This is shorthand for it.
     * 
     * @remark      This class should be overridden in the extended class.
     * @since       3.1.5
     */
    public function content( $sContent ) { return $sContent; }
    
    /**
     * Filters the post type post content.
     * 
     * @since       3.1.5
     * @internal
     * @callback    filter      the_content
     * @return      string
     */
    public function _replyToFilterPostTypeContent( $sContent ) {
        
        if ( ! is_singular() ) {
            return $sContent;
        }
        if ( ! is_main_query() ) {
            return $sContent;
        }
        global $post;
        if ( $this->oProp->sPostType !== $post->post_type ) {
            return $sContent;
        }
    
        return $this->oUtil->addAndApplyFilters(
            $this, 
            "content_{$this->oProp->sClassName}", 
            $this->content( $sContent )
        );    
        
    }
    
}