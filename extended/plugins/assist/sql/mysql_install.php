<?php
// 2011/04/08 tsuchitani AT ivywe DOT co DOT jp
// Last Update 20110408

$pro=$_CONF['path'] . 'plugins/assist/proversion/';
if (file_exists($pro)) {
	include_once($pro.'sql/mysql_install_xml.php');
}

?>
