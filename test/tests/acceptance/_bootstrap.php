<?php
codecept_debug( 'Acceptance: _bootstrap.php loaded' );
require_once dirname( dirname( __FILE__ ) ) . '/_page/LoginPage.php';
require_once dirname( dirname( __FILE__ ) ) . '/_page/UserLoginPage.php';
require_once dirname( __FILE__ ) . '/_step/MemberSteps.php';
require_once dirname( __FILE__ ) . '/_common/Loader_AdminPage_Base.php';
require_once dirname( __FILE__ ) . '/_common/Demo_AdminPage_Base.php';
