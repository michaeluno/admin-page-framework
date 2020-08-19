<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 * 
 */

/**
 * The base class of form input classes that return outputs of input form elements.
 * 
 * @package     AdminPageFramework/Common/Form/Input
 * @since       3.4.0
 * @extends     AdminPageFramework_FrameworkUtility
 * @internal
 */
abstract class AdminPageFramework_Input_Base extends AdminPageFramework_FrameworkUtility {
    
    /**
     * Stores the field definition array.
     * 
     * @since       3.4.0
     * @deprecated  3.5.3
     */
    public $aField = array();

    /**
     * Stores the attribute array.
     * 
     * @since       3.5.3
     */
    public $aAttributes = array();
    
    /**
     * Stores the options of how the input elements should be constructed.
     * 
     * @since       3.4.0
     */
    public $aOptions = array();
    
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
     * Sets up properties.
     * 
     * @since       3.4.0
     * @since       3.5.3       
     * @param       array       $aAttributes    The attribute array. A field definition array is deprecated.
     * @param       array       $aOptions       options that allows the user to set custom container tags and class selectors.
     */
    public function __construct( array $aAttributes, array $aOptions=array() ) {

        $this->aAttributes  = $this->getElementAsArray( 
            $aAttributes, 
            'attributes', 
            $aAttributes    // if the above key is not set, this will be set
        );

        $this->aOptions     = $aOptions + $this->aStructureOptions;
        
        // @deprecated 3.5.3+ use $aAttributes.
        $this->aField       = $aAttributes;  
        
        // User Constructor
        $this->construct();
        
    }
    
    /**
     * A user construct.
     * 
     * Rather than messing with __construct() simply use this method to implement additional tasks.
     * 
     * @since       3.5.3
     * @return      void
     */
    protected function construct() {}
        
    /**
     * Returns the output of the input element.
     * 
     * @remark       This method should be overridden in each extended class.
     * @since        3.4.0     
     */
    public function get() {}
 
    /**
     * Returns the set attribute by name. 
     * 
     * If a parameter is not passed, it returns the entire attribute array.
     * 
     * @since       3.5.3
     * @return      string|array|null        The specified attribute value or the entire attribute array if not specified. 
     * If not set, null will be returned as the `getAttributes()` method will not list an attribute with the null value.
     * @param       string      $sName      The attribute name.
     * @param       string      $sDefault   The defaqult value if the value is not set.
     */
    public function getAttribute( /* $sName=null, $sDefault=null */ ) {
        $_aParams = func_get_args() + array(
            0 => null,
            1 => null,
        );
        return isset( $_aParams[ 0 ] )
            ? $this->getElement( $this->aAttributes, $_aParams[ 0 ], $_aParams[ 1 ] )
            : $this->aAttributes();
    }
 
    /**
     * Adds class selector to the input class attribute.
     * 
     * @since       3.5.3
     * @return      string      The set class selector(s).
     */
    public function addClass( /* $asSelectors1, $asSelectors2 */ ) {
        foreach( func_get_args() as $_asSelectors ) {            
            $this->aAttributes['class'] = $this->getClassAttribute( 
                $this->aAttributes['class'],
                $_asSelectors
            );
        }
        return $this->aAttributes['class'];
    }
    
    /**
     * Sets an attribute to the attribute property.
     * 
     * <h4>Example</h4>
     * <code>
     * $oInput->setAttribute( 'data-default', '2' );
     *      // will be same as $this->aAttributes['data-default'] = 2;
     * $oInput->setAttribute( array( 'select', 'multiple' ), 'multiple' );
     *      // will be same as $this->aAttributes['select']['multiple'] = 'multiple';
     * </code>
     * @since       3.5.3
     * @return      void
     */
    public function setAttribute( /* $asAttributeName, $mValue */ ) {
        $_aParams = func_get_args() + array(
            0 => null,
            1 => null,
        );
        
        // $this->aAttributes[ $_aParams[ 0 ] ] = $_aParams[ 1 ];
        $this->setMultiDimensionalArray(
            $this->aAttributes, 
            $this->getElementAsArray( $_aParams, 0 ),   // $asAttributeName
            $_aParams[ 1 ]  // $mValue
        );
    }
    
    /**
     * Updates the attributes by the given array key.
     * 
     * Use this method to generate an attribute array for multiple input items.
     * 
     * @since       3.5.3
     * @return      void
     */
    public function setAttributesByKey( $sKey ) {
        $this->aAttributes = $this->getAttributesByKey( $sKey );
    }
    
    /**
     * Generates an attribute array from the given key based on the attributes set in the constructor.
     * 
     * @return      array       The updated attribute array. 
     * @since       3.5.3
     */
    public function getAttributesByKey() {
        return array();
    }
        
        /**
         * Calculates and returns the attributes as an array.
         * 
         * @since           3.4.0
         * @deprecated      3.5.3       Use `getAttributesByKey()`
         */
        public function getAttributeArray( /* $sKey */ ) {
            $_aParams = func_get_args();
            return call_user_func_array( array( $this, 'getAttributesByKey' ), $_aParams );
        }    
    
 
}
