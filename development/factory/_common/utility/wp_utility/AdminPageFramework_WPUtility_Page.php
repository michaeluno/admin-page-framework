<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods which use WordPress functions and classes.
 * 
 * The methods in this class mainly deal with determining the type of loading page such as a post type, url base name etc.
 *
 * @since       2.0.0
 * @extends     AdminPageFramework_Utility
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal
 */
class AdminPageFramework_WPUtility_Page extends AdminPageFramework_WPUtility_HTML {
    
    /**
     * Attempts to retrieve the current admin post type
     * 
     * @remark      Caches the result.
     * @since       3.0.0
     * @return      string|null     The found post type slug.
     */
    static public function getCurrentPostType() {
                         
        if ( isset( self::$_sCurrentPostType ) ) {
            return self::$_sCurrentPostType;
        }
        self::$_sCurrentPostType = self::_getCurrentPostType();

        return self::$_sCurrentPostType;
        
    }
        /**
         * Since the current page will be the same throughout the execution of the script, 
         * once it's found, there is no need to find it again.
         */
        static private $_sCurrentPostType;
        
        /**
         * Attempts to retrieve the current admin post type
         * 
         * @remark      Does not cache the result.
         * @since       3.5.3
         * @return      string|null     The found post type or null if not found.
         */
        static private function _getCurrentPostType() {
            
            // the array element order is important, 
            // the one listed fist will be tried first.
            $_aMethodsToTry = array(
                'getPostTypeByTypeNow',
                'getPostTypeByScreenObject',
                'getPostTypeByREQUEST',
                'getPostTypeByPostObject',  // 3.6.0+ Moved to the last as it is not reliable.
            );
            foreach ( $_aMethodsToTry as $_sMethodName ) {
                $_sPostType = call_user_func( array( __CLASS__, $_sMethodName ) );
                if ( $_sPostType ) {
                    return $_sPostType;
                }
            }

            return null;
          
        }
            /**#@+
             * Attempts to find a current post type.
             * @internal
             * @return      null|string
             * @callback    function        call_user_func
             * @since       3.5.3
             */
            static public function getPostTypeByTypeNow() {
                if ( isset( $GLOBALS[ 'typenow' ] ) && $GLOBALS[ 'typenow' ] ) {
                    return $GLOBALS[ 'typenow' ];
                }
            }
            static public function getPostTypeByScreenObject() {
                if (
                    isset( $GLOBALS[ 'current_screen' ]->post_type )
                    && $GLOBALS[ 'current_screen' ]->post_type
                ) {
                    return $GLOBALS[ 'current_screen' ]->post_type;
                }
            }
            /**
             * Tries to find the post type from the URL query for type.
             */
            static public function getPostTypeByREQUEST() {
                if ( isset( $_REQUEST[ 'post_type' ] ) ) {
                    return sanitize_key( $_REQUEST[ 'post_type' ] );
                }
                if ( isset( $_GET[ 'post' ] ) && $_GET[ 'post' ] ) {
                    // It will perform a database query.
                    return get_post_type( $_GET[ 'post' ] );
                }
            }
                
            /**
             * @remark      Checking with the global post object is not reliable because it gets modified when the `WP_Query::the_post()` method is performed.
             */
            static public function getPostTypeByPostObject() {
                if (
                    isset( $GLOBALS[ 'post' ]->post_type )
                    && $GLOBALS[ 'post' ]->post_type
                ) {
                    return $GLOBALS[ 'post' ]->post_type;
                }
            }
            /**#@-*/

    /**
     * Checks if the current page is a custom taxonomy of the give post types.
     * 
     * @since       3.1.3
     * @param       array|string        The post type slug(s) to check. If this is empty, the method just checks the current page is a taxonomy page.
     * @return      boolean
     */
    static public function isCustomTaxonomyPage( $asPostTypes=array() ) {
        
        if ( ! in_array( self::getPageNow(), array( 'tags.php', 'edit-tags.php', ) ) ) {
            return false;
        }

        return self::isCurrentPostTypeIn( $asPostTypes );

    }
    
    /**
     * Checks if the current page is a post editing page that belongs to the given post type(s).
     * 
     * @since       3.0.0
     * @param       array|string        The post type slug(s) to check. If this is empty, the method just checks the current page is a post definition page.
     * Otherwise, it will check if the page belongs to the given post type(s).
     * @return      boolean
     */
    static public function isPostDefinitionPage( $asPostTypes=array() ) {
        
        // If it's not the post definition page, 
        if ( ! in_array( self::getPageNow(), array( 'post.php', 'post-new.php', ) ) ) {
            return false;
        }

        return self::isCurrentPostTypeIn( $asPostTypes );
          
    }
        
    /**
     * Checks if the curently loading page's post type is of the given post types.
     * 
     * @param       array|string        $asPostTypes        The post type slugs that the current post type belongs to. 
     * @return      boolean             True if the current post type belongs to the given post types. If an empty value is passed to the first parameter, returns always true.
     * @since       3.5.3
     */
    static public function isCurrentPostTypeIn( $asPostTypes ) {
        
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
                
        if ( 'edit.php' != self::getPageNow() ) {
            return false;
        }
        
        $_aPostTypes = self::getAsArray( $asPostTypes );
        
        if ( ! isset( $_GET[ 'post_type' ] )  ) {
            return in_array( 'post', $_aPostTypes );
        }

        return in_array( $_GET[ 'post_type' ], $_aPostTypes );
        
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
        if ( isset( $GLOBALS[ 'pagenow' ] ) ) {
            self::$_sPageNow = $GLOBALS[ 'pagenow' ];

            return self::$_sPageNow;
        }
                        
        $_aMethodNames = array(
            0 => '_getPageNow_FrontEnd',
            1 => '_getPageNow_BackEnd',
        );
        $_sMethodName  = $_aMethodNames[ ( integer ) is_admin() ];
        self::$_sPageNow = self::$_sMethodName();

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
            if ( preg_match( '#([^/]+\.php)([?/].*?)?$#i', $_SERVER[ 'PHP_SELF' ], $_aMatches ) ) {
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
             * Return the base part of the currently loading admin url.
             * @since       3.5.3
             * @internal
             * return       string      The base part of the currently loading admin url.
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
                preg_match( $_sNeedle, $_SERVER[ 'PHP_SELF' ], $_aMatches );

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
        
        if ( isset( $GLBOALS[ 'page_hook' ] ) ) {
            return is_network_admin()
                ? $GLBOALS[ 'page_hook' ] . '-network'
                : $GLBOALS[ 'page_hook' ];
        }
        
        return '';
        
    }
    
    /**
     * Checks if a meta box exists in the current page.
     * 
     * @since       3.7.0
     * @return      boolean
     */
    static public function doesMetaBoxExist( $sContext='' ) {
        
        $_aDimensions = array( 'wp_meta_boxes', $GLOBALS[ 'page_hook' ] );
        if ( $sContext ) {
            $_aDimensions[] = $sContext;
        }
        $_aSideMetaBoxes = self::getElementAsArray(
            $GLOBALS,
            $_aDimensions
        );

        return count( $_aSideMetaBoxes ) > 0;
        
    }
        
    /**
     * Returns the number of columns in the currently loading page.
     * @return      integer     The number of columns that the current screen displays.
     * @since       3.6.3
     */
    static public function getNumberOfScreenColumns() {
        return get_current_screen()->get_columns();
    }

}
