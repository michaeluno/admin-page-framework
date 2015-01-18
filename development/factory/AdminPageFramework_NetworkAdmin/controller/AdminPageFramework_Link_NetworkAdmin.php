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
 * @since           3.5.0
 * @extends         AdminPageFramework_Link_Page
 * @package         AdminPageFramework
 * @subpackage      Link
 * @internal
 */
class AdminPageFramework_Link_NetworkAdmin extends AdminPageFramework_Link_Page {
    
    /**
     * The property object, commonly shared.
     * @since       3.5.0
     */ 
    public $oProp;
    
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
            add_filter( 'network_admin_plugin_action_links_' . plugin_basename( $this->oProp->aScriptInfo['sPath'] ) , array( $this, '_replyToAddSettingsLinkInPluginListingPage' ) );
        }
   
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
            add_filter( "network_admin_plugin_action_links_{$_sPluginBaseName}", array( $this, '_replyToAddLinkToPluginTitle' ) );
        }

    }
   
}