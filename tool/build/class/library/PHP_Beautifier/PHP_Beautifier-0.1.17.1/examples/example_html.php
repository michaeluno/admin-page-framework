<table a = "b">
<?php while ($aRow=fetchRow($x)) { ?> <tr> <? foreach($aRow                    as $sId=>$sName) {?> <td><b><?php echo $sId ?></b>:<?php echo $sName ?></b></td>
<?php } ?></tr><?php } ?></table>
<?php if (isset($_POST["text"])) { echo "FOTZEEE"; } else { ?>
<form method="post" action="<? echo $_SERVER["PHP_SELF"]; ?>">
<input type="text" name="text" />
<input type="submit" />
</form>
<? } ?>