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
class APF_Demo_BuiltinFieldTypes_MISC_Unsave {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'misc';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'unsaving_items';        
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        // Section
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_id'        => $this->sSectionID,
                'title'             => __( 'Unsaving Items', 'admin-page-framework-loader' ),
                'description'       => __( 'These form inputs will not be saved while they will be passed to the validation callback methods.', 'admin-page-framework-loader' ),
            )
        );   

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID        
            array(
                'field_id'          => 'unsaved',
                'title'             => __( 'Unsaved', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'save'              => false,
                'description'       => __( 'By passing <code>false</code> to the <code>save</code> argument, the form will not save the field value.', 'admin-page-framework-loader' ),
                'attributes'        => array(
                    'readonly'  => 'readonly',
                ),
                'value'             => date_i18n( 'j F Y g:i:s', time() ),
            ),
            array(
                'field_id'          => 'saved',
                'title'             => __( 'Saved', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'save'              => true,
                'description'       => __( 'On contrast to the above field, this field value gets saved.', 'admin-page-framework-loader' ),
                'attributes'        => array(
                    'readonly'  => 'readonly',
                ),
                'default'           => date_i18n( 'j F Y g:i:s', time() + 60*60*24 ),
            )
        );              
      
    }

}