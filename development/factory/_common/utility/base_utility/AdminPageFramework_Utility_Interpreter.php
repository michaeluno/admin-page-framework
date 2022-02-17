<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides utility methods which do not use WordPress functions.
 *
 * @since       3.9.0
 * @package     AdminPageFramework/Utility
 * @internal
 */
class AdminPageFramework_Utility_Interpreter extends AdminPageFramework_Utility_HTMLAttribute {

    /**
     * Returns a readable list of the given array contents.
     *
     * @remark      If the second dimension element is an array, it will be enclosed in parentheses.
     * @since       3.3.0
     * @since       3.9.0       Moved from `AdminPageFramework_Utility_Array.php`.
     * @return      string      A readable list generated from the given array.
     */
    static public function getReadableListOfArray( array $aArray ) {

        $_aOutput   = array();
        foreach( $aArray as $_sKey => $_vValue ) {
            $_aOutput[] = self::getReadableArrayContents( $_sKey, $_vValue, 32 ) . PHP_EOL;
        }
        return implode( PHP_EOL, $_aOutput );

    }
    /**
     * Generates readable array contents.
     *
     * @since       3.3.0
     * @since       3.9.0       Moved from `AdminPageFramework_Utility_Array.php`.
     * @return      string      The generated human-readable array contents.
     */
    static public function getReadableArrayContents( $sKey, $vValue, $sLabelCharLengths=16, $iOffset=0 ) {

        $_aOutput   = array();
        $_aOutput[] = ( $iOffset
                ? str_pad( ' ', $iOffset  )
                : ''
            )
            . ( $sKey
                ? '[' . $sKey . ']'
                : ''
            );

        if ( ! in_array( gettype( $vValue ), array( 'array', 'object' ) ) ) {
            $_aOutput[] = $vValue;
            return implode( PHP_EOL, $_aOutput );
        }

        foreach ( $vValue as $_sTitle => $_asDescription ) {
            if ( ! in_array( gettype( $_asDescription ), array( 'array', 'object' ) ) ) {
                $_aOutput[] = str_pad( ' ', $iOffset )
                    . $_sTitle
                    . str_pad( ':', $sLabelCharLengths - self::getStringLength( $_sTitle ) )
                    . $_asDescription;
                continue;
            }
            $_aOutput[] = str_pad( ' ', $iOffset )
                . $_sTitle
                . ": {"
                . self::getReadableArrayContents( '', $_asDescription, 16, $iOffset + 4 )
                . PHP_EOL
                . str_pad( ' ', $iOffset ) . "}";
        }
        return implode( PHP_EOL, $_aOutput );

    }
    /**
     * Returns the readable list of the given array contents as HTML.
     *
     * @since       3.3.0
     * @since       3.9.0       Moved from `AdminPageFramework_Utility_Array.php`.
     * @return      string      The HTML list generated from the given array.
     */
    static public function getReadableListOfArrayAsHTML( array $aArray ) {
        $_aOutput   = array();
        foreach( $aArray as $_sKey => $_vValue ) {
            $_aOutput[] = "<ul class='array-contents'>"
                    .  self::getReadableArrayContentsHTML( $_sKey, $_vValue )
                . "</ul>" . PHP_EOL;
        }
        return implode( PHP_EOL, $_aOutput );
    }

    /**
     * Returns the readable array contents.
     *
     * @since       3.3.0
     * @since       3.9.0       Moved from `AdminPageFramework_Utility_Array.php`.
     * @return      string      The HTML output generated from the given array.
     */
    static public function getReadableArrayContentsHTML( $sKey, $vValue ) {

        // Output container.
        $_aOutput   = array();

        // Title - array key
        $_aOutput[] = $sKey
            ? "<h3 class='array-key'>" . $sKey . "</h3>"
            : "";

        // If it does not have a nested array or object,
        if ( ! in_array( gettype( $vValue ), array( 'array', 'object' ), true ) ) {
            $_aOutput[] = "<div class='array-value'>"
                    . html_entity_decode( nl2br( $vValue ), ENT_QUOTES )
                . "</div>";
            return "<li>" . implode( PHP_EOL, $_aOutput ) . "</li>";
        }

        // Now it is a nested item.
        foreach ( $vValue as $_sKey => $_vValue ) {
            $_aOutput[] =  "<ul class='array-contents'>"
                    . self::getReadableArrayContentsHTML( $_sKey, $_vValue )
                . "</ul>";
        }
        return implode( PHP_EOL, $_aOutput ) ;

    }

    /**
     * Generates an HTML table from a given array.
     *
     * Similar to `getTableOfArray()` but this does not support multiple columns in a single row.
     * All rows consist of key-value pair, representing the array structure.
     *
     * @param  array   $aArray
     * @param  array   $aAllAttributes
     * @param  array   $aHeader
     * @param  array   $aFooter
     * @param  boolean $bEscape
     * @param  string  $sCaption
     * @return string
     * @since  3.9.0
     */
    static public function getTableOfKeyValues( array $aArray, array $aAllAttributes=array(), array $aHeader=array(), array $aFooter=array(), $bEscape=true, $sCaption='' ) {
        $_aAllAttributes = $aAllAttributes + array(
            'table'   => array(),
            'caption' => array(),
            'tbody'   => array(),
            'td'      => array(
                array(),
                array(),
            ),
            'tr'      => array(),
            't'       => array(),
            'ul'      => array(),
            'li'      => array(),
            'p'       => array(),
        );
        return "<table " . self::getAttributes( self::getElementAsArray( $_aAllAttributes, 'table' ) ) . ">"
                . self::___getTableCaption( $sCaption, $_aAllAttributes, $bEscape )
                . self::___getTableHeaderOfKeyValuePair( $aHeader, $aAllAttributes, $bEscape )
                . "<tbody " . self::getAttributes( self::getElementAsArray( $_aAllAttributes, 'tbody' ) ) . ">"
                    . self::___getRowsOfKeyValuePair( $aArray, $aAllAttributes, $bEscape )
                . "</tbody>"
                . self::___getTableFooterOfKeyValuePair( $aFooter, $aAllAttributes, $bEscape )
            . "</table>";
    }

    /**
     * Generates a table output of a given array.
     * Designed to display key-value pairs in a table.
     *
     * @param  array   $aArray           The data to display in a table.
     * @param  array   $aAllAttributes   A set of array representing tag attributes.
     * @param  array   $aHeader          Key value pairs of the table header. Only the first depth is supported.
     * @param  array   $aFooter          Key value pairs of the table footer. Only the first depth is supported.
     * @param  boolean $bEscape          Whether to escape values or not.
     * @param  string  $sCaption         The table caption.
     * @return string
     * @since  3.9.0
     */
    static public function getTableOfArray( array $aArray, array $aAllAttributes=array(), array $aHeader=array(), array $aFooter=array(), $bEscape=true, $sCaption='' ) {

        $_aAllAttributes = $aAllAttributes + array(
            'table'   => array(),
            'caption' => array(),
            'tbody'   => array(),
            'td'      => array(
                array(),
                array(),
            ),
            'tr'      => array(),
            't'       => array(),
            'ul'      => array(),
            'li'      => array(),
            'p'       => array(),
        );
        return "<table " . self::getAttributes( self::getElementAsArray( $_aAllAttributes, 'table' ) ) . ">"
                . self::___getTableCaption( $sCaption, $_aAllAttributes, $bEscape )
                . self::___getTableHeader( $aHeader, $_aAllAttributes, $bEscape )
                . "<tbody " . self::getAttributes( self::getElementAsArray( $_aAllAttributes, 'tbody' ) ) . ">"
                    . self::___getTableRows( $aArray, $_aAllAttributes, $bEscape )
                . "</tbody>"
                . self::___getTableFooter( $aFooter, $_aAllAttributes, $bEscape )
            . "</table>";
    }
        static private function ___getTableCaption( $sCaption, $aAllAttributes, $bEscape ) {
            $sCaption = ( string ) $sCaption;
            if ( ! strlen( $sCaption ) ) {
                return '';
            }
            $_aCapAttr = self::getElementAsArray( $aAllAttributes, 'caption' );
            $_sCaption = $bEscape
                ? htmlspecialchars( $sCaption )
                : $sCaption;
            return "<caption " . self::getAttributes( $_aCapAttr ) . ">"
                    . $_sCaption
                . "</caption>";
        }
        static private function ___getHTMLEscaped( $sOutput, $bEscape ) {
            return $bEscape ? htmlspecialchars( $sOutput ) : $sOutput;
        }
        static private function ___getTableHeader( array $aHeader, array $aAllAttributes, $bEscape ) {
            if ( empty( $aHeader ) ) {
                return '';
            }
            return self::isAssociative( $aHeader )
                ? self::___getTableHeaderOfKeyValuePair( $aHeader, $aAllAttributes, $bEscape )
                : self::___getTableHeaderOfMultiColumns( $aHeader, $aAllAttributes, $bEscape );

        }
            static private function ___getTableHeaderOfKeyValuePair( array $aHeader, array $aAllAttributes, $bEscape ) {
                $_aTRAttr  = self::getElementAsArray( $aAllAttributes, 'tr' );
                $_aTRAttr[ 'class' ] = self::___addClass( 'key-value', self::getElement( $_aTRAttr, array( 'class' ), '' ) );
                $_aTHAttr  = self::getElementAsArray( $aAllAttributes, 'th' );
                $_aTHAttr1 = self::getElementAsArray( $aAllAttributes, array( 'th', 0 ) ) + $_aTHAttr;
                $_aTHAttr2 = self::getElementAsArray( $aAllAttributes, array( 'th', 1 ) ) + $_aTHAttr;
                $_sOutput = '';
                foreach( $aHeader as $_sKey => $_sValue ) {
                    $_sOutput .= "<tr " . self::getAttributes( $_aTRAttr ) . ">"
                            . "<th " . self::getAttributes( $_aTHAttr1 ) . ">" . self::___getHTMLEscaped( $_sKey, $bEscape ) . "</th>"
                            . "<th " . self::getAttributes( $_aTHAttr2 ) . ">" . self::___getHTMLEscaped( $_sValue, $bEscape ) . "</th>"
                        . "</tr>";
                }
                return "<thead>" . $_sOutput . "</thead>";
            }
            static private function ___getTableHeaderOfMultiColumns( array $aHeader, array $aAllAttributes, $bEscape ) {
                $_aTRAttr  = self::getElementAsArray( $aAllAttributes, 'tr' );
                $_aTHAttr  = self::getElementAsArray( $aAllAttributes, 'th' );
                $_sOutput  = "<tr " . self::getAttributes( $_aTRAttr ) . ">";
                foreach( array_values( $aHeader ) as $_iIndex => $_sColumnName ) {
                    $_aTHAttrNth  = self::getElementAsArray( $aAllAttributes, array( 'th', $_iIndex ) ) + $_aTHAttr;
                    $_sOutput    .= "<th " . self::getAttributes( $_aTHAttrNth ) . ">" . self::___getHTMLEscaped( ( string ) $_sColumnName, $bEscape ) . "</th>";
                }
                $_sOutput .= "</tr>";
                return "<thead>" . $_sOutput . "</thead>";
            }
        static private function ___getTableFooter( array $aFooter, array $aAllAttributes, $bEscape ) {
            if ( empty( $aFooter ) ) {
                return '';
            }
            return self::isAssociative( $aFooter )
                ? self::___getTableFooterOfKeyValuePair( $aFooter, $aAllAttributes, $bEscape )
                : self::___getTableFooterOfMultiColumns( $aFooter, $aAllAttributes, $bEscape );
        }
            static private function ___getTableFooterOfKeyValuePair( array $aFooter, array $aAllAttributes, $bEscape ) {
                $_aTRAttr  = self::getElementAsArray( $aAllAttributes, 'tr' );
                $_aTDAttr  = self::getElementAsArray( $aAllAttributes, 'td' );
                $_aTRAttr[ 'class' ] = self::___addClass( 'key-value', self::getElement( $_aTRAttr, array( 'class' ), '' ) );
                $_aTDAttr1 = self::getElementAsArray( $aAllAttributes, array( 'td', 0 ) ) + $_aTDAttr;
                $_aTDAttr2 = self::getElementAsArray( $aAllAttributes, array( 'td', 1 ) ) + $_aTDAttr;
                $_sOutput = '';
                foreach( $aFooter as $_sKey => $_sValue ) {
                    $_sOutput = "<tr " . self::getAttributes( $_aTRAttr ) . ">"
                            . "<td " . self::getAttributes( $_aTDAttr1 ) . ">" . self::___getHTMLEscaped( $_sKey, $bEscape ) . "</td>"
                            . "<td " . self::getAttributes( $_aTDAttr2 ) . ">" . self::___getHTMLEscaped( $_sValue, $bEscape ) . "</td>"
                        . "</tr>";
                }
                return "<tfoot>" . $_sOutput . "</tfoot>";                
            }
            static private function ___getTableFooterOfMultiColumns( array $aFooter, array $aAllAttributes, $bEscape ) {
                $_aTRAttr  = self::getElementAsArray( $aAllAttributes, 'tr' );
                $_aTDAttr  = self::getElementAsArray( $aAllAttributes, 'td' );
                $_sOutput  = "<tr " . self::getAttributes( $_aTRAttr ) . ">";
                foreach( array_values( $aFooter ) as $_iIndex => $_sColumnName ) {
                    $_aTDAttrNth  = self::getElementAsArray( $aAllAttributes, array( 'td', $_iIndex ) ) + $_aTDAttr;
                    $_sOutput    .= "<td " . self::getAttributes( $_aTDAttrNth ) . ">" . self::___getHTMLEscaped( ( string ) $_sColumnName, $bEscape ) . "</td>";
                }
                $_sOutput .= "</tr>";
                return "<tfoot>" . $_sOutput . "</tfoot>";
            }            
        static private function ___getTableRows( array $aArray, array $aAllAttributes, $bEscape ) {
            if ( empty( $aArray ) ) {
                return '';
            }
            return self::___shouldKeyValuePair( $aArray )
                ? self::___getRowsOfKeyValuePair( $aArray, $aAllAttributes, $bEscape )
                : self::___getRowsOfMultiColumns( $aArray, $aAllAttributes, $bEscape );
        }
            static private function ___shouldKeyValuePair( array $aArray ) {
                if ( self::isAssociative( $aArray ) ) {
                    return true;
                }
                $_aFirstItem = self::getAsArray( self::getFirstElement( $aArray ) );
                if ( self::isAssociative( $_aFirstItem ) || self::isMultiDimensional( $_aFirstItem ) ) {
                    return true;
                }
                return false;
            }

            /**
             * @param  array   $aItem
             * @param  array   $aAllAttributes
             * @param  boolean $bEscape
             * @return string
             * @since  3.9.0
             */
            static private function ___getRowsOfKeyValuePair( array $aItem, array $aAllAttributes, $bEscape ) {
                $_aTRAttr                 = self::getElementAsArray( $aAllAttributes, 'tr' );
                $_aTRAttr[ 'class' ]      = self::___addClass( 'key-value', self::getElement( $_aTRAttr, array( 'class' ), '' ) );
                $_aTDAttr                 = self::getElementAsArray( $aAllAttributes, 'td' );
                $_aTDAttr                 = array_filter( $_aTDAttr, 'is_scalar' );
                $_aPAttr                  = self::getElementAsArray( $aAllAttributes, array( 'p' ) );
                $_aTDAttrFirst            = self::getElementAsArray( $aAllAttributes, array( 'td', 0 ) ) + $_aTDAttr;
                $_aTDAttrFirst[ 'class' ] = self::___addClass( 'column-key', self::getElement( $_aTDAttrFirst, array( 'class' ), '' ) );                
                $_sOutput = '';
                foreach( $aItem as $_sColumnName => $_asValue ) {
                    $_sOutput .= "<tr " . self::getAttributes( $_aTRAttr ) . ">";
                    $_sOutput .= "<td " . self::getAttributes( $_aTDAttrFirst ) . ">"
                            . "<p " . self::getAttributes( $_aPAttr )  . ">" . self::___getHTMLEscaped( $_sColumnName, $bEscape ) . "</p>"
                         . "</td>";
                    $_sOutput .= self::___getColumnValue( $_asValue, $aAllAttributes, $bEscape, 1 );
                    $_sOutput .= "</tr>";
                }
                return $_sOutput;
            }
            /**
             * @return string
             * @since  3.9.0
             */
            static private function ___getRowsOfMultiColumns( array $aArray, array $aAllAttributes, $bEscape ) {
                $_aTRAttr = self::getElementAsArray( $aAllAttributes, 'tr' );
                $_sOutput = '';
                foreach( $aArray as $_iRowIndex => $_asValue ) {
                    if ( is_scalar( $_asValue ) ) {
                        $_sOutput .= "<tr " . self::getAttributes( $_aTRAttr ) . ">"
                                . self::___getColumnValue( $_asValue, $aAllAttributes, $bEscape, 0 )
                            . "</tr>";
                        continue;
                    }
                    $_aColumns = self::getAsArray( $_asValue );
                    $_sOutput .= "<tr " . self::getAttributes( $_aTRAttr ) . ">"
                            . self::___getColumns( $_aColumns, $aAllAttributes, $bEscape )
                        . "</tr>";
                }
                return $_sOutput;
            }
                static private function ___getColumns( array $aColumns, $aAllAttributes, $bEscape ) {
                    $_sOutput = '';
                    foreach( array_values( $aColumns ) as $_iIndex => $_asValue ) {
                        $_sOutput .= self::___getColumnValue( $_asValue, $aAllAttributes, $bEscape, $_iIndex );
                    }
                    return $_sOutput;
                }

            /**
             * @param  string $sClassToAdd
             * @param  string $sClasses
             * @return string
             * @since  3.9.0
             */
            static private function ___addClass( $sClassToAdd, $sClasses ) {
                $_aClasses    = explode( ' ', $sClasses );
                $_aClasses[]  = $sClassToAdd;
                return trim( implode( ' ', array_unique( $_aClasses ) ) );
            }

            /**
             * @param  mixed   $mValue
             * @param  array   $aAllAttributes
             * @param  boolean $bEscape
             * @param  integer $iColumnIndex Zero-based column index
             * @return string
             * @since  3.9.0
             */
            static private function ___getColumnValue( $mValue, array $aAllAttributes, $bEscape, $iColumnIndex ) {
                $_aTDAttr    = self::getElementAsArray( $aAllAttributes, 'td' );
                $_aTDAttr    = array_filter( $_aTDAttr, 'is_scalar' );
                $_aTDAttrNth = self::getElementAsArray( $aAllAttributes, array( 'td', $iColumnIndex ) ) + $_aTDAttr;
                $_aTDAttrNth[ 'class' ] = self::___addClass( 'column-value', self::getElement( $_aTDAttrNth, array( 'class' ), '' ) );
                if ( is_null( $mValue ) ) {
                    $mValue = '(null)';
                }
                $_aPAttr        = self::getElementAsArray( $aAllAttributes, 'p' );
                if ( is_scalar( $mValue ) ) {
                    return "<td " . self::getAttributes( $_aTDAttrNth ) . ">"
                        . "<p " . self::getAttributes( $_aPAttr )  . ">" . self::___getHTMLEscaped( $mValue, $bEscape ) . "</p>"
                       . "</td>";
                }
                if ( is_array( $mValue ) ) {
                    return self::isAssociativeArray( $mValue ) || self::isMultiDimensional( $mValue )
                        ? "<td " . self::getAttributes( $_aTDAttrNth ) . ">"
                            . self::getTableOfKeyValues( $mValue, $aAllAttributes )
                        . "</td>"
                        : "<td " . self::getAttributes( $_aTDAttrNth ) . ">"
                            // @todo may ought to be numeric-rows table
                            . self::___getList( $mValue, $aAllAttributes, $bEscape )
                        . "</td>";
                }
                return "<td " . self::getAttributes( $_aTDAttrNth ) . ">"
                        . '(' . gettype( $mValue ) . ')' . ( is_object( $mValue ) ? get_class( $mValue ) : '' )
                    . "</td>";
            }
                /**
                 * @param  array $aArray
                 * @param  array $aAllAttributes
                 * @param  boolean $bEscape
                 * @return string
                 * @since  3.9.0
                 */
                static private function ___getList( array $aArray, $aAllAttributes, $bEscape ) {
                    $_aULAttr = self::getElementAsArray( $aAllAttributes, 'ul' );
                    $_aLIAttr = self::getElementAsArray( $aAllAttributes, 'li' );
                    $_aULAttr[ 'class' ] = self::___addClass( 'numeric', self::getElement( $_aULAttr, array( 'class' ), '' ) );
                    if ( empty( $aArray ) ) {
                        return '';
                    }
                    $_sList   = "<ul " . self::getAttributes( $_aULAttr ) . ">";
                    foreach( $aArray as $_asValue ) {
                        $_sItem  = is_array( $_asValue )
                            ? self::___getList( $_asValue, $aAllAttributes, $bEscape )
                            : self::___getHTMLEscaped( $_asValue, $bEscape );
                        $_sList .= "<li " . self::getAttributes( $_aLIAttr ) . ">"
                                . $_sItem
                            . "</li>";
                    }
                    $_sList  .= "</ul>";
                    return $_sList;
                }

}