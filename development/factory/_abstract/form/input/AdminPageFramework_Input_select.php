<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to output form input element of drop-down list.
 * 
 * @package         AdminPageFramework
 * @subpackage      FormInput
 * @since           3.4.0       
 * @internal
 */
class AdminPageFramework_Input_select extends AdminPageFramework_Input_Base {
    
    /**
     * Represents the structure of the options array.
     * 
     * @since       3.4.0
     */
    public $aStructureOptions = array(
        'input_container_tag'          => 'span',
        'input_container_attributes'    => array(
            'class' => 'admin-page-framework-input-container',
        ),
        'label_container_tag'          => 'span',
        'label_container_attributes'    => array(
            'class' => 'admin-page-framework-input-label-string',
        ),        
    );
    
    /**
     * A user constructor.
     * 
     * @since       3.5.3
     * @return      void
     */
    protected function construct() {
        
        // For backward compatibility.
        
        // If the $aField property is set, extract certain elements from it and set them to the attribute array.
        if ( isset( $this->aField[ 'is_multiple' ] ) ) {
            $this->aAttributes[ 'select' ][ 'multiple' ] = $this->aField[ 'is_multiple' ]
                ? 'multiple'
                : $this->getElement( $this->aAttributes, array( 'select', 'multiple' ) );
        }
        
    }
    
    /**
     * Returns the output of the input element.
     * 
     * @remark       This method should be overridden in each extended class.
     * @since        3.4.0     
     */    
    public function get( /* $aLabels, $aAttributes=array() */ ) {

        // Parameters
        $_aParams       = func_get_args() + array( 0 => null, 1 => array() );
        $_aLabels       = $_aParams[ 0 ];
        $_aAttributes   = $this->uniteArrays(
            $this->getElementAsArray( $_aParams, 1, array() ),
            $this->aAttributes 
        );    

        return  
            "<{$this->aOptions[ 'input_container_tag' ]} " . $this->getAttributes( $this->aOptions[ 'input_container_attributes' ] ) . ">"
                . "<select " . $this->getAttributes( $this->_getSelectAttributes( $_aAttributes ) ) . " >"
                    . $this->_getDropDownList( 
                        $this->getAttribute( 'id' ),
                        $this->getAsArray(
                            isset( $_aLabels ) 
                                ? $_aLabels
                                : $this->aField[ 'label' ],    // backward compatibility
                            true
                        ),
                        $_aAttributes
                    )
                . "</select>"
            . "</{$this->aOptions[ 'input_container_tag' ]}>"
            ;
            
    }
        /**
         * Retrusn an HTML select attribute array.
         * @since       3.5.3
         * @return      array       The generated attribute array for the `select` tag.
         */
        private function _getSelectAttributes( array $aBaseAttributes ) {
            $_bIsMultiple = $this->getElement( $aBaseAttributes, 'multiple' )
                ? true
                : ( ( bool ) $this->getElement( $aBaseAttributes, array( 'select', 'multiple' ) ) );
            return $this->uniteArrays(
                // allowing the user set attributes override the system set attributes.
                $this->getElementAsArray( $aBaseAttributes, 'select', array() ),
                array(
                    'id'        => $this->getAttribute( 'id' ),
                    'multiple'  => $_bIsMultiple 
                        ? 'multiple' 
                        : null,
                    'name'      => $_bIsMultiple 
                        ? $this->getAttribute( 'name' ) . '[]'
                        : $this->getAttribute( 'name' ),
                    'data-id'   => $this->getAttribute( 'id' ),       // referenced by the JavaScript scripts such as the revealer script.
                )
            );            
            
        }
        /**
         * Returns the option tags of the select field.
         * 
         * @since       2.0.0
         * @since       2.0.1       Added the $vValue parameter to the second parameter. This is the result of supporting the size field type.
         * @since       2.1.5       Added the $tag_id parameter.
         * @since       3.0.0       Reconstructed entirely.
         * @since       3.4.0  
         * @internal
         * @param       string      $sInputID           The input ID that will be the base of each generated option tag ID.
         * @param       array       $aLabels            The array holding labels.
         * @param       array       $aAttributes        The attribute arrays. Accepts the following arguments.
         * - optgroup
         * - option
         */     
        private function _getDropDownList( $sInputID, array $aLabels, array $aBaseAttributes ) {
            
            $_aOutput   = array();
            foreach( $aLabels as $__sKey => $__asLabel ) {
                
                // For an optgroup tag,
                if ( is_array( $__asLabel ) ) {
                    $_aOutput[] = $this->_getOptGroup(
                        $aBaseAttributes, 
                        $sInputID, 
                        $__sKey, 
                        $__asLabel
                    );
                    continue;
                }
                
                // A normal option tag,
                $_aOutput[] = $this->_getOptionTag( 
                    $__asLabel,   // the text label the user sees to be selected
                    $this->_getOptionTagAttributes( 
                        $aBaseAttributes, 
                        $sInputID, 
                        $__sKey,
                        $this->getAsArray( $aBaseAttributes[ 'value' ], true )
                    ) 
                );
                    
            }
            return implode( PHP_EOL, $_aOutput );    
            
        }
            /**
             * Returns an HTML output of optgroup tag.
             * @since       3.5.3
             * @return      string      an HTML output of optgroup tag.
             */
            private function _getOptGroup( array $aBaseAttributes, $sInputID, $sKey, $asLabel ) {
             
                $_aOptGroupAttributes = isset( $aBaseAttributes[ 'optgroup' ][ $sKey ] ) && is_array( $aBaseAttributes[ 'optgroup' ][ $sKey ] )
                    ? $aBaseAttributes[ 'optgroup' ][ $sKey ] + $aBaseAttributes[ 'optgroup' ]
                    : $aBaseAttributes[ 'optgroup' ];
                $_aOptGroupAttributes = array(
                    'label' => $sKey,
                ) + $_aOptGroupAttributes;
                return "<optgroup " . $this->getAttributes( $_aOptGroupAttributes ) . ">"
                        . $this->_getDropDownList( $sInputID, $asLabel, $aBaseAttributes )
                    . "</optgroup>";
             
            }
        
            /**
             * 
             * @since        3.5.3
             */
            private function _getOptionTagAttributes( array $aBaseAttributes, $sInputID, $sKey, $aValues ) {
            
                $aValues = $this->getElementAsArray( 
                    $aBaseAttributes, 
                    array( 'option', $sKey, 'value' ), 
                    $aValues 
                );
                return array(      
                        'id'        => $sInputID . '_' . $sKey,
                        'value'     => $sKey,
                        'selected'  => in_array( ( string ) $sKey, $aValues ) 
                            ? 'selected' 
                            : null,                                        
                    ) + ( isset( $aBaseAttributes[ 'option' ][ $sKey ] ) && is_array( $aBaseAttributes[ 'option' ][ $sKey ] )
                        ? $aBaseAttributes[ 'option' ][ $sKey ] + $aBaseAttributes[ 'option' ]
                        : $aBaseAttributes[ 'option' ] );
            
            }
        
            /**
             * Returns an HTML option tag output.
             * @sicne       3.4.0
             * @return      string      The generated option tag HTML output.
             */
            private function _getOptionTag( $sLabel, array $aOptionTagAttributes=array() ) {
                return "<option " . $this->getAttributes( $aOptionTagAttributes ) . " >"    
                        . $sLabel
                    . "</option>";
            }
    
}