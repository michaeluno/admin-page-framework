<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

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
        
        if ( ! $this->_isInThePage() ) { 
            return; 
        }
        
        foreach( $this->oProp->aAdminNotices as $_aAdminNotice ) {
            
            $_sClassSelectors = $this->oUtil->generateClassAttribute(
                $this->oUtil->getElement(
                    $_aAdminNotice,
                    array( 'sClassSelector' ),
                    ''
                ),
                'notice is-dismissible' // 3.5.12+
            );            
            echo "<div class='{$_sClassSelectors}' id='{$_aAdminNotice[ 'sID' ]}'>"
                    . "<p>" 
                        . $_aAdminNotice[ 'sMessage' ] 
                    . "</p>"
                . "</div>";
                
        }
        
    }    
    
    /**
     * The content filter method,
     * 
     * The user may just override this method instead of defining a `content_{...}` callback method.
     * 
     * @since       3.4.1
     * @remark      Declare this method in each factory class as the form of parameters varies and if parameters are different, it triggers PHP strict standard warnings.
     * @param       string      $sContent       The filtering content string.
     */
    public function content( $sContent ) {
        return $sContent;
    }         
    
}