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

class APF_MetaBox_CustomFieldTypes extends AdminPageFramework_MetaBox {
    
    /**
     * Called at the end of the constructor.
     * 
     * Alternatively, the 'start_{instantiated class name}' hook can be used.
     */
    public function start() {}

    
    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {
        
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
    
        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
            array(
                'field_id'      => 'metabox_geometry',
                'type'          => 'geometry',
                'title'         => __( 'Geometry', 'admin-page-framework-demo' ),
            ),
            array(
                'field_id'      => 'metabox_date',
                'type'          => 'date',
                'title'         => __( 'Date', 'admin-page-framework-demo' ),     
            ),
            array(
                'field_id'      => 'metabox_date_repeatable',
                'type'          => 'date',
                'title'         => __( 'Date Repeatable', 'admin-page-framework-demo' ),     
                'repeatable'    =>    true,
            ),     
            array(
                'field_id'      => 'metabox_date_time',
                'type'          => 'date_time',
                'title'         => __( 'Date Time', 'admin-page-framework-demo' ),     
            ),
            array(
                'field_id'      => 'metabox_date_time_repeatable',
                'type'          => 'date_time',
                'title'         => __( 'Date Time Repeatable', 'admin-page-framework-demo' ),     
                'repeatable'    =>    true,     
            ),     
            array(
                'field_id'      => 'metabox_time',
                'type'          => 'time',
                'title'         => __( 'Time', 'admin-page-framework-demo' ),     
            ),
            array(
                'field_id'      => 'metabox_time_repeatable',
                'type'          => 'time',
                'title'         => __( 'Time Repeatable', 'admin-page-framework-demo' ),     
                'repeatable'    =>    true,
            ),
            array(
                'field_id'      => 'metabox_dial',
                'type'          => 'dial',
                'title'         => __( 'Dial', 'admin-page-framework-demo' ),
            ),     
            array(
                'field_id'      => 'metabox_dial_repeatable',
                'type'          => 'dial',
                'title'         => __( 'Dial Repeatable', 'admin-page-framework-demo' ),     
                'repeatable'    =>    true,
            ),
            array(
                'field_id'      => 'metabox_font',
                'type'          => 'font',
                'title'         => __( 'Font', 'admin-page-framework-demo' ),
                'attributes'    => array(
                    'input'  => array(
                        'size'  => 40,
                    ),
                ),
            ),     
            array(
                'field_id'      => 'metabox_font_repeatable',
                'type'          => 'font',
                'title'         => __( 'Font Repeatable', 'admin-page-framework-demo' ),     
                'repeatable'    =>    true,
                'attributes'    => array(
                    'input'  => array(
                        'size'  => 40,
                    ),
                ),                
            ),
            array()
        );     
    }
        
    /**
     * The pre-defined validation callback method.
     */
    public function validation_APF_MetaBox_CustomFieldTypes( $aInput, $aOldInput, $oAdminPage ) { // validation_{instantiated class name}
        
        // You can check the passed values and correct the data by modifying them.
        // $this->oDebug->log( $aInput );
         
        return $aInput;
        
    }
}