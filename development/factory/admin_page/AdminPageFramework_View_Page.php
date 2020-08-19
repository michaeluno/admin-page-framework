<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to manipulate menu items.
 *
 * @abstract
 * @since           3.3.1       Moved most methods from `AdminPageFramework_Page`.
 * @since           3.6.3       Changed the name from `AdminPageFramework_Page_View`.
 * @extends         AdminPageFramework_Model_Page
 * @package         AdminPageFramework/Factory/AdminPage/View
 * @internal
 */
abstract class AdminPageFramework_View_Page extends AdminPageFramework_Model_Page {

    /**
     * Load resources of page meta boxes.
     * @callback    action      load_after_{page slug}
     * @since       3.7.10
     */
    public function _replyToEnablePageMetaBoxes() {
        new AdminPageFramework_View__PageMetaboxEnabler( $this );
    }

    /**
     * Enqueues assets set with the `style` and `script` arguments.
     *
     * @callback    action      load_after_{page slug}
     * @since       3.6.3
     * @internal
     * @return      void
     */
    public function _replyToEnqueuePageAssets() {
        new AdminPageFramework_View__Resource( $this );
    }

    /**
     * @since       3.7.10
     * @callback    function        add_submenu_page
     * @return      void
     */
    public function _replyToRenderPage() {
        $_sPageSlug = $this->oProp->getCurrentPageSlug();
        $this->_renderPage(
            $_sPageSlug,
            $this->oProp->getCurrentTabSlug( $_sPageSlug )
        );
    }

    /**
     * Renders the admin page.
     *
     * @remark      This is not intended for the users to use.
     * @since       2.0.0
     * @since       3.3.1       Moved from `AdminPageFramework_Page`.
     * @access      protected
     * @return      void
     * @internal
     */
    protected function _renderPage( $sPageSlug, $sTabSlug=null ) {
        $_oPageRenderer = new AdminPageFramework_View__PageRenderer( $this, $sPageSlug, $sTabSlug );
        $_oPageRenderer->render();
    }

}
