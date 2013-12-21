<?php
if ( ! class_exists( 'AdminPageFramework_CustomSubmitFields' ) ) :
/**
 * Provides helper methods that deal with custom submit fields and retrieve custom key elements.
 *
 * @abstract
 * @since			2.0.0
 * @remark			The classes that extend this include ExportOptions, ImportOptions, and Redirect.
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
abstract class AdminPageFramework_CustomSubmitFields {
	 
	public function __construct( $aPostElement ) {
		
		$this->aPostElement = $aPostElement;	// e.g. $_POST['__import'] or $_POST['__export'] or $_POST['__redirect']
		
	}
	
	/**
	 * Retrieves the value of the specified element key.
	 * 
	 * The element key is either a single key or two keys. The two keys means that the value is stored in the second dimension.
	 * 
	 * @since			2.0.0
	 */ 
	protected function getElement( $aElement, $aElementKey, $sElementKey='format' ) {
			
		$sFirstDimensionKey = $aElementKey[ 0 ];
		if ( ! isset( $aElement[ $sFirstDimensionKey ] ) || ! is_array( $aElement[ $sFirstDimensionKey ] ) ) return 'ERROR_A';

		/* For single element, e.g.
		 * <input type="hidden" name="__import[import_single][import_option_key]" value="APF_GettingStarted">
		 * <input type="hidden" name="__import[import_single][format]" value="array">
		 * */	
		if ( isset( $aElement[ $sFirstDimensionKey ][ $sElementKey ] ) && ! is_array( $aElement[ $sFirstDimensionKey ][ $sElementKey ] ) )
			return $aElement[ $sFirstDimensionKey ][ $sElementKey ];

		/* For multiple elements, e.g.
		 * <input type="hidden" name="__import[import_multiple][import_option_key][2]" value="APF_GettingStarted.txt">
		 * <input type="hidden" name="__import[import_multiple][format][2]" value="array">
		 * */
		if ( ! isset( $aElementKey[ 1 ] ) ) return 'ERROR_B';
		$sKey = $aElementKey[ 1 ];
		if ( isset( $aElement[ $sFirstDimensionKey ][ $sElementKey ][ $sKey ] ) )
			return $aElement[ $sFirstDimensionKey ][ $sElementKey ][ $sKey ];
			
		return 'ERROR_C';	// Something wrong happened.
		
	}	
	
	/**
	 * Retrieves an array consisting of two values.
	 * 
	 * The first element is the fist dimension's key and the second element is the second dimension's key.
	 * @since			2.0.0
	 */
	protected function getElementKey( $aElement, $sFirstDimensionKey ) {
		
		if ( ! isset( $aElement[ $sFirstDimensionKey ] ) ) return;
		
		// Set the first element the field ID.
		$aEkementKey = array( 0 => $sFirstDimensionKey );

		// For single export buttons, e.g. name="__import[submit][import_single]" 		
		if ( ! is_array( $aElement[ $sFirstDimensionKey ] ) ) return $aEkementKey;
		
		// For multiple ones, e.g. name="__import[submit][import_multiple][1]" 		
		foreach( $aElement[ $sFirstDimensionKey ] as $k => $v ) {
			
			// Only the pressed export button's element is submitted. In other words, it is necessary to check only one item.
			$aEkementKey[] = $k;
			return $aEkementKey;			
				
		}		
	}
		
	public function getFieldID() {
		
		// e.g.
		// single:		name="__import[submit][import_single]"
		// multiple:	name="__import[submit][import_multiple][1]"
		
		if ( isset( $this->sFieldID ) && $this->sFieldID  ) return $this->sFieldID;
		
		// Only the pressed element will be stored in the array.
		foreach( $this->aPostElement['submit'] as $sKey => $v ) {	// $this->aPostElement should have been set in the constructor.
			$this->sFieldID = $sKey;
			return $this->sFieldID;
		}
	}	
		
}
endif;