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
class APF_Demo_CustomFieldType_ACE {

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
                'title'         => __( 'ACE', 'admin-page-framework-loader' ),
            )
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );
  
    }
    
    /**
     * Triggered when the tab is loaded.
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
                'title'         => __( 'ACE Code Editors', 'admin-page-framework-loader' ),
                'description'   => array( 
                    sprintf( 
                        __( 'This field type uses the external script located at %1$s.', 'admin-page-framework-loader' ),
                        ( is_ssl() ? 'https:' : 'http:' ) . '//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ace.js'
                    ),
                    sprintf( 
                        __( 'For more information about this field type, see <a href="%1$s" target="_blank">this page</a>', 'addmin-page-framework-demo' ), 
                        'https://github.com/soderlind/AceCustomFieldType'
                    )                    
                ),
            )
        );        
        
        // Fields
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section id
            array(
                'field_id'      => 'ace_css',
                'type'          => 'ace',     
                'title'         => __( 'CSS', 'admin-page-framework-loader' ),
                'default'       => '.abc { color: #fff; }',
                'attributes'    =>  array(
                    'cols'        => 60,
                    'rows'        => 4,
                ),                
                'options'   => array(
                    'language'              => 'css',
                    'theme'                 => 'chrome',
                    'gutter'                => false,
                    'readonly'              => false,
                    'fontsize'              => 12,
                ),                
            ),
            array(
                'field_id'      => 'ace_php',
                'type'          => 'ace',     
                'title'         => __( 'PHP', 'admin-page-framework-loader' ),
                'default'       => 'echo "hello world!";',
                'attributes'    =>  array(
                    'cols'        => 60,
                    'rows'        => 4,
                ),                
                'options'   => array(
                    'language'              => 'php',
                ),           
                'repeatable'    => true,
            )
        );             
        
    }
    
        /**
         * Registers the field types.
         */
        private function registerFieldTypes( $sClassName ) {
            new AceCustomFieldType( $sClassName );                             
            
        }    
            
    
    public function replyToDoTab() {        
        submit_button();
    }
    
}