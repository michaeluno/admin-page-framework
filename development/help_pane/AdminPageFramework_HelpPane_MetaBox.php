<?php
if ( ! class_exists( 'AdminPageFramework_HelpPane_MetaBox' ) ) :
/**
 * Provides methods to manipulate the contextual help tab .
 *
 * @package				AdminPageFramework
 * @subpackage			HelpPane
 * @since			2.1.0
 * @since			3.0.0			Become not abstract.
 * @extends			AdminPageFramework_HelpPane_Base
 * @internal
 */
class AdminPageFramework_HelpPane_MetaBox extends AdminPageFramework_HelpPane_Base {
	
	function __construct( $oProp ) {
		
		$this->oProp = $oProp;
		
		// the contextual help pane
		add_action( "load-{$GLOBALS['pagenow']}", array( $this, '_replyToRegisterHelpTabTextForMetaBox' ), 20 );	
		
	}
	
	/**
	 * Adds the given HTML text to the contextual help pane.
	 * 
	 * The help tab will be the meta box title and all the added text will be inserted into the content area within the tab.
	 *  
	 * @since			2.1.0
	 * @remark			This method just adds the given text into the class property. The actual registration will be performed with the <em>replyToRegisterHelpTabTextForMetaBox()</em> method.
	 * @internal
	 */ 
	public function _addHelpText( $sHTMLContent, $sHTMLSidebarContent="" ) {
		$this->oProp->aHelpTabText[] = "<div class='contextual-help-description'>" . $sHTMLContent . "</div>";
		$this->oProp->aHelpTabTextSide[] = "<div class='contextual-help-description'>" . $sHTMLSidebarContent . "</div>";
	}
	
	/**
	 * Adds the given HTML text to the contextual help pane.
	 * 
	 * The help tab will be the meta box title and all the added text will be inserted into the content area within the tab.
	 * On contrary to the <em>addHelpTab()</em> method of the AdminPageFramework_HelpPane_Page class, the help tab title is already determined and the meta box ID and the title will be used.
	 * 
	 * @since			2.1.0
	 * @uses			_addHelpText()
	 * @remark			This method just adds the given text into the class property. The actual registration will be performed with the <em>replyToRegisterHelpTabTextForMetaBox()</em> method.
	 * @internal
	 */ 	
	public function _addHelpTextForFormFields( $sFieldTitle, $sHelpText, $sHelpTextSidebar="" ) {
		$this->_addHelpText(
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
	 * @internal
	 */ 
	public function _replyToRegisterHelpTabTextForMetaBox() {
	
		if ( ! in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) ) return;
		if ( isset( $_GET['post_type'] ) && ! in_array( $_GET['post_type'], $this->oProp->aPostTypes ) ) return;
		if ( ! isset( $_GET['post_type'] ) && ! in_array( 'post', $this->oProp->aPostTypes ) ) return;
		if ( isset( $_GET['post'], $_GET['action'] ) && ! in_array( get_post_type( $_GET['post'] ), $this->oProp->aPostTypes ) ) return; // edit post page
		
		$this->_setHelpTab( 	// this method is defined in the base class.
			$this->oProp->sMetaBoxID, 
			$this->oProp->sTitle, 
			$this->oProp->aHelpTabText, 
			$this->oProp->aHelpTabTextSide 
		);
		
	}
	
}
endif;