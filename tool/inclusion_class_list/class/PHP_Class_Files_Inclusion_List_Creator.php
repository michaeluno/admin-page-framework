<?php
/**
 * PHP Class Files Inclusion List Creator
 * 
 * @author		Michael Uno <michael@michaeluno.jp>
 * @copyright	2013-2014 (c) Michael Uno
 * @license		MIT	<http://opensource.org/licenses/MIT>
 */
if ( ! class_exists( 'PHP_Class_Files_Script_Generator_Base' ) ) {
	require( dirname( dirname( dirname( __FILE__ ) ) ) . '/php_class_files_script_generator/PHP_Class_Files_Script_Generator_Base.php' );	
}

/**
 * Creates a PHP file that defines an array holding file path with the class key.
 * 
 * This is meant to be used for the callback function for the spl_autoload_register() function.
 *  
 * @remark		The parsed class file must have a name of the class defined in the file.
 * @version		1.0.2
 */
class PHP_Class_Files_Inclusion_Script_Creator extends PHP_Class_Files_Script_Generator_Base {
	
	static protected $_aStructure_Options = array(
	
		'header_class_name'		=>	'',
		'header_class_path'		=>	'',
		'output_buffer'			=>	true,
		'header_type'			=>	'DOCBLOCK',	
		'exclude_classes'		=>	array(),
		'base_dir_var'			=>	'',
		'output_var_name'		=>	'$aClassFiles',
		
		// Search options
		'search'	=>	array(
			'allowed_extensions'	=>	array( 'php' ),	// e.g. array( 'php', 'inc' )
			'exclude_dir_paths'		=>	array(),
			'exclude_dir_names'		=>	array(),
			'is_recursive'			=>	true,
		),
		
	);
	
	/**
	 * @param		string			$sBaseDirPath			The base directory path that the inclusion path is relative to.
	 * @param		string|array	$asScanDirPaths			The target directory path(s).
	 * @param		string			$sOutputFilePath		The destination file path.
	 * @param		array			$aOptions				The options array. It takes the following arguments.
	 *  - 'header_class_name'	: string	the class name that provides the information for the heading comment of the result output of the minified script.
	 *  - 'header_class_path'	: string	(optional) the path to the header class file.
	 *  - 'output_buffer'		: boolean	whether or not output buffer should be printed.
	 *  - 'header_type'			: string	whether or not to use the docBlock of the header class; otherwise, it will parse the constants of the class. 
	 *  - 'exclude_classes' 	: array		an array holding class names to exclude.
	 *  - 'base_dir_var'		: string	the variable or constant name that is prefixed before the inclusion path.
	 *  - 'search'				: array		the arguments for the directory search options.
	 * 	The accepted values are 'CONSTANTS' or 'DOCBLOCK'.
	 * <h3>Example</h3>
	 * <code>array(
	 *		'header_class_name'	=>	'HeaderClassForMinifiedVerions',
	 *		'file_pettern'	=>	'/.+\.(php|inc)/i',
	 *		'output_buffer'	=>	false,
	 *		'header_type'	=>	'CONSTANTS',
	 * 		
	 * )</code>
	 * 
	 * When false is passed to the 'use_docblock' argument, the constants of the header class must include 'Version', 'Name', 'Description', 'URI', 'Author', 'CopyRight', 'License'. 
	 * <h3>Example</h3>
     * <code>class TaskScheduler_Registry_Base {
     *         const VERSION        = '1.0.0b08';
     *         const NAME           = 'Task Scheduler';
     *         const DESCRIPTION    = 'Provides an enhanced task management system for WordPress.';
     *         const URI            = 'http://en.michaeluno.jp/';
     *         const AUTHOR         = 'miunosoft (Michael Uno)';
     *         const AUTHOR_URI     = 'http://en.michaeluno.jp/';
     *         const COPYRIGHT      = 'Copyright (c) 2014, <Michael Uno>';
     *         const LICENSE        = 'GPL v2 or later';
     *         const CONTRIBUTORS   = '';
     * }</code>
     */
	public function __construct( $sBaseDirPath, $asScanDirPaths, $sOutputFilePath, array $aOptions=array() ) {

		$aOptions			= $aOptions + self::$_aStructure_Options;
		$aOptions['search']	= $aOptions['search'] + self::$_aStructure_Options['search'];
		
		$_sCarriageReturn	= php_sapi_name() == 'cli' ? PHP_EOL : '<br />';
		$_aScanDirPaths		= ( array ) $asScanDirPaths;
		if ( $aOptions['output_buffer'] ) {
			echo 'Searching files under the directories: ' . implode( ', ', $_aScanDirPaths ) . $_sCarriageReturn;
		}
		
		/* Store the file contents into an array. */
		$_aFilePaths	= $this->_getFileLists( $_aScanDirPaths, $aOptions['search'] );	
		$_aFiles		= $this->_formatFileArray( $_aFilePaths );
		unset( $_aFiles[ pathinfo( $sOutputFilePath, PATHINFO_FILENAME ) ] );	// it's possible that the minified file also gets loaded but we don't want it.

		if ( $aOptions['output_buffer'] ) {
			echo sprintf( 'Found %1$s file(s)', count( $_aFiles ) ) . $_sCarriageReturn;
		}			
	
		/* Generate the output script header comment */
		$_sHeaderComment = $this->_getHeaderComment( $_aFiles, $aOptions );
		if ( $aOptions['output_buffer'] ) {
			echo( $_sHeaderComment ) . $_sCarriageReturn;
		}
	
		/* Sort the classes - in some PHP versions, parent classes must be defined before extended classes. */
		$_aFiles = $this->sort( $_aFiles, $aOptions['exclude_classes'] );
		
		if ( $aOptions['output_buffer'] ) {
			echo sprintf( 'Sorted %1$s file(s)', count( $_aFiles ) ) . $_sCarriageReturn;			
		}				
		
		/* Write to a file */
		$this->write( $_aFiles, $sBaseDirPath, $sOutputFilePath, $_sHeaderComment, $aOptions['output_var_name'], $aOptions['base_dir_var'] );
		
	}
							
	public function sort( array $aFiles, array $aExcludingClassNames ) {
		
		foreach( $aFiles as $_sClassName => $_aFile ) {
			if ( in_array( $_sClassName, $aExcludingClassNames ) ) {
				unset( $aFiles[ $_sClassName ] );
			}
		}
		
		$aFiles = $this->_extractDefinedClasses( $aFiles, $aExcludingClassNames );
		
		return $aFiles;
	
	}
		private function _extractDefinedClasses( array $aFiles, array $aExcludingClassNames ) {
			
			$_aAdditionalClasses = array();
			foreach( $aFiles as $_sClassName => $_aFile ) {
				foreach( $_aFile['defined_classes'] as $_sAdditionalClass ) {
					if ( isset( $aFiles[ $_sAdditionalClass ] ) ) { 
                        continue; 
                    }
                    if ( in_array( $_sAdditionalClass, $aExcludingClassNames ) ) {
                        continue;
                    }                    
					$_aAdditionalClasses[ $_sAdditionalClass ] = $_aFile;
				}
			}
			return $aFiles + $_aAdditionalClasses;
			
		}
			
	public function write( array $aFiles, $sBaseDirPath, $sOutputFilePath, $sHeadingComment, $sOutputArrayVar, $sBaseDirVar ) {
			
		$_aData		= array();
		
		// Create a heading.
		$_aData[]	= mb_convert_encoding( '<?php ' . PHP_EOL . $sHeadingComment, 'UTF-8', 'auto' );
		
		// Start array declaration
		$_aData[]	= $sOutputArrayVar . ' = array( ' . PHP_EOL;
			
		// Insert the data
		foreach( $aFiles as $_sClassName => $_aFile ) {					
			$_sPath		= str_replace('\\', '/', $_aFile['path'] );
			$_sPath		= $this->_getRelativePath( $sBaseDirPath, $_sPath );
			$_aData[]	= "\t" . '"' . $_sClassName . '"' . "\t" . '=>' 
				. "\t" . $sBaseDirVar . ' . "' . $_sPath . '", ' . PHP_EOL;
		}
		
		// Close the array declaration
		$_aData[]	= ');' . PHP_EOL;
		
		// Remove the existing file.
		if ( file_exists( $sOutputFilePath ) ) {
			unlink( $sOutputFilePath );
		}
		
		// Write to a file.
		file_put_contents( 
            $sOutputFilePath, 
            trim( implode( '', $_aData ) ), 
            FILE_APPEND | LOCK_EX
        );
		
	}
	
		/**
		 * Calculates the relative path from the given path.
		 * 
		 */
		private function _getRelativePath( $from, $to ) {
			
			// some compatibility fixes for Windows paths
			$from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
			$to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
			$from = str_replace('\\', '/', $from);
			$to   = str_replace('\\', '/', $to);

			$from     = explode('/', $from);
			$to       = explode('/', $to);
			$relPath  = $to;

			foreach($from as $depth => $dir) {
				// find first non-matching dir
				if($dir === $to[$depth]) {
					// ignore this directory
					array_shift($relPath);
				} else {
					// get number of remaining dirs to $from
					$remaining = count($from) - $depth;
					if($remaining > 1) {
						// add traversals up to first matching dir
						$padLength = (count($relPath) + $remaining - 1) * -1;
						$relPath = array_pad($relPath, $padLength, '..');
						break;
					} else {
						$relPath[0] = './' . $relPath[0];
					}
				}
			}
			
			$relPath = implode( '/', $relPath );
			return ltrim( $relPath, '.' );
		}	

}