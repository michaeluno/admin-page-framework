<?php
/**
 * Handles plugin options.
 */
class AdminPageFrameworkLoader_Option {

    /**
     * Stores instances by option key.
     * 
     * @since       3.5.0
     */
    static public $aInstances = array(
        // key => object
    );
    
    /**
     * Stores the option values.
     */
    public $aOptions = array(
        'welcomed'              => false,       // if the welcome screen is displayed, this will be true.
        'enable_admin_pages'    => true,        // whether or not to enable the admin pages of the loader plugin.
        'version_upgraded_from' => null,        // the version number that the user has upgraded from
        'version_saved'         => null,        // represents the option version.
    );
         
    /**
     * stores the option key for this plugin. 
     */
    protected $sOptionKey = '';    
         
    /**
     * Stores whether the currently loading page is in the network admin area.
     */
    protected $bIsNetworkAdmin = false;     
         
    /**
     * Returns the instance of the class.
     * 
     * This is to ensure only one instance exists.
     * 
     * @since      3.5.0
     */
    static public function getInstance( $sOptionKey='' ) {
        
        $sOptionKey = $sOptionKey 
            ? $sOptionKey
            : AdminPageFrameworkLoader_Registry::$aOptionKeys['main'];
        
        if ( isset( self::$aInstances[ $sOptionKey ] ) ) {
            return self::$aInstances[ $sOptionKey ];
        }
        $_sClassName = __CLASS__;
        self::$aInstances[ $sOptionKey ] = new $_sClassName( $sOptionKey );
            
        return self::$aInstances[ $sOptionKey ];
        
    }         
    
    /**
     * Sets up properties.
     */
    public function __construct( $sOptionKey ) {
        
        $this->sOptionKey       = $sOptionKey;
        $this->aOptions         = $this->_getFormattedOptions( $sOptionKey );
        $this->bIsNetworkAdmin  = is_network_admin();
        
    }    
    
    /**
     * Returns the formatted options array.
     */
    private function _getFormattedOptions( $sOptionKey ) {
                    
        return $this->bIsNetworkAdmin
            ? get_site_option( $sOptionKey, array() ) + $this->aOptions
            : get_option( $sOptionKey, array() ) + $this->aOptions;
        
    }
    
    /**
     * Checks the version number
     * 
     * @since       3.5.0
     * @return      boolean        True if yes; otherwise, false.
     */
    public function hasUpgraded() {
        
        $_sOptionVersion  = $this->get( 'version_saved' );
        if ( ! $_sOptionVersion ) {
            return false;
        }
        $_sOptionVersion        = $this->_getVersionByDepth( $_sOptionVersion );
        $_sCurrentVersion       = $this->_getVersionByDepth( AdminPageFrameworkLoader_Registry::Version );
        return version_compare( $_sOptionVersion, $_sCurrentVersion, '<' );
        
    }
        /**
         * Returns a stating part of version by the given depth.
         * @since       3.5.0
         */
        private function _getVersionByDepth( $sVersion, $iDepth=2 ) {
            if ( ! $iDepth ) {
                return $sVersion;
            }
            $_aParts = explode( '.', $sVersion );
            $_aParts = array_slice( $_aParts, 0, $iDepth );
            return implode( '.', $_aParts );
        }    
    
    /**
     * Deletes the option from the database.
     */
    public function delete()  {
        return $this->bIsNetworkAdmin
            ? delete_site_option( $this->sOptionKey )
            : delete_option( $this->sOptionKey );
    }
    
    /**
     * Saves the options.
     */
    public function save( $aOptions=null ) {

        $_aOptions = $aOptions ? $aOptions : $this->aOptions;
        return $this->bIsNetworkAdmin
            ? update_site_option(
                $this->sOptionKey, 
                $_aOptions
            )
            : update_option( 
                $this->sOptionKey, 
                $_aOptions
            );
    }
    
    /**
     * Sets the options.
     */
    public function set( /* $asKeys, $mValue */ ) {
        
        $_aParameters   = func_get_args();
        if ( ! isset( $_aParameters[ 0 ], $_aParameters[ 1 ] ) ) {
            return;
        }
        $_asKeys        = $_aParameters[ 0 ];
        $_mValue        = $_aParameters[ 1 ];
        
        // string, integer, float, boolean
        if ( ! is_array( $_asKeys ) ) {
            $this->aOptions[ $_asKeys ] = $_mValue;
            return;
        }
        
        // the keys are passed as an array
        $this->_setMultiDimensionalArray( $this->aOptions, $_asKeys, $_mValue );
        
    }
    
    /**
     * Sets and save the options.
     */
    public function update( /* $asKeys, $mValue */ ) {
        
        $_aParameters   = func_get_args();
        call_user_func_array( array( $this, 'set' ),  $_aParameters );
        $this->save();

    }

        private function _setMultiDimensionalArray( &$mSubject, array $aKeys, $mValue ) {

            $_sKey = array_shift( $aKeys );
            if ( $aKeys ) {
                if( ! isset( $mSubject[ $_sKey ] ) || ! is_array( $mSubject[ $_sKey ] ) ) {
                    $mSubject[ $_sKey ] = array();
                }
                $this->_setMultiDimensionalArray( $mSubject[ $_sKey ], $aKeys, $mValue );
                return;
                
            }
            $mSubject[ $_sKey ] = $mValue;

        }
 
    
     /**
     * Returns the specified option value.
     * 
     * @since       3.5.0
     */
    public function get( /* $sKey1, $sKey2, $sKey3, ... OR $aKeys, $vDefault */ ) {
        
        $_mDefault  = null;
        $_aKeys     = func_get_args();
        if ( ! isset( $_aKeys[ 0 ] ) ) {
            return null;
        }
        if ( is_array( $_aKeys[ 0 ] ) ) {
            $_aKeys =  $_aKeys[ 0 ];
            $_mDefault = isset( $_aKeys[ 1 ] )
                ? $_aKeys[ 0 ]
                : null;
        }
        // Now either the section ID or field ID is given. 
        return AdminPageFramework_WPUtility::getArrayValueByArrayKeys( 
            $this->aOptions, 
            $_aKeys,
            $_mDefault
        );
        
    }
    

    
}