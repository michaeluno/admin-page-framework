<?php
/**
 * Admin Page Framework
 *
 * Facilitates WordPress plugin and theme development.
 *
 * @author      Michael Uno <michael@michaeluno.jp>
 * @copyright   2013-2021 (c) Michael Uno
 * @license     MIT <http://opensource.org/licenses/MIT>
 * @package     AdminPageFramework
 */

/**
 * A class that helps to create node data array.
 * 
 * @since       3.9.0
 */
class Path2CustomFieldType_Node extends AdminPageFramework_Utility {

    /**
     * @var   array
     * @since 3.9.0
     */
    public $aArguments = array();

    /**
     * @var string The subject directory path to scan.
     */
    public $sDirPath = '';

    /**
     * Sets up properties and hooks.
     */
    public function __construct( $sDirPath, array $aArguments ) {
        $this->sDirPath   = $sDirPath;
        $this->aArguments = $this->___getArgumentsFormatted( $aArguments );
    }
        /**
         * @param  array $aArguments
         * @return array Structure:
         * ```
         * array(
         *      [onlyFiles] => (string, length: 1) 0
         *      [onlyFolders] => (string, length: 1) 0
         *      [root] => (string, length: 1) /
         *      [fileExtensions] => array( 'php', 'txt )
         * )
         * ```
         */
        private function ___getArgumentsFormatted( array $aArguments ) {
            $aArguments  = $aArguments + array(
                'onlyFiles'      => false,
                'onlyFolders'    => false,
                'root'           => '/',
                'fileExtensions' => '',
            );
            $aArguments[ 'onlyFiles' ]      = ( boolean ) $aArguments[ 'onlyFiles' ];
            $aArguments[ 'onlyFolders' ]    = ( boolean ) $aArguments[ 'onlyFolders' ];
            $aArguments[ 'fileExtensions' ] = array_filter( explode( ',', $aArguments[ 'fileExtensions' ] ) );
            return $aArguments;
        } 

    /**
     * @return array
     */
    public function get() {

        $_sDirPath  = $this->sDirPath;
        $_aTreeData = array();
        $_aFiles    = scandir( $_sDirPath );
        natcasesort( $_aFiles );
        foreach( $_aFiles as $_sFileName ) {
            $_sFileFullPath = wp_normalize_path( trailingslashit( $_sDirPath ) . $_sFileName );
            if ( ! $this->___canTraverse( $_sFileFullPath, $_sFileName ) ) {
                continue;
            }
            $_bIsDir = is_dir( $_sFileFullPath );
            $_sExt   = $_bIsDir ? '' : pathinfo( $_sFileFullPath, PATHINFO_EXTENSION );
            if ( ! $this->___isAllowed( $_bIsDir, $_sExt ) ) {
                continue;
            }

            /**
             * @see
             */
            $_aItem  = array(
                'id'        => $_sFileFullPath,
                'text'      => $_sFileName,
                'children'  => $_bIsDir,
                'type'      => $_bIsDir ? 'folder' : 'file',
                'icon'      => $_bIsDir
                    ? 'folder'
                    : 'file file-' . $_sExt,
            );
            $_aTreeData[] = $_aItem;
        }
        return $_aTreeData;
    }

        private function ___canTraverse( $sPath, $sItemName ) {
            if ( ! file_exists( $sPath ) ) {
                return false;
            }
            if ( in_array( $sItemName, array( '.', '..' ), true ) ) {
                return false;
            }
            return true;
        }

        private function ___isAllowed( $bIsDir, $sExtension ) {
            if ( $bIsDir && $this->aArguments[ 'onlyFiles' ] ) {
                return false;
            }
            if (
                ! $bIsDir    // if it is a file
                && ! empty( $this->aArguments[ 'fileExtensions' ] ) // if a file extension is specified,
                && ! in_array( $sExtension, $this->aArguments[ 'fileExtensions' ], true )
            ) {
                return false;
            }
            return true;
        }

}