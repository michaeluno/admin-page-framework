<?php
/**
 * Adds the Contact page to the demo plugin.
 * 
 * @since   3.2.2
 */
class APF_Demo_Contact extends AdminPageFramework {

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
                'title'                 => __( 'Contact', 'admin-page-framework-demo' ),
                'page_slug'             => 'apf_contact',
                'screen_icon'           => 'page',
            )
        );

        /* ( optional ) Disable the automatic settings link in the plugin listing table. */    
        // $this->setPluginSettingsLinkLabel( '' ); // pass an empty string.
        
    }
    
    /**
     * The pre-defined callback method triggered when one of the added pages loads
     */
    public function load_APF_Demo_Contact( $oAdminPage ) { // load_{instantiated class name}
    
        /* ( optional ) Determine the page style */
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs     
        $this->setPageTitleVisibility( false, 'apf_contact' ); // disable the page title of a specific page.
        // $this->setInPageTabsVisibility( false, 'apf_read_me' ); // in-page tabs can be disabled like so.    
    
        /* 
         * ( optional ) Enqueue styles  
         * $this->enqueueStyle(  'stylesheet url/path' , 'page slug (optional)', 'tab slug (optional)', 'custom argument array(optional)' );
         * */
        // $_sStyleHandle = $this->enqueueStyle( plugins_url( 'asset/css/readme.css' , APFDEMO_FILE ) , 'apf_read_me' ); // a url can be used as well    
    
        /*
         * ( optional )Enqueue scripts
         * $this->enqueueScript(  'script url/path' , 'page slug (optional)', 'tab slug (optional)', 'custom argument array(optional)' );
         */
        // $this->enqueueScript(  
            // plugins_url( 'asset/js/test.js' , APFDEMO_FILE ), // source url or path
            // 'apf_read_me',     // page slug
            // '',     // tab slug
            // array(
                // 'handle_id' => 'my_script', // this handle ID also is used as the object name for the translation array below.
                // 'translation' => array( 
                    // 'a' => 'hello world!',
                    // 'style_handle_id' => $_sStyleHandle, // check the enqueued style handle ID here.
                // ),
            // )
        // );     

    }
    
    /**
     * Do page specific settings.
     */
    public function load_apf_contact() {
        
        // ( optional )
        $this->addInPageTabs( 
            'apf_contact',  // the target page slug
            array(
                'tab_slug'  => 'contact',
                'title'     => __( 'Contact', 'admin-page-framework-demo' ),
            ),     
            array(
                'tab_slug'  => 'report',
                'title'     => __( 'Report Issues', 'admin-page-framework-demo' ),
            )
        );     
                            
    }
    
    public function load_apf_contact_contact() {
        
        $_oCurrentUser = wp_get_current_user();
        
        /* Add setting sections */
        $this->addSettingSections(    
            'apf_contact', // the target page slug
            array(
                'section_id'        => 'contact', // avoid hyphen(dash), dots, and white spaces
                'tab_slug'          => 'contact',
                'title'             => __( 'Contact', 'admin-page-framework-demo' ),
                'description'       => __( 'Tell the developer how you are using the framework.', 'admin-page-framework-demo' ), 
            )
        );
        $this->addSettingFields(
            'contact',
            array( 
                'field_id'          => 'name',
                'title'             => __( 'Name', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'default'           => $_oCurrentUser->user_firstname || $_oCurrentUser->user_firstname 
                    ? $_oCurrentUser->user_lastname . ' ' .  $_oCurrentUser->user_lastname 
                    : '',
                'attributes'        => array(
                    'required' => 'required',
                    'placeholder'   => __( 'Type your name.', 'admin-page-framewrok-demo' ),
                ),
            ),    
            array( 
                'field_id'          => 'email',
                'title'             => __( 'Email Address.', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'default'           => $_oCurrentUser->user_email,
                'attributes'        => array(
                    'required'      => 'required',
                    'placeholder'   =>  __( 'Type your email here.' )
                ),
            ),     
            array( 
                'field_id'          => 'use_for_commercial_products',
                'title'             => __( 'I use the framework for', 'admin-page-framework-demo' ),
                'type'              => 'radio',
                'default'           => 1,
                'label'             => array(
                    1       => __( 'Commercial Products', 'admin-page-framework-demo' ),
                    0       => __( 'Non-commercial Products', 'admin-page-framework-demo' ),
                ),
            ),              
            array( 
                'field_id'          => 'use_for',
                'title'             => __( 'I use the framework for', 'admin-page-framework-demo' ),
                'type'              => 'radio',
                'default'           => 'others',
                'label'             => array(
                    'plugins'   => __( 'Plugins', 'admin-page-framework-demo' ),
                    'themes'    => __( 'Themes', 'admin-page-framework-demo' ),
                    'others'    => __( 'Others', 'admin-page-framework-demo' ),
                ),
            ),                    
            array( 
                'field_id'          => 'subject',
                'title'             => __( 'Subject', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'attributes'        => array(
                    'size' => 40,
                    'placeholder'   =>  __( 'Type the title here.' )
                ),
            ),    
            array( 
                'field_id'          => 'body',
                'title'             => __( 'Message', 'admin-page-framework-demo' ),
                'type'              => 'textarea',
                'rich'              => true,
                'attributes'        => array(
                    // 'size' => 20,
                    'placeholder'   =>  __( 'Type the message here.' )
                ),
            ),                 
            array( 
                'field_id'          => 'send',
                'type'              => 'submit',
                'attributes'        => array(
                    'field' => array(
                        'style' => 'float:right; clear:none; display: inline;',
                    ),
                ),    
            ),     
            array()    
        );
        
    }
    
    public function load_apf_contact_report() {
        
        $this->addSettingSections(
            'apf_contact',  // the target page slug
            array(
                'section_id'        => 'report',
                'tab_slug'          => 'report',
                'title'             => __( 'Report Issues', 'admin-page-framework-demo' ),
                'description'       => __( 'If you find a bug, you can report it from here.', 'admin-page-framework-demo' ),
            )
        );
        $this->addSettingFields(
            'report',
            array( 
                'field_id'          => 'expected_result',
                'title'             => __( 'Expected Result', 'admin-page-framework-demo' ),
                'type'              => 'textarea',
                'rich'              => true,
                'description'      => __( 'Tell how the framework should work.', 'admin-page-framework-demo' ),
            ),  
            array( 
                'field_id'          => 'actual_result',
                'title'             => __( 'Actual Result', 'admin-page-framework-demo' ),
                'type'              => 'textarea',
                'rich'              => true,
                'description'      => __( 'Describe the behavior of the framework.', 'admin-page-framework-demo' ),
            ),                 
            array( 
                'field_id'          => 'send',
                'type'              => 'submit',
                'attributes'        => array(
                    'field' => array(
                        'style' => 'float:right; clear:none; display: inline;',
                    ),
                ),    
                'email'             => array(
                    'to'          => 'contact.michaeluno.jp@gmail.com',
                    'subject'     => 'Reporting Issue',
                    'message'     => '',
                    'headers'     => '',
                    'attachments' => '',
                ),
            ),     
            array()
        );
        
    }
    
}
