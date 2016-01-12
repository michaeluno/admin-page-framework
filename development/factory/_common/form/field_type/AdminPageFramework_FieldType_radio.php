<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the radio field type.
 * 
 * @package         AdminPageFramework
 * @subpackage      FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 * @internal
 */
class AdminPageFramework_FieldType_radio extends AdminPageFramework_FieldType {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'radio' );
    
    /**
     * Defines the default key-values of this field type. 
     */
    protected $aDefaultKeys = array(
        'label'         => array(),
        'attributes'    => array(),
    );
 
    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     */ 
    protected function getStyles() {
        return <<<CSSRULES
/* Radio Field Type */
.admin-page-framework-field input[type='radio'] {
    margin-right: 0.5em;
}     
.admin-page-framework-field-radio .admin-page-framework-input-label-container {
    padding-right: 1em;
}     
.admin-page-framework-field-radio .admin-page-framework-input-container {
    display: inline;
}     
.admin-page-framework-field-radio .admin-page-framework-input-label-string  {
    display: inline; /* radio labels should not fold(wrap) after the check box */
}
CSSRULES;
    }

    /**
     * Returns the field type specific JavaScript script.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetScripts()`.
     * @since       3.6.0       Removed the script as the repeatable field mechanism has changed.
     */ 
    protected function getScripts() {
        return '';
    }     
    
    /**
     * Returns the output of the field type.
     * 
     * @since       2.1.5
     * @since       3.0.0     Removed unnecessary parameters.
     * @since       3.3.1     Changed from `_replyToGetField()`.
     */
    protected function getField( $aField ) {
        
        $_aOutput   = array();
        foreach( $this->getAsArray( $aField['label'] ) as $_sKey => $_sLabel ) {            
            $_aOutput[] = $this->_getEachRadioButtonOutput( $aField, $_sKey, $_sLabel );
        }
        $_aOutput[] = $this->_getUpdateCheckedScript( $aField['input_id'] );
        return implode( PHP_EOL, $_aOutput );
            
    }
        /**
         * Returns an HTML output of a single radio button.
         * @since       3.5.3
         * @return      string      The generated HTML output of the radio button.
         */
        private function _getEachRadioButtonOutput( array $aField, $sKey, $sLabel ) {
            
            $_aAttributes = $aField[ 'attributes' ] + $this->getElementAsArray( $aField, array( 'attributes', $sKey ) );
            $_oRadio      = new AdminPageFramework_Input_radio( $_aAttributes );
            $_oRadio->setAttributesByKey( $sKey );
            $_oRadio->setAttribute( 'data-default', $aField['default'] ); // refered by the repeater script
           
            // Output
            return $this->getElementByLabel( $aField[ 'before_label' ], $sKey, $aField[ 'label' ] )
                . "<div class='admin-page-framework-input-label-container admin-page-framework-radio-label' style='min-width: " . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>"
                    . "<label " . $this->getAttributes( 
                            array(
                                'for'   => $_oRadio->getAttribute( 'id' ),
                                'class' => $_oRadio->getAttribute( 'disabled' )
                                    ? 'disabled' 
                                    : null, // important to set null not '' as generateAttributes will not drop the element if it is ''
                            )
                        ) 
                    . ">"
                        . $this->getElementByLabel( $aField[ 'before_input' ], $sKey, $aField[ 'label' ] )
                        . $_oRadio->get( $sLabel )
                        . $this->getElementByLabel( $aField[ 'after_input' ], $sKey, $aField[ 'label' ] )
                    . "</label>"
                . "</div>"
                . $this->getElementByLabel( $aField[ 'after_label' ], $sKey, $aField[ 'label' ] )
                ;
                
        }    

        /**
         * Returns the JavaScript script that updates the checked attribute of radio buttons when the user select one.
         * This helps repeatable field script that duplicate the last checked item.
         * @since       3.0.0
         * @since       3.4.0       Changed the parameter to accept input id from the container tag id to prepare for the support of nested fields.
         */
        private function _getUpdateCheckedScript( $sInputID ) {

            $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function(){
    jQuery( 'input[type=radio][data-id=\"{$sInputID}\"]' ).change( function() {
        // Uncheck the other radio buttons
        jQuery( this ).closest( '.admin-page-framework-field' ).find( 'input[type=radio][data-id=\"{$sInputID}\"]' ).attr( 'checked', false );

        // Make sure the clicked item is checked
        jQuery( this ).attr( 'checked', 'checked' );
    });
});                 
JAVASCRIPTS;
            return 
                "<script type='text/javascript' class='radio-button-checked-attribute-updater'>"
                    . '/* <![CDATA[ */'
                    . $_sScript
                    . '/* ]]> */'
                . "</script>";
            
        }    
}
