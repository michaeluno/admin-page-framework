<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides the space to store the shared properties for taxonomy fields.
 *
 * @since       3.0.0
 * @package     AdminPageFramework/Factory/TaxonomyField/Property
 * @extends     AdminPageFramework_Property_post_meta_box
 * @internal
 */
class AdminPageFramework_Property_taxonomy_field extends AdminPageFramework_Property_post_meta_box {

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
     * @since       3.7.0
     * @access      pulbic      Called externally.
     */
    // public $_sFormRegistrationHook = 'admin_enqueue_scripts';
    // public $_sFormRegistrationHook = 'current_screen';


}
