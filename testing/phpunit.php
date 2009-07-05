#! /usr/bin/php -d safe_mode=Off
<?php
require_once dirname(__FILE__) . '/../src/cligit.php';
#define('CLIGIT_SRC', dirname(__FILE__) . '/../src');
// require_once dirname(__FILE__) . '/suite.php';
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

require 'PHPUnit/TextUI/Command.php';
?>
