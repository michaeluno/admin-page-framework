<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides utility methods which do not use WordPress functions.
 *
 * @since       3.9.0
 * @package     AdminPageFramework/Utility
 * @internal
 */
abstract class AdminPageFramework_Utility_Interpreter extends AdminPageFramework_Utility_InterpreterHTMLTable {

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

}