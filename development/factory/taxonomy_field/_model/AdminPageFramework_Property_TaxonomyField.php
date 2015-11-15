<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides the space to store the shared properties for taxonomy fields.
 *  
 * @since       3.0.0
 * @package     AdminPageFramework
 * @subpackage  Property
 * @extends     AdminPageFramework_Property_MetaBox
 * @internal
 */
class AdminPageFramework_Property_TaxonomyField extends AdminPageFramework_Property_MetaBox {

    /**
     * Defines the property type.
     * @remark      Setting the property type helps to check whether some components are loaded such as scripts that can be reused per a class type basis.
     * @since       3.0.0
     * @internal
     */
    public $_sPropertyType = 'taxonomy_field';
    
    /**
     * Stores the associated taxonomy slugs to the taxonomy field object.
     * @since       3.0.0
     */
    public $aTaxonomySlugs;
    
    /**
     * Stores the option key for the options table.
     * 
     * If the user does not set it, the class name will be applied.
     * @since       3.0.0
     */
    public $sOptionKey;

    /**
     * Stores the action hook name that gets triggered when the form registration is performed.
     * 'admin_page' and 'network_admin_page' will use a custom hook for it.
     * @since       DEVVER
     * @access      pulbic      Called externally.
     */
    // public $_sFormRegistrationHook = 'admin_enqueue_scripts';      
    // public $_sFormRegistrationHook = 'current_screen';      
        
    
}