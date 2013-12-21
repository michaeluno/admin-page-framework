<?php
if ( ! class_exists( 'AdminPageFramework_PageLoadInfo_Page' ) ) :
/**
 * Collects data of page loads of the added pages.
 *
 * @since			2.1.7
 * @extends			AdminPageFramework_PageLoadInfo_Base
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Debug
 */
class AdminPageFramework_PageLoadInfo_Page extends AdminPageFramework_PageLoadInfo_Base {
	
	private static $_oInstance;
	
	/**
	 * Ensures that only one instance of this class object exists. ( no multiple instances of this object ) 
	 * 
	 * @remark			This class should be instantiated via this method.
	 */
	public static function instantiate( $oProp, $oMsg ) {
		
		if ( ! isset( self::$_oInstance ) && ! ( self::$_oInstance instanceof AdminPageFramework_PageLoadInfo_Page ) ) 
			self::$_oInstance = new AdminPageFramework_PageLoadInfo_Page( $oProp, $oMsg );
		return self::$_oInstance;
		
	}		
	
	/**
	 * Sets the hook if the current page is one of the framework's added pages.
	 * @internal
	 */ 
	public function _replyToSetPageLoadInfoInFooter() {
		
		// For added pages
		$sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
		if ( $this->oProp->isPageAdded( $sCurrentPageSlug ) ) 
			add_filter( 'update_footer', array( $this, '_replyToGetPageLoadInfo' ), 999 );
	
	}		
	
}
endif;