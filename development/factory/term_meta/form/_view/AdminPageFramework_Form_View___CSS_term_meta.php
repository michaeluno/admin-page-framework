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
 * @since       3.8.0
 * @package     AdminPageFramework/Factory/TermMeta/Form
 * @internal
 */
class AdminPageFramework_Form_View___CSS_term_meta extends AdminPageFramework_Form_View___CSS_Base {
    
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
/* Term meta form fields */
.admin-page-framework-form-table-outer-row-term_meta,
.admin-page-framework-form-table-outer-row-term_meta > td {
    margin: 0;
    padding: 0;    
}
.admin-page-framework-form-table-term_meta > tbody > tr > td {
    margin-left: 0;
    padding-left: 0;
}

/* */
.admin-page-framework-form-table-term_meta .admin-page-framework-sectionset,
.admin-page-framework-form-table-term_meta .admin-page-framework-section 
{
    margin-bottom: 0;
}

/* Add New Term */
.admin-page-framework-form-table-term_meta.add-new-term .title-colon {
    margin-left: 0.2em;
}
.admin-page-framework-form-table-term_meta.add-new-term .admin-page-framework-section .form-table > tbody > tr > td,
.admin-page-framework-form-table-term_meta.add-new-term .admin-page-framework-section .form-table > tbody > tr > th
{
    display: inline-block;
    width: 100%;
    padding: 0;
    /* 3.4.0+ In IE inline-block does not take effect for td and th so make them float */
    float: right;
    clear: right;
}

.admin-page-framework-form-table-term_meta.add-new-term .admin-page-framework-field {

    /* width: 96% causes the repeatable buttons apper on the far right side. */
    width: auto;
}            

/* Fix image width in Firefox */
.admin-page-framework-form-table-term_meta.add-new-term .admin-page-framework-field {
    max-width: 100%;
}
/* Sortable fields do not look well if the width is fully expanded  */
.admin-page-framework-form-table-term_meta.add-new-term .sortable .admin-page-framework-field {
    /* In Firefox, in side meta boxes, the width needs to be smaller for image previews. */
    /* max-width: 84%;  */
    
    /* The above width: 84% looks inconsitent in the main meta box areas */
    width: auto;
}
            
/* Field Titles */             
.admin-page-framework-form-table-term_meta.add-new-term .admin-page-framework-section .form-table > tbody > tr > th {
    font-size: 13px;
    line-height: 1.5;
    /* margin: 1em 0px;     */
    margin: 0;
    font-weight: 700;
}

/* Section Heading Info */
.admin-page-framework-form-table-term_meta .admin-page-framework-section-title h3 {
    border: none;
    font-weight: bold;
    font-size: 1.12em;
    margin: 0;
    padding: 0;
    font-family: 'Open Sans', sans-serif;     
    cursor: inherit;     
    -webkit-user-select: inherit;
    -moz-user-select: inherit;
    user-select: inherit;    

}      
.admin-page-framework-form-table-term_meta .admin-page-framework-collapsible-title h3 {
    margin: 0;
}
.admin-page-framework-form-table-term_meta h4 {
    margin: 1em 0;
    font-size: 1.04em;
}
.admin-page-framework-form-table-term_meta .admin-page-framework-section-tab h4 {
    margin: 0;
}
   
CSSRULES;
        }        
        
    
}
