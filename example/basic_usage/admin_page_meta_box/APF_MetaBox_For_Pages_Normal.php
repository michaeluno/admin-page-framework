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

class APF_MetaBox_For_Pages_Normal extends AdminPageFramework_MetaBox_Page {
        
    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {

        /*
         * ( optional ) Adds a contextual help pane at the top right of the page that the meta box resides.
         */
        $this->addHelpText( 
            __( 'This text will appear in the contextual help pane.', 'admin-page-framework-demo' ), 
            __( 'This description goes to the sidebar of the help pane.', 'admin-page-framework-demo' )
        );    
    
        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
            array(
                'field_id' => 'metabox_text_field',
                'type' => 'text',
                'title' => __( 'Text Input', 'admin-page-framework-demo' ),
                'description' => __( 'The description for the field.', 'admin-page-framework-demo' ),
                'help' => __( 'This is help text.', 'admin-page-framework-demo' ),
                'help_aside' => __( 'This is additional help text which goes to the side bar of the help pane.', 'admin-page-framework-demo' ),
            ),
            array(
                'field_id' => 'metabox_text_field_repeatable',
                'type' => 'text',
                'title' => __( 'Text Repeatable', 'admin-page-framework-demo' ),
                'repeatable' =>    true
            ),     
            array(
                'field_id' => 'metabox_textarea_field',
                'type' => 'textarea',
                'title' => __( 'Text Area', 'admin-page-framework-demo' ),
                'description' => __( 'The description for the field.', 'admin-page-framework-demo' ),
                'help' => __( 'This a <em>text area</em> input field, which is larger than the <em>text</em> input field.', 'admin-page-framework-demo' ),
                'default' => __( 'This is a default text value.', 'admin-page-framework-demo' ),
                'attributes' => array(
                    'cols' => 40,     
                ),
            )
        );     
        
    }
    
    /**
     * (optional) Use this method to insert your custom text.
     */
    public function do_APF_MetaBox_For_Pages_Normal() { // do_{instantiated class name}
        ?>
            <p><?php _e( 'This meta box is placed with the <code>normal</code>context and this text is inserted with the <code>do_{instantiated class name}</code> hook.', 'admin-page-framework-demo' ) ?></p>
        <?php
        
    }
    
    /**
     * The content filter callback method.
     * 
     * Alternatively use the `content_{instantiated class name}` method instead.
     */
    public function content( $sContent ) {
        
        $_sInsert = "<p>" . sprintf( __( 'This text is inserted with the <code>%1$s</code> method.', 'admin-page-framework-demo' ), __FUNCTION__ ) . "</p>";
        return $_sInsert . $sContent;        
        
    }
    
    /**
     * The content filter callback method.
     */
    public function content_APF_MetaBox_For_Pages_Normal( $sContent ) { // content_{instantiated class name}
        
        $_sInsert = "<p>" . sprintf( __( 'This text is inserted with the <code>%1$s</code> hook.', 'admin-page-framework-demo' ), __FUNCTION__ ) . "</p>";
        return $sContent . $_sInsert;
        
    }    
    
    
    /**
     * (optional) The predefined validation callback method.
     * 
     * Alternatively, use the `validation_{instantiated class name}` method instead.
     */
    public function validate( $aInput, $aOldInput, $oAdminPage ) { // validtion_{instantiated class name}
        
        // Do something with the submitted data.
        return $aInput;
        
    }

    
}