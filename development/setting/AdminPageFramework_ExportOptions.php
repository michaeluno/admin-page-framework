<?php
if ( ! class_exists( 'AdminPageFramework_ExportOptions' ) ) :
/**
 * Provides methods to export option data.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_CustomSubmitFields
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AdminPageFramework_ExportOptions extends AdminPageFramework_CustomSubmitFields {

	public function __construct( $aPostExport, $sClassName ) {
		
		// Call the parent constructor.
		parent::__construct( $aPostExport );
		
		// Properties
		$this->aPostExport = $aPostExport;
		$this->sClassName = $sClassName;	// will be used in the getTransientIfSet() method.
		// $this->sPageSlug = $sPageSlug;
		// $this->sTabSlug = $sTabSlug;
		
		// Find the field ID and the element key ( for multiple export buttons )of the pressed submit ( export ) button.
		$this->sFieldID = $this->getFieldID();
		$this->aElementKey = $this->getElementKey( $aPostExport['submit'], $this->sFieldID );
		
		// Set the file name to download and the format type. Also find whether the exporting data is set in transient.
		$this->sFileName = $this->getElement( $aPostExport, $this->aElementKey, 'file_name' );
		$this->sFormatType = $this->getElement( $aPostExport, $this->aElementKey, 'format' );
		$this->bIsDataSet = $this->getElement( $aPostExport, $this->aElementKey, 'transient' );
	
	}
	
	public function getTransientIfSet( $vData ) {
		
		if ( $this->bIsDataSet ) {
			$sTransient = isset( $this->aElementKey[1] ) 
				? "{$this->sClassName}_{$this->sFieldID}_{$this->aElementKey[1]}" 
				: "{$this->sClassName}_{$this->sFieldID}";
			$tmp = get_transient( md5( $sTransient ) );
			if ( $tmp !== false ) {
				$vData = $tmp;
				delete_transient( md5( $sTransient ) );
			}
		}
		return $vData;
	}
	
	public function getFileName() {
		return $this->sFileName;
	}
	public function getFormat() {
		return $this->sFormatType;
	}
	
	/**
	 * Returns the specified sibling value.
	 * 
	 * @since			2.1.5
	 */
	public function getSiblingValue( $sKey ) {
		
		return $this->getElement( $this->aPostExport, $this->aElementKey, $sKey );
		
	}	

	/**
	 * Performs exporting data.
	 * 
	 * @since			2.0.0
	 */ 
	public function doExport( $vData, $sFileName=null, $sFormatType=null ) {

		/* 
		 * Sample HTML elements that triggers the method.
		 * e.g.
		 * <input type="hidden" name="__export[export_sinble][file_name]" value="APF_GettingStarted_20130708.txt">
		 * <input type="hidden" name="__export[export_sinble][format]" value="json">
		 * <input id="export_and_import_export_sinble_0" 
		 *  type="submit" 
		 *  name="__export[submit][export_sinble]" 
		 *  value="Export Options">
		*/	
		$sFileName = isset( $sFileName ) ? $sFileName : $this->sFileName;
		$sFormatType = isset( $sFormatType ) ? $sFormatType : $this->sFormatType;
							
		// Do export.
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $sFileName );
		switch ( strtolower( $sFormatType ) ) {
			case 'text':	// for plain text.
				if ( is_array( $vData ) || is_object( $vData ) ) {
					// $oDebug = new AdminPageFramework_Debug;
					// $sData = $oDebug->getArray( $vData );
					die( AdminPageFramework_Debug::getArray( $vData, null, false ) );
					 
				}
				die( $vData );
			case 'json':	// for json.
				die( json_encode( ( array ) $vData ) );
			case 'array':	// for serialized PHP array.
			default:	// for anything else, 
				die( serialize( ( array ) $vData  ));
		}
	}
}
endif;