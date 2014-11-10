<?php
/**
 * This example demonstrates the use of the set_up_{instantiated class name} hook.
 * 
 * Note that this class does not extend any class unlike the other admin page classes in the demo plugin examples.
 */
class APF_Demo_HiddenPage {
    
    /**
     * Sets up hooks.
     */
    public function __construct() {
        
        // set_up_{instantiated class name} - 'APF_Demo' is the class name of the main class.
        add_action( "set_up_" . "APF_Demo", array( $this, 'replyToSetUpPages' ) );
        add_action( "do_" . "apf_sample_page", array( $this, 'replyToModifySamplePage' ) );
        add_action( "do_" . "apf_hidden_page", array( $this, 'replyToModifyHiddenPage' ) );
        
    }
    
    /**
     * Sets up pages.
     */
    public function replyToSetUpPages( $oAdminPage ) {    
    
        /* ( required ) Add sub-menu items (pages or links) */
        $oAdminPage->addSubMenuItems(     
            array(
                'title'         => __( 'Sample Page', 'admin-page-framework-demo' ),
                'page_slug'     => 'apf_sample_page',
                'screen_icon'   => dirname( APFDEMO_FILE ) . '/asset/image/wp_logo_bw_32x32.png', // ( for WP v3.7.1 or below ) the icon _file path_ can be used
            ),     
            array(
                'title'         => __( 'Hidden Page', 'admin-page-framework-demo' ),
                'page_slug'     => 'apf_hidden_page',
                'screen_icon'   => version_compare( $GLOBALS['wp_version'], '3.8', '<' ) 
                    ? plugins_url( 'asset/image/wp_logo_bw_32x32.png', APFDEMO_FILE )
                    : null, // ( for WP v3.7.1 or below ) 
                'show_in_menu' => false,
            )
        );
                    
    }
    
    /*
     * The sample page and the hidden page
     */
    public function replyToModifySamplePage( $oAdminPage ) {
        
        echo "<p>" . __( 'This is a sample page that has a link to a hidden page created by the framework.', 'admin-page-framework-demo' ) . "</p>";
        $_sLinkToHiddenPage = esc_url( $oAdminPage->oUtil->getQueryAdminURL( array( 'page' => 'apf_hidden_page' ) ) );
        echo "<a href='{$_sLinkToHiddenPage}'>" . __( 'Go to Hidden Page', 'admin-page-framework-demo' ). "</a>";
    
    }
    public function replyToModifyHiddenPage( $oAdminPage ) {
        
        echo "<p>" . __( 'This is a hidden page.', 'admin-page-framework-demo' ) . "</p>";
        echo "<p>" . __( 'It is useful when you have a setting page that requires a proceeding page.', 'admin-page-framework-demo' ) . "</p>";
        $_sLinkToGoBack = esc_url( $oAdminPage->oUtil->getQueryAdminURL( array( 'page' => 'apf_sample_page' ) ) );
        echo "<a href='{$_sLinkToGoBack}'>" . __( 'Go Back', 'admin-page-framework-demo' ). "</a>";
        
        // Let's do something here. 
        // Fetch posts of the custom post type of this demo plugin.        
        echo "<h3>" . __( 'Query Posts by Custom Meta Value', 'admin-page-framework-demo' ) . "</h3>";
        echo "<p>" . __( 'Here we are going to retrieve posts of the demo plugin\'s custom post type.', 'admin-page-framework-demo' ) . "</p>"; // 'syntax fixer
        $_aArgs = array(
            'post_type'         => 'apf_posts', // the post type slug used for the demo plugin
            'posts_per_page'    => 100,          // retrieve 100 posts. Set -1 for all.
            'post_status'       => 'publish',   // published post
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'       => '_my_custom_date_timestamp',          // the field ID
                    'value'     => date( 'Y/m/d' ) - 24*60*60*7,        // seven days ago
                    'compare'   => '>=',    // newer than seven days 
                )
            ),
        );
        $_oResult = new WP_Query( $_aArgs );      

        echo "<p>" . sprintf( __( 'Found %1$s post(s).', 'admin-page-framework-demo' ), $_oResult->post_count ) . "</p>";
        if ( ! $_oResult->post_count ) {
            return;
        }
        echo "<ul>";
        echo "<li>" 
                . "<strong>" . __( 'Date', 'admin-page-framework-demo' ) . "</strong>, "
                . "<strong>" . __( 'Post ID', 'admin-page-framework-demo' ) . "</strong>, " 
                . "<strong>" . __( 'Title', 'admin-page-framework-demo' ) . "</strong>"
            . "</li>";        
        foreach( $_oResult->posts as $_oPost ) {
            echo "<li>" 
                    . get_post_meta( $_oPost->ID, 'my_custom_date', true ) . ", "
                    . $_oPost->ID . ", " 
                    . $_oPost->post_title 
                . "</li>";
        }
        echo "</ul>";

    }    

}