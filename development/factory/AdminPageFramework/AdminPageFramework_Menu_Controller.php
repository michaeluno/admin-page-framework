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
 * @since           2.0.0
 * @since           3.3.1       Changed the name from `AdminPageFramework_Menu`.
 * @extends         AdminPageFramework_Menu_View
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 */
abstract class AdminPageFramework_Menu_Controller extends AdminPageFramework_Menu_View {
             
     /**
      * Registers necessary callbacks and sets up properties.
      * 
      * @internal
      */
    function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {
        
        parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain );
        
        if ( $this->oProp->bIsAdminAjax ) {
            return;
        }
        
        add_action( 'admin_menu', array( $this, '_replyToBuildMenu' ), 98 );     
    } 
     
    /**
     * Sets to which top level page is going to be adding sub-pages.
     * 
     * <h4>Example</h4>
     * <code>
     * $this->setRootMenuPage( 'Settings' );
     * </code>
     * <code>$this->setRootMenuPage( 
     *     'APF Form',
     *     plugins_url( 'image/screen_icon32x32.jpg', __FILE__ )
     * );</code>
     * 
     * @acecss      public
     * @since       2.0.0
     * @since       2.1.6       The $sIcon16x16 parameter accepts a file path.
     * @since       3.0.0       The scope was changed to public from protected.
     * @remark      Only one root page can be set per one class instance.
     * @param       string      If the method cannot find the passed string from the following listed items, it will create a top level menu item with the passed string. ( case insensitive )
     * <blockquote>Dashboard, Posts, Media, Links, Pages, Comments, Appearance, Plugins, Users, Tools, Settings, Network Admin</blockquote>
     * @param       string      (optional) the source of menu icon with either of the following forms:
     * <ul>
     *  <li>the URL of the menu icon with the size of 16 by 16 in pixel.</li>
     *  <li>the file path of the menu icon with the size of 16 by 16 in pixel.</li>
     *  <li>the name of a Dashicons helper class to use a font icon, e.g. `dashicons-editor-customchar`.</li>
     *  <li>the string, 'none', to leave div.wp-menu-image empty so an icon can be added via CSS.</li>
     *  <li>a base64-encoded SVG using a data URI, which will be colored to match the color scheme. This should begin with 'data:image/svg+xml;base64,'.</li>
     * </ul>
     * @param       string      (optional) the position number that is passed to the <var>$position</var> parameter of the <a href="http://codex.wordpress.org/Function_Reference/add_menu_page">add_menu_page()</a> function.
     * @return      void
     */
    public function setRootMenuPage( $sRootMenuLabel, $sIcon16x16=null, $iMenuPosition=null ) {

        $sRootMenuLabel = trim( $sRootMenuLabel );
        $_sSlug         = $this->_isBuiltInMenuItem( $sRootMenuLabel ); // if true, this method returns the slug
        $this->oProp->aRootMenu = array(
            'sTitle'        => $sRootMenuLabel,
            'sPageSlug'     => $_sSlug ? $_sSlug : $this->oProp->sClassName,    
            'sIcon16x16'    => $this->oUtil->resolveSRC( $sIcon16x16 ),
            'iPosition'     => $iMenuPosition,
            'fCreateRoot'   => $_sSlug ? false : true,
        );    
                    
    }
        /**
         * Checks if a menu item is a WordPress built-in menu item from the given menu label.
         * 
         * @since       2.0.0
         * @internal
         * @return      void|string Returns the associated slug string, if true.
         */ 
        private function _isBuiltInMenuItem( $sMenuLabel ) {
            
            $_sMenuLabelLower = strtolower( $sMenuLabel );
            if ( array_key_exists( $_sMenuLabelLower, $this->_aBuiltInRootMenuSlugs ) )
                return $this->_aBuiltInRootMenuSlugs[ $_sMenuLabelLower ];
            
        }    

    /**
     * Sets the top level menu page by page slug.
     * 
     * The page should be already created or scheduled to be created separately.
     * 
     * <h4>Example</h4>
     * <code>
     *  $this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );
     * </code>
     * 
     * @since       2.0.0
     * @since       3.0.0       The scope was changed to public from protected.
     * @access      public
     * @param       string      The page slug of the top-level root page.
     * @return      void
     */ 
    public function setRootMenuPageBySlug( $sRootMenuSlug ) {
        
        $this->oProp->aRootMenu['sPageSlug'] = $sRootMenuSlug; // do not sanitize the slug here because post types includes a question mark.
        $this->oProp->aRootMenu['fCreateRoot'] = false; // indicates to use an existing menu item. 
        
    }
    
    /**
    * Adds sub-menu items on the left sidebar menu of the administration panel. 
    * 
    * It supports pages and links. Each of them has the specific array structure.
    * 
    * <h4>Example</h4>
    * <code>$this->addSubMenuItems(
    *       array(
    *           'title'         => 'Various Form Fields',
    *           'page_slug'     => 'first_page',
    *           'screen_icon'   => 'options-general',
    *       ),
    *       array(
    *           'title'         => 'Manage Options',
    *           'page_slug'     => 'second_page',
    *           'screen_icon'   => 'link-manager',
    *       ),
    *       array(
    *           'title'         => 'Google',
    *           'href'          => 'http://www.google.com',    
    *           'show_page_heading_tab' => false, // this removes the title from the page heading tabs.
    *       )
    * );</code>
    * 
    * @since        2.0.0
    * @since        3.0.0       Changed the scope to public.
    * @remark       The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
    * @remark       Accepts variadic parameters; the number of accepted parameters are not limited to three.
    * @param        array       $aSubMenuItem1      a first sub-menu array. A sub-menu array can be a link or a page. For the specifications of the array structures and its arguments, refer to the parameter section of the `addSubMenuItem()` method.
    * @param        array       $aSubMenuItem2      (optional) a second sub-menu array.
    * @param        array       $_and_more          (optional) a third and add items as many as necessary with next parameters.
    * @access       public
    * @return       void
    */     
    public function addSubMenuItems( $aSubMenuItem1, $aSubMenuItem2=null, $_and_more=null ) {
        foreach ( func_get_args() as $aSubMenuItem ) {
            $this->addSubMenuItem( $aSubMenuItem );     
        }
    }
    
    /**
    * Adds the given sub-menu item on the left sidebar menu of the administration panel.
    * 
    * It supports pages and links. Each of them has the specific array structure.
    * 
    * <h4>Example</h4>
    * <code>$this->addSubMenuItem(
    *       array(
    *           'title'         => 'Read Me',
    *           'menu_title'    => 'About'
    *           'page_slug'     => 'my_plugin_readme',
    *       )
    * );</code>
    * 
    * @since        2.0.0
    * @since        3.0.0       Changed the scope to public.
    * @remark       The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
    * @param        array       a sub-menu array. It can be a page or a link. The array structures are as follows:
    * <h4>Sub-menu Page Array</h4>
    * <ul>
    *   <li>**title** - (string) the page title of the page.</li>
    *   <li>**page_slug** - (string) the page slug of the page. Non-alphabetical characters should not be used including dots(.) and hyphens(-).</li>
    *   <li>**screen_icon** - (optional, string) either the ID selector name from the following list or the icon URL. The size of the icon should be 32 by 32 in pixel. This is for WordPress 3.7.x or below.
    *       <pre>edit, post, index, media, upload, link-manager, link, link-category, edit-pages, page, edit-comments, themes, plugins, users, profile, user-edit, tools, admin, options-general, ms-admin, generic</pre>
    *       <p>( Notes: the `generic` icon is available WordPress version 3.5 or above.)</p> 
    *   </li>
    *   <li>**capability** - (optional, string) the access level to the created admin pages defined <a href="http://codex.wordpress.org/Roles_and_Capabilities">here</a>. If not set, the overall capability assigned in the class constructor, which is `manage_options` by default, will be used.</li>
    *   <li>**order** - (optional, integer) the order number of the page. The lager the number is, the lower the position it is placed in the menu.</li>
    *   <li>**show_page_heading_tab** - (optional, boolean) if this is set to false, the page title won't be displayed in the page heading tab. Default: `true`.</li>
    *   <li>**show_in_menu** - (optional) If this is set to false, the page title won't be displayed in the sidebar menu while the page is still accessible. Default: true.</li>
    *   <li>**page_title** - (optional) [3.3.0+] When the page title differs from the menu title, use this argument.</li>
    *   <li>**menu_title** - (optional) [3.3.0+] When the menu title differs from the menu title, use this argument.</li>
    * </ul>
    * <h4>Sub-menu Link Array</h4>
    * <ul>
    *   <li>**title** - (string) the link title.</li>
    *   <li>**href** - (string) the URL of the target link.</li>
    *   <li>**capability** - (optional, string) the access level to show the item, defined <a href="http://codex.wordpress.org/Roles_and_Capabilities">here</a>. If not set, the overall capability assigned in the class constructor, which is `manage_options` by default, will be used.</li>
    *   <li>**order** - (optional, integer) the order number of the page. The lager the number is, the lower the position it is placed in the menu.</li>
    *   <li>**show_page_heading_tab** - (optional, boolean) if this is set to false, the page title won't be displayed in the page heading tab. Default: `true`.</li>
    *   <li>**show_in_menu** - (optional) If this is set to false, the page title won't be displayed in the sidebar menu while the page is still accessible. Default: true.</li>
    * </ul>
    * @access       public
    * @return       void
    */    
    public function addSubMenuItem( array $aSubMenuItem ) {
        if ( isset( $aSubMenuItem['href'] ) ) {
            $this->addSubMenuLink( $aSubMenuItem );
        } else {
            $this->addSubMenuPage( $aSubMenuItem );
        }
    }

    /**
    * Adds the given link into the menu on the left sidebar of the administration panel.
    * 
    * @since        2.0.0
    * @since        3.0.0       Changed the scope to public from protected.
    * @param        string      the menu title.
    * @param        string      the URL linked to the menu.
    * @param        string      (optional) the <a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">access level</a>.
    * @param        string      (optional) the order number. The larger it is, the lower the position it gets.
    * @param        string      (optional) if set to false, the menu title will not be listed in the tab navigation menu at the top of the page.
    * @access       protected
    * @return       void
    * @internal
    */    
    protected function addSubMenuLink( array $aSubMenuLink ) {
        
        // If required keys are not set, return.
        if ( ! isset( $aSubMenuLink['href'], $aSubMenuLink['title'] ) ) { return; }
        
        // If the set URL is not valid, return.
        if ( ! filter_var( $aSubMenuLink['href'], FILTER_VALIDATE_URL ) ) { return; }

        $this->oProp->aPages[ $aSubMenuLink['href'] ] = $this->_formatSubmenuLinkArray( $aSubMenuLink );
            
    }    
    
    /**
     * Adds sub-menu pages.
     * 
     * It is recommended to use addSubMenuItems() instead, which supports external links.
     * 
     * @since       2.0.0
     * @since       3.0.0       The scope was changed to public from protected.
     * @internal
     * @return      void
     * @remark      The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
     */ 
    protected function addSubMenuPages() {
        foreach ( func_get_args() as $aSubMenuPage ) {
            $this->addSubMenuPage( $aSubMenuPage );
        }
    }
    
    /**
     * Adds a single sub-menu page.
     * 
     * <h4>Example</h4>
     * <code>
     *  $this->addSubMenuPage(
     *      array(
     *          'title' => __( 'First Page', 'admin-page-framework-demo' ),
     *          'page_slug' => 'apf_first_page',
     *      ),
     *      array(
     *          'title' => __( 'Second Page', 'admin-page-framework-demo' ),
     *          'page_slug' => 'apf_second_page',
     *      )
     *  );</code>
     * 
     * 
     * @access      public
     * @since       2.0.0
     * @since       2.1.2       A key name was changed.
     * @since       2.1.6       $sScreenIcon accepts a file path.
     * @since       3.0.0       The scope was changed to public from protected. Deprecated all the parameters made it to accept them as an array. A key name was changed.
     * @remark      The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
     * @param       array       The sub menu page array.
     * <h4>Sub Menu Page Array</h4>
     * <ul>
     *     <li>title - (required) the title of the page.</li>
     *     <li>page_slug - (required) the slug of the page. Do not use hyphens as it serves as the callback method name.</li>
     *     <li>screen icon - (optional) Either a screen icon ID, a url of the icon, or a file path to the icon, with the size of 32 by 32 in pixel. The accepted icon IDs are as follows.</li>
     * <blockquote>edit, post, index, media, upload, link-manager, link, link-category, edit-pages, page, edit-comments, themes, plugins, users, profile, user-edit, tools, admin, options-general, ms-admin, generic</blockquote>
     * ( Note: the <em>generic</em> ID is available since WordPress 3.5. )
     *     <li>capability - (optional) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the page.</li>
     *     <li>order - (optional) the order number of the page. The lager the number is, the lower the position it is placed in the menu.</li>
     *     <li>show_page_heading_tab - (optional) If this is set to false, the page title won't be displayed in the page heading tab. Default: true.</li>
     *     <li>show_in_menu - (optional) If this is set to false, the page title won't be displayed in the sidebar menu while the page is still accessible. Default: true.</li>
     * </ul>
     * @return      void
     * @internal
     */ 
    protected function addSubMenuPage( array $aSubMenuPage ) {

        if ( ! isset( $aSubMenuPage['page_slug'] ) ) { return; }
            
        $aSubMenuPage['page_slug'] = $this->oUtil->sanitizeSlug( $aSubMenuPage['page_slug'] );
        $this->oProp->aPages[ $aSubMenuPage['page_slug'] ] = $this->_formatSubMenuPageArray( $aSubMenuPage );
        
    }
                       
}