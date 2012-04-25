<?php
$cwd = getcwd();
chdir(dirname(__FILE__));
$content = `php searchapplications.php`;
chdir($cwd);

