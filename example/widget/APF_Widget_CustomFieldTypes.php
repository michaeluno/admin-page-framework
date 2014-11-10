<?php
class APF_Widget_CustomFieldTypes extends AdminPageFramework_Widget {
    
    /**
     * The user constructor.
     * 
     * Alternatively you may use start_{instantiated class name} method.
     */
    public function start() {
        
        if ( $this->oProp->bIsAdmin ) {
                    
            /*
             * ( Optional ) Register custom field types.
             */     
            /* 1. Include the file that defines the custom field type. */
            $_sPluginDirName = dirname( APFDEMO_FILE );
            $_aFiles         = array(
                $_sPluginDirName . '/third-party/date-time-custom-field-types/DateTimeCustomFieldType.php',
                $_sPluginDirName . '/third-party/dial-custom-field-type/DialCustomFieldType.php',
                $_sPluginDirName . '/third-party/autocomplete-custom-field-type/AutocompleteCustomFieldType.php',     
            );
            foreach( $_aFiles as $_sFilePath ) {
                if ( file_exists( $_sFilePath ) ) {     
                    include_once( $_sFilePath );
                }
            }
                        
            /* 2. Instantiate the classes by passing the instantiated admin page class name. */
            $_sClassName = get_class( $this );
            new DateTimeCustomFieldType( $_sClassName );
            new DialCustomFieldType( $_sClassName );
            new AutocompleteCustomFieldType( $_sClassName );  
            
        }
        
    }
    
    /**
     * Sets up arguments.
     * 
     * Alternatively you may use set_up_{instantiated class name} method.
     */
    public function setUp() {

        $this->setArguments( 
            array(
                'description'   =>  __( 'This is a sample widget of Admin Page Framework with custom field types.', 'admin-page-framework-demo' ),
            ) 
        );
        
    
    }    

    /**
     * Sets up the form.
     * 
     * Alternatively you may use load_{instantiated class name} method.
     */
    public function load( $oAdminWidget ) {
    
        $this->addSettingFields(       
            array(
                'field_id'      => 'title',
                'type'          => 'text',
                'title'         => __( 'Title', 'admin-page-framework-demo' ),
            ),        
            array(
                'field_id'      => 'date_time',
                'type'          => 'date_time',
                'title'         => __( 'Date & Time', 'admin-page-framework-demo' ),
            ),
            array(
                'field_id'      => 'dial',
                'type'          => 'dial',
                'title'         => __( 'Dial', 'admin-page-framework-demo' ),
            ),     
            array(
                'field_id'      => 'autocomplete',
                'type'          => 'autocomplete',
                'title'         => __( 'Post', 'admin-page-framework-demo' ),
                'settings2'     =>  array(
                    'theme'             => 'admin_page_framework',
                    'preventDuplicates' => true,
                ),                    
            ),      
   
            array()
        );        

        
    }
    
    /**
     * Validates the submitted form data.
     * 
     * Alternatively you may use validation_{instantiated class name} method.
     */
    public function validate( $aSubmit, $aStored, $oAdminWidget ) {
        
        // Uncomment the following line to check the submitted value.
        // AdminPageFramework_Debug::log( $aSubmit );
        
        return $aSubmit;
        
    }    
    
    /**
     * Print out the contents in the front-end.
     * 
     * Alternatively you may use the content_{instantiated class name} method.
     */
    public function content( $sContent, $aArguments, $aFormData ) {
        
        return $sContent
            . '<p>' . __( 'Hello world! This is a widget created by Admin Page Framework with some custom field types.', 'admin-page-framework-demo' ) . '</p>'
            . AdminPageFramework_Debug::get( $aArguments )
            . AdminPageFramework_Debug::get( $aFormData );
            
    }
        
}