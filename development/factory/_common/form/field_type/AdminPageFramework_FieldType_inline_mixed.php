<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the `inline_mixed` field type.
 * 
 * 
 * @package         AdminPageFramework
 * @subpackage      FieldType
 * @extends         AdminPageFramework_FieldType__nested
 * @since           3.8.0
 * @internal
 */
class AdminPageFramework_FieldType_inline_mixed extends AdminPageFramework_FieldType__nested {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'inline_mixed' );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'label_min_width'  => '',   // disabled as the embedded elements are all inline.
    );

    
    /**
     * Returns the field type specific CSS output inside the `<style></style>` tags.
     * 
     * @since       3.8.0
     */        
    protected function getStyles() { 
        return <<<CSSRULES
.admin-page-framework-field-inline_mixed {
    width: 98%;
}
.admin-page-framework-field-inline_mixed > fieldset {
    display: inline-block;
    overflow-x: visible;    /* Disable scrollbars which appear when the browser width is narrow */
    padding-right: 0.4em;
}
.admin-page-framework-field-inline_mixed > fieldset > .admin-page-framework-fields
{
    /* display: inline; */
    display: inline;
    width: auto;
    table-layout: auto;
    margin: 0;
    padding: 0;
    vertical-align: middle;
    white-space: nowrap;
}
.admin-page-framework-field-inline_mixed > fieldset > .admin-page-framework-fields > .admin-page-framework-field {
    float: none;
    clear: none;
    width: 100%;    
    display: inline-block;
    margin-right: auto;
    vertical-align: middle; /* For the select field type */
}
/* inline mixed child field title */
.with-mixed-fields > fieldset > label {
    width: auto;
    padding: 0;
}

.admin-page-framework-field-inline_mixed > fieldset > .admin-page-framework-fields > .admin-page-framework-field .admin-page-framework-input-label-string {
    padding: 0;
}
.admin-page-framework-field-inline_mixed > fieldset > .admin-page-framework-fields > .admin-page-framework-field > .admin-page-framework-input-label-container,
.admin-page-framework-field-inline_mixed > fieldset > .admin-page-framework-fields > .admin-page-framework-field > * > .admin-page-framework-input-label-container
{
    padding: 0;
    display: inline-block;
    width: 100%;
        
}
.admin-page-framework-field-inline_mixed > fieldset > .admin-page-framework-fields > .admin-page-framework-field > .admin-page-framework-input-label-container > label,
.admin-page-framework-field-inline_mixed > fieldset > .admin-page-framework-fields > .admin-page-framework-field > * > .admin-page-framework-input-label-container > label
{
    display: inline-block;
}
.admin-page-framework-field-inline_mixed > fieldset > .admin-page-framework-fields > .admin-page-framework-field > .admin-page-framework-input-label-container > label > input,
.admin-page-framework-field-inline_mixed > fieldset > .admin-page-framework-fields > .admin-page-framework-field > * > .admin-page-framework-input-label-container > label > input
{
    display: inline-block;
    min-width: 100%;
    margin-right: auto;
    margin-left: auto;
}

CSSRULES;

    }    

}
