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

class APF_Widget_Example extends AdminPageFramework_Widget {
    
    /**
     * The user constructor.
     * 
     * Alternatively you may use start_{instantiated class name} method.
     */
    public function start() {}
    
    /**
     * Sets up arguments.
     * 
     * Alternatively you may use set_up_{instantiated class name} method.
     */
    public function setUp() {

        $this->setArguments( 
            array(
                'description' => sprintf( __( 'Displays a GitHub button which perform API calls to %1$s.', 'admin-page-framework-demo' ), 'https://api.github.com/' ),
            ) 
        );
    
    }    

    /**
     * Sets up the form.
     * 
     * Alternatively you may use load_{instantiated class name} method.
     */
    public function load( $oAdminWidget ) {
        
        $this->addSettingFields(
            array(
                'field_id'      => 'title',
                'type'          => 'text',
                'title'         => __( 'Title', 'admin-page-framework-demo' ),
            ),
            array(
                'field_id'      => 'user_name',
                'type'          => 'text',
                'title'         => __( 'GitHub User Name', 'admin-page-framework-demo' ),
                'default'       => 'michaeluno',
            ),
            array(
                'field_id'      => 'repository_name',
                'type'          => 'text',
                'title'         => __( 'Repository Name', 'admin-page-framework-demo' ),
                'default'       => 'Admin Page Framework',
            ),            
            array(
                'field_id'      => 'repository',
                'type'          => 'text',
                'title'         => __( 'GitHub Repository', 'admin-page-framework-demo' ),
                'default'       => 'admin-page-framework',
            ),
            array()
        );        

        
    }
    
    /**
     * Validates the submitted form data.
     * 
     * Alternatively you may use validation_{instantiated class name} method.
     */
    public function validate( $aSubmit, $aStored, $oAdminWidget ) {
        
        // Uncomment the following line to check the submitted value.
        // AdminPageFramework_Debug::log( $aSubmit );
        
        return $aSubmit;
        
    }    
    
    /**
     * Print out the contents in the front-end.
     * 
     * Alternatively you may use the content_{instantiated class name} method.
     */
    public function content( $sContent, $aArguments, $aFormData ) {
        
        $_aAttributes = array(
            'href'              => "https://github.com/" . $aFormData['user_name'] . '/' . $aFormData['repository'],
            'class'             => 'github-button',
            'data-count-href'   => $aFormData['repository'] . '/stargazers',
            'data-count-api'    => '/repos/' . $aFormData['user_name'] . '/' . $aFormData['repository'] . '#stargazers_count',
            'data-style'        => 'mega',
            'data-icon'         => 'octicon-star',
            // 'data-text'         => '',            
        );        
        
        return $sContent
            . "<div class='github-button-container'>"
                . "<a " . AdminPageFramework_WPUtility::generateAttributes( $_aAttributes ) . ">"
                    . $aFormData['repository_name']
                . "</a>"
            . "</div>" 
            . $this->_getScript();
    
    }
        static private $_bInlineScriptLoaded = false;
        
        private function _getScript() {
            
            // Ensure this inline script is rendered only once.
            if ( self::$_bInlineScriptLoaded ) { return ''; }
            self::$_bInlineScriptLoaded = true;
            
            return "<script async defer id='github-bjs' src='" . AdminPageFramework_WPUtility::resolveSRC( APFDEMO_DIRNAME . '/third-party/github-custom-field-type/asset/github-buttons/buttons.js' ) . "'>"
                . "</script>";
        }

}