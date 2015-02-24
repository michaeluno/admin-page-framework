<?php
/**
 * Admin Page Framework Loader
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 */

/**
 * Adds the 'Generator' section to the 'Generator' tab.
 * 
 * @since       3.5.4
 */
class AdminPageFrameworkLoader_AdminPage_Tool_Generator_Generator extends AdminPageFrameworkLoader_AdminPage_Section_Base {
    
    /**
     * A user constructor.
     * 
     * @since       3.5.4
     * @return      void
     */
    protected function construct( $oFactory ) {
        
        add_action( 
            'export_name_' . $this->sPageSlug . '_' . $this->sTabSlug, 
            array( $this, 'replyToFilterFileName' ), 
            10, 
            5 
        );
        add_action( 
            // export_{instantiated clas name}_{section id}_{field id}
            "export_{$oFactory->oProp->sClassName}_{$this->sSectionID}_download",
            array( $this, 'replyToDownloadMinifiedVersion' ), 
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
                'field_id'          => 'class_prefix',
                'title'             => __( 'Class Prefix', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'description'       => __( 'Set alphanumeric characters for the class names', 'admin-page-framework-loader' )
                    . ' ' .  __( 'For example, if you set here <code>MyPluginName_</code>, you will need to extend the class named <code>MyClassName_AdminPageFramework</code> instead of <code>AdminPageFramework</code>.', 'admin-page-framework-loader' )
                    . ' e.g.<code>MyPluginName_</code>',
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
                'description'       => __( 'The default text domain of your project.', 'admin-page-framework-loader' )
                    . ' e.g.<code>my-plugin</code>',
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
                'label'             => array(
                    'admin_pages'           => __( 'Admin Pages', 'admin-page-framework-loader' ),
                    'network_admin_pages'   => __( 'Nwtwork Admin Pages', 'admin-page-framework-loader' ),
                    'post_types'            => __( 'Custom Post Types', 'admin-page-framework-loader' ),
                    'taxonomies'            => __( 'Taxonomy Fields', 'admin-page-framework-loader' ),
                    'meta_boxes'            => __( 'Post Meta Boxes', 'admin-page-framework-loader' ),
                    'page_meta_boxes'       => __( 'Page Meta Boxes', 'admin-page-framework-loader' ),
                    'widgets'               => __( 'Widgets', 'admin-page-framework-loader' ),
                    'user_meta'             => __( 'User Meta', 'admin-page-framework-loader' ),
                    'utilities'             => __( 'Utilities', 'admin-page-framework-loader' ),
                ),
                'default'             => array(
                    'admin_pages'           => true,
                    'network_admin_pages'   => true,
                    'post_types'            => true,
                    'taxonomies'            => true,
                    'meta_boxes'            => true,
                    'page_meta_boxes'       => true,
                    'widgets'               => true,
                    'user_meta'             => true,
                    'utilities'             => true,
                ),      
                'select_all_button'     => true,    
                'select_none_button'    => true,
                'label_min_width'       => '100%',                
                'attributes'            => array(
                    'core'      => array(
                        'disabled' => 'disabled',
                    ),
                ),
                /* 'attributes'        => array(
                    'size'          => 40,
                    // 'required' => 'required',
                    'placeholder'   => __( 'Type your text domain.', 'admin-page-framework-loader' ),
                ), */
            ),                
            array( 
                'field_id'          => 'download',
                'title'             => __( 'Download', 'admin-page-framework-loader' ),
                'type'              => 'export',
                'label_min_width'   => 0,
                'value'             => __( 'Download', 'adimn-page-framework-demo' ),
                'file_name'         => 'admin-page-framework.min.php',  // the default file name. This will be modified by the filter.
                'format'            => 'text',  // 'json', 'text', 'array'      
                'description'       => __( 'Download the minified version.', 'admin-page-framework-loader' ),
            ) 
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
                
        // the class prefix must not contain white spaces and some other characters not supported in PHP class names.
        $aInput[ $this->sSectionID ][ 'class_prefix' ] = trim(
            $oAdminPage->oUtil->getElement(
                $aInput,
                array( $this->sSectionID, 'class_prefix' ),
                ''
            )
        );
                
        preg_match( 
            '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/',     // pattern - allowed characters for variables.
            $aInput[ $this->sSectionID ][ 'class_prefix' ],     // subject
            $_aMatches 
        );
        if ( $aInput[ $this->sSectionID ][ 'class_prefix' ] && empty( $_aMatches ) ) {
      
            // $variable[ 'sectioni_id' ]['field_id']
            $_aErrors[ $this->sSectionID ]['class_prefix'] = __( 'The prefix must consist of alphanumeric with underscores.', 'admin-page-framework-loader' );
            $_bVerified = false;
                    
        }
        
        // Sanitize the set test domain string.
        $aInput[ $this->sSectionID ][ 'text_domain' ] = trim(
            $oAdminPage->oUtil->getElement(
                $aInput,
                array( $this->sSectionID, 'text_domain' ),
                ''
            )
        );        

        // An invalid value is found.
        if ( ! $_bVerified ) {

            /* 4-1. Set the error array for the input fields. */
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'admin-page-framework-loader' ) );

            return $aOldInput;
            
        }
                
        return $aInput;     
        
    }
    
    /**
     * Lets the user download their own version of Admin Page Framework.
     * 
     * @since           3.5.4
     * @callback        filter      export_{instantiated clas name}_{section id}_{field id}
     */
    public function replyToDownloadMinifiedVersion( $aSavedData, $sSubmittedFieldID, $sSubmittedInputID, $oAdminPage ) {
        
        $_sFrameworkDirPath = AdminPageFrameworkLoader_Registry::$sDirPath . '/library/admin-page-framework';
        if ( file_exists( $_sFrameworkDirPath ) ) {
            $_sTempFile = $oAdminPage->oUtil->setTempPath( 'admin-page-framework.zip' );
            $_sData     = $this->_getDownloadFrameworkZipFile( $_sFrameworkDirPath, $_sTempFile );
            unlink( $_sTempFile );
            return $_sData;
        }
        return $aSavedData;
        
    }
        /**
         * 
         * @since       3.5.4
         */
        private function _getDownloadFrameworkZipFile( $sFrameworkDirPath, $sDestinationPath ) {
            
            $_oZip = new AdminPageFramework_Zip( 
                $sFrameworkDirPath, 
                $sDestinationPath, 
                false,  // wrap contents in a directory
                array(  // callbacks
                    'file_name'         => array( $this, '_replyToModifyFileNameInArchive' ),
                    'directory_name'    => array( $this, '_replyToModifyDirectoryNameInArchive' ),
                    'file_contents'     => array( $this, '_replyToModifyFileContents' ),
                ) 
            );
            $_bSucceed = $_oZip->compress();
            if ( ! $_bSucceed ) {
                return;
            }
            return file_get_contents( $sDestinationPath );
            
        }
            /**
             * Modifies the path in the archive which include the file name.
             * 
             * Return an empty string to drop the item.
             * 
             * @remark      Gets called earlier than the callback for the file contents.
             * @param       string      $sFileName      The internal path of the archive including the parsing file name.
             * @since       3.5.4
             * @return      string
             */
            public function _replyToModifyFileNameInArchive( $sPathInArchive ) {
                
                // Check if it belongs to selcted components.
                if ( false === $this->_isAllowedArchivePath( $sPathInArchive ) ) {
// AdminPageFramework_Debug::log( 'disalled' );
// AdminPageFramework_Debug::log( $sPathInArchive );
                    return '';
                }                
                return $this->_modifyClassName( $sPathInArchive );
                                
            }
            /**
             * Modifies the path in the archive which includes the directory name.
             * @since       3.5.4
             * @return      string
             * @param       string      $sFileName      The internal path of the archive including the parsing directory name.
             */
            public function _replyToModifyDirectoryNameInArchive( $sPathInArchive ) {
                
                if ( false === $this->_isAllowedArchivePath( $sPathInArchive ) ) {
                    return '';
                }
                return $this->_modifyClassName( $sPathInArchive );
                
            }

                /**
                 * Checks wiether the passed archive path is allowed.
                 * 
                 * @since       3.5.4
                 * @remark      string      $sPath      The path to check. It can be a directory or a file.
                 * @param       string      $sPathInArchive     The parsing directory path set to the archive. 
                 * The pased path for the archive has a trailing slash. It start with a directory name.
                 * e.g.
                 * `utility/AAA_AdminPageFramework_WPReadmeParser/`
                 * `factory/AAA_AdminPageFramework_Widget/model/`
                 * @return      boolean
                 */
                private function _isAllowedArchivePath( $sPath ) {
                    
                    foreach( $this->_getDisallowedArchiveDirectoryPaths() as $_sDisallowedPath ) {
                        if ( $this->oFactory->oUtil->hasPrefix( $_sDisallowedPath, $sPath ) ) {
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
                        'factory/AdminPageFramework/',
                    ),
                    'network_admin_pages'   => array(
                        'factory/AdminPageFramework/',
                        'factory/AdminPageFramework_NetworkAdmin/',
                    ),
                    'post_types'            => array(
                        'factory/AdminPageFramework_PostType/', 
                    ),
                    'taxonomies'            => array(
                        'factory/AdminPageFramework_TaxonomyField/',
                    ),
                    'meta_boxes'            => array(
                        'factory/AdminPageFramework_MetaBox/',
                    ),
                    'page_meta_boxes'       => array(
                        'factory/AdminPageFramework_MetaBox/',
                        'factory/AdminPageFramework_MetaBox_Page/',
                    ),
                    'widgets'               => array(
                        'factory/AdminPageFramework_Widget/',
                    ),
                    'user_meta'             => array(
                        'factory/AdminPageFramework_UserMeta/',
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
             * @since       3.5.4
             */
            public function _replyToModifyFileContents( $sFileContents, $sPathInArchive ) {
AdminPageFramework_Debug::log( $sPathInArchive );
                // Check the file extension.
                if ( ! in_array( pathinfo( $sPathInArchive, PATHINFO_EXTENSION ), array( 'php' ) ) ) {
                    return $sFileContents;
                }
                
                // At this point, it is a php file.
                $sFileContents = $this->_modifyClassName( $sFileContents );
                
                // If it is the message class, modifiy the text domain
                if ( ! $this->oFactory->oUtil->hasSuffix( 'AdminPageFramework_Message.php', $sPathInArchive ) ) {
                    return $sFileContents;
                }                
                return $this->_modifyTextDomain( $sFileContents );
                
            }
                /**
                 * Modifies the given class name.
                 * 
                 * @since       3.5.4
                 * @return      string
                 */
                private function _modifyClassName( $sSubject ) {
                    
                    static $_sPrefix;
                    $_sPrefix = isset( $_sPrefix )
                        ? $_sPrefix
                        : $this->oFactory->oUtil->getElement(
                            $_POST,
                            array( 
                                $this->oFactory->oProp->sOptionKey, 
                                $this->sSectionID, 
                                'class_prefix' 
                            ),
                            ''
                        );
                    $_sPrefix = trim( $_sPrefix );
                    if ( ! strlen( $_sPrefix ) ) {
                        return $sSubject;
                    }
                    return str_replace( 
                        'AdminPageFramework', // search 
                        $_sPrefix . 'AdminPageFramework', // replace
                        $sSubject // subject
                    );                       
                    
                }
                /**
                 * Modifies the text domain in the given file contents.
                 * 
                 * @since       3.5.4
                 * @return      string
                 */                
                private function _modifyTextDomain( $sFileContents ) {

                    static $_sTextDomain;
                    $_sTextDomain = isset( $_sTextDomain )
                        ? $_sTextDomain
                        : $this->oFactory->oUtil->getElement(
                            $_POST,
                            array( 
                                $this->oFactory->oProp->sOptionKey, 
                                $this->sSectionID, 
                                'text_domain' 
                            ),
                            ''
                        );
                    $_sTextDomain = trim( $_sTextDomain );
                    if ( ! strlen( $_sTextDomain ) ) {
                        return $sFileContents;
                    }
                    return str_replace( 
                        'admin-page-framework', // search 
                        $_sTextDomain, // replace
                        $sFileContents // subject
                    );                      
                }
        
    /**
     * Modifies the HTTP header of the export field.
     * 
     * @callback    filter      export_header_{...}
     * @since       3.5.4
     * #return      array
     */
    public function replyToModifyExportHTTPHeader( $aHeader, $sFieldID, $sInputID, $mData, $sFileName, $oFactory ) {
            
        return array(
            'Pragma'                    => 'public',
            'Expires'                   => 0,
            'Cache-Control'             => array(
                'must-revalidate, post-check=0, pre-check=0',
                'public',
            ),
            'Content-Description'       => 'File Transfer',
            'Content-type'              => 'application/zip',        // application/octet-stream
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition'       => 'attachment; filename="' . $sFileName .'"',
        ) + $aHeader;
        
    }

    /**
     * Filters the exportign file name.
     * 
     * @callback    filter    "export_name_{page slug}_{tab slug}" filter.
     * @return      string
     */
    public function replyToFilterFileName( $sFileName, $sFieldID, $sInputID, $vExportingData, $oAdminPage ) { 
        return 'admin-page-framework.zip';
    }        
    
}