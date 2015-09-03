<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to manipulate how admin pages are displayed.
 *
 * @abstract
 * @since           3.3.1
 * @extends         AdminPageFramework_Page_View
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 */
abstract class AdminPageFramework_Page_Controller extends AdminPageFramework_Page_View {
    
    /**
     * Adds in-page tabs.
     *
     * The parameters accept in-page tab arrays and they must have the following array keys.
     * 
     * <h4>Example</h4>
     * <code>$this->addInPageTabs(
     *      array(
     *          'page_slug' => 'myfirstpage'
     *          'tab_slug'  => 'firsttab',
     *          'title'     => __( 'Text Fields', 'my-text-domain' ),
     *      ),
     *      array(
     *          'page_slug' => 'myfirstpage'
     *          'tab_slug'  => 'secondtab',
     *          'title'     => __( 'Selectors and Checkboxes', 'my-text-domain' ),
     *      )
     * );</code>
     *
     * <code>$this->addInPageTabs(
     *      'myfirstpage', // sets the target page slug
     *      array(
     *          'tab_slug'  => 'firsttab',
     *          'title'     => __( 'Text Fields', 'my-text-domain' ),
     *      ),
     *      array(
     *          'tab_slug'  => 'secondtab',
     *          'title'     => __( 'Selectors and Checkboxes', 'my-text-domain' ),
     *      )
     * );</code>
     * @since       2.0.0
     * @since       3.0.0     Changed the scope to public. Added page slug target support. 
     * @since       3.3.1       Moved from `AdminPageFramework_Page`.
     * @param       array     $aTab1 The in-page tab array.
     * <h4>In-Page Tab Array</h4>
     * <ul>
     *     <li>**page_slug** - (string) the page slug that the tab belongs to.</li>
     *     <li>**tab_slug** -  (string) the tab slug. Non-alphabetical characters should not be used including dots(.) and hyphens(-).</li>
     *     <li>**title** - (string) the title of the tab.</li>
     *     <li>**order** - (optional, integer) the order number of the tab. The lager the number is, the lower the position it is placed in the menu.</li>
     *     <li>**show_in_page_tab** - (optional, boolean) default: `true`. If this is set to `false`, the tab title will not be displayed in the tab navigation menu; however, it is still accessible from the direct URL.</li>
     *     <li>**parent_tab_slug** - (optional, string) this needs to be set if the above `show_in_page_tab` argument is `false` so that the parent tab will be emphasized as active when the hidden page is accessed.</li>
     *     <li>**url** - [3.5.0+] (optional, string) If this is set, the link url of the navigation tab will be this url. Use this to create link only tab.</li>
     * </ul>
     * @param       array       $aTab2      Another in-page tab array.
     * @param       array       $_and_more  (optional) Add in-page tab arrays as many as necessary to the next parameters.
     * @param       string      $sPageSlug  (optional) If the passed parameter item is a string, it will be stored as the target page slug so that it will be applied to the next passed tab arrays as the page_slug element.
     * @remark      Accepts variadic parameters; the number of accepted parameters are not limited to three.
     * @remark      In-page tabs are different from page-heading tabs which is automatically added with page titles.  
     * @return      void
     */             
    public function addInPageTabs( /* $aTab1, $aTab2=null, $_and_more=null */ ) {
        foreach( func_get_args() as $asTab ) { 
            $this->addInPageTab( $asTab ); 
        }
    }
    
    /**
     * Adds an in-page tab.
     * 
     * The singular form of the `addInPageTabs()` method, which takes only one parameter.
     * 
     * @since       2.0.0
     * @since       3.0.0           Changed the scope to public.
     * @since       3.3.1       Moved from `AdminPageFramework_Page`.
     * @param       array|string    $asInPageTab        The in-page tab array or the target page slug. If the target page slug is set, the page_slug key can be omitted from next calls.
     * @remark      Use this method to add in-page tabs to ensure the array holds all the necessary keys.
     * @remark      In-page tabs are different from page-heading tabs which are automatically added with page titles.
     * @return      void
     */         
    public function addInPageTab( $asInPageTab ) {    
        
        // Target page slug - will be applied when no page slug is specified.
        static $__sTargetPageSlug; 
        if ( ! is_array( $asInPageTab ) ) {
            $__sTargetPageSlug = is_string( $asInPageTab ) 
                ? $asInPageTab 
                : $__sTargetPageSlug; // set the target page slug
            return;
        }         

        // Pre-format - avoid undefined index warnings.
        $aInPageTab         = $this->oUtil->uniteArrays( 
            $asInPageTab, 
            AdminPageFramework_Format_InPageTab::$aStructure, 
            array( 
                'page_slug' => $__sTargetPageSlug 
            ) 
        );
        
        // Set the target page slug for next calls
        $__sTargetPageSlug  = $aInPageTab[ 'page_slug' ]; 

        // Required keys
        if ( ! isset( $aInPageTab[ 'page_slug' ], $aInPageTab[ 'tab_slug' ] ) ) { 
            return; 
        }
        
        // Count - the number of added in-page tabs.
        $_aElements         = $this->oUtil->getElement(
            $this->oProp->aInPageTabs,
            $aInPageTab[ 'page_slug' ],
            array()
        );
        $_iCountElement      = count( $_aElements );

        // Format
        $aInPageTab         = array( 
            'page_slug' => $this->oUtil->sanitizeSlug( $aInPageTab[ 'page_slug' ] ),
            'tab_slug'  => $this->oUtil->sanitizeSlug( $aInPageTab[ 'tab_slug' ] ),
            'order'     => $this->oUtil->getAOrB(
                is_numeric( $aInPageTab[ 'order' ] ),
                $aInPageTab[ 'order' ],
                $_iCountElement + 10
            ),
        ) + $aInPageTab;

        // Set
        $this->oProp->aInPageTabs[ $aInPageTab['page_slug'] ][ $aInPageTab['tab_slug'] ] = $aInPageTab;
    
    }     
    
    /**
     * Sets whether the page title is displayed or not.
     * 
     * <h4>Example</h4>
     * <code>$this->setPageTitleVisibility( false );    // disables the page title.
     * </code>
     * 
     * @since       2.0.0
     * @since       3.0.0       Changed the scope to public.
     * @since       3.3.1       Moved from `AdminPageFramework_Page`.
     * @param       boolean     $bShow If false, the page title will not be displayed.
     * @return      void
     */ 
    public function setPageTitleVisibility( $bShow=true, $sPageSlug='' ) {
        
        // $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
        // if ( $sPageSlug ) {
            // $this->oProp->aPages[ $sPageSlug ]['show_page_title'] = $bShow;
            // return;
        // }
        // $this->oProp->bShowPageTitle = $bShow;
        // foreach( $this->oProp->aPages as &$aPage ) {
            // $aPage['show_page_title'] = $bShow;
        // }
        
        $this->_setPageProperty( 
            'bShowPageTitle', 
            'show_page_title', 
            $bShow, 
            $sPageSlug 
        );           
        
    }    
    
    /**
     * Sets whether page-heading tabs are displayed or not.
     * 
     * <h4>Example</h4>
     * <code>$this->setPageHeadingTabsVisibility( false );    // disables the page heading tabs by passing false.
     * </code>
     * 
     * @since       2.0.0
     * @since       3.0.0       Changed the scope to public.
     * @since       3.3.1       Moved from `AdminPageFramework_Page`.
     * @param       boolean     $bShow      If false, page-heading tabs will be disabled; otherwise, enabled.
     * @param       string      $sPageSlug  The page to apply the visibility setting. If not set, it applies to all the pages.
     * @remark      Page-heading tabs and in-page tabs are different. The former displays page titles and the latter displays tab titles.
     * @remark      If the second parameter is omitted, it sets the default value.
     */ 
    public function setPageHeadingTabsVisibility( $bShow=true, $sPageSlug='' ) {
        
        // $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
        // if ( $sPageSlug ) {
            // $this->oProp->aPages[ $sPageSlug ]['show_page_heading_tabs'] = $bShow;
            // return;     
        // }     
        // $this->oProp->bShowPageHeadingTabs = $bShow;
        // foreach( $this->oProp->aPages as &$aPage ) {
            // $aPage['show_page_heading_tabs'] = $bShow;
        // }
        
        $this->_setPageProperty( 
            'bShowPageHeadingTabs', 
            'show_page_heading_tabs', 
            $bShow, 
            $sPageSlug 
        );              
        
    }
    
    /**
     * Sets whether in-page tabs are displayed or not.
     * 
     * Sometimes, it is required to disable in-page tabs in certain pages. In that case, use the second parameter.
     * 
     * @since       2.1.1
     * @since       3.0.0       Changed the scope to public. Changed the name from `showInPageTabs()` to `setInPageTabsVisibility()`.
     * @since       3.3.1       Moved from `AdminPageFramework_Page`.
     * @param       boolean     $bShow      If false, in-page tabs will be disabled.
     * @param       string      $sPageSlug  The page to apply the visibility setting. If not set, it applies to all the pages.
     * @remark      If the second parameter is omitted, it sets the default value.
     */
    public function setInPageTabsVisibility( $bShow=true, $sPageSlug='' ) {
        
        // $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
        // if ( $sPageSlug ) {
            // $this->oProp->aPages[ $sPageSlug ]['show_in_page_tabs'] = $bShow;
            // return;
        // }
        // $this->oProp->bShowInPageTabs = $bShow;
        // foreach( $this->oProp->aPages as &$aPage ) {
            // $aPage['show_in_page_tabs'] = $bShow;
        // }
        
        $this->_setPageProperty( 
            'bShowInPageTabs', 
            'show_in_page_tabs', 
            $bShow, 
            $sPageSlug 
        );                
        
    }
    
    /**
     * Sets in-page tab's HTML tag.
     * 
     * <h4>Example</h4>
     * <code>$this->setInPageTabTag( 'h2' );
     * </code>
     * 
     * @since       2.0.0
     * @since       3.0.0       Changed the scope to public.
     * @since       3.3.1       Moved from `AdminPageFramework_Page`.
     * @param       string      $sTag           The HTML tag that encloses each in-page tab title. Default: `h3`.
     * @param       string      $sPageSlug      The page slug that applies the setting.    
     * @remark      If the second parameter is omitted, it sets the default value.
     */     
    public function setInPageTabTag( $sTag='h3', $sPageSlug='' ) {
        
        // $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
        // if ( $sPageSlug ) {
            // $this->oProp->aPages[ $sPageSlug ]['in_page_tab_tag'] = $sTag;
            // return;
        // }
        // $this->oProp->sInPageTabTag = $sTag;
        // foreach( $this->oProp->aPages as &$aPage ) {
            // $aPage['in_page_tab_tag'] = $sTag;
        // }
// return;        
        $this->_setPageProperty( 
            'sInPageTabTag', 
            'in_page_tab_tag', 
            $sTag, 
            $sPageSlug 
        );        
        
    }
    
    /**
     * Sets page-heading tab's HTML tag.
     * 
     * <h4>Example</h4>
     * <code>$this->setPageHeadingTabTag( 'h2' );
     * </code>
     * 
     * @since       2.1.2
     * @since       3.0.0       Changed the scope to public.
     * @since       3.3.1       Moved from `AdminPageFramework_Page`.
     * @param       string      $sTag       The HTML tag that encloses the page-heading tab title. Default: `h2`.
     * @param       string      $sPageSlug  The page slug that applies the setting.    
     * @remark      If the second parameter is omitted, it sets the default value.
     */
    public function setPageHeadingTabTag( $sTag='h2', $sPageSlug='' ) {
        
        // $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
        // if ( $sPageSlug ) {
            // $this->oProp->aPages[ $sPageSlug ]['page_heading_tab_tag'] = $sTag;
            // return;
        // }
        // $this->oProp->sPageHeadingTabTag = $sTag;
        // foreach( $this->oProp->aPages as &$aPage ) {
            // $aPage[ $sPageSlug ]['page_heading_tab_tag'] = $sTag;
        // }
     
        $this->_setPageProperty( 
            'sPageHeadingTabTag',   // property name
            'page_heading_tab_tag', // property key
            $sTag, // value
            $sPageSlug  // page slug
        );
        
    }
        
        /**
         * Sets a page property.
         * @since       3.6.0
         * @return      void
         */
        private function _setPageProperty( $sPropertyName, $sPropertyKey, $mValue, $sPageSlug ) {
            
            $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
            if ( $sPageSlug ) {
                $this->oProp->aPages[ $sPageSlug ][ $sPropertyKey ] = $mValue;
                return;
            }
            
            $this->oProp->{$sPropertyName} = $mValue;
       
            foreach( $this->oProp->aPages as &$_aPage ) {
                $_aPage[ $sPropertyKey ] = $mValue;

            }                        
        }
 
}