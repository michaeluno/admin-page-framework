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
     * Sets the default footer text on the left hand side.
     * 
     * @since       2.1.1
     */
    protected function _setFooterInfoLeft( $aScriptInfo, &$sFooterInfoLeft ) {

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
        
        // Update the variable
        $sFooterInfoLeft =  $_sPluginInfo . $_sAuthorInfo;
        
    }
    /**
     * Sets the default footer text on the right hand side.
     * 
     * @since 2.1.1
     */    
    protected function _setFooterInfoRight( $aScriptInfo, &$sFooterInfoRight ) {
// var_dump( $aScriptInfo );
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
        $sFooterInfoRight = $this->oMsg->get( 'powered_by' ) . '&nbsp;' 
            . $_sLibraryInfo
            . ",&nbsp;"
            . $this->generateHTMLTag( 
                'a', 
                array(
                    'href'      => 'http://wordpress.org',
                    'target'    => '_blank',
                    'title'     => 'WordPress' . $GLOBALS['wp_version']
                ), 
                'WordPress'
            );
    }
}