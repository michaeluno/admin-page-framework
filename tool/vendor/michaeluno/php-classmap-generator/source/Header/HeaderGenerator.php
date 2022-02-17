<?php


namespace PHPClassMapGenerator\Header;

use PHPClassMapGenerator\Utility\traitCodeParser;

class HeaderGenerator {

    use traitCodeParser;

    public $aItems   = array();
    public $aOptions = array();

    /**
     * HeaderGenerator constructor.
     * @param array $aItems
     * @param array $aOptions
     */
    public function __construct( array $aItems, array $aOptions ) {

        $this->aItems = $aItems;
        $this->aOptions = $aOptions;

    }

    /**
     *
     * @return string
     * @throws \ReflectionException
     */
    public function get() {
        return $this->___getProjectHeaderComment( $this->aItems, $this->aOptions );
    }
        /**
         * Generates the heading comment from the given path or class name.
         * @param array $aItems
         * @param array $aOptions
         * @throws \ReflectionException
         * @return string
         */
        private function ___getProjectHeaderComment( array $aItems, array $aOptions )     {

            if ( $aOptions[ 'header_class_path' ] && $aOptions[ 'header_class_name' ] ) {
                return $this->___getProjectHeaderCommentGenerated(
                    $aOptions[ 'header_class_path' ],
                    $aOptions[ 'header_class_name' ],
                    $aOptions[ 'header_type' ]
                );
            }

            if ( $aOptions[ 'header_class_name' ] ) {
                return $this->___getProjectHeaderCommentGenerated(
                    isset( $aItems[ $aOptions[ 'header_class_name' ] ] )
                        ? $aItems[ $aOptions['header_class_name'] ][ 'path' ]
                        : $aOptions[ 'header_class_path' ],
                    $aOptions[ 'header_class_name' ],
                    $aOptions[ 'header_type' ]
                );
            }

            if ( $aOptions[ 'header_class_path' ] ) {
                $_aConstructs        = $this->_getDefinedObjectConstructs( '<?php ' . $this->_getPHPCode( $aOptions[ 'header_class_path' ] ) );
                $_aDefinedClasses    = $_aConstructs[ 'classes' ];
                $_sHeaderClassName   = isset( $_aDefinedClasses[ 0 ] ) ? $_aDefinedClasses[ 0 ] : '';
                return $this->___getProjectHeaderCommentGenerated(
                    $aOptions[ 'header_class_path' ],
                    $_sHeaderClassName,
                    $aOptions[ 'header_type' ]
                );
            }

            return '';

        }
            /**
             * Generates the script heading comment.
             * @throws \ReflectionException
             * @param string $sFilePath
             * @param string $sClassName
             * @param string $sHeaderType
             * @return string
             */
            private function ___getProjectHeaderCommentGenerated( $sFilePath, $sClassName, $sHeaderType='DOCKBLOCK' ) {

                if ( ! file_exists( $sFilePath ) ) {
                    return '';
                }
                if ( ! $sClassName ) {
                    return '';
                }

                include_once( $sFilePath );
                $_aDeclaredClasses = ( array ) get_declared_classes();
                foreach( $_aDeclaredClasses as $_sClassName ) {
                    if ( $sClassName !== $_sClassName ) {
                        continue;
                    }
                    return 'DOCBLOCK' === $sHeaderType
                        ? $this->_getClassDocBlock( $_sClassName )
                        : $this->___generateHeaderComment( $_sClassName );
                }
                return '';

            }

            /**
             * Generates the heading comments from the class constants.
             * @throws \ReflectionException
             * @param string $sClassName
             * @return string
             */
            private function ___generateHeaderComment( $sClassName ) {

                $_oRC           = new \ReflectionClass( $sClassName );
                $_aConstants    = $_oRC->getConstants();
                $_aConstants    = array_change_key_case( $_aConstants, CASE_UPPER ) + array(
                    'NAME'          => '',  'VERSION'       => '', 'DESCRIPTION'   => '',
                    'URI'           => '',  'AUTHOR'        => '', 'AUTHOR_URI'    => '',
                    'COPYRIGHT'     => '',  'LICENSE'       => '', 'CONTRIBUTORS'  => '',
                );
                $_aOutputs      = array();
                $_aOutputs[]    = '/**';
                $_aOutputs[]    = ( $_aConstants[ 'NAME' ] ? "    " . $_aConstants[ 'NAME' ] . ' ' : '' )
                    . ( $_aConstants[ 'VERSION' ]   ? 'v' . $_aConstants[ 'VERSION' ] . ' '  : '' )
                    . ( $_aConstants[ 'AUTHOR' ]    ? 'by ' . $_aConstants[ 'AUTHOR' ] . ' ' : ''  );
                $_aOutputs[]    = $_aConstants[ 'DESCRIPTION' ]   ? "    ". $_aConstants[ 'DESCRIPTION' ] : '';
                $_aOutputs[]    = $_aConstants[ 'URI' ]           ? "    ". '<' . $_aConstants[ 'URI' ] . '>' : '';
                $_aOutputs[]    = ( $_aConstants[ 'COPYRIGHT' ]   ? "    " . $_aConstants[ 'COPYRIGHT' ] : '' )
                    . ( $_aConstants[ 'LICENSE' ]    ? '; Licensed under ' . $_aConstants[ 'LICENSE' ] : '' );
                $_aOutputs[]    = ' */';
                return implode( PHP_EOL, array_filter( $_aOutputs ) ) . PHP_EOL;
            }

}