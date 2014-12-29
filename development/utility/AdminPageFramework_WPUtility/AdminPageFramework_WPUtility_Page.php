<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods to detect types of admin pages which use WordPress functions and classes.
 *
 * @since 2.0.0
 * @extends AdminPageFramework_Utility
 * @package AdminPageFramework
 * @subpackage Utility
 * @internal
 */
class AdminPageFramework_WPUtility_Page extends AdminPageFramework_WPUtility_HTML {
    
    /**
     * Attempts to retrieve the current admin post type
     * 
     * @since 3.0.0
     */
    static public function getCurrentPostType() {
                 
        static $_sCurrentPostType;
        
        // Since the current page will be the same throughout the execution of the script, if once it's found, there is no need to find it again.
        if ( $_sCurrentPostType ) { 
            return $_sCurrentPostType; 
        }
        
        // Check to see if a post object exists
        if ( isset( $GLOBALS['post'], $GLOBALS['post']->post_type ) && $GLOBALS['post']->post_type ) {
            $_sCurrentPostType = $GLOBALS['post']->post_type;
            return $_sCurrentPostType;
        }
         
        // Check if the current type is set
        if ( isset( $GLOBALS['typenow'] ) && $GLOBALS['typenow'] ) {
            $_sCurrentPostType = $GLOBALS['typenow'];
            return $_sCurrentPostType;
        }
         
        // Check to see if the current screen is set
        if ( isset( $GLOBALS['current_screen']->post_type ) && $GLOBALS['current_screen']->post_type ) {
            $_sCurrentPostType = $GLOBALS['current_screen']->post_type;
            return $_sCurrentPostType;
        }
         
        // Finally make a last ditch effort to check the URL query for type
        if ( isset( $_REQUEST['post_type'] ) ) {
            $_sCurrentPostType = sanitize_key( $_REQUEST['post_type'] );
            return $_sCurrentPostType;
        }
        
        // If the post is set, find the post type from it. If will perform a database query if necessary.
        if ( isset( $_GET['post'] ) && $_GET['post'] ) {
            $_sCurrentPostType = get_post_type( $_GET['post'] );
            return $_sCurrentPostType;
        }
        
        return null;
        
    }

    /**
     * Checks if the current page is a custom taxonomy of the give post types.
     * 
     * @since 3.1.3
     * 
     * @param array|string The post type slug(s) to check. If this is empty, the method just checks the current page is a taxonomy page.
     * @return boolean
     */    
    static public function isCustomTaxonomyPage( $asPostTypes=array() ) {
        
        $_aPostTypes = is_array( $asPostTypes ) ? $asPostTypes : empty( $asPostTypes ) ? array() : array( $asPostTypes ) ;
        
        if ( ! in_array( self::getPageNow(), array( 'tags.php', 'edit-tags.php', ) ) ) {
            return false;
        }
        
        // If the parameter is empty, 
        if ( empty( $_aPostTypes ) ) { return true; }
        
        // If the parameter of the post type is set and it's in the given post types, 
        return in_array( self::getCurrentPostType(), $_aPostTypes );

    }
    
    /**
     * Checks if the current page is a post editing page that belongs to the given post type(s).
     * 
     * @since 3.0.0
     * @param array|string The post type slug(s) to check. If this is empty, the method just checks the current page is a post definition page.
     * Otherwise, it will check if the page belongs to the given post type(s).
     * @return boolean
     */
    static public function isPostDefinitionPage( $asPostTypes=array() ) {
        
        $_aPostTypes = is_array( $asPostTypes ) ? $asPostTypes : empty( $asPostTypes ) ? array() : array( $asPostTypes );

        // If it's not the post definition page, 
        if ( ! in_array( self::getPageNow(), array( 'post.php', 'post-new.php', ) ) ) return false;

        // If the parameter is empty, 
        if ( empty( $_aPostTypes ) ) return true;

        // If the parameter of the post type is set and it's in the given post types, 
        return in_array( self::getCurrentPostType(), $_aPostTypes );
        
    }     
    
    /**
     * Checks if the current page is in the post listing page of the given page slug(s).
     * 
     * @since 3.0.0
     */
    static public function isPostListingPage( $asPostTypes=array() ) {
                
        if ( 'edit.php' != self::getPageNow() ) return false;
        
        $_aPostTypes = is_array( $asPostTypes ) ? $asPostTypes : empty( $asPostTypes ) ? array() : array( $asPostTypes ) ;
        
        if ( ! isset( $_GET['post_type'] )  ) return in_array( 'post', $_aPostTypes );

        return in_array( $_GET['post_type'], $_aPostTypes );
        
    }
    
    /**
     * Stores the cache of the pagenow value.
     */
    static private $_sPageNow;
    
    /**
     * Returns the base name of the current url.
     * 
     * When a plugin is network activated, the global $pagenow variable sometimes is not set. Some framework's objects rely on the value of it.
     * So this method will provide an alternative mean when it is not set.
     * 
     * @since 3.0.5
     */
    static public function getPageNow() {
        
        if ( isset( self::$_sPageNow ) ) {
            return self::$_sPageNow;
        }
        
        // If already set, use that.
        if ( isset( $GLOBALS['pagenow'] ) ) {
            self::$_sPageNow = $GLOBALS['pagenow'];
            return self::$_sPageNow;
        }
                
        // Front-end
        if ( ! is_admin() ) {
            if ( preg_match( '#([^/]+\.php)([?/].*?)?$#i', $_SERVER['PHP_SELF'], $_aMatches ) ) {
                self::$_sPageNow = strtolower( $_aMatches[ 1 ] );
                return self::$_sPageNow;
            }
            self::$_sPageNow = 'index.php';
            return self::$_sPageNow;
        }
        
        // Back-end - wp-admin pages are checked more carefully     
        if ( is_network_admin() )
            preg_match( '#/wp-admin/network/?(.*?)$#i', $_SERVER['PHP_SELF'], $_aMatches );
        elseif ( is_user_admin() )
            preg_match( '#/wp-admin/user/?(.*?)$#i', $_SERVER['PHP_SELF'], $_aMatches );
        else
            preg_match( '#/wp-admin/?(.*?)$#i', $_SERVER['PHP_SELF'], $_aMatches );
            
        $_sPageNow = $_aMatches[ 1 ];
        $_sPageNow = trim( $_sPageNow, '/' );
        $_sPageNow = preg_replace( '#\?.*?$#', '', $_sPageNow );
        if ( '' === $_sPageNow || 'index' === $_sPageNow || 'index.php' === $_sPageNow ) {
            self::$_sPageNow = 'index.php';
            return self::$_sPageNow;
        } 
            
        preg_match( '#(.*?)(/|$)#', $_sPageNow, $_aMatches );
        $_sPageNow = strtolower( $_aMatches[1] );
        if ( '.php' !== substr( $_sPageNow, -4, 4 ) ) {
            $_sPageNow .= '.php'; // for Options +Multiviews: /wp-admin/themes/index.php (themes.php is queried)
            self::$_sPageNow = $_sPageNow;
        }
        return self::$_sPageNow;     
        
    }
    
    /**
     * Returns the current loading screen id.
     * 
     * @since 3.1.0
     * @return string The found screen ID.
     */
    static public function getCurrentScreenID() {
        
        $_oScreen = get_current_screen();
        if ( is_string( $_oScreen ) ) {
            $_oScreen = convert_to_screen( $_oScreen );
        }
        if ( isset( $_oScreen->id ) ) {
            return $_oScreen->id;
        }
        
        if ( isset( $GLBOALS['page_hook'] ) ) {
            return is_network_admin() 
                ? $GLBOALS['page_hook'] . '-network'
                : $GLBOALS['page_hook'];
        }
        
        return '';
        
    }
      
}