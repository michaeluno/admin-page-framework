<?php
if( !array_key_exists('HTTP_REFERER', $_SERVER) ) exit('No direct script access allowed');

/**
 * jQuery File Tree PHP Connector
 *
 * Version 1.1.0
 *
 * @author - Cory S.N. LaViska A Beautiful Site (http://abeautifulsite.net/)
 * @author - Dave Rogers - https://github.com/daverogers/jQueryFileTree
 *
 * History:
 *
 * 1.1.1 - SECURITY: forcing root to prevent users from determining system's file structure (per DaveBrad)
 * 1.1.0 - adding multiSelect (checkbox) support (08/22/2014)
 * 1.0.2 - fixes undefined 'dir' error - by itsyash (06/09/2014)
 * 1.0.1 - updated to work with foreign characters in directory/file names (12 April 2008)
 * 1.0.0 - released (24 March 2008)
 *
 * Output a list of files for jQuery File Tree
 * 
 * @author Michael Uno
 * @since  2020/11/13 Modified the part handling $_aPost to sanitize values using WordPress character escaping methods.
 */

/**
 * filesystem root - USER needs to set this!
 * -> prevents debug users from exploring system's directory structure
 * ex: $root = $_SERVER['DOCUMENT_ROOT'];
 */
//$root = null;
$root = $_SERVER['DOCUMENT_ROOT'];
if( !$root ) exit("ERROR: Root filesystem directory not set in jqueryFileTree.php");

$_aPost = $_POST;
$_aPost = _getPOSTValuesSanitized( $_POST );
function _getPOSTValuesSanitized( array $aPost ) {
    foreach( $aPost as $_isIndex => $_mValue ) {
        if ( is_string( $_mValue ) ) {
            $aPost[ $_isIndex ] = sanitize_text_field( $_mValue );
            continue;
        }
        if ( is_array( $_mValue ) ) {
            $aPost[ $_isIndex ] = _getPOSTValuesSanitized( $_mValue );
        }
    }
    return $aPost;
}

$postDir = rawurldecode($root.(isset($_aPost['dir']) ? $_aPost['dir'] : null ));

// set checkbox if multiSelect set to true
$checkbox = ( isset($_aPost['multiSelect']) && $_aPost['multiSelect'] == 'true' ) ? "<input type='checkbox' />" : null;
$onlyFolders = ( isset($_aPost['onlyFolders']) && $_aPost['onlyFolders'] == 'true' ) ? true : false;
$onlyFiles = ( isset($_aPost['onlyFiles']) && $_aPost['onlyFiles'] == 'true' ) ? true : false;

$_aAllowedExtensions = isset( $_aPost[ 'fileExtensions' ] ) 
    ?  array_filter( explode( ',', $_aPost[ 'fileExtensions' ] ) )
    : array();

if( file_exists($postDir) ) {

	$files		= scandir($postDir);
	$returnDir	= substr($postDir, strlen($root));

	natcasesort($files);

	if( count($files) > 2 ) { // The 2 accounts for . and ..

		echo "<ul class='jqueryFileTree'>";

		foreach( $files as $file ) {
            
			$htmlRel	= htmlentities($returnDir . $file);
			$htmlName	= htmlentities($file);
			$ext		= preg_replace('/^.*\./', '', $file);

            if ( ! file_exists($postDir . $file) ) {
                continue;
            }
            if ( '.' === $file ) {
                continue;
            }
            if ( '..' === $file ) {
                continue;
            }

            // For directories
            if( is_dir($postDir . $file) && (!$onlyFiles || $onlyFolders) ) {
                echo "<li class='directory collapsed'>{$checkbox}<a rel='" .$htmlRel. "/'>" . $htmlName . "</a></li>";
                continue;
            }
            
            // For files
            if ( ! empty( $_aAllowedExtensions ) && ! in_array( $ext, $_aAllowedExtensions ) ) {
                continue;
            }

            if ( !$onlyFolders || $onlyFiles ) {
                echo "<li class='file ext_{$ext}'>{$checkbox}<a rel='" . $htmlRel . "'>" . $htmlName . "</a></li>";
                continue;
            }
	
		}

		echo "</ul>";
	}
}