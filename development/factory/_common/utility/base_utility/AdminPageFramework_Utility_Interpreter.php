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
     * Generates a table output of a given array.
     * Designed to display key-value pairs in a table.
     * @since  3.9.0
     * @return string
     * @param  array   $aArray
     * @param  array   $aAllAttributes
     * @param  array   $aHeader          Key value pairs of the table header. Only the first depth is supported.
     * @param  array   $aFooter          Key value pairs of the table footer. Only the first depth is supported.
     * @param  boolean $bEscape          Whether to escape values or not.
     */
    static public function getTableOfArray( array $aArray, array $aAllAttributes=array(), array $aHeader=array(), array $aFooter=array(), $bEscape=true ) {

        $_aAllAttributes = $aAllAttributes + array(
            'table' => array(),
            'tbody' => array(),
            'td'    => array(
                array(),
                array(),
            ),
            'tr'    => array(),
            't'     => array(),
            'ul'    => array(),
            'li'    => array(),
            'p'     => array(),
        );
        return "<table " . self::getAttributes( self::getElementAsArray( $_aAllAttributes, 'table' ) ) . ">"
                . self::___getTableHeader( $aHeader, $_aAllAttributes, $bEscape )
                . "<tbody " . self::getAttributes( self::getElementAsArray( $_aAllAttributes, 'tbody' ) ) . ">"
                    . self::___getTableRows( $aArray, $_aAllAttributes, $bEscape )
                . "</tbody>"
                . self::___getTableFooter( $aFooter, $_aAllAttributes, $bEscape )
            . "</table>";
    }
        static private function ___getHTMLEscaped( $sOutput, $bEscape ) {
            return $bEscape ? htmlspecialchars( $sOutput ) : $sOutput;
        }
        static private function ___getTableHeader( array $aHeader, array $aAllAttributes, $bEscape ) {
            if ( empty( $aHeader ) ) {
                return '';
            }
            $_aTRAttr  = self::getElementAsArray( $aAllAttributes, 'tr' );
            $_aTHAttr  = self::getElementAsArray( $aAllAttributes, 'th' );
            $_aTHAttr1 = self::getElementAsArray( $aAllAttributes, array( 'th', 0 ) ) + $_aTHAttr;
            $_aTHAttr2 = self::getElementAsArray( $aAllAttributes, array( 'th', 1 ) ) + $_aTHAttr;
            $_sOutput = '';
            foreach( $aHeader as $_sKey => $_sValue ) {
                $_sOutput = "<tr " . self::getAttributes( $_aTRAttr ) . ">"
                        . "<th " . self::getAttributes( $_aTHAttr1 ) . ">" . self::___getHTMLEscaped( $_sKey, $bEscape ) . "</th>"
                        . "<th " . self::getAttributes( $_aTHAttr2 ) . ">" . self::___getHTMLEscaped( $_sValue, $bEscape ) . "</th>"
                    . "</tr>";
            }
            return "<thead>" . $_sOutput . "</thead>";
        }
        static private function ___getTableFooter( array $aFooter, array $aAllAttributes, $bEscape ) {
            if ( empty( $aFooter ) ) {
                return '';
            }
            $_aTRAttr  = self::getElementAsArray( $aAllAttributes, 'tr' );
            $_aTDAttr  = self::getElementAsArray( $aAllAttributes, 'td' );
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
        static private function ___getTableRows( array $aItem, array $aAllAttributes, $bEscape ) {
            $_aTRAttr = self::getElementAsArray( $aAllAttributes, 'tr' );
            $_aTDAttr = self::getElementAsArray( $aAllAttributes, 'td' );
            $_aTDAttr = array_filter( $_aTDAttr, 'is_scalar' );
            if ( empty( $aItem ) ) {
                return '';
                // @deprecaetd
                // $_aTDAttr = array( 'colspan' => 2 ) + $_aTDAttr;
                // return "<tr " . self::getAttributes( $_aTRAttr ) . ">"
                //         . "<td " . self::getAttributes( $_aTDAttr ) . ">"
                //             . __( 'No data found.', 'admin-page-framework' )
                //         . "</td>"
                //     . "</tr>";
            }
            $_aTDAttrFirst            = self::getElementAsArray( $aAllAttributes, array( 'td', 0 ) ) + $_aTDAttr;
            $_aTDAttrFirst[ 'class' ] = self::___addClass( 'column-key', self::getElement( $_aTDAttrFirst, array( 'class' ), '' ) );
            $_aPAttr                  = self::getElementAsArray( $aAllAttributes, array( 'p' ) );
            $_sOutput = '';
            foreach( $aItem as $_sColumnName => $_asValue ) {
                $_sOutput .= "<tr " . self::getAttributes( $_aTRAttr ) . ">";
                $_sOutput .= "<td " . self::getAttributes( $_aTDAttrFirst ) . ">"
                        . "<p " . self::getAttributes( $_aPAttr )  . ">" . self::___getHTMLEscaped( $_sColumnName, $bEscape ) . "</p>"
                     . "</td>";
                $_sOutput .= self::___getColumnValue( $_asValue, $aAllAttributes, $bEscape );
                $_sOutput .= "</tr>";
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
                return implode( ' ', array_unique( $_aClasses ) );
            }
            static private function ___getColumnValue( $mValue, array $aAllAttributes, $bEscape ) {
                $_aTDAttr       = self::getElementAsArray( $aAllAttributes, 'td' );
                $_aTDAttr       = array_filter( $_aTDAttr, 'is_scalar' );
                $_aTDAttrSecond = self::getElementAsArray( $aAllAttributes, array( 'td', 1 ) ) + $_aTDAttr;
                $_aTDAttrSecond[ 'class' ] = self::___addClass( 'column-value', self::getElement( $_aTDAttrSecond, array( 'class' ), '' ) );
                if ( is_null( $mValue ) ) {
                    $mValue = '(null)';
                }
                $_aPAttr        = self::getElementAsArray( $aAllAttributes, 'p' );
                if ( is_scalar( $mValue ) ) {
                    return "<td " . self::getAttributes( $_aTDAttrSecond ) . ">"
                        . "<p " . self::getAttributes( $_aPAttr )  . ">" . self::___getHTMLEscaped( $mValue, $bEscape ) . "</p>"
                       . "</td>";
                }
                if ( is_array( $mValue ) ) {
                    return self::isAssociativeArray( $mValue ) || self::isMultiDimensional( $mValue )
                        ? "<td " . self::getAttributes( $_aTDAttrSecond ) . ">"
                            . self::getTableOfArray( $mValue, $aAllAttributes )
                        . "</td>"
                        : "<td " . self::getAttributes( $_aTDAttrSecond ) . ">"
                            . self::___getList( $mValue, $aAllAttributes, $bEscape )
                        . "</td>";
                }
                return "<td " . self::getAttributes( $_aTDAttrSecond ) . ">"
                        . '(' . gettype( $mValue ) . ')' . ( is_object( $mValue ) ? get_class( $mValue ) : '' )
                    . "</td>";
            }
                /**
                 * @param array $aArray
                 * @param array $aAllAttributes
                 * @return string
                 * @since 3.9.0
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