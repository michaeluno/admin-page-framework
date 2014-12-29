<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Collects data of page loads in admin pages.
 *
 * @since 2.1.7
 * @package AdminPageFramework
 * @subpackage Debug
 * @internal
 */
abstract class AdminPageFramework_PageLoadInfo_Base {
    
    function __construct( $oProp, $oMsg ) {
        
        if ( $oProp->bIsAdminAjax || ! $oProp->bIsAdmin ) {
            return;
        }     
        
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            
            $this->oProp = $oProp;
            $this->oMsg = $oMsg;
            $this->_nInitialMemoryUsage = memory_get_usage();
            
            // must be loaded after the sub pages are registered
            add_action( 'in_admin_footer', array( $this, '_replyToSetPageLoadInfoInFooter' ), 999 );    
                        
        }

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
     * @access public
     * @internal
     */
    public function _replyToGetPageLoadInfo( $sFooterHTML ) {
        
        if ( self::$_bLoadedPageLoadInfo ) { return; }
        self::$_bLoadedPageLoadInfo = true;     
        
        $_nSeconds                 = timer_stop( 0 );
        $_nQueryCount             = get_num_queries();
        $_nMemoryUsage             = round( $this->_convertBytesToHR( memory_get_usage() ), 2 );
        $_nMemoryPeakUsage         = round( $this->_convertBytesToHR( memory_get_peak_usage() ), 2 );
        $_nMemoryLimit             = round( $this->_convertBytesToHR( $this->_convertToNumber( WP_MEMORY_LIMIT ) ), 2 );
        $_sInitialMemoryUsage = round( $this->_convertBytesToHR( $this->_nInitialMemoryUsage ), 2 );

        return $sFooterHTML
            . "<div id='admin-page-framework-page-load-stats'>"
                . "<ul>"
                    . "<li>" . sprintf( $this->oMsg->get( 'queries_in_seconds' ), $_nQueryCount, $_nSeconds ) . "</li>"
                    . "<li>" . sprintf( $this->oMsg->get( 'out_of_x_memory_used' ), $_nMemoryUsage, $_nMemoryLimit, round( ( $_nMemoryUsage / $_nMemoryLimit ), 2 ) * 100 . '%' ) . "</li>"
                    . "<li>" . sprintf( $this->oMsg->get( 'peak_memory_usage' ), $_nMemoryPeakUsage ) . "</li>"
                    . "<li>" . sprintf( $this->oMsg->get( 'initial_memory_usage' ), $_sInitialMemoryUsage ) . "</li>"
                . "</ul>"
            . "</div>";
        
    }

        /**
         * Transforms the php.ini notation for numbers (like '2M') to an integer
         *
         * @access private
         * @param $nSize
         * @return int
         * @remark This is influenced by the work of Mike Jolley.
         * @see http://mikejolley.com/projects/wp-page-load-stats/
         * @internal
         */
        private function _convertToNumber( $nSize ) {
            
            $_nReturn     = substr( $nSize, 0, -1 );
            switch( strtoupper( substr( $nSize, -1 ) ) ) {
                case 'P':
                    $_nReturn *= 1024;
                case 'T':
                    $_nReturn *= 1024;
                case 'G':
                    $_nReturn *= 1024;
                case 'M':
                    $_nReturn *= 1024;
                case 'K':
                    $_nReturn *= 1024;
            }
            return $_nReturn;
            
        }

        /**
         * Converts bytes to HR.
         *
         * @access private
         * @param mixed $bytes
         * @remark This is influenced by the work of Mike Jolley.
         * @see http://mikejolley.com/projects/wp-page-load-stats/
         */
        private function _convertBytesToHR( $nBytes ) {
            $_aUnits = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
            $_nLog = log( $nBytes, 1024 );
            $_iPower = ( int ) $_nLog;
            $_iSize = pow( 1024, $_nLog - $_iPower );
            return $_iSize . $_aUnits[ $_iPower ];
        }

}