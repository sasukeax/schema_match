<?php
require dirname(__FILE__).'/functions.php';

//设置xsd文件夹
$file_dir = dirname(dirname(__FILE__)).'/xsd/';

$file_name1 = $file_dir.'address.xsd';
$file_name2 = $file_dir.'order.xsd';

// 首先要建一个DOMDocument对象 
$doc1 = new DOMDocument();
$doc2 = new DOMDocument();

$doc1->load($file_name1);
$doc2->load($file_name2);

$arr1 = xml_to_tree($doc1);
$arr2 = xml_to_tree($doc2);

$result = match($arr1, $arr2);

echo ("相似度为：".$result);

