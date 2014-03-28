<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_PostType_Model' ) ) :
/**
 * Provides methods of models for the post type factory class.
 * 
 * Those methods are internal and deal with internal properties.
 * 
 * @abstract
 * @since			3.0.4
 * @package			AdminPageFramework
 * @subpackage		PostType
 */
abstract class AdminPageFramework_PostType_Model extends AdminPageFramework_PostType_Router {	

	function __construct( $oProp ) {
		
		parent::__construct( $oProp );
		
		add_action( 'init', array( $this, '_replyToRegisterPostType' ), 999 );	// this is loaded in the front-end as well so should not be admin_init. Also "if ( is_admin() )" should not be used either.
		
		// Properties
		$this->oProp->aColumnHeaders = array(
			'cb'			=> '<input type="checkbox" />',		// Checkbox for bulk actions. 
			'title'			=> $this->oMsg->__( 'title' ),		// Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
			'author'		=> $this->oMsg->__( 'author' ), 	// Post author.
			// 'categories'	=> $this->oMsg->__( 'categories' ),	// Categories the post belongs to. 
			// 'tags'		=> $this->oMsg->__( 'tags' ), 		//	Tags for the post. 
			'comments' 		=> '<div class="comment-grey-bubble"></div>', // Number of pending comments. 
			'date'			=> $this->oMsg->__( 'date' ), 		// The date and publish status of the post. 
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
	 * Determines whether the currently loaded page is of the post type page.
	 * 
	 * @since			3.0.4
	 */
	protected function _isInThePage() {
		
		// If it's not in one of the post type's pages
		if ( ! $this->oProp->bIsAdmin ) {
			return false;
		}
		if ( ! in_array( $GLOBALS['pagenow'], array( 'edit.php', 'edit-tags.php', 'post.php', 'post-new.php' ) ) ) {
			return false;
		}
				
		return ( $this->oUtil->getCurrentPostType() == $this->oProp->sPostType );

	}
	
	
	/**
	 * Defines the sortable column items in the custom post listing table.
	 * 
	 * This method should be overridden by the user in their extended class.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>manage_edit-{post type}_sortable_columns</em> hook.
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
	 * @since			2.0.0
	 * @remark			A callback for the <em>manage_{post type}_post)_columns</em> hook.
	 * @return			void
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
		
		if ( $this->oProp->bEnableAutoSave ) return;
		if ( $this->oProp->sPostType != get_post_type() ) return;
		wp_dequeue_script( 'autosave' );
			
	}
	
	/**
	 * Registers the post type passed to the constructor.
	 * 
	 * @internal
	 */
	public function _replyToRegisterPostType() {

		register_post_type( $this->oProp->sPostType, $this->oProp->aPostTypeArgs );
		
		if ( true !== get_option( "post_type_rules_flased_{$this->oProp->sPostType}" ) ) {
		   flush_rewrite_rules( false );
		   update_option( "post_type_rules_flased_{$this->oProp->sPostType}", true );
		}

	}

	/**
	 * Registerd the set custom taxonomies.
	 * 
	 * @internal
	 */
	public function _replyToRegisterTaxonomies() {
		
		foreach( $this->oProp->aTaxonomies as $sTaxonomySlug => $aArgs ) 
			register_taxonomy(
				$sTaxonomySlug,
				$this->oProp->sPostType,
				$aArgs	// for the argument array keys, refer to: http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
			);	
			
	}

	/**
	 * Removes taxonomy menu items from the sidebar menu.
	 * 
	 * @internal
	 */
	public function _replyToRemoveTexonomySubmenuPages() {
		
		foreach( $this->oProp->aTaxonomyRemoveSubmenuPages as $sSubmenuPageSlug => $sTopLevelPageSlug )
			remove_submenu_page( $sTopLevelPageSlug, $sSubmenuPageSlug );
		
	}
	
	
}
endif;