=== Admin Page Framework ===
Contributors:       Michael Uno, miunosoft, pcraig3
Donate link:        http://michaeluno.jp/en/donate
Tags:               admin, administration, options, settings, API, framework, library, meta box, custom post type, custom post types, utility, fields, custom field, custom fields, tool, tools, widget, widgets, form, forms, plugin, plugins, theme
Requires at least:  3.4
Tested up to:       4.3.1
Stable tag:         3.6.6
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
- **Nested Sections** - nest sections to construct complex forms.
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
- [Online Documentation](http://admin-page-framework.michaeluno.jp/en/v3/package-AdminPageFramework.AdminPage.html)
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

<h5><strong>How can I add sub-menu pages to the top-level page created by the framework from a separate script?</strong></h5>

Say, in your main plugin, your class `MyAdminPageClassA` created a top-level page. In your extension plugin, you want to add sub-menu pages from another instance `MyAdminPageClassB`. 

In the `setUp()` method of `MyAdminPageClasB`, pass the instantiated class name of the main plugin that created the root menu, `MyAdminPageClassA`, to the `setRootMenuPageBySlug()` method.

`
$this->setRootMenuPageBySlug( 'MyAdminPageClassA' );
`

Another option is to use the `set_up_{class name}` action hook. The callback method receives the admin page class object and you can access the framework methods to add sub-menu pages.

`
class ThirdPartyScript {

    public function __construct() { 
        add_action( 'set_up_' . 'MyAdminPageClassA', array( $this, 'replyToAddSubMenuPages' ) );
    }
    
    public function replyToAddSubMenuPages( $oAdminPage ) {
            
        $oAdminPage->addSubMenuPage(
            array(
                'page_slug' => 'my_admin_page_b',
                'title'     => __( 'Example', 'your-text-domain' ),
                'order'     => 20,
            )        
        );
            
    }
        
}
new ThirdPartyScript;
`


<h5><strong>I want my users to install the loader plugin but do not want to display any visuals of the loader plugin. Is there a way to disable it?</strong></h5>

Enable the silent mode of the loader plugin by setting the `APFL_SILENT_MODE` constant in your script.

`
define( 'APFL_SILENT_MODE', true );
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

<h4>Roadmap</h4>
Check out the [milestones](https://github.com/michaeluno/admin-page-framework/milestones) and [issues](https://github.com/michaeluno/admin-page-framework/issues?labels=enhancement&page=1&state=open) on GitHub labeled *enhancement*.

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
    'value'             => __( 'Save', 'task-scheduler' ),
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

== Changelog ==

= 3.7.1 =
- Tweaked the style of help tooltips.

= 3.7.0 - 2015/12/04 =
- Added the `setMessage()` and `getMessage()` methods to retrieve and modify the framework system messages.
- Added the 'type' argument for the `collapsible` argument for section definitions which supports `button` to make sections collapsible with a button.
- Added the ability to display tool tips that appear when the user moves the cursor over the `?` icon beside the section and field title.
- Added the `tip` argument for section definitions.
- Added a utility class to create pointer tool box in the admin area.
- Added the ability of nesting sections.
- Tweaked the way that forms appear.
- Tweaked the way that admin notices appear.
- Fixed a bug that the `collapsed` of `collapsible` section definition argument did not take effect for widget forms when the widget was initially dropped.
- Changed the factory class name of the page meta box from `AdminPageFramework_MetaBox_Page` to `AdminPageFramework_PageMetaBox`.

= 3.6.6 - 2015/11/26 =
- Changed back the change introduced in v3.6.4 not to convert backslashes to underscores in class names in the framework hooks but only apply to auto-callback method names.

= 3.6.5 - 2015/11/21 =
- Fixed a bug that layouts of page meta boxes were not displayed properly when no `side` meta box existed and one of `normal` or `advanced` was added.
- Fixed the style of section tabs in WordPress 4.4.

= 3.6.4 - 20105/11/19 =
- Added the `APFL_SILENT_MODE` constant to the loader plugin that toggle the visuals of the loader admin pages.
- Tweaked the style of `textarea` fields.
- Fixed an issue that a column data were not updated right away when the user uses Quick Edit in a post listing table of a post type.
- Changed the class names in the framework hook names to get backslashes converted to underscores to avoid invalid characters in callback method names.

= 3.6.3 - 2015/11/07 = 
- Added the `script` and `style` arguments for the page and in-page tab definitions.
- Tweaked the style of section tab titles in meta boxes.
- Fixed an issue in WordPress 3.7.x or below that the screen icon appeared even when the screen title and the in-page tabs are disabled.
- Changed the required WordPress version to 3.4.

= 3.6.2 - 2015/10/31 =
- Added a notification box in the admin pages of the loader plugin.
- Tweaked the style of heading tags in meta boxes.
- Tweaked the style of buttons of collapsible sections.
- Fixed a bug that the form values were not saved correctly with a sortable and repeatable section containing repeatable fields.
- Fixed a bug in the `taxonomy` fields that conditions set with the `if` and `capability` arguments were not applied.
 
= 3.6.1 - 2015/10/26 =
- Added the ability to activate a form section tab by URL.
- Added the `content` argument for section and field definition arrays to define custom outputs.
- Added a hook to filter parsing contents to the `AdminPageFramework_WPReadmeParser` utility class.
- Fixed a bug with form section tabs that the active content elements were not visible when a container element is hidden first on the page load.
- Fixed a bug caused a fatal error in the `AdminPageFramework_AdminNotice` class, introduced in 3.5.12.
 
= 3.6.0 - 2015/10/22 =
- Added the ability for the generator to include custom field types.
- Added the ability to sort sections with the `sortable` argument of a section definition array.
- Added the `save` argument for the section and field definition.
- Added the `if` argument to the in-page tab definition.
- Fixed an issue of a conflict that forms of post type meta boxes were not displayed when the global `$post` object is modified by third party scripts.
- Fixed an issue that the `file` fields could not trigger the section validation callback when there are no other field types in the section.
- Fixed a bug that an in-page tab that the `parent_tab_slug` is set and `show_in_page_tab` is `false` was displayed, introduced in 3.5.10.
- Fixed the `setPageHeadingTabTag()` method did not property set the tag.
- Changed the `capability` values of admin pages, in-page tabs, sections and fields to inherit the value from the outer container element.
- Changed the directory structure of included files to shorten the overall file paths.
- Changed the mechanism of the repeating and sorting fields.
 
= 3.5.12 - 2015/08/09 =
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

[Old Change Log](https://raw.githubusercontent.com/michaeluno/admin-page-framework/master/changelog.md)