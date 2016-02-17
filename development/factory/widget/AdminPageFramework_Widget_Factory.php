<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * A back-end factory class that redirects callback methods to the main widget class.
 * 
 * @abstract
 * @since       3.2.0
 * @package     AdminPageFramework
 * @subpackage  Widget
 * @extends     WP_Widget
 * @internal
 */
class AdminPageFramework_Widget_Factory extends WP_Widget {
    
    public $oCaller;
    
    /**
     * Sets up internal properties.
     * 
     * @since       3.2.0
     * @return      void
     */
	public function __construct( $oCaller, $sWidgetTitle, array $aArguments=array() ) {
		
        $aArguments = $aArguments
            + array(
                'classname'     => 'admin_page_framework_widget',
                'description'   => '',
            );
            
		parent::__construct(
            $oCaller->oProp->sClassName,  // base id 
            $sWidgetTitle,      // widget title
            $aArguments         // widget arguments
        );
        $this->oCaller = $oCaller;
     
	}
    
    /**
     * Displays the widget contents in the front end.
     * 
     * @since       3.2.0
     * @since       3.5.9       Changed the timing of the hooks (do_{...} and content_{...} ) to allow the user to decide 
     * whether the title should be visible or not depending on the content.
     * @return      void
     */
	public function widget( $aArguments, $aFormData ) {
           
        echo $aArguments[ 'before_widget' ];
        
        $this->oCaller->oUtil->addAndDoActions(
            $this->oCaller,
            'do_' . $this->oCaller->oProp->sClassName,
            $this->oCaller
        );
       
        $_sContent = $this->oCaller->oUtil->addAndApplyFilters(
            $this->oCaller,
            "content_{$this->oCaller->oProp->sClassName}",
            $this->oCaller->content( '', $aArguments, $aFormData ),
            $aArguments,
            $aFormData
        );
        
        // 3.5.9+ Moved this after the content_{...} filter hook so that the user can decide whether the title shoudl be visible or not.
        echo $this->_getTitle( $aArguments, $aFormData );

        echo $_sContent;
        
		echo $aArguments[ 'after_widget' ];
		
	}
        /**
         * Returns the widget title.
         * 
         * @since       3.5.7
         * @remark      The user needs to add a field with the id, `title` to display a title.
         * @remark      In order to disable the title, add a field with the id  `show_title` and if the value yields `false`, 
         * the title will not be displayed.
         * @return      string      The widget title
         */
        private function _getTitle( array $aArguments, array $aFormData ) {
                
            if ( ! $this->oCaller->oProp->bShowWidgetTitle ) {
                return '';
            }
            
            $_sTitle = apply_filters(
                'widget_title',
                $this->oCaller->oUtil->getElement(
                    $aFormData,
                    'title',
                    ''
                ),
                $aFormData,
                $this->id_base
            );
            if ( ! $_sTitle ) {
                return '';
            }

           return $aArguments['before_title']
                . $_sTitle
            . $aArguments['after_title'];
            
        }
            
    /**
     * Validates the submitted form data.
     * 
     * @since       3.2.0
     * @return      mixed       The validated form data. The type should be an array but it is dealt by the framework user it will be unknown.
     */
	public function update( $aSubmittedFormData, $aSavedFormData ) {
                
        return $this->oCaller->oUtil->addAndApplyFilters(
            $this->oCaller,
            "validation_{$this->oCaller->oProp->sClassName}",
            call_user_func_array(
                array( $this->oCaller, 'validate' ),    // triggers __call()
                array( $aSubmittedFormData, $aSavedFormData, $this->oCaller ) // parameters
            ), // 3.5.3+                        
            $aSavedFormData,
            $this->oCaller
        );
 
	}
    
    /**
     * Constructs the widget form with the given saved form data.
     * 
     * In widgets.php, this method is called multiple times per instance of the class defining the widget (widget model).
     * It is called for the number of added widget instances via drag-and-drop in the UI of the widget model that the caller class defines.
     * This means, the framework factory class has to renew the saved data every time this method is called.
     * 
     * @return      void
     */
	public function form( $aSavedFormData ) {
        
        $this->oCaller->oForm->aCallbacks = $this->_getFormCallbacks();

        /**
         * Set the form data - the form object will trigger a callback to construct the saved form data.
         * And the factory abstract class has a defined method (_replyToGetSavedFormData()) for it 
         * and it applies a filter (options_{...}) to the form data (options) array.
         */
        $this->oCaller->oProp->aOptions = $aSavedFormData;
                
        // The hook (load_{...}) in the method triggers the form registration method.
        $this->_loadFrameworkFactory();
        
        // Render the form 
        $this->oCaller->_printWidgetForm();

        /** 
         * Initialize the form object that stores registered sections and fields
         * because this class gets called multiple times to render the form including added widgets 
         * and the initial widget that gets listed on the left hand side of the page.
         * 
         * @since       3.5.2
         */
        $_aFieldTypeDefinitions = $this->oCaller->oForm->aFieldTypeDefinitions;
        $_aSectionsets          = $this->oCaller->oForm->aSectionsets;
        $_aFieldsets            = $this->oCaller->oForm->aFieldsets;
        $this->oCaller->oForm   = new AdminPageFramework_Form_widget(
            array(
                'register_if_action_already_done' => false, // do not register fields right away
            ) + $this->oCaller->oProp->aFormArguments,  // form arguments  
            $this->oCaller->oForm->aCallbacks,  // callbacks 
            $this->oCaller->oMsg
        );
        // Reuse the data of the previous widget instance.
        $this->oCaller->oForm->aFieldTypeDefinitions = $_aFieldTypeDefinitions;
        $this->oCaller->oForm->aSectionsets          = $_aSectionsets;
        $this->oCaller->oForm->aFieldsets            = $_aFieldsets;
        
	}
        /**
         * Calls the load() method of the caller factory object.
         * 
         * Ensures it is called once per a page load.
         * @since       3.7.0
         */
        private function _loadFrameworkFactory() {
            
            /**
             * Call the load method only once per class. Also the added field registrations in the class also done once.
             * @since       3.7.9
             */
            if ( $this->oCaller->oUtil->hasBeenCalled( '_widget_load_' . $this->oCaller->oProp->sClassName ) ) {
                
                // The saved option callback is done with the below load_... callback so for widget form instances called from the second time
                // need to call the callback manually.
                $this->oCaller->oForm->aSavedData = $this->_replyToGetSavedFormData();

                return;
                
            }
                       
            // Trigger the load() method and load_{...} actions. The user sets up the form.
            $this->oCaller->load( $this->oCaller );
            $this->oCaller->oUtil->addAndDoActions(
                $this->oCaller,
                array(
                    'load_' . $this->oCaller->oProp->sClassName,
                ),
                $this->oCaller
            );
            
        }
               
        /**
         * Returns callbacks for the form.
         * @return      array
         * @since       3.7.9
         */
        private function _getFormCallbacks() {
            return array(
                'hfID'          => array( $this, 'get_field_id' ),    // defined in the WP_Widget class.  
                'hfTagID'       => array( $this, 'get_field_id' ),    // defined in the WP_Widget class.  
                'hfName'        => array( $this, '_replyToGetFieldName' ),  // defined in the WP_Widget class.  
                'hfInputName'   => array( $this, '_replyToGetFieldInputName' ),
                
                'saved_data'    => array( $this, '_replyToGetSavedFormData' ),
            )
            + $this->oCaller->oProp->getFormCallbacks()
            ;
        }
    
    /**
     * @return      array
     * @since       3.7.9
     * @callback    form        saved_data
     */
    public function  _replyToGetSavedFormData() {
        return $this->oCaller->oUtil->addAndApplyFilter(
            $this->oCaller, // the caller factory object
            'options_' . $this->oCaller->oProp->sClassName,
            $this->oCaller->oProp->aOptions,      // subject value to be filtered
            $this->id   // 3.7.9+
        );
    }
    
    /**
     * 
     * @remark      This one is tricky as the core widget factory method enclose this value in []. So when the framework field has a section, it must NOT end with ].
     * @since       3.5.7       Moved from `AdminPageFramework_FormField`.
     * @since       3.6.0       Changed the name from `_replyToGetInputName`.
     * @return      string
     */
    public function _replyToGetFieldName( /* $sNameAttribute, array $aFieldset */ ) {
        
        $_aParams      = func_get_args() + array( null, null, null );
        $aFieldset     = $_aParams[ 1 ];

        return $this->_getNameAttributeDimensions( $aFieldset );
    
    }
    
        /**
         * Calculates the name attribute by adding section and field dimensions.
         * 
         * @remark      This one is tricky as the core widget factory method enclose this value in []. So when the framework field has a section, it must NOT end with ].
         * As of WordPress 4.4, the `get_field_name()` method of `WP_Widget` has some handling of strings containing `[`. So avoid the core method to be used as the framework supports nested sections.
         * @since       3.7.2       No longer uses `$this->get_field_name()`.
         * @return      string
         */
        private function _getNameAttributeDimensions( $aFieldset ) {
            $_sSectionIndex = isset( $aFieldset[ 'section_id' ], $aFieldset[ '_section_index' ] )
                ? "[{$aFieldset[ '_section_index' ]}]"
                : "";
            $_sDimensions   = $this->oCaller->isSectionSet( $aFieldset )
                ? $aFieldset[ 'section_id' ] . "]" . $_sSectionIndex . "[" . $aFieldset[ 'field_id' ]
                : $aFieldset[ 'field_id' ];

            return 'widget-' . $this->id_base . '[' . $this->number . '][' . $_sDimensions . ']';
        }
    
    /**
     * 
     * @remark      As of WordPress 4.4, the `get_field_name()` method of `WP_Widget` has some handling of strings containing `[`. So avoid the core method to be used as the framework supports nested sections.
     * @since       3.6.0
     * @return      string
     */
    public function _replyToGetFieldInputName( /* $sNameAttribute, array $aFieldset, $sIndex */ ) {
        
        $_aParams       = func_get_args() + array( null, null, null );
        $aFieldset      = $_aParams[ 1 ];
        $sIndex         = $_aParams[ 2 ];
        $_sIndex        = $this->oCaller->oUtil->getAOrB(
            '0' !== $sIndex && empty( $sIndex ),
            '',
            "[" . $sIndex . "]"
        );

        return $this->_replyToGetFieldName( '', $aFieldset ) . $_sIndex;

    }
  
}
