<?php

    set_magic_quotes_runtime(0);
    if (get_magic_quotes_gpc()) {
        array_stripslashes($_POST);
        array_stripslashes($_GET);
        array_stripslashes($_COOKIES);
    }
    function array_stripslashes(&$array) 
    {
        if (!is_array($array)) return false;
        while (list($key) = each($array)) {
            if (is_array($array[$key])) {
                stripslashes_array($array[$key]);
            } else {
                $array[$key] = stripslashes($array[$key]);
            }
        }
    }
?>
<HTML>
<HEAD>
<TITLE>Demo for PHP_Beautifier</TITLE>
	<style type="text/css" media="screen">
		@import url( http://apsique.virtuabyte.cl/php/wp-layout.css );
pre {
	padding:5px;
	border-bottom: double 1px #9166CC;
	border-left: solid 1px #9166CC;
	border-right: solid 1px #63468c;
	border-top: solid 1px #63468c;
	background: #C4ACE6;
	width:1%;
	color:black;
}
</style>    
</HEAD>
<BODY>
<h1 id='header'>Demo for PHP_Beautifier</h1>
<FORM action="" method="POST">
Enter your code:
<TEXTAREA name='text' rows='10' style='width:100%'><?php
    if (isset($_POST['text'])) echo htmlentities($_POST['text'])
?></TEXTAREA>
<input type='submit' value='Beautify!' />
<?php
    if (isset($_POST['text'])) {
        include ('PHP/Beautifier.php');
        $oBeautify = new PHP_Beautifier();
        $oBeautify->setInputString($_POST['text']);
        $oBeautify->process();
?>
<h2>Beautified text</h2>
<code>
<?php
    highlight_string(preg_replace("/\r\n|\n|\r/", "\n", $oBeautify->get()));
?>
</code>
    <?php
    if ($db = sqlite_open('../contadores', 0666, $sqliteerror)) {
        //sqlite_query($db, 'CREATE TABLE contadores (pagina varchar(255),numero int(11))');
        $result = sqlite_query($db, "select * from contadores where pagina='".$_SERVER['PHP_SELF']."'");
        if (!($aRow = sqlite_fetch_array($result))) {
            sqlite_query($db, "INSERT INTO contadores (pagina,numero) VALUES ('".$_SERVER['PHP_SELF']."',1)");
        } else {
            sqlite_query($db, "UPDATE  contadores SET numero=numero+1 where pagina='".$_SERVER['PHP_SELF']."'");
            $iNumero = $aRow['numero'];
        }
    } else {
        die($sqliteerror);
    }
?><p>Beautied <?php
    echo $iNumero;
?> scripts</p><?php
    }
?>
</FORM>
</BODY>
</HTML>