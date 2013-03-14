<?php
$less = new lessc;
echo $less->compileFile(".block { padding: 3 + 4px }");
