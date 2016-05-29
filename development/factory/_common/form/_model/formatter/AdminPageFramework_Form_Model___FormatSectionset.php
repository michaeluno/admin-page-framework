<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format each form section-set definition arrays.
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form/Model/Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Form_Model___FormatSectionset extends AdminPageFramework_Form_Utility {
    
    /**
     * Represents the structure of the form section array.
     * 
     * @internal
     * @since       2.0.0
     * @since       3.6.0       Moved from `AdminPageFramework_FormDefinition`.
     * @var         array       Represents the array structure of form section.
     * @static
     * @remark      accessed from the `AdminPageFramework_FormDefinition` class as well.
     */     
    static public $aStructure = array(    
        
        // Required
        'section_id'        => '_default', // 3.0.0+
        
        // Optional
        'page_slug'         => null,
        'tab_slug'          => null,
        'section_tab_slug'  => null,    // 3.0.0+
        'title'             => null,
        'description'       => null,
        'capability'        => null,
        'if'                => true,    
        'order'             => null,    // do not set the default number here because incremented numbers will be added when registering the sections.
        'help'              => null,
        'help_aside'        => null,
        'repeatable'        => false,   // (boolean|array) 3.0.0+
        'sortable'          => false,   // (boolean|array) 3.6.0+
        'attributes'        => array(   // 3.3.1+
            'class'         => null,    // set null to avoid undefined index warnings.
            'style'         => null,    // set null to avoid undefined index warnings.
            'tab'           => array(),
        ),
        'class'             => array(    // 3.3.1+
            'tab'           => array(),
        ),
        'hidden'            => false,    // 3.3.1+
        'collapsible'       => false,    // 3.4.0+ (boolean|array) For the array structure see the $aStructure_CollapsibleArguments property.
        'save'              => true,     // 3.6.0+
        
        'content'           => null,     // 3.6.1+  (string) An overriding section-set output.
        
        'tip'               => null,     // 3.7.0  (string) Tool tip HTML strings. 
        
        // Internal
        '_fields_type'      => null,     // @deprecated 3.7.0+ Use the `_structure_type` instead. 3.0.0+ - same as the one of the field definition array. Used to insert debug info at the bottom of sections.        
        '_structure_type'   => null,     // 3.7.0+
        '_is_first_index'   => false,    // 3.4.0+ (boolean) indicates whether it is the first item of the sub-sections (for repeatable sections).
        '_is_last_index'    => false,    // 3.4.0+ (boolean) indicates whether it is the last item of the sub-sections (for repeatable sections).
        
        '_section_path'         => '',       // 3.7.0+ (string) e.g. my_section|nested_section       
        '_section_path_array'   => '',       // 3.7.0+ (array) an array version of the above section_path argument. Numerically indexed.
        '_nested_depth'         => 0,        // 3.7.0+ (integer) the nested level of the section
        
        // 3.6.0+ - (object) the caller framework factory object. This allows the framework to access the factory property when rendering the section.
        // 3.7.0+  It no longer stores a factory object but a form object.
        '_caller_object'    => null,     
    );        
    
    /**
     * Stores the section definition.
     */
    public $aSectionset         = array();
    
    public $sSectionPath        = '';
    
    public $sStructureType      = '';
    
    public $sCapability         = 'manage_options';
    
    public $iCountOfElements    = 0;
    
    public $oCaller             = null;
    
    /**
     * Sets up properties.
     */
    public function __construct( /* array $aSectionset, $sSectionPath, $sStructureType, $sCapability, $iCountOfElements, $oCaller */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aSectionset, 
            $this->sSectionPath, 
            $this->sStructureType, 
            $this->sCapability, 
            $this->iCountOfElements,
            $this->oCaller,
        );
        $this->aSectionset          = $_aParameters[ 0 ];
        $this->sSectionPath         = $_aParameters[ 1 ];
        $this->sStructureType       = $_aParameters[ 2 ];
        $this->sCapability          = $_aParameters[ 3 ];
        $this->iCountOfElements     = $_aParameters[ 4 ];
        $this->oCaller              = $_aParameters[ 5 ];

    }
    
    /**
     * Returns an formatted definition array.
     * 
     * @return      array       The formatted definition array.
     */
    public function get() {

        // Fill missing argument keys - the `uniteArrays()` method overrides `null` values.
        $_aSectionPath = explode( '|', $this->sSectionPath );
        $_aSectionset  = $this->uniteArrays(
            array( 
                '_fields_type'          => $this->sStructureType,   // @deprecated  3.7.0+
                '_structure_type'       => $this->sStructureType,   // 3.7.0+
                '_section_path'         => $this->sSectionPath,     // 3.7.0+
                '_section_path_array'   => $_aSectionPath,
                '_nested_depth'         => count( $_aSectionPath ) - 1,    // 3.7.0+ - zero base
            ) 
            + $this->aSectionset
            + array(
                'capability'    => $this->sCapability,
            ),
            self::$aStructure
        );
           
        $_aSectionset[ 'order' ] = $this->getAOrB(
            is_numeric( $_aSectionset[ 'order' ] ),
            $_aSectionset[ 'order' ],
            $this->iCountOfElements + 10
        );
        
        // 3.4.0+ Added the collapsible argument
        // 3.6.0+ Made it use a class to format the argument.
        $_oCollapsibleArgumentFormatter = new AdminPageFramework_Form_Model___Format_CollapsibleSection(
            $_aSectionset[ 'collapsible' ],
            $_aSectionset[ 'title' ]
        );
        $_aSectionset[ 'collapsible' ] = $_oCollapsibleArgumentFormatter->get();
                
        // 3.5.2+ Accept a string value set to the 'class' element.
        $_aSectionset[ 'class' ] = $this->getAsArray( $_aSectionset[ 'class' ] );
        
        $_aSectionset[ '_caller_object' ] = $this->oCaller;
        
        return $_aSectionset;
        
    }
           
}
