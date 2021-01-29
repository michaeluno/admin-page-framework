<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 * 
 */

/**
 * Deals with displaying outputs.
 *
 * @abstract
 * @since           3.3.1
 * @package         AdminPageFramework/Factory/AdminPage
 */
abstract class AdminPageFramework_View extends AdminPageFramework_Model {
      
    /**
     * The content filter method,
     * 
     * The user may just override this method instead of defining a `content_{...}` callback method.
     * 
     * @since       3.4.1
     * @remark      Declare this method in each factory class as the form of parameters varies and if parameters are different, it triggers PHP strict standard warnings.
     * @param       string      $sContent       The filtering content string.
     * @return      string
     */
    public function content( $sContent ) {
        return $sContent;
    }         
    
}
