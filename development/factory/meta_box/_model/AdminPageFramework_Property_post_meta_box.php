<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides the space to store the shared properties for meta boxes.
 * 
 * This class stores various types of values. This is used to encapsulate properties so that it helps to avoid naming conflicts.
 * 
 * @since       2.1.0
 * @package     AdminPageFramework
 * @subpackage  Property
 * @extends     AdminPageFramework_Property_Base
 * @internal
 */
class AdminPageFramework_Property_post_meta_box extends AdminPageFramework_Property_Base {
    
    /**
     * Defines the property type.
     * @remark      Setting the property type helps to check whether some components are loaded such as scripts that can be reused per a class type basis.
     * @since       3.0.0
     * @internal
     */
    public $_sPropertyType = 'post_meta_box';

    /**
     * Stores the meta box id(slug).
     * 
     * @since       2.0.0
     * @since       2.1.0       Moved from the meta box class.
     * @var string
     */
    public $sMetaBoxID ='';
    
    /**
     * Stores the post type slugs associated with the meta box.
     * 
     * This is used in the meta box class for post type pages.
     * 
     * @since       2.0.0
     * @since       2.1.0       Moved from the meta box class.
     * @var array
     */
    public $aPostTypes = array();
    
    /**
     * The condition array for page slugs associated with the meta box.
     * 
     * This is used in the meta box class for pages.
     * 
     * @since       3.0.0
     */
    public $aPages = array();
    
    /**
     * Stores the parameter value, context, for the add_meta_box() function. 
     * 
     * @since       2.0.0
     * @since       2.1.0       Moved from the meta box class.
     * @remark      The value can be either 'normal', 'advanced', or 'side'.
     * @var         string
     * @see         http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
     */
    public $sContext = 'normal';

    /**
     * Stores the parameter value, priority, for the add_meta_box() function. 
     * 
     * @since       2.0.0
     * @since       2.1.0       Moved from the meta box class.
     * @remark      The value can be either 'high', 'core', 'default' or 'low'.
     * @var         string
     * @see         http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
     */
    public $sPriority = 'default';
    
    /**
     * Stores the extended class name.
     * 
     * @since       2.0.0
     * @since       2.1.0       Moved from the meta box class.
     */
    public $sClassName = '';
    
    /**
     * Stores the capability for displayable elements.
     * 
     * @since       2.0.0
     * @since       2.1.0      Moved from the meta box class.
     */
    public $sCapability = 'edit_posts';
        
    /**
     * Stores option values for form fields.
     * @since       2.0.0
     * @since       2.1.0       Moved from the meta box class.
     * @internal
     * @remark      Do not set this here to let the overload method _get() to be triggered when it is called.
     */
    // public $aOptions = array();

    /**
     * Stores the media uploader box's title.
     * @since       2.0.0
     * @since       2.1.0       Moved from the meta box class.
     * @internal
     */
    public $sThickBoxTitle = '';
    
    /**
     * Stores the label for for the "Insert to Post" button in the media uploader box.
     * @since       2.0.0
     * @since       2.1.0       Moved from the meta box class.
     * @internal
     */
    public $sThickBoxButtonUseThis = '';
        
    /**
     * Defines the fields type.
     * @since       3.0.4
     * @internal
     */
    public  $sStructureType = 'post_meta_box';
    
    /**
     * Stores the action hook name that gets triggered when the form registration is performed.
     * 'admin_page' and 'network_admin_page' will use a custom hook for it.
     * @since       3.7.0
     * @access      public      Called externally.
     */
    public $_sFormRegistrationHook = 'admin_enqueue_scripts';
    
    /**
     * Calls the parent constructor by formatting the parameter values.
     */
    public function __construct( $oCaller, $sClassName, $sCapability='edit_posts', $sTextDomain='admin-page-framework', $sStructureType='post_meta_box' ) {
        
        parent::__construct(
            $oCaller,           // caller object
            null,               // caller script path - meta boxes don't need the caller script path.
            $sClassName,        // class name
            $sCapability,       // capability
            $sTextDomain,       // text domain
            $sStructureType     // structure type
        );
     
    }
    
    /**
     * Returns the options array.
     * 
     * @since       3.4.1
     * @internal
     * @return      array       an empty array.
     * @remark      For meta boxes, the options array needs to be set after the fields are set and conditioned 
     * because the options array structure relies on the registered section and field ids. 
     * So here the method just returns an empty array.
     * 
     */
    protected function _getOptions() {
        return array();
    }
    
}
