<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_CSS' ) ) :
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
        $_sCSS = 
"/* Settings Notice */
.wrap div.updated, 
.wrap div.settings-error { 
    clear: both; 
    margin-top: 16px;
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

/* Heading - the meta box container element affects the styles of regular main content output. So it needs to be fixed. */
#poststuff .admin-page-framework-content h3 {
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
  
";        
        return $_sCSS . PHP_EOL 
            . self::_getFormFieldRules() . PHP_EOL
            . self::_getFieldErrorRules() . PHP_EOL
            . self::_getWidgetFormRules() . PHP_EOL
            . self::_getPageLoadStatsRules() . PHP_EOL
            . self::_getVersionSpecificRules( $GLOBALS['wp_version'] );
            
    }

        /**
         * Returns the CSS rules for form fields.
         * 
         * @since       3.2.0
         * @internal
         */    
        static private function _getFormFieldRules() {
            return "
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
    background: none;
    -webkit-box-shadow: none;
    box-shadow: none;
}
/* Metabox Section Heading Info */
#poststuff .metabox-holder .admin-page-framework-section-title h3{
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
/* Fields Container */
.admin-page-framework-fields {
    display: table; /* the block property does not give the element the solid height */
    width: 100%;
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
    padding: 1em 1.2em 0.72em;
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
";            
        }   
        /**
         * Returns CSS rules for field errors.
         * @since   3.2.1
         */
        static private function _getFieldErrorRules() {
            
return "

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

";    
        }        
    
        /**
         * Returns the CSS rules for page load stats.
         * 
         * @since       3.2.0
         * @internal
         */    
        static private function _getPageLoadStatsRules() {
            return 
"/* Page Load Stats */
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
";
            
        }
                
        /**
         * Returns the widget form specific CSS rules.
         * 
         * @since       3.2.0
         * @internal
         */
        static private function _getWidgetFormRules() {            
            return
"/* Widget Forms [3.2.0+] */
.widget .form-table td,
.widget .form-table th
{
    display: inline-block;
    width: 100%;
    padding: 0;
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
        width: 99.4%;
    }    
    .widget .admin-page-framework-field input[type='checkbox'], 
    .widget .admin-page-framework-field input[type='radio'] 
    {
        margin-top: 0;
    }

}
";
        }
        
        /**
         * Returns the framework default CSS rules.
         * 
         * @since       3.2.0
         * @internal
         */    
        static private function _getVersionSpecificRules( $sWPVersion ) {
            
            // If the WordPress version is greater than equal to 3.8, return MP6(the admin style introduced in 3.8) specific CSS rules.
            if ( version_compare( $sWPVersion, '3.8', '>=' ) ) {        
            
                return
"
/* Widget Forms */
.widget .form-table th
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

";        
                
            }
            
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
endif;