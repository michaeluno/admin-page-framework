<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Setting_Form' ) ) :
/**
 * Handles the submitted data of the framework's form.
 *  
 * @abstract
 * @since		3.0.0
 * @extends		AdminPageFramework_Menu
 * @package		AdminPageFramework
 * @subpackage	Page
 * @internal
 */
abstract class AdminPageFramework_Setting_Form extends AdminPageFramework_Setting_Base {
		
	/**
	 * Handles the form submitted data.
	 * 
	 * If the form is submitted, it calls the validation callback method and reloads the page.
	 * 
	 * @remark			This method is triggered when the page is about to be rendered.
	 * @since			3.1.0
	 */
	protected function _handleSubmittedData() {
		
		/*  The $_POST array will look like this:
			array(
				[option_page] => APF_Demo
				[action] => update
				[_wpnonce] => d3f9bd2fbc
				[_wp_http_referer] => /wp39x/wp-admin/edit.php?post_type=apf_posts&page=apf_builtin_field_types&tab=textfields
				[APF_Demo] => Array (
						[text_fields] => Array( ...)
					)

				[page_slug] => apf_builtin_field_types
				[tab_slug] => textfields
				[_is_admin_page_framework] => 1 
			)
		*/
		
		if ( 
			! isset( 
				// The framework specific keys
				$_POST['_is_admin_page_framework'], 
				$_POST['page_slug'], 
				$_POST['tab_slug'], 
				// The settings API fields keys
				$_POST['option_page'], 
				$_POST['action'], 
				$_POST['_wpnonce'],
				$_POST['_wp_http_referer']
			) 
		) {			
			return;
		}
		if ( wp_unslash( $_SERVER['REQUEST_URI'] ) != $_POST['_wp_http_referer'] ) {	// see the function definition of wp_referer_field() in functions.php.
			return;			
		}
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], $this->oProp->sOptionKey . '-options' ) ) {
			return;
		}

		
		// If only page-meta-boxes are used, it's possible that the option key element does not exist.
		$_aInput = isset( $_POST[ $this->oProp->sOptionKey ] ) ? $_POST[ $this->oProp->sOptionKey ] : array();
		$_aInput = $this->_doValidationCall( stripslashes_deep( $_aInput ) );
		$this->oProp->updateOption( $_aInput );
			// update_option( $this->oProp->sOptionKey, $_aInput );	// deprecated as of 3.1.0
		
		// Reload the page with the update notice.
		die( wp_redirect( $this->oUtil->getQueryAdminURL( array( 'settings-updated' => true ) ) ) );
		
	}
							

}
endif;