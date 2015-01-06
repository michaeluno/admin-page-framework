<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to return CSS rules.
 *
 * @since       3.2.0
 * @package     AdminPageFramework
 * @subpackage  CSS
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
    display: inline-table; /* Fixes the bottom margin getting placed at the top. */
    width: 100%; /* This allows float:right elements to go to the very right end of the page. */
}

/* Regular Heading Titles - the meta box container element affects the styles of regular main content output. So it needs to be fixed. */
.admin-page-framework-container #poststuff .admin-page-framework-content h3 {
    font-weight: bold;
    font-size: 1.3em;
    margin: 1em 0;
    padding: 0;
    font-family: 'Open Sans', sans-serif;
} 

/* In-page tabs */ 
.admin-page-framework-in-page-tab .nav-tab.nav-tab-active {
    border-bottom-width: 2px;
}

/* Framework System Information */
.admin-page-framework-info {
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
            . self::_getFormSectionRules() . PHP_EOL
            . self::_getFormFieldRules() . PHP_EOL
            . self::_getCollapsibleSectionsRules() . PHP_EOL
            . self::_getFieldErrorRules() . PHP_EOL
            . self::_getMetaBoxFormRules() . PHP_EOL
            . self::_getWidgetFormRules() . PHP_EOL
            . self::_getPageLoadStatsRules() . PHP_EOL
            . self::_getVersionSpecificRules( $GLOBALS['wp_version'] );
            
    }

         /**
         * Returns the CSS rules for form fields.
         * 
         * @since       3.4.0
         * @internal
         */    
        static private function _getFormSectionRules() {
            return <<<CSSRULES
.admin-page-framework-section {
    margin-bottom: 1em; /* gives a margin between sections. This helps for the debug info in each sectionset and collapsible sections. */
}            
.admin-page-framework-sectionset {
    margin-bottom: 1em; 
}            
CSSRULES;
            
        }
    
        /**
         * Returns the CSS rules for form fields.
         * 
         * @since       3.2.0
         * @internal
         */    
        static private function _getFormFieldRules() {
            return <<<CSSRULES
/* Form Elements */
/* TD paddings when the field title is disabled */
td.admin-page-framework-field-td-no-title {
    padding-left: 0;
    padding-right: 0;
}
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
}
.admin-page-framework-section-tab.active {
    background-color: #fdfdfd;     
}
.admin-page-framework-section-tab h4 {
    margin: 0;
    padding: 8px 14px 10px;
    font-size: 1.2em;
}
.admin-page-framework-section-tab.nav-tab {
    padding: 0;
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
/* Repeatable Sections */
.admin-page-framework-repeatable-section-buttons {
    float: right;
    clear: right;
    margin-top: 1em;
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

/* Fields Container */
.admin-page-framework-fields {
    display: table; /* the block property does not give the element the solid height */
    width: 100%;
    table-layout: fixed;    /* in Firefox fixes the issue that preview images causes the container element to expand */
}

/* Number Input */
.admin-page-framework-field input[type='number'] {
    text-align: right;
}     

/* Disabled */
.admin-page-framework-fields .disabled,
.admin-page-framework-fields .disabled input,
.admin-page-framework-fields .disabled textarea,
.admin-page-framework-fields .disabled select,
.admin-page-framework-fields .disabled option {
    color: #BBB;
}

/* HR */
.admin-page-framework-fields hr {
    border: 0; 
    height: 0;
    border-top: 1px solid #dfdfdf; 
}

/* Delimiter */
.admin-page-framework-fields .delimiter {
    display: inline;
}

/* Description */
.admin-page-framework-fields-description {
    margin-bottom: 0;
}
/* Field Container */
.admin-page-framework-field {
    float: left;
    clear: both;
    display: inline-block;
    margin: 1px 0;
}
.admin-page-framework-field label{
    display: inline-block; /* for WordPress v3.7.x or below */
    width: 100%;
}
.admin-page-framework-field .admin-page-framework-input-label-container {
    margin-bottom: 0.25em;
}
@media only screen and ( max-width: 780px ) { /* For WordPress v3.8 or greater */
    .admin-page-framework-field .admin-page-framework-input-label-container {
        margin-bottom: 0.5em;
    }
}     

.admin-page-framework-field .admin-page-framework-input-label-string {
    padding-right: 1em; /* for checkbox label strings, a right padding is needed */
    vertical-align: middle; 
    display: inline-block; /* each (sub)field label can have a fix min-width */
}
.admin-page-framework-field .admin-page-framework-input-button-container {
    padding-right: 1em; 
}
.admin-page-framework-field .admin-page-framework-input-container {
    display: inline-block;
    vertical-align: middle;
}
.admin-page-framework-field-image .admin-page-framework-input-label-container {     
    vertical-align: middle;
}

.admin-page-framework-field .admin-page-framework-input-label-container {
    display: inline-block;     
    vertical-align: middle; 
}

/* Repeatable Fields */     
.repeatable .admin-page-framework-field {
    clear: both;
    display: block;
}
.admin-page-framework-repeatable-field-buttons {
    float: right;     
    margin: 0.1em 0 0.5em 0.3em;
    vertical-align: middle;
}
.admin-page-framework-repeatable-field-buttons .repeatable-field-button {
    margin: 0 0.1em;
    font-weight: normal;
    vertical-align: middle;
    text-align: center;
}
@media only screen and (max-width: 960px) {
    .admin-page-framework-repeatable-field-buttons {
        margin-top: 0;
    }
}

/* Sortable Fields */
.sortable .admin-page-framework-field {
    clear: both;
    float: left;
    display: inline-block;
    padding: 1em 1.2em 0.78em;
    margin: 1px 0 0 0;
    border-top-width: 1px;
    border-bottom-width: 1px;
    border-bottom-style: solid;
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;     
    text-shadow: #fff 0 1px 0;
    -webkit-box-shadow: 0 1px 0 #fff;
    box-shadow: 0 1px 0 #fff;
    -webkit-box-shadow: inset 0 1px 0 #fff;
    box-shadow: inset 0 1px 0 #fff;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    background: #f1f1f1;
    background-image: -webkit-gradient(linear, left bottom, left top, from(#ececec), to(#f9f9f9));
    background-image: -webkit-linear-gradient(bottom, #ececec, #f9f9f9);
    background-image: -moz-linear-gradient(bottom, #ececec, #f9f9f9);
    background-image: -o-linear-gradient(bottom, #ececec, #f9f9f9);
    background-image: linear-gradient(to top, #ececec, #f9f9f9);
    border: 1px solid #CCC;
    background: #F6F6F6;    
}     
.admin-page-framework-fields.sortable {
    margin-bottom: 1.2em; /* each sortable field does not have a margin bottom so this rule gives a margin between the fields and the description */
}           

/* Media Upload Buttons */
.admin-page-framework-field .button.button-small {
    width: auto;
}
 
/* Fonts */
.font-lighter {
    font-weight: lighter;
}

/* Dashicons */ 
.admin-page-framework-field .button.button-small.dashicons {
    font-size: 1.2em;
    padding-left: 0.2em;
    padding-right: 0.22em;

}
CSSRULES;
        }   

        /**
         * Returns the collapsible sections specific CSS rules.
         * 
         * @since       3.4.0
         * @internal
         */
        static private function _getCollapsibleSectionsRules() {

            $_sCSSRules = <<<CSSRULES
/* Collapsible Sections Title Block */            
.admin-page-framework-collapsible-sections-title, 
.admin-page-framework-collapsible-section-title
{
    font-size:13px;
    background-color: #fff;
    padding: 15px 18px;
    margin-top: 1em; 
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}

.admin-page-framework-collapsible-sections-title.collapsed
.admin-page-framework-collapsible-section-title.collapsed {
    border-bottom: 1px solid #dfdfdf;
    margin-bottom: 1em; /* gives a margin for the debug info at the bottom of the meta box */
}
.admin-page-framework-collapsible-section-title {
    margin-top: 0;
}
.admin-page-framework-collapsible-section-title.collapsed {
    margin-bottom: 0;
}

/* Collapsible Sections Title Block in Meta Boxes */            
#poststuff .metabox-holder .admin-page-framework-collapsible-sections-title.admin-page-framework-section-title h3,
#poststuff .metabox-holder .admin-page-framework-collapsible-section-title.admin-page-framework-section-title h3
{
    font-size: 1em;
    margin: 0;
}

/* Collapsible Sections Title Dashicon */            
.admin-page-framework-collapsible-sections-title.accordion-section-title:after,
.admin-page-framework-collapsible-section-title.accordion-section-title:after 
{
    top: 12px;
    right: 15px;
}
.admin-page-framework-collapsible-sections-title.accordion-section-title:after,
.admin-page-framework-collapsible-section-title.accordion-section-title:after {
    content: '\\f142';
}
.admin-page-framework-collapsible-sections-title.accordion-section-title.collapsed:after,
.admin-page-framework-collapsible-section-title.accordion-section-title.collapsed:after 
{
    content: '\\f140';
}

/* Collapsible Sections Content Block */            
.admin-page-framework-collapsible-sections-content, 
.admin-page-framework-collapsible-section-content
{
    border: 1px solid #dfdfdf;
    border-top: 0;
    background-color: #fff;
    /* margin-bottom: 1em; */  /* gives a margin for the debug info at the bottom of the meta box */
}

tbody.admin-page-framework-collapsible-content {
    display: table-caption;     /* 'block' will be assigned in JavaScript if the browser is not Chrome */
    padding: 10px 20px 15px 20px;
}
/* Collapsible section containers get this class selector in Google Chrome */
tbody.admin-page-framework-collapsible-content.table-caption {
    display: table-caption; /* For some reasons, this display mode gives smooth animation in Google Chrome */
}
/* The Toggle All button */
.admin-page-framework-collapsible-toggle-all-button-container {
    margin-top: 1em;
    margin-bottom: 1em;
    width: 100%;
    display: table; /* if block, it gets hidden inside the section toggle bar */
}
.admin-page-framework-collapsible-toggle-all-button.button {

    height: 36px;
    line-height: 34px;
    padding: 0 16px 6px;    
    font-size: 20px;    /* Determines the dashicon size  */
    width: auto;
}

/* Repeatable Section buttons inside the collapsible section title block */
.admin-page-framework-collapsible-section-title .admin-page-framework-repeatable-section-buttons {
    /* Collapsible section bar has an icon at the right end so the repeatable button needs to be placed before it */
    margin: 0;
    margin-right: 2em; 
    margin-top: -0.32em;
}
/* When a section_title field is in the caption tag, do not set the margin-top to align vertically */
.admin-page-framework-collapsible-section-title .admin-page-framework-repeatable-section-buttons.section_title_field_sibling {
    margin-top: 0;
}

.admin-page-framework-collapsible-section-title .repeatable-section-button {
    background: none;   /* for Wordpress v3.7.x or below, the background image need to be removed as well */
}
CSSRULES;
            if ( version_compare( $GLOBALS['wp_version'], '3.8', '<' ) ) {
                $_sCSSRules .= <<<CSSRULES
.admin-page-framework-collapsible-sections-title.accordion-section-title:after,
.admin-page-framework-collapsible-section-title.accordion-section-title:after 
{
    content: '';
    top: 18px;
}
.admin-page-framework-collapsible-sections-title.accordion-section-title.collapsed:after,
.admin-page-framework-collapsible-section-title.accordion-section-title.collapsed:after 
{
    content: '';
}                 
.admin-page-framework-collapsible-toggle-all-button.button {
    font-size: 1em;
}

.admin-page-framework-collapsible-section-title .admin-page-framework-repeatable-section-buttons {
    top: -8px;
}
CSSRULES;
            }   
            return $_sCSSRules;
            
        }
            
        /**
         * Returns the meta-box form specific CSS rules.
         * 
         * @since       3.3.0
         * @internal
         */
        static private function _getMetaBoxFormRules() {     

            return <<<CSSRULES
/* Meta-box form fields */
.postbox .title-colon {
    margin-left: 0.2em;
}
.postbox .admin-page-framework-section .form-table > tbody > tr > td,
.postbox .admin-page-framework-section .form-table > tbody > tr > th
{
    display: inline-block;
    width: 100%;
    padding: 0;
    /* 3.4.0+ In IE inline-block does not take effect for td and th so make them float */
    float: right;
    clear: right; 
}

.postbox .admin-page-framework-field {
    width: 96%; /* Not 100% because it will stick out */
}            

/* Sortable fields do not look well if the width is fully expanded  */
.postbox .sortable .admin-page-framework-field {
    width: auto;
}
            
/* Field Titles */             
.postbox .admin-page-framework-section .form-table > tbody > tr > th {
    font-size: 13px;
    line-height: 1.5;
    margin: 1em 0px;    
    font-weight: 700;
}

/* Metabox Section Heading Info */
#poststuff .metabox-holder .admin-page-framework-section-title h3 {
    border: none;
    font-weight: bold;
    font-size: 1.3em;
    margin: 1em 0;
    padding: 0;
    font-family: 'Open Sans', sans-serif;     
    cursor: inherit;     
    -webkit-user-select: inherit;
    -moz-user-select: inherit;
    user-select: inherit;    

    /* v3.5 or below */
    text-shadow: none;
    -webkit-box-shadow: none;
    box-shadow: none;
    background: none;
}         
CSSRULES;
        }
        /**
         * Returns the widget form specific CSS rules.
         * 
         * @since       3.2.0
         * @internal
         */
        static private function _getWidgetFormRules() {            
            return <<<CSSRULES
/* Widget Forms [3.2.0+] */
.widget .admin-page-framework-section .form-table > tbody > tr > td,
.widget .admin-page-framework-section .form-table > tbody > tr > th
{
    display: inline-block;
    width: 100%;
    padding: 0;
    /* 3.4.0+ In IE inline-block does not take effect for td and th so make them float */
    float: right;
    clear: right;     
}

.widget .admin-page-framework-field,
.widget .admin-page-framework-input-label-container
{
    width: 100%;
}
.widget .sortable .admin-page-framework-field {
    /* Sortable fields have paddings so the width need to be adjusted to fit to 100% */
    padding: 4% 4.4% 3.2% 4.4%;
    width: 91.2%;
}
/* Gives a slight margin between the input field and buttons */
.widget .admin-page-framework-field input {
    margin-bottom: 0.1em;
    margin-top: 0.1em;
}

/* Input fields should have 100% width */
.widget .admin-page-framework-field input[type=text],
.widget .admin-page-framework-field textarea {
    width: 100%;
}

/* When the screen is less than 782px */ 
@media screen and ( max-width: 782px ) {
    
    /* The framework render fields with table elements and those container border seems to affect the width of fields */
    .widget .admin-page-framework-fields {
        width: 99.2%;
    }    
    .widget .admin-page-framework-field input[type='checkbox'], 
    .widget .admin-page-framework-field input[type='radio'] 
    {
        margin-top: 0;
    }

}
CSSRULES;
        }
        /**
         * Returns CSS rules for field errors.
         * @since   3.2.1
         */
        static private function _getFieldErrorRules() {
            return <<<CSSRULES
.field-error, 
.section-error
{
  color: red;
  float: left;
  clear: both;
  margin-bottom: 0.5em;
}
.repeatable-section-error,
.repeatable-field-error {
  float: right;
  clear: both;
  color: red;
  margin-left: 1em;
}
CSSRULES;
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
        static private function _getVersionSpecificRules( $sWPVersion ) {
            
            $_sCSSRules = '';
            if ( version_compare( $sWPVersion, '3.8', '<' ) ) {

                $_sCSSRules .= <<<CSSRULES
.admin-page-framework-field .remove_value.button.button-small {
    line-height: 1.5em; 
}

/* Fix tinyMCE width in 3.7x or below */
.widget .admin-page-framework-section table.mceLayout {
    table-layout: fixed;
}
CSSRULES;
            
            }
            // If the WordPress version is greater than equal to 3.8, add MP6(the admin style introduced in 3.8) specific CSS rules.
            if ( version_compare( $sWPVersion, '3.8', '>=' ) ) {        
            
                $_sCSSRules .= <<<CSSRULES
/* Widget Forms */
.widget .admin-page-framework-section .form-table th
{
    font-size: 13px;
    font-weight: normal;
    margin-bottom: 0.2em;
}

.widget .admin-page-framework-section .form-table {
    margin-top: 1em;
}

/* Repeatable field buttons */
.admin-page-framework-repeatable-field-buttons {
    margin: 2px 0 0 0.3em;
}

/* Fix Sortable fields drag&drop problem in MP6 */ 
    
@media screen and ( max-width: 782px ) {
	.admin-page-framework-fieldset {
		overflow-x: hidden;
	}
}    
CSSRULES;
   
            }
            
            return $_sCSSRules;
            
        }
    
    /**
     * Returns the framework default CSS.
     * 
     * @since       3.2.0
     * @internal
     */
    static public function getDefaultCSSIE() {

        return <<<CSSRULES
/* Collapsible sections - in IE tbody and tr cannot set paddings */        
tbody.admin-page-framework-collapsible-content > tr > th,
tbody.admin-page-framework-collapsible-content > tr > td
{
    padding-right: 20px;
    padding-left: 20px;
}

CSSRULES;

    }
    
}