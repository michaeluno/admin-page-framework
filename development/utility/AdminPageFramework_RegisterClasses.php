<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Registers classes in the given directory to be auto-loaded.
 *
 * @since 3.0.0
 * @package AdminPageFramework
 * @subpackage Utility
 * @internal
 */
class AdminPageFramework_RegisterClasses {
    
    /**
     * Stores the registered classes with the key of the class name and the value of the file path.
     */
    public $_aClasses = array();
    
    /**
     * Represents the structure of the recursive option array.
     * 
     */
    static protected $_aStructure_RecursiveOptions = array(
        'is_recursive' =>    true,
        'exclude_dir_paths' => array(),
        'exclude_dir_names' => array( 'asset', 'assets', 'css', 'js', 'image', 'images', 'license', 'document', 'documents' ),
        'allowed_extensions' => array( 'php', ), // 'inc'
    );
    
    /**
     * Sets up properties and performs registering classes.
     * 
     * param array|string $asScanDirPath     the target directory path to scan
     * param array $aSearchOptions The recursive settings
     *         array(
     *             'is_recursive' => true, // determines whether the scan should be performed recursively.
     *             'exclude_dir_paths' => array(), // set excluding directory paths without ending slash with numeric keys.
     *             'exclude_dir_names' => array(), // set excluding directory names.
     *             'allowed_extensions' => array(),
     *         )
     * param array $aClasses     the link to the array storing registered classes outside this object.
     * The structure of %aClasses must be consist of elements of a key-value pair of a file path and the key of the class name.
     * array(
     *     'MyClassName' => 'MyClassName.php',
     *     'MyClassName2' => 'MyClassName2.php',
     * )
     * @remark The directory paths set for the 'exclude_dir_paths' option should use the system directory separator.
     */
    function __construct( $asScanDirPaths, array $aSearchOptions=array(), array $aClasses=array() ) {
            
        $this->_aClasses = $aClasses + $this->_constructClassArray( $asScanDirPaths, $aSearchOptions + self::$_aStructure_RecursiveOptions );
        $this->_registerClasses();
        
    }
    
    /**
     * Sets up the array consisting of class paths with the key of file name w/o extension.
     */
    protected function _constructClassArray( $asScanDirPaths, array $aSearchOptions ) {
        
        if ( empty( $asScanDirPaths ) ) {
            return array();
        }
        $_aFilePaths = array();
        foreach( ( array ) $asScanDirPaths as $_sClassDirPath ) {
            if ( realpath( $_sClassDirPath ) ) {
                $_aFilePaths = array_merge( $this->getFilePaths( $_sClassDirPath, $aSearchOptions ), $_aFilePaths );
            }
        }
        $_aClasses = array();
        foreach( $_aFilePaths as $_sFilePath ) {
            $_aClasses[ pathinfo( $_sFilePath, PATHINFO_FILENAME ) ] = $_sFilePath; // the file name without extension will be assigned to the key
        }
        return $_aClasses;
            
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
        protected function getFilePaths( $sClassDirPath, array $aSearchOptions ) {
            
            $sClassDirPath = rtrim( $sClassDirPath, '\\/' ) . DIRECTORY_SEPARATOR; // ensures the trailing (back/)slash exists. 
            $_aAllowedExtensions = $aSearchOptions['allowed_extensions'];
            $_aExcludeDirPaths = ( array ) $aSearchOptions['exclude_dir_paths'];
            $_aExcludeDirNames = ( array ) $aSearchOptions['exclude_dir_names'];
            $_bIsRecursive = $aSearchOptions[ 'is_recursive' ];
            
            if ( defined( 'GLOB_BRACE' ) ) { // in some OSes this flag constant is not available.
                $_aFilePaths = $_bIsRecursive
                    ? $this->doRecursiveGlob( $sClassDirPath . '*.' . $this->_getGlobPatternExtensionPart( $_aAllowedExtensions ), GLOB_BRACE, $_aExcludeDirPaths, $_aExcludeDirNames )
                    : ( array ) glob( $sClassDirPath . '*.' . $this->_getGlobPatternExtensionPart( $_aAllowedExtensions ), GLOB_BRACE );
                return array_filter( $_aFilePaths ); // drop non-value elements.    
            } 
                
            // For the Solaris operation system.
            $_aFilePaths = array();
            foreach( $_aAllowedExtensions as $__sAllowedExtension ) {
                                
                $__aFilePaths = $_bIsRecursive
                    ? $this->doRecursiveGlob( $sClassDirPath . '*.' . $__sAllowedExtension, 0, $_aExcludeDirPaths, $_aExcludeDirNames )
                    : ( array ) glob( $sClassDirPath . '*.' . $__sAllowedExtension );

                $_aFilePaths = array_merge( $__aFilePaths, $_aFilePaths );
                
            }
            return array_unique( array_filter( $_aFilePaths ) );
            
        }
    
        /**
         * Constructs the file pattern of the file extension part used for the glob() function with the given file extensions.
         */
        protected function _getGlobPatternExtensionPart( array $aExtensions=array( 'php', 'inc' ) ) {
            return empty( $aExtensions ) 
                ? '*'
                : '{' . implode( ',', $aExtensions ) . '}';
        }
        
        /**
         * The recursive version of the glob() function.
         */
        protected function doRecursiveGlob( $sPathPatten, $nFlags=0, array $aExcludeDirs=array(), array $aExcludeDirNames=array() ) {

            $_aFiles = glob( $sPathPatten, $nFlags );    
            $_aFiles = is_array( $_aFiles ) ? $_aFiles : array(); // glob() can return false.
            $_aDirs = glob( dirname( $sPathPatten ) . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR|GLOB_NOSORT );
            $_aDirs = is_array( $_aDirs ) ? $_aDirs : array();
            foreach ( $_aDirs as $_sDirPath ) {

                if ( in_array( $_sDirPath, $aExcludeDirs ) ) { continue; }
                if ( in_array( pathinfo( $_sDirPath, PATHINFO_DIRNAME ), $aExcludeDirNames ) ) { continue; }
                
                $_aFiles = array_merge( $_aFiles, $this->doRecursiveGlob( $_sDirPath . DIRECTORY_SEPARATOR . basename( $sPathPatten ), $nFlags, $aExcludeDirs ) );
                
            }
            return $_aFiles;
            
        }     
    
    /**
     * Performs registration of the callback.
     * 
     * This registers the method to be triggered when an unknown class is instantiated. 
     * 
     */
    protected function _registerClasses() {
        spl_autoload_register( array( $this, '_replyToAutoLoad' ) );
    }    
        /**
         * Responds to the PHP auto-loader and includes the passed class based on the previously stored path associated with the class name in the constructor.
         */
        public function _replyToAutoLoad( $sCalledUnknownClassName ) {     
            if ( ! isset( $this->_aClasses[ $sCalledUnknownClassName ] ) ) {
                return;
            }
            // if ( ! file_exists( $this->_aClasses[ $sCalledUnknownClassName ] ) ) {
                // return;
            // }
            include( $this->_aClasses[ $sCalledUnknownClassName ] );
        }
    
}