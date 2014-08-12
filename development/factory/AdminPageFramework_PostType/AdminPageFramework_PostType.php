<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
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
abstract class AdminPageFramework_PostType extends AdminPageFramework_PostType_Controller {	
		
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
		
		if ( empty( $sPostType ) ) return;

		// Properties
		$this->oProp				= new AdminPageFramework_Property_PostType( 
			$this, 
			$sCallerPath ? trim( $sCallerPath ) : ( 
				( is_admin() && isset( $GLOBALS['pagenow'] ) && in_array( $GLOBALS['pagenow'], array( 'edit.php', 'post.php', 'post-new.php', 'plugins.php', 'tags.php', 'edit-tags.php', ) ) )
					? AdminPageFramework_Utility::getCallerScriptPath( __FILE__ ) 
					: null 
				), 	// this is important to attempt to find the caller script path here when separating the library into multiple files.	
			get_class( $this ),	// class name
			'post',				// capability
			$sTextDomain,		// text domain
			'post_type'			// fields type
		);
		$this->oProp->sPostType		= AdminPageFramework_WPUtility::sanitizeSlug( $sPostType );
		$this->oProp->aPostTypeArgs	= $aArgs;	// for the argument array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments

		parent::__construct( $this->oProp );
				
		$this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}" );
							
	}
				
}
endif;