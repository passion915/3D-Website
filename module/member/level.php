<?php
/*
 * Krpano100 会员级别
 * ============================================================================
 * 技术支持：2015-2099 成都世纪川翔科技有限公司
 * 官网地址: http://www.krpano100.com
 * ----------------------------------------------------------------------------
 * $Author: yuanjiang 932625974#qq.com $
 * $Id: level.php 28028 2016-06-19Z yuanjiang $
*/
if(!defined('IN_T')){
	die('hacking attempt');
}

$act = Common::sfilter($_REQUEST['act']);

//支付
if($act=='pay'){
	$pay_type = Common::sfilter($_REQUEST['pay_type']);
	$pay_method = Common::sfilter($_REQUEST['pay_method']);
	$pay_type = explode('-',$pay_type);
	$level = $pay_type[0];
	$pay_type = $pay_type[1];
	if($level<1 || empty($pay_type) || empty($pay_method)){
		die('Error，缺少支付参数！');
	}
	else{
		//生成订单号
		$error_no = 0;
		do{
		    $order_sn = Common::get_order_sn(); //生成订单号
		    $error_no = (int)$Db->query("select id from ".$Base->table('recharge')." where order_sn = '".$order_sn."'");
		}
		while($error_no>0); //如果是订单号重复则重新生成
		
		//金额
		$amount = $Db->query("select $pay_type from ".$Base->table('user_level')." where id=$level","One");
		
		//写入数据库
		$data = array(
		'uid'=>$user['pk_user_main'],
		'order_sn'=>$order_sn,
		'pay_method'=>$pay_method,
		'level'=>$level,
		'amount'=>$amount,
		'months'=>$_lang['recharge_months'][$pay_type],
		);
		
		$Db->insert($Base->table('recharge'),$data);
		
		//微信支付
		if($pay_method=='wxpay'){

			require_once ROOT_PATH."source/payment/wxpay/lib/WxPay.Api.php";
			require_once ROOT_PATH."source/payment/wxpay/action/WxPay.NativePay.php";
			require_once ROOT_PATH."source/payment/wxpay/action/log.php";
			
			$notify = new NativePay();
	
			/**
			 * 流程：
			 * 1、调用统一下单，取得code_url，生成二维码
			 * 2、用户扫描二维码，进行支付
			 * 3、支付完成之后，微信服务器会通知支付成功
			 * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
			 */
			$input = new WxPayUnifiedOrder();
			$input->SetBody("会员级别开通/续费");
			$input->SetAttach($order_sn);
			$input->SetOut_trade_no(WxPayConfig::$MCHID.date("YmdHis"));
			$input->SetTotal_fee($amount*100);   //支付金额，微信支付单位为0.01元
			$input->SetTime_start(date("YmdHis"));
			$input->SetTime_expire(date("YmdHis", time() + 600));
			$input->SetGoods_tag("tag内容");
			$input->SetNotify_url($_lang['host']."module/payment/wxpay_notify.php");
			$input->SetTrade_type("NATIVE");
			$input->SetProduct_id($order_sn);
			$result = $notify->GetPayUrl($input);
			$url2 = $result["code_url"];
			if(!$url2){
				die('Error，生成支付二维码失败！');
			}
			$tp->assign('title','微信支付');
			$tp->assign('pay_url',$url2);
		}
		//支付宝支付
		else if($pay_method=='alipay'){
		
			require_once ROOT_PATH."source/payment/alipay/alipay.config.php";
			require_once ROOT_PATH."source/payment/alipay/lib/alipay_submit.class.php";
			
			/**************************请求参数**************************/
			//商户订单号，商户网站订单系统中唯一订单号，必填
			$out_trade_no = $_POST['WIDout_trade_no'];
			//订单名称，必填
			$subject = $_POST['WIDsubject'];
			//付款金额，必填
			$total_fee = $_POST['WIDtotal_fee'];
			//商品描述，可空
			$body = $_POST['WIDbody'];
			/************************************************************/
			
			//构造要请求的参数数组，无需改动
			$parameter = array(
					"service"       => $alipay_config['service'],
					"partner"       => $alipay_config['partner'],
					"seller_id"  => $alipay_config['seller_id'],
					"payment_type"	=> $alipay_config['payment_type'],
					"notify_url"	=> $alipay_config['notify_url'],
					"return_url"	=> $alipay_config['return_url'],
					
					"anti_phishing_key"=>$alipay_config['anti_phishing_key'],
					"exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
					"out_trade_no"	=> $out_trade_no,
					"subject"	=> $subject,
					"total_fee"	=> $total_fee,
					"body"	=> $body,
					"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
					//其他业务参数根据在线开发文档，添加参数
					//文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
					//如"参数名"=>"参数值"
					
			);
			
			//建立请求
			$alipaySubmit = new AlipaySubmit($alipay_config);
			$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "跳转到支付宝...");
			echo $html_text;
			exit;
		}
	}
}
else if($act=='refresh'){
	$_SESSION['user']['level'] = $Db->query("select level from ".$Base->table('user')." where pk_user_main=".$user['pk_user_main']."","One");
	Common::base_header("Location:/member/level\n");
}
else{
	//会员级别名称 
	$level = $Db->query("select * from ".$Base->table('user_level')." where id=".$user['level']."","Row");
	$level['expire'] = $Db->query("select expire from ".$Base->table('user')." where pk_user_main=".$user['pk_user_main']."","One");
	$tp->assign('level',$level);
	//提取需要付费的会员级别
	$levels = $Db->query("select * from ".$Base->table('user_level')." where pay_month>0 && pay_season>0 && pay_year>0 order by id asc");
	$tp->assign('levels',$levels);
	$tp->assign('title','会员级别');
}
$tp->assign('act',$act);
?>