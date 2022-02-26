<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the `submit` field type.
 * 
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *     <li>**href** - (optional, string) the url(s) linked to the submit button.</li>
 *     <li>**redirect_url** - (optional, string) the url(s) redirected to after submitting the input form.</li>
 *     <li>**reset** - [2.1.2+] (optional, boolean|string|array) the option key to delete. Set 1 for the entire option. [3.5.3+] In order to reset a particular field that belongs to a section, set an array representing the dimensional keys such as `array( 'my_sectio_id', 'my_field_id' )`.</li>
 *     <li>**confirm** - [3.8.24+] (optional, string|array) A confirmation checkbox to be enabled. If non-empty value is set, it will appear. An empty string by default. An array of the following arguments is accepted.
 *         <ul>
 *             <li>**label** - (string) the checkbox label.</li>
 *             <li>**error** - (string) the error message to display when the user does not check it but presses the submit button.</li>
 *         </ul>
 *     </li>
 *     <li>**skip_confirmation** - [3.7.6+] (optional, boolean) Whether to skip confirmation. Default: `false`.</li>
 *     <li>**email** - [3.9.0+] deprecated Use the `contact` field type.
 *     <li>**save** - [3.9.0+] (optional, boolean) Whether to save the value of the button, which is usually the label text. Default: `false`.</li>
 *     </li>
 * </ul>
 * 
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 * 
 * <h2>Example</h2>
 * <code>
 *  array( 
 *      'field_id'          => 'submit_button_field',
 *      'title'             => __( 'Submit Button', 'admin-page-framework-loader' ),
 *      'type'              => 'submit',
 *      'save'              => false,
 *  )
 * </code>
 * <h3>Submit Button as a Link</h3>
 * <code>
 *  array( 
 *      'field_id'          => 'submit_button_link',
 *      'type'              => 'submit',
 *      'title'             => __( 'Link Button', 'admin-page-framework-loader' ),
 *      'label'             => 'WordPress',
 *      'href'              => 'https://wordpress.org',
 *      'attributes'        => array(
 *          'class'     => 'button button-secondary',     
 *          'title'     => __( 'Go to Google!', 'admin-page-framework-loader' ),
 *          'style'     => 'background-color: #C1DCFA;',
 *          'field'     => array(
 *              'style' => 'display: inline; clear: none;',
 *          ),
 *      ), 
 *  )
 * </code>
 * <h3>Download Button</h3>
 * <code>
 *  array( 
 *      'field_id'      => 'submit_button_download',
 *      'title'         => __( 'Download Button', 'admin-page-framework-loader' ),
 *      'type'          => 'submit',
 *      'label'         => __( 'Admin Page Framework', 'admin-page-framework-loader' ),
 *      'href'          => 'http://downloads.wordpress.org/plugin/admin-page-framework.latest-stable.zip',
 *  ) 
 * </code>
 * 
 * <h3>Redirect Button</h3>
 * Unlike the `href` argument, with the `redirect` argument, the form data will be saved and the user gets redirected.
 * <code>
 *  array( 
 *      'field_id'      => 'submit_button_redirect',
 *      'title'         => __( 'Redirect Button', 'admin-page-framework-loader' ),
 *      'type'          => 'submit',
 *      'label'         => __( 'Dashboard', 'admin-page-framework-loader' ),
 *      'redirect_url'  => admin_url(),
 *      'attributes'    => array(
 *          'class' => 'button button-secondary',
 *      ),
 *  )
 * </code>
 *  
 * <h3>Submit Button with an Image</h3>
 * Instead of a text label, an image can be used for the button.
 * <code>
 *  array( 
 *      'field_id'          => 'image_submit_button',
 *      'title'             => __( 'Image Submit Button', 'admin-page-framework-loader' ),
 *      'type'              => 'submit',
 *      'href'              => 'http://en.michaeluno.jp/donate',
 *      'attributes'        =>  array(
 *         'src'    => AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/image/donation.gif',
 *         'alt'    => __( 'Submit', 'admin-page-framework-loader' ),
 *         'class'  => '',
 *      ),
 *  )
 * </code>
 *
 * <h3>Reset Button</h3>
 * With the `reset` argument, the user can reset stored form data.
 * <code>
 *  array( 
 *      'field_id'      => 'submit_button_reset',
 *      'title'         => __( 'Reset Button', 'admin-page-framework-loader' ),
 *      'type'          => 'submit',
 *      'label'         => __( 'Reset', 'admin-page-framework-loader' ),
 *      'reset'         => true,
 *      'attributes'    => array(
 *          'class' => 'button button-secondary',
 *      ),
 *  )
 * </code>
 * 
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/submit.png
 * @package         AdminPageFramework/Common/Form/FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
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
                'to'            => null,    // string|array     The email address to send to or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.
                'subject'       => null,    // string|array     The email title or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.
                'message'       => null,    // string|array     The email body or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.
                'headers'       => null,    // string|array     The email header or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.
                'attachments'   => null,    // string|array     The file path(s) or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.
                'is_html'       => true,    // boolean  Whether the mail should be sent as an html text
                'from'          => null,    // the sender email or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.
                'name'          => null,    // the sender name or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.
            )
        */
        'confirm'       => '',
        'skip_confirmation' => false,   // 3.7.6+ For emails.
        'attributes'    => array(
            'class' => 'button button-primary',
        ),
        'save'          => false,       // 3.9.0+
    );    

    /**
     * @return array
     * @since  3.9.0
     */
    protected function getEnqueuingScripts() {
        return array(
            array(
                'handle_id'     => 'admin-page-framework-submit-field-type',
                'src'           => dirname( __FILE__ ) . '/js/submit.bundle.js',
                'in_footer'     => true,
                'dependencies'  => array( 'jquery', ),
            ),
        );
    }

    /**
     * Returns the output of the field type.
     * 
     * @since       2.1.5   Moved from `AdminPageFramework_FormField`.
     * @since       3.3.1   Changed from `_replyToGetField()`.
     * @internal
     * @param       array   $aField
     * @return      string
     */
    protected function getField( $aField ) {
        
        $aField                     = $this->___getFormattedFieldArray( $aField );
        $_aInputAttributes          = $this->_getInputAttributes( $aField );
        $_aLabelAttributes          = $this->___getLabelAttributes( $aField, $_aInputAttributes );
        $_aLabelContainerAttributes = $this->___getLabelContainerAttributes( $aField );

        return 
            $aField[ 'before_label' ]
            . "<div " . $this->getAttributes( $_aLabelContainerAttributes ) . ">"
                . $this->_getExtraFieldsBeforeLabel( $aField ) // this is for the import field type that cannot place file input tag inside the label tag.
                . "<label " . $this->getAttributes( $_aLabelAttributes ) . ">"
                    . $aField[ 'before_input' ]
                    . $this->_getExtraInputFields( $aField )
                    . "<input " . $this->getAttributes( $_aInputAttributes ) . " />" // this method is defined in the base class
                    . $aField[ 'after_input' ]
                . "</label>"
                . $this->___getConfirmationCheckbox( $aField )
            . "</div>"
            . $aField['after_label'];
        
    }
        /**
         * @param  array  $aField
         * @return string
         * @since  3.8.24
         */
        private function ___getConfirmationCheckbox( $aField ) {
            if ( empty( $aField[ 'confirm' ] ) ) {
                return '';
            }
            $_aConfirm    = is_string( $aField[ 'confirm' ] )
                ? array(
                    'label' => $aField[ 'confirm' ]
                )
                : $this->getAsArray( $aField[ 'confirm' ] );
            $_aConfirm    = $_aConfirm + array(
                'label' => $this->oMsg->get( 'submit_confirmation_label' ),
                'error' => $this->oMsg->get( 'submit_confirmation_error' ),
            );
            $_aAttributes = $this->getElementAsArray( $aField, array( 'attributes', 'confirm' ) );
            $_sInput      = $this->getHTMLTag(
                'input',
                array(
                    'type'       => 'checkbox',
                    'name'       => "{$aField[ 'input_id' ]}[confirm]",
                    'class'      => 'confirm-submit',
                    'value'      => 0, // unchecked by default
                    'data-error-message' => $_aConfirm[ 'error' ],
                ) + $_aAttributes
            );
            return "<p class='submit-confirm-container'><label>"
                   . $_sInput
                   . "<span>{$_aConfirm[ 'label' ]}</span>"
                . "</label></p>";
        }

        /**
         * Returns the formatted field definition array.
         *
         * @since       3.5.3
         * @param       array       $aField
         * @return      array       The formatted field definition array.
         * @internal
         */
        private function ___getFormattedFieldArray( array $aField ) {
            
            $aField[ 'label' ] = $aField[ 'label' ]
                ? $aField[ 'label' ] 
                : $this->oMsg->get( 'submit' );
            
            if ( isset( $aField[ 'attributes' ][ 'src' ] ) ) {
                $aField[ 'attributes' ][ 'src' ] = esc_url( $this->getResolvedSRC( $aField[ 'attributes' ][ 'src' ] ) );
            }            
            return $aField;
            
        }    
        /**
         * Returns the label attribute array.
         * 
         * @since       3.5.3
         * @return      array       The label attribute array.
         * @internal
         */            
        private function ___getLabelAttributes( array $aField, array $aInputAttributes ) {
            return array(
                'style' => $aField[ 'label_min_width' ] 
                    ? "min-width:" . $this->getLengthSanitized( $aField[ 'label_min_width' ] ) . ";" 
                    : null,
                'for'   => $aInputAttributes[ 'id' ],
                'class' => $aInputAttributes[ 'disabled' ] 
                    ? 'disabled' 
                    : null,
            );
        }
        /**
         * Returns the label container attribute array.
         * 
         * @since       3.5.3
         * @param       array   $aField
         * @return      array   The label container attribute array.
         * @internal
         */        
        private function ___getLabelContainerAttributes( array $aField ) {           
            return array(
                'style' => $aField[ 'label_min_width' ] || '0' === ( string ) $aField[ 'label_min_width' ]
                    ? "min-width:" . $this->getLengthSanitized( $aField[ 'label_min_width' ] ) . ";" 
                    : null,
                'class' => 'admin-page-framework-input-label-container'
                    . ' admin-page-framework-input-button-container'
                    . ' admin-page-framework-input-container',
            );
        }    
        /**
         * Returns the input attribute array.
         *
         * @param       array       $aField
         * @since       3.5.3
         * @since       3.9.0       Changed the visibility scope to `protected` as the `contact` field type extends it.
         * @return      array       The input attribute array.
         * @internal
         */
        protected function _getInputAttributes( array $aField ) {
            $_bIsImageButton    = isset( $aField[ 'attributes' ][ 'src' ] ) && filter_var( $aField[ 'attributes' ][ 'src' ], FILTER_VALIDATE_URL );
            $_sValue            = $this->_getInputFieldValueFromLabel( $aField );
            return array(
                    // the type must be set because child class including export will use this method; in that case, the export type will be assigned which input tag does not support
                    'type'  => $_bIsImageButton ? 'image' : 'submit', 
                    'value' => $_sValue,
                ) 
                + $aField[ 'attributes' ]
                + array(
                    'title' => $_sValue,
                    'alt'   => $_bIsImageButton ? 'submit' : '',
                );
        }
        
    /**
     * Returns extra output for the field.
     * 
     * This is for the import field type that extends this class. The import field type cannot place the file input tag inside the label tag that causes a problem in FireFox.
     * 
     * @since       3.0.0
     * @param       array   $aField
     * @return      string
     * @internal
     */
    protected function _getExtraFieldsBeforeLabel( &$aField ) {
        return '';     
    }
    
    /**
     * Returns the output of hidden fields for this field type that enables custom submit buttons.
     * @since       3.0.0
     * @internal
     * @param       array   $aField
     * @return      string
     */
    protected function _getExtraInputFields( &$aField ) {
        
        $_aOutput   = array();
        $_aOutput[] = $this->getHTMLTag( 
            'input',
            array(
                'type'  => 'hidden',
                'name'  => "__submit[{$aField[ 'input_id' ]}][input_id]",
                'value' => $aField[ 'input_id' ],
            )
        );
        $_aOutput[] = $this->getHTMLTag( 
            'input',
            array(
                'type'  => 'hidden',
                'name'  => "__submit[{$aField[ 'input_id' ]}][field_id]",
                'value' => $aField[ 'field_id' ],
            ) 
        );            
        $_aOutput[] = $this->getHTMLTag( 
            'input',
            array(
                'type'  => 'hidden',
                'name'  => "__submit[{$aField[ 'input_id' ]}][name]",
                'value' => $aField[ '_input_name_flat' ],
            ) 
        );         
        $_aOutput[] = $this->___getHiddenInput_SectionID( $aField );
        $_aOutput[] = $this->___getHiddenInputByKey( $aField, 'redirect_url' );       
        $_aOutput[] = $this->___getHiddenInputByKey( $aField, 'href' );       
        $_aOutput[] = $this->___getHiddenInput_Reset( $aField );
        $_aOutput[] = $this->_getHiddenInput_Email( $aField );
        return implode( PHP_EOL, array_filter( $_aOutput ) );  
        
    }
        /**
         * Returns the hidden input tag for the section id argument.
         * 
         * @since       3.5.3
         * @internal
         * @return      string      the HTML input tag output for the section id argument.
         * @param       array       $aField
         */    
        private function ___getHiddenInput_SectionID( array $aField ) {
            return $this->getHTMLTag( 
                'input',
                array(
                    'type'  => 'hidden',
                    'name'  => "__submit[{$aField['input_id']}][section_id]",
                    'value' => isset( $aField['section_id'] ) && '_default' !== $aField['section_id'] 
                        ? $aField['section_id'] 
                        : '',
                ) 
            );                  
        }           
        /**
         * Returns the hidden input tag for the given key argument.
         * 
         * @since       3.5.3
         * @internal
         * @param       array       $aField
         * @param       string      $sKey
         * @return      string      the HTML input tag output for the given key argument.
         */        
        private function ___getHiddenInputByKey( array $aField, $sKey ) {
            return isset( $aField[ $sKey ] )
                ? $this->getHTMLTag( 
                    'input',
                    array(
                        'type'  => 'hidden',
                        'name'  => "__submit[{$aField['input_id']}][{$sKey}]",
                        'value' => $aField[ $sKey ],
                    ) 
                )
                : '';            
        }       
        /**
         * Returns the hidden input tag for the 'reset' argument.
         * 
         * @since       3.5.3
         * @internal
         * @param       array       $aField
         * @return      string      the HTML input tag output for the 'reset' argument.
         */        
        private function ___getHiddenInput_Reset( array $aField ) {
            if ( ! $aField[ 'reset' ] ) {
                return '';
            }
            return ! $this->_checkConfirmationDisplayed( $aField, $aField[ '_input_name_flat' ], 'reset' )
                ? $this->getHTMLTag( 
                    'input',
                    array(
                        'type'  => 'hidden',
                        'name'  => "__submit[{$aField['input_id']}][is_reset]",
                        'value' => '1',
                    ) 
                )
                : $this->getHTMLTag( 
                    'input',
                    array(
                        'type'  => 'hidden',
                        'name'  => "__submit[{$aField[ 'input_id' ]}][reset_key]",
                        'value' => is_array( $aField[ 'reset' ] )   // set the option array key to delete.
                            ? implode( '|', $aField[ 'reset' ] )
                            : $aField[ 'reset' ],
                    )
                );      
        }
        /**
         * Returns the hidden input tag for the 'email' argument.
         * 
         * @since       3.5.3
         * @internal
         * @param       array       $aField
         * @return      string      the HTML input tag output for the 'email' argument.
         * @deprecated  3.9.0       Use the `contact` field type.
         * @todo    When the `email` argument is completely removed, move this method to the `contact` field type class.
         */ 
        protected function _getHiddenInput_Email( array $aField ) {
            
            if ( empty( $aField[ 'email' ] ) ) {
                return '';
            }
            if ( in_array( 'submit', $this->aFieldTypeSlugs, true ) ) {
                $this->showDeprecationNotice( 'The <code>email</code> argument has been deprecated.', 'the <code>contact</code> field type' );
            }

            $_sTransientKey = 'apf_em_' . md5( $aField[ '_input_name_flat' ] . get_current_user_id() );
            $this->setTransient( 
                $_sTransientKey,
                array(
                    'nonce' => $this->getNonceCreated( 'apf_email_nonce_' . md5( ( string ) site_url() ), 86400 ),  // @todo the nonce is crated when the page is rendered so change this to when the form is submitted so that a shorter nonce lifespan can be set.
                ) + $this->getAsArray( $aField[ 'email' ] )
            );
            return ! $this->_checkConfirmationDisplayed( $aField, $aField[ '_input_name_flat' ], 'email' )
                ? $this->getHTMLTag( 
                    'input',
                    array(
                        'type'  => 'hidden',
                        'name'  => "__submit[{$aField[ 'input_id' ]}][confirming_sending_email]",
                        'value' => '1',
                    ) 
                )
                : $this->getHTMLTag( 
                    'input',
                    array(
                        'type'  => 'hidden',
                        'name'  => "__submit[{$aField[ 'input_id' ]}][confirmed_sending_email]",
                        'value' => '1',
                    ) 
                );                
        }
    
        /**
         * A helper function for the above `getSubmitField()` that checks if a reset confirmation message has been displayed or not when the `reset` key is set.
         *
         * @param       array   $aField
         * @param       string  $sFlatFieldName
         * @param       string  $sType
         * @return      boolean
         * @internal
         */
        protected function _checkConfirmationDisplayed( $aField, $sFlatFieldName, $sType='reset' ) {
                            
            switch( $sType ) {
                default:
                case 'reset':       // admin page framework _ reset confirmation
                    $_sTransientKey = 'apf_rc_' . md5( $sFlatFieldName . get_current_user_id() );
                    break;
                case 'email':       // admin page framework _ email confirmation
                    $_sTransientKey = 'apf_ec_' . md5( $sFlatFieldName . get_current_user_id() );   
                    break;
            }
            $_bConfirmed = ! ( false === $this->getTransient( $_sTransientKey ) && ! $aField['skip_confirmation'] );
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
     * @remark      The `submit`, `import`, and `export` field types use this method.
     * @since       2.0.0
     * @since       2.1.5       Moved from `AdminPageFramwrork_InputField`. Changed the scope to protected from private. Removed the second parameter.
     * @internal
     * @param       array   $aField
     */ 
    protected function _getInputFieldValueFromLabel( $aField ) {    
        
        // If the value key is explicitly set, use it. But the empty string will be ignored.
        if ( isset( $aField[ 'value' ] ) && $aField[ 'value' ] != '' ) { 
            return $aField[ 'value' ]; 
        }
        
        if ( isset( $aField[ 'label' ] ) ) { 
            return $aField[ 'label' ]; 
        }
        
        // If the default value is set,
        if ( isset( $aField[ 'default' ] ) ) { 
            return $aField[ 'default' ]; 
        }
        
    }
    
}