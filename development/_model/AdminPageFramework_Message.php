<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Message' ) ) :
/**
 * Provides methods for text messages.
 *
 * @since       2.0.0
 * @since       2.1.6   Multiple instances of this class are disallowed.
 * @since       3.2.0   Multiple instances of this class are allowed by the instantiation is restricted to per text domain basis.
 * @extends     n/a
 * @package     AdminPageFramework
 * @subpackage  Property
 * @internal
 */
class AdminPageFramework_Message {

    /**
     * Stores the framework's messages.
     * 
     * @remark     The user may modify this property directly.
     */ 
    public $aMessages = array();
    
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
        
        $_oInstance = isset( $_aInstancesByTextDomain[ $sTextDomain ] ) && ( $_aInstancesByTextDomain[ $sTextDomain ] instanceof AdminPageFramework_Message )
            ? $_aInstancesByTextDomain[ $sTextDomain ]
            : new AdminPageFramework_Message( $sTextDomain );
        $_aInstancesByTextDomain[ $sTextDomain ] = $_oInstance;
        return $_aInstancesByTextDomain[ $sTextDomain ];
        
    }    
        /**
         * Ensures that only one instance of this class object exists. ( no multiple instances of this object ) 
         * @deprecated  3.2.0
         */
        public static function instantiate( $sTextDomain='admin-page-framework' ) {
            return self::getInstantiate( $sTextDomain );
        }
        
    public function __construct( $sTextDomain='admin-page-framework' ) {
        
        $this->_sTextDomain = $sTextDomain;
        
        // As of v3.1.3, no item is defined by default. The below array structure is kept for backward compatibility.
        $this->aMessages = array(    
    
            // AdminPageFramework
            'option_updated'                        => null,
            'option_cleared'                        => null,
            'export'                                => null,
            'export_options'                        => null,
            'import_options'                        => null,
            'import_options'                        => null,
            'submit'                                => null,
            'import_error'                          => null,
            'uploaded_file_type_not_supported'      => null,
            'could_not_load_importing_data'         => null,
            'imported_data'                         => null,
            'not_imported_data'                     => null,
            'upload_image'                          => null,
            'use_this_image'                        => null,
            'reset_options'                         => null,
            'confirm_perform_task'                  => null,
            'specified_option_been_deleted'         => null,
            'nonce_verification_failed'             => null,
            
            // AdminPageFramework_PostType
            'title'                                 => null,
            'author'                                => null,
            'categories'                            => null,
            'tags'                                  => null,
            'comments'                              => null,
            'date'                                  => null,
            'show_all'                              => null,

            // AdminPageFramework_Link_Base
            'powered_by'                            => null,
            
            // AdminPageFramework_Link_Page
            'settings'                              => null,
            
            // AdminPageFramework_Link_PostType
            'manage'                                => null,
            
            // AdminPageFramework_FieldType_Base
            'select_image'                          => null,
            'upload_file'                           => null,
            'use_this_file'                         => null,
            'select_file'                           => null,

            // AdminPageFramework_PageLoadInfo_Base
            'queries_in_seconds'                    => null,
            'out_of_x_memory_used'                  => null,
            'peak_memory_usage'                     => null,
            'initial_memory_usage'                  => null,

            // AdminPageFramework_FormField
            'allowed_maximum_number_of_fields'      => null,
            'allowed_minimum_number_of_fields'      => null,
            'add' => null,
            'remove' => null,
            
            // AdminPageFramework_FormTable
            'allowed_maximum_number_of_sections'    => null,
            'allowed_minimum_number_of_sections'    => null,
            'add_section'                           => null,
            'remove_section'                        => null,
            
        );     
        
    }
    
    /**
     * Returns the framework system message by key.
     * 
     * @remark  An alias of the __() method.
     * @since   3.2.0
     */
    public function get( $sKey ) {
        
        return isset( $this->aMessages[ $sKey ] )
            ? __( $this->aMessages[ $sKey ], $this->_sTextDomain )
            : __( $this->{$sKey}, $this->_sTextDomain );        
        
    }

    /**
     * Echoes the framework system message by key.
     * @remark  An alias of the _e() method.
     * @since   3.2.0
     */    
    public function output( $sKey ) {
        
        if ( isset( $this->aMessages[ $sKey ] ) ) {
            _e( $this->aMessages[ $sKey ], $this->_sTextDomain );
        } else {
            _e( $this->{$sKey}, $this->_sTextDomain );
        }
        
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
     * @since 3.1.3
     */
    public function __get( $sPropertyName ) {
    
        return $this->_getTranslation( $sPropertyName );
        
    }
        /**
         * Returns the translated text label from the given label key.
         * 
         * @since 3.1.3
         */
        private function _getTranslation( $_sLabelKey ) {
            
            switch ( $_sLabelKey ) {
                // AdminPageFramework
                case 'option_updated':
                    return __( 'The options have been updated.', 'admin-page-framework' );
                case 'option_cleared':     
                    return __( 'The options have been cleared.', 'admin-page-framework' );
                case 'export':     
                    return __( 'Export', 'admin-page-framework' );
                case 'export_options':
                    return __( 'Export Options', 'admin-page-framework' );
                case 'import_options':
                    return __( 'Import', 'admin-page-framework' );
                case 'import_options':
                    return __( 'Import Options', 'admin-page-framework' );
                case 'submit':
                    return __( 'Submit', 'admin-page-framework' );
                case 'import_error':
                    return __( 'An error occurred while uploading the import file.', 'admin-page-framework' );
                case 'uploaded_file_type_not_supported':
                    return __( 'The uploaded file type is not supported: %1$s', 'admin-page-framework' );
                case 'could_not_load_importing_data':
                    return __( 'Could not load the importing data.', 'admin-page-framework' );
                case 'imported_data':
                    return __( 'The uploaded file has been imported.', 'admin-page-framework' );
                case 'not_imported_data':
                    return __( 'No data could be imported.', 'admin-page-framework' );
                case 'upload_image':
                    return __( 'Upload Image', 'admin-page-framework' );
                case 'use_this_image':
                    return __( 'Use This Image', 'admin-page-framework' );
                case 'reset_options':
                    return __( 'Are you sure you want to reset the options?', 'admin-page-framework' );
                case 'confirm_perform_task':
                    return __( 'Please confirm if you want to perform the specified task.', 'admin-page-framework' );
                case 'specified_option_been_deleted':
                    return __( 'The specified options have been deleted.', 'admin-page-framework' );
                case 'nonce_verification_failed':
                    return __( 'A problem occurred while processing the form data. Please try again.', 'admin-page-framework' );
                
                // AdminPageFramework_PostType
                case 'title':
                    return __( 'Title', 'admin-page-framework' );    
                case 'author':
                    return __( 'Author', 'admin-page-framework' );    
                case 'categories':
                    return __( 'Categories', 'admin-page-framework' );
                case 'tags':
                    return __( 'Tags', 'admin-page-framework' );
                case 'comments':
                    return __( 'Comments', 'admin-page-framework' );
                case 'date':
                    return __( 'Date', 'admin-page-framework' ); 
                case 'show_all':
                    return __( 'Show All', 'admin-page-framework' );

                // AdminPageFramework_Link_Base
                case 'powered_by':
                    return __( 'Powered by', 'admin-page-framework' );

                // AdminPageFramework_Link_Page
                case 'settings':
                    return __( 'Settings', 'admin-page-framework' );

                // AdminPageFramework_Link_PostType
                case 'manage':
                    return __( 'Manage', 'admin-page-framework' );

                // AdminPageFramework_FieldType_Base
                case 'select_image':
                    return __( 'Select Image', 'admin-page-framework' );
                case 'upload_file':
                    return __( 'Upload File', 'admin-page-framework' );
                case 'use_this_file':
                    return __( 'Use This File', 'admin-page-framework' );
                case 'select_file':
                    return __( 'Select File', 'admin-page-framework' );

                // AdminPageFramework_PageLoadInfo_Base
                case 'queries_in_seconds':
                    return __( '%1$s queries in %2$s seconds.', 'admin-page-framework' );
                case 'out_of_x_memory_used':
                    return __( '%1$s out of %2$s MB (%3$s) memory used.', 'admin-page-framework' );
                case 'peak_memory_usage':
                    return __( 'Peak memory usage %1$s MB.', 'admin-page-framework' );
                case 'initial_memory_usage':
                    return __( 'Initial memory usage  %1$s MB.', 'admin-page-framework' );
        
                // AdminPageFramework_FormField
                case 'allowed_maximum_number_of_fields':
                    return __( 'The allowed maximum number of fields is {0}.', 'admin-page-framework' );
                case 'allowed_minimum_number_of_fields':
                    return __( 'The allowed minimum number of fields is {0}.', 'admin-page-framework' );
                case 'add':
                    return __( 'Add', 'admin-page-framework' );
                case 'remove':
                    return __( 'Remove', 'admin-page-framework' );

                // AdminPageFramework_FormTable
                case 'allowed_maximum_number_of_sections':
                    return __( 'The allowed maximum number of sections is {0}', 'admin-page-framework' );
                case 'allowed_minimum_number_of_sections':
                    return __( 'The allowed minimum number of sections is {0}', 'admin-page-framework' );
                case 'add_section':
                    return __( 'Add Section' );
                case 'remove_section':
                    return __( 'Remove Section' );     
                    
            }
    
        }
    
}
endif;