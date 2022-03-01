<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * The Table field type generates table outputs from given an array.
 *
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *     <li>**data** - (required, array|string) An array representing a table. When an associative array is passed, all the rows will consist of two columns of key-value pairs, suitable to inspect array contents. When a string value is passed, the value will be rendered. This is useful when creating FAQ with multiple tables.</li>
 *     <li>**stripe** - (optional, boolean) Whether to make the table rows striped or not.</li>
 *     <li>**collapsible** - (optional, boolean|array) whether to contain the table in a collapsible container. When an array is passed, the following sub-arguments are accepted.
 *          <ul>
 *              <li>**active** - (optional, boolean) Whether to open the container by default.</li>
 *              <li>**animate** - (optional, integer) The animation speed.  </li>
 *          </ul>
 *      </li>
 *      <li>**escape** - (optional, boolean) Whether to to escape HTML characters or not. When the inspecting data contains HTML code that shouldn't be rendered as HTML elements, turn this on; the code will be displayed instead of actual HTML elements.</li>
 *      <li>**caption** - (optional, string) The table title. When the <code>collapsible</code> argument is true, this is required.
 *      <li>**header** - (optional, array) Table header items which serving as column titles placed at the top of the table.
 *      <li>**footer** - (optional, array|string) Table footer items placed at the bottom of the table. When <code>USE_HEADER</code> is set and the <code>header</code> argument is set, the same value as the <code>header</code> argument is used.
 * </ul>
 *
 * <h3>Examples</h3>
 * <h4>Creating a Basic Table</h4>
 * <code>
 *   array(
 *       'field_id'          => 'basic',
 *       'type'              => 'table',
 *       'data'              => array(
 *           array(
 *               'Hat', 'ZA2001'
 *           ),
 *           array(
 *               'Jacket', 'ZB2002'
 *           ),
 *           array(
 *               'Shoe', 'ZB2003'
 *           ),
 *       ),
 *   )
 * </code>
 * <h4>Adding Header and Footer</h4>
 * <code>
 *   array(
 *       'type'              => 'table',
 *       'header'            => array(
 *           'Type', 'Code', 'Price'
 *       ),
 *       'footer'            => array(
 *           'Total', '', 180
 *       ),
 *       'data'              => array(
 *           array(
 *               'Hat', 'ZA2001', 20,
 *           ),
 *           array(
 *               'Jacket', 'ZB2002', 50,
 *           ),
 *           array(
 *               'Shoe', 'ZB2003', 40,
 *           ),
 *           array(
 *               'Watch', 'ZB2004', 70,
 *           ),
 *       ),
 *   )
 * </code>
 * <h4>Adjusting Layout with Custom Attributes</h4>
 * <code>
 *   array(
 *       'field_id'          => 'attributes',
 *       'type'              => 'table',
 *       'title'             => __( 'Attributes', 'admin-page-framework-loader' ),
 *       'header'            => array(
 *           'Type', 'Code', 'Price'
 *       ),
 *       'footer'            => array(
 *           'Total', '', 110
 *       ),
 *       'data'              => array(
 *           array(
 *               'Hat', 'ZA2001', 20,
 *           ),
 *           array(
 *               'Jacket', 'ZB2002', 50,
 *           ),
 *           array(
 *               'Shoe', 'ZB2003', 40,
 *           ),
 *       ),
 *       'attributes'        => array(
 *           'th' => array(
 *               // zero-based second column
 *               2 => array(
 *                   'style' => 'text-align: right; width: 10%;'
 *               ),
 *           ),
 *           'td' => array(
 *               // zero-based second column
 *               2 => array(
 *                   'style' => 'text-align: right; width: 10%;'
 *               ),
 *           ),
 *       ),
 *   )
 * </code>
 * <h4>Inspecting Array Data</h4>
 * To check array contents, pass multi-dimensional associative array to the <code>data</code> argument.
 * <code>
 *   array(
 *       'field_id'          => 'associative',
 *       'type'              => 'table',
 *       'title'             => __( 'Associative', 'admin-page-framework-loader' ),
 *       'data'              => array(
 *           "foo" => "bar",
 *           42    => 24,
 *           "multi" => array(
 *                "dimensional" => array(
 *                    "element" => "foo"
 *                )
 *           )
 *       ),
 *   )
 * </code>
 * <h4>Using Table Caption</h4>
 * With the <code>caption</code> argument, the title title can be set.
 * <code>
 *   array(
 *       'field_id'          => 'wide_table',
 *       'type'              => 'table',
 *       'show_title_column' => false,
 *       'data'              => array(
 *           'first_release' => '1995',
 *           'latest_release' => '7.3.11',
 *           'designed_by' => 'Rasmus Lerdorf',
 *           'description' => array(
 *               'extension' => '.php',
 *               'typing_discipline' => 'Dynamic, weak',
 *               'license' => 'PHP License (most of Zend engine
 *                    under Zend Engine License)'
 *           )
 *       ),
 *       'caption'           => __( 'Caption', 'admin-page-framework-loader' ),
 *   )
 * </code>
 * <h4>Table Header and Footer for Associative Arrays</h4>
 * Since associative array tables consist of only two columns in each row and the first column width has a set width, when you set a header and footer for those tables, set a key-value.
 * <code>
 *   array(
 *       'field_id'          => 'table_header_and_footer',
 *       'type'              => 'table',
 *       'data'              => array(
 *           'first_release' => '1991',
 *           'latest_release' => '3.8.0',
 *           'designed_by' => 'Guido van Rossum',
 *           'description' => array(
 *               'extension' => '.py',
 *               'typing_discipline' => 'Duck, dynamic, gradual',
 *               'license' => 'Python Software Foundation License'
 *           )
 *       ),
 *       'title'             => __( 'Footer and Header for Associative', 'admin-page-framework-loader' ),
 *       // for associative arrays, set key-value pairs to the header and footer
 *       'header'            => array( 'Custom Header Key' => 'Custom Header Value' ),
 *       'footer'            => array( 'Custom Footer Key' => 'Custom Footer Value' ),
 *   )
 * </code>
 * <h4>Placing Table in a Collapsible Container</h4>
 * <code>
 *   array(
 *       'field_id'          => 'collapsible',
 *       'title'             => __( 'Collapsible', 'admin-page-framework-loader' ),
 *       'type'              => 'table',
 *       'caption'           => 'WordPress',
 *       'collapsible'       => true,
 *       'data'              => array( 'foo' => 'bar' ),
 *   )
 * </code>
 * <h4>Creating Simple FAQ</h4>
 * <code>
 *   array(
 *       'field_id'          => 'simple_faq',
 *       'type'              => 'table',
 *       'collapsible'       => true,
 *       'caption'           => 'What day is it today?',
 *       'data'              => sprintf( 'Today is %1\$s.', date( 'l' ) ),
 *       array(
 *           'caption' => 'What time is it now?',
 *           'data'    => sprintf( 'Now is %1\$s.', date( get_option( 'date_format' ) ) ),
 *       )
 *   )
 * </code>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * @image   http://admin-page-framework.michaeluno.jp/image/common/form/field_type/table.png
 * @package AdminPageFramework/Common/Form/FieldType
 * @since   3.9.0
 */
class AdminPageFramework_FieldType_table extends AdminPageFramework_FieldType {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'table', );

    /**
     * Defines the default key-values of this field type.
     */
    protected $aDefaultKeys = array(
        'save'            => false,
        'data'            => array(),
        'stripe'          => true,
        'collapsible'     => false,
        'escape'          => false,
        'caption'         => '',
        'header'          => array(),  // (array) key-value pairs of the table header
        'footer'          => array(),  // (array|string) key-value pairs of the table footer. When `USE_HEADER` is passed, the value set to the `header` argument will be applied.

        // [Not fully implemented] To fully support most use cases, a library like the tablesorter jQuery plugin is big in size. And it is more suitable for a custom field type.
        // this argument just adds `sortable-column` class selector to specified column index but does nothing yet.
        'sortable_column' => array(),  // (array) linear array consisting of boolean values indicating which columns should be sortable by matching the array index with the header argument array index.
    );

    /**
     * @return array
     * @since  3.9.0
     */
    protected function getEnqueuingScripts() {
        return array(
            array(
                'handle_id'         => 'admin-page-framework-field-type-table',
                'src'               => dirname( __FILE__ ) . '/js/table.bundle.js',
                'in_footer'         => true,
                'dependencies'      => array( 'jquery', 'jquery-ui-accordion', 'admin-page-framework-script-form-main' ),
                'translation_var'   => 'AdminPageFrameworkFieldTypeTable',
                'translation'       => array(
                    'fieldTypeSlugs' => $this->aFieldTypeSlugs,
                    'label'          => array(),
                ),
            ),
        );
    }

    /**
     * Returns the output of the text input field.
     *
     * @since 3.9.0
     */
    protected function getField( $aField ) {
        return $aField[ 'before_label' ]
            . $this->___getLabel( $aField )
            . $aField[ 'after_label' ]
            . $aField[ 'before_input' ]
            . "<div class='table-container'>"
                . $this->___getTable( $aField )
            . "</div>"
            . $aField[ 'after_input' ];

    }
        /**
         * @param  array $aField
         * @return string
         */
        private function ___getLabel( $aField ) {
            if ( ! strlen( $aField[ 'label' ] ) ) {
                return '';
            }
            return "<div class='admin-page-framework-input-label-container'>"
                    . "<label for='" . esc_attr( $aField[ 'input_id' ] ) . "'>"
                    . "<span " . $this->getLabelContainerAttributes( $aField, 'admin-page-framework-input-label-string' ) . ">"
                        . $aField[ 'label' ]
                    . "</span>"
                    . "</label>"
                . "</div>";
        }
        /**
         * @param  array        $aField
         * @return string
         * @since  3.9.0
         */
        private function ___getTable( $aField ) {

            $_aAttributes = $this->___getTableAttributesFormatted( $aField );

            // Format the footer
            $_aFooter = 'USE_HEADER' === $aField[ 'footer' ]
                ? $aField[ 'header' ]
                : $aField[ 'footer' ];
            $_aCollapsible = $this->getAsArray( $aField[ 'collapsible' ] );

            // Non-collapsible tables
            if ( empty( $_aCollapsible ) ) {
                return $this->getTableOfArray(
                    $this->getAsArray( $aField[ 'data' ] ),
                    $_aAttributes,          // attributes
                    $aField[ 'header' ],    // header
                    $_aFooter,    // footer,
                    $aField[ 'escape' ],
                    $aField[ 'caption' ]
                );
            }

            // Collapsible tables
            $_sCaption = $aField[ 'caption' ]
                ? $aField[ 'caption' ]
                : __( 'Set the caption with the <code>caption</code> argument.', 'admin-page-framework' );
            $_sContent = is_scalar( $aField[ 'data' ] )
                ? "<div class='text-content'>{$aField[ 'data' ]}</div>" // this allows to create simple FAQ
                : $this->getTableOfArray(
                    $this->getAsArray( $aField[ 'data' ] ),
                    $_aAttributes,          // attributes,
                    $aField[ 'header' ],    // header
                    $_aFooter,    // footer,
                    $aField[ 'escape' ]
                );  // omit caption
            $_aCollapsible = $this->getAsArray( $_aCollapsible ) + array( 'active' => null );
            $_aCollapsible[ 'active' ] = is_numeric( $_aCollapsible[ 'active' ] )
                ? ( integer ) $_aCollapsible[ 'active' ]                // accepts numeric index values
                : ( $_aCollapsible[ 'active' ] ? 'true' : 'false' );    // passing a value as string to be parsed properly on the JS side
            return "<div class='accordion-container' " . $this->getDataAttributes( $_aCollapsible ) . ">"
                    . "<div class='accordion-title'><h4><span>{$_sCaption}</span></h4></div>"
                    . "<div class='accordion-content'>{$_sContent}</div>"
                . "</div>";

        }

            private function ___getTableAttributesFormatted( array $aField ) {

                // Add field type specific class attributes
                $_aAttributes = $aField[ 'attributes' ];
                $this->setMultiDimensionalArray(
                    $_aAttributes,
                    array( 'table', 'class' ),
                    $this->getClassAttribute( $this->getElementAsArray( $_aAttributes, array( 'table', 'class' ) ), 'widefat fixed', $aField[ 'stripe' ] ? "striped " : '' )
                );

                // Add sortable header and footer classes
                foreach( $this->getAsArray( $aField[ 'sortable_column' ] ) as $_iColumnIndex => $_bSortable ) {
                    if ( empty( $_bSortable ) ) {
                        continue;
                    }
                    $this->setMultiDimensionalArray(
                        $_aAttributes,
                        array( 'th', $_iColumnIndex, 'class' ),
                        $this->getClassAttribute( $this->getElementAsArray( $_aAttributes, array( 'th', $_iColumnIndex, 'class' ) ), 'sortable-column' )
                    );
                    $this->setMultiDimensionalArray(
                        $_aAttributes,
                        array( 'td', $_iColumnIndex, 'class' ),
                        $this->getClassAttribute( $this->getElementAsArray( $_aAttributes, array( 'th', $_iColumnIndex, 'class' ) ), 'sortable-column' )
                    );
                }
                return $_aAttributes;

            }

}