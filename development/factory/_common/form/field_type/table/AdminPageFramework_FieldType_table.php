<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * The Table field type generates table outputs from given an array.
 *
 *
 * <h3>Example</h3>
 * <code>
// @todo add an example
 * </code>
 *
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