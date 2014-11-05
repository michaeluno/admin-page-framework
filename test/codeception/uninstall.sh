#!/usr/bin/env bash

source settings.cfg

set -ex

uninstall_wordpress() {
    if [ -d "$WP_TEST_DIR" ]; then
        mv -f $WP_TEST_DIR /tmp/test-$PLUGIN_SLUG
        rm -rf /tmp/test-$PLUGIN_SLUG
    fi
}
uninstall_database_table(){

    RESULT=`mysql -u$DB_USER -p$DB_PASS --skip-column-names -e "SHOW DATABASES LIKE '$DB_NAME'"`
    if [ "$RESULT" == "$DB_NAME" ]; then
        mysqladmin -u$DB_USER -p$DB_PASS drop $DB_NAME --force
    fi
        
}

uninstall_database_table
uninstall_wordpress
echo done.
