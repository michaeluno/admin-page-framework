<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2018, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for creating meta boxes in pages added by the framework.
 * 
 * @abstract
 * @since       3.0.0      
 * @extends     AdminPageFramework_PageMetaBox
 * @package     AdminPageFramework/Factory/PageMetaBox
 * @deprecated  3.7.0
 */
abstract class AdminPageFramework_MetaBox_Page extends AdminPageFramework_PageMetaBox {
    
    /**
     * Registers necessary hooks and internal properties.
     * @since       3.0.0
     * @deprecated  3.7.0      Use     `AdminPageFramework_PageMetaBox` instead.
     */
    public function __construct( $sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {
                        
        parent::__construct( $sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain );
        
        $this->oUtil->showDeprecationNotice( 
            'The class, ' . __CLASS__ . ',', // deprecated item
            'AdminPageFramework_PageMetaBox' // alternative
        );
        
    }
                
}
