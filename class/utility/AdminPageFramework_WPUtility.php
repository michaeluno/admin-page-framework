<?php
if ( ! class_exists( 'AdminPageFramework_WPUtility' ) ) :
/**
 * Provides utility methods which use WordPress functions.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_Utility
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
class AdminPageFramework_WPUtility extends AdminPageFramework_Utility {

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
	 * @param			array			$aActionHooks			a numerically indexed array consisting of action hook names to execute.
	 * @param			mixed			$vArgs1					an argument to pass to the action callbacks.
	 * @param			mixed			$vArgs2					another argument to pass to the action callbacks.
	 * @param			mixed			$_and_more				add as many arguments as necessary to the next parameters.
	 * @return			void			does not return a value.
	 */		
	public function doActions( $aActionHooks, $vArgs1=null, $vArgs2=null, $_and_more=null ) {
		
		$aArgs = func_get_args();		
		$aActionHooks = $aArgs[ 0 ];
		foreach( ( array ) $aActionHooks as $sActionHook  ) {
			$aArgs[ 0 ] = $sActionHook;
			call_user_func_array( 'do_action' , $aArgs );
		}

	}
	
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
	 * <code>$this->oUtil->addAndDoActions( $this, array( 'my_action1', 'my_action2', 'my_action3' ), 'argument_a', 'argument_b' );</code>
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @remark			Accepts variadic parameters.
	 * @param			object			$oCallerObject			the object that holds the callback method that matches the action hook name.
	 * @param			array			$aActionHooks			a numerically index array consisting of action hook names that serve as the callback method names. 
	 * @param			mixed			$vArgs1					the argument to pass to the hook callback functions.
	 * @param			mixed			$vArgs2					another argument to pass to the hook callback functions.
	 * @param			mixed			$_and_more				add as many arguments as necessary to the next parameters.
	 * @return			void
	 */ 
	public function addAndDoActions( $oCallerObject, $aActionHooks, $vArgs1=null, $vArgs2=null, $_and_more=null ) {
	
		$aArgs = func_get_args();	
		$oCallerObject = $aArgs[ 0 ];
		$aActionHooks = $aArgs[ 1 ];
		foreach( ( array ) $aActionHooks as $sActionHook ) {
			$aArgs[ 1 ] = $sActionHook;
			call_user_func_array( array( $this, 'addAndDoAction' ) , $aArgs );			
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
	public function addAndDoAction( $oCallerObject, $sActionHook, $vArgs1=null, $vArgs2=null, $_and_more=null ) {
		
		$iArgs = func_num_args();
		$aArgs = func_get_args();
		$oCallerObject = $aArgs[ 0 ];
		$sActionHook = $aArgs[ 1 ];
		add_action( $sActionHook, array( $oCallerObject, $sActionHook ), 10, $iArgs - 2 );
		unset( $aArgs[ 0 ] );	// remove the first element, the caller object
		call_user_func_array( 'do_action' , $aArgs );
		
	}
	public function addAndApplyFilters() {	// Parameters: $oCallerObject, $aFilters, $vInput, $vArgs...
			
		$aArgs = func_get_args();	
		$oCallerObject = $aArgs[ 0 ];
		$aFilters = $aArgs[ 1 ];
		$vInput = $aArgs[ 2 ];

		foreach( ( array ) $aFilters as $sFilter ) {
			$aArgs[ 1 ] = $sFilter;
			$aArgs[ 2 ] = $vInput;
			$vInput = call_user_func_array( array( $this, 'addAndApplyFilter' ) , $aArgs );						
		}
		return $vInput;
		
	}
	public function addAndApplyFilter() {	// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...

		$iArgs = func_num_args();
		$aArgs = func_get_args();
		$oCallerObject = $aArgs[ 0 ];
		$sFilter = $aArgs[ 1 ];
		add_filter( $sFilter, array( $oCallerObject, $sFilter ), 10, $iArgs - 2 );	// this enables to trigger the method named $sFilter and the magic method __call() will be called
		unset( $aArgs[ 0 ] );	// remove the first element, the caller object	// array_shift( $aArgs );							
		return call_user_func_array( 'apply_filters', $aArgs );	// $aArgs: $vInput, $vArgs...
		
	}		
	
	/**
	 * Provides an array consisting of filters for the addAndApplyFileters() method.
	 * 
	 * The order is, page + tab -> page -> class, by default but it can be reversed with the <var>$bReverse</var> parameter value.
	 * 
	 * @since			2.0.0
	 * @access			public
	 * @return				array			Returns an array consisting of the filters.
	 */ 
	public function getFilterArrayByPrefix( $sPrefix, $sClassName, $sPageSlug, $sTabSlug, $bReverse=false ) {
				
		$aFilters = array();
		if ( $sTabSlug && $sPageSlug )
			$aFilters[] = "{$sPrefix}{$sPageSlug}_{$sTabSlug}";
		if ( $sPageSlug )	
			$aFilters[] = "{$sPrefix}{$sPageSlug}";			
		if ( $sClassName )
			$aFilters[] = "{$sPrefix}{$sClassName}";
		
		return $bReverse ? array_reverse( $aFilters ) : $aFilters;	
		
	}
		
	/**
	 * Returns an array of plugin data from the given path.		
	 * 
	 * An alternative to get_plugin_data() as some users change the location of the wp-admin directory.
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Changed the scope to public and become static.
	 * @access			public
	 */ 
	static public function getScriptData( $sPath, $sType='plugin' )	{
	
		$aData = get_file_data( 
			$sPath, 
			array(
				'sName' => 'Name',
				'sURI' => 'URI',
				'sScriptName' => 'Script Name',
				'sLibraryName' => 'Library Name',
				'sLibraryURI' => 'Library URI',
				'sPluginName' => 'Plugin Name',
				'sPluginURI' => 'Plugin URI',
				'sThemeName' => 'Theme Name',
				'sThemeURI' => 'Theme URI',
				'sVersion' => 'Version',
				'sDescription' => 'Description',
				'sAuthor' => 'Author',
				'sAuthorURI' => 'Author URI',
				'sTextDomain' => 'Text Domain',
				'sDomainPath' => 'Domain Path',
				'sNetwork' => 'Network',
				// Site Wide Only is deprecated in favour of Network.
				'_sitewide' => 'Site Wide Only',
			),
			in_array( $sType, array( 'plugin', 'theme' ) ) ? $sType : 'plugin' 
		);			

		switch ( trim( $sType ) ) {
			case 'theme':	
				$aData['sName'] = $aData['sThemeName'];
				$aData['sURI'] = $aData['sThemeURI'];
				break;
			case 'library':	
				$aData['sName'] = $aData['sLibraryName'];
				$aData['sURI'] = $aData['sLibraryURI'];
				break;
			case 'script':	
				$aData['sName'] = $aData['sScriptName'];
				break;		
			case 'plugin':	
				$aData['sName'] = $aData['sPluginName'];
				$aData['sURI'] = $aData['sPluginURI'];
				break;
			default:	
				break;				
		}		

		return $aData;
		
	}			
	
	/**
	 * Retrieves the current URL in the admin page.
	 * 
	 * @since			2.1.1
	 */
	public function getCurrentAdminURL() {
		
		$sRequestURI = $GLOBALS['is_IIS'] ? $_SERVER['PATH_INFO'] : $_SERVER["REQUEST_URI"];
		$sPageURL = ( @$_SERVER["HTTPS"] == "on" ) ? "https://" : "http://";
		
		if ( $_SERVER["SERVER_PORT"] != "80" ) 
			$sPageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $sRequestURI;
		else 
			$sPageURL .= $_SERVER["SERVER_NAME"] . $sRequestURI;
		
		return $sPageURL;
		
	}
	
	/**
	 * Returns a url with modified query stings.
	 * 
	 * Identical to the getQueryURL() method except that if the third parameter is omitted, it will use the currently browsed admin url.
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @param			array			$aAddingQueries			The appending query key value pairs e.g. array( 'page' => 'my_page_slug', 'tab' => 'my_tab_slug' )
	 * @param			array			$aRemovingQueryKeys		( optional ) The removing query keys. e.g. array( 'settings-updated', 'my-custom-admin-notice' )
	 * @param			string			$sSubjectURL				( optional ) The subject url to modify
	 * @return			string			The modified url.
	 */
	public function getQueryAdminURL( $aAddingQueries, $aRemovingQueryKeys=array(), $sSubjectURL='' ) {
		
		$sSubjectURL = $sSubjectURL ? $sSubjectURL : add_query_arg( $_GET, admin_url( $GLOBALS['pagenow'] ) );
		return $this->getQueryURL( $aAddingQueries, $aRemovingQueryKeys, $sSubjectURL );
		
	}
	/**
	 * Returns a url with modified query stings.
	 * 
	 * @since			2.1.2
	 * @param			array			$aAddingQueries			The appending query key value pairs
	 * @param			array			$aRemovingQueryKeys			The removing query key value pairs
	 * @param			string			$sSubjectURL				The subject url to modify
	 * @return			string			The modified url.
	 */
	public function getQueryURL( $aAddingQueries, $aRemovingQueryKeys, $sSubjectURL ) {
		
		// Remove Queries
		$sSubjectURL = empty( $aRemovingQueryKeys ) 
			? $sSubjectURL 
			: remove_query_arg( ( array ) $aRemovingQueryKeys, $sSubjectURL );
			
		// Add Queries
		$sSubjectURL = add_query_arg( $aAddingQueries, $sSubjectURL );
		
		return $sSubjectURL;
		
	}	

	/**
	 * Calculates the URL from the given path.
	 * 
	 * @since			2.1.5
	 * @static
	 * @access			public
	 * @return			string			The source url
	 */
	static public function getSRCFromPath( $sFilePath ) {
						
		$oWPStyles = new WP_Styles();	// It doesn't matter whether the file is a style or not. Just use the built-in WordPress class to calculate the SRC URL.
		$sRelativePath = AdminPageFramework_Utility::getRelativePath( ABSPATH, $sFilePath );		
		$sRelativePath = preg_replace( "/^\.[\/\\\]/", '', $sRelativePath, 1 );	// removes the heading ./ or .\ 
		$sHref = trailingslashit( $oWPStyles->base_url ) . $sRelativePath;
		unset( $oWPStyles );	// for PHP 5.2.x or below
		return esc_url( $sHref );		
		
	}	

	/**
	 * Resolves the given src.
	 * 
	 * Checks if the given string is a url, a relative path, or an absolute path and returns the url if it's not a relative path.
	 * 
	 * @since			2.1.5
	 * @since			2.1.6			Moved from the AdminPageFramework_HeadTag_Base class. Added the $bReturnNullIfNotExist parameter.
	 */
	static public function resolveSRC( $sSRC, $bReturnNullIfNotExist=false ) {	

		if ( ! $sSRC )	
			return $bReturnNullIfNotExist ? null : $sSRC;
			
		// It is a url
		if ( filter_var( $sSRC, FILTER_VALIDATE_URL ) )
			return $sSRC;

		// If the file exists, it means it is an absolute path. If so, calculate the URL from the path.
		if ( file_exists( realpath( $sSRC ) ) )
			return self::getSRCFromPath( $sSRC );
		
		if ( $bReturnNullIfNotExist )
			return null;
		
		// Otherwise, let's assume the string is a relative path 'to the WordPress installed absolute path'.
		return $sSRC;
		
	}	
	
	/**
	 * Enhances the parent method generateAttributes() by escaping the attribute values.
	 * 
	 * @since			3.0.0
	 */
	static public function generateAttributes( array $aAttributes ) {
		
		foreach( $aAttributes as $sAttribute => &$asProperty ) {
			if ( is_array( $asProperty ) || is_object( $asProperty ) )
				unset( $aAttributes[ $sAttribute ] );
			$asProperty = esc_attr( $asProperty );	 // $aAttributes = array_map( 'esc_attr', $aAttributes );	// this also converts arrays into string value, Array.
		}		
		return parent::generateAttributes( $aAttributes );
		
	}	
	
}
endif;