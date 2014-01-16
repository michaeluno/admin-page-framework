<?php
class APF_MetaBox_For_Pages_Side extends AdminPageFramework_MetaBox_Page {
		
	/*
	 * ( optional ) Use the setUp() method to define settings of this meta box.
	 */
	public function setUp() {
		
		/*
		 * ( optional ) Adds setting fields into the meta box.
		 */
		$this->addSettingFields(
			array (
				'field_id'		=> 'color_field',
				'type'			=> 'color',
				'title'			=> __( 'Color', 'admin-page-framework-demo' ),
				// 'show_title_column'	=> false,
			),
			array (
				'field_id'		=> 'size_field',
				'type'			=> 'size',
				'title'			=> __( 'Size', 'admin-page-framework-demo' ),
				'default'		=> array( 'size' => 5, 'unit' => '%' ),
				// 'show_title_column'	=> false,
			),
			array(
				'field_id'		=>	'submit_in_meta_box',
				'type'			=>	'submit',
				// 'show_title_column'	=> false,
			)
		);
		
	}
	
	public function do_apf_metabox_for_pages_side() {	// do_{metabox id}
		?>
			<p><?php _e( 'This is a side meta box. This is inserted with the <code>do_{metabox id}</code> hook.', 'admin-page-framework-demo' ) ?></p>
		<?php
		
	}

	
}