<?php 
/**
 * Admin Page Framework
 * 
 * Provides plugin and theme developers with simpler means of creating option pages, custom post types, ant meta boxes. 
 * The framework uses the built-in WordPress Settings API so it respects the WordPress standard form layout design.
 * 
 * @author				Michael Uno <michael@michaeluno.jp>
 * @copyright			Michael Uno
 * @license				GPLv2 or later
 * @see					http://wordpress.org/plugins/admin-page-framework/
 * @see					https://github.com/michaeluno/admin-page-framework
 * @link				http://en.michaeluno.jp/admin-page-framework
 * @package				Admin Page Framework
 * @remarks				To use the framework, 1. Extend the class 2. Override the setUp() method. 3. Use the hook functions.
 * @remarks				Requirements: WordPress 3.3 or above, PHP 5.2.4 or above.
 * @remarks				The documentation employs the <a href="http://en.wikipedia.org/wiki/PHPDoc">PHPDOc(DocBlock)</a> syntax.
 * @version				3.0.0b
 */
/*
	Library Name: Admin Page Framework
	Library URI: http://wordpress.org/extend/plugins/admin-page-framework/
	Author:  Michael Uno
	Author URI: http://michaeluno.jp
	Version: 3.0.0b
	Requirements: WordPress 3.3 or above, PHP 5.2.4 or above.
	Description: Provides simpler means of building administration pages for plugin and theme developers.
*/

if ( ! function_exists( 'includeAdminPageFramework' ) ) :
function includeAdminPageFramework() {
	
	$sDirPath = dirname( __FILE__ );
	include_once( $sDirPath . '/utility/AdminPageFramework_RegisterClasses.php' );
	new AdminPageFramework_RegisterClasses( $sDirPath );
	
	
}	
endif;
includeAdminPageFramework();



if ( ! class_exists( 'AdminPageFramework_Help_Page_Base' ) ) :
/**
 * Provides base methods and properties for manipulating the contextual help tabs.
 * 
 * @since			2.1.0
 */
abstract class AdminPageFramework_Help_Page_Base extends AdminPageFramework_Debug {
	
	/**
	 * Stores the screen object.
	 * @var				object
	 * @since			2.1.0
	 */ 
	protected $oScreen;
	
	/**
	 * Sets the contextual help tab.
	 * 
	 * On contrary to other methods relating to contextual help tabs that just modify the class properties, this finalizes the help tab contents.
	 * In other words, the set values here will take effect.
	 * 
	 * @access			protected
	 * @remark			The sidebar contents in the help pane can be set but if it's called from the meta box class and the page loads in regular post types, the sidebar text may be overridden by the default one.
	 * @since			2.1.0
	 */  
	protected function setHelpTab( $sID, $sTitle, $aContents, $aSideBarContents=array() ) {
		
		if ( empty( $aContents ) ) return;
		
		$this->oScreen = isset( $this->oScreen ) ? $this->oScreen : get_current_screen();
		$this->oScreen->add_help_tab( 
			array(
				'id'	=> $sID,
				'title'	=> $sTitle,
				'content'	=> implode( PHP_EOL, $aContents ),
			) 
		);						
		
		if ( ! empty( $aSideBarContents ) )
			$this->oScreen->set_help_sidebar( implode( PHP_EOL, $aSideBarContents ) );
			
	}
	
	/**
	 * Encloses the given string with the contextual help specific tag.
	 * @since			2.1.0
	 * @internal
	 */ 
	protected function formatHelpDescription( $sHelpDescription ) {
		return "<div class='contextual-help-description'>" . $sHelpDescription . "</div>";
	}
}
endif;

if ( ! class_exists( 'AdminPageFramework_Help_MetaBox' ) ) :
/**
 * Provides methods to manipulate the contextual help tab .
 * 
 * @since			2.1.0
 * @extends			AdminPageFramework_Help_Page_Base
 */
abstract class AdminPageFramework_Help_MetaBox extends AdminPageFramework_Help_Page_Base {
	
	/**
	 * Adds the given HTML text to the contextual help pane.
	 * 
	 * The help tab will be the meta box title and all the added text will be inserted into the content area within the tab.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->addHelpText( 
	 *		__( 'This text will appear in the contextual help pane.', 'admin-page-framework-demo' ), 
	 *		__( 'This description goes to the sidebar of the help pane.', 'admin-page-framework-demo' )
	 *	);</code>
	 * 
	 * @since			2.1.0
	 * @remark			This method just adds the given text into the class property. The actual registration will be performed with the <em>registerHelpTabTextForMetaBox()</em> method.
	 * @remark			The user may use this method to add contextual help text.
	 */ 
	protected function addHelpText( $sHTMLContent, $sHTMLSidebarContent="" ) {
		$this->oProps->aHelpTabText[] = "<div class='contextual-help-description'>" . $sHTMLContent . "</div>";
		$this->oProps->aHelpTabTextSide[] = "<div class='contextual-help-description'>" . $sHTMLSidebarContent . "</div>";
	}
	
	/**
	 * Adds the given HTML text to the contextual help pane.
	 * 
	 * The help tab will be the meta box title and all the added text will be inserted into the content area within the tab.
	 * On contrary to the <em>addHelpTab()</em> method of the AdminPageFramework_Help_Page class, the help tab title is already determined and the meta box ID and the title will be used.
	 * 
	 * @since			2.1.0
	 * @uses			addHelpText()
	 * @remark			This method just adds the given text into the class property. The actual registration will be performed with the <em>registerHelpTabTextForMetaBox()</em> method.
	 */ 	
	protected function addHelpTextForFormFields( $sFieldTitle, $sHelpText, $sHelpTextSidebar="" ) {
		$this->addHelpText(
			"<span class='contextual-help-tab-title'>" . $sFieldTitle . "</span> - " . PHP_EOL
				. $sHelpText,		
			$sHelpTextSidebar
		);		
	}

	/**
	 * Registers the contextual help tab contents.
	 * 
	 * @internal
	 * @since			2.1.0
	 * @remark			A call back for the <em>load-{page hook}</em> action hook.
	 * @remark			The method name implies that this is for meta boxes. This does not mean this method is only for meta box form fields. Extra help text can be added with the <em>addHelpText()</em> method.
	 */ 
	public function registerHelpTabTextForMetaBox() {
	
		if ( ! in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) ) return;
		if ( isset( $_GET['post_type'] ) && ! in_array( $_GET['post_type'], $this->oProps->aPostTypes ) ) return;
		if ( ! isset( $_GET['post_type'] ) && ! in_array( 'post', $this->oProps->aPostTypes ) ) return;
		if ( isset( $_GET['post'], $_GET['action'] ) && ! in_array( get_post_type( $_GET['post'] ), $this->oProps->aPostTypes ) ) return; // edit post page
		
		$this->setHelpTab( 	// this method is defined in the base class.
			$this->oProps->sMetaBoxID, 
			$this->oProps->sTitle, 
			$this->oProps->aHelpTabText, 
			$this->oProps->aHelpTabTextSide 
		);
		
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_Help_Page' ) ) :
/**
 * Provides methods to manipulate the help screen sections.
 * 
 * @abstract
 * @remark				Shared with the both AdminPageFramework and AdminPageFramework_PostType.
 * @since				2.1.0
 * @package				Admin Page Framework
 * @subpackage			Admin Page Framework - Page
 * @extends				AdminPageFramework_Help_Page_Base
 * @staticvar			array			$_aStructure_HelpTab			stores the array structure of the help tab array.
 */
abstract class AdminPageFramework_Help_Page extends AdminPageFramework_Help_Page_Base {
	
	/**
	 * Represents the structure of help tab array.
	 * 
	 * @since			2.1.0
	 * @internal
	 */ 
	protected static $_aStructure_HelpTab = array(
		'page_slug'					=> null,	// ( mandatory )
		'page_tab_slug'				=> null,	// ( optional )
		'help_tab_title'			=> null,	// ( mandatory )
		'help_tab_id'				=> null,	// ( mandatory )
		'help_tab_content'			=> null,	// ( optional )
		'help_tab_sidebar_content'	=> null,	// ( optional )
	);

	/**
	 * Registers help tabs to the help toggle pane.
	 * 
	 * This adds a user-defined help information into the help screen placed just below the top admin bar.
	 * 
	 * @remark			The callback of the <em>admin_head</em> action hook.
	 * @see				http://codex.wordpress.org/Plugin_API/Action_Reference/load-%28page%29
	 * @remark			the screen object is supported in WordPress 3.3 or above.
	 * @since			2.1.0
	 * @internal
	 */	 
	public function registerHelpTabs() {
			
		$sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
		$sCurrentPageTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : ( isset( $this->oProps->aDefaultInPageTabs[ $sCurrentPageSlug ] ) ? $this->oProps->aDefaultInPageTabs[ $sCurrentPageSlug ] : '' );
		
		if ( empty( $sCurrentPageSlug ) ) return;
		if ( ! $this->oProps->isPageAdded( $sCurrentPageSlug ) ) return;
		
		foreach( $this->oProps->aHelpTabs as $aHelpTab ) {
			
			if ( $sCurrentPageSlug != $aHelpTab['page_slug'] ) continue;
			if ( isset( $aHelpTab['page_tab_slug'] ) && ! empty( $aHelpTab['page_tab_slug'] ) & $sCurrentPageTabSlug != $aHelpTab['page_tab_slug'] ) continue;
				
			$this->setHelpTab( 
				$aHelpTab['sID'], 
				$aHelpTab['title'], 
				$aHelpTab['aContent'], 
				$aHelpTab['aSidebar']
			);
		}
		
	}
	
	/**
	 * Adds the given contextual help tab contents into the property.
	 * 
	 * <h4>Contextual Help Tab Array Structure</h4>
	 * <ul>
	 * 	<li><strong>page_slug</strong> - the page slug of the page that the contextual help tab and its contents are displayed.</li>
	 * 	<li><strong>page_tab_slug</strong> - ( optional ) the tab slug of the page that the contextual help tab and its contents are displayed.</li>
	 * 	<li><strong>help_tab_title</strong> - the title of the contextual help tab.</li>
	 * 	<li><strong>help_tab_id</strong> - the id of the contextual help tab.</li>
	 * 	<li><strong>help_tab_content</strong> - the HTML string content of the the contextual help tab.</li>
	 * 	<li><strong>help_tab_sidebar_content</strong> - ( optional ) the HTML string content of the sidebar of the contextual help tab.</li>
	 * </ul>
	 * 
	 * <h4>Example</h4>
	 * <code>	$this->addHelpTab( 
	 *		array(
	 *			'page_slug'				=> 'first_page',	// ( mandatory )
	 *			// 'page_tab_slug'			=> null,	// ( optional )
	 *			'help_tab_title'			=> 'Admin Page Framework',
	 *			'help_tab_id'				=> 'admin_page_framework',	// ( mandatory )
	 *			'help_tab_content'			=> __( 'This contextual help text can be set with the <em>addHelpTab()</em> method.', 'admin-page-framework' ),
	 *			'help_tab_sidebar_content'	=> __( 'This is placed in the sidebar of the help pane.', 'admin-page-framework' ),
	 *		)
	 *	);</code>
	 * 
	 * @since			2.1.0
	 * @remark			Called when registering setting sections and fields.
	 * @remark			The user may use this method.
	 * @param			array			$aHelpTab				The help tab array. The key structure is explained in the description part.
	 * @return			void
	 */ 
	protected function addHelpTab( $aHelpTab ) {
		
		// Avoid undefined index warnings.
		$aHelpTab = ( array ) $aHelpTab + self::$_aStructure_HelpTab;
		
		// If the key is not set, that means the help tab array is not created yet. So create it and go back.
		if ( ! isset( $this->oProps->aHelpTabs[ $aHelpTab['help_tab_id'] ] ) ) {
			$this->oProps->aHelpTabs[ $aHelpTab['help_tab_id'] ] = array(
				'sID'				=> $aHelpTab['help_tab_id'],
				'title'				=> $aHelpTab['help_tab_title'],
				'aContent'			=> ! empty( $aHelpTab['help_tab_content'] ) ? array( $this->formatHelpDescription( $aHelpTab['help_tab_content'] ) ) : array(),
				'aSidebar'			=> ! empty( $aHelpTab['help_tab_sidebar_content'] ) ? array( $this->formatHelpDescription( $aHelpTab['help_tab_sidebar_content'] ) ) : array(),
				'page_slug'			=> $aHelpTab['page_slug'],
				'page_tab_slug'		=> $aHelpTab['page_tab_slug'],
			);
			return;
		}

		// This line will be reached if the help tab array is already set. In this case, just append an array element into the keys.
		if ( ! empty( $aHelpTab['help_tab_content'] ) )
			$this->oProps->aHelpTabs[ $aHelpTab['help_tab_id']]['aContent'][] = $this->formatHelpDescription( $aHelpTab['help_tab_content'] );
		if ( ! empty( $aHelpTab['help_tab_sidebar_content'] ) )
			$this->oProps->aHelpTabs[ $aHelpTab['help_tab_id'] ]['aSidebar'][] = $this->formatHelpDescription( $aHelpTab['help_tab_sidebar_content'] );
		
	}
	
}
endif;


if ( ! class_exists( 'AdminPageFramework_HeadTag_Base' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag.
 * 
 * @since			2.1.5
 * 
 */
abstract class AdminPageFramework_HeadTag_Base {
	
	/**
	 * Represents the structure of the array for enqueuing scripts and styles.
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 * @since			3.0.0			Moved from the property class.
	 */
	protected static $_aStructure_EnqueuingScriptsAndStyles = array(
		'href' => null,
		'aPostTypes' => array(),		// for meta box class
		'page_slug' => null,	
		'tab_slug' => null,
		'type' => null,		// script or style
		'handle_id' => null,
		'aDependencies' => array(),
        'sVersion' => false,		// although the type should be string, the wp_enqueue_...() functions want false as the default value.
        'translation' => array(),	// only for scripts
        'fInFooter' => false,	// only for scripts
		'sMedia' => 'all',	// only for styles		
	);	
	
	function __construct( $oProps ) {
		
		$this->oProps = $oProps;
		$this->oUtil = new AdminPageFramework_Utility;
				
		// Hook the admin header to insert custom admin stylesheet.
		add_action( 'admin_head', array( $this, 'replyToAddStyle' ) );
		add_action( 'admin_head', array( $this, 'replyToAddScript' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'replyToEnqueueScripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'replyToEnqueueStyles' ) );
		
	}	
	
	/*
	 * Methods that should be overridden in extended classes.
	 */
	public function replyToAddStyle() {}
	public function replyToAddScript() {}
	protected function enqueueSRCByConditoin() {}
 	
	/*
	 * Shared methods
	 */
		
	/**
	 * Performs actual enqueuing items. 
	 * 
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class.
	 * @internal
	 */
	protected function enqueueSRC( $aEnqueueItem ) {
		
		// For styles
		if ( $aEnqueueItem['type'] == 'style' ) {
			wp_enqueue_style( $aEnqueueItem['handle_id'], $aEnqueueItem['sSRC'], $aEnqueueItem['aDependencies'], $aEnqueueItem['sVersion'], $aEnqueueItem['sMedia'] );
			return;
		}
		
		// For scripts
		wp_enqueue_script( $aEnqueueItem['handle_id'], $aEnqueueItem['sSRC'], $aEnqueueItem['aDependencies'], $aEnqueueItem['sVersion'], $aEnqueueItem['fInFooter'] );
		if ( $aEnqueueItem['translation'] ) 
			wp_localize_script( $aEnqueueItem['handle_id'], $aEnqueueItem['handle_id'], $aEnqueueItem['translation'] );
		
	}
	
	/**
	 * Takes care of added enqueuing scripts by page slug and tab slug.
	 * 
	 * @remark			A callback for the admin_enqueue_scripts hook.
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class. Changed the name from enqueueStylesCalback to replyToEnqueueStyles().
	 * @internal
	 */	
	public function replyToEnqueueStyles() {	
		foreach( $this->oProps->aEnqueuingStyles as $sKey => $aEnqueuingStyle ) 
			$this->enqueueSRCByConditoin( $aEnqueuingStyle );
	}
	
	/**
	 * Takes care of added enqueuing scripts by page slug and tab slug.
	 * 
	 * @remark			A callback for the admin_enqueue_scripts hook.
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class. Changed the name from enqueueScriptsCallback to callbackEnqueueScripts().
	 * @internal
	 */
	public function replyToEnqueueScripts() {							
		foreach( $this->oProps->aEnqueuingScripts as $sKey => $aEnqueuingScript ) 
			$this->enqueueSRCByConditoin( $aEnqueuingScript );				
	}
	
}

endif;

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
	public function replyToAddStyle() {
		
		$sPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
		$sTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProps->getDefaultInPageTab( $sPageSlug );
		
		// If the loading page has not been registered nor the plugin page which uses this library, do nothing.
		if ( ! $this->oProps->isPageAdded( $sPageSlug ) ) return;
					
		$oCaller = $this->oProps->getParentObject();
		
		// Print out the filtered styles.
		$sStyle = AdminPageFramework_Properties::$sDefaultStyle . PHP_EOL . $this->oProps->sStyle;
		$sStyle = $this->oUtil->addAndApplyFilters( $oCaller, $this->oUtil->getFilterArrayByPrefix( AdminPageFramework_Page::$aPrefixes['style_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, false ), $sStyle );
		$sStyleIE = AdminPageFramework_Properties::$sDefaultStyleIE . PHP_EOL . $this->oProps->sStyleIE;
		$sStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, $this->oUtil->getFilterArrayByPrefix( AdminPageFramework_Page::$aPrefixes['style_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, false ), $sStyleIE );
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
	public function replyToAddScript() {
		
		$sPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
		$sTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProps->getDefaultInPageTab( $sPageSlug );
		
		// If the loading page has not been registered or not the plugin page which uses this library, do nothing.
		if ( ! $this->oProps->isPageAdded( $sPageSlug ) ) return;

		$oCaller = $this->oProps->getParentObject();
		
		// Print out the filtered scripts.
		echo "<script type='text/javascript' id='admin-page-framework-script'>"
				. $this->oUtil->addAndApplyFilters( $oCaller, $this->oUtil->getFilterArrayByPrefix( AdminPageFramework_Page::$aPrefixes['script_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, false ), $this->oProps->sScript )
			. "</script>";		
		
	}

	/**
	 * Enqueues styles by page slug and tab slug.
	 * 
	 * @since			2.1.5
	 */
	public function enqueueStyles( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		
		$aHandleIDs = array();
		foreach( ( array ) $aSRCs as $sSRC )
			$aHandleIDs[] = $this->enqueueStyle( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
		return $aHandleIDs;
		
	}
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>aDependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>sVersion</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>sMedia</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
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
	public function enqueueStyle( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		
		$sSRC = trim( $sSRC );
		if ( empty( $sSRC ) ) return '';
		if ( isset( $this->oProps->aEnqueuingScripts[ md5( $sSRC ) ] ) ) return '';	// if already set
		
		$sSRC = $this->oUtil->resolveSRC( $sSRC );
		
		$sSRCHash = md5( $sSRC );	// setting the key based on the url prevents duplicate items
		$this->oProps->aEnqueuingStyles[ $sSRCHash ] = $this->oUtil->uniteArrays( 
			( array ) $aCustomArgs,
			array(		
				'sSRC' => $sSRC,
				'page_slug' => $sPageSlug,
				'tab_slug' => $sTabSlug,
				'type' => 'style',
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
	 */
	public function enqueueScripts( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		
		$aHandleIDs = array();
		foreach( ( array ) $aSRCs as $sSRC )
			$aHandleIDs[] = $this->enqueueScript( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
		return $aHandleIDs;
		
	}	
	/**
	 * Enqueues a script by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>aDependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>sVersion</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>fInFooter</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 * 
	 * <h4>Example</h4>
	 * <code>$this->enqueueScript(  
	 *		plugins_url( 'asset/js/test.js' , __FILE__ ),	// source url or path
	 *		'apf_read_me', 	// page slug
	 *		'', 	// tab slug
	 *		array(
	 *			'handle_id' => 'my_script',	// this handle ID also is used as the object name for the translation array below.
	 *			'translation' => array( 
	 *				'a' => 'hello world!',
	 *				'style_handle_id' => $sStyleHandle,	// check the enqueued style handle ID here.
	 *			),
	 *		)
	 *	);</code>
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
	 */
	public function enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		
		$sSRC = trim( $sSRC );
		if ( empty( $sSRC ) ) return '';
		if ( isset( $this->oProps->aEnqueuingScripts[ md5( $sSRC ) ] ) ) return '';	// if already set
		
		$sSRC = $this->oUtil->resolveSRC( $sSRC );
		
		$sSRCHash = md5( $sSRC );	// setting the key based on the url prevents duplicate items
		$this->oProps->aEnqueuingScripts[ $sSRCHash ] = $this->oUtil->uniteArrays( 
			( array ) $aCustomArgs,
			array(		
				'page_slug' => $sPageSlug,
				'tab_slug' => $sTabSlug,
				'sSRC' => $sSRC,
				'type' => 'script',
				'handle_id' => 'script_' . $this->oProps->sClassName . '_' .  ( ++$this->oProps->iEnqueuedScriptIndex ),
			),
			self::$_aStructure_EnqueuingScriptsAndStyles
		);
		return $this->oProps->aEnqueuingScripts[ $sSRCHash ][ 'handle_id' ];
	}
		
	/**
	 * A helper function for the above replyToEnqueueScripts() and replyToEnqueueStyle() methods.
	 * 
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class. Changed the name from enqueueSRCByPageConditoin.
	 */
	protected function enqueueSRCByConditoin( $aEnqueueItem ) {
		
		$sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
		$sCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProps->getDefaultInPageTab( $sCurrentPageSlug );
			
		$sPageSlug = $aEnqueueItem['page_slug'];
		$sTabSlug = $aEnqueueItem['tab_slug'];
		
		// If the page slug is not specified and the currently loading page is one of the pages that is added by the framework,
		if ( ! $sPageSlug && $this->oProps->isPageAdded( $sCurrentPageSlug ) )  // means script-global(among pages added by the framework)
			return $this->enqueueSRC( $aEnqueueItem );
				
		// If both tab and page slugs are specified,
		if ( 
			( $sPageSlug && $sCurrentPageSlug == $sPageSlug )
			&& ( $sTabSlug && $sCurrentTabSlug == $sTabSlug )
		) 
			return $this->enqueueSRC( $aEnqueueItem );
		
		// If the tab slug is not specified and the page slug is specified, 
		// and if the current loading page slug and the specified one matches,
		if ( 
			( $sPageSlug && ! $sTabSlug )
			&& ( $sCurrentPageSlug == $sPageSlug )
		) 
			return $this->enqueueSRC( $aEnqueueItem );

	}
	
}
endif;

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
	 */ 	
	public function replyToAddStyle() {
	
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
	 */ 
	public function replyToAddScript() {

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
	 */
	public function enqueueStyles( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) {
		
		$aHandleIDs = array();
		foreach( ( array ) $aSRCs as $sSRC )
			$aHandleIDs[] = $this->enqueueStyle( $sSRC, $aPostTypes, $aCustomArgs );
		return $aHandleIDs;
		
	}
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>aDependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>sVersion</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>sMedia</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	 * </ul>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.5			
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param			array			$aPostTypes		(optional) The post type slugs that the stylesheet should be added to. If not set, it applies to all the pages of the post types.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */	
	public function enqueueStyle( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) {
		
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
				'type' => 'style',
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
	 */
	public function enqueueScripts( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) {
		
		$aHandleIDs = array();
		foreach( ( array ) $aSRCs as $sSRC )
			$aHandleIDs[] = $this->enqueueScript( $sSRC, $aPostTypes, $aCustomArgs );
		return $aHandleIDs;
		
	}	
	/**
	 * Enqueues a script by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>aDependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>sVersion</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>fInFooter</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 * 
	 * <h4>Example</h4>
	 * <code>$this->enqueueScript(  
	 *		plugins_url( 'asset/js/test.js' , __FILE__ ),	// source url or path
	 *	);</code>
	 * 
	 * @since			2.1.5			
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param			array			$aPostTypes		(optional) The post type slugs that the script should be added to. If not set, it applies to all the pages with the post type slugs.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */
	public function enqueueScript( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) {
		
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
				'type' => 'script',
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
	 */
	protected function enqueueSRCByConditoin( $aEnqueueItem ) {
		
		$sCurrentPostType = isset( $_GET['post_type'] ) ? $_GET['post_type'] : ( isset( $GLOBALS['typenow'] ) ? $GLOBALS['typenow'] : null );
				
		if ( in_array( $sCurrentPostType, $aEnqueueItem['aPostTypes'] ) )		
			return $this->enqueueSRC( $aEnqueueItem );
			
	}
	
	
}
endif;

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
	 */ 	
	public function replyToAddStyle() {
	
		// If it's not the post type's post listing page or the taxtonomy page
		if ( 
			! (
				in_array( $GLOBALS['pagenow'], array( 'edit.php', 'edit-tags.php' ) ) 
				&& ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->oProps->sPostType )				
			)
		) return;	
	
		// Some users sets $_GET['post_type'] element even in regular admin pages. In that case, do not load the style to avoid duplicates.
		if ( isset( $_GET['page'] ) && $_GET['page'] ) return;
	
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
	 */ 
	public function replyToAddScript() {

		// If it's not the post type's post listing page
		if ( 
			! (
				in_array( $GLOBALS['pagenow'], array( 'edit.php', 'edit-tags.php' ) ) 
				&& ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->oProps->sPostType )				
			)
		) return;	
		
		// Some users sets $_GET['post_type'] element even in regular admin pages. In that case, do not load the style to avoid duplicates.
		if ( isset( $_GET['page'] ) && $_GET['page'] ) return;
	
		// This class may be instantiated multiple times so use a global flag.
		$sRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$sRootClassName}_ScriptLoaded" ] ) && $GLOBALS[ "{$sRootClassName}_ScriptLoaded" ] ) return;
		$GLOBALS[ "{$sRootClassName}_ScriptLoaded" ] = true;
	
		$oCaller = $this->oProps->getParentObject();
		
		// Print out the filtered scripts.
		$sScript = $this->oUtil->addAndApplyFilters( $oCaller, "script_{$this->oProps->sClassName}", $this->oProps->sScript );
		if ( ! empty( $sScript ) )
			echo 
				"<script type='text/javascript' id='admin-page-framework-script-post-type'>"
					. $sScript
				. "</script>";	
			
	}	
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_Page' ) ) :
/**
 * Provides methods to render admin page elements.
 *
 * @abstract
 * @since			2.0.0
 * @since			2.1.0		Extends AdminPageFramework_Help_Page.
 * @extends			AdminPageFramework_Help_Page
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Page
 * @staticvar		array		$aPrefixes						stores the prefix strings for filter and action hooks.
 * @staticvar		array		$aPrefixesForCallbacks			unlike $aPrefixes, these require to set the return value.
 * @staticvar		array		$aScreenIconIDs					stores the ID selector names for screen icons.
 * @staticvar		array		$aPrefixes						stores the prefix strings for filter and action hooks.
 * @staticvar		array		$_aStructure_InPageTabElements		represents the array structure of an in-page tab array.
 */
abstract class AdminPageFramework_Page extends AdminPageFramework_Help_Page {
			
	/**
	 * Stores the prefixes of the filters used by this framework.
	 * 
	 * This must not use the private scope as the extended class accesses it, such as 'start_' and must use the public since another class uses this externally.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Made it public from protected since the HeadTag class accesses it.
	 * @var				array
	 * @static
	 * @access			public
	 * @internal
	 */ 
	public static $aPrefixes = array(	
		'start_'		=> 'start_',
		'load_'			=> 'load_',
		'do_before_'	=> 'do_before_',
		'do_after_'		=> 'do_after_',
		'do_form_'		=> 'do_form_',
		'do_'			=> 'do_',
		'head_'			=> 'head_',
		'content_'		=> 'content_',
		'foot_'			=> 'foot_',
		'validation_'	=> 'validation_',
		'export_name'	=> 'export_name',
		'export_format' => 'export_format',
		'export_'		=> 'export_',
		'import_name'	=> 'import_name',
		'import_format'	=> 'import_format',
		'import_'		=> 'import_',
		'style_'		=> 'style_',
		'script_'		=> 'script_',
		'field_'		=> 'field_',
		'section_'		=> 'section_',
	);

	/**
	 * Unlike $aPrefixes, these require to set the return value.
	 * 
	 * @since			2.0.0
	 * @var				array
	 * @static
	 * @access			protected
	 * @internal
	 */ 	
	protected static $aPrefixesForCallbacks = array(
		'section_'		=> 'section_',
		'field_'		=> 'field_',
		'field_types_'	=> 'field_types_',
		'validation_'	=> 'validation_',
	);
	
	/**
	 * Stores the ID selector names for screen icons. <em>generic</em> is not available in WordPress v3.4.x.
	 * 
	 * @since			2.0.0
	 * @var				array
	 * @static
	 * @access			protected
	 * @internal
	 */ 	
	protected static $aScreenIconIDs = array(
		'edit', 'post', 'index', 'media', 'upload', 'link-manager', 'link', 'link-category', 
		'edit-pages', 'page', 'edit-comments', 'themes', 'plugins', 'users', 'profile', 
		'user-edit', 'tools', 'admin', 'options-general', 'ms-admin', 'generic',
	);	

	/**
	 * Represents the array structure of an in-page tab array.
	 * 
	 * @since			2.0.0
	 * @var				array
	 * @static
	 * @access			private
	 * @internal
	 */ 	
	private static $_aStructure_InPageTabElements = array(
		'page_slug' => null,
		'tab_slug' => null,
		'title' => null,
		'order' => null,
		'show_inpage_tab'	=> null,
		'parent_tab_slug' => null,	// this needs to be set if the above show_inpage_tab is true so that the plugin can mark the parent tab to be active when the hidden page is accessed.
	);
	
		
	/**
	 * Sets whether the page title is displayed or not.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setPageTitleVisibility( false );    // disables the page title.</code>
	 * 
	 * @since			2.0.0
	 * @param			boolean			$bShow			If false, the page title will not be displayed.
	 * @remark			The user may use this method.
	 * @return			void
	 */ 
	protected function setPageTitleVisibility( $bShow=true, $sPageSlug='' ) {
		$sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
		if ( ! empty( $sPageSlug ) )
			$this->oProps->aPages[ $sPageSlug ]['fShowPageTitle'] = $bShow;
		else {
			$this->oProps->bShowPageTitle = $bShow;
			foreach( $this->oProps->aPages as &$aPage ) 
				$aPage['fShowPageTitle'] = $bShow;
		}
	}	
	
	/**
	 * Sets whether page-heading tabs are displayed or not.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setPageHeadingTabsVisibility( false );    // disables the page heading tabs by passing false.</code>
	 * 
	 * @since			2.0.0
	 * @param			boolean			$bShow					If false, page-heading tabs will be disabled; otherwise, enabled.
	 * @param			string			$sPageSlug			The page to apply the visibility setting. If not set, it applies to all the pages.
	 * @remark			Page-heading tabs and in-page tabs are different. The former displays page titles and the latter displays tab titles.
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */ 
	protected function setPageHeadingTabsVisibility( $bShow=true, $sPageSlug='' ) {
		$sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
		if ( ! empty( $sPageSlug ) )
			$this->oProps->aPages[ $sPageSlug ]['fShowPageHeadingTabs'] = $bShow;
		else {
			$this->oProps->bShowPageHeadingTabs = $bShow;
			foreach( $this->oProps->aPages as &$aPage ) 
				$aPage['fShowPageHeadingTabs'] = $bShow;
		}
	}
	
	/**
	 * Sets whether in-page tabs are displayed or not.
	 * 
	 * Sometimes, it is required to disable in-page tabs in certain pages. In that case, use the second parameter.
	 * 
	 * @since			2.1.1
	 * @param			boolean			$bShow				If false, in-page tabs will be disabled.
	 * @param			string			$sPageSlug		The page to apply the visibility setting. If not set, it applies to all the pages.
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */
	protected function showInPageTabs( $bShow=true, $sPageSlug='' ) {
		$sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
		if ( ! empty( $sPageSlug ) )
			$this->oProps->aPages[ $sPageSlug ]['fShowInPageTabs'] = $bShow;
		else {
			$this->oProps->bShowInPageTabs = $bShow;
			foreach( $this->oProps->aPages as &$aPage )
				$aPage['fShowInPageTabs'] = $bShow;
		}
	}
	
	/**
	 * Sets in-page tab's HTML tag.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setInPageTabTag( 'h2' );</code>
	 * 
	 * @since			2.0.0
	 * @param			string			$sTag					The HTML tag that encloses each in-page tab title. Default: h3.
	 * @param			string			$sPageSlug			The page slug that applies the setting.	
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */ 	
	protected function setInPageTabTag( $sTag='h3', $sPageSlug='' ) {
		$sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
		if ( ! empty( $sPageSlug ) )
			$this->oProps->aPages[ $sPageSlug ]['sInPageTabTag'] = $sTag;
		else {
			$this->oProps->sInPageTabTag = $sTag;
			foreach( $this->oProps->aPages as &$aPage )
				$aPage['sInPageTabTag'] = $sTag;
		}
	}
	
	/**
	 * Sets page-heading tab's HTML tag.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setPageHeadingTabTag( 'h2' );</code>
	 * 
	 * @since			2.1.2
	 * @param			string			$sTag					The HTML tag that encloses the page-heading tab title. Default: h2.
	 * @param			string			$sPageSlug			The page slug that applies the setting.	
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */
	protected function setPageHeadingTabTag( $sTag='h2', $sPageSlug='' ) {
		$sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
		if ( ! empty( $sPageSlug ) )
			$this->oProps->aPages[ $sPageSlug ]['sPageHeadingTabTag'] = $sTag;
		else {
			$this->oProps->sPageHeadingTabTag = $sTag;
			foreach( $this->oProps->aPages as &$aPage )
				$aPage[ $sPageSlug ]['sPageHeadingTabTag'] = $sTag;
		}
	}
	
	/**
	 * Renders the admin page.
	 * 
	 * @remark			This is not intended for the users to use.
	 * @since			2.0.0
	 * @access			protected
	 * @return			void
	 * @internal
	 */ 
	protected function renderPage( $sPageSlug, $sTabSlug=null ) {

		// Do actions before rendering the page. In this order, global -> page -> in-page tab
		$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$aPrefixes['do_before_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, true ) );	
		?>
		<div class="wrap">
			<?php
				// Screen icon, page heading tabs(page title), and in-page tabs.
				$sHead = $this->getScreenIcon( $sPageSlug );	
				$sHead .= $this->getPageHeadingTabs( $sPageSlug, $this->oProps->sPageHeadingTabTag ); 	
				$sHead .= $this->getInPageTabs( $sPageSlug, $this->oProps->sInPageTabTag );

				// Apply filters in this order, in-page tab -> page -> global.
				echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$aPrefixes['head_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, false ), $sHead );
			?>
			<div class="admin-page-framework-container">
				<?php
					$this->showSettingsErrors();
						
					$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$aPrefixes['do_form_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, true ) );	

					echo $this->getFormOpeningTag();	// <form ... >
					
					// Capture the output buffer
					ob_start(); // start buffer
							 					
					// Render the form elements by Settings API
					if ( $this->oProps->bEnableForm ) {
						settings_fields( $this->oProps->sOptionKey );	// this value also determines the $option_page global variable value.
						do_settings_sections( $sPageSlug ); 
					}				
					 
					$sContent = ob_get_contents(); // assign the content buffer to a variable
					ob_end_clean(); // end buffer and remove the buffer
								
					// Apply the content filters.
					echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$aPrefixes['content_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, false ), $sContent );
	
					// Do the page actions.
					$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$aPrefixes['do_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, true ) );	
						
				?>
				
			<?php echo $this->getFormClosingTag( $sPageSlug, $sTabSlug );  ?>
			
			</div><!-- End admin-page-framework-container -->
				
			<?php	
				// Apply the foot filters.
				echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$aPrefixes['foot_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, false ), '' );	// empty string
			?>
		</div><!-- End Wrap -->
		<?php
		// Do actions after rendering the page.
		$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$aPrefixes['do_after_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, true ) );
		
	}
	
	/**
	 * Displays admin notices set for the settings.
	 * 
	 * @global			$pagenow
	 * @since			2.0.0
	 * @since			2.0.1			Fixed a bug that the admin messages were displayed twice in the options-general.php page.
	 * @internal		
	 * @return			void
	 */ 
	private function showSettingsErrors() {
		
		// WordPress automatically performs the settings_errors() function in the options pages. See options-head.php.
		if ( $GLOBALS['pagenow'] == 'options-general.php' ) return;	
		
		$aSettingsMessages = get_settings_errors( $this->oProps->sOptionKey );
		
		// If custom messages are added, remove the default one. 
		if ( count( $aSettingsMessages ) > 1 ) 
			$this->removeDefaultSettingsNotice();
		
		settings_errors( $this->oProps->sOptionKey );	// Show the message like "The options have been updated" etc.
	
	}

	/**
	 * Removes default admin notices set for the settings.
	 * 
	 * This removes the settings messages ( admin notice ) added automatically by the framework when the form is submitted.
	 * This is used when a custom message is added manually and the default message should not be displayed.
	 * 
	 * @since			2.0.0
	 * @internal
	 */	
	protected function removeDefaultSettingsNotice() {
				
		global $wp_settings_errors;
		/*
		 * The structure of $wp_settings_errors
		 * 	array(
		 *		array(
					'setting' => $setting,
					'code' => $code,
					'message' => $message,
					'type' => $type
				),
				array( ...
			)
		 * */
		
		$aDefaultMessages = array(
			$this->oMsg->__( 'option_cleared' ),
			$this->oMsg->__( 'option_updated' ),
		);
		
		foreach ( ( array ) $wp_settings_errors as $iIndex => $aDetails ) {
			
			if ( $aDetails['setting'] != $this->oProps->sOptionKey ) continue;
			
			if ( in_array( $aDetails['message'], $aDefaultMessages ) )
				unset( $wp_settings_errors[ $iIndex ] );
				
		}
	}
	
	/**
	 * Retrieves the form opening tag.
	 * 
	 * @since			2.0.0
	 * @internal
	 */ 
	protected function getFormOpeningTag() {
		
		if ( ! $this->oProps->bEnableForm ) return '';
		return "<form action='options.php' method='post' enctype='{$this->oProps->sFormEncType}'>";
	
	}
	
	/**
	 * Retrieves the form closing tag.
	 * 
	 * @since			2.0.0
	 * @internal
	 */ 	
	protected function getFormClosingTag( $sPageSlug, $sTabSlug ) {

		if ( ! $this->oProps->bEnableForm ) return '';	
		return "<input type='hidden' name='page_slug' value='{$sPageSlug}' />" . PHP_EOL
			. "<input type='hidden' name='tab_slug' value='{$sTabSlug}' />" . PHP_EOL			
			. "</form><!-- End Form -->";
	
	}	
	
	/**
	 * Retrieves the screen icon output as HTML.
	 * 
	 * @remark			the screen object is supported in WordPress 3.3 or above.
	 * @since			2.0.0
	 */ 	
	private function getScreenIcon( $sPageSlug ) {

		// If the icon path is explicitly set, use it.
		if ( isset( $this->oProps->aPages[ $sPageSlug ]['hrefIcon32x32'] ) ) 
			return '<div class="icon32" style="background-image: url(' . $this->oProps->aPages[ $sPageSlug ]['hrefIcon32x32'] . ');"><br /></div>';
		
		// If the screen icon ID is explicitly set, use it.
		if ( isset( $this->oProps->aPages[ $sPageSlug ]['screen_iconID'] ) )
			return '<div class="icon32" id="icon-' . $this->oProps->aPages[ $sPageSlug ]['screen_iconID'] . '"><br /></div>';
			
		// Retrieve the screen object for the current page.
		$oScreen = get_current_screen();
		$sIconIDAttribute = $this->getScreenIDAttribute( $oScreen );

		$sClass = 'icon32';
		if ( empty( $sIconIDAttribute ) && $oScreen->post_type ) 
			$sClass .= ' ' . sanitize_html_class( 'icon32-posts-' . $oScreen->post_type );
		
		if ( empty( $sIconIDAttribute ) || $sIconIDAttribute == $this->oProps->sClassName )
			$sIconIDAttribute = 'generic';		// the default value
		
		return '<div id="icon-' . $sIconIDAttribute . '" class="' . $sClass . '"><br /></div>';
			
	}
	
	/**
	 * Retrieves the screen ID attribute from the given screen object.
	 * 
	 * @since			2.0.0
	 */ 	
	private function getScreenIDAttribute( $oScreen ) {
		
		if ( ! empty( $oScreen->parent_base ) )
			return $oScreen->parent_base;
	
		if ( 'page' == $oScreen->post_type )
			return 'edit-pages';		
			
		return esc_attr( $oScreen->base );
		
	}

	/**
	 * Retrieves the output of page heading tab navigation bar as HTML.
	 * 
	 * @since			2.0.0
	 * @return			string			the output of page heading tabs.
	 */ 		
	private function getPageHeadingTabs( $sCurrentPageSlug, $sTag='h2', $aOutput=array() ) {
		
		// If the page title is disabled, return an empty string.
		if ( ! $this->oProps->aPages[ $sCurrentPageSlug ][ 'fShowPageTitle' ] ) return "";

		$sTag = $this->oProps->aPages[ $sCurrentPageSlug ][ 'sPageHeadingTabTag' ]
			? $this->oProps->aPages[ $sCurrentPageSlug ][ 'sPageHeadingTabTag' ]
			: $sTag;
	
		// If the page heading tab visibility is disabled, return the title.
		if ( ! $this->oProps->aPages[ $sCurrentPageSlug ][ 'fShowPageHeadingTabs' ] )
			return "<{$sTag}>" . $this->oProps->aPages[ $sCurrentPageSlug ]['title'] . "</{$sTag}>";		
		
		foreach( $this->oProps->aPages as $aSubPage ) {
			
			// For added sub-pages
			if ( isset( $aSubPage['page_slug'] ) && $aSubPage['fShowPageHeadingTab'] ) {
				// Check if the current tab number matches the iteration number. If not match, then assign blank; otherwise put the active class name.
				$sClassActive =  $sCurrentPageSlug == $aSubPage['page_slug']  ? 'nav-tab-active' : '';		
				$aOutput[] = "<a class='nav-tab {$sClassActive}' "
					. "href='" . $this->oUtil->getQueryAdminURL( array( 'page' => $aSubPage['page_slug'], 'tab' => false ), $this->oProps->aDisallowedQueryKeys ) 
					. "'>"
					. $aSubPage['title']
					. "</a>";	
			}
			
			// For added menu links
			if ( 
				isset( $aSubPage['href'] )
				&& $aSubPage['type'] == 'link' 
				&& $aSubPage['fShowPageHeadingTab']
			) 
				$aOutput[] = "<a class='nav-tab link' "
					. "href='{$aSubPage['href']}'>"
					. $aSubPage['title']
					. "</a>";					
			
		}
		return "<div class='admin-page-framework-page-heading-tab'><{$sTag} class='nav-tab-wrapper'>" 
			.  implode( '', $aOutput ) 
			. "</{$sTag}></div>";
		
	}

	/**
	 * Retrieves the output of in-page tab navigation bar as HTML.
	 * 
	 * @since			2.0.0
	 * @return			string			the output of in-page tabs.
	 */ 	
	private function getInPageTabs( $sCurrentPageSlug, $sTag='h3', $aOutput=array() ) {
		
		// If in-page tabs are not set, return an empty string.
		if ( empty( $this->oProps->aInPageTabs[ $sCurrentPageSlug ] ) ) return implode( '', $aOutput );
				
		// Determine the current tab slug.
		$sCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProps->getDefaultInPageTab( $sCurrentPageSlug );
		$sCurrentTabSlug = $this->getParentTabSlug( $sCurrentPageSlug, $sCurrentTabSlug );
		
		$sTag = $this->oProps->aPages[ $sCurrentPageSlug ][ 'sInPageTabTag' ]
			? $this->oProps->aPages[ $sCurrentPageSlug ][ 'sInPageTabTag' ]
			: $sTag;
	
		// If the in-page tabs' visibility is set to false, returns the title.
		if ( ! $this->oProps->aPages[ $sCurrentPageSlug ][ 'fShowInPageTabs' ]	)
			return isset( $this->oProps->aInPageTabs[ $sCurrentPageSlug ][ $sCurrentTabSlug ]['title'] ) 
				? "<{$sTag}>{$this->oProps->aInPageTabs[ $sCurrentPageSlug ][ $sCurrentTabSlug ]['title']}</{$sTag}>" 
				: "";
	
		// Get the actual string buffer.
		foreach( $this->oProps->aInPageTabs[ $sCurrentPageSlug ] as $sTabSlug => $aInPageTab ) {
					
			// If it's hidden and its parent tab is not set, skip
			if ( $aInPageTab['show_inpage_tab'] && ! isset( $aInPageTab['parent_tab_slug'] ) ) continue;
			
			// The parent tab means the root tab when there is a hidden tab that belongs to it. Also check it the specified parent tab exists.
			$sInPageTabSlug = isset( $aInPageTab['parent_tab_slug'], $this->oProps->aInPageTabs[ $sCurrentPageSlug ][ $aInPageTab['parent_tab_slug'] ] ) 
				? $aInPageTab['parent_tab_slug'] 
				: $aInPageTab['tab_slug'];
				
			// Check if the current tab slug matches the iteration slug. If not match, assign blank; otherwise, put the active class name.
			$bIsActiveTab = ( $sCurrentTabSlug == $sInPageTabSlug );
			$aOutput[ $sInPageTabSlug ] = "<a class='nav-tab " . ( $bIsActiveTab ? "nav-tab-active" : "" ) . "' "
				. "href='" . $this->oUtil->getQueryAdminURL( array( 'page' => $sCurrentPageSlug, 'tab' => $sInPageTabSlug ), $this->oProps->aDisallowedQueryKeys ) 
				. "'>"
				. $this->oProps->aInPageTabs[ $sCurrentPageSlug ][ $sInPageTabSlug ]['title'] //	"{$aInPageTab['title']}"
				. "</a>";
		
		}		
		
		return empty( $aOutput )
			? ""
			: "<div class='admin-page-framework-in-page-tab'><{$sTag} class='nav-tab-wrapper in-page-tab'>" 
					. implode( '', $aOutput )
				. "</{$sTag}></div>";
			
	}

	/**
	 * Retrieves the parent tab slug from the given tab slug.
	 * 
	 * @since			2.0.0
	 * @since			2.1.2			If the parent slug has the show_inpage_tab to be true, it returns an empty string.
	 * @return			string			the parent tab slug.
	 */ 	
	private function getParentTabSlug( $sPageSlug, $sTabSlug ) {
		
		$sParentTabSlug = isset( $this->oProps->aInPageTabs[ $sPageSlug ][ $sTabSlug ]['parent_tab_slug'] ) 
			? $this->oProps->aInPageTabs[ $sPageSlug ][ $sTabSlug ]['parent_tab_slug']
			: $sTabSlug;
		
		return isset( $this->oProps->aInPageTabs[ $sPageSlug ][ $sParentTabSlug ]['show_inpage_tab'] ) && $this->oProps->aInPageTabs[ $sPageSlug ][ $sParentTabSlug ]['show_inpage_tab']
			? ""
			: $sParentTabSlug;

	}

	/**
	 * Adds an in-page tab.
	 * 
	 * @since			2.0.0
	 * @param			string			$sPageSlug			The page slug that the tab belongs to.
	 * @param			string			$sTabTitle			The title of the tab.
	 * @param			string			$sTabSlug				The tab slug. Non-alphabetical characters should not be used including dots(.) and hyphens(-).
	 * @param			integer			$nOrder				( optional ) the order number of the tab. The lager the number is, the lower the position it is placed in the menu.
	 * @param			boolean			$bHide					( optional ) default: false. If this is set to false, the tab title will not be displayed in the tab navigation menu; however, it is still accessible from the direct URL.
	 * @param			string			$sParentTabSlug		( optional ) this needs to be set if the above show_inpage_tab is true so that the parent tab will be emphasized as active when the hidden page is accessed.
	 * @remark			Use this method to add in-page tabs to ensure the array holds all the necessary keys.
	 * @remark			In-page tabs are different from page-heading tabs which is automatically added with page titles.
	 * @return			void
	 */ 		
	protected function addInPageTab( $sPageSlug, $sTabTitle, $sTabSlug, $nOrder=null, $bHide=null, $sParentTabSlug=null ) {	
		
		$sTabSlug = $this->oUtil->sanitizeSlug( $sTabSlug );
		$sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
		$iCountElement = isset( $this->oProps->aInPageTabs[ $sPageSlug ] ) ? count( $this->oProps->aInPageTabs[ $sPageSlug ] ) : 0;
		if ( ! empty( $sTabSlug ) && ! empty( $sPageSlug ) ) 
			$this->oProps->aInPageTabs[ $sPageSlug ][ $sTabSlug ] = array(
				'page_slug'	=> $sPageSlug,
				'title'		=> trim( $sTabTitle ),
				'tab_slug'	=> $sTabSlug,
				'order'		=> is_numeric( $nOrder ) ? $nOrder : $iCountElement + 10,
				'show_inpage_tab'			=> ( $bHide ),
				'parent_tab_slug' => ! empty( $sParentTabSlug ) ? $this->oUtil->sanitizeSlug( $sParentTabSlug ) : null,
			);
	
	}
	/**
	 * Adds in-page tabs.
	 *
	 * The parameters accept in-page tab arrays and they must have the following array keys.
	 * <h4>In-Page Tab Array</h4>
	 * <ul>
	 * 	<li><strong>page_slug</strong> - ( string ) the page slug that the tab belongs to.</li>
	 * 	<li><strong>tab_slug</strong> -  ( string ) the tab slug. Non-alphabetical characters should not be used including dots(.) and hyphens(-).</li>
	 * 	<li><strong>title</strong> - ( string ) the title of the tab.</li>
	 * 	<li><strong>order</strong> - ( optional, integer ) the order number of the tab. The lager the number is, the lower the position it is placed in the menu.</li>
	 * 	<li><strong>show_inpage_tab</strong> - ( optional, boolean ) default: false. If this is set to false, the tab title will not be displayed in the tab navigation menu; however, it is still accessible from the direct URL.</li>
	 * 	<li><strong>parent_tab_slug</strong> - ( optional, string ) this needs to be set if the above show_inpage_tab is true so that the parent tab will be emphasized as active when the hidden page is accessed.</li>
	 * </ul>
	 * 
	 * <h4>Example</h4>
	 * <code>$this->addInPageTabs(
	 *		array(
	 *			'tab_slug' => 'firsttab',
	 *			'title' => __( 'Text Fields', 'my-text-domain' ),
	 *			'page_slug' => 'myfirstpage'
	 *		),
	 *		array(
	 *			'tab_slug' => 'secondtab',
	 *			'title' => __( 'Selectors and Checkboxes', 'my-text-domain' ),
	 *			'page_slug' => 'myfirstpage'
	 *		)
	 *	);</code>
	 * 
	 * @since			2.0.0
	 * @param			array			$aTab1			The in-page tab array.
	 * @param			array			$aTab2			Another in-page tab array.
	 * @param			array			$_and_more			Add in-page tab arrays as many as necessary to the next parameters.
	 * @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	 * @remark			In-page tabs are different from page-heading tabs which is automatically added with page titles.	 
	 * @return			void
	 */ 			
	protected function addInPageTabs( $aTab1, $aTab2=null, $_and_more=null ) {
		
		foreach( func_get_args() as $aTab ) {
			if ( ! is_array( $aTab ) ) continue;
			$aTab = $aTab + self::$_aStructure_InPageTabElements;	// avoid undefined index warnings.
			$this->addInPageTab( $aTab['page_slug'], $aTab['title'], $aTab['tab_slug'], $aTab['order'], $aTab['show_inpage_tab'], $aTab['parent_tab_slug'] );
		}
		
	}

	/**
	 * Finalizes the in-page tab property array.
	 * 
	 * This finalizes the added in-page tabs and sets the default in-page tab for each page.
	 * Also this sorts the in-page tab property array.
	 * This must be done before registering settings sections because the default tab needs to be determined in the process.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>admin_menu</em> hook.
	 * @return			void
	 */ 		
	public function finalizeInPageTabs() {
	
		foreach( $this->oProps->aPages as $sPageSlug => $aPage ) {
			
			if ( ! isset( $this->oProps->aInPageTabs[ $sPageSlug ] ) ) continue;
			
			// Apply filters to let modify the in-page tab array.
			$this->oProps->aInPageTabs[ $sPageSlug ] = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
				$this,
				"{$this->oProps->sClassName}_{$sPageSlug}_tabs",
				$this->oProps->aInPageTabs[ $sPageSlug ]			
			);	
			// Added in-page arrays may be missing necessary keys so merge them with the default array structure.
			foreach( $this->oProps->aInPageTabs[ $sPageSlug ] as &$aInPageTab ) 
				$aInPageTab = $aInPageTab + self::$_aStructure_InPageTabElements;
						
			// Sort the in-page tab array.
			uasort( $this->oProps->aInPageTabs[ $sPageSlug ], array( $this->oProps, 'sortByOrder' ) );
			
			// Set the default tab for the page.
			// Read the value as reference; otherwise, a strange bug occurs. It may be due to the variable name, $aInPageTab, is also used as reference in the above foreach.
			foreach( $this->oProps->aInPageTabs[ $sPageSlug ] as $sTabSlug => &$aInPageTab ) { 	
			
				if ( ! isset( $aInPageTab['tab_slug'] ) ) continue;	
				
				// Regardless of whether it's a hidden tab, it is stored as the default in-page tab.
				$this->oProps->aDefaultInPageTabs[ $sPageSlug ] = $aInPageTab['tab_slug'];
					
				break;	// The first iteration item is the default one.
			}
		}
	}			

}
endif;

if ( ! class_exists( 'AdminPageFramework_Menu' ) ) :
/**
 * Provides methods to manipulate menu items.
 *
 * @abstract
 * @since			2.0.0
 * @extends			AdminPageFramework_Page
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Page
 * @staticvar		array	$aBuiltInRootMenuSlugs	stores the WordPress built-in menu slugs.
 * @staticvar		array	$_aStructure_SubMenuPage	represents the structure of the sub-menu page array.
 */
abstract class AdminPageFramework_Menu extends AdminPageFramework_Page {
	
	/**
	 * Used to refer the built-in root menu slugs.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds the built-in root menu slugs.
	 * @static
	 * @internal
	 */ 
	protected static $aBuiltInRootMenuSlugs = array(
		// All keys must be lower case to support case insensitive look-ups.
		'dashboard' => 			'index.php',
		'posts' => 				'edit.php',
		'media' => 				'upload.php',
		'links' => 				'link-manager.php',
		'pages' => 				'edit.php?post_type=page',
		'comments' => 			'edit-comments.php',
		'appearance' => 		'themes.php',
		'plugins' => 			'plugins.php',
		'users' => 				'users.php',
		'tools' => 				'tools.php',
		'settings' => 			'options-general.php',
		'network admin' => 		"network_admin_menu",
	);		

	/**
	 * Represents the structure of sub-menu page array.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds array structure of sub-menu page.
	 * @static
	 * @internal
	 */ 
	protected static $_aStructure_SubMenuPage = array(
		'title' => null, 
		'page_slug' => null, 
		'screen_icon' => null,
		'sCapability' => null, 
		'order' => null,
		'fShowPageHeadingTab' => true,	// if this is false, the page title won't be displayed in the page heading tab.
		'fShowInMenu' => true,	// if this is false, the menu label will not be displayed in the sidebar menu.
	);
	 
	/**
	 * Sets to which top level page is going to be adding sub-pages.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setRootMenuPage( 'Settings' );</code>
	 * <code>$this->setRootMenuPage( 
	 * 	'APF Form',
	 * 	plugins_url( 'image/screen_icon32x32.jpg', __FILE__ )
	 * );</code>
	 * 
	 * @since			2.0.0
	 * @since			2.1.6			The $sURLIcon16x16 parameter accepts a file path.
	 * @remark			Only one root page can be set per one class instance.
	 * @param			string			$sRootMenuLabel			If the method cannot find the passed string from the following listed items, it will create a top level menu item with the passed string. ( case insensitive )
	 * <blockquote>Dashboard, Posts, Media, Links, Pages, Comments, Appearance, Plugins, Users, Tools, Settings, Network Admin</blockquote>
	 * @param			string			$sURLIcon16x16			( optional ) the URL or the file path of the menu icon. The size should be 16 by 16 in pixel.
	 * @param			string			$iMenuPosition			( optional ) the position number that is passed to the <var>$position</var> parameter of the <a href="http://codex.wordpress.org/Function_Reference/add_menu_page">add_menu_page()</a> function.
	 * @return			void
	 */
	protected function setRootMenuPage( $sRootMenuLabel, $sURLIcon16x16=null, $iMenuPosition=null ) {

		$sRootMenuLabel = trim( $sRootMenuLabel );
		$sSlug = $this->isBuiltInMenuItem( $sRootMenuLabel );	// if true, this method returns the slug
		$this->oProps->aRootMenu = array(
			'title'			=> $sRootMenuLabel,
			'page_slug' 		=> $sSlug ? $sSlug : $this->oProps->sClassName,	
			'hrefIcon16x16'	=> $this->oUtil->resolveSRC( $sURLIcon16x16, true ),
			'intPosition'		=> $iMenuPosition,
			'fCreateRoot'		=> $sSlug ? false : true,
		);	
					
	}
	
	/**
	 * Sets the top level menu page by page slug.
	 * 
	 * The page should be already created or scheduled to be created separately.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );</code>
	 * 
	 * @since			2.0.0
	 * @access			protected
	 * @remark			The user may use this method in their extended class definition.
	 * @param			string			$sRootMenuSlug			The page slug of the top-level root page.
	 * @return			void
	 */ 
	protected function setRootMenuPageBySlug( $sRootMenuSlug ) {
		
		$this->oProps->aRootMenu['page_slug'] = $sRootMenuSlug;	// do not sanitize the slug here because post types includes a question mark.
		$this->oProps->aRootMenu['fCreateRoot'] = false;		// indicates to use an existing menu item. 
		
	}
	
	/**
	 * Adds sub-menu pages.
	 * 
	 * Use addSubMenuItems() instead, which supports external links.
	 * 
	 * @since			2.0.0
	 * @internal
	 * @return			void
	 * @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	 */ 
	protected function addSubMenuPages() {
		foreach ( func_get_args() as $aSubMenuPage ) {
			$aSubMenuPage = $aSubMenuPage + self::$_aStructure_SubMenuPage;	// avoid undefined index warnings.
			$this->addSubMenuPage(
				$aSubMenuPage['title'],
				$aSubMenuPage['page_slug'],
				$aSubMenuPage['screen_icon'],
				$aSubMenuPage['sCapability'],
				$aSubMenuPage['order'],
				$aSubMenuPage['fShowPageHeadingTab']
			);				
		}
	}
	
	/**
	 * Adds a single sub-menu page.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->addSubMenuPage( 'My Page', 'my_page', 'edit-pages' );</code>
	 * 
	 * @since			2.0.0
	 * @since			2.1.2			The key name page_heading_tab_visibility was changed to fShowPageHeadingTab
	 * @since			2.1.6			$sScreenIcon accepts a file path.
	 * @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	 * @param			string			$sPageTitle			The title of the page.
	 * @param			string			$sPageSlug			The slug of the page.
	 * @param			string			$sScreenIcon			( optional ) Either a screen icon ID, a url of the icon, or a file path to the icon, with the size of 32 by 32 in pixel. The accepted icon IDs are as follows.
	 * <blockquote>edit, post, index, media, upload, link-manager, link, link-category, edit-pages, page, edit-comments, themes, plugins, users, profile, user-edit, tools, admin, options-general, ms-admin, generic</blockquote>
	 * <strong>Note:</strong> the <em>generic</em> ID is available since WordPress 3.5.
	 * @param			string			$sCapability			( optional ) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the page.
	 * @param			integer			$nOrder				( optional ) the order number of the page. The lager the number is, the lower the position it is placed in the menu.
	 * @param			boolean			$bShowPageHeadingTab	( optional ) If this is set to false, the page title won't be displayed in the page heading tab. Default: true.
	 * @param			boolean			$bShowInMenu			( optional ) If this is set to false, the page title won't be displayed in the sidebar menu while the page is still accessible. Default: true.
	 * @return			void
	 */ 
	protected function addSubMenuPage( $sPageTitle, $sPageSlug, $sScreenIcon=null, $sCapability=null, $nOrder=null, $bShowPageHeadingTab=true, $bShowInMenu=true ) {
		
		$sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
		$iCount = count( $this->oProps->aPages );
		$aPreviouslySetPage = isset( $this->oProps->aPages[ $sPageSlug ] ) 
			? $this->oProps->aPages[ $sPageSlug ]
			: array();
		$aThisPage = array(  
			'title'				=> $sPageTitle,
			'page_slug'				=> $sPageSlug,
			'type'					=> 'page',	// this is used to compare with the link type.
			'hrefIcon32x32'			=> $this->oUtil->resolveSRC( $sScreenIcon, true ),
			'screen_iconID'			=> in_array( $sScreenIcon, self::$aScreenIconIDs ) ? $sScreenIcon : null,
			'sCapability'				=> isset( $sCapability ) ? $sCapability : $this->oProps->sCapability,
			'order'					=> is_numeric( $nOrder ) ? $nOrder : $iCount + 10,
			'fShowPageHeadingTab'		=> $bShowPageHeadingTab,
			'fShowInMenu'				=> $bShowInMenu,	// since 1.3.4			
			'fShowPageTitle'			=> $this->oProps->bShowPageTitle,			// boolean
			'fShowPageHeadingTabs'		=> $this->oProps->bShowPageHeadingTabs,		// boolean
			'fShowInPageTabs'			=> $this->oProps->bShowInPageTabs,			// boolean
			'sInPageTabTag'			=> $this->oProps->sInPageTabTag,			// string
			'sPageHeadingTabTag'		=> $this->oProps->sPageHeadingTabTag,		// string			
		);
		$this->oProps->aPages[ $sPageSlug ] = $this->oUtil->uniteArraysRecursive( $aThisPage, $aPreviouslySetPage );
			
	}
	
	/**
	 * Checks if a menu item is a WordPress built-in menu item from the given menu label.
	 * 
	 * @since			2.0.0
	 * @internal
	 * @return			void|string			Returns the associated slug string, if true.
	 */ 
	protected function isBuiltInMenuItem( $sMenuLabel ) {
		
		$sMenuLabelLower = strtolower( $sMenuLabel );
		if ( array_key_exists( $sMenuLabelLower, self::$aBuiltInRootMenuSlugs ) )
			return self::$aBuiltInRootMenuSlugs[ $sMenuLabelLower ];
		
	}
	
	/**
	 * Registers the root menu page.
	 * 
	 * @since			2.0.0
	 */ 
	private function registerRootMenuPage() {

		$sHookName = add_menu_page(  
			$this->oProps->sClassName,						// Page title - will be invisible anyway
			$this->oProps->aRootMenu['title'],				// Menu title - should be the root page title.
			$this->oProps->sCapability,						// Capability - access right
			$this->oProps->aRootMenu['page_slug'],			// Menu ID 
			'', //array( $this, $this->oProps->sClassName ), 	// Page content displaying function
			$this->oProps->aRootMenu['hrefIcon16x16'],		// icon path
			isset( $this->oProps->aRootMenu['intPosition'] ) ? $this->oProps->aRootMenu['intPosition'] : null	// menu position
		);

	}
	
	/**
	 * Registers the sub-menu page.
	 * 
	 * @since			2.0.0
	 * @remark			Used in the buildMenu() method. 
	 * @remark			Within the <em>admin_menu</em> hook callback process.
	 * @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	 */ 
	private function registerSubMenuPage( $aArgs ) {
	
		// Format the argument array since it may be added by the third party scripts via the hook.
		$aArgs = isset( $aArgs['type'] ) && $aArgs['type'] == 'link' 
			? $aArgs + AdminPageFramework_Link::$_aStructure_SubMenuLink	// for link
			: $aArgs + self::$_aStructure_SubMenuPage;	// for page
		
		// Variables
		$sType = $aArgs['type'];	// page or link
		$sTitle = $sType == 'page' ? $aArgs['title'] : $aArgs['title'];
		$sCapability = $aArgs['sCapability'];
			
		// Check the capability
		$sCapability = isset( $sCapability ) ? $sCapability : $this->sCapability;
		if ( ! current_user_can( $sCapability ) ) return;		
		
		// Add the sub-page to the sub-menu
		$aResult = array();
		$sRootPageSlug = $this->oProps->aRootMenu['page_slug'];
		$sMenuLabel = plugin_basename( $sRootPageSlug );	// Make it compatible with the add_submenu_page() function.
		
		// If it's a page - it's possible that the page_slug key is not set if the user uses a method like setPageHeadingTabsVisibility() prior to addSubMenuItam().
		if ( $sType == 'page' && isset( $aArgs['page_slug'] ) ) {		
			
			$sPageSlug = $aArgs['page_slug'];
			$aResult[ $sPageSlug ] = add_submenu_page( 
				$sRootPageSlug,						// the root(parent) page slug
				$sTitle,								// page_title
				$sTitle,								// menu_title
				$sCapability,				 			// sCapability
				$sPageSlug,	// menu_slug
				// In admin.php ( line 149 of WordPress v3.6.1 ), do_action($page_hook) ( where $page_hook is $aResult[ $sPageSlug ] )
				// will be executed and it triggers the __call magic method with the method name of "md5 class hash + _page_ + this page slug".
				array( $this, $this->oProps->sClassHash . '_page_' . $sPageSlug )
			);			
			
			add_action( "load-" . $aResult[ $sPageSlug ] , array( $this, "load_pre_" . $sPageSlug ) );
				
			// If the visibility option is false, remove the one just added from the sub-menu array
			if ( ! $aArgs['fShowInMenu'] ) {

				foreach( ( array ) $GLOBALS['submenu'][ $sMenuLabel ] as $iIndex => $aSubMenu ) {
					
					if ( ! isset( $aSubMenu[ 3 ] ) ) continue;
					
					// the array structure is defined in plugin.php - $submenu[$parent_slug][] = array ( $menu_title, $capability, $menu_slug, $page_title ) 
					if ( $aSubMenu[0] == $sTitle && $aSubMenu[3] == $sTitle && $aSubMenu[2] == $sPageSlug ) {
						unset( $GLOBALS['submenu'][ $sMenuLabel ][ $iIndex ] );
						
						// The page title in the browser window title bar will miss the page title as this is left as it is.
						$this->oProps->aHiddenPages[ $sPageSlug ] = $sTitle;
						add_filter( 'admin_title', array( $this, 'fixPageTitleForHiddenPages' ), 10, 2 );
						
						break;
					}
				}
			} 
				
		} 
		// If it's a link,
		if ( $sType == 'link' && $aArgs['fShowInMenu'] ) {
			
			if ( ! isset( $GLOBALS['submenu'][ $sMenuLabel ] ) )
				$GLOBALS['submenu'][ $sMenuLabel ] = array();
			
			$GLOBALS['submenu'][ $sMenuLabel ][] = array ( 
				$sTitle, 
				$sCapability, 
				$aArgs['href'],
			);	
		}
	
		return $aResult;	// maybe useful to debug.

	}
	
	/**
	 * A callback function for the admin_title filter to fix the page title for hidden pages.
	 * @since			2.1.4
	 */
	public function fixPageTitleForHiddenPages( $sAdminTitle, $sPageTitle ) {

		if ( isset( $_GET['page'], $this->oProps->aHiddenPages[ $_GET['page'] ] ) )
			return $this->oProps->aHiddenPages[ $_GET['page'] ] . $sAdminTitle;
			
		return $sAdminTitle;
		
	}
	
	
	/**
	 * Builds menus.
	 * 
	 * @since			2.0.0
	 */
	public function buildMenus() {
		
		// If the root menu label is not set but the slug is set, 
		if ( $this->oProps->aRootMenu['fCreateRoot'] ) 
			$this->registerRootMenuPage();
		
		// Apply filters to let other scripts add sub menu pages.
		$this->oProps->aPages = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
			$this,
			"{$this->oProps->sClassName}_pages", 
			$this->oProps->aPages
		);
		
		// Sort the page array.
		uasort( $this->oProps->aPages, array( $this->oProps, 'sortByOrder' ) ); 
		
		// Set the default page, the first element.
		foreach ( $this->oProps->aPages as $aPage ) {
			
			if ( ! isset( $aPage['page_slug'] ) ) continue;
			$this->oProps->sDefaultPageSlug = $aPage['page_slug'];
			break;
			
		}
		
		// Register them.
		foreach ( $this->oProps->aPages as &$aSubMenuItem ) 
			$this->oProps->aRegisteredSubMenuPages = $this->registerSubMenuPage( $aSubMenuItem );
						
		// After adding the sub menus, if the root menu is created, remove the page that is automatically created when registering the root menu.
		if ( $this->oProps->aRootMenu['fCreateRoot'] ) 
			remove_submenu_page( $this->oProps->aRootMenu['page_slug'], $this->oProps->aRootMenu['page_slug'] );
		
	}	
}
endif;

if ( ! class_exists( 'AdminPageFramework_Setting' ) ) :
/**
 * Provides methods to add form elements with WordPress Settings API. 
 *
 * @abstract
 * @since		2.0.0
 * @extends		AdminPageFramework_Menu
 * @package		Admin Page Framework
 * @subpackage	Admin Page Framework - Page
 * @staticvar	array		$_aStructure_Section				represents the structure of the form section array.
 * @staticvar	array		$_aStructure_Field					represents the structure of the form field array.
 * @var			array		$aFieldErrors						stores the settings field errors.
 */
abstract class AdminPageFramework_Setting extends AdminPageFramework_Menu {
	
	/**
	 * Represents the structure of the form section array.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds array structure of form section.
	 * @static
	 * @internal
	 */ 	
	protected static $_aStructure_Section = array(	
		'section_id' => null,
		'page_slug' => null,
		'tab_slug' => null,
		'title' => null,
		'description' => null,
		'sCapability' => null,
		'fIf' => true,	
		'order' => null,	// do not set the default number here because incremented numbers will be added when registering the sections.
		'help' => null,
		'helpAside' => null,
	);	
	
	/**
	 * Represents the structure of the form field array.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds array structure of form field.
	 * @static
	 * @internal
	 */ 
	protected static $_aStructure_Field = array(
		'field_id'		=> null, 		// ( mandatory )
		'section_id'		=> null,		// ( mandatory )
		'sSectionTitle'	=> null,		// This will be assigned automatically in the formatting method.
		'type'			=> null,		// ( mandatory )
		'page_slug'		=> null,		// This will be assigned automatically in the formatting method.
		'tab_slug'		=> null,		// This will be assigned automatically in the formatting method.
		'sOptionKey'		=> null,		// This will be assigned automatically in the formatting method.
		'sClassName'		=> null,		// This will be assigned automatically in the formatting method.
		'sCapability'		=> null,		
		'title'			=> null,
		'tip'			=> null,
		'description'	=> null,
		'sName'			=> null,		// the name attribute of the input field.
		'sError'			=> null,		// error message for the field
		'sBeforeField'	=> null,
		'sAfterField'		=> null,
		'fIf' 				=> true,
		'order'			=> null,	// do not set the default number here for this key.		
		'help'			=> null,	// since 2.1.0
		'helpAside'		=> null,	// since 2.1.0
		'repeatable'		=> null,	// since 2.1.3
	);	
	
	/**
	 * Stores the settings field errors. 
	 * 
	 * @since			2.0.0
	 * @var				array			Stores field errors.
	 * @internal
	 */ 
	protected $aFieldErrors;		// Do not set a value here since it is checked to see it's null.
							
	/**
	* Sets the given message to be displayed in the next page load. 
	* 
	* This is used to inform users about the submitted input data, such as "Updated successfully." or "Problem occurred." etc. and normally used in validation callback methods.
	* 
	* <h4>Example</h4>
	* <code>if ( ! $bVerified ) {
	*		$this->setFieldErrors( $aErrors );		
	*		$this->setSettingNotice( 'There was an error in your input.' );
	*		return $aOldPageOptions;
	*	}</code>
	*
	* @since			2.0.0
	* @since			2.1.2			Added a check to prevent duplicate items.
	* @since			2.1.5			Added the $bOverride parameter.
	* @access 			protected
	* @remark			The user may use this method in their extended class definition.
	* @param			string			$sMsg					the text message to be displayed.
	* @param			string			$sType				( optional ) the type of the message, either "error" or "updated"  is used.
	* @param			string			$sID					( optional ) the ID of the message. This is used in the ID attribute of the message HTML element.
	* @param			integer		$bOverride				( optional ) false: do not override when there is a message of the same id. true: override the previous one.
	* @return			void
	*/		
	protected function setSettingNotice( $sMsg, $sType='error', $sID=null, $bOverride=true ) {
		
		// Check if the same message has been added already.
		$aWPSettingsErrors = isset( $GLOBALS['wp_settings_errors'] ) ? ( array ) $GLOBALS['wp_settings_errors'] : array();
		$sID = isset( $sID ) ? $sID : $this->oProps->sOptionKey; 	// the id attribute for the message div element.

		foreach( $aWPSettingsErrors as $iIndex => $aSettingsError ) {
			
			if ( $aSettingsError['setting'] != $this->oProps->sOptionKey ) continue;
						
			// If the same message is added, no need to add another.
			if ( $aSettingsError['message'] == $sMsg ) return;
				
			// Prevent duplicated ids.
			if ( $aSettingsError['code'] === $sID ) {
				if ( ! $bOverride ) 
					return;
				else	// remove the item with the same id  
					unset( $aWPSettingsErrors[ $iIndex ] );
			}
							
		}

		add_settings_error( 
			$this->oProps->sOptionKey, // the script specific ID so the other settings error won't be displayed with the settings_errors() function.
			$sID, 
			$sMsg,	// error or updated
			$sType
		);
					
	}

	/**
	* Adds the given form section items into the property. 
	* 
	* The passed section array must consist of the following keys.
	* 
	* <strong>Section Array</strong>
	* <ul>
	* <li><strong>section_id</strong> - ( string ) the section ID. Avoid using non-alphabetic characters exept underscore and numbers.</li>
	* <li><strong>page_slug</strong> - (  string ) the page slug that the section belongs to.</li>
	* <li><strong>tab_slug</strong> - ( optional, string ) the tab slug that the section belongs to.</li>
	* <li><strong>title</strong> - ( optional, string ) the title of the section.</li>
	* <li><strong>sCapability</strong> - ( optional, string ) the <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> of the section. If the page visitor does not have sufficient capability, the section will be invisible to them.</li>
	* <li><strong>fIf</strong> - ( optional, boolean ) if the passed value is false, the section will not be registered.</li>
	* <li><strong>order</strong> - ( optional, integer ) the order number of the section. The higher the number is, the lower the position it gets.</li>
	* <li><strong>help</strong> - ( optional, string ) the help description added to the contextual help tab.</li>
	* <li><strong>helpAside</strong> - ( optional, string ) the additional help description for the side bar of the contextual help tab.</li>
	* </ul>
	* 
	* <h4>Example</h4>
	* <code>$this->addSettingSections(
	*		array(
	*			'section_id'		=> 'text_fields',
	*			'page_slug'		=> 'first_page',
	*			'tab_slug'		=> 'textfields',
	*			'title'			=> 'Text Fields',
	*			'description'	=> 'These are text type fields.',
	*			'order'			=> 10,
	*		),	
	*		array(
	*			'section_id'		=> 'selectors',
	*			'page_slug'		=> 'first_page',
	*			'tab_slug'		=> 'selectors',
	*			'title'			=> 'Selectors and Checkboxes',
	*			'description'	=> 'These are selector type options such as dropdown lists, radio buttons, and checkboxes',
	*		)</code>
	*
	* @since			2.0.0
	* @access 			protected
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>registerSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			array		$aSection1				the section array.
	* @param			array		$aSection2				( optional ) another section array.
	* @param			array		$_and_more					( optional ) add more section array to the next parameters as many as necessary.
	* @return			void
	*/		
	protected function addSettingSections( $aSection1, $aSection2=null, $_and_more=null ) {	
				
		foreach( func_get_args() as $aSection ) 
			$this->addSettingSection( $aSection );
			
	}
	
	/**
	 * A singular form of the adSettingSections() method which takes only a single parameter.
	 * 
	 * This is useful when adding section arrays in loops.
	 * 
	 * @since			2.1.2
	 * @access			protected
	 * @param			array		$aSection				the section array.
	 * @remark			The user may use this method in their extended class definition.
	 * @remark			The actual registration will be performed in the <em>registerSettings()</em> method with the <em>admin_menu</em> hook.
	 */
	protected function addSettingSection( $aSection ) {
		
		$sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;		
		
		if ( ! is_array( $aSection ) ) return;

		$aSection = $aSection + self::$_aStructure_Section;	// avoid undefined index warnings.
		
		// Sanitize the IDs since they are used as a callback method name, the slugs as well.
		$aSection['section_id'] = $this->oUtil->sanitizeSlug( $aSection['section_id'] );
		$aSection['page_slug'] = $this->oUtil->sanitizeSlug( $aSection['page_slug'] );
		$aSection['tab_slug'] = $this->oUtil->sanitizeSlug( $aSection['tab_slug'] );
		
		if ( ! isset( $aSection['section_id'], $aSection['page_slug'] ) ) return;	// these keys are necessary.
		
		// If the page slug does not match the current loading page, there is no need to register form sections and fields.
		if ( $GLOBALS['pagenow'] != 'options.php' && ! $sCurrentPageSlug || $sCurrentPageSlug !=  $aSection['page_slug'] ) return;				

		// If the custom condition is set and it's not true, skip.
		if ( ! $aSection['fIf'] ) return;
		
		// If the access level is set and it is not sufficient, skip.
		$aSection['sCapability'] = isset( $aSection['sCapability'] ) ? $aSection['sCapability'] : $this->oProps->sCapability;
		if ( ! current_user_can( $aSection['sCapability'] ) ) return;	// since 1.0.2.1
		
		$this->oProps->aSections[ $aSection['section_id'] ] = $aSection;	
			
	}
	
	/**
	* Removes the given section(s) by section ID.
	* 
	* This accesses the property storing the added section arrays and removes the specified ones.
	* 
	* <h4>Example</h4>
	* <code>$this->removeSettingSections( 'text_fields', 'selectors', 'another_section', 'yet_another_section' );</code>
	* 
	* @since			2.0.0
	* @access 			protected
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>registerSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			string			$sSectionID1			the section ID to remove.
	* @param			string			$sSectionID2			( optional ) another section ID to remove.
	* @param			string			$_and_more				( optional ) add more section IDs to the next parameters as many as necessary.
	* @return			void
	*/	
	protected function removeSettingSections( $sSectionID1=null, $sSectionID2=null, $_and_more=null ) {	
		
		foreach( func_get_args() as $sSectionID ) 
			if ( isset( $this->oProps->aSections[ $sSectionID ] ) )
				unset( $this->oProps->aSections[ $sSectionID ] );
		
	}
	
	/**
	* Adds the given field array items into the field array property.
	* 
	* The passed field array must consist of the following keys. 
	* 
	* <h4>Field Array</h4>
	* <ul>
	* 	<li><strong>field_id</strong> - ( string ) the field ID. Avoid using non-alphabetic characters exept underscore and numbers.</li>
	* 	<li><strong>section_id</strong> - ( string ) the section ID that the field belongs to.</li>
	* 	<li><strong>type</strong> - ( string ) the type of the field. The supported types are listed below.</li>
	* 	<li><strong>title</strong> - ( optional, string ) the title of the section.</li>
	* 	<li><strong>description</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	* 	<li><strong>tip</strong> - ( optional, string ) the tip for the field which is displayed when the mouse is hovered over the field title.</li>
	* 	<li><strong>sCapability</strong> - ( optional, string ) the http://codex.wordpress.org/Roles_and_Capabilities">access level of the section. If the page visitor does not have sufficient capability, the section will be invisible to them.</li>
	* 	<li><strong>sName</strong> - ( optional, string ) the name attribute value of the input tag instead of automatically generated one.</li>
	* 	<li><strong>sError</strong> - ( optional, string ) the error message to display above the input field.</li>
	* 	<li><strong>sBeforeField</strong> - ( optional, string ) the HTML string to insert before the input field output.</li>
	* 	<li><strong>sAfterField</strong> - ( optional, string ) the HTML string to insert after the input field output.</li>
	* 	<li><strong>fIf</strong> - ( optional, boolean ) if the passed value is false, the section will not be registered.</li>
	* 	<li><strong>order</strong> - ( optional, integer ) the order number of the section. The higher the number is, the lower the position it gets.</li>
	* 	<li><strong>label</strong> - ( optional|mandatory, string|array ) the text label(s) associated with and displayed along with the input field. Some input types can ignore this key while some require it.</li>
	* 	<li><strong>default</strong> - ( optional, string|array ) the default value(s) assigned to the input tag's value attribute.</li>
	* 	<li><strong>vValue</strong> - ( optional, string|array ) the value(s) assigned to the input tag's <em>value</em> attribute to override the default or stored value.</li>
	* 	<li><strong>delimiter</strong> - ( optional, string|array ) the HTML string that delimits multiple elements. This is available if the <var>label</var> key is passed as array. It will be enclosed in inline-block elements so the passed HTML string should not contain block elements.</li>
	* 	<li><strong>vBeforeInputTag</strong> - ( optional, string|array ) the HTML string inserted right before the input tag. It will be enclosed in the <code>label</code> tag so the passed HTML string should not contain block elements.</li>
	* 	<li><strong>vAfterInputTag</strong> - ( optional, string|array ) the HTML string inserted right after the input tag. It will be enclosed in the <code>label</code> tag so the passed HTML string should not contain block elements.</li>
	* 	<li><strong>class_attribute</strong> - ( optional, string|array ) the value(s) assigned to the input tag's <em>class</em>.</li>
	* 	<li><strong>labelMinWidth</strong> - ( optional, string|array ) the inline style property of the <em>min-width</em> of the label tag for the field in pixel without the unit. Default: <code>120</code>.</li>
	* 	<li><strong>vDisable</strong> - ( optional, boolean|array ) if this is set to true, the <em>disabled</em> attribute will be inserted into the field input tag.</li>
	*	<li><strong>help</strong> - ( optional, string ) the help description added to the contextual help tab.</li>
	*	<li><strong>helpAside</strong> - ( optional, string ) the additional help description for the side bar of the contextual help tab.</li>
	* </ul>
	* <h4>Field Types</h4>
	* <p>Each field type uses specific array keys.</p>
	* <ul>
	* 	<li><strong>text</strong> - a text input field which allows the user to type text.</li>
	* 		<ul>
	* 			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>size</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>repeatable</strong> - [2.1.3+] ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>
	* 		</ul>
	* 	<li><strong>password</strong> - a password input field which allows the user to type text.</li>
	* 		<ul>
	* 			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>size</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>repeatable</strong> - [2.1.3+] ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>	* 
	* 		</ul>
	* 	<li><strong>datetime, datetime-local, email, month, search, tel, time, url, week</strong> - HTML5 input fields types. Some browsers do not support these.</li>
	* 		<ul>
	* 			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>size</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 		</ul>
	* 	<li><strong>number, range</strong> - HTML5 input field types. Some browsers do not support these.</li>
	* 		<ul>
	* 			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>size</strong> - ( optional, integer|array ) the number that indicates the <em>size</em> attribute of the input field.</li>
	* 			<li><strong>vMax</strong> - ( optional, integer|array ) the number that indicates the <em>max</em> attribute of the input field.</li>
	* 			<li><strong>vMin</strong> - ( optional, integer|array ) the number that indicates the <em>min</em> attribute of the input field.</li>
	* 			<li><strong>vStep</strong> - ( optional, integer|array ) the number that indicates the <em>step</em> attribute of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>repeatable</strong> - [2.1.3+]( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>
	* 		</ul>
	* 	<li><strong>textarea</strong> - a textarea input field. The following array keys are supported.
	* 		<ul>
	*			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>rows</strong> - ( optional, integer|array ) the number of rows of the textarea field.</li>
	* 			<li><strong>cols</strong> - ( optional, integer|array ) the number of cols of the textarea field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>vRich</strong> - [2.1.2+]( optional, array ) to make it a rich text editor pass a non-empty value. It accept a setting array of the <code>_WP_Editors</code> class defined in the core.
	* For more information, see the argument section of <a href="http://codex.wordpress.org/Function_Reference/wp_editor" target="_blank">this page</a>.
	* 			</li>
	* 			<li><strong>repeatable</strong> - [2.1.3+]( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields. It's not supported for the rich editor.</li>
	*		</ul>
	* 	</li>
	* 	<li><strong>radio</strong> - a radio button input field.</li>
	* 	<li><strong>checkbox</strong> - a check box input field.</li>
	* 	<li><strong>select</strong> - a dropdown input field.</li>
	* 		<ul>
	* 			<li><strong>vMultiple</strong> - ( optional, boolean|array ) if this is set to true, the <em>multiple</em> attribute will be inserted into the field input tag, which enables the multiple selections for the user.</li>
	* 			<li><strong>vWidth</strong> - ( optional, string|array ) the width of the dropdown list including the unit. e.g. 120px</li>
	* 			<li><strong>size</strong> - ( optional, integer|array ) the number that indicates the <em>size</em> attribute of the input field.</li>
	* 		</ul>
	* 	<li><strong>size</strong> - a size input field. This is a combination of number and select fields.</li>
	* 		<ul>
	* 			<li>
	* 				<strong>size_units</strong> - ( optional, array ) defines the units to show. e.g. <code>array( 'px' => 'px', '%' => '%', 'em' => 'em'  )</code> 
	* 				Default: <code>array( 'px' => 'px', '%' => '%', 'em' => 'em', 'ex' => 'ex', 'in' => 'in', 'cm' => 'cm', 'mm' => 'mm', 'pt' => 'pt', 'pc' => 'pc' )</code>
	* 			</li>
	* 			<li><strong>vMultiple</strong> - ( optional, boolean|array ) if this is set to true, the <em>multiple</em> attribute will be inserted into the field input tag, which enables the multiple selections for the user.</li>
	* 			<li><strong>vWidth</strong> - ( optional, string|array ) the width of the dropdown list including the unit. e.g. 120px</li>
	* 			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>size</strong> - ( optional, integer|array ) the number that indicates the <em>size</em> attribute of the number input field.</li>
	* 			<li><strong>vUnitSize</strong> - [2.1.5+]( optional, integer|array ) the number that indicates the <em>size</em> attribute of the select(unit) input field.</li>
	* 			<li><strong>vMax</strong> - ( optional, integer|array ) the number that indicates the <em>max</em> attribute of the input field.</li>
	* 			<li><strong>vMin</strong> - ( optional, integer|array ) the number that indicates the <em>min</em> attribute of the input field.</li>
	* 			<li><strong>vStep</strong> - ( optional, integer|array ) the number that indicates the <em>step</em> attribute of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>size</strong> - ( optional, integer|array ) the number that indicates the <em>size</em> attribute of the input field.</li>
	* 	</ul>
	* 	<li><strong>hidden</strong> - a hidden input field.</li>
	* 	<li><strong>file</strong> - a file upload input field.</li>
	* 		<ul>
	* 			<li><strong>vAcceptAttribute</strong> - ( optional, string|array ) the accept attribute value. Default: <code>audio/*|video/*|image/*|MIME_type</code></li>
	* 		</ul>
	* 	<li><strong>submit</strong> - a submit button input field.</li>
	* 		<ul>
	* 			<li><strong>links</strong> - ( optional, string|array ) the url(s) linked to the submit button.</li>
	* 			<li><strong>redirect_url</strong> - ( optional, string|array ) the url(s) redirected to after submitting the input form.</li>
	* 			<li><strong>is_reset</strong> - [2.1.2+] ( optional, string|array ) the option key to delete. Set 1 for the entire option.</li>
	* 		</ul>
	* 	<li><strong>import</strong> - an inport input field. This is a custom file and submit field.</li>
	* 		<ul>
	* 			<li><strong>vAcceptAttribute</strong> - ( optional, string|array ) the accept attribute value. Default: <code>audio/*|video/*|image/*|MIME_type</code></li>
	* 			<li><strong>class_attributeUpload</strong> - ( optional, string|array ) [2.1.5+] the class attribute for the file upload field. Default: <code>import</code></li>
	* 			<li><strong>vImportOptionKey</strong> - ( optional, string|array ) the option table key to save the importing data.</li>
	* 			<li><strong>vImportFormat</strong> - ( optional, string|array ) the import format. json, or array is supported. Default: array</li>
	* 			<li><strong>vMerge</strong> - ( optional, boolean|array ) [2.0.5+] determines whether the imported data should be merged with the existing options.</li>
	* 		</ul>
	* 	<li><strong>export</strong> - an export input field. This is a custom submit field.</li>
	* 		<ul>
	* 			<li><strong>export_file_name</strong> - ( optional, string|array ) the file name to download.</li>
	* 			<li><strong>export_format</strong> - ( optional, string|array ) the format type. array, json, or text is supported. Default: array.</li>
	* 			<li><strong>export_data</strong> - ( optional, string|array|object ) the data to export.</li>
	* 		</ul>
	* 	<li><strong>image</strong> - an image input field. This is a custom text field with an attached JavaScript script.</li>
	* 		<ul>
	*			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>size</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>vImagePreview</strong> - ( optional, boolean|array ) if this is set to false, the image preview will be disabled.</li>
	* 			<li><strong>sTickBoxTitle</strong> - ( optional, string ) the text label displayed in the media uploader box's title.</li>
	* 			<li><strong>sLabelUseThis</strong> - ( optional, string ) the text label displayed in the button of the media uploader to set the image.</li>
	* 			<li><strong>repeatable</strong> - [2.1.3+] ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>
	* 			<li><strong>attributes_to_capture</strong> - [2.1.3+] ( optional, array ) the array of the attribute names of the image to save. If this is set, the field will be an array with the specified attributes. The supported attributes are, 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', and 'link'. Note that for external URLs, ID will not be captured. e.g. <code>'attributes_to_capture' => array( 'id', 'caption', 'description' )</code></li>
	* 			<li><strong>allow_external_source</strong> - [2.1.3+] ( optional, boolean ) whether external URL can be set via the uploader.</li>
	* 		</ul>
	* 	<li><strong>media</strong> - [2.1.3+] a media input field. This is a custom text field with an attached JavaScript script.</li>
	* 		<ul>
	*			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>size</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>sTickBoxTitle</strong> - ( optional, string ) the text label displayed in the media uploader box's title.</li>
	* 			<li><strong>sLabelUseThis</strong> - ( optional, string ) the text label displayed in the button of the media uploader to set the image.</li>
	* 			<li><strong>repeatable</strong> - [2.1.3+] ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>
	* 			<li><strong>attributes_to_capture</strong> - [2.1.3+] ( optional, array ) the array of the attribute names of the image to save. If this is set, the field will be an array with the specified attributes. The supported attributes are, 'id', 'caption', and 'description'. Note that for external URLs, ID will not be captured. e.g. <code>'attributes_to_capture' => array( 'id', 'caption', 'description' )</code></li>
	* 			<li><strong>allow_external_source</strong> - [2.1.3+] ( optional, boolean ) whether external URL can be set via the uploader.</li>
	* 		</ul>
	* 	<li><strong>color</strong> - a color picker input field. This is a custom text field with a JavaScript script.</li>
	* 		<ul>
	*			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>size</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>repeatable</strong> - [2.1.3+] ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>
	* 		</ul>
	* 	<li><strong>taxonomy</strong> - a taxonomy check list. This is a set of check boxes listing a specified taxonomy. This does not accept to create multiple fields by passing an array of labels.</li>
	* 		<ul>
	*			<li><strong>taxonomy_slugs</strong> - ( optional, string|array ) the taxonomy slug to list.</li>
	*			<li><strong>sWidth</strong> - ( optional, string ) the inline style property value of <em>max-width</em> of this element. Include the unit such as px, %. Default: 100%</li>
	*			<li><strong>height</strong> - ( optional, string ) the inline style property value of <em>height</em> of this element. Include the unit such as px, %. Default: 250px</li>
	* 		</ul>
	* 	<li><strong>posttype</strong> - a posttype check list. This is a set of check boxes listing post type slugs.</li>
	* 		<ul>
	* 			<li><strong>aRemove</strong> - ( optional, array ) the post type slugs not to be listed. e.g.<code>array( 'revision', 'attachment', 'nav_menu_item' )</code></li>
	* 		</ul>

	* </ul>	
	* 
	* <h4>Example</h4>
	* <code>$this->addSettingFields(
	*		array(	// Single text field
	*			'field_id' => 'text',
	*			'section_id' => 'text_fields',
	*			'title' => __( 'Text', 'admin-page-framework-demo' ),
	*			'description' => __( 'Type something here.', 'admin-page-framework-demo' ),	// additional notes besides the form field
	*			'type' => 'text',
	*			'order' => 1,
	*			'default' => 123456,
	*			'size' => 40,
	*		),	
	*		array(	// Multiple text fields
	*			'field_id' => 'text_multiple',
	*			'section_id' => 'text_fields',
	*			'title' => 'Multiple Text Fields',
	*			'description' => 'These are multiple text fields.',	// additional notes besides the form field
	*			'type' => 'text',
	*			'order' => 2,
	*			'default' => array(
	*				'Hello World',
	*				'Foo bar',
	*				'Yes, we can.'
	*			),
	*			'label' => array( 
	*				'First Item: ', 
	*				'Second Item: ', 
	*				'Third Item: ' 
	*			),
	*			'size' => array(
	*				30,
	*				60,
	*				90,
	*			),
	*		)
	*	);</code> 
	* 
	* @since			2.0.0
	* @access 			protected
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>registerSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			array			$aField1			the field array.
	* @param			array			$aField2			( optional ) another field array.
	* @param			array			$_and_more			( optional ) add more field arrays to the next parameters as many as necessary.
	* @return			void
	*/		
	protected function addSettingFields( $aField1, $aField2=null, $_and_more=null ) {	
	
		foreach( func_get_args() as $aField ) 
			$this->addSettingField( $aField );

	}
	/**
	* Adds the given field array items into the field array property.
	* 
	* Itentical to the addSettingFields() method except that this method does not accept enumerated parameters. 
	* 
	* @since			2.1.2
	* @return			void
	* @remark			The user may use this method in their extended class definition.
	*/	
	protected function addSettingField( $aField ) {
		
		if ( ! is_array( $aField ) ) return;
		
		$aField = $aField + self::$_aStructure_Field;	// avoid undefined index warnings.
		
		// Sanitize the IDs since they are used as a callback method name.
		$aField['field_id'] = $this->oUtil->sanitizeSlug( $aField['field_id'] );
		$aField['section_id'] = $this->oUtil->sanitizeSlug( $aField['section_id'] );
		
		// Check the mandatory keys' values are set.
		if ( ! isset( $aField['field_id'], $aField['section_id'], $aField['type'] ) ) return;	// these keys are necessary.
		
		// If the custom condition is set and it's not true, skip.
		if ( ! $aField['fIf'] ) return;			
		
		// If the access level is not sufficient, skip.
		$aField['sCapability'] = isset( $aField['sCapability'] ) ? $aField['sCapability'] : $this->oProps->sCapability;
		if ( ! current_user_can( $aField['sCapability'] ) ) return; 
								
		$this->oProps->aFields[ $aField['field_id'] ] = $aField;		
		
	}
	
	/**
	* Removes the given field(s) by field ID.
	* 
	* This accesses the property storing the added field arrays and removes the specified ones.
	* 
	* <h4>Example</h4>
	* <code>$this->removeSettingFields( 'fieldID_A', 'fieldID_B', 'fieldID_C', 'fieldID_D' );</code>
	* 
	* @since			2.0.0
	* @access 			protected
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>registerSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			string			$sFieldID1				the field ID to remove.
	* @param			string			$sFieldID2				( optional ) another field ID to remove.
	* @param			string			$_and_more					( optional ) add more field IDs to the next parameters as many as necessary.
	* @return			void
	*/	
	protected function removeSettingFields( $sFieldID1, $sFieldID2=null, $_and_more ) {
				
		foreach( func_get_args() as $sFieldID ) 
			if ( isset( $this->oProps->aFields[ $sFieldID ] ) )
				unset( $this->oProps->aFields[ $sFieldID ] );

	}	
	
	/**
	 * Redirects the callback of the load-{page} action hook to the framework's callback.
	 * 
	 * @since			2.1.0
	 * @access			protected
	 * @internal
	 * @remark			This method will be triggered before the header gets sent.
	 * @return			void
	 */ 
	protected function doPageLoadCall( $sPageSlug, $sTabSlug, $aArg ) {

		// Do actions, class name -> page -> in-page tab.
		$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( "load_", $this->oProps->sClassName, $sPageSlug, $sTabSlug, true ) );
		
	}
			
	/**
	 * Validates the submitted user input.
	 * 
	 * @since			2.0.0
	 * @access			protected
	 * @internal
	 * @remark			This method is not intended for the users to use.
	 * @remark			the scope must be protected to be accessed from the extended class. The <em>AdminPageFramework</em> class uses this method in the overloading <em>__call()</em> method.
	 * @return			array			Return the input array merged with the original saved options so that other page's data will not be lost.
	 */ 
	protected function doValidationCall( $sMethodName, $aInput ) {
		
		$sTabSlug = isset( $_POST['tab_slug'] ) ? $_POST['tab_slug'] : '';	// no need to retrieve the default tab slug here because it's an embedded value that is already set in the previous page. 
		$sPageSlug = isset( $_POST['page_slug'] ) ? $_POST['page_slug'] : '';
		
		// Retrieve the submit field ID(the container that holds submit input tags) and the input ID(this determines exactly which submit button is pressed).
		$sPressedFieldID = isset( $_POST['__submit'] ) ? $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__submit'], 'field_id' ) : '';
		$sPressedInputID = isset( $_POST['__submit'] ) ? $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__submit'], 'input_id' ) : '';
		
		// Check if custom submit keys are set [part 1]
		if ( isset( $_POST['__import']['submit'], $_FILES['__import'] ) ) 
			return $this->importOptions( $this->oProps->aOptions, $sPageSlug, $sTabSlug );
		if ( isset( $_POST['__export']['submit'] ) ) 
			die( $this->exportOptions( $this->oProps->aOptions, $sPageSlug, $sTabSlug ) );		
		if ( isset( $_POST['__reset_confirm'] ) && $sPressedFieldName = $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__reset_confirm'], 'key' ) )
			return $this->askResetOptions( $sPressedFieldName, $sPageSlug );			
		if ( isset( $_POST['__link'] ) && $sLinkURL = $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__link'], 'url' ) )
			$this->oUtil->goRedirect( $sLinkURL );	// if the associated submit button for the link is pressed, the will be redirected.
		if ( isset( $_POST['__redirect'] ) && $sRedirectURL = $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__redirect'], 'url' ) )
			$this->setRedirectTransients( $sRedirectURL );
				
		// Apply validation filters - validation_{page slug}_{tab slug}, validation_{page slug}, validation_{instantiated class name}
		$aInput = $this->getFilteredOptions( $aInput, $sPageSlug, $sTabSlug, $sPressedFieldID, $sPressedInputID );
		
		// Check if custom submit keys are set [part 2] - these should be done after applying the filters.
		if ( isset( $_POST['__reset'] ) && $sKeyToReset = $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__reset'], 'key' ) )
			$aInput = $this->resetOptions( $sKeyToReset, $aInput );
		
		// Set the update notice
		$bEmpty = empty( $aInput );
		$this->setSettingNotice( 
			$bEmpty ? $this->oMsg->__( 'option_cleared' ) : $this->oMsg->__( 'option_updated' ), 
			$bEmpty ? 'error' : 'updated', 
			$this->oProps->sOptionKey,	// the id
			false	// do not override
		);
		
		return $aInput;	
		
	}
	
	/**
	 * Displays a confirmation message to the user when a reset button is pressed.
	 * 
	 * @since			2.1.2
	 */
	private function askResetOptions( $sPressedFieldName, $sPageSlug ) {
		
		// Retrieve the pressed button's associated submit field ID and its section ID.
		// $sFieldName = $this->getPressedCustomSubmitButtonFieldName( $_POST['__reset_confirm'] );
		$aNameKeys = explode( '|', $sPressedFieldName );	
		// $sPageSlug = $aNameKeys[ 1 ]; 
		$sSectionID = $aNameKeys[ 2 ]; 
		$sFieldID = $aNameKeys[ 3 ];
		
		// Set up the field error array.
		$aErrors = array();
		$aErrors[ $sSectionID ][ $sFieldID ] = $this->oMsg->__( 'reset_options' );
		$this->setFieldErrors( $aErrors );
		
		// Set a flag that the confirmation is displayed
		set_transient( md5( "reset_confirm_" . $sPressedFieldName ), $sPressedFieldName, 60*2 );
		
		$this->setSettingNotice( $this->oMsg->__( 'confirm_perform_task' ) );
		
		return $this->getPageOptions( $sPageSlug ); 			
		
	}
	/**
	 * Performs reset options.
	 * 
	 * @since			2.1.2
	 * @remark			$aInput has only the page elements that called the validation callback. In other words, it does not hold other pages' option keys.
	 */
	private function resetOptions( $sKeyToReset, $aInput ) {
		
		if ( $sKeyToReset == 1 or $sKeyToReset === true ) {
			delete_option( $this->oProps->sOptionKey );
			$this->setSettingNotice( $this->oMsg->__( 'option_been_reset' ) );
			return array();
		}
		
		unset( $this->oProps->aOptions[ trim( $sKeyToReset ) ] );
		unset( $aInput[ trim( $sKeyToReset ) ] );
		update_option( $this->oProps->sOptionKey, $this->oProps->aOptions );
		$this->setSettingNotice( $this->oMsg->__( 'specified_option_been_deleted' ) );
	
		return $aInput;	// the returned array will be saved with the Settings API.
	}
	
	private function setRedirectTransients( $sURL ) {
		if ( empty( $sURL ) ) return;
		$sTransient = md5( trim( "redirect_{$this->oProps->sClassName}_{$_POST['page_slug']}" ) );
		return set_transient( $sTransient, $sURL , 60*2 );
	}
		
	/**
	 * Retrieves the target key's value associated with the given data to a custom submit button.
	 * 
	 * This method checks if the associated submit button is pressed with the input fields.
	 * 
	 * @since			2.0.0
	 * @return			mixed			Returns null if no button is found and the associated link url if found. Otherwise, the URL associated with the button.
	 */ 
	private function getPressedCustomSubmitButtonSiblingValue( $aPostElements, $sTargetKey='url' ) {	
	
		foreach( $aPostElements as $sFieldName => $aSubElements ) {
			
			/*
			 * $aSubElements['name']	- the input field name property of the submit button, delimited by pipe (|) e.g. APF_GettingStarted|first_page|submit_buttons|submit_button_link
			 * $aSubElements['url']	- the URL to redirect to. e.g. http://www.somedomain.com
			 * */
			$aNameKeys = explode( '|', $aSubElements[ 'name' ] );		// the 'name' key must be set.
			
			// Count of 4 means it's a single element. Count of 5 means it's one of multiple elements.
			// The isset() checks if the associated button is actually pressed or not.
			if ( count( $aNameKeys ) == 4 && isset( $_POST[ $aNameKeys[0] ][ $aNameKeys[1] ][ $aNameKeys[2] ][ $aNameKeys[3] ] ) )
				return $aSubElements[ $sTargetKey ];
			if ( count( $aNameKeys ) == 5 && isset( $_POST[ $aNameKeys[0] ][ $aNameKeys[1] ][ $aNameKeys[2] ][ $aNameKeys[3] ][ $aNameKeys[4] ] ) )
				return $aSubElements[ $sTargetKey ];
				
		}
		
		return null;	// not found
		
	}

	/**
	 * Processes the imported data.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Added additional filters with field id and input id.
	 */
	private function importOptions( $aStoredOptions, $sPageSlug, $sTabSlug ) {
		
		$oImport = new AdminPageFramework_ImportOptions( $_FILES['__import'], $_POST['__import'] );	
		$sPressedFieldID = $oImport->getSiblingValue( 'field_id' );
		$sPressedInputID = $oImport->getSiblingValue( 'input_id' );
		$bMerge = $oImport->getSiblingValue( 'do_merge' );
		
		// Check if there is an upload error.
		if ( $oImport->getError() > 0 ) {
			$this->setSettingNotice( $this->oMsg->__( 'import_error' ) );	
			return $aStoredOptions;	// do not change the framework's options.
		}
		
		// Check the uploaded file type.
		if ( ! in_array( $oImport->getType(), array( 'text/plain', 'application/octet-stream' ) ) ) {	// .json file is dealt as binary file.
			$this->setSettingNotice( $this->oMsg->__( 'uploaded_file_type_not_supported' ) );		
			return $aStoredOptions;	// do not change the framework's options.
		}
		
		// Retrieve the importing data.
		$vData = $oImport->getImportData();
		if ( $vData === false ) {
			$this->setSettingNotice( $this->oMsg->__( 'could_not_load_importing_data' ) );		
			return $aStoredOptions;	// do not change the framework's options.
		}
		
		// Apply filters to the data format type.
		$sFormatType = $this->oUtil->addAndApplyFilters(
			$this,
			array( "import_format_{$sPageSlug}_{$sTabSlug}", "import_format_{$sPageSlug}", "import_format_{$this->oProps->sClassName}_{$sPressedInputID}", "import_format_{$this->oProps->sClassName}_{$sPressedFieldID}", "import_format_{$this->oProps->sClassName}" ),
			$oImport->getFormatType(),	// the set format type, array, json, or text.
			$sPressedFieldID,
			$sPressedInputID
		);	// import_format_{$sPageSlug}_{$sTabSlug}, import_format_{$sPageSlug}, import_format_{$sClassName}_{pressed input id}, import_format_{$sClassName}_{pressed field id}, import_format_{$sClassName}		

		// Format it.
		$oImport->formatImportData( $vData, $sFormatType );	// it is passed as reference.	
		
		// If a custom option key is set,
		// Apply filters to the importing option key.
		$sImportOptionKey = $this->oUtil->addAndApplyFilters(
			$this,
			array( "import_option_key_{$sPageSlug}_{$sTabSlug}", "import_option_key_{$sPageSlug}", "import_option_key_{$this->oProps->sClassName}_{$sPressedInputID}", "import_option_key_{$this->oProps->sClassName}_{$sPressedFieldID}", "import_option_key_{$this->oProps->sClassName}" ),
			$oImport->getSiblingValue( 'import_option_key' ),	
			$sPressedFieldID,
			$sPressedInputID
		);	// import_option_key_{$sPageSlug}_{$sTabSlug}, import_option_key_{$sPageSlug}, import_option_key_{$sClassName}_{pressed input id}, import_option_key_{$sClassName}_{pressed field id}, import_option_key_{$sClassName}			
		
		// Apply filters to the importing data.
		$vData = $this->oUtil->addAndApplyFilters(
			$this,
			array( "import_{$sPageSlug}_{$sTabSlug}", "import_{$sPageSlug}", "import_{$this->oProps->sClassName}_{$sPressedInputID}", "import_{$this->oProps->sClassName}_{$sPressedFieldID}", "import_{$this->oProps->sClassName}" ),
			$vData,
			$aStoredOptions,
			$sPressedFieldID,
			$sPressedInputID,
			$sFormatType,
			$sImportOptionKey,
			$bMerge
		);

		// Set the update notice
		$bEmpty = empty( $vData );
		$this->setSettingNotice( 
			$bEmpty ? $this->oMsg->__( 'not_imported_data' ) : $this->oMsg->__( 'imported_data' ), 
			$bEmpty ? 'error' : 'updated',
			$this->oProps->sOptionKey,	// message id
			false	// do not override 
		);
				
		if ( $sImportOptionKey != $this->oProps->sOptionKey ) {
			update_option( $sImportOptionKey, $vData );
			return $aStoredOptions;	// do not change the framework's options.
		}
	
		// The option data to be saved will be returned.
		return $bMerge ?
			$this->oUtil->unitArrays( $vData, $aStoredOptions )
			: $vData;
						
	}
	private function exportOptions( $vData, $sPageSlug, $sTabSlug ) {

		$oExport = new AdminPageFramework_ExportOptions( $_POST['__export'], $this->oProps->sClassName );
		$sPressedFieldID = $oExport->getSiblingValue( 'field_id' );
		$sPressedInputID = $oExport->getSiblingValue( 'input_id' );
		
		// If the data is set in transient,
		$vData = $oExport->getTransientIfSet( $vData );
	
		// Get the field ID.
		$sFieldID = $oExport->getFieldID();
	
		// Add and apply filters. - adding filters must be done in this class because the callback method belongs to this class 
		// and the magic method should be triggered.			
		$vData = $this->oUtil->addAndApplyFilters(
			$this,
			array( "export_{$sPageSlug}_{$sTabSlug}", "export_{$sPageSlug}", "export_{$this->oProps->sClassName}_{$sPressedInputID}", "export_{$this->oProps->sClassName}_{$sPressedFieldID}", "export_{$this->oProps->sClassName}" ),
			$vData,
			$sPressedFieldID,
			$sPressedInputID
		);	// export_{$sPageSlug}_{$sTabSlug}, export_{$sPageSlug}, export_{$sClassName}_{pressed input id}, export_{$sClassName}_{pressed field id}, export_{$sClassName}	
		
		$sFileName = $this->oUtil->addAndApplyFilters(
			$this,
			array( "export_name_{$sPageSlug}_{$sTabSlug}", "export_name_{$sPageSlug}", "export_name_{$this->oProps->sClassName}_{$sPressedInputID}", "export_name_{$this->oProps->sClassName}_{$sPressedFieldID}", "export_name_{$this->oProps->sClassName}" ),
			$oExport->getFileName(),
			$sPressedFieldID,
			$sPressedInputID
		);	// export_name_{$sPageSlug}_{$sTabSlug}, export_name_{$sPageSlug}, export_name_{$sClassName}_{pressed input id}, export_name_{$sClassName}_{pressed field id}, export_name_{$sClassName}	
	
		$sFormatType = $this->oUtil->addAndApplyFilters(
			$this,
			array( "export_format_{$sPageSlug}_{$sTabSlug}", "export_format_{$sPageSlug}", "export_format_{$this->oProps->sClassName}_{$sPressedInputID}", "export_format_{$this->oProps->sClassName}_{$sPressedFieldID}", "export_format_{$this->oProps->sClassName}" ),
			$oExport->getFormat(),
			$sPressedFieldID,
			$sPressedInputID
		);	// export_format_{$sPageSlug}_{$sTabSlug}, export_format_{$sPageSlug}, export_format_{$sClassName}_{pressed input id}, export_format_{$sClassName}_{pressed field id}, export_format_{$sClassName}	
							
		$oExport->doExport( $vData, $sFileName, $sFormatType );
		exit;
		
	}
	
	/**
	 * Apples validation filters to the submitted input data.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Added the $sPressedFieldID and $sPressedInputID parameters.
	 * @return			array			The filtered input array.
	 */
	private function getFilteredOptions( $aInput, $sPageSlug, $sTabSlug, $sPressedFieldID, $sPressedInputID ) {

		$aStoredPageOptions = $this->getPageOptions( $sPageSlug ); 			

		// for tabs
		if ( $sTabSlug && $sPageSlug )	{
			$aRegisteredSectionKeysForThisTab = isset( $aInput[ $sPageSlug ] ) ? array_keys( $aInput[ $sPageSlug ] ) : array();			
			$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$sPageSlug}_{$sTabSlug}", $aInput, $aStoredPageOptions );	// $aInput: new values, $aStoredPageOptions: old values
			$aInput = $this->oUtil->uniteArraysRecursive( $aInput, $this->getOtherTabOptions( $sPageSlug, $aRegisteredSectionKeysForThisTab ) );
		}
		
		// for pages	
		if ( $sPageSlug )	{
			$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$sPageSlug}", $aInput, $aStoredPageOptions ); // $aInput: new values, $aStoredPageOptions: old values
			$aInput = $this->oUtil->uniteArraysRecursive( $aInput, $this->getOtherPageOptions( $sPageSlug ) );
		}

		// for the input ID
		if ( $sPressedInputID )
			$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProps->sClassName}_{$sPressedInputID}", $aInput, $this->oProps->aOptions );
		
		// for the field ID
		if ( $sPressedFieldID )
			$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProps->sClassName}_{$sPressedFieldID}", $aInput, $this->oProps->aOptions );
		
		// for the class
		$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProps->sClassName}", $aInput, $this->oProps->aOptions );

		return $aInput;
	
	}	
	
	/**
	 * Retrieves the stored options of the given page slug.
	 * 
	 * Other pages' option data will not be contained in the returning array.
	 * This is used to pass the old option array to the validation callback method.
	 * 
	 * @since			2.0.0
	 * @return			array			the stored options of the given page slug. If not found, an empty array will be returned.
	 */ 
	private function getPageOptions( $sPageSlug ) {
				
		$aStoredPageOptions = array();
		if ( isset( $this->oProps->aOptions[ $sPageSlug ] ) )
			$aStoredPageOptions[ $sPageSlug ] = $this->oProps->aOptions[ $sPageSlug ];
		
		return $aStoredPageOptions;
		
	}
	
	/**
	 * Retrieves the stored options excluding the currently specified tab's sections and their fields.
	 * 
	 * This is used to merge the submitted form data with the previously stored option data of the form elements 
	 * that belong to the in-page tab of the given page.
	 * 
	 * @since			2.0.0
	 * @return			array			the stored options excluding the currently specified tab's sections and their fields.
	 * 	 If not found, an empty array will be returned.
	 */ 
	private function getOtherTabOptions( $sPageSlug, $aSectionKeysForTheTab ) {
	
		$aOtherTabOptions = array();
		if ( isset( $this->oProps->aOptions[ $sPageSlug ] ) )
			$aOtherTabOptions[ $sPageSlug ] = $this->oProps->aOptions[ $sPageSlug ];
			
		// Remove the elements of the given keys so that the other stored elements will remain. 
		// They are the other form section elements which need to be returned.
		foreach( $aSectionKeysForTheTab as $aSectionKey ) 
			unset( $aOtherTabOptions[ $sPageSlug ][ $aSectionKey ] );
			
		return $aOtherTabOptions;
		
	}
	
	/**
	 * Retrieves the stored options excluding the key of the given page slug.
	 * 
	 * This is used to merge the submitted form input data with the previously stored option data except the given page.
	 * 
	 * @since			2.0.0
	 * @return			array			the array storing the options excluding the key of the given page slug. 
	 */ 
	private function getOtherPageOptions( $sPageSlug ) {
	
		$aOtherPageOptions = $this->oProps->aOptions;
		if ( isset( $aOtherPageOptions[ $sPageSlug ] ) )
			unset( $aOtherPageOptions[ $sPageSlug ] );
		return $aOtherPageOptions;
		
	}
	
	/**
	 * Renders the registered setting fields.
	 * 
	 * @internal
	 * @since			2.0.0
	 * @remark			the protected scope is used because it's called from an extended class.
	 * @return			void
	 */ 
	protected function renderSettingField( $sFieldID, $sPageSlug ) {
			
		// If the specified field does not exist, do nothing.
		if ( ! isset( $this->oProps->aFields[ $sFieldID ] ) ) return;	// if it is not added, return
		$aField = $this->oProps->aFields[ $sFieldID ];
		
		// Retrieve the field error array.
		$this->aFieldErrors = isset( $this->aFieldErrors ) ? $this->aFieldErrors : $this->getFieldErrors( $sPageSlug ); 

		// Render the form field. 		
		$sFieldType = isset( $this->oProps->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] ) && is_callable( $this->oProps->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] )
			? $aField['type']
			: 'default';	// the predefined reserved field type is applied if the parsing field type is not defined(not found).

		$oField = new AdminPageFramework_InputField( $aField, $this->oProps->aOptions, $this->aFieldErrors, $this->oProps->aFieldTypeDefinitions[ $sFieldType ], $this->oMsg );
		$sFieldOutput = $oField->getInputField( $sFieldType );	// field output
		unset( $oField );	// release the object for PHP 5.2.x or below.

		echo $this->oUtil->addAndApplyFilters(
			$this,
			array( 
				$this->oProps->sClassName . '_' .  self::$aPrefixesForCallbacks['field_'] . $sFieldID,	// this filter will be deprecated
				self::$aPrefixesForCallbacks['field_'] . $this->oProps->sClassName . '_' . $sFieldID	// field_ + {extended class name} + _ {field id}
			),
			$sFieldOutput,
			$aField // the field array
		);
		
	}
	
	/**
	 * Retrieves the settings error array set by the user in the validation callback.
	 * 
	 * @since				2.0.0
	 * @since				2.1.2			Added the second parameter. 
	 */
	protected function getFieldErrors( $sPageSlug, $bDelete=true ) {
		
		// If a form submit button is not pressed, there is no need to set the setting errors.
		if ( ! isset( $_GET['settings-updated'] ) ) return null;
		
		// Find the transient.
		$sTransient = md5( $this->oProps->sClassName . '_' . $sPageSlug );
		$aFieldErrors = get_transient( $sTransient );
		if ( $bDelete )
			delete_transient( $sTransient );	
		return $aFieldErrors;

	}
	
	/**
	 * Sets the field error array. 
	 * 
	 * This is normally used in validation callback methods. when submitted data have an issue.
	 * This method saves the given array in a temporary area( transient ) of the options database table.
	 * 
	 * <h4>Example</h4>
	 * <code>public function validation_first_page_verification( $aInput, $aOldPageOptions ) {	// valication_ + page slug + _ + tab slug			
	 *		$bVerified = true;
	 *		$aErrors = array();
	 *		// Check if the submitted value meets your criteria. As an example, here a numeric value is expected.
	 *		if ( isset( $aInput['first_page']['verification']['verify_text_field'] ) && ! is_numeric( $aInput['first_page']['verification']['verify_text_field'] ) ) {
	 *			// Start with the section key in $aErrors, not the key of page slug.
	 *			$aErrors['verification']['verify_text_field'] = 'The value must be numeric: ' . $aInput['first_page']['verification']['verify_text_field'];	
	 *			$bVerified = false;
	 *		}
	 *		// An invalid value is found.
	 *		if ( ! $bVerified ) {
	 *			// Set the error array for the input fields.
	 *			$this->setFieldErrors( $aErrors );		
	 *			$this->setSettingNotice( 'There was an error in your input.' );
	 *			return $aOldPageOptions;
	 *		}
	 *		return $aInput;
	 *	}</code>
	 * 
	 * @since			2.0.0
	 * @remark			the transient name is a MD5 hash of the extended class name + _ + page slug ( the passed ID )
	 * @param			array			$aErrors			the field error array. The structure should follow the one contained in the submitted $_POST array.
	 * @param			string			$sID				this should be the page slug of the page that has the dealing form field.
	 * @param			integer			$nSavingDuration	the transient's lifetime. 300 seconds means 5 minutes.
	 */ 
	protected function setFieldErrors( $aErrors, $sID=null, $nSavingDuration=300 ) {
		
		$sID = isset( $sID ) ? $sID : ( isset( $_POST['page_slug'] ) ? $_POST['page_slug'] : ( isset( $_GET['page'] ) ? $_GET['page'] : $this->oProps->sClassName ) );	
		set_transient( md5( $this->oProps->sClassName . '_' . $sID ), $aErrors, $nSavingDuration );	// store it for 5 minutes ( 60 seconds * 5 )
	
	}

	/**
	 * Renders the filtered section description.
	 * 
	 * @internal
	 * @since			2.0.0
	 * @remark			the protected scope is used because it's called from an extended class.
	 * @remark			This is the redirected callback for the section description method from __call().
	 * @return			void
	 */ 	
	protected function renderSectionDescription( $sMethodName ) {		

		$sSectionID = substr( $sMethodName, strlen( 'section_pre_' ) );	// X will be the section ID in section_pre_X
		
		if ( ! isset( $this->oProps->aSections[ $sSectionID ] ) ) return;	// if it is not added
		
		echo $this->oUtil->addAndApplyFilters(
			$this,
			array( 
				$this->oProps->sClassName . '_' .  self::$aPrefixesForCallbacks['section_'] . $sSectionID,	// this filter will be deprecated
				self::$aPrefixesForCallbacks['section_'] . $this->oProps->sClassName . '_' . $sSectionID	// section_ + {extended class name} + _ {section id}
			),
			'<p>' . $this->oProps->aSections[ $sSectionID ]['description'] . '</p>',	 // the p-tagged description string
			$this->oProps->aSections[ $sSectionID ]['description']	// the original description
		);		
			
	}
	
	/**
	 * Retrieves the page slug that the settings section belongs to.		
	 * 
	 * @since			2.0.0
	 * @return			string|null
	 */ 
	private function getPageSlugBySectionID( $sSectionID ) {
		return isset( $this->oProps->aSections[ $sSectionID ]['page_slug'] )
			? $this->oProps->aSections[ $sSectionID ]['page_slug']
			: null;			
	}
	
	/**
	 * Registers the setting sections and fields.
	 * 
	 * This methods passes the stored section and field array contents to the <em>add_settings_section()</em> and <em>add_settings_fields()</em> functions.
	 * Then perform <em>register_setting()</em>.
	 * 
	 * The filters will be applied to the section and field arrays; that means that third-party scripts can modify the arrays.
	 * Also they get sorted before being registered based on the set order.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Added the ability to define custom field types.
	 * @remark			This method is not intended to be used by the user.
	 * @remark			The callback method for the <em>admin_menu</em> hook.
	 * @return			void
	 */ 
	public function registerSettings() {
		
		// Format ( sanitize ) the section and field arrays.
		$this->oProps->aSections = $this->formatSectionArrays( $this->oProps->aSections );
		$this->oProps->aFields = $this->formatFieldArrays( $this->oProps->aFields );	// must be done after the formatSectionArrays().
				
		// If there is no section or field to add, do nothing.
		if ( 
			$GLOBALS['pagenow'] != 'options.php'
			&& ( count( $this->oProps->aSections ) == 0 || count( $this->oProps->aFields ) == 0 ) 
		) return;
				
		// Define field types.
		// This class adds filters for the field type definitions so that framework's default field types will be added.
		new AdminPageFramework_BuiltinInputFieldTypeDefinitions( $this->oProps->aFieldTypeDefinitions, $this->oProps->sClassName, $this->oMsg );

		$this->oProps->aFieldTypeDefinitions = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
			$this,
			self::$aPrefixesForCallbacks['field_types_'] . $this->oProps->sClassName,	// 'field_types_' . {extended class name}
			$this->oProps->aFieldTypeDefinitions
		);		

		// Register settings sections 
		uasort( $this->oProps->aSections, array( $this->oProps, 'sortByOrder' ) ); 
		foreach( $this->oProps->aSections as $aSection ) {
			add_settings_section(	// Add the given section
				$aSection['section_id'],	//  section ID
				"<a id='{$aSection['section_id']}'></a>" . $aSection['title'],		// title - place the anchor in front of the title.
				array( $this, 'section_pre_' . $aSection['section_id'] ), 				// callback function -  this will trigger the __call() magic method.
				$aSection['page_slug']	// page
			);
			// For the contextual help pane,
			if ( ! empty( $aSection['help'] ) )
				$this->addHelpTab( 
					array(
						'page_slug'				=> $aSection['page_slug'],
						'page_tab_slug'			=> $aSection['tab_slug'],
						'help_tab_title'			=> $aSection['title'],
						'help_tab_id'				=> $aSection['section_id'],
						'help_tab_content'			=> $aSection['help'],
						'help_tab_sidebar_content'	=> $aSection['helpAside'] ? $aSection['helpAside'] : "",
					)
				);
				
		}
		
		// Register settings fields
		$sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
		uasort( $this->oProps->aFields, array( $this->oProps, 'sortByOrder' ) ); 
		foreach( $this->oProps->aFields as $aField ) {

			add_settings_field(	// Add the given field.
				$aField['field_id'],
				"<a id='{$aField['field_id']}'></a><span title='{$aField['tip']}'>{$aField['title']}</span>",
				array( $this, 'field_pre_' . $aField['field_id'] ),	// callback function - will trigger the __call() magic method.
				$this->getPageSlugBySectionID( $aField['section_id'] ), // page slug
				$aField['section_id'],	// section
				$aField['field_id']		// arguments - pass the field ID to the callback function
			);	

			// Set relevant scripts and styles for the input field.
			$this->setFieldHeadTagElements( $aField );
			
			// For the contextual help pane,
			if ( ! empty( $aField['help'] ) )
				$this->addHelpTab( 
					array(
						'page_slug'					=> $aField['page_slug'],
						'page_tab_slug'				=> $aField['tab_slug'],
						'help_tab_title'			=> $aField['sSectionTitle'],
						'help_tab_id'				=> $aField['section_id'],
						'help_tab_content'			=> "<span class='contextual-help-tab-title'>" . $aField['title'] . "</span> - " . PHP_EOL
														. $aField['help'],
						'help_tab_sidebar_content'	=> $aField['helpAside'] ? $aField['helpAside'] : "",
					)
				);

		}
		
		// Set the form enabling flag so that the <form></form> tag will be inserted in the page.
		$this->oProps->bEnableForm = true;
		register_setting(	
			$this->oProps->sOptionKey,	// the option group name.	
			$this->oProps->sOptionKey,	// the option key name that will be stored in the option table in the database.
			array( $this, 'validation_pre_' . $this->oProps->sClassName )	// validation method
		); 
		
	}
		/**
		 * Sets the given field type's enqueuing scripts and styles.
		 * 
		 * A helper function for the above registerSettings() method.
		 * 
		 * @since			2.1.5
		 */
		private function setFieldHeadTagElements( $aField ) {
			
			$sFieldType = $aField['type'];
			
			// Set the global flag to indicate whether the elements are already added and enqueued.
			if ( isset( $GLOBALS['aAdminPageFramework']['aFieldFlags'][ $sFieldType ] ) && $GLOBALS['aAdminPageFramework']['aFieldFlags'][ $sFieldType ] ) return;
			$GLOBALS['aAdminPageFramework']['aFieldFlags'][ $sFieldType ] = true;

			// If the field type is not defined, return.
			if ( ! isset( $this->oProps->aFieldTypeDefinitions[ $sFieldType ] ) ) return;

			if ( is_callable( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfFieldLoader'] ) )
				call_user_func_array( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfFieldLoader'], array() );		
			
			if ( is_callable( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfGetScripts'] ) )
				$this->oProps->sScript .= call_user_func_array( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfGetScripts'], array() );
				
			if ( is_callable( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfGetStyles'] ) )
				$this->oProps->sStyle .= call_user_func_array( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfGetStyles'], array() );
				
			if ( is_callable( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfGetIEStyles'] ) )
				$this->oProps->sStyleIE .= call_user_func_array( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfGetIEStyles'], array() );					
				
			$this->oHeadTag->enqueueStyles( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueStyles'] );
			$this->oHeadTag->enqueueScripts( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueScripts'] );
					
		}
	
	
	/**
	 * Formats the given section arrays.
	 * 
	 * @since			2.0.0
	 */ 
	private function formatSectionArrays( $aSections ) {

		// Apply filters to let other scripts to add sections.
		$aSections = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
			$this,
			"{$this->oProps->sClassName}_setting_sections",
			$aSections
		);
		
		$sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
		
		// Since the section array may have been modified, sanitize the elements and 
		// apply the conditions to remove unnecessary elements and put new orders.
		$aNewSectionArray = array();
		foreach( $aSections as $aSection ) {
		
			$aSection = $aSection + self::$_aStructure_Section;	// avoid undefined index warnings.
			
			// Sanitize the IDs since they are used as a callback method name, the slugs as well.
			$aSection['section_id'] = $this->oUtil->sanitizeSlug( $aSection['section_id'] );
			$aSection['page_slug'] = $this->oUtil->sanitizeSlug( $aSection['page_slug'] );
			$aSection['tab_slug'] = $this->oUtil->sanitizeSlug( $aSection['tab_slug'] );
			
			// Check the mandatory keys' values.
			if ( ! isset( $aSection['section_id'], $aSection['page_slug'] ) ) continue;	// these keys are necessary.
			
			// If the page slug does not match the current loading page, there is no need to register form sections and fields.
			if ( $GLOBALS['pagenow'] != 'options.php' && ! $sCurrentPageSlug || $sCurrentPageSlug !=  $aSection['page_slug'] ) continue;				

			// If this section does not belong to the currently loading page tab, skip.
			if ( ! $this->isSettingSectionOfCurrentTab( $aSection ) )  continue;
			
			// If the access level is set and it is not sufficient, skip.
			$aSection['sCapability'] = isset( $aSection['sCapability'] ) ? $aSection['sCapability'] : $this->oProps->sCapability;
			if ( ! current_user_can( $aSection['sCapability'] ) ) continue;	// since 1.0.2.1
		
			// If a custom condition is set and it's not true, skip,
			if ( $aSection['fIf'] !== true ) continue;
		
			// Set the order.
			$aSection['order']	= is_numeric( $aSection['order'] ) ? $aSection['order'] : count( $aNewSectionArray ) + 10;
		
			// Add the section array to the returning array.
			$aNewSectionArray[ $aSection['section_id'] ] = $aSection;
			
		}
		return $aNewSectionArray;
		
	}
	
	/**
	 * Checks if the given section belongs to the currently loading tab.
	 * 
	 * @since			2.0.0
	 * @return			boolean			Returns true if the section belongs to the current tab page. Otherwise, false.
	 */ 	
	private function isSettingSectionOfCurrentTab( $aSection ) {

		// Determine: 
		// 1. if the current tab matches the given tab slug. Yes -> the section should be registered.
		// 2. if the current page is the default tab. Yes -> the section should be registered.

		// If the tab slug is not specified, it means that the user wants the section to be visible in the page regardless of tabs.
		if ( ! isset( $aSection['tab_slug'] ) ) return true;
		
		// 1. If the checking tab slug and the current loading tab slug is the same, it should be registered.
		$sCurrentTab =  isset( $_GET['tab'] ) ? $_GET['tab'] : null;
		if ( $aSection['tab_slug'] == $sCurrentTab )  return true;

		// 2. If $_GET['tab'] is not set and the page slug is stored in the tab array, 
		// consider the default tab which should be loaded without the tab query value in the url
		$sPageSlug = $aSection['page_slug'];
		if ( ! isset( $_GET['tab'] ) && isset( $this->oProps->aInPageTabs[ $sPageSlug ] ) ) {
		
			$sDefaultTabSlug = isset( $this->oProps->aDefaultInPageTabs[ $sPageSlug ] ) ? $this->oProps->aDefaultInPageTabs[ $sPageSlug ] : '';
			if ( $sDefaultTabSlug  == $aSection['tab_slug'] ) return true;		// should be registered.			
				
		}
				
		// Otherwise, false.
		return false;
		
	}	
	
	/**
	 * Formats the given field arrays.
	 * 
	 * @since			2.0.0
	 */ 
	private function formatFieldArrays( $aFields ) {
		
		// Apply filters to let other scripts to add fields.
		$aFields = $this->oUtil->addAndApplyFilter(	// Parameters: $oCallerObject, $aFilters, $vInput, $vArgs...
			$this,
			"{$this->oProps->sClassName}_setting_fields",
			$aFields
		); 
		
		// Apply the conditions to remove unnecessary elements and put new orders.
		$aNewFieldArrays = array();
		foreach( $aFields as $aField ) {
		
			if ( ! is_array( $aField ) ) continue;		// the element must be an array.
			
			$aField = $aField + self::$_aStructure_Field;	// avoid undefined index warnings.
			
			// Sanitize the IDs since they are used as a callback method name.
			$aField['field_id'] = $this->oUtil->sanitizeSlug( $aField['field_id'] );
			$aField['section_id'] = $this->oUtil->sanitizeSlug( $aField['section_id'] );
			
			// If the section that this field belongs to is not set, no need to register this field.
			// The $aSection property must be formatted prior to perform this method.
			if ( ! isset( $this->oProps->aSections[ $aField['section_id'] ] ) ) continue;
			
			// Check the mandatory keys' values.
			if ( ! isset( $aField['field_id'], $aField['section_id'], $aField['type'] ) ) continue;	// these keys are necessary.
			
			// If the access level is not sufficient, skip.
			$aField['sCapability'] = isset( $aField['sCapability'] ) ? $aField['sCapability'] : $this->oProps->sCapability;
			if ( ! current_user_can( $aField['sCapability'] ) ) continue; 
						
			// If the condition is not met, skip.
			if ( $aField['fIf'] !== true ) continue;
						
			// Set the order.
			$aField['order']	= is_numeric( $aField['order'] ) ? $aField['order'] : count( $aNewFieldArrays ) + 10;
			
			// Set the tip, option key, instantiated class name, and page slug elements.
			$aField['tip'] = strip_tags( isset( $aField['tip'] ) ? $aField['tip'] : $aField['description'] );
			$aField['sOptionKey'] = $this->oProps->sOptionKey;
			$aField['sClassName'] = $this->oProps->sClassName;
			// $aField['page_slug'] = isset( $_GET['page'] ) ? $_GET['page'] : null;
			$aField['page_slug'] = $this->oProps->aSections[ $aField['section_id'] ]['page_slug'];
			$aField['tab_slug'] = $this->oProps->aSections[ $aField['section_id'] ]['tab_slug'];
			$aField['sSectionTitle'] = $this->oProps->aSections[ $aField['section_id'] ]['title'];	// used for the contextual help pane.
			
			// Add the element to the new returning array.
			$aNewFieldArrays[ $aField['field_id'] ] = $aField;
				
		}
		return $aNewFieldArrays;
		
	}
	
	/**
	 * Retrieves the specified field value stored in the options.
	 * 
	 * Useful when you don't know the section name but it's a bit slower than accessing the property value by specifying the section name.
	 * 
	 * @since			2.1.2
	 */
	protected function getFieldValue( $sFieldNameToFind ) {

		foreach( $this->oProps->aOptions as $sPageSlug => $aSections )  
			foreach( $aSections as $sSectionName => $aFields ) 
				foreach( $aFields as $sFieldName => $vValue ) 
					if ( trim( $sFieldNameToFind ) == trim( $sFieldName ) )
						return $vValue;	
		
		return null;
	}

}
endif; 

if ( ! class_exists( 'AdminPageFramework' ) ) :
/**
 * The main class of the framework. 
 * 
 * The user should extend this class and define the set-ups in the setUp() method. Most of the public methods are for hook callbacks and the private methods are internal helper functions. So the protected methods are for the users.
 * 
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 * 	<li><code>start_ + extended class name</code> – triggered at the end of the class constructor. This will be triggered in any admin page.</li>
 * 	<li><code>load_ + extended class name</code>[2.1.0+] – triggered when the framework's page is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework.</li>
 * 	<li><code>load_ + page slug</code>[2.1.0+] – triggered when the framework's page is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework.</li>
 * 	<li><code>load_ + page slug + _ + tab slug</code>[2.1.0+] – triggered when the framework's page is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework.</li>
 * 	<li><code>do_before_ + extended class name</code> – triggered before rendering the page. It applies to all pages created by the instantiated class object.</li>
 * 	<li><code>do_before_ + page slug</code> – triggered before rendering the page.</li>
 * 	<li><code>do_before_ + page slug + _ + tab slug</code> – triggered before rendering the page.</li>
 * 	<li><code>do_ + extended class name</code> – triggered in the middle of rendering the page. It applies to all pages created by the instantiated class object.</li>
 * 	<li><code>do_ + page slug</code> – triggered in the middle of rendering the page.</li>
 * 	<li><code>do_ + page slug + _ + tab slug</code> – triggered in the middle of rendering the page.</li>
 * 	<li><code>do_after_ + extended class name</code> – triggered after rendering the page. It applies to all pages created by the instantiated class object.</li>
 * 	<li><code>do_after_ + page slug</code> – triggered after rendering the page.</li>
 * 	<li><code>do_after_ + page slug + _ + tab slug</code> – triggered after rendering the page.</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 * 	<li><code>head_ + page slug</code> – receives the output of the top part of the page.</li>
 * 	<li><code>head_ + page slug + _ + tab slug</code> – receives the output of the top part of the page.</li>
 * 	<li><code>head_ + extended class name</code> – receives the output of the top part of the page, applied to all pages created by the instantiated class object.</li>
 * 	<li><code>content_ + page slug</code> – receives the output of the middle part of the page including form input fields.</li>
 * 	<li><code>content_ + page slug + _ + tab slug</code> – receives the output of the middle part of the page including form input fields.</li>
 * 	<li><code>content_ + extended class name</code> – receives the output of the middle part of the page, applied to all pages created by the instantiated class object.</li>
 * 	<li><code>foot_ + page slug</code> – receives the output of the bottom part of the page.</li>
 * 	<li><code>foot_ + page slug + _ + tab slug</code> – receives the output of the bottom part of the page.</li>
 * 	<li><code>foot_ + extended class name</code> – receives the output of the bottom part of the page, applied to all pages created by the instantiated class object.</li>
 * 	<li><code>section_ + extended class name + _ + section ID</code> – receives the description output of the given form section ID. The first parameter: output string. The second parameter: the array of option.</li> 
 * 	<li><code>field_ + extended class name + _ + field ID</code> – receives the form input field output of the given input field ID. The first parameter: output string. The second parameter: the array of option.</li>
 * 	<li><code>validation_ + page slug + _ + tab slug</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><code>validation_ + page slug</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><code>validation_ + extended class name + _ + input id</code> – [2.1.5+] receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database. The input ID is the one used to the name attribute of the submit input tag. For a submit button that is inserted without using the framework's method, it will not take effect.</li>
 * 	<li><code>validation_ + extended class name + _ + field id</code> – [2.1.5+] receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database. The field ID is the one that is passed to the field array to create the submit input field.</li>
 * 	<li><code>validation_ + extended class name</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><code>style_ + page slug + _ + tab slug</code> – receives the output of the CSS rules applied to the tab page of the slug.</li>
 * 	<li><code>style_ + page slug</code> – receives the output of the CSS rules applied to the page of the slug.</li>
 * 	<li><code>style_ + extended class name</code> – receives the output of the CSS rules applied to the pages added by the instantiated class object.</li>
 * 	<li><code>script_ + page slug + _ + tab slug</code> – receives the output of the JavaScript script applied to the tab page of the slug.</li>
 * 	<li><code>script_ + page slug</code> – receives the output of the JavaScript script applied to the page of the slug.</li>
 * 	<li><code>script_ + extended class name</code> – receives the output of the JavaScript script applied to the pages added by the instantiated class object.</li>
 * 	<li><code>export_ + page slug + _ + tab slug</code> – receives the exporting array sent from the tab page.</li>
 * 	<li><code>export_ + page slug</code> – receives the exporting array submitted from the page.</li>
 * 	<li><code>export_ + extended class name + _ + input id</code> – [2.1.5+] receives the exporting array submitted from the specific export button.</li>
 * 	<li><code>export_ + extended class name + _ + field id</code> – [2.1.5+] receives the exporting array submitted from the specific field.</li>
 * 	<li><code>export_ + extended class name</code> – receives the exporting array submitted from the plugin.</li>
 * 	<li><code>import_ + page slug + _ + tab slug</code> – receives the importing array submitted from the tab page.</li>
 * 	<li><code>import_ + page slug</code> – receives the importing array submitted from the page.</li>
 * 	<li><code>import_ + extended class name + _ + input id</code> – [2.1.5+] receives the importing array submitted from the specific import button.</li>
 * 	<li><code>import_ + extended class name + _ + field id</code> – [2.1.5+] receives the importing array submitted from the specific import field.</li>
 * 	<li><code>import_ + extended class name</code> – receives the importing array submitted from the plugin.</li>
 * </ul>
 * <h3>Remarks</h3>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p>
 * <h3>Examples</h3>
 * <p>If the extended class name is Sample_Admin_Pages, defining the following class method will embed a banner image in all pages created by the class.</p>
 * <code>class Sample_Admin_Pages extends AdminPageFramework {
 * ...
 *     function head_Sample_Admin_Pages( $sContent ) {
 *         return '&lt;div style="float:right;"&gt;&lt;img src="' . plugins_url( 'img/banner468x60.gif', __FILE__ ) . '" /&gt;&lt;/div&gt;' 
 *             . $sContent;
 *     }
 * ...
 * }</code>
 * <p>If the created page slug is my_first_setting_page, defining the following class method will filter the middle part of the page output.</p>
 * <code>class Sample_Admin_Pages extends AdminPageFramework {
 * ...
 *     function content_my_first_setting_page( $sContent ) {
 *         return $sContent . '&lt;p&gt;Hello world!&lt;/p&gt;';
 *     }
 * ...
 * }</code>
 * <h3>Timing of Hooks</h3>
 * <blockquote>------ When the class is instantiated ------
 *  
 *  start_ + extended class name
 *  load_ + extended class name
 *  load_ + page slug
 *  load_ + page slug + _ + tab slug
 *  
 *  ------ Start Rendering HTML ------
 *  
 *  &lt;head&gt;
 *      &lt;style type="text/css" name="admin-page-framework"&gt;
 *          style_ + page slug + _ + tab slug
 *          style_ + page slug
 *          style_ + extended class name
 *          script_ + page slug + _ + tab slug
 *          script_ + page slug
 *          script_ + extended class name       
 *      &lt;/style&gt;
 *  
 *  &lt;/head&gt;
 *  
 *  do_before_ + extended class name
 *  do_before_ + page slug
 *  do_before_ + page slug + _ + tab slug
 *  
 *  &lt;div class="wrap"&gt;
 *  
 *      head_ + page slug + _ + tab slug
 *      head_ + page slug
 *      head_ + extended class name                 
 *  
 *      &lt;div class="acmin-page-framework-container"&gt;
 *          &lt;form action="options.php" method="post"&gt;
 *  
 *              do_form_ + page slug + _ + tab slug
 *              do_form_ + page slug
 *              do_form_ + extended class name
 *  
 *              extended class name + _ + section_ + section ID
 *              extended class name + _ + field_ + field ID
 *  
 *              content_ + page slug + _ + tab slug
 *              content_ + page slug
 *              content_ + extended class name
 *  
 *              do_ + extended class name                   
 *              do_ + page slug
 *              do_ + page slug + _ + tab slug
 *  
 *          &lt;/form&gt;                 
 *      &lt;/div&gt;
 *  
 *          foot_ + page slug + _ + tab slug
 *          foot_ + page slug
 *          foot_ + extended class name         
 *  
 *  &lt;/div&gt;
 *  
 *  do_after_ + extended class name
 *  do_after_ + page slug
 *  do_after_ + page slug + _ + tab slug
 *  
 *  
 *  ----- After Submitting the Form ------
 *  
 *  validation_ + page slug + _ + tab slug 
 *  validation_ + page slug 
 *  validation_ + extended class name + _ + submit button input id
 *  validation_ + extended class name + _ + submit button field id
 *  validation_ + extended class name 
 *  export_ + page slug + _ + tab slug 
 *  export_ + page slug 
 *  export_ + extended class name
 *  import_ + page slug + _ + tab slug
 *  import_ + page slug
 *  import_ + extended class name</blockquote>
 * @abstract
 * @since			2.0.0
 * @use				AdminPageFramework_Properties
 * @use				AdminPageFramework_Debug
 * @use				AdminPageFramework_Properties
 * @use				AdminPageFramework_Messages
 * @use				AdminPageFramework_Link
 * @use				AdminPageFramework_Utility
 * @remark			This class stems from several abstract classes.
 * @extends			AdminPageFramework_Setting
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Page
 */
abstract class AdminPageFramework extends AdminPageFramework_Setting {
		
	/**
    * The common properties shared among sub-classes. 
	* 
	* @since			2.0.0
	* @access		protected
	* @var			object			an instance of AdminPageFramework_Properties will be assigned in the constructor.
    */		
	protected $oProps;	
	
	/**
    * The object that provides the debug methods. 
	* @since			2.0.0
	* @access		protected
	* @var			object			an instance of AdminPageFramework_Debug will be assigned in the constructor.
    */		
	protected $oDebug;
	
	/**
    * Provides the methods for text messages of the framework. 
	* @since			2.0.0
	* @access		protected
	* @var			object			an instance of AdminPageFramework_Messages will be assigned in the constructor.
    */	
	protected $oMsg;
	
	/**
    * Provides the methods for creating HTML link elements. 
	* @since			2.0.0
	* @access		protected
	* @var			object			an instance of AdminPageFramework_Link will be assigned in the constructor.
    */		
	protected $oLink;
	
	/**
    * Provides the utility methods. 
	* @since			2.0.0
	* @access		protected
	* @var			object			an instance of AdminPageFramework_Utility will be assigned in the constructor.
    */			
	protected $oUtil;
	
	/**
	 * Provides the methods to insert head tag elements.
	 * 
	 * @since			2.1.5
	 * @access			protected
	 * @var				object			an instance of AdminPageFramework_HeadTag_Page will be assigne in the constructor.
	 */
	protected $oHeadTag;
	
	/**
	 * The constructor of the main class.
	 * 
	 * <h4>Example</h4>
	 * <code>if ( is_admin() )
	 * 		new MyAdminPageClass( 'my_custom_option_key', __FILE__ );
	 * </code>
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @param			string		$sOptionKey			( optional ) specifies the option key name to store in the options table. If this is not set, the extended class name will be used.
	 * @param			string		$sCallerPath			( optional ) used to retrieve the plugin/theme details to auto-insert the information into the page footer.
	 * @param			string		$sCapability			( optional ) sets the overall access level to the admin pages created by the framework. The used capabilities are listed here( http://codex.wordpress.org/Roles_and_Capabilities ). If not set, <strong>manage_options</strong> will be assigned by default. The capability can be set per page, tab, setting section, setting field.
	 * @param			string		$sTextDomain			( optional ) the text domain( http://codex.wordpress.org/I18n_for_WordPress_Developers#Text_Domains ) used for the framework's text strings. Default: admin-page-framework.
	 * @remark			the scope is public because often <code>parent::__construct()</code> is used.
	 * @return			void		returns nothing.
	 */
	public function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability=null, $sTextDomain='admin-page-framework' ){
				 
		// Variables
		$sClassName = get_class( $this );
		
		// Objects
		$this->oProps = new AdminPageFramework_Properties( $this, $sClassName, $sOptionKey, $sCapability );
		$this->oMsg = AdminPageFramework_Messages::instantiate( $sTextDomain );
		$this->oPageLoadInfo = AdminPageFramework_PageLoadInfo_Page::instantiate( $this->oProps, $this->oMsg );
		$this->oUtil = new AdminPageFramework_Utility;
		$this->oDebug = new AdminPageFramework_Debug;
		$this->oLink = new AdminPageFramework_Link( $this->oProps, $sCallerPath, $this->oMsg );
		$this->oHeadTag = new AdminPageFramework_HeadTag_Page( $this->oProps );
								
		if ( is_admin() ) {

			// Hook the menu action - adds the menu items.
			add_action( 'wp_loaded', array( $this, 'setUp' ) );
			
			// AdminPageFramework_Menu
			add_action( 'admin_menu', array( $this, 'buildMenus' ), 98 );
			
			// AdminPageFramework_Page
			add_action( 'admin_menu', array( $this, 'finalizeInPageTabs' ), 99 );	// must be called before the registerSettings() method.
			
			// AdminPageFramework_Setting
			add_action( 'admin_menu', array( $this, 'registerSettings' ), 100 );
			
			// Redirect Buttons
			add_action( 'admin_init', array( $this, 'checkRedirects' ) );
						
			// The contextual help pane.
			add_action( "admin_head", array( $this, 'registerHelpTabs' ), 200 );
						
			// The capability for the settings. $this->oProps->sOptionKey is the part that is set in the settings_fields() function.
			// This prevents the "Cheatin' huh?" message.
			add_filter( "option_page_capability_{$this->oProps->sOptionKey}", array( $this->oProps, 'getCapability' ) );
						
			// For earlier loading than $this->setUp
			$this->oUtil->addAndDoAction( $this, self::$aPrefixes['start_'] . $this->oProps->sClassName );
		
		}
	}	
		
	/**
	 * The magic method which redirects callback-function calls with the pre-defined prefixes for hooks to the appropriate methods. 
	 * 
	 * @access			public
	 * @remark			the users do not need to call or extend this method unless they know what they are doing.
	 * @param			string		$sMethodName		the called method name. 
	 * @param			array		$aArgs			the argument array. The first element holds the parameters passed to the called method.
	 * @return			mixed		depends on the called method. If the method name matches one of the hook prefixes, the redirected methods return value will be returned. Otherwise, none.
	 * @since			2.0.0
	 */
	public function __call( $sMethodName, $aArgs=null ) {		
				 
		// Variables
		// The currently loading in-page tab slug. Careful that not all cases $sMethodName have the page slug.
		$sPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;	
		$sTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProps->getDefaultInPageTab( $sPageSlug );	

		// If it is a pre callback method, call the redirecting method.
		// add_settings_section() callback
		if ( substr( $sMethodName, 0, strlen( 'section_pre_' ) )	== 'section_pre_' ) return $this->renderSectionDescription( $sMethodName );  // section_pre_
		
		// add_settings_field() callback
		if ( substr( $sMethodName, 0, strlen( 'field_pre_' ) )	== 'field_pre_' ) return $this->renderSettingField( $aArgs[ 0 ], $sPageSlug );  // field_pre_
		
		// register_setting() callback
		if ( substr( $sMethodName, 0, strlen( 'validation_pre_' ) )	== 'validation_pre_' ) return $this->doValidationCall( $sMethodName, $aArgs[ 0 ] );  // section_pre_

		// load-{page} callback
		if ( substr( $sMethodName, 0, strlen( 'load_pre_' ) )	== 'load_pre_' ) return $this->doPageLoadCall( substr( $sMethodName, strlen( 'load_pre_' ) ), $sTabSlug, $aArgs[ 0 ] );  // load_pre_

		// The callback of the call_page_{page slug} action hook
		if ( $sMethodName == $this->oProps->sClassHash . '_page_' . $sPageSlug )
			return $this->renderPage( $sPageSlug, $sTabSlug );	
		
		// If it's one of the framework's callback methods, do nothing.	
		if ( $this->isFrameworkCallbackMethod( $sMethodName ) )
			return isset( $aArgs[0] ) ? $aArgs[0] : null;	// if $aArgs[0] is set, it's a filter, otherwise, it's an action.		

		
	}	
	
	/**
	 * Determines whether the method name matches the pre-defined hook prefixes.
	 * @access			private
	 * @since			2.0.0
	 * @remark			the users do not need to call or extend this method unless they know what they are doing.
	 * @param			string			$sMethodName			the called method name
	 * @return			boolean			If it is a framework's callback method, returns true; otherwise, false.
	 */
	private function isFrameworkCallbackMethod( $sMethodName ) {

		if ( substr( $sMethodName, 0, strlen( "{$this->oProps->sClassName}_" ) ) == "{$this->oProps->sClassName}_" )	// e.g. {instantiated class name} + _field_ + {field id}
			return true;
		
		if ( substr( $sMethodName, 0, strlen( "validation_{$this->oProps->sClassName}_" ) ) == "validation_{$this->oProps->sClassName}_" )	// e.g. validation_{instantiated class name}_ + {field id / input id}
			return true;

		if ( substr( $sMethodName, 0, strlen( "field_types_{$this->oProps->sClassName}" ) ) == "field_types_{$this->oProps->sClassName}" )	// e.g. field_types_{instantiated class name}
			return true;
			
		foreach( self::$aPrefixes as $sPrefix ) {
			if ( substr( $sMethodName, 0, strlen( $sPrefix ) )	== $sPrefix  ) 
				return true;
		}
		return false;
	}
	
	/**
	* The method for all the necessary set-ups.
	* 
	* The users should override this method to set-up necessary settings. 
	* To perform certain tasks prior to this method, use the <em>start_ + extended class name</em> hook that is triggered at the end of the class constructor.
	* 
	* <h4>Example</h4>
	* <code>public function setUp() {
	* 	$this->setRootMenuPage( 'APF Form' ); 
	* 	$this->addSubMenuItems(
	* 		array(
	* 			'title' => 'Form Fields',
	* 			'page_slug' => 'apf_form_fields',
	* 		)
	* 	);		
	* 	$this->addSettingSections(
	* 		array(
	* 			'section_id'		=> 'text_fields',
	* 			'page_slug'		=> 'apf_form_fields',
	* 			'title'			=> 'Text Fields',
	* 			'description'	=> 'These are text type fields.',
	* 		)
	* 	);
	* 	$this->addSettingFields(
	* 		array(	
	* 			'field_id' => 'text',
	* 			'section_id' => 'text_fields',
	* 			'title' => 'Text',
	* 			'type' => 'text',
	* 		)	
	* 	);			
	* }</code>
	* @abstract
	* @since			2.0.0
	* @remark			This is a callback for the <em>wp_loaded</em> hook. Thus, its public.
	* @remark			In v1, this is triggered with the <em>admin_menu</em> hook; however, in v2, this is triggered with the <em>wp_loaded</em> hook.
	* @access 			public
	* @return			void
	*/	
	public function setUp() {}
	
	/**
	* Adds sub-menu items on the left sidebar of the administration panel. 
	* 
	* It supports pages and links. Each of them has the specific array structure.
	* 
	* <h4>Sub-menu Page Array</h4>
	* <ul>
	* <li><strong>title</strong> - ( string ) the page title of the page.</li>
	* <li><strong>page_slug</strong> - ( string ) the page slug of the page. Non-alphabetical characters should not be used including dots(.) and hyphens(-).</li>
	* <li><strong>screen_icon</strong> - ( optional, string ) either the ID selector name from the following list or the icon URL. The size of the icon should be 32 by 32 in pixel.
	*	<pre>edit, post, index, media, upload, link-manager, link, link-category, edit-pages, page, edit-comments, themes, plugins, users, profile, user-edit, tools, admin, options-general, ms-admin, generic</pre>
	*	<p><strong>Notes</strong>: the <em>generic</em> icon is available WordPress version 3.5 or above.</p>
	* </li>
	* <li><strong>sCapability</strong> - ( optional, string ) the access level to the created admin pages defined [here](http://codex.wordpress.org/Roles_and_Capabilities). If not set, the overall capability assigned in the class constructor, which is *manage_options* by default, will be used.</li>
	* <li><strong>order</strong> - ( optional, integer ) the order number of the page. The lager the number is, the lower the position it is placed in the menu.</li>
	* <li><strong>fShowPageHeadingTab</strong> - ( optional, boolean ) if this is set to false, the page title won't be displayed in the page heading tab. Default: true.</li>
	* </ul>
	* <h4>Sub-menu Link Array</h4>
	* <ul>
	* <li><strong>title</strong> - ( string ) the link title.</li>
	* <li><strong>href</strong> - ( string ) the URL of the target link.</li>
	* <li><strong>sCapability</strong> - ( optional, string ) the access level to show the item, defined [here](http://codex.wordpress.org/Roles_and_Capabilities). If not set, the overall capability assigned in the class constructor, which is *manage_options* by default, will be used.</li>
	* <li><strong>order</strong> - ( optional, integer ) the order number of the page. The lager the number is, the lower the position it is placed in the menu.</li>
	* <li><strong>fShowPageHeadingTab</strong> - ( optional, boolean ) if this is set to false, the page title won't be displayed in the page heading tab. Default: true.</li>
	* </ul>
	* 
	* <h4>Example</h4>
	* <code>$this->addSubMenuItems(
	*		array(
	*			'title' => 'Various Form Fields',
	*			'page_slug' => 'first_page',
	*			'screen_icon' => 'options-general',
	*		),
	*		array(
	*			'title' => 'Manage Options',
	*			'page_slug' => 'second_page',
	*			'screen_icon' => 'link-manager',
	*		),
	*		array(
	*			'title' => 'Google',
	*			'href' => 'http://www.google.com',	
	*			'fShowPageHeadingTab' => false,	// this removes the title from the page heading tabs.
	*		),
	*	);</code>
	* 
	* @since			2.0.0
	* @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			array		$aSubMenuItem1		a first sub-menu array.
	* @param			array		$aSubMenuItem2		( optional ) a second sub-menu array.
	* @param			array		$_and_more				( optional ) third and add items as many as necessary with next parameters.
	* @access 			protected
	* @return			void
	*/		
	protected function addSubMenuItems( $aSubMenuItem1, $aSubMenuItem2=null, $_and_more=null ) {
		foreach ( func_get_args() as $aSubMenuItem ) 
			$this->addSubMenuItem( $aSubMenuItem );		
	}
	
	/**
	* Adds the given sub-menu item on the left sidebar of the administration panel.
	* 
	* This only adds one single item, called by the above <em>addSubMenuItem()</em> method.
	* 
	* The array structure of the parameter is documented in the <em>addSubMenuItem()</em> method section.
	* 
	* @since			2.0.0
	* @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	* @remark			This is not intended to be used by the user.
	* @param			array		$aSubMenuItem			a first sub-menu array.
	* @access 			private
	* @return			void
	*/	
	private function addSubMenuItem( $aSubMenuItem ) {
		if ( isset( $aSubMenuItem['href'] ) ) {
			$aSubMenuLink = $aSubMenuItem + AdminPageFramework_Link::$_aStructure_SubMenuLink;
			$this->oLink->addSubMenuLink(
				$aSubMenuLink['title'],
				$aSubMenuLink['href'],
				$aSubMenuLink['sCapability'],
				$aSubMenuLink['order'],
				$aSubMenuLink['fShowPageHeadingTab'],
				$aSubMenuLink['fShowInMenu']
			);			
		}
		else { // if ( $aSubMenuItem['type'] == 'page' ) {
			$aSubMenuPage = $aSubMenuItem + self::$_aStructure_SubMenuPage;	// avoid undefined index warnings.
			$this->addSubMenuPage(
				$aSubMenuPage['title'],
				$aSubMenuPage['page_slug'],
				$aSubMenuPage['screen_icon'],
				$aSubMenuPage['sCapability'],
				$aSubMenuPage['order'],	
				$aSubMenuPage['fShowPageHeadingTab'],
				$aSubMenuPage['fShowInMenu']
			);				
		}
	}

	/**
	* Adds the given link into the menu on the left sidebar of the administration panel.
	* 
	* @since			2.0.0
	* @remark			The user may use this method in their extended class definition.
	* @param			string		$sMenuTitle			the menu title.
	* @param			string		$sURL					the URL linked to the menu.
	* @param			string		$sCapability			( optional ) the access level. ( http://codex.wordpress.org/Roles_and_Capabilities)
	* @param			string		$nOrder				( optional ) the order number. The larger it is, the lower the position it gets.
	* @param			string		$bShowPageHeadingTab		( optional ) if set to false, the menu title will not be listed in the tab navigation menu at the top of the page.
	* @access 			protected
	* @return			void
	*/	
	protected function addSubMenuLink( $sMenuTitle, $sURL, $sCapability=null, $nOrder=null, $bShowPageHeadingTab=true, $bShowInMenu=true ) {
		$this->oLink->addSubMenuLink( $sMenuTitle, $sURL, $sCapability, $nOrder, $bShowPageHeadingTab, $bShowInMenu );
	}

	/**
	* Adds the given link(s) into the description cell of the plugin listing table.
	* 
	* <h4>Example</h4>
	* <code>$this->addLinkToPluginDescription( 
	*		"&lt;a href='http://www.google.com'&gt;Google&lt;/a&gt;",
	*		"&lt;a href='http://www.yahoo.com'&gt;Yahoo!&lt;/a&gt;"
	*	);</code>
	* 
	* @since			2.0.0
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			string			$sTaggedLinkHTML1			the tagged HTML link text.
	* @param			string			$sTaggedLinkHTML2			( optional ) another tagged HTML link text.
	* @param			string			$_and_more					( optional ) add more as many as want by adding items to the next parameters.
	* @access 			protected
	* @return			void
	*/		
	protected function addLinkToPluginDescription( $sTaggedLinkHTML1, $sTaggedLinkHTML2=null, $_and_more=null ) {				
		$this->oLink->addLinkToPluginDescription( func_get_args() );		
	}

	/**
	* Adds the given link(s) into the title cell of the plugin listing table.
	* 
	* <h4>Example</h4>
	* <code>$this->addLinkToPluginTitle( 
	*		"&lt;a href='http://www.wordpress.org'&gt;WordPress&lt;/a&gt;"
	*	);</code>
	* 
	* @since			2.0.0
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			string			$sTaggedLinkHTML1			the tagged HTML link text.
	* @param			string			$sTaggedLinkHTML2			( optional ) another tagged HTML link text.
	* @param			string			$_and_more					( optional ) add more as many as want by adding items to the next parameters.
	* @access 			protected
	* @return			void
	*/	
	protected function addLinkToPluginTitle( $sTaggedLinkHTML1, $sTaggedLinkHTML2=null, $_and_more=null ) {	
		$this->oLink->addLinkToPluginTitle( func_get_args() );		
	}
	 
	/*
	 * Methods that access the properties.
	 */
	/**
	 * Sets the overall capability.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setCpability( 'read' );		// let subscribers access the pages.</code>
	 * 
	 * @since			2.0.0
	 * @see				http://codex.wordpress.org/Roles_and_Capabilities
	 * @remark			The user may directly edit <code>$this->oProps->sCapability</code> instead.
	 * @param			string			$sCapability			The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> for the created pages.
	 * @return			void
	 */ 
	protected function setCapability( $sCapability ) {
		$this->oProps->sCapability = $sCapability;	
	}

	/**
	 * Sets the given HTML text into the footer on the left hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoLeft( '&lt;br /&gt;Custom Text on the left hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @remark			The user may directly edit <code>$this->oProps->aFooterInfo['sLeft']</code> instead.
	 * @param			string			$sHTML			The HTML code to insert.
	 * @param			boolean			$bAppend			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */	
	protected function setFooterInfoLeft( $sHTML, $bAppend=true ) {
		
		$this->oProps->aFooterInfo['sLeft'] = $bAppend 
			? $this->oProps->aFooterInfo['sLeft'] . $sHTML
			: $sHTML;
		
	}
	
	/**
	 * Sets the given HTML text into the footer on the right hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoRight( '&lt;br /&gt;Custom Text on the right hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @remark			The user may directly edit <code>$this->oProps->aFooterInfo['sRight']</code> instead.
	 * @param			string			$sHTML			The HTML code to insert.
	 * @param			boolean			$bAppend			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */	
	protected function setFooterInfoRight( $sHTML, $bAppend=true ) {
		
		$this->oProps->aFooterInfo['sRight'] = $bAppend 
			? $this->oProps->aFooterInfo['sRight'] . $sHTML
			: $sHTML;
		
	}
		
	/* 
	 * Callback methods
	 */ 
	public function checkRedirects() {

		// So it's not options.php. Now check if it's one of the plugin's added page. If not, do nothing.
		if ( ! ( isset( $_GET['page'] ) ) || ! $this->oProps->isPageAdded( $_GET['page'] ) ) return; 
		
		// If the Settings API has not updated the options, do nothing.
		if ( ! ( isset( $_GET['settings-updated'] ) && ! empty( $_GET['settings-updated'] ) ) ) return;

		// Check the settings error transient.
		$aError = $this->getFieldErrors( $_GET['page'], false );
		if ( ! empty( $aError ) ) return;
		
		// Okay, it seems the submitted data have been updated successfully.
		$sTransient = md5( trim( "redirect_{$this->oProps->sClassName}_{$_GET['page']}" ) );
		$sURL = get_transient( $sTransient );
		if ( $sURL === false ) return;
		
		// The redirect URL seems to be set.
		delete_transient( $sTransient );	// we don't need it any more.
		
		// if the redirect page is outside the plugin admin page, delete the plugin settings admin notices as well.
		// if ( ! $this->oCore->IsPluginPage( $sURL ) ) 	
			// delete_transient( md5( 'SettingsErrors_' . $this->oCore->sClassName . '_' . $this->oCore->sPageSlug ) );
				
		// Go to the page.
		$this->oUtil->goRedirect( $sURL );
		
	}
	
	
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>aDependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>sVersion</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>sMedia</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	 * </ul>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param			string			$sPageSlug		(optional) The page slug that the stylesheet should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$sTabSlug			(optional) The tab slug that the stylesheet should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */	
	public function enqueueStyle( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		return $this->oHeadTag->enqueueStyle( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );		
	}
	
	/**
	 * Enqueues a script by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>aDependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>sVersion</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>fInFooter</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 * 
	 * <h4>Example</h4>
	 * <code>$this->enqueueScript(  
	 *		plugins_url( 'asset/js/test.js' , __FILE__ ),	// source url or path
	 *		'apf_read_me', 	// page slug
	 *		'', 	// tab slug
	 *		array(
	 *			'handle_id' => 'my_script',	// this handle ID also is used as the object name for the translation array below.
	 *			'translation' => array( 
	 *				'a' => 'hello world!',
	 *				'style_handle_id' => $sStyleHandle,	// check the enqueued style handle ID here.
	 *			),
	 *		)
	 *	);</code>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param			string			$sPageSlug		(optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$sTabSlug			(optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */
	public function enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {	
		return $this->oHeadTag->enqueueScript( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
	}
		
	/**
	 * Sets an admin notice.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setAdminNotice( sprintf( 'Please click <a href="%1$s">here</a> to upgrade the options.', admin_url( 'admin.php?page="my_page"' ) ), 'updated' );</code>
	 * 
	 * @remark			It should be used before the 'admin_notices' hook is triggered.
	 * @since			2.1.2
	 * @param			string			$sMessage				The message to display
	 * @param			string			$sClassSelector		( optional ) The class selector used in the message HTML element. 'error' and 'updated' are prepared by WordPress but it's not limited to them and can pass a custom name. Default: 'error'
	 * @param			string			$sID					( optional ) The ID of the message. If not set, the hash of the message will be used.
	 */
	protected function setAdminNotice( $sMessage, $sClassSelector='error', $sID='' ) {
			
		$sID = $sID ? $sID : md5( $sMessage );
		$this->oProps->aAdminNotices[ md5( $sMessage ) ] = array(  
			'sMessage' => $sMessage,
			'sClassSelector' => $sClassSelector,
			'sID' => $sID,
		);
		add_action( 'admin_notices', array( $this, 'printAdminNotices' ) );
		
	}
	/**
	 * A helper function for the above setAdminNotice() method.
	 * @since			2.1.2
	 * @internal
	 */
	public function printAdminNotices() {
		
		foreach( $this->oProps->aAdminNotices as $aAdminNotice ) 
			echo "<div class='{$aAdminNotice['sClassSelector']}' id='{$aAdminNotice['sID']}' ><p>"
				. $aAdminNotice['sMessage']
				. "</p></div>";
		
	}	
	
	/**
	 * Sets the disallowed query keys in the links that the framework generates.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setDisallowedQueryKeys( array( 'my-custom-admin-notice' ) );</code>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 */
	public function setDisallowedQueryKeys( $aQueryKeys, $bAppend=true ) {
		
		if ( ! $bAppend ) {
			$this->oProps->aDisallowedQueryKeys = $aQueryKeys;
			return;
		}
		
		$aNewQueryKeys = array_merge( $aQueryKeys, $this->oProps->aDisallowedQueryKeys );
		$aNewQueryKeys = array_filter( $aNewQueryKeys );	// drop non-values
		$aNewQueryKeys = array_unique( $aNewQueryKeys );	// drop duplicates
		$this->oProps->aDisallowedQueryKeys = $aNewQueryKeys;
		
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_Messages' ) ) :
/**
 * Provides methods for text messages.
 *
 * @since			2.0.0
 * @since			2.1.6			Multiple instances of this class are disallowed.
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 */
class AdminPageFramework_Messages {

	/**
	 * Stores the framework's messages.
	 * 
	 * @remark			The user can modify this property directly.
	 */ 
	public $aMessages = array();

	/**
	 * 
	 * 
	 */
	private static $_oInstance;
	
	/**
	 * Ensures that only one instance of this class object exists. ( no multiple instances of this object ) 
	 * 
	 * @since			2.1.6
	 * @remark			This class should be instantiated via this method.
	 */
	public static function instantiate( $sTextDomain='admin-page-framework' ) {
		
		if ( ! isset( self::$_oInstance ) && ! ( self::$_oInstance instanceof AdminPageFramework_Messages ) ) 
			self::$_oInstance = new AdminPageFramework_Messages( $sTextDomain );
		return self::$_oInstance;
		
	}	
	
	public function __construct( $sTextDomain='admin-page-framework' ) {
		
		$this->sTextDomain = $sTextDomain;
		$this->aMessages = array(
			
			// AdminPageFramework
			'option_updated'		=> __( 'The options have been updated.', 'admin-page-framework' ),
			'option_cleared'		=> __( 'The options have been cleared.', 'admin-page-framework' ),
			'export_options'		=> __( 'Export Options', 'admin-page-framework' ),
			'import_options'		=> __( 'Import Options', 'admin-page-framework' ),
			'submit'				=> __( 'Submit', 'admin-page-framework' ),
			'import_error'			=> __( 'An error occurred while uploading the import file.', 'admin-page-framework' ),
			'uploaded_file_type_not_supported'	=> __( 'The uploaded file type is not supported.', 'admin-page-framework' ),
			'could_not_load_importing_data' => __( 'Could not load the importing data.', 'admin-page-framework' ),
			'imported_data'			=> __( 'The uploaded file has been imported.', 'admin-page-framework' ),
			'not_imported_data' 	=> __( 'No data could be imported.', 'admin-page-framework' ),
			'add'					=> __( 'Add', 'admin-page-framework' ),
			'remove'				=> __( 'Remove', 'admin-page-framework' ),
			'upload_image'			=> __( 'Upload Image', 'admin-page-framework' ),
			'use_this_image'		=> __( 'Use This Image', 'admin-page-framework' ),
			'reset_options'			=> __( 'Are you sure you want to reset the options?', 'admin-page-framework' ),
			'confirm_perform_task'	=> __( 'Please confirm if you want to perform the specified task.', 'admin-page-framework' ),
			'option_been_reset'		=> __( 'The options have been reset.', 'admin-page-framework' ),
			'specified_option_been_deleted'	=> __( 'The specified options have been deleted.', 'admin-page-framework' ),
			
			// AdminPageFramework_PostType
			'title'			=> __( 'Title', 'admin-page-framework' ),	
			'author'		=> __( 'Author', 'admin-page-framework' ),	
			'categories'	=> __( 'Categories', 'admin-page-framework' ),
			'tags'			=> __( 'Tags', 'admin-page-framework' ),
			'comments' 		=> __( 'Comments', 'admin-page-framework' ),
			'date'			=> __( 'Date', 'admin-page-framework' ), 
			'show_all'		=> __( 'Show All', 'admin-page-framework' ),
			
			// For the meta box class
			
			// AdminPageFramework_Link_Base
			'powered_by'	=> __( 'Powered by', 'admin-page-framework' ),
			
			// AdminPageFramework_Link
			'settings'		=> __( 'Settings', 'admin-page-framework' ),
			
			// AdminPageFramework_Link_PostType
			'manage'		=> __( 'Manage', 'admin-page-framework' ),
			
			// AdminPageFramework_InputFieldTypeDefinition_Base
			'select_image'			=> __( 'Select Image', 'admin-page-framework' ),
			'upload_file'			=> __( 'Upload File', 'admin-page-framework' ),
			'use_this_file'			=> __( 'Use This File', 'admin-page-framework' ),
			'select_file'			=> __( 'Select File', 'admin-page-framework' ),
			
			// AdminPageFramework_PageLoadInfo_Base
			'queries_in_seconds'	=> __( '%s queries in %s seconds.', 'admin-page-framework' ),
			'out_of_x_memory_used'	=> __( '%s out of %s MB (%s) memory used.', 'admin-page-framework' ),
			'peak_memory_usage'		=> __( 'Peak memory usage %s MB.', 'admin-page-framework' ),
			'initial_memory_usage'	=> __( 'Initial memory usage  %s MB.', 'admin-page-framework' ),
			
		);		
		
	}
	public function __( $sKey ) {
		
		return isset( $this->aMessages[ $sKey ] )
			? __( $this->aMessages[ $sKey ], $this->sTextDomain )
			: '';
			
	}
	
	public function _e( $sKey ) {
		
		if ( isset( $this->aMessages[ $sKey ] ) )
			_e( $this->aMessages[ $sKey ], $this->sTextDomain );
			
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_Properties_Base' ) ) :

/**
 * The base class for Property classes.
 * 
 * Provides the common methods  and properties for the property classes that are used by the main class, the meta box class, and the post type class.
 * @since			2.1.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 */ 
abstract class AdminPageFramework_Properties_Base {

	/**
	 * Stores the main (caller) object.
	 * 
	 * @since			2.1.5
	 */
	protected $oCaller;	
	
	/**
	 * Stores the script to be embedded in the head tag.
	 * 
	 * @remark			This should be an empty string by default since the related methods uses the append operator.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from each extended property class.
	 * @internal
	 */ 			
	public $sScript = '';	

	/**
	 * Stores the CSS rules to be embedded in the head tag.
	 * 
	 * @remark			This should be an empty string by default since the related methods uses the append operator.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from each extended property class.
	 * @internal
	 */ 		
	public $sStyle = '';	
	
	/**
	 * Stores the CSS rules for IE to be embedded in the head tag.
	 * 
	 * @remark			This should be an empty string by default since the related methods uses the append operator.
	 * @since			2.0.0 to 2.1.4
	 * @internal
	 */ 
	public $sStyleIE = '';	
	
	/**
	 * Stores the field type definitions.
	 * 
	 * @since			2.1.5
	 * @internal
	 */
	public $aFieldTypeDefinitions = array();
	
	/**
	 * The default CSS rules loaded in the head tag of the created admin pages.
	 * 
	 * @since			2.0.0
	 * @var				string
	 * @static
	 * @remark			It is accessed from the main class and meta box class.
	 * @access			public	
	 * @internal	
	 */
	public static $sDefaultStyle =
		".wrap div.updated, 
		.wrap div.settings-error { 
			clear: both; 
			margin-top: 16px;
		} 		

		.contextual-help-description {
			clear: left;	
			display: block;
			margin: 1em 0;
		}
		.contextual-help-tab-title {
			font-weight: bold;
		}
		
		/* Delimiter */
		.admin-page-framework-fields .delimiter {
			display: inline;
		}
		/* Description */
		.admin-page-framework-fields .admin-page-framework-fields-description {
			/* margin-top: 0px; */
			/* margin-bottom: 0.5em; */
			margin-bottom: 0;
		}
		/* Input form elements */
		.admin-page-framework-field {
			display: inline;
			margin-top: 1px;
			margin-bottom: 1px;
		}
		.admin-page-framework-field .admin-page-framework-input-label-container {
			margin-bottom: 0.25em;
		}
		@media only screen and ( max-width: 780px ) {	/* For WordPress v3.8 or greater */
			.admin-page-framework-field .admin-page-framework-input-label-container {
				margin-bottom: 0.5em;
			}
		}			
		.admin-page-framework-field input[type='radio'],
		.admin-page-framework-field input[type='checkbox']
		{
			margin-right: 0.5em;
		}		
		
		.admin-page-framework-field .admin-page-framework-input-label-string {
			padding-right: 1em;	/* for checkbox label strings, a right padding is needed */
		}
		.admin-page-framework-field .admin-page-framework-input-button-container {
			padding-right: 1em; 
		}
		.admin-page-framework-field-radio .admin-page-framework-input-label-container,
		.admin-page-framework-field-select .admin-page-framework-input-label-container,
		.admin-page-framework-field-checkbox .admin-page-framework-input-label-container 
		{
			padding-right: 1em;
		}

		.admin-page-framework-field .admin-page-framework-input-container {
			display: inline-block;
			vertical-align: middle; 
		}
		.admin-page-framework-field-text .admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field-textarea .admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field-color .admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field-select .admin-page-framework-field .admin-page-framework-input-label-container
		{
			vertical-align: top; 
		}
		.admin-page-framework-field-image .admin-page-framework-field .admin-page-framework-input-label-container {			
			vertical-align: middle;
		}
		.admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field .admin-page-framework-input-label-string
		{
			display: inline-block;		
			vertical-align: middle;
		}
		.admin-page-framework-field-textarea .admin-page-framework-input-label-string {
			vertical-align: top;
			margin-top: 2px;
		}
		
		.admin-page-framework-field-posttype .admin-page-framework-field input[type='checkbox'] { 
			margin-top: 0px;
		}
		.admin-page-framework-field-posttype .admin-page-framework-field {
			display: inline-block;
		}
		.admin-page-framework-field-radio .admin-page-framework-field .admin-page-framework-input-container {
			display: inline;
		}
		
		/* Repeatable Fields */		
		.admin-page-framework-field.repeatable {
			clear: both;
			display: block;
		}
		.admin-page-framework-repeatable-field-buttons {
			float: right;
			margin-bottom: 0.5em;
		}
		.admin-page-framework-repeatable-field-buttons .repeatable-field-button {
			margin: 0 2px;
			font-weight: normal;
			vertical-align: middle;
			text-align: center;
		}

		/* Import Field */
		.admin-page-framework-field-import input {
			margin-right: 0.5em;
		}
		/* Page Load Stats */
		#admin-page-framework-page-load-stats {
			clear: both;
			display: inline-block;
			width: 100%
		}
		#admin-page-framework-page-load-stats li{
			display: inline;
			margin-right: 1em;
		}		
		
		/* To give the footer area more space */
		#wpbody-content {
			padding-bottom: 140px;
		}
		";	
		
	/**
	 * The default CSS rules for IE loaded in the head tag of the created admin pages.
	 * @since			2.1.1
	 * @since			2.1.5			Moved the contents to the taxonomy field definition so it become an empty string.
	 */
	public static $sDefaultStyleIE = '';
		

	/**
	 * Stores enqueuing script URLs and their criteria.
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */
	public $aEnqueuingScripts = array();
	/**	
	 * Stores enqueuing style URLs and their criteria.
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */	
	public $aEnqueuingStyles = array();
	/**
	 * Stores the index of enqueued scripts.
	 * 
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */
	public $iEnqueuedScriptIndex = 0;
	/**
	 * Stores the index of enqueued styles.
	 * 
	 * The index number will be incremented as a script is enqueued regardless a previously added enqueue item has been removed or not.
	 * This is because this index number will be used for the script handle ID which is automatically generated.
	 * 
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */	
	public $iEnqueuedStyleIndex = 0;		
		
	function __construct( $oCaller ) {
		
		$this->oCaller = $oCaller;
		$GLOBALS['aAdminPageFramework'] = isset( $GLOBALS['aAdminPageFramework'] ) && is_array( $GLOBALS['aAdminPageFramework'] ) 
			? $GLOBALS['aAdminPageFramework']
			: array();

	}
	
	/**
	 * Calculates the subtraction of two values with the array key of <em>order</em>
	 * 
	 * This is used to sort arrays.
	 * 
	 * @since			2.0.0
	 * @remark			a callback method for uasort().
	 * @return			integer
	 */ 
	public function sortByOrder( $a, $b ) {	
		return $a['order'] - $b['order'];
	}		
	
	/**
	 * Returns the caller object.
	 * 
	 * This is used from other sub classes that need to retrieve the caller object.
	 * 
	 * @since			2.1.5
	 * @access			public	
	 * @internal
	 * @return			object			The caller class object.
	 */		
	public function getParentObject() {
		return $this->oCaller;
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_MetaBox_Properties' ) ) :
/**
 * Provides the space to store the shared properties for meta boxes.
 * 
 * This class stores various types of values. This is used to encapsulate properties so that it helps to avoid naming conflicts.
 * 
 * @since			2.1.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 * @extends			AdminPageFramework_Properties_Base
 */
class AdminPageFramework_MetaBox_Properties extends AdminPageFramework_Properties_Base {

	/**
	 * Stores the meta box id(slug).
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @var				string
	 */ 	
	public $sMetaBoxID ='';
	
	/**
	 * Stores the meta box title.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @var				string
	 */ 
	public $sTitle = '';

	/**
	 * Stores the post type slugs associated with the meta box.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @var				array
	 */ 	
	public $aPostTypes = array();
	
	/**
	 * Stores the parameter value, context, for the add_meta_box() function. 
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @remark			The value can be either 'normal', 'advanced', or 'side'.
	 * @var				string
	 * @see				http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
	 */ 
	public $sContext = 'normal';

	/**
	 * Stores the parameter value, priority, for the add_meta_box() function. 
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @remark			The value can be either 'high', 'core', 'default' or 'low'.
	 * @var				string
	 * @see				http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
	 */ 	
	public $sPriority = 'default';
	
	/**
	 * Stores the extended class name.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 */ 
	public $sClassName = '';
	
	/**
	 * Stores the capability for displayable elements.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 */ 	
	public $sCapability = 'edit_posts';
	
	/**
	 * @internal
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	*/ 		
	public $sPrefixStart = 'start_';	
	
	/**
	 * Stores the field arrays for meta box form elements.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 			
	public $aFields = array();
	
	/**
	 * Stores option values for form fields.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */	 
	public $aOptions = array();
	
	/**
	 * Stores the media uploader box's title.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 
	public $sThickBoxTitle = '';
	
	/**
	 * Stores the label for for the "Insert to Post" button in the media uploader box.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 	
	public $sThickBoxButtonUseThis = '';

	/**
	 * Stores text to insert into the contextual help tab.
	 * @since			2.1.0
	 */ 
	public $aHelpTabText = array();
	
	/**
	 * Stores text to insert into the sidebar of a contextual help tab.
	 * @since			2.1.0
	 */ 
	public $aHelpTabTextSide = array();
	
	// Default values
	/**
	 * Represents the structure of field array for meta box form fields.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 
	public static $_aStructure_Field = array(
		'field_id'		=> null,	// ( mandatory ) the field ID
		'type'			=> null,	// ( mandatory ) the field type.
		'title' 			=> null,	// the field title
		'description'	=> null,	// an additional note 
		'sCapability'		=> null,	// an additional note 
		'tip'			=> null,	// pop up text
		// 'options'			=> null,	// ? don't remember what this was for
		'vValue'			=> null,	// allows to override the stored value
		'default'			=> null,	// allows to set default values.
		'sName'			=> null,	// allows to set custom field name
		'label'			=> '',		// sets the label for the field. Setting a non-null value will let it parsed with the loop ( foreach ) of the input element rendering method.
		'fIf'				=> true,
		'help'			=> null,	// since 2.1.0
		'helpAside'		=> null,	// since 2.1.0
		'show_inpage_tabTitleColumn'	=> null,	// since 2.1.2
		
		// The followings may need to be uncommented.
		// 'sClassName' => null,		// This will be assigned automatically in the formatting method.
		// 'sError' => null,			// error message for the field
		// 'sBeforeField' => null,
		// 'sAfterField' => null,
		// 'order' => null,			// do not set the default number here for this key.		

		'repeatable'		=> null,	// since 2.1.3		
	);
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_PostType_Properties' ) ) :
/**
 * Provides the space to store the shared properties for custom post types.
 * 
 * This class stores various types of values. This is used to encapsulate properties so that it helps to avoid naming conflicts.
 * 
 * @since			2.1.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 * @extends			AdminPageFramework_Properties_Base
 */
class AdminPageFramework_PostType_Properties extends AdminPageFramework_Properties_Base {
	
	/**
	 * Stores the post type slug.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @var				string
	 * @access			public
	 */ 
	public $sPostType = '';
	
	/**
	 * Stores the post type argument.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @var				array
	 * @access			public
	 */ 
	public $aPostTypeArgs = array();	

	/**
	 * Stores the extended class name.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @var				string
	 * @access			public
	 */ 	
	public $sClassName = '';

	/**
	 * Stores the column headers of the post listing table.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @see				http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_edit-post_type_columns
	 * @remark			This should be overriden in the constructor because it includes translated text.
	 * @internal
	 * @access			public
	 */ 	
	public $aColumnHeaders = array(
		'cb'			=> '<input type="checkbox" />',	// Checkbox for bulk actions. 
		'title'			=> 'Title',		// Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
		'author'		=> 'Author',		// Post author.
		// 'categories'	=> __( 'Categories', 'admin-page-framework' ),	// Categories the post belongs to. 
		// 'tags'		=> __( 'Tags', 'admin-page-framework' ),	// Tags for the post. 
		'comments' 		=> '<div class="comment-grey-bubble"></div>', // Number of pending comments. 
		'date'			=> 'Date', 	// The date and publish status of the post. 
	);		
	
	/**
	 * Stores the sortable column items.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 		
	public $aColumnSortable = array(
		'title' => true,
		'date'	=> true,
	);	
	
	/**
	 * Stores the caller script path.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @var				string
	 * @access			public
	 */ 		
	public $sCallerPath = '';
	
	// Prefixes
	/**
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @internal
	 * @access			protected
	 */ 	
	public $sPrefix_Start = 'start_';
	/**
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 	
	public $sPrefix_Cell = 'cell_';
	
	// Containers
	/**
	 * Stores custom taxonomy slugs.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 	
	public $aTaxonomies;		// stores the registering taxonomy info.
	
	/**
	 * Stores the taxonomy IDs as value to indicate whether the drop-down filter option should be displayed or not.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 	
	public $aTaxonomyTableFilters = array();	
	
	/**
	 * Stores removing taxonomy menus' info.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 	
	public $aTaxonomyRemoveSubmenuPages = array();	
	
	// Default Values
	/**
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 					
	public $bEnableAutoSave = true;	

	/**
	 * Stores the flag value which indicates whether author table filters should be enabled or not.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 					
	public $bEnableAuthorTableFileter = false;	
		
}
endif;

if ( ! class_exists( 'AdminPageFramework_Properties' ) ) :
/**
 * Provides the space to store the shared properties.
 * 
 * This class stores various types of values. This is used to encapsulate properties so that it helps to avoid naming conflicts.
 * 
 * @since			2.0.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 * @extends			AdminPageFramework_Properties_Base
 */
class AdminPageFramework_Properties extends AdminPageFramework_Properties_Base {
			
	/**
	 * Stores framework's instantiated object name.
	 * 
	 * @since			2.0.0
	 */ 
	public $sClassName;	
	
	/**
	 * Stores the md5 hash string of framework's instantiated object name.
	 * @since			2.1.1
	 */
	public $sClassHash;
	
	/**
	 * Stores the access level to the root page. 
	 * 
	 * When sub pages are added and the capability value is not provided, this will be applied.
	 * 
	 * @since			2.0.0
	 */ 	
	public $sCapability = 'manage_options';	
	
	/**
	 * Stores the tag for the page heading navigation bar.
	 * @since			2.0.0
	 */ 
	public $sPageHeadingTabTag = 'h2';

	/**
	 * Stores the tag for the in-page tab navigation bar.
	 * @since			2.0.0
	 */ 
	public $sInPageTabTag = 'h3';
	
	/**
	 * Stores the default page slug.
	 * @since			2.0.0
	 */ 	
	public $sDefaultPageSlug;
		
	// Container arrays.
	/**
	 * A two-dimensional array storing registering sub-menu(page) item information with keys of the page slug.
	 * @since			2.0.0
	 */ 	
	public $aPages = array(); 

	/**
	 * Stores the hidden page slugs.
	 * @since			2.1.4
	 */
	public $aHiddenPages = array();
	
	/**
	 * Stores the registered sub menu pages.
	 * 
	 * Unlike the above $aPages that holds the pages to be added, this stores the added pages. This is referred when adding a help section.
	 * 
	 * @since			2.1.0
	 */ 
	public $aRegisteredSubMenuPages = array();
	
	/**
	 * Stores the root menu item information for one set root menu item.
	 * @since			2.0.0
	 */ 		
	public $aRootMenu = array(
		'title' => null,				// menu label that appears on the menu list
		'page_slug' => null,			// menu slug that identifies the menu item
		'hrefIcon16x16' => null,		// the associated icon that appears beside the label on the list
		'intPosition'	=> null,		// determines the position of the menu
		'fCreateRoot' => null,			// indicates whether the framework should create the root menu or not.
	); 
	
	/**
	 * Stores in-page tabs.
	 * @since			2.0.0
	 */ 	
	public $aInPageTabs = array();				
	
	/**
	 * Stores the default in-page tab.
	 * @since			2.0.0
	 */ 		
	public $aDefaultInPageTabs = array();			
		
	/**
	 * Stores link text that is scheduled to be embedded in the plugin listing table's description column cell.
	 * @since			2.0.0
	 */ 			
	public $aPluginDescriptionLinks = array(); 

	/**
	 * Stores link text that is scheduled to be embedded in the plugin listing table's title column cell.
	 * @since			2.0.0
	 */ 			
	public $aPluginTitleLinks = array();			
	
	/**
	 * Stores the information to insert into the page footer.
	 * @since			2.0.0
	 */ 			
	public $aFooterInfo = array(
		'sLeft' => '',
		'sRight' => '',
	);
		
	// Settings API
	// public $aOptions;			// Stores the framework's options. Do not even declare the property here because the __get() magic method needs to be triggered when it accessed for the first time.

	/**
	 * The instantiated class name will be assigned in the constructor if the first parameter is not set.
	 * @since			2.0.0
	 */ 				
	public $sOptionKey = '';		

	/**
	 * Stores form sections.
	 * @since			2.0.0
	 */ 					
	public $aSections = array();
	
	/**
	 * Stores form fields
	 * @since			2.0.0
	 */ 					
	public $aFields = array();

	/**
	 * Stores contextual help tabs.
	 * @since			2.1.0
	 */ 	
	public $aHelpTabs = array();
	
	/**
	 * Set one of the followings: application/x-www-form-urlencoded, multipart/form-data, text/plain
	 * @since			2.0.0
	 */ 					
	public $sFormEncType = 'multipart/form-data';	
	
	/**
	 * Stores the label for for the "Insert to Post" button in the media uploader box.
	 * @since			2.0.0
	 * @internal
	 */ 	
	public $sThickBoxButtonUseThis = '';
	
	// Flags	
	/**
	 * Decides whether the setting form tag is rendered or not.	
	 * 
	 * This will be enabled when a settings section and a field is added.
	 * @since			2.0.0
	 */ 						
	public $bEnableForm = false;			
	
	/**
	 * Indicates whether the page title should be displayed.
	 * @since			2.0.0
	 */ 						
	public $bShowPageTitle = true;	
	
	/**
	 * Indicates whether the page heading tabs should be displayed.
	 * @since			2.0.0
	 * @remark			Used by the setPageHeadingTabsVisibility() method.
	 */ 	
	public $bShowPageHeadingTabs = true;

	/**
	 * Indicates whether the in-page tabs should be displayed.
	 * 
	 * This sets globally among the script using the framework. 
	 * 
	 * @since			2.1.2
	 * @remark			Used by the showInPageTabs() method.
	 */
	public $bShowInPageTabs = true;

	/**
	 * Stores the set administration notices.
	 * 
	 * The index number will be incremented as a script is enqueued regardless a previously added enqueue item has been removed or not.
	 * This is because this index number will be used for the style handle ID which is automatically generated.
	 * @since			2.1.2
	 */
	public $aAdminNotices	= array();
	
	/**
	 * Stores the disallowed query keys in the links generated by the main class of the framework.
	 * 
	 * @remark			Currently this does not take effect on the meta box and post type classes of the framework.
	 * @since			2.1.2
	 */
	public $aDisallowedQueryKeys	= array( 'settings-updated' );
	
	/**
	 * Construct the instance of AdminPageFramework_Properties class object.
	 * 
	 * @remark			Used by the showInPageTabs() method.
	 * @since			2.0.0
	 * @since			2.1.5			The $oCaller parameter was added.
	 * @return			void
	 */ 
	public function __construct( $oCaller, $sClassName, $sOptionKey, $sCapability='manage_options' ) {
		
		parent::__construct( $oCaller );
		
		$this->sClassName = $sClassName;		
		$this->sClassHash = md5( $sClassName );
		$this->sOptionKey = $sOptionKey ? $sOptionKey : $sClassName;
		$this->sCapability = empty( $sCapability ) ? $this->sCapability : $sCapability;
		
	}
	
	/*
	 * Magic methods
	 * */
	public function &__get( $sName ) {
		
		// If $this->aOptions is called for the first time, retrieve the option data from the database and assign to the property.
		// One this is done, calling $this->aOptions will not trigger the __get() magic method any more.
		// Without the the ampersand in the method name, it causes a PHP warning.
		if ( $sName == 'aOptions' ) {
			$this->aOptions = $this->getOptions();
			return $this->aOptions;	
		}
		
		// For regular undefined items, 
		return null;
		
	}
	
	/*
	 * Utility methods
	 * */
	
	/**
	 * Checks if the given page slug is one of the pages added by the framework.
	 * @since			2.0.0
	 * @since			2.1.0			Set the default value to the parameter and if the parameter value is empty, it applies the current $_GET['page'] value.
	 * @return			boolean			Returns true if it is of framework's added page; otherwise, false.
	 */
	public function isPageAdded( $sPageSlug='' ) {	
		
		$sPageSlug = ! empty( $sPageSlug ) ? $sPageSlug : ( isset( $_GET['page'] ) ? $_GET['page'] : '' );
		return ( array_key_exists( trim( $sPageSlug ), $this->aPages ) )
			? true
			: false;
	}
	
	/**
	 * Retrieves the default in-page tab from the given tab slug.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Made it public and moved from the AdminPageFramework_Page class since this method is used by the AdminPageFramework_HeadTab class as well.
	 * @internal
	 * @remark			Used in the __call() method in the main class.
	 * @return			string			The default in-page tab slug if found; otherwise, an empty string.
	 */ 		
	public function getDefaultInPageTab( $sPageSlug ) {
	
		if ( ! $sPageSlug ) return '';		
		return isset( $this->aDefaultInPageTabs[ $sPageSlug ] ) 
			? $this->aDefaultInPageTabs[ $sPageSlug ]
			: '';

	}	
	
	public function getOptions() {
		
		$vOptions = get_option( $this->sOptionKey );
		if ( empty( $vOptions ) )
			return array();		// casting array causes an 0 key element. So this way it can be avoided
		
		if ( is_array( $vOptions ) )	// if it's array, no problem.
			return $vOptions;
		
		return ( array ) $vOptions;	// finally cast array.
		
	}
	
	/*
	 * callback methods
	 */ 
	public function getCapability() {
		return $this->sCapability;
	}	
		
}
endif;

if ( ! class_exists( 'AdminPageFramework_CustomSubmitFields' ) ) :
/**
 * Provides helper methods that deal with custom submit fields and retrieve custom key elements.
 *
 * @abstract
 * @since			2.0.0
 * @remark			The classes that extend this include ExportOptions, ImportOptions, and Redirect.
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
abstract class AdminPageFramework_CustomSubmitFields {
	 
	public function __construct( $aPostElement ) {
		
		$this->aPostElement = $aPostElement;	// e.g. $_POST['__import'] or $_POST['__export'] or $_POST['__redirect']
		
	}
	
	/**
	 * Retrieves the value of the specified element key.
	 * 
	 * The element key is either a single key or two keys. The two keys means that the value is stored in the second dimension.
	 * 
	 * @since			2.0.0
	 */ 
	protected function getElement( $aElement, $aElementKey, $sElementKey='format' ) {
			
		$sFirstDimensionKey = $aElementKey[ 0 ];
		if ( ! isset( $aElement[ $sFirstDimensionKey ] ) || ! is_array( $aElement[ $sFirstDimensionKey ] ) ) return 'ERROR_A';

		/* For single element, e.g.
		 * <input type="hidden" name="__import[import_single][import_option_key]" value="APF_GettingStarted">
		 * <input type="hidden" name="__import[import_single][format]" value="array">
		 * */	
		if ( isset( $aElement[ $sFirstDimensionKey ][ $sElementKey ] ) && ! is_array( $aElement[ $sFirstDimensionKey ][ $sElementKey ] ) )
			return $aElement[ $sFirstDimensionKey ][ $sElementKey ];

		/* For multiple elements, e.g.
		 * <input type="hidden" name="__import[import_multiple][import_option_key][2]" value="APF_GettingStarted.txt">
		 * <input type="hidden" name="__import[import_multiple][format][2]" value="array">
		 * */
		if ( ! isset( $aElementKey[ 1 ] ) ) return 'ERROR_B';
		$sKey = $aElementKey[ 1 ];
		if ( isset( $aElement[ $sFirstDimensionKey ][ $sElementKey ][ $sKey ] ) )
			return $aElement[ $sFirstDimensionKey ][ $sElementKey ][ $sKey ];
			
		return 'ERROR_C';	// Something wrong happened.
		
	}	
	
	/**
	 * Retrieves an array consisting of two values.
	 * 
	 * The first element is the fist dimension's key and the second element is the second dimension's key.
	 * @since			2.0.0
	 */
	protected function getElementKey( $aElement, $sFirstDimensionKey ) {
		
		if ( ! isset( $aElement[ $sFirstDimensionKey ] ) ) return;
		
		// Set the first element the field ID.
		$aEkementKey = array( 0 => $sFirstDimensionKey );

		// For single export buttons, e.g. name="__import[submit][import_single]" 		
		if ( ! is_array( $aElement[ $sFirstDimensionKey ] ) ) return $aEkementKey;
		
		// For multiple ones, e.g. name="__import[submit][import_multiple][1]" 		
		foreach( $aElement[ $sFirstDimensionKey ] as $k => $v ) {
			
			// Only the pressed export button's element is submitted. In other words, it is necessary to check only one item.
			$aEkementKey[] = $k;
			return $aEkementKey;			
				
		}		
	}
		
	public function getFieldID() {
		
		// e.g.
		// single:		name="__import[submit][import_single]"
		// multiple:	name="__import[submit][import_multiple][1]"
		
		if ( isset( $this->sFieldID ) && $this->sFieldID  ) return $this->sFieldID;
		
		// Only the pressed element will be stored in the array.
		foreach( $this->aPostElement['submit'] as $sKey => $v ) {	// $this->aPostElement should have been set in the constructor.
			$this->sFieldID = $sKey;
			return $this->sFieldID;
		}
	}	
		
}
endif;

if ( ! class_exists( 'AdminPageFramework_ImportOptions' ) ) :
/**
 * Provides methods to import option data.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_CustomSubmitFields
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AdminPageFramework_ImportOptions extends AdminPageFramework_CustomSubmitFields {
	
	/* Example of $_FILES for a single import field. 
		Array (
			[__import] => Array (
				[name] => Array (
				   [import_single] => APF_GettingStarted_20130709 (1).json
				)
				[type] => Array (
					[import_single] => application/octet-stream
				)
				[tmp_name] => Array (
					[import_single] => Y:\wamp\tmp\php7994.tmp
				)
				[error] => Array (
					[import_single] => 0
				)
				[size] => Array (
					[import_single] => 715
				)
			)
		)
	*/
	
	public function __construct( $aFilesImport, $aPostImport ) {

		// Call the parent constructor. This must be done before the getFieldID() method that uses the $aPostElement property.
		parent::__construct( $aPostImport );
	
		$this->aFilesImport = $aFilesImport;
		$this->aPostImport = $aPostImport;
		
		// Find the field ID and the element key ( for multiple export buttons )of the pressed submit ( export ) button.
		$this->sFieldID = $this->getFieldID();
		$this->aElementKey = $this->getElementKey( $aPostImport['submit'], $this->sFieldID );
			
	}
	
	private function getElementInFilesArray( $aFilesImport, $aElementKey, $sElementKey='error' ) {

		$sElementKey = strtolower( $sElementKey );
		$sFieldID = $aElementKey[ 0 ];	// or simply assigning $this->sFieldID would work as well.
		if ( ! isset( $aFilesImport[ $sElementKey ][ $sFieldID ] ) ) return 'ERROR_A: The given key does not exist.';
	
		// For single export buttons, e.g. $_FILES[__import][ $sElementKey ][import_single] 
		if ( isset( $aFilesImport[ $sElementKey ][ $sFieldID ] ) && ! is_array( $aFilesImport[ $sElementKey ][ $sFieldID ] ) )
			return $aFilesImport[ $sElementKey ][ $sFieldID ];
			
		// For multiple import buttons, e.g. $_FILES[__import][ $sElementKey ][import_multiple][2]
		if ( ! isset( $aElementKey[ 1 ] ) ) return 'ERROR_B: the sub element is not set.';
		$sKey = $aElementKey[ 1 ];		
		if ( isset( $aPostImport[ $sElementKey ][ $sFieldID ][ $sKey ] ) )
			return $aPostImport[ $sElementKey ][ $sFieldID ][ $sKey ];

		// Something wrong happened.
		return 'ERROR_C: unexpected problem occurred.';
		
	}	
		
	public function getError() {
		
		return $this->getElementInFilesArray( $this->aFilesImport, $this->aElementKey, 'error' );
		
	}
	public function getType() {
		
		return $this->getElementInFilesArray( $this->aFilesImport, $this->aElementKey, 'type' );
		
	}
	public function getImportData() {
		
		// Retrieve the uploaded file path.
		$sFilePath = $this->getElementInFilesArray( $this->aFilesImport, $this->aElementKey, 'tmp_name' );
		
		// Read the file contents.
		$vData = file_exists( $sFilePath ) ? file_get_contents( $sFilePath, true ) : false;
		
		return $vData;
		
	}
	public function formatImportData( &$vData, $sFormatType=null ) {
		
		$sFormatType = isset( $sFormatType ) ? $sFormatType : $this->getFormatType();
		switch ( strtolower( $sFormatType ) ) {
			case 'text':	// for plain text.
				return;	// do nothing
			case 'json':	// for json.
				$vData = json_decode( ( string ) $vData, true );	// the second parameter indicates to decode it as array.
				return;
			case 'array':	// for serialized PHP array.
			default:	// for anything else, 
				$vData = maybe_unserialize( trim( $vData ) );
				return;
		}		
	
	}
	public function getFormatType() {
					
		$this->sFormatType = isset( $this->sFormatType ) && $this->sFormatType 
			? $this->sFormatType
			: $this->getElement( $this->aPostImport, $this->aElementKey, 'format' );

		return $this->sFormatType;
		
	}
	
	/**
	 * Returns the specified sibling value.
	 * 
	 * @since			2.1.5
	 */
	public function getSiblingValue( $sKey ) {
		
		return $this->getElement( $this->aPostImport, $this->aElementKey, $sKey );
		
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_ExportOptions' ) ) :
/**
 * Provides methods to export option data.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_CustomSubmitFields
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AdminPageFramework_ExportOptions extends AdminPageFramework_CustomSubmitFields {

	public function __construct( $aPostExport, $sClassName ) {
		
		// Call the parent constructor.
		parent::__construct( $aPostExport );
		
		// Properties
		$this->aPostExport = $aPostExport;
		$this->sClassName = $sClassName;	// will be used in the getTransientIfSet() method.
		// $this->sPageSlug = $sPageSlug;
		// $this->sTabSlug = $sTabSlug;
		
		// Find the field ID and the element key ( for multiple export buttons )of the pressed submit ( export ) button.
		$this->sFieldID = $this->getFieldID();
		$this->aElementKey = $this->getElementKey( $aPostExport['submit'], $this->sFieldID );
		
		// Set the file name to download and the format type. Also find whether the exporting data is set in transient.
		$this->sFileName = $this->getElement( $aPostExport, $this->aElementKey, 'file_name' );
		$this->sFormatType = $this->getElement( $aPostExport, $this->aElementKey, 'format' );
		$this->bIsDataSet = $this->getElement( $aPostExport, $this->aElementKey, 'transient' );
	
	}
	
	public function getTransientIfSet( $vData ) {
		
		if ( $this->bIsDataSet ) {
			$sKey = $this->aElementKey[1];
			$sTransient = isset( $this->aElementKey[1] ) ? "{$this->sClassName}_{$this->sFieldID}_{$this->aElementKey[1]}" : "{$this->sClassName}_{$this->sFieldID}";
			$tmp = get_transient( md5( $sTransient ) );
			if ( $tmp !== false ) {
				$vData = $tmp;
				delete_transient( md5( $sTransient ) );
			}
		}
		return $vData;
	}
	
	public function getFileName() {
		return $this->sFileName;
	}
	public function getFormat() {
		return $this->sFormatType;
	}
	
	/**
	 * Returns the specified sibling value.
	 * 
	 * @since			2.1.5
	 */
	public function getSiblingValue( $sKey ) {
		
		return $this->getElement( $this->aPostExport, $this->aElementKey, $sKey );
		
	}	

	/**
	 * Performs exporting data.
	 * 
	 * @since			2.0.0
	 */ 
	public function doExport( $vData, $sFileName=null, $sFormatType=null ) {

		/* 
		 * Sample HTML elements that triggers the method.
		 * e.g.
		 * <input type="hidden" name="__export[export_sinble][file_name]" value="APF_GettingStarted_20130708.txt">
		 * <input type="hidden" name="__export[export_sinble][format]" value="json">
		 * <input id="export_and_import_export_sinble_0" 
		 *  type="submit" 
		 *  name="__export[submit][export_sinble]" 
		 *  value="Export Options">
		*/	
		$sFileName = isset( $sFileName ) ? $sFileName : $this->sFileName;
		$sFormatType = isset( $sFormatType ) ? $sFormatType : $this->sFormatType;
							
		// Do export.
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $sFileName );
		switch ( strtolower( $sFormatType ) ) {
			case 'text':	// for plain text.
				if ( is_array( $vData ) || is_object( $vData ) ) {
					$oDebug = new AdminPageFramework_Debug;
					$sData = $oDebug->getArray( $vData );
					die( $sData );
				}
				die( $vData );
			case 'json':	// for json.
				die( json_encode( ( array ) $vData ) );
			case 'array':	// for serialized PHP array.
			default:	// for anything else, 
				die( serialize( ( array ) $vData  ));
		}
	}
}
endif;

if ( ! class_exists( 'AdminPageFramework_Link_Base' ) ) :
/**
 * Provides methods for HTML link elements.
 *
 * @abstract
 * @since			2.0.0
 * @extends			AdminPageFramework_Utility
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Link
 */
abstract class AdminPageFramework_Link_Base extends AdminPageFramework_Utility {
	
	/**
	 * @internal
	 * @since			2.0.0
	 */ 
	private static $_aStructure_CallerInfo = array(
		'sPath'			=> null,
		'type'			=> null,
		'sName'			=> null,		
		'sURI'			=> null,
		'sVersion'		=> null,
		'sThemeURI'		=> null,
		'sScriptURI'		=> null,
		'sAuthorURI'		=> null,
		'sAuthor'			=> null,
		'description'	=> null,
	);	
	
	/*
	 * Methods for getting script info.
	 */ 
	
	/**
	 * Retrieves the caller script information whether it's a theme or plugin or something else.
	 * 
	 * @since			2.0.0
	 * @remark			The information can be used to embed into the footer etc.
	 * @return			array			The information of the script.
	 */	 
	protected function getCallerInfo( $sCallerPath=null ) {
		
		$aCallerInfo = self::$_aStructure_CallerInfo;
		$aCallerInfo['sPath'] = $sCallerPath;
		$aCallerInfo['type'] = $this->getCallerType( $aCallerInfo['sPath'] );

		if ( $aCallerInfo['type'] == 'unknown' ) return $aCallerInfo;
		
		if ( $aCallerInfo['type'] == 'plugin' ) 
			return $this->getScriptData( $aCallerInfo['sPath'], $aCallerInfo['type'] ) + $aCallerInfo;
			
		if ( $aCallerInfo['type'] == 'theme' ) {
			$oTheme = wp_get_theme();	// stores the theme info object
			return array(
				'sName'			=> $oTheme->Name,
				'sVersion' 		=> $oTheme->Version,
				'sThemeURI'		=> $oTheme->get( 'ThemeURI' ),
				'sURI'			=> $oTheme->get( 'ThemeURI' ),
				'sAuthorURI'		=> $oTheme->get( 'AuthorURI' ),
				'sAuthor'			=> $oTheme->get( 'Author' ),				
			) + $aCallerInfo;	
		}
	}

	/**
	 * Retrieves the library script info.
	 * 
	 * @since			2.1.1
	 */
	protected function getLibraryInfo() {
		return $this->getScriptData( __FILE__, 'library' ) + self::$_aStructure_CallerInfo;
	}
	
	/**
	 * Determines the script type.
	 * 
	 * It tries to find what kind of script this is, theme, plugin or something else from the given path.
	 * @since			2.0.0
	 * @return		string				Returns either 'theme', 'plugin', or 'unknown'
	 */ 
	protected function getCallerType( $sScriptPath ) {
		
		if ( preg_match( '/[\/\\\\]themes[\/\\\\]/', $sScriptPath, $m ) ) return 'theme';
		if ( preg_match( '/[\/\\\\]plugins[\/\\\\]/', $sScriptPath, $m ) ) return 'plugin';
		return 'unknown';	
	
	}
	protected function getCallerPath() {

		foreach( debug_backtrace() as $aDebugInfo )  {			
			if ( $aDebugInfo['file'] == __FILE__ ) continue;
			return $aDebugInfo['file'];	// return the first found item.
		}
	}	
	
	/**
	 * Sets the default footer text on the left hand side.
	 * 
	 * @since			2.1.1
	 */
	protected function setFooterInfoLeft( $aScriptInfo, &$sFooterInfoLeft ) {
		
		$sDescription = empty( $aScriptInfo['description'] ) 
			? ""
			: "&#13;{$aScriptInfo['description']}";
		$sVersion = empty( $aScriptInfo['sVersion'] )
			? ""
			: "&nbsp;{$aScriptInfo['sVersion']}";
		$sPluginInfo = empty( $aScriptInfo['sURI'] ) 
			? $aScriptInfo['sName'] 
			: "<a href='{$aScriptInfo['sURI']}' target='_blank' title='{$aScriptInfo['sName']}{$sVersion}{$sDescription}'>{$aScriptInfo['sName']}</a>";
		$sAuthorInfo = empty( $aScriptInfo['sAuthorURI'] )	
			? $aScriptInfo['sAuthor'] 
			: "<a href='{$aScriptInfo['sAuthorURI']}' target='_blank'>{$aScriptInfo['sAuthor']}</a>";
		$sAuthorInfo = empty( $aScriptInfo['sAuthor'] ) 
			? $sAuthorInfo 
			: ' by ' . $sAuthorInfo;
		$sFooterInfoLeft =  $sPluginInfo . $sAuthorInfo;
		
	}
	/**
	 * Sets the default footer text on the right hand side.
	 * 
	 * @since			2.1.1
	 */	
	protected function setFooterInfoRight( $aScriptInfo, &$sFooterInfoRight ) {
	
		$sDescription = empty( $aScriptInfo['description'] ) 
			? ""
			: "&#13;{$aScriptInfo['description']}";
		$sVersion = empty( $aScriptInfo['sVersion'] )
			? ""
			: "&nbsp;{$aScriptInfo['sVersion']}";		
		$sLibraryInfo = empty( $aScriptInfo['sURI'] ) 
			? $aScriptInfo['sName'] 
			: "<a href='{$aScriptInfo['sURI']}' target='_blank' title='{$aScriptInfo['sName']}{$sVersion}{$sDescription}'>{$aScriptInfo['sName']}</a>";	
	
		$sFooterInfoRight = $this->oMsg->__( 'powered_by' ) . '&nbsp;' 
			. $sLibraryInfo
			. ", <a href='http://wordpress.org' target='_blank' title='WordPress {$GLOBALS['wp_version']}'>WordPress</a>";
		
	}
}
endif;

if ( ! class_exists( 'AdminPageFramework_Link_PostType' ) ) :
/**
 * Provides methods for HTML link elements for custom post types.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_Utility
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Link
 */
class AdminPageFramework_Link_PostType extends AdminPageFramework_Link_Base {
	
	/**
	 * Stores the information to embed into the page footer.
	 * @since			2.0.0
	 * @remark			This is accessed from the AdminPageFramework_PostType class.
	 */ 
	public $aFooterInfo = array(
		'sLeft' => '',
		'sRight' => '',
	);
	
	public function __construct( $sPostTypeSlug, $sCallerPath=null, $oMsg=null ) {
		
		if ( ! is_admin() ) return;
		
		$this->sPostTypeSlug = $sPostTypeSlug;
		$this->sCallerPath = file_exists( $sCallerPath ) ? $sCallerPath : $this->getCallerPath();
		$this->aScriptInfo = $this->getCallerInfo( $this->sCallerPath ); 
		$this->aLibraryInfo = $this->getLibraryInfo();
		
		$this->oMsg = $oMsg;
		
		$this->sSettingPageLinkTitle = $this->oMsg->__( 'manage' );
		
		// Add script info into the footer 
		add_filter( 'update_footer', array( $this, 'addInfoInFooterRight' ), 11 );
		add_filter( 'admin_footer_text' , array( $this, 'addInfoInFooterLeft' ) );	
		$this->setFooterInfoLeft( $this->aScriptInfo, $this->aFooterInfo['sLeft'] );
		$this->setFooterInfoRight( $this->aLibraryInfo, $this->aFooterInfo['sRight'] );
		
		// For the plugin listing page
		if ( $this->aScriptInfo['type'] == 'plugin' )
			add_filter( 
				'plugin_action_links_' . plugin_basename( $this->aScriptInfo['sPath'] ),
				array( $this, 'addSettingsLinkInPluginListingPage' ), 
				20 	// set a lower priority so that the link will be embedded at the beginning ( the most left hand side ).
			);	
		
		// For post type posts listing table page ( edit.php )
		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->sPostTypeSlug )
			add_action( 'get_edit_post_link', array( $this, 'addPostTypeQueryInEditPostLink' ), 10, 3 );
		
	}
	
	/*
	 * Callback methods
	 */ 
	/**
	 * Adds the <em>post_type</em> query key and value in the link url.
	 * 
	 * This is used to make it easier to detect if the linked page belongs to the post type created with this class.
	 * So it can be used to embed footer links.
	 * 
	 * @since			2.0.0
	 * @remark			e.g. http://.../wp-admin/post.php?post=180&action=edit -> http://.../wp-admin/post.php?post=180&action=edit&post_type=[...]
	 * @remark			A callback for the <em>get_edit_post_link</em> hook.
	 */	 
	public function addPostTypeQueryInEditPostLink( $sURL, $iPostID=null, $sContext=null ) {
		return add_query_arg( array( 'post' => $iPostID, 'action' => 'edit', 'post_type' => $this->sPostTypeSlug ), $sURL );	
	}	
	public function addSettingsLinkInPluginListingPage( $aLinks ) {
		
		// http://.../wp-admin/edit.php?post_type=[...]
		array_unshift(	
			$aLinks,
			"<a href='edit.php?post_type={$this->sPostTypeSlug}'>" . $this->sSettingPageLinkTitle . "</a>"
		); 
		return $aLinks;		
		
	}
	
	/**
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the filter hook, <em>admin_footer_text</em>.
	 */ 
	public function addInfoInFooterLeft( $sLinkHTML='' ) {
		
		if ( ! isset( $_GET['post_type'] ) ||  $_GET['post_type'] != $this->sPostTypeSlug )
			return $sLinkHTML;	// $sLinkHTML is given by the hook.

		if ( empty( $this->aScriptInfo['sName'] ) ) return $sLinkHTML;
					
		return $this->aFooterInfo['sLeft'];
		
	}
	public function addInfoInFooterRight( $sLinkHTML='' ) {

		if ( ! isset( $_GET['post_type'] ) ||  $_GET['post_type'] != $this->sPostTypeSlug )
			return $sLinkHTML;	// $sLinkHTML is given by the hook.
			
		return $this->aFooterInfo['sRight'];		
			
	}
}
endif;
 
if ( ! class_exists( 'AdminPageFramework_Link' ) ) :
/**
 * Provides methods for HTML link elements for admin pages created by the framework, except the pages of custom post types.
 *
 * Embeds links in the footer and plugin's listing table etc.
 * 
 * @since			2.0.0
 * @extends			AdminPageFramework_Link_Base
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Link
 */
class AdminPageFramework_Link extends AdminPageFramework_Link_Base {
	
	/**
	 * Stores the caller script path.
	 * @since			2.0.0
	 */ 
	private $sCallerPath;
	
	/**
	 * The property object, commonly shared.
	 * @since			2.0.0
	 */ 
	private $oProps;
	
	public function __construct( &$oProps, $sCallerPath=null, $oMsg=null ) {
		
		if ( ! is_admin() ) return;
		
		$this->oProps = $oProps;
		$this->sCallerPath = file_exists( $sCallerPath ) ? $sCallerPath : $this->getCallerPath();
		$this->oProps->aScriptInfo = $this->getCallerInfo( $this->sCallerPath ); 
		$this->oProps->aLibraryInfo = $this->getLibraryInfo();
		$this->oMsg = $oMsg;
		
		// Add script info into the footer 
		add_filter( 'update_footer', array( $this, 'addInfoInFooterRight' ), 11 );
		add_filter( 'admin_footer_text' , array( $this, 'addInfoInFooterLeft' ) );	
		$this->setFooterInfoLeft( $this->oProps->aScriptInfo, $this->oProps->aFooterInfo['sLeft'] );
		$this->setFooterInfoRight( $this->oProps->aLibraryInfo, $this->oProps->aFooterInfo['sRight'] );
	
		if ( $this->oProps->aScriptInfo['type'] == 'plugin' )
			add_filter( 'plugin_action_links_' . plugin_basename( $this->oProps->aScriptInfo['sPath'] ) , array( $this, 'addSettingsLinkInPluginListingPage' ) );

	}

	
	/**	
	 * 
	 * @since			2.0.0
	 * @since			2.1.4			Changed to be static since it is used from multiple classes.
	 * @remark			The scope is public because this is accessed from an extended class.
	 */ 
	public static $_aStructure_SubMenuLink = array(		
		'title' => null,
		'href' => null,
		'sCapability' => null,
		'order' => null,
		'type' => 'link',
		'fShowPageHeadingTab' => true,
		'fShowInMenu' => true,
	);

	public function addSubMenuLink( $sMenuTitle, $sURL, $sCapability=null, $nOrder=null, $bShowPageHeadingTab=true, $bShowInMenu=true ) {
		
		$iCount = count( $this->oProps->aPages );
		$this->oProps->aPages[ $sURL ] = array(  
			'title'		=> $sMenuTitle,
			'title'		=> $sMenuTitle,	// used for the page heading tabs.
			'href'			=> $sURL,
			'type'			=> 'link',	// this is used to compare with the 'page' type.
			'sCapability'		=> isset( $sCapability ) ? $sCapability : $this->oProps->sCapability,
			'order'			=> is_numeric( $nOrder ) ? $nOrder : $iCount + 10,
			'fShowPageHeadingTab'	=> $bShowPageHeadingTab,
			'fShowInMenu'		=> $bShowInMenu,
		);	
			
	}
			
	/*
	 * Methods for embedding links 
	 */ 	
	public function addLinkToPluginDescription( $linkss ) {
		
		if ( !is_array( $linkss ) )
			$this->oProps->aPluginDescriptionLinks[] = $linkss;
		else
			$this->oProps->aPluginDescriptionLinks = array_merge( $this->oProps->aPluginDescriptionLinks , $linkss );
	
		add_filter( 'plugin_row_meta', array( $this, 'addLinkToPluginDescription_Callback' ), 10, 2 );

	}
	public function addLinkToPluginTitle( $linkss ) {
		
		if ( !is_array( $linkss ) )
			$this->oProps->aPluginTitleLinks[] = $linkss;
		else
			$this->oProps->aPluginTitleLinks = array_merge( $this->oProps->aPluginTitleLinks, $linkss );
		
		add_filter( 'plugin_action_links_' . plugin_basename( $this->oProps->aScriptInfo['sPath'] ), array( $this, 'AddLinkToPluginTitle_Callback' ) );

	}
	
	/*
	 * Callback methods
	 */ 
	
	/**
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the filter hook, <em>admin_footer_text</em>.
	 */ 
	public function addInfoInFooterLeft( $sLinkHTML='' ) {

		if ( ! isset( $_GET['page'] ) || ! $this->oProps->isPageAdded( $_GET['page'] )  ) 
			return $sLinkHTML;	// $sLinkHTML is given by the hook.
		
		if ( empty( $this->oProps->aScriptInfo['sName'] ) ) return $sLinkHTML;
		
		return $this->oProps->aFooterInfo['sLeft'];

	}
	public function addInfoInFooterRight( $sLinkHTML='' ) {

		if ( ! isset( $_GET['page'] ) || ! $this->oProps->isPageAdded( $_GET['page'] )  ) 
			return $sLinkHTML;	// $sLinkTHML is given by the hook.
			
		return $this->oProps->aFooterInfo['sRight'];
			
	}
	
	public function addSettingsLinkInPluginListingPage( $aLinks ) {
		
		// For a custom root slug,
		$sLinkURL = preg_match( '/^.+\.php/', $this->oProps->aRootMenu['page_slug'] ) 
			? add_query_arg( array( 'page' => $this->oProps->sDefaultPageSlug ), admin_url( $this->oProps->aRootMenu['page_slug'] ) )
			: "admin.php?page={$this->oProps->sDefaultPageSlug}";
		
		array_unshift(	
			$aLinks,
			'<a href="' . $sLinkURL . '">' . $this->oMsg->__( 'settings' ) . '</a>'
		); 
		return $aLinks;
		
	}	
	
	public function addLinkToPluginDescription_Callback( $aLinks, $sFile ) {

		if ( $sFile != plugin_basename( $this->oProps->aScriptInfo['sPath'] ) ) return $aLinks;
		
		// Backward compatibility sanitization.
		$aAddingLinks = array();
		foreach( $this->oProps->aPluginDescriptionLinks as $linksHTML )
			if ( is_array( $linksHTML ) )	// should not be an array
				$aAddingLinks = array_merge( $linksHTML, $aAddingLinks );
			else
				$aAddingLinks[] = ( string ) $linksHTML;
		
		return array_merge( $aLinks, $aAddingLinks );
		
	}			
	public function addLinkToPluginTitle_Callback( $aLinks ) {

		// Backward compatibility sanitization.
		$aAddingLinks = array();
		foreach( $this->oProps->aPluginTitleLinks as $linksHTML )
			if ( is_array( $linksHTML ) )	// should not be an array
				$aAddingLinks = array_merge( $linksHTML, $aAddingLinks );
			else
				$aAddingLinks[] = ( string ) $linksHTML;
		
		return array_merge( $aLinks, $aAddingLinks );
		
	}		
}
endif;

if ( ! class_exists( 'AdminPageFramework_PageLoadInfo_Base' ) ) :
/**
 * Collects data of page loads in admin pages.
 *
 * @since			2.1.7
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
abstract class AdminPageFramework_PageLoadInfo_Base {
	
	function __construct( $oProps, $oMsg ) {
		
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			
			$this->oProps = $oProps;
			$this->oMsg = $oMsg;
			$this->nInitialMemoryUsage = memory_get_usage();
			add_action( 'admin_menu', array( $this, 'replyToSetPageLoadInfoInFooter' ), 999 );	// must be loaded after the sub pages are registered
						
		}

	}
	
	/**
	 * @remark			Should be overridden in an extended class.
	 */
	public function replyToSetPageLoadInfoInFooter() {}
		
	/**
	 * Display gathered information.
	 *
	 * @access			public
	 */
	public function replyToGetPageLoadInfo( $sFooterHTML ) {
		
		// Get values we're displaying
		$nSeconds 				= timer_stop(0);
		$nQueryCount 			= get_num_queries();
		$memory_usage 			= round( $this->convert_bytes_to_hr( memory_get_usage() ), 2 );
		$memory_peak_usage 		= round( $this->convert_bytes_to_hr( memory_get_peak_usage() ), 2 );
		$memory_limit 			= round( $this->convert_bytes_to_hr( $this->let_to_num( WP_MEMORY_LIMIT ) ), 2 );
		$sInitialMemoryUsage	= round( $this->convert_bytes_to_hr( $this->nInitialMemoryUsage ), 2 );
				
		$sOutput = 
			"<div id='admin-page-framework-page-load-stats'>"
				. "<ul>"
					. "<li>" . sprintf( $this->oMsg->__( 'queries_in_seconds' ), $nQueryCount, $nSeconds ) . "</li>"
					. "<li>" . sprintf( $this->oMsg->__( 'out_of_x_memory_used' ), $memory_usage, $memory_limit, round( ( $memory_usage / $memory_limit ), 2 ) * 100 . '%' ) . "</li>"
					. "<li>" . sprintf( $this->oMsg->__( 'peak_memory_usage' ), $memory_peak_usage ) . "</li>"
					. "<li>" . sprintf( $this->oMsg->__( 'initial_memory_usage' ), $sInitialMemoryUsage ) . "</li>"
				. "</ul>"
			. "</div>";
		return $sFooterHTML . $sOutput;
		
	}

	/**
	 * let_to_num function.
	 *
	 * This function transforms the php.ini notation for numbers (like '2M') to an integer
	 *
	 * @access public
	 * @param $size
	 * @return int
	 * @author			Mike Jolley
	 * @see				http://mikejolley.com/projects/wp-page-load-stats/
	 */
	function let_to_num( $size ) {
		$l 		= substr( $size, -1 );
		$ret 	= substr( $size, 0, -1 );
		switch( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
			case 'T':
				$ret *= 1024;
			case 'G':
				$ret *= 1024;
			case 'M':
				$ret *= 1024;
			case 'K':
				$ret *= 1024;
		}
		return $ret;
	}

	/**
	 * convert_bytes_to_hr function.
	 *
	 * @access public
	 * @param mixed $bytes
	 * @author			Mike Jolley
	 * @see				http://mikejolley.com/projects/wp-page-load-stats/
	 */
	function convert_bytes_to_hr( $bytes ) {
		$units = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
		$log = log( $bytes, 1024 );
		$power = ( int ) $log;
		$size = pow( 1024, $log - $power );
		return $size . $units[ $power ];
	}

}
endif;

if ( ! class_exists( 'AdminPageFramework_PageLoadInfo_Page' ) ) :
/**
 * Collects data of page loads of the added pages.
 *
 * @since			2.1.7
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
class AdminPageFramework_PageLoadInfo_Page extends AdminPageFramework_PageLoadInfo_Base {
	
	private static $_oInstance;
	
	/**
	 * Ensures that only one instance of this class object exists. ( no multiple instances of this object ) 
	 * 
	 * @remark			This class should be instantiated via this method.
	 */
	public static function instantiate( $oProps, $oMsg ) {
		
		if ( ! isset( self::$_oInstance ) && ! ( self::$_oInstance instanceof AdminPageFramework_PageLoadInfo_Page ) ) 
			self::$_oInstance = new AdminPageFramework_PageLoadInfo_Page( $oProps, $oMsg );
		return self::$_oInstance;
		
	}		
	
	/**
	 * Sets the hook if the current page is one of the framework's added pages.
	 */ 
	public function replyToSetPageLoadInfoInFooter() {
		
		// For added pages
		$sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
		if ( $this->oProps->isPageAdded( $sCurrentPageSlug ) ) 
			add_filter( 'update_footer', array( $this, 'replyToGetPageLoadInfo' ), 999 );
	
	}		
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_PageLoadInfo_PostType' ) ) :
/**
 * Collects data of page loads of the added post type pages.
 *
 * @since			2.1.7
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
class AdminPageFramework_PageLoadInfo_PostType extends AdminPageFramework_PageLoadInfo_Base {
	
	private static $_oInstance;
	
	/**
	 * Ensures that only one instance of this class object exists. ( no multiple instances of this object ) 
	 * 
	 * @remark			This class should be instantiated via this method.
	 */
	public static function instantiate( $oProps, $oMsg ) {
		
		if ( ! isset( self::$_oInstance ) && ! ( self::$_oInstance instanceof AdminPageFramework_PageLoadInfo_PostType ) ) 
			self::$_oInstance = new AdminPageFramework_PageLoadInfo_PostType( $oProps, $oMsg );
		return self::$_oInstance;
		
	}	

	/**
	 * Sets the hook if the current page is one of the framework's added post type pages.
	 */ 
	public function replyToSetPageLoadInfoInFooter() {

		// Some users sets $_GET['post_type'] element even in regular admin pages. In that case, do not load the style to avoid duplicates.
		if ( isset( $_GET['page'] ) && $_GET['page'] ) return;
	
		// For post type pages
		if ( isset( $_GET['post_type'], $this->oProps->sPostType ) && $_GET['post_type'] == $this->oProps->sPostType )
			add_filter( 'update_footer', array( $this, 'replyToGetPageLoadInfo' ), 999 );
		
	}	
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldTypeDefinition_Base' ) ) :
/**
 * The base class of field type classes that define input field types.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
abstract class AdminPageFramework_InputFieldTypeDefinition_Base extends AdminPageFramework_Utility {
	
	protected static $_aDefaultKeys = array(
		'vValue'				=> null,				// ( array or string ) this suppress the default key value. This is useful to display the value saved in a custom place other than the framework automatically saves.
		'default'				=> null,				// ( array or string )
		'repeatable'			=> false,
		'class_attribute'		=> '',					// ( array or string ) the class attribute of the input field. Do not set an empty value here, but null because the submit field type uses own default value.
		'label'				=> '',					// ( array or string ) labels for some input fields. Do not set null here because it is casted as string in the field output methods, which creates an element of empty string so that it can be iterated with foreach().
		'delimiter'			=> '',
		'vDisable'				=> false,				// ( array or boolean ) This value indicates whether the set field is disabled or not. 
		'vReadOnly'				=> false,				// ( array or boolean ) sets the readonly attribute to text and textarea input fields.
		'vBeforeInputTag'		=> '',
		'vAfterInputTag'		=> '',				
		'labelMinWidth'		=> 140,
		
		// Mandatory keys.
		'field_id' => null,		
		
		// For the meta box class - it does not require the following keys; these are just to help to avoid undefined index warnings.
		'page_slug' => null,
		'section_id' => null,
		'sBeforeField' => null,
		'sAfterField' => null,	
	);	
	
	protected $oMsg;
	
	function __construct( $sClassName, $sFieldTypeSlug, $oMsg=null, $bAutoRegister=true ) {
			
		$this->sFieldTypeSlug = $sFieldTypeSlug;
		$this->sClassName = $sClassName;
		$this->oMsg	= $oMsg;
		
		// This automatically registers the field type. The build-in ones will be registered manually so it will be skipped.
		if ( $bAutoRegister )
			add_filter( "field_types_{$sClassName}", array( $this, 'replyToRegisterInputFieldType' ) );
	
	}	
	
	/**
	 * Registers the field type.
	 * 
	 * A callback function for the field_types_{$sClassName} filter.
	 * 
	 */
	public function replyToRegisterInputFieldType( $aFieldDefinitions ) {
		
		$aFieldDefinitions[ $this->sFieldTypeSlug ] = $this->getDefinitionArray();
		return $aFieldDefinitions;
		
	}
	
	/**
	 * Returns the field type definition array.
	 * 
	 * @remark			The scope is public since AdminPageFramework_FieldType class allows the user to use this method.
	 * @since			2.1.5
	 */
	public function getDefinitionArray() {
		
		return array(
			'hfRenderField' => array( $this, "replyToGetInputField" ),
			'hfGetScripts' => array( $this, "replyToGetInputScripts" ),
			'hfGetStyles' => array( $this, "replyToGetInputStyles" ),
			'hfGetIEStyles' => array( $this, "replyToGetInputIEStyles" ),
			'hfFieldLoader' => array( $this, "replyToFieldLoader" ),
			'aEnqueueScripts' => $this->getEnqueuingScripts(),	// urls of the scripts
			'aEnqueueStyles' => $this->getEnqueuingStyles(),	// urls of the styles
			'aDefaultKeys' => $this->getDefaultKeys() + self::$_aDefaultKeys, 
		);
		
	}
	
	/*
	 * These methods should be overridden in the extended class.
	 */
	public function replytToGetInputField() { return ''; }	// should return the field output
	public function replyToGetInputScripts() { return ''; }	// should return the script
	public function replyToGetInputIEStyles() { return ''; }	// should return the style for IE
	public function replyToGetInputStyles() { return ''; }	// should return the style
	public function replyToFieldLoader() {}	// do stuff that should be done when the field type is loaded for the first time.
	protected function getEnqueuingScripts() { return array(); }	// should return an array holding the urls of enqueuing items
	protected function getEnqueuingStyles() { return array(); }	// should return an array holding the urls of enqueuing items
	protected function getDefaultKeys() { return array(); }
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_FieldType' ) ) :
/**
 * The base class for the users to create their custom field types.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
abstract class AdminPageFramework_FieldType extends AdminPageFramework_InputFieldTypeDefinition_Base {}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_default' ) ) :
/**
 * Defines the default field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_default extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			// 'size'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * This one is triggered when the called field type is unknown. This does not insert the input tag but just renders the value stored in the $vValue variable.
	 * 
	 * @since			2.1.5				
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
				
		foreach( ( array ) $vValue as $sKey => $sValue ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. ( ( $sLabel = $this->getCorrespondingArrayValue( $aField['label'], $sKey, $_aDefaultKeys['label'] ) ) 
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>{$sLabel}</span>" 
								: "" 
							)
							. "<div class='admin-page-framework-input-container'>"
								. $sValue
							. "</div>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"		
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-default' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}

}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_text' ) ) :
/**
 * Defines the text field type.
 * 
 * Also the field types of 'password', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'url', and 'week' are defeined.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_text extends AdminPageFramework_InputFieldTypeDefinition_Base {

	/**
	 * Registers the field type.
	 * 
	 * A callback function for the field_types_{$sClassName} filter.
	 * 
	 * @remark			Since there are the other type slugs that are shared with the text field type, register them as well. 
	 */
	public function replyToRegisterInputFieldType( $aFieldDefinitions ) {
		
		foreach ( array( 'text', 'password', 'date', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'url', 'week', ) as $sTextTypeSlug )
			$aFieldDefinitions[ $sTextTypeSlug ] = $this->getDefinitionArray();

		return $aFieldDefinitions;
		
	}
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'size'					=> 30,
			'vMaxLength'			=> 400,
		);	
	}
	/**
	 * Returns the output of the text input field.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];
		$bMultiple = is_array( $aFields );
		
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, '' ) 
							. ( $sLabel && ! $aField['repeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
								: "" 
							)
							. "<input id='{$sTagID}_{$sKey}' "
								. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, '' ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, 30 ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
								. "type='{$aField['type']}' "	// text, password, etc.
								. "name=" . ( is_array( $aFields ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
								. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, null ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, '' )
						. "</label>"
					. "</div>"
				. "</div>"		
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, '', true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				)
			;
				
		return "<div class='admin-page-framework-field-text' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";

	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_number' ) ) :
/**
 * Defines the number, and range field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_number extends AdminPageFramework_InputFieldTypeDefinition_Base {

	/**
	 * Registers the field type.
	 * 
	 * A callback function for the field_types_{$sClassName} filter.
	 * 
	 * @remark			Since there are the other type slugs that are shared with the text field type, register them as well. 
	 */
	public function replyToRegisterInputFieldType( $aFieldDefinitions ) {
		
		foreach ( array( 'number', 'range' ) as $sTextTypeSlug ) 
			$aFieldDefinitions[ $sTextTypeSlug ] = $this->getDefinitionArray();
		return $aFieldDefinitions;
		
	}
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vMin'				=> null,
			'vMax'				=> null,
			'vStep'				=> null,
			'size'				=> 30,
			'vMaxLength'		=> 400,
		);	
	}
	
	/**
	 * Returns the output of the number input field.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {
		
		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];
			
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}' >"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, '' ) 
							. ( $sLabel && ! $aField['repeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
								: ""
							)
							. "<input id='{$sTagID}_{$sKey}' "
								. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, '' ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, 30 ) . "' "
								. "type='{$aField['type']}' "
								. "name=" . ( is_array( $aFields ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
								. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, null ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
								. "min='" . $this->getCorrespondingArrayValue( $aField['vMin'], $sKey, $_aDefaultKeys['vMin'] ) . "' "
								. "max='" . $this->getCorrespondingArrayValue( $aField['vMax'], $sKey, $_aDefaultKeys['vMax'] ) . "' "
								. "step='" . $this->getCorrespondingArrayValue( $aField['vStep'], $sKey, $_aDefaultKeys['vStep'] ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, '' )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, '', true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);				
					
		return "<div class='admin-page-framework-field-number' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";		
		
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_textarea' ) ) :
/**
 * Defines the textarea field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_textarea extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'rows'					=> 4,
			'cols'					=> 80,
			'vRich'					=> false,
			'vMaxLength'			=> 400,
		);	
	}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 
		"	/* Rich Text Editor */
			.admin-page-framework-field-textarea .wp-core-ui.wp-editor-wrap {
				margin-bottom: 0.5em;
			}		
		" . PHP_EOL;		
	}	
		
	/**
	 * Returns the output of the textarea input field.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];			
		$bSingle = ! is_array( $aFields );
		
		foreach( ( array ) $aFields as $sKey => $sLabel ) {
			
			$aRichEditorSettings = $bSingle
				? $aField['vRich']
				: $this->getCorrespondingArrayValue( $aField['vRich'], $sKey, null );
				
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}' >"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, '' ) 
							. ( $sLabel && ! $aField['repeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
								: "" 
							)
							. ( ! empty( $aRichEditorSettings ) && version_compare( $GLOBALS['wp_version'], '3.3', '>=' ) && function_exists( 'wp_editor' )
								? wp_editor( 
									$this->getCorrespondingArrayValue( $vValue, $sKey, null ), 
									"{$sTagID}_{$sKey}",  
									$this->uniteArrays( 
										( array ) $aRichEditorSettings,
										array(
											'wpautop' => true, // use wpautop?
											'media_buttons' => true, // show insert/upload button(s)
											'textarea_name' => is_array( $aFields ) ? "{$sFieldName}[{$sKey}]" : $sFieldName , // set the textarea name to something different, square brackets [] can be used here
											'textarea_rows' => $this->getCorrespondingArrayValue( $aField['rows'], $sKey, $_aDefaultKeys['rows'] ),
											'tabindex' => '',
											'tabfocus_elements' => ':prev,:next', // the previous and next element ID to move the focus to when pressing the Tab key in TinyMCE
											'editor_css' => '', // intended for extra styles for both visual and Text editors buttons, needs to include the <style> tags, can use "scoped".
											'editor_class' => $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, '' ), // add extra class(es) to the editor textarea
											'teeny' => false, // output the minimal editor config used in Press This
											'dfw' => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
											'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
											'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()													
										)
									)
								) . $this->getScriptForRichEditor( "{$sTagID}_{$sKey}" )
								: "<textarea id='{$sTagID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, '' ) . "' "
									. "rows='" . $this->getCorrespondingArrayValue( $aField['rows'], $sKey, $_aDefaultKeys['rows'] ) . "' "
									. "cols='" . $this->getCorrespondingArrayValue( $aField['cols'], $sKey, $_aDefaultKeys['cols'] ) . "' "
									. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
									. "type='{$aField['type']}' "
									. "name=" . ( is_array( $aFields ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
									. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
									. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
								. ">"
									. $this->getCorrespondingArrayValue( $vValue, $sKey, null )
								. "</textarea>"
							)
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, '' )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, '', true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		}
		
		return "<div class='admin-page-framework-field-textarea' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";		

	}	
		/**
		 * A helper function for the above getTextAreaField() method.
		 * 
		 * This adds a script that forces the rich editor element to be inside the field table cell.
		 * 
		 * @since			2.1.2
		 * @since			2.1.5			Moved from AdminPageFramework_InputField.
		 */	
		private function getScriptForRichEditor( $sIDSelector ) {

			// id: wp-sample_rich_textarea_0-wrap
			return "<script type='text/javascript'>
				jQuery( '#wp-{$sIDSelector}-wrap' ).hide();
				jQuery( document ).ready( function() {
					jQuery( '#wp-{$sIDSelector}-wrap' ).appendTo( '#field-{$sIDSelector}' );
					jQuery( '#wp-{$sIDSelector}-wrap' ).show();
				})
			</script>";		
			
		}	
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_color' ) ) :
/**
 * Defines the color field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_color extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'size'					=> 10,
			'vMaxLength'			=> 400,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 * 
	 * Loads necessary files of the color field type.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AdminPageFramework_MetaBox. Changed the name from enqueueColorFieldScript().
	 * @see				http://www.sitepoint.com/upgrading-to-the-new-wordpress-color-picker/
	 */ 
	public function replyToFieldLoader() {
		
		// If the WordPress version is greater than or equal to 3.5, then load the new WordPress color picker.
		if ( version_compare( $GLOBALS['wp_version'], '3.5', '>=' ) ) {
			//Both the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
		}
		//If the WordPress version is less than 3.5 load the older farbtasic color picker.
		else {
			//As with wp-color-picker the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_script( 'farbtastic' );
		}	
		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 
		"/* Color Picker */
			.repeatable .colorpicker {
				display: inline;
			}
			.admin-page-framework-field-color .wp-picker-container {
				vertical-align: middle;
			}
			.admin-page-framework-field-color .ui-widget-content {
				border: none;
				background: none;
				color: transparent;
			}
			.admin-page-framework-field-color .ui-slider-vertical {
				width: inherit;
				height: auto;
				margin-top: -11px;
			}	
			" . PHP_EOL;		
	}	
	
	/**
	 * Returns the color picker JavaScript script loaded in the head tag of the created admin pages.
	 * @since			2.0.0
	 * @since			2.1.3			Changed to define a global function literal that registers the given input field as a color picker.
	 * @since			2.1.5			Changed the name from getColorPickerScript().
	 * @var				string
	 * @remark			It is accessed from the main class and meta box class.
	 * @remark			This is made to be a method rather than a property because in the future a variable may need to be used in the script code like the above image selector script.
	 * @access			public	
	 * @internal
	 * @return			string			The image selector script.
	 */ 
	public function replyToGetInputScripts() {
		return "
			registerAPFColorPickerField = function( sInputID ) {
				'use strict';
				// This if statement checks if the color picker element exists within jQuery UI
				// If it does exist then we initialize the WordPress color picker on our text input field
				if( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function' ){
					var myColorPickerOptions = {
						defaultColor: false,	// you can declare a default color here, or in the data-default-color attribute on the input				
						change: function(event, ui){},	// a callback to fire whenever the color changes to a valid color. reference : http://automattic.github.io/Iris/			
						clear: function() {},	// a callback to fire when the input is emptied or an invalid color
						hide: true,	// hide the color picker controls on load
						palettes: true	// show a group of common colors beneath the square or, supply an array of colors to customize further
					};			
					jQuery( '#' + sInputID ).wpColorPicker( myColorPickerOptions );
				}
				else {
					// We use farbtastic if the WordPress color picker widget doesn't exist
					jQuery( '#color_' + sInputID ).farbtastic( '#' + sInputID );
				}
			}
		";		
	}	
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];
	
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];		
	
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}'>"					
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. ( $sLabel && ! $aField['repeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
								: "" 
							)
							. "<input id='{$sTagID}_{$sKey}' "
								. "class='input_color " . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
								. "type='text' "	// text
								. "name=" . ( is_array( $aFields ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
								. "value='" . ( $this->getCorrespondingArrayValue( $vValue, $sKey, 'transparent' ) ) . "' "
								. "color='" . ( $this->getCorrespondingArrayValue( $vValue, $sKey, 'transparent' ) ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
						. "<div class='colorpicker' id='color_{$sTagID}_{$sKey}' rel='{$sTagID}_{$sKey}'></div>"	// this div element with this class selector becomes a farbtastic color picker. ( below 3.4.x )
						. $this->getColorPickerEnablerScript( "{$sTagID}_{$sKey}" )
					. "</div>"
				. "</div>"	// admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-color' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";	
		
	}
		/**
		 * A helper function for the above getColorField() method to add a script to enable the color picker.
		 */
		private function getColorPickerEnablerScript( $sInputID ) {
			return
				"<script type='text/javascript' class='color-picker-enabler-script'>
					jQuery( document ).ready( function(){
						registerAPFColorPickerField( '{$sInputID}' );
					});
				</script>";
		}	

	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_image' ) ) :
/**
 * Defines the image field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_image extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(			
			'attributes_to_capture'					=> array(),	// ( array ) This is for the image and media field type. The attributes to save besides URL. e.g. ( for the image field type ) array( 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', 'link' ).
			'size'									=> 60,
			'vMaxLength'							=> 400,
			'vImagePreview'							=> true,	// ( array or boolean )	This is for the image field type. For array, each element should contain a boolean value ( true/false ).
			'sTickBoxTitle' 						=> '',		// ( string ) This is for the image field type.
			'sLabelUseThis' 						=> '',		// ( string ) This is for the image field type.			
			'allow_external_source' 					=> true,	// ( boolean ) Indicates whether the media library box has the From URL tab.
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
		$this->enqueueMediaUploader();	
	}	
	/**
	 * Enqueues scripts and styles for the media uploader.
	 * 
	 * @remark			Used by the image and media field types.
	 * @since			2.1.5
	 */
	protected function enqueueMediaUploader() {
		
		// add_filter( 'gettext', array( $this, 'replyToReplacingThickBoxText' ) , 1, 2 );
		add_filter( 'media_upload_tabs', array( $this, 'replyToRemovingMediaLibraryTab' ) );
		
		wp_enqueue_script( 'jquery' );			
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
	
		if ( function_exists( 'wp_enqueue_media' ) ) 	// means the WordPress version is 3.5 or above
			wp_enqueue_media();	
		else		
			wp_enqueue_script( 'media-upload' );
			
	}
		/**
		 * Removes the From URL tab from the media uploader.
		 * 
		 * since			2.1.3
		 * since			2.1.5			Moved from AdminPageFramework_Setting. Changed the name from removeMediaLibraryTab() to replyToRemovingMediaLibraryTab().
		 * @remark			A callback for the <em>media_upload_tabs</em> hook.	
		 */
		public function replyToRemovingMediaLibraryTab( $aTabs ) {
			
			if ( ! isset( $_REQUEST['enable_external_source'] ) ) return $aTabs;
			
			if ( ! $_REQUEST['enable_external_source'] )
				unset( $aTabs['type_url'] );	// removes the From URL tab in the thick box.
			
			return $aTabs;
			
		}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {		
		return $this->getScript_CustomMediaUploaderObject()	. PHP_EOL	
			. $this->getScript_ImageSelector( 
				"admin_page_framework", 
				$this->oMsg->__( 'upload_image' ),
				$this->oMsg->__( 'use_this_image' )
		);
	}
		/**
		 * Returns the JavaScript script that creates a custom media uploader object.
		 * 
		 * @remark			Used by the image and media field types.
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AdminPageFramework_Properties_Base.
		 */
		protected function getScript_CustomMediaUploaderObject() {
			
			 $bLoaded = isset( $GLOBALS['aAdminPageFramework']['fIsLoadedCustomMediaUploaderObject'] )
				? $GLOBALS['aAdminPageFramework']['fIsLoadedCustomMediaUploaderObject'] : false;
			
			if( ! function_exists( 'wp_enqueue_media' ) || $bLoaded )	// means the WordPress version is 3.4.x or below
				return "";
			
			$GLOBALS['aAdminPageFramework']['fIsLoadedCustomMediaUploaderObject'] = true;
			
			// Global function literal
			return "
				getAPFCustomMediaUploaderSelectObject = function() {
					return wp.media.view.MediaFrame.Select.extend({

						initialize: function() {
							wp.media.view.MediaFrame.prototype.initialize.apply( this, arguments );

							_.defaults( this.options, {
								multiple:  true,
								editing:   false,
								state:    'insert'
							});

							this.createSelection();
							this.createStates();
							this.bindHandlers();
							this.createIframeStates();
						},

						createStates: function() {
							var options = this.options;

							// Add the default states.
							this.states.add([
								// Main states.
								new wp.media.controller.Library({
									id:         'insert',
									title:      'Insert Media',
									priority:   20,
									toolbar:    'main-insert',
									filterable: 'image',
									library:    wp.media.query( options.library ),
									multiple:   options.multiple ? 'reset' : false,
									editable:   true,

									// If the user isn't allowed to edit fields,
									// can they still edit it locally?
									allowLocalEdits: true,

									// Show the attachment display settings.
									displaySettings: true,
									// Update user settings when users adjust the
									// attachment display settings.
									displayUserSettings: true
								}),

								// Embed states.
								new wp.media.controller.Embed(),
							]);


							if ( wp.media.view.settings.post.featuredImageId ) {
								this.states.add( new wp.media.controller.FeaturedImage() );
							}
						},

						bindHandlers: function() {
							// from Select
							this.on( 'router:create:browse', this.createRouter, this );
							this.on( 'router:render:browse', this.browseRouter, this );
							this.on( 'content:create:browse', this.browseContent, this );
							this.on( 'content:render:upload', this.uploadContent, this );
							this.on( 'toolbar:create:select', this.createSelectToolbar, this );
							//

							this.on( 'menu:create:gallery', this.createMenu, this );
							this.on( 'toolbar:create:main-insert', this.createToolbar, this );
							this.on( 'toolbar:create:main-gallery', this.createToolbar, this );
							this.on( 'toolbar:create:featured-image', this.featuredImageToolbar, this );
							this.on( 'toolbar:create:main-embed', this.mainEmbedToolbar, this );

							var handlers = {
									menu: {
										'default': 'mainMenu'
									},

									content: {
										'embed':          'embedContent',
										'edit-selection': 'editSelectionContent'
									},

									toolbar: {
										'main-insert':      'mainInsertToolbar'
									}
								};

							_.each( handlers, function( regionHandlers, region ) {
								_.each( regionHandlers, function( callback, handler ) {
									this.on( region + ':render:' + handler, this[ callback ], this );
								}, this );
							}, this );
						},

						// Menus
						mainMenu: function( view ) {
							view.set({
								'library-separator': new wp.media.View({
									className: 'separator',
									priority: 100
								})
							});
						},

						// Content
						embedContent: function() {
							var view = new wp.media.view.Embed({
								controller: this,
								model:      this.state()
							}).render();

							this.content.set( view );
							view.url.focus();
						},

						editSelectionContent: function() {
							var state = this.state(),
								selection = state.get('selection'),
								view;

							view = new wp.media.view.AttachmentsBrowser({
								controller: this,
								collection: selection,
								selection:  selection,
								model:      state,
								sortable:   true,
								search:     false,
								dragInfo:   true,

								AttachmentView: wp.media.view.Attachment.EditSelection
							}).render();

							view.toolbar.set( 'backToLibrary', {
								text:     'Return to Library',
								priority: -100,

								click: function() {
									this.controller.content.mode('browse');
								}
							});

							// Browse our library of attachments.
							this.content.set( view );
						},

						// Toolbars
						selectionStatusToolbar: function( view ) {
							var editable = this.state().get('editable');

							view.set( 'selection', new wp.media.view.Selection({
								controller: this,
								collection: this.state().get('selection'),
								priority:   -40,

								// If the selection is editable, pass the callback to
								// switch the content mode.
								editable: editable && function() {
									this.controller.content.mode('edit-selection');
								}
							}).render() );
						},

						mainInsertToolbar: function( view ) {
							var controller = this;

							this.selectionStatusToolbar( view );

							view.set( 'insert', {
								style:    'primary',
								priority: 80,
								text:     'Select Image',
								requires: { selection: true },

								click: function() {
									var state = controller.state(),
										selection = state.get('selection');

									controller.close();
									state.trigger( 'insert', selection ).reset();
								}
							});
						},

						featuredImageToolbar: function( toolbar ) {
							this.createSelectToolbar( toolbar, {
								text:  'Set Featured Image',
								state: this.options.state || 'upload'
							});
						},

						mainEmbedToolbar: function( toolbar ) {
							toolbar.view = new wp.media.view.Toolbar.Embed({
								controller: this,
								text: 'Insert Image'
							});
						}		
					});
				}
			";
		}	
		/**
		 * Returns the image selector JavaScript script to be loaded in the head tag of the created admin pages.
		 * @var				string
		 * @remark			It is accessed from the main class and meta box class.
		 * @remark			Moved to the base class since 2.1.0.
		 * @access			private	
		 * @internal
		 * @return			string			The image selector script.
		 * @since			2.0.0
		 * @since			2.1.5			Moved from the AdminPageFramework_Properties_Base class. Changed the name from getImageSelectorScript(). Changed the scope to private and not static anymore.
		 */		
		private function getScript_ImageSelector( $sReferrer, $sThickBoxTitle, $sThickBoxButtonUseThis ) {
			
			if( ! function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.4.x or below
				return "
					jQuery( document ).ready( function(){
						jQuery( '.select_image' ).click( function() {
							pressed_id = jQuery( this ).attr( 'id' );
							field_id = pressed_id.substring( 13 );	// remove the select_image_ prefix
							var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );
							tb_show( '{$sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$sThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
							return false;	// do not click the button after the script by returning false.
						});
						
						window.original_send_to_editor = window.send_to_editor;
						window.send_to_editor = function( sRawHTML ) {

							var sHTML = '<div>' + sRawHTML + '</div>';	// This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.
							var src = jQuery( 'img', sHTML ).attr( 'src' );
							var alt = jQuery( 'img', sHTML ).attr( 'alt' );
							var title = jQuery( 'img', sHTML ).attr( 'title' );
							var width = jQuery( 'img', sHTML ).attr( 'width' );
							var height = jQuery( 'img', sHTML ).attr( 'height' );
							var classes = jQuery( 'img', sHTML ).attr( 'class' );
							var id = ( classes ) ? classes.replace( /(.*?)wp-image-/, '' ) : '';	// attachment ID	
							var sCaption = sRawHTML.replace( /\[(\w+).*?\](.*?)\[\/(\w+)\]/m, '$2' )
								.replace( /<a.*?>(.*?)<\/a>/m, '' );
							var align = sRawHTML.replace( /^.*?\[\w+.*?\salign=([\'\"])(.*?)[\'\"]\s.+$/mg, '$2' );	//\'\" syntax fixer
							var link = jQuery( sHTML ).find( 'a:first' ).attr( 'href' );

							// Escape the strings of some of the attributes.
							var sCaption = jQuery( '<div/>' ).text( sCaption ).html();
							var sAlt = jQuery( '<div/>' ).text( alt ).html();
							var title = jQuery( '<div/>' ).text( title ).html();						
							
							// If the user wants to save relevant attributes, set them.
							jQuery( '#' + field_id ).val( src );	// sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
							jQuery( '#' + field_id + '_id' ).val( id );
							jQuery( '#' + field_id + '_width' ).val( width );
							jQuery( '#' + field_id + '_height' ).val( height );
							jQuery( '#' + field_id + '_caption' ).val( sCaption );
							jQuery( '#' + field_id + '_alt' ).val( sAlt );
							jQuery( '#' + field_id + '_title' ).val( title );						
							jQuery( '#' + field_id + '_align' ).val( align );						
							jQuery( '#' + field_id + '_link' ).val( link );						
							
							// Update the preview
							jQuery( '#image_preview_' + field_id ).attr( 'alt', alt );
							jQuery( '#image_preview_' + field_id ).attr( 'title', title );
							jQuery( '#image_preview_' + field_id ).attr( 'data-classes', classes );
							jQuery( '#image_preview_' + field_id ).attr( 'data-id', id );
							jQuery( '#image_preview_' + field_id ).attr( 'src', src );	// updates the preview image
							jQuery( '#image_preview_container_' + field_id ).css( 'display', '' );	// updates the visibility
							jQuery( '#image_preview_' + field_id ).show()	// updates the visibility
							
							// restore the original send_to_editor
							window.send_to_editor = window.original_send_to_editor;
							
							// close the thickbox
							tb_remove();	

						}
					});
				";
					
			return "jQuery( document ).ready( function(){

				// Global Function Literal 
				setAPFImageUploader = function( sInputID, fMultiple, fExternalSource ) {

					jQuery( '#select_image_' + sInputID ).unbind( 'click' );	// for repeatable fields
					jQuery( '#select_image_' + sInputID ).click( function( e ) {
						
						window.wpActiveEditor = null;						
						e.preventDefault();
						
						// If the uploader object has already been created, reopen the dialog
						if ( custom_uploader ) {
							custom_uploader.open();
							return;
						}					
						
						// Store the original select object in a global variable
						oAPFOriginalImageUploaderSelectObject = wp.media.view.MediaFrame.Select;
						
						// Assign a custom select object.
						wp.media.view.MediaFrame.Select = fExternalSource ? getAPFCustomMediaUploaderSelectObject() : oAPFOriginalImageUploaderSelectObject;
						var custom_uploader = wp.media({
							title: '{$sThickBoxTitle}',
							button: {
								text: '{$sThickBoxButtonUseThis}'
							},
							library     : { type : 'image' },
							multiple: fMultiple  // Set this to true to allow multiple files to be selected
						});
			
						// When the uploader window closes, 
						custom_uploader.on( 'close', function() {

							var state = custom_uploader.state();
							
							// Check if it's an external URL
							if ( typeof( state.props ) != 'undefined' && typeof( state.props.attributes ) != 'undefined' ) 
								var image = state.props.attributes;	
							
							// If the image variable is not defined at this point, it's an attachment, not an external URL.
							if ( typeof( image ) !== 'undefined'  ) {
								setPreviewElement( sInputID, image );
							} else {
								
								var selection = custom_uploader.state().get( 'selection' );
								selection.each( function( attachment, index ) {
									attachment = attachment.toJSON();
									if( index == 0 ){	
										// place first attachment in field
										setPreviewElement( sInputID, attachment );
									} else{
										
										var field_container = jQuery( '#' + sInputID ).closest( '.admin-page-framework-field' );
										var new_field = addAPFRepeatableField( field_container.attr( 'id' ) );
										var sInputIDOfNewField = new_field.find( 'input' ).attr( 'id' );
										setPreviewElement( sInputIDOfNewField, attachment );
			
									}
								});				
								
							}
							
							// Restore the original select object.
							wp.media.view.MediaFrame.Select = oAPFOriginalImageUploaderSelectObject;
											
						});
						
						// Open the uploader dialog
						custom_uploader.open();											
						return false;       
					});	
				
					var setPreviewElement = function( sInputID, image ) {

						// Escape the strings of some of the attributes.
						var sCaption = jQuery( '<div/>' ).text( image.caption ).html();
						var sAlt = jQuery( '<div/>' ).text( image.alt ).html();
						var title = jQuery( '<div/>' ).text( image.title ).html();
						
						// If the user want the attributes to be saved, set them in the input tags.
						jQuery( 'input#' + sInputID ).val( image.url );		// the url field is mandatory so it does not have the suffix.
						jQuery( 'input#' + sInputID + '_id' ).val( image.id );
						jQuery( 'input#' + sInputID + '_width' ).val( image.width );
						jQuery( 'input#' + sInputID + '_height' ).val( image.height );
						jQuery( 'input#' + sInputID + '_caption' ).val( sCaption );
						jQuery( 'input#' + sInputID + '_alt' ).val( sAlt );
						jQuery( 'input#' + sInputID + '_title' ).val( title );
						jQuery( 'input#' + sInputID + '_align' ).val( image.align );
						jQuery( 'input#' + sInputID + '_link' ).val( image.link );
						
						// Update up the preview
						jQuery( '#image_preview_' + sInputID ).attr( 'data-id', image.id );
						jQuery( '#image_preview_' + sInputID ).attr( 'data-width', image.width );
						jQuery( '#image_preview_' + sInputID ).attr( 'data-height', image.height );
						jQuery( '#image_preview_' + sInputID ).attr( 'data-caption', sCaption );
						jQuery( '#image_preview_' + sInputID ).attr( 'alt', sAlt );
						jQuery( '#image_preview_' + sInputID ).attr( 'title', title );
						jQuery( '#image_preview_' + sInputID ).attr( 'src', image.url );
						jQuery( '#image_preview_container_' + sInputID ).show();				
						
					}
				}		
			});
			";
		}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 
			"/* Image Field Preview Container */
			.admin-page-framework-field .image_preview {
				border: none; 
				clear:both; 
				margin-top: 1em;
				margin-bottom: 1em;
				display: block; 
			}		
			@media only screen and ( max-width: 1200px ) {
				.admin-page-framework-field .image_preview {
					max-width: 600px;
				}
			} 
			@media only screen and ( max-width: 900px ) {
				.admin-page-framework-field .image_preview {
					max-width: 440px;
				}
			}	
			@media only screen and ( max-width: 600px ) {
				.admin-page-framework-field .image_preview {
					max-width: 300px;
				}
			}		
			@media only screen and ( max-width: 480px ) {
				.admin-page-framework-field .image_preview {
					max-width: 240px;
				}
			}
			@media only screen and ( min-width: 1200px ) {
				.admin-page-framework-field .image_preview {
					max-width: 600px;
				}
			}		 
			.admin-page-framework-field .image_preview img {		
				width: auto;
				height: auto; 
				max-width: 100%;
				display: block;
			}
		/* Image Uploader Button */
			.admin-page-framework-field-image input {
				margin-right: 0.5em;
			}
			.select_image.button.button-small {
				vertical-align: baseline;
			}			
		" . PHP_EOL;	
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];
		$bMultipleFields = is_array( $aFields );	
		$bRepeatable = $aField['repeatable'];
			
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] =
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"					
					. $this->getImageInputTags( $vValue, $aField, $sFieldName, $sTagID, $sKey, $sLabel, $bMultipleFields, $_aDefaultKeys )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-image' id='{$sTagID}'>" 
				. implode( PHP_EOL, $aOutput ) 
			. "</div>";		
		
	}	
	
		/**
		 * A helper function for the above replyToGetInputField() method to return input elements.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AdminPageFramework_InputField. Added some parameters.
		 */
		private function getImageInputTags( $vValue, $aField, $sFieldName, $sTagID, $sKey, $sLabel, $bMultipleFields, $_aDefaultKeys ) {
			
			// If the saving extra attributes are not specified, the input field will be single only for the URL. 
			$iCountAttributes = count( ( array ) $aField['attributes_to_capture'] );
			
			// The URL input field is mandatory as the preview element uses it.
			$aOutputs = array(
				( $sLabel && ! $aField['repeatable']
					? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
					: ''
				)			
				. "<input id='{$sTagID}_{$sKey}' "	// the main url element does not have the suffix of the attribute
					. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
					. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) . "' "
					. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
					. "type='text' "	// text
					. "name='" . ( $bMultipleFields ? "{$sFieldName}[{$sKey}]" : "{$sFieldName}" ) . ( $iCountAttributes ? "[url]" : "" ) .  "' "
					. "value='" . ( $sImageURL = $this->getImageInputValue( $vValue, $sKey, $bMultipleFields, $iCountAttributes ? 'url' : '', $_aDefaultKeys  ) ) . "' "
					. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
					. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
				. "/>"	
			);
			
			// Add the input fields for saving extra attributes. It overrides the name attribute of the default text field for URL and saves them as an array.
			foreach( ( array ) $aField['attributes_to_capture'] as $sAttribute )
				$aOutputs[] = 
					"<input id='{$sTagID}_{$sKey}_{$sAttribute}' "
						. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
						. "type='hidden' " 	// other additional attributes are hidden
						. "name='" . ( $bMultipleFields ? "{$sFieldName}[{$sKey}]" : "{$sFieldName}" ) . "[{$sAttribute}]' " 
						. "value='" . $this->getImageInputValue( $vValue, $sKey, $bMultipleFields, $sAttribute, $_aDefaultKeys ) . "' "
						. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
					. "/>";
			
			// Returns the outputs as well as the uploader buttons and the preview element.
			return 
				"<div class='admin-page-framework-input-label-container admin-page-framework-input-container image-field'>"
					. "<label for='{$sTagID}_{$sKey}' >"
						. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
						. implode( PHP_EOL, $aOutputs ) . PHP_EOL
						. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
					. "</label>"
				. "</div>"
				. ( $this->getCorrespondingArrayValue( $aField['vImagePreview'], $sKey, true )
					? "<div id='image_preview_container_{$sTagID}_{$sKey}' "
							. "class='image_preview' "
							. "style='" . ( $sImageURL ? "" : "display : none;" ) . "'"
						. ">"
							. "<img src='{$sImageURL}' "
								. "id='image_preview_{$sTagID}_{$sKey}' "
							. "/>"
						. "</div>"
					: "" )
				. $this->getImageUploaderButtonScript( "{$sTagID}_{$sKey}", $aField['repeatable'] ? true : false, $aField['allow_external_source'] ? true : false );
			
		}
		/**
		 * A helper function for the above getImageInputTags() method that retrieve the specified input field value.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AdminPageFramework_InputField
		 */
		private function getImageInputValue( $vValue, $sKey, $bMultipleFields, $sCaptureAttribute, $_aDefaultKeys ) {	

			$vValue = $bMultipleFields
				? $this->getCorrespondingArrayValue( $vValue, $sKey, $_aDefaultKeys['default'] )
				: ( isset( $vValue ) ? $vValue : $_aDefaultKeys['default'] );

			return $sCaptureAttribute
				? ( isset( $vValue[ $sCaptureAttribute ] ) ? $vValue[ $sCaptureAttribute ] : "" )
				: $vValue;
			
		}
		/**
		 * A helper function for the above getImageInputTags() method to add a image button script.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AdminPageFramework_InputField.
		 */
		private function getImageUploaderButtonScript( $sInputID, $bRpeatable, $bExternalSource ) {
			
			$sButton ="<a id='select_image_{$sInputID}' "
						. "href='#' "
						. "class='select_image button button-small'"
						. "data-uploader_type='" . ( function_exists( 'wp_enqueue_media' ) ? 1 : 0 ) . "'"
						. "data-enable_external_source='" . ( $bExternalSource ? 1 : 0 ) . "'"
					. ">"
						. $this->oMsg->__( 'select_image' )
				."</a>";
			
			$sScript = "
				if ( jQuery( 'a#select_image_{$sInputID}' ).length == 0 ) {
					jQuery( 'input#{$sInputID}' ).after( \"{$sButton}\" );
				}			
			" . PHP_EOL;

			if( function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.5 or above
				$sScript .="
					jQuery( document ).ready( function(){			
						setAPFImageUploader( '{$sInputID}', '{$bRpeatable}', '{$bExternalSource}' );
					});" . PHP_EOL;	
					
			return "<script type='text/javascript'>" . $sScript . "</script>" . PHP_EOL;

		}	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_media' ) ) :
/**
 * Defines the media field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_media extends AdminPageFramework_InputFieldType_image {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'attributes_to_capture'					=> array(),
			'size'									=> 60,
			'vMaxLength'							=> 400,
			'sTickBoxTitle' 						=> '',		// ( string ) This is for the image field type.
			'sLabelUseThis' 						=> '',		// ( string ) This is for the image field type.			
			'allow_external_source' 					=> true,	// ( boolean ) Indicates whether the media library box has the From URL tab.
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
		$this->enqueueMediaUploader();
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return $this->getScript_CustomMediaUploaderObject()	. PHP_EOL	// defined in the parent class
			. $this->getScript_MediaUploader(
				"admin_page_framework", 
				$this->oMsg->__( 'upload_file' ),
				$this->oMsg->__( 'use_this_file' )
			);
	}	
		/**
		 * Returns the media uploader JavaScript script to be loaded in the head tag of the created admin pages.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from ... Chaned the name from getMediaUploaderScript().
		 */
		private function getScript_MediaUploader( $sReferrer, $sThickBoxTitle, $sThickBoxButtonUseThis ) {
			
			if ( ! function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.4.x or below
				return "
					jQuery( document ).ready( function(){
						jQuery( '.select_media' ).click( function() {
							pressed_id = jQuery( this ).attr( 'id' );
							field_id = pressed_id.substring( 13 );	// remove the select_file_ prefix
							var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );					
							tb_show( '{$sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$sThickBoxButtonUseThis}&amp;type=media&amp;TB_iframe=true', false );
							return false;	// do not click the button after the script by returning false.
						});
						
						window.original_send_to_editor = window.send_to_editor;
						window.send_to_editor = function( sRawHTML, param ) {

							var sHTML = '<div>' + sRawHTML + '</div>';	// This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.
							var src = jQuery( 'a', sHTML ).attr( 'href' );
							var classes = jQuery( 'a', sHTML ).attr( 'class' );
							var id = ( classes ) ? classes.replace( /(.*?)wp-image-/, '' ) : '';	// attachment ID	
						
							// If the user wants to save relavant attributes, set them.
							jQuery( '#' + field_id ).val( src );	// sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
							jQuery( '#' + field_id + '_id' ).val( id );			
								
							// restore the original send_to_editor
							window.send_to_editor = window.original_send_to_editor;
							
							// close the thickbox
							tb_remove();	

						}
					});
				";
				
			return "
			jQuery( document ).ready( function(){		
				// Global Function Literal 
				setAPFMediaUploader = function( sInputID, fMultiple, fExternalSource ) {

					jQuery( '#select_media_' + sInputID ).unbind( 'click' );	// for repeatable fields
					jQuery( '#select_media_' + sInputID ).click( function( e ) {
						
						window.wpActiveEditor = null;						
						e.preventDefault();
						
						// If the uploader object has already been created, reopen the dialog
						if ( media_uploader ) {
							media_uploader.open();
							return;
						}		
						
						// Store the original select object in a global variable
						oAPFOriginalMediaUploaderSelectObject = wp.media.view.MediaFrame.Select;
						
						// Assign a custom select object.
						wp.media.view.MediaFrame.Select = fExternalSource ? getAPFCustomMediaUploaderSelectObject() : oAPFOriginalMediaUploaderSelectObject;
						var media_uploader = wp.media({
							title: '{$sThickBoxTitle}',
							button: {
								text: '{$sThickBoxButtonUseThis}'
							},
							multiple: fMultiple  // Set this to true to allow multiple files to be selected
						});
			
						// When the uploader window closes, 
						media_uploader.on( 'close', function() {

							var state = media_uploader.state();
							
							// Check if it's an external URL
							if ( typeof( state.props ) != 'undefined' && typeof( state.props.attributes ) != 'undefined' ) 
								var image = state.props.attributes;	
							
							// If the image variable is not defined at this point, it's an attachment, not an external URL.
							if ( typeof( image ) !== 'undefined'  ) {
								setPreviewElement( sInputID, image );
							} else {
								
								var selection = media_uploader.state().get( 'selection' );
								selection.each( function( attachment, index ) {
									attachment = attachment.toJSON();
									if( index == 0 ){	
										// place first attachment in field
										setPreviewElement( sInputID, attachment );
									} else{
										
										var field_container = jQuery( '#' + sInputID ).closest( '.admin-page-framework-field' );
										var new_field = addAPFRepeatableField( field_container.attr( 'id' ) );
										var sInputIDOfNewField = new_field.find( 'input' ).attr( 'id' );
										setPreviewElement( sInputIDOfNewField, attachment );
			
									}
								});				
								
							}
							
							// Restore the original select object.
							wp.media.view.MediaFrame.Select = oAPFOriginalMediaUploaderSelectObject;	
							
						});
						
						// Open the uploader dialog
						media_uploader.open();											
						return false;       
					});	
				
					var setPreviewElement = function( sInputID, image ) {
									
						// If the user want the attributes to be saved, set them in the input tags.
						jQuery( '#' + sInputID ).val( image.url );		// the url field is mandatory so  it does not have the suffix.
						jQuery( '#' + sInputID + '_id' ).val( image.id );				
						jQuery( '#' + sInputID + '_caption' ).val( jQuery( '<div/>' ).text( image.caption ).html() );				
						jQuery( '#' + sInputID + '_description' ).val( jQuery( '<div/>' ).text( image.description ).html() );				
						
					}
				}		
				
			});";
		}
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return
		"/* Media Uploader Button */
			.admin-page-framework-field-media input {
				margin-right: 0.5em;
			}
			.select_media.button.button-small {
				vertical-align: baseline;
			}		
		";
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];			
		$bMultipleFields = is_array( $aFields );	
		$bRepeatable = $aField['repeatable'];			
			
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] =
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"					
					. $this->getMediaInputTags( $vValue, $aField, $sFieldName, $sTagID, $sKey, $sLabel, $bMultipleFields, $_aDefaultKeys )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-media' id='{$sTagID}'>" 
				. implode( PHP_EOL, $aOutput ) 
			. "</div>";		
			
	}
		/**
		 * A helper function for the above getImageField() method to return input elements.
		 * 
		 * @since			2.1.3
		 */
		private function getMediaInputTags( $vValue, $aField, $sFieldName, $sTagID, $sKey, $sLabel, $bMultipleFields, $_aDefaultKeys ) {
	
			// If the saving extra attributes are not specified, the input field will be single only for the URL. 
			$iCountAttributes = count( ( array ) $aField['attributes_to_capture'] );	
			
			// The URL input field is mandatory as the preview element uses it.
			$aOutputs = array(
				( $sLabel && ! $aField['repeatable']
					? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>" 
					: ''
				)
				. "<input id='{$sTagID}_{$sKey}' "	// the main url element does not have the suffix of the attribute
					. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
					. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) . "' "
					. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
					. "type='text' "	// text
					. "name='" . ( $bMultipleFields ? "{$sFieldName}[{$sKey}]" : "{$sFieldName}" ) . ( $iCountAttributes ? "[url]" : "" ) .  "' "
					. "value='" . ( $this->getMediaInputValue( $vValue, $sKey, $bMultipleFields, $iCountAttributes ? 'url' : '', $_aDefaultKeys ) ) . "' "
					. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
					. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
				. "/>"	
			);
			
			// Add the input fields for saving extra attributes. It overrides the name attribute of the default text field for URL and saves them as an array.
			foreach( ( array ) $aField['attributes_to_capture'] as $sAttribute )
				$aOutputs[] = 
					"<input id='{$sTagID}_{$sKey}_{$sAttribute}' "
						. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
						. "type='hidden' " 	// other additional attributes are hidden
						. "name='" . ( $bMultipleFields ? "{$sFieldName}[{$sKey}]" : "{$sFieldName}" ) . "[{$sAttribute}]' " 
						. "value='" . $this->getMediaInputValue( $vValue, $sKey, $bMultipleFields, $sAttribute, $_aDefaultKeys  ) . "' "
						. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
					. "/>";
			
			// Returns the outputs as well as the uploader buttons and the preview element.
			return 
				"<div class='admin-page-framework-input-label-container admin-page-framework-input-container media-field'>"
					. "<label for='{$sTagID}_{$sKey}' >"
						. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] )
						. implode( PHP_EOL, $aOutputs ) . PHP_EOL
						. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
					. "</label>"
				. "</div>"
				. $this->getMediaUploaderButtonScript( "{$sTagID}_{$sKey}", $aField['repeatable'] ? true : false, $aField['allow_external_source'] ? true : false );
			
		}
		/**
		 * A helper function for the above getMediaInputTags() method that retrieve the specified input field value.
		 * @since			2.1.3
		 */
		private function getMediaInputValue( $vValue, $sKey, $bMultipleFields, $sCaptureAttribute, $_aDefaultKeys ) {	

			$vValue = $bMultipleFields
				? $this->getCorrespondingArrayValue( $vValue, $sKey, $_aDefaultKeys['default'] )
				: ( isset( $vValue ) ? $vValue : $_aDefaultKeys['default'] );

			return $sCaptureAttribute
				? ( isset( $vValue[ $sCaptureAttribute ] ) ? $vValue[ $sCaptureAttribute ] : "" )
				: $vValue;
			
		}		
		/**
		 * A helper function for the above getMediaInputTags() method to add a image button script.
		 * 
		 * @since			2.1.3
		 */
		private function getMediaUploaderButtonScript( $sInputID, $bRpeatable, $bExternalSource ) {
			
			$sButton ="<a id='select_media_{$sInputID}' "
						. "href='#' "
						. "class='select_media button button-small'"
						. "data-uploader_type='" . ( function_exists( 'wp_enqueue_media' ) ? 1 : 0 ) . "'"
						. "data-enable_external_source='" . ( $bExternalSource ? 1 : 0 ) . "'"
					. ">"
						. $this->oMsg->__( 'select_file' )
				."</a>";
			
			$sScript = "
				if ( jQuery( 'a#select_media_{$sInputID}' ).length == 0 ) {
					jQuery( 'input#{$sInputID}' ).after( \"{$sButton}\" );
				}			
			" . PHP_EOL;

			if( function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.5 or above
				$sScript .="
					jQuery( document ).ready( function(){			
						setAPFMediaUploader( '{$sInputID}', '{$bRpeatable}', '{$bExternalSource}' );
					});" . PHP_EOL;	
					
			return "<script type='text/javascript'>" . $sScript . "</script>" . PHP_EOL;

		}	
		
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_select' ) ) :
/**
 * Defines the select field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_select extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'size'					=> 1,
			'vMultiple'				=> false,				// ( array or boolean ) This value indicates whether the select tag should have the multiple attribute or not.
			'vWidth'				=> '',
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
		
		// The value of the label key must be an array for the select type.
		if ( ! is_array( $aField['label'] ) ) return;	

		$bSingle = ( $this->getArrayDimension( ( array ) $aField['label'] ) == 1 );
		$aLabels = $bSingle ? array( $aField['label'] ) : $aField['label'];
		foreach( $aLabels as $sKey => $label ) {
			
			$bMultiple = $this->getCorrespondingArrayValue( $aField['vMultiple'], $sKey, $_aDefaultKeys['vMultiple'] );
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. "<span class='admin-page-framework-input-container'>"
								. "<select id='{$sTagID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='{$aField['type']}' "
									. ( $bMultiple ? "multiple='Multiple' " : '' )
									. "name=" . ( $bSingle ? "'{$sFieldName}" : "'{$sFieldName}[{$sKey}]" ) . ( $bMultiple ? "[]' " : "' " )
									. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
									. "size=" . ( $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) ) . " "
									. ( ( $sWidth = $this->getCorrespondingArrayValue( $aField['vWidth'], $sKey, $_aDefaultKeys['vWidth'] ) ) ? "style='width:{$sWidth};' " : "" )
								. ">"
									. $this->getOptionTags( $label, $vValue, $sTagID, $sKey, $bSingle, $bMultiple )
								. "</select>"
							. "</span>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-select' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";				
	
	}	
	
		/**
		 * A helper function for the above replyToGetInputField() methods.
		 * 
		 * @since			2.0.0
		 * @since			2.0.1			Added the $vValue parameter to the second parameter. This is the result of supporting the size field type.
		 * @since			2.1.5			Added the $sTagID parameter.
		 */ 
		private function getOptionTags( $aLabels, $vValue, $sTagID, $sIterationID, $bSingle, $bMultiple=false ) {	

			$aOutput = array();
			foreach ( $aLabels as $sKey => $sLabel ) {
				$aValue = $bSingle ? ( array ) $vValue : ( array ) $this->getCorrespondingArrayValue( $vValue, $sIterationID, array() ) ;
				$aOutput[] = "<option "
						. "id='{$sTagID}_{$sIterationID}_{$sKey}' "
						. "value='{$sKey}' "
						. (	$bMultiple 
							? ( in_array( $sKey, $aValue ) ? 'selected="Selected"' : '' )
							: ( $this->getCorrespondingArrayValue( $vValue, $sIterationID, null ) == $sKey ? "selected='Selected'" : "" )
						)
					. ">"
						. $sLabel
					. "</option>";
			}
			return implode( '', $aOutput );
		}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_radio' ) ) :
/**
 * Defines the radio field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_radio extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			// 'size'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
		
		// The value of the label key must be an array for the select type.
		if ( ! is_array( $aField['label'] ) ) return;	
		
		$bSingle = ( $this->getArrayDimension( ( array ) $aField['label'] ) == 1 );
		$aLabels =  $bSingle ? array( $aField['label'] ) : $aField['label'];
		foreach( $aLabels as $sKey => $label )  
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. $this->getRadioTags( $aField, $vValue, $label, $sFieldName, $sTagID, $sKey, $bSingle, $_aDefaultKeys )				
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-radio' id='{$sTagID}'>" 
				. implode( '', $aOutput )
			. "</div>";
		
	}
		/**
		 * A helper function for the <em>getRadioField()</em> method.
		 * @since			2.0.0
		 * @since			2.1.5			Moved from AdminPageFramework_InputField. Added the $aField, $sFieldName, $_aDefaultKeys, $sTagID, and $vValue parameter.
		 */ 
		private function getRadioTags( $aField, $vValue, $aLabels, $sFieldName, $sTagID, $sIterationID, $bSingle, $_aDefaultKeys ) {
			
			$aOutput = array();
			foreach ( $aLabels as $sKey => $sLabel ) 
				$aOutput[] = 
					"<div class='admin-page-framework-input-label-container admin-page-framework-radio-label' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<label for='{$sTagID}_{$sIterationID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. "<span class='admin-page-framework-input-container'>"
								. "<input "
									. "id='{$sTagID}_{$sIterationID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='radio' "
									. "value='{$sKey}' "
									. "name=" . ( ! $bSingle  ? "'{$sFieldName}[{$sIterationID}]' " : "'{$sFieldName}' " )
									. ( $this->getCorrespondingArrayValue( $vValue, $sIterationID, null ) == $sKey ? 'Checked ' : '' )
									. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
								. "/>"							
							. "</span>"
							. "<span class='admin-page-framework-input-label-string'>"
								. $sLabel
							. "</span>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>";

			return implode( '', $aOutput );
		}

}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_checkbox' ) ) :
/**
 * Defines the checkbox field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_checkbox extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			// 'size'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		

		foreach( ( array ) $aField['label'] as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container admin-page-framework-checkbox-label' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<label for='{$sTagID}_{$sKey}'>"	
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. "<span class='admin-page-framework-input-container'>"
								. "<input type='hidden' name=" .  ( is_array( $aField['label'] ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " ) . " value='0' />"	// the unchecked value must be set prior to the checkbox input field.
								. "<input "
									. "id='{$sTagID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='{$aField['type']}' "	// checkbox
									. "name=" . ( is_array( $aField['label'] ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
									. "value='1' "
									. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
									. ( $this->getCorrespondingArrayValue( $vValue, $sKey, null ) == 1 ? "Checked " : '' )
								. "/>"							
							. "</span>"
							. "<span class='admin-page-framework-input-label-string'>"
								. $sLabel
							. "</span>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>" // end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-checkbox' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";	
	
	}

}
endif;


if ( ! class_exists( 'AdminPageFramework_InputFieldType_size' ) ) :
/**
 * Defines the size field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_size extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'size_units'				=> array(	// the default unit size array.
				'px'	=> 'px',	// pixel
				'%'		=> '%',		// percentage
				'em'	=> 'em',	// font size
				'ex'	=> 'ex',	// font height
				'in'	=> 'in',	// inch
				'cm'	=> 'cm',	// centimetre
				'mm'	=> 'mm',	// millimetre
				'pt'	=> 'pt',	// point
				'pc'	=> 'pc',	// pica
			),
			'size'						=> 10,
			'vUnitSize'					=> 1,
			'vMaxLength'				=> 400,
			'vMin'						=> null,
			'vMax'						=> null,
			'vStep'						=> null,
			'vMultiple'					=> false,
			'vWidth'					=> '',
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return
		"/* Size Field Type */
		.admin-page-framework-field-size input {
			text-align: right;
		}
		.admin-page-framework-field-size select.size-field-select {
			vertical-align: 0px;			
		}
		" . PHP_EOL;
	}
	
	/**
	 * Returns the output of the field type.
	 *
	 * Returns the size input fields. This enables for the user to set a size with a unit. This is made up of a text input field and a drop-down selector field. 
	 * Useful for theme developers.
	 * 
	 * @since			2.0.1
	 * @since			2.1.5			Moved from AdminPageFramework_InputField. Changed the name from getSizeField().
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
				
		$bSingle = ! is_array( $aField['label'] );
		$bIsSizeUnitForSingle = ( $this->getArrayDimension( ( array ) $aField['size_units'] ) == 1 );
		$aSizeUnits = isset( $aField['size_units'] ) && is_array( $aField['size_units'] ) && $bIsSizeUnitForSingle 
			? $aField['size_units']
			: $_aDefaultKeys['size_units'];		
		
		foreach( ( array ) $aField['label'] as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<label for='{$sTagID}_{$sKey}'>"
						. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
						. ( $sLabel 
							? "<span class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel ."</span>"
							: "" 
						)
						. "<input id='{$sTagID}_{$sKey}' "	// number field
							// . "style='text-align: right;'"
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) . "' "
							. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
							. "type='number' "	// number
							. "name=" . ( $bSingle ? "'{$sFieldName}[size]' " : "'{$sFieldName}[{$sKey}][size]' " )
							. "value='" . ( $bSingle ? $this->getCorrespondingArrayValue( $vValue['size'], $sKey, '' ) : $this->getCorrespondingArrayValue( $this->getCorrespondingArrayValue( $vValue, $sKey, array() ), 'size', '' ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
							. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
							. "min='" . $this->getCorrespondingArrayValue( $aField['vMin'], $sKey, $_aDefaultKeys['vMin'] ) . "' "
							. "max='" . $this->getCorrespondingArrayValue( $aField['vMax'], $sKey, $_aDefaultKeys['vMax'] ) . "' "
							. "step='" . $this->getCorrespondingArrayValue( $aField['vStep'], $sKey, $_aDefaultKeys['vStep'] ) . "' "					
						. "/>"
					. "</label>"
						. "<select id='{$sTagID}_{$sKey}' class='size-field-select'"	// select field
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "type='{$aField['type']}' "
							. ( ( $bMultipleOptions = $this->getCorrespondingArrayValue( $aField['vMultiple'], $sKey, $_aDefaultKeys['vMultiple'] ) ) ? "multiple='Multiple' " : '' )
							. "name=" . ( $bSingle ? "'{$sFieldName}[unit]" : "'{$sFieldName}[{$sKey}][unit]" ) . ( $bMultipleOptions ? "[]' " : "' " )						
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
							. "size=" . ( $this->getCorrespondingArrayValue( $aField['vUnitSize'], $sKey, $_aDefaultKeys['vUnitSize'] ) ) . " "
							. ( ( $sWidth = $this->getCorrespondingArrayValue( $aField['vWidth'], $sKey, $_aDefaultKeys['vWidth'] ) ) ? "style='width:{$sWidth};' " : "" )
						. ">"
						. $this->getOptionTags( 
							$bSingle ? $aSizeUnits : $this->getCorrespondingArrayValue( $aField['size_units'], $sKey, $aSizeUnits ),
							$bSingle ? $this->getCorrespondingArrayValue( $vValue['unit'], $sKey, 'px' ) : $this->getCorrespondingArrayValue( $this->getCorrespondingArrayValue( $vValue, $sKey, array() ), 'unit', 'px' ),
							$sTagID,
							$sKey, 
							true, 	// since the above value is directly passed, call the function as a single element.
							$bMultipleOptions 
						)
					. "</select>"
					. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);			

		return "<div class='admin-page-framework-field-size' id='{$sTagID}'>" 
			. implode( '', $aOutput )
		. "</div>";
		
	}
		/**
		 * A helper function for the above replyToGetInputField() methods.
		 * 
		 * @since			2.0.0
		 * @since			2.0.1			Added the $vValue parameter to the second parameter. This is the result of supporting the size field type.
		 * @since			2.1.5			Added the $sTagID parameter. Moved from AdminPageFramwrodk_InputField.
		 */ 
		private function getOptionTags( $aLabels, $vValue, $sTagID, $sIterationID, $bSingle, $bMultiple=false ) {	

			$aOutput = array();
			foreach ( $aLabels as $sKey => $sLabel ) {
				$aValue = $bSingle ? ( array ) $vValue : ( array ) $this->getCorrespondingArrayValue( $vValue, $sIterationID, array() ) ;
				$aOutput[] = "<option "
						. "id='{$sTagID}_{$sIterationID}_{$sKey}' "
						. "value='{$sKey}' "
						. (	$bMultiple 
							? ( in_array( $sKey, $aValue ) ? 'selected="Selected"' : '' )
							: ( $this->getCorrespondingArrayValue( $vValue, $sIterationID, null ) == $sKey ? "selected='Selected'" : "" )
						)
					. ">"
						. $sLabel
					. "</option>";
			}
			return implode( '', $aOutput );
		}

}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_hidden' ) ) :
/**
 * Defines the hidden field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_hidden extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			// 'size'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @remark			The user needs to assign the value to either the default key or the vValue key in order to set the hidden field. 
	 * If it's not set ( null value ), the below foreach will not iterate an element so no input field will be embedded.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5				Moved from the AdminPageFramework_InputField class. The name was changed from getHiddenField().
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
				
		foreach( ( array ) $vValue as $sKey => $sValue ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. ( ( $sLabel = $this->getCorrespondingArrayValue( $aField['label'], $sKey, $_aDefaultKeys['label'] ) ) 
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>{$sLabel}</span>" 
								: "" 
							)
							. "<div class='admin-page-framework-input-container'>"
								. "<input "
									. "id='{$sTagID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='{$aField['type']}' "	// hidden
									. "name=" . ( is_array( $aField['label'] ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
									. "value='" . $sValue  . "' "
									. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
								. "/>"
							. "</div>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-hidden' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}

}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_file' ) ) :
/**
 * Defines the file field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_file extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vAcceptAttribute'				=> 'audio/*|video/*|image/*|MIME_type',
			// 'size'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];		
					
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. ( $sLabel && ! $aField['repeatable'] ?
								"<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
								: ""
							)
							. "<input "
								. "id='{$sTagID}_{$sKey}' "
								. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
								. "accept='" . $this->getCorrespondingArrayValue( $aField['vAcceptAttribute'], $sKey, $_aDefaultKeys['vAcceptAttribute'] ) . "' "
								. "type='{$aField['type']}' "	// file
								. "name=" . ( is_array( $aFields ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
								. "value='" . $this->getCorrespondingArrayValue( $aFields, $sKey ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-file' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
	}

}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_posttype' ) ) :
/**
 * Defines the posttype field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_posttype extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'aRemove'					=> array( 'revision', 'attachment', 'nav_menu_item' ), // for the posttype checklist field type
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * Returns the output of post type checklist check boxes.
	 * 
	 * @remark			the posttype checklist field does not support multiple elements by passing an array of labels.
	 * @since			2.0.0
	 * 
	 * @since			2.1.5			Moved from AdminPageFramework_InputField.
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
						
		foreach( ( array ) $this->getPostTypeArrayForChecklist( $aField['aRemove'] ) as $sKey => $sValue ) {
			$sName = "{$sFieldName}[{$sKey}]";
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] )
							. "<span class='admin-page-framework-input-container'>"
								. "<input type='hidden' name='{$sName}' value='0' />"
								. "<input "
									. "id='{$sTagID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='checkbox' "
									. "name='{$sName}'"
									. "value='1' "
									. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
									. ( $this->getCorrespondingArrayValue( $vValue, $sKey, false ) == 1 ? "Checked " : '' )				
								. "/>"
							. "</span>"
							. "<span class='admin-page-framework-input-label-string'>"
								. $sValue
							. "</span>"				
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-posttype' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}	
	
		/**
		 * A helper function for the above getPosttypeChecklistField method.
		 * 
		 * @since			2.0.0
		 * @since			2.1.1			Changed the returning array to have the labels in its element values.
		 * @since			2.1.5			Moved from AdminPageFramework_InputTag.
		 * @return			array			The array holding the elements of installed post types' labels and their slugs except the specified expluding post types.
		 */ 
		private function getPostTypeArrayForChecklist( $aRemoveNames, $aPostTypes=array() ) {
			
			foreach( get_post_types( '','objects' ) as $oPostType ) 
				if (  isset( $oPostType->name, $oPostType->label ) ) 
					$aPostTypes[ $oPostType->name ] = $oPostType->label;

			return array_diff_key( $aPostTypes, array_flip( $aRemoveNames ) );	

		}		
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_taxonomy' ) ) :
/**
 * Defines the taxonomy field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_taxonomy extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'taxonomy_slugs'					=> 'category',			// ( string ) This is for the taxonomy field type.
			'height'						=> '250px',				// for the taxonomy checklist field type, since 2.1.1.
			'sWidth'						=> '100%',				// for the taxonomy checklist field type, since 2.1.1.		
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 * 
	 * Returns the JavaScript script of the taxonomy field type.
	 * 
	 * @since			2.1.1
	 * @since			2.1.5			Moved from AdminPageFramework_Properties_Base().
	 */ 
	public function replyToGetInputScripts() {
		return "
			jQuery( document ).ready( function() {
				jQuery( '.tab-box-container' ).each( function() {
					jQuery( this ).find( '.tab-box-tab' ).each( function( i ) {
						
						if ( i == 0 )
							jQuery( this ).addClass( 'active' );
							
						jQuery( this ).click( function( e ){
								 
							// Prevents jumping to the anchor which moves the scroll bar.
							e.preventDefault();
							
							// Remove the active tab and set the clicked tab to be active.
							jQuery( this ).siblings( 'li.active' ).removeClass( 'active' );
							jQuery( this ).addClass( 'active' );
							
							// Find the element id and select the content element with it.
							var thisTab = jQuery( this ).find( 'a' ).attr( 'href' );
							active_content = jQuery( this ).closest( '.tab-box-container' ).find( thisTab ).css( 'display', 'block' ); 
							active_content.siblings().css( 'display', 'none' );
							
						});
					});			
				});
			});
		";
	}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 
		"/* Taxonomy Field Type */
			.admin-page-framework-field .taxonomy-checklist li { 
				margin: 8px 0 8px 20px; 
			}
			.admin-page-framework-field div.taxonomy-checklist {
				padding: 8px 0 8px 10px;
				margin-bottom: 20px;
			}
			.admin-page-framework-field .taxonomy-checklist ul {
				list-style-type: none;
				margin: 0;
			}
			.admin-page-framework-field .taxonomy-checklist ul ul {
				margin-left: 1em;
			}
			.admin-page-framework-field .taxonomy-checklist-label {
				/* margin-left: 0.5em; */
			}		
		/* Tabbed box */
			.admin-page-framework-field .tab-box-container.categorydiv {
				max-height: none;
			}
			.admin-page-framework-field .tab-box-tab-text {
				display: inline-block;
			}
			.admin-page-framework-field .tab-box-tabs {
				line-height: 12px;
				margin-bottom: 0;
			
			}
			.admin-page-framework-field .tab-box-tabs .tab-box-tab.active {
				display: inline;
				border-color: #dfdfdf #dfdfdf #fff;
				margin-bottom: 0;
				padding-bottom: 1px;
				background-color: #fff;
			}
			.admin-page-framework-field .tab-box-container { 
				position: relative; width: 100%; 

			}
			.admin-page-framework-field .tab-box-tabs li a { color: #333; text-decoration: none; }
			.admin-page-framework-field .tab-box-contents-container {  
				padding: 0 0 0 20px; 
				border: 1px solid #dfdfdf; 
				background-color: #fff;
			}
			.admin-page-framework-field .tab-box-contents { 
				overflow: hidden; 
				overflow-x: hidden; 
				position: relative; 
				top: -1px; 
				height: 300px;  
			}
			.admin-page-framework-field .tab-box-content { 
				height: 300px;
				display: none; 
				overflow: auto; 
				display: block; 
				position: relative; 
				overflow-x: hidden;
			}
			.admin-page-framework-field .tab-box-content:target, 
			.admin-page-framework-field .tab-box-content:target, 
			.admin-page-framework-field .tab-box-content:target { 
				display: block; 
			}			
		" . PHP_EOL;
	}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputIEStyles() {
		return 	".tab-box-content { display: block; }
			.tab-box-contents { overflow: hidden;position: relative; }
			b { position: absolute; top: 0px; right: 0px; width:1px; height: 251px; overflow: hidden; text-indent: -9999px; }
		";	

	}	
	
	/**
	 * Returns the output of the field type.
	 * 
	 * Returns the output of taxonomy checklist check boxes.
	 * 
	 * @remark			Multiple fields are not supported.
	 * @remark			Repeater fields are not supported.
	 * @since			2.0.0
	 * @since			2.1.1			The checklist boxes are rendered in a tabbed single box.
	 * @since			2.1.5			Moved from AdminPageFramework_InputField.
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
		
		$aTabs = array();
		$aCheckboxes = array();
		foreach( ( array ) $aField['taxonomy_slugs'] as $sKey => $sTaxonomySlug ) {
			$sActive = isset( $sActive ) ? '' : 'active';	// inserts the active class selector into the first element.
			$aTabs[] = 
				"<li class='tab-box-tab'>"
					. "<a href='#tab-{$sKey}'>"
						. "<span class='tab-box-tab-text'>" 
							. $this->getCorrespondingArrayValue( empty( $aField['label'] ) ? null : $aField['label'], $sKey, $this->getLabelFromTaxonomySlug( $sTaxonomySlug ) )
						. "</span>"
					."</a>"
				."</li>";
			$aCheckboxes[] = 
				"<div id='tab-{$sKey}' class='tab-box-content' style='height: {$aField['height']};'>"
					. "<ul class='list:category taxonomychecklist form-no-clear'>"
						. wp_list_categories( array(
							'walker' => new AdminPageFramework_WalkerTaxonomyChecklist,	// the walker class instance
							'name'     => is_array( $aField['taxonomy_slugs'] ) ? "{$sFieldName}[{$sKey}]" : "{$sFieldName}",   // name of the input
							'selected' => $this->getSelectedKeyArray( $vValue, $sKey ), 		// checked items ( term IDs )	e.g.  array( 6, 10, 7, 15 ), 
							'title_li'	=> '',	// disable the Categories heading string 
							'hide_empty' => 0,	
							'echo'	=> false,	// returns the output
							'taxonomy' => $sTaxonomySlug,	// the taxonomy slug (id) such as category and post_tag 
							'sTagID' => $sTagID,
						) )					
					. "</ul>"			
					. "<!--[if IE]><b>.</b><![endif]-->"
				. "</div>";
		}
		$sTabs = "<ul class='tab-box-tabs category-tabs'>" . implode( '', $aTabs ) . "</ul>";
		$sContents = 
			"<div class='tab-box-contents-container'>"
				. "<div class='tab-box-contents' style='height: {$aField['height']};'>"
					. implode( '', $aCheckboxes )
				. "</div>"
			. "</div>";
			
		$sOutput = 
			"<div id='{$sTagID}' class='{$sFieldClassSelector} admin-page-framework-field-taxonomy tab-box-container categorydiv' style='max-width:{$aField['sWidth']};'>"
				. $sTabs . PHP_EOL
				. $sContents . PHP_EOL
			. "</div>";

		return $sOutput;

	}	
	
		/**
		 * A helper function for the above getTaxonomyChecklistField() method. 
		 * 
		 * @since			2.0.0
		 * @param			array			$vValue			This can be either an one-dimensional array ( for single field ) or a two-dimensional array ( for multiple fields ).
		 * @param			string			$sKey			
		 * @return			array			Returns an array consisting of keys whose value is true.
		 */ 
		private function getSelectedKeyArray( $vValue, $sKey ) {
					
			$vValue = ( array ) $vValue;	// cast array because the initial value (null) may not be an array.
			$iArrayDimension = $this->getArrayDimension( ( array ) $vValue );
					
			if ( $iArrayDimension == 1 )
				$aKeys = $vValue;
			else if ( $iArrayDimension == 2 )
				$aKeys = ( array ) $this->getCorrespondingArrayValue( $vValue, $sKey, false );
				
			return array_keys( $aKeys, true );
		
		}
	
		/**
		 * A helper function for the above getTaxonomyChecklistField() method.
		 * 
		 * @since			2.1.1
		 * 
		 */
		private function getLabelFromTaxonomySlug( $sTaxonomySlug ) {
			
			$oTaxonomy = get_taxonomy( $sTaxonomySlug );
			return isset( $oTaxonomy->label )
				? $oTaxonomy->label
				: null;
			
		}
	
}
endif;
if ( ! class_exists( 'AdminPageFramework_InputFieldType_submit' ) ) :
/**
 * Defines the submit field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_submit extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(		
			'class_attribute'					=> 'button button-primary',
			'redirect_url'							=> null,
			'links'								=> null,
			'is_reset'							=> null,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 		
		"/* Submit Buttons */
		.admin-page-framework-field input[type='submit'] {
			margin-bottom: 0.5em;
		}" . PHP_EOL;		
	}
	
	/**
	 * Returns the output of the field type.
	 * @since			2.1.5			Moved from AdminPageFramework_InputField.
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		

		
		$vValue = $this->getInputFieldValueFromLabel( $aField );
		$sFieldNameFlat = $this->getInputFieldNameFlat( $aField );
		foreach( ( array ) $vValue as $sKey => $sValue ) {
			$sRedirectURL = $this->getCorrespondingArrayValue( $aField['redirect_url'], $sKey, $_aDefaultKeys['redirect_url'] );
			$sLinkURL = $this->getCorrespondingArrayValue( $aField['links'], $sKey, $_aDefaultKeys['links'] );
			$sResetKey = $this->getCorrespondingArrayValue( $aField['is_reset'], $sKey, $_aDefaultKeys['is_reset'] );
			$bResetConfirmed = $this->checkConfirmationDisplayed( $sResetKey, $sFieldNameFlat ); 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					// embed the field id and input id
					. "<input type='hidden' "
						. "name='__submit[{$sTagID}_{$sKey}][input_id]' "
						. "value='{$sTagID}_{$sKey}' "
					. "/>"
					. "<input type='hidden' "
						. "name='__submit[{$sTagID}_{$sKey}][field_id]' "
						. "value='{$aField['field_id']}' "
					. "/>"		
					. "<input type='hidden' "
						. "name='__submit[{$sTagID}_{$sKey}][name]' "
						. "value='{$sFieldNameFlat}" . ( is_array( $vValue ) ? "|{$sKey}'" : "'" )
					. "/>" 						
					// for the redirect_url key
					. ( $sRedirectURL 
						? "<input type='hidden' "
							. "name='__redirect[{$sTagID}_{$sKey}][url]' "
							. "value='" . $sRedirectURL . "' "
						. "/>" 
						. "<input type='hidden' "
							. "name='__redirect[{$sTagID}_{$sKey}][name]' "
							. "value='{$sFieldNameFlat}" . ( is_array( $vValue ) ? "|{$sKey}" : "'" )
						. "/>" 
						: "" 
					)
					// for the links key
					. ( $sLinkURL 
						? "<input type='hidden' "
							. "name='__link[{$sTagID}_{$sKey}][url]' "
							. "value='" . $sLinkURL . "' "
						. "/>"
						. "<input type='hidden' "
							. "name='__link[{$sTagID}_{$sKey}][name]' "
							. "value='{$sFieldNameFlat}" . ( is_array( $vValue ) ? "|{$sKey}'" : "'" )
						. "/>" 
						: "" 
					)
					// for the is_reset key
					. ( $sResetKey && ! $bResetConfirmed
						? "<input type='hidden' "
							. "name='__reset_confirm[{$sTagID}_{$sKey}][key]' "
							. "value='" . $sFieldNameFlat . "' "
						. "/>"
						. "<input type='hidden' "
							. "name='__reset_confirm[{$sTagID}_{$sKey}][name]' "
							. "value='{$sFieldNameFlat}" . ( is_array( $vValue ) ? "|{$sKey}'" : "'" )
						. "/>" 
						: ""
					)
					. ( $sResetKey && $bResetConfirmed
						? "<input type='hidden' "
							. "name='__reset[{$sTagID}_{$sKey}][key]' "
							. "value='" . $sResetKey . "' "
						. "/>"
						. "<input type='hidden' "
							. "name='__reset[{$sTagID}_{$sKey}][name]' "
							. "value='{$sFieldNameFlat}" . ( is_array( $vValue ) ? "|{$sKey}'" : "'" )
						. "/>" 
						: ""
					)
					. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
					. "<span class='admin-page-framework-input-button-container admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<input "
							. "id='{$sTagID}_{$sKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "type='{$aField['type']}' "	// submit
							. "name=" . ( is_array( $aField['label'] ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
							. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, $this->oMsg->__( 'submit' ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
				. "</div>" // end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-submit' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";		
	
	}
		/**
		 * A helper function for the above getSubmitField() that checks if a reset confirmation message has been displayed or not when the is_reset key is set.
		 * 
		 */
		private function checkConfirmationDisplayed( $sResetKey, $sFlatFieldName ) {
				
			if ( ! $sResetKey ) return false;
			
			$bResetConfirmed =  get_transient( md5( "reset_confirm_" . $sFlatFieldName ) ) !== false 
				? true
				: false;
			
			if ( $bResetConfirmed )
				delete_transient( md5( "reset_confirm_" . $sFlatFieldName ) );
				
			return $bResetConfirmed;
			
		}

	/*
	 *	Shared Methods 
	 */
	/**
	 * Retrieves the field name attribute whose dimensional elements are delimited by the pile character.
	 * 
	 * Instead of [] enclosing array elements, it uses the pipe(|) to represent the multi dimensional array key.
	 * This is used to create a reference the submit field name to determine which button is pressed.
	 * 
	 * @remark			Used by the import and submit field types.
	 * @since			2.0.0
	 * @since			2.1.5			Made the parameter mandatory. Changed the scope to protected from private. Moved from AdminPageFramework_InputField.
	 */ 
	protected function getInputFieldNameFlat( $aField ) {	
	
		return isset( $aField['sOptionKey'] ) // the meta box class does not use the option key
			? "{$aField['sOptionKey']}|{$aField['page_slug']}|{$aField['section_id']}|{$aField['field_id']}"
			: $aField['field_id'];
		
	}			
	/**
	 * Retrieves the input field value from the label.
	 * 
	 * This method is similar to the above <em>getInputFieldValue()</em> but this does not check the stored option value.
	 * It uses the value set to the <var>label</var> key. 
	 * This is for submit buttons including export custom field type that the label should serve as the value.
	 * 
	 * @remark			The submit, import, and export field types use this method.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AdminPageFramwrork_InputField. Changed the scope to protected from private. Removed the second parameter.
	 */ 
	protected function getInputFieldValueFromLabel( $aField ) {	
		
		// If the value key is explicitly set, use it.
		if ( isset( $aField['vValue'] ) ) return $aField['vValue'];
		
		if ( isset( $aField['label'] ) ) return $aField['label'];
		
		// If the default value is set,
		if ( isset( $aField['default'] ) ) return $aField['default'];
		
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_export' ) ) :
/**
 * Defines the export field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_export extends AdminPageFramework_InputFieldType_submit {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'export_data'					=> null,	// ( array or string or object ) This is for the export field type. 			
			'export_format'					=> 'array',	// ( array or string )	for the export field type. Do not set a default value here. Currently array, json, and text are supported.
			'export_file_name'				=> null,	// ( array or string )	for the export field type. Do not set a default value here.
			'class_attribute'				=> 'button button-primary',	// ( array or string )	
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * @since			2.1.5				Moved from the AdminPageFramework_InputField class. The name was changed from getHiddenField().
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
				
		$vValue = $this->getInputFieldValueFromLabel( $aField );
		
		// If vValue is not an array and the export data set, set the transient. ( it means single )
		if ( isset( $aField['export_data'] ) && ! is_array( $vValue ) )
			set_transient( md5( "{$aField['sClassName']}_{$aField['field_id']}" ), $aField['export_data'], 60*2 );	// 2 minutes.
		
		foreach( ( array ) $vValue as $sKey => $sValue ) {
			
			$sExportFormat = $this->getCorrespondingArrayValue( $aField['export_format'], $sKey, $_aDefaultKeys['export_format'] );
			
			// If it's one of the multiple export buttons and the export data is explictly set for the element, store it as transient in the option table.
			$bIsDataSet = false;
			if ( isset( $vValue[ $sKey ] ) && isset( $aField['export_data'][ $sKey ] ) ) {
				set_transient( md5( "{$aField['sClassName']}_{$aField['field_id']}_{$sKey}" ), $aField['export_data'][ $sKey ], 60*2 );	// 2 minutes.
				$bIsDataSet = true;
			}
			
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					// embed the field id and input id
					. "<input type='hidden' "
						. "name='__export[{$aField['field_id']}][input_id]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='{$sTagID}_{$sKey}' "
					. "/>"
					. "<input type='hidden' "
						. "name='__export[{$aField['field_id']}][field_id]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='{$aField['field_id']}' "
					. "/>"					
					. "<input type='hidden' "
						. "name='__export[{$aField['field_id']}][file_name]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $aField['export_file_name'], $sKey, $this->generateExportFileName( $aField['sOptionKey'], $sExportFormat ) )
					. "' />"
					. "<input type='hidden' "
						. "name='__export[{$aField['field_id']}][format]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . $sExportFormat
					. "' />"				
					. "<input type='hidden' "
						. "name='__export[{$aField['field_id']}][transient]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . ( $bIsDataSet ? 1 : 0 )
					. "' />"				
					. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, '' ) 
					. "<span class='admin-page-framework-input-button-container admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<input "
							. "id='{$sTagID}_{$sKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "type='submit' "	// the export button is a custom submit button.
							// . "name=" . ( is_array( $aField['label'] ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
							. "name='__export[submit][{$aField['field_id']}]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
							. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, $this->oMsg->__( 'export_options' ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
				. "</div>" // end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
									
		}
					
		return "<div class='admin-page-framework-field-export' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";		
	
	}
	
		/**
		 * A helper function for the above method.
		 * 
		 * @remark			Currently only array, text or json is supported.
		 * @since			2.0.0
		 * @since			2.1.5			Moved from the AdminPageFramework_InputField class.
		 */ 
		private function generateExportFileName( $sOptionKey, $sExportFormat='text' ) {
				
			switch ( trim( strtolower( $sExportFormat ) ) ) {
				case 'text':	// for plain text.
					$sExt = "txt";
					break;
				case 'json':	// for json.
					$sExt = "json";
					break;
				case 'array':	// for serialized PHP arrays.
				default:	// for anything else, 
					$sExt = "txt";
					break;
			}		
				
			return $sOptionKey . '_' . date("Ymd") . '.' . $sExt;
			
		}

}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_import' ) ) :
/**
 * Defines the import field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_import extends AdminPageFramework_InputFieldType_submit {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'class_attribute'					=> 'import button button-primary',	// ( array or string )	
			'vAcceptAttribute'					=> 'audio/*|video/*|image/*|MIME_type',
			'class_attributeUpload'				=> 'import',
			'vImportOptionKey'					=> null,	// ( array or string )	for the import field type. The default value is the set option key for the framework.
			'vImportFormat'						=> 'array',	// ( array or string )	for the import field type.
			'vMerge'							=> false,	// ( array or boolean ) [2.1.5+] for the import field
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * @since			2.1.5				Moved from the AdminPageFramework_InputField class. The name was changed from getHiddenField().
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
	
		$vValue = $this->getInputFieldValueFromLabel( $aField );
		$sFieldNameFlat = $this->getInputFieldNameFlat( $aField );
		foreach( ( array ) $vValue as $sKey => $sValue ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					// embed the field id and input id
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][input_id]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='{$sTagID}_{$sKey}' "
					. "/>"
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][field_id]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='{$aField['field_id']}' "
					. "/>"		
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][do_merge]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $aField['vMerge'], $sKey, $_aDefaultKeys['vMerge'] ) . "' "
					. "/>"							
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][import_option_key]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $aField['vImportOptionKey'], $sKey, $aField['sOptionKey'] )
					. "' />"
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][format]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $aField['vImportFormat'], $sKey, $_aDefaultKeys['vImportFormat'] )	// array, text, or json.
					. "' />"			
					. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, '' ) 
					. "<span class='admin-page-framework-input-button-container admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<input "		// upload button
							. "id='{$sTagID}_{$sKey}_file' "
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attributeUpload'], $sKey, $_aDefaultKeys['class_attributeUpload'] ) . "' "
							. "accept='" . $this->getCorrespondingArrayValue( $aField['vAcceptAttribute'], $sKey, $_aDefaultKeys['vAcceptAttribute'] ) . "' "
							. "type='file' "	// upload field. the file type will be stored in $_FILE
							. "name='__import[{$aField['field_id']}]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )				
						. "/>"
						. "<input "		// import button
							. "id='{$sTagID}_{$sKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "type='submit' "	// the import button is a custom submit button.
							. "name='__import[submit][{$aField['field_id']}]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
							. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, $this->oMsg->__( 'import_options' ), true ) . "' "
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, '' )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);		
					
		return "<div class='admin-page-framework-field-import' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_BuiltinInputFieldTypeDefinitions' ) ) :
/**
 * Provides means to define custom input fields not only by the framework but also by the user.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 * @since			2.1.6			Changed the name from AdminPageFramework_InputFieldTypeDefinitions
 */
class AdminPageFramework_BuiltinInputFieldTypeDefinitions  {
	
	/**
	 * Holds the default input field labels
	 * 
	 * @since			2.1.5
	 */
	protected static $aDefaultFieldTypeSlugs = array(
		'default' => array( 'default' ),	// undefined ones will be applied 
		'text' => array( 'text', 'password', 'date', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'time', 'url', 'week' ),
		'number' => array( 'number', 'range' ),
		'textarea' => array( 'textarea' ),
		'radio' => array( 'radio' ),
		'checkbox' => array( 'checkbox' ),
		'select' => array( 'select' ),
		'hidden' => array( 'hidden' ),
		'file' => array( 'file' ),
		'submit' => array( 'submit' ),
		'import' => array( 'import' ),
		'export' => array( 'export' ),
		'image' => array( 'image' ),
		'media' => array( 'media' ),
		'color' => array( 'color' ),
		'taxonomy' => array( 'color' ),
		'posttype' => array( 'posttype' ),
		'size' => array( 'size' ),
	);	
	
	function __construct( &$aFieldTypeDefinitions, $sExtendedClassName, $oMsg ) {
		foreach( self::$aDefaultFieldTypeSlugs as $sFieldTypeSlug => $aSlugs ) {
			$sInstantiatingClassName = "AdminPageFramework_InputFieldType_{$sFieldTypeSlug}";
			if ( class_exists( $sInstantiatingClassName ) ) {
				$oFieldType = new $sInstantiatingClassName( $sExtendedClassName, $sFieldTypeSlug, $oMsg, false );	// passing false for the forth parameter disables auto-registering.
				foreach( $aSlugs as $sSlug )
					$aFieldTypeDefinitions[ $sSlug ] = $oFieldType->getDefinitionArray();
			}
		}
	}
}


endif;

if ( ! class_exists( 'AdminPageFramework_InputField' ) ) :
/**
 * Provides methods for rendering form input fields.
 *
 * @since			2.0.0
 * @since			2.0.1			Added the <em>size</em> type.
 * @extends			AdminPageFramework_Utility
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AdminPageFramework_InputField extends AdminPageFramework_Utility {
		
	/**
	 * Indicates whether the creating fields are for meta box or not.
	 * @since			2.1.2
	 */
	private $bIsMetaBox = false;
		
	protected static $_aStructure_FieldDefinition = array(
		'hfRenderField' => null,
		'hfGetScripts' => null,
		'hfGetStyles' => null,
		'hfGetIEStyles' => null,
		'hfFieldLoader' => null,
		'aEnqueueScripts' => null,
		'aEnqueueStyles' => null,
		'aDefaultKeys' => null,
	);
	
	public function __construct( &$aField, &$aOptions, $aErrors, &$aFieldDefinition, &$oMsg ) {
			
		$this->aField = $aField + $aFieldDefinition['aDefaultKeys'] + self::$_aStructure_FieldDefinition;	// better not to merge recursively because some elements are array by default, not as multiple elements.
		$this->aFieldDefinition = $aFieldDefinition;
		$this->aOptions = $aOptions;
		$this->aErrors = $aErrors ? $aErrors : array();
		$this->oMsg = $oMsg;
			
		$this->sFieldName = $this->getInputFieldName();
		$this->sTagID = $this->getInputTagID( $aField );
		$this->vValue = $this->getInputFieldValue( $aField, $aOptions );
		
		// Global variable
		$GLOBALS['aAdminPageFramework']['aFieldFlags'] = isset( $GLOBALS['aAdminPageFramework']['aFieldFlags'] )
			? $GLOBALS['aAdminPageFramework']['aFieldFlags'] 
			: array();
		
	}	
		
	private function getInputFieldName( $aField=null ) {
		
		$aField = isset( $aField ) ? $aField : $this->aField;
		
		// If the name key is explicitly set, use it
		if ( ! empty( $aField['sName'] ) ) return $aField['sName'];
		
		return isset( $aField['sOptionKey'] ) // the meta box class does not use the option key
			? "{$aField['sOptionKey']}[{$aField['page_slug']}][{$aField['section_id']}][{$aField['field_id']}]"
			: $aField['field_id'];
		
	}

	private function getInputFieldValue( &$aField, $aOptions ) {	

		// If the value key is explicitly set, use it.
		if ( isset( $aField['vValue'] ) ) return $aField['vValue'];
		
		// Check if a previously saved option value exists or not.
		//  for regular setting pages. Meta boxes do not use these keys.
		if ( isset( $aField['page_slug'], $aField['section_id'] ) ) {			
		
			$vValue = $this->getInputFieldValueFromOptionTable( $aField, $aOptions );
			if ( $vValue != '' ) return $vValue;
			
		} 
		// For meta boxes
		else if ( isset( $_GET['action'], $_GET['post'] ) ) {

			$vValue = $this->getInputFieldValueFromPostTable( $_GET['post'], $aField );
			if ( $vValue != '' ) return $vValue;
			
		}
		
		// If the default value is set,
		if ( isset( $aField['default'] ) ) return $aField['default'];
		
	}	
	private function getInputFieldValueFromOptionTable( &$aField, &$aOptions ) {
		
		if ( ! isset( $aOptions[ $aField['page_slug'] ][ $aField['section_id'] ][ $aField['field_id'] ] ) )
			return;
						
		$vValue = $aOptions[ $aField['page_slug'] ][ $aField['section_id'] ][ $aField['field_id'] ];
		
		// Check if it's not an array return it.
		if ( ! is_array( $vValue ) && ! is_object( $vValue ) ) return $vValue;
		
		// If it's an array, check if there is an empty value in each element.
		$default = isset( $aField['default'] ) ? $aField['default'] : array(); 
		foreach ( $vValue as $sKey => &$sElement ) 
			if ( $sElement == '' )
				$sElement = $this->getCorrespondingArrayValue( $default, $sKey, '' );
		
		return $vValue;
			
		
	}	
	private function getInputFieldValueFromPostTable( $iPostID, &$aField ) {
		
		$vValue = get_post_meta( $iPostID, $aField['field_id'], true );
		
		// Check if it's not an array return it.
		if ( ! is_array( $vValue ) && ! is_object( $vValue ) ) return $vValue;
		
		// If it's an array, check if there is an empty value in each element.
		$default = isset( $aField['default'] ) ? $aField['default'] : array(); 
		foreach ( $vValue as $sKey => &$sElement ) 
			if ( $sElement == '' )
				$sElement = $this->getCorrespondingArrayValue( $default, $sKey, '' );
		
		return $vValue;
		
	}
		
	private function getInputTagID( $aField )  {
		
		// For Settings API's form fields should have these key values.
		if ( isset( $aField['section_id'], $aField['field_id'] ) )
			return "{$aField['section_id']}_{$aField['field_id']}";
			
		// For meta box form fields,
		if ( isset( $aField['field_id'] ) ) return $aField['field_id'];
		if ( isset( $aField['sName'] ) ) return $aField['sName'];	// the name key is for the input name attribute but it's better than nothing.
		
		// Not Found - it's not a big deal to have an empty value for this. It's just for the anchor link.
		return '';
			
	}		
	
	
	/** 
	 * Retrieves the input field HTML output.
	 * @since			2.0.0
	 * @since			2.1.6			Moved the repeater script outside the fieldset tag.
	 */ 
	public function getInputField( $sFieldType ) {
		
		// Prepend the field error message.
		$sOutput = isset( $this->aErrors[ $this->aField['section_id'] ][ $this->aField['field_id'] ] )
			? "<span style='color:red;'>*&nbsp;{$this->aField['sError']}" . $this->aErrors[ $this->aField['section_id'] ][ $this->aField['field_id'] ] . "</span><br />"
			: '';		
		
		// Prepeare the field class selector 
		$this->sFieldClassSelector = $this->aField['repeatable']
			? "admin-page-framework-field repeatable"
			: "admin-page-framework-field";
			
		// Add new elements
		$this->aField['sFieldName'] = $this->sFieldName;
		$this->aField['sTagID'] = $this->sTagID;
		$this->aField['sFieldClassSelector'] = $this->sFieldClassSelector;

		// Get the field output.
		$sOutput .= call_user_func_array( 
			$this->aFieldDefinition['hfRenderField'], 
			array( $this->vValue, $this->aField, $this->aOptions, $this->aErrors, $this->aFieldDefinition )
		);			
				
		// Add the description
		$sOutput .= ( isset( $this->aField['description'] ) && trim( $this->aField['description'] ) != '' ) 
			? "<p class='admin-page-framework-fields-description'><span class='description'>{$this->aField['description']}</span></p>"
			: '';
			
		// Add the repeater script
		$sOutput .= $this->aField['repeatable']
			? $this->getRepeaterScript( $this->sTagID, count( ( array ) $this->vValue ) )
			: '';
			
		return $this->getRepeaterScriptGlobal( $this->sTagID )
			. "<fieldset>"
				. "<div class='admin-page-framework-fields'>"
					. $this->aField['sBeforeField'] 
					. $sOutput
					. $this->aField['sAfterField']
				. "</div>"
			. "</fieldset>";
		
	}
	
	/**
	 * Sets or return the flag that indicates whether the creating fields are for meta boxes or not.
	 * 
	 * If the parameter is not set, it will return the stored value. Otherwise, it will set the value.
	 * 
	 * @since			2.1.2
	 */
	public function isMetaBox( $bTrueOrFalse=null ) {
		
		if ( isset( $bTrueOrFalse ) ) 
			$this->bIsMetaBox = $bTrueOrFalse;
			
		return $this->bIsMetaBox;
		
	}
	
	/**
	 * Indicates whether the repeatable fields script is called or not.
	 * 
	 * @since			2.1.3
	 */
	private $bIsRepeatableScriptCalled = false;
	
	/**
	 * Returns the repeatable fields script.
	 * 
	 * @since			2.1.3
	 */
	private function getRepeaterScript( $sTagID, $iFieldCount ) {

		$sAdd = $this->oMsg->__( 'add' );
		$sRemove = $this->oMsg->__( 'remove' );
		$sVisibility = $iFieldCount <= 1 ? " style='display:none;'" : "";
		$sButtons = 
			"<div class='admin-page-framework-repeatable-field-buttons'>"
				. "<a class='repeatable-field-add button-secondary repeatable-field-button button button-small' href='#' title='{$sAdd}' data-id='{$sTagID}'>+</a>"
				. "<a class='repeatable-field-remove button-secondary repeatable-field-button button button-small' href='#' title='{$sRemove}' {$sVisibility} data-id='{$sTagID}'>-</a>"
			. "</div>";

		return
			"<script type='text/javascript'>
				jQuery( document ).ready( function() {
				
					// Adds the buttons
					jQuery( '#{$sTagID} .admin-page-framework-field' ).append( \"{$sButtons}\" );
					
					// Update the fields
					updateAPFRepeatableFields( '{$sTagID}' );
					
				});
			</script>";
		
	}

	/**
	 * Returns the script that will be referred multiple times.
	 * since			2.1.3
	 */
	private function getRepeaterScriptGlobal( $sID ) {

		if ( $this->bIsRepeatableScriptCalled ) return '';
		$this->bIsRepeatableScriptCalled = true;
		return 
		"<script type='text/javascript'>
			jQuery( document ).ready( function() {
				
				// Global function literals
				
				// This function modifies the ids and names of the tags of input, textarea, and relevant tags for repeatable fields.
				updateAPFIDsAndNames = function( element, fIncrementOrDecrement ) {

					var updateID = function( index, name ) {
						
						if ( typeof name === 'undefined' ) {
							return name;
						}
						return name.replace( /_((\d+))(?=(_|$))/, function ( fullMatch, n ) {						
							return '_' + ( Number(n) + ( fIncrementOrDecrement == 1 ? 1 : -1 ) );
						});
						
					}
					var updateName = function( index, name ) {
						
						if ( typeof name === 'undefined' ) {
							return name;
						}
						return name.replace( /\[((\d+))(?=\])/, function ( fullMatch, n ) {				
							return '[' + ( Number(n) + ( fIncrementOrDecrement == 1 ? 1 : -1 ) );
						});
						
					}					
				
					element.attr( 'id', function( index, name ) { return updateID( index, name ) } );
					element.find( 'input,textarea' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
					element.find( 'input,textarea' ).attr( 'name', function( index, name ){ return updateName( index, name ) } );
					
					// Color Pickers
					var nodeColorInput = element.find( 'input.input_color' );
					if ( nodeColorInput.length > 0 ) {
						
							var previous_id = nodeColorInput.attr( 'id' );
							
							if ( fIncrementOrDecrement > 0 ) {	// Add
					
								// For WP 3.5+
								var nodeNewColorInput = nodeColorInput.clone();	// re-clone without bind events.
								
								// For WP 3.4.x or below
								var sInputValue = nodeNewColorInput.val() ? nodeNewColorInput.val() : 'transparent';
								var sInputStyle = sInputValue != 'transparent' && nodeNewColorInput.attr( 'style' ) ? nodeNewColorInput.attr( 'style' ) : '';
								
								nodeNewColorInput.val( sInputValue );	// set the default value	
								nodeNewColorInput.attr( 'style', sInputStyle );	// remove the background color set to the input field ( for WP 3.4.x or below )						 
								
								var nodeFarbtastic = element.find( '.colorpicker' );
								var nodeNewFarbtastic = nodeFarbtastic.clone();	// re-clone without bind elements.
								
								// Remove the old elements
								nodeIris = jQuery( '#' + previous_id ).closest( '.wp-picker-container' );	
								if ( nodeIris.length > 0 ) {	// WP 3.5+
									nodeIris.remove();	
								} else {
									jQuery( '#' + previous_id ).remove();	// WP 3.4.x or below
									element.find( '.colorpicker' ).remove();	// WP 3.4.x or below
								}
							
								// Add the new elements
								element.prepend( nodeNewFarbtastic );
								element.prepend( nodeNewColorInput );
								
							}
							
							element.find( '.colorpicker' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
							element.find( '.colorpicker' ).attr( 'rel', function( index, name ){ return updateID( index, name ) } );					

							// Renew the color picker script
							var cloned_id = element.find( 'input.input_color' ).attr( 'id' );
							registerAPFColorPickerField( cloned_id );					
					
					}

					// Image uploader buttons and image preview elements
					image_uploader_button = element.find( '.select_image' );
					if ( image_uploader_button.length > 0 ) {
						var previous_id = element.find( '.image-field input' ).attr( 'id' );
						image_uploader_button.attr( 'id', function( index, name ){ return updateID( index, name ) } );
						element.find( '.image_preview' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
						element.find( '.image_preview img' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
					
						if ( jQuery( image_uploader_button ).data( 'uploader_type' ) == '1' ) {	// for Wordpress 3.5 or above
							var fExternalSource = jQuery( image_uploader_button ).attr( 'data-enable_external_source' );
							setAPFImageUploader( previous_id, true, fExternalSource );	
						}						
					}
					
					// Media uploader buttons
					media_uploader_button = element.find( '.select_media' );
					if ( media_uploader_button.length > 0 ) {
						var previous_id = element.find( '.media-field input' ).attr( 'id' );
						media_uploader_button.attr( 'id', function( index, name ){ return updateID( index, name ) } );
					
						if ( jQuery( media_uploader_button ).data( 'uploader_type' ) == '1' ) {	// for Wordpress 3.5 or above
							var fExternalSource = jQuery( media_uploader_button ).attr( 'data-enable_external_source' );
							setAPFMediaUploader( previous_id, true, fExternalSource );	
						}						
					}
					
					// Date pickers - somehow it needs to destroy the both previous one and the added one and assign the new date pickers 
					var date_picker_script = element.find( 'script.date-picker-enabler-script' );
					if ( date_picker_script.length > 0 ) {
						var previous_id = date_picker_script.attr( 'data-id' );
						date_picker_script.attr( 'data-id', function( index, name ){ return updateID( index, name ) } );

						jQuery( '#' + date_picker_script.attr( 'data-id' ) ).datepicker( 'destroy' ); 
						jQuery( '#' + date_picker_script.attr( 'data-id' ) ).datepicker({
							dateFormat : date_picker_script.attr( 'data-date_format' )
						});						
						jQuery( '#' + previous_id ).datepicker( 'destroy' ); //here
						jQuery( '#' + previous_id ).datepicker({
							dateFormat : date_picker_script.attr( 'data-date_format' )
						});												
					}				
									
				}
				
				// This function is called from the updateAPFRepeatableFields() and from the media uploader for multiple file selections.
				addAPFRepeatableField = function( sFieldContainerID ) {	

					var field_container = jQuery( '#' + sFieldContainerID );
					var field_delimiter_id = sFieldContainerID.replace( 'field-', 'delimiter-' );
					var field_delimiter = field_container.siblings( '#' + field_delimiter_id );
					
					var field_new = field_container.clone( true );
					var delimiter_new = field_delimiter.clone( true );
					var target_element = ( jQuery( field_delimiter ).length ) ? field_delimiter : field_container;
			
					field_new.find( 'input,textarea' ).val( '' );	// empty the value		
					field_new.find( '.image_preview' ).hide();					// for the image field type, hide the preview element
					field_new.find( '.image_preview img' ).attr( 'src', '' );	// for the image field type, empty the src property for the image uploader field
					delimiter_new.insertAfter( target_element );	// add the delimiter
					field_new.insertAfter( target_element );		// add the cloned new field element

					// Increment the names and ids of the next following siblings.
					target_element.nextAll().each( function() {
						updateAPFIDsAndNames( jQuery( this ), true );
					});

					var remove_buttons =  field_container.closest( '.admin-page-framework-fields' ).find( '.repeatable-field-remove' );
					if ( remove_buttons.length > 1 ) 
						remove_buttons.show();				
					
					// Return the newly created element
					return field_new;
					
				}
				
				updateAPFRepeatableFields = function( sID ) {
				
					// Add button behaviour
					jQuery( '#' + sID + ' .repeatable-field-add' ).click( function() {
						
						var field_container = jQuery( this ).closest( '.admin-page-framework-field' );
						addAPFRepeatableField( field_container.attr( 'id' ) );
						return false;
						
					});		
					
					// Remove button behaviour
					jQuery( '#' + sID + ' .repeatable-field-remove' ).click( function() {
						
						// Need to remove two elements: the field container and the delimiter element.
						var field_container = jQuery( this ).closest( '.admin-page-framework-field' );
						var field_container_id = field_container.attr( 'id' );				
						var field_delimiter_id = field_container_id.replace( 'field-', 'delimiter-' );
						var field_delimiter = field_container.siblings( '#' + field_delimiter_id );
						var target_element = ( jQuery( field_delimiter ).length ) ? field_delimiter : field_container;

						// Decrement the names and ids of the next following siblings.
						target_element.nextAll().each( function() {
							updateAPFIDsAndNames( jQuery( this ), false );	// the second parameter value indicates it's for decrement.
						});

						field_delimiter.remove();
						field_container.remove();
						
						var fieldsCount = jQuery( '#' + sID + ' .repeatable-field-remove' ).length;
						if ( fieldsCount == 1 ) {
							jQuery( '#' + sID + ' .repeatable-field-remove' ).css( 'display', 'none' );
						}
						return false;
					});
									
				}
			});
		</script>";
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_WalkerTaxonomyChecklist' ) ) :
/**
 * Provides methods for rendering taxonomy check lists.
 * 
 * Used for the wp_list_categories() function to render category hierarchical checklist.
 * 
 * @see				Walker : wp-includes/class-wp-walker.php
 * @see				Walker_Category : wp-includes/category-template.php
 * @since			2.0.0
 * @since			2.1.5			Added the sTagID key to the argument array. Changed the format of 'id' and 'for' attribute of the input and label tags.
 * @extends			Walker_Category
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AdminPageFramework_WalkerTaxonomyChecklist extends Walker_Category {
		
	function start_el( &$sOutput, $oCategory, $iDepth=0, $aArgs=array(), $iCurrentObjectID=0 ) {
		
		/*	
		 	$aArgs keys:
			'show_option_all' => '', 
			'show_option_none' => __('No categories'),
			'orderby' => 'name', 
			'order' => 'ASC',
			'style' => 'list',
			'show_count' => 0, 
			'hide_empty' => 1,
			'use_desc_for_title' => 1, 
			'child_of' => 0,
			'feed' => '', 
			'feed_type' => '',
			'feed_image' => '', 
			'exclude' => '',
			'exclude_tree' => '', 
			'current_category' => 0,
			'hierarchical' => true, 
			'title_li' => __( 'Categories' ),
			'echo' => 1, 
			'depth' => 0,
			'taxonomy' => 'category'	// 'post_tag' or any other registered taxonomy slug will work.

			[class] => categories
			[has_children] => 1
		*/
		
		$aArgs = $aArgs + array(
			'name' 		=> null,
			'disabled'	=> null,
			'selected'	=> array(),
			'sTagID'	=> null,
		);
		
		$iID = $oCategory->term_id;
		$sTaxonomy = empty( $aArgs['taxonomy'] ) ? 'category' : $aArgs['taxonomy'];
		$sChecked = in_array( $iID, ( array ) $aArgs['selected'] )  ? 'Checked' : '';
		$sDisabled = $aArgs['disabled'] ? 'disabled="Disabled"' : '';
		$sClass = 'category-list';
		$sID = "{$aArgs['sTagID']}_{$sTaxonomy}_{$iID}";
		$sOutput .= "\n"
			. "<li id='list-{$sID}' $sClass>" 
				. "<label for='{$sID}' class='taxonomy-checklist-label'>"
					. "<input value='0' type='hidden' name='{$aArgs['name']}[{$iID}]' />"
					. "<input id='{$sID}' value='1' type='checkbox' name='{$aArgs['name']}[{$iID}]' {$sChecked} {$sDisabled} />"
					. esc_html( apply_filters( 'the_category', $oCategory->name ) ) 
				. "</label>";	
			// no need to close </li> since it is dealt in end_el().
			
	}
}
endif;

if ( ! class_exists( 'AdminPageFramework_PostType' ) ) :
/**
 * Provides methods for registering custom post types.
 * 
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 * 	<li><code>start_ + extended class name</code> – triggered at the end of the class constructor.</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 * 	<li><code>cell_ + post type + _ + column key</code> – receives the output string for the listing table of the custom post type's post. The first parameter: output string. The second parameter: the post ID.</li>
 * </ul>
 * <h3>Remarks</h3>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p> 
 * 
 * @abstract
 * @since			2.0.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Post Type
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
	* Constructs the class object, AdminPageFramework_PostType.
	* 
	* <h4>Example</h4>
	* <code>new APF_PostType( 
	* 	'apf_posts', 	// post type slug
	* 	array(			// argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
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
	* @param			string			$sPostType			The post type slug.
	* @param			array			$aArgs				The <a href="http://codex.wordpress.org/Function_Reference/register_post_type#Arguments">argument array</a> passed to register_post_type().
	* @param			string			$sCallerPath			The path of the caller script. This is used to retrieve the script information to insert it into the footer. If not set, the framework tries to detect it.
	* @param			string			$sTextDomain			The text domain of the caller script.
	* @return			void
	*/
	public function __construct( $sPostType, $aArgs=array(), $sCallerPath=null, $sTextDomain='admin-page-framework' ) {
		
		// Objects
		$this->oUtil = new AdminPageFramework_Utility;
		$this->oProps = new AdminPageFramework_PostType_Properties( $this );
		$this->oMsg = AdminPageFramework_Messages::instantiate( $sTextDomain );
		$this->oHeadTag = new AdminPageFramework_HeadTag_PostType( $this->oProps );
		$this->oPageLoadInfo = AdminPageFramework_PageLoadInfo_PostType::instantiate( $this->oProps, $this->oMsg );
		
		// Properties
		$this->oProps->sPostType = $this->oUtil->sanitizeSlug( $sPostType );
		$this->oProps->aPostTypeArgs = $aArgs;	// for the argument array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
		$this->oProps->sClassName = get_class( $this );
		$this->oProps->sClassHash = md5( $this->oProps->sClassName );
		$this->oProps->aColumnHeaders = array(
			'cb'			=> '<input type="checkbox" />',		// Checkbox for bulk actions. 
			'title'			=> $this->oMsg->__( 'title' ),		// Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
			'author'		=> $this->oMsg->__( 'author' ), 	// Post author.
			// 'categories'	=> $this->oMsg->__( 'categories' ),	// Categories the post belongs to. 
			// 'tags'		=> $this->oMsg->__( 'tags' ), 		//	Tags for the post. 
			'comments' 		=> '<div class="comment-grey-bubble"></div>', // Number of pending comments. 
			'date'			=> $this->oMsg->__( 'date' ), 		// The date and publish status of the post. 
		);			
		$this->oProps->sCallerPath = $sCallerPath;
		
		add_action( 'init', array( $this, 'registerPostType' ), 999 );	// this is loaded in the front-end as well so should not be admin_init. Also "if ( is_admin() )" should not be used either.
		
		if ( $this->oProps->sPostType != '' && is_admin() ) {			
		
			add_action( 'admin_enqueue_scripts', array( $this, 'disableAutoSave' ) );
			
			// For table columns
			add_filter( "manage_{$this->oProps->sPostType}_posts_columns", array( $this, 'setColumnHeader' ) );
			add_filter( "manage_edit-{$this->oProps->sPostType}_sortable_columns", array( $this, 'setSortableColumns' ) );
			add_action( "manage_{$this->oProps->sPostType}_posts_custom_column", array( $this, 'setColumnCell' ), 10, 2 );
			
			// For filters
			add_action( 'restrict_manage_posts', array( $this, 'addAuthorTableFilter' ) );
			add_action( 'restrict_manage_posts', array( $this, 'addTaxonomyTableFilter' ) );
			add_filter( 'parse_query', array( $this, 'setTableFilterQuery' ) );
			
			// Style
			add_action( 'admin_head', array( $this, 'addStyle' ) );
			
			// Links
			$this->oLink = new AdminPageFramework_Link_PostType( $this->oProps->sPostType, $this->oProps->sCallerPath, $this->oMsg );
			
			add_action( 'wp_loaded', array( $this, 'setUp' ) );
		}
	
		$this->oUtil->addAndDoAction( $this, "{$this->oProps->sPrefix_Start}{$this->oProps->sClassName}" );
		
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
	* 			array(			// argument - for the argument array keys, refer to : http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
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
	* @remark			The user may override this method in their class definition.
	* @remark			A callback for the <em>wp_loaded</em> hook.
	*/
	public function setUp() {}	
	
	/**
	 * Defines the column header items in the custom post listing table.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>manage_{post type}_post)_columns</em> hook.
	 * @remark			The user may override this method in their class definition.
	 * @return			void
	 */ 
	public function setColumnHeader( $aColumnHeaders ) {
		return $this->oProps->aColumnHeaders;
	}	
	
	/**
	 * Defines the sortable column items in the custom post listing table.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>manage_edit-{post type}_sortable_columns</em> hook.
	 * @remark			The user may override this method in their class definition.
	 */ 
	public function setSortableColumns( $aColumns ) {
		return $this->oProps->aColumnSortable;
	}
	
	/*
	 * Front-end methods
	 */
	/**
	* Enables or disables the auto-save feature in the custom post type's post submission page.
	* 
	* <h4>Example</h4>
	* <code>$this->setAutoSave( false );</code>
	* 
	* @since			2.0.0
	* @param			boolean			$bEnableAutoSave			If true, it enables the auto-save; othwerwise, it disables it.
	* return			void
	*/ 
	protected function setAutoSave( $bEnableAutoSave=True ) {
		$this->oProps->bEnableAutoSave = $bEnableAutoSave;		
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
	* @param			string			$sTaxonomySlug			The taxonomy slug.
	* @param			array			$aArgs					The taxonomy argument array passed to the second parameter of the <a href="http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments">register_taxonomy()</a> function.
	* @return			void
	*/ 
	protected function addTaxonomy( $sTaxonomySlug, $aArgs ) {
		
		$sTaxonomySlug = $this->oUtil->sanitizeSlug( $sTaxonomySlug );
		$this->oProps->aTaxonomies[ $sTaxonomySlug ] = $aArgs;	
		if ( isset( $aArgs['show_table_filter'] ) && $aArgs['show_table_filter'] )
			$this->oProps->aTaxonomyTableFilters[] = $sTaxonomySlug;
		if ( isset( $aArgs['show_in_sidebar_menus'] ) && ! $aArgs['show_in_sidebar_menus'] )
			$this->oProps->aTaxonomyRemoveSubmenuPages[ "edit-tags.php?taxonomy={$sTaxonomySlug}&amp;post_type={$this->oProps->sPostType}" ] = "edit.php?post_type={$this->oProps->sPostType}";
				
		if ( count( $this->oProps->aTaxonomyTableFilters ) == 1 )
			add_action( 'init', array( $this, 'registerTaxonomies' ) );	// the hook should not be admin_init because taxonomies need to be accessed in regular pages.
		if ( count( $this->oProps->aTaxonomyRemoveSubmenuPages ) == 1 )
			add_action( 'admin_menu', array( $this, 'removeTexonomySubmenuPages' ), 999 );		
			
	}	

	/**
	* Sets whether the author dropdown filter is enabled/disabled in the post type post list table.
	* 
	* <h4>Example</h4>
	* <code>this->setAuthorTableFilter( true );</code>
	* 
	* @since			2.0.0
	* @param			boolean			$bEnableAuthorTableFileter			If true, it enables the author filter; otherwise, it disables it.
	* @return			void
	*/ 
	protected function setAuthorTableFilter( $bEnableAuthorTableFileter=false ) {
		$this->oProps->bEnableAuthorTableFileter = $bEnableAuthorTableFileter;
	}
	
	/**
	 * Sets the post type arguments.
	 * 
	 * This is only necessary if it is not set to the constructor.
	 * 
	 * @since			2.0.0
	 * @see				http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
	 * @param			array			$aArgs			The <a href="http://codex.wordpress.org/Function_Reference/register_post_type#Arguments">array of arguments</a> to be passed to the second parameter of the <em>register_post_type()</em> function.
	 * @return			void
	 */ 
	protected function setPostTypeArgs( $aArgs ) {
		$this->oProps->aPostTypeArgs = $aArgs;
	}
	
	/**
	 * Sets the given HTML text into the footer on the left hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoLeft( '&lt;br /&gt;Custom Text on the left hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @param			string			$sHTML			The HTML code to insert.
	 * @param			boolean			$bAppend			If true, the text will be appended; otherwise, it will replace the default text.
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
	 * <code>$this->setFooterInfoRight( '&lt;br /&gt;Custom Text on the right hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @param			string			$sHTML			The HTML code to insert.
	 * @param			boolean			$bAppend			If true, the text will be appended; otherwise, it will replace the default text.
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
			#icon-edit.icon32.icon32-posts-" . $this->oProps->sPostType . " {
				background: url('" . $sSRC . "') no-repeat;
				background-size: 32px 32px;
			}			
		";		
		
	}
	
	/*
	 * Callback functions
	 */
	public function addStyle() {

		if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] != $this->oProps->sPostType )
			return;

		// If the screen icon url is specified
		if ( isset( $this->oProps->aPostTypeArgs['screen_icon'] ) && $this->oProps->aPostTypeArgs['screen_icon'] )
			$this->oProps->sStyle = $this->getStylesForPostTypeScreenIcon( $this->oProps->aPostTypeArgs['screen_icon'] );
			
		$this->oProps->sStyle = $this->oUtil->addAndApplyFilters( $this, "style_{$this->oProps->sClassName}", $this->oProps->sStyle );
		
		// Print out the filtered styles.
		if ( ! empty( $this->oProps->sStyle ) )
			echo "<style type='text/css' id='admin-page-framework-style-post-type'>" 
				. $this->oProps->sStyle
				. "</style>";			
		
	}
	
	public function registerPostType() {

		register_post_type( $this->oProps->sPostType, $this->oProps->aPostTypeArgs );
		
		$bIsPostTypeSet = get_option( "post_type_rules_flased_{$this->oProps->sPostType}" );
		if ( $bIsPostTypeSet !== true ) {
		   flush_rewrite_rules( false );
		   update_option( "post_type_rules_flased_{$this->oProps->sPostType}", true );
		}

	}	

	public function registerTaxonomies() {
		
		foreach( $this->oProps->aTaxonomies as $sTaxonomySlug => $aArgs ) 
			register_taxonomy(
				$sTaxonomySlug,
				$this->oProps->sPostType,
				$aArgs	// for the argument array keys, refer to: http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
			);	
			
	}
	
	public function removeTexonomySubmenuPages() {
		
		foreach( $this->oProps->aTaxonomyRemoveSubmenuPages as $sSubmenuPageSlug => $sTopLevelPageSlug )
			remove_submenu_page( $sTopLevelPageSlug, $sSubmenuPageSlug );
		
	}
	
	public function disableAutoSave() {
		
		if ( $this->oProps->bEnableAutoSave ) return;
		if ( $this->oProps->sPostType != get_post_type() ) return;
		wp_dequeue_script( 'autosave' );
			
	}
	
	/**
	 * Adds a dorpdown list to filter posts by author, placed above the post type listing table.
	 */ 
	public function addAuthorTableFilter() {
		
		if ( ! $this->oProps->bEnableAuthorTableFileter ) return;
		
		if ( ! ( isset( $_GET['post_type'] ) && post_type_exists( $_GET['post_type'] ) 
			&& in_array( strtolower( $_GET['post_type'] ), array( $this->oProps->sPostType ) ) ) )
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
	 */ 
	public function addTaxonomyTableFilter() {
		
		if ( $GLOBALS['typenow'] != $this->oProps->sPostType ) return;
		
		// If there is no post added to the post type, do nothing.
		$oPostCount = wp_count_posts( $this->oProps->sPostType );
		if ( $oPostCount->publish + $oPostCount->future + $oPostCount->draft + $oPostCount->pending + $oPostCount->private + $oPostCount->trash == 0 )
			return;
		
		foreach ( get_object_taxonomies( $GLOBALS['typenow'] ) as $sTaxonomySulg ) {
			
			if ( ! in_array( $sTaxonomySulg, $this->oProps->aTaxonomyTableFilters ) ) continue;
			
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
	public function setTableFilterQuery( $oQuery=null ) {
		
		if ( 'edit.php' != $GLOBALS['pagenow'] ) return $oQuery;
		
		if ( ! isset( $GLOBALS['typenow'] ) ) return $oQuery;
		
		foreach ( get_object_taxonomies( $GLOBALS['typenow'] ) as $sTaxonomySlug ) {
			
			if ( ! in_array( $sTaxonomySlug, $this->oProps->aTaxonomyTableFilters ) ) continue;
			
			$sVar = &$oQuery->query_vars[ $sTaxonomySlug ];
			if ( ! isset( $sVar ) ) continue;
			
			$oTerm = get_term_by( 'id', $sVar, $sTaxonomySlug );
			if ( is_object( $oTerm ) )
				$sVar = $oTerm->slug;

		}
		return $oQuery;
		
	}
	
	public function setColumnCell( $sColumnTitle, $iPostID ) { 
	
		// foreach ( $this->oProps->aColumnHeaders as $sColumnHeader => $sColumnHeaderTranslated ) 
			// if ( $sColumnHeader == $sColumnTitle ) 
			
		// cell_{post type}_{custom column key}
		echo $this->oUtil->addAndApplyFilter( $this, "{$this->oProps->sPrefix_Cell}{$this->oProps->sPostType}_{$sColumnTitle}", $sCell='', $iPostID );
				  
	}
	
	/*
	 * Magic method - this prevents PHP's not-a-valid-callback errors.
	*/
	public function __call( $sMethodName, $aArgs=null ) {	
		if ( substr( $sMethodName, 0, strlen( $this->oProps->sPrefix_Cell ) ) == $this->oProps->sPrefix_Cell ) return $aArgs[0];
		if ( substr( $sMethodName, 0, strlen( "style_" ) )== "style_" ) return $aArgs[0];
	}
	
}
endif;


if ( ! class_exists( 'AdminPageFramework_MetaBox' ) ) :
/**
 * Provides methods for creating meta boxes.
 *
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 * 	<li><code>start_ + extended class name</code> – triggered at the end of the class constructor.</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 * 	<li><code>extended class name + _ + field_ + field ID</code> – receives the form input field output of the given input field ID. The first parameter: output string. The second parameter: the array of option.</li>
 * 	<li><code>style_ + extended class name</code> –  receives the output of the CSS rules applied to the pages of the associated post types with the meta box.</li>
 * 	<li><code>script_ + extended class name</code> – receives the output of the JavaScript scripts applied to the pages of the associated post types with the meta box.</li>
 * 	<li><code>validation_ + extended class name</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * </ul>
 * <h3>Remarks</h3>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p>  
 * 
 * @abstract
 * @since			2.0.0
 * @use				AdminPageFramework_Utility
 * @use				AdminPageFramework_Messages
 * @use				AdminPageFramework_Debug
 * @use				AdminPageFramework_Properties
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Meta Box
 */
abstract class AdminPageFramework_MetaBox extends AdminPageFramework_Help_MetaBox {
	
	// Objects
	/**
	* @internal
	* @since			2.0.0
	*/ 	
	protected $oDebug;
	/**
	* @internal
	* @since			2.0.0
	*/ 		
	protected $oUtil;
	/**
	* @since			2.0.0
	* @internal
	*/ 		
	protected $oMsg;
	/**
	 * @since			2.1.5
	 * @internal
	 */
	protected $oHeadTag;
	
	/**
	 * Constructs the class object instance of AdminPageFramework_MetaBox.
	 * 
	 * @see				http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
	 * @since			2.0.0
	 * @param			string			$sMetaBoxID			The meta box ID.
	 * @param			string			$sTitle				The meta box title.
	 * @param			string|array	$vPostTypes				( optional ) The post type(s) that the meta box is associated with.
	 * @param			string			$sContext				( optional ) The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side') Default: normal.
	 * @param			string			$sPriority			( optional ) The priority within the context where the boxes should show ('high', 'core', 'default' or 'low') Default: default.
	 * @param			string			$sCapability			( optional ) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the meta box. Default: edit_posts.
	 * @param			string			$sTextDomain			( optional ) The text domain applied to the displayed text messages. Default: admin-page-framework.
	 * @return			void
	 */ 
	function __construct( $sMetaBoxID, $sTitle, $vPostTypes=array( 'post' ), $sContext='normal', $sPriority='default', $sCapability='edit_posts', $sTextDomain='admin-page-framework' ) {
		
		// Objects
		$this->oUtil = new AdminPageFramework_Utility;
		$this->oMsg = AdminPageFramework_Messages::instantiate( $sTextDomain );
		$this->oDebug = new AdminPageFramework_Debug;
		$this->oProps = new AdminPageFramework_MetaBox_Properties( $this );
		$this->oHeadTag = new AdminPageFramework_HeadTag_MetaBox( $this->oProps );
			
		// Properties
		$this->oProps->sMetaBoxID = $this->oUtil->sanitizeSlug( $sMetaBoxID );
		$this->oProps->sTitle = $sTitle;
		$this->oProps->aPostTypes = is_string( $vPostTypes ) ? array( $vPostTypes ) : $vPostTypes;	
		$this->oProps->sContext = $sContext;	//  'normal', 'advanced', or 'side' 
		$this->oProps->sPriority = $sPriority;	// 	'high', 'core', 'default' or 'low'
		$this->oProps->sClassName = get_class( $this );
		$this->oProps->sClassHash = md5( $this->oProps->sClassName );
		$this->oProps->sCapability = $sCapability;
				
		if ( is_admin() ) {
			
			add_action( 'wp_loaded', array( $this, 'replyToLoadDefaultFieldTypeDefinitions' ), 10 );	// should be loaded before the setUp() method.
			add_action( 'wp_loaded', array( $this, 'setUp' ), 11 );
			
			add_action( 'add_meta_boxes', array( $this, 'addMetaBox' ) );
			add_action( 'save_post', array( $this, 'saveMetaBoxFields' ) );
						
			// the contextual help pane
			add_action( "load-{$GLOBALS['pagenow']}", array( $this, 'registerHelpTabTextForMetaBox' ), 20 );	
	
			if ( in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php', ) ) ) 
				add_filter( 'gettext', array( $this, 'replaceThickBoxText' ) , 1, 2 );		
	
		}
		
		// Hooks
		$this->oUtil->addAndDoAction( $this, "{$this->oProps->sPrefixStart}{$this->oProps->sClassName}" );
		
	}
	
	/**
	 * Loads the default field type definition.
	 * 
	 * @since			2.1.5
	 */
	public function replyToLoadDefaultFieldTypeDefinitions() {
		
		// This class adds filters for the field type definitions so that framework's default field types will be added.
		new AdminPageFramework_BuiltinInputFieldTypeDefinitions( $this->oProps->aFieldTypeDefinitions, $this->oProps->sClassName, $this->oMsg );		
		$this->oProps->aFieldTypeDefinitions = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
			$this,
			'field_types_' . $this->oProps->sClassName,	// 'field_types_' . {extended class name}
			$this->oProps->aFieldTypeDefinitions
		);				
		
	}

	
	/**
	* The method for all necessary set-ups.
	* 
	* <h4>Example</h4>
	* <code>	public function setUp() {		
	* 	$this->addSettingFields(
	* 		array(
	* 			'field_id'		=> 'sample_metabox_text_field',
	* 			'title'			=> 'Text Input',
	* 			'description'	=> 'The description for the field.',
	* 			'type'			=> 'text',
	* 		),
	* 		array(
	* 			'field_id'		=> 'sample_metabox_textarea_field',
	* 			'title'			=> 'Textarea',
	* 			'description'	=> 'The description for the field.',
	* 			'type'			=> 'textarea',
	* 			'default'			=> 'This is a default text.',
	* 		)
	* 	);		
	* }</code>
	* 
	* @abstract
	* @since			2.0.0
	* @remark			The user may override this method.
	* @return			void
	*/	 
	public function setUp() {}
	
	/**
	* Adds the given field array items into the field array property. 
	* 
	* <h4>Example</h4>
	* <code>    $this->addSettingFields(
    *     array(
    *         'field_id'        => 'sample_metabox_text_field',
    *         'title'          => 'Text Input',
    *         'description'    => 'The description for the field.',
    *         'type'           => 'text',
    *     ),
    *     array(
    *         'field_id'        => 'sample_metabox_textarea_field',
    *         'title'          => 'Textarea',
    *         'description'    => 'The description for the field.',
    *         'type'           => 'textarea',
    *         'default'          => 'This is a default text.',
    *     )
    * );</code>
	* 
	* @since			2.0.0
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			array			$aField1			The field array.
	* @param			array			$aField2			Another field array.
	* @param			array			$_and_more			Add more fields arrays as many as necessary to the next parameters.
	* @return			void
	*/ 
	protected function addSettingFields( $aField1, $aField2=null, $_and_more=null ) {

		foreach( func_get_args() as $aField ) 
			$this->addSettingField( $aField );
		
	}	
	/**
	* Adds the given field array items into the field array property.
	* 
	* Itentical to the addSettingFields() method except that this method does not accept enumerated parameters. 
	* 
	* @since			2.1.2
	* @return			void
	* @remark			The user may use this method in their extended class definition.
	*/		
	protected function addSettingField( $aField ) {

		if ( ! is_array( $aField ) ) return;
		
		$aField = $aField + AdminPageFramework_MetaBox_Properties::$_aStructure_Field;	// avoid undefined index warnings.
		
		// Sanitize the IDs since they are used as a callback method name.
		$aField['field_id'] = $this->oUtil->sanitizeSlug( $aField['field_id'] );
		
		// Check the mandatory keys' values are set.
		if ( ! isset( $aField['field_id'], $aField['type'] ) ) return;	// these keys are necessary.
						
		// If a custom condition is set and it's not true, skip.
		if ( ! $aField['fIf'] ) return;
							
		// Load head tag elements for fields.
		if ( 
			in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) 
			&& ( 
				( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->oProps->aPostTypes ) )
				|| ( isset( $_GET['post'], $_GET['action'] ) && in_array( get_post_type( $_GET['post'] ), $this->oProps->aPostTypes ) )		// edit post page
			)
		) {
			// Set relevant scripts and styles for the input field.
			$this->setFieldHeadTagElements( $aField );

		}
		
		// For the contextual help pane,
		if ( 
			in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) 
			&& ( 
				( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->oProps->aPostTypes ) )
				|| ( isset( $_GET['post'], $_GET['action'] ) && in_array( get_post_type( $_GET['post'] ), $this->oProps->aPostTypes ) )		// edit post page
			)
			&& $aField['help']
		) {
			
			$this->addHelpTextForFormFields( $aField['title'], $aField['help'], $aField['helpAside'] );
							
		}
	
		$this->oProps->aFields[ $aField['field_id'] ] = $aField;
	
	}
		/**
		 * Sets the given field type's enqueuing scripts and styles.
		 * 
		 * A helper function for the above registerSettings() method.
		 * 
		 * @since			2.1.5
		 */
		private function setFieldHeadTagElements( $aField ) {
			
			$sFieldType = $aField['type'];
			
			// Set the global flag to indicate whether the elements are already added and enqueued.
			if ( isset( $GLOBALS['aAdminPageFramework']['aFieldFlags'][ $sFieldType ] ) && $GLOBALS['aAdminPageFramework']['aFieldFlags'][ $sFieldType ] ) return;
			$GLOBALS['aAdminPageFramework']['aFieldFlags'][ $sFieldType ] = true;

			// If the field type is not defined, return.
			if ( ! isset( $this->oProps->aFieldTypeDefinitions[ $sFieldType ] ) ) return;

			if ( is_callable( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfFieldLoader'] ) )
				call_user_func_array( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfFieldLoader'], array() );		
			
			if ( is_callable( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfGetScripts'] ) )
				$this->oProps->sScript .= call_user_func_array( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfGetScripts'], array() );
				
			if ( is_callable( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfGetStyles'] ) )
				$this->oProps->sStyle .= call_user_func_array( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfGetStyles'], array() );
				
			if ( is_callable( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfGetIEStyles'] ) )
				$this->oProps->sStyleIE .= call_user_func_array( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['hfGetIEStyles'], array() );					

			$this->oHeadTag->enqueueStyles( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueStyles'] );
			$this->oHeadTag->enqueueScripts( $this->oProps->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueScripts'] );
					
		}		

	/**
	 * 
	 * since			2.1.3
	 */
	public function removeMediaLibraryTab( $aTabs ) {
		
		if ( ! isset( $_REQUEST['enable_external_source'] ) ) return $aTabs;
		
		if ( ! $_REQUEST['enable_external_source'] )
			unset( $aTabs['type_url'] );	// removes the From URL tab in the thick box.
		
		return $aTabs;
		
	}

	/**
 	 * Replaces the label text of a button used in the media uploader.
	 * @since			2.0.0
	 * @remark			A callback for the <em>gettext</em> hook.
	 */ 
	public function replaceThickBoxText( $sTranslated, $sText ) {

		// Replace the button label in the media thick box.
		if ( ! in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php' ) ) ) return $sTranslated;
		if ( $sText != 'Insert into Post' ) return $sTranslated;
		if ( $this->oUtil->getQueryValueInURLByKey( wp_get_referer(), 'referrer' ) != 'admin_page_framework' ) return $sTranslated;
		
		if ( isset( $_GET['button_label'] ) ) return $_GET['button_label'];

		return $this->oProps->sThickBoxButtonUseThis ?  $this->oProps->sThickBoxButtonUseThis : $this->oMsg->__( 'use_this_image' );
		
	}
	
	/**
	 * Adds the defined meta box.
	 * 
	 * @since			2.0.0
	 * @remark			uses <em>add_meta_box()</em>.
	 * @remark			A callback for the <em>add_meta_boxes</em> hook.
	 * @return			void
	 */ 
	public function addMetaBox() {
		
		foreach( $this->oProps->aPostTypes as $sPostType ) 
			add_meta_box( 
				$this->oProps->sMetaBoxID, 		// id
				$this->oProps->sTitle, 	// title
				array( $this, 'echoMetaBoxContents' ), 	// callback
				$sPostType,		// post type
				$this->oProps->sContext, 	// context
				$this->oProps->sPriority,	// priority
				$this->oProps->aFields	// argument
			);
			
	}	
	
	/**
	 * Echoes the meta box contents.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>add_meta_box()</em> method.
	 * @param			object			$oPost			The object of the post associated with the meta box.
	 * @param			array			$vArgs			The array of arguments.
	 * @return			void
	 */ 
	public function echoMetaBoxContents( $oPost, $vArgs ) {	
		
		// Use nonce for verification
		$sOut = wp_nonce_field( $this->oProps->sMetaBoxID, $this->oProps->sMetaBoxID, true, false );
		
		// Begin the field table and loop
		$sOut .= '<table class="form-table">';
		$this->setOptionArray( $oPost->ID, $vArgs['args'] );
		
		foreach ( ( array ) $vArgs['args'] as $aField ) {
			
			// Avoid undefined index warnings
			$aField = $aField + AdminPageFramework_MetaBox_Properties::$_aStructure_Field;
			
			// get value of this field if it exists for this post
			$sStoredValue = get_post_meta( $oPost->ID, $aField['field_id'], true );
			$aField['vValue'] = $sStoredValue ? $sStoredValue : $aField['vValue'];
			
			// Check capability. If the access level is not sufficient, skip.
			$aField['sCapability'] = isset( $aField['sCapability'] ) ? $aField['sCapability'] : $this->oProps->sCapability;
			if ( ! current_user_can( $aField['sCapability'] ) ) continue; 			
			
			// Begin a table row. 
			
			// If it's a hidden input type, do now draw a table row
			if ( $aField['type'] == 'hidden' ) {
				$sOut .= "<tr><td style='height: 0; padding: 0; margin: 0; line-height: 0;'>"
					. $this->getFieldOutput( $aField )
					. "</td></tr>";
				continue;
			}
			$sOut .= "<tr>";
			if ( ! $aField['show_inpage_tabTitleColumn'] )
				$sOut .= "<th><label for='{$aField['field_id']}'>"
						. "<a id='{$aField['field_id']}'></a>"
						. "<span title='" . strip_tags( isset( $aField['tip'] ) ? $aField['tip'] : $aField['description'] ) . "'>"
						. $aField['title'] 
						. "</span>"
						. "</label></th>";		
			$sOut .= "<td>";
			$sOut .= $this->getFieldOutput( $aField );
			$sOut .= "</td>";
			$sOut .= "</tr>";
			
		} // end foreach
		$sOut .= '</table>'; // end table
		echo $sOut;
		
	}
	private function setOptionArray( $iPostID, $aFields ) {
		
		if ( ! is_array( $aFields ) ) return;
		
		foreach( $aFields as $iIndex => $aField ) {
			
			// Avoid undefined index warnings
			$aField = $aField + AdminPageFramework_MetaBox_Properties::$_aStructure_Field;

			$this->oProps->aOptions[ $iIndex ] = get_post_meta( $iPostID, $aField['field_id'], true );
			
		}
	}	
	private function getFieldOutput( $aField ) {

		// Set the input field name which becomes the option key of the custom meta field of the post.
		$aField['sName'] = isset( $aField['sName'] ) ? $aField['sName'] : $aField['field_id'];

		// Render the form field. 		
		$sFieldType = isset( $this->oProps->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] ) && is_callable( $this->oProps->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] )
			? $aField['type']
			: 'default';	// the predefined reserved field type is applied if the parsing field type is not defined(not found).
		$oField = new AdminPageFramework_InputField( $aField, $this->oProps->aOptions, array(), $this->oProps->aFieldTypeDefinitions[ $sFieldType ], $this->oMsg );	// currently the error array is not supported for meta-boxes
		$oField->isMetaBox( true );
		$sFieldOutput = $oField->getInputField( $sFieldType );	// field output
		unset( $oField );	// release the object for PHP 5.2.x or below.
		
		return $this->oUtil->addAndApplyFilters(
			$this,
			array( 
				$this->oProps->sClassName . '_' . 'field_' . $aField['field_id'],	// this filter will be deprecated
				'field_' . $this->oProps->sClassName . '_' . $aField['field_id']	// field_ + {extended class name} + _ {field id}
			),
			$sFieldOutput,
			$aField // the field array
		);		
		
		// return $this->oUtil->addAndApplyFilter(
			// $this,
			// $this->oProps->sClassName . '_' . 'field_' . $aField['field_id'],	// filter: class name + _ + field_ + field id
			// $sFieldOutput,
			// $aField // the field array
		// );	
				
	}
		
	/**
	 * Saves the meta box field data to the associated post. 
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>save_post</em> hook
	 */
	public function saveMetaBoxFields( $iPostID ) {
		
		// Bail if we're doing an auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		
		// If our nonce isn't there, or we can't verify it, bail
		if ( ! isset( $_POST[ $this->oProps->sMetaBoxID ] ) || ! wp_verify_nonce( $_POST[ $this->oProps->sMetaBoxID ], $this->oProps->sMetaBoxID ) ) return;
			
		// Check permissions
		if ( in_array( $_POST['post_type'], $this->oProps->aPostTypes )   
			&& ( ( ! current_user_can( $this->oProps->sCapability, $iPostID ) ) || ( ! current_user_can( $this->oProps->sCapability, $iPostID ) ) )
		) return;

		// Compose an array consisting of the submitted registered field values.
		$aInput = array();
		foreach( $this->oProps->aFields as $aField ) 
			$aInput[ $aField['field_id'] ] = isset( $_POST[ $aField['field_id'] ] ) ? $_POST[ $aField['field_id'] ] : null;
			
		// Prepare the old value array.
		$aOriginal = array();
		foreach ( $aInput as $sFieldID => $v )
			$aOriginal[ $sFieldID ] = get_post_meta( $iPostID, $sFieldID, true );
					
		// Apply filters to the array of the submitted values.
		$aInput = $this->oUtil->addAndApplyFilters( $this, "validation_{$this->oProps->sClassName}", $aInput, $aOriginal );

		// Loop through fields and save the data.
		foreach ( $aInput as $sFieldID => $vValue ) {
			
			// $sOldValue = get_post_meta( $iPostID, $sFieldID, true );			
			$sOldValue = isset( $aOriginal[ $sFieldID ] ) ? $aOriginal[ $sFieldID ] : null;
			if ( ! is_null( $vValue ) && $vValue != $sOldValue ) {
				update_post_meta( $iPostID, $sFieldID, $vValue );
				continue;
			} 
			// if ( '' == $sNewValue && $sOldValue ) 
				// delete_post_meta( $iPostID, $aField['field_id'], $sOldValue );
			
		} // end foreach
		
	}	
	
	/*
	 * Magic method
	*/
	function __call( $sMethodName, $aArgs=null ) {	
		
		// the start_ action hook.
		if ( $sMethodName == $this->oProps->sPrefixStart . $this->oProps->sClassName ) return;

		// the class name + field_ field ID filter.
		if ( substr( $sMethodName, 0, strlen( 'field_' . $this->oProps->sClassName . '_' ) ) == 'field_' . $this->oProps->sClassName . '_' )
			return $aArgs[ 0 ];
		
		// the class name + field_ field ID filter.
		if ( substr( $sMethodName, 0, strlen( $this->oProps->sClassName . '_' . 'field_' ) ) == $this->oProps->sClassName . '_' . 'field_' )
			return $aArgs[ 0 ];

		// the field_types_ + class name filter. [2.1.5+]
		if ( substr( $sMethodName, 0, strlen( "field_types_{$this->oProps->sClassName}" ) ) == "field_types_{$this->oProps->sClassName}" )
			return $aArgs[ 0 ];		
			
		// the script_ + class name	filter.
		if ( substr( $sMethodName, 0, strlen( "script_{$this->oProps->sClassName}" ) ) == "script_{$this->oProps->sClassName}" )
			return $aArgs[ 0 ];		
	
		// the style_ + class name	filter.
		if ( substr( $sMethodName, 0, strlen( "style_{$this->oProps->sClassName}" ) ) == "style_{$this->oProps->sClassName}" )
			return $aArgs[ 0 ];		

		// the validation_ + class name	filter.
		if ( substr( $sMethodName, 0, strlen( "validation_{$this->oProps->sClassName}" ) ) == "validation_{$this->oProps->sClassName}" )
			return $aArgs[ 0 ];				
			
	}
}
endif;