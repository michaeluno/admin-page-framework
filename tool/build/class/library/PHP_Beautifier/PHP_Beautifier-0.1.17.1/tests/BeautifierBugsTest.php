<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Claudio Bustos <cdx@users.sourceforge.net>                  |
// |          Jens Bierkandt <schtorch@users.sourceforge.net>             |
// +----------------------------------------------------------------------+
//
// $Id:
require_once('Helpers.php');


class BeautifierBugsTest extends PHPUnit_Framework_TestCase
{
    
    function setUp() 
    {
        error_reporting (E_ALL & ~(E_DEPRECATED | E_STRICT));
        $this->oBeaut = new PHP_Beautifier();
    }
    function setText($sText) 
    {
        $this->oBeaut->setInputString($sText);
        $this->oBeaut->process();
    }
    /**
     * HeredocBeforeCloseTag
     * Close tag after heredoc remove whitespace,
     * breaking the script.
     *
     */
    function testBugInternal1() 
    {
        $sText = <<<SCRIPT
<?php
\$a = <<<HEREDOC
sdsdsds
HEREDOC;
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
\$a = <<<HEREDOC
sdsdsds
HEREDOC;

?>
SCRIPT;
//        $sExpected = str_replace(array("\r"), "CR", $sExpected);
//        $sExpected = str_replace(array("\n"), "LF", $sExpected);
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
//        $sTextActual = str_replace(array("\r"), "CR", $sTextActual);
//        $sTextActual = str_replace(array("\n"), "LF", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
     * HEREDOC before parenthesis
     * Close tag after heredoc remove whitespace,
     * breaking the script.
     *
     */
    function testBugHEREDOCparen() 
    {
        $sText = <<<SCRIPT
<?php
\$a = someFunction(<<<HEREDOC
sdsdsds
HEREDOC
);
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
\$a = someFunction(<<<HEREDOC
sdsdsds
HEREDOC
);
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
     * Bug 1597
     * Brace after short comment in new line was appended to
     * the comment, breaking the code
     */
    function testBug1597() 
    {
        $sText = <<<SCRIPT
<?php
if (\$_POST["url"] != "") //inserting data
{
}
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
if (\$_POST["url"] != "") //inserting data
{
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
     * Bug 2301
     * When I try to beautify PHP5 code with the 'throw new Exception'
     * statement, the code is not formatted correctly. The
     * whitespace between throw AND new is deleted.
     */
    function testBug2301() 
    {
        $sText = <<<SCRIPT
<?php
throw new AccountFindException();
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
throw new AccountFindException();
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
     * Bug from Pavel Chtchevaev, 2004-11-17
     * There's one more issue about the default filter, try beautifying with
     * it the following string:
     * "<?php\n\$o->_test1(\$c->test2()->test3())\n?>"
     * It will return:
     * "<?php\n    \$o->_test1(\$c->test2() ->test3())\n?>"
     */
    function testBugChtchevaev_2004_11_17() 
    {
        $sText = <<<SCRIPT
<?php
\$o->_test1(\$c-> test2()-> test3());
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
\$o->_test1(\$c->test2()->test3());
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
     * Bug 3257
     * Comments between if and elseif screws up formatting.
     * The beautifier will cascade and start moving the indentations over
     * if there is a comment between if {} and elseif {}
     */
    function testBug3257() 
    {
        $sText = <<<SCRIPT
<?php
    class Foo {
        var \$foobar = 0;
        function Foo(\$a, \$b) {
            if (\$a) {
                dostuff();
            }
            // \$a no good
            elseif {
                dootherstuff();
            }
            // \$c maybe
            elseif {
                yea();
            }
        }

        function bar() {
            echo "Hello";
        }
    }

?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
class Foo {
    var \$foobar = 0;
    function Foo(\$a, \$b) {
        if (\$a) {
            dostuff();
        }
        // \$a no good
        elseif {
            dootherstuff();
        }
        // \$c maybe
        elseif {
            yea();
        }
    }
    function bar() {
        echo "Hello";
    }
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
     * Bug from Daniel Convissor, 2005-06-20
     * Switch statements aren't coming out right.  I, and most PEAR developers
     * I've asked, partial to them looking like this:
     * @deprecated as bug!
     * <code>
     * switch ($subId) {
     *   case "myevents";
     *       $myeventsOn = "on";
     *       break;
     *   case "publicevents";
     *       $publiceventsOn = "on";
     *       break;
     * }
     * </code>
     */
    function deprecatedtestBugConvissor_2005_06_20() 
    {
        $this->oBeaut->addFilter("Pear");
        $sText = <<<SCRIPT
<?php
switch (\$subId) {
case "myevents":
\$myeventsOn = "on";
break;
case "publicevents":
\$publiceventsOn = "on";
break;
}
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
switch (\$subId) {
    case "myevents":
        \$myeventsOn = "on";
        break;

    case "publicevents":
        \$publiceventsOn = "on";
        break;
}
?>
SCRIPT;
        $this->assertEquals($sExpected, $this->oBeaut->get());
    }
    function testBugJustinh_2005_07_26() 
    {
        $sText = <<<SCRIPT
<?php
switch (\$var) {
case 1:
print "hi";
break;
case 2:
default:
break;
}
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
switch (\$var) {
    case 1:
        print "hi";
    break;
    case 2:
    default:
    break;
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    function testBugjuancarlos2005_09_13() 
    {
        $this->oBeaut->addFilter("ArrayNested");
        $this->oBeaut->addFilter('IndentStyles', array(
            'style' => 'allman'
        ));
        $sText = <<<SCRIPT
<?php include_once ("turnos.conf.php")
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php include_once ("turnos.conf.php")
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    function testBug5711() 
    {
        $this->oBeaut->addFilter("Pear");
        $sText = <<<SCRIPT
<?php

class CampaignManagerConfig {
    
    const BLOCKSIZE_ALL = 9999999;
    
    public static function getStagingUrl(\$liveUrl) {
        return true;
    }
        
}

?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
class CampaignManagerConfig
{
    const BLOCKSIZE_ALL = 9999999;
    public static function getStagingUrl(\$liveUrl)
    {
        return true;
    }
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    function testBug6237() 
    {
        $this->oBeaut->addFilter("Pear");
        $sText = <<<SCRIPT
<?php
\$_SESSION["test\$i"];
\$_SESSION["test_\$i"];
\$_SESSION['test_\$i'];
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
\$_SESSION["test\$i"];
\$_SESSION["test_\$i"];
\$_SESSION['test_\$i'];
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
     * according to: http://pear.php.net/manual/en/standards.control.php
     * control strutures should be indented in K&R style
     *
     * if (<cond>) {
     * <body>
     *   }
     *   however, are getting indented in Allman style
     * all control structures are affected.
     */
    function testBug7347() 
    {
        $this->oBeaut->addFilter("Pear");
        $sText = <<<SCRIPT
<?php
class Foo { 
    public function __construct() {
        if(\$foo && \$bar) { echo "FUBAR"; }
    }
}
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
class Foo
{
    public function __construct()
    {
        if (\$foo && \$bar) {
            echo "FUBAR";
        }
    }
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
     * Bad code to detect tokens
     */
    function testInternal2() 
    {
        $this->assertTrue(array_key_exists(T_COMMENT, $this->oBeaut->aTokenFunctions));
    }
    /**
     * Adding a comment after a case statement in a switch causes
     * the indenting to be wrong.
     */
    function testBug7759() 
    {
        $sText = <<<SCRIPT
<?php
echo 0;
switch(1) {
case 1:
case 5: // 5
echo 1;
break;
case 2: //2
echo "something";
echo "something";
case 3: /*3 */ /* 3? */
case 4: 
default:
echo '2';
break;
}
echo 1;
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
echo 0;
switch (1) {
    case 1:
    case 5: // 5
        echo 1;
    break;
    case 2: //2
        echo "something";
        echo "something";
    case 3: /*3 */ /* 3? */
    case 4:
    default:
        echo '2';
    break;
}
echo 1;
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    function testBug7818() 
    {
        //$this->oBeaut->startLog();
        $sText = <<<SCRIPT
<?php
\$field->createElement(\$form, \$this->_table->{\$field->id}, \$defaults);
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
\$field->createElement(\$form, \$this->_table->{\$field->id}, \$defaults);
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
     * Will be great if you can rewrite T_OPEN_TAG_WITH_ECHO 
     * in the default filter, specially <?= because it will be 
     * removed in PHP6.
     */
    function testBug7854() 
    {
        if(ini_get("short_open_tag")) {
        $this->oBeaut->addFilter("Pear");
        $sText = <<<SCRIPT
<?= \$var ?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php echo \$var ?>
SCRIPT;
        $this->assertEquals($sExpected, $this->oBeaut->get());
        } else {
            $this->markTestSkipped(
          'Needs short_open_tag php.ini set.'
        );
        }
    }
    /**
     * the first lines are intended if -l "ListClassFunction()"
     * is enabled
     */
    function testBug7307() 
    {
        // $this->oBeaut->startLog();
        $this->oBeaut->addFilter("ListClassFunction");
        $sText = <<<SCRIPT
<?php
/**
 * Class and Function List:
 * Function list:
 * Classes list:
 */
require_once 'dbobject.class.php';
require_once 'kfp-globals.inc.php';
class test {
    function m1() {}
    function m2() {}
}
function f1() {
}
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
/**
 * Class and Function List:
 * Function list:
 * - m1()
 * - m2()
 * - f1()
 * Classes list:
 * - test
 */
require_once 'dbobject.class.php';
require_once 'kfp-globals.inc.php';
class test {
    function m1() {
    }
    function m2() {
    }
}
function f1() {
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
     * When using the "break" command, the command takes an optional parameter, see http://de.php.net/break for details. But this doesn't work when using the beautifier, because, for example "break 2;" morphs to "break2;" (notice the missing space, which makes the PHP interpreter quite sour :-(
     */
    function testBug_rolfhub_2007_02_07_1() 
    {
        $sText = <<<SCRIPT
<?php
\$i = 0;
while (++\$i) {
    switch (\$i) {
        case 5:
            echo "At 5<br />";
        break 1; /* Exit only the switch. */
        case 10:
            echo "At 10; quitting<br />";
        break 2; /* Exit the switch and the while. */
        default:
        break;
    }
}
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
\$i = 0;
while (++\$i) {
    switch (\$i) {
        case 5:
            echo "At 5<br />";
        break 1; /* Exit only the switch. */
        case 10:
            echo "At 10; quitting<br />";
        break 2; /* Exit the switch and the while. */
        default:
        break;
    }
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
     * When using the "break" command, the command takes an optional parameter, see http://de.php.net/break for details. But this doesn't work when using the beautifier, because, for example "break 2;" morphs to "break2;" (notice the missing space, which makes the PHP interpreter quite sour :-(
     */
    function testBug_rolfhub_2007_02_07_1_pear() 
    {
        $this->oBeaut->addFilter("Pear");
        $sText = <<<SCRIPT
<?php
\$i = 0;
while (++\$i) {
    switch (\$i) {
        case 5:
            echo "At 5<br />";
        break 1; /* Exit only the switch. */
        case 10:
            echo "At 10; quitting<br />";
        break 2; /* Exit the switch and the while. */
        default:
        break;
    }
}
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
\$i = 0;
while (++\$i) {
    switch (\$i) {
    case 5:
        echo "At 5<br />";
        break 1; /* Exit only the switch. */
    case 10:
        echo "At 10; quitting<br />";
        break 2; /* Exit the switch and the while. */
    default:
        break;
    }
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
     * The beautifer removes the whitespaces left and right of the operator, so for example "echo 2 . 1 . 0 . "\n";" becomes "echo 2.1.0."\n";"
     */
    function testBug_rolfhub_2007_02_07_2() 
    {
        $sText = <<<SCRIPT
<?php
echo (1.0 . " " . 2 . 3);
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
echo (1.0 . " " . 2 . 3);
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
     * Description:
     * ------------
     * When using the default filter, a T_ARRAY token used as a Type hint
     * (http://php.net/language.oop5.typehinting) does not get a space
     * after it.
     * Similarly the T_CLONE token also misses whitespace after it.
     */
    function testBug10839() 
    {
        $sText = <<<SCRIPT
<?php
class test
{
 function test(array \$moo)
 {
  return clone \$this;
 }
 public function test(OtherClass \$otherclass) {
        echo \$otherclass->var;
    }

}
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
class test {
    function test(array \$moo) {
        return clone \$this;
    }
    public function test(OtherClass \$otherclass) {
        echo \$otherclass->var;
    }
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
     * When processing the T_DOT in the partial tokenized script, and you use
     * getPreviousWhitespace(), it will go all the back pass the T_ECHO and
     * pick up the T_WHITESPACE prior to the T_ECHO.  It should actually stop
     * at the T_CONSTANT_ENCAPSED_STRING.
     */
    function testBug11661() 
    {
        $sText = <<<SCRIPT
<?php
if (empty(\$user_password) AND empty(\$user_password2)) {
            \$user_password = makepass();

        } elseif (\$user_password != \$user_password2) {

            title(_NEWUSERERROR);

            OpenTable();

            echo '<center><b>'._PASSDIFFERENT.'</b><br /><br />'._GOBACK.'</center>';  

            CloseTable();

            include_once('footer.php');

            die();

        } elseif (\$user_password == \$user_password2 AND
strlen(\$user_password) < \$minpass) {

            title(_NEWUSERERROR);

            OpenTable();

            echo '<center>'._YOUPASSMUSTBE.' <b>'.\$minpass.'</b>' . _CHARLONG . '<br /><br />' . _GOBACK . '</center>';

            CloseTable();

            include_once ('footer.php');

            die();

        }
?>
SCRIPT;

$this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
if (empty(\$user_password) AND empty(\$user_password2)) {
    \$user_password = makepass();
} elseif (\$user_password != \$user_password2) {
    title(_NEWUSERERROR);
    OpenTable();
    echo '<center><b>' . _PASSDIFFERENT . '</b><br /><br />' . _GOBACK . '</center>';
    CloseTable();
    include_once ('footer.php');
    die();
} elseif (\$user_password == \$user_password2 AND strlen(\$user_password) < \$minpass) {
    title(_NEWUSERERROR);
    OpenTable();
    echo '<center>' . _YOUPASSMUSTBE . ' <b>' . \$minpass . '</b>' . _CHARLONG . '<br /><br />' . _GOBACK . '</center>';
    CloseTable();
    include_once ('footer.php');
    die();
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
     * Doesn't works!
     */
    function atestComplexCurlySyntax() 
    {
        try {
            //$this->oBeaut->startLog();
            $sText = '<?php
$great = "fantastic";
echo "This is { $great}";
echo "This is {$great}";
echo "This is ${great}";
echo "This square is {$square->width}00 centimeters broad.";
echo "This works: {$arr[4][3]}";
echo "This is wrong: {$arr[foo][3]}";
echo "This works: {$arr[foo][3]}";
echo "This works: " . $arr["foo"][3];
echo "You can even write {$obj->values[3]->name}";
// echo "This is the value of the var named $name: {${$name}}";
?>';
            $this->setText($sText);
            $sExpected = '<?php
$great = "fantastic";
echo "This is { $great}";
echo "This is {$great}";
echo "This is ${great}";
echo "This square is {$square->width}00 centimeters broad.";
echo "This works: {$arr[4][3]}";
echo "This is wrong: {$arr[foo][3]}";
echo "This works: {$arr[foo][3]}";
echo "This works: " . $arr["foo"][3];
echo "You can even write {$obj->values[3]->name}";
// echo "This is the value of the var named $name: {${$name}}";
?>';
            $sTextActual = $this->oBeaut->get();
            $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
            $this->assertEquals($sExpected, $sTextActual);
        }
        catch(Exception $oExp) {
            $this->assertTrue(false);
        }
    }
    
    /**  	
    *  	Double Ternary Issue
    */
    function testBug11941() 
    {
        //$this->oBeaut->startLog();
        $this->oBeaut->addFilter('Pear');
        $sText = <<<SCRIPT
<?php
\$html_on = ( \$submit || \$refresh ) ? ((!empty(\$HTTP_POST_VARS['disable_html'])) ? 0 : TRUE ):\$userdata['user_allowhtml'];
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
\$html_on = (\$submit || \$refresh) ? ((!empty(\$HTTP_POST_VARS['disable_html'])) ? 0 : TRUE) : \$userdata['user_allowhtml'];
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    
    
    
    
    /**  	
    * Pear filter appends space to function definition line
    */
    function testBug13600() 
    {
        //$this->oBeaut->startLog();
        $this->oBeaut->addFilter('Pear');
        $sText = <<<SCRIPT
<?php
function example(){
}
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
function example()
{
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    

    /**  	
    * Pear filter breaks output - for valid, curly syntax "$this->{$method}();"
    */
    function testBug13602() 
    {
        //$this->oBeaut->startLog();
        $this->oBeaut->addFilter('Pear');
        $sText = <<<SCRIPT
<?php
function example()
{
    \$this->{\$method}();
}
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
function example()
{
    \$this->{\$method}();
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }

    
    
    function testBug13795() 
    {
        $this->oBeaut->addFilter("IndentStyles");
        $sText = <<<SCRIPT
<?php if (true){echo 'a';}else echo 'b'; ?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php if (true) {
    echo 'a';
}
else echo 'b'; ?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    
    function testBug13805() 
    {
        $this->oBeaut->addFilter("Pear");
        $sText = <<<SCRIPT
<?php
switch (\$condition) {
case 1:
action1();
break;
case 2:
action2();
break;
default:
defaultaction();
break;
}
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
switch (\$condition) {
case 1:
    action1();
    break;

case 2:
    action2();
    break;

default:
    defaultaction();
    break;
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    
    
    function atestBug13861() 
    {
        $sText = <<<SCRIPT
<?php
/*
<?php
class test
{
    function test()
    {
        switch (true) {
            default:
        }
}
}
*/
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
/*
<?php
class test
{
    function test()
    {
        switch (true) {
            default:
        }
}
}
*/
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    
    function testBug14175() 
    {
        $sText = <<<SCRIPT
<?php
func( <<<END
<form id="editform" name="editform" method="post" action=""
enctype="multipart/form-data">
END
);
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
func(<<<END
<form id="editform" name="editform" method="post" action=""
enctype="multipart/form-data">
END
);
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
     function testBug14429() 
    {
        $this->oBeaut->addFilter('Pear');
        
        $sText = <<<SCRIPT
<?php
\$var = new StdClass();
\$var->text = 'hello';
\$ok['what'] = 'ok';
switch (\$something){
case 'one':
echo "{\$var->text} world {\$ok['what']}";
break;
default:
break;
}
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
\$var = new StdClass();
\$var->text = 'hello';
\$ok['what'] = 'ok';
switch (\$something) {
case 'one':
    echo "{\$var->text} world {\$ok['what']}";
    break;

default:
    break;
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
function testBug14459() 
    {
        $sText = <<<SCRIPT
<?php
\$bye = "Goodbye";
echo "Curly {Hello}.";
echo "Curly {{\$bye}}.";
echo "Curly {". \$bye ."}.";
?>
SCRIPT;
        $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
\$bye = "Goodbye";
echo "Curly {Hello}.";
echo "Curly {{\$bye}}.";
echo "Curly {" . \$bye . "}.";
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
    * Lowercase filter prepends the control structure with ugly space
    */
    
    function testBug11245() {
        $this->oBeaut->addFilter('Lowercase');        
        $sText = <<<SCRIPT
<?php
IF (\$a OR \$b) { echo 'foo'; } ELSE IF (\$b AND \$c AND \$d) { echo 'bar'; }
?>
SCRIPT;
$this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
if (\$a or \$b) {
    echo 'foo';
} else if (\$b and \$c and \$d) {
    echo 'bar';
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
    * Lowercase filter prepends the control structure with ugly space
    */
    
    function testBug14396() {
        //$this->oBeaut->startLog();
        $this->oBeaut->addFilter('Lowercase');        
        $sText = <<<SCRIPT
<?php
\$a==FALSE;
\$b==TRUE;
?>
SCRIPT;
$this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
\$a == false;
\$b == true;
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    
    /** 
    * Bug 14537 
    * PHP_Beautifier breaks code with namespace and/or use statements
    */
    function testBug14537() { 
        
        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
                
            $sText = <<<SCRIPT
<?php
namespace MyTestnamespace\someSubNS; use OtherNamespace\ClassA; use AnotherNamespace\Class1 as Class2; 
?>
SCRIPT;
            $this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
namespace MyTestnamespace\someSubNS;
use OtherNamespace\ClassA;
use AnotherNamespace\Class1 as Class2;
?>
SCRIPT;
            $sTextActual = $this->oBeaut->get();
            $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
            $this->assertEquals($sExpected, $sTextActual);
        } else {
             $this->markTestSkipped(
              'Needs PHP5.3+');
        }
    }
    
    function testBug14754() {
        $this->oBeaut->startLog();
$sText = <<<SCRIPT
<?php
class Z {
    public function a() {
        echo "hi";
    }
    private function b() {
        echo "hi"; // Comment
    }
    private function c() {
        echo "hi";
    }
}
?>
SCRIPT;
$this->oBeaut->addFilter('BBY');
$this->setText($sText);
$sExpected = <<<SCRIPT
<?php
class Z {
    T_CLASSpublic function a() {
        echo "hi";
    }
    T_CLASSprivate function b() {
        echo "hi"; // Comment
        
    }
    T_CLASSprivate function c() {
        echo "hi";
    }
}
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    /**
    * Pear filter PHP_Beautifier_Filter_Pear with enabled
    * "switch_without_indent" setting, this is enabled by default, 
    * results in invalid code, if the case block contains 
    * closing brackets in strings. 
    */
    
    function testBug17063() {
        //$this->oBeaut->startLog();
        $this->oBeaut->addFilter('Pear');        
        $sText = <<<SCRIPT
function testFunction() {
    \$foo = 'foo'; 
    \$bar = 'bar'; 
    switch (\$foo) {
        case 'foo': 
            return "string with var having braces {\$foo}{\$bar}"; 
        break;
        case 'bar': 
            return \$foo{0} . \$bar{0};
        break;
        default:
            return "\\$\${var}";
    }
}
SCRIPT;
$this->setText($sText);
        $sExpected = <<<SCRIPT
function testFunction() {
    \$foo = 'foo'; 
    \$bar = 'bar'; 
    switch (\$foo) {
        case 'foo': 
            return "string with var having braces {\$foo}{\$bar}"; 
        break;
        case 'bar': 
            return \$foo{0} . \$bar{0};
        break;
        default:
            return "\\$\${var}";
    }
}
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->assertEquals($sExpected, $sTextActual);
    }
    
    
    /**
    * When using PHP_Beautifier on this code, the indenting of the switch
    * does not return to normal after the switch. Other switch code works
    * fine, so maybe it has something to do with the fact that 
    * there is no "break", or that there is an if without brackets or "else". 
    */
    
    function testBug14575() {
        //$this->oBeaut->startLog();
        $sText = <<<SCRIPT
<?php
define("dim", 3);
define("blank", " ");
\$hexdigits = str_repeat(" ",100);
function showno(\$no) {
    global \$hexdigits;
    
    if (\$no == blank) return '_';
    switch (dim) {
        case 3:
        case 4:
            return \$hexdigits[\$no % 16]; // (custom modulo?)
        case 5:
            return \$hexdigits[\$no + 9];
        case 6:
            if (\$no < 11) return \$hexdigits[\$no % 10]; // (custom modulo?)
            if (\$no > 10) return \$hexdigits[\$no - 1 ];
    }
    return "_";
}
echo showno(rand(0, 100));
?>
SCRIPT;
$this->setText($sText);
        $sExpected = <<<SCRIPT
<?php
define("dim", 3);
define("blank", " ");
\$hexdigits = str_repeat(" ", 100);
function showno(\$no) {
    global \$hexdigits;
    if (\$no == blank) return '_';
    switch (dim) {
        case 3:
        case 4:
            return \$hexdigits[\$no % 16]; // (custom modulo?)
            
        case 5:
            return \$hexdigits[\$no + 9];
        case 6:
            if (\$no < 11) return \$hexdigits[\$no % 10]; // (custom modulo?)
            if (\$no > 10) return \$hexdigits[\$no - 1];
    }
    return "_";
}
echo showno(rand(0, 100));
?>
SCRIPT;
        $sTextActual = $this->oBeaut->get();
        $sTextActual = str_replace(PHP_EOL, "\n", $sTextActual);
        $this->markTestSkipped("Not yet finished. http://pear.php.net/bugs/bug.php?id=14575");
        $this->assertEquals($sExpected, $sTextActual);
    }
    
}

