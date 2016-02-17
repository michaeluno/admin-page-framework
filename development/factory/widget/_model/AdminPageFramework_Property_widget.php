<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
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
class AdminPageFramework_Property_widget extends AdminPageFramework_Property_Base {

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
    public $sStructureType = 'widget';

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
     * @todo    3.7.10      Now the base property supports $sTitle property so maybe use that and deprecated this item.
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

    /**
     * Stores the widget object.
     * 
     * @since       3.5.9
     */
    public $oWidget;

    /**
     * Indicates the action hook to display setting notices.
     * @since       3.7.9
     */
    public $sSettingNoticeActionHook = '';
    
    /**
     * Stores the action hook name that gets triggered when the form registration is performed.
     * 'admin_page' and 'network_admin_page' will use a custom hook for it.
     * @since       3.7.0
     * @access      pulbic      Called externally.
     */
    // public $_sFormRegistrationHook = 'admin_enqueue_scripts'; 
    // public $_sFormRegistrationHook = ''; 

    /**
     * Sets up properties.
     * @since       3.7.0
     */
    public function __construct( $oCaller, $sCallerPath, $sClassName, $sCapability='manage_options', $sTextDomain='admin-page-framework', $sStructureType ) {

        // 3.7.0+
        $this->_sFormRegistrationHook   = 'load_' . $sClassName;
        
        // 3.7.9+ - setting a custom action hook for admin notices prevents the form object from being instantiated unnecessarily.
        $this->sSettingNoticeActionHook = 'load_' . $sClassName;

        parent::__construct(
            $oCaller,
            $sCallerPath,
            $sClassName,
            $sCapability,
            $sTextDomain,
            $sStructureType
        );

    }
    
}
