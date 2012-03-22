<?php
$cwd = getcwd();
chdir(dirname(__FILE__));
$content = `php getauthors.php`;
chdir($cwd);

