<?php
/*
CREATE TABLE IF NOT EXISTS `arbol` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default NULL,
  `nombre` varchar(50) character set utf8 collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;
*/

$link = mysql_connect('localhost', 'demo', 'demo');
if (!$link) {
   die('Not connected : ' . mysql_error());
}
// make foo the current db
$db_selected = mysql_select_db('test', $link);
if (!$db_selected) {
   die ('Can\'t use test: ' . mysql_error());
}