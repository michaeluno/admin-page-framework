<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the Contact page to the demo plugin.
 * 
 * @since   3.4.6
 */
class APF_Demo_Tool_Tab_MinifiedVersion {

    public function __construct( $oFactory, $sPageSlug='', $sTabSlug='' ) {
    
        $this->oFactory     = $oFactory;
        // $this->sClassName   = $oFactory->oProp->sClassName;
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
                'title'         => __( 'Minified Version', 'admin-page-framework-demo' ),
            )
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToAddFormElements' ) );
        add_action( 'validation_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToValidateSubmittedData' ), 10, 3 );
        add_action( 'export_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToFilterExportingData' ), 10, 4 );
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
                'title'         => __( 'Download Minified Version', 'admin-page-framework-demo' ),
                'description'   => __( 'When you click the Download link below, the minified version of the framework will be generated.', 'admin-page-framework-demo' ),
            )            
        );        

     
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section id
            array( 
                'field_id'          => 'class_prefix',
                'title'             => __( 'Class Prefix', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'description'       => __( 'Set alphanumeric characters for the class names', 'admin-page-framework-demo' )
                    . __( 'For example, if you set here <code>MyPluginName_</code>, you will need to extend the class named <code>MyClassName_AdminPageFramework</code> instead of <code>AdminPageFramework</code>.', 'admin-page-framework-demo' )
                    . ' e.g.<code>MyPluginName_</code>',
                'attributes'        => array(
                    'size'          => 30,
                    // 'required' => 'required',
                    'placeholder'   => __( 'Type a prefix.', 'admin-page-framework-demo' ),
                ),
            ),
            array( 
                'field_id'          => 'minified_script_name',
                'title'             => __( 'File Name', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'description'       => __( 'The file name of the minified script.', 'admin-page-framework-demo' )
                    . ' e.g.<code>my-plugin-admin-page-framework.min.php</code>',
                'default'   => 'admin-page-framework.min.php',
                'attributes'        => array(
                    'size'          => 40,
                    // 'required' => 'required',
                    'placeholder'   => __( 'Type a prefix.', 'admin-page-framework-demo' ),
                ),
            ),            
            array( 
                'field_id'          => 'send',
                'type'              => 'export',
                'label_min_width'   => 0,
                'value'             => __( 'Download', 'adimn-page-framework-demo' ),
                'file_name'         => 'admin-page-framework.min.php',  // the default file name. This will be modified by the filter.
                'format'            => 'text',  // 'json', 'text', 'array'
                'after_field'       => version_compare( PHP_VERSION, '5.3.0' ) >= 0
                    ? ''
                    : '<p class="field-error">*' . __( 'At least PHP v5.3.0 is required to minify scripts.', 'admin-page-framework-demo' ) . '</p>',
                'attributes'        => array(   
                    'disabled'      => version_compare( PHP_VERSION, '5.3.0' ) >= 0
                        ? null
                        : 'disabled',
                    // 'field' => array(
                        // 'style' => 'float:right; clear:none; display: inline;',
                    // ),
                ),    
             
            ),     
            array()    
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
            $_aErrors[ $this->sSectionID ]['class_prefix'] = __( 'The prefix must consist of alphanumeric with underscores.', 'admin-page-framework-demo' );
            $_bVerified = false;
                    
        }

        /* 4. An invalid value is found. */
        if ( ! $_bVerified ) {

            /* 4-1. Set the error array for the input fields. */
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'admin-page-framework-demo' ) );

            return $aOldInput;
            
        }
                
        return $aInput;     
        
    }
    
        
    
    /**
     * Filters the exporting data.
     * 
     * @remark      The callback method for the "export_{page slug}_{tab slug}".
     */
    public function replyToFilterExportingData( $aSavedData, $sSubmittedFieldID, $sSubmittedInputID, $oAdminPage ) {
               
        require( APFDEMO_DIRNAME . '/tool/minifier/class/PHP_Class_Files_Minifier.php' );

        $sTargetBaseDir		= APFDEMO_DIRNAME;
        $sTargetDir			= $sTargetBaseDir . '/development';
        $sResultFilePath	= $sTargetBaseDir . '/library/admin-page-framework.min.php';
        $sLicenseFileName	= 'MIT-LICENSE.txt';
        $sLicenseFilePath	= $sTargetDir . '/' . $sLicenseFileName;
        $sHeaderClassName	= 'AdminPageFramework_MinifiedVersionHeader';
        $sHeaderClassPath	= $sTargetDir . '/_model/AdminPageFramework_MinifiedVersionHeader.php';
                     
        $_oMinifier = new PHP_Class_Files_Minifier( 
            $sTargetDir, 
            '',     // the destination path - do not write to file
            array(
                'header_class_name'	=> $sHeaderClassName,
                'header_class_path'	=> $sHeaderClassPath,
                'output_buffer'		=> false,
                'write_to_file'     => false,
                'header_type'		=> 'CONSTANTS',	
                'exclude_classes'	=> array(
                    'AdminPageFramework_MinifiedVersionHeader', 
                    'AdminPageFramework_InclusionClassFilesHeader',
                    'admin-page-framework-include-class-list',
                ),
                'search'			=>	array(
                    'allowed_extensions'	=>	array( 'php' ),	// e.g. array( 'php', 'inc' )
                    // 'exclude_dir_paths'		=>	array( $sTargetBaseDir . '/include/class/admin' ),
                    'exclude_dir_names'		=>	array( '_document' ),
                    'is_recursive'			=>	true,
                ),			        
            )
        );
        
        $_sPrefix = isset( $_POST[ $this->oFactory->oProp->sClassName ][ $this->sSectionID ][ 'class_prefix' ] ) && $_POST[ $this->oFactory->oProp->sClassName ][ $this->sSectionID ][ 'class_prefix' ]
            ? $_POST[ $this->oFactory->oProp->sClassName ][ $this->sSectionID ][ 'class_prefix' ]
            : '';             
        
        return str_replace( 
            'AdminPageFramework',         // search 
            $_sPrefix . 'AdminPageFramework',         // replace
            $_oMinifier->get()  // subject
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
         *   [minified_version] => Array (
         *       [class_prefix] => 
         *       [minified_script_name] => admin-page-framework.min.php
         *   )
         * ) 
         */
        return isset( $_POST[ $this->oFactory->oProp->sClassName ][ $this->sSectionID ][ 'minified_script_name' ] ) && $_POST[ $this->oFactory->oProp->sClassName ][ $this->sSectionID ][ 'minified_script_name' ]
            ? $_POST[ $this->oFactory->oProp->sClassName ][ $this->sSectionID ][ 'minified_script_name' ]
            : $sFileName;      
        
    }    
    
}
