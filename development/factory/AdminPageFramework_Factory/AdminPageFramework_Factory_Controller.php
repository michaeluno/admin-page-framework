<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Factory_Controller' ) ) :
/**
 * Provides methods for models.
 * 
 * @abstract
 * @since       3.0.4
 * @package     AdminPageFramework
 * @subpackage  Factory
 */
abstract class AdminPageFramework_Factory_Controller extends AdminPageFramework_Factory_View {
            
    /**
     * Should be extended
     * 
     * @internal
     */
    public function start() {}
    public function setUp() {}
            
            
    /**
     * Head Tag Methods - should be extended.
     * 
     * @remark      the number of arguments depend on the extended class.
     * @internal
     */
    
    /**
     * Enqueues styles of the given sources.
     * 
     * @since       3.0.4       The method itself has existed since v3.0.0 but moved to this factory class.
     * @param       array       The sources of the stylesheet to enqueue: the url, the absolute file path, or the relative path to the root directory of WordPress. Example: `array( '/css/mystyle.css', '/css/mystyle2.css' )`
     * @param       array       (optional) The another source argument array.
     * @return      array       The array holing the queued items.
     * @internal
     */
    public function enqueueStyles( $aSRCs, $_vArg2=null ) {} 
    
    /**
     * Enqueues a style of the given source.
     * 
     * @since       3.0.4       The method itself has existed since v3.0.0 but moved to this factory class.
     * @see         http://codex.wordpress.org/Function_Reference/wp_enqueue_style
     * @param       string      The source of the stylesheet to enqueue: the url, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
     * @param       array       (optional) The argument array for more advanced parameters.
     * <h4>Argument Array</h4>
     * <ul>
     *     <li>**handle_id** - (optional, string) The handle ID of the stylesheet.</li>
     *     <li>**dependencies** - (optional, array) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
     *     <li>**version** - (optional, string) The stylesheet version number.</li>
     *     <li>**media** - (optional, string) the description of the field which is inserted into the after the input field tag.</li>
     *     <li>**attributes** - (optional, array) [3.3.0+] attributes array. `array( 'data-id' => '...' )`</li>
     * </ul>
     * @return      string      The style handle ID. If the passed url is not a valid url string, an empty string will be returned.
     * @internal
     */
    public function enqueueStyle( $sSRC, $_vArg2=null ) {}
    
    /**
     * Enqueues scripts by the given sources.
     * 
     * <h4>Example</h4>
     * <code>
     * $this->enqueueScripts(  
     *     array( 
     *          plugins_url( 'asset/js/test.js' , __FILE__ ), // source url or path
     *          plugins_url( 'asset/js/test2.js' , __FILE__ ),    
     *     )
     * );
     * </code>
     *
     * @since       3.0.4       The method itself has existed since v3.0.0 but moved to this factory class.
     * @param       array       The sources of the stylesheets to enqueue: the URL, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
     * @param       array       (optional) Ad additional source list array.
     * @return      array        The array holding the queued items.
     * @internal
     */    
    public function enqueueScripts( $aSRCs, $_vArg2=null ) {}
    /**
     * Enqueues a script by the given source.
     *  
     * <h4>Example</h4>
     * <code>$this->enqueueScript(  
     *      plugins_url( 'asset/js/test.js' , __FILE__ ), // source url or path
     *      array(
     *          'handle_id'     => 'my_script', // this handle ID also is used as the object name for the translation array below.
     *          'translation'   => array( 
     *              'a'                 => 'hello world!',
     *              'style_handle_id'   => $sStyleHandle, // check the enqueued style handle ID here.
     *          ),
     *      )
     * );</code>
     * 
     * @since       3.0.4       The method itself has existed since v3.0.0 but moved to this factory class.
     * @see         http://codex.wordpress.org/Function_Reference/wp_enqueue_script
     * @param       string      The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
     * @param       array       (optional) The argument array for more advanced parameters.
     * <h4>Argument Array</h4>
     * <ul>
     *     <li>**handle_id** - (optional, string) The handle ID of the script.</li>
     *     <li>**dependencies** - (optional, array) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
     *     <li>**version** - (optional, string) The stylesheet version number.</li>
     *     <li>**translation** - (optional, array) The translation array. The handle ID will be used for the object name.</li>
     *     <li>**in_footer** - (optional, boolean) Whether to enqueue the script before `</head>` or before`</body>` Default: `false`.</li>
     *     <li>**attributes** - (optional, array) [3.3.0+] attributes array. `array( 'data-id' => '...' )`</li>
     * </ul>
     * @return      string      The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     * @internal
     */    
    public function enqueueScript( $sSRC, $_vArg2=null ) {}    
    
    /*
     * Help Pane
     */
    /**
     * Adds the given HTML text to the contextual help pane.
     * 
     * The help tab will be the meta box title and all the added text will be inserted into the content area within the tab.
     * 
     * @example
     * <pre><code>$this->addHelpText( 
     *      __( 'This text will appear in the contextual help pane.', 'your-text-domain' ), 
     *      __( 'This description goes to the sidebar of the help pane.', 'your-text-domain' )
     * );</code></pre>
     * @since       2.1.0
     * @remark      This method just adds the given text into the class property. The actual registration will be performed with the `replyToRegisterHelpTabTextForMetaBox()` method.
     */ 
    public function addHelpText( $sHTMLContent, $sHTMLSidebarContent="" ) {
        if ( method_exists( $this->oHelpPane, '_addHelpText' ) ) {
            $this->oHelpPane->_addHelpText( $sHTMLContent, $sHTMLSidebarContent );
        }
    }

    /**
     * Adds sections.
     * 
     * It inserts the given section definition arrays into the class property and later they are parsed when sections are registered. The section definition array have required keys. Refer to the parameter section of this method.
     * 
     * @example
     * <pre><code>$this->addSettingSections(
     *       array(
     *            'section_id'    => 'text_fields',
     *            'title'         => __( 'Text Fields', 'your-text-domain' ),
     *            'description'   => __( 'These are text type fields.', 'your-text-domain' ),
     *       ),    
     *       array(
     *            'section_id'    => 'selectors',
     *            'title'         => __( 'Selectors', 'your-text-domain' ),
     *            'description'   => __( 'These are selector type options such as dropdown lists, radio buttons, and checkboxes', 'your-text-domain' ),
     *       )
     * );</code></pre>
     *
     * @since       3.0.0     
     * @access      public
     * @remark      Accepts variadic parameters; the number of accepted parameters are not limited to three.
     * @remark      The target section tab slug will be reset once the method returns.
     * @param       array|string     the section array or the target page slug. If the target page slug is set, the next section array can omit the page slug key.
     * <strong>Section Array</strong>
     * <ul>
     *      <li>**section_id** - (string) the section ID. Avoid using non-alphabetic characters except underscore and numbers.</li>
     *      <li>**title** - (optional, string) the title of the section.</li>
     *      <li>**capability** - (optional, string) the <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> of the section. If the page visitor does not have sufficient capability, the section will be invisible to them.</li>
     *      <li>**if** - (optional, boolean) if the passed value is false, the section will not be registered.</li>
     *      <li>**order** - (optional, integer) the order number of the section. The higher the number is, the lower the position it gets.</li>
     *      <li>**help** - (optional, string) the help description added to the contextual help tab.</li>
     *      <li>**help_aside** - (optional, string) the additional help description for the side bar of the contextual help tab.</li>
     * </ul>
     * @param       array       (optional) another section array.
     * @param       array       (optional)  add more section array to the next parameters as many as necessary.
     * @return      void
     */    
    public function addSettingSections( $aSection1, $aSection2=null, $_and_more=null ) {
        
        foreach( func_get_args() as $asSection ) { $this->addSettingSection( $asSection ); }
        
        // Reset the stored target tab slug and the target section tab slug.
        $this->_sTargetSectionTabSlug = null;
        
    }
    
    /**
     * A singular form of the `adSettingSections()` method which takes only a single parameter.
     * 
     * This is useful when adding section arrays in loops.
     * 
     * @since       3.0.0            Changed the scope to public from protected.
     * @access      public
     * @remark      The actual registration will be performed in the `_replyToRegisterSettings()` method with the `admin_menu` hook.
     * @remark      The `$oForm` property should be created in each extended class.
     * @param       array|string     the section array. If a string is passed, it is considered as a target page slug that will be used as a page slug element from the next call so that the element can be omitted.
     * @return      void
     */
    public function addSettingSection( $aSection ) {
        
        if ( ! is_array( $aSection ) ) { return; }
        
        $this->_sTargetSectionTabSlug = isset( $aSection['section_tab_slug'] ) ? $this->oUtil->sanitizeSlug( $aSection['section_tab_slug'] ) : $this->_sTargetSectionTabSlug;    
        $aSection['section_tab_slug'] = $this->_sTargetSectionTabSlug ?  $this->_sTargetSectionTabSlug : null;
                
        $this->oForm->addSection( $aSection );
            
    }     
        
    /**
    * Adds form fields.
    * 
    * It inserts the given field definition arrays into the class property and later they are parsed when fields are registered. The field definition array requires specific keys. Refer to the parameter section of this method.
    * 
    * @since        2.0.0
    * @remark       Accepts variadic parameters; the number of accepted parameters are not limited to three.
    * @param        array     the field definition array.
    * <h4>Built-in Field Types</h4>
    * <ul>
    *       <li>**text** - a normal field to enter text input.</li>
    *       <li>**password** - a masked text input field.</li>
    *       <li>**textarea** - a text input field with multiple lines. It supports rich text editor.</li>
    *       <li>**radio** - a set of radio buttons that lets the user pick an option.</li>
    *       <li>**checkbox** - a check box that lets the user enable/disable an item.</li>
    *       <li>**select** - a drop-down list that lest the user pick one or more item(s) from a list.</li>
    *       <li>**hidden** - a hidden field that will be useful to insert invisible values.</li>
    *       <li>**file** - a file uploader that lets the user upload files.</li>
    *       <li>**image** - a custom text field with the image uploader script that lets the user set the image URL.</li>
    *       <li>**media** - a custom text field with the media uploader script that lets the user set the file URL.</li>
    *       <li>**color** - a custom text field with the color picker script.</li>
    *       <li>**submit** - a submit button that lets the user send the form.</li>
    *       <li>**export** - a custom submit field that lets the user export the stored data.</li>
    *       <li>**import** - a custom combination field of the file and the submit fields that let the user import data.</li>
    *       <li>**posttype** - a check-list of post types enabled on the site.</li>
    *       <li>**taxonomy** - a set of check-lists of taxonomies enabled on the site in a tabbed box.</li>
    *       <li>**size** - a combination field of the text and the select fields that let the user set sizes with a selectable unit.</li>
    *       <li>**section_title** - [3.0.0+] a text field type that will be placed in the section title so that it lets the user set the section title. Note that only one field with this field type is allowed per a section.</li>
    *       <li>**system** - [3.3.0+] a custom textara field that displays the system information including the PHP settings, the framework version, MySQL version etc.</li>
    * </ul>
    * <h4>Field Definition Array</h4>
    * <ul>
    *       <li>**field_id** - ( required, string) the field ID. Avoid using non-alphabetic characters except underscore and numbers.</li>
    *       <li>**type** - ( required, string) the type of the field. The supported types are listed below.</li>
    *       <li>**section_id** - (optional, string) the section ID that the field belongs to. If not set, the internal `_default` section ID will be assigned.</li>
    *       <li>**title** - (optional, string) the title of the section.</li>
    *       <li>**description** - (optional, string) the description of the field which is inserted into the after the input field tag.</li>
    *       <li>**tip** - (optional, string) the tip for the field which is displayed when the mouse is hovered over the field title.</li>
    *       <li>**capability** - (optional, string) the <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> of the section. If the page visitor does not have sufficient capability, the section will be invisible to them.</li>
    *       <li>**error_message** - (optional, string) the error message to display above the input field.</li>
    *       <li>**before_field** - (optional, string) the HTML string to insert before the input field output.</li>
    *       <li>**after_field** - (optional, string) the HTML string to insert after the input field output.</li>
    *       <li>**if** - (optional, boolean) if the passed value is false, the section will not be registered.</li>
    *       <li>**order** - (optional, integer) the order number of the section. The higher the number is, the lower the position it gets.</li>
    *       <li>**label** - (optional, string) the text label(s) associated with and displayed along with the input field. Some input types can ignore this key.</li>
    *       <li>**default** - (optional, string|array) the default value(s) assigned to the input tag's value attribute.</li>
    *       <li>**value** - (optional, string|array) the value(s) assigned to the input tag's `value` attribute to override the default and the stored value.</li>
    *       <li>**delimiter** - (optional, string) the HTML string that delimits multiple elements. This is available if the <var>label</var> key is passed as array. It will be enclosed in inline-block elements so the passed HTML string should not contain block elements.</li>
    *       <li>**before_input** - (optional, string) the HTML string inserted right before the input tag. It will be enclosed in the <code>label</code> tag so the passed HTML string should not contain block elements.</li>
    *       <li>**after_input** - (optional, string) the HTML string inserted right after the input tag. It will be enclosed in the <code>label</code> tag so the passed HTML string should not contain block elements.</li>
    *       <li>**label_min_width** - (optional, string) the inline style property of the `min-width` of the label tag for the field. If the unit is not specified, 'px' is applied. Default: `120`. e.g. `100%`</li> 
    *       <li>**help** - (optional, string) the help description added to the contextual help tab.</li>
    *       <li>**help_aside** - (optional, string) the additional help description for the side bar of the contextual help tab.</li>
    *       <li>**repeatable** - [3.0.0+] (optional, array|boolean) whether the fields should be repeatable. If it yields true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields. Optionally an setting array can be passed.
    *           <h5>Repeatable Fields Setting Array</h5>
    *           <ul>
    *                 <li>**max** - the allowed maximum number of fields to be repeated.</li>
    *                 <li>**min** - the allowed minimum number of fields to be repeated.</li>
    *           </ul>
    *       </li>
    *       <li>**sortable** - [3.0.0+] (optional, boolean) whether the fields should be sortable. If it yields true, the fields will be enclosed in a draggable box.
    *       <li>**attributes** - [3.0.0+] (optional, array) holds key-value pairs representing the attribute and its property. Note that some field types have specific keys in the first dimensions. e.g. `array( 'class' => 'my_custom_class_selector', 'style' => 'background-color:#777', 'size' => 20, )` Every field holds the following nested attribute definition arrays.
    *           <ul>
    *               <li>**fieldrow** - the `td` tag element containing the field output.</li>
    *               <li>**fieldset** - the `fieldset` tag element containing the field output.</li>
    *               <li>**fields** - the `div` tag element containing the sub-fields and the main field.</li>
    *               <li>**field** - the `div` tag element containing each field.</li>
    *           </ul>
    *       </li>
    *       <li>**show_title_column** - [3.0.0+] (optional, boolean) If true, the field title column will be omitted from the output.</li>
    *       <li>**hidden** - [3.0.0+] (optional, boolean) If true, the entire field row output will be invisible with the inline style attribute of `style="display:none"`.</li>
    * </ul>
    * 
    * <h4>Field Type Specific Arguments</h4>
    * <p>Each field type uses specific array arguments.</p>
    * <ul>
    *       <li>**text** - a text input field which allows the user to type text.</li>
    *       <li>**password** - a password input field which allows the user to type text.</li>
    *       <li>**number, range** - HTML5 input field types. Some browsers do not support these.</li>
    *       <li>**textarea** - a textarea input field. The following array keys are supported.
    *           <ul>
    *               <li>**rich** - [2.1.2+] (optional, array) to make it a rich text editor pass a non-empty value. It accept a setting array of the <code>_WP_Editors</code> class defined in the core.
    * For more information, see the argument section of <a href="http://codex.wordpress.org/Function_Reference/wp_editor" target="_blank">this page</a>.
    *               </li>
    *           </ul>
    *       </li>
    *       <li>**radio** - a radio button input field.</li>
    *       <li>**checkbox** - a check box input field.</li>
    *           <ul>
    *               <li>**select_all_button** - [3.3.0+] (optional, array) pass `true` to enable the `Select All` button. To set a custom label, set the text such as `__( 'Check All', 'test-domain' )`. Default: `true`.</li>
    *               <li>**select_none_button** - [3.3.0+] (optional, array) pass `true` to enable the `Select None` button. To set a custom label, set the text such as `__( 'Check All', 'test-domain' )`. Default: `true`.</li>
    *           </ul>
    *       <li>**select** - a drop-down input field.
    *           <ul>
    *               <li>**is_multiple** - (optional, boolean) if this is set to true, the `multiple` attribute will be inserted into the field input tag, which enables the multiple selections for the user.</li>
    *           </ul>
    *       </li>
    *       <li>**size** - a size input field. This is a combination of number and select fields.
    *           <ul>
    *               <li>
    *                   **units** - (optional, array) defines the units to show. e.g. `array( 'px' => 'px', '%' => '%', 'em' => 'em'  )` 
    *                   Default: `array( 'px' => 'px', '%' => '%', 'em' => 'em', 'ex' => 'ex', 'in' => 'in', 'cm' => 'cm', 'mm' => 'mm', 'pt' => 'pt', 'pc' => 'pc' )`
    *               </li>
    *               <li>**is_multiple** - (optional, boolean) if this is set to true, the `multiple` attribute will be inserted into the field input tag, which enables the multiple selections for the user.</li>
    *               <li>**attributes** - [3.0.0+] (optional, array) The attributes array of this field type has four initial keys: size, unit, optgroup, and option and they have a regular attribute array in each.</li>
    *           </ul>
    *       </li>
    *       <li>**hidden** - a hidden input field.</li>
    *       <li>**file** - a file upload input field.</li>
    *       <li>**submit** - a submit button input field.
    *           <ul>
    *               <li>**href** - (optional, string) the url(s) linked to the submit button.</li>
    *               <li>**redirect_url** - (optional, string) the url(s) redirected to after submitting the input form.</li>
    *               <li>**reset** - [2.1.2+] (optional, boolean) the option key to delete. Set 1 for the entire option.</li>
    *               <li>**email** - [3.3.0+] (optional, array) Coming soon...
    *                   <ul>
    *                       <li>**to** - (string|array) the email address to send the email to. For multiple email addressed, set comma delimited items.</li>
    *                       <li>**subject** - (string|array) the email title.</li>
    *                       <li>**message** - (string|array) the email body text.</li>
    *                       <li>**attachments** - (string|array) the file path.</li>
    *                       <li>**name** - (string|array) the sender name.</li>
    *                       <li>**from** - (string|array) the sender email.</li>
    *                       <li>**is_html** - (boolean|array) indicates whether the message should be sent as an html or plain text.</li>
    *                   </ul>
    *               </li>
    *           </ul>
    *       </li>
    *       <li>**import** - an import input field. This is a custom file and submit field.
    *           <ul>
    *               <li>**option_key** - (optional, string) the option table key to save the importing data.</li>
    *               <li>**format** - (optional, string) the import format. json, or array is supported. Default: array</li>
    *               <li>**is_merge** - (optional, boolean) [2.0.5+] determines whether the imported data should be merged with the existing options.</li>
    *           </ul>
    *       </li>
    *       <li>**export** - an export input field. This is a custom submit field.
    *           <ul>
    *               <li>**file_name** - (optional, string) the file name to download.</li>
    *               <li>**format** - (optional, string) the format type. array, json, or text is supported. Default: array.</li>
    *               <li>**data** - (optional, string|array|object ) the data to export.</li>
    *           </ul>
    *       </li>
    *       <li>**image** - an image input field. This is a custom text field with an attached JavaScript script.
    *           <ul>
    *               <li>**show_preview** - (optional, boolean) if this is set to false, the image preview will be disabled.</li>
    *               <li>**attributes_to_store** - [2.1.3+] (optional, array) the array of the attribute names of the image to save. If this is set, the field will be an array with the specified attributes. The supported attributes are, 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', and 'link'. Note that for external URLs, ID will not be captured. e.g. `'attributes_to_store' => array( 'id', 'caption', 'description' )`</li>
    *               <li>**allow_external_source** - [2.1.3+] (optional, boolean) whether external URL can be set via the uploader.</li>
    *               <li>**attributes** - [3.0.0+] (optional, array) The attributes array of this field type has three keys: input, button, and preview and they have a regular attribute array in each.</li>
    *           </ul>
    *       </li>
    *       <li>**media** - [2.1.3+] a media input field. This is a custom text field with an attached JavaScript script.
    *           <ul>
    *               <li>**attributes_to_store** - [2.1.3+] (optional, array) the array of the attribute names of the image to save. If this is set, the field will be an array with the specified attributes. The supported attributes are, 'id', 'caption', and 'description'. Note that for external URLs, ID will not be captured. e.g. `'attributes_to_store' => array( 'id', 'caption', 'description' )`</li>
    *               <li>**allow_external_source** - [2.1.3+] (optional, boolean) whether external URL can be set via the uploader.</li>
    *           </ul>
    *       </li>
    *       <li>**color** - a color picker input field. This is a custom text field with a JavaScript script.</li>
    *       <li>**taxonomy** - a taxonomy check list. This is a set of check boxes listing a specified taxonomy. This does not accept to create multiple fields by passing an array of labels.
    *           <ul>
    *               <li>**taxonomy_slugs** - (optional, array) the taxonomy slug to list.</li>
    *               <li>**max_width** - (optional, string) the inline style property value of `max-width` of this element. Include the unit such as px, %. Default: 100%</li>
    *               <li>**height** - (optional, string) the inline style property value of `height` of this element. Include the unit such as px, %. Default: 250px</li>
    *               <li>**select_all_button** - [3.3.0+] (optional, array) pass `true` to enable the `Select All` button. To set a custom label, set the text such as `__( 'Check All', 'test-domain' )`. Default: `true`.</li>
    *               <li>**select_none_button** - [3.3.0+] (optional, array) pass `true` to enable the `Select None` button. To set a custom label, set the text such as `__( 'Check All', 'test-domain' )`. Default: `true`.</li>
    *           </ul>
    *       </li>
    *       <li>**posttype** - a post-type check list. This is a set of check boxes listing post type slugs.
    *           <ul>
    *               <li>**slugs_to_remove** - (optional, array) the post type slugs not to be listed. e.g.`array( 'revision', 'attachment', 'nav_menu_item' )`</li>
    *               <li>**select_all_button** - [3.3.0+] (optional, array) pass `true` to enable the `Select All` button. To set a custom label, set the text such as `__( 'Check All', 'test-domain' )`. Default: `true`.</li>
    *               <li>**select_none_button** - [3.3.0+] (optional, array) pass `true` to enable the `Select None` button. To set a custom label, set the text such as `__( 'Check All', 'test-domain' )`. Default: `true`.</li>
    *           </ul>
    *       </li>
    * </ul>    
    * @param        array (optional) another field array.
    * @param        array (optional) add more field arrays to the next parameters as many as necessary.
    * @return       void
    */ 
    public function addSettingFields( $aField1, $aField2=null, $_and_more=null ) {
        foreach( func_get_args() as $aField ) $this->addSettingField( $aField );
    }    
        
    /**
     * Adds the given field array items into the field array property.
     * 
     * Identical to the addSettingFields() method except that this method does not accept enumerated parameters. 
     * 
     * <h4>Examples</h4>
     * <code>
     *         $this->addSettingField(
     *             array(
     *                 'field_id'    => 'metabox_text_field',
     *                 'type'        => 'text',
     *                 'title'       => __( 'Text Input', 'admin-page-framework-demo' ),
     *                 'description' => __( 'The description for the field.', 'admin-page-framework-demo' ),
     *                 'help'        => 'This is help text.',
     *                 'help_aside'  => 'This is additional help text which goes to the side bar of the help pane.',
     *             )
     *         );    
     * </code>
     * 
     * @since        2.1.2
     * @since        3.0.0   The scope changed to public to indicate the users will use.
     * @return       void
     * @remark       The $oForm property should be created in each extended class.
     */     
    public function addSettingField( $asField ) {
        if ( method_exists( $this->oForm, 'addField' ) ) {
            $this->oForm->addField( $asField );     
        }
    }
    
    
    /**
     * Sets the field error array. 
     * 
     * This is normally used in validation callback methods when the submitted user's input data have an issue.
     * This method saves the given array in a temporary area (transient) of the options database table.
     * 
     * <h4>Example</h4>
     * <code>
     * public function validation_APF_Demo_verify_text_field_submit( $aNewInput, $aOldOptions ) {
     *
     *      // 1. Set a flag.
     *      $bVerified = true;
     *          
     *      // 2. Prepare an error array. 
     *      $aErrors = array();
     *      
     *      // 3. Check if the submitted value meets your criteria.
     *      if ( ! is_numeric( $aNewInput['verify_text_field'] ) ) {
     *          $aErrors['verify_text_field'] = __( 'The value must be numeric:', 'admin-page-framework-demo' ) 
     *              . $aNewInput['verify_text_field'];
     *          $bVerified = false;
     *      }
     *    
     *      // 4. An invalid value is found.
     *      if ( ! $bVerified ) {
     *  
     *          // 4-1. Set the error array for the input fields.
     *          $this->setFieldErrors( $aErrors );     
     *          $this->setSettingNotice( 'There was an error in your input.' );
     *          return $aOldOptions;
     * 
     *      }
     *     
     *      return $aNewInput;     
     *
     * }
     * </code>
     * 
     * @since   3.0.4     
     * @param   array   $aErrors     the field error array. The structure should follow the one contained in the submitted `$_POST` array.
     */ 
    public function setFieldErrors( $aErrors ) {
        
        // The field-errors array will be stored in this global array element.
        $GLOBALS['aAdminPageFramework']['aFieldErrors'] = isset( $GLOBALS['aAdminPageFramework']['aFieldErrors'] ) ? $GLOBALS['aAdminPageFramework']['aFieldErrors'] : array();
        if ( empty( $GLOBALS['aAdminPageFramework']['aFieldErrors'] ) ) {
            add_action( 'shutdown', array( $this, '_replyToSaveFieldErrors' ) ); // the method is defined in the controller class.
        }
        
        $_sID = md5( $this->oProp->sClassName );
        $GLOBALS['aAdminPageFramework']['aFieldErrors'][ $_sID ] = isset( $GLOBALS['aAdminPageFramework']['aFieldErrors'][ $_sID ] )
            ? $this->oUtil->uniteArrays( $GLOBALS['aAdminPageFramework']['aFieldErrors'][ $_sID ], $aErrors )
            : $aErrors;
    
    }   
    
    /**
     * Check whether a user has set a field error(s) or not.
     * 
     * @since       3.3.0
     * @return      boolean     Whether or not a field error exists or not.
     */
    public function hasFieldError() {
        return isset( $GLOBALS['aAdminPageFramework']['aFieldErrors'][ md5( $this->oProp->sClassName ) ] );
    }
    
    /**
    * Sets the given message to be displayed in the next page load. 
    * 
    * This is used to inform users about the submitted input data, such as "Updated successfully." or "Problem occurred." etc. and normally used in validation callback methods.
    * 
    * <h4>Example</h4>
    * <code>if ( ! $bVerified ) {
    *       $this->setFieldErrors( $aErrors );     
    *       $this->setSettingNotice( 'There was an error in your input.' );
    *       return $aOldPageOptions;
    * }</code>
    *
    * @since        3.0.4     
    * @access       public
    * @param        string      $sMessage       the text message to be displayed.
    * @param        string      $sType          (optional) the type of the message, either "error" or "updated"  is used.
    * @param        array       $asAttributes   (optional) the tag attribute array applied to the message container HTML element. If a string is given, it is used as the ID attribute value.
    * @param        boolean     $bOverride      (optional) false: do not override when there is a message of the same id. true: override the previous one.
    * @return       void
    */     
    public function setSettingNotice( $sMessage, $sType='error', $asAttributes=array(), $bOverride=true ) {
        
        // The framework user set notification messages will be stored in this global array element.
        $GLOBALS['aAdminPageFramework']['aNotices'] = isset( $GLOBALS['aAdminPageFramework']['aNotices'] ) ? $GLOBALS['aAdminPageFramework']['aNotices'] : array();
        
        // If the array is empty, save the array at shutdown.
        if ( empty( $GLOBALS['aAdminPageFramework']['aNotices'] ) ) {
            add_action( 'shutdown', array( $this, '_replyToSaveNotices' ) ); // the method is defined in the model class.
        }
        
        // Set up local variables
        $_sID = md5( trim( $sMessage ) );
            
        // If the override options is true, or if the message is set,
        if ( $bOverride || ! isset( $GLOBALS['aAdminPageFramework']['aNotices'][ $_sID ] )  ) {     
            
            $_aAttributes = is_array( $asAttributes ) ? $asAttributes : array();
            if ( is_string( $asAttributes ) && ! empty( $asAttributes ) ) {
                $_aAttributes['id'] = $asAttributes;
            }
            $GLOBALS['aAdminPageFramework']['aNotices'][ $_sID ] = array(
                'sMessage'      => $sMessage,
                'aAttributes'   => $_aAttributes + array(
                        'class'     => $sType,
                        'id'        => $this->oProp->sClassName . '_' . $_sID,
                    ),
            );
        }
                            
    }
    
    /**
     * Checks if an error settings notice has been set.
     * 
     * This is used in the internal validation callback method to decide whether the system error or update notice should be added or not.
     * If this method yields true, the framework discards the system message and displays the user set notification message.
     * 
     * @since       3.1.0
     * @param       string      $sType If empty, the method will check if a message exists in all types. Otherwise, it checks the existence of a message of the specified type.
     * @return      boolean     True if a setting notice is set; otherwise, false.
     */
    public function hasSettingNotice( $sType='' ) {
        
        // The framework user set notification messages are stored in this global array element.
        $_aNotices = isset( $GLOBALS['aAdminPageFramework']['aNotices'] ) ? $GLOBALS['aAdminPageFramework']['aNotices'] : array();
        if ( ! $sType ) {
            return count( $_aNotices ) ? true : false;
        }
        
        // Check if there is a message of the type.
        foreach( $_aNotices as $aNotice ) {
            if ( ! isset( $aNotice['aAttributes']['class'] ) ) {
                continue;
            }
            if ( $aNotice['aAttributes']['class'] == $sType ) {
                return true;
            }
        }
        return false;
        
    }

}
endif;