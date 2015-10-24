<?php
class AdminPageFramework_WPReadmeParser {
    static private $_aStructure_Callbacks = array('code_block' => null, '%PLUGIN_DIR_URL%' => null, '%WP_ADMIN_URL%' => null,);
    static private $_aStructure_Options = array('convert_shortcode' => true,);
    public $sText = '';
    protected $_aSections = array();
    public $aReplacements = array();
    public $aCallbacks = array();
    public $aOptions = array();
    public function __construct($sFilePathOrContent = '', array $aReplacements = array(), array $aCallbacks = array(), array $aOptions = array()) {
        $this->sText = file_exists($sFilePathOrContent) ? file_get_contents($sFilePathOrContent) : $sFilePathOrContent;
        $this->aReplacements = $aReplacements;
        $this->aCallbacks = $aCallbacks + self::$_aStructure_Callbacks;
        $this->aOptions = $aOptions + self::$_aStructure_Options;
        $this->_aSections = $this->sText ? $this->_getSplitContentsBySection($this->sText) : array();
    }
    public function setText($sText) {
        $this->sText = $sText;
        $this->_aSections = $this->sText ? $this->_getSplitContentsBySection($this->sText) : array();
    }
    private function _getSplitContentsBySection($sText) {
        $_aSections = preg_split('/^[\s]*==[\s]*(.+?)[\s]*==/m', $sText, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        return $_aSections;
    }
    public function get($sSectionName = '') {
        return $sSectionName ? $this->getSection($sSectionName) : $this->_getParsedText($this->sText);
    }
    public function getSection($sSectionName) {
        $_sContent = $this->getRawSection($sSectionName);
        return $this->_getParsedText($_sContent);
    }
    private function _getParsedText($sContent) {
        $_sContent = $this->_getShortcodeConverted($sContent);
        $_sContent = preg_replace('/`(.*?)`/', '<code>\\1</code>', $_sContent);
        $_sContent = preg_replace_callback('/`(.*?)`/ms', array($this, '_replyToReplaceCodeBlocks'), $_sContent);
        $_sContent = preg_replace('/= (.*?) =/', '<h4>\\1</h4>', $_sContent);
        $_sContent = str_replace(array_keys($this->aReplacements), array_values($this->aReplacements), $_sContent);
        $_oParsedown = new AdminPageFramework_Parsedown();
        return $_oParsedown->text($_sContent);
    }
    private function _getShortcodeConverted($sContent) {
        if (!$this->aOptions['convert_shortcode']) {
            return $sContent;
        }
        add_shortcode('embed', array($this, '_replyToProcessShortcode_embed'));
        return do_shortcode($sContent);
    }
    public function _replyToProcessShortcode_embed($aAttributes, $sURL, $sShortcode = '') {
        $sURL = isset($aAttributes['src']) ? $aAttributes['src'] : $sURL;
        $_sHTML = wp_oembed_get($sURL);
        if ($_sHTML) {
            return "<div class='video oembed'>" . apply_filters('embed_oembed_html', $_sHTML, $sURL, $aAttributes, 0) . "</div>";
        }
        $_oWPEmbed = new WP_Embed;
        return "<div class='video oembed'>" . $_oWPEmbed->maybe_make_link($sURL) . "</div>";
    }
    public function _replyToReplaceCodeBlocks($aMatches) {
        if (!isset($aMatches[1])) {
            return $aMatches[0];
        }
        $_sIntact = trim($aMatches[1]);
        $_sModified = "<pre><code>" . $this->getSyntaxHighlightedPHPCode($_sIntact) . "</code></pre>";
        return is_callable($this->aCallbacks['code_block']) ? call_user_func_array($this->aCallbacks['code_block'], array($_sModified, $_sIntact)) : $_sModified;
    }
    public function getRawSection($sSectionName) {
        $_iIndex = array_search($sSectionName, $this->_aSections);
        return false === $_iIndex ? '' : trim($this->_aSections[$_iIndex + 1]);
    }
    public function getSyntaxHighlightedPHPCode($sCode) {
        $_bHasPHPTag = "<?php" === substr($sCode, 0, 5);
        $sCode = $_bHasPHPTag ? $sCode : "<?php " . $sCode;
        $sCode = str_replace('"', "'", $sCode);
        $sCode = highlight_string($sCode, true);
        $sCode = $_bHasPHPTag ? $sCode : preg_replace('/(&lt;|<)\Q?php\E(&nbsp;)?/', '', $sCode, 1);
        return $sCode;
    }
}