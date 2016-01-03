#!/usr/bin/env bash

SCRIPT_NAME="Sync test files to the directory of the local server"
SCRIPT_VERSION="1.0.0"
WORKING_DIR=$(pwd)

# Include scripts defining functions
source $(dirname $0)/include/download.sh
source $(dirname $0)/include/info.sh
source $(dirname $0)/include/downloadCodeception.sh

# Parse arguments
CONFIGURATION_FILE_PATH="settings.cfg"
COVERAGE_FILE_PATH=
while getopts “ht:c:v:l:” OPTION
do
    case $OPTION in
        h)
            printUsage
            exit 1
            ;;
        v)
            printVersion
            exit 1
            ;;
        l)  
            COVERAGE_FILE_PATH=$OPTARG
            ;;            
        c)
            CONFIGURATION_FILE_PATH=$OPTARG
            ;;
        ?)
            printUsage
            exit 1
            ;;

    esac
done

# Configuration File
if [ ! -f "$CONFIGURATION_FILE_PATH" ]; then
    echo The setting file could not be loaded.
    exit 1
fi
source "$CONFIGURATION_FILE_PATH"
echo "Using the configuration file: $CONFIGURATION_FILE_PATH"

# Set up variables 
WORKING_DIR=$(pwd)
if [[ -z "$PROJECT_DIR" ]]; then
    PROJECT_DIR=$(cd "$WORKING_DIR/.."; pwd)
fi
# convert it to an absolute path
PROJECT_DIR="$(cd "$(dirname "$PROJECT_DIR")"; pwd)/$(basename "$PROJECT_DIR")"
cd "$WORKING_DIR"
TEMP=$([ -z "${TEMP}" ] && echo "/tmp" || echo "$TEMP")
CODECEPT="$TEMP/codecept.phar"

# convert any Windows path to linux/unix path to be usable for some path related commands such as basename
cd "$WP_TEST_DIR"
WP_TEST_DIR=$(pwd)   
CODECEPT_TEST_DIR="$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG/test"

echo "Project Slug: $PROJECT_SLUG"
echo "Codeception Test Dir: $CODECEPT_TEST_DIR"
echo "Coverage File Path: $COVERAGE_FILE_PATH"
set -ex

# Make sure Codeception is installed
downloadCodeception "$CODECEPT"

copyTestFiles() {
        
    # Run the bootstrap to generate necessary files.
    # php "$CODECEPT" bootstrap "$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG/test/"
    
    # Copy the bootstrap script of the functional tests.
    cp -fr "$PROJECT_DIR/test/tests" "$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG/test"
    
}

copyTestFiles
echo Copying test files has been completed!