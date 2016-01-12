<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

class APF_MetaBox_For_Pages_Normal extends AdminPageFramework_PageMetaBox {
        
    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {

        /*
         * ( optional ) Adds a contextual help pane at the top right of the page that the meta box resides.
         */
        $this->addHelpText( 
            __( 'This text will appear in the contextual help pane.', 'admin-page-framework-loader' ), 
            __( 'This description goes to the sidebar of the help pane.', 'admin-page-framework-loader' )
        );    
    
        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
            array(
                'field_id'      => 'metabox_text_field',
                'type'          => 'text',
                'title'         => __( 'Text Input', 'admin-page-framework-loader' ),
                'tip'           => __( 'With the validation callback method, you can show field error messages to the user.', 'admin-page-framework-loader' ),
                'description'   => __( 'Type more than 3 characters.', 'admin-page-framework-loader' ),
                'help'          => __( 'This is help text.', 'admin-page-framework-loader' ),
                'help_aside'    => __( 'This is additional help text which goes to the side bar of the help pane.', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'      => 'metabox_text_field_repeatable',
                'type'          => 'text',
                'title'         => __( 'Text Repeatable', 'admin-page-framework-loader' ),
                'repeatable'    => true,
            ),     
            array(
                'field_id'      => 'metabox_textarea_field',
                'type'          => 'textarea',
                'title'         => __( 'Text Area', 'admin-page-framework-loader' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-loader' ),
                'help'          => __( 'This a <em>text area</em> input field, which is larger than the <em>text</em> input field.', 'admin-page-framework-loader' ),
                'default'       => __( 'This is a default text value.', 'admin-page-framework-loader' ),
                'attributes'    => array(
                    'cols' => 40,     
                ),
            ),
            // array(
                // 'field_id' => 'submit_in_meta_box',
                // 'type' => 'submit',
                // 'show_title_column' => false,
                // 'label_min_width' => 0,
                // 'attributes' => array(
                    // 'fieldset' => array(
                        // 'style' => 'float:right;',
                    // ),
                // ),
            // ),            
            array()
        );     
        
    }
    
    /**
     * (optional) Use this method to insert your custom text.
     * @callback        action      do_{instantiated class name}
     */
    public function do_APF_MetaBox_For_Pages_Normal() { 
        ?>
            <p><?php _e( 'This meta box is placed with the <code>normal</code>context and this text is inserted with the <code>do_{instantiated class name}</code> hook.', 'admin-page-framework-loader' ) ?></p>
        <?php
        
    }
    
    /**
     * The content filter callback method.
     * 
     * Alternatively use the `content_{instantiated class name}` method instead.
     */
    public function content( $sContent ) {
        
        $_sInsert = "<p>" . sprintf( __( 'This text is inserted with the <code>%1$s</code> method.', 'admin-page-framework-loader' ), __FUNCTION__ ) . "</p>";
        return $_sInsert . $sContent;        
        
    }
    
    /**
     * The content filter callback method.
     */
    public function content_APF_MetaBox_For_Pages_Normal( $sContent ) { // content_{instantiated class name}
        
        $_sInsert = "<p>" . sprintf( __( 'This text is inserted with the <code>%1$s</code> hook.', 'admin-page-framework-loader' ), __FUNCTION__ ) . "</p>";
        return $sContent . $_sInsert;
        
    }    
    
    
    /**
     * (optional) The predefined validation callback method.
     * 
     * Alternatively, use the `validation_{instantiated class name}()` method instead.
     */
    public function validate( $aInput, $aOldInput, $oAdminPage ) {
        
        $_bIsValid  = true;
        $_aErrors   = array();

        // You can check the passed values with the log() method of the oDebug object.
        // $this->oDebug->log( $aInput );     
        // $this->oDebug->log( $aOldInput );
        
        // Validate the submitted data.
        if ( strlen( trim( $aInput['metabox_text_field'] ) ) < 3 ) {
            
            $_aErrors['metabox_text_field'] = __( 'The entered text is too short! Type more than 2 characters.', 'admin-page-framework-loader' ) . ': ' . $aInput['metabox_text_field'];
            $_bIsValid = false;     
            
        }
        
        if ( ! $_bIsValid ) {
            
            $this->setFieldErrors( $_aErrors );
            $this->setSettingNotice( __( 'There was an error in your input in meta box form fields', 'admin-page-framework-loader' ) );    
            return $aOldInput;
            
        }

        return $aInput;        

    }

    
}

new APF_MetaBox_For_Pages_Normal(
    null,                                           // meta box id - passing null will make it auto generate
    __( 'Sample Meta Box for Admin Pages Inserted in Normal Area', 'admin-page-framework-loader' ), // title
    'apf_first_page',                               // page slugs
    'normal',                                       // context
    'default'                                       // priority
);
