<?php 
	/*
	 * Krpano100 全景地图标注管理
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
		$wid = intval($input['wid']);
		$config = $Db->query('SELECT p.hotspot,w.pk_works_main,w.view_uuid FROM '.$Base->table('pano_config').' p LEFT JOIN '.$Base->table('worksmain').' w ON p.pk_works_main =  w.pk_works_main WHERE w.pk_works_main = '.$wid,'Row');
		echo $Json->encode($config);
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
	}
	else if($act=='get'){
		$re['status'] = 0;
		$pid = intval($input['pid']);
		$cus_woks = $Db->query('SELECT * FROM '.$Base->table('map_annotation').' WHERE id = '.$pid,'Row');
		if (empty($cus_woks)) {
			$re['msg'] ='查询不到该标注';
		}else{
			$re['obj'] = $cus_woks;
			$re['status'] = 1;
		}
		
		echo $Json->encode($re);
		exit;
	}
	else if($act == 'remove'){
		$re['status'] = 0;
		$pid = intval($input['pid']);
		$cus_woks = $Db->query('SELECT * FROM '.$Base->table('map_annotation').' WHERE id = '.$pid,'Row');
		if (empty($cus_woks)) {
			$re['msg'] ='查询不到该标注';
		}else{
			$Db->delete($Base->table('map_annotation'),array('id'=>$pid));
			$re['obj'] = $cus_woks;
			$re['status'] = 1;
		}
		
		echo $Json->encode($re);
		exit;
	}else if($act == 'update'){
		$re['status'] = 0;
		$pid = intval($input['pid']);
		$title = Common::sfilter($input['title']);
		if (mb_strlen($title)<1) {
			$re['msg'] = '请输入标题';
		}else{
			$cus_woks = $Db->query('SELECT * FROM '.$Base->table('map_annotation').' WHERE id = '.$pid,'Row');
			if(empty($cus_woks)){
				$re['msg'] ='查询不到该标注';
			}else{
				$Db->update($Base->table('map_annotation'),array('cus_hotspot_title'=>$title),array('id'=>$pid));
				$re['status'] = 1;
				$re['obj'] = $cus_woks;
			}
		}
		echo $Json->encode($re);
		exit;
	}
	else if($act == 'paste'){
		$param = $input['param'];
		$Db->update($Base->table('map_annotation'),array('ath'=>intval($param['ath']),'atv'=>intval($param['atv']),'sys_scene'=>Common::sfilter($param['sys_scene'])),array('id'=>intval($param['id'])));

		$re['status'] = 1;
		echo $Json->encode($re);
		exit;
	}
	else if($act == 'submit'){
		$re['status'] = 0;
		$params = $input['params'];
		
		foreach ($params as $k => $v) {
			$Db->update($Base->table('map_annotation'),array('ath'=>$v['ath'],'atv'=>$v['atv']),array('id'=>$k));
		}		
		$re['status'] = 1;
		echo $Json->encode($re);
		exit;
	}


?>