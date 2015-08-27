#!/usr/bin/env bash
downloadCodeception() {
    
    # The Latest Version
    # download "http://codeception.com/codecept.phar" "$1"        
    
    # v2.1 - does not work...
    
    # v2.0.11
    download "http://codeception.com/releases/2.0.11/codecept.phar" "$1"
    
    # v1.8.7 - causes an error " Path for logs is not writable. Please, set appropriate access mode for log"
    # download "http://codeception.com/releases/1.8.7/codecept.phar" "$1"        
    
    if [[ ! $(find "$1" -type f -size +0c 2>/dev/null) ]]; then
        echo Could not download Codeception.
        exit 1
    fi
    # Output the version in case an error occurs.
    php "$1" --version      
    
    # c3 
    # @see  https://github.com/Codeception/c3
    # download "https://raw.github.com/Codeception/c3/2.0/c3.php" "$C3"
    # if [[ ! $(find "$C3" -type f -size +0c 2>/dev/null) ]]; then
      #  echo Could not download c3.php.
      #  exit 1
    # fi
  
    
}