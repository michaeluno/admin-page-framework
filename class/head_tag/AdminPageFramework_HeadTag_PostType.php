<?php
if ( ! class_exists( 'AdminPageFramework_HeadTag_PostType' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag for the meta box class.
 * 
 * @since			2.1.5
 * @since			2.1.7			Added the replyToAddStyle() method.
 */
class AdminPageFramework_HeadTag_PostType extends AdminPageFramework_HeadTag_MetaBox {
	
	/**
	 * Appends the CSS rules of the framework in the head tag. 
	 * @since			2.1.7	
	 * @remark			A callback for the <em>admin_head</em> hook.
	 * @internal
	 */ 	
	public function _replyToAddStyle() {
	
		// If it's not the post type's post listing page or the taxtonomy page
		if ( 
			! (
				in_array( $GLOBALS['pagenow'], array( 'edit.php', 'edit-tags.php' ) ) 
				&& ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->oProp->sPostType )				
			)
		) return;	
	
		// Some users sets $_GET['post_type'] element even in regular admin pages. In that case, do not load the style to avoid duplicates.
		if ( isset( $_GET['page'] ) && $_GET['page'] ) return;
	
		// This class may be instantiated multiple times so use a global flag.
		$sRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$sRootClassName}_StyleLoaded" ] ) && $GLOBALS[ "{$sRootClassName}_StyleLoaded" ] ) return;
		$GLOBALS[ "{$sRootClassName}_StyleLoaded" ] = true;
				
		$oCaller = $this->oProp->getParentObject();		
				
		// Print out the filtered styles.
		$sStyle = AdminPageFramework_Property_Page::$sDefaultStyle . PHP_EOL . $this->oProp->sStyle;
		$sStyle = $this->oUtil->addAndApplyFilters( $oCaller, "style_{$this->oProp->sClassName}", $sStyle );
		$sStyleIE = AdminPageFramework_Property_Page::$sDefaultStyleIE . PHP_EOL . $this->oProp->sStyleIE;
		$sStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_{$this->oProp->sClassName}", $sStyleIE );
		if ( ! empty( $sStyle ) )
			echo 
				"<style type='text/css' id='admin-page-framework-style-post-type'>" 
					. $sStyle
				. "</style>";
		if ( ! empty( $sStyleIE ) )
			echo 
				"<!--[if IE]><style type='text/css' id='admin-page-framework-style-post-type'>" 
					. $sStyleIE
				. "</style><![endif]-->";
			
	}
	/**
	 * Appends the JavaScript script of the framework in the head tag. 
	 * @since			2.1.7
	 * @remark			A callback for the <em>admin_head</em> hook.
	 * @internal
	 */ 
	public function _replyToAddScript() {

		// If it's not the post type's post listing page
		if ( 
			! (
				in_array( $GLOBALS['pagenow'], array( 'edit.php', 'edit-tags.php' ) ) 
				&& ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->oProp->sPostType )				
			)
		) return;	
		
		// Some users sets $_GET['post_type'] element even in regular admin pages. In that case, do not load the style to avoid duplicates.
		if ( isset( $_GET['page'] ) && $_GET['page'] ) return;
	
		// This class may be instantiated multiple times so use a global flag.
		$sRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$sRootClassName}_ScriptLoaded" ] ) && $GLOBALS[ "{$sRootClassName}_ScriptLoaded" ] ) return;
		$GLOBALS[ "{$sRootClassName}_ScriptLoaded" ] = true;
	
		$oCaller = $this->oProp->getParentObject();
		
		// Print out the filtered scripts.
		$sScript = $this->oUtil->addAndApplyFilters( $oCaller, "script_{$this->oProp->sClassName}", $this->oProp->sScript );
		if ( ! empty( $sScript ) )
			echo 
				"<script type='text/javascript' id='admin-page-framework-script-post-type'>"
					. $sScript
				. "</script>";	
			
	}	
	
}
endif;