=== Admin Page Framework ===
Contributors: Michael Uno, miunosoft
Donate link: http://michaeluno.jp/en/donate
Tags: administration panel, admin panel, option page, option pages, option, options, setting, settings, framework, libraray, class
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides simpler means of building administration pages for plugin and theme developers. 

== Description ==

This framework class PHP library provides plugin and theme developers with easier means of creating option pages. Extend the library class and pass your arrays that define the form elements to the predefined class methods. The library handles all the complex coding behind the scene and creates the pages for you. The package includes demo plugins and documentation plugin that help you walk through all necessary features.

**Notes:** this framework does not do anything by itself. If you are not a developer, you do not need this.

<h4>Features</h4>
* Extensible ? the created admin pages will become highly extensible with the automatically created hooks. In other words, it empowers other developers to customize your plugin or theme. That will result on making your projects grow.
* Import and Export Options ? buttons that the user can import and export settings by uploading and downloading the text file.
* Image Upload - it lets the user easily upload images to the site or the user can choose from existent urls or already uploaded files.
* Demo plugins ? walkthrough demo plugins are included
* Documentation plugin ? the help file is included as a plugin.

<h4>Supported Field Types</h4>
* Text 
* Password
* Textarea
* Radio Buttons
* Checkboxes
* Dropdown List
* Buttons
* Hidden Fields 
* File Upload 

<h4>Necessary Files</h4>
* **`admin-page-framework.php`** is in the classes folder.

<h4>Documentation</h4>
* [Getting Started](http://en.michaeluno.jp/admin-page-framework/get-started/ "Get Started")
* [Demos](http://en.michaeluno.jp/admin-page-framework/demos/ "Demos")
* [Methods](http://en.michaeluno.jp/admin-page-framework/methods/ "Methods")
* [Hooks and Callbacks](http://en.michaeluno.jp/admin-page-framework/hooks-and-callbacks/ "Hooks and Callbacks") 

== Installation ==

<h4>Getting Started</h4>
1. Include **`admin-page-framework.php`** that is located in the **`classes`** folder into your theme or plugin.
`if ( !class_exists( 'Admin_Page_Framework' ) ) 
    include_once( dirname( __FILE__ ) . '/classes/admin-page-framework.php' );`
2. Extend the Library Class
`class APF_CreateSettingPage extends Admin_Page_Framework {
}`
3. Define the SetUp() Method. 
`function SetUp() {
    $this->SetRootMenu( 'Settings' );               // specifies to which parent menu to belong.
    $this->AddSubMenu(  'My First Setting Page',    // page and menu title
                        'my_first_settings_page' ); // page slug
}`
4. Define methods for hooks.
`function do_my_first_settings_page() {  // do_ + pageslug
    ?>
    <h3>Say Something</h3>
    <p>This is my first admin page!</p>
    <?php
}`
5. Instantiate the Class
`new APF_CreateSettingPage;`
	
== Frequently asked questions ==

= What is this for? =
This is	a PHP class library that enables to create option pages and form fields in the administration panel. Also it helps manage to save, export, and import options.

= I've written a useful class and functions. Do you want to include it? = 
The GitHub repository is avaiable. Create an issue first and we'll see if changes can be made. 

== Screenshots ==

1. **Text Fields**
1. **Selecter and Checkboxes**
1. **Image and Upload**
1. **Form Verification**
1. **Import and Export**


Changelog
==========

#### 1.0.0.0 2/14/2013
 - Initial Release

