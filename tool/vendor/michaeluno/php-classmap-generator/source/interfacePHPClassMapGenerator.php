<?php


namespace PHPClassMapGenerator;


interface interfacePHPClassMapGenerator {

    public function write();
    public function get();
    public function getItems();
    public function getMap();
    public function output( $sText );

}