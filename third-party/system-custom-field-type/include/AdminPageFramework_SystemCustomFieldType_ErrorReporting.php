<?php

class AdminPageFramework_SystemCustomFieldType_ErrorReporting {
    protected $levels = array(
            1 => 'E_ERROR',
            2 => 'E_WARNING',
            4 => 'E_PARSE',
            8 => 'E_NOTICE',
            16 => 'E_CORE_ERROR',
            32 => 'E_CORE_WARNING',
            64 => 'E_COMPILE_ERROR',
            128 => 'E_COMPILE_WARNING',
            256 => 'E_USER_ERROR',
            512 => 'E_USER_WARNING',
            1024 => 'E_USER_NOTICE',
            2048 => 'E_STRICT',
            4096 => 'E_RECOVERABLE_ERROR',
            8192 => 'E_DEPRECATED',
            16384 => 'E_USER_DEPRECATED'
    );

    protected $level;

    public function __construct( $level='' ) {
            $this->level = $level;
    }

    public function getErrorLevel()
    {
            $included = $this->_getIncluded();

            $errorLevel = $this->_getErrorDescription($included);

            return $errorLevel;
    }

    public function _getIncluded()
    {
        $included = array();

        foreach( $this->levels as $levelInt => $levelText ) {
            // This is where we check if a level was used or not
            if( $this->level & $levelInt ) {
                $included[] = $levelInt;
            }
        }

        return $included;
    }

    protected function _getErrorDescription($included)
    {
            $description = '';

            $all = count($this->levels);

            $values = array();
            if(count($included) > $all / 2)
            {
                    $values[] = 'E_ALL';

                    foreach($this->levels as $levelInt => $levelText)
                    {
                            if(!in_array($levelInt, $included))
                            {
                                    $values[] = $levelText;
                            }
                    }
                    $description = implode(' & ~', $values);
            }
            else
            {
                    foreach($included as $levelInt)
                    {
                            $values[] = $this->levels[$levelInt];
                    }
                    $description = implode(' | ', $values);
            }

            return $description;
    }
}