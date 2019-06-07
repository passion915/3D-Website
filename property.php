<?php
/**
 * 项目属性
 * @author wh 
*/
define('IN_T',true);
require './source/include/init.php';
require_once './source/include/cls_property.php';

$act = Common::sfilter($_REQUEST['act']);

if($act == 'list'){
	$id = intval($_REQUEST['id']);
	$id = $id<0?0:$id;
	$properties = Property::listByPid($id);
	echo $Json->encode($properties);
	die;
}



?>