<?php
if ( ! class_exists( 'GitHubCustomFieldType' ) ) :
class GitHubCustomFieldType extends AdminPageFramework_FieldType {
        
    /**
     * Defines the field type slugs used for this field type.
     * 
     * The slug is used for the type key in a field definition array.
     * <code>$this->addSettingFields(
     *      array(
     *          'section_id'    => '...',
     *          'type'          => 'github',        // <--- THIS PART
     *          'field_id'      => '...',
     *          'title'         => '...',
     *      )
     *  );</code>
     */
    public $aFieldTypeSlugs = array( 'github', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * The keys are used for the field definition array.
     * <code>$this->addSettingFields(
     *      array(
     *          'section_id'    => '...',    
     *          'type'          => '...',
     *          'field_id'      => '...',
     *          'my_custom_key' => '...',    // <-- THIS PART
     *      )
     *  );</code>
     * @remark            $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'user_name'     => 'michaeluno',
        'button_type'   => 'follow',        // either of the followings: follow, star, watch, fork, issue
        'count'         => true,            // whether or not the count should be displayed
        'repository'    => 'admin-page-framework',        
        'size'          => null,
        'attributes'    =>    array(
            'href'              => null,
            'data-style'        => null,
            'data-icon'         => null,
            'data-text'         => null,
            'data-count-href'   => null,
            'data-count-api'    => null,        
        ),    
    );

    /**
     * User constructor.
     * 
     * Loaded at the end of the constructor.
     */
    protected function construct() {}
        
    
    /**
     * Loads the field type necessary components.
     * 
     * This method is triggered when a field definition array that calls this field type is parsed. 
     */ 
    protected function setUp() {
        add_action( 'admin_footer', array( $this, '_replyToAddScript' ) );
    }    
        static public $_bAddedScriptToFooter;
        public function _replyToAddScript() {
            
            if ( isset( self::$_bAddedScriptToFooter ) && self::$_bAddedScriptToFooter ) {
                return;
            }
            self::$_bAddedScriptToFooter = true;
            echo "<script async defer id='github-bjs' src='" . $this->resolveSRC( dirname( __FILE__ ) . '/asset/github-buttons/buttons.js' ) . "'>"
                . "</script>";
            
        }
    

    /**
     * Returns an array holding the urls of enqueuing scripts.
     * 
     * The returning array should be composed with all numeric keys. Each element can be either a string( the url or the path of the source file) or an array of custom argument.
     * 
     * <h4>Custom Argument Array</h4>
     * <ul>
     *     <li><strong>src</strong> - ( required, string ) The url or path of the target source file</li>
     *     <li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
     *     <li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
     *     <li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
     *     <li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
     *     <li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
     *     <li><strong>arguments</strong> - ( optional, array ) [3.3.0+] argument array. <code>array( 'async' => '', 'data-id' => '...' )</code></li>
     * </ul>     
     * <h4>Example</h4>
     * <code>array( 
     *     'src'           => dirname( __FILE__ ) . '/asset/github-buttons/buttons.js',
     *     'handle_id'     => 'github-bjs',
     *     'in_footer'     => true,
     *     'attributes'    => array(
     *         'async'     => '',
     *         'defer'     => '',
     *     ),
     * )</code>
     */
    protected function getEnqueuingScripts() { 
        return array(
            // array( 
                // 'src'           => dirname( __FILE__ ) . '/asset/github-buttons/buttons.js',
                // 'handle_id'     => 'github-bjs',
                // 'in_footer'     => true,
                // 'attributes'    => array(
                    // 'async'     => '',
                    // 'defer'     => '',
                    // 'id'        => 'github-bjs',
                // ),
            // ),
        );
    }
    
    /**
     * Returns an array holding the urls of enqueuing styles.
     * 
     * <h4>Custom Argument Array</h4>
     * <ul>
     *     <li><strong>src</strong> - ( required, string ) The url or path of the target source file</li>
     *     <li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
     *     <li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
     *     <li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
     *     <li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
     *     <li><strong>arguments</strong> - ( optional, array ) [3.3.0+] argument array. <code>array( 'data-id' => '...' )</code></li>
     * </ul>
     * <h4>Example</h4>
     * <code>array(    
     *      array( 
     *          'src'       => dirname( __FILE__ ) . '/assets/css/main.css',
     *          'handle_id' => 'custom_button_css',
     *      ),
     *  );</code>
     */
    protected function getEnqueuingStyles() { 
        return array(    
            // array( 
                // 'src'       => dirname( __FILE__ ) . '/asset/github-buttons/assets/css/main.css',
                // 'handle_id' => 'github_button_css',
            // ),
        );
    }            


    /**
     * Returns the field type specific JavaScript script.
     */ 
    protected function getScripts() { 

        $aJSArray = json_encode( $this->aFieldTypeSlugs );
        /*    
         * The below function will be triggered when a new repeatable field is added. 
         * 
         * Use the registerAPFCallback method to register a callback.
         * Available callbacks are:
         *     added_repeatable_field      - triggered when a repeatable field gets repeated. Parameters 1. (object) the jQuery element object. 2. (string) the field type slug. 3. (string) the field tag id.
         *     removed_repeatable_field    - triggered when a repeatable field gets removed. Parameters 1. (object) the jQuery element object. 2. (string) the field type slug. 3. (string) the field tag id.
         *     sorted_fields               - triggered when a sortable field gets sorted. Parameters 1. (object) the jQuery element object. 2. (string) the field type slug. 3. (string) the field tag id.
         *     stopped_sorting_fields      - triggered when sorting fields finishes. 
         * */
        return "" . PHP_EOL;
        
    }

    /**
     * Returns IE specific CSS rules.
     */
    protected function getIEStyles() { return ''; }

    /**
     * Returns the field type specific CSS rules.
     */ 
    protected function getStyles() {
        return "
            .github-button-container {
                vertical-align: middle;
                display: inline-block;
                margin-top: 0.2em;
            }
        ";
    }

    
    /**
     * Returns the output of the geometry custom field type.
     * 
     */
    /**
     * Returns the output of the field type.
     */
    protected function getField( $aField ) { 

        $_aAttributes           = $aField['attributes'];
        $_aAttributes['class'] .= ' github github-button';
        $_aAttributes = $this->uniteArrays( 
            $_aAttributes,
            array(
                'href'              => $this->_getHrefByType( $aField['button_type'], $aField['user_name'], $aField['repository'] ),
                'class'             => $_aAttributes['class'],
                'data-count-href'   => $aField['count'] ? $this->_getCountHrefByType( $aField['button_type'], $aField['user_name'], $aField['repository'] ) : null,
                'data-count-api'    => $aField['count'] ? $this->_getCountAPIByType( $aField['button_type'], $aField['user_name'], $aField['repository'] ) : null,
                'data-style'        => strtolower( $aField['size'] ),
                'data-icon'         => $this->_getIcontByType( $aField['button_type'] ),
                'data-text'         => $this->_getButtonLabelByType( $aField['button_type'], $aField['user_name'], $aField['value'] ),
                
            )
        );

        return 
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container'>"        
                . $aField['before_input']
                . ( $aField['label'] && ! $aField['repeatable']
                    ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . $aField['label'] . "</span>"
                    : "" 
                )
                . "<div class='github-button-container'>"
                    . "<a " . $this->generateAttributes( $_aAttributes ) . ">"
                        . $_aAttributes['data-text']
                    . "</a>"
                . "</div>"
                . $aField['after_input']
                
            . "</div>"
            . $aField['after_label'];
        
    }             
        private function _getHrefByType( $sButtonType, $sUserName, $sRepository ) {
            $sButtonType = strtolower( $sButtonType );
            switch( $sButtonType ) {
                case 'follow': 
                    return esc_url( 'https://github.com/' . $sUserName );
                case 'issue':
                    return esc_url( 'https://github.com/' . $sUserName . '/' . $sRepository . '/issues' );
                default:
                    return esc_url( 'https://github.com/' . $sUserName . '/' . $sRepository );                    
            }
        }
        private function _getCountHrefByType( $sButtonType, $sUserName, $sRepository )  {
            
            // e.g. data-count-href="/ntkme/followers" 
            $sButtonType = strtolower( $sButtonType );
            switch( $sButtonType ) {
                case 'follow': 
                    return esc_url( '/' . $sUserName . '/' . $this->_getGitHubAPISlugByType( $sButtonType ) );
                case 'issue':
                    return '';
                default:
                    return esc_url( '/' . $sUserName . '/' . $sRepository . '/' . $this->_getGitHubAPISlugByType( $sButtonType ) );
            }                        
        }
        private function _getCountAPIByType( $sButtonType, $sUserName, $sRepository )  {
            
            $sButtonType = strtolower( $sButtonType );
            switch( $sButtonType ) {
                case 'follow': 
                    // e.g. data-count-api="/users/ntkme#followers">Follow @ntkme</a>            
                    return esc_url( '/users/' . $sUserName . '#' . $this->_getGitHubAPICountSlugByType( $sButtonType ) );
                default:
                    // e.g. data-count-api="/repos/ntkme/github-buttons#open_issues_count">Issue</a>
                    return esc_url( '/repos/' . $sUserName . '/' . $sRepository . '#' . $this->_getGitHubAPICountSlugByType( $sButtonType ) );
            }                           
        }        
        private function _getIcontByType( $sButtonType ) {
            $sButtonType = strtolower( $sButtonType );
            switch( $sButtonType ) {
                case 'follow': 
                    return '';
                case 'watch':
                    return 'octicon-eye';
                case 'star':
                    return 'octicon-star';
                case 'fork':
                    return 'octicon-git-branch';
                case 'issue':
                    return 'octicon-issue-opened';
                default:
                    return '';
            }                
        }
        private function _getButtonLabelByType( $sButtonType, $sUserName, $sValue ) {
            
            if ( null !== $sValue ) {
                return $sValue;
            }
            
            // At this point, the use does not specify the button text with the 'value' argument.
            $sButtonType = strtolower( $sButtonType );
            switch( $sButtonType ) {
                case 'follow': 
                    return sprintf( __( 'Follow @%1$s', 'admin-page-framework' ), $sUserName );
                case 'watch':
                    return __( 'Watch', 'admin-page-framework' );
                case 'star':
                    return __( 'Star', 'admin-page-framework' );
                case 'fork':
                    return __( 'Fork', 'admin-page-framework' );
                case 'issue':
                    return __( 'Issue', 'admin-page-framework' );
                default:
                    return '';
            }                 

        }        
        /**
         * Returns the API slug used for the given type.
         * 
         * Available types:
         * 
         * - follow
         * - watch
         * - star
         * - fork
         * - issue
         */
        private function _getGitHubAPISlugByType( $sType ) {
            
            switch( strtolower( $sType ) ) {
                case 'follow':
                    return 'followers';
                case 'watch':    
                    return 'watchers';
                case 'star':    
                    return 'stargazers';
                case 'fork':
                    return 'network';
                case 'issue':
                    return 'issues';
                default:
                    return '';
            }
            
        }    
        /**
         * Returns the API count slug used for the given type.
         * 
         * Available types:
         * 
         * - follow
         * - watch
         * - star
         * - fork
         * - issue
         */
        private function _getGitHubAPICountSlugByType( $sType ) {
            switch( strtolower( $sType ) ) {
                case 'follow':
                    return 'followers';
                case 'watch':    
                    return 'subscribers_count';
                case 'star':    
                    return 'stargazers_count';
                case 'fork':
                    return 'forks_count';
                case 'issue':
                    return 'open_issues_count';
                default:
                    return '';
            }
        }
        
}
endif;