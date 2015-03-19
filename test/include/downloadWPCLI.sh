#!/usr/bin/env bash
downloadWPCLI() {

    download https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar "$1"
    if [[ ! $(find "$1" -type f -size +0c 2>/dev/null) ]]; then
        echo Could not download wp-cii.
        exit 1
    fi

    # Output the wp-cli information in case an error occurs.
    php "$1" --info    
    
}