# Admin Page Framework Test Suite

Employes [Codecception](http://codeception.com/). What you need to do is to configure the settings and run the scripts.

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
bash install.sh` 
```

3. Run the test by typing the following. 

```
bash run.sh
```

4. When tests are done, to uninstall the test site, run the uninstaller script by tying the following.

```
bash uninstall.sh
```