<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods for creating meta boxes in pages added by the framework.
 *
 * @abstract
 * @since       3.0.0
 * @package     AdminPageFramework/Factory/PageMetaBox
 */
abstract class AdminPageFramework_PageMetaBox extends AdminPageFramework_PageMetaBox_Controller {

    /**
     * Defines the class object structure type.
     *
     * This is used to create a property object as well as to define the form element structure.
     *
     * @since       3.0.0
     * @since       3.7.0      Changed the name from `$_sPropertyType`.
     * @since       3.7.12     Moved from `AdminPageFramework_PageMetaBox_Model`.
     * @internal
     */
    protected $_sStructureType = 'page_meta_box';

    /**
     * Registers necessary hooks and internal properties.
     *
     * <h4>Examples</h4>
     * <code>
     *     new APF_MetaBox_For_Pages_Normal(
     *         'apf_metabox_for_pages_normal', // meta box id
     *         __( 'Sample Meta Box For Admin Pages Inserted in Normal Area' ), // title
     *         'apf_first_page', // page slugs
     *         'normal', // context
     *         'default' // priority
     *     );
     *     include( APFDEMO_DIRNAME . '/example/APF_MetaBox_For_Pages_Advanced.php' );
     *     new APF_MetaBox_For_Pages_Advanced(
     *         'apf_metabox_for_pages_advanced', // meta box id
     *         __( 'Sample Meta Box For Admin Pages Inserted in Advanced Area' ), // title
     *         'apf_first_page', // page slugs
     *         'advanced', // context
     *         'default' // priority
     *     );
     *     include( APFDEMO_DIRNAME . '/example/APF_MetaBox_For_Pages_Side.php' );
     *     new APF_MetaBox_For_Pages_Side(
     *         'apf_metabox_for_pages_side', // meta box id
     *         __( 'Sample Meta Box For Admin Pages Inserted in Advanced Area' ), // title
     *         array( 'apf_first_page', 'apf_second_page' ), // page slugs - setting multiple slugs is possible
     *         'side', // context
     *         'default' // priority
     *     );
     * </code>
     * @since       3.0.0
     *
     * @param       string          $sMetaBoxID     The meta box ID to be created.
     * @param       string          $sTitle         The meta box title.
     * @param       array|string    $asPageSlugs    the page slug(s) that the meta box belongs to. If the element is an array, it will be considered as a tab array.
     * <code>
     *  $asPageSlugs = array(
     *      'settings' => array(     // if the key is not numeric and the value is an array, it will be considered as a tab array.
     *          'help',         // enabled in the tab whose slug is 'help' which belongs to the page whose slug is 'settings'
     *          'about',        // enabled in the tab whose slug is 'about' which belongs to the page whose slug is 'settings'
     *          'general',      // enabled in the tab whose slug is 'general' which belongs to the page whose slug is 'settings'
     *      ),
     *      'manage', // if the numeric key with a string value is given, the condition applies to the page slug of this string value.
     *  );
     * </code>
     * @param       string          $sContext       The context, either `normal`, `advanced`, or `side`.
     * @param       string          $sPriority      The priority, either `high`, `core`, `default` or `low`.
     * @param       string          $sCapability    The capability. See <a href="https://codex.wordpress.org/Roles_and_Capabilities" target="_blank">Roles and Capabilities</a>.
     */
    public function __construct( $sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {

        if ( empty( $asPageSlugs ) ) {
            return;
        }

        if ( ! $this->_isInstantiatable() ) {
            return;
        }

        // The property object needs to be done first before the parent constructor.
        $_sPropertyClassName = isset( $this->aSubClassNames[ 'oProp' ] )
            ? $this->aSubClassNames[ 'oProp' ]
            : 'AdminPageFramework_Property_' . $this->_sStructureType;
        $this->oProp             = new $_sPropertyClassName(
            $this,
            get_class( $this ),
            $sCapability,
            $sTextDomain,
            $this->_sStructureType
        );

        // This property item must be set before the isInThePage() method is used.
        $this->oProp->aPageSlugs = is_string( $asPageSlugs )
            ? array( $asPageSlugs )
            : $asPageSlugs;

        parent::__construct(
            $sMetaBoxID,
            $sTitle,
            $asPageSlugs,
            $sContext,
            $sPriority,
            $sCapability,
            $sTextDomain
        );

    }

}
