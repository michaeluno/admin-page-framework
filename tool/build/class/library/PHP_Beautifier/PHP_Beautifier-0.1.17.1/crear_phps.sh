#!/bin/bash
find ./ -iname "*.php" -exec php_beautifier -l "ArrayNested() Pear()" {} ./site/phps/{}s \;
