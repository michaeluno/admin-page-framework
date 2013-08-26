## AdminPageFramework( $strOptionKey=null, $strCallerPath=null, $strCapability=null, $strTextDomain=null ) ##
**AdminPageFramework** is the class which should be extended to create administration pages and the following methods belong to this class. These methods are designed to be used within the extended class definition. 

#### Parameters ####
* **$strOptionKey** - ( optional, string ) the key used for the database *options* table to store the setting values.
* **$strCallerPath** - ( optional, string ) the script file path. This is used to retrieve the script information to insert into the page footer. 
If this is not specified, the framework will try to determine the caller script path. If the script information is not inserted into the footer properly, set this value.
* **$strCapability** - ( optional, string ) the access level to the created admin pages defined [here](http://codex.wordpress.org/Roles_and_Capabilities). If not set, ***manage_options*** will be assigned. 
The capabilities can be set per page, tab, setting section, setting field, so set this parameter for the overall capability.
* **$strTextDomain** - ( optional, string ) the [text domain](http://codex.wordpress.org/I18n_for_WordPress_Developers#Text_Domains) used for the framework's text strings.

***

### setUp() ###
Triggered by the *[wp_loaded](http://codex.wordpress.org/Plugin_API/Action_Reference/wp_loaded)* action hook. All initial set-ups should be defined in this method. To perform certain tasks prior to this method, use the `start_ + extended class name` hook that is triggered at the end of the class constructor.
***

### setRootMenuPage( $strRootMenuLabel, $strURLIcon16x16=null, $intMenuPosition=null ) ###
Determines to which top level page is going to be adding sub-pages. Only one root page can be set per one class instance.

#### Parameters ####
* **$strRootMenuLabel** - ( string ) if the method cannot find the passed string from the following listed items, it will create a top level menu item with the passed string. ( case insensitive )
 
Dashboard, Posts, Media, Links, Pages, Comments, Appearance, Plugins, Users, Tools, Settings, Network Admin
 
* **$strURLIcon16x16** - ( optional, string ) the URL of the menu icon. The size should be 16 by 16 in pixel.
* **$intMenuPosition** - ( optional, integer ) the position number that is passed to the ***$position*** parameter of the [add_menu_page()](http://codex.wordpress.org/Function_Reference/add_menu_page) function.

#### Example ####
	$this->setRootMenuPage( 'APF Form' );
	
***

### setRootMenuPageBySlug( $strRootMenuSlug ) ###
Sets the parent menu by page slug. This is useful when adding pages to an existing plugin top level menu.

#### Parameters ####
* **$strRootMenuSlug** – ( string ) the slug name of the parent menu to which sub pages are going to be added.

#### Example ####
	$this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );
***


### addSubMenuItems( $arrSubMenuItem1, $arrSubMenuItem2, ... ) ###
Adds sub-menu items.

#### Parameters ####
* **$arrSubMenuItem1**, **$arrSubMenuItem2**, **$arrSubMenuItem3**, ... - ( array ) the passed array can be either *sub-menu page array* or *sub-menu link array* and each array type must consist of the following listed keys.

##### Sub-menu Page Array #####
* **strPageTitle** - ( string ) the page title of the page.
* **strPageSlug** - ( string ) the page slug of the page. Non-alphabetical characters should not be used including dots(.) and hyphens(-).
* **strScreenIcon** - ( optional, string ) either the ID selector name from the following list or the icon URL. The size of the icon should be 32 by 32 in pixel.
	
	edit, post, index, media, upload, link-manager, link, link-category, edit-pages, page, edit-comments, themes, plugins, users, profile, user-edit, tools, admin, options-general, ms-admin, generic

	**Notes**: the *generic* icon is available WordPress version 3.5 and above.

* **strCapability** - ( optional, string ) the access level to the created admin pages defined [here](http://codex.wordpress.org/Roles_and_Capabilities). If not set, the overall capability assigned in the class constructor, which is *manage_options* by default, will be used.
* **numOrder** - ( optional, integer ) the order number of the page. The lager the number is, the lower the position it is placed in the menu.
* **fPageHeadingTab** - ( optional, boolean ) if this is set to false, the page title won't be displayed in the page heading tab. Default: true.

##### Sub-menu Link Array #####
* **strMenuTitle** - ( string ) the link title.
* **strURL** - ( string ) the URL of the target link.
* **strCapability** - ( optional, string ) the access level to show the item, defined [here](http://codex.wordpress.org/Roles_and_Capabilities). If not set, the overall capability assigned in the class constructor, which is *manage_options* by default, will be used.
* **numOrder** - ( optional, integer ) the order number of the page. The lager the number is, the lower the position it is placed in the menu.
* **fPageHeadingTab** - ( optional, boolean ) if this is set to false, the page title won't be displayed in the page heading tab. Default: true.

#### Example ####
	$this->addSubMenuItems(
		array(
			'strPageTitle' => 'Various Form Fields',
			'strPageSlug' => 'first_page',
			'strScreenIcon' => 'options-general',
		),
		array(
			'strPageTitle' => 'Manage Options',
			'strPageSlug' => 'second_page',
			'strScreenIcon' => 'link-manager',
		),
		array(
			'strMenuTitle' => 'Google',
			'strURL' => 'http://www.google.com',	
			'fPageHeadingTab' => false,	// this removes the title from the page heading tabs.
		),
	);
***

### addInPageTabs( $arrTab1, $arrTab2, ... ) ###
Adds in-page tabs to the specified page.

**Notes**: *in-page tabs* are different from *page-heading tabs* which is automatically added with page titles.

#### Parameters ####
* **$arrTab1**, **$arrTab2**, **$arrTab3**, ... - ( array ) the array must consist of the following listed keys.

##### In-Page Tab Array #####
* **strPageSlug** - ( string ) the page slug that the tab belongs to.
* **strTabSlug** - ( string ) the tab slug. Non-alphabetical characters should not be used including dots(.) and hyphens(-).
* **strTitle** - ( string ) the title of the tab.
* **numOrder** - ( optional, integer ) the order number of the tab. The lager the number is, the lower the position it is placed in the menu.
* **fHide** - ( optional, boolean ) default: false. If this is set to *false*, the tab title will not be displayed in the tab navigation menu; however, it is still accessible from the direct URL.
* **strParentTabSlug** - ( optional, string ) this needs to be set if the above *fHide* is true so that the parent tab will be emphasized as active when the hidden page is accessed.

#### Example ####

	$this->addInPageTabs(
		array(
			'strTabSlug' => 'firsttab',
			'strTitle' => __( 'Text Fields', 'my-text-domain' ),
			'strPageSlug' => 'myfirstpage'
		),
		array(
			'strTabSlug' => 'secondtab',
			'strTitle' => __( 'Selectors and Checkboxes', 'my-text-domain' ),
			'strPageSlug' => 'myfirstpage'
		)
	);	
	
***

### showPageHeadingTabs( $fShowPageHeadingTabs=true ) ###
Determines whether page-heading tabs are displayed or not.

**Notes**: *page-heading tabs* and *in-page tabs* are different. The former displays page titles and the latter displays tab titles.

#### Parameters ####
* **$fShowPageHeadingTabs** - ( boolean ) if *false* page-heading tabs will be disabled; otherwise, enabled.

#### Example ####

	$this->showPageHeadingTabs( false );	// disables the page heading tabs by passing false.

***

### setInPageTabTag( $strTag='h3' ) ###
Sets the enclosing HTML tag for in-page tabs. 

#### Parameters ####
* **$strTag** - ( string ) the tag name to enclose the in-page tab elements. Default: h3.

#### Example ####
	$this->setInPageTabTag( 'h2' );	

***

### addSettingSections() ###
work in progress
***

### addSettingFields() ###
work in progress
***

### setFieldErrors() ###
work in progress
***

### setSettingNotice() ###
work in progress
***

### setFooterInfoLeft( $strHTML, $fAppend=true ) ###
Sets the given text into the footer on the left hand side. 

#### Parameters ####
* **$strHTML** - ( string ) the text string to be inserted into the page footer on the left hand side.
* **$fAppend** - ( optional, boolean ) default: true. If false, the passed string in the first parameter replaces the default footer text. If true, it will append the given string to the default footer text.

***

### setFooterInfoRight( $strHTML, $fAppend=true ) ###
Sets the given text into the footer on the right hand side. 

#### Parameters ####
* **$strHTML** - ( string ) the text string to be inserted into the page footer on the right hand side.
* **$fAppend** - ( optional, boolean ) default: true. If false, the passed string in the first parameter replaces the default footer text. If true, it will append the given string to the default footer text.


***

## AdminPageFramework_PostType( $strPostType, $arrArgs=array(), $strCallerPath=null ) ##
**AdminPageFramework_PostType** is the class which should be extended to add a custom post type. 
***

### setAutoSave( $fEnableAutoSave=true ) ###
Enables or disables the auto-save feature in the custom post type's post submission page.

#### Parameters ####
* **$fEnableAutoSave** - ( optional, boolean ) default: true. If false, it will disable the auto-save functionality in the post submit page of the custom post type for the class.

***

### setAuthorTableFilter( $fEnableAuthorTableFileter=false ) ###
Sets whether the author dropdown filter is enabled/disabled in the post type post list table.

#### Parameters ####
* **$fEnableAuthorTableFileter** - ( optional, boolean ) default: false. If true, the author dropdown filter appears at the top of the post listing table of the post type.
***

### addTaxonomy() ###
work in progress
***

### setFooterInfoLeft( $strHTML, $fAppend=true ) ###
Sets the given text into the footer on the left hand side. 

#### Parameters ####
* **$strHTML** - ( string ) the text string to be inserted into the page footer on the left hand side.
* **$fAppend** - ( optional, boolean ) default: true. If false, the passed string in the first parameter replaces the default footer text. If true, it will append the given string to the default footer text.

***

### setFooterInfoRight( $strHTML, $fAppend=true ) ###
Sets the given text into the footer on the right hand side. 

#### Parameters ####
* **$strHTML** - ( string ) the text string to be inserted into the page footer on the right hand side.
* **$fAppend** - ( optional, boolean ) default: true. If false, the passed string in the first parameter replaces the default footer text. If true, it will append the given string to the default footer text.

***

## AdminPageFramework_MetaBox( $strMetaBoxID, $strTitle, $vPostTypes=array( 'post' ), $strContext='normal', $strPriority='default', $arrFields=null, $strCapability='edit_posts', $strTextDomain='admin-page-framework') ##
**AdminPageFramework_MetaBox** is the class which should be extended to add custom meta boxes. 
***


### addSettingFields() ###
work in progress
***