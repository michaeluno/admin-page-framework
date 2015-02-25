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
 * @version    1.0.0
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
     * @since       1.1.0
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
     *  - 'css_heredoc_keys'    : array     [1.1.0+] (optional) an array holding heredoc keywords used to assign CSS rules to a variable.
     *  - 'js_heredoc_keys'     : array     [1.1.0+] (optional) an array holding heredoc keywords used to assign JavaScript scripts to a variable.
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
        
        $aOptions  = $this->_formatOptions( $aOptions );

        $this->deleteDir( $sDestinationDirPath );
        $this->_outputBuffer(
            'Deleting: ' . $sDestinationDirPath,
            $aOptions
        );           
            
        $_bSuccess = $this->xcopy(
            $sSourceDirPath, 
            $sDestinationDirPath, 
            0755,
            $aOptions['search']
        );
        if ( ! $_bSuccess ) {
            $this->_outputBuffer(
                'Failed to copy the directory: ' . $sSourceDirPath,
                $aOptions
            );           
            return;            
        }
        $this->_outputBuffer(
            'Searching files under the directory: ' . $sSourceDirPath,
            $aOptions
        );
        
        // Store the file contents into an array. 
        $_aFiles = $this->_getFileLists( 
            $sDestinationDirPath, 
            $aOptions['search']
        );
        $this->_outputBuffer(
            sprintf( 'Found %1$s file(s)', count( $_aFiles ) ),
            $aOptions
        );
 
        // Generate the output script header comment.
        $aOptions['header_comment'] = trim( $this->_getHeaderComment( $_aFiles, $aOptions ) );
        $this->_outputBuffer(
            $this->sHeaderComment,
            $aOptions['output_buffer']
        );
     
        // Apply the beautifier 
        $this->beautify( $_aFiles, $aOptions );     
        
    }
        /**
         * Echoes the passed string.
         * 
         * @since       1.0.0
         * @return      void
         */
        private function _outputBuffer( $sText, $aOptions ) {
            if ( ! $aOptions['output_buffer'] ) {
                return;
            }
            echo $sText . $aOptions['carriage_return'];
        }
        /**
         * Formats the given options array
         * @since       1.0.0
         * @return      array
         */
        private function _formatOptions( array $aOptions ) {
                
            $aOptions                    = $aOptions + self::$_aStructure_Options;
            $aOptions['search']          = $aOptions['search'] + self::$_aStructure_Options['search'];
            $aOptions['carriage_return'] = php_sapi_name() == 'cli' 
                ? PHP_EOL 
                : '<br />';
            return $aOptions;
            
        }
    /**
     * Beautifies the PHP code.
     * 
     * @since       1.0.0
     */
    public function beautify( array $aFiles, array $aOptions ) {
         
        if ( ! function_exists( 'token_get_all' ) ) {
            $this->_outputBuffer(
                'Warning: The Tokenizer PHP extension needs to be installed to beautify PHP code.',
                $aOptions
            );
            return $aFiles;
        }
        
        // Find Beautifier.php in ./library/PHP_Beautifier/ directory.
        $_sBeautifierPath = $this->_getBeautifierPath( $aOptions );
        if ( ! file_exists( $_sBeautifierPath ) ) {
            $this->_outputBuffer(
                'Warning: The PHP_Beautifier needs to be placed in ./library/PHP_Beautifier directory.',
                $aOptions
            );
            return $aFiles;            
        }
        
        // Perform beautification.
        include_once( $_sBeautifierPath );        
        $this->_outputBuffer(
            'Beautifying PHP code.',
            $aOptions
        );
        foreach( $aFiles as $_sFilePath ) {
            $this->_beautifyPHPFile( $_sFilePath, $aOptions['header_comment'] );
        }

        
    }   
    
        /**
         * Beautifies PHP code with the PHP_Beautify library.
         * 
         * @since       1.0.0
         * @see         http://beautifyphp.sourceforge.net/docs/
         * @see         http://beautifyphp.sourceforge.net/docs/PHP_Beautifier/tutorial_PHP_Beautifier.howtouse.script.pkg.html
         */
        private function _beautifyPHPFile( $sFilePath, $sHeaderComment='' ) {
                        
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
            
        }
            
            private function _write( $sFilePath, $sData ) {
                
                // Remove the existing file.
                if ( file_exists( $sFilePath ) ) {
                    unlink( $sFilePath );
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
                        
            $_aExcludeDirPaths = isset( $aOptions['exclude_dir_paths'] )
                ? ( array ) $aOptions['exclude_dir_paths']
                : array();
            $_aExcludeDirNames = isset( $aOptions['exclude_dir_names'] )
                ? ( array ) $aOptions['exclude_dir_names']
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
     * 
     * @since       1.0.0
     * @return      boolean     Returns TRUE on success or FALSE on failure. 
     */
    public function deleteDir( $dirPath ) {
        if ( ! is_dir( $dirPath ) ) {
            return false;
            // throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        return rmdir($dirPath);
    }    
    
}