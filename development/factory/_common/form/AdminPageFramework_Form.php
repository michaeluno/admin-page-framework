<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to build forms.
 *
 * @package     AdminPageFramework/Common/Form
 * @since       3.7.0
 * @internal
 */
class AdminPageFramework_Form extends AdminPageFramework_Form_Controller {

    /**
     * Indicates the structure type of the form.
     *
     * The form input names and ids are constructed by this type.
     *
     * The main routine which instantiates this class shuld have its own structure type slug
     * such as `admin_page`, `widget`, `page_meta_box`, `post_meta_box`, `network` etc.
     *
     * Each extended class should override this value.
     */
    public $sStructureType = '';

    /**
     * Stores field type definitions.
     *
     * This will be set when fields are registered.
     */
    public $aFieldTypeDefinitions = array();

    /**
     * Stores section set definitions.
     */
    public $aSectionsets  = array(
        '_default' => array(
            'section_id'    => '_default',
        ),
    );

    /**
     * Stores field set definitions.
     */
    public $aFieldsets    = array();

    /**
     * Multi-dimensional array holding the saved form data.
     */
    public $aSavedData    = array();

    /**
     * The capability level of the form.
     *
     * This value is set via a callback before the form gets rendered.
     *
     * Each secitonset and fieldset has individual capability. If they are not set,
     * This value will be applied.
     */
    public $sCapability = '';       // default - an empty string

    /**
     * Stores callback functions.
     * Each value will have a callback.
     */
    public $aCallbacks    = array(

        /**
         * @return      string      The form default capability level.
         */
        'capability'    => null,

        /**
         * Decides whether the form elements should be registered or not.
         *
         * Registration in this context means whether to load related scripts and styles.
         * Also, a callback will be performed when a field is registered.
         *
         * @return     boolean
         */
        'is_in_the_page'    => null,

        /**
         * Decides whether the fieldset should load its resources.
         *
         * Registration in this context means whether to load related scripts and styles
         * and run associated callback upon filed type initialization.
         * @return      boolean
         */
        'is_fieldset_registration_allowed'  => null,

        /**
         * Called when a fieldset is parsed to add resources.
         * The main routine can use this hook to add help pane items.
         * @return      void
         */
        'load_fieldset_resource'        => null,

        /**
         * The saved form data.
         * @return     array
         */
        'saved_data'    => null,

        /**
         * Output
         */
        'fieldset_output'       => null,
        'section_head_output'   => null,

        /**
         * Called when a sectionset gets formatted.
         * Some factory classes such as the admin page have specific arguments such as `page_slug` and `tab_slug`.
         * This callback allows factory classes modify the formatted sectionset definition array.
         */
        'sectionset_before_output'     => null,
        'fieldset_before_output'       => null,

        /**
         * Decides whether the section is visible or not.
         * This will be called when the form gets rendered.
         * @return      boolean
         */
        'is_sectionset_visible'    => null,
        /**
         * Decides whether the field is visible or not.
         * This will be called when the form gets rendered.
         * @return      boolean
         */
        'is_fieldset_visible'      => null,

        /**
         * Allows the main routine to modify form element definitions including sectionsets and fieldsets.
         */
        'sectionsets_before_registration'   => null,
        'fieldsets_before_registration'     => null,

        /**
         * Allows the main routine modify form element definitions
         */
        'fieldset_after_formatting'     => null,
        'fieldsets_before_formatting'   => null,

        /**
         * Gets triggered after the form elements are registered and before the page gets rendered.
         * So use this hook to handle submitted form data and validate them.
         */
        'handle_form_data'         => null,

        /**
         * Determines whether to show debug information.
         */
        'show_debug_info'           => null,

        /**
         * Applies to form field errors array.
         *
         * This is introduced to customize the field errors passed to the form rendering method,
         * especially for front-end forms which does not reload the page after the form validation process.
         *
         * @since   3.8.11
         */
        'field_errors'              => null,

        /**
         * Field elements
         */
        'hfID'                              => null, // the input id attribute
        'hfTagID'                           => null, // the fields & fieldset & field row container id attribute
        'hfName'                            => null, // the input name attribute
        'hfNameFlat'                        => null, // the flat input name attribute
        'hfInputName'                       => null, // 3.6.0+   the field input name attribute
        'hfInputNameFlat'                   => null, // 3.6.0+   the flat field input name
        'hfClass'                           => null, // the class attribute
        'hfSectionName'                     => null, // 3.6.0+

    );

    /**
     * Stores the message object.
     * @var AdminPageFramework_Message
     */
    public $oMsg;

    /**
     * Stores arguments which define the behaviour of the class object.
     */
    public $aArguments = array(

        /**
         * A caller id
         *
         * This is used when field types are registered and for field error transient IDs to retrieve field errors.
         */
        'caller_id'                     => '',

        /**
         * @var      string      The structure type of the form.
         */
        'structure_type'                => 'admin_page',

        /**
         * should return the action hook name that the class calls back
         * to determine the action hook for form element registration and validation.
         * @return     string
         */
        'action_hook_form_registration' => 'current_screen',

        /**
         * If `true` and if the form registration (loading resources) action hook is already triggerd,
         * the callback will be triggered right away. If `false`, it must be dealt manually.
         * This allows an option of not auto-register. This is useful for widget forms that get called multiple times
         * so the form object need to be initialized many times. In that case, this value can be `false`
         * so that it won't reset the options (form data) right away.
         */
        'register_if_action_already_done'   => true,

        /**
         * Decides whether to autoload .min resource files if exist.
         * This value is automatically assigned by the property class of the factory class.
         * @see AdminPageFramework_Property_Base
         * @var boolean
         */
        'autoload_min_resource' => true,
    );

    /**
     * Stores sub-object class names.
     *
     * This allows extended form classes to define their own sub-classes.
     * For example, for front-end forms, setting notices and field errors may not need to use transients.
     * In that case, use a custom class to handle them.
     *
     * Also set an empty string to disable those objects.
     *
     * @since       3.8.11
     */
    public $aSubClasses = array(
        'submit_notice' => 'AdminPageFramework_Form___SubmitNotice',
        'field_error'   => 'AdminPageFramework_Form___FieldError',
        'last_input'    => 'AdminPageFramework_Form_Model___LastInput',
        'message'       => 'AdminPageFramework_Message',
    );

    /**
     * Sets up properties.
     * @since       3.7.0
     */
    public function __construct( /* array $aArguments, array $aCallbacks=array(), $oMsg */ ) {

        $_aParameters = func_get_args() + array(
            $this->aArguments,
            $this->aCallbacks,
            $this->oMsg,
        );
        $this->aArguments     = $this->___getArgumentsFormatted( $_aParameters[ 0 ] );
        $this->aCallbacks     = $this->getAsArray( $_aParameters[ 1 ] ) + $this->aCallbacks;
        $this->oMsg           = $_aParameters[ 2 ] ? $_aParameters[ 2 ] : new $this->aSubClasses[ 'message' ];

        // Sub-class objects
        $this->___setSubClassObjects();

        parent::__construct();

        $this->construct();

    }

        /**
         * Formats the argument array.
         * @param  array $aArguments
         * @return array       The formatted argument array.
         * @since  3.7.0
         */
        private function ___getArgumentsFormatted( $aArguments ) {

            $aArguments = $this->getAsArray( $aArguments )
                + $this->aArguments;
            $aArguments[ 'caller_id' ] = $aArguments[ 'caller_id' ]
                ? $aArguments[ 'caller_id' ]
                : get_class( $this );  // if a caller id is empty, this class name will be used.

            if ( $this->sStructureType ) {
                $aArguments[ 'structure_type' ] = $this->sStructureType;
            }

            return $aArguments;

        }

        /**
         * @since       3.8.11
         */
        private function ___setSubClassObjects() {
            if ( class_exists( $this->aSubClasses[ 'submit_notice' ] ) ) {
                $this->oSubmitNotice  = new $this->aSubClasses[ 'submit_notice' ];
            }
            if ( class_exists( $this->aSubClasses[ 'field_error' ] ) ) {
                $this->oFieldError    = new $this->aSubClasses[ 'field_error' ]( $this->aArguments[ 'caller_id' ] );
            }
            if ( class_exists( $this->aSubClasses[ 'last_input' ] ) ) {
                $this->oLastInputs    = new $this->aSubClasses[ 'last_input' ]( $this->aArguments[ 'caller_id' ] );
            }
        }

    /**
     * User constructor.
     * Extended classes override this method to do set ups.
     * @since       3.7.0
     */
    public function construct() {}

}
