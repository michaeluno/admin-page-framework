<?php
class APF_Demo_Readme extends AdminPageFramework {

	/**
	 * Sets up pages.
	 * 
	 * This method automatically gets triggered with the wp_loaded hook. 
	 */
	public function setUp() {

		/* ( optional ) this can be set via the constructor. For available values, see https://codex.wordpress.org/Roles_and_Capabilities */
		$this->setCapability( 'read' );
		
		/* ( required ) Set the root page */
		$this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );	
		
		/* ( required ) Add sub-menu items (pages or links) */
		$this->addSubMenuItems(					
			array(
				'title'					=>	__( 'Read Me', 'admin-page-framework-demo' ),
				'page_slug'				=>	'apf_read_me',
				'screen_icon'			=>	'page',
			),			
			array(
				'title'					=>	__( 'Documentation', 'admin-page-framework-demo' ),
				'href'					=>	plugins_url( 'document/class-AdminPageFramework.html', APFDEMO_FILE ),
				'show_page_heading_tab'	=>	false,
			)
		);

		$this->addInPageTabs(	// ( optional )
			'apf_read_me',
			array(
				'tab_slug'	=>	'description',
				'title'		=>	__( 'Description', 'admin-page-framework-demo' ),
			),				
			array(
				'tab_slug'	=>	'installation',
				'title'		=>	__( 'Installation', 'admin-page-framework-demo' ),
			),	
			array(
				'tab_slug'	=>	'frequently_asked_questions',
				'title'		=>	__( 'FAQ', 'admin-page-framework-demo' ),
			),		
			array(
				'tab_slug'	=>	'other_notes',
				'title'		=>	__( 'Other Notes', 'admin-page-framework-demo' ),
			),					
			array(
				'tab_slug'	=>	'changelog',
				'title'		=>	__( 'Change Log', 'admin-page-framework-demo' ),
			)
		);					

		/* 
		 * ( optional ) Enqueue styles  
		 * $this->enqueueStyle(  'stylesheet url/path' , 'page slug (optional)', 'tab slug (optional)', 'custom argument array(optional)' );
		 * */		
		$this->enqueueStyle(  plugins_url( 'asset/css/readme.css' , APFDEMO_FILE ) , 'apf_read_me' );	// a url can be used as well
		
		/* ( optional ) Determine the page style */
		$this->setPageHeadingTabsVisibility( false );	// disables the page heading tabs by passing false.
		$this->setInPageTabTag( 'h2' );		// sets the tag used for in-page tabs		
		$this->setPageTitleVisibility( false, 'apf_read_me' );	// disable the page title of a specific page.
		// $this->setInPageTabsVisibility( false, 'apf_read_me' );	// in-page tabs can be disabled like so.

		/* ( optional ) Disable the automatic settings link in the plugin listing table. */	
		$this->setPluginSettingsLinkLabel( '' );	// pass an empty string.
		
	}

	public function do_before_apf_read_me() {		// do_before_ + page slug 

		include( dirname( APFDEMO_FILE ) . '/third-party/wordpress-plugin-readme-parser/parse-readme.php' );
		$this->oWPReadMe	= new WordPress_Readme_Parser;
		$this->aWPReadMe	= $this->oWPReadMe->parse_readme( dirname( APFDEMO_FILE ) . '/readme.txt' );
	
	}
	public function do_apf_read_me_description() {		// do_ + page slug + _ + tab slug
		
		echo $this->aWPReadMe['sections']['description'];
		
	}
	public function do_apf_read_me_installation() {		// do_ + page slug + _ + tab slug
		
		echo $this->aWPReadMe['sections']['installation'];
		
	}
	public function do_apf_read_me_frequently_asked_questions() {	// do_ + page slug + _ + tab slug
		
		echo $this->aWPReadMe['sections']['frequently_asked_questions'];
		
	}
	public function do_apf_read_me_other_notes() {
		
		echo $this->aWPReadMe['remaining_content'];
		
	}
	public function do_apf_read_me_screenshots() {		// do_ + page slug + _ + tab slug
		
		echo $this->aWPReadMe['sections']['screenshots'];
		
	}	
	public function do_apf_read_me_changelog() {		// do_ + page slug + _ + tab slug
		
		echo $this->aWPReadMe['sections']['changelog'];
		
		$_aChangeLog	= $this->oWPReadMe->parse_readme( dirname( APFDEMO_FILE ) . '/changelog.md' );
		echo $_aChangeLog['sections']['changelog'];
	
	}
		

}