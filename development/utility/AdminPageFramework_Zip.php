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
     * Indicates whether the contents should be put inside a root directory.
     */
    public $bIncludeDir = false;
    
    /**
     * Stores a callable that gets applied to parsing file string contents.
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
     * @since       3.5.4
     * @return      boolean      True on success; false otherwise.
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
        return call_user_func_array(
            array( $this, $_sMethodName ),
            array(
                $_oZip,
                $this->sSource,
                $this->aCallbacks,
                $this->bIncludeDir
            )
        );
        
    }
        /**
         * 
         * @return      boolean     True on success, false otherwise.
         */
        private function _compressDirectory( ZipArchive $oZip, $sSourceDirPath, array $aCallbacks=array(), $bIncludeDir=false ) {
           
            $_oFilesIterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $sSourceDirPath ), 
                RecursiveIteratorIterator::SELF_FIRST
            );

            if ( $bIncludeDir ) {                
                $this->_addEmptyDir( 
                    $oZip, 
                    $this->_getMainDirectoryName( $sSourceDirPath ), 
                    $aCallbacks['directory_name']
                );
                $sSourceDirPath = $this->_getSubSourceDirPath( $sSourceDirPath );
            }

            foreach ( $_oFilesIterator as $_sIterationItem ) {
                $this->_addArchiveItem( 
                    $oZip, 
                    $sSourceDirPath,
                    $_sIterationItem, 
                    $aCallbacks 
                );
            }
            
            return $oZip->close();
            
        }   
            /**
             * Adds an item (directory or file) to the archive.
             * 
             * @since       3.5.4
             * @return      void
             */
            private function _addArchiveItem( ZipArchive $oZip, $sSource, $_sIterationItem, array $aCallbacks ) {
                
                $_sIterationItem = str_replace( '\\', '/', $_sIterationItem );

                // Ignore "." and ".." folders
                if ( 
                    in_array( 
                        substr( $_sIterationItem, strrpos( $_sIterationItem, '/' ) + 1 ), 
                        array( '.', '..' ) 
                   )
                ) {
                    return;
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
            
            /**
             * 
             * @since       3.5.4
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
             * @since       3.5.4
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
         * @since       3.5.4
         * @return      boolean     True on success, false otherwise.
         */
        private function _compressFile( ZipArchive $oZip, $sSourceFilePath, $aCallbacks=null ) {
            $this->_addFromString( 
                $oZip, 
                basename( $sSourceFilePath ), 
                file_get_contents( $sSourceFilePath ),
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
     * @since       3.5.4
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
     * @since       3.5.4
     * @return      boolean     Always false
     */
    private function _returnFalse() {
        return false;
    }        
    
    /**
     * Add an empty directory to an archive.
     * 
     * @since       3.5.4
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
     * @since       3.5.4
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
     * @since       3.5.4
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