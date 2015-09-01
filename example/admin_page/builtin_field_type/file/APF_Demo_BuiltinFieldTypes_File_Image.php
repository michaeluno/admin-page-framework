<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a section in a tab.
 * 
 * @package     AdminPageFramework
 * @subpackage  Example
 */
class APF_Demo_BuiltinFieldTypes_File_Image {
    
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
    public $sSectionID  = 'image_select';
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        // Section
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'             => __( 'Image Selector', 'admin-page-framework-loader' ),
                'description'       => __( 'Set an image url with jQuwey based image selector.', 'admin-page-framework-loader' ),
            )
        );   

        // Fields
        $oFactory->addSettingFields(     
            $this->sSectionID,
            array( 
                'field_id'      => 'image_select_field',
                'title'         => __( 'Select an Image', 'admin-page-framework-loader' ),
                'type'          => 'image',
                'label'         => __( 'First', 'admin-page-framework-loader' ),
                'default'       =>  plugins_url( 'asset/image/demo/wordpress-logo-2x.png' , APFDEMO_FILE ),
                'allow_external_source' => false,
                'attributes'    => array(
                    'preview' => array(
                        'style' => 'max-width:300px;' // determines the size of the preview image. // margin-left: auto; margin-right: auto; will make the image in the center.
                    ),
                ),
                array(
                    'label'         => __( 'Second', 'admin-page-framework-loader' ),
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
                    'label'         => __( 'Third', 'admin-page-framework-loader' ),
                    'default'       => '',
                ),     
                'description'   => __( 'See the button and the input colors of the second item are different. This is done by setting the attributes individually.', 'admin-page-framework-loader' ),
            ),     
            array( // Image selector with additional capturing attributes
                'field_id'              => 'image_with_attributes',
                'title'                 => __( 'Save Image Attributes', 'admin-page-framework-loader' ),
                'type'                  => 'image',
                'attributes_to_store'   => array( 'alt', 'id', 'title', 'caption', 'width', 'height', 'align', 'link' ), // some attributes cannot be captured with external URLs and the old media uploader.
                'attributes'            => array(
                    // To use a custom text label, pass the label to the 'data-label' attribute.
                    'button'        => array(
                        'data-label' => __( 'Select Image', 'admin-page-framework-loader' ),
                    ),
                    'remove_button' => array(      // 3.2.0+
                        'data-label' => __( 'Remove', 'admin-page-framework-loader' ), // will set the Remove button label instead of the dashicon
                    ),
                    'preview' => array(
                        'style' => 'max-width: 300px;'
                    ),                    
                ),
            ),     
            array( // Repeatable Image Fields
                'field_id'              => 'image_select_field_repeater',
                'title'                 => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'                  => 'image',
                'repeatable'            => true,
                'attributes'            => array(
                    'preview' => array(
                        'style' => 'max-width: 300px;'
                    ),
                ),    
                'description'           => __( 'In repeatable fields, you can select multiple items at once.', 'admin-page-framework-loader' ),
            ),
            array( // Sortable Image Fields
                'field_id'              => 'image_select_field_sortable',
                'title'                 => __( 'Sortable', 'admin-page-framework-loader' ),
                'type'                  => 'image',
                'sortable'              => true,
                'attributes'            => array(
                    'preview' => array(
                        'style' => 'max-width: 200px;'
                    ),
                ),    
                array(), // the second item
                array(), // the third item
                'description' => __( 'Image fields can be sortable. This may be useful when you need to let the user set an order of images.', 'admin-page-framework-loader' ),
            ),     
            array( // Repeatable & Sortable Image Fields
                'field_id'              => 'image_select_field_repeatable_and_sortable',
                'title'                 => __( 'Repeatable & Sortable', 'admin-page-framework-loader' ),
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
      
    }

}