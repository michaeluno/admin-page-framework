<?php
/**
 * Admin Page Framework
 * 
 * Helps to set up auto-load classes.
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 * 
 */

/**
 * Registers classes in the given directory to be auto-loaded.
 * 
 * <h2>Usage</h2>
 * Pass the scanning directory path or a list of class files to the class constructor and it will register the classes to be auto-loaded.
 * To set a class file path array, the structure must be consist of elements of a key-value pair of a file path and the key of the class name.
 * 
 * <code>
 * new RegisterClasses( array( $sMyDirPath, $sAnotherDirPath ), $aOptions=array(), $aFlassFilePaths=array() );
 * </code>
 * 
 * <h2>Example</h2>
 * <code>
 * $aClassFiles = array( 
 *     "AdminPageFramework"	=> $sDirPath . "/factory/admin_page/AdminPageFramework.php",
 *     "AdminPageFramework_Controller" => $sDirPath . "/factory/admin_page/AdminPageFramework_Controller.php",
 *     "AdminPageFramework_Controller_Form"	=> $sDirPath . "/factory/admin_page/AdminPageFramework_Controller_Form.php",
 *     "AdminPageFramework_Controller_Menu"	=> $sDirPath . "/factory/admin_page/AdminPageFramework_Controller_Menu.php",
 *     "AdminPageFramework_Controller_Page"	=> $sDirPath . "/factory/admin_page/AdminPageFramework_Controller_Page.php",
 *  );
 * new AdminPageFramework_RegisterClasses(
 *     '', // the scanning directory - do not scan anything
 *     array(
 *         'exclude_class_names'   => array(
 *             'AdminPageFramework_MinifiedVersionHeader',
 *             'AdminPageFramework_BeautifiedVersionHeader',
 *         ),
 *     ),
 *     $aClassFiles    // a class list array
 * );       
 * // Then the registered classes can be instantiated without including them as they are all handled by the auto-loader.
 * $_oAdminPage = new AdminPageFramework( ... );  
 * </code>
 * 
 * See the `__construct()` method below for the details of arguments.
 * 
 * @since       3.0.0
 * @package     AdminPageFramework/Common/Utility
 * @version     1.0.0
 */
class AdminPageFramework_RegisterClasses {
    
    /**
     * Stores the registered classes with the key of the class name and the value of the file path.
     * @var     array
     */
    public $_aClasses = array();
    
    /**
     * Represents the structure of the recursive option array.
     * @var     array
     */
    static protected $_aStructure_Options = array(
        'is_recursive'          => true,
        'exclude_dir_paths'     => array(),
        'exclude_dir_names'     => array( 'asset', 'assets', 'css', 'js', 'image', 'images', 'license', 'document', 'documents' ),
        'allowed_extensions'    => array( 'php', ), // 'inc'
        'include_function'      => 'include',
        'exclude_class_names'   => array( /* 'SomeClass', 'SomeOtherClass' */ ),
    );
    
    /**
     * Sets up properties and performs registering classes.
     *
     * 
     * @param    array|string       $asScanDirPath       the target directory path to scan
     * @param    array              $aOptions            The recursive settings.
     * <ul>
     *      <li><code>is_recursive</code> - (boolean) determines whether the scan should be performed recursively.</li>
     *      <li><code>exclude_dir_paths</code> - (array) An array holding excluding directory paths without an ending slash.</li>
     *      <li><code>exclude_dir_names</code> - (array) An array holding excluding directory names.</li>
     *      <li><code>allowed_extensions</code> - (array) An array holding allowed file extensions without a starting dot. e.g. array( 'php', 'inc' )</li>
     * </ul>
     * 
     * <code>
     *  array(
     *      'is_recursive'       => true, 
     *      'exclude_dir_paths'  => array(), 
     *      'exclude_dir_names'  => array(), 
     *      'allowed_extensions' => array(),
     *  )
     * </code>
     * 
     * @param    array              $aClasses            the link to the array storing registered classes outside this object.
     * The structure of `$aClasses` must be consist of elements of a key-value pair of a file path and the key of the class name.
     * 
     * <code>
     * array(
     *     'MyClassName'  => 'MyClassName.php',
     *     'MyClassName2' => 'MyClassName2.php',
     * )
     * </code>
     * 
     * @remark The directory paths set for the 'exclude_dir_paths' option should use the system directory separator.
     */
    public function __construct( $asScanDirPaths, array $aOptions=array(), array $aClasses=array() ) {
        
        $_aOptions = $aOptions + self::$_aStructure_Options;
        $this->_aClasses   = $aClasses + $this->_getClassArray( $asScanDirPaths, $_aOptions );
        $this->_registerClasses( $_aOptions[ 'include_function' ] );
        
    }
    
    /**
     * Sets up the array consisting of class paths with the key of file name w/o extension.
     * 
     * It will look like
     * `
     * array(
     *      // class Name (w/o ext) => path 
     *      'MyClassA' => '.../aaa/MyClassA.php',
     *      'MyClassB' => '.../bbb/MyClassB.php',
     *      'MyClassC' => '.../ccc/MyClassC.php',
     *      ... 
     * )
     * `
     * @internal
     */
    private function _getClassArray( $asScanDirPaths, array $aSearchOptions ) {
        
        if ( empty( $asScanDirPaths ) ) {
            return array();
        }
        $_aFilePaths = array();
        foreach( ( array ) $asScanDirPaths as $_sClassDirPath ) {
            if ( realpath( $_sClassDirPath ) ) {
                $_aFilePaths = array_merge( $this->getFilePaths( $_sClassDirPath, $aSearchOptions ), $_aFilePaths );
            }
        }
        
        // Store classes in an array.
        $_aClasses = array();
        foreach( $_aFilePaths as $_sFilePath ) {
            
            // Class name without a file extension.
            $_sClassNameWOExt = pathinfo( $_sFilePath, PATHINFO_FILENAME );
            if ( in_array( $_sClassNameWOExt, $aSearchOptions['exclude_class_names'] ) ) {
                continue;
            }
            $_aClasses[ $_sClassNameWOExt ] = $_sFilePath; 
            
        }
        
        return $_aClasses;
            
    }
        /**
         * @deprecated      3.7.9
         */
        protected function _constructClassArray( $asScanDirPaths, array $aSearchOptions ) {
            return $this->_getClassArray( $asScanDirPaths, $aSearchOptions );
        }
        /**
         * Returns an array of scanned file paths.
         * 
         * The returning array structure looks like this:
         *  array
         *    0 => string '.../class/MyClass.php'
         *    1 => string '.../class/MyClass2.php'
         *    2 => string '.../class/MyClass3.php'
         *    ...
         * 
         * @internal
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
         * @internal
         */
        protected function _getGlobPatternExtensionPart( array $aExtensions=array( 'php', 'inc' ) ) {
            return empty( $aExtensions ) 
                ? '*'
                : '{' . implode( ',', $aExtensions ) . '}';
        }
        
        /**
         * The recursive version of the glob() function.
         * @internal
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
     * @internal
     * @return      void
     */
    protected function _registerClasses( $sIncludeFunction ) {
        spl_autoload_register( array( $this, '_replyToAutoLoad_' . $sIncludeFunction ) );
    }    
        /**#@+
         * Responds to the PHP auto-loader and includes the passed class based on the previously stored path associated with the class name in the constructor.
         * @internal
         * @return      void
         */        
        public function _replyToAutoLoad_include( $sCalledUnknownClassName ) {            
            if ( ! isset( $this->_aClasses[ $sCalledUnknownClassName ] ) ) { 
                return; 
            }
            include( $this->_aClasses[ $sCalledUnknownClassName ] );
        }
        public function _replyToAutoLoad_include_once( $sCalledUnknownClassName ) {            
            if ( ! isset( $this->_aClasses[ $sCalledUnknownClassName ] ) ) { 
                return; 
            }
            include_once( $this->_aClasses[ $sCalledUnknownClassName ] );
        }        
        public function _replyToAutoLoad_require( $sCalledUnknownClassName ) {            
            if ( ! isset( $this->_aClasses[ $sCalledUnknownClassName ] ) ) { 
                return; 
            }
            require( $this->_aClasses[ $sCalledUnknownClassName ] );
        }        
        public function _replyToAutoLoad_require_once( $sCalledUnknownClassName ) {            
            if ( ! isset( $this->_aClasses[ $sCalledUnknownClassName ] ) ) { 
                return; 
            }
            require_once( $this->_aClasses[ $sCalledUnknownClassName ] );
        } 
        /**#@-*/
}
