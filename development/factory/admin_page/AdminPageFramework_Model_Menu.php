<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to manipulate menu items.
 *
 * @abstract
 * @since           3.3.1
 * @since           3.6.3           Changed the name from `AdminPageFramework_Menu_Model`.
 * @extends         AdminPageFramework_Controller_Page
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Model_Menu extends AdminPageFramework_Controller_Page {
    
    /**
     * Registers necessary callbacks and sets up properties.
     * 
     * @internal
     */
    public function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {
        
        parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain );
        
        if ( $this->oProp->bIsAdminAjax ) {
            return;
        }
        
        new AdminPageFramework_Model_Menu__RegisterMenu( $this );
        
    }     
   
}