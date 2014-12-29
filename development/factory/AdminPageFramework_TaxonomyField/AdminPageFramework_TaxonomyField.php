<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for creating fields in the taxonomy page (edit-tags.php).
 * 
 * @abstract
 * @since       3.0.0
 * @package     AdminPageFramework
 * @subpackage  TaxonomyField
 * @extends     AdminPageFramework_Factory
 */
abstract class AdminPageFramework_TaxonomyField extends AdminPageFramework_Factory {
    
    /**
     * Defines the fields type.
     * @since       3.0.0
     * @internal
     */
    static protected $_sFieldsType = 'taxonomy';
    
    /**
     * Constructs the class object instance of AdminPageFramework_TaxonomyField.
     * 
     * Handles setting up properties and hooks.
     * 
     * <h4>Examples</h4>
     * <code>
     * new APF_TaxonomyField( 'apf_sample_taxonomy' ); // taxonomy slug
     * </code>
     * 
     * @since       3.0.0
     * @param       array|string    The taxonomy slug(s). If multiple slugs need to be passed, enclose them in an array and pass the array.
     * @param       string          The option key used for the options table to save the data. By default, the instantiated class name will be applied.
     * @param       string          The access rights. Default: `manage_options`.
     * @param       string          The text domain. Default: `admin-page-framework`.
     * @return      void
     */ 
    function __construct( $asTaxonomySlug, $sOptionKey='', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {
        
        if ( empty( $asTaxonomySlug ) ) { return; }
        
        /* Properties */
        $this->oProp                    = new AdminPageFramework_Property_TaxonomyField( $this, get_class( $this ), $sCapability, $sTextDomain, self::$_sFieldsType );     
        $this->oProp->aTaxonomySlugs    = ( array ) $asTaxonomySlug;
        $this->oProp->sOptionKey        = $sOptionKey ? $sOptionKey : $this->oProp->sClassName;
        
        parent::__construct( $this->oProp );
        
        if ( $this->oProp->bIsAdmin ) {
            
            add_action( 'wp_loaded', array( $this, '_replyToDetermineToLoad' ) );
            
        }
        
        $this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}" );     
        
    }
    
    /**
     * Determines whether the taxonomy fields belong to the loading page.
     * 
     * @internal
     * @since       3.0.3
     * @since       3.2.0   Changed the scope to public from protected as the head tag object will access it.
     */
    public function _isInThePage() {

        if ( 'admin-ajax.php' == $this->oProp->sPageNow ) {
            return true;
        }    
        
        if ( 'edit-tags.php' != $this->oProp->sPageNow ) { 
            return false; 
        }
        
        if ( isset( $_GET['taxonomy'] ) && ! in_array( $_GET['taxonomy'], $this->oProp->aTaxonomySlugs ) ) {
            return false;
        }        
        
        return true;
  

    }    
    
    /**
     * Determines whether the meta box should be loaded in the currently loading page.
     * 
     * @since       3.0.3
     * @internal
     */
    public function _replyToDetermineToLoad( $oScreen ) {
        
        if ( ! $this->_isInThePage() ) { return; }
        
        $this->_setUp();
        $this->oUtil->addAndDoAction( $this, "set_up_{$this->oProp->sClassName}", $this );
        $this->oProp->_bSetupLoaded = true;
        
        // todo: remove the below line
        // $this->_replyToRegisterFormElements( $oScreen );
        add_action( 'current_screen', array( $this, '_replyToRegisterFormElements' ), 20 ); // the screen object should be established to detect the loaded page. 
        
        foreach( $this->oProp->aTaxonomySlugs as $__sTaxonomySlug ) {     
            
            /* Validation callbacks need to be set regardless of whether the current page is edit-tags.php or not */
            add_action( "created_{$__sTaxonomySlug}", array( $this, '_replyToValidateOptions' ), 10, 2 );
            add_action( "edited_{$__sTaxonomySlug}", array( $this, '_replyToValidateOptions' ), 10, 2 );

            // if ( $GLOBALS['pagenow'] != 'admin-ajax.php' && $GLOBALS['pagenow'] != 'edit-tags.php' ) continue;
            add_action( "{$__sTaxonomySlug}_add_form_fields", array( $this, '_replyToAddFieldsWOTableRows' ) );
            add_action( "{$__sTaxonomySlug}_edit_form_fields", array( $this, '_replyToAddFieldsWithTableRows' ) );
            
            add_filter( "manage_edit-{$__sTaxonomySlug}_columns", array( $this, '_replyToManageColumns' ), 10, 1 );
            add_filter( "manage_edit-{$__sTaxonomySlug}_sortable_columns", array( $this, '_replyToSetSortableColumns' ) );
            add_action( "manage_{$__sTaxonomySlug}_custom_column", array( $this, '_replyToSetColumnCell' ), 10, 3 );
            
        }     
        
    }    
    
    /**
     * The set up method.
     * 
     * @remark      should be overridden by the user definition class. 
     * @since       3.0.0
     */
    public function setUp() {}
        
    /**
     * Sets the <var>$aOptions</var> property array in the property object. 
     * 
     * This array will be referred later in the `getFieldOutput()` method.
     * 
     * @since       unknown
     * @since       3.0.0     the scope is changed to protected as the taxonomy field class redefines it.
     * #internal
     * @todo        Add the `options_{instantiated class name}` filter.
     */
    protected function _setOptionArray( $iTermID=null, $sOptionKey ) {
                
        $aOptions               = get_option( $sOptionKey, array() );
        $this->oProp->aOptions  = isset( $iTermID, $aOptions[ $iTermID ] ) ? $aOptions[ $iTermID ] : array();

    }    
    
    /**
     * Adds input fields
     * 
     * @internal
     * @since       3.0.0
     */    
    public function _replyToAddFieldsWOTableRows( $oTerm ) {
        echo $this->_getFieldsOutput( isset( $oTerm->term_id ) ? $oTerm->term_id : null, false );
    }
        
    /**
     * Adds input fields with table rows.
     * 
     * @remark      Used for the Edit Category(taxonomy) page.
     * @internal
     * @since       3.0.0
     */
    public function _replyToAddFieldsWithTableRows( $oTerm ) {
        echo $this->_getFieldsOutput( isset( $oTerm->term_id ) ? $oTerm->term_id : null, true );
    }
    
    /**
     * Modifies the columns of the term listing table in the edit-tags.php page.
     * 
     * @internal
     * @since       3.0.0
     */
    public function _replyToManageColumns( $aColumns ) {

        /* By default something like this is passed.
            Array (
                [cb] => <input type="checkbox" />
                [name] => Name
                [description] => Description
                [slug] => Slug
                [posts] => Admin Page Framework
            ) 
         */
        if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] ) {
            $aColumns = $this->oUtil->addAndApplyFilter( $this, "columns_{$_GET['taxonomy']}", $aColumns );
        }
        $aColumns = $this->oUtil->addAndApplyFilter( $this, "columns_{$this->oProp->sClassName}", $aColumns );    
        return $aColumns;
        
    }
    
    /**
     * 
     * @internal
     * @since 3.0.0
     */
    public function _replyToSetSortableColumns( $aSortableColumns ) {

        if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] ) {
            $aSortableColumns = $this->oUtil->addAndApplyFilter( $this, "sortable_columns_{$_GET['taxonomy']}", $aSortableColumns );
        }
        $aSortableColumns = $this->oUtil->addAndApplyFilter( $this, "sortable_columns_{$this->oProp->sClassName}", $aSortableColumns );
        return $aSortableColumns;
        
    }
    /**
     * 
     * @internal
     * @since       3.0.0
     */
    public function _replyToSetColumnCell( $vValue, $sColumnSlug, $sTermID ) {
        
        $sCellHTML = '';
        if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] ) {
            $sCellHTML = $this->oUtil->addAndApplyFilter( $this, "cell_{$_GET['taxonomy']}", $vValue, $sColumnSlug, $sTermID );
        }
        $sCellHTML = $this->oUtil->addAndApplyFilter( $this, "cell_{$this->oProp->sClassName}", $sCellHTML, $sColumnSlug, $sTermID );
        $sCellHTML = $this->oUtil->addAndApplyFilter( $this, "cell_{$this->oProp->sClassName}_{$sColumnSlug}", $sCellHTML, $sTermID ); // 3.0.2+
        echo $sCellHTML;
                
    }
    
    /**
     * Retrieves the fields output.
     * 
     * @since       3.0.0
     * @internal
     */
    private function _getFieldsOutput( $iTermID, $bRenderTableRow ) {
    
        $_aOutput = array();
        
        /* Set nonce. */
        $_aOutput[] = wp_nonce_field( $this->oProp->sClassHash, $this->oProp->sClassHash, true, false );
        
        /* Set the option property array */
        $this->_setOptionArray( $iTermID, $this->oProp->sOptionKey );
        
        /* Format the fields arrays - taxonomy fields do not support sections */
        $this->oForm->format();
        
        /* Get the field outputs */
        $_oFieldsTable = new AdminPageFramework_FormTable( $this->oProp->aFieldTypeDefinitions, $this->_getFieldErrors(), $this->oMsg );
        $_aOutput[] = $bRenderTableRow 
            ? $_oFieldsTable->getFieldRows( $this->oForm->aFields['_default'], array( $this, '_replyToGetFieldOutput' ) )
            : $_oFieldsTable->getFields( $this->oForm->aFields['_default'], array( $this, '_replyToGetFieldOutput' ) );
                
        /* Filter the output */
        // @todo call the content() method.
        $_sOutput = $this->oUtil->addAndApplyFilters( $this, 'content_' . $this->oProp->sClassName, implode( PHP_EOL, $_aOutput ) );
        
        /* Do action */
        $this->oUtil->addAndDoActions( $this, 'do_' . $this->oProp->sClassName, $this );
            
        return $_sOutput;
    
    }
    
    /**
     * Validates the given option array.
     * 
     * @since       3.0.0
     * @internal
     */
    public function _replyToValidateOptions( $iTermID ) {
        
        if ( ! isset( $_POST[ $this->oProp->sClassHash ] ) ) { return; }
        if ( ! wp_verify_nonce( $_POST[ $this->oProp->sClassHash ], $this->oProp->sClassHash ) ) { return; }
        
        $aTaxonomyFieldOptions  = get_option( $this->oProp->sOptionKey, array() );
        $aOldOptions            = isset( $aTaxonomyFieldOptions[ $iTermID ] ) ? $aTaxonomyFieldOptions[ $iTermID ] : array();
        $aSubmittedOptions      = array();
        foreach( $this->oForm->aFields as $_sSectionID => $_aFields ) {
            foreach( $_aFields as $_sFieldID => $_aField ) {
                if ( isset( $_POST[ $_sFieldID ] ) ) {
                    $aSubmittedOptions[ $_sFieldID ] = $_POST[ $_sFieldID ];
                }
            }
        }
            
        /* Apply validation filters to the submitted option array. */
        // @todo call the validate() method.
        $aSubmittedOptions                  = $this->oUtil->addAndApplyFilters( $this, 'validation_' . $this->oProp->sClassName, $aSubmittedOptions, $aOldOptions, $this );        
        $aTaxonomyFieldOptions[ $iTermID ]  = $this->oUtil->uniteArrays( $aSubmittedOptions, $aOldOptions );
        update_option( $this->oProp->sOptionKey, $aTaxonomyFieldOptions );
        
    }

    /**
     * Registers form fields and sections.
     * 
     * @since       3.0.0
     * @internal
     */
    public function _replyToRegisterFormElements( $oScreen ) {
              
        $this->_loadDefaultFieldTypeDefinitions();
        
        // Format the fields array.
        $this->oForm->format();
        $this->oForm->applyConditions();
        
        // @todo    Examine whether applyFiltersToFields() should be performed here or not.
        // @todo    Examine whether setDynamicElements() should be performed here or not.
        
        $this->_registerFields( $this->oForm->aConditionedFields );
        
    }    
    
    /**
     * Redirects undefined callback methods.
     * @internal
     * @since       3.0.0
     * @deprecated
     */
    function ___call( $sMethodName, $aArgs=null ) {     
    
        if ( has_filter( $sMethodName ) ) {
            return isset( $aArgs[ 0 ] ) ? $aArgs[ 0 ] : null;
        }  
        
        return parent::__call( $sMethodName, $aArgs );
        
    }
    
}