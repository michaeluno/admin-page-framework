<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides controller methods for creating meta boxes in pages added by the framework.
 * 
 * @abstract
 * @since       3.0.4
 * @package     AdminPageFramework
 * @subpackage  PageMetaBox
 */
abstract class AdminPageFramework_MetaBox_Page_Controller extends AdminPageFramework_MetaBox_Page_View {
    
    /**
     * Enqueues styles by page slug and tab slug.
     * 
     * @since 3.0.0
     */
    public function enqueueStyles( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
        if ( method_exists( $this->oResource, '_enqueueStyles' ) ) {
            return $this->oResource->_enqueueStyles( $aSRCs, $sPageSlug, $sTabSlug, $aCustomArgs );
        }
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
     */    
    public function enqueueStyle( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
        if ( method_exists( $this->oResource, '_enqueueStyle' ) ) {
            return $this->oResource->_enqueueStyle( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );     
        }
    }
    /**
     * Enqueues scripts by page slug and tab slug.
     * 
     * @since 2.1.5
     */
    public function enqueueScripts( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
        if ( method_exists( $this->oResource, '_enqueueScripts' ) ) {
            return $this->oResource->_enqueueScripts( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
        }
    }    
    /**
     * Enqueues a script by page slug and tab slug.
     * 
     * @since 3.0.0     
     * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_script
     * @param string The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
     * @param string (optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
     * @param string (optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
     * @param             array (optional) The argument array for more advanced parameters.
     * @return string The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     */
    public function enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {    
        if ( method_exists( $this->oResource, '_enqueueScript' ) ) {
            return $this->oResource->_enqueueScript( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
        }
    }
    
}