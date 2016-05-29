<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format form a definition array.
 * 
 * @package     AdminPageFramework
 * @subpackage  Factory/AdminPage/Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Format_SubMenuPage extends AdminPageFramework_Format_Base {
    
    /**
     * Represents the structure of sub-menu page array.
     * 
     * @since       2.0.0
     * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
     * @since       3.6.0       Moved from `AdminPageFramework_Menu_Model`.
     * @remark      Not for the user.
     * @var         array Holds array structure of sub-menu page.
     * @static
     * @internal
     */ 
    static public $aStructure = array(
        'page_slug'                 => null, // (required)
        'type'                      => 'page', // this is used to compare with the link type.
        'title'                     => null, 
        'page_title'                => null,    // (optional) 3.3.0+ When the page title is different from the above 'title' argument, set this.
        'menu_title'                => null,    // (optional) 3.3.0+ When the menu title is different from the above 'title' argument, set this.
        'screen_icon'               => null, // this will become either href_icon_32x32 or screen_icon_id
        'capability'                => null, 
        'order'                     => null,
        'show_page_heading_tab'     => true, // if this is false, the page title won't be displayed in the page heading tab.
        'show_in_menu'              => true, // if this is false, the menu label will not be displayed in the sidebar menu.     
        'href_icon_32x32'           => null,
        'screen_icon_id'            => null,
        // 'show_menu' => null, <-- not sure what this was for.
        'show_page_title'           => null,
        'show_page_heading_tabs'    => null,
        'show_in_page_tabs'         => null,
        'in_page_tab_tag'           => null,
        'page_heading_tab_tag'      => null,
        'disabled'                  => null, // 3.5.10+ (boolean) If false, in the page heading navigation tab, the link will be disabled.
        'attributes'                => null, // 3.5.10+ (array) Applied to navigation tab element.
        'style'                     => null, // 3.6.0+ (string|array) The path or url of a stylesheet which gets loaded in the head tag.
        'script'                    => null, // 3.6.3+ (string|array) The path or url of a JavaScript script which gets loaded in the head tag.
    );    
    
    /**
     * Stores the ID selector names for screen icons. `generic` is not available in WordPress v3.4.x.
     * 
     * @since       2.0.0
     * @since       3.3.1       Moved from `AdminPageFramework_Page`.
     * @since       3.6.0       Moved from `AdminPageFramework_Page_Model`.
     * @var         array
     * @static
     * @access      public
     * @internal
     */     
    static public $aScreenIconIDs = array(
        'edit', 'post', 'index', 'media', 'upload', 'link-manager', 'link', 'link-category', 
        'edit-pages', 'page', 'edit-comments', 'themes', 'plugins', 'users', 'profile', 
        'user-edit', 'tools', 'admin', 'options-general', 'ms-admin', 'generic',
    );        
    
    /**
     * Stores an in-page tab definition.
     */
    public $aSubMenuPage = array();
    
    public $oFactory;
    
    /**
     * The parsed index. 
     * 
     * This should represents the order of the registered sub-menu item as it is used to calculate the sub-menu position. 
     * @since       3.7.4
     */
    public $iParsedIndex = 1;
    
    /**
     * Sets up properties
     */
    public function __construct( /* $aSubMenuPage, $oFactory, $iParsedIndex */ ) {
     
        $_aParameters = func_get_args() + array( 
            $this->aSubMenuPage, 
            $this->oFactory,
            $this->iParsedIndex
        );
        $this->aSubMenuPage  = $_aParameters[ 0 ];
        $this->oFactory      = $_aParameters[ 1 ];
        $this->iParsedIndex  = $_aParameters[ 2 ]; 
        
    }

    /**
     * 
     * @return      array       The formatted subject array.
     */
    public function get() {
        return $this->_getFormattedSubMenuPageArray( $this->aSubMenuPage );
    }
     
        /**
         * Formats the given sub-menu page array.
         * 
         * @since       3.0.0
         * @since       3.3.1       Changed the scope to `protected` from `private` as the method is called from a different class.
         * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
         * @since       3.6.0       Moved from `AdminPageFramework_Menu_Model`.
         * @return      array
         * @internal
         */
        protected function _getFormattedSubMenuPageArray( array $aSubMenuPage ) {
            
            $aSubMenuPage = $aSubMenuPage 
                + array(
                    'show_page_title'           => $this->oFactory->oProp->bShowPageTitle,       // boolean
                    'show_page_heading_tabs'    => $this->oFactory->oProp->bShowPageHeadingTabs, // boolean
                    'show_in_page_tabs'         => $this->oFactory->oProp->bShowInPageTabs,      // boolean
                    'in_page_tab_tag'           => $this->oFactory->oProp->sInPageTabTag,        // string
                    'page_heading_tab_tag'      => $this->oFactory->oProp->sPageHeadingTabTag,   // string
                    'capability'                => $this->oFactory->oProp->sCapability,  // 3.6.0+
                )       
                + self::$aStructure;

            $aSubMenuPage[ 'page_slug' ]      = $this->sanitizeSlug( $aSubMenuPage[ 'page_slug' ] );
            $aSubMenuPage[ 'screen_icon_id' ] = trim( 
                $aSubMenuPage[ 'screen_icon_id' ] 
            );
            
            return array( 
                    'href_icon_32x32'   => $aSubMenuPage[ 'screen_icon' ],
                    'screen_icon_id'    => $this->getAOrB(
                        in_array( $aSubMenuPage[ 'screen_icon' ], self::$aScreenIconIDs ),
                        $aSubMenuPage[ 'screen_icon' ],
                        'generic'   
                    ), 
                    'capability'        => $this->getElement( 
                        $aSubMenuPage, 
                        'capability', 
                        $this->oFactory->oProp->sCapability 
                    ),
                    'order'             => $this->getAOrB(
                        is_numeric( $aSubMenuPage[ 'order' ] ),
                        $aSubMenuPage[ 'order' ],
                        $this->iParsedIndex * 10
                    ),
                )
                + $aSubMenuPage;
            
        }    
    
}
