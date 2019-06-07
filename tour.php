<?php

define('IN_T',true);
require_once "./source/include/init.php";
$act = Common::sfilter($_REQUEST['act']);
$input = $Json->decode(file_get_contents("php://input"));
$view_uuid = Common::sfilter($_REQUEST['view_uuid']);

if (!empty($input)) {
	$act = $input['act'];
	$view_uuid = Common::sfilter($input['view_uuid']);
}
//jssdk Ajax
if($act == 'jssdk'){
	require_once './source/include/cls_weixin_js.php';
	$cur_url = $_REQUEST['currentUrl'];

	$appid = $_lang['wx_config']['appid'];
	$appSecret = $_lang['wx_config']['appsecret'];
	$jssdk = new JSSDK($appid, $appSecret);
	$data = $jssdk->getSignPackage($cur_url);
	header('Content-Type: application/json;charset=utf-8');
	echo json_encode($data);
	exit;
}
//初始化全景项目
else if ($act == 'initPano') {
	//js 获取配置的json
	$pro = $Db->query("SELECT w.* , p.* FROM ".$Base->table('worksmain')." w LEFT JOIN ".$Base->table('pano_config')." p ON w.pk_works_main = p.pk_works_main WHERE w.view_uuid = '$view_uuid' AND flag_publish = 1","Row");
	if (empty($pro)) {
		die("未查询到相关项目");
	}
	$pro = Transaction::decode_str2arr($pro);
	$hotspots = &$pro['hotspot'];
	foreach ($hotspots as &$v) {
		$imgtext = &$v['imgtext'];
		if (!empty($imgtext)) {
			foreach ($imgtext as  &$v2) {
				if ($v2['imgtext_wordContent']) {
					$v2['imgtext_wordContent'] = base64_decode($v2['imgtext_wordContent']);
				}else if ($v2['wordContent']){
					$v2['imgtext_wordContent'] = base64_decode($v2['wordContent']);
					unset($v2['wordContent']);
				}
			}
		}
	}
	echo $Json->encode($pro);
	exit;
}
//点赞
else if($act == "add_praise"){
	$works = $Db->query('SELECT pk_user_main,pk_works_main,name,thumb_path FROM '.$Base->table('worksmain').' WHERE view_uuid="'.$view_uuid.'"' ,'Row','');
	if (!empty($works)) {
		$Db->execSql("UPDATE ".$Base->table('worksmain')." SET praised_num = praised_num+1 WHERE view_uuid = '$view_uuid'");
		$arr = array(
			'username'=>$user['nickname'],
			'avatar'=>$user['avatar'],
			'userid'=>$user['pk_user_main'],
			'ownerid'=>$works['pk_user_main'],
			'workid'=>$works['pk_works_main'],
			'workname'=>$works['name'],
			'work_thumb'=>$works['thumb_path'],
			'create_time'=>date('Y-m-d H:i:s',Common::gmtime())
		);
		$Db->insert($Base->table('praise_notice'),$arr);
	}
	
	exit;
}
//校验密码
else if($act=="privacyAccess") {
	$re['status'] = 0;
	$pid = intval($_POST['pid']);
	$pwd =  Common::sfilter($_POST['pwd']);
	$pro = $Db->query('SELECT privacy_password , view_uuid FROM '.$Base->table('worksmain').' WHERE pk_works_main = '.$pid,'Row');

	if (empty($pro)||$pwd!=$pro['privacy_password']) {
		$re['msg'] ="密码有误";
	}else{
		$_SESSION['privacyAccess'][$pro['view_uuid']] = 1;
		$re['status'] = 1;
		$re['url'] = '/tour/'.$pro['view_uuid'];
	}
	echo $Json->encode($re);
	exit;
}
else if($act == 'map'){
	// $tag = intval($_REQUEST['tag']) ;
	// $tag = $tag==0?24:$tag;
	$worksmain = $Db->query("SELECT w.*,u.nickname FROM ".$Base->table('worksmain')." w LEFT JOIN ".$Base->table('user')." u ON u.pk_user_main = w.pk_user_main WHERE u.pk_user_main = ".$_lang['map_uid']."  ORDER BY w.user_sort ASC LIMIT 1","Row");
	// $worksmain ;
	// if ($tag == 0) {
	// 	//按顺序查找第一条
	// 	$worksmain = $Db->query("SELECT w.*,u.nickname FROM ".$Base->table('worksmain')." w LEFT JOIN ".$Base->table('user')." u ON u.pk_user_main = w.pk_user_main WHERE u.pk_user_main = ".$_lang['map_uid']."  ORDER BY w.user_sort ASC LIMIT 1","Row");
	// }else{
	// 	$worksmain = $Db->query("SELECT w.view_uuid FROM ".$Base->table('tag_works')." t LEFT JOIN ".$Base->table('worksmain')." w ON w.pk_works_main = t.works_id WHERE tag_id = $tag AND w.pk_user_main = ".$_lang['map_uid']." LIMIT 1",'Row');

	// 	//根据tag查询一条
	// 	// $worksmain = $Db->query("SELECT w.*,u.nickname FROM ".$Base->table('worksmain')." w LEFT JOIN ".$Base->table('user')." u ON u.pk_user_main = w.pk_user_main WHERE w.pk_works_main = $wid ","Row");
	// }
	if (empty($worksmain['view_uuid'])) {
		die("未查询到相应的全景地图");
	}
	header("location:/tour/".$worksmain['view_uuid']);
}
else{
	$flag = Common::sfilter($_REQUEST['flag']);

	$pro;
	if ($flag=='map') {
		//查找第一条
		$pro = $Db->query("SELECT w.*,u.nickname FROM ".$Base->table('worksmain')." w LEFT JOIN ".$Base->table('user')." u ON u.pk_user_main = w.pk_user_main WHERE u.pk_user_main = ".$_lang['map_uid']."  ORDER BY w.user_sort ASC LIMIT 1","Row");
		$view_uuid = $pro['view_uuid'];
		$tp->assign('atlas',$_REQUEST['atlas']);
	}else{
		$pro = $Db->query("SELECT w.*,u.nickname FROM ".$Base->table('worksmain')." w LEFT JOIN ".$Base->table('user')." u ON u.pk_user_main = w.pk_user_main WHERE w.view_uuid = '$view_uuid' AND w.flag_publish = 1 AND u.state=0 ","Row");
		if (empty($pro)) {
				die("未查询到相关项目");
			}
			if(!empty($pro['privacy_password'])&&empty($_SESSION['privacyAccess'][$pro['view_uuid']])){
				//设置了访问密码并且没有登录
				$tp->assign("pid",$pro['pk_works_main']);
				$tp->display($_lang['moban']."/privacy.tpl");
				exit;
			}
	}
	require_once ROOT_PATH.'plugin/plugin_init_function.php';
	plugin_get_plugins("view");
	$Db->execSql("UPDATE ".$Base->table('worksmain')." SET browsing_num = browsing_num+1 WHERE view_uuid = '$view_uuid'");
    //查询imgagesmain
    $worksmain = $Db->query("SELECT w.pk_works_main,w.pk_user_main,w.cdn_host,p.hotspot,p.scene_group FROM ".$Base->table('worksmain')." w LEFT JOIN ".$Base->table('pano_config')." p ON w.pk_works_main = p.pk_works_main WHERE w.view_uuid = '$view_uuid'",'Row');
	$scene_group = $Json->decode($worksmain['scene_group']);
	$groups = $scene_group['sceneGroups'];
	if (empty($groups)) {
		$scenes = $Db->query("SELECT i.view_uuid AS viewuuid ,  i.filename AS sceneTitle ,i.thumb_path AS imgPath FROM ".$Base->table('imgsmain')."i LEFT JOIN ".$Base->table('imgs_works')." iw ON i.pk_img_main = iw.pk_img_main WHERE iw.pk_works_main =".$worksmain['pk_works_main']);
		$groups[]['scenes'] = $scenes;
	}
	// 时间线功能；
	// 所有父子场景
	$all_scenes = [];

	foreach ($groups as  $g) {
		foreach ($g['scenes'] as $k => $s) {
			$row = $Db->query("SELECT parent_img, pk_img_main,location FROM ".$Base->table('imgsmain')." WHERE view_uuid = '".$s['viewuuid']."'",'Row');

			// var_dump($row);
			if($row['pk_img_main'] != 0){
				$child_scenes = $Db->query('SELECT atv_offset, ath_offset, thumb_path, parent_img, pk_img_main, view_uuid, filename FROM u_imgsmain WHERE parent_img = '.$row['pk_img_main']);
			}

			// if(!empty($row['levels']) && $row['levels'] != 'null'){
			// 	$s['levels'] = explode(",", $row['levels']);
			// 	$s['lcount'] = sizeof($s['levels']);
			// 	$s['multi'] = 1;
			// }else{
			// 	$s['multi'] = 0;
			// }
			// if ($k==0&&$size>1) 
			// 	$s['album'] = $g['groupName'];
			// $location =  explode("/sourceimg", $row['location']);
			// if(!empty($location)){
			// 	// $prefix = substr(string, start);
			// 	$prefix = $location[0];
			// }else{
			// 	$prefix = $cdn_host.$worksmain['pk_user_main'];
			// }
			// $s['prefix'] = $prefix.'/works';
			$s['pk_img_main'] = $row['pk_img_main'];
			$s['p_id'] = $row['parent_img'];

			$s['children'] = $child_scenes;
			$all_scenes[$row['pk_img_main']] = $s;
		}
	}

	// var_dump($all_scenes);exit;
	$tp->assign('all_scenes', $all_scenes);
	$tp->assign("pro",$pro);
	$tp->assign("flag",$flag);
	$tp->assign("viewid",$view_uuid);
	$tp->display($_lang['moban']."/tour.tpl");

}

?>