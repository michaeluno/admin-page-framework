<?php
/**
 * Admin Page Framework Loader
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 */

/**
 * Adds the 'Generator' section to the 'Generator' tab.
 *
 * @since       3.5.4
 */
class AdminPageFrameworkLoader_AdminPage_Tool_Generator_Generator extends AdminPageFrameworkLoader_AdminPage_Section_Base {

    /**
     * Stores the admin page factory object.
     */
    public $oFactory;

    /**
     * A user constructor.
     *
     * @since       3.5.4
     * @return      void
     */
    protected function construct( $oFactory ) {

        // Store the factory object in a property.
        $this->oFactory = $oFactory;

        add_action(
            'export_name_' . $this->sPageSlug . '_' . $this->sTabSlug,
            array( $this, 'replyToFilterFileName' ),
            10,
            5
        );
        add_action(
            // export_{instantiated clasa name}_{section id}_{field id}
            "export_{$oFactory->oProp->sClassName}_{$this->sSectionID}_download",
            array( $this, 'replyToDownloadFramework' ),
            10,
            4
        );
        add_action(
            "export_header_{$oFactory->oProp->sClassName}_{$this->sSectionID}",
            array( $this, 'replyToModifyExportHTTPHeader' ),
            10,
            6
        );

    }

    /**
     * Adds form fields.
     * @since       3.5.4
     * @return      void
     */
    public function addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'          => 'version',
                'title'             => __( 'Version', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'save'              => false,
                'value'             => AdminPageFramework_Registry::VERSION,
                'attributes'        => array(
                    'size'          => 20,
                    'readonly'      => 'readonly',
                ),
            ),
            array(
                'field_id'          => 'class_prefix',
                'title'             => __( 'Class Prefix', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'tip'               => array(
                    __( 'Set alphanumeric characters for the class names.', 'admin-page-framework-loader' ),
                    __( 'For example, if you set here <code>MyPluginName_</code>, you will need to extend the class named <code>MyClassName_AdminPageFramework</code> instead of <code>AdminPageFramework</code>.', 'admin-page-framework-loader' ),
                ),
                'description'       => 'e.g.<code>MyPluginName_</code>',
                'attributes'        => array(
                    'size'          => 30,
                    // 'required' => 'required',
                    'placeholder'   => __( 'Type a prefix.', 'admin-page-framework-loader' ),
                ),
            ),
            array(
                'field_id'          => 'text_domain',
                'title'             => __( 'Text Domain', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'tip'               => __( 'The default text domain of your project.', 'admin-page-framework-loader' ),
                'description'       => 'e.g.<code>my-plugin</code>',
                'attributes'        => array(
                    'size'          => 40,
                    // 'required' => 'required',
                    'placeholder'   => __( 'Type your text domain.', 'admin-page-framework-loader' ),
                ),
            ),
            array(
                'field_id'          => 'components',
                'title'             => __( 'Components', 'admin-page-framework-loader' ),
                'type'              => 'checkbox',
                'description'       => array(
                    __( 'Select the components you would like to include in your framework files.', 'admin-page-framework-loader' ),
                    __( 'If you are not sure what to select, check them all.', 'admin-page-framework-loader' ),
                ),
                'label'               => $this->_getComponentLabels(),
                'default'             => array_fill_keys(
                    array_keys( $this->_getComponentLabels() ),
                    true // all true
                ),
                'select_all_button'     => true,
                'select_none_button'    => true,
                'label_min_width'       => '100%',
                'attributes'            => array(
                    'core'      => array(
                        'disabled' => 'disabled',
                    ),
                ),
            ),
            array(
                'field_id'          => 'download',
                'title'             => __( 'Download', 'admin-page-framework-loader' ),
                'type'              => 'export',
                'label_min_width'   => 0,
                'order'             => 100,
                'value'             => __( 'Download', 'adimn-page-framework-demo' ),
                'file_name'         => 'admin-page-framework.zip',  // the default file name. This will be modified by the filter.
                'format'            => 'text',  // 'json', 'text', 'array'
                'description'       => $oFactory->oUtil->getAOrB(
                    class_exists( 'ZipArchive' ),
                    __( 'Download the compiled framework files as a zip file.', 'admin-page-framework-loader' ),
                    __( 'The zip extension needs to be enabled to use this feature.', 'admin-page-framework-loader' )
                ),
                'attributes'        => array(
                    'disabled'  => $oFactory->oUtil->getAOrB(
                        class_exists( 'ZipArchive' ),
                        null,
                        'disabled'
                    ),
                ),
            )
        );

        new AdminPageFrameworkLoader_AdminPage_Tool_Generator_CustomFieldTypes(
            $oFactory,
            $sSectionID
        );

    }
        /**
         * Returns component labels as an array.
         * @since       3.5.4
         * @return      array
         */
        private function _getComponentLabels() {
            return array(
                'admin_pages'           => __( 'Admin Pages', 'admin-page-framework-loader' ),
                'network_admin_pages'   => __( 'Network Admin Pages', 'admin-page-framework-loader' ),
                'post_types'            => __( 'Custom Post Types', 'admin-page-framework-loader' ),
                'taxonomies'            => __( 'Taxonomy Fields', 'admin-page-framework-loader' ),
                'term_meta'             => __( 'Term Meta', 'admin-page-framework-loader' ),
                'meta_boxes'            => __( 'Post Meta Boxes', 'admin-page-framework-loader' ),
                'page_meta_boxes'       => __( 'Page Meta Boxes', 'admin-page-framework-loader' ),
                'widgets'               => __( 'Widgets', 'admin-page-framework-loader' ),
                'user_meta'             => __( 'User Meta', 'admin-page-framework-loader' ),
                'utilities'             => __( 'Utilities', 'admin-page-framework-loader' ),
            );
        }

    /**
     * Validates the submitted form data.
     *
     * @since       3.5.4
     */
    public function validate( $aInput, $aOldInput, $oAdminPage, $aSubmitInfo ) {

        $_bVerified = true;
        $_aErrors   = array();
        $aInput     = $this->_sanitizeFieldValues( $aInput, $oAdminPage );

        // the class prefix must not contain white spaces and some other characters not supported in PHP class names.
        preg_match(
            '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/',     // pattern - allowed characters for variables in PHP.
            $aInput[ 'class_prefix' ],     // subject
            $_aMatches
        );
        if ( $aInput[ 'class_prefix' ] && empty( $_aMatches ) ) {
            $_aErrors[ $this->sSectionID ][ 'class_prefix' ] = __( 'The prefix must consist of alphanumeric with underscores.', 'admin-page-framework-loader' );
            $_bVerified = false;
        }

        if ( ! $aInput[ 'text_domain' ] ) {
            $_aErrors[ $this->sSectionID ][ 'text_domain' ] = __( 'The text domain cannot be empty.', 'admin-page-framework-loader' );
            $_bVerified = false;
        }

        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'admin-page-framework-loader' ) );
            return $aOldInput;
        }

        return $aInput;

    }
        /**
         * Sanitizes user-submitted form field values.
         * @since       3.5.4
         * @return      array       The modified input array.
         */
        private function _sanitizeFieldValues( array $aInput, $oAdminPage ) {

            $aInput[ 'class_prefix' ] = trim(
                $oAdminPage->oUtil->getElement(
                    $aInput,
                    'class_prefix',
                    ''
                )
            );
            $aInput[ 'text_domain' ] = trim(
                $oAdminPage->oUtil->getElement(
                    $aInput,
                    'text_domain',
                    ''
                )
            );
            return $aInput;

        }

    /**
     * Lets the user download their own version of Admin Page Framework.
     *
     * @since           3.5.4
     * @callback        filter      export_{instantiated class name}_{section id}_{field id}
     */
    public function replyToDownloadFramework( $aSavedData, $sSubmittedFieldID, $sSubmittedInputID, $oAdminPage ) {

        $_sFrameworkDirPath = AdminPageFrameworkLoader_Registry::$sDirPath . '/library/apf';
        if ( ! file_exists( $_sFrameworkDirPath ) ) {
            return $aSavedData;
        }

        $_sTempFile = $oAdminPage->oUtil->setTempPath( 'admin-page-framework.zip' );
        $_sData     = $this->_getDownloadFrameworkZipFile(
            $_sFrameworkDirPath,
            $_sTempFile
        );
        header( "Content-Length: " . strlen( $_sData ) );
        unlink( $_sTempFile );
        return $_sData;

    }
        /**
         * Generates the framework zip data.
         *
         * @since       3.5.4
         * @return      string      The binary zip data.
         */
        private function _getDownloadFrameworkZipFile( $sFrameworkDirPath, $sDestinationPath ) {

            $_oZip = new AdminPageFramework_Zip(
                $sFrameworkDirPath,
                $sDestinationPath,
                array(
                    'include_directory'             => false,   // wrap contents in a sub-directory
                    'additional_source_directories' => apply_filters(
                        AdminPageFrameworkLoader_Registry::HOOK_SLUG . '_filter_generator_additional_source_directories',
                        array() // directory paths
                    ),
                ),
                array(  // callbacks
                    'file_name'         => array( $this, '_replyToModifyPathInArchive' ),
                    'directory_name'    => array( $this, '_replyToModifyPathInArchive' ),
                    'file_contents'     => array( $this, '_replyToModifyFileContents' ),
                )
            );
            $_bSucceed = $_oZip->compress();
            if ( ! $_bSucceed ) {
                return '';
            }
            return file_get_contents( $sDestinationPath );

        }
            /**
             * Modifies the path in the archive which include the file name.
             *
             * Return an empty string to drop the item.
             *
             * @remark      Gets called earlier than the callback for the file contents.
             * @param       string      $sPathInArchive      The internal path of the archive including the parsing file name.
             * @since       3.5.4
             * @return      string
             */
            public function _replyToModifyPathInArchive( $sPathInArchive ) {

                // Check if it belongs to selected components.
                if ( false === $this->_isAllowedArchivePath( $sPathInArchive ) ) {
                    return '';  // empty value will drop the entry
                }
                return $sPathInArchive;

            }
                /**
                 * Checks whether the passed archive path is allowed.
                 *
                 * @since       3.5.4
                 * @remark      string      $sPath              The path to check. It can be a directory or a file.
                 * @param       string      $sPathInArchive     The parsing directory path set to the archive.
                 * The passed path for the archive has a trailing slash. It starts with a directory name.
                 * e.g.
                 * `utility/AdminPageFramework_WPReadmeParser/`
                 * `factory/widget/model/`
                 * @return      boolean
                 */
                private function _isAllowedArchivePath( $sPath ) {

                    foreach( $this->_getDisallowedArchiveDirectoryPaths() as $_sDisallowedPath ) {
                        $_bHasPrefix = $this->oFactory->oUtil->hasPrefix(
                            ltrim( $_sDisallowedPath, '/' ), // needle
                            ltrim( $sPath, '/' ) // haystack
                        );
                        if ( $_bHasPrefix ) {
                            return false;
                        }
                    }
                    return true;

                }
                /**
                 * Defines the archive paths of components.
                 *
                 * @remark      Make sure to have the tailing slash.
                 * Othwerwise, 'factory/AdminPageFramework' will match items that belong to other components.
                 * @since       3.5.4
                 */
                private $_aComponentPaths = array(
                    'admin_pages'           => array(
                        'factory/admin_page/',
                    ),
                    'network_admin_pages'   => array(
                        'factory/admin_page/',
                        'factory/network_admin_page/',
                    ),
                    'post_types'            => array(
                        'factory/post_type/',
                    ),
                    'taxonomies'            => array(
                        'factory/taxonomy_field/',
                    ),
                    'term_meta'             => array(
                        'factory/taxonomy_field/',
                        'factory/term_meta/',
                    ),
                    'meta_boxes'            => array(
                        'factory/meta_box/',
                    ),
                    'page_meta_boxes'       => array(
                        'factory/meta_box/',
                        'factory/page_meta_box/',
                    ),
                    'widgets'               => array(
                        'factory/widget/',
                    ),
                    'user_meta'             => array(
                        'factory/user_meta/',
                    ),
                    'utilities'             => array(
                        'utility/',
                    ),
                );
                /**
                 * Returns an array holding allowed paths set to the archive.
                 * @since       3.5.4
                 * @return      array
                 */
                private function _getDisallowedArchiveDirectoryPaths() {

                    // Cache
                    static $_aDisallowedPaths;
                    if ( isset( $_aDisallowedPaths ) ) {
                        return $_aDisallowedPaths;
                    }

                    // User selected items
                    $_aSelectedComponents = $this->_getCheckedComponents();

                    // List paths.
                    $_aAllComponentsPaths       = array();
                    $_aSelectedComponentsPaths  = array();
                    foreach( $this->_aComponentPaths as $_sKey => $_aPaths ) {

                        // Extract all component paths.
                        $_aAllComponentsPaths = array_merge(
                            $_aAllComponentsPaths,
                            $_aPaths
                        );

                        // Extract selected components paths.
                        if ( in_array( $_sKey, $_aSelectedComponents ) ) {
                            $_aSelectedComponentsPaths = array_merge(
                                $_aSelectedComponentsPaths,
                                $_aPaths
                            );
                        }

                    }

                    return array_diff(
                        array_unique( $_aAllComponentsPaths ),
                        array_unique( $_aSelectedComponentsPaths )
                    );

                }
                    /**
                     * Returns an array holding elements that the user has selected in the form.
                     * @since       3.5.4
                     * @return      array
                     */
                    private function _getCheckedComponents() {

                        $_aCheckedComponents = $this->oFactory->oUtil->getElementAsArray(
                            $_POST,
                            array(
                                $this->oFactory->oProp->sOptionKey,
                                $this->sSectionID,
                                'components' // field id
                            ),
                            array()
                        );
                        $_aCheckedComponents = array_filter( $_aCheckedComponents );
                        return array_keys( $_aCheckedComponents );

                    }

            /**
             * Modifies the file contents of the archive.
             *
             * @since       3.5.4
             * @return      string      The modified file contents.
             */
            public function _replyToModifyFileContents( $sFileContents, $sPathInArchive ) {

                // Check the file extension.
                $_aAllowedExtensions = apply_filters(
                    AdminPageFrameworkLoader_Registry::HOOK_SLUG . '_filter_generator_allowed_file_extensions',
                    array( 'php', 'css', 'js' )
                );
                if ( ! in_array( pathinfo( $sPathInArchive, PATHINFO_EXTENSION ), $_aAllowedExtensions ) ) {
                    return $sFileContents;
                }

                // Modify the file contents.
                $sFileContents = apply_filters(
                    AdminPageFrameworkLoader_Registry::HOOK_SLUG . '_filter_generator_file_contents',
                    $sFileContents,
                    $sPathInArchive,
                    $this->oFactory->oUtil->getElement(
                        $_POST,
                        array(
                            $this->oFactory->oProp->sOptionKey,
                        ),
                        array()
                    ),
                    $this->oFactory
                );

                // At this point, it is a php file.
                return $this->_modifyClassNameByPath(
                    $sFileContents,
                    $sPathInArchive
                );

            }
                /**
                 * Modifies the given file contents.
                 *
                 * @since       3.5.4
                 * @return      string
                 */
                private function _modifyClassNameByPath( $sFileContents, $sPathInArchive ) {

                    // The inclusion class list file needs to be handled differently.
                    if ( $this->oFactory->oUtil->hasSuffix( 'admin-page-framework-include-class-list.php', $sPathInArchive ) ) {
                        return $this->_modifyClassNameOfInclusionList( $sFileContents );
                    }

                    // Insert a included component note in the header comment.
                    if ( $this->oFactory->oUtil->hasSuffix( 'admin-page-framework.php', $sPathInArchive ) ) {
                        $sFileContents = $this->_modifyFileDockblock( $sFileContents );
                        return $this->_modifyClassName( $sFileContents );
                    }

                    $sFileContents = $this->_modifyClassName( $sFileContents );

                    // If it is the message class, modify the text domain.
                    // @deprecated  3.6.0+
                    // if ( ! $this->oFactory->oUtil->hasSuffix( 'AdminPageFramework_Message.php', $sPathInArchive ) ) {
                        // return $sFileContents;
                    // }
                    return $this->_modifyTextDomain( $sFileContents );

                }
                    /**
                     * Inserts additional information such as an included component list and a date to the file doc-block (the header comment part).
                     * @since       3.5.4
                     * @return      string
                     */
                    private function _modifyFileDockblock( $sFileContents ) {

                        $_aCheckedComponents = $this->oFactory->oUtil->getArrayElementsByKeys(
                            $this->_getComponentLabels(),
                            $this->_getCheckedComponents()
                        );
                        $_aInsert = array(
                            'Included Components: ' . implode( ', ', $_aCheckedComponents ),
                            'Generated on ' . date( 'Y-m-d' ),  // today's date
                        );
                        return preg_replace(
                            '#\*/#', // needle - matches '*/'
                            implode( PHP_EOL . ' ', $_aInsert ) . ' \0', // replacement \0 is a back-reference to '*/'
                            $sFileContents, // subject
                            1 // replace only the first occurrence
                        );

                    }
                    /**
                     * Modifies the class inclusion list.
                     * @since       3.5.4
                     * @return      string
                     */
                    private function _modifyClassNameOfInclusionList( $sFileContents ) {
                        // Replace the array key names.
                        $sFileContents = preg_replace_callback(
                            '/(["\'])(.+)\1(?=\s?+=>)/',  // pattern '
                            array( $this, '_replyToModifyPathName' ),   // callable
                            $sFileContents // subject
                        );
                        // Replace the registry class names.
                        return preg_replace_callback(
                            '/(=>\s?+)(.+)(?=::)/',  // pattern '
                            array( $this, '_replyToModifyPathName' ),   // callable
                            $sFileContents // subject
                        );
                    }
                        /**
                         * Modifies the regex-matched string.
                         * @callback    function        preg_replace_callback()
                         * @since       3.5.4
                         */
                        public function _replyToModifyPathName( $aMatches ) {
                            return $this->_modifyClassName( $aMatches[ 0 ] );
                        }

                /**
                 * Modifies the given class name.
                 *
                 * @since       3.5.4
                 * @return      string
                 */
                private function _modifyClassName( $sSubject ) {

                    $_sPrefix = $this->_getFormSubmitValueByFieldIDAsString( 'class_prefix' );
                    return strlen( $_sPrefix )
                        ? str_replace(
                            'AdminPageFramework', // search
                            $_sPrefix . 'AdminPageFramework', // replace
                            $sSubject // subject
                        )
                        : $sSubject;

                }
                /**
                 * Modifies the text domain in the given file contents.
                 *
                 * @since       3.5.4
                 * @return      string
                 */
                private function _modifyTextDomain( $sFileContents ) {

                    $_sTextDomain = $this->_getFormSubmitValueByFieldIDAsString( 'text_domain' );
                    return strlen( $_sTextDomain )
                        ? str_replace(
                            'admin-page-framework', // search
                            $_sTextDomain, // replace
                            $sFileContents // subject
                        )
                        : $sFileContents;

                }
                    /**
                     * Retrieves the value from the $_POST array by the given field ID.
                     *
                     * @since       3.5.4
                     * @return      string
                     */
                    private function _getFormSubmitValueByFieldIDAsString( $sFieldID ) {

                        static $_aCaches=array();
                        $_aCaches[ $sFieldID ] = isset( $_aCaches[ $sFieldID ] )
                            ? $_aCaches[ $sFieldID ]
                            : $this->oFactory->oUtil->getElement(
                                $_POST,
                                array(
                                    $this->oFactory->oProp->sOptionKey,
                                    $this->sSectionID,
                                    $sFieldID
                                ),
                                ''
                            );
                        return trim( ( string ) $_aCaches[ $sFieldID ] );

                    }

    /**
     * Modifies the HTTP header of the export field.
     *
     * @callback    filter      export_header_{...}
     * @since       3.5.4
     * #return      array
     */
    public function replyToModifyExportHTTPHeader( $aHeader, $sFieldID, $sInputID, $mData, $sFileName, $oFactory ) {

        $sFileName = $this->_getDownloadFileName();
        return array(
            'Pragma'                    => 'public',
            'Expires'                   => 0,
            'Cache-Control'             => array(
                'must-revalidate, post-check=0, pre-check=0',
                'public',
            ),
            'Content-Description'       => 'File Transfer',
            'Content-type'              => 'application/octet-stream',   // 'application/zip' may work as well
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition'       => 'attachment; filename="' . $sFileName .'";',
            // 'Content-Length'            => strlen( $mData ),
        ) + $aHeader;

    }

    /**
     * Filters the exporting file name.
     *
     * @callback    filter    "export_name_{page slug}_{tab slug}" filter.
     * @return      string
     */
    public function replyToFilterFileName( $sFileName, $sFieldID, $sInputID, $vExportingData, $oAdminPage ) {
        return $this->_getDownloadFileName();
    }

    /**
     * Returns the user-set file name.
     *
     * The user set text domain will be added as a prefix to `admin-page-framework.zip`.
     *
     * @since       3.5.4
     * @return      string
     */
    private function _getDownloadFileName() {

        $_sFileNameWOExtension = $this->oFactory->oUtil->getElement(
            $_POST,
            array(
                $this->oFactory->oProp->sOptionKey,
                $this->sSectionID,
                'text_domain' // field id
            )
        );
        $_sFileNameWOExtension = trim( $_sFileNameWOExtension );
        return $this->oFactory->oUtil->getAOrB(
                $_sFileNameWOExtension,
                $_sFileNameWOExtension . '-admin-page-framework',
                'admin-page-framework'
            )
            . '.' . AdminPageFramework_Registry::VERSION
            . '.zip';

    }

}
