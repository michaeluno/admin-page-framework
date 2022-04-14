# Admin Page Framework Change Log

## Changelog

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

#### 3.8.34 - 2021/09/25 
- Fixed irregular vertical alignment of elements with the `inline_mixed` field type.

#### 3.8.33 - 2021/08/16 
- Fixed a bug that `img` tags were dropped in sending Emails with the `email` argument of the `submit` field type.
- Fixed a bug that delayed sending Emails with the `email` argument of the `submit` field type, started in v3.8.32.

#### 3.8.32 - 2021/08/14 
- Fixed some raw HTTP request values.
- Tweaked the behavior of the `submit` filed type with the `email` argument.
- Tweaked the visual of repeatable buttons.

#### 3.8.31 - 2021/08/10 
- Added the ability to automatically load .min files if they exist when enqueuing resource files.
- Added the `icon` argument to the `select2` field type that supports icons to be added to select option elements.
- Fixed an incompatibility issue with sites with custom `WP_CONTENT_DIR` and `WP_CONTENT_URL`.

#### 3.8.30 - 2021/07/09 
- Fixed a bug with repeatable radio buttons that initial selection remained in the original field after repeating.
- Fixed some JQMIGRATE browser console warnings.
- Fixed a bug that octet characters in URL query parameters were lost in request data sanitization.
- Fixed a bug that normal characters enclosed in the percent signs were stripped in the process of request data sanitization.

#### 3.8.29 - 2021/06/17 
- Fixed a bug that duplicated class selectors were added to the class attribute of input elements with the `class` field argument when nested elements are specified.
- Fixed a bug with the `AdminPageFramework_WPUtility_Meta::getSavedTermMetaArray()` method that did not retrieve any values
- Fixed a bug with the `AdminPageFramework_ArrayHandler::delete()` method that did not delete given array elements properly.
- Fixed a bug with the `AdminPageFramework_ErrorReporting` class that did not retrieve proper error level labels when a value was passed to the constructor parameter.
- Fixed a bug with the `addLinkToPluginTitle()` method that did not properly add links when a value of an array was passed.
- Fixed a bug with the `enqueueScripts()` method of the page metabox factory class that did not enqueue scripts properly.

#### 3.8.28 - 2021/05/15 
- Fixed a bug that page meta box did not appear without specifying in-page tabs in PHP 8 or above.

#### 3.8.27 - 2021/04/21 
- Fixed a bug that the `capability` field argument did not hide the field title.
- Fixed a bug that backslashes were stripped out for text input values.

#### 3.8.26 - 2021/02/11 
- Fixed a bug that stripped some valid text area values when sanitizing.
- Fixed a bug that form capabilities were not properly inherited from its page when a tab does not exist.
- Fixed the PHP Fatal error "Uncaught TypeError: round(): Argument #1 ($num) must be of type int|float, string given in ...AdminPageFramework_PageLoadInfo_Base.php:96." in PHP 8.0, which resulted in not loading the form with JavaScript errors.
- Fixed a bug that caused a PHP notice in PHP 8.0, saying "Deprecated: Required parameter $... follows optional parameter".

#### 3.8.25 - 2020/11/20 
- Fixed a bug that caused textarea form values to have no mark-ups.

#### 3.8.24 - 2020/11/14 
- Added the `confirm` argument for the `submit` field type.
- Added a message to appear when the submit button of the `import` field type is clicked without uploading a file.
- Tweaked the style of the `import` field type.
- Fixed non-sanitized HTTP request arrays.
- Fixed a bug that field capability did not inherit from its section properly.
- Changed the behavior of the textarea of the system field type not to select text on click.
- Removed the `ace` custom field type as it was warned by the wordpress.org plugin team not to load files remotely.

#### 3.8.23 - 2020/11/05 
- Added the ability to automatically insert the current in-page tab title in the page title.
- Added some utility methods dealing with transients.
- Fixed a bug with the `AdminPageFramework_Utility::getCurrentURL()` method that an extra port was inserted with the server with a custom port.

#### 3.8.22 - 2020/09/11 
- Added parameters for the `AdminPageFramework_Debug::log()` method that let the user set a log file name, whether to include a stack trace, and the truncation lengths for strings and arrays.
- Tweaked the style of repeatable buttons and dash-icon buttons for WordPress 5.3 or above.
- Changed the form nonce lifespan to 2 days from 1 hour.
- Changed the `id` attribute of internal styles and scripts to be all lower-cased.
- Fixed a bug with the `select2` custom field type that repeated select2 field was not selectable.
- Fixed a bug that some internal styles were not minified when the site debug mode is turned off.
- Fixed a bug that same values of internal styles and scripts with the `style_{extended class name}`, `style_ie_{extended class name}` and `script_{extended class name}` were inserted multiple times.
- Fixed a bug that the collapsible section toggle icons were not toggled.
- Fixed a bug that in `admin-ajax.php` unnecessary callbacks were loaded.
- Deprecated debug information for section and field arguments.

#### 3.8.21 - 2019/08/19 
- Fixed an incompatibility issue with WordPress 5.5 regarding radio buttons.
- Fixed an issue of a PHP warning of an undefined index `file`.
- Fixed an incompatibility issue with wordpress.com.

#### 3.8.20 - 2019/05/31 
- Fixed the PHP error `Call to undefined method WP_Error::get_items()` in the Add-ons page when fetching feeds failed.
- Fixed a bug with the `select2` custom field type that initialization failed for repeated fields.

#### 3.8.19 - 2019/02/24 
- Added checks to prevent unnecessary calls on `setUp()` in `admin-ajax.php`.
- Tweaked styling of the welcome page in the admin area of the loader plugin for WordPress 5.0.
- Changed generated log file names with `AdminPageFramework_Debug::log()` to be shortened.
- Fixed a bug with the custom `select2` field type that requests did not go through with nested fields.
- Fixed a bug with the custom `select2` field type that showed the message "the results could not be loaded" while processing a Ajax requests.
- Fixed a bug with the `AdminPageFramework_Debug::log()` method that did not properly retrieve correct caller method names.
- Fixed an issue that some third-party tools reported false positive for PHP 7.2 incompatibility due to a method name prefixed with double underscores.

#### 3.8.18 - 2018/07/17 
- Fixed a bug that inline/nested field values were not saved properly in widget forms.

#### 3.8.17 - 2018/07/09 
- Fixed a bug that the `save` argument did not take effect for User Meta, Page Meta Box and Widget factories.
- Fixed a compatibility issue with some third-parties which attempt to instantiate the framework widget class without given any parameters.

#### 3.8.16 - 2018/07/06 
- Fixed a bug that the default sub-menu page of a custom top-level menu page could not be removed when the PHP class with a namespace is used.

#### 3.8.15 - 2017/01/23 
- Fixed a bug that caused an undefined method warning in the admin notice class.

#### 3.8.14 - 2017/01/15 
- Added the `load()` methods to factory classes of post/page meta boxes, user/taxonomy meta.
- (minor breaking change) Deprecated the parameter of the `load()` method of the widget factory class.
- Changed the behaviour of not loading form components in `admin-ajax.php` so that custom field types can access `admin-ajax.php` using Ajax.
- Fixed an issue that Ajax requests of the `select2` custom field type could not retrieve responses for the factory classes other than the admin page.

#### 3.8.13 - 2016/12/22 
- Added the `disabled` repeatable section and repeatable field arguments.
- Added the `interactive`, `can_exceed_min`, `can_exceed_max`, and `allow_empty` arguments to the `no_ui_slider` field type.
- Fixed incompatibility with WordPress 4.7 that caused a notification page meta box to appear when it should not due to the change of action hook handling.
- Fixed a bug that clicking on a collapsible section button of the `button` type did not collapse/expand the section.
- Fixed a but that a vertical scrollbar appeared in an admin page that has a form when the browser width is less than 900px.
- Tweaked the style of the `size` field type.
- Tweaked the style of collapsible sections.
- Tweaked tye style of section tabs for WordPress 4.7.

#### 3.8.12 - 2016/11/28 
- Fixed a bug that caused an undefined index warning when a widget is added, introduced in 3.8.11.

#### 3.8.11 - 2016/11/25 
- Fixed a bug that `if` field argument did not applied to the entire field output.
- Fixed a bug which could cause warnings "Creating default object from empty value in ...AdminPageFramework_Resource_post_meta_box.php".
- Fixed a bug calling an undefined method when setting a `help` argument in form section definitions.
- Changed the behaviour of adding form resource scripts to add them all in the footer.

#### 3.8.10 - 2016/11/09 
- Fixed a bug that the `hidden` field argument did not take effect for nested fields.
- Fixed a bug that parent fields for nested fields were passed to the `repeated_field` callback argument of the custom `registerAdminPageFrameworkCallbacks()` jQuery plugin method.

#### 3.8.9 - 2016/11/05 
- Fixed a bug occurred in PHP 5.3 that caused a warning `debug_backtrace() expects at most 1 parameter, 2 given`.
- Fixed an issue that setting an object instance in field definition arguments caused slow performance on loading the form.
- Tweaked the debug output format of field/section arguments.
- Tweaked the style of tool-tips.

#### 3.8.8 - 2016/10/26 
- Added the `post_type_taxonomy` custom field type that lets the user select taxonomy terms of selected post types.
- Added the `show_debug_info` argument to the page, in-page tab, section, and field definition arguments to decide whether to display debug information.
- Added the ability to display section arguments in tool-tips beside the section title.
- Added the `save_unchecked` argument for the `checkbox`, `posttype`, and `taxonomy`  field types that decides whether to save values of unchecked items.
- Fixed a bug of calling a member function getSyntaxHighlightedPHPCode() on an undefined object in the network admin page of the demo.
- Fixed a section ID conflict of `mixed` in demo examples.
- Changed the handling mechanism of repeated fields (minor internal breaking change).
- Tweaked the style of tool-tips.
- Tweaked the style of `taxonomy` fields.

#### 3.8.7 - 2016/10/09 
- Added the `select2` custom field type that lets the user select items with auto-complete list which can possibly populated with AJAX.
- Added the ability for the `path` and `toggle` custom field types to support repeatable sections.
- Fixed a bug that a section title was not displayed when there was a field with the `placement` argument of the `section_title` value.
- Fixed a PHP warning with the `no_ui_slider`, `array_fill() [function.array-fill]: Number of elements must be positive...`.
- Fixed a bug with the `no_ui_slider` custom field type that saving a value of `0` caused a slider not to be displayed.
- Changed the `getDataAttributeArray()` utility method to convert camel-cased keys to be dashed.
- Tweaked the style of collapsible section titles.

#### 3.8.6 - 2016/10/02 
- Added the `no_ui_slider` custom field type which lets the user set values in ranges.
- Added the ability for the `text` field type to accept nested `attributes` argument of a name of the corresponding `label` argument array element.
- Changed the `getDataAttributeArray()` utility method to accept and convert array elements to a JSON string.

#### 3.8.5 - 2016/09/25 
- Added the `toggle` custom field type which lets the user switch a toggle button.
- Added the ability to automatically insert field definitions in a tool-tip for each field.
- Fixed a bug that duplicated form style resource files were loaded.
- Fixed a bug that handle IDs of style resources of forms and pages could conflict which resulted in not loading some items of page resources of styles.
- Fixed a bug that collapsible section arguments were not processed properly, introduced in v3.8.4.
- Fixed an issue that it was not possible to select tool-tip text.
- Tweaked the style of tool-tips.

#### 3.8.4 - 2016/09/21 
- Added the `path` custom field type which lets the user pick a file located on the server.
- Fixed a bug that the `aOptions` property values were not updated when they are filtered with the `options_{class name}` filter hook.
- Fixed a bug that setting `0` to the `label_min_width` field definition argument did not take effect.
- Fixed a bug that an empty heading element was displayed even when the tab title of in-page-tab was set to empty.
- Fixed a bug with the admin page factory class that transient option keys which get automatically set when an integer is passed to the first parameter of the constructor were not unique and could be too long.
- Fixed the timing of the `field_definitions_{class_name}` filter to keep compatibility with v3.7.
- Fixed a bug with the `Generator` that a set class prefix was not set properly for custom field types in some cases.
- Fixed a bug in the `getDataAttributeArray()` utility method that some non-true values were all treated as `0`.

#### 3.8.3 - 2016/09/08 
- Fixed an issue that array elements of sub-menu items without a key added with the `pages_{class name}` filter were not processed properly.
- Fixed an issue that a class creating a root menu page must be instantiated prior to a class adding a sub-menu page with the `setRootMenuPageBySlug()` method by passing the class name of the root menu page.

#### 3.8.2 - 2016/08/25 
- Fixed a bug that automatically generated script paths were not accurate when multiple scripts shared the same library file.

#### 3.8.1 - 2016/08/12 
- Fixed a bug that repeatable tabbed sections were not displayed properly until a tab is clicked when it is repeated.

#### 3.8.0 - 2016/07/26 
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

#### 3.7.15 - 2016/05/31 
- Fixed a bug that caused a warning saying accessing an undefined object property in the network admin area.

#### 3.7.14 - 2016/04/20 
- Fixed a compatibility issue with WordPress 4.5 that taxonomy form fields were not displayed in the term editing page.

#### 3.7.13 - 2016/03/04 
- Fixed a PHP notice, `Array to string conversion in ...AdminPageFramework_FieldType_media.php` caused when setting an array to the `repeatable` field definition argument of the `image` and `media` field type.

#### 3.7.12 - 2016/02/20 
- Added the ability to set custom classes for sub-objects.
- Added an example for page meta boxes for in-page tabs in the demo.
- Added the `load()` method to the admin page factory class which gets called when one of the added admin pages starts loading.
- Fixed a bug that zip files generated with `Generator` could not be opened on Windows.
- Fixed a bug that the `size` field type could not save and retrieve values in a sub-field, introduced in v3.7.0.
- Tweaked the style of page meta boxes.

#### 3.7.11 - 2016/01/15 
- Fixed a bug that the default plugin action link were doubled when setting a custom label.
- Changed the error level of custom errors for using deprecated items.

#### 3.7.10 - 2016/01/13 
- Added the `load()` method to the post type factory class which gets called when the `edit.php` page of the post type starts loading.
- Optimized performance in the common admin area.
- Changed the timing of registering page meta boxes so that their classes can be instantiated in the `load_{...}` hook of the admin page factory class.
- Changed the framework inline CSS rules in the compiled library files to be minified.

#### 3.7.9 - 2016/01/07 
- Reduced the number of library files.
- Fixed a bug that site-wide field type definitions were overridden by built-in field types of per-class field type definitions which caused some field types which override the built-in field type slug did not load, introduced in v3.7.1.
- Fixed an issue with widgets that caused some overhead.
- Fixed an issue that widget resources were loaded in pages that do not have widget forms.
- Tweaked the style of collapsible form sections.
- Changed the color of setting notices for resetting options with the `submit` field type from red to green.
- Changed the demo to be disabled when the loader plugin admin pages are disabled.

#### 3.7.8 - 2015/12/31 
- Reduced the number of database queries used in framework forms.

#### 3.7.7 - 2015/12/30 
- Fixed an issue that the framework forms could not be displayed when some third-party plugins or themes have JavaScript errors.
- Fixed a bug in a field object calling an undefined method.

#### 3.7.6.1 - 2015/12/23 
- Fixed a bug that debug log was generated, introduced in v3.7.6.
- Fixed a bug that Generator could not generate some files, introduced in v3.7.6.

#### 3.7.6 - 2015/12/23 
- Added the `skip_confirmation` argument for the `submit` field type, which skips submit confirmation.
- Fixed some compatibility issues with PHP 7.
- Fixed an issue that fron-end pages of custom post types registered with the framework could not be accessible in some occasions.
- Fixed a bug that resetting options did not show a message since v3.5.3.
- Fixed a bug that page titles got doubled in the `title` tag when there is a hidden page.
- Changed the timing of resetting options and sending contact form emails of the `submit` field type to after the validation hooks so that the user can cancel their actions.

#### 3.7.5 - 2015/12/18 
- Reduced the number of database queries used in framework widget forms.

#### 3.7.4 - 2015/12/16 
- Added the `submenu_order_addnew` and `submenu_order_manage` arguments for the post type arguments.
- Added the `submenu_order` argument for the taxonomy arguments.
- Fixed a bug that setting notices could not be displayed in the network admin area, introduced in 3.7.0.
- Fixed a bug in the demo of network admin pages that called non existent class, introduced in 3.7.0.
- Fixed a bug in the network admin factory class that called an undefined method, , introduced in 3.7.0.
- Changed the default capability value of the user meta factory class to `read` to allow subscribers to edit options of their profiles.
- Changed the incremental offset automatically assigned to the `order` argument of sub-menu items.
- Changed the `order` argument of sub-menu items to be effective site-wide.

#### 3.7.3 - 2015/12/13 
- Added the `action_links_{post type slug}` filter to the post type factory class.
- Fixed an issue of a fatal error `Maximum function nesting level of 'x' reached` when the server enables the XDebug extension and sets a low value for the `xdebug.max_nesting_level` option.

#### 3.7.2 - 2015/12/11 
- Fixed a compatibility issue with WordPress 4.4 that widget fields with a section could no longer save and retrieve the values.
- Fixed a bug that the framework library files and user-generated library files were missing file doc-blocks.
- Fixed an issue that custom field type specific text domain could not be converted with the Generator tool.
- Changed the form sections and fields registration mechanism of the admin page class to accept items without the `page_slug` and `tab_slug` by letting them being added to the current page or tab which is registed by the classs.

#### 3.7.1 - 2015/12/08 
- Added the internal ability for the `select` and `radio` field types to accept nested `attributes` arguments for each `label` element.
- Fixed a bug that site-wide field type definitions were loaded multiple times per page load, introduced in 3.7.0.
- Tweaked the style of form sections and fields.
- Tweaked the style of help tool tips.

#### 3.7.0 - 2015/12/04 
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

#### 3.6.6 - 2015/11/26 
- Changed back the change introduced in v3.6.4 not to convert backslashes to underscores in class names in the framework hooks but only apply to auto-callback method names.

#### 3.6.5 - 2015/11/21 
- Fixed a bug that layouts of page meta boxes were not displayed properly when no `side` meta box existed and one of `normal` or `advanced` was added.
- Fixed the style of section tabs in WordPress 4.4.

#### 3.6.4 - 20105/11/19 
- Added the `APFL_SILENT_MODE` constant to the loader plugin that toggle the visuals of the loader admin pages.
- Tweaked the style of `textarea` fields.
- Fixed an issue that a column data were not updated right away when the user uses Quick Edit in a post listing table of a post type.
- Changed the class names in the framework hook names to get backslashes converted to underscores to avoid invalid characters in callback method names.

#### 3.6.3 - 2015/11/07 
- Added the `script` and `style` arguments for the page and in-page tab definitions.
- Tweaked the style of section tab titles in meta boxes.
- Fixed an issue in WordPress 3.7.x or below that the screen icon appeared even when the screen title and the in-page tabs are disabled.
- Changed the required WordPress version to 3.4.

#### 3.6.2 - 2015/10/31 
- Added a notification box in the admin pages of the loader plugin.
- Tweaked the style of heading tags in meta boxes.
- Tweaked the style of buttons of collapsible sections.
- Fixed a bug that the form values were not saved correctly with a sortable and repeatable section containing repeatable fields.
- Fixed a bug in the `taxonomy` fields that conditions set with the `if` and `capability` arguments were not applied.

#### 3.6.1 - 2015/10/26 
- Added the ability to activate a form section tab by URL.
- Added the `content` argument for section and field definition arrays to define custom outputs.
- Added a hook to filter parsing contents to the `AdminPageFramework_WPReadmeParser` utility class.
- Fixed a bug with form section tabs that the active content elements were not visible when a container element is hidden first on the page load.
- Fixed a bug caused a fatal error in the `AdminPageFramework_AdminNotice` class, introduced in 3.5.12.

#### 3.6.0 - 2015/10/22 
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

#### 3.5.12 - 2015/08/09 
- Fixed a bug that the `Select All` and `Select None` buttons were doubled when a `checkbox` and `posttype` field types were in the same form and not displayed with the `taxonomy` field type when there are no `checkbox` or `posttype` fields in the same page.
- Tweaked the class selectors of admin notification elements to have dismiss buttons available in WordPress 4.2 or above.

#### 3.5.11 - 2015/07/14 
- Added a warning to be displayed for forms in generic admin pages when the user form inputs exceeds the PHP `max_input_vars` value.
- Added the column layout screen options for page meta boxes.
- Fixed a bug that screen options were not saved in generic pages added by the framework.

#### 3.5.10 - 2015/07/05 
- Added the `show_submenu_add_new` post type argument which enables to remove "Add New" sub-menu item from the sidebar menu.
- Added the `attributes` and `disabled` arguments to page and in-page tab definition array which gets applied to navigation tab element.
- Changed the behavior of the `color` field type to have the default value getting reflected in repeated field.
- Fixed an issue that default values could not be set for user meta fields.
- Fixed an issue that magic quotes were not sanitized in taxonomy field inputs.

#### 3.5.9 - 2015/06/25 
- Added the ability for the `size` field type to create sub-fields by passing an array of labels.
- Added the `reset_...` action hooks.
- Added the ability to disable setting notices by passing an empty string to the ` setSettingNotice()` method.
- Added the ability for the admin page factory class to save form options only for a set time of period by passing an integer for the option key to the constructor.
- Added the ability for transient utility methods to accept long transient key names.
- Fixed an issue that post meta-box form fields were not able to set default values when there were existing meta data.
- Fixed a bug in the `getOption()` utility method that the fourth parameter did not take effect when `null` is given to the second parameter.
- Changed the timing of rendering the widget title to after the `do_{...}` and `content_{...}` hooks.
- Changed the zip file name of generated framework files to have a version suffix.

#### 3.5.8 - 2015/05/29 
- Added the ability for the `getValue()` method to set a default value with the second parameter when the first parameter is an array.
- Added the ability for the `text` and `textarea` field types to create sub-input elements by passing an array to the `label` argument.
- Added the `width` argument for the `taxonomy` field type.
- Fixed a bug that the `name` attribute value was not set in post meta box fields in `post-new.php`, introduced in 3.5.7.
- Fixed a bug with the `taxonomy` field type that could not list terms when the `arguments` argument misses the `class` argument.

#### 3.5.7.1 - 2015/05/11 
- Fixed a bug with referencing pressed submit button name attributes, introduced in 3.5.7.

#### 3.5.7 - 2015/05/05 
- Fixed a compatibility issue with WordPress 4.2 or above that the `taxonomy` field type become not able to list taxonomy terms.
- Tweaked styling of page meta-box form elements.

#### 3.5.6 - 2015/03/15 
- Fixed a bug that form section values of page meta box could not be displayed.

#### 3.5.5 - 2015/03/08 
- Added the `footer_right_{...}` and `footer_left_{...}` filter hooks.
- Fixed an issue that the loader plugin could not get activated when a plugin that includes the framework of a lesser version that that not have a certain class constant.
- Changed not to include the development version in the plugin distribution package.
- Changed the `setFooterInfoLeft()` and `setFooterInfoRight()` methods to be deprecated which had not been functional since v3.1.3.

#### 3.5.4 - 2015/03/02 
- Added the framework component generator in the loader plugin which can be accessed via `Dashboard` -> `Admin Page Framework` -> `Tools` -> `Generator`.
- Added the `export_header_{...}` filters that lets the user set custom HTTP header outputs for the `export` field type.
- Fixed a bug in the `system` field type that PHP warnings occurred when the permission to read error logs is not sufficient.
- Changed the minified version to be deprecated.
- Changed the version name of the development version to have `.dev` notation in the version name.

#### 3.5.3 - 2015/02/21 
- Added the ability to reset individual field values with the `reset` argument of the `submit` field type.
- Added a user meta factory class demo example.
- Added the `validate()` and `content()` methods in the admin page factory class.
- Added the fourth parameter to the `validate()` method of page meta box factory class to receive submit information.
- Fixed a bug that invalid field values were stored when submitting the form multiple times using validation callback methods.
- Fixed an issue in the loader plugin that after resetting the loader plugin options via the `Debug` tab, a warning 'You do not have sufficient permissions to access this page.' appeared.
- Fixed an issue in the user meta factory class that a PHP notice, Trying to get property of non-object..., which appeared when creating a new user.
- Fixed an issue that the `image` field type did not extend `AdminPageFramework_FieldType` but `AdminPageFramework_FieldType_Base`.

#### 3.5.2 - 2015/02/02 
- Fixed a bug in the widget factory class that form sections could not be set.
- Changed the `class` argument of the section definition array to accept a string.

#### 3.5.1.1 - 2015/01/24 
- Fixed a bug that caused non-object type PHP error in the post type factory class introduced in v3.5.1.

#### 3.5.1 - 2015/01/23 
- Fixed a bug in the `enqueueScripts()` method of the admin page factory class.
- Fixed a bug that message objects were not properly instantiated.
- Fixed PHP strict standard warnings.

#### 3.5.0 - 2015/01/22 
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

#### 3.4.6 - 2015/01/06 
- Added a page that lets the user download the minified version of the framework in the demo examples.
- Added a utility class that checks requirements of servers such as PHP and MySQL versions etc.
- Added the `print_type` argument to the `system` field type.
- Added more information to the `system` field type.
- Added (revived) the minified version and it is now human readable.
- Fixed an issue that form fields without setting a section were not registered in pages except first added ones.
- Changed the timing of importing and exporting of the `import` and `export` field types to be done after performing validation callbacks.

#### 3.4.5.1 - 2015/01/01 
- Removed the minified version as the WordPress plugin team demanded to do so.

#### 3.4.5 - 2014/12/27 
- Added the `setting_update_url_{instantiated class name}` filter hook and the pre-defined callback method.
- CHanged the `getValue()` method of the admin page factory class to respect last input arrays.
- Fixed a bug that caused a PHP warning that prevented a contact form from being sent on servers with some error reporting settings.
- Fixed an issue on some servers that the script got aborted while sending an email of the contact form.
- Fixed a url query key after submitting a contact form.
- Tweaked the styling of the form confirmation message container element.

#### 3.4.4 - 2014/12/17 
- Fixed a bug that in in-page tabs saving form values of page meta box fields caused data loss of other in-page tabs, introduced in v3.4.3.

#### 3.4.3 - 2014/12/15 
- Added CSS rules for debug output container elements.
- Changed the format of ID of section container elements.
- Fixed a bug that repeated fields could not be removed in page meta boxes started in v3.4.1.
- Fixed a bug that in page meta boxes, the `select` field type with the `is_multiple` argument enabled could not store the submitted data properly.
- Fixed a bug that page meta boxes could not be added by in-page tab slug.

#### 3.4.2 - 2014/12/08 
- Added the ability to automatically update the preview element of an image field when an image URL is manually typed without using the upload window.
- Added the ability to accept URLs for the `attachments` argument for the `email` argument of the `submit` field type.
- Changed the timing of the `setUp()` method of the post type factory class to let the post type arguments set in the method.
- Fixed an issue of the custom media uploader with IE8.
- Fixed a bug that the `Insert from URL` pane of the custom uploader modal window did not function even when the `allow_external_source` is set to `true`.

#### 3.4.1 - 2014/12/02 
- Added the `options_update_status_{...}` and `validation_saved_options_without_dynamic_elements_` filters for the admin page factory class.
- Added the `field_definition_{...}` filters for the page meta box class.
- Added the `validate()` and `content()` methods for the meta box and page meta box factory classes.
- Changed the timing of `field_definition_{...}` filters of the admin page class.
- Changed not to lose the input data when a form validation error occurs for the meta box, page meta box, and page classes.
- Fixed an issue that github buttons of the `github` field type did not load in some sites.
- Fixed a bug that repeatable sections could not be repeated when there is no collapsible section in the page.
- Fixed a bug that the old options of the second parameter passed in a validation callback method of the page meta box class did not hold dynamic elements.
- Fixed a bug that the action hooks and their predefined callbacks `submit_{instantiated class name}_{page slug}` and `submit_after_{instantiated class name}_{page slug}` did not work.

#### 3.4.0 - 2014/11/23 
- Added the ability of collapsing and expanding section containers with the `collapsible` section definition argument.
- Added the `select_type` argument for the `revealer` custom field type that enables to have checkboxes and radio buttons for the selector.
- Tweaked the styling of the `section_title` field type.
- Fixed a bug that the layout of form fields broke in Internet Explorer.

#### 3.3.3 - 2014/11/08 
- Added the framework version indication at the bottom of form sections in meta-boxes and widgets when `WP_Debug` is true.
- Changed not to display colon(:) after the field title if the title is empty in meta-boxes, taxonomy fields, and widgets.
- Fixed a bug that a gray blank image was inserted in the featured image's image uploader when the framework media uploader is loaded, introduced in v3.3.1.
- Fixed a bug that caused the uploader button of the `media` field type not appear, introduced in v3.3.2.

#### 3.3.2 - 2014/11/07 
- Added the `label_no_term_found`, `label_list_title`, `query`, and `queries` arguments for the `taxonomy` field type.
- Added the ability to compress CSS rules and JavaScript scripts defined in PHP variables for the minified version.
- Tweaked the styling of the `taxonomy` field type to reflect the hierarchical depths.
- Fixed a bug that resources of taxonomy fields added by the framework were loaded in other taxonomy pages that caused JavaScript errors.
- Fixed a bug that meta box field values defined via the `field_definition_{...}` filter hooks were not saved.
- Fixed a bug that setting sections for page meta box fields caused an error after submitting the form.
- Fixed a bug that class selectors set in the top level of the `class` argument for fields got inserted in all type of field containers including `fieldrow`, `fieldset`, `fields`, `field`.
- Fixed the `for` attribute value of label tags for fields to the focus input.

#### 3.3.1 - 2014/11/02 
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

#### 3.3.0 - 2014/10/22 
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

#### 3.2.1 - 2014/09/29 
- Added an example of using the `content_top_{...}` and the `style_common_admin_page_framework` filters.
- Added the `style_common_admin_page_framework` hook.
- Added support for a file path to be passed for image submit buttons.
- Added support for custom queries for the `posttype` field type.
- Added the `radio_checkbox` custom field type.
- Added the `image_checkbox` custom field type.
- Tweaked the styling of field error messages.
- Fixed an issue that sortable fields could not be rendered properly when being dragged in browser screen widths of less than 782px in Chrome in WordPress above v3.8.
- Fixed the `content_top_{...}` hooks and the methods were not available.

#### 3.2.0 - 2014/09/25 
- Added an example of using an image for a submit button.
- Added the option to set custom button labels via the `data-label` attribute for the `image`, `media`, and `font` field types.
- Added the remove button for the `image`, `media`, and `font` field types.
- Added the default and Japanese translation files.
- Added the `show_post_count` argument for the `taxonomy` field type and made it enabled by default.
- Added the widget factory class and the examples of creating widgets with the framework in the demo plugin.
- Fixed an issue that registering multiple taxonomies after the `init` hook failed registering second or later items.
- Fixed a bug that a last item did not set when selecting multiple items in the fields of the `image`, `media`, `font` field types.
- Fixed a bug in the `autocomplete` custom field type that the default post type slug was not set properly when the page that the field is displayed contains the `post_type` query key in the url.

#### 3.1.7 - 2014/09/12 
- Added the `github` custom field type that displays GitHub buttons.
- Fixed an incompatibility issue of the `grid` custom field type with Internet Explorer.
- Fixed an incompatibility issue of the `link` custom field type with WordPress 3.8.x or below and Internet Explorer.
- Fixed a bug that the `checkbox` field type could not be repeated and sorted.
- Fixed an incompatibility issue of the `autocomplete` field type with WordPress 4.0 when `WP_DEBUG` is enabled.

#### 3.1.6 - 2014/09/08 
- Added the `stopped_sorting_fields` JavaScript hook for field type that supports sortable fields.
- Added support of repeatable and sortable rich text editor of the `textarea` field type except quick tags.
- Added an example of a download button in the demo plugin.
- Added the `system` custom field type.
- Changed the timing of the `removed_repeatable_field` callback for sections from before removing the section to after removing it.
- Tweaked the styling of switchable tabs of tabbed sections to remove dotted outlines when focused or activated which occur in FireFox.
- Fixed an incompatibility issue with WordPress 4.0 for the media modal frame.

#### 3.1.5 - 2014/08/31 
- Added the `content_{instantiated class name}` hook and the default `content()` callback method that filters the post type post content for the post type class.
- Added the ability to flush rewrite rules automatically upon plugin de/activation and theme activation.
- Changed the post type class to perform the set-ups including post type and taxonomy registration immediately if the class is instantiated after the `init` hook.
- Fixed an issue that then the user opens multiple pages created by the framework in the browser and submit one of the forms, the other forms failed nonce verification.
- Fixed a bug that caused JavaScript errors in `post.php` when adding meta box fields with the framework, which caused the media button not to function in the page.

#### 3.1.4 - 2014/08/29 
- Added the ability to search users for the `autocomplete` custom field type.
- Fixed an issue that field error transients and admin notice transients were not handled properly when multiple WordPress users on the site are working on admin pages created by the framework.
- Fixed an issue that options did not save when the site enables object caching.

#### 3.1.3 - 2014/08/13 
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

#### 3.1.2 - 2014/08/09 
- Added the `validation_saved_options_{instantiated class name}` filter hook.
- Changed the timing of loading field type definitions in the taxonomy and meta box classes to allow define custom field types in the `setUp()` method.
- Changed the `load_{...}` hook callbacks to be able to add form elements.
- Fixed an issue that nonce verification failed when there is an output of `WP_List_Table` in the framework page with the framework form elements.
- Fixed a bug that meta boxes for the `post` post type created with the framework meta box class did not appear in `post-new.php` page.

#### 3.1.1 - 2014/08/01 
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

#### 3.1.0 - 2014/07/18 
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

#### 3.0.6 - 05/10/2014 
- Fixed a JavaScript syntax error in the `font` custom field type.
- Fixed a bug in the `image` and `media` field types and the `font` custom field type that escaping the frame did not cancel setting the selection.
- Fixed an issue that the section tab script was applying the styling to all the `ul` elements inside the section.
- Tweaked the styling of the repeatable section buttons.
- Tweaked the `autocomplete` custom field type to find more posts by loosening the search criteria. 
- Fixed a bug in the `autocomplete` custom field type that setting the `prePopulate` option caused a JavaScript error after submitting the form.
- Fixed an issue that submitted form input data array in validation callback methods lost array keys of fields with individual set capabilities when the form-submitting user has lower capability than the stored field capability.
- Added the ability to set a link and its label in the title cell of the plugin listing table for a custom post type created by the framework.
- Fixed an issue that the `dial` and `autocomplete` custom field type fields could not be repeated properly in repeatable sections.

#### 3.0.5 - 04/29/2014 
- Fixed a bug that the `redirect_url` option of the `submit` field type did not take effect.
- Fixed a bug that repeatable sections messages did not indicate the correct maximum and minimum numbers.
- Tweaked the `autocomplete` custom field type to have some delays to perform post title queries in the background.
- Changed the `validation_{instantiated class name}_{section id}_{field id}` and `validation_{instantiated class name}_{field id}` hooks to be triggered only when the section or field belongs to the page that the form is submitted.
- Fixed a bug that some public methods caused a PHP fatal error "Call to a member function" after submitting a form in multi-sites when a plugin is network-activated.
- Changed the post type class methods, `enquueueStyles()`, `enquueueStyle()`, `enquueueScripts()`, `enquueueScript()` to silently fail when they are called not in the post type page.

#### 3.0.4 - 04/19/2014 
- Improved the accuracy on search results of the `autocomplete` custom field type.
- Fixed a bug that the help pane of meta box fields did not appear in the page after submitting the form.
- Added the ability to set a validation error message to appear at the top of a form section output.
- Fixed a bug that saved field values of page meta boxes got lost when fields are saved in a different tab but in the same page.
- Fixed a bug that the `script_common_{...}` filter was not functioning in meta box classes.
- Added the ability to throw a warning when undefined method is called.
- Changed the file structure of the `development` directory.

#### 3.0.3 - 03/24/2014 
- Added the ability to reveal more than one elements to the `revealer` custom field type with a small braking change. 
- Tweaked certain routines not to be triggered in irrelevant pages.
- Tweaked the field type registration process to be faster.
- Fixed an undefined index warning in the `AdminPageFramework_Property_MetaBox_Page` class.
- Fixed a bug in the development version that the fatal error occurred when trying to include a taxonomy field class individually.
- Changed the default log location and file name.

#### 3.0.2 - 03/22/2014 
- Fixed a bug that repeatable sections could not be removed when they are placed in generic pages but without in-page tabs.
- Fixed an issue of magic quotes with meta box fields for the framework pages.
- Added examples of implementing a custom sort algorithm for columns of the taxonomy term listing table and the custom post type post listing table in the demo plugin.
- Added the the `cell_{instantiated class name}_{column slug}` filter for the taxonomy field class.
- Added the `field_definition_{...}` filter.

#### 3.0.1.4 - 03/09/2014 
- Fixed a bug that `setCapability()` did not take effect for form elements.
- Fixed an issue that the target tab slug and the target section tab slug do not reset after the `setSettingFields()` method returns.
- Tweaked the layout of the geometry custom field type.

#### 3.0.1.3 - 03/07/2014 
- Fixed a bug that custom columns could not be updated properly in the taxonomy definition page (edit-tags.php).
- Added `class_exists()` checks for sample custom field type classes for the demo plugin.

= 3.0.1.2 - 03/04/2014 = 
- Fixed a bug that repeatable field buttons did not add/remove when a section is repeated with a new ID due to non-assigned options.
- Fixed a bug that sortable fields of a repeated section could not be sorted.
- Fixed a bug that `image` and `media` field type fields could not be repeated properly in repeatable sections.

#### 3.0.1.1 - 03/01/2014 
- Fixed a bug that `taxonomy` field type fields could not be properly repeated.
- Tweaked the styling of the `taxonomy` field type fields.
- Tweaked the styling of horizontal alignment of `th` and `td` form elements.
 
#### 3.0.1 - 02/26/2014 
- Added the `AdminPageFramework::getOption()` method that can be used from the front-end to retrieve saved option values. 
- Fixed a bug that the plus(+) field repeater button got inserted when a section is repeated in WordPress 3.5.x or below.
- Tweaked the styling of section tabs to prevent small dots from appearing when activating a tab. 
 
#### 3.0.0.1 - 02/24/2014 
- Tweaked the styling of section tabs with `section_title` type fields.
 
#### 3.0.0 - 02/24/2014 
- Added: the `section_title` field type that lets the user to enter a section title.
- Added: the ability to display form sections in tabs by specifying the `section_tab_slug`.
- Added: the `autocomplete` custom field type.
- Added: the ability to repeat form section.
- Added: the ability to set form sections in meta boxes.
- Added: the ability to omit the `addSettingSections()` method not to set a section. In other words, setting a section became optional.
- Added: the `fields_{instantiated class name}_{section id}` filter that receives registered field definition arrays which belong to the given section.
- Added: the `grid` custom field type.
- Added: the documentation pages in the distribution package.
- Added: an example to add a thumbnail to the taxonomy term listing table in the demo plugin.
- Added: the `cell_{taxonomy slug}` and the `cell_{instantiated class name}` filters for the taxonomy field class.
- Added: the `sortable_columns_{taxonomy slug}` and the `sortable_columns_{instantiated class name}` filters for the taxonomy field class.
- Added: the `columns_{taxonomy slug}` and the `columns_{instantiated class name}` filters for the taxonomy field class.
- Added: the `columns_{post type slug}` filter for the post type class.
- Added: the `sortable_columns_{post type slug}` filter for the post type class.
- Deprecated: ( ***Breaking Change*** ) the `setColumnHeader()` method in the post type class.
- Deprecated: ( ***Breaking Change*** ) the `setSortableColumns()` method in the post type class.
- Deprecated: ( ***Breaking Change*** ) the `addSubMenuLink()` method in the main class.
- Deprecated: ( ***Breaking Change*** ) the `addSubMenuPages()` and the addSubMenuPage() method in the main class.
- Added: the minified version of the library.
- Added: the ability to add fields in the taxonomy definition page.
- Added: the ability to add meta boxes in pages added by the framework.
- Added: the ability to set the target section ID in the `addSettingFields()` method so that the `section_id` key can be omitted for the next passing field arrays.
- Added: the ability to set the target page and tab slugs in the `addSettingSection()` and the `addSettingSections()` methods so that the page and tab slug keys can be omitted for the next passing section arrays.
- Added: the ability to set the target page slug in the `addInPageTabs()` method so that the page slug key can be omitted for the next passing tab arrays.
- Added: the ability to set options for repeatable fields including the maximum number of fields and the minimum number of fields.
- Changed: the all the registered field default values to be saved regardless of the page where the user submits the form.
- Changed: it to store all added sections and fields into the property object regardless of they belong to currently loading page and with some other conditions.
- Added: the ability to sort fields by drag and drop.
- Fixed: a bug that meta box specific styles were not loaded per class basis when multiple meta box class instances were created and they were displayed in the same page; only the first instantiated meta box class's styles were loaded.
- Added: the filters, `style_common_{extended meta box class name}`, `style_ie_common_{extended meta box class name}`, `style_ie_{extended meta box class name}`.
- Added: the ability to set option group in the `select` field type.
- Added: the ability to set tag attributes on field tags on an individual basis in the `select`, `radio`, and `checkbox` field types.
- Added: the ability to set tag attributes with the `attributes` key by passing an array with the key of the attribute name and the value of the property value for input fields.
- Added: the ability to mix field types in sub-fields.
- Added: the `hidden` key to the field definition array of pages that hides the field output.
- Added: the `show_title_column` key to the field definition array of pages.
- Added: the `after_fields` and the `before_fields` keys to the field definition array.
- Changed: ( ***Breaking Change*** ) the structure of field definition arrays.
- Changed: ( ***Breaking Change*** ) dropped the page slug dimensions from the saved option array structure.
- Fixed: a bug that page load info in the footer area was not embedded when multiple root pages are created.
- Moved: the method to retrieve library data into the property base class and they will be stored as static properties.
- Changed: ( ***Breaking Change*** ) the name of the `showInPageTabs()` method to `setInPageTabsVisibility()`.
- Changed: ( ***Breaking Change*** ) the name of the `showPageHeadingTabs()` method to `setPageHeadingTabsVisibility()`.
- Changed: ( ***Breaking Change*** ) the name of the `showPageTitle()` method to `setPageTitleVisibility()`.
- Changed: ( ***Breaking Change*** ) the `foot_{...}` filters to be renamed to `content_bottom_{...}`.
- Changed: ( ***Breaking Change*** ) the `head_{...}` filters to be renamed to `content_top_{...}`.
- Changed: ( ***Breaking Change*** ) the `{instantiated class name}_{page slug}_tabs` filter to be renamed to `tabs_{instantiated class name}_{page slug}`.
- Changed: ( ***Breaking Change*** ) the `{instantiated class name}_pages` filter to be renamed to `pages_{instantiated class name}`.
- Changed: ( ***Breaking Change*** ) the `{instantiated class name}_setting_fields` filter to be renamed to `fields_{instantiated class name}`.
- Changed: ( ***Breaking Change*** ) the `{instantiated class name}_setting_sections` filter to be renamed to `sections_{instantiated class name}`.
- Changed: ( ***Breaking Change*** ) the `{instantiated class name}_field_{field id}` filter to be renamed to `field_{instantiated class name}_{field id}`.
- Changed: ( ***Breaking Change*** ) the `{instantiated class name}_section_{section id}` filter to be renamed to `section_head_{instantiated class name}_{section id}`.
- Changed: the scope of all the methods intended to be used by the user to `public` from `protected`.
- Changed: all the callback methods to have the prefix of `replyTo`.
- Changed: all the internal methods to have the prefix of an underscore.
- Changed: all the variable names used in the code to apply the Alternative PHP Hungarian Notation.
- Changed: ( ***Breaking Change*** ) the name of the property `oProps` to `oProp`.
- Changed: ( ***Breaking Change*** ) the name of the class `AdminPageFramework_CustomFieldType` to `AdminPageFramework_FieldType`.
- Changed: some of the class names used internally.
- Changed: ( ***Breaking Change*** ) apart from the conversion to the lower case, renamed some of the keys of the field definition array and the section field definition array.
- Changed: ( ***Breaking Change*** ) all the names of array keys with which the user may interact to consist of lower case characters and underscores.

#### 2.1.7.2 - 01/18/2014 
- Fixed: a bug that the `for` attribute of the `label` tag was not updated in repeatable fields.
- Fixed: the warning: `Strict standards: Declaration of ... should be compatible with ...`.

#### 2.1.7.1 - 12/25/2013 
- Added: an example of basic usage of creating a page group as well as specifying a dashicon.
- Added: the ability for the `setRootMenuPage()` method to accept `dashicons`, the `none` value, and SVG base64 encoded icon for the second parameter.
- Fixed: a bug that the `color` field type was replaced with the `taxonomy` field type and the `taxonomy` field type was not available.

#### 2.1.7 - 12/23/2013 
- Fixed a bug that the screen icon could not be retrieved when the `strScreenIcon` key was not set (started to occur around v2.1.6).
- Added: the `import_mime_types_{...}` filter that receives the array holding allowed MIME types so that the user can add custom MIME types for the imported files.
- Added: the `enqueueScript()` and the `enqueueStyle()` methods for the post type class.
- Added: the ability to automatically insert page load information in the admin footer if the `WPDEBUG` constant is true.
- Fixed: a bug that the `password` field type could not be defined as of v2.1.6.

= 2.1.6 - 12/14/2013 = 
- Fixed: a bug that the focus of a drop-down list of the `size` field type got stolen when the user tries to select a unit.
- Added: another example to define custom field types in the demo plugin.
- Changed: the built-in field types to be declared before loading any custom field types.
- Added: a sample custom field type, `font`, in the demo plugin.
- Fixed: the `logArray()` method to use the site local time.
- Added: a sample page to view the message object's properties in the demo plugin.
- Fixed: all the individual messages to be in the message object so that it gives easier access to for the user to modify the framework's default messages.
- Added: a sample custom field type, `dial`, in the demo plugin.
- Added: sample custom field types, `date`, `time`, and `date_time` in the demo plugin.
- Added: additional input fields to the custom `geometry` field type to retrieve the location name and the elevation.
- Removed: ( ***Breaking Change*** ) the `date` field type.
- Added: the ability to set an icon with a file path for the `setRootMenuPage()`, `addSubMenuPage()`, and `getStylesForPostTypeScreenIcon()` methods.

#### 2.1.5 - 12/08/2013 
- Changed: ( *Minor Breaking Change* ) the format of the `id` and `for` attributes of the input and label tags of the `taxonomy` field type.
- Fixed: a bug that caused name collisions with the `for` attribute of label tags in the `taxonomy` field type.
- Added: the `field_{instantiated class name}_{field id}` and `section_{instantiated class name}_{section id}` filters. 
- Added: the `export_{instantiated class name}_{field id}`, `export_{instantiated class name}_{input id}` filters.
- Added: the `import_{instantiated class name}_{field id}`, `import_{instantiated class name}_{input id}` filters.
- Added: an example to retrieve the saved options from the front end in the demo plugin.
- Added: the ability for the `enqueueScript()` and `enqueueStyle()` methods to accept absolute file paths.
- Introduced: a new class `AdminPageFramework_CustomFieldType`.
- Added: a sample custom field type, `geometry`, in the demo plugin.
- Fixed: a bug that the `enqueueScripts()` method caused infinite loops.
- Added: the `field_types_{instantiated class name}` filter that receives the field type defining array so that the user can return custom field types by adding a definition array to it.
- Added: the `vClassAttributeUpload` key for the `import` field type that defines the class attribute of the custom file input tag in the field output.
- Added: the `vUnitSize` key for the `size` field type that indicates the `size` attribute of the select(unit) input field.</li>
- Added: the `vMerge` key for the `import` field type that determines whether the imported data should be merged with the existing options.
- Changed: admin settings notifications with `setSettingNotice()` not to have multiple messages with the same id.
- Added: the `validation_{instantiated class name}_{field id}` and the `validation_{instantiated class name}_{input id}` filters. 
- Fixed: a bug in the demo plugin that the `size` fields were not displayed properly.
- Fixed: a bug that menu positions could not be set with the `setRootMenuPage()` method.

#### 2.1.4 - 11/24/2013 
- Changed: the output of each field to have enclosing `fieldset` tag to be compatible with WordPress v3.8.
- Changed: ( *Minor Breaking Change* ) the default value of all the `vDelimiter` key to be an empty string as some input types' default values were `<br />`.
- Changed: ( *Minor Breaking Change* ) the structure of input field elements to enclose input elements in the `label` tag to make it compatible with the WordPress v3.8 admin style. Accordingly, those who are using the `vBeforeInputTag` and the `vAfterinputTag` keys should make sure that block elements are not passed to those outputs.
- Fixed: a bug that enqueuing multiple scripts/styles with the `enqueueStyle()`/`enqueueScript()` method did not take effect.
- Changed: some menu item labels in the demo plugin.
- Added: sample pages that demonstrate the use of hidden pages with the `fShowInMenu` key in the demo plugin.
- Added: the `fShowInMenu` key for the sub-menu page array which will add the ability to hide the page from the sidebar menu.

= 2.1.3 - 11/19/2013 = 
- Fixed: a bug that the style of the Iris color picker had a conflict with the date picker. 
- Added: the `screen_icon` key for the post type argument array that can set the screen icon of the post type pages.
- Added: the `fAllowExternalSource` key for the `image` and `media` field types that enables to set external URLs via the media uploader. 
- Added: the `media` field type.
- Added: the `arrCaptureAttributes` key to save additional attributes of the image selected via the media uploader. 
- Tweaked: the image fields' preview images to have the maximum width of 600px.
- Added: the ability to select multiple image files for repeatable fields.
- Added: the WordPress 3.5 uploader for the image field type.
- Fixed: a bug that an image URL could not be inserted from the `From URL` tab of the image uploader.
- Added: the `fRepeatable` key to the `text`,`textarea`, `image`, `date`, `color`, and `file` field types that make the fields to be repeatable.

#### 2.1.2 - 11/3/2013 
- Added: the 'vRich' key to the `textarea` field type that enables rich text editor.
- Added: the `vReset` key to the `submit` field type that performs resetting options. 
- Added: class electors to the field elements.
- Changed: ( *Minor Breacking Change* ) the `field_description` class selector name to `admin-page-framework-fields-description`.
- Changed: the *assets* folder name to *asset*.
- Added: the `setDisallowedQueryKeys()` method that can define disallowed query keys to be embedded in the links of in-page tabs and page-heading tabs.
- Fixed: a bug that the `settings-updated` query key string was embedded in the links of in-page tabs and page-heading tabs. 
- Changed: the `showPageTitle()`, `showPageHeadingTabs()`, `showInPageTabs()`, `setInPageTabTag()`, and `setPageHeadingTabTag()` methods to be able to use after registering pages. That means it can be used in methods that are triggered after registering pages such as the `do_before_{page slug}` hook.
- Changed: ( *Breacking Change* ) the key name of page property array `fPageHeadingTab` to `fShowPageHeadingTab`.
- Added: the `setAdminNotice()` method which enables the user to add custom admin messages. 
- Changed: the link class for custom post types to use a public property for the link title that appears in the plugin listing table so that the user can change the text.
- Fixed: a bug that the link url automatically inserted in the plugin listing table was not correct when setting a custom root page slug.
- Fixed: a bug that undefined index `typenow` warning occurred when a custom database query with the WP_Query class was performed in the edit.php admin page. 
- Added: the `admin-page-framework-radio-label` and the `admin-page-framework-checkbox-label` class selectors for the elements enclosing radio and checkbox input labels and removed `display:inline-block` from the inline CSS rule of the elements.
- Fixed: an undefined index warning to occur that appears when a non-existent parent tab slug is given to the `strParentTabSlug` in-page tab array element.
- Added: the `getFieldValue()` method which retrieves the stored value in the option properties by specifying the field name. This is helpful when the section name is unknown.
- Added: the `dumpArray()` method for the debug class.
- Added: the `fHideTitleColumn` field key for the meta box class's field array structure. This allows the user to disable the title column in the options table.
- Added: the `addSettingSection()` method that only accepts one section array so that the user can use it in loops to pass multiple items. 
- Added: the `addSettingField()` method that only accepts one field array so that the user can use it in loops to pass multiple items. 
- Added: the `enqueueStyle()` method and the `enqueueScript()` method that enqueue script/style by page/tab slug.
- Changed: the submit field type with the `vRedirect` value not to be redirected when a field error array is set.
- Fixed: a bug that hidden in-page tabs with the `fHide` value could not have associated callbacks such as `validation_{page slug}_{tab slug}`.
- Changed: the `getParentTabSlug()` method to return an empty string if the parent slug has the fHide to be true.
- Fixed: a bug that the redirect submit button did not work with a long page slug.
- Added: the Other Notes section including tips in the demo plugin.
- Added: the `setPageHeadingTabTag()` method that sets the page-heading tab's tag.
- Added: the ability to set visibility of in-page tabs, page-heading tabs, and page title by page slug.

#### 2.1.1 - 10/08/2013 
- Added: the *for* attribute of the *label* tag for checklist input elements so that clicking on the label checks/unchecks the item.
- Added: the *strWidth* and the *strHeight* field array keys for the *taxonomy* field type.
- Deprecated: the *numMaxWidth* and the *numMaxHeight* field array keys for the *taxonomy* field type.
- Changed: the *taxonomy* field type to display the elements in a tabbed box.
- Changed: the post type check list to display post types' labels instead of their slugs.
- Changed: the *vDelimiter* elements to be inserted after the *vAfterInputTag* elements. 
- Changed: the footer text links to have title attributes with script descriptions.
- Removed: the script version number in the footer text link and moved it to be displayed in the title attribute.
- Added: the *getCurrentAdminURL()* method.

#### 2.1.0 - 10/05/2013 
- Added: the *load_{instantiated class name}*, *load_{page slug}* and *load_{page slug}_{tab slug}* filters.
- Fixed: a bug that saving options with a custom capability caused the Cheatin' Uh message.
- Deprecated: ( ***Breaking Change*** ) the *setPageCapability()* method since it did not do anything.
- Changed: ( ***Breaking Change*** ) the *AdminPageFramework_PostType* class properties and *AdminPageFramework_MetaBox* to be encapsulated into a class object each.
- Added: the *strHelp* field key that adds a contextual help tab on the upper part of the admin page.
- Fixed: the required WordPress version to 3.3 as some of the functionalities were relying on the screen object that has been implemented since WordPress 3.3.

#### 2.0.2 - 09/07/2013 
- Fixed: a bug in the demo plugin that custom taxonomies were not added.
- Added: the *size* field type.

#### 2.0.1 - 09/04/2013 
- Fixed: a bug that admin setting notices were displayed twice in the options-general.php page.

#### 2.0.0 - 08/28/2013 
- Released 2.0.0.

#### 2.0.0.b4 - 08/28/2013 
- Fixed: a bug that custom post type preview page did not show the stored values in the demo plugin.
- Refactored: the code that loads the color picker script.
- Refactored: the code that loads the image selector script.
- Refactored: the code that loads framework's style.

#### 2.0.0.b3 - 08/28/2013 
- Added: more documentation in the source code.
- Removed: the *document* folder.
- Moved: the *documentation_guideline.md* file to the top level folder.
- Removed: the documentation pages and added an external link to the documentation web site.
- Removed: the *arrField* parameter of the constructor of the *AdminPageFramework_MetaBox* class.
- Removed: the *setFieldArray()* method of the *AdminPageFramework_MetaBox* class.
- Fixed: a bug that meta box color piker, image selector, data picker scripts did not load in the page after the Publish button was pressed.
- Changed: the *validation_ instantiated class name* filter for meta boxes to accept the second parameter to receive the stored data.

#### 2.0.0.b2 - 08/26/2013 
- Changed: *addLinkToPluginDescription()* and *addLinkToPluginTitle()* to accept variadic parameters. 
- Added: an example of using *addLinkToPluginDescription()* and *addLinkToPluginTitle()* in the demo plugin.
- Changed: the demo plugins file name.
- Fixed: an issue that date picker script caused an irregular element to be inserted around the page footer.
- Changed: the documentation compatible with the DocBlock syntax. 

#### 2.0.0.b1 - 08/24/13 
- Changed: the *setSettingsNotice()* method name to *setSettingNotice()* to be consistent with other names with *Settings*.
- Added: the *date* input field that adds a date picker.
- Added: the ability to specify the multiple attribute to the select field with the *vMultiple* key.
- Added: the *color* input field that adds a color picker.

#### 1.1.0 - 2013/07/13 
- Added: the *addSubMenuItems()* and *addSubMenuItem()* methods that enables to add not only sub menu pages but also external links.
- Added: the ability to list the terms of specified taxonomy with checkbox by taxonomy slug.
- Changed: ( *Breaking Change* ) the *category* field type to *taxonomy* field type.
- Fixed: a bug that adding sub pages to an existing custom post type page caused the links of in-page tabs to have the wrong urls.
- Changed: the *image* field type to be a custom text field.
- Added: the *import_format_{page slug}_{tab slug}*, *import_format_{page slug}*, *import_format_{instantiated class name}* filters to allow to modify the import format type.
- Added: the *import_option_key_{page slug}_{tab slug}*, *import_option_key_{page slug}*, *import_option_key_{instantiated class name}* filters to allow to modify the import option key.
- Added: the *export_format_{page slug}_{tab slug}*, *export_format_{page slug}*, *export_fomat_{instantiated class name}* filters to allow to modify the export format type.
- Added: the *export_name_{page slug}_{tab slug}*, *export_name_{page slug}*, *export_name_{instantiated class name}* filters to allow to modify the export file name.
- Added: the ability to set the *accept* attribute for the *file* input field.
- Added: ( *Breaking Change* ) the second parameter to the validation callback method to pass the old stored option data.
- Changed: ( *Breaking Change* ) the validation behaviour to maintain the stored option values to return the second parameter value in the validation callback method from returning an empty array.
- Changed: ( *Breaking Change* ) the validation behaviour to delete the stored option values to return an empty array in the validation callback method from returning a null value.
- Added: the *validation_{instantiated class name}* filter that allows to modify the submitted form data throughout the whole script.
- Added: the ability to set the text domain for the text messages that the framework uses.
- Added: the ability to set the minimum width for label tags for *textarea*, *text*, and *number* input fields.
- Added: the ability to set the label tag for *textarea*, *text*, and *number* input fields.
- Added: the *{instantiated class name}_field_{field id}* filter to allow to modify settings field output.
- Added: the *{instantiated class name}_{page slug}_tabs* filter to allow to modify adding in-page tabs.
- Added: the *{instantiated_class name}_pages* filter to allow to modify adding pages.
- Added: the *{instantiated class name}_setting_fields* and *{instantiated class name}_setting_sections* filters to allow to modify registering sections and fields.
- Changed: ( *Breaking Change* ) the default option key that is stored in the option database table to be the instantiated class name from the page slug.
- Changed: ( *Breaking Change* ) the section and field filters to have the prefix of the instantiated class name of the Admin Page Framework so that it prevents conflicts with other plugins that uses the framework.
- Changed: the anchor link *name* attribute to *id*.
- Added: the ability to order the in-page tabs with the *numOrder* key.
- Added: the *addInPageTab()* methods to set in-page tabs.
- Changed: ( *Breaking Change* ) the array structure of the parameter of the *addInPageTabs()* methods.
- Added: the ability to automatically assign the default screen icon if not set, which is of the **generic** id.
- Added: the ability to set the WordPress built-in screen icon to the custom added sub-menu pages.
- Added: a class for handling custom-post types.
- Added: a class for handling meta-boxes.
- Changed: ( *Breaking Change* ) to apply the camel-back notation to all the array argument keys.
- Changed: ( *Breaking Change* ) all the method names to be uncapitalised. 
- Changed: ( *Breaking Change* ) the sub-string of class names, Admin_Page_Framework, to AdminPageFramework.

#### 1.0.4.2 - 07/01/2013 
- Tweaked: the demo plugin to load the admin-page object only in the administration pages with the is_admin() function.
- Fixed: a bug that setting and retrieving a transient for the field error array caused extra database queries.
- Fixed: a bug that setting multiple checkboxes caused undefined index warning. 
- Fixed: a bug in the demo plugin that single upload field did not appear and caused undefined index warning after updating the options.

#### 1.0.4.1 - 04/14/2013 
- Added: the *if* key for section and field array that evaluates the passed expression to evaluate whether the section or field should be displayed or not.
- Added: the support of the *label* key for the *text* input field and multiple elements to be passed as array.
- Fixed: a bug that the disable field key for the check box type did not take effects when multiple elements were passed as array.

#### 1.0.4 - 04/07/2013 
- Fixed: an issue that the submit field type with the redirect key caused an unset index warning.
- Changed: not to use the get_plugin_data() function if it does not exist to support those who change the location of the wp-admin directory.
- Added: enclosed the checkbox, radio fields and its label in a tag with the *display:inline-block;* property so that each item do not wrap in the middle.
- Added: the *SetSettingsNotice()* method which can be used instead of the *AddSettingsError()* method. The new method does not require an ID to be passed.
- Changed: **(Breaking Change)** the parameters of *SetFieldErrors()* method; the first parameter is now the error array and the second parameter is the ID and it is optional.
- Changed: that when multiple labels were set for the field types that supports multiple labels but the *name* key was set null, it now returns the default value instead of an empty string.
- Tweaked: the settings registration process including sections and fields to be skipped if the loading page is not one of the pages added by the user.
- Improved: the accuracy to retrieve the caller script information.
- Added: the *posttype* field type.
- Added: the *category* field type.

#### 1.0.3.3 - 04/02/2013 
- Fixed: a bug that a debug log file was created after submitting form data introduced in 1.0.3.2.

#### 1.0.3.2 - 04/02/2013 
- Added: the *redirect* field key for the submit input type that redirects the page after the submitted form data is successfully saved.
- Fixed: an issue that when there are multiple submit input fields and the same label was used with the *href* key, the last url was set to previous buttons; the previous buttons urls were overwritten by the last one. 
- Fixed: a bug that a value for the *pre_field* was applied to the *post_field* key in some field types.
- Added: the ability to disable Settings API's admin notices to be automatically displayed after submitting a form by default. To enable the Settings API's notification messages, use the EnableSettingsAPIAdminNotice() method.

#### 1.0.3.1 - 04/01/2013 
- Added: the default message which appears when the settings are saved.
- Changed: to automatically insert plugin information into the plugin footer regardless of whether the second parameter of the constructor is set or not.

#### 1.0.3 - 04/01/2013 
- Added: the *href* field key for the submit field type that makes the button serve like a hyper link.
- Added: the SetFieldErrors() method that enables to set field errors easily without dealing with transients.
- Added: the *AddSettingsError()* and the *ShowSettingsErrors()* methods to be alternated with the settings_errors() and the add_settings_error() functions to prevent multiple duplicate messages to be displayed.
- Added: the ability to automatically insert anchor links to each section and field of form elements.
- Added: the *readonly* field key for text and textarea input fields that inserts the readonly attribute to the input tag.
- Added: the *pre_field* and *post_field* field keys that adds HTML code right before/after the input element.
- Fixed: a minor bug in the method that merges arrays that did not merge correctly with keys with a null value.

#### 1.0.2.3 - 03/17/2013 
- Added: the ability to set access rights ( capability ) to adding pages individually, which can be set in the newly added fourth parameter of the AddSubMenu() method.

#### 1.0.2.2 - 03/17/2013 
- Changed: (**Breaking Change**) the second parameter of the constructor from capability to script path; the capability can be set via the SetCapability() method.
- Added: the ability to automatically insert script information ( plugin/theme name, version, and author ) into the footer if the second parameter is set in the constructor.

#### 1.0.2.1 - 03/16/2013 
- Added: the capability key for section and field arrays which sets access rights to the form elements.
- Added: a hidden tab page which belongs to the first page with a link back-and-forth in the demo plugin. 
- Changed: the required WordPress version to 3.2 as the newly used filter option_page_capability_{$pageslug} requires it.
- Fixed: an issue that setting a custom capability caused the "Creatin' huh?" message and the user could not change the options.
- Added: the *HideInPageTab()* method which hides a specified in-page tab yet still accessible by the direct url.
- changed: the method name *RenderInPageTabs()* to *GetInPageTabs()* since it did not print anything but returned the output string. 

#### 1.0.2 - 03/11/2013 
- Added: the *export_{suffix}* and *import_{suffix}* filters and the corresponding callback methods to capture exporting/importing array to modify before processing it.
- Supported: multiple export buttons per page.
- Added: the *delimiter* key which delimits multiple fields passed as array including the field types of checkbox, radio, submit, export, import, and file.
- Fixed: to apply the value of the *disable* key to the *import* and *export* custom field.
- Fixed: a bug that an empty string was applied for the *description* key even when it is not set.
- Added: the transient key for the *export* custom field to set a custom exporting array.
- Added: *do_form* action hooks ( tag, page, global ) which are triggered before rendering the form elements after the form opening tag.
- Fixed: a bug that the *file_name* key for the *export* field key did not take effect.

#### 1.0.1.2 - 03/09/2013 
- Fixed: a typo which caused a page not to be added to the Appearance menu.

#### 1.0.1.1 - 03/08/2013 
- Fixed: typos in the demo plugin.
- Changed: error message for a field to display the field value as well in addition to the specified error message.
- Changed: the post_html key to be inserted after the description key.
- Changed: tip key to use the description key if it is not set.


#### 1.0.1 - 03/05/2013 
- Removed: array_replace_recursive() to support PHP below 5.3 and applied an alternative.
- Changed: to use md5() for the error transient name, class name + page slug, to prevent WordPress from failing to retrieve or save options for the character lengths exceeding 45 characters.
- Changed: to echo the value in a user-defined custom field type.
- Added: the *pre_html* and *post_html* keys for input fields that adds extra HTML code before/after the field input and the description.
- Added: the *value* key for input fields that precedes the option values saved in the database.
- Added: the *disable* key for input fields to add disabled="Disabled".

#### 1.0.0.2 - 02/17/2013 
- Fixed a warning in debug mode, undefined index, selectors.
- Added a brief instruction in the demo plugin code and fixed some inaccurate descriptions.

#### 1.0.0.1 - 02/15/2013 
- Fixed a bug that the options were not properly saved when the forms were created in multiple pages.

#### 1.0.0.0 - 02/14/2013
- Initial Release