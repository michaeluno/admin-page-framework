<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to return CSS rules for collapsible form sections.
 *
 * @since       3.7.0
 * @package     AdminPageFramework/Common/Form/View/CSS
 * @extends     AdminPageFramework_Form_View___CSS_Base
 * @internal
 */
class AdminPageFramework_Form_View___CSS_CollapsibleSection extends AdminPageFramework_Form_View___CSS_Base {

    /**
     * @since       3.7.0
     * @return      string
     */
    protected function _get() {
        return $this->___getCollapsibleSectionsRules();
    }
        /**
         * Returns the collapsible sections specific CSS rules.
         *
         * @since       3.4.0
         * @internal
         * @since       3.7.0      Moved from `AdminPageFramework_CSS`.
         * @return      string
         */
        private function ___getCollapsibleSectionsRules() {

            $_sCSSRules = <<<CSSRULES
/* Collapsible Sections Title Block */            
.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box, 
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box
{
    font-size:13px;
    background-color: #fff;
    padding: 1em 2.6em 1em 2em;
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

/* Collapsible Sections Title Block  */
#poststuff .admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.admin-page-framework-section-title > .section-title-outer-container > .section-title-container > .section-title,
#poststuff .admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.admin-page-framework-section-title > .section-title-outer-container > .section-title-container > .section-title
{
    font-size: 1em;
    margin: 0 1em 0 0;  /* 3.8.13+ The margin-right is required for fields in the section title area. */
 
}
#poststuff .admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.admin-page-framework-section-title > .section-title-outer-container > .section-title-container > fieldset {
    line-height: 0; /* 3.8.13 to vertically align in the center */
}
#poststuff .admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.admin-page-framework-section-title > .section-title-outer-container > .section-title-container > fieldset .admin-page-framework-field {
    margin: 0;
    padding: 0;
}
/* Collapsible Sections Title Dashicon */            
.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.accordion-section-title:after,
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.accordion-section-title:after 
{
    top: 0.88em;    /* about half of 2em which is the element height, and slightly higher. */
    top: 34%;    /* about half of 2em which is the element height, and slightly higher. */
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
    /* margin-right: 2em; @deprecated 3.8.13+ The section repeatable buttons are no longer float but positioned with the `position` property. */ 
    /* margin-top: -0.32em; @deprecated 3.8.7+ It is placed somewhat high in WP 4.6.1. */
}
/* When a section_title field is in the caption tag, do not set the margin-top to align vertically */
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .admin-page-framework-repeatable-section-buttons.section_title_field_sibling {
    margin-top: 0;
}

.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .repeatable-section-button {
    background: none;       /* for Wordpress v3.7.x or below, the background image need to be removed as well */
    
    /* 3.8.13+ Give a fixed button size */
    line-height: 1.8em; 
    margin: 0;
    padding: 0;
    width: 2em;
    height: 2em;
    text-align: center;
}

/* 3.8.13+ Treat the section title area and the repeatable buttons as columns. */
.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box .section-title-height-fixer, 
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .section-title-height-fixer 
{
    /* This height fixer element gives vertical alignment to the other column elements in the row. The other columns must not set height. */
    height: 100%;
    width: 0;
    display: inline-block;
    vertical-align: middle;
}
.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box .section-title-outer-container, 
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .section-title-outer-container 
{
    width: 88%;
    display: inline-block;
    text-align: left;
    vertical-align: middle;
}
.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box .admin-page-framework-repeatable-section-buttons-outer-container,
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .admin-page-framework-repeatable-section-buttons-outer-container 
{
    width: 10.88%;
    min-width: 60px; /* for the browser width gets lets than 600px */
    display: inline-block;
    text-align: right;
    vertical-align: middle;
}
@media only screen and ( max-width: 782px ) {
    .admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box .section-title-outer-container, 
    .admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .section-title-outer-container 
    {
        width: 87.8%;
    }
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
.collapsed .admin-page-framework-collapsible-button-collapse {
    display: none;
}
.admin-page-framework-collapsible-button-expand {
    display: none;
}
.collapsed .admin-page-framework-collapsible-button-expand {
    display: inline;
}

/* 3.8.0+ In the collapsible section title, the space is too limited so the fields be displayed inline. */
.admin-page-framework-collapsible-section-title .admin-page-framework-fields {
    display: inline;
    vertical-align: middle; /* 3.8.13+ aligning fields in vertically center especially for the collapsible section title era. */
    line-height: 1em; /* 3.8.13+ this helps aligning fields in vertically center. */
}
.admin-page-framework-collapsible-section-title .admin-page-framework-field {
    float: none;
}
.admin-page-framework-collapsible-section-title .admin-page-framework-fieldset {
    display: inline;
    margin-right: 1em;
    vertical-align: middle; /* to have consistent vertical alignment with section title and fields. */
}

/* 3.8.7+ Collapsible section titles. To live with the `placement` argument enabled fields, the section title must be vertically centered. */
#poststuff .admin-page-framework-collapsible-title.admin-page-framework-collapsible-section-title .section-title-container.has-fields .section-title
{
    width: auto;
    display: inline-block;
    margin: 0 1em 0 0.4em;
    vertical-align: middle;
}
CSSRULES;

            $_sCSSRules .= $this->___getForWP38OrBelow();
            $_sCSSRules .= $this->___getForWP53OrAbove();

            return $_sCSSRules;

        }
            private function ___getForWP53OrAbove() {
                if ( version_compare( $GLOBALS[ 'wp_version' ], '5.3', '<' ) ) {
                    return '';
                }
                return <<<CSSRULES
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .repeatable-section-button {
    min-width: 2.4em;
}
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .repeatable-section-button .dashicons {
    font-size: 1.2em;
    height: 100%;
    vertical-align: text-top;
}
.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .repeatable-section-button .dashicons:before {
    vertical-align: middle;
}
@media screen and (max-width: 782px) {
    .admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .admin-page-framework-repeatable-section-buttons {
        white-space: nowrap;
    }
    .admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .repeatable-section-button {
        font-size: 1.4em;
    }
    .admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .repeatable-section-button .dashicons {
        height: unset;
        vertical-align: unset;
    }    
}
CSSRULES;

            }
            private function ___getForWP38OrBelow(){
                if ( version_compare( $GLOBALS[ 'wp_version' ], '3.8', '>=' ) ) {
                    return '';
                }
                return <<<CSSRULES
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

}
