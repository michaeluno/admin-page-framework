<?php
/**
 * Parses WordPress readme files.
 *    
 * @package      Admin Page Framework
 * @copyright    Copyright (c) 2015, <Michael Uno>
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 */

/**
 * Parses WordPress readme files.
 * 
 * <h3>Examples</h3>
 * <code>
 * $_sText   = '...';
 * $_oParser = new AdminPageFramework_WPReadmeParser;
 * $_oParser->setText( $_sText );
 * $_sText   = $_oParser->get();
 * </code>
 * 
 * <code>
 * $_sFilePath  = dirname( __FILE__ ) . '/aa/bbb/readme.txt';
 * $_oParser    = new AdminPageFramework_WPReadmeParser( $_sFilePath );
 * $_sFAQ       = $_oParser->get( 'Frequently asked questions' ); // passing the section name
 * </code>
 * 
 * @since       3.5.0
 * @uses        AdminPageFramework_Parsedown
 * @package     AdminPageFramework
 * @subpackage  Utility
 */
class AdminPageFramework_WPReadmeParser {
    
    /**
     * Represents the structure of the array storing callbacks.
     * @since       3.5.0
     * @internal
     */
    static private $_aStructure_Callbacks = array(
        'code_block'        => null,
        '%PLUGIN_DIR_URL%'  => null,
        '%WP_ADMIN_URL%'    => null,
    ); 
    
    /**
     * Represents the structure of the array storing options.
     * @since       3.6.1
     */
    static private $_aStructure_Options  = array(
        'convert_shortcode'   => true,    // 3.6.1+
    );
    
    /**
     * Stores the parsing text.
     */
    public $sText = '';
    /**
     * Stores the divided sections.
     */
    protected $_aSections = array();
    /**
     * Replacement definitions..
     */
    public $aReplacements = array();
    /**
     * Callback definitions.
     */
    public $aCallbacks = array();
    /**
     * Options.
     */
    public $aOptions = array();
    
    /**
     * Sets up properties.
     * 
     * If you don't have a file path but a text string, then omit the first parameter and use the `setText()` method.
     * 
     * @param       string      $sFilePathOrContent     The WordPress readme text file path or the text string.
     * @param       array       $aReplacements          An array holding replacements.
     *  array(
     *      '%PLUGIN_DIR_URL%'  =>  plugin_directory_path,
     *      '%WP_ADMIN_URL%'  =>  admin_url()
     *  )
     * @param       array       $aCallbacks       Callbacks. The supported items are the followings:
     * `array(
     *      'code_block'        =>  ...,
     *      
     * )`
     * @param       array       $aOptions           The options array which determines the behaviour of the class.
     * @since       3.5.0
     * @since       3.6.0       Made it accept string content to be passed to the first parameter.
     * @since       3.6.1       Added the `$aOptions` parameter.
     */
    public function __construct( $sFilePathOrContent='', array $aReplacements=array(), array $aCallbacks=array(), array $aOptions=array() ) {

        $this->sText            = file_exists( $sFilePathOrContent )
            ? file_get_contents( $sFilePathOrContent )
            : $sFilePathOrContent;
        $this->aReplacements    = $aReplacements;
        $this->aCallbacks       = $aCallbacks + self::$_aStructure_Callbacks;        
        $this->aOptions         = $aOptions + self::$_aStructure_Options;            
        $this->_aSections       = $this->sText
            ? $this->_getSplitContentsBySection( $this->sText )
            : array();    
        
    } 
    
    /**
     * Sets the text to parse.
     * @return      void
     * @since       3.5.0
     */
    public function setText( $sText ) {
        $this->sText        = $sText;
        $this->_aSections   = $this->sText
            ? $this->_getSplitContentsBySection( $this->sText )
            : array();
    }
    
        /**
         * Get the split contents by section.
         * 
         * @since       3.5.0
         * @return      array
         */
        private function _getSplitContentsBySection( $sText ) {
            $_aSections = preg_split( 
                '/^[\s]*==[\s]*(.+?)[\s]*==/m', 
                $sText,
                -1, 
                PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY 
            );         
            return $_aSections;
        }
            
    /**
     * Returns the parsed text.
     * 
     * @since       3.5.0
     * @return      string
     */
    public function get( $sSectionName='' ) {
        return $sSectionName
            ? $this->getSection( $sSectionName )
            : $this->_getParsedText( $this->sText );
    }
    
    /**
     * Retrieves the section.
     * 
     * @return      string      The section content.
     * @since       3.5.0
     * @internal
     */
    public function getSection( $sSectionName ) {
        
        $_sContent = $this->getRawSection( $sSectionName );
        return $this->_getParsedText( $_sContent );
        
    }
        
        /**
         * Returns the parsed text.
         * @since       3.5.0
         * @internal
         */
        private function _getParsedText( $sContent ) {

            // WordPress Shortcodes
            $_sContent = $this->_getShortcodeConverted( $sContent );
            
            // inline backticks.
            $_sContent = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $_sContent );   
            
            // multi-line backticks - store code blocks in a separate place
            $_sContent = preg_replace_callback( '/`(.*?)`/ms', array( $this, '_replyToReplaceCodeBlocks' ), $_sContent );
            
            // WordPress specific sub sections
            $_sContent = preg_replace( '/= (.*?) =/', '<h4>\\1</h4>', $_sContent );
            
            // Replace user set strings.
            $_sContent = str_replace( array_keys( $this->aReplacements ), array_values( $this->aReplacements ), $_sContent );
                    
            // Markdown
            $_oParsedown = new AdminPageFramework_Parsedown();        
            return $_oParsedown->text( $_sContent );
            
        }
            /**
             * @return      string 
             * @return      3.6.1
             */
            private function _getShortcodeConverted( $sContent ) {
                if ( ! $this->aOptions[ 'convert_shortcode' ] ) {
                    return $sContent;
                }
                // Register the 'embed' shortcode.
                add_shortcode( 'embed', array( $this, '_replyToProcessShortcode_embed' ) );
                return do_shortcode( $sContent );
                
            }
                /**
                 * @since       3.6.1
                 * @return      string      The generate HTML output.
                 */
                public function _replyToProcessShortcode_embed( $aAttributes, $sURL, $sShortcode='' ) {

                    $sURL   = isset( $aAttributes[ 'src' ] ) ? $aAttributes[ 'src' ] : $sURL;      
                    $_sHTML = wp_oembed_get( $sURL );
                    
                    // If there was a result, return it
                    if ( $_sHTML ) {
                        // This filter is documented in wp-includes/class-wp-embed.php
                        return "<div class='video oembed'>" 
                                    . apply_filters(
                                        'embed_oembed_html', 
                                        $_sHTML, 
                                        $sURL, 
                                        $aAttributes, 
                                        0
                                    )
                            . "</div>";
                    }        
                    
                    // If not found, return the link.
                    $_oWPEmbed = new WP_Embed;        
                    return "<div class='video oembed'>" 
                            . $_oWPEmbed->maybe_make_link( $sURL )
                        . "</div>";
                    
                }            
    
        /**
         * Returns the modified code block.
         * 
         * @since       3.5.0
         * @internal
         */
        public function _replyToReplaceCodeBlocks( $aMatches ) {
            
            if ( ! isset( $aMatches[ 1 ] ) ) {
                return $aMatches[ 0 ];
            }
            
            $_sIntact   = trim( $aMatches[ 1 ] );
            $_sModified = "<pre><code>" 
                . $this->getSyntaxHighlightedPHPCode( $_sIntact )
            . "</code></pre>";                       
            
            return is_callable( $this->aCallbacks['code_block'] )
                ? call_user_func_array( $this->aCallbacks['code_block'], array( $_sModified, $_sIntact ) )
                : $_sModified;       
            
        }
    
    /**
     * Returns the section raw output by section name.
     * 
     * @since       3.5.0
     * @internal
     */
    public function getRawSection( $sSectionName ) {

        $_iIndex   = array_search( $sSectionName, $this->_aSections );  
        return false === $_iIndex
            ? ''
            : trim( $this->_aSections[ $_iIndex + 1 ] );
    
    }
    
    
    /**
     * Retrieves syntax highlighted PHP code.
     * 
     * @since       3.5.0
     * @param       string      $sCode
     * @internal
     */
    public function getSyntaxHighlightedPHPCode( $sCode ) {
        
        // If <?php notation is missing, highlight_string() will not highlight the syntax so add it.
        $_bHasPHPTag = "<?php" === substr( $sCode, 0, 5 );
        
        $sCode = $_bHasPHPTag ? $sCode : "<?php " . $sCode;
        
        $sCode = str_replace( '"', "'", $sCode ); // highlight_string() crashes if double quotes are contained in the code.
        $sCode = highlight_string( $sCode, true );
        
        $sCode = $_bHasPHPTag ? $sCode : preg_replace( '/(&lt;|<)\Q?php\E(&nbsp;)?/', '', $sCode, 1 );
        
        return $sCode;
    
    }    
    
}