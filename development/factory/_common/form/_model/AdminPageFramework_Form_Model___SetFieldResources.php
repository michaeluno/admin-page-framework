<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to build forms.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.7.0
 */
class AdminPageFramework_Form_Model___SetFieldResources extends AdminPageFramework_Form_Base {
    
    public $aArguments              = array();
    public $aFieldsets              = array();
    public $aResources              = array(
        'inline_styles'    => array(),
        'inline_styles_ie' => array(),
        'inline_scripts'   => array(),
        'src_styles'       => array(),
        'src_scripts'      => array(),
    );
    public $aFieldTypeDefinitions   = array();
    public $aCallbacks              = array(
        'is_fieldset_registration_allowed' => null,
    );
    
    /**
     * Sets up hooks.
     * @since       3.7.0
     */
    public function __construct( /* $aArguments, $aFieldsets, $aResources, $aFieldTypeDefinitions, $aCallbacks */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aArguments,
            $this->aFieldsets,
            $this->aResources,
            $this->aFieldTypeDefinitions,
            $this->aCallbacks,
        );
        $this->aArguments               = $_aParameters[ 0 ];
        $this->aFieldsets               = $_aParameters[ 1 ];
        $this->aResources               = $_aParameters[ 2 ];
        $this->aFieldTypeDefinitions    = $_aParameters[ 3 ];
        $this->aCallbacks               = $_aParameters[ 4 ] + $this->aCallbacks;
        
    }
    
    /**
     * Returns an updated the resource array.
     * 
     * @since       3.7.0
     * @return      array
     */
    public function get() {
        $this->_setCommon();
        $this->_set();
        return $this->aResources;
    }
        private static $_bCalled = false;
        /**
         * 
         */
        private function _setCommon() {
            if ( self::$_bCalled ) {
                return;
            }
            self::$_bCalled = true;
            
            new AdminPageFramework_Form_View___Script_RegisterCallback;
            
            $this->_setCommonFormInlineCSSRules();
            
        }
        /**
         * 
         * 
         */
        private function _setCommonFormInlineCSSRules() {
            
            $_aClassNames = array(
                'AdminPageFramework_Form_View___CSS_Form',
                'AdminPageFramework_Form_View___CSS_Field',
                'AdminPageFramework_Form_View___CSS_Section',
                'AdminPageFramework_Form_View___CSS_CollapsibleSection',
                'AdminPageFramework_Form_View___CSS_FieldError',
                'AdminPageFramework_Form_View___CSS_ToolTip',
            );
            foreach( $_aClassNames as $_sClassName ) {
                $_oCSS = new $_sClassName;
                $this->aResources[ 'inline_styles' ][] = $_oCSS->get();
            }
            $_aClassNamesForIE = array(
                'AdminPageFramework_Form_View___CSS_CollapsibleSectionIE',
            );
            foreach( $_aClassNames as $_sClassName ) {
                $_oCSS = new $_sClassName;
                $this->aResources[ 'inline_styles_ie' ][] = $_oCSS->get();
            }
            
        }
        
        /**
         * Registers the given fields.
         * 
         * @remark      `$oHelpPane` and `$oHeadTab` need to be set in the extended class.
         * @remark      This method should be called after the `_loadFieldTypeDefinitions()` emthod.
         * @since       3.0.0
         * @since       3.7.0      Moved from `AdminPageFramework_Factory_Model`. Changed the name from `_registerFields()`.
         * Removed the 1st parameter.
         * @internal
         * @return      void
         */
        protected function _set( ) {

            // Parse all added fieldsets and check associated resources.
// @todo Find a way to retrieve the section id for nested sections and fields.            
            foreach( $this->aFieldsets as $_sSecitonID => $_aFieldsets ) {
                
                $_bIsSubSectionLoaded = false;
                foreach( $_aFieldsets as $_iSubSectionIndexOrFieldID => $_aSubSectionOrField )  {
// @todo Examine if this structure is correct or not. 
// It may not be necessary to check the sub-section dimensions as this is not the saved options array.
                    // if it's a sub-section
                    if ( $this->isNumericInteger( $_iSubSectionIndexOrFieldID ) ) {

                        // no need to repeat the same set of fields
                        if ( $_bIsSubSectionLoaded ) { 
                            continue;
                        }
                        $_bIsSubSectionLoaded = true;
                        foreach( $_aSubSectionOrField as $_aField ) {
                            $this->_setFieldResources( $_aField );     
                        }
                        continue;
                    }
                        
                    $_aField = $_aSubSectionOrField;
                    $this->_setFieldResources( $_aField );
                
                }
            }
            
        }
            /**
             * Registers a field.
             * 
             * @since       3.0.4
             * @since       3.5.0       Changed the scope to protected as the admin page factory class overrides it.
             * @since       3.7.0      Moved from `AdminPageFramework_Factory_Model`. Changed the name from `_registerField()`.
             * @internal
             * @return      void
             */
            private function _setFieldResources( array $aFieldset ) {
                                
                // Check the field conditions here.
                if ( ! $this->_isFieldsetAllowed( $aFieldset ) ) {
                    return;
                }
                
                $_sFieldtype            = $this->getElement( $aFieldset, 'type' );
                $_aFieldTypeDefinition  = $this->getElementAsArray(
                    $this->aFieldTypeDefinitions,
                    $_sFieldtype
                );                

                // If the field type is not defined, it is not possible to load resources.
                if ( empty( $_aFieldTypeDefinition ) ) {
                    return;
                }                
                
                // Call the callback method to let the field type know a fieldset of the field type is registered.
                // This is supposed to be done before form validations so taht custom filed types add own routines for the validation.
                if ( is_callable( $_aFieldTypeDefinition[ 'hfDoOnRegistration' ] ) ) {
                    call_user_func_array( 
                        $_aFieldTypeDefinition[ 'hfDoOnRegistration' ], 
                        array( $aFieldset )
                    );
                }
                
                // Let the main routine do something upon adding fieldset resources such as adding help pane items.
                $this->callBack(
                    $this->aCallbacks[ 'load_fieldset_resource' ], 
                    array(
                        $aFieldset,   // 1st parameter 
                    )
                );
                
// @todo [3.7.0+] retrieve fieldset resources set to the `style` and `script` arguments.
// Be careful not to add duplicate items as currently the sub-field items are parsed.

                // Check the cache
                if ( $this->_isAlreadyRegistered( $_sFieldtype, $this->aArguments[ 'structure_type' ] ) ) {
                    return;
                }
                
                // Initialize the filed type - triggers callbacks defined in the field type.
                new AdminPageFramework_Form_Model___FieldTypeRegistration(
                    $_aFieldTypeDefinition,
                    $this->aArguments[ 'structure_type' ] // used for caching - the values will be cached by fields type
                );
                
                // Get resource items. The set resources to the property will be inserted later by the method defined in the View class component.
                $_oFieldTypeResources = new AdminPageFramework_Form_Model___FieldTypeResource(
                    $_aFieldTypeDefinition,
                    $this->aResources
                );
                $this->aResources = $_oFieldTypeResources->get();
                
            }     
                
                /**
                 * Checks if the given field type has been registered already by the given fields type.
                 * @since       3.7.0
                 * @return      boolean
                 */
                private function _isAlreadyRegistered( $sFieldtype, $sStructureType ) {
                    if ( isset( self::$_aRegisteredFieldTypes[ $sFieldtype . '_' .$sStructureType ] ) ) {
                        return true;
                    }
                    self::$_aRegisteredFieldTypes[ $sFieldtype . '_' .$sStructureType ] = true;
                    return false;
                }
                    /**
                     * @since       3.7.0
                     */
                    static private $_aRegisteredFieldTypes = array();
    

        
        /**
         * Decides whether the field set should be registered or not.
         * @since       3.7.0
         * @return      boolean
         */
        private function _isFieldsetAllowed( array $aFieldset ) {
            return $this->callBack(
                $this->aCallbacks[ 'is_fieldset_registration_allowed' ], 
                array(
                    true,   // 1st parameter 
                    $aFieldset, // 2nd parameter
                )
            );
        }            

}