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
                'title'        => __( 'Contact', 'admin-page-framework-demo' ),
                'page_slug'    => 'apf_contact',
                'screen_icon'  => 'page',
            )
        );

        /* ( optional ) Disable the automatic settings link in the plugin listing table. */    
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string.
        
    }
    
    /**
     * The pre-defined callback method triggered when one of the added pages loads
     */
    public function load_APF_Demo_Contact( $oAdminPage ) { // load_{instantiated class name}

        /* ( optional ) Determine the page style */
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs     
        $this->setPageTitleVisibility( false, 'apf_contact' ); // disable the page title of a specific page.

    }
    
    /**
     * Do page specific settings.
     */
    public function load_apf_contact() {    // load_ + {page slug}
    
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
    
    public function load_apf_contact_contact() {    // load_ + {page slug} + _  + {tab slug}
      
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
                'title'             => __( 'Your Name', 'admin-page-framework-demo' ),
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
                'field_id'          => 'from',
                'title'             => __( 'Your Email Address', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'default'           => $_oCurrentUser->user_email,
                'attributes'        => array(
                    'required'      => 'required',
                    'placeholder'   =>  __( 'Type your email here.', 'admin-page-framework-demo' ),
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
                    'placeholder'   =>  __( 'Type the title here.', 'admin-page-framework-demo' ),
                ),
            ),    
            array( 
                'field_id'          => 'body',
                'title'             => __( 'Message', 'admin-page-framework-demo' ),
                'type'              => 'textarea',
                'rich'              => true,
                'attributes'        => array(
                    'placeholder'   =>  __( 'Type the message here.', 'admin-page-framework-demo' ),
                ),
            ),            
            array( 
                'field_id'          => 'ip',
                'type'              => 'hidden',
                'value'             => $_SERVER["REMOTE_ADDR"],
            ),                        
            array( 
                'field_id'          => 'send',
                'type'              => 'submit',
                'label_min_width'   => 0,
                'attributes'        => array(
                    'field' => array(
                        'style' => 'float:right; clear:none; display: inline;',
                    ),
                ),    
                'email'             => array(
                    // Each argument can accept a string or an array representing the dimensional array key.
                    // For example, if there is a field for the email title, and its section id is 'my_section'  and  the field id is 'my_field', pass an array, array( 'my_section', 'my_field' )
                    'to'          => 'admin-page-framework@michaeluno.jp',
                    'subject'     => array( 'contact', 'subject' ),
                    'message'     => array( 'contact' ), // the section name enclosed in an array. If it is a field, set it to the second element like array( 'seciton id', 'field id' ).
                    'headers'     => '',
                    'attachments' => '',    // the file path
                    'is_html'     => true,  // boolean  Whether the mail should be sent as an html text
                    'from'        => array( 'contact', 'from' ),
                    'name'        => array( 'contact', 'name' ),
                ),                
            ),     
            array()    
        );
        
    }
    
    public function load_apf_contact_report() {
  
        $_oCurrentUser = wp_get_current_user();
  
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
                'field_id'          => 'name',
                'title'             => __( 'Your Name', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'default'           => $_oCurrentUser->user_firstname || $_oCurrentUser->user_firstname 
                    ? $_oCurrentUser->user_lastname . ' ' .  $_oCurrentUser->user_lastname 
                    : '',
                'attributes'        => array(
                    'required'      => 'required',
                    'placeholder'   => __( 'Type your name.', 'admin-page-framewrok-demo' ),
                ),
            ),    
            array( 
                'field_id'          => 'from',
                'title'             => __( 'Your Email Address', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'default'           => $_oCurrentUser->user_email,
                'attributes'        => array(
                    'required'      => 'required',
                    'placeholder'   =>  __( 'Type your email that the developer replies backt to.', 'admin-page-framework-demo' )
                ),
            ),                
            array( 
                'field_id'          => 'expected_result',
                'title'             => __( 'Expected Behavior', 'admin-page-framework-demo' ),
                'type'              => 'textarea',
                'description'       => __( 'Tell how the framework should work.', 'admin-page-framework-demo' ),
                'attributes'        => array(
                    'required'  => 'required',
                ),
            ),  
            array( 
                'field_id'          => 'actual_result',
                'title'             => __( 'Actual Behavior', 'admin-page-framework-demo' ),
                'type'              => 'textarea',
                'description'      => __( 'Describe the behavior of the framework.', 'admin-page-framework-demo' ),
                'attributes'        => array(
                    'required'  => 'required',
                ),                
            ),     
            array(
                'field_id'      => 'system_information',
                'type'          => 'system',     
                'title'         => __( 'System Information', 'admin-page-framework-demo' ),
                'data'          => array(
                    __( 'Custom Data', 'admin-page-framework-demo' )    => __( 'This is custom data inserted with the data argument.', 'admin-page-framework-demo' ),
                    __( 'Current Time', 'admin-page-framework' )        => '', // Removes the Current Time Section.
                ),
                'attributes'    => array(
                    'rows'          =>  10,
                ),
            ),
            array(
                'field_id'      => 'saved_options',
                'type'          => 'system',     
                'title'         => __( 'Saved Options', 'admin-page-framework-demo' ),
                'data'          => array(
                    // Removes the default data by passing an empty value below.
                    'Admin Page Framework'  => '', 
                    'WordPress'             => '', 
                    'PHP'                   => '', 
                    'MySQL'                 => '', 
                    'Server'                => '',
                ) 
                + get_option( 'APF_Demo' ), // the stored options of the main demo class
                'attributes'    => array(
                    'rows'          =>  10,
                ),
            ),               
            array( 
                'field_id'          => 'send',
                'type'              => 'submit',
                'label_min_width'   => 0,
                'attributes'        => array(
                    'field' => array(
                        'style' => 'float:right; clear:none; display: inline;',
                    ),
                ),    
                'email'             => array(
                    // Each argument can accept a string or an array representing the dimensional array key.
                    // For example, if there is a field for the email title, and its section id is 'my_section'  and  the field id is 'my_field', pass an array, array( 'my_section', 'my_field' )
                    'to'            => 'admin-page-framework@michaeluno.jp',
                    'subject'       => 'Reporting Issue',
                    'message'       => array( 'report' ), // the section name enclosed in an array. If it is a field, set it to the second element like array( 'seciton id', 'field id' ).
                    'headers'       => '',
                    'attachments'   => '', // the file path
                    'name'          => '', // The email sender name. If the 'name' argument is empty, the field named 'name' in this section will be applied
                    'from'          => '', // The sender email address. If the 'from' argument is empty, the field named 'from' in this section will be applied.
                    // 'is_html'       => true,
                ),
            ),     
            array()
        );
        
    }
    
}
