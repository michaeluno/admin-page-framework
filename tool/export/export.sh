#!/usr/bin/env bash

if [ ! -f export.cfg ]; then
    echo The setting file could not be loaded.
    exit 1
fi

source export.cfg

WORKING_DIRECTORY_PATH=$(pwd)
OUTPUT_DIRECTORY_PATH=${OUTPUT_DIRECTORY_PATH%/} #remove trailing slash
PATH_TO_CALCULATE=$WORKING_DIRECTORY_PATH/$OUTPUT_DIRECTORY_PATH
OUTPUT_DIRECTORY_PATH=$(cd $PATH_TO_CALCULATE; pwd)    # convert it to absolute path

ARCHIVE_FILE_PATH=$OUTPUT_DIRECTORY_PATH/$PROJECT_SLUG.zip
ARCHIVE_DIRECTORY_PATH=$OUTPUT_DIRECTORY_PATH/$PROJECT_SLUG

# Exit if a command fails and print each command line
set -ex

cd $LOCAL_WORKING_COPY_DIRECTORY_PATH

# Create a zip file from the project files.
rm -f "$ARCHIVE_FILE_PATH"  # remove the archive file if exists
git archive --format zip --output "$ARCHIVE_FILE_PATH" --prefix=$PROJECT_SLUG/ HEAD

# Extract files.
rm -rf "$ARCHIVE_DIRECTORY_PATH"    # remove the specified directory if exists
mkdir "$ARCHIVE_DIRECTORY_PATH"
unzip -qo "$ARCHIVE_FILE_PATH" -d "$ARCHIVE_DIRECTORY_PATH"   

# Open the directory
start "$OUTPUT_DIRECTORY_PATH"