<?php
/**
 * Created by JetBrains PhpStorm.
 * User: till
 * Date: 4/22/12
 * Time: 12:28 AM
 * To change this template use File | Settings | File Templates.
 */
$paths = (array)explode(PATH_SEPARATOR, ini_get('include_path'));
foreach($paths as $path)
{
    if(file_exists("{$path}/vfsStream/vfsStream.php"))
        require_once 'vfsStream/vfsStream.php';
}