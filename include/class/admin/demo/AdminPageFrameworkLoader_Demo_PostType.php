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
class AdminPageFrameworkLoader_Demo_PostType {
    
    public function __construct() {     
    
        new APF_PostType( 
            AdminPageFrameworkLoader_Registry::$aPostTypes['demo'],                // the post type slug
            array(),                    // the argument array. Here an empty array is passed because it is defined inside the class.
            APFDEMO_FILE,               // the caller script path.
            'admin-page-framework-loader' // the text domain.
        );   
           
        new AdminPageFrameworkLoader_Demo_MetaBox;
        new AdminPageFrameworkLoader_Demo_Taxonomy;
           
    }
     
}