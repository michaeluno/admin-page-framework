<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods for text messages.
 *
 * @since       2.0.0
 * @since       2.1.6       Multiple instances of this class are disallowed.
 * @since       3.2.0       Multiple instances of this class are allowed but the instantiation is restricted to per text domain basis.
 * @package     AdminPageFramework/Common/Factory/Property
 * @internal
 *
 * @remark      When adding a new framework translation item,
 * Step 1: add a key and the default value to the `$aDefaults` property array.
 * Step 2: add a dummy function call in the `___doDummy()` method so that parser programs can catch it.
 */
class AdminPageFramework_Message {

    /**
     * Stores the framework's messages.
     *
     * @since       2.0.0
     * @since       3.1.3       No item is defined by default but done on the fly per request. The below array structure is kept for backward compatibility.
     * @remark      The user may modify this property directly.
     */
    public $aMessages = array();

    /**
     * Stores default translated items.
     *
     * @remark      These items should be accessed only when its label needs to be displayed.
     * So the translation method `__()` only gets executed for one file.
     *
     * Consider the difference between the two.
     * <code>
     * $_aTranslations = array(
     *      'foo'  => __( 'Foo', 'admin-page-framework' ),
     *      'bar'  => __( 'Bar', 'admin-page-framework' ),
     *       ... more 100 items
     * )
     * return isset( $_aTranslations[ $sKey ] ) ? $_aTranslations[ $sKey ] : '';
     * </code>
     *
     * <code>
     * $_aTranslations = array(
     *      'foo'  => 'Foo',
     *      'bar'  => 'Bar',
     *       ... more 100 items
     * )
     * return isset( $_aTranslations[ $sKey ] )
     *      ? __( $_aTranslations[ $sKey ], $sUserSetTextdomain )
     *      : '';
     * </code>
     * @since       3.5.3
     */
    public $aDefaults = array(

        // AdminPageFramework
        'option_updated'                        => 'The options have been updated.',
        'option_cleared'                        => 'The options have been cleared.',
        'export'                                => 'Export',
        'export_options'                        => 'Export Options',
        'import'                                => 'Import',
        'import_options'                        => 'Import Options',
        'submit'                                => 'Submit',
        'import_error'                          => 'An error occurred while uploading the import file.',
        'uploaded_file_type_not_supported'      => 'The uploaded file type is not supported: %1$s',
        'could_not_load_importing_data'         => 'Could not load the importing data.',
        'imported_data'                         => 'The uploaded file has been imported.',
        'not_imported_data'                     => 'No data could be imported.',
        'upload_image'                          => 'Upload Image',
        'use_this_image'                        => 'Use This Image',
        'insert_from_url'                       => 'Insert from URL',
        'reset_options'                         => 'Are you sure you want to reset the options?',
        'confirm_perform_task'                  => 'Please confirm your action.',
        'specified_option_been_deleted'         => 'The specified options have been deleted.',
        'nonce_verification_failed'             => 'A problem occurred while processing the form data. Please try again.',
        'check_max_input_vars'                  => 'Not all form fields could not be sent. Please check your server settings of PHP <code>max_input_vars</code> and consult the server administrator to increase the value. <code>max input vars</code>: %1$s. <code>$_POST</code> count: %2$s',  // 3.5.11+
        'send_email'                            => 'Is it okay to send the email?',     // 3.3.0+
        'email_sent'                            => 'The email has been sent.',  // 3.3.0+, 3.3.5+ deprecated
        'email_scheduled'                       => 'The email has been scheduled.', // 3.3.5+
        'email_could_not_send'                  => 'There was a problem sending the email',     // 3.3.0+

        // AdminPageFramework_PostType
        'title'                                 => 'Title',
        'author'                                => 'Author',
        'categories'                            => 'Categories',
        'tags'                                  => 'Tags',
        'comments'                              => 'Comments',
        'date'                                  => 'Date',
        'show_all'                              => 'Show All',
        'show_all_authors'                      => 'Show all Authors', // 3.5.10+

        // AdminPageFramework_Link_Base
        'powered_by'                            => 'Thank you for creating with',
        'and'                                   => 'and',

        // AdminPageFramework_Link_admin_page
        'settings'                              => 'Settings',

        // AdminPageFramework_Link_post_type
        'manage'                                => 'Manage',

        // AdminPageFramework_FieldType_{...}
        'select_image'                          => 'Select Image',
        'upload_file'                           => 'Upload File',
        'use_this_file'                         => 'Use This File',
        'select_file'                           => 'Select File',
        'remove_value'                          => 'Remove Value',  // 3.2.0+
        'select_all'                            => 'Select All',    // 3.3.0+
        'select_none'                           => 'Select None',   // 3.3.0+
        'no_term_found'                         => 'No term found.', // 3.3.2+

        // AdminPageFramework_Form_View___Script_{...}
        'select'                                => 'Select', // 3.4.2+
        'insert'                                => 'Insert',  // 3.4.2+
        'use_this'                              => 'Use This', // 3.4.2+
        'return_to_library'                     => 'Return to Library', // 3.4.2+

        // AdminPageFramework_PageLoadInfo_Base
        'queries_in_seconds'                    => '%1$s queries in %2$s seconds.',
        'out_of_x_memory_used'                  => '%1$s out of %2$s MB (%3$s) memory used.',
        'peak_memory_usage'                     => 'Peak memory usage %1$s MB.',
        'initial_memory_usage'                  => 'Initial memory usage  %1$s MB.',

        // Repeatable sections & fields
        'repeatable_section_is_disabled'        => 'The ability to repeat sections is disabled.', // 3.8.13+
        'repeatable_field_is_disabled'          => 'The ability to repeat fields is disabled.',   // 3.8.13+
        'warning_caption'                       => 'Warning',   // 3.8.13+

        // AdminPageFramework_FormField
        'allowed_maximum_number_of_fields'      => 'The allowed maximum number of fields is {0}.',
        'allowed_minimum_number_of_fields'      => 'The allowed minimum number of fields is {0}.',
        'add'                                   => 'Add',
        'remove'                                => 'Remove',

        // AdminPageFramework_FormPart_Table
        'allowed_maximum_number_of_sections'    => 'The allowed maximum number of sections is {0}',
        'allowed_minimum_number_of_sections'    => 'The allowed minimum number of sections is {0}',
        'add_section'                           => 'Add Section',
        'remove_section'                        => 'Remove Section',
        'toggle_all'                            => 'Toggle All',
        'toggle_all_collapsible_sections'       => 'Toggle all collapsible sections',

        // AdminPageFramework_FieldType_reset 3.3.0+
        'reset'                                 => 'Reset',

        // AdminPageFramework_FieldType_system 3.5.3+
        'yes'                                   => 'Yes',
        'no'                                    => 'No',
        'on'                                    => 'On',
        'off'                                   => 'Off',
        'enabled'                               => 'Enabled',
        'disabled'                              => 'Disabled',
        'supported'                             => 'Supported',
        'not_supported'                         => 'Not Supported',
        'functional'                            => 'Functional',
        'not_functional'                        => 'Not Functional',
        'too_long'                              => 'Too Long',
        'acceptable'                            => 'Acceptable',
        'no_log_found'                          => 'No log found.',

        // 3.7.0+ - accessed from `AdminPageFramework_Form`
        'method_called_too_early'               => 'The method is called too early.',

        // 3.7.0+  - accessed from `AdminPageFramework_Form_View___DebugInfo`
        'debug_info'                            => 'Debug Info',
        // 3.8.5+
        'debug'                                 => 'Debug',
        'field_arguments'                       => 'Field Arguments',
        'debug_info_will_be_disabled'           => 'This information will be disabled when <code>WP_DEBUG</code> is set to <code>false</code> in <code>wp-config.php</code>.',

        'section_arguments'                     => 'Section Arguments', // 3.8.8+

        'click_to_expand'                       => 'Click here to expand to view the contents.',
        'click_to_collapse'                     => 'Click here to collapse the contents.',

        // 3.7.0+ - displayed while the page laods
        'loading'                               => 'Loading...',
        'please_enable_javascript'              => 'Please enable JavaScript for better user experience.',


    );

    /**
     * Stores the text domain.
     * @since       3.x
     * @since       3.5.0       Declared as a default property.
     */
    protected $_sTextDomain = 'admin-page-framework';

    /**
     * Stores the self instance by text domain.
     * @internal
     * @since       3.2.0
     */
    static private $_aInstancesByTextDomain = array();

    /**
     * Ensures that only one instance of this class object exists. ( no multiple instances of this object )
     *
     * @since       2.1.6
     * @since       3.2.0       Changed it to create an instance per text domain basis.
     * @remark      This class should be instantiated via this method.
     */
    public static function getInstance( $sTextDomain='admin-page-framework' ) {

        $_oInstance = isset( self::$_aInstancesByTextDomain[ $sTextDomain ] ) && ( self::$_aInstancesByTextDomain[ $sTextDomain ] instanceof AdminPageFramework_Message )
            ? self::$_aInstancesByTextDomain[ $sTextDomain ]
            : new AdminPageFramework_Message( $sTextDomain );
        self::$_aInstancesByTextDomain[ $sTextDomain ] = $_oInstance;
        return self::$_aInstancesByTextDomain[ $sTextDomain ];

    }
        /**
         * Ensures that only one instance of this class object exists. ( no multiple instances of this object )
         * @deprecated  3.2.0
         */
        public static function instantiate( $sTextDomain='admin-page-framework' ) {
            return self::getInstance( $sTextDomain );
        }

    /**
     * Sets up properties.
     */
    public function __construct( $sTextDomain='admin-page-framework' ) {

        $this->_sTextDomain = $sTextDomain;

        // Fill the $aMessages property with the keys extracted from the $aDefaults property
        // with the value of null.  The null is set to let it trigger the __get() method
        // so that each translation item gets processed individually.
        $this->aMessages    = array_fill_keys(
            array_keys( $this->aDefaults ),
            null
        );

    }

    /**
     * Returns the set text domain string.
     *
     * This is used from field type and input classes to display deprecated admin errors/
     *
     * @since       3.3.3
     */
    public function getTextDomain() {
        return $this->_sTextDomain;
    }

    /**
     * Sets a message for the given key.
     * @since       3.7.0
     */
    public function set( $sKey, $sValue ) {
        $this->aMessages[ $sKey ] = $sValue;
    }

    /**
     * Returns the framework system message by key.
     *
     * @remark      An alias of the __() method.
     * @since       3.2.0
     * @since       3.7.0      If no key is specified, return the entire mesage array.
     * @return      string|array
     */
    public function get( $sKey='' ) {
        if ( ! $sKey ) {
            return $this->_getAllMessages();
        }
        return isset( $this->aMessages[ $sKey ] )
            ? __( $this->aMessages[ $sKey ], $this->_sTextDomain )
            : __( $this->{$sKey}, $this->_sTextDomain );     // triggers __get()
    }
        /**
         * Returns the all registered messag items.
         * By default, no item is set for a performance reason; the message is retuned on the fly.
         * So all the keys must be iterated to get all the values.
         * @since       3.7.0
         * @return      array
         */
        private function _getAllMessages() {
            $_aMessages = array();
            foreach ( $this->aMessages as $_sLabel => $_sTranslation ) {
                $_aMessages[ $_sLabel ] = $this->get( $_sLabel );
            }
            return $_aMessages;
        }

    /**
     * Echoes the framework system message by key.
     * @remark  An alias of the _e() method.
     * @since   3.2.0
     */
    public function output( $sKey ) {
        echo $this->get( $sKey );
    }

        /**
         * Returns the framework system message by key.
         * @since       2.x
         * @deprecated  3.2.0
         */
        public function __( $sKey ) {
            return $this->get( $sKey );
        }
        /**
         * Echoes the framework system message by key.
         * @since       2.x
         * @deprecated  3.2.0
         */
        public function _e( $sKey ) {
            $this->output( $sKey );
        }

    /**
     * Responds to a request to an undefined property.
     *
     * @since       3.1.3
     * @return      string
     */
    public function __get( $sPropertyName ) {
        return isset( $this->aDefaults[ $sPropertyName ] ) ? $this->aDefaults[ $sPropertyName ] : $sPropertyName;
    }


    /**
     * A dummy method just lists translation items to be parsed by translation programs such as POEdit.
     *
     * @since       3.5.3
     * @since       3.8.19  Changed the name to avoid false-positives of PHP 7.2 incompatibility by third party tools.
     */
    private function ___doDummy() {

        __( 'The options have been updated.', 'admin-page-framework' );
        __( 'The options have been cleared.', 'admin-page-framework' );
        __( 'Export', 'admin-page-framework' );
        __( 'Export Options', 'admin-page-framework' );
        __( 'Import', 'admin-page-framework' );
        __( 'Import Options', 'admin-page-framework' );
        __( 'Submit', 'admin-page-framework' );
        __( 'An error occurred while uploading the import file.', 'admin-page-framework' );
        __( 'The uploaded file type is not supported: %1$s', 'admin-page-framework' );
        __( 'Could not load the importing data.', 'admin-page-framework' );
        __( 'The uploaded file has been imported.', 'admin-page-framework' );
        __( 'No data could be imported.', 'admin-page-framework' );
        __( 'Upload Image', 'admin-page-framework' );
        __( 'Use This Image', 'admin-page-framework' );
        __( 'Insert from URL', 'admin-page-framework' );
        __( 'Are you sure you want to reset the options?', 'admin-page-framework' );
        __( 'Please confirm your action.', 'admin-page-framework' );
        __( 'The specified options have been deleted.', 'admin-page-framework' );
        __( 'A problem occurred while processing the form data. Please try again.', 'admin-page-framework' );
        __( 'Not all form fields could not be sent. Please check your server settings of PHP <code>max_input_vars</code> and consult the server administrator to increase the value. <code>max input vars</code>: %1$s. <code>$_POST</code> count: %2$s', 'admin-page-framework' );
        __( 'Is it okay to send the email?', 'admin-page-framework' );
        __( 'The email has been sent.', 'admin-page-framework' );
        __( 'The email has been scheduled.', 'admin-page-framework' );
        __( 'There was a problem sending the email', 'admin-page-framework' );
        __( 'Title', 'admin-page-framework' );
        __( 'Author', 'admin-page-framework' );
        __( 'Categories', 'admin-page-framework' );
        __( 'Tags', 'admin-page-framework' );
        __( 'Comments', 'admin-page-framework' );
        __( 'Date', 'admin-page-framework' );
        __( 'Show All', 'admin-page-framework' );
        __( 'Show All Authors', 'admin-page-framework' );
        __( 'Thank you for creating with', 'admin-page-framework' );
        __( 'and', 'admin-page-framework' );
        __( 'Settings', 'admin-page-framework' );
        __( 'Manage', 'admin-page-framework' );
        __( 'Select Image', 'admin-page-framework' );
        __( 'Upload File', 'admin-page-framework' );
        __( 'Use This File', 'admin-page-framework' );
        __( 'Select File', 'admin-page-framework' );
        __( 'Remove Value', 'admin-page-framework' );
        __( 'Select All', 'admin-page-framework' );
        __( 'Select None', 'admin-page-framework' );
        __( 'No term found.', 'admin-page-framework' );
        __( 'Select', 'admin-page-framework' );
        __( 'Insert', 'admin-page-framework' );
        __( 'Use This', 'admin-page-framework' );
        __( 'Return to Library', 'admin-page-framework' );
        __( '%1$s queries in %2$s seconds.', 'admin-page-framework' );
        __( '%1$s out of %2$s MB (%3$s) memory used.', 'admin-page-framework' );
        __( 'Peak memory usage %1$s MB.', 'admin-page-framework' );
        __( 'Initial memory usage  %1$s MB.', 'admin-page-framework' );
        __( 'The allowed maximum number of fields is {0}.', 'admin-page-framework' );
        __( 'The allowed minimum number of fields is {0}.', 'admin-page-framework' );
        __( 'Add', 'admin-page-framework' );
        __( 'Remove', 'admin-page-framework' );
        __( 'The allowed maximum number of sections is {0}', 'admin-page-framework' );
        __( 'The allowed minimum number of sections is {0}', 'admin-page-framework' );
        __( 'Add Section', 'admin-page-framework' );
        __( 'Remove Section', 'admin-page-framework' );
        __( 'Toggle All', 'admin-page-framework' );
        __( 'Toggle all collapsible sections', 'admin-page-framework' );
        __( 'Reset', 'admin-page-framework' );
        __( 'Yes', 'admin-page-framework' );
        __( 'No', 'admin-page-framework' );
        __( 'On', 'admin-page-framework' );
        __( 'Off', 'admin-page-framework' );
        __( 'Enabled', 'admin-page-framework' );
        __( 'Disabled', 'admin-page-framework' );
        __( 'Supported', 'admin-page-framework' );
        __( 'Not Supported', 'admin-page-framework' );
        __( 'Functional', 'admin-page-framework' );
        __( 'Not Functional', 'admin-page-framework' );
        __( 'Too Long', 'admin-page-framework' );
        __( 'Acceptable', 'admin-page-framework' );
        __( 'No log found.', 'admin-page-framework' );

        __( 'The method is called too early: %1$s', 'admin-page-framework' );
        __( 'Debug Info', 'admin-page-framework' );

        __( 'Click here to expand to view the contents.', 'admin-page-framework' );
        __( 'Click here to collapse the contents.', 'admin-page-framework' );

        __( 'Loading...', 'admin-page-framework' );
        __( 'Please enable JavaScript for better user experience.', 'admin-page-framework' );

        __( 'Debug', 'admin-page-framework' );
        __( 'Field Arguments', 'admin-page-framework' );
        __( 'This information will be disabled when <code>WP_DEBUG</code> is set to <code>false</code> in <code>wp-config.php</code>.', 'admin-page-framework' );

        __( 'Section Arguments', 'admin-page-framework' ); // 3.8.8+

        __( 'The ability to repeat sections is disabled.', 'admin-page-framework' ); // 3.8.13+
        __( 'The ability to repeat fields is disabled.', 'admin-page-framework' ); // 3.8.13+
        __( 'Warning.', 'admin-page-framework' ); // 3.8.13+

    }

}
