<?php
/**
 * Admin Page Framework Loader
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Provides utility methods.
 * 
 * @since       3.5.0    
 */
class AdminPageFrameworkLoader_Utility {

    /**
     * Retrieves a section contents of a readme file.
     */
    static public function getWPReadMeSection( $sSectionName, $sReadMePath ) {            
        
        if ( ! file_exists( $sReadMePath ) ) {
            return '';
        }

        $_sContent = file_get_contents( $sReadMePath );
        // $_sContent = esc_html( $_sContent );

        $_aContent = preg_split( '/^[\s]*==[\s]*(.+?)[\s]*==/m', $_sContent, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY );
        
        $_iIndex   = array_search( $sSectionName, $_aContent );
        if ( false === $_iIndex ) {
            return '';
        }
        
        $_sContent = $_aContent[ $_iIndex + 1 ];
        // return $_sContent;
        
        $_sContent = preg_replace( '/^-\s(.+?)$/m', '<li>\\1</li>', $_sContent );
        
        $_sContent = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $_sContent );   // for inline backtics.
        $_sContent = preg_replace_callback( '/`(.*?)`/ms', array( __CLASS__, '_replyToReplaceCodeBlock' ), $_sContent ); // for multi-line backtics.
        
        $_sContent = preg_replace( '/[\040]\*\*(.*?)\*\*/', ' <strong>\\1</strong>', $_sContent );
        $_sContent = preg_replace( '/[\040]\*(.*?)\*/', ' <em>\\1</em>', $_sContent );
        $_sContent = preg_replace( '/= (.*?) =/', '<h4>\\1</h4>', $_sContent );
        $_sContent = preg_replace( '/\[(.*?)\]\((.*?)\)/', '<a href="\\2">\\1</a>', $_sContent );
        
        // $_sContent = preg_replace( '/(\s+\r\n)+/s', '<br />', trim( $_sContent ) );
        $_sContent = preg_replace_callback( '/_CODEBLOCK_\d+/', array( __CLASS__, '_replyToReplaceBackCode' ), $_sContent ); // for multi-line backtics.
        return $_sContent;            
        
    }
        static public $aCodeBlocks = array();
        static public function _replyToReplaceBackCode( $aMatches ) {
            return array_shift( self::$aCodeBlocks );
        }
        static public function _replyToReplaceCodeBlock( $aMatches ) {            
            static $_iCount = 0;
            if ( ! isset( $aMatches[ 1 ] ) ) {
                return $aMatches[ 0 ];
            }
            $_iCount++;
            self::$aCodeBlocks[] = "<pre><code>"
                    . esc_html( trim( $aMatches[ 1 ], " \t\n\r" ) )
                . "</code></pre>";
            return '_CODEBLOCK_' . $_iCount;
                     
        }
        
        
}