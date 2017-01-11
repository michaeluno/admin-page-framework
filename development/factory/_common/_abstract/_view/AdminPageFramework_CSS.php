<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to return CSS rules.
 *
 * @since       3.2.0
 * @package     AdminPageFramework/Common/Factory/CSS
 * @internal
 */
class AdminPageFramework_CSS {
    
    /**
     * Returns the framework default CSS.
     * 
     * @since   3.2.0
     * @internal
     */
    static public function getDefaultCSS() {

        $_sCSS = <<<CSSRULES
/* Settings Notice */
.wrap div.updated.admin-page-framework-settings-notice-container, 
.wrap div.error.admin-page-framework-settings-notice-container, 
.media-upload-form div.error.admin-page-framework-settings-notice-container
{
    clear: both;
    margin-top: 16px;
}
.wrap div.error.confirmation.admin-page-framework-settings-notice-container {
    border-color: #368ADD;
}  
      
/* Contextual Help Page */
.contextual-help-description {
    clear: left;    
    display: block;
    margin: 1em 0;
}
.contextual-help-tab-title {
    font-weight: bold;
}

/* Page Meta Boxes */
.admin-page-framework-content {

    margin-bottom: 1.48em;    
    width: 100%; /* This allows float:right elements to go to the very right end of the page. */
    
    /* display: inline-table; */ /* @deprecated 3.5.0. Fixes the bottom margin getting placed at the top. */
    /* [3.5.0+] The above display: inline-table makes it hard to display code blocks with overflow as container cannot have solid width. */
    display: block; 

}
.admin-page-framework-content > #post-body-content{
    margin-bottom: 0;
}

/* Regular Heading Titles - the meta box container element affects the styles of regular main content output. So it needs to be fixed. */
.admin-page-framework-container #poststuff .admin-page-framework-content h3 {
    font-weight: bold;
    font-size: 1.3em;
    margin: 1em 0;
    padding: 0;
    font-family: 'Open Sans', sans-serif;
} 

/* Tab Navigation Bar */
.nav-tab.tab-disabled,
.nav-tab.tab-disabled:hover {
    font-weight: normal;
    color: #AAAAAA;
}

/* In-page tabs */ 
.admin-page-framework-in-page-tab .nav-tab.nav-tab-active {
    border-bottom-width: 2px;
}
/* Give a space between generic admin notice containers and the in-page navigation tabs */
.wrap .admin-page-framework-in-page-tab div.error, 
.wrap .admin-page-framework-in-page-tab div.updated {
    margin-top: 15px;
}

/* Framework System Information */
.admin-page-framework-info,
.admin-page-framework-info code
{
    font-size: 0.8em;
    font-weight: lighter;
    text-align: right;
}

/* Debug containers */
pre.dump-array {
    border: 1px solid #ededed;
    margin: 24px 2em;
    margin: 1.714285714rem 2em;
    padding: 24px;
    padding: 1.714285714rem;				
    overflow-x: auto; 
    white-space: pre-wrap;
    background-color: #FFF;
    margin-bottom: 2em;
    width: auto;
}
CSSRULES;

        return $_sCSS . PHP_EOL
            . self::_getPageLoadStatsRules() . PHP_EOL
            . self::_getVersionSpecificRules();
            
    }

        /**
         * Returns the CSS rules for page load stats.
         * 
         * @since       3.2.0
         * @internal
         */    
        static private function _getPageLoadStatsRules() {
            return <<<CSSRULES
/* Page Load Stats */
#admin-page-framework-page-load-stats {
    clear: both;
    display: inline-block;
    width: 100%
}
#admin-page-framework-page-load-stats li{
    display: inline;
    margin-right: 1em;
}     

/* To give the footer area more space */
#wpbody-content {
    padding-bottom: 140px;
}            
CSSRULES;
        }
        
        /**
         * Returns the framework default CSS rules.
         * 
         * @since       3.2.0
         * @internal
         */    
        static private function _getVersionSpecificRules() {
            // $GLOBALS['wp_version']
            return '';
        }
    
    /**
     * Returns the framework default CSS.
     * 
     * @since       3.2.0
     * @internal
     */
    static public function getDefaultCSSIE() {
        return '';
    }
    
}
