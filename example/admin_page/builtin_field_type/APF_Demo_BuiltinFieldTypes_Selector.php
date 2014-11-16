<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed GPLv2
 * 
 */

class APF_Demo_BuiltinFieldTypes_Selector {
 
    /**
     * Stores the caller class name, set in the constructor.
     */   
    public $sClassName = 'APF_Demo';
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'selectors';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'selectors';
    
    /**
     * Sets up hooks and properties.
     */
    public function __construct( $sClassName='', $sPageSlug='', $sTabSlug='' ) {
        
        $this->sClassName   = $sClassName ? $sClassName : $this->sClassName;
        $this->sPageSlug    = $sPageSlug ? $sPageSlug : $this->sPageSlug;
        $this->sTabSlug     = $sTabSlug ? $sTabSlug : $this->sTabSlug;
             
        // load_ + page slug
        add_action( 'load_' . $this->sPageSlug, array( $this, 'replyToAddTab' ) );
        
    }
    
    /**
     * Triggered when the page is loaded.
     */
    public function replyToAddTab( $oAdminPage ) {
        
        // Tab
        $oAdminPage->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'  => $this->sTabSlug,
                'title'     => __( 'Selectors', 'admin-page-framework-demo' ),
            )     
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToAddFormElements' ) );
        
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToAddFormElements( $oAdminPage ) {
        
        // Section
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Selectors and Checkboxes', 'admin-page-framework-demo' ),
                'description'   => __( 'These are selector type options such as dropdown lists, radio buttons, and checkboxes.', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id'        => 'sizes',
                // 'tab_slug'       => 'selectors', // <-- similar to the page slug, if the tab slug is the same as the previous one, it can be omitted.
                'title'             => __( 'Sizes', 'admin-page-framework-demo' ),
            )
        );        
        
        /*
         * Selector type fields - dropdown (pulldown) list, checkbox, radio buttons, size selector
         */
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section ID
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
                'default'       => array( 3, 4 ), // note that PHP array indices are zero-base, meaning the index count starts from 0 (not 1). 3 here means the fourth item of the array. array( 3, 4 ) will select the fourth and fifth elements.
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
                            'disabled' => 'disabled',
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
            )
        );
        
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section ID       
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
                        'disabled' => 'disabled',
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
                'default'       => 1,
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
                    'default'   => 5,
                    'label'     => array( 
                        3 => 'three',
                        4 => 'four',    
                        5 => 'five' 
                    ),
                ),
                array(
                    'default'   => 7,
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
                'default' => 0, // set the key of the label array
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
                'default'       => 2, // set the key of the label array
                'sortable'      =>    true,
                array(), // the second item
                array(), // the third item
                array(), // the fourth item
            )
        );
        
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section ID        
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
                        'disabled' => 'disabled',
                    ),
                ),
                'description'   => __( 'It is possible to disable checkbox items on an individual basis.', 'admin-page-framework-demo' ),
                'after_label'   => '<br />',
            ),
            array( // Multiple sets of checkbox fields
                'field_id'              => 'checkbox_multiple_fields',
                'title'                 => __( 'Multiple Sets', 'admin-page-framework-demo' ),
                'type'                  => 'checkbox',
                'select_all_button'     => true,        // 3.3.0+   to change the label, set the label here
                'select_none_button'    => true,        // 3.3.0+   to change the label, set the label here                
                'label'                 => array( 
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'C' 
                ),
                'default'               => array( 
                    'a' => false,
                    'b' => true,
                    'c' => false 
                ),
                'delimiter'             => '<hr />',
                'attributes'            => array(
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
                'select_all_button'     => __( 'Check All', 'admin-page-framework-demo' ),        // 3.3.0+   to change the label, set the label here
                'select_none_button'    => __( 'Uncheck All', 'admin=page-framework-demo' ),      // 3.3.0+   to change the label, set the label here                        
            ),
            array( // sortable check boxes
                'field_id'      => 'checkbox_sortable_fields',
                'title'         => __( 'Sortable', 'admin-page-framework-demo' ),
                'type'          => 'checkbox',
                'label'         => array( 'x', 'y', 'z' ),
                'sortable'      => true,
                array(), // the second item
                array(), // the third item
                array(), // the fourth item
            )
        );        
        $oAdminPage->addSettingFields(
            'sizes', // the target section ID
            array( // Size
                'field_id'      => 'size_field',
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
                            'disabled'  => 'disabled',
                            'class'     => 'disabled',
                        ),
                        'style' => 'background-color: #F7EFFF', // applies to all the option elements
                    ),
                    'optgroup'  => array(
                        'style' => 'background-color: #EFEFEF',
                        __( 'Astronomical Units', 'admin-page-framework' ) => array(
                            'disabled' => 'disabled',
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
    }
    
}