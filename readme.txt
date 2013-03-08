=== Admin Page Framework ===
Contributors: Michael Uno, miunosoft
Donate link: http://michaeluno.jp/en/donate
Tags: admin, administration panel, admin panel, option page, option pages, option, options, setting, settings, Settings API, API, framework, library, class, development tool, developers
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 1.0.0.2
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
	$this->AddSubMenu(  'My First Setting Page',    // page and menu title
						'my_first_settings_page' ); // page slug
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
		$this->AddSubMenu(  'My First Setting Page',    // page and menu title
							'my_first_settings_page' ); // page slug
							
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

== To do ==

== Changelog ==

= 1.0.1.1 =
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