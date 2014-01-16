<?php
class APF_MetaBox_For_Pages_Normal extends AdminPageFramework_MetaBox_Page {
		
	/*
	 * ( optional ) Use the setUp() method to define settings of this meta box.
	 */
	public function setUp() {

		/*
		 * ( optional ) Adds setting fields into the meta box.
		 */
		$this->addSettingFields(
			array(
				'field_id'		=> 'metabox_text_field',
				'type'			=> 'text',
				'title'			=> __( 'Text Input', 'admin-page-framework-demo' ),
				'description'	=> __( 'The description for the field.', 'admin-page-framework-demo' ),
				'help'			=> 'This is help text.',
				'help_aside'	=> 'This is additional help text which goes to the side bar of the help pane.',
			),
			array(
				'field_id'		=> 'metabox_text_field_repeatable',
				'type'			=> 'text',
				'title'			=> __( 'Text Repeatable', 'admin-page-framework-demo' ),
				'repeatable'	=>	true
			),			
			array(
				'field_id'		=> 'metabox_textarea_field',
				'type'			=> 'textarea',
				'title'			=> __( 'Text Area', 'admin-page-framework-demo' ),
				'description'	=> __( 'The description for the field.', 'admin-page-framework-demo' ),
				'help'			=> __( 'This a <em>text area</em> input field, which is larger than the <em>text</em> input field.', 'admin-page-framework-demo' ),
				'default'		=> __( 'This is a default text.', 'admin-page-framework-demo' ),
				'attributes'	=>	array(
					'cols'	=>	40,				
				),
			)
		);			
		
	}
	
	/*
	 * ( optional ) Use this method to insert your custom text.
	 */
	public function do_apf_metabox_for_pages_normal() {	// do_{meta box id}
		?>
			<p><?php _e( 'This meta box is placed with the <code>normal</code>context and this text is inserted with the <code>do_{metabox id}</code> hook.', 'admin-page-framework-demo' ) ?></p>
		<?php
		
	}

	
}