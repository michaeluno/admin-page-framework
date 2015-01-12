<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods to return various system information.
 *
 * @since       3.4.6
 * @extends     AdminPageFramework_Utility_File
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal
 */
abstract class AdminPageFramework_Utility_SystemInformation extends AdminPageFramework_Utility_File {
    
    /**
     * Caches the result of PHP information array.
     * @since       3.4.6
     */
    static private $_aPHPInfo;
    
    /**
     * Returns the PHP information as an array.
     * 
     * @since       3.4.6
     */
    static public function getPHPInfo() {

        if ( isset( self::$_aPHPInfo ) ) {
            return self::$_aPHPInfo;
        }
    
        ob_start();
        phpinfo( -1 );

        $_sOutput = preg_replace(
            array(
                '#^.*<body>(.*)</body>.*$#ms', '#<h2>PHP License</h2>.*$#ms',
                '#<h1>Configuration</h1>#',  "#\r?\n#", "#</(h1|h2|h3|tr)>#", '# +<#',
                "#[ \t]+#", '#&nbsp;#', '#  +#', '# class=".*?"#', '%&#039;%',
                '#<tr>(?:.*?)" src="(?:.*?)=(.*?)" alt="PHP Logo" /></a>'
                    .'<h1>PHP Version (.*?)</h1>(?:\n+?)</td></tr>#',
                '#<h1><a href="(?:.*?)\?=(.*?)">PHP Credits</a></h1>#',
                '#<tr>(?:.*?)" src="(?:.*?)=(.*?)"(?:.*?)Zend Engine (.*?),(?:.*?)</tr>#',
                "# +#",
                '#<tr>#',
                '#</tr>#'
            ),
            array(
                '$1', '', '', '', '</$1>' . "\n", '<', ' ', ' ', ' ', '', ' ',
                '<h2>PHP Configuration</h2>'."\n".'<tr><td>PHP Version</td><td>$2</td></tr>'.
                "\n".'<tr><td>PHP Egg</td><td>$1</td></tr>',
                '<tr><td>PHP Credits Egg</td><td>$1</td></tr>',
                '<tr><td>Zend Engine</td><td>$2</td></tr>' . "\n" . '<tr><td>Zend Egg</td><td>$1</td></tr>',
                ' ',
                '%S%',
                '%E%'
            ),
            ob_get_clean()
        );

        $_aSections = explode( '<h2>', strip_tags( $_sOutput, '<h2><th><td>' ) );
        unset( $_aSections[ 0 ] );

        $_aOutput = array();
        foreach( $_aSections as $_sSection ) {
            $_iIndex = substr( $_sSection, 0, strpos( $_sSection, '</h2>' ) );
            preg_match_all(
                '#%S%(?:<td>(.*?)</td>)?(?:<td>(.*?)</td>)?(?:<td>(.*?)</td>)?%E%#',
                $_sSection, 
                $_aAskApache, 
                PREG_SET_ORDER
            );
            foreach( $_aAskApache as $_aMatches ) {
                if ( ! isset( $_aMatches[ 1 ], $_aMatches[ 2 ] ) ) {
                    array_slice( $_aMatches, 2 );
                    continue;
                }
                $_aOutput[ $_iIndex ][ $_aMatches[ 1 ] ] = ! isset( $_aMatches[ 3 ] ) || $_aMatches[ 2 ] == $_aMatches[ 3 ]
                    ? $_aMatches[ 2 ] 
                    : array_slice( $_aMatches, 2 );
            }
        }
        self::$_aPHPInfo = $_aOutput;
        return self::$_aPHPInfo;   
    
    }  
            
    /**
     * Returns an array of constants.
     * 
     * @since       3.4.6
     * @param       array|string      $asCategory      The category key names of the returning array.
     */
    static public function getDefinedConstants( $asCategories=null, $asRemovingCategories=null ) {
        
        $_aCategories           = is_array( $asCategories ) ? $asCategories : array( $asCategories );
        $_aCategories           = array_filter( $_aCategories );
        $_aRemovingCategories   = is_array( $asRemovingCategories ) ? $asRemovingCategories : array( $asRemovingCategories );
        $_aRemovingCategories   = array_filter( $_aRemovingCategories );
        $_aConstants            = get_defined_constants( true );
        
        if ( empty( $_aCategories ) ) {
            return self::dropElementsByKey( $_aConstants, $_aRemovingCategories );
        }
        return self::dropElementsByKey( 
            array_intersect_key( $_aConstants, array_flip( $_aCategories ) ),
            $_aRemovingCategories
        );
                
    }        
        
    /**
     * Returns PHP error log path.
     * 
     * @since       3.4.6
     * @return      array|string        The error log path. It can be multiple. If so an array holding them will be returned.
     */
    static public function getPHPErrorLogPath() {
                
        $_aPHPInfo = self::getPHPInfo();
        return isset( $_aPHPInfo['PHP Core']['error_log'] ) 
            ? $_aPHPInfo['PHP Core']['error_log']
            : '';
        
    }
    
    /**
     * Returns a PHP error log.
     * @since       3.4.6
     */
    static public function getPHPErrorLog( $iLines=1 ) {
        
        $_sLog = self::getFileTailContents( self::getPHPErrorLogPath(), $iLines );
        
        // If empty, it could be the log file could not be located. In that case, return the last error.
        return $_sLog
            ? $_sLog
            : print_r( @error_get_last(), true );   
        
    }        
        
}