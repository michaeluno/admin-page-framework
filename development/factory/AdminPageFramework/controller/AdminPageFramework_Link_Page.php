<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for HTML link elements for admin pages created by the framework, except the pages of custom post types.
 *
 * Embeds links in the footer and plugin's listing table etc.
 * 
 * @since           2.0.0
 * @since           3.0.0       Changed the name to `AdminPageFramework_Link_Page_Page` from `AdminPageFramework_Link_Page`.
 * @extends         AdminPageFramework_Link_Base
 * @package         AdminPageFramework
 * @subpackage      Link
 * @internal
 */
class AdminPageFramework_Link_Page extends AdminPageFramework_Link_Base {
    
    /**
     * The property object, commonly shared.
     * @since       2.0.0
     */ 
    private $oProp;
    
    public function __construct( &$oProp, $oMsg=null ) {
    
        if ( ! $oProp->bIsAdmin ) { return; }
        
        $this->oProp    = $oProp;
        $this->oMsg     = $oMsg;
        
        // The property object needs to be set as there are some public methods accesses the property object.
        if ( $oProp->bIsAdminAjax ) {
            return;
        }     
        
        $this->oProp->sLabelPluginSettingsLink = null === $this->oProp->sLabelPluginSettingsLink
            ? $this->oMsg->get( 'settings' )
            : $this->oProp->sLabelPluginSettingsLink;

        add_action( 'in_admin_footer', array( $this, '_replyToSetFooterInfo' ) );
    
        if ( in_array( $this->oProp->sPageNow, array( 'plugins.php' ) ) && 'plugin' == $this->oProp->aScriptInfo['sType'] ) {
            add_filter( 'plugin_action_links_' . plugin_basename( $this->oProp->aScriptInfo['sPath'] ) , array( $this, '_replyToAddSettingsLinkInPluginListingPage' ) );
        }

    }
        
    /**
     * Sets up footer information.
     * 
     * @since 3.1.3
     */
    public function _replyToSetFooterInfo() {

        if ( ! $this->oProp->isPageAdded() ) { return; }

        $this->_setFooterInfoLeft( $this->oProp->aScriptInfo, $this->oProp->aFooterInfo['sLeft'] );
        $this->_setFooterInfoRight( $this->oProp->_getLibraryData(), $this->oProp->aFooterInfo['sRight'] );    
    
        // Add script info into the footer 
        add_filter( 'admin_footer_text' , array( $this, '_replyToAddInfoInFooterLeft' ) );     
        add_filter( 'update_footer', array( $this, '_replyToAddInfoInFooterRight' ), 11 );     
        
    }
        
    /*
     * Methods for embedding links 
     * 
     */
    /**
     * @internal
     */
    public function _addLinkToPluginDescription( $asLinks ) {
        
        if ( ! is_array( $asLinks ) ) {
            $this->oProp->aPluginDescriptionLinks[] = $asLinks;
        } else {
            $this->oProp->aPluginDescriptionLinks = array_merge( $this->oProp->aPluginDescriptionLinks , $asLinks );
        }

        if ( 'plugins.php' !== $this->oProp->sPageNow ) {
            return;
        }
        
        add_filter( 'plugin_row_meta', array( $this, '_replyToAddLinkToPluginDescription' ), 10, 2 );

    }
    public function _addLinkToPluginTitle( $asLinks ) {
        
        static $_sPluginBaseName;        
        
        if ( ! is_array( $asLinks ) ) {
            $this->oProp->aPluginTitleLinks[] = $asLinks;
        } else {
            $this->oProp->aPluginTitleLinks = array_merge( $this->oProp->aPluginTitleLinks, $asLinks );
        }
        
        if ( 'plugins.php' !== $this->oProp->sPageNow ) {
            return;
        }
        
        if ( ! isset( $_sPluginBaseName ) ) {
            $_sPluginBaseName = plugin_basename( $this->oProp->aScriptInfo['sPath'] );
            add_filter( "plugin_action_links_{$_sPluginBaseName}", array( $this, '_replyToAddLinkToPluginTitle' ) );
        }

    }
        
    /**
     * 
     * @since 2.0.0
     * @remark A callback for the filter hook, `admin_footer_text`.
     */ 
    public function _replyToAddInfoInFooterLeft( $sLinkHTML='' ) {

        if ( ! isset( $_GET['page'] ) || ! $this->oProp->isPageAdded( $_GET['page'] )  ) {
            return $sLinkHTML; // $sLinkHTML is given by the hook.
        }
        
        if ( empty( $this->oProp->aScriptInfo['sName'] ) ) { return $sLinkHTML; }
        
        return $this->oProp->aFooterInfo['sLeft'];

    }
    public function _replyToAddInfoInFooterRight( $sLinkHTML='' ) {

        if ( ! isset( $_GET['page'] ) || ! $this->oProp->isPageAdded( $_GET['page'] )  ) {
            return $sLinkHTML; // $sLinkTHML is given by the hook.
        }
            
        return $this->oProp->aFooterInfo['sRight'];
            
    }
    
    public function _replyToAddSettingsLinkInPluginListingPage( $aLinks ) {
         
        // If the sub-pages are not added, do nothing.
        if ( count( $this->oProp->aPages ) < 1 ) { 
            return $aLinks; 
        }    

        // If the user disables the settings link, the label property is empty. If so, do not add it.
        if ( ! $this->oProp->sLabelPluginSettingsLink ) {
            return $aLinks;
        }
        
        // For a custom root slug,
        $_sLinkURL = preg_match( '/^.+\.php/', $this->oProp->aRootMenu['sPageSlug'] ) 
            ? add_query_arg( array( 'page' => $this->oProp->sDefaultPageSlug ), admin_url( $this->oProp->aRootMenu['sPageSlug'] ) )
            : "admin.php?page={$this->oProp->sDefaultPageSlug}";
        
        array_unshift(    
            $aLinks,
            '<a href="' . esc_url( $_sLinkURL ) . '">' . $this->oProp->sLabelPluginSettingsLink . '</a>'
        ); 
        return $aLinks;
        
    }    
    
    public function _replyToAddLinkToPluginDescription( $aLinks, $sFile ) {

        if ( $sFile != plugin_basename( $this->oProp->aScriptInfo['sPath'] ) ) { 
            return $aLinks; 
        }
        
        // Backward compatibility sanitisation.
        $_aAddingLinks = array();
        foreach( array_filter( $this->oProp->aPluginDescriptionLinks ) as $_sLLinkHTML ) {
            
            if ( ! $_sLLinkHTML ) {
                continue;
            }
            if ( is_array( $_sLLinkHTML ) ) {  // should not be an array
                $_aAddingLinks = array_merge( $_sLLinkHTML, $_aAddingLinks );
                continue;
            } 
            $_aAddingLinks[] = ( string ) $_sLLinkHTML;
            
        }
        return array_merge( $aLinks, $_aAddingLinks );
        
    }     
    public function _replyToAddLinkToPluginTitle( $aLinks ) {

        $_aAddingLinks = array();
        foreach( array_filter( $this->oProp->aPluginTitleLinks ) as $_sLinkHTML ) {
            
            if ( ! $_sLinkHTML ) {
                continue;
            }
            if ( is_array( $_sLinkHTML ) ) {
                $_aAddingLinks = array_merge( $_sLinkHTML, $aAddingLinks );
                continue;
            } 
            $_aAddingLinks[] = ( string ) $_sLinkHTML;
            
        }
        return array_merge( $aLinks, $_aAddingLinks );
        
    }     
}