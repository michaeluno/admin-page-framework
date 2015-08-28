<?php
abstract class AdminPageFramework_TaxonomyField extends AdminPageFramework_TaxonomyField_Controller {
    static protected $_sFieldsType = 'taxonomy';
    function __construct($asTaxonomySlug, $sOptionKey = '', $sCapability = 'manage_options', $sTextDomain = 'admin-page-framework') {
        if (empty($asTaxonomySlug)) {
            return;
        }
        $this->oProp = new AdminPageFramework_Property_TaxonomyField($this, get_class($this), $sCapability, $sTextDomain, self::$_sFieldsType);
        $this->oProp->aTaxonomySlugs = ( array )$asTaxonomySlug;
        $this->oProp->sOptionKey = $sOptionKey ? $sOptionKey : $this->oProp->sClassName;
        parent::__construct($this->oProp);
        $this->oUtil->addAndDoAction($this, "start_{$this->oProp->sClassName}");
    }
}