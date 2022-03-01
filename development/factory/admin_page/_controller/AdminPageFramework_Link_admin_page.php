<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for HTML link elements to be embedded in admin pages created by the framework.
 *
 * Embeds links in the footer and plugin's listing table etc.
 * 
 * @since           2.0.0
 * @since           3.0.0       Renamed.
 * @extends         AdminPageFramework_Link_Base
 * @package         AdminPageFramework/Factory/AdminPage/Link
 * @internal
 */
class AdminPageFramework_Link_admin_page extends AdminPageFramework_Link_Base {
    
    /**
     * The property object, commonly shared.
     * @since       2.0.0
     * @since       3.5.0       Made it public as the network admin class extend this class.
     */ 
    public $oProp;
    
    /**
     * Sets up footer information.
     * 
     * @since           3.1.3
     * @callback        action      in_admin_footer
     * @return          void
     */
    public function _replyToSetFooterInfo() {

        if ( ! $this->oProp->isPageAdded() ) { 
            return; 
        }
        parent::_replyToSetFooterInfo();
           
    }
        
    /*
     * Methods for embedding links 
     * 
     */
    /**
     * Called from the factory `addLinkToPluginDescription()` method.
     * 
     * Used by the user to add action links to the plugin description column of the plugin listing table.
     * @internal
     * @return      void
     */
    public function _addLinkToPluginDescription( $asLinks ) {
        
        if ( ! is_array( $asLinks ) ) {
            $this->oProp->aPluginDescriptionLinks[] = $asLinks;
        } else {
            $this->oProp->aPluginDescriptionLinks = array_merge( 
                $this->oProp->aPluginDescriptionLinks, 
                $asLinks 
            );
        }
        
        add_filter( 
            'plugin_row_meta', 
            array( $this, '_replyToAddLinkToPluginDescription' ), 
            10, 
            2 
        );

    }
    
    /**
     * Called from the factory `addLinkToPluginTitle()` method.
     * 
     * Used by the user to add custom action links to the title column of plugin listing table.
     * 
     * @internal
     * @return      void
     */    
    public function _addLinkToPluginTitle( $asLinks ) {
        
        if ( ! is_array( $asLinks ) ) {
            $this->oProp->aPluginTitleLinks[] = $asLinks;
        } else {
            $this->oProp->aPluginTitleLinks = array_merge( 
                $this->oProp->aPluginTitleLinks, 
                $asLinks 
           );
        }
                
        $this->_addFilterHook_PluginTitleActionLink();

    }
        /**
         * Stores the plugin action links filter suffix.
         * 
         * @remark      The network admin uses a different one and the network admin class will override this value.
         * @since       3.5.5
         * @internal
         */
        protected $_sFilterSuffix_PluginActionLinks = 'plugin_action_links_';
    
        /**
         * Adds a filter hook of a plugin title action link.
         * @remark      The network admin link class extends this method.
         * @uses        add_filter()
         * @uses        plugin_basename()
         * @since       3.5.5
         * @return      void
         */
        private function _addFilterHook_PluginTitleActionLink() {
            static $_sPluginBaseName;
            if ( isset( $_sPluginBaseName ) ) {
                return;
            }
            $_sPluginBaseName = plugin_basename( $this->oProp->aScriptInfo[ 'sPath' ] );
            add_filter( 
                $this->_sFilterSuffix_PluginActionLinks . $_sPluginBaseName, 
                array( $this, '_replyToAddPluginActionLinks' ) 
            );            
        }
        
    /**
     * Modifies the admin page footer text on the left hand side.
     * 
     * @since       2.0.0
     * @callback    filter      admin_footer_text
     * @return      string
     * @internal
     */ 
    public function _replyToAddInfoInFooterLeft( $sLinkHTML='' ) {

        if ( ! $this->_isPageAdded() ) {
            return $sLinkHTML; // $sLinkHTML is given by the hook.
        }
        $sLinkHTML  = empty( $this->oProp->aScriptInfo['sName'] )
            ? $sLinkHTML
            : $this->oProp->aFooterInfo['sLeft'];     
            
        $_sPageSlug = $this->oProp->getCurrentPageSlug();
        $_sTabSlug  = $this->oProp->getCurrentTabSlug();        

        // filtering order: tab -> page -> class
        return $this->addAndApplyFilters( 
            $this->oProp->oCaller, 
            array(                 
                $this->getAOrB( 
                    $_sTabSlug,
                    'footer_left_' . $_sPageSlug . '_' . $_sTabSlug,
                    null    // will be ignored
                ),
                'footer_left_' . $_sPageSlug,
                'footer_left_' . $this->oProp->sClassName
            ),
            $sLinkHTML
        );        

    }
    /**
     * Modifies the admin page footer text on the right hand side.
     * 
     * @since       2.0.0
     * @callback    filter      update_footer
     * @return      string
     * @internal
     */
    public function _replyToAddInfoInFooterRight( $sLinkHTML='' ) {

        if ( ! $this->_isPageAdded() ) {
            return $sLinkHTML; // $sLinkTHML is given by the hook.
        }
            
        $_sPageSlug = $this->oProp->getCurrentPageSlug();
        $_sTabSlug  = $this->oProp->getCurrentTabSlug();        

        // filtering order: tab -> page -> class
        return $this->addAndApplyFilters( 
            $this->oProp->oCaller, 
            array(                 
                $this->getAOrB( 
                    $_sTabSlug,
                    'footer_right_' . $_sPageSlug . '_' . $_sTabSlug,
                    null    // will be ignored
                ),
                'footer_right_' . $_sPageSlug,
                'footer_right_' . $this->oProp->sClassName
            ),
            $this->oProp->aFooterInfo['sRight']
        );             
            
    }
        /**
         * Checks if the current page has been added by the framework.
         * 
         * @since       3.5.5
         * @return      boolean
         * @internal
         */
        private function _isPageAdded() {
            if ( ! isset( $_GET[ 'page' ] ) ) { // sanitization unnecessary
                return false;
            }            
            return ( boolean ) $this->oProp->isPageAdded( $_GET[ 'page' ] ); // sanitization unnecessary
        }
    /**
     * Modifies the action link of the plugin title column in the plugin listing page (plugins.php).
     * 
     * @callback    filter      plugin_action_links_{plugin base name}
     * @return      array
     * @internal
     */
    public function _replyToAddSettingsLinkInPluginListingPage( $aLinks ) {
         
        // If the sub-pages are not added, do nothing.
        if ( count( $this->oProp->aPages ) < 1 ) { 
            return $aLinks; 
        }    
        
        $this->oProp->sLabelPluginSettingsLink = null === $this->oProp->sLabelPluginSettingsLink
            ? $this->oMsg->get( 'settings' )
            : $this->oProp->sLabelPluginSettingsLink;        

        // If the user disables the settings link, the label property is empty. If so, do not add it.
        if ( ! $this->oProp->sLabelPluginSettingsLink ) {
            return $aLinks;
        }

        // For a custom root slug,
        $_sLinkURL = preg_match( '/^.+\.php/', $this->oProp->aRootMenu[ 'sPageSlug' ] ) 
            ? add_query_arg( 
                array( 
                    'page' => $this->oProp->sDefaultPageSlug 
                ), 
                admin_url( $this->oProp->aRootMenu[ 'sPageSlug' ] )
            )
            : "admin.php?page={$this->oProp->sDefaultPageSlug}";
        
        // Insert the link
        array_unshift(    
            $aLinks,
            // '<a href="' . esc_url( $_sLinkURL ) . '">' 
            '<a ' . $this->getAttributes(
                array(
                    'href'      => esc_url( $_sLinkURL ),
                    // 3.5.7+ Added for acceptance testing
                    'class'     => 'apf-plugin-title-action-link apf-post-type',
                )
            ) . '>' 
                . $this->oProp->sLabelPluginSettingsLink 
            . '</a>'
        ); 
        return $aLinks;
        
    }    
    
    /**
     * Modifies the action links of the plugin description column in the plugin listing page (plugins.php).
     * 
     * @callback    filter      plugin_row_meta
     * @return      array
     * @internal
     */
    public function _replyToAddLinkToPluginDescription( $aLinks, $sFile ) {

        if ( $sFile !== plugin_basename( $this->oProp->aScriptInfo[ 'sPath' ] ) ) { 
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

    /**
     * @return      array
     * @callback    filter      plugin_action_links_{plugin base name}
     * @since       unknown
     * @since       3.7.11      Renamed from `_replyToAddLinkToPluginTitle`.
     * @internal
     */
    public function _replyToAddPluginActionLinks( $aLinks ) {

        $_aAddingLinks = array();
        foreach( array_filter( $this->oProp->aPluginTitleLinks ) as $_sLinkHTML ) {

            if ( is_array( $_sLinkHTML ) ) {
                $_aAddingLinks = array_merge( $_sLinkHTML, $_aAddingLinks );
                continue;
            } 
            $_aAddingLinks[] = ( string ) $_sLinkHTML;
            
        }
        return array_merge( $aLinks, $_aAddingLinks );
        
    }     
}
