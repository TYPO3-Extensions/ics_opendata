<?php
$cwd = getcwd();
chdir(dirname(__FILE__));
$content = `php getplatforms.php`;
chdir($cwd);

