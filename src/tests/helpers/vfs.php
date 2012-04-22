<?php
$paths = (array)explode(PATH_SEPARATOR, ini_get('include_path'));
foreach($paths as $path)
{
    if(file_exists("{$path}/vfsStream/vfsStream.php"))
        require_once 'vfsStream/vfsStream.php';
}