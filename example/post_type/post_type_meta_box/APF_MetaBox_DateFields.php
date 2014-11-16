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

class APF_MetaBox_DateFields extends AdminPageFramework_MetaBox {
    
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
            dirname( APFDEMO_FILE ) . '/third-party/date-time-custom-field-types/DateCustomFieldType.php',
        );
        foreach( $aFiles as $sFilePath ) {
            if ( file_exists( $sFilePath ) ) { 
                include( $sFilePath ); 
            }
        }
                    
        /* 2. Instantiate the classes  */
        $_sClassName = get_class( $this );
        new DateCustomFieldType( $_sClassName );
    
        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
            array(
                'field_id'      => 'my_custom_date',
                'type'          => 'date',
                'date_format'   => 'yy-mm-dd',  // e.g. 2014-10-06
                'title'         => __( 'Custom Date', 'admin-page-framework-demo' ),     
            ),
            array()
        );     
    }
        
    /**
     * The pre-defined validation callback method.
     */
    public function validation_APF_MetaBox_DateFields( $aInput, $aOldInput, $oAdminPage ) { // validation_{instantiated class name}
        
        // Let's store the date set in the 'my_custom_date' field as a timestamp so that we'll retrieve posts based on the set date later in the example hidden page.
        global $post;
        $_iTimestamp = isset( $aInput['my_custom_date'] ) ? strtotime( $aInput['my_custom_date'] ) : 0;
        update_post_meta( $post->ID, '_my_custom_date_timestamp', $_iTimestamp );
               
        return $aInput;
        
    }
}