<?php
class APF_PostType extends AdminPageFramework_PostType {
	
	public function start_APF_PostType() {	// start_ + extended class name
	
		// the setUp() method is too late to add taxonomies. So we use start_{class name} action hook.
	
		$this->setAutoSave( false );
		$this->setAuthorTableFilter( true );
		$this->addTaxonomy( 
			'apf_sample_taxonomy', // taxonomy slug
			array(			// argument - for the argument array keys, refer to : http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
				'labels' => array(
					'name' => 'Sample Genre',
					'add_new_item' => 'Add New Genre',
					'new_item_name' => "New Genre"
				),
				'show_ui' => true,
				'show_tagcloud' => false,
				'hierarchical' => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'show_table_filter' => true,	// framework specific key
				'show_in_sidebar_menus' => true,	// framework specific key
			)
		);
		$this->addTaxonomy( 
			'apf_second_taxonomy', 
			array(
				'labels' => array(
					'name' => 'Non Hierarchical',
					'add_new_item' => 'Add New Taxonomy',
					'new_item_name' => "New Sample Taxonomy"
				),
				'show_ui' => true,
				'show_tagcloud' => false,
				'hierarchical' => false,
				'show_admin_column' => true,
				'show_in_nav_menus' => false,
				'show_table_filter' => true,	// framework specific key
				'show_in_sidebar_menus' => false,	// framework specific key
			)
		);

		$this->setFooterInfoLeft( '<br />Custom Text on the left hand side.' );
		$this->setFooterInfoRight( '<br />Custom text on the right hand side' );
		
		
		
		add_filter( 'the_content', array( $this, 'replyToPrintOptionValues' ) );
		
	
	}
	
	/*
	 * Extensible methods
	 */
	public function setColumnHeader( $aColumnHeader ) {
		$aColumnHeaders = array(
			'cb'			=> '<input type="checkbox" />',	// Checkbox for bulk actions. 
			'title'			=> __( 'Title', 'admin-page-framework' ),		// Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
			'author'		=> __( 'Author', 'admin-page-framework' ),		// Post author.
			// 'categories'	=> __( 'Categories', 'admin-page-framework' ),	// Categories the post belongs to. 
			// 'tags'		=> __( 'Tags', 'admin-page-framework' ),	// Tags for the post. 
			'comments' 		=> '<div class="comment-grey-bubble"></div>', // Number of pending comments. 
			'date'			=> __( 'Date', 'admin-page-framework' ), 	// The date and publish status of the post. 
			'samplecolumn'			=> __( 'Sample Column' ),
		);		
		return array_merge( $aColumnHeader, $aColumnHeaders );
	}
	// public function setSortableColumns( $aColumns ) {
		// return array_merge( $aColumns, $this->oProp->aColumnSortable );		
	// }	
	
	/*
	 * Callback methods
	 */
	public function cell_apf_posts_samplecolumn( $sCell, $iPostID ) {	// cell_ + post type + column key
		
		return "the post id is : {$iPostID}";
		
	}
	
	public function replyToPrintOptionValues( $sContent ) {
		
		if ( ! isset( $GLOBALS['post']->ID ) || get_post_type() != 'apf_posts' ) return $sContent;
			
		// 1. To retrieve the meta box data	- get_post_meta( $post->ID ) will return an array of all the meta field values.
		// or if you know the field id of the value you want, you can do $value = get_post_meta( $post->ID, $field_id, true );
		$iPostID = $GLOBALS['post']->ID;
		$aPostData = array();
		foreach( ( array ) get_post_custom_keys( $iPostID ) as $sKey ) 	// This way, array will be unserialized; easier to view.
			$aPostData[ $sKey ] = get_post_meta( $iPostID, $sKey, true );
		
		// 2. To retrieve the saved options in the setting pages created by the framework - use the get_option() function.
		// The key name is the class name by default. This can be changed by passing an arbitrary string 
		// to the first parameter of the constructor of the AdminPageFramework class.		
		$aSavedOptions = get_option( 'APF_Demo' );
			
		return "<h3>" . __( 'Saved Meta Field Values', 'admin-page-framework-demo' ) . "</h3>" 
			. $this->oDebug->getArray( $aPostData )
			. "<h3>" . __( 'Saved Setting Options', 'admin-page-framework-demo' ) . "</h3>" 
			. $this->oDebug->getArray( $aSavedOptions );

	}	
	
}