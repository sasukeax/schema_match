<?php
//设置调试输出
ini_set ('xdebug.var_display_max_children', 128);
ini_set ('xdebug.var_display_max_data', 512);
ini_set ('xdebug.var_display_max_depth', 20);

//注册自动加载函数
spl_autoload_register('autoload');

function autoload($class) {
	static $map = array();
	if (!isset($map[$class])) {
        require dirname(__FILE__).'/'.$class.'.class.php';
        $map[$class] = $class;
    }
}

//从某节点开始向下遍历遍历XML文档所有节点的方法
function walk_xml($node, $depth, $arr) {
	for ($i = 0, $indent = ''; $i < $depth; $i++)
		$indent .= '   ';
	if(!empty($node->tagName)) {
		//print ($indent . $node->tagName . "  depth:".$depth. "\n");
		$arr->tagName = $node->tagName;
		$arr->depth = $depth;
	}
	$children = $node->childNodes;
	if(isset($children->length)){
		$children_count = $children->length;
		if($children_count > 0) {
			$depth++;
			//print ($indent . $node->tagName . "  depth:".$depth. "\n");
			for ($i=0; $i < $children_count; $i++) { 
				if(!empty($children->item($i)->tagName)){
					$child = new StructNode();
					walk_xml($children->item($i), $depth,$child);
					//$child->parent = $arr;
					$arr->addChild($child);
				}
			}
		}
	}
	return $arr;
}

function xml_to_tree($node) {
	$root = new StructNode();//辅助根节点
	$root = walk_xml($node, 0, $root);
	return $root->children_list[0];
}

function dump($var, $echo = true, $label = null, $strict = true)
{
    $label = (null === $label) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo ($output);
        return null;
    } else {
        return $output;
    }

}