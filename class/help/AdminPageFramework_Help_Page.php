<?php
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
 * @staticvar			array			$_aStructure_HelpTabUserArray			stores the array structure of the help tab array.
 */
abstract class AdminPageFramework_Help_Page extends AdminPageFramework_Help_Page_Base {
	
	/**
	 * Represents the structure of help tab array that is used by the user to set a help tab content.
	 * 
	 * @since			2.1.0
	 * @internal
	 */ 
	protected static $_aStructure_HelpTabUserArray = array(
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
	public function replyToRegisterHelpTabs() {
			
		$sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
		$sCurrentPageTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : ( isset( $this->oProps->aDefaultInPageTabs[ $sCurrentPageSlug ] ) ? $this->oProps->aDefaultInPageTabs[ $sCurrentPageSlug ] : '' );
		
		if ( empty( $sCurrentPageSlug ) ) return;
		if ( ! $this->oProps->isPageAdded( $sCurrentPageSlug ) ) return;
		
		foreach( $this->oProps->aHelpTabs as $aHelpTab ) {
			
			if ( $sCurrentPageSlug != $aHelpTab['sPageSlug'] ) continue;
			if ( isset( $aHelpTab['sPageTabSlug'] ) && ! empty( $aHelpTab['sPageTabSlug'] ) & $sCurrentPageTabSlug != $aHelpTab['sPageTabSlug'] ) continue;
				
			$this->_setHelpTab( 
				$aHelpTab['sID'], 
				$aHelpTab['sTitle'], 
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
	 * @param			array			$aHelpTab				The help tab array. The key structure is detailed in the description part.
	 * @return			void
	 */ 
	protected function addHelpTab( $aHelpTab ) {
		
		// Avoid undefined index warnings.
		$aHelpTab = ( array ) $aHelpTab + self::$_aStructure_HelpTabUserArray;
		
		// If the key is not set, that means the help tab array is not created yet. So create it and go back.
		if ( ! isset( $this->oProps->aHelpTabs[ $aHelpTab['help_tab_id'] ] ) ) {
			$this->oProps->aHelpTabs[ $aHelpTab['help_tab_id'] ] = array(
				'sID'				=> $aHelpTab['help_tab_id'],
				'sTitle'			=> $aHelpTab['help_tab_title'],
				'aContent'			=> ! empty( $aHelpTab['help_tab_content'] ) ? array( $this->_formatHelpDescription( $aHelpTab['help_tab_content'] ) ) : array(),
				'aSidebar'			=> ! empty( $aHelpTab['help_tab_sidebar_content'] ) ? array( $this->_formatHelpDescription( $aHelpTab['help_tab_sidebar_content'] ) ) : array(),
				'sPageSlug'			=> $aHelpTab['page_slug'],
				'sPageTabSlug'		=> $aHelpTab['page_tab_slug'],
			);
			return;
		}

		// This line will be reached if the help tab array is already set. In this case, just append an array element into the keys.
		if ( ! empty( $aHelpTab['help_tab_content'] ) )
			$this->oProps->aHelpTabs[ $aHelpTab['help_tab_id'] ]['aContent'][] = $this->_formatHelpDescription( $aHelpTab['help_tab_content'] );
		if ( ! empty( $aHelpTab['help_tab_sidebar_content'] ) )
			$this->oProps->aHelpTabs[ $aHelpTab['help_tab_id'] ]['aSidebar'][] = $this->_formatHelpDescription( $aHelpTab['help_tab_sidebar_content'] );
		
	}
	
}
endif;