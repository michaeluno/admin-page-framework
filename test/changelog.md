Change Log

### 1.1.2 - 2019/02/23
- Fixed an issue that has no longer been able to run on Travis tests against PHP 5.6 by changing the Codeception version to v2.5.4 for the PHP 5.x build. 

### 1.1.1
- Added some environment variables in the settings, accessible within the script and processes run from it. 
- Made the setting variable accessible from PHP test code.
- Fixed some tests were not running properly with `run.sh`, `run.functional.sh`, and `run.acceptance.sh`.
- Fixed an issue that NUL is created on Windows systems after downloading files. 

### 1.1.0
- Supported the configuration distribution files: `codeception.dist.yml`, `acceptance.suite.dist.yml`, `functional.suite.dist.yml`, `unit.suite.dist.yml`.
- Fixed an incompatibility issue with latest Codeception due to incorrect datable table prefix.
- Made it possible to set a table name prefix of a database installed by wp-cli.   
- Updated WP-CLI to 2.0.1 from 1.0.0.
- Updated Codeception to 2.5.1 from 2.2.7 

### 1.0.0
- Released.