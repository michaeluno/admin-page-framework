<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods that generates section related strings.
 *
 * @package     AdminPageFramework/Common/Form/View/Generator
 * @since       3.6.0
 * @internal
 */
abstract class AdminPageFramework_Form_View___Generate_Section_Base extends AdminPageFramework_Form_View___Generate_Base {

    /**
     * A section-set definition array will be set.
     */
    public $aArguments = array();

    /**
     * A callback function to filter the generated id.
     */
    public $hfCallback = null;

    public $sIndexMark = '___i___';

    /**
     * Sets up properties.
     */
    public function __construct( /* $aArguments, $hfCallback */ ) {

        $_aParameters = func_get_args() + array(
            $this->aArguments,
            $this->hfCallback,
        );
        $this->aArguments = $_aParameters[ 0 ];
        $this->hfCallback = $_aParameters[ 1 ];

    }

    /**
     * Returns a model that indicates where the index digit is placed.
     * @since       3.6.0
     * @return      string
     */
    public function getModel() {
        return '';
    }

    /**
     * Applies the subject string to the set callback filter function.
     * @since       3.6.0
     */
    protected function _getFiltered( $sSubject ) {
        return is_callable( $this->hfCallback )
            ? call_user_func_array(
                $this->hfCallback,
                array(
                    $sSubject,
                    $this->aArguments, // aSectionset
                )
            )
            : $sSubject;
    }

    /**
     * Converts an array to an input name for the name attribute.
     *
     * `
     * array( 'apple', 'banana', 'cherry' )
     * `
     * to
     * `
     * apple[banana][cherry]
     * `
     * The first item will not have braces.
     *
     * Used for the input `name` attribute.
     *
     * `
     * <input name="THIS PART" id=... />
     * `
     *
     * @return      string
     * @since       3.7.0
     */
    protected function _getInputNameConstructed( $aParts ) {

        // Extract the first part as it does not have braces
        $_sName = array_shift( $aParts );
        foreach( $aParts as $_sPart ) {
            $_sName .= '[' . $_sPart . ']';
        }
        return $_sName;

    }

}
