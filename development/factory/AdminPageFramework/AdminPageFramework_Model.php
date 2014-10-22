<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Model' ) ) :
/**
 * Deals with retrieving/saving data from the database.
 *
 * @abstract
 * @since           3.3.1
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Model extends AdminPageFramework_Menu_Controller {}
endif;