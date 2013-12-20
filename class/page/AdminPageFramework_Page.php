<?php
if ( ! class_exists( 'AdminPageFramework_Page' ) ) :
/**
 * Provides methods to render admin page elements.
 *
 * @abstract
 * @since			2.0.0
 * @since			2.1.0		Extends AdminPageFramework_HelpPane_Page.
 * @since			3.0.0		No longer extends AdminPageFramework_HelpPane_Page.
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Page
 * @staticvar		array		$_aScreenIconIDs					stores the ID selector names for screen icons.
 * @staticvar		array		$_aHookPrefixes							stores the prefix strings for filter and action hooks.
 * @staticvar		array		$_aStructure_InPageTabElements		represents the array structure of an in-page tab array.
 */
abstract class AdminPageFramework_Page extends AdminPageFramework_Base {
				
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
	public static $_aHookPrefixes = array(	
		'start_'			=> 'start_',
		'load_'				=> 'load_',
		'do_before_'		=> 'do_before_',
		'do_after_'			=> 'do_after_',
		'do_form_'			=> 'do_form_',
		'do_'				=> 'do_',
		'content_top_'		=> 'content_top_',
		'content_'			=> 'content_',
		'content_bottom_'	=> 'content_bottom_',
		'validation_'		=> 'validation_',
		'export_name'		=> 'export_name',
		'export_format' 	=> 'export_format',
		'export_'			=> 'export_',
		'import_name'		=> 'import_name',
		'import_format'		=> 'import_format',
		'import_'			=> 'import_',
		'style_'			=> 'style_',
		'style_ie_'			=> 'style_ie_',
		'script_'			=> 'script_',
		
		'field_'			=> 'field_',
		'section_'			=> 'section_',
		'fields_'			=> 'fields_',
		'sections_'			=> 'sections_',
		'pages_'			=> 'pages_',
		'tabs_'				=> 'tabs_',
		
		'field_types_'		=> 'field_types_',
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
	protected static $_aScreenIconIDs = array(
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
	
	
	function __construct() {	
	
		add_action( 'admin_menu', array( $this, '_replyToFinalizeInPageTabs' ), 99 );	// must be called before the _replyToRegisterSettings() method which uses the same hook.
		
		// Call the parent constructor.
		$aArgs = func_get_args();
		call_user_func_array( array( $this, "parent::__construct" ), $aArgs );
		
		
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
	 * @since			3.0.0			Changed the scope to public.
	 * @param			array			$aTab1			The in-page tab array.
	 * @param			array			$aTab2			Another in-page tab array.
	 * @param			array			$_and_more			Add in-page tab arrays as many as necessary to the next parameters.
	 * @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	 * @remark			In-page tabs are different from page-heading tabs which is automatically added with page titles.	 
	 * @return			void
	 */ 			
	public function addInPageTabs( $aTab1, $aTab2=null, $_and_more=null ) {
		
		foreach( func_get_args() as $aTab ) {
			if ( ! is_array( $aTab ) ) continue;
			$aTab = $aTab + self::$_aStructure_InPageTabElements;	// avoid undefined index warnings.
			$this->addInPageTab( $aTab['page_slug'], $aTab['title'], $aTab['tab_slug'], $aTab['order'], $aTab['show_inpage_tab'], $aTab['parent_tab_slug'] );
		}
		
	}
	/**
	 * Adds an in-page tab.
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Changed the scope to public.
	 * @param			string			$sPageSlug			The page slug that the tab belongs to.
	 * @param			string			$sTabTitle			The title of the tab.
	 * @param			string			$sTabSlug			The tab slug. Non-alphabetical characters should not be used including dots(.) and hyphens(-).
	 * @param			integer			$nOrder				( optional ) the order number of the tab. The lager the number is, the lower the position it is placed in the menu.
	 * @param			boolean			$bHide				( optional ) default: false. If this is set to false, the tab title will not be displayed in the tab navigation menu; however, it is still accessible from the direct URL.
	 * @param			string			$sParentTabSlug		( optional ) this needs to be set if the above show_inpage_tab is true so that the parent tab will be emphasized as active when the hidden page is accessed.
	 * @remark			Use this method to add in-page tabs to ensure the array holds all the necessary keys.
	 * @remark			In-page tabs are different from page-heading tabs which is automatically added with page titles.
	 * @return			void
	 */ 		
	public function addInPageTab( $sPageSlug, $sTabTitle, $sTabSlug, $nOrder=null, $bHide=null, $sParentTabSlug=null ) {	
		
		$sTabSlug = $this->oUtil->sanitizeSlug( $sTabSlug );
		$sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
		$iCountElement = isset( $this->oProp->aInPageTabs[ $sPageSlug ] ) ? count( $this->oProp->aInPageTabs[ $sPageSlug ] ) : 0;
		if ( ! empty( $sTabSlug ) && ! empty( $sPageSlug ) ) 
			$this->oProp->aInPageTabs[ $sPageSlug ][ $sTabSlug ] = array(
				'page_slug'	=> $sPageSlug,
				'title'		=> trim( $sTabTitle ),
				'tab_slug'	=> $sTabSlug,
				'order'		=> is_numeric( $nOrder ) ? $nOrder : $iCountElement + 10,
				'show_inpage_tab'			=> ( $bHide ),
				'parent_tab_slug' => ! empty( $sParentTabSlug ) ? $this->oUtil->sanitizeSlug( $sParentTabSlug ) : null,
			);
	
	}	
	
	/**
	 * Sets whether the page title is displayed or not.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setPageTitleVisibility( false );    // disables the page title.</code>
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Changed the scope to public.
	 * @param			boolean			$bShow			If false, the page title will not be displayed.
	 * @remark			The user may use this method.
	 * @return			void
	 */ 
	public function setPageTitleVisibility( $bShow=true, $sPageSlug='' ) {
		$sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
		if ( ! empty( $sPageSlug ) )
			$this->oProp->aPages[ $sPageSlug ]['fShowPageTitle'] = $bShow;
		else {
			$this->oProp->bShowPageTitle = $bShow;
			foreach( $this->oProp->aPages as &$aPage ) 
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
	 * @since			3.0.0			Changed the scope to public.
	 * @param			boolean			$bShow					If false, page-heading tabs will be disabled; otherwise, enabled.
	 * @param			string			$sPageSlug			The page to apply the visibility setting. If not set, it applies to all the pages.
	 * @remark			Page-heading tabs and in-page tabs are different. The former displays page titles and the latter displays tab titles.
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */ 
	public function setPageHeadingTabsVisibility( $bShow=true, $sPageSlug='' ) {
		$sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
		if ( ! empty( $sPageSlug ) )
			$this->oProp->aPages[ $sPageSlug ]['fShowPageHeadingTabs'] = $bShow;
		else {
			$this->oProp->bShowPageHeadingTabs = $bShow;
			foreach( $this->oProp->aPages as &$aPage ) 
				$aPage['fShowPageHeadingTabs'] = $bShow;
		}
	}
	
	/**
	 * Sets whether in-page tabs are displayed or not.
	 * 
	 * Sometimes, it is required to disable in-page tabs in certain pages. In that case, use the second parameter.
	 * 
	 * @since			2.1.1
	 * @since			3.0.0			Changed the scope to public. Changed the name from showInPageTabs() to setInPageTabsVisibility().
	 * @param			boolean			$bShow				If false, in-page tabs will be disabled.
	 * @param			string			$sPageSlug		The page to apply the visibility setting. If not set, it applies to all the pages.
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */
	public function setInPageTabsVisibility( $bShow=true, $sPageSlug='' ) {
		$sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
		if ( ! empty( $sPageSlug ) )
			$this->oProp->aPages[ $sPageSlug ]['fShowInPageTabs'] = $bShow;
		else {
			$this->oProp->bShowInPageTabs = $bShow;
			foreach( $this->oProp->aPages as &$aPage )
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
	 * @since			3.0.0			Changed the scope to public.
	 * @param			string			$sTag					The HTML tag that encloses each in-page tab title. Default: h3.
	 * @param			string			$sPageSlug			The page slug that applies the setting.	
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */ 	
	public function setInPageTabTag( $sTag='h3', $sPageSlug='' ) {
		$sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
		if ( ! empty( $sPageSlug ) )
			$this->oProp->aPages[ $sPageSlug ]['sInPageTabTag'] = $sTag;
		else {
			$this->oProp->sInPageTabTag = $sTag;
			foreach( $this->oProp->aPages as &$aPage )
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
	 * @since			3.0.0			Changed the scope to public.
	 * @param			string			$sTag					The HTML tag that encloses the page-heading tab title. Default: h2.
	 * @param			string			$sPageSlug			The page slug that applies the setting.	
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */
	public function setPageHeadingTabTag( $sTag='h2', $sPageSlug='' ) {
		$sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
		if ( ! empty( $sPageSlug ) )
			$this->oProp->aPages[ $sPageSlug ]['sPageHeadingTabTag'] = $sTag;
		else {
			$this->oProp->sPageHeadingTabTag = $sTag;
			foreach( $this->oProp->aPages as &$aPage )
				$aPage[ $sPageSlug ]['sPageHeadingTabTag'] = $sTag;
		}
	}
	
	/*
	 * Internal Methods
	 */
	
	/**
	 * Renders the admin page.
	 * 
	 * @remark			This is not intended for the users to use.
	 * @since			2.0.0
	 * @access			protected
	 * @return			void
	 * @internal
	 */ 
	protected function _renderPage( $sPageSlug, $sTabSlug=null ) {

		// Do actions before rendering the page. In this order, global -> page -> in-page tab
		$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( 'do_before_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ) );	
		?>
		<div class="wrap">
			<?php
				// Screen icon, page heading tabs(page title), and in-page tabs.
				$sContentTop = $this->_getScreenIcon( $sPageSlug );	
				$sContentTop .= $this->_getPageHeadingTabs( $sPageSlug, $this->oProp->sPageHeadingTabTag ); 	
				$sContentTop .= $this->_getInPageTabs( $sPageSlug, $this->oProp->sInPageTabTag );

				// Apply filters in this order, in-page tab -> page -> global.
				echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( 'content_top_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), $sContentTop );
			?>
			<div class="admin-page-framework-container">
				<?php
					$this->_showSettingsErrors();
						
					$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( 'do_form_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ) );	

					echo $this->_getFormOpeningTag();	// <form ... >
					
					// Capture the output buffer
					ob_start(); // start buffer
							 					
					// Render the form elements by Settings API
					if ( $this->oProp->bEnableForm ) {
						settings_fields( $this->oProp->sOptionKey );	// this value also determines the $option_page global variable value.
						do_settings_sections( $sPageSlug ); 
					}				
					 
					$sContent = ob_get_contents(); // assign the content buffer to a variable
					ob_end_clean(); // end buffer and remove the buffer
								
					// Apply the content filters.
					echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( 'content_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), $sContent );
	
					// Do the page actions.
					$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( 'do_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ) );	
						
				?>
				
			<?php echo $this->_getFormClosingTag( $sPageSlug, $sTabSlug );  // </form> ?>	
			
			</div><!-- End admin-page-framework-container -->
				
			<?php	
				// Apply the content_bottom filters.
				echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( 'content_bottom_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), '' );	// empty string
			?>
		</div><!-- End Wrap -->
		<?php
		// Do actions after rendering the page.
		$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( 'do_after_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ) );
		
	}
		/**
		 * Retrieves the form opening tag.
		 * 
		 * @since			2.0.0
		 * @internal
		 */ 
		private function _getFormOpeningTag() {	
			return $this->oProp->bEnableForm
				? "<form action='options.php' method='post' enctype='{$this->oProp->sFormEncType}'>"
				: "";
		}
		/**
		 * Retrieves the form closing tag.
		 * 
		 * @since			2.0.0
		 * @internal
		 */ 	
		private function _getFormClosingTag( $sPageSlug, $sTabSlug ) {
			return $this->oProp->bEnableForm 
				? "<input type='hidden' name='page_slug' value='{$sPageSlug}' />" . PHP_EOL
					. "<input type='hidden' name='tab_slug' value='{$sTabSlug}' />" . PHP_EOL			
					. "</form><!-- End Form -->"
				: '';
		}	
	
		/**
		 * Displays admin notices set for the settings.
		 * 
		 * @global			$pagenow
		 * @since			2.0.0
		 * @since			2.0.1			Fixed a bug that the admin messages were displayed twice in the options-general.php page.
		 * @return			void
		 * @internal		
		 */ 
		private function _showSettingsErrors() {
			
			// WordPress automatically performs the settings_errors() function in the options pages. See options-head.php.
			if ( $GLOBALS['pagenow'] == 'options-general.php' ) return;	
			
			$aSettingsMessages = get_settings_errors( $this->oProp->sOptionKey );
			
			// If custom messages are added, remove the default one. 
			if ( count( $aSettingsMessages ) > 1 ) 
				$this->_removeDefaultSettingsNotice();
			
			settings_errors( $this->oProp->sOptionKey );	// Show the message like "The options have been updated" etc.
		
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
			private function _removeDefaultSettingsNotice() {
						
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
					
					if ( $aDetails['setting'] != $this->oProp->sOptionKey ) continue;
					
					if ( in_array( $aDetails['message'], $aDefaultMessages ) )
						unset( $wp_settings_errors[ $iIndex ] );
						
				}
			}		
	
		/**
		 * Retrieves the screen icon output as HTML.
		 * 
		 * @remark			the screen object is supported in WordPress 3.3 or above.
		 * @since			2.0.0
		 */ 	
		private function _getScreenIcon( $sPageSlug ) {

			// If the icon path is explicitly set, use it.
			if ( isset( $this->oProp->aPages[ $sPageSlug ]['hrefIcon32x32'] ) ) 
				return '<div class="icon32" style="background-image: url(' . $this->oProp->aPages[ $sPageSlug ]['hrefIcon32x32'] . ');"><br /></div>';
			
			// If the screen icon ID is explicitly set, use it.
			if ( isset( $this->oProp->aPages[ $sPageSlug ]['screen_iconID'] ) )
				return '<div class="icon32" id="icon-' . $this->oProp->aPages[ $sPageSlug ]['screen_iconID'] . '"><br /></div>';
				
			// Retrieve the screen object for the current page.
			$oScreen = get_current_screen();
			$sIconIDAttribute = $this->_getScreenIDAttribute( $oScreen );

			$sClass = 'icon32';
			if ( empty( $sIconIDAttribute ) && $oScreen->post_type ) 
				$sClass .= ' ' . sanitize_html_class( 'icon32-posts-' . $oScreen->post_type );
			
			if ( empty( $sIconIDAttribute ) || $sIconIDAttribute == $this->oProp->sClassName )
				$sIconIDAttribute = 'generic';		// the default value
			
			return '<div id="icon-' . $sIconIDAttribute . '" class="' . $sClass . '"><br /></div>';
				
		}
			/**
			 * Retrieves the screen ID attribute from the given screen object.
			 * 
			 * @since			2.0.0
			 */ 	
			private function _getScreenIDAttribute( $oScreen ) {
				
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
		private function _getPageHeadingTabs( $sCurrentPageSlug, $sTag='h2', $aOutput=array() ) {
			
			// If the page title is disabled, return an empty string.
			if ( ! $this->oProp->aPages[ $sCurrentPageSlug ][ 'fShowPageTitle' ] ) return "";

			$sTag = $this->oProp->aPages[ $sCurrentPageSlug ][ 'sPageHeadingTabTag' ]
				? $this->oProp->aPages[ $sCurrentPageSlug ][ 'sPageHeadingTabTag' ]
				: $sTag;
		
			// If the page heading tab visibility is disabled, return the title.
			if ( ! $this->oProp->aPages[ $sCurrentPageSlug ][ 'fShowPageHeadingTabs' ] )
				return "<{$sTag}>" . $this->oProp->aPages[ $sCurrentPageSlug ]['title'] . "</{$sTag}>";		
			
			foreach( $this->oProp->aPages as $aSubPage ) {
				
				// For added sub-pages
				if ( isset( $aSubPage['page_slug'] ) && $aSubPage['fShowPageHeadingTab'] ) {
					// Check if the current tab number matches the iteration number. If not match, then assign blank; otherwise put the active class name.
					$sClassActive =  $sCurrentPageSlug == $aSubPage['page_slug']  ? 'nav-tab-active' : '';		
					$aOutput[] = "<a class='nav-tab {$sClassActive}' "
						. "href='" . $this->oUtil->getQueryAdminURL( array( 'page' => $aSubPage['page_slug'], 'tab' => false ), $this->oProp->aDisallowedQueryKeys ) 
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
		private function _getInPageTabs( $sCurrentPageSlug, $sTag='h3', $aOutput=array() ) {
			
			// If in-page tabs are not set, return an empty string.
			if ( empty( $this->oProp->aInPageTabs[ $sCurrentPageSlug ] ) ) return implode( '', $aOutput );
					
			// Determine the current tab slug.
			$sCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProp->getDefaultInPageTab( $sCurrentPageSlug );
			$sCurrentTabSlug = $this->_getParentTabSlug( $sCurrentPageSlug, $sCurrentTabSlug );
			
			$sTag = $this->oProp->aPages[ $sCurrentPageSlug ][ 'sInPageTabTag' ]
				? $this->oProp->aPages[ $sCurrentPageSlug ][ 'sInPageTabTag' ]
				: $sTag;
		
			// If the in-page tabs' visibility is set to false, returns the title.
			if ( ! $this->oProp->aPages[ $sCurrentPageSlug ][ 'fShowInPageTabs' ]	)
				return isset( $this->oProp->aInPageTabs[ $sCurrentPageSlug ][ $sCurrentTabSlug ]['title'] ) 
					? "<{$sTag}>{$this->oProp->aInPageTabs[ $sCurrentPageSlug ][ $sCurrentTabSlug ]['title']}</{$sTag}>" 
					: "";
		
			// Get the actual string buffer.
			foreach( $this->oProp->aInPageTabs[ $sCurrentPageSlug ] as $sTabSlug => $aInPageTab ) {
						
				// If it's hidden and its parent tab is not set, skip
				if ( $aInPageTab['show_inpage_tab'] && ! isset( $aInPageTab['parent_tab_slug'] ) ) continue;
				
				// The parent tab means the root tab when there is a hidden tab that belongs to it. Also check it the specified parent tab exists.
				$sInPageTabSlug = isset( $aInPageTab['parent_tab_slug'], $this->oProp->aInPageTabs[ $sCurrentPageSlug ][ $aInPageTab['parent_tab_slug'] ] ) 
					? $aInPageTab['parent_tab_slug'] 
					: $aInPageTab['tab_slug'];
					
				// Check if the current tab slug matches the iteration slug. If not match, assign blank; otherwise, put the active class name.
				$bIsActiveTab = ( $sCurrentTabSlug == $sInPageTabSlug );
				$aOutput[ $sInPageTabSlug ] = "<a class='nav-tab " . ( $bIsActiveTab ? "nav-tab-active" : "" ) . "' "
					. "href='" . $this->oUtil->getQueryAdminURL( array( 'page' => $sCurrentPageSlug, 'tab' => $sInPageTabSlug ), $this->oProp->aDisallowedQueryKeys ) 
					. "'>"
					. $this->oProp->aInPageTabs[ $sCurrentPageSlug ][ $sInPageTabSlug ]['title'] //	"{$aInPageTab['title']}"
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
			private function _getParentTabSlug( $sPageSlug, $sTabSlug ) {
				
				$sParentTabSlug = isset( $this->oProp->aInPageTabs[ $sPageSlug ][ $sTabSlug ]['parent_tab_slug'] ) 
					? $this->oProp->aInPageTabs[ $sPageSlug ][ $sTabSlug ]['parent_tab_slug']
					: $sTabSlug;
				
				return isset( $this->oProp->aInPageTabs[ $sPageSlug ][ $sParentTabSlug ]['show_inpage_tab'] ) && $this->oProp->aInPageTabs[ $sPageSlug ][ $sParentTabSlug ]['show_inpage_tab']
					? ""
					: $sParentTabSlug;

			}

	/*
	 * Callbacks
	 */
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
	public function _replyToFinalizeInPageTabs() {
	
		foreach( $this->oProp->aPages as $sPageSlug => $aPage ) {
			
			if ( ! isset( $this->oProp->aInPageTabs[ $sPageSlug ] ) ) continue;
			
			// Apply filters to let modify the in-page tab array.
			$this->oProp->aInPageTabs[ $sPageSlug ] = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
				$this,
				"tabs_{$this->oProp->sClassName}_{$sPageSlug}",
				$this->oProp->aInPageTabs[ $sPageSlug ]			
			);	
			// Added in-page arrays may be missing necessary keys so merge them with the default array structure.
			foreach( $this->oProp->aInPageTabs[ $sPageSlug ] as &$aInPageTab ) 
				$aInPageTab = $aInPageTab + self::$_aStructure_InPageTabElements;
						
			// Sort the in-page tab array.
			uasort( $this->oProp->aInPageTabs[ $sPageSlug ], array( $this->oProp, 'sortByOrder' ) );
			
			// Set the default tab for the page.
			// Read the value as reference; otherwise, a strange bug occurs. It may be due to the variable name, $aInPageTab, is also used as reference in the above foreach.
			foreach( $this->oProp->aInPageTabs[ $sPageSlug ] as $sTabSlug => &$aInPageTab ) { 	
			
				if ( ! isset( $aInPageTab['tab_slug'] ) ) continue;	
				
				// Regardless of whether it's a hidden tab, it is stored as the default in-page tab.
				$this->oProp->aDefaultInPageTabs[ $sPageSlug ] = $aInPageTab['tab_slug'];
					
				break;	// The first iteration item is the default one.
			}
		}
	}			

}
endif;