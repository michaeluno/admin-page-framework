<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format form section definition arrays.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Format_Sectionset extends AdminPageFramework_Format_Base {
    
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
        
        // Internal
        '_fields_type'      => null,     // 3.0.0+ - same as the one of the field definition array. Used to insert debug info at the bottom of sections.        
        '_is_first_index'   => false,    // 3.4.0+ (boolean) indicates whether it is the first item of the sub-sections (for repeatable sections).
        '_is_last_index'    => false,    // 3.4.0+ (boolean) indicates whether it is the last item of the sub-sections (for repeatable sections).
        
        '_caller_object'    => null,     // 3.6.0+ - the caller framework factory object. This allows the framework to access the factory property when rendering the section.
    );        
    
    /**
     * Stores the section definition.
     */
    public $aSection            = array();
    
    public $sFieldsType         = '';
    
    public $sCapability         = 'manage_options';
    
    public $iCountOfElements    = 0;
    
    public $oCaller             = null;
    
    /**
     * Sets up properties.
     */
    public function __construct( /* array $aSection, $sFieldsType, $sCapability, $iCountOfElements, $oCaller */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aSection, 
            $this->sFieldsType, 
            $this->sCapability, 
            $this->iCountOfElements,
            $this->oCaller,
        );
        $this->aSection             = $_aParameters[ 0 ];
        $this->sFieldsType          = $_aParameters[ 1 ];
        $this->sCapability          = $_aParameters[ 2 ];
        $this->iCountOfElements     = $_aParameters[ 3 ];
        $this->oCaller              = $_aParameters[ 4 ];

    }
    
    /**
     * Returns an formatted definition array.
     * 
     * @return      array       The formatted definition array.
     */
    public function get() {

        // Fill missing argument keys - the `uniteArrays()` method overrides `null` values.
        $_aSection = $this->uniteArrays(
            array( 
                '_fields_type'  => $this->sFieldsType,
                'capability'    => $this->sCapability,
            ) 
            + $this->aSection,
            self::$aStructure
        );
           
        $_aSection[ 'order' ] = $this->getAOrB(
            is_numeric( $_aSection[ 'order' ] ),
            $_aSection[ 'order' ],
            $this->iCountOfElements + 10
        );
        
        // 3.4.0+ Added the collapsible argument
        // 3.6.0+ Made it use a class to format the argument.
        $_oCollapsibleArgumentFormatter = new AdminPageFramework_Format_CollapsibleSection(
            $_aSection[ 'collapsible' ],
            $_aSection[ 'title' ]
        );
        $_aSection[ 'collapsible' ] = $_oCollapsibleArgumentFormatter->get();
                
        // 3.5.2+ Accept a string value set to the 'class' element.
        // $_aSection[ 'class' ] = is_array( $_aSection[ 'class' ] ) 
            // ? $_aSection[ 'class' ]
            // : $this->getAsArray( $_aSection[ 'class' ] );
        $_aSection[ 'class' ] = $this->getAsArray( $_aSection[ 'class' ] );
        
        $_aSection[ '_caller_object' ] = $this->oCaller;
        
        return $_aSection;
        
    }
           
}
