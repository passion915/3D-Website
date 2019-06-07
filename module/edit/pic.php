<?php

if(!defined('IN_T')){
	die('hacking attempt');
}
$act =  Common::sfilter($_REQUEST['act']);
$input=null;
if (empty($act)) {
	$input = $Json->decode(file_get_contents("php://input"));
	if (!empty($input)) {
		$act = $input['act'];
	}
}
if($act =="update_init"){
	//查询项目  worksmain
	$pid =intval($input['pid']);
	$worksmain = $Db->query("SELECT * FROM ".$Base->table('worksmain')." WHERE pk_works_main = ".$pid." AND pk_user_main = ".$user['pk_user_main'],'Row');

	if (empty($worksmain)) {
		die("未找到相关项目");
	}
	//查询图片  imagesmain
	$imgsmain = $Db->query("SELECT i.* FROM ".$Base->table('imgsmain')."i LEFT JOIN ".$Base->table('imgs_works')." iw ON i.pk_img_main = iw.pk_img_main WHERE iw.pk_works_main =".$pid);
	
	//查询配置  panoconfig
	$panoconfig = $Db->query("SELECT * FROM ".$Base->table('pano_config')." WHERE pk_works_main = ".$pid,"Row");

	// if (!empty($panoconfig['hotspot']['imgtext'])) {
	// 	$panoconfig['hotspot']['imgtext'] = base64_decode($panoconfig['hotspot']['imgtext'])
	// }

	$panoconfig = Transaction::decode_str2arr($panoconfig);
	$hotspots = &$panoconfig['hotspot'];
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
	//查询对应分类
	$atlasmain = $Db->query("SELECT name FROM ".$Base->table('atlasmain')." WHERE pk_atlas_main = ".$worksmain['pk_atlas_main'],'Row');
	$worksmain['name'] = $worksmain['name']." ";
	//查询项目对应标签
	$tags = $Db->query("SELECT * FROM ".$Base->table('tag_works')." WHERE works_id = $pid");
	$tag_list = $Db->query("SELECT * FROM ".$Base->table('tag')." WHERE type = 1");
	$custom_tag_list = $Db->query('SELECT * FROM '.$Base->table('custom_tag').' WHERE uid = '.$user['pk_user_main'].' AND type =1' ) ;
	$custom_tags = $Db->query('SELECT * FROM '.$Base->table('custom_tag_project').' WHERE pid = '.$pid);
	$result = array('worksmain' => $worksmain,'imgsmain'=>$imgsmain,'panoConfig'=>$panoconfig,'userInfo'=>$user,"atlasmain"=>$atlasmain,"tags"=>$tags,"tag_list"=>$tag_list,'custom_tag_list'=>$custom_tag_list,'custom_tags'=>$custom_tags);
	// $result = array('worksmain' => $worksmain,'imgsmain'=>$imgsmain,'panoConfig'=>$panoconfig,'userInfo'=>$user,"atlasmain"=>$atlasmain);
	echo  $Json->encode($result);
	exit;
}else if($act == "save_panosetting"){
	$result['flag'] =0;
	// $hotspots = &$input['hotspot'];
	//对图文进行base64
	// foreach ($hotspots as &$v) {
	// 		$imgtext = &$v['imgtext'];
	// 		if (!empty($imgtext)) {
	// 			foreach ($imgtext as &$v2) {
	// 				$v2['wordContent'] = base64_encode($v2['wordContent']);
	// 			}
	// 		}
	// }
	filter_array($input);
	$pk_works_main = intval($input['pk_works_main']);
	if ($Db->getCount($Base->table('worksmain'),"pk_works_main",array("pk_works_main"=>$pk_works_main ,"pk_user_main"=>$user['pk_user_main']))<=0) {
		$result['msg'] ="非法操作";
	}else{
		$params = array(
			'angle_of_view' =>$Json->encode($input['angle_of_view']),
			'special_effects'=>$Json->encode($input['special_effects']),
			'hotspot'=>$Json->encode_unescaped_unicode($input['hotspot']),
			'sand_table'=>$Json->encode_unescaped_unicode($input['sand_table']),
			'tour_guide'=>$Json->encode($input['tour_guide']),
			'scene_group'=>$Json->encode_unescaped_unicode($input['scene_group']),
			 );
		$Db->update($Base->table('pano_config'),$params,array("pk_works_main"=>$pk_works_main));
		print_r($params);
		$result['flag'] = 1;
	}
	echo $Json->encode($result,JSON_NUMERIC_CHECK);
	exit;
}elseif($act =='save_children_view'){
	$result['flag'] =0;
	$pk_works_main = intval($_REQUEST['pk_works_main']);
	$count = $Db->getCount($Base->table('worksmain'),"pk_works_main",array("pk_works_main"=>$pk_works_main ,"pk_user_main"=>$user['pk_user_main']));

	$input = $_REQUEST['children_view'];
	// $pk_works_main = 7;
	$scene_name = Common::sfilter($input['scene_name']);
	if(empty($scene_name)){
		$result['msg'] ="参数错误";
	}elseif($count<=0) {
		$result['msg'] ="非法操作";
	}
	else{
		// 获取父场景数据
		$view_uuid = explode('_', $scene_name);
		$view_uuid = $view_uuid['1'];
		unset($input['scene_name']);
		$Db->update('u_imgsmain', $input, ['view_uuid'=> $view_uuid]);
		$result['flag'] = 1 ;
	}
	echo $Json->encode($result,JSON_NUMERIC_CHECK);
	exit;
}

else if($act == 'update_works'){
	$result['flag'] =0;
	$works = $input['works'];
	$panoconfig = $input['panoConfig'];
	$tags = $works['tags'];
	$imgs = $input['imgs'];
	$name = Common::sfilter($works['name']);
	$properties = $input['properties'];
	//验证项目属性是否合法
	if (!empty($properties)) {
		filter_array($properties);
		foreach ($properties as $p) {
			if ($p['parent_node']<=0 || $p['child_node']<0) {
				$result['msg'] = "请选择项目属性";
				echo  $Json->encode($result);
				exit;
			}
		}
	}

	$regions = $input['regions'];
	if(!empty($regions)){
		foreach ($regions as &$r) {
			$r = intval($r);
			if ($r<=0) {
				$result['msg'] = "请选择区域";
				echo  $Json->encode($result);
				exit;
			}
		}
	}

	if (empty($works)||empty($panoconfig)) {
		//没传项目数据直接返回
		$result['msg'] = '未接受到数据';
	}else if(empty($name)||mb_strlen($name)>30){
		$result['msg'] = "请输入1到30个字符的项目名称";
	}else if(empty($tags)){
		//没有选择标签
		$result['msg'] ="请选择分类标签";
	}else if(empty($imgs)){
		//没有图片
		$result['msg'] ="不能删除所有图片";
	}else if ($Db->getCount($Base->table('worksmain'),"pk_works_main",array("pk_works_main"=>$works['pk_works_main'],"pk_user_main"=>$user['pk_user_main']))<=0){
		//用户id和项目不对应
		$result['msg'] = '非法操作';
	}else{
		//是否设置项目密码
		$privacy_password = Common::sfilter($works['privacy_password']);
		if (!empty($privacy_password)&&(mb_strlen($privacy_password)<3||mb_strlen($privacy_password)>20||!preg_match('/^[A-Za-z0-9]+$/',$privacy_password))) {
			$result['msg'] = '请输入3到20位英文或数字密码';
			echo  $Json->encode($result,JSON_NUMERIC_CHECK);
			exit;
		}
	 	filter_array($works);
		filter_array($imgs);
		filter_array($panoconfig);
		$Db->beginTransaction();
		try{
			//修改worksmain的标签
			//删除原来的标签
			$Db->delete($Base->table("tag_works"),array("works_id"=>$works['pk_works_main']));
			//插入标签
			foreach ($tags as  $tid) {
				if ($Db->getCount($Base->table("tag"),"id",array("id"=>$tid))) {
					$Db->insert($Base->table("tag_works"),array("tag_id"=>$tid,"works_id"=>$works['pk_works_main']));
				}
			}
			$Db->delete($Base->table('custom_tag_project'),array('pid'=>$works['pk_works_main']));
			foreach ($works['custome_tags'] as $ctid) {
				if ($Db->getCount($Base->table("custom_tag"),"id",array("id"=>$ctid,"type"=>1,'uid'=>$user['pk_user_main']))) {
					$Db->insert($Base->table("custom_tag_project"),array("tid"=>$ctid,"pid"=>$works['pk_works_main']));
				}
			}
			//查询数据库原来的图片
			$imgsmain = $Db->query("SELECT i.*,iw.pk_works_main FROM ".$Base->table('imgsmain')."i LEFT JOIN ".$Base->table('imgs_works')." iw ON i.pk_img_main = iw.pk_img_main WHERE iw.pk_works_main =".$works['pk_works_main']);
			//删除图片
			foreach ($imgsmain as $d_v) {
				$flag = true;
				foreach ($imgs as $n_v) {
					if ($d_v['pk_img_main']==$n_v['pk_img_main']) {
						$flag = false;
						break;
					}
				}
				if ($flag) {
					//找到要删除的文件
					$Db ->delete($Base->table('imgs_works'),array('pk_works_main'=>$d_v['pk_works_main'],'pk_img_main'=>$d_v['pk_img_main']));
				}

			}
			//添加图片
			foreach ($imgs as $n_v){
				$flag = true;			
				foreach ($imgsmain as $d_v) {
					if ($d_v['pk_img_main']==$n_v['pk_img_main']) {
						$flag = false;
						break;
					}
				}
				if ($flag) {
					//添加图片
					$Db->insert($Base->table('imgs_works'),array('pk_img_main' =>$n_v['pk_img_main'] ,'pk_works_main'=>$n_v['pk_works_main'] ));
				}
			}

			
			//修改worksmain
			$worksmain_params = array(
					'name' => $name,
					'profile' => Common::sfilter($works['profile']),
					'flag_publish' => intval($works['flag_publish']),
					'privacy_password' => empty($privacy_password)?"":$privacy_password,
					'hideuser_flag' => intval($works['hideuser_flag']),
					'hidelogo_flag' => intval($works['hidelogo_flag']),
					'hideviewnum_flag'=>intval($works['hideviewnum_flag']),
					'flag_allowed_recomm'=>intval($works['flag_allowed_recomm']),
					'hidevrglasses_flag'=>intval($works['hidevrglasses_flag']),
					'hideprofile_flag'=>intval($works['hideprofile_flag']),
					'hidepraise_flag'=>intval($works['hidepraise_flag']),
					'hideshare_flag'=>intval($works['hideshare_flag'])
				);
			$Db->update($Base->table('worksmain'),$worksmain_params,array('pk_works_main'=>$works['pk_works_main']));
			$panoconfig_params = array(
					'footmark' => empty($panoconfig['footmark'])?0:intval($panoconfig['footmark']),
					'littleplanet'=>empty($panoconfig['littleplanet'])?0:intval($panoconfig['littleplanet']),
					'gyro' => empty($panoconfig['gyro'])?0:intval($panoconfig['gyro']),
					'comment' => empty($panoconfig['comment'])?0:intval($panoconfig['comment']),
					'scenechoose' => empty($panoconfig['scenechoose'])?0:intval($panoconfig['scenechoose']),
					'autorotate' => empty($panoconfig['autorotate'])?0:intval($panoconfig['autorotate']),
					'open_alert' => $Json->encode_unescaped_unicode($panoconfig['open_alert']),
					'sky_land_shade' => $Json->encode_unescaped_unicode($panoconfig['sky_land_shade']),
					'url_phone_nvg' => $Json->encode_unescaped_unicode($panoconfig['url_phone_nvg']),
					'bg_music' => $Json->encode_unescaped_unicode($panoconfig['bg_music']),
					'speech_explain' => $Json->encode_unescaped_unicode($panoconfig['speech_explain']),
					'custom_logo' => $Json->encode_unescaped_unicode($panoconfig['custom_logo']),
					'scene_group' => $Json->encode_unescaped_unicode($panoconfig['scene_group']),
					'custom_right_button' => $Json->encode_unescaped_unicode($panoconfig['custom_right_button']),
					'top_ad' => $Json->encode_unescaped_unicode($panoconfig['top_ad']),
				);
			$top_ad = $panoconfig['top_ad'];
			$content="";
			if (!empty($top_ad)&&$top_ad['show']==1&&!empty($top_ad['adcontent'])) {
				$content = mb_strlen($top_ad['adcontent'])>50?substr($top_ad['adcontent'], 0,50):$top_ad['adcontent'];
			}
			$Db->update($Base->table('map_annotation'),array('adcontent'=>$content),array('cus_wid'=>$works['pk_works_main']) );
			$Db->update($Base->table('pano_config'),$panoconfig_params,
				array('pk_works_main'=>$works['pk_works_main']));

			//保存用户选择的区域
			if(!empty($regions)){
				$Db->delete($Base->table('work_region'),array('workid'=>$works['pk_works_main']) );
				$regions['workid'] = $works['pk_works_main'];
				$Db->insert($Base->table('work_region'),$regions);
			}
			//保存用户选择的项目属性
			if(!empty($properties)){
				$Db->delete($Base->table('work_property'),array('workid'=>$works['pk_works_main']) );
				foreach ($properties as $property) {
					$Db->insert($Base->table('work_property'),array('parent_node'=>$property['parent_node'],'child_node'=>$property['child_node'],'workid'=>$works['pk_works_main']));
				}
			}

		$Db->commit();
		$result['flag'] = 1;
		}catch(Exception $e){
			$Db->rollback();
			$result['msg'] = '操作失败！';
		}
	}
	echo  $Json->encode($result,JSON_NUMERIC_CHECK);
	exit;
}else if($act == "renameImg"){
	//重命名图片
	$pk_img_main = intval($input['pk_img_main']);	
	$filename = Common::sfilter($input['filename']);
	$resutl['flag'] = 0;
	if (empty($filename)||mb_strlen($filename,'utf-8')>30) {
		$result['msg'] = "文件名必须在1到30个字符之间";
	}else if($Db->getCount($Base->table('imgsmain'),'pk_img_main',
		array('pk_img_main'=>$pk_img_main,'pk_user_main'=>$user['pk_user_main']))<=0){
		$result['msg'] = "非法操作";
	}else{
		$Db->update($Base->table('imgsmain'),array('filename'=>$filename),array('pk_img_main'=>$pk_img_main) );
		$result['flag'] = 1;
	}
	echo  $Json->encode($result,JSON_NUMERIC_CHECK);
	exit;
}else if($act =="replaceWorkCover"){
	//修改封面
	$pk_works_main = intval($input['pk_works_main']);	
	$thumbpath = Common::sfilter($input['thumb_path']);
	$result['flag'] = 0;
	if (empty($thumbpath)) {
		$result['msg'] = "没有图片";
	}else if ($Db->getCount($Base->table('worksmain'),"pk_works_main",array("pk_works_main"=>$pk_works_main,"pk_user_main"=>$user['pk_user_main']))<=0){
		//用户id和项目不对应
		$result['msg'] = '非法操作';
	}else{
		$Db->update($Base->table("worksmain"),array('thumb_path'=>$_lang['cdn_host'].$thumbpath),array('pk_works_main'=>$pk_works_main));
		$result['absolutelocation'] = $_lang['cdn_host'].$thumbpath;
		$result['flag'] = 1;
	}
	echo  $Json->encode($result,JSON_NUMERIC_CHECK);
	exit;
}
//设置项目访问密码
else if($act == 'setPrivacyWord'){
	$privacyWord =  Common::sfilter($_REQUEST['privacyWord']);
	$pid = intval($_REQUEST['pid']);
	$re['status'] = 0 ;
	if (empty($privacyWord)||mb_strlen($privacyWord)<3||mb_strlen($privacyWord)>20||!preg_match('/^[A-Za-z0-9]+$/',$privacyWord)) {
		$re['msg'] = '请输入3到20位英文或数字密码';
	}else{
		$Db->update($Base->table('worksmain'),array('privacy_password'=>$privacy_password),array('pk_works_main'=>$pid,'pk_user_main'=>$user['pk_user_main']));
		$re['status']= 1;
	}
	echo $Json->encode($re);

}else if($act == 'edit_position'){
	$re['status'] = 0;
	$pid = intval($_POST['pid']);
	$params = $_POST['params'] ;
	$params['cover'] = Common::sfilter($params['cover']);
	$params['title'] = Common::sfilter($params['title']);
	$params['desc'] = filter_desc($params['desc']);
	$params['marker_ico'] = Common::sfilter($params['marker_ico']);
	if (empty($params['point'])) {
		$re['msg'] = "请选择地图标注点";
	}else if (empty($params['cover'])) {
		$re['msg'] = "请选择封面";
	}else if(empty($params['title']) || mb_strlen($params['title'])>20){
		$re['msg'] = "请输入20个字以内的标题";
	}else if(empty($params['desc'])){
		$re['msg'] = "请输入描述";
	}else if ($Db->query("SELECT COUNT(*) FROM u_worksmain WHERE pk_works_main = $pid AND pk_user_main = ".$user['pk_user_main'],'One') <=0){
		$re['msg'] ='无法查询到该项目';
	}else{
		$Db->update('u_worksmain',array('position'=>$params['point'],'position_title'=>$params['title'],'position_cover'=>$params['cover'],'position_desc'=>$params['desc'],'marker_ico'=>$params['marker_ico']),array('pk_works_main'=>$pid));
		$re['status'] = 1;
	}
	echo $Json->encode($re);
	die;
}else if($act == 'get_position'){
	$pid =$pid = intval($_POST['pid']);
	$re = $Db->query("SELECT thumb_path,name,position,position_title,position_cover,position_desc FROM u_worksmain WHERE pk_works_main = $pid ",'Row');
	if (empty($re['position_title'])) {
		$re['position_title'] = $re['name'];
	}
	if (empty($re['position_cover'])) {
		$re['position_cover'] = $re['thumb_path'];
	}
	echo $Json->encode($re);
	die;
}
else{

	//跳转编辑项目页面
	$pid = intval($_REQUEST['pid']);
	if ($pid<=0||$Db->getCount($Base->table('worksmain'),"pk_works_main",array("pk_works_main"=>$pid ,"pk_user_main"=>$user['pk_user_main']))<=0) {
		die("查询不到该项目");
	}
	//获取plugin
	require_once ROOT_PATH.'plugin/plugin_init_function.php';
	plugin_get_plugins("edit");
	//获取上传地址
	$tp->assign('up_url',$_lang['up_url']);
	//获取区域信息
	require_once ROOT_PATH.'source/include/cls_region.php';
	$tp->assign('maxNode',Region::getMaxNode());
	$tp->assign('regions',$Json->encode($Db->query('SELECT region_1,region_2,region_3,region_4,region_5,region_6 FROM '.$Base->table('work_region').' WHERE workid = '.$pid,'Row') ));

	require_once ROOT_PATH.'source/include/cls_property.php';
	$tp->assign('parentNodes',Property::listByPid(0));
	$tp->assign('properties',$Json->encode($Db->query('SELECT * FROM '.$Base->table('work_property').' WHERE workid = '.$pid )));

	$markers = $Db->query("SELECT * FROM u_def_mediares WHERE type = 3");

	$tp->assign('markers',$markers);
	$tp->assign('marker_ico',$Db->query("SELECT marker_ico FROM u_worksmain WHERE pk_works_main = $pid","One"));

	// 获取
    //查询imgagesmain
    $worksmain = $Db->query("SELECT w.pk_works_main,w.pk_user_main,w.cdn_host,p.hotspot,p.scene_group FROM ".$Base->table('worksmain')." w LEFT JOIN ".$Base->table('pano_config')." p ON w.pk_works_main = p.pk_works_main WHERE w.pk_works_main = '$pid'",'Row');


	$scene_group = $Json->decode($worksmain['scene_group']);
	$groups = $scene_group['sceneGroups'];
	if (empty($groups)) {
		$scenes = $Db->query("SELECT i.view_uuid AS viewuuid ,  i.filename AS sceneTitle ,i.thumb_path AS imgPath FROM ".$Base->table('imgsmain')."i LEFT JOIN ".$Base->table('imgs_works')." iw ON i.pk_img_main = iw.pk_img_main WHERE iw.pk_works_main =".$worksmain['pk_works_main']);
		$groups[]['scenes'] = $scenes;
	}

	$cdn_host = empty($worksmain['cdn_host'])?$_lang['cdn_host']:$worksmain['cdn_host'];
	//查询图片  imagesmain  
	$all_scenes = [];
	foreach ($groups as  $g) {
		foreach ($g['scenes'] as $k => $s) {
			$row = $Db->query("SELECT parent_img ,pk_img_main,location FROM ".$Base->table('imgsmain')." WHERE view_uuid = '".$s['viewuuid']."'",'Row');
			$child_scenes = $Db->query('SELECT thumb_path, parent_img, pk_img_main, view_uuid, filename FROM u_imgsmain WHERE parent_img = '.$row['pk_img_main']);
			$s['pk_img_main'] = $row['pk_img_main'];
			$s['p_id'] = $row['parent_img'];

			$s['children'] = $child_scenes;
			$all_scenes[$row['pk_img_main']] = $s;
		}
	}

	// var_dump($all_scenes);exit;
	$tp->assign('all_scenes', $all_scenes);
	// $tags = $Db->query("SELECT * FROM ".$Base->table('tag_works')." WHERE works_id = $pid");
	// $tag_list = $Db->query("SELECT * FROM ".$Base->table('tag')." WHERE type = 1");
	// $tp->assign('default_tags',$Db->query("SELECT * FROM ".$Base->table('tag_works')." WHERE works_id = $pid"));
}

function filter_array(&$arr){
	foreach($arr as $k => &$v){
		if(is_array($v)){
			filter_array($v);
		}else{
			if ((string)$k=='imgtext_wordContent') {
				$v=base64_encode($v);
			}else{
				$v=Common::sfilter($v);
			}
		}
	}
}
function filter_desc($char){
  	$char = preg_replace("(\\\\+')","'",$char);  //连续反斜杠加单引号，处理为单引号，php会对form表单中的'自动添加反斜杠
	$char = preg_replace('(\\\\+")','"',$char);  //连续反斜杠加双引号，处理为双引号，php会对form表单中的"自动添加反斜杠
	$char = str_replace("'","\'",$char);  //单引号添加反斜杠
    $char = str_replace('"',"\\\"",$char);  //双引号添加反斜杠
	$char = str_replace("<?","&lt;?",$char);  //转义php的声明符
	$char = str_replace("?>","?&gt;",$char); 
	$char = str_replace("<script","&lt;script",$char);  //转义script声明符
	$char = str_replace("</script>","&lt;/script&gt;",$char); 
	return $char;
}
?>