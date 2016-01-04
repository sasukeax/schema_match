<?php
require dirname(__FILE__).'/functions.php';

//设置xsd文件夹
$file_dir = dirname(dirname(__FILE__)).'/xsd/';

$file_name = $file_dir.'address.xsd';

// 首先要建一个DOMDocument对象 
$doc = new DOMDocument();

$doc->load($file_name);

//walk_xml($doc, 0);

$doc2 = new DOMDocument();

$doc2->load($file_dir.'order.xsd');

$global_var = 0;

$arr1 = xml_to_tree($doc);

$arr2 = xml_to_tree($doc2);

//dump($arr1);

$result = match($arr1, $arr2);


dump("global_var:".$global_var);
$debug = debug($arr1, $arr2);
echo '$debug='.$debug;

dump("相似度为：".$result);

