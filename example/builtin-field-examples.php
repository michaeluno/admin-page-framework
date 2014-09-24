<?php
/*
 * Text input - text, password, number, textarea, rich text editor
 */
$this->addSettingFields(
    'text_fields',
    array( // Single text field
        'field_id'          => 'text',
        // 'section_id'     => 'text_fields', // can be omitted as it is set previously
        'title'             => __( 'Text', 'admin-page-framework-demo' ),
        'description'       => __( 'Type something here. This text is inserted with the <code>description</code> argument in the field definition array.', 'admin-page-framework-demo' ),
        'help'              => __( 'This is a text field and typed text will be saved.', 'admin-page-framework-demo' )
            . ' ' . __( 'This text is inserted with the <code>help</code> argument in the field definition array.', 'admin-page-framework-demo' ),
        'type'              => 'text',
        'order'             => 1, // ( optional )
        'default'           => 123456,
        'attributes'        => array(
            'size' => 40,
        ),
    ),    
    array( // Password Field
        'field_id'          => 'password',
        'title'             => __( 'Password', 'admin-page-framework-demo' ),
        'tip'               => __( 'This input will be masked.', 'admin-page-framework-demo' ),
        'type'              => 'password',
        'help'              => __( 'This is a password type field; the user\'s entered input will be masked.', 'admin-page-framework-demo' ), //'
        'attributes'        => array(
            'size' => 20,
        ),
        'description'       => __( 'The entered characters will be masked.', 'admin-page-framework-demo' ),
    ),     
    array( // Read-only
        'field_id'          => 'read_only_text',
        'title'             => __( 'Read Only', 'admin-page-framework-demo' ),
        'type'              => 'text',
        'attributes'        => array(
            'size'          => 20,
            'readonly'      => 'ReadOnly',
            // 'disabled' => 'Disabled', // disabled can be specified like so
        ),
        'value'             => __( 'This is a read-only value.', 'admin-page-framework-demo' ),
        'description'       => __( 'The attribute can be set with the <code>attributes</code> argument.', 'admin-page-framework-demo' ),
    ),     
    array( // Number Field
        'field_id'          => 'number',
        'title'             => __( 'Number', 'admin-page-framework-demo' ),
        'type'              => 'number',
    ),     
    array( // Multiple text fields
        'field_id'          => 'text_multiple',
        'title'             => __( 'Multiple', 'admin-page-framework-demo' ),
        'help'              => __( 'Multiple text fields can be set by passing an array to the <code>label</code> argument.', 'admin-page-framework-demo' ),
        'type'              => 'text',
        'default'           => __( 'Hello world!', 'admin-page-framework-demo' ),
        'label'             => __( 'First', 'admin-page-framework-demo' ) . ': ',
        'attributes'        => array(
            'size' => 20,     
        ),
        'capability'        => 'manage_options',     
        'delimiter'         => '<br />',
        array(
            'default'       => 'Foo bar',
            'label'         => __( 'Second', 'admin-page-framework-demo' ) . ': ',
            'attributes'    => array(
                'size' => 40,
            )
        ),
        array(
            'default'       => __( 'Yes, we can', 'admin-page-framework-demo' ),
            'label'         => __( 'Third', 'admin-page-framework-demo' ) . ': ',
            'attributes'    => array(
                'size' => 60,
            )
        ),     
        'description'       => __( 'These are multiple text fields. To include multiple input fields associated with one field ID, use the numeric keys in the field definition array.', 'admin-page-framework-demo' ),
    ),     
    array( // Repeatable text fields
        'field_id'          => 'text_repeatable',
        'title'             => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type'              => 'text',
        'default'           => 'a',
        'capability'        => 'manage_options',
        'repeatable'        => array(
            'max' => 10,
            'min' => 3,
        ),
        'description'       => __( 'Press + / - to add / remove the fields. To enable the repeatable fields functionality, set the <code>repeatable</code> argument to <code>true</code>.', 'admin-page-framework-demo' )
            . __( 'To set maximum and minimum numbers of fields, set the <code>max</code> and <code>min</code> arguments in the <code>repeatable<code> argument array in the field deifnition array.' ),
    ),     
    array( // Sortable text fields
        'field_id'          => 'text_sortable',
        'title'             => __( 'Sortable', 'admin-page-framework-demo' ),
        'type'              => 'text',
        'default'           => 'a',
        'label'             => __( 'Sortable Item', 'admin-page-framework-demo' ),
        'sortable'          =>    true,
        'description'       => __( 'Drag and drop the fields to change the order.', 'admin-page-framework-demo' ),
        array(
            'default'       => 'b',
        ),
        array(
            'default'       => 'c',
        ),     
        array(
            'label'         => __( 'Disabled Item', 'admin-page-framework-demo' ),
            'default'       => 'd',
            'attributes'    => array(
                'disabled' => 'Disabled',
            ),
        ),     
        'delimiter'     => '<br />',
    ),    
    array( // Sortable + Repeatable text fields
        'field_id'      => 'text_repeatable_and_sortable',
        'title'         => __( 'Repeatable & Sortable', 'admin-page-framework-demo' ),
        'type'          => 'text',
        'repeatable'    => true,
        'sortable'      => true,
    ),     
    array( // Text Area
        'field_id'      => 'textarea',
        'title'         => __( 'Text Area', 'admin-page-framework-demo' ),
        'description'   => __( 'Type a text string here.', 'admin-page-framework-demo' ),
        'type'          => 'textarea',
        'default'       => __( 'Hello world!', 'admin-page-framework-demo' ) 
            . ' ' . __( 'This is set as the default string.', 'admin-page-framework-demo' ),
        'attributes'    => array(
            'rows' => 6,
            'cols' => 60,
        ),
    ),
    array( // Repeatable Text Areas
        'field_id'      => 'textarea_repeatable',
        'title'         => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type'          => 'textarea',
        'repeatable'    => array(
            'max' => 20,
            'min' => 2,
        ),
        'attributes'    => array(
            'rows' => 3,
            'cols' => 60,
        ),
    ),     
    array( // Sortable Text Areas
        'field_id'      => 'textarea_sortable',
        'title'         => __( 'Sortable', 'admin-page-framework-demo' ),
        'type'          => 'textarea',
        'sortable'      =>    true,
        'label'         => __( 'Sortable Item', 'admin-page-framework-demo' ),
        array(), // the second item
        array(), // the third item
    ),     
    array( // Rich Text Editors
        'field_id'      => 'rich_textarea',
        'title'         => __( 'Rich Text Area', 'admin-page-framework-demo' ),
        'type'          => 'textarea',
        'rich'          =>    true, // just pass non empty value to enable the rich editor.
        'attributes'    => array(
            'field' => array(
                'style' => 'width: 100%;' // since the rich editor does not accept the cols attribute, set the width by inline-style.
            ),
        ),
        'description'   =>  __( 'The arguments can be passed to the <code>rich</code> argument.', 'admin-page-framework-demo' )
            . sprintf( __( 'For more information see the <a href="%1$s">Codex page</a>.', 'admin-page-framework-demo' ), 'http://codex.wordpress.org/Function_Reference/wp_editor#Parameters' ),
        array(
            // pass the setting array to customize the editor. For the setting argument, see http://codex.wordpress.org/Function_Reference/wp_editor.
            'rich' => array( 
                'media_buttons' => false, 
                'tinymce'       => false
            ),    
        ),
    ),     
    array( // Multiple text areas
        'field_id'      => 'textarea_multiple',
        'title'         => __( 'Multiple', 'admin-page-framework-demo' ),
        'description'   => __( 'These are multiple text areas.', 'admin-page-framework-demo' ),
        'type'          => 'textarea',
        'label'         => __( 'First', 'admin-page-framework-demo' ),
        'default'       => __( 'The first default text.', 'admin-page-framework-demo' ),
        'delimiter'     => '<br />',
        'attributes'    => array(
            'rows' => 5,
            'cols' => 60,
        ),
        array(
            'label'         => __( 'Second', 'admin-page-framework-demo' ),
            'default'       => __( 'The second default text. See the background color is different from the others. This is done with the <code>attributes</code> argument.', 'admin-page-framework-demo' ),
            'attributes'    => array(
                'rows'  => 3,
                'cols'  => 40,
                'style' => 'background-color: #F0F8FA;' // this changes the style of the textarea tag.
            ),     
        ),
        array(
            'label'         => __( 'Third', 'admin-page-framework-demo' ),
            'default'       => __( 'The third default text.', 'admin-page-framework-demo' ),
            'attributes'    => array(
                'rows' => 2,
                'cols' => 20,
            ),     
        ),    
    ),
    array( // Repeatable TinyMCE Text Editor [3.1.6+]
        'field_id'      => 'repeatable_rich_textarea',
        'title'         => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type'          => 'textarea',
        'rich'          => true,
        'repeatable'    => true,
        'description'   => __( 'As of v3.1.6, repeatable TinyMCE editor fields are supported. However, Quick Tags are not supported.', 'admin-page-framework-demo' ),
    )
);

/*
 * Selectors - dropdown (pulldown) list, checkbox, radio buttons, size selector
 */
$this->addSettingFields(
    'selectors', // the target section ID
    array( // Single Drop-down List
        'field_id'      => 'select',
        'title'         => __( 'Dropdown List', 'admin-page-framework-demo' ),
        'type'          => 'select',
        'help'          => __( 'This is the <em>select</em> field type.', 'admin-page-framework-demo' ),
        'default'       => 2, // the index key of the label array below which yields 'Yellow'.
        'label'         => array( 
            0 => __( 'Red', 'admin-page-framework-demo' ),
            1 => __( 'Blue', 'admin-page-framework-demo' ),
            2 => __( 'Yellow', 'admin-page-framework-demo' ),
            3 => __( 'Orange', 'admin-page-framework-demo' ),
        ),
        'description' => __( 'The key of the array of the <code>label</code> argument serves as the value of the option tag which will be sent to the form and saved in the database.', 'admin-page-framework-demo' )
            . ' ' . __( 'So when you specify the default value with the <code>default</code> or <code>value</code> argument, specify the <em>KEY</em>.', 'admin-page-framework-demo' ),
    ),    
    array( // Single Drop-down List with Multiple Options
        'field_id'      => 'select_multiple_options',
        // 'section_id' => 'selectors', // <-- this can be omitted since it is set in the previous field array
        'title'         => __( 'Multiple', 'admin-page-framework-demo' ),
        'help'          => __( 'This is the <em>select</em> field type with multiple elements.', 'admin-page-framework' ),
        'type'          => 'select',
        'is_multiple'   => true,
        'default'       => 3, // note that PHP array indices are zero-base, meaning the index count starts from 0 (not 1). 3 here means the fourth item of the array.  
        'label'         => array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'November', 'October', 'December' ),
        'description'   => __( 'Use <code>is_multiple</code> argument to enable multiple selections.' ),
        'attributes'    =>  array(
            'select'    =>  array(
                'size'  => 10,
            ),
        ),
    ),    
    array( // Single Drop-down List with Multiple Options
        'field_id'      => 'select_multiple_groups',
        'title'         => __( 'Grouping', 'admin-page-framework-demo' ),
        'type'          => 'select',
        'default'       => 'b',
        'label'         => array(     
            'alphabets' => array(     // each key must be unique throughout this 'label' element array.
                'a' => 'a',     
                'b' => 'b', 
                'c' => 'c',
            ),
            'numbers' => array( 
                0 => '0',
                1 => '1',
                2 => '2', 
            ),
        ),
        'attributes'    => array( // the 'attributes' element of the select field type has three keys: select, 'option', and 'optgroup'.
            'select' => array(
                'style' => "width: 200px;",
            ),
            'option' => array(
                1 => array(
                    'disabled' => 'Disabled',
                    'style' => 'background-color: #ECECEC; color: #888;',
                ),
            ),
            'optgroup' => array(
                'style' => 'background-color: #DDD',
            )
        ),
        'description'   => __( 'To create grouped options, pass arrays with the key of the group label and pass the options as an array inside them.', 'admin-page-framework-demo' )
            . ' ' . __( 'To style the pulldown (dropdown) list, use the <code>attributes</code> argument. For the <code>select</code> field type, it has three major keys, <code>select</code>, <code>option</code>, and <code>optgroup</code>, representing the tag names.', 'admin-page-framework-demo' ),

    ),     
    array( // Drop-down Lists with Mixed Types
        'field_id'      => 'select_multiple_fields',
        'title'         => __( 'Multiple', 'admin-page-framework-demo' ),
        'description'   => __( 'These are multiple sets of drop down list.', 'admin-page-framework-demo' ),
        'type'          => 'select',
        'label'         => array( 'dark', 'light' ),
        'default'       => 1,
        'attributes'    => array(    
            'select'    => array(
                'size' => 1,
            ),
            'field'     => array(
                'style' => 'display: inline; clear: none', // this makes the field element inline, which means next fields continues from the right end of the field, not from the new line.
            ),
        ),
        array(
            'label'     => array( 'river', 'mountain', 'sky', ),
            'default'   => 2,
        ),
        array(
            'label'         => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
            'default'       => array( 3, 4 ), // 'default' => '', will select none
            'attributes'    => array(
                'select' => array(
                    'size' => 5,
                    'multiple' => 'multiple', // instead of 'is_multiple' =>    true, it is possible by setting it by the attribute key.
                ),
            )     
        ),
    ),     
    array( // Repeatable Drop-down List
        'field_id'      => 'select_repeatable',
        'title'         => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type'          => 'select',
        'repeatable'    =>    true,
        'description'   => __( 'To enable repeatable fields, pass <code>true</code> to the <code>repeatable</code> argument.', 'admin-page-framework-demo' ),
        'default'       => 'y',
        'label' => array( 
            'x' => 'X',
            'y' => 'Y',     
            'z' => 'Z',     
        ),
    ),     
    array( // Sortable Drop-down List
        'field_id'      => 'select_sortable',
        'title'         => __( 'Sortable', 'admin-page-framework-demo' ),
        'type'          => 'select',
        'sortable'      => true,
        'default'       => 'iii',
        'before_label'  => 
            "<span style='vertical-align:baseline; min-width: 140px; display:inline-block; margin-top: 0.5em; padding-bottom: 0.2em;'>" 
                . __( 'Sortable Item', 'admin-page-framework-demo' ) 
            . "</span>",
        'label'         => array( 
            'i'     => 'I',
            'ii'    => 'II',    
            'iii'   => 'III',     
            'iiv'   => 'IIV',     
        ),
        array(), // the second item - will inherit the main field's arguments
        array(), // the third item
        array(), // the forth item
    ),     
    array( // Single set of radio buttons
        'field_id'      => 'radio',
        'title'         => __( 'Radio Button', 'admin-page-framework-demo' ),
        'type'          => 'radio',
        'label'         => array(
            'a' => 'Apple',
            'b' => 'Banana ( this option is disabled. )',
            'c' => 'Cherry' 
        ),
        'default'       => 'c', // yields Cherry; its key is specified.
        'after_label'   => '<br />',
        'attributes'    => array(
            'b' => array(
                'disabled' => 'Disabled',
            ),
        ),
        'description'   => __( 'Use the <code>after_input</code> argument to insert <code>&lt;br /&gt;</code> after each sub-field.', 'admin-page-framework-demo' )
            . ' ' . __( 'To disable elements (or apply different attributes) on an individual element basis, use the <code>attributes</code> argument and create the element whose key name is the radio input element value.', 'admin-page-framework-demo' ),
        
    ),
    array( // Multiple sets of radio buttons
        'field_id'      => 'radio_multiple',
        'title'         => __( 'Multiple Sets', 'admin-page-framework-demo' ),
        'description'   => __( 'Multiple sets of radio buttons. The horizontal line is set with the <code>delimiter</code> argument.', 'admin-page-framework-demo' ),
        'type'          => 'radio',
        'delimiter'     => '<hr />',
        'default'       => 2,
        'label'         => array( 
            1 => 'one',
            2 => 'two',
        ),
        'attributes'    => array(
            'field' => array(
                'style' => 'width: 100%;',
            ),
        ),
        array(
            'default'   => 4,
            'label'     => array( 
                3 => 'three',
                4 => 'four',    
                5 => 'five' 
            ),
        ),
        array(
            'default'   => 8,
            'label'     => array( 
                6 => 'six',
                7 => 'seven',
                8 => 'eight',
                9 => 'nine' 
            ),
        ),
    ),    
    array( // Repeatable radio buttons
        'field_id'      => 'radio_repeatable',
        'title'         => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type'          => 'radio',
        'label'         => array( 
            1 => 'On', 
            0 => 'Off'
        ),
        'default' => 1, // set the key of the label array
        'repeatable' =>    true,
    ),    
    array( // Sortable radio buttons
        'field_id'      => 'radio_sortable',
        'title'         => __( 'Sortable', 'admin-page-framework-demo' ),
        'type'          => 'radio',
        'label'         => array( 
            1 => __( 'One', 'admin-page-framework-demo' ),
            2 => __( 'Two', 'admin-page-framework-demo' ),
            3 => __( 'Three', 'admin-page-framework-demo' ),
        ),
        'default'       => 1, // set the key of the label array
        'sortable'      =>    true,
        array(), // the second item
        array(), // the third item
        array(), // the fourth item
    ),     
    array( // Single checkbox item - set a check box item to the 'label' element.
        'field_id'      => 'checkbox',
        'title'         => __( 'Checkbox', 'admin-page-framework-demo' ),
        'tip'           => __( 'The <code>description</code> argument can be omitted though.', 'admin-page-framework-demo' ),
        'type'          => 'checkbox',
        'label'         => __( 'This is a check box.', 'admin-page-framework-demo' ) 
            . ' ' . __( 'A string can be passed to the <code>label</code> argument for a single item.', 'admin-page-framework-demo' ), 
        'default'   => false,
    ),    
    array( // Multiple checkbox items - for multiple checkbox items, set an array to the 'label' element.
        'field_id'      => 'checkbox_multiple_items',
        'title'         => __( 'Multiple', 'admin-page-framework-demo' ),
        'type'          => 'checkbox',
        'label'         => array( 
            'moon'  => __( 'Moon', 'admin-page-framework-demo' ),
            'earth' => __( 'Earth', 'admin-page-framework-demo' ) . ' (' . __( 'this option is disabled.', 'admin-page-framework-demo' ) . ')',
            'sun'   => __( 'Sun', 'admin-page-framework-demo' ),
            'mars'  => __( 'Mars', 'admin-page-framework-demo' ),
        ),
        'default'       => array( 
            'moon'  => true, 
            'earth' => false, 
            'sun'   => true, 
            'mars'  => false,
        ),
        'attributes'    => array(
            'earth' => array(
                'disabled' => 'Disabled',
            ),
        ),
        'description'   => __( 'It is possible to disable checkbox items on an individual basis.', 'admin-page-framework-demo' ),
        'after_label'   => '<br />',
    ),
    array( // Multiple sets of checkbox fields
        'field_id'      => 'checkbox_multiple_fields',
        'title'         => __( 'Multiple Sets', 'admin-page-framework-demo' ),
        'type'          => 'checkbox',
        'label'         => array( 
            'a' => 'A',
            'b' => 'B',
            'c' => 'C' 
        ),
        'default'       => array( 
            'a' => false,
            'b' => true,
            'c' => false 
        ),
        'delimiter'     => '<hr />',
        'attributes'    => array(
            'field' => array(
                'style' => 'width: 100%;',
            ),
        ),     
        array(
            'label' => array(
                'd' => 'D',
                'e' => 'E',
                'f' => 'F' 
            ),
            'default' => array(
                'd' => true,
                'e' => false,
                'f' => false 
            ),
        ),
        array(
            'label' => array(
                'g' => 'G',
                'h' => 'H',
                'i' => 'I'
            ),
            'default' => array(
                'g' => false,
                'h' => false,
                'i' => true 
            ),
        ),     
        'description'   => __( 'To create multiple fields for one field ID, use the numeric keys in the field definition array.', 'admin-page-framework-demo' ),
    ),
    array( // Repeatable check boxes
        'field_id'      => 'checkbox_repeatable_fields',
        'title'         => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type'          => 'checkbox',
        'label'         => array( 'x', 'y', 'z' ),
        'repeatable'    =>    true,
    ),
    array( // sortable check boxes
        'field_id'      => 'checkbox_sortable_fields',
        'title'         => __( 'Sortable', 'admin-page-framework-demo' ),
        'type'          => 'checkbox',
        'label'         => array( 'x', 'y', 'z' ),
        'sortable'      =>    true,
        array(), // the second item
        array(), // the third item
        array(), // the fourth item
    ),     
    array( // Size
        'field_id'      => 'size_field',
        'section_id'    => 'sizes', // changes the target section ID
        'title'         => __( 'Size', 'admin-page-framework-demo' ),
        'help'          => $sDescription = __( 'In order to set a default value for the size field type, an array with the <code>size</code> and the <code>unit</code> arguments needs to be set.', 'admin-page-framework-demo' ),
        'description'   => __( 'The default units and the lengths for CSS.', 'admin-page-framework-demo' ) 
            . ' ' . $sDescription,
        'type'          => 'size',
        'default'       => array( 
            'size' => 5, 
            'unit' => '%',
        ),
    ),     
    array( // Size with custom units
        'field_id'      => 'size_custom_unit_field',
        'title'         => __( 'Size with Custom Units', 'admin-page-framework-demo' ),
        'help'          => $sDescription = __( 'The units can be specified so it can be quantity, length, or capacity etc.', 'admin-page-framework-demo' ),
        'description'   => $sDescription,
        'type'          => 'size',
        'units'         => array(
            'grain'     => __( 'grains', 'admin-page-framework-demo' ),
            'dram'      => __( 'drams', 'admin-page-framework-demo' ),
            'ounce'     => __( 'ounces', 'admin-page-framework-demo' ),
            'pounds'    => __( 'pounds', 'admin-page-framework-demo' ),
        ),
        'default' => array( 
            'size'      => 200,
            'unit'      => 'ounce' 
        ),
    ),    
    array( // Size with custom attributes
        'field_id'      => 'size_field_custom_attributes',
        'title'         => __( 'Size with Custom Attributes', 'admin-page-framework-demo' ),
        'type'          => 'size',
        'units'         => array( // Pass the group label as the key of an option array.
            __( 'Metric Unit System', 'admin-page-framework' ) => array(     // each key must be unique throughout this 'label' element array.
                'mm'    => 'mm (' . __( 'millimetre', 'admin-page-framework' ) . ')', 
                'cm'    => 'cm (' . __( 'centmeter', 'admin-page-framework' ) . ')', 
                'm'     => 'm (' . __( 'meter', 'admin-page-framework' ) . ')', 
                'km'    => 'km (' . __( 'kilometer', 'admin-page-framework' ) . ')', 
            ),
            __( 'Imperial and US Unit System', 'admin-page-framework' ) => array( 
                'in'    => 'in (' . __( 'inch', 'admin-page-framework' ) . ')', 
                'ft'    => 'ft (' . __( 'foot', 'admin-page-framework' ) . ')', 
                'yd'    => 'yd (' . __( 'yard', 'admin-page-framework' ) . ')', 
                'ml'    => 'ml (' . __( 'mile', 'admin-page-framework' ) . ')', 
            ),     
            __( 'Astronomical Units', 'admin-page-framework' ) => array( 
                'au'    => 'au (' . __( 'astronomical unit', 'admin-page-framework' ) . ')', 
                'ly'    => 'ly (' . __( 'light year', 'admin-page-framework' ) . ')', 
                'pc'    => 'pc (' . __( 'parsec', 'admin-page-framework' ) . ')', 
            ),     
        ),
        'default'       => array( 
            'size' => 15.2, 
            'unit' => 'ft' 
        ),
        'attributes'    => array( // the size field type has four initial keys: size, option, optgroup.
            'size'      => array(
                'style' => 'background-color: #FAF0F0;',
                'step'  => 0.1,
            ),
            'unit'      => array(
                'style' => 'background-color: #F0FAF4',
            ),
            'option'    => array(
                'cm' => array( // applies only to the 'cm' element of the option elements
                    'disabled'  => 'Disabled',
                    'class'     => 'disabled',
                ),
                'style' => 'background-color: #F7EFFF', // applies to all the option elements
            ),
            'optgroup'  => array(
                'style' => 'background-color: #EFEFEF',
                __( 'Astronomical Units', 'admin-page-framework' ) => array(
                    'disabled' => 'Disabled',
                ),
            ),
        ),
        'description'   => __( 'The <code>size</code> field type has four initial keys in the <code>attributes</code> array element: <code>size</code>, <code>unit</code>, <code>optgroup</code>, and <code>option</code>.', 'admin-page-framework-demo' ),
    ),
    array( // Multiple Size Fields
        'field_id'      => 'sizes_field',
        'title'         => __( 'Multiple', 'admin-page-framework-demo' ),
        'type'          => 'size',
        'label'         => __( 'Weight', 'admin-page-framework-demo' ),
        'units'         => array( 'mg'=>'mg', 'g'=>'g', 'kg'=>'kg' ),
        'default'       => array( 'size' => 15, 'unit' => 'g' ),
        'delimiter'     => '<hr />',
        array(
            'label'     => __( 'Length', 'admin-page-framework-demo' ),
            'units'     => array( 'cm'=>'cm', 'mm'=>'mm', 'm'=>'m' ),
            'default'   => array( 'size' => 100, 'unit' => 'mm' ),
        ),
        array(
            'label'     => __( 'File Size', 'admin-page-framework-demo' ),
            'units'     => array( 'b'=>'b', 'kb'=>'kb', 'mb'=>'mb', 'gb' => 'gb', 'tb' => 'tb' ),
            'default'   => array( 'size' => 30, 'unit' => 'mb' ),
        ),     
    ),
    array( // Repeatable Size Fields
        'field_id'      => 'size_repeatable_fields',
        'title'         => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type'          => 'size',
        'repeatable'    => true,
    ),
    array( // Sortable Size Fields
        'field_id'      => 'size_sortable_fields',
        'title'         => __( 'Sortable', 'admin-page-framework-demo' ),
        'type'          => 'size',
        'sortable'      => true,
        array(), // the second item
        array(), // the third item
        array(), // the fourth item
    )     
);

/*
 * Files - media, image, and uploader
 */
$this->addSettingFields(     
    array( // Image Selector
        'field_id'      => 'image_select_field',
        'section_id'    => 'image_select',
        'title'         => __( 'Select an Image', 'admin-page-framework-demo' ),
        'type'          => 'image',
        'label'         => __( 'First', 'admin-page-framework-demo' ),
        'default'       =>  plugins_url( 'asset/image/wordpress-logo-2x.png' , APFDEMO_FILE ),
        'allow_external_source' => false,
        'attributes'    => array(
            'preview' => array(
                'style' => 'max-width:400px;' // determines the size of the preview image. // margin-left: auto; margin-right: auto; will make the image in the center.
            ),
        ),
        array(
            'label'         => __( 'Second', 'admin-page-framework-demo' ),
            'default'       => '',
            'allow_external_source' => true,
            'attributes'    => array(
                'input'     => array(
                    'style' => 'background-color: #F5FFDF',
                ),
                'button'    => array(
                    'style' => 'background-color: #E1FCD2',
                ),
                'remove_button'    => array(
                    'style' => 'background-color: #E1FCD2',
                ),                
            ),     
        ),
        array(
            'label'         => __( 'Third', 'admin-page-framework-demo' ),
            'default'       => '',
        ),     
        'description'   => __( 'See the button and the input colors of the second item are different. This is done by setting the attributes individually.', 'admin-page-framework-demo' ),
    ),     
    array( // Image selector with additional capturing attributes
        'field_id'              => 'image_with_attributes',
        'section_id'            => 'image_select',
        'title'                 => __( 'Save Image Attributes', 'admin-page-framework-demo' ),
        'type'                  => 'image',
        'attributes_to_store'   => array( 'alt', 'id', 'title', 'caption', 'width', 'height', 'align', 'link' ), // some attributes cannot be captured with external URLs and the old media uploader.
        'attributes'            => array(
            // To use a custom text label, pass the label to the 'data-label' attribute.
            'button'        => array(
                'data-label' => __( 'Select Image', 'admin-page-framework-demo' ),
            ),
            'remove_button' => array(      // 3.2.0+
                'data-label' => __( 'Remove', 'admin-page-framework-demo' ), // will set the Remove button label instead of the dashicon
            ),
        ),
    ),     
    array( // Repeatable Image Fields
        'field_id'              => 'image_select_field_repeater',
        'title'                 => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type'                  => 'image',
        'repeatable'            => true,
        'attributes'            => array(
            'preview' => array(
                'style' => 'max-width: 300px;'
            ),
        ),    
        'description'           => __( 'In repeatable fields, you can select multiple items at once.', 'admin-page-framework-demo' ),
    ),
    array( // Sortable Image Fields
        'field_id'              => 'image_select_field_sortable',
        'title'                 => __( 'Sortable', 'admin-page-framework-demo' ),
        'type'                  => 'image',
        'sortable'              => true,
        'attributes'            => array(
            'preview' => array(
                'style' => 'max-width: 200px;'
            ),
        ),    
        array(), // the second item
        array(), // the third item
        'description' => __( 'Image fields can be sortable. This may be useful when you need to let the user set an order of images.', 'admin-page-framework-demo' ),
    ),     
    array( // Repeatable & Sortable Image Fields
        'field_id'              => 'image_select_field_repeatable_and_sortable',
        'title'                 => __( 'Repeatable & Sortable', 'admin-page-framework-demo' ),
        'type'                  => 'image',
        'repeatable'            => true,
        'sortable'              => true,
        'attributes'            => array(
            'preview' => array(
                'style' => 'max-width: 200px;'
            ),
        ),    
    ),     
    array( // Media File
        'field_id'              => 'media_field',
        'section_id'            => 'media_upload',
        'title'                 => __( 'Media File', 'admin-page-framework-demo' ),
        'type'                  => 'media',
        'allow_external_source' => false,
    ),    
    array( // Media File with Attributes
        'field_id'              => 'media_with_attributes',
        'title'                 => __( 'Media File with Attributes', 'admin-page-framework-demo' ),
        'type'                  => 'media',
        'attributes_to_store'   => array( 'id', 'caption', 'description' ),
        'attributes'            => array(
            'button'        => array(
                'data-label' => __( 'Select File', 'admin-page-framework-demo' ),
            ),
            'remove_button' => array(      // 3.2.0+
                'data-label' => __( 'Remove', 'admin-page-framework-demo' ), // will set the Remove button label instead of the dashicon
            ),
        ),        
    ),     
    array( // Repeatable Media Files
        'field_id'              => 'media_repeatable_fields',
        'title'                 => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type'                  => 'media',
        'repeatable'            => true,
    ),     
    array( // Sortable Media Files
        'field_id'              => 'media_sortable_fields',
        'title'                 => __( 'Sortable', 'admin-page-framework-demo' ),
        'type'                  => 'media',
        'sortable'              => true,
        array(), // the second item
        array(), // the third item.
    ),     
    array( // Single File Upload Field
        'field_id'              => 'file_single',
        'section_id'            => 'file_uploads',
        'title'                 => __( 'File', 'admin-page-framework-demo' ),
        'type'                  => 'file',
        'label'                 => __( 'Select the file', 'admin-page-framework-demo' ) . ": ",
    ),     
    array( // Multiple File Upload Fields
        'field_id'              => 'file_multiple',
        'title'                 => __( 'Multiple', 'admin-page-framework-demo' ),
        'type'                  => 'file',
        'label'                 => __( 'First', 'admin-page-framework-demo' ),
        'delimiter'             => '<br />',
        array(
            'label' => __( 'Second', 'admin-page-framework-demo' ),
        ),
        array(
            'label' => __( 'Third', 'admin-page-framework-demo' ),
        ),     
    ),     
    array( // Single File Upload Field
        'field_id'              => 'file_repeatable',
        'title'                 => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type'                  => 'file',
        'repeatable'            => true,
    ),
    array()
);

/*
 * Check lists
 */
$this->addSettingFields(     
    array(
        'field_id'              => 'post_type_checklist',
        'section_id'            => 'checklists',
        'title'                 => __( 'Post Types', 'admin-page-framework-demo' ),
        'type'                  => 'posttype',
    ),     
    array(
        'field_id'              => 'post_type_multiple_checklists',
        'title'                 => __( 'Multiple', 'admin-page-framework-demo' ),
        'type'                  => 'posttype',
        'before_field'          => '<p style="clear: both; font-weight: bold;">' . __( 'For A', 'admin-page-framework-demo' ) . '</p>',
        array(
            'before_field' => '<p style="clear: both; font-weight: bold;">' . __( 'For B', 'admin-page-framework-demo' ) . '</p>',
        ),
        array(
            'before_field' => '<p style="clear: both; font-weight: bold;">' . __( 'For C', 'admin-page-framework-demo' ) . '</p>',
        ),
        'attributes'            => array(
            'field' => array(
                'style' => 'margin-bottom: 1em;',
            )
        ),
    ),     
    array(
        'field_id'              => 'post_type_checklist_repeatable',
        'title'                 => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type'                  => 'posttype',
        'repeatable'            => true,
        'delimiter'             => '<hr />',
    ),      
    array(  
        'field_id'              => 'taxonomy_checklist',
        'title'                 => __( 'Taxonomy Checklist', 'admin-page-framework-demo' ),
        'type'                  => 'taxonomy',
        'height'                => '200px', // (optional)
        'show_post_count'       => true,    // (optional) whether to show the post count. Default: false.
        'taxonomy_slugs'        => array( 'category', 'post_tag' ),
    ),      
    array(  
        'field_id'              => 'taxonomy_checklist_all',
        'title'                 => __( 'All Taxonomies', 'admin-page-framework-demo' ),
        'type'                  => 'taxonomy',
        'taxonomy_slugs'        => $aTaxnomies = get_taxonomies( '', 'names' ),
    ),
    array(
        'field_id'              => 'taxonomy_multiple_checklists',
        'title'                 => __( 'Multiple', 'admin-page-framework-demo' ),
        'type'                  => 'taxonomy',
        'taxonomy_slugs'        => $aTaxnomies,
        'before_field'          => '<p style="clear:both; font-weight: bold;">' . __( 'For I', 'admin-page-framework-demo' ) . '</p>',
        array(  
            'before_field' => '<p style="clear:both; font-weight: bold;">' . __( 'For II', 'admin-page-framework-demo' ) . '</p>',
        ),
        array(  
            'before_field' => '<p style="clear:both; font-weight: bold;">' . __( 'For III', 'admin-page-framework-demo' ) . '</p>',
        ),     
    ),
    array(
        'field_id'              => 'taxonomy_checklist_repeatable',
        'title'                 => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type'                  => 'taxonomy',
        'repeatable'            => true,
        'taxonomy_slugs'        => $aTaxnomies,
    ),
    array()
);

/*
 * MISC fields
 */
$this->addSettingFields(
    'color_picker', // the target section ID.
    array( // Color Picker
        'field_id' => 'color_picker_field',
        'title' => __( 'Color Picker', 'admin-page-framework-demo' ),
        'type' => 'color',
    ),     
    array( // Multiple Color Pickers
        'field_id' => 'multiple_color_picker_field',
        'title' => __( 'Multiple', 'admin-page-framework-demo' ),
        'type' => 'color',
        'label' => __( 'First', 'admin-page-framework-demo' ),
        'delimiter' => '<br />',
        array(
            'label' => __( 'Second', 'admin-page-framework-demo' ),
        ),
        array(
            'label' => __( 'Third', 'admin-page-framework-demo' ),
        ),     
    ),     
    array( // Repeatable Color Pickers
        'field_id' => 'color_picker_repeatable_field',
        'title' => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type' => 'color',
        'repeatable' =>    true,
    ),
    array( // Repeatable Color Pickers
        'field_id'  => 'color_picker_sortable',
        'title'     => __( 'Sortable', 'admin-page-framework-demo' ),
        'type'      => 'color',
        'sortable'  => true,
        array(),    // the second item
        array(),    // the third item
        
    )            
);
$this->addSettingFields(
    'hidden_field', // the target section ID.
    array( // Single Hidden Field
        'field_id'      => 'hidden_single',
        'title'         => __( 'Hidden Field', 'admin-page-framework-demo' ),
        'type'          => 'hidden',
        // 'hidden' =>    true // <-- the field row can be hidden with this option.
        'default'       => __( 'Test value', 'admin-page-framework-demo' ),
        'label'         => __( 'Test label', 'admin-page-framework-demo' ),
    ),
    array( // Single Hidden Field
        'field_id'      => 'hidden_repeatable',
        'title'         => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type'          => 'hidden',
        'value'         => 'HIIDENVALUE',
        'label'         => __( 'Repeat Me', 'admin-page-framework-demo' ),
        'repeatable'    =>    true,
    ),     
    array( // Multiple Hidden Fields
        'field_id'      => 'hidden_miltiple',
        'title'         => __( 'Multiple', 'admin-page-framework-demo' ),
        'type'          => 'hidden',
        'label'         => __( 'First', 'admin-page-framework-demo' ),
        'default'       => 'a',
        array(
            'label'     => __( 'Second', 'admin-page-framework-demo' ),
            'default'   => 'b',
        ),
        array(
            'label'     => __( 'Third', 'admin-page-framework-demo' ),
            'default'   => 'c',
        ),
        'sortable'      => true,
    )
);
$this->addSettingFields(
    'submit_buttons', // the target section ID.
    array( // Default Submit Button
        'field_id'          => 'submit_button_field',
        'title'             => __( 'Submit Button', 'admin-page-framework-demo' ),
        'type'              => 'submit',
        'description'       => __( 'This is the default submit button.', 'admin-page-framework-demo' ),
    ),     
    array( // Submit button as a link
        'field_id'          => 'submit_button_link',
        'type'              => 'submit',
        'title'             => __( 'Link Button', 'admin-page-framework-demo' ),
        'description'       => __( 'These buttons serve as a hyper link. Set the url to the <code>href</code> argument to enable this option.', 'admin-page-framework-demo' ),
        'label'             => __( 'Google', 'admin-page-framework-demo' ),
        'href'              => 'http://www.google.com',
        'attributes'        => array(
            'class'     => 'button button-secondary',     
            'title'     => __( 'Go to Google!', 'admin-page-framework-demo' ),
            'style'     => 'background-color: #C1DCFA;',
            'field'     => array(
                'style' => 'display: inline; clear: none;',
            ),
        ),
        array(
            'label'         => __( 'Yahoo', 'admin-page-framework-demo' ),
            'href'          => 'http://www.yahoo.com',
            'attributes'    => array(
                'class' => 'button button-secondary',     
                'title' => __( 'Go to Yahoo!', 'admin-page-framework-demo' ),
                'style' => 'background-color: #C8AEFF;',
            ),
        ),
        array(
            'label'         => __( 'Bing', 'admin-page-framework-demo' ),
            'href'          => 'http://www.bing.com',
            'attributes'    => array(
                'class' => 'button button-secondary',     
                'title' => __( 'Go to Bing!', 'admin-page-framework-demo' ),
                'style' => 'background-color: #FFE5AE;',
            ),     
        ),     
    ),     
    array( 
        'field_id'      => 'submit_button_download',
        'title'         => __( 'Download Button', 'admin-page-framework-demo' ),
        'type'          => 'submit',
        'label'         => __( 'Admin Page Framework', 'admin-page-framework-demo' ),
        'description'   => __( 'Download the latest version of the Admin Page Framework Demo plugin.', 'admin-page-framework-demo' ),
        'href'          => 'http://downloads.wordpress.org/plugin/admin-page-framework.latest-stable.zip',
    ),            
    array( // Submit button as a redirect
        'field_id'      => 'submit_button_redirect',
        'title'         => __( 'Redirect Button', 'admin-page-framework-demo' ),
        'type'          => 'submit',
        'description'   => sprintf( __( 'Unlike the above link buttons, this button saves the options and then redirects to: <code>%1$s</code>', 'admin-page-framework-demo' ), admin_url() )
            . ' ' . __( 'To enable this functionality, set the url to the <code>redirect_url</code> argument in the field definition array.', 'admin-page-framework-demo' ),
        'label'         => __( 'Dashboard', 'admin-page-framework-demo' ),
        'redirect_url'  => admin_url(),
        'attributes'    => array(
            'class' => 'button button-secondary',
        ),
    ),
    array( // Reset Submit button
        'field_id'      => 'submit_button_reset',
        'title'         => __( 'Reset Button', 'admin-page-framework-demo' ),
        'type'          => 'submit',
        'label'         => __( 'Reset', 'admin-page-framework-demo' ),
        'reset'         => true,
        'attributes'    => array(
            'class' => 'button button-secondary',
        ),
        'description'   => __( 'If you press this button, a confirmation message will appear and then if you press it again, it resets the option.', 'admin-page-framework-demo' ),
    ),
    array( // with an image
        'field_id'          => 'image_submit_button',
        'title'             => __( 'Image Submit Button', 'admin-page-framework-demo' ),
        'type'              => 'submit',
        'href'              => 'http://en.michaeluno.jp/donate',
        'attributes'        =>  array(
           'src'    => 'https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif',
           'alt'    => __( 'Submit', 'admin-page-framework-demo' ),
           'class'  => '',
        ),
        'description'   => __( 'For a custom image to the button, set the image url in the <code>src</code> attribute with the <code>attributes</code> argument.', 'admin-page-framework-demo' )
            . ' ' . __( 'This button will take you to the donation page for the developer of this framework. If you like to donate, please do so to help the development!', 'admin-page-framework-demo' ),
    ),    
    array()
);

/* 
 * Verification Example
 * */
$this->addSettingFields(     
    'verification', // the target sectin ID
    array(
        'field_id' => 'verify_text_field',
        'title' => __( 'Verify Text Input', 'admin-page-framework-demo' ),
        'type' => 'text',
        'description' => __( 'Enter a non numeric value here.', 'admin-page-framework-demo' ),
    ),
    array(
        'field_id' => 'verify_text_field_submit', // this submit field ID can be used in a validation callback method
        'type' => 'submit',     
        'label' => __( 'Verify', 'admin-page-framework-demo' ),
    )
);    
$this->addSettingFields(     
    'section_verification', // the target sectin ID
    array(
        'field_id' => 'item_a',
        'title' => __( 'Choose Item', 'admin-page-framework-demo' ),
        'type' => 'select',
        'label' => array(
            0       => '--- ' . __( 'Select Item', 'admin-page-framework-demo' ) . ' ---',
            'one'   => __( 'One', 'admin-page-framework-demo' ),
            'two'   => __( 'Two', 'admin-page-framework-demo' ),
            'three' => __( 'Three', 'admin-page-framework-demo' ),     
        ),
    ),
    array(
        'field_id' => 'item_b', // this submit field ID can be used in a validation callback method
        'type' => 'text',
        'description' => __( 'Select one above or enter text here.', 'admin-page-framework-demo' ),
    )
);     
$this->addSettingFields(     
    array(
        'field_id' => 'mixed_fields',
        'section_id' => 'mixed_types',
        'title' => __( 'Text and Hidden', 'admin-page-framework-demo' ),
        'type' => 'text',
        'default' => 'abc',
        array(
            'type' => 'hidden',
            'value' => 'xyz',
        ),
        'attributes' => array(
            'field' => array(
                'style' => 'display: inline; clear:none;' // since the rich editor does not accept the cols attribute, set the width by inline-style.
            ),
        ),     
        'description' => __( 'A hidden field is embedded. This is useful when you need to embed extra information to be sent with the visible elements.', 'admin-page-framework-demo' ),
    ),     
    array()
);    
$this->addSettingFields(
    'section_title_field_type', // the target section ID
    array(
        'field_id' => 'section_title_field',
        'type' => 'section_title',
        'label' => '<h3>' 
                . __( 'Section Name', 'admin-page-framework-demo' ) 
            . '</h3>',
        'attributes' => array(
            'size' => 30,
        ),
    )
);
$this->addSettingFields(    
    'repeatable_sections', // the target section ID
    array(
        'field_id' => 'text_field_in_repeatable_sections',
        'title' => __( 'Text', 'admin-page-framework-demo' ),
        'type' => 'text',
        'default' => 'xyz',
    ),
    array(
        'field_id' => 'repeatable_field_in_repeatable_sections',
        'title' => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type' => 'text',
        'repeatable' =>    true,
    ),     
    array(
        'field_id' => 'color_in_repeatable_sections',
        'title' => __( 'Color', 'admin-page-framework-demo' ),
        'type' => 'color',
    ),
    array(
        'field_id' => 'radio_in_repeatable_sections',
        'title' => __( 'Radio', 'admin-page-framework-demo' ),
        'type' => 'radio',
        'default' => 'b',
        'label' => array(
            'a' => 'A',
            'b' => 'B',
            'c' => 'c',     
        ),
    ),     
    array()
);     
$this->addSettingFields(    
    'tabbed_sections_a', // the target section ID
    array(
        'field_id' => 'text_field_in_tabbed_section',
        'title' => __( 'Text', 'admin-page-framework-demo' ),
        'type' => 'text',
        'default' => 'xyz',
    ),
    array(
        'field_id' => 'repeatable_field_in_tabbed_sections',
        'title' => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type' => 'text',
        'repeatable' =>    true,
    ),     
    'tabbed_sections_b', // the target section ID
    array(
        'field_id' => 'size_in_tabbed_sections',
        'title' => __( 'Size', 'admin-page-framework-demo' ),
        'type' => 'size',
    ),
    array(
        'field_id' => 'select_in_tabbed_sections',
        'title' => __( 'Select', 'admin-page-framework-demo' ),
        'type' => 'select',
        'default' => 'b',
        'label' => array(
            'a' => 'A',
            'b' => 'B',
            'c' => 'c',     
        ),
    ),     
    array()
);     
$this->addSettingFields(
    'repeatable_tabbed_sections', // the target section ID
     array(
        'field_id' => 'tab_title',
        'type' => 'section_title',
        'label' => __( 'Name', 'admin-page-framework-demo' ),
        'attributes' => array(
            'size' => 10,
            // 'type' => 'number', // change the input type 
        ),
    ),
    array(
        'field_id' => 'text_field_in_tabbed_section_in_repeatable_sections',
        'title' => __( 'Text', 'admin-page-framework-demo' ),
        'type' => 'text',
        'default' => 'xyz',
    ),
    array(
        'field_id' => 'repeatable_field_in_tabbed_sections_in_repetable_sections',
        'title' => __( 'Repeatable', 'admin-page-framework-demo' ),
        'type' => 'text',
        'repeatable' => true,
    ),     
    array(
        'field_id' => 'size_in_tabbed_sections_in_repeatable_sections',
        'title' => __( 'Size', 'admin-page-framework-demo' ),
        'type' => 'size',
    ),
    array(
        'field_id' => 'select_in_tabbed_sections_in_repeatable_sections',
        'title' => __( 'Select', 'admin-page-framework-demo' ),
        'type' => 'select',
        'default' => 'b',
        'label' => array(
            'a' => 'A',
            'b' => 'B',
            'c' => 'c',     
        ),
    ),     
    array(
        'field_id' => 'color_in_tabbed_sections_in_repeatable_sections',
        'title' => __( 'Color', 'admin-page-framework-demo' ),
        'type' => 'color',
        'repeatable' =>    true,
        'sortable' =>    true,
    ), 
    array(
        'field_id' => 'image_in_tabbed_sections_in_repeatable_sections',
        'title' => __( 'Image', 'admin-page-framework-demo' ),
        'type' => 'image',
        'repeatable' =>    true,
        'sortable' =>    true,
        'attributes' => array(
            'style' => 'max-width:300px;',
        ),
    ),     
    array(
        'field_id' => 'media_in_tabbed_sections_in_repeatable_sections',
        'title' => __( 'Media', 'admin-page-framework-demo' ),
        'type' => 'media',
        'repeatable' =>    true,
        'sortable' =>    true,
    ),     
    // array(
        // 'field_id'      => 'textarea_in_tabbed_sections_in_repeatable_sections',
        // 'title'         => __( 'Textarea', 'admin-page-framework-demo' ),
        // 'type'          => 'textarea',
        // 'rich'          => true,
        // 'repeatable'    => true,
        // 'sortable'   => true,
    // ),               
    array()
);     

/**
 * Fields to be defined with callback methods - pass only the required keys: 'field_id', 'section_id', and the 'type'.
 */
$this->addSettingFields(
    'callbacks', // the target section ID.
    array(
        'field_id' => 'callback_example',
        'type' => 'select',
    ),
    array(
        'field_id' => 'apf_post_titles',
        'type' => 'checkbox',
        'label_min_width' => '100%',
    ),     
    array()
);     