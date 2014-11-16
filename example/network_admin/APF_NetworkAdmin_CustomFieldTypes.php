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

class APF_NetworkAdmin_CustomFieldTypes extends AdminPageFramework_NetworkAdmin {

    /**
     * Triggered at the end of the constructor.
     * 
     * Alternatively you may use the start_{instantiated class name} predefined callback method.
     */
    public function start() {}

    public function setUp() { // this method automatically gets triggered with the wp_loaded hook. 

        /* ( optional ) this can be set via the constructor. For available values, see https://codex.wordpress.org/Roles_and_Capabilities */
        $this->setCapability( 'read' );
        
        /* ( required ) Set the root page */
        $this->setRootMenuPageBySlug( 'APF_NetworkAdmin' );    

        /* ( required ) Add sub-menu items (pages or links) */
        $this->addSubMenuItems(    
            array(
                'title' => __( 'Custom Field Types', 'admin-page-framework-demo' ),
                'page_slug' => 'apf_custom_field_types',
                'screen_icon' => 'options-general',
            )
        );     
                

        /* ( optional ) Determine the page style */
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs

        // Include custom field type pages (in-page tabs).
        $_sClassName = get_class( $this );
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Date.php' );
        new APF_Demo_CustomFieldTypes_Date( $_sClassName );
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Geometry.php' );
        new APF_Demo_CustomFieldTypes_Geometry( $_sClassName );
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Dial.php' );
        new APF_Demo_CustomFieldTypes_Dial( $_sClassName );
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Font.php' );
        new APF_Demo_CustomFieldTypes_Font( $_sClassName );
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Sample.php' );
        new APF_Demo_CustomFieldTypes_Sample( $_sClassName );
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Revealer.php' );
        new APF_Demo_CustomFieldTypes_Revealer( $_sClassName );
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Grid.php' );
        new APF_Demo_CustomFieldTypes_Grid( $_sClassName );
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_AutoComplete.php' );
        new APF_Demo_CustomFieldTypes_AutoComplete( $_sClassName );
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_Link.php' );
        new APF_Demo_CustomFieldTypes_Link( $_sClassName );
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_GitHub.php' );
        new APF_Demo_CustomFieldTypes_GitHub( $_sClassName );
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_ImageSelectors.php' );
        new APF_Demo_CustomFieldTypes_ImageSelectors( $_sClassName );
        include( APFDEMO_DIRNAME . '/example/admin_page/custom_field_type/APF_Demo_CustomFieldTypes_ACE.php' );
        new APF_Demo_CustomFieldTypes_ACE( $_sClassName );
                
    }
        
}