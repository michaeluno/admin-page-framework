<?php
/*
 * Admin Page Framework v3.9.1b02 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Utility_Path extends AdminPageFramework_Utility_ArraySetter {
    public static function getRelativePath($from, $to)
    {
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to = is_dir($to) ? rtrim($to, '\/') . '/' : $to;
        $from = str_replace('\\', '/', $from);
        $to = str_replace('\\', '/', $to);
        $from = explode('/', $from);
        $to = explode('/', $to);
        $relPath = $to;
        foreach ($from as $depth => $dir) {
            if ($dir === $to[ $depth ]) {
                array_shift($relPath);
            } else {
                $remaining = count($from) - $depth;
                if ($remaining > 1) {
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    $relPath[ 0 ] = './' . $relPath[ 0 ];
                }
            }
        }
        return implode('/', $relPath);
    }
    public static function getCallerScriptPath($sRedirectedFilePath)
    {
        $_aRedirectedFilePaths = array( $sRedirectedFilePath, __FILE__ );
        $_sCallerFilePath = '';
        $_aBackTrace = call_user_func_array('debug_backtrace', self::_getDebugBacktraceArguments());
        foreach ($_aBackTrace as $_aDebugInfo) {
            $_aDebugInfo = self::getAsArray($_aDebugInfo) + array( 'file' => '' );
            $_sCallerFilePath = $_aDebugInfo[ 'file' ];
            if (in_array($_sCallerFilePath, $_aRedirectedFilePaths)) {
                continue;
            }
            break;
        }
        return $_sCallerFilePath;
    }
    private static function _getDebugBacktraceArguments()
    {
        $_aArguments = array( defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? DEBUG_BACKTRACE_IGNORE_ARGS : false, 6, );
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            unset($_aArguments[ 1 ]);
        }
        return $_aArguments;
    }
}
