<?php
if ( ! class_exists( 'AdminPageFramework_HeadTag_PostType' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag for the meta box class.
 * 
 * @since			2.1.5
 * @since			2.1.7			Added the replyToAddStyle() method.
 * @package			AdminPageFramework
 * @subpackage		HeadTag
 * @internal
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
				in_array( $GLOBALS['pagenow'], array( 'edit.php', 'edit-tags.php', 'post-new.php' ) ) 
				&& ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->oProp->sPostType )				
			)
		) return;	
	
		// Some users sets $_GET['post_type'] element even in regular admin pages. In that case, do not load the style to avoid duplicates.
		if ( isset( $_GET['page'] ) && $_GET['page'] ) return;
	
		// This class may be instantiated multiple times so use a global flag.
		$sRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$sRootClassName}_StyleLoaded" ] ) && $GLOBALS[ "{$sRootClassName}_StyleLoaded" ] ) return;
		$GLOBALS[ "{$sRootClassName}_StyleLoaded" ] = true;
				
		// Print out the filtered styles.
		$oCaller = $this->oProp->_getCallerObject();
		$sStyle = $this->oUtil->addAndApplyFilters( $oCaller, "style_common_{$this->oProp->sClassName}", AdminPageFramework_Property_PostType::$_sDefaultStyle )
			. $this->oUtil->addAndApplyFilters( $oCaller, "style_{$this->oProp->sClassName}", $this->oProp->sStyle );
		$sStyle = $this->oUtil->minifyCSS( $sStyle );
		if ( $sStyle )
			echo "<style type='text/css' id='admin-page-framework-style-post-type'>{$sStyle}</style>";
		$sStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_{$this->oProp->sClassName}", AdminPageFramework_Property_PostType::$_sDefaultStyleIE )
			. $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_{$this->oProp->sClassName}", $this->oProp->sStyleIE );
		$sStyleIE = $this->oUtil->minifyCSS( $sStyleIE );
		if ( $sStyleIE )
			echo "<!--[if IE]><style type='text/css' id='admin-page-framework-style-post-type'>{$sStyleIE}</style><![endif]-->";
			
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
				in_array( $GLOBALS['pagenow'], array( 'edit.php', 'edit-tags.php', 'post-new.php' ) ) 
				&& ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->oProp->sPostType )				
			)
		) return;	
		
		// Some users sets $_GET['post_type'] element even in regular admin pages. In that case, do not load the style to avoid duplicates.
		if ( isset( $_GET['page'] ) && $_GET['page'] ) return;
	
		// This class may be instantiated multiple times so use a global flag.
		$sRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$sRootClassName}_ScriptLoaded" ] ) && $GLOBALS[ "{$sRootClassName}_ScriptLoaded" ] ) return;
		$GLOBALS[ "{$sRootClassName}_ScriptLoaded" ] = true;
	
		// Print out the filtered scripts.
		$oCaller = $this->oProp->_getCallerObject();
		$sScript = $this->oUtil->addAndApplyFilters( $oCaller, "script_{$this->oProp->sClassName}", $this->oProp->sScript );
		if ( $sScript )
			echo "<script type='text/javascript' id='admin-page-framework-script-post-type'>{$sScript}</script>"; 
			
	}	
	
}
endif;