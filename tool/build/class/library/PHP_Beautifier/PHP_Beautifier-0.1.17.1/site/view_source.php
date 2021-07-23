<?php
if (!empty($_GET['source']) and array_pop(explode('.', basename($_GET['source'])))=='phps') {
    highlight_file($_GET['source']);
}
?>
