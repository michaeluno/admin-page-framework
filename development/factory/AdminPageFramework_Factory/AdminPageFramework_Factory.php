<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * The factory class for creating Admin Page Framework objects.
 * 
 * This factory class consists of four componential classes by extending them.
 * 
 * - AdminPageFramework_Factory - The router class that deals with redirecting function calls and instantiation of classes to the appropriate object based on the factory type.
 * - AdminPageFramework_Model - The model class taht deals with retrieving data from the database or the framework properties.
 * - AdminPageFramework_View - The view class taht deals with displaying outputs.
 * - AdminPageFramework_Controller - The controller class which provides public methods that the framework users will interact with. They use methods to set up forms and admin messages etc.
 * 
 * @abstract
 * @since       3.0.4
 * @package     AdminPageFramework
 * @subpackage  Factory
 * @internal
 */
abstract class AdminPageFramework_Factory extends AdminPageFramework_Factory_Controller {}