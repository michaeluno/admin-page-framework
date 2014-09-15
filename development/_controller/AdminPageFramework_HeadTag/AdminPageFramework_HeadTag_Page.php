<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_HeadTag_Page' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag for the main framework class.
 * 
 * @since       2.1.5
 * @package     AdminPageFramework
 * @subpackage  HeadTag
 * @internal
 */
class AdminPageFramework_HeadTag_Page extends AdminPageFramework_HeadTag_Base {

    /**
     * Stores the class selector used to the class-specific style.
     * @since   3.2.0
     * @internal
     */
    protected $_sClassSelector_Style    = 'admin-page-framework-style-page';
    
    /**
     * Stores the class selector used to the class-specific script.
     * @since   3.2.0
     * @internal
     */    
    protected $_sClassSelector_Script   = 'admin-page-framework-script-page';
 
    /**
     * Enqueues styles by page slug and tab slug.
     * 
     * @since 2.1.5
     * @internal
     */
    public function _enqueueStyles( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
        
        $aHandleIDs = array();
        foreach( ( array ) $aSRCs as $sSRC ) {
            $aHandleIDs[] = $this->_enqueueStyle( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
        }
        return $aHandleIDs;
        
    }
    /**
     * Enqueues a style by page slug and tab slug.
     * 
     * <h4>Custom Argument Array for the Fourth Parameter</h4>
     * <ul>
     *     <li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
     *     <li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
     *     <li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
     *     <li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
     * </ul>
     * 
     * @since 2.1.2
     * @since 2.1.5 Moved from the main class.
     * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_style
     * @param string $sSRC The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
     * @param string $sPageSlug (optional) The page slug that the stylesheet should be added to. If not set, it applies to all the pages created by the framework.
     * @param string $sTabSlug (optional) The tab slug that the stylesheet should be added to. If not set, it applies to all the in-page tabs in the page.
     * @param             array $aCustomArgs (optional) The argument array for more advanced parameters.
     * @return string The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     * @internal
     */    
    public function _enqueueStyle( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
        
        $sSRC = trim( $sSRC );
        if ( empty( $sSRC ) ) { return ''; }
        if ( isset( $this->oProp->aEnqueuingScripts[ md5( $sSRC ) ] ) ) { return ''; } // if already set
        
        $sSRC = $this->oUtil->resolveSRC( $sSRC );
        
        $sSRCHash = md5( $sSRC ); // setting the key based on the url prevents duplicate items
        $this->oProp->aEnqueuingStyles[ $sSRCHash ] = $this->oUtil->uniteArrays( 
            ( array ) $aCustomArgs,
            array(     
                'sSRC' => $sSRC,
                'sPageSlug' => $sPageSlug,
                'sTabSlug' => $sTabSlug,
                'sType' => 'style',
                'handle_id' => 'style_' . $this->oProp->sClassName . '_' .  ( ++$this->oProp->iEnqueuedStyleIndex ),
            ),
            self::$_aStructure_EnqueuingScriptsAndStyles
        );
        return $this->oProp->aEnqueuingStyles[ $sSRCHash ][ 'handle_id' ];
        
    }
    
    /**
     * Enqueues scripts by page slug and tab slug.
     * 
     * @since 2.1.5
     */
    public function _enqueueScripts( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
        
        $aHandleIDs = array();
        foreach( ( array ) $aSRCs as $sSRC ) {
            $aHandleIDs[] = $this->_enqueueScript( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
        }
        return $aHandleIDs;
        
    }    
    /**
     * Enqueues a script by page slug and tab slug.
     * 
     * <h4>Custom Argument Array for the Fourth Parameter</h4>
     * <ul>
     *     <li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
     *     <li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
     *     <li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
     *     <li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
     *     <li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
     * </ul>  
     * 
     * @since 2.1.2
     * @since 2.1.5 Moved from the main class.
     * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_script
     * @param string $sSRC The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
     * @param string $sPageSlug (optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
     * @param string $sTabSlug (optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
     * @param             array $aCustomArgs (optional) The argument array for more advanced parameters.
     * @return string The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     * @internal
     */
    public function _enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
        
        $sSRC = trim( $sSRC );
        if ( empty( $sSRC ) ) { return ''; }
        if ( isset( $this->oProp->aEnqueuingScripts[ md5( $sSRC ) ] ) ) { return ''; } // if already set
        
        $sSRC = $this->oUtil->resolveSRC( $sSRC );
        
        $sSRCHash = md5( $sSRC ); // setting the key based on the url prevents duplicate items
        $this->oProp->aEnqueuingScripts[ $sSRCHash ] = $this->oUtil->uniteArrays( 
            ( array ) $aCustomArgs,
            array(     
                'sPageSlug' => $sPageSlug,
                'sTabSlug' => $sTabSlug,
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
     * @remark Used for inserting the input field head tag elements.
     * @since 3.0.0
     * @internal
     */
    public function _forceToEnqueueStyle( $sSRC, $aCustomArgs=array() ) {
        return $this->_enqueueStyle( $sSRC, '', '', $aCustomArgs );
    }
    /**
     * Enqueues a script source without conditions.
     * @remark Used for inserting the input field head tag elements.
     * @since 3.0.0
     * @internal
     */    
    public function _forceToEnqueueScript( $sSRC, $aCustomArgs=array() ) {
        return $this->_enqueueScript( $sSRC, '', '', $aCustomArgs );
    }
    
    
    /**
     * A helper function for the _replyToEnqueueScripts() and _replyToEnqueueStyle() methods.
     * 
     * @since 2.1.2
     * @since 2.1.5 Moved from the main class. Changed the name from enqueueSRCByPageConditoin.
     * @internal
     */
    protected function _enqueueSRCByConditoin( $aEnqueueItem ) {

        $sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
        $sCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProp->getDefaultInPageTab( $sCurrentPageSlug );
        $sPageSlug = $aEnqueueItem['sPageSlug'];
        $sTabSlug = $aEnqueueItem['sTabSlug'];
        
        // If the page slug is not specified and the currently loading page is one of the pages that is added by the framework,
        if ( ! $sPageSlug && $this->oProp->isPageAdded( $sCurrentPageSlug ) ) { // means script-global(among pages added by the framework)
            return $this->_enqueueSRC( $aEnqueueItem );
        }
                
        // If both tab and page slugs are specified,
        if ( 
            ( $sPageSlug && $sCurrentPageSlug == $sPageSlug )
            && ( $sTabSlug && $sCurrentTabSlug == $sTabSlug )
        ) {
            return $this->_enqueueSRC( $aEnqueueItem );
        }
        
        // If the tab slug is not specified and the page slug is specified, 
        // and if the current loading page slug and the specified one matches,
        if ( 
            ( $sPageSlug && ! $sTabSlug )
            && ( $sCurrentPageSlug == $sPageSlug )
        ) {
            return $this->_enqueueSRC( $aEnqueueItem );
        }

    }
}
endif;