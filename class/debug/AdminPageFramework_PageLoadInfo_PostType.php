<?php
if ( ! class_exists( 'AdminPageFramework_PageLoadInfo_PostType' ) ) :
/**
 * Collects data of page loads of the added post type pages.
 *
 * @since			2.1.7
 * @extends			AdminPageFramework_PageLoadInfo_Base
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Debug
 */
class AdminPageFramework_PageLoadInfo_PostType extends AdminPageFramework_PageLoadInfo_Base {
	
	private static $_oInstance;
	
	/**
	 * Ensures that only one instance of this class object exists. ( no multiple instances of this object ) 
	 * 
	 * @remark			This class should be instantiated via this method.
	 */
	public static function instantiate( $oProp, $oMsg ) {
		
		if ( ! isset( self::$_oInstance ) && ! ( self::$_oInstance instanceof AdminPageFramework_PageLoadInfo_PostType ) ) 
			self::$_oInstance = new AdminPageFramework_PageLoadInfo_PostType( $oProp, $oMsg );
		return self::$_oInstance;
		
	}	

	/**
	 * Sets the hook if the current page is one of the framework's added post type pages.
	 * @internal
	 */ 
	public function _replyToSetPageLoadInfoInFooter() {

		// Some users sets $_GET['post_type'] element even in regular admin pages. In that case, do not load the style to avoid duplicates.
		if ( isset( $_GET['page'] ) && $_GET['page'] ) return;
	
		// For post type pages
		if ( isset( $_GET['post_type'], $this->oProp->sPostType ) && $_GET['post_type'] == $this->oProp->sPostType )
			add_filter( 'update_footer', array( $this, '_replyToGetPageLoadInfo' ), 999 );
		
	}	
	
}
endif;