<?php
require dirname(__FILE__).'/functions.php';

//设置xsd文件夹
$file_dir = dirname(dirname(__FILE__)).'/xsd/';

$file_name = $file_dir.'address.xsd';

// 首先要建一个DOMDocument对象 
$doc = new DOMDocument();

$doc->load($file_name);
$child = $doc->childNodes;

//walk_xml($doc, 0);

$arr = xml_to_tree($doc);

$node = new StructNode();
dump($arr);







