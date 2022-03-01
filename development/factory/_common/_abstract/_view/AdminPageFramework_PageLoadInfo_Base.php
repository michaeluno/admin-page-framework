<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Collects data of page loads in admin pages.
 *
 * @since       2.1.7
 * @package     AdminPageFramework/Common/Factory/Debug
 * @internal
 */
abstract class AdminPageFramework_PageLoadInfo_Base extends AdminPageFramework_FrameworkUtility {

    public $oProp;

    public $oMsg;

    protected $_nInitialMemoryUsage;

    /**
     * Sets up hooks and properties.
     */
    public function __construct( $oProp, $oMsg ) {

        if ( ! $this->_shouldProceed( $oProp ) ) {
            return;
        }

        $this->oProp                = $oProp;
        $this->oMsg                 = $oMsg;
        $this->_nInitialMemoryUsage = memory_get_usage();

        add_action( 'in_admin_footer', array( $this, '_replyToSetPageLoadInfoInFooter' ), 999 );

    }
        /**
         * @since       3.8.5
         * @return      boolean
         */
        private function _shouldProceed( $oProp ) {

            if ( $oProp->bIsAdminAjax || ! $oProp->bIsAdmin ) {
                return false;
            }
            return ( boolean ) $oProp->bShowDebugInfo;

        }

    /**
     * @remark Should be overridden in an extended class.
     */
    public function _replyToSetPageLoadInfoInFooter() {}

    /**
     * Indicates whether the page load info is inserted or not.
     */
    static private $_bLoadedPageLoadInfo = false;

    /**
     * Display gathered information.
     *
     * @param    string $sFooterHTML
     * @return   string
     * @internal
     */
    public function _replyToGetPageLoadInfo( $sFooterHTML ) {

        // 3.8.8+ The `bShowDebugInfo` property may be updated by the user during the page load.
        if ( ! $this->oProp->bShowDebugInfo ) {
            return $sFooterHTML;
        }

        if ( self::$_bLoadedPageLoadInfo ) {
            return $sFooterHTML;
        }
        self::$_bLoadedPageLoadInfo = true;

        return $sFooterHTML
            . $this->___getPageLoadStats();

    }
        /**
         * Returns the output of page load stats.
         * @since  3.8.8
         * @return string
         */
        private function ___getPageLoadStats() {

            $_nSeconds            = timer_stop( 0 );
            $_nQueryCount         = get_num_queries();
            $_iMemoryUsage        = memory_get_usage();
            $_nMemoryUsage        = round( $_iMemoryUsage, 2 );
            $_sMemoryUsage        = $this->getReadableBytes( $_iMemoryUsage );
            $_nMemoryPeakUsage    = round( memory_get_peak_usage(), 2 );
            $_sMemoryPeakUsage    = $this->getReadableBytes( $_nMemoryPeakUsage );
            $_iMemoryLimit        = $this->getNumberOfReadableSize( WP_MEMORY_LIMIT );
            $_sMemoryLimit        = $this->getReadableBytes( $_iMemoryLimit );
            $_nMemoryLimit        = round( $_iMemoryLimit, 2 );
            $_nInitialMemoryUsage = round( $this->_nInitialMemoryUsage, 2 );
            $_sInitialMemoryUsage = $this->getReadableBytes( $_nInitialMemoryUsage );
            return "<div id='admin-page-framework-page-load-stats'>"
                    . "<ul>"
                        . "<li>" . sprintf( $this->oMsg->get( 'queries_in_seconds' ), $_nQueryCount, $_nSeconds ) . "</li>"
                        . "<li>" . sprintf( $this->oMsg->get( 'out_of_x_memory_used' ), $_sMemoryUsage, $_sMemoryLimit, round( ( $_nMemoryUsage / $_nMemoryLimit ), 2 ) * 100 . '%' ) . "</li>"
                        . "<li>" . sprintf( $this->oMsg->get( 'peak_memory_usage' ), $_sMemoryPeakUsage ) . "</li>"
                        . "<li>" . sprintf( $this->oMsg->get( 'initial_memory_usage' ), $_sInitialMemoryUsage ) . "</li>"
                    . "</ul>"
                . "</div>";

        }

}