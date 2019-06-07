<?php
	define('IN_T',true);
	require("../source/include/init.php");
	$flag = common::sfilter($_REQUEST['flag']);
    $view_uuid = Common::sfilter($_REQUEST['view']);
    //查询imgagesmain
    $worksmain = $Db->query("SELECT w.pk_works_main,w.pk_user_main,w.cdn_host,p.hotspot,p.scene_group FROM ".$Base->table('worksmain')." w LEFT JOIN ".$Base->table('pano_config')." p ON w.pk_works_main = p.pk_works_main WHERE w.view_uuid = '$view_uuid'",'Row');
	if (empty($worksmain)) {
		die("未找到相关项目");
	}
	if ($flag=="map") {
		$atlas = intval($_REQUEST['atlas']);
		// if ($atlas == 0 ) {
		// 	$scenes = $Db->query("SELECT m.view_uuid AS viewuuid , m.filename AS sceneTitle ,m.thumb_path AS imgPath FROM (SELECT i.pk_img_main FROM (SELECT pk_works_main FROM u_worksmain WHERE pk_user_main=".$_lang['map_uid']." ) w LEFT JOIN u_imgs_works i ON w.pk_works_main = i.pk_works_main) iw LEFT JOIN u_imgsmain m ON iw.pk_img_main = m.pk_img_main");
		// }else{
		// 	$scenes = $Db->query("SELECT m.view_uuid AS viewuuid , m.filename AS sceneTitle ,m.thumb_path AS imgPath FROM u_imgsmain m WHERE m.pk_atlas_main=".$atlas);
		// }
		$sql = "SELECT m.view_uuid AS viewuuid , m.filename AS sceneTitle ,m.thumb_path AS imgPath FROM u_imgsmain m WHERE m.pk_user_main = ".$_lang['map_uid'];
		if ($atlas > 0) {
			$sql.=" AND m.pk_atlas_main = ".$atlas;
		}
		$scenes = $Db->query($sql);
		foreach ($scenes as $ms) {
			$groups[0]['scenes'][]=$ms;
		}
	}else{
		$scene_group = $Json->decode($worksmain['scene_group']);
		$groups = $scene_group['sceneGroups'];
		if (empty($groups)) {
			$scenes = $Db->query("SELECT i.view_uuid AS viewuuid , i.filename AS sceneTitle ,i.thumb_path AS imgPath FROM ".$Base->table('imgsmain')."i LEFT JOIN ".$Base->table('imgs_works')." iw ON i.pk_img_main = iw.pk_img_main WHERE iw.pk_works_main =".$worksmain['pk_works_main']);
			$groups[]['scenes'] = $scenes;
		}
	}
	$scenesRes;
	$children_scenes = [];
	if(count($groups)>1){
		foreach ($groups as  $g) {
			foreach ($g['scenes'] as $k => $s) {
				$row = $Db->query("SELECT parent_img, pk_img_main,location FROM ".$Base->table('imgsmain')." WHERE view_uuid = '".$s['viewuuid']."'",'Row');
				
				$child_scenes = $Db->query('SELECT location, thumb_path, parent_img, pk_img_main, view_uuid, filename FROM u_imgsmain WHERE parent_img = '.$row['pk_img_main']);
				$children_scenes = array_merge($child_scenes, $children_scenes);
				if ($k==0) 
					$s['album'] = $g['groupName'];
				$scenesRes[] = $s;
			}
		}
	}else{
		$temp = $groups[0]['scenes'];

			// var_dump($temp);
		foreach ($temp as $key => $value) {
			$row = $Db->query("SELECT parent_img, pk_img_main,location FROM ".$Base->table('imgsmain')." WHERE view_uuid = '".$value['viewuuid']."'",'Row');
			// var_dump($row);
					
			$child_scenes = $Db->query('SELECT location, thumb_path, parent_img, pk_img_main, view_uuid, filename FROM u_imgsmain WHERE parent_img = '.$row['pk_img_main']);

			$children_scenes = array_merge($child_scenes, $children_scenes);
		}

		$scenesRes = $temp;
	}
	foreach ($children_scenes as $key => $value) {
		$cdn_host = empty($value['cdn_host'])?$_lang['cdn_host']:$value['cdn_host'];


		$location =  explode("/media", $value['location']);
		if(!isset($location[1])){
			$location =  explode("/sourceimg", $value['location']);
		}

		if(!empty($location)){
			$prefix = $location[0];
		}else{
			$prefix = $cdn_host.$value['pk_user_main'];
		}
		// var_dump($prefix);
		$value['prefix'] = $prefix.'/works';
		$children_scenes[$key] = $value;
	}

	// var_dump($children_scenes);
	$tp->assign('children_scenes', $children_scenes);

	
	require_once ROOT_PATH.'plugin/plugin_init_function.php';
	plugin_get_plugins("view",true);

	
	$cdn_host = empty($worksmain['cdn_host'])?$_lang['cdn_host']:$worksmain['cdn_host'];
	$tp->assign('cdn_host',$cdn_host);
	$tp->assign('prefix',$cdn_host.$worksmain['pk_user_main'].'/works');
	$tp->assign('scenesRes',$scenesRes);
	$tp->assign('hotspot',$Json->decode($worksmain['hotspot']));
	$tp->assign('startscene',Common::sfilter($_REQUEST['startscene']));
	$tp->setTemplateDir(ROOT_PATH.'tour');
	$tp->display('tour.xml');
?>