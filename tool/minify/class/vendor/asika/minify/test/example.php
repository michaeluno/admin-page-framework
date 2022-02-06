<?php

use Asika\Minifier\MinifierFactory;

$minify = new \Asika\Minifier\JsMinifier;

$minify->addFile($file, [\Asika\Minifier\AbstractMinifier::FLAGGED_COMMENTS => false]);

$minify->addFile($file, [
	'uri_rewrite' => [
		'current_dir' => '',
		'doc_root' => $_SERVER['DOCUMENT_ROOT'] // [Optional]
	]
]);

\Asika\Minifier\CssMinifier::URI_REWRITE;
