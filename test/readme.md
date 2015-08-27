# Sample TDD Plugin - Test Suite

In order to run the tests, configure the setting file (`settings.cfg`) and run the installer and the executor scripts.

## Requirements

- PHP 5.4 or above - Codeception v2+ requires this version.
- MySQL - the `mysql` command needs to be available.
- MySQLAdmin - the `mysqladmin` command needs to be available.
- SVN - the `svn` command needs to be available.

## Steps

1. **Important** Rename `settings-sample.cfg` in `test/` to `settings.cfg`. Edit the file and set up necessary paths, database user name password, and host, plugin slug, test site location etc.
2. cd to `test/` and run `install.sh` by typing the following command in a console program.

    ```
    bash install.sh
    ```

3. Run the test by typing the following. 

    ```
    bash run.sh
    ```

4. When tests are done, to uninstall the test site and the database, run the uninstaller script by tying the following.

    ```
    bash uninstall.sh
    ```

### Notes

To run `.sh` files on Windows, you need a Bash emulater. Here is [one](https://git-for-windows.github.io/).