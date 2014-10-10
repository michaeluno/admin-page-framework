=== Admin Page Framework ===
Contributors:       Michael Uno, miunosoft, pcraig3
Donate link:        http://michaeluno.jp/en/donate
Tags:               admin, administration, admin panel, admin page framework, option, options, setting, settings, Settings API, API, framework, library, class, classes, developers, developer tool, meta box, custom post type, custom post types, utility, utilities, field, fields, custom field, custom fields, tool, tools, widget, widgets, factory, form, forms
Requires at least:  3.3
Tested up to:       4.0
Stable tag:         3.1.7
License:            GPLv2 or later
License URI:        http://www.gnu.org/licenses/gpl-2.0.html

Provides simpler means of building administration pages for plugin and theme developers. 

== Description ==
It provides plugin and theme developers with easier means of creating option pages. Extend the library class and pass your arrays that define the form elements to the predefined class methods. The library handles all the complex coding behind the scene and creates the pages and the forms for you. The package includes a demo plugin which helps you walk through necessary features.

**Notes:** this framework does not do anything by itself. If you are not a developer, you do not need this.

= What you can do =

with it easily create:

- **Root Page, Sub Pages, and Tabs** - where your users will access to operate your plugin or theme.
- **Forms** - to let your users store their options.
- **Custom Post Types** - and the custom columns in the post listing table.
- **Custom Taxonomies and Fields** - to store options associated with a taxonomy in the taxonomy definition page.
- **Meta Boxes and Fields** - which help to store meta data associated with posts of set post types. Also meta boxes can be added to the pages created with the framework.
- **Widgets and Fields** - to display modular outputs based on the user's settings in the front end.
- **Network Admin Pages and Forms** - for WordPress multi-sites.
- **Email Form** - to let the user report issues or feedback via emails.

= What are useful about =
- **Extensible** - the created admin pages will become highly extensible with the automatically created hooks. In other words, it empowers other developers to customize your plugin or theme. That will result on making your projects grow.
- **Section Tabs** - form sections can be displayed in a tabbed box.
- **Repeatable Fields** - dynamically add/remove form sections and fields.
- **Sortable Fields** - drag and drop fields to change the order.
- **Import and Export Options** - buttons that the user can import and export settings by uploading and downloading text files.
- **Reset Button** - lets the user to initialize the saved options.
- **Validation and Error Messages** - with the pre-defined validation callbacks, the user's submitting form data can be verified. Furthermore, by setting the error array, you can display the error message to the user.
- **Contextual Help Pane** - help contents can be added to the contextual help pane that appears at the top right of each screen.
- **Custom Field Types** - your own field type can be registered. This allows you to design own fields such as a combination of a checkbox with a text field. 
= **Portable** - use the framework as a library and include the minified version and your plugin or theme does not require an extra plugin install. Therefore, your product will be perfectly portable.

= Built-in Field Types =
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

= Bundled Custom Field Types = 
You can include your own custom field types when they are necessary. The reason that they are not built-in is to keep the library size as small as possible. The followings are example custom field types.

- `geometry` - a location selector with the Google map.
- `date`, `time`, `date_time`, `date_range`, `time_range`, `date_time_range` - date and time fields with the date picker.
- `dial` - a dial input field.
- `font` - a font uploader and its preview.
- `revealer` - a selector field that displays a hidden HTML element.
- `grid` - a drag and drop grid composer.
- `autocomplete` - a custom text field that shows a predefined pop-up autocomplete list.
- `link` - it lets pick a post and set the url.
- `github` - displays GitHub buttons.
- `image_checkbox`, `image_radio` - displays images instead of text labels to be selected.
- `reset` - a custom submit button that initialize the text form inputs.

= Necessary Files =
- **`admin-page-framework.min.php`** is in the *library* folder. Alternatively you may use **`admin-page-framework.php`** located in the *development* folder. In that case, all the class files in the sub-folders need to be copied.

= Documentation =
The HTML documentation is included in the distribution package and can be accessed via the sidebar menu that the demo plugin creates.

- [Online Documentation](http://admin-page-framework.michaeluno.jp/en/v3/class-AdminPageFramework.html)

= Tutorials =
[Index](http://en.michaeluno.jp/admin-page-framework/tutorials-v3/)

1. [Create an Admin Page](http://en.michaeluno.jp/admin-page-framework/tutorials-v3/01-create-an-admin-page/)
2. [Create a Form](http://en.michaeluno.jp/admin-page-framework/tutorials-v3/02-create-a-form/)
3. [Create a Page Group](http://en.michaeluno.jp/admin-page-framework/tutorials-v3/03-create-a-page-group/)
4. [Create In-page Tabs](http://en.michaeluno.jp/admin-page-framework/tutorials-v3/04-create-inpage-tabs/)
5. [Organize a Form with Sections](http://en.michaeluno.jp/admin-page-framework/tutorials-v3/05-organize-a-form-with-sections/)
6. [Use Section Tabs and Repeatable Sections](http://en.michaeluno.jp/admin-page-framework/tutorials-v3/06-use-section-tabs-and-repeatable-sections/)
7. [Validate Submitted Form Data of a Single Field](http://en.michaeluno.jp/admin-page-framework/tutorials-v3/07-validate-submitted-form-data-of-a-single-field/)
8. [Validate Submitted Form Data of Multiple Fields](http://en.michaeluno.jp/admin-page-framework/tutorials-v3/08-validate-submitted-form-data-of-multiple-fields/)

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

== Installation ==

= Getting Started =

<h5><strong>Step 1</strong> - Include <em><strong>admin-page-framework.min.php</strong></em></h5>
You need to include the library file in your PHP script. The file is located in the `library` folder of the uncompressed plugin file.

`
if ( ! class_exists( 'AdminPageFramework' ) ) {
    include_once( dirname( __FILE__ ) . '/library/admin-page-framework.min.php' );
}
`
    
<h5><strong>Step 2</strong> - Extend the Library Class</h5>

`
class APF_GettingStarted extends AdminPageFramework {
}
`

<h5><strong>Step 3</strong> - Define the <em>setUp()</em> Method</h5>

`
public function setUp() {
    $this->setRootMenuPage( 'Settings' );    // specifies to which parent menu to belong.
    $this->addSubMenuItem(
        array(
            'title'     => 'My First Page',
            'page_slug' => 'myfirstpage',
        )
    ); 
}
`

<h5><strong>Step 4</strong> - Define the Methods for Hooks</h5>

`
public function do_myfirstpage() {  // do_{page slug}    
    ?>
    <h3>Say Something</h3>
    <p>This is my first admin page!</p>
    <?php
}
`

<h5><strong>Step 5</strong> - Instantiate the Class</h5>

`
new APF;
`

<h5><strong>Example Code</strong> - Getting Started</h5>

`
<?php
/* Plugin Name: Admin Page Framework - Getting Started */ 

if ( ! class_exists( 'AdminPageFramework' ) ) {
    include_once( dirname( __FILE__ ) . '/library/admin-page-framework.min.php' );
}
    
class APF extends AdminPageFramework {

    public function setUp() {
        
        $this->setRootMenuPage( 'Settings' );    // where to belong
        $this->addSubMenuItem(
            array(
                'title'     => 'My First Page',
                'page_slug' => 'myfirstpage'
            )
        );
            
    }

    public function do_myfirstpage() {  // do_{page slug}
        ?>
        <h3>Say Something</h3>
        <p>This is my first admin page!</p>
        <?php   
    }
    
}
new APF;
`

= Create a Form =

`<?php
/* Plugin Name: Admin Page Framework - My First Form */ 

if ( ! class_exists( 'AdminPageFramework' ) ) {
    include_once( dirname( __FILE__ ) . '/library/admin-page-framework.min.php' );
}
    
class APF_MyFirstFrom extends AdminPageFramework {

    public function setUp() {
        
        $this->setRootMenuPage( 'My Settings' );    // create a root page 
        $this->addSubMenuItem(
            array(
                'title'     => 'My First Form',
                'page_slug' => 'my_first_form'
            )
        );
                            
    }
    
    /**
     * The pre-defined callback method that is triggered when the page loads.
     */
    public function load_my_first_form( $oAdminPage ) {    // load_{page slug}
    
        $this->addSettingFields(
            array(    
                'field_id'      => 'text',
                'section_id'    => 'my_first_text_section',
                'title'         => 'Text',
                'type'          => 'text',
                'default'       => 123456,
            ),                     
            array(                 
                'field_id'      => 'submit',
                'type'          => 'submit',
            )
        );    
    
    }
    
    
}
new APF_MyFirstFrom;
`

== Frequently asked questions ==

<h4>About the Project</h4>
<h5><strong>What is this for?</strong></h5>
This is a PHP class library that helps to create option pages and form fields in the administration panel. In addition, it helps to manage to save, export, and import options.

<h5><strong>I've written a useful class, functions, and even custom field types that will be useful for others! Do you want to include it?</strong></h5>
The [GitHub repository](https://github.com/michaeluno/admin-page-framework "Admin Page Framework") is available. Raise an [issue](https://github.com/michaeluno/admin-page-framework/issues) first and we'll see if changes can be made. 

<h5><strong>How can I contribute to improving the documentation?</strong></h5>
You are welcome to submit documentation. Please follow the [Documentation Guideline](https://github.com/michaeluno/admin-page-framework/blob/master/documentation_guideline.md). 

In addition, your tutorials and snippets for the framework can be listed in the manual. Let us know it [here](https://github.com/michaeluno/admin-page-framework/issues?direction=desc&labels=Documentation&page=1&sort=created&state=open).

<h5><strong>Does my commercial product incorporating your framework library have to be released under GPL2v+?</strong></h5>
No. The demo plugin is released under GPLv2 or later but the library itself is released under MIT. Make sure to include only the library file.

<h5><strong>Does the framework work with WordPress Multi-site?</strong></h5>
Yes, it works with [WordPress MU](https://codex.wordpress.org/WordPress_MU).

<h4>Technical Questions</h4>
<h5><strong>Can I set a custom post type as a root page?</strong></h5>
Yes. For built-in root menu items or create your own ones, you need to use the `setRootMenuPage()` method. For root pages of custom post types, use `setRootMenuPageBySlug()`.

`$this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );    `

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

<h4>More FAQ Items</h4>
Check out the [questions tagged as FAQ](https://github.com/michaeluno/admin-page-framework/issues?q=is%3Aissue+label%3AFAQ) on GitHub.

== Other Notes ==

<h4>Use Unique Page Slug</h4>
The framework internally uses the `add_submenu_page()` function to register sub menu pages. When the same page slug is registered for multiple root pages, only the last registered callback gets triggered. The other ones will be ignored.

This means if you choose a very simple page slug such as <code>about</code> for your plugin/theme's information page and then if there is another plugin using the same page slug, your users will get either of your page or the other.

To avoid this, make sure to use a unique page slug. One way to do that is to add a prefix like <code>apf_about</code>. 

<h4>Change PHP Class Names</h4>
When you include the framework, change the class names that the framework uses. This is because if there is a plugin that uses a lesser version of the framework and it is loaded earlier than yours, your script may not work properly.

All the class names have the prefix <code>AdminPageFramework</code> so just change it to something like <code>MyPlugin_AdminPageFramework</code>. 

Most text editors supports the *Replace All* command so just use that. By the time WordPress's minimum required PHP version becomes 5.3 or higher, we can use namespaces then this problem will be solved.

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

This submit button will float on the right.
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

<h4>Change Preview Image Size of the 'image' Field Type</h4>
To specify a custom size to the preview element of the `image` field type, set an attribute array like the below, where 300px is the max width.

`array(
    'field_id'      => 'my_image_field_id',
    'title'         => __( 'Image', 'admin-page-framework-demo' ),
    'type'          => 'image',
    'attributes'    => array(
        'style' => 'max-width:300px;',
    ),
),`

<h4>Display items of 'radio' field type one per line</h4>
To display radio button items one per line, set the `label_min_width` to `100%`.

`array(
    'field_id'          => 'my_radio_field_id',
    'title'             => __( 'Radio Button', 'admin-page-framework-demo' ),
    'type'              => 'radio',
    'label_min_width'   => '100%',
    'label'             => array(
        'a' => __( 'This is a.', 'admin-page-framework-demo' ),
        'b' => __( 'This is b.', 'admin-page-framework-demo' ),
        'c' => __( 'This is a.', 'admin-page-framework-demo' )c
    ),
),`

<h4>Set default field value</h4>
To set the initial value of a field, use the `default` argument in the field definition array.

`array(
    'field_id'  => 'my_text_field_id',
    'title'     => __( 'My Text Input Field', 'admin-page-framework-demo' ),
    'type'      => 'text',
    'default'   => 'This text will be displayed for the first time that the field is displayed and will be overridden when a user set an own value.',
),`

<h4>Always display a particular value in a field</h4>
The `value` argument in the definition array can suppress the saved value. This is useful when you want to set a value from a different data source or create a wizard form that stores the data in a custom location.

`array(
    'field_id'  => 'my_text_field_id',
    'title'     => __( 'My Text Input Field', 'admin-page-framework-demo' ),
    'type'      => 'text',
    'value'     => 'This will be always set.',
),`

If it is a repeatable field, set the value in the sub-fields.

`array(
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
),`

Alternately, if it is in a framework's generic pages (not post meta box fields) you may use the `options_{instantiated class name}` filter to suppress the options so that setting the value argument is not necessary.
See examples, https://gist.github.com/michaeluno/c30713fcfe0d9d45d89f, https://gist.github.com/michaeluno/fcfac27825aa8a35b90f, 

<h3>Roadmap</h3>
Check out [the issues](https://github.com/michaeluno/admin-page-framework/issues?labels=enhancement&page=1&state=open) on GitHub labeled *enhancement*.

== Changelog ==

= 3.3.0 =
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
- Tweaked the timinig of jQuery event binding of the `date`, 'date_time', and 'time' custom field types.
- Tweaked the styling of remove buttons for `image`, `media`, `font` field types for WordPress 3.7.x or below.
- Tweaked the styling of sortable fields.
- Tweaked the styling of widget forms.
- Changed the metabox factory class to accept an empty ID to be passed to let the factory class automatically generates an ID from the class name.
- Changed the timing of finalizing in-page tabs.
- Changed the positions of the + and - repeatable buttons.

= 3.2.1 - 2014/09/29 =
- Added an example of using the `content_top_{...}` and the `style_common_admin_page_framework` filters.
- Added the `style_common_admin_page_framework` hook.
- Added support for a file path to be passed for image submit buttons.
- Added support for custom queries for the `posttype` field type.
- Added the `radio_checkbox` custom field type.
- Added the `image_checkbox` custom field type.
- Tweaked the styling of field error messages.
- Fixed an issue that sortable fields could not be rendered properly when being dragged in browser screen widths of less than 782px in Chrome in WordPress above v3.8.
- Fixed the `content_top_{...}` hooks and the methods were not available.

= 3.2.0 - 2014/09/25 =
- Added an example of using an image for a submit button.
- Added the option to set custom button labels via the `data-label` attribute for the `image`, `media`, and `font` field types.
- Added the remove button for the `image`, `media`, and `font` field types.
- Added the default and Japanese translation files.
- Added the `show_post_count` argument for the `taxonomy` field type and made it enabled by default.
- Added the widget factory class and the examples of creating widgets with the framework in the demo plugin.
- Fixed an issue that registering multiple taxonomies after the `init` hook failed registering second or later items.
- Fixed a bug that a last item did not set when selecting multiple items in the fields of the `image`, `media`, `font` field types.
- Fixed a bug in the `autocomplete` custom field type that the default post type slug was not set properly when the page that the field is displayed contains the `post_type` query key in the url.

= 3.1.7 - 2014/09/12 =
- Added the `github` custom field type that displays GitHub buttons.
- Fixed an incompatibility issue of the `grid` custom field type with Internet Explorer.
- Fixed an incompatibility issue of the `link` custom field type with WordPress 3.8.x or below and Internet Explorer.
- Fixed a bug that the `checkbox` field type could not be repeated and sorted.
- Fixed an incompatibility issue of the `autocomplete` field type with WordPress 4.0 when `WP_DEBUG` is enabled.

= 3.1.6 - 2014/09/08 =
- Added the `stopped_sorting_fields` JavaScript hook for field type that supports sortable fields.
- Added support of repeatable and sortable rich text editor of the `textarea` field type except quick tags.
- Added an example of a download button in the demo plugin.
- Added the `system` custom field type.
- Changed the timing of the `removed_repeatable_field` callback for sections from before removing the section to after removing it.
- Tweaked the styling of switchable tabs of tabbed sections to remove dotted outlines when focused or activated which occur in FireFox.
- Fixed an incompatibility issue with WordPress 4.0 for the media modal frame.

= 3.1.5 - 2014/08/31 =
- Added the `content_{instantiated class name}` hook and the default `content()` callback method that filters the post type post content for the post type class.
- Added the ability to flush rewrite rules automatically upon plugin de/activation and theme activation.
- Changed the post type class to perform the set-ups including post type and taxonomy registration immediately if the class is instantiated after the `init` hook.
- Fixed an issue that then the user opens multiple pages created by the framework in the browser and submit one of the forms, the other forms failed nonce verification.
- Fixed a bug that caused JavaScript errors in `post.php` when adding meta box fields with the framework, which caused the media button not to function in the page.

= 3.1.4 - 2014/08/29 =
- Added the ability to search users for the `autocomplete` custom field type.
- Fixed an issue that field error transients and admin notice transients were not handled properly when multiple WordPress users on the site are working on admin pages created by the framework.
- Fixed an issue that options did not save when the site enables object caching.

= 3.1.3 - 2014/08/13 =
- Added the `load_after_{instantiated class name}` hook that is triggered right after the `load_{...}` hooks are triggered.
- Added the `set_up_{instantiated class name}` hook that is triggered right after the `setUp()` method is called.
- Added the footer link in the custom taxonomy pages created by the framework (`tags.php`, `edit-tags.php`).
- Added the ability for the `autocomplete` custom field type to support multiple post types and post statues.
- Added the `link` custom field type in the demo plugin.
- Changed the timing of finalizing in-page tabs so that in-page tabs now can be added in `load_{...}` hook callbacks.
- Changed the `start_{instantiated class name}`, `do_{...}`, `do_before_{...}`, `do_after_{...}`, and `do_form_{...}` action hook to pass the class object instance in the first parameter of the callback methods.
- Tweaked the process of post type registration to improve performance.
- Tweaked the performance by eliminating unnecessary function calls.
- Tweaked the styling of media select buttons.
- Fixed bugs that in the network admin area, transients were not handled properly.
- Fixed a bug that admin notices were not displayed in the network admin pages.
- Fixed a bug that the `load_{...}` hooks are triggered more than once per page.
- Fixed a bug that the same setting notice message got displayed the number of times of the framework object instances when another framework page with a form is loaded while saving the form data in the page.

= 3.1.2 - 2014/08/09 =
- Added the `validation_saved_options_{instantiated class name}` filter hook.
- Changed the timing of loading field type definitions in the taxonomy and meta box classes to allow define custom field types in the `setUp()` method.
- Changed the `load_{...}` hook callbacks to be able to add form elements.
- Fixed an issue that nonce verification failed when there is an output of `WP_List_Table` in the framework page with the framework form elements.
- Fixed a bug that meta boxes for the `post` post type created with the framework meta box class did not appear in `post-new.php` page.

= 3.1.1 - 2014/08/01 =
- Added the `before_fieldset` and `after_fieldset` arguments for the field definition array.
- Added the third parameter to the `addTaxonomy()` method to accept multiple object types in the post type class.
- Changed the `label_min_width` argument to accept non pixel values.
- Changed the default value of the `order` argument of in-page tabs to 10.
- Changed the field definition arrays to be formatted after applying filters of the `field_definition_{instantiated class name}` hook.
- Changed the timing of `field_definition_{instantiated class name}` filter hook to be triggered after all `field_definition_{instantiated class name}_{section id}_{field_id}` and `field_definition_{instantiated class name}_{field_id}` filter hooks.
- Fixed a bug that the `show_in_menu` argument of the `addSubMenuItems()` method did not take effect.
- Fixed an issue that the `order` argument of in-page tabs did not take effect when in-page tabs are added via the `tabs_{instantiated class name}` filter.
- Fixed an issue that the `label_min_width` argument of a field definition array was no longer effective as of v3.1.0.
- Fixed a bug that the stored values of repeatable fields with a custom capability got lost when a lower capability user submits the form.
- Fixed a bug that items of repeatable fields of page-meta-boxes could not be removed.

= 3.1.0 - 2014/07/18 =
- Added the `options_{instantiated class name}` filter hook to suppress the data used to display the form values.
- Added the `AdminPageFramework_Debug::log()` method.
- Added the ability not to set the default link to the custom post type post listing table's page in the plugin listing table page by passing an empty string to the 'plugin_listing_table_title_cell_link` key of the 'label' argument option.
- Added the `date_range`, `date_time_range`, `time_range` custom field type.
- Added the ability to set options for the `date`, `date_time`, and `time` custom field types.
- Added the `hasSettingNotice()` method that checks if at least one setting notice has been set or not.
- Added the `admin-page-framework-subfield` class selector to the div element's class attribute of field containers of sub-fields. 
- Added the `field_definition_{instantiated class name}` filter hook that applies to all the defined field arrays.
- Added the `disableSavingOptions()` method that disables the functionality to save submitted form data into the options table.
- Added the `setPluginSettingsLinkLabel()` method which enables to set the text label to the automatically embedded link to the plugin listing table of the plugin title cell in addition to disabling the functionality.
- Added the `start()` method which is automatically called at the end of the constructor, which can be used when the instantiated class name cannot be determined. 
- Added the ability to disable settings notices by passing false to the `$_GET{'settings-notice']` key.
- Added the `AdminPageFramework_NetworkAdmin` abstract class that enables to add pages in the network admin area.
- Tweaked the styling of the `number` input type to align the text on the right.
- Tweaked the styling of the `checkbox` field type not to wrap the label after the checkbox.
- Tweaked the styling of field td element when the `show_title_column` option is set to false to disable the title.
- Changed the `removed_repeatable_field` callback to be triggered after the element is removed.
- Changed the target form action url not to contain the `settings-updated` key.
- Changed the demo plugin to be separated into smaller components.
- Changed the `validation_{...}` callback methods to receive a third parameter of the class object so that third party scripts can access object members inside from the validation method.
- Changed the `AdminPageFramework` class to accept an empty string value to be passed to the first parameter of the constructor, to be used to disable saving options.
- Changed the scope of `oUtil`, `oDebug`, and `oMsg` objects to public from protected to be accessed from an instantiated object.
- Changed the `section_head` filter hook to be triggered even when the section description is not set.
- Changed not to redirect to options.php when a form created by the framework is submitted in the pages created by the framework.
- Fixed a bug that a value of `0` did not get displayed but and empty string instead.
- Fixed a bug that sub-fields could not properly have the default key-values of the field definition of the type.
- Fixed a bug that in PHP v5.2.x, setting a section error message caused a string "A" to be inserted in each belonging field.
- Fixed a bug that previously set field error arrays were lost if the `setFieldErrors()` method is performed multiple times in a page load.
- Fixed a bug that page load info was not inserted when multiple admin page objects were instantiated.
- Fixed a bug that duplicated setting notices were displayed.
- Fixed a bug that the redirect transient remained when a field error is set and caused unexpected redirects when the 'href' argument is set for the submit field type.
- Fixed an issue that `textarea` input field was placed in the wrong position when the browser turned off JavaScript.
- Fixed a bug that the `autocomplete` custom field type's JavaScript script could not run when the prePopulate option is set and the value is saved without changing.
- Fixed an issue in the class autoloader that caused a PHP fatal error in some non GNU OSes such as Solaris in the development version.

<h4>[Old Change Log Items](https://raw.githubusercontent.com/michaeluno/admin-page-framework/master/changelog.md)</h4>