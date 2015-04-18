<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides the space to store the shared properties for custom post types.
 * 
 * This class stores various types of values. This is used to encapsulate properties so that it helps to avoid naming conflicts.
 * 
 * @since       3.2.0
 * @package     AdminPageFramework
 * @subpackage  Property
 * @extends     AdminPageFramework_Property_Base
 * @internal
 */
class AdminPageFramework_Property_Widget extends AdminPageFramework_Property_Base {
    
    /**
     * Defines the property type.
     * @remark      Setting the property type helps to check whether some components are loaded such as scripts that can be reused per a class type basis.
     * @since       3.2.0
     * @internal
     */
    public $_sPropertyType = 'widget';
    
    /**
     * Indicates the fields type.
     * 
     * @since       3.2.0
     * @internal
     */
    public $sFieldsType = 'widget';

    /**
     * Stores the extended instantiated class name.
     * 
     * @since       3.2.0
     * @var         string
     * @access      public
     */     
    public $sClassName = '';

    
    /**
     * Stores the caller script path.
     * 
     * @since 3.2.0
     * @var string
     * @access public
     */         
    public $sCallerPath = '';
    
    /**
     * Stores the widget title.
     * 
     * @since   3.2.0
     */
    public $sWidgetTitle = '';
    
    /**
     * Stores the widget arguments.
     * 
     * Structure:
     * array(
     *  'classname'     => '...',
     *  'description'   => __( '...', '...' ),
     * )
     * 
     * @since       3.2.0
     */
    public $aWidgetArguments = array();    
        
    /**
     * Determines whether the widget title should be displayed in the front end.
     * 
     * By default when the 'title' field ID exists and has a value, the framework displays the title. 
     * This property value can disable this behaviour by setting it to false.
     * 
     * @since       3.5.7
     */
    public $bShowWidgetTitle = true;
        
}