<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * A utility class for the debug classes.
 *
 * @since   3.9.0
 * @package AdminPageFramework/Common/Utility
 */
class AdminPageFramework_Debug_Utility extends AdminPageFramework_FrameworkUtility {

    /**
     * Returns an object name if it is an object. Otherwise, the value itself.
     * This is used to convert objects into a string in array-walk functions
     * as objects tent to get large when they are converted to a string representation.
     * @since  3.8.9
     * @since  3.8.32  Changed the visibility scope to public from private to be passed as a callback for outside the current class scope.
     * And renamed from `___getObjectName()`.
     * @since  3.9.0   Moved from `AdminPageFramework_Debug_Base`.
     * @param  mixed   $mItem
     * @return mixed
     */
    static public function getObjectName( $mItem ) {
        if ( is_object( $mItem ) ) {
            return '(object) ' . get_class( $mItem );
        }
        return $mItem;
    }

    /**
     * Slices an array by the given depth.
     *
     * @since  3.4.4
     * @since  3.8.9   Changed it not to convert an object into an array.
     * @since  3.8.9   Changed the scope to private.
     * @since  3.8.9   Renamed from `getSliceByDepth()`.
     * @since  3.8.22  Show a message when truncated by depth. Added the `$sMore` parameter.
     * @param  array   $aSubject
     * @param  integer $iDepth
     * @param  string  $sMore
     * @return array
     */
    static public function getSlicedByDepth( array $aSubject, $iDepth=0, $sMore='(array truncated) ...' ) {

        foreach ( $aSubject as $_sKey => $_vValue ) {

            if ( is_array( $_vValue ) ) {

                $_iDepth = $iDepth;
                if ( $iDepth > 0 ) {
                    $aSubject[ $_sKey ] = self::getSlicedByDepth( $_vValue, --$iDepth );
                    $iDepth = $_iDepth;
                    continue;
                }

                if ( strlen( $sMore ) ) {
                    $aSubject[ $_sKey ] = $sMore;
                    continue;
                }
                unset( $aSubject[ $_sKey ] );

            }

        }
        return $aSubject;

    }

    /**
     * @param  string $sString
     * @since  3.8.9
     * @since  3.9.0  Changed the visibility scope to public from protected. Moved from `AdminPageFramework_Debug_Base`.
     * @return string
     */
    static public function getArrayRepresentationSanitized( $sString ) {

        // Fix extra line breaks after `Array()`
        $sString = preg_replace(
            '/\)(\r\n?|\n)(?=(\r\n?|\n)\s+[\[)])/', // needle
            ')', // replacement
            $sString // subject
        );

        // Fix empty array output
        return preg_replace(
            '/Array(\r\n?|\n)\s+\((\r\n?|\n)\s+\)/', // needle
            'Array()', // replacement
            $sString // subject
        );

    }

}