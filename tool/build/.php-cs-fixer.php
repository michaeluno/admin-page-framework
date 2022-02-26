<?php
$oConfig = new PhpCsFixer\Config();
$oConfig
    ->setRiskyAllowed(true )
    ->setRules( [
        '@PSR2'         => true,
        'array_syntax'  => [ 'syntax' => 'long' ],
        'braces'        => [
            'allow_single_line_closure' => true,
        ],
    ] );
return $oConfig;