<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides routing methods for the widget factory class.
 * 
 * @abstract
 * @since       3.2.0
 * @package     AdminPageFramework
 * @subpackage  Widget
 * @internal
 */
abstract class AdminPageFramework_Widget_Router extends AdminPageFramework_Factory {    
        
    /**
     * Redirects undefined callback methods or to the appropriate methods.
     * 
     * @since       3.2.0
     * @internal
     */
    public function __call( $sMethodName, $aArgs=null ) {    
    
        if ( 'setup_pre' === $sMethodName ) { 
        
            // @todo introduce "set_up_pre_{ class name }" action hook.
            $this->_setUp();
            
            // This action hook must be called AFTER the _setUp() method as there are callback methods that hook into this hook and assumes required configurations have been made.
            // @todo Examine why the same action hook 'set_up_{class name}' is added in the AdminPageFramework_Widget_Model class.
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