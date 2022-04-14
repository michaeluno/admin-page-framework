<?php
/*
 * Admin Page Framework v3.9.1 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Zip {
    public $sSource;
    public $sDestination;
    public $aCallbacks = array( 'file_name' => null, 'file_contents' => null, 'directory_name' => null, );
    public $aOptions = array( 'include_directory' => false, 'additional_source_directories' => array(), );
    public function __construct($sSource, $sDestination, $abOptions=false, array $aCallbacks=array())
    {
        $this->sSource = $sSource;
        $this->sDestination = $sDestination;
        $this->aOptions = $this->___getFormattedOptions($abOptions);
        $this->aCallbacks = $aCallbacks + $this->aCallbacks;
    }
    private function ___getFormattedOptions($abOptions)
    {
        $_aOptions = is_array($abOptions) ? $abOptions : array( 'include_directory' => $abOptions, );
        return $_aOptions + $this->aOptions;
    }
    public function compress()
    {
        if (! $this->___canZip($this->sSource)) {
            return false;
        }
        if (file_exists($this->sDestination)) {
            unlink($this->sDestination);
        }
        $_oZip = new ZipArchive();
        if (! $_oZip->open($this->sDestination, ZIPARCHIVE::CREATE)) {
            return false;
        }
        $this->sSource = $this->___getSanitizedSourcePath($this->sSource);
        $_aCallbacks = array( 'unknown' => '__return_false', 'directory' => array( $this, '_replyToCompressDirectory' ), 'file' => array( $this, '_replyToCompressFile' ), );
        return call_user_func_array($_aCallbacks[ $this->___getSourceType($this->sSource) ], array( $_oZip, $this->sSource, $this->aCallbacks, $this->aOptions[ 'include_directory' ], $this->aOptions[ 'additional_source_directories' ], ));
    }
    private function ___getSanitizedSourcePath($sPath)
    {
        return str_replace('\\', '/', realpath($sPath));
    }
    public function _replyToCompressDirectory(ZipArchive $oZip, $sSourceDirPath, array $aCallbacks=array(), $bIncludeDir=false, array $aAdditionalSourceDirs=array())
    {
        $_sArchiveRootDirName = '';
        if ($bIncludeDir) {
            $_sArchiveRootDirName = $this->___getMainDirectoryName($sSourceDirPath);
            $this->___addEmptyDir($oZip, $_sArchiveRootDirName, $aCallbacks[ 'directory_name' ]);
        }
        array_unshift($aAdditionalSourceDirs, $sSourceDirPath);
        $_aSourceDirPaths = array_unique($aAdditionalSourceDirs);
        $this->___addArchiveItems($oZip, $_aSourceDirPaths, $aCallbacks, $_sArchiveRootDirName);
        return $oZip->close();
    }
    private function ___addArchiveItems($oZip, $aSourceDirPaths, $aCallbacks, $sRootDirName='')
    {
        $sRootDirName = $sRootDirName ? rtrim($sRootDirName, '/') . '/' : '';
        foreach ($aSourceDirPaths as $_isIndexOrRelativeDirPath => $_sSourceDirPath) {
            $_sSourceDirPath = $this->___getSanitizedSourcePath($_sSourceDirPath);
            $_sInsideDirPrefix = is_integer($_isIndexOrRelativeDirPath) ? '' : $_isIndexOrRelativeDirPath;
            if ($_sInsideDirPrefix) {
                $this->___addRelativeDir($oZip, $_sInsideDirPrefix, $aCallbacks[ 'directory_name' ]);
            }
            $_oFilesIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($_sSourceDirPath), RecursiveIteratorIterator::SELF_FIRST);
            foreach ($_oFilesIterator as $_sIterationItem) {
                $this->___addArchiveItem($oZip, $_sSourceDirPath, $_sIterationItem, $aCallbacks, $sRootDirName . $_sInsideDirPrefix);
            }
        }
    }
    private function ___addRelativeDir($oZip, $sRelativeDirPath, $oCallable)
    {
        $sRelativeDirPath = str_replace('\\', '/', $sRelativeDirPath);
        $_aPathPartsParse = array_filter(explode('/', $sRelativeDirPath));
        $_aDirPath = array();
        foreach ($_aPathPartsParse as $_sDirName) {
            $_aDirPath[] = $_sDirName;
            $this->___addEmptyDir($oZip, implode('/', $_aDirPath), $oCallable);
        }
    }
    private function ___addArchiveItem(ZipArchive $oZip, $sSource, $_sIterationItem, array $aCallbacks, $sInsidePathPrefix='')
    {
        $_sIterationItem = str_replace('\\', '/', $_sIterationItem);
        $sInsidePathPrefix = rtrim($sInsidePathPrefix, '/') . '/';
        if (in_array(substr($_sIterationItem, strrpos($_sIterationItem, '/') + 1), array( '.', '..' ))) {
            return;
        }
        $_sIterationItem = realpath($_sIterationItem);
        $_sIterationItem = str_replace('\\', '/', $_sIterationItem);
        if (true === is_dir($_sIterationItem)) {
            $this->___addEmptyDir($oZip, $sInsidePathPrefix . str_replace($sSource . '/', '', $_sIterationItem . '/'), $aCallbacks[ 'directory_name' ]);
        } elseif (true === is_file($_sIterationItem)) {
            $this->___addFromString($oZip, $sInsidePathPrefix . str_replace($sSource . '/', '', $_sIterationItem), file_get_contents($_sIterationItem), $aCallbacks, $_sIterationItem);
        }
    }
    private function ___getMainDirectoryName($sSource)
    {
        $_aPathParts = explode("/", $sSource);
        return $_aPathParts[ count($_aPathParts) - 1 ];
    }
    public function _replyToCompressFile(ZipArchive $oZip, $sSourceFilePath, $aCallbacks=null)
    {
        $this->___addFromString($oZip, basename($sSourceFilePath), file_get_contents($sSourceFilePath), $aCallbacks, $sSourceFilePath);
        return $oZip->close();
    }
    private function ___getSourceType($sSource)
    {
        if (true === is_dir($sSource)) {
            return 'directory';
        }
        if (true === is_file($sSource)) {
            return 'file';
        }
        return 'unknown';
    }
    private function ___canZip($sSource)
    {
        if (! extension_loaded('zip')) {
            return false;
        }
        return file_exists($sSource);
    }
    private function ___addEmptyDir(ZipArchive $oZip, $sInsidePath, $oCallable)
    {
        $sInsidePath = $this->___getFilteredArchivePath($sInsidePath, $oCallable);
        if (! strlen($sInsidePath)) {
            return;
        }
        $oZip->addEmptyDir(ltrim($sInsidePath, '/'));
    }
    private function ___addFromString(ZipArchive $oZip, $sInsidePath, $sSourceContents='', array $aCallbacks=array(), $sSourceFilePath)
    {
        $sInsidePath = $this->___getFilteredArchivePath($sInsidePath, $aCallbacks[ 'file_name' ]);
        if (! strlen($sInsidePath)) {
            return;
        }
        $oZip->addFromString(ltrim($sInsidePath, '/'), is_callable($aCallbacks[ 'file_contents' ]) ? call_user_func_array($aCallbacks[ 'file_contents' ], array( $sSourceContents, $sInsidePath, $sSourceFilePath )) : $sSourceContents);
    }
    private function ___getFilteredArchivePath($sArchivePath, $oCallable)
    {
        return is_callable($oCallable) ? call_user_func_array($oCallable, array( $sArchivePath, )) : $sArchivePath;
    }
}
