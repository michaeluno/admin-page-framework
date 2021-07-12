<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * {@inheritdoc}
 *
 * {@inheritdoc}
 *
 * This is for generic pages the framework creates.
 *
 * @since       2.1.5
 * @since       3.3.0       Changed the name from AdminPageFramework_HeadTag_Page.
 * @package     AdminPageFramework/Factory/AdminPage/Resource
 * @extends     AdminPageFramework_Resource_Base
 * @internal
 */
class AdminPageFramework_Resource_admin_page extends AdminPageFramework_Resource_Base {

    /**
     * Applies page and tab specific filters to inline CSS rules.
     *
     * @since       3.5.0
     * @return      void
     */
    protected function _printClassSpecificStyles( $sIDPrefix ) {

        // This method can be called two times in a page to support embedding in the footer.
        static $_bLoaded = false;
        if ( $_bLoaded ) {
            parent::_printClassSpecificStyles( $sIDPrefix );
            return;
        }
        $_bLoaded   = true;

        $_oCaller   = $this->oProp->oCaller;
        $_sPageSlug = $this->_getCurrentPageSlugForFilter();
        $_sTabSlug  = $this->_getCurrentTabSlugForFilter( $_sPageSlug );

        // tab
        if ( $_sPageSlug && $_sTabSlug ) {
            $this->oProp->sStyle     = $this->addAndApplyFilters(
                $_oCaller,
                "style_{$_sPageSlug}_{$_sTabSlug}",
                $this->oProp->sStyle
            );
        }

        // page
        if ( $_sPageSlug ) {
            $this->oProp->sStyle     = $this->addAndApplyFilters(
                $_oCaller,
                "style_{$_sPageSlug}",
                $this->oProp->sStyle
            );
        }

        // The parent method should be called after updating the $this->oProp->sStyle property above.
        parent::_printClassSpecificStyles( $sIDPrefix );

    }
        /**
         * Returns the currently loaded page slug to apply resource filters.
         *
         * If the page has not been added, an empty value will be returned.
         *
         * @since       3.5.3
         * @return      string      The page slug if the page has been added.
         */
        private function _getCurrentPageSlugForFilter() {
            $_sPageSlug = $this->oProp->getCurrentPageSlug();
            return $this->oProp->isPageAdded( $_sPageSlug )
                ? $_sPageSlug
                : '';
        }
        /**
         * Returns the currently loaded tab slug to apply resource filters.
         *
         * If the tab has not been added, an empty value will be returned.
         *
         * @since       3.5.3
         * @return      string      The tab slug if the tab has been added.
         */
        private function _getCurrentTabSlugForFilter( $sPageSlug ) {
            $_sTabSlug  = $this->oProp->getCurrentTabSlug( $sPageSlug );
            return isset( $this->oProp->aInPageTabs[ $sPageSlug ][ $_sTabSlug ] )
                ? $_sTabSlug
                : '';
        }

    /**
     * Applies page and tab specific filters to inline JaveScript scirpts.
     *
     * @since       3.5.0
     * @return      void
     */
    protected function _printClassSpecificScripts( $sIDPrefix ) {

        // This method can be called two times in a page to support embedding in the footer.
        static $_bLoaded = false;
        if ( $_bLoaded ) {
            parent::_printClassSpecificScripts( $sIDPrefix );
            return;
        }
        $_bLoaded   = true;

        $_oCaller   = $this->oProp->oCaller;
        $_sPageSlug = $this->_getCurrentPageSlugForFilter();
        $_sTabSlug  = $this->_getCurrentTabSlugForFilter( $_sPageSlug );

        // tab
        if ( $_sPageSlug && $_sTabSlug ) {
            $this->oProp->sScript     = $this->addAndApplyFilters(
                $_oCaller,
                "script_{$_sPageSlug}_{$_sTabSlug}",
                $this->oProp->sScript
            );
        }

        // page
        if ( $_sPageSlug ) {
            $this->oProp->sScript     = $this->addAndApplyFilters(
                $_oCaller,
                "script_{$_sPageSlug}",
                $this->oProp->sScript
            );
        }

        // The parent method should be called after updating the $this->oProp->sScript property above.
        parent::_printClassSpecificScripts( $sIDPrefix );

    }

    /**
     * A helper function for the _replyToEnqueueScripts() and _replyToEnqueueStyle() methods.
     *
     * @since       2.1.2
     * @since       2.1.5       Moved from the main class. Changed the name from enqueueSRCByPageConditoin.
     * @since       3.7.0      Fixed a typo in the method name.
     * @internal
     */
    protected function _enqueueSRCByCondition( $aEnqueueItem ) {

        $sCurrentPageSlug   = $this->oProp->getCurrentPageSlug();
        $sCurrentTabSlug    = $this->oProp->getCurrentTabSlug( $sCurrentPageSlug );
        $sPageSlug          = $aEnqueueItem['sPageSlug'];
        $sTabSlug           = $aEnqueueItem['sTabSlug'];

        // If the page slug is not specified and the currently loading page is one of the pages that is added by the framework,
        if ( ! $sPageSlug && $this->oProp->isPageAdded( $sCurrentPageSlug ) ) { // means script-global(among pages added by the framework)
            return $this->_enqueueSRC( $aEnqueueItem );
        }

        // If both tab and page slugs are specified,
        if (
            ( $sPageSlug && $sCurrentPageSlug == $sPageSlug )
            && ( $sTabSlug && $sCurrentTabSlug == $sTabSlug )
        ) {
            return $this->_enqueueSRC( $aEnqueueItem );
        }

        // If the tab slug is not specified and the page slug is specified,
        // and if the current loading page slug and the specified one matches,
        if (
            ( $sPageSlug && ! $sTabSlug )
            && ( $sCurrentPageSlug == $sPageSlug )
        ) {
            return $this->_enqueueSRC( $aEnqueueItem );
        }

    }
}