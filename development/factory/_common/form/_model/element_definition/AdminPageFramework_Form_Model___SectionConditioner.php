<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to build forms.
 *
 * @package     AdminPageFramework/Common/Form/Model
 * @since       3.7.0
 * @deprecated
 * @extends     AdminPageFramework_FrameworkUtility
 * @internal
 */
class AdminPageFramework_Form_Model___SectionConditioner extends AdminPageFramework_FrameworkUtility {

    public $aSectionsets  = array();

    /**
     * Sets up hooks.
     * @since       3.7.0
     */
    public function __construct( /* $aSectionsets */ ) {

        $_aParameters = func_get_args() + array(
            $this->aSectionsets,
        );
        $this->aSectionsets  = $_aParameters[ 0 ];

    }

    /**
     * @since       3.7.0
     * @return      array       The conditioned fieldsets array.
     */
    public function get() {
        return $this->_getSectionsConditioned(
            $this->aSectionsets
        );
    }

    /**
     * Returns a sections-array by applying the conditions.
     *
     * @since       3.0.0
     * @since       3.5.3       Added a type hint and changed the default value to array from null.
     * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition`. Changed the name from `getConditionedSections()`.
     * @return      array       The conditioned sectionsets array.
     */
    private function _getSectionsConditioned( array $aSections=array() ) {

        $_aNewSections  = array();
        foreach( $aSections as $_sSectionID => $_aSection ) {
            if ( ! $this->_isAllowed( $_aSection ) ) {
                continue;
            }
            $_aNewSections[ $_sSectionID ] = $_aSection;
        }
        return $_aNewSections;

    }


    /**
     * Checks if the passed item is allowed to be registered.
     * @remark      A child class also accesses this method.
     * @return      boolean
     * @since       3.7.0
     */
    protected function _isAllowed( array $aDefinition ) {

        // Check capability. If the access level is not sufficient, skip.
        if ( ! current_user_can( $aDefinition[ 'capability' ] ) ) {
            return false;
        }
        return ( boolean ) $aDefinition[ 'if' ];
    }

}
