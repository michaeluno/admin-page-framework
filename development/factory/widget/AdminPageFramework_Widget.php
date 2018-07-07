<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2018, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for creating widgets.
 * 
 * @abstract
 * @since       3.2.0
 * @package     AdminPageFramework/Factory/Widget
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
    protected $_sStructureType = 'widget';
       
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
     * @since        3.2.0
     * @since        3.8.17      Made it accept an empty parameter for cases that some third-parties assume this is a WP_Widget extending class.
     * @remark       If nothing is passed to the class constructor, it could be a third-party attempts to instantiate this class by misunderstanding that this is an extended class of the `WP_Widget` class.
     * @param        string      The widget title
     * @param        array       Widget arguments
     * @param        string      Capability
     * @param        string      Text domain for translation
     */
    public function __construct( /* $sWidgetTitle, $aWidgetArguments=array(), $sCapability='edit_theme_options', $sTextDomain='admin-page-framework' */ ) {

        $_sThisClassName     = get_class( $this );
        $_bAssumedAsWPWidget = 0 === func_num_args();
        $_aDefaults          = array( '', array(), 'edit_theme_options', 'admin-page-framework' );
        $_aParameters        = $_bAssumedAsWPWidget
            ? $this->___getConstructorParametersUsedForThisClassName( $_sThisClassName ) + $_aDefaults
            : func_get_args() + $_aDefaults;

        $this->___setProperties( $_aParameters, $_sThisClassName, $_bAssumedAsWPWidget );
        parent::__construct( $this->oProp );
                           
    }

        /**
         * Sets up properties.
         * @since       3.8.17
         * @return      void
         */
        private function ___setProperties( $aParameters, $sThisClassName, $_bAssumedAsWPWidget ) {

            $sWidgetTitle        = $aParameters[ 0 ];
            $aWidgetArguments    = $aParameters[ 1 ];
            $sCapability         = $aParameters[ 2 ];
            $sTextDomain         = $aParameters[ 3 ];

            $_sPropertyClassName = isset( $this->aSubClassNames[ 'oProp' ] )
                ? $this->aSubClassNames[ 'oProp' ]
                : 'AdminPageFramework_Property_' . $this->_sStructureType;
            $this->oProp         = new $_sPropertyClassName(
                $this,                  // caller object
                null,                   // the caller script path
                $sThisClassName,        // class name
                $sCapability,           // capability
                $sTextDomain,           // text domain
                $this->_sStructureType  // fields type
            );

            $this->oProp->sWidgetTitle            = $sWidgetTitle;
            $this->oProp->aWidgetArguments        = $aWidgetArguments;
            $this->oProp->bAssumedAsWPWidget      = $_bAssumedAsWPWidget;
            if ( $_bAssumedAsWPWidget ) {
                $this->oProp->aWPWidgetMethods    = get_class_methods( 'WP_Widget' );
                $this->oProp->aWPWidgetProperties = get_class_vars( 'WP_Widget' );
            }

        }

        /**
         * Finds used parameter values to this class constructor of the given extended class name that is already registered.
         *
         * @param       string    $sClassName
         * @return      array     Parameters passed to this class constructor of the given class name.
         * @since       3.8.17
         */
        private function ___getConstructorParametersUsedForThisClassName( $sClassName ) {
            if ( ! is_object( $GLOBALS[ 'wp_widget_factory' ] ) ) {
                return array();
            }
            if ( ! isset( $GLOBALS[ 'wp_widget_factory' ]->widgets[ $sClassName ] ) ) {
                return array();
            }
            // At this point, the widget of the given name is already registered.
            $_oWPWidget = $GLOBALS[ 'wp_widget_factory' ]->widgets[ $sClassName ];
            return array(
                $_oWPWidget->oCaller->oProp->sWidgetTitle,      // 1. widget title,
                $_oWPWidget->oCaller->oProp->aWidgetArguments,  // 2. widget arguments
                $_oWPWidget->oCaller->oProp->sCapability,       // 3. capability
                $_oWPWidget->oCaller->oProp->sTextDomain,       // 4. text domain
            );
        }

}
