<?php
class AdminPageFramework_Link_NetworkAdmin extends AdminPageFramework_Link_Page {
    public function __construct($oProp, $oMsg = null) {
        parent::__construct($oProp, $oMsg);
        if (in_array($this->oProp->sPageNow, array('plugins.php')) && 'plugin' === $this->oProp->aScriptInfo['sType']) {
            remove_filter('plugin_action_links_' . plugin_basename($this->oProp->aScriptInfo['sPath']), array($this, '_replyToAddSettingsLinkInPluginListingPage'), 20);
            add_filter('network_admin_plugin_action_links_' . plugin_basename($this->oProp->aScriptInfo['sPath']), array($this, '_replyToAddSettingsLinkInPluginListingPage'));
        }
    }
    protected $_sFilterSuffix_PluginActionLinks = 'network_admin_plugin_action_links_';
}