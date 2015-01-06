<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * {@inheritdoc}
 * 
 * {@inheritdoc}
 * 
 * This is for pages that have widget fields added by the framework.
 * 
 * @since       3.2.0
 * @since       3.3.0       Changed the name from AdminPageFramework_HeadTag_Widget.
 * @package     AdminPageFramework
 * @subpackage  HeadTag
 * @extends     AdminPageFramework_Resource_Base
 * @internal
 */
class AdminPageFramework_Resource_Widget extends AdminPageFramework_Resource_Base {
     
    /**
     * Enqueues styles by post type slug.
     * 
     * @since       3.2.0
     * @internal
     */
    public function _enqueueStyles( $aSRCs, $aCustomArgs=array() ) {
        
        $_aHandleIDs = array();
        foreach( ( array ) $aSRCs as $_sSRC ) {
            $_aHandleIDs[] = $this->_enqueueStyle( $_sSRC, $aCustomArgs );
        }
        return $_aHandleIDs;
        
    }
    /**
     * Enqueues a style by post type slug.
     * 
     * @since       3.2.0
     * @see         http://codex.wordpress.org/Function_Reference/wp_enqueue_style
     * @param       string      $sSRC The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
     * @param       array       $aCustomArgs (optional) The argument array for more advanced parameters.
     * @return      string      The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     * @internal
     */    
    public function _enqueueStyle( $sSRC, $aCustomArgs=array() ) {
        
        $sSRC = trim( $sSRC );
        if ( empty( $sSRC ) ) { return ''; }
        $sSRC       = $this->oUtil->resolveSRC( $sSRC );
        
        // Setting the key based on the url prevents duplicate items
        $_sSRCHash  = md5( $sSRC ); 
        if ( isset( $this->oProp->aEnqueuingStyles[ $_sSRCHash ] ) ) { return ''; } 
        
        $this->oProp->aEnqueuingStyles[ $_sSRCHash ] = $this->oUtil->uniteArrays( 
            ( array ) $aCustomArgs,
            array(     
                'sSRC'          => $sSRC,
                'sType'         => 'style',
                'handle_id'     => 'style_' . $this->oProp->sClassName . '_' .  ( ++$this->oProp->iEnqueuedStyleIndex ),
            ),
            self::$_aStructure_EnqueuingResources
        );
        
        // Store the attributes in another container by url.
        $this->oProp->aResourceAttributes[ $this->oProp->aEnqueuingStyles[ $_sSRCHash ]['handle_id'] ] = $this->oProp->aEnqueuingStyles[ $_sSRCHash ]['attributes'];
        
        return $this->oProp->aEnqueuingStyles[ $_sSRCHash ][ 'handle_id' ];
        
    }
    
    /**
     * Enqueues scripts by post type slug.
     * 
     * @since       3.2.0
     * @internal
     */
    public function _enqueueScripts( $aSRCs, $aCustomArgs=array() ) {
        
        $_aHandleIDs = array();
        foreach( ( array ) $aSRCs as $_sSRC ) {
            $_aHandleIDs[] = $this->_enqueueScript( $_sSRC, $aCustomArgs );
        }
        return $_aHandleIDs;
        
    }    
    /**
     * Enqueues a script by post type slug.
     * 
     * @since       3.2.0
     * @see         http://codex.wordpress.org/Function_Reference/wp_enqueue_script
     * @param       string      $sSRC The URL of the stylesheet to enqueue, the absolute file path, or relative path to the root directory of WordPress. Example: '/js/myscript.js'.
     * @param       array       $aCustomArgs (optional) The argument array for more advanced parameters.
     * @return      string      The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     * @internal
     */
    public function _enqueueScript( $sSRC, $aCustomArgs=array() ) {
        
        $sSRC       = trim( $sSRC );
        if ( empty( $sSRC ) ) { return ''; }
        $sSRC       = $this->oUtil->resolveSRC( $sSRC );
        
        // Setting the key based on the url prevents duplicate items
        $_sSRCHash  = md5( $sSRC ); 
        if ( isset( $this->oProp->aEnqueuingScripts[ $_sSRCHash ] ) ) { return ''; } 
        
        $this->oProp->aEnqueuingScripts[ $_sSRCHash ] = $this->oUtil->uniteArrays( 
            ( array ) $aCustomArgs,
            array(     
                'sSRC'      => $sSRC,
                'sType'     => 'script',
                'handle_id' => 'script_' . $this->oProp->sClassName . '_' .  ( ++$this->oProp->iEnqueuedScriptIndex ),
            ),
            self::$_aStructure_EnqueuingResources
        );

        // Store the attributes in another container by url.
        $this->oProp->aResourceAttributes[ $this->oProp->aEnqueuingScripts[ $_sSRCHash ]['handle_id'] ] = $this->oProp->aEnqueuingScripts[ $_sSRCHash ]['attributes'];
        
        return $this->oProp->aEnqueuingScripts[ $_sSRCHash ][ 'handle_id' ];
        
    }
    
    /**
     * Enqueues a style source without conditions.
     * 
     * @remark      Used for inserting the input field head tag elements.
     * @remark      The widget fields type does not have conditions unlike the meta-box type that requires to check currently loaded post type.
     * @since       3.2.0
     * @internal
     */
    public function _forceToEnqueueStyle( $sSRC, $aCustomArgs=array() ) {
        return $this->_enqueueStyle( $sSRC, $aCustomArgs );
    }
    /**
     * Enqueues a script source without conditions.
     * @remark      Used for inserting the input field head tag elements.
     * @remark      The widget fields type does not have conditions unlike the meta-box type that requires to check currently loaded post type.
     * @since       3.2.0
     * @internal
     */    
    public function _forceToEnqueueScript( $sSRC, $aCustomArgs=array() ) {
        return $this->_enqueueScript( $sSRC, $aCustomArgs );
    }
    

}