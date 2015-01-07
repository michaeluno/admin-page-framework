<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides the space to store the shared properties for taxonomy fields.
 *  
 * @since       3.5.0
 * @package     AdminPageFramework
 * @subpackage  Property
 * @extends     AdminPageFramework_Property_MetaBox
 * @internal
 */
class AdminPageFramework_Property_UserMeta extends AdminPageFramework_Property_MetaBox {

    /**
     * Defines      the property type.
     * @since       3.5.0
     * @internal
     */
    public $_sPropertyType = 'user_meta';
 
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