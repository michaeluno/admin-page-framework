<?php
if ( ! class_exists( 'AdminPageFramework_Message' ) ) :
/**
 * Provides methods for text messages.
 *
 * @since			2.0.0
 * @since			2.1.6			Multiple instances of this class are disallowed.
 * @extends			n/a
 * @package			AdminPageFramework
 * @subpackage		Property
 * @internal
 */
class AdminPageFramework_Message {

	/**
	 * Stores the framework's messages.
	 * 
	 * @remark			The user can modify this property directly.
	 */ 
	public $aMessages = array();

	/**
	 * Stores the self instance.
	 * @internal
	 */
	private static $_oInstance;
	
	/**
	 * Stores the text domain used for the messages.
	 * @internal
	 */
	protected $_sTextDomain = 'admin-page-framework';
	
	/**
	 * Ensures that only one instance of this class object exists. ( no multiple instances of this object ) 
	 * 
	 * @since			2.1.6
	 * @remark			This class should be instantiated via this method.
	 */
	public static function instantiate( $sTextDomain='admin-page-framework' ) {
		
		static $_sTextDomain;
		$_sTextDomain = $sTextDomain ? $sTextDomain : ( $_sTextDomain ? $_sTextDomain : 'admin-page-framework' ) ;
		if ( ! isset( self::$_oInstance ) && ! ( self::$_oInstance instanceof AdminPageFramework_Message ) ) 
			self::$_oInstance = new AdminPageFramework_Message( $_sTextDomain );
		return self::$_oInstance;
		
	}	
	
	public function __construct( $sTextDomain='admin-page-framework' ) {
		
		$this->_sTextDomain = $sTextDomain;
		$this->aMessages = array(
			
			// AdminPageFramework
			'option_updated'		=> __( 'The options have been updated.', 'admin-page-framework' ),
			'option_cleared'		=> __( 'The options have been cleared.', 'admin-page-framework' ),
			'export'				=> __( 'Export', 'admin-page-framework' ),
			'export_options'		=> __( 'Export Options', 'admin-page-framework' ),
			'import_options'		=> __( 'Import', 'admin-page-framework' ),
			'import_options'		=> __( 'Import Options', 'admin-page-framework' ),
			'submit'				=> __( 'Submit', 'admin-page-framework' ),
			'import_error'			=> __( 'An error occurred while uploading the import file.', 'admin-page-framework' ),
			'uploaded_file_type_not_supported'	=> __( 'The uploaded file type is not supported: %1$s', 'admin-page-framework' ),
			'could_not_load_importing_data' => __( 'Could not load the importing data.', 'admin-page-framework' ),
			'imported_data'			=> __( 'The uploaded file has been imported.', 'admin-page-framework' ),
			'not_imported_data' 	=> __( 'No data could be imported.', 'admin-page-framework' ),
			'upload_image'			=> __( 'Upload Image', 'admin-page-framework' ),
			'use_this_image'		=> __( 'Use This Image', 'admin-page-framework' ),
			'reset_options'			=> __( 'Are you sure you want to reset the options?', 'admin-page-framework' ),
			'confirm_perform_task'	=> __( 'Please confirm if you want to perform the specified task.', 'admin-page-framework' ),
			'option_been_reset'		=> __( 'The options have been reset.', 'admin-page-framework' ),
			'specified_option_been_deleted'	=> __( 'The specified options have been deleted.', 'admin-page-framework' ),
			
			// AdminPageFramework_PostType
			'title'			=> __( 'Title', 'admin-page-framework' ),	
			'author'		=> __( 'Author', 'admin-page-framework' ),	
			'categories'	=> __( 'Categories', 'admin-page-framework' ),
			'tags'			=> __( 'Tags', 'admin-page-framework' ),
			'comments' 		=> __( 'Comments', 'admin-page-framework' ),
			'date'			=> __( 'Date', 'admin-page-framework' ), 
			'show_all'		=> __( 'Show All', 'admin-page-framework' ),

			// AdminPageFramework_Link_Base
			'powered_by'	=> __( 'Powered by', 'admin-page-framework' ),
			
			// AdminPageFramework_Link_Page
			'settings'		=> __( 'Settings', 'admin-page-framework' ),
			
			// AdminPageFramework_Link_PostType
			'manage'		=> __( 'Manage', 'admin-page-framework' ),
			
			// AdminPageFramework_FieldType_Base
			'select_image'			=> __( 'Select Image', 'admin-page-framework' ),
			'upload_file'			=> __( 'Upload File', 'admin-page-framework' ),
			'use_this_file'			=> __( 'Use This File', 'admin-page-framework' ),
			'select_file'			=> __( 'Select File', 'admin-page-framework' ),
			
			// AdminPageFramework_PageLoadInfo_Base
			'queries_in_seconds'	=> __( '%s queries in %s seconds.', 'admin-page-framework' ),
			'out_of_x_memory_used'	=> __( '%s out of %s MB (%s) memory used.', 'admin-page-framework' ),
			'peak_memory_usage'		=> __( 'Peak memory usage %s MB.', 'admin-page-framework' ),
			'initial_memory_usage'	=> __( 'Initial memory usage  %s MB.', 'admin-page-framework' ),
						
			// AdminPageFramework_FormField
			'allowed_maximum_number_of_fields'	=>	__( 'The allowed maximum number of fields is {0}.', 'admin-page-framework' ),
			'allowed_minimum_number_of_fields'	=>	__( 'The allowed minimum number of fields is {0}.', 'admin-page-framework' ),
			'add'					=> __( 'Add', 'admin-page-framework' ),
			'remove'				=> __( 'Remove', 'admin-page-framework' ),
			
			// AdminPageFramework_FormTable
			'allowed_maximum_number_of_sections'	=>	__( 'The allowed maximum number of sections is (0)', 'admin-page-framework' ),
			'allowed_minimum_number_of_sections'	=>	__( 'The allowed minimum number of sections is (0)', 'admin-page-framework' ),
			'add_section'	=>	__( 'Add Section' ),
			'remove_section'	=>	__( 'Remove Section' ),
			
		);		
		
	}
	public function __( $sKey ) {
		
		return isset( $this->aMessages[ $sKey ] )
			? __( $this->aMessages[ $sKey ], $this->_sTextDomain )
			: '';
			
	}
	
	public function _e( $sKey ) {
		
		if ( isset( $this->aMessages[ $sKey ] ) )
			_e( $this->aMessages[ $sKey ], $this->_sTextDomain );
			
	}
	
}
endif;