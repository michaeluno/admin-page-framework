<?php
if ( ! class_exists( 'AdminPageFramework_HelpPane_MetaBox' ) ) :
/**
 * Provides methods to manipulate the contextual help tab .
 * 
 * @since			2.1.0
 * @extends			AdminPageFramework_HelpPane_Base
 */
abstract class AdminPageFramework_HelpPane_MetaBox extends AdminPageFramework_HelpPane_Base {
	
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
	 * @remark			This method just adds the given text into the class property. The actual registration will be performed with the <em>replyToRegisterHelpTabTextForMetaBox()</em> method.
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
	 * On contrary to the <em>addHelpTab()</em> method of the AdminPageFramework_HelpPane_Page class, the help tab title is already determined and the meta box ID and the title will be used.
	 * 
	 * @since			2.1.0
	 * @uses			addHelpText()
	 * @remark			This method just adds the given text into the class property. The actual registration will be performed with the <em>replyToRegisterHelpTabTextForMetaBox()</em> method.
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
	public function replyToRegisterHelpTabTextForMetaBox() {
	
		if ( ! in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) ) return;
		if ( isset( $_GET['post_type'] ) && ! in_array( $_GET['post_type'], $this->oProps->aPostTypes ) ) return;
		if ( ! isset( $_GET['post_type'] ) && ! in_array( 'post', $this->oProps->aPostTypes ) ) return;
		if ( isset( $_GET['post'], $_GET['action'] ) && ! in_array( get_post_type( $_GET['post'] ), $this->oProps->aPostTypes ) ) return; // edit post page
		
		$this->_setHelpTab( 	// this method is defined in the base class.
			$this->oProps->sMetaBoxID, 
			$this->oProps->sTitle, 
			$this->oProps->aHelpTabText, 
			$this->oProps->aHelpTabTextSide 
		);
		
	}
	
}
endif;