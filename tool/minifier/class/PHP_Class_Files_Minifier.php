<?php
/**
 * PHP Class Files Minifier
 * 
 * Provides plugin and theme developers with simpler means of creating option pages, custom post types, ant meta boxes. 
 * 
 * @author      Michael Uno <michael@michaeluno.jp>
 * @copyright   2013-2014 (c) Michael Uno
 * @license     MIT    <http://opensource.org/licenses/MIT>
 */
if ( ! class_exists( 'PHP_Class_Files_Script_Generator_Base' ) ) {
    require( dirname( dirname( dirname( __FILE__ ) ) ) . '/php_class_files_script_generator/PHP_Class_Files_Script_Generator_Base.php' );
}

/**
 * Creates a minified version of PHP scripts from the given PHP class directory.
 * 
 * It collects PHP class files and make them into one and removes PHP comments except the specified class docBlock.
 * 
 * @remark     The parsed class file must have a name of the class defined in the file.
 * @version    1.2.0
 */
class PHP_Class_Files_Minifier extends PHP_Class_Files_Script_Generator_Base {
    
    static protected $_aStructure_Options = array(
        'header_class_name' => '',
        'header_class_path' => '',        
        'output_buffer'     => true,
        'write_to_file'     => true,        // 1.2.0+
        'character_encode'  => 'UTF-8',     // 1.2.0+
        'header_type'       => 'DOCBLOCK',    
        'exclude_classes'   => array(),
        'css_heredoc_keys'  => array( 'CSSRULES' ),     // 1.1.0+
        'js_heredoc_keys'   => array( 'JAVASCRIPTS' ),  // 1.1.0+
        // Search options
        'search'    =>    array(
            'allowed_extensions' => array( 'php' ),    // e.g. array( 'php', 'inc' )
            'exclude_dir_paths'  => array(),
            'exclude_dir_names'  => array(),
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
     * @since       1.2.0
     */
    public $sOutputFilePath;    
    
    /**
     * Stores the header comment to insert at the top of the script.
     * @since       1.2.0
     */
    public $sHeaderComment;

    /**
     * Stores the scanned files.
     * @since       1.2.0
     */    
    public $aFiles = array();
    
    /**
     * 
     * @param string    $sSourceDirPath     The target directory path.
     * @param string    $sOutputFilePath    The destination file path.
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
     * When false is passed to the 'use_docblock' argument, the constants of the header class must include 'Version', 'Name', 'Description', 'URI', 'Author', 'CopyRight', 'License'. 
     * <h3>Example</h3>
     * <code>class TaskScheduler_Registry_Base {
     *         const Version       = '1.0.0b08';
     *         const Name          = 'Task Scheduler';
     *         const Description   = 'Provides an enhanced task management system for WordPress.';
     *         const URI           = 'http://en.michaeluno.jp/';
     *         const Author        = 'miunosoft (Michael Uno)';
     *         const AuthorURI     = 'http://en.michaeluno.jp/';
     *         const CopyRight     = 'Copyright (c) 2014, <Michael Uno>';
     *         const License       = 'GPL v2 or later';
     *         const Contributors  = '';
     * }</code>
     */
    public function __construct( $asScanDirPaths, $sOutputFilePath='', array $aOptions=array() ) {

        $this->sOutputFilePath      = $sOutputFilePath;
        
        $aOptions                   = $aOptions + self::$_aStructure_Options;
        $aOptions['search']         = $aOptions['search'] + self::$_aStructure_Options['search'];
        $aOptions['write_to_file']  = file_exists( $sOutputFilePath ) ? $aOptions['write_to_file'] : false;
        $aOptions['output_buffer']  = $aOptions['write_to_file'] ? $aOptions['output_buffer'] : false;
        $_sCarriageReturn   = php_sapi_name() == 'cli' ? PHP_EOL : '<br />';
        $_aScanDirPaths     = ( array ) $asScanDirPaths;
        
            if ( $aOptions['output_buffer'] ) {
                echo 'Searching files under the directory: ' . implode( ', ', $_aScanDirPaths ) . $_sCarriageReturn;
            }
        
        /* Store the file contents into an array. */
        $_aFiles = $this->_formatFileArray( $this->_getFileLists( $_aScanDirPaths, $aOptions['search'] ) );        
        unset( $_aFiles[ pathinfo( $sOutputFilePath, PATHINFO_FILENAME ) ] );    // it's possible that the minified file also gets loaded but we don't want it.

            if ( $aOptions['output_buffer'] ) {                
                echo sprintf( 'Found %1$s file(s)', count( $_aFiles ) ) . $_sCarriageReturn;
                foreach ( $_aFiles as $_aFile ) {
                    echo $_aFile['path'] . $_sCarriageReturn;
                    // echo implode( ', ', $_aFile['defined_classes'] ) . $_sCarriageReturn;
                }
            }            
       
        /* Minify CSS Rules in variables defined with the heredoc syntax [1.1.0+] */
        $_aFiles = $this->minifyCSS( $_aFiles, $aOptions['css_heredoc_keys'], $aOptions['output_buffer'] ? $_sCarriageReturn : false );
        
        /* Minify JavaScript scripts in variables defined with the heredoc syntax [1.1.0+] */
        $_sPathJSMinPlus = dirname( __FILE__ ) . '/library/JSMinPlus.php';
        if ( file_exists( $_sPathJSMinPlus ) ) {
            include( $_sPathJSMinPlus );
            $_aFiles = $this->minifyJS( $_aFiles, $aOptions['js_heredoc_keys'], $aOptions['output_buffer'] ? $_sCarriageReturn : false );
        }       

        /* Generate the output script header comment */
        $this->sHeaderComment = $this->_getHeaderComment( $_aFiles, $aOptions );
            if ( $aOptions['output_buffer'] ) {
                echo( $this->sHeaderComment ) . $_sCarriageReturn;
            }        
        
        /* Sort the classes - in some PHP versions, parent classes must be defined before extended classes. */
        $this->aFiles = $this->sort( $_aFiles, $aOptions['exclude_classes'] );
        
            if ( $aOptions['output_buffer'] ) {
                echo sprintf( 'Sorted %1$s file(s).', count( $this->aFiles ) ) . $_sCarriageReturn;
            }        
                    
        /* Write to a file */
        $this->sData = $this->get( $this->aFiles, $this->sHeaderComment, $aOptions['character_encode'] );
        if ( $aOptions['write_to_file'] ) {
            $this->write();
        }
        
    }

    /**
     * Minifies JavaScript scripts in variables defined with the heredoc syntax. 
     * @since       1.1.0
     * 
     * @param   array           $aFiles
     * @param   array           $aHereDocKeys
     * @param   boolean|string  $bsCarriageReturns      If the output buffer is enabled, the carriage return value; otherwise, false.
     */     
    public function minifyJS( array $aFiles, array $aHereDocKeys=array(), $bsCarriageReturn=false ) {
        
        // The JSMinPlus library crashes in PHP 5.2.9 or below.
        if ( version_compare( PHP_VERSION, '5.2.9' ) <= 0 ) {
            if ( $bsCarriageReturn ) {
                echo sprintf( 'JavaScript scripts are not minified. It requires PHP above 5.2.9 to minify JavaScripts. You are using PHP %1$s.', PHP_VERSION );
            }
            return $aFiles;
        }     
     
        $_iMinified = $_iCount = 0;
        foreach( $aFiles as $_sClassName => &$_aFile ) {
          
            $this->_sCurrentIterationClassName = $_sClassName;
            foreach( $aHereDocKeys as $_sHereDocKey ) {

                $_aFile['code'] = preg_replace_callback( 
                    "/\s?+\K(<<<{$_sHereDocKey}[\r\n])(.+?)([\r\n]{$_sHereDocKey};(\s+)?[\r\n])/ms",   // needle
                    array( $this, '_replyToMinifyJavaScripts' ),                               // callback
                    $_aFile['code'],                                                         // haystack
                    -1,  // limit -1 for no limit
                    $_iCount
                );
                $_iMinified = $_iCount ? $_iMinified + $_iCount : $_iMinified;
                
            }
            
        }
        
        if ( $bsCarriageReturn ) {
            echo sprintf( 'Minified JavaScript scripts in %1$s of heredoc variable(s).', $_iMinified ) . $bsCarriageReturn;
        }
        
        return $aFiles;
        
     
    }
        /**
         * The callback function to minify the JavaScript scripts defined in heredoc variable assignments.
         * 
         * @since       1.1.0
         */    
        public function _replyToMinifyJavaScripts( $aMatch ) {
            
            if ( ! isset( $aMatch[ 1 ], $aMatch[ 2 ], $aMatch[ 3 ] ) ) {
                return $aMatch[ 0 ];
            }                   
            if ( ! class_exists( 'JSMinPlus' ) ) {
                return $aMatch[ 0 ];
            }
            $_sJavaScript = $aMatch[ 2 ];
            
            return '"' 
                . JSMinPlus::minify( $_sJavaScript, $this->_sCurrentIterationClassName )
                . ';"; ';
            
        }
        
    /**
     * Minifies CSS Rules in variables defined with the heredoc syntax. 
     * @since       1.1.0
     * 
     * @param   array           $aFiles
     * @param   array           $aHereDocKeys
     * @param   boolean|string  $bsCarriageReturns      If the output buffer is enabled, the carriage return value; otherwise, false.
     */ 
    public function minifyCSS( array $aFiles, array $aHereDocKeys=array(), $bsCarriageReturn=false ) {

        $_iMinified = $_iCount = 0;
        foreach( $aFiles as $_sClassName => &$_aFile ) {
          
            foreach( $aHereDocKeys as $_sHereDocKey ) {

                $_aFile['code'] = preg_replace_callback( 
                    "/\s?+\K(<<<{$_sHereDocKey}[\r\n])(.+?)([\r\n]{$_sHereDocKey};(\s+)?[\r\n])/ms",   // needle
                    array( $this, '_replyToMinifyCSSRules' ),                               // callback
                    $_aFile['code'],                                                         // haystack
                    -1,  // limit -1 for no limit
                    $_iCount
                );
                $_iMinified = $_iCount ? $_iMinified + $_iCount : $_iMinified;
                
            }
            
        }
        
        if ( $bsCarriageReturn ) {
            echo sprintf( 'Minified CSS Rules in %1$s of heredoc variable(s).', $_iMinified ) . $bsCarriageReturn;            
        }
        
        return $aFiles;
        
    }
        /**
         * The callback function to modify the CSS rules defined in heredoc variable assignments.
         * 
         * @since       1.1.0
         */
        public function _replyToMinifyCSSRules( $aMatch ) {
 
            if ( ! isset( $aMatch[ 1 ], $aMatch[ 2 ], $aMatch[ 3 ] ) ) {
                return $aMatch[ 0 ];
            }                   
            $_sCSSRules = $aMatch[ 2 ];
            $_sCSSRules = str_replace( 
                array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    '),  // needle - remove tabs, spaces, newlines, etc.
                '',     // replace
                preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $_sCSSRules )  // haystack - comments removed
            );
            return '"' . $_sCSSRules . '"; '; 
            
        }
            
    public function sort( array $aFiles, array $aExcludingClassNames ) {
        
        foreach( $aFiles as $_sClassName => $_aFile ) {
            if ( in_array( $_sClassName, $aExcludingClassNames ) ) {
                unset( $aFiles[ $_sClassName ] );
            }
        }
        return $this->_resolveDependent( $aFiles );
    
    }
        /**
         * This sorts the parsed PHP classes by making parent classes come earlier.
         * 
         * In some PHP versions, extended class must be declared after the parent class. 
         */
        private function _resolveDependent( array $aFiles ) {
        
            /* Append the dependent code to the dependent one and remove the dependent. */
            $aFiles = $this->_moveDependant( $aFiles );
            
            /* Unset the defendant element */
            foreach ( $aFiles as $sClassName => $aFile ) {
                if ( $aFile['code'] ) { continue; }
                unset( $aFiles[ $sClassName ] );
            }
            
            /* Make sure dependant elements no longer exist.*/
            $_iDependency = 0;
            foreach ( $aFiles as $sClassName => $aFile ) {
                if ( $aFile['dependency'] && isset( $aFiles[ $aFile['dependency'] ] ) ) {
                    $_iDependency++;
                }
            }
            if ( $_iDependency ) {
                return $this->_resolveDependent( $aFiles );
            }
            return $aFiles;
            
        }
        private function _moveDependant( $aFiles ) {
            
            $iMoved = 0;
            foreach( $aFiles as $sClassName => &$aFile ) {
            
                if ( ! $aFile['dependency'] ) continue;
                if ( ! isset( $aFiles[ $aFile['dependency'] ] ) ) continue;    // it can be an external components.
                if ( ! $aFile['code'] ) continue;
                $aFiles[ $aFile['dependency'] ]['code'] .= $aFile['code'];
                $aFile['code'] = '';
                $iMoved++;
                
            }
            if ( $iMoved ) {
                $aFiles = $this->_moveDependant( $aFiles );
            }
            return $aFiles;
            
        }
        
    /**
     * Returns the output
     */
    public function get( array $aFiles=array(), $sHeadingComment='', $sCharEncode='UTF-8' ) {
        
        $_bMBFunctionExists = function_exists( 'mb_convert_encoding' );
        $aFiles             = empty( $aFiles ) ? $this->aFiles : $aFiles;
        $sHeadingComment    = $sHeadingComment ? $sHeadingComment : $this->sHeaderComment;
        // Create a heading.
        $_aData     = array();
        $_aData[]   = $_bMBFunctionExists
            ? mb_convert_encoding( '<?php ' . PHP_EOL . $sHeadingComment . ' ', $sCharEncode, 'auto' )
            : '<?php ' . PHP_EOL . $sHeadingComment . ' ';
            
        foreach( $aFiles as $_aFile ) {
            $_aData[] = $_bMBFunctionExists
                ? mb_convert_encoding( $_aFile['code'], $sCharEncode, 'auto' )
                : $_aFile['code'];
        }
                
        return implode( '', $_aData );
        
    }
    
    /**
     * Write the output to a file.
     */
    public function write( $sFilePath='' ) {
        
        $_sData     = isset( $this->sData ) ? $this->sData : $this->get();
        $_sFilePath = $sFilePath && file_exists( $sFilePath ) 
            ? $sFilePath 
            : isset( $this->sOutputFilePath )
                ? $this->sOutputFilePath
                : '';
        
        // Remove the existing file.
        if ( file_exists( $_sFilePath ) ) {
            unlink( $_sFilePath );
        } else {
            return;
        }        
        
        // Write to a file.
        file_put_contents( $_sFilePath, $_sData, FILE_APPEND | LOCK_EX );
        
    }

}