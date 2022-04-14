=== Admin Page Framework ===
Contributors:       Michael Uno, miunosoft, pcraig3
Donate link:        http://michaeluno.jp/en/donate
Tags:               admin pages, developers, options, settings, API, framework, library, meta box, custom post type, fields, widgets, forms, plugins, themes
Requires at least:  3.4
Tested up to:       5.9.3
Stable tag:         3.9.1
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

#### 3.9.1 - 2022/04/14
- Added the `mime_types` argument for the `image` and `media` field types.
- Fixed a bug that setting notices set with `setSettingNotice()` of classes extending the post type factory class were not displayed.
- Changed the behavior of the `color` field type by triggering a change event after setting a new value.
- Changed the behavior of the custom image uploader by triggering a change event after setting a new value.

#### 3.9.0 - 2022/03/02
- Refined tooltips.
- Refined the `path` custom field type due to the deprecation of the `jQueryFileTree` library and switching to `jstree`, which involves deprecation of some arguments and UI improvements.
- Added the `table` built-in field type.
- Added the `contact` built-in field type.
- Added the `selector` argument to the `select2` custom field type that enables to show/hide elements on selection.
- Added the ability for tooltips to include `a` tags.
- Added the `width` argument for the `tip` field argument.
- Added the ability to insert included custom field type labels in the file header comment of the compiled bootstrap file.
- Fixed a bug that sortable sections could not be sorted in some cases.
- Fixed an issue that field repeat buttons and labels of the `color` field type are hidden by the color picker pallet in recent WordPress versions.
- Fixed an issue that tooltips created with the `tip` field argument made the document width wider than the initial width.
- Fixed an issue that class names and text domains of sub-files of custom field types were not converted when building.
- Changed the default argument value of `save` of the `submit` field type to `false`.
- Changed the behavior of loading framework resources (stylesheets and JavaScript scripts) from internally to externally for most cases.
- Changed the tab label `Generator` to `Compiler`.
- Deprecated the `email` argument of the `submit` field type.

#### Old Change Log
See [here](https://raw.githubusercontent.com/michaeluno/admin-page-framework/master/changelog.md)