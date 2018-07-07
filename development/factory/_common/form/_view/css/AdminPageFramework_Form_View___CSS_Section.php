<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2018, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to return CSS rules for form outputs.
 *
 * @since       3.7.0
 * @package     AdminPageFramework/Common/Form/View/CSS
 * @internal
 */
class AdminPageFramework_Form_View___CSS_Section extends AdminPageFramework_Form_View___CSS_Base {
    
    /**
     * @since       3.7.0
     * @return      string
     */
    protected function _get() {
        return $this->_getFormSectionRules();
    }
         /**
         * Returns the CSS rules for form fields.
         * 
         * @since       3.4.0
         * @since       3.7.0      Moved from `AdminPageFramework_CSS`.
         * @internal
         */    
        private function _getFormSectionRules() {
            $_sCSSRules = <<<CSSRULES
/* Section Table */
.admin-page-framework-section .form-table {
    margin-top: 0;
}
.admin-page-framework-section .form-table td label {
   display: inline;  /* adjusts the horizontal alignment with the th element */
}
/* Section Tabs */
.admin-page-framework-section-tabs-contents {
    margin-top: 1em;
}
.admin-page-framework-section-tabs { /* The section tabs' container */
    margin: 0;
}
.admin-page-framework-tab-content { /* each section including sub-sections of repeatable fields */
    padding: 0.5em 2em 1.5em 2em;
    margin: 0;
    border-style: solid;
    border-width: 1px;
    border-color: #dfdfdf;
    background-color: #fdfdfd;     
}
.admin-page-framework-section-tab {
    background-color: transparent;
    vertical-align: bottom; /* for Firefox */
    margin-bottom: -2px;    /* For WP 3.4.4+, 3.8.13 Changed it from -1px for WP 4.7. */
    margin-left: 0px;
    margin-right: 0.5em;
    background-color: #F1F1F1;
    font-weight: normal;
}
.admin-page-framework-section-tab:hover {
    background-color: #F8F8F8;
}
.admin-page-framework-section-tab.active {
    background-color: #fdfdfd;     
}
/* Tab title */
.admin-page-framework-section-tab h4 {
    margin: 0;
    /* padding: 8px 14px 10px; */
    padding: 0.4em 0.8em;
    font-size: 1.12em;
    vertical-align: middle;
    white-space: nowrap;
    display:inline-block;
    font-weight: normal;
}
.admin-page-framework-section-tab.nav-tab {
    /* padding: 0; */
    padding: 0.2em 0.4em;
}
.admin-page-framework-section-tab.nav-tab a {
    text-decoration: none;
    color: #464646;
    vertical-align: inherit; /* for Firefox - without this tiny dots appear */
    outline: 0; /* for FireFox - remove dotted outline */
}        
.admin-page-framework-section-tab.nav-tab a:focus { 
    /* For FireFox - remove dotted outline when a switchable tab is activated */
    box-shadow: none;
}
.admin-page-framework-section-tab.nav-tab.active a {
    color: #000;
}
.admin-page-framework-content ul.admin-page-framework-section-tabs > li.admin-page-framework-section-tab {    
    /* Do not show bullets in section tabs */
    list-style-type: none;
    /* For WordPress 4.4, make sure to attach the tab to the container */
    margin: -4px 4px -1px 0;
}
/* Repeatable Sections */
.admin-page-framework-repeatable-section-buttons {  
    float: right;
    clear: right;
    margin-top: 1em;  
}
.admin-page-framework-repeatable-section-buttons.disabled > .repeatable-section-button {
    color: #edd;
    border-color: #edd;
}

/* Section Caption */
.admin-page-framework-section-caption {
    text-align: left;
    margin: 0;
}
/* Section Title */
.admin-page-framework-section .admin-page-framework-section-title {
    /* background: none; */               /* @todo examine what this is for. @deprecated 3.4.0 for repeatable collapsible section titles */
    /* -webkit-box-shadow: none; */       /* @todo examine what this is for. @deprecated 3.4.0 for repeatable collapsible section titles */
    /* _box-shadow: none; */              /* @todo examine what this is for. @deprecated 3.4.0 for repeatable collapsible section titles */
}

/* Sortable Sections */
.admin-page-framework-sections.sortable-section > .admin-page-framework-section {
    /* padding: 1em 2.5em 1.5em 2.5em; */
    padding: 1em 1.8em 1em 2.6em;
    
}

/* Sortable Collapsible Sections */
.admin-page-framework-sections.sortable-section > .admin-page-framework-section.is_subsection_collapsible {
    display: block; 
    float: none;
    border: 0px;
    padding: 0;
    background: transparent;
}
/* Sortable Tabbed Sections */
.admin-page-framework-sections.sortable-section > .admin-page-framework-tab-content {
    display: block; 
    float: none;
    border: 0px;    

    padding: 0.5em 2em 1.5em 2em;
    margin: 0;
    border-style: solid;
    border-width: 1px;
    border-color: #dfdfdf;
    background-color: #fdfdfd;      
}

.admin-page-framework-sections.sortable-section > .admin-page-framework-section {
    margin-bottom: 1em;
}
.admin-page-framework-section {
    /* gives a bottom margin between sections. This helps for the debug info in each sectionset and collapsible sections. */
    margin-bottom: 1em; 
}            
.admin-page-framework-sectionset {
    margin-bottom: 1em; 
    display:inline-block;
    width:100%;
}            
/* Nested sections */
.admin-page-framework-section > .admin-page-framework-sectionset {
    margin-left: 2em;
}

CSSRULES;
            $_sCSSRules .= $this->___getForWP47();
            return $_sCSSRules;
        }
            /**
             * Returns CSS rules specific to WordPress 4.7 or above.
             * @since   3.8.13
             * @return  string
             */
            private function ___getForWP47() {
                // If the WordPress version is below 4.7,
                if ( version_compare( $GLOBALS[ 'wp_version' ], '4.7', '<' ) ) {
                    return '';
                }
                return <<<CSSRULES
.admin-page-framework-content ul.admin-page-framework-section-tabs > li.admin-page-framework-section-tab {
    margin-bottom: -2px;
}
CSSRULES;
            }

}
