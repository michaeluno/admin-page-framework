<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to load field resources such as style-sheets and JavaScript scripts.
 *
 * @package     AdminPageFramework/Common/Form/Model
 * @since       3.7.0
 * @internal
 */
class AdminPageFramework_Form_Model___SetFieldResources extends AdminPageFramework_Form_Base {

    public $aArguments              = array();
    public $aFieldsets              = array();
    public $aResources              = array(
        'internal_styles'    => array(),
        'internal_styles_ie' => array(),
        'internal_scripts'   => array(),
        'src_styles'         => array(),
        'src_scripts'        => array(),
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
        $this->___setCommon();
        $this->___set( $this->aFieldsets );
        return $this->aResources;
    }

        /**
         *
         */
        private function ___setCommon() {

            if ( $this->hasBeenCalled( __METHOD__ ) ) {
                return;
            }

            new AdminPageFramework_Form_View___Script_RegisterCallback;

            $this->___setCommonFormInternalCSSRules();

        }
        /**
         *
         */
        private function ___setCommonFormInternalCSSRules() {

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
                $this->aResources[ 'internal_styles' ][] = $_oCSS->get();
            }
            $_aClassNamesForIE = array(
                'AdminPageFramework_Form_View___CSS_CollapsibleSectionIE',
            );
            foreach( $_aClassNamesForIE as $_sClassName ) {
                $_oCSS = new $_sClassName;
                $this->aResources[ 'internal_styles_ie' ][] = $_oCSS->get();
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
         * @since       3.8.14      Changed the scope to private. Added the `$aFieldsets` parameter.
         * @internal
         * @return      void
         */
        private function ___set( $aAllFieldsets ) {
            foreach( $aAllFieldsets as $_sSecitonID => $_aFieldsets ) {
                $this->___setFieldResourcesBySection( $_aFieldsets );
            }
        }
            /**
             *
             * @param       array       $_aFieldsets
             * @since       3.8.14
             */
            private function ___setFieldResourcesBySection( $_aFieldsets ) {

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
                            $this->___setFieldResources( $_aField );
                        }
                        continue;
                    }

                    $_aField = $_aSubSectionOrField;
                    $this->___setFieldResources( $_aField );

                }

            }

                /**
                 * Registers a field.
                 *
                 * @since       3.0.4
                 * @since       3.5.0      Changed the scope to protected as the admin page factory class overrides it.
                 * @since       3.7.0      Moved from `AdminPageFramework_Factory_Model`. Changed the name from `_registerField()`.
                 * @internal
                 * @return      void
                 */
                private function ___setFieldResources( $aFieldset ) {

                    // Check the field conditions.
                    if ( ! $this->___isFieldsetAllowed( $aFieldset ) ) {
                        return;
                    }

                    // 3.8.0+ Parse nested fields.
                    $this->___setResourcesOfNestedFields( $aFieldset );

                    // 3.8.0+ Set the internal field type.
                    if ( $this->hasNestedFields( $aFieldset ) ) {
                        $aFieldset[ 'type' ] = '_nested';
                    }

                    $_sFieldtype            = $this->getElement( $aFieldset, 'type' );
                    $_aFieldTypeDefinition  = $this->getElementAsArray(
                        $this->aFieldTypeDefinitions,
                        $_sFieldtype
                    );

                    $this->___setFieldResourcesByFieldTypeDefinition( $aFieldset, $_sFieldtype, $_aFieldTypeDefinition );

                }

                    /**
                     * Decides whether the field-set should be registered or not.
                     * @since       3.7.0
                     * @return      boolean
                     */
                    private function ___isFieldsetAllowed( $aFieldset ) {
                        return $this->callBack(
                            $this->aCallbacks[ 'is_fieldset_registration_allowed' ],
                            array(
                                true,   // 1st parameter
                                $aFieldset, // 2nd parameter
                            )
                        );
                    }

                    /**
                     * @param       array   $aFieldset
                     * @since       3.8.14
                     */
                    private function ___setResourcesOfNestedFields( $aFieldset ) {

                        if ( ! $this->hasFieldDefinitionsInContent( $aFieldset ) ) {
                            return;
                        }
                        foreach ( $aFieldset[ 'content' ] as $_asNestedFieldset ) {
                            if ( is_scalar( $_asNestedFieldset ) ) {
                                continue;
                            }
                            $this->___setFieldResources( $_asNestedFieldset );
                        }

                    }

                    /**
                     * @param       string  $_sFieldtype
                     * @param       array   $_aFieldTypeDefinition
                     * @since       3.8.14
                     */
                    private function ___setFieldResourcesByFieldTypeDefinition( $aFieldset, $_sFieldtype, $_aFieldTypeDefinition ) {

                        // If the field type is not defined, it is not possible to load resources.
                        if ( empty( $_aFieldTypeDefinition ) ) {
                            return;
                        }

                        /**
                         * Let the field type know a fieldset of the field type is registered.
                         * This is supposed to be done before form validations
                         * so that custom filed types add own routines for the validation.
                         */
                        $this->callback(
                            $_aFieldTypeDefinition[ 'hfDoOnRegistration' ],
                            array(
                                $aFieldset  // 1st parameter
                            )
                        );

                        // Let the main routine do something upon adding fieldset resources such as adding help pane items.
                        $this->callBack(
                            $this->aCallbacks[ 'load_fieldset_resource' ],
                            array(
                                $aFieldset,   // 1st parameter
                            )
                        );

        // @todo [3.7.0+] retrieve fieldset resources set to the `style` and `script` arguments.
        // Be careful not to add duplicate items as currently the sub-field items are parsed.

                        // Is already registered?
                        if ( $this->hasBeenCalled( 'registered_' . $_sFieldtype . '_' . $this->aArguments[ 'structure_type' ] ) ) {
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
                        $this->aResources     = $_oFieldTypeResources->get();

                    }

}
