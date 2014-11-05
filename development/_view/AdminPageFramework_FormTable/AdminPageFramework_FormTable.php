<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FormTable' ) ) :
/**
 * Provides methods to render setting sections and fields.
 * 
 * @package AdminPageFramework
 * @subpackage Form
 * @since 3.0.0
 * @internal
 */
class AdminPageFramework_FormTable extends AdminPageFramework_FormTable_Base {
        
    /**
     * Returns a set of HTML table outputs consisting of form sections and fields.
     * 
     * @since 3.0.0
     */
    public function getFormTables( $aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback ) {
        
        $_aOutput = array();
        foreach( $this->_getSectionsBySectionTabs( $aSections ) as $_sSectionTabSlug => $_aSections ) {
            $_sSectionSet = $this->_getFormTablesBySectionTab( $_sSectionTabSlug, $_aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback );
            if ( $_sSectionSet ) {
                $_aOutput[] = "<div " . $this->generateAttributes(
                        array(
                            'class' => 'admin-page-framework-sectionset',
                            'id'    => "sectionset-{$_sSectionTabSlug}_" . md5( serialize( $_aSections ) ),
                        )
                    ) . ">" 
                        . $_sSectionSet
                    . "</div>";
            }
        }
        return implode( PHP_EOL, $_aOutput ) 
            . $this->_getSectionTabsEnablerScript();
            
    }
        
        /**
         * Indicates whether the tab enabler script is loaded or not.
         */
        static private $_bLoadedTabEnablerScript = false;
        
        /**
         * Returns the JavaScript script that enables section tabs.
         * 
         * @since 3.0.0
         */
        private function _getSectionTabsEnablerScript() {
            
            if ( self::$_bLoadedTabEnablerScript ) { return ''; }
            self::$_bLoadedTabEnablerScript = true;
            $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function() {
    // the parent element of the ul tag; The ul element holds li tags of titles.
    jQuery( '.admin-page-framework-section-tabs-contents' ).createTabs(); 
});            
JAVASCRIPTS;
            return "<script type='text/javascript'>"
                . $_sScript
            . "</script>";
            
        }
        
        /**
         * Returns an output string of form tables.
         * 
         * @since 3.0.0
         */
        private function _getFormTablesBySectionTab( $sSectionTabSlug, $aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback ) {

            // if empty, return a blank string.
            if ( empty( $aSections ) ) { return ''; } 
        
            /* <ul>
                <li><a href="#tabs-1">Nunc tincidunt</a></li>
                <li><a href="#tabs-2">Proin dolor</a></li>
                <li><a href="#tabs-3">Aenean lacinia</a></li>
            </ul>  */     
            $_aSectionTabList = array();

            $_aOutput = array();
            foreach( $aFieldsInSections as $_sSectionID => $aSubSectionsOrFields ) {
                
                if ( ! isset( $aSections[ $_sSectionID ] ) ) { continue; }
                
                $_sSectionTabSlug   = $aSections[ $_sSectionID ]['section_tab_slug']; // will be referred outside the loop.
                $_aTabAttributes    = $aSections[ $_sSectionID ]['attributes']['tab'] + array( 'style' => null );
                $_sExtraClasses     = $aSections[ $_sSectionID ]['class']['tab'];
                
                // For repeatable sections
                $_aSubSections      = $aSubSectionsOrFields;
                $_aSubSections      = $this->getIntegerElements( $_aSubSections );
                $_iCountSubSections = count( $_aSubSections ); // Check sub-sections.
                if ( $_iCountSubSections ) {

                    // Add the repeatable sections enabler script.
                    if ( $aSections[ $_sSectionID ]['repeatable'] ) {
                        $_aOutput[] = $this->getRepeatableSectionsEnablerScript( 'sections-' .  md5( serialize( $aSections ) ), $_iCountSubSections, $aSections[ $_sSectionID ]['repeatable'] );    
                    }
                    
                    // Get the section tables.
                    foreach( $this->numerizeElements( $_aSubSections ) as $_iIndex => $_aFields ) { // will include the main section as well.
                    
                        $_sSectionTagID = 'section-' . $_sSectionID . '__' . $_iIndex;
                        
                        // For tabbed sections,
                        if ( $aSections[ $_sSectionID ]['section_tab_slug'] ) {

                            $__aTabAttributes = $_aTabAttributes    // note that this attribute array is defined outside the loop.
                                + array(
                                    'class' => 'admin-page-framework-section-tab nav-tab',
                                    'id'    => "section_tab-{$_sSectionTagID}",
                                );
                            $__aTabAttributes['class'] = $this->generateClassAttribute( $__aTabAttributes['class'], $_sExtraClasses );  // 3.3.1+
                            $__aTabAttributes['style'] = $this->generateStyleAttribute( $__aTabAttributes['style'], $aSections[ $_sSectionID ]['hidden'] ? 'display:none' : null );  // 3.3.1+        
                            $_aSectionTabList[] = "<li " . $this->generateAttributes( $__aTabAttributes ) . ">"
                                    . "<a href='#{$_sSectionTagID}'>"
                                        . $this->_getSectionTitle( $aSections[ $_sSectionID ]['title'], 'h4', $_aFields, $hfFieldCallback )
                                    ."</a>"
                                . "</li>";
                                
                        }
                    
                        $_aOutput[] = $this->getFormTable( $_sSectionID, $_iIndex, $aSections[ $_sSectionID ], $_aFields, $hfSectionCallback, $hfFieldCallback );
                        
                    }
                    
                } else {
                    // The normal section
                    $_sSectionTagID = 'section-' . $_sSectionID . '__' . '0';
                    $_aFields       = $aSubSectionsOrFields;
                    
                    // For tabbed sections,
                    if ( $aSections[ $_sSectionID ]['section_tab_slug'] ) {

                        $__aTabAttributes = $_aTabAttributes + array(
                            'class' => 'admin-page-framework-section-tab nav-tab',
                            'id'    => "section_tab-{$_sSectionTagID}",
                        );         
                        $__aTabAttributes['class'] = $this->generateClassAttribute( $__aTabAttributes['class'], $_sExtraClasses );
                        $__aTabAttributes['style'] = $this->generateStyleAttribute( $__aTabAttributes['style'], $aSections[ $_sSectionID ]['hidden'] ? 'display:none' : null );  // 3.3.1+        
                        $_aSectionTabList[] = "<li " . $this->generateAttributes( $__aTabAttributes ) . ">"
                                . "<a href='#{$_sSectionTagID}'>"
                                    . $this->_getSectionTitle( $aSections[ $_sSectionID ]['title'], 'h4', $_aFields, $hfFieldCallback )    
                                . "</a>"
                            . "</li>";
                            
                    }
                    
                    $_aOutput[] = $this->getFormTable( $_sSectionID, 0, $aSections[ $_sSectionID ], $_aFields, $hfSectionCallback, $hfFieldCallback );
                }
                    
            }
            
            return empty( $_aOutput )
                ? ''
                : "<div " . $this->generateAttributes(
                        array(
                            'class' => 'admin-page-framework-sections'
                                . ( ! $_sSectionTabSlug || $_sSectionTabSlug == '_default' ? null : ' admin-page-framework-section-tabs-contents' ),
                            'id'    => "sections-" . md5( serialize( $aSections ) ), 
                        )
                    ) . ">"                 
                    . ( $_sSectionTabSlug // if the section tab slug yields true, insert the section tab list
                        ? "<ul class='admin-page-framework-section-tabs nav-tab-wrapper'>" . implode( PHP_EOL, $_aSectionTabList ) . "</ul>"
                        : ''
                    )    
                    . implode( PHP_EOL, $_aOutput )
                . "</div>";
            
        }
        
        /**
         * Returns the section title output.
         * 
         * @since 3.0.0
         */
        private function _getSectionTitle( $sTitle, $sTag, $aFields, $hfFieldCallback ) {
            
            $aSectionTitleField = $this->_getSectionTitleField( $aFields );
            return $aSectionTitleField
                ? call_user_func_array( $hfFieldCallback, array( $aSectionTitleField ) )
                : "<{$sTag}>" . $sTitle . "</{$sTag}>";
            
        }
        
        /**
         * Returns the first found section_title field.
         * 
         * @since 3.0.0
         */
        private function _getSectionTitleField( $aFields ) {
            
            foreach( $aFields as $aField ) {
                if ( 'section_title' === $aField['type'] ) {
                    return $aField; // will return the first found one.
                }
            }
            
        }
        
        /**
         * Returns an array holding section definition array by section tab.
         * 
         * @since 3.0.0
         */
        private function _getSectionsBySectionTabs( array $aSections ) {

            $_aSectionsBySectionTab = array();
            $iIndex = 0;
            // $_aSectionsBySectionTab = array( '_default' => array() );
            foreach( $aSections as $_aSection ) {
                
                if ( ! $_aSection['section_tab_slug'] ) {
                    $_aSectionsBySectionTab[ '_default_' . $iIndex ][ $_aSection['section_id'] ] = $_aSection;
                    $iIndex++;
                    continue;
                }
                    
                $_sSectionTaqbSlug = $_aSection['section_tab_slug'];
                $_aSectionsBySectionTab[ $_sSectionTaqbSlug ] = isset( $_aSectionsBySectionTab[ $_sSectionTaqbSlug ] ) && is_array( $_aSectionsBySectionTab[ $_sSectionTaqbSlug ] )
                    ? $_aSectionsBySectionTab[ $_sSectionTaqbSlug ]
                    : array();
                
                $_aSectionsBySectionTab[ $_sSectionTaqbSlug ][ $_aSection['section_id'] ] = $_aSection;
                
            }
            return $_aSectionsBySectionTab;
            
        }

        /**
         * Returns the enabler script for repeatable sections.
         * @since 3.0.0
         */
        private function getRepeatableSectionsEnablerScript( $sContainerTagID, $iSectionCount, $aSettings ) {
            
            new AdminPageFramework_Script_RepeatableSection( $this->oMsg );            
            
            if ( empty( $aSettings ) ) return '';     
            $aSettings              = ( is_array( $aSettings ) ? $aSettings : array() ) + array( 'min' => 0, 'max' => 0 ); // do not cast array since it creates a zero key for an empty variable.
            
            $_sAdd                  = $this->oMsg->get( 'add_section' );
            $_sRemove               = $this->oMsg->get( 'remove_section' );
            $_sVisibility           = $iSectionCount <= 1 ? " style='display:none;'" : "";
            $_sSettingsAttributes   = $this->generateDataAttributes( $aSettings );
            $_sButtons              = 
                "<div class='admin-page-framework-repeatable-section-buttons' {$_sSettingsAttributes} >"
                    . "<a class='repeatable-section-remove button-secondary repeatable-section-button button button-large' href='#' title='{$_sRemove}' {$_sVisibility} data-id='{$sContainerTagID}'>-</a>"
                    . "<a class='repeatable-section-add button-secondary repeatable-section-button button button-large' href='#' title='{$_sAdd}' data-id='{$sContainerTagID}'>+</a>"
                . "</div>";
            $_sButtonsHTML  = '"' . $_sButtons . '"';
            $_aJSArray      = json_encode( $aSettings );
            $_sScript       = <<<JAVASCRIPTS
jQuery( document ).ready( function() {
    // Adds the buttons
    jQuery( '#{$sContainerTagID} .admin-page-framework-section-caption' ).show().prepend( $_sButtonsHTML );
    // Update the fields     
    jQuery( '#{$sContainerTagID}' ).updateAPFRepeatableSections( $_aJSArray ); 
});            
JAVASCRIPTS;
            return
                "<script type='text/javascript'>"
                    . $_sScript
                . "</script>";
                
        }
        
    /**
     * Returns a single HTML table output of a set of fields generated from the given field definition arrays.
     * 
     * @since       3.0.0
     * @since       3.3.1       Now the first parameter is for the section ID not the tag ID.
     * @param       string      $sSectionID          The section ID specified by the user.
     * @param       integer     $iSectionIndex       The section index. Zero based.
     * @param       array       $aSection            The section definition array,
     * @param       array       $sFields             The array holding field definition arrays.
     * @param       callable    $hfSectionCallback   The callback for the section header output.
     * @param       callable    $hfFieldCallback     The callback for the field output.
     */
    public function getFormTable( $sSectionID, $iSectionIndex, $aSection, $aFields, $hfSectionCallback, $hfFieldCallback ) {

        if ( count( $aFields ) <= 0 ) { return ''; }
        
        $_sSectionTagID = 'section-' . $sSectionID . '__' . $iSectionIndex;
        
        // For regular repeatable fields, the title should be omitted except the first item.
        $_sDisplayNone  = ( $aSection['repeatable'] && $iSectionIndex != 0 && ! $aSection['section_tab_slug'] )
            ? " style='display:none;'"
            : '';
                
        $_sSectionError = isset( $this->aFieldErrors[ $aSection['section_id'] ] ) && is_string( $this->aFieldErrors[ $aSection['section_id'] ] )
            ? $this->aFieldErrors[ $aSection['section_id'] ]
            : '';
                
        $_aOutput = array();
        $_aOutput[] = "<table "
            . $this->generateAttributes(  
                    array( 
                        'id'    => 'section_table-' . $_sSectionTagID,
                        'class' => 'form-table', // temporarily deprecated: admin-page-framework-section-table
                    )
                )
            . ">"
                . ( $aSection['description'] || $aSection['title'] 
                    ? "<caption class='admin-page-framework-section-caption' data-section_tab='{$aSection['section_tab_slug']}'>" // data-section_tab is referred by the repeater script to hide/show the title and the description
                            . ( $aSection['title'] && ! $aSection['section_tab_slug']
                                ? "<div class='admin-page-framework-section-title' {$_sDisplayNone}>" 
                                        .  $this->_getSectionTitle( $aSection['title'], 'h3', $aFields, $hfFieldCallback )    
                                    . "</div>"
                                : ""
                            )     
                            . ( is_callable( $hfSectionCallback )
                                ? "<div class='admin-page-framework-section-description'>"     // admin-page-framework-section-description is referred by the repeatable section buttons
                                        . call_user_func_array( $hfSectionCallback, array( $this->_getDescription( $aSection['description'] ) , $aSection ) )
                                    . "</div>"
                                : ""
                            )
                            . ( $_sSectionError  
                                ? "<div class='admin-page-framework-error'><span class='section-error'>* " . $_sSectionError .  "</span></div>"
                                : ''
                            )
                        . "</caption>"
                    : "<caption class='admin-page-framework-section-caption' style='display:none;'></caption>"
                )
                . $this->getFieldRows( $aFields, $hfFieldCallback )
            . "</table>";
            
        $_aSectionAttributes    = $this->uniteArrays(
            $this->dropElementsByType( $aSection['attributes'] ),   // remove elements of an array.
            array( 
                'id'            => $_sSectionTagID, // section-{section id}__{index}
                'class'         => 'admin-page-framework-section'
                    . ( $aSection['section_tab_slug'] ? ' admin-page-framework-tab-content' : null ),
                // [3.3.1+] The repeatable script refers to this model value to generate new IDs.
                'data-id_model' => 'section-' . $sSectionID . '__' . '-si-',
            )     
        );
        $_aSectionAttributes['class']   = $this->generateClassAttribute( $_aSectionAttributes['class'], $this->dropElementsByType( $aSection['class'] ) );  // 3.3.1+
        $_aSectionAttributes['style']   = $this->generateStyleAttribute( $_aSectionAttributes['style'], $aSection['hidden'] ? 'display:none' : null );  // 3.3.1+        

        return "<div "
                . $this->generateAttributes( $_aSectionAttributes )
            . ">"
                . implode( PHP_EOL, $_aOutput )
            . "</div>";
        
    }
        /**
         * Returns the HTML formatted description blocks by the given description definition.
         * 
         * @since   3.3.0
         * @return  string      The description output.
         */
        private function _getDescription( $asDescription ) {
            
            if ( empty( $asDescription ) ) { return ''; }
            
            $_aOutput = array();
            foreach( $this->getAsArray( $asDescription ) as $_sDescription ) {
                $_aOutput[] = "<p class='admin-page-framework-section-description'>"
                        . "<span class='description'>{$_sDescription}</span>"
                    . "</p>";
            }
            return implode( PHP_EOL, $_aOutput );
            
        }    
    /**
     * Returns the output of a set of fields generated from the given field definition arrays enclosed in a table row tag for each.
     * 
     * @since 3.0.0    
     */
    public function getFieldRows( $aFields, $hfCallback ) {
        
        if ( ! is_callable( $hfCallback ) ) { return ''; }
        $_aOutput = array();
        foreach( $aFields as $aField ) {
            $_aOutput[] = $this->_getFieldRow( $aField, $hfCallback );
        } 
        return implode( PHP_EOL, $_aOutput );
        
    }
        
        /**
         * Returns the field output enclosed in a table row.
         * 
         * @since 3.0.0
         */
        protected function _getFieldRow( $aField, $hfCallback ) {
            
            if ( 'section_title' === $aField['type'] ) { return ''; }
            
            $_aOutput           = array();
            $_aField            = $this->_mergeDefault( $aField );
            $_sAttributes_TR    = $this->_getFieldContainerAttributes( 
                $_aField,
                array( 
                    'id'        => 'fieldrow-' . AdminPageFramework_FormField::_getInputTagBaseID( $_aField ),
                    'valign'    => 'top',
                    'class'     => 'admin-page-framework-fieldrow',
                ),
                'fieldrow'
            );
            $_sAttributes_TD    = $this->generateAttributes( 
                array(
                    'colspan'   => $_aField['show_title_column'] ? 1 : 2,
                    'class'     => $_aField['show_title_column'] ? null : 'admin-page-framework-field-td-no-title',
                )
            );
            $_aOutput[] = "<tr {$_sAttributes_TR}>";
                if ( $_aField['show_title_column'] ) {
                    $_aOutput[] = "<th>" . $this->_getFieldTitle( $_aField ) . "</th>";
                }
                $_aOutput[] = "<td {$_sAttributes_TD}>" 
                        . call_user_func_array( $hfCallback, array( $aField ) ) 
                    . "</td>"; // $aField is passed, not $_aField as $_aField do not respect subfields.
            $_aOutput[] = "</tr>";
            return implode( PHP_EOL, $_aOutput );
                
        }
    
    /**
     * Returns a set of fields output from the given field definition array.
     * 
     * @remark This is similar to getFieldRows() but without the enclosing table row tag. Used for taxonomy fields.
     * @since 3.0.0
     */
    public function getFields( $aFields, $hfCallback ) {
        
        if ( ! is_callable( $hfCallback ) ) { return ''; }
        $_aOutput = array();
        foreach( $aFields as $_aField ) {
            $_aOutput[] = $this->_getField( $_aField, $hfCallback );
        }
        return implode( PHP_EOL, $_aOutput );
        
    }
    
        /**
         * Returns the given field output without a table row tag.
         * 
         * @internal
         * @since 3.0.0
         */
        protected function _getField( $aField, $hfCallback )  {
            
            if ( 'section_title' === $aField['type'] ) { return ''; }
            $_aOutput   = array();
            $_aField    = $this->_mergeDefault( $aField );
            $_aOutput[] = "<div " . $this->_getFieldContainerAttributes( $_aField, array(), 'fieldrow' ) . ">";
            if ( $_aField['show_title_column'] ) {
                $_aOutput[] = $this->_getFieldTitle( $_aField );
            }
            $_aOutput[] = call_user_func_array( $hfCallback, array( $aField ) ); // $aField is passed, not $_aField as $_aField do not respect subfields.
            $_aOutput[] = "</div>";
            return implode( PHP_EOL, $_aOutput );     
            
        }
            
}
endif;