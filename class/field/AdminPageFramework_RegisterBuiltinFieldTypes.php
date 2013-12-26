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
		'default' => array( 'default' ),	// undefined ones will be applied 
		'text' => array( 'text', 'password', 'date', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'time', 'url', 'week' ),
		// 'number' => array( 'number', 'range' ),
		// 'textarea' => array( 'textarea' ),
		// 'radio' => array( 'radio' ),
		// 'checkbox' => array( 'checkbox' ),
		// 'select' => array( 'select' ),
		// 'hidden' => array( 'hidden' ),
		// 'file' => array( 'file' ),
		// 'submit' => array( 'submit' ),
		// 'import' => array( 'import' ),
		// 'export' => array( 'export' ),
		// 'image' => array( 'image' ),
		// 'media' => array( 'media' ),
		// 'color' => array( 'color' ),
		// 'taxonomy' => array( 'taxonomy' ),
		// 'posttype' => array( 'posttype' ),
		// 'size' => array( 'size' ),
	);	
	
	function __construct( &$aFieldTypeDefinitions, $sExtendedClassName, $oMsg ) {
		foreach( self::$aDefaultFieldTypeSlugs as $sFieldTypeSlug => $aSlugs ) {
			$sInstantiatingClassName = "AdminPageFramework_FieldType_{$sFieldTypeSlug}";
			if ( class_exists( $sInstantiatingClassName ) ) {
				$oFieldType = new $sInstantiatingClassName( $sExtendedClassName, $sFieldTypeSlug, $oMsg, false );	// passing false for the forth parameter disables auto-registering.
				foreach( $aSlugs as $sSlug )
					$aFieldTypeDefinitions[ $sSlug ] = $oFieldType->getDefinitionArray();
			}
		}
	}
}
endif;