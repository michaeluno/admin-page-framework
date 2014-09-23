<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_MetaBox' ) ) :
/**
 * Provides methods for creating meta boxes for post types.
 *
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 *     <li><strong>start_{instantiated class name}</strong> – triggered at the end of the class constructor. This receives the class object in the first parameter.</li>
 *     <li><strong>set_up_{instantiated class name}</strong> – triggered after the setUp() method is called. This receives the class object in the first parameter.</li>
 *     <li><strong>do_{instantiated class name}</strong> – triggered when the meta box gets rendered. The first parameter: the calss object[3.1.3+].</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 *     <li><strong>field_types_{instantiated class name}</strong> – receives the field type definition array. The first parameter: the field type definition array.</li>
 *     <li><strong>field_{instantiated class name}_{field ID}</strong> – receives the form input field output of the given input field ID. The first parameter: output string. The second parameter: the array of option.</li>
 *     <li><strong>content_{instantiated class name}</strong> – receives the entire output of the meta box. The first parameter: the output HTML string.</li>
 *     <li><strong>style_common_{instantiated class name}</strong> –  receives the output of the base CSS rules applied to the pages of the associated post types with the meta box.</li>
 *     <li><strong>style_ie_common_{instantiated class name}</strong> –  receives the output of the base CSS rules for Internet Explorer applied to the pages of the associated post types with the meta box.</li>
 *     <li><strong>style_{instantiated class name}</strong> –  receives the output of the CSS rules applied to the pages of the associated post types with the meta box.</li>
 *     <li><strong>style_ie_{instantiated class name}</strong> –  receives the output of the CSS rules for Internet Explorer applied to the pages of the associated post types with the meta box.</li>
 *     <li><strong>script_common_{instantiated class name}</strong> – receives the output of the base JavaScript scripts applied to the pages of the associated post types with the meta box.</li>
 *     <li><strong>script_{instantiated class name}</strong> – receives the output of the JavaScript scripts applied to the pages of the associated post types with the meta box.</li>
 *     <li><strong>validation_{instantiated class name}</strong> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * </ul>
 * <h3>Remarks</h3>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p>  
 * 
 * @abstract
 * @since 2.0.0
 * @use AdminPageFramework_Utility
 * @use AdminPageFramework_Message
 * @use AdminPageFramework_Debug
 * @use AdminPageFramework_Property_MetaBox
 * @package AdminPageFramework
 * @subpackage MetaBox
 */
abstract class AdminPageFramework_MetaBox extends AdminPageFramework_MetaBox_Base {

    /**
     * Defines the fields type.
     * @since 3.0.0
     * @internal
     */
    static protected $_sFieldsType = 'post_meta_box';
        
    /**
     * Constructs the class object instance of AdminPageFramework_MetaBox.
     * 
     * Sets up properties and hooks.
     * 
     * <h4>Example</h4>
     * <code>
     *     new APF_MetaBox_BuiltinFieldTypes(
     *         'sample_custom_meta_box', // meta box ID
     *         __( 'Demo Meta Box with Built-in Field Types', 'admin-page-framework-demo' ), // title
     *         array( 'apf_posts' ), // post type slugs: post, page, etc.
     *         'normal', // context (what kind of metabox this is)
     *         'default' // priority
     *     );
     * </code>
     * @see http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
     * @since 2.0.0
     * @param string The meta box ID.
     * @param string The meta box title.
     * @param string|array ( optional ) The post type(s) or screen ID that the meta box is associated with.
     * @param string ( optional ) The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side') Default: normal.
     * @param string ( optional ) The priority within the context where the boxes should show ('high', 'core', 'default' or 'low') Default: default.
     * @param string ( optional ) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the meta box. Default: edit_posts.
     * @param string ( optional ) The text domain applied to the displayed text messages. Default: admin-page-framework.
     * @return void
     */ 
    function __construct( $sMetaBoxID, $sTitle, $asPostTypeOrScreenID=array( 'post' ), $sContext='normal', $sPriority='default', $sCapability='edit_posts', $sTextDomain='admin-page-framework' ) {
        
        if ( ! $this->_isInstantiatable() ) { return; }
        
        /* The property object needs to be done first */
        $this->oProp = new AdminPageFramework_Property_MetaBox( $this, get_class( $this ), $sCapability, $sTextDomain, self::$_sFieldsType );
        $this->oProp->aPostTypes = is_string( $asPostTypeOrScreenID ) ? array( $asPostTypeOrScreenID ) : $asPostTypeOrScreenID;    
        
        parent::__construct( $sMetaBoxID, $sTitle, $asPostTypeOrScreenID, $sContext, $sPriority, $sCapability, $sTextDomain );
                        
        $this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}", $this );
        
    }
        /**
         * Determines whether the meta box belongs to the loading page.
         * 
         * @since       3.0.3
         * @since       3.2.0   Changed the scope to public from protected as the head tag object will access it.
         * @internal
         */
        public function _isInThePage() {

            if ( ! in_array( $this->oProp->sPageNow, array( 'post.php', 'post-new.php' ) ) ) {            
                return false;
            }
            
            if ( ! in_array( $this->oUtil->getCurrentPostType(), $this->oProp->aPostTypes ) ) {     
                return false;    
            }    

            return true;
            
        }
        
    /**
    * The method for all necessary set-ups.
    * 
    * <h4>Example</h4>
    * <code> public function setUp() {     
    *     $this->addSettingFields(
    *         array(
    *             'field_id' => 'sample_metabox_text_field',
    *             'title' => 'Text Input',
    *             'description' => 'The description for the field.',
    *             'type' => 'text',
    *         ),
    *         array(
    *             'field_id' => 'sample_metabox_textarea_field',
    *             'title' => 'Textarea',
    *             'description' => 'The description for the field.',
    *             'type' => 'textarea',
    *             'default' => 'This is a default text value.',
    *         )
    *     );     
    * }</code>
    * 
    * @abstract
    * @since 2.0.0
    * @remark The user should override this method.
    * @return void
    */  
    public function setUp() {}    

    /**
     * Enqueues styles by page slug and tab slug.
     * 
     * @since 3.0.0
     */
    public function enqueueStyles( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) {
        return $this->oHeadTag->_enqueueStyles( $aSRCs, $aPostTypes, $aCustomArgs );
    }
    /**
     * Enqueues a style by page slug and tab slug.
     * 
     * @since 3.0.0
     * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_style
     * @param string The source of the stylesheet to enqueue: the URL, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
     * <h4>Custom Argument Array</h4>
     * <ul>
     *     <li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
     *     <li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
     *     <li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
     *     <li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into after the input field tag.</li>
     * </ul>
     * @param array (optional) The post type slugs that the stylesheet should be added to. If not set, it applies to all the pages of the post types.
     * @param             array (optional) The argument array for more advanced parameters.
     * @return string The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     */    
    public function enqueueStyle( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) {
        return $this->oHeadTag->_enqueueStyle( $sSRC, $aPostTypes, $aCustomArgs );     
    }
    /**
     * Enqueues scripts by page slug and tab slug.
     * 
     * @since 3.0.0
     */
    public function enqueueScripts( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) {
        return $this->oHeadTag->_enqueueScripts( $aSRCs, $aPostTypes, $aCustomArgs );
    }    
    /**
     * Enqueues a script by page slug and tab slug.
     *  
     * <h4>Example</h4>
     * <code>$this->enqueueScript(  
     * plugins_url( 'asset/js/test.js' , __FILE__ ), // source url or path
     * array( 'my_post_type_slug' ),
     * array(
     * 'handle_id' => 'my_script', // this handle ID also is used as the object name for the translation array below.
     * 'translation' => array( 
     * 'a' => 'hello world!',
     * 'style_handle_id' => $sStyleHandle, // check the enqueued style handle ID here.
     * ),
     * )
     * );</code>
     * 
     * @since 2.1.2
     * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_script
     * @param string The source of the stylesheet to enqueue: the URL, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
     * <h4>Custom Argument Array</h4>
     * <ul>
     *     <li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
     *     <li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
     *     <li><strong>version/strong> - ( optional, string ) The stylesheet version number.</li>
     *     <li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
     *     <li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before </head > or before </body> Default: <em>false</em>.</li>
     * </ul>  
     * @param string (optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
     * @param string (optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
     * @param             array (optional) The argument array for more advanced parameters.
     * @return string The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     */
    public function enqueueScript( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) {    
        return $this->oHeadTag->_enqueueScript( $sSRC, $aPostTypes, $aCustomArgs );
    }    
        
    /**
     * Adds the defined meta box.
     * 
     * @since 2.0.0
     * @internal
     * @remark uses <em>add_meta_box()</em>.
     * @remark A callback for the <em>add_meta_boxes</em> hook.
     * @return void
     */ 
    public function _replyToAddMetaBox() {

        foreach( $this->oProp->aPostTypes as $sPostType ) {
            add_meta_box( 
                $this->oProp->sMetaBoxID,         // id
                $this->oProp->sTitle,     // title
                array( $this, '_replyToPrintMetaBoxContents' ),     // callback
                $sPostType, // post type
                $this->oProp->sContext,     // context
                $this->oProp->sPriority, // priority
                null // deprecated $this->oForm->aFields // argument
            );
        }
            
    }     
    
    /**
     * Registers form fields and sections.
     * 
     * @since 3.0.0
     * @internal
     */
    public function _replyToRegisterFormElements( $oScreen ) {
                
        // Schedule to add head tag elements and help pane contents. 
        if ( ! $this->oUtil->isPostDefinitionPage( $this->oProp->aPostTypes ) ) return;
    
        $this->_loadDefaultFieldTypeDefinitions();  // defined in the factory class.
    
        // Format the fields array.
        $this->oForm->format();
        $this->oForm->applyConditions(); // will set $this->oForm->aConditionedFields
        
        // Set the option array - the framework will refer to this data when displaying the fields.
        if ( isset( $this->oProp->aOptions ) ) {
            $this->_setOptionArray( 
                isset( $GLOBALS['post']->ID ) ? $GLOBALS['post']->ID : ( isset( $_GET['page'] ) ? $_GET['page'] : null ),
                $this->oForm->aConditionedFields 
            ); // will set $this->oProp->aOptions
        }
        
        // Add the repeatable section elements to the fields definition array.
        $this->oForm->setDynamicElements( $this->oProp->aOptions ); // will update $this->oForm->aConditionedFields
        
        $this->_registerFields( $this->oForm->aConditionedFields );
                
    }    
    
}
endif;