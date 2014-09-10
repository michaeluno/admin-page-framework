<?php
class APF_Demo_CustomFieldTypes extends AdminPageFramework {

    /**
     * The start() method is called at the end of the constructor. [3.1.0+]
     * 
     * Alternatively you may use the 'start_{instantiated class name}()' method instead, which also called at the end of the constructor.
     * 
     */
    public function start() {
        
        /*
         * ( Optional ) Register custom field types.
         */     
        /* 1. Include the file that defines the custom field type. */
        $_sPluginDirName = dirname( APFDEMO_FILE );
        $_aFiles = array(
            $_sPluginDirName . '/third-party/date-time-custom-field-types/DateCustomFieldType.php',
            $_sPluginDirName . '/third-party/date-time-custom-field-types/TimeCustomFieldType.php',
            $_sPluginDirName . '/third-party/date-time-custom-field-types/DateTimeCustomFieldType.php',
            $_sPluginDirName . '/third-party/date-time-custom-field-types/DateRangeCustomFieldType.php',
            $_sPluginDirName . '/third-party/date-time-custom-field-types/DateTimeRangeCustomFieldType.php',
            $_sPluginDirName . '/third-party/date-time-custom-field-types/TimeRangeCustomFieldType.php',
            $_sPluginDirName . '/third-party/dial-custom-field-type/DialCustomFieldType.php',
            $_sPluginDirName . '/third-party/font-custom-field-type/FontCustomFieldType.php',
            $_sPluginDirName . '/third-party/sample-custom-field-type/SampleCustomFieldType.php',
            $_sPluginDirName . '/third-party/revealer-custom-field-type/RevealerCustomFieldType.php',
            $_sPluginDirName . '/third-party/grid-custom-field-type/GridCustomFieldType.php',
            $_sPluginDirName . '/third-party/autocomplete-custom-field-type/AutocompleteCustomFieldType.php',     
            $_sPluginDirName . '/third-party/link-custom-field-type/LinkCustomFieldType.php',     
            $_sPluginDirName . '/third-party/system-custom-field-type/SystemCustomFieldType.php',
            $_sPluginDirName . '/third-party/github-custom-field-type/GitHubCustomFieldType.php',
        );
        foreach( $_aFiles as $_sFilePath ) {
            if ( file_exists( $_sFilePath ) ) {     
                include( $_sFilePath );
            }
        }
                    
        /* 2. Instantiate the classes by passing the instantiated admin page class name. */
        $_sClassName = get_class( $this );
        new DateCustomFieldType( $_sClassName );
        new TimeCustomFieldType( $_sClassName );
        new DateTimeCustomFieldType( $_sClassName );
        new DateRangeCustomFieldType( $_sClassName );
        new DateTimeRangeCustomFieldType( $_sClassName );
        new TimeRangeCustomFieldType( $_sClassName );
        new DialCustomFieldType( $_sClassName );
        new FontCustomFieldType( $_sClassName );
        new SampleCustomFieldType( $_sClassName );
        new RevealerCustomFieldType( $_sClassName );
        new GridCustomFieldType( $_sClassName );
        new AutocompleteCustomFieldType( $_sClassName );     
        new LinkCustomFieldType( $_sClassName );     
        new SystemCustomFieldType( $_sClassName );     
        new GitHubCustomFieldType( $_sClassName );     
        
    }    

    /*
     * ( Required ) In the setUp() method, you will define pages.
     */
    public function setUp() { // this method automatically gets triggered with the wp_loaded hook. 

        /* ( optional ) this can be set via the constructor. For available values, see https://codex.wordpress.org/Roles_and_Capabilities */
        $this->setCapability( 'read' );
        
        /* ( required ) Set the root page */
        $this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );    
        
        /* ( required ) Add sub-menu items (pages or links) */
        $this->addSubMenuItems(    
            array(
                'title' => __( 'Custom Field Types', 'admin-page-framework-demo' ),
                'page_slug' => 'apf_custom_field_types',
                'screen_icon' => 'options-general',
            )
        );
        
        /* ( optional ) Disable the automatic settings link in the plugin listing table. */    
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string.     
            
    }
        
    /**
     * The pre-defined callback method triggered when one of the added pages loads
     */
    public function load_APF_Demo_CustomFieldTypes( $oAdminPage ) { // load_{instantiated class name}
        
        /* ( optional ) Determine the page style */
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs
                        
        /* ( optional ) Determine the page style */
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs
        
    }
        
    /**
     * The pre-defined callback method that is triggered when the page loads.
     */ 
    public function load_apf_custom_field_types( $oAdminPage ) { // load_{page slug}
        
        /*
         * ( optional ) Add in-page tabs - In Admin Page Framework, there are two kinds of tabs: page-heading tabs and in-page tabs.
         * Page-heading tabs show the titles of sub-page items which belong to the set root page. 
         * In-page tabs show tabs that you define to be embedded within an individual page.
         */
        $this->addInPageTabs(    
            'apf_custom_field_types', // target page slug
            array(
                'tab_slug'  => 'geometry',
                'title'     => __( 'Geometry', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug'  => 'date',
                'title'     => __( 'Date & Time', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug'  => 'dial',
                'title'     => __( 'Dials', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug'  => 'font',
                'title'     => __( 'Fonts', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug'  => 'sample',
                'title'     => __( 'Sample', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug'  => 'revealer',
                'title'     => __( 'Revealer', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug'  => 'grid',
                'title'     => __( 'Grid', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug'  => 'autocomplete',
                'title'     => __( 'Autocomplete', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug'  => 'link',
                'title'     => __( 'Links', 'admin-page-framework-demo' ),    
            ),     
            array(
                'tab_slug'  => 'system',
                'title'     => __( 'System', 'admin-page-framework-demo' ),    
            ),                 
            array(
                'tab_slug'  => 'github',
                'title'     => __( 'GitHub', 'admin-page-framework-demo' ),    
            ),                             
            array()     
        );    
                
        /*
         * ( optional ) Create a form - To create a form in Admin Page Framework, you need two kinds of components: sections and fields.
         * A section groups fields and fields belong to a section. So a section needs to be created prior to fields.
         * Use the addSettingSections() method to create sections and use the addSettingFields() method to create fields.
         */
        $this->addSettingSections(    
            'apf_custom_field_types', // the target page slug
            array(
                'section_id'    => 'geometry',
                'tab_slug'      => 'geometry',    
                'title'         => __( 'Geometry Custom Field Type', 'admin-page-framework-demo' ),
                'description'   => __( 'This is a custom field type defined externally.', 'admin-page-framework-demo' ),
            ),    
            array(
                'section_id'    => 'date_pickers',
                'tab_slug'      => 'date',
                'title'         => __( 'Date Custom Field Type', 'admin-page-framework' ),
                'description'   => __( 'We have date and time pickers.', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id'    => 'dial',
                'tab_slug'      => 'dial',
                'title'         => __( 'Dial Custom Field Type', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id'    => 'font',
                'tab_slug'      => 'font',
                'title'         => __( 'Font Custom Field Type', 'admin-page-framework-demo' ),
                'description'   => __( 'This is still experimental.', 'admin-page-framework-demo' ),     
            ),
            array(
                'section_id'    => 'sample',
                'tab_slug'      => 'sample',
                'title'         => __( 'Sample Custom Field Type', 'admin-page-framework-demo' ),
                'description'   => __( 'This is just an example of creating a custom field type with Admin Page Framework.', 'admin-page-framework-demo' ),     
            ),     
            array(
                'section_id'    => 'revealer',
                'tab_slug'      => 'revealer',
                'title'         => __( 'Revealer Custom Field Type', 'admin-page-framework-demo' ),
                'description'   => __( 'When the user selects an item from the selector, it reveals one of the predefined fields.', 'admin-page-framework-demo' ),     
            ),    
            array(
                'section_id'    => 'grid',
                'tab_slug'      => 'grid',
                'title'         => __( 'Grid Custom Field Type', 'admin-page-framework-demo' ),
                'description'   => __( 'This field will save the grid positions of the widgets.', 'admin-page-framework-demo' ),     
            ),
            array(
                'section_id'    => 'autocomplete',
                'tab_slug'      => 'autocomplete',
                'title'         => __( 'Autocomplete Custom Field Type', 'admin-page-framework-demo' ),
                'description'   => __( 'This field will show predefined list when the user type something on the input field.', 'admin-page-framework-demo' ),     
            ),
            array(
                'section_id'    => 'link',
                'tab_slug'      => 'link',
                'title'         => __( 'Link Custom Field Type', 'admin-page-framework-demo' ),
                'description'   => __( 'Allows to insert page and post links.', 'admin-page-framework-demo' ),     
            ),
            array(
                'section_id'    => 'system',
                'tab_slug'      => 'system',
                'title'         => __( 'System Custom Field Type', 'admin-page-framework-demo' ),
                'description'   => __( 'Displays the system information.', 'admin-page-framework-demo' ),     
            ),            
            array(
                'section_id'    => 'github',
                'tab_slug'      => 'github',
                'title'         => __( 'GitHub Buttons', 'admin-page-framework-demo' ),
            ),                 
            array()
        );

        /* Add setting fields */     
        /*
         * Custom Field Types - in order to use these types, those custom field types must be registered. 
         * The way to register a field type is demonstrated in the start_{extended class name} callback function.
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
            // To use advanced options, pass the options in the 'options' argument.
            // The argument keys are the same as the ones documented here : http://trentrichardson.com/examples/timepicker/#rest_examples     
            'date_pickers', // the target section ID.
            array( // Single date picker
                'field_id' => 'date',
                'title' => __( 'Date', 'admin-page-framework-demo' ),
                'type' => 'date',
            ),     
            array( // Repeatable date picker fields
                'field_id' => 'date_repeatable',
                'type' => 'date',
                'title' => __( 'Repeatable', 'admin-page-framework-demo' ),
                'repeatable' =>    true,
                'date_format' => 'yy-mm-dd', // yy/mm/dd is the default format.
                'options' => array(
                    'numberOfMonths' => 2,
                ),
                'description' => __( 'Notice that the multiple panels are shown.', 'admin-page-framework-demo' ), 
            ),     
            array( // Sortable date picker fields
                'field_id' => 'date_sortable',
                'type' => 'date',
                'title' => __( 'Sortable', 'admin-page-framework-demo' ),
                'sortable' => true,
                'options' => '{
                    minDate: new Date(2010, 11, 20, 8, 30),
                    maxDate: new Date(2010, 11, 31, 17, 30)
                }',     
                'description' => __( 'The option can be passed as a string.', 'admin-page-framework-demo' ),
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
                'options' => array(     
                    'hourGrid' => 4,
                    'minuteGrid' => 10,
                    'timeFormat' => 'hh:mm tt',
                ),
                'description' => __( 'The grid option is set.', 'admin-page-framework-demo' ), 
            ),
            array( // Sortable tune picker fields
                'field_id' => 'time_sortable',
                'type' => 'time',
                'title' => __( 'Sortable', 'admin-page-framework-demo' ),
                'sortable' => true,
                'options' => array(
                    'hourMin' => 8,
                    'hourMax' => 16,
                ),
                'description' => __( 'The maximum and minimum hours are set.', 'admin-page-framework-demo' ), 
                array(), // the second item
                array(), // the third item
            ),     
            array( // Single date-time picker
                'field_id' => 'date_time',
                'type' => 'date_time',
                'title' => __( 'Date & Time', 'admin-page-framework-demo' ),
                'date_format' => 'yy-mm-dd', // yy/mm/dd is the default format.
                'time_format' => 'H:mm', // H:mm is the default format.
            ),     
            array( // Multiple date-time pickers
                'field_id' => 'dates_time_multiple',
                'type' => 'date_time',
                'title' => __( 'Multiple Date and Time', 'admin-page-framework-demo' ),
                'description' => __( 'With different time formats', 'admin-page-framework-demo' ),
                'label' => __( 'Default', 'admin-page-framework-demo' ), 
                'time_format' => 'H:mm',
                'date_format' => 'yy-mm-dd', // yy/mm/dd is the default format.
                'delimiter' => '<br />',     
                'attributes' => array(
                    'size' => 24,
                ),     
                array(
                    'label' => __( 'AM PM', 'admin-page-framework-demo' ), 
                    'time_format' => 'hh:mm tt',
                ),
                array(
                    'label' => __( 'Time Zone', 'admin-page-framework-demo' ), 
                    'time_format' => 'hh:mm tt z',
                ),
                array(
                    'label' => __( 'Number Of Months', 'admin-page-framework-demo' ), 
                    'options' => array(
                        'numberOfMonths' => 3,
                    ),
                ),     
                array(
                    'label' => __( 'Min & Max Dates', 'admin-page-framework-demo' ), 
                    'options' => array(
                        'numberOfMonths' => 2,
                        'minDate' => 0,
                        'maxDate' => 30,
                    ),
                ),     
            ),
            array( // Single date time picker
                'field_id' => 'date_time_repeatable',
                'type' => 'date_time',
                'title' => __( 'Repeatable Date & Time Fields', 'admin-page-framework-demo' ),
                'repeatable' => true,
                'options' => array(
                    'timeFormat' => 'HH:mm:ss',
                    'stepHour' => 2,
                    'stepMinute' => 10,
                    'stepSecond' => 10,
                ),
            ),    
            array( // Sortable date_time picker fields
                'field_id' => 'date_time_sortable',
                'type' => 'date_time',
                'title' => __( 'Sortable', 'admin-page-framework-demo' ),
                'sortable' => true,
                'attributes' => array(
                    'size' => 30,
                ),
                'options' => array(     
                    'timeFormat' => 'HH:mm z',
                    'timezoneList' => array(
                        array(
                            'value' => -300,
                            'label' => __( 'Eastern', 'admin-page-framework-demo' ),
                        ),
                        array(
                            'value' => -360,
                            'label' => __( 'Central', 'admin-page-framework-demo' ),
                        ),     
                        array(
                            'value' => -420,
                            'label' => __( 'Mountain', 'admin-page-framework-demo' ),
                        ),     
                        array(
                            'value' => -480,
                            'label' => __( 'Pacific', 'admin-page-framework-demo' ),
                        ),     
                    ),
                ),
                array(), // the second item
                array(), // the third item
            ),
            array( // Single date_range picker
                'field_id' => 'date_range',
                'title' => __( 'Date Range', 'admin-page-framework-demo' ),
                'type' => 'date_range',
            ),     
            array( // Single date_range picker
                'field_id' => 'date_range_repeatable',
                'title' => __( 'Repeatable Date Range', 'admin-page-framework-demo' ),
                'type' => 'date_range',
                'repeatable' =>    true,
                'sortable' =>    true,
                'options' => array(
                    'numberOfMonths' => 2,
                ),
            ),    
            array( // Single date_time_range picker
                'field_id' => 'date_time_range',
                'title' => __( 'Date Time Range', 'admin-page-framework-demo' ),
                'type' => 'date_time_range',
            ),     
            array( // Single date_time_range picker
                'field_id' => 'date_time_range_repeatable',
                'title' => __( 'Repeatable Date Time Range', 'admin-page-framework-demo' ),
                'type' => 'date_time_range',
                'time_format' => 'HH:mm:ss',
                'repeatable' =>    true,
                'sortable' =>    true,
                'options' => array(
                    'numberOfMonths' => 2,
                ),
            ),    
            array( // Single date_time_range picker
                'field_id' => 'time_range',
                'title' => __( 'Time Range', 'admin-page-framework-demo' ),
                'type' => 'time_range',
            ),    
            array( // Single date_time_range picker
                'field_id' => 'time_range_repeatable',
                'title' => __( 'Repeatable Time Range', 'admin-page-framework-demo' ),
                'type' => 'time_range',
                'time_format' => 'HH:mm:ss',
                'repeatable' =>    true,
                'sortable' =>    true,     
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
            'revealer', // the target section id
            array(
                'field_id' => 'revealer_field_by_id',
                'type' => 'revealer',     
                'title' => __( 'Reveal Hidden Fields' ),
                'value' => 'undefined', // always set the 'Select a Field' label.
                'label' => array( // the keys represent the selector to reveal, in this case, their tag id : #fieldrow-{field id}
                    'undefined' => __( '-- Select a Field --', 'admin-page-framework-demo' ),     
                    '#fieldrow-revealer_revealer_field_option_a' => __( 'Option A', 'admin-page-framework-demo' ),     
                    '#fieldrow-revealer_revealer_field_option_b, #fieldrow-revealer_revealer_field_option_c' => __( 'Option B and C', 'admin-page-framework-demo' ),
                ),
                'description' => __( 'Specify the selectors to reveal in the label keys in the field definition array.', 'admin-page-framework-demo' ),
            ),
            array(
                'field_id' => 'revealer_field_option_a',
                'type' => 'textarea',     
                'default' => __( 'Hi there!', 'admin-page-framework-demo' ),
                'hidden' => true,
            ),
            array(
                'field_id' => 'revealer_field_option_b',     
                'type' => 'password',     
                'description' => __( 'Type a password.', 'admin-page-framework-demo' ),     
                'hidden' => true,
            ),
            array(
                'field_id' => 'revealer_field_option_c',
                'type' => 'text',     
                'description' => __( 'Type text.', 'admin-page-framework-demo' ),     
                'hidden' => true,
            )
        );
        $this->addSettingFields(
            'grid', // the target section id
            array(
                'field_id' => 'grid_field',     
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
        $this->addSettingFields(
            // The 'Autocomplete' custom field type - the settings are the same as the tokeninput jQuery plugin.
            // see: http://loopj.com/jquery-tokeninput/
            // For the first parameter, use the 'settings' key and the second parameter, use the 'settings2'.
            'autocomplete', // the target section id
            array(
                'type'          => 'autocomplete',     
                'field_id'      => 'autocomplete_field',
                'title'         => __( 'Default', 'admin-page-framework-demo' ),
                'description'   => __( 'By default, all the post titles will be fetched in the background and will pop up.', 'admin-page-framework-demo' ),    
            ),
            array(
                'type' => 'autocomplete',     
                'field_id' => 'autocomplete_local_data',
                'title' => __( 'Local Data', 'admin-page-framework-demo' ),
                'settings' => array(
                    array( 'id' => 7, 'name' => 'Ruby' ),
                    array( 'id' => 11, 'name' => 'Python' ),
                    array( 'id' => 13, 'name' => 'JavaScript' ),
                    array( 'id' => 17, 'name' => 'ActionScript' ),
                    array( 'id' => 19, 'name' => 'Scheme' ),
                    array( 'id' => 23, 'name' => 'Lisp' ),
                    array( 'id' => 29, 'name' => 'C#' ),
                    array( 'id' => 31, 'name' => 'Fortran' ),
                    array( 'id' => 37, 'name' => 'Visual Basic' ),
                    array( 'id' => 41, 'name' => 'C' ),
                    array( 'id' => 43, 'name' => 'C++' ),
                    array( 'id' => 47, 'name' => 'Java' ),
                ),
                'settings2' => array(
                    'theme' => 'mac',
                    'hintText' => __( 'Type a programming language.', 'admin-page-framework-demo' ),
                    'prePopulate' => array(
                        array( 'id' => 3, 'name' => 'PHP' ),
                        array( 'id' => 5, 'name' => 'APS' ),
                    )     
                ),
                'description' => __( 'Predefined items are Ruby, Python, JavaScript, ActionScript, Scheme, Lisp, C#, Fortran, Vidual Basic, C, C++, Java.', 'admin-page-framework-demo' ),    
            ),
            array(
                'type' => 'autocomplete',     
                'field_id' => 'autocomplete_custom_post_type',
                'title' => __( 'Custom Post Type', 'admin-page-framework-demo' ),
                'settings' => add_query_arg( array( 'request' => 'autocomplete', 'post_type' => 'apf_posts' ) + $_GET, admin_url( AdminPageFramework_WPUtility::getPageNow() ) ),
                'settings2' => array( // equivalent to the second parameter of the tokenInput() method
                    'tokenLimit' => 5,
                    'preventDuplicates' =>    true,
                    'theme' => 'facebook',    
                    'searchDelay' => 50, // 50 milliseconds. Default: 300
                ),
                'description' => __( 'To set a custom post type, you need to compose the query url. This field is for the titles of this demo plugin\'s custom post type.', 'admin-page-framework-demo' ), //' syntax fixer
            ),     
            array(
                'type' => 'autocomplete',     
                'field_id' => 'autocomplete_mixed_field_types',
                'title' => __( 'Mixed Post Types', 'admin-page-framework-demo' ),
                'settings' => add_query_arg( 
                    array( 
                        'request' => 'autocomplete', 
                        'post_types' => 'post, page, apf_posts', // Note that the argument key is not 'post_type'
                        'post_status' => 'publish, private',
                    ) + $_GET,
                    admin_url( AdminPageFramework_WPUtility::getPageNow() )
                ),
                'settings2'     =>  array(
                    'theme'         => 'admin_page_framework',
                ),                
                'description' => __( 'To search from multiple post types use the \'post_types\' argument (not \'post_type\') and pass comma delimited post type slugs.', 'admin-page-framework-demo' ), //' syntax fixer
            ),     
            array(
                'type'          => 'autocomplete',     
                'field_id'      => 'autocomplete_repeatable_field',
                'title'         => __( 'Repeatable', 'admin-page-framework-demo' ),
                'repeatable'    => true,
            ),
            array(
                'type'          => 'autocomplete', 
                'field_id'      => 'autocomplete_users',
                'title'         => __( 'Search Users', 'admin-page-framework-demo' ),
                'settings'      => add_query_arg( 
                    array( 
                        'request'   => 'autocomplete', 
                        'type'      => 'user', // Note that the argument key is not 'post_type'
                    ) + $_GET,
                    admin_url( AdminPageFramework_WPUtility::getPageNow() )
                ),                
                'settings2'     =>  array(
                    'theme'             => 'admin_page_framework',
                    'hintText'          => __( 'Type a user name.', 'auto-post' ),
                    'preventDuplicates' => true,
                ),                
                'description'   => __( 'To search users, pass the \'user\' to the <code>type</code> argument.', 'admin-page-framework-demo' ), //' syntax fixer   
            ),            
            array()
        );     
        
        $this->addSettingFields(
            'link', // the target section id
            array(
                'field_id' => 'link_field',
                'type' => 'link',     
                'title' => __( 'Single Link' ),
            ),
            array(
                'field_id' => 'link_repeatable_field',
                'type' => 'link',     
                'title' => __( 'Repeatable Links' ),
                'repeatable' =>    true,
            ),     
            array()
        );     
        
        $this->addSettingFields(
            'system', // the target section id
            array(
                'field_id'      => 'system_information',
                'type'          => 'system',     
                'title'         => __( 'System Information', 'admin-page-framework-demo' ),
            ),
            array()
        );     
        
        // Github buttons. For the arguments, see https://github.com/ntkme/github-buttons#syntax
        $this->addSettingFields(
            'github', // the target section id
            array(
                'field_id'      => 'github_follow',
                'type'          => 'github',     
                'title'         => __( 'Follow', 'admin-page-framework-demo' ),
                'label'         => __( 'Small & Count', 'admin-page-framework-demo' ),
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'follow',        // either of the followings: follow, star, watch, fork, issue     
                'repository'    => 'admin-page-framework',
                array(
                    'size'          => 'mega',   //   currently only 'mega' can be supported. Otherwise, a small icon will be used.
                    'label'         => __( 'Mega & Count', 'admin-page-framework-demo' ),    
                ),
                array(
                    'size'          => '',      // pass something not 'mega' to use a small icon.
                    'label'         => __( 'Small', 'admin-page-framework-demo' ),                    
                    'count'         => false,   // whether or not the count should be displayed
                ),
                array(
                    'size'          => 'mega',   
                    'label'         => __( 'Mega', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                ),                                                                    
            ),          
            array(
                'field_id'      => 'github_star',
                'type'          => 'github',     
                'title'         => __( 'Star', 'admin-page-framework-demo' ),
                'label'         => __( 'Small & Count', 'admin-page-framework-demo' ),
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'star',        // either of the followings: follow, star, watch, fork, issue
                'repository'    => 'admin-page-framework',
                array(
                    'size'          => 'mega',   //   currently only 'mega' can be supported. Otherwise, a small icon will be used.
                    'label'         => __( 'Mega & Count', 'admin-page-framework-demo' ),    
                ),
                array(
                    'size'          => '',      //   pass something not 'mega' to use a small icon.
                    'label'         => __( 'Small', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                ),
                array(
                    'size'          => 'mega',   
                    'label'         => __( 'Mega', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                )                                          
            ),
            array(
                'field_id'      => 'github_watch',
                'type'          => 'github',     
                'title'         => __( 'Watch', 'admin-page-framework-demo' ),
                'label'         => __( 'Small & Count', 'admin-page-framework-demo' ),
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'watch',        // either of the followings: follow, star, watch, fork, issue
                'repository'    => 'admin-page-framework',
                array(
                    'size'          => 'mega',   //   currently only 'mega' can be supported. Otherwise, a small icon will be used.
                    'label'         => __( 'Mega & Count', 'admin-page-framework-demo' ),    
                ),
                array(
                    'size'          => '',      //   pass something not 'mega' to use a small icon.
                    'label'         => __( 'Small', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                ),
                array(
                    'size'          => 'mega',   
                    'label'         => __( 'Mega', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                )                                          
            ), 
            array(
                'field_id'      => 'github_fork',
                'type'          => 'github',     
                'title'         => __( 'Fork', 'admin-page-framework-demo' ),
                'label'         => __( 'Small & Count', 'admin-page-framework-demo' ),
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'fork',        // either of the followings: follow, star, watch, fork, issue
                'repository'    => 'admin-page-framework',
                array(
                    'size'          => 'mega',   //   currently only 'mega' can be supported. Otherwise, a small icon will be used.
                    'label'         => __( 'Mega & Count', 'admin-page-framework-demo' ),    
                ),
                array(
                    'size'          => '',      //   pass something not 'mega' to use a small icon.
                    'label'         => __( 'Small', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                ),
                array(
                    'size'          => 'mega',   
                    'label'         => __( 'Mega', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                )                                          
            ),      
            array(
                'field_id'      => 'github_issue',
                'type'          => 'github',     
                'title'         => __( 'Issue', 'admin-page-framework-demo' ),
                'label'         => __( 'Small & Count', 'admin-page-framework-demo' ),
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'issue',        // either of the followings: follow, star, watch, fork, issue
                'repository'    => 'admin-page-framework',
                array(
                    'size'          => 'mega',   //   currently only 'mega' can be supported. Otherwise, a small icon will be used.
                    'label'         => __( 'Mega & Count', 'admin-page-framework-demo' ),    
                ),
                array(
                    'size'          => '',      //   pass something not 'mega' to use a small icon.
                    'label'         => __( 'Small', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                ),
                array(
                    'size'          => 'mega',   
                    'label'         => __( 'Mega', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                )                                          
            ),             
            array(            
                'field_id'      => 'github_follow_custom_label',
                'type'          => 'github',     
                'title'         => __( 'Custom Label', 'admin-page-framework-demo' ),
                'value'         => __( 'Follow Me', 'admin-page-framework-demo' ),  // <-- the custom label 
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'follow',        // either of the followings: follow, star, watch, fork, issue     
                'repository'    => 'admin-page-framework',
                'size'          => 'mega',   
                'count'         => false,
            ),
            array(            
                'field_id'      => 'github_custom_link_a',
                'type'          => 'github',     
                'title'         => __( 'Download', 'admin-page-framework-demo' ),
                'size'          => 'mega',   
                'count'         => false,
                'attributes'    =>  array(
                    'href'      =>  'https://github.com/michaeluno/admin-page-framework/archive/master.zip',   // the target link url.
                    'data-icon' => 'octicon-cloud-download',    // override the icon. Pass the octicon icon class name.
                ),
                'value'         => __( 'Download', 'admin-page-framework-demo' ),
            ),
            array(            
                'field_id'      => 'github_custom_link_b',
                'type'          => 'github',     
                'title'         => __( 'Gist', 'admin-page-framework-demo' ),
                'size'          => 'mega',   
                'count'         => false,
                'attributes'    =>  array(
                    'href'      =>  'https://gist.github.com/schacon/1',   // the target link url.
                    'data-icon' => 'octicon-gist',    // override the icon. Pass the octicon icon class name.
                ),
                'value'         => 'The Meaning of Gist', 
            )              
        );
        
    }
    
    /*
     * Custom Field Types Page
     * */
    public function do_apf_custom_field_types() { // do_{page slug}
        submit_button();
    }
    
    /*
     * Custom field types - This is another way to register a custom field type. 
     * This method gets fired when the framework tries to define field types. 
     */
     public function field_types_APF_Demo_CustomFieldTypes( $aFieldTypeDefinitions ) { // field_types_{extended class name}
                
        /* 1. Include the file that defines the custom field type. 
         This class should extend the predefined abstract class that the library prepares already with necessary methods. */
        $_sFilePath = dirname( APFDEMO_FILE ) . '/third-party/geometry-custom-field-type/GeometryCustomFieldType.php';
        if ( file_exists( $_sFilePath ) ) {
            include( $_sFilePath );    
        }
        
        /* 2. Instantiate the class - use the getDefinitionArray() method to get the field type definition array.
         Then assign it to the filtering array with the key of the field type slug. */
        $_oFieldType = new GeometryCustomFieldType( 'APF_Demo' );
        $aFieldTypeDefinitions['geometry'] = $_oFieldType->getDefinitionArray();
        
        /* 3. Return the modified array. */
        return $aFieldTypeDefinitions;
        
    } 
    
}