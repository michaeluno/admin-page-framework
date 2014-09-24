<?php
class APF_NetworkAdmin extends AdminPageFramework_NetworkAdmin {
        
    /*
     * ( Required ) In the setUp() method, you will define how the pages and the form elements should be composed.
     */
    public function setUp() { // this method automatically gets triggered with the wp_loaded hook. 

        /* ( optional ) this can be set via the constructor. For available values, see https://codex.wordpress.org/Roles_and_Capabilities */
        // $this->setCapability( 'read' );
        
        /* ( required ) Set the root page */
        $this->setRootMenuPage( 'Admin Page Framework' ); // or $this->setRootMenuPageBySlug( 'sites.php' );    
            
        /* ( required ) Add sub-menu items (pages or links) */
        $this->addSubMenuItems(    
            array(
                'title' => __( 'Built-in Field Types', 'admin-page-framework-demo' ),
                'page_slug' => 'apf_builtin_field_types',
                'screen_icon' => 'options-general', // one of the screen type from the below can be used.
                /* Screen Types (for WordPress v3.7.x or below) :
                    'edit', 'post', 'index', 'media', 'upload', 'link-manager', 'link', 'link-category', 
                    'edit-pages', 'page', 'edit-comments', 'themes', 'plugins', 'users', 'profile', 
                    'user-edit', 'tools', 'admin', 'options-general', 'ms-admin', 'generic',  
                */     
                'order' => 1, // ( optional ) - if you don't set this, an index will be assigned internally in the added order
            )
        );
        
        /*
         * ( optional ) Add in-page tabs - In Admin Page Framework, there are two kinds of tabs: page-heading tabs and in-page tabs.
         * Page-heading tabs show the titles of sub-page items which belong to the set root page. 
         * In-page tabs show tabs that you define to be embedded within an individual page.
         */
        $this->addInPageTabs(
            /*
             * In-page tabs to display built-in field types
             * */
            'apf_builtin_field_types', // set the target page slug so that the 'page_slug' key can be omitted from the next continuing in-page tab arrays.
            array(
                'tab_slug' => 'textfields', // avoid hyphen(dash), dots, and white spaces
                'title' => __( 'Text', 'admin-page-framework-demo' ),
                'order' => 1, // ( optional ) - if you don't set this, an index will be assigned internally in the added order
            ),     
            array(
                'tab_slug' => 'selectors',
                'title' => __( 'Selectors', 'admin-page-framework-demo' ),
            ),     
            array(
                'tab_slug' => 'files',
                'title' => __( 'Files', 'admin-page-framework-demo' ),
            ),
            array(
                'tab_slug' => 'checklist',
                'title' => __( 'Checklist', 'admin-page-framework-demo' ),
            ),     
            array(
                'tab_slug' => 'misc',
                'title' => __( 'MISC', 'admin-page-framework-demo' ),    
            ),     
            array(
                'tab_slug' => 'verification',
                'title' => __( 'Verification', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug' => 'mixed_types',
                'title' => __( 'Mixed', 'admin-page-framework-demo' ),    
            ),
            array(
                'tab_slug' => 'sections',
                'title' => __( 'Sections', 'admin-page-framework-demo' ),    
            ),
            array()
        );

        
        /* ( optional ) Determine the page style */
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs
        
            
        /*
         * ( optional ) Contextual help pane
         */
        $this->addHelpTab( 
            array(
                'page_slug' => 'apf_builtin_field_types', // ( mandatory )
                // 'page_tab_slug' => null, // ( optional )
                'help_tab_title' => 'Admin Page Framework',
                'help_tab_id' => 'admin_page_framework', // ( mandatory )
                'help_tab_content' => __( 'This contextual help text can be set with the <code>addHelpTab()</code> method.', 'admin-page-framework' ),
                'help_tab_sidebar_content' => __( 'This is placed in the sidebar of the help pane.', 'admin-page-framework' ),
            )
        );
        
        /*
         * ( optional ) Add links in the plugin listing table. ( .../wp-admin/plugins.php )
         */
         $this->addLinkToPluginDescription( 
            "<a href='http://en.michaeluno.jp/donate'>Donate</a>",
            "<a href='https://github.com/michaeluno/admin-page-framework' title='Contribute to the GitHub repository!' >Repository</a>"
        );
        $this->addLinkToPluginTitle(
            "<a href='http://en.michaeluno.jp'>miunosoft</a>"
        );

    }
    
    /**
     * The pre-defined callback method that is triggered when the page loads.
     */     
    public function load_apf_builtin_field_types( $oAdminPage ) { // load_{page slug}
        
        /*
         * ( optional ) Create a form - To create a form in Admin Page Framework, you need two kinds of components: sections and fields.
         * A section groups fields and fields belong to a section. So a section needs to be created prior to fields.
         * Use the addSettingSections() method to create sections and use the addSettingFields() method to create fields.
         */
        /* Add setting sections */
        $this->addSettingSections(    
            'apf_builtin_field_types', // the target page slug
            array(
                'section_id' => 'text_fields', // avoid hyphen(dash), dots, and white spaces
                // 'page_slug' => 'apf_builtin_field_types', // <-- the method remembers the last used page slug and the tab slug so they can be omitted from the second parameter.
                'tab_slug' => 'textfields',
                'title' => __( 'Text Fields', 'admin-page-framework-demo' ),
                'description' => __( 'These are text type fields.', 'admin-page-framework-demo' ), // ( optional )
                'order' => 10, // ( optional ) - if you don't set this, an index will be assigned internally in the added order
            ),    
            array(
                'section_id' => 'selectors',
                'tab_slug' => 'selectors',
                'title' => __( 'Selectors and Checkboxes', 'admin-page-framework-demo' ),
                'description' => __( 'These are selector type options such as dropdown lists, radio buttons, and checkboxes.', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id' => 'sizes',
                // 'tab_slug' => 'selectors', // <-- similar to the page slug, if the tab slug is the same as the previous one, it can be omitted.
                'title' => __( 'Sizes', 'admin-page-framework-demo' ),
            ),     
            array(
                'section_id' => 'image_select',
                'tab_slug' => 'files', // the target tab slug wil lbe renewed 
                'title' => __( 'Image Selector', 'admin-page-framework-demo' ),
                'description' => __( 'Set an image url with jQuwey based image selector.', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id' => 'color_picker',
                'tab_slug' => 'misc',
                'title' => __( 'Colors', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id' => 'media_upload',
                'tab_slug' => 'files',
                'title' => __( 'Media Uploader', 'admin-page-framework-demo' ),
                'description' => __( 'Upload binary files in addition to images.', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id' => 'checklists',
                'tab_slug' => 'checklist',
                'title' => __( 'Checklists', 'admin-page-framework-demo' ),
                'description' => __( 'Post type and taxonomy checklists ( custom checkbox ).', 'admin-page-framework-demo' ),
            ),    
            array(
                'section_id' => 'hidden_field',
                'tab_slug' => 'misc',
                'title' => __( 'Hidden Fields', 'admin-page-framework-demo' ),
                'description' => __( 'These are hidden fields.', 'admin-page-framework-demo' ),
            ),     
            array(
                'section_id' => 'file_uploads',
                'tab_slug' => 'files',
                'title' => __( 'File Uploads', 'admin-page-framework-demo' ),
                'description' => __( 'These are upload fields. Check the <code>$_FILES</code> variable in the validation callback method that indicates the temporary location of the uploaded files.', 'admin-page-framework-demo' ),
            ),     
            array(
                'section_id' => 'submit_buttons',
                'tab_slug' => 'misc',
                'title' => __( 'Submit Buttons', 'admin-page-framework-demo' ),
                'description' => __( 'These are custom submit buttons.', 'admin-page-framework-demo' ),
            ),     
            array(
                'section_id' => 'verification',
                'tab_slug' => 'verification',
                'title' => __( 'Verify Submitted Data', 'admin-page-framework-demo' ),
                'description' => __( 'Show error messages when the user submits improper option value.', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id' => 'mixed_types',
                'tab_slug' => 'mixed_types',
                'title' => __( 'Mixed Field Types', 'admin-page-framework-demo' ),
                'description' => __( 'As of v3, it is possible to mix field types in one field on a per-ID basis.', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id' => 'section_title_field_type',
                'tab_slug' => 'sections',
                'title' => __( 'Section Title', 'admin-page-framework-demo' ),
                'description' => __( 'The <code>section_title</code> field type will be placed in the position of the section title if set. If not set, the set section title will be placed. Only one <code>section_title</code> field is allowed per section.', 'admin-page-framework-demo' ),
            ),     
            array(
                'section_id' => 'repeatable_sections',
                'tab_slug' => 'sections',
                'title' => __( 'Repeatable Sections', 'admin-page-framework-demo' ),
                'description' => __( 'As of v3, it is possible to repeat sections.', 'admin-page-framework-demo' ),
                'repeatable' =>    true, // this makes the section repeatable
            ),
            array(
                'section_id' => 'tabbed_sections_a',
                'section_tab_slug' => 'tabbed_sections',
                'title' => __( 'Section Tab A', 'admin-page-framework-demo' ),
                'description' => __( 'This is the first item of the tabbed section.', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id' => 'tabbed_sections_b',
                'title' => __( 'Section Tab B', 'admin-page-framework-demo' ),
                'description' => __( 'This is the second item of the tabbed section.', 'admin-page-framework-demo' ),
            ),     
            array(
                'section_id' => 'repeatable_tabbed_sections',
                'tab_slug' => 'sections',
                'section_tab_slug' => 'repeatable_tabbes_sections',
                'title' => __( 'Repeatable', 'admin-page-framework-demo' ),
                'description' => __( 'It is possible to tab repeatable sections.', 'admin-page-framework-demo' ),
                'repeatable' =>    true, // this makes the section repeatable
            ),     
            array(
                'section_tab_slug' => '', // reset the target section tab slug for the next call.
                
            ),
            array()
        );     
    
        /* Add setting fields - defined in a separate file as they are also used in the other built-in example page as well.*/
        include( APFDEMO_DIRNAME . '/example/builtin-field-examples.php' );
    
    }
        
    /*
     * Built-in Field Types Page
     * */
    public function do_apf_builtin_field_types() { // do_{page slug}
        submit_button();
    }
        
    /*
     * The sample page and the hidden page
     */
    public function do_apf_sample_page() {
        
        echo "<p>" . __( 'This is a sample page that has a link to a hidden page created by the framework.', 'admin-page-framework-demo' ) . "</p>";
        $sLinkToHiddenPage = $this->oUtil->getQueryAdminURL( array( 'page' => 'apf_hidden_page' ) );
        echo "<a href='{$sLinkToHiddenPage}'>" . __( 'Go to Hidden Page', 'admin-page-framework-demo' ). "</a>";
    
    }
    public function do_apf_hidden_page() {
        
        echo "<p>" . __( 'This is a hidden page.', 'admin-page-framework-demo' ) . "</p>";
        echo "<p>" . __( 'It is useful when you have a setting page that requires a proceeding page.', 'admin-page-framework-demo' ) . "</p>";
        $sLinkToGoBack = $this->oUtil->getQueryAdminURL( array( 'page' => 'apf_sample_page' ) );
        echo "<a href='{$sLinkToGoBack}'>" . __( 'Go Back', 'admin-page-framework-demo' ). "</a>";
        
    }
    
    
    /*
     * Validation Callbacks
     * */
    public function validation_APF_NetworkAdmin_verification_verify_text_field( $sNewInput, $sOldInput ) { // validation_{instantiated class name}_{section id}_{field id}
    
        /* 1. Set a flag. */
        $_bVerified = true;
        
        /* 2. Prepare an error array.
             We store values that have an error in an array and pass it to the setFieldErrors() method.
            It internally stores the error array in a temporary area of the database called transient.
            The used name of the transient is a md5 hash of 'instantiated class name' + '_' + 'page slug'. 
            The library class will search for this transient when it renders the form fields 
            and if it is found, it will display the error message set in the field array.     
        */
        $_aErrors = array();

        /* 3. Check if the submitted value meets your criteria. As an example, here a numeric value is expected. */
        if ( ! is_numeric( $sNewInput ) ) {
            
            // $variable[ 'sectioni_id' ]['field_id']
            $_aErrors['verification']['verify_text_field'] = __( 'The value must be numeric:', 'admin-page-framework-demo' ) . ' ' . $sNewInput;
            $_bVerified = false;
                    
        }
        
        /* 4. An invalid value is found. */
        if ( ! $_bVerified ) {
        
            /* 4-1. Set the error array for the input fields. */
            $this->setFieldErrors( $_aErrors );     
            $this->setSettingNotice( __( 'There was something wrong with your input.', 'admin-page-framework-demo' ) );
            return $sOldInput;
            
        }
                
        return $sNewInput;     
        
    }
    public function validation_apf_builtin_field_types_files( $aInput, $aOldPageOptions ) { // validation_{page slug}_{tab slug}

        /* Display the uploaded file information. */
        $aFileErrors = array();
        $aFileErrors[] = $_FILES[ $this->oProp->sOptionKey ]['error']['file_uploads']['file_single'];
        $aFileErrors[] = $_FILES[ $this->oProp->sOptionKey ]['error']['file_uploads']['file_multiple'][0];
        $aFileErrors[] = $_FILES[ $this->oProp->sOptionKey ]['error']['file_uploads']['file_multiple'][1];
        $aFileErrors[] = $_FILES[ $this->oProp->sOptionKey ]['error']['file_uploads']['file_multiple'][2];
        foreach( $_FILES[ $this->oProp->sOptionKey ]['error']['file_uploads']['file_repeatable'] as $aFile )
            $aFileErrors[] = $aFile;
            
        if ( in_array( 0, $aFileErrors ) ) 
            $this->setSettingNotice( __( '<h3>File(s) Uploaded</h3>', 'admin-page-framework-demo' ) . $this->oDebug->getArray( $_FILES ), 'updated' );
        
        return $aInput;
        
    }
    
    public function validation_APF_NetworkAdmin( $aInput, $aOldOptions ) { // validation_{instantiated class name}
        
        /* If the delete options button is pressed, return an empty array that will delete the entire options stored in the database. */
        if ( isset( $_POST[ $this->oProp->sOptionKey ]['submit_buttons_confirm']['submit_delete_options_confirmation'] ) ) { 
            return array();
        }
        return $aInput;
        
    }
                
}