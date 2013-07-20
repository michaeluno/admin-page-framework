<?php 
/*
	Name: Admin Page Framework
	Plugin URI: http://wordpress.org/extend/plugins/admin-page-framework/
	Author:  Michael Uno
	Author URI: http://michaeluno.jp
	Version: 1.1.0
	Requirements: WordPress 3.2 or above, PHP 5.2.4 or above.
	Description: Provides simpler means of building administration pages for plugin and theme developers. 
	Usage: 1. Extend the class 2. Override the setUp() method. 3. Use the hook functions.
*/

if ( ! class_exists( 'AdminPageFramework_WPUtilities' ) ) :
abstract class AdminPageFramework_WPUtilities {
		
	/*
	 * Utility methods which use WordPress functions
	 */
	protected function doActions() {	// Parameters: $arrActionHooks, $vArgs...
		
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
	public function addAndDoActions() {	// Parameters: $oCallerObject, $arrActionHooks, $vArgs...
	
		$arrArgs = func_get_args();	
		$oCallerObject = $arrArgs[ 0 ];
		$arrActionHooks = $arrArgs[ 1 ];
		foreach( ( array ) $arrActionHooks as $strActionHook ) {
			$arrArgs[ 1 ] = $strActionHook;
			call_user_func_array( array( $this, 'addAndDoAction' ) , $arrArgs );			
		}
		
	}
	public function addAndDoAction() {	// Parameters: $oCallerObject, $strActionHook, $vArgs...
		
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
	
	public function getFilterArrayByPrefix( $strPrefix, $strClassName, $strPageSlug, $strTabSlug, $fReverse=false ) {
			
		// This provides an array consisting of filters for the addAndApplyFileters() method.	
		// The order is page + tab -> page -> class, by default but it can be reversed with the $fReverse parameter value.
		
		$arrFilters = array();
		if ( $strTabSlug && $strPageSlug )
			$arrFilters[] = "{$strPrefix}{$strPageSlug}_{$strTabSlug}";
		if ( $strPageSlug )	
			$arrFilters[] = "{$strPrefix}{$strPageSlug}";
		$arrFilters[] = "{$strPrefix}{$strClassName}";
		
		return $fReverse ? array_reverse( $arrFilters ) : $arrFilters;	
		
	}
	
	public function goRedirect( $strURL ) {
		
		// Redirects to the given URL and exits. Saves one extra line, exit;.
		if ( ! function_exists('wp_redirect') ) include_once( ABSPATH . WPINC . '/pluggable.php' );
		wp_redirect( $strURL );
		exit;		
		
	}
	
	protected function getScriptData( $strPath, $strType )	{
	
		// Returns an array of plugin data from the given path.		
		// An alternative to get_plugin_data() as some users change the location of the wp-admin directory.
		$arrData = get_file_data( 
			$strPath, 
			array(
				'strPluginName' => 'Plugin Name',
				'strPluginURI' => 'Plugin URI',
				'strThemeURI' => 'Theme URI',
				'strThemeName' => 'Theme Name',
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
			$strType	// 'plugin' or 'theme'
		);				
		$arrData['strName'] = ( $strType == 'plugin' ) ? $arrData['strPluginName'] : $arrData['strThemeName'];
		$arrData['strScriptURI'] = ( $strType == 'plugin' ) ? $arrData['strPluginURI'] : $arrData['strThemeURI'];
		return $arrData;
		
	}			
}
endif;

if ( ! class_exists( 'AdminPageFramework_Utilities' ) ) :
class AdminPageFramework_Utilities extends AdminPageFramework_WPUtilities {

	/*
	 * Utility methods which do not use WordPress functions
	 */
	public function sanitizeSlug( $strSlug ) {	// moved from the main class in 1.0.4, must be public 
		
		// Converts non-alphabetic characters to underscore.
		return preg_replace( '/[^a-zA-Z0-9_\x7f-\xff]/', '_', trim( $strSlug ) );
		
	}	
	public function sanitizeString( $strString ) {	// moved from the main class in 1.0.4, must be public

		// Similar to the above SanitizeSlug() except that this allows hyphen.
		return preg_replace( '/[^a-zA-Z0-9_\x7f-\xff\-]/', '_', $strString );

	}	
	
	protected function getCorrespondingArrayValue( $vSubject, $strKey, $strDefault='' ) {	
		
		// When there are multiple arrays and they have similar index structure but it's not certain,
		// use this method to retrieve the corresponding key value. If the subject value is not an array, it will return
		// the string value of the subject value.
		// This is mainly used by the field array to insert user-defined key values.
		
		// If $vSubject is null,
		if ( ! isset( $vSubject ) ) return $strDefault;	
			
		// If $vSubject is not an array, 
		if ( ! is_array( $vSubject ) ) return ( string ) $vSubject;	// consider it as string.
		
		// Consider $vSubject as array.
		if ( isset( $vSubject[ $strKey ] ) ) return $vSubject[ $strKey ];
		
		return $strDefault;
		
	}

	protected function getArrayDimension( $array ) {
		return ( is_array( reset( $array ) ) ) ? $this->getArrayDimension( reset( $array ) ) + 1 : 1;
	}

	public function uniteArraysRecursive( $arrPrecedence, $arrDefault ) {
		
		// Merges two multi-dimensional arrays recursively. The first parameter array takes its precedence.
		// This is useful to merge default option values.
		
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

	public function getQueryValueInURLByKey( $strURL, $strQueryKey ) {
		
		$arrURL = parse_url( $strURL );
		parse_str( $arrURL['query'], $arrQuery );		
		return isset( $arrQuery[ $strQueryKey ] ) ? $arrQuery[ $strQueryKey ] : null;
		
	}

	public function fixNumber( $numToFix, $numDefault, $numMin="", $numMax="" ) {
	
		// Checks if the passed value is a number and set it to the default if not.
		// This is useful for form data validation. If it is a number and exceeds the set maximum number, 
		// it sets it to the maximum value. If it is a number and is below the minimum number, it sets to the minimum value.
		// Set a blank value for no limit.
		
		if ( ! is_numeric( trim( $numToFix ) ) ) return $numDefault;
		if ( $numMin != "" && $numToFix < $numMin) return $numMin;
		if ( $numMax != "" && $numToFix > $numMax ) return $numMax;
		return $numToFix;
		
	}		
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_Pages' ) ) :
abstract class AdminPageFramework_Pages {
	
	/*
	 * Stores registering page information.
	 */ 
	
	protected static $strDefaultStyle = 	// the default css rules
		".wrap div.updated, .wrap div.settings-error { clear: both; margin-top: 16px;} 
		.taxonomy-checklist li { margin: 8px 0 8px 20px; }
		div.taxonomy-checklist {
			padding: 8px 0 8px 10px;
			margin-bottom: 20px;
		}
		.taxonomy-checklist ul {
			list-style-type: none;
			margin: 0;
		}
		.taxonomy-checklist ul ul {
			margin-left: 1em;
		}
		.taxonomy-checklist-label {
			margin-left: 0.5em;
		}
		.image_preview {
			border: none; clear:both; margin-top: 20px;	max-width:100%; 
		}
		.image_preview img {
			max-height: 600px; max-width: 800px;
		}
		";	
	protected static $arrPrefixes = array(	// must be protected as the extended class accesses it, such as 'start_';
		'start_'		=> 'start_',
		'do_before_'	=> 'do_before_',
		'do_after_'		=> 'do_after_',
		'do_form_'		=> 'do_form_',
		'do_'			=> 'do_',
		'content_'		=> 'content_',
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
	protected static $arrPrefixesForCallbacks = array(		// unlike the above $arrPrefixes, these require to set the return value 
		'section_'		=> 'section_',
		'field_'		=> 'field_',
		'validation_'	=> 'validation_',
	);
	protected static $arrScreenIconIDs = array(
		'edit', 'post', 'index', 'media', 'upload', 'link-manager', 'link', 'link-category', 
		'edit-pages', 'page', 'edit-comments', 'themes', 'plugins', 'users', 'profile', 
		'user-edit', 'tools', 'admin', 'options-general', 'ms-admin', 'generic',
	);	
	private static $arrStructure_InPageTabElements = array(
		'strPageSlug' => null,
		'strTabSlug' => null,
		'strTitle' => null,
		'numOrder' => null,
		'fHide'	=> null,
		'strParentTabSlug' => null,	// this needs to be set if the above fHide is true so that the plugin can mark the parent tab to be active when the hidden page is accessed.
	);
	
	
	/*
	 * Front end methods - users will use these methods in their class definitions
	 * */
	protected function showPageTitle( $fShowPageTitle=true ) {
		$this->oProps->fShowPageTitle = $fShowPageTitle;
	}	 
	protected function showPageHeadingTabs( $fShowPageHeadingTabs=true ) {
		$this->oProps->fShowPageHeadingTabs = $fShowPageHeadingTabs;
	}
	protected function setInPageTabTag( $strTag='h3' ) {
		$this->oProps->strInPageTabTag = $strTag;
	}
	
	/*
	 * Back end methods
	 * */
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
						settings_fields( $this->oProps->strOptionKey );
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
	private function showSettingsErrors() {
		
		$arrSettingsMessages = get_settings_errors( $this->oProps->strOptionKey );
		
		// If custom messages are added, remove the default one. 
		if ( count( $arrSettingsMessages ) > 1 ) 
			$this->removeDefaultSettingsNotice();
		
		settings_errors( $this->oProps->strOptionKey );	// Show the message like "The options have been updated" etc.
	
	}	
	protected function removeDefaultSettingsNotice() {
		
		// This removes the settings messages ( admin notice ) added automatically by the framework when the form is submitted.
		// This is used when a custom message is added manually and the default message should not be displayed.
		
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
	protected function getFormOpeningTag() {
		
		if ( ! $this->oProps->fEnableForm ) return '';
		return "<form action='options.php' method='post' enctype='{$this->oProps->strFormEncType}'>";
	
	}
	protected function getFormClosingTag( $strPageSlug, $strTabSlug ) {

		if ( ! $this->oProps->fEnableForm ) return '';	
		return "<input type='hidden' name='strPageSlug' value='{$strPageSlug}' />" . PHP_EOL
			. "<input type='hidden' name='strTabSlug' value='{$strTabSlug}' />" . PHP_EOL			
			. "</form><!-- End Form -->";
	
	}	
	private function getScreenIcon( $strPageSlug ) {	// since 1.1.0

		// If the icon path is explicitly set, use it.
		if ( isset( $this->oProps->arrPages[ $strPageSlug ]['strPathIcon32x32'] ) ) 
			return '<div class="icon32" style="background-image: url(' . $this->oProps->arrPages[ $strPageSlug ]['strPathIcon32x32'] . ');"><br /></div>';
		
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
	private function getScreenIDAttribute( $oScreen ) {		// since 1.1.0
		
		if ( ! empty( $oScreen->parent_base ) )
			return $oScreen->parent_base;
	
		if ( 'page' == $oScreen->post_type )
			return 'edit-pages';		
			
		return esc_attr( $oScreen->base );
		
	}
	
	private function getPageHeadingTabs( $strCurrentPageSlug, $strTag='h2', $arrOutput=array() ) {
		
		// If the page title is disabled, return an empty string.
		if ( ! $this->oProps->fShowPageTitle ) return "";
		
		// If the page heading tab visibility is disabled, return the title.
		if ( ! $this->oProps->fShowPageHeadingTabs ) 
			return "<{$strTag}>" . $this->oProps->arrPages[ $strCurrentPageSlug ]['strPageTitle'] . "</{$strTag}>";		
		
		foreach( $this->oProps->arrPages as $arrSubPage ) {
			
			// For added sub-pages
			if ( isset( $arrSubPage['strPageSlug'] ) && $arrSubPage['fPageHeadingTab'] ) {
				// Check if the current tab number matches the iteration number. If not match, then assign blank; otherwise put the active class name.
				$strClassActive =  $strCurrentPageSlug == $arrSubPage['strPageSlug']  ? 'nav-tab-active' : '';		
				$arrOutput[] = "<a class='nav-tab {$strClassActive}' "
					. "href='" . add_query_arg( array( 'page' => $arrSubPage['strPageSlug'], 'tab' => false ) ) . "'"	//?page={$arrSubPage['strPageSlug']}"
					. "'>"
					. $arrSubPage['strPageTitle']
					. "</a>";	
			}
			
			// For added menu links
			if ( 
				isset( $arrSubPage['strURL'] )
				&& $arrSubPage['strType'] == 'link' 
				&& $arrSubPage['fPageHeadingTab']
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
	
	private function getInPageTabs( $strCurrentPageSlug, $strTag='h3', $arrOutput=array() ) {
		
		// If in-page tabs are not set, return an empty string.
		if ( empty( $this->oProps->arrInPageTabs[ $strCurrentPageSlug ] ) ) return implode( '', $arrOutput );
		
		$strCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->getDefaultInPageTab( $strCurrentPageSlug );
		$strCurrentTabSlug = $this->getParentTabSlug( $strCurrentPageSlug, $strCurrentTabSlug );
	
		// Get the actual string buffer.
		foreach( $this->oProps->arrInPageTabs[ $strCurrentPageSlug ] as $strTabSlug => $arrInPageTab ) {
					
			// If it's hidden and its parent tab is not set, skip
			if ( $arrInPageTab['fHide'] && ! isset( $arrInPageTab['strParentTabSlug'] ) ) continue;
			
			// The parent tab means the root tab when there is a hidden tab that belongs to it.
			$strInPageTabSlug = isset( $arrInPageTab['strParentTabSlug'] ) ? $arrInPageTab['strParentTabSlug'] : $arrInPageTab['strTabSlug'];
							
			// Check if the current tab slug matches the iteration slug. If not match, assign blank; otherwise, put the active class name.
			$fIsActiveTab = ( $strCurrentTabSlug == $strInPageTabSlug );
			$arrOutput[ $strInPageTabSlug ] = "<a class='nav-tab " . ( $fIsActiveTab ? "nav-tab-active" : "" ) . "' "
				. "href='" . add_query_arg( array( 'page' => $strCurrentPageSlug, 'tab' => $strInPageTabSlug ) ) 
				. "'>"
				. $this->oProps->arrInPageTabs[ $strCurrentPageSlug ][ $strInPageTabSlug ]['strTitle'] //	"{$arrInPageTab['strTitle']}"
				. "</a>";
		
		}		
		if ( ! empty( $arrOutput ) )
			return "<div class='admin-page-framework-in-page-tab'><{$strTag} class='nav-tab-wrapper in-page-tab'>" 
				. implode( '', $arrOutput )
				. "</{$strTag}></div>";
			
	}
	private function getParentTabSlug( $strPageSlug, $strTabSlug ) {
		
		return isset( $this->oProps->arrInPageTabs[ $strPageSlug ][ $strTabSlug ]['strParentTabSlug'] ) 
			? $this->oProps->arrInPageTabs[ $strPageSlug ][ $strTabSlug ]['strParentTabSlug']
			: $strTabSlug;
		
	}
	
	protected function addInPageTab( $strPageSlug, $strTabTitle, $strTabSlug, $numOrder=null, $fHide=null, $strParentTabSlug=null ) {	// since 1.1.0		
		
		// Always use this method to add in-page tabs to ensure the array holds all the necessary keys.
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
	protected function addInPageTabs() {	// since 1.1.0
	
		/* Usage: e.g.
		$this->addInPageTabs(
			array(
				'strTabSlug' => 'firsttab',
				'strTitle' => 'Text Fields',
				'fHide'	=> false,
				'strPageSlug' => 'myfirstpage'
			),
			array(
				'strTabSlug' => 'secondtab',
				'strTitle' => 'Selectors and Checkboxes',
				'fHide'	=> false,
				'strPageSlug' => 'myfirstpage'
				'strParentTabSlug' => 'something'
			)
		);	 */		
		
		foreach( func_get_args() as $arrTab ) {
			if ( ! is_array( $arrTab ) ) continue;
			$arrTab = $arrTab + self::$arrStructure_InPageTabElements;	// avoid undefined index warnings.
			$this->addInPageTab( $arrTab['strPageSlug'], $arrTab['strTitle'], $arrTab['strTabSlug'], $arrTab['numOrder'], $arrTab['fHide'], $arrTab['strParentTabSlug'] );
		}
		
	}

	public function finalizeInPageTabs() {	// since 1.1.0
	
		// A callback method for the admin_menu hook. This finalizes the added in-page tabs and sets the default in-page tab for each page.
		// Also this sorts the in-page tab array.
		// This must be done before registering settings sections because the default tab needs to be determined in the process.
		
		foreach( $this->oProps->arrPages as $strPageSlug => $arrPage ) {
			
			if ( ! isset( $this->oProps->arrInPageTabs[ $strPageSlug ] ) ) continue;
			
			// Apply filters to let modify the in-page tab array.
			$this->oProps->arrInPageTabs[ $strPageSlug ] = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $strFilter, $vInput, $vArgs...
				$this,
				"{$this->oProps->strClassName}_{$strPageSlug}_tabs",
				$this->oProps->arrInPageTabs[ $strPageSlug ]			
			);	
						
			// Sort the in-page tab array.
			uasort( $this->oProps->arrInPageTabs[ $strPageSlug ], array( $this->oProps, 'sortByOrder' ) );
			
			// Set the default tab for the page.
			foreach( $this->oProps->arrInPageTabs[ $strPageSlug ] as $strTabSlug => $arrInPageTab ) { 		// The first iteration item is the default one.
			
				if ( ! isset( $arrInPageTab['strTabSlug'] ) || isset( $arrInPageTab['fHide'] ) ) continue;	// if it's a hidden tab, it should not be the default tab.
				
				$this->oProps->arrDefaultInPageTabs[ $strPageSlug ] = $arrInPageTab['strTabSlug'];
				break;
			}
		}
	}			
	
	protected function getDefaultInPageTab( $strPageSlug ) {
	
		// Returns the default in-page tab slug by the given page slug. This is used in the __call() method in the main class.		
		if ( ! $strPageSlug ) return '';
		
		return isset( $this->oProps->arrDefaultInPageTabs[ $strPageSlug ] ) 
			? $this->oProps->arrDefaultInPageTabs[ $strPageSlug ]
			: '';

	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_Menu' ) ) :
abstract class AdminPageFramework_Menu extends AdminPageFramework_Pages {
	
	/*
	 * Manipulates menu creation.
	 * since 1.1.0
	 */
	
	public static $arrBuiltInRootMenuSlugs = array(
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
	protected static $arrStructure_SubMenuPage = array(
		'strPageTitle' => null, 
		'strPageSlug' => null, 
		'strScreenIcon' => null,
		'strCapability' => null, 
		'numOrder' => null,
		'fPageHeadingTab' => true,	// if this is set false, the won't be displayed in the page heading tab.
	);
	
	/*
	 * Front-end methods - the user uses these methods in their class definition.
	 * */
	protected function setRootMenuPage( $strRootMenuLabel, $strPathIcon16x16=null, $intMenuPosition=null ) {

		$strRootMenuLabel = trim( $strRootMenuLabel );
		$strSlug = $this->isBuiltInMenuItem( $strRootMenuLabel );	// if true, this method returns the slug
		$this->oProps->arrRootMenu = array(
			'strTitle'			=> $strRootMenuLabel,
			'strPageSlug' 		=> $strSlug ? $strSlug : $this->oProps->strClassName,	
			'strPathIcon16x16'	=> $strPathIcon16x16,
			'intPosition'		=> $intMenuPosition,
			'fCreateRoot'		=> $strSlug ? false : true,
		);	
		
		$this->setPageCapability();
			
	}
	protected function setRootMenuPageBySlug( $strRootMenuSlug ) {
		
		// Sets the root menu by the given slug. The page should be already created or scheduled to be created separately.
		// The flag key, fCreateRoot, becomes false, which indicates to use an existing menu item. 
		// e.g. $this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );
		
		$this->oProps->arrRootMenu['strPageSlug'] = $strRootMenuSlug;	// do not sanitize the slug here because post types includes a question mark.
		$this->oProps->arrRootMenu['fCreateRoot'] = false;
		
		$this->setPageCapability();
	}	
	protected function addSubMenuPages() {
		foreach ( func_get_args() as $arrSubMenuPage ) {
			$arrSubMenuPage = $arrSubMenuPage + self::$arrStructure_SubMenuPage;	// avoid undefined index warnings.
			$this->addSubMenuPage(
				$arrSubMenuPage['strPageTitle'],
				$arrSubMenuPage['strPageSlug'],
				$arrSubMenuPage['strScreenIcon'],
				$arrSubMenuPage['strCapability'],
				$arrSubMenuPage['numOrder'],
				$arrSubMenuPage['fPageHeadingTab']
			);				
		}
	}
	protected function addSubMenuPage( $strPageTitle, $strPageSlug, $strScreenIcon=null, $strCapability=null, $numOrder=null, $fPageHeadingTab=true ) {
		
		$strPageSlug = $this->oUtil->sanitizeSlug( $strPageSlug );
		$intCount = count( $this->oProps->arrPages );
		$this->oProps->arrPages[ $strPageSlug ] = array(  
			'strPageTitle'		=> $strPageTitle,
			'strPageSlug'		=> $strPageSlug,
			'strType'			=> 'page',	// this is used to compare with the link type.
			'strPathIcon32x32'	=> $strScreenIcon ? $strScreenIcon : null,
			'strScreenIconID'	=> in_array( $strScreenIcon, self::$arrScreenIconIDs ) ? $strScreenIcon : null,
			'strCapability'		=> isset( $strCapability ) ? $strCapability : $this->oProps->strCapability,
			'numOrder'			=> is_numeric( $numOrder ) ? $numOrder : $intCount + 10,
			'fPageHeadingTab'	=> $fPageHeadingTab,
		);	
			
	}

	protected function isBuiltInMenuItem( $strMenuLabel ) {
		
		// Returns the associated slug string, if true.
		$strMenuLabelLower = strtolower( $strMenuLabel );
		if ( array_key_exists( $strMenuLabelLower, self::$arrBuiltInRootMenuSlugs ) )
			return self::$arrBuiltInRootMenuSlugs[ $strMenuLabelLower ];
		
	}
	
	/*
	 * Back-end public methods
	*/ 	 
	 /*
	 * Private methods that are called from the class methods internally.
	 */
	private function setPageCapability() {
		
		// This lets the Settings API to allow the custom capability. The Settings API requires manage_options by default.
		// the option_page_capability_{} filter is supported since WordPress 3.2 
		add_filter( "option_page_capability_" .  $this->oProps->arrRootMenu['strPageSlug'], array( $this->oProps, 'getCapability' ) );		

	}	
	private function registerRootMenuPage() {

		$strHookName = add_menu_page(  
			$this->oProps->strClassName,						// Page title - will be invisible anyway
			$this->oProps->arrRootMenu['strTitle'],				// Menu title - should be the root page title.
			$this->oProps->strCapability,						// Capability - access right
			$this->oProps->arrRootMenu['strPageSlug'],			// Menu ID 
			'', //array( $this, $this->oProps->strClassName ), 	// Page content displaying function
			$this->oProps->arrRootMenu['strPathIcon16x16'],		// icon path
			isset( $this->arrRootMenu['intPosition'] ) ? $this->arrRootMenu['intPosition'] : null	// menu position
		);

	}
	private function registerSubMenu( $arrArgs ) {
	
		$strType = $arrArgs['strType'];	// page or link
		$strTitle = $strType == 'page' ? $arrArgs['strPageTitle'] : $arrArgs['strMenuTitle'];
		$strCapability = $arrArgs['strCapability'];
			
		$strCapability = isset( $strCapability ) ? $strCapability : $this->strCapability;
		if ( ! current_user_can( $strCapability ) ) return;		
		
		// Add the sub-page to the sub-menu
		// $this->oUtil should be instantiated in the extended object constructor.

		
		$arrResult = array();
		$strRootPageSlug = $this->oProps->arrRootMenu['strPageSlug'];
		
		if ( $strType == 'page' )
			$arrResult[ $strPageSlug ] = add_submenu_page( 
				$strRootPageSlug,						// the root(parent) page slug
				$strTitle,								// page_title
				$strTitle,								// menu_title
				$strCapability,				 			// strCapability
				$strPageSlug = $this->oUtil->sanitizeSlug( $arrArgs['strPageSlug'] ),								// menu_slug
				array( $this, $strPageSlug ) 				// triggers the __call() magic method with the method name of this slug.
			);			
		else if ( $strType == 'link' )
			$GLOBALS['submenu'][ $strRootPageSlug ][] = array ( 
				$strTitle, 
				$strCapability, 
				$arrArgs['strURL'],
			);	
			
		return $arrResult;	// maybe useful to debug.

	}
	
	/*
	 * Callback methods
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
		foreach ( $this->oProps->arrPages as $arrSubMenuItem ) 
			$this->registerSubMenu( $arrSubMenuItem );
			
		// After adding the sub menus, if the root menu is created, remove the page that is automatically created when registering the root menu.
		if ( $this->oProps->arrRootMenu['fCreateRoot'] ) 
			remove_submenu_page( $this->oProps->arrRootMenu['strPageSlug'], $this->oProps->arrRootMenu['strPageSlug'] );
		
	}	
}
endif;

if ( ! class_exists( 'AdminPageFramework_SettingsAPI' ) ) :
abstract class AdminPageFramework_SettingsAPI extends AdminPageFramework_Menu {
	
	protected static $arrStructure_Section = array(	// the default structure of the section array.
		'strSectionID' => null,
		'strPageSlug' => null,
		'strTabSlug' => null,
		'strTitle' => null,
		'strDescription' => null,
		'strCapability' => null,
		'fIf' => true,	
		'numOrder' => null,	// do not set the default number here because incremented numbers will be added when registering the sections.
	);	
	protected static $arrStructure_Field = array(	// the default structure of the field array.
		'strFieldID' => null, 		// ( mandatory )
		'strSectionID' => null,		// ( mandatory )
		'strType' => null,			// ( mandatory )
		'strPageSlug' => null,		// This will be assigned automatically in the formatting method.
		'strOptionKey' => null,		// This will be assigned automatically in the formatting method.
		'strClassName' => null,		// This will be assigned automatically in the formatting method.
		'strCapability' => null,		
		'strTitle' => null,
		'strTip' => null,
		'strDescription' => null,
		'strName' => null,			// the name attribute of the input field.
		'strError' => null,			// error message for the field
		'strBeforeField' => null,
		'strAfterField' => null,
		'fIf' => true,
		'numOrder' => null,			// do not set the default number here for this key.		
	);	
	
	protected $arrFieldErrors;		// Stores the settings field errors. Do not set a value here since it is checked to see it's null.
	
	protected $fIsImageFieldScriptEnqueued = false;	// A flag that indicates whether the JavaScript script for image selector is enqueued.
	
	/*
	 * Front-end methods that the user uses in the class definition.
	 * */
	protected function setSettingsNotice( $strMsg, $strType='error', $strID=null ) {
		
		add_settings_error( 
			$this->oProps->strOptionKey, // the script specific ID so the other settings error won't be displayed with the settings_errors() function.
			isset( $strID ) ? $strID : ( isset( $_GET['page'] ) ? $_GET['page'] : $this->oProps->strOptionKey ), 	// the id attribute for the message div element.
			$strMsg,	// error or updated
			$strType
		);
					
	}
	
	protected function addSettingSections() {	// since 1.1.0
			
		// This method just adds the given section array items into the section array property. 
		// The actual registration will be dealt with the registerSetction() method.
				
		$strCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;		
				
		foreach( func_get_args() as $arrSection ) {	

			if ( ! is_array( $arrSection ) ) continue;

			$arrSection = $arrSection + self::$arrStructure_Section;	// avoid undefined index warnings.
			
			// Sanitize the IDs since they are used as a callback method name, the slugs as well.
			$arrSection['strSectionID'] = $this->oUtil->sanitizeSlug( $arrSection['strSectionID'] );
			$arrSection['strPageSlug'] = $this->oUtil->sanitizeSlug( $arrSection['strPageSlug'] );
			$arrSection['strTabSlug'] = $this->oUtil->sanitizeSlug( $arrSection['strTabSlug'] );
			
			if ( ! isset( $arrSection['strSectionID'], $arrSection['strPageSlug'] ) ) continue;	// these keys are necessary.
			
			// If the page slug does not match the current loading page, there is no need to register form sections and fields.
			if ( $GLOBALS['pagenow'] != 'options.php' && ! $strCurrentPageSlug || $strCurrentPageSlug !=  $arrSection['strPageSlug'] ) continue;				

			// If the custom condition is set and it's not true, skip.
			if ( ! $arrSection['fIf'] ) continue;
			
			// If the access level is set and it is not sufficient, skip.
			$arrSection['strCapability'] = isset( $arrSection['strCapability'] ) ? $arrSection['strCapability'] : $this->oProps->strCapability;
			if ( ! current_user_can( $arrSection['strCapability'] ) ) continue;	// since 1.0.2.1
			
			$this->oProps->arrSections[ $arrSection['strSectionID'] ] = $arrSection;		
			
		}	
	}
	protected function removeSettignSections() {	// since 1.1.0
		
		// Removes the given section by section ID.
		// Usage: $this->removeSettignSections( 'sactionID_A', 'sactionID_B', 'sactionID_C',... );

		foreach( func_get_args() as $strSectionID ) 
			if ( isset( $this->oProps->arrSections[ $strSectionID ] ) )
				unset( $this->oProps->arrSections[ $strSectionID ] );
		
	}
	protected function addSettingFields() {	// since 1.1.0
	
		// This method just adds the given field array items into the field array property. 
		// The actual registration will be done with the registerSetction() method.
	
		foreach( func_get_args() as $arrField ) {
			
			if ( ! is_array( $arrField ) ) continue;
			
			$arrField = $arrField + self::$arrStructure_Field;	// avoid undefined index warnings.
			
			// Sanitize the IDs since they are used as a callback method name.
			$arrField['strFieldID'] = $this->oUtil->sanitizeSlug( $arrField['strFieldID'] );
			$arrField['strSectionID'] = $this->oUtil->sanitizeSlug( $arrField['strSectionID'] );
			
			// Check the mandatory keys' values are set.
			if ( ! isset( $arrField['strFieldID'], $arrField['strSectionID'], $arrField['strType'] ) ) continue;	// these keys are necessary.
			
			// If the custom condition is set and it's not true, skip.
			if ( ! $arrField['fIf'] ) continue;			
			
			// If the access level is not sufficient, skip.
			$arrField['strCapability'] = isset( $arrField['strCapability'] ) ? $arrField['strCapability'] : $this->oProps->strCapability;
			if ( ! current_user_can( $arrField['strCapability'] ) ) continue; 
					
			// If it's the image type field, extra jQuery scripts need to be loaded.
			if ( $arrField['strType'] == 'image' ) $this->addImageFieldScript( $arrField );
					
			$this->oProps->arrFields[ $arrField['strFieldID'] ] = $arrField;
						
		}
	}
	private function addImageFieldScript( &$arrField ) {
					
		// These two hooks should be enabled when the image field type is added in the field array.
		$this->oProps->strThickBoxTitle = isset( $arrField['strTickBoxTitle'] ) ? $arrField['strTickBoxTitle'] : __( 'Upload Image', 'admin-page-framework' );
		$this->oProps->strThickBoxButtonUseThis = isset( $arrField['strLabelUseThis'] ) ? $arrField['strLabelUseThis'] : __( 'Use This Image', 'admin-page-framework' ); 

		if ( $this->fIsImageFieldScriptEnqueued	) return;
		$this->fIsImageFieldScriptEnqueued = true;
		
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueUploaderScripts' ) );	// called later than the admin_menu hook
		add_filter( 'gettext', array( $this, 'replaceThickBoxText' ) , 1, 2 );	
		
		// Append the script
		$this->oProps->strScript .= "
			jQuery( document ).ready( function( $ ){
				$( '.select_image' ).click( function() {
					pressed_id = $( this ).attr( 'id' );
					field_id = pressed_id.substring( 13 );	// remove the select_image_ prefix
					tb_show('{$this->oProps->strThickBoxTitle}', 'media-upload.php?referer={$this->oProps->strOptionKey}&amp;button_label={$this->oProps->strThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true&amp;post_id=0', false );
					return false;	// do not click the button after the script by returning false.
				});
				window.send_to_editor = function( html ) {
					var image_url = $( 'img',html ).attr( 'src' );
					$( '#' + field_id ).val( image_url );	// sets the image url in the main text field.
					tb_remove();	// close the thickbox
					$( '#image_preview_' + field_id ).attr( 'src', image_url );	// updates the preview image
					$( '#image_preview_' + field_id ).show()	// updates the visibility
				}
			});";
			
	}
			 	
	protected function removeSettignFields() {	// since 1.1.0
		
		// Removes the given fields by section ID.
		// Usage: $this->removeSettignFields( 'fieldID_A', 'fieldID_B', 'fieldID_C',... );
		
		foreach( func_get_args() as $strFieldID ) 
			if ( isset( $this->oProps->arrFields[ $strFieldID ] ) )
				unset( $this->oProps->arrFields[ $strFieldID ] );

	}
	
	/*
	 * Back-end methods
	 * */
	protected function doValidationCall( $strMethodName, $arrInput ) {	// since 1.1.0
		
		$strTabSlug = isset( $_POST['strTabSlug'] ) ? $_POST['strTabSlug'] : '';	// no need to retrieve the default tab slug here because it's an embedded value that is already set in the previous page. 
		$strPageSlug = isset( $_POST['strPageSlug'] ) ? $_POST['strPageSlug'] : '';

		// Check if custom submit keys are set.
		if ( isset( $_POST['__import']['submit'], $_FILES['__import'] ) ) 
			return $this->importOptions( $arrInput, $strPageSlug, $strTabSlug );
		if ( isset( $_POST['__export']['submit'] ) ) 
			die( $this->exportOptions( $this->oProps->arrOptions, $strPageSlug, $strTabSlug ) );
		if ( isset( $_POST['__link'] ) && $strLinkURL = $this->getPressedCustomSubmitButton( $_POST['__link'] ) )
			$this->oUtil->goRedirect( $strLinkURL );	// if the associated submit button for the link is pressed, the will be redirected.
		if ( isset( $_POST['__redirect'] ) && $strRedirectURL = $this->getPressedCustomSubmitButton( $_POST['__redirect'] ) )
			$this->setRedirectTransients( $strRedirectURL );
				
		// Apply validation filters - validation_{page slug}_{tab slug}, validation_{page slug}, validation_{instantiated class name}
		$arrInput = $this->getFilteredOptions( $arrInput, $strPageSlug, $strTabSlug );
		
		// Set the update notice
		$fEmpty = empty( $arrInput );
		add_settings_error( 
			$this->oProps->strOptionKey, 
			$strPageSlug, 
			$fEmpty ? $this->oMsg->___( 'option_cleared' ) : $this->oMsg->___( 'option_updated' ),
			$fEmpty ? 'error' : 'updated' 
		);
		
		// Return the input array merged with the original saved options so that other page's data will not be lost.
		return $arrInput;	
		
	}
	
	private function setRedirectTransients( $strURL ) {
		
		set_transient( "redirect_{$this->oProps->strClassName}_{$_POST['strPageSlug']}", $strURL , 60*2 );
		
	}
	
	private function getPressedCustomSubmitButton( $arrPostElements ) {	// since 1.1.0
	
		// Checks if the associated submit button is pressed with the input fields whose name property starts with __link or __redirect. 
		// The custom ( currently __link or __redirect is supported ) input array should contain the 'name' and 'url' keys and their values.
		// Returns null if no button is found and the associated link url if found.
		
		foreach( $arrPostElements as $strFieldName => $arrSubElements ) {
			
			/*
			 * $arrSubElements['name']	- the input field name property of the submit button, delimited by pipe (|) e.g. APF_GettingStarted|first_page|submit_buttons|submit_button_link
			 * $arrSubElements['url']	- the URL to redirect to. e.g. http://www.somedomain.com
			 * */
			$arrNameKeys = explode( '|', $arrSubElements['name'] );
			
			// Count of 4 means it's a single element. Count of 5 means it's one of multiple elements.
			if ( count( $arrNameKeys ) == 4 && isset( $_POST[ $arrNameKeys[0] ][ $arrNameKeys[1] ][ $arrNameKeys[2] ][ $arrNameKeys[3] ] ) )
				return $arrSubElements['url'];
			if ( count( $arrNameKeys ) == 5 && isset( $_POST[ $arrNameKeys[0] ][ $arrNameKeys[1] ][ $arrNameKeys[2] ][ $arrNameKeys[3] ][ $arrNameKeys[4] ] ) )
				return $arrSubElements['url'];
				
		}
		
		return null;	// not found
		
	}

	private function importOptions( $arrInput, $strPageSlug, $strTabSlug ) {	// since 1.1.0
	
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
	private function exportOptions( $vData, $strPageSlug, $strTabSlug ) {	// since 1.1.0

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
	private function getFilteredOptions( $arrInput, $strPageSlug, $strTabSlug ) {	// since 1.1.0

		$arrStoredPageOptions = $this->getPageOptions( $strPageSlug ); 			

		// for tab
		if ( $strTabSlug && $strPageSlug )	{
			$arrRegisteredSectionKeysForThisTab = array_keys( $arrInput );
			$arrInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$strPageSlug}_{$strTabSlug}", $arrInput, $arrStoredPageOptions );	
			$arrInput = $this->oUtil->uniteArraysRecursive( $arrInput, $this->getOtherTabOptions( $strPageSlug, $arrRegisteredSectionKeysForThisTab ) );
		}
		// for page	
		if ( $strPageSlug )	{
			$arrInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$strPageSlug}", $arrInput, $arrStoredPageOptions );		
			$arrInput = $this->oUtil->uniteArraysRecursive( $arrInput, $this->getOtherPageOptions( $strPageSlug ) );
		}
		// for class
		$arrInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProps->strClassName}", $arrInput, $this->oProps->arrOptions );

		return $arrInput;
	
	}	
	private function getPageOptions( $strPageSlug ) {	// since 1.1.0
		
		// Returns the stored options of the given page slug. Other pages' option data will not be contained in the returning array.
		// This is used to pass the old option array to the validation callback method.
		
		$arrStoredPageOptions = array();
		if ( isset( $this->oProps->arrOptions[ $strPageSlug ] ) )
			$arrStoredPageOptions[ $strPageSlug ] = $this->oProps->arrOptions[ $strPageSlug ];
		
		return $arrStoredPageOptions;
		
	}
	private function getOtherTabOptions( $strPageSlug, $arrSectionKeysForTheTab ) {	// since 1.1.0
	
		// Returns the stored options excluding the currently specified tab's sections and their fields.
		// This is used to merge the submitted form data with the previously stored option data of the form elements 
		// that belong to the in-page tab of the given page.
		
		$arrOtherTabOptions = array();
		if ( isset( $this->oProps->arrOptions[ $strPageSlug ] ) )
			$arrOtherTabOptions[ $strPageSlug ] = $this->oProps->arrOptions[ $strPageSlug ];
			
		// Remove the elements of the given keys so that the other stored elements will remain. 
		// They are the other form section elements which need to be returned.
		foreach( $arrSectionKeysForTheTab as $arrSectionKey ) 
			unset( $arrOtherTabOptions[ $strPageSlug ][ $arrSectionKey ] );
			
		return $arrOtherTabOptions;
		
	}
	
	private function getOtherPageOptions( $strPageSlug ) {	// since 1.1.0
	
		// Returns the stored options excluding the key of the given page slug. This is used to merge the submitted form input data with the 
		// previously stored option data except the given page.
		
		$arrOtherPageOptions = $this->oProps->arrOptions;
		if ( isset( $arrOtherPageOptions[ $strPageSlug ] ) )
			unset( $arrOtherPageOptions[ $strPageSlug ] );
		return $arrOtherPageOptions;
		
	}
	
	protected function renderSettingField( $strFieldID, $strPageSlug ) {
		
		// @protected:  cannot be private because it's called from an extended class
		
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
	private function getFieldErrors( $strPageSlug ) {
		
		// If a form submit button is not pressed, there is no need to set the setting errors.
		if ( ! isset( $_GET['settings-updated'] ) ) return null;
		
		// Find the transient.
		$strTransient = md5( $this->oProps->strClassName . '_' . $strPageSlug );
		$arrFieldErrors = get_transient( $strTransient );
		delete_transient( $strTransient );	
		return $arrFieldErrors;

	}
	protected function setFieldErrors( $arrErrors, $strID=null, $numSavingDuration=300 ) {	// since 1.0.3
		
		// Saves the given array in a temporary area of the option database table.
		// $strID should be the page slug of the page that has the dealing form filed.
		// $arrErrors should be constructed as the $_POST array submitted to the Settings API.
		// $numSavingDuration is 300 by default which is 5 minutes ( 60 seconds * 5 ).
		
		$strID = isset( $strID ) ? $strID : ( isset( $_POST['strPageSlug'] ) ? $_POST['strPageSlug'] : ( isset( $_GET['page'] ) ? $_GET['page'] : $this->oProps->strClassName ) );	
		
		// Store the error array in the transient with the name of a MD5 hash string that consists of the extended class name + _ + page slug.
		set_transient( md5( $this->oProps->strClassName . '_' . $strID ), $arrErrors, $numSavingDuration );	// store it for 5 minutes ( 60 seconds * 5 )
	
	}

	protected function renderSectionDescription( $strMethodName ) {		
		
		// @protected:  cannot be private because it's called from an extended class
		// Renders the section description and apply the filter to be extensible.
		// This is redirected from __call() and the callback for the section description method.
		$strSectionID = substr( $strMethodName, strlen( 'section_pre_' ) );	// X will be the section ID in section_pre_X
		
		if ( ! isset( $this->oProps->arrSections[ $strSectionID ] ) ) return;	// if it is not added

		echo  $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $strFilter, $vInput, $vArgs...
			$this,
			$this->oProps->strClassName . '_' .  self::$arrPrefixesForCallbacks['section_'] . $strSectionID,	// class name + _ + section_ + section id
			'<p>' . $this->oProps->arrSections[ $strSectionID ]['strDescription'] . '</p>',	 // the p-tagged description string
			$this->oProps->arrSections[ $strSectionID ]['strDescription']	// the original description
		);		
			
	}
	private function getPageSlugBySectionID( $strSectionID ) {
		
		// Retrieves the page slug that the settings section belongs.		
		return isset( $this->oProps->arrSections[ $strSectionID ]['strPageSlug'] )
			? $this->oProps->arrSections[ $strSectionID ]['strPageSlug']
			: null;
				
	}
	
	/*
	 * WordPress Settings API wrapper class.
	 * */
	public function registerSettings() {	// since 1.1.0. Callback method.
		
		// Format ( sanitize ) the section and field arrays.
		$this->oProps->arrSections = $this->formatSectionArrays( $this->oProps->arrSections );
		$this->oProps->arrFields = $this->formatFieldArrays( $this->oProps->arrFields );
				
		// If there is no section or field to add, do nothing.
		if ( 
			$GLOBALS['pagenow'] != 'options.php'
			&& ( count( $this->oProps->arrSections ) == 0 || count( $this->oProps->arrFields ) == 0 ) 
		) return;
				
		// Register settings sections 
		uasort( $this->oProps->arrSections, array( $this->oProps, 'sortByOrder' ) ); 
		foreach( $this->oProps->arrSections as $arrSection ) 
			add_settings_section(	// Add the given section
				$arrSection['strSectionID'],	//  section ID
				"<a id='{$arrSection['strSectionID']}'></a>" . $arrSection['strTitle'],		// title - place the anchor in front of the title.
				array( $this, 'section_pre_' . $arrSection['strSectionID'] ), 				// callback function -  this will trigger the __call() magic method.
				$arrSection['strPageSlug']	// page
			);
		
		// Register settings fields
		uasort( $this->oProps->arrFields, array( $this->oProps, 'sortByOrder' ) ); 
		foreach( $this->oProps->arrFields as $arrField ) 
			add_settings_field(	// Add the given field.
				$arrField['strFieldID'],
				"<a id='{$arrField['strFieldID']}'></a><span title='{$arrField['strTip']}'>{$arrField['strTitle']}</span>",
				array( $this, 'field_pre_' . $arrField['strFieldID'] ),	// callback function - will trigger the __call() magic method.
				$this->getPageSlugBySectionID( $arrField['strSectionID'] ), // page slug
				$arrField['strSectionID'],	// section
				$arrField['strFieldID']		// arguments - pass the field ID to the callback function
			);	

		// Set the form enabling flag so that the <form></form> tag will be inserted in the page.
		$this->oProps->fEnableForm = true;
		register_setting(	
			$this->oProps->strOptionKey,	// the option group name.	
			$this->oProps->strOptionKey,	// the option key name that will be stored in the option table in the database.
			array( $this, 'validation_pre_' . $this->oProps->strClassName )	// validation method
		); 
		
	}
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
	private function formatFieldArrays( $arrFields ) {
		
		// Apply filters to let other scripts to add fields.
		$arrFields = $this->oUtil->addAndApplyFilter(	// Parameters: $oCallerObject, $arrFilters, $vInput, $vArgs...
			$this,
			"{$this->oProps->strClassName}_setting_fields",
			$arrFields
		); 
		
		// Apply the conditions to remove unnecessary elements and put new orders.
		$arrNewFieldArray = array();
		foreach( $arrFields as $arrField ) {
		
			if ( ! is_array( $arrField ) ) continue;		// the element must be an array.
			
			$arrField = $arrField + self::$arrStructure_Field;	// avoid undefined index warnings.
			
			// Sanitize the IDs since they are used as a callback method name.
			$arrField['strFieldID'] = $this->oUtil->sanitizeSlug( $arrField['strFieldID'] );
			$arrField['strSectionID'] = $this->oUtil->sanitizeSlug( $arrField['strSectionID'] );
			
			// Check the mandatory keys' values.
			if ( ! isset( $arrField['strFieldID'], $arrField['strSectionID'], $arrField['strType'] ) ) continue;	// these keys are necessary.
			
			// If the access level is not sufficient, skip.
			$arrField['strCapability'] = isset( $arrField['strCapability'] ) ? $arrField['strCapability'] : $this->oProps->strCapability;
			if ( ! current_user_can( $arrField['strCapability'] ) ) continue; 
						
			// If the condition is not met, skip.
			if ( $arrField['fIf'] !== true ) continue;
						
			// Set the order.
			$arrField['numOrder']	= is_numeric( $arrField['numOrder'] ) ? $arrField['numOrder'] : count( $arrNewFieldArray ) + 10;
			
			// Set the tip, option key, instantiated class name, and page slug elements.
			$arrField['strTip'] = strip_tags( isset( $arrField['strTip'] ) ? $arrField['strTip'] : $arrField['strDescription'] );
			$arrField['strOptionKey'] = $this->oProps->strOptionKey;
			$arrField['strClassName'] = $this->oProps->strClassName;
			$arrField['strPageSlug'] = isset( $_GET['page'] ) ? $_GET['page'] : null;
			
			// Add the element to the new returning array.
			$arrNewFieldArray[ $arrField['strFieldID'] ] = $arrField;
				
		}
		return $arrNewFieldArray;
		
	}
	
	/*
	 *	Callbacks 
	 * */
	public function enqueueUploaderScripts() {
			
		wp_enqueue_script('jquery');			
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');				
		wp_enqueue_script('media-upload');
	
	} 
	public function replaceThickBoxText( $strTranslated, $strText ) {	// called from a filter so do not protect

	
		// Replace the button label in the media thick box.
		if ( ! in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php' ) ) ) return $strTranslated;
		if ( $strText != 'Insert into Post' ) return $strTranslated;
		if ( $this->oUtil->getQueryValueInURLByKey( wp_get_referer(), 'referer' ) != $this->oProps->strOptionKey ) return $strTranslated;
		
		if ( isset( $_GET['button_label'] ) ) return $_GET['button_label'];

		return $this->oProps->strThickBoxButtonUseThis ?  $this->oProps->strThickBoxButtonUseThis : __( 'Use This Image', 'admin-page-framework' );
		
	}
}
endif; 

if ( ! class_exists( 'AdminPageFramework' ) ) :
abstract class AdminPageFramework extends AdminPageFramework_SettingsAPI {
	
	protected $oProps;	// The common properties shared among sub-classes.
	protected $oDebug;	// Provides the debug methods.
	
	public function __construct( $strOptionKey=null, $strCallerPath=null, $strCapability=null, $strTextDomain=null ){
		
		/*
		 * $strOptionKey :	Specifies the option key name to store in the option database table. 
		 * 					If this is set, all the options will be stored in an array to the key of this passed string.
		 * $strCallerPath :	used to retrieve the plugin/theme plugin data to auto-insert credit info into the footer.
		 * */
		 
		// Variables
		$strClassName = get_class( $this );
		
		// Objects
		$this->oProps = new AdminPageFramework_Properties( $strClassName, $strOptionKey, $strCapability );
		$this->oMsg = new AdminPageFramework_Messages( $strTextDomain );
		$this->oUtil = new AdminPageFramework_Utilities;
		$this->oDebug = new AdminPageFramework_Debug;
		$this->oLink = new AdminPageFramework_Link( $this->oProps, $strCallerPath );
								
		if ( is_admin() ) {
			
			// Disable the Settings API's admin notice.
			// add_action( 'admin_menu', array( $this, 'DisableSettingsAPIAdminNotice' ), 999 );
			
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

			// For the media uploader.
			// add_filter( 'gettext', array( $this, 'replaceThickBoxText' ) , 1, 2 );	
						
			// For earlier loading than $this->setUp
			$this->oUtil->addAndDoAction( $this, self::$arrPrefixes['start_'] . $this->oProps->strClassName );
		
		}
	}	
	public function __call( $strMethodName, $arrArgs=null ) {		
		
		/*
		 *  Undefined but called by the callback methods automatically inserted by the class will trigger this magic method, __call().
		 *  So determine which callback method triggered this and redirect the call to the appropriate method.
		 * */
		 
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
		
		// If it's one of the framework's callback methods, do nothing.	
		if ( $this->isFrameworkCallbackMethod( $strMethodName ) )
			return isset( $arrArgs[0] ) ? $arrArgs[0] : null;	// if $arrArgs[0] is set, it's a filter, otherwise, it's an action.
		
		// The callback of add_submenu_page() - render the page contents.
		if ( isset( $_GET['page'] ) && $_GET['page'] == $strMethodName ) $this->renderPage( $strMethodName, $strTabSlug );
						
	}	
	private function isFrameworkCallbackMethod( $strMethodName ) {

		if ( substr( $strMethodName, 0, strlen( "{$this->oProps->strClassName}_" ) ) == "{$this->oProps->strClassName}_" )	// e.g. {instantiated class name} + field_ + {field id}
			return true;
		
		foreach( self::$arrPrefixes as $strPrefix ) {
			if ( substr( $strMethodName, 0, strlen( $strPrefix ) )	== $strPrefix  ) 
				return true;
		}
	}
	
	 
	/*
	 *	Front-End methods - the user may call it but it should not necessarily be customized in the extended class. 
	 * */
	protected function addSubMenuItems() {
		foreach ( func_get_args() as $arrSubMenuItem ) 
			$this->addSubMenuItem( $arrSubMenuItem );		
	}
	protected function addSubMenuItem( $arrSubMenuItem ) {
		if ( isset( $arrSubMenuItem['strURL'] ) ) {
			$arrSubMenuLink = $arrSubMenuItem + $this->oLink->arrStructure_SubMenuLink;
			$this->oLink->addSubMenuLink(
				$arrSubMenuLink['strMenuTitle'],
				$arrSubMenuLink['strURL'],
				$arrSubMenuLink['strCapability'],
				$arrSubMenuLink['numOrder'],
				$arrSubMenuLink['fPageHeadingTab']
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
				$arrSubMenuPage['fPageHeadingTab']
			);				
		}
	}
	// protected function addSubMenuLinks() {
		// call_user_func_array( array( $this->oLink, 'addSubMenuLinks' ), func_get_args() );
	// }
	protected function addSubMenuLink( $strMenuTitle, $strURL, $strCapability=null, $numOrder=null, $fPageHeadingTab=true ) {
		$this->oLink->addSubMenuLink( $strMenuTitle, $strURL, $strCapability, $numOrder, $fPageHeadingTab );
	}
	
	public function addLinkToPluginDescription( $vLinks ) {
		
		// $vLinks : ( string or array ) e.g. <a href="http://www.google.com">Google</a>  or array( '<a href="http://www.google.com">Google</a>', '...' )
		$this->oLink->addLinkToPluginDescription( $vLinks );
		
	}
	public function addLinkToPluginTitle( $vLinks ) {
		
		// $vLinks : ( string or array ) e.g. <a href="http://www.google.com">Google</a>  or array( '<a href="http://www.google.com">Google</a>', '...' )
		$this->oLink->addLinkToPluginTitle( $vLinks );
		
	}
	 
	/*
	 * Methods for setting the access level
	 */
	public function setCapability( $strCapability ) {
		$this->oProps->strCapability = $strCapability;	
	}
	
	/*
	 * Back end methods
	 * */
	
	/* 
	 * Callback methods
	 */ 
	public function checkRedirects() {

		// So it's not options.php. Now check if it's one of the plugin's added page. If not, do nothing.
		if ( ! ( isset( $_GET['page'] ) ) || ! $this->oProps->isPageAdded( $_GET['page'] ) ) return; 
		
		// If the Settings API has not updated the options, do nothing.
		if ( ! ( isset( $_GET['settings-updated'] ) && ! empty( $_GET['settings-updated'] ) ) ) return;

		// Okay, it seems the submitted data have been updated successfully.
		$strTransient = "redirect_{$this->oProps->strClassName}_{$_GET['page']}";
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
			. $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['style_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, false ), self::$strDefaultStyle )
			. "</style>";
	}
	
	public function addScript() {
		
		$strPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
		$strTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->getDefaultInPageTab( $strPageSlug );
		
		// If the loading page has not been registered or not the plugin page which uses this library, do nothing.
		if ( ! $this->oProps->isPageAdded( $strPageSlug ) ) return;

		// Print out the filtered styles.
		echo "<script type='text/javascript' id='admin-page-framework-script'>"
			. $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['style_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, false ), $this->oProps->strScript )
			. "</script>";		
		
	}
}
endif;

if ( ! class_exists( 'AdminPageFramework_Messages' ) ) :
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

if ( ! class_exists( 'AdminPageFramework_Properties' ) ) :
class AdminPageFramework_Properties {
	
	/*
	 * Stores various values. This is used to encapsulate properties so that it helps to avoid naming conflicts.
	 * since 1.1.0
	 */
	
	// Strings
	public $strClassName;	// Stores framework's instantiated object name.
	public $strCapability = 'manage_options';	// Stores the access level to the root page. When sub pages are added and the capability value is not provided, this will be applied.
	public $strPageHeadingTabTag = 'h2';
	public $strInPageTabTag = 'h3';
	public $strDefaultPageSlug;	// Stores the default page slug.
	public $strScript;	// Stores the adding scripts.
	
	// Container arrays.
	public $arrPages = array();	// A two-dimensional array storing registering sub-menu(page) item information with keys of the page slug.
	public $arrRootMenu = array(	// Stores the root menu item information for one set root menu item.
		'strTitle' => null,				// menu label that appears on the menu list
		'strPageSlug' => null,				// menu slug that identifies the menu item
		'strPathIcon16x16' => null,		// the associated icon that appears beside the label on the list
		'intPosition'	=> null,		// determines the position of the menu
		'fCreateRoot' => null,			// indicates whether the framework should create the root menu or not.
	); 
	public $arrInPageTabs = array();				// Stores in-page tabs.
	public $arrDefaultInPageTabs = array();			// Stores the default tab.
	public $arrPluginDescriptionLinks = array(); 	// Stores link text that is scheduled to be embedded in the plugin listing table's description column cell.
	public $arrPluginTitleLinks = array();			// Stores link text that is scheduled to be embedded in the plugin listing table's title column cell.
	
	// Settings API
	// public $arrOptions;			// Stores the framework's options. Do not even declare the property here because the __get() magic method needs to be triggered when it accessed for the first time.
	public $strOptionKey = '';		// the instantiated class name will be assigned in the constructor if the first parameter is not set.
	public $arrSections = array();	// Stores option sections.
	public $arrFields = array();	// Stores option fields
	public $strFormEncType = 'multipart/form-data';	// Set one of the followings: application/x-www-form-urlencoded, multipart/form-data, text/plain
	public $fEnableForm = false;			// Decides whether the setting form tag is rendered or not.	This will be enabled when a settings section and a field is added.
	
	// Flags
	public $fShowPageTitle = true;		// indicates whether the page title should be displayed.
	public $fShowPageHeadingTabs = true;	// indicates whether the page heading tabs should be displayed.
	
	public function __construct( $strClassName, $strOptionKey, $strCapability='manage_options' ) {
		
		$this->strClassName = $strClassName;		
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
	public function isPageAdded( $strPageSlug ) {
	
		// Returns true if the given page slug is one of the pages added by the framework.
		if ( array_key_exists( trim( $strPageSlug ), $this->arrPages ) ) return true; 
	
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
	
	public function sortByOrder( $a, $b ) {	// since 1.1.0 - a callback method for uasort()
		return $a['numOrder'] - $b['numOrder'];
	}		
}
endif;

if ( ! class_exists( 'AdminPageFramework_CustomSubmitFields' ) ) :
abstract class AdminPageFramework_CustomSubmitFields {

	/*
	 * This class provides helper functions that deal with custom submit fields and require to retrieve custom key elements.
	 * The classes that extend this include ExportOptions, ImportOptions, and Redirect.
	 * */
	 
	public function __construct( $arrPostElement ) {
		
		$this->arrPostElement = $arrPostElement;	// e.g. $_POST['__import'] or $_POST['__export'] or $_POST['__redirect']
		
	}
	
	protected function getElement( $arrElement, $arrElementKey, $strElementKey='format' ) {
		
		// This methods returns the value of the specified element key.
		// The element key is either a single key or two keys. The two keys means that the value is stored in the second dimension.
		
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
			
		// Something wrong happened.
		return 'ERROR_C';
		
	}	
	protected function getElementKey( $arrElement, $strFirstDimensionKey ) {
		
		// This method returns an array consisting of two values. 
		// The first element is the fist dimension's key and the second element is the second dimension's key.
		
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
		
		if ( isset( $this->strFiledID ) && $this->strFiledID  ) return $this->strFiledID;
		
		// Only the pressed element will be stored in the array.
		foreach( $this->arrPostElement['submit'] as $strKey => $v ) {	// $this->arrPostElement should have been set in the constructor.
			$this->strFieldID = $strKey;
			return $this->strFieldID;
		}
	}	
		
}
endif;

if ( ! class_exists( 'AdminPageFramework_ImportOptions' ) ) :
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

	/* e.g.
	 * <input type="hidden" name="__export[export_sinble][file_name]" value="APF_GettingStarted_20130708.txt">
	 * <input type="hidden" name="__export[export_sinble][format]" value="json">
	 * <input id="export_and_import_export_sinble_0" 
	 *  type="submit" 
	 *  name="__export[submit][export_sinble]" 
	 *  value="Export Options">
	*/
	public function doExport( $vData, $strFileName=null, $strFormatType=null ) {
		
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
abstract class AdminPageFramework_LinkBase extends AdminPageFramework_Utilities {
	
	private static $arrStructure_CallerInfo = array(
		'strPath'			=> null,
		'strType'			=> null,
		'strName'			=> null,		
		'strVersion'		=> null,
		'strThemeURI'		=> null,
		'strScriptURI'		=> null,
		'strAuthorURI'		=> null,
		'strAuthor'			=> null,
	);	
	/*
	 * Methods for getting script info.
	 */ 
	protected function getCallerInfo( $strCallerPath=null ) {
		
		// Attempts to retrieve the caller script information whether it's a theme or plugin or something else.
		// The information can be used to embed into the footer etc.
		
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
				'strScriptURI'		=> $oTheme->get( 'ThemeURI' ),
				'strAuthorURI'		=> $oTheme->get( 'AuthorURI' ),
				'strAuthor'			=> $oTheme->get( 'Author' ),				
			) + $arrCallerInfo;	
		}
	}
	protected function getCallerType( $strScriptPath ) {	// since 1.0.2.2

		// Determines what kind of script this is, theme, plugin or something else from the given path.
		// Returns either 'theme', 'plugin', or 'unknown'
		
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
}
endif;

if ( ! class_exists( 'AdminPageFramework_LinkForPostType' ) ) :
class AdminPageFramework_LinkForPostType extends AdminPageFramework_LinkBase {
	
	public function __construct( $strPostTypeSlug, $strCallerPath=null ) {
		
		if ( ! is_admin() ) return;
		
		$this->strPostTypeSlug = $strPostTypeSlug;
		$this->strCallerPath = file_exists( $strCallerPath ) ? $strCallerPath : $this->getCallerPath();
		$this->arrScriptInfo = $this->getCallerInfo( $this->strCallerPath ); 
				
		// Add script info into the footer 
		add_filter( 'update_footer', array( $this, 'addInfoInFooterRight' ), 11 );
		add_filter( 'admin_footer_text' , array( $this, 'addInfoInFooterLeft' ) );	
		
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
	public function addPostTypeQueryInEditPostLink( $strURL, $intPostID=null, $strContext=null ) {
		
		// This adds the post_type query key and value in the link url so that in the linked page, the framework will determine the post type
		// and can embed footer links automatically.
		// e.g. http://.../wp-admin/post.php?post=180&action=edit -> http://.../wp-admin/post.php?post=180&action=edit&post_type=[...]
		return add_query_arg( array( 'post' => $intPostID, 'action' => 'edit', 'post_type' => $this->strPostTypeSlug ), $strURL );
	
	}	
	public function addSettingsLinkInPluginListingPage( $arrLinks ) {
		
		// http://.../wp-admin/edit.php?post_type=[...]
		array_unshift(	
			$arrLinks,
			"<a href='edit.php?post_type={$this->strPostTypeSlug}'>" . __( 'Manage', 'admin-page-framework' ) . "</a>"
		); 
		return $arrLinks;		
		
	}
	public function addInfoInFooterLeft( $strLinkHTML='' ) {
		
		// The callback for the filter hook, admin_footer_text.
		if ( ! isset( $_GET['post_type'] ) ||  $_GET['post_type'] != $this->strPostTypeSlug )
			return $strLinkHTML;	// $strLinkHTML is given by the hook.

		if ( empty( $this->arrScriptInfo['strName'] ) ) return $strLinkHTML;
		
		$strPluginInfo = $this->arrScriptInfo['strName'];
		$strPluginInfo .= empty( $this->arrScriptInfo['strVersion'] ) ? '' : ' ' . $this->arrScriptInfo['strVersion'];
		$strPluginInfo = empty( $this->arrScriptInfo['strScriptURI'] ) ? $strPluginInfo : '<a href="' . $this->arrScriptInfo['strScriptURI'] . '" target="_blank">' . $strPluginInfo . '</a>';
		$strAuthorInfo = empty( $this->arrScriptInfo['strAuthorURI'] )	? $this->arrScriptInfo['strAuthor'] : '<a href="' . $this->arrScriptInfo['strAuthorURI'] . '" target="_blank">' . $this->arrScriptInfo['strAuthor'] . '</a>';
		$strAuthorInfo = empty( $this->arrScriptInfo['strAuthor'] ) ? $strAuthorInfo : 'by ' . $strAuthorInfo;
		return $strPluginInfo . ' ' . $strAuthorInfo;			

	}
	public function addInfoInFooterRight( $strLinkHTML='' ) {
		
		if ( ! isset( $_GET['post_type'] ) ||  $_GET['post_type'] != $this->strPostTypeSlug )
			return $strLinkHTML;	// $strLinkHTML is given by the hook.
			
		return __( 'Powered by', 'admin-page-framework' ) . '&nbsp;' 
			. '<a href="http://wordpress.org/extend/plugins/admin-page-framework/">Admin Page Framework</a>'
			. ', <a href="http://wordpress.org">WordPress</a>';
			
	}
}
endif;
 
if ( ! class_exists( 'AdminPageFramework_Link' ) ) :
class AdminPageFramework_Link extends AdminPageFramework_LinkBase {

	/*
	 * Embeds links in the footer and plugin's listing table etc.
	 */
	
	private $strCallerPath;	// Stores the caller script path.
	private $oProps;	// the property object, commonly shared.
	
	public function __construct( &$oProps, $strCallerPath=null ) {
		
		if ( ! is_admin() ) return;
		
		$this->oProps = $oProps;
		$this->strCallerPath = file_exists( $strCallerPath ) ? $strCallerPath : $this->getCallerPath();
		$this->oProps->arrScriptInfo = $this->getCallerInfo( $this->strCallerPath ); 
		
		// Add script info into the footer 
		add_filter( 'update_footer', array( $this, 'addInfoInFooterRight' ), 11 );
		add_filter( 'admin_footer_text' , array( $this, 'addInfoInFooterLeft' ) );	
	
		if ( $this->oProps->arrScriptInfo['strType'] == 'plugin' )
			add_filter( 'plugin_action_links_' . plugin_basename( $this->oProps->arrScriptInfo['strPath'] ) , array( $this, 'addSettingsLinkInPluginListingPage' ) );

	}
		
	/*
	 * Methods for adding menu links.
	 * */
	public $arrStructure_SubMenuLink = array(		// public as this is accessed from the extended class
		'strMenuTitle' => null,
		'strURL' => null,
		'strCapability' => null,
		'numOrder' => null,
		'strType' => 'link',
		'fPageHeadingTab' => true,
	
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
	public function addSubMenuLink( $strMenuTitle, $strURL, $strCapability=null, $numOrder=null, $fPageHeadingTab=true ) {
		
		$intCount = count( $this->oProps->arrPages );
		$this->oProps->arrPages[ $strURL ] = array(  
			'strMenuTitle'		=> $strMenuTitle,
			'strPageTitle'		=> $strMenuTitle,	// used for the page heading tabs.
			'strURL'			=> $strURL,
			'strType'			=> 'link',	// this is used to compare with the 'page' type.
			'strCapability'		=> isset( $strCapability ) ? $strCapability : $this->oProps->strCapability,
			'numOrder'			=> is_numeric( $numOrder ) ? $numOrder : $intCount + 10,
			'fPageHeadingTab'	=> $fPageHeadingTab,
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
	public function addInfoInFooterLeft( $strLinkHTML='' ) {

		// The callback for the filter hook, admin_footer_text.
		if ( ! isset( $_GET['page'] ) || ! $this->oProps->isPageAdded( $_GET['page'] )  ) 
			return $strLinkHTML;	// $strLinkHTML is given by the hook.
		
		if ( empty( $this->oProps->arrScriptInfo['strName'] ) ) return $strLinkHTML;
		
		$strPluginInfo = $this->oProps->arrScriptInfo['strName'];
		$strPluginInfo .= empty( $this->oProps->arrScriptInfo['strVersion'] ) ? '' : ' ' . $this->oProps->arrScriptInfo['strVersion'];
		$strPluginInfo = empty( $this->oProps->arrScriptInfo['strScriptURI'] ) ? $strPluginInfo : '<a href="' . $this->oProps->arrScriptInfo['strScriptURI'] . '" target="_blank">' . $strPluginInfo . '</a>';
		$strAuthorInfo = empty( $this->oProps->arrScriptInfo['strAuthorURI'] )	? $this->oProps->arrScriptInfo['strAuthor'] : '<a href="' . $this->oProps->arrScriptInfo['strAuthorURI'] . '" target="_blank">' . $this->oProps->arrScriptInfo['strAuthor'] . '</a>';
		$strAuthorInfo = empty( $this->oProps->arrScriptInfo['strAuthor'] ) ? $strAuthorInfo : 'by ' . $strAuthorInfo;
		return $strPluginInfo . ' ' . $strAuthorInfo;			

	}
	public function addInfoInFooterRight( $strLinkHTML='' ) {
		
		if ( ! isset( $_GET['page'] ) || ! $this->oProps->isPageAdded( $_GET['page'] )  ) 
			return $strLinkHTML;	// $strLinkTHML is given by the hook.
			
		return __( 'Powered by', 'admin-page-framework' ) . '&nbsp;' 
			. '<a href="http://wordpress.org/extend/plugins/admin-page-framework/">Admin Page Framework</a>'
			. ', <a href="http://wordpress.org">WordPress</a>';
			
	}
	
	public function addSettingsLinkInPluginListingPage( $arrLinks ) {
	
		array_unshift(	
			$arrLinks,
			'<a href="admin.php?page=' . $this->oProps->strDefaultPageSlug . '">' . __( 'Settings', 'admin-page-framework' ) . '</a>'
		); 
		return $arrLinks;
		
	}		
	
	public function addLinkToPluginDescription_Callback( $arrLinks, $strFile ) {

		if ( $strFile != plugin_basename( $this->oProps->arrScriptInfo['strPath'] ) ) return $arrLinks;
		return array_merge( $arrLinks, $this->oProps->arrPluginDescriptionLinks );
		
	}			
	public function addLinkToPluginTitle_Callback( $arrLinks ) {
		
		return array_merge( $arrLinks, $this->oProps->arrPluginTitleLinks );
	
	}		
}
endif;

if ( ! class_exists( 'AdminPageFramework_Debug' ) ) :
class AdminPageFramework_Debug {
	
	public function getArray( $arr, $strFilePath=null ) {
		
		if ( $strFilePath ) {
			file_put_contents( 
				$strFilePath , 
				date( "Y/m/d H:i:s" ) . PHP_EOL
				. print_r( $arr, true ) . PHP_EOL . PHP_EOL
				, FILE_APPEND 
			);					
		}
		return '<pre>' . esc_html( print_r( $arr, true ) ) . '</pre>';
		
	}	
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputField' ) ) :
class AdminPageFramework_InputField extends AdminPageFramework_Utilities {
	
	private static $arrDefaultFieldValues = array(
		'vValue' => null,			// ( array or string ) this suppress the default key value. This is useful to display the value saved in a custom place other than the framework automatically saves.
		'vDefault' => null,			// ( array or string )
		'vClassAttribute' => null,	// ( array or string ) the class attribute of the input field. Do not set an empty value here, but null because the submit field type uses own default value.
		'vLabel' => '',				// ( array or string ) labels for some input fields. Do not set null here because it is casted as string in the field output methods, which creates an element of empty string so that it can be iterated with foreach().
		'vLabelMinWidth' => 120,	// ( array or integer ) This sets the min-width of the label tag for the textarea, text, and numbers input types.
		'vDisable' => null,			// ( array or string ) This value indicates whether the set field is disabled or not. 
		'vDelimiter' => null,		// do not set an empty value here because the radio input field uses own default value.
		'vReadOnly' => '',			// ( array or string ) sets the readonly attribute to text and textarea input fields.
		'vBeforeInputTag' => '',
		'vAfterInputTag' => '',
		'vSize' => null,			// ( array or integer )	This is for the text fild type including custom image field. Do not set a value here.
		'vRows' => 4,				// ( array or integer ) This is for the textarea field type.
		'vCols' => 80,				// ( array or integer ) This is for the textarea field type.
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
		'vImagePreview' => null,	// ( array or string )	This is for the image filed type. For array, each element should contain a boolean value ( true/false ).
		'strTickBoxTitle' => null,	// ( string ) This is for the image field type.
		'strLabelUseThis' => null,	// ( string ) This is for the image field type.
		'vTaxonomySlug' => 'category',	// ( string ) This is for the taxonomy field type.
		'arrRemove' => array( 'revision', 'attachment', 'nav_menu_item' ), // for the posttype checklist field type
		'numMaxWidth' => 400,	// for the taxonomy checklist filed type.
		'numMaxHeight' => 200,	// for the taxonomy checklist filed type.		
		
		// Mandatory keys.
		'strFieldID' => null,		
		
		// For the meta box class - it does not require the following keys so these helps to avoid undefined index warinings.
		'strPageSlug' => null,
		'strSectionID' => null,
		'strBeforeField' => null,
		'strAfterField' => null,
		
	);
	
	public function __construct( &$arrField, &$arrOptions, &$arrErrors, &$oMsg ) {
			
		$this->oMsg = $oMsg;
		
		$this->arrField = $arrField + self::$arrDefaultFieldValues;
		$this->arrOptions = $arrOptions;
		$this->arrErrors = $arrErrors;
			
		$this->strFieldName = $this->getInputFieldName();
		$this->strTagID = $this->getInputTagID( $arrField );
		$this->vValue = $this->getInputFieldValue( $arrField, $arrOptions );
		
	}	
		
	private function getInputFieldName( $arrField=null ) {	// since 1.0.4, moved from GetFormFieldsByType()
		
		$arrField = isset( $arrField ) ? $arrField : $this->arrField;
		
		// If the name key is explicitly set, use it
		if ( ! empty( $arrField['strName'] ) ) return $arrField['strName'];
		
		return isset( $arrField['strOptionKey'] ) // the meta box class does not use the option key
			? "{$arrField['strOptionKey']}[{$arrField['strPageSlug']}][{$arrField['strSectionID']}][{$arrField['strFieldID']}]"
			: $arrField['strFieldID'];
		
	}	
	private function getInputFieldNameFlat( $arrField=null ) {	
	
		// Instead of [] enclosing array elements, it uses the pipe(|) to represent the multi dimensional array key.
		// This is used to create a reference the submit field name to determine which button is pressed.
		
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
	private function getInputFieldValueFromLabel( $arrField, $arrOptions ) {	

		// This method is similar to the above getInputFieldValue() but this does not check the stored option value.
		// It uses the value set to the vLabel key. This is for submit buttons including export custom field type 
		// that the label should serve as the value.
		
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
	public function getInputField( $strFieldType ) {
		
		// Prepend the field error message.
		$strOutput = isset( $this->arrErrors[ $this->arrField['strSectionID'] ][ $this->arrField['strFieldID'] ] )
			? "<span style='color:red;'>*&nbsp;{$this->arrField['strError']}" . $this->arrErrors[ $this->arrField['strSectionID'] ][ $this->arrField['strFieldID'] ] . "</span><br />"
			: '';		
			
		// Get the input field output.
		switch ( $strFieldType ) {
			case in_array( $strFieldType, array( 'text', 'password', 'color', 'date', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'time', 'url', 'week' ) ):
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
			case 'taxonomy':
				$strOutput .= $this->getTaxonomyChecklistField();
				break;
			case 'posttype':
				$strOutput .= $this->getPostTypeChecklistField();
				break;
			default:	// for anything else, 				
				$strOutput .= $this->arrField['vBeforeInputTag'] . ( ( string ) $this->vValue ) . $this->arrField['vAfterInputTag'];
				break;				
		}
	
		$strOutput .= ( isset( $this->arrField['strDescription'] ) && trim( $this->arrField['strDescription'] ) != '' ) 
			? "<p class='field_description'><span class='description'>{$this->arrField['strDescription']}</span></p>"
			: '';
			
		return $this->arrField['strBeforeField'] 
			. $strOutput
			. $this->arrField['strAfterField'];
		
	}
	private function getTextField( $arrOutput=array() ) {
		
		foreach( ( array ) $this->arrField['vLabel'] as $strKey => $strLabel ) 
			$arrOutput[] = $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
				. ( $strLabel 
					? "<span style='display: inline-block; min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
					. "<label for='{$this->strTagID}_{$strKey}' class='text-label'>{$strLabel}</label>&nbsp;&nbsp;&nbsp;</span>" 
					: "" 
					)
				. "<input id='{$this->strTagID}_{$strKey}' "
				. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
				. "size='" . $this->getCorrespondingArrayValue( $this->arrField['vSize'], $strKey, 30 ) . "' "
				. "maxlength='" . $this->getCorrespondingArrayValue( $this->arrField['vMaxLength'], $strKey, self::$arrDefaultFieldValues['vMaxLength'] ) . "' "
				. "type='{$this->arrField['strType']}' "	// text, password, etc.
				. "name=" . ( is_array( $this->arrField['vLabel'] ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " )
				. "value='" . $this->getCorrespondingArrayValue( $this->vValue, $strKey, null ) . "' "
				. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
				. ( $this->getCorrespondingArrayValue( $this->arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
				. "/>"
				. $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '<br />' )
				. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' );
				
		return "<div id='{$this->strTagID}'>" . implode( '', $arrOutput ) . "</div>";

	}
	private function getNumberField( $arrOutput=array() ) {
		
		foreach( ( array ) $this->arrField['vLabel'] as $strKey => $strLabel ) 
			$arrOutput[] = $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
				. ( $strLabel 
					? "<span style='display: inline-block; min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
					. "<label for='{$this->strTagID}_{$strKey}' class='text-label'>{$strLabel}</label>&nbsp;&nbsp;&nbsp;</span>" 
					: "" 
					)
				. "<input id='{$this->strTagID}_{$strKey}' "
				. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
				. "size='" . $this->getCorrespondingArrayValue( $this->arrField['vSize'], $strKey, 30 ) . "' "
				. "type='{$this->arrField['strType']}' "
				. ( is_array( $this->arrField['vLabel'] ) ? "name='{$this->strFieldName}[{$strKey}]' " : "name='{$this->strFieldName}' " )
				. "value='" . $this->getCorrespondingArrayValue( $this->vValue, $strKey, null ) . "' "
				. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
				. ( $this->getCorrespondingArrayValue( $this->arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
				. "min='" . $this->getCorrespondingArrayValue( $this->arrField['vMin'], $strKey, self::$arrDefaultFieldValues['vMin'] ) . "' "
				. "max='" . $this->getCorrespondingArrayValue( $this->arrField['vMax'], $strKey, self::$arrDefaultFieldValues['vMax'] ) . "' "
				. "step='" . $this->getCorrespondingArrayValue( $this->arrField['vStep'], $strKey, self::$arrDefaultFieldValues['vStep'] ) . "' "
				. "maxlength='" . $this->getCorrespondingArrayValue( $this->arrField['vMaxLength'], $strKey, self::$arrDefaultFieldValues['vMaxLength'] ) . "' "
				. "/>"
				. $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '<br />' )
				. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' );
					
		return "<div id='{$this->strTagID}'>" . implode( '', $arrOutput ) . "</div>";		
		
	}
	private function getTextAreaField( $arrOutput=array() ) {
		
		foreach( ( array ) $this->arrField['vLabel'] as $strKey => $strLabel ) 
			$arrOutput[] = $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
				. ( $strLabel
					? "<span style='display: inline-block; min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
					. "<label for='{$this->strTagID}_{$strKey}' class='text-label'>{$strLabel}</label>&nbsp;&nbsp;&nbsp;</span>" 
					: "" 
					)
				. "<textarea id='{$this->strTagID}_{$strKey}' "
				. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
				. "rows='" . $this->getCorrespondingArrayValue( $this->arrField['vRows'], $strKey, self::$arrDefaultFieldValues['vRows'] ) . "' "
				. "cols='" . $this->getCorrespondingArrayValue( $this->arrField['vCols'], $strKey, self::$arrDefaultFieldValues['vCols'] ) . "' "
				. "maxlength='" . $this->getCorrespondingArrayValue( $this->arrField['vMaxLength'], $strKey, self::$arrDefaultFieldValues['vMaxLength'] ) . "' "
				. "type='{$this->arrField['strType']}' "
				. ( is_array( $this->arrField['vLabel'] ) ? "name='{$this->strFieldName}[{$strKey}]' " : "name='{$this->strFieldName}' " )
				. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
				. ( $this->getCorrespondingArrayValue( $this->arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
				. ">"
				. $this->getCorrespondingArrayValue( $this->vValue, $strKey, null )
				. "</textarea>"
				. $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '<br />' )
				. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' );
		
		return "<div id='{$this->strTagID}'>" . implode( '', $arrOutput ) . "</div>";		
		
	}
	private function getSelectField( $arrOutput=array() ) {

		// The value of the label key must be an array for the select type.
		if ( ! is_array( $this->arrField['vLabel'] ) ) return;	

		$fSingle = ( $this->getArrayDimension( $this->arrField['vLabel'] ) == 1 );
		$arrLabels = $fSingle ? array( $this->arrField['vLabel'] ) : $this->arrField['vLabel'];
		foreach( $arrLabels as $strKey => $vLabel ) 
			$arrOutput[] = $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
				. "<span style='display: inline-block; min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
				. "<select id='{$this->strTagID}_{$strKey}' "
				. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
				. "type='{$this->arrField['strType']}' "
				. "name=" . ( $fSingle ? "'{$this->strFieldName}' " : "'{$this->strFieldName}[{$strKey}]' " )
				. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
				. ">"
				. $this->getOptionTags( $vLabel, $strKey )
				. "</select>"
				. "</span>"
				. $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '&nbsp;&nbsp;' )
				. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' );
		
		return "<div id='{$this->strTagID}'>" . implode( '', $arrOutput ) . "</div>";				
	
	}	
	private function getOptionTags( $arrLabels, $strIterationID ) {	// since 1.1.0
		
		// This is a helper function for the above getSelectField() method.
		$arrOutput = array();
		foreach ( $arrLabels as $strKey => $strLabel ) {
			$arrOutput[] = "<option "
				. "id='{$this->strTagID}_{$strIterationID}_{$strKey}' "
				. "value='{$strKey}' "
				. ( $this->getCorrespondingArrayValue( $this->vValue, $strIterationID, null ) == $strKey ? 'Selected' : '' )
				. ">"
				. $strLabel
				. "</option>";
		}
		return implode( '', $arrOutput );
	}
	private function getRadioField( $arrOutput=array() ) {
		
		// The value of the label key must be an array for the select type.
		if ( ! is_array( $this->arrField['vLabel'] ) ) return;	
		
		$fSingle = ( $this->getArrayDimension( $this->arrField['vLabel'] ) == 1 );
		$arrLabels =  $fSingle ? array( $this->arrField['vLabel'] ) : $this->arrField['vLabel'];
		foreach( $arrLabels as $strKey => $vLabel )  
			$arrOutput[] = $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
				. $this->getRadioTags( $vLabel, $strKey, $fSingle )				
				. $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '<br />' )
				. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' );
				
		return "<div id='{$this->strTagID}'>" . implode( '', $arrOutput ) . "</div>";				
		
	}
	private function getRadioTags( $arrLabels, $strIterationID, $fSingle ) {
		
		// This is a helper function for the above getRadioField() method.
		$arrOutput = array();
		foreach ( $arrLabels as $strKey => $strLabel ) 
			$arrOutput[] = "<span style='display: inline-block;'>"
				. "<input "
				. "id='{$this->strTagID}_{$strIterationID}_{$strKey}' "
				. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
				. "type='radio' "
				. "value='{$strKey}' "
				. "name=" . ( ! $fSingle  ? "'{$this->strFieldName}[{$strIterationID}]' " : "'{$this->strFieldName}' " )
				. ( $this->getCorrespondingArrayValue( $this->vValue, $strIterationID, null ) == $strKey ? 'Checked ' : '' )
				. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
				. "/>&nbsp;&nbsp;"
				. "<span style='display: inline-block; min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
				. "<label for='{$this->strTagID}_{$strIterationID}_{$strKey}'>"
				. $strLabel
				. "</label>"
				. "</span>"
				. "</span>&nbsp;&nbsp;";

		return implode( '', $arrOutput );
	}

	private function getCheckBoxField( $arrOutput=array() ) {

		foreach( ( array ) $this->arrField['vLabel'] as $strKey => $strLabel ) 
			$arrOutput[] = "<input type='hidden' name=" .  ( is_array( $this->arrField['vLabel'] ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " ) . " value='0' />"	// the unchecked value must be set prior to the checkbox input field.
				. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
				. "<span style='display: inline-block; min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
				. "<input "
				. "id='{$this->strTagID}_{$strKey}' "
				. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
				. "type='{$this->arrField['strType']}' "	// checkbox
				. "name=" . ( is_array( $this->arrField['vLabel'] ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " )
				. "value='1' "
				. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
				. ( $this->getCorrespondingArrayValue( $this->vValue, $strKey, null ) == 1 ? "Checked " : '' )
				. "/>&nbsp;&nbsp;"
				. "<span style='display: inline-block; min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
				. "<label for='{$this->strTagID}_{$strKey}'>"				
				. $strLabel
				. "</label>"
				. "</span>"
				. "</span>"
				. $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '&nbsp;&nbsp;&nbsp;' )
				. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' );
					
		return "<div id='{$this->strTagID}'>" . implode( '', $arrOutput ) . "</div>";		
	
	}
	private function getHiddenField( $arrOutput=array() ) {
		
		// The user needs to assign the value to the vDefault key in order to set the hidden field. 
		// If it's not set ( null value ), the below foreach will not iterate an element so no input field will be embedded.
		
		foreach( ( array ) $this->vValue as $strKey => $strValue ) 
			$arrOutput[] = $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
				. "<span style='display: inline-block; min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
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
				. $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '' )
				. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' );
					
		return "<div id='{$this->strTagID}'>" . implode( '', $arrOutput ) . "</div>";			
		
	}
	private function getFileField( $arrOutput=array() ) {

		foreach( ( array ) $this->arrField['vLabel'] as $strKey => $strLabel ) 
			$arrOutput[] = $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
				. "<span style='display: inline-block; min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
				. "<label for='{$this->strTagID}_{$strKey}'>{$strLabel}</label>"
				. "</span>"
				. "<input "
				. "id='{$this->strTagID}_{$strKey}' "
				. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
				. "accept='" . $this->getCorrespondingArrayValue( $this->arrField['vAcceptAttribute'], $strKey, 'audio/*|video/*|image/*|MIME_type' ) . "' "
				. "type='{$this->arrField['strType']}' "	// file
				. "name=" . ( is_array( $this->arrField['vLabel'] ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " )
				. "value='" . $this->getCorrespondingArrayValue( $this->arrField['vLabel'], $strKey, __( 'Submit', 'admin-page-framework' ) ) . "' "
				. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
				. "/>&nbsp;&nbsp;"
				. $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '&nbsp;&nbsp;&nbsp;' )
				. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' );
					
		return "<div id='{$this->strTagID}'>" . implode( '', $arrOutput ) . "</div>";			
	
	}
	private function getSubmitField( $arrOutput=array() ) {
		
		$this->vValue = $this->getInputFieldValueFromLabel( $this->arrField, $this->arrOptions );
		$this->strFieldNameFlat = $this->getInputFieldNameFlat();
		foreach( ( array ) $this->vValue as $strKey => $strValue ) {
			$strRedirectURL = $this->getCorrespondingArrayValue( $this->arrField['vRedirect'], $strKey, null );
			$strLinkURL = $this->getCorrespondingArrayValue( $this->arrField['vLink'], $strKey, null );
			$arrOutput[] = ( $strRedirectURL ? "<input type='hidden' "
				. "name='__redirect[{$this->strTagID}_{$strKey}][url]' "
				. "value='" . $this->getCorrespondingArrayValue( $this->arrField['vRedirect'], $strKey, null ) . "' "
				. "/>" 
				. "<input type='hidden' "
				. "name='__redirect[{$this->strTagID}_{$strKey}][name]' "
				. "value='{$this->strFieldNameFlat}" . ( is_array( $this->vValue ) ? "|{$strKey}" : "'" )
				. "/>" : "" )
				. ( $strLinkURL ? "<input type='hidden' "
				. "name='__link[{$this->strTagID}_{$strKey}][url]' "
				. "value='" . $this->getCorrespondingArrayValue( $this->arrField['vLink'], $strKey, null ) . "' "
				. "/>"
				. "<input type='hidden' "
				. "name='__link[{$this->strTagID}_{$strKey}][name]' "
				. "value='{$this->strFieldNameFlat}" . ( is_array( $this->vValue ) ? "|{$strKey}'" : "'" )
				. "/>" : "" )
				. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
				. "<span style='display: inline-block; min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
				. "<input "
				. "id='{$this->strTagID}_{$strKey}' "
				. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, 'button button-primary' ) . "' "
				. "type='{$this->arrField['strType']}' "	// submit
				. "name=" . ( is_array( $this->arrField['vLabel'] ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " )
				. "value='" . $this->getCorrespondingArrayValue( $this->vValue, $strKey, $this->oMsg->___( 'submit' ) ) . "' "
				. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
				. "/>&nbsp;&nbsp;"
				. "</span>"
				. $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '&nbsp;&nbsp;&nbsp;' )
				. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' );
		}
		return "<div id='{$this->strTagID}'>" . implode( '', $arrOutput ) . "</div>";		
	
	}

	private function getImportField( $arrOutput=array() ) {
		
		$this->vValue = $this->getInputFieldValueFromLabel( $this->arrField, $this->arrOptions );
		
		foreach( ( array ) $this->vValue as $strKey => $strValue ) {
						
			$arrOutput[] = "<input type='hidden' "
				. "name='__import[{$this->arrField['strFieldID']}][import_option_key]" . ( is_array( $this->arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
				. "value='" . $this->getCorrespondingArrayValue( $this->arrField['vImportOptionKey'], $strKey, $this->arrField['strOptionKey'] )
				. "' />"
				. "<input type='hidden' "
				. "name='__import[{$this->arrField['strFieldID']}][format]" . ( is_array( $this->arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
				. "value='" . $this->getCorrespondingArrayValue( $this->arrField['vImportFormat'], $strKey, 'array' )	// array, text, or json.
				. "' />"			
				. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
				. "<span style='display: inline-block; min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
				. "<input "
				. "id='{$this->strTagID}_{$strKey}_file' "
				. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, 'import' ) . "' "
				. "accept='" . $this->getCorrespondingArrayValue( $this->arrField['vAcceptAttribute'], $strKey, 'audio/*|video/*|image/*|MIME_type' ) . "' "
				. "type='file' "	// upload filed. the file type will be stored in $_FILE
				. "name='__import[{$this->arrField['strFieldID']}]" . ( is_array( $this->arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
				. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )				
				. "/>"	
				. "&nbsp;&nbsp;&nbsp;"
				. "<input "
				. "id='{$this->strTagID}_{$strKey}' "
				. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, 'import button button-primary' ) . "' "
				. "type='submit' "	// the export button is a custom submit button.
				. "name='__import[submit][{$this->arrField['strFieldID']}]" . ( is_array( $this->arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
				. "value='" . $this->getCorrespondingArrayValue( $this->vValue, $strKey, $this->oMsg->___( 'import_options' ) ) . "' "
				. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
				. "/>"
				. "</span>"
				. $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '&nbsp;&nbsp;&nbsp;' )
				. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' );
									
		}
					
		return "<div id='{$this->strTagID}'>" . implode( '', $arrOutput ) . "</div>";				
		
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
			
			$arrOutput[] = "<input type='hidden' "
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
				. "<span style='display: inline-block; min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
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
				. $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '&nbsp;&nbsp;&nbsp;' )
				. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' );
									
		}
					
		return "<div id='{$this->strTagID}'>" . implode( '', $arrOutput ) . "</div>";		
	
	}
	private function generateExportFileName( $strOptionKey, $strExportFormat='text' ) {
			
		// Currently only array, text or json is supported.
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

	private function getImageField( $arrOutput=array() ) {
		
		$strSelectImage = __( 'Select Image', 'admin-page-framework' );
		foreach( ( array ) $this->arrField['vLabel'] as $strKey => $strLabel ) 
			$arrOutput[] = $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 
				. ( $strLabel 
					? "<span style='display: inline-block; min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
					. "<label for='{$this->strTagID}_{$strKey}' class='text-label'>{$strLabel}</label>&nbsp;&nbsp;&nbsp;</span>" 
					: "" 
					)
				. "<input id='{$this->strTagID}_{$strKey}' "
				. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
				. "size='" . $this->getCorrespondingArrayValue( $this->arrField['vSize'], $strKey, 80 ) . "' "
				. "maxlength='" . $this->getCorrespondingArrayValue( $this->arrField['vMaxLength'], $strKey, self::$arrDefaultFieldValues['vMaxLength'] ) . "' "
				. "type='text' "	// text
				. "name=" . ( is_array( $this->arrField['vLabel'] ) ? "'{$this->strFieldName}[{$strKey}]' " : "'{$this->strFieldName}' " )
				. "value='" . ( $strImageURL = $this->getCorrespondingArrayValue( $this->vValue, $strKey, self::$arrDefaultFieldValues['vDefault'] ) ) . "' "
				. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
				. ( $this->getCorrespondingArrayValue( $this->arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
				. "/>"
				. "<script>document.write( '&nbsp;&nbsp;&nbsp;<input type=\'submit\' id=\'select_image_{$this->strTagID}_{$strKey}\' value=\'{$strSelectImage}\' class=\'select_image button button-small\' />' );</script>"
				. ( $this->getCorrespondingArrayValue( $this->arrField['vImagePreview'], $strKey, true ) 
					? "<div class='image_preview'><img src='{$strImageURL}' "
					. "id='image_preview_{$this->strTagID}_{$strKey}' "
					. "/></div>"
					: "" )
				. $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '<br />' )
				. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' );
				
		return "<div id='{$this->strTagID}'>" . implode( '', $arrOutput ) . "</div>";		
		
	}
	
	private function getPostTypeChecklistField( $arrOutput=array() ) {
		
		// Note that the posttype checklist field does not support multiple elements by passing an array of labels.
		
		foreach( ( array ) $this->getPostTypeArrayForChecklist( $this->arrField['arrRemove'] ) as $strKey => $strValue ) {
			$strName = "{$this->strFieldName}[{$strKey}]";
			$arrOutput[] = "<input type='hidden' name='{$strName}' value='0' />"
				. $this->getCorrespondingArrayValue( $this->arrField['vBeforeInputTag'], $strKey, '' ) 				
				. "<input "
				. "id='{$this->strTagID}_{$strKey}' "
				. "class='" . $this->getCorrespondingArrayValue( $this->arrField['vClassAttribute'], $strKey, '' ) . "' "
				. "type='checkbox' "
				. "name='{$strName}'"
				. "value='1' "
				. ( $this->getCorrespondingArrayValue( $this->arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
				. ( $this->getCorrespondingArrayValue( $this->vValue, $strKey, false ) == 1 ? "Checked " : '' )				
				. "/>&nbsp;&nbsp;"
				. "<span style='display: inline-block; min-width:" . $this->getCorrespondingArrayValue( $this->arrField['vLabelMinWidth'], $strKey, self::$arrDefaultFieldValues['vLabelMinWidth'] ) . "px;'>"
				. "<label for='{$this->strTagID}_{$strKey}'>"				
				. $strKey
				. "</label>"
				. "</span>"				
				. $this->getCorrespondingArrayValue( $this->arrField['vDelimiter'], $strKey, '&nbsp;&nbsp;&nbsp;' )
				. $this->getCorrespondingArrayValue( $this->arrField['vAfterInputTag'], $strKey, '' );
		}
		return "<div id='{$this->strTagID}'>" . implode( '', $arrOutput ) . "</div>";				
		
	}	
	private function getPostTypeArrayForChecklist( $arrRemoveNames ) {
		
		// A helper function for the above getPosttypeChecklistField method.
		
		$arrPostTypes = get_post_types( '','names' ); 
		$arrPostTypes = array_diff_key( $arrPostTypes, array_flip( $arrRemoveNames ) );	// remove unnecessary keys.
		$arrPostTypes = array_fill_keys( $arrPostTypes, True );
		return $arrPostTypes;		
		
	}		
	
	private function getTaxonomyChecklistField( $arrOutput=array() ) {

		foreach( ( array ) $this->arrField['vTaxonomySlug'] as $strKey => $strTaxonomySlug ) 
			$arrOutput[] = "<div class='wp-tab-panel taxonomy-checklist' style='max-width:{$this->arrField['numMaxWidth']}px; max-height:{$this->arrField['numMaxHeight']}px;'>"
				. "<label>" . $this->getCorrespondingArrayValue( $this->arrField['vLabel'], $strKey, '' ) . "</label>"
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
				. "</div>";
			
		return "<div id='{$this->strTagID}'>" . implode( '', $arrOutput ) . "</div>";	
				
	}	
	private function getSelectedKeyArray( $vValue, $strKey ) {
		
		// This is a helper function for the above getTaxonomyChecklistField() method. 
		// Returns an array consisting of keys whose value is true.
		
		// $vValue can be either an one-dimensional array ( for single fiels ) or a two-dimensional array ( for multiple fields ).
		
		$vValue = ( array ) $vValue;	// cast array because the initial value (null) may not be an array.
		$intArrayDimension = $this->getArrayDimension( $vValue );
				
		if ( $intArrayDimension == 1 )
			$arrKeys = $vValue;
		else if ( $intArrayDimension == 2 )
			$arrKeys = ( array ) $this->getCorrespondingArrayValue( $vValue, $strKey, false );
			
		return array_keys( $arrKeys, true );
	
	}
}
endif;

if ( ! class_exists( 'AdminPageFramework_WalkerTaxonomyChecklist' ) ) :
class AdminPageFramework_WalkerTaxonomyChecklist extends Walker_Category {	// since 1.0.4
	
	/*
	 * Used for the wp_list_categories() function to render category hierarchical checklist.
		Walker : wp-includes/class-wp-walker.php
		Walker_Category : wp-includes/category-template.php
	 * */
	
	function start_el( &$strOutput, $oCategory, $intDepth, $arrArgs ) {
		
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
			. "<li id='{$strID}' $strClass>" 
			. "<input value='0' type='hidden' name='{$arrArgs['name']}[{$intID}]' />"
			. "<input id='{$strID}' value='1' type='checkbox' name='{$arrArgs['name']}[{$intID}]' {$strChecked} {$strDisabled} />"
			. "<label id='{$strID}' class='taxonomy-checklist-label'>"
			. esc_html( apply_filters( 'the_category', $oCategory->name ) ) 
			. "</label>";	// no need to close </li> since it is done in end_el().
			
	}
}
endif;

if ( ! class_exists( 'AdminPageFramework_PostType' ) ) :
abstract class AdminPageFramework_PostType {	

	// Objects
	protected $oUtil;
	
	// Prefixes
	protected $strPrefix_Start = 'start_';
	protected $strPrefix_Cell = 'cell_';
	
	// Containers
	protected $arrTaxonomies;		// stores the registering taxonomy info.
	protected $arrTaxonomyTableFilters = array();	// stores the taxonomy IDs as value to indicate whether the dropdown filter option should be displayed or not.
	protected $arrTaxonomyRemoveSubmenuPages = array();	// stores removing taxonomy menus' info.
	// stores the column headers of the post listing table.
	// reference: http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_edit-post_type_columns
	protected $arrColumnHeaders;	// defined in the constructor.
	protected $arrColumnSortable = array(	// stores the sortable column items.
		'title' => true,
		'date'	=> true,
	);
	
	// Default values
	protected $fEnableAutoSave = true;
	protected $fEnableAuthorTableFileter = false;
	
	public function __construct( $strPostType, $arrArgs=array(), $strCallerPath=null ) {
		
		$this->oUtil = new AdminPageFramework_Utilities;
		
		$this->strPostType = $this->oUtil->sanitizeSlug( $strPostType );
		$this->arrPostTypeArgs = $arrArgs;	// for the argument array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
		$this->strClassName = get_class( $this );
		$this->arrColumnHeaders = array(
			'cb'			=> '<input type="checkbox" />',	// Checkbox for bulk actions. 
			'title'			=> __( 'Title', 'admin-page-framework' ),		// Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
			'author'		=> __( 'Author', 'admin-page-framework' ),		// Post author.
			// 'categories'	=> __( 'Categories', 'admin-page-framework' ),	// Categories the post belongs to. 
			// 'tags'		=> __( 'Tags', 'admin-page-framework' ),	// Tags for the post. 
			'comments' 		=> '<div class="comment-grey-bubble"></div>', // Number of pending comments. 
			'date'			=> __( 'Date', 'admin-page-framework' ), 	// The date and publish status of the post. 
		);			
		$this->strCallerPath = $strCallerPath;
		
		add_action( 'init', array( $this, 'registerPostType' ), 999 );	// this is loaded in the front-end as well so should not be admin_init. Also "if ( is_admin() )" should not be used either.
		add_action( 'admin_enqueue_scripts', array( $this, 'disableAutoSave' ) );
		
		if ( $this->strPostType != '' && is_admin() ) {			
		
			// For table columns
			add_filter( "manage_{$this->strPostType}_posts_columns", array( $this, 'setColumnHeader' ) );
			add_filter( "manage_edit-{$this->strPostType}_sortable_columns", array( $this, 'setSortableColumns' ) );
			add_action( "manage_{$this->strPostType}_posts_custom_column", array( $this, 'setColumnCell' ), 10, 2 );
			
			// For filters
			add_action( 'restrict_manage_posts', array( $this, 'addAuthorTableFilter' ) );
			add_action( 'restrict_manage_posts', array( $this, 'addTaxonomyTableFilter' ) );
			add_filter( 'parse_query', array( $this, 'setTableFilterQuery' ) );
			
			// Style
			add_action( 'admin_head', array( $this, 'addStyle' ) );
			
			// Links
			$this->oLink = new AdminPageFramework_LinkForPostType( $this->strPostType, $this->strCallerPath );
			
		}
	
		$this->oUtil->addAndDoAction( $this, "{$this->strPrefix_Start}{$this->strClassName}" );
		$this->setUp();	// add_action( 'plugins_loaded', array( $this, 'setUp' ) );
		
	}
	
	/*
	 * Extensible methods
	*/
	public function setUp() {}	
	
	public function setColumnHeader( $arrColumnHeaders ) {	// callback for the manage_{post type}_post)_columns hook.
		
		return $this->arrColumnHeaders;
		
	}	
	public function setSortableColumns( $arrColumns ) {	// callback for the manage_edit-{post type}_sortable_columns hook.
		
		return $this->arrColumnSortable;
		
	}
	
	/*
	 * Front-end methods
	 */
	protected function setAutoSave( $fEnableAutoSave=True ) {
		$this->fEnableAutoSave = $fEnableAutoSave;		
	}

	protected function addTaxonomy( $strTaxonomySlug, $arrArgs ) {
		
		$strTaxonomySlug = $this->oUtil->sanitizeSlug( $strTaxonomySlug );
		$this->arrTaxonomies[ $strTaxonomySlug ] = $arrArgs;	
		if ( isset( $arrArgs['show_table_filter'] ) && $arrArgs['show_table_filter'] )
			$this->arrTaxonomyTableFilters[] = $strTaxonomySlug;
		if ( isset( $arrArgs['show_in_sidebar_menus'] ) && ! $arrArgs['show_in_sidebar_menus'] )
			$this->arrTaxonomyRemoveSubmenuPages[ "edit-tags.php?taxonomy={$strTaxonomySlug}&amp;post_type={$this->strPostType}" ] = "edit.php?post_type={$this->strPostType}";
				
		if ( count( $this->arrTaxonomyTableFilters ) == 1 )
			add_action( 'init', array( $this, 'registerTaxonomies' ) );	// the hook should not be admin_init because taxonomies need to be accessed in regular pages.
		if ( count( $this->arrTaxonomyRemoveSubmenuPages ) == 1 )
			add_action( 'admin_menu', array( $this, 'removeTexonomySubmenuPages' ), 999 );		
			
	}	
	
	protected function setAuthorTableFilter( $fEnableAuthorTableFileter=false ) {
		$this->fEnableAuthorTableFileter = $fEnableAuthorTableFileter;
	}
	
	public function setPostTypeArgs( $arrArgs ) {
		$this->arrPostTypeArgs = $arrArgs;
	}

	/*
	 * Callback functions
	 */
	public function addStyle() {

		if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] != $this->strPostType )
			return;

		$this->strStyle = '';	
			
		// Print out the filtered styles.
		echo "<style type='text/css' id='admin-page-framework-style-post-type'>" 
			. $this->oUtil->addAndApplyFilters( $this, "style_{$this->strClassName}", $this->strStyle )
			. "</style>";			
		
	}
	
	public function registerPostType() {

		register_post_type( $this->strPostType, $this->arrPostTypeArgs );
		
		$bIsPostTypeSet = get_option( "post_type_rules_flased_{$this->strPostType}" );
		if ( $bIsPostTypeSet !== true ) {
		   flush_rewrite_rules( false );
		   update_option( "post_type_rules_flased_{$this->strPostType}", true );
		}

	}	

	public function registerTaxonomies() {
		
		foreach( $this->arrTaxonomies as $strTaxonomySlug => $arrArgs ) 
			register_taxonomy(
				$strTaxonomySlug,
				$this->strPostType,
				$arrArgs	// for the argument array keys, refer to: http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
			);	
			
	}
	
	public function removeTexonomySubmenuPages() {
		
		foreach( $this->arrTaxonomyRemoveSubmenuPages as $strSubmenuPageSlug => $strTopLevelPageSlug )
			remove_submenu_page( $strTopLevelPageSlug, $strSubmenuPageSlug );
		
	}
	
	public function disableAutoSave() {
		
		if ( $this->fEnableAutoSave ) return;
		if ( $this->strPostType != get_post_type() ) return;
		wp_dequeue_script( 'autosave' );
			
	}
	
	public function addAuthorTableFilter() {
		
		// Adds a dorpdown list to filter posts by author, placed above the post type listing table.
		
		if ( ! $this->fEnableAuthorTableFileter ) return;
		
		if ( ! ( isset( $_GET['post_type'] ) && post_type_exists( $_GET['post_type'] ) 
			&& in_array( strtolower( $_GET['post_type'] ), array( $this->strPostType ) ) ) )
			return;
		
		wp_dropdown_users( array(
			'show_option_all'	=> 'Show all Authors',
			'show_option_none'	=> false,
			'name'			=> 'author',
			'selected'		=> ! empty( $_GET['author'] ) ? $_GET['author'] : 0,
			'include_selected'	=> false
		));
			
	}
	
	public function addTaxonomyTableFilter() {

		// Adds dorpdown lists to filter posts by added taxonomies, placed above the post type listing table.
		
		if ( $GLOBALS['typenow'] != $this->strPostType ) return;
		
		// If there is no post added to the post type, do nothing.
		$oPostCount = wp_count_posts( $this->strPostType );
		if ( $oPostCount->publish + $oPostCount->future + $oPostCount->draft + $oPostCount->pending + $oPostCount->private + $oPostCount->trash == 0 )
			return;
		
		foreach ( get_object_taxonomies( $GLOBALS['typenow'] ) as $strTaxonomySulg ) {
			
			if ( ! in_array( $strTaxonomySulg, $this->arrTaxonomyTableFilters ) ) continue;
			
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
		
		foreach ( get_object_taxonomies( $GLOBALS['typenow'] ) as $strTaxonomySlug ) {
			
			if ( ! in_array( $strTaxonomySlug, $this->arrTaxonomyTableFilters ) ) continue;
			
			$strVar = &$oQuery->query_vars[ $strTaxonomySlug ];
			if ( ! isset( $strVar ) ) continue;
			
			$oTerm = get_term_by( 'id', $strVar, $strTaxonomySlug );
			if ( is_object( $oTerm ) )
				$strVar = $oTerm->slug;

		}
		return $oQuery;
		
	}
	
	public function setColumnCell( $strColumnTitle, $intPostID ) { 
	
		// foreach ( $this->arrColumnHeaders as $strColumnHeader => $strColumnHeaderTranslated ) 
			// if ( $strColumnHeader == $strColumnTitle ) 
			
		// cell_{post type}_{custom column key}
		echo $this->oUtil->addAndApplyFilter( $this, "{$this->strPrefix_Cell}{$this->strPostType}_{$strColumnTitle}", $strCell='', $intPostID );
				  
	}
	
	/*
	 * Magic method - this prevents PHP's not-a-valid-callback errors.
	*/
	public function __call( $strMethodName, $arrArgs=null ) {	
		if ( substr( $strMethodName, 0, strlen( $this->strPrefix_Cell ) ) == $this->strPrefix_Cell ) return $arrArgs[0];
		if ( substr( $strMethodName, 0, strlen( "style_" ) )== "style_" ) return $arrArgs[0];
	}
	
}
endif;


if ( ! class_exists( 'AdminPageFramework_MetaBox' ) ) :
class AdminPageFramework_MetaBox {
	
	// Default values
	protected static $arrStructure_Field = array(
		'strFieldID'		=> null,	// the field ID
		'strType'			=> null,	// the field type.
		'strTitle' 			=> null,	// the field title
		'strDescription'	=> null,	// an additional note 
		'strTip'			=> null,	// pop up text
		'options'			=> null,	// ? don't remember what this was for
		'vValue'			=> null,	// allows to override the stored value
		'vDefault'			=> null,	// allows to set default values.
		'strName'			=> null,	// allows to set custom field name
		'vLabel'			=> '',		// sets the label for the field. Setting a non-null value will let it parsed with the loop ( foreach ) of the input element rendering method.
		'fIf'				=> true,
	);
	protected $strPrefixStart = 'start_';
	
	function __construct( $strMetaBoxID, $strTitle, $vPostTypes=array( 'post' ), $strContext='normal', $strPriority='default', $arrFields=null, $strCapability='edit_posts', $strTextDomain='admin-page-framework' ) {
		
		// Objects
		$this->oUtil = new AdminPageFramework_Utilities;
		$this->oMsg = new AdminPageFramework_Messages( $strTextDomain );
		$this->oDebug = new AdminPageFramework_Debug;
		
		// Properties
		$this->strMetaBoxID = $this->oUtil->sanitizeSlug( $strMetaBoxID );
		$this->strTitle = $strTitle;
		$this->arrPostTypes = is_string( $vPostTypes ) ? array( $vPostTypes ) : $vPostTypes;	
		$this->strContext = $strContext;	//  'normal', 'advanced', or 'side' 
		$this->strPriority = $strPriority;	// 	'high', 'core', 'default' or 'low'
		$this->arrFields = $arrFields;
		$this->strClassName = get_class( $this );
		$this->strCapability = $strCapability;
		
		// Hooks
		$this->oUtil->addAndDoAction( $this, "{$this->strPrefixStart}{$this->strClassName}" );
		
		if ( is_admin() ) {
			add_action( 'add_meta_boxes', array( $this, 'addMetaBox' ) );
			add_action( 'save_post', array( $this, 'saveMetaBoxFields' ) );
			
			// if ( in_array( $_GET['post_type'], $this->arrPostTypes ) {
			// if ( $GLOBALS['pagenow'] == 'post.php' || ( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->arrPostTypes ) ) ) {
				add_action( 'admin_head', array( $this, 'addStyle' ) );
				add_filter( 'gettext', array( $this, 'replaceThickBoxText' ) , 1, 2 );		
			// }
		}
		
		$this->setUp();
	}
	
	/*
	 * Front-end methods - user may use it.
	 * */
	protected function setUp() {}
	
	public function setFieldArray( $arrFields ) {
		$this->arrFields = $arrFields;
	}
	protected function addSettingFields() {
	
		// This method just adds the given field array items into the field array property. 
	
		foreach( func_get_args() as $arrField ) {
			
			if ( ! is_array( $arrField ) ) continue;
			
			$arrField = $arrField + self::$arrStructure_Field;	// avoid undefined index warnings.
			
			// Sanitize the IDs since they are used as a callback method name.
			$arrField['strFieldID'] = $this->oUtil->sanitizeSlug( $arrField['strFieldID'] );
			
			// Check the mandatory keys' values are set.
			if ( ! isset( $arrField['strFieldID'], $arrField['strType'] ) ) continue;	// these keys are necessary.
							
			// If a custom condition is set and it's not true, skip.
			if ( ! $arrField['fIf'] ) continue;
								
			// If it's the image type field, extra jQuery scripts need to be loaded.
			if ( $arrField['strType'] == 'image' ) $this->addImageFieldScript( $arrField );
					
			$this->arrFields[ $arrField['strFieldID'] ] = $arrField;
						
		}
	}	
	
	/*
	 * Back end methods - public callbacks and private methods.
	 * */
	private function addImageFieldScript( &$arrField ) {
					
		// These two hooks should be enabled when the image field type is added in the field array.
		$this->strThickBoxTitle = isset( $arrField['strTickBoxTitle'] ) ? $arrField['strTickBoxTitle'] : __( 'Upload Image', 'admin-page-framework' );
		$this->strThickBoxButtonUseThis = isset( $arrField['strLabelUseThis'] ) ? $arrField['strLabelUseThis'] : __( 'Use This Image', 'admin-page-framework' ); 
		
		// If it's not post (post edit) page nor the post type page, do not add scripts for media uploader.
		if ( 
			! in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php' ) ) 
			|| ! ( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->arrPostTypes ) )
		
		) return;
		
		// This class may be instantiated multiple times so use a global flag.
		$strRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$strRootClassName}_ScriptEnqueued" ] ) && $GLOBALS[ "{$strRootClassName}_ScriptEnqueued" ] ) return;
		$GLOBALS[ "{$strRootClassName}_ScriptEnqueued" ] = true;
			
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueUploaderScripts' ) );	// called later than the admin_menu hook
		add_action( 'admin_head', array( $this, 'addScript' ) );
					
	}
	
	public function addStyle() {	// callback for the admin_head hook.
			
		// This class may be instantiated multiple times so use a global flag.
		$strRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$strRootClassName}_StyleLoaded" ] ) && $GLOBALS[ "{$strRootClassName}_StyleLoaded" ] ) return;
		$GLOBALS[ "{$strRootClassName}_StyleLoaded" ] = true;

		$this->strStyle = 
			".wrap div.updated, .wrap div.settings-error, .wrap div.error, .wrap div.updated
			{ clear: both; margin-top: 16px;} 
			.taxonomy-checklist li { margin: 8px 0 8px 20px; }
			div.taxonomy-checklist {
				padding: 8px 0 8px 10px;
				margin-bottom: 20px;
			}
			.taxonomy-checklist ul {
				list-style-type: none;
				margin: 0;
			}
			.taxonomy-checklist ul ul {
				margin-left: 1em;
			}
			.taxonomy-checklist-label {
				margin-left: 0.5em;
			}
			.image_preview {
				border: none; clear:both; margin-top: 20px;	max-width:100%; 
			}
			.image_preview img {
				max-height: 600px; max-width: 800px;
			}
		";			
					
		// Print out the filtered styles.
		echo "<style type='text/css' id='admin-page-framework-style'>" 
			. $this->oUtil->addAndApplyFilters( $this, "style_{$this->strClassName}", $this->strStyle )
			. "</style>";
			
	}
	public function addScript() {	// callback for the admin_head hook.
		
		// Append the script
		$this->strScript = "
			jQuery( document ).ready( function( $ ){
				$( '.select_image' ).click( function() {
					pressed_id = $( this ).attr( 'id' );
					field_id = pressed_id.substring( 13 );	// remove the select_image_ prefix
					tb_show('{$this->strThickBoxTitle}', 'media-upload.php?referrer={$this->strClassName}&amp;button_label={$this->strThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true&amp;post_id=0', false );
					return false;	// do not click the button after the script by returning false.
				});
				window.send_to_editor = function( html ) {
					var image_url = $( 'img',html ).attr( 'src' );
					$( '#' + field_id ).val( image_url );	// sets the image url in the main text field.
					tb_remove();	// close the thickbox
					$( '#image_preview_' + field_id ).attr( 'src', image_url );	// updates the preview image
					$( '#image_preview_' + field_id ).show()	// updates the visibility
				}
			});";

		// Print out the filtered styles.
		echo "<script type='text/javascript' id='admin-page-framework-script'>"
			. $this->oUtil->addAndApplyFilters( $this, "script_{$this->strClassName}", $this->strScript )
			. "</script>";	
			
	}
	public function enqueueUploaderScripts() {	// callback for the admin_enqueue_scripts hook.
			
		wp_enqueue_script('jquery');			
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');				
		wp_enqueue_script('media-upload');
	
	} 	 
	public function replaceThickBoxText( $strTranslated, $strText ) {	// callback for the gettext hook.

		// Replace the button label in the media thick box.
		if ( ! in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php' ) ) ) return $strTranslated;
		if ( $strText != 'Insert into Post' ) return $strTranslated;
		if ( $this->oUtil->getQueryValueInURLByKey( wp_get_referer(), 'referrer' ) != $this->strClassName ) return $strTranslated;
		
		if ( isset( $_GET['button_label'] ) ) return $_GET['button_label'];

		return $this->strThickBoxButtonUseThis ?  $this->strThickBoxButtonUseThis : __( 'Use This Image', 'admin-page-framework' );
		
	}
	
	public function addMetaBox() {	// callback for the add_meta_boxes hook.
		
		foreach( $this->arrPostTypes as $strPostType ) 
			add_meta_box( 
				$this->strMetaBoxID, 		// id
				$this->strTitle, 	// title
				array( $this, 'addMetaBoxContents' ), 	// callback
				$strPostType,		// post type
				$this->strContext, 	// context
				$this->strPriority,	// priority
				$this->arrFields	// argument
			);
			
	}	
	public function addMetaBoxContents( $oPost, $vArgs ) {	// call back for the add_meta_box() method.
		
		// Use nonce for verification
		$strOut = wp_nonce_field( $this->strMetaBoxID, $this->strMetaBoxID, true, false );
		
		// Begin the field table and loop
		$strOut .= '<table class="form-table">';
		$this->setOptionArray( $oPost->ID, $vArgs['args'] );
		
		foreach ( ( array ) $vArgs['args'] as $arrField ) {
			
			// Avoid undefined index warnings
			$arrField = $arrField + self::$arrStructure_Field;
			
			// get value of this field if it exists for this post
			$strStoredValue = get_post_meta( $oPost->ID, $arrField['strFieldID'], true );
			$arrField['vValue'] = $strStoredValue ? $strStoredValue : $arrField['vValue'];
			
			// Check capability. If the access level is not sufficient, skip.
			$arrField['strCapability'] = isset( $arrField['strCapability'] ) ? $arrField['strCapability'] : $this->strCapability;
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
		
		$this->arrOptions = array();
		foreach( $arrFields as $intIndex => $arrField ) {
			
			// Avoid undefined index warnings
			$arrField = $arrField + self::$arrStructure_Field;

			$this->arrOptions[ $intIndex ] = get_post_meta( $intPostID, $arrField['strFieldID'], true );
			
		}
	}	
	private function getField( $arrField ) {
		
		// Set the input field name which becomes the option key of the custom meta field of the post.
		$arrField['strName'] = isset( $arrField['strName'] ) ? $arrField['strName'] : $arrField['strFieldID'];
		
		$oField = new AdminPageFramework_InputField( $arrField, $this->arrOptions, $arr=array(), $this->oMsg );	// currently error arrays are not supported for meta-boxes 
		$strOut = $this->oUtil->addAndApplyFilter(
			$this,
			$this->strClassName . '_' . 'field_' . $arrField['strFieldID'],	// filter: class name + _ + field_ + field id
			$oField->getInputField( $arrField['strType'] ),	// field output
			$arrField // the field array
		);
		unset( $oField );	// release the object for PHP 5.2.x or below.		
		return $strOut;
				
	}
		
	// Save the Data
	public function saveMetaBoxFields( $intPostID ) {	// callback for the save_post hook
		
		// Bail if we're doing an auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		
		// If our nonce isn't there, or we can't verify it, bail
		if ( ! isset( $_POST[ $this->strMetaBoxID ] ) || ! wp_verify_nonce( $_POST[ $this->strMetaBoxID ], $this->strMetaBoxID ) ) return;
			
		// Check permissions
		if ( in_array( $_POST['post_type'], $this->arrPostTypes )   
			&& ( ( ! current_user_can( $this->strCapability, $intPostID ) ) || ( ! current_user_can( $this->strCapability, $intPostID ) ) )
		) return;

		// Compose an array consisting of the submitted registered field values.
		$arrInput = array();
		foreach( $this->arrFields as $arrField ) 
			$arrInput[ $arrField['strFieldID'] ] = isset( $_POST[ $arrField['strFieldID'] ] ) ? $_POST[ $arrField['strFieldID'] ] : null;
			
		// Apply filters to the array of the submitted values.
		$arrInput = $this->oUtil->addAndApplyFilters( $this, "validation_{$this->strClassName}", $arrInput );
		
		// Loop through fields and save the data.
		foreach ( $arrInput as $strFieldID => $vValue ) {
			
			$strOldValue = get_post_meta( $intPostID, $strFieldID, true );			
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
		if ( $strMethodName == $this->strPrefixStart . $this->strClassName ) return;
		
		// the class name + field_ field ID filter.
		if ( substr( $strMethodName, 0, strlen( $this->strClassName . '_' . 'field_' ) ) == $this->strClassName . '_' . 'field_' ) 
			return $arrArgs[ 0 ];
			
		// the script_ + class name	filter.
		if ( substr( $strMethodName, 0, strlen( "script_{$this->strClassName}" ) ) == "script_{$this->strClassName}" ) 
			return $arrArgs[ 0 ];		
	
		// the style_ + class name	filter.
		if ( substr( $strMethodName, 0, strlen( "style_{$this->strClassName}" ) ) == "style_{$this->strClassName}" ) 
			return $arrArgs[ 0 ];		

		// the validation_ + class name	filter.
		if ( substr( $strMethodName, 0, strlen( "validation_{$this->strClassName}" ) ) == "validation_{$this->strClassName}" )
			return $arrArgs[ 0 ];				
			
	}
}
endif;