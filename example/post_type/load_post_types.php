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

include( dirname( __FILE__ ) . '/APF_PostType.php' );
new APF_PostType( 
    'apf_posts',                // the post type slug
    null,                       // the argument array. Here null is passed because it is defined inside the class.
    APFDEMO_FILE,               // the caller script path.
    'admin-page-framework-demo' // the text domain.
);     

if ( is_admin() ) :

    // Create meta boxes with form fields that appear in post definition pages (where you create a post) of the given post type.
    include( dirname( __FILE__ ) . '/post_type_meta_box/load_post_type_meta_boxes.php' );    

    // Add fields in the taxonomy page
    include( dirname( __FILE__ ) . '/taxonomy/load_taxonomies.php' );    
    
endif;
