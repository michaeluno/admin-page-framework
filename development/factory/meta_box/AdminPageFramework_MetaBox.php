<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for creating meta boxes for post types.
 *
 * @abstract
 * @since           2.0.0
 * @package         AdminPageFramework
 * @subpackage      MetaBox
 */
abstract class AdminPageFramework_MetaBox extends AdminPageFramework_MetaBox_Controller {

    /**
     * Defines the class object structure type.
     * 
     * This is used to create a property object as well as to define the form element structure.
     * 
     * @since       3.0.0
     * @since       3.7.0      Changed the name from `$_sStructureType`.
     * @internal
     */
    protected $_sStructureType = 'post_meta_box';
        
    /**
     * Constructs the class object instance of AdminPageFramework_MetaBox.
     * 
     * Sets up properties and hooks.
     * 
     * <h4>Example</h4>
     * <code>
     *     new APF_MetaBox_BuiltinFieldTypes(
     *         'sample_custom_meta_box', // meta box ID
     *         __( 'Demo Meta Box with Built-in Field Types', 'admin-page-framework-demo' ), // title
     *         array( 'apf_posts' ), // post type slugs: post, page, etc.
     *         'normal', // context (what kind of metabox this is)
     *         'default' // priority
     *     );
     * </code>
     * @see         http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
     * @since       2.0.0
     * @param       string            $sMetaBoxID               The meta box ID. [3.3.0+] If an empty value is passed, the ID will be automatically generated and the lower-cased class name will be used.
     * @param       string            $sTitle                   The meta box title.
     * @param       string|array      $asPostTypeOrScreenID     (optional) The post type(s) or screen ID that the meta box is associated with.
     * @param       string            $sContext                 (optional) The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side') Default: `normal`.
     * @param       string            $sPriority                (optional) The priority within the context where the boxes should show ('high', 'core', 'default' or 'low') Default: `default`.
     * @param       string            $sPriority                (optional) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the meta box. Default: `edit_posts`.
     * @param       string            $sTextDomain              (optional) The text domain applied to the displayed text messages. Default: `admin-page-framework`.
     * @return      void
     */
    public function __construct( $sMetaBoxID, $sTitle, $asPostTypeOrScreenID=array( 'post' ), $sContext='normal', $sPriority='default', $sCapability='edit_posts', $sTextDomain='admin-page-framework' ) {
        
        if ( ! $this->_isInstantiatable() ) {
            return;
        }
        if ( empty( $asPostTypeOrScreenID ) ) {
            return;
        }
        
        // A property object needs to be done first.
        $_sProprtyClassName = isset( $this->aSubClassNames[ 'oProp' ] )
            ? $this->aSubClassNames[ 'oProp' ]
            : 'AdminPageFramework_Property_' . $this->_sStructureType;
        $this->oProp                = new $_sProprtyClassName(
            $this,
            get_class( $this ),
            $sCapability,
            $sTextDomain,
            $this->_sStructureType
        );
        $this->oProp->aPostTypes    = is_string( $asPostTypeOrScreenID )
            ? array( $asPostTypeOrScreenID )
            : $asPostTypeOrScreenID;
        
        parent::__construct(
            $sMetaBoxID,
            $sTitle,
            $asPostTypeOrScreenID,
            $sContext,
            $sPriority,
            $sCapability,
            $sTextDomain
        );
        
    }
    
}
