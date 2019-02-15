<?php
/**
 * Admin Page Framework
 *
 * Facilitates WordPress plugin and theme development.
 *
 * @author      Michael Uno <michael@michaeluno.jp>
 * @copyright   2013-2019 (c) Michael Uno
 * @license     MIT <http://opensource.org/licenses/MIT>
 * @package     AdminPageFramework
 */

if ( ! class_exists( 'NoUISliderCustomFieldType' ) ) :
/**
 * A field type that lets the user toggle a switch.
 *
 * @since       3.8.6
 * @version     0.0.4
 * @remark      Requires Admin Page Framework 3.8.8 or above.
 */
class NoUISliderCustomFieldType extends AdminPageFramework_FieldType_text {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'no_ui_slider', );

    /**
     * Defines the default key-values of this field type settings.
     *
     * @remark\ $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(

        'attributes'    => array(
            'size'  => 12,
        ),

        /**
         * @see     http://refreshless.com/nouislider/slider-options/
         */
        'options'   => array(
            'range'         => array( // Slider can select '0' to '100'
                'min'   => 0,
                'max'   => 100,
            ),
            'step'          => 1, // Slider moves in increments of '1'
        	'start'         => array( 0 ), // Handle start position

            // Custom options

            'round' => 0,  // for the number of digits to multiply to the actual result. e.g. 10.00 -> 10 for the value `2`.

            'interactive'   => array( false ),  // for multiple handles, set multiple boolean values in the array, `array( true, true )`.

            /**
             * Whether the user can set a number exceeding the initially set the min/max range via the input fields.
             * For this options to take effect, the `interactive` option must be enabled.
             */
            'can_exceed_min' => false,
            'can_exceed_max' => false,

            /**
             * Whether to allow empty values to be set when the `interactive` option is enabled.
             * This way empty value can represents no limit.
             */
            'allow_empty'    => false,
        ),


    );

    protected function construct() {
    }

    /**
     * Loads the field type necessary components.
     */
    public function setUp() {}


    /**
     * Returns an array holding the urls of enqueuing scripts.
     * @return      array
     */
    protected function getEnqueuingScripts() {
        return array(
            array(
                'src'           => $this->isDebugMode()
                    ? dirname( __FILE__ ) . '/no-ui-slider/js/nouislider.js'
                    : dirname( __FILE__ ) . '/no-ui-slider/js/nouislider.min.js',
                'in_footer'     => true,
                'dependencies'  => array( 'jquery' )
            ),
            dirname( __FILE__ ) . '/js/no-ui-slider-initializer.js',
        );
    }

    /**
     * @return      array
     */
    protected function getEnqueuingStyles() {
        return array(
            $this->isDebugMode()
                ? dirname( __FILE__ ) . '/no-ui-slider/css/nouislider.css'
                : dirname( __FILE__ ) . '/no-ui-slider/css/nouislider.min.css',
            dirname( __FILE__ ) . '/no-ui-slider/css/nouislider.pips.css',
            dirname( __FILE__ ) . '/no-ui-slider/css/nouislider.tooltips.css',
            dirname( __FILE__ ) . '/css/no-ui-slider-field-type.css',
        );
    }

    /**
     * Returns the output of the field type.
     *
     * @return      string
     */
    public function getField( $aField ) {

        // Set required attributes.
        $aField[ 'attributes' ][ 'type' ]           = 'text';   // not `number` because noUISlider supports a format option that appends a unit like `10 pounds`.
        $aField[ 'attributes' ][ 'data-type' ]      = 'no_ui_slider';
        $aField[ 'attributes' ][ 'readonly' ]       = 'readonly';

        // Format the `options` argument.
        $aField[ 'options' ]    = $this->___getNoUISliderOptionsFormatted(
            $this->getElementAsArray( $aField, 'options' ),
            $aField
        );

        // Format the `label` argument.
        $aField[ 'label' ]      = $this->_getLabelsFormatted( $aField[ 'label' ], $aField );

        // Format the input attributes. This must be AFTER formatting the `label` argument.
        $aField[ 'attributes' ] = $this->_getAttributesFormatted( $aField );

        $_aAttributes = array(
                'class' => $this->_getSliderElementClassSelectors( $aField ),
            ) + $this->getDataAttributeArray( $aField[ 'options' ] )
              + $this->getElementAsArray( $aField, array( 'attributes', 'slider' ) );

        return '<div ' . $this->getAttributes( $_aAttributes ) . ' ></div>'
            . parent::getField( $aField );

    }

        /**
         * @return      string
         */
        private function _getSliderElementClassSelectors( $aField ) {
            $_sSelectors = 'no-ui-sliders';
            $_aPips      = $this->getElement( $aField[ 'options' ], 'pips' );
            if ( empty( $_aPips ) ) {
                return $_sSelectors;
            }
            if ( 'vertical' === $this->getElement( $aField[ 'options' ], 'orientation' ) ) {
                return $_sSelectors;
            }
            return $_sSelectors . ' has-pips';
        }

        /**
         * Formats the `attributes` argument.
         * @return      array
         */
        private function _getAttributesFormatted( $aField ) {

            $_aLabels          = $this->getAsArray(
                $aField[ 'label' ],
                true    // preserve empty
            );
            $_iNumberOfHandles = count( $_aLabels );

            $_aAttributes      = array();
            foreach( $_aLabels as $_isIndex => $_sLabel ) {

                $_bInteractive     = ( boolean ) $this->getElement( $aField, array( 'options', 'interactive', $_isIndex ) );
                $_aInputAttributes = array(
                    'data-key'              => $_isIndex,
                    'data-handles'          => $_iNumberOfHandles,
                    'data-interactive'      => $_bInteractive,
                    'readonly'              => $_bInteractive ? null : 'readonly',
                ) + $this->getElementAsArray( $aField, array( 'attributes', $_isIndex ) );

                // If the label is a single item, there is no nested attribute element.
                if ( 1 === $_iNumberOfHandles ) {
                    $_aAttributes = $_aInputAttributes;
                    break;
                }

                $_aAttributes[ $_isIndex ] = $_aInputAttributes;

            }
            return $_aAttributes + $this->getAsArray( $aField[ 'attributes' ] );

        }

        /**
         * Formats the `label` argument.
         *
         * This determines the number of input fields that store the selected numbers.
         *
         * If only one label is set, the option structure of the field will be one dimension.
         * So just return the label itself. Otherwise, return an array holding labels.
         *
         * @return      array|string
         */
        private function _getLabelsFormatted( $aLabels, $aField ) {
            $_aStart   = $this->getElementAsArray( $aField, array( 'options', 'start' ) );
            $_aLabels  = $this->getAsArray(
                $aLabels,
                true    // preserve empty
            );
            $_iHandles = count( $_aStart );
            $_aLabels = $_aLabels + array_fill(
                0,          // start index
                $_iHandles ? $_iHandles : 1, // end index (must be a positive number)
                ''          // the value to fill
            );

            return 1 >= count( $_aLabels )
                ? $_aLabels[ 0 ]
                : $_aLabels;
        }


        /**
         *
         * @return      array
         */
        private function ___getNoUISliderOptionsFormatted( $aOptions, $aField ) {

            // Determine the position of the slider handles. Set the stored values to the `start` argument.
            $aOptions[ 'start' ]   = $this->___getHandlePositions( $aOptions, $aField );

            // Format the `connect` argument.
            $aOptions[ 'connect' ] = $this->___getConnectArgumentFormatted( $aOptions, $aField );

            $aOptions[ 'range' ]   = $this->___getRangeOptionFormatted( $aOptions, $aField );

            return $aOptions;

        }
            /**
             * If the `can_exceed_min`/`can_exceed_max` argument is enabled
             * and the user has saved a value exceeding the range, adjust the range accordingly.
             * @param $aOptions
             * @param $aField
             * @since 0.0.4
             * @return array
             */
            private function ___getRangeOptionFormatted( $aOptions, $aField ) {

                $_bCanExceedMin    = $this->getElement( $aOptions, array( 'can_exceed_min' ), false );
                $_bCanExceedMax    = $this->getElement( $aOptions, array( 'can_exceed_max' ), false );
                if ( ! $_bCanExceedMin && ! $_bCanExceedMax ) {
                    return $aOptions[ 'range' ];
                }

                $_nDefinedMin      = $this->getElement( $aOptions, array( 'range', 'min' ) );
                $_nDefinedMax      = $this->getElement( $aOptions, array( 'range', 'max' ) );
                $_nUserSetMin      = $this->getElement( $aField, array( 'value', 0 ) );
                $_nUserSetMax      = $this->getElement( $aField, array( 'value', count( $this->getElementAsArray( $aField, array( 'value' ) ) ) - 1 ) );
                $_nSetMin          = $_nDefinedMin;
                $_nSetMax          = $_nDefinedMax;
                if ( $_bCanExceedMin && ! is_null( $_nUserSetMin ) && ( $_nDefinedMin > $_nUserSetMin ) ) {
                    $_nSetMin = $_nUserSetMin;
                }
                if ( $_bCanExceedMax && ! is_null( $_nUserSetMax ) && ( $_nDefinedMax < $_nUserSetMax ) ) {
                    $_nSetMax = $_nUserSetMax;
                }
                return array(
                    'min'   => ( float ) $_nSetMin,
                    'max'   => ( float ) $_nSetMax,
                );
            }
            /**
             * Formats the `connect` argument to avoid errors on the JS script side.
             *
             * @remark      This must be called AFTER the `start` argument is formatted as it counts the number of element of the `start` argument.
             * @return      array|null
             */
            private function ___getConnectArgumentFormatted( $aOptions, $aField ) {

                $_iHandles = count( $aOptions[ 'start' ] );
                $_aConnect = $this->getElementAsArray( $aOptions, 'connect' );
                if ( empty( $_aConnect ) ) {
                    return null;
                }

                $_aFalses = array_fill( 0, $_iHandles + 1, false );
                return $_aConnect + $_aFalses;

            }

            /**
             * Retrieves the value of the `start` argument which determines the position of the slider handle.
             * @return      array
             */
            private function ___getHandlePositions( $aOptions, $aField ) {

                // For the first time of loading the form, a value is not set.
                // If the value is not set, use the value set to the `start` argument.
                $_aDefault = $this->getElementAsArray( $aOptions, 'start', array( 0 ) );
                if ( ! isset( $aField[ 'value' ] ) ) {
                    return $_aDefault;
                }

                $_aHandlePositions = $this->getAsArray(
                    $aField[ 'value' ],
                    true    // preserve empty
                );

                // If the `allow_empty` option is enabled, the field may store empty values.
                if ( $this->getElement( $aOptions, array( 'allow_empty' ) ) ) {
                    $_aHandlePositions = $this->___getHandlePositionsAdjustedForEmptyValues( $_aHandlePositions, $aOptions );
                }

                return $_aHandlePositions;

            }
                /**
                 * If the `allow_empty` option is enabled, the field may store an empty value.
                 * In thet case replace it with the corresponding handle position set in the `range` option.
                 * Note that when there are more than two handles, the middle values other than the first and last are nothing to do with it.
                 * @param   $_aHandlePositions
                 * @param   $aOptions
                 * @return  array
                 * @since   0.0.4
                 */
                private function ___getHandlePositionsAdjustedForEmptyValues( array $_aHandlePositions, array $aOptions ) {

                    $_iFirstIndex = $_iIndex = 0;
                    $_iLastIndex  = count( $_aHandlePositions ) - 1;
                    foreach ( $_aHandlePositions as $_isIndex => $_sValue ) {

                        if ( '' !== $_sValue ) {
                            $_iIndex++;
                            continue;
                        }

                        if ( $_iFirstIndex === $_iIndex ) {
                            $_aHandlePositions[ $_isIndex ] = $this->getElement(
                                $aOptions, array( 'range', 'min' )
                            );
                        } else if ( $_iLastIndex === $_iIndex ) {
                            $_aHandlePositions[ $_isIndex ] = $this->getElement(
                                $aOptions, array( 'range', 'max' )
                            );
                        }
                        $_iIndex++;

                    }
                    return $_aHandlePositions;

                }

}
endif;
