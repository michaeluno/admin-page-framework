<?php
class APF_Demo_CustomFieldTypes_AutoComplete {
    
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
    public $sTabSlug    = 'autocomplete';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'autocomplete';
    
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
            
            include( dirname( APFDEMO_FILE ) . '/third-party/autocomplete-custom-field-type/AutocompleteCustomFieldType.php' );
            new AutocompleteCustomFieldType( $sClassName );
                        
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
                'title'     => __( 'Autocomplete', 'admin-page-framework-demo' ),    
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
                'title'         => __( 'Autocomplete Custom Field Type', 'admin-page-framework-demo' ),
                'description'   => __( 'This field will show predefined list when the user type something on the input field.', 'admin-page-framework-demo' ),     
            )
        );        
        
        // Fields
        $oAdminPage->addSettingFields(
            // The 'Autocomplete' custom field type - the settings are the same as the tokeninput jQuery plugin.
            // see: http://loopj.com/jquery-tokeninput/
            // For the first parameter, use the 'settings' key and the second parameter, use the 'settings2'.
            $this->sSectionID, // the target section id
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
                'description' => __( 'To set a custom post type, you need to construct the query url. This field is for the titles of this demo plugin\'s custom post type.', 'admin-page-framework-demo' ), //' syntax fixer
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
                'description' => __( 'To search from multiple post types use the <code>post_types</code> argument (not <code>post_type</code>) and pass comma delimited post type slugs.', 'admin-page-framework-demo' ), 
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
                'description'   => __( 'To search users, pass the <code>user</code> to the <code>type</code> argument.', 'admin-page-framework-demo' ), 
            )
        );            
       

    }
    
    /**
     * Inserts an output into the page.
     */
    public function replyToInsertOutput() {
        submit_button();
    }
        
}