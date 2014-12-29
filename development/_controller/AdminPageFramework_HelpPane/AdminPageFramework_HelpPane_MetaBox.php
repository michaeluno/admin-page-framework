<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to manipulate the contextual help tab .
 *
 * @package     AdminPageFramework
 * @subpackage  HelpPane
 * @since       2.1.0
 * @since       3.0.0 Become not abstract.
 * @extends     AdminPageFramework_HelpPane_Base
 * @internal
 */
class AdminPageFramework_HelpPane_MetaBox extends AdminPageFramework_HelpPane_Base {
    
    /**
     * Sets up hooks and properties.
     */
    function __construct( $oProp ) {
        
        parent::__construct( $oProp );
        
        if ( $oProp->bIsAdminAjax ) {
            return;
        }
        
        // the contextual help pane
        add_action( 'admin_head', array( $this, '_replyToRegisterHelpTabTextForMetaBox' ) ); // since the screen object needs to be established, some hooks are too early like admin_init or admin_menu.
        
    }
    
    /**
     * Adds the given HTML text to the contextual help pane.
     * 
     * The help tab will be the meta box title and all the added text will be inserted into the content area within the tab.
     *  
     * @since       2.1.0
     * @remark      This method just adds the given text into the class property. The actual registration will be performed with the <em>replyToRegisterHelpTabTextForMetaBox()</em> method.
     * @internal
     */ 
    public function _addHelpText( $sHTMLContent, $sHTMLSidebarContent="" ) {
        
        $this->oProp->aHelpTabText[]        = "<div class='contextual-help-description'>" . $sHTMLContent . "</div>";
        $this->oProp->aHelpTabTextSide[]    = "<div class='contextual-help-description'>" . $sHTMLSidebarContent . "</div>";
        
    }
    
    /**
     * Adds the given HTML text to the contextual help pane.
     * 
     * The help tab will be the meta box title and all the added text will be inserted into the content area within the tab.
     * On contrary to the `addHelpTab()` method of the `AdminPageFramework_HelpPane_Page` class, the help tab title is already determined and the meta box ID and the title will be used.
     * 
     * @since       2.1.0
     * @uses        _addHelpText()
     * @remark      This method just adds the given text into the class property. The actual registration will be performed with the `replyToRegisterHelpTabTextForMetaBox()` method.
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
     * @since       2.1.0
     * @remark      A call back for the `load-{page hook}` action hook.
     * @remark      The method name implies that this is for meta boxes. This does not mean this method is only for meta box form fields. Extra help text can be added with the `addHelpText()` method.
     * @internal
     */ 
    public function _replyToRegisterHelpTabTextForMetaBox() {

        // Check if the currently loaded page is of meta box page.
        if ( ! $this->_isInThePage() ) { return false; }

        $this->_setHelpTab(     // defined in the base class.
            $this->oProp->sMetaBoxID, 
            $this->oProp->sTitle, 
            $this->oProp->aHelpTabText, 
            $this->oProp->aHelpTabTextSide 
        );
        
    }
    
    /**
     * Determines whether the currently loaded page belongs to the meta box page.
     * 
     * @since       3.0.4
     * @internal
     */
    protected function _isInThePage() {
        
        if ( ! $this->oProp->bIsAdmin ) { return false; }
        if ( ! in_array( $this->oProp->sPageNow, array( 'post.php', 'post-new.php' ) ) ) {
            return false;
        }
        if ( ! in_array( $this->oUtil->getCurrentPostType(), $this->oProp->aPostTypes ) ) {
            return false;     
        }    
        return true;
        
    }
    
}