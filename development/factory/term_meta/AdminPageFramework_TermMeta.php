<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods for creating fields in the taxonomy page (edit-tags.php).
 *
 * @abstract
 * @since       3.8.0
 * @package     AdminPageFramework/Factory/TermMeta
 * @extends     AdminPageFramework_TermMeta_Controller
 */
abstract class AdminPageFramework_TermMeta extends AdminPageFramework_TermMeta_Controller {

    /**
     * Defines the class object structure type.
     *
     * This is used to create a property object as well as to define the form element structure.
     *
     * @since       3.8.0
     * @internal
     */
    protected $_sStructureType = 'term_meta';

    /**
     * Constructs the class object instance of AdminPageFramework_TermMeta.
     *
     * Handles setting up properties and hooks.
     *
     * <h4>Examples</h4>
     * <code>
     * new APF_TermMeta(
     *  'apf_sample_taxonomy'   // taxonomy slug
     * );
     * </code>
     *
     * @since       3.8.0
     * @param       array|string    The taxonomy slug(s). If multiple slugs need to be passed, enclose them in an array and pass the array.
     * @param       string          The access rights. Default: `manage_options`.
     * @param       string          The text domain. Default: `admin-page-framework`.
     * @return      void
     */
    public function __construct( $asTaxonomySlug, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {

        if ( empty( $asTaxonomySlug ) ) {
            return;
        }

        // Properties
        $_sPropertyClassName = isset( $this->aSubClassNames[ 'oProp' ] )
            ? $this->aSubClassNames[ 'oProp' ]
            : 'AdminPageFramework_Property_' . $this->_sStructureType;
        $this->oProp        = new $_sPropertyClassName(
            $this,
            get_class( $this ),
            $sCapability,
            $sTextDomain,
            $this->_sStructureType
        );
        $this->oProp->aTaxonomySlugs    = ( array ) $asTaxonomySlug;

        parent::__construct( $this->oProp );

    }

}
