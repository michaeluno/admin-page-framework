<?php
/*
 * Admin Page Framework v3.9.0b15 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_PageMetaBox_Controller extends AdminPageFramework_PageMetaBox_View
{
    public function enqueueStyles()
    {
        if (! method_exists($this->oResource, '_enqueueResourcesByType')) {
            return array();
        }
        $_aParams = func_get_args() + array( array(), '', '', array() );
        return $this->oResource->_enqueueResourcesByType($_aParams[ 0 ], array( 'sPageSlug' => $_aParams[ 1 ], 'sTabSlug' => $_aParams[ 2 ], ) + $_aParams[ 3 ], 'style');
    }
    public function enqueueStyle()
    {
        if (! method_exists($this->oResource, '_addEnqueuingResourceByType')) {
            return '';
        }
        $_aParams = func_get_args() + array( '', '', '', array() );
        return $this->oResource->_addEnqueuingResourceByType($_aParams[ 0 ], array( 'sPageSlug' => $_aParams[ 1 ], 'sTabSlug' => $_aParams[ 2 ], ) + $_aParams[ 3 ], 'style');
    }
    public function enqueueScripts()
    {
        if (! method_exists($this->oResource, '_enqueueResourcesByType')) {
            return array();
        }
        $_aParams = func_get_args() + array( array(), '', '', array() );
        return $this->oResource->_enqueueResourcesByType($_aParams[ 0 ], array( 'sPageSlug' => $_aParams[ 1 ], 'sTabSlug' => $_aParams[ 2 ], ) + $_aParams[ 3 ], 'script');
    }
    public function enqueueScript()
    {
        if (! method_exists($this->oResource, '_addEnqueuingResourceByType')) {
            return '';
        }
        $_aParams = func_get_args() + array( '', '', '', array() );
        return $this->oResource->_addEnqueuingResourceByType($_aParams[ 0 ], array( 'sPageSlug' => $_aParams[ 1 ], 'sTabSlug' => $_aParams[ 2 ], ) + $_aParams[ 3 ], 'script');
    }
}
