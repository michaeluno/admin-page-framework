#! /bin/bash

# See https://github.com/GaryJones/wordpress-plugin-git-flow-svn-deploy for instructions and credits.
# Modified by Michael Uno
echo
echo "WordPress Plugins/Theme Coding Standard Checker"
echo "------------------------------------------------------"
echo

WORKING_DIR=$(pwd)

############################### FUNCTIONS ##############################
# Downloads a file
# Example:
# download http://codeception.com/codecept.phar /tmp/codecept.phar
download() {
    
    # If the file size is more than 0 byte, do not download.
    if [[ $(find "$2" -type f -size +0c 2>/dev/null) ]]; then
        echo "Download: Using the cached file."
        return
    fi    

    if [ `which curl` ]; then
        # Not sure why but enclosing paths in quotes results in an empty file
        curl -s $1 > $2
        
        # Sometimes curl fails to fill the file contents although it creates a file.
        if [[ ! $(find "$1" -type f -size +0c 2>/dev/null) ]]; then
            # Try with wget as the above function is default to curl
            echo Could not fill the file. Now trying with wget.
            wget -nv -O "$2" "$1" --no-check-certificate 1> NUL 2> NUL
        fi 
        
    elif [ `which wget` ]; then
        wget -nv -O "$2" "$1" --no-check-certificate 1> NUL 2> NUL
    fi        
    
}

#################################### Start ###############################

# Configuration File
CONFIGURATION_FILE_PATH="settings.cfg"
if [ -f "$CONFIGURATION_FILE_PATH" ]; then
    source "$CONFIGURATION_FILE_PATH"
    echo "Using the configuration file: $CONFIGURATION_FILE_PATH"
    echo
fi

TEMP=$([ -z "${TEMP}" ] && echo "/tmp" || echo "$TEMP")
COMPOSER="$TEMP/composer.phar"
PHPCS="$TEMP/phpcs.phar"
PHPCBF="$TEMP/phpcbf.phar"
WPCSDIR="$TEMP/wpcs"


echo current dir: "$(pwd)"
echo cd "$WORKING_DIR"
cd "$WORKING_DIR"

# Install PHP Code Sniffer
if [ ! -f "$PHPCS" ]; then
    download https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar "$PHPCS"
fi
if [ ! -f "$PHPCBF" ]; then
    download https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar "$PHPCBF"
fi

# Install Composer
if [ ! -f "$COMPOSER" ]; then
    download https://getcomposer.org/composer.phar "$COMPOSER"
fi

# Echo commends
# set -e
set -ex

# Install WPCS using Composer

## Make sure no old file exists.
if [ -d "$WPCSDIR" ]; then
    rm -rf "$WPCSDIR"    
fi
echo WPCS Directory Path: "$WPCSDIR"

## Generate dependencies. 

### Determine OS and set the json extension file
if [ "$(expr substr $(uname -s) 1 10)" == "MINGW32_NT" ]; then
    PHPFILEEXT="dll"
else
    PHPFILEEXT="so"
fi

if [ -f "$WORKING_DIR/$PHPINI" ]; then
    PHP_OPTION_CUSTOM_INI="-c $WORKING_DIR/$PHPINI"
else 
    PHP_OPTION_CUSTOM_INI=
fi
php $PHP_OPTION_CUSTOM_INI "$COMPOSER" create-project wp-coding-standards/wpcs:dev-master "$WPCSDIR" --no-dev --no-ansi --no-interaction

## Copy custom coding standard definitions.
if [ -d "$WPCSDIR/$CUSTOM_DEFINITION_DIRNAME" ]; then
    rm -rf "$WPCSDIR/$CUSTOM_DEFINITION_DIRNAME"
fi
if [ -d "$WORKING_DIR/$CUSTOM_DEFINITION_DIRNAME" ]; then
    ## cp source destination
    cp -rf "$WORKING_DIR/$CUSTOM_DEFINITION_DIRNAME" "$WPCSDIR/$CUSTOM_DEFINITION_DIRNAME"
fi

# Run Checks
## Register the coding standards
# php "$PHPCS" --config-set installed_paths "$WPCSDIR"
# php "$PHPCS" -i
# php "$PHPCS" --standard="$CUSTOM_DEFINITION_DIRNAME" "$SCANDIRPATH"

# Fix errors
php "$PHPCBF" --config-set installed_paths "$WPCSDIR"
php "$PHPCBF" -i
for _i in "${SCANDIRPATHS[@]}"
do
   php "$PHPCBF" -w --no-patch --standard="$CUSTOM_DEFINITION_DIRNAME" "$_i"
done

echo "*** FIN ***"
# start notice.mp3
$SHELL
