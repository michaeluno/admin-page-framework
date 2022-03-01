<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * The base factory class for factory components.
 *
 * This factory class consists of four classes by extending them.
 *
 * - AdminPageFramework_Factory - The router class that deals with redirecting function calls and instantiation of classes to the appropriate object based on the factory type.
 * - AdminPageFramework_Model - The model class that deals with retrieving data from the database or the framework properties.
 * - AdminPageFramework_View - The view class that deals with displaying outputs.
 * - AdminPageFramework_Controller - The controller class which provides public methods that the framework users will interact with. They use methods to set up forms and admin messages etc.
 *
 * @abstract
 * @since       3.0.4
 * @package     AdminPageFramework/Common/Factory
 * @internal
 */
abstract class AdminPageFramework_Factory extends AdminPageFramework_Factory_Controller {}
