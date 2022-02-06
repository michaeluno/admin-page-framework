# PHP CSS & JS Minify

Simple CSS & JS minify library.

This project is port of [YUI CSSmin](https://github.com/mrclay/minify/blob/2.x/min/lib/Minify/CSSmin.php) 
and [JShrink](https://github.com/tedious/JShrink).

## Installation

Via Composer

```bash
composer require asika/minify
```

## Getting Started

### Get Minifier

Use Factory

```php
use Asika\Minifier\MinifierFactory;

$cssMinify = MinifierFactory::create('css');

$jsMinify = MinifierFactory::create('js');
```

Directly new object:

```php
$minify = new \Asika\Minifier\JsMinifier;

// OR

$minify = new \Asika\Minifier\CssMinifier;
```

### Minify CSS & JS

Use object

```php
use Asika\Minifier\MinifierFactory;

$minify = MinifierFactory::create($type);

$minify->addFile($path); // Add file path
$minify->addContent($path); // Add text content

// Get minify content
$minify->minify();

// To file
$minify->toFile($minifyFile);
```

Use static class to process single file:

```php
$minified = \Asika\Minifier\JsMinifier::process($fileOrContent);

$minified = \Asika\Minifier\CssMinifier::process($fileOrContent);
```

## Remove `/*!` Comments

Use `flaggedComments = false` options to remove `/*!` comments:

```php
// Add options for every file
$minify->addFile($path, ['flaggedComments' => false]);

// OR

$minify->addContent($content, ['flaggedComments' => false]);

// OR

\Asika\Minifier\JsMinifier::process($fileOrContent, ['flaggedComments' => false]);

// Use constant as key
$minify->addFile($path, [
    \Asika\Minifier\AbstractMinifier::FLAGGED_COMMENTS => false
]);
```

## Rewrite `url(...)` in CSS files

Add `uri_rewrite` option for every file:

```php
// This css in HTML is `{PUBLIC_ROOT}/foo/bar/yoo.css`

$minify->addFile(
    $file,
    [
        'uri_rewrite' => [ // OR \Asika\Minifier\CssMinifier::URI_REWRITE
            'current_dir' => 'foo/bar/yoo.css',
            'doc_root' => $_SERVER['DOCUMENT_ROOT'] // [Optional]
        ]
    ]
);
```

Or use `UriRewriter` class to rewrite css content:

```php
$css = \Asika\Minifier\CSS\UriRewriter::rewrite(
	$content,
	'foo/bar/yoo.css'
);
```
