<?php
if(!defined('IN_T')){
	die('hacking attempt');
}

$act =  Common::sfilter($_REQUEST['act']);

if($act=='add'){
	$re['status'] = 0 ;
	$data =  array(
			'name' => Common::sfilter($_POST['name']),
			'type' => Common::sfilter($_POST['type'])
		);
	if (empty($data['name'])||mb_strlen($data['name'])>10) {
		$re['msg'] = '请输入10个字符内的标签名';
	}else if($data['type']!=1&&$data['type']!=2){
		$re['msg'] = '参数非法';
	}else if($Db->getCount($Base->table('custom_tag'),'id',array('uid'=>$user['pk_user_main'],'name'=>$data['name']))>0){
		$re['msg'] = '该标签已存在';
	}else{
		$data['uid'] = $user['pk_user_main'];
		$id = $Db->insert($Base->table('custom_tag'),$data);
		if ($id>0) {
			$re['tid'] = $id;
			$re['status'] =1;
		}else{
			$re['msg']='添加失败';
		}
	}
	echo $Json->encode($re);
	exit;
}
else if ($act=='delete'){
	$re['status'] = 0;
	$id = intval($_POST['tid']);
	if($Db->getCount($Base->table('custom_tag'),'id',array('uid'=>$user['pk_user_main'],'id'=>$id))==0){
		$re['msg'] = '删除失败';
	}else{
		$Db->delete($Base->table('custom_tag'),array('id'=>$id));
		$Db->delete($Base->table('custom_tag_project'),array('tid'=>$id));
		$re['status'] =1;
	}
	echo $Json->encode($re);
	exit;
}


?>