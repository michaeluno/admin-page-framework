<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 * 
 */

/**
 * [3.9.0+] The Contact field type is a custom submit field that helps create custom contact forms by sending data of sibling fields within the same section.
 * 
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *     <li>**email** - (optional, array|string) A string of an email address to send to or it can be an array with the following keys.
 *         <ul>
 *             <li>**to** - (string|array) The email address to send to or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.</li>
 *             <li>**subject** - (string|array) The email title or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.</li>
 *             <li>**message** - (string|array) The email body or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.</li>
 *             <li>**headers** - (string|array) The email header or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.</li>
 *             <li>**attachments** - (string|array) The file path(s) or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID</li>
 *             <li>**name** - (string|array) the sender name or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.</li>
 *             <li>**from** - (string|array) the sender email or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.</li>
 *             <li>**is_html** - (boolean|array) indicates whether the message should be sent as an html or plain text.</li>
 *             <li>**data** - (array) Extra data appended to the message body.</li>
 *         </ul>
 *     </li>
 *     <li>**system_message** - (optional, array) System messages to display to the user.
 *         <ul>
 *             <li>**success** - (string) A message to display when an Email is sent.</li>
 *             <li>**fail** - (string) A message to display when an Email is failed to send.</li>
 *             <li>**error** - (string) A message to display when an error occurs.</li>
 *         </ul>
 *     </li>
 * </ul>
 * 
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 * 
 * <h2>Example</h2>
 * <code>
 *      $_sSectionID   = 'my_contact_form'; // add your section in prior
 *      $_oCurrentUser = wp_get_current_user();
 *      $oFactory->addSettingFields(
 *          $_sSectionID, // the target section id
 *          array(
 *              'field_id'          => 'name',
 *              'title'             => __( 'Your Name', 'admin-page-framework-loader' ),
 *              'type'              => 'text',
 *              'default'           => $_oCurrentUser->user_firstname || $_oCurrentUser->user_lastname
 *                  ? $_oCurrentUser->user_lastname . ' ' .  $_oCurrentUser->user_lastname
 *                  : '',
 *              'attributes'        => array(
 *                  'required' => 'required',
 *                  'placeholder'   => __( 'Type your name.', 'admin-page-framewrok-demo' ),
 *              ),
 *          ),
 *          array(
 *              'field_id'          => 'from',
 *              'title'             => __( 'Your Email Address', 'admin-page-framework-loader' ),
 *              'type'              => 'text',
 *              'default'           => $_oCurrentUser->user_email,
 *              'attributes'        => array(
 *                  'required'      => 'required',
 *                  'placeholder'   =>  __( 'Type your email here.', 'admin-page-framework-loader' ),
 *              ),
 *          ),
 *          array(
 *              'field_id'          => 'use_for_commercial_products',
 *              'title'             => __( 'I use the framework for', 'admin-page-framework-loader' ),
 *              'type'              => 'radio',
 *              'default'           => 1,
 *              'label'             => array(
 *                  1       => __( 'Commercial Products', 'admin-page-framework-loader' ),
 *                  0       => __( 'Non-commercial Products', 'admin-page-framework-loader' ),
 *              ),
 *          ),
 *          array(
 *              'field_id'          => 'use_for',
 *              'title'             => __( 'I use the framework for', 'admin-page-framework-loader' ),
 *              'type'              => 'radio',
 *              'default'           => 'others',
 *              'label'             => array(
 *                  'plugins'   => __( 'Plugins', 'admin-page-framework-loader' ),
 *                  'themes'    => __( 'Themes', 'admin-page-framework-loader' ),
 *                  'others'    => __( 'Others', 'admin-page-framework-loader' ),
 *              ),
 *          ),
 *          array(
 *              'field_id'          => 'subject',
 *              'title'             => __( 'Subject', 'admin-page-framework-loader' ),
 *              'type'              => 'text',
 *              'attributes'        => array(
 *                  'size' => 40,
 *                  'placeholder'   =>  __( 'Type the title here.', 'admin-page-framework-loader' ),
 *              ),
 *          ),
 *          array(
 *              'field_id'          => 'body',
 *              'title'             => __( 'Message', 'admin-page-framework-loader' ),
 *              'type'              => 'textarea',
 *              'rich'              => array(
 *                  'textarea_rows' => 10,  // set height
 *                  // Or use the below to set height
 *                  // 'editor_height' => 300,
 *                  // @see _WP_Editors::parse_settings()
 *              ),
 *              'attributes'        => array(
 *                  'placeholder'   =>  __( 'Type the message here.', 'admin-page-framework-loader' ),
 *              ),
 *          ),
 *          array(
 *              'field_id'          => 'ip',
 *              'type'              => 'hidden',
 *              'value'             => $_SERVER[ "REMOTE_ADDR" ],
 *          ),
 *          array(
 *              'field_id'          => 'send',
 *              'type'              => 'contact',
 *              'label_min_width'   => 0,
 *              'value'             => __( 'Send', 'admin-page-framework-demo' ),
 *              'attributes'        => array(
 *                  'field' => array(
 *                      'style' => 'float:right; clear:none; display: inline;',
 *                  ),
 *              ),
 *              'skip_confirmation' => true,
 *              'system_message'    => array(
 *                  'success' => __( 'Thanks for the feedback!', 'admin-page-framework-demo' ),
 *              ),
 *              'email'             => array(
 *                  // Each argument can accept a string or an array representing the dimensional array key.
 *                  // For example, if there is a field for the email title, and its section id is 'my_section'  and  the field id is 'my_field', pass an array, array( 'my_section', 'my_field' )
 *                  'to'          => 'admin-page-framework@michaeluno.jp',
 *                  'subject'     => array( $_sSectionID, 'subject' ),
 *                  'message'     => array( $_sSectionID ), // the section name enclosed in an array. If it is a field, set it to the second element like array( 'section id', 'field id' ).
 *                  'headers'     => '',
 *                  'attachments' => '',    // the file path
 *                  'is_html'     => true,  // boolean  Whether the mail should be sent as an html text
 *                  'from'        => array( $_sSectionID, 'from' ),
 *                  'name'        => array( $_sSectionID, 'name' ),
 *              ),
 *          )
 *      );
 * </code>
 * 
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/contact.png
 * @package         AdminPageFramework/Common/Form/FieldType
 * @since           3.9.0
 */
class AdminPageFramework_FieldType_contact extends AdminPageFramework_FieldType_submit {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'contact', );

    /**
     * @var string
     * @since 3.9.0
     */
    private $___sAction = 'admin-page-framework_contact_field_type_email';

    /**
     * @since 3.9.0
     */
    protected function construct() {
        $this->aDefaultKeys = array(
            'email' => array(
                'to'            => null,    // string|array     The email address to send to or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.
                'subject'       => null,    // string|array     The email title or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.
                'message'       => null,    // string|array     The email body or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.
                'headers'       => null,    // string|array     The email header or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.
                'attachments'   => null,    // string|array     The file path(s) or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.
                'is_html'       => true,    // boolean  Whether the mail should be sent as an html text
                'from'          => null,    // the sender email or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.
                'name'          => null,    // the sender name or an array representing the key structure of the submitted form data holding the value. The first key should be the section ID and the second key is the field ID.
                'data'          => array(), // array            extra data appended to the message
            ),
            'system_message' => array(
                'success'  => $this->oMsg->get( 'email_sent' ),
                'failure'  => $this->oMsg->get( 'email_could_not_send' ),
                'error'    => $this->oMsg->get( 'nonce_verification_failed' ),
            ),
        ) + $this->aDefaultKeys;
        // wp_ajax_{action name}
        // This is a dummy callback. Adding a dummy callback because WordPress does not proceed in admin-ajax.php
        // and the `admin_init` action is not triggered if no `wp_ajax_{...}` action is registered.
        // for guests:  `wp_ajax_nopriv_{...}` might be needed. Not tested for front-end uses.
        add_action( "wp_ajax_{$this->___sAction}" , '__return_empty_string' );
    }

    /**
     * @param  array $aField
     * @return string
     * @since  3.9.0
     */
    protected function getField( $aField ) {
        return "<div class='result-placeholder'>"
                . "<span class='dashicons'></span>"
            . "</div>"
            . parent::getField( $aField );
    }

    /**
     * @return array
     * @since  3.9.0
     */
    protected function getEnqueuingScripts() {
        return array(
            array(
                'handle_id'     => 'admin-page-framework-field-type-contact',
                'src'           => dirname( __FILE__ ) . '/js/contact.bundle.js',
                'in_footer'         => true,
                'dependencies'      => array( 'jquery', 'admin-page-framework-script-form-main' ),
                'translation_var'   => 'AdminPageFrameworkContactFieldType',
                'translation'       => array(
                    'nonce'         => wp_create_nonce( get_class( $this ) ),
                    'action'        => $this->___sAction,
                    'messages'      => array(
                        'requiredField' =>  $this->oMsg->get( 'please_fill_out_this_field' ),
                    ),
                ),
            ),
        );
    }

    /**
     * Returns the input attribute array.
     *
     * @param       array       $aField
     * @since       3.9.0
     * @return      array       The input attribute array.
     * @internal
     */
    protected function _getInputAttributes( array $aField ) {
        return parent::_getInputAttributes( $aField )
            + $this->getDataAttributeArray( $this->___getEmailArguments( $aField ) );
    }
        /**
         * @param  array $aField
         * @since  3.9.0
         * @return array
         */
        private function ___getEmailArguments( array $aField ) {
            if ( empty( $aField[ 'email' ] ) ) {
                return array();
            }
            return array(
                'email'             => true,    // for the JavaScript script to detect
                'input_flat'        => $aField[ '_input_name_flat' ],  // this is a part of the email transient key. This is passed to the Ajax response event so that it can receive the transient set for this field
                'section_id'        => $aField[ 'section_id' ],
            );
        }

    /**
     * @param  array  $aField
     * @return string
     */
    protected function _getHiddenInput_Email( array $aField ) {

        $_sTransientKey = 'apf_em_' . md5( $aField[ '_input_name_flat' ] . get_current_user_id() );
        $this->setTransient(
            $_sTransientKey,
            array(
                'nonce' => $this->getNonceCreated( 'apf_email_nonce_' . md5( ( string ) site_url() ), 86400 ),  // @todo the nonce is crated when the page is rendered so change this to when the form is submitted so that a shorter nonce lifespan can be set.
                'system_message' => $this->getElementAsArray( $aField, 'system_message' ),
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
     * Calls back the callback function if it is set.
     *
     * Called when the field type is registered.
     * @param array $aFieldset
     * @since 3.9.0
     */
    protected function doOnFieldRegistration( $aFieldset ) {
        if ( isset( $_REQUEST[ 'action' ] ) && $this->___sAction === $_REQUEST[ 'action' ] ) {
            $this->___doAjaxResponse();
        }
    }

    /**
     * @since 3.9.0
     */
    private function ___doAjaxResponse() {

        if ( false === wp_verify_nonce( $this->getElement( $_REQUEST, 'nonce' ),  get_class( $this ) ) ) {
            wp_send_json( array(
                'result'  => false,
                'message' => $this->oMsg->get( 'nonce_verification_failed' )
            ) );
        }

        $_aRequest         = $this->getHTTPRequestSanitized( $_REQUEST );
        $_sInputFlat       = $this->getElement( $_aRequest, 'input_flat' );
        $_sSubmitSectionID = $this->getElement( $_aRequest, 'section_id' );
        $_aInputPath       = explode( '|', $_sInputFlat );
        $_sRootDimension   = reset( $_aInputPath );
        $_aForm            = $this->___getFormDataParsed( $this->getElementAsArray( $_aRequest, array( 'form' ) ) );
        $_aInputs          = $this->getElementAsArray( $_aForm, array( $_sRootDimension ) );
        $_sTransientKey    = 'apf_em_' . md5( $_sInputFlat . get_current_user_id() );
        $_aEmailOptions    = $this->getTransient( $_sTransientKey );
        $_aEmailOptions    = $this->getAsArray( $_aEmailOptions ) + array(
            'nonce'         => '',
            'to'            => '',
            'subject'       => '',
            'message'       => '',
            'headers'       => '',
            'attachments'   => '',
            'is_html'       => false,
            'from'          => '',
            'name'          => '',
        );
        $_aMessages        = $this->getElementAsArray( $_aEmailOptions, array( 'system_message' ) );
        if ( false === wp_verify_nonce( $_aEmailOptions[ 'nonce' ], 'apf_email_nonce_' . md5( ( string ) site_url() ) ) ) {
            wp_send_json( array(
                'result'  => false,
                'message' => $this->getElement( $_aMessages, 'error', $this->oMsg->get( 'nonce_verification_failed' ) )
            ) );
        }
        $_oEmail  = new AdminPageFramework_FormEmail( $_aEmailOptions, $_aInputs, $_sSubmitSectionID );
        $_bResult = $_oEmail->send();
        wp_send_json( array(
            'result'  => $_bResult,
            'message' => $_bResult
                ? $this->getElement( $_aMessages, 'success', $this->oMsg->get( 'email_sent' ) )
                : $this->getElement( $_aMessages, 'failure', $this->oMsg->get( 'email_could_not_send' ) ),
        ) );

    }
        /**
         * @param  array $aForm
         * ```
         *   Array (
         *       [3] => Array(
         *           [name] => (string, length: 8) _wpnonce
         *           [value] => (string, length: 10) 253674ce0a
         *       )
         *       [4] => Array(
         *           [name] => (string, length: 16) _wp_http_referer
         *           [value] => (string, length: 94) /test-admin-page-framework/wp-admin/edit.php?post_type=apf_posts&page=apf_contact&tab=feedback
         *       )
         *       [5] => Array(
         *           [name] => (string, length: 24) APF_Demo[feedback][name]
         *           [value] => (string, length: 0)
         *       )
         * ```
         *
         * @return array
         */
        private function ___getFormDataParsed( array $aForm ) {
            $_aForm = array();
            $aForm  = array_reverse( $aForm );  // to preserver checkbox checked values as checkbox inputs have a preceding hidden input with the same name.
            foreach( $aForm as $_iIndex => $_aNameValue ) {
                parse_str( $_aNameValue[ 'name' ] . '=' . $_aNameValue[ 'value' ], $_a );
                $_aForm = $this->uniteArrays( $_aForm, $_a );
            }
            return array_reverse( $_aForm );    // @note inner nested elements are still in the reversed order
        }
    
}