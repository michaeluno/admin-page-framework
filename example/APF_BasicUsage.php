<?php
class APF_BasicUsage extends AdminPageFramework {
	
	public function setUp() {
		
		$this->setRootMenuPage( 
			'Admin Page Framework',
			version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 'dashicons-format-audio' : null	// dash-icons are supported since WordPress v3.8
		);
		$this->addSubMenuItems(
			array(
				'title' => __( 'First Page', 'admin-page-framework-demo' ),
				'page_slug' => 'apf_first_page',
			),
			array(
				'title' => __( 'Second Page', 'admin-page-framework-demo' ),
				'page_slug' => 'apf_second_page',
			)
		);
		
		$this->setPageHeadingTabsVisibility( true );		// disables the page heading tabs by passing false.
	}	
	
	public function do_apf_first_page() {	// do_ + {page slug}
		?>
			<h3><?php _e( 'do_ + {...} Action Hooks', 'admin-page-framework-demo' ); ?></h3>
			<p><?php _e( 'Hi there! This text is inserted by the <code>do_{page slug}</code> action hook and the callback method.', 'admin-page-framework-demo' ); ?></p>
		<?php

	}
	
	public function content_apf_second_page( $sContent ) {	// content_ + {page slug}
		
		return $sContent 
			. "<h3>" . __( 'content_ + {...} Filter Hooks', 'admin-page-framework-demo' ) . "</h3>"
			. "<p>" 
				. __( 'This message is inserted by the <code>content_{page slug}</code> filter.', 'admin-page-framework-demo' ) 
			. "</p>"
			. "<h3>" . __( 'Saved Options', 'admin-page-framework-demo' ) . "</h3>"
			. $this->oDebug->getArray( $this->oProp->aOptions ); 
			
	}
	
}