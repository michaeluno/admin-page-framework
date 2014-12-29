<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods of models for the post type factory class.
 * 
 * Those methods are internal and deal with internal properties.
 * 
 * @abstract
 * @since           3.0.4
 * @package         AdminPageFramework
 * @subpackage      PostType
 * @internal
 */
abstract class AdminPageFramework_PostType_Model extends AdminPageFramework_PostType_Router {    

    /**
     * Sets up hooks and properties.
     * 
     * @internal
     */
    function __construct( $oProp ) {
        
        parent::__construct( $oProp );
        
        // When instantiating this class from the plugin activation hook, 'init' is already done.
        if ( did_action( 'init' ) ) {
            $this->_replyToRegisterPostType();
        } else {
            add_action( 'init', array( $this, '_replyToRegisterPostType' ), 999 ); // this is loaded in the front-end as well so should not be admin_init. Also "if ( is_admin() )" should not be used either.
        }
        
        // Properties
        $this->oProp->aColumnHeaders = array(
            'cb'        => '<input type="checkbox" />',     // Checkbox for bulk actions. 
            'title'     => $this->oMsg->get( 'title' ),     // Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
            'author'    => $this->oMsg->get( 'author' ),    // Post author.
            // 'categories' => $this->oMsg->get( 'categories' ), // Categories the post belongs to. 
            // 'tags' => $this->oMsg->get( 'tags' ),        // Tags for the post. 
            'comments'  => '<div class="comment-grey-bubble"></div>', // Number of pending comments. 
            'date'      => $this->oMsg->get( 'date' ),      // The date and publish status of the post. 
        );     
                            
        if ( $this->_isInThePage() ) :
        
            // For table columns
            add_filter( "manage_{$this->oProp->sPostType}_posts_columns", array( $this, '_replyToSetColumnHeader' ) );
            add_filter( "manage_edit-{$this->oProp->sPostType}_sortable_columns", array( $this, '_replyToSetSortableColumns' ) );
            add_action( "manage_{$this->oProp->sPostType}_posts_custom_column", array( $this, '_replyToSetColumnCell' ), 10, 2 );
        
            // Auto-save
            add_action( 'admin_enqueue_scripts', array( $this, '_replyToDisableAutoSave' ) );     
        
        endif;
        
    }    
    
    /**
     * Defines the sortable column items in the custom post listing table.
     * 
     * This method should be overridden by the user in their extended class.
     * 
     * @since       2.0.0
     * @remark      A callback for the `manage_edit-{post type}_sortable_columns` hook.
     * @internal
     */ 
    public function _replyToSetSortableColumns( $aColumns ) {
        return $this->oUtil->addAndApplyFilter( $this, "sortable_columns_{$this->oProp->sPostType}", $aColumns );
    }
    
    
    /**
     * Defines the column header items in the custom post listing table.
     * 
     * This method should be overridden by the user in their extended class.
     * 
     * @since 2.0.0
     * @remark A callback for the <em>manage_{post type}_post)_columns</em> hook.
     * @return void
     * @internal
     */ 
    public function _replyToSetColumnHeader( $aHeaderColumns ) {
        return $this->oUtil->addAndApplyFilter( $this, "columns_{$this->oProp->sPostType}", $aHeaderColumns );
    }    
    
    /**
     * 
     * @internal
     */
    public function _replyToSetColumnCell( $sColumnTitle, $iPostID ) { 
                
        // cell_{post type}_{custom column key}
        echo $this->oUtil->addAndApplyFilter( $this, "cell_{$this->oProp->sPostType}_{$sColumnTitle}", $sCell='', $iPostID );
                  
    }    
    
    /**
     * Disables the WordPress's built-in auto-save functionality.
     * 
     * @internal
     */
    public function _replyToDisableAutoSave() {
        
        if ( $this->oProp->bEnableAutoSave ) { return; }
        if ( $this->oProp->sPostType != get_post_type() ) { return; }
        wp_dequeue_script( 'autosave' );
            
    }
    
    /**
     * Registers the post type passed to the constructor.
     * 
     * @internal
     */
    public function _replyToRegisterPostType() {
        register_post_type( $this->oProp->sPostType, $this->oProp->aPostTypeArgs );
    }

    /**
     * Registers the set custom taxonomies.
     * 
     * @internal
     */
    public function _replyToRegisterTaxonomies() {

        foreach( $this->oProp->aTaxonomies as $_sTaxonomySlug => $_aArgs ) {
            $this->_registerTaxonomy( 
                $_sTaxonomySlug,  
                is_array( $this->oProp->aTaxonomyObjectTypes[ $_sTaxonomySlug ] ) 
                    ? $this->oProp->aTaxonomyObjectTypes[ $_sTaxonomySlug ] 
                    : array(), // object types
                $_aArgs // for the argument array keys, refer to: http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
            );
        }

    }
    
    /**
     * Registers a taxonomy.
     * @since       3.2.0
     * @internal    
     */
    public function _registerTaxonomy( $sTaxonomySlug, array $aObjectTypes, array $aArguments ) {
        
        if ( ! in_array( $this->oProp->sPostType, $aObjectTypes ) ) {
            $aObjectTypes[] = $this->oProp->sPostType;
        }
        register_taxonomy(
            $sTaxonomySlug,
            array_unique( $aObjectTypes ), // object types
            $aArguments // for the argument array keys, refer to: http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
        );            
        
    }

    /**
     * Removes taxonomy menu items from the sidebar menu.
     * 
     * @internal
     */
    public function _replyToRemoveTexonomySubmenuPages() {
    
        foreach( $this->oProp->aTaxonomyRemoveSubmenuPages as $sSubmenuPageSlug => $sTopLevelPageSlug ) {
            
            remove_submenu_page( $sTopLevelPageSlug, $sSubmenuPageSlug );
            
            // This method is called directly without a hook if the admin_menu hook is already passed.
            // In that case, when registering multiple taxonomies, this method can be called multiple times.
            // For that, the removed item should be cleared to avoid multiple menu removals.
            unset( $this->oProp->aTaxonomyRemoveSubmenuPages[ $sSubmenuPageSlug ] );
            
        }

    }
    
}