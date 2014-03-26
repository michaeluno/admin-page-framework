<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FieldTypeRegistration' ) ) :
/**
 * Provides means to define custom input fields not only by the framework but also by the user.
 * 
 * @package			AdminPageFramework
 * @subpackage		Form
 * @since			2.1.5
 * @since			2.1.6			Changed the name from AdminPageFramework_FieldTypeDefinitions
 * @internal
 */
class AdminPageFramework_FieldTypeRegistration  {
	
	/**
	 * Holds the default input field labels
	 * 
	 * @since			2.1.5
	 */
	protected static $aDefaultFieldTypeSlugs = array(
		'default', // undefined ones will be applied 
		'text',
		'number',
		'textarea',
		'radio',
		'checkbox',
		'select',
		'hidden',
		'file',
		'submit',
		'import',
		'export',
		'image',
		'media',
		'color',
		'taxonomy',
		'posttype',
		'size',
		'section_title',	//	3.0.0+
	);	
	
	function __construct( &$aFieldTypeDefinitions, $sExtendedClassName, $oMsg ) {
		
		$_aFieldTypeDefinitions = array();
		foreach( self::$aDefaultFieldTypeSlugs as $sFieldTypeSlug ) {
			$_sInstantiatingClassName = "AdminPageFramework_FieldType_{$sFieldTypeSlug}";
			if ( ! class_exists( $_sInstantiatingClassName ) ) continue;
			$_oFieldType = new $_sInstantiatingClassName( $sExtendedClassName, null, $oMsg, false );	// passing false for the forth parameter disables auto-registering.		
			foreach( $_oFieldType->aFieldTypeSlugs as $__sSlug ) {			
				$_aFieldTypeDefinitions[ $__sSlug ] = $_oFieldType->getDefinitionArray();
			}
			
		}
		$aFieldTypeDefinitions = $_aFieldTypeDefinitions;
	}
	
	/**
	 * Sets the given field type's enqueuing scripts and styles.
	 * 
	 * A helper function for the above addSettingField() method.
	 * 
	 * @since			2.1.5
	 * @since			3.0.0			Moved to the field type registration class and made it static to be used by different classes.
	 */
	static public function _setFieldHeadTagElements( array $aField, $oProp, $oHeadTag ) {

		$sFieldType = $aField['type'];
			
		// Set the global flag to indicate whether the elements are already added and enqueued. Note that it must be checked per a type (here property type is used).
		static $aLoadFlags = array();	// indicates whether the field type is already processed or not.
		$aLoadFlags[ $oProp->_sPropertyType ] = isset( $aLoadFlags[ $oProp->_sPropertyType ] ) && is_array( $aLoadFlags[ $oProp->_sPropertyType ] )
			? $aLoadFlags[ $oProp->_sPropertyType ]
			: array();
		if ( isset( $aLoadFlags[ $oProp->_sPropertyType ][ $sFieldType ] ) && $aLoadFlags[ $oProp->_sPropertyType ][ $sFieldType ] ) return;
		$aLoadFlags[ $oProp->_sPropertyType ][ $sFieldType ] = true;
				
		// If the field type is not defined, return.
		if ( ! isset( $oProp->aFieldTypeDefinitions[ $sFieldType ] ) ) return;

		if ( is_callable( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfFieldSetTypeSetter'] ) )
			call_user_func_array( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfFieldSetTypeSetter'], array( $oProp->_sPropertyType ) );
		
		if ( is_callable( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfFieldLoader'] ) )
			call_user_func_array( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfFieldLoader'], array() );		
		
		if ( is_callable( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetScripts'] ) )
			$oProp->sScript .= call_user_func_array( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetScripts'], array() );
			
		if ( is_callable( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetStyles'] ) ) 
			$oProp->sStyle .= call_user_func_array( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetStyles'], array() );
			
		if ( is_callable( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetIEStyles'] ) )
			$oProp->sStyleIE .= call_user_func_array( $oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetIEStyles'], array() );					
	
		foreach( $oProp->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueStyles'] as $asSource ) {
			if ( is_string( $asSource ) )
				$oHeadTag->_forceToEnqueueStyle( $asSource );
			else if ( is_array( $asSource ) && isset( $asSource[ 'src' ] ) )				
				$oHeadTag->_forceToEnqueueStyle( $asSource[ 'src' ], $asSource );
		}
		foreach( $oProp->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueScripts'] as $asSource ) {
			if ( is_string( $asSource ) )
				$oHeadTag->_forceToEnqueueScript( $asSource );
			else if ( is_array( $asSource ) && isset( $asSource[ 'src' ] ) )
				$oHeadTag->_forceToEnqueueScript( $asSource[ 'src' ], $asSource );			
		}							
			
	}
}
endif;