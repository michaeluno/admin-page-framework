<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the checkbox field type.
 * 
 * @package         AdminPageFramework
 * @subpackage      FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 * @internal
 */
class AdminPageFramework_FieldType_checkbox extends AdminPageFramework_FieldType {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'checkbox' );
    
    /**
     * Defines the default key-values of this field type. 
     */
    protected $aDefaultKeys = array(
        'select_all_button'     => false,        // 3.3.0+   to change the label, set the label here
        'select_none_button'    => false,        // 3.3.0+   to change the label, set the label here
    );
        
    /**
     * Returns the field type specific JavaScript script.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetScripts()`.
     */
    protected function getScripts() {
        new AdminPageFramework_Form_View___Script_CheckboxSelector;
        $_sClassSelectorSelectAll  = $this->_getSelectButtonClassSelectors(
            $this->aFieldTypeSlugs,
            'select_all_button' // data attribute
        );
        $_sClassSelectorSelectNone = $this->_getSelectButtonClassSelectors(
            $this->aFieldTypeSlugs,
            'select_none_button' // data attribute
        );

        return <<<JAVASCRIPTS
jQuery( document ).ready( function(){
    // Add the buttons.
    jQuery( '{$_sClassSelectorSelectAll}' ).each( function(){
        jQuery( this ).before( '<div class=\"select_all_button_container\" onclick=\"jQuery( this ).selectAllAdminPageFrameworkCheckboxes(); return false;\"><a class=\"select_all_button button button-small\">' + jQuery( this ).data( 'select_all_button' ) + '</a></div>' );
    });            
    jQuery( '{$_sClassSelectorSelectNone}' ).each( function(){
        jQuery( this ).before( '<div class=\"select_none_button_container\" onclick=\"jQuery( this ).deselectAllAdminPageFrameworkCheckboxes(); return false;\"><a class=\"select_all_button button button-small\">' + jQuery( this ).data( 'select_none_button' ) + '</a></div>' );
    });
});
JAVASCRIPTS;

    }
        /**
         * 
         * @since       3.5.12
         * @return      string
         */
        private function _getSelectButtonClassSelectors( array $aFieldTypeSlugs, $sDataAttribute='select_all_button' ) {
            
            $_aClassSelectors = array();
            foreach ( $aFieldTypeSlugs as $_sSlug ) {
                if ( ! is_scalar( $_sSlug ) ) {
                    continue;
                }
                $_aClassSelectors[] = '.admin-page-framework-checkbox-container-' . $_sSlug . "[data-{$sDataAttribute}]";
            }

            return implode( ',', $_aClassSelectors );
            
        }

    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     */
    protected function getStyles() {
        return <<<CSSRULES
/* Checkbox field type */
.select_all_button_container, 
.select_none_button_container
{
    display: inline-block;
    margin-bottom: 0.4em;
}
.admin-page-framework-checkbox-label {
    margin-top: 0.1em;
}
.admin-page-framework-field input[type='checkbox' ] {
    margin-right: 0.5em;
}     
.admin-page-framework-field-checkbox .admin-page-framework-input-label-container {
    padding-right: 1em;
}
.admin-page-framework-field-checkbox .admin-page-framework-input-label-string  {
    display: inline; /* Checkbox labels should not fold(wrap) after the check box */
}
CSSRULES;

    }
    
    /**
     * The class selector to indicate that the input tag is a admin page framework checkbox.
     * 
     * This selector is used for the repeatable and sortable field scripts.
     * @since   3.1.7
     */
    protected $_sCheckboxClassSelector = 'apf_checkbox';
    
    /**
     * Returns the output of the field type.
     * 
     * @since       2.1.5
     * @since       3.0.0     Removed unnecessary parameters.
     * @since       3.3.0     Changed from `_replyToGetField()`.
     */
    protected function getField( $aField ) {

        $_aOutput       = array();
        $_bIsMultiple   = is_array( $aField[ 'label' ] );
        foreach( $this->getAsArray( $aField[ 'label' ], true ) as $_sKey => $_sLabel ) {
            $_aOutput[] = $this->_getEachCheckboxOutput(
                $aField,
                $_bIsMultiple
                    ? $_sKey
                    : '',
                $_sLabel
            );
        }

        return "<div " . $this->getAttributes( $this->_getCheckboxContainerAttributes( $aField ) ) . ">"
                . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                . implode( PHP_EOL, $_aOutput )
            . "</div>";
            
    }
        /**
         * Returns the checkbox container element attributes array.
         * @internal
         * @access      protected   The taxonomy field type class accesses this method.
         * @sinec       3.5.3
         * @return      array       The generated attributes array.
         */
        protected function _getCheckboxContainerAttributes( array $aField ) {
            return array(
                'class'                     => 'admin-page-framework-checkbox-container-' . $aField[ 'type' ],
                'data-select_all_button'    => $aField[ 'select_all_button' ]
                    ? ( ! is_string( $aField[ 'select_all_button' ] ) ? $this->oMsg->get( 'select_all' ) : $aField[ 'select_all_button' ] )
                    : null,
                'data-select_none_button'   => $aField[ 'select_none_button' ]
                    ? ( ! is_string( $aField[ 'select_none_button' ] ) ? $this->oMsg->get( 'select_none' ) : $aField[ 'select_none_button' ] )
                    : null,
            );
        }

        /**
         * Returns the output of an individual checkbox by the given key.
         * 
         * @since       3.5.3
         * @return      string      The generated checkbox output.
         */
        private function _getEachCheckboxOutput( array $aField, $sKey, $sLabel ) {

            $_oCheckbox = new AdminPageFramework_Input_checkbox( $aField[ 'attributes' ] );
            $_oCheckbox->setAttributesByKey( $sKey );
            $_oCheckbox->addClass( $this->_sCheckboxClassSelector );

            return $this->getElementByLabel( $aField[ 'before_label' ], $sKey, $aField[ 'label' ] )
                . "<div class='admin-page-framework-input-label-container admin-page-framework-checkbox-label' style='min-width: " . $this->sanitizeLength( $aField[ 'label_min_width' ] ) . ";'>"
                    . "<label " . $this->getAttributes(
                        array(
                            'for'   => $_oCheckbox->getAttribute( 'id' ),
                            'class' => $_oCheckbox->getAttribute( 'disabled' )
                                ? 'disabled'
                                : null,
                        )
                    )
                    . ">"
                        . $this->getElementByLabel( $aField[ 'before_input' ], $sKey, $aField[ 'label' ] )
                        . $_oCheckbox->get( $sLabel )
                        . $this->getElementByLabel( $aField[ 'after_input' ], $sKey, $aField[ 'label' ] )
                    . "</label>"
                . "</div>"
                . $this->getElementByLabel( $aField[ 'after_label' ], $sKey, $aField[ 'label' ] )
                ;
                
        }
    
}
