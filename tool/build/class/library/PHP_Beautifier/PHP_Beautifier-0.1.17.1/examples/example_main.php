<html>
</html>
<?php

    // Class and Function List    
    // First, the ugly code from the homepage
 for ($i = 0;$i<strlen($str);$i++) { if ($new_line_counter == 0) {if (($i+1) <sizeof($a)) { if ($a[$i+1] == "?" AND $a[$i] == "<") { out($outstr); out("<?php"); $indent++; $new_line_counter++; if (($i+4) <sizeof($a)) { if ($a[$i+2] == "p" AND $a[$i+3] == "h" AND $a[$i+4] == "p") $i = $i+3; } $i++; continue; } else { $outstr.= $a[$i]; continue; } } } }
    // Without Beautify
    // PHP_Beautifier->setBeautify(false);
 for ($i = 0;$i<strlen($str);$i++) {if ($new_line_counter == 0) {if (($i+1) <sizeof($a)) { if ($a[$i+1] == "?" AND $a[$i] == "<") { out($outstr); out("<?php"); $indent++; $new_line_counter++; if (($i+4) <sizeof($a)) { if ($a[$i+2] == "p" AND $a[$i+3] == "h" AND $a[$i+4] == "p") $i = $i+3; } $i++; continue; } else { $outstr.= $a[$i]; continue; } } } }

    // And beautify again!
    // PHP_Beautifier->setBeautify(true);
    // Second, the comments!
    echo $a; // inline
    echo $a; // inline
    /**
* Multiline
    */
    // and the old folding markers!
    function folder() 
    { /*{{{{*/
    }   /*}}}*/
    // The power of callbacks: the next line deactivate the Filter ListClassFunction for functions
    // ListClassFunction->includeInList('functions',false);
    function notInList() 
    {
    }
    // ListClassFunction->includeInList('functions',true);
    function InList() 
    {
    }
    class class_1 extends class_0 {
    }
    // Third, do you remember Pascal?
    if ($a):
        echo 'a';
        echo 'n';
    else:
        echo 'b';
    endif;
    while ($japan):
        echo 'nothing';
    endwhile;
    // Fourth.... The horrible switch inside switch! ... break includes!
    switch ($a) {
        case 'a':
        case 'b':
        echo "something";
        break;

        default:
            foreach($a as $b) {
                if ($x) {
                    break;
                }
            }
            switch ($b) {
                case 'a':
                    echo 'a';
                case 'b':
                    echo 'b';
                break;

                default:
                    echo 'b';
                break;
            }
        break;
    }
    // ArrayNested->off()    
    // Fifth: Nested arrays...
    $aMyArray = array(
        array(
            array(
                array(
                    'el'=>1,
                    'el'=>2
                )
            )
        )
    );
    // =
    $a=5;
    $b="";
    $c=$a.$b;
    $d.=" ".$b;
    // Somebody remember callbacks?
    // ArrayNested->on()
    $aMyArray = array(array(array(array('el'=>1,'el'=>2))));
    // PHP5, dude!
	throw new Exception();
    $o->getArray() -> createNewThing();
    MyFunction :: singleton()->record();
    echo (__FUNCTION__.__LINE__.__CLASS__);
    // Nested ternary operators!!!
    $a = ($b) ? ($c ? $d:$e) : $f;
    // and switch again...
    switch ($b) {
case 'a':
    echo 'a';
case 'b':
    echo 'b';
break;
    }
?>
