<?php
if ( ! class_exists( 'AdminPageFramework_FormElement' ) ) :
/**
 * Provides methods to compose form elements
 * 
 * @package			AdminPageFramework
 * @subpackage		Property
 * @since			3.0.0
 * @internal
 */
class AdminPageFramework_FormElement extends AdminPageFramework_WPUtility {
	
	/**
	 * Represents the structure of the form section array.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds array structure of form section.
	 * @static
	 * @internal
	 */ 	
	public static $_aStructure_Section = array(	
		'section_id' => '_default',		// 3.0.0+
		'page_slug' => null,
		'tab_slug' => null,
		'title' => null,
		'description' => null,
		'capability' => null,
		'if' => true,	
		'order' => null,	// do not set the default number here because incremented numbers will be added when registering the sections.
		'help' => null,
		'help_aside' => null,
		'repeatable'	=> null,		// 3.0.0+
	);	
	
	/**
	 * Represents the structure of the form field array.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds array structure of form field.
	 * @static
	 * @internal
	 */ 
	public static $_aStructure_Field = array(
		'field_id'			=> null, 		// ( required )
		'type'				=> null,		// ( required )
		'section_id'		=> null,		// ( optional )
		'section_title'		=> null,		// This will be assigned automatically in the formatting method.
		'page_slug'			=> null,		// This will be assigned automatically in the formatting method.
		'tab_slug'			=> null,		// This will be assigned automatically in the formatting method.
		'option_key'		=> null,		// This will be assigned automatically in the formatting method.
		'class_name'		=> null,		// This will be assigned automatically in the formatting method.
		'capability'		=> null,		
		'title'				=> null,
		'tip'				=> null,
		'description'		=> null,
		'error_message'		=> null,		// error message for the field
		'before_label'		=> null,
		'after_label'		=> null,
		'if' 				=> true,
		'order'				=> null,		// do not set the default number here for this key.		
		'default'			=> null,
		'value'				=> null,
		'help'				=> null,		// [2.1.0+]
		'help_aside'		=> null,		// [2.1.0+]
		'repeatable'		=> null,		// [2.1.3+]
		'sortable'			=> null,		// [2.1.3+]
		'attributes'		=> null,		// [3.0.0+] - the array represents the attributes of input tag
		'show_title_column' => true,		// [3.0.0+]
		'hidden'			=> null,		// [3.0.0+]
		'_fields_type'		=> null,		// [3.0.0+] - an internal key that indicates the fields type such as page, meta box for pages, meta box for posts, or taxonomy.
		'_section_index'	=> null,		// [3.0.0+] - internally set to indicate the section index for repeatable sections.
	);	
	
	/**
	 * Stores field definition arrays.
	 * @since			3.0.0
	 */
	public $aFields = array();
	
	/**
	 * Stores section definition arrays.
	 * 
	 * @since			3.0.0
	 */
	public $aSections = array();
	
	/**
	 * Stores the fields type. 
	 * 
	 * @since			3.0.0
	 */
	protected $sFieldsType = '';
	
	/**
	 * Stores the target page slug which will be applied when no page slug is specified.
	 * 
	 * @since			3.0.0
	 */
	protected $_sTargetSectionID = '_default';	
	
	/**
	 * Stores the default capability.
	 * 
	 * @since			3.0.0
	 */
	public function __construct( $sFieldsType, $sCapability ) {
		
		$this->sFieldsType = $sFieldsType;
		$this->sCapability = $sCapability;
		
	}
	
	/**
	 * Adds the given section definition array to the form property.
	 * 
	 * @since			3.0.0
	 */
	public function addSection( array $aSection ) {
		
		$aSection = $aSection + self::$_aStructure_Section;
		$aSection['section_id'] = $this->sanitizeSlug( $aSection['section_id'] );
		
		$this->aSections[ $aSection['section_id'] ] = $aSection;	
		$this->aFields[ $aSection['section_id'] ] = isset( $this->aFields[ $aSection['section_id'] ] ) ? $this->aFields[ $aSection['section_id'] ] : array();

	}
	
	/**
	 * Removes a section definition array from the property by the given section ID.
	 * 
	 * @since			3.0.0
	 */
	public function removeSection( $sSectionID ) {
		
		if ( $sSectionID == '_default' ) return;
		
		unset( $this->aSections[ $sSectionID ] );
		unset( $this->aFields[ $sSectionID ] );
		
	}
	
	/*
	 * Adds the given field definition array to the form property.
	 * 
	 * @since			3.0.0
	 * @return			array|string|null			If the passed field is set, it returns the set field array. If the target section id is set, the set section id is returned. Otherwise null.
	 */	
	public function addField( $asField ) {
		
		if ( ! is_array( $asField ) ) {
			$this->_sTargetSectionID = is_string( $asField ) ? $asField : $this->_sTargetSectionID;
			return $this->_sTargetSectionID;	// result
		}
		$this->_sTargetSectionID = isset( $asField['section_id'] ) ? $asField['section_id'] : $this->_sTargetSectionID;
		
		$aField = $this->uniteArrays( 
			array( '_fields_type' => $this->sFieldsType ),
			$asField, 
			array( 'section_id' => $this->_sTargetSectionID ),
			self::$_aStructure_Field
		);
		if ( ! isset( $aField['field_id'], $aField['type'] ) ) return null;	// Check the required keys as these keys are necessary.
			
		// Sanitize the IDs since they are used as a callback method name.
		$aField['field_id'] = $this->sanitizeSlug( $aField['field_id'] );
		$aField['section_id'] = $this->sanitizeSlug( $aField['section_id'] );		
		
		$this->aFields[ $aField['section_id'] ][ $aField['field_id'] ] = $aField;
		return $aField;	// result
		
	}	
		
	/**
	 * Removes a field definition array from the property array by the given field ID.
	 * 
	 * @since			3.0.0
	 */		
	public function removeField( $sFieldID ) {
		
		/* The structure of the aFields property array looks like this:
			array( 
				'my_sec_a' => array(
					'my_field_a' => array( ... ),
					'my_field_b' => array( ... ),
					'my_field_c' => array( ... ),
				),
				'my_sec_b' => array(
					'my_field_a' => array( ... ),
					'my_field_b' => array( ... ),
					1	=> array(
						'my_field_a' => array( ... ),
						'my_field_b' => array( ... ),
					)
					2	=> array(
						'my_field_a' => array( ... ),
						'my_field_b' => array( ... ),
					)					
				)

			)
		 */
		foreach( $this->aFields as $_sSectionID => $_aSubSectionsOrFields ) {

			if ( array_key_exists( $sFieldID, $_aSubSectionsOrFields ) ) 
				unset( $this->aFields[ $_sSectionID ][ $sFieldID ] );
			
			// Check sub-sections.
			foreach ( $_aSubSectionsOrFields as $_sIndexOrFieldID => $_aSubSectionOrFields ) {
				
				if ( is_numeric( $_sIndexOrFieldID ) && is_int( $_sIndexOrFieldID + 0 ) ) {	// means it's a sub-section
					
					if ( array_key_exists( $sFieldID, $_aSubSectionOrFields ) )
						unset( $this->aFields[ $_sSectionID ][ $_sIndexOrFieldID ] );
					
					continue;
					
				}
				
			}
		}
		
	}
	
	/**
	 * Formats the section and field definition arrays.
	 * 
	 * @since			3.0.0
	 */
	public function format() {
		
		$this->formatSections( $this->sFieldsType, $this->sCapability );
		$this->formatFields( $this->sFieldsType, $this->sCapability );
		
	}
	
	/**
	 * Formats the stored sections definition array.
	 * 
	 * @since			3.0.0
	 */
	public function formatSections( $sFieldsType, $sCapability ) {
		
		$aNewSectionArray = array();
		foreach( $this->aSections as $_sSectionID => $_aSection ) {
			
			$_aSection = $this->uniteArrays(
				$_aSection,
				array( 
					'_fields_type' => $sFieldsType,
					'capability' => $sCapability,
				),
				self::$_aStructure_Section
			);
						
			// Check capability. If the access level is not sufficient, skip.
			if ( ! current_user_can( $_aSection['capability'] ) ) continue;
			if ( ! $_aSection['if'] ) continue;
			
			$aNewSectionArray[ $_sSectionID ] = $_aSection;
			
			
		}
		uasort( $aNewSectionArray, array( $this, '_sortByOrder' ) ); 
		$this->aSections = $aNewSectionArray;
		
	}
	
			
	/**
	 * Formats the stored fields definition array.
	 * 
	 * @since			3.0.0
	 */
	public function formatFields( $sFieldsType, $sCapability ) {

		$_aNewFields = array();
		foreach ( $this->aFields as $_sSectionID => $_aSubSectionORFields ) {
						
			foreach( $_aSubSectionORFields as $_sIndexOrFieldID => $_aSubSectionOrField ) {
				
				// If it is a sub-section array.
				if ( is_numeric( $_sIndexOrFieldID ) && is_int( $_sIndexOrFieldID + 0 ) ) {
					
					$_sSubSectionIndex = $_sIndexOrFieldID;
					$_aFields = $_aSubSectionOrField;
					foreach( $_aFields as $_aField ) {
						
						$_aField = $this->getFormatedField( $_aField, $sFieldsType, $sCapability );
						if ( $_aField )
							$_aNewFields[ $_sSectionID ][ $_sSubSectionIndex ][ $_aField['field_id'] ] = $_aField;						
						
					}
					uasort( $_aNewFields[ $_sSectionID ][ $_sSubSectionIndex ], array( $this, '_sortByOrder' ) ); 
					continue;
					
				}
				
				// Otherwise, insert the formatted field definiton array.
				$_aField = $_aSubSectionOrField;
				$_aField = $this->getFormatedField( $_aField, $sFieldsType, $sCapability );
				if ( $_aField )
					$_aNewFields[ $_sSectionID ][ $_aField['field_id'] ] = $_aField;
				
			}
			uasort( $_aNewFields[ $_sSectionID ], array( $this, '_sortByOrder' ) ); 
				
		}
		
		// Sort by the order of the sections.
		if ( ! empty( $this->aSections ) ) :	// as taxonomy fields don't have sections
			$_aSortedFields = array();
			foreach( $this->aSections as $sSectionID => $aSeciton ) 	// will be parsed in the order of the $aSections array. Therefore, the sections must be formatted before this method.
				$_aSortedFields[ $sSectionID ] = $_aNewFields[ $sSectionID ];
			$_aNewFields = $_aSortedFields;
		endif;
		
		$this->aFields = $_aNewFields;
		
	}
		/**
		 * Returns the formatted field array.
		 * 
		 * @since			3.0.0
		 */
		protected function getFormatedField( $aField, $sFieldsType, $sCapability ) {
			
			$_aField = $this->uniteArrays(
				array( '_fields_type' => $sFieldsType ),
				$aField,
				array( 'capability' => $sCapability ),
				self::$_aStructure_Field
			);
			
			// Check capability. If the access level is not sufficient, skip.
			if ( ! current_user_can( $_aField['capability'] ) ) return null;
			if ( ! $_aField['if'] ) return null;
			
			return $_aField;
			
		}
	/**
	 * Determines whether the given ID is of a registered form section.
	 * 
	 * @since			3.0.0
	 */
	public function isSection( $sID ) {
		
		/* 
		 * Consider the possibility that the given ID may be used both for a section and a field.
		 * 1. Check if the given ID is not a section.
		 * 2. Parse stored fields and check their ID. If one matches, return false.
		 */
		
		if ( is_numeric( $sID ) && is_int( $sID + 0 ) ) return false;		// integer IDs are not accepted.
		
		// If the section ID is not registered, return false.
		if ( ! array_key_exists( $sID, $this->aSections ) ) return false;
		
		if ( ! array_key_exists( $sID, $this->aFields ) ) return false;
		
		$_bIsSeciton = false;
		foreach( $this->aFields as $_sSectionID => $_aFields ) {	// since numeric IDs are denied at the beginning of the method, the elements will not be sub-sections.
			
			if ( $_sSectionID == $sID ) $_bIsSeciton = true;
			
			if ( array_key_exists( $sID, $_aFields ) ) return false;	// a field using the ID is found, and it precedes a section match.
			
		}
		
		return $_bIsSeciton;
		
	}	
	
	/**
	 * Returns the output of the title and description part of the given section by section ID.
	 * 
	 * @since			3.0.0
	 */ 
	public function getSectionHeader( $sSectionID ) {
		
		if ( ! isset( $this->aSections[ $sSectionID ] ) ) return '';
		
		$aOutput = array();
		$aOutput[] = $this->aSections[ $sSectionID ]['title'] ? "<h3 class='admin-page-framework-section-title'>" . $this->aSections[ $sSectionID ]['title'] . "</h3>" : '';
		$aOutput[] = $this->aSections[ $sSectionID ]['description'] ? "<p class='admin-page-framework-section-description'>" . $this->aSections[ $sSectionID ]['description'] . "</p>" : '';
		return implode( PHP_EOL, $aOutput );
		
	}
	
	/**
	 * Returns a fields model array that represents the structure of the array of saving data from the given fields definition array.
	 * 
	 * The passed fields array should be structured like the following.
	 * 
	 * 	array(  
	 * 		'_default'	=> array(		// _default is reserved for the system.
	 * 			'my_field_id' => array( .... ),
	 * 			'my_field_id2' => array( .... ),
	 * 		),
	 * 		'my_secion_id' => array(
	 * 			'my_field_id' => array( ... ),
	 * 			'my_field_id2' => array( ... ),
	 * 			'my_field_id3' => array( ... ),
	 * 	
	 * 		),
	 * 		'my_section_id2' => array(
	 * 			'my_field_id' => array( ... ),
	 * 		),
	 * 		...
	 * )
	 * 
	 * It will be converted to 
	 * 	array(  
	 * 		'my_field_id' => array( .... ),
	 * 		'my_field_id2' => array( .... ),
	 * 		'my_secion_id' => array(
	 * 			'my_field_id' => array( ... ),
	 * 			'my_field_id2' => array( ... ),
	 * 			'my_field_id3' => array( ... ),
	 * 	
	 * 		),
	 * 		'my_section_id2' => array(
	 * 			'my_field_id' => array( ... ),
	 * 		),
	 * 		...
	 * )
	 * 
	 * @remark			Just the _default section elements get extracted to the upper dimension.
	 * @since			3.0.0
	 */
	public static function getFieldsModel( array $aFields )  {
		
		$_aFieldsModel = array();
		foreach ( $aFields as $_sSectionID => $_aFields ) {

			if ( $_sSectionID != '_default' ) {
				$_aFieldsModel[ $_sSectionID ][ $_aField['field_id'] ] = $_aField;	
				continue;
			}
			
			// For default field items.
			foreach( $_aFields as $_sFieldID => $_aField ) 
				$_aFieldsModel[ $_aField['field_id'] ] = $_aField;

		}
		return $_aFieldsModel;
	}
	
	
		/**
		 * Calculates the subtraction of two values with the array key of <em>order</em>
		 * 
		 * This is used to sort arrays.
		 * 
		 * @since			3.0.0			
		 * @remark			a callback method for uasort().
		 * @return			integer
		 * @internal
		 */ 
		public function _sortByOrder( $a, $b ) {
			return isset( $a['order'], $b['order'] )
				? $a['order'] - $b['order']
				: 1;
		}		
}
endif;