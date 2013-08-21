## AdminPageFramework ##
**AdminPageFramework** is the class you would extend to create administration pages and the following methods belong to this class. 
***

### setUp() ###
Triggered by the *wp_loaded* hook. Your initial setups should be defined in this method. To perform certain tasks prior to this method, use the `start_ + extended class name` hook that is triggered at the end of the class constructor.
***

### setRootMenuPage( $strRootMenuLabel, $strURLIcon16x16=null, $intMenuPosition=null ) ###
Determines to which top level page is going to be adding sub-pages. Only one root page can be set per one class instance.

#### Parameters ####
* $strRootMenuLabel - ( string ) If the method cannot find the passed string from the following listed items, it will create a top level menu item with the passed string. ( case insensitive )
	* Dashboard
	* Posts
	* Media
	* Links
	* Pages      
	* Comments       
	* Appearance   
	* Plugins       
	* Users           
	* Tools           
	* Settings       
	* Network Admin
* $strURLIcon16x16 - ( optional, string ) The URL of the menu icon. The size should be 16 by 16 in pixel.
* $intMenuPosition - ( optional, integer ) The position number that is passed to the ***$position*** parameter of the [add_menu_page()](http://codex.wordpress.org/Function_Reference/add_menu_page) function.
***

### setRootMenuPageBySlug( $strRootMenuSlug ) ###
Sets the parent menu by page slug. This is useful when adding pages to an existing plugin top level menu.

#### Parameters ####
* $strRootMenuSlug – ( string ) The slug name of the parent menu to which sub pages are going to be added.
***


### addSubMenuItems() ###
work in progress
***

### addInPageTabs() ###
work in progress
***

### showPageHeadingTabs() ###
work in progress
***

### setInPageTabTag() ###
work in progress
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

### setSettingsNotice() ###
work in progress
***

## AdminPageFramework_PostType ##
**AdminPageFramework_PostType** is the class you would extend to add a custom post type. 
***

### __consturct() ###
work in progress
***

### setAutoSave() ###
work in progress
***

### setAuthorTableFilter() ###
work in progress
***

### addTaxonomy() ###
work in progress
***

## AdminPageFramework_MetaBox ##
**AdminPageFramework_MetaBox** is the class you would extend to add custom meta boxes. 
***

### __consturct() ###
work in progress
***

### addSettingFields() ###
work in progress
***