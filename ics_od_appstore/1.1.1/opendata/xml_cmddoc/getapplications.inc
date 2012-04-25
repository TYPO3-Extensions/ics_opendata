<?php
$cwd = getcwd();
chdir(dirname(__FILE__));
$content = `php getapplications.php`;
chdir($cwd);

