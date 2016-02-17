<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a section in a tab.
 * 
 * @package     AdminPageFramework
 * @subpackage  Example
 */
class APF_Demo_BuiltinFieldTypes_File_Media {
    
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
    public $sSectionID  = 'media_upload';
        
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
                'title'         => __( 'Media Uploader', 'admin-page-framework-loader' ),
                'tip'           => __( 'If <code>repeatable</code> is set to <code>true</code>, you can select multiple items in the pop up media uploader.', 'admin-page-framework-loader' ),
                'description'   => __( 'Upload binary files in addition to images.', 'admin-page-framework-loader' ),
            )
        );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID,
            array( // Media File
                'field_id'              => 'media_field',
                'title'                 => __( 'Media File', 'admin-page-framework-loader' ),
                'type'                  => 'media',
                'allow_external_source' => false,
            ),
            array( // Media File with Attributes
                'field_id'              => 'media_with_attributes',
                'title'                 => __( 'Media File with Attributes', 'admin-page-framework-loader' ),
                'type'                  => 'media',
                'attributes_to_store'   => array( 'id', 'caption', 'description' ),
                'attributes'            => array(
                    'button'        => array(
                        'data-label' => __( 'Select File', 'admin-page-framework-loader' ),
                    ),
                    'remove_button' => array(      // 3.2.0+
                        'data-label' => __( 'Remove', 'admin-page-framework-loader' ), // will set the Remove button label instead of the dashicon
                    ),
                ),
            ),
            array( // Repeatable Media Files
                'field_id'              => 'media_repeatable_fields',
                'title'                 => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'                  => 'media',
                'repeatable'            => true,
            ),
            array( // Sortable Media Files
                'field_id'              => 'media_sortable_fields',
                'title'                 => __( 'Sortable', 'admin-page-framework-loader' ),
                'type'                  => 'media',
                'sortable'              => true,
                array(), // the second item
                array(), // the third item.
            )
        );
        
    }

}
