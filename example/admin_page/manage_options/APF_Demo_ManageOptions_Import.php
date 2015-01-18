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
 * Adds a tab of the set page to the loader plugin.
 * 
 * @since       3.5.0    
 */
class APF_Demo_ManageOptions_Import {

    public function __construct( $oFactory, $sPageSlug, $sTabSlug ) {
    
        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug; 
        $this->sTabSlug     = $sTabSlug;
        $this->sSectionID   = $this->sTabSlug;
        
        $this->_addTab();
    
    }
    
    private function _addTab() {
        
        $this->oFactory->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Import', 'admin-page-framework-loader' ),
            )
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );
  
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );

        $oAdminPage->addSettingSections(     
            $this->sPageSlug,
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Import Data', 'admin-page-framework-demo' ),
            )
        );     
        $oAdminPage->addSettingFields(     
            $this->sSectionID,
            array(
                'field_id'      => 'import_format_type',     
                'title'         => __( 'Import Format Type', 'admin-page-framework-demo' ),
                'type'          => 'radio',
                'description'   => __( 'The text format type will not set the option values properly. However, you can see that the text contents are directly saved in the database.', 'admin-page-framework-demo' ),
                'label'         => array( 
                    'json'  => __( 'JSON', 'admin-page-framework-demo' ),
                    'array' => __( 'Serialized Array', 'admin-page-framework-demo' ),
                    'text'  => __( 'Text', 'admin-page-framework-demo' ),
                ),
                'default'       => 'json',
            ),
            array( // Single Import Button
                'field_id'      => 'import_single',
                'title'         => __( 'Single Import Field', 'admin-page-framework-demo' ),
                'type'          => 'import',
                'description'   => __( 'Upload the saved option data.', 'admin-page-framework-demo' ),
                'label'         => __( 'Import Options', 'admin-page-framework-demo' ),
            )
        );             
        
        // import_format_{page slug}_{tab slug}
        add_filter( "import_format_{$this->sPageSlug}_{$this->sTabSlug}", array( $this, 'replyToModifyFormat' ), 10, 2 );
        
        // import_{instantiated class name}_{import section id}_{import field id}
        add_filter( "import_{$this->oFactory->oProp->sClassName}_{$this->sSectionID}_import_single", array( $this, 'replyToModifyImportData' ), 10, 6 );
        
    }
    
    public function replyToDoTab() {}
 
    /**
     * 
     * @remark      import_format_{page slug}_{tab slug}
     */
    public function replyToModifyFormat( $sFormatType, $sFieldID ) { 
        
        return isset( $_POST[ $this->oFactory->oProp->sOptionKey ][ $this->sSectionID ]['import_format_type'] ) 
            ? $_POST[ $this->oFactory->oProp->sOptionKey ][ $this->sSectionID ]['import_format_type']
            : $sFormatType;
        
    }
    /**
     * 
     * @remark      import_{instantiated class name}_{import section id}_{import field id}
     */    
    public function replyToModifyImportData( $vData, $aOldOptions, $sFieldID, $sInputID, $sImportFormat, $sOptionKey ) { 

        if ( 'text' === $sImportFormat ) {
            $this->oFactory->setSettingNotice( 
                __( 'The text import type is not supported.', 'admin-page-framework-demo' )
            );
            return $aOldOptions;
        }
        
        $this->oFactory->setSettingNotice( 
            __( 'Importing options were validated.', 'admin-page-framework-demo' ), 
            'updated' 
        );
        return $vData;
        
    }
 
}