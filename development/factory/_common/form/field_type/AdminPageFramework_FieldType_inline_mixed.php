<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * A field that includes child fields with different field types.
 * 
 * This class defines the `inline_mixed` field type.
 * 
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *     <li>**content** - (optional, array) an array holding child field definition arrays.</li>
 * </ul>
 * 
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 * 
 * <h2>Example</h2>
 * <h3>Check box, Number and Select</h3>
 * <code>
 *  array(
 *      'field_id'      => 'checkbox_number_select',
 *      'type'          => 'inline_mixed',
 *      'title'         => __( 'Checkbox, Number & Select', 'admin-page-framework-loader' ),            
 *      'content'       => array(
 *          array(
 *              'field_id'        => 'enable',
 *              'type'            => 'checkbox',
 *              'label_min_width' => '',
 *              'label'           => __( 'Do something in', 'admin-page-framework-loader' ),        
 *          ),                                
 *          array(
 *              'field_id'        => 'interval',
 *              'type'            => 'number',
 *              'label_min_width' => '',
 *              'default'         => 3,
 *              'attributes'      => array(
 *                  'style'     => 'width: 80px',
 *              ),    
 *          ),    
 *          array(
 *              'field_id'        => 'interval_unit',
 *              'type'            => 'select',
 *              'label_min_width' => '',
 *              'label'           => array(
 *                  'hour'    => __( 'hours', 'admin-page-framework-loader' ),
 *                  'day'     => __( 'days', 'admin-page-framework-loader' ),
 *                  'week'    => __( 'weeks', 'admin-page-framework-loader' ),
 *              ),                    
 *              'default'         => 'day',
 *          ),
 *          array(
 *              'field_id'      => '_text',
 *              'content'       => __( 'to do something else.', 'admin-page-framework-loader' ),
 *          ),                                 
 *      ),
 *  )
 * </code>
 * <h3>Text and Number</h3>
 * <code>
 *  array(
 *      'field_id'      => 'text_number',
 *      'type'          => 'inline_mixed',
 *      'title'         => __( 'Text & Number', 'admin-page-framework-loader' ),            
 *      'repeatable'    => true,
 *      'content'       => array(                             
 *          __( 'Server', 'admin-page-framework-loader' ), 
 *          array(
 *              'field_id'        => 'server',
 *              'type'            => 'text',
 *              'default'         => 'www.wordpress.org',
 *              'attributes'      => array(
 *                  'fieldset'  => array(
 *                      'style'     => 'min-width: 400px;',
 *                  )
 *              ),                            
 *          ),                
 *          __( 'Port', 'admin-page-framework-loader' ),
 *          array(
 *              'field_id'        => 'port',
 *              'type'            => 'number',
 *              'label_min_width' => '',
 *              'default'         => 3,
 *              'attributes'      => array(
 *                  // 'style'     => 'width: 80px',
 *              ),    
 *          ),
 *
 *      ),
 *  )
 * </code>
 * <h3>Custom Layout</h3>
 * <code>
 *  array(
 *      'field_id'      => 'shipping_address',
 *      'title'         => __( 'Shipping Information', 'admin-page-framework-loader' ),
 *      'type'          => 'inline_mixed',
 *      'repeatable'    => true,
 *      'sortable'      => true,
 *      'content'       => array(
 *          array(
 *              'field_id'      => 'first_name',
 *              'type'          => 'text',
 *              'title'         => __( 'First Name', 'admin-page-framework-loader' ),
 *              'attributes'    => array(
 *                  'fieldset'  => array(
 *                      'style'  => 'width: 48%; padding-right: 2%;',
 *                  ),
 *              ),                        
 *          ),
 *          array(
 *              'field_id'      => 'last_name',
 *              'type'          => 'text',
 *              'title'         => __( 'Last Name', 'admin-page-framework-loader' ),
 *              'attributes'    => array(
 *                  'fieldset'  => array(
 *                      'style'  => 'width: 48%; padding-right: 2%;',
 *                  ),
 *            
 *              ),
 *          ),
 *          array(
 *              'field_id'      => 'mailing_address',
 *              'type'          => 'text',
 *              'title'         => __( 'Street Address', 'admin-page-framework-loader' ),
 *              'attributes'    => array(
 *                  'fieldset'  => array(
 *                      'style'  => 'width: 98%; padding-right: 2%;',
 *                  ),
 *              ),                                        
 *          ),
 *          array(
 *              'field_id'      => 'city',
 *              'type'          => 'text',
 *              'title'         => __( 'City/Town', 'admin-page-framework-loader' ),
 *              'attributes'    => array(
 *                  'fieldset'  => array(
 *                      'style'  => 'width: 36%; padding-right: 2%;',
 *                  ),
 *              ),
 *          ),
 *          array(
 *              'field_id'      => 'state',
 *              'type'          => 'text',
 *              'title'         => __( 'State/Province', 'admin-page-framework-loader' ),
 *              'attributes'    => array(
 *                  'fieldset'  => array(
 *                      'style'  => 'width: 36%; padding-right: 2%;',
 *                  ),
 *              ),                
 *          ),
 *          array(
 *              'field_id'      => 'zip',
 *              'type'          => 'text',
 *              'title'         => __( 'Zip/Postal Code', 'admin-page-framework-loader' ),
 *              'attributes'    => array(
 *                  'fieldset'  => array(
 *                      'style'  => 'width: 22%; padding-right: 2%;',
 *                  ),
 *              ),  
 *          ),                    
 *          array(
 *              'field_id'      => 'telephone',
 *              'type'          => 'text',
 *              'title'         => __( 'Tel. No.', 'admin-page-framework-loader' ),
 *              'attributes'    => array(
 *                  'fieldset'  => array(                               
 *                      'style'  => 'width: 31%; padding-right: 2%;',
 *                  ),
 *              ),
 *          ),
 *          array(
 *              'field_id'      => 'fax',
 *              'type'          => 'text',
 *              'title'         => __( 'Fax No.', 'admin-page-framework-loader' ),
 *              'attributes'    => array(
 *                  'fieldset'  => array(
 *                      'style'  => 'width: 31%; padding-right: 2%;',
 *                  ),
 *              ),            
 *          ),
 *          array(
 *              'field_id'      => 'email',
 *              'type'          => 'text',
 *              'title'         => __( 'Email', 'admin-page-framework-loader' ),
 *              'attributes'    => array(
 *                  'fieldset'  => array(
 *                      'style'  => 'width: 32%; padding-right: 2%;',
 *                  ),
 *              ),                  
 *          ),                         
 *      ),            
 *  ) 
 * </code>
 * 
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/inline_mixed.png
 * @package         AdminPageFramework
 * @subpackage      Common/Form/FieldType
 * @extends         AdminPageFramework_FieldType__nested
 * @since           3.8.0
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
        'label_min_width'  => '',       // disabled as the embedded elements are all inline.
        'show_debug_info'  => false,    // 3.8.8+ @todo Examine why this value does not override the default value of field definition arguments and if possible and appropriate, override it.
    );

    
    /**
     * Returns the field type specific CSS output inside the `<style></style>` tags.
     * 
     * @since       3.8.0
     * @internal
     * @return      string
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

/* Input container label 3.8.0+ */
.admin-page-framework-field-inline_mixed .admin-page-framework-input-label-container,
.admin-page-framework-field-inline_mixed .admin-page-framework-input-label-string
{
    min-width: 0;
}

CSSRULES;

    }    

}
