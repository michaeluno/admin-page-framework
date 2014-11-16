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

class APF_Demo_Readme extends AdminPageFramework {

    /**
     * Sets up pages.
     * 
     * This method automatically gets triggered with the wp_loaded hook. 
     */
    public function setUp() {

        /* ( optional ) this can be set via the constructor. For available values, see https://codex.wordpress.org/Roles_and_Capabilities */
        $this->setCapability( 'read' );
        
        /* ( required ) Set the root page */
        $this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );    
        
        /* ( required ) Add sub-menu items (pages or links) */
        $this->addSubMenuItems(     
            array(
                'title'                 => __( 'Read Me', 'admin-page-framework-demo' ),    // the page title
                'menu_title'            => __( 'About', 'admin-page-framework' ),           // (optional) to make it different from the page title.
                'page_slug'             => 'apf_read_me',
                'screen_icon'           => 'page',
            ),     
            array(
                'title'                 => __( 'Documentation', 'admin-page-framework-demo' ),
                'href'                  => plugins_url( 'document/package-AdminPageFramework.html', APFDEMO_FILE ),
                'show_page_heading_tab' => false,
            ),            
            array()
        );

        /* ( optional ) Disable the automatic settings link in the plugin listing table. */    
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string.
        
    }
    
    /**
     * The pre-defined callback method triggered when one of the added pages loads
     */
    public function load_APF_Demo_Readme( $oAdminPage ) { // load_{instantiated class name}
    
        /* ( optional ) Determine the page style */
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs     
        $this->setPageTitleVisibility( false, 'apf_read_me' ); // disable the page title of a specific page.
        // $this->setInPageTabsVisibility( false, 'apf_read_me' ); // in-page tabs can be disabled like so.    
    
        /* 
         * ( optional ) Enqueue styles  
         * $this->enqueueStyle(  'stylesheet url/path' , 'page slug (optional)', 'tab slug (optional)', 'custom argument array(optional)' );
         * */
        $_sStyleHandle = $this->enqueueStyle( plugins_url( 'asset/css/readme.css' , APFDEMO_FILE ) , 'apf_read_me' ); // a url can be used as well    
    
        /*
         * ( optional )Enqueue scripts
         * $this->enqueueScript(  'script url/path' , 'page slug (optional)', 'tab slug (optional)', 'custom argument array(optional)' );
         */
        $this->enqueueScript(  
            plugins_url( 'asset/js/test.js' , APFDEMO_FILE ), // source url or path
            'apf_read_me',     // page slug
            '',     // tab slug
            array(
                'handle_id' => 'my_script', // this handle ID also is used as the object name for the translation array below.
                'translation' => array( 
                    'a' => 'hello world!',
                    'style_handle_id' => $_sStyleHandle, // check the enqueued style handle ID here.
                ),
            )
        );     

    }
    
    /**
     * Do page specific settings.
     */
    public function load_apf_read_me() {
        
        $this->addInPageTabs( // ( optional )
            'apf_read_me',
            array(
                'tab_slug'  => 'description',
                'title'     => __( 'Description', 'admin-page-framework-demo' ),
            ),     
            array(
                'tab_slug'  => 'installation',
                'title'     => __( 'Installation', 'admin-page-framework-demo' ),
            ),    
            array(
                'tab_slug'  => 'frequently_asked_questions',
                'title'     => __( 'FAQ', 'admin-page-framework-demo' ),
            ),     
            array(
                'tab_slug'  => 'other_notes',
                'title'     => __( 'Other Notes', 'admin-page-framework-demo' ),
            ),     
            array(
                'tab_slug'  => 'changelog',
                'title'     => __( 'Change Log', 'admin-page-framework-demo' ),
            )
        );     
                            
    }
        
    public function do_before_apf_read_me() { // do_before_ + page slug 

        include( dirname( APFDEMO_FILE ) . '/third-party/wordpress-plugin-readme-parser/parse-readme.php' );
        $this->oWPReadMe = new WordPress_Readme_Parser;
        $this->aWPReadMe = $this->oWPReadMe->parse_readme( dirname( APFDEMO_FILE ) . '/readme.txt' );
    
    }
    public function do_apf_read_me_description() { // do_ + page slug + _ + tab slug
        
        echo $this->aWPReadMe['sections']['description'];
        
    }
    public function do_apf_read_me_installation() { // do_ + page slug + _ + tab slug
        
        echo $this->aWPReadMe['sections']['installation'];
        
    }
    public function do_apf_read_me_frequently_asked_questions() { // do_ + page slug + _ + tab slug
        
        echo $this->aWPReadMe['sections']['frequently_asked_questions'];
        
    }
    public function do_apf_read_me_other_notes() {
        
        echo $this->aWPReadMe['remaining_content'];
        
    }
    public function do_apf_read_me_screenshots() { // do_ + page slug + _ + tab slug
        
        echo $this->aWPReadMe['sections']['screenshots'];
        
    }    
    public function do_apf_read_me_changelog() { // do_ + page slug + _ + tab slug
        
        echo $this->aWPReadMe['sections']['changelog'];
        
        $_aChangeLog = $this->oWPReadMe->parse_readme( dirname( APFDEMO_FILE ) . '/changelog.md' );
        echo $_aChangeLog['sections']['changelog'];
    
    }        

}