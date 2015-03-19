#!/usr/bin/env bash

SCRIPT_NAME="WordPress Plugin The Test Suite Script Executor"
SCRIPT_VERSION="1.0.0"
WORKING_DIR=$(pwd)

# Include scripts defining functions
source $(dirname $0)/include/download.sh
source $(dirname $0)/include/info.sh

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
download http://codeception.com/codecept.phar "$CODECEPT"

# Check if the codecemption configuration file exists.
if [ ! -f "$CODECEPT_TEST_DIR/codeception.yml" ]; then
    echo The codeception setting file could not be located.
    exit 1
fi

# Run tests
# @usage    php codecept run -c /path/to/my/project
# @see      http://codeception.com/install
# @bug      the --steps option makes the coverage not being generated
if [[ $WP_MULTISITE = 1 ]]; then    
    echo "Testing against a multi-site."
    OPTION_SKIP_GROUP=
    OPTION_GROUP="--group multisite --group ms-files"
else
    echo "Testing against a normal site."
    OPTION_SKIP_GROUP="--skip-group multisite"
    OPTION_GROUP=
fi    
if [[ ! -z "$COVERAGE_FILE_PATH" ]]; then
    OPTION_COVERAGE="--coverage-xml"
    OPTION_COVERAGE="--coverage-xml --coverage-html"
else 
    OPTION_COVERAGE=
fi

php "$CODECEPT" run acceptance --report --colors --config="$CODECEPT_TEST_DIR" $OPTION_GROUP $OPTION_SKIP_GROUP
php "$CODECEPT" run functional --report --colors --config="$CODECEPT_TEST_DIR" $OPTION_GROUP $OPTION_SKIP_GROUP $OPTION_COVERAGE
php "$CODECEPT" run unit --report --colors --config="$CODECEPT_TEST_DIR" $OPTION_GROUP $OPTION_SKIP_GROUP

# Copy the coverage file to the specified path
if [[ ! -z "$COVERAGE_FILE_PATH" ]]; then

    # Convert it to absolute path
    GENERATED_COVERAGE_DIR_PATH="$(cd "$(dirname "$CODECEPT_TEST_DIR/tests/_output")"; pwd)/$(basename "$CODECEPT_TEST_DIR/tests/_output")"
    GENERATED_COVERAGE_XML_FILE_PATH="$GENERATED_COVERAGE_DIR_PATH/coverage.xml"
    if [ ! -f "$GENERATED_COVERAGE_XML_FILE_PATH" ]; then
        echo "The xml coverage file could not be found: $GENERATED_COVERAGE_XML_FILE_PATH"
    else
        echo "Copying the xml coverage file to the specified location."
        cd "$WORKING_DIR"
        cp -f "$GENERATED_COVERAGE_XML_FILE_PATH" "$COVERAGE_FILE_PATH"
    fi    
fi

echo "Tests have completed!"