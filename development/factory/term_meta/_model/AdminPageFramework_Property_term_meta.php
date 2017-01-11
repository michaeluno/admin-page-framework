<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides the space to store the shared properties for taxonomy fields.
 *  
 * @since       3.8.0
 * @package     AdminPageFramework/Factory/TermMeta/Property
 * @extends     AdminPageFramework_Property_post_meta_box
 * @internal
 */
class AdminPageFramework_Property_term_meta extends AdminPageFramework_Property_post_meta_box {

    /**
     * Defines the property type.
     * @remark      Setting the property type helps to check whether some components are loaded such as scripts that can be reused per a class type basis.
     * @since       3.8.0
     * @internal
     */
    public $_sPropertyType = 'term_meta';
    
    /**
     * Stores the associated taxonomy slugs to the taxonomy field object.
     * @since       3.8.0
     */
    public $aTaxonomySlugs;
    
    /**
     * Stores the action hook name that gets triggered when the form registration is performed.
     * 'admin_page' and 'network_admin_page' will use a custom hook for it.
     * @since       3.8.0
     * @access      pulbic      Called externally.
     */
    // public $_sFormRegistrationHook = 'admin_enqueue_scripts';      
    // public $_sFormRegistrationHook = 'current_screen';      
        
    
}
