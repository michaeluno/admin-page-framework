# Github to WordPress Plugin Directory Deployment Script, Git-Flow Version

Deploys a WordPress plugin from Github (git) to the WordPress Plugin Repostiory (svn), taking account of standard [git-flow](https://github.com/nvie/gitflow) usage.

## Credits
Well over 90% of this script was written by others:

 - **[Dean Clatworthy](https://twitter.com/deanclatworthy)** - [Original script](https://github.com/deanc/wordpress-plugin-git-svn)
 - **[Brent Shepherd](https://twitter.com/thenbrent)** - [Avoids permanent local SVN repo, avoids sending redundant stuff to WP repo](http://thereforei.am/2011/04/21/git-to-svn-automated-wordpress-plugin-deployment/)
 - **[Patrick Rauland](https://twitter.com/BFTrick)** - [Support for WP assets folder for plugin page banner and screenshots](https://github.com/BFTrick/jotform-integration/blob/master/deploy.sh)
 - **[Ben Balter](https://twitter.com/benbalter)** - [Submodules support and plugin slug prompt](https://github.com/benbalter/Github-to-WordPress-Plugin-Directory-Deployment-Script/)
 - **[Gary Jones](https://twitter.com/GaryJ)** *(me)* - [Personalisation and commenting out bits not required when using git-flow](https://github.com/GaryJones/wordpress-plugin-git-flow-svn-deploy) 
 
## Process
 1. Prompts for plugin slug and other data.
 - Verifies plugin header version number matches readme stable version number or readme stable version is trunk
 - Pushes latest git commit and tags to GitHub.
 - Creates temporary checkout of SVN repo.
 - Ignores non-WordPress repo files from SVN.
 - Copies git export to SVN trunk.
 - Checks out any submodules.
 - Copies contents of assets directory in trunk to a directory parallel to trunk.
 - Commits SVN trunk, assets and tag.
 - Attempts to remove temporary SVN checkout.

## Install

1. In your terminal, `cd` into the directory which contains subdirectories for each of your plugins. i.e. on a local install of WordPress, this will probably be `wp-content/plugins`. Then `git clone https://github.com/GaryJones/wordpress-plugin-git-flow-svn-deploy.git .` to clone the deploy script locally.
2. Ensure that the shell script is executable. In Mac / Unix, run `chmod +x deploy.sh`.
3. Run the script with `sh deploy.sh`. You can also double-click it in Finder / Explorer to start it.
4. You'll now be guided through a set of questions.

With [git-flow](https://github.com/nvie/gitflow), specifically the `git flow release finish ...` command, the release branch is merged into the develop branch, the master branch and a tag is created, so these aren't needed with this deploy script.
 
I prefer to keep this script in the root of my projects directory. Each project directory is named as the plugin slug, as is the corresponding GitHub repo. To use, just call the script, enter the plugin slug, confirm or amend default suggestions, and sit back as the code is sent to SVN and git repos including tags. The commit messages here are hard-coded for consistency.
