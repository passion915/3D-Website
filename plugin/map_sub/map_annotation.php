<?php 
	/*
	 * Krpano100 全景地图标注
	 * ============================================================================
	 * 技术支持：2015-2099 成都世纪川翔科技有限公司
	 * 官网地址: http://www.krpano100.com
	 * ----------------------------------------------------------------------------
	 * $Author: wanghao 932625974#qq.com $
	 * $Id: bind.php 28028 2016-06-19Z yuanjiang $
	*/
	define('IN_T',true);
	require_once "../../source/include/init.php";
	$act = Common::sfilter($_REQUEST['act']);
	$input=null;
	if (empty($act)) {
		$input = $Json->decode(file_get_contents("php://input"));
		if (!empty($input)) {
			$act = $input['act'];
		}
	}
	if($act == 'get_pano_config'){
		$config = $Db->query('SELECT p.hotspot,w.pk_works_main,w.view_uuid FROM '.$Base->table('pano_config').' p LEFT JOIN '.$Base->table('worksmain').' w ON p.pk_works_main =  w.pk_works_main WHERE w.pk_works_main = 230','Row');
		echo $Json->encode($config);
		exit;
	}else if($act == 'doAdd'){
		$re['status'] = 0 ;
		$param = $input['param'];
		$data =  array(
				'sys_wid' => intval($param['sys_wid']),
				'sys_view_id' => Common::sfilter($param['sys_view_id']),
				'sys_scene' => Common::sfilter($param['sys_scene']),
				'cus_wid' => intval($param['cus_wid']),
				'cus_view_id' => Common::sfilter($param['cus_view_id']),
				'ath' => intval($param['ath']),
				'atv' => intval($param['atv']),
				'cus_hotspot_name' => Common::sfilter($param['cus_hotspot_name']),
				'cus_hotspot_title' => Common::sfilter($param['cus_hotspot_title']),
				'cus_img' => '/plugin/map_annotation/images/dyn_fj.png',	
			);

		//判断用户传入的项目是否为空，用户传入的项目名称是否一致
		$cus_woks = $Db->query('SELECT * FROM '.$Base->table('worksmain').' WHERE pk_works_main = '.$data['cus_wid'],'Row');
		if (empty($cus_woks)) {
			$re['msg'] = '无法查询对应的地图数据';
			echo $Json->encode($re);
			exit;
		}
		// if (Common::sfilter($cus_woks['name'])!=$data['cus_hotspot_title']) {
		// 	$re['msg'] = '地图标注文字不正确';
		// 	echo $Json->encode($re);
		// 	exit;
		// }
		if(mb_strlen($data['cus_hotspot_title'])>10) $data['cus_hotspot_title'] = substr($data['cus_hotspot_title'], 0,10).'...';
		//判断是否添加过标注
		$cus_map_a = $Db->query('SELECT * FROM '.$Base->table('map_annotation').' WHERE cus_wid = '.$data['cus_wid'],'Row');
		if (empty($cus_map_a)) {
			$data['create_time'] =date("Y-m-d H:i:s",Common::gmtime());
			$Db->insert($Base->table('map_annotation'),$data);
		}else{
			$Db->update($Base->table('map_annotation'),$data,array('id'=>$cus_map_a['id']));
		}
		$re['status'] = 1;
		
		echo $Json->encode($re);
		exit;
	}else if($act == 'list'){
		$wid = intval($_REQUEST['sys_wid']);
		$scene = Common::sfilter($_REQUEST['scene']);
		$list = $Db->query('SELECT * FROM '.$Base->table('map_annotation').' WHERE sys_wid='.$wid.' AND sys_scene = "'.$scene.'"');
		if (empty($list)) {
			$list = array();
		}
		echo $Json->encode($list);
		exit;
	}else if($act =='get'){
		#查询某一个标注
		$wid = intval($_REQUEST['wid']);
		$cus_map_a = $Db->query('SELECT * FROM '.$Base->table('map_annotation').' WHERE cus_wid = '.$wid,'Row');
		if(empty($cus_map_a))
			$cus_map_a = array();
		echo $Json->encode($cus_map_a);
		exit;
	}else if($act == 'remove'){
		$re['status'] = 0;
		$wid = intval($input['cus_wid']);
		$cus_woks = $Db->query('SELECT * FROM '.$Base->table('map_annotation').' WHERE cus_wid = '.$wid,'Row');
		if (!empty($Db->query('SELECT pk_user_main FROM '.$Base->table('map_annotation').' m LEFT JOIN '.$Base->table('worksmain').' w ON m.cus_wid = w.pk_works_main WHERE m.cus_wid = '.$wid.' AND w.pk_user_main = '.$user['pk_user_main'] ,'Row'))) {
			$Db->delete($Base->table('map_annotation'),array('cus_wid'=>$wid) );
		}
		$re['status'] = 1;
		echo $Json->encode($re);
		exit;
	}


?>