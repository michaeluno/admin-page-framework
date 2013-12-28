<?php
if ( ! class_exists( 'AdminPageFramework_RegisterBuiltinFieldTypes' ) ) :
/**
 * Provides means to define custom input fields not only by the framework but also by the user.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 * @since			2.1.6			Changed the name from AdminPageFramework_FieldTypeDefinitions
 */
class AdminPageFramework_RegisterBuiltinFieldTypes  {
	
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
		// 'radio',
		// 'checkbox',
		// 'select',
		'hidden',
		// 'file',
		// 'submit',
		// 'import',
		// 'export',
		// 'image',
		// 'media',
		'color',
		// 'taxonomy',
		// 'posttype',
		// 'size',
	);	
	
	function __construct( &$aFieldTypeDefinitions, $sExtendedClassName, $oMsg ) {
		foreach( self::$aDefaultFieldTypeSlugs as $sFieldTypeSlug ) {
			$sInstantiatingClassName = "AdminPageFramework_FieldType_{$sFieldTypeSlug}";
			if ( class_exists( $sInstantiatingClassName ) ) {
				$oFieldType = new $sInstantiatingClassName( $sExtendedClassName, null, $oMsg, false );	// passing false for the forth parameter disables auto-registering.
				foreach( $oFieldType->aFieldTypeSlugs as $sSlug )
					$aFieldTypeDefinitions[ $sSlug ] = $oFieldType->getDefinitionArray();
			}
		}
	}
}
endif;