<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides public methods to add form elements. 
 *
 * @abstract
 * @since           2.0.0
 * @since           3.3.1       Changed the name from `AdminPageFramework_Setting`.
 * @extends         AdminPageFramework_Setting_Validation
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 */
abstract class AdminPageFramework_Form_Controller extends AdminPageFramework_Form_View {
                                    
    /**
     * {@inheritdoc}
     * 
     * {@inheritdoc}
     * 
     * <h4>Example</h4>
     * <code>$this->addSettingSections(
     *      array(
     *          'section_id'    => 'text_fields',
     *          'page_slug'     => 'first_page',
     *          'tab_slug'      => 'textfields',
     *          'title'         => 'Text Fields',
     *          'description'   => 'These are text type fields.',
     *          'order'         => 10,
     *      ),    
     *      array(
     *          'section_id'    => 'selectors',
     *          'page_slug'     => 'first_page',
     *          'tab_slug'      => 'selectors',
     *          'title'         => 'Selectors',
     *          'description'   => 'These are selector type options such as dropdown lists, radio buttons, and checkboxes',
     *      )
     * );</code>
     *
     * @since       2.0.0
     * @since       3.0.0           Changed the scope to public from protected.
     * @access      public
     * @remark      Accepts variadic parameters; the number of accepted parameters are not limited to three.
     * @remark      The actual registration will be performed in the <em>_replyToRegisterSettings()</em> method with the <em>admin_menu</em> hook.
     * @remark      The target section tab slug and the target tab slug will be reset once the method returns.
     * @param       array|string    the section array or the target page slug. If the target page slug is set, the next section array can omit the page slug key.
     * <h4>Section Array</h4>
     * <ul>
     *      <li>**section_id** - (required, string) the section ID. Avoid using non-alphabetic characters except underscore and numbers.</li>
     *      <li>**page_slug** - (optional, string) the page slug that the section belongs to. If the target page slug is set, it can be omitted.</li>
     *      <li>**tab_slug** - (optional, string) the tab slug that the section belongs to. The tab here refers to in-page tabs.</li>
     *      <li>**section_tab_slug** - (optional, string) [3.0.0+] the section tab slug that the section are grouped into. The tab here refers to section tabs.</li>
     *      <li>**title** - (optional, string) the title of the section.</li>
     *      <li>**capability** - (optional, string) the <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> of the section. If the page visitor does not have sufficient capability, the section will be invisible to them.</li>
     *      <li>**if** - (optional, boolean) if the passed value is false, the section will not be registered.</li>
     *      <li>**order** - (optional, integer) the order number of the section. The higher the number is, the lower the position it gets.</li>
     *      <li>**help** - (optional, string) the help description added to the contextual help tab.</li>
     *      <li>**help_aside** - (optional, string) the additional help description for the side bar of the contextual help tab.</li>
     *      <li>**repeatable** - (optional, boolean|array) [3.0.0+] Indicates whether or not the section is repeatable. To set a minimum/maximum number of sections, pass an array with the key, `min`, and `max`. e.g. `array( 'min' => 3, 'max' => 10 )`</li>
     * </ul>
     * @param       array           (optional) another section array.
     * @param       array           (optional) add more section array to the next parameters as many as necessary.
     * @return      void
     */     
    public function addSettingSections( $aSection1, $aSection2=null, $_and_more=null ) {
        
        foreach( func_get_args() as $asSection ) { 
            $this->addSettingSection( $asSection ); 
        }
        
        // reset the stored target tab slug and the target section tab slug
        $this->_sTargetTabSlug          = null;
        $this->_sTargetSectionTabSlug   = null;
        
    }
    
    /**
     * A singular form of the adSettingSections() method which takes only a single parameter.
     * 
     * This is useful when adding section arrays in loops.
     * 
     * @since       2.1.2
     * @since       3.0.0           Changed the scope to public from protected.
     * @access      public
     * @param       array|string    the section array. If a string is passed, it is considered as a target page slug that will be used as a page slug element from the next call so that the element can be omitted.
     * @return      void
     */
    public function addSettingSection( $asSection ) {
                
        if ( ! is_array( $asSection ) ) {
            $this->_sTargetPageSlug = is_string( $asSection ) ? $asSection : $this->_sTargetPageSlug;
            return;
        } 
        
        $aSection = $asSection;
        $this->_sTargetPageSlug         = isset( $aSection['page_slug'] ) ? $aSection['page_slug'] : $this->_sTargetPageSlug;
        $this->_sTargetTabSlug          = isset( $aSection['tab_slug'] ) ? $aSection['tab_slug'] : $this->_sTargetTabSlug;
        $this->_sTargetSectionTabSlug   = isset( $aSection['section_tab_slug'] ) ? $aSection['section_tab_slug'] : $this->_sTargetSectionTabSlug;
        $aSection = $this->oUtil->uniteArrays( 
            $aSection, 
            array( 
                'page_slug'         => $this->_sTargetPageSlug ? $this->_sTargetPageSlug : null, // checking the value allows the user to reset the internal target manually
                'tab_slug'          => $this->_sTargetTabSlug ? $this->_sTargetTabSlug : null,
                'section_tab_slug'  => $this->_sTargetSectionTabSlug ? $this->_sTargetSectionTabSlug : null,
            )
        ); // avoid undefined index warnings.
        
        $aSection['page_slug']          = $aSection['page_slug'] ? $this->oUtil->sanitizeSlug( $aSection['page_slug'] ) : ( $this->oProp->sDefaultPageSlug ? $this->oProp->sDefaultPageSlug : null );
        $aSection['tab_slug']           = $this->oUtil->sanitizeSlug( $aSection['tab_slug'] );
        $aSection['section_tab_slug']   = $this->oUtil->sanitizeSlug( $aSection['section_tab_slug'] );
        
        // The page slug is necessary.
        if ( ! $aSection['page_slug'] ) { return; }
        $this->oForm->addSection( $aSection );
        
    }
    
    /**
    * Removes the given section(s) by section ID.
    * 
    * This accesses the property storing the added section arrays and removes the specified ones.
    * 
    * <h4>Example</h4>
    * <code>$this->removeSettingSections( 'text_fields', 'selectors', 'another_section', 'yet_another_section' );
    * </code>
    * 
    * @since        2.0.0
    * @since        3.0.0       Changed the scope to public from protected.
    * @access       public
    * @remark       Accepts variadic parameters; the number of accepted parameters are not limited to three.
    * @param        string      $sSectionID1        the section ID to remove.
    * @param        string      $sSectionID2        (optional) another section ID to remove.
    * @param        string      $_and_more          (optional) add more section IDs to the next parameters as many as necessary.
    * @return       void
    */    
    public function removeSettingSections( $sSectionID1=null, $sSectionID2=null, $_and_more=null ) {    
        
        foreach( func_get_args() as $_sSectionID ) {
            $this->oForm->removeSection( $_sSectionID );
        }
        
    }
    
    /**
     * {@inheritdoc}
     * 
     * {@inheritdoc}
     * 
     * <h4>Example</h4>
     * <code>$this->addSettingFields(
     *      array(
     *          'field_id'      => 'text',
     *          'section_id'    => 'text_fields',
     *          'title'         => __( 'Text', 'my-text-domain' ),
     *          'description'   => __( 'Type something here.', 'my-text-domain' ),
     *          'type'          => 'text',
     *          'order'         => 1,
     *          'default'       => 123456,
     *      ),    
     *      array(
     *          'field_id'      => 'text_multiple',
     *          'section_id'    => 'text_fields',
     *          'title'         => __( 'Multiple Text Fields', 'my-text-domain' ),
     *          'description'   => __( 'These are multiple text fields.', 'my-text-domain' ),
     *          'type'          => 'text',
     *          'order'         => 2,
     *          'default'       => __( 'Hello World', 'my-text-domain' ),
     *          'label'         => __( 'First Item', 'my-text-domain' ),
     *          'attributes' => array(
     *              'size' => 30
     *          ),
     *          array(
     *              'label'         => __( 'Second Item', 'my-text-domain' ),
     *              'default'       => __( 'Foo bar', 'my-text-domain' ),
     *              'attributes'    => array(
     *                  'size' => 60,
     *              ),
     *          ),
     *          array(
     *              'label'         => __( 'Third Item', 'my-text-domain' ),
     *              'default'       => __( 'Yes, we can.', 'my-text-domain' ),
     *              'attributes' => array(
     *                  'size' => 90,
     *              ),
     *          ),
     *      )
     * );</code>
     * 
     * @since       2.0.0
     * @since       3.0.0 Changed the scope to public from protected.
     * @access      public
     * @remark      Accepts variadic parameters; the number of accepted parameters are not limited to three.
     * @remark      The actual registration will be performed in the <em>_replyToRegisterSettings()</em> method with the <em>admin_menu</em> hook.
     */     
    public function addSettingFields( $aField1, $aField2=null, $_and_more=null ) {    
        foreach( func_get_args() as $aField ) { 
            $this->addSettingField( $aField ); 
        }
    }
    /**
    * Adds the given field array items into the field array property.
    * 
    * Identical to the `addSettingFields()` method except that this method does not accept enumerated parameters. 
    * 
    * @since        2.1.2
    * @since        3.0.0           Changed the scope to public from protected.
    * @access       public
    * @param        array|string    $asField        the field array or the target section ID. If the target section ID is set, the section_id key can be omitted from the next passing field array.
    * @return       void
    */    
    public function addSettingField( $asField ) {
        $this->oForm->addField( $asField );    
    }    
    
    /**
    * Removes the given field(s) by field ID.
    * 
    * This accesses the property storing the added field arrays and removes the specified ones.
    * 
    * <h4>Example</h4>
    * <code>$this->removeSettingFields( 'fieldID_A', 'fieldID_B', 'fieldID_C', 'fieldID_D' );
    * </code>
    * 
    * @since        2.0.0
    * @since        3.0.0       Changed the scope to public from protected.
    * @access       public
    * @remark       Accepts variadic parameters; the number of accepted parameters are not limited to three.
    * @param        string      $sFieldID1      the field ID to remove.
    * @param        string      $sFieldID2      (optional) another field ID to remove.
    * @param        string      $_and_more      (optional) add more field IDs to the next parameters as many as necessary.
    * @return void
    */    
    public function removeSettingFields( $sFieldID1, $sFieldID2=null, $_and_more ) {
        foreach( func_get_args() as $_sFieldID ) { 
            $this->oForm->removeField( $_sFieldID ); 
        }
    }    
            
    /**
     * Retrieves the specified field value stored in the options by field ID.
     * 
     * <h4>Example</h4>
     * <code>
     *  $this->addSettingFields(
     *      'number_section',  // section id
     *      array( 
     *          'field_id'          => 'preset_field',
     *          'title'             => __( 'Preset', 'admin-page-framework-demo' ),
     *          'type'              => 'number',
     *      ),
     *      array( 
     *          'field_id'          => 'value_based_on_preset',
     *          'title'             => __( 'Value Based on Preset', 'admin-page-framework-demo' ),
     *          'type'              => 'number',
     *          'default'           => 10 + ( int ) $this->getValue( 
     *              'number_section',   // section id
     *              'preset_field'      // field id
     *          ),
     *      ),    
     *  );
     * </code>
     * 
     * @since       3.3.0
     * @since       3.3.5           Made it respect last input arrays.
     * @param       The key that points the dimensional array key of the options array.
     */
    public function getValue() {
        
        $_aParams   = func_get_args();        
        return AdminPageFramework_WPUtility::getOption( 
            $this->oProp->sOptionKey, 
            $_aParams, 
            null,            // default
            $this->getSavedOptions() + $this->oProp->getDefaultOptions( $this->oForm->aFields ) // additional array to merge with the options
        );
        
    }
            
    /**
     * Retrieves the specified field value stored in the options by field ID.
     *  
     * @since       2.1.2
     * @since       3.0.0       Changed the scope to public from protected. Dropped the sections. Made it return a default value even if it's not saved in the database.
     * @access      public
     * @param       string      $sFieldID         The field ID.
     * @param       string      $sSectionID       The section ID.
     * @return      array|string|null       If the field ID is not set in the saved option array, it will return null. Otherwise, the set value.
     * If the user has not submitted the form, the framework will try to return the default value set in the field definition array.
     * @deprecated  3.3.0
     */
    public function getFieldValue( $sFieldID, $sSectionID='' ) {
                               
        trigger_error( 'Admin Page Framework: ' . ' : ' . sprintf( __( 'The method is deprecated: %1$s. Use %2$s instead.', $this->oProp->sTextDomain ), __METHOD__, 'getValue()' ), E_USER_WARNING );
    
        $_aOptions = $this->oUtil->uniteArrays( $this->oProp->aOptions, $this->oProp->getDefaultOptions( $this->oForm->aFields ) );
        /* If it's saved, return it */
        if ( ! $sSectionID ) {
            if ( array_key_exists( $sFieldID, $_aOptions ) ) {
                return $_aOptions[ $sFieldID ];
            }    
            // loop through section elements
            foreach( $_aOptions as $aOptions ) {
                if ( array_key_exists( $sFieldID, $aOptions ) ) {
                    return $aOptions[ $sFieldID ];
                }
            }
        }
        if ( $sSectionID ) {
            if ( array_key_exists( $sSectionID, $_aOptions ) && array_key_exists( $sFieldID, $_aOptions[ $sSectionID ] ) ) {
                return $_aOptions[ $sSectionID ][ $sFieldID ];
            }
        }
        return null;
                    
    }
            
}