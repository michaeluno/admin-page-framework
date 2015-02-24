<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Compresses files into a zip file.
 * 
 * <h3>Usage</h3>
 * <code>
 * $_oZip = new AdminPageFramework_Zip( $sSourcePath, $sDestinationPath );
 * $_bSucceed = $_oZip->compress();
 * </code>
 * 
 * @since       3.5.4
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal    
 */
class AdminPageFramework_Zip {
    
    /**
     * Stores a source directory/file path.
     */
    public $sSource;
    
    /**
     * Stores a destination path.
     */
    public $sDestination;
    
    /**
     * Indicates whether the contensts should be put inside a root directory.
     */
    public $bIncludeDir = false;
    
    /**
     * Stores a callable that gets appleid to parsing file string contents.
     */
    public $aCallbacks = array(
        'file_name'         => null,
        'file_contents'     => null,
        'directory_name'    => null,
    );
              
    /**
     * Sets up properties.
     * 
     * @param       string      $sSource
     * @param       string      $sDestination
     * @param       boolean     $bIncludeDir
     * @param       callable    $aCallbacks
     */
    public function __construct( $sSource, $sDestination, $bIncludeDir=false, array $aCallbacks=array() ) {
        
        $this->sSource              = $sSource;
        $this->sDestination         = $sDestination;
        $this->bIncludeDir          = $bIncludeDir;
        $this->aCallbacks           = $aCallbacks + $this->aCallbacks;
        
    }
    
    /**
     * Performs zip file compression.
     * 
     * @return boolean      True on sucess; false otherwise.
     */
    public function compress() {

        // Check whether it is possible to perform the task.
        if ( ! $this->isFeasible( $this->sSource ) ) {
            return false;
        }
        
        // Delete the existing file / directory.
        if ( file_exists( $this->sDestination ) ) {
            unlink( $this->sDestination );
        }

        $_oZip = new ZipArchive();
        if ( ! $_oZip->open( $this->sDestination, ZIPARCHIVE::CREATE ) ) {
            return false;
        }
        $this->sSource = str_replace( 
            '\\', 
            '/', 
            realpath( $this->sSource )
        );

        $_aMethods      = array(
            'unknown'   => '_returnFalse',
            'directory' => '_compressDirectory',
            'file'      => '_compressFile',
        );
        $_sMethodName   = $_aMethods[ $this->_getSourceType( $this->sSource ) ];
        return $this->{$_sMethodName}( 
            $_oZip, 
            $this->sSource,
            $this->aCallbacks
        );
        
    }
        /**
         * 
         * @return      boolean     True on success, false otherwise.
         */
        private function _compressDirectory( ZipArchive $oZip, $sSource, array $aCallbacks=array() ) {
           
            $_oFilesIterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $sSource ), 
                RecursiveIteratorIterator::SELF_FIRST
            );

            if ( $this->bIncludeDir ) {                
                $this->_addEmptyDir( 
                    $oZip, 
                    $this->_getMainDirectoryName( $sSource ), 
                    $aCallbacks['directory_name']
                );
                $sSource = $this->_getSubSourceDirPath( $sSource );
            }

            foreach ( $_oFilesIterator as $_sIterationItem ) {
                
                $_sIterationItem = str_replace( '\\', '/', $_sIterationItem );

                // Ignore "." and ".." folders
                if ( 
                    in_array( 
                        substr( $_sIterationItem, strrpos( $_sIterationItem, '/' ) + 1 ), 
                        array( '.', '..' ) 
                   )
                ) {
                    continue;
                }

                $_sIterationItem = realpath( $_sIterationItem );
                $_sIterationItem = str_replace( '\\', '/', $_sIterationItem );

                if ( true === is_dir( $_sIterationItem ) ) {
                    $this->_addEmptyDir( 
                        $oZip, 
                        str_replace( 
                            $sSource . '/', 
                            '', 
                            $_sIterationItem . '/'
                        ), 
                        $aCallbacks['directory_name']
                    );                    
                } else if ( true === is_file( $_sIterationItem ) ) {
                    $this->_addFromString( 
                        $oZip, 
                        str_replace(
                            $sSource . '/', 
                            '', 
                            $_sIterationItem
                        ),
                        file_get_contents( $_sIterationItem ),
                        $aCallbacks
                    );                    
                }
            }
            
            return $oZip->close();
            
        }   
                /**
                 * 
                 * @remark      Assumes the path is sanitized.
                 * @return      string      The main directory base name.
                 */
                private function _getMainDirectoryName( $sSource ) {
                    $_aPathParts = explode( "/", $sSource );
                    return $_aPathParts[ count( $_aPathParts ) - 1 ];
                }
                /**
                 * Returns a source dir path for wrapping contents into a root directory.
                 * 
                 * @return      string
                 */
                private function _getSubSourceDirPath( $sSource ) {
                    
                    $_aPathParts = explode( "/", $sSource );
                    $sSource     = '';
                    for ( $i=0; $i < count( $_aPathParts ) - 1; $i++ ) {
                        $sSource .= '/' . $_aPathParts[ $i ];
                    }
                    return substr( $sSource, 1 );
                    
                }
        
        /**
         * Compresses a file.
         * @return      boolean     True on success, false othersize.
         */
        private function _compressFile( ZipArchive $oZip, $sSource, $aCallbacks=null ) {
            $this->_addFromString( 
                $oZip, 
                basename( $sSource ), 
                file_get_contents( $sSource ),
                $aCallbacks
            );
            return $oZip->close();            
        }        

    /**
     * 
     * @return      string      'directory' or 'file' or 'unknown'
     */
    private function _getSourceType( $sSource ) {
     
        if ( true === is_dir( $sSource ) ) {
            return 'directory';
        }
        if ( true === is_file( $sSource ) ) {
            return 'file';
        }
        return 'unknown';
     
    }
    /**
     * Checks if the action of compressing files is feasible.
     * @return      boolean
     */
    private function isFeasible( $sSource ) {
        if ( ! extension_loaded( 'zip' ) ) {
            return false;
        }
        if ( ! file_exists( $sSource ) ) {
            return false;
        }
        return true;
    }    
    /**
     * Returns false.
     * @return      boolean     Always false
     */
    private function _returnFalse() {
        return false;
    }        
    
    /**
     * Add an empty directory to an archive.
     * @remark      If the path is empty, it will not process.
     * @return      void
     */
    private function _addEmptyDir( ZipArchive $oZip, $sInsidePath, $oCallable ) {
        $sInsidePath = $this->_getFilteredArchivePath( $sInsidePath, $oCallable );
        if ( ! strlen( $sInsidePath ) ) {
            return;
        }
        $oZip->addEmptyDir( $sInsidePath );        
    }
    /**
     * Adds a file to an archive by appling a callback to the read file contents.
     * 
     * @remark      If the path is empty, it will not process.
     * @return      void
     */
    private function _addFromString( ZipArchive $oZip, $sInsidePath, $sSourceContents='', array $aCallbacks=array() ) {
        
        $sInsidePath = $this->_getFilteredArchivePath( $sInsidePath, $aCallbacks[ 'file_name' ] );
        if ( ! strlen( $sInsidePath ) ) {
            return;
        }
        $oZip->addFromString(
            $sInsidePath, 
            is_callable( $aCallbacks[ 'file_contents' ] )
                ? call_user_func_array( 
                    $aCallbacks[ 'file_contents' ], 
                    array( 
                        $sSourceContents, 
                        $sInsidePath
                    ) 
                )
                : $sSourceContents
        );        
        
    }
    
    /**
     * Retrieves a filtered archive path.
     * @return      string
     */
    private function _getFilteredArchivePath( $sArchivePath, $oCallable ) {
        return is_callable( $oCallable )
            ? call_user_func_array(
                $oCallable,
                array(
                    $sArchivePath,
                )
            )
            : $sArchivePath;
    }    
        
}