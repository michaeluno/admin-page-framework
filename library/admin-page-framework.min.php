<?php 
/**
 * Loads the Admin Page Framework library.
 * 
 * @info
 * Library Name: Admin Page Framework
 * Library URI: http://wordpress.org/extend/plugins/admin-page-framework/
 * Author:  Michael Uno
 * Author URI: http://michaeluno.jp
 * Version: 3.0.4b04
 * Requirements: WordPress 3.3 or above, PHP 5.2.4 or above.
 * Description: Provides simpler means of building administration pages for plugin and theme developers.
 * @copyright	  	2013-2014 (c) Michael Uno
 * @license		  	MIT <http://opensource.org/licenses/MIT>
 * @see			    http://wordpress.org/plugins/admin-page-framework/
 * @see			    https://github.com/michaeluno/admin-page-framework
 * @link		    http://en.michaeluno.jp/admin-page-framework
 * @since		  	3.0.0
 * @remark			The minifier script will refer this comment section to create the comment header. So don't remove the @info section.
 * @remark			This class will not be included in the minifiled version.
 * @package			AdminPageFramework
 * @subpackage		Utility
 * @internal
 */ if ( ! class_exists( 'AdminPageFramework_Base' ) ) : abstract class AdminPageFramework_Base { protected static $_aHookPrefixes = array( 'start_' => 'start_', 'load_' => 'load_', 'do_before_' => 'do_before_', 'do_after_' => 'do_after_', 'do_form_' => 'do_form_', 'do_' => 'do_', 'submit_' => 'submit_', 'content_foot_' => 'content_foot_', 'content_bottom_' => 'content_bottom_', 'content_' => 'content_', 'validation_' => 'validation_', 'validation_saved_options_' => 'validation_saved_options_', 'export_name' => 'export_name', 'export_format' => 'export_format', 'export_' => 'export_', 'import_name' => 'import_name', 'import_format' => 'import_format', 'import_' => 'import_', 'style_common_ie_' => 'style_common_ie_', 'style_common_' => 'style_common_', 'style_ie_' => 'style_ie_', 'style_' => 'style_', 'script_' => 'script_', 'field_' => 'field_', 'section_head_' => 'section_head_', 'fields_' => 'fields_', 'sections_' => 'sections_', 'pages_' => 'pages_', 'tabs_' => 'tabs_', 'field_types_' => 'field_types_', 'field_definition_' => 'field_definition_', ); public $oProp; protected $oDebug; protected $oMsg; protected $oLink; protected $oUtil; protected $oHeadTag; protected $oPageLoadInfo; protected $oHelpPane; function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) { $this->oProp = new AdminPageFramework_Property_Page( $this, $sCallerPath, get_class( $this ), $sOptionKey, $sCapability, $sTextDomain ); $this->oMsg = AdminPageFramework_Message::instantiate( $sTextDomain ); $this->oPageLoadInfo = AdminPageFramework_PageLoadInfo_Page::instantiate( $this->oProp, $this->oMsg ); $this->oHelpPane = new AdminPageFramework_HelpPane_Page( $this->oProp ); $this->oLink = new AdminPageFramework_Link_Page( $this->oProp, $this->oMsg ); $this->oHeadTag = new AdminPageFramework_HeadTag_Page( $this->oProp ); $this->oUtil = new AdminPageFramework_WPUtility; $this->oDebug = new AdminPageFramework_Debug; if ( $this->oProp->bIsAdmin ) { add_action( 'wp_loaded', array( $this, 'setUp' ) ); } } public function setUp() {} public function addHelpTab( $aHelpTab ) {} public function enqueueStyles( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {} public function enqueueStyle( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {} public function enqueueScripts( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {} public function enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {} public function addLinkToPluginDescription( $sTaggedLinkHTML1, $sTaggedLinkHTML2=null, $_and_more=null ) {} public function addLinkToPluginTitle( $sTaggedLinkHTML1, $sTaggedLinkHTML2=null, $_and_more=null ) {} public function setCapability( $sCapability ) {} public function setFooterInfoLeft( $sHTML, $bAppend=true ) {} public function setFooterInfoRight( $sHTML, $bAppend=true ) {} public function setAdminNotice( $sMessage, $sClassSelector='error', $sID='' ) {} public function setDisallowedQueryKeys( $asQueryKeys, $bAppend=true ) {} public function addInPageTabs( $aTab1, $aTab2=null, $_and_more=null ) {} public function addInPageTab( $asInPageTab ) {} public function setPageTitleVisibility( $bShow=true, $sPageSlug='' ) {} public function setPageHeadingTabsVisibility( $bShow=true, $sPageSlug='' ) {} public function setInPageTabsVisibility( $bShow=true, $sPageSlug='' ) {} public function setInPageTabTag( $sTag='h3', $sPageSlug='' ) {} public function setPageHeadingTabTag( $sTag='h2', $sPageSlug='' ) {} public function setRootMenuPage( $sRootMenuLabel, $sIcon16x16=null, $iMenuPosition=null ) {} public function setRootMenuPageBySlug( $sRootMenuSlug ) {} public function addSubMenuItems( $aSubMenuItem1, $aSubMenuItem2=null, $_and_more=null ) {} public function addSubMenuItem( array $aSubMenuItem ) {} protected function addSubMenuLink( array $aSubMenuLink ) {} protected function addSubMenuPages() {} protected function addSubMenuPage( array $aSubMenuPage ) {} public function setSettingNotice( $sMsg, $sType='error', $sID=null, $bOverride=true ) {} public function addSettingSections( $aSection1, $aSection2=null, $_and_more=null ) {} public function addSettingSection( $asSection ) {} public function removeSettingSections( $sSectionID1=null, $sSectionID2=null, $_and_more=null ) {} public function addSettingFields( $aField1, $aField2=null, $_and_more=null ) {} public function addSettingField( $asField ) {} public function removeSettingFields( $sFieldID1, $sFieldID2=null, $_and_more ) {} public function setFieldErrors( $aErrors, $sID=null, $iLifeSpan=300 ) {} public function getFieldValue( $sFieldID ) {} public function __call( $sMethodName, $aArgs=null ) { $sPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null; $sTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProp->getDefaultInPageTab( $sPageSlug ); if ( substr( $sMethodName, 0, strlen( 'section_pre_' ) ) == 'section_pre_' ) return $this->_renderSectionDescription( $sMethodName ); if ( substr( $sMethodName, 0, strlen( 'field_pre_' ) ) == 'field_pre_' ) return $this->_renderSettingField( $aArgs[ 0 ], $sPageSlug ); if ( substr( $sMethodName, 0, strlen( 'validation_pre_' ) ) == 'validation_pre_' ) return $this->_doValidationCall( $sMethodName, $aArgs[ 0 ] ); if ( substr( $sMethodName, 0, strlen( 'load_pre_' ) ) == 'load_pre_' ) return $this->_doPageLoadCall( substr( $sMethodName, strlen( 'load_pre_' ) ), $sTabSlug, $aArgs[ 0 ] ); if ( $sMethodName == $this->oProp->sClassHash . '_page_' . $sPageSlug ) return $this->_renderPage( $sPageSlug, $sTabSlug ); if ( $this->_isFrameworkCallbackMethod( $sMethodName ) ) return isset( $aArgs[0] ) ? $aArgs[0] : null; trigger_error( 'Admin Page Framework: ' . ' : ' . sprintf( __( 'The method is not defined: %1$s', $this->oProp->sTextDomain ), $sMethodName ), E_USER_ERROR ); } private function _isFrameworkCallbackMethod( $sMethodName ) { foreach( self::$_aHookPrefixes as $sPrefix ) if ( substr( $sMethodName, 0, strlen( $sPrefix ) ) == $sPrefix ) return true; return false; } protected function _doPageLoadCall( $sPageSlug, $sTabSlug, $aArg ) { $this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( "load_", $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ) ); } public function _sortByOrder( $a, $b ) { return isset( $a['order'], $b['order'] ) ? $a['order'] - $b['order'] : 1; } protected function _isInThePage( $aPageSlugs=array() ) { if ( in_array( $GLOBALS['pagenow'], array( 'options.php' ) ) ) { return true; } if ( ! isset( $_GET['page'] ) ) return false; if ( empty( $aPageSlugs ) ) { return $this->oProp->isPageAdded(); } return ( in_array( $_GET['page'], $aPageSlugs ) ); } } endif;if ( ! class_exists( 'AdminPageFramework_Page_MetaBox' ) ) : abstract class AdminPageFramework_Page_MetaBox extends AdminPageFramework_Base { function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) { add_action( 'admin_head', array( $this, '_replyToEnableMetaBox' ) ); add_filter( 'screen_layout_columns', array( $this, '_replyToSetNumberOfScreenLayoutColumns'), 10, 2 ); parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain ); } protected function _printMetaBox( $sContext, $iContainerID ) { if ( ! isset( $GLOBALS['wp_meta_boxes'][ $GLOBALS['page_hook'] ][ $sContext ] ) || count( $GLOBALS['wp_meta_boxes'][ $GLOBALS['page_hook'] ][ $sContext ] ) <= 0 ) return; echo "<div id='postbox-container-{$iContainerID}' class='postbox-container'>"; do_meta_boxes( '', $sContext, null ); echo "</div>"; } protected function _getNumberOfColumns() { if ( isset( $GLOBALS['wp_meta_boxes'][ $GLOBALS['page_hook'] ][ 'side' ] ) && count( $GLOBALS['wp_meta_boxes'][ $GLOBALS['page_hook'] ][ 'side' ] ) > 0 ) return 2; return 1; return 1 == get_current_screen()->get_columns() ? '1' : '2'; } public function _replyToSetNumberOfScreenLayoutColumns( $aColumns, $sScreenID ) { if ( ! isset( $GLOBALS['page_hook'] ) ) return; if ( ! $this->_isMetaBoxAdded() ) return; if ( ! $this->oProp->isPageAdded() ) return; add_filter( 'get_user_option_' . 'screen_layout_' . $GLOBALS['page_hook'], array( $this, '_replyToReturnDefaultNumberOfScreenColumns' ), 10, 3 ); if ( $sScreenID == $GLOBALS['page_hook'] ) $aColumns[ $GLOBALS['page_hook'] ] = 2; return $aColumns; } private function _isMetaBoxAdded( $sPageSlug='' ) { if ( ! isset( $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] ) ) return false; if ( ! is_array( $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] ) ) return false; $sPageSlug = $sPageSlug ? $sPageSlug : ( isset( $_GET['page'] ) ? $_GET['page'] : '' ); if ( ! $sPageSlug ) return false; foreach( $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] as $sClassName => $oMetaBox ) if ( $this->_isPageOfMetaBox( $sPageSlug, $oMetaBox ) ) return true; return false; } private function _isPageOfMetaBox( $sPageSlug, $oMetaBox ) { if ( in_array( $sPageSlug , $oMetaBox->oProp->aPageSlugs ) ) return true; if ( ! array_key_exists( $sPageSlug , $oMetaBox->oProp->aPageSlugs ) ) return false; $aTabs = $oMetaBox->oProp->aPageSlugs[ $sPageSlug ]; $sCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : ( isset( $_GET['page'] ) ? $this->oProp->getDefaultInPageTab( $_GET['page'] ) : '' ); if ( $sCurrentTabSlug && in_array( $sCurrentTabSlug, $aTabs ) ) return true; return false; } public function _replyToReturnDefaultNumberOfScreenColumns( $vStoredData, $sOptionKey, $oUser ) { if ( $sOptionKey != 'screen_layout_' . $GLOBALS['page_hook'] ) return $vStoredData; return ( $vStoredData ) ? $vStoredData : $this->_getNumberOfColumns(); } public function _replyToEnableMetaBox() { if ( ! $this->oProp->isPageAdded() ) return; if ( ! $this->_isMetaBoxAdded() ) return; $oScreen = get_current_screen(); $sScreenID = $oScreen->id; do_action( "add_meta_boxes_{$sScreenID}", null ); do_action( 'add_meta_boxes', $sScreenID, null ); wp_enqueue_script( 'postbox' ); add_action( "admin_footer-{$sScreenID}", array( $this, '_replyToAddMetaboxScript' ) ); } public function _replyToAddMetaboxScript() { if ( isset( $GLOBALS['aAdminPageFramework']['bAddedMetaBoxScript'] ) ) return; $GLOBALS['aAdminPageFramework']['bAddedMetaBoxScript'] = true; ?>
			<script class="admin-page-framework-insert-metabox-script">
				jQuery( document).ready( function(){ postboxes.add_postbox_toggles( pagenow ); });
			</script>
			<?php
 } } endif;if ( ! class_exists( 'AdminPageFramework_Page' ) ) : abstract class AdminPageFramework_Page extends AdminPageFramework_Page_MetaBox { protected static $_aScreenIconIDs = array( 'edit', 'post', 'index', 'media', 'upload', 'link-manager', 'link', 'link-category', 'edit-pages', 'page', 'edit-comments', 'themes', 'plugins', 'users', 'profile', 'user-edit', 'tools', 'admin', 'options-general', 'ms-admin', 'generic', ); private static $_aStructure_InPageTabElements = array( 'page_slug' => null, 'tab_slug' => null, 'title' => null, 'order' => null, 'show_in_page_tab' => true, 'parent_tab_slug' => null, ); function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) { add_action( 'admin_menu', array( $this, '_replyToFinalizeInPageTabs' ), 99 ); parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain ); } public function addInPageTabs( $aTab1, $aTab2=null, $_and_more=null ) { foreach( func_get_args() as $asTab ) $this->addInPageTab( $asTab ); } public function addInPageTab( $asInPageTab ) { static $__sTargetPageSlug; if ( ! is_array( $asInPageTab ) ) { $__sTargetPageSlug = is_string( $asInPageTab ) ? $asInPageTab : $__sTargetPageSlug; return; } $aInPageTab = $this->oUtil->uniteArrays( $asInPageTab, self::$_aStructure_InPageTabElements, array( 'page_slug' => $__sTargetPageSlug ) ); $__sTargetPageSlug = $aInPageTab['page_slug']; if ( ! isset( $aInPageTab['page_slug'], $aInPageTab['tab_slug'] ) ) return; $iCountElement = isset( $this->oProp->aInPageTabs[ $aInPageTab['page_slug'] ] ) ? count( $this->oProp->aInPageTabs[ $aInPageTab['page_slug'] ] ) : 0; $aInPageTab = array( 'page_slug' => $this->oUtil->sanitizeSlug( $aInPageTab['page_slug'] ), 'tab_slug' => $this->oUtil->sanitizeSlug( $aInPageTab['tab_slug'] ), 'order' => is_numeric( $aInPageTab['order'] ) ? $aInPageTab['order'] : $iCountElement + 10, ) + $aInPageTab; $this->oProp->aInPageTabs[ $aInPageTab['page_slug'] ][ $aInPageTab['tab_slug'] ] = $aInPageTab; } public function setPageTitleVisibility( $bShow=true, $sPageSlug='' ) { $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug ); if ( $sPageSlug ) { $this->oProp->aPages[ $sPageSlug ]['show_page_title'] = $bShow; return; } $this->oProp->bShowPageTitle = $bShow; foreach( $this->oProp->aPages as &$aPage ) $aPage['show_page_title'] = $bShow; } public function setPageHeadingTabsVisibility( $bShow=true, $sPageSlug='' ) { $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug ); if ( $sPageSlug ) { $this->oProp->aPages[ $sPageSlug ]['show_page_heading_tabs'] = $bShow; return; } $this->oProp->bShowPageHeadingTabs = $bShow; foreach( $this->oProp->aPages as &$aPage ) $aPage['show_page_heading_tabs'] = $bShow; } public function setInPageTabsVisibility( $bShow=true, $sPageSlug='' ) { $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug ); if ( $sPageSlug ) { $this->oProp->aPages[ $sPageSlug ]['show_in_page_tabs'] = $bShow; return; } $this->oProp->bShowInPageTabs = $bShow; foreach( $this->oProp->aPages as &$aPage ) $aPage['show_in_page_tabs'] = $bShow; } public function setInPageTabTag( $sTag='h3', $sPageSlug='' ) { $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug ); if ( $sPageSlug ) { $this->oProp->aPages[ $sPageSlug ]['in_page_tab_tag'] = $sTag; return; } $this->oProp->sInPageTabTag = $sTag; foreach( $this->oProp->aPages as &$aPage ) $aPage['in_page_tab_tag'] = $sTag; } public function setPageHeadingTabTag( $sTag='h2', $sPageSlug='' ) { $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug ); if ( $sPageSlug ) { $this->oProp->aPages[ $sPageSlug ]['page_heading_tab_tag'] = $sTag; return; } $this->oProp->sPageHeadingTabTag = $sTag; foreach( $this->oProp->aPages as &$aPage ) $aPage[ $sPageSlug ]['page_heading_tab_tag'] = $sTag; } protected function _renderPage( $sPageSlug, $sTabSlug=null ) { $this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( 'do_before_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ) ); ?>
		<div class="wrap">
			<?php
 $sContentTop = $this->_getScreenIcon( $sPageSlug ); $sContentTop .= $this->_getPageHeadingTabs( $sPageSlug, $this->oProp->sPageHeadingTabTag ); $sContentTop .= $this->_getInPageTabs( $sPageSlug, $this->oProp->sInPageTabTag ); echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( 'content_foot_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), $sContentTop ); ?>
			<div class="admin-page-framework-container">	
				<?php
 $this->_showSettingsErrors(); $this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( 'do_form_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ) ); echo $this->_getFormOpeningTag(); ?>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-<?php echo $this->_getNumberOfColumns(); ?>">
					<?php
 $this->_printMainContent( $sPageSlug, $sTabSlug ); $this->_printMetaBox( 'side', 1 ); $this->_printMetaBox( 'normal', 2 ); $this->_printMetaBox( 'advanced', 3 ); ?>						
					</div><!-- #post-body -->	
				</div><!-- #poststuff -->
				
			<?php echo $this->_getFormClosingTag( $sPageSlug, $sTabSlug ); ?>
			</div><!-- .admin-page-framework-container -->
				
			<?php	 echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( 'content_bottom_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), '' ); ?>
		</div><!-- .wrap -->
		<?php
 $this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( 'do_after_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ) ); } private function _printMainContent( $sPageSlug, $sTabSlug ) { $_bIsSideMetaboxExist = ( isset( $GLOBALS['wp_meta_boxes'][ $GLOBALS['page_hook'] ][ 'side' ] ) && count( $GLOBALS['wp_meta_boxes'][ $GLOBALS['page_hook'] ][ 'side' ] ) > 0 ); echo "<!-- main admin page content -->"; echo "<div class='admin-page-framework-content'>"; if ( $_bIsSideMetaboxExist ) echo "<div id='post-body-content'>"; ob_start(); if ( $this->oProp->bEnableForm ) { settings_fields( $this->oProp->sOptionKey ); if ( $this->oForm->isPageAdded( $sPageSlug ) ) { $this->aFieldErrors = isset( $this->aFieldErrors ) ? $this->aFieldErrors : $this->_getFieldErrors( $sPageSlug ); $oFieldsTable = new AdminPageFramework_FormTable( $this->oProp->aFieldTypeDefinitions, $this->aFieldErrors, $this->oMsg ); $this->oForm->setCurrentPageSlug( $sPageSlug ); $this->oForm->setCurrentTabSlug( $sTabSlug ); $this->oForm->applyConditions(); $this->oForm->applyFiltersToFields( $this, $this->oProp->sClassName ); $this->oForm->setDynamicElements( $this->oProp->aOptions ); echo $oFieldsTable->getFormTables( $this->oForm->aConditionedSections, $this->oForm->aConditionedFields, array( $this, '_replyToGetSectionHeaderOutput' ), array( $this, '_replyToGetFieldOutput' ) ); } } $sContent = ob_get_contents(); ob_end_clean(); echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( 'content_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), $sContent ); $this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( 'do_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ) ); if ( $_bIsSideMetaboxExist ) echo "</div><!-- #post-body-content -->"; echo "</div><!-- .admin-page-framework-content -->"; } private function _getFormOpeningTag() { return $this->oProp->bEnableForm ? "<form action='options.php' method='post' enctype='{$this->oProp->sFormEncType}' id='admin-page-framework-form'>" : ""; } private function _getFormClosingTag( $sPageSlug, $sTabSlug ) { return $this->oProp->bEnableForm ? "<input type='hidden' name='page_slug' value='{$sPageSlug}' />" . PHP_EOL . "<input type='hidden' name='tab_slug' value='{$sTabSlug}' />" . PHP_EOL . "<input type='hidden' name='_is_admin_page_framework' value='1' />" . PHP_EOL . "</form><!-- End Form -->" : ''; } private function _showSettingsErrors() { if ( $GLOBALS['pagenow'] == 'options-general.php' ) return; $aSettingsMessages = get_settings_errors( $this->oProp->sOptionKey ); if ( count( $aSettingsMessages ) > 1 ) $this->_removeDefaultSettingsNotice(); settings_errors( $this->oProp->sOptionKey ); } private function _removeDefaultSettingsNotice() { global $wp_settings_errors; $aDefaultMessages = array( $this->oMsg->__( 'option_cleared' ), $this->oMsg->__( 'option_updated' ), ); foreach ( ( array ) $wp_settings_errors as $iIndex => $aDetails ) { if ( $aDetails['setting'] != $this->oProp->sOptionKey ) continue; if ( in_array( $aDetails['message'], $aDefaultMessages ) ) unset( $wp_settings_errors[ $iIndex ] ); } } private function _getScreenIcon( $sPageSlug ) { if ( isset( $this->oProp->aPages[ $sPageSlug ]['href_icon_32x32'] ) ) return '<div class="icon32" style="background-image: url(' . $this->oProp->aPages[ $sPageSlug ]['href_icon_32x32'] . ');"><br /></div>'; if ( isset( $this->oProp->aPages[ $sPageSlug ]['screen_icon_id'] ) ) return '<div class="icon32" id="icon-' . $this->oProp->aPages[ $sPageSlug ]['screen_icon_id'] . '"><br /></div>'; $oScreen = get_current_screen(); $sIconIDAttribute = $this->_getScreenIDAttribute( $oScreen ); $sClass = 'icon32'; if ( empty( $sIconIDAttribute ) && $oScreen->post_type ) $sClass .= ' ' . sanitize_html_class( 'icon32-posts-' . $oScreen->post_type ); if ( empty( $sIconIDAttribute ) || $sIconIDAttribute == $this->oProp->sClassName ) $sIconIDAttribute = 'generic'; return '<div id="icon-' . $sIconIDAttribute . '" class="' . $sClass . '"><br /></div>'; } private function _getScreenIDAttribute( $oScreen ) { if ( ! empty( $oScreen->parent_base ) ) return $oScreen->parent_base; if ( 'page' == $oScreen->post_type ) return 'edit-pages'; return esc_attr( $oScreen->base ); } private function _getPageHeadingTabs( $sCurrentPageSlug, $sTag='h2', $aOutput=array() ) { if ( ! $this->oProp->aPages[ $sCurrentPageSlug ][ 'show_page_title' ] ) return ""; $sTag = $this->oProp->aPages[ $sCurrentPageSlug ][ 'page_heading_tab_tag' ] ? $this->oProp->aPages[ $sCurrentPageSlug ][ 'page_heading_tab_tag' ] : $sTag; if ( ! $this->oProp->aPages[ $sCurrentPageSlug ][ 'show_page_heading_tabs' ] || count( $this->oProp->aPages ) == 1 ) return "<{$sTag}>" . $this->oProp->aPages[ $sCurrentPageSlug ]['title'] . "</{$sTag}>"; foreach( $this->oProp->aPages as $aSubPage ) { if ( isset( $aSubPage['page_slug'] ) && $aSubPage['show_page_heading_tab'] ) { $sClassActive = $sCurrentPageSlug == $aSubPage['page_slug'] ? 'nav-tab-active' : ''; $aOutput[] = "<a class='nav-tab {$sClassActive}' " . "href='" . $this->oUtil->getQueryAdminURL( array( 'page' => $aSubPage['page_slug'], 'tab' => false ), $this->oProp->aDisallowedQueryKeys ) . "'>" . $aSubPage['title'] . "</a>"; } if ( isset( $aSubPage['href'] ) && $aSubPage['type'] == 'link' && $aSubPage['show_page_heading_tab'] ) $aOutput[] = "<a class='nav-tab link' " . "href='{$aSubPage['href']}'>" . $aSubPage['title'] . "</a>"; } return "<div class='admin-page-framework-page-heading-tab'><{$sTag} class='nav-tab-wrapper'>" . implode( '', $aOutput ) . "</{$sTag}></div>"; } private function _getInPageTabs( $sCurrentPageSlug, $sTag='h3', $aOutput=array() ) { if ( empty( $this->oProp->aInPageTabs[ $sCurrentPageSlug ] ) ) return implode( '', $aOutput ); $sCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProp->getDefaultInPageTab( $sCurrentPageSlug ); $sCurrentTabSlug = $this->_getParentTabSlug( $sCurrentPageSlug, $sCurrentTabSlug ); $sTag = $this->oProp->aPages[ $sCurrentPageSlug ][ 'in_page_tab_tag' ] ? $this->oProp->aPages[ $sCurrentPageSlug ][ 'in_page_tab_tag' ] : $sTag; if ( ! $this->oProp->aPages[ $sCurrentPageSlug ][ 'show_in_page_tabs' ] ) return isset( $this->oProp->aInPageTabs[ $sCurrentPageSlug ][ $sCurrentTabSlug ]['title'] ) ? "<{$sTag}>{$this->oProp->aInPageTabs[ $sCurrentPageSlug ][ $sCurrentTabSlug ]['title']}</{$sTag}>" : ""; foreach( $this->oProp->aInPageTabs[ $sCurrentPageSlug ] as $sTabSlug => $aInPageTab ) { if ( ! $aInPageTab['show_in_page_tab'] && ! isset( $aInPageTab['parent_tab_slug'] ) ) continue; $sInPageTabSlug = isset( $aInPageTab['parent_tab_slug'], $this->oProp->aInPageTabs[ $sCurrentPageSlug ][ $aInPageTab['parent_tab_slug'] ] ) ? $aInPageTab['parent_tab_slug'] : $aInPageTab['tab_slug']; $bIsActiveTab = ( $sCurrentTabSlug == $sInPageTabSlug ); $aOutput[ $sInPageTabSlug ] = "<a class='nav-tab " . ( $bIsActiveTab ? "nav-tab-active" : "" ) . "' " . "href='" . $this->oUtil->getQueryAdminURL( array( 'page' => $sCurrentPageSlug, 'tab' => $sInPageTabSlug ), $this->oProp->aDisallowedQueryKeys ) . "'>" . $this->oProp->aInPageTabs[ $sCurrentPageSlug ][ $sInPageTabSlug ]['title'] . "</a>"; } return empty( $aOutput ) ? "" : "<div class='admin-page-framework-in-page-tab'><{$sTag} class='nav-tab-wrapper in-page-tab'>" . implode( '', $aOutput ) . "</{$sTag}></div>"; } private function _getParentTabSlug( $sPageSlug, $sTabSlug ) { $sParentTabSlug = isset( $this->oProp->aInPageTabs[ $sPageSlug ][ $sTabSlug ]['parent_tab_slug'] ) ? $this->oProp->aInPageTabs[ $sPageSlug ][ $sTabSlug ]['parent_tab_slug'] : $sTabSlug; return isset( $this->oProp->aInPageTabs[ $sPageSlug ][ $sParentTabSlug ]['show_in_page_tab'] ) && $this->oProp->aInPageTabs[ $sPageSlug ][ $sParentTabSlug ]['show_in_page_tab'] ? $sParentTabSlug : ''; } public function _replyToFinalizeInPageTabs() { if ( ! $this->oProp->isPageAdded() ) return; foreach( $this->oProp->aPages as $sPageSlug => $aPage ) { if ( ! isset( $this->oProp->aInPageTabs[ $sPageSlug ] ) ) continue; $this->oProp->aInPageTabs[ $sPageSlug ] = $this->oUtil->addAndApplyFilter( $this, "tabs_{$this->oProp->sClassName}_{$sPageSlug}", $this->oProp->aInPageTabs[ $sPageSlug ] ); foreach( $this->oProp->aInPageTabs[ $sPageSlug ] as &$aInPageTab ) $aInPageTab = $aInPageTab + self::$_aStructure_InPageTabElements; uasort( $this->oProp->aInPageTabs[ $sPageSlug ], array( $this, '_sortByOrder' ) ); foreach( $this->oProp->aInPageTabs[ $sPageSlug ] as $sTabSlug => &$aInPageTab ) { if ( ! isset( $aInPageTab['tab_slug'] ) ) continue; $this->oProp->aDefaultInPageTabs[ $sPageSlug ] = $aInPageTab['tab_slug']; break; } } } } endif;if ( ! class_exists( 'AdminPageFramework_Menu' ) ) : abstract class AdminPageFramework_Menu extends AdminPageFramework_Page { protected static $_aBuiltInRootMenuSlugs = array( 'dashboard' => 'index.php', 'posts' => 'edit.php', 'media' => 'upload.php', 'links' => 'link-manager.php', 'pages' => 'edit.php?post_type=page', 'comments' => 'edit-comments.php', 'appearance' => 'themes.php', 'plugins' => 'plugins.php', 'users' => 'users.php', 'tools' => 'tools.php', 'settings' => 'options-general.php', 'network admin' => "network_admin_menu", ); protected static $_aStructure_SubMenuLinkForUser = array( 'type' => 'link', 'title' => null, 'href' => null, 'capability' => null, 'order' => null, 'show_page_heading_tab' => true, 'show_in_menu' => true, ); protected static $_aStructure_SubMenuPageForUser = array( 'type' => 'page', 'title' => null, 'page_slug' => null, 'screen_icon' => null, 'capability' => null, 'order' => null, 'show_page_heading_tab' => true, 'show_in_menu' => true, 'href_icon_32x32' => null, 'screen_icon_id' => null, 'show_page_title' => null, 'show_page_heading_tabs' => null, 'show_in_page_tabs' => null, 'in_page_tab_tag' => null, 'page_heading_tab_tag' => null, ); function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) { add_action( 'admin_menu', array( $this, '_replyToBuildMenu' ), 98 ); parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain ); } public function setRootMenuPage( $sRootMenuLabel, $sIcon16x16=null, $iMenuPosition=null ) { $sRootMenuLabel = trim( $sRootMenuLabel ); $sSlug = $this->_isBuiltInMenuItem( $sRootMenuLabel ); $this->oProp->aRootMenu = array( 'sTitle' => $sRootMenuLabel, 'sPageSlug' => $sSlug ? $sSlug : $this->oProp->sClassName, 'sIcon16x16' => $this->oUtil->resolveSRC( $sIcon16x16 ), 'iPosition' => $iMenuPosition, 'fCreateRoot' => $sSlug ? false : true, ); } private function _isBuiltInMenuItem( $sMenuLabel ) { $sMenuLabelLower = strtolower( $sMenuLabel ); if ( array_key_exists( $sMenuLabelLower, self::$_aBuiltInRootMenuSlugs ) ) return self::$_aBuiltInRootMenuSlugs[ $sMenuLabelLower ]; } public function setRootMenuPageBySlug( $sRootMenuSlug ) { $this->oProp->aRootMenu['sPageSlug'] = $sRootMenuSlug; $this->oProp->aRootMenu['fCreateRoot'] = false; } public function addSubMenuItems( $aSubMenuItem1, $aSubMenuItem2=null, $_and_more=null ) { foreach ( func_get_args() as $aSubMenuItem ) $this->addSubMenuItem( $aSubMenuItem ); } public function addSubMenuItem( array $aSubMenuItem ) { if ( isset( $aSubMenuItem['href'] ) ) $this->addSubMenuLink( $aSubMenuItem ); else $this->addSubMenuPage( $aSubMenuItem ); } protected function addSubMenuLink( array $aSubMenuLink ) { if ( ! isset( $aSubMenuLink['href'], $aSubMenuLink['title'] ) ) return; if ( ! filter_var( $aSubMenuLink['href'], FILTER_VALIDATE_URL ) ) return; $this->oProp->aPages[ $aSubMenuLink['href'] ] = $this->_formatSubmenuLinkArray( $aSubMenuLink ); } protected function addSubMenuPages() { foreach ( func_get_args() as $aSubMenuPage ) $this->addSubMenuPage( $aSubMenuPage ); } protected function addSubMenuPage( array $aSubMenuPage ) { if ( ! isset( $aSubMenuPage['page_slug'] ) ) return; $aSubMenuPage['page_slug'] = $this->oUtil->sanitizeSlug( $aSubMenuPage['page_slug'] ); $this->oProp->aPages[ $aSubMenuPage['page_slug'] ] = $this->_formatSubMenuPageArray( $aSubMenuPage ); } public function _replyToBuildMenu() { if ( $this->oProp->aRootMenu['fCreateRoot'] ) $this->_registerRootMenuPage(); $this->oProp->aPages = $this->oUtil->addAndApplyFilter( $this, "pages_{$this->oProp->sClassName}", $this->oProp->aPages ); uasort( $this->oProp->aPages, array( $this, '_sortByOrder' ) ); foreach ( $this->oProp->aPages as $aPage ) { if ( ! isset( $aPage['page_slug'] ) ) continue; $this->oProp->sDefaultPageSlug = $aPage['page_slug']; break; } foreach ( $this->oProp->aPages as &$aSubMenuItem ) { $aSubMenuItem = $this->_formatSubMenuItemArray( $aSubMenuItem ); $aSubMenuItem['_page_hook'] = $this->_registerSubMenuItem( $aSubMenuItem ); } if ( $this->oProp->aRootMenu['fCreateRoot'] ) remove_submenu_page( $this->oProp->aRootMenu['sPageSlug'], $this->oProp->aRootMenu['sPageSlug'] ); } private function _registerRootMenuPage() { $this->oProp->aRootMenu['_page_hook'] = add_menu_page( $this->oProp->sClassName, $this->oProp->aRootMenu['sTitle'], $this->oProp->sCapability, $this->oProp->aRootMenu['sPageSlug'], '', $this->oProp->aRootMenu['sIcon16x16'], isset( $this->oProp->aRootMenu['iPosition'] ) ? $this->oProp->aRootMenu['iPosition'] : null ); } private function _formatSubMenuItemArray( $aSubMenuItem ) { if ( isset( $aSubMenuItem['page_slug'] ) ) return $this->_formatSubMenuPageArray( $aSubMenuItem ); if ( isset( $aSubMenuItem['href'] ) ) return $this->_formatSubmenuLinkArray( $aSubMenuItem ); return array(); } private function _formatSubmenuLinkArray( $aSubMenuLink ) { if ( ! filter_var( $aSubMenuLink['href'], FILTER_VALIDATE_URL ) ) return array(); return $this->oUtil->uniteArrays( array( 'capability' => isset( $aSubMenuLink['capability'] ) ? $aSubMenuLink['capability'] : $this->oProp->sCapability, 'order' => isset( $aSubMenuLink['order'] ) && is_numeric( $aSubMenuLink['order'] ) ? $aSubMenuLink['order'] : count( $this->oProp->aPages ) + 10, ), $aSubMenuLink + self::$_aStructure_SubMenuLinkForUser ); } private function _formatSubMenuPageArray( $aSubMenuPage ) { $aSubMenuPage = $aSubMenuPage + self::$_aStructure_SubMenuPageForUser; $aSubMenuPage['screen_icon_id'] = trim( $aSubMenuPage['screen_icon_id'] ); return $this->oUtil->uniteArrays( array( 'href_icon_32x32' => $this->oUtil->resolveSRC( $aSubMenuPage['screen_icon'], true ), 'screen_icon_id' => in_array( $aSubMenuPage['screen_icon'], self::$_aScreenIconIDs ) ? $aSubMenuPage['screen_icon'] : 'generic', 'capability' => isset( $aSubMenuPage['capability'] ) ? $aSubMenuPage['capability'] : $this->oProp->sCapability, 'order' => is_numeric( $aSubMenuPage['order'] ) ? $aSubMenuPage['order'] : count( $this->oProp->aPages ) + 10, ), $aSubMenuPage, array( 'show_page_title' => $this->oProp->bShowPageTitle, 'show_page_heading_tabs' => $this->oProp->bShowPageHeadingTabs, 'show_in_page_tabs' => $this->oProp->bShowInPageTabs, 'in_page_tab_tag' => $this->oProp->sInPageTabTag, 'page_heading_tab_tag' => $this->oProp->sPageHeadingTabTag, ) ); } private function _registerSubMenuItem( $aArgs ) { $sType = $aArgs['type']; $sTitle = $sType == 'page' ? $aArgs['title'] : $aArgs['title']; $sCapability = isset( $aArgs['capability'] ) ? $aArgs['capability'] : $this->oProp->sCapability; $_sPageHook = ''; if ( ! current_user_can( $sCapability ) ) return; $sRootPageSlug = $this->oProp->aRootMenu['sPageSlug']; $sMenuLabel = plugin_basename( $sRootPageSlug ); if ( $sType == 'page' && isset( $aArgs['page_slug'] ) ) { $sPageSlug = $aArgs['page_slug']; $_sPageHook = add_submenu_page( $sRootPageSlug, $sTitle, $sTitle, $sCapability, $sPageSlug, array( $this, $this->oProp->sClassHash . '_page_' . $sPageSlug ) ); add_action( "load-" . $_sPageHook , array( $this, "load_pre_" . $sPageSlug ) ); if ( ! $aArgs['show_in_menu'] ) { foreach( ( array ) $GLOBALS['submenu'][ $sMenuLabel ] as $iIndex => $aSubMenu ) { if ( ! isset( $aSubMenu[ 3 ] ) ) continue; if ( $aSubMenu[0] == $sTitle && $aSubMenu[3] == $sTitle && $aSubMenu[2] == $sPageSlug ) { unset( $GLOBALS['submenu'][ $sMenuLabel ][ $iIndex ] ); $this->oProp->aHiddenPages[ $sPageSlug ] = $sTitle; add_filter( 'admin_title', array( $this, '_replyToFixPageTitleForHiddenPages' ), 10, 2 ); break; } } } } if ( $sType == 'link' && $aArgs['show_in_menu'] ) { if ( ! isset( $GLOBALS['submenu'][ $sMenuLabel ] ) ) $GLOBALS['submenu'][ $sMenuLabel ] = array(); $GLOBALS['submenu'][ $sMenuLabel ][] = array ( $sTitle, $sCapability, $aArgs['href'], ); } return $_sPageHook; } public function _replyToFixPageTitleForHiddenPages( $sAdminTitle, $sPageTitle ) { if ( isset( $_GET['page'], $this->oProp->aHiddenPages[ $_GET['page'] ] ) ) return $this->oProp->aHiddenPages[ $_GET['page'] ] . $sAdminTitle; return $sAdminTitle; } } endif;if ( ! class_exists( 'AdminPageFramework_Setting_Base' ) ) : abstract class AdminPageFramework_Setting_Base extends AdminPageFramework_Menu { protected $aFieldErrors; static protected $_sFieldsType = 'page'; protected $_sTargetPageSlug = null; protected $_sTargetTabSlug = null; protected $_sTargetSectionTabSlug = null; function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) { add_action( 'admin_menu', array( $this, '_replyToRegisterSettings' ), 100 ); add_action( 'admin_init', array( $this, '_replyToCheckRedirects' ) ); parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain ); $this->oForm = new AdminPageFramework_FormElement_Page( $this->oProp->sFieldsType, $this->oProp->sCapability ); } protected function _getFieldErrors( $sPageSlug, $bDelete=true ) { if ( ! isset( $_GET['settings-updated'] ) ) return array(); $_sTransient = md5( $this->oProp->sClassName . '_' . $sPageSlug ); $_aFieldErrors = get_transient( $_sTransient ); if ( $bDelete ) { delete_transient( $_sTransient ); } return ( array ) $_aFieldErrors; } public function _replyToCheckRedirects() { if ( ! ( isset( $_GET['page'] ) ) || ! $this->oProp->isPageAdded( $_GET['page'] ) ) return; if ( ! ( isset( $_GET['settings-updated'] ) && ! empty( $_GET['settings-updated'] ) ) ) return; $aError = $this->_getFieldErrors( $_GET['page'], false ); if ( ! empty( $aError ) ) return; $sTransient = md5( trim( "redirect_{$this->oProp->sClassName}_{$_GET['page']}" ) ); $sURL = get_transient( $sTransient ); if ( $sURL === false ) return; delete_transient( $sTransient ); die( wp_redirect( $sURL ) ); } public function _replyToRegisterSettings() { if ( ! $this->_isInThePage() ) return; $this->oForm->aSections = $this->oUtil->addAndApplyFilter( $this, "sections_{$this->oProp->sClassName}", $this->oForm->aSections ); foreach( $this->oForm->aFields as $_sSectionID => &$_aFields ) { $_aFields = $this->oUtil->addAndApplyFilter( $this, "fields_{$this->oProp->sClassName}_{$_sSectionID}", $_aFields ); unset( $_aFields ); } $this->oForm->aFields = $this->oUtil->addAndApplyFilter( $this, "fields_{$this->oProp->sClassName}", $this->oForm->aFields ); $this->oForm->setDefaultPageSlug( $this->oProp->sDefaultPageSlug ); $this->oForm->setOptionKey( $this->oProp->sOptionKey ); $this->oForm->setCallerClassName( $this->oProp->sClassName ); $this->oForm->format(); $this->oForm->setCurrentPageSlug( isset( $_GET['page'] ) && $_GET['page'] ? $_GET['page'] : '' ); $this->oForm->setCurrentTabSlug( $this->oProp->getCurrentTab() ); $this->oForm->applyConditions(); $this->oForm->setDynamicElements( $this->oProp->aOptions ); if ( $GLOBALS['pagenow'] != 'options.php' && ( count( $this->oForm->aConditionedFields ) == 0 ) ) return; new AdminPageFramework_FieldTypeRegistration( $this->oProp->aFieldTypeDefinitions, $this->oProp->sClassName, $this->oMsg ); $this->oProp->aFieldTypeDefinitions = $this->oUtil->addAndApplyFilter( $this, 'field_types_' . $this->oProp->sClassName, $this->oProp->aFieldTypeDefinitions ); foreach( $this->oForm->aConditionedSections as $_aSection ) { add_settings_section( $_aSection['section_id'], "<a id='{$_aSection['section_id']}'></a>" . $_aSection['title'], array( $this, 'section_pre_' . $_aSection['section_id'] ), $_aSection['page_slug'] ); if ( ! empty( $_aSection['help'] ) ) $this->addHelpTab( array( 'page_slug' => $_aSection['page_slug'], 'page_tab_slug' => $_aSection['tab_slug'], 'help_tab_title' => $_aSection['title'], 'help_tab_id' => $_aSection['section_id'], 'help_tab_content' => $_aSection['help'], 'help_tab_sidebar_content' => $_aSection['help_aside'] ? $_aSection['help_aside'] : "", ) ); } foreach( $this->oForm->aConditionedFields as $_sSectionID => $_aSubSectionOrFields ) { foreach( $_aSubSectionOrFields as $_sSubSectionIndexOrFieldID => $_aSubSectionOrField ) { if ( is_numeric( $_sSubSectionIndexOrFieldID ) && is_int( $_sSubSectionIndexOrFieldID + 0 ) ) { $_iSubSectionIndex = $_sSubSectionIndexOrFieldID; $_aSubSection = $_aSubSectionOrField; foreach( $_aSubSection as $__sFieldID => $__aField ) { add_settings_field( $__aField['section_id'] . '_' . $_iSubSectionIndex . '_' . $__aField['field_id'], "<a id='{$__aField['section_id']}_{$_iSubSectionIndex}_{$__aField['field_id']}'></a><span title='{$__aField['tip']}'>{$__aField['title']}</span>", null, $this->oForm->getPageSlugBySectionID( $__aField['section_id'] ), $__aField['section_id'] ); AdminPageFramework_FieldTypeRegistration::_setFieldHeadTagElements( $__aField, $this->oProp, $this->oHeadTag ); } continue; } $aField = $_aSubSectionOrField; add_settings_field( $aField['section_id'] . '_' . $aField['field_id'], "<a id='{$aField['section_id']}_{$aField['field_id']}'></a><span title='{$aField['tip']}'>{$aField['title']}</span>", null, $this->oForm->getPageSlugBySectionID( $aField['section_id'] ), $aField['section_id'] ); AdminPageFramework_FieldTypeRegistration::_setFieldHeadTagElements( $aField, $this->oProp, $this->oHeadTag ); if ( ! empty( $aField['help'] ) ) { $this->addHelpTab( array( 'page_slug' => $aField['page_slug'], 'page_tab_slug' => $aField['tab_slug'], 'help_tab_title' => $aField['section_title'], 'help_tab_id' => $aField['section_id'], 'help_tab_content' => "<span class='contextual-help-tab-title'>" . $aField['title'] . "</span> - " . PHP_EOL . $aField['help'], 'help_tab_sidebar_content' => $aField['help_aside'] ? $aField['help_aside'] : "", ) ); } } } $this->oProp->bEnableForm = true; register_setting( $this->oProp->sOptionKey, $this->oProp->sOptionKey, array( $this, 'validation_pre_' . $this->oProp->sClassName ) ); } public function _replyToGetSectionHeaderOutput( $sSectionDescription, $aSection ) { return $this->oUtil->addAndApplyFilters( $this, array( 'section_head_' . $this->oProp->sClassName . '_' . $aSection['section_id'] ), $sSectionDescription ); } public function _replyToGetFieldOutput( $aField ) { $_sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null; $_sSectionID = isset( $aField['section_id'] ) ? $aField['section_id'] : '_default'; $_sFieldID = $aField['field_id']; if ( $aField['page_slug'] != $_sCurrentPageSlug ) return ''; $this->aFieldErrors = isset( $this->aFieldErrors ) ? $this->aFieldErrors : $this->_getFieldErrors( $_sCurrentPageSlug ); $sFieldType = isset( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] ) && is_callable( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] ) ? $aField['type'] : 'default'; $oField = new AdminPageFramework_FormField( $aField, $this->oProp->aOptions, $this->aFieldErrors, $this->oProp->aFieldTypeDefinitions, $this->oMsg ); $sFieldOutput = $oField->_getFieldOutput(); unset( $oField ); return $this->oUtil->addAndApplyFilters( $this, array( isset( $aField['section_id'] ) && $aField['section_id'] != '_default' ? 'field_' . $this->oProp->sClassName . '_' . $aField['section_id'] . '_' . $_sFieldID : 'field_' . $this->oProp->sClassName . '_' . $_sFieldID, ), $sFieldOutput, $aField ); } } endif;if ( ! class_exists( 'AdminPageFramework_Setting' ) ) : abstract class AdminPageFramework_Setting extends AdminPageFramework_Setting_Base { public function setSettingNotice( $sMsg, $sType='error', $sID=null, $bOverride=true ) { $aWPSettingsErrors = isset( $GLOBALS['wp_settings_errors'] ) ? ( array ) $GLOBALS['wp_settings_errors'] : array(); $sID = isset( $sID ) ? $sID : $this->oProp->sOptionKey; foreach( $aWPSettingsErrors as $iIndex => $aSettingsError ) { if ( $aSettingsError['setting'] != $this->oProp->sOptionKey ) continue; if ( $aSettingsError['message'] == $sMsg ) return; if ( $aSettingsError['code'] === $sID ) { if ( ! $bOverride ) return; else unset( $aWPSettingsErrors[ $iIndex ] ); } } add_settings_error( $this->oProp->sOptionKey, $sID, $sMsg, $sType ); } public function addSettingSections( $aSection1, $aSection2=null, $_and_more=null ) { foreach( func_get_args() as $asSection ) $this->addSettingSection( $asSection ); $this->_sTargetTabSlug = null; $this->_sTargetSectionTabSlug = null; } public function addSettingSection( $asSection ) { if ( ! is_array( $asSection ) ) { $this->_sTargetPageSlug = is_string( $asSection ) ? $asSection : $this->_sTargetPageSlug; return; } $aSection = $asSection; $this->_sTargetPageSlug = isset( $aSection['page_slug'] ) ? $aSection['page_slug'] : $this->_sTargetPageSlug; $this->_sTargetTabSlug = isset( $aSection['tab_slug'] ) ? $aSection['tab_slug'] : $this->_sTargetTabSlug; $this->_sTargetSectionTabSlug = isset( $aSection['section_tab_slug'] ) ? $aSection['section_tab_slug'] : $this->_sTargetSectionTabSlug; $aSection = $this->oUtil->uniteArrays( $aSection, array( 'page_slug' => $this->_sTargetPageSlug ? $this->_sTargetPageSlug : null, 'tab_slug' => $this->_sTargetTabSlug ? $this->_sTargetTabSlug : null, 'section_tab_slug' => $this->_sTargetSectionTabSlug ? $this->_sTargetSectionTabSlug : null, ) ); $aSection['page_slug'] = $aSection['page_slug'] ? $this->oUtil->sanitizeSlug( $aSection['page_slug'] ) : ( $this->oProp->sDefaultPageSlug ? $this->oProp->sDefaultPageSlug : null ); $aSection['tab_slug'] = $this->oUtil->sanitizeSlug( $aSection['tab_slug'] ); $aSection['section_tab_slug'] = $this->oUtil->sanitizeSlug( $aSection['section_tab_slug'] ); if ( ! $aSection['page_slug'] ) return; $this->oForm->addSection( $aSection ); } public function removeSettingSections( $sSectionID1=null, $sSectionID2=null, $_and_more=null ) { foreach( func_get_args() as $_sSectionID ) $this->oForm->removeSection( $_sSectionID ); } public function addSettingFields( $aField1, $aField2=null, $_and_more=null ) { foreach( func_get_args() as $aField ) $this->addSettingField( $aField ); } public function addSettingField( $asField ) { $this->oForm->addField( $asField ); } public function removeSettingFields( $sFieldID1, $sFieldID2=null, $_and_more ) { foreach( func_get_args() as $_sFieldID ) $this->oForm->removeField( $_sFieldID ); } public function setFieldErrors( $aErrors, $sID=null, $iLifeSpan=300 ) { $sID = isset( $sID ) ? $sID : ( isset( $_POST['page_slug'] ) ? $_POST['page_slug'] : ( isset( $_GET['page'] ) ? $_GET['page'] : $this->oProp->sClassName ) ); set_transient( md5( $this->oProp->sClassName . '_' . $sID ), $aErrors, $iLifeSpan ); } public function getFieldValue( $sFieldID, $sSectionID='' ) { $_aOptions = $this->oUtil->uniteArrays( $this->oProp->aOptions, $this->oProp->getDefaultOptions( $this->oForm->aFields ) ); if ( ! $sSectionID ) { if ( array_key_exists( $sFieldID, $_aOptions ) ) return $_aOptions[ $sFieldID ]; foreach( $_aOptions as $aOptions ) { if ( array_key_exists( $sFieldID, $aOptions ) ) return $aOptions[ $sFieldID ]; } } if ( $sSectionID ) if ( array_key_exists( $sSectionID, $_aOptions ) && array_key_exists( $sFieldID, $_aOptions[ $sSectionID ] ) ) return $_aOptions[ $sSectionID ][ $sFieldID ]; return null; } } endif;if ( ! class_exists( 'AdminPageFramework_Setting_Port' ) ) : abstract class AdminPageFramework_Setting_Port extends AdminPageFramework_Setting { protected function _importOptions( $aStoredOptions, $sPageSlug, $sTabSlug ) { $oImport = new AdminPageFramework_ImportOptions( $_FILES['__import'], $_POST['__import'] ); $sSectionID = $oImport->getSiblingValue( 'section_id' ); $sPressedFieldID = $oImport->getSiblingValue( 'field_id' ); $sPressedInputID = $oImport->getSiblingValue( 'input_id' ); $bMerge = $oImport->getSiblingValue( 'is_merge' ); if ( $oImport->getError() > 0 ) { $this->setSettingNotice( $this->oMsg->__( 'import_error' ) ); return $aStoredOptions; } $aMIMEType = $this->oUtil->addAndApplyFilters( $this, array( "import_mime_types_{$this->oProp->sClassName}_{$sPressedInputID}", $sSectionID ? "import_mime_types_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "import_mime_types_{$this->oProp->sClassName}_{$sPressedFieldID}", $sSectionID ? "import_mime_types_{$this->oProp->sClassName}_{$sSectionID}" : null, $sTabSlug ? "import_mime_types_{$sPageSlug}_{$sTabSlug}" : null, "import_mime_types_{$sPageSlug}", "import_mime_types_{$this->oProp->sClassName}" ), array( 'text/plain', 'application/octet-stream' ), $sPressedFieldID, $sPressedInputID ); $_sType = $oImport->getType(); if ( ! in_array( $oImport->getType(), $aMIMEType ) ) { $this->setSettingNotice( sprintf( $this->oMsg->__( 'uploaded_file_type_not_supported' ), $_sType ) ); return $aStoredOptions; } $vData = $oImport->getImportData(); if ( $vData === false ) { $this->setSettingNotice( $this->oMsg->__( 'could_not_load_importing_data' ) ); return $aStoredOptions; } $sFormatType = $this->oUtil->addAndApplyFilters( $this, array( "import_format_{$this->oProp->sClassName}_{$sPressedInputID}", $sSectionID ? "import_format_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "import_format_{$this->oProp->sClassName}_{$sPressedFieldID}", $sSectionID ? "import_format_{$this->oProp->sClassName}_{$sSectionID}" : null, $sTabSlug ? "import_format_{$sPageSlug}_{$sTabSlug}" : null, "import_format_{$sPageSlug}", "import_format_{$this->oProp->sClassName}" ), $oImport->getFormatType(), $sPressedFieldID, $sPressedInputID ); $oImport->formatImportData( $vData, $sFormatType ); $sImportOptionKey = $this->oUtil->addAndApplyFilters( $this, array( "import_option_key_{$this->oProp->sClassName}_{$sPressedInputID}", $sSectionID ? "import_option_key_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "import_option_key_{$this->oProp->sClassName}_{$sPressedFieldID}", $sSectionID ? "import_option_key_{$this->oProp->sClassName}_{$sSectionID}" : null, $sTabSlug ? "import_option_key_{$sPageSlug}_{$sTabSlug}" : null, "import_option_key_{$sPageSlug}", "import_option_key_{$this->oProp->sClassName}" ), $oImport->getSiblingValue( 'option_key' ), $sPressedFieldID, $sPressedInputID ); $vData = $this->oUtil->addAndApplyFilters( $this, array( "import_{$this->oProp->sClassName}_{$sPressedInputID}", $sSectionID ? "import_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "import_{$this->oProp->sClassName}_{$sPressedFieldID}", $sSectionID ? "import_{$this->oProp->sClassName}_{$sSectionID}" : null, $sTabSlug ? "import_{$sPageSlug}_{$sTabSlug}" : null, "import_{$sPageSlug}", "import_{$this->oProp->sClassName}" ), $vData, $aStoredOptions, $sPressedFieldID, $sPressedInputID, $sFormatType, $sImportOptionKey, $bMerge ); $bEmpty = empty( $vData ); $this->setSettingNotice( $bEmpty ? $this->oMsg->__( 'not_imported_data' ) : $this->oMsg->__( 'imported_data' ), $bEmpty ? 'error' : 'updated', $this->oProp->sOptionKey, false ); if ( $sImportOptionKey != $this->oProp->sOptionKey ) { update_option( $sImportOptionKey, $vData ); return $aStoredOptions; } return $bMerge ? $this->oUtil->unitArrays( $vData, $aStoredOptions ) : $vData; } protected function _exportOptions( $vData, $sPageSlug, $sTabSlug ) { $oExport = new AdminPageFramework_ExportOptions( $_POST['__export'], $this->oProp->sClassName ); $sSectionID = $oExport->getSiblingValue( 'section_id' ); $sPressedFieldID = $oExport->getSiblingValue( 'field_id' ); $sPressedInputID = $oExport->getSiblingValue( 'input_id' ); $vData = $oExport->getTransientIfSet( $vData ); $vData = $this->oUtil->addAndApplyFilters( $this, array( "export_{$this->oProp->sClassName}_{$sPressedInputID}", $sSectionID ? "export_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "export_{$this->oProp->sClassName}_{$sPressedFieldID}", $sTabSlug ? "export_{$sPageSlug}_{$sTabSlug}" : null, "export_{$sPageSlug}", "export_{$this->oProp->sClassName}" ), $vData, $sPressedFieldID, $sPressedInputID ); $sFileName = $this->oUtil->addAndApplyFilters( $this, array( "export_name_{$this->oProp->sClassName}_{$sPressedInputID}", "export_name_{$this->oProp->sClassName}_{$sPressedFieldID}", $sSectionID ? "export_name_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "export_name_{$this->oProp->sClassName}_{$sPressedFieldID}", $sTabSlug ? "export_name_{$sPageSlug}_{$sTabSlug}" : null, "export_name_{$sPageSlug}", "export_name_{$this->oProp->sClassName}" ), $oExport->getFileName(), $sPressedFieldID, $sPressedInputID ); $sFormatType = $this->oUtil->addAndApplyFilters( $this, array( "export_format_{$this->oProp->sClassName}_{$sPressedInputID}", "export_format_{$this->oProp->sClassName}_{$sPressedFieldID}", $sSectionID ? "export_format_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "export_format_{$this->oProp->sClassName}_{$sPressedFieldID}", $sTabSlug ? "export_format_{$sPageSlug}_{$sTabSlug}" : null, "export_format_{$sPageSlug}", "export_format_{$this->oProp->sClassName}" ), $oExport->getFormat(), $sPressedFieldID, $sPressedInputID ); $oExport->doExport( $vData, $sFileName, $sFormatType ); exit; } } endif;if ( ! class_exists( 'AdminPageFramework_Setting_Validation' ) ) : abstract class AdminPageFramework_Setting_Validation extends AdminPageFramework_Setting_Port { protected function _doValidationCall( $sMethodName, $aInput ) { if ( ! isset( $_POST['_is_admin_page_framework'] ) ) return $aInput; $_sTabSlug = isset( $_POST['tab_slug'] ) ? $_POST['tab_slug'] : ''; $_sPageSlug = isset( $_POST['page_slug'] ) ? $_POST['page_slug'] : ''; $_sPressedFieldID = isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'field_id' ) : ''; $_sPressedInputID = isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'input_id' ) : ''; $_sPressedInputName = isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'name' ) : ''; $_bIsReset = isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'is_reset' ) : ''; $_sKeyToReset = isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'reset_key' ) : ''; $_sSubmitSectionID = isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'section_id' ) : ''; $this->oUtil->addAndDoActions( $this, array( "submit_{$this->oProp->sClassName}_{$_sPressedInputID}", $_sSubmitSectionID ? "submit_{$this->oProp->sClassName}_{$_sSubmitSectionID}_{$_sPressedFieldID}" : "submit_{$this->oProp->sClassName}_{$_sPressedFieldID}", $_sSubmitSectionID ? "submit_{$this->oProp->sClassName}_{$_sSubmitSectionID}" : null, isset( $_POST['tab_slug'] ) ? "submit_{$this->oProp->sClassName}_{$_sPageSlug}_{$_sTabSlug}" : null, "submit_{$this->oProp->sClassName}_{sPageSlug}", "submit_{$this->oProp->sClassName}", ) ); if ( isset( $_POST['__import']['submit'], $_FILES['__import'] ) ) return $this->_importOptions( $this->oProp->aOptions, $_sPageSlug, $_sTabSlug ); if ( isset( $_POST['__export']['submit'] ) ) die( $this->_exportOptions( $this->oProp->aOptions, $_sPageSlug, $_sTabSlug ) ); if ( $_bIsReset ) return $this->_askResetOptions( $_sPressedInputName, $_sPageSlug, $_sSubmitSectionID ); if ( isset( $_POST['__submit'] ) && $_sLinkURL = $this->_getPressedSubmitButtonData( $_POST['__submit'], 'link_url' ) ) die( wp_redirect( $_sLinkURL ) ); if ( isset( $_POST['__submit'] ) && $_sRedirectURL = $this->_getPressedSubmitButtonData( $_POST['__submit'], 'redirect_url' ) ) $this->_setRedirectTransients( $_sRedirectURL ); $aInput = $this->_getFilteredOptions( $aInput, $_sPageSlug, $_sTabSlug ); if ( $_sKeyToReset ) { $aInput = $this->_resetOptions( $_sKeyToReset, $aInput ); } $_bEmpty = empty( $aInput ); $this->setSettingNotice( $_bEmpty ? $this->oMsg->__( 'option_cleared' ) : $this->oMsg->__( 'option_updated' ), $_bEmpty ? 'error' : 'updated', $this->oProp->sOptionKey, false ); return $aInput; } private function _askResetOptions( $sPressedInputName, $sPageSlug, $sSectionID ) { $aNameKeys = explode( '|', $sPressedInputName ); $sFieldID = $sSectionID ? $aNameKeys[ 2 ] : $aNameKeys[ 1 ]; $aErrors = array(); if ( $sSectionID ) $aErrors[ $sSectionID ][ $sFieldID ] = $this->oMsg->__( 'reset_options' ); else $aErrors[ $sFieldID ] = $this->oMsg->__( 'reset_options' ); $this->setFieldErrors( $aErrors ); set_transient( md5( "reset_confirm_" . $sPressedInputName ), $sPressedInputName, 60*2 ); $this->setSettingNotice( $this->oMsg->__( 'confirm_perform_task' ) ); return $this->oForm->getPageOptions( $this->oProp->aOptions, $sPageSlug ); } private function _resetOptions( $sKeyToReset, $aInput ) { if ( $sKeyToReset == 1 || $sKeyToReset === true ) { delete_option( $this->oProp->sOptionKey ); $this->setSettingNotice( $this->oMsg->__( 'option_been_reset' ) ); return array(); } unset( $this->oProp->aOptions[ trim( $sKeyToReset ) ], $aInput[ trim( $sKeyToReset ) ] ); update_option( $this->oProp->sOptionKey, $this->oProp->aOptions ); $this->setSettingNotice( $this->oMsg->__( 'specified_option_been_deleted' ) ); return $aInput; } private function _setRedirectTransients( $sURL ) { if ( empty( $sURL ) ) return; $sTransient = md5( trim( "redirect_{$this->oProp->sClassName}_{$_POST['page_slug']}" ) ); return set_transient( $sTransient, $sURL , 60*2 ); } private function _getPressedSubmitButtonData( $aPostElements, $sTargetKey='field_id' ) { foreach( $aPostElements as $sInputID => $aSubElements ) { $aNameKeys = explode( '|', $aSubElements[ 'name' ] ); if ( count( $aNameKeys ) == 2 && isset( $_POST[ $aNameKeys[0] ][ $aNameKeys[1] ] ) ) return isset( $aSubElements[ $sTargetKey ] ) ? $aSubElements[ $sTargetKey ] :null; if ( count( $aNameKeys ) == 3 && isset( $_POST[ $aNameKeys[0] ][ $aNameKeys[1] ][ $aNameKeys[2] ] ) ) return isset( $aSubElements[ $sTargetKey ] ) ? $aSubElements[ $sTargetKey ] :null; if ( count( $aNameKeys ) == 4 && isset( $_POST[ $aNameKeys[0] ][ $aNameKeys[1] ][ $aNameKeys[2] ][ $aNameKeys[3] ] ) ) return isset( $aSubElements[ $sTargetKey ] ) ? $aSubElements[ $sTargetKey ] :null; } return null; } private function _getFilteredOptions( $aInput, $sPageSlug, $sTabSlug ) { $aInput = is_array( $aInput ) ? $aInput : array(); $_aInputToParse = $aInput; $_aDefaultOptions = $this->oProp->getDefaultOptions( $this->oForm->aFields ); $_aOptions = $this->oUtil->uniteArrays( $this->oProp->aOptions, $_aDefaultOptions ); $_aTabOptions = array(); $_aDefaultOptions = $this->_removePageElements( $_aDefaultOptions, $sPageSlug, $sTabSlug ); $aInput = $this->oUtil->uniteArrays( $aInput, $this->oUtil->castArrayContents( $aInput, $_aDefaultOptions ) ); unset( $_aDefaultOptions ); $aInput = $this->_validateEachField( $aInput, $_aOptions, $_aInputToParse ); unset( $_aInputToParse ); $aInput = $this->_validateTabFields( $aInput, $_aOptions, $_aTabOptions, $sPageSlug, $sTabSlug ); $aInput = $this->_validatePageFields( $aInput, $_aOptions, $_aTabOptions, $sPageSlug, $sTabSlug ); return $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProp->sClassName}", $aInput, $_aOptions ); } private function _validateEachField( $aInput, $aOptions, $aInputToParse ) { foreach( $aInputToParse as $sID => $aSectionOrFields ) { if ( $this->oForm->isSection( $sID ) ) { foreach( $aSectionOrFields as $sFieldID => $aFields ) $aInput[ $sID ][ $sFieldID ] = $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProp->sClassName}_{$sID}_{$sFieldID}", $aInput[ $sID ][ $sFieldID ], isset( $aOptions[ $sID ][ $sFieldID ] ) ? $aOptions[ $sID ][ $sFieldID ] : null ); } $aInput[ $sID ] = $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProp->sClassName}_{$sID}", $aInput[ $sID ], isset( $aOptions[ $sID ] ) ? $aOptions[ $sID ] : null ); } return $aInput; } private function _validateTabFields( $aInput, $aOptions, & $aTabOptions, $sPageSlug, $sTabSlug ) { if ( ! ( $sTabSlug && $sPageSlug ) ) { return $aInput; } $_aTabOnlyOptions = $this->oForm->getTabOnlyOptions( $aOptions, $sPageSlug, $sTabSlug ); $aTabOptions = $this->oForm->getTabOptions( $aOptions, $sPageSlug, $sTabSlug ); $aTabOptions = $this->oUtil->addAndApplyFilter( $this, "validation_saved_options_{$sPageSlug}_{$sTabSlug}", $aTabOptions ); return $this->oUtil->uniteArrays( $this->oUtil->addAndApplyFilter( $this, "validation_{$sPageSlug}_{$sTabSlug}", $aInput, $aTabOptions ), $this->oUtil->invertCastArrayContents( $aTabOptions, $_aTabOnlyOptions ), $this->oForm->getOtherTabOptions( $aOptions, $sPageSlug, $sTabSlug ) ); } private function _validatePageFields( $aInput, $aOptions, $aTabOptions, $sPageSlug, $sTabSlug ) { if ( ! $sPageSlug ) { return $aInput; } $_aPageOptions = $this->oForm->getPageOptions( $aOptions, $sPageSlug ); $_aPageOptions = $this->oUtil->addAndApplyFilter( $this, "validation_saved_options_{$sPageSlug}", $_aPageOptions ); $aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$sPageSlug}", $aInput, $_aPageOptions ); $_aPageOptions = $sTabSlug && ! empty( $aTabOptions ) ? $this->oUtil->invertCastArrayContents( $_aPageOptions, $aTabOptions ) : ( ! $sTabSlug ? array() : $_aPageOptions ); return $this->oUtil->uniteArrays( $aInput, $_aPageOptions, $this->oUtil->invertCastArrayContents( $this->oForm->getOtherPageOptions( $aOptions, $sPageSlug ), $_aPageOptions ) ); } private function _removePageElements( $aOptions, $sPageSlug, $sTabSlug ) { if ( ! $sPageSlug && ! $sTabSlug ) return $aOptions; if ( $sTabSlug && $sPageSlug ) { return $this->oForm->getOtherTabOptions( $aOptions, $sPageSlug, $sTabSlug ); } return $this->oForm->getOtherPageOptions( $aOptions, $sPageSlug ); } } endif;if ( ! class_exists( 'AdminPageFramework' ) ) : abstract class AdminPageFramework extends AdminPageFramework_Setting_Validation { public function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ){ parent::__construct( $sOptionKey, $sCallerPath ? $sCallerPath : AdminPageFramework_Utility::getCallerScriptPath( __FILE__ ), $sCapability, $sTextDomain ); $this->oUtil->addAndDoAction( $this, 'start_' . $this->oProp->sClassName ); } public function setUp() {} public function addHelpTab( $aHelpTab ) { $this->oHelpPane->_addHelpTab( $aHelpTab ); } public function enqueueStyles( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueStyles( $aSRCs, $sPageSlug, $sTabSlug, $aCustomArgs ); } public function enqueueStyle( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueStyle( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs ); } public function enqueueScripts( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueScripts( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs ); } public function enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueScript( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs ); } public function addLinkToPluginDescription( $sTaggedLinkHTML1, $sTaggedLinkHTML2=null, $_and_more=null ) { $this->oLink->_addLinkToPluginDescription( func_get_args() ); } public function addLinkToPluginTitle( $sTaggedLinkHTML1, $sTaggedLinkHTML2=null, $_and_more=null ) { $this->oLink->_addLinkToPluginTitle( func_get_args() ); } public function setCapability( $sCapability ) { $this->oProp->sCapability = $sCapability; $this->oForm->sCapability = $sCapability; } public function setFooterInfoLeft( $sHTML, $bAppend=true ) { $this->oProp->aFooterInfo['sLeft'] = $bAppend ? $this->oProp->aFooterInfo['sLeft'] . PHP_EOL . $sHTML : $sHTML; } public function setFooterInfoRight( $sHTML, $bAppend=true ) { $this->oProp->aFooterInfo['sRight'] = $bAppend ? $this->oProp->aFooterInfo['sRight'] . PHP_EOL . $sHTML : $sHTML; } public function setAdminNotice( $sMessage, $sClassSelector='error', $sID='' ) { $sID = $sID ? $sID : md5( $sMessage ); $this->oProp->aAdminNotices[ md5( $sMessage ) ] = array( 'sMessage' => $sMessage, 'sClassSelector' => $sClassSelector, 'sID' => $sID, ); add_action( 'admin_notices', array( $this, '_replyToPrintAdminNotices' ) ); } public function _replyToPrintAdminNotices() { foreach( $this->oProp->aAdminNotices as $aAdminNotice ) echo "<div class='{$aAdminNotice['sClassSelector']}' id='{$aAdminNotice['sID']}' ><p>" . $aAdminNotice['sMessage'] . "</p></div>"; } public function setDisallowedQueryKeys( $asQueryKeys, $bAppend=true ) { if ( ! $bAppend ) { $this->oProp->aDisallowedQueryKeys = ( array ) $asQueryKeys; return; } $aNewQueryKeys = array_merge( ( array ) $asQueryKeys, $this->oProp->aDisallowedQueryKeys ); $aNewQueryKeys = array_filter( $aNewQueryKeys ); $aNewQueryKeys = array_unique( $aNewQueryKeys ); $this->oProp->aDisallowedQueryKeys = $aNewQueryKeys; } static public function getOption( $sOptionKey, $asKey=null , $vDefault=null ) { return AdminPageFramework_WPUtility::getOption( $sOptionKey,$asKey, $vDefault ); } } endif;if ( ! class_exists( 'AdminPageFramework_CustomSubmitFields' ) ) : abstract class AdminPageFramework_CustomSubmitFields { public function __construct( $aPostElement ) { $this->aPost = $aPostElement; $this->sInputID = $this->getInputID( $aPostElement['submit'] ); } protected function getElement( $aElement, $sInputID, $sElementKey='format' ) { return ( isset( $aElement[ $sInputID ][ $sElementKey ] ) ) ? $aElement[ $sInputID ][ $sElementKey ] : null; } public function getSiblingValue( $sKey ) { return $this->getElement( $this->aPost, $this->sInputID, $sKey ); } public function getInputID( $aSubmitElement ) { foreach( $aSubmitElement as $sInputID => $v ) { $this->sInputID = $sInputID; return $this->sInputID; } } } endif;if ( ! class_exists( 'AdminPageFramework_ExportOptions' ) ) : class AdminPageFramework_ExportOptions extends AdminPageFramework_CustomSubmitFields { public function __construct( $aPostExport, $sClassName ) { parent::__construct( $aPostExport ); $this->sClassName = $sClassName; $this->sFileName = $this->getElement( $aPostExport, $this->sInputID, 'file_name' ); $this->sFormatType = $this->getElement( $aPostExport, $this->sInputID, 'format' ); $this->bIsDataSet = $this->getElement( $aPostExport, $this->sInputID, 'transient' ); } public function getTransientIfSet( $vData ) { if ( $this->bIsDataSet ) { $_tmp = get_transient( md5( "{$this->sClassName}_{$this->sInputID}" ) ); if ( $_tmp !== false ) { $vData = $_tmp; } } return $vData; } public function getFileName() { return $this->sFileName; } public function getFormat() { return $this->sFormatType; } public function doExport( $vData, $sFileName=null, $sFormatType=null ) { $sFileName = isset( $sFileName ) ? $sFileName : $this->sFileName; $sFormatType = isset( $sFormatType ) ? $sFormatType : $this->sFormatType; header( 'Content-Description: File Transfer' ); header( 'Content-Disposition: attachment; filename=' . $sFileName ); switch ( strtolower( $sFormatType ) ) { case 'text': if ( is_array( $vData ) || is_object( $vData ) ) die( AdminPageFramework_Debug::getArray( $vData, null, false ) ); die( $vData ); case 'json': die( json_encode( ( array ) $vData ) ); case 'array': default: die( serialize( ( array ) $vData )); } } } endif;if ( ! class_exists( 'AdminPageFramework_ImportOptions' ) ) : class AdminPageFramework_ImportOptions extends AdminPageFramework_CustomSubmitFields { public function __construct( $aFilesImport, $aPostImport ) { parent::__construct( $aPostImport ); $this->aFilesImport = $aFilesImport; } private function getElementInFilesArray( $aFilesImport, $sInputID, $sElementKey='error' ) { $sElementKey = strtolower( $sElementKey ); return isset( $aFilesImport[ $sElementKey ][ $sInputID ] ) ? $aFilesImport[ $sElementKey ][ $sInputID ] : null; } public function getError() { return $this->getElementInFilesArray( $this->aFilesImport, $this->sInputID, 'error' ); } public function getType() { return $this->getElementInFilesArray( $this->aFilesImport, $this->sInputID, 'type' ); } public function getImportData() { $sFilePath = $this->getElementInFilesArray( $this->aFilesImport, $this->sInputID, 'tmp_name' ); $vData = file_exists( $sFilePath ) ? file_get_contents( $sFilePath, true ) : false; return $vData; } public function formatImportData( &$vData, $sFormatType=null ) { $sFormatType = isset( $sFormatType ) ? $sFormatType : $this->getFormatType(); switch ( strtolower( $sFormatType ) ) { case 'text': return; case 'json': $vData = json_decode( ( string ) $vData, true ); return; case 'array': default: $vData = maybe_unserialize( trim( $vData ) ); return; } } public function getFormatType() { $this->sFormatType = isset( $this->sFormatType ) && $this->sFormatType ? $this->sFormatType : $this->getElement( $this->aPost, $this->sInputID, 'format' ); return $this->sFormatType; } } endif;if ( ! class_exists( 'AdminPageFramework_Factory_Router' ) ) : abstract class AdminPageFramework_Factory_Router { public $oProp; protected $oDebug; protected $oUtil; protected $oMsg; protected $oForm; protected $oPageLoadInfo; protected $oHeadTag; protected $oHelpPane; protected $oLink; function __construct( $oProp ) { $this->oUtil = new AdminPageFramework_WPUtility; $this->oDebug = new AdminPageFramework_Debug; $this->oProp = $oProp; $this->oMsg = AdminPageFramework_Message::instantiate( $oProp->sTextDomain ); if ( $this->_isInThePage() ) : $this->oForm = new AdminPageFramework_FormElement( $oProp->sFieldsType, $oProp->sCapability ); $this->oHeadTag = $this->_getHeadTagInstance( $oProp ); $this->oHelpPane = $this->_getHelpPaneInstance( $oProp ); $this->oLink = $this->_getLinkInstancce( $oProp, $this->oMsg ); $this->oPageLoadInfo = $this->_getPageLoadInfoInstance( $oProp, $this->oMsg ); endif; } protected function _isInThePage() { return true; } protected function _getHeadTagInstance( $oProp ) { switch ( $oProp->sFieldsType ) { case 'page': return new AdminPageFramework_HeadTag_Page( $oProp ); case 'post_meta_box': return new AdminPageFramework_HeadTag_MetaBox( $oProp ); case 'page_meta_box': return new AdminPageFramework_HeadTag_MetaBox_Page( $oProp ); case 'post_type': return new AdminPageFramework_HeadTag_PostType( $oProp ); case 'taxonomy': return new AdminPageFramework_HeadTag_TaxonomyField( $oProp ); } } protected function _getHelpPaneInstance( $oProp ) { switch ( $oProp->sFieldsType ) { case 'page': return new AdminPageFramework_HelpPane_Page( $oProp ); case 'post_meta_box': return new AdminPageFramework_HelpPane_MetaBox( $oProp ); case 'page_meta_box': return new AdminPageFramework_HelpPane_MetaBox( $oProp ); case 'post_type': return null; case 'taxonomy': return new AdminPageFramework_HelpPane_TaxonomyField( $oProp ); } } protected function _getLinkInstancce( $oProp, $oMsg ) { switch ( $oProp->sFieldsType ) { case 'page': return null; case 'post_meta_box': return null; case 'page_meta_box': return null; case 'post_type': return new AdminPageFramework_Link_PostType( $oProp, $oMsg ); case 'taxonomy': return null; } } protected function _getPageLoadInfoInstance( $oProp, $oMsg ) { switch ( $oProp->sFieldsType ) { case 'page': return AdminPageFramework_PageLoadInfo_Page::instantiate( $oProp, $oMsg ); case 'post_meta_box': return null; case 'page_meta_box': return null; case 'post_type': return AdminPageFramework_PageLoadInfo_PostType::instantiate( $oProp, $oMsg ); case 'taxonomy': return null; } } function __call( $sMethodName, $aArgs=null ) { if ( $sMethodName == 'start_' . $this->oProp->sClassName ) return; if ( substr( $sMethodName, 0, strlen( 'section_head_' . $this->oProp->sClassName . '_' ) ) == 'section_head_' . $this->oProp->sClassName . '_' ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( 'field_' . $this->oProp->sClassName . '_' ) ) == 'field_' . $this->oProp->sClassName . '_' ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( "field_types_{$this->oProp->sClassName}" ) ) == "field_types_{$this->oProp->sClassName}" ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( "field_definition_{$this->oProp->sClassName}" ) ) == "field_definition_{$this->oProp->sClassName}" ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( "script_common_{$this->oProp->sClassName}" ) ) == "script_common_{$this->oProp->sClassName}" ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( "script_{$this->oProp->sClassName}" ) ) == "script_{$this->oProp->sClassName}" ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( "style_ie_common_{$this->oProp->sClassName}" ) ) == "style_ie_common_{$this->oProp->sClassName}" ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( "style_common_{$this->oProp->sClassName}" ) ) == "style_common_{$this->oProp->sClassName}" ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( "style_ie_{$this->oProp->sClassName}" ) ) == "style_ie_{$this->oProp->sClassName}" ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( "style_{$this->oProp->sClassName}" ) ) == "style_{$this->oProp->sClassName}" ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( "validation_{$this->oProp->sClassName}" ) ) == "validation_{$this->oProp->sClassName}" ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( "content_{$this->oProp->sClassName}" ) ) == "content_{$this->oProp->sClassName}" ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( "do_{$this->oProp->sClassName}" ) ) == "do_{$this->oProp->sClassName}" ) return; trigger_error( 'Admin Page Framework: ' . ' : ' . sprintf( __( 'The method is not defined: %1$s', $this->oProp->sTextDomain ), $sMethodName ), E_USER_ERROR ); } } endif;if ( ! class_exists( 'AdminPageFramework_Factory_Model' ) ) : abstract class AdminPageFramework_Factory_Model extends AdminPageFramework_Factory_Router { function __construct( $oProp ) { parent::__construct( $oProp ); } public function _loadDefaultFieldTypeDefinitions() { static $_aFieldTypeDefinitions = array(); if ( empty( $_aFieldTypeDefinitions ) ) { new AdminPageFramework_FieldTypeRegistration( $_aFieldTypeDefinitions, $this->oProp->sClassName, $this->oMsg ); } $this->oProp->aFieldTypeDefinitions = $this->oUtil->addAndApplyFilter( $this, 'field_types_' . $this->oProp->sClassName, $_aFieldTypeDefinitions ); } protected function _registerFields( array $aFields ) { foreach( $aFields as $_sSecitonID => $_aFields ) { $_bIsSubSectionLoaded = false; foreach( $_aFields as $_iSubSectionIndexOrFieldID => $_aSubSectionOrField ) { if ( is_numeric( $_iSubSectionIndexOrFieldID ) && is_int( $_iSubSectionIndexOrFieldID + 0 ) ) { if ( $_bIsSubSectionLoaded ) continue; $_bIsSubSectionLoaded = true; foreach( $_aSubSectionOrField as $_aField ) { $this->_registerField( $_aField ); } continue; } $_aField = $_aSubSectionOrField; $this->_registerField( $_aField ); } } } private function _registerField( array $aField ) { AdminPageFramework_FieldTypeRegistration::_setFieldHeadTagElements( $aField, $this->oProp, $this->oHeadTag ); if ( $aField['help'] ) { $this->oHelpPane->_addHelpTextForFormFields( $aField['title'], $aField['help'], $aField['help_aside'] ); } } protected function _getFieldErrors( $sID='', $bDelete=true ) { static $_aFieldErrors; $_sTransientKey = 'AdminPageFramework_FieldErrors'; $_sID = md5( $this->oProp->sClassName ); $_aFieldErrors = isset( $_aFieldErrors ) ? $_aFieldErrors : get_transient( $_sTransientKey ); if ( $bDelete ) { delete_transient( $_sTransientKey ); } return isset( $_aFieldErrors[ $_sID ] ) ? $_aFieldErrors[ $_sID ] : array(); } protected function _isValidationErrors() { if ( isset( $GLOBALS['aAdminPageFramework']['aFieldErrors'] ) && $GLOBALS['aAdminPageFramework']['aFieldErrors'] ) { return true; } return get_transient( 'AdminPageFramework_FieldErrors' ); } protected function _deleteFieldErrors() { delete_transient( 'AdminPageFramework_FieldErrors' ); } public function _replyToSaveFieldErrors() { if ( ! isset( $GLOBALS['aAdminPageFramework']['aFieldErrors'] ) ) return; set_transient( 'AdminPageFramework_FieldErrors', $GLOBALS['aAdminPageFramework']['aFieldErrors'], 300 ); } public function _replyToSaveNotices() { if ( ! isset( $GLOBALS['aAdminPageFramework']['aNotices'] ) ) return; if ( empty( $GLOBALS['aAdminPageFramework']['aNotices'] ) ) return; set_transient( 'AdminPageFramework_Notices', $GLOBALS['aAdminPageFramework']['aNotices'] ); } } endif;if ( ! class_exists( 'AdminPageFramework_Factory_View' ) ) : abstract class AdminPageFramework_Factory_View extends AdminPageFramework_Factory_Model { function __construct( $oProp ) { parent::__construct( $oProp ); if ( $this->_isInThePage() && 'admin-ajax.php' != $GLOBALS['pagenow'] ) { add_action( 'admin_notices', array( $this, '_replyToPrintSettingNotice' ) ); } } public function _replyToPrintSettingNotice() { static $_fIsLoaded; if ( $_fIsLoaded ) return; $_fIsLoaded = true; $_aNotices = get_transient( 'AdminPageFramework_Notices' ); if ( false === $_aNotices ) return; foreach ( ( array ) $_aNotices as $__aNotice ) { if ( ! isset( $__aNotice['aAttributes'], $__aNotice['sMessage'] ) ) continue; echo "<div " . $this->oUtil->generateAttributes( $__aNotice['aAttributes'] ). "><p>" . $__aNotice['sMessage'] . "</p></div>"; } delete_transient( 'AdminPageFramework_Notices' ); } public function _replyToGetFieldOutput( $aField ) { $_oField = new AdminPageFramework_FormField( $aField, $this->oProp->aOptions, $this->_getFieldErrors(), $this->oProp->aFieldTypeDefinitions, $this->oMsg ); return $this->oUtil->addAndApplyFilters( $this, array( 'field_' . $this->oProp->sClassName . '_' . $aField['field_id'] ), $_oField->_getFieldOutput(), $aField ); } } endif;if ( ! class_exists( 'AdminPageFramework_Factory_Controller' ) ) : abstract class AdminPageFramework_Factory_Controller extends AdminPageFramework_Factory_View { public function setUp() {} public function enqueueStyles( $aSRCs, $_vArg2=null ) {} public function enqueueStyle( $sSRC, $_vArg2=null ) {} public function enqueueScripts( $aSRCs, $_vArg2=null ) {} public function enqueueScript( $sSRC, $_vArg2=null ) {} public function addHelpText( $sHTMLContent, $sHTMLSidebarContent="" ) { $this->oHelpPane->_addHelpText( $sHTMLContent, $sHTMLSidebarContent ); } public function addSettingSections( $aSection1, $aSection2=null, $_and_more=null ) { foreach( func_get_args() as $asSection ) $this->addSettingSection( $asSection ); $this->_sTargetSectionTabSlug = null; } public function addSettingSection( $aSection ) { if ( ! is_array( $aSection ) ) return; $this->_sTargetSectionTabSlug = isset( $aSection['section_tab_slug'] ) ? $this->oUtil->sanitizeSlug( $aSection['section_tab_slug'] ) : $this->_sTargetSectionTabSlug; $aSection['section_tab_slug'] = $this->_sTargetSectionTabSlug ? $this->_sTargetSectionTabSlug : null; $this->oForm->addSection( $aSection ); } public function addSettingFields( $aField1, $aField2=null, $_and_more=null ) { foreach( func_get_args() as $aField ) $this->addSettingField( $aField ); } public function addSettingField( $asField ) { $this->oForm->addField( $asField ); } public function setFieldErrors( $aErrors ) { $GLOBALS['aAdminPageFramework']['aFieldErrors'] = ! isset( $GLOBALS['aAdminPageFramework']['aFieldErrors'] ) ? $GLOBALS['aAdminPageFramework']['aFieldErrors'] : array(); if ( empty( $GLOBALS['aAdminPageFramework']['aFieldErrors'] ) ) { add_action( 'shutdown', array( $this, '_replyToSaveFieldErrors' ) ); } $_sID = md5( $this->oProp->sClassName ); $GLOBALS['aAdminPageFramework']['aFieldErrors'][ $_sID ] = $aErrors; } public function setSettingNotice( $sMessage, $sType='error', $asAttributes=array(), $bOverride=true ) { $GLOBALS['aAdminPageFramework']['aNotices'] = ! isset( $GLOBALS['aAdminPageFramework']['aNotices'] ) ? $GLOBALS['aAdminPageFramework']['aNotices'] : array(); if ( empty( $GLOBALS['aAdminPageFramework']['aNotices'] ) ) { add_action( 'shutdown', array( $this, '_replyToSaveNotices' ) ); } $_sID = md5( trim( $sMessage ) ); if ( $bOverride || ! isset( $GLOBALS['aAdminPageFramework']['aNotices'][ $_sID ] ) ) { $GLOBALS['aAdminPageFramework']['aNotices'][ $_sID ] = array( 'sMessage' => $sMessage, 'aAttributes' => ( is_array( $asAttributes ) ? $asAttributes : array( 'id' => $asAttributes ) ) + array( 'class' => $sType, 'id' => $this->oProp->sClassName . '_' . $_sID, ), ); } } } endif;if ( ! class_exists( 'AdminPageFramework_Factory' ) ) : abstract class AdminPageFramework_Factory extends AdminPageFramework_Factory_Controller {} endif;if ( ! class_exists( 'AdminPageFramework_MetaBox_Base' ) ) : abstract class AdminPageFramework_MetaBox_Base extends AdminPageFramework_Factory { protected $oHeadTag; static protected $_sFieldsType; protected $_sTargetSectionTabSlug; function __construct( $sMetaBoxID, $sTitle, $asPostTypeOrScreenID=array( 'post' ), $sContext='normal', $sPriority='default', $sCapability='edit_posts', $sTextDomain='admin-page-framework' ) { if ( empty( $asPostTypeOrScreenID ) ) return; parent::__construct( isset( $this->oProp )? $this->oProp : new AdminPageFramework_Property_MetaBox( $this, get_class( $this ), $sCapability ) ); $this->oProp->sMetaBoxID = $this->oUtil->sanitizeSlug( $sMetaBoxID ); $this->oProp->sTitle = $sTitle; $this->oProp->sContext = $sContext; $this->oProp->sPriority = $sPriority; if ( $this->oProp->bIsAdmin ) { add_action( 'wp_loaded', array( $this, '_replyToDetermineToLoad' ) ); } } public function _replyToAddMetaBox() {} public function _replyToRegisterFormElements() {} public function _replyToDetermineToLoad() { if ( ! $this->_isInThePage() ) return; $this->_loadDefaultFieldTypeDefinitions(); $this->setUp(); add_action( 'current_screen', array( $this, '_replyToRegisterFormElements' ) ); add_action( 'add_meta_boxes', array( $this, '_replyToAddMetaBox' ) ); add_action( 'save_post', array( $this, '_replyToSaveMetaBoxFields' ) ); } public function _replyToPrintMetaBoxContents( $oPost, $vArgs ) { $_aOutput = array(); $_aOutput[] = wp_nonce_field( $this->oProp->sMetaBoxID, $this->oProp->sMetaBoxID, true, false ); $this->oForm->applyConditions(); $this->oForm->applyFiltersToFields( $this, $this->oProp->sClassName ); if ( isset( $this->oProp->aOptions ) ) $this->_setOptionArray( isset( $oPost->ID ) ? $oPost->ID : ( isset( $_GET['page'] ) ? $_GET['page'] : null ), $this->oForm->aConditionedFields ); $this->oForm->setDynamicElements( $this->oProp->aOptions ); $_oFieldsTable = new AdminPageFramework_FormTable( $this->oProp->aFieldTypeDefinitions, $this->_getFieldErrors(), $this->oMsg ); $_aOutput[] = $_oFieldsTable->getFormTables( $this->oForm->aConditionedSections, $this->oForm->aConditionedFields, array( $this, '_replyToGetSectionHeaderOutput' ), array( $this, '_replyToGetFieldOutput' ) ); $this->oUtil->addAndDoActions( $this, 'do_' . $this->oProp->sClassName ); echo $this->oUtil->addAndApplyFilters( $this, 'content_' . $this->oProp->sClassName, implode( PHP_EOL, $_aOutput ) ); } protected function _setOptionArray( $isPostIDOrPageSlug, $aFields ) { if ( ! is_array( $aFields ) ) return; if ( is_numeric( $isPostIDOrPageSlug ) && is_int( $isPostIDOrPageSlug + 0 ) ) : $_iPostID = $isPostIDOrPageSlug; foreach( $aFields as $_sSectionID => $_aFields ) { if ( $_sSectionID == '_default' ) { foreach( $_aFields as $_aField ) $this->oProp->aOptions[ $_aField['field_id'] ] = get_post_meta( $_iPostID, $_aField['field_id'], true ); } $this->oProp->aOptions[ $_sSectionID ] = get_post_meta( $_iPostID, $_sSectionID, true ); } endif; } public function _replyToGetSectionHeaderOutput( $sSectionDescription, $aSection ) { return $this->oUtil->addAndApplyFilters( $this, array( 'section_head_' . $this->oProp->sClassName . '_' . $aSection['section_id'] ), $sSectionDescription ); } public function _replyToSaveMetaBoxFields( $iPostID ) { if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return; if ( ! isset( $_POST[ $this->oProp->sMetaBoxID ] ) || ! wp_verify_nonce( $_POST[ $this->oProp->sMetaBoxID ], $this->oProp->sMetaBoxID ) ) return; if ( ! $iPostID ) return; if ( in_array( $_POST['post_type'], $this->oProp->aPostTypes ) && ( ! current_user_can( $this->oProp->sCapability, $iPostID ) ) ) return; $_aInput = $this->_getInputArray( $this->oForm->aFields, $this->oForm->aSections ); $_aSavedMeta = $this->oUtil->getSavedMetaArray( $iPostID, array_keys( $_aInput ) ); $_aInput = $this->oUtil->addAndApplyFilters( $this, "validation_{$this->oProp->sClassName}", $_aInput, $_aSavedMeta ); if ( $this->_isValidationErrors() ) { remove_action( 'save_post', array( $this, '_replyToSaveMetaBoxFields' ) ); $_sPreviousPostStatus = 'draft'; wp_update_post( array( 'ID' => $iPostID, 'post_status' => $_sPreviousPostStatus ) ); add_action( 'save_post', array( $this, '_replyToSaveMetaBoxFields' ) ); return; } $this->_updatePostMeta( $iPostID, $_aInput, $this->oForm->dropRepeatableElements( $_aSavedMeta ) ); } private function _updatePostMeta( $iPostID, array $aInput, array $aSavedMeta ) { foreach ( $aInput as $_sSectionOrFieldID => $_vValue ) { if ( is_null( $_vValue ) ) continue; $_vSavedValue = isset( $aSavedMeta[ $_sSectionOrFieldID ] ) ? $aSavedMeta[ $_sSectionOrFieldID ] : null; if ( $_vValue == $_vSavedValue ) continue; update_post_meta( $iPostID, $_sSectionOrFieldID, $_vValue ); } } protected function _getInputArray( array $aFieldDefinitionArrays, array $aSectionDefinitionArrays ) { $aInput = array(); foreach( $aFieldDefinitionArrays as $_sSectionID => $_aSubSectionsOrFields ) { if ( $_sSectionID == '_default' ) { $_aFields = $_aSubSectionsOrFields; foreach( $_aFields as $_aField ) { $aInput[ $_aField['field_id'] ] = isset( $_POST[ $_aField['field_id'] ] ) ? $_POST[ $_aField['field_id'] ] : null; } continue; } $aInput[ $_sSectionID ] = isset( $aInput[ $_sSectionID ] ) ? $aInput[ $_sSectionID ] : array(); if ( ! count( $this->oUtil->getIntegerElements( $_aSubSectionsOrFields ) ) ) { $_aFields = $_aSubSectionsOrFields; foreach( $_aFields as $_aField ) { $aInput[ $_sSectionID ][ $_aField['field_id'] ] = isset( $_POST[ $_sSectionID ][ $_aField['field_id'] ] ) ? $_POST[ $_sSectionID ][ $_aField['field_id'] ] : null; } continue; } foreach( $_POST[ $_sSectionID ] as $_iIndex => $_aFields ) { $aInput[ $_sSectionID ][ $_iIndex ] = isset( $_POST[ $_sSectionID ][ $_iIndex ] ) ? $_POST[ $_sSectionID ][ $_iIndex ] : null; } } return $aInput; } protected function _getSavedMetaArray( $iPostID, $aInputStructure ) { $_aSavedMeta = array(); foreach ( $aInputStructure as $_sSectionORFieldID => $_v ) { $_aSavedMeta[ $_sSectionORFieldID ] = get_post_meta( $iPostID, $_sSectionORFieldID, true ); } return $_aSavedMeta; } } endif;if ( ! class_exists( 'AdminPageFramework_MetaBox' ) ) : abstract class AdminPageFramework_MetaBox extends AdminPageFramework_MetaBox_Base { static protected $_sFieldsType = 'post_meta_box'; function __construct( $sMetaBoxID, $sTitle, $asPostTypeOrScreenID=array( 'post' ), $sContext='normal', $sPriority='default', $sCapability='edit_posts', $sTextDomain='admin-page-framework' ) { $this->oProp = new AdminPageFramework_Property_MetaBox( $this, get_class( $this ), $sCapability, $sTextDomain, self::$_sFieldsType ); $this->oProp->aPostTypes = is_string( $asPostTypeOrScreenID ) ? array( $asPostTypeOrScreenID ) : $asPostTypeOrScreenID; parent::__construct( $sMetaBoxID, $sTitle, $asPostTypeOrScreenID, $sContext, $sPriority, $sCapability, $sTextDomain ); $this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}" ); } protected function _isInThePage() { if ( ! in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php' ) ) ) { return false; } if ( ! in_array( $this->oUtil->getCurrentPostType(), $this->oProp->aPostTypes ) ) { return false; } return true; } public function setUp() {} public function enqueueStyles( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueStyles( $aSRCs, $aPostTypes, $aCustomArgs ); } public function enqueueStyle( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueStyle( $sSRC, $aPostTypes, $aCustomArgs ); } public function enqueueScripts( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueScripts( $aSRCs, $aPostTypes, $aCustomArgs ); } public function enqueueScript( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueScript( $sSRC, $aPostTypes, $aCustomArgs ); } public function _replyToAddMetaBox() { foreach( $this->oProp->aPostTypes as $sPostType ) { add_meta_box( $this->oProp->sMetaBoxID, $this->oProp->sTitle, array( $this, '_replyToPrintMetaBoxContents' ), $sPostType, $this->oProp->sContext, $this->oProp->sPriority, null ); } } public function _replyToRegisterFormElements() { if ( ! $this->oUtil->isPostDefinitionPage( $this->oProp->aPostTypes ) ) return; $this->oForm->format(); $this->oForm->applyConditions(); if ( isset( $this->oProp->aOptions ) ) { $this->_setOptionArray( isset( $GLOBALS['post']->ID ) ? $GLOBALS['post']->ID : ( isset( $_GET['page'] ) ? $_GET['page'] : null ), $this->oForm->aConditionedFields ); } $this->oForm->setDynamicElements( $this->oProp->aOptions ); $this->_registerFields( $this->oForm->aConditionedFields ); } } endif;if ( ! class_exists( 'AdminPageFramework_PostType_Router' ) ) : abstract class AdminPageFramework_PostType_Router extends AdminPageFramework_Factory { public function __call( $sMethodName, $aArgs=null ) { if ( substr( $sMethodName, 0, strlen( "cell_" ) ) == "cell_" ) return $aArgs[0]; if ( substr( $sMethodName, 0, strlen( "sortable_columns_" ) ) == "sortable_columns_" ) return $aArgs[0]; if ( substr( $sMethodName, 0, strlen( "columns_" ) ) == "columns_" ) return $aArgs[0]; if ( substr( $sMethodName, 0, strlen( "style_ie_common_" ) )== "style_ie_common_" ) return $aArgs[0]; if ( substr( $sMethodName, 0, strlen( "style_common_" ) )== "style_common_" ) return $aArgs[0]; if ( substr( $sMethodName, 0, strlen( "style_ie_" ) )== "style_ie_" ) return $aArgs[0]; if ( substr( $sMethodName, 0, strlen( "style_" ) )== "style_" ) return $aArgs[0]; parent::__call( $sMethodName, $aArgs ); } } endif;if ( ! class_exists( 'AdminPageFramework_PostType_Model' ) ) : abstract class AdminPageFramework_PostType_Model extends AdminPageFramework_PostType_Router { function __construct( $oProp ) { parent::__construct( $oProp ); add_action( 'init', array( $this, '_replyToRegisterPostType' ), 999 ); $this->oProp->aColumnHeaders = array( 'cb' => '<input type="checkbox" />', 'title' => $this->oMsg->__( 'title' ), 'author' => $this->oMsg->__( 'author' ), 'comments' => '<div class="comment-grey-bubble"></div>', 'date' => $this->oMsg->__( 'date' ), ); if ( $this->_isInThePage() ) : add_filter( "manage_{$this->oProp->sPostType}_posts_columns", array( $this, '_replyToSetColumnHeader' ) ); add_filter( "manage_edit-{$this->oProp->sPostType}_sortable_columns", array( $this, '_replyToSetSortableColumns' ) ); add_action( "manage_{$this->oProp->sPostType}_posts_custom_column", array( $this, '_replyToSetColumnCell' ), 10, 2 ); add_action( 'admin_enqueue_scripts', array( $this, '_replyToDisableAutoSave' ) ); endif; } protected function _isInThePage() { if ( ! $this->oProp->bIsAdmin ) { return false; } if ( ! in_array( $GLOBALS['pagenow'], array( 'edit.php', 'edit-tags.php', 'post.php', 'post-new.php' ) ) ) { return false; } return ( $this->oUtil->getCurrentPostType() == $this->oProp->sPostType ); } public function _replyToSetSortableColumns( $aColumns ) { return $this->oUtil->addAndApplyFilter( $this, "sortable_columns_{$this->oProp->sPostType}", $aColumns ); } public function _replyToSetColumnHeader( $aHeaderColumns ) { return $this->oUtil->addAndApplyFilter( $this, "columns_{$this->oProp->sPostType}", $aHeaderColumns ); } public function _replyToSetColumnCell( $sColumnTitle, $iPostID ) { echo $this->oUtil->addAndApplyFilter( $this, "cell_{$this->oProp->sPostType}_{$sColumnTitle}", $sCell='', $iPostID ); } public function _replyToDisableAutoSave() { if ( $this->oProp->bEnableAutoSave ) return; if ( $this->oProp->sPostType != get_post_type() ) return; wp_dequeue_script( 'autosave' ); } public function _replyToRegisterPostType() { register_post_type( $this->oProp->sPostType, $this->oProp->aPostTypeArgs ); if ( true !== get_option( "post_type_rules_flased_{$this->oProp->sPostType}" ) ) { flush_rewrite_rules( false ); update_option( "post_type_rules_flased_{$this->oProp->sPostType}", true ); } } public function _replyToRegisterTaxonomies() { foreach( $this->oProp->aTaxonomies as $sTaxonomySlug => $aArgs ) register_taxonomy( $sTaxonomySlug, $this->oProp->sPostType, $aArgs ); } public function _replyToRemoveTexonomySubmenuPages() { foreach( $this->oProp->aTaxonomyRemoveSubmenuPages as $sSubmenuPageSlug => $sTopLevelPageSlug ) remove_submenu_page( $sTopLevelPageSlug, $sSubmenuPageSlug ); } } endif;if ( ! class_exists( 'AdminPageFramework_TaxonomyField' ) ) : abstract class AdminPageFramework_TaxonomyField extends AdminPageFramework_Factory { public $oProp; protected $oHeadTag; protected $oHelpPane; static protected $_sFieldsType = 'taxonomy'; function __construct( $asTaxonomySlug, $sOptionKey='', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) { if ( empty( $asTaxonomySlug ) ) return; $this->oProp = new AdminPageFramework_Property_TaxonomyField( $this, get_class( $this ), $sCapability, $sTextDomain, self::$_sFieldsType ); $this->oProp->aTaxonomySlugs = ( array ) $asTaxonomySlug; $this->oProp->sOptionKey = $sOptionKey ? $sOptionKey : $this->oProp->sClassName; parent::__construct( $this->oProp ); if ( $this->oProp->bIsAdmin ) { add_action( 'wp_loaded', array( $this, '_replyToDetermineToLoad' ) ); } $this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}" ); } protected function _isInThePage() { return ( in_array( $GLOBALS['pagenow'], array( 'edit-tags.php', 'admin-ajax.php' ) ) ); } public function _replyToDetermineToLoad() { if ( ! $this->_isInThePage() ) return; $this->_loadDefaultFieldTypeDefinitions(); $this->setUp(); add_action( 'current_screen', array( $this, '_replyToRegisterFormElements' ) ); foreach( $this->oProp->aTaxonomySlugs as $__sTaxonomySlug ) { add_action( "created_{$__sTaxonomySlug}", array( $this, '_replyToValidateOptions' ), 10, 2 ); add_action( "edited_{$__sTaxonomySlug}", array( $this, '_replyToValidateOptions' ), 10, 2 ); add_action( "{$__sTaxonomySlug}_add_form_fields", array( $this, '_replyToAddFieldsWOTableRows' ) ); add_action( "{$__sTaxonomySlug}_edit_form_fields", array( $this, '_replyToAddFieldsWithTableRows' ) ); add_filter( "manage_edit-{$__sTaxonomySlug}_columns", array( $this, '_replyToManageColumns' ), 10, 1 ); add_filter( "manage_edit-{$__sTaxonomySlug}_sortable_columns", array( $this, '_replyToSetSortableColumns' ) ); add_action( "manage_{$__sTaxonomySlug}_custom_column", array( $this, '_replyToSetColumnCell' ), 10, 3 ); } } public function setUp() {} protected function _setOptionArray( $iTermID=null, $sOptionKey ) { $aOptions = get_option( $sOptionKey, array() ); $this->oProp->aOptions = isset( $iTermID, $aOptions[ $iTermID ] ) ? $aOptions[ $iTermID ] : array(); } public function _replyToAddFieldsWOTableRows( $oTerm ) { echo $this->_getFieldsOutput( isset( $oTerm->term_id ) ? $oTerm->term_id : null, false ); } public function _replyToAddFieldsWithTableRows( $oTerm ) { echo $this->_getFieldsOutput( isset( $oTerm->term_id ) ? $oTerm->term_id : null, true ); } public function _replyToManageColumns( $aColumns ) { if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] ) $aColumns = $this->oUtil->addAndApplyFilter( $this, "columns_{$_GET['taxonomy']}", $aColumns ); $aColumns = $this->oUtil->addAndApplyFilter( $this, "columns_{$this->oProp->sClassName}", $aColumns ); return $aColumns; } public function _replyToSetSortableColumns( $aSortableColumns ) { if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] ) $aSortableColumns = $this->oUtil->addAndApplyFilter( $this, "sortable_columns_{$_GET['taxonomy']}", $aSortableColumns ); $aSortableColumns = $this->oUtil->addAndApplyFilter( $this, "sortable_columns_{$this->oProp->sClassName}", $aSortableColumns ); return $aSortableColumns; } public function _replyToSetColumnCell( $vValue, $sColumnSlug, $sTermID ) { $sCellHTML = ''; if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] ) $sCellHTML = $this->oUtil->addAndApplyFilter( $this, "cell_{$_GET['taxonomy']}", $vValue, $sColumnSlug, $sTermID ); $sCellHTML = $this->oUtil->addAndApplyFilter( $this, "cell_{$this->oProp->sClassName}", $sCellHTML, $sColumnSlug, $sTermID ); $sCellHTML = $this->oUtil->addAndApplyFilter( $this, "cell_{$this->oProp->sClassName}_{$sColumnSlug}", $sCellHTML, $sTermID ); echo $sCellHTML; } private function _getFieldsOutput( $iTermID, $bRenderTableRow ) { $_aOutput = array(); $_aOutput[] = wp_nonce_field( $this->oProp->sClassHash, $this->oProp->sClassHash, true, false ); $this->_setOptionArray( $iTermID, $this->oProp->sOptionKey ); $this->oForm->format(); $_oFieldsTable = new AdminPageFramework_FormTable( $this->oProp->aFieldTypeDefinitions, $this->_getFieldErrors(), $this->oMsg ); $_aOutput[] = $bRenderTableRow ? $_oFieldsTable->getFieldRows( $this->oForm->aFields['_default'], array( $this, '_replyToGetFieldOutput' ) ) : $_oFieldsTable->getFields( $this->oForm->aFields['_default'], array( $this, '_replyToGetFieldOutput' ) ); $_sOutput = $this->oUtil->addAndApplyFilters( $this, 'content_' . $this->oProp->sClassName, implode( PHP_EOL, $_aOutput ) ); $this->oUtil->addAndDoActions( $this, 'do_' . $this->oProp->sClassName ); return $_sOutput; } public function _replyToValidateOptions( $iTermID ) { if ( ! wp_verify_nonce( $_POST[ $this->oProp->sClassHash ], $this->oProp->sClassHash ) ) return; $aTaxonomyFieldOptions = get_option( $this->oProp->sOptionKey, array() ); $aOldOptions = isset( $aTaxonomyFieldOptions[ $iTermID ] ) ? $aTaxonomyFieldOptions[ $iTermID ] : array(); $aSubmittedOptions = array(); foreach( $this->oForm->aFields as $_sSectionID => $_aFields ) foreach( $_aFields as $_sFieldID => $_aField ) if ( isset( $_POST[ $_sFieldID ] ) ) $aSubmittedOptions[ $_sFieldID ] = $_POST[ $_sFieldID ]; $aSubmittedOptions = $this->oUtil->addAndApplyFilters( $this, 'validation_' . $this->oProp->sClassName, $aSubmittedOptions, $aOldOptions ); $aTaxonomyFieldOptions[ $iTermID ] = $this->oUtil->uniteArrays( $aSubmittedOptions, $aOldOptions ); update_option( $this->oProp->sOptionKey, $aTaxonomyFieldOptions ); } public function _replyToRegisterFormElements() { if ( $GLOBALS['pagenow'] != 'edit-tags.php' ) return; $this->oForm->format(); $this->oForm->applyConditions(); $this->_registerFields( $this->oForm->aConditionedFields ); } function __call( $sMethodName, $aArgs=null ) { if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] ) : if ( substr( $sMethodName, 0, strlen( 'columns_' . $_GET['taxonomy'] ) ) == 'columns_' . $_GET['taxonomy'] ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( 'sortable_columns_' . $_GET['taxonomy'] ) ) == 'sortable_columns_' . $_GET['taxonomy'] ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( 'cell_' . $_GET['taxonomy'] ) ) == 'cell_' . $_GET['taxonomy'] ) return $aArgs[ 0 ]; endif; if ( substr( $sMethodName, 0, strlen( 'columns_' . $this->oProp->sClassName ) ) == 'columns_' . $this->oProp->sClassName ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( 'sortable_columns_' . $this->oProp->sClassName ) ) == 'sortable_columns_' . $this->oProp->sClassName ) return $aArgs[ 0 ]; if ( substr( $sMethodName, 0, strlen( 'cell_' . $this->oProp->sClassName ) ) == 'cell_' . $this->oProp->sClassName ) return $aArgs[ 0 ]; return parent::__call( $sMethodName, $aArgs ); } } endif;if ( ! class_exists( 'AdminPageFramework_MetaBox_Page_Router' ) ) : abstract class AdminPageFramework_MetaBox_Page_Router extends AdminPageFramework_MetaBox_Base { function __construct( $sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) { parent::__construct( $sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain ); $this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}" ); } protected function _isInThePage() { if ( ! $this->oProp->bIsAdmin ) { return false; } if ( in_array( $GLOBALS['pagenow'], array( 'options.php' ) ) ) { return true; } if ( ! isset( $_GET['page'] ) ) { return false; } return in_array( $_GET['page'], $this->oProp->aPageSlugs ); } } endif;if ( ! class_exists( 'AdminPageFramework_MetaBox_Page_Model' ) ) : abstract class AdminPageFramework_MetaBox_Page_Model extends AdminPageFramework_MetaBox_Page_Router { static protected $_sFieldsType = 'page_meta_box'; function __construct( $sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) { $this->oProp = new AdminPageFramework_Property_MetaBox_Page( $this, get_class( $this ), $sCapability, $sTextDomain, self::$_sFieldsType ); $this->oProp->aPageSlugs = is_string( $asPageSlugs ) ? array( $asPageSlugs ) : $asPageSlugs; parent::__construct( $sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain ); if ( $this->_isInThePage() ) : foreach( $this->oProp->aPageSlugs as $_sIndexOrPageSlug => $_asTabArrayOrPageSlug ) { if ( is_string( $_asTabArrayOrPageSlug ) ) { $_sPageSlug = $_asTabArrayOrPageSlug; add_filter( "validation_saved_options_{$_sPageSlug}", array( $this, '_replyToFilterPageOptions' ) ); add_filter( "validation_{$_sPageSlug}", array( $this, '_replyToValidateOptions' ), 10, 2 ); continue; } $_sPageSlug = $_sIndexOrPageSlug; $_aTabs = $_asTabArrayOrPageSlug; add_filter( "validation_{$_sPageSlug}", array( $this, '_replyToValidateOptions' ), 10, 2 ); foreach( $_aTabs as $_sTabSlug ) { add_filter( "validation_saved_options_{$_sPageSlug}_{$_sTabSlug}", array( $this, '_replyToFilterPageOptions' ) ); } } endif; } protected function getFieldOutput( $aField ) { $sOptionKey = $this->_getOptionKey(); $aField['option_key'] = $sOptionKey ? $sOptionKey : null; $aField['page_slug'] = isset( $_GET['page'] ) ? $_GET['page'] : ''; return parent::getFieldOutput( $aField ); } private function _getOptionkey() { return isset( $_GET['page'] ) ? $this->oProp->getOptionKey( $_GET['page'] ) : null; } public function _replyToAddMetaBox() { foreach( $this->oProp->aPageSlugs as $sKey => $asPage ) { if ( is_string( $asPage ) ) { $this->_addMetaBox( $asPage ); continue; } if ( ! is_array( $asPage ) ) continue; $sPageSlug = $sKey; foreach( $asPage as $sTabSlug ) { if ( ! $this->oProp->isCurrentTab( $sTabSlug ) ) continue; $this->_addMetaBox( $sPageSlug ); } } } private function _addMetaBox( $sPageSlug ) { add_meta_box( $this->oProp->sMetaBoxID, $this->oProp->sTitle, array( $this, '_replyToPrintMetaBoxContents' ), $this->oProp->_getScreenIDOfPage( $sPageSlug ), $this->oProp->sContext, $this->oProp->sPriority, null ); } public function _replyToFilterPageOptions( $aPageOptions ) { return $this->oForm->dropRepeatableElements( $aPageOptions ); } public function _replyToValidateOptions( $aNewPageOptions, $aOldPageOptions ) { $_aFieldsModel = $this->oForm->getFieldsModel(); $_aNewMetaBoxInput = $this->oUtil->castArrayContents( $_aFieldsModel, $_POST ); $_aOldMetaBoxInput = $this->oUtil->castArrayContents( $_aFieldsModel, $aOldPageOptions ); $_aOtherOldMetaBoxInput = $this->oUtil->invertCastArrayContents( $aOldPageOptions, $_aFieldsModel ); $_aNewMetaBoxInput = stripslashes_deep( $_aNewMetaBoxInput ); $_aNewMetaBoxInput = $this->oUtil->addAndApplyFilters( $this, "validation_{$this->oProp->sClassName}", $_aNewMetaBoxInput, $_aOldMetaBoxInput ); return $this->oUtil->uniteArrays( $_aNewMetaBoxInput, $aNewPageOptions, $_aOtherOldMetaBoxInput ); } public function _replyToRegisterFormElements() { if ( ! $this->_isInThePage() ) return; $this->oForm->format(); $this->oForm->applyConditions(); $this->oForm->setDynamicElements( $this->oProp->aOptions ); $this->_registerFields( $this->oForm->aConditionedFields ); } } endif;if ( ! class_exists( 'AdminPageFramework_PostType_View' ) ) : abstract class AdminPageFramework_PostType_View extends AdminPageFramework_PostType_Model { function __construct( $oProp ) { parent::__construct( $oProp ); if ( $this->_isInThePage() ) { add_action( 'restrict_manage_posts', array( $this, '_replyToAddAuthorTableFilter' ) ); add_action( 'restrict_manage_posts', array( $this, '_replyToAddTaxonomyTableFilter' ) ); add_filter( 'parse_query', array( $this, '_replyToGetTableFilterQueryForTaxonomies' ) ); add_action( 'admin_head', array( $this, '_replyToPrintStyle' ) ); } } public function _replyToAddAuthorTableFilter() { if ( ! $this->oProp->bEnableAuthorTableFileter ) return; if ( ! ( isset( $_GET['post_type'] ) && post_type_exists( $_GET['post_type'] ) && in_array( strtolower( $_GET['post_type'] ), array( $this->oProp->sPostType ) ) ) ) return; wp_dropdown_users( array( 'show_option_all' => 'Show all Authors', 'show_option_none' => false, 'name' => 'author', 'selected' => ! empty( $_GET['author'] ) ? $_GET['author'] : 0, 'include_selected' => false )); } public function _replyToAddTaxonomyTableFilter() { if ( $GLOBALS['typenow'] != $this->oProp->sPostType ) return; $oPostCount = wp_count_posts( $this->oProp->sPostType ); if ( $oPostCount->publish + $oPostCount->future + $oPostCount->draft + $oPostCount->pending + $oPostCount->private + $oPostCount->trash == 0 ) return; foreach ( get_object_taxonomies( $GLOBALS['typenow'] ) as $sTaxonomySulg ) { if ( ! in_array( $sTaxonomySulg, $this->oProp->aTaxonomyTableFilters ) ) continue; $oTaxonomy = get_taxonomy( $sTaxonomySulg ); if ( wp_count_terms( $oTaxonomy->name ) == 0 ) continue; wp_dropdown_categories( array( 'show_option_all' => $this->oMsg->__( 'show_all' ) . ' ' . $oTaxonomy->label, 'taxonomy' => $sTaxonomySulg, 'name' => $oTaxonomy->name, 'orderby' => 'name', 'selected' => intval( isset( $_GET[ $sTaxonomySulg ] ) ), 'hierarchical' => $oTaxonomy->hierarchical, 'show_count' => true, 'hide_empty' => false, 'hide_if_empty' => false, 'echo' => true, ) ); } } public function _replyToGetTableFilterQueryForTaxonomies( $oQuery=null ) { if ( 'edit.php' != $GLOBALS['pagenow'] ) return $oQuery; if ( ! isset( $GLOBALS['typenow'] ) ) return $oQuery; foreach ( get_object_taxonomies( $GLOBALS['typenow'] ) as $sTaxonomySlug ) { if ( ! in_array( $sTaxonomySlug, $this->oProp->aTaxonomyTableFilters ) ) continue; $sVar = &$oQuery->query_vars[ $sTaxonomySlug ]; if ( ! isset( $sVar ) ) continue; $oTerm = get_term_by( 'id', $sVar, $sTaxonomySlug ); if ( is_object( $oTerm ) ) $sVar = $oTerm->slug; } return $oQuery; } public function _replyToPrintStyle() { if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] != $this->oProp->sPostType ) return; if ( isset( $this->oProp->aPostTypeArgs['screen_icon'] ) && $this->oProp->aPostTypeArgs['screen_icon'] ) $this->oProp->sStyle .= $this->_getStylesForPostTypeScreenIcon( $this->oProp->aPostTypeArgs['screen_icon'] ); $this->oProp->sStyle = $this->oUtil->addAndApplyFilters( $this, "style_{$this->oProp->sClassName}", $this->oProp->sStyle ); if ( ! empty( $this->oProp->sStyle ) ) echo "<style type='text/css' id='admin-page-framework-style-post-type'>" . $this->oProp->sStyle . "</style>"; } private function _getStylesForPostTypeScreenIcon( $sSRC ) { $sNone = 'none'; $sSRC = $this->oUtil->resolveSRC( $sSRC ); return "#post-body-content {
					margin-bottom: 10px;
				}
				#edit-slug-box {
					display: {$sNone};
				}
				#icon-edit.icon32.icon32-posts-" . $this->oProp->sPostType . " {
					background: url('" . $sSRC . "') no-repeat;
					background-size: 32px 32px;
				}			
			"; } } endif;if ( ! class_exists( 'AdminPageFramework_PostType_Controller' ) ) : abstract class AdminPageFramework_PostType_Controller extends AdminPageFramework_PostType_View { function __construct( $oProp ) { parent::__construct( $oProp ); if ( $this->_isInThePage() ) : add_action( 'wp_loaded', array( $this, 'setUp' ) ); endif; } public function setUp() {} public function enqueueStyles( $aSRCs, $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueStyles( $aSRCs, array( $this->oProp->sPostType ), $aCustomArgs ); } public function enqueueStyle( $sSRC, $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueStyle( $sSRC, array( $this->oProp->sPostType ), $aCustomArgs ); } public function enqueueScripts( $aSRCs, $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueScripts( $aSRCs, array( $this->oProp->sPostType ), $aCustomArgs ); } public function enqueueScript( $sSRC, $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueScript( $sSRC, array( $this->oProp->sPostType ), $aCustomArgs ); } protected function setAutoSave( $bEnableAutoSave=True ) { $this->oProp->bEnableAutoSave = $bEnableAutoSave; } protected function addTaxonomy( $sTaxonomySlug, $aArgs ) { $sTaxonomySlug = $this->oUtil->sanitizeSlug( $sTaxonomySlug ); $this->oProp->aTaxonomies[ $sTaxonomySlug ] = $aArgs; if ( isset( $aArgs['show_table_filter'] ) && $aArgs['show_table_filter'] ) $this->oProp->aTaxonomyTableFilters[] = $sTaxonomySlug; if ( isset( $aArgs['show_in_sidebar_menus'] ) && ! $aArgs['show_in_sidebar_menus'] ) $this->oProp->aTaxonomyRemoveSubmenuPages[ "edit-tags.php?taxonomy={$sTaxonomySlug}&amp;post_type={$this->oProp->sPostType}" ] = "edit.php?post_type={$this->oProp->sPostType}"; if ( count( $this->oProp->aTaxonomyTableFilters ) == 1 ) add_action( 'init', array( $this, '_replyToRegisterTaxonomies' ) ); if ( count( $this->oProp->aTaxonomyRemoveSubmenuPages ) == 1 ) add_action( 'admin_menu', array( $this, '_replyToRemoveTexonomySubmenuPages' ), 999 ); } protected function setAuthorTableFilter( $bEnableAuthorTableFileter=false ) { $this->oProp->bEnableAuthorTableFileter = $bEnableAuthorTableFileter; } protected function setPostTypeArgs( $aArgs ) { $this->oProp->aPostTypeArgs = $aArgs; } protected function setFooterInfoLeft( $sHTML, $bAppend=true ) { if ( isset( $this->oLink ) ) $this->oLink->aFooterInfo['sLeft'] = $bAppend ? $this->oLink->aFooterInfo['sLeft'] . $sHTML : $sHTML; } protected function setFooterInfoRight( $sHTML, $bAppend=true ) { if ( isset( $this->oLink ) ) $this->oLink->aFooterInfo['sRight'] = $bAppend ? $this->oLink->aFooterInfo['sRight'] . $sHTML : $sHTML; } } endif;if ( ! class_exists( 'AdminPageFramework_PostType' ) ) : abstract class AdminPageFramework_PostType extends AdminPageFramework_PostType_Controller { protected $oUtil; protected $oLink; public function __construct( $sPostType, $aArgs=array(), $sCallerPath=null, $sTextDomain='admin-page-framework' ) { if ( empty( $sPostType ) ) return; $this->oProp = new AdminPageFramework_Property_PostType( $this, $sCallerPath ? trim( $sCallerPath ) : AdminPageFramework_Utility::getCallerScriptPath( __FILE__ ), get_class( $this ), 'post', $sTextDomain, 'post_type' ); $this->oProp->sPostType = AdminPageFramework_WPUtility::sanitizeSlug( $sPostType ); $this->oProp->aPostTypeArgs = $aArgs; parent::__construct( $this->oProp ); $this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}" ); } } endif;if ( ! class_exists( 'AdminPageFramework_MetaBox_Page_View' ) ) : abstract class AdminPageFramework_MetaBox_Page_View extends AdminPageFramework_MetaBox_Page_Model { } endif;if ( ! class_exists( 'AdminPageFramework_MetaBox_Page_Controller' ) ) : abstract class AdminPageFramework_MetaBox_Page_Controller extends AdminPageFramework_MetaBox_Page_View { public function enqueueStyles( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueStyles( $aSRCs, $sPageSlug, $sTabSlug, $aCustomArgs ); } public function enqueueStyle( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueStyle( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs ); } public function enqueueScripts( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueScripts( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs ); } public function enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) { return $this->oHeadTag->_enqueueScript( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs ); } } endif;if ( ! class_exists( 'AdminPageFramework_MetaBox_Page' ) ) : abstract class AdminPageFramework_MetaBox_Page extends AdminPageFramework_MetaBox_Page_Controller { function __construct( $sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) { if ( empty( $asPageSlugs ) ) return; parent::__construct( $sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain ); } } endif;if ( ! class_exists( 'AdminPageFramework_Debug' ) ) : class AdminPageFramework_Debug { static public function dumpArray( $asArray, $sFilePath=null ) { echo self::getArray( $asArray, $sFilePath ); } static public function getArray( $asArray, $sFilePath=null, $bEscape=true ) { if ( $sFilePath ) self::logArray( $asArray, $sFilePath ); return $bEscape ? "<pre class='dump-array'>" . htmlspecialchars( print_r( $asArray, true ) ) . "</pre>" : print_r( $asArray, true ); } static public function logArray( $asArray, $sFilePath=null ) { static $_iPageLoadID; $_iPageLoadID = $_iPageLoadID ? $_iPageLoadID : uniqid(); $_oCallerInfo = debug_backtrace(); $_sCallerFunction = isset( $_oCallerInfo[ 1 ]['function'] ) ? $_oCallerInfo[ 1 ]['function'] : ''; $_sCallerClasss = isset( $_oCallerInfo[ 1 ]['class'] ) ? $_oCallerInfo[ 1 ]['class'] : ''; $sFilePath = $sFilePath ? $sFilePath : WP_CONTENT_DIR . DIRECTORY_SEPARATOR . get_class() . '_' . date( "Ymd" ) . '.log'; file_put_contents( $sFilePath, date( "Y/m/d H:i:s", current_time( 'timestamp' ) ) . ' ' . "{$_iPageLoadID} {$_sCallerClasss}::{$_sCallerFunction} " . AdminPageFramework_Utility::getCurrentURL() . PHP_EOL . print_r( $asArray, true ) . PHP_EOL . PHP_EOL, FILE_APPEND ); } } endif;if ( ! class_exists( 'AdminPageFramework_HelpPane_Base' ) ) : abstract class AdminPageFramework_HelpPane_Base extends AdminPageFramework_Debug { protected $_oScreen; function __construct( $oProp ) { $this->oProp = $oProp; $this->oUtil = new AdminPageFramework_WPUtility; } protected function _setHelpTab( $sID, $sTitle, $aContents, $aSideBarContents=array() ) { if ( empty( $aContents ) ) return; $this->_oScreen = isset( $this->_oScreen ) ? $this->_oScreen : get_current_screen(); $this->_oScreen->add_help_tab( array( 'id' => $sID, 'title' => $sTitle, 'content' => implode( PHP_EOL, $aContents ), ) ); if ( ! empty( $aSideBarContents ) ) $this->_oScreen->set_help_sidebar( implode( PHP_EOL, $aSideBarContents ) ); } protected function _formatHelpDescription( $sHelpDescription ) { return "<div class='contextual-help-description'>" . $sHelpDescription . "</div>"; } } endif;if ( ! class_exists( 'AdminPageFramework_HelpPane_MetaBox' ) ) : class AdminPageFramework_HelpPane_MetaBox extends AdminPageFramework_HelpPane_Base { function __construct( $oProp ) { parent::__construct( $oProp ); add_action( "load-{$GLOBALS['pagenow']}", array( $this, '_replyToRegisterHelpTabTextForMetaBox' ), 20 ); } public function _addHelpText( $sHTMLContent, $sHTMLSidebarContent="" ) { $this->oProp->aHelpTabText[] = "<div class='contextual-help-description'>" . $sHTMLContent . "</div>"; $this->oProp->aHelpTabTextSide[] = "<div class='contextual-help-description'>" . $sHTMLSidebarContent . "</div>"; } public function _addHelpTextForFormFields( $sFieldTitle, $sHelpText, $sHelpTextSidebar="" ) { $this->_addHelpText( "<span class='contextual-help-tab-title'>" . $sFieldTitle . "</span> - " . PHP_EOL . $sHelpText, $sHelpTextSidebar ); } public function _replyToRegisterHelpTabTextForMetaBox() { if ( ! in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php' ) ) ) { return; } if ( ! in_array( $this->oUtil->getCurrentPostType(), $this->oProp->aPostTypes ) ) { return; } $this->_setHelpTab( $this->oProp->sMetaBoxID, $this->oProp->sTitle, $this->oProp->aHelpTabText, $this->oProp->aHelpTabTextSide ); } } endif;if ( ! class_exists( 'AdminPageFramework_HelpPane_Page' ) ) : class AdminPageFramework_HelpPane_Page extends AdminPageFramework_HelpPane_Base { protected static $_aStructure_HelpTabUserArray = array( 'page_slug' => null, 'page_tab_slug' => null, 'help_tab_title' => null, 'help_tab_id' => null, 'help_tab_content' => null, 'help_tab_sidebar_content' => null, ); function __construct( $oProp ) { parent::__construct( $oProp ); add_action( 'admin_head', array( $this, '_replyToRegisterHelpTabs' ), 200 ); } public function _replyToRegisterHelpTabs() { $sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : ''; $sCurrentPageTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : ( isset( $this->oProp->aDefaultInPageTabs[ $sCurrentPageSlug ] ) ? $this->oProp->aDefaultInPageTabs[ $sCurrentPageSlug ] : '' ); if ( empty( $sCurrentPageSlug ) ) return; if ( ! $this->oProp->isPageAdded( $sCurrentPageSlug ) ) return; foreach( $this->oProp->aHelpTabs as $aHelpTab ) { if ( $sCurrentPageSlug != $aHelpTab['sPageSlug'] ) continue; if ( isset( $aHelpTab['sPageTabSlug'] ) && ! empty( $aHelpTab['sPageTabSlug'] ) && $sCurrentPageTabSlug != $aHelpTab['sPageTabSlug'] ) continue; $this->_setHelpTab( $aHelpTab['sID'], $aHelpTab['sTitle'], $aHelpTab['aContent'], $aHelpTab['aSidebar'] ); } } public function _addHelpTab( $aHelpTab ) { $aHelpTab = ( array ) $aHelpTab + self::$_aStructure_HelpTabUserArray; if ( ! isset( $this->oProp->aHelpTabs[ $aHelpTab['help_tab_id'] ] ) ) { $this->oProp->aHelpTabs[ $aHelpTab['help_tab_id'] ] = array( 'sID' => $aHelpTab['help_tab_id'], 'sTitle' => $aHelpTab['help_tab_title'], 'aContent' => ! empty( $aHelpTab['help_tab_content'] ) ? array( $this->_formatHelpDescription( $aHelpTab['help_tab_content'] ) ) : array(), 'aSidebar' => ! empty( $aHelpTab['help_tab_sidebar_content'] ) ? array( $this->_formatHelpDescription( $aHelpTab['help_tab_sidebar_content'] ) ) : array(), 'sPageSlug' => $aHelpTab['page_slug'], 'sPageTabSlug' => $aHelpTab['page_tab_slug'], ); return; } if ( ! empty( $aHelpTab['help_tab_content'] ) ) $this->oProp->aHelpTabs[ $aHelpTab['help_tab_id'] ]['aContent'][] = $this->_formatHelpDescription( $aHelpTab['help_tab_content'] ); if ( ! empty( $aHelpTab['help_tab_sidebar_content'] ) ) $this->oProp->aHelpTabs[ $aHelpTab['help_tab_id'] ]['aSidebar'][] = $this->_formatHelpDescription( $aHelpTab['help_tab_sidebar_content'] ); } } endif;if ( ! class_exists( 'AdminPageFramework_HelpPane_TaxonomyField' ) ) : class AdminPageFramework_HelpPane_TaxonomyField extends AdminPageFramework_HelpPane_MetaBox { public function _replyToRegisterHelpTabTextForMetaBox() { $this->_setHelpTab( $this->oProp->sMetaBoxID, $this->oProp->sTitle, $this->oProp->aHelpTabText, $this->oProp->aHelpTabTextSide ); } } endif;if ( ! class_exists( 'AdminPageFramework_RegisterClasses' ) ) : class AdminPageFramework_RegisterClasses { public $_aClasses = array(); static protected $_aStructure_RecursiveOptions = array( 'is_recursive' => true, 'exclude_dirs' => array(), ); function __construct( $sClassDirPath, $aClasses=array(), $aAllowedExtensions=array( 'php', 'inc' ), $aRecursiveOptions=array( 'is_recursive' => true, 'exclude_dirs' => array() ), $aAllowedExtensions=array( 'php', 'inc' ) ) { $aRecursiveOptions = $aRecursiveOptions + self::$_aStructure_RecursiveOptions; $this->_aClasses = $aClasses + $this->composeClassArray( $sClassDirPath, $aAllowedExtensions, $aRecursiveOptions ); $this->registerClasses(); } protected function composeClassArray( $sClassDirPath, $aAllowedExtensions, $aRecursiveOptions ) { $sClassDirPath = rtrim( $sClassDirPath, '\\/' ) . DIRECTORY_SEPARATOR; $_aFilePaths = $aRecursiveOptions['is_recursive'] ? $this->doRecursiveGlob( $sClassDirPath . '*.' . $this->getGlobPatternExtensionPart( $aAllowedExtensions ), GLOB_BRACE, $aRecursiveOptions['exclude_dirs'] ) : ( array ) glob( $sClassDirPath . '*.' . $this->getGlobPatternExtensionPart( $aAllowedExtensions ), GLOB_BRACE ); $_aFilePaths = array_filter( $_aFilePaths ); $_aClasses = array(); foreach( $_aFilePaths as $_sFilePath ) { $_aClasses[ pathinfo( $_sFilePath, PATHINFO_FILENAME ) ] = $_sFilePath; } return $_aClasses; } protected function getGlobPatternExtensionPart( $aExtensions=array( 'php', 'inc' ) ) { return empty( $aExtensions ) ? '*' : '{' . implode( ',', $aExtensions ) . '}'; } protected function doRecursiveGlob( $sPathPatten, $iFlags=0, $asExcludeDirs=array() ) { $_aFiles = glob( $sPathPatten, $iFlags ); $_aFiles = is_array( $_aFiles ) ? $_aFiles : array(); $_aDirs = glob( dirname( $sPathPatten ) . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR|GLOB_NOSORT ); $_aDirs = is_array( $_aDirs ) ? $_aDirs : array(); foreach ( $_aDirs as $_sDirPath ) { if ( in_array( $_sDirPath, ( array ) $asExcludeDirs ) ) continue; $_aFiles = array_merge( $_aFiles, $this->doRecursiveGlob( $_sDirPath . DIRECTORY_SEPARATOR . basename( $sPathPatten ), $iFlags, $asExcludeDirs ) ); } return $_aFiles; } protected function registerClasses() { spl_autoload_register( array( $this, 'replyToAutoLoader' ) ); } public function replyToAutoLoader( $sCalledUnknownClassName ) { if ( array_key_exists( $sCalledUnknownClassName, $this->_aClasses ) && file_exists( $this->_aClasses[ $sCalledUnknownClassName ] ) ) include_once( $this->_aClasses[ $sCalledUnknownClassName ] ); } } endif;if ( ! class_exists( 'AdminPageFramework_Utility_Array' ) ) : abstract class AdminPageFramework_Utility_Array { public static function getCorrespondingArrayValue( $vSubject, $sKey, $sDefault='', $bBlankToDefault=false ) { if ( ! isset( $vSubject ) ) return $sDefault; if ( $bBlankToDefault && $vSubject == '' ) return $sDefault; if ( ! is_array( $vSubject ) ) return ( string ) $vSubject; if ( isset( $vSubject[ $sKey ] ) ) return $vSubject[ $sKey ]; return $sDefault; } public static function getArrayDimension( $array ) { return ( is_array( reset( $array ) ) ) ? self::getArrayDimension( reset( $array ) ) + 1 : 1; } public static function castArrayContents( $aModel, $aSubject ) { $aMod = array(); foreach( $aModel as $sKey => $_v ) $aMod[ $sKey ] = isset( $aSubject[ $sKey ] ) ? $aSubject[ $sKey ] : null; return $aMod; } public static function invertCastArrayContents( $sModel, $aSubject ) { $aMod = array(); foreach( $sModel as $sKey => $_v ) { if ( array_key_exists( $sKey, $aSubject ) ) continue; $aMod[ $sKey ] = $_v; } return $aMod; } public static function uniteArrays( $aPrecedence, $aDefault1 ) { $aArgs = array_reverse( func_get_args() ); $aArray = array(); foreach( $aArgs as $aArg ) $aArray = self::uniteArraysRecursive( $aArg, $aArray ); return $aArray; } public static function uniteArraysRecursive( $aPrecedence, $aDefault ) { if ( is_null( $aPrecedence ) ) $aPrecedence = array(); if ( ! is_array( $aDefault ) || ! is_array( $aPrecedence ) ) return $aPrecedence; foreach( $aDefault as $sKey => $v ) { if ( ! array_key_exists( $sKey, $aPrecedence ) || is_null( $aPrecedence[ $sKey ] ) ) $aPrecedence[ $sKey ] = $v; else { if ( is_array( $aPrecedence[ $sKey ] ) && is_array( $v ) ) $aPrecedence[ $sKey ] = self::uniteArraysRecursive( $aPrecedence[ $sKey ], $v ); } } return $aPrecedence; } static public function isLastElement( array $aArray, $sKey ) { end( $aArray ); return $sKey === key( $aArray ); } static public function getIntegerElements( $aParse ) { if ( ! is_array( $aParse ) ) return array(); foreach ( $aParse as $isKey => $v ) { if ( ! is_numeric( $isKey ) ) { unset( $aParse[ $isKey ] ); continue; } $isKey = $isKey + 0; if ( ! is_int( $isKey ) ) unset( $aParse[ $isKey ] ); } return $aParse; } static public function getNonIntegerElements( $aParse ) { foreach ( $aParse as $isKey => $v ) if ( is_numeric( $isKey ) && is_int( $isKey+ 0 ) ) unset( $aParse[ $isKey ] ); return $aParse; } static public function numerizeElements( $aSubject ) { $_aNumeric = self::getIntegerElements( $aSubject ); $_aAssociative = self::invertCastArrayContents( $aSubject, $_aNumeric ); foreach( $_aNumeric as &$_aElem ) $_aElem = self::uniteArrays( $_aElem, $_aAssociative ); if ( ! empty( $_aAssociative ) ) array_unshift( $_aNumeric, $_aAssociative ); return $_aNumeric; } static public function isAssociativeArray( array $aArray ) { return ( bool ) count( array_filter( array_keys( $aArray ), 'is_string' ) ); } static public function shiftTillTrue( array $aArray ) { foreach( $aArray as &$vElem ) { if ( $vElem ) break; unset( $vElem ); } return array_values( $aArray ); } static public function getArrayValueByArrayKeys( $aArray, $aKeys, $vDefault=null ) { $sKey = array_shift( $aKeys ); if ( isset( $aArray[ $sKey ] ) ) { if ( empty( $aKeys ) ) { return $aArray[ $sKey ]; } if ( is_array( $aArray[ $sKey ] ) ) { return self::getArrayValueByArrayKeys( $aArray[ $sKey ], $aKeys, $vDefault ); } } return $vDefault; } static public function getAsArray( $asValue ) { if ( is_array( $asValue ) ) return $asValue; if ( ! isset( $asValue ) ) return array(); return ( array ) $asValue; } } endif;if ( ! class_exists( 'AdminPageFramework_Utility_String' ) ) : abstract class AdminPageFramework_Utility_String extends AdminPageFramework_Utility_Array { public static function sanitizeSlug( $sSlug ) { return is_null( $sSlug ) ? null : preg_replace( '/[^a-zA-Z0-9_\x7f-\xff]/', '_', trim( $sSlug ) ); } public static function sanitizeString( $sString ) { return is_null( $sString ) ? null : preg_replace( '/[^a-zA-Z0-9_\x7f-\xff\-]/', '_', $sString ); } static public function fixNumber( $nToFix, $nDefault, $nMin="", $nMax="" ) { if ( ! is_numeric( trim( $nToFix ) ) ) return $nDefault; if ( $nMin !== "" && $nToFix < $nMin ) return $nMin; if ( $nMax !== "" && $nToFix > $nMax ) return $nMax; return $nToFix; } static public function minifyCSS( $sCSSRules ) { return str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $sCSSRules ) ); } } endif;if ( ! class_exists( 'AdminPageFramework_Utility_Path' ) ) : abstract class AdminPageFramework_Utility_Path extends AdminPageFramework_Utility_String { static public function getRelativePath( $from, $to ) { $from = is_dir( $from ) ? rtrim( $from, '\/') . '/' : $from; $to = is_dir( $to ) ? rtrim( $to, '\/') . '/' : $to; $from = str_replace( '\\', '/', $from ); $to = str_replace( '\\', '/', $to ); $from = explode( '/', $from ); $to = explode( '/', $to ); $relPath = $to; foreach( $from as $depth => $dir ) { if( $dir === $to[ $depth ] ) { array_shift( $relPath ); } else { $remaining = count( $from ) - $depth; if( $remaining > 1 ) { $padLength = ( count( $relPath ) + $remaining - 1 ) * -1; $relPath = array_pad( $relPath, $padLength, '..' ); break; } else { $relPath[ 0 ] = './' . $relPath[ 0 ]; } } } return implode( '/', $relPath ); } static public function getCallerScriptPath( $asRedirectedFiles=array( __FILE__ ) ) { $aRedirectedFiles = ( array ) $asRedirectedFiles; $aRedirectedFiles[] = __FILE__; $sCallerFilePath = ''; foreach( debug_backtrace() as $aDebugInfo ) { $sCallerFilePath = $aDebugInfo['file']; if ( in_array( $sCallerFilePath, $aRedirectedFiles ) ) continue; break; } return $sCallerFilePath; } } endif;if ( ! class_exists( 'AdminPageFramework_Utility_URL' ) ) : abstract class AdminPageFramework_Utility_URL extends AdminPageFramework_Utility_Path { static public function getCurrentURL() { $sSSL = ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) ? true:false; $sServerProtocol = strtolower( $_SERVER['SERVER_PROTOCOL'] ); $sProtocol = substr( $sServerProtocol, 0, strpos( $sServerProtocol, '/' ) ) . ( ( $sSSL ) ? 's' : '' ); $sPort = $_SERVER['SERVER_PORT']; $sPort = ( ( !$sSSL && $sPort=='80' ) || ( $sSSL && $sPort=='443' ) ) ? '' : ':' . $sPort; $sHost = isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']; return $sProtocol . '://' . $sHost . $sPort . $_SERVER['REQUEST_URI']; } } endif;if ( ! class_exists( 'AdminPageFramework_Utility' ) ) : abstract class AdminPageFramework_Utility extends AdminPageFramework_Utility_URL { static public function getQueryValueInURLByKey( $sURL, $sQueryKey ) { $aURL = parse_url( $sURL ); parse_str( $aURL['query'], $aQuery ); return isset( $aQuery[ $sQueryKey ] ) ? $aQuery[ $sQueryKey ] : null; } static public function generateAttributes( array $aAttributes ) { $aOutput = array(); foreach( $aAttributes as $sAttribute => $sProperty ) { if ( empty( $sProperty ) && $sProperty !== 0 ) continue; if ( is_array( $sProperty ) || is_object( $sProperty ) ) continue; $aOutput[] = "{$sAttribute}='{$sProperty}'"; } return implode( ' ', $aOutput ); } } endif;if ( ! class_exists( 'AdminPageFramework_WPUtility_URL' ) ) : class AdminPageFramework_WPUtility_URL extends AdminPageFramework_Utility { public function getCurrentAdminURL() { $sRequestURI = $GLOBALS['is_IIS'] ? $_SERVER['PATH_INFO'] : $_SERVER["REQUEST_URI"]; $sPageURL = ( @$_SERVER["HTTPS"] == "on" ) ? "https://" : "http://"; if ( $_SERVER["SERVER_PORT"] != "80" ) $sPageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $sRequestURI; else $sPageURL .= $_SERVER["SERVER_NAME"] . $sRequestURI; return $sPageURL; } public function getQueryAdminURL( $aAddingQueries, $aRemovingQueryKeys=array(), $sSubjectURL='' ) { $sSubjectURL = $sSubjectURL ? $sSubjectURL : add_query_arg( $_GET, admin_url( $GLOBALS['pagenow'] ) ); return $this->getQueryURL( $aAddingQueries, $aRemovingQueryKeys, $sSubjectURL ); } public function getQueryURL( $aAddingQueries, $aRemovingQueryKeys, $sSubjectURL ) { $sSubjectURL = empty( $aRemovingQueryKeys ) ? $sSubjectURL : remove_query_arg( ( array ) $aRemovingQueryKeys, $sSubjectURL ); $sSubjectURL = add_query_arg( $aAddingQueries, $sSubjectURL ); return $sSubjectURL; } static public function getSRCFromPath( $sFilePath ) { $oWPStyles = new WP_Styles(); $sRelativePath = AdminPageFramework_Utility::getRelativePath( ABSPATH, $sFilePath ); $sRelativePath = preg_replace( "/^\.[\/\\\]/", '', $sRelativePath, 1 ); $sHref = trailingslashit( $oWPStyles->base_url ) . $sRelativePath; unset( $oWPStyles ); return esc_url( $sHref ); } static public function resolveSRC( $sSRC, $bReturnNullIfNotExist=false ) { if ( ! $sSRC ) return $bReturnNullIfNotExist ? null : $sSRC; if ( filter_var( $sSRC, FILTER_VALIDATE_URL ) ) return $sSRC; if ( file_exists( realpath( $sSRC ) ) ) return self::getSRCFromPath( $sSRC ); if ( $bReturnNullIfNotExist ) return null; return $sSRC; } } endif;if ( ! class_exists( 'AdminPageFramework_WPUtility_HTML' ) ) : class AdminPageFramework_WPUtility_HTML extends AdminPageFramework_WPUtility_URL { static public function generateAttributes( array $aAttributes ) { foreach( $aAttributes as $sAttribute => &$asProperty ) { if ( is_array( $asProperty ) || is_object( $asProperty ) ) unset( $aAttributes[ $sAttribute ] ); if ( is_string( $asProperty ) ) $asProperty = esc_attr( $asProperty ); } return parent::generateAttributes( $aAttributes ); } static public function generateDataAttributes( array $aArray ) { $aNewArray = array(); foreach( $aArray as $sKey => $v ) $aNewArray[ "data-{$sKey}" ] = $v; return self::generateAttributes( $aNewArray ); } } endif;if ( ! class_exists( 'AdminPageFramework_WPUtility_Page' ) ) : class AdminPageFramework_WPUtility_Page extends AdminPageFramework_WPUtility_HTML { static public function getCurrentPostType() { static $_sCurrentPostType; if ( $_sCurrentPostType ) return $_sCurrentPostType; if ( isset( $GLOBALS['post'], $GLOBALS['post']->post_type ) && $GLOBALS['post']->post_type ) { $_sCurrentPostType = $GLOBALS['post']->post_type; return $_sCurrentPostType; } if ( isset( $GLOBALS['typenow'] ) && $GLOBALS['typenow'] ) { $_sCurrentPostType = $GLOBALS['typenow']; return $_sCurrentPostType; } if ( isset( $GLOBALS['current_screen']->post_type ) && $GLOBALS['current_screen']->post_type ) { $_sCurrentPostType = $GLOBALS['current_screen']->post_type; return $_sCurrentPostType; } if ( isset( $_REQUEST['post_type'] ) ) { $_sCurrentPostType = sanitize_key( $_REQUEST['post_type'] ); return $_sCurrentPostType; } if ( isset( $_GET['post'] ) && $_GET['post'] ) { $_sCurrentPostType = get_post_type( $_GET['post'] ); return $_sCurrentPostType; } return null; } static public function isPostDefinitionPage( $asPostTypes=array() ) { $_aPostTypes = ( array ) $asPostTypes; if ( ! in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) ) return false; if ( empty( $_aPostTypes ) ) return true; if ( in_array( self::getCurrentPostType(), $_aPostTypes ) ) return true; return false; } static public function isPostListingPage( $asPostTypes=array() ) { if ( $GLOBALS['pagenow'] != 'edit.php' ) return false; $_aPostTypes = is_array( $asPostTypes ) ? $asPostTypes : array( $asPostTypes ); if ( ! isset( $_GET['post_type'] ) ) return in_array( 'post', $_aPostTypes ); return in_array( $_GET['post_type'], $_aPostTypes ); } } endif;if ( ! class_exists( 'AdminPageFramework_WPUtility_Hook' ) ) : class AdminPageFramework_WPUtility_Hook extends AdminPageFramework_WPUtility_Page { public function doActions( $aActionHooks, $vArgs1=null, $vArgs2=null, $_and_more=null ) { $aArgs = func_get_args(); $aActionHooks = $aArgs[ 0 ]; foreach( ( array ) $aActionHooks as $sActionHook ) { $aArgs[ 0 ] = $sActionHook; call_user_func_array( 'do_action' , $aArgs ); } } public function addAndDoActions( $oCallerObject, $aActionHooks, $vArgs1=null, $vArgs2=null, $_and_more=null ) { $aArgs = func_get_args(); $oCallerObject = $aArgs[ 0 ]; $aActionHooks = $aArgs[ 1 ]; foreach( ( array ) $aActionHooks as $sActionHook ) { if ( ! $sActionHook ) continue; $aArgs[ 1 ] = $sActionHook; call_user_func_array( array( $this, 'addAndDoAction' ) , $aArgs ); } } public function addAndDoAction( $oCallerObject, $sActionHook, $vArgs1=null, $vArgs2=null, $_and_more=null ) { $iArgs = func_num_args(); $aArgs = func_get_args(); $oCallerObject = $aArgs[ 0 ]; $sActionHook = $aArgs[ 1 ]; if ( ! $sActionHook ) return; add_action( $sActionHook, array( $oCallerObject, $sActionHook ), 10, $iArgs - 2 ); unset( $aArgs[ 0 ] ); call_user_func_array( 'do_action' , $aArgs ); } public function addAndApplyFilters() { $aArgs = func_get_args(); $oCallerObject = $aArgs[ 0 ]; $aFilters = $aArgs[ 1 ]; $vInput = $aArgs[ 2 ]; foreach( ( array ) $aFilters as $sFilter ) { if ( ! $sFilter ) continue; $aArgs[ 1 ] = $sFilter; $aArgs[ 2 ] = $vInput; $vInput = call_user_func_array( array( $this, 'addAndApplyFilter' ) , $aArgs ); } return $vInput; } public function addAndApplyFilter() { $iArgs = func_num_args(); $aArgs = func_get_args(); $oCallerObject = $aArgs[ 0 ]; $sFilter = $aArgs[ 1 ]; if ( ! $sFilter ) return $aArgs[ 2 ]; add_filter( $sFilter, array( $oCallerObject, $sFilter ), 10, $iArgs - 2 ); unset( $aArgs[ 0 ] ); return call_user_func_array( 'apply_filters', $aArgs ); } public function getFilterArrayByPrefix( $sPrefix, $sClassName, $sPageSlug, $sTabSlug, $bReverse=false ) { $aFilters = array(); if ( $sTabSlug && $sPageSlug ) $aFilters[] = "{$sPrefix}{$sPageSlug}_{$sTabSlug}"; if ( $sPageSlug ) $aFilters[] = "{$sPrefix}{$sPageSlug}"; if ( $sClassName ) $aFilters[] = "{$sPrefix}{$sClassName}"; return $bReverse ? array_reverse( $aFilters ) : $aFilters; } } endif;if ( ! class_exists( 'AdminPageFramework_WPUtility_File' ) ) : class AdminPageFramework_WPUtility_File extends AdminPageFramework_WPUtility_Hook { static public function getScriptData( $sPath, $sType='plugin' ) { $aData = get_file_data( $sPath, array( 'sName' => 'Name', 'sURI' => 'URI', 'sScriptName' => 'Script Name', 'sLibraryName' => 'Library Name', 'sLibraryURI' => 'Library URI', 'sPluginName' => 'Plugin Name', 'sPluginURI' => 'Plugin URI', 'sThemeName' => 'Theme Name', 'sThemeURI' => 'Theme URI', 'sVersion' => 'Version', 'sDescription' => 'Description', 'sAuthor' => 'Author', 'sAuthorURI' => 'Author URI', 'sTextDomain' => 'Text Domain', 'sDomainPath' => 'Domain Path', 'sNetwork' => 'Network', '_sitewide' => 'Site Wide Only', ), in_array( $sType, array( 'plugin', 'theme' ) ) ? $sType : 'plugin' ); switch ( trim( $sType ) ) { case 'theme': $aData['sName'] = $aData['sThemeName']; $aData['sURI'] = $aData['sThemeURI']; break; case 'library': $aData['sName'] = $aData['sLibraryName']; $aData['sURI'] = $aData['sLibraryURI']; break; case 'script': $aData['sName'] = $aData['sScriptName']; break; case 'plugin': $aData['sName'] = $aData['sPluginName']; $aData['sURI'] = $aData['sPluginURI']; break; default: break; } return $aData; } } endif;if ( ! class_exists( 'AdminPageFramework_WPUtility_Option' ) ) : class AdminPageFramework_WPUtility_Option extends AdminPageFramework_WPUtility_File { static public function getOption( $sOptionKey, $asKey=null, $vDefault=null ) { if ( ! $asKey ) { return get_option( $sOptionKey, isset( $vDefault ) ? $vDefault : array() ); } $_aOptions = get_option( $sOptionKey, array() ); $_aKeys = self::shiftTillTrue( self::getAsArray( $asKey ) ); return self::getArrayValueByArrayKeys( $_aOptions, $_aKeys, $vDefault ); } } endif;if ( ! class_exists( 'AdminPageFramework_WPUtility_Post' ) ) : class AdminPageFramework_WPUtility_Post extends AdminPageFramework_WPUtility_Option { static public function getSavedMetaArray( $iPostID, array $aKeys ) { $_aSavedMeta = array(); foreach ( $aKeys as $_sKey ) { $_aSavedMeta[ $_sKey ] = get_post_meta( $iPostID, $_sKey, true ); } return $_aSavedMeta; } } endif;if ( ! class_exists( 'AdminPageFramework_WPUtility' ) ) : class AdminPageFramework_WPUtility extends AdminPageFramework_WPUtility_Post {} endif;if ( ! class_exists( 'AdminPageFramework_Link_Base' ) ) : abstract class AdminPageFramework_Link_Base extends AdminPageFramework_WPUtility { protected function _setFooterInfoLeft( $aScriptInfo, &$sFooterInfoLeft ) { $sDescription = empty( $aScriptInfo['sDescription'] ) ? "" : "&#13;{$aScriptInfo['sDescription']}"; $sVersion = empty( $aScriptInfo['sVersion'] ) ? "" : "&nbsp;{$aScriptInfo['sVersion']}"; $sPluginInfo = empty( $aScriptInfo['sURI'] ) ? $aScriptInfo['sName'] : "<a href='{$aScriptInfo['sURI']}' target='_blank' title='{$aScriptInfo['sName']}{$sVersion}{$sDescription}'>{$aScriptInfo['sName']}</a>"; $sAuthorInfo = empty( $aScriptInfo['sAuthorURI'] ) ? $aScriptInfo['sAuthor'] : "<a href='{$aScriptInfo['sAuthorURI']}' target='_blank'>{$aScriptInfo['sAuthor']}</a>"; $sAuthorInfo = empty( $aScriptInfo['sAuthor'] ) ? $sAuthorInfo : ' by ' . $sAuthorInfo; $sFooterInfoLeft = $sPluginInfo . $sAuthorInfo; } protected function _setFooterInfoRight( $aScriptInfo, &$sFooterInfoRight ) { $sDescription = empty( $aScriptInfo['sDescription'] ) ? "" : "&#13;{$aScriptInfo['sDescription']}"; $sVersion = empty( $aScriptInfo['sVersion'] ) ? "" : "&nbsp;{$aScriptInfo['sVersion']}"; $sLibraryInfo = empty( $aScriptInfo['sURI'] ) ? $aScriptInfo['sName'] : "<a href='{$aScriptInfo['sURI']}' target='_blank' title='{$aScriptInfo['sName']}{$sVersion}{$sDescription}'>{$aScriptInfo['sName']}</a>"; $sFooterInfoRight = $this->oMsg->__( 'powered_by' ) . '&nbsp;' . $sLibraryInfo . ", <a href='http://wordpress.org' target='_blank' title='WordPress {$GLOBALS['wp_version']}'>WordPress</a>"; } } endif;if ( ! class_exists( 'AdminPageFramework_FormElement_Utility' ) ) : class AdminPageFramework_FormElement_Utility extends AdminPageFramework_WPUtility { public function dropRepeatableElements( array $aOptions ) { foreach( $aOptions as $_sFieldOrSectionID => $_aSectionOrFieldValue ) { if ( $this->isSection( $_sFieldOrSectionID ) ) { $_aFields = $_aSectionOrFieldValue; $_sSectionID = $_sFieldOrSectionID; if ( $this->isRepeatableSection( $_sSectionID ) ) { unset( $aOptions[ $_sSectionID ] ); continue; } if ( ! is_array( $_aFields ) ) continue; foreach( $_aFields as $_sFieldID => $_aField ) { if ( $this->isRepeatableField( $_sFieldID, $_sSectionID ) ) { unset( $aOptions[ $_sSectionID ][ $_sFieldID ] ); continue; } } continue; } $_sFieldID = $_sFieldOrSectionID; if ( $this->isRepeatableField( $_sFieldID, '_default' ) ) unset( $aOptions[ $_sFieldID ] ); } return $aOptions; } private function isRepeatableSection( $sSectionID ) { return isset( $this->aSections[ $sSectionID ]['repeatable'] ) && $this->aSections[ $sSectionID ]['repeatable']; } private function isRepeatableField( $sFieldID, $sSectionID ) { return ( isset( $this->aFields[ $sSectionID ][ $sFieldID ]['repeatable'] ) && $this->aFields[ $sSectionID ][ $sFieldID ]['repeatable'] ); } public function isSection( $sID ) { if ( is_numeric( $sID ) && is_int( $sID + 0 ) ) return false; if ( ! array_key_exists( $sID, $this->aSections ) ) return false; if ( ! array_key_exists( $sID, $this->aFields ) ) return false; $_bIsSeciton = false; foreach( $this->aFields as $_sSectionID => $_aFields ) { if ( $_sSectionID == $sID ) $_bIsSeciton = true; if ( array_key_exists( $sID, $_aFields ) ) return false; } return $_bIsSeciton; } public function getFieldsModel( array $aFields=array() ) { $_aFieldsModel = array(); $aFields = empty( $aFields ) ? $this->aFields : $aFields; foreach ( $aFields as $_sSectionID => $_aFields ) { if ( $_sSectionID != '_default' ) { $_aFieldsModel[ $_sSectionID ][ $_aField['field_id'] ] = $_aField; continue; } foreach( $_aFields as $_sFieldID => $_aField ) $_aFieldsModel[ $_aField['field_id'] ] = $_aField; } return $_aFieldsModel; } public function _sortByOrder( $a, $b ) { return isset( $a['order'], $b['order'] ) ? $a['order'] - $b['order'] : 1; } public function applyFiltersToFields( $oCaller, $sClassName ) { foreach( $this->aConditionedFields as $_sSectionID => $_aSubSectionOrFields ) { foreach( $_aSubSectionOrFields as $_sIndexOrFieldID => $_aSubSectionOrField ) { if ( is_numeric( $_sIndexOrFieldID ) && is_int( $_sIndexOrFieldID + 0 ) ) { $_sSubSectionIndex = $_sIndexOrFieldID; $_aFields = $_aSubSectionOrField; $_sSectionSubString = $_sSectionID == '_default' ? '' : "_{$_sSectionID}"; foreach( $_aFields as $_aField ) { $this->aConditionedFields[ $_sSectionID ][ $_sSubSectionIndex ][ $_aField['field_id'] ] = $this->addAndApplyFilter( $oCaller, "field_definition_{$sClassName}{$_sSectionSubString}_{$_aField['field_id']}", $_aField, $_sSubSectionIndex ); } continue; } $_aField = $_aSubSectionOrField; $_sSectionSubString = $_sSectionID == '_default' ? '' : "_{$_sSectionID}"; $this->aConditionedFields[ $_sSectionID ][ $_aField['field_id'] ] = $this->addAndApplyFilter( $oCaller, "field_definition_{$sClassName}{$_sSectionSubString}_{$_aField['field_id']}", $_aField ); } } } } endif;if ( ! class_exists( 'AdminPageFramework_FormElement' ) ) : class AdminPageFramework_FormElement extends AdminPageFramework_FormElement_Utility { public static $_aStructure_Section = array( 'section_id' => '_default', 'page_slug' => null, 'tab_slug' => null, 'section_tab_slug' => null, 'title' => null, 'description' => null, 'capability' => null, 'if' => true, 'order' => null, 'help' => null, 'help_aside' => null, 'repeatable' => null, 'section_tab_slug' => null, ); public static $_aStructure_Field = array( 'field_id' => null, 'type' => null, 'section_id' => null, 'section_title' => null, 'page_slug' => null, 'tab_slug' => null, 'option_key' => null, 'class_name' => null, 'capability' => null, 'title' => null, 'tip' => null, 'description' => null, 'error_message' => null, 'before_label' => null, 'after_label' => null, 'if' => true, 'order' => null, 'default' => null, 'value' => null, 'help' => null, 'help_aside' => null, 'repeatable' => null, 'sortable' => null, 'attributes' => null, 'show_title_column' => true, 'hidden' => null, '_fields_type' => null, '_section_index' => null, ); public $aFields = array(); public $aSections = array( '_default' => array(), ); public $aConditionedFields = array(); public $aConditionedSections = array(); protected $sFieldsType = ''; protected $_sTargetSectionID = '_default'; public $sCapability = 'manage_option'; public function __construct( $sFieldsType, $sCapability ) { $this->sFieldsType = $sFieldsType; $this->sCapability = $sCapability; } public function addSection( array $aSection ) { $aSection = $aSection + self::$_aStructure_Section; $aSection['section_id'] = $this->sanitizeSlug( $aSection['section_id'] ); $this->aSections[ $aSection['section_id'] ] = $aSection; $this->aFields[ $aSection['section_id'] ] = isset( $this->aFields[ $aSection['section_id'] ] ) ? $this->aFields[ $aSection['section_id'] ] : array(); } public function removeSection( $sSectionID ) { if ( $sSectionID == '_default' ) return; unset( $this->aSections[ $sSectionID ] ); unset( $this->aFields[ $sSectionID ] ); } public function addField( $asField ) { if ( ! is_array( $asField ) ) { $this->_sTargetSectionID = is_string( $asField ) ? $asField : $this->_sTargetSectionID; return $this->_sTargetSectionID; } $aField = $asField; $this->_sTargetSectionID = isset( $aField['section_id'] ) ? $aField['section_id'] : $this->_sTargetSectionID; $aField = $this->uniteArrays( array( '_fields_type' => $this->sFieldsType ), $aField, array( 'section_id' => $this->_sTargetSectionID ), self::$_aStructure_Field ); if ( ! isset( $aField['field_id'], $aField['type'] ) ) return null; $aField['field_id'] = $this->sanitizeSlug( $aField['field_id'] ); $aField['section_id'] = $this->sanitizeSlug( $aField['section_id'] ); $this->aFields[ $aField['section_id'] ][ $aField['field_id'] ] = $aField; return $aField; } public function removeField( $sFieldID ) { foreach( $this->aFields as $_sSectionID => $_aSubSectionsOrFields ) { if ( array_key_exists( $sFieldID, $_aSubSectionsOrFields ) ) unset( $this->aFields[ $_sSectionID ][ $sFieldID ] ); foreach ( $_aSubSectionsOrFields as $_sIndexOrFieldID => $_aSubSectionOrFields ) { if ( is_numeric( $_sIndexOrFieldID ) && is_int( $_sIndexOrFieldID + 0 ) ) { if ( array_key_exists( $sFieldID, $_aSubSectionOrFields ) ) unset( $this->aFields[ $_sSectionID ][ $_sIndexOrFieldID ] ); continue; } } } } public function format() { $this->formatSections( $this->sFieldsType, $this->sCapability ); $this->formatFields( $this->sFieldsType, $this->sCapability ); } public function formatSections( $sFieldsType, $sCapability ) { $_aNewSectionArray = array(); foreach( $this->aSections as $_sSectionID => $_aSection ) { if ( ! is_array( $_aSection ) ) continue; $_aSection = $this->formatSection( $_aSection, $sFieldsType, $sCapability, count( $_aNewSectionArray ) ); if ( ! $_aSection ) continue; $_aNewSectionArray[ $_sSectionID ] = $_aSection; } uasort( $_aNewSectionArray, array( $this, '_sortByOrder' ) ); $this->aSections = $_aNewSectionArray; } protected function formatSection( array $aSection, $sFieldsType, $sCapability, $iCountOfElements ) { $aSection = $this->uniteArrays( $aSection, array( '_fields_type' => $sFieldsType, 'capability' => $sCapability, ), self::$_aStructure_Section ); $aSection['order'] = is_numeric( $aSection['order'] ) ? $aSection['order'] : $iCountOfElements + 10; return $aSection; } public function formatFields( $sFieldsType, $sCapability ) { $_aNewFields = array(); foreach ( $this->aFields as $_sSectionID => $_aSubSectionsOrFields ) { if ( ! isset( $this->aSections[ $_sSectionID ] ) ) continue; $_aNewFields[ $_sSectionID ] = isset( $_aNewFields[ $_sSectionID ] ) ? $_aNewFields[ $_sSectionID ] : array(); $_abSectionRepeatable = $this->aSections[ $_sSectionID ]['repeatable']; if ( count( $this->getIntegerElements( $_aSubSectionsOrFields ) ) || $_abSectionRepeatable ) { foreach( $this->numerizeElements( $_aSubSectionsOrFields ) as $_iSectionIndex => $_aFields ) { foreach( $_aFields as $_aField ) { $_iCountElement = isset( $_aNewFields[ $_sSectionID ][ $_iSectionIndex ] ) ? count( $_aNewFields[ $_sSectionID ][ $_iSectionIndex ] ) : 0 ; $_aField = $this->formatField( $_aField, $sFieldsType, $sCapability, $_iCountElement, $_iSectionIndex, $_abSectionRepeatable ); if ( $_aField ) $_aNewFields[ $_sSectionID ][ $_iSectionIndex ][ $_aField['field_id'] ] = $_aField; } uasort( $_aNewFields[ $_sSectionID ][ $_iSectionIndex ], array( $this, '_sortByOrder' ) ); } continue; } $_aSectionedFields = $_aSubSectionsOrFields; foreach( $_aSectionedFields as $_sFieldID => $_aField ) { $_iCountElement = isset( $_aNewFields[ $_sSectionID ] ) ? count( $_aNewFields[ $_sSectionID ] ) : 0; $_aField = $this->formatField( $_aField, $sFieldsType, $sCapability, $_iCountElement, null, $_abSectionRepeatable ); if ( $_aField ) $_aNewFields[ $_sSectionID ][ $_aField['field_id'] ] = $_aField; } uasort( $_aNewFields[ $_sSectionID ], array( $this, '_sortByOrder' ) ); } if ( ! empty( $this->aSections ) && ! empty( $_aNewFields ) ) : $_aSortedFields = array(); foreach( $this->aSections as $sSectionID => $aSeciton ) if ( isset( $_aNewFields[ $sSectionID ] ) ) $_aSortedFields[ $sSectionID ] = $_aNewFields[ $sSectionID ]; $_aNewFields = $_aSortedFields; endif; $this->aFields = $_aNewFields; } protected function formatField( $aField, $sFieldsType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable ) { if ( ! isset( $aField['field_id'], $aField['type'] ) ) return; $_aField = $this->uniteArrays( array( '_fields_type' => $sFieldsType ), $aField, array( 'capability' => $sCapability, 'section_id' => '_default', '_section_index' => $iSectionIndex, '_section_repeatable' => $bIsSectionRepeatable, ), self::$_aStructure_Field ); $_aField['field_id'] = $this->sanitizeSlug( $_aField['field_id'] ); $_aField['section_id'] = $this->sanitizeSlug( $_aField['section_id'] ); $_aField['tip'] = esc_attr( strip_tags( isset( $_aField['tip'] ) ? $_aField['tip'] : $_aField['description'] ) ); $_aField['order'] = is_numeric( $_aField['order'] ) ? $_aField['order'] : $iCountOfElements + 10; return $_aField; } public function applyConditions( $aFields=null, $aSections=null ) { return $this->getConditionedFields( $aFields, $this->getConditionedSections( $aSections ) ); } public function getConditionedSections( $aSections=null ) { $aSections = is_null( $aSections ) ? $this->aSections : $aSections; $aNewSections = array(); foreach( $aSections as $_sSectionID => $_aSection ) { $_aSection = $this->getConditionedSection( $_aSection ); if ( $_aSection ) $aNewSections[ $_sSectionID ] = $_aSection; } $this->aConditionedSections = $aNewSections; return $aNewSections; } protected function getConditionedSection( array $aSection ) { if ( ! current_user_can( $aSection['capability'] ) ) return; if ( ! $aSection['if'] ) return; return $aSection; } public function getConditionedFields( $aFields=null, $aSections=null ) { $aFields = is_null( $aFields ) ? $this->aFields : $aFields; $aSections = is_null( $aSections ) ? $this->aSections : $aSections; $aFields = ( array ) $this->castArrayContents( $aSections, $aFields ); $_aNewFields = array(); foreach( $aFields as $_sSectionID => $_aSubSectionOrFields ) { if ( ! is_array( $_aSubSectionOrFields ) ) continue; if ( ! array_key_exists( $_sSectionID, $aSections ) ) continue; foreach( $_aSubSectionOrFields as $_sIndexOrFieldID => $_aSubSectionOrField ) { if ( is_numeric( $_sIndexOrFieldID ) && is_int( $_sIndexOrFieldID + 0 ) ) { $_sSubSectionIndex = $_sIndexOrFieldID; $_aFields = $_aSubSectionOrField; foreach( $_aFields as $_aField ) { $_aField = $this->getConditionedField( $_aField ); if ( $_aField ) $_aNewFields[ $_sSectionID ][ $_sSubSectionIndex ][ $_aField['field_id'] ] = $_aField; } continue; } $_aField = $_aSubSectionOrField; $_aField = $this->getConditionedField( $_aField ); if ( $_aField ) $_aNewFields[ $_sSectionID ][ $_aField['field_id'] ] = $_aField; } } $this->aConditionedFields = $_aNewFields; return $_aNewFields; } protected function getConditionedField( $aField ) { if ( ! current_user_can( $aField['capability'] ) ) return null; if ( ! $aField['if'] ) return null; return $aField; } public function setDynamicElements( $aOptions ) { $aOptions = $this->castArrayContents( $this->aConditionedSections, $aOptions ); foreach( $aOptions as $_sSectionID => $_aSubSectionOrFields ) { if ( ! is_array( $_aSubSectionOrFields ) ) continue; $_aSubSection = array(); foreach( $_aSubSectionOrFields as $_isIndexOrFieldID => $_aSubSectionOrFieldOptions ) { if ( ! ( is_numeric( $_isIndexOrFieldID ) && is_int( $_isIndexOrFieldID + 0 ) ) ) continue; $_iIndex = $_isIndexOrFieldID; $_aSubSection[ $_iIndex ] = isset( $this->aConditionedFields[ $_sSectionID ][ $_iIndex ] ) ? $this->aConditionedFields[ $_sSectionID ][ $_iIndex ] : $this->getNonIntegerElements( $this->aConditionedFields[ $_sSectionID ] ); $_aSubSection[ $_iIndex ] = ! empty( $_aSubSection[ $_iIndex ] ) ? $_aSubSection[ $_iIndex ] : ( isset( $_aSubSection[ $_iPrevIndex ] ) ? $_aSubSection[ $_iPrevIndex ] : array() ); foreach( $_aSubSection[ $_iIndex ] as &$_aField ) $_aField['_section_index'] = $_iIndex; unset( $_aField ); $_iPrevIndex = $_iIndex; } if ( ! empty( $_aSubSection ) ) $this->aConditionedFields[ $_sSectionID ] = $_aSubSection; } } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_Base' ) ) : abstract class AdminPageFramework_FieldType_Base extends AdminPageFramework_WPUtility { public $_sFieldSetType = ''; public $aFieldTypeSlugs = array( 'default' ); protected $aDefaultKeys = array(); protected static $_aDefaultKeys = array( 'value' => null, 'default' => null, 'repeatable' => false, 'sortable' => false, 'label' => '', 'delimiter' => '', 'before_input' => '', 'after_input' => '', 'before_label' => null, 'after_label' => null, 'before_field' => null, 'after_field' => null, 'label_min_width' => 140, 'field_id' => null, 'page_slug' => null, 'section_id' => null, 'before_fields' => null, 'after_fields' => null, 'attributes' => array( 'disabled' => '', 'class' => '', 'fieldrow' => array(), 'fieldset' => array(), 'fields' => array(), 'field' => array(), ), ); protected $oMsg; function __construct( $asClassName, $asFieldTypeSlug=null, $oMsg=null, $bAutoRegister=true ) { $this->aFieldTypeSlugs = empty( $asFieldTypeSlug ) ? $this->aFieldTypeSlugs : ( array ) $asFieldTypeSlug; $this->oMsg = $oMsg ? $oMsg : AdminPageFramework_Message::instantiate(); if ( $bAutoRegister ) { foreach( ( array ) $asClassName as $sClassName ) add_filter( "field_types_{$sClassName}", array( $this, 'replyToRegisterInputFieldType' ) ); } } public function replyToRegisterInputFieldType( $aFieldDefinitions ) { foreach ( $this->aFieldTypeSlugs as $sFieldTypeSlug ) $aFieldDefinitions[ $sFieldTypeSlug ] = $this->getDefinitionArray( $sFieldTypeSlug ); return $aFieldDefinitions; } public function getDefinitionArray( $sFieldTypeSlug='' ) { $_aDefaultKeys = $this->aDefaultKeys + self::$_aDefaultKeys; $_aDefaultKeys['attributes'] = isset( $this->aDefaultKeys['attributes'] ) && is_array( $this->aDefaultKeys['attributes'] ) ? $this->aDefaultKeys['attributes'] + self::$_aDefaultKeys['attributes'] : self::$_aDefaultKeys['attributes']; return array( 'sFieldTypeSlug' => $sFieldTypeSlug, 'aFieldTypeSlugs' => $this->aFieldTypeSlugs, 'hfRenderField' => array( $this, "_replyToGetField" ), 'hfGetScripts' => array( $this, "_replyToGetScripts" ), 'hfGetStyles' => array( $this, "_replyToGetStyles" ), 'hfGetIEStyles' => array( $this, "_replyToGetInputIEStyles" ), 'hfFieldLoader' => array( $this, "_replyToFieldLoader" ), 'hfFieldSetTypeSetter' => array( $this, "_replyToFieldTypeSetter" ), 'aEnqueueScripts' => $this->_replyToGetEnqueuingScripts(), 'aEnqueueStyles' => $this->_replyToGetEnqueuingStyles(), 'aDefaultKeys' => $_aDefaultKeys, ); } public function _replyToGetField( $aField ) { return ''; } public function _replyToGetScripts() { return ''; } public function _replyToGetInputIEStyles() { return ''; } public function _replyToGetStyles() { return ''; } public function _replyToFieldLoader() {} public function _replyToFieldTypeSetter( $sFieldSetType='' ) { $this->_sFieldSetType = $sFieldSetType; } protected function _replyToGetEnqueuingScripts() { return array(); } protected function _replyToGetEnqueuingStyles() { return array(); } protected function getFieldElementByKey( $asElement, $sKey, $asDefault='' ) { if ( ! is_array( $asElement ) || ! isset( $sKey ) ) return $asElement; $aElements = &$asElement; return isset( $aElements[ $sKey ] ) ? $aElements[ $sKey ] : $asDefault; } protected function enqueueMediaUploader() { add_filter( 'media_upload_tabs', array( $this, '_replyToRemovingMediaLibraryTab' ) ); wp_enqueue_script( 'jquery' ); wp_enqueue_script( 'thickbox' ); wp_enqueue_style( 'thickbox' ); if ( function_exists( 'wp_enqueue_media' ) ) add_action( 'admin_footer', array( $this, '_replyToEnqueueMedia' ), 1 ); else wp_enqueue_script( 'media-upload' ); if ( in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php', ) ) ) add_filter( 'gettext', array( $this, '_replyToReplaceThickBoxText' ) , 1, 2 ); } public function _replyToEnqueueMedia() { wp_enqueue_media(); } public function _replyToReplaceThickBoxText( $sTranslated, $sText ) { if ( ! in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php' ) ) ) return $sTranslated; if ( $sText != 'Insert into Post' ) return $sTranslated; if ( $this->getQueryValueInURLByKey( wp_get_referer(), 'referrer' ) != 'admin_page_framework' ) return $sTranslated; if ( isset( $_GET['button_label'] ) ) return $_GET['button_label']; return $this->oProp->sThickBoxButtonUseThis ? $this->oProp->sThickBoxButtonUseThis : $this->oMsg->__( 'use_this_image' ); } public function _replyToRemovingMediaLibraryTab( $aTabs ) { if ( ! isset( $_REQUEST['enable_external_source'] ) ) return $aTabs; if ( ! $_REQUEST['enable_external_source'] ) unset( $aTabs['type_url'] ); return $aTabs; } protected function _getScript_CustomMediaUploaderObject() { if ( ! function_exists( 'wp_enqueue_media' ) ) return ""; $GLOBALS['aAdminPageFramework']['aLoadedCustomMediaUploaderObject'] = isset( $GLOBALS['aAdminPageFramework']['aLoadedCustomMediaUploaderObject'] ) ? $GLOBALS['aAdminPageFramework']['aLoadedCustomMediaUploaderObject'] : array(); if ( isset( $GLOBALS['aAdminPageFramework']['aLoadedCustomMediaUploaderObject'][ $this->_sFieldSetType ] ) ) return ''; $GLOBALS['aAdminPageFramework']['aLoadedCustomMediaUploaderObject'][ $this->_sFieldSetType ] = true; return "
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
							text:  l10n.setFeaturedImage,
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
		"; } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType' ) ) : abstract class AdminPageFramework_FieldType extends AdminPageFramework_FieldType_Base { public function _replyToFieldLoader() { $this->setUp(); } public function _replyToGetScripts() { return $this->getScripts(); } public function _replyToGetInputIEStyles() { return $this->getIEStyles(); } public function _replyToGetStyles() { return $this->getStyles(); } public function _replyToGetField( $aField ) { return $this->getField( $aField ); } protected function _replyToGetEnqueuingScripts() { return $this->getEnqueuingScripts(); } protected function _replyToGetEnqueuingStyles() { return $this->getEnqueuingStyles(); } public $aFieldTypeSlugs = array(); protected $aDefaultKeys = array(); protected function setUp() {} protected function getScripts() { return ''; } protected function getIEStyles() { return ''; } protected function getStyles() { return ''; } protected function getField( $aField ) { return ''; } protected function getEnqueuingScripts() { return array(); } protected function getEnqueuingStyles() { return array(); } } endif;if ( ! class_exists( 'AdminPageFramework_FormField_Base' ) ) : class AdminPageFramework_FormField_Base extends AdminPageFramework_WPUtility { public function __construct( &$aField, &$aOptions, $aErrors, &$aFieldTypeDefinitions, &$oMsg ) { $aFieldTypeDefinition = isset( $aFieldTypeDefinitions[ $aField['type'] ] ) ? $aFieldTypeDefinitions[ $aField['type'] ] : $aFieldTypeDefinitions['default']; $aFieldTypeDefinition['aDefaultKeys']['attributes'] = array( 'fieldrow' => $aFieldTypeDefinition['aDefaultKeys']['attributes']['fieldrow'], 'fieldset' => $aFieldTypeDefinition['aDefaultKeys']['attributes']['fieldset'], 'fields' => $aFieldTypeDefinition['aDefaultKeys']['attributes']['fields'], 'field' => $aFieldTypeDefinition['aDefaultKeys']['attributes']['field'], ); $this->aField = $this->uniteArrays( $aField, $aFieldTypeDefinition['aDefaultKeys'] ); $this->aFieldTypeDefinitions = $aFieldTypeDefinitions; $this->aOptions = $aOptions; $this->aErrors = $aErrors ? $aErrors : array(); $this->oMsg = $oMsg; $this->_loadScripts(); } private function _loadScripts() { static $_bIsLoadedUtility, $_bIsLoadedRepeatable, $_bIsLoadedSortable, $_bIsLoadedRegisterCallback; if ( ! $_bIsLoadedUtility ) { add_action( 'admin_footer', array( $this, '_replyToAddUtilityPlugins' ) ); $_bIsLoadedUtility = add_action( 'admin_footer', array( $this, '_replyToAddAttributeUpdaterjQueryPlugin' ) ); } if ( ! $_bIsLoadedRepeatable ) { $_bIsLoadedRepeatable = add_action( 'admin_footer', array( $this, '_replyToAddRepeatableFieldjQueryPlugin' ) ); } if ( ! $_bIsLoadedSortable ) { $_bIsLoadedSortable = add_action( 'admin_footer', array( $this, '_replyToAddSortableFieldPlugin' ) ); } if ( ! $_bIsLoadedRegisterCallback ) { $_bIsLoadedRegisterCallback = add_action( 'admin_footer', array( $this, '_replyToAddRegisterCallbackjQueryPlugin' ) ); } } protected function _getRepeaterFieldEnablerScript( $sFieldsContainerID, $iFieldCount, $aSettings ) { $_sAdd = $this->oMsg->__( 'add' ); $_sRemove = $this->oMsg->__( 'remove' ); $_sVisibility = $iFieldCount <= 1 ? " style='display:none;'" : ""; $_sSettingsAttributes = $this->generateDataAttributes( ( array ) $aSettings ); $_sButtons = "<div class='admin-page-framework-repeatable-field-buttons' {$_sSettingsAttributes} >" . "<a class='repeatable-field-add button-secondary repeatable-field-button button button-small' href='#' title='{$_sAdd}' data-id='{$sFieldsContainerID}'>+</a>" . "<a class='repeatable-field-remove button-secondary repeatable-field-button button button-small' href='#' title='{$_sRemove}' {$_sVisibility} data-id='{$sFieldsContainerID}'>-</a>" . "</div>"; $_aJSArray = json_encode( $aSettings ); return "<script type='text/javascript'>
				jQuery( document ).ready( function() {
					nodePositionIndicators = jQuery( '#{$sFieldsContainerID} .admin-page-framework-field .repeatable-field-buttons' );
					if ( nodePositionIndicators.length > 0 ) {	/* If the position of inserting the buttons is specified in the field type definition, replace the pointer element with the created output */
						nodePositionIndicators.replaceWith( \"{$_sButtons}\" );						
					} else {	/* Otherwise, insert the button element at the beginning of the field tag */
						if ( ! jQuery( '#{$sFieldsContainerID} .admin-page-framework-repeatable-field-buttons' ).length ) {	// check the button container already exists for WordPress 3.5.1 or below
							jQuery( '#{$sFieldsContainerID} .admin-page-framework-field' ).prepend( \"{$_sButtons}\" );	// Adds the buttons
						}
					}					
					jQuery( '#{$sFieldsContainerID}' ).updateAPFRepeatableFields( {$_aJSArray} );	// Update the fields			
				});
			</script>"; } protected function _getSortableFieldEnablerScript( $sFieldsContainerID ) { return "<script type='text/javascript' class='admin-page-framework-sortable-field-enabler-script'>
				jQuery( document ).ready( function() {
					jQuery( this ).enableAPFSortable( '{$sFieldsContainerID}' );
				});
			</script>"; } public function _replyToAddRepeatableFieldjQueryPlugin() { echo "<script type='text/javascript' class='admin-page-framework-repeatable-fields-plugin'>" . AdminPageFramework_Script_RepeatableField::getjQueryPlugin( $this->oMsg->__( 'allowed_maximum_number_of_fields' ), $this->oMsg->__( 'allowed_minimum_number_of_fields' ) ) . "</script>"; } public function _replyToAddAttributeUpdaterjQueryPlugin() { echo "<script type='text/javascript' class='admin-page-framework-attribute-updater'>" . AdminPageFramework_Script_AttributeUpdator::getjQueryPlugin() . "</script>"; } public function _replyToAddRegisterCallbackjQueryPlugin() { echo "<script type='text/javascript' class='admin-page-framework-register-callback'>" . AdminPageFramework_Script_RegisterCallback::getjQueryPlugin() . "</script>"; } public function _replyToAddUtilityPlugins() { echo "<script type='text/javascript' class='admin-page-framework-utility-plugins'>" . AdminPageFramework_Script_Utility::getjQueryPlugin() . "</script>"; } public function _replyToAddSortableFieldPlugin() { wp_enqueue_script( 'jquery-ui-sortable' ); echo "<script type='text/javascript' class='admin-page-framework-sortable-field-plugin'>" . AdminPageFramework_Script_Sortable::getjQueryPlugin() . "</script>"; } } endif;if ( ! class_exists( 'AdminPageFramework_FormField' ) ) : class AdminPageFramework_FormField extends AdminPageFramework_FormField_Base { private function _getInputName( $aField=null, $sKey='' ) { $sKey = ( string ) $sKey; $aField = isset( $aField ) ? $aField : $this->aField; $_sKey = $sKey !== '0' && empty( $sKey ) ? '' : "[{$sKey}]"; $sSectionIndex = isset( $aField['section_id'], $aField['_section_index'] ) ? "[{$aField['_section_index']}]" : ""; switch( $aField['_fields_type'] ) { default: case 'page': $sSectionDimension = isset( $aField['section_id'] ) && $aField['section_id'] && $aField['section_id'] != '_default' ? "[{$aField['section_id']}]" : ''; return "{$aField['option_key']}{$sSectionDimension}{$sSectionIndex}[{$aField['field_id']}]{$_sKey}"; case 'page_meta_box': case 'post_meta_box': return isset( $aField['section_id'] ) && $aField['section_id'] && $aField['section_id'] != '_default' ? "{$aField['section_id']}{$sSectionIndex}[{$aField['field_id']}]{$_sKey}" : "{$aField['field_id']}{$_sKey}"; case 'taxonomy': return "{$aField['field_id']}{$_sKey}"; } } protected function _getFlatInputName( $aField, $sKey='' ) { $sKey = ( string ) $sKey; $_sKey = $sKey !== '0' && empty( $sKey ) ? '' : "|{$sKey}"; $sSectionIndex = isset( $aField['section_id'], $aField['_section_index'] ) ? "|{$aField['_section_index']}" : ""; switch( $aField['_fields_type'] ) { default: case 'page': $sSectionDimension = isset( $aField['section_id'] ) && $aField['section_id'] && $aField['section_id'] != '_default' ? "|{$aField['section_id']}" : ''; return "{$aField['option_key']}{$sSectionDimension}{$sSectionIndex}|{$aField['field_id']}{$_sKey}"; case 'page_meta_box': case 'post_meta_box': return isset( $aField['section_id'] ) && $aField['section_id'] && $aField['section_id'] != '_default' ? "{$aField['section_id']}{$sSectionIndex}|{$aField['field_id']}{$_sKey}" : "{$aField['field_id']}{$_sKey}"; case 'taxonomy': return "{$aField['field_id']}{$_sKey}"; } } private function _getInputFieldValue( $aField, $aOptions ) { switch( $aField['_fields_type'] ) { default: case 'page': case 'page_meta_box': case 'taxonomy': if ( ! isset( $aField['section_id'] ) || $aField['section_id'] == '_default' ) return isset( $aOptions[ $aField['field_id'] ] ) ? $aOptions[ $aField['field_id'] ] : null; if ( isset( $aField['_section_index'] ) ) return isset( $aOptions[ $aField['section_id'] ][ $aField['_section_index'] ][ $aField['field_id'] ] ) ? $aOptions[ $aField['section_id'] ][ $aField['_section_index'] ][ $aField['field_id'] ] : null; return isset( $aOptions[ $aField['section_id'] ][ $aField['field_id'] ] ) ? $aOptions[ $aField['section_id'] ][ $aField['field_id'] ] : null; case 'post_meta_box': if ( ! isset( $_GET['action'], $_GET['post'] ) ) return null; if ( ! isset( $aField['section_id'] ) || $aField['section_id'] == '_default' ) return get_post_meta( $_GET['post'], $aField['field_id'], true ); $aSectionArray = get_post_meta( $_GET['post'], $aField['section_id'], true ); if ( isset( $aField['_section_index'] ) ) return isset( $aSectionArray[ $aField['_section_index'] ][ $aField['field_id'] ] ) ? $aSectionArray[ $aField['_section_index'] ][ $aField['field_id'] ] : null; return isset( $aSectionArray[ $aField['field_id'] ] ) ? $aSectionArray[ $aField['field_id'] ] : null; } return null; } private function _getInputID( $aField, $sIndex ) { $sSectionIndex = isset( $aField['_section_index'] ) ? '__' . $aField['_section_index'] : ''; $sFieldIndex = '__' . $sIndex; return isset( $aField['section_id'] ) && $aField['section_id'] != '_default' ? $aField['section_id'] . $sSectionIndex . '_' . $aField['field_id'] . $sFieldIndex : $aField['field_id'] . $sFieldIndex; } static public function _getInputTagID( $aField ) { $sSectionIndex = isset( $aField['_section_index'] ) ? '__' . $aField['_section_index'] : ''; return isset( $aField['section_id'] ) && $aField['section_id'] != '_default' ? $aField['section_id'] . $sSectionIndex . '_' . $aField['field_id'] : $aField['field_id']; } public function _getFieldOutput() { $aFieldsOutput = array(); $aExtraOutput = array(); if ( isset( $this->aField['section_id'], $this->aErrors[ $this->aField['section_id'] ], $this->aErrors[ $this->aField['section_id'] ][ $this->aField['field_id'] ] ) ) $aFieldsOutput[] = "<span style='color:red;'>*&nbsp;{$this->aField['error_message']}" . $this->aErrors[ $this->aField['section_id'] ][ $this->aField['field_id'] ] . "</span><br />"; else if ( isset( $this->aErrors[ $this->aField['field_id'] ] ) ) $aFieldsOutput[] = "<span style='color:red;'>*&nbsp;{$this->aField['error_message']}" . $this->aErrors[ $this->aField['field_id'] ] . "</span><br />"; $this->aField['tag_id'] = $this->_getInputTagID( $this->aField ); $aFields = $this->_composeFieldsArray( $this->aField, $this->aOptions ); foreach( $aFields as $sKey => $aField ) { $aFieldTypeDefinition = isset( $this->aFieldTypeDefinitions[ $aField['type'] ] ) ? $this->aFieldTypeDefinitions[ $aField['type'] ] : $this->aFieldTypeDefinitions['default']; $aField['_index'] = $sKey; $aField['input_id'] = $this->_getInputID( $aField, $sKey ); $aField['_input_name'] = $this->_getInputName( $this->aField, $aField['_is_multiple_fields'] ? $sKey : '' ); $aField['_input_name_flat'] = $this->_getFlatInputName( $this->aField, $aField['_is_multiple_fields'] ? $sKey : '' ); $aField['_field_container_id'] = "field-{$aField['input_id']}"; $aField['_fields_container_id'] = "fields-{$this->aField['tag_id']}"; $aField['_fieldset_container_id'] = "fieldset-{$this->aField['tag_id']}"; $aField['attributes'] = $this->uniteArrays( ( array ) $aField['attributes'], array( 'id' => $aField['input_id'], 'name' => $aField['_input_name'], 'value' => $aField['value'], 'type' => $aField['type'], 'disabled' => null, ), ( array ) $aFieldTypeDefinition['aDefaultKeys']['attributes'] ); $_aFieldAttributes = array( 'id' => $aField['_field_container_id'], 'class' => "admin-page-framework-field admin-page-framework-field-{$aField['type']}" . ( $aField['attributes']['disabled'] ? ' disabled' : '' ), 'data-type' => "{$aField['type']}", ) + $aField['attributes']['field']; $aFieldsOutput[] = is_callable( $aFieldTypeDefinition['hfRenderField'] ) ? $aField['before_field'] . "<div " . $this->generateAttributes( $_aFieldAttributes ) . ">" . call_user_func_array( $aFieldTypeDefinition['hfRenderField'], array( $aField ) ) . ( ( $sDelimiter = $aField['delimiter'] ) ? "<div " . $this->generateAttributes( array( 'class' => 'delimiter', 'id' => "delimiter-{$aField['input_id']}", 'style' => $this->isLastElement( $aFields, $sKey ) ? "display:none;" : "", ) ) . ">{$sDelimiter}</div>" : "" ) . "</div>" . $aField['after_field'] : ""; } $aExtraOutput[] = ( isset( $this->aField['description'] ) && trim( $this->aField['description'] ) != '' ) ? "<p class='admin-page-framework-fields-description'><span class='description'>{$this->aField['description']}</span></p>" : ''; $aExtraOutput[] = $this->aField['repeatable'] ? $this->_getRepeaterFieldEnablerScript( 'fields-' . $this->aField['tag_id'], count( $aFields ), $this->aField['repeatable'] ) : ''; $aExtraOutput[] = $this->aField['sortable'] && ( count( $aFields ) > 1 || $this->aField['repeatable'] ) ? $this->_getSortableFieldEnablerScript( 'fields-' . $this->aField['tag_id'] ) : ''; $_aFieldsSetAttributes = array( 'id' => 'fieldset-' . $this->aField['tag_id'], 'class' => 'admin-page-framework-fieldset', 'data-field_id' => $this->aField['tag_id'], ) + $this->aField['attributes']['fieldset']; $_aFieldsContainerAttributes = array( 'id' => 'fields-' . $this->aField['tag_id'], 'class' => 'admin-page-framework-fields' . ( $this->aField['repeatable'] ? ' repeatable' : '' ) . ( $this->aField['sortable'] ? ' sortable' : '' ), 'data-type' => $this->aField['type'], ) + $this->aField['attributes']['fields']; return "<fieldset " . $this->generateAttributes( $_aFieldsSetAttributes ) . ">" . "<div " . $this->generateAttributes( $_aFieldsContainerAttributes ) . ">" . $this->aField['before_fields'] . implode( PHP_EOL, $aFieldsOutput ) . $this->aField['after_fields'] . "</div>" . implode( PHP_EOL, $aExtraOutput ) . "</fieldset>"; } protected function _composeFieldsArray( &$aField, &$aOptions ) { $vSavedValue = $this->_getInputFieldValue( $aField, $aOptions ); $aFirstField = array(); $aSubFields = array(); foreach( $aField as $nsIndex => $vFieldElement ) { if ( is_numeric( $nsIndex ) ) $aSubFields[] = $vFieldElement; else $aFirstField[ $nsIndex ] = $vFieldElement; } if ( $aField['repeatable'] ) foreach( ( array ) $vSavedValue as $iIndex => $vValue ) { if ( $iIndex == 0 ) continue; $aSubFields[ $iIndex - 1 ] = isset( $aSubFields[ $iIndex - 1 ] ) && is_array( $aSubFields[ $iIndex - 1 ] ) ? $aSubFields[ $iIndex - 1 ] : array(); } foreach( $aSubFields as &$aSubField ) { $aLabel = isset( $aSubField['label'] ) ? $aSubField['label'] : ( isset( $aFirstField['label'] ) ? $aFirstField['label'] : null ); $aSubField = $this->uniteArrays( $aSubField, $aFirstField ); $aSubField['label'] = $aLabel; } $aFields = array_merge( array( $aFirstField ), $aSubFields ); if ( count( $aSubFields ) > 0 || $aField['repeatable'] || $aField['sortable'] ) { foreach( $aFields as $iIndex => &$aThisField ) { $aThisField['_saved_value'] = isset( $vSavedValue[ $iIndex ] ) ? $vSavedValue[ $iIndex ] : null; $aThisField['_is_multiple_fields'] = true; } } else { $aFields[ 0 ]['_saved_value'] = $vSavedValue; $aFields[ 0 ]['_is_multiple_fields'] = false; } unset( $aThisField ); foreach( $aFields as &$aThisField ) { $aThisField['_is_value_set_by_user'] = isset( $aThisField['value'] ); $aThisField['value'] = isset( $aThisField['value'] ) ? $aThisField['value'] : ( isset( $aThisField['_saved_value'] ) ? $aThisField['_saved_value'] : ( isset( $aThisField['default'] ) ? $aThisField['default'] : null ) ); } return $aFields; } } endif;if ( ! class_exists( 'AdminPageFramework_FormTable_Base' ) ) : class AdminPageFramework_FormTable_Base extends AdminPageFramework_WPUtility { public function __construct( $aFieldTypeDefinitions, array $aFieldErrors, $oMsg=null ) { $this->aFieldTypeDefinitions = $aFieldTypeDefinitions; $this->aFieldErrors = $aFieldErrors; $this->oMsg = $oMsg ? $oMsg: AdminPageFramework_Message::instantiate( '' ); $this->_loadScripts(); } private function _loadScripts() { static $_bIsLoadedTabPlugin; if ( ! $_bIsLoadedTabPlugin ) { $_bIsLoadedTabPlugin = add_action( 'admin_footer', array( $this, '_replyToAddTabPlugin' ) ); } } protected function _getAttributes( $aField, $aAttributes=array() ) { $_aAttributes = $aAttributes + ( isset( $aField['attributes']['fieldrow'] ) ? $aField['attributes']['fieldrow'] : array() ); if ( $aField['hidden'] ) $_aAttributes['style'] = 'display:none;' . ( isset( $_aAttributes['style'] ) ? $_aAttributes['style'] : '' ); return $this->generateAttributes( $_aAttributes ); } protected function _getFieldTitle( $aField ) { return "<label for='{$aField['field_id']}'>" . "<a id='{$aField['field_id']}'></a>" . "<span title='" . ( strip_tags( isset( $aField['tip'] ) ? $aField['tip'] : $aField['description'] ) ) . "'>" . $aField['title'] . "</span>" . "</label>"; } protected function _mergeDefault( $aField ) { return $this->uniteArrays( $aField, isset( $this->aFieldTypeDefinitions[ $aField['type'] ]['aDefaultKeys'] ) ? $this->aFieldTypeDefinitions[ $aField['type'] ]['aDefaultKeys'] : array() ); } public function _replyToAddRepeatableSectionjQueryPlugin() { static $bIsCalled = false; if ( $bIsCalled ) return; $bIsCalled = true; echo "<script type='text/javascript' class='admin-page-framework-repeatable-sections-plugin'>" . AdminPageFramework_Script_RepeatableSection::getjQueryPlugin( $this->oMsg->__( 'allowed_maximum_number_of_sections' ), $this->oMsg->__( 'allowed_minimum_number_of_sections' ) ) . "</script>"; } public function _replyToAddTabPlugin() { echo "<script type='text/javascript' class='admin-page-framework-tab-plugin'>" . AdminPageFramework_Script_Tab::getjQueryPlugin() . "</script>"; } } endif;if ( ! class_exists( 'AdminPageFramework_FormTable' ) ) : class AdminPageFramework_FormTable extends AdminPageFramework_FormTable_Base { public function getFormTables( $aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback ) { $_aOutput = array(); foreach( $this->_getSectionsBySectionTabs( $aSections ) as $_sSectionTabSlug => $_aSections ) { $_sSectionSet = $this->_getFormTablesBySectionTab( $_sSectionTabSlug, $_aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback ); if ( $_sSectionSet ) $_aOutput[] = "<div " . $this->generateAttributes( array( 'class' => 'admin-page-framework-sectionset', 'id' => "sectionset-{$_sSectionTabSlug}_" . md5( serialize( $_aSections ) ), ) ) . ">" . $_sSectionSet . "</div>"; } return implode( PHP_EOL, $_aOutput ) . $this->_getSectionTabsEnablerScript(); } private function _getSectionTabsEnablerScript() { static $bIsCalled = false; if ( $bIsCalled ) return ''; $bIsCalled = true; return "<script type='text/javascript'>
				jQuery( document ).ready( function() {
					jQuery( '.admin-page-framework-section-tabs-contents' ).createTabs();	// the parent element of the ul tag; The ul element holds li tags of titles.
					// jQuery( '.admin-page-framework-section-tabs-contents' ).tabs();	// the parent element of the ul tag; The ul element holds li tags of titles.
				});
			</script>"; } private function _getFormTablesBySectionTab( $sSectionTabSlug, $aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback ) { if ( empty( $aSections ) ) return ''; $_aSectionTabList = array(); $aOutput = array(); foreach( $aFieldsInSections as $_sSectionID => $aSubSectionsOrFields ) { if ( ! isset( $aSections[ $_sSectionID ] ) ) continue; $_sSectionTabSlug = $aSections[ $_sSectionID ]['section_tab_slug']; $_aSubSections = $aSubSectionsOrFields; $_aSubSections = $this->getIntegerElements( $_aSubSections ); $_iCountSubSections = count( $_aSubSections ); if ( $_iCountSubSections ) { if ( $aSections[ $_sSectionID ]['repeatable'] ) $aOutput[] = $this->getRepeatableSectionsEnablerScript( 'sections-' . md5( serialize( $aSections ) ), $_iCountSubSections, $aSections[ $_sSectionID ]['repeatable'] ); foreach( $this->numerizeElements( $_aSubSections ) as $_iIndex => $_aFields ) { $_sSectionTagID = 'section-' . $_sSectionID . '__' . $_iIndex; if ( $aSections[ $_sSectionID ]['section_tab_slug'] ) $_aSectionTabList[] = "<li class='admin-page-framework-section-tab nav-tab' id='section_tab-{$_sSectionTagID}'><a href='#{$_sSectionTagID}'>" . $this->_getSectionTitle( $aSections[ $_sSectionID ]['title'], 'h4', $_aFields, $hfFieldCallback ) ."</a></li>"; $aOutput[] = $this->getFormTable( $_sSectionTagID, $_iIndex, $aSections[ $_sSectionID ], $_aFields, $hfSectionCallback, $hfFieldCallback ); } } else { $_sSectionTagID = 'section-' . $_sSectionID . '__' . '0'; $_aFields = $aSubSectionsOrFields; if ( $aSections[ $_sSectionID ]['section_tab_slug'] ) $_aSectionTabList[] = "<li class='admin-page-framework-section-tab nav-tab' id='section_tab-{$_sSectionTagID}'><a href='#{$_sSectionTagID}'>" . $this->_getSectionTitle( $aSections[ $_sSectionID ]['title'], 'h4', $_aFields, $hfFieldCallback ) . "</a></li>"; $aOutput[] = $this->getFormTable( $_sSectionTagID, 0, $aSections[ $_sSectionID ], $_aFields, $hfSectionCallback, $hfFieldCallback ); } } if ( empty( $aOutput ) ) return ''; return "<div " . $this->generateAttributes( array( 'class' => 'admin-page-framework-sections' . ( ! $_sSectionTabSlug || $_sSectionTabSlug == '_default' ? '' : ' admin-page-framework-section-tabs-contents' ), 'id' => "sections-" . md5( serialize( $aSections ) ), ) ) . ">" . ( $_sSectionTabSlug ? "<ul class='admin-page-framework-section-tabs nav-tab-wrapper'>" . implode( PHP_EOL, $_aSectionTabList ) . "</ul>" : '' ) . implode( PHP_EOL, $aOutput ) . "</div>"; } private function _getSectionTitle( $sTitle, $sTag, $aFields, $hfFieldCallback ) { $aSectionTitleField = $this->_getSectionTitleField( $aFields ); return $aSectionTitleField ? call_user_func_array( $hfFieldCallback, array( $aSectionTitleField ) ) : "<{$sTag}>" . $sTitle . "</{$sTag}>"; } private function _getSectionTitleField( $aFields ) { foreach( $aFields as $aField ) if ( $aField['type'] == 'section_title' ) return $aField; } private function _getSectionsBySectionTabs( array $aSections ) { $_aSectionsBySectionTab = array(); $iIndex = 0; foreach( $aSections as $_aSection ) { if ( ! $_aSection['section_tab_slug'] ) { $_aSectionsBySectionTab[ '_default_' . $iIndex ][ $_aSection['section_id'] ] = $_aSection; $iIndex++; continue; } $_sSectionTaqbSlug = $_aSection['section_tab_slug']; $_aSectionsBySectionTab[ $_sSectionTaqbSlug ] = isset( $_aSectionsBySectionTab[ $_sSectionTaqbSlug ] ) && is_array( $_aSectionsBySectionTab[ $_sSectionTaqbSlug ] ) ? $_aSectionsBySectionTab[ $_sSectionTaqbSlug ] : array(); $_aSectionsBySectionTab[ $_sSectionTaqbSlug ][ $_aSection['section_id'] ] = $_aSection; } return $_aSectionsBySectionTab; } private function getRepeatableSectionsEnablerScript( $sContainerTagID, $iSectionCount, $aSettings ) { add_action( 'admin_footer', array( $this, '_replyToAddRepeatableSectionjQueryPlugin' ) ); if ( empty( $aSettings ) ) return ''; $aSettings = ( is_array( $aSettings ) ? $aSettings : array() ) + array( 'min' => 0, 'max' => 0 ); $_sAdd = $this->oMsg->__( 'add_section' ); $_sRemove = $this->oMsg->__( 'remove_section' ); $_sVisibility = $iSectionCount <= 1 ? " style='display:none;'" : ""; $_sSettingsAttributes = $this->generateDataAttributes( $aSettings ); $_sButtons = "<div class='admin-page-framework-repeatable-section-buttons' {$_sSettingsAttributes} >" . "<a class='repeatable-section-add button-secondary repeatable-section-button button button-large' href='#' title='{$_sAdd}' data-id='{$sContainerTagID}'>+</a>" . "<a class='repeatable-section-remove button-secondary repeatable-section-button button button-large' href='#' title='{$_sRemove}' {$_sVisibility} data-id='{$sContainerTagID}'>-</a>" . "</div>"; $aJSArray = json_encode( $aSettings ); return "<script type='text/javascript'>
					jQuery( document ).ready( function() {
						jQuery( '#{$sContainerTagID} .admin-page-framework-section-caption' ).show().prepend( \"{$_sButtons}\" );	// Adds the buttons
						jQuery( '#{$sContainerTagID}' ).updateAPFRepeatableSections( {$aJSArray} );	// Update the fields			
					});
				</script>"; } public function getFormTable( $sSectionTagID, $iSectionIndex, $aSection, $aFields, $hfSectionCallback, $hfFieldCallback ) { if ( count( $aFields ) <= 0 ) return ''; $_sDisplayNone = ( $aSection['repeatable'] && $iSectionIndex != 0 && ! $aSection['section_tab_slug'] ) ? " style='display:none;'" : ''; $_sSectionError = isset( $this->aFieldErrors[ $aSection['section_id'] ] ) && is_string( $this->aFieldErrors[ $aSection['section_id'] ] ) ? $this->aFieldErrors[ $aSection['section_id'] ] : ''; $_aOutput = array(); $_aOutput[] = "<table " . $this->generateAttributes( array( 'id' => 'section_table-' . $sSectionTagID, 'class' => 'form-table', ) ) . ">" . ( $aSection['description'] || $aSection['title'] ? "<caption class='admin-page-framework-section-caption' data-section_tab='{$aSection['section_tab_slug']}'>" . ( $aSection['title'] && ! $aSection['section_tab_slug'] ? "<div class='admin-page-framework-section-title' {$_sDisplayNone}>" . $this->_getSectionTitle( $aSection['title'], 'h3', $aFields, $hfFieldCallback ) . "</div>" : "" ) . ( $aSection['description'] && is_callable( $hfSectionCallback ) ? "<div class='admin-page-framework-section-description'>" . call_user_func_array( $hfSectionCallback, array( '<p>' . $aSection['description'] . '</p>', $aSection ) ) . "</div>" : "" ) . ( $_sSectionError ? "<div class='admin-page-framework-error'><span style='color:red;'>* " . $_sSectionError . "</span></div>" : '' ) . "</caption>" : "<caption class='admin-page-framework-section-caption' style='display:none;'></caption>" ) . $this->getFieldRows( $aFields, $hfFieldCallback ) . "</table>"; return "<div " . $this->generateAttributes( array( 'id' => $sSectionTagID, 'class' => 'admin-page-framework-section' . ( $aSection['section_tab_slug'] ? ' admin-page-framework-tab-content' : '' ), ) ) . ">" . implode( PHP_EOL, $_aOutput ) . "</div>"; } public function getFieldRows( $aFields, $hfCallback ) { if ( ! is_callable( $hfCallback ) ) return ''; $aOutput = array(); foreach( $aFields as $aField ) $aOutput[] = $this->_getFieldRow( $aField, $hfCallback ); return implode( PHP_EOL, $aOutput ); } protected function _getFieldRow( $aField, $hfCallback ) { if ( $aField['type'] == 'section_title' ) return ''; $aOutput = array(); $_aField = $this->_mergeDefault( $aField ); $_sAttributes = $this->_getAttributes( $_aField, array( 'id' => 'fieldrow-' . AdminPageFramework_FormField::_getInputTagID( $_aField ), 'valign' => 'top', 'class' => 'admin-page-framework-fieldrow', ) ); $aOutput[] = "<tr {$_sAttributes}>"; if ( $_aField['show_title_column'] ) $aOutput[] = "<th>" . $this->_getFieldTitle( $_aField ) . "</th>"; $aOutput[] = "<td>" . call_user_func_array( $hfCallback, array( $aField ) ) . "</td>"; $aOutput[] = "</tr>"; return implode( PHP_EOL, $aOutput ); } public function getFields( $aFields, $hfCallback ) { if ( ! is_callable( $hfCallback ) ) return ''; $aOutput = array(); foreach( $aFields as $aField ) $aOutput[] = $this->_getField( $aField, $hfCallback ); return implode( PHP_EOL, $aOutput ); } protected function _getField( $aField, $hfCallback ) { if ( $aField['type'] == 'section_title' ) return ''; $aOutput = array(); $_aField = $this->_mergeDefault( $aField ); $aOutput[] = "<div " . $this->_getAttributes( $_aField ) . ">"; if ( $_aField['show_title_column'] ) $aOutput[] = $this->_getFieldTitle( $_aField ); $aOutput[] = call_user_func_array( $hfCallback, array( $aField ) ); $aOutput[] = "</div>"; return implode( PHP_EOL, $aOutput ); } } endif;if ( ! class_exists( 'AdminPageFramework_Link_Page' ) ) : class AdminPageFramework_Link_Page extends AdminPageFramework_Link_Base { private $oProp; public function __construct( &$oProp, $oMsg=null ) { if ( ! is_admin() ) return; $this->oProp = $oProp; $this->oMsg = $oMsg; add_filter( 'update_footer', array( $this, '_replyToAddInfoInFooterRight' ), 11 ); add_filter( 'admin_footer_text' , array( $this, '_replyToAddInfoInFooterLeft' ) ); $this->_setFooterInfoLeft( $this->oProp->aScriptInfo, $this->oProp->aFooterInfo['sLeft'] ); $aLibraryData = AdminPageFramework_Property_Base::_getLibraryData(); $aLibraryData['sVersion'] = $this->oProp->bIsMinifiedVersion ? $aLibraryData['sVersion'] . '.min' : $aLibraryData['sVersion']; $this->_setFooterInfoRight( $aLibraryData, $this->oProp->aFooterInfo['sRight'] ); if ( $this->oProp->aScriptInfo['sType'] == 'plugin' ) add_filter( 'plugin_action_links_' . plugin_basename( $this->oProp->aScriptInfo['sPath'] ) , array( $this, '_replyToAddSettingsLinkInPluginListingPage' ) ); } public function _addLinkToPluginDescription( $linkss ) { if ( !is_array( $linkss ) ) $this->oProp->aPluginDescriptionLinks[] = $linkss; else $this->oProp->aPluginDescriptionLinks = array_merge( $this->oProp->aPluginDescriptionLinks , $linkss ); add_filter( 'plugin_row_meta', array( $this, '_replyToAddLinkToPluginDescription' ), 10, 2 ); } public function _addLinkToPluginTitle( $linkss ) { if ( !is_array( $linkss ) ) $this->oProp->aPluginTitleLinks[] = $linkss; else $this->oProp->aPluginTitleLinks = array_merge( $this->oProp->aPluginTitleLinks, $linkss ); add_filter( 'plugin_action_links_' . plugin_basename( $this->oProp->aScriptInfo['sPath'] ), array( $this, '_replyToAddLinkToPluginTitle' ) ); } public function _replyToAddInfoInFooterLeft( $sLinkHTML='' ) { if ( ! isset( $_GET['page'] ) || ! $this->oProp->isPageAdded( $_GET['page'] ) ) return $sLinkHTML; if ( empty( $this->oProp->aScriptInfo['sName'] ) ) return $sLinkHTML; return $this->oProp->aFooterInfo['sLeft']; } public function _replyToAddInfoInFooterRight( $sLinkHTML='' ) { if ( ! isset( $_GET['page'] ) || ! $this->oProp->isPageAdded( $_GET['page'] ) ) return $sLinkHTML; return $this->oProp->aFooterInfo['sRight']; } public function _replyToAddSettingsLinkInPluginListingPage( $aLinks ) { if ( count( $this->oProp->aPages ) < 1 ) return $aLinks; $sLinkURL = preg_match( '/^.+\.php/', $this->oProp->aRootMenu['sPageSlug'] ) ? add_query_arg( array( 'page' => $this->oProp->sDefaultPageSlug ), admin_url( $this->oProp->aRootMenu['sPageSlug'] ) ) : "admin.php?page={$this->oProp->sDefaultPageSlug}"; array_unshift( $aLinks, '<a href="' . $sLinkURL . '">' . $this->oMsg->__( 'settings' ) . '</a>' ); return $aLinks; } public function _replyToAddLinkToPluginDescription( $aLinks, $sFile ) { if ( $sFile != plugin_basename( $this->oProp->aScriptInfo['sPath'] ) ) return $aLinks; $aAddingLinks = array(); foreach( $this->oProp->aPluginDescriptionLinks as $linksHTML ) if ( is_array( $linksHTML ) ) $aAddingLinks = array_merge( $linksHTML, $aAddingLinks ); else $aAddingLinks[] = ( string ) $linksHTML; return array_merge( $aLinks, $aAddingLinks ); } public function _replyToAddLinkToPluginTitle( $aLinks ) { $aAddingLinks = array(); foreach( $this->oProp->aPluginTitleLinks as $linksHTML ) if ( is_array( $linksHTML ) ) $aAddingLinks = array_merge( $linksHTML, $aAddingLinks ); else $aAddingLinks[] = ( string ) $linksHTML; return array_merge( $aLinks, $aAddingLinks ); } } endif;if ( ! class_exists( 'AdminPageFramework_Link_PostType' ) ) : class AdminPageFramework_Link_PostType extends AdminPageFramework_Link_Base { public $aFooterInfo = array( 'sLeft' => '', 'sRight' => '', ); public function __construct( $oProp, $oMsg=null ) { if ( ! is_admin() ) return; $this->oProp = $oProp; $this->oMsg = $oMsg; $this->sSettingPageLinkTitle = $this->oMsg->__( 'manage' ); add_filter( 'update_footer', array( $this, '_replyToAddInfoInFooterRight' ), 11 ); add_filter( 'admin_footer_text' , array( $this, '_replyToAddInfoInFooterLeft' ) ); $this->_setFooterInfoLeft( $this->oProp->aScriptInfo, $this->aFooterInfo['sLeft'] ); $aLibraryData = $this->oProp->_getLibraryData(); $aLibraryData['sVersion'] = $this->oProp->bIsMinifiedVersion ? $aLibraryData['sVersion'] . '.min' : $aLibraryData['sVersion']; $this->_setFooterInfoRight( $aLibraryData, $this->aFooterInfo['sRight'] ); if ( $this->oProp->aScriptInfo['sType'] == 'plugin' ) add_filter( 'plugin_action_links_' . plugin_basename( $this->oProp->aScriptInfo['sPath'] ), array( $this, '_replyToAddSettingsLinkInPluginListingPage' ), 20 ); if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->oProp->sPostType ) add_action( 'get_edit_post_link', array( $this, '_replyToAddPostTypeQueryInEditPostLink' ), 10, 3 ); } public function _replyToAddPostTypeQueryInEditPostLink( $sURL, $iPostID=null, $sContext=null ) { return add_query_arg( array( 'post' => $iPostID, 'action' => 'edit', 'post_type' => $this->oProp->sPostType ), $sURL ); } public function _replyToAddSettingsLinkInPluginListingPage( $aLinks ) { array_unshift( $aLinks, "<a href='edit.php?post_type={$this->oProp->sPostType}'>" . $this->sSettingPageLinkTitle . "</a>" ); return $aLinks; } public function _replyToAddInfoInFooterLeft( $sLinkHTML='' ) { if ( ! $this->isPostDefinitionPage( $this->oProp->sPostType ) && ! $this->isPostListingPage( $this->oProp->sPostType ) ) return $sLinkHTML; if ( empty( $this->oProp->aScriptInfo['sName'] ) ) return $sLinkHTML; return $this->aFooterInfo['sLeft']; } public function _replyToAddInfoInFooterRight( $sLinkHTML='' ) { if ( ! $this->isPostDefinitionPage( $this->oProp->sPostType ) && ! $this->isPostListingPage( $this->oProp->sPostType ) ) return $sLinkHTML; return $this->aFooterInfo['sRight']; } } endif;if ( ! class_exists( 'AdminPageFramework_FormElement_Page' ) ) : class AdminPageFramework_FormElement_Page extends AdminPageFramework_FormElement { protected $sDefaultPageSlug; public function isPageAdded( $sPageSlug ) { foreach( $this->aSections as $_sSectionID => $_aSection ) { if ( isset( $_aSection['page_slug'] ) && $_aSection['page_slug'] == $sPageSlug ) return true; } return false; } public function getFieldsByPageSlug( $sPageSlug, $sTabSlug='' ) { return $this->castArrayContents( $this->getSectionsByPageSlug( $sPageSlug, $sTabSlug ), $this->aFields ); } public function getSectionsByPageSlug( $sPageSlug, $sTabSlug='' ) { $_aSections = array(); foreach( $this->aSections as $_sSecitonID => $_aSection ) { if ( $sTabSlug && $_aSection['tab_slug'] != $sTabSlug ) continue; if ( $_aSection['page_slug'] != $sPageSlug ) continue; $_aSections[ $_sSecitonID ] = $_aSection; } uasort( $_aSections, array( $this, '_sortByOrder' ) ); return $_aSections; } public function getPageSlugBySectionID( $sSectionID ) { return isset( $this->aSections[ $sSectionID ]['page_slug'] ) ? $this->aSections[ $sSectionID ]['page_slug'] : null; } public function setDefaultPageSlug( $sDefaultPageSlug ) { $this->sDefaultPageSlug = $sDefaultPageSlug; } public function setOptionKey( $sOptionKey ) { $this->sOptionKey = $sOptionKey; } public function setCallerClassName( $sClassName ) { $this->sClassName = $sClassName; } public function setCurrentPageSlug( $sCurrentPageSlug ) { $this->sCurrentPageSlug = $sCurrentPageSlug; } public function setCurrentTabSlug( $sCurrentTabSlug ) { $this->sCurrentTabSlug = $sCurrentTabSlug; } protected function formatSection( array $aSection, $sFieldsType, $sCapability, $iCountOfElements ) { $aSection = $this->uniteArrays( $aSection, array( '_fields_type' => $sFieldsType, 'capability' => $sCapability, 'page_slug' => $this->sDefaultPageSlug, ), self::$_aStructure_Section ); $aSection['order'] = is_numeric( $aSection['order'] ) ? $aSection['order'] : $iCountOfElements + 10; return $aSection; } protected function formatField( $aField, $sFieldsType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable ) { $_aField = parent::formatField( $aField, $sFieldsType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable ); if ( ! $_aField ) return; $_aField['option_key'] = $this->sOptionKey; $_aField['class_name'] = $this->sClassName; $_aField['page_slug'] = isset( $this->aSections[ $_aField['section_id'] ]['page_slug'] ) ? $this->aSections[ $_aField['section_id'] ]['page_slug'] : null; $_aField['tab_slug'] = isset( $this->aSections[ $_aField['section_id'] ]['tab_slug'] ) ? $this->aSections[ $_aField['section_id'] ]['tab_slug'] : null; $_aField['section_title'] = isset( $this->aSections[ $_aField['section_id'] ]['title'] ) ? $this->aSections[ $_aField['section_id'] ]['title'] : null; return $_aField; } protected function getConditionedSection( array $aSection ) { if ( ! current_user_can( $aSection['capability'] ) ) return; if ( ! $aSection['if'] ) return; if ( ! $aSection['page_slug'] ) return; if ( $GLOBALS['pagenow'] != 'options.php' && $this->sCurrentPageSlug != $aSection['page_slug'] ) return; if ( ! $this->_isSectionOfCurrentTab( $aSection, $this->sCurrentPageSlug, $this->sCurrentTabSlug ) ) return; return $aSection; } private function _isSectionOfCurrentTab( $aSection, $sCurrentPageSlug, $sCurrentTabSlug ) { if ( $aSection['page_slug'] != $sCurrentPageSlug ) return false; if ( ! isset( $aSection['tab_slug'] ) ) return true; if ( $aSection['tab_slug'] == $sCurrentTabSlug ) return true; return false; } protected function getConditionedField( $aField ) { if ( ! current_user_can( $aField['capability'] ) ) return null; if ( ! $aField['if'] ) return null; return $aField; } public function getPageOptions( $aOptions, $sPageSlug ) { $_aOtherPageOptions = $this->getOtherPageOptions( $aOptions, $sPageSlug ); return $this->invertCastArrayContents( $aOptions, $_aOtherPageOptions ); } public function getPageOnlyOptions( $aOptions, $sPageSlug ) { $_aStoredOptionsOfThePage = array(); foreach( $this->aFields as $_sSectionID => $_aFields ) { if ( isset( $this->aSections[ $_sSectionID ]['page_slug'] ) && $this->aSections[ $_sSectionID ]['page_slug'] != $sPageSlug ) continue; foreach( $_aFields as $_sFieldID => $_aField ) { if ( ! isset( $_aField['page_slug'] ) || $_aField['page_slug'] != $sPageSlug ) continue; if ( is_numeric( $_sFieldID ) && is_int( $_sFieldID + 0 ) ) { if ( array_key_exists( $_sSectionID, $aOptions ) ) $_aStoredOptionsOfThePage[ $_sSectionID ] = $aOptions[ $_sSectionID ]; continue; } if ( isset( $_aField['section_id'] ) && $_aField['section_id'] != '_default' ) { if ( array_key_exists( $_aField['section_id'], $aOptions ) ) $_aStoredOptionsOfThePage[ $_aField['section_id'] ] = $aOptions[ $_aField['section_id'] ]; continue; } if ( array_key_exists( $_aField['field_id'], $aOptions ) ) $_aStoredOptionsOfThePage[ $_aField['field_id'] ] = $aOptions[ $_aField['field_id'] ]; } } return $_aStoredOptionsOfThePage; } public function getOtherPageOptions( $aOptions, $sPageSlug ) { $_aStoredOptionsNotOfThePage = array(); foreach( $this->aFields as $_sSectionID => $_aFields ) { if ( isset( $this->aSections[ $_sSectionID ]['page_slug'] ) && $this->aSections[ $_sSectionID ]['page_slug'] == $sPageSlug ) continue; foreach( $_aFields as $_sFieldID => $_aField ) { if ( ! isset( $_aField['page_slug'] ) ) continue; if ( $_aField['page_slug'] == $sPageSlug ) continue; if ( is_numeric( $_sFieldID ) && is_int( $_sFieldID + 0 ) ) continue; if ( isset( $_aField['section_id'] ) && $_aField['section_id'] != '_default' ) { if ( array_key_exists( $_aField['section_id'], $aOptions ) ) $_aStoredOptionsNotOfThePage[ $_aField['section_id'] ] = $aOptions[ $_aField['section_id'] ]; continue; } if ( array_key_exists( $_aField['field_id'], $aOptions ) ) $_aStoredOptionsNotOfThePage[ $_aField['field_id'] ] = $aOptions[ $_aField['field_id'] ]; } } return $_aStoredOptionsNotOfThePage; } public function getOtherTabOptions( $aOptions, $sPageSlug, $sTabSlug ) { $_aStoredOptionsNotOfTheTab = array(); foreach( $this->aFields as $_sSectionID => $_aSubSectionsOrFields ) { if ( isset( $this->aSections[ $_sSectionID ]['page_slug'] ) && $this->aSections[ $_sSectionID ]['page_slug'] == $sPageSlug && isset( $this->aSections[ $_sSectionID ]['tab_slug'] ) && $this->aSections[ $_sSectionID ]['tab_slug'] == $sTabSlug ) continue; foreach ( $_aSubSectionsOrFields as $_isSubSectionIndexOrFieldID => $_aSubSectionOrField ) { if ( is_numeric( $_isSubSectionIndexOrFieldID ) && is_int( $_isSubSectionIndexOrFieldID + 0 ) ) { if ( array_key_exists( $_sSectionID, $aOptions ) ) $_aStoredOptionsNotOfTheTab[ $_sSectionID ] = $aOptions[ $_sSectionID ]; continue; } $_aField = $_aSubSectionOrField; if ( isset( $_aField['section_id'] ) && $_aField['section_id'] != '_default' ) { if ( array_key_exists( $_aField['section_id'], $aOptions ) ) $_aStoredOptionsNotOfTheTab[ $_aField['section_id'] ] = $aOptions[ $_aField['section_id'] ]; continue; } if ( array_key_exists( $_aField['field_id'], $aOptions ) ) $_aStoredOptionsNotOfTheTab[ $_aField['field_id'] ] = $aOptions[ $_aField['field_id'] ]; } } return $_aStoredOptionsNotOfTheTab; } public function getTabOptions( $aOptions, $sPageSlug, $sTabSlug='' ) { $_aOtherTabOptions = $this->getOtherTabOptions( $aOptions, $sPageSlug, $sTabSlug ); return $this->invertCastArrayContents( $aOptions, $_aOtherTabOptions ); } public function getTabOnlyOptions( $aOptions, $sPageSlug, $sTabSlug='' ) { $_aStoredOptionsOfTheTab = array(); if ( ! $sTabSlug ) return $_aStoredOptionsOfTheTab; foreach( $this->aFields as $_sSectionID => $_aSubSectionsOrFields ) { if ( isset( $this->aSections[ $_sSectionID ]['page_slug'] ) && $this->aSections[ $_sSectionID ]['page_slug'] != $sPageSlug ) continue; if ( isset( $this->aSections[ $_sSectionID ]['tab_slug'] ) && $this->aSections[ $_sSectionID ]['tab_slug'] != $sTabSlug ) continue; foreach( $_aSubSectionsOrFields as $_sFieldID => $_aField ) { if ( is_numeric( $_sFieldID ) && is_int( $_sFieldID + 0 ) ) { if ( array_key_exists( $_sSectionID, $aOptions ) ) $_aStoredOptionsOfTheTab[ $_sSectionID ] = $aOptions[ $_sSectionID ]; continue; } if ( isset( $_aField['section_id'] ) && $_aField['section_id'] != '_default' ) { if ( array_key_exists( $_aField['section_id'], $aOptions ) ) $_aStoredOptionsOfTheTab[ $_aField['section_id'] ] = $aOptions[ $_aField['section_id'] ]; continue; } if ( array_key_exists( $_aField['field_id'], $aOptions ) ) $_aStoredOptionsOfTheTab[ $_aField['field_id'] ] = $aOptions[ $_aField['field_id'] ]; } } return $_aStoredOptionsOfTheTab; } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_checkbox' ) ) : class AdminPageFramework_FieldType_checkbox extends AdminPageFramework_FieldType_Base { public $aFieldTypeSlugs = array( 'checkbox' ); protected $aDefaultKeys = array( ); public function _replyToFieldLoader() { } public function _replyToGetScripts() { return ""; } public function _replyToGetStyles() { return "/* Checkbox field type */
			.admin-page-framework-field input[type='checkbox'] {
				margin-right: 0.5em;
			}			
			.admin-page-framework-field-checkbox .admin-page-framework-input-label-container {
				padding-right: 1em;
			}
		"; } public function _replyToGetField( $aField ) { $aOutput = array(); $asValue = $aField['attributes']['value']; foreach( ( array ) $aField['label'] as $sKey => $sLabel ) { $aInputAttributes = array( 'type' => 'checkbox', 'id' => $aField['input_id'] . '_' . $sKey, 'checked' => $this->getCorrespondingArrayValue( $asValue, $sKey, null ) == 1 ? 'checked' : '', 'value' => 1, 'name' => is_array( $aField['label'] ) ? "{$aField['attributes']['name']}[{$sKey}]" : $aField['attributes']['name'], ) + $this->getFieldElementByKey( $aField['attributes'], $sKey, $aField['attributes'] ) + $aField['attributes']; $aLabelAttributes = array( 'for' => $aInputAttributes['id'], 'class' => $aInputAttributes['disabled'] ? 'disabled' : '', ); $aOutput[] = $this->getFieldElementByKey( $aField['before_label'], $sKey ) . "<div class='admin-page-framework-input-label-container admin-page-framework-checkbox-label' style='min-width: {$aField['label_min_width']}px;'>" . "<label " . $this->generateAttributes( $aLabelAttributes ) . ">" . $this->getFieldElementByKey( $aField['before_input'], $sKey ) . "<span class='admin-page-framework-input-container'>" . "<input type='hidden' name='{$aInputAttributes['name']}' value='0' />" . "<input " . $this->generateAttributes( $aInputAttributes ) . " />" . "</span>" . "<span class='admin-page-framework-input-label-string'>" . $sLabel . "</span>" . $this->getFieldElementByKey( $aField['after_input'], $sKey ) . "</label>" . "</div>" . $this->getFieldElementByKey( $aField['after_label'], $sKey ); } return implode( PHP_EOL, $aOutput ); } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_color' ) ) : class AdminPageFramework_FieldType_color extends AdminPageFramework_FieldType_Base { public $aFieldTypeSlugs = array( 'color' ); protected $aDefaultKeys = array( 'attributes' => array( 'size' => 10, 'maxlength' => 400, 'value' => 'transparent', ), ); public function _replyToFieldLoader() { if ( version_compare( $GLOBALS['wp_version'], '3.5', '>=' ) ) { wp_enqueue_style( 'wp-color-picker' ); wp_enqueue_script( 'wp-color-picker' ); } else { wp_enqueue_style( 'farbtastic' ); wp_enqueue_script( 'farbtastic' ); } } public function _replyToGetStyles() { return "/* Color Picker */
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
			.admin-page-framework-field-color .admin-page-framework-field .admin-page-framework-input-label-container {
				vertical-align: top; 
			}
			.admin-page-framework-field-color .admin-page-framework-repeatable-field-buttons {
				margin-top: 0;
			}
			" . PHP_EOL; } public function _replyToGetScripts() { $aJSArray = json_encode( $this->aFieldTypeSlugs ); return "
			registerAPFColorPickerField = function( osTragetInput ) {
				
				var osTargetInput = typeof osTragetInput === 'string' ? '#' + osTragetInput : osTragetInput;
				var sInputID = typeof osTragetInput === 'string' ? osTragetInput : osTragetInput.attr( 'id' );
				
				'use strict';
				/* This if statement checks if the color picker element exists within jQuery UI
				 If it does exist then we initialize the WordPress color picker on our text input field */
				if( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function' ){
					var aColorPickerOptions = {
						defaultColor: false,	// you can declare a default color here, or in the data-default-color attribute on the input				
						change: function(event, ui){},	// a callback to fire whenever the color changes to a valid color. reference : http://automattic.github.io/Iris/			
						clear: function() {},	// a callback to fire when the input is emptied or an invalid color
						hide: true,	// hide the color picker controls on load
						palettes: true	// show a group of common colors beneath the square or, supply an array of colors to customize further
					};			

					jQuery( osTargetInput ).wpColorPicker( aColorPickerOptions );
			
				}
				else {
					/* We use farbtastic if the WordPress color picker widget doesn't exist */
					jQuery( '#color_' + sInputID ).farbtastic( osTargetInput );
				}
			}
			
			/*	The below function will be triggered when a new repeatable field is added. Since the APF repeater script does not
				renew the color piker element (while it does on the input tag value), the renewal task must be dealt here separately. */
			jQuery( document ).ready( function(){
				jQuery().registerAPFCallback( {				
					added_repeatable_field: function( node, sFieldType, sFieldTagID, sCallType ) {
			
						/* If it is not the color field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;
						
						/* If the input tag is not found, do nothing  */
						var nodeNewColorInput = node.find( 'input.input_color' );
						if ( nodeNewColorInput.length <= 0 ) return;
						
						var nodeIris = node.find( '.wp-picker-container' ).first();
						if ( nodeIris.length > 0 ) {	// WP 3.5+
							var nodeNewColorInput = nodeNewColorInput.clone();	// unbind the existing color picker script in case there is.
						}
						var sInputID = nodeNewColorInput.attr( 'id' );

						/* Reset the value of the color picker */
						var sInputValue = nodeNewColorInput.val() ? nodeNewColorInput.val() : 'transparent';	// For WP 3.4.x or below
						var sInputStyle = sInputValue != 'transparent' && nodeNewColorInput.attr( 'style' ) ? nodeNewColorInput.attr( 'style' ) : '';
						nodeNewColorInput.val( sInputValue );	// set the default value	
						nodeNewColorInput.attr( 'style', sInputStyle );	// remove the background color set to the input field ( for WP 3.4.x or below )						 

						/* Replace the old color picker elements with the new one */
						if ( nodeIris.length > 0 ) {	// WP 3.5+
							jQuery( nodeIris ).replaceWith( nodeNewColorInput );
						} 
						else {	// WP 3.4.x -				
							node.find( '.colorpicker' ).replaceWith( '<div class=\'colorpicker\' id=\'color_' + sInputID + '\'></div>' );	
						}
			
						/* Bind the color picker script */					
						registerAPFColorPickerField( nodeNewColorInput );											
						
					}
				});
			});
		"; } public function _replyToGetField( $aField ) { $aField['attributes'] = array( 'color' => $aField['value'], 'type' => 'text', 'class' => trim( 'input_color ' . $aField['attributes']['class'] ), ) + $aField['attributes']; return $aField['before_label'] . "<div class='admin-page-framework-input-label-container'>" . "<label for='{$aField['input_id']}'>" . $aField['before_input'] . ( $aField['label'] && ! $aField['repeatable'] ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>" : "" ) . "<input " . $this->generateAttributes( $aField['attributes'] ) . " />" . $aField['after_input'] . "<div class='repeatable-field-buttons'></div>" . "</label>" . "<div class='colorpicker' id='color_{$aField['input_id']}'></div>" . $this->_getColorPickerEnablerScript( "{$aField['input_id']}" ) . "</div>" . $aField['after_label']; } private function _getColorPickerEnablerScript( $sInputID ) { return "<script type='text/javascript' class='color-picker-enabler-script'>
					jQuery( document ).ready( function(){
						registerAPFColorPickerField( '{$sInputID}' );
					});
				</script>"; } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_default' ) ) : class AdminPageFramework_FieldType_default extends AdminPageFramework_FieldType_Base { public $aDefaultKeys = array( ); public function _replyToFieldLoader() { } public function _replyToGetScripts() { return ""; } public function _replyToGetStyles() { return ""; } public function _replyToGetField( $aField ) { return $aField['before_label'] . "<div class='admin-page-framework-input-label-container'>" . "<label for='{$aField['input_id']}'>" . $aField['before_input'] . ( $aField['label'] && ! $aField['repeatable'] ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>" : "" ) . $aField['value'] . $aField['after_input'] . "</label>" . "</div>" . $aField['after_label'] ; } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_hidden' ) ) : class AdminPageFramework_FieldType_hidden extends AdminPageFramework_FieldType_Base { public $aFieldTypeSlugs = array( 'hidden' ); protected $aDefaultKeys = array(); public function _replyToFieldLoader() { } public function _replyToGetScripts() { return ""; } public function _replyToGetStyles() { return ""; } public function _replyToGetField( $aField ) { return $aField['before_label'] . "<div class='admin-page-framework-input-label-container'>" . "<label for='{$aField['input_id']}'>" . $aField['before_input'] . ( $aField['label'] ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>" : "" ) . "<input " . $this->generateAttributes( $aField['attributes'] ) . " />" . $aField['after_input'] . "</label>" . "</div>" . $aField['after_label']; } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_image' ) ) : class AdminPageFramework_FieldType_image extends AdminPageFramework_FieldType_Base { public $aFieldTypeSlugs = array( 'image', ); protected $aDefaultKeys = array( 'attributes_to_store' => array(), 'show_preview' => true, 'allow_external_source' => true, 'attributes' => array( 'input' => array( 'size' => 40, 'maxlength' => 400, ), 'button' => array( ), 'preview' => array( ), ), ); public function _replyToFieldLoader() { $this->enqueueMediaUploader(); } public function _replyToGetScripts() { return $this->_getScript_CustomMediaUploaderObject() . PHP_EOL . $this->_getScript_ImageSelector( "admin_page_framework", $this->oMsg->__( 'upload_image' ), $this->oMsg->__( 'use_this_image' ) ) . PHP_EOL . $this->_getScript_RegisterCallbacks(); } protected function _getScript_RegisterCallbacks() { $aJSArray = json_encode( $this->aFieldTypeSlugs ); return"
			jQuery( document ).ready( function(){
		
				jQuery().registerAPFCallback( {				
					/**
					 * The repeatable field callback.
					 * 
					 * @param	object	node
					 * @param	string	the field type slug
					 * @param	string	the field container tag ID
					 * @param	integer	the caller type. 1 : repeatable sections. 0 : repeatable fields.
					 */
					added_repeatable_field: function( node, sFieldType, sFieldTagID, iCallType ) {
						
						/* If it is not the image field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;
											
						/* If the uploader buttons are not found, do nothing */
						if ( node.find( '.select_image' ).length <= 0 )  return;
						
						/* Remove the value of the cloned preview element - check the value for repeatable sections */
						var sValue = node.find( 'input' ).first().val();
						if ( iCallType !== 1 || ! sValue ) {	// if it's not for repeatable sections
							node.find( '.image_preview' ).hide();					// for the image field type, hide the preview element
							node.find( '.image_preview img' ).attr( 'src', '' );	// for the image field type, empty the src property for the image uploader field
						}
						
						/* Increment the ids of the next all (including this one) uploader buttons and the preview elements ( the input values are already dealt by the framework repeater script ) */
						var nodeFieldContainer = node.closest( '.admin-page-framework-field' );
						var iOccurence = iCallType === 1 ? 1 : 0;
						nodeFieldContainer.nextAll().andSelf().each( function( iIndex ) {

							var nodeButton = jQuery( this ).find( '.select_image' );							
							
							// If it's for repeatable sections, updating the attributes is only necessary for the first iteration.
							if ( ! ( iCallType === 1 && iIndex !== 0 ) ) {
									
								nodeButton.incrementIDAttribute( 'id', iOccurence );
								jQuery( this ).find( '.image_preview' ).incrementIDAttribute( 'id', iOccurence );
								jQuery( this ).find( '.image_preview img' ).incrementIDAttribute( 'id', iOccurence );
								
							}
							
							/* Rebind the uploader script to each button. The previously assigned ones also need to be renewed; 
							 * otherwise, the script sets the preview image in the wrong place. */						
							var nodeImageInput = jQuery( this ).find( '.image-field input' );
							if ( nodeImageInput.length <= 0 ) return true;
							
							var fExternalSource = jQuery( nodeButton ).attr( 'data-enable_external_source' );
							setAPFImageUploader( nodeImageInput.attr( 'id' ), true, fExternalSource );	

						});
					},
					removed_repeatable_field: function( node, sFieldType, sFieldTagID, iCallType ) {
						
						/* If it is not the color field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;
											
						/* If the uploader buttons are not found, do nothing */
						if ( node.find( '.select_image' ).length <= 0 )  return;						
						
						/* Decrement the ids of the next all (including this one) uploader buttons and the preview elements. ( the input values are already dealt by the framework repeater script ) */
						var nodeFieldContainer = node.closest( '.admin-page-framework-field' );
						var iOccurence = iCallType === 1 ? 1 : 0;	// the occurrence value indicates which part of digit to change 
						nodeFieldContainer.nextAll().andSelf().each( function( iIndex ) {
							
							var nodeButton = jQuery( this ).find( '.select_image' );			
							
							// If it's for repeatable sections, updating the attributes is only necessary for the first iteration.
							if ( ! ( iCallType === 1 && iIndex !== 0 ) ) {							
								nodeButton.decrementIDAttribute( 'id', iOccurence );
								jQuery( this ).find( '.image_preview' ).decrementIDAttribute( 'id', iOccurence );
								jQuery( this ).find( '.image_preview img' ).decrementIDAttribute( 'id', iOccurence );
							}
							
							/* Rebind the uploader script to each button. The previously assigned ones also need to be renewed; 
							 * otherwise, the script sets the preview image in the wrong place. */						
							var nodeImageInput = jQuery( this ).find( '.image-field input' );
							if ( nodeImageInput.length <= 0 ) return true;
							
							var fExternalSource = jQuery( nodeButton ).attr( 'data-enable_external_source' );
							setAPFImageUploader( nodeImageInput.attr( 'id' ), true, fExternalSource );	
						
						});
						
					},
					sorted_fields : function( node, sFieldType, sFieldsTagID, iCallType ) {	// on contrary to repeatable callbacks, the _fields_ container node and its ID will be passed.

						/* 1. Return if it is not the type. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;	/* If it is not the color field type, do nothing. */						
						if ( node.find( '.select_image' ).length <= 0 )  return;	/* If the uploader buttons are not found, do nothing */
						
						/* 2. Update the Select File button */
						var iCount = 0;
						var iOccurence = iCallType === 1 ? 1 : 0;	// the occurrence value indicates which part of digit to change 
						node.children( '.admin-page-framework-field' ).each( function() {
							
							var nodeButton = jQuery( this ).find( '.select_image' );
							
							/* 2-1. Set the current iteration index to the button ID, and the image preview elements */
							nodeButton.setIndexIDAttribute( 'id', iCount, iOccurence );	
							jQuery( this ).find( '.image_preview' ).setIndexIDAttribute( 'id', iCount, iOccurence );
							jQuery( this ).find( '.image_preview img' ).setIndexIDAttribute( 'id', iCount, iOccurence );
							
							/* 2-2. Rebuind the uploader script to the button */
							var nodeImageInput = jQuery( this ).find( '.image-field input' );
							if ( nodeImageInput.length <= 0 ) return true;
							setAPFImageUploader( nodeImageInput.attr( 'id' ), true, jQuery( nodeButton ).attr( 'data-enable_external_source' ) );
	
							iCount++;
						});
					},					
				});
			});" . PHP_EOL; } private function _getScript_ImageSelector( $sReferrer, $sThickBoxTitle, $sThickBoxButtonUseThis ) { if ( ! function_exists( 'wp_enqueue_media' ) ) return "
					jQuery( document ).ready( function(){
						/**
						 * Bind/rebinds the thickbox script the given selector element.
						 * The fMultiple parameter does not do anything. It is there to be consistent with the one for the WordPress version 3.5 or above.
						 */
						setAPFImageUploader = function( sInputID, fMultiple, fExternalSource ) {
							jQuery( '#select_image_' + sInputID ).unbind( 'click' );	// for repeatable fields
							jQuery( '#select_image_' + sInputID ).click( function() {
								var sPressedID = jQuery( this ).attr( 'id' );			
								window.sInputID = sPressedID.substring( 13 );	// remove the select_image_ prefix and set a property to pass it to the editor callback method.
								window.original_send_to_editor = window.send_to_editor;
								window.send_to_editor = hfAPFSendToEditorImage;
								var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );
								tb_show( '{$sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$sThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
								return false;	// do not click the button after the script by returning false.									
							});	
						}			
						
						var hfAPFSendToEditorImage = function( sRawHTML ) {

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
							var sInputID = window.sInputID;	// window.sInputID should be assigned when the thickbox is opened.
				
							jQuery( '#' + sInputID ).val( src );	// sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
							jQuery( '#' + sInputID + '_id' ).val( id );
							jQuery( '#' + sInputID + '_width' ).val( width );
							jQuery( '#' + sInputID + '_height' ).val( height );
							jQuery( '#' + sInputID + '_caption' ).val( sCaption );
							jQuery( '#' + sInputID + '_alt' ).val( sAlt );
							jQuery( '#' + sInputID + '_title' ).val( title );						
							jQuery( '#' + sInputID + '_align' ).val( align );						
							jQuery( '#' + sInputID + '_link' ).val( link );						
							
							// Update the preview
							jQuery( '#image_preview_' + sInputID ).attr( 'alt', alt );
							jQuery( '#image_preview_' + sInputID ).attr( 'title', title );
							jQuery( '#image_preview_' + sInputID ).attr( 'data-classes', classes );
							jQuery( '#image_preview_' + sInputID ).attr( 'data-id', id );
							jQuery( '#image_preview_' + sInputID ).attr( 'src', src );	// updates the preview image
							jQuery( '#image_preview_container_' + sInputID ).css( 'display', '' );	// updates the visibility
							jQuery( '#image_preview_' + sInputID ).show()	// updates the visibility
							
							// restore the original send_to_editor
							window.send_to_editor = window.original_send_to_editor;

							// close the thickbox
							tb_remove();	

						}
					});
				"; return "jQuery( document ).ready( function(){

				// Global Function Literal 
				/**
				 * Binds/rebinds the uploader button script to the specified element with the given ID.
				 */
				setAPFImageUploader = function( sInputID, fMultiple, fExternalSource ) {

					jQuery( '#select_image_' + sInputID ).unbind( 'click' );	// for repeatable fields
					jQuery( '#select_image_' + sInputID ).click( function( e ) {
						
						// Reassign the input id from the pressed element ( do not use the passed parameter value to the caller function ) for repeatable sections.
						var sInputID = jQuery( this ).attr( 'id' ).substring( 13 );	// remove the select_image_ prefix and set a property to pass it to the editor callback method.
						
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
										var new_field = jQuery( this ).addAPFRepeatableField( field_container.attr( 'id' ) );
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
console.log( 'input id: ' + sInputID );
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
			"; } public function _replyToGetStyles() { return "/* Image Field Preview Container */
			.admin-page-framework-field .image_preview {
				border: none; 
				clear:both; 
				margin-top: 0.4em;
				margin-bottom: 0.8em;
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
			/* Image Uploader Input Field */
			.admin-page-framework-field-image input {
				margin-right: 0.5em;
				vertical-align: middle;	
			}
			/* Image Uploader Button */
			.select_image.button.button-small {
				margin-top: 0.1em;				
			}
		" . PHP_EOL; } public function _replyToGetField( $aField ) { $aOutput = array(); $iCountAttributes = count( ( array ) $aField['attributes_to_store'] ); $sCaptureAttribute = $iCountAttributes ? 'url' : ''; $sImageURL = $sCaptureAttribute ? ( isset( $aField['attributes']['value'][ $sCaptureAttribute ] ) ? $aField['attributes']['value'][ $sCaptureAttribute ] : "" ) : $aField['attributes']['value']; $aBaseAttributes = $aField['attributes']; unset( $aBaseAttributes['input'], $aBaseAttributes['button'], $aBaseAttributes['preview'], $aBaseAttributes['name'], $aBaseAttributes['value'], $aBaseAttributes['type'] ); $aInputAttributes = array( 'name' => $aField['attributes']['name'] . ( $iCountAttributes ? "[url]" : "" ), 'value' => $sImageURL, 'type' => 'text', ) + $aField['attributes']['input'] + $aBaseAttributes; $aButtonAtributes = $aField['attributes']['button'] + $aBaseAttributes; $aPreviewAtrributes = $aField['attributes']['preview'] + $aBaseAttributes; $aOutput[] = $aField['before_label'] . "<div class='admin-page-framework-input-label-container admin-page-framework-input-container {$aField['type']}-field'>" . "<label for='{$aField['input_id']}'>" . $aField['before_input'] . ( $aField['label'] && ! $aField['repeatable'] ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>" : "" ) . "<input " . $this->generateAttributes( $aInputAttributes ) . " />" . $aField['after_input'] . "<div class='repeatable-field-buttons'></div>" . $this->getExtraInputFields( $aField ) . "</label>" . "</div>" . $aField['after_label'] . $this->_getPreviewContainer( $aField, $sImageURL, $aPreviewAtrributes ) . $this->_getUploaderButtonScript( $aField['input_id'], $aField['repeatable'], $aField['allow_external_source'], $aButtonAtributes ); ; return implode( PHP_EOL, $aOutput ); } protected function getExtraInputFields( &$aField ) { $aOutputs = array(); foreach( ( array ) $aField['attributes_to_store'] as $sAttribute ) $aOutputs[] = "<input " . $this->generateAttributes( array( 'id' => "{$aField['input_id']}_{$sAttribute}", 'type' => 'hidden', 'name' => "{$aField['_input_name']}[{$sAttribute}]", 'disabled' => isset( $aField['attributes']['diabled'] ) && $aField['attributes']['diabled'] ? 'Disabled' : '', 'value' => isset( $aField['attributes']['value'][ $sAttribute ] ) ? $aField['attributes']['value'][ $sAttribute ] : '', ) ) . "/>"; return implode( PHP_EOL, $aOutputs ); } protected function _getPreviewContainer( $aField, $sImageURL, $aPreviewAtrributes ) { if ( ! $aField['show_preview'] ) return ''; $sImageURL = $this->resolveSRC( $sImageURL, true ); return "<div " . $this->generateAttributes( array( 'id' => "image_preview_container_{$aField['input_id']}", 'class' => 'image_preview ' . ( isset( $aPreviewAtrributes['class'] ) ? $aPreviewAtrributes['class'] : '' ), 'style' => ( $sImageURL ? '' : "display: none; " ). ( isset( $aPreviewAtrributes['style'] ) ? $aPreviewAtrributes['style'] : '' ), ) + $aPreviewAtrributes ) . ">" . "<img src='{$sImageURL}' " . "id='image_preview_{$aField['input_id']}' " . "/>" . "</div>"; } protected function _getUploaderButtonScript( $sInputID, $bRpeatable, $bExternalSource, array $aButtonAttributes ) { $sButton = "<a " . $this->generateAttributes( array( 'id' => "select_image_{$sInputID}", 'href' => '#', 'class' => 'select_image button button-small ' . ( isset( $aButtonAttributes['class'] ) ? $aButtonAttributes['class'] : '' ), 'data-uploader_type' => function_exists( 'wp_enqueue_media' ) ? 1 : 0, 'data-enable_external_source' => $bExternalSource ? 1 : 0, ) + $aButtonAttributes ) . ">" . $this->oMsg->__( 'select_image' ) ."</a>"; $sScript = "
				if ( jQuery( 'a#select_image_{$sInputID}' ).length == 0 ) {
					jQuery( 'input#{$sInputID}' ).after( \"{$sButton}\" );
				}
				jQuery( document ).ready( function(){			
					setAPFImageUploader( '{$sInputID}', '{$bRpeatable}', '{$bExternalSource}' );
				});" . PHP_EOL; return "<script type='text/javascript' class='admin-page-framework-image-uploader-button'>" . $sScript . "</script>". PHP_EOL; } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_radio' ) ) : class AdminPageFramework_FieldType_radio extends AdminPageFramework_FieldType_Base { public $aFieldTypeSlugs = array( 'radio' ); protected $aDefaultKeys = array( 'label' => array(), 'attributes' => array( ), ); public function _replyToFieldLoader() { } public function _replyToGetStyles() { return "/* Radio Field Type */
			.admin-page-framework-field input[type='radio'] {
				margin-right: 0.5em;
			}		
			.admin-page-framework-field-radio .admin-page-framework-input-label-container {
				padding-right: 1em;
			}			
			.admin-page-framework-field-radio .admin-page-framework-input-container {
				display: inline;
			}			
		"; } public function _replyToGetScripts() { $aJSArray = json_encode( $this->aFieldTypeSlugs ); return "			
			jQuery( document ).ready( function(){
				jQuery().registerAPFCallback( {				
					added_repeatable_field: function( nodeField, sFieldType, sFieldTagID ) {
			
						/* If it is not the color field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;
													
						/* the checked state of radio buttons somehow lose their values so re-check them again */	
						nodeField.closest( '.admin-page-framework-fields' )
							.find( 'input[type=radio][checked=checked]' )
							.attr( 'checked', 'checked' );
							
						/* Rebind the checked attribute updater */
						nodeField.find( 'input[type=radio]' ).change( function() {
							jQuery( this ).closest( '.admin-page-framework-field' )
								.find( 'input[type=radio]' )
								.attr( 'checked', false );
							jQuery( this ).attr( 'checked', 'Checked' );
						});

					}
				});
			});
		"; } public function _replyToGetField( $aField ) { $aOutput = array(); $sValue = $aField['attributes']['value']; foreach( $aField['label'] as $sKey =>$sLabel ) { $aInputAttributes = array( 'type' => 'radio', 'checked' => $sValue == $sKey ? 'checked' : '', 'value' => $sKey, 'id' => $aField['input_id'] . '_' . $sKey, 'data-default' => $aField['default'], ) + $this->getFieldElementByKey( $aField['attributes'], $sKey, $aField['attributes'] ) + $aField['attributes']; $aLabelAttributes = array( 'for' => $aInputAttributes['id'], 'class' => $aInputAttributes['disabled'] ? 'disabled' : '', ); $aOutput[] = $this->getFieldElementByKey( $aField['before_label'], $sKey ) . "<div class='admin-page-framework-input-label-container admin-page-framework-radio-label' style='min-width: {$aField['label_min_width']}px;'>" . "<label " . $this->generateAttributes( $aLabelAttributes ) . ">" . $this->getFieldElementByKey( $aField['before_input'], $sKey ) . "<span class='admin-page-framework-input-container'>" . "<input " . $this->generateAttributes( $aInputAttributes ) . " />" . "</span>" . "<span class='admin-page-framework-input-label-string'>" . $sLabel . "</span>" . $this->getFieldElementByKey( $aField['after_input'], $sKey ) . "</label>" . "</div>" . $this->getFieldElementByKey( $aField['after_label'], $sKey ) ; } $aOutput[] = $this->_getUpdateCheckedScript( $aField['_field_container_id'] ); return implode( PHP_EOL, $aOutput ); } private function _getUpdateCheckedScript( $sFieldContainerID ) { return "<script type='text/javascript' class='radio-button-checked-attribute-updater'>
					jQuery( document ).ready( function(){
						jQuery( '#{$sFieldContainerID} input[type=radio]' ).change( function() {
							jQuery( this ).closest( '.admin-page-framework-field' ).find( 'input[type=radio]' ).attr( 'checked', false );
							jQuery( this ).attr( 'checked', 'Checked' );
						});
					});				
				</script>"; } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_section_title' ) ) : class AdminPageFramework_FieldType_section_title extends AdminPageFramework_FieldType_Base { public $aFieldTypeSlugs = array( 'section_title', ); protected $aDefaultKeys = array( 'label_min_width' => 30, 'attributes' => array( 'size' => 20, 'maxlength' => 100, ), ); public function _replyToGetStyles() { return "/* Section Tab Field Type */
			.admin-page-framework-section-tab .admin-page-framework-field-section_title {
				padding: 0.5em;
			}
 			.admin-page-framework-section-tab .admin-page-framework-field-section_title .admin-page-framework-input-label-string {			
				vertical-align: text-top; 
			} 
 			.admin-page-framework-section-tab .admin-page-framework-fields {
				display: inline-block;
			} 
			.admin-page-framework-field.admin-page-framework-field-section_title {
				float: none;
			} 
			.admin-page-framework-field.admin-page-framework-field-section_title input {
				background-color: #fff;
				color: #333;
				border-color: #ddd;
				box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
				border-width: 1px;
				border-style: solid;
				outline: 0;
				box-sizing: border-box;
			}
			" . PHP_EOL; } public function _replyToGetField( $aField ) { return $aField['before_label'] . "<div class='admin-page-framework-input-label-container'>" . "<label for='{$aField['input_id']}'>" . $aField['before_input'] . ( $aField['label'] && ! $aField['repeatable'] ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>" : "" ) . "<input " . $this->generateAttributes( $aField['attributes'] ) . " />" . $aField['after_input'] . "<div class='repeatable-field-buttons'></div>" . "</label>" . "</div>" . $aField['after_label']; } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_select' ) ) : class AdminPageFramework_FieldType_select extends AdminPageFramework_FieldType_Base { public $aFieldTypeSlugs = array( 'select' ); protected $aDefaultKeys = array( 'label' => array(), 'is_multiple' => '', 'attributes' => array( 'select' => array( 'size' => 1, 'autofocusNew' => '', 'multiple' => '', 'required' => '', ), 'optgroup' => array(), 'option' => array(), ), ); public function _replyToFieldLoader() { } public function _replyToGetScripts() { return ""; } public function _replyToGetStyles() { return "/* Select Field Type */
			.admin-page-framework-field-select .admin-page-framework-input-label-container {
				vertical-align: top; 
			}
			.admin-page-framework-field-select .admin-page-framework-input-label-container {
				padding-right: 1em;
			}
		"; } public function _replyToGetField( $aField ) { $aSelectAttributes = array( 'id' => $aField['input_id'], 'multiple' => $aField['is_multiple'] ? 'multiple' : $aField['attributes']['select']['multiple'], ) + $aField['attributes']['select']; $aSelectAttributes['name'] = empty( $aSelectAttributes['multiple'] ) ? $aField['_input_name'] : "{$aField['_input_name']}[]"; return $aField['before_label'] . "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: {$aField['label_min_width']}px;'>" . "<label for='{$aField['input_id']}'>" . $aField['before_input'] . "<span class='admin-page-framework-input-container'>" . "<select " . $this->generateAttributes( $aSelectAttributes ) . " >" . $this->_getOptionTags( $aField['input_id'], $aField['attributes'], $aField['label'] ) . "</select>" . "</span>" . $aField['after_input'] . "<div class='repeatable-field-buttons'></div>" . "</label>" . "</div>" . $aField['after_label']; } protected function _getOptionTags( $sInputID, &$aAttributes, $aLabel ) { $aOutput = array(); $aValue = ( array ) $aAttributes['value']; foreach( $aLabel as $sKey => $asLabel ) { if ( is_array( $asLabel ) ) { $aOptGroupAttributes = isset( $aAttributes['optgroup'][ $sKey ] ) && is_array( $aAttributes['optgroup'][ $sKey ] ) ? $aAttributes['optgroup'][ $sKey ] + $aAttributes['optgroup'] : $aAttributes['optgroup']; $aOutput[] = "<optgroup label='{$sKey}'" . $this->generateAttributes( $aOptGroupAttributes ) . ">" . $this->_getOptionTags( $sInputID, $aAttributes, $asLabel ) . "</optgroup>"; continue; } $aValue = isset( $aAttributes['option'][ $sKey ]['value'] ) ? $aAttributes['option'][ $sKey ]['value'] : $aValue; $aOptionAttributes = array( 'id' => $sInputID . '_' . $sKey, 'value' => $sKey, 'selected' => in_array( ( string ) $sKey, $aValue ) ? 'Selected' : '', ) + ( isset( $aAttributes['option'][ $sKey ] ) && is_array( $aAttributes['option'][ $sKey ] ) ? $aAttributes['option'][ $sKey ] + $aAttributes['option'] : $aAttributes['option'] ); $aOutput[] = "<option " . $this->generateAttributes( $aOptionAttributes ) . " >" . $asLabel . "</option>"; } return implode( PHP_EOL, $aOutput ); } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_submit' ) ) : class AdminPageFramework_FieldType_submit extends AdminPageFramework_FieldType_Base { public $aFieldTypeSlugs = array( 'submit', ); protected $aDefaultKeys = array( 'redirect_url' => null, 'href' => null, 'reset' => null, 'attributes' => array( 'class' => 'button button-primary', ), ); public function _replyToFieldLoader() { } public function _replyToGetScripts() { return ""; } public function _replyToGetStyles() { return "/* Submit Buttons */
		.admin-page-framework-field input[type='submit'] {
			margin-bottom: 0.5em;
		}" . PHP_EOL; } public function _replyToGetField( $aField ) { $aField['label'] = $aField['label'] ? $aField['label'] : $this->oMsg->__( 'submit' ); $aInputAttributes = array( 'type' => 'submit', 'value' => ( $sValue = $this->_getInputFieldValueFromLabel( $aField ) ), ) + $aField['attributes'] + array( 'title' => $sValue, ); $aLabelAttributes = array( 'style' => $aField['label_min_width'] ? "min-width:{$aField['label_min_width']}px;" : null, 'for' => $aInputAttributes['id'], 'class' => $aInputAttributes['disabled'] ? 'disabled' : '', ); $aLabelContainerAttributes = array( 'style' => $aField['label_min_width'] ? "min-width:{$aField['label_min_width']}px;" : null, 'class' => 'admin-page-framework-input-label-container admin-page-framework-input-button-container admin-page-framework-input-container', ); return $aField['before_label'] . "<div " . $this->generateAttributes( $aLabelContainerAttributes ) . ">" . $this->_getExtraFieldsBeforeLabel( $aField ) . "<label " . $this->generateAttributes( $aLabelAttributes ) . ">" . $aField['before_input'] . $this->_getExtraInputFields( $aField ) . "<input " . $this->generateAttributes( $aInputAttributes ) . " />" . $aField['after_input'] . "</label>" . "</div>" . $aField['after_label']; } protected function _getExtraFieldsBeforeLabel( &$aField ) { return ''; } protected function _getExtraInputFields( &$aField ) { return "<input type='hidden' " . "name='__submit[{$aField['input_id']}][input_id]' " . "value='{$aField['input_id']}'" . "/>" . "<input type='hidden' " . "name='__submit[{$aField['input_id']}][field_id]' " . "value='{$aField['field_id']}'" . "/>" . "<input type='hidden' " . "name='__submit[{$aField['input_id']}][name]' " . "value='{$aField['_input_name_flat']}'" . "/>" . "<input type='hidden' " . "name='__submit[{$aField['input_id']}][section_id]' " . "value='" . ( isset( $aField['section_id'] ) && $aField['section_id'] != '_default' ? $aField['section_id'] : '' ) . "'" . "/>" . ( $aField['redirect_url'] ? "<input type='hidden' " . "name='__submit[{$aField['input_id']}][redirect_url]' " . "value='{$aField['redirect_url']}'" . "/>" : "" ) . ( $aField['href'] ? "<input type='hidden' " . "name='__submit[{$aField['input_id']}][link_url]' " . "value='{$aField['href']}'" . "/>" : "" ) . ( $aField['reset'] && ( ! ( $bResetConfirmed = $this->_checkConfirmationDisplayed( $aField['reset'], $aField['_input_name_flat'] ) ) ) ? "<input type='hidden' " . "name='__submit[{$aField['input_id']}][is_reset]' " . "value='1'" . "/>" : "" ) . ( $aField['reset'] && $bResetConfirmed ? "<input type='hidden' " . "name='__submit[{$aField['input_id']}][reset_key]' " . "value='{$aField['reset']}'" . "/>" : "" ); } private function _checkConfirmationDisplayed( $sResetKey, $sFlatFieldName ) { if ( ! $sResetKey ) return false; $bResetConfirmed = get_transient( md5( "reset_confirm_" . $sFlatFieldName ) ) !== false ? true : false; if ( $bResetConfirmed ) delete_transient( md5( "reset_confirm_" . $sFlatFieldName ) ); return $bResetConfirmed; } protected function _getInputFieldValueFromLabel( $aField ) { if ( isset( $aField['value'] ) && $aField['value'] != '' ) return $aField['value']; if ( isset( $aField['label'] ) ) return $aField['label']; if ( isset( $aField['default'] ) ) return $aField['default']; } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_export' ) ) : class AdminPageFramework_FieldType_export extends AdminPageFramework_FieldType_submit { public $aFieldTypeSlugs = array( 'export', ); protected $aDefaultKeys = array( 'data' => null, 'format' => 'json', 'file_name' => null, 'attributes' => array( 'class' => 'button button-primary', ), ); public function _replyToFieldLoader() { } public function _replyToGetScripts() { return ""; } public function _replyToGetStyles() { return ""; } public function _replyToGetField( $aField ) { if ( isset( $aField['data'] ) ) set_transient( md5( "{$aField['class_name']}_{$aField['input_id']}" ), $aField['data'], 60*2 ); $aField['attributes']['name'] = "__export[submit][{$aField['input_id']}]"; $aField['file_name'] = $aField['file_name'] ? $aField['file_name'] : $this->_generateExportFileName( $aField['option_key'] ? $aField['option_key'] : $aField['class_name'], $aField['format'] ); $aField['label'] = $aField['label'] ? $aField['label'] : $this->oMsg->__( 'export' ); return parent::_replyToGetField( $aField ); } protected function _getExtraInputFields( &$aField ) { $_aAttributes = array( 'type' => 'hidden' ); return "<input " . $this->generateAttributes( array( 'name' => "__export[{$aField['input_id']}][input_id]", 'value' => $aField['input_id'], ) + $_aAttributes ) . "/>" . "<input " . $this->generateAttributes( array( 'name' => "__export[{$aField['input_id']}][field_id]", 'value' => $aField['field_id'], ) + $_aAttributes ) . "/>" . "<input " . $this->generateAttributes( array( 'name' => "__export[{$aField['input_id']}][section_id]", 'value' => isset( $aField['section_id'] ) && $aField['section_id'] != '_default' ? $aField['section_id'] : '', ) + $_aAttributes ) . "/>" . "<input " . $this->generateAttributes( array( 'name' => "__export[{$aField['input_id']}][file_name]", 'value' => $aField['file_name'], ) + $_aAttributes ) . "/>" . "<input " . $this->generateAttributes( array( 'name' => "__export[{$aField['input_id']}][format]", 'value' => $aField['format'], ) + $_aAttributes ) . "/>" . "<input " . $this->generateAttributes( array( 'name' => "__export[{$aField['input_id']}][transient]", 'value' => isset( $aField['data'] ), ) + $_aAttributes ) . "/>" ; } private function _generateExportFileName( $sOptionKey, $sExportFormat='json' ) { switch ( trim( strtolower( $sExportFormat ) ) ) { case 'text': $sExt = "txt"; break; case 'json': $sExt = "json"; break; case 'array': default: $sExt = "txt"; break; } return $sOptionKey . '_' . date("Ymd") . '.' . $sExt; } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_import' ) ) : class AdminPageFramework_FieldType_import extends AdminPageFramework_FieldType_submit { public $aFieldTypeSlugs = array( 'import', ); protected $aDefaultKeys = array( 'option_key' => null, 'format' => 'json', 'is_merge' => false, 'attributes' => array( 'class' => 'button button-primary', 'file' => array( 'accept' => 'audio/*|video/*|image/*|MIME_type', 'class' => 'import', 'type' => 'file', ), 'submit' => array( 'class' => 'import button button-primary', 'type' => 'submit', ), ), ); public function _replyToFieldLoader() { } public function _replyToGetScripts() { return ""; } public function _replyToGetStyles() { return "/* Import Field */
		.admin-page-framework-field-import input {
			margin-right: 0.5em;
		}
		.admin-page-framework-field-import label,
		.form-table td fieldset.admin-page-framework-fieldset .admin-page-framework-field-import label {	/* for Wordpress 3.8 or above */
			display: inline;	/* to display the submit button in the same line to the file input tag */
		}" . PHP_EOL; } public function _replyToGetField( $aField ) { $aField['attributes']['name'] = "__import[submit][{$aField['input_id']}]"; $aField['label'] = $aField['label'] ? $aField['label'] : $this->oMsg->__( 'import' ); return parent::_replyToGetField( $aField ); } protected function _getExtraFieldsBeforeLabel( &$aField ) { return "<input " . $this->generateAttributes( array( 'id' => "{$aField['input_id']}_file", 'type' => 'file', 'name' => "__import[{$aField['input_id']}]", ) + $aField['attributes']['file'] ) . " />"; } protected function _getExtraInputFields( &$aField ) { $aHiddenAttributes = array( 'type' => 'hidden', ); return "<input " . $this->generateAttributes( array( 'name' => "__import[{$aField['input_id']}][input_id]", 'value' => $aField['input_id'], ) + $aHiddenAttributes ) . "/>" . "<input " . $this->generateAttributes( array( 'name' => "__import[{$aField['input_id']}][field_id]", 'value' => $aField['field_id'], ) + $aHiddenAttributes ) . "/>" . "<input " . $this->generateAttributes( array( 'name' => "__import[{$aField['input_id']}][section_id]", 'value' => isset( $aField['section_id'] ) && $aField['section_id'] != '_default' ? $aField['section_id'] : '', ) + $aHiddenAttributes ) . "/>" . "<input " . $this->generateAttributes( array( 'name' => "__import[{$aField['input_id']}][is_merge]", 'value' => $aField['is_merge'], ) + $aHiddenAttributes ) . "/>" . "<input " . $this->generateAttributes( array( 'name' => "__import[{$aField['input_id']}][option_key]", 'value' => $aField['option_key'], ) + $aHiddenAttributes ) . "/>" . "<input " . $this->generateAttributes( array( 'name' => "__import[{$aField['input_id']}][format]", 'value' => $aField['format'], ) + $aHiddenAttributes ) . "/>" ; } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_taxonomy' ) ) : class AdminPageFramework_FieldType_taxonomy extends AdminPageFramework_FieldType_Base { public $aFieldTypeSlugs = array( 'taxonomy', ); protected $aDefaultKeys = array( 'taxonomy_slugs' => 'category', 'height' => '250px', 'max_width' => '100$', 'attributes' => array( ), ); public function _replyToFieldLoader() { } public function _replyToGetScripts() { $aJSArray = json_encode( $this->aFieldTypeSlugs ); return "	
			jQuery( document ).ready( function() {
				/* For tabs */
				var enableAPFTabbedBox = function( nodeTabBoxContainer ) {
					jQuery( nodeTabBoxContainer ).each( function() {
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
				}		
				enableAPFTabbedBox( jQuery( '.tab-box-container' ) );

				/*	The repeatable event */
				jQuery().registerAPFCallback( {				
					added_repeatable_field: function( node, sFieldType, sFieldTagID ) {
			
						/* If it is not the color field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;

						node.nextAll().andSelf().each( function() {							
							jQuery( this ).find( 'div' ).incrementIDAttribute( 'id' );
							jQuery( this ).find( 'li.tab-box-tab a' ).incrementIDAttribute( 'href' );
							jQuery( this ).find( 'li.category-list' ).incrementIDAttribute( 'id' );
							jQuery( this ).find( 'input' ).decrementNameAttribute( 'name' );	// the framework increments the last found digit by default so revert it
							jQuery( this ).find( 'input' ).incrementNameAttribute( 'name', -1 );	// now increment the second found digit from the end 
							enableAPFTabbedBox( jQuery( this ).find( '.tab-box-container' ) );
						});						
						
					},
					removed_repeatable_field: function( node, sFieldType, sFieldTagID ) {
			
						/* If it is not the color field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;
	
						node.nextAll().each( function() {
							jQuery( this ).find( 'div' ).decrementIDAttribute( 'id' );
							jQuery( this ).find( 'li.tab-box-tab a' ).decrementIDAttribute( 'href' );
							jQuery( this ).find( 'li.category-list' ).decrementIDAttribute( 'id' );
							jQuery( this ).find( 'input' ).incrementNameAttribute( 'name' );	// the framework decrements the last found digit by default so revert it
							jQuery( this ).find( 'input' ).decrementNameAttribute( 'name', -1 );	// now decrement the second found digit from the end 
						});	
						
						// enableAPFTabbedBox( node.find( '.tab-box-container' ) );
						
					},					
				});
			});			
		"; } public function _replyToGetStyles() { return "/* Taxonomy Field Type */
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
				white-space: nowrap;			
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
			/* .admin-page-framework-field .tab-box-tab {		
vertical-align: top;
			} */
			.admin-page-framework-field .tab-box-tabs .tab-box-tab.active {
				display: inline;
				border-color: #dfdfdf #dfdfdf #fff;
				margin-bottom: 0px;
				padding-bottom: 2px;
				background-color: #fff;
				
			}
			.admin-page-framework-field .tab-box-container { 
				position: relative; 
				width: 100%; 
				clear: both;
				margin-bottom: 1em;
			}
			.admin-page-framework-field .tab-box-tabs li a { color: #333; text-decoration: none; }
			.admin-page-framework-field .tab-box-contents-container {  
				padding: 0 0 0 1.8em;
				padding: 0.55em 0.5em 0.55em 1.8em;
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

				/* height: 300px; */
				display: none; 
				overflow: auto; 
				display: block; 
				position: relative; 
				overflow-x: hidden;
			}
			.admin-page-framework-field .tab-box-content .taxonomychecklist {
				margin-right: 3.2em;
			}
			.admin-page-framework-field .tab-box-content:target, 
			.admin-page-framework-field .tab-box-content:target, 
			.admin-page-framework-field .tab-box-content:target { 
				display: block; 
			}			
		" . PHP_EOL; } public function _replyToGetInputIEStyles() { return ".tab-box-content { display: block; }
			.tab-box-contents { overflow: hidden;position: relative; }
			b { position: absolute; top: 0px; right: 0px; width:1px; height: 251px; overflow: hidden; text-indent: -9999px; }
		"; } public function _replyToGetField( $aField ) { $aTabs = array(); $aCheckboxes = array(); foreach( ( array ) $aField['taxonomy_slugs'] as $sKey => $sTaxonomySlug ) { $aInputAttributes = isset( $aField['attributes'][ $sKey ] ) && is_array( $aField['attributes'][ $sKey ] ) ? $aField['attributes'][ $sKey ] + $aField['attributes'] : $aField['attributes']; $aTabs[] = "<li class='tab-box-tab'>" . "<a href='#tab_{$aField['input_id']}_{$sKey}'>" . "<span class='tab-box-tab-text'>" . $this->_getLabelFromTaxonomySlug( $sTaxonomySlug ) . "</span>" ."</a>" ."</li>"; $aCheckboxes[] = "<div id='tab_{$aField['input_id']}_{$sKey}' class='tab-box-content' style='height: {$aField['height']};'>" . $this->getFieldElementByKey( $aField['before_label'], $sKey ) . "<ul class='list:category taxonomychecklist form-no-clear'>" . wp_list_categories( array( 'walker' => new AdminPageFramework_WalkerTaxonomyChecklist, 'name' => is_array( $aField['taxonomy_slugs'] ) ? "{$aField['_input_name']}[{$sTaxonomySlug}]" : $aField['_input_name'], 'selected' => $this->_getSelectedKeyArray( $aField['value'], $sTaxonomySlug ), 'title_li' => '', 'hide_empty' => 0, 'echo' => false, 'taxonomy' => $sTaxonomySlug, 'input_id' => $aField['input_id'], 'attributes' => $aInputAttributes, ) ) . "</ul>" . "<!--[if IE]><b>.</b><![endif]-->" . $this->getFieldElementByKey( $aField['after_label'], $sKey ) . "</div>"; } $sTabs = "<ul class='tab-box-tabs category-tabs'>" . implode( PHP_EOL, $aTabs ) . "</ul>"; $sContents = "<div class='tab-box-contents-container'>" . "<div class='tab-box-contents' style='height: {$aField['height']};'>" . implode( PHP_EOL, $aCheckboxes ) . "</div>" . "</div>"; return '' . "<div id='tabbox-{$aField['field_id']}' class='tab-box-container categorydiv' style='max-width:{$aField['max_width']};'>" . $sTabs . PHP_EOL . $sContents . PHP_EOL . "</div>" ; } private function _getSelectedKeyArray( $vValue, $sTaxonomySlug ) { $vValue = ( array ) $vValue; if ( ! isset( $vValue[ $sTaxonomySlug ] ) ) return array(); if ( ! is_array( $vValue[ $sTaxonomySlug ] ) ) return array(); return array_keys( $vValue[ $sTaxonomySlug ], true ); } private function _getLabelFromTaxonomySlug( $sTaxonomySlug ) { $oTaxonomy = get_taxonomy( $sTaxonomySlug ); return isset( $oTaxonomy->label ) ? $oTaxonomy->label : null; } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_text' ) ) : class AdminPageFramework_FieldType_text extends AdminPageFramework_FieldType_Base { public $aFieldTypeSlugs = array( 'text', 'password', 'date', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'url', 'week', ); protected $aDefaultKeys = array( 'attributes' => array( 'size' => 30, 'maxlength' => 400, ), ); public function _replyToGetStyles() { return "/* Text Field Type */
				.admin-page-framework-field-text .admin-page-framework-field .admin-page-framework-input-label-container {
					vertical-align: top; 
				}
			" . PHP_EOL; } public function _replyToGetField( $aField ) { return $aField['before_label'] . "<div class='admin-page-framework-input-label-container'>" . "<label for='{$aField['input_id']}'>" . $aField['before_input'] . ( $aField['label'] && ! $aField['repeatable'] ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>" : "" ) . "<input " . $this->generateAttributes( $aField['attributes'] ) . " />" . $aField['after_input'] . "<div class='repeatable-field-buttons'></div>" . "</label>" . "</div>" . $aField['after_label']; } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_file' ) ) : class AdminPageFramework_FieldType_file extends AdminPageFramework_FieldType_text { public $aFieldTypeSlugs = array( 'file', ); protected $aDefaultKeys = array( 'attributes' => array( 'accept' => 'audio/*|video/*|image/*|MIME_type', ), ); public function _replyToFieldLoader() { } public function _replyToGetScripts() { return ""; } public function _replyToGetStyles() { return ""; } public function _replyToGetField( $aField ) { return parent::_replyToGetField( $aField ); } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_number' ) ) : class AdminPageFramework_FieldType_number extends AdminPageFramework_FieldType_text { public $aFieldTypeSlugs = array( 'number', 'range' ); protected $aDefaultKeys = array( 'attributes' => array( 'size' => 30, 'maxlength' => 400, 'class' => '', 'min' => '', 'max' => '', 'step' => '', 'readonly' => '', 'required' => '', 'placeholder' => '', 'list' => '', 'autofocus' => '', 'autocomplete' => '', ), ); public function _replyToFieldLoader() { } public function _replyToGetScripts() { return ""; } public function _replyToGetStyles() { return ""; } public function _replyToGetField( $aField ) { return parent::_replyToGetField( $aField ); } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_textarea' ) ) : class AdminPageFramework_FieldType_textarea extends AdminPageFramework_FieldType_Base { public $aFieldTypeSlugs = array( 'textarea' ); protected $aDefaultKeys = array( 'rich' => false, 'attributes' => array( 'autofocus' => '', 'cols' => 60, 'disabled' => '', 'formNew' => '', 'maxlength' => '', 'placeholder' => '', 'readonly' => '', 'required' => '', 'rows' => 4, 'wrap' => '', ), ); public function _replyToGetStyles() { return "/* Textarea Field Type */
			.admin-page-framework-field-textarea .admin-page-framework-input-label-string {
				vertical-align: top;
				margin-top: 2px;
			}		
			/* Rich Text Editor */
			.admin-page-framework-field-textarea .wp-core-ui.wp-editor-wrap {
				margin-bottom: 0.5em;
			}
			.admin-page-framework-field-textarea.admin-page-framework-field .admin-page-framework-input-label-container {
				vertical-align: top; 
			} 
			
		" . PHP_EOL; } public function _replyToGetField( $aField ) { return "<div class='admin-page-framework-input-label-container'>" . "<label for='{$aField['input_id']}'>" . $aField['before_input'] . ( $aField['label'] && ! $aField['repeatable'] ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>" : "" ) . ( ! empty( $aField['rich'] ) && version_compare( $GLOBALS['wp_version'], '3.3', '>=' ) && function_exists( 'wp_editor' ) ? wp_editor( $aField['value'], $aField['attributes']['id'], $this->uniteArrays( ( array ) $aField['rich'], array( 'wpautop' => true, 'media_buttons' => true, 'textarea_name' => $aField['attributes']['name'], 'textarea_rows' => $aField['attributes']['rows'], 'tabindex' => '', 'tabfocus_elements' => ':prev,:next', 'editor_css' => '', 'editor_class' => $aField['attributes']['class'], 'teeny' => false, 'dfw' => false, 'tinymce' => true, 'quicktags' => true ) ) ) . $this->_getScriptForRichEditor( $aField['attributes']['id'] ) : "<textarea " . $this->generateAttributes( $aField['attributes'] ) . " >" . $aField['value'] . "</textarea>" ) . "<div class='repeatable-field-buttons'></div>" . $aField['after_input'] . "</label>" . "</div>" ; } private function _getScriptForRichEditor( $sIDSelector ) { return "<script type='text/javascript'>
				jQuery( '#wp-{$sIDSelector}-wrap' ).hide();
				jQuery( document ).ready( function() {
					jQuery( '#wp-{$sIDSelector}-wrap' ).appendTo( '#field-{$sIDSelector}' );
					jQuery( '#wp-{$sIDSelector}-wrap' ).show();
				})
			</script>"; } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_posttype' ) ) : class AdminPageFramework_FieldType_posttype extends AdminPageFramework_FieldType_checkbox { public $aFieldTypeSlugs = array( 'posttype', ); protected $aDefaultKeys = array( 'slugs_to_remove' => null, 'attributes' => array( 'size' => 30, 'maxlength' => 400, ), ); protected $aDefaultRemovingPostTypeSlugs = array( 'revision', 'attachment', 'nav_menu_item', ); public function _replyToFieldLoader() { } public function _replyToGetScripts() { return ""; } public function _replyToGetStyles() { return "/* Posttype Field Type */
			.admin-page-framework-field input[type='checkbox'] {
				margin-right: 0.5em;
			}			
			.admin-page-framework-field-posttype .admin-page-framework-input-label-container {
				padding-right: 1em;
			}	
		"; } public function _replyToGetField( $aField ) { $aField['label'] = $this->_getPostTypeArrayForChecklist( isset( $aField['slugs_to_remove'] ) ? $aField['slugs_to_remove'] : $this->aDefaultRemovingPostTypeSlugs ); return parent::_replyToGetField( $aField ); } private function _getPostTypeArrayForChecklist( $aRemoveNames, $aPostTypes=array() ) { foreach( get_post_types( '','objects' ) as $oPostType ) if ( isset( $oPostType->name, $oPostType->label ) ) $aPostTypes[ $oPostType->name ] = $oPostType->label; return array_diff_key( $aPostTypes, array_flip( $aRemoveNames ) ); } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_media' ) ) : class AdminPageFramework_FieldType_media extends AdminPageFramework_FieldType_image { public $aFieldTypeSlugs = array( 'media', ); protected $aDefaultKeys = array( 'attributes_to_store' => array(), 'show_preview' => true, 'allow_external_source' => true, 'attributes' => array( 'input' => array( 'size' => 40, 'maxlength' => 400, ), 'button' => array( ), 'preview' => array( ), ), ); public function _replyToFieldLoader() { parent::_replyToFieldLoader(); } public function _replyToGetScripts() { return $this->_getScript_CustomMediaUploaderObject() . PHP_EOL . $this->_getScript_MediaUploader( "admin_page_framework", $this->oMsg->__( 'upload_file' ), $this->oMsg->__( 'use_this_file' ) ) . PHP_EOL . $this->_getScript_RegisterCallbacks(); } protected function _getScript_RegisterCallbacks() { $aJSArray = json_encode( $this->aFieldTypeSlugs ); return"
			jQuery( document ).ready( function(){
						
				jQuery().registerAPFCallback( {	
				
					added_repeatable_field: function( node, sFieldType, sFieldTagID, iCallType ) {
						
						/* 1. Return if it is not the type. */						
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;	/* If it is not the media field type, do nothing. */
						if ( node.find( '.select_media' ).length <= 0 )  return;	/* If the uploader buttons are not found, do nothing */
						
						/* 2. Increment the ids of the next all (including this one) uploader buttons  */
						var nodeFieldContainer = node.closest( '.admin-page-framework-field' );
						var iOccurence = iCallType === 1 ? 1 : 0;
						nodeFieldContainer.nextAll().andSelf().each( function( iIndex ) {

							/* 2-1. Increment the button ID */
							nodeButton = jQuery( this ).find( '.select_media' );
							
							// If it's for repeatable sections, updating the attributes is only necessary for the first iteration.
							if ( ! ( iCallType === 1 && iIndex !== 0 ) ) {
								nodeButton.incrementIDAttribute( 'id', iOccurence );
							}
							
							/* 2-2. Rebind the uploader script to each button. The previously assigned ones also need to be renewed; 
							 * otherwise, the script sets the preview image in the wrong place. */						
							var nodeMediaInput = jQuery( this ).find( '.media-field input' );
							if ( nodeMediaInput.length <= 0 ) return true;
							setAPFMediaUploader( nodeMediaInput.attr( 'id' ), true, jQuery( nodeButton ).attr( 'data-enable_external_source' ) );
							
						});						
					},
					removed_repeatable_field: function( node, sFieldType, sFieldTagID, iCallType ) {
						
						/* 1. Return if it is not the type. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;	/* If it is not the color field type, do nothing. */
						if ( node.find( '.select_media' ).length <= 0 )  return;	/* If the uploader buttons are not found, do nothing */
						
						/* 2. Decrement the ids of the next all (including this one) uploader buttons. ( the input values are already dealt by the framework repeater script ) */
						var nodeFieldContainer = node.closest( '.admin-page-framework-field' );
						var iOccurence = iCallType === 1 ? 1 : 0;	// the occurrence value indicates which part of digit to change 
						nodeFieldContainer.nextAll().andSelf().each( function( iIndex ) {
							
							/* 2-1. Decrement the button ID */
							nodeButton = jQuery( this ).find( '.select_media' );		

							// If it's for repeatable sections, updating the attributes is only necessary for the first iteration.
							if ( ! ( iCallType === 1 && iIndex !== 0 ) ) {										
								nodeButton.decrementIDAttribute( 'id', iOccurence );
							}
														
							/* 2-2. Rebind the uploader script to each button. */
							var nodeMediaInput = jQuery( this ).find( '.media-field input' );
							if ( nodeMediaInput.length <= 0 ) return true;
							setAPFMediaUploader( nodeMediaInput.attr( 'id' ), true, jQuery( nodeButton ).attr( 'data-enable_external_source' ) );	
console.log( 'updated media input: ' + nodeMediaInput.attr( 'id' ) );
						});
					},
					
					sorted_fields : function( node, sFieldType, sFieldsTagID ) {	// on contrary to repeatable callbacks, the _fields_ container node and its ID will be passed.

						/* 1. Return if it is not the type. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;	/* If it is not the color field type, do nothing. */						
						if ( node.find( '.select_media' ).length <= 0 )  return;	/* If the uploader buttons are not found, do nothing */
						
						/* 2. Update the Select File button */
						var iCount = 0;
						node.children( '.admin-page-framework-field' ).each( function() {
							
							nodeButton = jQuery( this ).find( '.select_media' );
							
							/* 2-1. Set the current iteration index to the button ID */
							nodeButton.setIndexIDAttribute( 'id', iCount );	
							
							/* 2-2. Rebuind the uploader script to the button */
							var nodeMediaInput = jQuery( this ).find( '.media-field input' );
							if ( nodeMediaInput.length <= 0 ) return true;
							setAPFMediaUploader( nodeMediaInput.attr( 'id' ), true, jQuery( nodeButton ).attr( 'data-enable_external_source' ) );
	
							iCount++;
						});
					},
					
				});
			});" . PHP_EOL; } private function _getScript_MediaUploader( $sReferrer, $sThickBoxTitle, $sThickBoxButtonUseThis ) { if ( ! function_exists( 'wp_enqueue_media' ) ) return "
					jQuery( document ).ready( function(){
						
						/**
						 * Bind/rebinds the thickbox script the given selector element.
						 * The fMultiple parameter does not do anything. It is there to be consistent with the one for the WordPress version 3.5 or above.
						 */
						setAPFMediaUploader = function( sInputID, fMultiple, fExternalSource ) {
							jQuery( '#select_media_' + sInputID ).unbind( 'click' );	// for repeatable fields
							jQuery( '#select_media_' + sInputID ).click( function() {
								var sPressedID = jQuery( this ).attr( 'id' );
								window.sInputID = sPressedID.substring( 13 );	// remove the select_media_ prefix and set a property to pass it to the editor callback method.
								window.original_send_to_editor = window.send_to_editor;
								window.send_to_editor = hfAPFSendToEditorMedia;
								var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );
								tb_show( '{$sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$sThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
								return false;	// do not click the button after the script by returning false.									
							});	
						}			
														
						var hfAPFSendToEditorMedia = function( sRawHTML, param ) {

							var sHTML = '<div>' + sRawHTML + '</div>';	// This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.
							var src = jQuery( 'a', sHTML ).attr( 'href' );
							var classes = jQuery( 'a', sHTML ).attr( 'class' );
							var id = ( classes ) ? classes.replace( /(.*?)wp-image-/, '' ) : '';	// attachment ID	
						
							// If the user wants to save relavant attributes, set them.
							var sInputID = window.sInputID;
							jQuery( '#' + sInputID ).val( src );	// sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
							jQuery( '#' + sInputID + '_id' ).val( id );			
								
							// restore the original send_to_editor
							window.send_to_editor = window.original_send_to_editor;
							
							// close the thickbox
							tb_remove();	

						}
					});
				"; return "
			jQuery( document ).ready( function(){		
				
				// Global Function Literal 
				/**
				 * Binds/rebinds the uploader button script to the specified element with the given ID.
				 */				
				setAPFMediaUploader = function( sInputID, fMultiple, fExternalSource ) {

					jQuery( '#select_media_' + sInputID ).unbind( 'click' );	// for repeatable fields
					jQuery( '#select_media_' + sInputID ).click( function( e ) {
console.log( 'pressed id: ' + jQuery( this ).attr( 'id' ) );					
						// Reassign the input id from the pressed element ( do not use the passed parameter value to the caller function ) for repeatable sections.
						var sInputID = jQuery( this ).attr( 'id' ).substring( 13 );	// remove the select_image_ prefix and set a property to pass it to the editor callback method.
console.log( 'rebinding id: ' + sInputID );
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
										var new_field = jQuery( this ).addAPFRepeatableField( field_container.attr( 'id' ) );
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
				
			});"; } public function _replyToGetStyles() { return "/* Media Uploader Button */
			.admin-page-framework-field-media input {
				margin-right: 0.5em;
				vertical-align: middle;	
			}
			.select_media.button.button-small {
				margin-top: 0.1em;
			}
		"; } public function _replyToGetField( $aField ) { return parent::_replyToGetField( $aField ); } protected function _getPreviewContainer( $aField, $sImageURL, $aPreviewAtrributes ) { return ""; } protected function _getUploaderButtonScript( $sInputID, $bRpeatable, $bExternalSource, array $aButtonAttributes ) { $sButton = "<a " . $this->generateAttributes( array( 'id' => "select_media_{$sInputID}", 'href' => '#', 'class' => 'select_media button button-small ' . ( isset( $aButtonAttributes['class'] ) ? $aButtonAttributes['class'] : '' ), 'data-uploader_type' => function_exists( 'wp_enqueue_media' ) ? 1 : 0, 'data-enable_external_source' => $bExternalSource ? 1 : 0, ) + $aButtonAttributes ) . ">" . $this->oMsg->__( 'select_file' ) ."</a>"; $sScript = "
				if ( jQuery( 'a#select_media_{$sInputID}' ).length == 0 ) {
					jQuery( 'input#{$sInputID}' ).after( \"{$sButton}\" );
				}
				jQuery( document ).ready( function(){			
					setAPFMediaUploader( '{$sInputID}', '{$bRpeatable}', '{$bExternalSource}' );
				});" . PHP_EOL; return "<script type='text/javascript' class='admin-page-framework-media-uploader-button'>" . $sScript . "</script>". PHP_EOL; } } endif;if ( ! class_exists( 'AdminPageFramework_FieldType_size' ) ) : class AdminPageFramework_FieldType_size extends AdminPageFramework_FieldType_select { public $aFieldTypeSlugs = array( 'size', ); protected $aDefaultKeys = array( 'is_multiple' => false, 'units' => null, 'attributes' => array( 'size' => array( 'size' => 10, 'maxlength' => 400, 'min' => '', 'max' => '', ), 'unit' => array( 'multiple' => '', 'size' => 1, 'autofocusNew' => '', 'multiple' => '', 'required' => '', ), 'optgroup' => array(), 'option' => array(), ), ); protected $aDefaultUnits = array( 'px' => 'px', '%' => '%', 'em' => 'em', 'ex' => 'ex', 'in' => 'in', 'cm' => 'cm', 'mm' => 'mm', 'pt' => 'pt', 'pc' => 'pc', ); public function _replyToFieldLoader() { } public function _replyToGetScripts() { return ""; } public function _replyToGetStyles() { return "/* Size Field Type */
		.admin-page-framework-field-size input {
			text-align: right;
		}
		.admin-page-framework-field-size select.size-field-select {
			vertical-align: 0px;			
		}
		.admin-page-framework-field-size label {
			width: auto;			
		} 
		.form-table td fieldset .admin-page-framework-field-size label {
			display: inline;
		}
		" . PHP_EOL; } public function _replyToGetField( $aField ) { $aField['units'] = isset( $aField['units'] ) ? $aField['units'] : $this->aDefaultUnits; $aBaseAttributes = $aField['attributes']; unset( $aBaseAttributes['unit'], $aBaseAttributes['size'] ); $aSizeAttributes = array( 'type' => 'number', 'id' => $aField['input_id'] . '_' . 'size', 'name' => $aField['_input_name'] . '[size]', 'value' => isset( $aField['value']['size'] ) ? $aField['value']['size'] : '', ) + $this->getFieldElementByKey( $aField['attributes'], 'size', $this->aDefaultKeys['attributes']['size'] ) + $aBaseAttributes; $aSizeLabelAttributes = array( 'for' => $aSizeAttributes['id'], 'class' => $aSizeAttributes['disabled'] ? 'disabled' : '', ); $aUnitAttributes = array( 'type' => 'select', 'id' => $aField['input_id'] . '_' . 'unit', 'multiple' => $aField['is_multiple'] ? 'Multiple' : $aField['attributes']['unit']['multiple'], 'value' => isset( $aField['value']['unit'] ) ? $aField['value']['unit'] : '', ) + $this->getFieldElementByKey( $aField['attributes'], 'unit', $this->aDefaultKeys['attributes']['unit'] ) + $aBaseAttributes; $aUnitAttributes['name'] = empty( $aUnitAttributes['multiple'] ) ? "{$aField['_input_name']}[unit]" : "{$aField['_input_name']}[unit][]"; $aUnitLabelAttributes = array( 'for' => $aUnitAttributes['id'], 'class' => $aUnitAttributes['disabled'] ? 'disabled' : '', ); return $aField['before_label'] . "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: {$aField['label_min_width']}px;'>" . "<label " . $this->generateAttributes( $aSizeLabelAttributes ) . ">" . $this->getFieldElementByKey( $aField['before_label'], 'size' ) . ( $aField['label'] && ! $aField['repeatable'] ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>" : "" ) . "<input " . $this->generateAttributes( $aSizeAttributes ) . " />" . $this->getFieldElementByKey( $aField['after_input'], 'size' ) . "</label>" . "<label " . $this->generateAttributes( $aUnitLabelAttributes ) . ">" . $this->getFieldElementByKey( $aField['before_label'], 'unit' ) . "<span class='admin-page-framework-input-container'>" . "<select " . $this->generateAttributes( $aUnitAttributes ) . " >" . $this->_getOptionTags( $aUnitAttributes['id'], $aBaseAttributes, $aField['units'] ) . "</select>" . "</span>" . $this->getFieldElementByKey( $aField['after_input'], 'unit' ) . "<div class='repeatable-field-buttons'></div>" . "</label>" . "</div>" . $aField['after_label']; } } endif;if ( ! class_exists( 'AdminPageFramework_HeadTag_Base' ) ) : abstract class AdminPageFramework_HeadTag_Base { protected static $_aStructure_EnqueuingScriptsAndStyles = array( 'sSRC' => null, 'aPostTypes' => array(), 'sPageSlug' => null, 'sTabSlug' => null, 'sType' => null, 'handle_id' => null, 'dependencies' => array(), 'version' => false, 'translation' => array(), 'in_footer' => false, 'media' => 'all', ); function __construct( $oProp ) { $this->oProp = $oProp; $this->oUtil = new AdminPageFramework_WPUtility; add_action( 'admin_head', array( $this, '_replyToAddStyle' ), 999 ); add_action( 'admin_head', array( $this, '_replyToAddScript' ), 999 ); add_action( 'admin_enqueue_scripts', array( $this, '_replyToEnqueueScripts' ) ); add_action( 'admin_enqueue_scripts', array( $this, '_replyToEnqueueStyles' ) ); } public function _replyToAddStyle() {} public function _replyToAddScript() {} protected function _enqueueSRCByConditoin( $aEnqueueItem ) {} public function _forceToEnqueueStyle( $sSRC, $aCustomArgs=array() ) {} public function _forceToEnqueueScript( $sSRC, $aCustomArgs=array() ) {} protected function _enqueueSRC( $aEnqueueItem ) { if ( $aEnqueueItem['sType'] == 'style' ) { wp_enqueue_style( $aEnqueueItem['handle_id'], $aEnqueueItem['sSRC'], $aEnqueueItem['dependencies'], $aEnqueueItem['version'], $aEnqueueItem['media'] ); return; } wp_enqueue_script( $aEnqueueItem['handle_id'], $aEnqueueItem['sSRC'], $aEnqueueItem['dependencies'], $aEnqueueItem['version'], $aEnqueueItem['in_footer'] ); if ( $aEnqueueItem['translation'] ) wp_localize_script( $aEnqueueItem['handle_id'], $aEnqueueItem['handle_id'], $aEnqueueItem['translation'] ); } public function _replyToEnqueueStyles() { foreach( $this->oProp->aEnqueuingStyles as $sKey => $aEnqueuingStyle ) $this->_enqueueSRCByConditoin( $aEnqueuingStyle ); } public function _replyToEnqueueScripts() { foreach( $this->oProp->aEnqueuingScripts as $sKey => $aEnqueuingScript ) $this->_enqueueSRCByConditoin( $aEnqueuingScript ); } } endif;if ( ! class_exists( 'AdminPageFramework_HeadTag_MetaBox' ) ) : class AdminPageFramework_HeadTag_MetaBox extends AdminPageFramework_HeadTag_Base { private $_sPostTypeSlugOfCurrentPost = null; public function _replyToAddStyle() { if ( ! $this->oUtil->isPostDefinitionPage( $this->oProp->aPostTypes ) ) return; $this->_printCommonStyles( 'admin-page-framework-style-meta-box-common', get_class() ); $this->_printClassSpecificStyles( 'admin-page-framework-style-meta-box' ); $this->oProp->_bAddedStyle = true; } public function _replyToAddScript() { if ( ! $this->oUtil->isPostDefinitionPage( $this->oProp->aPostTypes ) ) return; $this->_printCommonScripts( 'admin-page-framework-script-meta-box-common', get_class() ); $this->_printClassSpecificScripts( 'admin-page-framework-script-meta-box' ); $this->oProp->_bAddedScript = true; } protected function _printClassSpecificStyles( $sIDPrefix ) { $oCaller = $this->oProp->_getCallerObject(); $sStyle = $this->oUtil->addAndApplyFilters( $oCaller, "style_{$this->oProp->sClassName}", $this->oProp->sStyle ); $sStyle = $this->oUtil->minifyCSS( $sStyle ); if ( $sStyle ) echo "<style type='text/css' id='{$sIDPrefix}-{$this->oProp->sClassName}'>{$sStyle}</style>"; $sStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_{$this->oProp->sClassName}", $this->oProp->sStyleIE ); $sStyleIE = $this->oUtil->minifyCSS( $sStyleIE ); if ( $sStyleIE ) echo "<!--[if IE]><style type='text/css' id='{$sIDPrefix}-ie-{$this->oProp->sClassName}'>{$sStyleIE}</style><![endif]-->"; } protected function _printCommonStyles( $sIDPrefix, $sClassName ) { if ( isset( $GLOBALS[ "{$sClassName}_StyleLoaded" ] ) && $GLOBALS[ "{$sClassName}_StyleLoaded" ] ) return; $GLOBALS[ "{$sClassName}_StyleLoaded" ] = true; $oCaller = $this->oProp->_getCallerObject(); $sStyle = $this->oUtil->addAndApplyFilters( $oCaller, "style_common_{$this->oProp->sClassName}", AdminPageFramework_Property_Base::$_sDefaultStyle ); $sStyle = $this->oUtil->minifyCSS( $sStyle ); if ( $sStyle ) echo "<style type='text/css' id='{$sIDPrefix}'>{$sStyle}</style>"; $sStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_common_{$this->oProp->sClassName}", AdminPageFramework_Property_Base::$_sDefaultStyleIE ); $sStyleIE = $this->oUtil->minifyCSS( $sStyleIE ); if ( $sStyleIE ) echo "<!--[if IE]><style type='text/css' id='{$sIDPrefix}-ie'>{$sStyleIE}</style><![endif]-->"; } protected function _printClassSpecificScripts( $sIDPrefix ) { $sScript = $this->oUtil->addAndApplyFilters( $this->oProp->_getCallerObject(), "script_{$this->oProp->sClassName}", $this->oProp->sScript ); if ( $sScript ) echo "<script type='text/javascript' id='{$sIDPrefix}-{$this->oProp->sClassName}'>{$sScript}</script>"; } protected function _printCommonScripts( $sIDPrefix, $sClassName ) { if ( isset( $GLOBALS[ "{$sClassName}_ScriptLoaded" ] ) && $GLOBALS[ "{$sClassName}_ScriptLoaded" ] ) return; $GLOBALS[ "{$sClassName}_ScriptLoaded" ] = true; $sScript = $this->oUtil->addAndApplyFilters( $this->oProp->_getCallerObject(), "script_common_{$this->oProp->sClassName}", AdminPageFramework_Property_Base::$_sDefaultScript ); if ( $sScript ) echo "<script type='text/javascript' id='{$sIDPrefix}'>{$sScript}</script>"; } public function _enqueueStyles( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) { $aHandleIDs = array(); foreach( ( array ) $aSRCs as $sSRC ) $aHandleIDs[] = $this->_enqueueStyle( $sSRC, $aPostTypes, $aCustomArgs ); return $aHandleIDs; } public function _enqueueStyle( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) { $sSRC = trim( $sSRC ); if ( empty( $sSRC ) ) return ''; if ( isset( $this->oProp->aEnqueuingScripts[ md5( $sSRC ) ] ) ) return ''; $sSRC = $this->oUtil->resolveSRC( $sSRC ); $sSRCHash = md5( $sSRC ); $this->oProp->aEnqueuingStyles[ $sSRCHash ] = $this->oUtil->uniteArrays( ( array ) $aCustomArgs, array( 'sSRC' => $sSRC, 'aPostTypes' => empty( $aPostTypes ) ? $this->oProp->aPostTypes : $aPostTypes, 'sType' => 'style', 'handle_id' => 'style_' . $this->oProp->sClassName . '_' . ( ++$this->oProp->iEnqueuedStyleIndex ), ), self::$_aStructure_EnqueuingScriptsAndStyles ); return $this->oProp->aEnqueuingStyles[ $sSRCHash ][ 'handle_id' ]; } public function _enqueueScripts( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) { $aHandleIDs = array(); foreach( ( array ) $aSRCs as $sSRC ) $aHandleIDs[] = $this->_enqueueScript( $sSRC, $aPostTypes, $aCustomArgs ); return $aHandleIDs; } public function _enqueueScript( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) { $sSRC = trim( $sSRC ); if ( empty( $sSRC ) ) return ''; if ( isset( $this->oProp->aEnqueuingScripts[ md5( $sSRC ) ] ) ) return ''; $sSRC = $this->oUtil->resolveSRC( $sSRC ); $sSRCHash = md5( $sSRC ); $this->oProp->aEnqueuingScripts[ $sSRCHash ] = $this->oUtil->uniteArrays( ( array ) $aCustomArgs, array( 'sSRC' => $sSRC, 'aPostTypes' => empty( $aPostTypes ) ? $this->oProp->aPostTypes : $aPostTypes, 'sType' => 'script', 'handle_id' => 'script_' . $this->oProp->sClassName . '_' . ( ++$this->oProp->iEnqueuedScriptIndex ), ), self::$_aStructure_EnqueuingScriptsAndStyles ); return $this->oProp->aEnqueuingScripts[ $sSRCHash ][ 'handle_id' ]; } public function _forceToEnqueueStyle( $sSRC, $aCustomArgs=array() ) { return $this->_enqueueStyle( $sSRC, array(), $aCustomArgs ); } public function _forceToEnqueueScript( $sSRC, $aCustomArgs=array() ) { return $this->_enqueueScript( $sSRC, array(), $aCustomArgs ); } protected function _enqueueSRCByConditoin( $aEnqueueItem ) { $sCurrentPostType = isset( $_GET['post_type'] ) ? $_GET['post_type'] : ( isset( $GLOBALS['typenow'] ) ? $GLOBALS['typenow'] : null ); if ( in_array( $sCurrentPostType, $aEnqueueItem['aPostTypes'] ) ) return $this->_enqueueSRC( $aEnqueueItem ); } } endif;if ( ! class_exists( 'AdminPageFramework_HeadTag_Page' ) ) : class AdminPageFramework_HeadTag_Page extends AdminPageFramework_HeadTag_Base { public function _replyToAddStyle() { $sPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null; $sTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProp->getDefaultInPageTab( $sPageSlug ); if ( ! $this->oProp->isPageAdded( $sPageSlug ) ) return; $oCaller = $this->oProp->_getCallerObject(); $sStyle = $this->oUtil->addAndApplyFilters( $oCaller, $this->oUtil->getFilterArrayByPrefix( 'style_common_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), AdminPageFramework_Property_Page::$_sDefaultStyle ) . $this->oUtil->addAndApplyFilters( $oCaller, $this->oUtil->getFilterArrayByPrefix( 'style_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), $this->oProp->sStyle ); $sStyle = $this->oUtil->minifyCSS( $sStyle ); if ( $sStyle ) echo "<style type='text/css' id='admin-page-framework-style_{$this->oProp->sClassName}'>{$sStyle}</style>"; $sStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, $this->oUtil->getFilterArrayByPrefix( 'style_common_ie_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), AdminPageFramework_Property_Page::$_sDefaultStyleIE ) . $this->oUtil->addAndApplyFilters( $oCaller, $this->oUtil->getFilterArrayByPrefix( 'style_ie_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), $this->oProp->sStyleIE ); $sStyleIE = $this->oUtil->minifyCSS( $sStyleIE ); if ( $sStyleIE ) echo "<!--[if IE]><style type='text/css' id='admin-page-framework-style-for-IE_{$this->oProp->sClassName}'>{$sStyleIE}</style><![endif]-->"; $this->oProp->_bAddedStyle = true; } public function _replyToAddScript() { $sPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null; $sTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProp->getDefaultInPageTab( $sPageSlug ); if ( ! $this->oProp->isPageAdded( $sPageSlug ) ) return; $oCaller = $this->oProp->_getCallerObject(); echo "<script type='text/javascript' id='admin-page-framework-script_{$this->oProp->sClassName}'>" . ( $sScript = $this->oUtil->addAndApplyFilters( $oCaller, $this->oUtil->getFilterArrayByPrefix( 'script_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), $this->oProp->sScript ) ) . "</script>"; $this->oProp->_bAddedScript = true; } public function _enqueueStyles( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) { $aHandleIDs = array(); foreach( ( array ) $aSRCs as $sSRC ) $aHandleIDs[] = $this->_enqueueStyle( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs ); return $aHandleIDs; } public function _enqueueStyle( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) { $sSRC = trim( $sSRC ); if ( empty( $sSRC ) ) return ''; if ( isset( $this->oProp->aEnqueuingScripts[ md5( $sSRC ) ] ) ) return ''; $sSRC = $this->oUtil->resolveSRC( $sSRC ); $sSRCHash = md5( $sSRC ); $this->oProp->aEnqueuingStyles[ $sSRCHash ] = $this->oUtil->uniteArrays( ( array ) $aCustomArgs, array( 'sSRC' => $sSRC, 'sPageSlug' => $sPageSlug, 'sTabSlug' => $sTabSlug, 'sType' => 'style', 'handle_id' => 'style_' . $this->oProp->sClassName . '_' . ( ++$this->oProp->iEnqueuedStyleIndex ), ), self::$_aStructure_EnqueuingScriptsAndStyles ); return $this->oProp->aEnqueuingStyles[ $sSRCHash ][ 'handle_id' ]; } public function _enqueueScripts( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) { $aHandleIDs = array(); foreach( ( array ) $aSRCs as $sSRC ) $aHandleIDs[] = $this->_enqueueScript( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs ); return $aHandleIDs; } public function _enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) { $sSRC = trim( $sSRC ); if ( empty( $sSRC ) ) return ''; if ( isset( $this->oProp->aEnqueuingScripts[ md5( $sSRC ) ] ) ) return ''; $sSRC = $this->oUtil->resolveSRC( $sSRC ); $sSRCHash = md5( $sSRC ); $this->oProp->aEnqueuingScripts[ $sSRCHash ] = $this->oUtil->uniteArrays( ( array ) $aCustomArgs, array( 'sPageSlug' => $sPageSlug, 'sTabSlug' => $sTabSlug, 'sSRC' => $sSRC, 'sType' => 'script', 'handle_id' => 'script_' . $this->oProp->sClassName . '_' . ( ++$this->oProp->iEnqueuedScriptIndex ), ), self::$_aStructure_EnqueuingScriptsAndStyles ); return $this->oProp->aEnqueuingScripts[ $sSRCHash ][ 'handle_id' ]; } public function _forceToEnqueueStyle( $sSRC, $aCustomArgs=array() ) { return $this->_enqueueStyle( $sSRC, '', '', $aCustomArgs ); } public function _forceToEnqueueScript( $sSRC, $aCustomArgs=array() ) { return $this->_enqueueScript( $sSRC, '', '', $aCustomArgs ); } protected function _enqueueSRCByConditoin( $aEnqueueItem ) { $sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : ''; $sCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProp->getDefaultInPageTab( $sCurrentPageSlug ); $sPageSlug = $aEnqueueItem['sPageSlug']; $sTabSlug = $aEnqueueItem['sTabSlug']; if ( ! $sPageSlug && $this->oProp->isPageAdded( $sCurrentPageSlug ) ) return $this->_enqueueSRC( $aEnqueueItem ); if ( ( $sPageSlug && $sCurrentPageSlug == $sPageSlug ) && ( $sTabSlug && $sCurrentTabSlug == $sTabSlug ) ) return $this->_enqueueSRC( $aEnqueueItem ); if ( ( $sPageSlug && ! $sTabSlug ) && ( $sCurrentPageSlug == $sPageSlug ) ) return $this->_enqueueSRC( $aEnqueueItem ); } } endif;if ( ! class_exists( 'AdminPageFramework_HeadTag_MetaBox_Page' ) ) : class AdminPageFramework_HeadTag_MetaBox_Page extends AdminPageFramework_HeadTag_Page { private function _isMetaBoxPage() { if ( ! isset( $_GET['page'] ) ) return false; if ( in_array( $_GET['page'], $this->oProp->aPageSlugs ) ) return true; return false; } public function _replyToAddStyle() { if ( ! $this->_isMetaBoxPage() ) return; $this->_printCommonStyles( 'admin-page-framework-style-meta-box-common', get_class() ); $this->_printClassSpecificStyles( 'admin-page-framework-style-meta-box' ); $this->oProp->_bAddedStyle = true; } public function _replyToAddScript() { if ( ! $this->_isMetaBoxPage() ) return; $this->_printCommonScripts( 'admin-page-framework-style-meta-box-common', get_class() ); $this->_printClassSpecificScripts( 'admin-page-framework-script-meta-box' ); $this->oProp->_bAddedScript = true; } protected function _printClassSpecificStyles( $sIDPrefix ) { $oCaller = $this->oProp->_getCallerObject(); $sStyle = $this->oUtil->addAndApplyFilters( $oCaller, "style_{$this->oProp->sClassName}", $this->oProp->sStyle ); $sStyle = $this->oUtil->minifyCSS( $sStyle ); if ( $sStyle ) echo "<style type='text/css' id='{$sIDPrefix}-{$this->oProp->sClassName}'>{$sStyle}</style>"; $sStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_{$this->oProp->sClassName}", $this->oProp->sStyleIE ); if ( $sStyleIE ) echo "<!--[if IE]><style type='text/css' id='{$sIDPrefix}-ie-{$this->oProp->sClassName}'>{$sStyleIE}</style><![endif]-->"; } protected function _printCommonStyles( $sIDPrefix, $sClassName ) { if ( isset( $GLOBALS[ "{$sClassName}_StyleLoaded" ] ) && $GLOBALS[ "{$sClassName}_StyleLoaded" ] ) return; $GLOBALS[ "{$sClassName}_StyleLoaded" ] = true; $oCaller = $this->oProp->_getCallerObject(); $sStyle = $this->oUtil->addAndApplyFilters( $oCaller, "style_common_{$this->oProp->sClassName}", AdminPageFramework_Property_Base::$_sDefaultStyle ); $sStyle = $this->oUtil->minifyCSS( $sStyle ); if ( $sStyle ) echo "<style type='text/css' id='{$sIDPrefix}'>{$sStyle}</style>"; $sStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_common_{$this->oProp->sClassName}", AdminPageFramework_Property_Base::$_sDefaultStyleIE ); $sStyleIE = $this->oUtil->minifyCSS( $sStyleIE ); if ( $sStyleIE ) echo "<!--[if IE]><style type='text/css' id='{$sIDPrefix}-ie'>{$sStyleIE}</style><![endif]-->"; } protected function _printClassSpecificScripts( $sIDPrefix ) { $sScript = $this->oUtil->addAndApplyFilters( $this->oProp->_getCallerObject(), "script_{$this->oProp->sClassName}", $this->oProp->sScript ); if ( $sScript ) echo "<script type='text/javascript' id='{$sIDPrefix}-{$this->oProp->sClassName}'>{$sScript}</script>"; } protected function _printCommonScripts( $sIDPrefix, $sClassName ) { if ( isset( $GLOBALS[ "{$sClassName}_ScriptLoaded" ] ) && $GLOBALS[ "{$sClassName}_ScriptLoaded" ] ) return; $GLOBALS[ "{$sClassName}_ScriptLoaded" ] = true; $sScript = $this->oUtil->addAndApplyFilters( $this->oProp->_getCallerObject(), "script_common_{$this->oProp->sClassName}", AdminPageFramework_Property_Base::$_sDefaultScript ); if ( $sScript ) echo "<script type='text/javascript' id='{$sIDPrefix}'>{$sScript}</script>"; } } endif;if ( ! class_exists( 'AdminPageFramework_HeadTag_PostType' ) ) : class AdminPageFramework_HeadTag_PostType extends AdminPageFramework_HeadTag_MetaBox { public function _replyToAddStyle() { if ( ! ( in_array( $GLOBALS['pagenow'], array( 'edit.php', 'edit-tags.php' ) ) && ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->oProp->sPostType ) || $this->oUtil->isPostDefinitionPage( $this->oProp->sPostType ) ) ) return; if ( isset( $_GET['page'] ) && $_GET['page'] ) return; $sRootClassName = get_class(); if ( isset( $GLOBALS[ "{$sRootClassName}_StyleLoaded" ] ) && $GLOBALS[ "{$sRootClassName}_StyleLoaded" ] ) return; $GLOBALS[ "{$sRootClassName}_StyleLoaded" ] = true; $oCaller = $this->oProp->_getCallerObject(); $sStyle = $this->oUtil->addAndApplyFilters( $oCaller, "style_common_{$this->oProp->sClassName}", AdminPageFramework_Property_PostType::$_sDefaultStyle ) . $this->oUtil->addAndApplyFilters( $oCaller, "style_{$this->oProp->sClassName}", $this->oProp->sStyle ); $sStyle = $this->oUtil->minifyCSS( $sStyle ); if ( $sStyle ) echo "<style type='text/css' id='admin-page-framework-style-post-type'>{$sStyle}</style>"; $sStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_{$this->oProp->sClassName}", AdminPageFramework_Property_PostType::$_sDefaultStyleIE ) . $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_{$this->oProp->sClassName}", $this->oProp->sStyleIE ); $sStyleIE = $this->oUtil->minifyCSS( $sStyleIE ); if ( $sStyleIE ) echo "<!--[if IE]><style type='text/css' id='admin-page-framework-style-post-type'>{$sStyleIE}</style><![endif]-->"; } public function _replyToAddScript() { if ( ! ( in_array( $GLOBALS['pagenow'], array( 'edit.php', 'edit-tags.php' ) ) && ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->oProp->sPostType ) || $this->oUtil->isPostDefinitionPage( $this->oProp->sPostType ) ) ) return; if ( isset( $_GET['page'] ) && $_GET['page'] ) return; $sRootClassName = get_class(); if ( isset( $GLOBALS[ "{$sRootClassName}_ScriptLoaded" ] ) && $GLOBALS[ "{$sRootClassName}_ScriptLoaded" ] ) return; $GLOBALS[ "{$sRootClassName}_ScriptLoaded" ] = true; $oCaller = $this->oProp->_getCallerObject(); $sScript = $this->oUtil->addAndApplyFilters( $oCaller, "script_{$this->oProp->sClassName}", $this->oProp->sScript ); if ( $sScript ) echo "<script type='text/javascript' id='admin-page-framework-script-post-type'>{$sScript}</script>"; } } endif;if ( ! class_exists( 'AdminPageFramework_HeadTag_TaxonomyField' ) ) : class AdminPageFramework_HeadTag_TaxonomyField extends AdminPageFramework_HeadTag_MetaBox { public function _replyToAddStyle() { if ( $GLOBALS['pagenow'] != 'edit-tags.php' ) return; $this->_printCommonStyles( 'admin-page-framework-style-taxonomy-field-common', get_class() ); $this->_printClassSpecificStyles( 'admin-page-framework-style-taxonomy-field' ); $this->oProp->_bAddedStyle = true; } public function _replyToAddScript() { if ( $GLOBALS['pagenow'] != 'edit-tags.php' ) return; $this->_printCommonScripts( 'admin-page-framework-style-taxonomy-field-common', get_class() ); $this->_printClassSpecificScripts( 'admin-page-framework-script-taxonomy-field' ); $this->oProp->_bAddedScript = true; } public function _enqueueStyles( $aSRCs, $aCustomArgs=array(), $_deprecated=null ) { $aHandleIDs = array(); foreach( ( array ) $aSRCs as $sSRC ) $aHandleIDs[] = $this->_enqueueStyle( $sSRC, $aCustomArgs ); return $aHandleIDs; } public function _enqueueStyle( $sSRC, $aCustomArgs=array(), $_deprecated=null ) { $sSRC = trim( $sSRC ); if ( empty( $sSRC ) ) return ''; if ( isset( $this->oProp->aEnqueuingScripts[ md5( $sSRC ) ] ) ) return ''; $sSRC = $this->oUtil->resolveSRC( $sSRC ); $sSRCHash = md5( $sSRC ); $this->oProp->aEnqueuingStyles[ $sSRCHash ] = $this->oUtil->uniteArrays( ( array ) $aCustomArgs, array( 'sSRC' => $sSRC, 'sType' => 'style', 'handle_id' => 'style_' . $this->oProp->sClassName . '_' . ( ++$this->oProp->iEnqueuedStyleIndex ), ), self::$_aStructure_EnqueuingScriptsAndStyles ); return $this->oProp->aEnqueuingStyles[ $sSRCHash ][ 'handle_id' ]; } public function _enqueueScripts( $aSRCs, $aCustomArgs=array(), $_deprecated=null ) { $aHandleIDs = array(); foreach( ( array ) $aSRCs as $sSRC ) $aHandleIDs[] = $this->_enqueueScript( $sSRC, $aCustomArgs ); return $aHandleIDs; } public function _enqueueScript( $sSRC, $aCustomArgs=array(), $_deprecated=null ) { $sSRC = trim( $sSRC ); if ( empty( $sSRC ) ) return ''; if ( isset( $this->oProp->aEnqueuingScripts[ md5( $sSRC ) ] ) ) return ''; $sSRC = $this->oUtil->resolveSRC( $sSRC ); $sSRCHash = md5( $sSRC ); $this->oProp->aEnqueuingScripts[ $sSRCHash ] = $this->oUtil->uniteArrays( ( array ) $aCustomArgs, array( 'sSRC' => $sSRC, 'sType' => 'script', 'handle_id' => 'script_' . $this->oProp->sClassName . '_' . ( ++$this->oProp->iEnqueuedScriptIndex ), ), self::$_aStructure_EnqueuingScriptsAndStyles ); return $this->oProp->aEnqueuingScripts[ $sSRCHash ][ 'handle_id' ]; } public function _forceToEnqueueStyle( $sSRC, $aCustomArgs=array() ) { return $this->_enqueueStyle( $sSRC, $aCustomArgs ); } public function _forceToEnqueueScript( $sSRC, $aCustomArgs=array() ) { return $this->_enqueueScript( $sSRC, $aCustomArgs ); } protected function _enqueueSRCByConditoin( $aEnqueueItem ) { return $this->_enqueueSRC( $aEnqueueItem ); } } endif;if ( ! class_exists( 'AdminPageFramework_Message' ) ) : class AdminPageFramework_Message { public $aMessages = array(); private static $_oInstance; protected $_sTextDomain = 'admin-page-framework'; public static function instantiate( $sTextDomain='admin-page-framework' ) { static $_sTextDomain; $_sTextDomain = $sTextDomain ? $sTextDomain : ( $_sTextDomain ? $_sTextDomain : 'admin-page-framework' ) ; if ( ! isset( self::$_oInstance ) && ! ( self::$_oInstance instanceof AdminPageFramework_Message ) ) self::$_oInstance = new AdminPageFramework_Message( $_sTextDomain ); return self::$_oInstance; } public function __construct( $sTextDomain='admin-page-framework' ) { $this->_sTextDomain = $sTextDomain; $this->aMessages = array( 'option_updated' => __( 'The options have been updated.', 'admin-page-framework' ), 'option_cleared' => __( 'The options have been cleared.', 'admin-page-framework' ), 'export' => __( 'Export', 'admin-page-framework' ), 'export_options' => __( 'Export Options', 'admin-page-framework' ), 'import_options' => __( 'Import', 'admin-page-framework' ), 'import_options' => __( 'Import Options', 'admin-page-framework' ), 'submit' => __( 'Submit', 'admin-page-framework' ), 'import_error' => __( 'An error occurred while uploading the import file.', 'admin-page-framework' ), 'uploaded_file_type_not_supported' => __( 'The uploaded file type is not supported: %1$s', 'admin-page-framework' ), 'could_not_load_importing_data' => __( 'Could not load the importing data.', 'admin-page-framework' ), 'imported_data' => __( 'The uploaded file has been imported.', 'admin-page-framework' ), 'not_imported_data' => __( 'No data could be imported.', 'admin-page-framework' ), 'upload_image' => __( 'Upload Image', 'admin-page-framework' ), 'use_this_image' => __( 'Use This Image', 'admin-page-framework' ), 'reset_options' => __( 'Are you sure you want to reset the options?', 'admin-page-framework' ), 'confirm_perform_task' => __( 'Please confirm if you want to perform the specified task.', 'admin-page-framework' ), 'option_been_reset' => __( 'The options have been reset.', 'admin-page-framework' ), 'specified_option_been_deleted' => __( 'The specified options have been deleted.', 'admin-page-framework' ), 'title' => __( 'Title', 'admin-page-framework' ), 'author' => __( 'Author', 'admin-page-framework' ), 'categories' => __( 'Categories', 'admin-page-framework' ), 'tags' => __( 'Tags', 'admin-page-framework' ), 'comments' => __( 'Comments', 'admin-page-framework' ), 'date' => __( 'Date', 'admin-page-framework' ), 'show_all' => __( 'Show All', 'admin-page-framework' ), 'powered_by' => __( 'Powered by', 'admin-page-framework' ), 'settings' => __( 'Settings', 'admin-page-framework' ), 'manage' => __( 'Manage', 'admin-page-framework' ), 'select_image' => __( 'Select Image', 'admin-page-framework' ), 'upload_file' => __( 'Upload File', 'admin-page-framework' ), 'use_this_file' => __( 'Use This File', 'admin-page-framework' ), 'select_file' => __( 'Select File', 'admin-page-framework' ), 'queries_in_seconds' => __( '%s queries in %s seconds.', 'admin-page-framework' ), 'out_of_x_memory_used' => __( '%s out of %s MB (%s) memory used.', 'admin-page-framework' ), 'peak_memory_usage' => __( 'Peak memory usage %s MB.', 'admin-page-framework' ), 'initial_memory_usage' => __( 'Initial memory usage  %s MB.', 'admin-page-framework' ), 'allowed_maximum_number_of_fields' => __( 'The allowed maximum number of fields is {0}.', 'admin-page-framework' ), 'allowed_minimum_number_of_fields' => __( 'The allowed minimum number of fields is {0}.', 'admin-page-framework' ), 'add' => __( 'Add', 'admin-page-framework' ), 'remove' => __( 'Remove', 'admin-page-framework' ), 'allowed_maximum_number_of_sections' => __( 'The allowed maximum number of sections is (0)', 'admin-page-framework' ), 'allowed_minimum_number_of_sections' => __( 'The allowed minimum number of sections is (0)', 'admin-page-framework' ), 'add_section' => __( 'Add Section' ), 'remove_section' => __( 'Remove Section' ), ); } public function __( $sKey ) { return isset( $this->aMessages[ $sKey ] ) ? __( $this->aMessages[ $sKey ], $this->_sTextDomain ) : ''; } public function _e( $sKey ) { if ( isset( $this->aMessages[ $sKey ] ) ) _e( $this->aMessages[ $sKey ], $this->_sTextDomain ); } } endif;if ( ! class_exists( 'AdminPageFramework_Property_Base' ) ) : abstract class AdminPageFramework_Property_Base { private static $_aStructure_CallerInfo = array( 'sPath' => null, 'sType' => null, 'sName' => null, 'sURI' => null, 'sVersion' => null, 'sThemeURI' => null, 'sScriptURI' => null, 'sAuthorURI' => null, 'sAuthor' => null, 'sDescription' => null, ); static public $_aLibraryData; protected $oCaller; public $sCallerPath; public $aScriptInfo; public $sClassName; public $sClassHash; public $sScript = ''; public $sStyle = ''; public $sStyleIE = ''; public $_bAddedStyle = false; public $_bAddedScript = false; public $aFieldTypeDefinitions = array(); public static $_sDefaultScript = ""; public static $_sDefaultStyle = "
		/* Settings Notice */
		.wrap div.updated, 
		.wrap div.settings-error { 
			clear: both; 
			margin-top: 16px;
		} 		
				
		/* Contextual Help Page */
		.contextual-help-description {
			clear: left;	
			display: block;
			margin: 1em 0;
		}
		.contextual-help-tab-title {
			font-weight: bold;
		}
		
		/* Page Meta Boxes */
		.admin-page-framework-content {
			margin-bottom: 1.48em;		
			display: inline-table;	/* Fixes the bottom margin gets placed at the top. */
/* display: block; */
			width: 100%;	/* This allows float:right elements to go to the very right end of the page. */
		}
		
		/* Heading - the meta box container element affects the styles of regular main content output. So it needs to be fixed. */
		#poststuff .admin-page-framework-content h3 {
			font-weight: bold;
			font-size: 1.3em;
			margin: 1em 0;
			padding: 0;
			font-family: 'Open Sans', sans-serif;
		}
		
		/* Form Elements */
		/* Section Table */
		.admin-page-framework-section .form-table {
			margin-top: 0;
		}
		.admin-page-framework-section .form-table td label {
		   	display: inline;  /* adjusts the horizontal alignment with the th element */
		}
		/* Section Tabs */
		.admin-page-framework-section-tabs-contents {
			margin-top: 1em;
		}
		.admin-page-framework-section-tabs {	/* The section tabs' container */
			margin: 0;
		}
		.admin-page-framework-tab-content {		/* each section including sub-sections of repeatable fields */
			padding: 0.5em 2em 1.5em 2em;
			margin: 0;
			border-style: solid;
			border-width: 1px;
			border-color: #dfdfdf;
			background-color: #fdfdfd;				
			
		}
		.admin-page-framework-section-tab {
			background-color: transparent;
			vertical-align: bottom;	/* for Firefox */
		}
		.admin-page-framework-section-tab.active {
			background-color: #fdfdfd;			
		}
		.admin-page-framework-section-tab h4 {
			margin: 0;
			padding: 8px 14px 10px;
			font-size: 1.2em;
		}
		.admin-page-framework-section-tab.nav-tab {
			padding: 0;
		}
		.admin-page-framework-section-tab.nav-tab a {
			text-decoration: none;
			color: #464646;
			vertical-align: inherit; /* for Firefox - without this tiny dots appear */
		}
		.admin-page-framework-section-tab.nav-tab.active a {
			color: #000;
		}
		/* Repeatable Sections */
		.admin-page-framework-repeatable-section-buttons {
			float: right;
		}
		/* Section Caption */
		.admin-page-framework-section-caption {
			text-align: left;
			margin: 1em 0;
		}
		/* Section Title */
		.admin-page-framework-section .admin-page-framework-section-title {
			background: none;
			-webkit-box-shadow: none;
			box-shadow: none;
		}
		/* Metabox Section Heading Info */
		#poststuff .metabox-holder .admin-page-framework-section-title h3{
			border: none;
			font-weight: bold;
			font-size: 1.3em;
			margin: 1em 0;
			padding: 0;
			font-family: 'Open Sans', sans-serif;		
			cursor: inherit;			
			-webkit-user-select: inherit;
			-moz-user-select: inherit;
			user-select: inherit;	

			/* v3.5 or below */
			text-shadow: none;
			-webkit-box-shadow: none;
			box-shadow: none;
			background: none;
		}
		
		/* Fields Container */
		.admin-page-framework-fields {
			display: table;	/* the block property does not give the element the solid height */
			width: 100%;
		}
		
		/* Disabled */
		.admin-page-framework-fields .disabled,
		.admin-page-framework-fields .disabled input,
		.admin-page-framework-fields .disabled textarea,
		.admin-page-framework-fields .disabled select,
		.admin-page-framework-fields .disabled option {
			color: #BBB;
		}
		
		/* HR */
		.admin-page-framework-fields hr {
			border: 0; 
			height: 0;
			border-top: 1px solid #dfdfdf; 
		}
		
		/* Delimiter */
		.admin-page-framework-fields .delimiter {
			display: inline;
		}
		
		/* Description */
		.admin-page-framework-fields-description {
			margin-bottom: 0;
		}
		/* Field Container */
		.admin-page-framework-field {
			float: left;
			clear: both;
			display: inline-block;
			margin: 1px 0;
		}
		.admin-page-framework-field label{
			display: inline-block;	/* for WordPress v3.7.x or below */
			width: 100%;
		}
		.admin-page-framework-field .admin-page-framework-input-label-container {
			margin-bottom: 0.25em;
		}
		@media only screen and ( max-width: 780px ) {	/* For WordPress v3.8 or greater */
			.admin-page-framework-field .admin-page-framework-input-label-container {
				margin-bottom: 0.5em;
			}
		}			
		
		.admin-page-framework-field .admin-page-framework-input-label-string {
			padding-right: 1em;	/* for checkbox label strings, a right padding is needed */
		}
		.admin-page-framework-field .admin-page-framework-input-button-container {
			padding-right: 1em; 
		}
		.admin-page-framework-field .admin-page-framework-input-container {
			display: inline-block;
			vertical-align: middle;
		}
		.admin-page-framework-field-image .admin-page-framework-input-label-container {			
			vertical-align: middle;
		}
		
		.admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field .admin-page-framework-input-label-string
		{
			display: inline-block;		
			vertical-align: middle; 
		}
		
		/* Repeatable Fields */		
		.repeatable .admin-page-framework-field {
			clear: both;
			display: block;
		}
		.admin-page-framework-repeatable-field-buttons {
			float: right;		
			margin: 0.1em 0 0.5em 0.3em;
			vertical-align: middle;
		}
		.admin-page-framework-repeatable-field-buttons .repeatable-field-button {
			margin: 0 0.1em;
			font-weight: normal;
			vertical-align: middle;
			text-align: center;
		}

		/* Sortable Fields */
		.sortable .admin-page-framework-field {
			clear: both;
			float: left;
			display: inline-block;
			padding: 1em 1.2em 0.72em;
			margin: 1px 0 0 0;
			border-top-width: 1px;
			border-bottom-width: 1px;
			border-bottom-style: solid;
			-webkit-user-select: none;
			-moz-user-select: none;
			user-select: none;			
			text-shadow: #fff 0 1px 0;
			-webkit-box-shadow: 0 1px 0 #fff;
			box-shadow: 0 1px 0 #fff;
			-webkit-box-shadow: inset 0 1px 0 #fff;
			box-shadow: inset 0 1px 0 #fff;
			-webkit-border-radius: 3px;
			border-radius: 3px;
			background: #f1f1f1;
			background-image: -webkit-gradient(linear, left bottom, left top, from(#ececec), to(#f9f9f9));
			background-image: -webkit-linear-gradient(bottom, #ececec, #f9f9f9);
			background-image:    -moz-linear-gradient(bottom, #ececec, #f9f9f9);
			background-image:      -o-linear-gradient(bottom, #ececec, #f9f9f9);
			background-image: linear-gradient(to top, #ececec, #f9f9f9);
			border: 1px solid #CCC;
			background: #F6F6F6;	
		}		
		.admin-page-framework-fields.sortable {
			margin-bottom: 1.2em;	/* each sortable field does not have a margin bottom so this rule gives a margin between the fields and the description */
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
		}"; public static $_sDefaultStyleIE = ''; public $aEnqueuingScripts = array(); public $aEnqueuingStyles = array(); public $iEnqueuedScriptIndex = 0; public $iEnqueuedStyleIndex = 0; public $bIsAdmin; public $bIsMinifiedVersion; public $sCapability; public $sFieldsType; public $sTextDomain; function __construct( $oCaller, $sCallerPath, $sClassName, $sCapability, $sTextDomain, $sFieldsType ) { $this->oCaller = $oCaller; $this->sCallerPath = $sCallerPath ? $sCallerPath : AdminPageFramework_Utility::getCallerScriptPath( __FILE__ ); $this->sClassName = $sClassName; $this->sClassHash = md5( $sClassName ); $this->sCapability = empty( $sCapability ) ? 'manage_options' : $sCapability ; $this->sTextDomain = empty( $sTextDomain ) ? 'admin-page-framework' : $sTextDomain; $this->sFieldsType = $sFieldsType; $this->aScriptInfo = $this->getCallerInfo( $this->sCallerPath ); $GLOBALS['aAdminPageFramework'] = isset( $GLOBALS['aAdminPageFramework'] ) && is_array( $GLOBALS['aAdminPageFramework'] ) ? $GLOBALS['aAdminPageFramework'] : array( 'aFieldFlags' => array() ); $this->bIsAdmin = is_admin(); $this->bIsMinifiedVersion = ! class_exists( 'AdminPageFramework_Bootstrap' ); if ( ! isset( self::$_aLibraryData ) ) { $_sLibraryMainClassName = ( $this->bIsMinifiedVersion ) ? 'AdminPageFramework' : 'AdminPageFramework_Bootstrap'; $oRC = new ReflectionClass( $_sLibraryMainClassName ); self::_setLibraryData( $oRC->getFileName() ); } } public function _getCallerObject() { return $this->oCaller; } static public function _setLibraryData( $sLibraryFilePath ) { self::$_aLibraryData = AdminPageFramework_WPUtility::getScriptData( $sLibraryFilePath, 'library' ); } static public function _getLibraryData( $sLibraryFilePath=null ) { if ( isset( self::$_aLibraryData ) ) return self::$_aLibraryData; if ( $sLibraryFilePath ) self::_setLibraryData( $sLibraryFilePath ); return self::$_aLibraryData; } protected function getCallerInfo( $sCallerPath=null ) { $aCallerInfo = self::$_aStructure_CallerInfo; $aCallerInfo['sPath'] = $sCallerPath; $aCallerInfo['sType'] = $this->_getCallerType( $aCallerInfo['sPath'] ); if ( $aCallerInfo['sType'] == 'unknown' ) return $aCallerInfo; if ( $aCallerInfo['sType'] == 'plugin' ) return AdminPageFramework_WPUtility::getScriptData( $aCallerInfo['sPath'], $aCallerInfo['sType'] ) + $aCallerInfo; if ( $aCallerInfo['sType'] == 'theme' ) { $oTheme = wp_get_theme(); return array( 'sName' => $oTheme->Name, 'sVersion' => $oTheme->Version, 'sThemeURI' => $oTheme->get( 'ThemeURI' ), 'sURI' => $oTheme->get( 'ThemeURI' ), 'sAuthorURI' => $oTheme->get( 'AuthorURI' ), 'sAuthor' => $oTheme->get( 'Author' ), ) + $aCallerInfo; } } private function _getCallerType( $sScriptPath ) { if ( preg_match( '/[\/\\\\]themes[\/\\\\]/', $sScriptPath, $m ) ) return 'theme'; if ( preg_match( '/[\/\\\\]plugins[\/\\\\]/', $sScriptPath, $m ) ) return 'plugin'; return 'unknown'; } public function isPostDefinitionPage( $asPostTypes=array() ) { $_aPostTypes = ( array ) $asPostTypes; if ( ! in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) ) return false; if ( empty( $_aPostTypes ) ) return true; if ( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $_aPostTypes ) ) return true; $this->_sCurrentPostType = isset( $this->_sCurrentPostType ) ? $this->_sCurrentPostType : ( isset( $_GET['post'] ) ? get_post_type( $_GET['post'] ) : '' ); if ( isset( $_GET['post'], $_GET['action'] ) && in_array( $this->_sCurrentPostType, $_aPostTypes ) ) return true; return false; } } endif;if ( ! class_exists( 'AdminPageFramework_Property_MetaBox' ) ) : class AdminPageFramework_Property_MetaBox extends AdminPageFramework_Property_Base { public $_sPropertyType = 'post_meta_box'; public $sMetaBoxID =''; public $sTitle = ''; public $aPostTypes = array(); public $aPages = array(); public $sContext = 'normal'; public $sPriority = 'default'; public $sClassName = ''; public $sCapability = 'edit_posts'; public $aOptions = array(); public $sThickBoxTitle = ''; public $sThickBoxButtonUseThis = ''; public $aHelpTabText = array(); public $aHelpTabTextSide = array(); public $sFieldsType = 'post_meta_box'; function __construct( $oCaller, $sClassName, $sCapability='edit_posts', $sTextDomain='admin-page-framework', $sFieldsType='post_meta_box' ) { parent::__construct( $oCaller, null, $sClassName, $sCapability, $sTextDomain, $sFieldsType ); } } endif;if ( ! class_exists( 'AdminPageFramework_Property_Page' ) ) : class AdminPageFramework_Property_Page extends AdminPageFramework_Property_Base { public $_sPropertyType = 'page'; public $sClassName; public $sClassHash; public $sCapability = 'manage_options'; public $sPageHeadingTabTag = 'h2'; public $sInPageTabTag = 'h3'; public $sDefaultPageSlug; public $aPages = array(); public $aHiddenPages = array(); public $aRegisteredSubMenuPages = array(); public $aRootMenu = array( 'sTitle' => null, 'sPageSlug' => null, 'sIcon16x16' => null, 'iPosition' => null, 'fCreateRoot' => null, ); public $aInPageTabs = array(); public $aDefaultInPageTabs = array(); public $aPluginDescriptionLinks = array(); public $aPluginTitleLinks = array(); public $aFooterInfo = array( 'sLeft' => '', 'sRight' => '', ); public $sOptionKey = ''; public $aHelpTabs = array(); public $sFormEncType = 'multipart/form-data'; public $sThickBoxButtonUseThis = ''; public $bEnableForm = false; public $bShowPageTitle = true; public $bShowPageHeadingTabs = true; public $bShowInPageTabs = true; public $aAdminNotices = array(); public $aDisallowedQueryKeys = array( 'settings-updated' ); public function __construct( $oCaller, $sCallerPath, $sClassName, $sOptionKey, $sCapability='manage_options', $sTextDomain='admin-page-framework', $sFieldsType='page' ) { parent::__construct( $oCaller, $sCallerPath, $sClassName, $sCapability, $sTextDomain, $sFieldsType ); $this->sOptionKey = $sOptionKey ? $sOptionKey : $sClassName; $GLOBALS['aAdminPageFramework']['aPageClasses'] = isset( $GLOBALS['aAdminPageFramework']['aPageClasses'] ) && is_array( $GLOBALS['aAdminPageFramework']['aPageClasses'] ) ? $GLOBALS['aAdminPageFramework']['aPageClasses'] : array(); $GLOBALS['aAdminPageFramework']['aPageClasses'][ $sClassName ] = $oCaller; add_filter( "option_page_capability_{$this->sOptionKey}", array( $this, '_replyToGetCapability' ) ); } protected function _isAdminPage() { if ( ! is_admin() ) { return false; } if ( in_array( $GLOBALS['pagenow'], array( 'options.php' ) ) ) { return true; } return isset( $_GET['page'] ); } public function &__get( $sName ) { if ( $sName == 'aOptions' ) { $this->aOptions = get_option( $this->sOptionKey, array() ); return $this->aOptions; } return null; } public function isPageAdded( $sPageSlug='' ) { $sPageSlug = $sPageSlug ? $sPageSlug : ( isset( $_GET['page'] ) ? $_GET['page'] : '' ); return ( array_key_exists( trim( $sPageSlug ), $this->aPages ) ) ? true : false; } public function getCurrentTab() { if ( isset( $_GET['tab'] ) && $_GET['tab'] ) return $_GET['tab']; return isset( $_GET['page'] ) && $_GET['page'] ? $this->getDefaultInPageTab( $_GET['page'] ) : null; } public function getDefaultInPageTab( $sPageSlug ) { if ( ! $sPageSlug ) return ''; return isset( $this->aDefaultInPageTabs[ $sPageSlug ] ) ? $this->aDefaultInPageTabs[ $sPageSlug ] : ''; } public function getDefaultOptions( $aFields ) { $_aDefaultOptions = array(); foreach( $aFields as $_sSectionID => $_aFields ) { foreach( $_aFields as $_sFieldID => $_aField ) { $_vDefault = $this->_getDefautValue( $_aField ); if ( isset( $_aField['section_id'] ) && $_aField['section_id'] != '_default' ) $_aDefaultOptions[ $_aField['section_id'] ][ $_sFieldID ] = $_vDefault; else $_aDefaultOptions[ $_sFieldID ] = $_vDefault; } } return $_aDefaultOptions; } private function _getDefautValue( $aField ) { $_aSubFields = AdminPageFramework_Utility::getIntegerElements( $aField ); if ( count( $_aSubFields ) == 0 ) { $_aField = $aField; return isset( $_aField['value'] ) ? $_aField['value'] : ( isset( $_aField['default'] ) ? $_aField['default'] : null ); } $_aDefault = array(); array_unshift( $_aSubFields, $aField ); foreach( $_aSubFields as $_iIndex => $_aField ) $_aDefault[ $_iIndex ] = isset( $_aField['value'] ) ? $_aField['value'] : ( isset( $_aField['default'] ) ? $_aField['default'] : null ); return $_aDefault; } public function _replyToGetCapability() { return $this->sCapability; } } endif;if ( ! class_exists( 'AdminPageFramework_Property_PostType' ) ) : class AdminPageFramework_Property_PostType extends AdminPageFramework_Property_Base { public $_sPropertyType = 'post_type'; public $sPostType = ''; public $aPostTypeArgs = array(); public $sClassName = ''; public $aColumnHeaders = array( 'cb' => '<input type="checkbox" />', 'title' => 'Title', 'author' => 'Author', 'comments' => '<div class="comment-grey-bubble"></div>', 'date' => 'Date', ); public $aColumnSortable = array( 'title' => true, 'date' => true, ); public $sCallerPath = ''; public $aTaxonomies; public $aTaxonomyTableFilters = array(); public $aTaxonomyRemoveSubmenuPages = array(); public $bEnableAutoSave = true; public $bEnableAuthorTableFileter = false; } endif;if ( ! class_exists( 'AdminPageFramework_Property_MetaBox_Page' ) ) : class AdminPageFramework_Property_MetaBox_Page extends AdminPageFramework_Property_MetaBox { public $_sPropertyType = 'page_meta_box'; public $aPageSlugs = array(); public $oAdminPage; public $aHelpTabs = array(); function __construct( $oCaller, $sClassName, $sCapability='manage_options', $sTextDomain='admin-page-framework', $sFieldsType='page_meta_box' ) { add_action( 'admin_menu', array( $this, '_replyToSetUpProperties' ), 100 ); parent::__construct( $oCaller, $sClassName, $sCapability, $sTextDomain, $sFieldsType ); $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] = isset( $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] ) && is_array( $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] ) ? $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] : array(); $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'][ $sClassName ] = $oCaller; } public function _replyToSetUpProperties() { if ( ! isset( $_GET['page'] ) ) return; $this->oAdminPage = $this->_getOwnerClass( $_GET['page'] ); if ( ! $this->oAdminPage ) return; $this->aHelpTabs = $this->oAdminPage->oProp->aHelpTabs; $this->oAdminPage->oProp->bEnableForm = true; $this->aOptions = $this->oAdminPage->oProp->aOptions; } public function _getScreenIDOfPage( $sPageSlug ) { return ( $oAdminPage = $this->_getOwnerClass( $sPageSlug ) ) ? $oAdminPage->oProp->aPages[ $sPageSlug ]['_page_hook'] : ''; } public function isPageAdded( $sPageSlug='' ) { return ( $oAdminPage = $this->_getOwnerClass( $sPageSlug ) ) ? $oAdminPage->oProp->isPageAdded( $sPageSlug ) : false; } public function isCurrentTab( $sTabSlug ) { $sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : ''; if ( ! $sCurrentPageSlug ) return false; $sCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->getDefaultInPageTab( $sCurrentPageSlug ); return ( $sTabSlug == $sCurrentTabSlug ); } public function getDefaultInPageTab( $sPageSlug ) { if ( ! $sPageSlug ) return ''; return ( $oAdminPage = $this->_getOwnerClass( $sPageSlug ) ) ? $oAdminPage->oProp->getDefaultInPageTab( $sPageSlug ) : ''; } public function getOptionKey( $sPageSlug ) { if ( ! $sPageSlug ) return ''; return ( $oAdminPage = $this->_getOwnerClass( $sPageSlug ) ) ? $oAdminPage->oProp->sOptionKey : ''; } private function _getOwnerClass( $sPageSlug ) { if ( ! isset( $GLOBALS['aAdminPageFramework']['aPageClasses'] ) ) return null; if ( ! is_array( $GLOBALS['aAdminPageFramework']['aPageClasses'] ) ) return null; foreach( $GLOBALS['aAdminPageFramework']['aPageClasses'] as $oClass ) if ( $oClass->oProp->isPageAdded( $sPageSlug ) ) return $oClass; return null; } } endif;if ( ! class_exists( 'AdminPageFramework_Property_TaxonomyField' ) ) : class AdminPageFramework_Property_TaxonomyField extends AdminPageFramework_Property_MetaBox { public $_sPropertyType = 'taxonomy_field'; public $aTaxonomySlugs; public $sOptionKey; } endif;if ( ! class_exists( 'AdminPageFramework_FieldTypeRegistration' ) ) : class AdminPageFramework_FieldTypeRegistration { protected static $aDefaultFieldTypeSlugs = array( 'default', 'text', 'number', 'textarea', 'radio', 'checkbox', 'select', 'hidden', 'file', 'submit', 'import', 'export', 'image', 'media', 'color', 'taxonomy', 'posttype', 'size', 'section_title', ); function __construct( &$aFieldTypeDefinitions, $sExtendedClassName, $oMsg ) { $_aFieldTypeDefinitions = array(); foreach( self::$aDefaultFieldTypeSlugs as $sFieldTypeSlug ) { $_sInstantiatingClassName = "AdminPageFramework_FieldType_{$sFieldTypeSlug}"; if ( ! class_exists( $_sInstantiatingClassName ) ) continue; $_oFieldType = new $_sInstantiatingClassName( $sExtendedClassName, null, $oMsg, false ); foreach( $_oFieldType->aFieldTypeSlugs as $__sSlug ) { $_aFieldTypeDefinitions[ $__sSlug ] = $_oFieldType->getDefinitionArray(); } } $aFieldTypeDefinitions = $_aFieldTypeDefinitions; } static public function _setFieldHeadTagElements( array $aField, $oProp, $oHeadTag ) { $sFieldType = $aField['type']; static $aLoadFlags = array(); $aLoadFlags[ $oProp->_sPropertyType ] = isset( $aLoadFlags[ $oProp->_sPropertyType ] ) && is_array( $aLoadFlags[ $oProp->_sPropertyType ] ) ? $aLoadFlags[ $oProp->_sPropertyType ] : array(); if ( isset( $aLoadFlags[ $oProp->_sPropertyType ][ $sFieldType ] ) && $aLoadFlags[ $oProp->_sPropertyType ][ $sFieldType ] ) return; $aLoadFlags[ $oProp->_sPropertyType ][ $sFieldType ] = true; if ( ! isset( $oProp->aFieldTypeDefinitions[ $sFieldType ] ) ) return; if ( is_callable( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfFieldSetTypeSetter'] ) ) call_user_func_array( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfFieldSetTypeSetter'], array( $oProp->_sPropertyType ) ); if ( is_callable( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfFieldLoader'] ) ) call_user_func_array( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfFieldLoader'], array() ); if ( is_callable( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetScripts'] ) ) $oProp->sScript .= call_user_func_array( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetScripts'], array() ); if ( is_callable( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetStyles'] ) ) $oProp->sStyle .= call_user_func_array( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetStyles'], array() ); if ( is_callable( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetIEStyles'] ) ) $oProp->sStyleIE .= call_user_func_array( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetIEStyles'], array() ); foreach( $oProp->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueStyles'] as $asSource ) { if ( is_string( $asSource ) ) $oHeadTag->_forceToEnqueueStyle( $asSource ); else if ( is_array( $asSource ) && isset( $asSource[ 'src' ] ) ) $oHeadTag->_forceToEnqueueStyle( $asSource[ 'src' ], $asSource ); } foreach( $oProp->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueScripts'] as $asSource ) { if ( is_string( $asSource ) ) $oHeadTag->_forceToEnqueueScript( $asSource ); else if ( is_array( $asSource ) && isset( $asSource[ 'src' ] ) ) $oHeadTag->_forceToEnqueueScript( $asSource[ 'src' ], $asSource ); } } } endif;if ( ! class_exists( 'AdminPageFramework_WalkerTaxonomyChecklist' ) ) : class AdminPageFramework_WalkerTaxonomyChecklist extends Walker_Category { function start_el( &$sOutput, $oCategory, $iDepth=0, $aArgs=array(), $iCurrentObjectID=0 ) { $aArgs = $aArgs + array( 'name' => null, 'disabled' => null, 'selected' => array(), 'input_id' => null, 'attributes' => array(), 'taxonomy' => null, ); $iID = $oCategory->term_id; $sTaxonomySlug = empty( $aArgs['taxonomy'] ) ? 'category' : $aArgs['taxonomy']; $sID = "{$aArgs['input_id']}_{$sTaxonomySlug}_{$iID}"; $aInputAttributes = isset( $aInputAttributes[ $iID ] ) ? $aInputAttributes[ $iID ] + $aArgs['attributes'] : $aArgs['attributes']; $aInputAttributes = array( 'id' => $sID, 'value' => 1, 'type' => 'checkbox', 'name' => "{$aArgs['name']}[{$iID}]", 'checked' => in_array( $iID, ( array ) $aArgs['selected'] ) ? 'Checked' : '', ) + $aInputAttributes; $sOutput .= "\n" . "<li id='list-{$sID}' class='category-list'>" . "<label for='{$sID}' class='taxonomy-checklist-label'>" . "<input value='0' type='hidden' name='{$aArgs['name']}[{$iID}]' />" . "<input " . AdminPageFramework_WPUtility::generateAttributes( $aInputAttributes ) . " />" . esc_html( apply_filters( 'the_category', $oCategory->name ) ) . "</label>"; } } endif;if ( ! class_exists( 'AdminPageFramework_PageLoadInfo_Base' ) ) : abstract class AdminPageFramework_PageLoadInfo_Base { function __construct( $oProp, $oMsg ) { if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) { $this->oProp = $oProp; $this->oMsg = $oMsg; $this->_nInitialMemoryUsage = memory_get_usage(); add_action( 'admin_menu', array( $this, '_replyToSetPageLoadInfoInFooter' ), 999 ); } } public function _replyToSetPageLoadInfoInFooter() {} public function _replyToGetPageLoadInfo( $sFooterHTML ) { $_nSeconds = timer_stop( 0 ); $_nQueryCount = get_num_queries(); $_nMemoryUsage = round( $this->_convertBytesToHR( memory_get_usage() ), 2 ); $_nMemoryPeakUsage = round( $this->_convertBytesToHR( memory_get_peak_usage() ), 2 ); $_nMemoryLimit = round( $this->_convertBytesToHR( $this->_convertToNumber( WP_MEMORY_LIMIT ) ), 2 ); $_sInitialMemoryUsage = round( $this->_convertBytesToHR( $this->_nInitialMemoryUsage ), 2 ); return $sFooterHTML . "<div id='admin-page-framework-page-load-stats'>" . "<ul>" . "<li>" . sprintf( $this->oMsg->__( 'queries_in_seconds' ), $_nQueryCount, $_nSeconds ) . "</li>" . "<li>" . sprintf( $this->oMsg->__( 'out_of_x_memory_used' ), $_nMemoryUsage, $_nMemoryLimit, round( ( $_nMemoryUsage / $_nMemoryLimit ), 2 ) * 100 . '%' ) . "</li>" . "<li>" . sprintf( $this->oMsg->__( 'peak_memory_usage' ), $_nMemoryPeakUsage ) . "</li>" . "<li>" . sprintf( $this->oMsg->__( 'initial_memory_usage' ), $_sInitialMemoryUsage ) . "</li>" . "</ul>" . "</div>"; } private function _convertToNumber( $nSize ) { $_nReturn = substr( $nSize, 0, -1 ); switch( strtoupper( substr( $nSize, -1 ) ) ) { case 'P': $_nReturn *= 1024; case 'T': $_nReturn *= 1024; case 'G': $_nReturn *= 1024; case 'M': $_nReturn *= 1024; case 'K': $_nReturn *= 1024; } return $_nReturn; } private function _convertBytesToHR( $nBytes ) { $_aUnits = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' ); $_nLog = log( $nBytes, 1024 ); $_iPower = ( int ) $_nLog; $_iSize = pow( 1024, $_nLog - $_iPower ); return $_iSize . $_aUnits[ $_iPower ]; } } endif;if ( ! class_exists( 'AdminPageFramework_PageLoadInfo_Page' ) ) : class AdminPageFramework_PageLoadInfo_Page extends AdminPageFramework_PageLoadInfo_Base { private static $_oInstance; private static $aClassNames = array(); public static function instantiate( $oProp, $oMsg ) { if ( in_array( $oProp->sClassName, self::$aClassNames ) ) return self::$_oInstance; self::$aClassNames[] = $oProp->sClassName; self::$_oInstance = new AdminPageFramework_PageLoadInfo_Page( $oProp, $oMsg ); return self::$_oInstance; } public function _replyToSetPageLoadInfoInFooter() { $_sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : ''; if ( $this->oProp->isPageAdded( $_sCurrentPageSlug ) ) add_filter( 'update_footer', array( $this, '_replyToGetPageLoadInfo' ), 999 ); } } endif;if ( ! class_exists( 'AdminPageFramework_PageLoadInfo_PostType' ) ) : class AdminPageFramework_PageLoadInfo_PostType extends AdminPageFramework_PageLoadInfo_Base { private static $_oInstance; private static $aClassNames = array(); public static function instantiate( $oProp, $oMsg ) { if ( in_array( $oProp->sClassName, self::$aClassNames ) ) return self::$_oInstance; self::$aClassNames[] = $oProp->sClassName; self::$_oInstance = new AdminPageFramework_PageLoadInfo_PostType( $oProp, $oMsg ); return self::$_oInstance; } public function _replyToSetPageLoadInfoInFooter() { if ( isset( $_GET['page'] ) && $_GET['page'] ) return; if ( isset( $_GET['post_type'], $this->oProp->sPostType ) && $_GET['post_type'] == $this->oProp->sPostType || AdminPageFramework_WPUtility::isPostDefinitionPage( $this->oProp->sPostType ) ) add_filter( 'update_footer', array( $this, '_replyToGetPageLoadInfo' ), 999 ); } } endif;if ( ! class_exists( 'AdminPageFramework_Script_AttributeUpdator' ) ) : class AdminPageFramework_Script_AttributeUpdator { static public function getjQueryPlugin() { return "(function ( $ ) {
		
			/**
			 * Increments a first/last found digit with the prefix of underscore in a specified attribute value.
			 * if the bFirstOccurence is false, the last found one will be replaced.
			 */
			$.fn.incrementIDAttribute = function( sAttribute, bFirstOccurence ) {				
				return this.attr( sAttribute, function( iIndex, sValue ) {	
					return updateID( iIndex, sValue, 1, bFirstOccurence );
				}); 
			};
			/**
			 * Increments a first/last found digit enclosed in [] in a specified attribute value.
			 */
			$.fn.incrementNameAttribute = function( sAttribute, bFirstOccurence ) {				
				return this.attr( sAttribute, function( iIndex, sValue ) {	
					return updateName( iIndex, sValue, 1, bFirstOccurence );
				}); 
			};
	
			/**
			 * Decrements a first/last found digit with the prefix of underscore in a specified attribute value.
			 */
			$.fn.decrementIDAttribute = function( sAttribute, bFirstOccurence ) {
				return this.attr( sAttribute, function( iIndex, sValue ) {
					return updateID( iIndex, sValue, -1, bFirstOccurence );
				}); 
			};			
			/**
			 * Decrements a first/last found digit enclosed in [] in a specified attribute value.
			 */
			$.fn.decrementNameAttribute = function( sAttribute, bFirstOccurence ) {
				return this.attr( sAttribute, function( iIndex, sValue ) {
					return updateName( iIndex, sValue, -1, bFirstOccurence );
				}); 
			};				
			
			/* Sets the current index to the ID attribute. Used for sortable fields. */
			$.fn.setIndexIDAttribute = function( sAttribute, iIndex, bFirstOccurence ){
				return this.attr( sAttribute, function( i, sValue ) {
					return updateID( iIndex, sValue, 0, bFirstOccurence );
				});
			};
			/* Sets the current index to the name attribute. Used for sortable fields. */
			$.fn.setIndexNameAttribute = function( sAttribute, iIndex, bFirstOccurence ){
				return this.attr( sAttribute, function( i, sValue ) {
					return updateName( iIndex, sValue, 0, bFirstOccurence );
				});
			};		
			
			/* Local Function Literals */	
			var updateID = function( iIndex, sID, bIncrement, bFirstOccurence ) {
				if ( typeof sID === 'undefined' ) return sID;
				var sNeedlePrefix = ( typeof bFirstOccurence === 'undefined' ) || ! bFirstOccurence ? '(.+)': '(.+?)';
				var sNeedle = new RegExp( sNeedlePrefix + '__(\\\d+)(?=(_|$))' );	// triple escape - not sure why but on a separate test script, double escape was working
				return sID.replace( sNeedle, function ( sFullMatch, m0, m1 ) {
					if ( bIncrement === 1 )
						return m0 + '__' + ( Number( m1 ) + 1 );
					else if ( bIncrement === -1 )
						return m0 + '__' + ( Number( m1 ) - 1 );
					else 
						return m0 + '__' + ( iIndex );
				});
			}
			var updateName = function( iIndex, sName, bIncrement, biFirstOccurence ) {
				if ( typeof sName === 'undefined' ) return sName;
				var sNeedlePrefix = ( typeof biFirstOccurence === 'undefined' ) || ! biFirstOccurence ? '(.+)': '(.+?)';
				var sNeedle = biFirstOccurence === -1	
					? new RegExp( '(.+)' + '\\\[(\\\d+)(?=\\\].+\\\[\\\d+(?=\\\]))' )	// -1 is for the second occurrence from the last; for taxonomy field type
					: new RegExp( sNeedlePrefix + '\\\[(\\\d+)(?=\\\])' );	// triple escape - not sure why but on a separate test script, double escape was working
				return sName.replace( sNeedle, function ( sFullMatch, m0, m1 ) {
								
					if ( bIncrement === 1 )
						return m0 + '[' + ( Number( m1 ) + 1 );
					else if ( bIncrement === -1 )
						return m0 + '[' + ( Number( m1 ) - 1 );
					else 
						return m0 + '[' + ( iIndex );
				});
			}
				
		}( jQuery ));"; } } endif;if ( ! class_exists( 'AdminPageFramework_Script_RegisterCallback' ) ) : class AdminPageFramework_Script_RegisterCallback { static public function getjQueryPlugin() { return "(function ( $ ) {
						
			// The method that gets triggered when a repeatable field add button is pressed.
			$.fn.callBackAddRepeatableField = function( sFieldType, sID, iCallType ) {
				var nodeThis = this;
				if ( ! $.fn.aAPFAddRepeatableFieldCallbacks ) $.fn.aAPFAddRepeatableFieldCallbacks = [];
				$.fn.aAPFAddRepeatableFieldCallbacks.forEach( function( hfCallback ) {
					if ( jQuery.isFunction( hfCallback ) ) hfCallback( nodeThis, sFieldType, sID, iCallType );
				});
			};
			
			// The method that gets triggered when a repeatable field remove button is pressed.
			$.fn.callBackRemoveRepeatableField = function( sFieldType, sID, iCallType ) {
				var nodeThis = this;
				if ( ! $.fn.aAPFRemoveRepeatableFieldCallbacks ) $.fn.aAPFRemoveRepeatableFieldCallbacks = [];
				$.fn.aAPFRemoveRepeatableFieldCallbacks.forEach( function( hfCallback ) {
					if ( jQuery.isFunction( hfCallback ) ) hfCallback( nodeThis, sFieldType, sID, iCallType );
				});
			};

			// The method that gets triggered when a sortable field is dropped and the sort event occurred
			$.fn.callBackSortedFields = function( sFieldType, sID, iCallType ) {
				var nodeThis = this;
				if ( ! $.fn.aAPFSortedFieldsCallbacks ) $.fn.aAPFSortedFieldsCallbacks = [];
				$.fn.aAPFSortedFieldsCallbacks.forEach( function( hfCallback ) {
					if ( jQuery.isFunction( hfCallback ) ) hfCallback( nodeThis, sFieldType, sID, iCallType );
				});
			};
			
			// The method that registers callbacks. This will be called in field type definition class.
			$.fn.registerAPFCallback = function( oOptions ) {
				
				// This is the easiest way to have default options.
				var oSettings = $.extend({
					// The user specifies the settings with the following options.
					added_repeatable_field: function() {},
					removed_repeatable_field: function() {},
					sorted_fields: function() {},
				}, oOptions );

				// Set up arrays to store callback functions
				if( ! $.fn.aAPFAddRepeatableFieldCallbacks ) $.fn.aAPFAddRepeatableFieldCallbacks = [];
				if( ! $.fn.aAPFRemoveRepeatableFieldCallbacks ) $.fn.aAPFRemoveRepeatableFieldCallbacks = [];
				if( ! $.fn.aAPFSortedFieldsCallbacks ) $.fn.aAPFSortedFieldsCallbacks = [];

				// Store the callback functions
				$.fn.aAPFAddRepeatableFieldCallbacks.push( oSettings.added_repeatable_field );
				$.fn.aAPFRemoveRepeatableFieldCallbacks.push( oSettings.removed_repeatable_field );
				$.fn.aAPFSortedFieldsCallbacks.push( oSettings.sorted_fields );
				
				return;

			};
			
		}( jQuery ));"; } } endif;if ( ! class_exists( 'AdminPageFramework_Script_RepeatableField' ) ) : class AdminPageFramework_Script_RepeatableField { static public function getjQueryPlugin( $sCannotAddMore, $sCannotRemoveMore ) { return "(function ( $ ) {
		
			$.fn.updateAPFRepeatableFields = function( aSettings ) {
				
				var nodeThis = this;	// it can be from a fields container or a cloned field container.
				var sFieldsContainerID = nodeThis.find( '.repeatable-field-add' ).first().data( 'id' );
				/* Store the fields specific options in an array  */
				if ( ! $.fn.aAPFRepeatableFieldsOptions ) $.fn.aAPFRepeatableFieldsOptions = [];
				if ( ! $.fn.aAPFRepeatableFieldsOptions.hasOwnProperty( sFieldsContainerID ) ) {		
					$.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ] = $.extend({	
						max: 0,	// These are the defaults.
						min: 0,
						}, aSettings );
				}
				var aOptions = $.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ];
				
				/* Set the option values in the data attributes so that when a section is repeated and creates a brand new field container, it can refer the options */
				$( nodeThis ).find( '.admin-page-framework-repeatable-field-buttons' ).attr( 'data-max', aOptions['max'] );
				$( nodeThis ).find( '.admin-page-framework-repeatable-field-buttons' ).attr( 'data-min', aOptions['min'] );
				
				/* The Add button behaviour - if the tag id is given, multiple buttons will be selected. 
				 * Otherwise, a field node is given and single button will be selected. */
				$( nodeThis ).find( '.repeatable-field-add' ).unbind( 'click' );
				$( nodeThis ).find( '.repeatable-field-add' ).click( function() {
					$( this ).addAPFRepeatableField();
					return false;	// will not click after that
				});
				
				/* The Remove button behaviour */
				$( nodeThis ).find( '.repeatable-field-remove' ).unbind( 'click' );
				$( nodeThis ).find( '.repeatable-field-remove' ).click( function() {
					$( this ).removeAPFRepeatableField();
					return false;	// will not click after that
				});		
				
				/* If the number of fields is less than the set minimum value, add fields. */
				var sFieldID = nodeThis.find( '.repeatable-field-add' ).first().closest( '.admin-page-framework-field' ).attr( 'id' );
				var nCurrentFieldCount = jQuery( '#' + sFieldsContainerID ).find( '.admin-page-framework-field' ).length;
				if ( aOptions['min'] > 0 && nCurrentFieldCount > 0 ) {
					if ( ( aOptions['min'] - nCurrentFieldCount ) > 0 ) {					
						$( '#' + sFieldID ).addAPFRepeatableField( sFieldID );				 
					}
				}
				
			};
			
			/**
			 * Adds a repeatable field.
			 */
			$.fn.addAPFRepeatableField = function( sFieldContainerID ) {
				if ( typeof sFieldContainerID === 'undefined' ) {
					var sFieldContainerID = $( this ).closest( '.admin-page-framework-field' ).attr( 'id' );	
				}

				var nodeFieldContainer = $( '#' + sFieldContainerID );
				var nodeNewField = nodeFieldContainer.clone();	// clone without bind events.
				var nodeFieldsContainer = nodeFieldContainer.closest( '.admin-page-framework-fields' );
				var sFieldsContainerID = nodeFieldsContainer.attr( 'id' );

				/* If the set maximum number of fields already exists, do not add */
 				if ( ! $.fn.aAPFRepeatableFieldsOptions.hasOwnProperty( sFieldsContainerID ) ) {		
					var nodeButtonContainer = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' );
					$.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ] = {	
						max: nodeButtonContainer.attr( 'data-max' ),	// These are the defaults.
						min: nodeButtonContainer.attr( 'data-min' ),
					};
				}		 
				var sMaxNumberOfFields = $.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ]['max'];
				if ( sMaxNumberOfFields != 0 && nodeFieldsContainer.find( '.admin-page-framework-field' ).length >= sMaxNumberOfFields ) {
					var nodeLastRepeaterButtons = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' ).last();
					var sMessage = $( this ).formatPrintText( '{$sCannotAddMore}', sMaxNumberOfFields );
					var nodeMessage = $( '<span class=\"repeatable-error\" id=\"repeatable-error-' + sFieldsContainerID + '\" style=\"float:right;color:red;margin-left:1em;\">' + sMessage + '</span>' );
					if ( nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).length > 0 )
						nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).replaceWith( nodeMessage );
					else
						nodeLastRepeaterButtons.before( nodeMessage );
					nodeMessage.delay( 2000 ).fadeOut( 1000 );
					return;		
				}
				
				nodeNewField.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' );	// empty the value		
				nodeNewField.find( '.repeatable-error' ).remove();	// remove error messages.
				
				/* Add the cloned new field element */
				nodeNewField.insertAfter( nodeFieldContainer );	
				
				/* Increment the names and ids of the next following siblings. */
				nodeFieldContainer.nextAll().each( function() {
					$( this ).incrementIDAttribute( 'id' );
					$( this ).find( 'label' ).incrementIDAttribute( 'for' );
					$( this ).find( 'input,textarea,select' ).incrementIDAttribute( 'id' );
					$( this ).find( 'input,textarea,select' ).incrementNameAttribute( 'name' );
				});

				/* Rebind the click event to the buttons - important to update AFTER inserting the clone to the document node since the update method need to count fields. 
				 * Also do this after updating the attributes since the script needs to check the last added id for repeatable field options such as 'min'
				 * */
				nodeNewField.updateAPFRepeatableFields();
				
				/* It seems radio buttons of the original field need to be reassigned. Otherwise, the checked items will be gone. */
				nodeFieldContainer.find( 'input[type=radio][checked=checked]' ).attr( 'checked', 'Checked' );	
				
				/* Call the registered callback functions */
				nodeNewField.callBackAddRepeatableField( nodeNewField.data( 'type' ), nodeNewField.attr( 'id' ) );					
				
				/* If more than one fields are created, show the Remove button */
				var nodeRemoveButtons =  nodeFieldsContainer.find( '.repeatable-field-remove' );
				if ( nodeRemoveButtons.length > 1 ) nodeRemoveButtons.show();				
									
				/* Return the newly created element */
				return nodeNewField;	// media uploader needs this 
				
			};
				
			$.fn.removeAPFRepeatableField = function() {
				
				/* Need to remove the element: the field container */
				var nodeFieldContainer = $( this ).closest( '.admin-page-framework-field' );
				var nodeFieldsContainer = $( this ).closest( '.admin-page-framework-fields' );
				var sFieldsContainerID = nodeFieldsContainer.attr( 'id' );
				
				/* If the set minimum number of fields already exists, do not remove */
				var sMinNumberOfFields = $.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ]['min'];
				if ( sMinNumberOfFields != 0 && nodeFieldsContainer.find( '.admin-page-framework-field' ).length <= sMinNumberOfFields ) {
					var nodeLastRepeaterButtons = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' ).last();
					var sMessage = $( this ).formatPrintText( '{$sCannotRemoveMore}', sMinNumberOfFields );
					var nodeMessage = $( '<span class=\"repeatable-error\" id=\"repeatable-error-' + sFieldsContainerID + '\" style=\"float:right;color:red;margin-left:1em;\">' + sMessage + '</span>' );
					if ( nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).length > 0 )
						nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).replaceWith( nodeMessage );
					else
						nodeLastRepeaterButtons.before( nodeMessage );
					nodeMessage.delay( 2000 ).fadeOut( 1000 );
					return;		
				}				
				
				/* Decrement the names and ids of the next following siblings. */
				nodeFieldContainer.nextAll().each( function() {
					$( this ).decrementIDAttribute( 'id' );
					$( this ).find( 'label' ).decrementIDAttribute( 'for' );
					$( this ).find( 'input,textarea,select' ).decrementIDAttribute( 'id' );
					$( this ).find( 'input,textarea,select' ).decrementNameAttribute( 'name' );																	
				});

				/* Call the registered callback functions */
				nodeFieldContainer.callBackRemoveRepeatableField( nodeFieldContainer.data( 'type' ), nodeFieldContainer.attr( 'id' ) );	
			
				/* Remove the field */
				nodeFieldContainer.remove();
				
				/* Count the remaining Remove buttons and if it is one, disable the visibility of it */
				var nodeRemoveButtons = nodeFieldsContainer.find( '.repeatable-field-remove' );
				if ( nodeRemoveButtons.length == 1 ) nodeRemoveButtons.css( 'display', 'none' );
					
			};
				
		}( jQuery ));	
		"; } } endif;if ( ! class_exists( 'AdminPageFramework_Script_RepeatableSection' ) ) : class AdminPageFramework_Script_RepeatableSection { static public function getjQueryPlugin( $sCannotAddMore, $sCannotRemoveMore ) { return "( function( $ ) {

			$.fn.updateAPFRepeatableSections = function( aSettings ) {
				
				var nodeThis = this;	// it can be from a sections container or a cloned section container.
				var sSectionsContainerID = nodeThis.find( '.repeatable-section-add' ).first().closest( '.admin-page-framework-sectionset' ).attr( 'id' );

				/* Store the sections specific options in an array  */
				if ( ! $.fn.aAPFRepeatableSectionsOptions ) $.fn.aAPFRepeatableSectionsOptions = [];
				if ( ! $.fn.aAPFRepeatableSectionsOptions.hasOwnProperty( sSectionsContainerID ) ) {		
					$.fn.aAPFRepeatableSectionsOptions[ sSectionsContainerID ] = $.extend({	
						max: 0,	// These are the defaults.
						min: 0,
						}, aSettings );
				}
				var aOptions = $.fn.aAPFRepeatableSectionsOptions[ sSectionsContainerID ];

				/* The Add button behavior - if the tag id is given, multiple buttons will be selected. 
				 * Otherwise, a section node is given and single button will be selected. */
				$( nodeThis ).find( '.repeatable-section-add' ).click( function() {
					$( this ).addAPFRepeatableSection();
					return false;	// will not click after that
				});
				
				/* The Remove button behavior */
				$( nodeThis ).find( '.repeatable-section-remove' ).click( function() {
					$( this ).removeAPFRepeatableSection();
					return false;	// will not click after that
				});		
				
				/* If the number of sections is less than the set minimum value, add sections. */
				var sSectionID = nodeThis.find( '.repeatable-section-add' ).first().closest( '.admin-page-framework-section' ).attr( 'id' );
				var nCurrentSectionCount = jQuery( '#' + sSectionsContainerID ).find( '.admin-page-framework-section' ).length;
				if ( aOptions['min'] > 0 && nCurrentSectionCount > 0 ) {
					if ( ( aOptions['min'] - nCurrentSectionCount ) > 0 ) {					
						$( '#' + sSectionID ).addAPFRepeatableSection( sSectionID );				 
					}
				}
				
			};
			
			/**
			 * Adds a repeatable section.
			 */
			$.fn.addAPFRepeatableSection = function( sSectionContainerID ) {
				if ( typeof sSectionContainerID === 'undefined' ) {
					var sSectionContainerID = $( this ).closest( '.admin-page-framework-section' ).attr( 'id' );	
				}

				var nodeSectionContainer = $( '#' + sSectionContainerID );
				var nodeNewSection = nodeSectionContainer.clone();	// clone without bind events.
				var nodeSectionsContainer = nodeSectionContainer.closest( '.admin-page-framework-sectionset' );
				var sSectionsContainerID = nodeSectionsContainer.attr( 'id' );
				var nodeTabsContainer = $( '#' + sSectionContainerID ).closest( '.admin-page-framework-sectionset' ).find( '.admin-page-framework-section-tabs' );
				
				/* If the set maximum number of sections already exists, do not add */
				var sMaxNumberOfSections = $.fn.aAPFRepeatableSectionsOptions[ sSectionsContainerID ]['max'];
				if ( sMaxNumberOfSections != 0 && nodeSectionsContainer.find( '.admin-page-framework-section' ).length >= sMaxNumberOfSections ) {
					var nodeLastRepeaterButtons = nodeSectionContainer.find( '.admin-page-framework-repeatable-section-buttons' ).last();
					var sMessage = $( this ).formatPrintText( '{$sCannotAddMore}', sMaxNumberOfSections );
					var nodeMessage = $( '<span class=\"repeatable-section-error\" id=\"repeatable-section-error-' + sSectionsContainerID + '\" style=\"float:right;color:red;margin-left:1em;\">' + sMessage + '</span>' );
					if ( nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).length > 0 )
						nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).replaceWith( nodeMessage );
					else
						nodeLastRepeaterButtons.before( nodeMessage );
					nodeMessage.delay( 2000 ).fadeOut( 1000 );
					return;		
				}
				
				nodeNewSection.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' );	// empty the value		
				nodeNewSection.find( '.repeatable-section-error' ).remove();	// remove error messages.
				
				/* If this is not for tabbed sections, do not show the title */
				var sSectionTabSlug = nodeNewSection.find( '.admin-page-framework-section-caption' ).first().attr( 'data-section_tab' );
				if ( ! sSectionTabSlug || sSectionTabSlug === '_default' ) {
					nodeNewSection.find( '.admin-page-framework-section-title' ).hide();
				}
								
				/* Add the cloned new field element */
				nodeNewSection.insertAfter( nodeSectionContainer );	

				/* It seems radio buttons of the original field need to be reassigned. Otherwise, the checked items will be gone. */
				nodeSectionContainer.find( 'input[type=radio][checked=checked]' ).attr( 'checked', 'Checked' );	
				
				/* Iterate each section and increment the names and ids of the next following siblings. */
				nodeSectionContainer.nextAll().each( function() {
					
					incrementAttributes( this );
					
					/* Iterate each field one by one */
					$( this ).find( '.admin-page-framework-field' ).each( function() {	
					
						/* Rebind the click event to the repeatable field buttons - important to update AFTER inserting the clone to the document node since the update method need to count fields. */
						$( this ).updateAPFRepeatableFields();
													
						/* Call the registered callback functions */
						$( this ).callBackAddRepeatableField( $( this ).data( 'type' ), $( this ).attr( 'id' ), 1 );
						
					});					
					
				});
			
				/* Rebind the click event to the repeatable sections buttons - important to update AFTER inserting the clone to the document node since the update method need to count sections. 
				 * Also do this after updating the attributes since the script needs to check the last added id for repeatable section options such as 'min'
				 * */
				nodeNewSection.updateAPFRepeatableSections();	
				
				/* Rebind sortable fields - iterate sortable fields containers */
				nodeNewSection.find( '.admin-page-framework-fields.sortable' ).each( function() {
					$( this ).enableAPFSortable();
				});
				
				/* For tabbed sections - add the title tab list */
				if ( nodeTabsContainer.length > 0 ) {
					
					/* The clicked(copy source) section tab */
					var nodeTab = nodeTabsContainer.find( '#section_tab-' + sSectionContainerID );
					var nodeNewTab = nodeTab.clone();
					
					nodeNewTab.removeClass( 'active' );
					nodeNewTab.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' );	// empty the value
				
					/* Add the cloned new field tab */
					nodeNewTab.insertAfter( nodeTab );	
					
					/* Increment the names and ids of the next following siblings. */
					nodeTab.nextAll().each( function() {
						incrementAttributes( this );
						$( this ).find( 'a.anchor' ).incrementIDAttribute( 'href' );
					});					
					
					nodeTabsContainer.closest( '.admin-page-framework-section-tabs-contents' ).createTabs( 'refresh' );
				}				
				
				/* If more than one sections are created, show the Remove button */
				var nodeRemoveButtons =  nodeSectionsContainer.find( '.repeatable-section-remove' );
				if ( nodeRemoveButtons.length > 1 ) nodeRemoveButtons.show();				
									
				/* Return the newly created element */
				return nodeNewSection;	
				
			};	
			// Local function literal
			var incrementAttributes = function( oElement, bFirstFound ) {
				
				bFirstFound = typeof bFirstFound !== 'undefined' ? bFirstFound : true;
				$( oElement ).incrementIDAttribute( 'id', bFirstFound );	// passing true in the second parameter means to apply the change to the first occurrence.
				$( oElement ).find( 'tr.admin-page-framework-fieldrow' ).incrementIDAttribute( 'id', bFirstFound );
				$( oElement ).find( '.admin-page-framework-fieldset' ).incrementIDAttribute( 'id', bFirstFound );
				$( oElement ).find( '.admin-page-framework-fieldset' ).incrementIDAttribute( 'data-field_id', bFirstFound );	// I don't remember what this data attribute was for...
				$( oElement ).find( '.admin-page-framework-fields' ).incrementIDAttribute( 'id', bFirstFound );
				$( oElement ).find( '.admin-page-framework-field' ).incrementIDAttribute( 'id', bFirstFound );
				$( oElement ).find( 'table.form-table' ).incrementIDAttribute( 'id', bFirstFound );
				$( oElement ).find( '.repeatable-field-add' ).incrementIDAttribute( 'data-id', bFirstFound );	// holds the fields container ID referred by the repeater field script.
				$( oElement ).find( 'label' ).incrementIDAttribute( 'for', bFirstFound );	
				$( oElement ).find( 'input,textarea,select' ).incrementIDAttribute( 'id', bFirstFound );
				$( oElement ).find( 'input,textarea,select' ).incrementNameAttribute( 'name', bFirstFound );				
				
			}			
				
			$.fn.removeAPFRepeatableSection = function() {
				
				/* Need to remove the element: the secitons container */
				var nodeSectionContainer = $( this ).closest( '.admin-page-framework-section' );
				var sSectionConteinrID = nodeSectionContainer.attr( 'id' );
				var nodeSectionsContainer = $( this ).closest( '.admin-page-framework-sectionset' );
				var sSectionsContainerID = nodeSectionsContainer.attr( 'id' );
				var nodeTabsContainer = nodeSectionsContainer.find( '.admin-page-framework-section-tabs' );
				var nodeTabs = nodeTabsContainer.find( '.admin-page-framework-section-tab' );
				
				/* If the set minimum number of sections already exists, do not remove */
				var sMinNumberOfSections = $.fn.aAPFRepeatableSectionsOptions[ sSectionsContainerID ]['min'];
				if ( sMinNumberOfSections != 0 && nodeSectionsContainer.find( '.admin-page-framework-section' ).length <= sMinNumberOfSections ) {
					var nodeLastRepeaterButtons = nodeSectionContainer.find( '.admin-page-framework-repeatable-section-buttons' ).last();
					var sMessage = $( this ).formatPrintText( '{$sCannotRemoveMore}', sMinNumberOfSections );
					var nodeMessage = $( '<span class=\"repeatable-section-error\" id=\"repeatable-section-error-' + sSectionsContainerID + '\" style=\"float:right;color:red;margin-left:1em;\">' + sMessage + '</span>' );
					if ( nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).length > 0 )
						nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).replaceWith( nodeMessage );
					else
						nodeLastRepeaterButtons.before( nodeMessage );
					nodeMessage.delay( 2000 ).fadeOut( 1000 );
					return;		
				}				
				
				/* Decrement the names and ids of the next following siblings. */
				nodeSectionContainer.nextAll().each( function() {
					
					decrementAttributes( this );
					
					/* Call the registered callback functions */
					$( this ).find( '.admin-page-framework-field' ).each( function() {	
						$( this ).callBackRemoveRepeatableField( $( this ).data( 'type' ), $( this ).attr( 'id' ), 1 );
					});					
					
				});
			
				/* Remove the field */
				nodeSectionContainer.remove();
				
				/* For tabbed sections - remove the title tab list */
				if ( nodeTabsContainer.length > 0 && nodeTabs.length > 1 ) {
					nodeSelectionTab = nodeTabsContainer.find( '#section_tab-' + sSectionConteinrID );
					nodeSelectionTab.nextAll().each( function() {
						$( this ).find( 'a.anchor' ).decrementIDAttribute( 'href' );
						decrementAttributes( this );
					});	
					
					if (  nodeSelectionTab.prev().length )
						nodeSelectionTab.prev().addClass( 'active' );
					else
						nodeSelectionTab.next().addClass( 'active' );
						
					nodeSelectionTab.remove();
					nodeTabsContainer.closest( '.admin-page-framework-section-tabs-contents' ).createTabs( 'refresh' );
				}						
				
				/* Count the remaining Remove buttons and if it is one, disable the visibility of it */
				var nodeRemoveButtons = nodeSectionsContainer.find( '.repeatable-section-remove' );
				if ( nodeRemoveButtons.length == 1 ) {
					
					nodeRemoveButtons.css( 'display', 'none' );
					
					/* Also if this is not for tabbed sections, do show the title */
					var sSectionTabSlug = nodeSectionsContainer.find( '.admin-page-framework-section-caption' ).first().attr( 'data-section_tab' );
					if ( ! sSectionTabSlug || sSectionTabSlug === '_default' ) 
						nodeSectionsContainer.find( '.admin-page-framework-section-title' ).first().show();
					
				}
					
			};
			// Local function literal
			var decrementAttributes = function( oElement, bFirstFound ) {
				
				bFirstFound = typeof bFirstFound !== 'undefined' ? bFirstFound : true;
				$( oElement ).decrementIDAttribute( 'id' );					
				$( oElement ).find( 'tr.admin-page-framework-fieldrow' ).decrementIDAttribute( 'id', bFirstFound );
				$( oElement ).find( '.admin-page-framework-fieldset' ).decrementIDAttribute( 'id', bFirstFound );
				$( oElement ).find( '.admin-page-framework-fieldset' ).decrementIDAttribute( 'data-field_id', bFirstFound );	// I don't remember what this data attribute was for...
				$( oElement ).find( '.admin-page-framework-fields' ).decrementIDAttribute( 'id', bFirstFound );
				$( oElement ).find( '.admin-page-framework-field' ).decrementIDAttribute( 'id', bFirstFound );
				$( oElement ).find( 'table.form-table' ).decrementIDAttribute( 'id', bFirstFound );
				$( oElement ).find( '.repeatable-field-add' ).decrementIDAttribute( 'data-id', bFirstFound );	// holds the fields container ID referred by the repeater field script.
				$( oElement ).find( 'label' ).decrementIDAttribute( 'for', bFirstFound );
				$( oElement ).find( 'input,textarea,select' ).decrementIDAttribute( 'id', bFirstFound );
				$( oElement ).find( 'input,textarea,select' ).decrementNameAttribute( 'name', bFirstFound );				
				
			}	
			
		}( jQuery ));"; } } endif;if ( ! class_exists( 'AdminPageFramework_Script_Sortable' ) ) : class AdminPageFramework_Script_Sortable { static public function getjQueryPlugin() { return "(function($) {
			var dragging, placeholders = $();
			$.fn.sortable = function(options) {
				var method = String(options);
				options = $.extend({
					connectWith: false
				}, options);
				return this.each(function() {
					if (/^enable|disable|destroy$/.test(method)) {
						var items = $(this).children($(this).data('items')).attr('draggable', method == 'enable');
						if (method == 'destroy') {
							items.add(this).removeData('connectWith items')
								.off('dragstart.h5s dragend.h5s selectstart.h5s dragover.h5s dragenter.h5s drop.h5s');
						}
						return;
					}
					var isHandle, index, items = $(this).children(options.items);
					var placeholder = $('<' + (/^ul|ol$/i.test(this.tagName) ? 'li' : 'div') + ' class=\"sortable-placeholder\">');
					items.find(options.handle).mousedown(function() {
						isHandle = true;
					}).mouseup(function() {
						isHandle = false;
					});
					$(this).data('items', options.items)
					placeholders = placeholders.add(placeholder);
					if (options.connectWith) {
						$(options.connectWith).add(this).data('connectWith', options.connectWith);
					}
					items.attr('draggable', 'true').on('dragstart.h5s', function(e) {
						if (options.handle && !isHandle) {
							return false;
						}
						isHandle = false;
						var dt = e.originalEvent.dataTransfer;
						dt.effectAllowed = 'move';
						dt.setData('Text', 'dummy');
						index = (dragging = $(this)).addClass('sortable-dragging').index();
					}).on('dragend.h5s', function() {
						dragging.removeClass('sortable-dragging').show();
						placeholders.detach();
						if (index != dragging.index()) {
							items.parent().trigger('sortupdate', {item: dragging});
						}
						dragging = null;
					}).not('a[href], img').on('selectstart.h5s', function() {
						this.dragDrop && this.dragDrop();
						return false;
					}).end().add([this, placeholder]).on('dragover.h5s dragenter.h5s drop.h5s', function(e) {
						if (!items.is(dragging) && options.connectWith !== $(dragging).parent().data('connectWith')) {
							return true;
						}
						if (e.type == 'drop') {
							e.stopPropagation();
							placeholders.filter(':visible').after(dragging);
							return false;
						}
						e.preventDefault();
						e.originalEvent.dataTransfer.dropEffect = 'move';
						if (items.is(this)) {
							if (options.forcePlaceholderSize) {
								placeholder.height(dragging.outerHeight());
							}
							dragging.hide();
							$(this)[placeholder.index() < $(this).index() ? 'after' : 'before'](placeholder);
							placeholders.not(placeholder).detach();
						} else if (!placeholders.is(this) && !$(this).children(options.items).length) {
							placeholders.detach();
							$(this).append(placeholder);
						}
						return false;
					});
				});
			};
			
			$.fn.enableAPFSortable = function( sFieldsContainerID ) {
				
				var oTarget = typeof sFieldsContainerID === 'string' 
					? $( '#' + sFieldsContainerID + '.sortable' )
					: this;
				
				oTarget.unbind( 'sortupdate' );
				oTarget.sortable(
					{	items: '> div:not( .disabled )', }	// the options for the sortable plugin
				).bind( 'sortupdate', function() {
					
					/* Rename the ids and names */
					var nodeFields = $( this ).children( 'div' );
					var iCount = 1;
					var iMaxCount = nodeFields.length;

					$( $( this ).children( 'div' ).reverse() ).each( function() {	// reverse is needed for radio buttons since they loose the selections when updating the IDs

						var iIndex = ( iMaxCount - iCount );
						$( this ).setIndexIDAttribute( 'id', iIndex );
						$( this ).find( 'label' ).setIndexIDAttribute( 'for', iIndex );
						$( this ).find( 'input,textarea,select' ).setIndexIDAttribute( 'id', iIndex );
						$( this ).find( 'input,textarea,select' ).setIndexNameAttribute( 'name', iIndex );

						/* Radio buttons loose their selections when IDs and names are updated, so reassign them */
						$( this ).find( 'input[type=radio]' ).each( function() {	
							var sAttr = $( this ).prop( 'checked' );
							if ( typeof sAttr !== 'undefined' && sAttr !== false) 
								$( this ).attr( 'checked', 'Checked' );
						});
							
						iCount++;
					});
					
					/* It seems radio buttons need to be taken cared of again. Otherwise, the checked items will be gone. */
					$( this ).find( 'input[type=radio][checked=checked]' ).attr( 'checked', 'Checked' );	
					
					/* Callback the registered functions */
					$( this ).callBackSortedFields( $( this ).data( 'type' ), $( this ).attr( 'id' ) );
					
				}); 				
			
			};
		}( jQuery ));"; } } endif;if ( ! class_exists( 'AdminPageFramework_Script_Tab' ) ) : class AdminPageFramework_Script_Tab { static public function getjQueryPlugin() { return "( function( $ ) {
			
			$.fn.createTabs = function( asOptions ) {
				
				bIsRefresh = ( typeof asOptions === 'string' && asOptions === 'refresh' );
				if ( typeof asOptions === 'object' )
					var aOptions = $.extend( {
					}, asOptions );
				
				this.find( 'ul' ).each( function () {
				
					$( this ).children( 'li' ).each( function( i ) {			
						
						var sTabContentID = $( this ).children( 'a' ).attr( 'href' );
						if ( ! bIsRefresh && i == 0 ) 
							$( this ).addClass( 'active' );
						
						if ( $( this ).hasClass( 'active' ) ) 
							$( sTabContentID ).show();
						else
							$( sTabContentID ).css( 'display', 'none' );
						
						$( this ).addClass( 'nav-tab' );
						$( this ).children( 'a' ).addClass( 'anchor' );
						
						$( this ).unbind( 'click' );	// for refreshing 
						$( this ).click( function( e ){
								 
							e.preventDefault();	// Prevents jumping to the anchor which moves the scroll bar.
							
							// Remove the active tab and set the clicked tab to be active.
							$( this ).siblings( 'li.active' ).removeClass( 'active' );
							$( this ).addClass( 'active' );
							
							// Find the element id and select the content element with it.
							var sTabContentID = $( this ).find( 'a' ).attr( 'href' );
							oActiveContent = $( this ).parent().parent().find( sTabContentID ).css( 'display', 'block' ); 
							oActiveContent.siblings( ':not( ul )' ).css( 'display', 'none' );
							
						});
					});
				});
								
			};
		}( jQuery ));"; } } endif;if ( ! class_exists( 'AdminPageFramework_Script_Utility' ) ) : class AdminPageFramework_Script_Utility { static public function getjQueryPlugin() { return "( function( $ ) {
			$.fn.reverse = [].reverse;
		
			$.fn.formatPrintText = function() {
				var aArgs = arguments;
				return aArgs[ 0 ].replace( /{(\d+)}/g, function( match, number ) {
					return typeof aArgs[ parseInt( number ) + 1 ] != 'undefined'
						? aArgs[ parseInt( number ) + 1 ]
						: match
				;});
			};
		}( jQuery ));"; } } endif;