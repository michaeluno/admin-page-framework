<?php
/**
 * Admin Page Framework Loader
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
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
            'class_name'           => '',   // the source class name to be prefixed with the user specified one.
            'directory_path'       => '',
            'label'                => '',
            'description'          => '',
            'archive_file_path'    => '',
            'archive_dir_path'     => '',
            'text_domain'          => '',   // the source text domain to be converted to the user specified one.
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
                    'text_domain'          => 'admin-page-framework',
                ),
                'GitHubCustomFieldType'   =>  array(
                    'class_name'           => 'GitHubCustomFieldType',
                    'label'                => __( 'GitHub Buttons', 'admin-page-framework-loader' ),
                    'description'          => __( 'allows you to display GitHub buttons in a field.', 'admin-page-framework-loader' ),
                    'directory_path'       => AdminPageFrameworkLoader_Registry::$sDirPath . '/include/library/github-custom-field-type',
                    'archive_file_path'    => 'custom-field-types/github-custom-field-type/GitHubCustomFieldType.php',
                    'archive_dir_path'     => 'custom-field-types/github-custom-field-type',
                    'text_domain'          => 'admin-page-framework',
                ),
                'PathCustomFieldType'   =>  array(
                    'class_name'           => 'PathCustomFieldType',
                    'label'                => __( 'Path', 'admin-page-framework-loader' ),
                    'description'          => __( 'allows the user to select a file path on the server.', 'admin-page-framework-loader' ),
                    'directory_path'       => AdminPageFrameworkLoader_Registry::$sDirPath . '/example/library/path-custom-field-type',
                    'archive_file_path'    => 'custom-field-types/path-custom-field-type/PathCustomFieldType.php',
                    'archive_dir_path'     => 'custom-field-types/path-custom-field-type',
                    'text_domain'          => 'admin-page-framework',
                ),
                'ToggleCustomFieldType'   =>  array(
                    'class_name'           => 'ToggleCustomFieldType',
                    'label'                => __( 'Toggle', 'admin-page-framework-loader' ),
                    'description'          => __( 'allows the user to switch a button.', 'admin-page-framework-loader' ),
                    'directory_path'       => AdminPageFrameworkLoader_Registry::$sDirPath . '/example/library/toggle-custom-field-type',
                    'archive_file_path'    => 'custom-field-types/toggle-custom-field-type/ToggleCustomFieldType.php',
                    'archive_dir_path'     => 'custom-field-types/toggle-custom-field-type',
                    'text_domain'          => 'admin-page-framework',
                ),
                'NoUISliderCustomFieldType'   =>  array(
                    'class_name'           => 'NoUISliderCustomFieldType',
                    'label'                => __( 'NoUISlider (Range Slider)', 'admin-page-framework-loader' ),
                    'description'          => __( 'allows the user to set values in ranges.', 'admin-page-framework-loader' ),
                    'directory_path'       => AdminPageFrameworkLoader_Registry::$sDirPath . '/example/library/nouislider-custom-field-type',
                    'archive_file_path'    => 'custom-field-types/nouislider-custom-field-type/NoUISliderCustomFieldType.php',
                    'archive_dir_path'     => 'custom-field-types/nouislider-custom-field-type',
                    'text_domain'          => 'admin-page-framework',
                ),
                'Select2CustomFieldType'   =>  array(
                    'class_name'           => 'Select2CustomFieldType',
                    'label'                => __( 'Select2', 'admin-page-framework-loader' ),
                    'description'          => __( 'allows the user to select items with autocomplete from a list which can be populated with AJAX.', 'admin-page-framework-loader' ),
                    'directory_path'       => AdminPageFrameworkLoader_Registry::$sDirPath . '/example/library/select2-custom-field-type',
                    'archive_file_path'    => 'custom-field-types/select2-custom-field-type/Select2CustomFieldType.php',
                    'archive_dir_path'     => 'custom-field-types/select2-custom-field-type',
                    'text_domain'          => 'admin-page-framework',
                ),
                'PostTypeTaxonomyCustomFieldType'   =>  array(
                    'class_name'           => 'PostTypeTaxonomyCustomFieldType',
                    'label'                => __( 'Post Type Taxonomy', 'admin-page-framework-loader' ),
                    'description'          => __( 'allows the user to select taxonomy terms of selected post types.', 'admin-page-framework-loader' ),
                    'directory_path'       => AdminPageFrameworkLoader_Registry::$sDirPath . '/example/library/post_type_taxonomy_field-type',
                    'archive_file_path'    => 'custom-field-types/post_type_taxonomy_field-type/PostTypeTaxonomyCustomFieldType.php',
                    'archive_dir_path'     => 'custom-field-types/post_type_taxonomy_field-type',
                    'text_domain'          => 'admin-page-framework',
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
             * @return      string
             * @callback    filter      admin_page_framework_loader_filter_generator_file_contents
             */
            public function replyToModifyFileContents( $sFileContents, $sPathInArchive, $aFormData, $oFactory ) {

                // Check the file extension.
                $_aAllowedExtensions = apply_filters(
                    AdminPageFrameworkLoader_Registry::HOOK_SLUG . '_filter_generator_allowed_file_extensions',
                    array( 'php', 'css', 'js' )
                );
                if ( ! in_array( pathinfo( $sPathInArchive, PATHINFO_EXTENSION ), $_aAllowedExtensions ) ) {
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

                $_bsParsingClassName = $this->_getClassNameIfSelected( $sPathInArchive );
                if ( $_bsParsingClassName ) {
                    return $this->_getModifiedFileContents( $sFileContents, $sPathInArchive, $_bsParsingClassName );
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
                                'generator',    // section id
                                'class_prefix'  // field id
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
                 *
                 * Checks if the user select the field type in the Generator form.
                 *
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
                 * Modify the file contents of the given path.
                 *
                 * Converts the class name by adding the user-set class name prefix.
                 * Also the text domain used in the custom field type will be converted.
                 *
                 * @remark      The reason why it retrieve all the selected custom field type classes, not the parsing item is
                 * because some custom field types extend another custom field type. In that case, a name is used among multiple files.
                 * @since       3.6.0
                 * @since       3.7.2      Added the `$sParsingClassName` argument.
                 * @return      string
                 */
                private function _getModifiedFileContents( $sFileContents, $sPathInArchive, $sParsingClassName ) {

                    // 3.8.4+ Some custom field types names conflict each other such as `TuneCustomFieldType` and `DateTimeCustomFieldType` so they must be dealt with regular expressions.
                    $sFileContents = $this->_getClassNamesPrefixed( $sFileContents );

                    // Searches and replaces for `str_replace()`.
                    $_aSearches = array();
                    $_aReplaces = array();

                    $_sUserTextDomain = $this->oFactory->oUtil->getElement(
                        $_POST,
                        array(
                            $this->oFactory->oProp->sOptionKey,
                            'generator', // section id
                            'text_domain' // field id
                        ),
                        ''
                    );

                    // Change the text domain.

                    /// 3.7.2+ Get the custom field type text domain.
                    $_sFieldTypeTextDomain = $this->oFactory->oUtil->getElement(
                        $this->aCustomFieldTypes,
                        array( $sParsingClassName, 'text_domain' )
                    );
                    if ( $_sFieldTypeTextDomain ) {
                        $_aSearches[] = $_sFieldTypeTextDomain;
                        $_aReplaces[] = $_sUserTextDomain;
                    }

                    $_aSearches[] = 'admin-page-framework';
                    $_aReplaces[] = $_sUserTextDomain;

                    // Return the converted string.
                    return str_replace(
                        $_aSearches,    // search
                        $_aReplaces,    // replace
                        $sFileContents  // subject
                    );

                }

                    /**
                     * Modifies the given content by replacing the class names with a prefix.
                     * @since       3.8.4
                     * @return      string
                     */
                    private function _getClassNamesPrefixed( $sFileContents ) {

                        $_aSelectedFieldTypeClassNames = array_keys(
                            $this->_getSelectedCustomFieldTypes( $this->aCustomFieldTypes )
                        );

                        return preg_replace(
                            $this->_getClassPrefixRegexPatterns( $_aSelectedFieldTypeClassNames ),    // search
                            $this->_getClassPrefixRegexReplacements( $_aSelectedFieldTypeClassNames ),    // replace
                            $sFileContents  // subject
                        );

                    }
                        /**
                         * Returns an array holding regular expressions needle patterns for class names.
                         * @since       3.8.4
                         * @return      array
                         */
                        private function _getClassPrefixRegexPatterns( array $aSelectedFieldTypeClassNames ) {

                            $_aPregSearches = array();
                            foreach( $aSelectedFieldTypeClassNames as $_sClassName ) {
                                $_aPregSearches[] = '/(?<=[^a-zA-Z0-9])(' . $_sClassName . ')/';
                            }
                            return $_aPregSearches;

                        }
                        /**
                         * Returns an array holding regular expressions replacements for class names.
                         * @since       3.8.4
                         * @return      array
                         */
                        private function _getClassPrefixRegexReplacements( array $aSelectedFieldTypeClassNames ) {

                            $_aPrefixedClassNames          = $aSelectedFieldTypeClassNames;
                            array_walk(
                                $_aPrefixedClassNames,  // passed by reference
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
                            return $_aPrefixedClassNames;

                        }
                            /**
                             * @since       3.6.0
                             * @since       3.8.4       Changed it for regular expression patterns.
                             * @callback    function    array_walk
                             * @return      string
                             */
                            public function _replyToSetPrefix( &$sClassName, $sKey, $sPrefix='' ) {
                                $sClassName = $sPrefix . '$0';
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
