<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

 /**
  * Defines a meta box without form fields.
  */
class APF_Demo_PageMetaBox__NoField extends AdminPageFramework_PageMetaBox {
    
    /**
     * @return      string
     */
    public function content( $sContent ) {
        return $sContent
            . "<p>"
                . __( 'A meta box can be used to just display information', 'admin-page-framework-loader' )
            . "</p>";
    }
    
}
