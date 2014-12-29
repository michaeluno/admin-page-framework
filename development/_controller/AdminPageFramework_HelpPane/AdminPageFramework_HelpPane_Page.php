<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to manipulate the help screen sections.
 * 
 * @since       2.1.0
 * @since       3.0.0       Become not abstract.
 * @package     AdminPageFramework
 * @subpackage  HelpPane
 * @extends     AdminPageFramework_HelpPane_Base
 * @staticvar   array       $_aStructure_HelpTabUserArray       stores the array structure of the help tab array.
 * @internal
 */
class AdminPageFramework_HelpPane_Page extends AdminPageFramework_HelpPane_Base {
        
    /**
     * Represents the structure of help tab array that is used by the user to set a help tab content.
     * 
     * @since       2.1.0
     * @internal
     */ 
    protected static $_aStructure_HelpTabUserArray = array(
        'page_slug'                 => null, // (mandatory)
        'page_tab_slug'             => null, // (optional)
        'help_tab_title'            => null, // (mandatory)
        'help_tab_id'               => null, // (mandatory)
        'help_tab_content'          => null, // (optional)
        'help_tab_sidebar_content'  => null, // (optional)
    );

    /**
     * Sets up hooks and properties.
     * 
     * @internal
     */
    function __construct( $oProp ) {
        
        parent::__construct( $oProp );
        
        if ( $oProp->bIsAdminAjax ) {
            return;
        }
        
        // The contextual help pane.
        add_action( 'admin_head', array( $this, '_replyToRegisterHelpTabs' ), 200 );     
        
    }
    
    /**
     * Registers help tabs to the help toggle pane.
     * 
     * This adds a user-defined help information into the help screen placed just below the top admin bar.
     * 
     * @remark      The callback of the `admin_head` action hook.
     * @see         http://codex.wordpress.org/Plugin_API/Action_Reference/load-%28page%29
     * @remark      the screen object is supported in WordPress 3.3 or above.
     * @since       2.1.0
     * @internal
     */  
    public function _replyToRegisterHelpTabs() {
            
        $sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
        $sCurrentPageTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : ( isset( $this->oProp->aDefaultInPageTabs[ $sCurrentPageSlug ] ) ? $this->oProp->aDefaultInPageTabs[ $sCurrentPageSlug ] : '' );
        
        if ( empty( $sCurrentPageSlug ) ) return;
        if ( ! $this->oProp->isPageAdded( $sCurrentPageSlug ) ) return;
        
        foreach( $this->oProp->aHelpTabs as $aHelpTab ) {
            
            if ( $sCurrentPageSlug != $aHelpTab['sPageSlug'] ) continue;
            if ( isset( $aHelpTab['sPageTabSlug'] ) && ! empty( $aHelpTab['sPageTabSlug'] ) && $sCurrentPageTabSlug != $aHelpTab['sPageTabSlug'] ) continue;
                
            $this->_setHelpTab( 
                $aHelpTab['sID'], 
                $aHelpTab['sTitle'], 
                $aHelpTab['aContent'], 
                $aHelpTab['aSidebar']
            );
        }
        
    }
    
    /**
     * Adds the given contextual help tab contents into the property.
     * 
     * <h4>Contextual Help Tab Array Structure</h4>
     * <ul>
     *     <li>**page_slug** - (required) the page slug of the page that the contextual help tab and its contents are displayed.</li>
     *     <li>**page_tab_slug** - (optional) the tab slug of the page that the contextual help tab and its contents are displayed.</li>
     *     <li>**help_tab_title** - (required) the title of the contextual help tab.</li>
     *     <li>**help_tab_id** - (required) the id of the contextual help tab.</li>
     *     <li>**help_tab_content** - (optional) the HTML string content of the the contextual help tab.</li>
     *     <li>**help_tab_sidebar_content** - (optional) the HTML string content of the sidebar of the contextual help tab.</li>
     * </ul>
     *  
     * @since       2.1.0
     * @remark      Called when registering setting sections and fields.
     * @remark      This is internal because the main class uses a bypass method to call this method where the user will interact with.
     * @param       array       $aHelpTab The help tab array. The key structure is detailed in the description part.
     * @return      void
     * @internal
     */ 
    public function _addHelpTab( $aHelpTab ) {
        
        // Avoid undefined index warnings.
        $aHelpTab = ( array ) $aHelpTab + self::$_aStructure_HelpTabUserArray;
        
        // If the key is not set, that means the help tab array is not created yet. So create it and go back.
        if ( ! isset( $this->oProp->aHelpTabs[ $aHelpTab['help_tab_id'] ] ) ) {
            $this->oProp->aHelpTabs[ $aHelpTab['help_tab_id'] ] = array(
                'sID'           => $aHelpTab['help_tab_id'],
                'sTitle'        => $aHelpTab['help_tab_title'],
                'aContent'      => ! empty( $aHelpTab['help_tab_content'] ) ? array( $this->_formatHelpDescription( $aHelpTab['help_tab_content'] ) ) : array(),
                'aSidebar'      => ! empty( $aHelpTab['help_tab_sidebar_content'] ) ? array( $this->_formatHelpDescription( $aHelpTab['help_tab_sidebar_content'] ) ) : array(),
                'sPageSlug'     => $aHelpTab['page_slug'],
                'sPageTabSlug'  => $aHelpTab['page_tab_slug'],
            );
            return;
        }

        // This line will be reached if the help tab array is already set. In this case, just append an array element into the keys.
        if ( ! empty( $aHelpTab['help_tab_content'] ) ) {
            $this->oProp->aHelpTabs[ $aHelpTab['help_tab_id'] ]['aContent'][] = $this->_formatHelpDescription( $aHelpTab['help_tab_content'] );
        }
        if ( ! empty( $aHelpTab['help_tab_sidebar_content'] ) ) {
            $this->oProp->aHelpTabs[ $aHelpTab['help_tab_id'] ]['aSidebar'][] = $this->_formatHelpDescription( $aHelpTab['help_tab_sidebar_content'] );
        }
        
    }
    
}