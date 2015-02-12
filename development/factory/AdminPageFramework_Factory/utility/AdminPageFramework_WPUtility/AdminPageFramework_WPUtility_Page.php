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
        
        if ( ! in_array( self::getPageNow(), array( 'tags.php', 'edit-tags.php', ) ) ) {
            return false;
        }
        
        $_aPostTypes = self::getAsArray( $asPostTypes );        
        
        // If the parameter is empty, 
        if ( empty( $_aPostTypes ) ) { 
            return true; 
        }
        
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
        
        // If it's not the post definition page, 
        if ( ! in_array( self::getPageNow(), array( 'post.php', 'post-new.php', ) ) ) { 
            return false;
        }

        $_aPostTypes = self::getAsArray( $asPostTypes );        
        
        // If the parameter is empty, 
        if ( empty( $_aPostTypes ) ) { 
            return true;
        }

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
     * @since       3.0.5
     */
    static public function getPageNow() {

        // Use the cache,
        if ( isset( self::$_sPageNow ) ) {
            return self::$_sPageNow;
        }
        
        // If already set, use that.
        if ( isset( $GLOBALS['pagenow'] ) ) {
            self::$_sPageNow = $GLOBALS['pagenow'];
            return self::$_sPageNow;
        }
                        
        self::$_sPageNow = is_admin() 
            ? self::_getPageNow_BackEnd() 
            : self::_getPageNow_FrontEnd();
            
        return self::$_sPageNow;          
        
    }
        /**
         * Returns the current page url base name.
         * 
         * Assumes the current page is not in the admin area.
         * 
         * @since       3.5.3
         * @return      string      The current page url base name.
         */
        static private function _getPageNow_FrontEnd() {
            if ( preg_match( '#([^/]+\.php)([?/].*?)?$#i', $_SERVER['PHP_SELF'], $_aMatches ) ) {
                return strtolower( $_aMatches[ 1 ] );
            }
            return 'index.php';                
        }    

        /**
         * Returns the current page url base name of the admin page.
         * 
         * Assumes the current page is in the admin area.
         * @remark      In admin area, it is checked carefully than in the fron-end.
         * @since       3.5.3
         * @return      string      The current page url base name of the admin page.
         */
        static private function _getPageNow_BackEnd() {
             
            $_sPageNow = self::_getPageNowAdminURLBasePath();
            if ( self::_isInAdminIndex( $_sPageNow ) ) {
                return 'index.php';
            }       
            
            preg_match( '#(.*?)(/|$)#', $_sPageNow, $_aMatches );
            $_sPageNow = strtolower( $_aMatches[ 1 ] );
            if ( '.php' !== substr( $_sPageNow, -4, 4 ) ) {
                $_sPageNow .= '.php'; // for Options +Multiviews: /wp-admin/themes/index.php (themes.php is queried)
            }
            return $_sPageNow;
            
        }   
            /**
             * Reurn the base part of the crurrently loading admin url.
             * @since       3.5.3
             * @internal
             * return       string      The base part of the crurrently loading admin url.
             */
            static private function _getPageNowAdminURLBasePath() {
                
                if ( is_network_admin() ) {
                    $_sNeedle = '#/wp-admin/network/?(.*?)$#i';
                }
                else if ( is_user_admin() ) {
                    $_sNeedle = '#/wp-admin/user/?(.*?)$#i';
                }
                else {
                    $_sNeedle = '#/wp-admin/?(.*?)$#i';
                }                
                preg_match( $_sNeedle, $_SERVER['PHP_SELF'], $_aMatches );
                return preg_replace( '#\?.*?$#', '', trim( $_aMatches[ 1 ], '/' ) );
                
            }
            /**
             * Checkes whether the passed base url name is of the admin index page.
             * @since       3.5.3
             * return       boolean      Whether the passed base url name is of the admin index page.
             */
            static private function _isInAdminIndex( $sPageNow ) {
                return in_array(
                    $sPageNow,
                    array( '', 'index', 'index.php' )
                );
            }
        
    
    /**
     * Returns the current loading screen id.
     * 
     * @since       3.1.0
     * @return      string      The found screen ID.
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