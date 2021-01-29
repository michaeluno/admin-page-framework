<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods for HTML link elements to be embedded in network admin pages (multi-site) created by the framework.
 *
 * Embeds links in the footer and plugin's listing table etc.
 *
 * @since           3.5.0
 * @extends         AdminPageFramework_Link_admin_page
 * @package         AdminPageFramework/Factory/NetworkAdmin/Link
 * @internal
 */
class AdminPageFramework_Link_network_admin_page extends AdminPageFramework_Link_admin_page {

    /**
     * Sets up hooks and properties.
     */
    public function __construct( $oProp, $oMsg=null ) {

        parent::__construct( $oProp, $oMsg );

        if ( $this->_shouldSetPluginActionLinks() ) {

            // This filter for non-network-admin action links is added in the parent constructor.
            remove_filter(
                'plugin_action_links_' . plugin_basename( $this->oProp->aScriptInfo['sPath'] ),
                array( $this, '_replyToAddSettingsLinkInPluginListingPage' ),
                20
            );
            // Add the action link filter for the multi-site network admin.
            add_filter(
                'network_admin_plugin_action_links_' . plugin_basename( $this->oProp->aScriptInfo['sPath'] ),
                array( $this, '_replyToAddSettingsLinkInPluginListingPage' )
            );

        }

    }

    /**
     * Stores the plugin action links filter suffix.
     *
     * @since       3.5.5
     */
    protected $_sFilterSuffix_PluginActionLinks = 'network_admin_plugin_action_links_';


}
