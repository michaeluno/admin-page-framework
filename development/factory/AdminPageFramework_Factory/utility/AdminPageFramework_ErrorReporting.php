<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Retrieves the server set error reporting level.
 * 
 * @since       3.3.0
 * @see         http://www.efrag.gr/2013/01/how-to-get-your-error-reporting-constant-values/
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal    
 */
class AdminPageFramework_ErrorReporting {
    
    private $_aLevels = array(
        1       => 'E_ERROR',
        2       => 'E_WARNING',
        4       => 'E_PARSE',
        8       => 'E_NOTICE',
        16      => 'E_CORE_ERROR',
        32      => 'E_CORE_WARNING',
        64      => 'E_COMPILE_ERROR',
        128     => 'E_COMPILE_WARNING',
        256     => 'E_USER_ERROR',
        512     => 'E_USER_WARNING',
        1024    => 'E_USER_NOTICE',
        2048    => 'E_STRICT',
        4096    => 'E_RECOVERABLE_ERROR',
        8192    => 'E_DEPRECATED',
        16384   => 'E_USER_DEPRECATED'
    );

    private $_iLevel;

    public function __construct( $iLevel=null ) {
        $this->_iLevel = null !== $iLevel 
            ? $iLeevl
            : error_reporting();
    }
    
    /**
     * Returns the readable error level description.
     */
    public function getErrorLevel() {
        return $this->_getErrorDescription( $this->_getIncluded() );
    }    
        /**
         * 
         * @return  array
         */
        private function _getIncluded() {
            
            $_aIncluded = array();
            foreach( $this->_aLevels as $_iLevel => $iLevelText ) {
                
                // This is where we check if a level was used or not
                if ( $this->_iLevel & $_iLevel ) {
                    $_aIncluded[] = $_iLevel;
                }
                
            }
            return $_aIncluded;
            
        }

        private function _getErrorDescription( $aIncluded ) {
            
            $_iAll          = count( $this->_aLevels );
            $_aValues       = array();
            
            if ( count( $aIncluded ) > $_iAll / 2 ) {
                $_aValues[] = 'E_ALL';
                foreach( $this->_aLevels as $_iLevel => $iLevelText ) {
                    if ( ! in_array( $_iLevel, $aIncluded ) ) {
                        $_aValues[] = $iLevelText;
                    }
                }
                return implode( ' & ~', $_aValues );
            } 
            foreach( $aIncluded as $_iLevel ) {
                $_aValues[] = $this->_aLevels[ $_iLevel ];
            }
            return implode( ' | ', $_aValues );

        }
}