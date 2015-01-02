<?php 
// 3.4.5.1+ The minified verion was removed.
trigger_error( 
    'Admin Page Framework: The minified script is temporarily deprecated due to an issue with wordpress.org. Use the minifier via Dashboard -> Admin Page Framework -> Tool -< Minified Version and place the file in your convenient location.',
    E_USER_WARNING  // E_USER_NOTICEs
);
include_once( dirname( dirname( __FILE__ ) ) . '/development/admin-page-framework.php' );
	