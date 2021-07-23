<?php
function test($val) {
echo $val;
}

test( <<<END
<form id="editform" name="editform" method="post" action="$action"
enctype="multipart/form-data">
END
);
