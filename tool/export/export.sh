#!/usr/bin/env bash

# Override these default settings with the configuration file.
PROJECT_SLUG="sample-slug"
LOCAL_WORKING_COPY_DIRECTORY_PATH="../../"
OUTPUT_DIRECTORY_PATH="../../../output/"
if [ ! -f settings.cfg ]; then
    echo The setting file could not be loaded.
    exit 1
fi
source settings.cfg

# Variables
WORKING_DIRECTORY_PATH=$(pwd)
OUTPUT_DIRECTORY_PATH=${OUTPUT_DIRECTORY_PATH%/} #remove trailing slash
PATH_TO_CALCULATE=$WORKING_DIRECTORY_PATH/$OUTPUT_DIRECTORY_PATH
OUTPUT_DIRECTORY_PATH=$(cd $PATH_TO_CALCULATE; pwd)    # convert it to absolute path


## Start 

cd $LOCAL_WORKING_COPY_DIRECTORY_PATH

### Retrieve the current version
PROJECT_DIRECTORY_PATH=`pwd`
MAIN_FILE_NAME=$([ -z "$MAIN_FILE_NAME" ] && echo "$PROJECT_SLUG.php" || echo "$MAIN_FILE_NAME")
MAIN_FILE_CONTENTS=$(<$PROJECT_DIRECTORY_PATH/$MAIN_FILE_NAME)
VERSION=$(echo $MAIN_FILE_CONTENTS | grep "Version:" $PROJECT_DIRECTORY_PATH/$MAIN_FILE_NAME | awk -F' ' '{print $NF}')
echo $VERSION

### Archive Paths
ARCHIVE_FILE_PATH=$OUTPUT_DIRECTORY_PATH/$PROJECT_SLUG.$VERSION.zip
ARCHIVE_DIRECTORY_PATH=$OUTPUT_DIRECTORY_PATH/$PROJECT_SLUG.$VERSION

# Exit if a command fails and print each command line
set -ex

# Create a zip file from the project files.
rm -f "$ARCHIVE_FILE_PATH"  # remove the archive file if exists
git archive --format zip --output "$ARCHIVE_FILE_PATH" --prefix=$PROJECT_SLUG/ HEAD

# Extract files.
rm -rf "$ARCHIVE_DIRECTORY_PATH"    # remove the specified directory if exists
mkdir "$ARCHIVE_DIRECTORY_PATH"
unzip -qo "$ARCHIVE_FILE_PATH" -d "$ARCHIVE_DIRECTORY_PATH"   

## End

# Open the directory
start "$OUTPUT_DIRECTORY_PATH"
