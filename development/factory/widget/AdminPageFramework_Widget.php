<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for creating widgets.
 * 
 * @abstract
 * @since       3.2.0
 * @package     AdminPageFramework
 * @subpackage  Widget
 */
abstract class AdminPageFramework_Widget extends AdminPageFramework_Widget_Controller {    
       
    /**
     * Defines the class object structure type.
     * 
     * This is used to create a property object as well as to define the form element structure.
     * 
     * @since       3.2.0
     * @since       3.7.0      Changed the name from `$_sFieldsType`.
     * @internal
     */
    static protected $_sStructureType = 'widget';
       
    /**
    * The constructor of the class object.
    * 
    * Registers necessary hooks and sets up internal properties.
    * 
    * <h4>Example</h4>
    * <code>
    *   new APF_Widget( __( 'Admin Page Framework', 'admin-page-framework-demo' ) );  // the widget title
    *   new APF_Widget_CustomFieldTypes( __( 'APF - Advanced', 'admin-page-framework-demo' ) );
    *   new APF_Widget_Example( __( 'APF - GitHub Button', 'admin-page-framework-demo' ) );
    * </code>
    * 
    * @return void
    */
    public function __construct( $sWidgetTitle, $aWidgetArguments=array(), $sCapability='edit_theme_options', $sTextDomain='admin-page-framework' ) {
        
        if ( empty( $sWidgetTitle ) ) { 
            return; 
        }
     
        // Properties
        $this->oProp = new AdminPageFramework_Property_Widget( 
            $this,                  // caller object
            null,                   // the caller script path
            get_class( $this ),     // class name
            $sCapability,           // capability 
            $sTextDomain,           // text domain
            self::$_sStructureType  // fields type
        );
        
        $this->oProp->sWidgetTitle           = $sWidgetTitle;
        $this->oProp->aWidgetArguments       = $aWidgetArguments;
                                
        parent::__construct( $this->oProp );
        
        $this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}", $this );
                           
    }
                
}