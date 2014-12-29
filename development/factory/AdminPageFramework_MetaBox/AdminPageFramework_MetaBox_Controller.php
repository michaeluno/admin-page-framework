<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides UI related methods.
 *
 * @abstract
 * @since           3.3.0
 * @package         AdminPageFramework
 * @subpackage      MetaBox
 */
abstract class AdminPageFramework_MetaBox_Controller extends AdminPageFramework_MetaBox_View {
       
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
     * Enqueues styles by post type.
     *      
     * {@inheritdoc}
     * 
     * @since 3.0.0
     */
    public function enqueueStyles( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) {
        return $this->oResource->_enqueueStyles( $aSRCs, $aPostTypes, $aCustomArgs );
    }
    /**
     * Enqueues a style by post type.
     * 
     * 
     * {@inheritdoc}
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
     * Enqueues a script by post type.
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
    
}