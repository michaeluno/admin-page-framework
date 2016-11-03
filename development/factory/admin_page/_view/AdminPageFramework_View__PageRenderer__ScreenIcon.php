<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Enqueues page resources set with the `style` and `script` arguments.
 *
 * @abstract
 * @since           3.6.3
 * @package         AdminPageFramework
 * @subpackage      Factory/AdminPage/View
 * @internal
 * @extends         AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_View__PageRenderer__ScreenIcon extends AdminPageFramework_FrameworkUtility {
        
    public $oFactory;
    public $sPageSlug;
    public $sTabSlug;

    /**
     * Sets up properties.
     * @since       3.6.3
     */
    public function __construct( $oFactory, $sPageSlug, $sTabSlug ) {
       
        $this->oFactory         = $oFactory;
        $this->sPageSlug        = $sPageSlug;
        $this->sTabSlug         = $sTabSlug;
        
    }   
    
    /**
     * Returns the screen icon output.
     * @since       3.6.3
     */
    public function get() {
        
        if ( ! $this->_isScreenIconVisible() ) {
            return '';
        }
        return $this->_getScreenIcon( $this->sPageSlug );
        
    }
        /**
         * @since       3.6.3
         * @return      boolean
         */
        private function _isScreenIconVisible() {
            
            $_bShowPageTitle        = $this->getElement(
                $this->oFactory->oProp->aPages,
                array( $this->sPageSlug, 'show_page_title' )
            );
            if ( $_bShowPageTitle ) {
                return true;
            }          
            
            $_bShowPageHeadingTabs = $this->getElement(
                $this->oFactory->oProp->aPages,
                array( $this->sPageSlug, 'show_page_heading_tabs' )
            );  
            if ( $_bShowPageHeadingTabs ) {
                return true;
            }
            
            $_bShowInPageTabs = $this->getElement(
                $this->oFactory->oProp->aPages,
                array( $this->sPageSlug, 'show_in_page_tabs' )
            );            
            if ( $_bShowInPageTabs ) {
                return true;
            }            
            
            $_bShowInPageTab = $this->getElementAsArray(
                $this->oFactory->oProp->aInPageTabs,
                array( $this->sPageSlug, $this->sTabSlug, 'show_in_page_tab' ),
                false
            );        
            $_sInPageTabTitle = $this->getElement(
                $this->oFactory->oProp->aInPageTabs,
                array( $this->sPageSlug, $this->sTabSlug, 'title' )
            );            
            if ( $_bShowInPageTab && $_sInPageTabTitle ) {
                return true;
            }            
                
        }

    /**
     * Retrieves the screen icon output as HTML.
     * 
     * @remark      the screen object is supported in WordPress 3.3 or above.
     * @since       2.0.0
     * @since       3.3.1       Moved from `AdminPageFramework_Page`.
     * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
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
         * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
         * @return      void
         */
        private function _throwScreenIconByURLOrPath( $sPageSlug ) {
                        
            $_sScreenIconPath = $this->getElement(
                $this->oFactory->oProp->aPages,
                array( $sPageSlug, 'href_icon_32x32' ),
                ''
            );
            if ( ! $_sScreenIconPath ) {
                return;
            }
            $_sScreenIconPath = $this->getResolvedSRC( 
                $_sScreenIconPath, 
                true 
            );
            $_aAttributes = array(
                'style'    => $this->getInlineCSS(
                    array(
                        'background-image' => "url('" . esc_url( $_sScreenIconPath ) . "')"
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
         * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
         * @return      void
         */
        private function _throwScreenIconByID( $sPageSlug ) {
            
            $_sScreenIconID = $this->getElement(
                $this->oFactory->oProp->aPages,
                array( $sPageSlug, 'screen_icon_id' ),
                ''
            );
            if ( ! $_sScreenIconID ) {
                return;
            }
            
            $_aAttributes = array(
                'id'       => "icon-" . $_sScreenIconID,
            );
            
            // Go to the catch clause.
            throw new Exception( 
                $this->_getScreenIconByAttributes( $_aAttributes ) 
            );                      
        
        }   
        /**
         * Throws a default screen icon output.
         * @since       3.5.3
         * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
         * @return      string      
         */
        private function _getDefaultScreenIcon() {            

            $_oScreen           = get_current_screen();
            $_sIconIDAttribute  = $this->_getScreenIDAttribute( $_oScreen );
            $_aAttributes       = array(
                'class'    => $this->getClassAttribute(
                    $this->getAOrB(
                        empty( $_sIconIDAttribute ) && $_oScreen->post_type,
                        sanitize_html_class( 'icon32-posts-' . $_oScreen->post_type ),
                        ''
                    ),
                    $this->getAOrB(
                        empty( $_sIconIDAttribute ) || $_sIconIDAttribute == $this->oFactory->oProp->sClassName,
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
             * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
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
             * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
             * @return      string
             */
            private function _getScreenIconByAttributes( array $aAttributes ) {
                
                $aAttributes[ 'class' ] = $this->getClassAttribute( 
                    'icon32',   // required for a screen icon container element.
                    $this->getElement( $aAttributes, 'class' )
                );
                return "<div " . $this->getAttributes( $aAttributes ) . ">"
                        . "<br />"
                    . "</div>";
                
            }                


                
}
