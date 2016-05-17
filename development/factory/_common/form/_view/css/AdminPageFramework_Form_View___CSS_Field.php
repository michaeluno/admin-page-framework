<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to return CSS rules for form outputs.
 *
 * @since       3.7.0
 * @package     AdminPageFramework
 * @subpackage  Form
 * @internal
 */
class AdminPageFramework_Form_View___CSS_Field extends AdminPageFramework_Form_View___CSS_Base {
    
    /**
     * @since       3.7.0
     * @return      string
     */
    protected function _get() {
        return $this->_getFormFieldRules();
    }
        /**
         * Returns the CSS rules for form fields.
         * 
         * @since       3.2.0
         * @internal
         * @since       3.7.0      Moved from `AdminPageFramework_CSS`.
         * @return      string
         */    
        static private function _getFormFieldRules() {
            return <<<CSSRULES
/* Form Elements */
/* TD paddings when the field title is disabled */
td.admin-page-framework-field-td-no-title {
    padding-left: 0;
    padding-right: 0;
}

/* Fields Container */
.admin-page-framework-fields {
    display: table; /* the block property does not give the element the solid height */
    width: 100%;
    table-layout: fixed;    /* in Firefox, fix the issue that preview images cause the container element to expand */
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

/* Sortable Section and Fields */
.admin-page-framework-sections.sortable-section > .admin-page-framework-section,
.sortable .admin-page-framework-field {
    clear: both;
    float: left;
    display: inline-block;
    /* padding: 1em 1.2em 0.78em; @deprecated 3.7.1 */
    padding: 1em 1.32em 1em;
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

/* Nested fieldsets 3.8.0+ */
.admin-page-framework-fieldset > .admin-page-framework-fields > .admin-page-framework-field > .admin-page-framework-fieldset.multiple-nesting {
    margin-left: 2em;
}
.admin-page-framework-fieldset > .admin-page-framework-fields > .admin-page-framework-field > .admin-page-framework-fieldset {
    margin-bottom: 1em;
}
.admin-page-framework-fieldset.nested-fieldset > .admin-page-framework-nested-field-title {
    font-weight: 600;
    display: inline-block;
    padding: 0 0 0.4em 0;
}

CSSRULES;
        }   

        /**
         * @since       3.7.0
         * @return      string
         */
        protected function _getVersionSpecific() {

            $_sCSSRules = '';
            if ( version_compare( $GLOBALS[ 'wp_version' ], '3.8', '<' ) ) {

                $_sCSSRules .= <<<CSSRULES
.admin-page-framework-field .remove_value.button.button-small {
    line-height: 1.5em; 
}
CSSRULES;
            
            }
            
            // If the WordPress version is greater than equal to 3.8, add MP6(the admin style introduced in 3.8) specific CSS rules.
            if ( version_compare( $GLOBALS[ 'wp_version' ], '3.8', '>=' ) ) {
            
                $_sCSSRules .= <<<CSSRULES
                
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
    
}
