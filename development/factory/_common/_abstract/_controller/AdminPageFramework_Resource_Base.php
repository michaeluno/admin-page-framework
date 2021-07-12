<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to enqueue or insert resource elements.
 *
 * The class handles `<link>`, `<style>` and `<script>` tags to be inserted conditionally into the head tag or the footer of the page.
 *
 * @abstract
 * @since       2.1.5
 * @since       3.3.0       Changed the name from `AdminPageFramework_HeadTag_Base`.
 * @since       3.6.3       Changed it to extend `AdminPageFramework_WPUtility`.
 * @extends     AdminPageFramework_FrameworkUtility
 * @package     AdminPageFramework/Common/Factory/Resource
 * @internal
 * @extends     AdminPageFramework_FrameworkUtility
 */
abstract class AdminPageFramework_Resource_Base extends AdminPageFramework_FrameworkUtility {

    /**
     * Represents the structure of the array for enqueuing scripts and styles.
     *
     * @since       2.1.2
     * @since       2.1.5       Moved to the base class.
     * @since       3.0.0       Moved from the property class.
     * @since       3.3.0       Changed the name to `$_aStructure_EnqueuingResources` from `$_aStructure_EnqueuingScriptsAndStyles`.
     * @internal
     */
    protected static $_aStructure_EnqueuingResources = array(

        /* The system internal keys. */
        'sSRC'          => null,
        'aPostTypes'    => array(),     // for meta box class
        'sPageSlug'     => null,
        'sTabSlug'      => null,
        'sType'         => null,        // script or style

        /* The below keys are for users. */
        'handle_id'     => null,
        'dependencies'  => array(),
        'version'       => false,       // although the type should be string, the wp_enqueue_...() functions want false as the default value.
        'translation'   => array(),     // only for scripts
        'in_footer'     => false,       // only for scripts
        'media'         => 'all',       // only for styles
        'attributes'    => array(),     // 3.3.0+ - the attribute array

    );

    /**
     * Stores the class selector used for the class-specific style.
     *
     * @since       3.2.0
     * @remark      This value should be overridden in an extended class.
     * @internal
     */
    protected $_sClassSelector_Style    = 'admin-page-framework-style';

    /**
     * Stores the class selector used to the class-specific script.
     *
     * @since       3.2.0
     * @remark      This value should be overridden in an extended class.
     * @internal
     */
    protected $_sClassSelector_Script   = 'admin-page-framework-script';

    /**
     * Stores hand IDs by resource url to look up handle id and add custom arguments.
     * @since       3.3.0
     * @internal
     */
    protected $_aHandleIDs = array();

    /**
     * A property object.
     *
     * @remark      Set in the constructor.
     */
    public $oProp;

    /**
     * A utility object.
     *
     * @remark      Set in the constructor.
     * @deprecated  3.6.3
     * @remark      kept for backward compatibility.
     * @var         AdminPageFramework_WPUtility
     */
    public $oUtil;

    /**
     * Sets up properties and hooks.
     * @internal
     */
    public function __construct( $oProp ) {

        $this->oProp = $oProp;

        // for backward compatibility
        $this->oUtil = new AdminPageFramework_WPUtility;

        if ( $this->isDoingAjax() ) {
            return;
        }

        // Hook the admin header to insert custom admin stylesheets and scripts.
        add_action( 'admin_enqueue_scripts', array( $this, '_replyToEnqueueScripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, '_replyToEnqueueStyles' ) );

        /// A low priority is required to let dependencies loaded fast especially in customizer.php.
        add_action( did_action( 'admin_print_styles' ) ? 'admin_print_footer_scripts' : 'admin_print_styles', array( $this, '_replyToAddStyle' ), 999 );
        add_action( did_action( 'admin_print_scripts' ) ? 'admin_print_footer_scripts' : 'admin_print_scripts', array( $this, '_replyToAddScript' ), 999 );

        // Take care of items that could not be added in the head tag.

        /// For wp-admin/customizer.php
        add_action( 'customize_controls_print_footer_scripts', array( $this, '_replyToEnqueueScripts' ) );
        add_action( 'customize_controls_print_footer_scripts', array( $this, '_replyToEnqueueStyles' ) );

        /// For admin pages other than wp-admin/customizer.php
        add_action( 'admin_footer', array( $this, '_replyToEnqueueScripts' ) );
        add_action( 'admin_footer', array( $this, '_replyToEnqueueStyles' ) );

        /// For all admin pages.
        add_action( 'admin_print_footer_scripts', array( $this, '_replyToAddStyle' ), 999 );
        add_action( 'admin_print_footer_scripts', array( $this, '_replyToAddScript' ), 999 );


        // To add the custom attributes to the enqueued style and script tags.
        add_filter( 'script_loader_src', array( $this, '_replyToSetupArgumentCallback' ), 1, 2 );
        add_filter( 'style_loader_src', array( $this, '_replyToSetupArgumentCallback' ), 1, 2 );

    }

    /*
     * Methods that should be overridden in extended classes.
     * @internal
     */

    public function _forceToEnqueueStyle( $sSRC, $aCustomArgs=array() ) {}
    public function _forceToEnqueueScript( $sSRC, $aCustomArgs=array() ) {}

    /**
     * A helper function for the _replyToEnqueueScripts() and the `_replyToEnqueueStyle()` methods.
     *
     * @since       2.1.5
     * @since       3.7.0      Fixed a typo in the method name.
     * @internal
     * @remark      The widget fields type does not have conditions unlike the meta-box type that requires to check currently loaded post type.
     * @remark      This method should be redefined in the extended class.
     */
    protected function _enqueueSRCByCondition( $aEnqueueItem ) {
        return $this->_enqueueSRC( $aEnqueueItem );
    }

    /*
     * Shared methods
     */
    	/**
         * Checks the src url of the enqueued script/style to determine whether or not to set up a attribute modification callback.
         *
         * If it is one of the framework added item, the method sets up a hook to modify the url to add custom attributes.
         *
         * @since       3.3.0
         * @internal
         * @callback    action      script_loader_src
         * @callback    action      style_loader_src
         */
        public function _replyToSetupArgumentCallback( $sSRC, $sHandleID ) {

            if ( isset( $this->oProp->aResourceAttributes[ $sHandleID ] ) ) {
                $this->_aHandleIDs[ $sSRC ] = $sHandleID;
                add_filter( 'clean_url', array( $this, '_replyToModifyEnqueuedAttributes' ), 1, 3 );
                remove_filter( current_filter(), array( $this, '_replyToSetupArgumentCallback' ), 1 );
            }
            return $sSRC;

        }
            /**
             * Modifies the attributes of the enqueued script tag.
             *
             * @since   3.3.0
             * @internal
             */
            public function _replyToModifyEnqueuedAttributes( $sSanitizedURL, $sOriginalURL, $sContext ) {

                if ( 'display' !== $sContext ) {
                    return $sSanitizedURL;
                }

                // Returns the modified url which attributes are embedded at the end.
                if ( isset( $this->_aHandleIDs[ $sOriginalURL ] ) ) {

                    $_sHandleID     = $this->_aHandleIDs[ $sOriginalURL ];
                    $_aAttributes   = $this->oProp->aResourceAttributes[ $_sHandleID ];

                    if ( empty( $_aAttributes ) ) {
                        return $sSanitizedURL;
                    }

                    $_sAttributes   = $this->getAttributes( $_aAttributes );
                    return $sSanitizedURL . "' " . rtrim( $_sAttributes, "'\"" );

                }

                return $sSanitizedURL;

            }

    /**
     * Prints the inline stylesheet of the meta-box common CSS rules with the style tag.
     *
     * @internal
     * @since       3.0.0
     * @since       3.2.0       Moved to the base class from the meta box class.
     * @remark      The meta box class may be instantiated multiple times so prevent echoing the same styles multiple times.
     * @parameter   string      $sIDPrefix   The id selector embedded in the script tag.
     * @parameter   string      $sClassName  The class name that identify the call group. This is important for the meta-box class because it can be instantiated multiple times in one particular page.
     */
    protected function _printCommonStyles( $sIDPrefix, $sClassName ) {

        if ( $this->hasBeenCalled( 'COMMON_STYLES: ' . get_class( $this ) . '::' . __METHOD__ ) ) {
            return;
        }
        $_oCaller = $this->oProp->oCaller;
        echo $this->___getCommonStyleTag( $_oCaller, $sIDPrefix );
        echo $this->___getCommonIEStyleTag( $_oCaller, $sIDPrefix );

    }
        /**
         * @internal
         * @since       3.5.7
         * @since       3.8.22  Renamed from `_getStyleTag()`.
         * @return      string
         */
        private function ___getCommonStyleTag( $oCaller, $sIDPrefix ) {

            $_sStyle     = $this->addAndApplyFilters(
                $oCaller,
                array(
                    "style_common_admin_page_framework",            // 3.2.1+
                    "style_common_{$this->oProp->sClassName}",
                ),
                AdminPageFramework_CSS::getDefaultCSS()
            );
            $_sStyle     = $this->isDebugMode() ? $_sStyle : $this->getCSSMinified( $_sStyle );
            $_sStyle     = trim( $_sStyle );
            if ( $_sStyle ) {
                echo "<style type='text/css' id='" . esc_attr( strtolower( $sIDPrefix ) ) . "'>"
                        . $_sStyle
                    . "</style>";
            }


        }
        /**
         * @internal
         * @since       3.5.7
         * @since       3.8.22  Renamed from `_getIEStyleTag()`.
         * @return      string
         */
        private function ___getCommonIEStyleTag( $oCaller, $sIDPrefix ) {

            $_sStyleIE   = $this->addAndApplyFilters(
                $oCaller,
                array(
                    "style_ie_common_admin_page_framework",         // 3.2.1+
                    "style_ie_common_{$this->oProp->sClassName}",
                ),
                AdminPageFramework_CSS::getDefaultCSSIE()
            );
            $_sStyleIE  = $this->isDebugMode() ? $_sStyleIE : $this->getCSSMinified( $_sStyleIE );
            $_sStyleIE  = trim( $_sStyleIE );
            return $_sStyleIE
                ? "<!--[if IE]><style type='text/css' id='" . esc_attr( strtolower( $sIDPrefix . "-ie" ) ) . "'>"
                        . $_sStyleIE
                    . "</style><![endif]-->"
                : '';

        }

    /**
     * Prints the inline scripts of the meta-box common scripts.
     *
     * @internal
     * @since       3.0.0
     * @since       3.2.0       Moved to the base class from the meta box class.
     * @remark      The meta box class may be instantiated multiple times so prevent echoing the same styles multiple times.
     * @parametr    string      $sIDPrefix      The id selector embedded in the script tag.
     * @parametr    string      $sClassName     The class name that identify the call group. This is important for the meta-box class because it can be instantiated multiple times in one particular page.
     */
    protected function _printCommonScripts( $sIDPrefix, $sClassName ) {

        if ( $this->hasBeenCalled( 'COMMON_SCRIPT: ' . get_class( $this ) . '::' . __METHOD__ ) ) {
            return;
        }

        $_sScript = $this->addAndApplyFilters(
            $this->oProp->oCaller,
            array(
                "script_common_admin_page_framework",       // 3.2.1+
                "script_common_{$this->oProp->sClassName}",
            ),
            AdminPageFramework_Property_Base::$_sDefaultScript
        );
        $_sScript = trim( $_sScript );
        if ( ! $_sScript ) {
            return;
        }
        echo "<script type='text/javascript' id='" . esc_attr( strtolower( $sIDPrefix ) ) . "'>"
                . '/* <![CDATA[ */'
                . $_sScript
                . '/* ]]> */'
            . "</script>";

    }

    /**
     * Prints the inline stylesheet of this class stored in this class property.
     *
     * @since       3.0.0
     * @since       3.2.0       Made the properties storing styles empty. Moved to the base class.
     * @internal
     * @return      void
     */
    protected function _printClassSpecificStyles( $sIDPrefix ) {

        $_oCaller   = $this->oProp->oCaller;
        echo $this->_getClassSpecificStyleTag( $_oCaller, $sIDPrefix );
        echo $this->_getClassSpecificIEStyleTag( $_oCaller, $sIDPrefix );

        // As of 3.2.0, this method also gets called in the footer to ensure there is not any left styles.
        // This happens when a head tag item is added after the head tag is already rendered such as for widget forms.
        $this->oProp->sStyle    = '';
        $this->oProp->sStyleIE  = '';

    }
        /**
         *
         * @internal
         * @since       3.5.7
         * @return      string
         */
        private function _getClassSpecificStyleTag( $_oCaller, $sIDPrefix ) {

            static $_iCallCount = 0;

            $_sFilterName = "style_{$this->oProp->sClassName}";
            if ( $this->hasBeenCalled( 'FILTER: ' . $_sFilterName ) ) { // 3.8.22
                return '';
            }
            $_sStyle = $this->addAndApplyFilters( $_oCaller, $_sFilterName, $this->oProp->sStyle );
            $_sStyle = $this->isDebugMode() ? $_sStyle : $this->getCSSMinified( $_sStyle );
            $_sStyle = trim( $_sStyle );
            if ( ! $_sStyle ) {
                return '';
            }
            $_iCallCount++;
            $_sID = strtolower( "{$sIDPrefix}-" . $this->oProp->sClassName . "_{$_iCallCount}" );
            return "<style type='text/css' id='" . esc_attr( $_sID ) . "'>"
                    . $_sStyle
                . "</style>";

        }
        /**
         *
         * @internal
         * @since       3.5.7
         * @return      string
         */
        private function _getClassSpecificIEStyleTag( $_oCaller, $sIDPrefix ) {

            static $_iCallCountIE = 1;

            $_sFilterName = "style_ie_{$this->oProp->sClassName}";
            if ( $this->hasBeenCalled( 'FILTER: ' . $_sFilterName ) ) { // 3.8.22
                return '';
            }
            $_sStyleIE = $this->addAndApplyFilters( $_oCaller, $_sFilterName, $this->oProp->sStyleIE );
            $_sStyleIE = $this->isDebugMode() ? $_sStyleIE : $this->getCSSMinified( $_sStyleIE );
            $_sStyleIE = trim( $_sStyleIE );
            if ( ! $_sStyleIE ) {
                return '';
            }
            $_iCallCountIE++;
            $_sID  = strtolower( "{$sIDPrefix}-ie-{$this->oProp->sClassName}_{$_iCallCountIE}" );
            return "<!--[if IE]><style type='text/css' id='" . esc_attr( $_sID ) . "'>"
                    . $_sStyleIE
                . "</style><![endif]-->";

        }

    /**
     * Prints the inline scripts of this class stored in this class property.
     *
     * @since       3.0.0
     * @since       3.2.0       Made the property empty that stores scripts. Moved to the base class.
     * @internal
     */
    protected function _printClassSpecificScripts( $sIDPrefix ) {

        static $_iCallCount = 1;
        $_sFilterName = "script_{$this->oProp->sClassName}";
        if ( $this->hasBeenCalled( 'FILTER: ' . $_sFilterName ) ) { // 3.8.22
            return '';
        }
        $_sScript = $this->addAndApplyFilters( $this->oProp->oCaller, $_sFilterName, $this->oProp->sScript );
        $_sScript = trim( $_sScript );
        if ( ! $_sScript ) {
            return '';
        }

        $_iCallCount++;
        $_sID = strtolower( "{$sIDPrefix}-{$this->oProp->sClassName}_{$_iCallCount}" );
        echo "<script type='text/javascript' id='" . esc_attr( $_sID ) . "'>"
                . '/* <![CDATA[ */'
                . $_sScript
                . '/* ]]> */'
            . "</script>";

        // As of 3.2.0, this method also gets called in the footer to ensure there is not any left scripts.
        // This happens when a head tag item is added after the head tag is already rendered such as for widget forms.
        $this->oProp->sScript = '';

    }

    /**
     * Appends the CSS rules of the framework in the head tag.
     *
     * @since       2.0.0
     * @since       2.1.5       Moved from `AdminPageFramework_MetaBox`. Changed the name from `addAtyle()` to `replyToAddStyle()`.
     * @callback    action      admin_head
     * @internal
     */
    public function _replyToAddStyle() {

        $_oCaller = $this->oProp->oCaller;
        if ( ! $_oCaller->isInThePage() ) {
            return;
        }

        $this->_printCommonStyles( 'admin-page-framework-style-common', get_class() );
        $this->_printClassSpecificStyles( $this->_sClassSelector_Style . '-' . $this->oProp->sStructureType );

    }
    /**
     * Appends the JavaScript script of the framework in the head tag.
     *
     * @callback    action      admin_head
     * @since       2.0.0
     * @since       2.1.5       Moved from AdminPageFramework_MetaBox. Changed the name from `addScript()` to `replyToAddScript()`.
     * @since       3.2.0       Moved from AdminPageFramework_Resource_post_meta_box.
     * @internal
     */
    public function _replyToAddScript() {

        $_oCaller = $this->oProp->oCaller;
        if ( ! $_oCaller->isInThePage() ) {
            return;
        }

        $this->_printCommonScripts( 'admin-page-framework-script-common', get_class() );
        $this->_printClassSpecificScripts( $this->_sClassSelector_Script . '-' . $this->oProp->sStructureType );

    }

    /**
     * Performs actual enqueuing items.
     *
     * @since       2.1.2
     * @since       2.1.5       Moved from the main class.
     * @param       array       $aEnqueueItem
     * @internal
     */
    protected function _enqueueSRC( $aEnqueueItem ) {

        // For styles
        if ( 'style' === $aEnqueueItem[ 'sType' ] ) {
            wp_enqueue_style(
                $aEnqueueItem[ 'handle_id' ],
                $aEnqueueItem[ 'sSRC' ],
                $aEnqueueItem[ 'dependencies' ],
                $aEnqueueItem[ 'version' ],
                $aEnqueueItem[ 'media' ]
            );
            return;
        }

        // For scripts
        wp_enqueue_script(
            $aEnqueueItem[ 'handle_id' ],
            $aEnqueueItem[ 'sSRC' ],
            $aEnqueueItem[ 'dependencies' ],
            $aEnqueueItem[ 'version' ],
            did_action( 'admin_body_class' ) ? true : $aEnqueueItem[ 'in_footer' ]
        );

        if ( $aEnqueueItem[ 'translation' ] ) {
            wp_localize_script( $aEnqueueItem[ 'handle_id' ], $aEnqueueItem[ 'handle_id' ], $aEnqueueItem[ 'translation' ] );
        }

    }

    /**
     * Takes care of added enqueuing scripts by checking the currently loading page.
     *
     * @remark      A callback for the admin_enqueue_scripts hook.
     * @since       2.1.2
     * @since       2.1.5   Moved from the main class. Changed the name from `enqueueStylesCalback` to `replyToEnqueueStyles()`.
     * @since       3.0.0   Changed the name to `_replyToEnqueueStyles()`.
     * @since       3.2.0   Changed it unset the enqueued item so that the method can be called multiple times.
     * @internal
     */
    public function _replyToEnqueueStyles() {
        foreach( $this->oProp->aEnqueuingStyles as $_sKey => $_aEnqueuingStyle ) {
            $this->_enqueueSRCByCondition( $_aEnqueuingStyle );
            unset( $this->oProp->aEnqueuingStyles[ $_sKey ] );
        }
    }

    /**
     * Takes care of added enqueuing scripts by page slug and tab slug.
     *
     * @remark      A callback for the admin_enqueue_scripts hook.
     * @since       2.1.2
     * @since       2.1.5   Moved from the main class. Changed the name from `enqueueScriptsCallback` to `callbackEnqueueScripts()`.
     * @since       3.0.0   Changed the name to `_replyToEnqueueScripts()`.
     * @since       3.2.0   Changed it unset the enqueued item so that the method can be called multiple times.
     * @internal
     */
    public function _replyToEnqueueScripts() {
        foreach( $this->oProp->aEnqueuingScripts as $_sKey => $_aEnqueuingScript ) {
            $this->_enqueueSRCByCondition( $_aEnqueuingScript );
            unset( $this->oProp->aEnqueuingScripts[ $_sKey ] );
        }
    }

    /**
     * Enqueues a resource.
     *
     * @since       3.5.3
     * @since       3.8.31      Moved from `AdminPageFramework_Resource_admin_page`.
     * @param       string      $sSRC           The source path or url.
     * @param       array       $aCustomArgs    A custom argument array.
     * @param       string      $sType          Accepts 'style' or 'script'
     * @return      string      The script handle ID if added. If the passed url is not a valid url string, an empty string will be returned.
     * @internal
     */
    protected function _enqueueResourceByType( $sSRC, array $aCustomArgs=array(), $sType='style' ) {

        $sSRC       = trim( $sSRC );
        if ( empty( $sSRC ) ) {
            return '';
        }
        $_sRawSRC   = wp_normalize_path( $sSRC );
        $_sSRC      = $this->getResolvedSRC( $_sRawSRC );

        // Get the property name for the type
        $_sContainerPropertyName     = $this->___getContainerPropertyNameByType( $sType );
        $_sEnqueuedIndexPropertyName = $this->___getEnqueuedIndexPropertyNameByType( $sType );

        $this->oProp->{$_sContainerPropertyName}[ $_sSRC ] = array_filter( $this->getAsArray( $aCustomArgs ), array( $this, 'isNotNull' ) )
            + array(
                'sSRCRaw'   => $_sRawSRC,
                'sSRC'      => $_sSRC,
                'sType'     => $sType,
                'handle_id' => $sType . '_' . $this->oProp->sClassName . '_' .  ( ++$this->oProp->{$_sEnqueuedIndexPropertyName} ),
            )
            + self::$_aStructure_EnqueuingResources;

        // Store the attributes in another container by url.
        $this->oProp->aResourceAttributes[ $this->oProp->{$_sContainerPropertyName}[ $_sSRC ]['handle_id'] ] = $this->oProp->{$_sContainerPropertyName}[ $_sSRC ]['attributes'];

        return $this->oProp->{$_sContainerPropertyName}[ $_sSRC ][ 'handle_id' ];

    }
        /**
         * Returns the property name that contains the information of resources by type.
         * @since   3.5.3
         * @since   3.8.31      Moved from `AdminPageFramework_Resource_admin_page`.
         * @return  string      the property name that contains the information of resources by type.
         */
        private function ___getContainerPropertyNameByType( $sType ) {
            switch ( $sType ) {
                default:
                case 'style':
                    return 'aEnqueuingStyles';
                case 'script':
                    return 'aEnqueuingScripts';
            }
        }
        /**
         * Returns the property name that contains the added count of resources by type.
         * @since   3.5.3
         * @since   3.8.31      Moved from `AdminPageFramework_Resource_admin_page`.
         * @return  string      the property name that contains the added count of resources by type.
         */
        private function ___getEnqueuedIndexPropertyNameByType( $sType ) {
            switch ( $sType ) {
                default:
                case 'style':
                    return 'iEnqueuedStyleIndex';
                case 'script':
                    return 'iEnqueuedScriptIndex';
            }
        }

}