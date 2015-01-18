<?php
/**
 * @package         Admin Page Framework Loader
 * @copyright       Copyright (c) 2015, Michael Uno
 * @license         http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since           3.5.0
*/

/** 
 * Retrieves an array of RSS feed items.
 */
class AdminPageFrameworkLoader_FeedList {

    /**
     * A container array that stores fetched feed items.
     */
    protected $_aFeedItems = array();   
    
    /**
     * Stores the feed object. 
     */
    protected $_oFeed;    
    

    /**
     * Stores the target URLs.
     */
    protected $_aURLs = array();
    
    /**
     * Sets up properties.
     */
    public function __construct( $asURLs=array() ) {
        
        $this->_aURLs = is_array( $asURLs ) 
            ? $asURLs 
            : ( empty( $asURLs )
                ? array()
                : ( array ) $asURLs
            );

    }
    
    /**
     * 
     * @return      array       Feed items.
     */
    public function get( $asURLs=array(), $iItems=0, $bCacheRenew=false ) {
        
        $_aOutput   = array();
        $asURLs     = empty( $asURLs ) ? $this->_aURLs : $asURLs;
        $_aURLs     = is_array( $asURLs ) ? $asURLs : ( array ) $asURLs ;
        
        if ( empty( $_aURLs ) ) {
            return $_aOutput;
        }
        
        $_sURLID    = md5( serialize( $_aURLs ) );
            
        // If it's out of stock, fill the array by fetching the feed.
        if ( ! isset( $this->_aFeedItems[ $_sURLID ] ) || empty( $this->_aFeedItems[ $_sURLID ] ) ) {
                        
            /* 
             * When an array of urls is passed to the Simple Pie's set_feed_url() method, the memory usage increases largely.
             * So fetch the feeds one by one per url and store the output into an array.
             */
            foreach( $_aURLs as $_sURL ) {
                                
                $_oFeed = $this->_getFeedObject( 
                    $_sURL, 
                    null, 
                    $bCacheRenew 
                        ? 0 
                        : 3600 
                );
                
                foreach ( $_oFeed->get_items() as $_oItem ) {
                    $this->_aFeedItems[ $_sURLID ][ $_oItem->get_title() ] = array( 
                        'content'        => $_oItem->get_content(),
                        'description'    => $_oItem->get_description(),
                        'title'          => $_oItem->get_title(),
                        'date'           => $_oItem->get_title(),
                        'author'         => $_oItem->get_date( 'j F Y, g:i a' ),
                        'link'           => $_oItem->get_permalink(),    // get_link() may be used as well        
                    );
                }
                
                // For PHP below 5.3 to release the memory.
                $_oFeed->__destruct(); // Do what PHP should be doing on it's own.
                unset( $_oFeed ); 
                
            }
                    
        }
        
        $_aOutput = $this->_aFeedItems[ $_sURLID ];
        if ( $iItems  ) {
            array_splice( $_aOutput, $iItems );
        }
            
        return $_aOutput;
        
    }
    
        /**
         * Returns a SimplePie object that handles retrieving RSS outputs.
         * 
         * @param       array|string    $asUrls
         * @param       integer         $iItem
         * @param       integer         $iCacheDuration      Seconds to store caches. 60 seconds * 60 = 1 hour, 1800 = 30 minutes
         */
        protected function _getFeedObject( $asUrls, $iItem=0, $iCacheDuration=3600 ) {    
            
            // Reuse the object that already exists. This conserves the memory usage.
            $this->_oFeed = isset( $this->_oFeed ) 
                ? $this->_oFeed 
                : new AdminPageFrameworkLoader_SimplePie();
            
            // Set sort type.
            $this->_oFeed->set_sortorder( 'date' );

            // Set urls
            $this->_oFeed->set_feed_url( $asUrls );    
            if ( $iItem ) {
                $this->_oFeed->set_item_limit( $iItem );    
            }
            
            // This should be set after defining $urls
            $this->_oFeed->set_cache_duration( $iCacheDuration );    
            
            $this->_oFeed->set_stupidly_fast( true );
            
            // If the cache lifetime is explicitly set to 0, do not trigger the background renewal cache event
            if ( 0 == $iCacheDuration ) {
                $this->_oFeed->setBackground( true );    // setting it true will be considered the background process; thus, it won't trigger the renewal event.
            }
            
            // set_stupidly_fast() disables this internally so turn it on manually because it will trigger the custom sort method
            $this->_oFeed->enable_order_by_date( true );    
            $this->_oFeed->init();            
            return $this->_oFeed;
            
        } 
    
}