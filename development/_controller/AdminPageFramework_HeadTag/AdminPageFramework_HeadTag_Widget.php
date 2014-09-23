<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_HeadTag_Widget' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag for the meta box class.
 * 
 * @since       3.2.0
 * @package     AdminPageFramework
 * @subpackage  HeadTag
 * @extends     AdminPageFramework_HeadTag_Base
 * @internal
 */
class AdminPageFramework_HeadTag_Widget extends AdminPageFramework_HeadTag_Base {
     
    /**
     * Enqueues styles by post type slug.
     * 
     * @since       3.2.0
     * @internal
     */
    public function _enqueueStyles( $aSRCs, $aCustomArgs=array() ) {
        
        $aHandleIDs = array();
        foreach( ( array ) $aSRCs as $sSRC ) {
            $aHandleIDs[] = $this->_enqueueStyle( $sSRC, $aCustomArgs );
        }
        return $aHandleIDs;
        
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
        if ( isset( $this->oProp->aEnqueuingStyles[ md5( $sSRC ) ] ) ) { return ''; } // if already set
        
        $sSRC = $this->oUtil->resolveSRC( $sSRC );
        
        $sSRCHash = md5( $sSRC ); // setting the key based on the url prevents duplicate items
        $this->oProp->aEnqueuingStyles[ $sSRCHash ] = $this->oUtil->uniteArrays( 
            ( array ) $aCustomArgs,
            array(     
                'sSRC'          => $sSRC,
                'sType'         => 'style',
                'handle_id'     => 'style_' . $this->oProp->sClassName . '_' .  ( ++$this->oProp->iEnqueuedStyleIndex ),
            ),
            self::$_aStructure_EnqueuingScriptsAndStyles
        );
        return $this->oProp->aEnqueuingStyles[ $sSRCHash ][ 'handle_id' ];
        
    }
    
    /**
     * Enqueues scripts by post type slug.
     * 
     * @since       3.2.0
     * @internal
     */
    public function _enqueueScripts( $aSRCs, $aCustomArgs=array() ) {
        
        $aHandleIDs = array();
        foreach( ( array ) $aSRCs as $sSRC ) {
            $aHandleIDs[] = $this->_enqueueScript( $sSRC, $aCustomArgs );
        }
        return $aHandleIDs;
        
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
        
        $sSRC = trim( $sSRC );
        if ( empty( $sSRC ) ) { return ''; }
        
        // if already set
        if ( isset( $this->oProp->aEnqueuingScripts[ md5( $sSRC ) ] ) ) { return ''; } 
        
        $sSRC = $this->oUtil->resolveSRC( $sSRC );
        
        $sSRCHash = md5( $sSRC ); // setting the key based on the url prevents duplicate items
        $this->oProp->aEnqueuingScripts[ $sSRCHash ] = $this->oUtil->uniteArrays( 
            ( array ) $aCustomArgs,
            array(     
                'sSRC' => $sSRC,
                'sType' => 'script',
                'handle_id' => 'script_' . $this->oProp->sClassName . '_' .  ( ++$this->oProp->iEnqueuedScriptIndex ),
            ),
            self::$_aStructure_EnqueuingScriptsAndStyles
        );
        return $this->oProp->aEnqueuingScripts[ $sSRCHash ][ 'handle_id' ];
        
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
endif;