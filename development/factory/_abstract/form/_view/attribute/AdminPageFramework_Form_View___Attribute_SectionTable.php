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
class AdminPageFramework_Form_View___Attribute_SectionTable extends AdminPageFramework_Form_View___Attribute_Base {

    public $sContext    = 'section_table';
                   
    /**
     * Returns an attribute array.
     * 
     * @since       3.6.0
     * @return      array
     */
    protected function _getAttributes() {    
        return array( 
            'id'    => 'section_table-' . $this->aArguments[ '_tag_id' ], // 'section-' . $sSectionID . '__' . $iSectionIndex
            'class' =>  $this->getClassAttribute(
                'form-table',
                'admin-page-framework-section-table'   // referred by the collapsible section script
            ),
        );
    }      
    
}