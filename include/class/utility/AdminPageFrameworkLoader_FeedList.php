<?php
/**
 * @package         Admin Page Framework Loader
 * @copyright       Copyright (c) 2013-2019, Michael Uno
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
     *
     * @param       array|string        $asURLs     A url of string or urls of array.
     */
    public function __construct( $asURLs ) {

        $this->_aURLs = is_array( $asURLs )
            ? $asURLs
            : ( empty( $asURLs )
                ? array()
                : ( array ) $asURLs
            );

    }

    /**
     *
     * @return      array
     */
    public function get( $iItems=0 ) {

        $_aOutput   = array();
        $_aURLs     = $this->_aURLs;

        if ( empty( $_aURLs ) ) {
            return $_aOutput;
        }

        $_oFeed     = fetch_feed( $_aURLs );
        foreach ( $_oFeed->get_items() as $_oItem ) {
            $_aOutput[ $_oItem->get_title() ] = array(
                'content'        => $_oItem->get_content(),
                'description'    => $_oItem->get_description(),
                'title'          => $_oItem->get_title(),
                'date'           => $_oItem->get_date( 'j F Y, g:i a' ),
                'author'         => $_oItem->get_author(),
                'link'           => $_oItem->get_permalink(),    // get_link() may be used as well
            );
        }

        if ( $iItems ) {
            array_splice( $_aOutput, $iItems );
        }

        return $_aOutput;

    }

}
