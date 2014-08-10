<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_PostType_Router' ) ) :
/**
 * Provides routing methods for the post type factory class.
 * 
 * @abstract
 * @since			3.0.4
 * @package			AdminPageFramework
 * @subpackage		PostType
 */
abstract class AdminPageFramework_PostType_Router extends AdminPageFramework_Factory {	
		
	/**
	 * Redirects undefined callback methods or to the appropriate methods.
	 * 
	 * @internal
	 */
	public function __call( $sMethodName, $aArgs=null ) {	
	
		if ( 'setup_pre' == $sMethodName ) { 
			$this->_setUp();
			$this->oProp->_bSetupLoaded = true;
			return;
		}
		if ( substr( $sMethodName, 0, strlen( "cell_" ) ) == "cell_" ) return $aArgs[0];
		if ( substr( $sMethodName, 0, strlen( "sortable_columns_" ) ) == "sortable_columns_" ) return $aArgs[0];
		if ( substr( $sMethodName, 0, strlen( "columns_" ) ) == "columns_" ) return $aArgs[0];
		if ( substr( $sMethodName, 0, strlen( "style_ie_common_" ) )== "style_ie_common_" ) return $aArgs[0];
		if ( substr( $sMethodName, 0, strlen( "style_common_" ) )== "style_common_" ) return $aArgs[0];
		if ( substr( $sMethodName, 0, strlen( "style_ie_" ) )== "style_ie_" ) return $aArgs[0];
		if ( substr( $sMethodName, 0, strlen( "style_" ) )== "style_" ) return $aArgs[0];
	
		parent::__call( $sMethodName, $aArgs );
				
	}
	
}
endif;