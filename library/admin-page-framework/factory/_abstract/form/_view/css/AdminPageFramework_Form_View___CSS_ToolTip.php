<?php
class AdminPageFramework_Form_View___CSS_ToolTip extends AdminPageFramework_Form_View___CSS_Base {
    protected function _get() {
        return <<<CSSRULES

/* Inside Field Title */        
th > label > span > .admin-page-framework-form-tooltip {
    margin-top: 1px;
    margin-left: 1em;
    /* float: right; */
}
.postbox-container th > label > span > .admin-page-framework-form-tooltip {
    margin-left: 1em;
    float: none;
}
        
/* Regular section titles have + button and collapsible title bar has a triangle icon so give a right margin */
.admin-page-framework-section-title > * > a.admin-page-framework-form-tooltip,
.admin-page-framework-collapsible-title > * > a.admin-page-framework-form-tooltip {
    margin-left: 1em;    
}

/* When it is placed inside h2, h3, h4, the tooltip text becomes large so avoid that */
a.admin-page-framework-form-tooltip > .admin-page-framework-form-tooltip-content {
    font-size: 13px;
    font-weight: normal;
}

.admin-page-framework-section-tab a.admin-page-framework-form-tooltip {
    margin-left: 0.48em;
    color: #BEBEBE;
    vertical-align: middle;
}     
.admin-page-framework-section-tab.nav-tab.active a.admin-page-framework-form-tooltip {
    color: #BEBEBE;
}

/* The question mark (?) size  - we want it to be a little bit smaller thatn the title */
.admin-page-framework-section-title > * > a.admin-page-framework-form-tooltip > span,
.admin-page-framework-collapsible-title > * > a.admin-page-framework-form-tooltip > span {
    margin-top: -4px;
    font-size: inherit;
}
.admin-page-framework-form-tooltip > span {
    
    font-size: 1.2em;
    
    /* Dashicon vertical alignment */
    vertical-align: middle;
    
}

a.admin-page-framework-form-tooltip {
    vertical-align: middle;
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

    display: none; 
    padding: 14px 20px 14px;
    margin-top: -30px; 
    margin-left: 28px;
    width: 400px; 
    line-height:16px;
    
    /* High z-index is required to appear over the left side bar menu */
    z-index: 100000;
    
}
a.admin-page-framework-form-tooltip:hover > span.admin-page-framework-form-tooltip-content{
    display: inline; 
    position: absolute; 
    color: #111;
    border:1px solid #DCA; 
    background: #FFFFF4;
    
    /* Adjust the position of the tooltip here */
    margin-left: -100px;
    margin-top: -72px;
}

/* Balloon Style */
/* .callout {
    z-index: 200000;
    position: absolute;
    top: 30px;
    border: 0;
    left: -12px;
}
 */

/* Tooltip Box Shadow */
a.admin-page-framework-form-tooltip > span.admin-page-framework-form-tooltip-content {
    border-radius:4px;
    box-shadow: 5px 5px 8px #CCC;
}

CSSRULES;
        
    }
}