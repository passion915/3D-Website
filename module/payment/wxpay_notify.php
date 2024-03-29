<?php
/**
 * 微信异步通知页面
 */
define('IN_T',true);
require '../../source/include/init.php';

require_once ROOT_PATH."source/payment/wxpay/lib/WxPay.Api.php";
require_once ROOT_PATH."source/payment/wxpay/lib/WxPay.Notify.php";
require_once ROOT_PATH."source/payment/wxpay/action/log.php";

//初始化日志
$logHandler= new CLogFileHandler(ROOT_PATH."source/payment/wxpay/logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return $result;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$result = $this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		
		/*============================== 处理业务  start ============================ */ 
		$order_sn = $result['attach'];   //订单号
		$amount = $result['total_fee']/100;   //支付金额
		//数据库中的订单信息
		$order = $GLOBALS['Db']->query("select * from ".$GLOBALS['Base']->table('recharge')." where order_sn='$order_sn' limit 1","Row");
		//对比订单号对应的金额是否合法
		if($amount == $order['amount']){
			//未支付过
			if($order['status']==0){
				//原来到期时间
				$expire = $GLOBALS['Db']->query("select expire from ".$GLOBALS['Base']->table('user')." where pk_user_main=".$order['uid']."","One");
				//新的过期时间
				$cur_expire = strtotime("".($expire>0 ? date('Y-m-d',$expire) : date('Y-m-d',Common::gmtime()))." +".$order['months']." months");
				
				//更新订单状态
				$data = array(
				'status'=>1,
				'last_expire'=>date('Y-m-d',$expire),
				'cur_expire'=>date('Y-m-d',$cur_expire),
				'confirm_time'=>date('Y-m-d H:i:s',Common::gmtime()),
				);
				$GLOBALS['Db']->update($GLOBALS['Base']->table('recharge'),$data,array('order_sn'=>$order_sn));
				
				//更新user表
				$data = array(
				'level'=>$order['level'],
				'expire'=>$cur_expire,
				);
				$GLOBALS['Db']->update($GLOBALS['Base']->table('user'),$data,array('pk_user_main'=>$order['uid']));
			}
			//已支付过
			else{
				return true;
			}
		}
		//支付的金额与数据库不匹配
		else{
			return false;
		}			
		/* =========================== 处理业务  end ======================= */
		
		return true;
	}
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);

?>