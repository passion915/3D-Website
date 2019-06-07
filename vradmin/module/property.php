<?php
//会员管理
if(!defined('IN_T')){
   die('hacking attempt');
}
$act = Common::sfilter($_REQUEST['act']);
if($act=='profile'){
	$id = intval($_REQUEST['id']);
	if ($id>0) {
		$tp->assign("row",$Db->query("SELECT * FROM ".$Base->table('property')." WHERE id = $id","Row"));
	}
	$tp->assign('pid',$_REQUEST['pid']);
}
else if($act == 'doedit'){
	$re['status']=0;
	$name = Common::sfilter($_REQUEST['name']);
	$sort = intval($_REQUEST['sort']);
	$pid = intval($_REQUEST['pid']);
	if (empty($name)||mb_strlen($name)>10) {
		$re['msg'] = "请输入1到10个字符长度";
	}else if($sort>99||$sort<0){
		$re['msg'] = "请输入0到99之间的排序";
	}else{
		$params['name'] = $name;
		$params['sort'] = $sort;
		$id = intval($_REQUEST['id']);
		if ($id>0) {
			$Db->update($Base->table('property'),$params,array('id'=>$id));
			$re['msg'] = '编辑成功';
		}else{
			$params['pid'] = $pid;
			$Db->insert($Base->table('property'),$params);
			$re['msg'] = '添加成功';
		}
		$re['status'] = 1;
		$re['href'] ='/'.ADMIN_PATH.'/?m=property&pid='.$pid;
	}
	echo $Json->encode($re);
	exit;
}
else if($act == 'delete'){
	$re['status']=0;
	$id = intval($_REQUEST['id']);
	$property = $Db->query('SELECT * FROM '.$Base->table('property')." WHERE id =  $id","Row");
	if (empty($property)) {
		$re['msg'] = '查询不到相应的属性';
	}
	if($Db->getCount($Base->table('property'),'id',array('pid'=>$id))>0){
		$re['msg'] = '请先删除子属性';
	}else{
		$Db->delete($Base->table('property'),array('id'=>$id));
		$re['status'] = 1;
	}

	echo $Json->encode($re);
	exit;	
}
else{
	require_once ROOT_PATH.'source/include/cls_property.php';
	$pid = intval($_REQUEST['pid']);
	$tp->assign('properties',Property::listByPid($pid));
	$tp->assign('pid',$pid);
}
$tp->assign('nav','区域管理');
$tp->assign('act',$act);

?>