<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to manipulate menu items.
 *
 * @abstract
 * @since           2.0.0
 * @since           3.3.1       Changed the name from `AdminPageFramework_Menu`.
 * @since           3.6.3       Changed the name from `AdminPageFramework_Menu_Controller`.
 * @extends         AdminPageFramework_View_Menu
 * @package         AdminPageFramework/Factory/AdminPage
 */
abstract class AdminPageFramework_Controller_Menu extends AdminPageFramework_View_Menu {
       
    /**
     * A look-up array for the built-in root menu slugs.
     * 
     * @since       2.0.0
     * @since       3.1.0       Changed it non-static.
     * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
     * @since       3.7.4       Moved from `AdminPageFramework_Model_Menu`.
     * @var         array       Holds the built-in root menu slugs.
     * @internal
     * @remark      All the keys must be lower case to support case insensitive look-ups.
     */ 
    protected $_aBuiltInRootMenuSlugs = array(
        'dashboard'     => 'index.php',
        'posts'         => 'edit.php',
        'media'         => 'upload.php',
        'links'         => 'link-manager.php',
        'pages'         => 'edit.php?post_type=page',
        'comments'      => 'edit-comments.php',
        'appearance'    => 'themes.php',
        'plugins'       => 'plugins.php',
        'users'         => 'users.php',
        'tools'         => 'tools.php',
        'settings'      => 'options-general.php',
        'network admin' => 'network_admin_menu',
    );   
    
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
     * @since  2.0.0
     * @since  2.1.6       The $sIcon16x16 parameter accepts a file path.
     * @since  3.0.0       The scope was changed to public from protected.
     * @remark Only one root page can be set per one class instance.
     * @param  string      If the method cannot find the passed string from the following listed items, it will create a top level menu item with the passed string. ( case insensitive )
     * <blockquote>Dashboard, Posts, Media, Links, Pages, Comments, Appearance, Plugins, Users, Tools, Settings, Network Admin</blockquote>
     * @param  string      (optional) the source of menu icon with either of the following forms:
     * <ul>
     *  <li>the URL of the menu icon with the size of 16 by 16 in pixel.</li>
     *  <li>the file path of the menu icon with the size of 16 by 16 in pixel.</li>
     *  <li>the name of a Dashicons helper class to use a font icon, e.g. `dashicons-editor-customchar`.</li>
     *  <li>the string, 'none', to leave div.wp-menu-image empty so an icon can be added via CSS.</li>
     *  <li>a base64-encoded SVG using a data URI, which will be colored to match the color scheme. This should begin with 'data:image/svg+xml;base64,'.</li>
     * </ul>
     * @param  string      (optional) the position number that is passed to the <var>$position</var> parameter of the <a href="http://codex.wordpress.org/Function_Reference/add_menu_page">add_menu_page()</a> function.
     */
    public function setRootMenuPage( $sRootMenuLabel, $sIcon16x16=null, $iMenuPosition=null ) {
        $sRootMenuLabel = trim( $sRootMenuLabel );
        $_sSlug         = $this->___getBuiltInMenuSlugByLabel( $sRootMenuLabel ); // if true, this method returns the slug
        $this->oProp->aRootMenu = array(
            'sTitle'        => $sRootMenuLabel,
            'sPageSlug'     => strlen( $_sSlug ) ? $_sSlug : $this->oProp->sClassName,
            'sIcon16x16'    => $this->oUtil->getResolvedSRC( $sIcon16x16 ),
            'iPosition'     => $iMenuPosition,
            'fCreateRoot'   => empty( $_sSlug ),
        );
    }
        /**
         * Checks if a menu item is a WordPress built-in menu item from the given menu label.
         *
         * @internal
         * @since       2.0.0
         * @since       3.9.0       Made the return type always string. Renamed from `_isBuiltInMenuItem()`.
         * @return      string     Returns the associated slug string, if true.
         */
        private function ___getBuiltInMenuSlugByLabel( $sMenuLabel ) {
            $_sMenuLabelLower = strtolower( $sMenuLabel );
            return array_key_exists( $_sMenuLabelLower, $this->_aBuiltInRootMenuSlugs )
                ? $this->_aBuiltInRootMenuSlugs[ $_sMenuLabelLower ]
                : '';
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
     * @param       string      The page slug of the top-level root page.
     */ 
    public function setRootMenuPageBySlug( $sRootMenuSlug ) {
        $this->oProp->aRootMenu[ 'sPageSlug' ]    = $sRootMenuSlug; // do not sanitize the slug here because post types includes a question mark.
        $this->oProp->aRootMenu[ 'fCreateRoot' ]  = false; // indicates whether to use an existing menu item.
    }
    
    /**
     * Adds sub-menu items on the left sidebar menu of the administration panel. 
     * 
     * It supports pages and links. Each of them has the specific array structure.
     * 
     * <h4>Example</h4>
     * <code>
     * $this->addSubMenuItems(
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
     * );
     * </code>
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
    public function addSubMenuItems( /* $aSubMenuItem1, $aSubMenuItem2=null, $_and_more=null */ ) {
        foreach ( func_get_args() as $_aSubMenuItem ) {
            $this->addSubMenuItem( $_aSubMenuItem );     
        }
    }
    
    /**
     * Adds the given sub-menu item on the left sidebar menu of the administration panel.
     * 
     * It supports pages and links. Each of them has the specific array structure.
     * 
     * <h4>Example</h4>
     * <code>
     * $this->addSubMenuItem(
     *       array(
     *           'title'         => 'Read Me',
     *           'menu_title'    => 'About'
     *           'page_slug'     => 'my_plugin_readme',
     *       )
     * );
     * </code>
     * 
     * @since        2.0.0
     * @since        3.0.0       Changed the scope to public.
     * @remark       The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
     * @param        array       a sub-menu array. It can be a page or a link. The array structures are as follows:
     * <h4>Sub-menu Page Arguments</h4>
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
     *   <li>style - (optional) [3.6.3+] (string|array) The path or url of a stylesheet which gets loaded in the head tag. Or inline CSS rules.
     * When custom arguments need to be set such as whether it should be inserted in the footer, set an array holding the following arguments.
     *          <ul>
     *              <li>src - (required, string) the source url or path.</li>    
     *              <li>handle_id - (optional, string) The handle ID of the stylesheet.</li>    
     *              <li>dependencies - (optional, array) The dependency array.</li>    
     *              <li>version - (optional, string) The stylesheet version number.</li>    
     *              <li>media - (optional, string) the description of the field which is inserted into the after the input field tag.</li>    
     *          </ul>
     *   </li>
     *   <li>script - (optional) [3.6.3+] (string|array) The path or url of a JavaScript script which gets loaded in the head tag. Or an inline JavaScript script.
     * When custom arguments need to be set such as whether it should be inserted in the footer, set an array holding the following arguments.
     *          <ul>
     *              <li>src - (required, string) the source url or path.</li>    
     *              <li>handle_id - (optional, string) The handle ID of the stylesheet.</li>    
     *              <li>dependencies - (optional, array) The dependency array.</li>    
     *              <li>version - (optional, string) The stylesheet version number.</li>    
     *              <li>translation - (optional, array) an array holding translation key-value pairs.</li>    
     *          </ul>
     *   </li>
     *   <li>**disabled** - (optional, boolean) [3.5.10+] If false, in the page heading navigation tab, the link will be disabled. Default: `false`.</li>
     *   <li>**attributes** - (optional, array) [3.5.10+] An attribute array applied to navigation tab element.</li>
     *   <li>**show_debug_info** - (optional, boolean) [3.8.8+] Whether to show debug information. If `WP_DEBUG` is false, the debug output will not be displayed. Default: `true`.</li>
     * </ul>
     * <h4>Sub-menu Link Arguments</h4>
     * <ul>
     *   <li>**title** - (string) the link title.</li>
     *   <li>**href** - (string) the URL of the target link.</li>
     *   <li>**capability** - (optional, string) the access level to show the item, defined <a href="http://codex.wordpress.org/Roles_and_Capabilities">here</a>. If not set, the overall capability assigned in the class constructor, which is `manage_options` by default, will be used.</li>
     *   <li>**order** - (optional, integer) the order number of the page. The lager the number is, the lower the position it is placed in the menu.</li>
     *   <li>**show_page_heading_tab** - (optional, boolean) if this is set to false, the page title won't be displayed in the page heading tab. Default: `true`.</li>
     * </ul>
     * @access       public
     * @return       void
     */    
    public function addSubMenuItem( array $aSubMenuItem ) {
        if ( isset( $aSubMenuItem[ 'href' ] ) ) {
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
    * @since        3.5.0       Changed the scope to public as it was still protected.
    * @param        string      the menu title.
    * @param        string      the URL linked to the menu.
    * @param        string      (optional) the <a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">access level</a>.
    * @param        string      (optional) the order number. The larger it is, the lower the position it gets.
    * @param        string      (optional) if set to false, the menu title will not be listed in the tab navigation menu at the top of the page.
    * @access       public
    * @return       void
    * @internal
    */    
    public function addSubMenuLink( array $aSubMenuLink ) {
        
        // If required keys are not set, return.
        if ( ! isset( $aSubMenuLink[ 'href' ], $aSubMenuLink[ 'title' ] ) ) { 
            return; 
        }
        
        // If the set URL is not valid, return.
        if ( ! filter_var( $aSubMenuLink[ 'href' ], FILTER_VALIDATE_URL ) ) { 
            return; 
        }

        $_oFormatter   = new AdminPageFramework_Format_SubMenuLink( 
            $aSubMenuLink, 
            $this,
            count( $this->oProp->aPages ) + 1
        );
        $_aSubMenuLink = $_oFormatter->get();
        $this->oProp->aPages[ $_aSubMenuLink[ 'href' ] ] = $_aSubMenuLink;
        
    }    
    
    /**
     * Adds sub-menu pages.
     * 
     * It is recommended using {@link addSubMenuItems()} instead, which supports external links.
     * 
     * @since       2.0.0
     * @since       3.0.0       The scope was changed to public from protected.
     * @since       3.5.0       The scope was changes to public as it was still protected.
     * @internal
     * @return      void
     * @remark      The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
     */ 
    public function addSubMenuPages() {
        foreach ( func_get_args() as $_aSubMenuPage ) {
            $this->addSubMenuPage( $_aSubMenuPage );
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
     * @since       3.5.0       Changed the scope to public as it was protected.
     * @remark      The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
     * @param       array       The sub menu page array.
     * @return      void
     * @internal
     */ 
    public function addSubMenuPage( array $aSubMenuPage ) {

        if ( ! isset( $aSubMenuPage[ 'page_slug' ] ) ) { 
            return; 
        }
            
        $_oFormatter   = new AdminPageFramework_Format_SubMenuPage( 
            $aSubMenuPage,
            $this,
            count( $this->oProp->aPages ) + 1
        );
        $_aSubMenuPage = $_oFormatter->get();
        $this->oProp->aPages[ $_aSubMenuPage[ 'page_slug' ] ] = $_aSubMenuPage;
        
    }
    
}