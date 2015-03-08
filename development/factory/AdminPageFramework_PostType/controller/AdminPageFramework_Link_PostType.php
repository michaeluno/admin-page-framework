<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for HTML link elements for custom post types.
 *
 * @since       2.0.0
 * @extends     AdminPageFramework_Utility
 * @package     AdminPageFramework
 * @subpackage  Link
 * @internal
 */
class AdminPageFramework_Link_PostType extends AdminPageFramework_Link_Base {
        
    /**
     * Sets up hooks and properties.
     * 
     */
    public function __construct( $oProp, $oMsg=null ) {
        
        parent::__construct( $oProp, $oMsg );
                       
        // For post type posts listing table page ( edit.php )
        if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->oProp->sPostType ) {
            add_action( 
                'get_edit_post_link', 
                array( $this, '_replyToAddPostTypeQueryInEditPostLink' ), 
                10, 
                3 
           );
        }        
    }

    /**
     * Adds the post type link in the title cell of the plugin listing table in plugins.php.
     * 
     * @since       3.0.6       Moved from the link class.
     * @since       3.1.0       Made it not insert the link if the user sets an empty string to the 'plugin_listing_table_title_cell_link' key of the label argument array.
     * @since       3.1.3       Moved from the post type class.
     */
    public function _replyToAddSettingsLinkInPluginListingPage( $aLinks ) {
        
        $_sLinkLabel = isset( $this->oProp->aPostTypeArgs['labels']['plugin_listing_table_title_cell_link'] )
            ? $this->oProp->aPostTypeArgs['labels']['plugin_listing_table_title_cell_link']
            : $this->oMsg->get( 'manage' );

        // If the user explicitly sets an empty string to the label key, do not insert a link.
        if ( ! $_sLinkLabel ) {
            return $aLinks;
        }

        // http://.../wp-admin/edit.php?post_type=[...]
        array_unshift(    
            $aLinks,
            "<a href='" . esc_url( "edit.php?post_type={$this->oProp->sPostType}" ) . "'>" 
                . $_sLinkLabel 
            . "</a>"
        ); 
        return $aLinks;     
        
    }
    
    /**
     * Sets up footer information.
     * 
     * @since   3.1.3
     */
    public function _replyToSetFooterInfo() {

        if ( 
            ! $this->isPostDefinitionPage( $this->oProp->sPostType ) 
            && ! $this->isPostListingPage( $this->oProp->sPostType ) 
            && ! $this->isCustomTaxonomyPage( $this->oProp->sPostType )
        ) {
            return;
        }
        parent::_replyToSetFooterInfo();
        
    }
    
    /**
     * Adds the `post_type` query key and value in the link url.
     * 
     * This is used to make it easier to detect if the linked page belongs to the post type created with this class.
     * So it can be used to embed footer links.
     * 
     * @since       2.0.0
     * @remark      e.g. `http://.../wp-admin/post.php?post=180&action=edit` -> `http://.../wp-admin/post.php?post=180&action=edit&post_type=[...]`
     * @callback    filter      get_edit_post_link
     */  
    public function _replyToAddPostTypeQueryInEditPostLink( $sURL, $iPostID=null, $sContext=null ) {
        return add_query_arg( 
            array( 
                'post'      => $iPostID, 
                'action'    => 'edit', 
                'post_type' => $this->oProp->sPostType 
            ), 
            $sURL
        );    
    }

}