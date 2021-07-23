<?php
@include_once 'PHPUnit/Autoload.php';
# use pear or local version of php_beautifier

if (file_exists(dirname(__FILE__).'/../Beautifier.php')) {
    include_once dirname(__FILE__).'/../Beautifier.php';
} else {
    include_once "PHP/Beautifier.php";
}

// Mock Filter object
class Test_Filter extends PHP_Beautifier_Filter {
    public $aTokens = array();
    public $aModes = array();
    public $iIndex = 0;
    function handleToken($token) 
    {                
        $this->oBeaut->add($token[1]);
        $this->aTokens[] = $token;
        foreach($this->oBeaut->aModesAvailable as $sMode) {
            $this->aModes[$this->iIndex][$sMode] = $this->oBeaut->getMode($sMode);
        }
        $this->iIndex++;
    }
}

class PHP_Beautifier_Filter_BBY extends PHP_Beautifier_Filter {
    function t_access($sTag) {
        $this->oBeaut->add($this->oBeaut->getTokenName($this->oBeaut->getControlSeq())); return PHP_Beautifier_Filter::BYPASS; 
    } 
} 
