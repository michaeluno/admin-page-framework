<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to return CSS rules for tool tip outputs embedded in forms.
 *
 * @since       3.7.0
 * @package     AdminPageFramework/Common/Form/View/CSS
 * @internal
 */
class AdminPageFramework_Form_View___CSS_ToolTip extends AdminPageFramework_Form_View___CSS_Base {
    
    /**
     * @since       3.7.0
     * @return      string
     * @see         http://www.menucool.com/tooltip/css-tooltip
     */
    protected function _get() {

        return <<<CSSRULES

/* Inside Field Title */        
th > label > span > .admin-page-framework-form-tooltip {
    margin-top: 1px;
    margin-left: 1em;
    
}
/* For admin page fields, put the ? icon to the right hand side */
.admin-page-framework-content th > .admin-page-framework-form-tooltip {
    float: right;
}

.postbox-container th > .admin-page-framework-form-tooltip {
    margin-left: 1em;
    float: none;
}
        
/* Regular section titles have the `+` button and collapsible title bar has a triangle icon so give a left margin */
.admin-page-framework-section-title a.admin-page-framework-form-tooltip,
.admin-page-framework-collapsible-title a.admin-page-framework-form-tooltip {
    margin-left: 1em;
    vertical-align: middle; /* 3.8.13+ Fixes vertical alignment, especially in the collapsible section title area. */
}


.admin-page-framework-section-tab a.admin-page-framework-form-tooltip {
    margin-left: 0.48em;
    color: #BEBEBE;
    vertical-align: middle;
}     
.admin-page-framework-section-tab.nav-tab.active a.admin-page-framework-form-tooltip {
    color: #BEBEBE;
}

/* Font sizees */

/* Question Mark (?) - we want it to be a little bit smaller than the title */
.admin-page-framework-section-title a.admin-page-framework-form-tooltip > span,
.admin-page-framework-collapsible-title a.admin-page-framework-form-tooltip > span {
    font-size: inherit;
}
.admin-page-framework-form-tooltip > span {
    font-size: 1.2em;
    padding-top: 2px;    
    vertical-align: middle; /* Dashicon vertical alignment */
    display: inline; /* for vertical-align to take effect */
}

/* Tip Contents - When it is placed inside h2, h3, h4, the tooltip text becomes large so avoid that */
.admin-page-framework-section-title a.admin-page-framework-form-tooltip > span.admin-page-framework-form-tooltip-content,
.admin-page-framework-collapsible-title a.admin-page-framework-form-tooltip > span.admin-page-framework-form-tooltip-content,
a.admin-page-framework-form-tooltip > .admin-page-framework-form-tooltip-content {
    font-size: 13px;
    font-weight: normal;
}

a.admin-page-framework-form-tooltip {
    /* vertical-align: middle; @deprecated 3.8.7 Withtout this, the element aligns more in vertically center. */
    outline: none; 
    text-decoration: none;
    cursor: default;
    color: #BEBEBE;
}
a.admin-page-framework-form-tooltip > .admin-page-framework-form-tooltip-content > .admin-page-framework-form-tooltip-title {
    font-weight: bold;
}
a.admin-page-framework-form-tooltip strong {
    line-height:30px;
}
a.admin-page-framework-form-tooltip:hover {
    text-decoration: none;
} 
a.admin-page-framework-form-tooltip > span.admin-page-framework-form-tooltip-content {

    position: absolute; 
    padding: 14px 20px 14px;
    width: 320px; 
    line-height:16px;
    color: #111;
    border:1px solid #DCA; 
    background: #FFFFF4;

    /* Fade-in-and-out Effect */
    visibility: hidden;
    opacity:0;
    transition: visibility 0.4s, opacity 0.4s linear;
    
    /* Adjust the position of the tooltip here */
    margin-top: -28px; 
    margin-left: 8px;
    
    /* High z-index is required to appear over the left side bar menu */
    z-index: 100000;
    
}
a.admin-page-framework-form-tooltip:hover > span.admin-page-framework-form-tooltip-content{
    visibility: visible;   
    opacity:1;    /* Fade-in-and-out Effect */
}

/* Balloon Left Arrow */
a.admin-page-framework-form-tooltip:hover > span.admin-page-framework-form-tooltip-content::before {
    content: ' ';
    position: absolute;
    top: 40%;
    right: 100%; /* To the left of the tooltip */
    margin-top: -5px;
    border-width: 7px;
    border-style: solid;
    border-color: transparent #DCA transparent transparent;
}
a.admin-page-framework-form-tooltip:hover > span.admin-page-framework-form-tooltip-content::after {
    content: ' ';
    position: absolute;
    top: 40%;
    right: 100%; /* To the left of the tooltip */
    margin-top: -4px;
    border-width: 6px;
    border-style: solid;
    border-color: transparent #FFFFF4 transparent transparent;
}

/* Tooltip Box Shadow */
a.admin-page-framework-form-tooltip > span.admin-page-framework-form-tooltip-content {
    border-radius:4px;
    box-shadow: 5px 5px 8px #CCC;
    -webkit-box-shadow: 5px 5px 8px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 5px 5px 8px rgba(0, 0, 0, 0.2);
    box-shadow: 5px 5px 8px rgba(0, 0, 0, 0.2);    
}

/* Title */
.admin-page-framework-form-tooltip-title {
    font-weight: bold;
    display: block;
}

/* Debug Info - Field Arguments */
a.admin-page-framework-form-tooltip.debug-info-field-arguments > span.admin-page-framework-form-tooltip-content {
    min-width: 640px;
    background-color: #F8F8F8;
}
a.admin-page-framework-form-tooltip.debug-info-field-arguments > span.admin-page-framework-form-tooltip-content .dump-array {
    max-height: 400px;
    margin: 20px 8px;
}
/* For smaller screens */
@media (max-width: 640px) {
    a.admin-page-framework-form-tooltip.debug-info-field-arguments > span.admin-page-framework-form-tooltip-content {
        max-width: 280px;
    } 
}

a.admin-page-framework-form-tooltip.debug-info-field-arguments:hover > span.admin-page-framework-form-tooltip-content::before {
    content: ' ';
    position: absolute;
    top: 36px;
    right: 100%; /* To the left of the tooltip */
    margin-top: -5px;
    border-width: 8px;
    border-style: solid;
    border-color: transparent #DCA transparent transparent;
}
a.admin-page-framework-form-tooltip.debug-info-field-arguments:hover > span.admin-page-framework-form-tooltip-content::after {
    content: ' ';
    position: absolute;
    top: 38px;
    right: 100%; /* To the left of the tooltip */
    margin-top: -5px;
    border-width: 6px;
    border-style: solid;
    border-color: transparent #F8F8F8 transparent transparent;
}

CSSRULES;
            
        }
    
}
