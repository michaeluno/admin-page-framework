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
        
        $_sText = file_get_contents( $sFilePath );
        
        // Register the shortcode.
		add_shortcode( 'embed', array( $this, 'replyToProcessShortcode' ) );

		// Do the shortcode (only the [embed] one is registered)
		$_sText = do_shortcode( $_sText );        
        
        $_oWPReadmeParser = new AdminPageFramework_WPReadmeParser( 
            // $sFilePath,
            $_sText,
            array(
                '%PLUGIN_DIR_URL%'  => AdminPageFrameworkLoader_Registry::getPluginURL(),
                '%WP_ADMIN_URL%'    => admin_url(),
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
     * @since       3.6.0
     * @return      string
     */
    public function replyToProcessShortcode( $aAttributes, $sURL ) {
        
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
                            $aAttribute, 
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