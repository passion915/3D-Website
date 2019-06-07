<?php
//添加图片项目
if(!defined('IN_T')){
	die('hacking attempt');
}
$act =  Common::sfilter($_REQUEST['act']);
$input=null;
if(empty($act)){
	$input = $Json->decode(file_get_contents("php://input"));
	if (!empty($input)) {
		$act = $input['act'];
	}
}
if ($act =="doAdd") {
	//设置该次请求超时时长，1800s
	@ini_set("max_execution_time", "1800"); 
	//兼容php-fpm设置超时
	@ini_set("request_terminate_timeout", "1800");
	
	$re['flag'] = 0;
	$params = $Json->decode(stripslashes($_REQUEST['params']));
	$atlas_id = intval($params['atlas_id']);
	$pname = Common::sfilter($params['pname']);
	$map_annotation_param = $params['map_annotation_param'];
	// if ($atlas_id<=0) {
	// 	$re['msg'] = "分类有误";	
	// }else if(mb_strlen($pname)<=0||mb_strlen($pname)>100){
	// 	$re['msg'] = "项目名称在1到100个字符之间";
	// }else if(empty($params['pic_tags'])||sizeof($params['pic_tags'])>3){
	// 	$re['msg'] = "请选择1到3个标签";
	// }else{
	if(!function_exists('exec')){
		$re['msg'] = '系统当前不支持exec方法，无法发布！';
	}
	else if(!Transaction::get_user_projects_limit($user['pk_user_main'])){
		$re['msg'] = "你可发布的作品数量已达上限，无法再发布！";
	}
	else if(mb_strlen($pname)<=0||mb_strlen($pname)>100){
		$re['msg'] = "项目名称在1到100个字符之间";
	}
	else{
		require_once __DIR__.'/../../source/krpano/cls_common_operation.php';
		$imgsmain = KrOperation::slice($user['pk_user_main'],$params['imgs'],$user['pk_user_main']."/works/");
		
		//执行切图失败 
		if(empty($imgsmain)) {	
			$re['msg'] = '切图失败';
		}
		//切图成功
		else{
			try{
				if (empty($worksmain['flag_from_uc'])){
					require_once ROOT_PATH.'plugin/plugin_init_function.php';
					plugin_get_plugins("edit");
				}
				
				$Db->beginTransaction();
				if($atlas_id<=0||$Db->getCount($Base->table('atlasmain'),'pk_atlas_main',array('pk_atlas_main'=>$atlas_id,'pk_user_main'=>$user['pk_user_main']) )<=0 ){
					//没有提交图册分类
					$atlas_id = $Db->query('SELECT pk_atlas_main FROM '.$Base->table('atlasmain').' WHERE pk_user_main = '.$user['pk_user_main'].' AND atlas_type = 0','One');
					if(empty($atlas_id)){
						$atlas = array("pk_user_main"=>$user['pk_user_main'],"name"=>"默认图册","atlas_type"=>0,"create_time"=>date('Y-m-d H:i:s',Common::gmtime()));
						//初始化用户图册信息
						$atlas_id = $Db->insert($Base->table("atlasmain"),$atlas);
					}
				}
				//存储数据
				$worksmain['pk_user_main']=$user['pk_user_main'];
				$worksmain['pk_atlas_main']=$atlas_id;
				$worksmain['name'] =$pname ;
				$worksmain['thumb_path']= $imgsmain[0]['thumb_path'];
				$worksmain['view_uuid']=Common::guid(16);
				$worksmain['photo_date']=date("Y-m-d H:i:s",Common::gmtime());
				$worksmain['create_time']=date("Y-m-d H:i:s",Common::gmtime());
				$worksmain['flag_allowed_recomm']=intval($params['allow_recomm']);
				$worksmain['flag_publish']=1;
				$worksmain['cdn_host'] = $_lang['cdn_host'];
				
				if (empty($worksmain['flag_from_uc']))
					$worksmain = init_data($worksmain,'worksmain');

				$wid = $Db->insert($Base->table('worksmain'),$worksmain);
				foreach ($imgsmain as $value) {
					$value['pk_atlas_main']=0;
					$value['pk_user_main']=$user['pk_user_main'];
					$value['create_time']=date("Y-m-d H:i:s",Common::gmtime());
					$img_id = $Db->insert($Base->table('imgsmain'),$value);
					$Db->insert($Base->table('imgs_works'),array('pk_img_main' =>$img_id ,'pk_works_main'=>$wid ));
				}
	
				$panoconfig['pk_works_main']=$wid;
				$panoconfig['open_alert']='{}';
				$panoconfig['bg_music']='{"useMusic": false,"isWhole": true}';
				$panoconfig['sky_land_shade']='{ "useShade": false,"isWhole": true}';
				$panoconfig['tour_guide']='{"useStartImg": false,"useEndImg": false,"points": []}';
				$panoconfig['url_phone_nvg']='{}';
				$panoconfig['speech_explain']='{"isWhole": true,"useSpeech": false}';
				$panoconfig['angle_of_view']='{"viewSettings": []}';
				$panoconfig['special_effects']='{}';
				$panoconfig['sand_table']='{"sandTables": [],"isOpen": false}';
				$panoconfig['custom_logo']='{"logoImgPath": "","useCustomLogo": false}';
				$panoconfig['scene_group']='{"sceneGroups": []}';
				$panoconfig['hotspot']='{}';
				$panoconfig['loading_img']='{}';
				$panoconfig['custom_right_button']='{}';
				
				if (empty($worksmain['flag_from_uc']))
					$panoconfig = init_data($panoconfig,'pano_config');
				$Db->insert($Base->table('pano_config'),$panoconfig);
				foreach ($params['pic_tags'] as  $tid) {
					if ($Db->getCount($Base->table("tag"),"id",array("id"=>$tid,"type"=>1))) {
						$Db->insert($Base->table("tag_works"),array("tag_id"=>$tid,"works_id"=>$wid));
					}
				}
				foreach ($params['custome_tags'] as $ctid) {
					if ($Db->getCount($Base->table("custom_tag"),"id",array("id"=>$ctid,"type"=>1,'uid'=>$user['pk_user_main']))) {
						$Db->insert($Base->table("custom_tag_project"),array("tid"=>$ctid,"pid"=>$wid));
					}
				}
				if($_lang['map_uid']!=$user['pk_user_main']){
					$map_annotation_param['cus_view_id'] = $worksmain['view_uuid'];
					$map_annotation_param['cus_wid'] = $wid;
					$map_annotation_param['create_time'] = date("Y-m-d H:i:s",Common::gmtime());
					$tag='';
					foreach ($params['pic_tags'] as  $tid) {
						$tag.=$tid.',';
					}
					$map_annotation_param['tag'] = $tag;
					$Db->insert($Base->table('map_annotation'),$map_annotation_param);
				}
				$Db->commit();
				$re['flag'] = 1;
				$re['pid'] = $wid;
				$re['view_uuid'] = $worksmain['view_uuid'];
				$re['thumb_path'] = $worksmain['thumb_path'];
			}catch(Exception $e){
				$Db->rollback();
				print_r($e);
				$re['msg'] = '服务器异常';
			}
		}
	}
	echo $Json->encode($re);
	exit;
}
else if($act =='keep_alive'){
	//前端循环请求该方法 ，避免切图时造成session丢失
	echo "";
	exit;
}else{
	$atlas_list = $Db->query("SELECT * FROM ".$Base->table("atlasmain")." WHERE pk_user_main = ".$user['pk_user_main']);
	if(empty($atlas_list)){
		$atlas = array("pk_user_main"=>$user['pk_user_main'],"name"=>"默认图册","atlas_type"=>0,"create_time"=>date('Y-m-d H:i:s',Common::gmtime()));
		//初始化用户图册信息
        $atlas['pk_atlas_main'] = $Db->insert($Base->table("atlasmain"),$atlas);
        $atlas_list[] = $atlas;
	}
	//显示添加页面
	$tp->assign("atlas",$atlas_list);
	$tp->assign("tags",$Db->query("SELECT * FROM ".$Base->table('tag')));
	$tp->assign('custom_tags',$Db->query('SELECT * FROM '.$Base->table('custom_tag').' WHERE uid = '.$user['pk_user_main']));
	$tp->assign('title','发布全景');
	$tp->assign('up_url',$_lang['up_url']);
	$tp->assign('img_store_type',$_lang['global_storage']);
	$tp->assign('pic_add_ad',Transaction::get_ad_by_postion(6));
}

function init_data($arr,$type){
	$config = $_SESSION['edit'];
	foreach($config as $v){
		if ($v['table']==$type&&isset($v['default'])) {
			$arr[$v['column']] = $v['default'];
		}
	}   
	return $arr;
}

?>