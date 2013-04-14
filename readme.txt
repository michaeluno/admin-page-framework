=== Admin Page Framework ===
Contributors: Michael Uno, miunosoft
Donate link: http://michaeluno.jp/en/donate
Tags: admin, administration panel, admin panel, option page, option pages, option, options, setting, settings, Settings API, API, framework, library, class, development tool, developers
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides simpler means of building administration pages for plugin and theme developers. 

== Description ==
It provides plugin and theme developers with easier means of creating option pages. Extend the library class and pass your arrays that define the form elements to the predefined class methods. The library handles all the complex coding behind the scene and creates the pages for you. The package includes a demo plugin which helps you walk through necessary features.

**Notes:** this framework does not do anything by itself. If you are not a developer, you do not need this.

= Features =
* **Extensible** - the created admin pages will become highly extensible with the automatically created hooks. In other words, it empowers other developers to customize your plugin or theme. That will result on making your projects grow.
* **Import and Export Options** - buttons that the user can import and export settings by uploading and downloading the text file.
* **Image Upload** - it lets the user easily upload images to the site or the user can choose from existent urls or already uploaded files.
* **Settings API Implemented** - it uses the WordPress Settings API for creating the form so the standard option design will be implemented.
* **Validation and Error Messages** - with the pre-defined validation callbacks, the user's submitting data can be verified as a part of using the Settings API. Furthermore, by setting the error array, you can display the error message to the user.

= Supported Field Types =
* Text 
* Password
* Textarea
* Radio Buttons
* Checkboxes
* Dropdown List
* Buttons
* Hidden Fields 
* File Upload 
* Image Upload (Custom File Upload)
* Option Export and Import (Custom File Upload)
* Post Types (Custom Checkboxes)
* Categoris (Custom Checkboxes)

= Necessary Files =
* **`admin-page-framework.php`** is in the classes folder.

= Documentation =
* [Getting Started](http://en.michaeluno.jp/admin-page-framework/get-started/ "Get Started")
* [Demos](http://en.michaeluno.jp/admin-page-framework/demos/ "Demos")
* [Methods](http://en.michaeluno.jp/admin-page-framework/methods/ "Methods")
* [Hooks and Callbacks](http://en.michaeluno.jp/admin-page-framework/hooks-and-callbacks/ "Hooks and Callbacks") 

== Screenshots ==
1. **Text Fields**
2. **Selector and Checkboxes**
3. **Image and Upload**
4. **Form Verification**
5. **Import and Export**
6. **Category and Post Type Checklist**

== Installation ==
= Getting Started =
1. Include **`admin-page-framework.php`** that is located in the **`classes`** folder into your theme or plugin.
`if ( !class_exists( 'Admin_Page_Framework' ) )
    include_once( dirname( __FILE__ ) . '/classes/admin-page-framework.php' );`
	
1. Extend the Library Class.
`class APF_GettingStarted extends Admin_Page_Framework {
}`

1. Define the SetUp() Method.
`function SetUp() {
	$this->SetRootMenu( 'Settings' );               // specifies to which parent menu to belong.
	$this->AddSubMenu(
		'My First Setting Page',    // page and menu title
		'my_first_settings_page' 	// page slug
	); 
}`

1. Define methods for hooks.
`function do_my_first_settings_page() {  // do_ + pageslug	
	?>
	<h3>Say Something</h3>
	<p>This is my first admin page!</p>
	<?php
}`

1. Instantiate the Class.
`new APF_GettingStarted;`

= Example Code = 
`<?php
/* Plugin Name: Admin Page Framework - Getting Started */ 

if ( !class_exists( 'Admin_Page_Framework' ) )
    include_once( dirname( __FILE__ ) . '/classes/admin-page-framework.php' );
	
class APF_GettingStarted extends Admin_Page_Framework {

	function SetUp() {
	
		$this->SetRootMenu( 'Settings' );               // specifies to which parent menu to belong.
		$this->AddSubMenu(
			'My First Setting Page',    // page and menu title
			'my_first_settings_page' 	// page slug
		); 
							
	}

	function do_my_first_settings_page() {  // do_ + pageslug
	
		?>
		<h3>Say Something</h3>
		<p>This is my first admin page!</p>
		<?php
		
	}
	
}
new APF_GettingStarted;

// That's it!`

== Frequently asked questions ==
= What is this for? =
This is	a PHP class library that enables to create option pages and form fields in the administration panel. Also it helps manage to save, export, and import options.

= I've written a useful class and functions. Do you want to include it? = 
The [GitHub repository](https://github.com/michaeluno/admin-page-framework "Admin Page Framework") is avaiable. Create an issue first and we'll see if changes can be made. 

== Roadmap ==
* Add: the ability to remove registered form elements.

== Done ==
* <s>Add: a custom input filed for category select checkboxes</s>. Implemented in 1.0.4.
* <s>Add: the ability to specify a redirect page after the form data is successfully updated.</s> Implemented in 1.0.3.2.

== Changelog ==

= 1.0.4.1 =
* Added: the *if* key for section and field array that evaluates the passed expression to evaluate whether the section or field should be displayed or not.
* Added: the support of the *label* key for the *text* input field and multiple elements to be passed as array.
* Fixed: a bug that the disable field key for the check box type did not take effects when multiple elements were passed as array.

= 1.0.4 - 04/07/2013 =
* Fixed: an issue that the submit field type with the redirect key caused an unset index warning.
* Changed: not to use the get_plugin_data() function if it does not exist to support those who change the location of the wp-admin directory.
* Added: enclosed the checkbox, radio fields and its label in a tag with the *display:inline-block;* property so that each item do not wrap in the middle.
* Added: the *SetSettingsNotice()* method which can be used instead of the *AddSettingsError()* method. The new method does not require an ID to be passed.
* Changed: **(Breaking Change)** the parameters of *SetFieldErrors()* method; the first parameter is now the error array and the second parameter is the ID and it is optional.
* Changed: that when multiple labels were set for the field types that supports multiple labels but the *name* key was set null, it now returns the default value instead of an empty string.
* Tweaked: the settings registration process including sections and fields to be skipped if the loading page is not one of the pages added by the user.
* Improved: the accuracy to retrieve the caller script information.
* Added: the *posttype* field type.
* Added: the *category* field type.

= 1.0.3.3 - 04/02/2013 =
* Fixed: a bug that a debug log file was created after submitting form data introduced in 1.0.3.2.

= 1.0.3.2 - 04/02/2013 =
* Added: the *redirect* field key for the submit input type that redirects the page after the submitted form data is successfully saved.
* Fixed: an issue that when there are multiple submit input fields and the same label was used with the *href* key, the last url was set to previous buttons; the previous buttons urls were overwritten by the last one. 
* Fixed: a bug that a value for the *pre_field* was applied to the *post_field* key in some field types.
* Added: the ability to disable Settings API's admin notices to be automatically displayed after submitting a form by default. To enable the Settings API's notification messages, use the EnableSettingsAPIAdminNotice() method.

= 1.0.3.1 - 04/01/2013 =
* Added: the default message which appears when the settings are saved.
* Changed: to automatically insert plugin information into the plugin footer regardless of whether the second parameter of the constructor is set or not.

= 1.0.3 - 04/01/2013 =
* Added: the *href* field key for the submit field type that makes the button serve like a hyper link.
* Added: the SetFieldErrors() method that enables to set field errors easily without dealing with transients.
* Added: the *AddSettingsError()* and the *ShowSettingsErrors()* methods to be alternated with the settings_errors() and the add_settings_error() functions to prevent multiple duplicate messages to be displayed.
* Added: the ability to automatically insert anchor links to each section and field of form elements.
* Added: the *readonly* field key for text and textarea input fields that inserts the readonly attribute to the input tag.
* Added: the *pre_field* and *post_field* filed keys that adds HTML code right before/after the input element.
* Fixed: a minor bug in the method that merges arrays that did not merge correctly with keys with a null value.

= 1.0.2.3 - 03/17/2013 =
* Added: the ability to set access rights ( capability ) to adding pages individually, which can be set in the newly added fourth parameter of the AddSubMenu() method.

= 1.0.2.2 - 03/17/2013 =
* Changed: (**Breaking Change**) the second parameter of the constructor from capability to script path; the capability can be set via the SetCapability() method.
* Added: the ability to automatically insert script information ( plugin/theme name, version, and author ) into the footer if the second parameter is set in the constructor.

= 1.0.2.1 - 03/16/2013 =
* Added: the capability key for section and field arrays which sets access rights to the form elements.
* Added: a hidden tab page which belongs to the first page with a link back-and-forth in the demo plugin. 
* Changed: the required WordPress version to 3.2 as the newly used filter option_page_capability_{$pageslug} requires it.
* Fixed: an issue that setting a custom capability caused the "Creatin' huh?" message and the user could not change the options.
* Added: the *HideInPageTab()* method which hides a specified in-page tab yet still accessible by the direct url.
* changed: the method name *RenderInPageTabs()* to *GetInPageTabs()* since it did not print anything but returned the output string. 

= 1.0.2 - 03/11/2013 =
* Added: the *export_{suffix}* and *import_{suffix}* filters and the corresponding callback methods to capture exporting/importing array to modify before processing it.
* Suppoerted: multiple export buttons per page.
* Added: the *delimiter* key which delimits multiple fields passed as array including the field types of checkbox, radio, submit, export, import, and file.
* Fixed: to apply the value of the *disable* key to the *import* and *export* custom field.
* Fixed: a bug that an empty string was applied for the *description* key even when it is not set.
* Added: the transient key for the *export* custom field to set a custom exporting array.
* Added: *do_form* action hooks ( tag, page, global ) which are triggered before rendering the form elements after the form opening tag.
* Fixed: a bug that the *file_name* key for the *export* field key did not take effect.

= 1.0.1.2 - 03/09/2013 =
* Fixed: a typo which caused a page not to be added to the Appearance menu.

= 1.0.1.1 - 03/08/2013 =
* Fixed: typos in the demo plugin.
* Changed: error message for a field to display the field value as well in addition to the specified error message.
* Changed: the post_html key to be inserted after the description key.
* Changed: tip key to use the description key if it is not set.


= 1.0.1 - 03/05/2013 =
* Removed: array_replace_recursive() to support PHP below 5.3 and applied an alternative.
* Changed: to use md5() for the error transient name, class name + page slug, to prevent WordPress from failing to retrieve or save options for the character lengths exceeding 45 characters.
* Changed: to echo the value in a user-defined custom field type.
* Added: the *pre_html* and *post_html* keys for input fields that adds extra HTML code before/after the field input and the description.
* Added: the *value* key for input fields that precedes the option values saved in the database.
* Added: the *disable* key for input fields to add disabled="Disabled".

= 1.0.0.2 - 02/17/2013 =
* Fixd a warining in debug mode, undefined index, selectors.
* Added a brief instruction in the demo plugin code and fixed some inaccurate descriptions.

= 1.0.0.1 - 02/15/2013 =
* Fixed a bug that the options were not properly saved when the forms were created in multiple pages.

= 1.0.0.0 - 02/14/2013 = 
* Initial Release