<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework' ) ) :
/**
 * The main class of the framework to create admin pages and forms.
 * 
 * This class should be extended and the `setUp()` method should be overridden to define how pages are composed.
 * Most of the internal methods are prefixed with the underscore like `_getSomething()` and callback methods are prefixed with `_reply`.
 * The methods for the users are public and do not have those prefixes.
 * 
 * @abstract
 * @since       2.0.0
 * @remark      This class stems from several abstract classes.
 * @extends     AdminPageFramework_Setting
 * @package     AdminPageFramework
 * @subpackage  AdminPage
 */
abstract class AdminPageFramework extends AdminPageFramework_Setting {
        
    /**
     * Registers necessary callbacks ans sets up internal components including properties.
     * 
     * <h4>Example</h4>
     * <code>if ( is_admin() ) {
     *     new MyAdminPageClass( 'my_custom_option_key', __FILE__ );
     * }</code>
     * 
     * @access      public
     * @since       2.0.0
     * @see         http://codex.wordpress.org/Roles_and_Capabilities
     * @see         http://codex.wordpress.org/I18n_for_WordPress_Developers#Text_Domains
     * @param       string      $sOptionKey (optional) specifies the option key name to store in the options table. If this is not set, the instantiated class name will be used.
     * @param       string      $sCallerPath (optional) used to retrieve the plugin/theme details to auto-insert the information into the page footer.
     * @param       string      $sCapability (optional) sets the overall access level to the admin pages created by the framework. The used capabilities are listed <a href="http://codex.wordpress.org/Roles_and_Capabilities">here</a>. The capability can be set per page, tab, setting section, setting field. Default: `manage_options`
     * @param       string      $sTextDomain (optional) the <a href="http://codex.wordpress.org/I18n_for_WordPress_Developers#Text_Domains" target="_blank">text domain</a> used for the framework's system messages. Default: admin-page-framework.
     * @return      void        returns nothing.
     */
    public function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ){
                        
        if ( ! $this->_isInstantiatable() ) {
            return;
        }
                        
        parent::__construct( 
            $sOptionKey, 
            $sCallerPath 
                ? trim( $sCallerPath )
                : $sCallerPath = ( is_admin() && ( isset( $GLOBALS['pagenow'] ) && in_array( $GLOBALS['pagenow'], array( 'plugins.php', ) ) || isset( $_GET['page'] ) ) 
                    ? AdminPageFramework_Utility::getCallerScriptPath( __FILE__ ) 
                    : null 
                ),     // this is important to attempt to find the caller script path here when separating the library into multiple files.    
            $sCapability, 
            $sTextDomain 
        );
        
        $this->oUtil->addAndDoAction( $this, 'start_' . $this->oProp->sClassName, $this );    

    }    

    /**
     * The method for all the necessary set-ups. 
     * 
     * The users should override this method to set-up necessary settings. To perform certain tasks prior to this method, use the `start_{instantiated class name}` hook that is triggered at the end of the class constructor.
     * 
     * <h4>Example</h4>
     * <code>public function setUp() {
     *     $this->setRootMenuPage( 'APF Form' ); 
     *     $this->addSubMenuItems(
     *         array(
     *             'title' => 'Form Fields',
     *             'page_slug' => 'apf_form_fields',
     *         )
     *     );     
     *     $this->addSettingSections(
     *         array(
     *             'section_id' => 'text_fields',
     *             'page_slug' => 'apf_form_fields',
     *             'title' => 'Text Fields',
     *             'description' => 'These are text type fields.',
     *         )
     *     );
     *     $this->addSettingFields(
     *         array(    
     *             'field_id' => 'text',
     *             'section_id' => 'text_fields',
     *             'title' => 'Text',
     *             'type' => 'text',
     *         )    
     *     );     
     * }</code>
     * @abstract
     * @since       2.0.0
     * @remark      This is a callback for the `wp_loaded` hook.
     * @remark      In v1, this is triggered with the `admin_menu` hook; however, in v2, this is triggered with the `wp_loaded` hook.
     * @access      public
     * @return      void
     */    
    public function setUp() {}
        
    /*
     * Help Pane Methods
     */
    
    /**
     * Adds the given contextual help tab contents into the property.
     * 
     * <h4>Example</h4>
     * <code> $this->addHelpTab( 
     * array(
     *      'page_slug                  => 'first_page', // (required)
     *      // 'page_tab_slug'          => null, // (optional)
     *      'help_tab_title'            => 'Admin Page Framework',
     *      'help_tab_id'               => 'admin_page_framework', // (required)
     *      'help_tab_content'          => __( 'This contextual help text can be set with the `addHelpTab()` method.', 'admin-page-framework' ),
     *      'help_tab_sidebar_content'  => __( 'This is placed in the sidebar of the help pane.', 'admin-page-framework' ),
     * )
     * );</code>
     * 
     * @since       2.1.0
     * @remark      Called when registering setting sections and fields.
     * @param       array The help tab array.
     * <h4>Contextual Help Tab Array Structure</h4>
     * <ul>
     *     <li>**page_slug** - (required) the page slug of the page that the contextual help tab and its contents are displayed.</li>
     *     <li>**page_tab_slug** - (optional) the tab slug of the page that the contextual help tab and its contents are displayed.</li>
     *     <li>**help_tab_title** - (required) the title of the contextual help tab.</li>
     *     <li>**help_tab_id** - (required) the id of the contextual help tab.</li>
     *     <li>**help_tab_content** - (optional) the HTML string content of the the contextual help tab.</li>
     *     <li>**help_tab_sidebar_content** - (optional) the HTML string content of the sidebar of the contextual help tab.</li>
     * </ul>
     * @return void
     */ 
    public function addHelpTab( $aHelpTab ) {
        if ( method_exists( $this->oHelpPane, '_addHelpTab' ) ) {
            $this->oHelpPane->_addHelpTab( $aHelpTab );
        }
    }

    /*
     * Head Tag Methods
     */
    /**
     * Enqueues styles by page slug and tab slug.
     * 
     * Use this method to pass multiple files to the same page.
     * 
     * <h4>Example</h4>
     * <code>$this->enqueueStyle(  
     *         array( 
     *             dirname( APFDEMO_FILE ) . '/asset/css/code.css',
     *             dirname( APFDEMO_FILE ) . '/asset/css/code2.css',
     *         ),
     *         'apf_manage_options' 
     * );</code>
     * 
     * @since       3.0.0
     * @param       array       The sources of the stylesheet to enqueue: the url, the absolute file path, or the relative path to the root directory of WordPress. Example: `array( '/css/mystyle.css', '/css/mystyle2.css' )`
     * @param       string      (optional) The page slug that the stylesheet should be added to. If not set, it applies to all the pages created by the framework.
     * @param       string      (optional) The tab slug that the stylesheet should be added to. If not set, it applies to all the in-page tabs in the page.
     * @param       array       (optional) The argument array for more advanced parameters.
     * @return      array       The array holing the queued items.
     */
    public function enqueueStyles( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
        if ( method_exists( $this->oResource, '_enqueueStyles' ) ) {
            return $this->oResource->_enqueueStyles( $aSRCs, $sPageSlug, $sTabSlug, $aCustomArgs );
        }
    }
    /**
     * Enqueues a style by page slug and tab slug.
     * 
     * <h4>Example</h4>
     * <code>$this->enqueueStyle(  dirname( APFDEMO_FILE ) . '/asset/css/code.css', 'apf_manage_options' );
     * $this->enqueueStyle(  plugins_url( 'asset/css/readme.css' , APFDEMO_FILE ) , 'apf_read_me' );</code>
     * 
     * @since       2.1.2
     * @see         http://codex.wordpress.org/Function_Reference/wp_enqueue_style
     * @param       string      The source of the stylesheet to enqueue: the url, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
     * @param       string      (optional) The page slug that the stylesheet should be added to. If not set, it applies to all the pages created by the framework.
     * @param       string      (optional) The tab slug that the stylesheet should be added to. If not set, it applies to all the in-page tabs in the page.
     * @param       array       (optional) The argument array for more advanced parameters.
     * <h4>Argument Array</h4>
     * <ul>
     *     <li>**handle_id** - (optional, string) The handle ID of the stylesheet.</li>
     *     <li>**dependencies** - (optional, array) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
     *     <li>**version** - (optional, string) The stylesheet version number.</li>
     *     <li>**media** - (optional, string) the description of the field which is inserted into the after the input field tag.</li>
     * </ul>
     * @return      string      The style handle ID. If the passed url is not a valid url string, an empty string will be returned.
     */    
    public function enqueueStyle( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
        if ( method_exists( $this->oResource, '_enqueueStyle' ) ) {
            return $this->oResource->_enqueueStyle( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );     
        }
    }
    /**
     * Enqueues scripts by page slug and tab slug.
     * 
     * <h4>Example</h4>
     * <code>
     * $this->enqueueScripts(  
     *     array( 
     *          plugins_url( 'asset/js/test.js' , __FILE__ ), // source url or path
     *          plugins_url( 'asset/js/test2.js' , __FILE__ ),    
     *     )
     *     'apf_read_me',     // page slug
     * );
     * </code>
     *
     * @since       2.1.5
     * @param       array       The sources of the stylesheets to enqueue: the URL, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
     * @param       string      (optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
     * @param       string      (optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
     * @param       array       (optional) The argument array for more advanced parameters.
     * @return      array        The array holding the queued items.
     */
    public function enqueueScripts( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
        if ( method_exists( $this->oResource, '_enqueueScripts' ) ) {
            return $this->oResource->_enqueueScripts( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
        }
    }    
    /**
     * Enqueues a script by page slug and tab slug.
     *  
     * <h4>Example</h4>
     * <code>$this->enqueueScript(  
     *      plugins_url( 'asset/js/test.js' , __FILE__ ), // source url or path
     *      'apf_read_me',     // page slug
     *      '',     // tab slug
     *      array(
     *          'handle_id'     => 'my_script', // this handle ID also is used as the object name for the translation array below.
     *          'translation'   => array( 
     *              'a'                 => 'hello world!',
     *              'style_handle_id'   => $sStyleHandle, // check the enqueued style handle ID here.
     *          ),
     *      )
     * );</code>
     * 
     * @since       2.1.2
     * @since       3.0.0       Changed the scope to public
     * @see         http://codex.wordpress.org/Function_Reference/wp_enqueue_script
     * @param       string      The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
     * @param       string      (optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
     * @param       string      (optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
     * @param       array       (optional) The argument array for more advanced parameters.
     * <h4>Argument Array</h4>
     * <ul>
     *     <li>**handle_id** - (optional, string) The handle ID of the script.</li>
     *     <li>**dependencies** - (optional, array) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
     *     <li>**version** - (optional, string) The stylesheet version number.</li>
     *     <li>**translation** - (optional, array) The translation array. The handle ID will be used for the object name.</li>
     *     <li>**in_footer** - (optional, boolean) Whether to enqueue the script before `</head>` or before`</body>` Default: `false`.</li>
     * </ul>
     * @return      string      The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     */
    public function enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {    
        if ( method_exists( $this->oResource, '_enqueueScript' ) ) {
            return $this->oResource->_enqueueScript( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
        }
    }
    
    /**
    * Adds the given link(s) into the description cell of the plugin listing table.
    * 
    * <h4>Example</h4>
    * <code>$this->addLinkToPluginDescription( 
    *       "<a href='http://www.google.com'>Google</a>",
    *       "<a href='http://www.yahoo.com'>Yahoo!</a>"
    * );</code>
    * 
    * @since        2.0.0
    * @since        3.0.0       Changed the scope to public from protected.
    * @remark       Accepts variadic parameters; the number of accepted parameters are not limited to three.
    * @param        string      the tagged HTML link text.
    * @param        string      (optional) another tagged HTML link text.
    * @param        string      (optional) add more as many as want by adding items to the next parameters.
    * @access       public
    * @return       void
    */     
    public function addLinkToPluginDescription( $sTaggedLinkHTML1, $sTaggedLinkHTML2=null, $_and_more=null ) {
        if ( method_exists( $this->oLink, '_addLinkToPluginDescription' ) ) {
            $this->oLink->_addLinkToPluginDescription( func_get_args() );     
        }
    }

    /**
    * Adds the given link(s) into the title cell of the plugin listing table.
    * 
    * <h4>Example</h4>
    * <code>$this->addLinkToPluginTitle( 
    *       "<a href='http://www.wordpress.org'>WordPress</a>"
    * );</code>
    * 
    * @since        2.0.0
    * @since        3.0.0       Changed the scope to public from protected.
    * @remark       Accepts variadic parameters; the number of accepted parameters are not limited to three.
    * @param        string      the tagged HTML link text.
    * @param        string      (optional) another tagged HTML link text.
    * @param        string      (optional) add more as many as want by adding items to the next parameters.
    * @access       public
    * @return       void
    */    
    public function addLinkToPluginTitle( $sTaggedLinkHTML1, $sTaggedLinkHTML2=null, $_and_more=null ) {    
        if ( method_exists( $this->oLink, '_addLinkToPluginTitle' ) ) {
            $this->oLink->_addLinkToPluginTitle( func_get_args() );     
        }
    }
     
    /**
     * Sets the label applied to the settings link which automatically embedded to the plugin listing table of the plugin title cell.
     * 
     * To disable the embedded settings link, pass an empty value.
     * 
     * @since       3.1.0
     */  
    public function setPluginSettingsLinkLabel( $sLabel ) {
        $this->oProp->sLabelPluginSettingsLink = $sLabel;
    }
     
    /**
     * Sets the overall capability.
     * 
     * <h4>Example</h4>
     * <code>$this->setCpability( 'read' ); // let subscribers access the pages.
     * </code>
     * 
     * @since       2.0.0
     * @since       3.0.0       Changed the scope to public from protected.
     * @see         http://codex.wordpress.org/Roles_and_Capabilities
     * @param       string      The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> for the created pages.
     * @return      void
     * @access      public
     */ 
    public function setCapability( $sCapability ) {
        $this->oProp->sCapability = $sCapability;
        if ( isset( $this->oForm ) ) {
            $this->oForm->sCapability = $sCapability;
        }  
    }

    /**
     * Sets the given HTML text into the footer on the left hand side.
     * 
     * <h4>Example</h4>
     * <code>$this->setFooterInfoLeft( '<br />Custom Text on the left hand side.' );
     * </code>
     * 
     * @since       2.0.0
     * @since       3.0.0       Changed the scope to public from protected.
     * @param       string      The HTML code to insert.
     * @param       boolean     If true, the text will be appended; otherwise, it will replace the default text.
     * @access      public
     * @return      void
     */    
    public function setFooterInfoLeft( $sHTML, $bAppend=true ) {
        $this->oProp->aFooterInfo['sLeft'] = $bAppend 
            ? $this->oProp->aFooterInfo['sLeft'] . PHP_EOL . $sHTML
            : $sHTML;
    }
    
    /**
     * Sets the given HTML text into the footer on the right hand side.
     * 
     * <h4>Example</h4>
     * <code>$this->setFooterInfoRight( '<br />Custom Text on the right hand side.' );
     * </code>
     * 
     * @access      public
     * @since       2.0.0
     * @since       3.0.0       Changed the scope to public from protected.
     * @param       string      The HTML code to insert.
     * @param       boolean     If true, the text will be appended; otherwise, it will replace the default text.
     * @return      void
     */    
    public function setFooterInfoRight( $sHTML, $bAppend=true ) {
        $this->oProp->aFooterInfo['sRight'] = $bAppend 
            ? $this->oProp->aFooterInfo['sRight'] . PHP_EOL . $sHTML
            : $sHTML;
    }
            
    /**
     * Sets an admin notice.
     * 
     * <h4>Example</h4>
     * <code>$this->setAdminNotice( sprintf( 'Please click <a href="%1$s">here</a> to upgrade the options.', admin_url( 'admin.php?page="my_page"' ) ), 'updated' );
     * </code>
     * 
     * @access      public
     * @remark      It should be used before the 'admin_notices' hook is triggered.
     * @since       2.1.2
     * @since       3.0.0       Changed the scope to public from protected.
     * @param       string      The message to display
     * @param       string      (optional) The class selector used in the message HTML element. 'error' and 'updated' are prepared by WordPress but it's not limited to them and can pass a custom name. Default: 'error'.
     * @param       string      (optional) The ID of the message. If not set, the hash of the message will be used.
     */
    public function setAdminNotice( $sMessage, $sClassSelector='error', $sID='' ) {
            
        $sID = $sID ? $sID : md5( $sMessage );
        $this->oProp->aAdminNotices[ md5( $sMessage ) ] = array(  
            'sMessage' => $sMessage,
            'sClassSelector' => $sClassSelector,
            'sID' => $sID,
        );
        if ( is_network_admin() ) {
            add_action( 'network_admin_notices', array( $this, '_replyToPrintAdminNotices' ) );
        } else {
            add_action( 'admin_notices', array( $this, '_replyToPrintAdminNotices' ) );
        }
        
    }
        /**
         * A helper function for the above setAdminNotice() method.
         * 
         * @since       2.1.2
         * @internal
         */
        public function _replyToPrintAdminNotices() {
            
            if ( ! $this->_isInThePage() ) { return; }
            foreach( $this->oProp->aAdminNotices as $aAdminNotice ) {
                echo "<div class='{$aAdminNotice['sClassSelector']}' id='{$aAdminNotice['sID']}'>"
                        . "<p>" . $aAdminNotice['sMessage'] . "</p>"
                    . "</div>";
            }
            
        }    
    
    /**
     * Sets the disallowed query keys in the links that the framework generates.
     * 
     * <h4>Example</h4>
     * <code>$this->setDisallowedQueryKeys( 'my-custom-admin-notice' );
     * </code>
     * 
     * @since       2.1.2
     * @since       3.0.0           It also accepts a string. Changed the scope to public.
     * @access      public
     * @param       array|string    The query key(s) to disallow.
     * @param       boolean         If true, the passed key(s) will be appended to the property; otherwise, it will override the property.
     * @return      void
     */
    public function setDisallowedQueryKeys( $asQueryKeys, $bAppend=true ) {
        
        if ( ! $bAppend ) {
            $this->oProp->aDisallowedQueryKeys = ( array ) $asQueryKeys;
            return;
        }
        
        $aNewQueryKeys = array_merge( ( array ) $asQueryKeys, $this->oProp->aDisallowedQueryKeys );
        $aNewQueryKeys = array_filter( $aNewQueryKeys ); // drop non-values
        $aNewQueryKeys = array_unique( $aNewQueryKeys ); // drop duplicates
        $this->oProp->aDisallowedQueryKeys = $aNewQueryKeys;
        
    }
    
    /**
     * Retrieves the saved option value from the given option key and the dimensional array key representation.
     * 
     * <h4>Example</h4>
     * <code>
     * $aData       = AdminPageFramework::getOption( 'APF' );
     * $aSection    = AdminPageFramework::getOption( 'APF', 'my_section' );
     * $sText       = AdminPageFramework::getOption( 'APF', array( 'my_section', 'my_text_field' ), 'foo' );
     * $sColor      = AdminPageFramework::getOption( 'APF', 'my_color_field', '#FFF' );
     * </code>
     * 
     * @since       3.0.1
     * @param       string      $sOptionKey     the option key of the options table.
     * @param       string      $asKey          the representation of dimensional array keys. If the returning option structure is like the following,
     * <code>
     * array(
     *     'a' => array(
     *         'b' => array(
     *             'c' => 'ccc',
     *         ),
     *     ),
     * )
     * </code>
     * then the value 'ccc' can be retrieved with the key representation array of 
     * <code>
     * array( 'a', 'b', 'c' )
     * </code>
     * @param       mixed       $vDefault       the default value that will be returned if nothing is stored.
     * @return      mixed       If the field ID is not specified
     */
    static public function getOption( $sOptionKey, $asKey=null , $vDefault=null ) {
        return AdminPageFramework_WPUtility::getOption( $sOptionKey,$asKey, $vDefault );
    }
        
}
endif;