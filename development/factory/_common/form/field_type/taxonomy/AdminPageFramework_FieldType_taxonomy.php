<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * A taxonomy field can list terms of specified taxonomies.
 *
 * This class defines the `taxonomy` field type.
 *
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 *  <ul>
 *      <li>**taxonomy_slugs** - (optional, array) the taxonomy slug to list. Default: `category`</li>
 *      <li>**max_width** - (optional, string) the inline style property value of `max-width` of this element. Include the unit such as px, %. Default: 100%</li>
 *      <li>**height** - (optional, string) the inline style property value of `height` of this element. Include the unit such as px, %. Default: 250px</li>
 *      <li>**select_all_button** - [3.3.0+] (optional, array) pass `true` to enable the `Select All` button. To set a custom label, set the text such as `__( 'Check All', 'test-domain' )`. Default: `true`.</li>
 *      <li>**select_none_button** - [3.3.0+] (optional, array) pass `true` to enable the `Select None` button. To set a custom label, set the text such as `__( 'Check All', 'test-domain' )`. Default: `true`.</li>
 *      <li>**label_no_term_found** - [3.3.2+] (optional, string) The label to display when no term is found. Default: `No Term Found`.</li>
 *      <li>**label_list_title** - [3.3.2+] (optional, string) The heading title string for a term list. Default: `''`. Insert an HTML custom string right before the list starts.</li>
 *      <li>**query** - [3.3.2+] (optional, array) an query argument array to search terms. For more details, see the argument of the [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters) function.
 *          <ul>
 *              <li>child_of - (integer) The parent term ID. All the descendant terms such as child's child term will be listed. default: `0`</li>
 *              <li>parent   - (integer) The direct parent term ID. Only the first level children will be listed. </li>
 *              <li>orderby - (string) The type of how the term list should be ordered by. Either `ID`, `term_id`, or `name` can be accepted. Default: `name`.</li>
 *              <li>order - (string) The order of the list. `ASC` or `DESC`. Default: `ASC`.</li>
 *              <li>hide_empty - (boolean) whether to show the terms with no post associated. Default: `false`.</li>
 *              <li>hierarchical - (boolean) whether to show the terms as a hierarchical tree. Default: `true`</li>
 *              <li>number - (integer) The maximum number of the terms to show. 0 for no limit. Default: `0`.</li>
 *              <li>pad_counts - (boolean) whether to sum up the post counts with the child post counts. Default: `false`</li>
 *              <li>exclude - (string|array) Comma separated term IDs or an array to exclude from the list. for example `1` will remove the 'Uncategorized' category from the list. </li>
 *              <li>exclude_tree - (integer) For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters)..</li>
 *              <li>include - (string|array) Comma separated term IDs to include in the list.</li>
 *              <li>fields - (string) Default: `all`. For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters).</li>
 *              <li>slug - (string) For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters).</li>
 *              <li>get - (string) For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters).</li>
 *              <li>name__like - (string) For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters).</li>
 *              <li>description__like - (string) For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters).</li>
 *              <li>offset - (integer) For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters).</li>
 *              <li>search - (string) The search keyword to get the term with. Default ``.</li>
 *              <li>cache_domain - (string) Default:`core`. For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters).</li>
 *          </ul>
 *      </li>
 *      <li>**queries** - [3.3.2+] (optional, array) Sets a query argument for each taxonomy. The array key must be the taxonomy slug and the value is the query argument array.</li>
 *      <li>**save_unchecked** - [3.8.8+] (optional, boolean) Decides whether to save values of unchecked terms.</li>
 *  </ul>
 *
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * <h2>Example</h2>
 * <code>
 *  array(
 *      'field_id'              => 'taxonomy_checklist',
 *      'title'                 => __( 'Taxonomy Checklist', 'admin-page-framework-loader' ),
 *      'type'                  => 'taxonomy',
 *      'height'                => '200px', // (optional)
 *      'width'                 => '400px', // (optional)
 *      'show_post_count'       => true,    // (optional) whether to show the post count. Default: false.
 *      'taxonomy_slugs'        => array( 'category', 'post_tag', ),
 *      'select_all_button'     => false,        // 3.3.0+   to change the label, set the label here
 *      'select_none_button'    => false,        // 3.3.0+   to change the label, set the label here
 *  )
 * </code>
 *
 * <h3>List Taxonomies with a Custom Query</h3>
 * <code>
 * array(
 *     'field_id'              => 'taxonomy_custom_queries',
 *     'title'                 => __( 'Custom Taxonomy Queries', 'admin-page-framework-demo' ),
 *     'type'                  => 'taxonomy',
 *
 *     // (required)   Determines which taxonomies should be listed
 *     'taxonomy_slugs'        => $aTaxnomies = get_taxonomies( '', 'names' ),
 *
 *     // (optional) This defines the default query argument. For the structure and supported arguments, see http://codex.wordpress.org/Function_Reference/get_terms#Parameters
 *     'query'                 => array(
 *         'depth'     => 2,
 *         'orderby'   => 'term_id',       // accepts 'ID', 'term_id', or 'name'
 *         'order'     => 'DESC',
 *         // 'exclude'   => '1', // removes the 'Uncategorized' category.
 *         // 'search' => 'PHP',   // the search keyward
 *         // 'parent'    => 9,    // only show terms whose direct parent ID is 9.
 *         // 'child_of'  => 8,    // only show child terms of the term ID of 8.
 *     ),
 *     // (optional) This allows the user to set a query argument for each taxonomy.
 *     // Note that each element will be merged with the above default 'query' argument array.
 *     // So unset keys here will be overridden by the default argument array above.
 *     'queries'               => array(
 *         // taxonomy slug => query argument array
 *         'category'  =>  array(
 *             'depth'     => 2,
 *             'orderby'   => 'term_id',
 *             'order'     => 'DESC',
 *             'exclude'   => array( 1 ),
 *         ),
 *         'post_tag'  => array(
 *             'orderby'   => 'name',
 *             'order'     => 'ASC',
 *             // 'include'   => array( 4, ), // term ids
 *         ),
 *     ),
 * ),
 * </code>
 *
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/taxonomy.png
 * @package         AdminPageFramework/Common/Form/FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 */
class AdminPageFramework_FieldType_taxonomy extends AdminPageFramework_FieldType_checkbox {

    /**
     * Defines the field type slugs used for this field type.
     * @var     array
     */
    public $aFieldTypeSlugs = array( 'taxonomy', );

    /**
     * Defines the default key-values of this field type.
     *
     * @remark  `$_aDefaultKeys` holds shared default key-values defined in the base class.
     * @var     array
     */
    protected $aDefaultKeys = array(
        'taxonomy_slugs'        => 'category',      // (array|string) This is for the taxonomy field type.
        'height'                => '250px',         // the tab box height
        'width'                 => null,            // 3.5.7.2+ the tab box width
        'max_width'             => '100%',          // for the taxonomy checklist field type, since 2.1.1.
        'show_post_count'       => true,            // (boolean) 3.2.0+ whether or not the post count associated with the term should be displayed or not.
        'attributes'            => array(),
        'select_all_button'     => true,            // (boolean|string) 3.3.0+ to change the label, set the label here
        'select_none_button'    => true,            // (boolean|string) 3.3.0+ to change the label, set the label here
        'label_no_term_found'   => null,            // (string) 3.3.2+  The label to display when no term is found. null needs to be set here as the default value will be assigned in the field output method.
        'label_list_title'      => '',              // (string) 3.3.2+ The heading title string for a term list. Default: `''`. Insert an HTML custom string right before the list starts.
        'query'                 => array(       // (array)  3.3.2+ Defines the default query argument.
            // see the arguments of the get_category() function: http://codex.wordpress.org/Function_Reference/get_categories
            // see the argument of the get_terms() function: http://codex.wordpress.org/Function_Reference/get_terms
            'child_of'          => 0,
            'parent'            => '',
            'orderby'           => 'name',      // (string) 'ID' or 'term_id' or 'name'
            'order'             => 'ASC',       // (string) 'ASC' or 'DESC'
            'hide_empty'        => false,       // (boolean) whether to show the terms with no post associated. Default: false
            'hierarchical'      => true,        // (boolean) whether to show the terms as a hierarchical tree.
            'number'            => '',          // (integer) The maximum number of the terms to show.
            'pad_counts'        => false,       // (boolean) whether to sum up the post counts with the child post counts.
            'exclude'           => array(),     // (string) Comma separated term IDs to exclude from the list. for example `1` will remove the 'Uncategorized' category from the list.
            'exclude_tree'      => array(),
            'include'           => array(),     // (string) Comma separated term IDs to include in the list.
            'fields'            => 'all',
            'slug'              => '',
            'get'               => '',
            'name__like'        => '',
            'description__like' => '',
            'offset'            => '',
            'search'            => '',          // (string) The search keyword to get the term with.
            'cache_domain'      => 'core',
        ),
        'queries'   => array(),         // (optional, array) 3.3.2+  Sets a query argument for each taxonomy. The array key must be the taxonomy slug and the value is the query argument array.
        'save_unchecked'        => true,        // (optional, boolean) 3.8.8+   Whether to store the values of unchecked items.
    );

    /**
     * @return array
     * @since  3.9.0
     */
    protected function getEnqueuingScripts() {
        return array(
            array(
                'handle_id'         => 'admin-page-framework-field-type-taxonomy',
                'src'               => dirname( __FILE__ ) . '/js/taxonomy.bundle.js',
                'in_footer'         => true,
                'dependencies'      => array( 'jquery', 'admin-page-framework-script-form-main' ),
                'translation_var'   => 'AdminPageFrameworkFieldTypeTaxonomy',
                'translation'       => array(
                    'fieldTypeSlugs' => $this->aFieldTypeSlugs,
                    'label'          => array(),
                ),
            ),
        );
    }

    /**
     * Returns the output of taxonomy field type which shows check-list check boxes of taxonomy terms.
     *
     * @remark      Multiple fields are not supported.
     * @remark      Repeater fields are not supported.
     * @since       2.0.0
     * @since       2.1.1       The check-list boxes are rendered in a tabbed single box.
     * @since       2.1.5       Moved from AdminPageFramework_FormField.
     * @since       3.3.1       Changed from `_replyToGetField()`.
     * @internal
     * @return      string
     */
    protected function getField( $aField ) {

        // Format
        $aField[ 'label_no_term_found' ] = $this->getElement(
            $aField,
            'label_no_term_found',
            $this->oMsg->get( 'no_term_found' )
        );

        // Parse
        $_aTabs         = array();
        $_aCheckboxes   = array();
        foreach( $this->getAsArray( $aField[ 'taxonomy_slugs' ] ) as $_isKey => $_sTaxonomySlug ) {
            $_aAssociatedDataAttributes = $this->___getDataAttributesOfAssociatedPostTypes(
                $_sTaxonomySlug,
                $this->___getPostTypesByTaxonomySlug( $_sTaxonomySlug )
            );
            $_aTabs[]                   = $this->___getTaxonomyTab( $aField, $_isKey, $_sTaxonomySlug, $_aAssociatedDataAttributes );
            $_aCheckboxes[]             = $this->___getTaxonomyCheckboxes( $aField, $_isKey, $_sTaxonomySlug, $_aAssociatedDataAttributes );
        }

        // Output
        return "<div id='tabbox-{$aField['field_id']}' class='tab-box-container categorydiv' style='max-width:{$aField['max_width']};'>"
                . "<ul class='tab-box-tabs category-tabs'>"
                    . implode( PHP_EOL, $_aTabs )
                . "</ul>"
                . "<div class='tab-box-contents-container'>"
                    . "<div class='tab-box-contents' style='height: {$aField['height']};'>"
                        . implode( PHP_EOL, $_aCheckboxes )
                    . "</div>"
                . "</div>"
            . "</div>"
            ;

    }

        /**
         * @since       3.8.8
         * @return      array       Post type slugs associated with the given taxonomy.
         */
        private function ___getPostTypesByTaxonomySlug( $sTaxonomySlug ) {
            $_oTaxonomy = get_taxonomy( $sTaxonomySlug );
            return $_oTaxonomy->object_type;
        }

        /**
         * @remark      This is for the `post_type_taxonomy` field type.
         * @since       3.8.8
         * @return      array
         */
        private function ___getDataAttributesOfAssociatedPostTypes( $sTaxonomySlusg, $aPostTypes ) {
            return array(
                'data-associated-with'       => $sTaxonomySlusg,
                'data-associated-post-types' => implode( ',', $aPostTypes ) . ',',  // must add a trailing comma for jQuery to detect the item.
            );
        }

        /**
         * Returns the HTML output of taxonomy checkboxes.
         *
         * @since       3.5.3
         * @since       3.8.8       Added the `$aAttributes` parameter.
         * @return      string      the generated HTML output of taxonomy checkboxes.
         * @internal
         */
        private function ___getTaxonomyCheckboxes( array $aField, $sKey, $sTaxonomySlug, $aAttributes ) {

            $_aTabBoxContainerArguments = array(
                'id'    => "tab_{$aField['input_id']}_{$sKey}",
                'class' => 'tab-box-content',
                'style' => $this->getInlineCSS(
                    array(
                        'height' => $this->getAOrB( $aField[ 'height' ], $this->getLengthSanitized( $aField[ 'height' ] ), null ),
                        'width'  => $this->getAOrB( $aField[ 'width' ], $this->getLengthSanitized( $aField[ 'width' ] ), null ),
                    )
                ),
            ) + $aAttributes;
            return "<div " . $this->getAttributes( $_aTabBoxContainerArguments ) . ">"
                    . $this->getElement( $aField, array( 'before_label', $sKey ) )
                    . "<div " . $this->getAttributes( $this->_getCheckboxContainerAttributes( $aField ) ) . ">"
                    . "</div>"
                    . "<ul class='list:category taxonomychecklist form-no-clear'>"
                        . $this->___getTaxonomyChecklist( $aField, $sKey, $sTaxonomySlug )
                    . "</ul>"
                    . "<!--[if IE]><b>.</b><![endif]-->"
                    . $this->getElement( $aField, array( 'after_label', $sKey ) )
                . "</div><!-- tab-box-content -->";

        }
            /**
             *
             * @param       array       $aField         Field definition array,
             * @param       string      $sKey           The array key of the 'taxonomy_slugs' argument array.
             * @param       string      $sTaxonomySlug  the taxonomy slug (id) such as category and post_tag
             * @internal
             * @return      string
             */
            private function ___getTaxonomyChecklist( $aField, $sKey, $sTaxonomySlug ) {
                return wp_list_categories(
                    array(
                        'walker'                => new AdminPageFramework_WalkerTaxonomyChecklist, // a walker class instance
                        'taxonomy'              => $sTaxonomySlug,
                        '_name_prefix'          => is_array( $aField[ 'taxonomy_slugs' ] )
                            ? "{$aField[ '_input_name' ]}[{$sTaxonomySlug}]"
                            : $aField[ '_input_name' ],   // name prefix of the input
                        '_input_id_prefix'      => $aField[ 'input_id' ],
                        '_attributes'           => $this->getElementAsArray(
                            $aField,
                            array( 'attributes', $sKey )
                        ) + $aField[ 'attributes' ],

                        // checked items ( term IDs ) e.g.  array( 6, 10, 7, 15 ),
                        '_selected_items'       => $this->___getSelectedKeyArray( $aField['value'], $sTaxonomySlug ),

                        'echo'                  => false,  // returns the output
                        'show_post_count'       => $aField[ 'show_post_count' ],      // 3.2.0+
                        'show_option_none'      => $aField[ 'label_no_term_found' ],  // 3.3.2+
                        'title_li'              => $aField[ 'label_list_title' ],     // 3.3.2+

                        '_save_unchecked'       => $aField[ 'save_unchecked' ], // 3.8.8+ Whether to insert hidden input element for unchecked.
                    )
                    + $this->getAsArray(
                        $this->getElement(
                            $aField,
                            array( 'queries', $sTaxonomySlug ),
                            array()
                        ),
                        true
                    )
                    + $aField[ 'query' ]
                );
            }

            /**
             * Returns an array consisting of keys whose value is `true`.
             *
             * @since   2.0.0
             * @param   array   $vValue         This can be either an one-dimensional array ( for single field ) or a two-dimensional array ( for multiple fields ).
             * @param   string  $sTaxonomySlug
             * @return  array   Returns an numerically indexed array holding the keys that yield true as the value. e.g. `array( 6, 10, 7, 15 )`
             * @internal
             */
            private function ___getSelectedKeyArray( $vValue, $sTaxonomySlug ) {
                $_aSelected = $this->getElementAsArray(
                    $this->getAsArray( $vValue ), // The initial value (null) may not be an array.
                    array( $sTaxonomySlug )
                );
                return array_keys( $_aSelected, true ); // return keys that are `true`.
            }

        /**
         * Returns an HTML tab list item output.
         *
         * @since       3.5.3
         * @since       3.8.8       Added the `$aAttributes` parameter.
         * @return      string      The generated HTML tab list item output.
         * @internal
         */
        private function ___getTaxonomyTab( $aField, $sKey, $sTaxonomySlug, $aAttributes ) {
            $_aLiAttributes = array(
                'class' => 'tab-box-tab',
            ) + $aAttributes;
            return "<li " . $this->getAttributes( $_aLiAttributes ) . ">"
                    . "<a href='#tab_{$aField['input_id']}_{$sKey}'>"
                        . "<span class='tab-box-tab-text'>"
                            . $this->___getLabelFromTaxonomySlug( $sTaxonomySlug )
                        . "</span>"
                    ."</a>"
                ."</li>";
        }
            /**
             * Retrieves the label of the given taxonomy by its slug.
             *
             * A helper function for the above getTaxonomyChecklistField() method.
             *
             * @since       2.1.1
             * @since       3.8.8       Changed the return value type to string from string|null.
             * @return      string
             * @internal
             */
            private function ___getLabelFromTaxonomySlug( $sTaxonomySlug ) {
                $_oTaxonomy = get_taxonomy( $sTaxonomySlug );
                return isset( $_oTaxonomy->label ) ? $_oTaxonomy->label : '';
            }

}