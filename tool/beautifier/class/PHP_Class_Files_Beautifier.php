<?php
/**
 * PHP Class Files Beautifier
 * 
 * @author      Michael Uno <michael@michaeluno.jp>
 * @copyright   2015 (c) Michael Uno
 * @license     MIT    <http://opensource.org/licenses/MIT>
 */
if ( ! class_exists( 'PHP_Class_Files_Script_Generator_Base' ) ) {
    require( dirname( dirname( dirname( __FILE__ ) ) ) . '/php_class_files_script_generator/PHP_Class_Files_Script_Generator_Base.php' );
}

/**
 * Copies files in a specified directory into a set destination directory and applies beautification.
 * 
 * @version    1.1.0
 */
class PHP_Class_Files_Beautifier extends PHP_Class_Files_Script_Generator_Base {
    
    static protected $_aStructure_Options = array(
    
        'header_class_name' => '',
        'header_class_path' => '',        
        'output_buffer'     => true,
        'character_encode'  => 'UTF-8',     

        'header_type'       => 'DOCBLOCK',    
        'exclude_classes'   => array(),
        'css_heredoc_keys'  => array( 'CSSRULES' ),     
        'js_heredoc_keys'   => array( 'JAVASCRIPTS' ),  
        'carriage_return'   => PHP_EOL,
        
        /**
         * Whether or not to combine files in the same directory with hierarchical relationships. 
         * 
         * For example, say there are A.php defining class A extends A_Base {} and A_Base.php defining A_Base {} in the same directory,
         * A.php will include the definition of A_Base in the same file and A_Base.php will be omitted.
         * 
         * This helps to reduce time for loading files and improve performance when using auto-loader. 
         * @since       1.1.0
         */
        'combine' => array(
            'inheritance'       => true,
            'exclude_classes'   => array(),
        ),
        
        // Search options
        'search'    =>    array(
            'allowed_extensions' => array( 'php' ),    // e.g. array( 'php', 'inc' )
            'exclude_dir_paths'  => array(),
            'exclude_dir_names'  => array(),
            'exclude_file_names' => array(),
            'is_recursive'       => true,
        ),        
        
    );
        
    /**
     * Stores current iterated class name.
     * 
     * Currently only used in the loop of the JavaScript minifier.
     * 
     * @since       ? 1.1.0
     */
    private $_sCurrentIterationClassName;
    
    /**
     * Stores the output file path.
     * @since       1.0.0
     */
    public $sDestinationDirPath;    
    
    /**
     * Stores the header comment to insert at the top of the script.
     * @since       1.0.0
     */
    public $sHeaderComment;

    /**
     * Stores the scanned files.
     * @since       1.0.0
     */    
    public $aFiles = array();
    
    /**
     * 
     * @param string    $sSourceDirPath     The target directory path.
     * @param string    $sDestinationDirPath    The destination file path.
     * @param array     $aOptions           The options array. It takes the following arguments.
     *  - 'header_class_name'   : string    the class name that provides the information for the heading comment of the result output of the minified script.
     *  - 'header_class_path'   : string    (optional) the path to the header class file.
     *  - 'output_buffer'       : boolean    whether or not output buffer should be printed.
     *  - 'header_type'         : string    whether or not to use the docBlock of the header class; otherwise, it will parse the constants of the class. 
     *  - 'exclude_classes'     : array        an array holding class names to exclude.
     *  - 'css_heredoc_keys'    : (array, optional) an array holding heredoc keywords used to assign CSS rules to a variable.
     *  - 'js_heredoc_keys'     : (array, optional) an array holding heredoc keywords used to assign JavaScript scripts to a variable.
     *  - 'combine' : (array, optional) [1.1.0+]  Combine option
     *      - 'inheritance' : (boolean) Whether or not to combine files in the same directory with hierarchical relationships. 
     *      - 'exclude_classes' : (string|array, optional)  Class names to exclude from combining.
     *  - 'search'              : array        the arguments for the directory search options.
     *     The accepted values are 'CONSTANTS' or 'DOCBLOCK'.
     * <h3>Example</h3>
     * <code>array(
     *        'header_class_name' => 'HeaderClassForMinifiedVerions',
     *        'file_pettern'      => '/.+\.(php|inc)/i',
     *        'output_buffer'     => false,
     *        'header_type'       => 'CONSTANTS',
     * 
     * )</code>
     * 
     * When false is passed to the 'use_docblock' argument, the constants of the header class must include 'VERSION', 'NAME', 'DESCRIPTION', 'URI', 'AUTHOR', 'COPYRIGHT', 'LICENSE'. 
     * <h3>Example</h3>
     * <code>class TaskScheduler_Registry_Base {
     *         const VERSION       = '1.0.0b08';
     *         const NAME          = 'Task Scheduler';
     *         const DESCRIPTION   = 'Provides an enhanced task management system for WordPress.';
     *         const URI           = 'http://en.michaeluno.jp/';
     *         const AUTHOR        = 'miunosoft (Michael Uno)';
     *         const AUTHOR_URI    = 'http://en.michaeluno.jp/';
     *         const COPYRIGHT     = 'Copyright (c) 2014, <Michael Uno>';
     *         const LICENSE       = 'GPL v2 or later';
     *         const CONTRIBUTORS  = '';
     * }</code>
     */
    public function __construct( $sSourceDirPath, $sDestinationDirPath, array $aOptions=array() ) {
        
        $aOptions  = $this->_getOptionsFormatted( $aOptions );

        $_sTempDirPath = $this->createTempDir();
        if ( ! $_sTempDirPath ) {
            $this->output(
                'Failed to create a temporary directory: ' . $sSourceDirPath,
                $aOptions
            );           
            return;                        
        }
        
        $_bSuccess = $this->xcopy(
            $sSourceDirPath, 
            $_sTempDirPath, 
            0755,
            $aOptions[ 'search' ]
        );
        if ( ! $_bSuccess ) {
            $this->output(
                'Failed to copy the directory: ' . $sSourceDirPath,
                $aOptions
            );           
            return;            
        }
        $this->output(
            'Searching files under the directory: ' . $sSourceDirPath,
            $aOptions
        );
        
        $_aFiles = $this->_formatFileArray(
            $this->_getFileLists( 
                $_sTempDirPath, 
                $aOptions[ 'search' ]
            )
        );
        $this->output(
            sprintf( 'Found %1$s file(s)', count( $_aFiles ) ),
            $aOptions
        );
 
        // Generate the output script header comment.
        $aOptions[ 'header_comment' ] = trim( $this->_getHeaderComment( array(), $aOptions ) );
        $this->output( 'File Header', $aOptions );        
        $this->output(
            $aOptions[ 'header_comment' ],
            $aOptions
        );
        
        // Retrieve file contents.
  
        // Combine files.
        $_aFiles = $this->_getCombinedFiles( $_aFiles, $aOptions );
        
        // Apply the beautifier 
        $_aFiles = $this->_getBeautifiedFiles( $_aFiles, $aOptions );
   
        $this->_createFiles( 
            $_aFiles,               // parsing files
            $_sTempDirPath,         // temporary directory path
            $sDestinationDirPath,   // destination directory path
            $aOptions 
        );
        
    }
        /**
         * Formats the given options array
         * @since       1.0.0
         * @return      array
         */
        private function _getOptionsFormatted( array $aOptions ) {
            $aOptions                      = $aOptions + self::$_aStructure_Options;
            $aOptions[ 'search' ]          = $aOptions['search'] + self::$_aStructure_Options[ 'search' ];
            $aOptions[ 'search' ][ 'exclude_dir_paths' ] = $this->_formatPaths( $aOptions[ 'search' ][ 'exclude_dir_paths' ] );
            $aOptions[ 'carriage_return' ] = php_sapi_name() == 'cli' 
                ? PHP_EOL 
                : '<br />';
            $aOptions[ 'combine' ]         = $aOptions[ 'combine' ] + self::$_aStructure_Options[ 'combine' ];
            $aOptions[ 'combine' ][ 'exclude_classes' ] = is_array( $aOptions[ 'combine' ][ 'exclude_classes' ] )
                ? $aOptions[ 'combine' ][ 'exclude_classes' ]
                : array( $aOptions[ 'combine' ][ 'exclude_classes' ] );
            return $aOptions;
        }    


    /**
     * @return      array
     * @since       1.1.0
     */
    private function _getCombinedFiles( $aFiles, $aOptions ) {
        
        $_aCombineOptions = $aOptions[ 'combine' ];
        if ( ! $_aCombineOptions[ 'inheritance' ] ) {
            return $aFiles;
        }
        
        $_aNew = array();
        $_aClassNamesToRemove = array();
        foreach( $aFiles as $_sClassName => $_aFile ) {
            
            $_sParentClassName = $_aFile[ 'dependency' ];
            
            // If it does not extend any, do nothing.
            if ( empty( $_sParentClassName ) ) {
                $_aNew[ $_sClassName ] = $_aFile;
                continue;
            }
            // For parent classes which do not belong to the project,
            if ( ! isset( $aFiles[ $_sParentClassName ] ) ) {
                $_aNew[ $_sClassName ] = $_aFile;
                continue;                
            }
            if ( in_array( $_sClassName, $_aCombineOptions[ 'exclude_classes' ] ) ) {
                $_aNew[ $_sClassName ] = $_aFile;
                continue;
            }
            // If it is a parent of another class, do nothing.
            if ( $this->_isParent( $_sClassName, $aFiles, true, $_aCombineOptions[ 'exclude_classes' ] ) ) {
                $_aNew[ $_sClassName ] = $_aFile;
                continue;                                
            }
            
            // At this point, the parsing item is the most extended class in the same directory.
            
            // Combine code
            $_sThisCode = $_aFile[ 'code' ];
            foreach( $this->_getAncestorClassNames( $_sClassName, $aFiles, true ) as $_sAnscestorClassName ) {
                // Insert the parent code at the top of the code of the parsing file
                $_sThisCode = $aFiles[ $_sAnscestorClassName ][ 'code' ] . ' ' . $_sThisCode;
                unset( $aFiles[ $_sAnscestorClassName ] );
                $_aClassNamesToRemove[] = $_sAnscestorClassName;
            }
            $_aFile[ 'code' ] = $_sThisCode;
            
            // Add it to the new array
            $_aNew[ $_sClassName ] = $_aFile;
            
        }

        // Remove combined items
        foreach ( $_aClassNamesToRemove as $_sClassNameToRemove ) {
            unset( $_aNew[ $_sClassNameToRemove ] );
        }
        
        return $_aNew;
        
    }
        /**
         * Checks if there is a class extending the subject class in the project files.
         * @since       1.1.0
         * @return      boolean
         */
        private function _isParent( $sClassName, $aFiles, $bOnlyInTheSameDirectory=true, array $aExcludingClassNames=array() ) {
            
            $_sSubjectDirPath = dirname( $aFiles[ $sClassName ][ 'path' ] );
            foreach( $aFiles as $_sClassName => $_aFile ) {
    
                if ( in_array( $_sClassName, $aExcludingClassNames ) ) {
                    continue;
                }
            
                if ( $bOnlyInTheSameDirectory && $_sSubjectDirPath !== dirname( $_aFile[ 'path' ] ) ) {
                    continue;
                }
                
                if ( $sClassName === $_aFile[ 'dependency' ] ) {
                    return true;
                }
            }
            return false;
            
        }
        /**
         * @remark      The closet ancestor (the direct parent) will come first and the farthest one goes the last in the array 
         * The order is important as the their contents will be appended to the subject class code. And in some PHP versions,
         * parent classes must be written before its child class; otherwise. it causes a fatal error.
         * @since       1.1.0
         * @return      array
         */
        private function _getAncestorClassNames( $sClassName, &$aFiles, $bOnlyInTheSameDirectory=true ) {
            
            $_aAncestors = array();
            
            $_sParentClass = isset( $aFiles[ $sClassName ] )
                ? $aFiles[ $sClassName ][ 'dependency' ]
                : '';
            // Make sure the retrieved parent one also belongs to the project files.
            $_sParentClass = isset( $aFiles[ $_sParentClass ] ) 
                ? $_sParentClass
                : '';
            if ( ! $_sParentClass ) {
                return $_aAncestors;
            }
            
            // Add the parent class to the returining array.
            if ( $bOnlyInTheSameDirectory ) {
                $_sThisDirPath        = dirname( $aFiles[ $sClassName ][ 'path' ] );
                $_sParentClassDirPath = dirname( $aFiles[ $_sParentClass ][ 'path' ] );
                if ( $_sThisDirPath !== $_sParentClassDirPath ) {                    
                    return $_aAncestors;
                }
            } 
            
            $_aAncestors[] = $_sParentClass;
            
            $_aAncestors = array_merge(
                $_aAncestors,   // for numeric numeric items, the first parameter items will come first
                $this->_getAncestorClassNames( $_sParentClass, $aFiles, $bOnlyInTheSameDirectory )
            );
            
            return $_aAncestors;
        }
        
    
    /**
     * @return      array
     * @since       1.1.0
     */
    private function _getBeautifiedFiles( $aFiles, $aOptions ) {
        
        if ( ! $this->_isBeautifierSet( $aOptions ) ) {
            return $aFiles;
        }
        
        $_sCR = $aOptions[ 'carriage_return' ];
        $aOptions[ 'carriage_return' ] = '';
        $this->output( 'Beautifying PHP code.', $aOptions );
        
        $_aNew   = array();
        $_iCount = 0;
        foreach( $aFiles as $_sClassName => $_aFile  ) {
            $_aFile[ 'code' ] = $this->_getBeautifiedCode( 
                $_aFile[ 'code' ],
                $aOptions[ 'header_comment' ]
            );
            $_aNew[ $_sClassName ] = $_aFile;
            $this->output( '.', $aOptions );
        }
        $this->output( $_sCR, $aOptions );
        return $_aNew;
        
    }
        /**
         * @return      boolean
         */
        private function _isBeautifierSet( $aOptions ) {
        
            if ( ! function_exists( 'token_get_all' ) ) {
                $this->output(
                    'Warning: The Tokenizer PHP extension needs to be installed to beautify PHP code.',
                    $aOptions
                );
                return false;
            }
            
            // Find Beautifier.php in ./library/PHP_Beautifier/ directory.
            $_sBeautifierPath = $this->_getBeautifierPath( $aOptions );
            if ( ! file_exists( $_sBeautifierPath ) ) {
                $this->output(
                    'Warning: The PHP_Beautifier needs to be placed in ./library/PHP_Beautifier directory.',
                    $aOptions
                );
                return false;            
            }
            
            // Perform beautification.
            include_once( $_sBeautifierPath );        
            return true;
        
        }
        
        /**
         * Beautifies PHP code with the PHP_Beautify library.
         * 
         * @since       1.1.0
         * @see         http://beautifyphp.sourceforge.net/docs/
         * @see         http://beautifyphp.sourceforge.net/docs/PHP_Beautifier/tutorial_PHP_Beautifier.howtouse.script.pkg.html
         */
        private function _getBeautifiedCode( $sCode, $sHeaderComment='' ) {
                        
            // Set up a beautified object.
            $_oBeautifier = new PHP_Beautifier(); 
            $_oBeautifier->setIndentChar(' ');  
            $_oBeautifier->setIndentNumber( 4 );
            $_oBeautifier->setNewLine( "\n" );
            
            // PHP_Beautifier needs the beginning < ?php notation. The passed code is already formatted and the notation is removed.
            $sCode = '<?php ' . trim( $sCode );  
            $_oBeautifier->setInputString( $sCode );
            $_oBeautifier->process();
            
            $sCode = $_oBeautifier->get();
            
            $sCode = trim( $sCode );    // remove trailing line-feed.
            $sCode = preg_replace( 
                '/^<\?php\s+?/', 
                '<?php ' . PHP_EOL . $sHeaderComment . PHP_EOL,
                $sCode 
            ); // File comment header
            return $sCode;
            
        }    
        
    /**
     * Writes contents to files.
     * @since       1.1.0
     * @return      void
     */
    private function _createFiles( array $aFiles, $sTempDirPath, $sDestinationDirPath, array $aOptions ) {
        
        // Make sure to remove old files.
        $this->deleteDir( $sDestinationDirPath );
        $this->output(
            'Deleting: ' . $sDestinationDirPath,
            $aOptions
        );  
        
        // Create files.
        foreach( $aFiles as $_sClassName => $_aFile ) {
            $this->_write( 
                $this->_getDestinationFilePathFromTempPath( $sDestinationDirPath, $sTempDirPath, $_aFile[ 'path' ] ),
                $_aFile[ 'code' ] 
            );
        }
        
        // Make sure to delete the used files.
        $this->deleteDir( $sTempDirPath );
        
    }
        /**
         * @return      string
         * @since       1.1.0
         */
        private function _getDestinationFilePathFromTempPath( $sDestinationDirPath, $sTempDirPath, $sFilePath ) {           
            return $this->_getAbsolutePathFromRelative( 
                $sDestinationDirPath, 
                $this->_getRelativePath( $sTempDirPath, $sFilePath )
            );
        }
            /**
             * @since       1.1.0
             */
            private function _getAbsolutePathFromRelative( $sPrefix, $sRelativePath ) {
                
                // removes the heading ./ or .\ 
                $sRelativePath  = preg_replace( "/^\.[\/\\\]/", '', $sRelativePath, 1 );    
                
                // APSPATH has a trailing slash.
                return rtrim( $sPrefix, '/\\' ) . '/' . ltrim( $sRelativePath,'/\\' );    
                
            }        
            /**
             * Calculates the relative path from the given path.
             * 
             * This function is used to generate a template path.
             * 
             * @author            Gordon
             * @see               http://stackoverflow.com/questions/2637945/getting-relative-path-from-absolute-path-in-php/2638272#2638272
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
                return implode('/', $relPath);
                
            }
    
    /**
     * Beautifies the PHP code.
     * 
     * @since       1.0.0
     * @deprecated
     */
  /*   public function beautify( array $aFiles, array $aOptions ) {
         
        if ( ! function_exists( 'token_get_all' ) ) {
            $this->output(
                'Warning: The Tokenizer PHP extension needs to be installed to beautify PHP code.',
                $aOptions
            );
            return $aFiles;
        }
        
        // Find Beautifier.php in ./library/PHP_Beautifier/ directory.
        $_sBeautifierPath = $this->_getBeautifierPath( $aOptions );
        if ( ! file_exists( $_sBeautifierPath ) ) {
            $this->output(
                'Warning: The PHP_Beautifier needs to be placed in ./library/PHP_Beautifier directory.',
                $aOptions
            );
            return $aFiles;            
        }
        
        // Perform beautification.
        include_once( $_sBeautifierPath );        
        $this->output(
            'Beautifying PHP code.',
            $aOptions
        );
        foreach( $aFiles as $_sFilePath ) {
            $this->_beautifyPHPFile( $_sFilePath, $aOptions[ 'header_comment' ] );
        }

        
    }    */
    
        /**
         * Beautifies PHP code with the PHP_Beautify library.
         * 
         * @since       1.0.0
         * @see         http://beautifyphp.sourceforge.net/docs/
         * @see         http://beautifyphp.sourceforge.net/docs/PHP_Beautifier/tutorial_PHP_Beautifier.howtouse.script.pkg.html
         * @deprecated
         */
     /*    private function _beautifyPHPFile( $sFilePath, $sHeaderComment='' ) {
                        
            // Create the instance
            $_oBeautifier = new PHP_Beautifier(); 
         
            // Set the indent char, number of chars to indent and newline char
            $_oBeautifier->setIndentChar(' ');
            $_oBeautifier->setIndentNumber( 4 );
            $_oBeautifier->setNewLine( "\n" );
            
            // Set the paths
            // $_oBeautifier->setInputFile( $sFilePath );
            
            $_sCode = php_strip_whitespace( $sFilePath );
            
            // PHP_Beautifier needs the beginning < ?php notation. So add it for parsing and remove it after that.       
            $_sCode = preg_replace( '/^<\?php/', '<?php ' . PHP_EOL . $sHeaderComment, $_sCode );
            $_oBeautifier->setInputString( trim( $_sCode ) );
            
            $_oBeautifier->setOutputFile( $sFilePath ); 
            
            // Process the file. DON'T FORGET TO USE IT
            $_oBeautifier->process();
            
            // The save() method causes a line break to be embedded at the end
            // $_oBeautifier->save(); 
            
            $_sCode = $_oBeautifier->get(); 
            $this->_write( $sFilePath, trim( $_sCode ) );
            
            return;
            
        } */
            
            private function _write( $sFilePath, $sData ) {
                
                // Remove the existing file.
                if ( file_exists( $sFilePath ) ) {
                    unlink( $sFilePath );
                }   
                
                // Make sure the parent directory exists.                
                $_sDirPath = dirname( $sFilePath );
                if ( ! is_dir( $_sDirPath ) ) {
                    mkdir( 
                        $_sDirPath,  
                        0777,       // chmod
                        true        // recursive
                    );
                }                
                
                // Write to a file.
                file_put_contents( $sFilePath, $sData, FILE_APPEND | LOCK_EX );                
                
            }
        
        /**
         * Returns the path of Beautifier.php.
         * @since   1.0.0
         */
        private function _getBeautifierPath( array $aOptions=array() ) {
            
            $_sPath = $this->_findBeautifierPath();
            if ( $_sPath ) {
                return $_sPath;
            }
            
            // Download it.
            $_bDownloaded = $this->_downloadZip( 
                "https://github.com/clbustos/PHP_Beautifier/archive/master.zip",
                dirname( __FILE__ ) . '/library/PHP_Beautifier/php_beautifier.zip',
                $aOptions
            );
            if ( ! $_bDownloaded ) {                
                return '';
            }
        
            $this->_unZip(
                dirname( __FILE__ ) . '/library/PHP_Beautifier/php_beautifier.zip',
                $aOptions            
            );            
            
            $_sPath = $this->_findBeautifierPath();
            return $_sPath
                ? $_sPath
                : '';
            
        }
            /**
             * Attempts to find the the beutifier path.
             * @since       1.0.0
             */
            private function _findBeautifierPath() {
                
                // Scan the 'library' directory and return the script path if found.
                $_aScannedFiles = $this->_formatFileArray( 
                    $this->_getFileLists( 
                        dirname( __FILE__ ) . '/library', 
                        self::$_aStructure_Options['search'] 
                    )
                );
                if ( isset( $_aScannedFiles['Beautifier']['path'] ) ) {
                    return $_aScannedFiles['Beautifier']['path'];
                } 
             
            }
            /**
             * 
             */
            private function _unzip( $sFilePath, array $aOptions=array() ) {
                
                if ( ! class_exists( 'ZipArchive' ) ) {
                    if ( $aOptions['output_buffer'] ) {
                        echo "The zlib PHP extension is required to extract zip files." . $aOptions['carriage_return'];
                    }                                        
                    return;
                }
                
                /* Open the Zip file */
                $_oZip = new ZipArchive;
                if( $_oZip->open( $sFilePath ) != "true" ) {
                    if ( $aOptions['output_buffer'] ) {
                        echo "Error :- Unable to open the Zip File" . $aOptions['carriage_return'];
                    }                          
                } 
                
                /* Extract Zip File */
                $_oZip->extractTo( dirname( $sFilePath ) );
                $_oZip->close();                
                
            }
            /**
             * Downloads the given url.
             * @since       1.0.0
             */
            private function _downloadZip( $sURL, $sFilePath, array $aOptions=array() ) {
                
                // The cURL extension is required.
                if ( ! function_exists( 'curl_init' ) ) {
                    
                    if ( $aOptions['output_buffer'] ) {
                        echo 'To download a file, the cURL PHP extension needs to be installed. You are using PHP ' . PHP_VERSION . '.' . $aOptions['carriage_return'];
                    }                    
                    return false;
                }
                
                // Create the directory if not exists.
                $_sDirPath = dirname( $sFilePath );
                if ( ! is_dir( $_sDirPath ) ) {
                    mkdir( $_sDirPath, 0777, true );
                }                
      
                // Remove the existing file.
                if ( file_exists( $sFilePath ) ) {
                    unlink( $sFilePath );
                }   
                
                $sURL = $this->_getRedirectedURL( $sURL );
                
                $_hZipResource = fopen( $sFilePath , "w" );
                    if ( $aOptions['output_buffer'] ) {
                        echo 'Downloading PHP Beautifier.' . $aOptions['carriage_return'];
                    }                          
                // Get The Zip File From Server
                $ch = curl_init();
                curl_setopt( $ch, CURLOPT_URL, $sURL );
                curl_setopt( $ch, CURLOPT_FAILONERROR, true );
                curl_setopt( $ch, CURLOPT_HEADER, 0);
                curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
                curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
                curl_setopt( $ch, CURLOPT_BINARYTRANSFER,true );
                curl_setopt( $ch, CURLOPT_TIMEOUT, 10);
                curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
                curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 ); 
                curl_setopt( $ch, CURLOPT_FILE, $_hZipResource );
                $page = curl_exec( $ch );
                if( ! $page && $aOptions['output_buffer'] ) {
                    echo "Download Error : " . curl_error( $ch ) . $aOptions['carriage_return'];
                    curl_close( $ch );            
                    return false;
                }
                curl_close( $ch );                
                return true;
            }
            
            /**
             * Returns the final destination of redirected URL.
             * 
             * @since   1.0.0  
             */
            private function _getRedirectedURL( $sURL ) {
                
                $ch = curl_init( $sURL );
                curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects
                curl_setopt( $ch, 
                    CURLOPT_USERAGENT, 
                    'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.7 '
                    . '(KHTML, like Gecko) Chrome/7.0.517.41 Safari/534.7'  // imitate chrome
                ); 
                curl_setopt( $ch, CURLOPT_NOBODY, true ); // HEAD request only (faster)
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // don't echo results
                curl_exec( $ch );
                $_sFinalURL = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL ); // get last URL followed
                curl_close($ch);
                
                return $_sFinalURL
                    ? $_sFinalURL
                    : $sURL;
                
            }       
  
    /**
     * Copy a file, or recursively copy a folder and its contents
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @param       string   $permissions New folder creation permissions
     * @return      bool     Returns true on success, false on failure
     */
    public function xcopy( $source, $dest, $permissions = 0755, array $aOptions=array() ) {
        // Check for symlinks
        if ( is_link( $source ) ) {
            return symlink( readlink( $source ), $dest );
        }

        // Simple copy for a file
        if ( is_file( $source ) ) {
            return $this->_copyFile( $source, $dest, $aOptions );
        }

        // Make destination directory
        if ( ! is_dir( $dest ) ) {
            if ( ! $this->isInExcludeList( $dest, $aOptions ) ) {
                mkdir( $dest, $permissions );
            } 
        }
        
        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            
            // Skip pointers
            if ( $entry == '.' || $entry == '..' ) {
                continue;
            }
            
            if ( $this->isInExcludeList( $dir->path, $aOptions ) ) {
                continue;
            }

            // Deep copy directories
            $this->xcopy( "$source/$entry", "$dest/$entry", $permissions, $aOptions );
        }

        // Clean up
        $dir->close();
        return true;
        
    }    
        private function _copyFile( $sSource, $sDestination, array $aOptions=array() ) {
            if ( ! file_exists( $sSource ) ) {
                return false;
            }
            // check it is in a class exclude list
            if ( $this->_isInClassExclusionList( $sSource, $aOptions ) ) {
                return false;
            }
            return copy( $sSource, $sDestination );
        }    
            private function _isInClassExclusionList( $sSource, $aOptions ) {
                return in_array( 
                    basename( $sSource ), 
                    $aOptions['exclude_file_names']
                );
            }
        private function isInExcludeList( $sDirPath, array $aOptions=array() ) {
                        
            $sDirPath          = $this->_getPathFormatted( $sDirPath );
            $_aExcludeDirPaths = isset( $aOptions[ 'exclude_dir_paths' ] )
                ? ( array ) $aOptions[ 'exclude_dir_paths' ]
                : array();
            $_aExcludeDirNames = isset( $aOptions[ 'exclude_dir_names' ] )
                ? ( array ) $aOptions[ 'exclude_dir_names' ]
                : array();            
            
            if ( in_array( $sDirPath, $_aExcludeDirPaths ) ) { 
                return true;
            }
            if ( in_array( pathinfo( $sDirPath, PATHINFO_BASENAME ), $_aExcludeDirNames ) ) { 
                return true;
            }            
            return false;
            
        }
    
    /**
     * @return      the created directory path
     * @since       1.1.0
     */
    public function createTempDir() {
        
        $_sTempFilePath = tempnam( sys_get_temp_dir(),'' );
        
        if ( file_exists( $_sTempFilePath ) ) { 
            unlink( $_sTempFilePath );
        }
        
        mkdir( $_sTempFilePath );
        
        if ( is_dir( $_sTempFilePath ) ) {
            return $_sTempFilePath; 
        }
        return '';
        
    }
    
    /**
     * 
     * @since       1.0.0
     * @return      boolean     Returns TRUE on success or FALSE on failure. 
     */
    public function deleteDir( $dirPath ) {
        if ( ! is_dir( $dirPath ) ) {
            return false;
            // throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr( $dirPath, strlen( $dirPath ) - 1, 1 ) != '/') {
            $dirPath .= '/';
        }
        $files = glob( $dirPath . '*', GLOB_MARK );
        foreach( $files as $file ) {
            if ( is_dir( $file ) ) {
                self::deleteDir( $file );
            } else {
                unlink( $file );
            }
        }
        return @rmdir( $dirPath );
    }    
    
}