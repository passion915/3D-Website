<?php 

define('IN_T',true);
require_once "../../source/include/init.php";
$act = Common::sfilter($_REQUEST['act']);

if ($act == 'list') {
	$uid = intval($_REQUEST['pk_user_main']);
	$sql = "SELECT view_uuid,position,position_title,position_cover,position_desc,marker_ico FROM u_worksmain";
	if ($uid>0) {
		$sql .= " WHERE pk_user_main = $uid";
	}
	$positions = $Db->query($sql);
	$pid = intval($_REQUEST['pk_works_main']);
	$center = $Db->query("SELECT position FROM u_worksmain WHERE pk_works_main = $pid",'One');
	if (empty($center)) {
		$center = '117.647596,24.513087';
	}
	$re['status'] = 1;
	$re['data'] = $positions;
	$re['center'] = $center;
	echo $Json->encode($re);
	die;
}



 ?>