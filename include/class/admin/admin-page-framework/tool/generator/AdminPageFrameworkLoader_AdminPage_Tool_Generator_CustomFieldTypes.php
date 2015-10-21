<?php
/**
 * Admin Page Framework Loader
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 */

/**
 * Adds the 'custom_field_types' filed to the 'Generator' section.
 * 
 * @since       3.6.0
 * @filter      apply       admin_page_framework_loader_filter_generator_custom_field_types
 */
class AdminPageFrameworkLoader_AdminPage_Tool_Generator_CustomFieldTypes {
    
    /**
     * Stores the admin page factory object.
     */
    public $oFactory;    
    
    public $aCustomFieldTypes = array(
        /*         
        '__key_structure'   =>  array(
            'directory_path'       => '',
            'label'                => '',
            'description'          => '',
            'archive_file_path'    => '',
            'archive_dir_path'     => '',
        ), */
    );
    
    public $aCustomFieldTypeLabels = array();

    
    /**
     * Sets up hooks and properties.
     */
    public function __construct( $oFactory, $sSectionID ) {
        
        // Properties
        $this->oFactory = $oFactory;
        $this->_setProperties();
        
        // Form fields - the field type pack extension also uses this field.
        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array( 
                'field_id'              => 'custom_field_types',
                'title'                 => __( 'Custom Field Types', 'admin-page-framework-loader' ),
                'type'                  => 'checkbox',
                'order'                 => 13,
                'label'                 => $this->aCustomFieldTypeLabels,
                'label_min_width'       => '100%',
                'select_all_button'     => true,        
                'select_none_button'    => true,        
            )
        );    
      
        /// Hooks        
        // Register custom field files to the Generator of the framework loader.
        add_filter( 
            'admin_page_framework_loader_filter_generator_additional_source_directories',
            array( $this, 'replyToSetAdditionalDirectoriesForGenerator' )
        );        
         
        // Register a callback to modify archive files. 
        add_filter(
            'admin_page_framework_loader_filter_generator_file_contents',
            array( $this, 'replyToModifyFileContents' ),
            10,
            4
        );        
        
    }
    
        /**
         * Sets up properties.
         * @since       3.6.0
         * @return      void
         */
        private function _setProperties() {

            $this->aCustomFieldTypes = array(
                'AceCustomFieldType'   =>  array(
                    'class_name'           => 'AceCustomFieldType',
                    'label'                => __( 'ACE', 'admin-page-framework-loader' ),
                    'description'          => __( 'provides code syntax highlighting in a text area field.', 'admin-page-framework-loader' ),
                    'directory_path'       => AdminPageFrameworkLoader_Registry::$sDirPath . '/example/library/ace-custom-field-type',
                    'archive_file_path'    => 'custom-field-types/ace-custom-field-type/AceCustomFieldType.php',
                    'archive_dir_path'     => 'custom-field-types/ace-custom-field-type',
                ),
                'GitHubCustomFieldType'   =>  array(
                    'class_name'           => 'GitHubCustomFieldType',
                    'label'                => __( 'GitHub Buttons', 'admin-page-framework-loader' ),
                    'description'          => __( 'allows you to display GitHub buttons in a field.', 'admin-page-framework-loader' ),
                    'directory_path'       => AdminPageFrameworkLoader_Registry::$sDirPath . '/include/library/github-custom-field-type',
                    'archive_file_path'    => 'custom-field-types/github-custom-field-type/GitHubCustomFieldType.php',
                    'archive_dir_path'     => 'custom-field-types/github-custom-field-type',
                ),                
            );
                
            // Let third-party scripts add custom field types.
            $this->aCustomFieldTypes = apply_filters(
                AdminPageFrameworkLoader_Registry::HOOK_SLUG . '_filter_generator_custom_field_types',
                $this->aCustomFieldTypes
            );                    
            
            foreach( $this->aCustomFieldTypes as $_sKey => $_aCustomFieldType ) {
                $this->aCustomFieldTypeLabels[ $_sKey ] = $_aCustomFieldType[ 'label' ]
                    . ' - <span class="description">' . $_aCustomFieldType[ 'description' ] . '</span>';
            }
     
        }
        
            /**
             * Modifies the file contents.
             * 
             * @since       3.6.0
             */
            public function replyToModifyFileContents( $sFileContents, $sPathInArchive, $aFormData, $oFactory ) {

                // Check the file extension.
                if ( ! in_array( pathinfo( $sPathInArchive, PATHINFO_EXTENSION ), array( 'php', 'css', 'js' ) ) ) {
                    return $sFileContents;
                }            
                
                // Skip the framework bootstrap file.
                if ( $this->oFactory->oUtil->hasSuffix( 'admin-page-framework.php', $sPathInArchive ) ) {
                    return $sFileContents;
                }

                // The inclusion class list file needs to be handled differently.
                if ( $this->oFactory->oUtil->hasSuffix( 'admin-page-framework-include-class-list.php', $sPathInArchive ) ) {
                    return $this->_getModifiedInclusionList( $sFileContents );
                }
                
                $_sParsingClassName = $this->_getClassNameIfSelected( $sPathInArchive );
                if ( $_sParsingClassName ) {
                    return $this->_getModifiedFileContents( $sFileContents, $sPathInArchive );
                }
                
                return $sFileContents;
                
            }
                /**
                 * Modifies the class include list.
                 * @since       3.6.0
                 * @return      string
                 */
                private function _getModifiedInclusionList( $sFileContents ) {
                    return str_replace(
                        ');', // search
                        $this->_getClassListOfCustomFieldTypes() . ');', // replace - @todo insert the selected class list here
                        $sFileContents // subject
                    );
                }
                    /**
                     * @since       3.6.0
                     * @return      string
                     */
                    private function _getClassListOfCustomFieldTypes() {
                        
                        $_aCheckedCustomFieldTypes = $this->_getSelectedCustomFieldTypes(
                            $this->aCustomFieldTypes
                        );
                        $_sClassPrefix = $this->oFactory->oUtil->getElement(
                            $_POST,
                            array( 
                                $this->oFactory->oProp->sOptionKey, 
                                'generator', // section id
                                'class_prefix' // field id
                            ),
                            ''
                        );
                        $_aOutput = array();
                        foreach( $_aCheckedCustomFieldTypes as $_sClassName => $_aCustomFieldType ) {
                            $_aOutput[] = '    "' . $_sClassPrefix . $_sClassName . '"'
                                . ' => ' 
                                . 'AdminPageFramework_Registry::$sDirPath . ' . '"/' . ltrim( $_aCustomFieldType[ 'archive_file_path' ], '/' ) . '",';
                        }
                        return implode( PHP_EOL, $_aOutput ) . PHP_EOL;
                    
                    }

                /**
                 * Retrieves the custom field type class name from the given archive path.
                 * @since       3.6.0
                 * @return      string|boolean      The found class name; false, otherwise.
                 */
                private function _getClassNameIfSelected( $sPathInArchive ) {
                    
                    $_aSelectedCustomFieldTypes = $this->_getSelectedCustomFieldTypes(
                        $this->aCustomFieldTypes // ArchiveFilePaths
                    );
                    $_aArchiveFilePaths = array();
                    foreach( $_aSelectedCustomFieldTypes as $_sClassName => $_aCustomFieldType ) {
                        $_aArchiveFilePaths[ $_sClassName ] = $this->oFactory->oUtil->getElement(
                            $_aCustomFieldType,
                            'archive_file_path',
                            ''
                        );
                    }
                    return array_search( 
                        ltrim( $sPathInArchive, '/' ),
                        $_aArchiveFilePaths
                    );                    
                }                    
            
                /**
                 * Modify the class name by adding the user-set class name prefix. 
                 * @since       3.6.0
                 * @return      string
                 */
                private function _getModifiedFileContents( $sFileContents, $sPathInArchive ) {
                    
                    // Add the class prefix to each element.
                    $_aSelectedFieldTypeClassNames = array_keys( 
                        $this->_getSelectedCustomFieldTypes( $this->aCustomFieldTypes ) 
                    );
                    $_aPrefixedClassNames          = $_aSelectedFieldTypeClassNames;
                    array_walk(
                        $_aPrefixedClassNames, 
                        array( $this, '_replyToSetPrefix' ),
                        $this->oFactory->oUtil->getElement(
                            $_POST,
                            array( 
                                $this->oFactory->oProp->sOptionKey, 
                                'generator', // section id
                                'class_prefix' // field id
                            ),
                            ''
                        )
                    );
                    
                    // Search and replace.
                    $_aSearches = $_aSelectedFieldTypeClassNames;
                    $_aReplaces = $_aPrefixedClassNames;                    
                    
                    // Change the text domain.
                    $_aSearches[] = 'admin-page-framework';
                    $_aReplaces[] = $this->oFactory->oUtil->getElement(
                        $_POST,
                        array( 
                            $this->oFactory->oProp->sOptionKey, 
                            'generator', // section id
                            'text_domain' // field id
                        ),
                        ''
                    );
                    
                    // Return the changed string.
                    return str_replace(
                        $_aSearches, // search
                        $_aReplaces, // replace
                        $sFileContents // subject
                    );
                    
                }
                    /**
                     * @since       3.6.0
                     * @callback    function        array_walk
                     */
                    public function _replyToSetPrefix( &$sClassName, $sKey, $sPrefix='' ) {
                        $sClassName = $sPrefix . $sClassName;
                    }   
                                    
 
            /**
             * Inserts extra archive directories of custom field types chosen by the user.
             * 
             * Structure: 
             * Archive directory path => Source directory path
             * `
             * array(
             *  'custom-field-types/my-custom-field-type' =>  AdminPageFrameworkLoader_Registry::$sDirPath . '/include/library/my-custom-field-type',
             * )
             * `
             * 
             * @since       3.6.0
             * @return      array
             * @callback    filter      admin_page_framework_loader_filter_generator_additional_source_directories
             */
            public function replyToSetAdditionalDirectoriesForGenerator( $aDirPaths ) {
                                
                $_aCheckedCustomFieldTypes        = $this->_getSelectedCustomFieldTypes(
                    $this->aCustomFieldTypes
                );
                
                $_aDirPathInfo = array();
                foreach( $_aCheckedCustomFieldTypes as $_sKey => $_aCheckedCustomFieldType ) {
                    
                    $_sArchiveDirPath = $this->oFactory->oUtil->getElement( $_aCheckedCustomFieldType, 'archive_dir_path' );
                    $_sSourceDirPath  = $this->oFactory->oUtil->getElement( $_aCheckedCustomFieldType, 'directory_path' );
                    $_aDirPathInfo[ $_sArchiveDirPath ] = $_sSourceDirPath;
                    
                }
                
                return $aDirPaths + $_aDirPathInfo;

            }
            
                /**
                 * @return      array       The array keys of the checked items.
                 * @since       3.6.0
                 */
                private function _getSelectedCustomFieldTypes( array $aSubject=array() ) {
                    
                    $_aCheckedCustomFieldTypes = $this->oFactory->oUtil->getElementAsArray(
                        $_POST,
                        array( 
                            $this->oFactory->oProp->sOptionKey, 
                            'generator', // section id
                            'custom_field_types' // field id
                        ),
                        array()
                    );                
                    $_aCheckedCustomFieldTypes = array_intersect_key(
                        $aSubject, 
                        array_filter( $_aCheckedCustomFieldTypes ) // drop 0 values
                    );        
                    return $_aCheckedCustomFieldTypes;
                    
                } 
 
}