#! /bin/bash

# See https://github.com/GaryJones/wordpress-plugin-git-flow-svn-deploy for instructions and credits.
# Modified by Michael Uno
echo
echo "WordPress Plugin Git-Flow SVN Deploy v2.0.0-dev.mod.01"
echo "------------------------------------------------------"
echo
echo "Step 1. Let's collect some information first."
echo
echo "Default values are in brackets - just hit enter to accept them."
echo

# Configuration File
CONFIGURATION_FILE_PATH="settings.cfg"
if [ -f "$CONFIGURATION_FILE_PATH" ]; then
    source "$CONFIGURATION_FILE_PATH"
    echo "Using the configuration file: $CONFIGURATION_FILE_PATH"
    echo
fi

# Get some user input
# Can't use the -i flag for read, since that doesn't work for bash 3
if [ -z "$PLUGINSLUG" ]; then
    printf "1a) WordPress Repository Plugin Slug e.g. my-awesome-plugin: "
    read -e PLUGINSLUG
    echo
else 
    echo "1a) WordPress Repository Plugin Slug: $PLUGINSLUG"
    echo 
fi


# Set up some default values. Feel free to change these in your own script
CURRENTDIR=`pwd`
SVNPATH=$([ -z "$SVNPATH" ] && echo "/tmp/$PLUGINSLUG" || echo "$SVNPATH") 
SVNURL=$([ -z "$SVNURL" ] && echo "http://plugins.svn.wordpress.org/$PLUGINSLUG" || echo "$SVNURL")
SVNUSER=$([ -z "$SVNUSER" ] && echo "JohnDoe" || echo "$SVNUSER") 
PLUGINDIR=$([ -z "$PLUGINDIR" ] && echo "$CURRENTDIR/$PLUGINSLUG" || echo "$PLUGINDIR") 
MAINFILE=$([ -z "$MAINFILE" ] && echo "$PLUGINSLUG.php" || echo "$MAINFILE")

if [ -z "$SVNPATH" ]; then
    echo "1b) Path to a local directory where a temporary SVN checkout can be made."
    printf "No trailing slash and don't add trunk ($SVNPATH): "
    read -e input
    input="${input%/}" # Strip trailing slash
    SVNPATH="${input:-$SVNPATH}" # Populate with default if empty
    echo
fi

if [ -z "$SVNURL" ]; then
    echo "1c) Remote SVN repo on WordPress.org. No trailing slash."
    printf "($SVNURL): "
    read -e input
    input="${input%/}" # Strip trailing slash
    SVNURL="${input:-$SVNURL}" # Populate with default if empty
    echo
fi

if [ -z "$SVNUSER" ]; then
    printf "1d) Your WordPress repo SVN username ($SVNUSER): "
    read -e input
    SVNUSER="${input:-$SVNUSER}" # Populate with default if empty
    echo
fi    

if [ -z "$SVNPASS" ]; then
    printf "1e) SVN Password: "
    read -s input
    SVNPASS="${input:-$SVNPASS}" # Populate with default if empty
    echo
fi

if [ -z "$PLUGINDIR" ]; then   
    echo "1f) Your local plugin root directory, the Git repo. No trailing slash."
    printf "($PLUGINDIR): "
    read -e  input
    input="${input%/}" # Strip trailing slash
    PLUGINDIR="${input:-$PLUGINDIR}" # Populate with default if empty
    echo
fi
# Convert the directory path to an absolute path
PLUGINDIR="$(cd "$(dirname "$PLUGINDIR")"; pwd)"

if [ -z "$MAINFILE" ]; then 
    printf "1g) Name of the main plugin file ($MAINFILE): "
    read -e input
    MAINFILE="${input:-$MAINFILE}" # Populate with default if empty
    echo
fi

echo "That's all of the data collected."
echo
echo "Slug: $PLUGINSLUG"
echo "Temp checkout path: $SVNPATH"
echo "Remote SVN repo: $SVNURL"
echo "SVN username: $SVNUSER"
echo "SVN password: ****"
echo "Plugin directory: $PLUGINDIR"
echo "Main file: $MAINFILE"
echo 

printf "OK to proceed (y|n)? "
read -e input
PROCEED="${input:-y}"
echo

# Allow user cancellation
if [ "$PROCEED" != "y" ]; then echo "Aborting..."; exit 1; fi

# git config
GITPATH="$PLUGINDIR/" # this file should be in the base of your git repository

# Let's begin...
echo ".........................................."
echo 
echo "Preparing to deploy WordPress plugin"
echo 
echo ".........................................."
echo 

# Check version in readme.txt is the same as plugin file after translating both to unix line breaks to work around grep's failure to identify mac line breaks
PLUGINVERSION=`grep "Version:" $GITPATH/$MAINFILE | awk -F' ' '{print $NF}' | tr -d '\r'`
echo "$MAINFILE version: $PLUGINVERSION"
READMEVERSION=`grep "^Stable tag:" $GITPATH/readme.txt | awk -F' ' '{print $NF}' | tr -d '\r'`
echo "readme.txt version: $READMEVERSION"

if [ "$READMEVERSION" = "trunk" ]; then
	echo "Version in readme.txt & $MAINFILE don't match, but Stable tag is trunk. Let's proceed..."
elif [ "$PLUGINVERSION" != "$READMEVERSION" ]; then
	echo "Version in readme.txt & $MAINFILE don't match. Exiting...."
	exit 1;
elif [ "$PLUGINVERSION" = "$READMEVERSION" ]; then
	echo "Versions match in readme.txt and $MAINFILE. Let's proceed..."
fi

# GaryJ: Ignore check for git tag, as git flow release finish creates this.
#if git show-ref --tags --quiet --verify -- "refs/tags/$PLUGINVERSION"
#	then 
#		echo "Version $PLUGINVERSION already exists as git tag. Exiting...."; 
#		exit 1; 
#	else
#		echo "Git version does not exist. Let's proceed..."
#fi

echo "# Changing to $GITPATH"
cd $GITPATH
# GaryJ: Commit message variable not needed . Hard coded for SVN trunk commit for consistency.
#echo -e "Enter a commit message for this new version: \c"
#read COMMITMSG
# GaryJ: git flow release finish already covers this commit.
#git commit -am "$COMMITMSG"

# GaryJ: git flow release finish already covers this tag creation.
#echo "Tagging new version in git"
#git tag -a "$PLUGINVERSION" -m "Tagging version $PLUGINVERSION"

echo "# Pushing git master to origin, with tags"
git push origin master
git push origin master --tags

echo 
echo "# Creating local copy of SVN repository trunk ..."
svn checkout $SVNURL $SVNPATH --depth immediates
svn update --quiet $SVNPATH/trunk --set-depth infinity

echo "# Ignoring GitHub specific files"
svn propset svn:ignore "README.md
Thumbs.db
.git
.gitignore" "$SVNPATH/trunk/"

echo "# Exporting the HEAD of master from git to the trunk of SVN"
# The below checkout-index command does not respect export-ignore items so we are going to archive files first and unzip it.
# git checkout-index -a -f --prefix=$SVNPATH/trunk/
ARCHIVE_FILE_PATH=$SVNPATH/$PLUGINSLUG.zip
ARCHIVE_DIRECTORY_PATH="$SVNPATH/trunk"
git archive --format zip --output "$ARCHIVE_FILE_PATH" HEAD
rm -rf "$ARCHIVE_DIRECTORY_PATH"    # remove the specified directory if exists
mkdir "$ARCHIVE_DIRECTORY_PATH"
unzip -qo "$ARCHIVE_FILE_PATH" -d "$ARCHIVE_DIRECTORY_PATH"    # extract
rm -f "$ARCHIVE_FILE_PATH"  # remove the archive file

# If submodule exist, recursively check out their indexes
if [ -f ".gitmodules" ]
	then
		echo "# Exporting the HEAD of each submodule from git to the trunk of SVN"
		git submodule init
		git submodule update
		git config -f .gitmodules --get-regexp '^submodule\..*\.path$' |
			while read path_key path
			do
				#url_key=$(echo $path_key | sed 's/\.path/.url/')
				#url=$(git config -f .gitmodules --get "$url_key")
				#git submodule add $url $path
				echo "This is the submodule path: $path"
				echo "The following line is the command to checkout the submodule."
				echo "git submodule foreach --recursive 'git checkout-index -a -f --prefix=$SVNPATH/trunk/$path/'"
				git submodule foreach --recursive 'git checkout-index -a -f --prefix=$SVNPATH/trunk/$path/'
			done
fi

# Support for the /assets folder on the .org repo.
echo "# Moving assets"
# Make the directory if it doesn't already exist
mkdir -p $SVNPATH/assets/
mv $SVNPATH/trunk/assets/* $SVNPATH/assets/
svn add --force $SVNPATH/assets/
svn delete --force $SVNPATH/trunk/assets

echo "# Changing directory to SVN and committing to trunk"
cd $SVNPATH/trunk/
# Delete all files that should not now be added.
# (`sed` converts \ to /)
svn status | grep -v "^.[ \t]*\..*" | grep "^\!" | sed 's/\\/\//g' | awk '{print $2}' | xargs svn del
# Add all new files that are not set to be ignored
svn status | grep -v "^.[ \t]*\..*" | grep "^?" | sed 's/\\/\//g' | awk '{print $2}' | xargs svn add
svn commit --username=$SVNUSER --password=$SVNPASS -m "Preparing for $PLUGINVERSION release"

echo "# Updating WordPress plugin repository assets and committing"
cd $SVNPATH/assets/
# Delete all new files that are not set to be ignored
svn status | grep -v "^.[ \t]*\..*" | grep "^\!" | sed 's/\\/\//g' | awk '{print $2}' | xargs svn del

# Add all new files that are not set to be ignored
svn status | grep -v "^.[ \t]*\..*" | grep "^?" | sed 's/\\/\//g' | awk '{print $2}' | xargs svn add
svn update --accept mine-full $SVNPATH/assets/*
svn commit --username=$SVNUSER --password=$SVNPASS -m "Updating assets"

echo "# Creating new SVN tag and committing it"
cd $SVNPATH
svn update --quiet $SVNPATH/tags/$PLUGINVERSION
svn copy --quiet trunk/ tags/$PLUGINVERSION/
# Remove assets and trunk directories from tag directory
svn delete --force --quiet $SVNPATH/tags/$PLUGINVERSION/assets
svn delete --force --quiet $SVNPATH/tags/$PLUGINVERSION/trunk
cd $SVNPATH/tags/$PLUGINVERSION
svn commit --username=$SVNUSER --password=$SVNPASS -m "Tagging version $PLUGINVERSION"

echo "# Removing temporary directory $SVNPATH"
cd $SVNPATH
cd ..
rm -fr $SVNPATH/

echo "*** FIN ***"
start "$CURRENTDIR/notice.mp3"
$SHELL
