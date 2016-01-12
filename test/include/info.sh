# Displays script information
# The following variables need to be set in the caller script.
# - SCRIPT_NAME
# - SCRIPT_VERSION

printVersion() {
    echo "$SCRIPT_NAME $SCRIPT_VERSION"
}
printUsage() {
    printVersion
    cat << EOF
-----------------------------------------------
usage: $0 options

OPTIONS:
    -c      Configuration file path
    -v      Show version
    -l      Coverage log file path. Available only for run.sh.
EOF
}
