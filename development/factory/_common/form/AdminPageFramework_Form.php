<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to build forms.
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form
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
         * @return      boolen
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
        'secitonsets_before_registration'   => null,
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
        
    );
    
    /**
     * Stores the message object.
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
        
    );
    
    /**
     * A submit notice object.
     */
    public $oSubmitNotice;    
    
    /**
     * A field error object.
     */
    public $oFieldError;
    
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
        $this->aArguments     = $this->_getFormattedArguments( $_aParameters[ 0 ] );
        $this->aCallbacks     = $this->getAsArray( $_aParameters[ 1 ] ) + $this->aCallbacks;
        $this->oMsg           = $_aParameters[ 2 ];
        
        // Sub-objects
        $this->oSubmitNotice  = new AdminPageFramework_Form___SubmitNotice;
        $this->oFieldError    = new AdminPageFramework_Form___FieldError( $this->aArguments[ 'caller_id' ] );
        $this->oLastInputs    = new AdminPageFramework_Form_Model___LastInput( $this->aArguments[ 'caller_id' ] );
                
        parent::__construct();
        
        $this->construct();
        
    }
    /**
     * User constructor.
     * Extended classes override this method to do set ups.
     * @since       3.7.0
     */
    public function construct() {}
    
        /**
         * Formats the argument array.
         * @return      array       The formatted argument array.
         * @since       3.7.0
         */
        private function _getFormattedArguments( $aArguments ) {
            
            $aArguments = $this->getAsArray( $aArguments ) 
                + $this->aArguments;
            $aArguments[ 'caller_id' ] = $aArguments[ 'caller_id' ]
                ? $aArguments[ 'caller_id' ]
// @todo determine the caller class name
                : get_class( $this );   
            
            if ( $this->sStructureType ) {
                $aArguments[ 'structure_type' ] = $this->sStructureType;
            }
                
            return $aArguments;
            
        }
}
