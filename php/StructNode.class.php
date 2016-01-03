<?php
class StructNode {

	//public $type_list = array();
	public $tagName = null;
	public $depth = 0;
	public $parent= null;
	public $children_list = array();

	public function __construct($tagName = null, $depth = 0) {
		$this->tagName = $tagName;
		$this->depth = $depth;
	}

	//获取子节点
	public function getChildrenList() {
		return $this->children_list;
	}

	//添加孩子节点
	public function addChild($child) {
		$this->children_list[] = $child;
	}

	//获取节点的深度
	public function getDepth() {
		return $this->depth;
	}

	public function setParent($parent) {
		$this->parent = $parent;
	}

	public function getParent() {
		return $this->parent;
	}

}