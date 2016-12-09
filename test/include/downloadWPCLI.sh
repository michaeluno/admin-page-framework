#!/usr/bin/env bash
downloadWPCLI() {

    # Latest
    # download https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar "$1"
    
    # 1.0.0
    download https://github.com/wp-cli/wp-cli/releases/download/v1.0.0/wp-cli-1.0.0.phar "$1"
    
    # 0.24.1 @issue https://github.com/wp-cli/wp-cli/issues/2953, 
    # download https://github.com/wp-cli/wp-cli/releases/download/v0.24.1/wp-cli-0.24.1.phar "$1"
    
    # 0.20.4 Problem: Creates a directory to the project working dir
    # download https://github.com/wp-cli/wp-cli/releases/download/v0.20.4/wp-cli-0.20.4.phar "$1"
    
    # 0.20.2 Fatal error: Class 'WP_REST_Server' not found in .../wordpress-tests-lib/includes/spy-rest-server.php on line 3
    # https://github.com/wp-cli/wp-cli/releases/download/v0.20.2/wp-cli-0.20.2.phar
        
    # 0.20.0
    # download https://github.com/wp-cli/wp-cli/releases/download/v0.20.0/wp-cli-0.20.0.phar "$1"
    
    # 0.17.2 Some commands do not work
    # download https://github.com/wp-cli/wp-cli/releases/download/v0.17.2/wp-cli-0.17.2.phar "$1"    
    
    if [[ ! $(find "$1" -type f -size +0c 2>/dev/null) ]]; then
        echo Could not download wp-cii.
        exit 1
    fi

    # Output the wp-cli information in case an error occurs.
    php "$1" --info    
    
}
