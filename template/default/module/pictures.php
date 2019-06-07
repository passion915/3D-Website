<?php
//全景摄影
if(!defined('IN_T')){
	die('hacking attempt');
}

$act = Common::sfilter($_REQUEST['act']);
if ($act == 'list') {
	$tag = intval($_REQUEST['tag']);
	$page = intval($_REQUEST['page']);
	$page = $page<1?1:$page;
	$regions = $_REQUEST['regions'];
	//加入region
	$r1=intval($regions['region_1']);
	$r2=intval($regions['region_2']);
	$r3=intval($regions['region_3']);
	$r4=intval($regions['region_4']);
	$r5=intval($regions['region_5']);
	$r6=intval($regions['region_6']);

	if($r6>0){
		$r = $r6;
		$t = 6;
	}else if($r5>0){
		$r = $r5;
		$t = 5;
	}else if($r4>0){
		$r = $r4;
		$t = 4;
	}else if($r3>0){
		$r = $r3;
		$t = 3;
	}else if($r2>0){
		$r = $r2;
		$t = 2;
	}else if($r1>0){
		$r = $r1;
		$t = 1;
	}

	$list = get_picture_projects($tagid,$r,$t,$_REQUEST['properties'],$page,27);
	echo $Json->encode($list);
	exit;
}else{
	//加入region
	$r1=intval($_REQUEST['r1']);
	$r2=intval($_REQUEST['r2']);
	$r3=intval($_REQUEST['r3']);
	$r4=intval($_REQUEST['r4']);
	$r5=intval($_REQUEST['r5']);
	$r6=intval($_REQUEST['r6']);


	if($r6>0){
		$r = $r6;
		$t = 6;
	}else if($r5>0){
		$r = $r5;
		$t = 5;
	}else if($r4>0){
		$r = $r4;
		$t = 4;
	}else if($r3>0){
		$r = $r3;
		$t = 3;
	}else if($r2>0){
		$r = $r2;
		$t = 2;
	}else if($r1>0){
		$r = $r1;
		$t = 1;
	}

	$tagid = intval($_REQUEST['tag']);
	$tp->assign('pictures',get_picture_projects($tagid,$r,$t,null,1,27));
	$tp->assign('tag',$tagid);
	

	//获取区域信息
	require_once ROOT_PATH.'source/include/cls_region.php';
	$tp->assign('maxNode',Region::getMaxNode());
	$tp->assign('regions',$Json->encode(array("region_1"=>$r1,"region_2"=>$r2,"region_3"=>$r3,"region_4"=>$r4,"region_5"=>$r5,"region_6"=>$r6)) );
	//获取属性
	require_once ROOT_PATH.'source/include/cls_property.php';
	$tp->assign('parentNodes',Property::listByPid(0));
	$tp->assign('properties',$Json->encode(array()) );

	$tp->assign('picture_tags',get_picture_tags());
	$tp->assign('picturs_top_ad',Transaction::get_ad_by_postion(3));
}

//提取图片标签
function get_picture_tags(){
	$sql = "select * from ".$GLOBALS['Base']->table('tag')." where type=1 order by sort asc, id asc";
	$res = $GLOBALS['Db']->query($sql);
	return $res;
}

//提取图片项目
function get_picture_projects($tagid,$region,$type,$properties,$page,$size){
	$sql = "select w.name,w.thumb_path,w.view_uuid,w.profile,w.browsing_num ".
	       "from ".$GLOBALS['Base']->table('worksmain')." as w ";
	
	if ($region>0 && $type>0) {
		$rsql = ' (SELECT workid AS wid FROM '.$GLOBALS['Base']->table('work_region').' WHERE region_'.$type.'='.$region.') AS r ';
	}

	if ($tagid>0) {
		$tsql = ' (SELECT works_id AS wid FROM '.$GLOBALS['Base']->table('tag_works').' WHERE tag_id='.$tagid.' group by works_id ) AS t ';
	}

	if(!empty($properties)){
		$psql = ' (SELECT workid AS wid FROM '.$GLOBALS['Base']->table('work_property').' WHERE ';
		foreach ($properties as $p) {
			$p=intval($p);
			$psql .= ' child_node='.$p.' AND ';
		}
		$psql = substr($psql, 0,-4);
		$psql.=') AS p';
	}

	if (!empty($rsql)) {
		$sql.=' RIGHT JOIN (SELECT r.wid FROM ('.$rsql;
		if(!empty($tsql)){
			$sql.=' INNER JOIN '.$tsql.' ON t.wid = r.wid ';
		}
		if(!empty($psql)){
			$sql .= ' INNER JOIN '.$psql.' ON r.wid=p.wid ';
		}
		$sql.=' )) AS result ON result.wid = w.pk_works_main ';
	}else if(!empty($tsql)){
		$sql.=' RIGHT JOIN (SELECT t.wid FROM ('.$tsql;
		if(!empty($psql)){
			$sql .= ' INNER JOIN '.$psql.' ON t.wid=p.wid ';
		}
		$sql.=' )) AS result ON result.wid = w.pk_works_main ';
	}else if(!empty($psql)){
		$sql.=' RIGHT JOIN (SELECT p.wid FROM ('.$psql.' )) AS result ON result.wid = w.pk_works_main ';
	}
	if($tagid==-1){
		$sql .= " where w.recommend=1 ";
	}
	$sql .= "order by  w.sort asc, w.pk_works_main desc limit ".($page-1)*$size.",".$size;
	$res = $GLOBALS['Db']->query($sql);
	return $res;
}


?>