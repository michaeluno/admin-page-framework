#!/usr/bin/env bash

if [ ! -f settings.cfg ]; then
    echo The setting file could not be loaded.
    exit 1
fi
source settings.cfg

WORKINGDIR=$(pwd)
PLUGINDIR=$WORKINGDIR/../..

set -ex

install_wordpress() {

    if [ ! -d "$WP_TEST_DIR" ]; then
        # Control will enter here if $DIRECTORY exists.
        
        # Remove the destination folder if exists
        rm -rf $WP_TEST_DIR
      
        # We use wp-cli command
        wp core download --force --path=$WP_TEST_DIR         
      
    fi

    # Change to the WordPres install directory.
    cd $WP_TEST_DIR
    rm -f wp-config.php
    wp core config --dbname=$DB_NAME --dbuser="$DB_USER" --dbpass="$DB_PASS"$EXTRA  <<PHP
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
PHP
    
    # Renew the database table
    setup_database_table    

    # Create/renew the database
    wp core install --url="$WP_URL" --title="$WP_SITETITLE" --admin_user="$WP_ADMINUSERNAME" --admin_password="$WP_ADMINPASSWORD" --admin_email="$WP_ADMINEMAIL"

    
}
    setup_database_table(){

        RESULT=`mysql -u$DB_USER -p$DB_PASS --skip-column-names -e "SHOW DATABASES LIKE '$DB_NAME'"`
        if [ "$RESULT" == "$DB_NAME" ]; then
            wp db drop --yes
        fi
    
        # mysql -u $DB_USER -p$DB_PASS -e --f "DROP $DB_NAME"
        # mysqladmin -u$#DB_USER -p$DB_PASS drop -f $DB_NAME
        wp db create
        
    }
    
install_plugin() {
        
    # Directly removing the directory sometimes fails saying it's not empty. So move it to a different location and then remove.
    if [ ! -d $WP_TEST_DIR/wp-content/plugins/$PLUGIN_SLUG /tmp/$PLUGIN_SLUG ]; then
        mv -f $WP_TEST_DIR/wp-content/plugins/$PLUGIN_SLUG /tmp/$PLUGIN_SLUG
        rm -rf /tmp/$PLUGIN_SLUG
        # rm -rf $WP_TEST_DIR/wp-content/plugins/$PLUGIN_SLUG
    fi    
    
    # The ln command gives "Protocol Error" on Windows hosts so use the cp command.
    cp -r "$PLUGINDIR" "$WP_TEST_DIR/wp-content/plugins/$PLUGIN_SLUG"
 
    # wp cli command
    wp plugin activate $PLUGIN_SLUG
    
}

install_codeception() {
    
    cd $WP_TEST_DIR/wp-content/plugins/$PLUGIN_SLUG/test/codeception
    
    if [ ! -f $WP_TEST_DIR/wp-content/plugins/$PLUGIN_SLUG/test/codeception/codecept.phar ]; then
        wget -O $WP_TEST_DIR/wp-content/plugins/$PLUGIN_SLUG/test/codeception/codecept.phar http://codeception.com/codecept.phar
        # wget http://codeception.com/codecept.phar
    fi    
    
    # Run the bootstrap. This generates necessary files.
    php $WP_TEST_DIR/wp-content/plugins/$PLUGIN_SLUG/test/codeception/codecept.phar bootstrap $WP_TEST_DIR/wp-content/plugins/$PLUGIN_SLUG/test/codeception/
    
    # Create the acceptance setting files.
    FILE=$WP_TEST_DIR/wp-content/plugins/$PLUGIN_SLUG/test/codeception/tests/acceptance.suite.yml
    cat <<EOM >$FILE
class_name: AdminPageFramework_AcceptanceTester
modules:
    enabled:
        - PhpBrowser
        - AcceptanceHelper
    config:
        PhpBrowser:
            url: '$WP_URL'
EOM
   # Create the codeception setting file
   FILE=$WP_TEST_DIR/wp-content/plugins/$PLUGIN_SLUG/test/codeception/codeception.dist.yml
   cat <<EOM >$FILE
actor: AdminPageFramework_Tester
paths:
    tests: tests
    log: tests/_output
    data: tests/_data
    helpers: tests/_support
settings:
    bootstrap: _bootstrap.php
    colors: true
    memory_limit: 1024M
modules:
    config:
        Db:
            dsn: 'mysql:host=$DB_HOST;dbname=$DB_NAME'
            user: '$DB_USER'
            password: '$DB_PASS'
            dump: 'tests/_data/dump.sql'
            populate: true
            cleanup: false
EOM
   
   
}

install_wordpress
install_plugin
install_codeception