<?php
/**
 * Provides shared methods for PHP Class Files Script Generator.
 * 
 * @author		Michael Uno <michael@michaeluno.jp>
 * @copyright	2013-2014 (c) Michael Uno
 * @license		MIT	<http://opensource.org/licenses/MIT>
 */
 
/**
 * The base class of script creator.
 * 
 * @version		1.0.4
 */
abstract class PHP_Class_Files_Script_Generator_Base {

	static protected $_aStructure_Options = array(
	
		'header_class_name' => '',
		'header_class_path' => '',		
		'output_buffer'     => true,
		'header_type'       => 'DOCBLOCK',	
		'exclude_classes'   => array(),
		
		// Search options
		'search'	=>	array(
			'allowed_extensions'    => array( 'php' ),	// e.g. array( 'php', 'inc' )
			'exclude_dir_paths'     => array(),
			'exclude_dir_names'     => array(),         // the directory 'base' name
			'is_recursive'          => true,
		),		
		
	);
	
	/**
	 * Returns an array holding a list of file paths combined from multiple sources.
	 */
	protected function _getFileLists( $asDirPaths, $aSearchOptions ) {
		$_aFiles    = array();
        $asDirPaths = is_array( $asDirPaths ) ? $asDirPaths : array( $asDirPaths );
		foreach( $asDirPaths as $_sDirPath ) {
			$_aFiles = array_merge( $this->_getFileList( $_sDirPath, $aSearchOptions ), $_aFiles );
		}
		return array_unique( $_aFiles );
	}
	
		/**
		 * Returns an array containing file paths.
		 * 
		 * @deprecated The directory iterator cannot filter out certain directories in some PHP versions.
		 */
		private function __getFileList( $sDirPath, $sFilePathRegexNeedle ) {
			
			$sDirPath = rtrim( $sDirPath, '\\/' );
			$_aFileList = array();
			if ( ! is_dir( $sDirPath ) ) {
				return $_aFileList;
			}
			$_oDir              = new RecursiveDirectoryIterator( $sDirPath );
			$_oIterator         = new RecursiveIteratorIterator( $_oDir );
			$_oRegexIterator    = new RegexIterator( $_oIterator, $sFilePathRegexNeedle, RegexIterator::GET_MATCH );
			foreach( $_oRegexIterator as $_aFile ) {
				$_aFileList = array_merge( $_aFileList, $_aFile );
			}
			return $_aFileList;
		}	
		/**
		 * Returns an array of scanned file paths.
		 * 
		 * The returning array structure looks like this:
			array
			  0 => string '.../class/MyClass.php'
			  1 => string '.../class/MyClass2.php'
			  2 => string '.../class/MyClass3.php'
			  ...
		 * 
		 */
		protected function _getFileList( $sDirPath, array $aSearchOptions ) {
			
			$sDirPath            = rtrim( $sDirPath, '\\/' ) . DIRECTORY_SEPARATOR;	// ensures the trailing (back/)slash exists. 		
			$_aExcludingDirPaths = $this->_formatPaths( $aSearchOptions['exclude_dir_paths'] );
			
			if ( defined( 'GLOB_BRACE' ) ) {	// in some OSes this flag constant is not available.
				$_sFileExtensionPattern = $this->_getGlobPatternExtensionPart( $aSearchOptions['allowed_extensions'] );
				$_aFilePaths = $aSearchOptions[ 'is_recursive' ]
					? $this->doRecursiveGlob( $sDirPath . '*.' . $_sFileExtensionPattern, GLOB_BRACE, $_aExcludingDirPaths, ( array ) $aSearchOptions['exclude_dir_names'] )
					: ( array ) glob( $sDirPath . '*.' . $_sFileExtensionPattern, GLOB_BRACE );
				return array_filter( $_aFilePaths );	// drop non-value elements.	
			} 
				
			// For the Solaris operation system.
			$_aFilePaths = array();
			foreach( $aSearchOptions['allowed_extensions'] as $__sAllowedExtension ) {
				$__aFilePaths = $aSearchOptions[ 'is_recursive' ]
					? $this->doRecursiveGlob( $sDirPath . '*.' . $__sAllowedExtension, 0, $_aExcludingDirPaths, ( array ) $aSearchOptions['exclude_dir_names'] )
					: ( array ) glob( $sDirPath . '*.' . $__sAllowedExtension );
				$_aFilePaths = array_merge( $__aFilePaths, $_aFilePaths );
			}
			return array_unique( array_filter( $_aFilePaths ) );
			
		}
			/**
			 * Formats the paths.
			 * 
			 * This is necessary to check excluding paths because the user may pass paths with a forward slash but the system may use backslashes.
			 */
			private function _formatPaths( $asDirPaths ) {
				
				$_aFormattedDirPaths = array();
				$_aDirPaths = is_array( $asDirPaths ) ? $asDirPaths : array( $asDirPaths );
				foreach( $_aDirPaths as $_sPath ) {
					$_aFormattedDirPaths[] = str_replace( '\\', '/', $_sPath );
				}
				return $_aFormattedDirPaths;
				
			}
			/**
			 * The recursive version of the glob() function.
			 */
			private function doRecursiveGlob( $sPathPatten, $nFlags=0, array $aExcludeDirPaths=array(), array $aExcludeDirNames=array() ) {

				$_aFiles    = glob( $sPathPatten, $nFlags );	
				$_aFiles    = is_array( $_aFiles ) ? $_aFiles : array();	// glob() can return false.
				$_aDirs     = glob( dirname( $sPathPatten ) . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR|GLOB_NOSORT );
				$_aDirs     = is_array( $_aDirs ) ? $_aDirs : array();
				foreach ( $_aDirs as $_sDirPath ) {
					$_sDirPath  = str_replace( '\\', '/', $_sDirPath );
					if ( in_array( $_sDirPath, $aExcludeDirPaths ) ) { continue; }
					if ( in_array( pathinfo( $_sDirPath, PATHINFO_BASENAME ), $aExcludeDirNames ) ) { continue; }					
					$_aFiles    = array_merge( $_aFiles, $this->doRecursiveGlob( $_sDirPath . DIRECTORY_SEPARATOR . basename( $sPathPatten ), $nFlags, $aExcludeDirPaths ) );
					
				}
				return $_aFiles;
				
			}				
			/**
			 * Constructs the file pattern of the file extension part used for the glob() function with the given file extensions.
			 */
			private function _getGlobPatternExtensionPart( array $aExtensions=array( 'php', 'inc' ) ) {
				return empty( $aExtensions ) 
					? '*'
					: '{' . implode( ',', $aExtensions ) . '}';
			}

	/**
	 * Sets up the array consisting of class paths with the key of file name w/o extension.
	 */
	protected function _formatFileArray( array $_aFilePaths ) {
					
		/*
		 * Now the structure of $_aFilePaths looks like:
			array
			  0 => string '.../class/MyClass.php'
			  1 => string '.../class/MyClass2.php'
			  2 => string '.../class/MyClass3.php'
			  ...
		 * 
		 */		 
		$_aFiles = array();
		foreach( $_aFilePaths as $_sFilePath ) {
			
			$_sClassName	= pathinfo( $_sFilePath, PATHINFO_FILENAME );
			$_sPHPCode		= $this->getPHPCode( $_sFilePath );
			$_aFiles[ $_sClassName ] = array(	// the file name without extension will be assigned to the key
				'path'              =>	$_sFilePath,	
				'code'              =>	$_sPHPCode ? trim( $_sPHPCode ) : '',
				'dependency'        =>	$this->_getParentClass( $_sPHPCode ),
				'defined_classes'   =>	$this->_getDefinedClasses( $_sPHPCode ),
			); 

		}
		return $_aFiles;
			
	}
		/**
		 * Retrieves PHP code from the given path.
		 */
		protected function getPHPCode( $sFilePath ) {
			$_sCode = php_strip_whitespace( $sFilePath );
			$_sCode = preg_replace( '/^<\?php/', '', $_sCode );
			$_sCode = preg_replace( '/\?>\s+?$/', '', $_sCode );
			return $_sCode;
		}
					
		/**
		 * Returns an array holding class names defined in the given PHP code.
		 */
		protected function _getDefinedClasses( $sPHPCode ) {
			preg_match_all( '/(^|\s)class\s+(.+?)\s+/i', $sPHPCode, $aMatch ) ;
			return $aMatch[ 2 ];						
		}
		/**
		 * Returns the parent class
		 */
		protected function _getParentClass( $sPHPCode ) {
			if ( ! preg_match( '/class\s+(.+?)\s+extends\s+(.+?)\s+{/i', $sPHPCode, $aMatch ) ) {
				return null;	
			}
			return $aMatch[ 2 ];
		}			
			
	/**
	 * Generates the heading comment from the given path or class name.
	 */
	protected function _getHeaderComment( $aFiles, $aOptions )	 {

		if ( $aOptions['header_class_path'] && $aOptions['header_class_name'] ) {
			return $this->__getHeaderComment( 
				$aOptions['header_class_path'],
				$aOptions['header_class_name'],
				$aOptions['header_type']
			);				
		}
		
		if ( $aOptions['header_class_name'] ) {
			return $this->__getHeaderComment( 
				isset( $aFiles[ $aOptions['header_class_name'] ] ) ? $aFiles[ $aOptions['header_class_name'] ][ 'path' ] : $aOptions['header_class_path'],
				$aOptions['header_class_name'],
				$aOptions['header_type']
			);			
		} 
		
		if ( $aOptions['header_class_path'] ) {
			$_aDefinedClasses	= $this->_getDefinedClasses( $this->getPHPCode( $aOptions['header_class_path'] ) );
			$_sHeaderClassName	= isset( $_aDefinedClasses[ 0 ] ) ? $_aDefinedClasses[ 0 ] : '';
			return $this->__getHeaderComment( 
				$aOptions['header_class_path'],
				$_sHeaderClassName,
				$aOptions['header_type']
			);			
		}	
	
	}	
		/**
		 * Generates the script heading comment.
		 */
		protected function __getHeaderComment( $sFilePath, $sClassName, $sHeaderType='DOCKBLOCK' ) {

			if ( ! file_exists( $sFilePath ) ) { return ''; }
			if ( ! $sClassName ) { return ''; }

			include_once( $sFilePath );
			$_aDeclaredClasses = ( array ) get_declared_classes();
			foreach( $_aDeclaredClasses as $_sClassName ) {
				if ( $sClassName !== $_sClassName ) { continue; }
				return 'DOCBLOCK' === $sHeaderType
					? $this->_getClassDocBlock( $_sClassName )
					: $this->_generateHeaderComment( $_sClassName );
			}
			return '';
		
		}
		/**
		 * Generates the heading comments from the class constants.
		 */
		protected function _generateHeaderComment( $sClassName ) {
			
			$_oRC           = new ReflectionClass( $sClassName );
			$_aConstants    = $_oRC->getConstants() + array(
				'Name'          => '', 'Version'        =>	'',
				'Description'   => '', 'URI'            =>	'',
				'Author'        => '', 'AuthorURI'      =>	'',
				'Copyright'     => '', 'License'        =>	'',
				'Contributors'  => '',
			);
			$_aOutputs      = array();
			$_aOutputs[]    = '/' . '**' . PHP_EOL;
			$_aOutputs[]    = "\t" . $_aConstants['Name'] . ' '
				. ( $_aConstants['Version']	? 'v' . $_aConstants['Version'] . ' '  : '' ) 
				. ( $_aConstants['Author']	? 'by ' . $_aConstants['Author'] . ' ' : ''  )
				. PHP_EOL;
			$_aOutputs[]    = $_aConstants['Description']	? "\t". $_aConstants['Description'] . PHP_EOL : '';
			$_aOutputs[]    = $_aConstants['URI'] 			? "\t". '<' . $_aConstants['URI'] . '>' . PHP_EOL : '';
			$_aOutputs[]    = "\t" . $_aConstants['Copyright']
				. ( $_aConstants['License']	? '; Licensed under ' . $_aConstants['License'] : '' );
			$_aOutputs[]    = ' */' . PHP_EOL;
			return implode( '', array_filter( $_aOutputs ) );
		}	
		/**
		 * Returns the docblock of the specified class
		 */
		protected function _getClassDocBlock( $sClassName ) {
			$_oRC = new ReflectionClass( $sClassName );
			return trim( $_oRC->getDocComment() );
		}

}