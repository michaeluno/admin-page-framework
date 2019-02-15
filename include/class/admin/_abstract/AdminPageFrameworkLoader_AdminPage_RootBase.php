<?php
/**
 * Admin Page Framework Loader
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 */

/**
 * Provides an abstract base for bases.
 * 
 * @since       3.5.3
 */
abstract class AdminPageFrameworkLoader_AdminPage_RootBase {
    
    /**
     * Stores callback method names.
     * 
     * @since   3.5.3
     */
    protected $aMethods = array(
        'replyToLoadPage',
        'replyToDoPage',
        'replyToDoAfterPage',
        'replyToLoadTab',
        'replyToDoTab',
        'validate',        
    );

    /**
     * Handles callback methods.
     * @since       3.5.3
     * @return      mixed
     */
    public function __call( $sMethodName, $aArguments ) {
        
        if ( in_array( $sMethodName, $this->aMethods ) ) {
            return isset( $aArguments[ 0 ] ) 
                ? $aArguments[ 0 ] 
                : null;
        }       
        
        trigger_error( 
            'Admin Page Framework - Loader: ' . ' : ' . sprintf( 
                __( 'The method is not defined: %1$s', 'admin-page-framework-loader' ),
                $sMethodName 
            ), 
            E_USER_WARNING 
        );        
    }
   
    /**
     * A user constructor.
     * @since       3.5.3
     * @return      void
     */
    protected function construct( $oFactory ) {}
    
}
