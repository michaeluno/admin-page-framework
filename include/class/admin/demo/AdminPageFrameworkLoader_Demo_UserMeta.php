<?php
/**
 * Admin Page Framework - Loader
 * 
 * Loads Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Loads the demo components.
 * 
 * @since        3.5.3
 */
class AdminPageFrameworkLoader_Demo_UserMeta {
    
    public function __construct() {     
        new APF_Demo_MyUserMeta; 
    }

}