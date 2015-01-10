<?php
/**
 * Admin Page Framework Loader
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the Contact page to the demo plugin.
 * 
 * @since       3.4.6
 * @since       3.5.0       Moved from the demo.
 */
class AdminPageFrameworkLoader_Tool_Minifier {

    public function __construct( $oFactory, $sPageSlug='', $sTabSlug='' ) {
    
        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug ? $sPageSlug : $this->sPageSlug;
        $this->sTabSlug     = $sTabSlug ? $sTabSlug : $this->sTabSlug;
        $this->sSectionID   = $this->sTabSlug;
        $this->_addTab();
    
    }
    
    private function _addTab() {
        
        $this->oFactory->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Minifier', 'admin-page-framework-loader' ),
            )
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToAddFormElements' ) );
        add_action( 'validation_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToValidateSubmittedData' ), 10, 3 );
        add_action( "export_{$this->sClassName}_{$this->sSectionID}_download", array( $this, 'replyToDownloadMinifiedVersion' ), 10, 4 );
        add_action( 'export_name_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToFilterFileName' ), 10, 5 );
          
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToAddFormElements( $oAdminPage ) {
        
        /*
         * ( optional ) Create a form - To create a form in Admin Page Framework, you need two kinds of components: sections and fields.
         * A section groups fields and fields belong to a section. So a section needs to be created prior to fields.
         * Use the addSettingSections() method to create sections and use the addSettingFields() method to create fields.
         */
        // Section
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'    => $this->sSectionID,       // avoid hyphen(dash), dots, and white spaces
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Download Minified Version', 'admin-page-framework-loader' ),
                'description'   => __( 'When you click the Download link below, the minified version of the framework will be generated.', 'admin-page-framework-loader' ),
            )            
        );        

     
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section id
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
                'field_id'          => 'minified_script_name',
                'title'             => __( 'File Name', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'description'       => __( 'The file name of the minified script.', 'admin-page-framework-loader' )
                    . ' e.g.<code>my-plugin-admin-page-framework.min.php</code>',
                'default'   => 'admin-page-framework.min.php',
                'attributes'        => array(
                    'size'          => 40,
                    // 'required' => 'required',
                    'placeholder'   => __( 'Type a prefix.', 'admin-page-framework-loader' ),
                ),
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
     * @since       3.4.6
     */
    public function replyToValidateSubmittedData( $aInput, $aOldInput, $oAdminPage ) {
    
        $_bVerified = true;
        $_aErrors = array();
        
        // Sanitize the file name.
        $aInput[ $this->sSectionID ][ 'minified_script_name' ] = $oAdminPage->oUtil->sanitizeFileName( $aInput[ $this->sSectionID ][ 'minified_script_name' ], '-' );
        
        // the class prefix must not contain white spaces and some other characters not supported in PHP class names.
        $aInput[ $this->sSectionID ][ 'class_prefix' ] = isset( $aInput[ $this->sSectionID ][ 'class_prefix' ] )
            ? trim( $aInput[ $this->sSectionID ][ 'class_prefix' ] )
            : '';
        preg_match( '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $aInput[ $this->sSectionID ][ 'class_prefix' ], $_aMatches );
        if ( $aInput[ $this->sSectionID ][ 'class_prefix' ] && empty( $_aMatches ) ) {
      
            // $variable[ 'sectioni_id' ]['field_id']
            $_aErrors[ $this->sSectionID ]['class_prefix'] = __( 'The prefix must consist of alphanumeric with underscores.', 'admin-page-framework-loader' );
            $_bVerified = false;
                    
        }

        /* 4. An invalid value is found. */
        if ( ! $_bVerified ) {

            /* 4-1. Set the error array for the input fields. */
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'admin-page-framework-loader' ) );

            return $aOldInput;
            
        }
                
        return $aInput;     
        
    }
    
    /**
     * Lets the user download the minified version of Admin Page Framework.
     * 
     * @since   3.4.6
     */
    public function replyToDownloadMinifiedVersion( $aSavedData, $sSubmittedFieldID, $sSubmittedInputID, $oAdminPage ) {
        
        $_sMinifiedVersionPath = AdminPageFrameworkLoader_Registry::$sDirPath . '/library/admin-page-framework.min.php';
        if ( file_exists( $_sMinifiedVersionPath ) ) {
            return $this->_modifyClassNames( file_get_contents( $_sMinifiedVersionPath ) );
        }
        return $aSavedData;
        
    }
        
        /**
         * Modifies the class names of the minified script.
         * 
         * @since       3.4.6
         */
        private function _modifyClassNames( $sCode ) {

            $_sPrefix = isset( $_POST[ $this->oFactory->oProp->sOptionKey ][ $this->sSectionID ][ 'class_prefix' ] ) && $_POST[ $this->oFactory->oProp->sOptionKey ][ $this->sSectionID ][ 'class_prefix' ]
                ? $_POST[ $this->oFactory->oProp->sOptionKey ][ $this->sSectionID ][ 'class_prefix' ]
                : '';             
            
            return str_replace( 
                'AdminPageFramework',         // search 
                $_sPrefix . 'AdminPageFramework',         // replace
                $sCode // subject
            );
        
            
        }
    /**
     * Filters the file name.
     * 
     * @remark      The callback method for the "export_name_{page slug}_{tab slug}" filter.
     */
    public function replyToFilterFileName( $sFileName, $sFieldID, $sInputID, $vExportingData, $oAdminPage ) { 

        /* Inside $_POST
         * [APF_Demo_Tool] => Array (
         *   [minifier] => Array (
         *       [class_prefix] => 
         *       [minified_script_name] => admin-page-framework.min.php
         *   )
         * ) 
         */
        return isset( $_POST[ $this->oFactory->oProp->sOptionKey ][ $this->sSectionID ][ 'minified_script_name' ] ) && $_POST[ $this->oFactory->oProp->sOptionKey ][ $this->sSectionID ][ 'minified_script_name' ]
            ? $_POST[ $this->oFactory->oProp->sOptionKey ][ $this->sSectionID ][ 'minified_script_name' ]
            : $sFileName;      

    }    
    
}
