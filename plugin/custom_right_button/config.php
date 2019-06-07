<?php
/*
 * Krpano100 自定义右键菜单
 * ============================================================================
 * 技术支持：2015-2099 成都世纪川翔科技有限公司
 * 官网地址: http://www.krpano100.com
 * ----------------------------------------------------------------------------
 * $Author: wanghao 932625974#qq.com $
 * $Id: bind.php 28028 2016-06-19Z yuanjiang $
*/

$plugins['custom_right_button'] = array(
		'plugin_name' => '自定义右键菜单',
		"enable" => 1,    			
		"edit_container" => "setting_group",
		"edit_sort" => 6,
		"edit_resource"=>1,
		"view_container" => "",
		"view_sort" => 2,
		"view_resource"=>1,
		"xml" => 'plugin.xml.php'
	);


?>