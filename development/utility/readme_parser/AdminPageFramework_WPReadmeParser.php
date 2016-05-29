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
 * Parses WordPress readme files and generates HTML outputs.
 * 
 * This helps when creating a help page with some readme file contents.
 * 
 * <h3>Usage</h3>
 * Set a file path or a text content of WordPress formatted readme and instantiate the class.
 * Then perform the `get()` method by passing a section name. If a section name is omitted, the entire contents will be returned.
 * 
 * <h3>Example</h3>
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
    
    /**#@+
     * @internal
     */        
    /**
     * Represents the structure of the array storing callbacks.
     * @since       3.5.0
     * @internal
     */
    static private $_aStructure_Callbacks = array(
        'code_block'              => null,
        'content_before_parsing'  => null, // 3.6.1
    ); 
    
    static private $_aStructure_Replacements = array(
        // '%PLUGIN_DIR_URL%'  => null,
        // '%WP_ADMIN_URL%'    => null,    
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
    /**#@-*/ 
        
    /**
     * Sets up properties.
     * 
     * If you don't have a file path but a text string, then omit the first parameter and use the `setText()` method.
     * 
     * @param       string      $sFilePathOrContent     The WordPress readme text file path or the text string.
     * @param       array       $aReplacements          An array holding replacements.
     * <code>
     *  array(
     *      '%PLUGIN_DIR_URL%'  =>  plugin_directory_path,
     *      '%WP_ADMIN_URL%'    =>  admin_url()
     *  )
     * </code>
     * @param       array       $aCallbacks       Callbacks. The supported items are the followings:
     * <code>
     * array(
     *      'code_block'        =>  ...,
     *      
     * )</code>
     * @param       array       $aOptions           The options array which determines the behaviour of the class.
     * @since       3.5.0
     * @since       3.6.0       Made it accept string content to be passed to the first parameter.
     */
    public function __construct( $sFilePathOrContent='', array $aReplacements=array(), array $aCallbacks=array() ) {

        $this->sText            = file_exists( $sFilePathOrContent )
            ? file_get_contents( $sFilePathOrContent )
            : $sFilePathOrContent;
        $this->aReplacements    = $aReplacements + self::$_aStructure_Replacements;
        $this->aCallbacks       = $aCallbacks + self::$_aStructure_Callbacks;        
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

            // User set callbacks.
            $_sContent = is_callable( $this->aCallbacks[ 'content_before_parsing' ] )
                ? call_user_func_array( $this->aCallbacks[ 'content_before_parsing' ], array( $sContent ) )
                : $sContent; 
            
            // inline backticks.
            $_sContent = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $_sContent );   
            
            // multi-line backticks - store code blocks in a separate place
            $_sContent = preg_replace_callback( '/`(.*?)`/ms', array( $this, '_replyToReplaceCodeBlocks' ), $_sContent );
            
            // WordPress specific sub sections
            $_sContent = preg_replace( '/= (.*?) =/', '<h4>\\1</h4>', $_sContent );
            
            // Replace user set strings.
            $_sContent = str_replace( 
                array_keys( $this->aReplacements ), // searches
                array_values( $this->aReplacements ), // replacements
                $_sContent // subject
            );
                    
            // Markdown
            $_oParsedown = new AdminPageFramework_Parsedown();        
            return $_oParsedown->text( $_sContent );
            
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
            
            return is_callable( $this->aCallbacks[ 'code_block' ] )
                ? call_user_func_array( $this->aCallbacks[ 'code_block' ], array( $_sModified, $_sIntact ) )
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
