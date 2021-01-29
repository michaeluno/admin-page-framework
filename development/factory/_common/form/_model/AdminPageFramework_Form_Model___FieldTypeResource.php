<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to retrieve field type resources.
 *
 * Resources in this context means scripts and styles.
 *
 * @package     AdminPageFramework/Common/Form/Model
 * @since       3.7.0
 * @extends     AdminPageFramework_FrameworkUtility
 * @internal
 */
class AdminPageFramework_Form_Model___FieldTypeResource extends AdminPageFramework_FrameworkUtility {

    /**
     * Stores field type callbacks and properties.
     */
    public $aFieldTypeDefinition = array();

    /**
     * Represents the resource array structure.
     */
    public $aResources = array(
        'internal_styles'    => array(),
        'internal_styles_ie' => array(),
        'internal_scripts'   => array(),
        'src_styles'         => array(),
        'src_scripts'        => array(),
    );

    /**
     * Sets up properties.
     * @since       3.7.0
     */
    public function __construct( /* array $aFieldTypeDefinition, array $aResources */ ) {

        $_aParameters = func_get_args() + array(
            $this->aFieldTypeDefinition,
            $this->aResources,
        );
        $this->aFieldTypeDefinition = $this->getAsArray( $_aParameters[ 0 ] );
        $this->aResources           = $this->getAsArray( $_aParameters[ 1 ] );

    }

    /**
     * Updates the given resource array by adding resources (styles and scripts) of the fieldset.
     *
     * @return      array       The updated resource array.
     * @since       3.7.0
     */
    public function get() {

        $this->aResources[ 'internal_scripts' ]      = $this->_getUpdatedInternalItemsByCallback(
            $this->aResources[ 'internal_scripts' ],
            'hfGetScripts'
        );
        $this->aResources[ 'internal_styles' ]       = $this->_getUpdatedInternalItemsByCallback(
            $this->aResources[ 'internal_styles' ],
            'hfGetStyles'
        );
        $this->aResources[ 'internal_styles_ie' ]    = $this->_getUpdatedInternalItemsByCallback(
            $this->aResources[ 'internal_styles_ie' ],
            'hfGetIEStyles'
        );
        $this->aResources[ 'src_styles' ]          = $this->_getUpdatedEnqueuingItemsByCallback(
            $this->aResources[ 'src_styles' ],
            'aEnqueueStyles'
        );
        $this->aResources[ 'src_scripts' ]         = $this->_getUpdatedEnqueuingItemsByCallback(
            $this->aResources[ 'src_scripts' ],
            'aEnqueueScripts'
        );
        return $this->aResources;

    }
        /**
         * @retuen      array
         * @since       3.7.0
         * @since       3.8.0
         */
        private function _getUpdatedInternalItemsByCallback( array $aSubject, $sKey ) {
            $_oCallable = $this->getElement( $this->aFieldTypeDefinition, $sKey );
            if ( ! is_callable( $_oCallable ) ) {
                return $aSubject;
            }
            $aSubject[] = call_user_func_array(
                $_oCallable,
                array()
            );
            return $aSubject;
        }
        /**
         * @return      array
         * @since       3.7.0
         */
        private function _getUpdatedEnqueuingItemsByCallback( $aSubject, $sKey ) {
            return array_merge(
                $aSubject,
                $this->getElementAsArray( $this->aFieldTypeDefinition, $sKey )
            );
        }

}
