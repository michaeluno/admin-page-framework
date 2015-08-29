=== Admin Page Framework ===
Contributors:       Michael Uno, miunosoft, pcraig3
Donate link:        http://michaeluno.jp/en/donate
Tags:               admin, administration, options, settings, API, framework, library, meta box, custom post type, custom post types, utility, fields, custom field, custom fields, tool, tools, widget, widgets, form, forms, plugin, plugins, theme
Requires at least:  3.3
Tested up to:       4.2.4
Stable tag:         3.5.11
License:            GPLv2 or later
License URI:        http://www.gnu.org/licenses/gpl-2.0.html

Facilitates WordPress plugin and theme development.

== Description ==
One of the time-consuming part of developing WordPress plugins and themes is creating setting pages. As you more and more write plugins and themes, you'll soon realize major part of code can be reused. Admin Page Framework aims to provide reusable code that eliminates the necessity of writing repeated code over and over again.

Admin Page Framework provides plugin and theme developers with easier means of building option pages. Extend the library class and pass your arrays that define the form elements to the predefined class methods. The library handles all the complex coding behind the scene and creates the pages and the forms for you.

**Notes:** this framework does not do anything by itself. If you are not a developer, you do not need this.

= What you can do =

easily create:

- **Top-level Page, Sub Pages, and In-page Tabs** - where your users will access to operate your plugin or theme.
- **Forms** - to let your users store their options.
- **Custom Post Types** - and the custom columns in the post listing table.
- **Custom Taxonomies and Fields** - to store options associated with a taxonomy in the taxonomy definition page.
- **Meta Boxes and Fields** - which help to store meta data associated with posts of set post types. Also meta boxes can be added to the pages created with the framework.
- **Widgets and Fields** - to display modular outputs based on the user's settings in the front end.
- **Network Admin Pages and Forms** - for WordPress multi-sites.
- **Contact Form** - receive emails of user feedback and issue reports sent via the contact form embedded in an admin page.

= What are useful about =
- **Extensible** - the created admin pages will become highly extensible with the automatically created hooks. In other words, it empowers other developers to customize your plugin or theme. That will result on making your projects grow.
- **Section Tabs** - form sections can be displayed in a tabbed box.
- **Collapsible Sections** - form sections can be collapsed and expanded.
- **Repeatable Sections and Fields** - dynamically add/remove form sections and fields.
- **Sortable Sections and Fields** - drag and drop form sections and fields to change the order.
- **Import and Export Options** - buttons that the user can import and export settings by uploading and downloading text files.
- **Reset Button** - lets the user to initialize the saved options.
- **Validation and Error Messages** - with the pre-defined validation callbacks, the user's submitting form data can be verified. Furthermore, by setting the error array, you can display the error message to the user.
- **Contextual Help Pane** - help contents can be added to the contextual help pane that appears at the top right of each screen.
- **Custom Field Types** - your own field type can be registered. This allows you to design own fields such as a combination of a checkbox with a text field. 
- **Portable** - use the framework as a library and include the minified version and your plugin or theme does not require an extra plugin to be installed. Therefore, your product will be perfectly portable.

<h4>Built-in Field Types</h4>
- `text` - a normal field to enter text input.
- `password` - a masked text input field.
- `textarea` - a text input field with multiple lines. It supports TinyMCE rich text editor.
- `radio` - a set of radio buttons that lets the user pick an option.
- `checkbox` - a check box that lets the user enable/disable an item.
- `select` - a drop-down list that lest the user pick one or more item(s) from a list.
- `hidden` - a hidden field that will be useful to insert invisible values.
- `file` - a file uploader that lets the user upload files.
- `image` - a custom text field with the image uploader script that lets the user set an image URL.
- `media` - a custom text field with the media uploader script that lets the user set a file URL.
- `color` - a custom text field with the color picker script.
- `submit` - a submit button that lets the user send the form.
- `export` - a custom submit field that lets the user export the stored data.
- `import` - a custom combination field of the file and the submit fields that let the user import data.
- `posttype` - a set of check-lists of taxonomies enabled on the site in a tabbed box.
- `taxonomy` - check-lists of taxonomies enabled on the site in a tabbed box.
- `size` - a combination field of the text and the select fields that let the user set sizes with a selectable unit.
- `section_title` - a text field type that will be placed in the section title so that it lets the user set the section title.
- `system` - displays the site system information.

<h4>Bundled Custom Field Types</h4>
With custom field types, you can create more detailed customized field outputs. The demo component includes the following example custom field types.

- `ace` - a rich code editor.
- `sample` - a sample custom field type with a JavaScript script.
- `github` - displays GitHub buttons.

If you want a field type that are not listed here, you can check the [field type pack](http://admin-page-framework.michaeluno.jp/add-ons/field-type-pack/) or request a new one in the forum.

<h4>Getting Started</h4>
Go to **Dashboard** -> **Admin Page Framework** -> **About** -> **Getting Started**.

<h4>Demo</h4>
Activate the demo pages to see the possible features of the framework. To activate it, go to **Dashboard** -> **Admin Page Framework** -> **Add Ons** -> **Demo**.

<h4>Documentation</h4>
- [Online Documentation](http://admin-page-framework.michaeluno.jp/en/v3/class-AdminPageFramework.html)
- [Tutorials](http://admin-page-framework.michaeluno.jp/tutorials/)

== Screenshots ==
1. **Text Fields**
2. **Selector and Checkboxes**
3. **Image, Media, and File Upload**
4. **Form Input Verification**
5. **Import and Export**
6. **Taxonomy and Post Type Checklists**
7. **Color Pickers and Buttons**
8. **Custom Post Type and Meta Box**
9. **Contextual Help Pane**
10. **Taxonomy Field**
11. **Meta Boxes in Pages Added by Framework**
12. **Repeatable Sections, Section Tabs and Section Title Field**
13. **Widget Form**
14. **User Meta Fields**
 
== Installation ==

1. Upload [admin-page-framework.zip](http://downloads.wordpress.org/plugin/admin-page-framework.latest-stable.zip) via **Dashboard** -> **Plugins** -> **Add New** -> **Upload Plugin**.
2. Activate it.

For usage instructions to get started, go to **Dashboard** -> **Admin Page Framework** -> **About** -> **Getting Started** and create your first plugin.

== Frequently asked questions ==

<h4>About the Project</h4>
<h5><strong>What is this for?</strong></h5>
This is a PHP class-based WordPress library that helps to create option pages and form fields in the administration area. In addition, it helps to manage to save, export, and import options.

<h5><strong>Who needs it?</strong></h5>
WordPress plugin/theme developers who want to speed up creating setting forms, widgets, contact form etc. and don't want to require their users to install extra dependencies. 

<h5><strong>Do my plugin/theme users have to install Admin Page Framework?</strong></h5>
No. Include the generated framework files in your distribution package. You can generate your own framework files via `Dashboard` -> `Admin Page Framework` -> `Tools` -> `Generator`.

<h5><strong>Where can I get the framework files to include?</strong></h5>
Go to `Dashboard` -> `Admin Page Framework` -> `Tools` -> `Generator` and download the files.

<h5><strong>Does my commercial product incorporating your framework library have to be released under GPL2v+?</strong></h5>
No. The loader plugin is released under GPLv2 or later but the library itself is released under MIT. Make sure to include only the library file.

<h5><strong>Does the framework work with WordPress Multi-site?</strong></h5>
Yes, it works with [WordPress MU](https://codex.wordpress.org/WordPress_MU).

<h4>Technical Questions</h4>
<h5><strong>Can I set a custom post type as a root page?</strong></h5>
Yes. For built-in root menu items or create your own ones, you need to use the `setRootMenuPage()` method. For root pages of custom post types, use `setRootMenuPageBySlug()`.

`
$this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );
`

<h5><strong>How do I retrieve the stored options?</strong></h5>
The framework stores them as an organized multidimensional array in the options table in a single row. So use the `get_option()` function and pass the instantiated class name as the key or the custom key if you specify one in the constructor. 

For instance, if your instantiated class name is `APF` then the code would be 

`
$my_options = get_option( 'APF' );
` 

Alternatively, use the [AdminPageFramework::getOption()](http://admin-page-framework.michaeluno.jp/en/v3/class-AdminPageFramework.html#_getOption) static method.

<h5><strong>Is it possible to use a custom options data for the form instead of the ones used by the framework?</strong></h5>
Yes, there are two main means to achieve that. 

1. Use the `value` argument in the field definition array to suppress the displaying value in the field.
See an example. https://gist.github.com/michaeluno/fb4088b922b71710c7fb

2. Override the options array set to the entire form using the `options_{instantiated class name}`.
See an example. https://gist.github.com/michaeluno/fcfac27825aa8a35b90f

When you go with the second method, make sure to pass an empty string, `''` to the first parameter of the constructor so that it disables the ability to store submitted form data into the options table.

`
new MyAdminPage( '' );
`

<h5><strong>How can I add sub-menu pages to the top-level page created by the framework in a separate script?</strong></h5>

Say, in your main plugin, your class `MyAdminPageClassA` created a top-level page. In your extension plugin, you want to add sub-menu pages from another instance `MyAdminPageClassB`. 

In the `setUp()` method of `MyAdminPageClasB`, pass the instantiated class name of the main plugin that created the root menu, `MyAdminPageClassA`, to the `setRootMenuPageBySlug()` method.

`
$this->setRootMenuPageBySlug( 'MyAdminPageClassA' );
`

<h5><strong>Can I create pages in the network admin area?</strong></h5>
Yes, See the demo.

<h5><strong>Some of my users claim they cannot save options. What would be a possible cause?</strong></h5>

- `max_input_vars` of PHP settings. If this value is small and the there are lots of form input elements, the user may not be able to save the options.

To increase the value, edit `php.ini` and add the following line where `10000` is the increased number.

`
max_input_vars = 10000
`

- `max_allowed_packet` of MySQL settings. Try increasing this value in the `my.ini` or `my.cnf` file.

The `500M` in the following line is where the increased value should be set.

`
max_allowed_packet=500M
`

Please keep in mind that these are just a few of many possibilities. If you encounter a situation that prevented the user from saving options, please report.

<h5><strong>I cannot find what I'd like to do in tutorials and documentation. Where else should I look for more information?</strong></h5>

- You may directly read the code of the demo plugin. The demo plugin code is located in the `example` directory.
- Ask questions in the [support forum](https://wordpress.org/support/plugin/admin-page-framework).

<h4>Supporting the Project</h4>
<h5><strong>I've written a useful class, functions, and even custom field types that will be useful for others! Do you want to include it?</strong></h5>
The [GitHub repository](https://github.com/michaeluno/admin-page-framework "Admin Page Framework") is available. Raise an [issue](https://github.com/michaeluno/admin-page-framework/issues) first and we'll see if changes can be made. 

<h5><strong>How can I contribute to improving the documentation?</strong></h5>
You are welcome to submit documentation. Please follow the [Documentation Guideline](https://github.com/michaeluno/admin-page-framework/blob/master/documentation_guideline.md). 

In addition, your tutorials and snippets for the framework can be listed in the manual. Let us know it [here](https://github.com/michaeluno/admin-page-framework/issues?direction=desc&labels=Documentation&page=1&sort=created&state=open).

<h4>More FAQ Items</h4>
Check out the [questions tagged as FAQ](https://github.com/michaeluno/admin-page-framework/issues?q=is%3Aissue+label%3AFAQ) on GitHub.

<h4>Tutorials</h4>
[Index](http://en.michaeluno.jp/admin-page-framework/tutorials-v3/)

== Other Notes ==

<h4>Use Unique Page Slug</h4>
The framework internally uses the `add_submenu_page()` function to register sub menu pages. When the same page slug is registered for multiple root pages, only the last registered callback gets triggered. The other ones will be ignored.

This means if you choose a very simple page slug such as <code>about</code> for your plugin/theme's information page and then if there is another plugin using the same page slug, your users will get either of your page or the other.

To avoid this, make sure to use a unique page slug. One way to do that is to add a prefix like <code>apf_about</code>. 

<h4>Use the files generated with the component generator</h4>

There is one thing you need to be careful when you include the framework: the framework version conflicts. Imagine you publish a plugin using the framework v3.4.6 and your plugin user installs a plugin using the framework v3.0.0 which is below your framework version. If the other plugin loads earlier than yours, your plugin may not work properly and vice versa.

There is a way to avoid such a conflict: change the PHP class names of the framework you include. All the class names have the prefix <code>AdminPageFramework</code> so just change it to something like <code>MyPlugin_AdminPageFramework</code>.

Go to **Dashboard** -> **Admin Page Framework** -> **Tools** -> **Generator**. Set the prefix in the option field and download the files.

If you do not modify the framework class names, you are supposed to extend the `AdminPageFramework` factory class.

`
class MyAdminPage extends AdminPageFramework {
    ...
}
`

When you modify the framework class names, make sure you extend the class with the modified name.

`
class MyAdminPage extends MyPlugin_AdminPageFramework {
    ...
}
`

For more detailed instruction, go to **Dashboard** -> **Admin Page Framework** -> **About** -> **Getting Started**.

By the time WordPress's minimum required PHP version becomes 5.3 or higher, we can use name spaces then this problem will be solved.

<h4>Change Framework's System Messages</h4>
The default messages defined by the framework can be changed. For example, when you import a setting with the framework, the setting notice "The options have been updated." will be displayed. 

If you want to change it to something else, modify the `oMsg` object. It has the `aMessages` public property array holding all the messages that the framework uses.

<h4>Get comfortable with the 'attributes' array argument</h4>
In each field definition array, you can set the `attributes` arguments which defines the HTML attributes of the field so that you can modify the output of the field by passing attribute values.

The argument accepts the values as an array. Each element represents the attribute's name and value. The array key corresponds to the name of the attribute and the value to the attribute value.

For example,
`
array(    
    'field_id'      => 'interval',
    'title'         => __( 'Interval', 'task-scheduler' ),
    'type'          => 'number',
    'attributes'    => array(
        'min'   => 0,
        'step'  => 1,
        'max'   => 24,
    ),
),
`

In addition, you can change the attributes of the following container elements by setting their key and passing a nested attribute array.

- `fieldrow` - the `td` tag element containing the field output.
- `fieldset` - the `fieldset` tag element containing the field output.
- `fields` - the `div` tag element containing the sub-fields and the main field.
- `field` - the `div` tag element containing each field.

This submit button will float right.
`
array(    
    'field_id'          => 'submit',
    'type'              => 'submit',
    'label'             => __( 'Save', 'task-scheduler' ),
    'label_min_width'   => 0,
    'attributes'        => array(
        'field' => array(
            'style' => 'float:right; clear:none; display: inline;',
        ),
    ),                    
)    
`

For meta boxe and widget form fields (as they have a sligtly different styling than generic admin pages),
`
array(
    'field_id'          => 'submit_in_meta_box',
    'type'              => 'submit',
    'show_title_column' => false,
    'label_min_width'   => 0,
    'attributes'        => array(
        'field' => array(
            'style' => 'float:right; width:auto;',
        ),                   
    ),
),
`

<h4>Change Preview Image Size of the 'image' Field Type</h4>
To specify a custom size to the preview element of the `image` field type, set an attribute array like the below, where 300px is the max width.

`
array(
    'field_id'      => 'my_image_field_id',
    'title'         => __( 'Image', 'admin-page-framework-demo' ),
    'type'          => 'image',
    'attributes'    => array(
        'preview' => array(
            'style' => 'max-width: 200px;',
        ),
    ),
),
`

<h4>Display items of 'radio' field type one per line</h4>
To display radio button items one per line, set the `label_min_width` to `100%`.

`
array(
    'field_id'          => 'my_radio_field_id',
    'title'             => __( 'Radio Button', 'admin-page-framework-demo' ),
    'type'              => 'radio',
    'label_min_width'   => '100%',
    'label'             => array(
        'a' => __( 'This is a.', 'admin-page-framework-demo' ),
        'b' => __( 'This is b.', 'admin-page-framework-demo' ),
        'c' => __( 'This is a.', 'admin-page-framework-demo' )c
    ),
),
`

<h4>Set default field value</h4>
To set the initial value of a field, use the `default` argument in the field definition array.

`
array(
    'field_id'  => 'my_text_field_id',
    'title'     => __( 'My Text Input Field', 'admin-page-framework-demo' ),
    'type'      => 'text',
    'default'   => 'This text will be displayed for the first time that the field is displayed and will be overridden when a user set an own value.',
),
`

<h4>Always display a particular value in a field</h4>
The `value` argument in the definition array can suppress the saved value. This is useful when you want to set a value from a different data source or create a wizard form that stores the data in a custom location.

`
array(
    'field_id'  => 'my_text_field_id',
    'title'     => __( 'My Text Input Field', 'admin-page-framework-demo' ),
    'type'      => 'text',
    'value'     => 'This will be always set.',
),
`

If it is a repeatable field, set the value in the sub-fields.

`
array(
    'field_id'      => 'my_text_field_id',
    'title'         => __( 'My Text Input Field', 'admin-page-framework-demo' ),
    'type'          => 'text',
    'repeatable'    => true,
    'value'         => 'the first value',
    array(
        'value' => 'the second value',
    ),
    array(
        'value' => 'the third value',
    ),    
),
`

Alternately, if it is in a framework's generic pages (not post meta box fields) you may use the `options_{instantiated class name}` filter to suppress the options so that setting the value argument is not necessary.
See examples, https://gist.github.com/michaeluno/c30713fcfe0d9d45d89f, https://gist.github.com/michaeluno/fcfac27825aa8a35b90f, 

<h3>Roadmap</h3>
Check out [the issues](https://github.com/michaeluno/admin-page-framework/issues?labels=enhancement&page=1&state=open) on GitHub labeled *enhancement*.

== Changelog ==
 
= 3.6.0 =
- Added the ability to sort sections with the `sortable` argument of a section definition array.
- Added the `save` argument for the field definition.
- Fixed a bug that an in-page tab that the `parent_tab_slug` is set and `show_in_page_tab` is `false` was displayed, introduced in 3.5.10.
- Fixed the `setPageHeadingTabTag()` method did not property set the tag.
- Changed the directory structure of included files to shorten the overall file paths.
- Changed the mechanism of the repeating and sorting fields.
 
= 3.5.12 -2015/08/09 =
- Fixed a bug that the `Select All` and `Select None` buttons were doubled when a `checkbox` and `posttype` field types were in the same form and not displayed with the `taxonomy` field type when there are no `checkbox` or `posttype` fields in the same page.
- Tweaked the class selectors of admin notification elements to have dismiss buttons available in WordPress 4.2 or above.
 
= 3.5.11 - 2015/07/14 =
- Added a warning to be displayed for forms in generic admin pages when the user form inputs exceeds the PHP `max_input_vars` value.
- Added the column layout screen options for page meta boxes.
- Fixed a bug that screen options were not saved in generic pages added by the framework.

= 3.5.10 - 2015/07/05 =
- Added the `show_submenu_add_new` post type argument which enables to remove "Add New" sub-menu item from the sidebar menu.
- Added the `attributes` and `disabled` arguments to page and in-page tab definition array which gets applied to navigation tab element.
- Changed the behavior of the `color` field type to have the default value getting reflected in repeated field.
- Fixed an issue that default values could not be set for user meta fields.
- Fixed an issue that magic quotes were not sanitized in taxonomy field inputs.

= 3.5.9 - 2015/06/25 =
- Added the ability for the `size` field type to create sub-fields by passing an array of labels.
- Added the `reset_...` action hooks.
- Added the ability to disable setting notices by passing an empty string to the ` setSettingNotice()` method.
- Added the ability for the admin page factory class to save form options only for a set time of period by passing an integer for the option key to the constructor.
- Added the ability for transient utility methods to accept long transient key names.
- Fixed an issue that post meta-box form fields were not able to set default values when there were existing meta data.
- Fixed a bug in the `getOption()` utility method that the fourth parameter did not take effect when `null` is given to the second parameter.
- Changed the timing of rendering the widget title to after the `do_{...}` and `content_{...}` hooks.
- Changed the zip file name of generated framework files to have a version suffix.

= 3.5.8 - 2015/05/29 =
- Added the ability for the `getValue()` method to set a default value with the second parameter when the first parameter is an array.
- Added the ability for the `text` and `textarea` field types to create sub-input elements by passing an array to the `label` argument.
- Added the `width` argument for the `taxonomy` field type.
- Fixed a bug that the `name` attribute value was not set in post meta box fields in `post-new.php`, introduced in 3.5.7.
- Fixed a bug with the `taxonomy` field type that could not list terms when the `arguments` argument misses the `class` argument.

= 3.5.7.1 - 2015/05/11 =
- Fixed a bug with referencing pressed submit button name attributes, introduced in 3.5.7.

= 3.5.7 - 2015/05/05 =
- Fixed a compatibility issue with WordPress 4.2 or above that the `taxonomy` field type become not able to list taxonomy terms.
- Tweaked styling of page meta-box form elements.

= 3.5.6 - 2015/03/15 =
- Fixed a bug that form section values of page meta box could not be displayed.

= 3.5.5 - 2015/03/08 =
- Added the `footer_right_{...}` and `footer_left_{...}` filter hooks.
- Fixed an issue that the loader plugin could not get activated when a plugin that includes the framework of a lesser version that that not have a certain class constant.
- Changed not to include the development version in the plugin distribution package.
- Changed the `setFooterInfoLeft()` and `setFooterInfoRight()` methods to be deprecated which had not been functional since v3.1.3.

= 3.5.4 - 2015/03/02 =
- Added the framework component generator in the loader plugin which can be accessed via `Dashboard` -> `Admin Page Framework` -> `Tools` -> `Generator`.
- Added the `export_header_{...}` filters that lets the user set custom HTTP header outputs for the `export` field type.
- Fixed a bug in the `system` field type that PHP warnings occurred when the permission to read error logs is not sufficient.
- Changed the minified version to be deprecated.
- Changed the version name of the development version to have `.dev` notation in the version name.

= 3.5.3 - 2015/02/21 =
- Added the ability to reset individual field values with the `reset` argument of the `submit` field type.
- Added a user meta factory class demo example.
- Added the `validate()` and `content()` methods in the admin page factory class.
- Added the fourth parameter to the `validate()` method of page meta box factory class to receive submit information.
- Fixed a bug that invalid field values were stored when submitting the form multiple times using validation callback methods.
- Fixed an issue in the loader plugin that after resetting the loader plugin options via the `Debug` tab, a warning 'You do not have sufficient permissions to access this page.' appeared.
- Fixed an issue in the user meta factory class that a PHP notice, Trying to get property of non-object..., which appeared when creating a new user.
- Fixed an issue that the `image` field type did not extend `AdminPageFramework_FieldType` but `AdminPageFramework_FieldType_Base`.

= 3.5.2 - 2015/02/02 =
- Fixed a bug in the widget factory class that form sections could not be set.
- Changed the `class` argument of the section definition array to accept a string.

= 3.5.1.1 - 2015/01/24 = 
- Fixed a bug that caused non-object type PHP error in the post type factory class introduced in v3.5.1.

= 3.5.1 - 2015/01/23 =
- Fixed a bug in the `enqueueScripts()` method of the admin page factory class.
- Fixed a bug that message objects were not properly instantiated. 
- Fixed PHP strict standard warnings.

= 3.5.0 - 2015/01/22 =
- Added the forth parameter of submit information to the validation filter callbacks so that callback methods can know which framework field submit button is pressed etc.
- Added the a method to the field type class that is triggered when a field of the field type is registered.
- Added the `field_types_admin_page_framework` filter that allows you to register field types sitewide.
- Added the `url` argument for in-page tab definition arrays that allows the user to add link navigation tab.
- Added a utility class for WordPres Readme parser and the markdown syntax.
- Added a utility class for plugin bootstraps.
- Added user meta factory class for adding fields in the user profile page.
- Fixed the network admin factory class could not add plugin action links via the `addLinkToPluginTitle()` method.
- Fixed an issue that empty strings could be inserted with the `addLinkToPluginTitle()` and `addLinkToPluginDescription()` method.
- Fixed a bug that `style_{...}` and `script_{...}` filters were triggered twice in generic admin pages.
- Fixed a bug that `style_{page slug}_{tab slug}`, `style_{page_slug}`, `script_{page slug}_{tab slug}`, and `script_{page slug}_{tab slug}` were not available.
- Changed the demo plugin to be a loader plugin that loads Admin Page Framework.
- Tweaked the styling of admin page container elements.

= 3.4.6 - 2015/01/06 =
- Added a page that lets the user download the minified version of the framework in the demo examples.
- Added a utility class that checks requirements of servers such as PHP and MySQL versions etc.
- Added the `print_type` argument to the `system` field type.
- Added more information to the `system` field type.
- Added (revived) the minified version and it is now human readable.
- Fixed an issue that form fields without setting a section were not registered in pages except first added ones.
- Changed the timing of importing and exporting of the `import` and `export` field types to be done after performing validation callbacks.

= 3.4.5.1 - 2015/01/01 =
- Removed the minified version as the WordPress plugin team demanded to do so.

= 3.4.5 - 2014/12/27 =
- Added the `setting_update_url_{instantiated class name}` filter hook and the pre-defined callback method.
- CHanged the `getValue()` method of the admin page factory class to respect last input arrays.
- Fixed a bug that caused a PHP warning that prevented a contact form from being sent on servers with some error reporting settings.
- Fixed an issue on some servers that the script got aborted while sending an email of the contact form.
- Fixed a url query key after submitting a contact form.
- Tweaked the styling of the form confirmation message container element.

= 3.4.4 - 2014/12/17 =
- Fixed a bug that in in-page tabs saving form values of page meta box fields caused data loss of other in-page tabs, introduced in v3.4.3.

= 3.4.3 - 2014/12/15 =
- Added CSS rules for debug output container elements.
- Changed the format of ID of section container elements.
- Fixed a bug that repeated fields could not be removed in page meta boxes started in v3.4.1.
- Fixed a bug that in page meta boxes, the `select` field type with the `is_multiple` argument enabled could not store the submitted data properly.
- Fixed a bug that page meta boxes could not be added by in-page tab slug.

= 3.4.2 - 2014/12/08 =
- Added the ability to automatically update the preview element of an image field when an image URL is manually typed without using the upload window.
- Added the ability to accept URLs for the `attachments` argument for the `email` argument of the `submit` field type.
- Changed the timing of the `setUp()` method of the post type factory class to let the post type arguments set in the method.
- Fixed an issue of the custom media uploader with IE8.
- Fixed a bug that the `Insert from URL` pane of the custom uploader modal window did not function even when the `allow_external_source` is set to `true`.

= 3.4.1 - 2014/12/02 =
- Added the `options_update_status_{...}` and `validation_saved_options_without_dynamic_elements_` filters for the admin page factory class.
- Added the `field_definition_{...}` filters for the page meta box class.
- Added the `validate()` and `content()` methods for the meta box and page meta box factory classes.
- Changed the timing of `field_definition_{...}` filters of the admin page class.
- Changed not to lose the input data when a form validation error occurs for the meta box, page meta box, and page classes.
- Fixed an issue that github buttons of the `github` field type did not load in some sites.
- Fixed a bug that repeatable sections could not be repeated when there is no collapsible section in the page.
- Fixed a bug that the old options of the second parameter passed in a validation callback method of the page meta box class did not hold dynamic elements.
- Fixed a bug that the action hooks and their predefined callbacks `submit_{instantiated class name}_{page slug}` and `submit_after_{instantiated class name}_{page slug}` did not work.

= 3.4.0 - 2014/11/23 =
- Added the ability of collapsing and expanding section containers with the `collapsible` section definition argument.
- Added the `select_type` argument for the `revealer` custom field type that enables to have checkboxes and radio buttons for the selector.
- Tweaked the styling of the `section_title` field type.
- Fixed a bug that the layout of form fields broke in Internet Explorer.

= 3.3.3 - 2014/11/08 =
- Added the framework version indication at the bottom of form sections in meta-boxes and widgets when `WP_Debug` is true.
- Changed not to display colon(:) after the field title if the title is empty in meta-boxes, taxonomy fields, and widgets.
- Fixed a bug that a gray blank image was inserted in the featured image's image uploader when the framework media uploader is loaded, introduced in v3.3.1.
- Fixed a bug that caused the uploader button of the `media` field type not appear, introduced in v3.3.2.

= 3.3.2 - 2014/11/07 =
- Added the `label_no_term_found`, `label_list_title`, `query`, and `queries` arguments for the `taxonomy` field type.
- Added the ability to compress CSS rules and JavaScript scripts defined in PHP variables for the minified version.
- Tweaked the styling of the `taxonomy` field type to reflect the hierarchical depths.
- Fixed a bug that resources of taxonomy fields added by the framework were loaded in other taxonomy pages that caused JavaScript errors.
- Fixed a bug that meta box field values defined via the `field_definition_{...}` filter hooks were not saved.
- Fixed a bug that setting sections for page meta box fields caused an error after submitting the form.
- Fixed a bug that class selectors set in the top level of the `class` argument for fields got inserted in all type of field containers including `fieldrow`, `fieldset`, `fields`, `field`.
- Fixed the `for` attribute value of label tags for fields to the focus input.

= 3.3.1 - 2014/11/02 =
- Added the `class` argument for fields.
- Added the `class`, `attributes`, `hidden` arguments for sections.
- Added the `submit_after_{...}` action hooks that are triggered when the submitted form data are saved.
- Added the value length to be indicated in the log file created with the log method of the debug class.
- Fixed an issue that resources of widgets registered by the framework were loaded in all admin pages.
- Fixed a jQuery method conflict of the `sortable()` plugin.
- Tweaked the styling of sortable fields in meta boxes.
- Tweaked the styling of form fields in meta boxes.
- Changed the `attributes` field definition argument to be able to override the system set attributes.
- Changed the built-in field types to extend the same base class to custom field types.

= 3.3.0 - 2014/10/22 =
- Added the `Select All` and `Select None` buttons for check boxes.
- Added the [ace](https://github.com/soderlind/AceCustomFieldType) custom field type.
- Added the ability to have different menu title from the page title with the `page_title` and `menu_title` argument pass to the `addSubMenuItems()` method.
- Added the ability for the `description` field/section definition argument to accept an array to process elements as multiple paragraphs.
- Added the ability to set custom arguments to enqueuing resource(style/script) tags.
- Added an example of querying posts by custom meta key and value in the demo plugin.
- Added the ability to send emails with the `submit` field type.
- Added the `reset` custom field type.
- Added the `system` field type (changed from a custom field type and became built-in).
- Fixed an issue that JavaScript scripts added by widget fields could not be properly loaded in `customize.php`.
- Fixed an issue of the `revealer` custom field type that the saved selected item could not be displayed after saving the form.
- Fixed unescaped tags and attributes in tabs.
- Fixed a bug that the `reset` argument of the `submit` field type caused a loss of stored options when the form fields are not added via the load_{...} hooks and have multiple pages are added.
- Fixed an issue that when setting form elements with the `load_{page slug}_{tab}` hook, the fields could not be displayed if the user clicked on the sidebar menu and the tab is the default tab.
- Fixed an issue that the TinyMCE rich editor could not be enabled in widget forms.
- Fixed an issue that Quick Tags of rich editors could not be repeated.
- Fixed an issue that values set to textarea tags were not escaped.
- Optimized the process of handling field errors in post meta boxes. 
- Tweaked the timing of jQuery event binding of the `date`, 'date_time', and 'time' custom field types.
- Tweaked the styling of remove buttons for `image`, `media`, `font` field types for WordPress 3.7.x or below.
- Tweaked the styling of sortable fields.
- Tweaked the styling of widget forms.
- Changed to prevent posts from being published if there are field errors of meta boxes. 
- Changed the layout of meta-box field table by making the field title column to be displayed as inline-block.
- Changed the metabox factory class to accept an empty ID to be passed to let the factory class automatically generates an ID from the class name.
- Changed the timing of finalizing in-page tabs.
- Changed the positions of the + and - repeatable buttons.

[Old Change Log Items](https://raw.githubusercontent.com/michaeluno/admin-page-framework/master/changelog.md)