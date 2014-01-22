<?php
if ( ! class_exists( 'AdminPageFramework_PostType' ) ) :
/**
 * Provides methods for registering custom post types.
 * 
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 * 	<li><strong>start_ + extended class name</strong> – triggered at the end of the class constructor.</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 * 	<li><strong>cell_{post type slug}_{column key}</strong> – receives the output string for the listing table of the custom post type's post. The first parameter: output string. The second parameter: the post ID.</li>
 * 	<li><strong>columns_{post type slug}</strong> – receives the array containing the header columns for the listing table of the custom post type's post. The first parameter: the header columns container array.</li>
 * 	<li><strong>sortable_columns_{post type slug}</strong> – receives the array containing the sortable header column array for the listing table of the custom post type's post. The first parameter: the sortable header columns container array.</li>
 * </ul>
 * <h3>Remarks</h3>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p> 
 * 
 * @abstract
 * @since			2.0.0
 * @package			AdminPageFramework
 * @subpackage		PostType
 */
abstract class AdminPageFramework_PostType {	

	// Objects
	/**
	 * @since			2.0.0
	 * @internal
	 */ 
	protected $oUtil;
	/**
	 * @since			2.0.0
	 * @internal
	 */ 	
	protected $oLink;
		
	/**
	* The constructor of the class object.
	* 
	* Registers necessary hooks and sets up internal properties.
	* 
	* <h4>Example</h4>
	* <code>new APF_PostType( 
	* 	'apf_posts', 	// post type slug
	* 	array(	// argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
	* 		'labels' => array(
	* 			'name' => 'Admin Page Framework',
	* 			'singular_name' => 'Admin Page Framework',
	* 			'add_new' => 'Add New',
	* 			'add_new_item' => 'Add New APF Post',
	* 			'edit' => 'Edit',
	* 			'edit_item' => 'Edit APF Post',
	* 			'new_item' => 'New APF Post',
	* 			'view' => 'View',
	* 			'view_item' => 'View APF Post',
	* 			'search_items' => 'Search APF Post',
	* 			'not_found' => 'No APF Post found',
	* 			'not_found_in_trash' => 'No APF Post found in Trash',
	* 			'parent' => 'Parent APF Post'
	* 		),
	* 		'public' => true,
	* 		'menu_position' => 110,
	* 		'supports' => array( 'title' ),
	* 		'taxonomies' => array( '' ),
	* 		'menu_icon' => null,
	* 		'has_archive' => true,
	* 		'show_admin_column' => true,	// for custom taxonomies
	* 	)		
	* );</code>
	* @since			2.0.0
	* @since			2.1.6			Added the $sTextDomain parameter.
	* @see				http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
	* @param			string			The post type slug.
	* @param			array			The <a href="http://codex.wordpress.org/Function_Reference/register_post_type#Arguments">argument array</a> passed to register_post_type().
	* @param			string			The path of the caller script. This is used to retrieve the script information to insert it into the footer. If not set, the framework tries to detect it.
	* @param			string			The text domain of the caller script.
	* @return			void
	*/
	public function __construct( $sPostType, $aArgs=array(), $sCallerPath=null, $sTextDomain='admin-page-framework' ) {
		
		// Objects
		$this->oUtil = new AdminPageFramework_WPUtility;
		$this->oProp = new AdminPageFramework_Property_PostType( 
			$this, 
			$sCallerPath ? trim( $sCallerPath ) : AdminPageFramework_Utility::getCallerScriptPath( __FILE__ ), 	// this is important to attempt to find the caller script path here when separating the library into multiple files.			
			get_class( $this )	// class name
		);
		$this->oMsg = AdminPageFramework_Message::instantiate( $sTextDomain );
		$this->oHeadTag = new AdminPageFramework_HeadTag_PostType( $this->oProp );
		$this->oPageLoadInfo = AdminPageFramework_PageLoadInfo_PostType::instantiate( $this->oProp, $this->oMsg );
		$this->oDebug = new AdminPageFramework_Debug;
		
		// Properties
		$this->oProp->sPostType = $this->oUtil->sanitizeSlug( $sPostType );
		$this->oProp->aPostTypeArgs = $aArgs;	// for the argument array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
		$this->oProp->aColumnHeaders = array(
			'cb'			=> '<input type="checkbox" />',		// Checkbox for bulk actions. 
			'title'			=> $this->oMsg->__( 'title' ),		// Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
			'author'		=> $this->oMsg->__( 'author' ), 	// Post author.
			// 'categories'	=> $this->oMsg->__( 'categories' ),	// Categories the post belongs to. 
			// 'tags'		=> $this->oMsg->__( 'tags' ), 		//	Tags for the post. 
			'comments' 		=> '<div class="comment-grey-bubble"></div>', // Number of pending comments. 
			'date'			=> $this->oMsg->__( 'date' ), 		// The date and publish status of the post. 
		);			
		
		add_action( 'init', array( $this, '_replyToRegisterPostType' ), 999 );	// this is loaded in the front-end as well so should not be admin_init. Also "if ( is_admin() )" should not be used either.
		
		if ( $this->oProp->sPostType != '' && $this->oProp->bIsAdmin ) {			
		
			add_action( 'admin_enqueue_scripts', array( $this, '_replyToDisableAutoSave' ) );
			
			// For table columns
			add_filter( "manage_{$this->oProp->sPostType}_posts_columns", array( $this, '_replyToSetColumnHeader' ) );
			add_filter( "manage_edit-{$this->oProp->sPostType}_sortable_columns", array( $this, '_replyToSetSortableColumns' ) );
			add_action( "manage_{$this->oProp->sPostType}_posts_custom_column", array( $this, '_replyToSetColumnCell' ), 10, 2 );
			
			// For filters
			add_action( 'restrict_manage_posts', array( $this, '_replyToAddAuthorTableFilter' ) );
			add_action( 'restrict_manage_posts', array( $this, '_replyToAddTaxonomyTableFilter' ) );
			add_filter( 'parse_query', array( $this, '_replyToSetTableFilterQuery' ) );
			
			// Style
			add_action( 'admin_head', array( $this, '_replyToAddStyle' ) );
			
			// Links
			$this->oLink = new AdminPageFramework_Link_PostType( $this->oProp, $this->oMsg );
			
			add_action( 'wp_loaded', array( $this, 'setUp' ) );
		}
	
		$this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}" );
		
	}
	
	/*
	 * Extensible methods
	 */

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

	/**
	 * Sets the given screen icon to the post type screen icon.
	 * 
	 * @since			2.1.3
	 * @since			2.1.6				The $sSRC parameter can accept file path.
	 */
	private function getStylesForPostTypeScreenIcon( $sSRC ) {
		
		$sNone = 'none';
		
		$sSRC = $this->oUtil->resolveSRC( $sSRC );
		
		return "#post-body-content {
				margin-bottom: 10px;
			}
			#edit-slug-box {
				display: {$sNone};
			}
			#icon-edit.icon32.icon32-posts-" . $this->oProp->sPostType . " {
				background: url('" . $sSRC . "') no-repeat;
				background-size: 32px 32px;
			}			
		";		
		
	}
	
	/*
	 * Callback functions
	 */
	
	/**
	 * Defines the column header items in the custom post listing table.
	 * 
	 * This method should be overridden by the user in their extended class.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>manage_{post type}_post)_columns</em> hook.
	 * @return			void
	 * @internal
	 */ 
	public function _replyToSetColumnHeader( $aHeaderColumns ) {
		return $this->oUtil->addAndApplyFilter( $this, "columns_{$this->oProp->sPostType}", $aHeaderColumns );
	}	
	
	/**
	 * Defines the sortable column items in the custom post listing table.
	 * 
	 * This method should be overridden by the user in their extended class.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>manage_edit-{post type}_sortable_columns</em> hook.
	 * @internal
	 */ 
	public function _replyToSetSortableColumns( $aColumns ) {
		return $this->oUtil->addAndApplyFilter( $this, "sortable_columns_{$this->oProp->sPostType}", $aColumns );
	}
	
	/**
	 * Adds the script.
	 * @internal
	 */
	public function _replyToAddStyle() {

		if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] != $this->oProp->sPostType )
			return;

		// If the screen icon url is specified
		if ( isset( $this->oProp->aPostTypeArgs['screen_icon'] ) && $this->oProp->aPostTypeArgs['screen_icon'] )
			$this->oProp->sStyle .= $this->getStylesForPostTypeScreenIcon( $this->oProp->aPostTypeArgs['screen_icon'] );
			
		$this->oProp->sStyle = $this->oUtil->addAndApplyFilters( $this, "style_{$this->oProp->sClassName}", $this->oProp->sStyle );
		
		// Print out the filtered styles.
		if ( ! empty( $this->oProp->sStyle ) )
			echo "<style type='text/css' id='admin-page-framework-style-post-type'>" 
				. $this->oProp->sStyle
				. "</style>";			
		
	}
	
	/**
	 * Registers the post type passed to the constructor.
	 * 
	 * @internal
	 */
	public function _replyToRegisterPostType() {

		register_post_type( $this->oProp->sPostType, $this->oProp->aPostTypeArgs );
		
		$bIsPostTypeSet = get_option( "post_type_rules_flased_{$this->oProp->sPostType}" );
		if ( $bIsPostTypeSet !== true ) {
		   flush_rewrite_rules( false );
		   update_option( "post_type_rules_flased_{$this->oProp->sPostType}", true );
		}

	}	

	/**
	 * Registerd the set custom taxonomies.
	 * 
	 * @internal
	 */
	public function _replyToRegisterTaxonomies() {
		
		foreach( $this->oProp->aTaxonomies as $sTaxonomySlug => $aArgs ) 
			register_taxonomy(
				$sTaxonomySlug,
				$this->oProp->sPostType,
				$aArgs	// for the argument array keys, refer to: http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
			);	
			
	}
	
	/**
	 * Removes taxonomy menu items from the sidebar menu.
	 * 
	 * @internal
	 */
	public function _replyToRemoveTexonomySubmenuPages() {
		
		foreach( $this->oProp->aTaxonomyRemoveSubmenuPages as $sSubmenuPageSlug => $sTopLevelPageSlug )
			remove_submenu_page( $sTopLevelPageSlug, $sSubmenuPageSlug );
		
	}
	
	/**
	 * Disables the WordPress's built-in auto-save functionality.
	 * 
	 * @internal
	 */
	public function _replyToDisableAutoSave() {
		
		if ( $this->oProp->bEnableAutoSave ) return;
		if ( $this->oProp->sPostType != get_post_type() ) return;
		wp_dequeue_script( 'autosave' );
			
	}
	
	/**
	 * Adds a drop-down list to filter posts by author, placed above the post type listing table.
	 * 
	 * @internal
	 */ 
	public function _replyToAddAuthorTableFilter() {
		
		if ( ! $this->oProp->bEnableAuthorTableFileter ) return;
		
		if ( ! ( isset( $_GET['post_type'] ) && post_type_exists( $_GET['post_type'] ) 
			&& in_array( strtolower( $_GET['post_type'] ), array( $this->oProp->sPostType ) ) ) )
			return;
		
		wp_dropdown_users( array(
			'show_option_all'	=> 'Show all Authors',
			'show_option_none'	=> false,
			'name'			=> 'author',
			'selected'		=> ! empty( $_GET['author'] ) ? $_GET['author'] : 0,
			'include_selected'	=> false
		));
			
	}
	
	/**
	 * Adds drop-down lists to filter posts by added taxonomies, placed above the post type listing table.
	 * 
	 * @internal
	 */ 
	public function _replyToAddTaxonomyTableFilter() {
		
		if ( $GLOBALS['typenow'] != $this->oProp->sPostType ) return;
		
		// If there is no post added to the post type, do nothing.
		$oPostCount = wp_count_posts( $this->oProp->sPostType );
		if ( $oPostCount->publish + $oPostCount->future + $oPostCount->draft + $oPostCount->pending + $oPostCount->private + $oPostCount->trash == 0 )
			return;
		
		foreach ( get_object_taxonomies( $GLOBALS['typenow'] ) as $sTaxonomySulg ) {
			
			if ( ! in_array( $sTaxonomySulg, $this->oProp->aTaxonomyTableFilters ) ) continue;
			
			$oTaxonomy = get_taxonomy( $sTaxonomySulg );
 
			// If there is no added term, skip.
			if ( wp_count_terms( $oTaxonomy->name ) == 0 ) continue; 			

			// This function will echo the drop down list based on the passed array argument.
			wp_dropdown_categories( array(
				'show_option_all' => $this->oMsg->__( 'show_all' ) . ' ' . $oTaxonomy->label,
				'taxonomy' 	  => $sTaxonomySulg,
				'name' 		  => $oTaxonomy->name,
				'orderby' 	  => 'name',
				'selected' 	  => intval( isset( $_GET[ $sTaxonomySulg ] ) ),
				'hierarchical' 	  => $oTaxonomy->hierarchical,
				'show_count' 	  => true,
				'hide_empty' 	  => false,
				'hide_if_empty'	=> false,
				'echo'	=> true,	// this make the function print the output
			) );
			
		}
	}
	
	/**
	 * 
	 * 
	 * @internal
	 */
	public function _replyToSetTableFilterQuery( $oQuery=null ) {
		
		if ( 'edit.php' != $GLOBALS['pagenow'] ) return $oQuery;
		
		if ( ! isset( $GLOBALS['typenow'] ) ) return $oQuery;
		
		foreach ( get_object_taxonomies( $GLOBALS['typenow'] ) as $sTaxonomySlug ) {
			
			if ( ! in_array( $sTaxonomySlug, $this->oProp->aTaxonomyTableFilters ) ) continue;
			
			$sVar = &$oQuery->query_vars[ $sTaxonomySlug ];
			if ( ! isset( $sVar ) ) continue;
			
			$oTerm = get_term_by( 'id', $sVar, $sTaxonomySlug );
			if ( is_object( $oTerm ) )
				$sVar = $oTerm->slug;

		}
		return $oQuery;
		
	}
	
	/**
	 * 
	 * @internal
	 */
	public function _replyToSetColumnCell( $sColumnTitle, $iPostID ) { 
				
		// cell_{post type}_{custom column key}
		echo $this->oUtil->addAndApplyFilter( $this, "cell_{$this->oProp->sPostType}_{$sColumnTitle}", $sCell='', $iPostID );
				  
	}
	
	/**
	 * Redirects undefined callback methods or to the appropriate methods.
	 * 
	 * @internal
	 */
	public function __call( $sMethodName, $aArgs=null ) {	
		if ( substr( $sMethodName, 0, strlen( "cell_" ) ) == "cell_" ) return $aArgs[0];
		if ( substr( $sMethodName, 0, strlen( "sortable_columns_" ) ) == "sortable_columns_" ) return $aArgs[0];
		if ( substr( $sMethodName, 0, strlen( "columns_" ) ) == "columns_" ) return $aArgs[0];
		if ( substr( $sMethodName, 0, strlen( "style_ie_common_" ) )== "style_ie_common_" ) return $aArgs[0];
		if ( substr( $sMethodName, 0, strlen( "style_common_" ) )== "style_common_" ) return $aArgs[0];
		if ( substr( $sMethodName, 0, strlen( "style_ie_" ) )== "style_ie_" ) return $aArgs[0];
		if ( substr( $sMethodName, 0, strlen( "style_" ) )== "style_" ) return $aArgs[0];
	}
	
}
endif;