<?php
$GLOBALS[ '_sProjectDirPath' ]    = dirname( dirname( dirname( dirname( __FILE__ ) ) ) );
$GLOBALS[ '_sTestSiteDirPath' ]   = dirname( dirname( dirname( $GLOBALS['_sProjectDirPath'] ) ) );

// ABSPATH is needed to load the framework.
define( 'ABSPATH', $GLOBALS[ '_sTestSiteDirPath' ]. '/' );
