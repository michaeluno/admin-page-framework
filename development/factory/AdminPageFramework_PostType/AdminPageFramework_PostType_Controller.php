<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_PostType_Controller' ) ) :
/**
 * Provides methods of views for the post type factory class.
 * 
 * Those methods are public and provides means for users to set property values.
 * 
 * @abstract
 * @since			3.0.4
 * @package			AdminPageFramework
 * @subpackage		PostType
 */
abstract class AdminPageFramework_PostType_Controller extends AdminPageFramework_PostType_View {	

	function __construct( $oProp ) {
		
		parent::__construct( $oProp );
			
		if ( $this->_isInThePage() ) :
		
			add_action( 'wp_loaded', array( $this, 'setUp' ) );					
			
		endif;
		
	}

	/**
	* The method for all necessary set-ups.
	* 
	* <h4>Example</h4>
	* <code>public function setUp() {
	* 		$this->setAutoSave( false );
	* 		$this->setAuthorTableFilter( true );
	* 		$this->addTaxonomy( 
	* 			'sample_taxonomy', // taxonomy slug
	* 			array(	// argument - for the argument array keys, refer to : http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
	* 				'labels' => array(
	* 					'name' => 'Genre',
	* 					'add_new_item' => 'Add New Genre',
	* 					'new_item_name' => "New Genre"
	* 				),
	* 				'show_ui' => true,
	* 				'show_tagcloud' => false,
	* 				'hierarchical' => true,
	* 				'show_admin_column' => true,
	* 				'show_in_nav_menus' => true,
	* 				'show_table_filter' => true,	// framework specific key
	* 				'show_in_sidebar_menus' => false,	// framework specific key
	* 			)
	* 		);
	* 	}</code>
	* 
	* @abstract
	* @since			2.0.0
	* @remark			The user should override this method in their class definition.
	* @remark			A callback for the <em>wp_loaded</em> hook.
	*/
	public function setUp() {}	
		
	/*
	 * Head Tag Methods
	 */
	/**
	 * Enqueues styles by page slug and tab slug.
	 * 
	 * @since			3.0.0
	 * @return			array			An array holding the handle IDs of queued items.
	 */
	public function enqueueStyles( $aSRCs, $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueStyles( $aSRCs, array( $this->oProp->sPostType ), $aCustomArgs );
	}
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 
	 * @since			3.0.0
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param 			array			(optional) The argument array for more advanced parameters.
	 * <h4>Custom Argument Array</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into after the input field tag.</li>
	 * </ul>
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */	
	public function enqueueStyle( $sSRC, $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueStyle( $sSRC, array( $this->oProp->sPostType ), $aCustomArgs );		
	}
	/**
	 * Enqueues scripts by page slug and tab slug.
	 * 
	 * @since			3.0.0
	 * @return			array			An array holding the handle IDs of queued items.
	 */
	public function enqueueScripts( $aSRCs, $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueScripts( $aSRCs, array( $this->oProp->sPostType ), $aCustomArgs );
	}	
	/**
	 * Enqueues a script by page slug and tab slug.
	 *  
	 * <h4>Example</h4>
	 * <code>$this->enqueueScript(  
	 *		plugins_url( 'asset/js/test.js' , __FILE__ ),	// source url or path
	 *		array(
	 *			'handle_id' => 'my_script',	// this handle ID also is used as the object name for the translation array below.
	 *			'translation' => array( 
	 *				'a' => 'hello world!',
	 *				'style_handle_id' => $sStyleHandle,	// check the enqueued style handle ID here.
	 *			),
	 *		)
	 *	);</code>
	 * 
	 * @since			3.0.0
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param 			array			(optional) The argument array for more advanced parameters.
	 * <h4>Custom Argument Array</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before <code></head ></code> or before <code></body></code> Default: <em>false</em>.</li>
	 * </ul>
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */
	public function enqueueScript( $sSRC, $aCustomArgs=array() ) {	
		return $this->oHeadTag->_enqueueScript( $sSRC, array( $this->oProp->sPostType ), $aCustomArgs );
	}		
	
	/*
	 * Front-end methods
	 */
	/**
	* Enables or disables the auto-save feature in the custom post type's post submission page.
	* 
	* <h4>Example</h4>
	* <code>$this->setAutoSave( false );
	* </code>
	* 
	* @since			2.0.0
	* @param			boolean			If true, it enables the auto-save; otherwise, it disables it.
	* return			void
	*/ 
	protected function setAutoSave( $bEnableAutoSave=True ) {
		$this->oProp->bEnableAutoSave = $bEnableAutoSave;		
	}
	
	/**
	* Adds a custom taxonomy to the class post type.
	* <h4>Example</h4>
	* <code>$this->addTaxonomy( 
	*		'sample_taxonomy', // taxonomy slug
	*		array(			// argument
	*			'labels' => array(
	*				'name' => 'Genre',
	*				'add_new_item' => 'Add New Genre',
	*				'new_item_name' => "New Genre"
	*			),
	*			'show_ui' => true,
	*			'show_tagcloud' => false,
	*			'hierarchical' => true,
	*			'show_admin_column' => true,
	*			'show_in_nav_menus' => true,
	*			'show_table_filter' => true,	// framework specific key
	*			'show_in_sidebar_menus' => false,	// framework specific key
	*		)
	*	);</code>
	* 
	* @see				http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
	* @since			2.0.0
	* @param			string			The taxonomy slug.
	* @param			array			The taxonomy argument array passed to the second parameter of the <a href="http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments">register_taxonomy()</a> function.
	* @return			void
	*/ 
	protected function addTaxonomy( $sTaxonomySlug, $aArgs ) {
		
		$sTaxonomySlug = $this->oUtil->sanitizeSlug( $sTaxonomySlug );
		$this->oProp->aTaxonomies[ $sTaxonomySlug ] = $aArgs;	
		if ( isset( $aArgs['show_table_filter'] ) && $aArgs['show_table_filter'] )
			$this->oProp->aTaxonomyTableFilters[] = $sTaxonomySlug;
		if ( isset( $aArgs['show_in_sidebar_menus'] ) && ! $aArgs['show_in_sidebar_menus'] )
			$this->oProp->aTaxonomyRemoveSubmenuPages[ "edit-tags.php?taxonomy={$sTaxonomySlug}&amp;post_type={$this->oProp->sPostType}" ] = "edit.php?post_type={$this->oProp->sPostType}";
				
		if ( count( $this->oProp->aTaxonomyTableFilters ) == 1 )
			add_action( 'init', array( $this, '_replyToRegisterTaxonomies' ) );	// the hook should not be admin_init because taxonomies need to be accessed in regular pages.
		if ( count( $this->oProp->aTaxonomyRemoveSubmenuPages ) == 1 )
			add_action( 'admin_menu', array( $this, '_replyToRemoveTexonomySubmenuPages' ), 999 );		
			
	}	

	/**
	* Sets whether the author drop-down filter is enabled/disabled in the post type post list table.
	* 
	* <h4>Example</h4>
	* <code>$this->setAuthorTableFilter( true );
	* </code>
	* 
	* @since			2.0.0
	* @param			boolean			If true, it enables the author filter; otherwise, it disables it.
	* @return			void
	*/ 
	protected function setAuthorTableFilter( $bEnableAuthorTableFileter=false ) {
		$this->oProp->bEnableAuthorTableFileter = $bEnableAuthorTableFileter;
	}
	
	/**
	 * Sets the post type arguments.
	 * 
	 * This is only necessary if it is not set in the constructor.
	 * 
	 * @since			2.0.0
	 * @see				http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
	 * @param			array			The <a href="http://codex.wordpress.org/Function_Reference/register_post_type#Arguments">array of arguments</a> to be passed to the second parameter of the <em>register_post_type()</em> function.
	 * @return			void
	 */ 
	protected function setPostTypeArgs( $aArgs ) {
		$this->oProp->aPostTypeArgs = $aArgs;
	}
	
	/**
	 * Sets the given HTML text into the footer on the left hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoLeft( '<br />Custom Text on the left hand side.' );
	 * </code>
	 * 
	 * @since			2.0.0
	 * @param			string			The HTML code to insert.
	 * @param			boolean			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */	
	protected function setFooterInfoLeft( $sHTML, $bAppend=true ) {
		if ( isset( $this->oLink ) )	// check if the object is set to ensure it won't trigger a warning message in non-admin pages.
			$this->oLink->aFooterInfo['sLeft'] = $bAppend 
				? $this->oLink->aFooterInfo['sLeft'] . $sHTML
				: $sHTML;
	}
	
	/**
	 * Sets the given HTML text into the footer on the right hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoRight( '<br />Custom Text on the right hand side.' );
	 * </code>
	 * 
	 * @since			2.0.0
	 * @param			string			The HTML code to insert.
	 * @param			boolean			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */		
	protected function setFooterInfoRight( $sHTML, $bAppend=true ) {
		if ( isset( $this->oLink ) )	// check if the object is set to ensure it won't trigger a warning message in non-admin pages.	
			$this->oLink->aFooterInfo['sRight'] = $bAppend 
				? $this->oLink->aFooterInfo['sRight'] . $sHTML
				: $sHTML;
	}
	
	
}
endif;