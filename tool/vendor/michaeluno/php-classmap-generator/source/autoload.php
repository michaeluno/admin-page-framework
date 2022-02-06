<?php

namespace PHPClassMapGenerator;

Autoload::set( include( __DIR__ . '/class-map.php' ) );

class Autoload {
    static public function set( array $aClasses ) {
        self::$aAutoLoadClasses = $aClasses + self::$aAutoLoadClasses;
        spl_autoload_register( array( __CLASS__, 'replyToIncludeClass' ), false );
    }
    static public function replyToIncludeClass( $sUnknownClassName ) {
        if ( ! isset( self::$aAutoLoadClasses[ $sUnknownClassName ] ) ) {
            return;
        }
        include( self::$aAutoLoadClasses[ $sUnknownClassName ] );
    }
    static public $aAutoLoadClasses = [];
}