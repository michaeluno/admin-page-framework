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
 * @version				2.1.3b
 */
/*
	Library Name: Admin Page Framework
	Library URI: http://wordpress.org/extend/plugins/admin-page-framework/
	Author:  Michael Uno
	Author URI: http://michaeluno.jp
	Version: 2.1.3b
	Requirements: WordPress 3.3 or above, PHP 5.2.4 or above.
	Description: Provides simpler means of building administration pages for plugin and theme developers.
*/

if ( ! class_exists( 'AdminPageFramework_WPUtilities' ) ) :
/**
 * Provides utility methods which use WordPress functions.
 *
 * @abstract
 * @since			2.0.0
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
abstract class AdminPageFramework_WPUtilities {

	/**
	 * Triggers the do_action() function with the given action names and the arguments.
	 * 
	 * This is useful to perform do_action() on multiple action hooks with the same set of arguments.
	 * For example, if there are the following action hooks, <em>action_name</em>, <em>action_name1</em>, and <em>action_name2</em>, and to perform these, normally it takes the following lines.
	 * <code>do_action( 'action_name1', $var1, $var2 );
	 * do_action( 'action_name2', $var1, $var2 );
	 * do_action( 'action_name3', $var1, $var2 );</code>
	 * 
	 * This method saves these line this way:
	 * <code>$this->doActions( array( 'action_name1', 'action_name2', 'action_name3' ), $var1, $var2 );</code>
	 * 
	 * <h4>Example</h4>
	 * <code>$this->doActions( array( 'action_name1' ), $var1, $var2, $var3 );</code> 
	 * 
	 * @since			2.0.0
	 * @access			public
	 * @remark			Accepts variadic parameters; the number of accepted parameters are not limited to four.
	 * @param			array			$arrActionHooks			a numerically indexed array consisting of action hook names to execute.
	 * @param			mixed			$vArgs1					an argument to pass to the action callbacks.
	 * @param			mixed			$vArgs2					another argument to pass to the action callbacks.
	 * @param			mixed			$_and_more				add as many arguments as necessary to the next parameters.
	 * @return			void			does not return a value.
	 */		
	public function doActions( $arrActionHooks, $vArgs1=null, $vArgs2=null, $_and_more=null ) {
		
		$arrArgs = func_get_args();		
		$arrActionHooks = $arrArgs[ 0 ];
		foreach( ( array ) $arrActionHooks as $strActionHook  ) {
			$arrArgs[ 0 ] = $strActionHook;
			call_user_func_array( 'do_action' , $arrArgs );
		}

	}
	// protected function doAction() {		// Parameters: $strActionHook, $vArgs...
		
		// $arrArgs = func_get_args();	
		// call_user_func_array( 'do_action' , $arrArgs );
		
	// }
	
	/**
	 * Adds the method of the given action hook name(s) to the given action hook(s) with arguments.
	 * 
	 * In other words, this enables to register methods to the custom hooks with the same name and triggers the callbacks (not limited to the registered ones) assigned to the hooks. 
	 * Of course, the registered methods will be triggered right away. Thus, the magic overloading __call() should catch them and redirect the call to the appropriate methods.
	 * This enables, at the same time, publicly the added custom action hooks; therefore, third-party scripts can use the action hooks.
	 * 
	 * This is the reason the object instance must be passed to the first parameter. Regular functions as the callback are not supported for this method.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->oUtil->addAndDoActions( $this, array( 'my_action1', 'my_ction2', 'my_action3' ), 'argument_a', 'argument_b' );</code>
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @remark			Accepts variadic parameters.
	 * @param			object			$oCallerObject			the object that holds the callback method that matches the action hook name.
	 * @param			array			$arrActionHooks			a numerically index array consisting of action hook names that serve as the callback method names. 
	 * @param			mixed			$vArgs1					the argument to pass to the hook callback functions.
	 * @param			mixed			$vArgs2					another argument to pass to the hook callback functions.
	 * @param			mixed			$_and_more				add as many arguments as necessary to the next parameters.
	 * @return			void
	 */ 
	public function addAndDoActions( $oCallerObject, $arrActionHooks, $vArgs1=null, $vArgs2=null, $_and_more=null ) {
	
		$arrArgs = func_get_args();	
		$oCallerObject = $arrArgs[ 0 ];
		$arrActionHooks = $arrArgs[ 1 ];
		foreach( ( array ) $arrActionHooks as $strActionHook ) {
			$arrArgs[ 1 ] = $strActionHook;
			call_user_func_array( array( $this, 'addAndDoAction' ) , $arrArgs );			
		}
		
	}
	
	/**
	 * Adds the methods of the given action hook name to the given action hook with arguments.
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @remark			Accepts variadic parameters.
	 * @return			void
	 */ 
	public function addAndDoAction( $oCallerObject, $strActionHook, $vArgs1=null, $vArgs2=null, $_and_more=null ) {
		
		$intArgs = func_num_args();
		$arrArgs = func_get_args();
		$oCallerObject = $arrArgs[ 0 ];
		$strActionHook = $arrArgs[ 1 ];
		add_action( $strActionHook, array( $oCallerObject, $strActionHook ), 10, $intArgs - 2 );
		unset( $arrArgs[ 0 ] );	// remove the first element, the caller object
		call_user_func_array( 'do_action' , $arrArgs );
		
	}
	public function addAndApplyFilters() {	// Parameters: $oCallerObject, $arrFilters, $vInput, $vArgs...
			
		$arrArgs = func_get_args();	
		$oCallerObject = $arrArgs[ 0 ];
		$arrFilters = $arrArgs[ 1 ];
		$vInput = $arrArgs[ 2 ];

		foreach( ( array ) $arrFilters as $strFilter ) {
			$arrArgs[ 1 ] = $strFilter;
			$arrArgs[ 2 ] = $vInput;
			$vInput = call_user_func_array( array( $this, 'addAndApplyFilter' ) , $arrArgs );						
		}
		return $vInput;
		
	}
	public function addAndApplyFilter() {	// Parameters: $oCallerObject, $strFilter, $vInput, $vArgs...

		$intArgs = func_num_args();
		$arrArgs = func_get_args();
		$oCallerObject = $arrArgs[ 0 ];
		$strFilter = $arrArgs[ 1 ];
		add_filter( $strFilter, array( $oCallerObject, $strFilter ), 10, $intArgs - 2 );	// this enables to trigger the method named $strFilter and the magic method __call() will be called
		unset( $arrArgs[ 0 ] );	// remove the first element, the caller object	// array_shift( $arrArgs );							
		return call_user_func_array( 'apply_filters', $arrArgs );	// $arrArgs: $vInput, $vArgs...
		
	}		
	
	/**
	 * Provides an array consisting of filters for the addAndApplyFileters() method.
	 * 
	 * The order is, page + tab -> page -> class, by default but it can be reversed with the <var>$fReverse</var> parameter value.
	 * 
	 * @since			2.0.0
	 * @access			public
	 * @return				array			Returns an array consisting of the filters.
	 */ 
	public function getFilterArrayByPrefix( $strPrefix, $strClassName, $strPageSlug, $strTabSlug, $fReverse=false ) {
				
		$arrFilters = array();
		if ( $strTabSlug && $strPageSlug )
			$arrFilters[] = "{$strPrefix}{$strPageSlug}_{$strTabSlug}";
		if ( $strPageSlug )	
			$arrFilters[] = "{$strPrefix}{$strPageSlug}";			
		if ( $strClassName )
			$arrFilters[] = "{$strPrefix}{$strClassName}";
		
		return $fReverse ? array_reverse( $arrFilters ) : $arrFilters;	
		
	}
	
	/**
	 * Redirects to the given URL and exits. Saves one extra line, exit;.
	 * 
	 * @since			2.0.0
	 */ 
	public function goRedirect( $strURL ) {
		
		if ( ! function_exists('wp_redirect') ) include_once( ABSPATH . WPINC . '/pluggable.php' );
		wp_redirect( $strURL );
		exit;		
		
	}
	
	/**
	 * Returns an array of plugin data from the given path.		
	 * 
	 * An alternative to get_plugin_data() as some users change the location of the wp-admin directory.
	 * 
	 * @since			2.0.0
	 */ 
	protected function getScriptData( $strPath, $strType='plugin' )	{
	
		$arrData = get_file_data( 
			$strPath, 
			array(
				'strName' => 'Name',
				'strURI' => 'URI',
				'strScriptName' => 'Script Name',
				'strLibraryName' => 'Library Name',
				'strLibraryURI' => 'Library URI',
				'strPluginName' => 'Plugin Name',
				'strPluginURI' => 'Plugin URI',
				'strThemeName' => 'Theme Name',
				'strThemeURI' => 'Theme URI',
				'strVersion' => 'Version',
				'strDescription' => 'Description',
				'strAuthor' => 'Author',
				'strAuthorURI' => 'Author URI',
				'strTextDomain' => 'Text Domain',
				'strDomainPath' => 'Domain Path',
				'strNetwork' => 'Network',
				// Site Wide Only is deprecated in favour of Network.
				'_sitewide' => 'Site Wide Only',
			),
			in_array( $strType, array( 'plugin', 'theme' ) ) ? $strType : 'plugin' 
		);			

		switch ( trim( $strType ) ) {
			case 'theme':	
				$arrData['strName'] = $arrData['strThemeName'];
				$arrData['strURI'] = $arrData['strThemeURI'];
				break;
			case 'library':	
				$arrData['strName'] = $arrData['strLibraryName'];
				$arrData['strURI'] = $arrData['strLibraryURI'];
				break;
			case 'script':	
				$arrData['strName'] = $arrData['strScriptName'];
				break;		
			case 'plugin':	
				$arrData['strName'] = $arrData['strPluginName'];
				$arrData['strURI'] = $arrData['strPluginURI'];
				break;
			default:	
				break;				
		}		

		return $arrData;
		
	}			
	
	/**
	 * Retrieves the current URL in the admin page.
	 * 
	 * @since			2.1.1
	 */
	public function getCurrentAdminURL() {
		
		$strRequestURI = $GLOBALS['is_IIS'] ? $_SERVER['PATH_INFO'] : $_SERVER["REQUEST_URI"];
		$strPageURL = ( @$_SERVER["HTTPS"] == "on" ) ? "https://" : "http://";
		
		if ( $_SERVER["SERVER_PORT"] != "80" ) 
			$strPageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $strRequestURI;
		else 
			$strPageURL .= $_SERVER["SERVER_NAME"] . $strRequestURI;
		
		return $strPageURL;
		
	}
	
	/**
	 * Returns a url with modified query stings.
	 * 
	 * Identical to the getQueryURL() method except that if the third parameter is omitted, it will use the currently browsed admin url.
	 * 
	 * @since			2.1.2
	 * @param			array			$arrAddingQueries			The appending query key value pairs e.g. array( 'page' => 'my_page_slug', 'tab' => 'my_tab_slug' )
	 * @param			array			$arrRemovingQueryKeys		The removing query keys. e.g. array( 'settings-updated', 'my-custom-admin-notice' )
	 * @param			string			$strSubjectURL				The subject url to modify
	 * @return			string			The modified url.
	 */
	public function getQueryAdminURL( $arrAddingQueries, $arrRemovingQueryKeys, $strSubjectURL='' ) {
		
		$strSubjectURL = $strSubjectURL ? $strSubjectURL : add_query_arg( $_GET, admin_url( $GLOBALS['pagenow'] ) );
		return $this->getQueryURL( $arrAddingQueries, $arrRemovingQueryKeys, $strSubjectURL );
		
	}
	/**
	 * Returns a url with modified query stings.
	 * 
	 * @since			2.1.2
	 * @param			array			$arrAddingQueries			The appending query key value pairs
	 * @param			array			$arrRemovingQueryKeys			The removing query key value pairs
	 * @param			string			$strSubjectURL				The subject url to modify
	 * @return			string			The modified url.
	 */
	public function getQueryURL( $arrAddingQueries, $arrRemovingQueryKeys, $strSubjectURL ) {
		
		// Remove Queries
		$strSubjectURL = empty( $arrRemovingQueryKeys ) 
			? $strSubjectURL 
			: remove_query_arg( ( array ) $arrRemovingQueryKeys, $strSubjectURL );
			
		// Add Queries
		$strSubjectURL = add_query_arg( $arrAddingQueries, $strSubjectURL );
		
		return $strSubjectURL;
		
	}	
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_Utilities' ) ) :
/**
 * Provides utility methods which do not use WordPress functions.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_WPUtilities
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
class AdminPageFramework_Utilities extends AdminPageFramework_WPUtilities {
	
	/**
	 * Converts non-alphabetic characters to underscore.
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @remark			it must be public 
	 * @return			string			The sanitized string.
	 */ 
	public function sanitizeSlug( $strSlug ) {
		return preg_replace( '/[^a-zA-Z0-9_\x7f-\xff]/', '_', trim( $strSlug ) );
	}	
	
	/**
	 * Converts non-alphabetic characters to underscore except hyphen(dash).
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @remark			it must be public 
	 * @return			string			The sanitized string.
	 */ 
	public function sanitizeString( $strString ) {
		return preg_replace( '/[^a-zA-Z0-9_\x7f-\xff\-]/', '_', $strString );
	}	
	
	/**
	 * Retrieves a corresponding array value from the given array.
	 * 
	 * When there are multiple arrays and they have similar index structures but it's not certain if one has the key and the others,
	 * use this method to retrieve the corresponding key value. 
	 * 
	 * @remark			This is mainly used by the field array to insert user-defined key values.
	 * @return			string|array			If the key does not exist in the passed array, it will return the default. If the subject value is not an array, it will return the subject value itself.
	 * @since			2.0.0
	 * @since			2.1.3					Added the $fBlankToDefault parameter that sets the default value if the subject value is empty.
	 * @access			protected
	 */
	protected function getCorrespondingArrayValue( $vSubject, $strKey, $strDefault='', $fBlankToDefault=false ) {	
				
		// If $vSubject is null,
		if ( ! isset( $vSubject ) ) return $strDefault;	
			
		// If the $fBlankToDefault flag is set and the subject value is a blank string, return the default value.
		if ( $fBlankToDefault && $vSubject == '' ) return $strDefault;
			
		// If $vSubject is not an array, 
		if ( ! is_array( $vSubject ) ) return ( string ) $vSubject;	// consider it as string.
		
		// Consider $vSubject as array.
		if ( isset( $vSubject[ $strKey ] ) ) return $vSubject[ $strKey ];
		
		return $strDefault;
		
	}
	
	/**
	 * Finds the dimension depth of the given array.
	 * 
	 * @access			protected
	 * @since			2.0.0
	 * @remark			There is a limitation that this only checks the first element so if the second or other elements have deeper dimensions, it will not be caught.
	 * @param			array			$array			the subject array to check.
	 * @return			integer			returns the number of dimensions of the array.
	 */
	protected function getArrayDimension( $array ) {
		return ( is_array( reset( $array ) ) ) ? $this->getArrayDimension( reset( $array ) ) + 1 : 1;
	}
	
	
	/**
	 * Merges multiple multi-dimensional array recursively.
	 * 
	 * The advantage of using this method over the array unite operator or array_merge() is that it merges recursively and the null values of the preceding array will be overridden.
	 * 
	 * @since			2.1.2
	 * @static
	 * @access			public
	 * @remark			The parameters are variadic and can add arrays as many as necessary.
	 * @return			array			the united array.
	 */
	public function uniteArrays( $arrPrecedence, $arrDefault1 ) {
				
		$arrArgs = array_reverse( func_get_args() );
		$arrArray = array();
		foreach( $arrArgs as $arrArg ) 
			$arrArray = $this->uniteArraysRecursive( $arrArg, $arrArray );
			
		return $arrArray;
		
	}
	
	/**
	 * Merges two multi-dimensional arrays recursively.
	 * 
	 * The first parameter array takes its precedence. This is useful to merge default option values. 
	 * An alternative to <em>array_replace_recursive()</em>; it is not supported PHP 5.2.x or below.
	 * 
	 * @since			2.0.0
	 * @access			public
	 * @remark			null values will be overwritten. 	
	 * @param			array			$arrPrecedence			the array that overrides the same keys.
	 * @param			array			$arrDefault				the array that is going to be overridden.
	 * @return			array			the united array.
	 */ 
	public function uniteArraysRecursive( $arrPrecedence, $arrDefault ) {
				
		if ( is_null( $arrPrecedence ) ) $arrPrecedence = array();
		
		if ( ! is_array( $arrDefault ) || ! is_array( $arrPrecedence ) ) return $arrPrecedence;
			
		foreach( $arrDefault as $strKey => $v ) {
			
			// If the precedence does not have the key, assign the default's value.
			if ( ! array_key_exists( $strKey, $arrPrecedence ) || is_null( $arrPrecedence[ $strKey ] ) )
				$arrPrecedence[ $strKey ] = $v;
			else {
				
				// if the both are arrays, do the recursive process.
				if ( is_array( $arrPrecedence[ $strKey ] ) && is_array( $v ) ) 
					$arrPrecedence[ $strKey ] = $this->uniteArraysRecursive( $arrPrecedence[ $strKey ], $v );			
			
			}
		}
		return $arrPrecedence;		
	}		
	
	/**
	 * Retrieves the query value from the given URL with a key.
	 * 
	 * @since			2.0.0
	 * @return			string|null
	 */ 
	public function getQueryValueInURLByKey( $strURL, $strQueryKey ) {
		
		$arrURL = parse_url( $strURL );
		parse_str( $arrURL['query'], $arrQuery );		
		return isset( $arrQuery[ $strQueryKey ] ) ? $arrQuery[ $strQueryKey ] : null;
		
	}
	
	/**
	 * Checks if the passed value is a number and set it to the default if not.
	 * 
	 * This is useful for form data validation. If it is a number and exceeds the set maximum number, 
	 * it sets it to the maximum value. If it is a number and is below the minimum number, it sets to the minimum value.
	 * Set a blank value for no limit.
	 * 
	 * @since			2.0.0
	 * @return			string|integer			A numeric value will be returned. 
	 */ 
	public function fixNumber( $numToFix, $numDefault, $numMin="", $numMax="" ) {

		if ( ! is_numeric( trim( $numToFix ) ) ) return $numDefault;
		if ( $numMin !== "" && $numToFix < $numMin ) return $numMin;
		if ( $numMax !== "" && $numToFix > $numMax ) return $numMax;
		return $numToFix;
		
	}		
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_Help_Base' ) ) :
/**
 * Provides base methods and properties for manipulating the contextual help tabs.
 * 
 * @since			2.1.0
 */
abstract class AdminPageFramework_Help_Base {
	
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
	protected function setHelpTab( $strID, $strTitle, $arrContents, $arrSideBarContents=array() ) {
		
		if ( empty( $arrContents ) ) return;
		
		$this->oScreen = isset( $this->oScreen ) ? $this->oScreen : get_current_screen();
		$this->oScreen->add_help_tab( 
			array(
				'id'	=> $strID,
				'title'	=> $strTitle,
				'content'	=> implode( PHP_EOL, $arrContents ),
			) 
		);						
		
		if ( ! empty( $arrSideBarContents ) )
			$this->oScreen->set_help_sidebar( implode( PHP_EOL, $arrSideBarContents ) );
			
	}
	
	/**
	 * Encloses the given string with the contextual help specific tag.
	 * @since			2.1.0
	 * @internal
	 */ 
	protected function formatHelpDescription( $strHelpDescription ) {
		return "<div class='contextual-help-description'>" . $strHelpDescription . "</div>";
	}
}
endif;

if ( ! class_exists( 'AdminPageFramework_MetaBox_Help' ) ) :
/**
 * Provides methods to manipulate the contextual help tab .
 * 
 * @since			2.1.0
 * @extends			AdminPageFramework_Help_Base
 */
abstract class AdminPageFramework_MetaBox_Help extends AdminPageFramework_Help_Base {
	
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
	protected function addHelpText( $strHTMLContent, $strHTMLSidebarContent="" ) {
		$this->oProps->arrHelpTabText[] = "<div class='contextual-help-description'>" . $strHTMLContent . "</div>";
		$this->oProps->arrHelpTabTextSide[] = "<div class='contextual-help-description'>" . $strHTMLSidebarContent . "</div>";
	}
	
	/**
	 * Adds the given HTML text to the contextual help pane.
	 * 
	 * The help tab will be the meta box title and all the added text will be inserted into the content area within the tab.
	 * On contrary to the <em>addHelpTab()</em> method of the AdminPageFramework_Help class, the help tab title is already determined and the meta box ID and the title will be used.
	 * 
	 * @since			2.1.0
	 * @uses			addHelpText()
	 * @remark			This method just adds the given text into the class property. The actual registration will be performed with the <em>registerHelpTabTextForMetaBox()</em> method.
	 */ 	
	protected function addHelpTextForFormFields( $strFieldTitle, $strHelpText, $strHelpTextSidebar="" ) {
		$this->addHelpText(
			"<span class='contextual-help-tab-title'>" . $strFieldTitle . "</span> - " . PHP_EOL
				. $strHelpText,		
			$strHelpTextSidebar
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
		if ( isset( $_GET['post_type'] ) && ! in_array( $_GET['post_type'], $this->oProps->arrPostTypes ) ) return;
		if ( ! isset( $_GET['post_type'] ) && ! in_array( 'post', $this->oProps->arrPostTypes ) ) return;
		if ( isset( $_GET['post'], $_GET['action'] ) && ! in_array( get_post_type( $_GET['post'] ), $this->oProps->arrPostTypes ) ) return; // edit post page
		
		$this->setHelpTab( 	// this method is defined in the base class.
			$this->oProps->strMetaBoxID, 
			$this->oProps->strTitle, 
			$this->oProps->arrHelpTabText, 
			$this->oProps->arrHelpTabTextSide 
		);
		
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_Help' ) ) :
/**
 * Provides methods to manipulate the help screen sections.
 * 
 * @abstract
 * @remark				Shared with the both AdminPageFramework and AdminPageFramework_PostType.
 * @since				2.1.0
 * @package				Admin Page Framework
 * @subpackage			Admin Page Framework - Page
 * @extends				AdminPageFramework_Help_Base
 * @staticvar			array			$arrStructure_HelpTab			stores the array structure of the help tab array.
 */
abstract class AdminPageFramework_Help extends AdminPageFramework_Help_Base {
	
	/**
	 * Represents the structure of help tab array.
	 * 
	 * @since			2.1.0
	 * @internal
	 */ 
	public static $arrStructure_HelpTab = array(
		'strPageSlug'				=> null,	// ( mandatory )
		'strPageTabSlug'			=> null,	// ( optional )
		'strHelpTabTitle'			=> null,	// ( mandatory )
		'strHelpTabID'				=> null,	// ( mandatory )
		'strHelpTabContent'			=> null,	// ( optional )
		'strHelpTabSidebarContent'	=> null,	// ( optional )
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
			
		$strCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
		$strCurrentPageTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : ( isset( $this->oProps->arrDefaultInPageTabs[ $strCurrentPageSlug ] ) ? $this->oProps->arrDefaultInPageTabs[ $strCurrentPageSlug ] : '' );
		
		if ( empty( $strCurrentPageSlug ) ) return;
		if ( ! $this->oProps->isPageAdded( $strCurrentPageSlug ) ) return;
		
		foreach( $this->oProps->arrHelpTabs as $arrHelpTab ) {
			
			if ( $strCurrentPageSlug != $arrHelpTab['strPageSlug'] ) continue;
			if ( isset( $arrHelpTab['strPageTabSlug'] ) && ! empty( $arrHelpTab['strPageTabSlug'] ) & $strCurrentPageTabSlug != $arrHelpTab['strPageTabSlug'] ) continue;
				
			$this->setHelpTab( 
				$arrHelpTab['strID'], 
				$arrHelpTab['strTitle'], 
				$arrHelpTab['arrContent'], 
				$arrHelpTab['arrSidebar']
			);
		}
		
	}
	
	/**
	 * Adds the given contextual help tab contents into the property.
	 * 
	 * <h4>Contextual Help Tab Array Structure</h4>
	 * <ul>
	 * 	<li><strong>strPageSlug</strong> - the page slug of the page that the contextual help tab and its contents are displayed.</li>
	 * 	<li><strong>strPageTabSlug</strong> - ( optional ) the tab slug of the page that the contextual help tab and its contents are displayed.</li>
	 * 	<li><strong>strHelpTabTitle</strong> - the title of the contextual help tab.</li>
	 * 	<li><strong>strHelpTabID</strong> - the id of the contextual help tab.</li>
	 * 	<li><strong>strHelpTabContent</strong> - the HTML string content of the the contextual help tab.</li>
	 * 	<li><strong>strHelpTabSidebarContent</strong> - ( optional ) the HTML string content of the sidebar of the contextual help tab.</li>
	 * </ul>
	 * 
	 * <h4>Example</h4>
	 * <code>	$this->addHelpTab( 
	 *		array(
	 *			'strPageSlug'				=> 'first_page',	// ( mandatory )
	 *			// 'strPageTabSlug'			=> null,	// ( optional )
	 *			'strHelpTabTitle'			=> 'Admin Page Framework',
	 *			'strHelpTabID'				=> 'admin_page_framework',	// ( mandatory )
	 *			'strHelpTabContent'			=> __( 'This contextual help text can be set with the <em>addHelpTab()</em> method.', 'admin-page-framework' ),
	 *			'strHelpTabSidebarContent'	=> __( 'This is placed in the sidebar of the help pane.', 'admin-page-framework' ),
	 *		)
	 *	);</code>
	 * 
	 * @since			2.1.0
	 * @remark			Called when registering setting sections and fields.
	 * @remark			The user may use this method.
	 * @param			array			$arrHelpTab				The help tab array. The key structure is explained in the description part.
	 * @return			void
	 */ 
	protected function addHelpTab( $arrHelpTab ) {
		
		// Avoid undefined index warnings.
		$arrHelpTab = ( array ) $arrHelpTab + AdminPageFramework_Help::$arrStructure_HelpTab;
		
		// If the key is not set, that means the help tab array is not created yet. So create it and go back.
		if ( ! isset( $this->oProps->arrHelpTabs[ $arrHelpTab['strHelpTabID'] ] ) ) {
			$this->oProps->arrHelpTabs[ $arrHelpTab['strHelpTabID'] ] = array(
				'strID' => $arrHelpTab['strHelpTabID'],
				'strTitle' => $arrHelpTab['strHelpTabTitle'],
				'arrContent' => ! empty( $arrHelpTab['strHelpTabContent'] ) ? array( $this->formatHelpDescription( $arrHelpTab['strHelpTabContent'] ) ) : array(),
				'arrSidebar' => ! empty( $arrHelpTab['strHelpTabSidebarContent'] ) ? array( $this->formatHelpDescription( $arrHelpTab['strHelpTabSidebarContent'] ) ) : array(),
				'strPageSlug' => $arrHelpTab['strPageSlug'],
				'strPageTabSlug' => $arrHelpTab['strPageTabSlug'],
			);
			return;
		}

		// This line will be reached if the help tab array is already set. In this case, just append an array element into the keys.
		if ( ! empty( $arrHelpTab['strHelpTabContent'] ) )
			$this->oProps->arrHelpTabs[ $arrHelpTab['strHelpTabID']]['arrContent'][] = $this->formatHelpDescription( $arrHelpTab['strHelpTabContent'] );
		if ( ! empty( $arrHelpTab['strHelpTabSidebarContent'] ) )
			$this->oProps->arrHelpTabs[ $arrHelpTab['strHelpTabID'] ]['arrSidebar'][] = $this->formatHelpDescription( $arrHelpTab['strHelpTabSidebarContent'] );
		
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_Pages' ) ) :
/**
 * Provides methods to renders admin page elements.
 *
 * @abstract
 * @since			2.0.0
 * @since			2.1.0		Extends AdminPageFramework_Help.
 * @extends			AdminPageFramework_Help
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Page
 * @staticvar		array		$arrPrefixes						stores the prefix strings for filter and action hooks.
 * @staticvar		array		$arrPrefixesForCallbacks			unlike $arrPrefixes, these require to set the return value.
 * @staticvar		array		$arrScreenIconIDs					stores the ID selector names for screen icons.
 * @staticvar		array		$arrPrefixes						stores the prefix strings for filter and action hooks.
 * @staticvar		array		$arrStructure_InPageTabElements		represents the array structure of an in-page tab array.
 */
abstract class AdminPageFramework_Pages extends AdminPageFramework_Help {
			
	/**
	 * Stores the prefixes of the filters used by this framework.
	 * 
	 * This must use the protected scope as the extended class accesses it, such as 'start_'.
	 * 
	 * @since			2.0.0
	 * @var				array
	 * @static
	 * @access			protected
	 * @internal
	 */ 
	protected static $arrPrefixes = array(	
		'start_'		=> 'start_',
		'do_before_'	=> 'do_before_',
		'do_after_'		=> 'do_after_',
		'do_form_'		=> 'do_form_',
		'do_'			=> 'do_',
		'content_'		=> 'content_',
		'load_'			=> 'load_',
		'head_'			=> 'head_',
		'foot_'			=> 'foot_',
		'validation_'	=> 'validation_',
		'export_name'	=> 'export_name',
		'export_format' => 'export_format',
		'export_'		=> 'export_',
		'import_'		=> 'import_',
		'style_'		=> 'style_',
		
		'script_'		=> 'script_',
	);

	/**
	 * Unlike $arrPrefixes, these require to set the return value.
	 * 
	 * @since			2.0.0
	 * @var				array
	 * @static
	 * @access			protected
	 * @internal
	 */ 	
	protected static $arrPrefixesForCallbacks = array(
		'section_'		=> 'section_',
		'field_'		=> 'field_',
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
	protected static $arrScreenIconIDs = array(
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
	private static $arrStructure_InPageTabElements = array(
		'strPageSlug' => null,
		'strTabSlug' => null,
		'strTitle' => null,
		'numOrder' => null,
		'fHide'	=> null,
		'strParentTabSlug' => null,	// this needs to be set if the above fHide is true so that the plugin can mark the parent tab to be active when the hidden page is accessed.
	);
	
		
	/**
	 * Sets whether the page title is displayed or not.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->showPageTitle( false );    // disables the page title.</code>
	 * 
	 * @since			2.0.0
	 * @param			boolean			$fShow			If false, the page title will not be displayed.
	 * @remark			The user may use this method.
	 * @return			void
	 */ 
	protected function showPageTitle( $fShow=true, $strPageSlug='' ) {
		$strPageSlug = $this->oUtil->sanitizeSlug( $strPageSlug );
		if ( ! empty( $strPageSlug ) )
			$this->oProps->arrPages[ $strPageSlug ]['fShowPageTitle'] = $fShow;
		else {
			$this->oProps->fShowPageTitle = $fShow;
			foreach( $this->oProps->arrPages as &$arrPage ) 
				$arrPage['fShowPageTitle'] = $fShow;
		}
	}	
	
	/**
	 * Sets whether page-heading tabs are displayed or not.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->showPageHeadingTabs( false );    // disables the page heading tabs by passing false.</code>
	 * 
	 * @since			2.0.0
	 * @param			boolean			$fShow					If false, page-heading tabs will be disabled; otherwise, enabled.
	 * @param			string			$strPageSlug			The page to apply the visibility setting. If not set, it applies to all the pages.
	 * @remark			Page-heading tabs and in-page tabs are different. The former displays page titles and the latter displays tab titles.
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */ 
	protected function showPageHeadingTabs( $fShow=true, $strPageSlug='' ) {
		$strPageSlug = $this->oUtil->sanitizeSlug( $strPageSlug );
		if ( ! empty( $strPageSlug ) )
			$this->oProps->arrPages[ $strPageSlug ]['fShowPageHeadingTabs'] = $fShow;
		else {
			$this->oProps->fShowPageHeadingTabs = $fShow;
			foreach( $this->oProps->arrPages as &$arrPage ) 
				$arrPage['fShowPageHeadingTabs'] = $fShow;
		}
	}
	
	/**
	 * Sets whether in-page tabs are displayed or not.
	 * 
	 * Sometimes, it is required to disable in-page tabs in certain pages. In that case, use the second parameter.
	 * 
	 * @since			2.1.1
	 * @param			boolean			$fShow				If false, in-page tabs will be disabled.
	 * @param			string			$strPageSlug		The page to apply the visibility setting. If not set, it applies to all the pages.
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */
	protected function showInPageTabs( $fShow=true, $strPageSlug='' ) {
		$strPageSlug = $this->oUtil->sanitizeSlug( $strPageSlug );
		if ( ! empty( $strPageSlug ) )
			$this->oProps->arrPages[ $strPageSlug ]['fShowInPageTabs'] = $fShow;
		else {
			$this->oProps->fShowInPageTabs = $fShow;
			foreach( $this->oProps->arrPages as &$arrPage )
				$arrPage['fShowInPageTabs'] = $fShow;
		}
	}
	
	/**
	 * Sets in-page tab's HTML tag.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setInPageTabTag( 'h2' );</code>
	 * 
	 * @since			2.0.0
	 * @param			string			$strTag					The HTML tag that encloses each in-page tab title. Default: h3.
	 * @param			string			$strPageSlug			The page slug that applies the setting.	
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */ 	
	protected function setInPageTabTag( $strTag='h3', $strPageSlug='' ) {
		$strPageSlug = $this->oUtil->sanitizeSlug( $strPageSlug );
		if ( ! empty( $strPageSlug ) )
			$this->oProps->arrPages[ $strPageSlug ]['strInPageTabTag'] = $strTag;
		else {
			$this->oProps->strInPageTabTag = $strTag;
			foreach( $this->oProps->arrPages as &$arrPage )
				$arrPage['strInPageTabTag'] = $strTag;
		}
	}
	
	/**
	 * Sets page-heading tab's HTML tag.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setPageHeadingTabTag( 'h2' );</code>
	 * 
	 * @since			2.1.2
	 * @param			stting			$strTag					The HTML tag that encloses the page-heading tab title. Default: h2.
	 * @param			string			$strPageSlug			The page slug that applies the setting.	
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */
	protected function setPageHeadingTabTag( $strTag='h2', $strPageSlug='' ) {
		$strPageSlug = $this->oUtil->sanitizeSlug( $strPageSlug );
		if ( ! empty( $strPageSlug ) )
			$this->oProps->arrPages[ $strPageSlug ]['strPageHeadingTabTag'] = $strTag;
		else {
			$this->oProps->strPageHeadingTabTag = $strTag;
			foreach( $this->oProps->arrPages as &$arrPage )
				$arrPage[ $strPageSlug ]['strPageHeadingTabTag'] = $strTag;
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
	protected function renderPage( $strPageSlug, $strTabSlug=null ) {

		// Do actions before rendering the page. In this order, global -> page -> in-page tab
		$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['do_before_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, true ) );	
		?>
		<div class="wrap">
			<?php
				// Screen icon, page heading tabs(page title), and in-page tabs.
				$strHead = $this->getScreenIcon( $strPageSlug );	
				$strHead .= $this->getPageHeadingTabs( $strPageSlug, $this->oProps->strPageHeadingTabTag ); 	
				$strHead .= $this->getInPageTabs( $strPageSlug, $this->oProps->strInPageTabTag );

				// Apply filters in this order, in-page tab -> page -> global.
				echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['head_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, false ), $strHead );
			?>
			<div class="admin-page-framework-container">
				<?php
					$this->showSettingsErrors();
						
					$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['do_form_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, true ) );	

					echo $this->getFormOpeningTag();	// <form ... >
					
					// Capture the output buffer
					ob_start(); // start buffer
							 					
					// Render the form elements by Settings API
					if ( $this->oProps->fEnableForm ) {
						settings_fields( $this->oProps->strOptionKey );	// this value also determines the $option_page global variable value.
						do_settings_sections( $strPageSlug ); 
					}				
					 
					$strContent = ob_get_contents(); // assign the content buffer to a variable
					ob_end_clean(); // end buffer and remove the buffer
								
					// Apply the content filters.
					echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['content_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, false ), $strContent );
	
					// Do the page actions.
					$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['do_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, true ) );	
						
				?>
				
			<?php echo $this->getFormClosingTag( $strPageSlug, $strTabSlug );  ?>
			
			</div><!-- End admin-page-framework-container -->
				
			<?php	
				// Apply the foot filters.
				echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['foot_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, false ), '' );	// empty string
			?>
		</div><!-- End Wrap -->
		<?php
		// Do actions after rendering the page.
		$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['do_after_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, true ) );
		
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
		
		$arrSettingsMessages = get_settings_errors( $this->oProps->strOptionKey );
		
		// If custom messages are added, remove the default one. 
		if ( count( $arrSettingsMessages ) > 1 ) 
			$this->removeDefaultSettingsNotice();
		
		settings_errors( $this->oProps->strOptionKey );	// Show the message like "The options have been updated" etc.
	
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
		
		$arrDefaultMessages = array(
			$this->oMsg->___( 'option_cleared' ),
			$this->oMsg->___( 'option_updated' ),
		);
		
		foreach ( ( array ) $wp_settings_errors as $intIndex => $arrDetails ) {
			
			if ( $arrDetails['setting'] != $this->oProps->strOptionKey ) continue;
			
			if ( in_array( $arrDetails['message'], $arrDefaultMessages ) )
				unset( $wp_settings_errors[ $intIndex ] );
				
		}
	}
	
	/**
	 * Retrieves the form opening tag.
	 * 
	 * @since			2.0.0
	 * @internal
	 */ 
	protected function getFormOpeningTag() {
		
		if ( ! $this->oProps->fEnableForm ) return '';
		return "<form action='options.php' method='post' enctype='{$this->oProps->strFormEncType}'>";
	
	}
	
	/**
	 * Retrieves the form closing tag.
	 * 
	 * @since			2.0.0
	 * @internal
	 */ 	
	protected function getFormClosingTag( $strPageSlug, $strTabSlug ) {

		if ( ! $this->oProps->fEnableForm ) return '';	
		return "<input type='hidden' name='strPageSlug' value='{$strPageSlug}' />" . PHP_EOL
			. "<input type='hidden' name='strTabSlug' value='{$strTabSlug}' />" . PHP_EOL			
			. "</form><!-- End Form -->";
	
	}	
	
	/**
	 * Retrieves the screen icon output as HTML.
	 * 
	 * @remark			the screen object is supported in WordPress 3.3 or above.
	 * @since			2.0.0
	 */ 	
	private function getScreenIcon( $strPageSlug ) {

		// If the icon path is explicitly set, use it.
		if ( isset( $this->oProps->arrPages[ $strPageSlug ]['strURLIcon32x32'] ) ) 
			return '<div class="icon32" style="background-image: url(' . $this->oProps->arrPages[ $strPageSlug ]['strURLIcon32x32'] . ');"><br /></div>';
		
		// If the screen icon ID is explicitly set, use it.
		if ( isset( $this->oProps->arrPages[ $strPageSlug ]['strScreenIconID'] ) )
			return '<div class="icon32" id="icon-' . $this->oProps->arrPages[ $strPageSlug ]['strScreenIconID'] . '"><br /></div>';
			
		// Retrieve the screen object for the current page.
		$oScreen = get_current_screen();
		$strIconIDAttribute = $this->getScreenIDAttribute( $oScreen );

		$strClass = 'icon32';
		if ( empty( $strIconIDAttribute ) && $oScreen->post_type ) 
			$strClass .= ' ' . sanitize_html_class( 'icon32-posts-' . $oScreen->post_type );
		
		if ( empty( $strIconIDAttribute ) || $strIconIDAttribute == $this->oProps->strClassName )
			$strIconIDAttribute = 'generic';		// the default value
		
		return '<div id="icon-' . $strIconIDAttribute . '" class="' . $strClass . '"><br /></div>';
			
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
	private function getPageHeadingTabs( $strCurrentPageSlug, $strTag='h2', $arrOutput=array() ) {
		
		// If the page title is disabled, return an empty string.
		if ( ! $this->oProps->arrPages[ $strCurrentPageSlug ][ 'fShowPageTitle' ] ) return "";

		$strTag = $this->oProps->arrPages[ $strCurrentPageSlug ][ 'strPageHeadingTabTag' ]
			? $this->oProps->arrPages[ $strCurrentPageSlug ][ 'strPageHeadingTabTag' ]
			: $strTag;
	
		// If the page heading tab visibility is disabled, return the title.
		if ( ! $this->oProps->arrPages[ $strCurrentPageSlug ][ 'fShowPageHeadingTabs' ] )
			return "<{$strTag}>" . $this->oProps->arrPages[ $strCurrentPageSlug ]['strPageTitle'] . "</{$strTag}>";		
		
		foreach( $this->oProps->arrPages as $arrSubPage ) {
			
			// For added sub-pages
			if ( isset( $arrSubPage['strPageSlug'] ) && $arrSubPage['fShowPageHeadingTab'] ) {
				// Check if the current tab number matches the iteration number. If not match, then assign blank; otherwise put the active class name.
				$strClassActive =  $strCurrentPageSlug == $arrSubPage['strPageSlug']  ? 'nav-tab-active' : '';		
				$arrOutput[] = "<a class='nav-tab {$strClassActive}' "
					. "href='" . $this->oUtil->getQueryAdminURL( array( 'page' => $arrSubPage['strPageSlug'], 'tab' => false ), $this->oProps->arrDisallowedQueryKeys ) 
					. "'>"
					. $arrSubPage['strPageTitle']
					. "</a>";	
			}
			
			// For added menu links
			if ( 
				isset( $arrSubPage['strURL'] )
				&& $arrSubPage['strType'] == 'link' 
				&& $arrSubPage['fShowPageHeadingTab']
			) 
				$arrOutput[] = "<a class='nav-tab link' "
					. "href='{$arrSubPage['strURL']}'>"
					. $arrSubPage['strMenuTitle']
					. "</a>";					
			
		}
		return "<div class='admin-page-framework-page-heading-tab'><{$strTag} class='nav-tab-wrapper'>" 
			.  implode( '', $arrOutput ) 
			. "</{$strTag}></div>";
		
	}

	/**
	 * Retrieves the output of in-page tab navigation bar as HTML.
	 * 
	 * @since			2.0.0
	 * @return			string			the output of in-page tabs.
	 */ 	
	private function getInPageTabs( $strCurrentPageSlug, $strTag='h3', $arrOutput=array() ) {
		
		// If in-page tabs are not set, return an empty string.
		if ( empty( $this->oProps->arrInPageTabs[ $strCurrentPageSlug ] ) ) return implode( '', $arrOutput );
				
		// Determine the current tab slug.
		$strCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->getDefaultInPageTab( $strCurrentPageSlug );
		$strCurrentTabSlug = $this->getParentTabSlug( $strCurrentPageSlug, $strCurrentTabSlug );
		
		$strTag = $this->oProps->arrPages[ $strCurrentPageSlug ][ 'strInPageTabTag' ]
			? $this->oProps->arrPages[ $strCurrentPageSlug ][ 'strInPageTabTag' ]
			: $strTag;
	
		// If the in-page tabs' visibility is set to false, returns the title.
		if ( ! $this->oProps->arrPages[ $strCurrentPageSlug ][ 'fShowInPageTabs' ]	)
			return isset( $this->oProps->arrInPageTabs[ $strCurrentPageSlug ][ $strCurrentTabSlug ]['strTitle'] ) 
				? "<{$strTag}>{$this->oProps->arrInPageTabs[ $strCurrentPageSlug ][ $strCurrentTabSlug ]['strTitle']}</{$strTag}>" 
				: "";
	
		// Get the actual string buffer.
		foreach( $this->oProps->arrInPageTabs[ $strCurrentPageSlug ] as $strTabSlug => $arrInPageTab ) {
					
			// If it's hidden and its parent tab is not set, skip
			if ( $arrInPageTab['fHide'] && ! isset( $arrInPageTab['strParentTabSlug'] ) ) continue;
			
			// The parent tab means the root tab when there is a hidden tab that belongs to it. Also check it the specified parent tab exists.
			$strInPageTabSlug = isset( $arrInPageTab['strParentTabSlug'], $this->oProps->arrInPageTabs[ $strCurrentPageSlug ][ $arrInPageTab['strParentTabSlug'] ] ) 
				? $arrInPageTab['strParentTabSlug'] 
				: $arrInPageTab['strTabSlug'];
				
			// Check if the current tab slug matches the iteration slug. If not match, assign blank; otherwise, put the active class name.
			$fIsActiveTab = ( $strCurrentTabSlug == $strInPageTabSlug );
			$arrOutput[ $strInPageTabSlug ] = "<a class='nav-tab " . ( $fIsActiveTab ? "nav-tab-active" : "" ) . "' "
				. "href='" . $this->oUtil->getQueryAdminURL( array( 'page' => $strCurrentPageSlug, 'tab' => $strInPageTabSlug ), $this->oProps->arrDisallowedQueryKeys ) 
				. "'>"
				. $this->oProps->arrInPageTabs[ $strCurrentPageSlug ][ $strInPageTabSlug ]['strTitle'] //	"{$arrInPageTab['strTitle']}"
				. "</a>";
		
		}		
		if ( ! empty( $arrOutput ) )
			return "<div class='admin-page-framework-in-page-tab'><{$strTag} class='nav-tab-wrapper in-page-tab'>" 
				. implode( '', $arrOutput )
				. "</{$strTag}></div>";
			
	}

	/**
	 * Retrieves the parent tab slug from the given tab slug.
	 * 
	 * @since			2.0.0
	 * @since			2.1.2			If the parent slug has the fHide to be true, it returns an empty string.
	 * @return			string			the parent tab slug.
	 */ 	
	private function getParentTabSlug( $strPageSlug, $strTabSlug ) {
		
		$strParentTabSlug = isset( $this->oProps->arrInPageTabs[ $strPageSlug ][ $strTabSlug ]['strParentTabSlug'] ) 
			? $this->oProps->arrInPageTabs[ $strPageSlug ][ $strTabSlug ]['strParentTabSlug']
			: $strTabSlug;
		
		return isset( $this->oProps->arrInPageTabs[ $strPageSlug ][ $strParentTabSlug ]['fHide'] ) && $this->oProps->arrInPageTabs[ $strPageSlug ][ $strParentTabSlug ]['fHide']
			? ""
			: $strParentTabSlug;

	}

	/**
	 * Adds an in-page tab.
	 * 
	 * @since			2.0.0
	 * @param			string			$strPageSlug			The page slug that the tab belongs to.
	 * @param			string			$strTabTitle			The title of the tab.
	 * @param			string			$strTabSlug				The tab slug. Non-alphabetical characters should not be used including dots(.) and hyphens(-).
	 * @param			integer			$numOrder				( optional ) the order number of the tab. The lager the number is, the lower the position it is placed in the menu.
	 * @param			boolean			$fHide					( optional ) default: false. If this is set to false, the tab title will not be displayed in the tab navigation menu; however, it is still accessible from the direct URL.
	 * @param			string			$strParentTabSlug		( optional ) this needs to be set if the above fHide is true so that the parent tab will be emphasized as active when the hidden page is accessed.
	 * @remark			Use this method to add in-page tabs to ensure the array holds all the necessary keys.
	 * @remark			In-page tabs are different from page-heading tabs which is automatically added with page titles.
	 * @return			void
	 */ 		
	protected function addInPageTab( $strPageSlug, $strTabTitle, $strTabSlug, $numOrder=null, $fHide=null, $strParentTabSlug=null ) {	
		
		$strTabSlug = $this->oUtil->sanitizeSlug( $strTabSlug );
		$strPageSlug = $this->oUtil->sanitizeSlug( $strPageSlug );
		$intCountElement = isset( $this->oProps->arrInPageTabs[ $strPageSlug ] ) ? count( $this->oProps->arrInPageTabs[ $strPageSlug ] ) : 0;
		if ( ! empty( $strTabSlug ) && ! empty( $strPageSlug ) ) 
			$this->oProps->arrInPageTabs[ $strPageSlug ][ $strTabSlug ] = array(
				'strPageSlug'	=> $strPageSlug,
				'strTitle'		=> trim( $strTabTitle ),
				'strTabSlug'	=> $strTabSlug,
				'numOrder'		=> is_numeric( $numOrder ) ? $numOrder : $intCountElement + 10,
				'fHide'			=> ( $fHide ),
				'strParentTabSlug' => ! empty( $strParentTabSlug ) ? $this->oUtil->sanitizeSlug( $strParentTabSlug ) : null,
			);
	
	}
	/**
	 * Adds in-page tabs.
	 *
	 * The parameters accept in-page tab arrays and they must have the following array keys.
	 * <h4>In-Page Tab Array</h4>
	 * <ul>
	 * 	<li><strong>strPageSlug</strong> - ( string ) the page slug that the tab belongs to.</li>
	 * 	<li><strong>strTabSlug</strong> -  ( string ) the tab slug. Non-alphabetical characters should not be used including dots(.) and hyphens(-).</li>
	 * 	<li><strong>strTitle</strong> - ( string ) the title of the tab.</li>
	 * 	<li><strong>numOrder</strong> - ( optional, integer ) the order number of the tab. The lager the number is, the lower the position it is placed in the menu.</li>
	 * 	<li><strong>fHide</strong> - ( optional, boolean ) default: false. If this is set to false, the tab title will not be displayed in the tab navigation menu; however, it is still accessible from the direct URL.</li>
	 * 	<li><strong>strParentTabSlug</strong> - ( optional, string ) this needs to be set if the above fHide is true so that the parent tab will be emphasized as active when the hidden page is accessed.</li>
	 * </ul>
	 * 
	 * <h4>Example</h4>
	 * <code>$this->addInPageTabs(
	 *		array(
	 *			'strTabSlug' => 'firsttab',
	 *			'strTitle' => __( 'Text Fields', 'my-text-domain' ),
	 *			'strPageSlug' => 'myfirstpage'
	 *		),
	 *		array(
	 *			'strTabSlug' => 'secondtab',
	 *			'strTitle' => __( 'Selectors and Checkboxes', 'my-text-domain' ),
	 *			'strPageSlug' => 'myfirstpage'
	 *		)
	 *	);</code>
	 * 
	 * @since			2.0.0
	 * @param			array			$arrTab1			The in-page tab array.
	 * @param			array			$arrTab2			Another in-page tab array.
	 * @param			array			$_and_more			Add in-page tab arrays as many as necessary to the next parameters.
	 * @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	 * @remark			In-page tabs are different from page-heading tabs which is automatically added with page titles.	 
	 * @return			void
	 */ 			
	protected function addInPageTabs( $arrTab1, $arrTab2=null, $_and_more=null ) {
		
		foreach( func_get_args() as $arrTab ) {
			if ( ! is_array( $arrTab ) ) continue;
			$arrTab = $arrTab + self::$arrStructure_InPageTabElements;	// avoid undefined index warnings.
			$this->addInPageTab( $arrTab['strPageSlug'], $arrTab['strTitle'], $arrTab['strTabSlug'], $arrTab['numOrder'], $arrTab['fHide'], $arrTab['strParentTabSlug'] );
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
	
		foreach( $this->oProps->arrPages as $strPageSlug => $arrPage ) {
			
			if ( ! isset( $this->oProps->arrInPageTabs[ $strPageSlug ] ) ) continue;
			
			// Apply filters to let modify the in-page tab array.
			$this->oProps->arrInPageTabs[ $strPageSlug ] = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $strFilter, $vInput, $vArgs...
				$this,
				"{$this->oProps->strClassName}_{$strPageSlug}_tabs",
				$this->oProps->arrInPageTabs[ $strPageSlug ]			
			);	
			// Added in-page arrays may be missing necessary keys so merge them with the default array strucure.
			foreach( $this->oProps->arrInPageTabs[ $strPageSlug ] as &$arrInPageTab ) 
				$arrInPageTab = $arrInPageTab + self::$arrStructure_InPageTabElements;
						
			// Sort the in-page tab array.
			uasort( $this->oProps->arrInPageTabs[ $strPageSlug ], array( $this->oProps, 'sortByOrder' ) );
			
			// Set the default tab for the page.
			// Read the value as reference; otherwise, a strange bug occurs. It may be due to the variable name, $arrInPageTab, is also used as reference in the above foreach.
			foreach( $this->oProps->arrInPageTabs[ $strPageSlug ] as $strTabSlug => &$arrInPageTab ) { 	
			
				if ( ! isset( $arrInPageTab['strTabSlug'] ) ) continue;	
				
				// Regardless of whether it's a hidden tab, it is stored as the default in-page tab.
				$this->oProps->arrDefaultInPageTabs[ $strPageSlug ] = $arrInPageTab['strTabSlug'];
					
				break;	// The first iteration item is the default one.
			}
		}
	}			

	/**
	 * Retrieves the default in-page tab from the given tab slug.
	 * 
	 * @since			2.0.0
	 * @internal
	 * @remark			Used in the __call() method in the main class.
	 * @return			string			The default in-page tab slug if found; otherwise, an empty string.
	 */ 		
	protected function getDefaultInPageTab( $strPageSlug ) {
	
		if ( ! $strPageSlug ) return '';		
		return isset( $this->oProps->arrDefaultInPageTabs[ $strPageSlug ] ) 
			? $this->oProps->arrDefaultInPageTabs[ $strPageSlug ]
			: '';

	}
	
	
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_Menu' ) ) :
/**
 * Provides methods to manipulate menu items.
 *
 * @abstract
 * @since			2.0.0
 * @extends			AdminPageFramework_Pages
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Page
 * @staticvar		array	$arrBuiltInRootMenuSlugs	stores the WordPress built-in menu slugs.
 * @staticvar		array	$arrStructure_SubMenuPage	represents the structure of the sub-menu page array.
 */
abstract class AdminPageFramework_Menu extends AdminPageFramework_Pages {
	
	/**
	 * Used to refer the built-in root menu slugs.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds the built-in root menu slugs.
	 * @static
	 * @internal
	 */ 
	protected static $arrBuiltInRootMenuSlugs = array(
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
	protected static $arrStructure_SubMenuPage = array(
		'strPageTitle' => null, 
		'strPageSlug' => null, 
		'strScreenIcon' => null,
		'strCapability' => null, 
		'numOrder' => null,
		'fShowPageHeadingTab' => true,	// if this is set false, the page title won't be displayed in the page heading tab.
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
	 * @remark			Only one root page can be set per one class instance.
	 * @param			string			$strRootMenuLabel			If the method cannot find the passed string from the following listed items, it will create a top level menu item with the passed string. ( case insensitive )
	 * <blockquote>Dashboard, Posts, Media, Links, Pages, Comments, Appearance, Plugins, Users, Tools, Settings, Network Admin</blockquote>
	 * @param			string			$strURLIcon16x16			( optional ) the URL of the menu icon. The size should be 16 by 16 in pixel.
	 * @param			string			$intMenuPosition			( optional ) the position number that is passed to the <var>$position</var> parameter of the <a href="http://codex.wordpress.org/Function_Reference/add_menu_page">add_menu_page()</a> function.
	 * @return			void
	 */
	protected function setRootMenuPage( $strRootMenuLabel, $strURLIcon16x16=null, $intMenuPosition=null ) {

		$strRootMenuLabel = trim( $strRootMenuLabel );
		$strSlug = $this->isBuiltInMenuItem( $strRootMenuLabel );	// if true, this method returns the slug
		$this->oProps->arrRootMenu = array(
			'strTitle'			=> $strRootMenuLabel,
			'strPageSlug' 		=> $strSlug ? $strSlug : $this->oProps->strClassName,	
			'strURLIcon16x16'	=> filter_var( $strURLIcon16x16, FILTER_VALIDATE_URL) ? $strURLIcon16x16 : null,
			'intPosition'		=> $intMenuPosition,
			'fCreateRoot'		=> $strSlug ? false : true,
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
	 * @param			string			$strRootMenuSlug			The page slug of the top-level root page.
	 * @return			void
	 */ 
	protected function setRootMenuPageBySlug( $strRootMenuSlug ) {
		
		$this->oProps->arrRootMenu['strPageSlug'] = $strRootMenuSlug;	// do not sanitize the slug here because post types includes a question mark.
		$this->oProps->arrRootMenu['fCreateRoot'] = false;		// indicates to use an existing menu item. 
		
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
		foreach ( func_get_args() as $arrSubMenuPage ) {
			$arrSubMenuPage = $arrSubMenuPage + self::$arrStructure_SubMenuPage;	// avoid undefined index warnings.
			$this->addSubMenuPage(
				$arrSubMenuPage['strPageTitle'],
				$arrSubMenuPage['strPageSlug'],
				$arrSubMenuPage['strScreenIcon'],
				$arrSubMenuPage['strCapability'],
				$arrSubMenuPage['numOrder'],
				$arrSubMenuPage['fShowPageHeadingTab']
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
	 * @since			2.1.2			The key name fPageHeadingTab was changed to fShowPageHeadingTab
	 * @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	 * @param			string			$strPageTitle			The title of the page.
	 * @param			string			$strPageSlug			The slug of the page.
	 * @param			string			$strScreenIcon			( optional ) Either a screen icon ID or a url of the icon with the size of 32 by 32 in pixel. The accepted icon IDs are as follows.
	 * <blockquote>edit, post, index, media, upload, link-manager, link, link-category, edit-pages, page, edit-comments, themes, plugins, users, profile, user-edit, tools, admin, options-general, ms-admin, generic</blockquote>
	 * <strong>Note:</strong> the <em>generic</em> ID is available since WordPress 3.5.
	 * @param			string			$strCapability			( optional ) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the page.
	 * @param			integer			$numOrder				( optional ) the order number of the page. The lager the number is, the lower the position it is placed in the menu.
	 * @param			boolean			$fShowPageHeadingTab	( optional ) If this is set to false, the page title won't be displayed in the page heading tab. Default: true.
	 * @return			void
	 */ 
	protected function addSubMenuPage( $strPageTitle, $strPageSlug, $strScreenIcon=null, $strCapability=null, $numOrder=null, $fShowPageHeadingTab=true ) {
		
		$strPageSlug = $this->oUtil->sanitizeSlug( $strPageSlug );
		$intCount = count( $this->oProps->arrPages );
		$arrPreviouslySetPage = isset( $this->oProps->arrPages[ $strPageSlug ] ) 
			? $this->oProps->arrPages[ $strPageSlug ]
			: array();
		$arrThisPage = array(  
			'strPageTitle'				=> $strPageTitle,
			'strPageSlug'				=> $strPageSlug,
			'strType'					=> 'page',	// this is used to compare with the link type.
			'strURLIcon32x32'			=> filter_var( $strScreenIcon, FILTER_VALIDATE_URL) ? $strScreenIcon : null,
			'strScreenIconID'			=> in_array( $strScreenIcon, self::$arrScreenIconIDs ) ? $strScreenIcon : null,
			'strCapability'				=> isset( $strCapability ) ? $strCapability : $this->oProps->strCapability,
			'numOrder'					=> is_numeric( $numOrder ) ? $numOrder : $intCount + 10,
			'fShowPageHeadingTab'		=> $fShowPageHeadingTab,
			'fShowPageTitle'			=> $this->oProps->fShowPageTitle,			// boolean
			'fShowPageHeadingTabs'		=> $this->oProps->fShowPageHeadingTabs,			// boolean
			'fShowInPageTabs'			=> $this->oProps->fShowInPageTabs,			// boolean
			'strInPageTabTag'			=> $this->oProps->strInPageTabTag,			// string
			'strPageHeadingTabTag'		=> $this->oProps->strPageHeadingTabTag,			// string			
		);
		$this->oProps->arrPages[ $strPageSlug ] = $this->oUtil->uniteArraysRecursive( $arrThisPage, $arrPreviouslySetPage );
			
	}
	
	/**
	 * Checks if a menu item is a WordPress built-in menu item from the given menu label.
	 * 
	 * @since			2.0.0
	 * @internal
	 * @return			void|string			Returns the associated slug string, if true.
	 */ 
	protected function isBuiltInMenuItem( $strMenuLabel ) {
		
		$strMenuLabelLower = strtolower( $strMenuLabel );
		if ( array_key_exists( $strMenuLabelLower, self::$arrBuiltInRootMenuSlugs ) )
			return self::$arrBuiltInRootMenuSlugs[ $strMenuLabelLower ];
		
	}
	
	/**
	 * Registers the root menu page.
	 * 
	 * @since			2.0.0
	 */ 
	private function registerRootMenuPage() {

		$strHookName = add_menu_page(  
			$this->oProps->strClassName,						// Page title - will be invisible anyway
			$this->oProps->arrRootMenu['strTitle'],				// Menu title - should be the root page title.
			$this->oProps->strCapability,						// Capability - access right
			$this->oProps->arrRootMenu['strPageSlug'],			// Menu ID 
			'', //array( $this, $this->oProps->strClassName ), 	// Page content displaying function
			$this->oProps->arrRootMenu['strURLIcon16x16'],		// icon path
			isset( $this->arrRootMenu['intPosition'] ) ? $this->arrRootMenu['intPosition'] : null	// menu position
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
	private function registerSubMenuPage( $arrArgs ) {
	
		// Variables
		$strType = $arrArgs['strType'];	// page or link
		$strTitle = $strType == 'page' ? $arrArgs['strPageTitle'] : $arrArgs['strMenuTitle'];
		$strCapability = $arrArgs['strCapability'];
			
		// Check the capability
		$strCapability = isset( $strCapability ) ? $strCapability : $this->strCapability;
		if ( ! current_user_can( $strCapability ) ) return;		
		
		// Add the sub-page to the sub-menu
		$arrResult = array();
		$strRootPageSlug = $this->oProps->arrRootMenu['strPageSlug'];
		
		if ( $strType == 'page' && isset( $arrArgs['strPageSlug'] ) ) {		
		// it's possible that the strPageSlug key is not set 
		// if the user uses a method like showPageHeadingTabs() prior to addSubMenuItam().
			
			$strPageSlug = $arrArgs['strPageSlug'];
			$arrResult[ $strPageSlug ] = add_submenu_page( 
				$strRootPageSlug,						// the root(parent) page slug
				$strTitle,								// page_title
				$strTitle,								// menu_title
				$strCapability,				 			// strCapability
				$strPageSlug,	// menu_slug
				// In admin.php ( line 149 of WordPress v3.6.1 ), do_action($page_hook) ( where $page_hook is $arrResult[ $strPageSlug ] )
				// will be executed and it triggers the __call magic method with the method name of "md5 class hash + _page_ + this page slug".
				array( $this, $this->oProps->strClassHash . '_page_' . $strPageSlug )
			);			
			
			add_action( "load-" . $arrResult[ $strPageSlug ] , array( $this, "load_pre_" . $strPageSlug ) );
				
		} 
		if ( $strType == 'link' )
			$GLOBALS['submenu'][ $strRootPageSlug ][] = array ( 
				$strTitle, 
				$strCapability, 
				$arrArgs['strURL'],
			);	
	
		return $arrResult;	// maybe useful to debug.

	}
	
	/**
	 * Builds menus.
	 * 
	 * @since			2.0.0
	 */
	public function buildMenus() {
		
		// If the root menu label is not set but the slug is set, 
		if ( $this->oProps->arrRootMenu['fCreateRoot'] ) 
			$this->registerRootMenuPage();
		
		// Apply filters to let other scripts add sub menu pages.
		$this->oProps->arrPages = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $strFilter, $vInput, $vArgs...
			$this,
			"{$this->oProps->strClassName}_pages", 
			$this->oProps->arrPages
		);
		
		// Sort the page array.
		uasort( $this->oProps->arrPages, array( $this->oProps, 'sortByOrder' ) ); 
		
		// Set the default page, the first element.
		foreach ( $this->oProps->arrPages as $arrPage ) {
			
			if ( ! isset( $arrPage['strPageSlug'] ) ) continue;
			$this->oProps->strDefaultPageSlug = $arrPage['strPageSlug'];
			break;
			
		}
		
		// Register them.
		foreach ( $this->oProps->arrPages as &$arrSubMenuItem ) 
			$this->oProps->arrRegisteredSubMenuPages = $this->registerSubMenuPage( $arrSubMenuItem );
		
					
		// After adding the sub menus, if the root menu is created, remove the page that is automatically created when registering the root menu.
		if ( $this->oProps->arrRootMenu['fCreateRoot'] ) 
			remove_submenu_page( $this->oProps->arrRootMenu['strPageSlug'], $this->oProps->arrRootMenu['strPageSlug'] );
		
	}	
}
endif;

if ( ! class_exists( 'AdminPageFramework_SettingsAPI' ) ) :
/**
 * Provides methods to add form elements with WordPress Settings API. 
 *
 * @abstract
 * @since		2.0.0
 * @extends		AdminPageFramework_Menu
 * @package		Admin Page Framework
 * @subpackage	Admin Page Framework - Page
 * @staticvar	array		$arrStructure_Section				represents the structure of the form section array.
 * @staticvar	array		$arrStructure_Field					represents the structure of the form field array.
 * @var			array		$arrFieldErrors						stores the settings field errors.
 * @var			boolean		$fIsMediaUploaderScriptEnqueued		indicates whether the JavaScript script for media uploader is enqueued.
 * @var			boolean		$fIsImageFieldScriptEnqueued		indicates whether the JavaScript script for image selector is enqueued.
 * @var			boolean		$fIsColorFieldScriptEnqueued		indicates whether the JavaScript script for color picker is enqueued.
 * @var			boolean		$fIsDateFieldScriptEnqueued			indicates whether the JavaScript script for date picker is enqueued.
 */
abstract class AdminPageFramework_SettingsAPI extends AdminPageFramework_Menu {
	
	/**
	 * Represents the structure of the form section array.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds array structure of form section.
	 * @static
	 * @internal
	 */ 	
	protected static $arrStructure_Section = array(	
		'strSectionID' => null,
		'strPageSlug' => null,
		'strTabSlug' => null,
		'strTitle' => null,
		'strDescription' => null,
		'strCapability' => null,
		'fIf' => true,	
		'numOrder' => null,	// do not set the default number here because incremented numbers will be added when registering the sections.
		'strHelp' => null,
		'strHelpAside' => null,
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
	protected static $arrStructure_Field = array(
		'strFieldID'		=> null, 		// ( mandatory )
		'strSectionID'		=> null,		// ( mandatory )
		'strSectionTitle'	=> null,		// This will be assigned automatically in the formatting method.
		'strType'			=> null,		// ( mandatory )
		'strPageSlug'		=> null,		// This will be assigned automatically in the formatting method.
		'strTabSlug'		=> null,		// This will be assigned automatically in the formatting method.
		'strOptionKey'		=> null,		// This will be assigned automatically in the formatting method.
		'strClassName'		=> null,		// This will be assigned automatically in the formatting method.
		'strCapability'		=> null,		
		'strTitle'			=> null,
		'strTip'			=> null,
		'strDescription'	=> null,
		'strName'			=> null,		// the name attribute of the input field.
		'strError'			=> null,		// error message for the field
		'strBeforeField'	=> null,
		'strAfterField'		=> null,
		'fIf' 				=> true,
		'numOrder'			=> null,	// do not set the default number here for this key.		
		'strHelp'			=> null,	// since 2.1.0
		'strHelpAside'		=> null,	// since 2.1.0
		'fRepeatable'		=> null,	// since 2.1.3
	);	
	
	/**
	 * Stores the settings field errors. 
	 * 
	 * @since			2.0.0
	 * @var				array			Stores field errors.
	 * @internal
	 */ 
	protected $arrFieldErrors;		// Do not set a value here since it is checked to see it's null.
	
	/**
	 * A flag that indicates whether the JavaScript script for media uploader is enqueued.
	 * 
	 * @since			2.0.0
	 * @internal
	 */ 
	protected $fIsMediaUploaderScriptEnqueued = false;
	
	/**
	 * A flag that indicates whether the JavaScript script for image selector is enqueued.
	 * 
	 * @since			2.0.0
	 * @internal
	 */ 	
	protected $fIsImageFieldScriptEnqueued = false;	
	
	/**
	 * A flag that indicates whether the JavaScript script for taxonomy checklist boxes.
	 * 
	 * @since			2.1.1
	 * @internal
	 */
	protected $fIsTaxonomyChecklistScriptAdded = false;
	
	/**
	 * A flag that indicates whether the JavaScript script for color picker is enqueued.
	 * 
	 * @since			2.0.0
	 * @internal
	 */ 		
	protected $fIsColorFieldScriptEnqueued = false;	
	
	/**
	 * A flag that indicates whether the JavaScript script for date picker is enqueued.
	 * 
	 * @since			2.0.0
	 * @internal
	 */ 			
	protected $fIsDateFieldScriptEnqueued = false;	
		
	/**
	* Sets the given message to be displayed in the next page load. 
	* 
	* This is used to inform users about the submitted input data, such as "Updated successfully." or "Problem occurred." etc. and normally used in validation callback methods.
	* 
	* <h4>Example</h4>
	* <code>if ( ! $fVerified ) {
	*		$this->setFieldErrors( $arrErrors );		
	*		$this->setSettingNotice( 'There was an error in your input.' );
	*		return $arrOldPageOptions;
	*	}</code>
	*
	* @since			2.0.0
	* @since			2.1.2			Added a check to prevent duplicate items.
	* @access 			protected
	* @remark			The user may use this method in their extended class definition.
	* @param			string		$strMsg					the text message to be displayed.
	* @param			string		$strType				( optional ) the type of the message, either "error" or "updated"  is used.
	* @param			string		$strID					( optional ) the ID of the message. This is used in the ID attribute of the message HTML element.
	* @return			void
	*/		
	protected function setSettingNotice( $strMsg, $strType='error', $strID=null ) {
		
		// Check if the same message has been added already.
		$arrWPSettingsErrors = isset( $GLOBALS['wp_settings_errors'] ) ? ( array ) $GLOBALS['wp_settings_errors'] : array();
		foreach( $arrWPSettingsErrors as $arrSettingsError ) {
			
			if ( $arrSettingsError['setting'] != $this->oProps->strOptionKey )
				continue;
			
			// If the same message is added, no need to add another.
			if ( $arrSettingsError['message'] == $strMsg ) 
				return;
			
		}
		
		add_settings_error( 
			$this->oProps->strOptionKey, // the script specific ID so the other settings error won't be displayed with the settings_errors() function.
			isset( $strID ) ? $strID : ( isset( $_GET['page'] ) ? $_GET['page'] : $this->oProps->strOptionKey ), 	// the id attribute for the message div element.
			$strMsg,	// error or updated
			$strType
		);
					
	}

	/**
	* Adds the given form section items into the property. 
	* 
	* The passed section array must consist of the following keys.
	* 
	* <strong>Section Array</strong>
	* <ul>
	* <li><strong>strSectionID</strong> - ( string ) the section ID. Avoid using non-alphabetic characters exept underscore and numbers.</li>
	* <li><strong>strPageSlug</strong> - (  string ) the page slug that the section belongs to.</li>
	* <li><strong>strTabSlug</strong> - ( optional, string ) the tab slug that the section belongs to.</li>
	* <li><strong>strTitle</strong> - ( optional, string ) the title of the section.</li>
	* <li><strong>strCapability</strong> - ( optional, string ) the <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> of the section. If the page visitor does not have sufficient capability, the section will be invisible to them.</li>
	* <li><strong>fIf</strong> - ( optional, boolean ) if the passed value is false, the section will not be registered.</li>
	* <li><strong>numOrder</strong> - ( optional, integer ) the order number of the section. The higher the number is, the lower the position it gets.</li>
	* <li><strong>strHelp</strong> - ( optional, string ) the help description added to the contextual help tab.</li>
	* <li><strong>strHelpAside</strong> - ( optional, string ) the additional help description for the side bar of the contextual help tab.</li>
	* </ul>
	* 
	* <h4>Example</h4>
	* <code>$this->addSettingSections(
	*		array(
	*			'strSectionID'		=> 'text_fields',
	*			'strPageSlug'		=> 'first_page',
	*			'strTabSlug'		=> 'textfields',
	*			'strTitle'			=> 'Text Fields',
	*			'strDescription'	=> 'These are text type fields.',
	*			'numOrder'			=> 10,
	*		),	
	*		array(
	*			'strSectionID'		=> 'selectors',
	*			'strPageSlug'		=> 'first_page',
	*			'strTabSlug'		=> 'selectors',
	*			'strTitle'			=> 'Selectors and Checkboxes',
	*			'strDescription'	=> 'These are selector type options such as dropdown lists, radio buttons, and checkboxes',
	*		)</code>
	*
	* @since			2.0.0
	* @access 			protected
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>registerSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			array		$arrSection1				the section array.
	* @param			array		$arrSection2				( optional ) another section array.
	* @param			array		$_and_more					( optional ) add more section array to the next parameters as many as necessary.
	* @return			void
	*/		
	protected function addSettingSections( $arrSection1, $arrSection2=null, $_and_more=null ) {	
				
		foreach( func_get_args() as $arrSection ) 
			$this->addSettingSection( $arrSection );
			
	}
	
	/**
	 * A singular form of the adSettingSections() method which takes only a single parameter.
	 * 
	 * This is useful when adding section arrays in loops.
	 * 
	 * @since			2.1.2
	 * @access			protected
	 * @param			array		$arrSection				the section array.
	 * @remark			The user may use this method in their extended class definition.
	 * @remark			The actual registration will be performed in the <em>registerSettings()</em> method with the <em>admin_menu</em> hook.
	 */
	protected function addSettingSection( $arrSection ) {
		
		$strCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;		
		
		if ( ! is_array( $arrSection ) ) return;

		$arrSection = $arrSection + self::$arrStructure_Section;	// avoid undefined index warnings.
		
		// Sanitize the IDs since they are used as a callback method name, the slugs as well.
		$arrSection['strSectionID'] = $this->oUtil->sanitizeSlug( $arrSection['strSectionID'] );
		$arrSection['strPageSlug'] = $this->oUtil->sanitizeSlug( $arrSection['strPageSlug'] );
		$arrSection['strTabSlug'] = $this->oUtil->sanitizeSlug( $arrSection['strTabSlug'] );
		
		if ( ! isset( $arrSection['strSectionID'], $arrSection['strPageSlug'] ) ) return;	// these keys are necessary.
		
		// If the page slug does not match the current loading page, there is no need to register form sections and fields.
		if ( $GLOBALS['pagenow'] != 'options.php' && ! $strCurrentPageSlug || $strCurrentPageSlug !=  $arrSection['strPageSlug'] ) return;				

		// If the custom condition is set and it's not true, skip.
		if ( ! $arrSection['fIf'] ) return;
		
		// If the access level is set and it is not sufficient, skip.
		$arrSection['strCapability'] = isset( $arrSection['strCapability'] ) ? $arrSection['strCapability'] : $this->oProps->strCapability;
		if ( ! current_user_can( $arrSection['strCapability'] ) ) return;	// since 1.0.2.1
		
		$this->oProps->arrSections[ $arrSection['strSectionID'] ] = $arrSection;	
			
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
	* @param			string			$strSectionID1			the section ID to remove.
	* @param			string			$strSectionID2			( optional ) another section ID to remove.
	* @param			string			$_and_more				( optional ) add more section IDs to the next parameters as many as necessary.
	* @return			void
	*/	
	protected function removeSettingSections( $strSectionID1=null, $strSectionID2=null, $_and_more=null ) {	
		
		foreach( func_get_args() as $strSectionID ) 
			if ( isset( $this->oProps->arrSections[ $strSectionID ] ) )
				unset( $this->oProps->arrSections[ $strSectionID ] );
		
	}
	
	/**
	* Adds the given field array items into the field array property.
	* 
	* The passed field array must consist of the following keys. 
	* 
	* <h4>Field Array</h4>
	* <ul>
	* 	<li><strong>strFieldID</strong> - ( string ) the field ID. Avoid using non-alphabetic characters exept underscore and numbers.</li>
	* 	<li><strong>strSectionID</strong> - ( string ) the section ID that the field belongs to.</li>
	* 	<li><strong>strType</strong> - ( string ) the type of the field. The supported types are listed below.</li>
	* 	<li><strong>strTitle</strong> - ( optional, string ) the title of the section.</li>
	* 	<li><strong>strDescription</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	* 	<li><strong>strTip</strong> - ( optional, string ) the tip for the field which is displayed when the mouse is hovered over the field title.</li>
	* 	<li><strong>strCapability</strong> - ( optional, string ) the http://codex.wordpress.org/Roles_and_Capabilities">access level of the section. If the page visitor does not have sufficient capability, the section will be invisible to them.</li>
	* 	<li><strong>strName</strong> - ( optional, string ) the name attribute value of the input tag instead of automatically generated one.</li>
	* 	<li><strong>strError</strong> - ( optional, string ) the error message to display above the input field.</li>
	* 	<li><strong>strBeforeField</strong> - ( optional, string ) the HTML string to insert before the input field output.</li>
	* 	<li><strong>strAfterField</strong> - ( optional, string ) the HTML string to insert after the input field output.</li>
	* 	<li><strong>fIf</strong> - ( optional, boolean ) if the passed value is false, the section will not be registered.</li>
	* 	<li><strong>numOrder</strong> - ( optional, integer ) the order number of the section. The higher the number is, the lower the position it gets.</li>
	* 	<li><strong>vLabel</strong> - ( optional|mandatory, string|array ) the text label(s) associated with and displayed along with the input field. Some input types can ignore this key while some require it.</li>
	* 	<li><strong>vDefault</strong> - ( optional, string|array ) the default value(s) assigned to the input tag's value attribute.</li>
	* 	<li><strong>vValue</strong> - ( optional, string|array ) the value(s) assigned to the input tag's <em>value</em> attribute to override the default or stored value.</li>
	* 	<li><strong>vDelimiter</strong> - ( optional, string|array ) the HTML string that delimits multiple elements. This is available if the <var>vLabel</var> key is passed as array.</li>
	* 	<li><strong>vBeforeInputTag</strong> - ( optional, string|array ) the HTML string inserted right before the input tag.</li>
	* 	<li><strong>vAfterInputTag</strong> - ( optional, string|array ) the HTML string inserted right after the input tag.</li>
	* 	<li><strong>vClassAttribute</strong> - ( optional, string|array ) the value(s) assigned to the input tag's <em>class</em>.</li>
	* 	<li><strong>vLabelMinWidth</strong> - ( optional, string|array ) the inline style property of the <em>min-width</em> of the label tag for the field.</li>
	* 	<li><strong>vDisable</strong> - ( optional, boolean|array ) if this is set to true, the <em>disabled</em> attribute will be inserted into the field input tag.</li>
	*	<li><strong>strHelp</strong> - ( optional, string ) the help description added to the contextual help tab.</li>
	*	<li><strong>strHelpAside</strong> - ( optional, string ) the additional help description for the side bar of the contextual help tab.</li>
	* </ul>
	* <h4>Field Types</h4>
	* <p>Each field type uses specific array keys.</p>
	* <ul>
	* 	<li><strong>text</strong> - a text input field which allows the user to type text.</li>
	* 		<ul>
	* 			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>fRepeatable</strong> - ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>
	* 		</ul>
	* 	<li><strong>password</strong> - a password input field which allows the user to type text.</li>
	* 		<ul>
	* 			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>fRepeatable</strong> - ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>	* 
	* 		</ul>
	* 	<li><strong>datetime, datetime-local, email, month, search, tel, time, url, week</strong> - HTML5 input fields types. Some browsers do not support these.</li>
	* 		<ul>
	* 			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 		</ul>
	* 	<li><strong>number, range</strong> - HTML5 input field types. Some browsers do not support these.</li>
	* 		<ul>
	* 			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the <em>size</em> attribute of the input field.</li>
	* 			<li><strong>vMax</strong> - ( optional, integer|array ) the number that indicates the <em>max</em> attribute of the input field.</li>
	* 			<li><strong>vMin</strong> - ( optional, integer|array ) the number that indicates the <em>min</em> attribute of the input field.</li>
	* 			<li><strong>vStep</strong> - ( optional, integer|array ) the number that indicates the <em>step</em> attribute of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>fRepeatable</strong> - ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>
	* 		</ul>
	* 	<li><strong>textarea</strong> - a textarea input field. The following array keys are supported.
	* 		<ul>
	*			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vRows</strong> - ( optional, integer|array ) the number of rows of the textarea field.</li>
	* 			<li><strong>vCols</strong> - ( optional, integer|array ) the number of cols of the textarea field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>vRich</strong> - ( optional, array ) to make it a rich text editor pass a non-empty value. It accept a setting array of the <code>_WP_Editors</code> class defined in the core.
	* For more information, see the argument section of <a href="http://codex.wordpress.org/Function_Reference/wp_editor" target="_blank">this page</a>.
	* 			</li>
	* 			<li><strong>fRepeatable</strong> - ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields. It's not supported for the rich editor.</li>
	*		</ul>
	* 	</li>
	* 	<li><strong>radio</strong> - a radio button input field.</li>
	* 	<li><strong>checkbox</strong> - a check box input field.</li>
	* 	<li><strong>select</strong> - a dropdown input field.</li>
	* 		<ul>
	* 			<li><strong>vMultiple</strong> - ( optional, boolean|array ) if this is set to true, the <em>multiple</em> attribute will be inserted into the field input tag, which enables the multiple selections for the user.</li>
	* 			<li><strong>vWidth</strong> - ( optional, integer|array ) the width of the dropdown list.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the <em>size</em> attribute of the input field.</li>
	* 		</ul>
	* 	<li><strong>size</strong> - a size input filed. This is a combination of number and select fields.</li>
	* 		<ul>
	* 			<li>
	* 				<strong>vSizeUnits</strong> - ( optional, array ) defines the units to show. e.g. <code>array( 'px' => 'px', '%' => '%', 'em' => 'em'  )</code> 
	* 				Default: <code>array( 'px' => 'px', '%' => '%', 'em' => 'em', 'ex' => 'ex', 'in' => 'in', 'cm' => 'cm', 'mm' => 'mm', 'pt' => 'pt', 'pc' => 'pc' )</code>
	* 			</li>
	* 			<li><strong>vMultiple</strong> - ( optional, boolean|array ) if this is set to true, the <em>multiple</em> attribute will be inserted into the field input tag, which enables the multiple selections for the user.</li>
	* 			<li><strong>vWidth</strong> - ( optional, integer|array ) the width of the dropdown list.</li>
	* 			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the <em>size</em> attribute of the input field.</li>
	* 			<li><strong>vMax</strong> - ( optional, integer|array ) the number that indicates the <em>max</em> attribute of the input field.</li>
	* 			<li><strong>vMin</strong> - ( optional, integer|array ) the number that indicates the <em>min</em> attribute of the input field.</li>
	* 			<li><strong>vStep</strong> - ( optional, integer|array ) the number that indicates the <em>step</em> attribute of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the <em>size</em> attribute of the input field.</li>
	* 	</ul>
	* 	<li><strong>hidden</strong> - a hidden input field.</li>
	* 	<li><strong>file</strong> - a file upload input field.</li>
	* 	<li><strong>submit</strong> - a submit button input field.</li>
	* 		<ul>
	* 			<li><strong>vLink</strong> - ( optional, string|array ) the url(s) linked to the submit button.</li>
	* 			<li><strong>vRedirect</strong> - ( optional, string|array ) the url(s) redirected to after submitting the input form.</li>
	* 			<li><strong>vReset</strong> - [+2.1.2] ( optional, string|array ) the option key to delete. Set 1 for the entire option.</li>
	* 		</ul>
	* 	<li><strong>import</strong> - an inport input field. This is a custom file and submit field.</li>
	* 		<ul>
	* 			<li><strong>vAcceptAttribute</strong> - ( optional, string|array )</li>
	* 			<li><strong>vImportOptionKey</strong> - ( optional, string|array )</li>
	* 			<li><strong>vImportFormat</strong> - ( optional, string|array )</li>
	* 		</ul>
	* 	<li><strong>export</strong> - an export input field. This is a custom submit field.</li>
	* 		<ul>
	* 			<li><strong>vAcceptAttribute</strong> - ( optional, string|array )</li>
	* 			<li><strong>vExportFileName</strong> - ( optional, string|array )</li>
	* 			<li><strong>vExportFormat</strong> - ( optional, string|array )</li>
	* 			<li><strong>vExportData</strong> - ( optional, string|array|object )</li>
	* 		</ul>
	* 	<li><strong>image</strong> - an image input field. This is a custom text with a JavaScript script.</li>
	* 		<ul>
	*			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>vImagePreview</strong> - ( optional, boolean|array ) if this is set to false, the image preview will be disabled.</li>
	* 			<li><strong>strTickBoxTitle</strong> - ( optional, string ) the text label displayed in the media uploader box's title.</li>
	* 			<li><strong>strLabelUseThis</strong> - ( optional, string ) the text label displayed in the button of the media uploader to set the image.</li>
	* 			<li><strong>fRepeatable</strong> - ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>
	* 		</ul>
	* 	<li><strong>color</strong> - a color picker input field. This is a custom text field with a JavaScript script.</li>
	* 		<ul>
	*			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 		</ul>
	* 	<li><strong>date</strong> - a date picker input field. This is a custom text field with a JavaScript script.</li>
	* 		<ul>
	*			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>vDateFormat</strong> - ( optional, string|array ) the date format. The syntax follows the one used <a href="http://api.jqueryui.com/datepicker/#utility-formatDate">here</a>.</li>
	* 			<li><strong>fRepeatable</strong> - ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>
	* 		</ul>
	* 	<li><strong>taxonomy</strong> - a taxonomy check list. This is a set of check boxes listing a specified taxonomy. This does not accept to create multiple fields by passing an array of labels.</li>
	* 		<ul>
	*			<li><strong>vTaxonomySlug</strong> - ( optional, string|array ) the taxonomy slug to list.</li>
	*			<li><strong>strWidth</strong> - ( optional, string ) the inline style property value of <em>max-width</em> of this element. Include the unit such as px, %. Default: 100%</li>
	*			<li><strong>strHeight</strong> - ( optional, string ) the inline style property value of <em>height</em> of this element. Include the unit such as px, %. Default: 250px</li>
	* 		</ul>
	* 	<li><strong>posttype</strong> - a posttype check list. This is a set of check boxes listing post type slugs.</li>
	* 		<ul>
	* 			<li><strong>arrRemove</strong> - ( optional, array ) the post type slugs not to be listed. e.g.<code>array( 'revision', 'attachment', 'nav_menu_item' )</code></li>
	* 		</ul>

	* </ul>	
	* 
	* <h4>Example</h4>
	* <code>$this->addSettingFields(
	*		array(	// Single text field
	*			'strFieldID' => 'text',
	*			'strSectionID' => 'text_fields',
	*			'strTitle' => __( 'Text', 'admin-page-framework-demo' ),
	*			'strDescription' => __( 'Type something here.', 'admin-page-framework-demo' ),	// additional notes besides the form field
	*			'strType' => 'text',
	*			'numOrder' => 1,
	*			'vDefault' => 123456,
	*			'vSize' => 40,
	*		),	
	*		array(	// Multiple text fields
	*			'strFieldID' => 'text_multiple',
	*			'strSectionID' => 'text_fields',
	*			'strTitle' => 'Multiple Text Fields',
	*			'strDescription' => 'These are multiple text fields.',	// additional notes besides the form field
	*			'strType' => 'text',
	*			'numOrder' => 2,
	*			'vDefault' => array(
	*				'Hello World',
	*				'Foo bar',
	*				'Yes, we can.'
	*			),
	*			'vLabel' => array( 
	*				'First Item: ', 
	*				'Second Item: ', 
	*				'Third Item: ' 
	*			),
	*			'vSize' => array(
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
	* @param			array			$arrField1			the field array.
	* @param			array			$arrField2			( optional ) another field array.
	* @param			array			$_and_more			( optional ) add more field arrays to the next parameters as many as necessary.
	* @return			void
	*/		
	protected function addSettingFields( $arrField1, $arrField2=null, $_and_more=null ) {	
	
		foreach( func_get_args() as $arrField ) 
			$this->addSettingField( $arrField );

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
	protected function addSettingField( $arrField ) {
		
		if ( ! is_array( $arrField ) ) return;
		
		$arrField = $arrField + self::$arrStructure_Field;	// avoid undefined index warnings.
		
		// Sanitize the IDs since they are used as a callback method name.
		$arrField['strFieldID'] = $this->oUtil->sanitizeSlug( $arrField['strFieldID'] );
		$arrField['strSectionID'] = $this->oUtil->sanitizeSlug( $arrField['strSectionID'] );
		
		// Check the mandatory keys' values are set.
		if ( ! isset( $arrField['strFieldID'], $arrField['strSectionID'], $arrField['strType'] ) ) return;	// these keys are necessary.
		
		// If the custom condition is set and it's not true, skip.
		if ( ! $arrField['fIf'] ) return;			
		
		// If the access level is not sufficient, skip.
		$arrField['strCapability'] = isset( $arrField['strCapability'] ) ? $arrField['strCapability'] : $this->oProps->strCapability;
		if ( ! current_user_can( $arrField['strCapability'] ) ) return; 
				
		// If it's the image type field, extra jQuery scripts need to be loaded.
		if ( $arrField['strType'] == 'image' ) $this->enqueueMediaUploaderScript( $arrField );
				
		$this->oProps->arrFields[ $arrField['strFieldID'] ] = $arrField;		
		
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
	* @param			string			$strFieldID1				the field ID to remove.
	* @param			string			$strFieldID2				( optional ) another field ID to remove.
	* @param			string			$_and_more					( optional ) add more field IDs to the next parameters as many as necessary.
	* @return			void
	*/	
	protected function removeSettingFields( $strFieldID1, $strFieldID2=null, $_and_more ) {
				
		foreach( func_get_args() as $strFieldID ) 
			if ( isset( $this->oProps->arrFields[ $strFieldID ] ) )
				unset( $this->oProps->arrFields[ $strFieldID ] );

	}	
	
	private function enqueueDateFieldScript( &$arrField ) {
		
		if ( $this->fIsDateFieldScriptEnqueued	) return;
		$this->fIsDateFieldScriptEnqueued = true;

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
		
	}
	private function enqueueColorFieldScript( &$arrField ) {
	
		if ( $this->fIsColorFieldScriptEnqueued	) return;
		$this->fIsColorFieldScriptEnqueued	 = true;
				
		// Reference: http://www.sitepoint.com/upgrading-to-the-new-wordpress-color-picker/
		//If the WordPress version is greater than or equal to 3.5, then load the new WordPress color picker.
		if ( 3.5 <= $GLOBALS['wp_version'] ){
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
		
		// Append the script
		//Setup the color pickers to work with our text input field
		// $this->oProps->strScript .= AdminPageFramework_Properties::getColorPickerScript();	// deprecated
	
	}
	private function enqueueMediaUploaderScript() {
		
		if ( $this->fIsMediaUploaderScriptEnqueued	) return;
		$this->fIsMediaUploaderScriptEnqueued = true;	

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueUploaderScripts' ) );	// called later than the admin_menu hook
		add_filter( 'gettext', array( $this, 'replaceThickBoxText' ) , 1, 2 );	

				
	}
	private function addImageFieldScript( &$arrField ) {
					
		if ( $this->fIsImageFieldScriptEnqueued	) return;
		$this->fIsImageFieldScriptEnqueued = true;

		// These two hooks should be enabled when the image field type is added in the field array.
		$this->oProps->strThickBoxTitle = isset( $arrField['strTickBoxTitle'] ) ? $arrField['strTickBoxTitle'] : __( 'Upload Image', 'admin-page-framework' );
		$this->oProps->strThickBoxButtonUseThis = isset( $arrField['strLabelUseThis'] ) ? $arrField['strLabelUseThis'] : __( 'Use This Image', 'admin-page-framework' ); 
		
		// Append the script
		if( ! function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.5 or above		
			$this->oProps->strScript .= AdminPageFramework_Properties::getImageSelectorScript( "admin_page_framework", $this->oProps->strThickBoxTitle, $this->oProps->strThickBoxButtonUseThis );
		
	}
	
	/**
	 * Adds the script for taxonomy checklist tabbed boxes to the property.
	 * 
	 * @since			2.1.1
	 */
	private function addTaxonomyChecklistScript( &$arrField ) {
	
		if ( $this->fIsTaxonomyChecklistScriptAdded	) return;
		$this->fIsTaxonomyChecklistScriptAdded = true;
		
		// Append the script
		$this->oProps->strScript .= AdminPageFramework_Properties::getTaxonomyChecklistScript();
		
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
	protected function doPageLoadCall( $strPageSlug, $strTabSlug, $arrArg ) {

		// Do actions, class name -> page -> in-page tab.
		$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( "load_", $this->oProps->strClassName, $strPageSlug, $strTabSlug, true ) );
		
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
	protected function doValidationCall( $strMethodName, $arrInput ) {
		
		$strTabSlug = isset( $_POST['strTabSlug'] ) ? $_POST['strTabSlug'] : '';	// no need to retrieve the default tab slug here because it's an embedded value that is already set in the previous page. 
		$strPageSlug = isset( $_POST['strPageSlug'] ) ? $_POST['strPageSlug'] : '';

		// Check if custom submit keys are set [part 1]
		if ( isset( $_POST['__import']['submit'], $_FILES['__import'] ) ) 
			return $this->importOptions( $arrInput, $strPageSlug, $strTabSlug );
		if ( isset( $_POST['__export']['submit'] ) ) 
			die( $this->exportOptions( $this->oProps->arrOptions, $strPageSlug, $strTabSlug ) );		
		if ( isset( $_POST['__reset_confirm'] ) && $strPressedFieldName = $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__reset_confirm'], 'key' ) )
			return $this->askResetOptions( $strPressedFieldName, $strPageSlug );			
		if ( isset( $_POST['__link'] ) && $strLinkURL = $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__link'], 'url' ) )
			$this->oUtil->goRedirect( $strLinkURL );	// if the associated submit button for the link is pressed, the will be redirected.
		if ( isset( $_POST['__redirect'] ) && $strRedirectURL = $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__redirect'], 'url' ) )
			$this->setRedirectTransients( $strRedirectURL );
				
		// Apply validation filters - validation_{page slug}_{tab slug}, validation_{page slug}, validation_{instantiated class name}
		$arrInput = $this->getFilteredOptions( $arrInput, $strPageSlug, $strTabSlug );
		
		// Check if custom submit keys are set [part 2] - these should be done after applying the filters.
		if ( isset( $_POST['__reset'] ) && $strKeyToReset = $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__reset'], 'key' ) )
			$arrInput = $this->resetOptions( $strKeyToReset, $arrInput );
		
		// Set the update notice
		$fEmpty = empty( $arrInput );
		add_settings_error( 
			$this->oProps->strOptionKey, 
			$strPageSlug, 
			$fEmpty ? $this->oMsg->___( 'option_cleared' ) : $this->oMsg->___( 'option_updated' ),
			$fEmpty ? 'error' : 'updated' 
		);
		
		return $arrInput;	
		
	}
	
	/**
	 * Displays a confirmation message to the user when a reset button is pressed.
	 * 
	 * @since			2.1.2
	 */
	private function askResetOptions( $strPressedFieldName, $strPageSlug ) {
		
		// Retrieve the pressed button's associated submit field ID and its section ID.
		// $strFieldName = $this->getPressedCustomSubmitButtonFieldName( $_POST['__reset_confirm'] );
		$arrNameKeys = explode( '|', $strPressedFieldName );	
		// $strPageSlug = $arrNameKeys[ 1 ]; 
		$strSectionID = $arrNameKeys[ 2 ]; 
		$strFieldID = $arrNameKeys[ 3 ];
		
		// Set up the field error array.
		$arrErrors = array();
		$arrErrors[ $strSectionID ][ $strFieldID ] = __( 'Are you sure you want to reset options?', 'admin-page-framework' );
		$this->setFieldErrors( $arrErrors );
		
		// Set a flag that the confirmation is displayed
		set_transient( md5( "reset_confirm_" . $strPressedFieldName ), $strPressedFieldName, 60*2 );
		
		$this->setSettingNotice( __( 'Please confirm if you want to perform the specified task.', 'admin-page-framework' ) );
		
		return $this->getPageOptions( $strPageSlug ); 			
		
	}
	/**
	 * Performs reset options.
	 * 
	 * @since			2.1.2
	 * @remark			$arrInput has only the page elements that called the validation callback. In other words, it does not hold other pages' option keys.
	 */
	private function resetOptions( $strKeyToReset, $arrInput ) {
		
		if ( $strKeyToReset == 1 or $strKeyToReset === true ) {
			delete_option( $this->oProps->strOptionKey );
			$this->setSettingNotice( __( 'The options has been reset.', 'admin-page-framework' ) );
			$this->setSettingNotice( __( 'The options has been reset.', 'admin-page-framework' ) );
			return array();
		}
		
		unset( $this->oProps->arrOptions[ trim( $strKeyToReset ) ] );
		unset( $arrInput[ trim( $strKeyToReset ) ] );
		update_option( $this->oProps->strOptionKey, $this->oProps->arrOptions );
		$this->setSettingNotice( __( 'The specified options has been deleted.', 'admin-page-framework' ) );
		
		return $arrInput;	// the returned array will be saved with the Settings API.
	}
	
	private function setRedirectTransients( $strURL ) {
		if ( empty( $strURL ) ) return;
		$strTransient = md5( trim( "redirect_{$this->oProps->strClassName}_{$_POST['strPageSlug']}" ) );
		return set_transient( $strTransient, $strURL , 60*2 );
	}
	
	
	/**
	 * Returns the flattened string containing the filed key information of the pressed custom submit button.
	 * 
	 */
/* 	private function getPressedCustomSubmitButtonFieldName( $arrPostElements ) {
		
		foreach( $arrPostElements as $strFieldName => $arrSubElements ) {
			
			$arrNameKeys = explode( '|', $arrSubElements['name'] );	
			
			// Count of 4 means it's a single element. Count of 5 means it's one of multiple elements.
			// The isset() checks if the associated button is actually pressed or not.
			if ( count( $arrNameKeys ) == 4 && isset( $_POST[ $arrNameKeys[0] ][ $arrNameKeys[1] ][ $arrNameKeys[2] ][ $arrNameKeys[3] ] ) )
				return $arrSubElements['name'];
			if ( count( $arrNameKeys ) == 5 && isset( $_POST[ $arrNameKeys[0] ][ $arrNameKeys[1] ][ $arrNameKeys[2] ][ $arrNameKeys[3] ][ $arrNameKeys[4] ] ) )
				return $arrSubElements['name'];
				
		}
		return '';
	}
	 */
	
	/**
	 * Retrieves the target key value associated with the given data to a custom submit button.
	 * 
	 * This method checks if the associated submit button is pressed with the input fields whose name property starts with __link or __redirect. 
	 * The custom ( currently __link or __redirect is supported ) input array should contain the 'name' and 'url' keys and their values.
	 * 
	 * @since			2.0.0
	 * @return			mixed			Returns null if no button is found and the associated link url if found. Otherwise, the URL associated with the button.
	 */ 
	private function getPressedCustomSubmitButtonSiblingValue( $arrPostElements, $strTargetKey='url' ) {	
	
		foreach( $arrPostElements as $strFieldName => $arrSubElements ) {
			
			/*
			 * $arrSubElements['name']	- the input field name property of the submit button, delimited by pipe (|) e.g. APF_GettingStarted|first_page|submit_buttons|submit_button_link
			 * $arrSubElements['url']	- the URL to redirect to. e.g. http://www.somedomain.com
			 * */
			$arrNameKeys = explode( '|', $arrSubElements['name'] );
			
			// Count of 4 means it's a single element. Count of 5 means it's one of multiple elements.
			// The isset() checks if the associated button is actually pressed or not.
			if ( count( $arrNameKeys ) == 4 && isset( $_POST[ $arrNameKeys[0] ][ $arrNameKeys[1] ][ $arrNameKeys[2] ][ $arrNameKeys[3] ] ) )
				return $arrSubElements[ $strTargetKey ];
			if ( count( $arrNameKeys ) == 5 && isset( $_POST[ $arrNameKeys[0] ][ $arrNameKeys[1] ][ $arrNameKeys[2] ][ $arrNameKeys[3] ][ $arrNameKeys[4] ] ) )
				return $arrSubElements[ $strTargetKey ];
				
		}
		
		return null;	// not found
		
	}

	private function importOptions( $arrInput, $strPageSlug, $strTabSlug ) {
	
		$oImport = new AdminPageFramework_ImportOptions( $_FILES['__import'], $_POST['__import'] );
	
		// Check if there is an upload error.
		if ( $oImport->getError() > 0 ) {
			add_settings_error( 
				$this->oProps->strOptionKey, 
				$strPageSlug,
				$this->oMsg->___( 'import_error' ),
				'error'
			);			
			return $arrInput;	// do not change the framework's options.
		}
		
		// Check the uploaded file type.
		if ( ! in_array( $oImport->getType(), array( 'text/plain', 'application/octet-stream' ) ) ) {	// .json file is dealt as binary file.
			add_settings_error( 
				$this->oProps->strOptionKey, 
				$strPageSlug,
				$this->oMsg->___( 'uploaded_file_type_not_supported' ),
				'error'
			);			
			return $arrInput;	// do not change the framework's options.
		}
		
		// Retrieve the importing data.
		$vData = $oImport->getImportData();
		if ( $vData === false ) {
			add_settings_error( 
				$this->oProps->strOptionKey, 
				$strPageSlug,
				$this->oMsg->___( 'could_not_load_importing_data' ),
				'error'
			);			
			return $arrInput;	// do not change the framework's options.
		}
		
		// Apply filters to the data format type.
		$strFormatType = $this->oUtil->addAndApplyFilters(
			$this,
			$this->oUtil->getFilterArrayByPrefix( 'import_format_', $this->oProps->strClassName, $strPageSlug, $strTabSlug ),
			$oImport->getFormatType(),	// the set format type, array, json, or text.
			$oImport->getFieldID()	// additional argument
		);	// import_format_{$strPageSlug}_{$strTabSlug}, import_format_{$strPageSlug}, import_format_{$strClassName}		

		// Format it.
		$oImport->formatImportData( $vData, $strFormatType );	// it is passed as reference.
		
		// Apply filters to the importing data.
		$vData = $this->oUtil->addAndApplyFilters(
			$this,
			$this->oUtil->getFilterArrayByPrefix( 'import_', $this->oProps->strClassName, $strPageSlug, $strTabSlug ),
			$vData,
			$oImport->getFieldID()
		);
				
		// Set the admin notice.
		add_settings_error( 
			$this->oProps->strOptionKey, 
			$strPageSlug,
			$this->oMsg->___( 'imported_data' ),
			'updated'
		);			
				
		// If a custom option key is set,
		// Apply filters to the importing option key.
		$strImportOptionKey = $this->oUtil->addAndApplyFilters(
			$this,
			$this->oUtil->getFilterArrayByPrefix( 'import_option_key_', $this->oProps->strClassName, $strPageSlug, $strTabSlug ),
			$oImport->getImportOptionKey(),	// the set option key, by default it's the value of $this->oProps->strOptionKey.
			$oImport->getFieldID()	// additional argument
		);	// import_option_key_{$strPageSlug}_{$strTabSlug}, import_option_key_{$strPageSlug}, import_option_key_{$strClassName}		
		if ( $strImportOptionKey != $this->oProps->strOptionKey ) {
			update_option( $strImportOptionKey, $vData );
			return $arrInput;	// do not change the framework's options.
		}
		
		return $vData;
						
	}
	private function exportOptions( $vData, $strPageSlug, $strTabSlug ) {

		$oExport = new AdminPageFramework_ExportOptions( $_POST['__export'], $this->oProps->strClassName );

		// If the data is set in transient,
		$vData = $oExport->getTransientIfSet( $vData );
	
		// Get the filed ID.
		$strFieldID = $oExport->getFieldID();
	
		// Add and apply filters. - adding filters must be done in this class because the callback method belongs to this class 
		// and the magic method should be triggered.		
		$vData = $this->oUtil->addAndApplyFilters(	
			$this,
			$this->oUtil->getFilterArrayByPrefix( 'export_', $this->oProps->strClassName, $strPageSlug, $strTabSlug ),
			$vData,
			$strFieldID
		);	// export_{$strPageSlug}_{$strTabSlug}, export_{$strPageSlug}, export_{$strClassName}
		$strFileName = $this->oUtil->addAndApplyFilters(
			$this,
			$this->oUtil->getFilterArrayByPrefix( 'export_name_', $this->oProps->strClassName, $strPageSlug, $strTabSlug ),
			$oExport->getFileName(),
			$strFieldID
		);	// export_name_{$strPageSlug}_{$strTabSlug}, export_name_{$strPageSlug}, export_name_{$strClassName}
		$strFormatType = $this->oUtil->addAndApplyFilters(
			$this,
			$this->oUtil->getFilterArrayByPrefix( 'export_format_', $this->oProps->strClassName, $strPageSlug, $strTabSlug ),
			$oExport->getFormat(),
			$strFieldID
		);	// export_format_{$strPageSlug}_{$strTabSlug}, export_format_{$strPageSlug}, export_format_{$strClassName}
					
		$oExport->doExport( $vData, $strFileName, $strFormatType );
		exit;
		
	}
	private function getFilteredOptions( $arrInput, $strPageSlug, $strTabSlug ) {

		$arrStoredPageOptions = $this->getPageOptions( $strPageSlug ); 			

		// for tabs
		if ( $strTabSlug && $strPageSlug )	{
			$arrRegisteredSectionKeysForThisTab = isset( $arrInput[ $strPageSlug ] ) ? array_keys( $arrInput[ $strPageSlug ] ) : array();			
			$arrInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$strPageSlug}_{$strTabSlug}", $arrInput, $arrStoredPageOptions );	
			$arrInput = $this->oUtil->uniteArraysRecursive( $arrInput, $this->getOtherTabOptions( $strPageSlug, $arrRegisteredSectionKeysForThisTab ) );
		}
		// for pages	
		if ( $strPageSlug )	{
			$arrInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$strPageSlug}", $arrInput, $arrStoredPageOptions );		
			$arrInput = $this->oUtil->uniteArraysRecursive( $arrInput, $this->getOtherPageOptions( $strPageSlug ) );
		}
		// for the class
		$arrInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProps->strClassName}", $arrInput, $this->oProps->arrOptions );

		return $arrInput;
	
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
	private function getPageOptions( $strPageSlug ) {
				
		$arrStoredPageOptions = array();
		if ( isset( $this->oProps->arrOptions[ $strPageSlug ] ) )
			$arrStoredPageOptions[ $strPageSlug ] = $this->oProps->arrOptions[ $strPageSlug ];
		
		return $arrStoredPageOptions;
		
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
	private function getOtherTabOptions( $strPageSlug, $arrSectionKeysForTheTab ) {
	
		$arrOtherTabOptions = array();
		if ( isset( $this->oProps->arrOptions[ $strPageSlug ] ) )
			$arrOtherTabOptions[ $strPageSlug ] = $this->oProps->arrOptions[ $strPageSlug ];
			
		// Remove the elements of the given keys so that the other stored elements will remain. 
		// They are the other form section elements which need to be returned.
		foreach( $arrSectionKeysForTheTab as $arrSectionKey ) 
			unset( $arrOtherTabOptions[ $strPageSlug ][ $arrSectionKey ] );
			
		return $arrOtherTabOptions;
		
	}
	
	/**
	 * Retrieves the stored options excluding the key of the given page slug.
	 * 
	 * This is used to merge the submitted form input data with the previously stored option data except the given page.
	 * 
	 * @since			2.0.0
	 * @return			array			the array storing the options excluding the key of the given page slug. 
	 */ 
	private function getOtherPageOptions( $strPageSlug ) {
	
		$arrOtherPageOptions = $this->oProps->arrOptions;
		if ( isset( $arrOtherPageOptions[ $strPageSlug ] ) )
			unset( $arrOtherPageOptions[ $strPageSlug ] );
		return $arrOtherPageOptions;
		
	}
	
	/**
	 * Renders the registered setting fields.
	 * 
	 * @internal
	 * @since			2.0.0
	 * @remark			the protected scope is used because it's called from an extended class.
	 * @return			void
	 */ 
	protected function renderSettingField( $strFieldID, $strPageSlug ) {
			
		// If the specified field does not exist, do nothing.
		if ( ! isset( $this->oProps->arrFields[ $strFieldID ] ) ) return;	// if it is not added, return
		$arrField = $this->oProps->arrFields[ $strFieldID ];
		
		// Retrieve the field error array.
		$this->arrFieldErrors = isset( $this->arrFieldErrors ) ? $this->arrFieldErrors : $this->getFieldErrors( $strPageSlug ); 
		
		// Do render the form field.
		$oField = new AdminPageFramework_InputField( $arrField, $this->oProps->arrOptions, $this->arrFieldErrors, $this->oMsg );
		echo $this->oUtil->addAndApplyFilter(
			$this,
			$this->oProps->strClassName . '_' .  self::$arrPrefixesForCallbacks['field_'] . $strFieldID,	// filter: class name + _ + section_ + section id
			$oField->getInputField( $arrField['strType'] ),	// field output
			$arrField // the field array
		);
		unset( $oField );	// release the object for PHP 5.2.x or below.
		
	}
	
	/**
	 * Retrieves the settings error array set by the user in the validation callback.
	 * 
	 * @since				2.0.0
	 * @since				2.1.2			Added the second parameter. 
	 */
	protected function getFieldErrors( $strPageSlug, $fDelete=true ) {
		
		// If a form submit button is not pressed, there is no need to set the setting errors.
		if ( ! isset( $_GET['settings-updated'] ) ) return null;
		
		// Find the transient.
		$strTransient = md5( $this->oProps->strClassName . '_' . $strPageSlug );
		$arrFieldErrors = get_transient( $strTransient );
		if ( $fDelete )
			delete_transient( $strTransient );	
		return $arrFieldErrors;

	}
	
	/**
	 * Sets the field error array. 
	 * 
	 * This is normally used in validation callback methods. when submitted data have an issue.
	 * This method saves the given array in a temporary area( transient ) of the options database table.
	 * 
	 * <h4>Example</h4>
	 * <code>public function validation_first_page_verification( $arrInput, $arrOldPageOptions ) {	// valication_ + page slug + _ + tab slug			
	 *		$fVerified = true;
	 *		$arrErrors = array();
	 *		// Check if the submitted value meets your criteria. As an example, here a numeric value is expected.
	 *		if ( isset( $arrInput['first_page']['verification']['verify_text_field'] ) && ! is_numeric( $arrInput['first_page']['verification']['verify_text_field'] ) ) {
	 *			// Start with the section key in $arrErrors, not the key of page slug.
	 *			$arrErrors['verification']['verify_text_field'] = 'The value must be numeric: ' . $arrInput['first_page']['verification']['verify_text_field'];	
	 *			$fVerified = false;
	 *		}
	 *		// An invalid value is found.
	 *		if ( ! $fVerified ) {
	 *			// Set the error array for the input fields.
	 *			$this->setFieldErrors( $arrErrors );		
	 *			$this->setSettingNotice( 'There was an error in your input.' );
	 *			return $arrOldPageOptions;
	 *		}
	 *		return $arrInput;
	 *	}</code>
	 * 
	 * @since			2.0.0
	 * @remark			the transient name is a MD5 hash of the extended class name + _ + page slug ( the passed ID )
	 * @param			array			$arrErrors			the field error array. The structure should follow the one contained in the submitted $_POST array.
	 * @param			string			$strID				this should be the page slug of the page that has the dealing form filed.
	 * @param			integer			$numSavingDuration	the transient's lifetime. 300 seconds means 5 minutes.
	 */ 
	protected function setFieldErrors( $arrErrors, $strID=null, $numSavingDuration=300 ) {
		
		$strID = isset( $strID ) ? $strID : ( isset( $_POST['strPageSlug'] ) ? $_POST['strPageSlug'] : ( isset( $_GET['page'] ) ? $_GET['page'] : $this->oProps->strClassName ) );	
		set_transient( md5( $this->oProps->strClassName . '_' . $strID ), $arrErrors, $numSavingDuration );	// store it for 5 minutes ( 60 seconds * 5 )
	
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
	protected function renderSectionDescription( $strMethodName ) {		

		$strSectionID = substr( $strMethodName, strlen( 'section_pre_' ) );	// X will be the section ID in section_pre_X
		
		if ( ! isset( $this->oProps->arrSections[ $strSectionID ] ) ) return;	// if it is not added

		echo  $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $strFilter, $vInput, $vArgs...
			$this,
			$this->oProps->strClassName . '_' .  self::$arrPrefixesForCallbacks['section_'] . $strSectionID,	// class name + _ + section_ + section id
			'<p>' . $this->oProps->arrSections[ $strSectionID ]['strDescription'] . '</p>',	 // the p-tagged description string
			$this->oProps->arrSections[ $strSectionID ]['strDescription']	// the original description
		);		
			
	}
	
	/**
	 * Retrieves the page slug that the settings section belongs to.		
	 * 
	 * @since			2.0.0
	 * @return			string|null
	 */ 
	private function getPageSlugBySectionID( $strSectionID ) {
		return isset( $this->oProps->arrSections[ $strSectionID ]['strPageSlug'] )
			? $this->oProps->arrSections[ $strSectionID ]['strPageSlug']
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
	 * @remark			This method is not intended to be used by the user.
	 * @remark			The callback method for the <em>admin_menu</em> hook.
	 * @return			void
	 */ 
	public function registerSettings() {
		
		// Format ( sanitize ) the section and field arrays.
		$this->oProps->arrSections = $this->formatSectionArrays( $this->oProps->arrSections );
		$this->oProps->arrFields = $this->formatFieldArrays( $this->oProps->arrFields );	// must be done after the formatSectionArrays().
				
		// If there is no section or field to add, do nothing.
		if ( 
			$GLOBALS['pagenow'] != 'options.php'
			&& ( count( $this->oProps->arrSections ) == 0 || count( $this->oProps->arrFields ) == 0 ) 
		) return;
				
		// Register settings sections 
		uasort( $this->oProps->arrSections, array( $this->oProps, 'sortByOrder' ) ); 
		foreach( $this->oProps->arrSections as $arrSection ) {
			add_settings_section(	// Add the given section
				$arrSection['strSectionID'],	//  section ID
				"<a id='{$arrSection['strSectionID']}'></a>" . $arrSection['strTitle'],		// title - place the anchor in front of the title.
				array( $this, 'section_pre_' . $arrSection['strSectionID'] ), 				// callback function -  this will trigger the __call() magic method.
				$arrSection['strPageSlug']	// page
			);
			// For the contextual help pane,
			if ( ! empty( $arrSection['strHelp'] ) )
				$this->addHelpTab( 
					array(
						'strPageSlug'				=> $arrSection['strPageSlug'],
						'strPageTabSlug'			=> $arrSection['strTabSlug'],
						'strHelpTabTitle'			=> $arrSection['strTitle'],
						'strHelpTabID'				=> $arrSection['strSectionID'],
						'strHelpTabContent'			=> $arrSection['strHelp'],
						'strHelpTabSidebarContent'	=> $arrSection['strHelpAside'] ? $arrSection['strHelpAside'] : "",
					)
				);
				
		}
		
		// Register settings fields
		$strCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
		uasort( $this->oProps->arrFields, array( $this->oProps, 'sortByOrder' ) ); 
		foreach( $this->oProps->arrFields as $arrField ) {

			add_settings_field(	// Add the given field.
				$arrField['strFieldID'],
				"<a id='{$arrField['strFieldID']}'></a><span title='{$arrField['strTip']}'>{$arrField['strTitle']}</span>",
				array( $this, 'field_pre_' . $arrField['strFieldID'] ),	// callback function - will trigger the __call() magic method.
				$this->getPageSlugBySectionID( $arrField['strSectionID'] ), // page slug
				$arrField['strSectionID'],	// section
				$arrField['strFieldID']		// arguments - pass the field ID to the callback function
			);	
			
			// If it's the field type that requires extra scripts, call the relavant methods.
			if ( $arrField['strType'] == 'taxonomy' && $arrField['strPageSlug'] == $strCurrentPageSlug ) $this->addTaxonomyChecklistScript( $arrField );
			if ( $arrField['strType'] == 'image' && $arrField['strPageSlug'] == $strCurrentPageSlug ) $this->addImageFieldScript( $arrField );
			if ( $arrField['strType'] == 'color' && $arrField['strPageSlug'] == $strCurrentPageSlug ) $this->enqueueColorFieldScript( $arrField );
			if ( $arrField['strType'] == 'date' && $arrField['strPageSlug'] == $strCurrentPageSlug ) $this->enqueueDateFieldScript( $arrField );
			
			// For the contextual help pane,
			if ( ! empty( $arrField['strHelp'] ) )
				$this->addHelpTab( 
					array(
						'strPageSlug'				=> $arrField['strPageSlug'],
						'strPageTabSlug'			=> $arrField['strTabSlug'],
						'strHelpTabTitle'			=> $arrField['strSectionTitle'],
						'strHelpTabID'				=> $arrField['strSectionID'],
						'strHelpTabContent'			=> "<span class='contextual-help-tab-title'>" . $arrField['strTitle'] . "</span> - " . PHP_EOL
														. $arrField['strHelp'],
						'strHelpTabSidebarContent'	=> $arrField['strHelpAside'] ? $arrField['strHelpAside'] : "",
					)
				);

		}

		// Set the form enabling flag so that the <form></form> tag will be inserted in the page.
		$this->oProps->fEnableForm = true;
		register_setting(	
			$this->oProps->strOptionKey,	// the option group name.	
			$this->oProps->strOptionKey,	// the option key name that will be stored in the option table in the database.
			array( $this, 'validation_pre_' . $this->oProps->strClassName )	// validation method
		); 
		
	}
	
	/**
	 * Formats the given section arrays.
	 * 
	 * @since			2.0.0
	 */ 
	private function formatSectionArrays( $arrSections ) {

		// Apply filters to let other scripts to add sections.
		$arrSections = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $strFilter, $vInput, $vArgs...
			$this,
			"{$this->oProps->strClassName}_setting_sections",
			$arrSections
		);
		
		$strCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
		
		// Since the section array may have been modified, sanitize the elements and 
		// apply the conditions to remove unnecessary elements and put new orders.
		$arrNewSectionArray = array();
		foreach( $arrSections as $arrSection ) {
		
			$arrSection = $arrSection + self::$arrStructure_Section;	// avoid undefined index warnings.
			
			// Sanitize the IDs since they are used as a callback method name, the slugs as well.
			$arrSection['strSectionID'] = $this->oUtil->sanitizeSlug( $arrSection['strSectionID'] );
			$arrSection['strPageSlug'] = $this->oUtil->sanitizeSlug( $arrSection['strPageSlug'] );
			$arrSection['strTabSlug'] = $this->oUtil->sanitizeSlug( $arrSection['strTabSlug'] );
			
			// Check the mandatory keys' values.
			if ( ! isset( $arrSection['strSectionID'], $arrSection['strPageSlug'] ) ) continue;	// these keys are necessary.
			
			// If the page slug does not match the current loading page, there is no need to register form sections and fields.
			if ( $GLOBALS['pagenow'] != 'options.php' && ! $strCurrentPageSlug || $strCurrentPageSlug !=  $arrSection['strPageSlug'] ) continue;				

			// If this section does not belong to the currently loading page tab, skip.
			if ( ! $this->isSettingSectionOfCurrentTab( $arrSection ) )  continue;
			
			// If the access level is set and it is not sufficient, skip.
			$arrSection['strCapability'] = isset( $arrSection['strCapability'] ) ? $arrSection['strCapability'] : $this->oProps->strCapability;
			if ( ! current_user_can( $arrSection['strCapability'] ) ) continue;	// since 1.0.2.1
		
			// If a custom condition is set and it's not true, skip,
			if ( $arrSection['fIf'] !== true ) continue;
		
			// Set the order.
			$arrSection['numOrder']	= is_numeric( $arrSection['numOrder'] ) ? $arrSection['numOrder'] : count( $arrNewSectionArray ) + 10;
		
			// Add the section array to the returning array.
			$arrNewSectionArray[ $arrSection['strSectionID'] ] = $arrSection;
			
		}
		return $arrNewSectionArray;
		
	}
	
	/**
	 * Checks if the given section belongs to the currently loading tab.
	 * 
	 * @since			2.0.0
	 * @return			boolean			Returns true if the section belongs to the current tab page. Otherwise, false.
	 */ 	
	private function isSettingSectionOfCurrentTab( $arrSection ) {

		// Determine: 
		// 1. if the current tab matches the given tab slug. Yes -> the section should be registered.
		// 2. if the current page is the default tab. Yes -> the section should be registered.

		// If the tab slug is not specified, it means that the user wants the section to be visible in the page regardless of tabs.
		if ( ! isset( $arrSection['strTabSlug'] ) ) return true;
		
		// 1. If the checking tab slug and the current loading tab slug is the same, it should be registered.
		$strCurrentTab =  isset( $_GET['tab'] ) ? $_GET['tab'] : null;
		if ( $arrSection['strTabSlug'] == $strCurrentTab )  return true;

		// 2. If $_GET['tab'] is not set and the page slug is stored in the tab array, 
		// consider the default tab which should be loaded without the tab query value in the url
		$strPageSlug = $arrSection['strPageSlug'];
		if ( ! isset( $_GET['tab'] ) && isset( $this->oProps->arrInPageTabs[ $strPageSlug ] ) ) {
		
			$strDefaultTabSlug = isset( $this->oProps->arrDefaultInPageTabs[ $strPageSlug ] ) ? $this->oProps->arrDefaultInPageTabs[ $strPageSlug ] : '';
			if ( $strDefaultTabSlug  == $arrSection['strTabSlug'] ) return true;		// should be registered.			
				
		}
				
		// Otherwise, false.
		return false;
		
	}	
	
	/**
	 * Formats the given field arrays.
	 * 
	 * @since			2.0.0
	 */ 
	private function formatFieldArrays( $arrFields ) {
		
		// Apply filters to let other scripts to add fields.
		$arrFields = $this->oUtil->addAndApplyFilter(	// Parameters: $oCallerObject, $arrFilters, $vInput, $vArgs...
			$this,
			"{$this->oProps->strClassName}_setting_fields",
			$arrFields
		); 
		
		// Apply the conditions to remove unnecessary elements and put new orders.
		$arrNewFieldArrays = array();
		foreach( $arrFields as $arrField ) {
		
			if ( ! is_array( $arrField ) ) continue;		// the element must be an array.
			
			$arrField = $arrField + self::$arrStructure_Field;	// avoid undefined index warnings.
			
			// Sanitize the IDs since they are used as a callback method name.
			$arrField['strFieldID'] = $this->oUtil->sanitizeSlug( $arrField['strFieldID'] );
			$arrField['strSectionID'] = $this->oUtil->sanitizeSlug( $arrField['strSectionID'] );
			
			// If the section that this field belongs to is not set, no need to register this field.
			// The $arrSection property must be formatted prior to perform this method.
			if ( ! isset( $this->oProps->arrSections[ $arrField['strSectionID'] ] ) ) continue;
			
			// Check the mandatory keys' values.
			if ( ! isset( $arrField['strFieldID'], $arrField['strSectionID'], $arrField['strType'] ) ) continue;	// these keys are necessary.
			
			// If the access level is not sufficient, skip.
			$arrField['strCapability'] = isset( $arrField['strCapability'] ) ? $arrField['strCapability'] : $this->oProps->strCapability;
			if ( ! current_user_can( $arrField['strCapability'] ) ) continue; 
						
			// If the condition is not met, skip.
			if ( $arrField['fIf'] !== true ) continue;
						
			// Set the order.
			$arrField['numOrder']	= is_numeric( $arrField['numOrder'] ) ? $arrField['numOrder'] : count( $arrNewFieldArrays ) + 10;
			
			// Set the tip, option key, instantiated class name, and page slug elements.
			$arrField['strTip'] = strip_tags( isset( $arrField['strTip'] ) ? $arrField['strTip'] : $arrField['strDescription'] );
			$arrField['strOptionKey'] = $this->oProps->strOptionKey;
			$arrField['strClassName'] = $this->oProps->strClassName;
			// $arrField['strPageSlug'] = isset( $_GET['page'] ) ? $_GET['page'] : null;
			$arrField['strPageSlug'] = $this->oProps->arrSections[ $arrField['strSectionID'] ]['strPageSlug'];
			$arrField['strTabSlug'] = $this->oProps->arrSections[ $arrField['strSectionID'] ]['strTabSlug'];
			$arrField['strSectionTitle'] = $this->oProps->arrSections[ $arrField['strSectionID'] ]['strTitle'];	// used for the contextual help pane.
			
			// Add the element to the new returning array.
			$arrNewFieldArrays[ $arrField['strFieldID'] ] = $arrField;
				
		}
		return $arrNewFieldArrays;
		
	}
	
	/**
	 * Retrieves the specified field value stored in the options.
	 * 
	 * Useful when you don't know the section name but it's a bit slower than accessing the property value by specifying the section name.
	 * 
	 * @since			2.1.2
	 */
	protected function getFieldValue( $strFieldNameToFind ) {

		foreach( $this->oProps->arrOptions as $strPageSlug => $arrSections )  
			foreach( $arrSections as $strSectionName => $arrFields ) 
				foreach( $arrFields as $strFieldName => $vValue ) 
					if ( trim( $strFieldNameToFind ) == trim( $strFieldName ) )
						return $vValue;	
		
		return null;
	}
	
	/*
	 *	Callbacks 
	 * */
	/**
	 * Enqueues media uploader scripts.
	 * 
	 * @since			2.0.0
	 * @since			2.1.3			Added the support for the 3.5 media uploader
	 * @return			void
	 * @internal
	 */ 
	public function enqueueUploaderScripts() {

		wp_enqueue_script('jquery');			
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');	
	
		if( function_exists( 'wp_enqueue_media' ) ) {	// means the WordPress version is 3.5 or above
			wp_enqueue_media();	
		} else		
			wp_enqueue_script('media-upload');
	
	} 
	
	/**
	 * Replaces the label text of a button used in the media uploader.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>gettext</em> hook.	
	 */ 
	public function replaceThickBoxText( $strTranslated, $strText ) {

		// Replace the button label in the media thick box.
		if ( ! in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php' ) ) ) return $strTranslated;
		if ( $strText != 'Insert into Post' ) return $strTranslated;
		if ( $this->oUtil->getQueryValueInURLByKey( wp_get_referer(), 'referrer' ) != 'admin_page_framework' ) return $strTranslated;
		
		if ( isset( $_GET['button_label'] ) ) return $_GET['button_label'];

		return $this->oProps->strThickBoxButtonUseThis ?  $this->oProps->strThickBoxButtonUseThis : __( 'Use This Image', 'admin-page-framework' );
		
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
 * 	<li><code>extended class name + _ + section_ + section ID</code> – receives the description output of the given form section ID. The first parameter: output string. The second parameter: the array of option.</li>
 * 	<li><code>extended class name + _ + field_ + field ID</code> – receives the form input field output of the given input field ID. The first parameter: output string. The second parameter: the array of option.</li>
 * 	<li><code>validation_ + extended class name</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><code>validation_ + page slug</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><code>validation_ + page slug + _ + tab slug</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><code>style_ + page slug + _ + tab slug</code> – receives the output of the CSS rules applied to the tab page of the slug.</li>
 * 	<li><code>style_ + page slug</code> – receives the output of the CSS rules applied to the page of the slug.</li>
 * 	<li><code>style_ + extended class name</code> – receives the output of the CSS rules applied to the pages added by the instantiated class object.</li>
 * 	<li><code>script_ + page slug + _ + tab slug</code> – receives the output of the JavaScript script applied to the tab page of the slug.</li>
 * 	<li><code>script_ + page slug</code> – receives the output of the JavaScript script applied to the page of the slug.</li>
 * 	<li><code>script_ + extended class name</code> – receives the output of the JavaScript script applied to the pages added by the instantiated class object.</li>
 * 	<li><code>export_ + page slug + _ + tab slug</code> – receives the exporting array sent from the tab page.</li>
 * 	<li><code>export_ + page slug</code> – receives the exporting array submitted from the page.</li>
 * 	<li><code>export_ + extended class name</code> – receives the exporting array submitted from the plugin.</li>
 * 	<li><code>import_ + page slug + _ + tab slug</code> – receives the importing array submitted from the tab page.</li>
 * 	<li><code>import_ + page slug</code> – receives the importing array submitted from the page.</li>
 * 	<li><code>import_ + extended class name</code> – receives the importing array submitted from the plugin.</li>
 * </ul>
 * <h3>Remarks</h3>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p>
 * <h3>Examples</h3>
 * <p>If the extended class name is Sample_Admin_Pages, defining the following class method will embed a banner image in all pages created by the class.</p>
 * <code>class Sample_Admin_Pages extends AdminPageFramework {
 * ...
 *     function head_Sample_Admin_Pages( $strContent ) {
 *         return '&lt;div style="float:right;"&gt;&lt;img src="' . plugins_url( 'img/banner468x60.gif', __FILE__ ) . '" /&gt;&lt;/div&gt;' 
 *             . $strContent;
 *     }
 * ...
 * }</code>
 * <p>If the created page slug is my_first_setting_page, defining the following class method will filter the middle part of the page output.</p>
 * <code>class Sample_Admin_Pages extends AdminPageFramework {
 * ...
 *     function content_my_first_setting_page( $strContent ) {
 *         return $strContent . '&lt;p&gt;Hello world!&lt;/p&gt;';
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
 * @use				AdminPageFramework_Utilities
 * @remark			This class stems from several abstract classes.
 * @extends			AdminPageFramework_SettingsAPI
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Page
 */
abstract class AdminPageFramework extends AdminPageFramework_SettingsAPI {
		
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
	* @var			object			an instance of AdminPageFramework_Utilities will be assigned in the constructor.
    */			
	protected $oUtil;
	
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
	 * @param			string		$strOptionKey			( optional ) specifies the option key name to store in the options table. If this is not set, the extended class name will be used.
	 * @param			string		$strCallerPath			( optional ) used to retrieve the plugin/theme details to auto-insert the information into the page footer.
	 * @param			string		$strCapability			( optional ) sets the overall access level to the admin pages created by the framework. The used capabilities are listed here( http://codex.wordpress.org/Roles_and_Capabilities ). If not set, <strong>manage_options</strong> will be assigned by default. The capability can be set per page, tab, setting section, setting field.
	 * @param			string		$strTextDomain			( optional ) the text domain( http://codex.wordpress.org/I18n_for_WordPress_Developers#Text_Domains ) used for the framework's text strings.
	 * @remark			the scope is public because often <code>parent::__construct()</code> is used.
	 * @return			void		returns nothing.
	 */
	public function __construct( $strOptionKey=null, $strCallerPath=null, $strCapability=null, $strTextDomain=null ){
				 
		// Variables
		$strClassName = get_class( $this );
		
		// Objects
		$this->oProps = new AdminPageFramework_Properties( $strClassName, $strOptionKey, $strCapability );
		$this->oMsg = new AdminPageFramework_Messages( $strTextDomain );
		$this->oUtil = new AdminPageFramework_Utilities;
		$this->oDebug = new AdminPageFramework_Debug;
		$this->oLink = new AdminPageFramework_Link( $this->oProps, $strCallerPath );
								
		if ( is_admin() ) {

			// Hook the menu action - adds the menu items.
			add_action( 'wp_loaded', array( $this, 'setUp' ) );
			
			// AdminPageFramework_Menu
			add_action( 'admin_menu', array( $this, 'buildMenus' ), 98 );
			
			// AdminPageFramework_Page
			add_action( 'admin_menu', array( $this, 'finalizeInPageTabs' ), 99 );	// must be called before the registerSettings() method.
			
			// AdminPageFramework_SettingsAPI
			add_action( 'admin_menu', array( $this, 'registerSettings' ), 100 );
			
			// Redirect Buttons
			add_action( 'admin_init', array( $this, 'checkRedirects' ) );
			
			// Hook the admin header to insert custom admin stylesheet.
			add_action( 'admin_head', array( $this, 'addStyle' ) );
			add_action( 'admin_head', array( $this, 'addScript' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScriptsCallback' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueueStylesCallback' ) );
			
			// The contextual help pane.
			add_action( "admin_head", array( $this, 'registerHelpTabs' ), 200 );
						
			// The capability for the settings. $this->oProps->strOptionKey is the part that is set in the settings_fields() function.
			// This prevents the "Cheatin' huh?" message.
			add_filter( "option_page_capability_{$this->oProps->strOptionKey}", array( $this->oProps, 'getCapability' ) );
						
			// For earlier loading than $this->setUp
			$this->oUtil->addAndDoAction( $this, self::$arrPrefixes['start_'] . $this->oProps->strClassName );
		
		}
	}	
	
	/**
	 * The magic method which redirects callback-function calls with the pre-defined prefixes for hooks to the appropriate methods. 
	 * 
	 * @access			public
	 * @remark			the users do not need to call or extend this method unless they know what they are doing.
	 * @param			string		$strMethodName		the called method name. 
	 * @param			array		$arrArgs			the argument array. The first element holds the parameters passed to the called method.
	 * @return			mixed		depends on the called method. If the method name matches one of the hook prefixes, the redirected methods return value will be returned. Otherwise, none.
	 * @since			2.0.0
	 */
	public function __call( $strMethodName, $arrArgs=null ) {		
				 
		// Variables
		// The currently loading in-page tab slug. Careful that not all cases $strMethodName have the page slug.
		$strPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;	
		$strTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->getDefaultInPageTab( $strPageSlug );	

		// If it is a pre callback method, call the redirecting method.
		// add_settings_section() callback
		if ( substr( $strMethodName, 0, strlen( 'section_pre_' ) )	== 'section_pre_' ) return $this->renderSectionDescription( $strMethodName );  // section_pre_
		
		// add_settings_field() callback
		if ( substr( $strMethodName, 0, strlen( 'field_pre_' ) )	== 'field_pre_' ) return $this->renderSettingField( $arrArgs[ 0 ], $strPageSlug );  // field_pre_
		
		// register_setting() callback
		if ( substr( $strMethodName, 0, strlen( 'validation_pre_' ) )	== 'validation_pre_' ) return $this->doValidationCall( $strMethodName, $arrArgs[ 0 ] );  // section_pre_

		// load-{page} callback
		if ( substr( $strMethodName, 0, strlen( 'load_pre_' ) )	== 'load_pre_' ) return $this->doPageLoadCall( substr( $strMethodName, strlen( 'load_pre_' ) ), $strTabSlug, $arrArgs[ 0 ] );  // load_pre_

		// The callback of the call_page_{page slug} action hook
		if ( $strMethodName == $this->oProps->strClassHash . '_page_' . $strPageSlug )
			return $this->renderPage( $strPageSlug, $strTabSlug );	
		
		// If it's one of the framework's callback methods, do nothing.	
		if ( $this->isFrameworkCallbackMethod( $strMethodName ) )
			return isset( $arrArgs[0] ) ? $arrArgs[0] : null;	// if $arrArgs[0] is set, it's a filter, otherwise, it's an action.		

		
	}	
	
	/**
	 * Determines whether the method name matches the pre-defined hook prefixes.
	 * @access			private
	 * @since			2.0.0
	 * @remark			the users do not need to call or extend this method unless they know what they are doing.
	 * @param			string			$strMethodName			the called method name
	 * @return			boolean			If it is a framework's callback method, returns true; otherwise, false.
	 */
	private function isFrameworkCallbackMethod( $strMethodName ) {

		if ( substr( $strMethodName, 0, strlen( "{$this->oProps->strClassName}_" ) ) == "{$this->oProps->strClassName}_" )	// e.g. {instantiated class name} + field_ + {field id}
			return true;
		
		foreach( self::$arrPrefixes as $strPrefix ) {
			if ( substr( $strMethodName, 0, strlen( $strPrefix ) )	== $strPrefix  ) 
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
	* 			'strPageTitle' => 'Form Fields',
	* 			'strPageSlug' => 'apf_form_fields',
	* 		)
	* 	);		
	* 	$this->addSettingSections(
	* 		array(
	* 			'strSectionID'		=> 'text_fields',
	* 			'strPageSlug'		=> 'apf_form_fields',
	* 			'strTitle'			=> 'Text Fields',
	* 			'strDescription'	=> 'These are text type fields.',
	* 		)
	* 	);
	* 	$this->addSettingFields(
	* 		array(	
	* 			'strFieldID' => 'text',
	* 			'strSectionID' => 'text_fields',
	* 			'strTitle' => 'Text',
	* 			'strType' => 'text',
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
	* <li><strong>strPageTitle</strong> - ( string ) the page title of the page.</li>
	* <li><strong>strPageSlug</strong> - ( string ) the page slug of the page. Non-alphabetical characters should not be used including dots(.) and hyphens(-).</li>
	* <li><strong>strScreenIcon</strong> - ( optional, string ) either the ID selector name from the following list or the icon URL. The size of the icon should be 32 by 32 in pixel.
	*	<pre>edit, post, index, media, upload, link-manager, link, link-category, edit-pages, page, edit-comments, themes, plugins, users, profile, user-edit, tools, admin, options-general, ms-admin, generic</pre>
	*	<p><strong>Notes</strong>: the <em>generic</em> icon is available WordPress version 3.5 or above.</p>
	* </li>
	* <li><strong>strCapability</strong> - ( optional, string ) the access level to the created admin pages defined [here](http://codex.wordpress.org/Roles_and_Capabilities). If not set, the overall capability assigned in the class constructor, which is *manage_options* by default, will be used.</li>
	* <li><strong>numOrder</strong> - ( optional, integer ) the order number of the page. The lager the number is, the lower the position it is placed in the menu.</li>
	* <li><strong>fShowPageHeadingTab</strong> - ( optional, boolean ) if this is set to false, the page title won't be displayed in the page heading tab. Default: true.</li>
	* </ul>
	* <h4>Sub-menu Link Array</h4>
	* <ul>
	* <li><strong>strMenuTitle</strong> - ( string ) the link title.</li>
	* <li><strong>strURL</strong> - ( string ) the URL of the target link.</li>
	* <li><strong>strCapability</strong> - ( optional, string ) the access level to show the item, defined [here](http://codex.wordpress.org/Roles_and_Capabilities). If not set, the overall capability assigned in the class constructor, which is *manage_options* by default, will be used.</li>
	* <li><strong>numOrder</strong> - ( optional, integer ) the order number of the page. The lager the number is, the lower the position it is placed in the menu.</li>
	* <li><strong>fShowPageHeadingTab</strong> - ( optional, boolean ) if this is set to false, the page title won't be displayed in the page heading tab. Default: true.</li>
	* </ul>
	* 
	* <h4>Example</h4>
	* <code>$this->addSubMenuItems(
	*		array(
	*			'strPageTitle' => 'Various Form Fields',
	*			'strPageSlug' => 'first_page',
	*			'strScreenIcon' => 'options-general',
	*		),
	*		array(
	*			'strPageTitle' => 'Manage Options',
	*			'strPageSlug' => 'second_page',
	*			'strScreenIcon' => 'link-manager',
	*		),
	*		array(
	*			'strMenuTitle' => 'Google',
	*			'strURL' => 'http://www.google.com',	
	*			'fShowPageHeadingTab' => false,	// this removes the title from the page heading tabs.
	*		),
	*	);</code>
	* 
	* @since			2.0.0
	* @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			array		$arrSubMenuItem1		a first sub-menu array.
	* @param			array		$arrSubMenuItem2		( optional ) a second sub-menu array.
	* @param			array		$_and_more				( optional ) third and add items as many as necessary with next parameters.
	* @access 			protected
	* @return			void
	*/		
	protected function addSubMenuItems( $arrSubMenuItem1, $arrSubMenuItem2=null, $_and_more=null ) {
		foreach ( func_get_args() as $arrSubMenuItem ) 
			$this->addSubMenuItem( $arrSubMenuItem );		
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
	* @param			array		$arrSubMenuItem			a first sub-menu array.
	* @access 			private
	* @return			void
	*/	
	private function addSubMenuItem( $arrSubMenuItem ) {
		if ( isset( $arrSubMenuItem['strURL'] ) ) {
			$arrSubMenuLink = $arrSubMenuItem + $this->oLink->arrStructure_SubMenuLink;
			$this->oLink->addSubMenuLink(
				$arrSubMenuLink['strMenuTitle'],
				$arrSubMenuLink['strURL'],
				$arrSubMenuLink['strCapability'],
				$arrSubMenuLink['numOrder'],
				$arrSubMenuLink['fShowPageHeadingTab']
			);			
		}
		else { // if ( $arrSubMenuItem['strType'] == 'page' ) {
			$arrSubMenuPage = $arrSubMenuItem + self::$arrStructure_SubMenuPage;	// avoid undefined index warnings.
			$this->addSubMenuPage(
				$arrSubMenuPage['strPageTitle'],
				$arrSubMenuPage['strPageSlug'],
				$arrSubMenuPage['strScreenIcon'],
				$arrSubMenuPage['strCapability'],
				$arrSubMenuPage['numOrder'],	
				$arrSubMenuPage['fShowPageHeadingTab']
			);				
		}
	}

	/**
	* Adds the given link into the menu on the left sidebar of the administration panel.
	* 
	* @since			2.0.0
	* @remark			The user may use this method in their extended class definition.
	* @param			string		$strMenuTitle			the menu title.
	* @param			string		$strURL					the URL linked to the menu.
	* @param			string		$strCapability			( optional ) the access level. ( http://codex.wordpress.org/Roles_and_Capabilities)
	* @param			string		$numOrder				( optional ) the order number. The larger it is, the lower the position it gets.
	* @param			string		$fShowPageHeadingTab		( optional ) if set to false, the menu title will not be listed in the tab navigation menu at the top of the page.
	* @access 			protected
	* @return			void
	*/	
	protected function addSubMenuLink( $strMenuTitle, $strURL, $strCapability=null, $numOrder=null, $fShowPageHeadingTab=true ) {
		$this->oLink->addSubMenuLink( $strMenuTitle, $strURL, $strCapability, $numOrder, $fShowPageHeadingTab );
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
	* @param			string			$strTaggedLinkHTML1			the tagged HTML link text.
	* @param			string			$strTaggedLinkHTML2			( optional ) another tagged HTML link text.
	* @param			string			$_and_more					( optional ) add more as many as want by adding items to the next parameters.
	* @access 			protected
	* @return			void
	*/		
	protected function addLinkToPluginDescription( $strTaggedLinkHTML1, $strTaggedLinkHTML2=null, $_and_more=null ) {				
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
	* @param			string			$strTaggedLinkHTML1			the tagged HTML link text.
	* @param			string			$strTaggedLinkHTML2			( optional ) another tagged HTML link text.
	* @param			string			$_and_more					( optional ) add more as many as want by adding items to the next parameters.
	* @access 			protected
	* @return			void
	*/	
	protected function addLinkToPluginTitle( $strTaggedLinkHTML1, $strTaggedLinkHTML2=null, $_and_more=null ) {	
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
	 * @remark			The user may directly edit <code>$this->oProps->strCapability</code> instead.
	 * @param			string			$strCapability			The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> for the created pages.
	 * @return			void
	 */ 
	protected function setCapability( $strCapability ) {
		$this->oProps->strCapability = $strCapability;	
	}

	/**
	 * Sets the given HTML text into the footer on the left hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoLeft( '&lt;br /&gt;Custom Text on the left hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @remark			The user may directly edit <code>$this->oProps->arrFooterInfo['strLeft']</code> instead.
	 * @param			string			$strHTML			The HTML code to insert.
	 * @param			boolean			$fAppend			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */	
	protected function setFooterInfoLeft( $strHTML, $fAppend=true ) {
		
		$this->oProps->arrFooterInfo['strLeft'] = $fAppend 
			? $this->oProps->arrFooterInfo['strLeft'] . $strHTML
			: $strHTML;
		
	}
	
	/**
	 * Sets the given HTML text into the footer on the right hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoRight( '&lt;br /&gt;Custom Text on the right hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @remark			The user may directly edit <code>$this->oProps->arrFooterInfo['strRight']</code> instead.
	 * @param			string			$strHTML			The HTML code to insert.
	 * @param			boolean			$fAppend			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */	
	protected function setFooterInfoRight( $strHTML, $fAppend=true ) {
		
		$this->oProps->arrFooterInfo['strRight'] = $fAppend 
			? $this->oProps->arrFooterInfo['strRight'] . $strHTML
			: $strHTML;
		
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
		$arrError = $this->getFieldErrors( $_GET['page'], false );
		if ( ! empty( $arrError ) ) return;
		
		// Okay, it seems the submitted data have been updated successfully.
		$strTransient = md5( trim( "redirect_{$this->oProps->strClassName}_{$_GET['page']}" ) );
		$strURL = get_transient( $strTransient );
		if ( $strURL === false ) return;
		
		// The redirect URL seems to be set.
		delete_transient( $strTransient );	// we don't need it any more.
		
		// if the redirect page is outside the plugin admin page, delete the plugin settings admin notices as well.
		// if ( ! $this->oCore->IsPluginPage( $strURL ) ) 	
			// delete_transient( md5( 'SettingsErrors_' . $this->oCore->strClassName . '_' . $this->oCore->strPageSlug ) );
				
		// Go to the page.
		$this->oUtil->goRedirect( $strURL );
		
	}
	
	public function addStyle() {
		
		$strPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
		$strTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->getDefaultInPageTab( $strPageSlug );
		
		// If the loading page has not been registered nor the plugin page which uses this library, do nothing.
		if ( ! $this->oProps->isPageAdded( $strPageSlug ) ) return;
					
		// Print out the filtered styles.
		echo "<style type='text/css' id='admin-page-framework-style'>" 
			. $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['style_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, false ), AdminPageFramework_Properties::$strDefaultStyle )
			. "</style>";
		echo "<!--[if IE]><style type='text/css' id='admin-page-framework-style-for-IE'>" 
			. $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['style_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, false ), AdminPageFramework_Properties::$strDefaultStyleIE )
			. "</style><![endif]-->";
			
	}
	
	public function addScript() {
		
		$strPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
		$strTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->getDefaultInPageTab( $strPageSlug );
		
		// If the loading page has not been registered or not the plugin page which uses this library, do nothing.
		if ( ! $this->oProps->isPageAdded( $strPageSlug ) ) return;

		// Print out the filtered scripts.
		echo "<script type='text/javascript' id='admin-page-framework-script'>"
			. $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['script_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, false ), $this->oProps->strScript )			
			. "</script>";		
		
	}

	/**
	 * Enqueues a style by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>strHandleID</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>arrDependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>strVersion</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>strMedia</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	 * </ul>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			$strSRC				The URL of the stylesheet to enqueue or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param			string			$strPageSlug		(optional) The page slug that the stylesheet should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$strTabSlug			(optional) The tab slug that the stylesheet should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$arrCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */	
	public function enqueueStyle( $strSRC, $strPageSlug='', $strTabSlug='', $arrCustomArgs=array() ) {
		
		$strSRC = trim( $strSRC );
		if ( empty( $strSRC ) ) return '';
		if ( isset( $this->oProps->arrEnqueuingScripts[ md5( $strSRC ) ] ) ) return '';	// if already set
		
		$strSRCHash = md5( $strSRC );	// setting the key based on the url prevents duplicate items
		$this->oProps->arrEnqueuingStyles[ $strSRCHash ] = $this->oUtil->uniteArrays( 
			( array ) $arrCustomArgs,
			array(		
				'strSRC' => $strSRC,
				'strPageSlug' => $strPageSlug,
				'strTabSlug' => $strTabSlug,
				'strType' => 'style',
				'strHandleID' => 'style_' . $this->oProps->strClassName . '_' .  ( $this->oProps->intEnqueuedStyleIndex + 1 ),
			),
			AdminPageFramework_Properties::$arrStructure_EnqueuingScriptsAndStyles
		);
		return $this->oProps->arrEnqueuingStyles[ $strSRCHash ][ 'strHandleID' ];
		
	}
	
	/**
	 * Enqueues a script by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>strHandleID</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>arrDependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>strVersion</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>arrTranslation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>fInFooter</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 * 
	 * <h4>Example</h4>
	 * <code>$this->enqueueScript(  
	 *		plugins_url( 'asset/js/test.js' , __FILE__ ),	// source url or path
	 *		'apf_read_me', 	// page slug
	 *		'', 	// tab slug
	 *		array(
	 *			'strHandleID' => 'my_script',	// this handle ID also is used as the object name for the translation array below.
	 *			'arrTranslation' => array( 
	 *				'a' => 'hello world!',
	 *				'style_handle_id' => $strStyleHandle,	// check the enqueued style handle ID here.
	 *			),
	 *		)
	 *	);</code>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			$strSRC				The URL of the stylesheet to enqueue or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param			string			$strPageSlug		(optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$strTabSlug			(optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$arrCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */
	public function enqueueScript( $strSRC, $strPageSlug='', $strTabSlug='', $arrCustomArgs=array() ) {
		
		$strSRC = trim( $strSRC );
		if ( empty( $strSRC ) ) return '';
		if ( isset( $this->oProps->arrEnqueuingScripts[ md5( $strSRC ) ] ) ) return '';	// if already set
		
		$strSRCHash = md5( $strSRC );	// setting the key based on the url prevents duplicate items
		$this->oProps->arrEnqueuingScripts[ $strSRCHash ] = $this->oUtil->uniteArrays( 
			( array ) $arrCustomArgs,
			array(		
				'strPageSlug' => $strPageSlug,
				'strTabSlug' => $strTabSlug,
				'strSRC' => $strSRC,
				'strType' => 'script',
				'strHandleID' => 'script_' . $this->oProps->strClassName . '_' .  ( $this->oProps->intEnqueuedScriptIndex + 1 ),
			),
			AdminPageFramework_Properties::$arrStructure_EnqueuingScriptsAndStyles
		);
		return $this->oProps->arrEnqueuingScripts[ $strSRCHash ][ 'strHandleID' ];
	}
	
	/**
	 * Takes care of added enqueuing scripts by page slug and tab slug.
	 * 
	 * @remark			A callback for the admin_enqueue_scripts hook.
	 * @since			2.1.2
	 * @internal
	 */	
	public function enqueueStylesCallback() {	
		foreach( $this->oProps->arrEnqueuingStyles as $strKey => $arrEnqueuingStyle ) 
			$this->enqueueSRCByPageConditoin( $arrEnqueuingStyle );
	}
	
	/**
	 * Takes care of added enqueuing scripts by page slug and tab slug.
	 * 
	 * @remark			A callback for the admin_enqueue_scripts hook.
	 * @since			2.1.2
	 * @internal
	 */
	public function enqueueScriptsCallback() {							
		foreach( $this->oProps->arrEnqueuingScripts as $strKey => $arrEnqueuingScript ) 
			$this->enqueueSRCByPageConditoin( $arrEnqueuingScript );				
	}
	
	/**
	 * A helper function for the above enqueueScriptsAndStyles() method.
	 * 
	 * @since			2.1.2
	 */
	private function enqueueSRCByPageConditoin( $arrEnqueueItem ) {
		
		$strCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
		$strCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->getDefaultInPageTab( $strCurrentPageSlug );
			
		$strPageSlug = $arrEnqueueItem['strPageSlug'];
		$strTabSlug = $arrEnqueueItem['strTabSlug'];
		
		// If the page slug is not specified and the currently loading page is one of the pages that is added by the framework,
		if ( ! $strPageSlug && $this->oProps->isPageAdded( $strCurrentPageSlug ) )  // means script-global(among pages added by the framework)
			return $this->enqueueSRC( $arrEnqueueItem );
				
		// If both tab and page slugs are specified,
		if ( 
			( $strPageSlug && $strCurrentPageSlug == $strPageSlug )
			&& ( $strTabSlug && $strCurrentTabSlug == $strTabSlug )
		) 
			return $this->enqueueSRC( $arrEnqueueItem );
		
		// If the tab slug is not specified and the page slug is specified, 
		// and if the current loading page slug and the specified one matches,
		if ( 
			( $strPageSlug && ! $strTabSlug )
			&& ( $strCurrentPageSlug == $strPageSlug )
		) 
			return $this->enqueueSRC( $arrEnqueueItem );

	}
	/**
	 * A helper function for the above enqueueSRCByPageConditoin() method.
	 * 
	 * @since			2.1.2
	 * @internal
	 */
	private function enqueueSRC( $arrEnqueueItem ) {
		
		// For styles
		if ( $arrEnqueueItem['strType'] == 'style' ) {
			wp_enqueue_style( $arrEnqueueItem['strHandleID'], $arrEnqueueItem['strSRC'], $arrEnqueueItem['arrDependencies'], $arrEnqueueItem['strVersion'], $arrEnqueueItem['strMedia'] );
			return;
		}
		
		// For scripts
		wp_enqueue_script( $arrEnqueueItem['strHandleID'], $arrEnqueueItem['strSRC'], $arrEnqueueItem['arrDependencies'], $arrEnqueueItem['strVersion'], $arrEnqueueItem['fInFooter'] );
		if ( $arrEnqueueItem['arrTranslation'] ) 
			wp_localize_script( $arrEnqueueItem['strHandleID'], $arrEnqueueItem['strHandleID'], $arrEnqueueItem['arrTranslation'] );
		
	}
	/**
	 * Sets an admin notice.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setAdminNotice( sprintf( 'Please click <a href="%1$s">here</a> to upgrade the options.', admin_url( 'admin.php?page="my_page"' ) ), 'updated' );</code>
	 * 
	 * @remark			It should be used before the 'admin_notices' hook is triggered.
	 * @since			2.1.2
	 * @param			string			$strMessage				The message to display
	 * @param			string			$strClassSelector		( optional ) The class selector used in the message HTML element. 'error' and 'updated' are prepared by WordPress but it's not limited to them and can pass a custom name. Default: 'error'
	 * @param			string			$strID					( optional ) The ID of the message. If not set, the hash of the message will be used.
	 */
	protected function setAdminNotice( $strMessage, $strClassSelector='error', $strID='' ) {
			
		$strID = $strID ? $strID : md5( $strMessage );
		$this->oProps->arrAdminNotices[ md5( $strMessage ) ] = array(  
			'strMessage' => $strMessage,
			'strClassSelector' => $strClassSelector,
			'strID' => $strID,
		);
		add_action( 'admin_notices', array( $this, 'printAdminNotices' ) );
		
	}
	/**
	 * A helper function for the above setAdminNotice() method.
	 * @since			2.1.2
	 * @internal
	 */
	public function printAdminNotices() {
		
		foreach( $this->oProps->arrAdminNotices as $arrAdminNotice ) 
			echo "<div class='{$arrAdminNotice['strClassSelector']}' id='{$arrAdminNotice['strID']}' ><p>"
				. $arrAdminNotice['strMessage']
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
	public function setDisallowedQueryKeys( $arrQueryKeys, $fAppend=true ) {
		
		if ( ! $fAppend ) {
			$this->oProps->arrDisallowedQueryKeys = $arrQueryKeys;
			return;
		}
		
		$arrNewQueryKeys = array_merge( $arrQueryKeys, $this->oProps->arrDisallowedQueryKeys );
		$arrNewQueryKeys = array_filter( $arrNewQueryKeys );	// drop non-values
		$arrNewQueryKeys = array_unique( $arrNewQueryKeys );	// drop duplicates
		$this->oProps->arrDisallowedQueryKeys = $arrNewQueryKeys;
		
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_Messages' ) ) :
/**
 * Provides methods for text messages.
 *
 * @since			2.0.0
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 */
class AdminPageFramework_Messages {

	// The user can modify this property directly.
	public $arrMessages = array(
		'option_updated'	=> 'The options have been updated.',
		'option_cleared'	=> 'The options have been cleared.',
		'export_options'	=> 'Export Options',
		'import_options'	=> 'Import Options',
		'submit'			=> 'Submit',
		'import_error'		=> 'An error occurred while uploading the import file.',
		'uploaded_file_type_not_supported'	=> 'The uploaded file type is not supported.',
		'could_not_load_importing_data' => 'Could not load the importing data.',
		'imported_data'		=> 'The uploaded file has been imported.'
	);

	public function __construct( $strTextDomain='admin-page-framework' ) {
		$this->strTextDomain = $strTextDomain;
	}
	public function ___( $strKey ) {
		
		return isset( $this->arrMessages[ $strKey ] )
			? __( $this->arrMessages[ $strKey ], $this->strTextDomain )
			: '';
		
	}
	public function __e( $strKey ) {
		
		if ( isset( $this->arrMessages[ $strKey ] ) )
			_e( $this->arrMessages[ $strKey ], $this->strTextDomain );
		
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
	 * The default CSS rules loaded in the head tag of the created admin pages.
	 * 
	 * @since			2.0.0
	 * @var				string
	 * @static
	 * @remark			It is accessed from the main class and meta box class.
	 * @access			public	
	 * @internal	
	 */
	public static $strDefaultStyle =
		".wrap div.updated, 
		.wrap div.settings-error { 
			clear: both; 
			margin-top: 16px;
		} 
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
		.admin-page-framework-field .image_preview {
			border: none; clear:both; 
			max-width:100%; 
			margin-top: 1em;
			margin-bottom: 1em;
		}
		.admin-page-framework-field .image_preview img {
			max-width: 100%;
			height: auto;
			width: auto\9; /* ie8 */			
		}
		.ui-datepicker.ui-widget.ui-widget-content.ui-helper-clearfix.ui-corner-all {
			display: none;
		}
		.contextual-help-description {
			clear: left;	
			display: block;
			margin: 1em 0;
		}
		.contextual-help-tab-title {
			font-weight: bold;
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
		
		/* Input form elements */
		.admin-page-framework-field {
			display: inline;
			margin-top: 1px;
			margin-bottom: 1px;
		}
		.admin-page-framework-field input[type='checkbox'], 
		.admin-page-framework-field input[type='radio'] { 
			vertical-align: middle;
		}		
		.admin-page-framework-field input {
			margin-right: 0.5em;
			
		}		
		.admin-page-framework-field input[type='text'] {
			margin-bottom: 0.5em;
		}
		.admin-page-framework-field .admin-page-framework-radio-label, 
		.admin-page-framework-field .admin-page-framework-checkbox-label {
			margin-right: 1em;			
		}
		.admin-page-framework-field .admin-page-framework-input-label-container label {
			margin-right: 1em;
		}		
		.admin-page-framework-field .admin-page-framework-input-container {
			display: inline-block;
			vertical-align: middle; 
		}
		.admin-page-framework-field-text .admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field-textarea .admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field-image .admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field-color .admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field-select .admin-page-framework-field .admin-page-framework-input-label-container
		{
			vertical-align: top; 
		}
		.admin-page-framework-field .admin-page-framework-input-label-container {
			margin-top: 2px; 
			vertical-align: middle; 
			display: inline-block;
		}
		.admin-page-framework-field-size input {
			text-align: right;
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
		/* Rich Text Editor */
		.admin-page-framework-field-textarea .wp-core-ui.wp-editor-wrap {
			margin-bottom: 0.5em;
		}
		/* Image Uploader Button */
		.select_image {
			margin-left: 0.5em;
		}
		/* Color Picker */
		.repeatable .colorpicker {
			display: inline;
		}
		";	
	/**
	 * The default CSS rules for IE loaded in the head tag of the created admin pages.
	 * @since			2.1.1
	 */
	public static $strDefaultStyleIE = 
		".tab-box-content { display: block; }
			.tab-box-contents { overflow: hidden;position: relative; }
			b { position: absolute; top: 0px; right: 0px; width:1px; height: 251px; overflow: hidden; text-indent: -9999px; }
		";	
		
	/**
	 * Returns the JavaScript script for taxonomy checklist.
	 * 
	 * @since			2.1.1
	 */ 
	public static function getTaxonomyChecklistScript() {
		return "
			jQuery(document).ready( function() {
				
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
	 * Returns the image selector JavaScript script loaded in the head tag of the created admin pages.
	 * @var				string
	 * @static
	 * @remark			It is accessed from the main class and meta box class.
	 * @remark			Moved to the base class since 2.1.0.
	 * @access			public	
	 * @internal
	 * @return			string			The image selector script.
	 */		
	public static function getImageSelectorScript( $strReferrer, $strThickBoxTitle, $strThickBoxButtonUseThis ) {
		return "
			jQuery( document ).ready( function( $ ){
				$( '.select_image' ).click( function() {
					pressed_id = $( this ).attr( 'id' );
					field_id = pressed_id.substring( 13 );	// remove the select_image_ prefix
					tb_show( '{$strThickBoxTitle}', 'media-upload.php?referrer={$strReferrer}&amp;button_label={$strThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true&amp;post_id=0', false );
					return false;	// do not click the button after the script by returning false.
				});
				window.send_to_editor = function( html ) {
					var image_url = $( 'img',html ).attr( 'src' );
					$( '#' + field_id ).val( image_url );	// sets the image url in the main text field.
					tb_remove();	// close the thickbox
					$( '#image_preview_' + field_id ).attr( 'src', image_url );	// updates the preview image
					$( '#image_preview_container_' + field_id ).css( 'display', '' );	// updates the visibility
					$( '#image_preview_' + field_id ).show()	// updates the visibility
				}
			});
		";
	}

	/**
	 * Returns the color picker JavaScript script loaded in the head tag of the created admin pages.
	 * @since			2.0.0
	 * @var			string
	 * @static
	 * @remark		It is accessed from the main class and meta box class.
	 * @remark		This is made to be a method rather than a property because in the future a variable may need to be used in the script code like the above image selector script.
	 * @access		public	
	 * @internal
	 * @return			string			The image selector script.
	 * @deprecated
	 */ 
	public static function getColorPickerScript() {
		return "
			jQuery( document ).ready( function(){
				'use strict';
				// This if statement checks if the color picker element exists within jQuery UI
				// If it does exist then we initialize the WordPress color picker on our text input field
				if( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function' ){
					var myOptions = {
						// you can declare a default color here,
						// or in the data-default-color attribute on the input
						defaultColor: false,
						// a callback to fire whenever the color changes to a valid color
						change: function(event, ui){
							// reference : http://automattic.github.io/Iris/
							// update the image element as well
							// event = standard jQuery event, produced by whichever control was changed.
							// ui = standard jQuery UI object, with a color member containing a Color.js object

							// change the headline color
							// jQuery( '#widget_box_container_background_color_image' ).css( 'background-color', ui.color.toString());	
							
						},
						// a callback to fire when the input is emptied or an invalid color
						clear: function() {
							// jQuery( '#widget_box_container_background_color_image' ).css( 'background-color', 'transparent' );	
							
						},
						// hide the color picker controls on load
						hide: true,
						// show a group of common colors beneath the square
						// or, supply an array of colors to customize further
						palettes: true
					};			
					jQuery( '.input_color' ).wpColorPicker( myOptions );
				}
				else {
					//We use farbtastic if the WordPress color picker widget doesn't exist
					// jQuery( '.colorpicker' ).farbtastic( '.input_color' );
				}
			});	
		";			
	}
	
	/**
	 * Calculates the subtraction of two values with the array key of <em>numOrder</em>
	 * 
	 * This is used to sort arrays.
	 * 
	 * @since			2.0.0
	 * @remark			a callback method for uasort().
	 * @return			integer
	 */ 
	public function sortByOrder( $a, $b ) {	
		return $a['numOrder'] - $b['numOrder'];
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
	public $strMetaBoxID ='';
	
	/**
	 * Stores the meta box title.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @var				string
	 */ 
	public $strTitle = '';

	/**
	 * Stores the post type slugs associated with the meta box.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @var				array
	 */ 	
	public $arrPostTypes = array();
	
	/**
	 * Stores the parameter value, context, for the add_meta_box() function. 
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @remark			The value can be either 'normal', 'advanced', or 'side'.
	 * @var				string
	 * @see				http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
	 */ 
	public $strContext = 'normal';

	/**
	 * Stores the parameter value, priority, for the add_meta_box() function. 
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @remark			The value can be either 'high', 'core', 'default' or 'low'.
	 * @var				string
	 * @see				http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
	 */ 	
	public $strPriority = 'default';
	
	/**
	 * Stores the extended class name.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 */ 
	public $strClassName = '';
	
	/**
	 * Stores the capability for displayable elements.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 */ 	
	public $strCapability = 'edit_posts';
	
	/**
	 * @internal
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	*/ 		
	public $strPrefixStart = 'start_';	
	
	/**
	 * Stores the field arrays for meta box form elements.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 			
	public $arrFields = array();
	
	/**
	 * Stores option values for form fields.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */	 
	public $arrOptions = array();
	
	/**
	 * Stores the script to be embedded in the head tag.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 		
	public $strScript = "";

	/**
	 * Stores the CSS rules to be embedded in the head tag.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 			
	public $strStyle = '';
	/**
	 * Stores the media uploader box's title.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 
	public $strThickBoxTitle = '';
	
	/**
	 * Stores the label for for the "Insert to Post" button in the media uploader box.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 	
	public $strThickBoxButtonUseThis = '';

	/**
	 * Stores text to insert into the contextual help tab.
	 * @since			2.1.0
	 */ 
	public $arrHelpTabText = array();
	
	/**
	 * Stores text to insert into the sidebar of a contextual help tab.
	 * @since			2.1.0
	 */ 
	public $arrHelpTabTextSide = array();
	
	// Default values
	/**
	 * Represents the structure of field array for meta box form fields.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 
	public static $arrStructure_Field = array(
		'strFieldID'		=> null,	// ( mandatory ) the field ID
		'strType'			=> null,	// ( mandatory ) the field type.
		'strTitle' 			=> null,	// the field title
		'strDescription'	=> null,	// an additional note 
		'strCapability'		=> null,	// an additional note 
		'strTip'			=> null,	// pop up text
		// 'options'			=> null,	// ? don't remember what this was for
		'vValue'			=> null,	// allows to override the stored value
		'vDefault'			=> null,	// allows to set default values.
		'strName'			=> null,	// allows to set custom field name
		'vLabel'			=> '',		// sets the label for the field. Setting a non-null value will let it parsed with the loop ( foreach ) of the input element rendering method.
		'fIf'				=> true,
		'strHelp'			=> null,	// since 2.1.0
		'strHelpAside'		=> null,	// since 2.1.0
		'fHideTitleColumn'	=> null,	// since 2.1.2
		
		// The followings may need to be uncommented.
		// 'strClassName' => null,		// This will be assigned automatically in the formatting method.
		// 'strError' => null,			// error message for the field
		// 'strBeforeField' => null,
		// 'strAfterField' => null,
		// 'numOrder' => null,			// do not set the default number here for this key.		

		'fRepeatable'		=> null,	// since 2.1.3		
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
	public $strPostType = '';
	
	/**
	 * Stores the post type argument.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @var				array
	 * @access			public
	 */ 
	public $arrPostTypeArgs = array();	

	/**
	 * Stores the extended class name.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @var				string
	 * @access			public
	 */ 	
	public $strClassName = '';

	/**
	 * Stores the column headers of the post listing table.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @see				http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_edit-post_type_columns
	 * @remark			This should be overriden in the constructor because it includes translated text.
	 * @internal
	 * @access			public
	 */ 	
	public $arrColumnHeaders = array(
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
	public $arrColumnSortable = array(
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
	public $strCallerPath = '';
	
	// Prefixes
	/**
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @internal
	 * @access			protected
	 */ 	
	public $strPrefix_Start = 'start_';
	/**
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 	
	public $strPrefix_Cell = 'cell_';
	
	// Containers
	/**
	 * Stores custom taxonomy slugs.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 	
	public $arrTaxonomies;		// stores the registering taxonomy info.
	
	/**
	 * Stores the taxonomy IDs as value to indicate whether the drop-down filter option should be displayed or not.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 	
	public $arrTaxonomyTableFilters = array();	
	
	/**
	 * Stores removing taxonomy menus' info.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 	
	public $arrTaxonomyRemoveSubmenuPages = array();	
	
	// Default Values
	/**
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 					
	public $fEnableAutoSave = true;	

	/**
	 * Stores the flag value which indicates whether author table filters should be enabled or not.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 					
	public $fEnableAuthorTableFileter = false;	
	
	// Strings
	/**
	 * Stores the CSS rules.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AdminPageFramework_PostType_Properties.
	 * @remark			Unlike the pages and meta boxes style, it is empty because they are for setting fields.
	 * @internal
	 */ 				
	public $strStyle = '';
	
	
	

	/**
	 * Stores the text inserted into the footer.
	 * @since			2.0.0
	 * @internal
	 */ 			
	// protected $arrFooterInfo = array();	
	
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
	public $strClassName;	
	
	/**
	 * Stores the md5 hash string of framework's instantiated object name.
	 * @since			2.1.1
	 */
	public $strClassHash;
	
	/**
	 * Stores the access level to the root page. 
	 * 
	 * When sub pages are added and the capability value is not provided, this will be applied.
	 * 
	 * @since			2.0.0
	 */ 	
	public $strCapability = 'manage_options';	
	
	/**
	 * Stores the tag for the page heading navigation bar.
	 * @since			2.0.0
	 */ 
	public $strPageHeadingTabTag = 'h2';

	/**
	 * Stores the tag for the in-page tab navigation bar.
	 * @since			2.0.0
	 */ 
	public $strInPageTabTag = 'h3';
	
	/**
	 * Stores the default page slug.
	 * @since			2.0.0
	 */ 	
	public $strDefaultPageSlug;
	
	/**
	 * Stores the adding scripts.
	 * @since			2.0.0
	 */ 		
	public $strScript;
	
	// Container arrays.
	/**
	 * A two-dimensional array storing registering sub-menu(page) item information with keys of the page slug.
	 * @since			2.0.0
	 */ 	
	public $arrPages = array(); 

	/**
	 * Stores the registered sub menu pages.
	 * 
	 * Unlike the above $arrPages that holds the pages to be added, this stores the added pages. This is referred when adding a help section.
	 * 
	 * @since			2.1.0
	 */ 
	public $arrRegisteredSubMenuPages = array();
	
	/**
	 * Stores the root menu item information for one set root menu item.
	 * @since			2.0.0
	 */ 		
	public $arrRootMenu = array(
		'strTitle' => null,				// menu label that appears on the menu list
		'strPageSlug' => null,			// menu slug that identifies the menu item
		'strURLIcon16x16' => null,		// the associated icon that appears beside the label on the list
		'intPosition'	=> null,		// determines the position of the menu
		'fCreateRoot' => null,			// indicates whether the framework should create the root menu or not.
	); 
	
	/**
	 * Stores in-page tabs.
	 * @since			2.0.0
	 */ 	
	public $arrInPageTabs = array();				
	
	/**
	 * Stores the default in-page tab.
	 * @since			2.0.0
	 */ 		
	public $arrDefaultInPageTabs = array();			
		
	/**
	 * Stores link text that is scheduled to be embedded in the plugin listing table's description column cell.
	 * @since			2.0.0
	 */ 			
	public $arrPluginDescriptionLinks = array(); 

	/**
	 * Stores link text that is scheduled to be embedded in the plugin listing table's title column cell.
	 * @since			2.0.0
	 */ 			
	public $arrPluginTitleLinks = array();			
	
	/**
	 * Stores the information to insert into the page footer.
	 * @since			2.0.0
	 */ 			
	public $arrFooterInfo = array(
		'strLeft' => '',
		'strRight' => '',
	);
		
	// Settings API
	// public $arrOptions;			// Stores the framework's options. Do not even declare the property here because the __get() magic method needs to be triggered when it accessed for the first time.

	/**
	 * The instantiated class name will be assigned in the constructor if the first parameter is not set.
	 * @since			2.0.0
	 */ 				
	public $strOptionKey = '';		

	/**
	 * Stores form sections.
	 * @since			2.0.0
	 */ 					
	public $arrSections = array();
	
	/**
	 * Stores form fields
	 * @since			2.0.0
	 */ 					
	public $arrFields = array();

	/**
	 * Stores contextual help tabs.
	 * @since			2.1.0
	 */ 	
	public $arrHelpTabs = array();
	
	/**
	 * Set one of the followings: application/x-www-form-urlencoded, multipart/form-data, text/plain
	 * @since			2.0.0
	 */ 					
	public $strFormEncType = 'multipart/form-data';	
	
	/**
	 * Stores the label for for the "Insert to Post" button in the media uploader box.
	 * @since			2.0.0
	 * @internal
	 */ 	
	public $strThickBoxButtonUseThis = '';
	
	// Flags	
	/**
	 * Decides whether the setting form tag is rendered or not.	
	 * 
	 * This will be enabled when a settings section and a field is added.
	 * @since			2.0.0
	 */ 						
	public $fEnableForm = false;			
	
	/**
	 * Indicates whether the page title should be displayed.
	 * @since			2.0.0
	 */ 						
	public $fShowPageTitle = true;	
	
	/**
	 * Indicates whether the page heading tabs should be displayed.
	 * @since			2.0.0
	 * @remark			Used by the showPageHeadingTabs() method.
	 */ 	
	public $fShowPageHeadingTabs = true;

	/**
	 * Indicates whether the in-page tabs should be displayed.
	 * 
	 * This sets globally among the script using the framework. 
	 * 
	 * @since			2.1.2
	 * @remark			Used by the showInPageTabs() method.
	 */
	public $fShowInPageTabs = true;

	/**
	 * Represents the structure of the array for enqueuing scripts and styles.
	 * @since			2.1.2
	 */
	public static $arrStructure_EnqueuingScriptsAndStyles = array(
		'strURL' => null,
		'strPageSlug' => null,
		'strTabSlug' => null,
		'strType' => null,		// script or style
		'strHandleID' => null,
		'arrDependencies' => array(),
        'strVersion' => false,		// although the type should be string, the wp_enqueue_...() functions want false as the default value.
        'arrTranslation' => array(),	// only for scripts
        'fInFooter' => false,	// only for scripts
		'strMedia' => 'all',	// only for styles		
	);
	/**
	 * Stores enqueuing script URLs and their criteria.
	 * @since			2.1.2
	 */
	public $arrEnqueuingScripts = array();
	/**	
	 * Stores enqueuing style URLs and their criteria.
	 * @since			2.1.2
	 */	
	public $arrEnqueuingStyles = array();
	/**
	 * Stores the index of enqueued scripts.
	 * 
	 * @since			2.1.2
	 */
	public $intEnqueuedScriptIndex = 0;
	/**
	 * Stores the index of enqueued styles.
	 * 
	 * The index number will be incremented as a script is enqueued regardless a previously added enqueue item has been removed or not.
	 * This is because this index number will be used for the script handle ID which is automatically generated.
	 * 
	 * @since			2.1.2
	 */	
	public $intEnqueuedStyleIndex = 0;
	/**
	 * Stores the set administration notices.
	 * 
	 * The index number will be incremented as a script is enqueued regardless a previously added enqueue item has been removed or not.
	 * This is because this index number will be used for the style handle ID which is automatically generated.
	 * @since			2.1.2
	 */
	public $arrAdminNotices	= array();
	
	/**
	 * Stores the disallowed query keys in the links generated by the main class of the framework.
	 * 
	 * @remark			Currently this does not take effect on the meta box and post type classes of the framework.
	 * @since			2.1.2
	 */
	public $arrDisallowedQueryKeys	= array( 'settings-updated' );
	
	
	/**
	 * Construct the instance of AdminPageFramework_Properties class object.
	 * 
	 * @remark			Used by the showInPageTabs() method.
	 * @since			2.0.0
	 * @return			void
	 */ 
	public function __construct( $strClassName, $strOptionKey, $strCapability='manage_options' ) {
		
		$this->strClassName = $strClassName;		
		$this->strClassHash = md5( $strClassName );
		$this->strOptionKey = $strOptionKey ? $strOptionKey : $strClassName;
		$this->strCapability = empty( $strCapability ) ? $this->strCapability : $strCapability;
		
	}
	
	/*
	 * Magic methods
	 * */
	public function &__get( $strName ) {
		
		// If $this->arrOptions is called for the first time, retrieve the option data from the database and assign to the property.
		// One this is done, calling $this->arrOptions will not trigger the __get() magic method any more.
		// Without the the ampersand in the method name, it causes a PHP warning.
		if ( $strName == 'arrOptions' ) {
			$this->arrOptions = $this->getOptions();
			return $this->arrOptions;	
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
	public function isPageAdded( $strPageSlug='' ) {	
		
		$strPageSlug = ! empty( $strPageSlug ) ? $strPageSlug : ( isset( $_GET['page'] ) ? $_GET['page'] : '' );
		return ( array_key_exists( trim( $strPageSlug ), $this->arrPages ) )
			? true
			: false;
	}
	
	
	public function getOptions() {
		
		$vOptions = get_option( $this->strOptionKey );
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
		return $this->strCapability;
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
	 
	public function __construct( $arrPostElement ) {
		
		$this->arrPostElement = $arrPostElement;	// e.g. $_POST['__import'] or $_POST['__export'] or $_POST['__redirect']
		
	}
	
	/**
	 * Retrieves the value of the specified element key.
	 * 
	 * The element key is either a single key or two keys. The two keys means that the value is stored in the second dimension.
	 * 
	 * @since			2.0.0
	 */ 
	protected function getElement( $arrElement, $arrElementKey, $strElementKey='format' ) {
			
		$strFirstDimensionKey = $arrElementKey[ 0 ];
		if ( ! isset( $arrElement[ $strFirstDimensionKey ] ) || ! is_array( $arrElement[ $strFirstDimensionKey ] ) ) return 'ERROR_A';

		/* For single element, e.g.
		 * <input type="hidden" name="__import[import_single][import_option_key]" value="APF_GettingStarted">
		 * <input type="hidden" name="__import[import_single][format]" value="array">
		 * */	
		if ( isset( $arrElement[ $strFirstDimensionKey ][ $strElementKey ] ) && ! is_array( $arrElement[ $strFirstDimensionKey ][ $strElementKey ] ) )
			return $arrElement[ $strFirstDimensionKey ][ $strElementKey ];

		/* For multiple elements, e.g.
		 * <input type="hidden" name="__import[import_multiple][import_option_key][2]" value="APF_GettingStarted.txt">
		 * <input type="hidden" name="__import[import_multiple][format][2]" value="array">
		 * */
		if ( ! isset( $arrElementKey[ 1 ] ) ) return 'ERROR_B';
		$strKey = $arrElementKey[ 1 ];
		if ( isset( $arrElement[ $strFirstDimensionKey ][ $strElementKey ][ $strKey ] ) )
			return $arrElement[ $strFirstDimensionKey ][ $strElementKey ][ $strKey ];
			
		return 'ERROR_C';	// Something wrong happened.
		
	}	
	
	/**
	 * Retrieves an array consisting of two values.
	 * 
	 * The first element is the fist dimension's key and the second element is the second dimension's key.
	 * @since			2.0.0
	 */
	protected function getElementKey( $arrElement, $strFirstDimensionKey ) {
		
		if ( ! isset( $arrElement[ $strFirstDimensionKey ] ) ) return;
		
		// Set the first element the field ID.
		$arrEkementKey = array( 0 => $strFirstDimensionKey );

		// For single export buttons, e.g. name="__import[submit][import_single]" 		
		if ( ! is_array( $arrElement[ $strFirstDimensionKey ] ) ) return $arrEkementKey;
		
		// For multiple ones, e.g. name="__import[submit][import_multiple][1]" 		
		foreach( $arrElement[ $strFirstDimensionKey ] as $k => $v ) {
			
			// Only the pressed export button's element is submitted. In other words, it is necessary to check only one item.
			$arrEkementKey[] = $k;
			return $arrEkementKey;			
				
		}		
	}
		
	public function getFieldID() {
		
		// e.g.
		// single:		name="__import[submit][import_single]"
		// multiple:	name="__import[submit][import_multiple][1]"
		
		if ( isset( $this->strFieldID ) && $this->strFieldID  ) return $this->strFieldID;
		
		// Only the pressed element will be stored in the array.
		foreach( $this->arrPostElement['submit'] as $strKey => $v ) {	// $this->arrPostElement should have been set in the constructor.
			$this->strFieldID = $strKey;
			return $this->strFieldID;
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
	
	public function __construct( $arrFilesImport, $arrPostImport ) {

		// Call the parent constructor. This must be done before the getFieldID() method that uses the $arrPostElement property.
		parent::__construct( $arrPostImport );
	
		$this->arrFilesImport = $arrFilesImport;
		$this->arrPostImport = $arrPostImport;
		
		// Find the field ID and the element key ( for multiple export buttons )of the pressed submit ( export ) button.
		$this->strFieldID = $this->getFieldID();
		$this->arrElementKey = $this->getElementKey( $arrPostImport['submit'], $this->strFieldID );
			
	}
	
	private function getElementInFilesArray( $arrFilesImport, $arrElementKey, $strElementKey='error' ) {

		$strElementKey = strtolower( $strElementKey );
		$strFieldID = $arrElementKey[ 0 ];	// or simply assigning $this->strFieldID would work as well.
		if ( ! isset( $arrFilesImport[ $strElementKey ][ $strFieldID ] ) ) return 'ERROR_A: The given key does not exist.';
	
		// For single export buttons, e.g. $_FILES[__import][ $strElementKey ][import_single] 
		if ( isset( $arrFilesImport[ $strElementKey ][ $strFieldID ] ) && ! is_array( $arrFilesImport[ $strElementKey ][ $strFieldID ] ) )
			return $arrFilesImport[ $strElementKey ][ $strFieldID ];
			
		// For multiple import buttons, e.g. $_FILES[__import][ $strElementKey ][import_multiple][2]
		if ( ! isset( $arrElementKey[ 1 ] ) ) return 'ERROR_B: the sub element is not set.';
		$strKey = $arrElementKey[ 1 ];		
		if ( isset( $arrPostImport[ $strElementKey ][ $strFieldID ][ $strKey ] ) )
			return $arrPostImport[ $strElementKey ][ $strFieldID ][ $strKey ];

		// Something wrong happened.
		return 'ERROR_C: unexpected problem occurred.';
		
	}	
		
	public function getError() {
		
		return $this->getElementInFilesArray( $this->arrFilesImport, $this->arrElementKey, 'error' );
		
	}
	public function getType() {
		
		return $this->getElementInFilesArray( $this->arrFilesImport, $this->arrElementKey, 'type' );
		
	}
	public function getImportData() {
		
		// Retrieve the uploaded file path.
		$strFilePath = $this->getElementInFilesArray( $this->arrFilesImport, $this->arrElementKey, 'tmp_name' );
		
		// Read the file contents.
		$vData = file_exists( $strFilePath ) ? file_get_contents( $strFilePath, true ) : false;
		
		return $vData;
		
	}
	public function formatImportData( &$vData, $strFormatType=null ) {
		
		$strFormatType = isset( $strFormatType ) ? $strFormatType : $this->getFormatType();
		switch ( strtolower( $strFormatType ) ) {
			case 'text':	// for plain text.
				return;	// do nothing
			case 'json':	// for json.
				$vData = json_decode( $vData, true );	// the second parameter indicates to decode it as array.
				return;
			case 'array':	// for serialized PHP array.
			default:	// for anything else, 
				$vData = maybe_unserialize( trim( $vData ) );
				return;
		}		
	
	}
	public function getFormatType() {
					
		$this->strFormatType = isset( $this->strFormatType ) && $this->strFormatType 
			? $this->strFormatType
			: $this->getElement( $this->arrPostImport, $this->arrElementKey, 'format' );

		return $this->strFormatType;
		
	}
	public function getImportOptionKey() {
		
		$this->strImportOptionKey = isset( $this->strImportOptionKey ) && $this->strImportOptionKey 
			? $this->strImportOptionKey
			: $this->getElement( $this->arrPostImport, $this->arrElementKey, 'import_option_key' );

		return $this->strImportOptionKey;

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

	public function __construct( $arrPostExport, $strClassName ) {
		
		// Call the parent constructor.
		parent::__construct( $arrPostExport );
		
		// Properties
		$this->arrPostExport = $arrPostExport;
		$this->strClassName = $strClassName;	// will be used in the getTransientIfSet() method.
		// $this->strPageSlug = $strPageSlug;
		// $this->strTabSlug = $strTabSlug;
		
		// Find the field ID and the element key ( for multiple export buttons )of the pressed submit ( export ) button.
		$this->strFieldID = $this->getFieldID();
		$this->arrElementKey = $this->getElementKey( $arrPostExport['submit'], $this->strFieldID );
		
		// Set the file name to download and the format type. Also find whether the exporting data is set in transient.
		$this->strFileName = $this->getElement( $arrPostExport, $this->arrElementKey, 'file_name' );
		$this->strFormatType = $this->getElement( $arrPostExport, $this->arrElementKey, 'format' );
		$this->fIsDataSet = $this->getElement( $arrPostExport, $this->arrElementKey, 'transient' );
	
	}
	
	public function getTransientIfSet( $vData ) {
		
		if ( $this->fIsDataSet ) {
			$strKey = $this->arrElementKey[1];
			$strTransient = isset( $this->arrElementKey[1] ) ? "{$this->strClassName}_{$this->strFieldID}_{$this->arrElementKey[1]}" : "{$this->strClassName}_{$this->strFieldID}";
			$tmp = get_transient( md5( $strTransient ) );
			if ( $tmp !== false ) {
				$vData = $tmp;
				delete_transient( md5( $strTransient ) );
			}
		}
		return $vData;
	}
	
	public function getFileName() {
		return $this->strFileName;
	}
	public function getFormat() {
		return $this->strFormatType;
	}

	/**
	 * Performs exporting data.
	 * 
	 * @since			2.0.0
	 */ 
	public function doExport( $vData, $strFileName=null, $strFormatType=null ) {

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
		$strFileName = isset( $strFileName ) ? $strFileName : $this->strFileName;
		$strFormatType = isset( $strFormatType ) ? $strFormatType : $this->strFormatType;
							
		// Do export.
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $strFileName );
		switch ( strtolower( $strFormatType ) ) {
			case 'text':	// for plain text.
				if ( is_array( $vData ) || is_object( $vData ) ) {
					$oDebug = new AdminPageFramework_Debug;
					$strData = $oDebug->getArray( $vData );
					die( $strData );
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

if ( ! class_exists( 'AdminPageFramework_LinkBase' ) ) :
/**
 * Provides methods for HTML link elements.
 *
 * @abstract
 * @since			2.0.0
 * @extends			AdminPageFramework_Utilities
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Link
 */
abstract class AdminPageFramework_LinkBase extends AdminPageFramework_Utilities {
	
	/**
	 * @internal
	 * @since			2.0.0
	 */ 
	private static $arrStructure_CallerInfo = array(
		'strPath'			=> null,
		'strType'			=> null,
		'strName'			=> null,		
		'strURI'			=> null,
		'strVersion'		=> null,
		'strThemeURI'		=> null,
		'strScriptURI'		=> null,
		'strAuthorURI'		=> null,
		'strAuthor'			=> null,
		'strDescription'	=> null,
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
	protected function getCallerInfo( $strCallerPath=null ) {
		
		$arrCallerInfo = self::$arrStructure_CallerInfo;
		$arrCallerInfo['strPath'] = $strCallerPath;
		$arrCallerInfo['strType'] = $this->getCallerType( $arrCallerInfo['strPath'] );

		if ( $arrCallerInfo['strType'] == 'unknown' ) return $arrCallerInfo;
		
		if ( $arrCallerInfo['strType'] == 'plugin' ) 
			return $this->getScriptData( $arrCallerInfo['strPath'], $arrCallerInfo['strType'] ) + $arrCallerInfo;
			
		if ( $arrCallerInfo['strType'] == 'theme' ) {
			$oTheme = wp_get_theme();	// stores the theme info object
			return array(
				'strName'			=> $oTheme->Name,
				'strVersion' 		=> $oTheme->Version,
				'strThemeURI'		=> $oTheme->get( 'ThemeURI' ),
				'strURI'			=> $oTheme->get( 'ThemeURI' ),
				'strAuthorURI'		=> $oTheme->get( 'AuthorURI' ),
				'strAuthor'			=> $oTheme->get( 'Author' ),				
			) + $arrCallerInfo;	
		}
	}

	/**
	 * Retrieves the library script info.
	 * 
	 * @since			2.1.1
	 */
	protected function getLibraryInfo() {
		return $this->getScriptData( __FILE__, 'library' ) + self::$arrStructure_CallerInfo;
	}
	
	/**
	 * Determines the script type.
	 * 
	 * It tries to find what kind of script this is, theme, plugin or something else from the given path.
	 * @since			2.0.0
	 * @return		string				Returns either 'theme', 'plugin', or 'unknown'
	 */ 
	protected function getCallerType( $strScriptPath ) {
		
		if ( preg_match( '/[\/\\\\]themes[\/\\\\]/', $strScriptPath, $m ) ) return 'theme';
		if ( preg_match( '/[\/\\\\]plugins[\/\\\\]/', $strScriptPath, $m ) ) return 'plugin';
		return 'unknown';	
	
	}
	protected function getCallerPath() {

		foreach( debug_backtrace() as $arrDebugInfo )  {			
			if ( $arrDebugInfo['file'] == __FILE__ ) continue;
			return $arrDebugInfo['file'];	// return the first found item.
		}
	}	
	
	/**
	 * Sets the default footer text on the left hand side.
	 * 
	 * @since			2.1.1
	 */
	protected function setFooterInfoLeft( $arrScriptInfo, &$strFooterInfoLeft ) {
		
		$strDescription = empty( $arrScriptInfo['strDescription'] ) 
			? ""
			: "&#13;{$arrScriptInfo['strDescription']}";
		$strVersion = empty( $arrScriptInfo['strVersion'] )
			? ""
			: "&nbsp;{$arrScriptInfo['strVersion']}";
		$strPluginInfo = empty( $arrScriptInfo['strURI'] ) 
			? $arrScriptInfo['strName'] 
			: "<a href='{$arrScriptInfo['strURI']}' target='_blank' title='{$arrScriptInfo['strName']}{$strVersion}{$strDescription}'>{$arrScriptInfo['strName']}</a>";
		$strAuthorInfo = empty( $arrScriptInfo['strAuthorURI'] )	
			? $arrScriptInfo['strAuthor'] 
			: "<a href='{$arrScriptInfo['strAuthorURI']}' target='_blank'>{$arrScriptInfo['strAuthor']}</a>";
		$strAuthorInfo = empty( $arrScriptInfo['strAuthor'] ) 
			? $strAuthorInfo 
			: ' by ' . $strAuthorInfo;
		$strFooterInfoLeft =  $strPluginInfo . $strAuthorInfo;
		
	}
	/**
	 * Sets the default footer text on the right hand side.
	 * 
	 * @since			2.1.1
	 */	
	protected function setFooterInfoRight( $arrScriptInfo, &$strFooterInfoRight ) {

		$strDescription = empty( $arrScriptInfo['strDescription'] ) 
			? ""
			: "&#13;{$arrScriptInfo['strDescription']}";
		$strVersion = empty( $arrScriptInfo['strVersion'] )
			? ""
			: "&nbsp;{$arrScriptInfo['strVersion']}";		
		$strLibraryInfo = empty( $arrScriptInfo['strURI'] ) 
			? $arrScriptInfo['strName'] 
			: "<a href='{$arrScriptInfo['strURI']}' target='_blank' title='{$arrScriptInfo['strName']}{$strVersion}{$strDescription}'>{$arrScriptInfo['strName']}</a>";			
		$strFooterInfoRight = __( 'Powered by', 'admin-page-framework' ) . '&nbsp;' 
			. $strLibraryInfo
			. ", <a href='http://wordpress.org' target='_blank' title='WordPress {$GLOBALS['wp_version']}'>WordPress</a>";
		
	}
}
endif;

if ( ! class_exists( 'AdminPageFramework_LinkForPostType' ) ) :
/**
 * Provides methods for HTML link elements for custom post types.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_Utilities
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Link
 */
class AdminPageFramework_LinkForPostType extends AdminPageFramework_LinkBase {
	
	/**
	 * Stores the information to embed into the page footer.
	 * @since			2.0.0
	 * @remark			This is accessed from the AdminPageFramework_PostType class.
	 */ 
	public $arrFooterInfo = array(
		'strLeft' => '',
		'strRight' => '',
	);
	
	public function __construct( $strPostTypeSlug, $strCallerPath=null ) {
		
		if ( ! is_admin() ) return;
		
		$this->strPostTypeSlug = $strPostTypeSlug;
		$this->strCallerPath = file_exists( $strCallerPath ) ? $strCallerPath : $this->getCallerPath();
		$this->arrScriptInfo = $this->getCallerInfo( $this->strCallerPath ); 
		$this->arrLibraryInfo = $this->getLibraryInfo();
		
		$this->strSettingPageLinkTitle = __( 'Manage', 'admin-page-framework' );
		
		// Add script info into the footer 
		add_filter( 'update_footer', array( $this, 'addInfoInFooterRight' ), 11 );
		add_filter( 'admin_footer_text' , array( $this, 'addInfoInFooterLeft' ) );	
		$this->setFooterInfoLeft( $this->arrScriptInfo, $this->arrFooterInfo['strLeft'] );
		$this->setFooterInfoRight( $this->arrLibraryInfo, $this->arrFooterInfo['strRight'] );
		
		// For the plugin listing page
		if ( $this->arrScriptInfo['strType'] == 'plugin' )
			add_filter( 
				'plugin_action_links_' . plugin_basename( $this->arrScriptInfo['strPath'] ),
				array( $this, 'addSettingsLinkInPluginListingPage' ), 
				20 	// set a lower priority so that the link will be embedded at the beginning ( the most left hand side ).
			);	
		
		// For post type posts listing table page ( edit.php )
		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->strPostTypeSlug )
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
	public function addPostTypeQueryInEditPostLink( $strURL, $intPostID=null, $strContext=null ) {
		return add_query_arg( array( 'post' => $intPostID, 'action' => 'edit', 'post_type' => $this->strPostTypeSlug ), $strURL );	
	}	
	public function addSettingsLinkInPluginListingPage( $arrLinks ) {
		
		// http://.../wp-admin/edit.php?post_type=[...]
		array_unshift(	
			$arrLinks,
			"<a href='edit.php?post_type={$this->strPostTypeSlug}'>" . $this->strSettingPageLinkTitle . "</a>"
		); 
		return $arrLinks;		
		
	}
	
	/**
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the filter hook, <em>admin_footer_text</em>.
	 */ 
	public function addInfoInFooterLeft( $strLinkHTML='' ) {
		
		if ( ! isset( $_GET['post_type'] ) ||  $_GET['post_type'] != $this->strPostTypeSlug )
			return $strLinkHTML;	// $strLinkHTML is given by the hook.

		if ( empty( $this->arrScriptInfo['strName'] ) ) return $strLinkHTML;
					
		return $this->arrFooterInfo['strLeft'];
		
	}
	public function addInfoInFooterRight( $strLinkHTML='' ) {
		
		if ( ! isset( $_GET['post_type'] ) ||  $_GET['post_type'] != $this->strPostTypeSlug )
			return $strLinkHTML;	// $strLinkHTML is given by the hook.
			
		return $this->arrFooterInfo['strRight'];		
			
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
 * @extends			AdminPageFramework_LinkBase
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Link
 */
class AdminPageFramework_Link extends AdminPageFramework_LinkBase {
	
	/**
	 * Stores the caller script path.
	 * @since			2.0.0
	 */ 
	private $strCallerPath;
	
	/**
	 * The property object, commonly shared.
	 * @since			2.0.0
	 */ 
	private $oProps;
	
	public function __construct( &$oProps, $strCallerPath=null ) {
		
		if ( ! is_admin() ) return;
		
		$this->oProps = $oProps;
		$this->strCallerPath = file_exists( $strCallerPath ) ? $strCallerPath : $this->getCallerPath();
		$this->oProps->arrScriptInfo = $this->getCallerInfo( $this->strCallerPath ); 
		$this->oProps->arrLibraryInfo = $this->getLibraryInfo();
		
		// Add script info into the footer 
		add_filter( 'update_footer', array( $this, 'addInfoInFooterRight' ), 11 );
		add_filter( 'admin_footer_text' , array( $this, 'addInfoInFooterLeft' ) );	
		$this->setFooterInfoLeft( $this->oProps->arrScriptInfo, $this->oProps->arrFooterInfo['strLeft'] );
		$this->setFooterInfoRight( $this->oProps->arrLibraryInfo, $this->oProps->arrFooterInfo['strRight'] );
	
		if ( $this->oProps->arrScriptInfo['strType'] == 'plugin' )
			add_filter( 'plugin_action_links_' . plugin_basename( $this->oProps->arrScriptInfo['strPath'] ) , array( $this, 'addSettingsLinkInPluginListingPage' ) );

	}

	
	/**	
	 * 
	 * @since			2.0.0
	 * @remark			The scope is public because this is accessed from an extended class.
	 */ 
	public $arrStructure_SubMenuLink = array(		
		'strMenuTitle' => null,
		'strURL' => null,
		'strCapability' => null,
		'numOrder' => null,
		'strType' => 'link',
		'fShowPageHeadingTab' => true,
	
	);
	// public function addSubMenuLinks() {
		// foreach ( func_get_args() as $arrSubMenuLink ) {
			// $arrSubMenuLink = $arrSubMenuLink + self::$arrStructure_SubMenuLink;	// avoid undefined index warnings.
			// $this->addSubMenuLink(
				// $arrSubMenuLink['strMenuTitle'],
				// $arrSubMenuLink['strURL'],				
				// $arrSubMenuLink['strCapability'],
				// $arrSubMenuLink['numOrder']			
			// );				
		// }
	// }
	public function addSubMenuLink( $strMenuTitle, $strURL, $strCapability=null, $numOrder=null, $fShowPageHeadingTab=true ) {
		
		$intCount = count( $this->oProps->arrPages );
		$this->oProps->arrPages[ $strURL ] = array(  
			'strMenuTitle'		=> $strMenuTitle,
			'strPageTitle'		=> $strMenuTitle,	// used for the page heading tabs.
			'strURL'			=> $strURL,
			'strType'			=> 'link',	// this is used to compare with the 'page' type.
			'strCapability'		=> isset( $strCapability ) ? $strCapability : $this->oProps->strCapability,
			'numOrder'			=> is_numeric( $numOrder ) ? $numOrder : $intCount + 10,
			'fShowPageHeadingTab'	=> $fShowPageHeadingTab,
		);	
			
	}
			
	/*
	 * Methods for embedding links 
	 */ 	
	public function addLinkToPluginDescription( $vLinks ) {
		
		if ( !is_array( $vLinks ) )
			$this->oProps->arrPluginDescriptionLinks[] = $vLinks;
		else
			$this->oProps->arrPluginDescriptionLinks = array_merge( $this->oProps->arrPluginDescriptionLinks , $vLinks );
	
		add_filter( 'plugin_row_meta', array( $this, 'addLinkToPluginDescription_Callback' ), 10, 2 );

	}
	public function addLinkToPluginTitle( $vLinks ) {
		
		if ( !is_array( $vLinks ) )
			$this->oProps->arrPluginTitleLinks[] = $vLinks;
		else
			$this->oProps->arrPluginTitleLinks = array_merge( $this->oProps->arrPluginTitleLinks, $vLinks );
		
		add_filter( 'plugin_action_links_' . plugin_basename( $this->oProps->arrScriptInfo['strPath'] ), array( $this, 'AddLinkToPluginTitle_Callback' ) );

	}
	
	/*
	 * Callback methods
	 */ 
	
	/**
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the filter hook, <em>admin_footer_text</em>.
	 */ 
	public function addInfoInFooterLeft( $strLinkHTML='' ) {

		if ( ! isset( $_GET['page'] ) || ! $this->oProps->isPageAdded( $_GET['page'] )  ) 
			return $strLinkHTML;	// $strLinkHTML is given by the hook.
		
		if ( empty( $this->oProps->arrScriptInfo['strName'] ) ) return $strLinkHTML;
		
		return $this->oProps->arrFooterInfo['strLeft'];

	}
	public function addInfoInFooterRight( $strLinkHTML='' ) {
		
		if ( ! isset( $_GET['page'] ) || ! $this->oProps->isPageAdded( $_GET['page'] )  ) 
			return $strLinkHTML;	// $strLinkTHML is given by the hook.
			
		return $this->oProps->arrFooterInfo['strRight'];
			
	}
	
	public function addSettingsLinkInPluginListingPage( $arrLinks ) {
		
		// For a custom root slug,
		$strLinkURL = preg_match( '/^.+\.php/', $this->oProps->arrRootMenu['strPageSlug'] ) 
			? add_query_arg( array( 'page' => $this->oProps->strDefaultPageSlug ), admin_url( $this->oProps->arrRootMenu['strPageSlug'] ) )
			: "admin.php?page={$this->oProps->strDefaultPageSlug}";
		
		array_unshift(	
			$arrLinks,
			'<a href="' . $strLinkURL . '">' . __( 'Settings', 'admin-page-framework' ) . '</a>'
		); 
		return $arrLinks;
		
	}	
	
	public function addLinkToPluginDescription_Callback( $arrLinks, $strFile ) {

		if ( $strFile != plugin_basename( $this->oProps->arrScriptInfo['strPath'] ) ) return $arrLinks;
		
		// Backward compatibility sanitization.
		$arrAddingLinks = array();
		foreach( $this->oProps->arrPluginDescriptionLinks as $vLinkHTML )
			if ( is_array( $vLinkHTML ) )	// should not be an array
				$arrAddingLinks = array_merge( $vLinkHTML, $arrAddingLinks );
			else
				$arrAddingLinks[] = ( string ) $vLinkHTML;
		
		return array_merge( $arrLinks, $arrAddingLinks );
		
	}			
	public function addLinkToPluginTitle_Callback( $arrLinks ) {

		// Backward compatibility sanitization.
		$arrAddingLinks = array();
		foreach( $this->oProps->arrPluginTitleLinks as $vLinkHTML )
			if ( is_array( $vLinkHTML ) )	// should not be an array
				$arrAddingLinks = array_merge( $vLinkHTML, $arrAddingLinks );
			else
				$arrAddingLinks[] = ( string ) $vLinkHTML;
		
		return array_merge( $arrLinks, $arrAddingLinks );
		
	}		
}
endif;

if ( ! class_exists( 'AdminPageFramework_Debug' ) ) :
/**
 * Provides debugging methods.
 *
 * @since			2.0.0
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
class AdminPageFramework_Debug {
		
	public function dumpArray( $arr, $strFilePath=null ) {
				
		echo $this->getArray( $arr, $strFilePath );
		
	}
	
	public function getArray( $arr, $strFilePath=null ) {
			
		if ( $strFilePath ) 
			self::logArray( $arr, $strFilePath );			
			
		// esc_html() has a bug that breaks with complex HTML code.
		return "<pre class='dump-array'>" . htmlspecialchars( print_r( $arr, true ) ) . "</pre>";		
	}	
	
	/**
	 * Logs given array output into the given file.
	 * 
	 * @since			2.1.1
	 */
	static public function logArray( $arr, $strFilePath=null ) {
								
		file_put_contents( 
			$strFilePath ? $strFilePath : dirname( __FILE__ ) . '/array_log.txt', 
			date( "Y/m/d H:i:s" ) . PHP_EOL
			. print_r( $arr, true ) . PHP_EOL . PHP_EOL
			, FILE_APPEND 
		);					
							
	}	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputField' ) ) :
/**
 * Provides methods for rendering form input fields.
 *
 * @since			2.0.0
 * @since			2.0.1			Added the <em>size</em> type.
 * @extends			AdminPageFramework_Utilities
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AdminPageFramework_InputField extends AdminPageFramework_Utilities {
	
	/**
	 * Represents the structure of field array.
	 * @since			2.0.0
	 * @access			private
	 */ 
	private static $arrDefaultFieldValues = array(
		'vValue' => null,			// ( array or string ) this suppress the default key value. This is useful to display the value saved in a custom place other than the framework automatically saves.
		'vDefault' => null,			// ( array or string )
		'vClassAttribute' => null,	// ( array or string ) the class attribute of the input field. Do not set an empty value here, but null because the submit field type uses own default value.
		'vLabel' => '',				// ( array or string ) labels for some input fields. Do not set null here because it is casted as string in the field output methods, which creates an element of empty string so that it can be iterated with foreach().
		'vLabelMinWidth' => 120,	// ( array or integer ) This sets the min-width of the label tag for the textarea, text, and numbers input types.
		'vDelimiter' => null,		// do not set an empty value here because the radio input field uses own default value.
		'vDisable' => null,			// ( array or boolean ) This value indicates whether the set field is disabled or not. 
		'vReadOnly' => '',			// ( array or boolean ) sets the readonly attribute to text and textarea input fields.
		'vMultiple'	=> false,		// ( array or boolean ) This value indicates whether the select tag should have the multiple attribute or not.
		'vBeforeInputTag' => '',
		'vAfterInputTag' => '',
		'vSize' => null,			// ( array or integer )	This is for the text, the select field, and the image field type. Do not set a value here.
		'vRows' => 4,				// ( array or integer ) This is for the textarea field type.
		'vCols' => 80,				// ( array or integer ) This is for the textarea field type.
		'vRich' => null,			// ( array or boolean ) This is for the textarea field type.
		'vMax' => null,				// ( array or integer ) This is for the number field type.
		'vMin' => null,				// ( array or integer ) This is for the number field type.
		'vStep' => null,			// ( array or integer ) This is for the number field type.
		'vMaxLength' => null,		// Maximum number of characters in textara, text, number etc.
		'vAcceptAttribute' => null,	// ( array or string )	This is for the file and import field type. Do not set a default value here because it will be passed in the dealing method.
		'vExportFileName' => null,	// ( array or string )	This is for the export field type. Do not set a default value here.
		'vExportFormat' => null,	// ( array or string )	This is for the export field type. Do not set a default value here. Currently array, json, and text are supported.
		'vExportData' => null,		// ( array or string or object ) This is for the export field type. 
		'vImportOptionKey' => null,	// ( array or string )	This is for the import field type. The default is the set option key for the framework.
		'vImportFormat' => null,	// ( array or string )	This is for the import field type. Do not set a default value here. Currently array, json, and text are supported.
		'vLink'	=> null,			// ( array or string )	This is for the submit field type.
		'vRedirect'	=> null,		// ( array or string )	This is for the submit field type.
		'vReset'	=> null,		// ( array or string )	[2.1.2+] This is for the submit field type.
		'vImagePreview' => null,	// ( array or boolean )	This is for the image filed type. For array, each element should contain a boolean value ( true/false ).
		'strTickBoxTitle' => null,	// ( string ) This is for the image field type.
		'strLabelUseThis' => null,	// ( string ) This is for the image field type.
		'vTaxonomySlug' => 'category',	// ( string ) This is for the taxonomy field type.
		'arrRemove' => array( 'revision', 'attachment', 'nav_menu_item' ), // for the posttype checklist field type
		'vWidth' => null,			// ( array or string ) This is for the select field type that specifies the width of the select tag element.
		'vDateFormat' => null,			// ( array or string ) This is for the date field type that specifies the date format.
		// 'numMaxWidth' => 400,	// for the taxonomy checklist filed type.
		// 'numMaxHeight' => 200,	// for the taxonomy checklist filed type.	
		'strHeight' => '250px',		// for the taxonomy checklist field type, since 2.1.1.
		'strWidth' => '100%',	// for the taxonomy checklist field type, since 2.1.1.
		
		// Mandatory keys.
		'strFieldID' => null,		
		
		// For the meta box class - it does not require the following keys so these helps to avoid undefined index warinings.
		'strPageSlug' => null,
		'strSectionID' => null,
		'strBeforeField' => null,
		'strAfterField' => null,
		
		// For the size field
		'vSizeUnits' => null,	// not setting the default value here. 
		
	);
	
	/**
	 * Indicates whether the creating fields are for meta box or not.
	 * @since			2.1.2
	 */
	private $fIsMetaBox = false;
		
	
	public function __construct( &$arrField, &$arrOptions, $arrErrors=array(), &$oMsg ) {
			
		$this->oMsg = $oMsg;
		$this->oUtil = new AdminPageFramework_Utilities;
		
		$this->arrField = $arrField + self::$arrDefaultFieldValues;
		$this->arrOptions = $arrOptions;
		$this->arrErrors = $arrErrors ? $arrErrors : array();
			
		$this->strFieldName = $this->getInputFieldName();
		$this->strTagID = $this->getInputTagID( $arrField );
		$this->vValue = $this->getInputFieldValue( $arrField, $arrOptions );
		
	}	
		
	private function getInputFieldName( $arrField=null ) {
		
		$arrField = isset( $arrField ) ? $arrField : $this->arrField;
		
		// If the name key is explicitly set, use it
		if ( ! empty( $arrField['strName'] ) ) return $arrField['strName'];
		
		return isset( $arrField['strOptionKey'] ) // the meta box class does not use the option key
			? "{$arrField['strOptionKey']}[{$arrField['strPageSlug']}][{$arrField['strSectionID']}][{$arrField['strFieldID']}]"
			: $arrField['strFieldID'];
		
	}

	/**
	 * Retrieves the field name attribute whose dimensional elements are delimited by the pile character.
	 * 
	 * Instead of [] enclosing array elements, it uses the pipe(|) to represent the multi dimensional array key.
	 * This is used to create a reference the submit field name to determine which button is pressed.
	 */ 
	private function getInputFieldNameFlat( $arrField=null ) {	
	
		$arrField = isset( $arrField ) ? $arrField : $this->arrField;
		return isset( $arrField['strOptionKey'] ) // the meta box class does not use the option key
			? "{$arrField['strOptionKey']}|{$arrField['strPageSlug']}|{$arrField['strSectionID']}|{$arrField['strFieldID']}"
			: $arrField['strFieldID'];
		
	}	
	private function getInputFieldValue( &$arrField, $arrOptions ) {	

		// If the value key is explicitly set, use it.
		if ( isset( $arrField['vValue'] ) ) return $arrField['vValue'];
		
		// Check if a previously saved option value exists or not.
		//  for regular setting pages. Meta boxes do not use these keys.
		if ( isset( $arrField['strPageSlug'], $arrField['strSectionID'] ) ) {			
		
			$vValue = $this->getInputFieldValueFromOptionTable( $arrField, $arrOptions );
			if ( $vValue != '' ) return $vValue;
			
		} 
		// For meta boxes
		else if ( isset( $_GET['action'], $_GET['post'] ) ) {

			$vValue = $this->getInputFieldValueFromPostTable( $_GET['post'], $arrField );
			if ( $vValue != '' ) return $vValue;
			
		}
		
		// If the default value is set,
		if ( isset( $arrField['vDefault'] ) ) return $arrField['vDefault'];
		
	}	
	private function getInputFieldValueFromOptionTable( &$arrField, &$arrOptions ) {
		
		if ( ! isset( $arrOptions[ $arrField['strPageSlug'] ][ $arrField['strSectionID'] ][ $arrField['strFieldID'] ] ) )
			return;
						
		$vValue = $arrOptions[ $arrField['strPageSlug'] ][ $arrField['strSectionID'] ][ $arrField['strFieldID'] ];
		
		// Check if it's not an array return it.
		if ( ! is_array( $vValue ) && ! is_object( $vValue ) ) return $vValue;
		
		// If it's an array, check if there is an empty value in each element.
		$vDefault = isset( $arrField['vDefault'] ) ? $arrField['vDefault'] : array(); 
		foreach ( $vValue as $strKey => &$strElement ) 
			if ( $strElement == '' )
				$strElement = $this->getCorrespondingArrayValue( $vDefault, $strKey, '' );
		
		return $vValue;
			
		
	}	
	private function getInputFieldValueFromPostTable( $intPostID, &$arrField ) {
		
		$vValue = get_post_meta( $intPostID, $arrField['strFieldID'], true );
		
		// Check if it's not an array return it.
		if ( ! is_array( $vValue ) && ! is_object( $vValue ) ) return $vValue;
		
		// If it's an array, check if there is an empty value in each element.
		$vDefault = isset( $arrField['vDefault'] ) ? $arrField['vDefault'] : array(); 
		foreach ( $vValue as $strKey => &$strElement ) 
			if ( $strElement == '' )
				$strElement = $this->getCorrespondingArrayValue( $vDefault, $strKey, '' );
		
		return $vValue;
		
	}
	
	/**
	 * Retrieves the input field value from the label.
	 * 
	 * This method is similar to the above <em>getInputFieldValue()</em> but this does not check the stored option value.
	 * It uses the value set to the <var>vLabel</var> key. 
	 * This is for submit buttons including export custom field type that the label should serve as the value.
	 * 
	 * @since			2.0.0
	 */ 
	private function getInputFieldValueFromLabel( $arrField, $arrOptions ) {	
		
		// If the value key is explicitly set, use it.
		if ( isset( $arrField['vValue'] ) ) return $arrField['vValue'];
		
		if ( isset( $arrField['vLabel'] ) ) return $arrField['vLabel'];
		
		// If the default value is set,
		if ( isset( $arrField['vDefault'] ) ) return $arrField['vDefault'];
		
	}			
	private function getInputTagID( $arrField )  {
		
		// For Settings API's form fields should have these key values.
		if ( isset( $arrField['strSectionID'], $arrField['strFieldID'] ) )
			return "{$arrField['strSectionID']}_{$arrField['strFieldID']}";
			
		// For meta box form fields,
		if ( isset( $arrField['strFieldID'] ) ) return $arrField['strFieldID'];
		if ( isset( $arrField['strName'] ) ) return $arrField['strName'];	// the name key is for the input name attribute but it's better than nothing.
		
		// Not Found - it's not a big deal to have an empty value for this. It's just for the anchor link.
		return '';
			
	}		
	
	/*
	 * Public methods
	 * */
	 
	/** 
	 * Retrieves the input field HTML output.
	 * @since			2.0.0
	 */ 
	public function getInputField( $strFieldType ) {
		
		// Prepend the field error message.
		$strOutput = isset( $this->arrErrors[ $this->arrField['strSectionID'] ][ $this->arrField['strFieldID'] ] )
			? "<span style='color:red;'>*&nbsp;{$this->arrField['strError']}" . $this->arrErrors[ $this->arrField['strSectionID'] ][ $this->arrField['strFieldID'] ] . "</span><br />"
			: '';		
		
		// Prepeare the field class selector 
		$this->strFieldClassSelector = $this->arrField['fRepeatable']
			? "admin-page-framework-field repeatable"
			: "admin-page-framework-field";
			
		// Get the input field output.
		switch ( strtolower( $strFieldType ) ) {
			case in_array( $strFieldType, array( 'text', 'password', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'time', 'url', 'week' ) ):
				$strOutput .= $this->getTextField();
				break;
			case in_array( $strFieldType, array( 'number', 'range' ) ):	// HTML5 elements
				$strOutput .= $this->getNumberField();
				break;
			case 'textarea':	// Additional attributes: rows, cols
				$strOutput .= $this->getTextAreaField();
				break;	
			case 'radio':
				$strOutput .= $this->getRadioField();
				break;
			case 'checkbox':	// Supports multiple creation with array of label				
				$strOutput .= $this->getCheckBoxField();
				break;
			case 'select':
				$strOutput .= $this->getSelectField();
				break;
			case 'hidden':	// Supports multiple creation with array of label
				$strOutput .= $this->getHiddenField();
				break;		
			case 'file':	// Supports multiple creation with array of label
				$strOutput .= $this->getFileField();
				break;
			case 'submit':	
				$strOutput .= $this->getSubmitField();
				break;
			case 'import':	// import options
				$strOutput .= $this->getImportField();
				break;	
			case 'export':	// export options
				$strOutput .= $this->getExportField();
				break;
			case 'image':	// image uploader
				$strOutput .= $this->getImageField();
				break;
			case 'color':	// color picker
				$strOutput .= $this->getColorField();
				break;			
			case 'date':	// date picker
				$strOutput .= $this->getDateField();
				break;
			case 'taxonomy':
				$strOutput .= $this->getTaxonomyChecklistField();
				break;			
			case 'posttype':
				$strOutput .= $this->getPostTypeChecklistField();
				break;
			case 'size':	// size selector
				$strOutput .= $this->getSizeField();
				break;				
			default:	// for anything else, 				
				$strOutput .= $this->arrField['vBeforeInputTag'] . ( ( string ) $this->vValue ) . $this->arrField['vAfterInputTag'];
				break;				
		}
		
		// Add the description
		$strOutput .= ( isset( $this->arrField['strDescription'] ) && trim( $this->arrField['strDescription'] ) != '' ) 
			? "<p class='admin-page-framework-fields-description'><span class='description'>{$this->arrField['strDescription']}</span></p>"
			: '';
			
		// Add the repeater script
		$strOutput .= $this->arrField['fRepeatable']
			? $this->getRepeaterScript( $this->strTagID, count( ( array ) $this->vValue ) )
			: '';
			
		return "<div class='admin-page-framework-fields'>"
				. $this->arrField['strBeforeField'] 
				. $strOutput
				. $this->arrField['strAfterField']
			. "</div>";
		
	}
	private function getTextField( $arrOutput=array() ) {

		$arrFields = $this->arrField['fRepeatable'] ? 
			( empty( $this->vValue ) ? array( '' ) : ( array ) $this->vValue )
			: $this->arrField['vLabel'];
			
		foreach( ( array ) $arrFields as $strKey => $strLabel ) 
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
					. ( $strLabel && ! $this->arrField['fRepeatable']
						? "<span class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
							. "<label for='{$this->strTagID}_{$strKey}' class='text-label'>{$strLabel}</label>"
						. "</span>" 
						: "" 
						)
					. "<input id='{$this->strTagID}_{$strKey}' "
						. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
						. "size='" . $this->getCorrespondingArrayValue( $this->arrField['vSize'], $strKey, 30 ) . "' "
						. "maxlength='" . $this->getCorrespondingArrayValue( $this->arrField['vMaxLength'], $strKey, self::$arrDefaultFieldValues['vMaxLength'] ) . "' "
						. "type='{$this->arrField['strType']}' "	// text, password, etc.
						. "name=" . ( is_array( $arrFields ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " )
						. "value='" . $this->getCorrespondingArrayValue( $this->vValue, $strKey, null ) . "' "
						. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
						. ( $this->getCorrespondingArrayValue( $this->arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
					. "/>"
					. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '<br />' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				)
			;
				
		return "<div class='admin-page-framework-field-text' id='{$this->strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";

	}
	private function getNumberField( $arrOutput=array() ) {
		
		$arrFields = $this->arrField['fRepeatable'] ? 
			( empty( $this->vValue ) ? array( '' ) : ( array ) $this->vValue )
			: $this->arrField['vLabel'];
			
		foreach( ( array ) $arrFields as $strKey => $strLabel ) 
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
					. ( $strLabel && ! $this->arrField['fRepeatable']
						? "<span class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
							. "<label for='{$this->strTagID}_{$strKey}' class='text-label'>{$strLabel}</label>"
						. "</span>" 
						: "" 
						)
					. "<input id='{$this->strTagID}_{$strKey}' "
						. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
						. "size='" . $this->getCorrespondingArrayValue( $this->arrField['vSize'], $strKey, 30 ) . "' "
						. "type='{$this->arrField['strType']}' "
						. "name=" . ( is_array( $arrFields ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " )
						. "value='" . $this->getCorrespondingArrayValue( $this->vValue, $strKey, null ) . "' "
						. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
						. ( $this->getCorrespondingArrayValue( $this->arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
						. "min='" . $this->getCorrespondingArrayValue( $this->arrField['vMin'], $strKey, self::$arrDefaultFieldValues['vMin'] ) . "' "
						. "max='" . $this->getCorrespondingArrayValue( $this->arrField['vMax'], $strKey, self::$arrDefaultFieldValues['vMax'] ) . "' "
						. "step='" . $this->getCorrespondingArrayValue( $this->arrField['vStep'], $strKey, self::$arrDefaultFieldValues['vStep'] ) . "' "
						. "maxlength='" . $this->getCorrespondingArrayValue( $this->arrField['vMaxLength'], $strKey, self::$arrDefaultFieldValues['vMaxLength'] ) . "' "
					. "/>"
					. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '<br />' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				);				
					
		return "<div class='admin-page-framework-field-number' id='{$this->strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";		
		
	}
	private function getTextAreaField( $arrOutput=array() ) {
		
		$arrFields = $this->arrField['fRepeatable'] ? 
			( empty( $this->vValue ) ? array( '' ) : ( array ) $this->vValue )
			: $this->arrField['vLabel'];		
		
		$fSingle = ! is_array( $arrFields );
		foreach( ( array ) $arrFields as $strKey => $strLabel ) {
			
			$arrRichEditorSettings = $fSingle
				? $this->arrField['vRich']
				: $this->getCorrespondingArrayValue( $this->arrField['vRich'], $strKey, null );
				
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
					. ( $strLabel && ! $this->arrField['fRepeatable']
						? "<span class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
							. "<label for='{$this->strTagID}_{$strKey}' class='text-label'>{$strLabel}</label>"
						. "</span>" 
						: "" 
						)
					. ( ! empty( $arrRichEditorSettings ) && version_compare( $GLOBALS['wp_version'], '3.3', '>=' ) && function_exists( 'wp_editor' )
						? wp_editor( 
							$this->getCorrespondingArrayValue( $this->vValue, $strKey, null ), 
							"{$this->strTagID}_{$strKey}",  
							$this->oUtil->uniteArrays( 
								( array ) $arrRichEditorSettings,
								array(
									'wpautop' => true, // use wpautop?
									'media_buttons' => true, // show insert/upload button(s)
									'textarea_name' => is_array( $arrFields ) ? "{$this->strFieldName}[{$strKey}]" : $this->strFieldName , // set the textarea name to something different, square brackets [] can be used here
									'textarea_rows' => $this->getCorrespondingArrayValue( $this->arrField['vRows'], $strKey, self::$arrDefaultFieldValues['vRows'] ),
									'tabindex' => '',
									'tabfocus_elements' => ':prev,:next', // the previous and next element ID to move the focus to when pressing the Tab key in TinyMCE
									'editor_css' => '', // intended for extra styles for both visual and Text editors buttons, needs to include the <style> tags, can use "scoped".
									'editor_class' => $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ), // add extra class(es) to the editor textarea
									'teeny' => false, // output the minimal editor config used in Press This
									'dfw' => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
									'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
									'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()													
								)
							)
						) . $this->getScriptForRichEditor( "{$this->strTagID}_{$strKey}" )
						: "<textarea id='{$this->strTagID}_{$strKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
							. "rows='" . $this->getCorrespondingArrayValue( $this->arrField['vRows'], $strKey, self::$arrDefaultFieldValues['vRows'] ) . "' "
							. "cols='" . $this->getCorrespondingArrayValue( $this->arrField['vCols'], $strKey, self::$arrDefaultFieldValues['vCols'] ) . "' "
							. "maxlength='" . $this->getCorrespondingArrayValue( $this->arrField['vMaxLength'], $strKey, self::$arrDefaultFieldValues['vMaxLength'] ) . "' "
							. "type='{$this->arrField['strType']}' "
							. "name=" . ( is_array( $arrFields ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " )
							. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
							. ( $this->getCorrespondingArrayValue( $this->arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
						. ">"
							. $this->getCorrespondingArrayValue( $this->vValue, $strKey, null )
						. "</textarea>"
					)
					. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '<br />' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				);
				
		}
		
		return "<div class='admin-page-framework-field-textarea' id='{$this->strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";		
		
	}
		/**
		 * A helper function for the above getTextAreaField() method.
		 * 
		 * This adds a script that forces the rich editor element to be inside the field table cell.
		 * 
		 * @since			2.1.2
		 */
		private function getScriptForRichEditor( $strIDSelector ) {

			// id: wp-sample_rich_textarea_0-wrap
			return "<script type='text/javascript'>
				jQuery( '#wp-{$strIDSelector}-wrap' ).hide();
				jQuery( document ).ready( function() {
					jQuery( '#wp-{$strIDSelector}-wrap' ).appendTo( '#field-{$strIDSelector}' );
					jQuery( '#wp-{$strIDSelector}-wrap' ).show();
				})
			</script>";		
			
		}
	
	private function getSelectField( $arrOutput=array() ) {

		// The value of the label key must be an array for the select type.
		if ( ! is_array( $this->arrField['vLabel'] ) ) return;	

		$fSingle = ( $this->getArrayDimension( ( array ) $this->arrField['vLabel'] ) == 1 );
		$arrLabels = $fSingle ? array( $this->arrField['vLabel'] ) : $this->arrField['vLabel'];
		foreach( $arrLabels as $strKey => $vLabel ) {
			
			$fMultiple = $this->getCorrespondingArrayValue( $this->arrField['vMultiple'], $strKey, self::$arrDefaultFieldValues['vMultiple'] );
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
					. "<span class='admin-page-framework-input-container admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
						. "<select id='{$this->strTagID}_{$strKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
							. "type='{$this->arrField['strType']}' "
							. ( $fMultiple ? "multiple='Multiple' " : '' )
							. "name=" . ( $fSingle ? "'{$this->strFieldName}" : "'{$this->strFieldName}[{$strKey}]" )
							. ( $fMultiple ? "[]' " : "' " )
							. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
							. "size=" . ( $this->getCorrespondingArrayValue( $this->arrField['vSize'], $strKey, 1 ) ) . " "
							. ( ( $strWidth = $this->getCorrespondingArrayValue( $this->arrField['vWidth'], $strKey, "" ) ) ? "style='width:{$strWidth};' " : "" )
						. ">"
							. $this->getOptionTags( $vLabel, $this->vValue, $strKey, $fSingle, $fMultiple )
						. "</select>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-select' id='{$this->strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";				
	
	}	
	
		/**
		 * A helper function for the getSelectField() and getSizeField() methods.
		 * 
		 * @since			2.0.0
		 * @since			2.0.1			Added the $vValue parameter to the second parameter. This is the result of supporting the size field type.
		 */ 
		private function getOptionTags( $arrLabels, $vValue, $strIterationID, $fSingle, $fMultiple=false ) {	

			$arrOutput = array();
			foreach ( $arrLabels as $strKey => $strLabel ) {
				$arrValue = $fSingle ? ( array ) $vValue : ( array ) $this->getCorrespondingArrayValue( $vValue, $strIterationID, array() ) ;
				$arrOutput[] = "<option "
						. "id='{$this->strTagID}_{$strIterationID}_{$strKey}' "
						. "value='{$strKey}' "
						. (	$fMultiple 
							? ( in_array( $strKey, $arrValue ) ? 'selected="Selected"' : '' )
							: ( $this->getCorrespondingArrayValue( $vValue, $strIterationID, null ) == $strKey ? "selected='Selected'" : "" )
						)
					. ">"
						. $strLabel
					. "</option>";
			}
			return implode( '', $arrOutput );
		}
	
	/**
	 * Returns the size input fields.
	 * 
	 * This enables for the user to set a size with a unit. This is made up of a text input field and a drop-down selector field. 
	 * Useful for theme developers.
	 * 
	 * @since			2.0.1
	 */ 
	private function getSizeField( $arrOutput=array() ) {
		
		$fSingle = ! is_array( $this->arrField['vLabel'] );
		$fIsSizeUnitForSingle = ( $this->getArrayDimension( ( array ) $this->arrField['vSizeUnits'] ) == 1 );
		$arrSizeUnits = isset( $this->arrField['vSizeUnits'] ) && is_array( $this->arrField['vSizeUnits'] ) && $fIsSizeUnitForSingle 
			? $this->arrField['vSizeUnits']
			: array(	// the default unit size array.
				'px'	=> 'px',	// pixel
				'%'		=> '%',		// percentage
				'em'	=> 'em',	// font size
				'ex'	=> 'ex',	// font height
				'in'	=> 'in',	// inch
				'cm'	=> 'cm',	// centimetre
				'mm'	=> 'mm',	// millimetre
				'pt'	=> 'pt',	// point
				'pc'	=> 'pc',	// pica
		);		
		
		foreach( ( array ) $this->arrField['vLabel'] as $strKey => $strLabel ) 
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
					. ( $strLabel 
						? "<span class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
								. "<label for='{$this->strTagID}_{$strKey}' class='text-label'>{$strLabel}</label>"
							."</span>" 
						: "" 
						)
					. "<input id='{$this->strTagID}_{$strKey}' "	// number field
						// . "style='text-align: right;'"
						. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
						. "size='" . $this->getCorrespondingArrayValue( $this->arrField['vSize'], $strKey, 10 ) . "' "
						. "maxlength='" . $this->getCorrespondingArrayValue( $this->arrField['vMaxLength'], $strKey, self::$arrDefaultFieldValues['vMaxLength'] ) . "' "
						. "type='number' "	// number
						. "name=" . ( $fSingle ? "'{$this->strFieldName}[size]' " : "'{$this->strFieldName}[{$strKey}][size]' " )
						. "value='" . ( $fSingle ? $this->getCorrespondingArrayValue( $this->vValue['size'], $strKey, '' ) : $this->getCorrespondingArrayValue( $this->getCorrespondingArrayValue( $this->vValue, $strKey, array() ), 'size', '' ) ) . "' "
						. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
						. ( $this->getCorrespondingArrayValue( $this->arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
						. "min='" . $this->getCorrespondingArrayValue( $this->arrField['vMin'], $strKey, self::$arrDefaultFieldValues['vMin'] ) . "' "
						. "max='" . $this->getCorrespondingArrayValue( $this->arrField['vMax'], $strKey, self::$arrDefaultFieldValues['vMax'] ) . "' "
						. "step='" . $this->getCorrespondingArrayValue( $this->arrField['vStep'], $strKey, self::$arrDefaultFieldValues['vStep'] ) . "' "					
					. "/>"
					. "<select id='{$this->strTagID}_{$strKey}' "	// select field
						. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
						. "type='{$this->arrField['strType']}' "
						. ( ( $fMultipleOptions = $this->getCorrespondingArrayValue( $this->arrField['vMultiple'], $strKey, self::$arrDefaultFieldValues['vMultiple'] ) ) ? "multiple='Multiple' " : '' )
						. "name=" . ( $fSingle ? "'{$this->strFieldName}[unit]" : "'{$this->strFieldName}[{$strKey}][unit]" ) . ( $fMultipleOptions ? "[]' " : "' " )						
						. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
						. "size=" . ( $this->getCorrespondingArrayValue( $this->arrField['vSize'], $strKey, 1 ) ) . " "
						. ( ( $strWidth = $this->getCorrespondingArrayValue( $this->arrField['vWidth'], $strKey, "" ) ) ? "style='width:{$strWidth};' " : "" )
					. ">"
						. $this->getOptionTags( 
							$fSingle ? $arrSizeUnits : $this->getCorrespondingArrayValue( $this->arrField['vSizeUnits'], $strKey, $arrSizeUnits ),
							$fSingle ? $this->getCorrespondingArrayValue( $this->vValue['unit'], $strKey, 'px' ) : $this->getCorrespondingArrayValue( $this->getCorrespondingArrayValue( $this->vValue, $strKey, array() ), 'unit', 'px' ),
							$strKey, 
							true, 	// since the above value is directly passed, pass call the function as for a single element.
							$fMultipleOptions 
						)
					. "</select>"
					. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '<br />' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				);			
				
		return "<div class='admin-page-framework-field-size' id='{$this->strTagID}'>" 
			. implode( '', $arrOutput ) 
		. "</div>";
		
	}
	
	private function getRadioField( $arrOutput=array() ) {
		
		// The value of the label key must be an array for the select type.
		if ( ! is_array( $this->arrField['vLabel'] ) ) return;	
		
		$fSingle = ( $this->getArrayDimension( ( array ) $this->arrField['vLabel'] ) == 1 );
		$arrLabels =  $fSingle ? array( $this->arrField['vLabel'] ) : $this->arrField['vLabel'];
		foreach( $arrLabels as $strKey => $vLabel )  
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. $this->getRadioTags( $vLabel, $strKey, $fSingle )				
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-radio' id='{$this->strTagID}'>" 
				. implode( '', $arrOutput )
			. "</div>";
		
	}
		/**
		 * A helper function for the <em>getRadioField()</em> method.
		 * @since			2.0.0
		 */ 
		private function getRadioTags( $arrLabels, $strIterationID, $fSingle ) {
			
			$arrOutput = array();
			foreach ( $arrLabels as $strKey => $strLabel ) 
				$arrOutput[] = "<span class='admin-page-framework-input-container'>"
						. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
						. "<input "
							. "id='{$this->strTagID}_{$strIterationID}_{$strKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
							. "type='radio' "
							. "value='{$strKey}' "
							. "name=" . ( ! $fSingle  ? "'{$this->strFieldName}[{$strIterationID}]' " : "'{$this->strFieldName}' " )
							. ( $this->getCorrespondingArrayValue( $this->vValue, $strIterationID, null ) == $strKey ? 'Checked ' : '' )
							. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
						. "/>"
						. "<span class='admin-page-framework-input-label-container admin-page-framework-radio-label' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
							. "<label for='{$this->strTagID}_{$strIterationID}_{$strKey}'>"
								. $strLabel
							. "</label>"
						. "</span>"
						. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
					. "</span>";

			return implode( '', $arrOutput );
		}

	private function getCheckBoxField( $arrOutput=array() ) {

		foreach( ( array ) $this->arrField['vLabel'] as $strKey => $strLabel ) 
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. "<input type='hidden' name=" .  ( is_array( $this->arrField['vLabel'] ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " ) . " value='0' />"	// the unchecked value must be set prior to the checkbox input field.
					. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
					. "<span class='admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
						. "<input "
							. "id='{$this->strTagID}_{$strKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
							. "type='{$this->arrField['strType']}' "	// checkbox
							. "name=" . ( is_array( $this->arrField['vLabel'] ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " )
							. "value='1' "
							. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
							. ( $this->getCorrespondingArrayValue( $this->vValue, $strKey, null ) == 1 ? "Checked " : '' )
						. "/>"
						. "<span class='admin-page-framework-input-label-container admin-page-framework-checkbox-label' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
							. "<label for='{$this->strTagID}_{$strKey}'>"				
								. $strLabel
							. "</label>"
						. "</span>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
				. "</div>" // end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-checkbox' id='{$this->strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";	
	
	}
	
	/**
	 * 
	 * @remark			The user needs to assign the value to the vDefault key in order to set the hidden field. 
	 * If it's not set ( null value ), the below foreach will not iterate an element so no input field will be embedded.
	 */
	private function getHiddenField( $arrOutput=array() ) {
					
		foreach( ( array ) $this->vValue as $strKey => $strValue ) 
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
					. "<span class='admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
						. ( ( $strLabel = $this->getCorrespondingArrayValue( $this->arrField['vLabel'], $strKey, '' ) ) ? "<label for='{$this->strTagID}_{$strKey}'>{$strLabel}</label>" : "" )
						. "<input "
							. "id='{$this->strTagID}_{$strKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
							. "type='{$this->arrField['strType']}' "	// hidden
							. "name=" . ( is_array( $this->arrField['vLabel'] ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " )
							. "value='" . $strValue  . "' "
							. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-hidden' id='{$this->strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";
		
	}
	
	private function getFileField( $arrOutput=array() ) {

		$arrFields = $this->arrField['fRepeatable'] ? 
			( empty( $this->vValue ) ? array( '' ) : ( array ) $this->vValue )
			: $this->arrField['vLabel'];	
	
		foreach( ( array ) $arrFields as $strKey => $strLabel ) 
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
					. ( $strLabel && ! $this->arrField['fRepeatable'] ?
						"<span class='admin-page-framework-input-container'  style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
							. "<label for='{$this->strTagID}_{$strKey}'>{$strLabel}</label>"
						. "</span>"
						: ""
					)
					. "<input "
						. "id='{$this->strTagID}_{$strKey}' "
						. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
						. "accept='" . $this->getCorrespondingArrayValue( $this->arrField['vAcceptAttribute'], $strKey, 'audio/*|video/*|image/*|MIME_type' ) . "' "
						. "type='{$this->arrField['strType']}' "	// file
						. "name=" . ( is_array( $arrFields ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " )
						. "value='" . $this->getCorrespondingArrayValue( $arrFields, $strKey, __( 'Submit', 'admin-page-framework' ) ) . "' "
						. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
					. "/>"
					. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-file' id='{$this->strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";			
	
	}
	private function getSubmitField( $arrOutput=array() ) {
		
		$this->vValue = $this->getInputFieldValueFromLabel( $this->arrField, $this->arrOptions );
		$this->strFieldNameFlat = $this->getInputFieldNameFlat();
		foreach( ( array ) $this->vValue as $strKey => $strValue ) {
			$strRedirectURL = $this->getCorrespondingArrayValue( $this->arrField['vRedirect'], $strKey, null );
			$strLinkURL = $this->getCorrespondingArrayValue( $this->arrField['vLink'], $strKey, null );
			$strResetKey = $this->getCorrespondingArrayValue( $this->arrField['vReset'], $strKey, null );
			$fResetConfirmed = $this->checkConfirmationDisplayed( $strResetKey, $this->strFieldNameFlat ); 
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. ( $strRedirectURL 
						? "<input type='hidden' "
							. "name='__redirect[{$this->strTagID}_{$strKey}][url]' "
							. "value='" . $strRedirectURL . "' "
						. "/>" 
						. "<input type='hidden' "
							. "name='__redirect[{$this->strTagID}_{$strKey}][name]' "
							. "value='{$this->strFieldNameFlat}" . ( is_array( $this->vValue ) ? "|{$strKey}" : "'" )
						. "/>" 
						: "" 
					)
					. ( $strLinkURL 
						? "<input type='hidden' "
							. "name='__link[{$this->strTagID}_{$strKey}][url]' "
							. "value='" . $strLinkURL . "' "
						. "/>"
						. "<input type='hidden' "
							. "name='__link[{$this->strTagID}_{$strKey}][name]' "
							. "value='{$this->strFieldNameFlat}" . ( is_array( $this->vValue ) ? "|{$strKey}'" : "'" )
						. "/>" 
						: "" 
					)
					. ( $strResetKey && ! $fResetConfirmed
						? "<input type='hidden' "
							. "name='__reset_confirm[{$this->strTagID}_{$strKey}][key]' "
							. "value='" . $this->strFieldNameFlat . "' "
						. "/>"
						. "<input type='hidden' "
							. "name='__reset_confirm[{$this->strTagID}_{$strKey}][name]' "
							. "value='{$this->strFieldNameFlat}" . ( is_array( $this->vValue ) ? "|{$strKey}'" : "'" )
						. "/>" 
						: ""
					)
					. ( $strResetKey && $fResetConfirmed
						? "<input type='hidden' "
							. "name='__reset[{$this->strTagID}_{$strKey}][key]' "
							. "value='" . $strResetKey . "' "
						. "/>"
						. "<input type='hidden' "
							. "name='__reset[{$this->strTagID}_{$strKey}][name]' "
							. "value='{$this->strFieldNameFlat}" . ( is_array( $this->vValue ) ? "|{$strKey}'" : "'" )
						. "/>" 
						: ""
					)
					. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
					. "<span class='admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
						. "<input "
							. "id='{$this->strTagID}_{$strKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, 'button button-primary' ) . "' "
							. "type='{$this->arrField['strType']}' "	// submit
							. "name=" . ( is_array( $this->arrField['vLabel'] ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " )
							. "value='" . $this->getCorrespondingArrayValue( $this->vValue, $strKey, $this->oMsg->___( 'submit' ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
				. "</div>" // end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-submit' id='{$this->strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";		
	
	}
		/**
		 * A helper function for the above getSubmitField() that checks if a reset confirmation message has been displayed or not when the vReset key is set.
		 * 
		 */
		private function checkConfirmationDisplayed( $strResetKey, $strFlatFieldName ) {
				
			if ( ! $strResetKey ) return false;
			
			$fResetConfirmed =  get_transient( md5( "reset_confirm_" . $strFlatFieldName ) ) !== false 
				? true
				: false;
			
			if ( $fResetConfirmed )
				delete_transient( md5( "reset_confirm_" . $strFlatFieldName ) );
				
			return $fResetConfirmed;
			
		}

	private function getImportField( $arrOutput=array() ) {
	
		$this->vValue = $this->getInputFieldValueFromLabel( $this->arrField, $this->arrOptions );
		foreach( ( array ) $this->vValue as $strKey => $strValue ) 
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. "<input type='hidden' "
						. "name='__import[{$this->arrField['strFieldID']}][import_option_key]" . ( is_array( $this->arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $this->arrField['vImportOptionKey'], $strKey, $this->arrField['strOptionKey'] )
					. "' />"
					. "<input type='hidden' "
						. "name='__import[{$this->arrField['strFieldID']}][format]" . ( is_array( $this->arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $this->arrField['vImportFormat'], $strKey, 'array' )	// array, text, or json.
					. "' />"			
					. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
					. "<span class='admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
						. "<input "		// upload button
							. "id='{$this->strTagID}_{$strKey}_file' "
							. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, 'import' ) . "' "
							. "accept='" . $this->getCorrespondingArrayValue( $this->arrField['vAcceptAttribute'], $strKey, 'audio/*|video/*|image/*|MIME_type' ) . "' "
							. "type='file' "	// upload filed. the file type will be stored in $_FILE
							. "name='__import[{$this->arrField['strFieldID']}]" . ( is_array( $this->arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
							. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )				
						. "/>"
						. "<input "		// import button
							. "id='{$this->strTagID}_{$strKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, 'import button button-primary' ) . "' "
							. "type='submit' "	// the import button is a custom submit button.
							. "name='__import[submit][{$this->arrField['strFieldID']}]" . ( is_array( $this->arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
							. "value='" . $this->getCorrespondingArrayValue( $this->vValue, $strKey, $this->oMsg->___( 'import_options' ), true ) . "' "
							. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				);		
					
		return "<div class='admin-page-framework-field-import' id='{$this->strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";
		
	}
	private function getExportField( $arrOutput=array() ) {
		
		$this->vValue = $this->getInputFieldValueFromLabel( $this->arrField, $this->arrOptions );
		
		// If vValue is not an array and the export data set, set the transient. ( it means single )
		if ( isset( $this->arrField['vExportData'] ) && ! is_array( $this->vValue ) )
			set_transient( md5( "{$this->arrField['strClassName']}_{$this->arrField['strFieldID']}" ), $this->arrField['vExportData'], 60*2 );	// 2 minutes.
		
		foreach( ( array ) $this->vValue as $strKey => $strValue ) {
			
			$strExportFormat = $this->getCorrespondingArrayValue( $this->arrField['vExportFormat'], $strKey, 'array' );
			
			// If it's one of the multiple export buttons and the export data is explictly set for the element, store it as transient in the option table.
			$fIsDataSet = false;
			if ( isset( $this->vValue[ $strKey ] ) && isset( $this->arrField['vExportData'][ $strKey ] ) ) {
				set_transient( md5( "{$this->arrField['strClassName']}_{$this->arrField['strFieldID']}_{$strKey}" ), $this->arrField['vExportData'][ $strKey ], 60*2 );	// 2 minutes.
				$fIsDataSet = true;
			}
			
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. "<input type='hidden' "
						. "name='__export[{$this->arrField['strFieldID']}][file_name]" . ( is_array( $this->arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $this->arrField['vExportFileName'], $strKey, $this->generateExportFileName( $this->arrField['strOptionKey'], $strExportFormat ) )
					. "' />"
					. "<input type='hidden' "
						. "name='__export[{$this->arrField['strFieldID']}][format]" . ( is_array( $this->arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
						. "value='" . $strExportFormat
					. "' />"				
					. "<input type='hidden' "
						. "name='__export[{$this->arrField['strFieldID']}][transient]" . ( is_array( $this->arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
						. "value='" . ( $fIsDataSet ? 1 : 0 )
					. "' />"				
					. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
					. "<span class='admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
						. "<input "
							. "id='{$this->strTagID}_{$strKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, 'button button-primary' ) . "' "
							. "type='submit' "	// the export button is a custom submit button.
							// . "name=" . ( is_array( $this->arrField['vLabel'] ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " )
							. "name='__export[submit][{$this->arrField['strFieldID']}]" . ( is_array( $this->arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
							. "value='" . $this->getCorrespondingArrayValue( $this->vValue, $strKey, $this->oMsg->___( 'export_options' ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
				. "</div>" // end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				);
									
		}
					
		return "<div class='admin-page-framework-field-export' id='{$this->strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";		
	
	}
	
		/**
		 * 
		 * @since			2.0.0
		 * @remark			Currently only array, text or json is supported.
		 */ 
		private function generateExportFileName( $strOptionKey, $strExportFormat='text' ) {
				
			switch ( trim( strtolower( $strExportFormat ) ) ) {
				case 'text':	// for plain text.
					$strExt = "txt";
					break;
				case 'json':	// for json.
					$strExt = "json";
					break;
				case 'array':	// for serialized PHP arrays.
				default:	// for anything else, 
					$strExt = "txt";
					break;
			}		
				
			return $strOptionKey . '_' . date("Ymd") . '.' . $strExt;
			
		}

	private function getDateField( $arrOutput=array() ) {
		
		$arrFields = $this->arrField['fRepeatable'] ? 
			( empty( $this->vValue ) ? array( '' ) : ( array ) $this->vValue )
			: $this->arrField['vLabel'];		
		
		foreach( ( array ) $arrFields as $strKey => $strLabel ) 
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
					. ( $strLabel && ! $this->arrField['fRepeatable']
						? "<span class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
							. "<label for='{$this->strTagID}_{$strKey}' class='text-label'>{$strLabel}</label>"
						. "</span>" 
						: "" 
					)
					. "<input id='{$this->strTagID}_{$strKey}' "
						. "class='datepicker " . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
						. "size='" . $this->getCorrespondingArrayValue( $this->arrField['vSize'], $strKey, 10 ) . "' "
						. "maxlength='" . $this->getCorrespondingArrayValue( $this->arrField['vMaxLength'], $strKey, self::$arrDefaultFieldValues['vMaxLength'] ) . "' "
						. "type='text' "	// text, password, etc.
						. "name=" . ( is_array( $arrFields ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " )
						. "value='" . $this->getCorrespondingArrayValue( $this->vValue, $strKey, null ) . "' "
						. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
						. ( $this->getCorrespondingArrayValue( $this->arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
					. "/>"
					. $this->getDatePickerEnablerScript( "{$this->strTagID}_{$strKey}", $this->getCorrespondingArrayValue( $this->arrField['vDateFormat'], $strKey, 'yy/mm/dd' ) )
					. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-date' id='{$this->strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";
		
	}
		/**
		 * A helper function for the above getDateField() method.
		 * 
		 */
		private function getDatePickerEnablerScript( $strID, $strDateFormat ) {

			return 
"<script type='text/javascript' class='date-picker-enabler-script' data-id='{$strID}' data-date_format='{$strDateFormat}'>
	jQuery( document ).ready( function() {
		jQuery( '#{$strID}' ).datepicker({
			dateFormat : '{$strDateFormat}'
		});
	})
</script>";	
			
		}
	
	private function getColorField( $arrOutput=array() ) {
	
		$arrFields = $this->arrField['fRepeatable'] ? 
			( empty( $this->vValue ) ? array( '' ) : ( array ) $this->vValue )
			: $this->arrField['vLabel'];		
	
		foreach( ( array ) $arrFields as $strKey => $strLabel ) 
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
					. ( $strLabel && ! $this->arrField['fRepeatable']
						? "<span class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
							. "<label for='{$this->strTagID}_{$strKey}' class='text-label'>{$strLabel}</label>"
						. "</span>" 
						: "" 
					)
					. "<input id='{$this->strTagID}_{$strKey}' "
						. "class='input_color " . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
						. "size='" . $this->getCorrespondingArrayValue( $this->arrField['vSize'], $strKey, 10 ) . "' "
						. "maxlength='" . $this->getCorrespondingArrayValue( $this->arrField['vMaxLength'], $strKey, self::$arrDefaultFieldValues['vMaxLength'] ) . "' "
						. "type='text' "	// text
						. "name=" . ( is_array( $arrFields ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " )
						. "value='" . ( $this->getCorrespondingArrayValue( $this->vValue, $strKey, 'transparent' ) ) . "' "
						. "color='" . ( $this->getCorrespondingArrayValue( $this->vValue, $strKey, 'transparent' ) ) . "' "
						. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
						. ( $this->getCorrespondingArrayValue( $this->arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
					. "/>"
					. "<div class='colorpicker' id='color_{$this->strTagID}_{$strKey}' rel='{$this->strTagID}_{$strKey}'></div>"	// this div element with this class selector becomes a farbtastic color picker. ( below 3.4.x )
					. $this->getColorPickerEnablerScript( "{$this->strTagID}_{$strKey}" )
					. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
				. "</div>"	// admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-color' id='{$this->strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";	
		
	}
		/**
		 * A helper function for the above getColorField() method to add a script to enable the color picker.
		 */
		private function getColorPickerEnablerScript( $strID ) {
			return
"<script type='text/javascript' class='color-picker-enabler-script' data-id='{$strID}'>
	jQuery( document ).ready( function(){
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
			jQuery( '#{$strID}' ).wpColorPicker( myColorPickerOptions );
		}
		else {
			// We use farbtastic if the WordPress color picker widget doesn't exist
			jQuery( '#color_{$strID}' ).farbtastic( '#{$strID}' );
		}
	});
</script>";
		}
		
	private function getImageField( $arrOutput=array() ) {
		
		$arrFields = $this->arrField['fRepeatable'] ? 
			( empty( $this->vValue ) ? array( '' ) : ( array ) $this->vValue )
			: $this->arrField['vLabel'];		
			
		foreach( ( array ) $arrFields as $strKey => $strLabel ) 
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
					. ( $strLabel && ! $this->arrField['fRepeatable']
						? "<span class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
							. "<label for='{$this->strTagID}_{$strKey}' class='text-label'>{$strLabel}</label>"
						. "</span>" 
						: "" 
					)
					. "<div class='admin-page-framework-input-container image-field'>"
						. "<input id='{$this->strTagID}_{$strKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
							. "size='" . $this->getCorrespondingArrayValue( $this->arrField['vSize'], $strKey, 60 ) . "' "
							. "maxlength='" . $this->getCorrespondingArrayValue( $this->arrField['vMaxLength'], $strKey, self::$arrDefaultFieldValues['vMaxLength'] ) . "' "
							. "type='text' "	// text
							. "name='" . ( $strName = is_array( $arrFields ) ? "{$this->strFieldName}[{$strKey}]" : $this->strFieldName ) . "' "
							. "value='" . ( $strImageURL = $this->getCorrespondingArrayValue( $this->vValue, $strKey, self::$arrDefaultFieldValues['vDefault'] ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
							. ( $this->getCorrespondingArrayValue( $this->arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
						. "/>"
						. $this->getImageUploaderButtonScript( "{$this->strTagID}_{$strKey}", $strName )	
						. ( $this->getCorrespondingArrayValue( $this->arrField['vImagePreview'], $strKey, true )
							? "<div id='image_preview_container_{$this->strTagID}_{$strKey}' "
									. "class='image_preview' "
									. "style='" . $this->getImagePreviewContainerStyle( $strImageURL  ) . "'"
								. ">"
									. "<img src='{$strImageURL}' "
										. 	"id='image_preview_{$this->strTagID}_{$strKey}' "
									. "/>"
								. "</div>"
							: "" )
					. "</div>"
					. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
				. "</div>"	// admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '<Br />' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-image' id='{$this->strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";		
		
	}	
		/**
		 * A helper function for the above getImageField() method to add a image button script.
		 */
		private function getImageUploaderButtonScript( $strID, $strName ) {
			
			$strSelectImage = __( 'Select Image', 'admin-page-framework' );
			$strButton ="<a id='select_image_{$strID}' "
						. "href='#' "
						. "class='select_image button button-small'"
						. "data-uploader_type='" . ( function_exists( 'wp_enqueue_media' ) ? 1 : 0 ) . "'"
					. ">"
						. $strSelectImage 
				."</a>";
			
			$strScript = "
				if ( jQuery( 'a#select_image_{$strID}' ).length == 0 ) {
					jQuery( 'input#{$strID}' ).after( \"{$strButton}\" );
				}			
			";
			
			if( function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.5 or above
				$strScript .="
				jQuery( document ).ready( function(){
					jQuery( '#select_image_{$strID}' ).click( function() {

						window.wpActiveEditor = null;
						var send_attachment_bkp = wp.media.editor.send.attachment;

						wp.media.editor.send.attachment = function( props, attachment ) {

							jQuery( '#{$strID}' ).val( attachment.url );
							jQuery( '#image_preview_{$strID}' ).attr( 'src', attachment.url );
							jQuery( '#image_preview_container_{$strID}' ).show();
							
							wp.media.editor.send.attachment = send_attachment_bkp;
							
						}
						wp.media.editor.open();
						return false;       
					});
				});";	
			return "<script type='text/javascript'>" . $strScript . "</script>";

		}
		/**
		 * A helper function for the above getImageField() method to return the inline-style of the image preview container element.
		 * 
		 * @since			2.1.3
		 */
		private function getImagePreviewContainerStyle( $strImageURL ) {
			
			$strInlineStyle = $strImageURL ? "" : "display : none;";
			return $strInlineStyle . "zoom:1;";
			
		}		
		
	/**
	 * Returns the output of post type checklist check boxes.
	 * 
	 * @remark			the posttype checklist field does not support multiple elements by passing an array of labels.
	 * @since			2.0.0
	 */ 
	private function getPostTypeChecklistField( $arrOutput=array() ) {
				
		foreach( ( array ) $this->getPostTypeArrayForChecklist( $this->arrField['arrRemove'] ) as $strKey => $strValue ) {
			$strName = "{$this->strFieldName}[{$strKey}]";
			$arrOutput[] = "<div class='{$this->strFieldClassSelector}' id='field-{$this->strTagID}_{$strKey}'>"
					. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 	
					. "<span class='admin-page-framework-input-container'>"
						. "<input type='hidden' name='{$strName}' value='0' />"
						. "<input "
							. "id='{$this->strTagID}_{$strKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
							. "type='checkbox' "
							. "name='{$strName}'"
							. "value='1' "
							. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
							. ( $this->getCorrespondingArrayValue( $this->vValue, $strKey, false ) == 1 ? "Checked " : '' )				
						. "/>"
					. "</span>"
					. "<span class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
						. "<label for='{$this->strTagID}_{$strKey}'>"				
							. $strValue
						. "</label>"
					. "</span>"				
					. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' )
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '' ) )
					? "<span class='delimiter' id='delimiter-{$this->strTagID}_{$strKey}'>" . $strDelimiter . "</span>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-posttype' id='{$this->strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";
		
	}	
	
		/**
		 * A helper function for the above getPosttypeChecklistField method.
		 * 
		 * @since			2.0.0
		 * @since			2.1.1			Changed the returning array to have the labels in its element values.
		 * @return			array			The array holding the elements of installed post types' labels and their slugs except the specified expluding post types.
		 */ 
		private function getPostTypeArrayForChecklist( $arrRemoveNames, $arrPostTypes=array() ) {
			
			foreach( get_post_types( '','objects' ) as $oPostType ) 
				if (  isset( $oPostType->name, $oPostType->label ) ) 
					$arrPostTypes[ $oPostType->name ] = $oPostType->label;

			return array_diff_key( $arrPostTypes, array_flip( $arrRemoveNames ) );	

		}		
	
	/**
	 * Returns the output of taxonomy checklist check boxes.
	 * 
	 * @remark			Multiple fields are not supported.
	 * @remark			Repeater fields are not supported.
	 * @since			2.0.0
	 * @since			2.1.1			The checklist boxes are rendered in a tabbed single box.
	 */
	private function getTaxonomyChecklistField( $arrOutput=array() ) {
	
		$arrTabs = array();
		$arrCheckboxes = array();
		foreach( ( array ) $this->arrField['vTaxonomySlug'] as $strKey => $strTaxonomySlug ) {
			$strActive = isset( $strActive ) ? '' : 'active';	// inserts the active class selector into the first element.
			$arrTabs[] = "<li class='tab-box-tab'><a href='#tab-{$strKey}'><span class='tab-box-tab-text'>" . $this->getCorrespondingArrayValue( empty( $this->arrField['vLabel'] ) ? null : $this->arrField['vLabel'], $strKey, $this->getLabelFromTaxonomySlug( $strTaxonomySlug ) ) . "</span></a></li>";
			$arrCheckboxes[] = "<div id='tab-{$strKey}' class='tab-box-content' style='height: {$this->arrField['strHeight']};'>"
					. "<ul class='list:category taxonomychecklist form-no-clear'>"
						. wp_list_categories( array(
							'walker' => new AdminPageFramework_WalkerTaxonomyChecklist,	// the walker class instance
							'name'     => is_array( $this->arrField['vTaxonomySlug'] ) ? "{$this->strFieldName}[{$strKey}]" : "{$this->strFieldName}",   // name of the input
							'selected' => $this->getSelectedKeyArray( $this->vValue, $strKey ), 		// checked items ( term IDs )	e.g.  array( 6, 10, 7, 15 ), 
							'title_li'	=> '',	// disable the Categories heading string 
							'hide_empty' => 0,	
							'echo'	=> false,	// returns the output
							'taxonomy' => $strTaxonomySlug,	// the taxonomy slug (id) such as category and post_tag 
						) )					
					. "</ul>"			
					. "<!--[if IE]><b>.</b><![endif]-->"
				. "</div>";
		}
		$strTabs = "<ul class='tab-box-tabs category-tabs'>" . implode( '', $arrTabs ) . "</ul>";
		$strContents = "<div class='tab-box-contents-container'>"
				. "<div class='tab-box-contents' style='height: {$this->arrField['strHeight']};'>"
					. implode( '', $arrCheckboxes )
				. "</div>"
			. "</div>";
			
		$strOutput = "<div id='{$this->strTagID}' class='{$this->strFieldClassSelector} admin-page-framework-field-taxonomy tab-box-container categorydiv' style='max-width:{$this->arrField['strWidth']};'>"
				. $strTabs . PHP_EOL
				. $strContents . PHP_EOL
			. "</div>";

		return $strOutput;

	}	
	
		/**
		 * A helper function for the above getTaxonomyChecklistField() method. 
		 * 
		 * @since			2.0.0
		 * @param			array			$vValue			This can be either an one-dimensional array ( for single field ) or a two-dimensional array ( for multiple fields ).
		 * @param			string			$strKey			
		 * @return			array			Returns an array consisting of keys whose value is true.
		 */ 
		private function getSelectedKeyArray( $vValue, $strKey ) {
					
			$vValue = ( array ) $vValue;	// cast array because the initial value (null) may not be an array.
			$intArrayDimension = $this->getArrayDimension( ( array ) $vValue );
					
			if ( $intArrayDimension == 1 )
				$arrKeys = $vValue;
			else if ( $intArrayDimension == 2 )
				$arrKeys = ( array ) $this->getCorrespondingArrayValue( $vValue, $strKey, false );
				
			return array_keys( $arrKeys, true );
		
		}
	
		/**
		 * A helper function for the above getTaxonomyChecklistField() method.
		 * 
		 * @since			2.1.1
		 * 
		 */
		private function getLabelFromTaxonomySlug( $strTaxonomySlug ) {
			
			$oTaxonomy = get_taxonomy( $strTaxonomySlug );
			return isset( $oTaxonomy->label )
				? $oTaxonomy->label
				: null;
			
		}

	
	
	/**
	 * Sets or return the flag that indicates whether the creating fields are for meta boxes or not.
	 * 
	 * If the parameter is not set, it will return the stored value. Otherwise, it will set the value.
	 * 
	 * @since			2.1.2
	 */
	public function isMetaBox( $fTrueOrFalse=null ) {
		
		if ( isset( $fTrueOrFalse ) ) 
			$this->fIsMetaBox = $fTrueOrFalse;
			
		return $this->fIsMetaBox;
		
	}
	
	/**
	 * Returns the repeatable fields script.
	 * 
	 * @since			2.1.3
	 */
	private function getRepeaterScript( $strTagID, $intFieldCount ) {

		$strAdd = __( 'Add', 'admin-page-framework' );
		$strRemove = __( 'Remove', 'admin-page-framework' );
		$strVisibility = $intFieldCount <= 1 ? " style='display:none;'" : "";
		$strButtons = "<div class='admin-page-framework-repeatable-field-buttons'>"
				. "<a class='repeatable-field-add button-secondary repeatable-field-button button button-small' href='#' title='{$strAdd}'>+</a>"
				. "<a class='repeatable-field-remove button-secondary repeatable-field-button button button-small' href='#' title='{$strRemove}' {$strVisibility}>-</a>"
			. "</div>";
	
		return 
		"<script type='text/javascript'>
			jQuery( document ).ready( function() {
			
				// Adds the buttons
				jQuery( '#{$strTagID} .admin-page-framework-field' ).append( \"{$strButtons}\" );
				
				// Add button behaviour
				jQuery( '#{$strTagID} .repeatable-field-add' ).click( function() {
				
					var field_container = jQuery( this ).closest( '.admin-page-framework-field' );
					var field_container_id = field_container.attr( 'id' );	
					var field_delimiter_id = field_container_id.replace( 'field-', 'delimiter-' );
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
				
						jQuery( this ).attr( 'id', function( index, name ) { return incrementID( index, name ) } );
						jQuery( this ).find( 'input,textarea' ).attr( 'id', function( index, name ){ return incrementID( index, name ) } );
						jQuery( this ).find( 'input,textarea' ).attr( 'name', function( index, name ){ return incrementName( index, name ) } );
						
						// Color Pickers - currently not functional
						var color_picker_script = jQuery( this ).find( 'script.color-picker-enabler-script' );
						if ( color_picker_script.length > 0 ) {
							
							var previous_id = color_picker_script.attr( 'data-id' );
							color_picker_script.attr( 'data-id', function( index, name ){ return incrementID( index, name ) } );
							var cloned_id = color_picker_script.attr( 'data-id' );
							jQuery( this ).find( '.colorpicker' ).attr( 'id', function( index, name ){ return incrementID( index, name ) } );
							jQuery( this ).find( '.colorpicker' ).attr( 'rel', function( index, name ){ return incrementID( index, name ) } );
							
							if( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function' ){
								
								// Remove the color picker script elements in the clone						
								// jQuery( 'input#' + cloned_id ).prependTo( '#field-' + cloned_id ).show();
								// jQuery( this ).find( '.wp-color-result' ).remove();
								// jQuery( this ).find( '.wp-picker-holder' ).remove();
								// jQuery( this ).find( '.wp-picker-container' ).remove();
								
								var myColorPickerOptions = {
									defaultColor: false,	// you can declare a default color here, or in the data-default-color attribute on the input
									change: function( event, ui ){},	// a callback to fire whenever the color changes to a valid color reference : http://automattic.github.io/Iris/
									clear: function() {},	// a callback to fire when the input is emptied or an invalid color
									hide: true,	// hide the color picker controls on load
									palettes: true // show a group of common colors beneath the square or, supply an array of colors to customize further
								};						
								
								// Reassign the color picker script to the input field.
								jQuery( '#' + color_picker_script.attr( 'data-id' ) ).wpColorPicker( myColorPickerOptions ); 					
								// jQuery( '#' + previous_id ).wpColorPicker( myColorPickerOptions ); 
								
							} else {
								//We use farbtastic if the WordPress color picker widget doesn't exist
								jQuery( '#color_' + color_picker_script.attr( 'data-id' ) ).farbtastic( '#' + color_picker_script.attr( 'data-id' ) );
								jQuery( '#color_' + previous_id ).farbtastic( '#' + previous_id );
							}
						
						}

						// Image Uploader Button
						image_uploader_button = jQuery( this ).find( '.select_image' );
						if ( image_uploader_button.length > 0 ) {
							var previous_id = jQuery( this ).find( '.image-field input' ).attr( 'id' );
							image_uploader_button.attr( 'id', function( index, name ){ return incrementID( index, name ) } );
							jQuery( this ).find( '.image_preview' ).attr( 'id', function( index, name ){ return incrementID( index, name ) } );
							jQuery( this ).find( '.image_preview img' ).attr( 'id', function( index, name ){ return incrementID( index, name ) } );
							
							if ( jQuery( image_uploader_button ).data( 'uploader_type' ) == '1' ) {	// for Wordpress 3.5 or above
								jQuery( '#select_image_' + previous_id ).click( function() {
									window.wpActiveEditor = null;
									var send_attachment_bkp = wp.media.editor.send.attachment;
									wp.media.editor.send.attachment = function( props, attachment ) {
										jQuery( '#' + previous_id ).val( attachment.url );
										jQuery( '#image_preview_' + previous_id ).attr( 'src', attachment.url );
										jQuery( '#image_preview_container_' + previous_id ).show();
										wp.media.editor.send.attachment = send_attachment_bkp;
									}
									wp.media.editor.open();
									return false;       
								});
							}						
						}
						
						// Date pickers - somehow it needs to destroy the both previous one and the added one and assign the new date pickers 
						var date_picker_script = jQuery( this ).find( 'script.date-picker-enabler-script' );
						if ( date_picker_script.length > 0 ) {
							var previous_id = date_picker_script.attr( 'data-id' );
							date_picker_script.attr( 'data-id', function( index, name ){ return incrementID( index, name ) } );

							jQuery( '#' + date_picker_script.attr( 'data-id' ) ).datepicker( 'destroy' ); 
							jQuery( '#' + date_picker_script.attr( 'data-id' ) ).datepicker({
								dateFormat : date_picker_script.attr( 'data-date_format' )
							});						
							jQuery( '#' + previous_id ).datepicker( 'destroy' ); //here
							jQuery( '#' + previous_id ).datepicker({
								dateFormat : date_picker_script.attr( 'data-date_format' )
							});												
						}				
						
					});

					var fieldsCount = jQuery('#{$strTagID} .repeatable-field-remove').length;
					if ( fieldsCount > 1 ) {
						jQuery('#{$strTagID} .repeatable-field-remove' ).show();
					}
					
					return false;
					
				});		
				
				// Remove button behaviour
				jQuery( '#{$strTagID} .repeatable-field-remove' ).click( function() {
					
					// Need to remove two elements: the field container and the delimiter element.
					var field_container = jQuery( this ).closest( '.admin-page-framework-field' );
					var field_container_id = field_container.attr( 'id' );				
					var field_delimiter_id = field_container_id.replace( 'field-', 'delimiter-' );
					var field_delimiter = field_container.siblings( '#' + field_delimiter_id );
					
					// Decrement the names and ids of the next following siblings.
					field_delimiter.nextAll().each( function() {
						
						jQuery( this ).attr( 'id', function( index, name ) { return decrementID( index, name ) } );
						jQuery( this ).find( 'input,textarea' ).attr( 'name', function( index, name ){ return decrementName( index, name ) } );
						
					});
					
					field_delimiter.remove();
					field_container.remove();
					
					var fieldsCount = jQuery( '#{$strTagID} .repeatable-field-remove' ).length;
					if ( fieldsCount == 1 ) {
						jQuery( '#{$strTagID} .repeatable-field-remove' ).css( 'display', 'none' );
					}
					return false;
				});
			
				// Helper Closure functions
				var incrementID = function( index, name ) {
					
					if ( typeof name === 'undefined' ) {
						return name;
					}
					return name.replace( /((\d+)$)/, function ( fullMatch, n ) {
						return Number(n) + 1;
					});
					
				}
				var incrementName = function( index, name ) {
					
					if ( typeof name === 'undefined' ) {
						return name;
					}
					return name.replace( /([(\d+)])/, function ( fullMatch, n ) {
						return Number(n) + 1;
					});
					
				}		
				var decrementID = function( index, name ) {
					
					if ( typeof name === 'undefined' ) {
						return name;
					}			
					return name.replace( /((\d+)$)/, function ( fullMatch, n ) {
						return Number(n) - 1;
					});    
					
				}
				var decrementName = function( index, name ) {
					
					if ( typeof name === 'undefined' ) {
						return name;
					}			
					return name.replace( /([(\d+)])/, function ( fullMatch, n ) {
						return Number(n) - 1;
					});      
					
				}
				
			})
			
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
 * @extends			Walker_Category
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AdminPageFramework_WalkerTaxonomyChecklist extends Walker_Category {
		
	function start_el( &$strOutput, $oCategory, $intDepth=0, $arrArgs=array(), $intCurrentObjectID=0 ) {
		
		/*	
		 	$arrArgs keys:
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
		
		$arrArgs = $arrArgs + array(
			'name' 		=> null,
			'disabled'	=> null,
			'selected'	=> array(),
		);
		
		$intID = $oCategory->term_id;
		$strTaxonomy = empty( $arrArgs['taxonomy'] ) ? 'category' : $arrArgs['taxonomy'];
		$strChecked = in_array( $intID, ( array ) $arrArgs['selected'] )  ? 'Checked' : '';
		$strDisabled = $arrArgs['disabled'] ? 'disabled="Disabled"' : '';
		$strClass = 'category-list';
		$strID = "{$strTaxonomy}-{$intID}";
		$strOutput .= "\n"
			. "<li id='list-{$strID}' $strClass>" 
			. "<input value='0' type='hidden' name='{$arrArgs['name']}[{$intID}]' />"
			. "<input id='{$strID}' value='1' type='checkbox' name='{$arrArgs['name']}[{$intID}]' {$strChecked} {$strDisabled} />"
			. "<label for='{$strID}' class='taxonomy-checklist-label'>"
				. esc_html( apply_filters( 'the_category', $oCategory->name ) ) 
			. "</label>";	// no need to close </li> since it is done in end_el().
			
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
	* 		'show_admin_column' => true,
	* 	)		
	* );</code>
	* @since			2.0.0
	* @see				http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
	* @param			string			$strPostType			The post type slug.
	* @param			array			$arrArgs				The <a href="http://codex.wordpress.org/Function_Reference/register_post_type#Arguments">argument array</a> passed to register_post_type().
	* @param			string			$strCallerPath			The path of the caller script. This is used to retrieve the script information to insert it into the footer. If not set, the framework tries to detect it.
	* @return			void
	*/
	public function __construct( $strPostType, $arrArgs=array(), $strCallerPath=null ) {
		
		// Objects
		$this->oUtil = new AdminPageFramework_Utilities;
		$this->oProps = new AdminPageFramework_PostType_Properties;
		
		// Properties
		$this->oProps->strPostType = $this->oUtil->sanitizeSlug( $strPostType );
		$this->oProps->arrPostTypeArgs = $arrArgs;	// for the argument array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
		$this->oProps->strClassName = get_class( $this );
		$this->oProps->strClassHash = md5( $this->oProps->strClassName );
		$this->oProps->arrColumnHeaders = array(
			'cb'			=> '<input type="checkbox" />',	// Checkbox for bulk actions. 
			'title'			=> __( 'Title', 'admin-page-framework' ),		// Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
			'author'		=> __( 'Author', 'admin-page-framework' ),		// Post author.
			// 'categories'	=> __( 'Categories', 'admin-page-framework' ),	// Categories the post belongs to. 
			// 'tags'		=> __( 'Tags', 'admin-page-framework' ),	// Tags for the post. 
			'comments' 		=> '<div class="comment-grey-bubble"></div>', // Number of pending comments. 
			'date'			=> __( 'Date', 'admin-page-framework' ), 	// The date and publish status of the post. 
		);			
		$this->oProps->strCallerPath = $strCallerPath;
		
		add_action( 'init', array( $this, 'registerPostType' ), 999 );	// this is loaded in the front-end as well so should not be admin_init. Also "if ( is_admin() )" should not be used either.
		add_action( 'admin_enqueue_scripts', array( $this, 'disableAutoSave' ) );
		
		if ( $this->oProps->strPostType != '' && is_admin() ) {			
		
			// For table columns
			add_filter( "manage_{$this->oProps->strPostType}_posts_columns", array( $this, 'setColumnHeader' ) );
			add_filter( "manage_edit-{$this->oProps->strPostType}_sortable_columns", array( $this, 'setSortableColumns' ) );
			add_action( "manage_{$this->oProps->strPostType}_posts_custom_column", array( $this, 'setColumnCell' ), 10, 2 );
			
			// For filters
			add_action( 'restrict_manage_posts', array( $this, 'addAuthorTableFilter' ) );
			add_action( 'restrict_manage_posts', array( $this, 'addTaxonomyTableFilter' ) );
			add_filter( 'parse_query', array( $this, 'setTableFilterQuery' ) );
			
			// Style
			add_action( 'admin_head', array( $this, 'addStyle' ) );
			
			// Links
			$this->oLink = new AdminPageFramework_LinkForPostType( $this->oProps->strPostType, $this->oProps->strCallerPath );
			
			add_action( 'wp_loaded', array( $this, 'setUp' ) );
		}
	
		$this->oUtil->addAndDoAction( $this, "{$this->oProps->strPrefix_Start}{$this->oProps->strClassName}" );
		
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
	public function setColumnHeader( $arrColumnHeaders ) {
		return $this->oProps->arrColumnHeaders;
	}	
	
	/**
	 * Defines the sortable column items in the custom post listing table.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>manage_edit-{post type}_sortable_columns</em> hook.
	 * @remark			The user may override this method in their class definition.
	 */ 
	public function setSortableColumns( $arrColumns ) {
		return $this->oProps->arrColumnSortable;
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
	* @param			boolean			$fEnableAutoSave			If true, it enables the auto-save; othwerwise, it disables it.
	* return			void
	*/ 
	protected function setAutoSave( $fEnableAutoSave=True ) {
		$this->oProps->fEnableAutoSave = $fEnableAutoSave;		
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
	* @param			string			$strTaxonomySlug			The taxonomy slug.
	* @param			array			$arrArgs					The taxonomy argument array passed to the second parameter of the <a href="http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments">register_taxonomy()</a> function.
	* @return			void
	*/ 
	protected function addTaxonomy( $strTaxonomySlug, $arrArgs ) {
		
		$strTaxonomySlug = $this->oUtil->sanitizeSlug( $strTaxonomySlug );
		$this->oProps->arrTaxonomies[ $strTaxonomySlug ] = $arrArgs;	
		if ( isset( $arrArgs['show_table_filter'] ) && $arrArgs['show_table_filter'] )
			$this->oProps->arrTaxonomyTableFilters[] = $strTaxonomySlug;
		if ( isset( $arrArgs['show_in_sidebar_menus'] ) && ! $arrArgs['show_in_sidebar_menus'] )
			$this->oProps->arrTaxonomyRemoveSubmenuPages[ "edit-tags.php?taxonomy={$strTaxonomySlug}&amp;post_type={$this->oProps->strPostType}" ] = "edit.php?post_type={$this->oProps->strPostType}";
				
		if ( count( $this->oProps->arrTaxonomyTableFilters ) == 1 )
			add_action( 'init', array( $this, 'registerTaxonomies' ) );	// the hook should not be admin_init because taxonomies need to be accessed in regular pages.
		if ( count( $this->oProps->arrTaxonomyRemoveSubmenuPages ) == 1 )
			add_action( 'admin_menu', array( $this, 'removeTexonomySubmenuPages' ), 999 );		
			
	}	

	/**
	* Sets whether the author dropdown filter is enabled/disabled in the post type post list table.
	* 
	* <h4>Example</h4>
	* <code>this->setAuthorTableFilter( true );</code>
	* 
	* @since			2.0.0
	* @param			boolean			$fEnableAuthorTableFileter			If true, it enables the author filter; otherwise, it disables it.
	* @return			void
	*/ 
	protected function setAuthorTableFilter( $fEnableAuthorTableFileter=false ) {
		$this->oProps->fEnableAuthorTableFileter = $fEnableAuthorTableFileter;
	}
	
	/**
	 * Sets the post type arguments.
	 * 
	 * This is only necessary if it is not set to the constructor.
	 * 
	 * @since			2.0.0
	 * @see				http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
	 * @param			array			$arrArgs			The <a href="http://codex.wordpress.org/Function_Reference/register_post_type#Arguments">array of arguments</a> to be passed to the second parameter of the <em>register_post_type()</em> function.
	 * @return			void
	 */ 
	protected function setPostTypeArgs( $arrArgs ) {
		$this->oProps->arrPostTypeArgs = $arrArgs;
	}
	
	/**
	 * Sets the given HTML text into the footer on the left hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoLeft( '&lt;br /&gt;Custom Text on the left hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @param			string			$strHTML			The HTML code to insert.
	 * @param			boolean			$fAppend			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */	
	protected function setFooterInfoLeft( $strHTML, $fAppend=true ) {
		if ( isset( $this->oLink ) )	// check if the object is set to ensure it won't trigger a warning message in non-admin pages.
			$this->oLink->arrFooterInfo['strLeft'] = $fAppend 
				? $this->oLink->arrFooterInfo['strLeft'] . $strHTML
				: $strHTML;
	}
	
	/**
	 * Sets the given HTML text into the footer on the right hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoRight( '&lt;br /&gt;Custom Text on the right hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @param			string			$strHTML			The HTML code to insert.
	 * @param			boolean			$fAppend			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */		
	protected function setFooterInfoRight( $strHTML, $fAppend=true ) {
		if ( isset( $this->oLink ) )	// check if the object is set to ensure it won't trigger a warning message in non-admin pages.	
			$this->oLink->arrFooterInfo['strRight'] = $fAppend 
				? $this->oLink->arrFooterInfo['strRight'] . $strHTML
				: $strHTML;
	}

	/*
	 * Callback functions
	 */
	public function addStyle() {

		if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] != $this->oProps->strPostType )
			return;

		$this->oProps->strStyle = $this->oUtil->addAndApplyFilters( $this, "style_{$this->oProps->strClassName}", $this->oProps->strStyle );	
			
		// Print out the filtered styles.
		if ( ! empty( $this->oProps->strStyle ) )
			echo "<style type='text/css' id='admin-page-framework-style-post-type'>" 
				. $this->oProps->strStyle
				. "</style>";			
		
	}
	
	public function registerPostType() {

		register_post_type( $this->oProps->strPostType, $this->oProps->arrPostTypeArgs );
		
		$bIsPostTypeSet = get_option( "post_type_rules_flased_{$this->oProps->strPostType}" );
		if ( $bIsPostTypeSet !== true ) {
		   flush_rewrite_rules( false );
		   update_option( "post_type_rules_flased_{$this->oProps->strPostType}", true );
		}

	}	

	public function registerTaxonomies() {
		
		foreach( $this->oProps->arrTaxonomies as $strTaxonomySlug => $arrArgs ) 
			register_taxonomy(
				$strTaxonomySlug,
				$this->oProps->strPostType,
				$arrArgs	// for the argument array keys, refer to: http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
			);	
			
	}
	
	public function removeTexonomySubmenuPages() {
		
		foreach( $this->oProps->arrTaxonomyRemoveSubmenuPages as $strSubmenuPageSlug => $strTopLevelPageSlug )
			remove_submenu_page( $strTopLevelPageSlug, $strSubmenuPageSlug );
		
	}
	
	public function disableAutoSave() {
		
		if ( $this->oProps->fEnableAutoSave ) return;
		if ( $this->oProps->strPostType != get_post_type() ) return;
		wp_dequeue_script( 'autosave' );
			
	}
	
	/**
	 * Adds a dorpdown list to filter posts by author, placed above the post type listing table.
	 */ 
	public function addAuthorTableFilter() {
		
		if ( ! $this->oProps->fEnableAuthorTableFileter ) return;
		
		if ( ! ( isset( $_GET['post_type'] ) && post_type_exists( $_GET['post_type'] ) 
			&& in_array( strtolower( $_GET['post_type'] ), array( $this->oProps->strPostType ) ) ) )
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
		
		if ( $GLOBALS['typenow'] != $this->oProps->strPostType ) return;
		
		// If there is no post added to the post type, do nothing.
		$oPostCount = wp_count_posts( $this->oProps->strPostType );
		if ( $oPostCount->publish + $oPostCount->future + $oPostCount->draft + $oPostCount->pending + $oPostCount->private + $oPostCount->trash == 0 )
			return;
		
		foreach ( get_object_taxonomies( $GLOBALS['typenow'] ) as $strTaxonomySulg ) {
			
			if ( ! in_array( $strTaxonomySulg, $this->oProps->arrTaxonomyTableFilters ) ) continue;
			
			$oTaxonomy = get_taxonomy( $strTaxonomySulg );
 
			// If there is no added term, skip.
			if ( wp_count_terms( $oTaxonomy->name ) == 0 ) continue; 			

			// This function will echo the drop down list based on the passed array argument.
			wp_dropdown_categories( array(
				'show_option_all' => __( 'Show All', 'admin-page-framework' ) . ' ' . $oTaxonomy->label,
				'taxonomy' 	  => $strTaxonomySulg,
				'name' 		  => $oTaxonomy->name,
				'orderby' 	  => 'name',
				'selected' 	  => intval( isset( $_GET[ $strTaxonomySulg ] ) ),
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
		
		foreach ( get_object_taxonomies( $GLOBALS['typenow'] ) as $strTaxonomySlug ) {
			
			if ( ! in_array( $strTaxonomySlug, $this->oProps->arrTaxonomyTableFilters ) ) continue;
			
			$strVar = &$oQuery->query_vars[ $strTaxonomySlug ];
			if ( ! isset( $strVar ) ) continue;
			
			$oTerm = get_term_by( 'id', $strVar, $strTaxonomySlug );
			if ( is_object( $oTerm ) )
				$strVar = $oTerm->slug;

		}
		return $oQuery;
		
	}
	
	public function setColumnCell( $strColumnTitle, $intPostID ) { 
	
		// foreach ( $this->oProps->arrColumnHeaders as $strColumnHeader => $strColumnHeaderTranslated ) 
			// if ( $strColumnHeader == $strColumnTitle ) 
			
		// cell_{post type}_{custom column key}
		echo $this->oUtil->addAndApplyFilter( $this, "{$this->oProps->strPrefix_Cell}{$this->oProps->strPostType}_{$strColumnTitle}", $strCell='', $intPostID );
				  
	}
	
	/*
	 * Magic method - this prevents PHP's not-a-valid-callback errors.
	*/
	public function __call( $strMethodName, $arrArgs=null ) {	
		if ( substr( $strMethodName, 0, strlen( $this->oProps->strPrefix_Cell ) ) == $this->oProps->strPrefix_Cell ) return $arrArgs[0];
		if ( substr( $strMethodName, 0, strlen( "style_" ) )== "style_" ) return $arrArgs[0];
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
 * @use				AdminPageFramework_Utilities
 * @use				AdminPageFramework_Messages
 * @use				AdminPageFramework_Debug
 * @use				AdminPageFramework_Properties
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Meta Box
 */
abstract class AdminPageFramework_MetaBox extends AdminPageFramework_MetaBox_Help {
	
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
	 * Constructs the class object instance of AdminPageFramework_MetaBox.
	 * 
	 * @see				http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
	 * @since			2.0.0
	 * @param			string			$strMetaBoxID			The meta box ID.
	 * @param			string			$strTitle				The meta box title.
	 * @param			string|array	$vPostTypes				( optional ) The post type(s) that the meta box is associated with.
	 * @param			string			$strContext				( optional ) The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side') Default: normal.
	 * @param			string			$strPriority			( optional ) The priority within the context where the boxes should show ('high', 'core', 'default' or 'low') Default: default.
	 * @param			string			$strCapability			( optional ) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the meta box. Default: edit_posts.
	 * @param			string			$strTextDomain			( optional ) The text domain applied to the displayed text messages. Default: admin-page-framework.
	 * @return			void
	 */ 
	function __construct( $strMetaBoxID, $strTitle, $vPostTypes=array( 'post' ), $strContext='normal', $strPriority='default', $strCapability='edit_posts', $strTextDomain='admin-page-framework' ) {
		
		// Objects
		$this->oUtil = new AdminPageFramework_Utilities;
		$this->oMsg = new AdminPageFramework_Messages( $strTextDomain );
		$this->oDebug = new AdminPageFramework_Debug;
		$this->oProps = new AdminPageFramework_MetaBox_Properties;
		
		// Properties
		$this->oProps->strMetaBoxID = $this->oUtil->sanitizeSlug( $strMetaBoxID );
		$this->oProps->strTitle = $strTitle;
		$this->oProps->arrPostTypes = is_string( $vPostTypes ) ? array( $vPostTypes ) : $vPostTypes;	
		$this->oProps->strContext = $strContext;	//  'normal', 'advanced', or 'side' 
		$this->oProps->strPriority = $strPriority;	// 	'high', 'core', 'default' or 'low'
		$this->oProps->strClassName = get_class( $this );
		$this->oProps->strClassHash = md5( $this->oProps->strClassName );
		$this->oProps->strCapability = $strCapability;
				
		if ( is_admin() ) {
			
			add_action( 'wp_loaded', array( $this, 'setUp' ) );
			
			add_action( 'add_meta_boxes', array( $this, 'addMetaBox' ) );
			add_action( 'save_post', array( $this, 'saveMetaBoxFields' ) );
			
			// If it's not post (post edit) page nor the post type page, do not add scripts for media uploader.
			if ( 
				in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) 
				&& ( 
					( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->oProps->arrPostTypes ) )
					|| ( isset( $_GET['post'], $_GET['action'] ) && in_array( get_post_type( $_GET['post'] ), $this->oProps->arrPostTypes ) )		// edit post page
				)				
			) {	
				add_action( 'admin_head', array( $this, 'addScript' ) );
				add_action( 'admin_head', array( $this, 'addStyle' ) );
			}
			if ( in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php', ) ) ) 
				add_filter( 'gettext', array( $this, 'replaceThickBoxText' ) , 1, 2 );		
				
			// the contextual help pane
			add_action( "load-{$GLOBALS['pagenow']}", array( $this, 'registerHelpTabTextForMetaBox' ), 20 );	
			
		}
		
		// Hooks
		$this->oUtil->addAndDoAction( $this, "{$this->oProps->strPrefixStart}{$this->oProps->strClassName}" );
		
	}
	
	
	/**
	* The method for all necessary set-ups.
	* 
	* <h4>Example</h4>
	* <code>	public function setUp() {		
	* 	$this->addSettingFields(
	* 		array(
	* 			'strFieldID'		=> 'sample_metabox_text_field',
	* 			'strTitle'			=> 'Text Input',
	* 			'strDescription'	=> 'The description for the field.',
	* 			'strType'			=> 'text',
	* 		),
	* 		array(
	* 			'strFieldID'		=> 'sample_metabox_textarea_field',
	* 			'strTitle'			=> 'Textarea',
	* 			'strDescription'	=> 'The description for the field.',
	* 			'strType'			=> 'textarea',
	* 			'vDefault'			=> 'This is a default text.',
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
    *         'strFieldID'        => 'sample_metabox_text_field',
    *         'strTitle'          => 'Text Input',
    *         'strDescription'    => 'The description for the field.',
    *         'strType'           => 'text',
    *     ),
    *     array(
    *         'strFieldID'        => 'sample_metabox_textarea_field',
    *         'strTitle'          => 'Textarea',
    *         'strDescription'    => 'The description for the field.',
    *         'strType'           => 'textarea',
    *         'vDefault'          => 'This is a default text.',
    *     )
    * );</code>
	* 
	* @since			2.0.0
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			array			$arrField1			The field array.
	* @param			array			$arrField2			Another field array.
	* @param			array			$_and_more			Add more fields arrays as many as necessary to the next parameters.
	* @return			void
	*/ 
	protected function addSettingFields( $arrField1, $arrField2=null, $_and_more=null ) {

		foreach( func_get_args() as $arrField ) 
			$this->addSettingField( $arrField );
		
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
	protected function addSettingField( $arrField ) {

		if ( ! is_array( $arrField ) ) return;
		
		$arrField = $arrField + AdminPageFramework_MetaBox_Properties::$arrStructure_Field;	// avoid undefined index warnings.
		
		// Sanitize the IDs since they are used as a callback method name.
		$arrField['strFieldID'] = $this->oUtil->sanitizeSlug( $arrField['strFieldID'] );
		
		// Check the mandatory keys' values are set.
		if ( ! isset( $arrField['strFieldID'], $arrField['strType'] ) ) return;	// these keys are necessary.
						
		// If a custom condition is set and it's not true, skip.
		if ( ! $arrField['fIf'] ) return;
							
		// If it's the image, color, or date type field, extra jQuery scripts need to be loaded.
		if ( 
			in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) 
			&& ( 
				( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->oProps->arrPostTypes ) )
				|| ( isset( $_GET['post'], $_GET['action'] ) && in_array( get_post_type( $_GET['post'] ), $this->oProps->arrPostTypes ) )		// edit post page
			)
		) {
			if ( $arrField['strType'] == 'image' ) { 
				$this->enqueueMediaUploaderScript( $arrField );
				$this->addImageFieldScript( $arrField );
			}
			if ( $arrField['strType'] == 'taxonomy' ) $this->addTaxonomyChecklistScript( $arrField );
			if ( $arrField['strType'] == 'color' ) $this->addColorFieldScript( $arrField );
			if ( $arrField['strType'] == 'date' ) $this->addDateFieldScript( $arrField );
		}
		
		// For the contextual help pane,
		if ( 
			in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) 
			&& ( 
				( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->oProps->arrPostTypes ) )
				|| ( isset( $_GET['post'], $_GET['action'] ) && in_array( get_post_type( $_GET['post'] ), $this->oProps->arrPostTypes ) )		// edit post page
			)
			&& $arrField['strHelp']
		) {
			
			$this->addHelpTextForFormFields( $arrField['strTitle'], $arrField['strHelp'], $arrField['strHelpAside'] );
							
		}
	
		$this->oProps->arrFields[ $arrField['strFieldID'] ] = $arrField;
	
	}
	
	/*
	 * Back end methods - public callbacks and private methods.
	 * */
	private function addDateFieldScript( &$arrField ) {
		
		// This class may be instantiated multiple times so use a global flag.
		$strRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$strRootClassName}_DateScriptEnqueued" ] ) && $GLOBALS[ "{$strRootClassName}_DateScriptEnqueued" ] ) return;
		$GLOBALS[ "{$strRootClassName}_DateScriptEnqueued" ] = true;		
		
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueDateFieldScript' ) );
		
	}
	public function enqueueDateFieldScript() {

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );

	}

	/**
	 * Adds the script for taxonomy checklist tabbed boxes to the property.
	 * 
	 * @since			2.1.1
	 */
	private function addTaxonomyChecklistScript( &$arrField ) {
	
		// This class may be instantiated multiple times so use a global flag.
		$strRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$strRootClassName}_TaxonomyChecklistScriptAdded" ] ) && $GLOBALS[ "{$strRootClassName}_TaxonomyChecklistScriptAdded" ] ) return;
		$GLOBALS[ "{$strRootClassName}_TaxonomyChecklistScriptAdded" ] = true;

		// Append the script
		$this->oProps->strScript .= AdminPageFramework_Properties::getTaxonomyChecklistScript();
		
	}
	
	/**
	 * Adds the script for the color picker to the property and enqueues the WordPress built-in script for the color picker.
	 * @since			2.0.0
	 */
	private function addColorFieldScript( &$arrField ) {
	
		// This class may be instantiated multiple times so use a global flag.
		$strRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$strRootClassName}_ColorScriptEnqueued" ] ) && $GLOBALS[ "{$strRootClassName}_ColorScriptEnqueued" ] ) return;
		$GLOBALS[ "{$strRootClassName}_ColorScriptEnqueued" ] = true;

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueColorFieldScript' ) );
	
		// Append the script
		// Set up the color pickers to work with our text input field
		// $this->oProps->strScript .= AdminPageFramework_Properties::getColorPickerScript();	// deprecated
	
	}
	
	/**
	 * Enqueues the color picker script.
	 * @since			2.0.0
	 * @see			http://www.sitepoint.com/upgrading-to-the-new-wordpress-color-picker/
	 */ 
	public function enqueueColorFieldScript() {
		
		// If the WordPress version is greater than or equal to 3.5, then load the new WordPress color picker.
		if ( 3.5 <= $GLOBALS['wp_version'] ){
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
	private function enqueueMediaUploaderScript() {
		
		// This class may be instantiated multiple times so use a global flag.
		$strRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$strRootClassName}_MediaUploaderScriptEnqueued" ] ) && $GLOBALS[ "{$strRootClassName}_MediaUploaderScriptEnqueued" ] ) return;
		$GLOBALS[ "{$strRootClassName}_MediaUploaderScriptEnqueued" ] = true;
	
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueUploaderScripts' ) );	// called later than the admin_menu hook
		add_filter( 'gettext', array( $this, 'replaceThickBoxText' ) , 1, 2 );	
	
		
	}
	private function addImageFieldScript( &$arrField ) {
							
		// This class may be instantiated multiple times so use a global flag.
		$strRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$strRootClassName}_ImageScriptEnqueued" ] ) && $GLOBALS[ "{$strRootClassName}_ImageScriptEnqueued" ] ) return;
		$GLOBALS[ "{$strRootClassName}_ImageScriptEnqueued" ] = true;
					
		// These two hooks should be enabled when the image field type is added in the field array.
		$this->oProps->strThickBoxTitle = isset( $arrField['strTickBoxTitle'] ) ? $arrField['strTickBoxTitle'] : __( 'Upload Image', 'admin-page-framework' );
		$this->oProps->strThickBoxButtonUseThis = isset( $arrField['strLabelUseThis'] ) ? $arrField['strLabelUseThis'] : __( 'Use This Image', 'admin-page-framework' ); 			
					
		// Append the script
		if( ! function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.5 or above		
			$this->oProps->strScript .= AdminPageFramework_Properties::getImageSelectorScript( "admin_page_framework", $this->oProps->strThickBoxTitle, $this->oProps->strThickBoxButtonUseThis );
		
	}

	/**
	 * Appends the CSS rules of the framework in the head tag. 
	 * @since			2.0.0
	 * @remark			A callback for the <em>admin_head</em> hook.
	 */ 	
	public function addStyle() {
	
		// This class may be instantiated multiple times so use a global flag.
		$strRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$strRootClassName}_StyleLoaded" ] ) && $GLOBALS[ "{$strRootClassName}_StyleLoaded" ] ) return;
		$GLOBALS[ "{$strRootClassName}_StyleLoaded" ] = true;
				
		// Print out the filtered styles.
		$this->oProps->strStyle = $this->oUtil->addAndApplyFilters( $this, "style_{$this->oProps->strClassName}", AdminPageFramework_Properties::$strDefaultStyle );
		$this->oProps->strStyleIE = $this->oUtil->addAndApplyFilters( $this, "style_ie_{$this->oProps->strClassName}", AdminPageFramework_Properties::$strDefaultStyleIE );
		if ( ! empty( $this->oProps->strStyle ) )
			echo "<style type='text/css' id='admin-page-framework-style-meta-box'>" 
				. $this->oProps->strStyle
				. "</style>";
		if ( ! empty( $this->oProps->strStyleIE ) )
			echo "<!--[if IE]><style type='text/css' id='admin-page-framework-style-meta-box'>" 
				. $this->oProps->strStyleIE
				. "</style><![endif]-->";
			
	}
	
	/**
	 * Appends the JavaScript script of the framework in the head tag. 
	 * @since			2.0.0
	 * @remark			A callback for the <em>admin_head</em> hook.
	 */ 
	public function addScript() {

		// This class may be instantiated multiple times so use a global flag.
		$strRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$strRootClassName}_ScriptLoaded" ] ) && $GLOBALS[ "{$strRootClassName}_ScriptLoaded" ] ) return;
		$GLOBALS[ "{$strRootClassName}_ScriptLoaded" ] = true;
	
		// Print out the filtered scripts.
		echo "<script type='text/javascript' id='admin-page-framework-script-meta-box'>"
			. $this->oUtil->addAndApplyFilters( $this, "script_{$this->oProps->strClassName}", $this->oProps->strScript )
			. "</script>";	
			
	}
	
	/**
	 * Enqueues the media uploader scripts.
	 * @since			2.0.0
	 * @since			2.1.3			Added the support for the 3.5 media uploader
	 * @remark			A callback for the <em>admin_enqueue_scripts</em> hook.
	 */ 
	public function enqueueUploaderScripts() {
			
		wp_enqueue_script('jquery');			
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');				
			
		if( function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.5 or above
			wp_enqueue_media();	
		else
			wp_enqueue_script('media-upload');
				
	} 	 
	
	/**
 	 * Replaces the label text of a button used in the media uploader.
	 * @since			2.0.0
	 * @remark			A callback for the <em>gettext</em> hook.
	 */ 
	public function replaceThickBoxText( $strTranslated, $strText ) {

		// Replace the button label in the media thick box.
		if ( ! in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php' ) ) ) return $strTranslated;
		if ( $strText != 'Insert into Post' ) return $strTranslated;
		if ( $this->oUtil->getQueryValueInURLByKey( wp_get_referer(), 'referrer' ) != 'admin_page_framework' ) return $strTranslated;
		
		if ( isset( $_GET['button_label'] ) ) return $_GET['button_label'];

		return $this->oProps->strThickBoxButtonUseThis ?  $this->oProps->strThickBoxButtonUseThis : __( 'Use This Image', 'admin-page-framework' );
		
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
		
		foreach( $this->oProps->arrPostTypes as $strPostType ) 
			add_meta_box( 
				$this->oProps->strMetaBoxID, 		// id
				$this->oProps->strTitle, 	// title
				array( $this, 'echoMetaBoxContents' ), 	// callback
				$strPostType,		// post type
				$this->oProps->strContext, 	// context
				$this->oProps->strPriority,	// priority
				$this->oProps->arrFields	// argument
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
		$strOut = wp_nonce_field( $this->oProps->strMetaBoxID, $this->oProps->strMetaBoxID, true, false );
		
		// Begin the field table and loop
		$strOut .= '<table class="form-table">';
		$this->setOptionArray( $oPost->ID, $vArgs['args'] );
		
		foreach ( ( array ) $vArgs['args'] as $arrField ) {
			
			// Avoid undefined index warnings
			$arrField = $arrField + AdminPageFramework_MetaBox_Properties::$arrStructure_Field;
			
			// get value of this field if it exists for this post
			$strStoredValue = get_post_meta( $oPost->ID, $arrField['strFieldID'], true );
			$arrField['vValue'] = $strStoredValue ? $strStoredValue : $arrField['vValue'];
			
			// Check capability. If the access level is not sufficient, skip.
			$arrField['strCapability'] = isset( $arrField['strCapability'] ) ? $arrField['strCapability'] : $this->oProps->strCapability;
			if ( ! current_user_can( $arrField['strCapability'] ) ) continue; 			
			
			// Begin a table row. 
			
			// If it's a hidden input type, do now draw a table row
			if ( $arrField['strType'] == 'hidden' ) {
				$strOut .= "<tr><td style='height: 0; padding: 0; margin: 0; line-height: 0;'>"
					. $this->getField( $arrField )
					. "</td></tr>";
				continue;
			}
			$strOut .= "<tr>";
			if ( ! $arrField['fHideTitleColumn'] )
				$strOut .= "<th><label for='{$arrField['strFieldID']}'>"
						. "<a id='{$arrField['strFieldID']}'></a>"
						. "<span title='" . strip_tags( isset( $arrField['strTip'] ) ? $arrField['strTip'] : $arrField['strDescription'] ) . "'>"
						. $arrField['strTitle'] 
						. "</span>"
						. "</label></th>";		
			$strOut .= "<td>";
			$strOut .= $this->getField( $arrField );
			$strOut .= "</td>";
			$strOut .= "</tr>";
			
		} // end foreach
		$strOut .= '</table>'; // end table
		echo $strOut;
		
	}
	private function setOptionArray( $intPostID, $arrFields ) {
		
		if ( ! is_array( $arrFields ) ) return;
		
		foreach( $arrFields as $intIndex => $arrField ) {
			
			// Avoid undefined index warnings
			$arrField = $arrField + AdminPageFramework_MetaBox_Properties::$arrStructure_Field;

			$this->oProps->arrOptions[ $intIndex ] = get_post_meta( $intPostID, $arrField['strFieldID'], true );
			
		}
	}	
	private function getField( $arrField ) {
		
		// Set the input field name which becomes the option key of the custom meta field of the post.
		$arrField['strName'] = isset( $arrField['strName'] ) ? $arrField['strName'] : $arrField['strFieldID'];
		
		$oField = new AdminPageFramework_InputField( $arrField, $this->oProps->arrOptions, array(), $this->oMsg );	// currently error arrays are not supported for meta-boxes 
		$oField->isMetaBox( true );
		$strOut = $this->oUtil->addAndApplyFilter(
			$this,
			$this->oProps->strClassName . '_' . 'field_' . $arrField['strFieldID'],	// filter: class name + _ + field_ + field id
			$oField->getInputField( $arrField['strType'] ),	// field output
			$arrField // the field array
		);
		unset( $oField );	// release the object for PHP 5.2.x or below.		
		return $strOut;
				
	}
		
	/**
	 * Saves the meta box field data to the associated post. 
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>save_post</em> hook
	 */
	public function saveMetaBoxFields( $intPostID ) {
		
		// Bail if we're doing an auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		
		// If our nonce isn't there, or we can't verify it, bail
		if ( ! isset( $_POST[ $this->oProps->strMetaBoxID ] ) || ! wp_verify_nonce( $_POST[ $this->oProps->strMetaBoxID ], $this->oProps->strMetaBoxID ) ) return;
			
		// Check permissions
		if ( in_array( $_POST['post_type'], $this->oProps->arrPostTypes )   
			&& ( ( ! current_user_can( $this->oProps->strCapability, $intPostID ) ) || ( ! current_user_can( $this->oProps->strCapability, $intPostID ) ) )
		) return;

		// Compose an array consisting of the submitted registered field values.
		$arrInput = array();
		foreach( $this->oProps->arrFields as $arrField ) 
			$arrInput[ $arrField['strFieldID'] ] = isset( $_POST[ $arrField['strFieldID'] ] ) ? $_POST[ $arrField['strFieldID'] ] : null;
			
		// Prepare the old value array.
		$arrOriginal = array();
		foreach ( $arrInput as $strFieldID => $v )
			$arrOriginal[ $strFieldID ] = get_post_meta( $intPostID, $strFieldID, true );
					
		// Apply filters to the array of the submitted values.
		$arrInput = $this->oUtil->addAndApplyFilters( $this, "validation_{$this->oProps->strClassName}", $arrInput, $arrOriginal );

		// Loop through fields and save the data.
		foreach ( $arrInput as $strFieldID => $vValue ) {
			
			// $strOldValue = get_post_meta( $intPostID, $strFieldID, true );			
			$strOldValue = isset( $arrOriginal[ $strFieldID ] ) ? $arrOriginal[ $strFieldID ] : null;
			if ( ! is_null( $vValue ) && $vValue != $strOldValue ) {
				update_post_meta( $intPostID, $strFieldID, $vValue );
				continue;
			} 
			// if ( '' == $strNewValue && $strOldValue ) 
				// delete_post_meta( $intPostID, $arrField['strFieldID'], $strOldValue );
			
		} // end foreach
		
	}	
	
	/*
	 * Magic method
	*/
	function __call( $strMethodName, $arrArgs=null ) {	
		
		// the start_ action hook.
		if ( $strMethodName == $this->oProps->strPrefixStart . $this->oProps->strClassName ) return;
		
		// the class name + field_ field ID filter.
		if ( substr( $strMethodName, 0, strlen( $this->oProps->strClassName . '_' . 'field_' ) ) == $this->oProps->strClassName . '_' . 'field_' ) 
			return $arrArgs[ 0 ];
			
		// the script_ + class name	filter.
		if ( substr( $strMethodName, 0, strlen( "script_{$this->oProps->strClassName}" ) ) == "script_{$this->oProps->strClassName}" ) 
			return $arrArgs[ 0 ];		
	
		// the style_ + class name	filter.
		if ( substr( $strMethodName, 0, strlen( "style_{$this->oProps->strClassName}" ) ) == "style_{$this->oProps->strClassName}" ) 
			return $arrArgs[ 0 ];		

		// the validation_ + class name	filter.
		if ( substr( $strMethodName, 0, strlen( "validation_{$this->oProps->strClassName}" ) ) == "validation_{$this->oProps->strClassName}" )
			return $arrArgs[ 0 ];				
			
	}
}
endif;