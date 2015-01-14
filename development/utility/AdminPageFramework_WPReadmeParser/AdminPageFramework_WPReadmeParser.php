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
 * @since       3.5.0
 * @uses        AdminPageFramework_Parsedown
 * @package     AdminPageFramework
 * @subpackage  Utility
 */
class AdminPageFramework_WPReadmeParser {
    
    /**
     * Represents the structure of the array storing callbacks.
     * @since       3.5.0
     */
    static private $_aStructure_Callbacks = array(
        'code_block'        => null,
        '%PLUGIN_DIR_URL%'  => null,
        '%WP_ADMIN_URL%'    => null,
    ); 
  
    /**
     * Sets up properties.
     * 
     * If you don't have a file path but a text string, then omit the first parameter and use the `setText()` method.
     * 
     * @param       string      $sFilePath        The WordPress readme text file path.
     * @param       array       $aReplacements    An array holding replacements.
     *  array(
     *      '%PLUGIN_DIR_URL%'  =>  plugin_directory_path,
     *      '%WP_ADMIN_URL%'  =>  admin_url()
     *  )
     * @param       array       $aCallbacks       Callbacks. The supported items are the followings:
     * `array(
     *      'code_block'        =>  ...,
     *      
     * )`
     * @since       3.5.0
     */
    public function __construct( $sFilePath='', array $aReplacements=array(), array $aCallbacks=array() ) {
        $this->sText            = file_exists( $sFilePath )
            ? file_get_contents( $sFilePath )
            : '';
        $this->_aContents       = $this->sText
            ? $this->_getSplitContentsBySection( $this->sText )
            : array();    
        $this->aReplacements    = $aReplacements;
        $this->aCallbacks       = $aCallbacks + self::$_aStructure_Callbacks;
    } 
    
    /**
     * Sets the text to parse.
     * @return      void
     * @since       3.5.0
     */
    public function setText( $sText ) {
        $this->sText        = $sText;
        $this->_aContents   = $this->sText
            ? $this->_getSplitContentsBySection( $this->sText )
            : array();
    }
    
        /**
         * Get the split contents by section.
         * @since       3.5.0
         */
        private function _getSplitContentsBySection( $sText ) {
            return preg_split( 
                '/^[\s]*==[\s]*(.+?)[\s]*==/m', 
                $sText,
                -1, 
                PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY 
            );            
        }
    
    /**
     * Retrieves the section.
     * 
     * @return      string      The section content.
     * @since       3.5.0
     */
    public function getSection( $sSectionName ) {
        
        $_sContent = $this->getRawSection( $sSectionName );

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
         * Returns the modified code block.
         * 
         * @since       3.5.0
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
     * Returns the section output by section name.
     * 
     * @since       3.5.0
     */
    public function getRawSection( $sSectionName ) {

        $_iIndex   = array_search( $sSectionName, $this->_aContents );  
        return false === $_iIndex
            ? ''
            : trim( $this->_aContents[ $_iIndex + 1 ] );
    
    }
    
    
    /**
     * Retrieves syntax highlighted PHP code.
     * 
     * @since       3.5.0
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