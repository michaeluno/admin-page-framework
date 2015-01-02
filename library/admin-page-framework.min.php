<?php
// 3.4.5.1+ The minified version was removed.
class AdminPageFramework_DeprecatedWarning_MinifiedVersion {

    public function __construct() {
        add_action( 'admin_notices', array( $this, '_replyToShowWarnings' ) );
    }
    public function _replyToShowWarnings() {
        trigger_error( 
            'Admin Page Framework: The minified script is temporarily deprecated due to an issue with wordpress.org. Use the minifier via Dashboard -> Admin Page Framework -> Tool -> Minifier and place the file in your convenient location.',
            E_USER_WARNING  // E_USER_NOTICEs
        );    
    }
}
new AdminPageFramework_DeprecatedWarning_MinifiedVersion;
include_once( dirname( dirname( __FILE__ ) ) . '/development/admin-page-framework.php' );
	