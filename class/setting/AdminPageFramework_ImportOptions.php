<?php
if ( ! class_exists( 'AdminPageFramework_ImportOptions' ) ) :
/**
 * Provides methods to import option data.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_CustomSubmitFields
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AdminPageFramework_ImportOptions extends AdminPageFramework_CustomSubmitFields {
	
	/* Example of $_FILES for a single import field. 
		Array (
			[__import] => Array (
				[name] => Array (
				   [import_single] => APF_GettingStarted_20130709 (1).json
				)
				[type] => Array (
					[import_single] => application/octet-stream
				)
				[tmp_name] => Array (
					[import_single] => Y:\wamp\tmp\php7994.tmp
				)
				[error] => Array (
					[import_single] => 0
				)
				[size] => Array (
					[import_single] => 715
				)
			)
		)
	*/
	
	public function __construct( $aFilesImport, $aPostImport ) {

		// Call the parent constructor. This must be done before the getFieldID() method that uses the $aPostElement property.
		parent::__construct( $aPostImport );
	
		$this->aFilesImport = $aFilesImport;
		$this->aPostImport = $aPostImport;
		
		// Find the field ID and the element key ( for multiple export buttons )of the pressed submit ( export ) button.
		$this->sFieldID = $this->getFieldID();
		$this->aElementKey = $this->getElementKey( $aPostImport['submit'], $this->sFieldID );
			
	}
	
	private function getElementInFilesArray( $aFilesImport, $aElementKey, $sElementKey='error' ) {

		$sElementKey = strtolower( $sElementKey );
		$sFieldID = $aElementKey[ 0 ];	// or simply assigning $this->sFieldID would work as well.
		if ( ! isset( $aFilesImport[ $sElementKey ][ $sFieldID ] ) ) return 'ERROR_A: The given key does not exist.';
	
		// For single export buttons, e.g. $_FILES[__import][ $sElementKey ][import_single] 
		if ( isset( $aFilesImport[ $sElementKey ][ $sFieldID ] ) && ! is_array( $aFilesImport[ $sElementKey ][ $sFieldID ] ) )
			return $aFilesImport[ $sElementKey ][ $sFieldID ];
			
		// For multiple import buttons, e.g. $_FILES[__import][ $sElementKey ][import_multiple][2]
		if ( ! isset( $aElementKey[ 1 ] ) ) return 'ERROR_B: the sub element is not set.';
		$sKey = $aElementKey[ 1 ];		
		if ( isset( $aPostImport[ $sElementKey ][ $sFieldID ][ $sKey ] ) )
			return $aPostImport[ $sElementKey ][ $sFieldID ][ $sKey ];

		// Something wrong happened.
		return 'ERROR_C: unexpected problem occurred.';
		
	}	
		
	public function getError() {
		
		return $this->getElementInFilesArray( $this->aFilesImport, $this->aElementKey, 'error' );
		
	}
	public function getType() {
		
		return $this->getElementInFilesArray( $this->aFilesImport, $this->aElementKey, 'type' );
		
	}
	public function getImportData() {
		
		// Retrieve the uploaded file path.
		$sFilePath = $this->getElementInFilesArray( $this->aFilesImport, $this->aElementKey, 'tmp_name' );
		
		// Read the file contents.
		$vData = file_exists( $sFilePath ) ? file_get_contents( $sFilePath, true ) : false;
		
		return $vData;
		
	}
	public function formatImportData( &$vData, $sFormatType=null ) {
		
		$sFormatType = isset( $sFormatType ) ? $sFormatType : $this->getFormatType();
		switch ( strtolower( $sFormatType ) ) {
			case 'text':	// for plain text.
				return;	// do nothing
			case 'json':	// for json.
				$vData = json_decode( ( string ) $vData, true );	// the second parameter indicates to decode it as array.
				return;
			case 'array':	// for serialized PHP array.
			default:	// for anything else, 
				$vData = maybe_unserialize( trim( $vData ) );
				return;
		}		
	
	}
	public function getFormatType() {
					
		$this->sFormatType = isset( $this->sFormatType ) && $this->sFormatType 
			? $this->sFormatType
			: $this->getElement( $this->aPostImport, $this->aElementKey, 'format' );

		return $this->sFormatType;
		
	}
	
	/**
	 * Returns the specified sibling value.
	 * 
	 * @since			2.1.5
	 */
	public function getSiblingValue( $sKey ) {
		
		return $this->getElement( $this->aPostImport, $this->aElementKey, $sKey );
		
	}
	
}
endif;