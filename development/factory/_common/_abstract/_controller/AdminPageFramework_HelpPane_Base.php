<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides base methods and properties for manipulating the contextual help tabs.
 *
 * @package     AdminPageFramework/Common/Factory/HelpPane
 * @since       2.1.0
 * @abstract
 * @internal
 */
abstract class AdminPageFramework_HelpPane_Base extends AdminPageFramework_FrameworkUtility {

    /**
     * A property object.
     *
     * @remark      Set in the constructor.
     */
    public $oProp;

    /**
     * Stores the screen object.
     * @var     object
     * @since   2.1.0
     */
    protected $_oScreen;

    /**
     * Sets up properties and hooks.
     */
    public function __construct( $oProp ) {

        $this->oProp = $oProp;

        add_action( 'admin_head', array( $this, '_replyToRegisterHelpTabText' ) );

    }

    /**
     * Sets the contextual help tab.
     *
     * On contrary to other methods relating to contextual help tabs that just modify the class properties, this finalizes the help tab contents.
     * In other words, the set values here will take effect.
     *
     * @access protected
     * @remark The sidebar contents in the help pane can be set but if it's called from the meta box class and the page loads in regular post types; the sidebar text may be overridden by the default one.
     * @since 2.1.0
     * @internal
     */
    protected function _setHelpTab( $sID, $sTitle, $aContents, $aSideBarContents=array() ) {

        if ( empty( $aContents ) ) {
            return;
        }

        $this->_oScreen = isset( $this->_oScreen )
            ? $this->_oScreen
            : get_current_screen();
        $this->_oScreen->add_help_tab(
            array(
                'id'      => $sID,
                'title'   => $sTitle,
                'content' => implode( PHP_EOL, $aContents ),
            )
        );

        if ( ! empty( $aSideBarContents ) ) {
            $this->_oScreen->set_help_sidebar( implode( PHP_EOL, $aSideBarContents ) );
        }

    }

    /**
     * Encloses the given string with the contextual help specific tag.
     * @since 2.1.0
     * @internal
     */
    protected function _formatHelpDescription( $sHelpDescription ) {
        return "<div class='contextual-help-description'>" . $sHelpDescription . "</div>";
    }


    /**
     * Determines whether the currently loaded page belongs to the meta box page.
     *
     * @since       3.7.10
     * @internal
     * @deprecated  3.8.14
     */
//    protected function _isInThePage() {
//        return $this->oProp->oCaller->isInThePage();
//    }

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
     * On contrary to the `addHelpTab()` method of the `AdminPageFramework_HelpPane_admin_page` class, the help tab title is already determined and the meta box ID and the title will be used.
     *
     * @since       2.1.0
     * @since       3.7.10      Moved from `AdminPageFrameowrk_HelpPane_post_meta_box`
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
     * @remark      Since the screen object needs to be established, some hooks are too early like `admin_init` or `admin_menu`.
     * @since       2.1.0
     * @since       3.7.10      Moved from `AdminPageFrameowrk_HelpPane_post_meta_box`. Changed the name from `_replyToRegisterHelpTabTextForMetaBox()`.
     * @callback    action      admin_head
     * @internal
     */
    public function _replyToRegisterHelpTabText() {

        // Check if the currently loaded page is of meta box page.
        if ( ! $this->oProp->oCaller->isInThePage() ) {
            return false;
        }

        $this->_setHelpTab(     // defined in the base class.
            $this->oProp->sClassName,
            $this->oProp->sTitle,
            $this->oProp->aHelpTabText,
            $this->oProp->aHelpTabTextSide
        );

    }

}
