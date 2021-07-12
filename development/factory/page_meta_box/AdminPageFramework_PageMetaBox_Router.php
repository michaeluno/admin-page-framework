<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides routing methods for creating meta boxes in pages added by the framework.
 *
 * @abstract
 * @since           3.0.4
 * @package         AdminPageFramework/Factory/PageMetaBox
 * @internal
 * @extends         AdminPageFramework_MetaBox_View
 */
abstract class AdminPageFramework_PageMetaBox_Router extends AdminPageFramework_MetaBox_View {

    /**
     * @var AdminPageFramework_Resource_page_meta_box
     */
    protected $oResource;

    /**
     * Determines whether the meta box class components should be loaded in the currently loading page.
     *
     * @since       3.1.3
     * @internal
     */
    protected function _isInstantiatable() {

        if ( $this->_isWordPressCoreAjaxRequest() ) {
            return false;
        }
        return true;

    }

    /**
     * Determines whether the meta box belongs to the loading page.
     *
     * @since       3.0.3
     * @since       3.2.0   Changed the scope to `public` from `protected` as the head tag object will access it.
     * @since       3.8.14  Changed the visibility scope to `protected` from `public` as there is the `isInThePage()` public method.
     * @remak       In-page tabs cannot be checked because this method can be called earlier than `setUp()` or `load()` of the admin page factory class which defines added tabs.
     * @internal
     */
    protected function _isInThePage() {

        if ( ! $this->oProp->bIsAdmin ) {
            return false;
        }

        // Foe admin-ajax.php, aQuery holds the referer's GET URL parameters so the check covers the cases of ajax calls.
        $_sPageSlug = $this->oUtil->getElement( $this->oProp->aQuery, array( 'page' ), '' );
        if ( ! $this->___isAddedPage( $_sPageSlug ) ) {
            return false;
        }
        return true;

    }
        /**
         * @return boolean
         * @since  3.8.19
         */
        private function ___isAddedPage( $sPageSlug ) {

            // Case: page slugs are stored with numeric index
            if ( ! $this->oUtil->isAssociative( $this->oProp->aPageSlugs ) ) {
                return in_array( $sPageSlug, $this->oProp->aPageSlugs, true );
            }

            // Case: page slugs are stored as keys
            return in_array( $sPageSlug, array_keys( $this->oProp->aPageSlugs ), true );

        }

    /**
     * Checks if the `admin-ajax.php` is called from the page that this meta box belongs to.
     * @sicne   3.8.19
     * @remark  since 3.8.14, the check for `admin-ajax.php` has been added.
     * @remark  No need to check anything as the above _isInThePage() method covers the cases of admin-ajax.php calls.
     * If this method needs to be called individually other than _isInThePage(), then add checks accordingly.
     * @return  boolean
     */
    protected function _isValidAjaxReferrer() {
        return true;
    }

}
