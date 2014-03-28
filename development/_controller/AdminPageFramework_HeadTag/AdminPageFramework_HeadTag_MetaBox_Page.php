<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_HeadTag_MetaBox_Page' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag for the meta box class for pages added by the framework.
 * 
 * @since			3.0.0
 * @use				AdminPageFramework_Utility
 * @package			AdminPageFramework
 * @subpackage		HeadTag
 * @internal
 */
class AdminPageFramework_HeadTag_MetaBox_Page extends AdminPageFramework_HeadTag_Page {
		
	/**
	 * Checks wither the currently loading page is appropriate for the meta box to be displayed.
	 * @since			3.0.0
	 * @internal
	 */
	private function _isMetaBoxPage() {
			
		if ( ! isset( $_GET['page'] ) )	 return false;
		
		if ( in_array( $_GET['page'], $this->oProp->aPageSlugs ) )
			return true;
		
		return false;
		
	}
	
	/**
	 * Appends the CSS rules of the framework in the head tag. 
	 * @since			3.0.0
	 * @remark			A callback for the <em>admin_head</em> hook.
	 * @internal
	 */ 	
	public function _replyToAddStyle() {
	
		if ( ! $this->_isMetaBoxPage() ) return;	// if it's not post (post edit) page nor the post type page,
	
		$this->_printCommonStyles( 'admin-page-framework-style-meta-box-common', get_class() );
		$this->_printClassSpecificStyles( 'admin-page-framework-style-meta-box' );
		$this->oProp->_bAddedStyle = true;
			
	}
	/**
	 * Appends the JavaScript script of the framework in the head tag. 
	 * @since			3.0.0
	 * @remark			A callback for the <em>admin_head</em> hook.
	 * @internal
	 */ 
	public function _replyToAddScript() {

		if ( ! $this->_isMetaBoxPage() ) return;	// if it's not post (post edit) page nor the post type page,
	
		$this->_printCommonScripts( 'admin-page-framework-style-meta-box-common', get_class() );
		$this->_printClassSpecificScripts( 'admin-page-framework-script-meta-box' );
		$this->oProp->_bAddedScript = true;
		
	}	
		/**
		 *	Prints the inline stylesheet of this class stored in this class property.
		 *	@since			3.0.0
		 */
		protected function _printClassSpecificStyles( $sIDPrefix ) {
				
			$oCaller = $this->oProp->_getCallerObject();		

			// Print out the filtered styles.
			$sStyle = $this->oUtil->addAndApplyFilters( $oCaller, "style_{$this->oProp->sClassName}", $this->oProp->sStyle );
			$sStyle = $this->oUtil->minifyCSS( $sStyle );
			if ( $sStyle )
				echo "<style type='text/css' id='{$sIDPrefix}-{$this->oProp->sClassName}'>{$sStyle}</style>";
				
			$sStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_{$this->oProp->sClassName}", $this->oProp->sStyleIE );
			if ( $sStyleIE )
				echo  "<!--[if IE]><style type='text/css' id='{$sIDPrefix}-ie-{$this->oProp->sClassName}'>{$sStyleIE}</style><![endif]-->";
		
		}
		/**
		 * Prints the inline stylesheet of the meta-box common CSS rules with the style tag.
		 * 
		 * @since			3.0.0
		 * @remark			The meta box class may be instantiated multiple times so use a global flag.
		 * @parametr		string			$sIDPrefix			The id selector embedded in the script tag.
		 * @parametr		string			$sClassName			The class name that identify the call group. This is important for the meta-box class because it can be instantiated multiple times in one particular page.
		 */
		protected function _printCommonStyles( $sIDPrefix, $sClassName ) {
			
			if ( isset( $GLOBALS[ "{$sClassName}_StyleLoaded" ] ) && $GLOBALS[ "{$sClassName}_StyleLoaded" ] ) return;
			$GLOBALS[ "{$sClassName}_StyleLoaded" ] = true;			
			
			$oCaller = $this->oProp->_getCallerObject();				
			$sStyle = $this->oUtil->addAndApplyFilters( $oCaller, "style_common_{$this->oProp->sClassName}", AdminPageFramework_Property_Base::$_sDefaultStyle );
			$sStyle = $this->oUtil->minifyCSS( $sStyle );
			if ( $sStyle )
				echo "<style type='text/css' id='{$sIDPrefix}'>{$sStyle}</style>";

			$sStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_common_{$this->oProp->sClassName}", AdminPageFramework_Property_Base::$_sDefaultStyleIE );
			$sStyleIE = $this->oUtil->minifyCSS( $sStyleIE );
			if ( $sStyleIE )
				echo "<!--[if IE]><style type='text/css' id='{$sIDPrefix}-ie'>{$sStyleIE}</style><![endif]-->";
				
		}		
		/**
		 *	Prints the inline scripts of this class stored in this class property.
		 *	@since			3.0.0
		 */
		protected function _printClassSpecificScripts( $sIDPrefix ) {
				
			$sScript = $this->oUtil->addAndApplyFilters( $this->oProp->_getCallerObject(), "script_{$this->oProp->sClassName}", $this->oProp->sScript );
			if ( $sScript )
				echo "<script type='text/javascript' id='{$sIDPrefix}-{$this->oProp->sClassName}'>{$sScript}</script>";				

		}
		/**
		 * Prints the inline scripts of the meta-box common scripts.
		 * 
		 * @remark			The meta box class may be instantiated multiple times so use a global flag.
		 * @parametr		string			$sIDPrefix			The id selector embedded in the script tag.
		 * @parametr		string			$sClassName			The class name that identify the call group. This is important for the meta-box class because it can be instantiated multiple times in one particular page.
		 * @since			3.0.0
		 */
		protected function _printCommonScripts( $sIDPrefix, $sClassName ) {
			
			if ( isset( $GLOBALS[ "{$sClassName}_ScriptLoaded" ] ) && $GLOBALS[ "{$sClassName}_ScriptLoaded" ] ) return;
			$GLOBALS[ "{$sClassName}_ScriptLoaded" ] = true;
			
			$sScript = $this->oUtil->addAndApplyFilters( $this->oProp->_getCallerObject(), "script_common_{$this->oProp->sClassName}", AdminPageFramework_Property_Base::$_sDefaultScript );
			if ( $sScript )
				echo "<script type='text/javascript' id='{$sIDPrefix}'>{$sScript}</script>";
		
		}
	
	

}
endif;