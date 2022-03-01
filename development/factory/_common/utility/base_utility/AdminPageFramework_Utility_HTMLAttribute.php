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
 * @since       3.7.0
 * @package     AdminPageFramework/Utility
 * @internal
 */
abstract class AdminPageFramework_Utility_HTMLAttribute extends AdminPageFramework_Utility_SystemInformation {

    /**
     * Generates a string of attributes to be embedded in an HTML tag from an associative array.
     *
     * For example,
     * <code>
     *     array( 'id' => 'my_id', 'name' => 'my_name', 'style' => 'background-color:#fff' )
     * </code>
     * becomes
     * <code>
     *     id="my_id" name="my_name" style="background-color:#fff"
     * </code>
     *
     * This is mostly used by methods to output input fields.
     *
     * @since       3.0.0
     * @since       3.3.0       Made it allow empty value.
     * @since       3.5.3       Deprecated. Moved from `AdminPageFramework_Utility`.
     * @since       3.9.0       Revived and moved from `AdminPageFramework_Utility_Depreacated`.
     * @return      string
     * @remark      This is overridden by AdminPageFramework_WPUtility_HTML.
     */
    static public function getAttributes( array $aAttributes ) {

        $_sQuoteCharactor   ="'";
        $_aOutput           = array();
        foreach( $aAttributes as $sAttribute => $sProperty ) {
            if ( ! is_scalar( $sProperty ) ) {
                continue;
            }
            $_aOutput[] = "{$sAttribute}={$_sQuoteCharactor}{$sProperty}{$_sQuoteCharactor}";
        }
        return implode( ' ', $_aOutput );

    }

    /**
     * Generates inline CSS rules from the given array.
     *
     * For example,
     * <code>
     * array(
     *      'width'  => '32px',
     *      'height' => '32px',
     * )
     * </code>
     * will be
     * <code>
     * 'width: 32px; height: 32px;'
     * </code>
     *
     * @since       3.6.0
     * @return      string
     */
    static public function getInlineCSS( array $aCSSRules ) {
        $_aOutput = array();
        foreach( $aCSSRules as $_sProperty => $_sValue ) {
            if ( is_null( $_sValue ) ) {
                continue;
            }
            $_aOutput[] = $_sProperty . ': ' . $_sValue;
        }
        return implode( '; ', $_aOutput );
    }
        /**
         * @since           3.2.0
         * @deprecated      3.6.0       Use `getInlineCSS()` instead.
         */
        static public function generateInlineCSS( array $aCSSRules ) {
            return self::getInlineCSS( $aCSSRules );
        }

    /**
     * Generates a string of inline styles for the style attribute value from multiple arguments.
     *
     * Duplicated items will be merged.
     *
     * For example,
     * `
     * getStyleAttribute( array( 'margin-top' => '10px', 'display: inline-block' ), 'float:right; display: none;' )
     * `
     * will generate
     * `
     * margin-top: 10px; display: inline-block; float:right;
     * `
     * @since       3.6.0
     * @return      string
     */
    static public function getStyleAttribute( $asInlineCSSes ) {

        $_aCSSRules = array();
        foreach( array_reverse( func_get_args() ) as $_asCSSRules ) {

            // For array, store in the container.
            if ( is_array( $_asCSSRules ) ) {
                $_aCSSRules = array_merge( $_asCSSRules, $_aCSSRules );
                continue;
            }

            // At this point, it is a string. Break them down to array elements.
            $__aCSSRules = explode( ';', $_asCSSRules );
            foreach( $__aCSSRules as $_sPair ) {
                $_aCSSPair = explode( ':', $_sPair );
                if ( ! isset( $_aCSSPair[ 0 ], $_aCSSPair[ 1 ] ) ) {
                    continue;
                }
                $_aCSSRules[ $_aCSSPair[ 0 ] ] = $_aCSSPair[ 1 ];
            }

        }
        return self::getInlineCSS( array_unique( $_aCSSRules ) );

    }
        /**
         * @since           3.3.1
         * @deprecated      3.6.0       Use `getStyleAttribute()` instead.
         */
        static public function generateStyleAttribute( $asInlineCSSes ) {
            self::getStyleAttribute( $asInlineCSSes );
        }

    /**
     * Generates a string of class selectors from multiple arguments.
     *
     * For example,
     * <code>
     * $sClasses = getClassAttribute( array( 'button, button-primary' ), 'remove_button button' );
     * </code>
     * generates:
     * <code>
     *  button button-primary remove_button
     * </code>
     *
     * @remark      Duplicated items will be merged.
     * @since       3.6.0
     * @todo        Fix an issue that when a multidimensional array is passed, it causes a warning:  Notice: Array to string conversion.
     * @return      string
     */
    static public function getClassAttribute( /* $asClassSelectors1, $asClassSelectors12, ... */ ) {

        $_aClasses  = array();
        foreach( func_get_args() as $_asClasses ) {
            if ( ! in_array( gettype( $_asClasses ), array( 'array', 'string' ) ) ) {
                continue;
            }
            $_aClasses = array_merge(
                $_aClasses,
                is_array( $_asClasses )
                    ? $_asClasses
                    : explode( ' ', $_asClasses )
            );
        }
        $_aClasses  = array_unique( array_filter( $_aClasses ) );

        // @todo examine if it is okay to remove the trim() function below.
        return trim( implode( ' ', $_aClasses ) );

    }
        /**
         * Generates a string of class selectors from multiple arguments.
         *
         * @since       3.2.0
         * @return      string
         * @deprecated  3.6.0
         */
        static public function generateClassAttribute( /* $asClassSelectors1, $asClassSelectors12 ... */ ) {
            $_aParams = func_get_args();
            return call_user_func_array(
                array( __CLASS__ , 'getClassAttribute' ),
                $_aParams
            );
        }

    /**
     * Returns an array for generating a data attribute from the given associative array.
     *
     * @since       3.4.0
     * @since       3.8.4       Made it possible to set an empty string value to the data attribute.
     * @since       3.8.6       Changed it to convert an array element to a JSON string.
     * @since       3.8.7       Changed it to convert camel cased keys to be dashed. For the `data()` jQuery method. e.g. `camelCale` -> `camel-case`.
     * @return      array
     */
    static public function getDataAttributeArray( array $aArray ) {

        $_aNewArray = array();
        foreach( $aArray as $sKey => $v ) {

            if ( in_array( gettype( $v ), array( 'object', 'NULL' ) ) ) {
                continue;
            }

            // 3.8.6+
            if ( is_array( $v ) ) {
                $v = json_encode( $v );
            }

            // 3.8.7+ Convert camel cased keys to be dashed.
            $_sKey = strtolower( preg_replace( '/([a-zA-Z])(?=[A-Z])/', '$1-', $sKey ) );

            // 3.8.4+ Lets an empty string value to be set.
            if ( '' === $v ) {
                $_aNewArray[ "data-{$_sKey}" ] = '';
                continue;
            }

            $_aNewArray[ "data-{$_sKey}" ] = $v
                ? $v
                : '0';

        }
        return $_aNewArray;

    }

}