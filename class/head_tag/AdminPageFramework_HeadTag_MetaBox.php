<?php
if ( ! class_exists( 'AdminPageFramework_HeadTag_MetaBox' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag for the post type class.
 * 
 * @since			2.1.5
 * 
 */
class AdminPageFramework_HeadTag_MetaBox extends AdminPageFramework_HeadTag_Base {
	
	/**
	 * Appends the CSS rules of the framework in the head tag. 
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AdminPageFramework_MetaBox. Changed the name from addAtyle() to replyToAddStyle().
	 * @remark			A callback for the <em>admin_head</em> hook.
	 * @internal
	 */ 	
	public function _replyToAddStyle() {
	
		// If it's not post (post edit) page nor the post type page,
		if ( 
			! (
				in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) 
				&& ( 
					( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->oProps->aPostTypes ) )
					|| ( isset( $_GET['post'], $_GET['action'] ) && in_array( get_post_type( $_GET['post'] ), $this->oProps->aPostTypes ) )		// edit post page
				) 
			)
		) return;	
	
		// This class may be instantiated multiple times so use a global flag.
		$sRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$sRootClassName}_StyleLoaded" ] ) && $GLOBALS[ "{$sRootClassName}_StyleLoaded" ] ) return;
		$GLOBALS[ "{$sRootClassName}_StyleLoaded" ] = true;
				
		$oCaller = $this->oProps->getParentObject();		
				
		// Print out the filtered styles.
		$sStyle = AdminPageFramework_Properties::$sDefaultStyle . PHP_EOL . $this->oProps->sStyle;
		$sStyle = $this->oUtil->addAndApplyFilters( $oCaller, "style_{$this->oProps->sClassName}", $sStyle );
		$sStyleIE = AdminPageFramework_Properties::$sDefaultStyleIE . PHP_EOL . $this->oProps->sStyleIE;
		$sStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_{$this->oProps->sClassName}", $sStyleIE );
		if ( ! empty( $sStyle ) )
			echo 
				"<style type='text/css' id='admin-page-framework-style-meta-box'>" 
					. $sStyle
				. "</style>";
		if ( ! empty( $sStyleIE ) )
			echo 
				"<!--[if IE]><style type='text/css' id='admin-page-framework-style-meta-box'>" 
					. $sStyleIE
				. "</style><![endif]-->";
			
	}
	
	/**
	 * Appends the JavaScript script of the framework in the head tag. 
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AdminPageFramework_MetaBox. Changed the name from addScript() to replyToAddScript().
	 * @remark			A callback for the <em>admin_head</em> hook.
	 * @internal
	 */ 
	public function _replyToAddScript() {

		// If it's not post (post edit) page nor the post type page, do not add scripts for media uploader.
		if ( 
			! (
				in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) 
				&& ( 
					( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->oProps->aPostTypes ) )
					|| ( isset( $_GET['post'], $_GET['action'] ) && in_array( get_post_type( $_GET['post'] ), $this->oProps->aPostTypes ) )		// edit post page
				) 
			)
		) return;	
	
		// This class may be instantiated multiple times so use a global flag.
		$sRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$sRootClassName}_ScriptLoaded" ] ) && $GLOBALS[ "{$sRootClassName}_ScriptLoaded" ] ) return;
		$GLOBALS[ "{$sRootClassName}_ScriptLoaded" ] = true;
	
		$oCaller = $this->oProps->getParentObject();
		
		// Print out the filtered scripts.
		$sScript = $this->oUtil->addAndApplyFilters( $oCaller, "script_{$this->oProps->sClassName}", $this->oProps->sScript );
		if ( ! empty( $sScript ) )
			echo 
				"<script type='text/javascript' id='admin-page-framework-script-meta-box'>"
					. $sScript
				. "</script>";	
			
	}	
	
	
	/**
	 * Enqueues styles by page slug and tab slug.
	 * 
	 * @since			2.1.5
	 * @internal
	 */
	public function _enqueueStyles( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) {
		
		$aHandleIDs = array();
		foreach( ( array ) $aSRCs as $sSRC )
			$aHandleIDs[] = $this->_enqueueStyle( $sSRC, $aPostTypes, $aCustomArgs );
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
	 * 	<li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into after the input field tag.</li>
	 * </ul>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.5			
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param			array			$aPostTypes		(optional) The post type slugs that the stylesheet should be added to. If not set, it applies to all the pages of the post types.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 * @internal
	 */	
	public function _enqueueStyle( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) {
		
		$sSRC = trim( $sSRC );
		if ( empty( $sSRC ) ) return '';
		if ( isset( $this->oProps->aEnqueuingScripts[ md5( $sSRC ) ] ) ) return '';	// if already set
		
		$sSRC = $this->oUtil->resolveSRC( $sSRC );
		
		$sSRCHash = md5( $sSRC );	// setting the key based on the url prevents duplicate items
		$this->oProps->aEnqueuingStyles[ $sSRCHash ] = $this->oUtil->uniteArrays( 
			( array ) $aCustomArgs,
			array(		
				'sSRC' => $sSRC,
				'aPostTypes' => empty( $aPostTypes ) ? $this->oProps->aPostTypes : $aPostTypes,
				'sType' => 'style',
				'handle_id' => 'style_' . $this->oProps->sClassName . '_' .  ( ++$this->oProps->iEnqueuedStyleIndex ),
			),
			self::$_aStructure_EnqueuingScriptsAndStyles
		);
		return $this->oProps->aEnqueuingStyles[ $sSRCHash ][ 'handle_id' ];
		
	}
	
	/**
	 * Enqueues scripts by page slug and tab slug.
	 * 
	 * @since			2.1.5
	 * @internal
	 */
	public function _enqueueScripts( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) {
		
		$aHandleIDs = array();
		foreach( ( array ) $aSRCs as $sSRC )
			$aHandleIDs[] = $this->_enqueueScript( $sSRC, $aPostTypes, $aCustomArgs );
		return $aHandleIDs;
		
	}	
	/**
	 * Enqueues a script by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array.  Array of the handles of all the registered scripts that this script depends on. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 * 
	 * 
	 * @since			2.1.5			
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param			array			$aPostTypes		(optional) The post type slugs that the script should be added to. If not set, it applies to all the pages with the post type slugs.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 * @internal
	 */
	public function _enqueueScript( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) {
		
		$sSRC = trim( $sSRC );
		if ( empty( $sSRC ) ) return '';
		if ( isset( $this->oProps->aEnqueuingScripts[ md5( $sSRC ) ] ) ) return '';	// if already set
		
		$sSRC = $this->oUtil->resolveSRC( $sSRC );
		
		$sSRCHash = md5( $sSRC );	// setting the key based on the url prevents duplicate items
		$this->oProps->aEnqueuingScripts[ $sSRCHash ] = $this->oUtil->uniteArrays( 
			( array ) $aCustomArgs,
			array(		
				'sSRC' => $sSRC,
				'aPostTypes' => empty( $aPostTypes ) ? $this->oProps->aPostTypes : $aPostTypes,
				'sType' => 'script',
				'handle_id' => 'script_' . $this->oProps->sClassName . '_' .  ( ++$this->oProps->iEnqueuedScriptIndex ),
			),
			self::$_aStructure_EnqueuingScriptsAndStyles
		);
		return $this->oProps->aEnqueuingScripts[ $sSRCHash ][ 'handle_id' ];
	}

	/**
	 * A helper function for the above replyToEnqueueScripts() and replyToEnqueueStyle() methods.
	 * 
	 * @since			2.1.5
	 * @internal
	 */
	protected function _enqueueSRCByConditoin( $aEnqueueItem ) {
		
		$sCurrentPostType = isset( $_GET['post_type'] ) ? $_GET['post_type'] : ( isset( $GLOBALS['typenow'] ) ? $GLOBALS['typenow'] : null );
				
		if ( in_array( $sCurrentPostType, $aEnqueueItem['aPostTypes'] ) )		
			return $this->_enqueueSRC( $aEnqueueItem );
			
	}

}
endif;