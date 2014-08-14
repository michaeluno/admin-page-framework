<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Page' ) ) :
/**
 * Provides methods to render admin page elements.
 *
 * @abstract
 * @extends AdminPageFramework_Page_MetaBox
 * @since 2.0.0
 * @since 2.1.0 Extends AdminPageFramework_HelpPane_Page.
 * @since 3.0.0 No longer extends AdminPageFramework_HelpPane_Page.
 * @package AdminPageFramework
 * @subpackage Page
 * @staticvar array $_aScreenIconIDs stores the ID selector names for screen icons.
 * @staticvar array $_aHookPrefixes stores the prefix strings for filter and action hooks.
 * @staticvar array $_aStructure_InPageTabElements represents the array structure of an in-page tab array.
 */
abstract class AdminPageFramework_Page extends AdminPageFramework_Page_MetaBox {
                
    /**
     * Stores the ID selector names for screen icons. <em>generic</em> is not available in WordPress v3.4.x.
     * 
     * @since 2.0.0
     * @var array
     * @static
     * @access protected
     * @internal
     */     
    protected static $_aScreenIconIDs = array(
        'edit', 'post', 'index', 'media', 'upload', 'link-manager', 'link', 'link-category', 
        'edit-pages', 'page', 'edit-comments', 'themes', 'plugins', 'users', 'profile', 
        'user-edit', 'tools', 'admin', 'options-general', 'ms-admin', 'generic',
    );    

    /**
     * Represents the array structure of an in-page tab array.
     * 
     * @since 2.0.0
     * @var array
     * @static
     * @access private
     * @internal
     */     
    private static $_aStructure_InPageTabElements = array(
        'page_slug' => null,
        'tab_slug' => null,
        'title' => null,
        'order' => null,
        'show_in_page_tab' => true,
        'parent_tab_slug' => null, // this needs to be set if the above show_in_page_tab is false so that the framework can mark the parent tab to be active when the hidden page is accessed.
    );
        
    /**
     * Registers necessary hooks and sets up properties.
     * 
     * @internal
     */
    function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {    
    
        parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain );
        
        if ( $this->oProp->bIsAdminAjax ) {
            return;
        }     
        add_action( "load_after_{$this->oProp->sClassName}", array( $this, '_replyToFinalizeInPageTabs' ), 19 ); // must be called before the _replyToRegisterSettings() method 
                
    }
    
    /**
     * Adds in-page tabs.
     *
     * The parameters accept in-page tab arrays and they must have the following array keys.
     * 
     * <h4>Example</h4>
     * <code>$this->addInPageTabs(
     * array(
     * 'page_slug' => 'myfirstpage'
     * 'tab_slug' => 'firsttab',
     * 'title' => __( 'Text Fields', 'my-text-domain' ),
     * ),
     * array(
     * 'page_slug' => 'myfirstpage'
     * 'tab_slug' => 'secondtab',
     * 'title' => __( 'Selectors and Checkboxes', 'my-text-domain' ),
     * )
     * );</code>
     *
     * <code>$this->addInPageTabs(
     * 'myfirstpage', // sets the target page slug
     * array(
     * 'tab_slug' => 'firsttab',
     * 'title' => __( 'Text Fields', 'my-text-domain' ),
     * ),
     * array(
     * 'tab_slug' => 'secondtab',
     * 'title' => __( 'Selectors and Checkboxes', 'my-text-domain' ),
     * )
     * );</code>
     * @since 2.0.0
     * @since 3.0.0 Changed the scope to public. Added page slug target support. 
     * @param array $aTab1 The in-page tab array.
     * <h4>In-Page Tab Array</h4>
     * <ul>
     *     <li><strong>page_slug</strong> - ( string ) the page slug that the tab belongs to.</li>
     *     <li><strong>tab_slug</strong> -  ( string ) the tab slug. Non-alphabetical characters should not be used including dots(.) and hyphens(-).</li>
     *     <li><strong>title</strong> - ( string ) the title of the tab.</li>
     *     <li><strong>order</strong> - ( optional, integer ) the order number of the tab. The lager the number is, the lower the position it is placed in the menu.</li>
     *     <li><strong>show_in_page_tab</strong> - ( optional, boolean ) default: false. If this is set to false, the tab title will not be displayed in the tab navigation menu; however, it is still accessible from the direct URL.</li>
     *     <li><strong>parent_tab_slug</strong> - ( optional, string ) this needs to be set if the above show_in_page_tab is true so that the parent tab will be emphasized as active when the hidden page is accessed.</li>
     * </ul>
     * @param array $aTab2 Another in-page tab array.
     * @param array $_and_more Add in-page tab arrays as many as necessary to the next parameters.
     * @param string (optional) $sPageSlug If the passed parameter item is a string, it will be stored as the target page slug so that it will be applied to the next passed tab arrays as the page_slug element.
     * @remark Accepts variadic parameters; the number of accepted parameters are not limited to three.
     * @remark In-page tabs are different from page-heading tabs which is automatically added with page titles.  
     * @return void
     */             
    public function addInPageTabs( $aTab1, $aTab2=null, $_and_more=null ) {
        foreach( func_get_args() as $asTab ) $this->addInPageTab( $asTab );
    }
    
    /**
     * Adds an in-page tab.
     * 
     * The singular form of the addInPageTabs() method, which takes only one parameter.
     * 
     * @since 2.0.0
     * @since 3.0.0 Changed the scope to public.
     * @param array|string $asInPageTab The in-page tab array or the target page slug. If the target page slug is set, the page_slug key can be omitted from next calls.
     * @remark Use this method to add in-page tabs to ensure the array holds all the necessary keys.
     * @remark In-page tabs are different from page-heading tabs which are automatically added with page titles.
     * @return void
     */         
    public function addInPageTab( $asInPageTab ) {    
        
        static $__sTargetPageSlug; // stores the target page slug which will be applied when no page slug is specified.
        if ( ! is_array( $asInPageTab ) ) {
            $__sTargetPageSlug = is_string( $asInPageTab ) ? $asInPageTab : $__sTargetPageSlug; // set the target page slug
            return;
        }         
        
        $aInPageTab = $this->oUtil->uniteArrays( $asInPageTab, self::$_aStructure_InPageTabElements, array( 'page_slug' => $__sTargetPageSlug ) ); // avoid undefined index warnings.     
        $__sTargetPageSlug = $aInPageTab['page_slug']; // set the target page slug for next calls
        if ( ! isset( $aInPageTab['page_slug'], $aInPageTab['tab_slug'] ) ) return; // check the required keys.
        
        $iCountElement = isset( $this->oProp->aInPageTabs[ $aInPageTab['page_slug'] ] ) ? count( $this->oProp->aInPageTabs[ $aInPageTab['page_slug'] ] ) : 0;
        $aInPageTab = array( // sanitize some elements
            'page_slug' => $this->oUtil->sanitizeSlug( $aInPageTab['page_slug'] ),
            'tab_slug' => $this->oUtil->sanitizeSlug( $aInPageTab['tab_slug'] ),
            'order' => is_numeric( $aInPageTab['order'] ) ? $aInPageTab['order'] : $iCountElement + 10,
        ) + $aInPageTab;

        $this->oProp->aInPageTabs[ $aInPageTab['page_slug'] ][ $aInPageTab['tab_slug'] ] = $aInPageTab;
    
    }     
    
    /**
     * Sets whether the page title is displayed or not.
     * 
     * <h4>Example</h4>
     * <code>$this->setPageTitleVisibility( false );    // disables the page title.
     * </code>
     * 
     * @since 2.0.0
     * @since 3.0.0 Changed the scope to public.
     * @param boolean $bShow If false, the page title will not be displayed.
     * @return void
     */ 
    public function setPageTitleVisibility( $bShow=true, $sPageSlug='' ) {
        
        $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
        if ( $sPageSlug ) {
            $this->oProp->aPages[ $sPageSlug ]['show_page_title'] = $bShow;
            return;
        }
        $this->oProp->bShowPageTitle = $bShow;
        foreach( $this->oProp->aPages as &$aPage ) 
            $aPage['show_page_title'] = $bShow;
        
    }    
    
    /**
     * Sets whether page-heading tabs are displayed or not.
     * 
     * <h4>Example</h4>
     * <code>$this->setPageHeadingTabsVisibility( false );    // disables the page heading tabs by passing false.
     * </code>
     * 
     * @since 2.0.0
     * @since 3.0.0 Changed the scope to public.
     * @param boolean $bShow If false, page-heading tabs will be disabled; otherwise, enabled.
     * @param string $sPageSlug The page to apply the visibility setting. If not set, it applies to all the pages.
     * @remark Page-heading tabs and in-page tabs are different. The former displays page titles and the latter displays tab titles.
     * @remark If the second parameter is omitted, it sets the default value.
     */ 
    public function setPageHeadingTabsVisibility( $bShow=true, $sPageSlug='' ) {
        
        $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
        if ( $sPageSlug ) {
            $this->oProp->aPages[ $sPageSlug ]['show_page_heading_tabs'] = $bShow;
            return;     
        }     
        $this->oProp->bShowPageHeadingTabs = $bShow;
        foreach( $this->oProp->aPages as &$aPage ) 
            $aPage['show_page_heading_tabs'] = $bShow;
        
    }
    
    /**
     * Sets whether in-page tabs are displayed or not.
     * 
     * Sometimes, it is required to disable in-page tabs in certain pages. In that case, use the second parameter.
     * 
     * @since 2.1.1
     * @since 3.0.0 Changed the scope to public. Changed the name from showInPageTabs() to setInPageTabsVisibility().
     * @param boolean $bShow If false, in-page tabs will be disabled.
     * @param string $sPageSlug The page to apply the visibility setting. If not set, it applies to all the pages.
     * @remark If the second parameter is omitted, it sets the default value.
     */
    public function setInPageTabsVisibility( $bShow=true, $sPageSlug='' ) {
        
        $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
        if ( $sPageSlug ) {
            $this->oProp->aPages[ $sPageSlug ]['show_in_page_tabs'] = $bShow;
            return;
        }
        $this->oProp->bShowInPageTabs = $bShow;
        foreach( $this->oProp->aPages as &$aPage )
            $aPage['show_in_page_tabs'] = $bShow;
        
    }
    
    /**
     * Sets in-page tab's HTML tag.
     * 
     * <h4>Example</h4>
     * <code>$this->setInPageTabTag( 'h2' );
     * </code>
     * 
     * @since 2.0.0
     * @since 3.0.0 Changed the scope to public.
     * @param string $sTag The HTML tag that encloses each in-page tab title. Default: h3.
     * @param string $sPageSlug The page slug that applies the setting.    
     * @remark If the second parameter is omitted, it sets the default value.
     */     
    public function setInPageTabTag( $sTag='h3', $sPageSlug='' ) {
        
        $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
        if ( $sPageSlug ) {
            $this->oProp->aPages[ $sPageSlug ]['in_page_tab_tag'] = $sTag;
            return;
        }
        $this->oProp->sInPageTabTag = $sTag;
        foreach( $this->oProp->aPages as &$aPage )
            $aPage['in_page_tab_tag'] = $sTag;
        
    }
    
    /**
     * Sets page-heading tab's HTML tag.
     * 
     * <h4>Example</h4>
     * <code>$this->setPageHeadingTabTag( 'h2' );
     * </code>
     * 
     * @since 2.1.2
     * @since 3.0.0 Changed the scope to public.
     * @param string $sTag The HTML tag that encloses the page-heading tab title. Default: h2.
     * @param string $sPageSlug The page slug that applies the setting.    
     * @remark If the second parameter is omitted, it sets the default value.
     */
    public function setPageHeadingTabTag( $sTag='h2', $sPageSlug='' ) {
        
        $sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
        if ( $sPageSlug ) {
            $this->oProp->aPages[ $sPageSlug ]['page_heading_tab_tag'] = $sTag;
            return;
        }
        $this->oProp->sPageHeadingTabTag = $sTag;
        foreach( $this->oProp->aPages as &$aPage )
            $aPage[ $sPageSlug ]['page_heading_tab_tag'] = $sTag;
        
    }
    
    /*
     * Internal Methods
     */
    
    /**
     * Renders the admin page.
     * 
     * @remark This is not intended for the users to use.
     * @since 2.0.0
     * @access protected
     * @return void
     * @internal
     */ 
    protected function _renderPage( $sPageSlug, $sTabSlug=null ) {

        // Do actions before rendering the page. In this order, global -> page -> in-page tab
        $this->oUtil->addAndDoActions( 
            $this,     // the caller object
            $this->oUtil->getFilterArrayByPrefix( 'do_before_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ), // the action hooks
            $this // the argument 1
        );
        ?>
        <div class="wrap">
            <?php
                // Screen icon, page heading tabs(page title), and in-page tabs.
                $sContentTop = $this->_getScreenIcon( $sPageSlug );    
                $sContentTop .= $this->_getPageHeadingTabs( $sPageSlug, $this->oProp->sPageHeadingTabTag );
                $sContentTop .= $this->_getInPageTabs( $sPageSlug, $this->oProp->sInPageTabTag );

                // Apply filters in this order, in-page tab -> page -> global.
                echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( 'content_foot_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), $sContentTop );

            ?>
            <div class="admin-page-framework-container">    
                <?php
                    $this->oUtil->addAndDoActions( 
                        $this, // the caller object
                        $this->oUtil->getFilterArrayByPrefix( 'do_form_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ), // the action hooks
                        $this // the argument 1
                    );
                    $this->_printFormOpeningTag( $this->oProp->bEnableForm ); // <form ... >
                ?>
                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-<?php echo $this->_getNumberOfColumns(); ?>">
                    <?php
                        $this->_printMainContent( $sPageSlug, $sTabSlug );
                        $this->_printMetaBox( 'side', 1 ); // defined in the parrent class.
                        $this->_printMetaBox( 'normal', 2 );
                        $this->_printMetaBox( 'advanced', 3 );
                    ?>     
                    </div><!-- #post-body -->    
                </div><!-- #poststuff -->
                
            <?php echo $this->_printFormClosingTag( $sPageSlug, $sTabSlug, $this->oProp->bEnableForm );  // </form> ?>
            </div><!-- .admin-page-framework-container -->
                
            <?php    
                // Apply the content_bottom filters.
                echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( 'content_bottom_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), '' ); // empty string
            ?>
        </div><!-- .wrap -->
        <?php
        // Do actions after rendering the page.
        $this->oUtil->addAndDoActions( 
            $this, // the caller object
            $this->oUtil->getFilterArrayByPrefix( 'do_after_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ), // the action hooks
            $this // the argument 1
        );
        
    }

        /**
         * Renders the main content of the admin page.
         * @since 3.0.0
         */
        private function _printMainContent( $sPageSlug, $sTabSlug ) {
            
            /* Check if a sidebar meta box is registered */
            $_bIsSideMetaboxExist = ( isset( $GLOBALS['wp_meta_boxes'][ $GLOBALS['page_hook'] ][ 'side' ] ) && count( $GLOBALS['wp_meta_boxes'][ $GLOBALS['page_hook'] ][ 'side' ] ) > 0 );

            echo "<!-- main admin page content -->";
            echo "<div class='admin-page-framework-content'>";
            if ( $_bIsSideMetaboxExist ) {
                echo "<div id='post-body-content'>";
            }
    
            /* Capture the output buffer */
            ob_start(); // start buffer
                                        
            // Render the form elements.
            if ( $this->oProp->bEnableForm ) {
                
                // do_settings_sections( $sPageSlug ); // deprecated     
                if ( $this->oForm->isPageAdded( $sPageSlug ) ) {

                    $this->aFieldErrors = isset( $this->aFieldErrors ) ? $this->aFieldErrors : $this->_getFieldErrors( $sPageSlug ); 
                    $oFieldsTable = new AdminPageFramework_FormTable( $this->oProp->aFieldTypeDefinitions, $this->aFieldErrors, $this->oMsg );
                    $this->oForm->setCurrentPageSlug( $sPageSlug );
                    $this->oForm->setCurrentTabSlug( $sTabSlug );
                    $this->oForm->applyConditions();
                    $this->oForm->applyFiltersToFields( $this, $this->oProp->sClassName ); // applies filters to the conditioned field definition arrays.
                    $this->oForm->setDynamicElements( $this->oProp->aOptions ); // will update $this->oForm->aConditionedFields
                    echo $oFieldsTable->getFormTables( $this->oForm->aConditionedSections, $this->oForm->aConditionedFields, array( $this, '_replyToGetSectionHeaderOutput' ), array( $this, '_replyToGetFieldOutput' ) );

                } 
                
            }     
             
            $_sContent = ob_get_contents(); // assign the content buffer to a variable
            ob_end_clean(); // end buffer and remove the buffer
                        
            // Apply the content filters.
            echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( 'content_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), $_sContent );

            // Do the page actions.
            $this->oUtil->addAndDoActions(
                $this, // the caller object
                $this->oUtil->getFilterArrayByPrefix( 'do_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ), // the action hooks
                $this // the argument 1
            );     
            
            if ( $_bIsSideMetaboxExist )
                echo "</div><!-- #post-body-content -->";
            echo "</div><!-- .admin-page-framework-content -->";
        }
    
        /**
         * Retrieves the form opening tag.
         * 
         * @since 2.0.0
         * @since 3.1.0 Changed to echo the output. Changed to remove disallowed query keys in the target action url.
         * @internal
         * @return void
         */ 
        private function _printFormOpeningTag( $fEnableForm=true ) {    
            
            if ( ! $fEnableForm ) {
                return;
            }
    
            echo "<form " 
                    . $this->oUtil->generateAttributes(
                        array(
                            'method' => 'post',
                            'enctype' => $this->oProp->sFormEncType,
                            'id' => 'admin-page-framework-form',
                            'action' => wp_unslash( remove_query_arg( 'settings-updated', $this->oProp->sTargetFormPage ) ),
                        )    
                    ) 
                . ">";
            settings_fields( $this->oProp->sOptionKey );
            
        }
        /**
         * Retrieves the form closing tag.
         * 
         * @since 2.0.0
         * @since 3.1.0 Prints out the output.
         * @internal
         * @return void
         */     
        private function _printFormClosingTag( $sPageSlug, $sTabSlug, $fEnableForm=true ) {
            
            if ( ! $fEnableForm ) {
                return;
            }
            
            $_sNonce = '_admin_page_framework_form_nonce_' . uniqid();
            $this->oUtil->setTransient( 'form_' . md5( $this->oProp->sClassName . get_current_user_id() ), $_sNonce, 60*60 ); // 60 minutes
            echo "<input type='hidden' name='page_slug' value='{$sPageSlug}' />" . PHP_EOL
                . "<input type='hidden' name='tab_slug' value='{$sTabSlug}' />" . PHP_EOL     
                . "<input type='hidden' name='_is_admin_page_framework' value='{$_sNonce}' />" . PHP_EOL
                . "</form><!-- End Form -->" . PHP_EOL;
            
        }    
    
        /**
         * Retrieves the screen icon output as HTML.
         * 
         * @remark     the screen object is supported in WordPress 3.3 or above.
         * @since 2.0.0
         */     
        private function _getScreenIcon( $sPageSlug ) {

            // If the icon path is explicitly set, use it.
            if ( isset( $this->oProp->aPages[ $sPageSlug ]['href_icon_32x32'] ) ) 
                return '<div class="icon32" style="background-image: url(' . $this->oProp->aPages[ $sPageSlug ]['href_icon_32x32'] . ');"><br /></div>';
            
            // If the screen icon ID is explicitly set, use it.
            if ( isset( $this->oProp->aPages[ $sPageSlug ]['screen_icon_id'] ) )
                return '<div class="icon32" id="icon-' . $this->oProp->aPages[ $sPageSlug ]['screen_icon_id'] . '"><br /></div>';
                
            // Retrieve the screen object for the current page.
            $oScreen = get_current_screen();
            $sIconIDAttribute = $this->_getScreenIDAttribute( $oScreen );

            $sClass = 'icon32';
            if ( empty( $sIconIDAttribute ) && $oScreen->post_type ) 
                $sClass .= ' ' . sanitize_html_class( 'icon32-posts-' . $oScreen->post_type );
            
            if ( empty( $sIconIDAttribute ) || $sIconIDAttribute == $this->oProp->sClassName )
                $sIconIDAttribute = 'generic'; // the default value
            
            return '<div id="icon-' . $sIconIDAttribute . '" class="' . $sClass . '"><br /></div>';
                
        }
            /**
             * Retrieves the screen ID attribute from the given screen object.
             * 
             * @since 2.0.0
             */     
            private function _getScreenIDAttribute( $oScreen ) {
                
                if ( ! empty( $oScreen->parent_base ) )
                    return $oScreen->parent_base;
            
                if ( 'page' == $oScreen->post_type )
                    return 'edit-pages';     
                    
                return esc_attr( $oScreen->base );
                
            }

        /**
         * Retrieves the output of page heading tab navigation bar as HTML.
         * 
         * @since 2.0.0
         * @return string     the output of page heading tabs.
         */         
        private function _getPageHeadingTabs( $sCurrentPageSlug, $sTag='h2', $aOutput=array() ) {
            
            // If the page title is disabled, return an empty string.
            if ( ! $this->oProp->aPages[ $sCurrentPageSlug ][ 'show_page_title' ] ) return "";

            $sTag = $this->oProp->aPages[ $sCurrentPageSlug ][ 'page_heading_tab_tag' ]
                ? $this->oProp->aPages[ $sCurrentPageSlug ][ 'page_heading_tab_tag' ]
                : $sTag;
        
            // If the page heading tab visibility is disabled, or only one page is registered, return the title.
            if ( ! $this->oProp->aPages[ $sCurrentPageSlug ][ 'show_page_heading_tabs' ] || count( $this->oProp->aPages ) == 1 )
                return "<{$sTag}>" . $this->oProp->aPages[ $sCurrentPageSlug ]['title'] . "</{$sTag}>";     

            foreach( $this->oProp->aPages as $aSubPage ) {
                
                // For added sub-pages
                if ( isset( $aSubPage['page_slug'] ) && $aSubPage['show_page_heading_tab'] ) {
                    // Check if the current tab number matches the iteration number. If not match, then assign blank; otherwise put the active class name.
                    $sClassActive =  $sCurrentPageSlug == $aSubPage['page_slug']  ? 'nav-tab-active' : '';     
                    $aOutput[] = "<a class='nav-tab {$sClassActive}' "
                        . "href='" . $this->oUtil->getQueryAdminURL( array( 'page' => $aSubPage['page_slug'], 'tab' => false ), $this->oProp->aDisallowedQueryKeys ) 
                        . "'>"
                        . $aSubPage['title']
                        . "</a>";    
                }
                
                // For added menu links
                if ( 
                    isset( $aSubPage['href'] )
                    && $aSubPage['type'] == 'link' 
                    && $aSubPage['show_page_heading_tab']
                ) 
                    $aOutput[] = 
                        "<a class='nav-tab link' "
                        . "href='{$aSubPage['href']}'>"
                            . $aSubPage['title']
                        . "</a>";     
                
            }     
            return "<div class='admin-page-framework-page-heading-tab'><{$sTag} class='nav-tab-wrapper'>" 
                .  implode( '', $aOutput ) 
                . "</{$sTag}></div>";
            
        }

        /**
         * Retrieves the output of in-page tab navigation bar as HTML.
         * 
         * @since 2.0.0
         * @return string     the output of in-page tabs.
         */     
        private function _getInPageTabs( $sCurrentPageSlug, $sTag='h3', $aOutput=array() ) {
            
            // If in-page tabs are not set, return an empty string.
            if ( empty( $this->oProp->aInPageTabs[ $sCurrentPageSlug ] ) ) { 
                return implode( '', $aOutput ); 
            }
                    
            // Determine the current tab slug.
            $sCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProp->getDefaultInPageTab( $sCurrentPageSlug );
            $sCurrentTabSlug = $this->_getParentTabSlug( $sCurrentPageSlug, $sCurrentTabSlug );

            $sTag = $this->oProp->aPages[ $sCurrentPageSlug ][ 'in_page_tab_tag' ]
                ? $this->oProp->aPages[ $sCurrentPageSlug ][ 'in_page_tab_tag' ]
                : $sTag;
        
            // If the in-page tabs' visibility is set to false, returns the title.
            if ( ! $this->oProp->aPages[ $sCurrentPageSlug ][ 'show_in_page_tabs' ] ) {
                return isset( $this->oProp->aInPageTabs[ $sCurrentPageSlug ][ $sCurrentTabSlug ]['title'] ) 
                    ? "<{$sTag}>{$this->oProp->aInPageTabs[ $sCurrentPageSlug ][ $sCurrentTabSlug ]['title']}</{$sTag}>" 
                    : "";
            }

            // Get the actual string buffer.
            foreach( $this->oProp->aInPageTabs[ $sCurrentPageSlug ] as $sTabSlug => $aInPageTab ) {
                        
                // If it's hidden and its parent tab is not set, skip
                if ( ! $aInPageTab['show_in_page_tab'] && ! isset( $aInPageTab['parent_tab_slug'] ) ) { continue; }
                
                // The parent tab means the root tab when there is a hidden tab that belongs to it. Also check it the specified parent tab exists.
                $sInPageTabSlug = isset( $aInPageTab['parent_tab_slug'], $this->oProp->aInPageTabs[ $sCurrentPageSlug ][ $aInPageTab['parent_tab_slug'] ] ) 
                    ? $aInPageTab['parent_tab_slug'] 
                    : $aInPageTab['tab_slug'];
                    
                // Check if the current tab slug matches the iteration slug. If not match, assign blank; otherwise, put the active class name.
                $bIsActiveTab = ( $sCurrentTabSlug == $sInPageTabSlug );

                $aOutput[ $sInPageTabSlug ] = "<a class='nav-tab " . ( $bIsActiveTab ? "nav-tab-active" : "" ) . "' "
                    . "href='" . $this->oUtil->getQueryAdminURL( array( 'page' => $sCurrentPageSlug, 'tab' => $sInPageTabSlug ), $this->oProp->aDisallowedQueryKeys ) 
                    . "'>"
                    . $this->oProp->aInPageTabs[ $sCurrentPageSlug ][ $sInPageTabSlug ]['title'] // "{$aInPageTab['title']}"
                    . "</a>";
            
            }     
            
            return empty( $aOutput )
                ? ""
                : "<div class='admin-page-framework-in-page-tab'><{$sTag} class='nav-tab-wrapper in-page-tab'>" 
                        . implode( '', $aOutput )
                    . "</{$sTag}></div>";
                
        }

            /**
             * Retrieves the parent tab slug from the given tab slug.
             * 
             * @since 2.0.0
             * @since 2.1.2 If the parent slug has the show_in_page_tab to be true, it returns an empty string.
             * @return string     the parent tab slug.
             */     
            private function _getParentTabSlug( $sPageSlug, $sTabSlug ) {
                
                $sParentTabSlug = isset( $this->oProp->aInPageTabs[ $sPageSlug ][ $sTabSlug ]['parent_tab_slug'] ) 
                    ? $this->oProp->aInPageTabs[ $sPageSlug ][ $sTabSlug ]['parent_tab_slug']
                    : $sTabSlug;
 
                return isset( $this->oProp->aInPageTabs[ $sPageSlug ][ $sParentTabSlug ]['show_in_page_tab'] ) && $this->oProp->aInPageTabs[ $sPageSlug ][ $sParentTabSlug ]['show_in_page_tab']
                    ? $sParentTabSlug
                    : '';

            }

    /**
     * Finalizes the in-page tab property array.
     * 
     * This finalizes the added in-page tabs and sets the default in-page tab for each page.
     * Also this sorts the in-page tab property array.
     * This must be done before registering settings sections because the default tab needs to be determined in the process.
     * 
     * @since 2.0.0
     * @remark A callback for the <em>admin_menu</em> hook. It must be called earlier than _replyToRegisterSettings() method.
     * @return void
     */         
    public function _replyToFinalizeInPageTabs() {

        if ( ! $this->oProp->isPageAdded() ) { return; }

        foreach( $this->oProp->aPages as $sPageSlug => $aPage ) {
            
            if ( ! isset( $this->oProp->aInPageTabs[ $sPageSlug ] ) ) { continue; }
            
            // Apply filters to let modify the in-page tab array.
            $this->oProp->aInPageTabs[ $sPageSlug ] = $this->oUtil->addAndApplyFilter( // Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
                $this,
                "tabs_{$this->oProp->sClassName}_{$sPageSlug}",
                $this->oProp->aInPageTabs[ $sPageSlug ]     
            );    
            // Added in-page arrays may be missing necessary keys so merge them with the default array structure.
            foreach( $this->oProp->aInPageTabs[ $sPageSlug ] as &$aInPageTab ) {
                $aInPageTab = $aInPageTab + self::$_aStructure_InPageTabElements;
                $aInPageTab['order'] = is_null( $aInPageTab['order'] ) ? 10 : $aInPageTab['order'];
            }
                        
            // Sort the in-page tab array.
            uasort( $this->oProp->aInPageTabs[ $sPageSlug ], array( $this, '_sortByOrder' ) );
            
            // Set the default tab for the page.
            // Read the value as reference; otherwise, a strange bug occurs. It may be due to the variable name, $aInPageTab, is also used as reference in the above foreach.
            foreach( $this->oProp->aInPageTabs[ $sPageSlug ] as $sTabSlug => &$aInPageTab ) {     
            
                if ( ! isset( $aInPageTab['tab_slug'] ) ) { continue; }
                
                // Regardless of whether it's a hidden tab, it is stored as the default in-page tab.
                $this->oProp->aDefaultInPageTabs[ $sPageSlug ] = $aInPageTab['tab_slug'];
                    
                break; // The first iteration item is the default one.
            }
        }

    }     
    
}
endif;