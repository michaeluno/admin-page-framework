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
 * @abstract
 * @since           2.0.0
 * @package         AdminPageFramework
 * @subpackage      MetaBox
 */
abstract class AdminPageFramework_MetaBox extends AdminPageFramework_MetaBox_Base {

    /**
     * Defines the fields type.
     * @since       3.0.0
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
     * @see         http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
     * @since       2.0.0
     * @param       string            The meta box ID.
     * @param       string            The meta box title.
     * @param       string|array      (optional) The post type(s) or screen ID that the meta box is associated with.
     * @param       string            (optional) The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side') Default: `normal`.
     * @param       string            (optional) The priority within the context where the boxes should show ('high', 'core', 'default' or 'low') Default: `default`.
     * @param       string            (optional) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the meta box. Default: `edit_posts`.
     * @param       string            (optional) The text domain applied to the displayed text messages. Default: `admin-page-framework`.
     * @return      void
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
    *             'field_id'    => 'sample_metabox_text_field',
    *             'title'       => 'Text Input',
    *             'description' => 'The description for the field.',
    *             'type'        => 'text',
    *         ),
    *         array(
    *             'field_id'    => 'sample_metabox_textarea_field',
    *             'title'       => 'Textarea',
    *             'description' => 'The description for the field.',
    *             'type'        => 'textarea',
    *             'default'     => 'This is a default text value.',
    *         )
    *     );     
    * }</code>
    * 
    * @abstract
    * @since        2.0.0
    * @remark       The user should override this method.
    * @return       void
    */  
    public function setUp() {}    

    /**
     * Enqueues styles by page slug and tab slug.
     * 
     * @since 3.0.0
     */
    public function enqueueStyles( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) {
        return $this->oResource->_enqueueStyles( $aSRCs, $aPostTypes, $aCustomArgs );
    }
    /**
     * Enqueues a style by page slug and tab slug.
     * 
     * @since       3.0.0
     * @see         http://codex.wordpress.org/Function_Reference/wp_enqueue_style
     * @param       string      The source of the stylesheet to enqueue: the URL, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
     * <h4>Custom Argument Array</h4>
     * <ul>
     *     <li>**handle_id** - (optional, string) The handle ID of the stylesheet.</li>
     *     <li>**dependencies** - (optional, array) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
     *     <li>**version** - (optional, string) The stylesheet version number.</li>
     *     <li>**media** - (optional, string) the description of the field which is inserted into after the input field tag.</li>
     * </ul>
     * @param       array       (optional) The post type slugs that the stylesheet should be added to. If not set, it applies to all the pages of the post types.
     * @param       array       (optional) The argument array for more advanced parameters.
     * @return      string The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     */    
    public function enqueueStyle( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) {
        return $this->oResource->_enqueueStyle( $sSRC, $aPostTypes, $aCustomArgs );     
    }
    /**
     * Enqueues scripts by page slug and tab slug.
     * 
     * @since 3.0.0
     */
    public function enqueueScripts( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) {
        return $this->oResource->_enqueueScripts( $aSRCs, $aPostTypes, $aCustomArgs );
    }    
    /**
     * Enqueues a script by page slug and tab slug.
     *  
     * <h4>Example</h4>
     * <code>$this->enqueueScript(  
     *      plugins_url( 'asset/js/test.js' , __FILE__ ), // source url or path
     *      array( 'my_post_type_slug' ),
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
     * @see         http://codex.wordpress.org/Function_Reference/wp_enqueue_script
     * @param       string The source of the stylesheet to enqueue: the URL, the absolute file path, or the relative path to the root directory of WordPress. Example: `/js/myscript.js`.
     * <h4>Custom Argument Array</h4>
     * <ul>
     *     <li>**handle_id** - (optional, string) The handle ID of the script.</li>
     *     <li>**dependencies** - (optional, array) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
     *     <li>**version** - (optional, string) The stylesheet version number.</li>
     *     <li>**translation** - (optional, array) The translation array. The handle ID will be used for the object name.</li>
     *     <li>**in_footer** - (optional, boolean) Whether to enqueue the script before </head > or before </body> Default: `false`.</li>
     * </ul>  
     * @param       string      (optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
     * @param       string      (optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
     * @param       array       (optional) The argument array for more advanced parameters.
     * @return      string The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     */
    public function enqueueScript( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) {    
        return $this->oResource->_enqueueScript( $sSRC, $aPostTypes, $aCustomArgs );
    }    
        
    /**
     * Adds the defined meta box.
     * 
     * @since       2.0.0
     * @internal
     * @remark      uses `add_meta_box()`.
     * @remark      A callback for the `add_meta_boxes` hook.
     * @return      void
     */ 
    public function _replyToAddMetaBox() {

        foreach( $this->oProp->aPostTypes as $sPostType ) {
            add_meta_box( 
                $this->oProp->sMetaBoxID,                       // id
                $this->oProp->sTitle,                           // title
                array( $this, '_replyToPrintMetaBoxContents' ), // callback
                $sPostType,                                     // post type
                $this->oProp->sContext,                         // context
                $this->oProp->sPriority,                        // priority
                null                                            // argument - deprecated $this->oForm->aFields
            );
        }
            
    }     
    
    /**
     * Registers form fields and sections.
     * 
     * @since       3.0.0
     * @internal
     */
    public function _replyToRegisterFormElements( $oScreen ) {
                
        // Schedule to add head tag elements and help pane contents. 
        if ( ! $this->oUtil->isPostDefinitionPage( $this->oProp->aPostTypes ) ) { return; }
    
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