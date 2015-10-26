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
 * A base class that provides methods to display readme file contents.
 * 
 * @sicne       3.5.3       Extends `AdminPageFrameworkLoader_AdminPage_Tab_Base`.
 * @extends     AdminPageFrameworkLoader_AdminPage_Tab_Base
 */
abstract class AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase extends AdminPageFrameworkLoader_AdminPage_Tab_Base {
        
    /**
     * 
     * @since       3.5.3
     */
    protected function _getReadmeContents( $sFilePath, $sTOCTitle, $asSections=array() ) {
        
        $_oWPReadmeParser = new AdminPageFramework_WPReadmeParser( 
            $sFilePath, 
            array( // replacements
                '%PLUGIN_DIR_URL%'  => AdminPageFrameworkLoader_Registry::getPluginURL(),
                '%WP_ADMIN_URL%'    => admin_url(),
            ),
            array( // callbacks
                'content_before_parsing' => array( $this, '_replyToProcessShortcodes' ),
            )
        );    
        $_sContent = '';
        foreach( ( array ) $asSections as $_sSection  ) {
            $_sContent .= $_oWPReadmeParser->getSection( $_sSection );  
        }        
        if ( $sTOCTitle ) {            
            $_oTOC = new AdminPageFramework_TableOfContents(
                $_sContent,
                4,
                $sTOCTitle
            );
            return $_oTOC->get();        
        }
        return ''
         . $_sContent;
        
    }
    
        /**
         * @return      string 
         * @return      3.6.1
         */
        public function _replyToProcessShortcodes( $sContent ) {

            // Register the 'embed' shortcode.
            add_shortcode( 'embed', array( $this, '_replyToProcessShortcode_embed' ) );
            return do_shortcode( $sContent );
            
        }
               
            /**
             * @since       3.6.1
             * @return      string      The generate HTML output.
             */
            public function _replyToProcessShortcode_embed( $aAttributes, $sURL, $sShortcode='' ) {

                $sURL   = isset( $aAttributes[ 'src' ] ) ? $aAttributes[ 'src' ] : $sURL;      
                $_sHTML = wp_oembed_get( $sURL );
                
                // If there was a result, return it
                if ( $_sHTML ) {
                    // This filter is documented in wp-includes/class-wp-embed.php
                    return "<div class='video oembed'>" 
                                . apply_filters(
                                    'embed_oembed_html', 
                                    $_sHTML, 
                                    $sURL, 
                                    $aAttributes, 
                                    0
                                )
                        . "</div>";
                }        
                
                // If not found, return the link.
                $_oWPEmbed = new WP_Embed;        
                return "<div class='video oembed'>" 
                        . $_oWPEmbed->maybe_make_link( $sURL )
                    . "</div>";
                
            }         
    
}