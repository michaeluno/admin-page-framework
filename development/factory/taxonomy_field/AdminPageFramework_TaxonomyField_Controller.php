<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to manipulate the class behavior.
 *
 * @abstract
 * @since           3.5.0
 * @package         AdminPageFramework
 * @subpackage      Factory/TaxonomyField
 */
abstract class AdminPageFramework_TaxonomyField_Controller extends AdminPageFramework_TaxonomyField_View {
    
    /**
     * The set up method.
     * 
     * @remark      should be overridden by the user definition class.
     * @since       3.0.0
     * @since       3.5.0       Moved from `AdminPageFramework_TaxonomyField`.
     * @return      void
     */
    public function setUp() {}    
        
}
