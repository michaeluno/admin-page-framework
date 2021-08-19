<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * A set of radio buttons that lets the user pick an option.
 *
 * This class defines the radio field type.
 *
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * <h2>Example</h2>
 * <code>
 *  array(
 *      'field_id'      => 'radio',
 *      'title'         => __( 'Radio Button', 'admin-page-framework-loader' ),
 *      'type'          => 'radio',
 *      'label'         => array(
 *          'a' => 'Apple',
 *          'b' => 'Banana ( this option is disabled. )',
 *          'c' => 'Cherry'
 *      ),
 *      'default'       => 'c', // yields Cherry; its key is specified.
 *      'after_label'   => '<br />',
 *      'attributes'    => array(
 *          'b' => array(
 *              'disabled' => 'disabled',
 *          ),
 *      ),
 *  )
 * </code>
 *
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/radio.png
 * @package         AdminPageFramework/Common/Form/FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
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
     * Returns the field type specific JavaScript script.
     *
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetScripts()`.
     * @since       3.6.0       Removed the script as the repeatable field mechanism has changed.
     * @internal
     * @return      string
     * @deprecated
     */
    protected function getScripts() {
return '';
        $_aJSArray = json_encode( $this->aFieldTypeSlugs );
        /* The below function will be triggered when a new repeatable field is added. Since the APF repeater script does not
            renew the upload button and the preview elements (while it does on the input tag value), the renewal task must be dealt here separately. */
        return <<<JAVASCRIPTS
jQuery( document ).ready( function(){

    jQuery().registerAdminPageFrameworkCallbacks( { 
        
        /**
         * Called when a field of this field type gets repeated.
         */
        repeated_field: function( oCloned, aModel ) {            
            oCloned.find( 'input[type=radio]' )
                .off( 'change' )                                    
                .on( 'change', function( e ) {
            
                // Uncheck the other radio buttons
                // prop( 'checked', ... ) does not seem to take effect so use .attr( 'checked' ) also.
                // removeAttr( 'checked' ) causes JQMIGRATE warnings for its deprecation.  
                jQuery( this ).closest( '.admin-page-framework-field' ).find( 'input[type=radio]' )
                    .prop( 'checked', false )
                    .attr( 'checked', false ); 
                                    
                // Make sure the clicked item is checked                
                jQuery( this )
                    .prop( 'checked', true )
                    .attr( 'checked', 'checked' );       
            });                           
        },
    },
    {$_aJSArray}
    );
});
JAVASCRIPTS;

    }

    /**
     * @return array
     * @since  3.9.0
     */
    protected function getEnqueuingScripts() {
        return array(
            array(
                'handle_id'         => 'admin-page-framework-field-type-radio',
                'src'               => dirname( __FILE__ ) . '/js/radio.bundle.js',
                'in_footer'         => true,
                'dependencies'      => array( 'jquery', 'admin-page-framework-script-form-main' ),
                'translation_var'   => 'AdminPageFrameworkFieldTypeRadio',
                'translation'       => array(
                    'fieldTypeSlugs' => $this->aFieldTypeSlugs,
                    'messages'       => array(),
                ),
            ),
        );
    }

    /**
     * Returns the output of the field type.
     *
     * @since       2.1.5
     * @since       3.0.0     Removed unnecessary parameters.
     * @since       3.3.1     Changed from `_replyToGetField()`.
     * @internal
     * @return      string
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
         *
         * @since       3.5.3
         * @internal
         * @return      string      The generated HTML output of the radio button.
         */
        private function _getEachRadioButtonOutput( array $aField, $sKey, $sLabel ) {

            $_aAttributes = $aField[ 'attributes' ] + $this->getElementAsArray( $aField, array( 'attributes', $sKey ) );
            $_oRadio      = new AdminPageFramework_Input_radio( $_aAttributes );
            $_oRadio->setAttributesByKey( $sKey );
            $_oRadio->setAttribute( 'data-default', $aField['default'] ); // refered by the repeater script

            // Output
            return $this->getElementByLabel( $aField[ 'before_label' ], $sKey, $aField[ 'label' ] )
                . "<div " . $this->getLabelContainerAttributes( $aField, 'admin-page-framework-input-label-container admin-page-framework-radio-label' ) . ">"
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
         *
         * @since       3.0.0
         * @since       3.4.0       Changed the parameter to accept input id from the container tag id to prepare for the support of nested fields.
         * @internal
         */
        private function _getUpdateCheckedScript( $sInputID ) {

            $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function(){
    jQuery( 'input[type=radio][data-id=\"{$sInputID}\"]' ).on( 'change', function( e ) {
    
        // Uncheck the other radio buttons
        jQuery( this ).closest( '.admin-page-framework-field' ).find( 'input[type=radio][data-id=\"{$sInputID}\"]' )
            .prop( 'checked', false )
            .attr( 'checked', false );
        
        // Make sure the clicked item is checked
        jQuery( this )  
            .prop( 'checked', true )
            .attr( 'checked', 'checked' );

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
