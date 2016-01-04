<?php
class StructNode {
	
	public $tagName = "";	//标签名称
	public $depth = 0;		//树高
	public $parent= null;	//父亲节点
	public $xmlTagName = "";//定义的对应xml文件的标签名 如： <city></city> 则存'city'
	public $xmlTagType = "";//定义的对应xml文件的标签的属性
	public $children_total_count = 0;//子孙个数，不包括自身
	public $children_list = array();//儿子节点
}