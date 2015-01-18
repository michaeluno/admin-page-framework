<?php
/**
 * @package         Admin Page Framework Loader
 * @copyright       Copyright (c) 2015, Michael Uno
 * @license         http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since           3.5.0
*/

/** 
 * Outputs a list of RSS feed items.
 */
class AdminPageFrameworkLoader_FeedLister {

    // Container arrays
    protected $arrFeedItems = array();    // stores fetched feed items.
    
    // Objects
    protected $oFeed;    // stores the feed object. 
    
    // Properties
    protected $strTransientPrefix = 'APFL_';
    
    /**
     * 
     * @return      array       Feed items.
     */
    public function get( $vURLs, $numItems=0, $fCacheRenew=false ) {
        
        $arrURLs = is_array( $vURLs ) ? $vURLs : ( array ) $vURLs ;
        $strURLID = md5( serialize( $arrURLs ) );
        
        if ( ! isset( $this->arrFeedItems[ $strURLID ] ) && $fCacheRenew == false ) {
            $this->arrFeedItems[ $strURLID ] = AdminPageFramework_WPUtility::getTransient( $this->strTransientPrefix . $strURLID, array() );
            unset( $this->arrFeedItems[ $strURLID ][0] );    // casting array causes the 0 key,
        }
            
        // If it's out of stock, fill the array by fetching the feed.
        if ( empty( $this->arrFeedItems[ $strURLID ] ) ) {    
                        
            // When an array of urls is passed to the Simple Pie's set_feed_url() method, the memory usage increases largely.
            // So fetch the feeds one by one per url and store the output into an array.
            foreach( $arrURLs as $strURL ) {
                                
                $oFeed = $this->getFeedObj( $strURL, null, $fCacheRenew ? 0 : 3600 );
                foreach ( $oFeed->get_items() as $oItem )     // foreach ( $oFeed->get_items( 0, $numItems * 3 ) as $item ) does not change the memory usage
                    $this->arrFeedItems[ $strURLID ][ $oItem->get_title() ] = array( 
                        'strContent'     => $oItem->get_content(),
                        'description'    => $oItem->get_description(),
                        'title'          => $oItem->get_title(),
                        'strDate'        => $oItem->get_title(),
                        'strAuthor'      => $oItem->get_date( 'j F Y, g:i a' ),
                        'strLink'        => $oItem->get_permalink(),    // get_link() may be used as well        
                    );
                
                // For PHP below 5.3 to release the memory.
                $oFeed->__destruct(); // Do what PHP should be doing on it's own.
                unset( $oFeed ); 
                
            }
        
            // This life span should be little longer than the feed cache life span, which is 1700.
            AdminPageFramework_WPUtility::setTransient( $this->strTransientPrefix . $strURLID, $this->arrFeedItems[ $strURLID ], 1800 );    // 30 minutes    
            
        }
        
        $arrOut = $this->arrFeedItems[ $strURLID ];
        if ( $numItems  ) {
            array_splice( $arrOut, $$numItems );
        }
            
        return $arrOut;
        
    }
    
    protected function getFeedObj( $arrUrls, $numItem=0, $numCacheDuration=3600 ) {    // 60 seconds * 60 = 1 hour, 1800 = 30 minutes
        
        // Reuse the object that already exists. This conserves the memory usage.
        $this->oFeed = isset( $this->oFeed ) ? $this->oFeed : new AdminPageFrameworkLoader_SimplePie();
        $oFeed = $this->oFeed; 
        
        // Set sort type.
        $oFeed->set_sortorder( 'date' );

        // Set urls
        $oFeed->set_feed_url( $arrUrls );    
        if ( $numItem ) {
            $oFeed->set_item_limit( $numItem );    
        }
        
        // This should be set after defining $urls
        $oFeed->set_cache_duration( $numCacheDuration );    
        
        $oFeed->set_stupidly_fast( true );
        
        // If the cache lifetime is explicitly set to 0, do not trigger the background renewal cache event
        if ( $numCacheDuration == 0 )
            $oFeed->setBackground( true );    // setting it true will be considered the background process; thus, it won't trigger the renewal event.
        
        // set_stupidly_fast() disables this internally so turn it on manually because it will trigger the custom sort method
        $oFeed->enable_order_by_date( true );    
        $oFeed->init();            
        return $oFeed;
        
    }    
}