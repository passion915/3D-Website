<?php
/*
 * Krpano100 vr切换插件
 * ============================================================================
 * 技术支持：2015-2099 成都世纪川翔科技有限公司
 * 官网地址: http://www.krpano100.com
 * ----------------------------------------------------------------------------
 * $Author: wanghao 932625974#qq.com $
 * $Id: bind.php 28028 2016-06-19Z yuanjiang $
*/

$plugins['showvrglass'] = array(
		'plugin_name' => 'VR切换',
		"enable" => 1,    			
		"edit_container" => "option_group",
		"edit_sort" => 6,
		"view_container" => "right_top",
		"view_sort" => 1,
		"view_resource"=>1,
		"table"=>"worksmain",
  		"column"=>"hidevrglasses_flag"
	);

?>