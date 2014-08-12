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
				'field_id'			=>	'color_field',
				'type'				=>	'color',
				'title'				=>	__( 'Color', 'admin-page-framework-demo' ),
			),
			array (
				'field_id'			=>	'size_field',
				'type'				=>	'size',
				'title'				=>	__( 'Size', 'admin-page-framework-demo' ),
				'default'			=>	array( 'size' => 5, 'unit' => '%' ),
			),
			array(
				'field_id'			=>	'submit_in_meta_box',
				'type'				=>	'submit',
				'show_title_column'	=>	false,
				'label_min_width'	=>	0,
				'attributes'		=>	array(
					'fieldset'	=>	array(
						'style'	=>	'float:right;',
					),
				),
			),
			array()
		);
		
	}
	
	public function do_APF_MetaBox_For_Pages_Side() {	// do_{extended class name}
		?>
			<p><?php _e( 'This is a side meta box. This is inserted with the <code>do_{extended class name}</code> hook.', 'admin-page-framework-demo' ) ?></p>
		<?php
		
	}

	public function validation_APF_MetaBox_For_Pages_Side( $aNewOptions, $aOldOptions ) { // validation_{extended class name}

		// Do something with the submitted values.
		return $aNewOptions;
		
	}
	
}