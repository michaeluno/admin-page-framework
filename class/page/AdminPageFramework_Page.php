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
 * @staticvar		array		$_aPrefixes						stores the prefix strings for filter and action hooks.
 * @staticvar		array		$_aPrefixesForCallbacks			unlike $_aPrefixes, these require to set the return value.
 * @staticvar		array		$_aScreenIconIDs					stores the ID selector names for screen icons.
 * @staticvar		array		$_aPrefixes						stores the prefix strings for filter and action hooks.
 * @staticvar		array		$_aStructure_InPageTabElements		represents the array structure of an in-page tab array.
 */
abstract class AdminPageFramework_Page {
			
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
	public static $_aPrefixes = array(	
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
	 * Unlike $_aPrefixes, these require to set the return value.
	 * 
	 * @since			2.0.0
	 * @var				array
	 * @static
	 * @access			protected
	 * @internal
	 */ 	
	protected static $_aPrefixesForCallbacks = array(
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
		$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$_aPrefixes['do_before_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, true ) );	
		?>
		<div class="wrap">
			<?php
				// Screen icon, page heading tabs(page title), and in-page tabs.
				$sHead = $this->getScreenIcon( $sPageSlug );	
				$sHead .= $this->getPageHeadingTabs( $sPageSlug, $this->oProps->sPageHeadingTabTag ); 	
				$sHead .= $this->getInPageTabs( $sPageSlug, $this->oProps->sInPageTabTag );

				// Apply filters in this order, in-page tab -> page -> global.
				echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$_aPrefixes['head_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, false ), $sHead );
			?>
			<div class="admin-page-framework-container">
				<?php
					$this->showSettingsErrors();
						
					$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$_aPrefixes['do_form_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, true ) );	

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
					echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$_aPrefixes['content_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, false ), $sContent );
	
					// Do the page actions.
					$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$_aPrefixes['do_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, true ) );	
						
				?>
				
			<?php echo $this->getFormClosingTag( $sPageSlug, $sTabSlug );  ?>
			
			</div><!-- End admin-page-framework-container -->
				
			<?php	
				// Apply the foot filters.
				echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$_aPrefixes['foot_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, false ), '' );	// empty string
			?>
		</div><!-- End Wrap -->
		<?php
		// Do actions after rendering the page.
		$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$_aPrefixes['do_after_'], $this->oProps->sClassName, $sPageSlug, $sTabSlug, true ) );
		
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