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
 * This is for post definition pages that have meta box fields added by the framework.
 * 
 * @since       2.1.5
 * @since       3.3.0       Changed the name from AdminPageFramework_HeadTag_MetaBox.
 * @use         AdminPageFramework_Utility
 * @package     AdminPageFramework
 * @extends     AdminPageFramework_Resource_Base
 * @subpackage  HeadTag
 * @internal
 */
class AdminPageFramework_Resource_MetaBox extends AdminPageFramework_Resource_Base {
             
    /**
     * Enqueues styles by post type slug.
     * 
     * @since 2.1.5
     * @internal
     */
    public function _enqueueStyles( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) {
        
        $_aHandleIDs = array();
        foreach( ( array ) $aSRCs as $_sSRC ) {
            $_aHandleIDs[] = $this->_enqueueStyle( $_sSRC, $aPostTypes, $aCustomArgs );
        }
        return $_aHandleIDs;
        
    }
    /**
     * Enqueues a style by post type slug.
     * 
     * <h4>Custom Argument Array for the Third Parameter</h4>
     * <ul>
     *     <li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
     *     <li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
     *     <li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
     *     <li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into after the input field tag.</li>
     * </ul>
     * 
     * @since 2.1.5     
     * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_style
     * @param string $sSRC The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
     * @param array $aPostTypes (optional) The post type slugs that the stylesheet should be added to. If not set, it applies to all the pages of the post types.
     * @param             array $aCustomArgs (optional) The argument array for more advanced parameters.
     * @return string The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     * @internal
     */    
    public function _enqueueStyle( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) {
        
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
                'aPostTypes'    => empty( $aPostTypes ) ? $this->oProp->aPostTypes : $aPostTypes,
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
     * @since 2.1.5
     * @internal
     */
    public function _enqueueScripts( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) {
        
        $_aHandleIDs = array();
        foreach( ( array ) $aSRCs as $_sSRC ) {
            $_aHandleIDs[] = $this->_enqueueScript( $_sSRC, $aPostTypes, $aCustomArgs );
        }
        return $_aHandleIDs;
        
    }    
    /**
     * Enqueues a script by post type slug.
     * 
     * <h4>Custom Argument Array for the Third Parameter</h4>
     * <ul>
     *     <li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
     *     <li><strong>dependencies</strong> - ( optional, array ) The dependency array.  Array of the handles of all the registered scripts that this script depends on. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
     *     <li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
     *     <li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
     *     <li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
     * </ul>  
     * 
     * @since 2.1.5     
     * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_script
     * @param string $sSRC The URL of the stylesheet to enqueue, the absolute file path, or relative path to the root directory of WordPress. Example: '/js/myscript.js'.
     * @param array $aPostTypes (optional) The post type slugs that the script should be added to. If not set, it applies to all the pages with the post type slugs.
     * @param             array $aCustomArgs (optional) The argument array for more advanced parameters.
     * @return string The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     * @internal
     */
    public function _enqueueScript( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) {
        
        $sSRC       = trim( $sSRC );
        if ( empty( $sSRC ) ) { return ''; }
        $sSRC       = $this->oUtil->resolveSRC( $sSRC );
        
        // Setting the key based on the url prevents duplicate items
        $_sSRCHash  = md5( $sSRC ); 
        if ( isset( $this->oProp->aEnqueuingScripts[ $_sSRCHash ] ) ) { return ''; } 
        
        $this->oProp->aEnqueuingScripts[ $_sSRCHash ] = $this->oUtil->uniteArrays( 
            ( array ) $aCustomArgs,
            array(     
                'sSRC'          => $sSRC,
                'aPostTypes'    => empty( $aPostTypes ) ? $this->oProp->aPostTypes : $aPostTypes,
                'sType'         => 'script',
                'handle_id'     => 'script_' . $this->oProp->sClassName . '_' .  ( ++$this->oProp->iEnqueuedScriptIndex ),
            ),
            self::$_aStructure_EnqueuingResources
        );
        
        // Store the attributes in another container by url.
        $this->oProp->aResourceAttributes[ $this->oProp->aEnqueuingScripts[ $_sSRCHash ]['handle_id'] ] = $this->oProp->aEnqueuingScripts[ $_sSRCHash ]['attributes'];
        
        return $this->oProp->aEnqueuingScripts[ $_sSRCHash ][ 'handle_id' ];
        
    }
    
    /**
     * Enqueues a style source without conditions.
     * @remark Used for inserting the input field head tag elements.
     * @since 3.0.0
     * @internal
     */
    public function _forceToEnqueueStyle( $sSRC, $aCustomArgs=array() ) {
        return $this->_enqueueStyle( $sSRC, array(), $aCustomArgs );
    }
    /**
     * Enqueues a script source without conditions.
     * @remark Used for inserting the input field head tag elements.
     * @since 3.0.0
     * @internal
     */    
    public function _forceToEnqueueScript( $sSRC, $aCustomArgs=array() ) {
        return $this->_enqueueScript( $sSRC, array(), $aCustomArgs );
    }
    
    /**
     * A helper function for the _replyToEnqueueScripts() and the _replyToEnqueueStyle() methods.
     * 
     * @since 2.1.5
     * @internal
     */
    protected function _enqueueSRCByConditoin( $aEnqueueItem ) {
       
        $_sCurrentPostType = isset( $_GET['post_type'] ) ? $_GET['post_type'] : ( isset( $GLOBALS['typenow'] ) ? $GLOBALS['typenow'] : null );
        if ( in_array( $_sCurrentPostType, $aEnqueueItem['aPostTypes'] ) ) {
            return $this->_enqueueSRC( $aEnqueueItem );
        }
            
    }

}