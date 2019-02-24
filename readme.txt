=== Admin Page Framework ===
Contributors:       Michael Uno, miunosoft, pcraig3
Donate link:        http://michaeluno.jp/en/donate
Tags:               admin pages, developers, options, settings, API, framework, library, meta box, custom post type, fields, widgets, forms, plugins, themes
Requires at least:  3.4
Tested up to:       5.1.0
Stable tag:         3.8.19
License:            GPLv2 or later
License URI:        http://www.gnu.org/licenses/gpl-2.0.html

Facilitates WordPress plugin and theme development.

== Description ==

<h4>Reduce the Time Spent for Plugin and Theme Development</h4>
One of the time-consuming part of developing WordPress plugins and themes is creating setting pages. As you more and more write plugins and themes, you will soon realize major part of code can be reused. Admin Page Framework aims to provide reusable code that eliminates the necessity of writing repeated code over and over again.

You will have more organized means of building option pages with the framework. Extend the library class and pass your arrays defining the form elements to the predefined class methods. The library handles all the complex coding behind the scene and creates the pages and the forms for you.

<h4>Create Essential Page and Form Components for Your Users</h4>
- **Top-level Page, Sub Pages, and In-page Tabs** - where your users will access to operate your plugin or theme.
- **Forms** - to let your users store their options.
- **Custom Post Types** - and the custom columns in the post listing table.
- **Custom Taxonomies and Fields** - store options associated with a taxonomy in the taxonomy definition page.
- **Meta Boxes and Fields** - store meta data associated with posts of set post types. Also meta boxes can be added to the pages created with the framework.
- **Widgets and Fields** - display modular outputs based on the user's settings in the front end.
- **Network Admin Pages and Forms** - create admin pages in the newtork admin area of WordPress multi-sites.

<h4>Construct Simple Yet Complex Setting Forms</h4>
- **Section Tabs** - form sections can be displayed in a tabbed box.
- **Collapsible Sections** - form sections can be collapsed and expanded.
- **Repeatable Sections and Fields** - dynamically add/remove form sections and fields.
- **Sortable Sections and Fields** - drag and drop form sections and fields to change the order.
- **Nested Sections and Fields** - nest sections and fields to construct complex forms.
- **Import and Export Options** - buttons that the user can import and export settings by uploading and downloading text files.
- **Reset Button** - let your users to initialize the saved options.
- **Validation and Error Messages** - with the pre-defined validation callbacks, the user's submitting form data can be verified. Furthermore, by setting the error array, you can display the error message to the user.
- **Contextual Help Pane** - help information can be added to the contextual help pane that appears at the top right of each screen.
- **Custom Field Types** - your own field type can be registered. This allows you to design own fields such as a combination of a checkbox with a text field.
- **Contact Form** - receive emails of user feedback and issue reports sent via the contact form embedded in an admin page.
- **Tooltips** - add a small pop-up box beside section and field title for the users to read about the option.

<h4>Produce a Series of Products with the Framework Extensibility</h4>
If you are planning to create a product possibly extended with an unlimited number of add-ons, take advantage of the framework's native extensibility. The created admin pages and forms will become highly extensible with the automatically created hooks. In other words, it empowers other developers to customize your plugin or theme. That will result on making your projects grow.

Also, use the framework as a library and your plugin or theme does not have to require an extra dependency to be installed. Therefore, your product will be perfectly portable.

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
- `section_title` - a text field placed in the section title to let the user name the section.
- `system` - displays the site system information.
- `inline_mixed` - consists of inline elements of fields with different field types.

<h4>Bundled Custom Field Types</h4>
With custom field types, you can create more detailed customized field outputs. The demo component includes the following example custom field types.

- `ace` - a rich code editor.
- `sample` - a sample custom field type with a JavaScript script.
- `github` - displays GitHub buttons.
- `path` - lets the user select file paths on the server.
- `toggle` - lets the user toggle a switch button.
- `no_ui_slider` - lets the user set values between ranges with a slider.
- `select2` - lets the user select items from a predefined list which cam be populated with AJAX.
- `post_type_taxonomy` - lets the user select taxonomy terms of selected post types.


If you want a field type that are not listed here, you can check the [field type pack](http://admin-page-framework.michaeluno.jp/add-ons/field-type-pack/) or request a new one in the [forum](https://wordpress.org/support/plugin/admin-page-framework).

<h4>Getting Started</h4>
To get started, go to **Dashboard** -> **Admin Page Framework** -> **About** -> **Getting Started**.

<h4>Demo</h4>
Activate the demo pages to see the possible features of the framework. To activate it, go to **Dashboard** -> **Admin Page Framework** -> **Add Ons** -> **Demo**.

<h4>Documentation</h4>
- [Online Documentation](http://admin-page-framework.michaeluno.jp/en/v3/package-AdminPageFramework.AdminPage.html)
- [Tutorials](http://admin-page-framework.michaeluno.jp/tutorials/)

**Notes:** this framework does not do anything by itself. If you are not a developer, you do not need this.

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
WordPress plugin/theme developers who publish own products and want to speed up creating setting forms, widgets, contact form etc. and don't want to require their users to install extra dependencies.

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
Yes. For built-in root menu items or to create your own ones, you need to use the `setRootMenuPage()` method. For root pages of custom post types, use `setRootMenuPageBySlug()`.

`
$this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );
`

<h5><strong>How do I retrieve the stored options?</strong></h5>
The framework stores them as an organized multidimensional array in the options table in a single row. So use the `get_option()` function and pass the instantiated class name as the key or the custom key if you specify one in the constructor.

For instance, if your instantiated class name is `APF` then the code would be

`
$my_options = get_option( 'APF' );
`

And the option data is formed as an array with a structure like the following.

`
$my_options = array(
    // field id => field value
    'field_a' => 'value for field a'
    'field_b' => 'value for field b'
    ...
    // section id => array(
    //      field id => value,
    // )
    'section_a' => array(
        'field_a' => 'value for field_a of section_a',
        'field_b' => 'value for field_b of section_b',
        ...
    ),
    'section_b' => array(
        'field_a' => 'value for field_a of section_b',
        'field_b' => 'value for field_b of section_b',
        ...
    ),
    ...
);
`

If you are new to PHP, you may feel uncomfortable dealing with multi-dimensional arrays because you would need to call `isset()` so many times. The framework has a utility method to help retrieve values of multi-dimensional arrays.

`
$_oUtil = new AdminPageFramework_WPUtility;
$value  = $_oUtil->getElement(
    $my_options,    // (required) subject array
    array( 'key_in_the_first_depth', 'key_in_the_second_depth' ),   // (required) dimensional path
    'My Default Value Here' // (optional) set your default value in case a value is not set
);
`

So for example, if you need to retrieve the value of `field_a` in `section_b`, you can do something like this.

`
$value = $_oUtil->getElement(
    $my_options,
    array( 'section_b', 'field_a' ),
    'some default value'
);
`

In the framework factory class, you can access the utility object as it is defined already.

`
$value = $this->oUtil->getElement( $subject, $keys, $default );
`

<h5><strong>Is it possible to use a custom options data for the form instead of the ones used by the framework?</strong></h5>
Yes, there are two main means to achieve that.

1. Use the `value` argument in the field definition array to suppress the displaying value in the field.
See an example. https://gist.github.com/michaeluno/fb4088b922b71710c7fb

2. Override the options array set to the entire form using the `options_{instantiated class name}` filter hook or pre-defined method.
See an example. https://gist.github.com/michaeluno/fcfac27825aa8a35b90f

When you go with the second method, make sure to pass an empty string, `''`, to the first parameter of the constructor so that it disables the ability to store submitted form data into the options table.

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

See an [example](https://gist.github.com/michaeluno/b580ae3a021bafbe13da2d352885b13a).

<h5><strong>I want my users to install the loader plugin but do not want to display any visuals of the loader plugin. Is there a way to disable it?</strong></h5>

Enable the silent mode of the loader plugin by setting the `APFL_SILENT_MODE` constant in your script.

`
define( 'APFL_SILENT_MODE', true );
`

<h5><strong>Can I create pages in the network admin area?</strong></h5>
Yes, See the demo.

<h5><strong>Some of my users claim they cannot save options. What would be a possible cause?</strong></h5>

- `max_input_vars` of PHP settings. If this value is small and there are lots of form input elements, the user may not be able to save the options.

To increase the value, edit `php.ini` and add the following line where `10000` is the increased number.

`
max_input_vars = 10000
`

- `max_allowed_packet` of MySQL settings. Try increasing this value in the `my.ini` or `my.cnf` file.

The `500M` in the following line is where the increased value should be set.

`
max_allowed_packet=500M
`

Please keep in mind that these are just a few of many possibilities. If you encounter a situation that prevented the user from saving options, please [report](https://github.com/michaeluno/admin-page-framework/issues).

<h5><strong>My class is getting too big by defining predefined callback methods. Is there a way to separate those?</strong></h5>
Yes. The predefine method names also serve as a WordPress filter/action hook name. So you can just add callbacks to those hooks from a separate file.

For example, if you want to move your method `content_my_page_slug()`, then you would do something like,

`
function getMyPageContent( $sContent ) {
    return $sContent . ' additional contents here.';
}
add_filter( 'content_my_page_slug', 'getMyPageContent' );
`

IF you want to move your method `load_my_page_slug()`, then you would do something like,

`
function loadMyPage( $oFactory ) {
    // do something when the page loads.
}
add_action( 'load_my_page_slug', 'loadMyPage' );
`

<h5><strong>Custom field types do not seem to show up. What did I do wrong?</strong></h5>

Most likely, you have not registered the field type. The check-box in `Generator` will include the field type files in the zip archive and their paths in the list for the auto-loader loaded by the framework bootstrap file.

This essentially eliminates the use of `include()` or `require()`, meaning you can call the custom field type files without using `include()`. However, the field type is not registered by itself yet.

In order to use a custom field type, you need to instantiate the field type class by passing the extended framework class name. For example, if your framework class name is `MyPlugin_AdminPageFramework` and the field type class name is `Select2CustomFieldType`, then you need to do

`
new Select2CustomFieldType( 'MyPlugin_AdminPageFramework' );
`

Do this in the `setUp()` method in your extended framework class.

`
public function setUp() {
    new Select2CustomFieldType( 'MyPlugin_AdminPageFramework' );
}
`

This enables the `select2` custom field type for the class `MyPlugin_AdminPageFramework`, not for the other classes. So essentially, do this for every class that uses the field type.

<h5><strong>I cannot find what I'd like to do in tutorials and documentation. Where else should I look for more information?</strong></h5>

- You may directly read the code of the demo plugin. The demo plugin code is located in the [example](https://github.com/michaeluno/admin-page-framework/tree/master/example) directory.
- Ask questions in the [support forum](https://wordpress.org/support/plugin/admin-page-framework).

<h4>Getting Involved</h4>
<h5><strong>I've written a useful class, functions, and even custom field types that will be useful for others! Do you want to include it?</strong></h5>
The [GitHub repository](https://github.com/michaeluno/admin-page-framework "Admin Page Framework") is available. Raise an [issue](https://github.com/michaeluno/admin-page-framework/issues) first and we'll see if changes can be made.

<h5><strong>How can I contribute to this project?</strong></h5>
There are various ways to do so. Please refer to the [contribution guideline](https://github.com/michaeluno/admin-page-framework/blob/master/contributing.md).

<h5><strong>How can I contribute to improving the documentation?</strong></h5>
You are welcome to submit documentation. Please follow the [Documentation Guideline](https://github.com/michaeluno/admin-page-framework/blob/master/documentation_guideline.md).

In addition, your tutorials and snippets for the framework can be listed in the manual. Let us know it [here](https://github.com/michaeluno/admin-page-framework/issues?direction=desc&labels=Documentation&page=1&sort=created&state=open).

<h4>More FAQ Items</h4>
Check out the [questions tagged as FAQ](https://github.com/michaeluno/admin-page-framework/issues?q=is%3Aissue+label%3AFAQ) on GitHub.

<h4>Tutorials</h4>
[Index](http://admin-page-framework.michaeluno.jp/tutorials/)

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
    'save'              => false,
    'value'             => __( 'Save', 'task-scheduler' ),
    'label_min_width'   => 0,
    'attributes'        => array(
        'field' => array(
            'style' => 'float:right; clear:none; display: inline;',
        ),
    ),
)
`

For meta box and widget form fields (as they have slightly different styling than generic admin pages),
`
array(
    'field_id'          => 'submit_in_meta_box',
    'type'              => 'submit',
    'save'              => false,
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

If it is a repeatable field, set values in numerically indexed sub-elements.

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

Alternately, you may use the `options_{instantiated class name}` filter to suppress the options so that setting the value argument is not necessary.
See examples, https://gist.github.com/michaeluno/c30713fcfe0d9d45d89f, https://gist.github.com/michaeluno/fcfac27825aa8a35b90f,

== Changelog ==

= 3.8.19 - 2019/02/24 =
- Added checks to prevent unnecessary calls on `setUp()` in `admin-ajax.php`.
- Tweaked styling of the welcome page in the admin area of the loader plugin for WordPress 5.0.
- Changed generated log file names with `AdminPageFramework_Debug::log()` to be shortened.
- Fixed a bug with the custom `select2` field type that requests did not go through with nested fields.
- Fixed a bug with the custom `select2` field type that showed the message "the results could not be loaded" while processing a Ajax requests.
- Fixed a bug with the `AdminPageFramework_Debug::log()` method that did not properly retrieve correct caller method names.
- Fixed an issue that some third-party tools reported false positive for PHP 7.2 incompatibility due to a method name prefixed with double underscores.

= 3.8.18 - 2018/07/17 =
- Fixed a bug that inline/nested field values were not saved properly in widget forms.

= 3.8.17 - 2018/07/09 =
- Fixed a bug that the `save` argument did not take effect for User Meta, Page Meta Box and Widget factories.
- Fixed a compatibility issue with some third-parties which attempt to instantiate the framework widget class without given any parameters.

= 3.8.16 - 2018/07/06 =
- Fixed a bug that the default sub-menu page of a custom top-level menu page could not be removed when the PHP class with a namespace is used.

= 3.8.15 - 2017/01/23 =
- Fixed a bug that caused an undefined method warning in the admin notice class.

= 3.8.14 - 2017/01/15 =
- Added the `load()` methods to factory classes of post/page meta boxes, user/taxonomy meta.
- (minor breaking change) Deprecated the parameter of the `load()` method of the widget factory class.
- Changed the behaviour of not loading form components in `admin-ajax.php` so that custom field types can access `admin-ajax.php` using Ajax.
- Fixed an issue that Ajax requests of the `select2` custom field type could not retrieve responses for the factory classes other than the admin page.

= 3.8.13 - 2016/12/22 =
- Added the `disabled` repeatable section and repeatable field arguments.
- Added the `interactive`, `can_exceed_min`, `can_exceed_max`, and `allow_empty` arguments to the `no_ui_slider` field type.
- Fixed incompatibility with WordPress 4.7 that caused a notification page meta box to appear when it should not due to the change of action hook handling.
- Fixed a bug that clicking on a collapsible section button of the `button` type did not collapse/expand the section.
- Fixed a but that a vertical scrollbar appeared in an admin page that has a form when the browser width is less than 900px.
- Tweaked the style of the `size` field type.
- Tweaked the style of collapsible sections.
- Tweaked tye style of section tabs for WordPress 4.7.

= 3.8.12 - 2016/11/28 =
- Fixed a bug that caused an undefined index warning when a widget is added, introduced in 3.8.11.

= 3.8.11 - 2016/11/25 =
- Fixed a bug that `if` field argument did not applied to the entire field output.
- Fixed a bug which could cause warnings "Creating default object from empty value in ...AdminPageFramework_Resource_post_meta_box.php".
- Fixed a bug calling an undefined method when setting a `help` argument in form section definitions.
- Changed the behaviour of adding form resource scripts to add them all in the footer.

= 3.8.10 - 2016/11/09 =
- Fixed a bug that the `hidden` field argument did not take effect for nested fields.
- Fixed a bug that parent fields for nested fields were passed to the `repeated_field` callback argument of the custom `registerAdminPageFrameworkCallbacks()` jQuery plugin method.

= 3.8.9 - 2016/11/05 =
- Fixed a bug occurred in PHP 5.3 that caused a warning `debug_backtrace() expects at most 1 parameter, 2 given`.
- Fixed an issue that setting an object instance in field definition arguments caused slow performance on loading the form.
- Tweaked the debug output format of field/section arguments.
- Tweaked the style of tool-tips.

= 3.8.8 - 2016/10/26 =
- Added the `post_type_taxonomy` custom field type that lets the user select taxonomy terms of selected post types.
- Added the `show_debug_info` argument to the page, in-page tab, section, and field definition arguments to decide whether to display debug information.
- Added the ability to display section arguments in tool-tips beside the section title.
- Added the `save_unchecked` argument for the `checkbox`, `posttype`, and `taxonomy`  field types that decides whether to save values of unchecked items.
- Fixed a bug of calling a member function getSyntaxHighlightedPHPCode() on an undefined object in the network admin page of the demo.
- Fixed a section ID conflict of `mixed` in demo examples.
- Changed the handling mechanism of repeated fields (minor internal breaking change).
- Tweaked the style of tool-tips.
- Tweaked the style of `taxonomy` fields.

= 3.8.7 - 2016/10/09 =
- Added the `select2` custom field type that lets the user select items with auto-complete list which can possibly populated with AJAX.
- Added the ability for the `path` and `toggle` custom field types to support repeatable sections.
- Fixed a bug that a section title was not displayed when there was a field with the `placement` argument of the `section_title` value.
- Fixed a PHP warning with the `no_ui_slider`, `array_fill() [function.array-fill]: Number of elements must be positive...`.
- Fixed a bug with the `no_ui_slider` custom field type that saving a value of `0` caused a slider not to be displayed.
- Changed the `getDataAttributeArray()` utility method to convert camel-cased keys to be dashed.
- Tweaked the style of collapsible section titles.

= 3.8.6 - 2016/10/02 =
- Added the `no_ui_slider` custom field type which lets the user set values in ranges.
- Added the ability for the `text` field type to accept nested `attributes` argument of a name of the corresponding `label` argument array element.
- Changed the `getDataAttributeArray()` utility method to accept and convert array elements to a JSON string.

= 3.8.5 - 2016/09/25 =
- Added the `toggle` custom field type which lets the user switch a toggle button.
- Added the ability to automatically insert field definitions in a tool-tip for each field.
- Fixed a bug that duplicated form style resource files were loaded.
- Fixed a bug that handle IDs of style resources of forms and pages could conflict which resulted in not loading some items of page resources of styles.
- Fixed a bug that collapsible section arguments were not processed properly, introduced in v3.8.4.
- Fixed an issue that it was not possible to select tool-tip text.
- Tweaked the style of tool-tips.

= 3.8.4 - 2016/09/21 =
- Added the `path` custom field type which lets the user pick a file located on the server.
- Fixed a bug that the `aOptions` property values were not updated when they are filtered with the `options_{class name}` filter hook.
- Fixed a bug that setting `0` to the `label_min_width` field definition argument did not take effect.
- Fixed a bug that an empty heading element was displayed even when the tab title of in-page-tab was set to empty.
- Fixed a bug with the admin page factory class that transient option keys which get automatically set when an integer is passed to the first parameter of the constructor were not unique and could be too long.
- Fixed the timing of the `field_definitions_{class_name}` filter to keep compatibility with v3.7.
- Fixed a bug with the `Generator` that a set class prefix was not set properly for custom field types in some cases.
- Fixed a bug in the `getDataAttributeArray()` utility method that some non-true values were all treated as `0`.

= 3.8.3 - 2016/09/08 =
- Fixed an issue that array elements of sub-menu items without a key added with the `pages_{class name}` filter were not processed properly.
- Fixed an issue that a class creating a root menu page must be instantiated prior to a class adding a sub-menu page with the `setRootMenuPageBySlug()` method by passing the class name of the root menu page.

= 3.8.2 - 2016/08/25 =
- Fixed a bug that automatically generated script paths were not accurate when multiple scripts shared the same library file.

= 3.8.1 - 2016/08/12 =
- Fixed a bug that repeatable tabbed sections were not displayed properly until a tab is clicked when it is repeated.

= 3.8.0 - 2016/07/26 =
- Added the `placement` field argument that allows the user to place field in the areas of section title and field title.
- Added the `inline_mixed` built-in field type which introduces the ability to create inline mixed fields with the `content` field definition argument.
- Added the ability to nest fields with the `content` field definition argument.
- Added a new factory class, `AdminPageFramework_TermMeta`, to build forms for taxonomy terms.
- Changed the `type` argument in the field definition to be able to be omitted.
- Changed the default value of the `label_min_width` field argument to `0` from `140` and its `min-width` CSS property value set to elements are handled with embedded CSS.
- Changed the behaviour of collapsible section bars that expanded/collapsed when a field in the bar was clicked.
- Fixed a bug that setting an argument array to the `script` and `style` argument of a menu page definition did not take effect.
- Fixed a bug with the rich text editor that set text was erased when the field is sortable and the sorted the field.
- Fixed a bug that the tool bar of repeatable rich text area fields did not get updated when the field or section was repeatable.
- Fixed a bug that delimiter elements were not displayed when a last repeatable field is duplicated.

= 3.7.15 - 2016/05/31 =
- Fixed a bug that caused a warning saying accessing an undefined object property in the network admin area.

= 3.7.14 - 2016/04/20 =
- Fixed a compatibility issue with WordPress 4.5 that taxonomy form fields were not displayed in the term editing page.

= 3.7.13 - 2016/03/04 =
- Fixed a PHP notice, `Array to string conversion in ...AdminPageFramework_FieldType_media.php` caused when setting an array to the `repeatable` field definition argument of the `image` and `media` field type.

= 3.7.12 - 2016/02/20 =
- Added the ability to set custom classes for sub-objects.
- Added an example for page meta boxes for in-page tabs in the demo.
- Added the `load()` method to the admin page factory class which gets called when one of the added admin pages starts loading.
- Fixed a bug that zip files generated with `Generator` could not be opened on Windows.
- Fixed a bug that the `size` field type could not save and retrieve values in a sub-field, introduced in v3.7.0.
- Tweaked the style of page meta boxes.

= 3.7.11 - 2016/01/15 =
- Fixed a bug that the default plugin action link were doubled when setting a custom label.
- Changed the error level of custom errors for using deprecated items.

= 3.7.10 - 2016/01/13 =
- Added the `load()` method to the post type factory class which gets called when the `edit.php` page of the post type starts loading.
- Optimized performance in the common admin area.
- Changed the timing of registering page meta boxes so that their classes can be instantiated in the `load_{...}` hook of the admin page factory class.
- Changed the framework inline CSS rules in the compiled library files to be minified.

= 3.7.9 - 2016/01/07 =
- Reduced the number of library files.
- Fixed a bug that site-wide field type definitions were overridden by built-in field types of per-class field type definitions which caused some field types which override the built-in field type slug did not load, introduced in v3.7.1.
- Fixed an issue with widgets that caused some overhead.
- Fixed an issue that widget resources were loaded in pages that do not have widget forms.
- Tweaked the style of collapsible form sections.
- Changed the color of setting notices for resetting options with the `submit` field type from red to green.
- Changed the demo to be disabled when the loader plugin admin pages are disabled.

= 3.7.8 - 2015/12/31 =
- Reduced the number of database queries used in framework forms.

= 3.7.7 - 2015/12/30 =
- Fixed an issue that the framework forms could not be displayed when some third-party plugins or themes have JavaScript errors.
- Fixed a bug in a field object calling an undefined method.

= 3.7.6.1 - 2015/12/23 =
- Fixed a bug that debug log was generated, introduced in v3.7.6.
- Fixed a bug that Generator could not generate some files, introduced in v3.7.6.

= 3.7.6 - 2015/12/23 =
- Added the `skip_confirmation` argument for the `submit` field type, which skips submit confirmation.
- Fixed some compatibility issues with PHP 7.
- Fixed an issue that fron-end pages of custom post types registered with the framework could not be accessible in some occasions.
- Fixed a bug that resetting options did not show a message since v3.5.3.
- Fixed a bug that page titles got doubled in the `title` tag when there is a hidden page.
- Changed the timing of resetting options and sending contact form emails of the `submit` field type to after the validation hooks so that the user can cancel their actions.

= 3.7.5 - 2015/12/18 =
- Reduced the number of database queries used in framework widget forms.

= 3.7.4 - 2015/12/16 =
- Added the `submenu_order_addnew` and `submenu_order_manage` arguments for the post type arguments.
- Added the `submenu_order` argument for the taxonomy arguments.
- Fixed a bug that setting notices could not be displayed in the network admin area, introduced in 3.7.0.
- Fixed a bug in the demo of network admin pages that called non existent class, introduced in 3.7.0.
- Fixed a bug in the network admin factory class that called an undefined method, , introduced in 3.7.0.
- Changed the default capability value of the user meta factory class to `read` to allow subscribers to edit options of their profiles.
- Changed the incremental offset automatically assigned to the `order` argument of sub-menu items.
- Changed the `order` argument of sub-menu items to be effective site-wide.

= 3.7.3 - 2015/12/13 =
- Added the `action_links_{post type slug}` filter to the post type factory class.
- Fixed an issue of a fatal error `Maximum function nesting level of 'x' reached` when the server enables the XDebug extension and sets a low value for the `xdebug.max_nesting_level` option.

= 3.7.2 - 2015/12/11 =
- Fixed a compatibility issue with WordPress 4.4 that widget fields with a section could no longer save and retrieve the values.
- Fixed a bug that the framework library files and user-generated library files were missing file doc-blocks.
- Fixed an issue that custom field type specific text domain could not be converted with the Generator tool.
- Changed the form sections and fields registration mechanism of the admin page class to accept items without the `page_slug` and `tab_slug` by letting them being added to the current page or tab which is registed by the classs.

= 3.7.1 - 2015/12/08 =
- Added the internal ability for the `select` and `radio` field types to accept nested `attributes` arguments for each `label` element.
- Fixed a bug that site-wide field type definitions were loaded multiple times per page load, introduced in 3.7.0.
- Tweaked the style of form sections and fields.
- Tweaked the style of help tool tips.

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
