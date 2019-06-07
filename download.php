<?php
/*
 * Krpano100 下载功能
 * ============================================================================
 * 技术支持：2015-2099 成都世纪川翔科技有限公司
 * 官网地址: http://www.krpano100.com
 * ----------------------------------------------------------------------------
 * $Author: wanghao 932625974#qq.com $
 * $Id: bind.php 28028 2016-06-19Z yuanjiang $
*/
define('IN_T',true);
require_once "./source/include/init.php";

$act = Common::sfilter($_REQUEST['act']);
//下载离线项目
if ($act=='project') {
	$filename = Common::sfilter($_REQUEST['filename']);
	$filename = str_replace('.','',$filename).'.zip';
	$url = ROOT_PATH.'temp/down/'.$filename;
	if (!file_exists($url)) {
		die('找不到该文件');
	}
	createDowanload($url,$filename);
	exit;
}

// function createDowanload($url,$filename,$isDelete=false){
// 	/* headers */
// 	//设置该次请求超时时长，1800s
// 	@ini_set("max_execution_time", "1800"); 
// 	ob_clean();
// 	header('Cache-control: private');
// 	header("Content-type:application/x-zip-compressed"); 
// 	header('Content-Length: '.filesize($url));
// 	header('Content-Disposition:attachment; filename='.$filename);
// 	flush();
// 	$fh = @fopen($url, 'r');
// 	while(!feof($fh)){
// 	    print fread($fh, 1024);
// 	    flush();
// 	}
// 	@fclose($fh);
// 	if($isDelete)
// 		unlink($url);
// }



function createDowanload($filePath,$filename,$isDelete=false){
	//设置文件最长执行时间和内存
	set_time_limit ( 0 );
	ini_set ( 'memory_limit', '1024M' );
	ob_clean();
	//开始写输出头信息 
	header ( "Cache-Control: public" );
	//设置输出浏览器格式
	header ( "Content-Type: application/octet-stream" );
	header ( "Content-Disposition: attachment; filename=" . $filename );
	header ( "Content-Transfer-Encoding: binary" );
	header ( "Accept-Ranges: bytes" );
	$size = filesize ( $filePath );
	$range=0;
	if (isset ( $_SERVER ['HTTP_RANGE'] )) {
	// 断点后再次连接 $_SERVER['HTTP_RANGE'] 的值 bytes=4390912-
	list ( $a, $range ) = explode ( "=", $_SERVER ['HTTP_RANGE'] );
	//if yes, download missing part
	$size2 = $size - 1; //文件总字节数
	$new_length = $size2 - $range; //获取下次下载的长度
		header ( "HTTP/1.1 206 Partial Content" );
		header ( "Content-Length: {$new_length}" ); //输入总长
		header ( "Content-Range: bytes {$range}-{$size2}/{$size}" ); //Content-Range: bytes 4908618-4988927/4988928 95%的时候
	} else {
		//第一次连接
		$size2 = $size - 1;
		header ( "Content-Range: bytes 0-{$size2}/{$size}" ); //Content-Range: bytes 0-4988927/4988928
		header ( "Content-Length: " . $size ); //输出总长
	}
	//打开文件
	$fp = fopen ( "{$filePath}", "rb" );
	//设置指针位置
	fseek ( $fp, $range );
	//虚幻输出
	while ( ! feof ( $fp ) ) {
	print ( fread ( $fp, 1024 * 8 ) ); //输出文件
	flush (); //输出缓冲
	ob_flush ();
	}
	fclose ( $fp );
	if($isDelete)
		unlink($url);
	exit ();
}

?>