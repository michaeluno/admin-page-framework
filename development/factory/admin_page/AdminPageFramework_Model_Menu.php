<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to manipulate menu items.
 *
 * @abstract
 * @since           3.3.1
 * @since           3.6.3           Changed the name from `AdminPageFramework_Menu_Model`.
 * @extends         AdminPageFramework_Controller_Page
 * @package         AdminPageFramework/Factory/AdminPage/Model
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

        // @deprecated  3.8.14
//        if ( $this->oProp->bIsAdminAjax ) {
//            return;
//        }

        new AdminPageFramework_Model_Menu__RegisterMenu( $this );

    }

}
