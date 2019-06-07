<?php
/*
 * Krpano100 清空缓存文件
 * ============================================================================
 * 技术支持：2015-2099 成都世纪川翔科技有限公司
 * 官网地址: http://www.krpano100.com
 * ----------------------------------------------------------------------------
 * $Author: yuanjiang 932625974#qq.com $
 * $Id: index.php 28028 2016-03-09Z yuanjiang $
*/
if(!defined('IN_T')){
   die('hacking attempt');
}

//删除全景图片缓存目录
require_once ROOT_PATH.'source/include/cls_file_util.php';
FileUtil::unlinkDir(ROOT_PATH.'data/qr/');
echo $Json->encode(array('status'=>1));
exit;

?>