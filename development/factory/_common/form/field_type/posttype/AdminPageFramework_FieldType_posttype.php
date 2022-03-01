<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * A post type field can list available post types on the site.
 *
 * This class defines the posttype field type. By default, the `revision`, `attachment`, and `nav_menu_item` post type are not displayed.
 *
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *     <li>**slugs_to_remove** - (optional, array) the post type slugs not to be listed. e.g.`array( 'revision', 'attachment', 'nav_menu_item' )`</li>
 *     <li>**select_all_button** - [3.3.0+] (optional, array) pass `true` to enable the `Select All` button. To set a custom label, set the text such as `__( 'Check All', 'test-domain' )`. Default: `true`.</li>
 *     <li>**select_none_button** - [3.3.0+] (optional, array) pass `true` to enable the `Select None` button. To set a custom label, set the text such as `__( 'Check All', 'test-domain' )`. Default: `true`.</li>
 *     <li>**query** - [3.2.1+] (optional, array) an query argument array to perform custom query to search post types. For the argument specification, see the `arg` parameter of [get_post_types()](http://codex.wordpress.org/Function_Reference/get_post_types#Parameters) function.
 *          <blockquote>
 *              <ul>
 *                  <li>`public` - Boolean. If true, only public post types will be returned.</li>
 *                  <li>`publicly_queryable` - Boolean</li>
 *                  <li>`exclude_from_search` - Boolean</li>
 *                  <li>`show_ui` - Boolean</li>
 *                  <li>`capability_type`</li>
 *                  <li>`hierarchical`</li>
 *                  <li>`menu_position`</li>
 *                  <li>`menu_icon`</li>
 *                  <li>`permalink_epmask`</li>
 *                  <li>`rewrite`</li>
 *                  <li>`query_var`</li>
 *                  <li>`_builtin` - Boolean. If true, will return WordPress default post types. Use false to return only custom post types.</li>
 *              </ul>
 *          </blockquote>
 *     </li>
 *     <li>**operator** - [3.2.1+] (optional, string) An operator to use with multiple arguments. Either `and` or `or` can be used. Default: `and`.</li>
 *     <li>**save_unchecked**       - [3.8.8+] (optional, boolean) Whether to store the values of unchecked items. Default: `true`.</li>
 *
 * </ul>
 *
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * <h2>Example</h2>
 * <h3>List All Post Types</h3>
 * <code>
 * array(
 *     'field_id'              => 'post_type_checklist',
 *     'title'                 => __( 'Post Types', 'admin-page-framework-loader' ),
 *     'type'                  => 'posttype',
 * )
 * </code>
 *
 * <h3>Perform Custom Query</h3>
 * <code>
 *  array(
 *      'field_id'              => 'post_type_checklist_custom_query',
 *      'title'                 => __( 'Custom Query', 'admin-page-framework-loader' ),
 *      'type'                  => 'posttype',
 *      'query'                 => array(
 *          'public'   => true,
 *          '_builtin' => false,
 *      ),
 *      'select_all_button'     => false,
 *      'select_none_button'    => false,
 *      'operator'              => 'and',
 *      'slugs_to_remove'       => array(),
 *  )
 * </code>
 *
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/posttype.png
 * @package         AdminPageFramework/Common/Form/FieldType
 * @since           2.1.5
 */
class AdminPageFramework_FieldType_posttype extends AdminPageFramework_FieldType_checkbox {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'posttype', );

    /**
     * Defines the default key-values of this field type.
     *
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'slugs_to_remove'       => null,    // the default array will be assigned in the rendering method.
        /**
         * Accepts query arguments. For the argument specification, see the arg parameter of get_post_types() function.
         * See: http://codex.wordpress.org/Function_Reference/get_post_types#Parameters
         */
        'query'                 => array(),  // 3.2.1+
        'operator'              => 'and',    // 3.2.1+ either 'and' or 'or'
        'attributes'            => array(
            'size'      => 30,
            'maxlength' => 400,
        ),
        'select_all_button'     => true,     // 3.3.0+   to change the label, set the label here
        'select_none_button'    => true,     // 3.3.0+   to change the label, set the label here
        'save_unchecked'        => true,     // (optional, boolean) 3.8.8+   Whether to store the values of unchecked items.
    );
    protected $aDefaultRemovingPostTypeSlugs = array(
        'revision',
        'attachment',
        'nav_menu_item',
    );

    /**
     * Returns the output of the field type.
     *
     * Returns the output of post type checklist check boxes.
     *
     * @remark      the posttype checklist field does not support multiple elements by passing an array of labels.
     * @since       2.0.0
     * @since       2.1.5       Moved from AdminPageFramework_FormField.
     * @since       3.0.0       Reconstructed entirely.
     * @since       3.3.1       Changed from `_replyToGetField()`.
     * @internal
     * @return      string
     */
    protected function getField( $aField ) {
        $this->_sCheckboxClassSelector = '';    // disable the checkbox class selector.
        $aField[ 'label' ] = $this->_getPostTypeArrayForChecklist(
            isset( $aField[ 'slugs_to_remove' ] )
                ? $this->getAsArray( $aField[ 'slugs_to_remove' ] )
                : $this->aDefaultRemovingPostTypeSlugs,    // slugs to remove
            $aField[ 'query' ],
            $aField[ 'operator' ]
        );
        return parent::getField( $aField );
    }

        /**
         * A helper function for the above getPosttypeChecklistField method.
         *
         * @since   2.0.0
         * @since   2.1.1   Changed the returning array to have the labels in its element values.
         * @since   2.1.5   Moved from AdminPageFramework_InputTag.
         * @since   3.2.1   Added the $asQueryArgs and $sOperator parameters.
         * @param   $aSlugsToRemove     array   The slugs to remove from the result.
         * @param   $asQueryArgs        array   The query argument.
         * @param   $sOperator          array   The query operator.
         * @return  array   The array holding the elements of installed post types' labels and their slugs except the specified expluding post types.
         * @internal
         */
        private function _getPostTypeArrayForChecklist( $aSlugsToRemove, $asQueryArgs=array(), $sOperator='and' ) {
            $_aPostTypes = array();
            foreach( get_post_types( $asQueryArgs, 'objects' ) as $_oPostType ) {
                if (  isset( $_oPostType->name, $_oPostType->label ) ) {
                    $_aPostTypes[ $_oPostType->name ] = $_oPostType->label;
                }
            }
            return array_diff_key( $_aPostTypes, array_flip( $aSlugsToRemove ) );
        }

}