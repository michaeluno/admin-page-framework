<?php
class APF_NetworkAdmin_CustomFieldTypes extends AdminPageFramework_NetworkAdmin {

    /**
     * Triggered at the end of the constructor.
     * 
     * Alternatively you may use the start_{instantiated class name} predefined callback method.
     */
    public function start() {}

    public function setUp() { // this method automatically gets triggered with the wp_loaded hook. 

        /* ( optional ) this can be set via the constructor. For available values, see https://codex.wordpress.org/Roles_and_Capabilities */
        $this->setCapability( 'read' );
        
        /* ( required ) Set the root page */
        $this->setRootMenuPageBySlug( 'APF_NetworkAdmin' );    

        /* ( required ) Add sub-menu items (pages or links) */
        $this->addSubMenuItems(    
            array(
                'title' => __( 'Custom Field Types', 'admin-page-framework-demo' ),
                'page_slug' => 'apf_custom_field_types',
                'screen_icon' => 'options-general',
            )
        );     
                
        $this->addInPageTabs( // ( optional )
            /*
             * Page-heading tabs for custom field types
             */
            'apf_custom_field_types', // target page slug
            array(
                'tab_slug' => 'geometry',
                'title' => __( 'Geometry', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug' => 'date',
                'title' => __( 'Date & Time', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug' => 'dial',
                'title' => __( 'Dials', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug' => 'font',
                'title' => __( 'Fonts', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug' => 'sample',
                'title' => __( 'Sample', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug' => 'revealer',
                'title' => __( 'Revealer', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug' => 'grid',
                'title' => __( 'Grid', 'admin-page-framework-demo' ),    
            ),
            array()     
        );

        /* ( optional ) Determine the page style */
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs
            
                
    }
    
    /**
     * The pre-defined callback method that is triggered when the page loads.
     */     
    public function load_apf_custom_field_types( $oAdminPage ) {
    
        /*
         * ( Optional ) Register custom field types.
         */     
        /* 1. Include the file that defines the custom field type. */
        $aFiles = array(
            dirname( APFDEMO_FILE ) . '/third-party/geometry-custom-field-type/GeometryCustomFieldType.php',
            dirname( APFDEMO_FILE ) . '/third-party/date-time-custom-field-types/DateCustomFieldType.php',
            dirname( APFDEMO_FILE ) . '/third-party/date-time-custom-field-types/TimeCustomFieldType.php',
            dirname( APFDEMO_FILE ) . '/third-party/date-time-custom-field-types/DateTimeCustomFieldType.php',
            dirname( APFDEMO_FILE ) . '/third-party/dial-custom-field-type/DialCustomFieldType.php',
            dirname( APFDEMO_FILE ) . '/third-party/font-custom-field-type/FontCustomFieldType.php',
            dirname( APFDEMO_FILE ) . '/third-party/sample-custom-field-type/SampleCustomFieldType.php',
            dirname( APFDEMO_FILE ) . '/third-party/revealer-custom-field-type/RevealerCustomFieldType.php',
            dirname( APFDEMO_FILE ) . '/third-party/grid-custom-field-type/GridCustomFieldType.php',
            dirname( APFDEMO_FILE ) . '/third-party/autocomplete-custom-field-type/AutocompleteCustomFieldType.php',     
        );
        foreach( $aFiles as $sFilePath ) {
            if ( file_exists( $sFilePath ) ) { 
                include( $sFilePath ); 
            }
        }
                    
        /* 2. Instantiate the classes  */
        $sClassName = get_class( $this );
        new GeometryCustomFieldType( $sClassName );
        new DateCustomFieldType( $sClassName );
        new TimeCustomFieldType( $sClassName );
        new DateTimeCustomFieldType( $sClassName );
        new DialCustomFieldType( $sClassName );
        new FontCustomFieldType( $sClassName );
        new SampleCustomFieldType( $sClassName );
        new RevealerCustomFieldType( $sClassName );
        new GridCustomFieldType( $sClassName );
        new AutocompleteCustomFieldType( $sClassName );
    
        $this->addSettingSections(    
            array(
                'section_id' => 'geometry',
                'page_slug' => 'apf_custom_field_types', // renew the target page slug
                'tab_slug' => 'geometry',    
                'title' => __( 'Geometry Custom Field Type', 'admin-page-framework-demo' ),
                'description' => __( 'This is a custom field type defined externally.', 'admin-page-framework-demo' ),
            ),    
            array(
                'section_id' => 'date_pickers',
                'tab_slug' => 'date',
                'title' => __( 'Date Custom Field Type', 'admin-page-framework' ),
                'description' => __( 'These are date and time pickers.', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id' => 'dial',
                'tab_slug' => 'dial',
                'title' => __( 'Dial Custom Field Type', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id' => 'font',
                'tab_slug' => 'font',
                'title' => __( 'Font Custom Field Type', 'admin-page-framework-demo' ),
                'description' => __( 'This is still experimental.', 'admin-page-framework-demo' ),     
            ),
            array(
                'section_id' => 'sample',
                'tab_slug' => 'sample',
                'title' => __( 'Sample Custom Field Type', 'admin-page-framework-demo' ),
                'description' => __( 'This is just an example of creating a custom field type with Admin Page Framework.', 'admin-page-framework-demo' ),     
            ),     
            array(
                'section_id' => 'revealer',
                'tab_slug' => 'revealer',
                'title' => __( 'Revealer Custom Field Type', 'admin-page-framework-demo' ),
                'description' => __( 'When the user selects an item from the selector, it reveals one of the predefined fields.', 'admin-page-framework-demo' ),     
            ),    
            array(
                'section_id' => 'grid',
                'tab_slug' => 'grid',
                'title' => __( 'Grid Custom Field Type', 'admin-page-framework-demo' ),
                'description' => __( 'This field will save the grid positions of the widgets.', 'admin-page-framework-demo' ),     
            ),     
            array()
        );
                
        /*
         * Custom Field Types - in order to use these types, those custom field types must be registered. 
         * The way to register a field type is demonstrated in the start_{instantiated class name} callback function.
         */
        $this->addSettingFields(     
            array(
                'field_id' => 'geometrical_coordinates',
                'section_id' => 'geometry',
                'title' => __( 'Geometrical Coordinates', 'admin-page-framework-demo' ),
                'type' => 'geometry',
                'description' => __( 'Get the coordinates from the map.', 'admin-page-framework-demo' ),
                'default' => array(
                    'latitude' => 20,
                    'longitude' => 20,
                ),
            )
        );
        $this->addSettingFields(
            array( // Single date picker
                'field_id' => 'date',
                'section_id' => 'date_pickers',
                'title' => __( 'Date', 'admin-page-framework-demo' ),
                'type' => 'date',
            ),     
            array( // Multiple date pickers
                'field_id' => 'dates',
                'title' => __( 'Dates', 'admin-page-framework-demo' ),
                'type' => 'date',
                'label' => __( 'Start Date: ', 'amin-page-framework-demo' ),
                'date_format' => 'yy-mm-dd', // yy/mm/dd is the default format.
                'delimiter' => '&nbsp;&nbsp;&nbsp;&nbsp;',
                array( 
                    'label' => __( 'End Date: ', 'amin-page-framework-demo' ), 
                ),
            ),    
            array( // Repeatable date picker fields
                'field_id' => 'date_repeatable',
                'type' => 'date',
                'title' => __( 'Repeatable', 'admin-page-framework-demo' ),
                'repeatable' => true,
            ),     
            array( // Sortable date picker fields
                'field_id' => 'date_sortable',
                'type' => 'date',
                'title' => __( 'Sortable', 'admin-page-framework-demo' ),
                'sortable' => true,
                array(), // the second item
                array(), // the third item
            ),     
            array( // Single time picker
                'field_id' => 'time',
                'type' => 'time',
                'title' => __( 'Time', 'admin-page-framework-demo' ),
                'time_format' => 'H:mm', // H:mm is the default format.
            ),
            array( // Repeatable time picker fields
                'field_id' => 'time_repeatable',
                'type' => 'time',
                'title' => __( 'Repeatable Time Fields', 'admin-page-framework-demo' ),
                'repeatable' => true,
            ),
            array( // Sortable tune picker fields
                'field_id' => 'time_sortable',
                'type' => 'time',
                'title' => __( 'Sortable', 'admin-page-framework-demo' ),
                'sortable' => true,
                array(), // the second item
                array(), // the third item
            ),     
            array( // Single date time picker
                'field_id' => 'date_time',
                'type' => 'date_time',
                'title' => __( 'Date & Time', 'admin-page-framework-demo' ),
                'date_format' => 'yy-mm-dd', // yy/mm/dd is the default format.
                'time_format' => 'H:mm', // H:mm is the default format.
            ),     
            array( // Multiple date time pickers
                'field_id' => 'dates_time_multiple',
                'type' => 'date_time',
                'title' => __( 'Multiple Date and Time', 'admin-page-framework-demo' ),
                'description' => __( 'With different time formats', 'admin-page-framework-demo' ),
                'label' => __( 'Default', 'amin-page-framework-demo' ), 
                'time_format' => 'H:mm',
                'date_format' => 'yy-mm-dd', // yy/mm/dd is the default format.
                'delimiter' => '<br />',     
                array(
                    'label' => __( 'AM PM', 'amin-page-framework-demo' ), 
                    'time_format' => 'hh:mm tt',
                ),
                array(
                    'label' => __( 'Time Zone', 'amin-page-framework-demo' ), 
                    'time_format' => 'hh:mm tt z',
                ),    
            ),
            array( // Single date time picker
                'field_id' => 'date_time_repeatable',
                'type' => 'date_time',
                'title' => __( 'Repeatable Date & Time Fields', 'admin-page-framework-demo' ),
                'repeatable' => true,
            ),    
            array( // Sortable date_time picker fields
                'field_id' => 'date_time_sortable',
                'type' => 'date_time',
                'title' => __( 'Sortable', 'admin-page-framework-demo' ),
                'sortable' => true,
                array(), // the second item
                array(), // the third item
            ),
            array()
        );
        $this->addSettingFields(     
            array(
                'field_id' => 'dials',
                'section_id' => 'dial',
                'title' => __( 'Multiple Dials', 'admin-page-framework-demo' ),
                'type' => 'dial',
                'label' => __( 'Default', 'admin-page-framework-demo' ),
                'attributes' => array(    
                    'field' => array(
                        'style' => 'display: inline; clear: none', // this makes the field element inline, which means next fields continues from the right end of the field, not from the new line.
                    ),
                ),
                array(     
                    'label' => __( 'Disable display input', 'admin-page-framework-demo' ),
                    'attributes' => array(
                        // For details, see https://github.com/aterrien/jQuery-Knob
                        'data-width' => 100,
                        'data-displayInput' => 'false',
                    ),
                ),     
                array(     
                    'label' => __( 'Cursor mode', 'admin-page-framework-demo' ),
                    'attributes' => array(
                        'data-width' => 150,
                        'data-cursor' => 'true',
                        'data-thickness' => '.3', 
                        'data-fgColor' => '#222222',     
                    ),
                ),
                array(
                    'label' => __( 'Display previous value (effect)', 'admin-page-framework-demo' ),
                    'attributes' => array(
                        'data-width' => 200,
                        'data-min' => -100, 
                        'data-displayPrevious' => 'true', // a boolean value also needs to be passed as string
                    ),     
                ),
                array(
                    'label' => __( 'Angle offset', 'admin-page-framework-demo' ),     
                    'attributes' => array(
                        'data-angleOffset' => 90,
                        'data-linecap' => 'round',
                    ),     
                ),
                array(
                    'label' => __( 'Angle offset and arc', 'admin-page-framework-demo' ),
                    'attributes' => array(
                        'data-fgColor' => '#66CC66',
                        'data-angleOffset' => -125,
                        'data-angleArc' => 250,
                    ),     
                ),
                array(
                    'label' => __( '5-digit values, step 1000', 'admin-page-framework-demo' ),
                    'attributes' => array(
                        'data-step' => 1000,
                        'data-min' => -15000,
                        'data-max' => 15000,
                        'data-displayPrevious' =>    true,
                    ),     
                ),

            ),
            array(
                'field_id' => 'dial_big',
                'title' => __( 'Big', 'admin-page-framework-demo' ),
                'type' => 'dial',
                'attributes' => array(
                    'data-width' => 400,
                    'data-height' => 400,
                ),
            ),
            array(
                'field_id' => 'dial_repeatable',
                'title' => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type' => 'dial',
                'repeatable' =>    true,
            ),
            array(
                'field_id' => 'dial_sortable',
                'title' => __( 'Sortable', 'admin-page-framework-demo' ),
                'type' => 'dial',
                'sortable' =>    true,
                'attributes' => array(    
                    'field' => array(
                        'style' => 'display: inline; clear: none', // this makes the field element inline, which means next fields continues from the right end of the field, not from the new line.
                    ),
                    'data-width' => 100,
                    'data-height' =>     100,
                ),     
                array(), // the second item
                array(), // the third item
                array(), // the fourth item
            )     
        );
        $this->addSettingFields(     
            array(
                'field_id' => 'font_field',
                'section_id' => 'font',
                'title' => __( 'Font Upload', 'admin-page-framework-demo' ),
                'type' => 'font',
                'description' => __( 'Set the URL of the font.', 'admin-page-framework-demo' ),
            ),
            array(
                'field_id' => 'font_field_repeatable',
                'title' => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type' => 'font',
                'repeatable' =>  true,
            ),     
            array(
                'field_id' => 'font_field_sortable',
                'title' => __( 'Sortable', 'admin-page-framework-demo' ),
                'type' => 'font',
                'sortable' =>  true,
                array(), // second
                array(), // third
            ),     
            array()
        );
        $this->addSettingFields(
            array(
                'field_id' => 'sample_field',
                'section_id' => 'sample',
                'type' => 'sample',
                'title' => __( 'Sample', 'admin-page-framework-demo' ),
                'description' => __( 'This sample custom field demonstrates how to display a certain element after selecting a radio button.', 'admin-page-framework-demo' ),
                // 'default' => 'red',
                'label' => array(
                    'red' => __( 'Red', 'admin-page-framework-demo' ),
                    'blue' => __( 'Blue', 'admin-page-framework-demo' ),
                    'green' => __( 'Green', 'admin-page-framework-demo' ),
                ),
                'reveal' => array( // the field type specific key. This is defined in the
                    'red' => '<p style="color:red;">' . __( 'You selected red!', 'admin-page-framework-demo' ) . '</p>',
                    'blue' => '<p style="color:blue;">' . __( 'You selected blue!', 'admin-page-framework-demo' ) . '</p>',
                    'green' => '<p style="color:green;">' . __( 'You selected green!', 'admin-page-framework-demo' ) . '</p>',
                ),
            ),
            array(
                'field_id' => 'sample_field_repeatable',
                'type' => 'sample',
                'title' => __( 'Sample', 'admin-page-framework-demo' ),
                // 'default' => 'red',
                'label' => array(
                    'red' => __( 'Red', 'admin-page-framework-demo' ),
                    'blue' => __( 'Blue', 'admin-page-framework-demo' ),
                    'green' => __( 'Green', 'admin-page-framework-demo' ),
                ),
                'reveal' => array( // the field type specific key. This is defined in the
                    'red' => '<p style="color:red;">' . __( 'You selected red!', 'admin-page-framework-demo' ) . '</p>',
                    'blue' => '<p style="color:blue;">' . __( 'You selected blue!', 'admin-page-framework-demo' ) . '</p>',
                    'green' => '<p style="color:green;">' . __( 'You selected green!', 'admin-page-framework-demo' ) . '</p>',
                ),
                'repeatable' => true,
            )    
        );
        $this->addSettingFields(
            array(
                'field_id' => 'revealer_field_by_id',
                'section_id' => 'revealer',
                'type' => 'revealer',     
                'title' => __( 'Reveal Hidden Fields' ),
                'value' => 'undefined', // always set the 'Select a Field' label.
                'label' => array( // the keys represent the selector to reveal, in this case, their tag id : #fieldrow-{field id}
                    'undefined' => __( '-- Select a Field --', 'admin-page-framework-demo' ),     
                    '#fieldrow-revealer_revealer_field_option_a' => __( 'Option A', 'admin-page-framework-demo' ),     
                    '#fieldrow-revealer_revealer_field_option_b, #fieldrow-revealer_revealer_field_option_c' => __( 'Option B and C', 'admin-page-framework-demo' ),
                ),
                'description' => __( 'Specify the selectors to reveal in the label argument keys in the field definition array.', 'admin-page-framework-demo' ),
            ),
            array(
                'field_id' => 'revealer_field_option_a',
                'section_id' => 'revealer',
                'type' => 'textarea',     
                'default' => __( 'Hi there!', 'admin-page-framework-demo' ),
                'hidden' => true,
            ),
            array(
                'field_id' => 'revealer_field_option_b',     
                'section_id' => 'revealer',
                'type' => 'password',     
                'description' => __( 'Type a password.', 'admin-page-framework-demo' ),     
                'hidden' => true,
            ),
            array(
                'field_id' => 'revealer_field_option_c',
                'section_id' => 'revealer',
                'type' => 'text',     
                'description' => __( 'Type text.', 'admin-page-framework-demo' ),     
                'hidden' => true,
            )
        );
        $this->addSettingFields(
            array(
                'field_id' => 'grid_field',     
                'section_id' => 'grid',
                'type' => 'grid',     
                'description' => __( 'Move the widgets.', 'admin-page-framework-demo' ),    
                'show_title_column' => false, // this removes the title column of the field output
                'grid_options' => array(
                    'resize' => array(
                        'enabled' => false,
                    ),
                ),
                'default' => array( // '[{"id":"","col":1,"row":1,"size_y":1,"size_x":1},{"id":"","col":1,"row":2,"size_y":1,"size_x":1}]',
                    array( 
                        'col' => 1,
                        'row' => 1,
                        'size_y' => 1,
                        'size_x' => 1,
                    ),
                    array(
                        'col' => 2,
                        'row' => 2,
                        'size_y' => 1,
                        'size_x' => 1,     
                    ),
                ),
            ),
            array(
                'field_id' => 'grid_field2',     
                'description' => __( 'Widgets can be expanded.', 'admin-page-framework-demo' ),    
                'type' => 'grid',     
                'grid_options' => array(
                    'resize' => array(
                        'enabled' =>    true,
                    ),
                ),
                'show_title_column' => false,    
                'default' => array(    
                    array( 
                        'col' => 1,
                        'row' => 1,
                        'size_y' => 2,
                        'size_x' => 1,
                    ),
                    array(
                        'col' => 2,
                        'row' => 1,
                        'size_y' => 1,
                        'size_x' => 2,     
                    ),
                    array(
                        'col' => 4,
                        'row' => 1,
                        'size_y' => 1,
                        'size_x' => 2,     
                    ),     
                ),
            ),    
            array(
                'field_id' => 'grid_field3',
                'type' => 'grid',     
                'description' => __( 'The base size can be different.', 'admin-page-framework-demo' ),    
                'grid_options' => array(
                    'resize' => array(
                        'enabled' =>    true,
                    ),
                    'widget_margins' => array( 10, 10 ),
                    'widget_base_dimensions' => array( 100, 100 ),     
                ),
                'show_title_column' => false,    
                'default' => array(    
                    array( 
                        'col' => 1,
                        'row' => 1,
                        'size_y' => 1,
                        'size_x' => 1,
                    ),     
                ),
            ),     
            array()
        );
        
    }
    
    /*
     * Custom Field Types Page
     * */
    public function do_apf_custom_field_types() { // do_{page slug}
        submit_button();
    }

    
}