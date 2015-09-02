<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to manipulate menu items.
 *
 * @abstract
 * @since           3.3.1       Moved most methods from `AdminPageFramework_Page`.
 * @extends         AdminPageFramework_Page_View
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Page_View extends AdminPageFramework_Page_View_MetaBox {
        
    /**
     * Renders the admin page.
     * 
     * @remark      This is not intended for the users to use.
     * @since       2.0.0
     * @since       3.3.1       Moved from `AdminPageFramework_Page`.
     * @access      protected
     * @return      void
     * @internal
     */ 
    protected function _renderPage( $sPageSlug, $sTabSlug=null ) {

        // Do actions before rendering the page. In this order, global -> page -> in-page tab
        $this->oUtil->addAndDoActions( 
            $this,  // the caller object
            $this->oUtil->getFilterArrayByPrefix( 'do_before_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ), // the action hooks
            $this   // the argument 1
        );
        ?>
        <div class="<?php echo esc_attr( $this->oProp->sWrapperClassAttribute ); ?>">
            <?php
                // Screen icon, page heading tabs(page title), and in-page tabs.
                $sContentTop  = $this->_getScreenIcon( $sPageSlug );    
                $sContentTop .= $this->_getPageHeadingTabs( $sPageSlug, $this->oProp->sPageHeadingTabTag );
                $sContentTop .= $this->_getInPageTabs( $sPageSlug, $this->oProp->sInPageTabTag );

                // Apply filters in this order, in-page tab -> page -> global.
                echo $this->oUtil->addAndApplyFilters( 
                    $this, 
                    $this->oUtil->getFilterArrayByPrefix( 
                        'content_top_', 
                        $this->oProp->sClassName, 
                        $sPageSlug, 
                        $sTabSlug, 
                        false 
                    ), 
                    $sContentTop 
                );

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
                        $this->_printMainPageContent( $sPageSlug, $sTabSlug );
                        $this->_printMetaBox( 'side', 1 );      // defined in the parent class.
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
            $this,  // the caller object
            $this->oUtil->getFilterArrayByPrefix( 'do_after_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ), // the action hooks
            $this   // the argument 1
        );
        
    }

        /**
         * Renders the main content of the admin page.
         * 
         * @since       3.0.0
         * @since       3.3.1       Moved from `AdminPageFramework_Page`.
         */
        private function _printMainPageContent( $sPageSlug, $sTabSlug ) {
            
            /* Check if a sidebar meta box is registered */
            $_bSideMetaboxExists = ( isset( $GLOBALS['wp_meta_boxes'][ $GLOBALS['page_hook'] ][ 'side' ] ) 
                && count( $GLOBALS['wp_meta_boxes'][ $GLOBALS['page_hook'] ][ 'side' ] ) > 0 );

            echo "<!-- main admin page content -->";
            echo "<div class='admin-page-framework-content'>";
            if ( $_bSideMetaboxExists ) {
                echo "<div id='post-body-content'>";
            }
    
            $_sContent = call_user_func_array( 
                array( $this, 'content' ),     // triggers __call()
                array( $this->_getMainPageContentOutput( $sPageSlug ) )
            );    // 3.5.3+
            
            // Apply the content filters.
            echo $this->oUtil->addAndApplyFilters( 
                $this,
                $this->oUtil->getFilterArrayByPrefix( 
                    'content_', 
                    $this->oProp->sClassName, 
                    $sPageSlug, 
                    $sTabSlug, 
                    false ), 
                $_sContent 
            );

            // Do the page actions.
            $this->oUtil->addAndDoActions(
                $this, // the caller object
                $this->oUtil->getFilterArrayByPrefix( 'do_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ), // the action hooks
                $this // the argument 1
            );     
            
            if ( $_bSideMetaboxExists ) {
                echo "</div><!-- #post-body-content -->";
            }
            echo "</div><!-- .admin-page-framework-content -->";
        }
            /**
             * Returns the main admin page HTML output.
             * @since       3.5.3
             * @internal
             * @return      string      The main admin page HTML output.
             */
            private function _getMainPageContentOutput( $sPageSlug ) {
                
                ob_start(); 
                echo $this->_getFormOutput( $sPageSlug );
                $_sContent = ob_get_contents(); 
                ob_end_clean(); 
                return $_sContent;
                
            }
                /**
                 * Returns the form output of the page.
                 * @since       3.5.3
                 * @internal
                 * @return      string      The form output of the page.
                 */
                private function _getFormOutput( $sPageSlug ) {
                    
                    if ( ! $this->oProp->bEnableForm ) {
                        return '';
                    }
                    if ( ! $this->oForm->isPageAdded( $sPageSlug ) ) {
                        return '';
                    }
                             
                    $this->aFieldErrors = isset( $this->aFieldErrors )
                        ? $this->aFieldErrors 
                        : $this->_getFieldErrors( $sPageSlug ); 
                        
                    $_oFieldsTable = new AdminPageFramework_FormPart_Table(
                        $this->oProp->aFieldTypeDefinitions, 
                        $this->aFieldErrors, 
                        $this->oMsg
                    );
                    
                    return $_oFieldsTable->getFormTables( 
                        $this->oForm->aConditionedSections, 
                        $this->oForm->aConditionedFields, 
                        array( $this, '_replyToGetSectionHeaderOutput' ), 
                        array( $this, '_replyToGetFieldOutput' ) 
                     );
                       
                }
                
        /**
         * Retrieves the form opening tag.
         * 
         * @since       2.0.0
         * @since       3.1.0       Changed to echo the output. Changed to remove disallowed query keys in the target action url.
         * @since       3.3.1       Moved from `AdminPageFramework_Page`.
         * @internal
         * @return      void
         */ 
        private function _printFormOpeningTag( $fEnableForm=true ) {    
            
            if ( ! $fEnableForm ) {
                return;
            }
    
            echo "<form " 
                    . $this->oUtil->getAttributes(
                        array(
                            'method'    => 'post',
                            'enctype'   => $this->oProp->sFormEncType,
                            'id'        => 'admin-page-framework-form',
                            'action'    => wp_unslash( remove_query_arg( 'settings-updated', $this->oProp->sTargetFormPage ) ),
                        )    
                    ) 
                . " >" . PHP_EOL;
            
            // [3.5.11+] Insert a mark that indicates the framework form has started.
            // This will be checked in the validation method with the `is_admin_page_framework` input value which gets inserted at the end of the form
            // in order to determine all the fields are sent for the PHP max_input_vars limitation set in the server configuration.
            echo "<input type='hidden' name='admin_page_framework_start' value='1' />" . PHP_EOL;
            
            // Embed the '_wp_http_referer' hidden field that is checked in the submit data processing method.
            settings_fields( $this->oProp->sOptionKey );
            
        }
        /**
         * Prints out the form closing tag.
         * 
         * @since       2.0.0
         * @since       3.1.0       Prints out the output.
         * @since       3.3.1       Moved from `AdminPageFramework_Page`.
         * @internal
         * @return      void
         */     
        private function _printFormClosingTag( $sPageSlug, $sTabSlug, $fEnableForm=true ) {
            
            if ( ! $fEnableForm ) {
                return;
            }
            
            $_sNonceTransientKey    = 'form_' . md5( $this->oProp->sClassName . get_current_user_id() );
            $_sNonce                = $this->oUtil->getTransient( $_sNonceTransientKey, '_admin_page_framework_form_nonce_' . uniqid() );
            $this->oUtil->setTransient( $_sNonceTransientKey, $_sNonce, 60*60 ); // 60 minutes
            echo "<input type='hidden' name='page_slug' value='{$sPageSlug}' />" . PHP_EOL
                . "<input type='hidden' name='tab_slug' value='{$sTabSlug}' />" . PHP_EOL     
                . "<input type='hidden' name='_is_admin_page_framework' value='{$_sNonce}' />" . PHP_EOL
                . "</form><!-- End Form -->" . PHP_EOL;
            
        }    
    
        /**
         * Retrieves the screen icon output as HTML.
         * 
         * @remark      the screen object is supported in WordPress 3.3 or above.
         * @since       2.0.0
         * @since       3.3.1       Moved from `AdminPageFramework_Page`.
         * @return      string      The screen icon HTML output.
         */     
        private function _getScreenIcon( $sPageSlug ) {

            try {
                $this->_throwScreenIconByURLOrPath( $sPageSlug );
                $this->_throwScreenIconByID( $sPageSlug );             
            } 
            
            // If the user sets a screen icon, this code block will be triggered 
            // and the exception message contains the custom screen icon output.
            catch ( Exception $_oException ) {
                
                return $_oException->getMessage();
                
            }
            
            // Otherwise, return the default one.
            return $this->_getDefaultScreenIcon();
                            
        }
            /**
             * Throws a screen icon output with an image url if set.
             * @since       3.5.3
             * @return      void
             */
            private function _throwScreenIconByURLOrPath( $sPageSlug ) {
                
                // If the icon path is explicitly set, use it.
                if ( ! isset( $this->oProp->aPages[ $sPageSlug ]['href_icon_32x32'] ) ) { 
                    return;
                }
                
                $_aAttributes = array(
                    'style'    => $this->oUtil->generateInlineCSS(
                        array(
                            'background-image' => "url('" . esc_url( $this->oProp->aPages[ $sPageSlug ]['href_icon_32x32'] ) . "')"
                        )
                    )
                );
                
                // Go to the catch clause.
                throw new Exception( 
                    $this->_getScreenIconByAttributes( $_aAttributes ) 
                );
            
            }
            /**
             * Throws a screen icon output with an ID if set.
             * @since       3.5.3
             * @return      void
             */
            private function _throwScreenIconByID( $sPageSlug ) {
                
                // If the screen icon ID is explicitly set, use it.
                if ( ! isset( $this->oProp->aPages[ $sPageSlug ]['screen_icon_id'] ) ) { 
                    return;
                }
                
                $_aAttributes = array(
                    'id'       => "icon-" . $this->oProp->aPages[ $sPageSlug ]['screen_icon_id'],
                );
                
                // Go to the catch clause.
                throw new Exception( 
                    $this->_getScreenIconByAttributes( $_aAttributes ) 
                );                      
            
            }   
            /**
             * Throws a default screen icon output.
             * @since       3.5.3
             * @return      string      
             */
            private function _getDefaultScreenIcon() {            

                $_oScreen           = get_current_screen();
                $_sIconIDAttribute  = $this->_getScreenIDAttribute( $_oScreen );
                $_aAttributes       = array(
                    'class'    => $this->oUtil->getClassAttribute(
                        $this->oUtil->getAOrB(
                            empty( $_sIconIDAttribute ) && $_oScreen->post_type,
                            sanitize_html_class( 'icon32-posts-' . $_oScreen->post_type ),
                            ''
                        ),
                        $this->oUtil->getAOrB(
                            empty( $_sIconIDAttribute ) || $_sIconIDAttribute == $this->oProp->sClassName,
                            'generic',  // the default value
                            ''
                        )
                    ),
                    'id'       => "icon-" . $_sIconIDAttribute,                
                );
                return $this->_getScreenIconByAttributes( $_aAttributes );
            
            }           
                /**
                 * Retrieves the screen ID attribute from the given screen object.
                 * 
                 * @since       2.0.0
                 * @since       3.3.1       Moved from `AdminPageFramework_Page`.
                 */     
                private function _getScreenIDAttribute( $oScreen ) {
                    
                    if ( ! empty( $oScreen->parent_base ) ) {
                        return $oScreen->parent_base;
                    }
                    if ( 'page' === $oScreen->post_type ) {
                        return 'edit-pages';     
                    }
                    return esc_attr( $oScreen->base );
                    
                }
                
                /**
                 * Returns a screen icon HTML output by the given attributes array.
                 * 
                 * @internal
                 * @since       3.5.3
                 * @return      string
                 */
                private function _getScreenIconByAttributes( array $aAttributes ) {
                    
                    $aAttributes['class'] = $this->oUtil->getClassAttribute( 
                        'icon32',   // required for a screen icon container element.
                        $this->oUtil->getElement( $aAttributes, 'class' )
                    );
                    return "<div " . $this->oUtil->getAttributes( $aAttributes ) . ">"
                        . "<br />"
                    . "</div>";
                    
                }                

        /**
         * Retrieves the output of page heading tab navigation bar as HTML.
         * 
         * @since       2.0.0
         * @since       3.3.1       Moved from `AdminPageFramework_Page`.
         * @since       3.5.3       Deprecated the `$aOutput` parameter.
         * @return      string      the output of page heading tabs.
         */         
        private function _getPageHeadingTabs( $sCurrentPageSlug, $sTag='h2' ) {

            $_aPage = $this->oProp->aPages[ $sCurrentPageSlug ];
        
            // If the page title is disabled, return an empty string.
            if ( ! $_aPage[ 'show_page_title' ] ) { 
                return ""; 
            }
            
            $sTag  = $this->_getPageHeadingTabTag( $sTag, $_aPage );
            
            // If the page heading tab visibility is disabled, or only one page is registered, return the title.
            if ( ! $_aPage[ 'show_page_heading_tabs' ] || count( $this->oProp->aPages ) == 1 ) {
                return "<{$sTag}>" 
                        . $_aPage['title'] 
                    . "</{$sTag}>";     
            }

            return $this->_getPageHeadingtabNavigationBar( 
                $this->oProp->aPages, 
                $sTag,
                $sCurrentPageSlug 
            );
                        
        }
            /**
             * Returns the HTML page heading tab tag.
             * @since       3.5.3
             * @internal
             * @return      string      the HTML page heading tab tag.
             */
            private function _getPageHeadingTabTag( $sTag, array $aPage ) {
                return tag_escape( 
                    $aPage[ 'page_heading_tab_tag' ]
                        ? $aPage[ 'page_heading_tab_tag' ]
                        : $sTag
                );
            }    
            
            /**
             * Returns the HTML page heading tab navigation bar output.
             * 
             * @internal
             * @since       3.5.3
             * @return      string      the HTML page heading tab navigation bar output.
             */
            private function _getPageHeadingtabNavigationBar( array $aPages, $sTag, $sCurrentPageSlug ) {
                
                $_oTabBar = new AdminPageFramework_TabNavigationBar(
                    $aPages,     // tab items
                    $sCurrentPageSlug, // active tab slug
                    $sTag,       // container tag
                    array(       // container attributes
                        // 'class' => '...',
                    ),
                    array(       // callbacks
                        'format'    => array( $this, '_replyToFormatNavigationTabItem_PageHeadingTab' ),
                    )
                );            
                $_sTabBar = $_oTabBar->get();
                return $_sTabBar
                    ? "<div class='admin-page-framework-page-heading-tab'>"
                            . $_sTabBar
                        . "</div>"
                    : '';                
            }
                /**
                 * Formats navigation tab array of page-heading tabs.
                 * 
                 * @callback        function        AdminPageFramework_TabNavigationBar::_getFormattedTab
                 * @return          array
                 * @since           3.5.10
                 */            
                public function _replyToFormatNavigationTabItem_PageHeadingTab( $aSubPage, $aStructure, $aPages, $aArguments=array() ) {                    
                    switch( $aSubPage['type'] ) {
                        case 'link':
                            return $this->_getFormattedPageHeadingtabNavigationBarLinkItem( $aSubPage, $aStructure );
                        default:
                            return $this->_getFormattedPageHeadingtabNavigationBarPageItem( $aSubPage, $aStructure );
                    }                    
                    return $aSubPage + $aStructure;
                }
                    /**
                     * Returns the HTML output of a navigation bar item of a sub-page.
                     * @since       3.5.3
                     * @internal
                     * @return      string      the HTML output of a navigation bar item of a sub-page.
                     */
                    private function _getFormattedPageHeadingtabNavigationBarPageItem( array $aSubPage, $aStructure ) {
                        
                        if ( ! isset( $aSubPage[ 'page_slug' ] ) ) {
                            return array();
                        }
                        if ( ! $aSubPage[ 'show_page_heading_tab' ] ) {
                            return array();
                        }
                        return array(
                            'slug'  => $aSubPage[ 'page_slug' ],
                            'title' => $aSubPage[ 'title' ],
                            'href'  => esc_url( 
                                $this->oUtil->getQueryAdminURL( 
                                    array( 
                                        'page'  => $aSubPage[ 'page_slug' ], 
                                        'tab'   => false, 
                                    ), 
                                    $this->oProp->aDisallowedQueryKeys 
                                ) 
                            ),
                        ) 
                        + $aSubPage
                        + array( 'class' => null )
                        + $aStructure;
                        
                    }
                    /**
                     * Returns a formatted tab array for a navigation bar item of a link for page heading tabs.
                     * @since       3.5.10
                     * @since       
                     * @internal
                     * @return      array      the HTML output of a navigation bar item of a link.
                     */                    
                    private function _getFormattedPageHeadingtabNavigationBarLinkItem( array $aSubPage, $aStructure ) {
                        
                        if ( ! isset( $aSubPage[ 'href' ] ) ) {
                            return array();
                        }
                        if ( ! $aSubPage[ 'show_page_heading_tab' ] ) {
                            return array();
                        }                        
                        $aSubPage = array(
                            'slug'  => $aSubPage[ 'href' ],
                            'title' => $aSubPage[ 'title' ],
                            'href'  => esc_url( $aSubPage[ 'href' ] ),
                        ) 
                            + $aSubPage
                            + array( 'class' => null )
                            + $aStructure;
                            
                        $aSubPage[ 'class' ] = trim( $aSubPage[ 'class' ] . ' link' );
                        return $aSubPage;
                    }                
      
        /**
         * Retrieves the output of in-page tab navigation tabs bar as HTML.
         * 
         * @since       2.0.0
         * @since       3.3.1        Moved from `AdminPageFramework_Page`.
         * @since       3.5.0        Deprecated the third $aOutput parameter.
         * @return      string       The output of in-page tabs.
         * @internal
         */     
        private function _getInPageTabs( $sCurrentPageSlug, $sTag='h3' ) {
            
            // If in-page tabs are not set, return an empty string.
            $_aInPageTabs = $this->oUtil->getElement( $this->oProp->aInPageTabs, $sCurrentPageSlug, array() );
            if ( empty( $_aInPageTabs ) ) { 
                return ''; 
            }
            
            $_aPage             = $this->oProp->aPages[ $sCurrentPageSlug ];
            $_sCurrentTabSlug   = $this->_getCurrentTabSlug( $sCurrentPageSlug );
            $_sTag              = $this->_getInPageTabTag( $sTag, $_aPage );
             
            // If the in-page tabs' visibility is set to false, returns the title.
            if ( ! $_aPage[ 'show_in_page_tabs' ] ) {
                return isset( $_aInPageTabs[ $_sCurrentTabSlug ][ 'title' ] ) 
                    ? "<{$_sTag}>" 
                            . $_aInPageTabs[ $_sCurrentTabSlug ][ 'title' ]
                        . "</{$_sTag}>" 
                    : "";
            }         
            
            return $this->_getInPageTabNavigationBar(
                $_aInPageTabs,
                $_sCurrentTabSlug,
                $sCurrentPageSlug,
                $_sTag
            );
                        
        }
            /**
             * Generates in-page tab navigation bar HTML output.
             * 
             * @since       3.5.3
             * @internal
             * @return      string      the in-page tab navigation bar output.
             */
            private function _getInPageTabNavigationBar( array $aTabs, $sActiveTab, $sCurrentPageSlug, $sTag ) {
                
                $_oTabBar = new AdminPageFramework_TabNavigationBar(
                    $aTabs,      // tabs
                    $sActiveTab, // active tab slug
                    $sTag,       // container tag
                    array(       // container attributes
                        'class' => 'in-page-tab',
                    ),
                    array(       // callbacks
                        'format'    => array( $this, '_replyToFormatNavigationTabItem_InPageTab' ),
                        
                        // Custom arguments to pass to the callback functions
                        'arguments' => array(
                            'page_slug'         => $sCurrentPageSlug,
                        ),                        
                    )
                );            
                $_sTabBar = $_oTabBar->get();
                return $_sTabBar
                    ? "<div class='admin-page-framework-in-page-tab'>"
                            . $_sTabBar
                        . "</div>"
                    : '';
                
            }
                /**
                 * Formats navigation tab array of in-page tabs.
                 * @callback        function        AdminPageFramework_TabNavigationBar::_getFormattedTab
                 * @return          array
                 * @since           3.5.10
                 */
                public function _replyToFormatNavigationTabItem_InPageTab( array $aTab, array $aStructure, array $aTabs, array $aArguments=array() ) {
                    
                    $_sSlug = isset( $aTab[ 'parent_tab_slug' ], $aTabs[ $aTab[ 'parent_tab_slug' ] ] )
                        ? $aTab[ 'parent_tab_slug' ] 
                        : $aTab[ 'tab_slug' ];

                    // If it's hidden, skip
                    if ( ! $aTab[ 'show_in_page_tab' ] ) {
                        return array();
                    }
                             
                    $aTab = array(
                        'slug'  => $_sSlug,
                        'title' => $aTabs[ $_sSlug ][ 'title' ],
                        'href'  => $aTab[ 'disabled' ]
                            ? null
                            : esc_url( 
                                $this->oUtil->getElement( 
                                    $aTab, 
                                    'url',  // if the 'url' argument is set, use it. Otherwise, use the below gnerated url.
                                    $this->oUtil->getQueryAdminURL( 
                                        array( 
                                            'page'  => $aArguments[ 'page_slug' ],
                                            'tab'   => $_sSlug,
                                        ), 
                                        $this->oProp->aDisallowedQueryKeys 
                                    )
                                )
                            ),
                    ) + $this->oUtil->uniteArrays(
                        $aTab,
                        array(
                            'attributes'    => array(
                                // 3.5.7+ Added for acceptance tests 
                                'data-tab-slug' => $_sSlug,     
                            ),
                        ),
                        $aStructure
                    );
                      
                    return $aTab;
                    
                }

            /**
             * Returns the in-page tab tag.
             * 
             * @since       3.5.3
             * @return      string      the in-page tab tag.
             * @internal
             */
            private function _getInPageTabTag( $sTag, array $aPage ) {
                return tag_escape(
                    $aPage[ 'in_page_tab_tag' ]
                        ? $aPage[ 'in_page_tab_tag' ]
                        : $sTag                
                ); 
            }                        
            /**
             * Determines the currently loading in-page tab slug.
             * @since       3.5.3
             * @return      string      the currently loading in-page tab slug.
             * @internal
             */
            private function _getCurrentTabSlug( $sCurrentPageSlug ) {
                
                $_sCurrentTabSlug = $this->oUtil->getElement( 
                    $_GET,
                    'tab', 
                    $this->oProp->getDefaultInPageTab( $sCurrentPageSlug )
                );
                return $this->_getParentTabSlug( 
                    $sCurrentPageSlug, 
                    $_sCurrentTabSlug 
                );  
                
            }
                /**
                 * Retrieves the parent tab slug from the given tab slug.
                 * 
                 * @since       2.0.0
                 * @since       2.1.2       If the parent slug has the show_in_page_tab to be true, it returns an empty string.
                 * @since       3.3.1       Moved from `AdminPageFramework_Page`.
                 * @return      string      the parent tab slug.
                 * @internal
                 */     
                private function _getParentTabSlug( $sPageSlug, $sTabSlug ) {
                    
                    $_sParentTabSlug = $this->oUtil->getElement(
                        $this->oProp->aInPageTabs,
                        array( $sPageSlug, $sTabSlug, 'parent_tab_slug' ),
                        $sTabSlug
                    );
                    return isset( $this->oProp->aInPageTabs[ $sPageSlug ][ $_sParentTabSlug ][ 'show_in_page_tab' ] )
                            && $this->oProp->aInPageTabs[ $sPageSlug ][ $_sParentTabSlug ][ 'show_in_page_tab' ]
                        ? $_sParentTabSlug
                        : '';

                }
                
}