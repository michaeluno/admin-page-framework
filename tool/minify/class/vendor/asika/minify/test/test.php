<?php
use Asika\Minifier\CssMinifier;

error_reporting(-1);
include_once __DIR__ . '/../vendor/autoload.php';
echo CssMinifier::process(__DIR__ . '/test.css');
