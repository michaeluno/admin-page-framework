<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format HTML attributes.
 * 
 * @package     AdminPageFramework
 * @subpackage  Attribute
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Attribute_SectionTableBody extends AdminPageFramework_Attribute_Base {

    public $sContext    = 'section_table_content';
                   
    /**
     * Returns an attribute array.
     * 
     * @since       3.6.0
     * @return      array
     */
    protected function _getAttributes() {                    
        return array(
            'class' => $this->getAOrB(
                $this->aArguments[ '_is_collapsible' ],
                'admin-page-framework-collapsible-section-content' . ' '
                    . 'admin-page-framework-collapsible-content' . ' '
                    . 'accordion-section-content',
                null
            ),
        );
    }      
    
}