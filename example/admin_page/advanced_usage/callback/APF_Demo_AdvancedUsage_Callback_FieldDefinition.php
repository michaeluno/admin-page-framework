<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a section in a tab.
 * 
 * @package     AdminPageFramework/Example
 */
class APF_Demo_AdvancedUsage_Callback_FieldDefinition {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_advanced_usage';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'callbacks';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'field_definitions';        
        
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
                'title'             => __( 'Using Callbacks', 'admin-page-framework-loader' ),
                'description'       => __( 'These fields are (re)defined with callbacks.', 'admin-page-framework-loader' ),
            )
        );   
        
        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID        
            array(
                'field_id'          => 'callback_example',
                'type'              => 'select',
            ),
            array(
                'field_id'          => 'apf_post_titles',
                'type'              => 'checkbox',
                'label_min_width'   => '100%',
            )          
        );              
        
        add_filter( 
            'field_definition_' . $oFactory->oProp->sClassName . '_' . $this->sSectionID . '_callback_example', 
            array( $this, 'replyToRedefineExampleField' )
        );
            
        add_filter( 
            'field_definition_' . $oFactory->oProp->sClassName . '_' . $this->sSectionID . '_apf_post_titles', 
            array( $this, 'replyToRedefinePostTitleField' )
        );
        
    } 

    /**
     * Field callback methods - for field definitions that require heavy tasks should be defined with the callback methods.
     *
     * @callback    filter      field_definition_{instantiated class name}_{section id}_{field_id}
     * @return      array
     */
    public function replyToRedefineExampleField( $aField ) { 
        
        $aField[ 'title' ]        = __( 'Post Titles', 'admin-page-framework-loader' );
        $aField[ 'description' ]  = sprintf( 
            __( 'This description is inserted with the filter, named: <code>%1$s</code>.', 'admin-page-framework-loader' ), 
            current_filter() 
        );
        $aField[ 'label' ]        = $this->_getPostTitles();
        return $aField;
        
    }
    
    /**
     * Field callback methods - for field definitions that require heavy tasks should be defined with the callback methods.
     *
     * @callback    filter      field_definition_{instantiated class name}_{section id}_{field_id}
     * @return      array
     */    
    public function replyToRedefinePostTitleField( $aField ) { // field_definition_{instantiated class name}_{section id}_{field_id}
        
        $aField[ 'title' ] = __( 'APF Custom Post Titles', 'admin-page-framework-loader' );
        $aField[ 'label' ] = $this->_getPostTitles( AdminPageFrameworkLoader_Registry::$aPostTypes[ 'demo' ] );
        return $aField;
        
    }    
        /**
         * @return      array
         */
        private function _getPostTitles( $sPostTypeSlug='post' ) {
            
            $_aArgs         = array(
                'post_type' => $sPostTypeSlug,
            );
            $_oResults      = new WP_Query( $_aArgs );
            $_aPostTitles   = array();
            foreach( $_oResults->posts as $_iIndex => $_oPost ) {
                $_aPostTitles[ $_oPost->ID ] = $_oPost->post_title;
            }
            return $_aPostTitles;
            
        }    
    
}
