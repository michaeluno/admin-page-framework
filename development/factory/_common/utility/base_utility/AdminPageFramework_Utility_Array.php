<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides utility methods dealing with PHP arrays which do not use WordPress functions.
 *
 * @since       2.0.0
 * @package     AdminPageFramework/Utility
 * @extends     AdminPageFramework_Utility_String
 * @internal
 */
abstract class AdminPageFramework_Utility_Array extends AdminPageFramework_Utility_String {

    /**
     * Finds an unused numeric index of an array.
     *
     * @remark      the user may set a decimal number for the `order` argument.
     * @return      numeric
     * @since       3.7.4
     */
    static public function getUnusedNumericIndex( $aArray, $nIndex, $iOffset=1 ) {

        // Check if the order value is not used.
        if ( ! isset( $aArray[ $nIndex ] ) ) {
            return $nIndex;
        }

        // At this point, the index is already taken. So find one.
        return self::getUnusedNumericIndex( $aArray, $nIndex + $iOffset, $iOffset );

    }

    /**
     * Checks if the given array is an associative array or not.
     * @since       3.7.0
     * @return      boolean
     */
    static public function isAssociative( array $aArray ) {
        return array_keys ( $aArray ) !== range( 0, count( $aArray ) - 1 );
    }

    /**
     * Determines whether the element is the last element of an array by the given key.
     *
     * @since       3.0.0
     * @return      boolean
     */
    static public function isLastElement( array $aArray, $sKey ) {
        end( $aArray );
        return $sKey === key( $aArray );
    }
    /**
     * Determines whether element is the first element of an array by the given key.
     *
     * @since       3.4.0
     * @return      boolean
     */
    static public function isFirstElement( array $aArray, $sKey ) {
        reset( $aArray );
        return $sKey === key( $aArray );
    }

    /**
     * Returns a readable list of the given array contents.
     *
     * @remark      If the second dimension element is an array, it will be enclosed in parenthesis.
     * @since       3.3.0
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
     * @return      string      The generated human readable array contents.
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
            if ( ! in_array( gettype( $vValue ), array( 'array', 'object' ) ) ) {
                $_aOutput[] = "<div class='array-value'>"
                        . html_entity_decode( nl2br( str_replace( ' ', '&nbsp;', $vValue ) ), ENT_QUOTES )
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

}
