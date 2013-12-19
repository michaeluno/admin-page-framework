<?php
class AdminPageFramework_RegisterClasses {
	
	/**
	 * Stores the registered classes with the key of the class name and the value of the file path.
	 */
	protected $_aClasses = array();
	
	/**
	 * 
	 * param			array			$aClasses			the link to the array storing registered classes outside this object.
	 * The structure of %aClasses must be consist of elements of a key-value pair of a file path and the key of the class name.
	 * array(
	 * 	'MyClassName' => 'MyClassName.php',
	 * 	'MyClassName2' => 'MyClassName2.php',
	 * )
	 * 
	 */
	function __construct( $sClassDirPath, & $aClasses=array(), $aAllowedExtensions=array( 'php', 'inc' ) ) {
			
		$this->_aClasses = $aClasses + $this->composeClassArray( $sClassDirPath, $aAllowedExtensions );
		$this->registerClasses();
		
	}
	
	/**
	 * Sets up the array consisting of class paths with the key of file name w/o extension.
	 */
	protected function composeClassArray( $sClassDirPath, $aAllowedExtensions ) {
		
		$sClassDirPath = rtrim( $sClassDirPath, '\\/' ) . DIRECTORY_SEPARATOR;	// ensures the trailing (back/)slash exists.
		$aFilePaths = $this->doRecursiveGlob( $sClassDirPath . '*.' . $this->getGlobPatternExtensionPart( $aAllowedExtensions ), GLOB_BRACE );

		/*
		 * Now the structure of $aFilePaths looks like:
			array
			  0 => string '.../class/MyClass.php'
			  1 => string '.../class/MyClass2.php'
			  2 => string '.../class/MyClass3.php'
			  ...
		 * 
		 */		 
		$aClasses = array();
		foreach( $aFilePaths as $sFilePath )
			$aClasses[ pathinfo( $sFilePath, PATHINFO_FILENAME ) ] = $sFilePath;	// the file name without extension will be assigned to the key

		return $aClasses;
			
	}
		/**
		 * Composes the file pattern of the file extension part used for the glob() function with the given file extensions.
		 */
		protected function getGlobPatternExtensionPart( $aExtensions=array( 'php', 'inc' ) ) {
			return empty( $aExtensions ) 
				? '*'
				: '{' . implode( ',', $aExtensions ) . '}';
		}
		
		/**
		 * The recursive version of the glob() function.
		 */
		protected function doRecursiveGlob( $sPathPatten, $iFlags=0 ) {

			$aFiles = glob( $sPathPatten, $iFlags );
			foreach ( glob( dirname( $sPathPatten ) . '/*', GLOB_ONLYDIR|GLOB_NOSORT ) as $sDirPath )
				$aFiles = array_merge( $aFiles, $this->doRecursiveGlob( $sDirPath . '/' . basename( $sPathPatten ), $iFlags ) );
		
			return $aFiles;
			
		}
		 
	
	/**
	 * Performs registration of the callback.
	 * 
	 * This registers the method to be triggered when an unknown class is instantiated. 
	 * 
	 */
	protected function registerClasses() {
		spl_autoload_register( array( $this, 'replyToAutoLoader' ) );
	}	
		/**
		 * Responds to the PHP auto-loader and includes the passed class based on the previously stored path associated with the class name in the constructor.
		 */
		public function replyToAutoLoader( $sCalledUnknownClassName ) {			
			if ( array_key_exists( $sCalledUnknownClassName, $this->_aClasses ) &&  file_exists( $this->_aClasses[ $sCalledUnknownClassName ] ) ) 
				include_once( $this->_aClasses[ $sCalledUnknownClassName ] );
		}
	
}