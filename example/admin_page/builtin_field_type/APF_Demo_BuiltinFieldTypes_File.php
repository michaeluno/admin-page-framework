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

class APF_Demo_BuiltinFieldTypes_File {

    /**
     * Stores the caller class name, set in the constructor.
     */   
    public $sClassName = 'APF_Demo';
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'files';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = '';
    
    /**
     * Sets up hooks and properties.
     */
    public function __construct( $sClassName='', $sPageSlug='', $sTabSlug='' ) {
        
        $this->sClassName   = $sClassName ? $sClassName : $this->sClassName;
        $this->sPageSlug    = $sPageSlug ? $sPageSlug : $this->sPageSlug;
        $this->sTabSlug     = $sTabSlug ? $sTabSlug : $this->sTabSlug;
              
        // load_ + page slug
        add_action( 'load_' . $this->sPageSlug, array( $this, 'replyToAddTab' ) );
        
    }
    
    /**
     * Triggered when the page is loaded.
     */
    public function replyToAddTab( $oAdminPage ) {
        
        // Tab
        $oAdminPage->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'  => $this->sTabSlug,
                'title'     => __( 'Files', 'admin-page-framework-demo' ),
            )
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToAddFormElements' ) );
        
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToAddFormElements( $oAdminPage ) {
        
        // Section
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'        => 'image_select',
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Image Selector', 'admin-page-framework-demo' ),
                'description'       => __( 'Set an image url with jQuwey based image selector.', 'admin-page-framework-demo' ),
            ),            
            array(
                'section_id'        => 'media_upload',
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Media Uploader', 'admin-page-framework-demo' ),
                'description'       => __( 'Upload binary files in addition to images.', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id'        => 'file_uploads',
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'File Uploads', 'admin-page-framework-demo' ),
                'description'       => __( 'These are upload fields. Check the <code>$_FILES</code> variable in the validation callback method that indicates the temporary location of the uploaded files.', 'admin-page-framework-demo' ),
            )            
        );        
        

        /*
         * Files - media, image, and uploader
         */
        $oAdminPage->addSettingFields(     
            'image_select',
            array( // Image Selector
                'field_id'      => 'image_select_field',
                'title'         => __( 'Select an Image', 'admin-page-framework-demo' ),
                'type'          => 'image',
                'label'         => __( 'First', 'admin-page-framework-demo' ),
                'default'       =>  plugins_url( 'asset/image/wordpress-logo-2x.png' , APFDEMO_FILE ),
                'allow_external_source' => false,
                'attributes'    => array(
                    'preview' => array(
                        'style' => 'max-width:400px;' // determines the size of the preview image. // margin-left: auto; margin-right: auto; will make the image in the center.
                    ),
                ),
                array(
                    'label'         => __( 'Second', 'admin-page-framework-demo' ),
                    'default'       => '',
                    'allow_external_source' => true,
                    'attributes'    => array(
                        'input'     => array(
                            'style' => 'background-color: #F5FFDF',
                        ),
                        'button'    => array(
                            'style' => 'background-color: #E1FCD2',
                        ),
                        'remove_button'    => array(
                            'style' => 'background-color: #E1FCD2',
                        ),                
                    ),     
                ),
                array(
                    'label'         => __( 'Third', 'admin-page-framework-demo' ),
                    'default'       => '',
                ),     
                'description'   => __( 'See the button and the input colors of the second item are different. This is done by setting the attributes individually.', 'admin-page-framework-demo' ),
            ),     
            array( // Image selector with additional capturing attributes
                'field_id'              => 'image_with_attributes',
                'title'                 => __( 'Save Image Attributes', 'admin-page-framework-demo' ),
                'type'                  => 'image',
                'attributes_to_store'   => array( 'alt', 'id', 'title', 'caption', 'width', 'height', 'align', 'link' ), // some attributes cannot be captured with external URLs and the old media uploader.
                'attributes'            => array(
                    // To use a custom text label, pass the label to the 'data-label' attribute.
                    'button'        => array(
                        'data-label' => __( 'Select Image', 'admin-page-framework-demo' ),
                    ),
                    'remove_button' => array(      // 3.2.0+
                        'data-label' => __( 'Remove', 'admin-page-framework-demo' ), // will set the Remove button label instead of the dashicon
                    ),
                ),
            ),     
            array( // Repeatable Image Fields
                'field_id'              => 'image_select_field_repeater',
                'title'                 => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type'                  => 'image',
                'repeatable'            => true,
                'attributes'            => array(
                    'preview' => array(
                        'style' => 'max-width: 300px;'
                    ),
                ),    
                'description'           => __( 'In repeatable fields, you can select multiple items at once.', 'admin-page-framework-demo' ),
            ),
            array( // Sortable Image Fields
                'field_id'              => 'image_select_field_sortable',
                'title'                 => __( 'Sortable', 'admin-page-framework-demo' ),
                'type'                  => 'image',
                'sortable'              => true,
                'attributes'            => array(
                    'preview' => array(
                        'style' => 'max-width: 200px;'
                    ),
                ),    
                array(), // the second item
                array(), // the third item
                'description' => __( 'Image fields can be sortable. This may be useful when you need to let the user set an order of images.', 'admin-page-framework-demo' ),
            ),     
            array( // Repeatable & Sortable Image Fields
                'field_id'              => 'image_select_field_repeatable_and_sortable',
                'title'                 => __( 'Repeatable & Sortable', 'admin-page-framework-demo' ),
                'type'                  => 'image',
                'repeatable'            => true,
                'sortable'              => true,
                'attributes'            => array(
                    'preview' => array(
                        'style' => 'max-width: 200px;'
                    ),
                ),    
            )
        );
        $oAdminPage->addSettingFields(   
            'media_upload',
            array( // Media File
                'field_id'              => 'media_field',
                'title'                 => __( 'Media File', 'admin-page-framework-demo' ),
                'type'                  => 'media',
                'allow_external_source' => false,
            ),    
            array( // Media File with Attributes
                'field_id'              => 'media_with_attributes',
                'title'                 => __( 'Media File with Attributes', 'admin-page-framework-demo' ),
                'type'                  => 'media',
                'attributes_to_store'   => array( 'id', 'caption', 'description' ),
                'attributes'            => array(
                    'button'        => array(
                        'data-label' => __( 'Select File', 'admin-page-framework-demo' ),
                    ),
                    'remove_button' => array(      // 3.2.0+
                        'data-label' => __( 'Remove', 'admin-page-framework-demo' ), // will set the Remove button label instead of the dashicon
                    ),
                ),        
            ),     
            array( // Repeatable Media Files
                'field_id'              => 'media_repeatable_fields',
                'title'                 => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type'                  => 'media',
                'repeatable'            => true,
            ),     
            array( // Sortable Media Files
                'field_id'              => 'media_sortable_fields',
                'title'                 => __( 'Sortable', 'admin-page-framework-demo' ),
                'type'                  => 'media',
                'sortable'              => true,
                array(), // the second item
                array(), // the third item.
            )
        );
            
        $oAdminPage->addSettingFields(   
            'file_uploads',            
            array( // Single File Upload Field
                'field_id'              => 'file_single',
                'title'                 => __( 'File', 'admin-page-framework-demo' ),
                'type'                  => 'file',
                'label'                 => __( 'Select the file', 'admin-page-framework-demo' ) . ": ",
            ),     
            array( // Multiple File Upload Fields
                'field_id'              => 'file_multiple',
                'title'                 => __( 'Multiple', 'admin-page-framework-demo' ),
                'type'                  => 'file',
                'label'                 => __( 'First', 'admin-page-framework-demo' ),
                'delimiter'             => '<br />',
                array(
                    'label' => __( 'Second', 'admin-page-framework-demo' ),
                ),
                array(
                    'label' => __( 'Third', 'admin-page-framework-demo' ),
                ),     
            ),     
            array( // Single File Upload Field
                'field_id'              => 'file_repeatable',
                'title'                 => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type'                  => 'file',
                'repeatable'            => true,
            ),
            array()
        );           
    
        add_filter( 'validation_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'validateTabForm' ), 10, 3 );
    
    }
    
    /**
     * Validates the items in the 'files' tab of the 'apf_bultin_field_types' page.
     */
    public function validateTabForm( $aInput, $aOldPageOptions, $oAdmin ) { // validation_{page slug}_{tab slug}

        /* Display the uploaded file information. */
        $aFileErrors = array();
        $aFileErrors[] = $_FILES[ $oAdmin->oProp->sOptionKey ]['error']['file_uploads']['file_single'];
        $aFileErrors[] = $_FILES[ $oAdmin->oProp->sOptionKey ]['error']['file_uploads']['file_multiple'][0];
        $aFileErrors[] = $_FILES[ $oAdmin->oProp->sOptionKey ]['error']['file_uploads']['file_multiple'][1];
        $aFileErrors[] = $_FILES[ $oAdmin->oProp->sOptionKey ]['error']['file_uploads']['file_multiple'][2];
        foreach( $_FILES[ $oAdmin->oProp->sOptionKey ]['error']['file_uploads']['file_repeatable'] as $aFile ) {
            $aFileErrors[] = $aFile;
        }
            
        if ( in_array( 0, $aFileErrors ) ) {            
            $oAdmin->setSettingNotice( __( '<h3>File(s) Uploaded</h3>', 'admin-page-framework-demo' ) . $oAdmin->oDebug->getArray( $_FILES ), 'updated' );
        }
        
        return $aInput;
        
    }    
    
}
