<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides the space to store the shared properties for taxonomy fields.
 *
 * @since       3.5.0
 * @package     AdminPageFramework/Factory/UserMeta/Property
 * @extends     AdminPageFramework_Property_post_meta_box
 * @internal
 */
class AdminPageFramework_Property_user_meta extends AdminPageFramework_Property_post_meta_box {

    /**
     * Defines      the property type.
     * @since       3.5.0
     * @internal
     */
    public $_sPropertyType = 'user_meta';

    /**
     * Stores the action hook name that gets triggered when the form registration is performed.
     * 'admin_page' and 'network_admin_page' will use a custom hook for it.
     * @since       3.7.0
     * @access      pulbic      Called externally.
     */
    public $_sFormRegistrationHook = 'admin_enqueue_scripts';

    /**
     * Retrieves the option array.
     *
     * This method is triggerd from the __get() overload magic method to set the $aOptions property.
     * @since       3.5.0
     * @internal
     * @return      array       An empty array.
     * @remark      For user meta fields, like meta boxes, the options array needs to be set after the fields are set and conditioned
     * because the options array structure relies on the registered section and field ids.
     * So here the method just returns an empty array.
     */
    protected function _getOptions() {
        return array();
    }

}
