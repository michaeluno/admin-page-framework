<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides base methods and properties for manipulating the contextual help tabs.
 * 
 * @package AdminPageFramework
 * @subpackage HelpPane
 * @since 2.1.0
 * @abstract
 * @internal
 */
abstract class AdminPageFramework_HelpPane_Base extends AdminPageFramework_Debug {
    
    /**
     * Stores the screen object.
     * @var object
     * @since 2.1.0
     */ 
    protected $_oScreen;
    
    function __construct( $oProp ) {
        
        $this->oProp = $oProp;
        $this->oUtil = new AdminPageFramework_WPUtility;

    }
        
    
    /**
     * Sets the contextual help tab.
     * 
     * On contrary to other methods relating to contextual help tabs that just modify the class properties, this finalizes the help tab contents.
     * In other words, the set values here will take effect.
     * 
     * @access protected
     * @remark The sidebar contents in the help pane can be set but if it's called from the meta box class and the page loads in regular post types; the sidebar text may be overridden by the default one.
     * @since 2.1.0
     * @internal
     */  
    protected function _setHelpTab( $sID, $sTitle, $aContents, $aSideBarContents=array() ) {
        
        if ( empty( $aContents ) ) return;
        
        $this->_oScreen = isset( $this->_oScreen ) ? $this->_oScreen : get_current_screen();
        $this->_oScreen->add_help_tab( 
            array(
                'id' => $sID,
                'title' => $sTitle,
                'content' => implode( PHP_EOL, $aContents ),
            ) 
        );     
        
        if ( ! empty( $aSideBarContents ) )
            $this->_oScreen->set_help_sidebar( implode( PHP_EOL, $aSideBarContents ) );
            
    }
    
    /**
     * Encloses the given string with the contextual help specific tag.
     * @since 2.1.0
     * @internal
     */ 
    protected function _formatHelpDescription( $sHelpDescription ) {
        return "<div class='contextual-help-description'>" . $sHelpDescription . "</div>";
    }
}