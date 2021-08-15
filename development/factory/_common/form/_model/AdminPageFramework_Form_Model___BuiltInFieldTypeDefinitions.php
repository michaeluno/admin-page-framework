<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to retrieve built-in filed type definitions array.
 *
 * @package     AdminPageFramework/Common/Form/Model
 * @since       3.7.0
 * @internal
 */
class AdminPageFramework_Form_Model___BuiltInFieldTypeDefinitions {

    /**
     * Holds the built-in filed type slugs.
     *
     * @since       2.1.5
     * @since       3.7.0      Changed the name from `$aDefaultFieldTypeSlugs`. Moved from `AdminPageFramework_FieldTypeRegistration`.
     */
    static protected $_aDefaultFieldTypeSlugs = array(
        'default', // undefined ones will be applied
        'text',
        'number',
        'textarea',
        'radio',
        'checkbox',
        'select',
        'hidden',
        'file',
        'submit',
        'import',
        'export',
        'image',
        'media',
        'color',
        'taxonomy',
        'posttype',
        'size',
        'section_title', // 3.0.0+
        'system',        // 3.3.0+
        'inline_mixed',  // 3.8.0+
        '_nested',       // 3.8.0+
        'contact'        // 3.9.0+
    );

    public $sCallerID = '';

    public $oMsg;

    /**
     * Sets up properties.
     *
     * @param       string      $sCallerID  The call ID, usually the caller class name.
     * @param       object      $oMsg       A message object that field types refer to.
     * Field types will show system messages to the user using the message defined in this object.
     * @since       3.7.0
     */
    public function __construct( $sCallerID, $oMsg ) {
        $this->sCallerID    = $sCallerID;
        $this->oMsg         = $oMsg;
    }

    /**
     * Returns a field type definitions array.
     *
     * @since       3.1.3       Moved from the constructor.
     * @since       3.7.0       Moved from `AdminPageFramework_FieldTypeRegistration`. Change the name from `register()`.
     * @return      array       The field type definitions array.
     */
    public function get() {

        $_aFieldTypeDefinitions = array();
        foreach( self::$_aDefaultFieldTypeSlugs as $_sFieldTypeSlug ) {

            $_sFieldTypeClassName = "AdminPageFramework_FieldType_{$_sFieldTypeSlug}";
            $_oFieldType = new $_sFieldTypeClassName(
                $this->sCallerID,   // usually an instantiated class name
                null,               // field type slugs - if it is different from the one defined in the class property
                $this->oMsg,
                false               // `false` to disable auto-registering.
            );
            foreach( $_oFieldType->aFieldTypeSlugs as $_sSlug ) {
                $_aFieldTypeDefinitions[ $_sSlug ] = $_oFieldType->getDefinitionArray();
            }
        }
        return $_aFieldTypeDefinitions;

    }

}
