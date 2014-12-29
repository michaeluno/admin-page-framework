<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
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
        <div class="wrap">
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
                        $this->_printMainContent( $sPageSlug, $sTabSlug );
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
            if ( $this->oProp->bEnableForm && $this->oForm->isPageAdded( $sPageSlug ) ) {
     
                $this->aFieldErrors = isset( $this->aFieldErrors ) ? $this->aFieldErrors : $this->_getFieldErrors( $sPageSlug ); 
                $_oFieldsTable = new AdminPageFramework_FormTable( $this->oProp->aFieldTypeDefinitions, $this->aFieldErrors, $this->oMsg );
                
                // @deprecated 3.4.1 the followings are already done in _replyToRegisterSettings().
                // $this->oForm->setCurrentPageSlug( $sPageSlug );
                // $this->oForm->setCurrentTabSlug( $sTabSlug );
                // $this->oForm->applyConditions();
                // $this->oForm->applyFiltersToFields( $this, $this->oProp->sClassName ); // applies filters to the conditioned field definition arrays.
                // $this->oForm->setDynamicElements( $this->oProp->aOptions ); // will update $this->oForm->aConditionedFields
                echo $_oFieldsTable->getFormTables( $this->oForm->aConditionedSections, $this->oForm->aConditionedFields, array( $this, '_replyToGetSectionHeaderOutput' ), array( $this, '_replyToGetFieldOutput' ) );
                
            }     
             
            $_sContent = ob_get_contents(); // assign the content buffer to a variable
            ob_end_clean(); // end buffer and remove the buffer
                        
            // Apply the content filters.
            // @todo call the content() method.
            echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( 'content_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, false ), $_sContent );

            // Do the page actions.
            $this->oUtil->addAndDoActions(
                $this, // the caller object
                $this->oUtil->getFilterArrayByPrefix( 'do_', $this->oProp->sClassName, $sPageSlug, $sTabSlug, true ), // the action hooks
                $this // the argument 1
            );     
            
            if ( $_bIsSideMetaboxExist ) {
                echo "</div><!-- #post-body-content -->";
            }
            echo "</div><!-- .admin-page-framework-content -->";
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
                if ( 'page' == $oScreen->post_type ) {
                    return 'edit-pages';     
                }
                return esc_attr( $oScreen->base );
                
            }

        /**
         * Retrieves the output of page heading tab navigation bar as HTML.
         * 
         * @since       2.0.0
         * @since       3.3.1       Moved from `AdminPageFramework_Page`.
         * @return      string      the output of page heading tabs.
         */         
        private function _getPageHeadingTabs( $sCurrentPageSlug, $sTag='h2', $aOutput=array() ) {
            
            // If the page title is disabled, return an empty string.
            if ( ! $this->oProp->aPages[ $sCurrentPageSlug ][ 'show_page_title' ] ) { return ""; }

            $sTag = $this->oProp->aPages[ $sCurrentPageSlug ][ 'page_heading_tab_tag' ]
                ? $this->oProp->aPages[ $sCurrentPageSlug ][ 'page_heading_tab_tag' ]
                : $sTag;
            $sTag = tag_escape( $sTag );
            
            // If the page heading tab visibility is disabled, or only one page is registered, return the title.
            if ( ! $this->oProp->aPages[ $sCurrentPageSlug ][ 'show_page_heading_tabs' ] || count( $this->oProp->aPages ) == 1 ) {
                return "<{$sTag}>" . $this->oProp->aPages[ $sCurrentPageSlug ]['title'] . "</{$sTag}>";     
            }

            foreach( $this->oProp->aPages as $aSubPage ) {
                
                // For added sub-pages
                if ( isset( $aSubPage['page_slug'] ) && $aSubPage['show_page_heading_tab'] ) {
                    
                    // Check if the current tab number matches the iteration number. If not match, then assign blank; otherwise put the active class name.
                    $aOutput[] = "<a " . $this->oUtil->generateAttributes(
                            array(
                                // 'class' => 'nav-tab ' . ( $sCurrentPageSlug === $aSubPage['page_slug']  ? 'nav-tab-active' : '' ),
                                'class' => $this->oUtil->generateClassAttribute(
                                    'nav-tab',
                                    $sCurrentPageSlug === $aSubPage['page_slug'] ? 'nav-tab-active' : ''
                                ),                                
                                'href'  => esc_url( $this->oUtil->getQueryAdminURL( array( 'page' => $aSubPage['page_slug'], 'tab' => false ), $this->oProp->aDisallowedQueryKeys ) ),
                            )    
                        ) . ">"
                            . $aSubPage['title']
                        . "</a>";
                        
                }
                
                // For added menu links
                if ( 
                    isset( $aSubPage['href'] )
                    && 'link' === $aSubPage['type'] 
                    && $aSubPage['show_page_heading_tab']
                ) {
                    $aOutput[] = "<a " . $this->oUtil->generateAttributes(
                            array(
                                'class' => 'nav-tab link',
                                'href'  => esc_url( $aSubPage['href'] ),
                            )    
                        ) . ">" 
                            . $aSubPage['title']
                        . "</a>";
                }
                
            }     
            return "<div class='admin-page-framework-page-heading-tab'>"
                    ."<{$sTag} class='nav-tab-wrapper'>" 
                        .  implode( '', $aOutput ) 
                    . "</{$sTag}>"
                . "</div>";
            
        }

        /**
         * Retrieves the output of in-page tab navigation bar as HTML.
         * 
         * @since       2.0.0
         * @since       3.3.1       Moved from `AdminPageFramework_Page`.
         * @return      string     the output of in-page tabs.
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
            $sTag = tag_escape( $sTag );
            
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
                                        
                $aOutput[ $sInPageTabSlug ] = "<a " . $this->oUtil->generateAttributes(
                        array(
                            'class' => $this->oUtil->generateClassAttribute(
                                'nav-tab',
                                $sCurrentTabSlug == $sInPageTabSlug ? "nav-tab-active" : "" // check whether the current tab is the active one
                            ),
                            'href'  => esc_url( $this->oUtil->getQueryAdminURL( array( 'page' => $sCurrentPageSlug, 'tab' => $sInPageTabSlug ), $this->oProp->aDisallowedQueryKeys ) ),
                        )    
                    ) . ">"
                        . $this->oProp->aInPageTabs[ $sCurrentPageSlug ][ $sInPageTabSlug ]['title']
                    . "</a>";
            
            }     
            
            return empty( $aOutput )
                ? ""
                : "<div class='admin-page-framework-in-page-tab'>"
                        . "<{$sTag} class='nav-tab-wrapper in-page-tab'>"
                            . implode( '', $aOutput )
                        . "</{$sTag}>"
                    . "</div>";
                
        }

            /**
             * Retrieves the parent tab slug from the given tab slug.
             * 
             * @since       2.0.0
             * @since       2.1.2       If the parent slug has the show_in_page_tab to be true, it returns an empty string.
             * @since       3.3.1       Moved from `AdminPageFramework_Page`.
             * @return      string      the parent tab slug.
             */     
            private function _getParentTabSlug( $sPageSlug, $sTabSlug ) {
                
                $sParentTabSlug = isset( $this->oProp->aInPageTabs[ $sPageSlug ][ $sTabSlug ]['parent_tab_slug'] ) 
                    ? $this->oProp->aInPageTabs[ $sPageSlug ][ $sTabSlug ]['parent_tab_slug']
                    : $sTabSlug;
 
                return isset( $this->oProp->aInPageTabs[ $sPageSlug ][ $sParentTabSlug ]['show_in_page_tab'] ) && $this->oProp->aInPageTabs[ $sPageSlug ][ $sParentTabSlug ]['show_in_page_tab']
                    ? $sParentTabSlug
                    : '';

            }
            
}