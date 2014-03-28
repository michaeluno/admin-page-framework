<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Factory' ) ) :
/**
 * The factory class for creating Admin Page Framework objects.
 * 
 * This class defines public methods that the user will interact with.
 * 
 * @abstract
 * @since			3.0.4
 * @subpackage		Factory
 * @internal
 * @todo			List up common methods and properties shared among other abstract classes and define them in this class.
 */
abstract class AdminPageFramework_Factory extends AdminPageFramework_Factory_Controller {}
endif;