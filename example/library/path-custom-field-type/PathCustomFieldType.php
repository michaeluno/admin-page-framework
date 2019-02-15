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

if ( ! class_exists( 'PathCustomFieldType' ) ) :
/**
 * A field type that lets the user pick a file located on the server.
 * 
 * @since       3.8.4
 * @version     0.0.3b
 * @requires    Admin Page Framework 3.8.8
 */
class PathCustomFieldType extends AdminPageFramework_FieldType_image {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'path', );

    /**
     * Defines the default key-values of this field type settings.
     *
     * @remark\ $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes'    =>  array(
            'input'         => array(),
            'remove_button' => array(),
            'select_button' => array(),
        ),
        /**
         * @see     https://github.com/jqueryfiletree/jqueryfiletree#configuring-the-file-tree
         */
        'options'   => array(
            'root'              => '/',          // (string) root folder to display, relative to the web document root.
            // 'script'         => null,         // (disabled) location url of the serverside AJAX file to use	"jqueryFileTree.php"
            'folderEvent'       => 'click',      // (string) event to trigger expand/collapse
            'expandSpeed'       => 500,          // (string|integer) Speed to expand branches (in ms); use -1 for no animation
            'collapseSpeed'     => 500,          // (string|integer) Speed to collapse branches (in ms); use -1 for no animation
            'expandEasing'      => 'swing',      // (string) Easing function to use on expand
            'collapseEasing'    => 'swing',      // (string) Easing function to use on collapse
            'multiFolder'       => true,         // (boolean|string) Whether or not to limit the browser to one subfolder at a time
            'loadMessage'       => 'Loading...', // (string) Message to display while initial tree loads (can be HTML)
            'errorMessage'      => "Unable to get file tree information", // (string) Message to display if unable to load tree
            'multiSelect'       => false,        // (boolean|string) Append checkbox to each line item to select more than one
            'onlyFolders'       => false,        // (boolean|string) Filter files and only return folders
            'onlyFiles'         => false,        // (boolean|string) Filter folders and only return files
            'preventLinkAction' => false,        // (boolean|string) Prevents default link-clicking action from occurring. This, in effect, prevents the page from resetting to the top.	
            'fileExtensions'    => '',           // (string) file extensions to be listed without a dot, separated with commas. e.g. php,txt,js
        ),
    );
    
    protected function construct() {
    }
    
    /**
     * Loads the field type necessary components.
     */
    public function setUp() {
        add_thickbox();
    }

        /**
         * Normalize a file system path.
         *
         * @since       3.8.4
         * @return      string
         */
        private function _getPathNormalized( $sPath ) {
            $sPath = str_replace( '\\', '/', $sPath );
            $sPath = preg_replace( '|(?<=.)/+|', '/', $sPath );
            if ( ':' === substr( $sPath, 1, 1 ) ) {
                $sPath = ucfirst( $sPath );
            }
            return $sPath;
        }            
            
    /**
     * Returns an array holding the urls of enqueuing scripts.
     * @return      array
     */
    protected function getEnqueuingScripts() {
        return array(
            array( 
                // 'src'           => dirname( __FILE__ ) . '/js/jquery.easing.js',
                'src'           => dirname( __FILE__ ) . '/js/jQueryFileTree.js',
                'dependencies'  => array( 'jquery' ) 
            ),
        );
    }

    /**
     * @return      array
     */
    protected function getEnqueuingStyles() {
        return array(
            dirname( __FILE__ ) . '/css/jQueryFileTree.min.css',
            dirname( __FILE__ ) . '/css/style.css',
        );
    }

    /**
     * Returns the field type specific JavaScript script.
     */
    protected function getScripts() {

        $_aJSArray            = json_encode( $this->aFieldTypeSlugs );
        $_sConnectorScriptURL = $this->getSRCFromPath( dirname( __FILE__ ) . '/connectors/jQueryFileTreePlus.php' );
        return "jQuery( document ).ready( function(){

        
            /**
             * Removes the set values to the input tags.
             * 
             * @since   3.8.4
             */
            removeInputValuesForPath = function( oElem ) {
                jQuery( oElem )
                    .closest( '.admin-page-framework-field' )
                    .find( '.path-field input' )
                    .val( '' );
            }        
            
            /**
             * An enabler function
             * 
             * @since 3.8.4
             */
            bindFileTree = function( oElem ) {
                
                var _iTargetInputID = jQuery( oElem ).attr( 'data-id' );
                var _aOptions = {};

                jQuery.each( jQuery( oElem ).data(), function( index, value ) {

                    if ( 'string' !== typeof value ) {
                        _aOptions[ index ] = value;
                        return true;
                    }
                    if ( 'true' === value.toUpperCase() ) {
                        _aOptions[ index ] = true;
                        return true;
                    }
                    if ( 'false' === value.toUpperCase() ) {
                        _aOptions[ index ] = false;
                        return true;
                    }
                    _aOptions[ index ] = value;
                    
                });                
                
                var _aOptions       = jQuery.extend(
                    {}, 
                    {}, // default
                    _aOptions,  // user input
                    {
                        script: '{$_sConnectorScriptURL}' ,
                        multiFolder: false,
                    }   // overriding values
                );

                jQuery( oElem ).unbind( 'filetreeclicked' );
                jQuery( oElem ).fileTree(
                    _aOptions
                    , 
                    function( sPath ) {
                        jQuery( '#' + _iTargetInputID ).val( sPath );
                    }
                );
                
            }
            
            jQuery( '.select_path_file_trees' ).each( function () {
                bindFileTree( this );
            });

            jQuery().registerAdminPageFrameworkCallbacks( {
                /**
                 * Called when a field of this field type gets repeated.
                 */
                repeated_field: function( oCloned, aModel ) {
                                
                    // Increment element IDs.
                    oCloned.find( '.select_path, .select_path_file_trees_container, .select_path_file_trees' ).incrementAttributes(
                        [ 'id', 'data-id', 'href' ], // attribute name
                        aModel[ 'incremented_from' ], // increment from
                        aModel[ 'id' ] // digit model
                    );
                        
                    // Initialize the event bindings.
                    oCloned.find( '.select_path_file_trees' ).each( function () {
                        bindFileTree( this );
                    });                    
                    
                },
            },
            [ 'path' ]  // subject field type slugs
            );

        });";
    }
    
    /**
     * Returns the field type specific CSS rules.
     */
    protected function getStyles() {
        return "";
    }

    /**
     * Returns the output of the field type.
     */
    public function getField( $aField ) {
        
        $_sPath             = $this->getElement( $aField, array( 'attributes', 'value' ), '' );
        $_aBaseAttributes   = $this->_getBaseAttributes( $aField );
    
        return
            $aField[ 'before_label' ]
            . "<div class='admin-page-framework-input-label-container admin-page-framework-input-container {$aField[ 'type' ]}-field'>" 
                . "<label for='{$aField[ 'input_id' ]}'>"
                    . $aField[ 'before_input' ]
                    . $this->getAOrB(
                        $aField[ 'label' ] && ! $aField[ 'repeatable' ],
                        "<span " . $this->getLabelContainerAttributes( $aField, 'admin-page-framework-input-label-string' ) . ">" 
                            . $aField[ 'label' ] 
                        . "</span>",                        
                        ''                        
                    )
                    . "<input " . $this->getAttributes( $this->_getPathInputAttributes( $aField, $_sPath, $_aBaseAttributes ) ) . " />" 
                    . $aField[ 'after_input' ]
                    . "<div class='repeatable-field-buttons'></div>" 
                . "</label>"
            . "</div>"     
            . $aField[ 'after_label' ]
            . $this->_getRemoveButtonScript( 
                $aField[ 'input_id' ], 
                $this->getElementAsArray( $aField, array( 'attributes', 'remove_button' ) ) 
                + $_aBaseAttributes,
                $aField[ 'type' ] // path 
            )
            . $this->_getSelectButtonScript( 
                $aField[ 'input_id' ], 
                $aField[ 'repeatable' ], 
                $this->getElementAsArray( $aField, array( 'attributes', 'select_button' ) )
                + $_aBaseAttributes
            )
            . $this->_getSelectorElement( $aField[ 'input_id' ], $aField[ 'options' ] )
            ;

    }
        
        /**
         * Returns the HTML output of the selector element inserted in the tick-box.
         * @since       3.8.4
         * @return      string
         */
        private function _getSelectorElement( $sInputID, array $aOptions ) {
            
            $aOptions           = $this->_getFileTreeOptionsFormatted( $aOptions );
            $_aOptionsData      = $this->getDataAttributeArray( $aOptions );   
            $_sAttributes       = $this->getAttributes(
                array(
                    'id'        => "select_path_file_tree_{$sInputID}",
                    'class'     => 'select_path_file_trees',
                    'data-id'   => $sInputID,
                ) + $_aOptionsData
            );
            return "<div class='select_path_file_trees_container' id='path_selector_{$sInputID}' style='display:none;' >"
                . "<div " .  $_sAttributes . "></div>"
            . "</div>";
        
        }
            /**
             * 
             * @return      array
             */
            private function _getFileTreeOptionsFormatted( $aOptions ) {
                
                $aOptions[ 'root' ] = $this->_getRootFormatted( $aOptions[ 'root' ] );
                
                $_aOptions = array();
                foreach( $aOptions as $_sKey => $_mValue ) {
                    
                    // Convert boolean values to a string value.
                    if ( is_bool( $_mValue ) ) {
                        $_mValue = $_mValue ? 'true' : 'false';
                    }
                    
                    $_sKey = isset( $this->_aJSDataAttributes[ $_sKey ] ) 
                        ? $this->_aJSDataAttributes[ $_sKey ]
                        : $_sKey;
                    $_aOptions[ $_sKey ] = $_mValue;
                    
                }
                return $_aOptions;
            }        
                private $_aJSDataAttributes = array(
                    'folderEvent'       => 'folder-event',
                    'expandSpeed'       => 'expand-speed',
                    'collapseSpeed'     => 'collapse-speed',
                    'expandEasing'      => 'expand-easing',
                    'collapseEasing'    => 'collapse-easing',
                    'multiFolder'       => 'multi-folder',
                    'loadMessage'       => 'load-message',
                    'errorMessage'      => 'error-message', 
                    'multiSelect'       => 'multi-select',
                    'onlyFolders'       => 'only-folders',
                    'onlyFiles'         => 'only-files',
                    'preventLinkAction' => 'prevent-link-action',
                    
                    // custom options
                    'fileExtensions'    => 'file-extensions',
                );
                
            /**
             * @since       3.8.4
             * @return      string
             */
            private function _getRootFormatted( $sPath ) {
                
                $_sPath             = trim( $this->_getPathNormalized( $sPath ), '\\/' );
                $_sDocumentRootPath = trim( $this->_getPathNormalized( $_SERVER[ 'DOCUMENT_ROOT' ] ), '\\/' );
                $_sPath             = str_replace(
                    $_sDocumentRootPath, // search
                    '', // replace
                    $_sPath // subject
                );
                return trailingslashit( $_sPath );
                
            }
        
        /**
         * Returns a base attribute array.
         * @since       3.8.4
         * @return      array       The generated base attribute array.
         * @internal
         */
        private function _getBaseAttributes( $aField ) {
            
            $_aBaseAttributes   = $aField[ 'attributes' ] + array( 'class' => null );
            unset( 
                $_aBaseAttributes[ 'input' ], 
                $_aBaseAttributes[ 'select_button' ], 
                $_aBaseAttributes[ 'name' ], 
                $_aBaseAttributes[ 'value' ],
                $_aBaseAttributes[ 'type' ],
                $_aBaseAttributes[ 'remove_button' ] 
            );
            return $_aBaseAttributes;
            
        }   
        
        /**
         * Returns a path field input attribute array for the input tag that stores the user's selecting path.
         * @since       3.8.4
         * @return      array
         * @internal
         */
        private function _getPathInputAttributes( array $aField, $sPath, array $aBaseAttributes ) {
            
            return array(
                'name'              => $aField[ 'attributes' ][ 'name' ],
                'value'             => $sPath,
                'type'              => 'text',
                
                'data-type-path'    => 'path',
                // 'data-...' => 'set here JavaScript script options'
            ) 
            + $this->getElementAsArray( $aField, array( 'attributes', 'input' ) )
            + $aBaseAttributes;
            
        }    
    
        /**
         * Returns an inline script tag that removes the set value.
         * 
         * @since       3.8.4
         * @return      string
         * @internal
         */
        protected function _getRemoveButtonScript( $sInputID, array $aButtonAttributes, $sType='path' ) {
                           
            $_sButtonHTML  = '"' . $this->_getRemoveButtonHTMLByType( $sInputID, $aButtonAttributes, $sType ) . '"';
            $_sScript      = <<<JAVASCRIPTS
                if ( 0 === jQuery( 'a#remove_{$sType}_{$sInputID}' ).length ) {
                    jQuery( 'input#{$sInputID}' ).after( $_sButtonHTML );
                }
JAVASCRIPTS;
                    
            return "<script type='text/javascript' class='admin-page-framework-{$sType}-remove-button'>"
                    . '/* <![CDATA[ */'
                    . $_sScript 
                    . '/* ]]> */'
                . "</script>". PHP_EOL;
           
        }    
   
        
        /**
         * Returns a `<script>` tag element with a JavaScript script that enables select buttons.
         * 
         * @since       3.8.4
         * @return      string
         * @internal
         */     
        protected function _getSelectButtonScript( $sInputID, $abRepeatable, array $aButtonAttributes ) {
      
            $_sButtonHTML       = '"' . $this->_getSelectButtonHTML( $sInputID, $aButtonAttributes ) . '"';
            $_sRpeatable        = $this->getAOrB( ! empty( $abRepeatable ), 'true', 'false' );
            $_sScript                = <<<JAVASCRIPTS
if ( jQuery( 'a#select_path_{$sInputID}' ).length == 0 ) {
    jQuery( 'input#{$sInputID}' ).after( $_sButtonHTML );
}
jQuery( document ).ready( function(){   
    //setAdminPageFrameworkMediaUploader( '{$sInputID}', 'true' === '{$_sRpeatable}' );
});
JAVASCRIPTS;
                    
            return "<script type='text/javascript' class='admin-page-framework-media-uploader-button'>" 
                    . '/* <![CDATA[ */'
                    . $_sScript 
                    . '/* ]]> */'
                . "</script>". PHP_EOL;

        }        
            /**
             * Returns an HTML output of a select button.
             * @since       3.8.4
             * @return      string      The generated HTML uploader button output.
             * @internal
             */
            private function _getSelectButtonHTML( $sInputID, array $aButtonAttributes ) {
                                      
                $_bIsLabelSet = isset( $aButtonAttributes[ 'data-label' ] ) && $aButtonAttributes[ 'data-label' ];
                $_aAttributes = $this->_getFormattedSelectButtonAttributes( 
                    $sInputID, 
                    $aButtonAttributes, 
                    $_bIsLabelSet
                );
                return "<a " . $this->getAttributes( $_aAttributes ) . ">"
                        . $this->getAOrB( 
                            $_bIsLabelSet,
                            $_aAttributes[ 'data-label' ],
                            $this->getAOrB(
                                strrpos( $_aAttributes[ 'class' ], 'dashicons' ),
                                '',
                                __( 'Select Path', 'admin-page-framework-field-type-pack' )
                            )
                        )
                    ."</a>";
                    
            }      
                /**
                 * Returns a formatted upload button attributes array.
                 * @since       3.8.4
                 * @return      array       The formatted upload button attributes array.
                 * @internal
                 */
                private function _getFormattedSelectButtonAttributes( $sInputID, array $aButtonAttributes, $_bIsLabelSet ) {
                                     
                    $_aAttributes           = array(
                            'id'        => "select_path_{$sInputID}",
                            'href'      => "#TB_inline?width=600&height=550&inlineId=path_selector_{$sInputID}",
                        ) 
                        + $aButtonAttributes
                        + array(
                            'title'     => $_bIsLabelSet 
                                ? $aButtonAttributes[ 'data-label' ]
                                : __( 'Select Path', 'admin-page-framework-field-type-pack' ),
                            'data-label' => null,
                        );
                    $_aAttributes['class']  = $this->getClassAttribute( 
                        'thickbox select_path button button-small ',
                        $this->getAOrB(
                            trim( $aButtonAttributes['class'] ),
                            $aButtonAttributes['class'],
                            $this->getAOrB( 
                                ! $_bIsLabelSet && version_compare( $GLOBALS[ 'wp_version' ], '3.8', '>=' ),
                                'dashicons dashicons-portfolio',
                                ''
                            )
                        )
                    );       
                    return $_aAttributes;
                    
                }           
    
}
endif;
