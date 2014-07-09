<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_WPUtility_Hook' ) ) :
/**
 * Provides utility methods regarding WordPress hooks (actions and filters) which use WordPress built-in functions and classes.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_Utility
 * @package			AdminPageFramework
 * @subpackage		Utility
 * @internal
 */
class AdminPageFramework_WPUtility_Hook extends AdminPageFramework_WPUtility_Page {
	
	/**
	 * Triggers the do_action() function with the given action names and the arguments.
	 * 
	 * This is useful to perform do_action() on multiple action hooks with the same set of arguments.
	 * For example, if there are the following action hooks, <em>action_name</em>, <em>action_name1</em>, and <em>action_name2</em>, and to perform these, normally it takes the following lines.
	 * <code>do_action( 'action_name1', $var1, $var2 );
	 * do_action( 'action_name2', $var1, $var2 );
	 * do_action( 'action_name3', $var1, $var2 );</code>
	 * 
	 * This method saves these line this way:
	 * <code>$this->doActions( array( 'action_name1', 'action_name2', 'action_name3' ), $var1, $var2 );</code>
	 * 
	 * <h4>Example</h4>
	 * <code>$this->doActions( array( 'action_name1' ), $var1, $var2, $var3 );</code> 
	 * 
	 * @since			2.0.0
	 * @access			public
	 * @remark			Accepts variadic parameters; the number of accepted parameters are not limited to four.
	 * @param			array			$aActionHooks			a numerically indexed array consisting of action hook names to execute.
	 * @param			mixed			$vArgs1					an argument to pass to the action callbacks.
	 * @param			mixed			$vArgs2					another argument to pass to the action callbacks.
	 * @param			mixed			$_and_more				add as many arguments as necessary to the next parameters.
	 * @return			void			does not return a value.
	 */		
	static public function doActions( $aActionHooks, $vArgs1=null, $vArgs2=null, $_and_more=null ) {
		
		$aArgs = func_get_args();		
		$aActionHooks = $aArgs[ 0 ];
		foreach( ( array ) $aActionHooks as $sActionHook  ) {
			$aArgs[ 0 ] = $sActionHook;
			call_user_func_array( 'do_action' , $aArgs );
		}

	}
	
	/**
	 * Adds the method of the given action hook name(s) to the given action hook(s) with arguments.
	 * 
	 * In other words, this enables to register methods to the custom hooks with the same name and triggers the callbacks (not limited to the registered ones) assigned to the hooks. 
	 * Of course, the registered methods will be triggered right away. Thus, the magic overloading __call() should catch them and redirect the call to the appropriate methods.
	 * This enables, at the same time, publicly the added custom action hooks; therefore, third-party scripts can use the action hooks.
	 * 
	 * This is the reason the object instance must be passed to the first parameter. Regular functions as the callback are not supported for this method.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->oUtil->addAndDoActions( $this, array( 'my_action1', 'my_action2', 'my_action3' ), 'argument_a', 'argument_b' );</code>
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @remark			Accepts variadic parameters.
	 * @param			object			$oCallerObject			the object that holds the callback method that matches the action hook name.
	 * @param			array			$aActionHooks			a numerically index array consisting of action hook names that serve as the callback method names. 
	 * @param			mixed			$vArgs1					the argument to pass to the hook callback functions.
	 * @param			mixed			$vArgs2					another argument to pass to the hook callback functions.
	 * @param			mixed			$_and_more				add as many arguments as necessary to the next parameters.
	 * @return			void
	 */ 
	static public function addAndDoActions( $oCallerObject, $aActionHooks, $vArgs1=null, $vArgs2=null, $_and_more=null ) {
	
		$aArgs = func_get_args();	
		$oCallerObject = $aArgs[ 0 ];
		$aActionHooks = $aArgs[ 1 ];
		foreach( ( array ) $aActionHooks as $sActionHook ) {
			if ( ! $sActionHook ) continue;
			$aArgs[ 1 ] = $sActionHook;
			call_user_func_array( array( 'self', 'addAndDoAction' ) , $aArgs );			
		}
		
	}
	
	/**
	 * Adds the methods of the given action hook name to the given action hook with arguments.
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @remark			Accepts variadic parameters.
	 * @return			void
	 */ 
	static public function addAndDoAction( $oCallerObject, $sActionHook, $vArgs1=null, $vArgs2=null, $_and_more=null ) {
		
		$iArgs = func_num_args();
		$aArgs = func_get_args();
		$oCallerObject = $aArgs[ 0 ];
		$sActionHook = $aArgs[ 1 ];
		if ( ! $sActionHook ) return;
		add_action( $sActionHook, array( $oCallerObject, $sActionHook ), 10, $iArgs - 2 );
		unset( $aArgs[ 0 ] );	// remove the first element, the caller object
		call_user_func_array( 'do_action' , $aArgs );
		
	}
	static public function addAndApplyFilters() {	// Parameters: $oCallerObject, $aFilters, $vInput, $vArgs...
			
		$aArgs = func_get_args();	
		$oCallerObject = $aArgs[ 0 ];
		$aFilters = $aArgs[ 1 ];
		$vInput = $aArgs[ 2 ];

		foreach( ( array ) $aFilters as $sFilter ) {
			if ( ! $sFilter ) continue;
			$aArgs[ 1 ] = $sFilter;
			$aArgs[ 2 ] = $vInput;
			$vInput = call_user_func_array( array( 'self', 'addAndApplyFilter' ) , $aArgs );						
		}
		return $vInput;
		
	}
	static public function addAndApplyFilter() {	// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...

		$iArgs = func_num_args();
		$aArgs = func_get_args();
		$oCallerObject = $aArgs[ 0 ];
		$sFilter = $aArgs[ 1 ];
		if ( ! $sFilter ) return $aArgs[ 2 ];
		add_filter( $sFilter, array( $oCallerObject, $sFilter ), 10, $iArgs - 2 );	// this enables to trigger the method named $sFilter and the magic method __call() will be called
		unset( $aArgs[ 0 ] );	// remove the first element, the caller object	// array_shift( $aArgs );							
		return call_user_func_array( 'apply_filters', $aArgs );	// $aArgs: $vInput, $vArgs...
		
	}		
	
	/**
	 * Provides an array consisting of filters for the addAndApplyFileters() method.
	 * 
	 * The order is, page + tab -> page -> class, by default but it can be reversed with the <var>$bReverse</var> parameter value.
	 * 
	 * @since			2.0.0
	 * @access			public
	 * @return				array			Returns an array consisting of the filters.
	 */ 
	static public function getFilterArrayByPrefix( $sPrefix, $sClassName, $sPageSlug, $sTabSlug, $bReverse=false ) {
				
		$aFilters = array();
		if ( $sTabSlug && $sPageSlug )
			$aFilters[] = "{$sPrefix}{$sPageSlug}_{$sTabSlug}";
		if ( $sPageSlug )	
			$aFilters[] = "{$sPrefix}{$sPageSlug}";			
		if ( $sClassName )
			$aFilters[] = "{$sPrefix}{$sClassName}";
		
		return $bReverse ? array_reverse( $aFilters ) : $aFilters;	
		
	}	
	
}
endif;