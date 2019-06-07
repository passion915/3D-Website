<?php
/**
 * 属性管理
 * @author wh 10.18.2014
*/
class Property{

	/**
	*  根据父级id返回子集的集合
	*/
	public static function listByPid($pid = 0 ){
		return  $GLOBALS['Db']->query('SELECT * FROM '.$GLOBALS['Base']->table('property').' WHERE pid = '.$pid.' ORDER BY sort ASC');
	}

	/**
	* 查询最大层级
	*/
	// public static function getMaxNode(){
	// 	return  $GLOBALS['Db']->query('SELECT MAX(type) AS m FROM '.$GLOBALS['Base']->table('region'),'One');
	// }
}



?>