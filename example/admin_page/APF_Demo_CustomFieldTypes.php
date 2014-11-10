<?php
class APF_Demo_CustomFieldTypes extends AdminPageFramework {

    /**
     * The start() method is called at the end of the constructor. [3.1.0+]
     * 
     * Alternatively you may use the 'start_{instantiated class name}()' method instead, which also called at the end of the constructor.
     * 
     */
    public function start() {         
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
                'title'         => __( 'Custom Field Types', 'admin-page-framework-demo' ),
                'page_slug'     => 'apf_custom_field_types',
                'screen_icon'   => 'options-general',
            )
        );
        
        /* ( optional ) Disable the automatic settings link in the plugin listing table. */    
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string.     
            
        // Include custom field type pages (in-page tabs).
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Date.php' );
        new APF_Demo_CustomFieldTypes_Date;
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Geometry.php' );
        new APF_Demo_CustomFieldTypes_Geometry;
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Dial.php' );
        new APF_Demo_CustomFieldTypes_Dial;
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Font.php' );
        new APF_Demo_CustomFieldTypes_Font;
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Sample.php' );
        new APF_Demo_CustomFieldTypes_Sample;
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Revealer.php' );
        new APF_Demo_CustomFieldTypes_Revealer;
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Grid.php' );
        new APF_Demo_CustomFieldTypes_Grid;
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_AutoComplete.php' );
        new APF_Demo_CustomFieldTypes_AutoComplete;
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Link.php' );
        new APF_Demo_CustomFieldTypes_Link;
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_GitHub.php' );
        new APF_Demo_CustomFieldTypes_GitHub;
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_ImageSelectors.php' );
        new APF_Demo_CustomFieldTypes_ImageSelectors;                
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_ACE.php' );
        new APF_Demo_CustomFieldTypes_ACE;

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
           
      
}