<?php
//全景摄影
if(!defined('IN_T')){
	die('hacking attempt');
}

$act = Common::sfilter($_REQUEST['act']);

//系统通知
if($act == 'system'){
	$size = 20;
	$allNum = $Db->getCount($Base->table('system_notice'),'id',array('userid'=>$user['pk_user_main']));

	$pageIndex = intval($_REQUEST['pageIndex']);
	$pageIndex = $pageIndex<=0?1:$pageIndex;
	$tp->assign('notices',$Db->query('SELECT * FROM '.$Base->table('system_notice').' WHERE userid ='.$user['pk_user_main'].' ORDER BY id DESC LIMIT '.(($pageIndex-1)*$size).' , '.$size));
	$pages = Common::set_page($size,$pageIndex,$allNum);
	foreach ($pages as $key => $value) {
		$pages[$key]['url'] = "/member/notice?act=system&pageIndex=".$value['num'];
	}
	$tp->assign('pages',$pages);
	$tp->assign('allNum',$allNum );
	$tp->assign('allPages',ceil($allNum/$size));

}
//删除系统通知
else if($act=='sys_notice_del'){
	$ids = $_REQUEST['ids'];
	foreach ($ids as $id) {
		$id = intval($id);
		if($Db->getCount($Base->table('system_notice'),'id',array('userid'=>$user['pk_user_main'],'id'=>$id))==1){
			$Db->delete($Base->table('system_notice'),array('id'=>$id));
		}
	}
	$re['status']=1;
	echo $Json->encode($re);
	exit;
}
//点赞通知
else if ($act == 'praise') {
	$size = 20;
	$allNum = $Db->getCount($Base->table('praise_notice'),'id',array('ownerid'=>$user['pk_user_main']));

	$pageIndex = intval($_REQUEST['pageIndex']);
	$pageIndex = $pageIndex<=0?1:$pageIndex;
	$tp->assign('notices',$Db->query('SELECT * FROM '.$Base->table('praise_notice').' WHERE ownerid ='.$user['pk_user_main'].' ORDER BY id DESC LIMIT '.(($pageIndex-1)*$size).' , '.$size));
	$pages = Common::set_page($size,$pageIndex,$allNum);
	foreach ($pages as $key => $value) {
		$pages[$key]['url'] = "/member/notice?act=praise&pageIndex=".$value['num'];
	}
	$tp->assign('pages',$pages);
	$tp->assign('allNum',$allNum );
	$tp->assign('allPages',ceil($allNum/$size));
}


$tp->assign('act',$act);

?>