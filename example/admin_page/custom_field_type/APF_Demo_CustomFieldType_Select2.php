<?php
/**
 * Admin Page Framework Loader
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a tab that displays the `select2` field examples.
 *
 * @since       3.8.6
 */
class APF_Demo_CustomFieldType_Select2 {

    public $oFactory;

    public $sClassName;

    public $sPageSlug;

    public $sTabSlug = 'select2';

    public function __construct( $oFactory, $sPageSlug ) {

        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug;
        $this->sSectionID   = $this->sTabSlug;

        $this->oFactory->addInPageTabs(
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Select2', 'admin-page-framework-loader' ),
            )
        );

        // Register the field type.
        new Select2CustomFieldType( $this->sClassName );

        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );

    }

    /**
     * Triggered when the tab starts loading.
     *
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oAdminPage ) {

        add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );

        // validation_{page slug}_{tab slug}
        add_filter( 'validation_' . $this->sClassName . '_' . $this->sSectionID, array( $this, 'validate' ), 10, 4 );

         // Section
        $oAdminPage->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Select2', 'admin-page-framework-loader' ),
                'description'   => array(
                    __( 'This field type lets the user select predefined items with auto-complete.', 'admin-page-framework-loader' )
                    . ' ' .sprintf(
                        __( 'For the specifications of the <code>options</code> argument, see <a href="%1$s" target="blank">here</a>.', 'admin-page-framework-loader' ),
                        'https://select2.github.io/options.html'
                    ),
                ),
            )
        );

        // Fields
        $oAdminPage->addSettingFields(
            $this->sSectionID,
            array(
                'field_id'      => 'default',
                'type'          => 'select2',
                'title'         => __( 'Select2', 'admin-page-framework-loader' ),
                'label'         => array(
                    0 => __( 'Red', 'admin-page-framework-loader' ),
                    1 => __( 'Blue', 'admin-page-framework-loader' ),
                    2 => __( 'Yellow', 'admin-page-framework-loader' ),
                    3 => __( 'Orange', 'admin-page-framework-loader' ),
                ),
                'default'       => 2,
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'select2',
    'label'         => array(  
        0 => 'Red', 
        1 => 'Blue',
        2 => 'Yellow', 
        3 => 'Orange',
    ),     
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'multiple',
                'type'          => 'select2',
                'title'         => __( 'Multiple', 'admin-page-framework-loader' ),
                'is_multiple'   => true,
                'default'       => array( 3, 4 ), // note that PHP array indices are zero-base
                'label'         => array( 'January', 'February', 'March',
                    'April', 'May', 'June', 'July',
                    'August', 'September', 'November',
                    'October', 'December'
                ),
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'select2',
    'is_multiple'   => true, 
    'default'       => array( 3, 4 ), 
    'label'         => array( 'January', 'February', 'March',  
        'April', 'May', 'June', 'July',  
        'August', 'September', 'November',  
        'October', 'December'  
    ),     
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'group',
                'type'          => 'select2',
                'title'         => __( 'Group', 'admin-page-framework-loader' ),
                'default'       => 'b',
                'label'         => array(
                    'alphabets' => array(
                        'a' => 'a',
                        'b' => 'b',
                        'c' => 'c',
                    ),
                    'numbers' => array(
                        0 => '0',
                        1 => '1',
                        2 => '2',
                    ),
                ),
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'select2',
    'default'       => 'b', 
    'label'         => array(      
        'alphabets' => array(    
            'a' => 'a',      
            'b' => 'b',  
            'c' => 'c', 
        ), 
        'numbers' => array(  
            0 => '0', 
            1 => '1', 
            2 => '2',  
        ), 
    ), 
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'placeholder',
                'type'          => 'select2',
                'title'         => __( 'Placeholder', 'admin-page-framework-loader' ),
                'label'         => array( 'January', 'February', 'March',
                    'April', 'May', 'June', 'July',
                    'August', 'September', 'November',
                    'October', 'December'
                ),
                'options'       => array(
                    'placeholder' => __( 'Select a mounth', 'admin-page-framework' ),
                    'width'       => '50%',
                    'allowClear'  => true,
                ),
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'select2',
    'label'         => array( 'January', 'February', 'March',  
        'April', 'May', 'June', 'July',  
        'August', 'September', 'November',  
        'October', 'December'  
    ),     
    'options'       => array(
        'placeholder' => 'Select a mounth',
        'width'       => '50%',
        'allowClear' => true,
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'        => 'ajax',
                'type'            => 'select2',
                'title'           => __( 'Ajax', 'admin-page-framework-loader' ),
                'options'         => array(
                    'minimumInputLength' => 2,
                    'width' => '100%',
                ),
                'callback'        => array(
                    'search'    => __CLASS__ . '::getPosts',
                ),
                'description'     => array(
                    __( 'Post titles will be listed.', 'admin-page-framework-loader' ),
                    __( 'To use a data source with AJAX, set a callback function to the <code>search_callback</code> argument.', 'admin-page-framework-loader' )
                        . ' ' . __( 'From the callback function, return an array of lists.', 'admin-page-framework-loader' ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'select2',
    'options'         => array(
        'minimumInputLength' => 2,
        'width' => '100%',
    ),
    'callback'        => array(
        // If the `search` callback is set, the field will be AJAX based.
        'search'    => __CLASS__ . '::getPosts', 
    ),
)
EOD
                        )
                    . "</pre>",
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
function getPosts( \$aQueries, \$aFieldset ) {
            
    \$_aArgs         = array(
        'post_type'         => 'post',
        'paged'             => \$aQueries[ 'page' ],
        's'                 => \$aQueries[ 'q' ],
        'posts_per_page'    => 30,
        'nopaging'          => false,
    );
    \$_oResults      = new WP_Query( \$_aArgs );
    \$_aPostTitles   = array();
    foreach( \$_oResults->posts as \$_iIndex => \$_oPost ) {
        \$_aPostTitles[] = array(    // must be numeric
            'id'    => \$_oPost->ID,
            'text'  => \$_oPost->post_title,
        );
    }
    return array( 
        'results'       => \$_aPostTitles,
        'pagination'    => array(
            'more'  => intval( \$_oResults->max_num_pages ) !== intval( \$_oResults->get( 'paged' ) ),
        ),
    );
    
}    
EOD
                    )
                    . "</pre>",
                ),
            ),
            array(
                'field_id'          => 'ajax_multiple',
                'type'              => 'select2',
                'title'             => __( 'Ajax Multiple', 'admin-page-framework-loader' ),
                'is_multiple'       => true,
                'options'           => array(
                    'minimumInputLength'    => 2,
                    'width'                 => '100%',
                    'selectOnClose'         => true,
                    'tags'                  => true,    // user can create new tags
                    'tokenSeparators'       => array( ',', ),
                ),
                'callback'        => array(
                    // use a static class method or a funcion rather than an instantiated object method for faster processing.
                    'search'    => __CLASS__ . '::getTerms',
                    'new_tag'   => __CLASS__ . '::createTerm',
                ),
                'description'       => array(
                    __( 'Enter post tags, separated by commas.', 'admin-page-framework-loader' ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'select2',
    'is_multiple'       => true,
    'options'           => array(
        'minimumInputLength'    => 2,
        'width'                 => '100%',
        'selectOnClose'         => true,
        'tags'                  => true, // user can create new tags
        'tokenSeparators'       => array( ',', ),
    ),
    
    'callback'        => array(
        'search'    => __CLASS__ . '::getTerms',    // Ajax feature will be enabled 
        'new_tag'   => __CLASS__ . '::createTerm',  // `options`->`tags` will be enabled
    ),    
)
EOD
                        )
                    . "</pre>",
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
function getTerms( \$aQueries, \$aFieldset ) {
    
    \$_aArguments = array(
        'taxonomy'   => 'post_tag',
        'hide_empty' => false,
        'name__like' => \$aQueries[ 'q' ],
    );
    \$_aTerms = get_terms( \$_aArguments );
    if ( is_wp_error( \$_aTerms ) ) {
        return array(
            'results'       => array(),
        );
    }       
                        
    \$_aResults   = array();
    foreach( \$_aTerms as \$_iIndex => \$_oTerm ) {
        \$_aResults[] = array(    // must be numeric
            'id'    => \$_oTerm->term_id,
            'text'  => \$_oTerm->name,
        );
    }
                   
    return array( 
        'results'       => \$_aResults,
    );    
    
}
EOD
                    )
                    . "</pre>",
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
function createTerm( \$aQueries, \$aFieldset ) {

    \$_sTermName   = \$aQueries[ 'tag' ];
    \$_oTerm       = get_term_by( 'name', \$_sTermName, 'post_tag' );
    \$_bTermExists = is_object( \$_oTerm ) && isset( \$_oTerm->term_id );
    
    if ( \$_bTermExists ) {
        \$_iTermID     = \$_oTerm->term_id;
        return array( 
            'id'    => \$_iTermID,
            'text'  => \$_sTermName,
        );            
    }

    \$_aoResult = wp_insert_term( \$_sTermName, 'post_tag' );
    if ( is_wp_error( \$_aoResult )) {
        return array(
            'error' => \$_aoResult->get_error_message(),
        );
    }
    
    \$_aResults = \$_aoResult; // e.g. array('term_id'=>12,'term_taxonomy_id'=>34))
    \$_oTerm    = get_term( \$_aResults[ 'term_id' ], 'post_tag' );

    return array( 
        'id'    => \$_oTerm->term_id,
        'text'  => \$_oTerm->name,
    );

}
EOD
                    )
                    . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'repeatable_and_sortable',
                'type'          => 'select2',
                'title'         => __( 'Repeatable & Sortable', 'admin-page-framework-loader' ),
                'label'         => array( 'January', 'February', 'March',
                    'April', 'May', 'June', 'July',
                    'August', 'September', 'November',
                    'October', 'December'
                ),
                'repeatable'    => true,
                'sortable'      => true,
                'options'       => array(
                    'width'       => '400px',
                ),
                'description'     => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'select2',
    'label'         => array( 'January', 'February', 'March',  
        'April', 'May', 'June', 'July',  
        'August', 'September', 'November',  
        'October', 'December'  
    ),     
    'repeatable'    => true,
    'sortable'      => true,
    'options'       => array(
        'width'       => '400px',
    ),    
)
EOD
                        )
                    . "</pre>",
                ),
            ),
            array(
                'field_id'        => 'ajax_repeatable_sortable',
                'type'            => 'select2',
                'title'           => __( 'Ajax Repeatable and Sortable', 'admin-page-framework-loader' ),
                'repeatable'      => true,
                'sortable'        => true,
                'options'         => array(
                    'width' => '100%',
                ),
                'callback'        => array(
                    'search'    => __CLASS__ . '::getPosts',
                ),
                'description'     => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'select2',
    'repeatable'      => true,
    'sortable'        => true,
    'options'         => array(
        'width' => '100%',
    ),
    'callback'        => array(
        'search'    => __CLASS__ . '::getPosts', 
    ),    
)
EOD
                        )
                    . "</pre>",
                ),
            ),
            array()
        );

    }



    public function replyToDoTab() {
        submit_button();
    }

    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        return $aInputs;
    }


    /**
     * Retrieves and return posts with the array structure of `select2` AJAX format.
     *
     * <h4>Structure of Response Array</h4>
     * It must be an associative array with the element keys of `results` and `pagination`.
     * In the `results` element must be a numerically index array holding an array with the kes of `id` and `text`.
     * The `pagination` element can be optional and shouold be an array holding an element named `more` which accepts a boolean value.
     *
     * ```
     * array(
     *      'results'  => array(
     *          array( 'id' => 223, 'text' => 'Title of 223' ),
     *          array( 'id' => 665, 'text' => 'Title of 665' ),
     *          array( 'id' => 9355, 'text' => 'Title of 9355' ),
     *          ...
     *      ),
     *      'pagination' => array(
     *          'more'  => true,    // (boolean) or false - whether the next paginated item exists or not.
     *      )
     * )
     * ```
     * Or the `pagination` element can be omitted.
     * ```
     * array(
     *      'results'  => array(
     *          array( 'id' => 223, 'text' => 'Title of 223' ),
     *          array( 'id' => 665, 'text' => 'Title of 665' ),
     *          array( 'id' => 9355, 'text' => 'Title of 9355' ),
     *          ...
     *      ),
     * )
     * ```
     *
     * @access      static      For faster processing.
     * @remark      The arguments of the passed queries by select2 are `page` (the page number) and `q` (the user-typed keyword in the input).
     * @remark      For the WP_Query arguments, see https://codex.wordpress.org/Class_Reference/WP_Query#Pagination_Parameters
     * @see         https://codex.wordpress.org/Class_Reference/WP_Query#Pagination_Parameters
     * @see         https://select2.github.io/examples.html#data-ajax
     * @return      array
     */
    static public function getPosts( $aQueries, $aFieldset ) {

        $_aArgs         = array(
            'post_type'         => 'post',
            'paged'             => $aQueries[ 'page' ],
            's'                 => $aQueries[ 'q' ],
            'posts_per_page'    => 30,
            'nopaging'          => false,
        );
        $_oResults      = new WP_Query( $_aArgs );
        $_aPostTitles   = array();
        foreach( $_oResults->posts as $_iIndex => $_oPost ) {
            $_aPostTitles[] = array(    // must be numeric
                'id'    => $_oPost->ID,
                'text'  => $_oPost->post_title,
            );
        }
        return array(
            'results'       => $_aPostTitles,
            'pagination'    => array(
                'more'  => intval( $_oResults->max_num_pages ) !== intval( $_oResults->get( 'paged' ) ),
            ),
        );

    }

    /**
     * @return      array       An array holding the search result of taxonomy terms.
     */
    static public function getTerms( $aQueries, $aFieldset ) {

        $_aArguments = array(
            'taxonomy'   => 'post_tag',
            'hide_empty' => false,
            'name__like' => $aQueries[ 'q' ],
        );
        $_aTerms = get_terms( $_aArguments );
        if ( is_wp_error( $_aTerms ) ) {
            return array(
                'results'       => array(),
            );
        }

        $_aResults   = array();
        foreach( $_aTerms as $_iIndex => $_oTerm ) {
            $_aResults[] = array(    // must be numeric
                'id'    => $_oTerm->term_id,
                'text'  => $_oTerm->name,
            );
        }

        return array(
            'results'       => $_aResults,
        );

    }

    /**
     * Returns the created item with the format of select2 AJAX response data strucute.
     *
     * <h4>Structure of Response Array</h4>
     * It must be an associative array with the element keys of `id` and `text`, and optionally `error`/
     *
     * ```
     * array( 'id' => 223, 'text' => 'WordPress' )
     * ```
     *
     * ```
     * array( 'error' => 'Something went wrong' )
     * ```
     * @return      array
     */
    static public function createTerm( $aQueries, $aFieldset ) {

        $_sTermName   = $aQueries[ 'tag' ];
        $_oTerm       = get_term_by( 'name', $_sTermName, 'post_tag' );
        $_bTermExists = is_object( $_oTerm ) && isset( $_oTerm->term_id );

        if ( $_bTermExists ) {
            $_iTermID     = $_oTerm->term_id;
            return array(
                'id'    => $_iTermID,
                'text'  => $_sTermName,

                // for developers.
                'note'  => 'The term already existed. ID: ' . $_iTermID . ' Name: ' . $_sTermName,
            );
        }

        $_aoResult = wp_insert_term( $_sTermName, 'post_tag' );
        if ( is_wp_error( $_aoResult )) {
            return array(
                'error' => $_aoResult->get_error_message(),
            );
        }

        $_aResults = $_aoResult;    // e.g. array('term_id'=>12,'term_taxonomy_id'=>34))
        $_oTerm    = get_term( $_aResults[ 'term_id' ], 'post_tag' );

        return array(
            'id'    => $_oTerm->term_id,
            'text'  => $_oTerm->name,
            'note'  => 'A new term has been created. ID: ' . $_oTerm->term_id . ' Name: ' . $_oTerm->name,
        );

    }

}
