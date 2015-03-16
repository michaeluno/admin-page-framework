<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for HTML link elements.
 *
 * @abstract
 * @since       2.0.0
 * @extends     AdminPageFramework_Utility
 * @package     AdminPageFramework
 * @subpackage  Link
 * @internal
 */
abstract class AdminPageFramework_Link_Base extends AdminPageFramework_WPUtility {
     
    /**
     * Sets up hooks and properties.
     */ 
    public function __construct( $oProp, $oMsg=null ) {
        
        if ( ! $this->_isLoadable( $oProp ) ) {
            return;
        }
                
        $this->oProp    = $oProp;
        $this->oMsg     = $oMsg;        
        
        add_action( 'in_admin_footer', array( $this, '_replyToSetFooterInfo' ) );
        
        // Add an action link in the plugin listing page
        if ( 'plugins.php' === $this->oProp->sPageNow && 'plugin' === $this->oProp->aScriptInfo['sType'] ) {
            add_filter( 
                'plugin_action_links_' . plugin_basename( $this->oProp->aScriptInfo['sPath'] ),
                array( $this, '_replyToAddSettingsLinkInPluginListingPage' ), 
                20     // set a lower priority so that the link will be embedded at the beginning ( the most left hand side ).
            );     
        }        
        
    }
        /**
         * Determines if construction the object should be performed or not.
         * 
         * @since       3.5.5
         * @return      boolean
         */
        private function _isLoadable( $oProp ) {
            if ( ! $oProp->bIsAdmin ) { 
                return false;
            }
            if ( $oProp->bIsAdminAjax ) {
                return false;
            }
            return true;            
        }
        
    /**
     * Sets up footer information.
     * 
     * @since           3.5.5
     * @callback        action      in_admin_footer
     */
    public function _replyToSetFooterInfo() {

        $this->_setDefaultFooterText();
        $this->_setFooterHooks();
           
    }              
       
        /**
         * Set the default footer text values.
         * @internal        
         * @since       3.5.5
         * @return      void
         */
        protected function _setDefaultFooterText() {
        
            $this->oProp->aFooterInfo['sLeft'] = str_replace( 
                '__SCRIPT_CREDIT__', 
                $this->_getFooterInfoLeft( $this->oProp->aScriptInfo ),
                $this->oProp->aFooterInfo['sLeft']
            );
            $this->oProp->aFooterInfo['sRight'] = str_replace(
                '__FRAMEWORK_CREDIT__',
                $this->_getFooterInfoRight( $this->oProp->_getLibraryData() ),
                $this->oProp->aFooterInfo['sRight']
            );
            
        }
            /**
             * Sets the default footer text on the left hand side.
             * 
             * @since       2.1.1
             * @since       3.5.5       Changed the name from `_setFooterInfoLeft()` and dropped the second parameter.
             * @return      string
             */
            private function _getFooterInfoLeft( $aScriptInfo ) {

                $_sDescription = $this->getAOrB(
                    empty( $aScriptInfo['sDescription'] ),
                    '',
                    "&#13;{$aScriptInfo['sDescription']}"
                );
                $_sVersion = $this->getAOrB(
                    empty( $aScriptInfo['sVersion'] ),
                    '',
                    "&nbsp;{$aScriptInfo['sVersion']}"
                );
                $_sPluginInfo = $this->getAOrB(
                    empty( $aScriptInfo['sURI'] ),
                    $aScriptInfo['sName'],
                    $this->generateHTMLTag( 
                        'a', 
                        array(
                            'href'      => $aScriptInfo['sURI'],
                            'target'    => '_blank',
                            'title'     => $aScriptInfo['sName'] . $_sVersion . $_sDescription 
                        ), 
                        $aScriptInfo['sName'] 
                    )    
                );

                $_sAuthorInfo = $this->getAOrB(
                    empty( $aScriptInfo['sAuthorURI'] ),
                    '',
                    $this->generateHTMLTag( 
                        'a', 
                        array(
                            'href'      => $aScriptInfo['sAuthorURI'],
                            'target'    => '_blank',
                            'title'     => $aScriptInfo['sAuthor'],
                        ), 
                        $aScriptInfo['sAuthor']
                    )                
                );
                $_sAuthorInfo = $this->getAOrB(
                    empty( $aScriptInfo['sAuthor'] ),
                    $_sAuthorInfo,
                    ' by ' . $_sAuthorInfo
                );
                
                // Enclosing the output in a span tag as the outer element is a '<p>' tag. So this cannot be div.
                // 3.5.7+ Added the class attribute for acceptance testing
                return "<span class='apf-script-info'>"  
                        . $_sPluginInfo . $_sAuthorInfo
                    . "</span>";
          
            }
            /**
             * Sets the default footer text on the right hand side.
             * 
             * @since       2.1.1
             * @since       3.5.5       Changed the name from `_setFooterInfoRight()` and dropped the second parameter.
             * @return      string
             */    
            private function _getFooterInfoRight( $aScriptInfo ) {

                $_sDescription = $this->getAOrB(
                    empty( $aScriptInfo['sDescription'] ),
                    '',
                    "&#13;{$aScriptInfo['sDescription']}"
                );
                $_sVersion = $this->getAOrB(
                    empty( $aScriptInfo['sVersion'] ),
                    '',
                    "&nbsp;{$aScriptInfo['sVersion']}"
                );
                $_sLibraryInfo = $this->getAOrB(
                    empty( $aScriptInfo['sURI'] ),
                    $aScriptInfo['sName'],
                    $this->generateHTMLTag( 
                        'a', 
                        array(
                            'href'      => $aScriptInfo['sURI'],
                            'target'    => '_blank',
                            'title'     => $aScriptInfo['sName'] . $_sVersion . $_sDescription,
                        ), 
                        $aScriptInfo['sName']
                    )                   
                );
                
                // Update the variable
                return "<span class='apf-credit'>" // 3.5.7+ added 'apf-credit' class attribute for acceptance testing
                    . $this->oMsg->get( 'powered_by' ) . '&nbsp;' 
                    . $_sLibraryInfo
                    . ",&nbsp;"
                    . $this->generateHTMLTag( 
                        'a', 
                        array(
                            'href'      => 'https://wordpress.org',
                            'target'    => '_blank',
                            'title'     => 'WordPress' . $GLOBALS['wp_version']
                        ), 
                        'WordPress'
                    )
                    . "</span>";
                    
            }        
        
        /**
         * Sets up hooks to insert admin footer text strings.
         * @internal
         * @since       3.5.5
         * @return      void
         */
        protected function _setFooterHooks() {
            
            add_filter( 'admin_footer_text' , array( $this, '_replyToAddInfoInFooterLeft' ) );
            add_filter( 'update_footer', array( $this, '_replyToAddInfoInFooterRight' ), 11 );
            
        }             
            /**
             * Inserts the left footer text.
             * @since       2.0.0
             * @since       3.5.5       Moved from `AdminPageFramework_Link_PostType`.
             * @remark      The page link class will override this method.
             * @callback    filter      admin_footer_text
             * @internal
             */ 
            public function _replyToAddInfoInFooterLeft( $sLinkHTML='' ) {

                $sLinkHTML = empty( $this->oProp->aScriptInfo['sName'] )
                    ? $sLinkHTML
                    : $this->oProp->aFooterInfo['sLeft'];
             
                return $this->addAndApplyFilters( 
                    $this->oProp->_getCallerObject(), 
                    'footer_left_' . $this->oProp->sClassName, 
                    $sLinkHTML
                );
             
            }
            /**
             * Inserts the right footer text.
             * @since       2.0.0
             * @since       3.5.5       Moved from `AdminPageFramework_Link_PostType`.
             * @remark      The page link class will override this method.
             * @callback    filter      admin_footer_text
             * @internal
             */     
            public function _replyToAddInfoInFooterRight( $sLinkHTML='' ) {
                return $this->addAndApplyFilters( 
                    $this->oProp->_getCallerObject(), 
                    'footer_right_' . $this->oProp->sClassName, 
                    $this->oProp->aFooterInfo['sRight']
                );                
            }       
       
}