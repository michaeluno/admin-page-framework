<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods of views for the widget factory class.
 * 
 * Those methods are internal and deal with printing outputs.
 * 
 * @abstract
 * @since       3.2.0
 * @package     AdminPageFramework
 * @subpackage  Factory/Widget
 */
abstract class AdminPageFramework_Widget_View extends AdminPageFramework_Widget_Model {    
 
    /**
     * Filters the post type post content.
     * 
     * This method is called in the same timing of the `content_{instantiated class name}`. This is shorthand for it.
     * 
     * <h4>Example</h4>
     * <code>
     *  public function content( $sContent, $aArguments, $aFormData ) {
     *      
     *      return $sContent
     *          . '<p>' . __( 'Hello world! This is a widget created by Admin Page Framework with some custom field types.', 'admin-page-framework-demo' ) . '</p>'
     *          . AdminPageFramework_Debug::get( $aArguments )
     *          . AdminPageFramework_Debug::get( $aFormData );
     *          
     *  }   
     * </code>
     * 
     * @remark  This class should be overridden in the extended class so that the user can display own contents.
     * @since   3.2.0
     * @return  string
     */
    public function content( $sContent, $aArguments, $aFormData ) { 
        return $sContent; 
    }
    
    /**
     * Prints out the widget form.
     * 
     * @since       3.2.0
     * @internal
     */
    public function _printWidgetForm() {
        echo $this->oForm->get();   
    }
    
    
}
