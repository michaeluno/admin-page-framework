<?php
if ( ! class_exists( 'AdminPageFramework_Property_TaxonomyField' ) ) :
/**
 * Provides the space to store the shared properties for taxonomy fields.
 *  
 * @since			3.0.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 * @extends			AdminPageFramework_Property_MetaBox
 */
class AdminPageFramework_Property_TaxonomyField extends AdminPageFramework_Property_MetaBox {

	/**
	 * Stores the associated taxonomy slugs to the taxonomy field object.
	 * @since			3.0.0
	 */
	public $aTaxonomySlugs;
	
	/**
	 * Stores the option key for the options table.
	 * 
	 * If the user does not set it, the class name will be applied.
	 * @since			3.0.0
	 */
	public $sOptionKey;

}
endif;