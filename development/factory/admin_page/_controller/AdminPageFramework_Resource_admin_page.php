<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * {@inheritdoc}
 * 
 * {@inheritdoc}
 * 
 * This is for generic pages the framework creates.
 * 
 * @since       2.1.5
 * @since       3.3.0       Changed the name from AdminPageFramework_HeadTag_Page.
 * @package     AdminPageFramework
 * @subpackage  Factory/AdminPage/Resource
 * @extends     AdminPageFramework_Resource_Base
 * @internal
 */
class AdminPageFramework_Resource_admin_page extends AdminPageFramework_Resource_Base {
    
    /**
     * Applies page and tab specific filters to inline CSS rules.
     * 
     * @since       3.5.0
     * @return      void
     */
    protected function _printClassSpecificStyles( $sIDPrefix ) {
     
        // This method can be called two times in a page to support embedding in the footer. 
        static $_bLoaded = false;
        if ( $_bLoaded ) {
            parent::_printClassSpecificStyles( $sIDPrefix );
            return;
        }        
        $_bLoaded   = true;
     
        $_oCaller   = $this->oProp->oCaller;     
        $_sPageSlug = $this->_getCurrentPageSlugForFilter();
        $_sTabSlug  = $this->_getCurrentTabSlugForFilter( $_sPageSlug );
        
        // tab 
        if ( $_sPageSlug && $_sTabSlug ) {
            $this->oProp->sStyle     = $this->addAndApplyFilters( 
                $_oCaller, 
                "style_{$_sPageSlug}_{$_sTabSlug}", 
                $this->oProp->sStyle 
            );                 
        }
        
        // page
        if ( $_sPageSlug ) {
            $this->oProp->sStyle     = $this->addAndApplyFilters( 
                $_oCaller, 
                "style_{$_sPageSlug}", 
                $this->oProp->sStyle 
            );     
        }
        
        // The parent method should be called after updating the $this->oProp->sStyle property above.
        parent::_printClassSpecificStyles( $sIDPrefix );
        
    }
        /**
         * Returns the currently loaded page slug to apply resource filters.
         * 
         * If the page has not been added, an empty value will be returned.
         * 
         * @since       3.5.3
         * @return      string      The page slug if the page has been added.
         */
        private function _getCurrentPageSlugForFilter() {
            $_sPageSlug = $this->oProp->getCurrentPageSlug();
            return $this->oProp->isPageAdded( $_sPageSlug )
                ? $_sPageSlug
                : '';            
        }
        /**
         * Returns the currently loaded tab slug to apply resource filters.
         * 
         * If the tab has not been added, an empty value will be returned.
         * 
         * @since       3.5.3
         * @return      string      The tab slug if the tab has been added.
         */
        private function _getCurrentTabSlugForFilter( $sPageSlug ) {
            $_sTabSlug  = $this->oProp->getCurrentTabSlug( $sPageSlug ); 
            return isset( $this->oProp->aInPageTabs[ $sPageSlug ][ $_sTabSlug ] )
                ? $_sTabSlug
                : '';          
        }
        
    /**
     * Applies page and tab specific filters to inline JaveScript scirpts.
     * 
     * @since       3.5.0
     * @return      void
     */
    protected function _printClassSpecificScripts( $sIDPrefix ) {
       
        // This method can be called two times in a page to support embedding in the footer. 
        static $_bLoaded = false;
        if ( $_bLoaded ) {
            parent::_printClassSpecificScripts( $sIDPrefix );
            return;
        }        
        $_bLoaded   = true;
       
        $_oCaller   = $this->oProp->oCaller;     
        $_sPageSlug = $this->_getCurrentPageSlugForFilter();
        $_sTabSlug  = $this->_getCurrentTabSlugForFilter( $_sPageSlug );
        
        // tab 
        if ( $_sPageSlug && $_sTabSlug ) {
            $this->oProp->sScript     = $this->addAndApplyFilters( 
                $_oCaller, 
                "script_{$_sPageSlug}_{$_sTabSlug}", 
                $this->oProp->sScript 
            );                 
        }
        
        // page
        if ( $_sPageSlug ) {
            $this->oProp->sScript     = $this->addAndApplyFilters( 
                $_oCaller, 
                "script_{$_sPageSlug}", 
                $this->oProp->sScript 
            );     
        }        
        
        // The parent method should be called after updating the $this->oProp->sScript property above.
        parent::_printClassSpecificScripts( $sIDPrefix );
        
    }
    
    /**
     * Enqueues styles by page slug and tab slug.
     * 
     * @since 2.1.5
     * @internal
     */
    public function _enqueueStyles( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
        
        $_aHandleIDs = array();
        foreach( ( array ) $aSRCs as $_sSRC ) {
            $_aHandleIDs[] = $this->_enqueueStyle( $_sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
        }
        return $_aHandleIDs;
        
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
     * @since       2.1.2
     * @since       2.1.5   Moved from the main class.
     * @see         http://codex.wordpress.org/Function_Reference/wp_enqueue_style
     * @param       string  $sSRC           The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
     * @param       string  $sPageSlug      (optional) The page slug that the stylesheet should be added to. If not set, it applies to all the pages created by the framework.
     * @param       string  $sTabSlug       (optional) The tab slug that the stylesheet should be added to. If not set, it applies to all the in-page tabs in the page.
     * @param       array   $aCustomArgs    (optional) The argument array for more advanced parameters.
     * @return      string  The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     * @internal
     */    
    public function _enqueueStyle( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
        return $this->_enqueueResourceByType( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs, 'style' );        
    }
    
    /**
     * Enqueues scripts by page slug and tab slug.
     * 
     * @since 2.1.5
     */
    public function _enqueueScripts( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
        
        $_aHandleIDs = array();
        foreach( ( array ) $aSRCs as $_sSRC ) {
            $_aHandleIDs[] = $this->_enqueueScript( $_sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
        }
        return $_aHandleIDs;
        
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
     * @since       2.1.2
     * @since       2.1.5       Moved from the main class.
     * @see         http://codex.wordpress.org/Function_Reference/wp_enqueue_script
     * @param       string      $sSRC           The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
     * @param       string      $sPageSlug      (optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
     * @param       string      $sTabSlug       (optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
     * @param       array       $aCustomArgs    (optional) The argument array for more advanced parameters.
     * @return      string      The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     * @internal
     */
    public function _enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
        return $this->_enqueueResourceByType( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs, 'script' );
    }
        /**
         * Enqueues a resouce.
         * 
         * @since       3.5.3
         * @param       string      $sSRC           The source path or url.
         * @param       string      $sPageSlug      The page slug that the item gets enqueued.
         * @param       string      $sTabSlug       The page slug that the item gets enqueued.
         * @param       array       $aCustomArgs    A custom argument array.
         * @param       string      $sType          Accepts 'style' or 'script'
         */
        private function _enqueueResourceByType( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array(), $sType='style' ) {
            
            $sSRC       = trim( $sSRC );
            if ( empty( $sSRC ) ) { 
                return ''; 
            }
            $sSRC       = $this->getResolvedSRC( $sSRC );

            // Get the property name for the type
            $_sContainerPropertyName     = $this->_getContainerPropertyNameByType( $sType );
            $_sEnqueuedIndexPropertyName = $this->_getEnqueuedIndexPropertyNameByType( $sType );
            
            // setting the key based on the url prevents duplicate items
            $_sSRCHash  = md5( $sSRC );
            if ( isset( $this->oProp->{$_sContainerPropertyName}[ $_sSRCHash ] ) ) { 
                return ''; 
            } 
            
            $this->oProp->{$_sContainerPropertyName}[ $_sSRCHash ] = array_filter( $this->getAsArray( $aCustomArgs ), array( $this, 'isNotNull' ) )
                + array(     
                    'sPageSlug' => $sPageSlug,
                    'sTabSlug'  => $sTabSlug,
                    'sSRC'      => $sSRC,
                    'sType'     => $sType,
                    'handle_id' => $sType . '_' . $this->oProp->sClassName . '_' .  ( ++$this->oProp->{$_sEnqueuedIndexPropertyName} ),
                )
                + self::$_aStructure_EnqueuingResources
                ;

            // Store the attributes in another container by url.
            $this->oProp->aResourceAttributes[ $this->oProp->{$_sContainerPropertyName}[ $_sSRCHash ]['handle_id'] ] = $this->oProp->{$_sContainerPropertyName}[ $_sSRCHash ]['attributes'];
            
            return $this->oProp->{$_sContainerPropertyName}[ $_sSRCHash ][ 'handle_id' ];
            
        }
            /**
             * Returns the property name that contains the information of resources by type.
             * @since       3.5.3
             * @return      string      the property name that contains the information of resources by type.
             */
            private function _getContainerPropertyNameByType( $sType ) {
                switch ( $sType ) {
                    default:
                    case 'style':
                        return 'aEnqueuingStyles';
                    case 'script':
                        return 'aEnqueuingScripts';
                }
            }
            /**
             * Returns the property name that contains the added count of resources by type.
             * @since       3.5.3
             * @return      string      the property name that contains the added count of resources by type.
             */            
            private function _getEnqueuedIndexPropertyNameByType( $sType ) {
                switch ( $sType ) {
                    default:
                    case 'style':
                        return 'iEnqueuedStyleIndex';
                    case 'script':
                        return 'iEnqueuedScriptIndex';
                }
            }            
    /**
     * Enqueues a style source without conditions.
     * 
     * @remark      Used for inserting the input field head tag elements.
     * @since       3.0.0
     * @internal
     */
    public function _forceToEnqueueStyle( $sSRC, $aCustomArgs=array() ) {
        return $this->_enqueueStyle( $sSRC, '', '', $aCustomArgs );
    }
    /**
     * Enqueues a script source without conditions.
     * 
     * @remark      Used for inserting the input field head tag elements.
     * @since       3.0.0
     * @internal
     */    
    public function _forceToEnqueueScript( $sSRC, $aCustomArgs=array() ) {
        return $this->_enqueueScript( $sSRC, '', '', $aCustomArgs );
    }
    
    
    /**
     * A helper function for the _replyToEnqueueScripts() and _replyToEnqueueStyle() methods.
     * 
     * @since       2.1.2
     * @since       2.1.5       Moved from the main class. Changed the name from enqueueSRCByPageConditoin.
     * @since       3.7.0      Fixed a typo in the method name.
     * @internal
     */
    protected function _enqueueSRCByCondition( $aEnqueueItem ) {

        $sCurrentPageSlug   = $this->oProp->getCurrentPageSlug();
        $sCurrentTabSlug    = $this->oProp->getCurrentTabSlug( $sCurrentPageSlug );
        $sPageSlug          = $aEnqueueItem['sPageSlug'];
        $sTabSlug           = $aEnqueueItem['sTabSlug'];

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
