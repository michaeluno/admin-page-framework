<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Property_NetworkAdmin' ) ) :
/**
 * Stores properties of a network admin object.
 * 
 * @since			3.1.0
 * @package			AdminPageFramework
 * @subpackage		Property
 * @extends			AdminPageFramework_Property_Page
 * @internal
 */
class AdminPageFramework_Property_NetworkAdmin extends AdminPageFramework_Property_Page {
	
	/**
	 * Defines the property type.
	 * 
	 * @since			3.1.0
	 * @internal
	 */
	public $_sPropertyType = 'network_admin_page';
	
	/**
	 * Defines the fields type.
	 * 
	 * @since			3.1.0
	 */
	public $sFieldsType = 'network_admin_page';
	
	
	/*
	 * Magic methods
	 * */
	public function &__get( $sName ) {
		
		// If $this->aOptions is called for the first time, retrieve the option data from the database and assign to the property.
		// Once this is done, calling $this->aOptions will not trigger the __get() magic method any more.
		// Without the the ampersand in the method name, it causes a PHP warning.
		if ( $sName == 'aOptions' ) {
			$this->aOptions = get_site_option( $this->sOptionKey, array() );
			return $this->aOptions;	
		}
		
		// For regular undefined items, 
		return null;
		
	}	
	
	/**
	 * Utility methods
	 */
	/**
	 * Saves the options into the database.
	 * 
	 * @since			3.1.0
	 */
	public function updateOption( $aOptions=null ) {
		update_site_option( $this->sOptionKey, $aOptions !== null ? $aOptions : $this->aOptions );
	}	
	
}
endif;