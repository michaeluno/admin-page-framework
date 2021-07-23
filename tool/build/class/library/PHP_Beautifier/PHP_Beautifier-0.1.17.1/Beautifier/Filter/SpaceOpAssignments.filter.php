<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * Add space before operator+assignments
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   PHP
 * @package    PHP_Beautifier
 * @subpackage Filter
 * @author     Elod Csirmaz <https://github.com/csirmaz>
 * @copyright  2014 Elod Csirmaz
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0
 * @link       http://pear.php.net/package/PHP_Beautifier
 * @link       http://beautifyphp.sourceforge.net
 */
/**
 * Add space before operator+assignments
 *
 * @category   PHP
 * @package    PHP_Beautifier
 * @subpackage Filter
 * @author     Elod Csirmaz <https://github.com/csirmaz>
 * @copyright  2014 Elod Csirmaz
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0
 * @link       http://pear.php.net/package/PHP_Beautifier
 * @link       http://beautifyphp.sourceforge.net
 */
class PHP_Beautifier_Filter_SpaceOpAssignments extends PHP_Beautifier_Filter
{
    protected $sDescription = 'Add space before operator+assignments';
    /**
     * t_assigment_pre
     *
     * @param mixed $sTag The tag to be processed
     *
     * @access public
     * @return void
     */
    function t_assigment_pre($sTag)
    {
        $this->oBeaut->removeWhitespace();
        $this->oBeaut->add(' ' . $sTag . ' ');
    }
}
?>
