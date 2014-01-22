<?php
if ( ! class_exists( 'AdminPageFramework_HeadTag_TaxonomyField' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag in the taxonomy page.
 * 
 * @since			3.0.0
 * @package			Admin Page Framework
 * @subpackage		HeadTag
 * @extends			AdminPageFramework_HeadTag_MetaBox
 * @internal
 */
class AdminPageFramework_HeadTag_TaxonomyField extends AdminPageFramework_HeadTag_MetaBox {

	/**
	 * Adds the stored CSS rules in the property into the head tag.
	 * 
	 * @remark	A callback for the <em>admin_head</em> hook.
	 * @since	3.0.0
	 */		
	public function _replyToAddStyle() {
		
		if ( $GLOBALS['pagenow'] != 'edit-tags.php' ) return;
		$this->_printCommonStyles( 'admin-page-framework-style-taxonomy-field-common', get_class() );	// Note that it's not get_class( $this ) to give the abstract class name.
		$this->_printClassSpecificStyles( 'admin-page-framework-style-taxonomy-field' );
		$this->oProp->_bAddedStyle = true;
		
	}
	
	/**
	 * Adds the stored JavaScript scripts in the property into the head tag.
	 * 
	 * @remark			A callback for the <em>admin_head</em> hook.
	 * @since			3.0.0
	 */
	public function _replyToAddScript() {
		
		if ( $GLOBALS['pagenow'] != 'edit-tags.php' ) return;
		$this->_printCommonScripts( 'admin-page-framework-style-taxonomy-field-common', get_class() );	// Note that it's not get_class( $this ) to give the abstract class name.
		$this->_printClassSpecificScripts( 'admin-page-framework-script-taxonomy-field' );
		$this->oProp->_bAddedScript = true;
		
	}

	/**
	 * Enqueues styles by page slug and tab slug.
	 * 
	 * @since			3.0.0
	 * @remark			the $_deprecated parameter is just to avoid the PHP strict standards warning.
	 * @internal
	 */
	public function _enqueueStyles( $aSRCs, $aCustomArgs=array(), $_deprecated=null ) {
		
		$aHandleIDs = array();
		foreach( ( array ) $aSRCs as $sSRC )
			$aHandleIDs[] = $this->_enqueueStyle( $sSRC, $aCustomArgs );
		return $aHandleIDs;
		
	}
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Second Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	 * </ul>
	 * 
	 * @since			3.0.0
	 * @remark			the $_deprecated parameter is just to avoid the PHP strict standards warning.
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 * @internal
	 */	
	public function _enqueueStyle( $sSRC, $aCustomArgs=array(), $_deprecated=null ) {
		
		$sSRC = trim( $sSRC );
		if ( empty( $sSRC ) ) return '';
		if ( isset( $this->oProp->aEnqueuingScripts[ md5( $sSRC ) ] ) ) return '';	// if already set
		
		$sSRC = $this->oUtil->resolveSRC( $sSRC );
		
		$sSRCHash = md5( $sSRC );	// setting the key based on the url prevents duplicate items
		$this->oProp->aEnqueuingStyles[ $sSRCHash ] = $this->oUtil->uniteArrays( 
			( array ) $aCustomArgs,
			array(		
				'sSRC' => $sSRC,
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
	 * @since			3.0.0
	 * @remark			the $_deprecated parameter is just to avoid the PHP strict standards warning.
	 */
	public function _enqueueScripts( $aSRCs, $aCustomArgs=array(), $_deprecated=null ) {
		
		$aHandleIDs = array();
		foreach( ( array ) $aSRCs as $sSRC )
			$aHandleIDs[] = $this->_enqueueScript( $sSRC, $aCustomArgs );
		return $aHandleIDs;
		
	}	
	/**
	 * Enqueues a script by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Second Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 * 
	 * @since			3.0.0
	 * @remark			the $_deprecated parameter is just to avoid the PHP strict standards warning.
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 * @internal
	 */
	public function _enqueueScript( $sSRC, $aCustomArgs=array(), $_deprecated=null ) {
		
		$sSRC = trim( $sSRC );
		if ( empty( $sSRC ) ) return '';
		if ( isset( $this->oProp->aEnqueuingScripts[ md5( $sSRC ) ] ) ) return '';	// if already set
		
		$sSRC = $this->oUtil->resolveSRC( $sSRC );
		
		$sSRCHash = md5( $sSRC );	// setting the key based on the url prevents duplicate items
		$this->oProp->aEnqueuingScripts[ $sSRCHash ] = $this->oUtil->uniteArrays( 
			( array ) $aCustomArgs,
			array(		
				'sSRC' => $sSRC,
				'sType' => 'script',
				'handle_id' => 'script_' . $this->oProp->sClassName . '_' .  ( ++$this->oProp->iEnqueuedScriptIndex ),
			),
			self::$_aStructure_EnqueuingScriptsAndStyles
		);
		return $this->oProp->aEnqueuingScripts[ $sSRCHash ][ 'handle_id' ];
	}

	/**
	 * Enqueues a style source without conditions.
	 * @remark			Used for inserting the input field head tag elements.
	 * @since			3.0.0
	 * @internal
	 */
	public function _forceToEnqueueStyle( $sSRC, $aCustomArgs=array() ) {
		return $this->_enqueueStyle( $sSRC, $aCustomArgs );
	}
	/**
	 * Enqueues a script source without conditions.
	 * @remark			Used for inserting the input field head tag elements.
	 * @since			3.0.0
	 * @internal
	 */	
	public function _forceToEnqueueScript( $sSRC, $aCustomArgs=array() ) {
		return $this->_enqueueScript( $sSRC, $aCustomArgs );
	}
	
	/**
	 * A helper function for the _replyToEnqueueScripts() and _replyToEnqueueStyle() methods.
	 * 
	 * @since			3.0.0
	 * @internal
	 */
	protected function _enqueueSRCByConditoin( $aEnqueueItem ) {
					
		return $this->_enqueueSRC( $aEnqueueItem );	// the taxonomy page is checked in the constructor, so there is no need to apply a condition.

	}
	
}
endif;