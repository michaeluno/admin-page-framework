<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to build forms of the `post_type` structure type.
 *
 * The suffix represents the structure type of the form.
 *
 * The form class has methods that deal with setting notices and this class is used to access those.
 * Custom post type factory type does not have the ability to render forms however.
 *
 * @package     AdminPageFramework/Factory/PostType/Form
 * @since       3.7.0
 * @extends     AdminPageFramework_Form
 * @internal
 */
class AdminPageFramework_Form_post_type extends AdminPageFramework_Form {
    public $sStructureType = 'post_type';
}
