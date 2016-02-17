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
class AdminPageFramework_Form_View___CSS_CollapsibleSection extends AdminPageFramework_Form_View___CSS_Base {
    
    /**
     * @since       3.7.0
     * @return      string
     */
    protected function _get() {
        return $this->_getCollapsibleSectionsRules();
    }
        /**
         * Returns the collapsible sections specific CSS rules.
         * 
         * @since       3.4.0
         * @internal
         * @since       3.7.0      Moved from `AdminPageFramework_CSS`.
         * @return      string
         */
        private function _getCollapsibleSectionsRules() {

            $_sCSSRules = <<<CSSRULES
/* Collapsible Sections Title Block */            
.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box, 
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box
{
    font-size:13px;
    background-color: #fff;
    padding: 15px 18px;
    margin-top: 1em; 
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}

.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.collapsed
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.collapsed {
    border-bottom: 1px solid #dfdfdf;
    margin-bottom: 1em; /* gives a margin for the debug info at the bottom of the meta box */
}
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box {
    margin-top: 0;
}
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.collapsed {
    margin-bottom: 0;
}

/* Collapsible Sections Title Block in Meta Boxes */            
#poststuff .metabox-holder .admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.admin-page-framework-section-title h3,
#poststuff .metabox-holder .admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.admin-page-framework-section-title h3
{
    font-size: 1em;
    margin: 0;
}

/* Collapsible Sections Title Dashicon */            
.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.accordion-section-title:after,
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.accordion-section-title:after 
{
    top: 12px;
    right: 15px;
}
.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.accordion-section-title:after,
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.accordion-section-title:after {
    content: '\\f142';
}
.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.accordion-section-title.collapsed:after,
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.accordion-section-title.collapsed:after 
{
    content: '\\f140';
}

/** 
 * Collapsible Sections Content Block 
 * @todo ...-content-type-box may have been deprecated. So investigate whether it is not used anywhere and remove it in that case.
 **/           
.admin-page-framework-collapsible-sections-content.admin-page-framework-collapsible-content.accordion-section-content,
.admin-page-framework-collapsible-section-content.admin-page-framework-collapsible-content.accordion-section-content,
.admin-page-framework-collapsible-sections-content.admin-page-framework-collapsible-content-type-box, 
.admin-page-framework-collapsible-section-content.admin-page-framework-collapsible-content-type-box
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
/* Vertically flip the toggle button */
.flipped > .admin-page-framework-collapsible-toggle-all-button.button.dashicons {
    -moz-transform: scaleY(-1);
    -webkit-transform: scaleY(-1);
    transform: scaleY(-1);
    filter: flipv; /*IE*/
}

/* Repeatable Section buttons inside the collapsible section title block */
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .admin-page-framework-repeatable-section-buttons {
    /* Collapsible section bar has an icon at the right end so the repeatable button needs to be placed before it */
    margin: 0;
    margin-right: 2em; 
    margin-top: -0.32em;
}
/* When a section_title field is in the caption tag, do not set the margin-top to align vertically */
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .admin-page-framework-repeatable-section-buttons.section_title_field_sibling {
    margin-top: 0;
}

.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .repeatable-section-button {
    background: none;   /* for Wordpress v3.7.x or below, the background image need to be removed as well */
}

/* 3.7.0+ For the button type collapsible sections, do not set a white color in the background */
.accordion-section-content.admin-page-framework-collapsible-content-type-button {
    background-color: transparent;
}
/* @todo When fields support a collapsible option, move this rule to a common CSS class as this class is for sections. */
.admin-page-framework-collapsible-button {
    color: #888;
    margin-right: 0.4em;
    font-size: 0.8em;
}
/* Toggle the visibility of the buttons */
.admin-page-framework-collapsible-button-collapse {
    display: inline;
} 
.collapsed > * > .admin-page-framework-collapsible-button-collapse {
    display: none;
}
.admin-page-framework-collapsible-button-expand {
    display: none;
}
.collapsed > * > .admin-page-framework-collapsible-button-expand {
    display: inline;
}
CSSRULES;
            if ( version_compare( $GLOBALS['wp_version'], '3.8', '<' ) ) {
                $_sCSSRules .= <<<CSSRULES
.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.accordion-section-title:after,
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.accordion-section-title:after 
{
    content: '';
    top: 18px;
}
.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.accordion-section-title.collapsed:after,
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.accordion-section-title.collapsed:after 
{
    content: '';
}                 
.admin-page-framework-collapsible-toggle-all-button.button {
    font-size: 1em;
}

.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .admin-page-framework-repeatable-section-buttons {
    top: -8px;
}

CSSRULES;
            }

            return $_sCSSRules;
            
        }

}
