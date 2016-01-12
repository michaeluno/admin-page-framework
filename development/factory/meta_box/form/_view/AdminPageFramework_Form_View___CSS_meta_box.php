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
class AdminPageFramework_Form_View___CSS_meta_box extends AdminPageFramework_Form_View___CSS_Base {
    
    /**
     * @since       3.7.0
     * @return      string
     */
    protected function _get() {        
        return $this->_getRules();
    }
        /**
         * Returns the meta-box form specific CSS rules.
         * 
         * @since       3.3.0
         * @since       3.7.0      Moved from `AdminPageFramework_CSS`.
         * @internal
         * @return      string
         */        
        private function _getRules() {
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
    /* Not 100% because it will stick out */
    /* width: 96%; @deprecated 3.7.1 */ 
    
    /* width: 96% causes the repeatable buttons apper on the far right side. */
    width: auto;
}            

/* Fix image width in Firefox */
.postbox .admin-page-framework-field {
    max-width: 100%;
}
/* Sortable fields do not look well if the width is fully expanded  */
.postbox .sortable .admin-page-framework-field {
    /* In Firefox, in side meta boxes, the width needs to be smaller for image previews. */
    max-width: 84%; 
    /* The above width: 84% looks inconsitent in the main meta box areas */
    width: auto;
}
            
/* Field Titles */             
.postbox .admin-page-framework-section .form-table > tbody > tr > th {
    font-size: 13px;
    line-height: 1.5;
    margin: 1em 0px;    
    font-weight: 700;
}

/* Post Metabox Section Heading Info */
#poststuff .metabox-holder .postbox-container .admin-page-framework-section-title h3 {
    border: none;
    font-weight: bold;
    font-size: 1.12em;
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
#poststuff .metabox-holder .postbox-container .admin-page-framework-collapsible-title h3 {
    margin: 0;
}
#poststuff .metabox-holder .postbox-container h4 {
    margin: 1em 0;
    font-size: 1.04em;
}
#poststuff .metabox-holder .postbox-container .admin-page-framework-section-tab h4 {
    margin: 0;
}

/* Side meta boxes */
@media screen and (min-width: 783px) {    
    /* Fix that the text input fields stick out the meta-box container */
    #poststuff #post-body.columns-2 #side-sortables .postbox .admin-page-framework-section .form-table input[type=text]{
        width: 98%;
    }
}            
CSSRULES;
        }        
        
    
}
