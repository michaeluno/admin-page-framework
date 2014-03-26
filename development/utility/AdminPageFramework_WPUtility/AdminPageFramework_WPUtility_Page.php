<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_WPUtility_Page' ) ) :
/**
 * Provides utility methods to detect types of admin pages which use WordPress functions and classes.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_Utility
 * @package			AdminPageFramework
 * @subpackage		Utility
 * @internal
 */
class AdminPageFramework_WPUtility_Page extends AdminPageFramework_WPUtility_HTML {
	
	/**
	 * Attempts to retrieve the current admin post type
	 * 
	 * @since			3.0.0
	 */
	static public function getCurrentPostType() {
				 
		static $_sCurrentPostType;
		
		// Since the current page will be the same throughout the execution of the script, if once it's found, there is no need to find it again.
		if ( $_sCurrentPostType ) return $_sCurrentPostType;
		
		// Check to see if a post object exists
		if ( isset( $GLOBALS['post'], $GLOBALS['post']->post_type ) && $GLOBALS['post']->post_type ) {
			$_sCurrentPostType = $GLOBALS['post']->post_type;
			return $_sCurrentPostType;
		}
		 
		// Check if the current type is set
		if ( isset( $GLOBALS['typenow'] ) && $GLOBALS['typenow'] ) {
			$_sCurrentPostType = $GLOBALS['typenow'];
			return $_sCurrentPostType;
		}
		 
		// Check to see if the current screen is set
		if ( isset( $GLOBALS['current_screen']->post_type ) && $GLOBALS['current_screen']->post_type ) {
			$_sCurrentPostType = $GLOBALS['current_screen']->post_type;
			return $_sCurrentPostType;
		}
		 
		// Finally make a last ditch effort to check the URL query for type
		if ( isset( $_REQUEST['post_type'] ) ) {
			$_sCurrentPostType = sanitize_key( $_REQUEST['post_type'] );
			return $_sCurrentPostType;
		}
		
		// If the post is set, find the post type from it. If will perform a database query if necessary.
		if ( isset( $_GET['post'] ) && $_GET['post'] ) {
			$_sCurrentPostType = get_post_type( $_GET['post'] );
			return $_sCurrentPostType;
		}
		
		return null;
		
	}

	/**
	 * Checks if the current page is post editing page that belongs to the given post type(s).
	 * 
	 * @since			3.0.0
	 * @param			array|string			The post type slug(s) to check. If this is empty, the method just checks the current page is a post definition page.
	 * Otherwise, it will check if the page belongs to the given post type(s).
	 * @return			boolean
	 */
	static public function isPostDefinitionPage( $asPostTypes=array() ) {
		
		$_aPostTypes = ( array ) $asPostTypes;

		// If it's not the post definition page, 
		if ( ! in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) ) return false;

		// If the parameter is empty, 
		if ( empty( $_aPostTypes ) ) return true;

		// If the parameter of the post type is set and it's in the given post types, 
		if ( in_array( self::getCurrentPostType(), $_aPostTypes ) ) return true;				

		// Otherwise,
		return false;
		
	}		
	
	/**
	 * Checks if the current page is in the post listing page of the given page slug(s).
	 * 
	 * @since			3.0.0
	 */
	static public function isPostListingPage( $asPostTypes=array() ) {
				
		if ( $GLOBALS['pagenow'] != 'edit.php' ) return false;
		
		$_aPostTypes = is_array( $asPostTypes ) ? $asPostTypes : array( $asPostTypes );
		
		if ( ! isset( $_GET['post_type'] )  ) return in_array( 'post', $_aPostTypes );

		return in_array( $_GET['post_type'], $_aPostTypes );
		
	}
	
}
endif;