<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the submit field type.
 * 
 * @package         AdminPageFramework
 * @subpackage      FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 * @internal
 */
class AdminPageFramework_FieldType_submit extends AdminPageFramework_FieldType {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'submit', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'redirect_url'  => null,
        'href'          => null,
        'reset'         => null, 
        'email'         => null,    // [3.3.0+] string of an email address to send to or it can be an array with the following keys.
        /* 
            array(
                'to'            => null,    // string|array     The email address to send to or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the the field ID.
                'subject'       => null,    // string|array     The email title or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the the field ID.
                'message'       => null,    // string|array     The email body or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the the field ID.
                'headers'       => null,    // string|array     The email header or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the the field ID.
                'attachments'   => null,    // string|array     The file path(s) or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the the field ID.
            )
        */
        'attributes'    => array(
            'class' => 'button button-primary',
        ),    
    );    

    /**
     * Returns the field type specific CSS rules.
     * 
     * @since           2.1.5
     * @since           3.3.1           Changed from `_replyToGetStyles()`.
     */ 
    protected function getStyles() {
        return <<<CSSRULES
/* Submit Buttons */
.admin-page-framework-field input[type='submit'] {
    margin-bottom: 0.5em;
}
CSSRULES;
    }
    
    /**
     * Returns the output of the field type.
     * 
     * @since       2.1.5       Moved from `AdminPageFramework_FormField`.
     * @since       3.3.1       Changed from `_replyToGetField()`.
     */
    protected function getField( $aField ) {
        
        $aField['label'] = $aField['label'] ? $aField['label'] : $this->oMsg->get( 'submit' );
        
        if ( isset( $aField['attributes']['src'] ) ) {
            $aField['attributes']['src'] = $this->resolveSRC( $aField['attributes']['src'] );
        }
        $_bIsImageButton    = isset( $aField['attributes']['src'] ) && filter_var( $aField['attributes']['src'], FILTER_VALIDATE_URL );
        $_aInputAttributes  = array(
                // the type must be set because child class including export will use this method; in that case, the export type will be assigned which input tag does not support
                'type'  => $_bIsImageButton ? 'image' : 'submit', 
                'value' => ( $_sValue = $this->_getInputFieldValueFromLabel( $aField ) ),
            ) 
            + $aField['attributes']
            + array(
                'title' => $_sValue,
                'alt'   => $_bIsImageButton ? 'submit' : '',
            );

        $_aLabelAttributes          = array(
            'style' => $aField['label_min_width'] ? "min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";" : null,
            'for'   => $_aInputAttributes['id'],
            'class' => $_aInputAttributes['disabled'] ? 'disabled' : null,
        );
        $_aLabelContainerAttributes = array(
            'style' => $aField['label_min_width'] ? "min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";" : null,
            'class' => 'admin-page-framework-input-label-container admin-page-framework-input-button-container admin-page-framework-input-container',
        );

        return 
            $aField['before_label']
            . "<div " . $this->generateAttributes( $_aLabelContainerAttributes ) . ">"
                . $this->_getExtraFieldsBeforeLabel( $aField ) // this is for the import field type that cannot place file input tag inside the label tag.
                . "<label " . $this->generateAttributes( $_aLabelAttributes ) . ">"
                    . $aField['before_input']
                    . $this->_getExtraInputFields( $aField )
                    . "<input " . $this->generateAttributes( $_aInputAttributes ) . " />" // this method is defined in the base class
                    . $aField['after_input']
                . "</label>"
            . "</div>"
            . $aField['after_label'];
        
    }
    
    /**
     * Returns extra output for the field.
     * 
     * This is for the import field type that extends this class. The import field type cannot place the file input tag inside the label tag that causes a problem in FireFox.
     * 
     * @since 3.0.0
     */
    protected function _getExtraFieldsBeforeLabel( &$aField ) {
        return '';     
    }
    
    /**
     * Returns the output of hidden fields for this field type that enables custom submit buttons.
     * @since 3.0.0
     */
    protected function _getExtraInputFields( &$aField ) {
        
        $_aOutput   = array();
        $_aOutput[] = "<input " 
                . $this->generateAttributes( 
                    array(
                        'type'  => 'hidden',
                        'name'  => "__submit[{$aField['input_id']}][input_id]",
                        'value' => $aField['input_id'],
                    ) 
                )
            . " />";
        $_aOutput[] = "<input " 
                . $this->generateAttributes( 
                    array(
                        'type'  => 'hidden',
                        'name'  => "__submit[{$aField['input_id']}][field_id]",
                        'value' => $aField['field_id'],
                    ) 
                )
            . " />";
        $_aOutput[] = "<input " 
                . $this->generateAttributes( 
                    array(
                        'type'  => 'hidden',
                        'name'  => "__submit[{$aField['input_id']}][name]",
                        'value' => $aField['_input_name_flat'],
                    ) 
                )
            . " />";
        $_aOutput[] = "<input " 
                . $this->generateAttributes( 
                    array(
                        'type'  => 'hidden',
                        'name'  => "__submit[{$aField['input_id']}][section_id]",
                        'value' => isset( $aField['section_id'] ) && '_default' !== $aField['section_id'] 
                            ? $aField['section_id'] 
                            : '',
                    ) 
                )
            . " />";
            
        /* for the redirect_url key */
        if ( $aField['redirect_url'] ) {
            $_aOutput[] = "<input " 
                . $this->generateAttributes( 
                    array(
                        'type'  => 'hidden',
                        'name'  => "__submit[{$aField['input_id']}][redirect_url]",
                        'value' => $aField['redirect_url'],
                    ) 
                )
                . " />";
        }
        /* for the href key */
        if ( $aField['href'] ) {            
            $_aOutput[] = "<input " 
                . $this->generateAttributes( 
                    array(
                        'type'  => 'hidden',
                        'name'  => "__submit[{$aField['input_id']}][link_url]",
                        'value' => $aField['href'],
                    ) 
                )
                . " />";
        }
        /* for the 'reset' key */
        if ( $aField['reset'] ) {            
            $_aOutput[] = ! $this->_checkConfirmationDisplayed( $aField['_input_name_flat'], 'reset' )
                ? "<input "
                    . $this->generateAttributes( 
                        array(
                            'type'  => 'hidden',
                            'name'  => "__submit[{$aField['input_id']}][is_reset]",
                            'value' => '1',
                        ) 
                    )
                . " />"
                : "<input "
                    . $this->generateAttributes( 
                        array(
                            'type'  => 'hidden',
                            'name'  => "__submit[{$aField['input_id']}][reset_key]",
                            'value' => is_array( $aField['reset'] )   // set the option array key to delete.
                                ? implode( '|', $aField['reset'] )
                                : $aField['reset'],
                        )
                    )
                . " />";    
        }
        /* for the 'email' key */
        if ( ! empty( $aField['email'] ) ) {
            
            $this->setTransient( 'apf_em_' . md5( $aField['_input_name_flat'] . get_current_user_id() ), $aField['email'] );
            $_aOutput[] = ! $this->_checkConfirmationDisplayed( $aField['_input_name_flat'], 'email' )
                ? "<input "
                    . $this->generateAttributes( 
                        array(
                            'type'  => 'hidden',
                            'name'  => "__submit[{$aField['input_id']}][confirming_sending_email]",
                            'value' => '1',
                        ) 
                    )
                    . " />" 
                : "<input "
                    . $this->generateAttributes( 
                        array(
                            'type'  => 'hidden',
                            'name'  => "__submit[{$aField['input_id']}][confirmed_sending_email]",
                            'value' => '1',
                        ) 
                    )
                    . " />";
        }
        return implode( PHP_EOL, $_aOutput );  
        
    }
         
        /**
         * A helper function for the above getSubmitField() that checks if a reset confirmation message has been displayed or not when the 'reset' key is set.
         * 
         */
        private function _checkConfirmationDisplayed( $sFlatFieldName, $sType='reset' ) {
                            
            switch( $sType ) {
                default:
                case 'reset':       // admin page framework _ reset confirmation
                    $_sTransientKey = 'apf_rc_' . md5( $sFlatFieldName . get_current_user_id() );
                    break;
                case 'email':       // admin page framework _ email confirmation
                    $_sTransientKey = 'apf_ec_' . md5( $sFlatFieldName . get_current_user_id() );   
                    break;
            }
            
            $_bConfirmed        = false === $this->getTransient( $_sTransientKey ) 
                ? false
                : true;
            
            if ( $_bConfirmed ) {
                $this->deleteTransient( $_sTransientKey );
            }
                
            return $_bConfirmed;
            
        }

    /*
     * Shared Methods 
     */

    /**
     * Retrieves the input field value from the label.
     * 
     * This method is similar to the above <em>getInputFieldValue()</em> but this does not check the stored option value.
     * It uses the value set to the <var>label</var> key. 
     * This is for submit buttons including export custom field type that the label should serve as the value.
     * 
     * @remark  The submit, import, and export field types use this method.
     * @since   2.0.0
     * @since   2.1.5 Moved from AdminPageFramwrork_InputField. Changed the scope to protected from private. Removed the second parameter.
     */ 
    protected function _getInputFieldValueFromLabel( $aField ) {    
        
        // If the value key is explicitly set, use it. But the empty string will be ignored.
        if ( isset( $aField['value'] ) && $aField['value'] != '' ) { return $aField['value']; }
        
        if ( isset( $aField['label'] ) ) { return $aField['label']; }
        
        // If the default value is set,
        if ( isset( $aField['default'] ) ) { return $aField['default']; }
        
    }
    
}