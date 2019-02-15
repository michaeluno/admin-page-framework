<?php
/**
 * Loads Admin Page Framework loader plugin components.
 *
 * @package      Admin Page Framework Loader
 * @copyright    Copyright (c) 2013-2019, Michael Uno
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 *
 */

/**
 * Performs events.
 *
 * @since       3.5.2
 */
final class AdminPageFrameworkLoader_Event {

    /**
     * Performs events.
     *
     * @since       3.6.2
     */
    public function __construct() {

        new AdminPageFrameworkLoader_Event_Action_GetDevelopmentVersion(
            AdminPageFrameworkLoader_Registry::HOOK_SLUG . '_action_get_development_version'  // action name
        );

    }


}
