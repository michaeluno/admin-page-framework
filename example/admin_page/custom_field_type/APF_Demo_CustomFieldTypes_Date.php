<?php
class APF_Demo_CustomFieldTypes_Date {
    
    /**
     * Stores the caller class name, set in the constructor.
     */   
    public $sClassName  = 'APF_Demo_CustomFieldTypes';
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_custom_field_types';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'date';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'date_pickers';
    
    /**
     * Sets up hooks and properties.
     */
    public function __construct( $sClassName='', $sPageSlug='', $sTabSlug='' ) {
        
        $this->sClassName   = $sClassName ? $sClassName : $this->sClassName;
        $this->sPageSlug    = $sPageSlug ? $sPageSlug : $this->sPageSlug;
        $this->sTabSlug     = $sTabSlug ? $sTabSlug : $this->sTabSlug;
        
        // load_ + page slug
        add_action( 'load_' . $this->sPageSlug, array( $this, 'replyToAddTab' ) );
        
        $this->registerFieldTypes( $this->sClassName );
        
    }
        
        /**
         * Registers the field types.
         */
        private function registerFieldTypes( $sClassName ) {
            
            $_sPluginDirName = dirname( APFDEMO_FILE );
            include( $_sPluginDirName . '/third-party/date-time-custom-field-types/DateCustomFieldType.php' );
            include( $_sPluginDirName . '/third-party/date-time-custom-field-types/TimeCustomFieldType.php' );
            include( $_sPluginDirName . '/third-party/date-time-custom-field-types/DateTimeCustomFieldType.php' );
            include( $_sPluginDirName . '/third-party/date-time-custom-field-types/DateRangeCustomFieldType.php' );
            include( $_sPluginDirName . '/third-party/date-time-custom-field-types/DateTimeRangeCustomFieldType.php' );
            include( $_sPluginDirName . '/third-party/date-time-custom-field-types/TimeRangeCustomFieldType.php' );
            include( $_sPluginDirName . '/third-party/reset-custom-field-type/ResetCustomFieldType.php' );
            
            new DateCustomFieldType( $sClassName );
            new TimeCustomFieldType( $sClassName );
            new DateTimeCustomFieldType( $sClassName );
            new DateRangeCustomFieldType( $sClassName );
            new DateTimeRangeCustomFieldType( $sClassName );
            new TimeRangeCustomFieldType( $sClassName );   
            new ResetCustomFieldType( $sClassName );
            
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
                'title'     => __( 'Date & Time', 'admin-page-framework-demo' ),    
            )
        );  
        
        // load_ + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToAddFormElements' ) );
        
        // do_ + page slug + tab slug 
        add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToInsertOutput' ) );        
        
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
                'title'         => __( 'Date Custom Field Type', 'admin-page-framework' ),
                'description'   => __( 'We have date and time pickers.', 'admin-page-framework-demo' ),            
            )
        );        
        
        // Fields   
        $oAdminPage->addSettingFields(
            // To use advanced options, pass the options in the 'options' argument.
            // The argument keys are the same as the ones documented here : http://trentrichardson.com/examples/timepicker/#rest_examples     
            $this->sSectionID, // the target section ID.
            array( // Single date picker
                'field_id'      => 'date',
                'title'         => __( 'Date', 'admin-page-framework-demo' ),
                'type'          => 'date',
            ),     
            array( // Custom date format - use a unix timestamp.
                'field_id'      => 'date_custom_date_format',
                'title'         => __( 'Date Format', 'admin-page-framework-demo' ),
                'type'          => 'date',
                'date_format'   => '@',
                'description'   => __( 'Setting <code>@</code> to the <code>date_format</code> argument will save the date as a timestamp.', 'admin-page-framework-demo' ),
                'attributes'    => array(
                    'size'  => 16,
                ),
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
            array(
                'field_id'          => 'send',
                'type'              => 'reset',
                'label'             => __( 'Reset', 'admin-page-framework-demo' ),
                'label_min_width'   => 0,
                array(
                    'type'  => 'submit',
                    'label' => __( 'Save', 'admin-page-framework-demo' ),                  
                ),
                'attributes'    => array(
                    'style'    => 'float: right;',
                    'fieldset' => array(
                        'style' => 'float: right;'
                    ),
                    'field'     => array(
                        'style' => 'float: none;'
                    ),            
                ),
            ),               
            array()
        );         
   
    }
    
    /**
     * Inserts an output into the page.
     */
    public function replyToInsertOutput() {
    }   
    
}