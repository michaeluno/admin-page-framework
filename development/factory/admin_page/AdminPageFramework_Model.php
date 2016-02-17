<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Deals with retrieving/saving data from the database.
 *
 * @abstract
 * @since           3.3.1
 * @extends         AdminPageFramework_Controller_Menu
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Model extends AdminPageFramework_Controller_Menu {
    
    /**
     * A validation callback method.
     * 
     * The user may just override this method instead of defining a `validation_{...}` callback method.
     * 
     * @since       3.5.3       
     * @remark      Do not even define the method as the construct of the parameters may change which can lead PHP strict standard warnings.
     */
    // public function validate( $aInput, $aOldInput, $oFactory, $aSubmitInfo ) {
        // return $aInput;
    // }         

}
