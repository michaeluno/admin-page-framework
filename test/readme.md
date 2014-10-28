# Admin Page Framework Test Suite

Employes [Codecception](http://codeception.com/). In order to run the tests. configure the setting file (`settings.cfg`) and run the installer and the executor scripts.

## Requirements

- Unix based server
- wp-cli
- PHP 5.4 or above
- MySQL
- MySQLAdmin

## Steps

1. **Important** Set up necessary paths, database user name password, and host, plugin slug, test site location etc. in `test/codeception/settings.cfg`.
2. cd to `test/codeception/` and run `install.sh` by typing the following command in the console program.

```
bash install.sh
```

3. Run the test by typing the following. 

```
bash run.sh
```

4. When tests are done, to uninstall the test site, run the uninstaller script by tying the following.

```
bash uninstall.sh
```