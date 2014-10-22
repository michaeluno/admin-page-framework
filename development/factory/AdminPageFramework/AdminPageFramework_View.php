<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_View' ) ) :
/**
 * Deals with displaying outputs.
 *
 * @abstract
 * @since           3.3.1
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_View extends AdminPageFramework_Model {
    
    /**
     * A helper function for the above setAdminNotice() method.
     * 
     * @since       2.1.2
     * @since       3.3.1       Moved from `AdminPageFramework`.
     * @internal
     */
    public function _replyToPrintAdminNotices() {
        if ( ! $this->_isInThePage() ) { return; }
        foreach( $this->oProp->aAdminNotices as $aAdminNotice ) {
            echo "<div class='{$aAdminNotice['sClassSelector']}' id='{$aAdminNotice['sID']}'>"
                    . "<p>" . $aAdminNotice['sMessage'] . "</p>"
                . "</div>";
        }
    }    
    
}
endif;