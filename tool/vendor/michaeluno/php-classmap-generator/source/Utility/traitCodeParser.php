<?php


namespace PHPClassMapGenerator\Utility;


trait traitCodeParser {

    /**
     * Returns the docblock of the specified class
     * @param   string $sClassName
     * @throws \ReflectionException
     * @return string
     */
    protected function _getClassDocBlock( $sClassName ) {
        $_oRC = new \ReflectionClass( $sClassName );
        return trim( $_oRC->getDocComment() );
    }

    /**
     * Retrieve metadata from a file.
     *
     * Searches for metadata in the first 8 KB of a file, such as a plugin or theme.
     * Each piece of metadata must be on its own line. Fields can not span multiple
     * lines, the value will get cut at the end of the first line.
     *
     * If the file data is not within that first 8 KB, then the author should correct
     * their plugin file and move the data headers to the top.
     *
     * @link https://codex.wordpress.org/File_Header
     *
     * @since 2.9.0
     *
     * @param string $sFilePath            Absolute path to the file.
     * @param array  $aCommentItems List of headers, in the format `array( 'HeaderKey' => 'Header Name' )`.
     * @return string[] Array of file header values keyed by header name.
     */
    public function getFileHeaderComment( $sFilePath, array $aCommentItems ) {

        // We don't need to write to the file, so just open for reading.
        $fp = fopen( $sFilePath, 'r' );

        // Pull only the first 8 KB of the file in.
        $_bsFileData = fread( $fp, 8 * 1024 );

        // PHP will close file handle, but we are good citizens.
        fclose( $fp );

        // Make sure we catch CR-only line endings.
        $_bsFileData = str_replace( "\r", "\n", $_bsFileData );

        $_aCommentItems = $aCommentItems;
        foreach ( $_aCommentItems as $_sKey => $regex ) {
            if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':?(.*)$/mi', $_bsFileData, $match ) && $match[ 1 ] ) {
                $_aCommentItems[ $_sKey ] = $this->___getFileHeaderCommentCleaned( $match[ 1 ] );
                continue;
            }
            $_aCommentItems[ $_sKey ] = '';
        }
        return $_aCommentItems;

    }
        private function ___getFileHeaderCommentCleaned( $sString ) {
            return trim( preg_replace( '/\s*(?:\*\/|\?>).*/', '', $sString ) );
        }

    /**
     * Retrieves PHP code from the given path.
     *
     * @param       string  $sFilePath
     * @remark      Enclosing `<?php ?>` tags will be removed.
     * @return      string
     */
    protected function _getPHPCode( $sFilePath ) {
        $_sCode = php_strip_whitespace( $sFilePath );
        $_sCode = preg_replace( '/^<\?php/', '', $_sCode );
        $_sCode = preg_replace( '/\?>\s+?$/', '', $_sCode );
        return $_sCode;
    }

    /**
     * Returns the parent class
     * @param string $sPHPCode
     * @return string
     */
    protected function _getParentClass( $sPHPCode ) {
        if ( ! preg_match( '/class\s+(.+?)\s+extends\s+(.+?)\s+{/i', $sPHPCode, $aMatch ) ) {
            return null;
        }
        return $aMatch[ 2 ];
    }

    /**
     * Retrieves defined PHP class names using the `token_get_all` function.
     *
     * @param string $sPHPCode PHP code with the `<?php ` opening tag.
     * @return      array
     */
    protected function _getDefinedObjectConstructs( $sPHPCode ) {

        $_aConstructs       = array(
            'classes'    => array(), 'interfaces' => array(),
            'traits'     => array(), 'namespaces' => array(),
            'aliases'    => array(),
        );
        $_aTokens           = token_get_all( $sPHPCode );
        $_iCount            = count( $_aTokens );
        $_sCurrentNameSpace = '';
        for ( $i = 2; $i < $_iCount; $i++ ) {

            // Namespace
            if ( T_NAMESPACE === $_aTokens[ $i ][ 0 ] ) {
                $_sCurrentNameSpace = $this->___getNamespaceExtractedFromTokens( $_aTokens, $i, $_iCount ) . '\\';
                $_aConstructs[ 'namespaces' ][] = $_sCurrentNameSpace;
            }

            // Class
            $_sClassName = $this->___getObjectConstructNameExtractedFromToken( $_aTokens, $i, T_CLASS );
            if ( $_sClassName ) {
                $_aConstructs[ 'classes' ][] = $_sCurrentNameSpace . $_sClassName;
            }

            // Interface
            $_sInterface = $this->___getObjectConstructNameExtractedFromToken( $_aTokens, $i, T_INTERFACE );
            if ( $_sInterface ) {
                $_aConstructs[ 'interfaces' ][] = $_sCurrentNameSpace . $_sInterface;
            }

            // Trait
            $_sTrait = $this->___getObjectConstructNameExtractedFromToken( $_aTokens, $i, T_TRAIT );
            if ( $_sTrait ) {
                $_aConstructs[ 'traits' ][] = $_sCurrentNameSpace . $_sTrait;
            }

            // Class Alias
            $_aClassAliasParameters = $this->___getClassAliasFromToken( $_aTokens, $i );
            if ( isset( $_aClassAliasParameters[ 1 ] ) ) {
                $_aConstructs[ 'aliases' ][] = $_aClassAliasParameters[ 1 ];
            }

        }
        return $_aConstructs;

    }
        /**
         * @param  array   $aTokens An array holding token arrays.
         * @param  integer $i       The token array index being parsed.
         * @return array
         * @since  1.2.0
         */
        private function ___getClassAliasFromToken( array $aTokens, $i ) {
            if ( T_STRING !== $aTokens[ $i ][ 0 ] ) {
                return array();
            }
            if ( 'class_alias' !== $aTokens[ $i ][ 1 ] ) {
                return array();
            }
            $_aParameters = array();
            for ( $_i = $i; $_i < count( $aTokens ); $_i++ ) {
                if ( ! is_array( $aTokens[ $_i ] ) ) {
                    continue;
                }
                if ( 323 !== $aTokens[ $_i ][ 0 ] ) {
                    continue;
                }
                $_aParameters[] = trim( $aTokens[ $_i ][ 1 ],'\'"' ); // any combination of ' and "
                if ( 2 === count( $_aParameters ) ) {
                    break;
                }
            }
            return $_aParameters;
        }

        private function ___getObjectConstructNameExtractedFromToken( array $aTokens, $i, $iObjectConstruct ) {
            if ( $iObjectConstruct !== $aTokens[ $i - 2 ][ 0 ] ) {
                return '';
            }
            if ( T_WHITESPACE !== $aTokens[ $i - 1 ][ 0 ] ) {
                return '';
            }
            if ( T_STRING !== $aTokens[ $i ][ 0 ] ) {
                return '';
            }
            return $aTokens[ $i ][ 1 ];
        }
        private function ___getNamespaceExtractedFromTokens( array $aTokens, $i, $iCount ) {
            $_sNamespace = '';
            while ( ++$i < $iCount ) {
                if ( $aTokens [ $i ] === ';') {
                    $_sNamespace = trim( $_sNamespace );
                    break;
                }
                $_sNamespace .= is_array( $aTokens[ $i ] )
                    ? $aTokens[ $i ][ 1 ]
                    : $aTokens[ $i ];
            }
            return $_sNamespace;
        }


}