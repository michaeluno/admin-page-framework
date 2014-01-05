<?php
if ( ! class_exists( 'AdminPageFramework_HeadTag_Page' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag for the main framework class.
 * 
 * @since			2.1.5
 */
class AdminPageFramework_HeadTag_Page extends AdminPageFramework_HeadTag_Base {

	/**
	 * Adds the stored CSS rules in the property into the head tag.
	 * 
	 * @remark			A callback for the <em>admin_head</em> hook.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from the main class.
	 */		
	public function _replyToAddStyle() {
		
		$sPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
		$sTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProp->getDefaultInPageTab( $sPageSlug );
		
		// If the loading page has not been registered nor the plugin page which uses this library, do nothing.
		if ( ! $this->oProp->isPageAdded( $sPageSlug ) ) return;
					
		$oCaller = $this->oProp->_getParentObject();
		
		// Print out the filtered styles.
		$sStyle = AdminPageFramework_Property_Page::$_sDefaultStyle . PHP_EOL . $this->oProp->sStyle;
		$sStyle = $this->oUtil->addAndApplyFilters( $oCaller, $this->oUtil->getFilterArrayByPrefix( 'style_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), $sStyle );
		$sStyleIE = AdminPageFramework_Property_Page::$_sDefaultStyleIE . PHP_EOL . $this->oProp->sStyleIE;
		$sStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, $this->oUtil->getFilterArrayByPrefix( 'style_ie_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), $sStyleIE );
		if ( ! empty( $sStyle ) )
			echo 
				"<style type='text/css' id='admin-page-framework-style'>" 
					. $sStyle
				. "</style>";
		if ( ! empty( $sStyleIE ) )
			echo 
				"<!--[if IE]><style type='text/css' id='admin-page-framework-style-for-IE'>" 
					. $sStyleIE
				. "</style><![endif]-->";
						
	}
	
	/**
	 * Adds the stored JavaScript scripts in the property into the head tag.
	 * 
	 * @remark			A callback for the <em>admin_head</em> hook.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from the main class.
	 */
	public function _replyToAddScript() {
		
		$sPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
		$sTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProp->getDefaultInPageTab( $sPageSlug );
		
		// If the loading page has not been registered or not the plugin page which uses this library, do nothing.
		if ( ! $this->oProp->isPageAdded( $sPageSlug ) ) return;

		$oCaller = $this->oProp->_getParentObject();
		
		// Print out the filtered scripts.
		echo "<script type='text/javascript' id='admin-page-framework-script'>"
				. $this->oUtil->addAndApplyFilters( $oCaller, $this->oUtil->getFilterArrayByPrefix( 'script_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), $this->oProp->sScript )
			. "</script>";		
		
	}

	/**
	 * Enqueues styles by page slug and tab slug.
	 * 
	 * @since			2.1.5
	 * @internal
	 */
	public function _enqueueStyles( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		
		$aHandleIDs = array();
		foreach( ( array ) $aSRCs as $sSRC )
			$aHandleIDs[] = $this->_enqueueStyle( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
		return $aHandleIDs;
		
	}
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	 * </ul>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class.
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param			string			$sPageSlug		(optional) The page slug that the stylesheet should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$sTabSlug			(optional) The tab slug that the stylesheet should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */	
	public function _enqueueStyle( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		
		$sSRC = trim( $sSRC );
		if ( empty( $sSRC ) ) return '';
		if ( isset( $this->oProp->aEnqueuingScripts[ md5( $sSRC ) ] ) ) return '';	// if already set
		
		$sSRC = $this->oUtil->resolveSRC( $sSRC );
		
		$sSRCHash = md5( $sSRC );	// setting the key based on the url prevents duplicate items
		$this->oProp->aEnqueuingStyles[ $sSRCHash ] = $this->oUtil->uniteArrays( 
			( array ) $aCustomArgs,
			array(		
				'sSRC' => $sSRC,
				'sPageSlug' => $sPageSlug,
				'sTabSlug' => $sTabSlug,
				'sType' => 'style',
				'handle_id' => 'style_' . $this->oProp->sClassName . '_' .  ( ++$this->oProp->iEnqueuedStyleIndex ),
			),
			self::$_aStructure_EnqueuingScriptsAndStyles
		);
		return $this->oProp->aEnqueuingStyles[ $sSRCHash ][ 'handle_id' ];
		
	}
	
	/**
	 * Enqueues scripts by page slug and tab slug.
	 * 
	 * @since			2.1.5
	 */
	public function _enqueueScripts( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		
		$aHandleIDs = array();
		foreach( ( array ) $aSRCs as $sSRC )
			$aHandleIDs[] = $this->_enqueueScript( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
		return $aHandleIDs;
		
	}	
	/**
	 * Enqueues a script by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class.
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param			string			$sPageSlug		(optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$sTabSlug			(optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 * @internal
	 */
	public function _enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		
		$sSRC = trim( $sSRC );
		if ( empty( $sSRC ) ) return '';
		if ( isset( $this->oProp->aEnqueuingScripts[ md5( $sSRC ) ] ) ) return '';	// if already set
		
		$sSRC = $this->oUtil->resolveSRC( $sSRC );
		
		$sSRCHash = md5( $sSRC );	// setting the key based on the url prevents duplicate items
		$this->oProp->aEnqueuingScripts[ $sSRCHash ] = $this->oUtil->uniteArrays( 
			( array ) $aCustomArgs,
			array(		
				'sPageSlug' => $sPageSlug,
				'sTabSlug' => $sTabSlug,
				'sSRC' => $sSRC,
				'sType' => 'script',
				'handle_id' => 'script_' . $this->oProp->sClassName . '_' .  ( ++$this->oProp->iEnqueuedScriptIndex ),
			),
			self::$_aStructure_EnqueuingScriptsAndStyles
		);
		return $this->oProp->aEnqueuingScripts[ $sSRCHash ][ 'handle_id' ];
	}
		
	/**
	 * A helper function for the above replyToEnqueueScripts() and replyToEnqueueStyle() methods.
	 * 
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class. Changed the name from enqueueSRCByPageConditoin.
	 * @internal
	 */
	protected function _enqueueSRCByConditoin( $aEnqueueItem ) {
		
		$sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
		$sCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProp->getDefaultInPageTab( $sCurrentPageSlug );
			
		$sPageSlug = $aEnqueueItem['sPageSlug'];
		$sTabSlug = $aEnqueueItem['sTabSlug'];
		
		// If the page slug is not specified and the currently loading page is one of the pages that is added by the framework,
		if ( ! $sPageSlug && $this->oProp->isPageAdded( $sCurrentPageSlug ) )  // means script-global(among pages added by the framework)
			return $this->_enqueueSRC( $aEnqueueItem );
				
		// If both tab and page slugs are specified,
		if ( 
			( $sPageSlug && $sCurrentPageSlug == $sPageSlug )
			&& ( $sTabSlug && $sCurrentTabSlug == $sTabSlug )
		) 
			return $this->_enqueueSRC( $aEnqueueItem );
		
		// If the tab slug is not specified and the page slug is specified, 
		// and if the current loading page slug and the specified one matches,
		if ( 
			( $sPageSlug && ! $sTabSlug )
			&& ( $sCurrentPageSlug == $sPageSlug )
		) 
			return $this->_enqueueSRC( $aEnqueueItem );

	}
	
}
endif;