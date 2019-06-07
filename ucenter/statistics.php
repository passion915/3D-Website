<?php
define('IN_T',true);
require './include/init.php';

$act =  Common::sfilter($_REQUEST['act']);
$domain=$GLOBALS['_lang']['domain'];
// $userid=$Db->query("SELECT id FROM u_proxy WHERE domain='$domain'","One");
$re['status'] = 0;
$re['msg'] = "";
// if(intval($userid)<=0){
// 	$re['msg'] = '非法请求';
// 	echo $Json->encode($re);
// 	exit;
// }
$browser = $_REQUEST['browser'];
$pk_works_main =intval($_REQUEST['pk_works_main']);
if(!empty($browser)){
	$browser = $browser[0];
	$count = $Db->getCount('u_browser','pk_works_main', ['pk_works_main'=>$pk_works_main]);
	if($count < 1 ){
		$Db->insert('u_browser', [$browser.'_count'=> 1, 'pk_works_main'=>$pk_works_main ]);
	}else{
		$Db->query("update u_browser set ".$browser."_count = ".$browser."_count +1 where pk_works_main = ".$pk_works_main);
	}
}
if ($act == 'PV') {
	// $url =Common::sfilter($_REQUEST['url']);
	$url=$_SERVER['REQUEST_URI'];
	$ip=Common::get_client_ip();
	$address=Common::get_ip_address_taobao($ip);
	$Db->insert($Base->table('vistor_pv'),array('url'=>$url,'vistortime'=>date('Y-m-d H:i:s',Common::gmtime())
		,'ip'=>$ip,'address'=>$address));
	$re['status']=1;
	echo $Json->encode($re);
	exit;

}else if($act == 'UV'){
	$vistorid=intval($user['pk_user_main']);
	if($vistorid<=0){
		$re['msg'] = '非法请求';
		echo $Json->encode($re);
		exit;
	}

	$url=$_SERVER['REQUEST_URI'];
	$id=$Db->query("SELECT id FROM u_vistor_uv WHERE vistorid=$vistorid AND  DATE_FORMAT(vistortime,'%Y-%m-%d')=DATE_FORMAT(now(),'%Y-%m-%d') limit 1","One");
	if(intval($id)<=0){
		//添加
		$ip=Common::get_client_ip();
		$address=Common::get_ip_address_taobao($ip);
		$data = array('url'=>$url,'vistortime'=>date('Y-m-d H:i:s',Common::gmtime()),'ip'=>$ip,'address'=>$address,'vistorid'=>$vistorid);
		var_dump($data);
		$Db->insert($Base->table('vistor_uv'), $data);
		$re['status']=1;
	}
	echo $Json->encode($re);
	exit;
}
else if($act=="panos"){
	//全景作品统计
	$pk_works_main =intval($_REQUEST['pk_works_main']);
	if($pk_works_main<=0){
		$re['msg'] = '非法请求';
		echo $Json->encode($re);
		exit;
	}
	$user_id=$Db->query("SELECT pk_user_main FROM u_worksmain where pk_works_main=$pk_works_main","One");
	if(intval($user_id)<=0){
		$re['msg'] = '非法参数';
		echo $Json->encode($re);
		exit;
	}
	$ip=Common::get_client_ip();
	$address=Common::get_ip_address_taobao($ip);
	handleRegion($address, $pk_works_main);
	$Db->insert($Base->table('vistor_works'),array('pk_works_main'=>$pk_works_main,'vistortime'=>date('Y-m-d H:i:s',Common::gmtime()),'ip'=>$ip,'address'=>$address));

	$re['status']=1;
	echo $Json->encode($re);
	exit;
}
else{
	$re['msg'] = '非法请求';
	echo $Json->encode($re);
	exit;
}

function handleRegion($address, $pk_works_main) {
	global $Db;
	$address = explode(' ', $address);
	$pro = $address[0];
	$id = $Db->query('SELECT id FROM u_region_sts WHERE type = 1 AND region_name= "'.$pro .'" AND pk_works_main = '. $pk_works_main, 'One');
	if($id < 1 ){
		$Db->insert('u_region_sts', ['region_name'=>$pro, 'type'=>1, 'pk_works_main'=>$pk_works_main]);
	}else{
		$Db->query("update u_region_sts set region_count = region_count +1 where id = ".$id);
	}

	$city = $address[1];
	$id = $Db->query('SELECT id FROM u_region_sts WHERE type = 2 AND region_name= "'.$city .'" AND pk_works_main = '. $pk_works_main, 'One');
	if($id < 1 ){
		$Db->insert('u_region_sts', ['region_name'=>$city, 'type'=>2, 'pk_works_main'=>$pk_works_main]);
	}else{
		$Db->query("update u_region_sts set region_count = region_count +1 where id = ".$id);
	}
}
?>