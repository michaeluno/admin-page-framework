#!/usr/bin/env bash
downloadCodeception() {

    # The Latest Version
    # For other versions @see https://codeception.com/builds
    # download "http://codeception.com/codecept.phar" "$1"

    # v2.5.4 (stable as of 2019/02/23)
    # download "http://codeception.com/releases/2.5.4/codecept.phar" "$1"

    # v2.5.1 ~ v2.5.4 for PHP 5.6 causes an error PHP Warning: require_once(phar://codecept.phar/autoload.php): failed to open st                          ream: phar error: invalid url or non-existent phar "phar://codecept.phar/autoload. ...\codecept.phar on line 5
    # Maybe related @see https://github.com/Codeception/Codeception/issues/4875
    download "https://codeception.com/releases/2.5.4/php54/codecept.phar" "$1"
    # download "https://codeception.com/releases/2.5.1/php54/codecept.phar" "$1"

    # v2.5.1 (stable as of 2018/11/01) -> does not run on Travis tests against PHP 5.6
    # download "http://codeception.com/releases/2.5.1/codecept.phar" "$1"

    # v2.5.0
    # download "https://codeception.com/releases/2.5.0/codecept.phar" "$1"

    # v2.4.5 -> PHP Warning: Cannot declare class PHPUnit_Framework_TestCase, because the name is already in use in ...\wordpress-tests-lib\includes\phpunit6-compat.php
    # download "https://codeception.com/releases/2.4.5/codecept.phar" "$1"

    # v2.3.9 -> PHP Warning: Cannot declare class PHPUnit_Framework_TestCase, because the name is already in use in ...\wordpress-tests-lib\includes\phpunit6-compat.php
    # download "https://codeception.com/releases/2.3.9/codecept.phar" "$1"

    # v2.2.12 -> with PHP 7.2 [PHPUnit_Framework_Exception] count(): Parameter must be an array or an object that implements Countable
    # download "https://codeception.com/releases/2.2.12/codecept.phar" "$1"

    # v2.2.7 (Stable)
    # download "http://codeception.com/releases/2.2.7/php54/codecept.phar" "$1"

    # v2.1.6
    # download "http://codeception.com/releases/2.1.6/codecept.phar" "$1"

    # v2.1 - $I->click() does not work...

    # v2.0.12 - Working but unable to set cURL timeouts in PHPBrowser.
    # download "http://codeception.com/releases/2.0.12/codecept.phar" "$1"

    # v2.0.11 - Unable to set cURL timeouts in PHPBrowser.
    # download "http://codeception.com/releases/2.0.11/codecept.phar" "$1"

    # v2.0.7 - needs 30000 cURL timeout @see https://github.com/Codeception/Codeception/issues/1984#issuecomment-113561925
    # but this version does not load accurate urls in PHPBrowser.
    # download "http://codeception.com/releases/2.0.7/codecept.phar" "$1"

    # v1.8.7 - causes an error " Path for logs is not writable. Please, set appropriate access mode for log"
    # download "http://codeception.com/releases/1.8.7/codecept.phar" "$1"

    if [[ ! $(find "$1" -type f -size +0c 2>/dev/null) ]]; then
        echo Could not download Codeception.
        exit 1
    fi
    # Output the version in case an error occurs.
    # Codeception Builds for PHP 5.x has been not able to call the file with absolute path
    # php "$1" --version

    # c3
    # @see  https://github.com/Codeception/c3
    # download "https://raw.github.com/Codeception/c3/2.0/c3.php" "$C3"
    # if [[ ! $(find "$C3" -type f -size +0c 2>/dev/null) ]]; then
      #  echo Could not download c3.php.
      #  exit 1
    # fi


}
