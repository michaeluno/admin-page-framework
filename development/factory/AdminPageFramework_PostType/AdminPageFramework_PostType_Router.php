<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_PostType_Router' ) ) :
/**
 * Provides routing methods for the post type factory class.
 * 
 * @abstract
 * @since           3.0.4
 * @package         AdminPageFramework
 * @subpackage      PostType
 * @internal
 */
abstract class AdminPageFramework_PostType_Router extends AdminPageFramework_Factory {    
        
    /**
     * Redirects undefined callback methods or to the appropriate methods.
     * 
     * @internal
     */
    public function __call( $sMethodName, $aArgs=null ) {    
    
        if ( 'setup_pre' == $sMethodName ) { 
            $this->_setUp();
            $this->oUtil->addAndDoAction( $this, "set_up_{$this->oProp->sClassName}", $this );
            $this->oProp->_bSetupLoaded = true;
            return;
        }

        if ( has_filter( $sMethodName ) ) {
            return isset( $aArgs[ 0 ] ) ? $aArgs[ 0 ] : null;
        }
        
        parent::__call( $sMethodName, $aArgs );
                
    }
    
}
endif;