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

class APF_NetworkAdmin_ManageOptions extends AdminPageFramework_NetworkAdmin {

    public function setUp() { // this method automatically gets triggered with the wp_loaded hook. 

        /* ( optional ) this can be set via the constructor. For available values, see https://codex.wordpress.org/Roles_and_Capabilities */
        $this->setCapability( 'read' );
        
        /* ( required ) Set the root page */
        $this->setRootMenuPageBySlug( 'APF_NetworkAdmin' );    

        
        /* ( required ) Add sub-menu items (pages or links) */
        $this->addSubMenuItems(    
            array(
                'title'         => __( 'Manage Options', 'admin-page-framework-demo' ),
                'page_slug'     => 'apf_manage_options',
                'screen_icon'   => 'link-manager',    
                'order'         => 3, // ( optional )
            )
        );
                
        $this->addInPageTabs( // ( optional )
            /*
             * Manage Options
             * */
            'apf_manage_options', // target page slug
            array(
                'tab_slug' => 'saved_data',
                'title' => 'Saved Data',
            ),
            array(
                'tab_slug' => 'properties',
                'title' => __( 'Properties', 'admin-page-framework-demo' ),
            ),
            array(
                'tab_slug' => 'messages',
                'title' => __( 'Messages', 'admin-page-framework-demo' ),
            ),     
            array(
                'tab_slug' => 'export_import',
                'title' => __( 'Export / Import', 'admin-page-framework-demo' ),     
            ),
            array(
                'tab_slug' => 'delete_options',
                'title' => __( 'Reset', 'admin-page-framework-demo' ),
                'order' => 99,    
            ),     
            array( // TIPS: you can hide an in-page tab by setting the 'show_in_page_tab' key
                'tab_slug' => 'delete_options_confirm',
                'title' => __( 'Reset Confirmation', 'admin-page-framework-demo' ),
                'show_in_page_tab' => false,
                'parent_tab_slug' => 'delete_options',
                'order' => 97,
            )
        );

        /* ( optional ) Determine the page style */
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs
    
        /* 
         * ( optional ) Enqueue styles  
         * $this->enqueueStyle(  'stylesheet url/path' , 'page slug (optional)', 'tab slug (optional)', 'custom argument array(optional)' );
         * */
        $sStyleHandle = $this->enqueueStyle(  dirname( APFDEMO_FILE ) . '/asset/css/code.css', 'apf_manage_options' ); // a path can be used
    
    
    }
    
    /**
     * The pre-defined callback method that is triggered when the page loads.
     */ 
    public function load_apf_manage_options( $oAdminPage ) { // load_{page slug}
        
        $this->addSettingSections(    
            array(
                'section_id' => 'submit_buttons_manage',
                'page_slug' => 'apf_manage_options',
                'tab_slug' => 'delete_options',
                'title' => 'Reset Button',
                'order' => 10,
            ),     
            array(
                'section_id' => 'submit_buttons_confirm',
                'tab_slug' => 'delete_options_confirm',
                'title' => 'Confirmation',
                'description' => "<div class='settings-error error'><p><strong>Are you sure you want to delete all the options?</strong></p></div>",
                'order' => 10,
            ),     
            array(
                'section_id' => 'exports',
                'tab_slug' => 'export_import',
                'title' => 'Export Data',
                'description' => 'After exporting the options, change and save new options and then import the file to see if the options get restored.',
            ),     
            array(
                'section_id' => 'imports',
                'tab_slug' => 'export_import',
                'title' => 'Import Data',
            ),     
            array()     
        );
    
        /*
         * Fields for the manage option page.
         */
        $this->addSettingFields(     
            array( // Delete Option Button
                'field_id' => 'submit_manage',
                'section_id' => 'submit_buttons_manage',
                'title' => __( 'Delete Options', 'admin-page-framework' ),
                'type' => 'submit',
                'label' => __( 'Delete Options', 'admin-page-framework' ),
                'href' => network_admin_url( 'admin.php?page=apf_manage_options&tab=delete_options_confirm' ),
                'attributes' => array(
                    'class' => 'button-secondary',
                ),     
            ),     
            array( // Delete Option Confirmation Button
                'field_id' => 'submit_delete_options_confirmation',
                'section_id' => 'submit_buttons_confirm',
                'title' => __( 'Delete Options', 'admin-page-framework' ),
                'type' => 'submit',     
                'label' => __( 'Delete Options', 'admin-page-framework' ),
                'redirect_url' => network_admin_url( 'admin.php?page=apf_manage_options&tab=saved_data&settings-updated=true' ),
                'attributes' => array(
                    'class' => 'button-secondary',
                ),
            ),     
            array(
                'field_id' => 'export_format_type',     
                'section_id' => 'exports',
                'title' => __( 'Export Format Type', 'admin-page-framework-demo' ),
                'type' => 'radio',
                'description' => __( 'Choose the file format. Array means the PHP serialized array.', 'admin-page-framework-demo' ),
                'label' => array( 
                    'json' => __( 'JSON', 'admin-page-framework-demo' ),
                    'array' => __( 'Serialized Array', 'admin-page-framework-demo' ),
                    'text' => __( 'Text', 'admin-page-framework-demo' ),
                ),
                'default' => 'json',
            ),     
            array( // Single Export Button
                'field_id' => 'export_single',
                'section_id' => 'exports',
                'type' => 'export',
                'description' => __( 'Download the saved option data.', 'admin-page-framework-demo' ),
            ),
            array( // Multiple Export Buttons
                'field_id' => 'export_multiple',
                'section_id' => 'exports',
                'title' => __( 'Multiple Export Buttons', 'admin-page-framework-demo' ),
                'type' => 'export',
                'label' => __( 'Pain Text', 'admin-page-framework-demo' ),
                'file_name' => 'plain_text.txt',
                'format' => 'text',
                'attributes' => array(
                    'field' => array(
                        'style' => 'display: inline; clear: none;',
                    ),
                ),
                array(
                    'label' => __( 'JSON', 'admin-page-framework-demo' ),
                    'file_name' => 'json.json', 
                    'format' => 'json',
                ),
                array(
                    'label' => __( 'Serialized Array', 'admin-page-framework-demo' ),
                    'file_name' => 'serialized_array.txt', 
                    'format' => 'array',
                ),
                'description' => __( 'To set a file name, use the <code>file_name</code> argument in the field definition array.', 'admin-page-framework-demo' )
                 . ' ' . __( 'To set the data format, use the <code>format</code> argument in the field definition array.', 'admin-page-framework-demo' ),    
            ),    
            array( // Custom Data to Export
                'field_id' => 'export_custom_data',
                'section_id' => 'exports',     
                'title' => __( 'Custom Exporting Data', 'admin-page-framework-demo' ),
                'type' => 'export',
                'data' => __( 'Hello World! This is custom export data.', 'admin-page-framework-demo' ),
                'file_name' => 'hello_world.txt',
                'label' => __( 'Export Custom Data', 'admin-page-framework-demo' ),
                'description' => __( 'It is possible to set custom data to be downloaded. For that, use the <code>data</code> argument in the field definition array.', 'admin-page-framework-demo' ),    
            ),
            array(
                'field_id' => 'import_format_type',     
                'section_id' => 'imports',
                'title' => __( 'Import Format Type', 'admin-page-framework-demo' ),
                'type' => 'radio',
                'description' => __( 'The text format type will not set the option values properly. However, you can see that the text contents are directly saved in the database.', 'admin-page-framework-demo' ),
                'label' => array( 
                    'json' => __( 'JSON', 'admin-page-framework-demo' ),
                    'array' => __( 'Serialized Array', 'admin-page-framework-demo' ),
                    'text' => __( 'Text', 'admin-page-framework-demo' ),
                ),
                'default' => 'json',
            ),
            array( // Single Import Button
                'field_id' => 'import_single',
                'section_id' => 'imports',
                'title' => __( 'Single Import Field', 'admin-page-framework-demo' ),
                'type' => 'import',
                'description' => __( 'Upload the saved option data.', 'admin-page-framework-demo' ),
                'label' => 'Import Options',
            ),     
            array()
        );     
        
    }
    
    /*
     * Manage Options Page
     * */
    public function do_apf_manage_options_saved_data() { // do_{page slug}_{tab slug}
    
        ?>
        <h3><?php _e( 'Saved Data', 'admin-page-framework-demo' ); ?></h3>
        <p>
        <?php 
            echo sprintf( __( 'To retrieve the saved option values simply you can use the WordPress <code>get_option()</code> function. The key is the instantiated class name by default unless it is specified in the constructor. In this demo plugin, <code>%1$s</code>, is used as the option key.', 'admin-page-framework-demo' ), $this->oProp->sOptionKey );
            echo ' ' . sprintf( __( 'It is stored in the <code>$this->oProp-sOptionKey</code> class property so you may access it directly to confirm the value. So the required code would be <code>get_option( %1$s );</code>.', 'admin-page-framework-demo' ), $this->oProp->sOptionKey );
            echo ' ' . __( 'If you are retrieving them within the framework class, simply call <code>$this->oProp->aOptions</code>.', 'admin-page-framework-demo' );
        ?>
        </p>
        <p>
        <?php
            echo __( 'Alternatively, there is the <code>AdminPageFramework::getOption()</code> static method. This allows you to retrieve the array element by specifying the option key and the array key (field id or section id).', 'admin-page-framework-demo' );
            echo ' ' . __( 'Pass the option key to the first parameter and an array representing the dimensional keys to the second parameter', 'admin-page-framework-demo' );
            echo ' ' . __( '<code>$aData = AdminPageFramework::getOption( \'APF_NetworkAdmin\', array( \'text_fields\', \'text\' ), \'default value\' );</code> will retrieve the option array value of <code>$aArray[\'text_field\'][\'text\']</code>.', 'admin-page-framework-demo' );    
            echo ' ' . __( 'This method is merely to avoid multiple uses of <code>isset()</code> to prevent PHP warnings.', 'admin-page-framework-demo' );
            echo ' ' . __( 'So if you already know how to retrieve a value of an array element, you don\'t have to use it.', 'admin-page-framework-demo' ); // ' syntax fixer
        ?>
        </p>
        <?php
            echo $this->oDebug->getArray( $this->oProp->aOptions ); 
            // echo $this->oDebug->getArray( AdminPageFramework::getOption( 'APF_NetworkAdmin', array( 'text_fields' ) ) ); 
        
    }
    public function do_apf_manage_options_properties() { // do_{page slug}_{tab slug}
        ?>
        <h3><?php _e( 'Framework Properties', 'admin-page-framework-demo' ); ?></h3>
        <p><?php _e( 'These are the property values stored in the framework. Advanced users may change the property values by directly modifying the <code>$this->oProp</code> object.', 'admin-page-framework-demo' ); ?></p>
        <pre><code>$this-&gt;oDebug-&gt;getArray( get_object_vars( $this-&gt;oProp ) );</code></pre>     
        <?php
            $this->oDebug->dumpArray( get_object_vars( $this->oProp ) );
    }
    public function do_apf_manage_options_messages() { // do_{page slug}_{tab slug}
        ?>
        <h3><?php _e( 'Framework Messages', 'admin-page-framework-demo' ); ?></h3>
        <p><?php _e( 'You can change the framework\'s defined internal messages by directly modifying the <code>$aMessages</code> array in the <code>oMsg</code> object.', 'admin-page-framework-demo' ); // ' syntax fixer ?>
            <?php _e( 'The keys and the default values are listed below.', 'admin-page-framework-demo' ); ?>
        </p>
        <?php
            $_aMessages = array();
            foreach ( $this->oMsg->aMessages as $_sLabel => $_sTranslation ) {
                $_aMessages[ $_sLabel ] = $this->oMsg->__( $_sLabel );
            }
            echo $this->oDebug->getArray( $_aMessages );
    }
    
    /*
     * Import and Export Callbacks
     * */
    public function export_name_APF_NetworkAdmin_exports_export_single( $sFileName, $sFieldID, $sInputID ) { // export_name_{instantiated class name}_{export section id}_{export field id}

        // Change the exporting file name based on the selected format type in the other field.
        $sSelectedFormatType = isset( $_POST[ $this->oProp->sOptionKey ]['exports']['export_format_type'] )
            ? $_POST[ $this->oProp->sOptionKey ]['exports']['export_format_type'] 
            : null;    
        $aFileNameParts = pathinfo( $sFileName );
        $sFileNameWOExt = $aFileNameParts['filename'];     
        switch( $sSelectedFormatType ) {     
            default:
            case 'json':
                $sReturnName = $sFileNameWOExt . '.json';
                break;
            case 'text':
            case 'array':
                $sReturnName = $sFileNameWOExt . '.txt';
                break;     
        }
        return $sReturnName;
        
    }
    public function export_format_APF_NetworkAdmin_exports_export_single( $sFormatType, $sFieldID ) { // export_format_{instantiated class name}_{export section id}_{export field id}

        // Set the internal formatting type based on the selected format type in the other field.
        return isset( $_POST[ $this->oProp->sOptionKey ]['exports']['export_format_type'] ) 
            ? $_POST[ $this->oProp->sOptionKey ]['exports']['export_format_type']
            : $sFormatType;
        
    }    
    public function import_format_apf_manage_options_export_import( $sFormatType, $sFieldID ) { // import_format_{page slug}_{tab slug}
        
        return isset( $_POST[ $this->oProp->sOptionKey ]['imports']['import_format_type'] ) 
            ? $_POST[ $this->oProp->sOptionKey ]['imports']['import_format_type']
            : $sFormatType;
        
    }
    public function import_APF_NetworkAdmin_imports_import_single( $vData, $aOldOptions, $sFieldID, $sInputID, $sImportFormat, $sOptionKey ) { // import_{instantiated class name}_{import section id}_{import field id}

        if ( $sImportFormat == 'text' ) {
            $this->setSettingNotice( __( 'The text import type is not supported.', 'admin-page-framework-demo' ) );
            return $aOldOptions;
        }
        
        $this->setSettingNotice( __( 'Importing options were validated.', 'admin-page-framework-demo' ), 'updated' );
        return $vData;
        
    } 
    
    public function validation_APF_NetworkAdmin_ManageOptions( $aInput, $aOldOptions, $oAdmin ) { // validation_{instantiated class name}
        
        /* If the delete options button is pressed, return an empty array that will delete the entire options stored in the database. */
        if ( isset( $_POST[ $this->oProp->sOptionKey ]['submit_buttons_confirm']['submit_delete_options_confirmation'] ) ) { 
            return array(); 
        }
        return $aInput;
        
    }     
    
}