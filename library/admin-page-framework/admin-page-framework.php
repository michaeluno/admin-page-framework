<?php
trigger_error( 
    'Admin Page Framework:'
        . ' The file is moved to: ' . dirname( dirname( __FILE__ ) ) . '/apf/admin-page-framework.php',
    E_USER_WARNING 
);
include( dirname( dirname( __FILE__ ) ) . '/apf/admin-page-framework.php' );
