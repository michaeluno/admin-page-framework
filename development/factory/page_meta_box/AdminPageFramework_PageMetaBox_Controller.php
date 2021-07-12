<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides controller methods for creating meta boxes in pages added by the framework.
 *
 * @abstract
 * @since       3.0.4
 * @package     AdminPageFramework/Factory/PageMetaBox
 */
abstract class AdminPageFramework_PageMetaBox_Controller extends AdminPageFramework_PageMetaBox_View {

    /**
     * Enqueues styles by page slug and tab slug.
     *
     * @since 3.0.0
     * @since 3.8.31 Removed parameters to be compatible with the base class.
     * @return array Added stylesheet handle IDs.
     */
    public function enqueueStyles( /*$aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() */ ) {
        if ( ! method_exists( $this->oResource, '_enqueueResourcesByType' ) ) {
            return array();
        }
        $_aParams = func_get_args() + array( array(), '', '', array() );
        return $this->oResource->_enqueueResourcesByType(
            $_aParams[ 0 ],
            array(
                'sPageSlug' => $_aParams[ 1 ],
                'sTabSlug'  => $_aParams[ 2 ],
            ) + $_aParams[ 3 ],
            'style'
        );
    }
    /**
     * Enqueues a style by page slug and tab slug.
     *
     * @since 3.0.0
     * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_style
     * @param string The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
     * @param string (optional) The page slug that the stylesheet should be added to. If not set, it applies to all the pages created by the framework.
     * @param string (optional) The tab slug that the stylesheet should be added to. If not set, it applies to all the in-page tabs in the page.
     * @param array (optional) The argument array for more advanced parameters.
     * @return string The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     * @since 3.8.31 Removed parameters to be compatible with the base class.
     */
    public function enqueueStyle( /* $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() */ ) {
        if ( ! method_exists( $this->oResource, '_addEnqueuingResourceByType' ) ) {
            return '';
        }
        $_aParams = func_get_args() + array( '', '', '', array() );
        return $this->oResource->_addEnqueuingResourceByType(
            $_aParams[ 0 ],
            array(
                'sPageSlug' => $_aParams[ 1 ],
                'sTabSlug'  => $_aParams[ 2 ],
            ) + $_aParams[ 3 ],
            'style'
        );
    }
    /**
     * Enqueues scripts by page slug and tab slug.
     *
     * @since 2.1.5
     * @since 3.8.31 Removed parameters to be compatible with the base class.
     * @return array Added script handle IDs
     */
    public function enqueueScripts( /* $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() */ ) {
        if ( ! method_exists( $this->oResource, '_enqueueResourcesByType' ) ) {
            return array();
        }
        $_aParams = func_get_args() + array( array(), '', '', array() );
        return $this->oResource->_enqueueResourcesByType(
            $_aParams[ 0 ],
            array(
                'sPageSlug' => $_aParams[ 1 ],
                'sTabSlug'  => $_aParams[ 2 ],
            ) + $_aParams[ 3 ],
            'script'
        );
    }
    /**
     * Enqueues a script by page slug and tab slug.
     *
     * @since 3.0.0
     * @since 3.8.31 Removed parameters to be compatible with the base class.
     * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_script
     * @param string The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
     * @param string (optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
     * @param string (optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
     * @param             array (optional) The argument array for more advanced parameters.
     * @return string The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     */
    public function enqueueScript( /* $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() */ ) {
        if ( ! method_exists( $this->oResource, '_addEnqueuingResourceByType' ) ) {
            return '';
        }
        $_aParams = func_get_args() + array( '', '', '', array() );
        return $this->oResource->_addEnqueuingResourceByType(
            $_aParams[ 0 ],
            array(
                'sPageSlug' => $_aParams[ 1 ],
                'sTabSlug'  => $_aParams[ 2 ],
            ) + $_aParams[ 3 ],
            'script'
        );
    }

}