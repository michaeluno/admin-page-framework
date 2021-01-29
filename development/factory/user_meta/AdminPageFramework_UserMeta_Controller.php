<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to manipulate the factory behaviour.
 *
 * @abstract
 * @since           3.5.0
 * @package         AdminPageFramework/Factory/UserMeta
 */
abstract class AdminPageFramework_UserMeta_Controller extends AdminPageFramework_UserMeta_View {

    /**
     * The set up method.
     *
     * <h4>Example</h4>
     *     public function setUp() {
     *
     *          $this->addSettingFields(
     *              array(
     *                  'field_id'      => 'text_field',
     *                  'type'          => 'text',
     *                  'title'         => __( 'Text', 'admin-page-framework-demo' ),
     *                  'repeatable'    => true,
     *                  'sortable'      => true,
     *                  'description'   => 'Type something here.',
     *              ),
     *              array(
     *                  'field_id'      => 'text_area',
     *                  'type'          => 'textarea',
     *                  'title'         => __( 'Text Area', 'admin-page-framework-demo' ),
     *                  'default'       => 'Hi there!',
     *              ),
     *              array(
     *                  'field_id'      => 'radio_buttons',
     *                  'type'          => 'radio',
     *                  'title'         => __( 'Radio', 'admin-page-framework-demo' ),
     *                  'label'         => array(
     *                      'a' => 'A',
     *                      'b' => 'B',
     *                      'c' => 'C',
     *                  ),
     *                  'default'       => 'a',
     *              )
     *          );
     *
     *      }
     *
     * @remark      should be overridden by the user definition class.
     * @since       3.5.0
     */
    public function setUp() {}


    /**
     * Enqueues styles by post type.
     *
     * {@inheritdoc}
     *
     * @since 3.5.0
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
     * @since       3.5.0
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
     * @since 3.5.0
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
     * @since       3.5.0
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
