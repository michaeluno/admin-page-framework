<?php

// Create a custom post type - this class deals with front-end components so checking with is_admin() is not necessary.
include( APFDEMO_DIRNAME . '/example/post_type/load_post_types.php' );

// Create widgets - this class also deals with front-end components so no need to check with is_admin().
include( APFDEMO_DIRNAME . '/example/widget/load_widgets.php' );

// Create admin pages.
if ( is_admin() ) :

    // Create an example page group and add sub-pages including a page with the slug 'apf_first_page'.
    include( APFDEMO_DIRNAME . '/example/basic_usage/load_basic_usage.php' ); // Include the basic usage example that creates a root page and its sub-pages.

    // Add pages and forms in the custom post type root page.
    include( APFDEMO_DIRNAME . '/example/admin_page/load_demo_pages.php' );
                
    if ( is_network_admin() ) {
        
        include( APFDEMO_DIRNAME . '/example/network_admin/load_network_admin_pages.php' );
                
    }
    
endif;