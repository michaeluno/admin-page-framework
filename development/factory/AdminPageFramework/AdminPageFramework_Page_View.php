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
                        
                    $_oFieldsTable = new AdminPageFramework_FormTable(
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
                    . $this->oUtil->generateAttributes(
                        array(
                            'method'    => 'post',
                            'enctype'   => $this->oProp->sFormEncType,
                            'id'        => 'admin-page-framework-form',
                            'action'    => wp_unslash( remove_query_arg( 'settings-updated', $this->oProp->sTargetFormPage ) ),
                        )    
                    ) 
                . " >";
                
            // Embed the '_wp_http_referer' hidden field that is checked in the submit data processing method.
            settings_fields( $this->oProp->sOptionKey );
            
        }
        /**
         * Retrieves the form closing tag.
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
         */     
        private function _getScreenIcon( $sPageSlug ) {

            // If the icon path is explicitly set, use it.
            if ( isset( $this->oProp->aPages[ $sPageSlug ]['href_icon_32x32'] ) ) { 
                return "<div " . $this->oUtil->generateAttributes(
                        array(
                            'class'    => 'icon32',
                            'style'    => $this->oUtil->generateInlineCSS(
                                array(
                                    'background-image' => "url('" . esc_url( $this->oProp->aPages[ $sPageSlug ]['href_icon_32x32'] ) . "')"
                                )
                            )
                        )    
                    ) . ">"
                        . "<br />"
                    . "</div>";
                
            }
            
            // If the screen icon ID is explicitly set, use it.
            if ( isset( $this->oProp->aPages[ $sPageSlug ]['screen_icon_id'] ) ) {
                return "<div " . $this->oUtil->generateAttributes(
                        array(
                            'class'    => 'icon32',
                            'id'       => "icon-" . $this->oProp->aPages[ $sPageSlug ]['screen_icon_id'],
                        )    
                    ) . ">"
                        . "<br />"
                    . "</div>";
            }
                
            // Retrieve the screen object for the current page.
            $_oScreen           = get_current_screen();
            $_sIconIDAttribute  = $this->_getScreenIDAttribute( $_oScreen );
            $_sClass            = 'icon32';
            if ( empty( $_sIconIDAttribute ) && $_oScreen->post_type ) {
                $_sClass .= ' ' . sanitize_html_class( 'icon32-posts-' . $_oScreen->post_type );
            }
            if ( empty( $_sIconIDAttribute ) || $_sIconIDAttribute == $this->oProp->sClassName ) {
                $_sIconIDAttribute = 'generic'; // the default value
            }
            return "<div " . $this->oUtil->generateAttributes(
                    array(
                        'class'    => $_sClass,
                        'id'       => "icon-" . $_sIconIDAttribute,
                    )    
                ) . ">"
                    . "<br />"
                . "</div>";
                
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

                return tag_escape( $aPage[ 'page_heading_tab_tag' ]
                    ? $aPage[ 'page_heading_tab_tag' ]
                    : $sTag
                );
            
            }    
            /**
             * Returns the HTML page heading tab navigation bar output.
             * @since       3.5.3
             * @internal
             * @return      string      the HTML page heading tab navigation bar output.
             */
            private function _getPageHeadingtabNavigationBar( array $aPages, $sTag, $sCurrentPageSlug ) {
                                
                $_aOutput = array();
                foreach( $aPages as $aSubPage ) {   
                    $_aOutput[] = $this->_getPageHeadingtabNavigationBarItem( 
                        $aSubPage, 
                        $sCurrentPageSlug 
                    );                    
                }     
                $_aOutput = array_filter( $_aOutput );
                return empty( $_aOutput )
                    ? ''
                    : "<div class='admin-page-framework-page-heading-tab'>"
                        ."<{$sTag} class='nav-tab-wrapper'>" 
                            .  implode( '', $_aOutput ) 
                        . "</{$sTag}>"
                    . "</div>";                
                
            }       
                /**
                 * Returns the HTML output of an individual page-heading-navigation bar item.
                 * @since       3.5.3
                 * @internal
                 * @return      string      The HTML output of page heading navigation bar item.
                 */
                private function _getPageHeadingtabNavigationBarItem( array $aSubPage, $sCurrentPageSlug ) {
                    
                    switch( $aSubPage['type'] ) {
                        case 'link':
                            return $this->_getPageHeadingtabNavigationBarLinkItem( $aSubPage );
                        default:
                            return $this->_getPageHeadingtabNavigationBarPageItem( $aSubPage, $sCurrentPageSlug );
                    }
                     
                }
                    /**
                     * Returns the HTML output of a navigation bar item of a sub-page.
                     * @since       3.5.3
                     * @internal
                     * @return      string      the HTML output of a navigation bar item of a sub-page.
                     */
                    private function _getPageHeadingtabNavigationBarPageItem( array $aSubPage, $sCurrentPageSlug ) {
                        
                        if ( ! isset( $aSubPage['page_slug'] ) ) {
                            return '';
                        }
                        if ( ! $aSubPage['show_page_heading_tab'] ) {
                            return '';
                        }
                                                    
                        return "<a " . $this->oUtil->generateAttributes(
                                array(
                                    'class' => $this->oUtil->generateClassAttribute(
                                        'nav-tab',
                                        $this->oUtil->getAOrB(
                                            $sCurrentPageSlug === $aSubPage['page_slug'],
                                            'nav-tab-active',
                                            ''
                                        )
                                    ),                                
                                    'href'  => esc_url( 
                                        $this->oUtil->getQueryAdminURL( 
                                            array( 
                                                'page'  => $aSubPage['page_slug'], 
                                                'tab'   => false, 
                                            ), 
                                            $this->oProp->aDisallowedQueryKeys 
                                        ) 
                                    ),
                                )    
                            ) . ">"
                                . $aSubPage['title']
                            . "</a>";                 
                        
                    }
                    /**
                     * Returns the HTML output of a navigation bar item of a link.
                     * @since       3.5.3
                     * @internal
                     * @return      string      the HTML output of a navigation bar item of a link.
                     */                    
                    private function _getPageHeadingtabNavigationBarLinkItem( array $aSubPage ) {
                        
                        if ( ! isset( $aSubPage['href'] ) ) {
                            return '';
                        }
                        if ( ! $aSubPage['show_page_heading_tab'] ) {
                            return '';
                        }                        
                        return "<a " . $this->oUtil->generateAttributes(
                                array(
                                    'class' => 'nav-tab link',
                                    'href'  => esc_url( $aSubPage['href'] ),
                                )    
                            ) . ">" 
                                . $aSubPage['title']
                            . "</a>";
                            
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
                return isset( $_aInPageTabs[ $_sCurrentTabSlug ]['title'] ) 
                    ? "<{$_sTag}>" 
                            . $_aInPageTabs[ $_sCurrentTabSlug ]['title'] 
                        . "</{$_sTag}>" 
                    : "";
            }         
            
            // Get the output.
            return $this->_getInPageTabNavigationBar( 
                $_aInPageTabs, 
                $_sTag, 
                $sCurrentPageSlug, 
                $_sCurrentTabSlug
            );
                        
        }

            /**
             * Generates in-page tab navigation bar HTML output.
             * 
             * @since       3.5.3
             * @internal
             * @return      string      the in-page tab navigation bar output.
             */        
            private function _getInPageTabNavigationBar( $aInPageTabs, $sTag, $sCurrentPageSlug, $sCurrentTabSlug ) {
                
                $_aOutput = array();
                foreach( $aInPageTabs as $_sTabSlug => $_aInPageTab ) {
                    
                    // The parent tab means the root tab when there is a hidden tab that belongs to it. Also check if the specified parent tab exists.
                    $_sInPageTabSlug = isset( $_aInPageTab['parent_tab_slug'], $aInPageTabs[ $_aInPageTab['parent_tab_slug'] ] ) 
                        ? $_aInPageTab['parent_tab_slug'] 
                        : $_aInPageTab['tab_slug'];                    
                    
                    $_aOutput[ $_sInPageTabSlug ] = $this->_getInPageTabNavigationBarItem(
                        $aInPageTabs[ $_sInPageTabSlug ]['title'],
                        $_aInPageTab,
                        $_sInPageTabSlug,
                        $sCurrentPageSlug,
                        $sCurrentTabSlug
                    );
                
                }     
                $_aOutput = array_filter( $_aOutput );
                return empty( $_aOutput )
                    ? ""
                    : "<div class='admin-page-framework-in-page-tab'>"
                            . "<{$sTag} class='nav-tab-wrapper in-page-tab'>"
                                . implode( '', $_aOutput )
                            . "</{$sTag}>"
                        . "</div>";
            
            }        
                /**
                 * Returns each item of in-page tab navigation bar.
                 * @since       3.5.3
                 * @internal
                 * @return      string      The generated in-page tab navigation item.
                 */        
                private function _getInPageTabNavigationBarItem( $sTitle, array $aInPageTab, $sInPageTabSlug, $sCurrentPageSlug, $sCurrentTabSlug ) {
                    
                    // If it's hidden and its parent tab is not set, skip
                    if ( ! $aInPageTab['show_in_page_tab'] && ! isset( $aInPageTab['parent_tab_slug'] ) ) {
                        return '';
                    }
                                                                
                    return "<a " . $this->oUtil->generateAttributes(
                            array(
                                'class' => $this->oUtil->generateClassAttribute(
                                    'nav-tab',
                                    $this->oUtil->getAOrB( 
                                        $sCurrentTabSlug === $sInPageTabSlug, // check whether the current tab is the active one
                                        "nav-tab-active",
                                        ''
                                    )
                                ),
                                'href'  => esc_url( 
                                    $this->oUtil->getElement( 
                                        $aInPageTab, 
                                        'url',
                                        $this->oUtil->getQueryAdminURL( 
                                            array( 
                                                'page'  => $sCurrentPageSlug,
                                                'tab'   => $sInPageTabSlug,
                                            ), 
                                            $this->oProp->aDisallowedQueryKeys 
                                        )
                                    )
                                ),
                            )    
                        ) . ">"
                            . $sTitle
                        . "</a>";

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
                    
                    return isset( $this->oProp->aInPageTabs[ $sPageSlug ][ $_sParentTabSlug ]['show_in_page_tab'] ) && $this->oProp->aInPageTabs[ $sPageSlug ][ $_sParentTabSlug ]['show_in_page_tab']
                        ? $_sParentTabSlug
                        : '';

                }
                
}