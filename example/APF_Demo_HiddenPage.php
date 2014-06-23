<?php
class APF_Demo_HiddenPage extends AdminPageFramework {

	public function setUp() {	// this method automatically gets triggered with the wp_loaded hook. 

		/* ( optional ) this can be set via the constructor. For available values, see https://codex.wordpress.org/Roles_and_Capabilities */
		$this->setCapability( 'read' );
		
		/* ( required ) Set the root page */
		$this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );	
		
		/* ( required ) Add sub-menu items (pages or links) */
		$this->addSubMenuItems(					
			array(
				'title'	=>	__( 'Sample Page', 'admin-page-framework-demo' ),
				'page_slug'	=>	'apf_sample_page',
				'screen_icon'	=>	dirname( APFDEMO_FILE ) . '/asset/image/wp_logo_bw_32x32.png',	// ( for WP v3.7.1 or below ) the icon _file path_ can be used
			),					
			array(
				'title'	=>	__( 'Hidden Page', 'admin-page-framework-demo' ),
				'page_slug'	=>	'apf_hidden_page',
				'screen_icon'	=>	plugins_url( 'asset/image/wp_logo_bw_32x32.png', APFDEMO_FILE ),	// ( for WP v3.7.1 or below ) the icon _url_ can be used
				'show_in_menu'	=>	false,
			)
		);
			
		/* ( optional ) Determine the page style */
		$this->setPageHeadingTabsVisibility( false );	// disables the page heading tabs by passing false.
		$this->setInPageTabTag( 'h2' );		// sets the tag used for in-page tabs		

		
	}
	
	/*
	 * The sample page and the hidden page
	 */
	public function do_apf_sample_page() {
		
		echo "<p>" . __( 'This is a sample page that has a link to a hidden page created by the framework.', 'admin-page-framework-demo' ) . "</p>";
		$sLinkToHiddenPage = $this->oUtil->getQueryAdminURL( array( 'page'	=>	'apf_hidden_page' ) );
		echo "<a href='{$sLinkToHiddenPage}'>" . __( 'Go to Hidden Page', 'admin-page-framework-demo' ). "</a>";
	
	}
	public function do_apf_hidden_page() {
		
		echo "<p>" . __( 'This is a hidden page.', 'admin-page-framework-demo' ) . "</p>";
		echo "<p>" . __( 'It is useful when you have a setting page that requires a proceeding page.', 'admin-page-framework-demo' ) . "</p>";
		$sLinkToGoBack = $this->oUtil->getQueryAdminURL( array( 'page'	=>	'apf_sample_page' ) );
		echo "<a href='{$sLinkToGoBack}'>" . __( 'Go Back', 'admin-page-framework-demo' ). "</a>";
		
	}	
		

}