<?php
/**
    Extends the SimplePie library. 
 * 
 * @package     Admin Page Framework Loader
 * @copyright   Copyright (c) 2015, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
*/

/*
 * Custom Hooks
 * - admin_page_framework_loader_action_simplepie_renew_cache : the event action that renew caches in the background.
 * - SimplePie_filter_cache_transient_lifetime_{FileID} : applies to cache transients. FileID is md5( $url ).
 * 
 * Global Variables
 * - $arrSimplePieCacheModTimestamps : stores mod timestamps of cached data. This will be stored in a transient when WordPress exits, 
 *   which prevents multiple calls of get_transiet() that performs a database query ( slows down the page load ).
 * - $arrSimplePieCacheExpiredItems : stores expired cache items' file IDs ( md5( $url ) ). This will be saved in the transient at the WordPress shutdown action event.
 *   the separate cache renewal event with WP Cron will read it and renew the expired caches.
 * 
 * */

/**
 * Make sure that SimplePie has been already loaded. This is very important. Without this line, the cache setting breaks. 
 * @remark  Do not include class-simplepie.php, which causes the unknown class warning.
 */
if ( ! class_exists( 'SimplePie' ) ) { 
    include( ABSPATH . WPINC . '/class-feed.php' );        
}

// If the WordPress version is below 3.5, which uses SimplePie below 1.3,
if ( version_compare( get_bloginfo( 'version' ) , '3.5', "<" ) ) {    

    class AdminPageFrameworkLoader_SimplePie__ extends SimplePie {
        
        public static $sortorder = 'random';
        public function sort_items( $a, $b ) {

            // Sort 
            // by date
            if ( self::$sortorder == 'date' ) 
                return $a->get_date( 'U' ) <= $b->get_date( 'U' );
            // by title ascending
            if ( self::$sortorder == 'title' ) 
                return self::sort_items_by_title( $a, $b );
            // by title decending
            if ( self::$sortorder == 'title_descending' ) 
                return self::sort_items_by_title_descending( $a, $b );
            // by random 
            return rand( -1, 1 );    
            
        }        
    }
    
} else {
    
    class AdminPageFrameworkLoader_SimplePie__ extends SimplePie {
        
        public static $sortorder = 'random';
        public static function sort_items( $a, $b ) {

            // Sort 
            // by date
            if ( self::$sortorder == 'date' ) 
                return $a->get_date( 'U' ) <= $b->get_date( 'U' );
            // by title ascending
            if ( self::$sortorder == 'title' ) 
                return self::sort_items_by_title( $a, $b );
            // by title decending
            if ( self::$sortorder == 'title_descending' ) 
                return self::sort_items_by_title_descending( $a, $b );
            // by random 
            return rand( -1, 1 );    
            
        }        
    }    

}

/**
 * 
 * @uses    AdminPageFramework_WPUtility
 */
class AdminPageFrameworkLoader_SimplePie extends AdminPageFrameworkLoader_SimplePie__ {
    
    public static $sortorder = 'random';
    public static $bKeepRawTitle = false;
    public static $strCharEncoding = 'UTF-8';
    var $vSetURL;    // stores the feed url(s) set by the user.
    var $fIsBackgroundProcess = false;        // indicates whether it is from the event action ( background call )
    var $numCacheLifetimeExpand = 100;
    protected $strPluginKey = 'FTWSFeedMs';
    
    public function __construct() {
    
        // Set up the global arrays. Consider the cases that multiple instances of this object are created so the arrays may have been already created.
        // - This stores real mod timestamps.
        $GLOBALS['arrSimplePieCacheModTimestamps'] = isset( $GLOBALS['arrSimplePieCacheModTimestamps'] ) && is_array( $GLOBALS['arrSimplePieCacheModTimestamps'] ) ? $GLOBALS['arrSimplePieCacheModTimestamps'] : array();
        $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ] = isset( $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ] ) && is_array( $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ] ) 
            ? $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ]
            : AdminPageFramework_WPUtility::getTransient( $this->strPluginKey, array() );
            
        // - this stores expired cache items.
        $GLOBALS['arrSimplePieCacheExpiredItems'] = isset( $GLOBALS['arrSimplePieCacheExpiredItems'] ) && is_array( $GLOBALS['arrSimplePieCacheExpiredItems'] ) ? $GLOBALS['arrSimplePieCacheExpiredItems'] : array();
        $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] = isset( $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] ) && is_array( $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] ) ? $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] : array();
        
        // Schedule the transient update task.
        add_action( 'shutdown', array( $this, 'updateCacheItems' ) );
        
        parent::__construct();
            
    }
    public function updateCacheItems() {    
    
        // Saves the global array, $arrSimplePieCacheModTimestamps, into the transient of the option table.
        // This is used to avoid multiple calls of set_transient() by the cache class.
        if ( ! ( isset( $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ]['bIsCacheTransientSet'] ) && $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ]['bIsCacheTransientSet'] ) ) {
            unset( $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ]['bIsCacheTransientSet'] ); // remove the unnecessary data.
            AdminPageFramework_WPUtility::setTransient( $this->strPluginKey, $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ], $this->cache_duration * $this->numCacheLifetimeExpand );
            $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ]['bIsCacheTransientSet'] = true;
        }
        
        $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] = array_unique( $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] );
        if ( count( $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] ) > 0 ) {
            $this->scheduleCacheRenewal( $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] );
        }
        

    }
    protected function scheduleCacheRenewal( $arrURLs ) {
        
        // Schedules the action to run in the background with WP Cron.
        if ( wp_next_scheduled( 'admin_page_framework_loader_action_simplepie_renew_cache', array( $arrURLs ) ) ) { return; }
        wp_schedule_single_event( time() , 'admin_page_framework_loader_action_simplepie_renew_cache', array( $arrURLs ) );
                
    }
    
    /**
     * This is a callback for the filter that sets cache duration for the SimplePie cache object.
     */
    public function setCacheTransientLifetime( $intLifespan, $strFileID=null ) {
        
        return isset( $this->cache_duration ) 
            ? $this->cache_duration 
            : 0;
        
    }
    public function setCacheTransientLifetimeByGlobalKey( $intLifespan, $strKey=null ) {
        
        // This is a callback for the filter that sets cache duration for the SimplePie cache object.
        
        // If the key is not the one set by this class, it could be some other script's ( plugin's ) filtering item.
        if ( $strKey != $this->strPluginKey ) return $intLifespan;    
        
        return isset( $this->cache_duration ) ? $this->cache_duration : 0;
        
    }
    
    /*
     * For background cache renewal task.
     * */        
    public function set_feed_url( $vURL ) {
        
        $this->vSetURL = $vURL;    // array or string
        
        // Hook the cache lifetime filter
        foreach( ( array ) $vURL as $strURL ) {
            add_filter( 'SimplePie_filter_cache_transient_lifetime_' . md5( $strURL ), array( $this, 'setCacheTransientLifetime' ) );
        }
        add_filter( 'SimplePie_filter_cache_transient_lifetime_' . $this->strPluginKey, array( $this, 'setCacheTransientLifetimeByGlobalKey' ) );
        
        return parent::set_feed_url( $vURL );
        
    }
    public function init() {

        // Setup Caches
        $this->enable_cache( true );
        
        // force the cache class to the custom plugin cache class
        $this->set_cache_class( 'AdminPageFrameworkLoader_Cache' );
        $this->set_file_class( 'WP_SimplePie_File' );
                        
        if ( isset( $this->vSetURL ) && ! $this->fIsBackgroundProcess ) {
            
            foreach ( ( array) $this->vSetURL as $strURL ) {
                
                $strFileID = md5( $strURL );
                $intModTimestamp = isset( $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ][ $strFileID ] )
                    ? $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ][ $strFileID ] 
                    : 0;
                if ( $intModTimestamp + $this->cache_duration < time() ) {
                    $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ][] = $strURL;
                }     

            }
                                            
        }    
        
        return parent::init();
        
    }
    public function setBackground( $fIsBackgroundProcess=false ) {
        $this->fIsBackgroundProcess = $fIsBackgroundProcess;
    }

    /*
     * For sort
     * */
    public function set_sortorder( $sortorder ) {
        self::$sortorder = $sortorder;
    }
    public function set_keeprawtitle( $bKeepRawTitle ) {
        self::$bKeepRawTitle = $bKeepRawTitle;        
    }
    public function set_charset_for_sort( $strCharEncoding ) {
        self::$strCharEncoding = $strCharEncoding;        
    }

    public static function sort_items_by_title( $a, $b ) {
        $a_title = ( self::$bKeepRawTitle ) ? $a->get_title() : preg_replace('/#\d+?:\s?/i', '', $a->get_title());
        $b_title = ( self::$bKeepRawTitle ) ? $b->get_title() : preg_replace('/#\d+?:\s?/i', '', $b->get_title());
        $a_title = html_entity_decode( trim( strip_tags( $a_title ) ), ENT_COMPAT | ENT_HTML401, self::$strCharEncoding );
        $b_title = html_entity_decode( trim( strip_tags( $b_title ) ), ENT_COMPAT | ENT_HTML401, self::$strCharEncoding );
        return strnatcasecmp( $a_title, $b_title );    
    }
    public static function sort_items_by_title_descending( $a, $b ) {
        $a_title = ( self::$bKeepRawTitle ) ? $a->get_title() : preg_replace('/#\d+?:\s?/i', '', $a->get_title());
        $b_title = ( self::$bKeepRawTitle ) ? $b->get_title() : preg_replace('/#\d+?:\s?/i', '', $b->get_title());
        $a_title = html_entity_decode( trim( strip_tags( $a_title ) ), ENT_COMPAT | ENT_HTML402, self::$strCharEncoding );
        $b_title = html_entity_decode( trim( strip_tags( $b_title ) ), ENT_COMPAT | ENT_HTML402, self::$strCharEncoding );
        return strnatcasecmp( $b_title, $a_title );
    }
    

    function set_force_cache_class( $class = 'AdminPageFrameworkLoader_Cache' ) {
        $this->cache_class = $class;
    }
    function set_force_file_class( $class = 'SimplePie_File' ) {
        $this->file_class = $class;
    }    
}

class AdminPageFrameworkLoader_Cache extends SimplePie_Cache {
    
    /**
     * Create a new SimplePie_Cache object
     *
     * @static
     * @access public
     */
    function create( $location, $filename, $extension ) {
        return new AdminPageFrameworkLoader_Feed_Cache_Transient( $location, $filename, $extension );
    }
    
}
class AdminPageFrameworkLoader_Feed_Cache_Transient {
    
    var $strTransientName;
    var $iLifetime = 43200; // Default cache lifetime of 12 hours. This should be overridden by the filter callback function. 
    var $numExpand = 100;
    var $strPluginKey = 'FTWSFeedMs';
    
    protected $strFileID;    // stores the file name given to the constructor.
    
    public function __construct( $location, $strFileID, $extension ) {
        
        /* 
         * Parameters:
         * - $location ( not used in this class ) : './cache'
         * - $strFileID : md5( $url )    e.g. b22d9dad80577a8e66a230777d91cc6e // <-- the hash type may be changed by the user.
         * - $extension ( not used in this class ) : spc
         */
        
        // $strFileID should not be empty but I've seen a case that happened with v3.4.x or below.
        $this->strFileID = empty( $strFileID ) ? $this->strPluginKey . '_a_file' : $strFileID;    
        
        $this->strTransientName = $this->strPluginKey . '_' . $this->strFileID;
        $this->iLifetime = apply_filters( 
            'SimplePie_filter_cache_transient_lifetime_' . $this->strFileID, 
            $this->iLifetime,     // it barely expires by itself
            $this->strFileID
        );
        
    }

    public function save( $data ) {
        
        if ( is_a( $data, 'SimplePie' ) ) {
            $data = $data->data;
        }

        // $GLOBALS['arrSimplePieCacheModTimestamps'] should be already created by the caller (parent) custom SimplePie class.
        $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ][ $this->strFileID ] = time();    

        // make it 100 times longer so that it barely gets expires by itself
        AdminPageFramework_WPUtility::setTransient( $this->strTransientName, $data, $this->iLifetime * $this->numExpand );
        return true;
        
    }
    public function load() {        

        // If this returns an empty value, SimplePie will fetch the feed.
        if ( $this->iLifetime == 0 ) return null;  
        
        return AdminPageFramework_WPUtility::getTransient( $this->strTransientName );    // the stored cache data
        
    }
    public function mtime() {        
    
        // Here we are going to deceive SimplePie in order to force it to use the remaining cache and renew the cache in the background, not doing it right away.
        return isset( $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ][ $this->strFileID ] ) 
            ? time()    // return the current time so that SimplePie believes it's not expired yet.
            : 0;    // if the array key is not set, So pass 0 to tell that the cache needs to be created. 
            
    }
    public function touch() {
        $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ][ $this->strFileID ] = time(); 
        return true;
    }
    public function unlink() {
        unset( $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ][ $this->strFileID ] );
        AdminPageFramework_WPUtility::deleteTransient( $this->strTransientName );
        return true;
    }
}
