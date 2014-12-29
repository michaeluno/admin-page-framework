<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for HTML link elements.
 *
 * @abstract
 * @since 2.0.0
 * @extends AdminPageFramework_Utility
 * @package AdminPageFramework
 * @subpackage Link
 * @internal
 */
abstract class AdminPageFramework_Link_Base extends AdminPageFramework_WPUtility {
        
    /**
     * Sets the default footer text on the left hand side.
     * 
     * @since 2.1.1
     */
    protected function _setFooterInfoLeft( $aScriptInfo, &$sFooterInfoLeft ) {
        
        $sDescription = empty( $aScriptInfo['sDescription'] ) 
            ? ""
            : "&#13;{$aScriptInfo['sDescription']}";
        $sVersion = empty( $aScriptInfo['sVersion'] )
            ? ""
            : "&nbsp;{$aScriptInfo['sVersion']}";
        $sPluginInfo = empty( $aScriptInfo['sURI'] ) 
            ? $aScriptInfo['sName'] 
            : "<a href='{$aScriptInfo['sURI']}' target='_blank' title='{$aScriptInfo['sName']}{$sVersion}{$sDescription}'>{$aScriptInfo['sName']}</a>";
        $sAuthorInfo = empty( $aScriptInfo['sAuthorURI'] )    
            ? $aScriptInfo['sAuthor'] 
            : "<a href='{$aScriptInfo['sAuthorURI']}' target='_blank'>{$aScriptInfo['sAuthor']}</a>";
        $sAuthorInfo = empty( $aScriptInfo['sAuthor'] ) 
            ? $sAuthorInfo 
            : ' by ' . $sAuthorInfo;
        $sFooterInfoLeft =  $sPluginInfo . $sAuthorInfo;
        
    }
    /**
     * Sets the default footer text on the right hand side.
     * 
     * @since 2.1.1
     */    
    protected function _setFooterInfoRight( $aScriptInfo, &$sFooterInfoRight ) {
    
        $sDescription = empty( $aScriptInfo['sDescription'] ) 
            ? ""
            : "&#13;{$aScriptInfo['sDescription']}";
        $sVersion = empty( $aScriptInfo['sVersion'] )
            ? ""
            : "&nbsp;{$aScriptInfo['sVersion']}";     
        $sLibraryInfo = empty( $aScriptInfo['sURI'] ) 
            ? $aScriptInfo['sName'] 
            : "<a href='{$aScriptInfo['sURI']}' target='_blank' title='{$aScriptInfo['sName']}{$sVersion}{$sDescription}'>{$aScriptInfo['sName']}</a>";    
    
        $sFooterInfoRight = $this->oMsg->get( 'powered_by' ) . '&nbsp;' 
            . $sLibraryInfo
            . ", <a href='http://wordpress.org' target='_blank' title='WordPress {$GLOBALS['wp_version']}'>WordPress</a>";
        
    }
}