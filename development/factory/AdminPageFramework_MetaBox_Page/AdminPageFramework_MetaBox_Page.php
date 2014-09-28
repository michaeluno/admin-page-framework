<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_MetaBox_Page' ) ) :
/**
 * Provides methods for creating meta boxes in pages added by the framework.
 *
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 *     <li><strong>start_{instantiated class name}</strong> – triggered at the end of the class constructor. This receives the class object in the first parameter.</li>
 *     <li><strong>set_up{instantiated class name}</strong> – triggered afther the setUp() method is called. This receives the class object in the first parameter.</li>
 *     <li><strong>do_{instantiated class name}</strong> – triggered when the meta box gets rendered. The first parameter: the class object [3.1.3+].</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 *     <li><strong>field_types_{instantiated class name}</strong> – receives the field type definition array. The first parameter: the field type definition array.</li>
 *     <li><strong>field_{instantiated class name}_{field ID}</strong> – receives the form input field output of the given input field ID. The first parameter: output string. The second parameter: the array of option.</li>
 *     <li><strong>content_{instantiated class name}</strong> – receives the entire output of the meta box. The first parameter: the output HTML string.</li>
 *     <li><strong>style_common_admin_page_framework</strong> –  [3.2.1+] receives the output of the base CSS rules applied to common CSS rules shared by the framework.</li>
 *     <li><strong>style_common_{instantiated class name}</strong> –  receives the output of the base CSS rules applied to the pages of the associated post types with the meta box.</li>
 *     <li><strong>style_ie_common_{instantiated class name}</strong> –  receives the output of the base CSS rules for Internet Explorer applied to the pages of the associated post types with the meta box.</li>
 *     <li><strong>style_{instantiated class name}</strong> –  receives the output of the CSS rules applied to the pages of the associated post types with the meta box.</li>
 *     <li><strong>style_ie_{instantiated class name}</strong> –  receives the output of the CSS rules for Internet Explorer applied to the pages of the associated post types with the meta box.</li>
 *     <li><strong>script_common_{instantiated class name}</strong> – receives the output of the base JavaScript scripts applied to the pages of the associated post types with the meta box.</li>
 *     <li><strong>script_{instantiated class name}</strong> – receives the output of the JavaScript scripts applied to the pages of the associated post types with the meta box.</li>
 *     <li><strong>validation_{instantiated class name}</strong> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * </ul>
 * 
 * @abstract
 * @since 3.0.0
 * @use AdminPageFramework_Utility
 * @use AdminPageFramework_Message
 * @use AdminPageFramework_Debug
 * @use AdminPageFramework_Property_Page
 * @package AdminPageFramework
 * @subpackage PageMetaBox
 */
abstract class AdminPageFramework_MetaBox_Page extends AdminPageFramework_MetaBox_Page_Controller {
    
    /**
     * Registers necessary hooks and internal properties.
     * 
     * <h4>Examples</h4>
     * <code>
     *     new APF_MetaBox_For_Pages_Normal(
     *         'apf_metabox_for_pages_normal', // meta box id
     *         __( 'Sample Meta Box For Admin Pages Inserted in Normal Area' ), // title
     *         'apf_first_page', // page slugs
     *         'normal', // context
     *         'default' // priority
     *     );
     *     include( APFDEMO_DIRNAME . '/example/APF_MetaBox_For_Pages_Advanced.php' );
     *     new APF_MetaBox_For_Pages_Advanced(
     *         'apf_metabox_for_pages_advanced', // meta box id
     *         __( 'Sample Meta Box For Admin Pages Inserted in Advanced Area' ), // title
     *         'apf_first_page', // page slugs
     *         'advanced', // context
     *         'default' // priority
     *     );    
     *     include( APFDEMO_DIRNAME . '/example/APF_MetaBox_For_Pages_Side.php' );
     *     new APF_MetaBox_For_Pages_Side(
     *         'apf_metabox_for_pages_side', // meta box id
     *         __( 'Sample Meta Box For Admin Pages Inserted in Advanced Area' ), // title
     *         array( 'apf_first_page', 'apf_second_page' ), // page slugs - setting multiple slugs is possible
     *         'side', // context
     *         'default' // priority
     *     );     
     * </code>
     * @since 3.0.0
     * 
     * @param string $sMetaBoxID The meta box ID to be created.
     * @param string $sTitle The meta box title.
     * @param array|string $asPageSlugs     the page slug(s) that the meta box belongs to. If the element is an array, it will be considered as a tab array.
     * <code>
        $asPageSlugs = array(     
            'settings' => array(     // if the key is not numeric and the value is an array, it will be considered as a tab array.
                'help',         // enabled in the tab whose slug is 'help' which belongs to the page whose slug is 'settings'
                'about',         // enabled in the tab whose slug is 'about' which belongs to the page whose slug is 'settings'
                'general', // enabled in the tab whose slug is 'general' which belongs to the page whose slug is 'settings'
            ),
            'manage', // if the numeric key with a string value is given, the condition applies to the page slug of this string value.
        );
     * </code>
     * @param string $sContext The context, either 'normal', 'advanced', or 'side'.
     * @param string $sPriority The priority, either 'high', 'core', 'default' or 'low'.
     * @param string $sCapability The capability. See <a href="https://codex.wordpress.org/Roles_and_Capabilities" target="_blank">Roles and Capabilities</a>.
     */
    function __construct( $sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {     
        
        if ( empty( $asPageSlugs ) ) { return; }
        
        if ( ! $this->_isInstantiatable() ) { return; }
                
        parent::__construct( $sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain );
                    
    }
                
}
endif;