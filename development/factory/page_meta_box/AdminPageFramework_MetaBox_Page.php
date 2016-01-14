<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for creating meta boxes in pages added by the framework.
 * 
 * @abstract
 * @since       3.0.0      
 * @extends     AdminPageFramework_PageMetaBox
 * @package     AdminPageFramework
 * @subpackage  PageMetaBox
 * @deprecated  3.7.0
 */
abstract class AdminPageFramework_MetaBox_Page extends AdminPageFramework_PageMetaBox {
    
    /**
     * Registers necessary hooks and internal properties.
     * @since       3.0.0
     * @deprecated  3.7.0      Use     `AdminPageFramework_PageMetaBox` instead.
     */
    function __construct( $sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {
    
        trigger_error( 
            sprintf(
                __( 'The class <code>%1$s</code> is deprecated. Use <code>%2$s</code> instead.', 'admin-page-framework' ),
                __CLASS__, // %1$s
                'AdminPageFramework_PageMetaBox'    // %2%s
            ),
            E_USER_NOTICE 
        );        
                
        parent::__construct( $sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain );
                    
    }
                
}
