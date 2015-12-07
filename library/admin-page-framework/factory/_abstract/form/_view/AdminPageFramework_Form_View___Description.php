<?php
class AdminPageFramework_Form_View___Description extends AdminPageFramework_FrameworkUtility {
    public $aDescriptions = array();
    public $sClassAttribute = 'admin-page-framework-form-element-description';
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aDescriptions, $this->sClassAttribute,);
        $this->aDescriptions = $this->getAsArray($_aParameters[0]);
        $this->sClassAttribute = $_aParameters[1];
    }
    public function get() {
        if (empty($this->aDescriptions)) {
            return '';
        }
        $_aOutput = array();
        foreach ($this->aDescriptions as $_sDescription) {
            $_aOutput[] = "<p class='" . esc_attr($this->sClassAttribute) . "'>" . "<span class='description'>" . $_sDescription . "</span>" . "</p>";
        }
        return implode(PHP_EOL, $_aOutput);
    }
}