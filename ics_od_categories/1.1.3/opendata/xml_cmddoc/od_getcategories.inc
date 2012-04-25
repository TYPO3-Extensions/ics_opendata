<?php
$cwd = getcwd();
chdir(dirname(__FILE__));
$content = `php od_getcategories.php`;
chdir($cwd);

