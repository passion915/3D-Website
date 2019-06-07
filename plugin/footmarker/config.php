<?php
/*
 * Krpano100 足迹插件
 * ============================================================================
 * 技术支持：2015-2099 成都世纪川翔科技有限公司
 * 官网地址: http://www.krpano100.com
 * ----------------------------------------------------------------------------
 * $Author: wanghao 932625974#qq.com $
 * $Id: bind.php 28028 2016-06-19Z yuanjiang $
*/

$plugins['footmarker'] = array(
		'plugin_name' => '足迹',
		"enable" => 1,    			
		"edit_container" => "option_group",
		"edit_sort" => 2,
		"view_container" => "right_bottom",
		"view_sort" => 1,
		"view_resource"=>1,
		"table"=>"pano_config",
  		"column"=>"footmark"
	);


?>