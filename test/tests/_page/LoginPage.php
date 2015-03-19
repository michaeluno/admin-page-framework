<?php

class LoginPage {
    
    // include url of current page
    static public $sURL = '/wp-login.php';
    
    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */ 
    static public $sUserNameField   = 'input[type=text]#user_login';
    static public $sPasswordField   = 'input[name=pwd]#user_pass';
    static public $sLoginButton     = 'input[type=submit]#wp-submit';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: EditPage::route('/123-post');
     */
    static public function route( $sParams ) {
        return self::$sURL . $sParams;
    }

}