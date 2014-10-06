<?php

// Create a custom post type - this class deals with front-end components so checking with is_admin() is not necessary.
include( APFDEMO_DIRNAME . '/example/APF_PostType.php' );
new APF_PostType( 
    'apf_posts',                // the post type slug
    null,                       // the argument array. Here null is passed because it is defined inside the class.
    APFDEMO_FILE,               // the caller script path.
    'admin-page-framework-demo' // the text domain.
);     

// Create widgets - this class also deals with front-end components so no need to check with is_admin().
include( APFDEMO_DIRNAME . '/example/APF_Widget.php' );
new APF_Widget( __( 'Admin Page Framework', 'admin-page-framework-demo' ) );  // the widget title
include( APFDEMO_DIRNAME . '/example/APF_Widget_CustomFieldTypes.php' );
new APF_Widget_CustomFieldTypes( __( 'APF - Advanced', 'admin-page-framework-demo' ) );
include( APFDEMO_DIRNAME . '/example/APF_Widget_Example.php' );
new APF_Widget_Example( __( 'APF - GitHub Button', 'admin-page-framework-demo' ) );

// Create admin pages.
if ( is_admin() ) :

    // Create meta boxes with form fields that appear in post definition pages (where you create a post) of the given post type.
    include( APFDEMO_DIRNAME . '/example/APF_MetaBox_BuiltinFieldTypes.php' );    
    new APF_MetaBox_BuiltinFieldTypes(
        'sample_custom_meta_box',                           // meta box ID
        __( 'Demo Meta Box with Built-in Field Types', 'admin-page-framework-demo' ), // title
        array( 'apf_posts' ),                               // post type slugs: post, page, etc.
        'normal',                                           // context (what kind of metabox this is)
        'default'                                           // priority
    );
    
    include( APFDEMO_DIRNAME . '/example/APF_MetaBox_CustomFieldTypes.php' );    
    new APF_MetaBox_CustomFieldTypes(
        'sample_custom_meta_box_with_custom_field_types',   // meta box ID
        __( 'Demo Meta Box with Custom Field Types', 'admin-page-framework-demo' ), // title
        array( 'apf_posts' ),                               // post type slugs: post, page, etc.
        'normal',                                           // context
        'default'                                           // priority
    ); 
    
    include( APFDEMO_DIRNAME . '/example/APF_MetaBox_DateFields.php' );
    new APF_MetaBox_DateFields(
        'sample_metabox_data_fields',
        __( 'Custom Data Fields', 'admin-page-framework-demo' ),
        array( 'apf_posts' ),                             
        'side'                                
    );
    
    // Add fields in the taxonomy page
    include( APFDEMO_DIRNAME . '/example/APF_TaxonomyField.php' );
    new APF_TaxonomyField( 'apf_sample_taxonomy' ); // taxonomy slug

    // Create an example page group and add sub-pages including a page with the slug 'apf_first_page'.
    include( APFDEMO_DIRNAME . '/example/APF_BasicUsage.php' ); // Include the basic usage example that creates a root page and its sub-pages.
    new APF_BasicUsage(
        null,                       // the option key - when null is passed the class name in this case 'APF_BasicUsage' will be used
        APFDEMO_FILE,               // the caller script path.
        'manage_options',           // the default capability
        'admin-page-framework-demo' // the text domain    
    );

        // Create meta boxes that belongs to the 'apf_first_page' page.
        include( APFDEMO_DIRNAME . '/example/APF_MetaBox_For_Pages_Normal.php' );
        new APF_MetaBox_For_Pages_Normal(
            'apf_metabox_for_pages_normal',                 // meta box id
            __( 'Sample Meta Box for Admin Pages Inserted in Normal Area', 'admin-page-framework-demo' ), // title
            'apf_first_page',                               // page slugs
            'normal',                                       // context
            'default'                                       // priority
        );
        include( APFDEMO_DIRNAME . '/example/APF_MetaBox_For_Pages_Advanced.php' );
        new APF_MetaBox_For_Pages_Advanced(    
            'apf_metabox_for_pages_advanced',               // meta box id
            __( 'Sample Meta Box for Admin Pages Inserted in Advanced Area', 'admin-page-framework-demo' ), // title
            'apf_first_page',                               // page slugs
            'advanced',                                     // context
            'default'                                       // priority
        );    
        include( APFDEMO_DIRNAME . '/example/APF_MetaBox_For_Pages_Side.php' );    
        new APF_MetaBox_For_Pages_Side(    
            'apf_metabox_for_pages_side',                   // meta box id
            __( 'Sample Meta Box for Admin Pages Inserted in Advanced Area', 'admin-page-framework-demo' ), // title
            array( 'apf_first_page', 'apf_second_page' ),   // page slugs - setting multiple slugs is possible
            'side',                                         // context
            'default'                                       // priority
        );       
    
    
    // Add pages and forms in the custom post type root page.
    include( APFDEMO_DIRNAME . '/example/APF_Demo.php' );
    new APF_Demo( 
        null,                       // the option key - when null is passed the class name in this case 'APF_Demo' will be used
        APFDEMO_FILE,               // the caller script path.
        'manage_options',           // the default capability
        'admin-page-framework-demo' // the text domain
    );

        // Add pages and forms in the custom post type root page
        include( APFDEMO_DIRNAME . '/example/APF_Demo_CustomFieldTypes.php' ); // Include the demo class that creates various forms.
        new APF_Demo_CustomFieldTypes( 
            'APF_Demo',                 // passing the option key used by the main pages.
            APFDEMO_FILE,               // the caller script path.
            'manage_options',           // the default capability
            'admin-page-framework-demo' // the text domain            
        ); 
            
        // Add the Manage Options page.
        include( APFDEMO_DIRNAME . '/example/APF_Demo_ManageOptions.php' );
        new APF_Demo_ManageOptions( 
            'APF_Demo',                 // passing the option key used by the main pages.
            APFDEMO_FILE,               // the caller script path.
            'manage_options',           // the default capability
            'admin-page-framework-demo' // the text domain        
        );
        
        // Add a hidden page. This class does not extend the framework factory class.
        include( APFDEMO_DIRNAME . '/example/APF_Demo_HiddenPage.php' );
        new APF_Demo_HiddenPage;
        
        // Add the readme and the documentation sub-menu items to the above main demo plugin root page.
        include( APFDEMO_DIRNAME . '/example/APF_Demo_Readme.php' );
        new APF_Demo_Readme(
            '',                         // passing an empty string will disable the form data to be saved.
            APFDEMO_FILE,               // the caller script path.
            'read',                     // the default capability
            'admin-page-framework-demo' // the text domain        
        );

        // Add the contact page
        include( APFDEMO_DIRNAME . '/example/APF_Demo_Contact.php' );
        new APF_Demo_Contact(
            '',                         // passing an empty string will disable the form data to be saved.
            APFDEMO_FILE,               // the caller script path.
            'read',                     // the default capability
            'admin-page-framework-demo' // the text domain        
        );
                
        
        // Modify the top part of the pages with a separate script
        include( APFDEMO_DIRNAME . '/example/APF_Demo_AddPluginTitle.php' );
        new APF_Demo_AddPluginTitle;
            
    if ( is_network_admin() ) :
    
        // Add pages and forms in the network admin area.
        include( APFDEMO_DIRNAME . '/example/APF_NetworkAdmin.php' );
        new APF_NetworkAdmin(
            null,                       // passing the option key used by the main pages.
            APFDEMO_FILE,               // the caller script path.
            'manage_options',           // the default capability
            'admin-page-framework-demo' // the text domain        
        );
        
        include( APFDEMO_DIRNAME . '/example/APF_NetworkAdmin_CustomFieldTypes.php' );
        new APF_NetworkAdmin_CustomFieldTypes(
            'APF_NetworkAdmin',
            APFDEMO_FILE,               // the caller script path.
            'manage_options',           // the default capability
            'admin-page-framework-demo' // the text domain                    
        );     
        
        include( APFDEMO_DIRNAME . '/example/APF_NetworkAdmin_ManageOptions.php' );
        new APF_NetworkAdmin_ManageOptions( 
            'APF_NetworkAdmin', 
            APFDEMO_FILE,               // the caller script path.
            'manage_options',           // the default capability
            'admin-page-framework-demo' // the text domain                    
        );
        
        new APF_MetaBox_For_Pages_Side(    
            'apf_metabox_for_pages_side',       // meta box id
            __( 'Sample Meta Box for Admin Pages Inserted in Advanced Area', 'admin-page-framework-demo' ), // title
            array( 'apf_builtin_field_types' ), // page slugs - setting multiple slugs is possible
            'side',                             // context
            'default'                           // priority
        );   
        
    endif;
    
endif;