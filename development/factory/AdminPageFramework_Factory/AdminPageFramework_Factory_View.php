<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Factory_View' ) ) :
/**
 * Provides methods for views.
 * 
 * @abstract
 * @since			3.0.4
 * @subpackage		Factory
 * @internal
 */
abstract class AdminPageFramework_Factory_View extends AdminPageFramework_Factory_Model {
	
	/**
	 * Returns the field output from the given field definition array.
	 * 
	 * @since			3.0.0
	 */
	public function _replyToGetFieldOutput( $aField ) {

		$_oField = new AdminPageFramework_FormField( $aField, $this->oProp->aOptions, array(), $this->oProp->aFieldTypeDefinitions, $this->oMsg );	// currently the error array is not supported for meta-boxes		
		return $this->oUtil->addAndApplyFilters(
			$this,
			array( 	'field_' . $this->oProp->sClassName . '_' . $aField['field_id'] ),	// field_ + {extended class name} + _ {field id}
			$_oField->_getFieldOutput(),	// field output
			$aField // the field array
		);		
						
	}	
	
	
}
endif;