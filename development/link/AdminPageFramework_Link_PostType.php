<?php
if ( ! class_exists( 'AdminPageFramework_Link_PostType' ) ) :
/**
 * Provides methods for HTML link elements for custom post types.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_Utility
 * @package			AdminPageFramework
 * @subpackage		Link
 * @internal
 */
class AdminPageFramework_Link_PostType extends AdminPageFramework_Link_Base {
	
	/**
	 * Stores the information to embed into the page footer.
	 * @since			2.0.0
	 * @remark			This is accessed from the AdminPageFramework_PostType class.
	 */ 
	public $aFooterInfo = array(
		'sLeft' => '',
		'sRight' => '',
	);
	
	public function __construct( $oProp, $oMsg=null ) {
		
		if ( ! is_admin() ) return;
		
		$this->oProp = $oProp;
		$this->oMsg = $oMsg;
		
		$this->sSettingPageLinkTitle = $this->oMsg->__( 'manage' );
		
		// Add script info into the footer 
		add_filter( 'update_footer', array( $this, '_replyToAddInfoInFooterRight' ), 11 );
		add_filter( 'admin_footer_text' , array( $this, '_replyToAddInfoInFooterLeft' ) );	
		$this->_setFooterInfoLeft( $this->oProp->aScriptInfo, $this->aFooterInfo['sLeft'] );
		$aLibraryData = $this->oProp->_getLibraryData();
		$aLibraryData['sVersion'] = $this->oProp->bIsMinifiedVersion ? $aLibraryData['sVersion'] . '.min' : $aLibraryData['sVersion'];
		$this->_setFooterInfoRight( $aLibraryData, $this->aFooterInfo['sRight'] );
		
		// For the plugin listing page
		if ( $this->oProp->aScriptInfo['sType'] == 'plugin' )
			add_filter( 
				'plugin_action_links_' . plugin_basename( $this->oProp->aScriptInfo['sPath'] ),
				array( $this, '_replyToAddSettingsLinkInPluginListingPage' ), 
				20 	// set a lower priority so that the link will be embedded at the beginning ( the most left hand side ).
			);	
		
		// For post type posts listing table page ( edit.php )
		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->oProp->sPostType )
			add_action( 'get_edit_post_link', array( $this, '_replyToAddPostTypeQueryInEditPostLink' ), 10, 3 );
		
	}
	
	/**
	 * Adds the <em>post_type</em> query key and value in the link url.
	 * 
	 * This is used to make it easier to detect if the linked page belongs to the post type created with this class.
	 * So it can be used to embed footer links.
	 * 
	 * @since			2.0.0
	 * @remark			e.g. http://.../wp-admin/post.php?post=180&action=edit -> http://.../wp-admin/post.php?post=180&action=edit&post_type=[...]
	 * @remark			A callback for the <em>get_edit_post_link</em> hook.
	 */	 
	public function _replyToAddPostTypeQueryInEditPostLink( $sURL, $iPostID=null, $sContext=null ) {
		return add_query_arg( array( 'post' => $iPostID, 'action' => 'edit', 'post_type' => $this->oProp->sPostType ), $sURL );	
	}	
	public function _replyToAddSettingsLinkInPluginListingPage( $aLinks ) {
		
		// http://.../wp-admin/edit.php?post_type=[...]
		array_unshift(	
			$aLinks,
			"<a href='edit.php?post_type={$this->oProp->sPostType}'>" . $this->sSettingPageLinkTitle . "</a>"
		); 
		return $aLinks;		
		
	}
	
	/**
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the filter hook, <em>admin_footer_text</em>.
	 * @internal
	 */ 
	public function _replyToAddInfoInFooterLeft( $sLinkHTML='' ) {
		
		// Check if it's in the post definition page and the post listing page
		if ( ! $this->isPostDefinitionPage( $this->oProp->sPostType ) && ! $this->isPostListingPage( $this->oProp->sPostType ) )	
			return $sLinkHTML;	// $sLinkHTML is given by the hook.
			
		if ( empty( $this->oProp->aScriptInfo['sName'] ) ) return $sLinkHTML;
					
		return $this->aFooterInfo['sLeft'];
		
	}
	/**
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the filter hook, <em>admin_footer_text</em>.
	 * @internal
	 */ 	
	public function _replyToAddInfoInFooterRight( $sLinkHTML='' ) {

		// Check if it's in the post definition page and the post listing page
		if ( ! $this->isPostDefinitionPage( $this->oProp->sPostType ) && ! $this->isPostListingPage( $this->oProp->sPostType ) )	
			return $sLinkHTML;	// $sLinkHTML is given by the hook.
			
		return $this->aFooterInfo['sRight'];		
			
	}
}
endif;