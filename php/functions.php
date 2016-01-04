<?php
//设置调试输出
ini_set ('xdebug.var_display_max_children', 512);
ini_set ('xdebug.var_display_max_data', 512);
ini_set ('xdebug.var_display_max_depth', 50);

//注册自动加载函数
spl_autoload_register('autoload');

//设置schema匹配一致性系数
define('MATCH_W1',0.5);     //名称相似度系数
define('MATCH_W2',0.2);     //属性相似度系数
define('MATCH_W3',0.3);     //儿子相似度系数
define('MATCH_W4',0.001);   //路径相似度系数

//类自动加载
function autoload($class) {
	static $map = array();
	if (!isset($map[$class])) {
        require dirname(__FILE__).'/'.$class.'.class.php';
        $map[$class] = $class;
    }
}

//从某节点开始向下遍历遍历XML文档所有节点的方法
function walk_xml($node, $depth, $arr) {
    global $global_var;
	for ($i = 0, $indent = ''; $i < $depth; $i++)
		$indent .= '   ';
	if(!empty($node->tagName)) {//非空格换行或非纯文本的有效标签
		$arr->tagName = $node->tagName;
		$arr->depth = $depth;
        $temp = $arr;
        //设置上辈节点的子孙数+1
        while($temp->parent != NULL) {
            $parent_node = $temp->parent;
            $parent_node->children_total_count ++;
            $temp = $temp->parent;
        }
        $arr->xmlTagName = $node->getAttribute("name");//设置对应xml的标签名
        $arr->xmlTagType = $node->getAttribute("type");//设置对应xml的标签属性
	}

	$children = $node->childNodes;
	if(isset($children->length)){
		$children_count = $children->length;
		if($children_count > 0) {
			$depth++;
			for ($i=0; $i < $children_count; $i++) { 
				if(!empty($children->item($i)->tagName)){
					$child = new StructNode();
                    $child->parent = $arr;
					walk_xml($children->item($i), $depth,$child);
					$arr->children_list[] = $child;
				}
			}
		}
	}
	return $arr;
}

function xml_to_tree($node) {
	$root = new StructNode();//辅助根节点
	$root = walk_xml($node, 0, $root);
    $root = fidder_to_root_node($root);
    //dump($root);
    return $root;
}

//过滤xsd节点，获取对应xml的root节点
function fidder_to_root_node($node) {
    if(!empty($node->xmlTagName)) {
        return $node;
    }else {
        if(count($node->children_list) != 0) {
            foreach ($node->children_list as $value) {
                return fidder_to_root_node($value);
            }
        }
    }
    return null;//找不到xml文件的root节点
}

//用路径匹配算法计算节点相似度
function match($tree1, $tree2) {
    //获取两棵树儿子个数
    $tree1_children_count = count($tree1->children_list);
    $tree2_children_count = count($tree2->children_list);
    if($tree1_children_count == 0 && $tree2_children_count == 0) {//如果两个节点都是叶节点
        $sim = MATCH_W1 * Lmatch($tree1, $tree2) + MATCH_W2 * Pmatch($tree1, $tree2) + MATCH_W3;
    }else if(($tree1_children_count != 0 && $tree2_children_count == 0) || ($tree1_children_count == 0 && $tree2_children_count != 0)) {
        //如果两个节点一个是叶节点，一个不是
        $sim = MATCH_W1 * Lmatch($tree1, $tree2) + MATCH_W2 * Pmatch($tree1, $tree2) + 0;
    }else {
        //两个节点都不是叶节点
        $sim = MATCH_W1 * Lmatch($tree1, $tree2) + MATCH_W2 * Pmatch($tree1, $tree2) + MATCH_W3 * childMatch($tree1, $tree2);
    }
    return $sim;
}

function debug($tree1, $tree2) {
    return childMatch($tree1, $tree2);
}

//计算两个节点名称相似度
function Lmatch($node1, $node2) {
    return $node1->xmlTagName == $node2->xmlTagName ? 1 : 0;
}

//计算两个节点属性相似度
function Pmatch($node1, $node2) {
    return $node1->xmlTagType == $node2->xmlTagType ? 1 : 0;
}

//计算节点下面所有孩子节点的相似度，其中用到match函数，两个函数相互调用,要保证两个节点都有字节点
function childMatch($tree1, $tree2) {
    global $global_var;
    $nMatch = 0;//匹配的路径数
    $max = 0;
    $weight = 0;
    foreach($tree1->children_list as $tree1_child) {
        foreach ($tree2->children_list as $tree2_child ) {
            $global_var++;
            $_weight = match($tree1_child, $tree2_child);//递归调用，将遍历所有儿子节点
            if($_weight > $max) {
                $max = $_weight;
            }
            
        }
        if($max > 0) {
            $pathLength1 = $tree1_child->depth;
            $pathLength2 = $tree2_child->depth;
            $weight = $_weight - MATCH_W4 * abs($pathLength1 - $pathLength2);
            $nMatch ++;
        }
    }
    //递归结束，下面计算匹配结果
    $w1 = $weight / count($tree1->children_list);
    $w2 = $nMatch / (count($tree2->children_list) + count($tree1->children_list));
    $re = ($w1 + $w2) / 2;
    return ($w1 + $w2) / 2;
}

//友好的调试变量输出
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