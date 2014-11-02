#!/usr/bin/env bash

source settings.cfg

cd $WP_TEST_DIR/wp-content/plugins/$PLUGIN_SLUG/test/codeception

php codecept.phar run acceptance 