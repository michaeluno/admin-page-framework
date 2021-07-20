<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides shared methods for the form class.
 *
 * @package     AdminPageFramework/Common/Form
 * @since       3.7.0
 * @internal
 */
abstract class AdminPageFramework_Form_Base extends AdminPageFramework_Form_Utility {

    /**
     * A submit notice object.
     * @var AdminPageFramework_Form___SubmitNotice
     */
    public $oSubmitNotice;

    /**
     * A field error object.
     * @var AdminPageFramework_Form___FieldError
     */
    public $oFieldError;

    /**
     * Last inputs handler object.
     * @var AdminPageFramework_Form_Model___LastInput
     */
    public $oLastInputs;

    /**
     * Stores resource items.
     *
     * @internal
     */
    static public $_aResources = array(
        'internal_styles'    => array(),
        'internal_styles_ie' => array(),
        'internal_scripts'   => array(),
        'src_styles'         => array(),
        'src_scripts'        => array(),
    );

    /**
     * @var array
     */
    public $aCallbacks;
    /**
     * @var array
     */
    public $aSectionsets;
    /**
     * @var array
     */
    public $aFieldsets;

    /**
     * Checks if a given array holds fieldsets or not.
     *
     * @todo        It seems this method is not used. If so deprecate it.
     * @return      boolean
     */
    // public function isFieldsets( array $aItems ) {
        // $_aItem = $this->getFirstElement( $aItems );
        // return isset( $_aItem[ 'field_id' ], $_aItem[ 'section_id' ] );
    // }

    /**
     * Stores the message object.
     * @var AdminPageFramework_Message
     */
    public $oMsg;

    /**
     * Determines whether the given ID is of a registered form section.
     *
     * Consider the possibility that the given ID may be used both for a section and a field.
     *
     * 1. Check if the given ID is not a section.
     * 2. Parse stored fields and check their ID. If one matches, return false.
     *
     * @param       string  $sID
     * @return      boolean
     * @since       3.0.0
     * @since       3.7.0   Moved from `AdminPageFramework_FormDefinition_Base`.
     * @todo Find a way for nested sections.
     */
    public function isSection( $sID ) {

        // Integer IDs are not accepted as they are reserved for sub-sections.
        if ( $this->isNumericInteger( $sID ) ) {
            return false;
        }

        // If the section ID is not registered, return false.
        if ( ! array_key_exists( $sID, $this->aSectionsets ) ) {
            return false;
        }

        // the fields array's first dimension is also filled with the keys of section ids.
        if ( ! array_key_exists( $sID, $this->aFieldsets ) ) {
            return false;
        }

        // Since numeric IDs are denied at the beginning of the method, the elements will not be sub-sections.
        $_bIsSection = false;
        foreach( $this->aFieldsets as $_sSectionID => $_aFields ) {

            if ( $_sSectionID == $sID ) {
                $_bIsSection = true;
            }

            // a field using the ID is found, and it precedes a section match.
            if ( array_key_exists( $sID, $_aFields ) ) {
                return false;
            }

        }
        return $_bIsSection;

    }

    /**
     * Decides whether the current user including guests can view the form or not.
     *
     * To allow guests to view the form set an empty value to it.
     *
     * @since       3.7.0
     * @param       string  $sCapability
     * @return      boolean
     */
    public function canUserView( $sCapability ) {
        if ( ! $sCapability  ) {
            return true;
        }
        return ( boolean ) current_user_can( $sCapability );
    }

    /**
     * Decides whether the form elements should be registered or not.
     *
     * @access      public      A delegation class accesses this method so it must be public.
     * @since       3.7.0
     * @return      boolean
     */
    public function isInThePage() {
        return $this->callBack( $this->aCallbacks[ 'is_in_the_page' ], true );
    }

    /**
     * Prevents the output from getting too long when the object is dumped.
     *
     * Field definition arrays contain the factory object reference and when the debug log method tries to dump it, the output gets too long.
     * So shorten it here.
     *
     * @remark      Called when the object is called as a string.
     * @since       3.7.0
     */
    public function __toString() {
        return $this->getObjectInfo( $this );
    }

    /**
     * Returns the form object.
     * This method is for a callback and return the form object. This will be used by delegation classes which need to access the form object
     * such as a collapsible section handle class that needs to enqueue resource using the form public method.
     * @since 3.9.0
     * @return AdminPageFramework_Form_Base
     */
    public function replyToGetSelf() {
        return $this;
    }

}