<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for models.
 * 
 * @abstract
 * @since       3.0.4
 * @package     AdminPageFramework
 * @subpackage  Common/Factory
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
     * Allows the user to check if the current page belongs to the admin pages of the factory component.
     * 
     * @since       3.7.9
     * @return      boolean
     */
    public function isInThePage() {
        return $this->_isInThePage();
    }
         
    /**
     * Sets a system message.
     * 
     * This can be used to replace the framework system messages such as "Allowed maximum number of fields is ...".
     * 
     * @return      void
     * @since       3.7.0
     */
    public function setMessage( $sKey, $sMessage ) {
        $this->oMsg->set( $sKey, $sMessage );
    }
    
    /**
     * Returns the registered system message item(s).
     * 
     * @return      array|string
     * @since       3.7.0
     */
    public function getMessage( $sKey='' ) {
        return $this->oMsg->get( $sKey );
    }
       
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
     * @param       array       The sources of the style-sheets to enqueue: the URL, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
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
     * <h3>Example</h3>
     * <code>$this->addSettingSections(
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
     * );</code>
     * @since       3.0.0     
     * @since       3.5.3       Removed the parameter declarations as they are caught with func_get_args().
     * @access      public
     * @remark      Accepts variadic parameters; the number of accepted parameters are not limited to three.
     * @remark      The target section tab slug will be reset once the method returns.
     * @param       array     a section definition array.
     * @param       array     (optional) another section array.
     * @param       array     (optional)  add more section array to the next parameters as many as necessary.
     * @return      void
     */    
    public function addSettingSections( /* $aSection1, $aSection2=null, $_and_more=null */ ) {
        
        foreach( func_get_args() as $_asSectionset ) { 
            $this->addSettingSection( $_asSectionset ); 
        }
        
        // Reset the stored target tab slug and the target section tab slug.
        $this->_sTargetSectionTabSlug = null;
        
    }
    
    /**
     * A singular form of the `adSettingSections()` method which takes only a single parameter.
     * 
     * This is useful when adding section arrays in loops.
     * 
     * @since       3.0.0               Changed the scope to public from protected.
     * @access      public
     * @remark      The actual registration will be performed in the `_replyToRegisterSettings()` method with the `admin_menu` hook.
     * @remark      The `$oForm` property should be created in each extended class.
     * @param       array|string        $aSection       the section array. If a string is passed, it is considered as a target page slug that will be used as a page slug element from the next call so that the element can be omitted.
     * <h4>Section Definition Array</h4>
     * <ul>
     *      <li>**section_id** - (string) the section ID. Avoid using non-alphabetic characters except underscore and numbers.</li>
     *      <li>**title** - (optional, string) the title of the section.</li>
     *      <li>**capability** - (optional, string) the <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> of the section. If the page visitor does not have sufficient capability, the section will be invisible to them.</li>
     *      <li>**if** - (optional, boolean) if the passed value is false, the section will not be registered.</li>
     *      <li>**order** - (optional, integer) the order number of the section. The higher the number is, the lower the position it gets.</li>
     *      <li>**help** - (optional, string) the help description added to the contextual help tab.</li>
     *      <li>**help_aside** - (optional, string) the additional help description for the side bar of the contextual help tab.</li>
     *      <li>**section_tab_slug** - (optional, string) the unique section tab slug to display the section in a tabbed container with other sections that have the same section tab slug.</li>
     *      <li>**hidden** - (optional, boolean) [3.3.1+] whether or not the section should be hidden. Default: <code>false</code>.</li>
     *      <li>**attributes** - (optional, string) [3.3.1+] An attribute array that applies to the section container element. e.g. `array( 'data-custom_data' => 'my_custom_data' )` The following sub-elements are supported.
     *          <ul>
     *              <li>**tab** - (optional, array) An sub-attribute array that applies to the section tab `<li>` tag element.</li>
     *          </ul>
     *      </li>
     *      <li>**class** - (optional, array) [3.3.1+] class selector(s) appended to the section container element. The following sub-elements are supported.
     *          <ul>
     *              <li>**tab** - (optional, array) An sub-class array that applies to the section tab `<li>` tag element.</li>
     *          </ul>
     *      </li>
     *      <li>**collapsible** - (optional, array) [3.4.0+] class selector(s) appended to the section container element. The following sub-elements are supported.
     *          <ul>
     *              <li>**title** - (optional, string) the section title will be assigned by default in the section formatting method.</li>
     *              <li>**is_collapsed** - (optional, boolean) whether it is already collapsed or expanded.</li>
     *              <li>**toggle_all_button** - (optional, boolean|string|array) the position of where to display the toggle-all button that toggles the folding state of all collapsible sections. Accepts the following values. 'top-right', 'top-left', 'bottom-right', 'bottom-left'. If true is passed, the default 'top-right' will be used. To not to display, do not set any or pass `false` or `null`.</li>
     *              <li>**collapse_others_on_expand** - (optional, boolean) whether the other collapsible sections should be folded when the section is unfolded. If the below `container` argument is set to `section`, this argument value does not take effect.</li>
     *              <li>**container** - (optional, string) the container element that collapsible styling gets applied to. Either 'sections' or 'section' is accepted. Use 'section' for repeatable sections.</li>
     *              <li>**type** - [3.7.0+] (optional, string) either `box` or `button`. Default: `box`. The `button` type only is supported when the `container` argument is set to `section`.</li>
     *          </ul>
     * `
     * $this->addSettingSections(
     *      array(
     *          'section_id'        => 'collapsible_repeatable_section',
     *          'title'             => __( 'Collapsible Repeatable Section', 'admin-page-framework-demo' ),
     *          'collapsible'       => array(
     *              'toggle_all_button' => array( 'top-left', 'bottom-left' ),
     *              'container'         => 'section',
     *          ),
     *          'repeatable'        => true,
     *      )
     *  );
     * `
     *      </li>
     *      <li>**sortable** - (optional, boolean) [3.6.0+] whether the section is sortable or not. In order for this option to be effective, the `repeatable` argument must be enabled.</li>
     *      <li>**content** - (optional, string) [3.6.1+] a custom section output.</li>
     *      <li>**tip** - (optional, string) [3.7.0+] a tool tip which pops up when the user hovers their mouse over the ? mark icon beside the title..</li>
     * </ul>
     * @return      void
     */
    public function addSettingSection( $aSectionset ) {
        
        if ( ! is_array( $aSectionset ) ) { 
            return; 
        }
        
        $this->_sTargetSectionTabSlug = $this->oUtil->getElement(
            $aSectionset,
            'section_tab_slug',
            $this->_sTargetSectionTabSlug
        );
        $aSectionset[ 'section_tab_slug' ] = $this->oUtil->getAOrB(
            $this->_sTargetSectionTabSlug,
            $this->_sTargetSectionTabSlug,
            null
        );
                
        $this->oForm->addSection( $aSectionset );
            
    }     
        
    /**
    * Adds form fields.
    * 
    * It inserts the given field definition arrays into the class property and later they are parsed when fields are registered. The field definition array requires specific keys. Refer to the parameter section of this method.
    * 
    * @since        2.0.0
    * @since        3.5.3       Removed the parameter declarations as they are caught with the func_get_args().
    * @remark       Accepts variadic parameters; the number of accepted parameters are not limited to three.
    * @param        array       the field definition array.
    * @param        array       (optional) another field array.
    * @param        array       (optional) add more field arrays to the next parameters as many as necessary.
    * @return       void
    */ 
    public function addSettingFields( /* $aField1, $aField2=null, $_and_more=null */ ) {
        foreach( func_get_args() as $_aFieldset ) { 
            $this->addSettingField( $_aFieldset ); 
        }
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
     * @param        array|string       $asField        A field definition array or a string of the target section id.
    * <h4>Built-in Field Types</h4>
    * <ul>
    *       <li>[text](./class-AdminPageFramework_FieldType_text.html) - a normal field to enter short text input.</li>
    *       <li>[password](./class-AdminPageFramework_FieldType_text.html) - a masked text input field.</li>
    *       <li>[textarea](./class-AdminPageFramework_FieldType_textarea.html) - a text input field with multiple lines. It supports rich text editors.</li>
    *       <li>[radio](./class-AdminPageFramework_FieldType_radio.html) - a set of radio buttons that lets the user pick an option.</li>
    *       <li>[checkbox](./class-AdminPageFramework_FieldType_checkbox.html) - a check box that lets the user enable/disable an item.</li>
    *       <li>[select](./class-AdminPageFramework_FieldType_select.html) - a drop-down list that lest the user pick one or more item(s) from a list.</li>
    *       <li>[hidden](./class-AdminPageFramework_FieldType_hidden.html) - a field with invisible input values.</li>
    *       <li>[file](./class-AdminPageFramework_FieldType_file.html) - a file uploader that lets the user upload files.</li>
    *       <li>[image](./class-AdminPageFramework_FieldType_image.html) - a text field with an image uploader that lets the user set the image URL.</li>
    *       <li>[media](./class-AdminPageFramework_FieldType_media.html) - a text field with a media uploader that lets the user set the file URL.</li>
    *       <li>[color](./class-AdminPageFramework_FieldType_color.html) - a text field with a color picker.</li>
    *       <li>[submit](./class-AdminPageFramework_FieldType_submit.html) - a submit button that lets the user send the form.</li>
    *       <li>[export](./class-AdminPageFramework_FieldType_export.html) - a custom submit field that lets the user export stored data.</li>
    *       <li>[import](./class-AdminPageFramework_FieldType_import.html) - a custom combination field of file and submit fields that let the user import data.</li>
    *       <li>[posttype](./class-AdminPageFramework_FieldType_posttype.html) - a check-list of post types enabled on the site.</li>
    *       <li>[taxonomy](./class-AdminPageFramework_FieldType_taxonomy.html) - a set of check-lists of taxonomies enabled on the site in a tabbed box.</li>
    *       <li>[size](./class-AdminPageFramework_FieldType_size.html) - a combination field of the text and the select fields that let the user set sizes with a selectable unit.</li>
    *       <li>[section_title](./class-AdminPageFramework_FieldType_section_title.html) - [3.0.0+] a text field type that will be placed in the section title so that it lets the user set the section title. Note that only one field with this field type is allowed per a section.</li>
    *       <li>[system](./class-AdminPageFramework_FieldType_system.html) - [3.3.0+] a custom text area field that displays the system information including the PHP settings, the framework version, MySQL version etc.</li>
    *       <li>[inline_mixed](./class-AdminPageFramework_FieldType_inline_mixed.html) - [3.8.0+] a field that include inner fields with different field types. </li>
    * </ul>
    * <h4>Field Definition Array</h4>
    * <ul>
    *       <li>**field_id** - (required, string) the field ID. Avoid using non-alphabetic characters except underscore and numbers.</li>
    *       <li>**type** - (optional, string) the type of the field. The supported types are listed below. When creating nested fields, this argument can be omitted.</li>
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
    *       <li>**save** - [3.6.0+] (optional, boolean) If `false`, the field value will not be saved. Default: `true`</li>
    *       <li>**content** - (optional, string|array) [3.6.1+] a custom section output. [3.8.0+] Supports an array to be passed for nested and inline-mixed fields. If an array holding field definitions is set, those fields will be nested.
    *           <h4>Example</h4>
    * <pre><code>
    *   $this->addSettingFields(
    *       'my_section_id', // the target section ID - pass dimensional keys of the section
    *       array(
    *           'field_id'      => 'Y',
    *           'title'         => __( 'Y', 'admin-page-framework-loader' ),
    *           'description'   => __( 'By passing an array of field definition to the <code>content</code> argument, you can nest fields.', 'admin-page-framework-loader' )
    *               . ' ' . __( 'Also the <code>type</code> argument can be omitted.', 'admin-page-framework-loader' ),
    *           'content'       => array(
    *               array(
    *                   'field_id'      => 'i',
    *                   'title'         => __( 'i', 'admin-page-framework-loader' ),                    
    *                   'type'          => 'textarea',
    *               ),
    *               array(
    *                   'field_id'      => 'ii',
    *                   'title'         => __( 'ii', 'admin-page-framework-loader' ),                    
    *                   'type'          => 'color',                    
    *               ),
    *               array(
    *                   'field_id'      => 'iii',
    *                   'title'         => __( 'iii', 'admin-page-framework-loader' ),
    *                   'repeatable'    => true,
    *                   'sortable'      => true,
    *                   'content'       => array(
    *                       array(
    *                           'field_id'      => 'a',
    *                           'title'         => __( 'a', 'admin-page-framework-loader' ),                    
    *                           'type'          => 'image',
    *                           'attributes'    => array(
    *                               'preview' => array(
    *                                   'style' => 'max-width: 200px;',
    *                               ),
    *                           ),                                
    *                       ),
    *                       array(
    *                           'field_id'      => 'b',
    *                           'title'         => __( 'b', 'admin-page-framework-loader' ),
    *                           'content'       => array(
    *                               array(
    *                                   'field_id'      => 'first',
    *                                   'title'         => __( '1st', 'admin-page-framework-loader' ),                    
    *                                   'type'          => 'color',
    *                                   'repeatable'    => true,
    *                                   'sortable'      => true,
    *                               ),                                
    *                               array(
    *                                   'field_id'      => 'second',
    *                                   'title'         => __( '2nd', 'admin-page-framework-loader' ),                    
    *                                   'type'          => 'size',
    *                               ),
    *                               array(
    *                                   'field_id'      => 'third',
    *                                   'title'         => __( '3rd', 'admin-page-framework-loader' ),                    
    *                                   'type'          => 'select',
    *                                   'label'         => array(
    *                                       'x' => 'X',
    *                                       'y' => 'Y',
    *                                       'z' => 'Z',                                        
    *                                   ),
    *                               ),                                    
    *                           ),
    *                           // 'description'   => '',
    *                       ),                            
    *                       array(
    *                           'field_id'      => 'c',
    *                           'title'         => __( 'c', 'admin-page-framework-loader' ),                    
    *                           'type'          => 'radio',                    
    *                           'label'         => array(
    *                               'a' => __( 'Apple', 'admin-page-framework-loader' ),
    *                               'b' => __( 'Banana', 'admin-page-framework-loader' ),
    *                               'c' => __( 'Cherry', 'admin-page-framework-loader' ),
    *                           ),
    *                           'default'       => 'b',
    *                       ),                        
    *                   )
    *               ),                    
    *           ),
    *       )
    *   );
    * </code></pre>
    *       </li>
    * </ul>
    */
    public function addSettingField( $asFieldset ) {
        if ( method_exists( $this->oForm, 'addField' ) ) {
            $this->oForm->addField( $asFieldset );     
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
     *      if ( ! is_numeric( $aNewInput[ 'verify_text_field' ] ) ) {
     *          $aErrors[ 'verify_text_field' ] = __( 'The value must be numeric:', 'admin-page-framework-demo' ) 
     *              . $aNewInput[ 'verify_text_field' ];
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
     * @return  void
     */    
    public function setFieldErrors( $aErrors ) {
        $this->oForm->setFieldErrors( $aErrors );      
    }   
    
    /**
     * Check whether a user has set a field error(s) or not.
     * 
     * @since       3.3.0
     * @return      boolean     Whether or not a field error exists.
     */
    public function hasFieldError() {
        return $this->oForm->hasFieldError();
    }
    
    /**
    * Sets the given message to be displayed in the next page load. 
    * 
    * This is used to inform users about the submitted input data, such as "Updated successfully." or "Problem occurred." etc. 
    * and normally used in validation callback methods.
    * 
    * <h4>Example</h4>
    * `
    * if ( ! $bVerified ) {
    *       $this->setFieldErrors( $aErrors );     
    *       $this->setSettingNotice( 'There was an error in your input.' );
    *       return $aOldPageOptions;
    * }
    * `
    *
    * @since        3.0.4     
    * @access       public
    * @param        string      $sMessage       the text message to be displayed.
    * @param        string      $sType          (optional) the type of the message, either "error" or "updated"  is used.
    * @param        array       $asAttributes   (optional) the tag attribute array applied to the message container HTML element. If a string is given, it is used as the ID attribute value.
    * @param        boolean     $bOverride      (optional) If true, only one message will be shown in the next page load. false: do not override when there is a message of the same id. true: override the previous one.
    * @return       void
    */      
    public function setSettingNotice( $sMessage, $sType='error', $asAttributes=array(), $bOverride=true ) {
        $this->oForm->setSubmitNotice(
            $sMessage,
            $sType,
            $asAttributes,
            $bOverride
        );        
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
        return $this->oForm->hasSubmitNotice( $sType );
    }

}
