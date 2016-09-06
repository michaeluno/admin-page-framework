# Contributing To Admin Page Framework

Community made patches, localisations, bug reports and contributions are always welcome and are crucial to ensure Admin Page Framework remains the top quality developer platform on WordPress.

When contributing please ensure you follow the guidelines below so that we can keep on top of things.

__Please Note:__ GitHub is for bug reports and contributions only - if you have a support question or a request for a customization don't post here, go to our [Support page](https://wordpress.org/support/plugin/admin-page-framework) instead. If your ticket is considered a support request, it will be labelled as so.

You would need to access the code of the core files to add modifications. The downloadable zip file does not include core files but the complied library files only.

## Getting Started

* Submit a ticket for your issue, assuming one does not already exist.
  * Raise it on our [Issue Tracker](https://github.com/michaeluno/admin-page-framework/issues)
  * Clearly describe the issue including the _steps_ to reproduce the bug.
  * Make sure you fill in the earliest version that you know has the issue as well as the version of WordPress you're using.

### Getting Core Files 
In order to modify the framework development files, clone the repository files. 

#### Git
With Git, simply clone the repository from either of the following addresses.

##### ssh
```
git@github.com:michaeluno/admin-page-framework.git
```

##### https
```
https://github.com/michaeluno/admin-page-framework.git
```
    
#### [Composer](https://getcomposer.org/)
With Composer, create a `composer.json` file with the following contents and perform `php composer.phar install`.

```
{
    "require": {
        "michaeluno/admin-page-framework": "dev-master"
    }
}
```

## Adding and Submitting Changes

* Fork the repository on GitHub.
* Make the changes to your forked repository.  
The core files are located in the `development` directory. 
    1. Make sure the constant `WP_DEBUG` is set to `true` in the `wp-config.php` file of your development site. If this is `false`, the loader plugin will load the complied files in the `library` directory.
    2. Modify the core files and make sure your changes take effect. 
        - Ensure you stick to the [Admin Page Framework Coding Standards](./coding_standard.md).
    3. If you add class files, make sure to run `run.sh` in `tool/inclusion_class_list` to generate inclusion file lists.
    4. After all the modifications are done, run `run.sh` in `tool/beautifier` to compile the files.
    
* When committing, reference your issue (if present) and include a note about the fix.
* If possible, and if applicable, please also add/update unit/functional/acceptance tests for your changes.
* Push the changes to your fork and submit a pull request to the [dev](https://github.com/michaeluno/admin-page-framework/tree/dev) branch of the Admin Page Framework repository.

## Code Documentation

* We ensure that every Admin Page Framework function is documented well and follows the standards set by phpDoc.
* An example function can be found [here](https://gist.github.com/sunnyratilal/5308969).
* Please make sure that every function is documented so that when we update our API Documentation things don't go awry!
	* If you're adding/editing a function in a class, make sure to add `@access {private|public|protected}`
* Finally, please use 4 spaces and not tabs for indentations.

At this point you're waiting on us to merge your pull request. We'll review all pull requests, and make suggestions and changes if necessary.

# Additional Resources
- [Tutorials](http://admin-page-framework.michaeluno.jp/tutorials/)
- [Manual](http://admin-page-framework.michaeluno.jp/en/v3/package-AdminPageFramework.html)
- [FAQ](https://wordpress.org/plugins/admin-page-framework/faq/)
- [Other Notes](https://wordpress.org/plugins/admin-page-framework/other_notes/)
- [Change Log](https://wordpress.org/plugins/admin-page-framework/changelog/)
- [How to Run Admin Page Framework Tests](./test/readme.md)
- [GitHub Pull Request documentation](https://help.github.com/send-pull-requests/)
- [Codeception Tests Guide](http://codeception.com/docs/02-GettingStarted)
