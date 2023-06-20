<?php
/**
 * Generates an HTML Table of Contents block.
 *
 * @package      Admin Page Framework
 * @copyright    Copyright (c) 2013-2022, Michael Uno
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 */

/**
 * Generates an HTML Table of Contents block.
 *
 * This parses given text and generate headings with links to the heading elements. It helps the viewer browse the vertically long contents.
 * Use this class to create a help page from existent text document.
 *
 * <h2>Usage</h2>
 * Pass text data to the first parameter and the depth of nested headings to the second parameter.
 * Then use the `get()` method to retrieve the generated output.
 *
 * <h2>Example</h2>
 * <code>
 * $_oTOC = new AdminPageFramework_TableOfContents( $sText, 4 );
 * $_sTOC = $_oTOC->get();
 * </code>
 *
 * @image       http://admin-page-framework.michaeluno.jp/image/utility/table_of_contents.png
 * @since       3.5.0
 * @package     AdminPageFramework/Utility
 */
class AdminPageFramework_TableOfContents {

    /**
     * Sets up properties.
     *
     * @param       string      $sHTML      The HTML text to parse.
     * @param       integer     $iDepth     The header number to parse.
     * @param       string      $sTitle     The heading title which appears at the beginning of the output.
     */
    public function __construct( $sHTML, $iDepth=4, $sTitle='' ) {

        $this->sTitle   = $sTitle;
        $this->sHTML    = $sHTML;
        $this->iDepth   = $iDepth;

    }

    /**
     * Returns the TOC block and the contents.
     *
     * @since       3.5.0
     * @see         http://www.10stripe.com/articles/automatically-generate-table-of-contents-php.php
     * @return      string
     */
    public function get() {
        return $this->getTOC()
            . $this->getContents();
    }

    /**
     * Returns only the contents.
     *
     * The contents will be modified as named elements need to be inserted.
     * @return      string
     * @internal
     */
    public function getContents() {
        return $this->sHTML;
    }

    /**
     * Returns only the TOC block.
     * @return      string
     * @internal
     */
    public function getTOC() {

        $iDepth     = $this->iDepth;

        // get the headings down to the specified depth
        $this->sHTML = preg_replace_callback(
            '/<h[2-' . $iDepth . ']*[^>]*>.*?<\/h[2-' . $iDepth . ']>/i',
            array( $this, '_replyToInsertNamedElement' ),
            $this->sHTML
        );

        $_aOutput = array();
        foreach( $this->_aMatches as $_iIndex => $_sMatch ) {
            $_sMatch = is_string( $_sMatch ) ? $_sMatch : '';
            $_sMatch = strip_tags( $_sMatch, '<h1><h2><h3><h4><h5><h6><h7><h8>' );
            $_sMatch = preg_replace( '/<h([1-' . $iDepth . '])>/', '<li class="toc$1"><a href="#toc_' . $_iIndex . '">', $_sMatch );
            $_sMatch = preg_replace( '/<\/h[1-' . $iDepth . ']>/', '</a></li>', $_sMatch );
            $_aOutput[] = $_sMatch;
        }

        // plug the results into appropriate HTML tags
        $this->sTitle = $this->sTitle
            ? '<p class="toc-title">' . $this->sTitle . '</p>'
            : '';
        return '<div class="toc">'
                . $this->sTitle
                . '<ul>'
                    . implode( PHP_EOL, $_aOutput )
                . '</ul>'
            . '</div>';

    }
        /**#@+
         * @internal
         */
        protected $_aMatches = array();
        public function _replyToInsertNamedElement( $aMatches ) {
            static $_icount = -1;
            $_icount++;
            $this->_aMatches[] = $aMatches[ 0 ];
            return "<span class='toc_header_link' id='toc_{$_icount}'></span>" . PHP_EOL
                . $aMatches[ 0 ];
        }
        /**#@-*/

}
