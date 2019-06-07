<?php 
//商品分类
if(!defined('IN_T')){
   die('hacking attempt');
}
$act = Common::sfilter($_REQUEST['act']);
if ($act == 'delete') {
	$pid = intval($_REQUEST['pid']);
	$Db->update("u_worksmain",array('position'=>''),array('pk_works_main'=>$pid));
	echo $Json->encode(array('status'=>1));
	die;
}else if($act=='list'){
	$uid = intval($_REQUEST['uid']);
	$pid = intval($_REQUEST['pid']);

	$sql = "SELECT pk_works_main,view_uuid,position,position_title,position_cover,position_desc,marker_ico FROM u_worksmain WHERE 1 = 1 ";
	if ($uid > 0) {
		$sql .= " AND pk_user_main = $uid";
	}
	if ($pid > 0) {
		$sql .= " AND pk_works_main = $pid";
	}
	$positions = $Db->query($sql);

	$pid = intval($_REQUEST['pk_works_main']);
	$center = '108.501635,34.914848';

	$re['status'] = 1;
	$re['data'] = $positions;
	$re['center'] = $center;
	echo $Json->encode($re);
	die;
}


 ?>