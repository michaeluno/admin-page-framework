=== Admin Page Framework ===
Contributors: Michael Uno, miunosoft
Donate link: http://michaeluno.jp/en/donate
Tags: admin, administration, administration panel, admin panel, admin page, admin pages, admin page framework, option page, option pages, option, options, setting, settings, Settings API, API, framework, library, class, classes, development tool, developers, developer tool, meta box, custom post type, utility, utilities
Requires at least: 3.3
Tested up to: 3.6
Stable tag: 2.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides simpler means of building administration pages for plugin and theme developers. 

== Description ==
It provides plugin and theme developers with easier means of creating option pages. Extend the library class and pass your arrays that define the form elements to the predefined class methods. The library handles all the complex coding behind the scene and creates the pages and the forms for you. The package includes a demo plugin which helps you walk through necessary features.

**Notes:** this framework does not do anything by itself. If you are not a developer, you do not need this.

= Features =
* **Root Page, Sub Pages, and Tabs** - it allows you to instantly create a top level page and the sub pages of it, plus tabs inside the sub pages.
* **Extensible** - the created admin pages will become highly extensible with the automatically created hooks. In other words, it empowers other developers to customize your plugin or theme. That will result on making your projects grow.
* **Import and Export Options** - buttons that the user can import and export settings by uploading and downloading the text file.
* **Image Upload** - it lets the user easily upload images to the site or the user can choose from existent urls or already uploaded files.
* **Date Picker** - it lets the user easily select dates.
* **Color Picker** - it lets the user easily pick colors.
* **Settings API Implemented** - it uses the [WordPress Settings API](http://codex.wordpress.org/Settings_API) for creating the form so the standard option design will be employed.
* **Validation and Error Messages** - with the pre-defined validation callbacks, the user's submitting data can be verified as a part of using the Settings API. Furthermore, by setting the error array, you can display the error message to the user.
* **Custom Post Types** - the framework provides methods to create custom post types.
* **Meta Boxes** - the framework provides methods to create custom meta boxes with form elements that you define.
* **Contextual Help Tabs** - the contextual help pane can be easily added. 

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
* Image Upload (Custom Text Field)
* Color Picker (Custom Text Field)
* Date Picker (Custom Text Field)
* Option Export and Import (Custom File Upload and Submit Button)
* Post Types (Custom Checkboxes)
* Taxonomies (Custom Checkboxes)
* Size ( Custom Text and Select Fields )

= Necessary Files =
* **`admin-page-framework.php`** is in the *class* folder.

= Documentation =
Visit [Admin Page Framework Documentation](http://admin-page-framework.michaeluno.jp/en/v2/).

= Tutorials = 
* [Tutorials](http://en.michaeluno.jp/admin-page-framework/tutorials/)

== Screenshots ==
1. **Text Fields**
2. **Selector and Checkboxes**
3. **Image and Upload and Color Picker**
4. **Form Verification**
5. **Import and Export**
6. **Taxonomy and Post Type Checklists**

== Installation ==

= Getting Started =

**Step 1.** Include **`admin-page-framework.php`** that is located in the **`classes`** folder into your theme or plugin.

`if ( ! class_exists( 'AdminPageFramework' ) )
    include_once( dirname( __FILE__ ) . '/class/admin-page-framework.php' );`
	
**Step 2.** Extend the Library Class.

`class APF_GettingStarted extends AdminPageFramework {
}`

**Step 3.** Define the **setUp()** Method.

`function setUp() {
	$this->setRootMenuPage( 'Settings' );               // specifies to which parent menu to belong.
	$this->addSubMenuPage(
		'My First Page',    // page and menu title
		'myfirstpage' 	// page slug
	); 
}`

**Step 4.** Define methods for hooks.

`function do_myfirstpage() {  // do_ + pageslug	
	?>
	<h3>Say Something</h3>
	<p>This is my first admin page!</p>
	<?php
}`

**Step 5.** Instantiate the Class.

`new APF_GettingStarted;`

= Example Code = 

`<?php
/* Plugin Name: Admin Page Framework - Getting Started */ 

if ( ! class_exists( 'AdminPageFramework' ) )
    include_once( dirname( __FILE__ ) . '/class/admin-page-framework.php' );
    
class APF extends AdminPageFramework {

    function setUp() {
		
    	$this->setRootMenuPage( 'Settings' );	
		$this->addSubMenuPage(
			'My First Page',	// page and menu title
			'myfirstpage'		// page slug
		);
	
    }

    function do_myfirstpage() {  // do_ + pageslug
        ?>
        <h3>Say Something</h3>
        <p>This is my first admin page!</p>
        <?php   
    }
    
}
new APF;
// That's it!`

== Frequently asked questions ==
= What is this for? =
This is	a PHP class library that enables to create option pages and form fields in the administration panel. Also it helps manage to save, export, and import options.

= I've written a useful class and functions. Do you want to include it? = 
The [GitHub repository](https://github.com/michaeluno/admin-page-framework "Admin Page Framework") is avaiable. Raise an [issue](https://github.com/michaeluno/admin-page-framework/issues) first and we'll see if changes can be made. 

= How can I contribute to improving the documentation? =
You are welcome to submit documentation. Please follow the [Documentation Guidline](https://github.com/michaeluno/admin-page-framework/blob/master/documentation_guideline.md). 

In addition, your tutorials and snippets for the framework can be listed in the manual. Let us know it [here](https://github.com/michaeluno/admin-page-framework/issues?direction=desc&labels=Documentation&page=1&sort=created&state=open).

= What if other themes or plugins include a lesser version of this library than mine? =
Let's say your plugin uses Admin Page Framework v2.1.0 and another plugin uses v2.0.0. If another plugin's library gets loaded earlier than yours, your library may not work property. 

To work around it, rename all the class names used by the library in your library file. All the class name is prefixed with `AdminPageFramework` so change it to something like, for instance, `MyPlugin_AdminPageFramework`, then you are safe. 

Most code editor supports "Replace All" functionality so just use that. By the time WordPress's minimum required PHP version becomes 3.3 or higher, we can use namespaces then this problem will be solved.

== Roadmap ==
* Add the dynamic fluctuability to the form input elements.

== Done ==
* <s>Add the ability to set text in the contextual help section.</s>
* <s>Add the date picker form field type.</s> Implemented in 2.0.0.
* <s>Add the color picker form field type.</s> Implemented in 2.0.0.
* <s>Add the ability to remove registered form elements.</s> Implemented in 2.0.0.
* <s>Add a custom input filed for category select checkboxes</s>. Implemented in 1.0.4.
* <s>Add the ability to specify a redirect page after the form data is successfully updated.</s> Implemented in 1.0.3.2.

== Changelog ==

= 2.1.0 =
* Changed: ( ***Breaking Change*** ) the *AdminPageFramework_PostType* class properties and *AdminPageFramework_MetaBox* to be encapsulated into a class object each.
* Added: the *strHelp* field key that adds a contextual help tab on the upper part of the admin page.
* Fixed: the required WordPress version to 3.3 as some of the functionalities were relying on the screen object that has been implemented since WordPress 3.3.

= 2.0.2 - 09/07/2013 =
* Fixed: a bug in the demo plugin that custom taxonomies were not added.
* Added: the *size* field type.

= 2.0.1 - 09/04/2013 =
* Fixed: a bug that admin setting notices were displayed twice in the options-general.php page.

= 2.0.0 - 08/28/2013 =
* Released 2.0.0.

= 2.0.0.b4 - 08/28/2013 =
* Fixed: a bug that custom post type preview page did not show the stored values in the demo plugin.
* Refactored: the code that loads the color picker script.
* Refactored: the code that loads the image selector script.
* Refactored: the code that loads framework's style.

= 2.0.0.b3 - 08/28/2013 =
* Added: more documentation in the source code.
* Removed: the *document* folder.
* Moved: the *documentation_guideline.md* file to the top level folder.
* Removed: the documentation pages and added an external link to the documentation web site.
* Removed: the *arrField* parameter of the constructor of the *AdminPageFramework_MetaBox* class.
* Removed: the *setFieldArray()* method of the *AdminPageFramework_MetaBox* class.
* Fixed: a bug that meta box color piker, image selector, data picker scripts did not load in the page after the Publish button was pressed.
* Changed: the *validation_ extended class name* filter for meta boxes to accept the second parameter to receive the stored data.

= 2.0.0.b2 - 08/26/2013 =
* Changed: *addLinkToPluginDescription()* and *addLinkToPluginTitle()* to accept variadic parameters. 
* Added: an example of using *addLinkToPluginDescription()* and *addLinkToPluginTitle()* in the demo plugin.
* Changed: the demo plugins file name.
* Fixed: an issue that date picker script caused an irregular element to be inserted around the page footer.
* Changed: the documentation compatible with the DocBlock syntax. 

= 2.0.0.b1 - 08/24/13 =
* Changed: the *setSettingsNotice()* method name to *setSettingNotice()* to be consistent with other names with *Settings*.
* Added: the *date* input field that adds a date picker.
* Added: the ability to specify the multiple attribute to the select field with the *vMultiple* key.
* Added: the *color* input field that adds a color picker.

= 1.1.0 - 2013/07/13 =
* Added: the *addSubMenuItems()* and *addSubMenuItem()* methods that enables to add not only sub menu pages but also external links.
* Added: the ability to list the terms of specified taxonomy with checkbox by taxonomy slug.
* Changed: ( *Breaking Change* ) the *category* field type to *taxonomy* field type.
* Fixed: a bug that adding sub pages to an existing custom post type page caused the links of in-page tabs to have the wrong urls.
* Changed: the *image* field type to be a custom text field.
* Added: the *import_format_{page slug}_{tab slug}*, *import_format_{page slug}*, *import_format_{instantiated class name}* filters to allow to modify the import format type.
* Added: the *import_option_key_{page slug}_{tab slug}*, *import_option_key_{page slug}*, *import_option_key_{instantiated class name}* filters to allow to modify the import option key.
* Added: the *export_format_{page slug}_{tab slug}*, *export_format_{page slug}*, *export_fomat_{instantiated class name}* filters to allow to modify the export format type.
* Added: the *export_name_{page slug}_{tab slug}*, *export_name_{page slug}*, *export_name_{instantiated class name}* filters to allow to modify the export file name.
* Added: the ability to set the *accept* attribute for the *file* input field.
* Added: ( *Breaking Change* ) the second parameter to the validation callback method to pass the old stored option data.
* Changed: ( *Breaking Change* ) the validation behaviour to maintain the stored option values to return the second parameter value in the validation callback method from returning an empty array.
* Changed: ( *Breaking Change* ) the validation behaviour to delete the stored option values to return an empty array in the validation callback method from returning a null value.
* Added: the *validation_{instantiated class name}* filter that allows to modify the submitted form data throughout the whole script.
* Added: the ability to set the text domain for the text messages that the framework uses.
* Added: the ability to set the minimum width for label tags for *textarea*, *text*, and *number* input fields.
* Added: the ability to set the label tag for *textarea*, *text*, and *number* input fields.
* Added: the *{instantiated class name}_field_{field id}* filter to allow to modify settings field output.
* Added: the *{instantiated class name}_{page slug}_tabs* filter to allow to modify adding in-page tabs.
* Added: the *{instantiated_class name}_pages* filter to allow to modify adding pages.
* Added: the *{instantiated class name}_setting_fields* and *{instantiated class name}_setting_sections* filters to allow to modify registering sections and fields.
* Changed: ( *Breaking Change* ) the default option key that is stored in the option database table to be the instantiated class name from the page slug.
* Changed: ( *Breaking Change* ) the section and field filters to have the prefix of the instantiated class name of the Admin Page Framework so that it prevents conflicts with other plugins that uses the framework.
* Changed: the anchor link *name* attribute to *id*.
* Added: the ability to order the in-page tabs with the *numOrder* key.
* Added: the *addInPageTab()* methods to set in-page tabs.
* Changed: ( *Breaking Change* ) the array structure of the parameter of the *addInPageTabs()* methods.
* Added: the ability to automatically assign the default screen icon if not set, which is of the **generic** id.
* Added: the ability to set the WordPress built-in screen icon to the custom added sub-menu pages.
* Added: a class for handling custom-post types.
* Added: a class for handling meta-boxes.
* Changed: ( *Breaking Change* ) to apply the camel-back notation to all the array argument keys.
* Changed: ( *Breaking Change* ) all the method names to be uncapitalised. 
* Changed: ( *Breaking Change* ) the sub-string of class names, Admin_Page_Framework, to AdminPageFramework.

= 1.0.4.2 - 07/01/2013 =
* Tweaked: the demo plugin to load the admin-page object only in the administration pages with the is_admin() function.
* Fixed: a bug that setting and retrieving a transient for the field error array caused extra database queries.
* Fixed: a bug that setting multiple checkboxes caused undefined index warning. 
* Fixed: a bug in the demo plugin that single upload field did not appear and caused undefined index warning after updating the options.

= 1.0.4.1 - 04/14/2013 =
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
* Supported: multiple export buttons per page.
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
* Fixed a warning in debug mode, undefined index, selectors.
* Added a brief instruction in the demo plugin code and fixed some inaccurate descriptions.

= 1.0.0.1 - 02/15/2013 =
* Fixed a bug that the options were not properly saved when the forms were created in multiple pages.

= 1.0.0.0 - 02/14/2013 = 
* Initial Release