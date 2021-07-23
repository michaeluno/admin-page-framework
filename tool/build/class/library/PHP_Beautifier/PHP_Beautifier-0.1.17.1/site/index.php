<html>
<head>
<title>PHP_Beautifier</title>
	<style type="text/css" media="screen">
		@import url(style.css );
	</style>

</head>
<style>
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
<body>
<h1 id="header">PHP_Beautifier</h1>
<h2>Purpose</h2>
<p>This program reformat and beautify PHP source code files automatically. The program is Open Source and distributed under the terms of PHP Licence. It is written in PHP 5 and has a command line tool.</p>
<p>If you need a PHP 4 tool to beautify files, use <a href='http://www.bierkandt.org/beautify/'>Beautify PHP</a>, made by Jens Bierkandt</p>
<h2>Team</h2>
<ul>
<li>Claudio Bustos: Lead Developer</li>
<li>Jes&uacute;s Espino: Developer</li>
</ul>
<h2>Pear</h2>
<ul>
<li>Main page:<a href='http://pear.php.net/package/PHP_Beautifier'>http://pear.php.net/package/PHP_Beautifier</a></li>
<li>Download: <a href='http://pear.php.net/package/PHP_Beautifier/download'>http://pear.php.net/package/PHP_Beautifier/download</a></li>
<li>Bugs: <a href='http://pear.php.net/package/PHP_Beautifier/bugs'>http://pear.php.net/package/PHP_Beautifier/bugs</a>
</li>
</ul>
<!--<h2>Demostration</h2>
<p>Test the code on <a href='demo/'>demo page</a></p>
-->
<h2>Related</h2>
<ul><li>Project page:<a href='http://sourceforge.net/projects/beautifyphp'>http://sourceforge.net/projects/beautifyphp</a></li>
<li>News:<a href='http://sourceforge.net/news/?group_id=65412'>http://sourceforge.net/news/?group_id=65412</a></li>
<li>Source code (GIT): <a href='http://github.com/clbustos/PHP_Beautifier'>http://github.com/clbustos/PHP_Beautifier</a>
</li>
<li>Forum: <a href='https://sourceforge.net/projects/beautifyphp/forums'>https://sourceforge.net/projects/beautifyphp/forums</a></li>
</ul>
<ul>
<?php
function getUrl($sFile) 
    {
        return 'http://'.$_SERVER['SERVER_NAME']."/".$sFile;
    }

    function recurse($it) 
    {
        echo '<ul>';
        for (;$it->valid();$it->next()) {
            if ($it->isDir() && !$it->isDot()) {
                printf('<li class="dir">%s</li>', $it->current());
                if ($it->hasChildren()) {
                    $bleh = $it->getChildren();
                    echo '<ul>'.recurse($bleh) .'</ul>';
                }
            } elseif ($it->isFile() and stripos($it->getFileName() , ".phps")) {
                echo '<li class="file"><a href="'.getUrl($it->getPath() ."/".$it->getFileName()) .'">'.$it->current() .'</a> ('.$it->getSize() .' Bytes)</li>';
            }
        }
        echo '</ul>';
    }
    // recurse(new RecursiveDirectoryIterator('phps/'));
?>
</ul>
<h2>Documentation</h2>
<h3>API</h3>
<p><a href='docs/'>Documentation made by PhpDocumentor</a></p>
<h2>Bugs Report</h2>
<p><a href='http://pear.php.net/package/PHP_Beautifier/bugs'>http://pear.php.net/package/PHP_Beautifier/bugs</a></p>
<h2>Example</h2>
<p>A script that beautify itself: <a href='example.phps'>This file</a> generate this <a href='example_output.phps'>output</a></p>
<h2>Banners</h2>
<p>If you use PHP_Beautifier on your site, use one of the following banners</p>
<p><img src='button-php_beautifier.png' /> 
<br />
<code>
&lt;a href='http://beautifyphp.sourceforge.net/'&gt;&lt;img src='http://beautifyphp.sourceforge.net/button-php_beautifier.png' /&gt;&lt;/a&gt;
</code>
</p>
<hr />

<a href="http://www.jedit.org">
<img src="http://www.jedit.org/made-with-jedit-4.png"
alt="Developed with jEdit"
border="0" width="96" height="36">
</a>
</p>
<p>
<a href="http://sourceforge.net"><img src="http://sflogo.sourceforge.net/sflogo.php?group_id=65412&amp;type=2" width="125" height="37" border="0" alt="SourceForge.net Logo" /></a>
</p>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-5764936-7");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>
