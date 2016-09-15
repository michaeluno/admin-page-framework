<?php
/**
 * Admin Page Framework Loader
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a tab of the set page to the loader plugin.
 * 
 * @since       3.8.4
 * @version     1.0.0
 */
class APF_Demo_CustomFieldType_Path {

    public $oFactory;
    
    public $sClassName;
    
    public $sPageSlug;
    
    public $sTabSlug = 'path';

    public function __construct( $oFactory, $sPageSlug ) {
    
        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug; 
        $this->sSectionID   = $this->sTabSlug;
                        
        $this->oFactory->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Path', 'admin-page-framework-loader' ),
            )
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );
  
    }
    
    /**
     * Triggered when the tab starts loading.
     * 
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        $this->registerFieldTypes( $this->sClassName );
        
        add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );
        
         // Section
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'File/Directory Path Selector', 'admin-page-framework-loader' ),
                'description'   => array( 
                    __( 'This field type lets the user select a file/directory path.', 'admin-page-framework-loader' )
                    . ' ' 
                    . __( 'The relative path to the value of <code>$_SERVER[ "DOCUMENT_ROOT" ]</code> (the document root set by the web server) will be set.', 'admin-page-framework-loader' ),
                ),
            )            
        );        
                    
        // Fields   
        $oAdminPage->addSettingFields(
            $this->sSectionID,
            array(
                'field_id'      => 'path_field',
                'type'          => 'path',
                'title'         => __( 'Path', 'admin-page-framework-loader' ),
                // @see For the list of arguments, refer to https://github.com/jqueryfiletree/jqueryfiletree#configuring-the-file-tree
                'options'       => array(
                    'root'  => ABSPATH,
                    'fileExtensions'    => 'php,txt',
                ),
                'descriptions'   => array(
                    __( 'With the `fileExtensions` option, listed file types can be specified.', 'admin-page-framework-loader' ),
                ),
            ),
            array(
                'field_id'      => 'path_field_repeatable',
                'type'          => 'path',
                'title'         => __( 'Repeatable', 'admin-page-framework-loader' ),
                'repeatable' => true,
            )    
        );  
 
    }
    
        /**
         * Registers the field types.
         */
        private function registerFieldTypes( $sClassName ) {
            new PathCustomFieldType( $sClassName );                             
        }    
            
    
    public function replyToDoTab() {        
        submit_button();
    }
    
}
